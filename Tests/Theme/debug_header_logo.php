<?php
/**
 * Header Logo Debug Test
 * Bu test header logo değerlerinin yüklenmesini kontrol eder
 */

// Debug için theme sistemi yükleme
$rootPath = realpath(__DIR__ . '/../..');
echo "Root Path: " . $rootPath . "\n";

// Config ve Helper yükleme
require_once $rootPath . '/App/Core/Config.php';
require_once $rootPath . '/App/Helpers/Helper.php';

// CSS dosyasını oku
$cssFile = $rootPath . '/Public/Json/CSS/index.json';
if (file_exists($cssFile)) {
    $customCSS = json_decode(file_get_contents($cssFile), true);
    
    echo "\n=== JSON DEĞERLER ===\n";
    echo "header-logo-width: " . ($customCSS['header-logo-width'] ?? 'YOK') . "\n";
    echo "header-logo-mobile-width: " . ($customCSS['header-logo-mobile-width'] ?? 'YOK') . "\n";
    
    // sanitizeNumericValue fonksiyonu var mı kontrol et
    echo "\n=== FUNCTION TEST ===\n";
    if (function_exists('sanitizeNumericValue')) {
        echo "sanitizeNumericValue fonksiyonu mevcut\n";
        echo "header-logo-width test: " . sanitizeNumericValue($customCSS['header-logo-width'], 'px') . "\n";
        echo "header-logo-mobile-width test: " . sanitizeNumericValue($customCSS['header-logo-mobile-width'], 'px') . "\n";
    } else {
        echo "sanitizeNumericValue fonksiyonu BULUNAMADI!\n";
        echo "Manuel test:\n";
        echo "header-logo-width: " . ($customCSS['header-logo-width'] ?? 'UNDEFINED') . "\n";
        echo "header-logo-mobile-width: " . ($customCSS['header-logo-mobile-width'] ?? 'UNDEFINED') . "\n";
    }
    
    echo "\n=== JSON ANAHTAR LİSTESİ ===\n";
    $logoKeys = array_filter(array_keys($customCSS), function($key) {
        return strpos($key, 'header-logo') === 0;
    });
    foreach ($logoKeys as $key) {
        echo "✓ " . $key . " = " . $customCSS[$key] . "\n";
    }
    
} else {
    echo "CSS dosyası bulunamadı: " . $cssFile . "\n";
}
