<?php
/**
 * Test Temizleme Yardımcı Sınıfı
 * 
 * Test ortamında oluşturulan gereksiz dosyaları otomatik olarak temizler.
 * Güvenlik için sadece Tests/ klasörü altındaki dosyaları işler.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

class TestCleaner
{
    /**
     * @var string Tests klasörünün tam yolu
     */
    private static $testsPath;
      /**
     * @var array Silinebilir dosya uzantıları
     */
    private static $allowedExtensions = [
        'php', 'txt', 'log', 'json', 'xml', 'csv', 'html', 'md', 'css', 'js'
    ];
    
    /**
     * @var array Korunacak dosyalar (silinmeyecek)
     */
    private static $protectedFiles = [
        'index.php',
        'README.md',
        'TestModel/TestAssert.php',
        'TestModel/TestDataGenerator.php',
        'TestModel/TestLogger.php',
        'TestModel/TestRunner.php',
        'TestModel/TestValidator.php',
        'example_test.php'
    ];
    
    /**
     * Sınıf başlatma
     */
    public static function init()
    {
        self::$testsPath = realpath(__DIR__ . '/../');
        TestLogger::info('TestCleaner başlatıldı: ' . self::$testsPath);
    }
    
    /**
     * Belirtilen dosyaları siler
     * 
     * @param array $files Silinecek dosyalar (Tests klasörüne göre relative path)
     * @param bool $dryRun Sadece kontrol et, gerçekten silme (default: false)
     * @return array Sonuç raporu
     */
    public static function cleanFiles($files, $dryRun = false)
    {
        self::init();
        
        $results = [
            'deleted' => [],
            'skipped' => [],
            'errors' => [],
            'protected' => []
        ];
        
        TestLogger::info('Dosya temizleme başlatıldı' . ($dryRun ? ' (DRY RUN)' : ''));
        TestLogger::info('Toplam dosya sayısı: ' . count($files));
        
        foreach ($files as $file) {
            $result = self::deleteFile($file, $dryRun);
            $results[$result['status']][] = $result;
        }
        
        self::printSummary($results, $dryRun);
        return $results;
    }
    
    /**
     * Tek dosya silme işlemi
     * 
     * @param string $file Dosya yolu (Tests klasörüne göre relative)
     * @param bool $dryRun Sadece kontrol et
     * @return array Dosya işlem sonucu
     */
    private static function deleteFile($file, $dryRun = false)
    {
        // Dosya yolunu normalleştir
        $file = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);
        $fullPath = self::$testsPath . DIRECTORY_SEPARATOR . $file;
        
        // Güvenlik kontrolü: Tests klasörü dışına çıkma
        if (!self::isInTestsFolder($fullPath)) {
            TestLogger::error("Güvenlik ihlali: $file Tests klasörü dışında");
            return [
                'file' => $file,
                'status' => 'errors',
                'message' => 'Tests klasörü dışında'
            ];
        }
        
        // Dosya mevcut mu?
        if (!file_exists($fullPath)) {
            TestLogger::warning("Dosya bulunamadı: $file");
            return [
                'file' => $file,
                'status' => 'skipped',
                'message' => 'Dosya bulunamadı'
            ];
        }
        
        // Korumalı dosya mı?
        $filename = basename($file);
        if (in_array($filename, self::$protectedFiles)) {
            TestLogger::warning("Korumalı dosya: $file");
            return [
                'file' => $file,
                'status' => 'protected',
                'message' => 'Korumalı dosya'
            ];
        }
        
        // Dosya uzantısı uygun mu?
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (!in_array($extension, self::$allowedExtensions)) {
            TestLogger::warning("İzin verilmeyen uzantı: $file ($extension)");
            return [
                'file' => $file,
                'status' => 'skipped',
                'message' => "İzin verilmeyen uzantı: $extension"
            ];
        }
        
        // Dosyayı sil (veya dry run)
        if ($dryRun) {
            TestLogger::info("DRY RUN: Silinecek -> $file");
            return [
                'file' => $file,
                'status' => 'deleted',
                'message' => 'DRY RUN - Silinebilir'
            ];
        }
        
        try {
            if (unlink($fullPath)) {
                TestLogger::success("Silindi: $file");
                return [
                    'file' => $file,
                    'status' => 'deleted',
                    'message' => 'Başarıyla silindi'
                ];
            } else {
                TestLogger::error("Silinemedi: $file");
                return [
                    'file' => $file,
                    'status' => 'errors',
                    'message' => 'Dosya silinemedi'
                ];
            }
        } catch (Exception $e) {
            TestLogger::error("Hata: $file - " . $e->getMessage());
            return [
                'file' => $file,
                'status' => 'errors',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Dosyanın Tests klasörü içinde olup olmadığını kontrol eder
     * 
     * @param string $fullPath Tam dosya yolu
     * @return bool
     */
    private static function isInTestsFolder($fullPath)
    {
        $realPath = realpath(dirname($fullPath));
        return $realPath !== false && strpos($realPath, self::$testsPath) === 0;
    }
    
    /**
     * Temizleme özeti yazdır
     * 
     * @param array $results Sonuçlar
     * @param bool $dryRun Dry run modu
     */
    private static function printSummary($results, $dryRun)
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📋 TEST DOSYA TEMİZLEME ÖZETİ" . ($dryRun ? " (DRY RUN)" : "") . "\n";
        echo str_repeat("=", 60) . "\n";
        
        echo "✅ Silinen dosyalar: " . count($results['deleted']) . "\n";
        foreach ($results['deleted'] as $item) {
            echo "   - " . $item['file'] . "\n";
        }
        
        if (count($results['protected']) > 0) {
            echo "\n🛡️  Korumalı dosyalar: " . count($results['protected']) . "\n";
            foreach ($results['protected'] as $item) {
                echo "   - " . $item['file'] . "\n";
            }
        }
        
        if (count($results['skipped']) > 0) {
            echo "\n⏭️  Atlanan dosyalar: " . count($results['skipped']) . "\n";
            foreach ($results['skipped'] as $item) {
                echo "   - " . $item['file'] . " (" . $item['message'] . ")\n";
            }
        }
        
        if (count($results['errors']) > 0) {
            echo "\n❌ Hatalı dosyalar: " . count($results['errors']) . "\n";
            foreach ($results['errors'] as $item) {
                echo "   - " . $item['file'] . " (" . $item['message'] . ")\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        TestLogger::info('TestCleaner özet tamamlandı');
    }
    
    /**
     * Belirtilen klasördeki tüm dosyaları listeler
     * 
     * @param string $folder Tests klasörüne göre relative path
     * @param array $extensions Filtrelenecek uzantılar (boşsa tümü)
     * @return array Dosya listesi
     */
    public static function listFiles($folder, $extensions = [])
    {
        self::init();
        
        $folder = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $folder);
        $fullPath = self::$testsPath . DIRECTORY_SEPARATOR . $folder;
        
        if (!is_dir($fullPath)) {
            TestLogger::error("Klasör bulunamadı: $folder");
            return [];
        }
        
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace(self::$testsPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                
                // Uzantı filtresi
                if (!empty($extensions)) {
                    $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
                    if (!in_array($extension, $extensions)) {
                        continue;
                    }
                }
                
                $files[] = $relativePath;
            }
        }
        
        return $files;
    }
    
    /**
     * Geçici dosyaları temizle (Temp/ klasörü)
     * 
     * @param bool $dryRun Sadece kontrol et
     * @return array Sonuç raporu
     */
    public static function cleanTempFiles($dryRun = false)
    {
        $tempFiles = self::listFiles('Temp', ['php', 'txt', 'log', 'json']);
        
        if (empty($tempFiles)) {
            TestLogger::info('Temp klasöründe silinecek dosya yok');
            return ['deleted' => [], 'skipped' => [], 'errors' => [], 'protected' => []];
        }
        
        TestLogger::info('Temp klasörü temizleniyor...');
        return self::cleanFiles($tempFiles, $dryRun);
    }
    
    /**
     * Log dosyalarını temizle (30 günden eski)
     * 
     * @param int $daysOld Kaç günden eski dosyalar silinsin
     * @param bool $dryRun Sadece kontrol et
     * @return array Sonuç raporu
     */
    public static function cleanOldLogs($daysOld = 30, $dryRun = false)
    {
        $logFiles = self::listFiles('Logs', ['log']);
        $oldFiles = [];
        
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        
        foreach ($logFiles as $file) {
            $fullPath = self::$testsPath . DIRECTORY_SEPARATOR . $file;
            if (filemtime($fullPath) < $cutoffTime) {
                $oldFiles[] = $file;
            }
        }
        
        if (empty($oldFiles)) {
            TestLogger::info("$daysOld günden eski log dosyası yok");
            return ['deleted' => [], 'skipped' => [], 'errors' => [], 'protected' => []];
        }
        
        TestLogger::info("$daysOld günden eski log dosyaları temizleniyor...");
        return self::cleanFiles($oldFiles, $dryRun);
    }
    
    /**
     * Tüm test modüllerindeki geçici dosyaları temizle
     * 
     * @param bool $dryRun Sadece kontrol et
     * @return array Toplam sonuç raporu
     */
    public static function cleanAllTempFiles($dryRun = false)
    {
        self::init();
        
        TestLogger::info('Tüm modüllerdeki geçici dosyalar temizleniyor...');
        
        $allResults = [
            'deleted' => [],
            'skipped' => [],
            'errors' => [],
            'protected' => []
        ];
        
        // Test modüllerindeki geçici dosya pattern'leri
        $tempPatterns = ['temp_', 'debug_', 'test_', 'old_', 'sample_'];
        $modules = ['Orders', 'Products', 'Members', 'Carts', 'Banners', 'Categorys', 'System'];
        
        // Her modülde geçici dosyaları bul
        foreach ($modules as $module) {
            if (is_dir(self::$testsPath . DIRECTORY_SEPARATOR . $module)) {
                $moduleFiles = self::listFiles($module);
                $tempFiles = array_filter($moduleFiles, function($file) use ($tempPatterns) {
                    $filename = basename($file);
                    foreach ($tempPatterns as $pattern) {
                        if (strpos($filename, $pattern) === 0) {
                            return true;
                        }
                    }
                    return false;
                });
                
                if (!empty($tempFiles)) {
                    TestLogger::info("$module modülünde " . count($tempFiles) . " geçici dosya bulundu");
                    $moduleResults = self::cleanFiles($tempFiles, $dryRun);
                    
                    // Sonuçları birleştir
                    foreach ($moduleResults as $status => $items) {
                        $allResults[$status] = array_merge($allResults[$status], $items);
                    }
                }
            }
        }
        
        // Temp klasörünü de temizle
        $tempResults = self::cleanTempFiles($dryRun);
        foreach ($tempResults as $status => $items) {
            $allResults[$status] = array_merge($allResults[$status], $items);
        }
        
        TestLogger::info('Tüm modül temizleme tamamlandı');
        return $allResults;
    }
}

