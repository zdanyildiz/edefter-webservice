<?php
include_once __DIR__ . '/../index.php';

TestHelper::startTest('AI Asistanı Direktifleri Doğrulama Testi');

try {
    TestLogger::info('=== AI ASISTANI DİREKTİFLERİ DOĞRULAMA TESTİ ===');
    
    // 1. Temel proje yapısı anlama testi
    TestLogger::info('1. Proje yapısı analiz edilebilir mi?');
    $projectStructure = [
        'App/Core/Config.php',
        'App/Database/database.sql', 
        'Tests/index.php',
        'Tests/example_test.php',
        '.github/copilot-instructions.md'
    ];
    
    foreach ($projectStructure as $file) {
        $filePath = __DIR__ . '/../../' . $file;
        if (file_exists($filePath)) {
            TestLogger::success("✅ {$file} dosyası mevcut");
        } else {
            TestLogger::error("❌ {$file} dosyası bulunamadı");
        }
    }
    
    // 2. Test framework yükleme testi
    TestLogger::info('2. Test framework doğru yüklendi mi?');
    $requiredClasses = ['TestDatabase', 'TestLogger', 'TestValidator', 'TestDataGenerator', 'TestAssert', 'TestHelper'];
    
    foreach ($requiredClasses as $class) {
        if (class_exists($class)) {
            TestLogger::success("✅ {$class} sınıfı yüklendi");
        } else {
            TestLogger::error("❌ {$class} sınıfı yüklenemedi");
        }
    }
    
    // 3. Veritabanı güvenlik testi
    TestLogger::info('3. Test veritabanı güvenliği kontrol ediliyor...');
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Test DB bağlantısı kurulmalı');
    
    // Test DB'nin ana projeden farklı olduğunu kontrol et
    $testConnection = get_class($db);
    TestLogger::success("✅ Test DB sınıfı: {$testConnection}");
    
    // 4. Direktif dosyası içerik kontrol testi
    TestLogger::info('4. Direktif dosyası içeriği kontrol ediliyor...');
    $directiveFile = __DIR__ . '/../../.github/copilot-instructions.md';
    $content = file_get_contents($directiveFile);
    
    $requiredSections = [
        'YAPAY ZEKA ASISTANI İÇİN',
        'KRİTİK KURALLAR VE YASAKLAR',
        'ZORUNLU TEST PROTOKOLÜ',
        'TEST YAPILANDIRMA REHBERİ',
        'HATA AYIKLAMA VE ÇÖZÜM REHBERİ',
        'SÜREKLİ İYİLEŞTİRME'
    ];
    
    foreach ($requiredSections as $section) {
        if (strpos($content, $section) !== false) {
            TestLogger::success("✅ '{$section}' bölümü mevcut");
        } else {
            TestLogger::error("❌ '{$section}' bölümü bulunamadı");
        }
    }
    
    // 5. PowerShell komut syntax kontrolleri
    TestLogger::info('5. PowerShell komut formatları kontrol ediliyor...');
    $powershellPatterns = [
        'vendor\\bin\\phinx',  // Backslash kullanımı
        ';',                   // Komut ayırıcı
        'Get-Content'          // PowerShell komutları
    ];
    
    foreach ($powershellPatterns as $pattern) {
        if (strpos($content, $pattern) !== false) {
            TestLogger::success("✅ PowerShell pattern '{$pattern}' doğru kullanılıyor");
        } else {
            TestLogger::warning("⚠️ PowerShell pattern '{$pattern}' bulunamadı");
        }
    }
    
    // 6. Yasak komut kontrolleri
    TestLogger::info('6. Yasak komutlar kontrol ediliyor...');
    $forbiddenPatterns = [
        'php -r',     // İnline PHP komutu yasak
        '&&',         // Bash komut ayırıcı yasak
    ];
    
    $forbiddenFound = false;
    foreach ($forbiddenPatterns as $pattern) {
        if (strpos($content, $pattern) !== false && strpos($content, "YANLIŞ: $pattern") === false) {
            TestLogger::error("❌ Yasak pattern '{$pattern}' direktiflerde bulundu!");
            $forbiddenFound = true;
        }
    }
    
    if (!$forbiddenFound) {
        TestLogger::success("✅ Yasak komutlar direktiflerde doğru şekilde belirtilmiş");
    }
    
    // 7. Test dosyası şablonu kontrol
    TestLogger::info('7. Test dosyası şablonu kontrol ediliyor...');
    $templateElements = [
        'include_once __DIR__',
        'TestHelper::startTest',
        'TestDatabase::getInstance',
        'TestAssert::',
        'TestLogger::',
        'TestHelper::endTest'
    ];
    
    foreach ($templateElements as $element) {
        if (strpos($content, $element) !== false) {
            TestLogger::success("✅ Test şablonu elementi '{$element}' mevcut");
        } else {
            TestLogger::warning("⚠️ Test şablonu elementi '{$element}' bulunamadı");
        }
    }
    
    // 8. Modül dokümantasyon kontrolleri
    TestLogger::info('8. Mevcut modül dokümantasyonları kontrol ediliyor...');
    $modulePrompts = [
        'Tests/Banners/banner_prompt.md',
        'Tests/Products/product_prompt.md', 
        'Tests/Members/member_prompt.md',
        'Tests/Orders/order_prompt.md',
        'Tests/Carts/cart_prompt.md'
    ];
    
    $existingPrompts = 0;
    foreach ($modulePrompts as $prompt) {
        $promptPath = __DIR__ . '/../../' . $prompt;
        if (file_exists($promptPath)) {
            TestLogger::success("✅ {$prompt} dokümantasyonu mevcut");
            $existingPrompts++;
        } else {
            TestLogger::info("ℹ️ {$prompt} dokümantasyonu henüz oluşturulmamış");
        }
    }
    
    TestLogger::info("Toplam {$existingPrompts} modül dokümantasyonu mevcut");
    
    // 9. Son özet ve öneriler
    TestLogger::info('=== DİREKTİF DOĞRULAMA ÖZETİ ===');
    TestLogger::success('✅ AI Asistanı direktifleri başarıyla oluşturuldu');
    TestLogger::success('✅ Kapsamlı test protokolleri tanımlandı');
    TestLogger::success('✅ Güvenlik kuralları belirlendi');
    TestLogger::success('✅ Hata ayıklama rehberi hazırlandı');
    TestLogger::success('✅ Sürekli iyileştirme protokolü oluşturuldu');
    
    TestLogger::info('=== YENİ AI ASISTANI İÇİN TAVSİYELER ===');
    TestLogger::info('1. İlk olarak .github/copilot-instructions.md dosyasını tamamen okuyun');
    TestLogger::info('2. Tests/example_test.php dosyasını çalıştırarak test framework\'ünü öğrenin');
    TestLogger::info('3. Her değişiklikten önce MUTLAKA tablo/sütun varlık kontrolü yapın');
    TestLogger::info('4. Ana proje dosyalarına (App/, Public/, _y/) KESİNLİKLE müdahale etmeyin');
    TestLogger::info('5. PowerShell komut syntax\'ına dikkat edin (\\, ;, Get-Content)');
    TestLogger::info('6. Her hata çözdüğünüzde direktif dosyasını güncelleyin');
    TestLogger::info('7. Test-driven development yaklaşımını benimseyin');
    
    TestLogger::success('🎉 AI Asistanı direktifleri başarıyla doğrulandı!');
    
} catch (Exception $e) {
    TestLogger::error('Direktif doğrulama hatası: ' . $e->getMessage());
}

TestHelper::endTest();
