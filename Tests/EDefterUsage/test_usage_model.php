<?php
/**
 * EDefterUsage model sınıfını test et
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('EDefterUsage Model Testi');

try {
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Model sınıfını yükle
    require_once __DIR__ . '/../../App/Model/EDefterUsage.php';
    $usageModel = new EDefterUsage($db);
    TestAssert::assertNotNull($usageModel, 'EDefterUsage modeli yüklenmeli');
    
    // Test kullanıcıları
    $testVisitor = 'test_visitor_' . time();
    $testMember = 'test_member_' . time();
    
    TestLogger::info("Test kullanıcıları: Ziyaretçi=$testVisitor, Üye=$testMember");
    
    // 1. Ziyaretçi testleri
    TestLogger::info('=== ZİYARETÇİ TESTLERİ ===');
    
    // İlk kullanım kontrolü
    $initialUsage = $usageModel->getDailyUsage($testVisitor);
    TestAssert::assertEquals(0, $initialUsage, 'İlk kullanım 0 olmalı');
    
    // Sınır kontrolü
    $isExceeded = $usageModel->isLimitExceeded($testVisitor, 'visitor');
    TestAssert::assertFalse($isExceeded, 'İlk durumda sınır aşılmamalı');
    
    // 5 kez kullanım ekle (sınır)
    for ($i = 1; $i <= 5; $i++) {
        $result = $usageModel->incrementUsage($testVisitor, 'visitor');
        TestAssert::assertTrue($result, "Kullanım $i eklenmeli");
        
        $currentUsage = $usageModel->getDailyUsage($testVisitor);
        TestAssert::assertEquals($i, $currentUsage, "Kullanım sayısı $i olmalı");
        
        $remaining = $usageModel->getRemainingUsage($testVisitor, 'visitor');
        TestAssert::assertEquals(5 - $i, $remaining, "Kalan hak " . (5 - $i) . " olmalı");
    }
    
    // Sınır kontrolü (5 kullanım sonrası)
    $isExceeded = $usageModel->isLimitExceeded($testVisitor, 'visitor');
    TestAssert::assertTrue($isExceeded, '5 kullanım sonrası sınır aşılmalı');
    
    // 6. kullanım ekleme (sınır aşımı)
    $usageModel->incrementUsage($testVisitor, 'visitor');
    $finalUsage = $usageModel->getDailyUsage($testVisitor);
    TestAssert::assertEquals(6, $finalUsage, 'Sınır aşımında da sayaç artmalı');
    
    TestLogger::success('Ziyaretçi testleri başarılı');
    
    // 2. Üye testleri
    TestLogger::info('=== ÜYE TESTLERİ ===');
    
    // İlk kullanım kontrolü
    $initialMemberUsage = $usageModel->getDailyUsage($testMember);
    TestAssert::assertEquals(0, $initialMemberUsage, 'Üye ilk kullanım 0 olmalı');
    
    // 10 kez kullanım ekle
    for ($i = 1; $i <= 10; $i++) {
        $result = $usageModel->incrementUsage($testMember, 'member');
        TestAssert::assertTrue($result, "Üye kullanım $i eklenmeli");
    }
    
    $memberUsage = $usageModel->getDailyUsage($testMember);
    TestAssert::assertEquals(10, $memberUsage, 'Üye 10 kullanım olmalı');
    
    // Sınır kontrolü (10 kullanım, limit 20)
    $isMemberExceeded = $usageModel->isLimitExceeded($testMember, 'member');
    TestAssert::assertFalse($isMemberExceeded, '10 kullanımda üye sınırı aşılmamalı');
    
    $memberRemaining = $usageModel->getRemainingUsage($testMember, 'member');
    TestAssert::assertEquals(10, $memberRemaining, 'Üye 10 hak kalmalı');
    
    TestLogger::success('Üye testleri başarılı');
    
    // 3. Kullanım bilgisi testi
    TestLogger::info('=== KULLANIM BİLGİSİ TESTİ ===');
    
    $visitorInfo = $usageModel->getUsageInfo($testVisitor, 'visitor');
    TestAssert::assertEquals(6, $visitorInfo['current_usage'], 'Ziyaretçi kullanım 6 olmalı');
    TestAssert::assertEquals(5, $visitorInfo['daily_limit'], 'Ziyaretçi limit 5 olmalı');
    TestAssert::assertTrue($visitorInfo['is_limit_exceeded'], 'Ziyaretçi sınır aşımı true olmalı');
    
    $memberInfo = $usageModel->getUsageInfo($testMember, 'member');
    TestAssert::assertEquals(10, $memberInfo['current_usage'], 'Üye kullanım 10 olmalı');
    TestAssert::assertEquals(20, $memberInfo['daily_limit'], 'Üye limit 20 olmalı');
    TestAssert::assertFalse($memberInfo['is_limit_exceeded'], 'Üye sınır aşımı false olmalı');
    
    TestLogger::success('Kullanım bilgisi testleri başarılı');
    
    // Test verilerini temizle
    $cleanSQL = "DELETE FROM edefter_usage WHERE user_identifier IN (?, ?)";
    $stmt = $db->prepare($cleanSQL);
    $stmt->execute([$testVisitor, $testMember]);
    TestLogger::info('Test verileri temizlendi');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

TestHelper::endTest();