// Komut satırından çalıştırılıyorsa
if (isset($argv) && basename(__FILE__) == basename($argv[0])) {
    echo "🧹 Test Dosya Temizleyici\n";
    echo "========================\n\n";
    
    if (isset($argv[1])) {
        switch ($argv[1]) {
            case 'temp':
                TestCleaner::cleanTempFiles();
                break;
                
            case 'logs':
                $days = isset($argv[2]) ? (int)$argv[2] : 30;
                TestCleaner::cleanOldLogs($days);
                break;
                
            case 'dry-run':
                echo "🔍 DRY RUN - Temp klasörü kontrolü:\n";
                TestCleaner::cleanTempFiles(true);
                break;
                  case 'all-temp':
                TestCleaner::cleanAllTempFiles();
                break;
                
            case 'dry-all':
                echo "🔍 DRY RUN - Tüm modüller kontrolü:\n";
                TestCleaner::cleanAllTempFiles(true);
                break;
                  default:
                echo "Kullanım:\n";
                echo "  php TestCleaner.php temp          # Temp dosyalarını sil\n";
                echo "  php TestCleaner.php logs [days]   # Eski log dosyalarını sil\n";
                echo "  php TestCleaner.php all-temp      # Tüm modüllerdeki geçici dosyaları sil\n";
                echo "  php TestCleaner.php dry-run       # Sadece temp kontrolü\n";
                echo "  php TestCleaner.php dry-all       # Tüm modüller kontrolü\n";
        }
    } else {
        echo "Kullanım:\n";
        echo "  php TestCleaner.php temp          # Temp dosyalarını sil\n";
        echo "  php TestCleaner.php logs [days]   # Eski log dosyalarını sil\n";
        echo "  php TestCleaner.php all-temp      # Tüm modüllerdeki geçici dosyaları sil\n";
        echo "  php TestCleaner.php dry-run       # Sadece temp kontrolü\n";
        echo "  php TestCleaner.php dry-all       # Tüm modüller kontrolü\n";
    }
}
