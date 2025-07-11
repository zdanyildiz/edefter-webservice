<?php
// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// Test başlat
TestHelper::startTest('Deneme Kullanıcısı Basit Testi');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // trial_users tablosunu manuel oluştur
    $createTrialUsersTable = "
        CREATE TABLE IF NOT EXISTS trial_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id INT NOT NULL,
            trial_start_date DATETIME NOT NULL,
            trial_end_date DATETIME NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_member_id (member_id)
        )
    ";
    
    $db->execute($createTrialUsersTable);
    TestLogger::info('trial_users tablosu oluşturuldu');
    
    // Tablo varlığını kontrol et
    TestAssert::assertTrue($db->tableExists('trial_users'), 'trial_users tablosu mevcut olmalı');
    
    // Test verileri ekle
    $testMemberID = 9999;
    $trialStartDate = date('Y-m-d H:i:s');
    $trialEndDate = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $insertQuery = "
        INSERT INTO trial_users (member_id, trial_start_date, trial_end_date, is_active)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        trial_start_date = VALUES(trial_start_date),
        trial_end_date = VALUES(trial_end_date),
        is_active = VALUES(is_active)
    ";
    
    $db->execute($insertQuery, [$testMemberID, $trialStartDate, $trialEndDate, true]);
    TestLogger::info('Test deneme kullanıcısı eklendi');
    
    // Deneme kullanıcısı kontrolü
    $selectQuery = "SELECT * FROM trial_users WHERE member_id = ? AND is_active = 1";
    $result = $db->query($selectQuery, [$testMemberID]);
    TestAssert::assertNotEmpty($result, 'Deneme kullanıcısı bulunmalı');
    
    // Süre kontrolü
    $trialUser = $result[0];
    $isExpired = strtotime($trialUser['trial_end_date']) < time();
    TestAssert::assertFalse($isExpired, 'Yeni oluşturulan deneme süresi dolmamalı');
    
    // Temizlik
    $deleteQuery = "DELETE FROM trial_users WHERE member_id = ?";
    $db->execute($deleteQuery, [$testMemberID]);
    TestLogger::info('Test verisi temizlendi');
    
    TestLogger::success('✅ Deneme kullanıcısı basit testi başarıyla tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('❌ Test hatası: ' . $e->getMessage());
    TestLogger::error('Stack trace: ' . $e->getTraceAsString());
}

// Test sonlandır
TestHelper::endTest();
