<?php
include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Sayfa Analizi ve Toggle Sorunu');

try {
    TestLogger::info('=== PLATFORM TRACKING SAYFA ANALİZİ ===');
    
    // 1. Platform Tracking sayfasının varlığını kontrol et
    $trackingPage = __DIR__ . '/../../_y/s/s/ekkodlar/PlatformTracking.php';
    TestAssert::assertTrue(file_exists($trackingPage), 'PlatformTracking.php sayfası mevcut olmalı');
    TestLogger::success('✅ PlatformTracking.php sayfası bulundu');
    
    // 2. Sayfa içeriğini analiz et
    $pageContent = file_get_contents($trackingPage);
    TestAssert::assertNotEmpty($pageContent, 'Sayfa içeriği boş olmamalı');
    
    // 3. JavaScript toggle mantığını kontrol et
    $toggleLogic = strpos($pageContent, '.platform-toggle') !== false;
    TestAssert::assertTrue($toggleLogic, 'Platform toggle logic mevcut olmalı');
    TestLogger::success('✅ Platform toggle JavaScript kodu mevcut');
    
    // 4. AJAX kaydetme fonksiyonlarının varlığını kontrol et
    $saveFunctions = [
        'savePlatformTracking',
        'saveAllPlatforms',
        'previewPlatformTracking'
    ];
    
    foreach ($saveFunctions as $func) {
        $funcExists = strpos($pageContent, $func) !== false;
        TestAssert::assertTrue($funcExists, "{$func} fonksiyonu mevcut olmalı");
        TestLogger::success("✅ {$func} fonksiyonu bulundu");
    }
    
    // 5. Toggle sorunu analizi
    TestLogger::info('=== TOGGLE SORUNU ANALİZİ ===');
    
    // Toggle event handler kontrolü
    $toggleHandler = strpos($pageContent, '$(".platform-toggle").change') !== false;
    if ($toggleHandler) {
        TestLogger::success('✅ Toggle change event handler mevcut');
    } else {
        TestLogger::warning('⚠️ Toggle change event handler eksik olabilir');
    }
    
    // Platform status kaydetme kontrolü
    $statusSaving = strpos($pageContent, 'status: status') !== false;
    if ($statusSaving) {
        TestLogger::success('✅ Platform status kaydetme kodu mevcut');
    } else {
        TestLogger::error('❌ Platform status kaydetme kodu eksik');
    }
    
    // 6. Sayfa yenileme sorunu analizi
    TestLogger::info('6. Sayfa yenileme sorunu analiz ediliyor...');
    
    // Backend'den veri yükleme kontrolü
    $dataLoading = strpos($pageContent, '$activePlatforms[$platformKey]') !== false;
    if ($dataLoading) {
        TestLogger::success('✅ Backend\'den platform verisi yükleme kodu mevcut');
    }
    
    // Platform status database'den gelme kontrolü
    $statusFromDB = strpos($pageContent, '$platformConfig[\'status\']') !== false;
    if ($statusFromDB) {
        TestLogger::success('✅ Platform status database\'den yükleniyor');
    }
    
    // 7. Olası sorun noktaları
    TestLogger::info('=== OLASI SORUN NOKTALARI ===');
    
    $problemPoints = [
        'JavaScript sayfa yenileme sonrası toggle state\'i kaybolabilir',
        'AJAX kaydetme başarısız olduğunda client-side değişiklikler kalıcı olmaz',
        'Database transaction problemi olabilir',
        'Cache sorunu olabilir - browser veya server-side',
        'JavaScript event binding sayfa yenileme sonrası eksik olabilir'
    ];
    
    foreach ($problemPoints as $point) {
        TestLogger::warning("⚠️ {$point}");
    }
    
    // 8. AdminPluginsController kontrolü
    TestLogger::info('8. AdminPluginsController.php kontrol ediliyor...');
    $controllerFile = __DIR__ . '/../../App/Controller/Admin/AdminPluginsController.php';
    
    if (file_exists($controllerFile)) {
        TestLogger::success('✅ AdminPluginsController.php mevcut');
        
        $controllerContent = file_get_contents($controllerFile);
        
        $controllerActions = [
            'savePlatformTracking',
            'saveAllPlatforms', 
            'previewPlatformTracking'
        ];
        
        foreach ($controllerActions as $action) {
            $actionExists = strpos($controllerContent, $action) !== false;
            if ($actionExists) {
                TestLogger::success("✅ Controller action '{$action}' mevcut");
            } else {
                TestLogger::error("❌ Controller action '{$action}' eksik!");
            }
        }
    } else {
        TestLogger::error('❌ AdminPluginsController.php bulunamadı!');
    }
    
    // 9. Önerilen çözümler
    TestLogger::info('=== ÖNERİLEN ÇÖZÜMLER ===');
    
    $solutions = [
        '1. AJAX Response Check: Kaydetme işleminin gerçekten başarılı olduğunu kontrol et',
        '2. Database Logging: Platform status değişikliklerini logla',
        '3. Client-Side Persistence: Sayfa yenileme sonrası state\'i koruma mekanizması',
        '4. Error Handling: AJAX hatalarında kullanıcıya feedback ver',
        '5. Cache Busting: Browser cache\'ini temizleme mekanizması'
    ];
    
    foreach ($solutions as $solution) {
        TestLogger::info("💡 {$solution}");
    }
    
    TestLogger::success('🎯 Platform Tracking sayfa analizi tamamlandı!');
    
} catch (Exception $e) {
    TestLogger::error('Platform Tracking analiz hatası: ' . $e->getMessage());
}

TestHelper::endTest();
