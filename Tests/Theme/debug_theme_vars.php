<?php
/**
 * Gerçek Theme.php Ortamında Debug
 */

// Theme.php başındaki değişkenleri simüle et
$_GET['languageID'] = 1; // Test için
$languageID = $_GET['languageID'];
$languageID = intval($languageID);

echo "Language ID: " . $languageID . "\n";

// JSON_DIR constant'ını bul
$rootPath = realpath(__DIR__ . '/../..');
define('JSON_DIR', $rootPath . '/Public/Json/');

echo "JSON_DIR: " . JSON_DIR . "\n";

// Dosya kontrolleri
$files = [
    JSON_DIR . "CSS/custom-" . $languageID . ".json",
    JSON_DIR . "CSS/index-" . $languageID . ".json", 
    JSON_DIR . "CSS/index.json"
];

echo "\n=== DOSYA KONTROLLERI ===\n";
foreach ($files as $file) {
    $exists = file_exists($file) ? "✅ MEVCUT" : "❌ YOK";
    echo $exists . " - " . basename($file) . "\n";
}

// index.json'u yükle
$indexJsonFile = JSON_DIR . "CSS/index.json";
if (file_exists($indexJsonFile)) {
    $content = file_get_contents($indexJsonFile);
    $customCSS = json_decode($content, true);
    
    echo "\n=== JSON YÜKLEME TEST ===\n";
    echo "JSON decode error: " . json_last_error_msg() . "\n";
    echo "customCSS boş mu: " . (empty($customCSS) ? "EVET" : "HAYIR") . "\n";
    echo "Total keys: " . count($customCSS ?? []) . "\n";
    
    echo "\n=== LOGO DEĞERLERİ ===\n";
    echo "header-logo-width: " . ($customCSS['header-logo-width'] ?? 'YOK') . "\n";
    echo "header-logo-mobile-width: " . ($customCSS['header-logo-mobile-width'] ?? 'YOK') . "\n";
}
