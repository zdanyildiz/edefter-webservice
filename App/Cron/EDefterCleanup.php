<?php
// App/Cron/EDefterCleanup.php
// E-Defter çıktı dosyalarını otomatik temizleyen cron job

$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/CronGlobal.php';

/**
 * @var AdminDatabase $db
 * @var Helper $helper
 */

// E-Defter çıktı klasörü
$outputDir = $documentRoot . '/Public/File/outputs';

Log::adminWrite("EDefterCleanup cron job başladı.", "info", "edefter-cleanup");

try {
    // 1. KLASÖR KONTROLÜ VE DOSYA TEMİZLEME
    if (!is_dir($outputDir)) {
        Log::adminWrite("E-Defter çıktı klasörü bulunamadı: $outputDir", "warning", "edefter-cleanup");
        exit("E-Defter çıktı klasörü bulunamadı.\n");
    }

    // Klasör içindeki dosyaları tara
    $files = glob($outputDir . '/*');
    
    if (empty($files)) {
        Log::adminWrite("Temizlenecek dosya bulunamadı.", "info", "edefter-cleanup");
    } else {
        $deletedCount = 0;
        $deletedSize = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($files as $file) {
            // Sadece dosyaları işle, alt klasörleri atla
            if (!is_file($file)) {
                $skippedCount++;
                continue;
            }

            $fileName = basename($file);
            $fileSize = filesize($file);
            
            // Dosya boyutunu hesapla
            $deletedSize += $fileSize;

            // Dosyayı sil
            if (unlink($file)) {
                $deletedCount++;
                Log::adminWrite("Dosya silindi: $fileName (Boyut: " . formatBytes($fileSize) . ")", "info", "edefter-cleanup");
            } else {
                $errorCount++;
                Log::adminWrite("Dosya silinemedi: $fileName", "error", "edefter-cleanup");
            }
        }

        // Özet raporu
        $summaryMessage = sprintf(
            "Temizleme tamamlandı. Toplam: %d, Silinen: %d, Hata: %d, Atlanan: %d, Toplam Boyut: %s",
            count($files),
            $deletedCount,
            $errorCount,
            $skippedCount,
            formatBytes($deletedSize)
        );

        Log::adminWrite($summaryMessage, "info", "edefter-cleanup");

        // İstatistikleri veritabanına kaydet (opsiyonel)
        if ($deletedCount > 0) {
            try {
                // Tablo varlığını kontrol et
                $checkTableQuery = "SHOW TABLES LIKE 'edefter_cleanup_stats'";
                $stmt = $db->prepare($checkTableQuery);
                $stmt->execute();
                $tableExists = $stmt->rowCount() > 0;
                
                if ($tableExists) {
                    $statsQuery = "INSERT INTO edefter_cleanup_stats (cleanup_date, deleted_files, deleted_size_bytes, error_count) 
                                   VALUES (CURDATE(), ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE 
                                   deleted_files = deleted_files + VALUES(deleted_files),
                                   deleted_size_bytes = deleted_size_bytes + VALUES(deleted_size_bytes),
                                   error_count = error_count + VALUES(error_count),
                                   last_cleanup_time = NOW()";
                    
                    $stmt = $db->prepare($statsQuery);
                    $result = $stmt->execute([$deletedCount, $deletedSize, $errorCount]);
                    
                    if ($result) {
                        Log::adminWrite("Temizleme istatistikleri veritabanına kaydedildi.", "info", "edefter-cleanup");
                    } else {
                        Log::adminWrite("Temizleme istatistikleri veritabanına kaydedilemedi.", "warning", "edefter-cleanup");
                    }
                } else {
                    Log::adminWrite("edefter_cleanup_stats tablosu bulunamadı, istatistik kaydı atlandı.", "info", "edefter-cleanup");
                }
            } catch (Exception $e) {
                // Veritabanı hatası kritik değil, loglayıp devam et
                Log::adminWrite("İstatistik kaydetme hatası: " . $e->getMessage(), "warning", "edefter-cleanup");
            }
        }
    }

    // 2. ESKİ KULLANIM KAYITLARINI TEMİZLE
    try {
        require_once MODEL . 'EDefterUsage.php';
        $usageModel = new EDefterUsage($db);
        $cleanupResult = $usageModel->cleanOldRecords();
        
        if ($cleanupResult) {
            Log::adminWrite("Eski kullanım kayıtları (30+ gün) temizlendi.", "info", "edefter-cleanup");
        } else {
            Log::adminWrite("Eski kullanım kayıtları temizlenirken sorun oluştu.", "warning", "edefter-cleanup");
        }
    } catch (Exception $e) {
        Log::adminWrite("Kullanım kayıtları temizleme hatası: " . $e->getMessage(), "warning", "edefter-cleanup");
    }

    // 3. GEÇİCİ UPLOAD DOSYALARINI TEMİZLE
    $tempDir = $documentRoot . '/Public/File/temp_uploads';
    if (is_dir($tempDir)) {
        $tempFiles = glob($tempDir . '/*');
        $deletedTempFiles = 0;
        
        foreach ($tempFiles as $tempFile) {
            if (is_file($tempFile)) {
                // 1 günden eski geçici dosyaları sil
                if (time() - filemtime($tempFile) > 86400) { // 24 saat
                    if (unlink($tempFile)) {
                        $deletedTempFiles++;
                        Log::adminWrite("Geçici dosya silindi: " . basename($tempFile), "info", "edefter-cleanup");
                    }
                }
            }
        }
        
        Log::adminWrite("Geçici dosyalar temizlendi: $deletedTempFiles dosya silindi", "info", "edefter-cleanup");
    }
    
    // 4. GÜNLÜK KULLANIM İSTATİSTİKLERİ
    $todayUsageSQL = "SELECT user_type, COUNT(*) as count, SUM(usage_count) as total_usage 
                      FROM edefter_usage 
                      WHERE usage_date = CURDATE() 
                      GROUP BY user_type";
    try {
        $stmt = $db->prepare($todayUsageSQL);
        $stmt->execute();
        $todayStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        Log::adminWrite("Günlük kullanım istatistikleri:", "info", "edefter-cleanup");
        foreach ($todayStats as $stat) {
            Log::adminWrite("- {$stat['user_type']}: {$stat['count']} kullanıcı, {$stat['total_usage']} toplam işlem", "info", "edefter-cleanup");
        }
    } catch (Exception $e) {
        Log::adminWrite("İstatistik okuma hatası: " . $e->getMessage(), "warning", "edefter-cleanup");
    }

    Log::adminWrite("EDefterCleanup cron job başarıyla tamamlandı.", "info", "edefter-cleanup");

} catch (Exception $e) {
    $errorMessage = "EDefterCleanup cron job hatası: " . $e->getMessage();
    Log::adminWrite($errorMessage, "error", "edefter-cleanup");
    echo "Hata: $errorMessage\n";
    exit(1);
}

/**
 * Byte cinsinden boyutu okunabilir formata çevirir
 */
function formatBytes($bytes, $precision = 2) {
    if ($bytes == 0) return '0 B';
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
