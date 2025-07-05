<?php
/**
 * Fallback temizleme sonrası doğrulama testi
 * Tüm fallback değerlerin başarıyla kaldırıldığını kontrol eder
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Fallback temizleme doğrulama');

try {
    // 1. Theme.php kontrol
    $themeFile = 'c:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\_y\\s\\s\\tasarim\\Theme.php';
    $content = file_get_contents($themeFile);
    
    // Fallback pattern'leri ara
    $fallbackPatterns = [
        '/\?\?\s*\'[^\']*\'/',
        '/\?\?\s*"[^"]*"/',
        '/\?\?\s*\$[^;]*/',
    ];
    
    $totalFallbacks = 0;
    foreach ($fallbackPatterns as $pattern) {
        $matches = preg_match_all($pattern, $content);
        if ($matches) {
            $totalFallbacks += $matches;
        }
    }
    
    TestAssert::assertEquals(0, $totalFallbacks, 'Theme.php dosyasında fallback kalmamalı');
    TestLogger::success('Theme.php dosyası temiz - hiç fallback yok');
    
    // 2. Tab dosyalarını kontrol
    $tabsDir = 'c:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\_y\\s\\s\\tasarim\\Theme\\tabs\\';
    $tabFiles = glob($tabsDir . '*.php');
    
    $totalTabFallbacks = 0;
    foreach ($tabFiles as $tabFile) {
        $tabContent = file_get_contents($tabFile);
        
        $tabFallbacks = 0;
        foreach ($fallbackPatterns as $pattern) {
            $matches = preg_match_all($pattern, $tabContent);
            if ($matches) {
                $tabFallbacks += $matches;
            }
        }
        
        if ($tabFallbacks > 0) {
            TestLogger::error(basename($tabFile) . " dosyasında {$tabFallbacks} fallback bulundu");
        }
        
        $totalTabFallbacks += $tabFallbacks;
    }
    
    TestAssert::assertEquals(0, $totalTabFallbacks, 'Tab dosyalarında fallback kalmamalı');
    TestLogger::success('Tüm tab dosyaları temiz - hiç fallback yok');
    
    // 3. index.json kontrol
    $jsonFile = 'c:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\Public\\Json\\CSS\\index.json';
    $jsonContent = file_get_contents($jsonFile);
    $jsonData = json_decode($jsonContent, true);
    
    TestAssert::assertNotNull($jsonData, 'index.json geçerli JSON formatında olmalı');
    TestAssert::assertTrue(count($jsonData) > 50, 'index.json yeterli sayıda değişken içermeli');
    
    // Kritik değişkenlerin varlığını kontrol et
    $criticalVars = [
        'header-mobile-logo-margin-top',
        'header-mobile-logo-margin-right', 
        'header-mobile-logo-margin-bottom',
        'header-mobile-logo-margin-left',
        'top-contact-and-social-link-color',
        'top-contact-and-social-link-hover-color',
        'top-contact-and-social-icon-color',
        'top-contact-and-social-icon-hover-color'
    ];
    
    foreach ($criticalVars as $var) {
        TestAssert::assertTrue(
            isset($jsonData[$var]), 
            "Kritik değişken '{$var}' index.json'da mevcut olmalı"
        );
    }
    
    TestLogger::success('index.json dosyası kontrol edildi - tüm kritik değişkenler mevcut');
    
    // 4. Örnek kod kontrol
    $samplePHPCode = '<?=$customCSS[\'header-mobile-logo-margin-top\'] ?>';
    TestAssert::assertTrue(
        strpos($content, $samplePHPCode) !== false,
        'Theme.php dosyasında temiz PHP kodu bulunmalı'
    );
    
    TestLogger::success('Tüm fallback değerleri başarıyla kaldırıldı ve doğrulandı');
    
} catch (Exception $e) {
    TestLogger::error('Doğrulama hatası: ' . $e->getMessage());
}

TestHelper::endTest();
?>
