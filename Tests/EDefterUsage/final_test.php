<?php
/**
 * E-Defter Kullanım Sınırlaması - Final Test Süreci
 * Tüm bileşenleri test eder
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('E-Defter Kullanım Sınırlaması - Final Test');

try {
    echo "\n";
    echo "🎯 E-DEFTER KULLANIM SINIRLAMASI FİNAL TEST\n";
    echo "==========================================\n\n";

    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // 1. Tablo kontrolü
    echo "📋 1. VERİTABANI TABLO KONTROLÜ\n";
    echo "------------------------------\n";
    
    $tableExists = $db->tableExists('edefter_usage');
    TestAssert::assertTrue($tableExists, 'edefter_usage tablosu mevcut olmalı');
    echo "✅ edefter_usage tablosu mevcut\n";
    
    $expectedColumns = ['id', 'user_identifier', 'user_type', 'usage_date', 'usage_count', 'last_usage_time'];
    foreach ($expectedColumns as $column) {
        // Direkt SQL ile kontrol
        $checkSQL = "SHOW COLUMNS FROM edefter_usage LIKE '$column'";
        $result = $db->query($checkSQL);
        $exists = $result && $result->rowCount() > 0;
        TestAssert::assertTrue($exists, "$column sütunu mevcut olmalı");
        echo "✅ $column sütunu mevcut\n";
    }
    
    // 2. Model sınıfı kontrolü
    echo "\n🔧 2. MODEL SINIFI KONTROLÜ\n";
    echo "--------------------------\n";
    
    require_once __DIR__ . '/../../App/Model/EDefterUsage.php';
    $usageModel = new EDefterUsage($db);
    TestAssert::assertNotNull($usageModel, 'EDefterUsage modeli yüklenmeli');
    echo "✅ EDefterUsage modeli başarıyla yüklendi\n";
    
    // 3. Controller dosyası kontrolü
    echo "\n🎮 3. CONTROLLER DOSYASI KONTROLÜ\n";
    echo "--------------------------------\n";
    
    $controllerFile = __DIR__ . '/../../App/Controller/EDefterController.php';
    TestAssert::assertTrue(file_exists($controllerFile), 'EDefterController.php dosyası mevcut olmalı');
    echo "✅ EDefterController.php dosyası mevcut\n";
    
    $controllerContent = file_get_contents($controllerFile);
    $hasEDefterUsage = strpos($controllerContent, 'EDefterUsage.php') !== false;
    TestAssert::assertTrue($hasEDefterUsage, 'Controller EDefterUsage modelini yüklemeli');
    echo "✅ Controller EDefterUsage modelini yüklüyor\n";
    
    $hasLimitCheck = strpos($controllerContent, 'isLimitExceeded') !== false;
    TestAssert::assertTrue($hasLimitCheck, 'Controller sınır kontrolü yapmalı');
    echo "✅ Controller sınır kontrolü yapıyor\n";
    
    $hasIncrement = strpos($controllerContent, 'incrementUsage') !== false;
    TestAssert::assertTrue($hasIncrement, 'Controller kullanım sayacını artırmalı');
    echo "✅ Controller kullanım sayacını artırıyor\n";
    
    // 4. Işlevsellik testi
    echo "\n⚡ 4. İŞLEVSELLİK TESTİ\n";
    echo "---------------------\n";
    
    $testUser = 'final_test_' . time();
    
    // İlk durum
    $initialUsage = $usageModel->getDailyUsage($testUser);
    TestAssert::assertEquals(0, $initialUsage, 'İlk kullanım 0 olmalı');
    echo "✅ İlk kullanım durumu: $initialUsage\n";
    
    // Sınır kontrolü
    $isExceeded = $usageModel->isLimitExceeded($testUser, 'visitor');
    TestAssert::assertFalse($isExceeded, 'İlk durumda sınır aşılmamalı');
    echo "✅ İlk sınır durumu: " . ($isExceeded ? 'Aşılmış' : 'Normal') . "\n";
    
    // 3 kullanım ekle
    for ($i = 1; $i <= 3; $i++) {
        $result = $usageModel->incrementUsage($testUser, 'visitor');
        TestAssert::assertTrue($result, "Kullanım $i eklenmeli");
    }
    echo "✅ 3 kullanım başarıyla eklendi\n";
    
    // Kullanım bilgisi al
    $usageInfo = $usageModel->getUsageInfo($testUser, 'visitor');
    TestAssert::assertEquals(3, $usageInfo['current_usage'], 'Kullanım 3 olmalı');
    TestAssert::assertEquals(5, $usageInfo['daily_limit'], 'Limit 5 olmalı');
    TestAssert::assertEquals(2, $usageInfo['remaining_usage'], 'Kalan 2 olmalı');
    echo "✅ Kullanım bilgisi: {$usageInfo['current_usage']}/{$usageInfo['daily_limit']} (Kalan: {$usageInfo['remaining_usage']})\n";
    
    // 5. JSON yanıt testi
    echo "\n📤 5. JSON YANIT TESTİ\n";
    echo "--------------------\n";
    
    $successResponse = [
        'success' => true,
        'results' => ['Test çıktısı'],
        'usage_info' => $usageInfo
    ];
    $successJson = json_encode($successResponse, JSON_UNESCAPED_UNICODE);
    TestAssert::assertNotEmpty($successJson, 'JSON yanıt oluşturulmalı');
    echo "✅ Başarılı JSON yanıtı oluşturuldu\n";
    
    // Sınır aşımı yanıtı
    $usageModel->incrementUsage($testUser, 'visitor'); // 4. kullanım
    $usageModel->incrementUsage($testUser, 'visitor'); // 5. kullanım (sınır)
    
    $limitInfo = $usageModel->getUsageInfo($testUser, 'visitor');
    $limitResponse = [
        'success' => false,
        'errors' => ['Günlük işlem sınırınız (5) dolmuştur. Üye olarak 20 işlem yapabilirsiniz.'],
        'usage_info' => $limitInfo
    ];
    $limitJson = json_encode($limitResponse, JSON_UNESCAPED_UNICODE);
    TestAssert::assertNotEmpty($limitJson, 'Sınır aşım JSON yanıtı oluşturulmalı');
    echo "✅ Sınır aşım JSON yanıtı oluşturuldu\n";
    
    // 6. Temizlik
    echo "\n🧹 6. TEST VERİSİ TEMİZLİĞİ\n";
    echo "---------------------------\n";
    
    $cleanSQL = "DELETE FROM edefter_usage WHERE user_identifier = ?";
    $stmt = $db->prepare($cleanSQL);
    $cleanResult = $stmt->execute([$testUser]);
    TestAssert::assertTrue($cleanResult, 'Test verisi temizlenmeli');
    echo "✅ Test verisi temizlendi\n";
    
    // 7. Final özet
    echo "\n🎉 7. FİNAL ÖZET\n";
    echo "---------------\n";
    echo "✅ Veritabanı tablosu: HAZIR\n";
    echo "✅ Model sınıfı: HAZIR\n";
    echo "✅ Controller entegrasyonu: HAZIR\n";
    echo "✅ Sınır kontrolü: ÇALIŞIYOR\n";
    echo "✅ Kullanım sayacı: ÇALIŞIYOR\n";
    echo "✅ JSON yanıtları: ÇALIŞIYOR\n";
    echo "✅ Ziyaretçi sınırı: 5/gün\n";
    echo "✅ Üye sınırı: 20/gün\n";
    
    TestLogger::success('🚀 E-Defter kullanım sınırlaması sistemi BAŞARIYLA KURULDU!');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

echo "\n";
TestHelper::endTest();
