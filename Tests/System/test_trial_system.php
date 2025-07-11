<?php
// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// Test başlat
TestHelper::startTest('Deneme Kullanıcısı Sistemi Testi');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // MemberModel yükle
    require_once __DIR__ . '/../../App/Webservice/MemberModel.php';
    $memberModel = new MemberModel($db);
    TestAssert::assertNotNull($memberModel, 'MemberModel yüklenmelidir');
    
    // trial_users tablosunun varlığını kontrol et
    TestAssert::assertTrue($db->tableExists('trial_users'), 'trial_users tablosu mevcut olmalı');
    
    // Tablo yapısını kontrol et
    $tableInfo = $db->getTableInfo('trial_users');
    TestAssert::assertNotEmpty($tableInfo, 'trial_users tablo bilgisi alınmalı');
    
    // Gerekli sütunları kontrol et
    $requiredColumns = ['member_id', 'trial_start_date', 'trial_end_date', 'is_active'];
    foreach ($requiredColumns as $column) {
        TestAssert::assertTrue(
            $db->columnExists('trial_users', $column), 
            "trial_users tablosunda {$column} sütunu mevcut olmalı"
        );
    }
    
    // Test member ID'si
    $testMemberID = 9999;
    
    // Deneme kullanıcısı ekleme testi
    $addResult = $memberModel->addTrialUser($testMemberID, 30);
    TestAssert::assertTrue($addResult, 'Deneme kullanıcısı eklenmelidir');
    
    // Deneme kullanıcısı kontrolü
    $trialUser = $memberModel->checkTrialUser($testMemberID);
    TestAssert::assertNotEmpty($trialUser, 'Deneme kullanıcısı bulunmalı');
    TestAssert::assertEquals($testMemberID, $trialUser[0]['member_id'], 'Member ID eşleşmeli');
    
    // Deneme süresi kontrolü
    $isExpired = $memberModel->isTrialExpired($testMemberID);
    TestAssert::assertFalse($isExpired, 'Yeni oluşturulan deneme süresi dolmamalı');
    
    // Deneme kullanıcısını deaktif etme
    $deactivateResult = $memberModel->deactivateTrialUser($testMemberID);
    TestAssert::assertTrue($deactivateResult, 'Deneme kullanıcısı deaktif edilmelidir');
    
    // Deaktif edilen kullanıcı kontrolü
    $deactivatedUser = $memberModel->checkTrialUser($testMemberID);
    TestAssert::assertEmpty($deactivatedUser, 'Deaktif edilen kullanıcı bulunmamalı');
    
    TestLogger::success('✅ Deneme kullanıcısı sistemi testi başarıyla tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('❌ Test hatası: ' . $e->getMessage());
    TestLogger::error('Stack trace: ' . $e->getTraceAsString());
}

// Test sonlandır
TestHelper::endTest();
