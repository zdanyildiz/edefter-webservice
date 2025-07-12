<?php
include_once __DIR__ . '/../index.php';

TestHelper::startTest('Eski Tracking Sisteminden Platform Tracking Sistemine Geçiş');

try {
    // Test veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // Platform tracking tablosunun varlığını kontrol et
    if (!$db->tableExists('platform_tracking')) {
        TestLogger::warning('Platform tracking tablosu bulunamadı, oluşturuluyor...');
        
        $createTableSQL = "
        CREATE TABLE IF NOT EXISTS platform_tracking (
            tracking_id INT AUTO_INCREMENT PRIMARY KEY,
            platform VARCHAR(50) NOT NULL COMMENT 'Platform adı (google_analytics, facebook_pixel, etc.)',
            language_id INT DEFAULT 1 COMMENT 'Dil ID',
            config TEXT COMMENT 'Platform yapılandırması (JSON)',
            status BOOLEAN DEFAULT TRUE COMMENT 'Aktif/Pasif',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_platform_lang (platform, language_id),
            KEY idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->exec($createTableSQL);
        TestLogger::success('Platform tracking tablosu oluşturuldu');
    }
    
    // PlatformTrackingManager'ı yükle
    include_once __DIR__ . '/../../App/Helpers/PlatformTrackingManager.php';
    $config = new stdClass();
    $trackingManager = new PlatformTrackingManager($db, $config);
    
    // Eski sistemlerden veri geçişi
    TestLogger::info('Eski tracking sistemlerinden veri geçişi başlıyor...');
    
    // 1. Tag Manager verilerini geçir
    if ($db->tableExists('tag_manager')) {
        $stmt = $db->query("SELECT * FROM tag_manager WHERE status = 1");
        $tagManagers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($tagManagers as $tagManager) {
            $config = [
                'tag_id' => $tagManager['tag_manager_head'] ?? '',
                'container_id' => $tagManager['tag_manager_content'] ?? ''
            ];
            
            $result = $trackingManager->savePlatformConfig('google_tag_manager', $config, $tagManager['language_id']);
            
            if ($result) {
                TestLogger::success('Tag Manager verisi geçirildi: ' . $tagManager['tag_manager_name']);
            }
        }
    }
    
    // 2. Sales Conversion Code verilerini Google Ads'e geçir
    if ($db->tableExists('sales_conversion_codes')) {
        $stmt = $db->query("SELECT * FROM sales_conversion_codes WHERE status = 1");
        $salesCodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($salesCodes as $salesCode) {
            // Kod içeriğinden Google Ads bilgilerini çıkar
            $content = $salesCode['salesConversionCodeContent'] ?? '';
            
            // AW-XXXXXXX formatını ara
            if (preg_match('/AW-(\d+)/', $content, $matches)) {
                $conversionId = 'AW-' . $matches[1];
                
                // Conversion label'ı ara
                $conversionLabel = '';
                if (preg_match('/\/([A-Za-z0-9_-]+)/', $content, $labelMatches)) {
                    $conversionLabel = $labelMatches[1];
                }
                
                $config = [
                    'conversion_id' => $conversionId,
                    'conversion_label' => $conversionLabel
                ];
                
                $result = $trackingManager->savePlatformConfig('google_ads', $config, $salesCode['languageID']);
                
                if ($result) {
                    TestLogger::success('Sales Conversion Code geçirildi: ' . $salesCode['salesConversionCodeName']);
                }
            }
        }
    }
    
    // 3. Analysis Code verilerini Google Analytics'e geçir
    if ($db->tableExists('analysis_codes')) {
        $stmt = $db->query("SELECT * FROM analysis_codes WHERE status = 1");
        $analysisCodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($analysisCodes as $analysisCode) {
            $content = $analysisCode['analysisCodeContent'] ?? '';
            
            // Google Analytics tracking ID'sini ara (GA-XXXXX-X veya G-XXXXXXXXXX)
            $trackingId = '';
            $measurementId = '';
            
            if (preg_match('/GA-[0-9]+-[0-9]+/', $content, $matches)) {
                $trackingId = $matches[0];
            }
            
            if (preg_match('/G-[A-Z0-9]+/', $content, $matches)) {
                $measurementId = $matches[0];
            }
            
            if ($trackingId || $measurementId) {
                $config = [
                    'tracking_id' => $trackingId,
                    'measurement_id' => $measurementId
                ];
                
                $result = $trackingManager->savePlatformConfig('google_analytics', $config, $analysisCode['languageID']);
                
                if ($result) {
                    TestLogger::success('Analysis Code geçirildi: ' . $analysisCode['analysisCodeName']);
                }
            }
        }
    }
    
    // 4. Facebook Pixel verilerini geçir (eğer varsa)
    if ($db->tableExists('facebook_pixels')) {
        $stmt = $db->query("SELECT * FROM facebook_pixels WHERE status = 1");
        $facebookPixels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($facebookPixels as $pixel) {
            $content = $pixel['pixel_content'] ?? '';
            
            // Facebook Pixel ID'sini ara
            if (preg_match('/fbq\("init",\s*"(\d+)"/', $content, $matches)) {
                $pixelId = $matches[1];
                
                $config = [
                    'pixel_id' => $pixelId
                ];
                
                $result = $trackingManager->savePlatformConfig('facebook_pixel', $config, $pixel['language_id']);
                
                if ($result) {
                    TestLogger::success('Facebook Pixel geçirildi: ' . $pixel['pixel_name']);
                }
            }
        }
    }
    
    // 5. Mevcut platform tracking verilerini kontrol et
    TestLogger::info('Mevcut platform tracking konfigürasyonları:');
    
    $activePlatforms = $trackingManager->getActivePlatforms(1);
    foreach ($activePlatforms as $platform) {
        $config = json_decode($platform['config'], true);
        TestLogger::info('- ' . $platform['platform'] . ': ' . json_encode($config));
    }
    
    // 6. Test tracking kodları oluştur
    TestLogger::info('Head tracking kodları oluşturuluyor...');
    $headCodes = $trackingManager->generateHeadCodes(1);
    
    if (!empty($headCodes)) {
        TestLogger::success('Head tracking kodları başarıyla oluşturuldu');
        TestLogger::info('Kod uzunluğu: ' . strlen($headCodes) . ' karakter');
        
        // İlk 500 karakteri göster
        $preview = substr($headCodes, 0, 500);
        TestLogger::info('Kod önizlemesi: ' . $preview . '...');
    } else {
        TestLogger::warning('Head tracking kodu oluşturulamadı');
    }
    
    // 7. Test dönüşüm kodu oluştur
    TestLogger::info('Test dönüşüm kodu oluşturuluyor...');
    $conversionCode = $trackingManager->generateConversionCode('google_ads', 'purchase', [
        'value' => 150.75,
        'currency' => 'TRY',
        'order_id' => 'TEST_ORDER_123'
    ], 1);
    
    if (!empty($conversionCode)) {
        TestLogger::success('Dönüşüm kodu başarıyla oluşturuldu');
        TestLogger::info('Dönüşüm kodu: ' . $conversionCode);
    }
    
    TestLogger::success('Eski sistemden Platform Tracking sistemine geçiş tamamlandı');
    
    // 8. Kullanım örnekleri
    TestLogger::info('KULLANIM ÖRNEKLERİ:');
    TestLogger::info('1. Head bölümünde kullanım:');
    TestLogger::info('<?php echo HeadTrackingInjector::inject($db, $config, $languageID); ?>');
    TestLogger::info('');
    TestLogger::info('2. Ürün sayfasında kullanım:');
    TestLogger::info('<?php echo HeadTrackingInjector::inject($db, $config, $languageID, "product", $productData); ?>');
    TestLogger::info('');
    TestLogger::info('3. Satın alma sayfasında kullanım:');
    TestLogger::info('<?php echo HeadTrackingInjector::inject($db, $config, $languageID, "thankyou", $orderData); ?>');
    
} catch (Exception $e) {
    TestLogger::error('Geçiş hatası: ' . $e->getMessage());
}

TestHelper::endTest();
