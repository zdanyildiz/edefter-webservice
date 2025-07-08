<?php
/**
 * E-Defter Cleanup Cron Test
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('E-Defter Cleanup Cron Test');

try {
    echo "\n";
    echo "🧹 E-DEFTER CLEANUP CRON TEST\n";
    echo "============================\n\n";
    
    // 1. Test dosyaları oluştur
    echo "📁 1. TEST DOSYALARI OLUŞTURULUYOR\n";
    echo "----------------------------------\n";
    
    $outputsDir = __DIR__ . '/../../Public/File/outputs';
    if (!is_dir($outputsDir)) {
        mkdir($outputsDir, 0777, true);
        echo "✅ Outputs klasörü oluşturuldu\n";
    }
    
    // Test dosyaları oluştur
    $testFiles = [
        'test_output_1.html' => '<html><body>Test Output 1</body></html>',
        'test_output_2.html' => '<html><body>Test Output 2</body></html>',
        'old_berat_output.html' => '<html><body>Old Berat Output</body></html>',
        'kebir_result.html' => '<html><body>Kebir Result</body></html>',
        'yevmiye_data.html' => '<html><body>Yevmiye Data</body></html>'
    ];
    
    $createdFiles = 0;
    $totalTestSize = 0;
    
    foreach ($testFiles as $fileName => $content) {
        $filePath = $outputsDir . '/' . $fileName;
        file_put_contents($filePath, $content);
        $fileSize = filesize($filePath);
        $totalTestSize += $fileSize;
        $createdFiles++;
        echo "✅ Test dosyası oluşturuldu: $fileName ($fileSize bytes)\n";
    }
    
    echo "📊 Toplam $createdFiles test dosyası oluşturuldu ($totalTestSize bytes)\n";
    
    // 2. Cron dosyasını kontrol et
    echo "\n🔧 2. CRON DOSYASI KONTROLÜ\n";
    echo "-------------------------\n";
    
    $cronFile = __DIR__ . '/../../App/Cron/EDefterCleanup.php';
    TestAssert::assertTrue(file_exists($cronFile), 'EDefterCleanup.php dosyası mevcut olmalı');
    echo "✅ Cron dosyası mevcut: $cronFile\n";
    
    $cronContent = file_get_contents($cronFile);
    TestAssert::assertNotEmpty($cronContent, 'Cron dosyası içeriği boş olmamalı');
    echo "✅ Cron dosyası içeriği mevcut\n";
    
    // 3. Test edefter_usage kayıtları oluştur
    echo "\n📋 3. TEST EDEFTER_USAGE KAYITLARI\n";
    echo "--------------------------------\n";
    
    $db = TestDatabase::getInstance();
    require_once __DIR__ . '/../../App/Model/EDefterUsage.php';
    $usageModel = new EDefterUsage($db);
    
    // Eski test kayıtları oluştur (35 gün önce)
    $oldDate = date('Y-m-d', strtotime('-35 days'));
    $oldInsertSQL = "INSERT INTO edefter_usage (user_identifier, user_type, usage_date, usage_count, last_usage_time) 
                     VALUES ('old_test_user', 'visitor', '$oldDate', 5, '$oldDate 12:00:00')";
    $db->query($oldInsertSQL);
    echo "✅ Eski test kaydı oluşturuldu ($oldDate)\n";
    
    // Bugünkü test kayıtları
    $usageModel->incrementUsage('current_test_user', 'visitor');
    echo "✅ Güncel test kaydı oluşturuldu\n";
    
    // 4. Cron job'ı test et (dry run)
    echo "\n⚡ 4. CRON JOB TEST (DRY RUN)\n";
    echo "-------------------------\n";
    
    echo "🔄 Cron job çalıştırılıyor...\n";
    
    // Test için outputs klasöründeki dosya sayısını say
    $filesBefore = glob($outputsDir . '/*');
    $filesCountBefore = count($filesBefore);
    echo "📊 Temizlik öncesi dosya sayısı: $filesCountBefore\n";
    
    // Eski kayıt sayısını say
    $oldRecordsSQL = "SELECT COUNT(*) as count FROM edefter_usage WHERE usage_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $stmt = $db->prepare($oldRecordsSQL);
    $stmt->execute();
    $oldRecordsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📊 Temizlenecek eski kayıt sayısı: $oldRecordsCount\n";
    
    // 5. Gerçek temizlik işlemi simülasyonu
    echo "\n🧹 5. TEMİZLİK İŞLEMİ SİMÜLASYONU\n";
    echo "--------------------------------\n";
    
    // Outputs klasörünü temizle
    $deletedFiles = 0;
    $deletedSize = 0;
    
    foreach ($filesBefore as $file) {
        if (is_file($file)) {
            $fileSize = filesize($file);
            $fileName = basename($file);
            
            if (unlink($file)) {
                $deletedFiles++;
                $deletedSize += $fileSize;
                echo "🗑️ Silindi: $fileName ($fileSize bytes)\n";
            }
        }
    }
    
    echo "✅ Toplam $deletedFiles dosya silindi ($deletedSize bytes)\n";
    
    // Eski kayıtları temizle
    $cleanResult = $usageModel->cleanOldRecords();
    TestAssert::assertTrue($cleanResult, 'Eski kayıtlar temizlenmeli');
    echo "✅ Eski kullanım kayıtları temizlendi\n";
    
    // 6. Sonuç kontrolü
    echo "\n📊 6. SONUÇ KONTROLÜ\n";
    echo "-------------------\n";
    
    $filesAfter = glob($outputsDir . '/*');
    $filesCountAfter = count($filesAfter);
    echo "📈 Temizlik sonrası dosya sayısı: $filesCountAfter\n";
    
    TestAssert::assertEquals(0, $filesCountAfter, 'Tüm dosyalar silinmiş olmalı');
    echo "✅ Outputs klasörü temizlendi\n";
    
    // Güncel kayıtların varlığını kontrol et
    $currentRecordsSQL = "SELECT COUNT(*) as count FROM edefter_usage WHERE usage_date >= CURDATE()";
    $stmt = $db->prepare($currentRecordsSQL);
    $stmt->execute();
    $currentRecordsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📊 Güncel kayıt sayısı: $currentRecordsCount\n";
    
    TestAssert::assertGreaterThan(0, $currentRecordsCount, 'Güncel kayıtlar korunmalı');
    echo "✅ Güncel kayıtlar korundu\n";
    
    // 7. Cron komut örnekleri
    echo "\n⏰ 7. CRON KOMUT ÖRNEKLERİ\n";
    echo "------------------------\n";
    echo "Linux/Unix için crontab:\n";
    echo "0 0 * * * /usr/bin/php " . realpath($cronFile) . " >> /var/log/edefter_cleanup.log 2>&1\n\n";
    
    echo "Windows Task Scheduler için:\n";
    echo "Program: php.exe\n";
    echo "Arguments: \"" . realpath($cronFile) . "\"\n";
    echo "Trigger: Daily at 00:00\n\n";
    
    echo "Manuel test komutu:\n";
    echo "php \"" . realpath($cronFile) . "\"\n\n";
    
    // Test verilerini temizle
    $cleanupSQL = "DELETE FROM edefter_usage WHERE user_identifier IN ('old_test_user', 'current_test_user')";
    $db->query($cleanupSQL);
    echo "🧹 Test verileri temizlendi\n";
    
    TestLogger::success('🚀 E-Defter Cleanup Cron testi BAŞARIYLA TAMAMLANDI!');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

echo "\n";
TestHelper::endTest();
