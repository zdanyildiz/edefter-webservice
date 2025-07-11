<?php
// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// Test başlat
TestHelper::startTest('Deneme Kullanıcısı Manuel Testi');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // Manuel SQL komutları ile test
    TestLogger::info('📋 Manuel SQL komutları ile deneme kullanıcısı sistemi testi...');
    
    // 1. Tablo oluşturma SQL'i
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS trial_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        member_id INT NOT NULL,
        trial_start_date DATETIME NOT NULL,
        trial_end_date DATETIME NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_member_id (member_id)
    )";
    
    TestLogger::info('📝 Tablo oluşturma SQL\'i hazırlandı');
    
    // 2. Test verisi ekleme SQL'i
    $insertSQL = "
    INSERT INTO trial_users (member_id, trial_start_date, trial_end_date, is_active)
    VALUES (9999, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), TRUE)
    ON DUPLICATE KEY UPDATE
    trial_start_date = VALUES(trial_start_date),
    trial_end_date = VALUES(trial_end_date),
    is_active = VALUES(is_active)";
    
    TestLogger::info('📝 Test verisi ekleme SQL\'i hazırlandı');
    
    // 3. Deneme kullanıcısı kontrol SQL'i
    $checkSQL = "SELECT * FROM trial_users WHERE member_id = 9999 AND is_active = 1";
    
    TestLogger::info('📝 Deneme kullanıcısı kontrol SQL\'i hazırlandı');
    
    // 4. Süre kontrolü SQL'i
    $expiredCheckSQL = "SELECT trial_end_date, (trial_end_date < NOW()) as is_expired FROM trial_users WHERE member_id = 9999";
    
    TestLogger::info('📝 Süre kontrolü SQL\'i hazırlandı');
    
    // 5. Temizlik SQL'i
    $cleanupSQL = "DELETE FROM trial_users WHERE member_id = 9999";
    
    TestLogger::info('📝 Temizlik SQL\'i hazırlandı');
    
    // SQL komutlarını ekrana yazdır
    TestLogger::info('=== KULLANILACAK SQL KOMUTLARI ===');
    TestLogger::info('1. Tablo oluşturma:');
    TestLogger::info($createTableSQL);
    TestLogger::info('');
    TestLogger::info('2. Test verisi ekleme:');
    TestLogger::info($insertSQL);
    TestLogger::info('');
    TestLogger::info('3. Deneme kullanıcısı kontrol:');
    TestLogger::info($checkSQL);
    TestLogger::info('');
    TestLogger::info('4. Süre kontrolü:');
    TestLogger::info($expiredCheckSQL);
    TestLogger::info('');
    TestLogger::info('5. Temizlik:');
    TestLogger::info($cleanupSQL);
    
    TestLogger::success('✅ Deneme kullanıcısı sistemi SQL komutları hazırlandı');
    TestLogger::info('💡 Bu SQL komutlarını phpMyAdmin veya benzer bir araçla çalıştırarak test edebilirsiniz');
    
} catch (Exception $e) {
    TestLogger::error('❌ Test hatası: ' . $e->getMessage());
    TestLogger::error('Stack trace: ' . $e->getTraceAsString());
}

// Test sonlandır
TestHelper::endTest();
