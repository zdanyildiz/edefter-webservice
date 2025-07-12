<?php
include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Test');

try {
    // Test veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // Platform tracking tablosunu manuel oluştur
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
    
    $result = $db->exec($createTableSQL);
    TestLogger::info('Platform tracking tablosu oluşturuldu');
    
    // Tablo varlığını kontrol et
    TestAssert::assertTrue($db->tableExists('platform_tracking'), 'Platform tracking tablosu mevcut olmalı');
    
    // Test verileri ekle
    $testData = [
        [
            'platform' => 'google_analytics',
            'language_id' => 1,
            'config' => json_encode([
                'tracking_id' => 'GA-XXXXX-X',
                'measurement_id' => 'G-XXXXXXXXXX'
            ]),
            'status' => 1
        ],
        [
            'platform' => 'facebook_pixel',
            'language_id' => 1,
            'config' => json_encode([
                'pixel_id' => '123456789'
            ]),
            'status' => 1
        ],
        [
            'platform' => 'google_ads',
            'language_id' => 1,
            'config' => json_encode([
                'conversion_id' => 'AW-123456789',
                'conversion_label' => 'AbC-D_efGhIjKlMnOpQr'
            ]),
            'status' => 1
        ]
    ];
    
    foreach ($testData as $data) {
        $sql = "INSERT INTO platform_tracking (platform, language_id, config, status) 
                VALUES (:platform, :language_id, :config, :status)
                ON DUPLICATE KEY UPDATE 
                config = VALUES(config), 
                status = VALUES(status)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
    }
    
    TestLogger::success('Test verileri eklendi');
    
    // PlatformTrackingManager test et
    include_once __DIR__ . '/../../App/Helpers/PlatformTrackingManager.php';
    
    $config = new stdClass();
    $trackingManager = new PlatformTrackingManager($db, $config);
    
    // Google Analytics konfigürasyonunu getir
    $gaConfig = $trackingManager->getPlatformConfig('google_analytics', 1);
    TestAssert::assertNotEmpty($gaConfig, 'Google Analytics konfigürasyonu bulunmalı');
    
    // Head kodlarını oluştur
    $headCodes = $trackingManager->generateHeadCodes(1);
    TestAssert::assertNotEmpty($headCodes, 'Head kodları oluşturulmalı');
    TestAssert::assertStringContains($headCodes, 'googletagmanager.com', 'Google Analytics kodu bulunmalı');
    TestAssert::assertStringContains($headCodes, 'fbq("init"', 'Facebook pixel kodu bulunmalı');
    
    TestLogger::info('Oluşturulan head kodları:');
    TestLogger::info($headCodes);
    
    // Dönüşüm kodu test et
    $conversionCode = $trackingManager->generateConversionCode('google_ads', 'purchase', [
        'value' => 100,
        'currency' => 'TRY'
    ], 1);
    
    TestAssert::assertNotEmpty($conversionCode, 'Dönüşüm kodu oluşturulmalı');
    TestAssert::assertStringContains($conversionCode, 'gtag("event"', 'Google Ads dönüşüm kodu bulunmalı');
    
    TestLogger::info('Oluşturulan dönüşüm kodu:');
    TestLogger::info($conversionCode);
    
    TestLogger::success('Platform Tracking Manager testi başarılı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

TestHelper::endTest();
