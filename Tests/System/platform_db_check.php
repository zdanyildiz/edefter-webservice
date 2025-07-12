<?php
/**
 * Platform Tracking Debug Test - Basit Versiyon
 * Sadece veritabanı durumunu kontrol eder
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Database Check');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Platform tracking tablosunun varlığını kontrol et
    TestAssert::assertTrue($db->tableExists('platform_tracking'), 'platform_tracking tablosu mevcut olmalı');
    
    // Mevcut verileri detaylıca kontrol et
    $sql = "SELECT * FROM platform_tracking ORDER BY platform, language_id";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    TestLogger::info('Platform Tracking Verileri (Detaylı):');
    foreach ($records as $record) {
        TestLogger::info("Platform: {$record['platform']}");
        TestLogger::info("  - Language ID: {$record['language_id']}");
        TestLogger::info("  - Status: {$record['status']}");
        TestLogger::info("  - Config: " . substr($record['config'], 0, 100) . (strlen($record['config']) > 100 ? '...' : ''));
        TestLogger::info("  - Created: {$record['created_at']}");
        TestLogger::info("  - Updated: {$record['updated_at']}");
        TestLogger::info("---");
    }
    
    // Facebook pixel kaydını özel olarak kontrol et
    $sql = "SELECT * FROM platform_tracking WHERE platform = 'facebook_pixel' AND language_id = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $fbRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($fbRecord) {
        TestLogger::info('Facebook Pixel Kaydı Detayı:');
        TestLogger::info('Status: ' . $fbRecord['status']);
        TestLogger::info('Config: ' . $fbRecord['config']);
        
        // Config'i parse et
        $config = json_decode($fbRecord['config'], true);
        if ($config) {
            TestLogger::info('Config Fields:');
            foreach ($config as $key => $value) {
                TestLogger::info("  - $key: $value");
            }
        }
    } else {
        TestLogger::warning('Facebook Pixel kaydı bulunamadı');
    }
    
    // Test UPDATE işlemi
    TestLogger::info('Test UPDATE işlemi başlatılıyor...');
    
    // Status değiştirme testi
    $sql = "UPDATE platform_tracking SET status = 1, updated_at = NOW() WHERE platform = 'facebook_pixel' AND language_id = 1";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute();
    
    if ($result) {
        TestLogger::success('Facebook Pixel status 1 olarak güncellendi');
        
        // Güncellenmiş hali
        $sql = "SELECT status, updated_at FROM platform_tracking WHERE platform = 'facebook_pixel' AND language_id = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $updated = $stmt->fetch(PDO::FETCH_ASSOC);
        TestLogger::info('Güncellenmiş durum: Status=' . $updated['status'] . ', Updated=' . $updated['updated_at']);
    } else {
        TestLogger::error('Facebook Pixel güncellenemedi');
        $errorInfo = $stmt->errorInfo();
        TestLogger::error('SQL Error: ' . json_encode($errorInfo));
    }
    
    TestLogger::success('Veritabanı kontrol testi tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    TestLogger::error('Stack trace: ' . $e->getTraceAsString());
}

TestHelper::endTest();
?>
