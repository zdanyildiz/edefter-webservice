<?php
include_once __DIR__ . '/../index.php';

TestHelper::startTest('Config.php Çifte Tanımlama Düzeltme Testi');

try {
    TestLogger::info('=== CONFIG.PHP ÇİFTE TANIMLAMA DÜZELTMESİ TESTİ ===');
    
    // 1. Config.php dosyasının varlığını kontrol et
    $configFile = __DIR__ . '/../../App/Core/Config.php';
    TestAssert::assertTrue(file_exists($configFile), 'Config.php dosyası mevcut olmalı');
    TestLogger::success('✅ Config.php dosyası bulundu');
    
    // 2. Config.php içeriğini analiz et
    $configContent = file_get_contents($configFile);
    TestAssert::assertNotEmpty($configContent, 'Config.php içeriği boş olmamalı');
    
    // 3. loadHelpers() metodunun kaldırıldığını kontrol et
    $loadHelpersExists = strpos($configContent, 'private function loadHelpers()') !== false;
    TestAssert::assertFalse($loadHelpersExists, 'loadHelpers() metodu kaldırılmış olmalı');
    
    if (!$loadHelpersExists) {
        TestLogger::success('✅ loadHelpers() metodu başarıyla kaldırıldı');
    } else {
        TestLogger::error('❌ loadHelpers() metodu hala mevcut');
    }
    
    // 4. Constructor'da loadHelpers() çağrısının kaldırıldığını kontrol et
    $loadHelpersCall = strpos($configContent, '$this->loadHelpers()') !== false;
    TestAssert::assertFalse($loadHelpersCall, 'Constructor\'da loadHelpers() çağrısı kaldırılmış olmalı');
    
    if (!$loadHelpersCall) {
        TestLogger::success('✅ Constructor\'da loadHelpers() çağrısı kaldırıldı');
    } else {
        TestLogger::error('❌ Constructor\'da loadHelpers() çağrısı hala mevcut');
    }
    
    // 5. setFilesystemConstants içinde Helper tanımlamasının mevcut olduğunu kontrol et
    $helperDefinition = strpos($configContent, '$this->Helper = new Helper()') !== false;
    TestAssert::assertTrue($helperDefinition, 'Helper tanımlaması setFilesystemConstants\'ta mevcut olmalı');
    
    if ($helperDefinition) {
        TestLogger::success('✅ Helper tanımlaması setFilesystemConstants\'ta mevcut');
    }
    
    // 6. Json tanımlamasının JSON_DIR parametresi ile mevcut olduğunu kontrol et
    $jsonDefinition = strpos($configContent, '$this->Json = new Json(JSON_DIR)') !== false;
    TestAssert::assertTrue($jsonDefinition, 'Json tanımlaması JSON_DIR parametresi ile mevcut olmalı');
    
    if ($jsonDefinition) {
        TestLogger::success('✅ Json tanımlaması JSON_DIR parametresi ile mevcut');
    }
    
    // 7. Çifte tanımlama olmadığını kontrol et
    $helperIncludeCount = substr_count($configContent, "include_once Helpers.'Helper.php'");
    $helperIncludeAltCount = substr_count($configContent, "include_once ROOT . '/App/Helpers/Helper.php'");
    $totalHelperIncludes = $helperIncludeCount + $helperIncludeAltCount;
    
    TestAssert::assertEquals(1, $totalHelperIncludes, 'Helper.php sadece bir kez include edilmeli');
    TestLogger::success("✅ Helper.php include sayısı: {$totalHelperIncludes}");
    
    $jsonIncludeCount = substr_count($configContent, "include_once CORE.'Json.php'");
    $jsonIncludeAltCount = substr_count($configContent, "include_once ROOT . '/App/Core/Json.php'");
    $totalJsonIncludes = $jsonIncludeCount + $jsonIncludeAltCount;
    
    TestAssert::assertEquals(1, $totalJsonIncludes, 'Json.php sadece bir kez include edilmeli');
    TestLogger::success("✅ Json.php include sayısı: {$totalJsonIncludes}");
    
    // 8. Config sınıfının yüklenebilirliğini test et (syntax kontrol)
    $syntaxCheck = shell_exec('php -l ' . escapeshellarg($configFile) . ' 2>&1');
    $syntaxOk = strpos($syntaxCheck, 'No syntax errors') !== false;
    TestAssert::assertTrue($syntaxOk, 'Config.php syntax hatası olmamalı');
    
    if ($syntaxOk) {
        TestLogger::success('✅ Config.php syntax kontrolü başarılı');
    } else {
        TestLogger::error('❌ Config.php syntax hatası: ' . $syntaxCheck);
    }
    
    // 9. Method yapısını kontrol et
    TestLogger::info('9. Method yapısını kontrol ediliyor...');
    
    $methods = [
        'setFilesystemConstants' => true,  // Mevcut olmalı
        'getHeadTrackingInjector' => true, // Mevcut olmalı  
        'includeClass' => true,            // Mevcut olmalı
        'loadHelpers' => false             // Mevcut OLMAMALI
    ];
    
    foreach ($methods as $method => $shouldExist) {
        $methodExists = strpos($configContent, "function {$method}(") !== false;
        
        if ($shouldExist) {
            TestAssert::assertTrue($methodExists, "{$method} metodu mevcut olmalı");
            TestLogger::success("✅ {$method} metodu mevcut");
        } else {
            TestAssert::assertFalse($methodExists, "{$method} metodu mevcut olmamalı");
            TestLogger::success("✅ {$method} metodu başarıyla kaldırıldı");
        }
    }
    
    // 10. Özet rapor
    TestLogger::info('=== DÜZELTİLEN SORUNLAR ÖZETİ ===');
    TestLogger::success('✅ Çifte Helper include sorunu çözüldü');
    TestLogger::success('✅ Çifte Json include sorunu çözüldü');
    TestLogger::success('✅ loadHelpers() metodu kaldırıldı');
    TestLogger::success('✅ Constructor\'da gereksiz çağrı kaldırıldı');
    TestLogger::success('✅ setFilesystemConstants tek merkezi nokta oldu');
    TestLogger::success('✅ Json sınıfı JSON_DIR parametresi ile doğru tanımlandı');
    
    TestLogger::success('🎉 Config.php çifte tanımlama sorunu başarıyla çözüldü!');
    
} catch (Exception $e) {
    TestLogger::error('Config.php test hatası: ' . $e->getMessage());
}

TestHelper::endTest();
