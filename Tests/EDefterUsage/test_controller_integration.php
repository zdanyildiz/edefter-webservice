<?php
/**
 * EDefterController kullanım sınırlaması testi
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('EDefterController Kullanım Sınırlaması Testi');

try {
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Test kullanıcıları
    $testVisitor = 'test_visitor_' . time();
    $testMember = 'test_member_' . time();
    
    TestLogger::info("Test kullanıcıları oluşturuldu:");
    TestLogger::info("- Ziyaretçi: $testVisitor");
    TestLogger::info("- Üye: $testMember");
    
    // Model sınıfını yükle
    require_once __DIR__ . '/../../App/Model/EDefterUsage.php';
    $usageModel = new EDefterUsage($db);
    
    // 1. Ziyaretçi sınır testi
    TestLogger::info('=== ZİYARETÇİ SINIR TESTİ ===');
    
    // İlk durum - sınır aşılmamış
    $isExceeded = $usageModel->isLimitExceeded($testVisitor, 'visitor');
    TestAssert::assertFalse($isExceeded, 'Ziyaretçi başlangıçta sınır aşmamalı');
    
    // 5 kez kullanım ekle (sınır)
    for ($i = 1; $i <= 5; $i++) {
        $usageModel->incrementUsage($testVisitor, 'visitor');
        $usage = $usageModel->getDailyUsage($testVisitor);
        TestLogger::info("Ziyaretçi kullanım $i: $usage");
    }
    
    // Sınır aşıldı mı kontrol et
    $isExceeded = $usageModel->isLimitExceeded($testVisitor, 'visitor');
    TestAssert::assertTrue($isExceeded, 'Ziyaretçi 5 kullanım sonrası sınırı aşmalı');
    
    $usageInfo = $usageModel->getUsageInfo($testVisitor, 'visitor');
    TestAssert::assertEquals(5, $usageInfo['current_usage'], 'Ziyaretçi kullanım 5 olmalı');
    TestAssert::assertEquals(5, $usageInfo['daily_limit'], 'Ziyaretçi limit 5 olmalı');
    TestAssert::assertTrue($usageInfo['is_limit_exceeded'], 'Ziyaretçi sınır aşımı true olmalı');
    TestAssert::assertEquals(0, $usageInfo['remaining_usage'], 'Ziyaretçi kalan hak 0 olmalı');
    
    TestLogger::success('Ziyaretçi sınır testi başarılı');
    
    // 2. Üye sınır testi
    TestLogger::info('=== ÜYE SINIR TESTİ ===');
    
    // İlk durum - sınır aşılmamış
    $isExceeded = $usageModel->isLimitExceeded($testMember, 'member');
    TestAssert::assertFalse($isExceeded, 'Üye başlangıçta sınır aşmamalı');
    
    // 15 kez kullanım ekle (sınır altında)
    for ($i = 1; $i <= 15; $i++) {
        $usageModel->incrementUsage($testMember, 'member');
    }
    
    $memberUsage = $usageModel->getDailyUsage($testMember);
    TestAssert::assertEquals(15, $memberUsage, 'Üye 15 kullanım olmalı');
    
    // Sınır aşılmadı mı kontrol et
    $isExceeded = $usageModel->isLimitExceeded($testMember, 'member');
    TestAssert::assertFalse($isExceeded, 'Üye 15 kullanımda sınırı aşmamalı');
    
    // 20'ye kadar tamamla
    for ($i = 16; $i <= 20; $i++) {
        $usageModel->incrementUsage($testMember, 'member');
    }
    
    // Sınır aşıldı mı kontrol et
    $isExceeded = $usageModel->isLimitExceeded($testMember, 'member');
    TestAssert::assertTrue($isExceeded, 'Üye 20 kullanım sonrası sınırı aşmalı');
    
    $memberInfo = $usageModel->getUsageInfo($testMember, 'member');
    TestAssert::assertEquals(20, $memberInfo['current_usage'], 'Üye kullanım 20 olmalı');
    TestAssert::assertEquals(20, $memberInfo['daily_limit'], 'Üye limit 20 olmalı');
    TestAssert::assertTrue($memberInfo['is_limit_exceeded'], 'Üye sınır aşımı true olmalı');
    TestAssert::assertEquals(0, $memberInfo['remaining_usage'], 'Üye kalan hak 0 olmalı');
    
    TestLogger::success('Üye sınır testi başarılı');
    
    // 3. Sınır aşımı mesaj testleri
    TestLogger::info('=== SINIR AŞIMI MESAJ TESTLERİ ===');
    
    // Ziyaretçi sınır aşım mesajı
    if ($usageModel->isLimitExceeded($testVisitor, 'visitor')) {
        $usageInfo = $usageModel->getUsageInfo($testVisitor, 'visitor');
        $limitMessage = "Günlük işlem sınırınız (5) dolmuştur. Üye olarak 20 işlem yapabilirsiniz.";
        TestLogger::info("Ziyaretçi mesajı: $limitMessage");
    }
    
    // Üye sınır aşım mesajı
    if ($usageModel->isLimitExceeded($testMember, 'member')) {
        $usageInfo = $usageModel->getUsageInfo($testMember, 'member');
        $limitMessage = "Günlük işlem sınırınız (20) dolmuştur.";
        TestLogger::info("Üye mesajı: $limitMessage");
    }
    
    TestLogger::success('Mesaj testleri başarılı');
    
    // 4. JSON yanıt simülasyonu
    TestLogger::info('=== JSON YANIT SİMÜLASYONU ===');
    
    // Ziyaretçi sınır aşım yanıtı
    $visitorResponse = [
        'success' => false,
        'errors' => ["Günlük işlem sınırınız (5) dolmuştur. Üye olarak 20 işlem yapabilirsiniz."],
        'usage_info' => $usageModel->getUsageInfo($testVisitor, 'visitor')
    ];
    TestLogger::info('Ziyaretçi yanıtı: ' . json_encode($visitorResponse, JSON_UNESCAPED_UNICODE));
    
    // Üye sınır aşım yanıtı
    $memberResponse = [
        'success' => false,
        'errors' => ["Günlük işlem sınırınız (20) dolmuştur."],
        'usage_info' => $usageModel->getUsageInfo($testMember, 'member')
    ];
    TestLogger::info('Üye yanıtı: ' . json_encode($memberResponse, JSON_UNESCAPED_UNICODE));
    
    TestLogger::success('JSON yanıt simülasyonu başarılı');
    
    // Test verilerini temizle
    $cleanSQL = "DELETE FROM edefter_usage WHERE user_identifier IN (?, ?)";
    $stmt = $db->prepare($cleanSQL);
    $stmt->execute([$testVisitor, $testMember]);
    TestLogger::info('Test verileri temizlendi');
    
    TestLogger::success('Tüm testler başarıyla tamamlandı!');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

TestHelper::endTest();
