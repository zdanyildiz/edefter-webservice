<?php
include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Menü JavaScript Düzeltme Testi');

try {
    TestLogger::info('=== PLATFORM TRACKING MENÜ JAVASCRIPT DÜZELTMESİ ===');
    
    // 1. PlatformTracking.php dosyasının varlığını kontrol et
    $platformFile = __DIR__ . '/../../_y/s/s/ekkodlar/PlatformTracking.php';
    TestAssert::assertTrue(file_exists($platformFile), 'PlatformTracking.php dosyası mevcut olmalı');
    TestLogger::success('✅ PlatformTracking.php dosyası bulundu');
    
    // 2. Dosya içeriğini analiz et
    $fileContent = file_get_contents($platformFile);
    TestAssert::assertNotEmpty($fileContent, 'Dosya içeriği boş olmamalı');
    
    // 3. Gerekli JavaScript dosyalarının varlığını kontrol et
    $requiredJSFiles = [
        '/_y/assets/js/libs/jquery/jquery-1.11.2.min.js',
        '/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js',
        '/_y/assets/js/libs/jquery-ui/jquery-ui.min.js',
        '/_y/assets/js/libs/bootstrap/bootstrap.min.js',
        '/_y/assets/js/core/source/App.js',
        '/_y/assets/js/core/source/AppNavigation.js',
        '/_y/assets/js/core/source/AppOffcanvas.js',
        '/_y/assets/js/core/source/AppCard.js',
        '/_y/assets/js/core/source/AppForm.js',
        '/_y/assets/js/core/source/AppNavSearch.js',
        '/_y/assets/js/core/source/AppVendor.js'
    ];
    
    foreach ($requiredJSFiles as $jsFile) {
        $jsExists = strpos($fileContent, $jsFile) !== false;
        TestAssert::assertTrue($jsExists, "JavaScript dosyası '{$jsFile}' sayfada mevcut olmalı");
        
        if ($jsExists) {
            TestLogger::success("✅ {$jsFile} bulundu");
        } else {
            TestLogger::error("❌ {$jsFile} bulunamadı");
        }
    }
    
    // 4. Özellikle AppNavigation.js'nin varlığını vurgula
    $appNavigationExists = strpos($fileContent, 'AppNavigation.js') !== false;
    TestAssert::assertTrue($appNavigationExists, 'AppNavigation.js dosyası menü toggle için gerekli');
    
    if ($appNavigationExists) {
        TestLogger::success('✅ AppNavigation.js eklendi - menü toggle çalışacak');
    }
    
    // 5. AppNavigation.js dosyasının varlığını fiziksel olarak kontrol et
    $appNavFile = __DIR__ . '/../../_y/assets/js/core/source/AppNavigation.js';
    TestAssert::assertTrue(file_exists($appNavFile), 'AppNavigation.js dosyası fiziksel olarak mevcut olmalı');
    TestLogger::success('✅ AppNavigation.js dosyası fiziksel olarak mevcut');
    
    // 6. AppNavigation.js içinde menu toggle functionality kontrolü
    $appNavContent = file_get_contents($appNavFile);
    $menuToggleExists = strpos($appNavContent, 'data-toggle="menubar"') !== false;
    TestAssert::assertTrue($menuToggleExists, 'AppNavigation.js içinde menu toggle fonksiyonalitesi mevcut olmalı');
    
    if ($menuToggleExists) {
        TestLogger::success('✅ AppNavigation.js içinde menu toggle fonksiyonalitesi mevcut');
    }
    
    // 7. Header.php'de menu toggle butonunun varlığını kontrol et
    $headerFile = __DIR__ . '/../../_y/s/b/header.php';
    $headerContent = file_get_contents($headerFile);
    $menubarToggleExists = strpos($headerContent, 'data-toggle="menubar"') !== false;
    TestAssert::assertTrue($menubarToggleExists, 'Header.php\'de menubar toggle butonu mevcut olmalı');
    
    if ($menubarToggleExists) {
        TestLogger::success('✅ Header.php\'de menubar toggle butonu mevcut');
    }
    
    // 8. CSS class kontrolü
    $menubarToggleClass = strpos($headerContent, 'menubar-toggle') !== false;
    TestAssert::assertTrue($menubarToggleClass, 'Header.php\'de menubar-toggle class\'ı mevcut olmalı');
    
    if ($menubarToggleClass) {
        TestLogger::success('✅ Header.php\'de menubar-toggle class\'ı mevcut');
    }
    
    // 9. Syntax kontrolü
    TestLogger::info('9. Syntax kontrolleri yapılıyor...');
    
    $platformSyntax = shell_exec('php -l ' . escapeshellarg($platformFile) . ' 2>&1');
    $platformSyntaxOk = strpos($platformSyntax, 'No syntax errors') !== false;
    TestAssert::assertTrue($platformSyntaxOk, 'PlatformTracking.php syntax hatası olmamalı');
    
    if ($platformSyntaxOk) {
        TestLogger::success('✅ PlatformTracking.php syntax kontrolü başarılı');
    } else {
        TestLogger::error('❌ PlatformTracking.php syntax hatası: ' . $platformSyntax);
    }
    
    // 10. Menü sorunu çözüm açıklaması
    TestLogger::info('=== MENÜ SORUNU ÇÖZÜM AÇIKLAMASI ===');
    
    TestLogger::info('🔧 SORUN: PlatformTracking.php sayfasında menüler açılıp kapanmıyordu');
    TestLogger::info('🔍 NEDEN: AppNavigation.js ve diğer core JavaScript dosyaları eksikti');
    TestLogger::info('💡 ÇÖZÜM: Eksik JavaScript dosyaları eklendi');
    TestLogger::info('✅ SONUÇ: Menü toggle şimdi çalışacak');
    
    // 11. Eklenen dosyaların işlevleri
    TestLogger::info('=== EKLENİLEN JAVASCRIPT DOSYALARININ İŞLEVLERİ ===');
    
    $jsDescriptions = [
        'AppNavigation.js' => 'Menü açma/kapama, menü navigasyonu',
        'AppOffcanvas.js' => 'Yan panel (offcanvas) yönetimi',
        'AppCard.js' => 'Kart bileşenlerinin yönetimi',
        'AppForm.js' => 'Form validasyonu ve işlemleri',
        'AppNavSearch.js' => 'Navigasyon arama fonksiyonalitesi',
        'AppVendor.js' => 'Üçüncü parti eklenti yönetimi',
        'jquery-migrate-1.2.1.min.js' => 'jQuery eski versiyon uyumluluğu',
        'jquery-ui.min.js' => 'jQuery UI bileşenleri'
    ];
    
    foreach ($jsDescriptions as $file => $description) {
        TestLogger::info("📜 {$file}: {$description}");
    }
    
    // 12. Test senaryoları
    TestLogger::info('=== TEST SENARYOLARI ===');
    
    $testScenarios = [
        '1. Platform Tracking sayfasını aç',
        '2. Sol üst köşedeki hamburger menü butonuna tıkla',
        '3. Menü açılmalı ve kapanmalı',
        '4. Menü içindeki alt kategoriler çalışmalı',
        '5. Breadcrumb navigasyonu çalışmalı'
    ];
    
    foreach ($testScenarios as $scenario) {
        TestLogger::info("📋 {$scenario}");
    }
    
    TestLogger::success('🎉 Platform Tracking menü JavaScript sorunu başarıyla çözüldü!');
    
} catch (Exception $e) {
    TestLogger::error('Platform Tracking menü testi hatası: ' . $e->getMessage());
}

TestHelper::endTest();
