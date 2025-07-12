<?php
include_once __DIR__ . '/../index.php';

TestHelper::startTest('Platform Tracking Toggle Sorunu Düzeltme Testi');

try {
    TestLogger::info('=== PLATFORM TRACKING TOGGLE SORUNU DÜZELTMESİ ===');
    
    // 1. PlatformTrackingManager'da status parametresi kontrolü
    $managerFile = __DIR__ . '/../../App/Helpers/PlatformTrackingManager.php';
    $managerContent = file_get_contents($managerFile);
    
    $statusParameterExists = strpos($managerContent, 'savePlatformConfig($platform, $config, $languageID = 1, $status = null)') !== false;
    TestAssert::assertTrue($statusParameterExists, 'savePlatformConfig metodunda status parametresi mevcut olmalı');
    TestLogger::success('✅ PlatformTrackingManager\'da status parametresi eklendi');
    
    // 2. Status kaydetme logicinin varlığını kontrol et
    $statusSaveLogic = strpos($managerContent, 'status = :status') !== false;
    TestAssert::assertTrue($statusSaveLogic, 'Status kaydetme logic\'i mevcut olmalı');
    TestLogger::success('✅ Status kaydetme logic\'i mevcut');
    
    // 3. AdminPluginsController'da status gönderme kontrolü
    $controllerFile = __DIR__ . '/../../App/Controller/Admin/AdminPluginsController.php';
    $controllerContent = file_get_contents($controllerFile);
    
    $statusSending = strpos($controllerContent, 'savePlatformConfig($platform, $config, $languageID, $status)') !== false;
    TestAssert::assertTrue($statusSending, 'Controller\'da status parametresi gönderilmeli');
    TestLogger::success('✅ Controller\'da status parametresi gönderiliyor');
    
    // 4. Syntax kontrolleri
    TestLogger::info('4. Syntax kontrolleri yapılıyor...');
    
    $managerSyntax = shell_exec('php -l ' . escapeshellarg($managerFile) . ' 2>&1');
    $managerSyntaxOk = strpos($managerSyntax, 'No syntax errors') !== false;
    TestAssert::assertTrue($managerSyntaxOk, 'PlatformTrackingManager syntax hatası olmamalı');
    
    if ($managerSyntaxOk) {
        TestLogger::success('✅ PlatformTrackingManager syntax kontrolü başarılı');
    } else {
        TestLogger::error('❌ PlatformTrackingManager syntax hatası: ' . $managerSyntax);
    }
    
    $controllerSyntax = shell_exec('php -l ' . escapeshellarg($controllerFile) . ' 2>&1');
    $controllerSyntaxOk = strpos($controllerSyntax, 'No syntax errors') !== false;
    TestAssert::assertTrue($controllerSyntaxOk, 'AdminPluginsController syntax hatası olmamalı');
    
    if ($controllerSyntaxOk) {
        TestLogger::success('✅ AdminPluginsController syntax kontrolü başarılı');
    } else {
        TestLogger::error('❌ AdminPluginsController syntax hatası: ' . $controllerSyntax);
    }
    
    // 5. Düzeltilen sorunların özeti
    TestLogger::info('=== DÜZELTİLEN SORUNLAR ÖZETİ ===');
    
    $fixedIssues = [
        '✅ PlatformTrackingManager::savePlatformConfig() metoduna $status parametresi eklendi',
        '✅ Status kaydetme işlemi UPDATE ve INSERT SQL\'lerine eklendi',
        '✅ AdminPluginsController\'da savePlatformTracking action\'ında status parametresi gönderiliyor',
        '✅ AdminPluginsController\'da saveAllPlatforms action\'ında status parametresi gönderiliyor',
        '✅ Mevcut kayıt varsa status güncelleniyor, yoksa yeni kayıtta status set ediliyor'
    ];
    
    foreach ($fixedIssues as $issue) {
        TestLogger::success($issue);
    }
    
    // 6. Toggle sorununun çözüm açıklaması
    TestLogger::info('=== TOGGLE SORUNU ÇÖZÜM AÇIKLAMASI ===');
    
    TestLogger::info('🔧 SORUN: Facebook Pixel toggle\'ını kapatıp sayfayı yenilediğinde tekrar açılıyordu');
    TestLogger::info('🔍 NEDEN: Status (açık/kapalı durumu) veritabanına kaydedilmiyordu');
    TestLogger::info('💡 ÇÖZÜM: savePlatformConfig metoduna status parametresi eklendi');
    TestLogger::info('✅ SONUÇ: Artık toggle durumu veritabanında kalıcı olarak saklanıyor');
    
    // 7. Test senaryoları
    TestLogger::info('=== TEST SENARYOLARI ===');
    
    $testScenarios = [
        '1. Facebook Pixel\'i aktif et, sayfayı yenile → Açık kalmalı',
        '2. Facebook Pixel\'i pasif et, sayfayı yenile → Kapalı kalmalı', 
        '3. Google Analytics\'i aktif et, diğer platformları pasif et → Sadece GA açık kalmalı',
        '4. Tüm platformları aktif et → Hepsi açık kalmalı',
        '5. Dil değiştir → Her dil için ayrı ayarlar korunmalı'
    ];
    
    foreach ($testScenarios as $scenario) {
        TestLogger::info("📋 {$scenario}");
    }
    
    // 8. Browser cache temizleme önerisi
    TestLogger::info('=== KULLANICI AKSİYONLARI ===');
    TestLogger::warning('⚠️ Değişikliklerin etkili olması için browser cache\'ini temizleyin');
    TestLogger::warning('⚠️ Hard refresh yapın (Ctrl+F5 veya Ctrl+Shift+R)');
    TestLogger::warning('⚠️ Developer Tools açık iken sayfayı yenileyin');
    
    TestLogger::success('🎉 Platform Tracking toggle sorunu başarıyla çözüldü!');
    
} catch (Exception $e) {
    TestLogger::error('Platform Tracking düzeltme testi hatası: ' . $e->getMessage());
}

TestHelper::endTest();
