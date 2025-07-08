<?php
// Tests/EDefterUsage/test_cleanup_verbose.php
// EDefterCleanup cron job'ını test etmek için

include_once __DIR__ . '/../index.php';

TestHelper::startTest('EDefterCleanup Cron Job Test');

try {
    // Test dosyası oluştur
    $outputDir = 'Public/File/outputs';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }
    
    // Test dosyası oluştur
    $testFile = $outputDir . '/test_cleanup_' . time() . '.xml';
    file_put_contents($testFile, '<?xml version="1.0"?><test>Test content</test>');
    
    TestLogger::info("Test dosyası oluşturuldu: " . basename($testFile));
    
    // Cron job'ı çalıştır
    TestLogger::info("EDefterCleanup cron job çalıştırılıyor...");
    
    // Cron job'ı exec ile çalıştır ve çıktıyı yakala
    $output = [];
    $returnCode = 0;
    exec('php App\Cron\EDefterCleanup.php 2>&1', $output, $returnCode);
    
    TestLogger::info("Cron job return code: $returnCode");
    TestLogger::info("Cron job output: " . implode("\n", $output));
    
    // Test dosyasının silindiğini kontrol et
    if (!file_exists($testFile)) {
        TestLogger::success("Test dosyası başarıyla silindi");
    } else {
        TestLogger::error("Test dosyası silinemedi");
    }
    
    // Log dosyasını kontrol et
    $logFile = 'Public/Log/Admin/' . date('Y-m-d') . '.log';
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $cleanupLogs = array_filter(explode("\n", $logContent), function($line) {
            return strpos($line, 'edefter-cleanup') !== false;
        });
        
        if (!empty($cleanupLogs)) {
            TestLogger::success("Log kayıtları bulundu:");
            foreach (array_slice($cleanupLogs, -5) as $log) {
                TestLogger::info("  " . $log);
            }
        } else {
            TestLogger::warning("Edefter-cleanup log kayıtları bulunamadı");
        }
    }

} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

TestHelper::endTest();
?>
