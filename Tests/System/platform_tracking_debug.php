<?php
/**
 * Platform Tracking Debug Test
 * Veritabanı tablosu ve kaydetme işlemlerini test eder
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Debug Test');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Platform tracking tablosunun varlığını kontrol et
    TestAssert::assertTrue($db->tableExists('platform_tracking'), 'platform_tracking tablosu mevcut olmalı');
    
    // Tablo yapısını kontrol et
    $tableInfo = $db->getTableInfo('platform_tracking');
    TestLogger::info('Platform Tracking Tablo Yapısı:');
    foreach ($tableInfo as $column) {
        TestLogger::info("- {$column['Field']}: {$column['Type']} " . 
                        ($column['Null'] === 'YES' ? '(NULL)' : '(NOT NULL)') .
                        ($column['Key'] === 'PRI' ? ' PRIMARY KEY' : ''));
    }
    
    // Mevcut verileri kontrol et
    $sql = "SELECT * FROM platform_tracking ORDER BY platform, language_id";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    TestLogger::info('Mevcut Platform Tracking Verileri:');
    if (empty($records)) {
        TestLogger::warning('Hiç platform tracking kaydı bulunamadı');
    } else {
        foreach ($records as $record) {
            TestLogger::info("- Platform: {$record['platform']}, Language: {$record['language_id']}, Status: {$record['status']}");
        }
    }
    
    // Test verisi ekleyerek kaydetme işlemini test et
    $projectRoot = dirname(__DIR__, 2);
    include_once $projectRoot . '/App/Helpers/PlatformTrackingManager.php';
    include_once $projectRoot . '/App/Core/Config.php';
    
    $config = new Config();
    $platformManager = new PlatformTrackingManager($db, $config);
    
    // Test konfigürasyonu
    $testConfig = [
        'pixel_id' => '123456789',
        'access_token' => 'test_token'
    ];
    
    TestLogger::info('Test konfigürasyonu kaydediliyor...');
    
    // Facebook pixel test - aktif
    $result1 = $platformManager->savePlatformConfig('facebook_pixel', $testConfig, 1, 1);
    TestAssert::assertTrue($result1, 'Facebook Pixel aktif kaydı başarılı olmalı');
    
    // Facebook pixel test - pasif
    $result2 = $platformManager->savePlatformConfig('facebook_pixel', $testConfig, 1, 0);
    TestAssert::assertTrue($result2, 'Facebook Pixel pasif kaydı başarılı olmalı');
    
    // Google Analytics test
    $gaConfig = ['tracking_id' => 'GA-123456-1'];
    $result3 = $platformManager->savePlatformConfig('google_analytics', $gaConfig, 1, 1);
    TestAssert::assertTrue($result3, 'Google Analytics kaydı başarılı olmalı');
    
    // Sonuçları kontrol et
    $sql = "SELECT platform, status, config FROM platform_tracking WHERE language_id = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    TestLogger::info('Test sonrası veriler:');
    foreach ($results as $result) {
        TestLogger::info("- {$result['platform']}: Status={$result['status']}, Config=" . 
                        substr($result['config'], 0, 50) . (strlen($result['config']) > 50 ? '...' : ''));
    }
    
    TestLogger::success('Platform Tracking debug testi tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    TestLogger::error('Stack trace: ' . $e->getTraceAsString());
}

TestHelper::endTest();
?>
