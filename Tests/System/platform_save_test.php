<?php
/**
 * Platform Tracking Save Test
 * Kaydetme işlemlerini test eder
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Save Test');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Test config'leri oluştur (gerçek proje olmadığı için config'i bypass edeceğiz)
    $projectRoot = dirname(__DIR__, 2);
    include_once $projectRoot . '/App/Helpers/PlatformTrackingManager.php';
    
    // Mock config nesnesi
    $mockConfig = new stdClass();
    $platformManager = new PlatformTrackingManager($db, $mockConfig);
    
    TestLogger::info('Platform Tracking Manager test başlıyor...');
    
    // 1. Mevcut TikTok kaydı var mı kontrol et
    $sql = "SELECT * FROM platform_tracking WHERE platform = 'tiktok_pixel' AND language_id = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        TestLogger::info('TikTok kaydı mevcut - UPDATE testi yapılacak');
        
        // UPDATE testi
        $updateConfig = ['pixel_id' => 'TEST_TIKTOK_UPDATE_' . time()];
        $result = $platformManager->savePlatformConfig('tiktok_pixel', $updateConfig, 1, 1);
        TestAssert::assertTrue($result, 'TikTok UPDATE işlemi başarılı olmalı');
        
    } else {
        TestLogger::info('TikTok kaydı yok - INSERT testi yapılacak');
        
        // INSERT testi
        $insertConfig = ['pixel_id' => 'TEST_TIKTOK_INSERT_' . time()];
        $result = $platformManager->savePlatformConfig('tiktok_pixel', $insertConfig, 1, 1);
        TestAssert::assertTrue($result, 'TikTok INSERT işlemi başarılı olmalı');
    }
    
    // 2. LinkedIn test - INSERT (muhtemelen yok)
    $linkedinConfig = ['partner_id' => 'TEST_LINKEDIN_' . time()];
    $result2 = $platformManager->savePlatformConfig('linkedin_insight', $linkedinConfig, 1, 0);
    TestAssert::assertTrue($result2, 'LinkedIn INSERT işlemi başarılı olmalı');
    
    // 3. Mevcut Facebook kaydını güncelle - UPDATE
    $fbConfig = ['pixel_id' => 'TEST_FB_UPDATE_' . time()];
    $result3 = $platformManager->savePlatformConfig('facebook_pixel', $fbConfig, 1, 0);
    TestAssert::assertTrue($result3, 'Facebook UPDATE işlemi başarılı olmalı');
    
    // Son durumu kontrol et
    $sql = "SELECT platform, status, config, updated_at FROM platform_tracking 
            WHERE platform IN ('facebook_pixel', 'tiktok_pixel', 'linkedin_insight') 
            AND language_id = 1 
            ORDER BY platform";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    TestLogger::info('Test sonrası veriler:');
    foreach ($results as $result) {
        TestLogger::info("- {$result['platform']}: Status={$result['status']}, Updated={$result['updated_at']}");
        $config = json_decode($result['config'], true);
        if ($config) {
            foreach ($config as $key => $value) {
                TestLogger::info("  Config $key: $value");
            }
        }
    }
    
    TestLogger::success('Platform Tracking save testi tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    TestLogger::error('Stack trace: ' . $e->getTraceAsString());
}

TestHelper::endTest();
?>
