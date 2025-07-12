<?php
/**
 * Platform Tracking Frontend Integration Test
 * Frontend head ve body entegrasyonunu test eder
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Frontend Integration Test');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Config mock'u oluştur
    $projectRoot = dirname(__DIR__, 2);
    include_once $projectRoot . '/App/Helpers/PlatformTrackingManager.php';
    
    // Mock config
    $mockConfig = new stdClass();
    $mockConfig->http = 'http://';
    $mockConfig->hostDomain = 'test.local';
    
    $platformManager = new PlatformTrackingManager($db, $mockConfig);
    
    TestLogger::info('Platform Tracking Manager frontend integration testi başlıyor...');
    
    // 1. generateHeadCodes testi
    $headCodes = $platformManager->generateHeadCodes(1);
    TestLogger::info('Head Codes Generated:');
    if (!empty($headCodes)) {
        TestLogger::info('Head codes length: ' . strlen($headCodes) . ' characters');
        TestLogger::info('First 200 chars: ' . substr($headCodes, 0, 200) . '...');
    } else {
        TestLogger::warning('Head codes boş - aktif platform bulunamadı');
    }
    
    // 2. generateConversionCodes testi
    $conversionCodes = $platformManager->generateConversionCodes(1);
    TestLogger::info('Conversion Codes Generated:');
    if (!empty($conversionCodes)) {
        TestLogger::info('Conversion codes length: ' . strlen($conversionCodes) . ' characters');
        TestLogger::info('First 200 chars: ' . substr($conversionCodes, 0, 200) . '...');
    } else {
        TestLogger::warning('Conversion codes boş - body template bulunamadı');
    }
    
    // 3. Aktif platformları kontrol et
    $sql = "SELECT platform, status, config FROM platform_tracking WHERE language_id = 1 AND status = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $activePlatforms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    TestLogger::info('Aktif Platformlar:');
    if (empty($activePlatforms)) {
        TestLogger::warning('Hiç aktif platform bulunamadı!');
        TestAssert::assertGreaterThan(0, count($activePlatforms), 'En az bir aktif platform olmalı');
    } else {
        foreach ($activePlatforms as $platform) {
            TestLogger::info("- {$platform['platform']}: Status={$platform['status']}");
            $config = json_decode($platform['config'], true);
            if ($config) {
                foreach ($config as $key => $value) {
                    TestLogger::info("  $key: $value");
                }
            }
        }
        TestAssert::assertGreaterThan(0, count($activePlatforms), 'Aktif platform sayısı > 0 olmalı');
    }
    
    // 4. Frontend dosyalarının Platform Tracking entegrasyonunu kontrol et
    $headFilePath = $projectRoot . '/App/View/Layouts/head.php';
    $bodyFilePath = $projectRoot . '/App/View/Layouts/body.php';
    
    TestAssert::assertTrue(file_exists($headFilePath), 'head.php dosyası mevcut olmalı');
    TestAssert::assertTrue(file_exists($bodyFilePath), 'body.php dosyası mevcut olmalı');
    
    $headContent = file_get_contents($headFilePath);
    $bodyContent = file_get_contents($bodyFilePath);
    
    // Platform Tracking entegrasyonu kontrolü
    TestAssert::assertStringContains($headContent, 'PlatformTrackingManager', 'head.php Platform Tracking entegrasyonu olmalı');
    TestAssert::assertStringContains($headContent, 'generateHeadCodes', 'head.php generateHeadCodes çağrısı olmalı');
    
    TestAssert::assertStringContains($bodyContent, 'PlatformTrackingManager', 'body.php Platform Tracking entegrasyonu olmalı');
    TestAssert::assertStringContains($bodyContent, 'generateConversionCodes', 'body.php generateConversionCodes çağrısı olmalı');
    
    TestLogger::success('Platform Tracking frontend entegrasyon testi tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    TestLogger::error('Stack trace: ' . $e->getTraceAsString());
}

TestHelper::endTest();
?>
