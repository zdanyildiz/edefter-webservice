<?php
// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// Test başlat
TestHelper::startTest('Index.json vs Theme.php Tutarlılık Kontrolü');

try {
    // JSON dosyasını oku
    $projectRoot = dirname(dirname(__DIR__));
    $jsonFile = $projectRoot . "/Public/Json/CSS/index.json";
    $content = file_get_contents($jsonFile);
    $indexJson = json_decode($content, true);
    
    // Theme.php dosyasını oku
    $themeFile = $projectRoot . "/_y/s/s/tasarim/Theme.php";
    $themeContent = file_get_contents($themeFile);
    
    TestLogger::info("JSON keys count: " . count($indexJson));
    
    // Index.json'daki anahtarları kontrol et
    $missingInTheme = [];
    $inconsistentValues = [];
    
    foreach ($indexJson as $key => $value) {
        // Theme.php'de bu anahtar var mı kontrol et
        if (strpos($themeContent, $key) === false) {
            $missingInTheme[] = $key;
        }
    }
    
    TestLogger::info("Theme.php'de eksik olan değişkenler: " . count($missingInTheme));
    
    // İlk 20 eksik değişkeni listele
    foreach (array_slice($missingInTheme, 0, 20) as $missing) {
        TestLogger::warning("Eksik: $missing = " . $indexJson[$missing]);
    }
    
    // Logo ile ilgili tutarsızlıkları özel olarak kontrol et
    $logoKeys = array_filter(array_keys($indexJson), function($key) {
        return strpos($key, 'logo') !== false || strpos($key, 'header') !== false;
    });
    
    TestLogger::info("Logo/Header ile ilgili anahtar sayısı: " . count($logoKeys));
    
    $logoInconsistencies = [];
    foreach ($logoKeys as $key) {
        if (strpos($themeContent, $key) === false) {
            $logoInconsistencies[] = $key;
        }
    }
    
    TestLogger::info("Logo/Header tutarsızlıkları: " . count($logoInconsistencies));
    foreach ($logoInconsistencies as $inconsistent) {
        TestLogger::error("Logo tutarsızlığı: $inconsistent = " . $indexJson[$inconsistent]);
    }
    
    TestLogger::success('Tutarlılık kontrolü tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

// Test sonlandır
TestHelper::endTest();
