#!/usr/bin/env php
<?php
/**
 * Menu Preview Test
 * Menü önizleme sisteminin test edilmesi
 */

require_once __DIR__ . '/../../App/Core/Config.php';

$config = new Config();

echo "🎨 MENU PREVIEW SYSTEM TEST\n";
echo "=====================================\n\n";

// Test 1: Tema dosyaları kontrol
echo "📋 Test 1: Tema dosyaları kontrol\n";
echo "-------------------------------------\n";

$requiredFiles = [
    '_y/s/s/tasarim/Theme.php',
    '_y/s/s/tasarim/Theme/tabs/menu.php',
    '_y/s/s/tasarim/Theme/js/menu.js',
    '_y/s/s/tasarim/Theme/js/core.js',
    '_y/s/s/tasarim/Theme/js/header.js'
];

foreach ($requiredFiles as $file) {
    $fullPath = ROOT . $file;
    if (file_exists($fullPath)) {
        echo "✅ " . $file . " - MEVCUT\n";
    } else {
        echo "❌ " . $file . " - EKSİK\n";
    }
}

echo "\n";

// Test 2: JavaScript fonksiyonları kontrol
echo "📋 Test 2: JavaScript fonksiyonları kontrol\n";
echo "-------------------------------------\n";

$jsFile = ROOT . '_y/s/s/tasarim/Theme/js/menu.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    $functions = [
        'updateMenuPreview',
        'updateMobileMenuPreview', 
        'initMenuPreviewToggle',
        'toggleMenuPreview',
        'openMenuDualPreview'
    ];
    
    foreach ($functions as $func) {
        if (strpos($jsContent, $func) !== false) {
            echo "✅ " . $func . " - MEVCUT\n";
        } else {
            echo "❌ " . $func . " - EKSİK\n";
        }
    }
} else {
    echo "❌ menu.js dosyası bulunamadı!\n";
}

echo "\n";

// Test 3: Theme.php içinde script yüklemesi kontrol
echo "📋 Test 3: Script yüklemesi kontrol\n";
echo "-------------------------------------\n";

$themeFile = ROOT . '_y/s/s/tasarim/Theme.php';
if (file_exists($themeFile)) {
    $themeContent = file_get_contents($themeFile);
    
    if (strpos($themeContent, 'menu.js') !== false) {
        echo "✅ menu.js script yüklemesi - MEVCUT\n";
    } else {
        echo "❌ menu.js script yüklemesi - EKSİK\n";
    }
    
    if (strpos($themeContent, 'id="menu-panel"') !== false) {
        echo "✅ Menu panel - MEVCUT\n";
    } else {
        echo "❌ Menu panel - EKSİK\n";
    }
} else {
    echo "❌ Theme.php dosyası bulunamadı!\n";
}

echo "\n";

// Test 4: Menu tab dosyası kontrol
echo "📋 Test 4: Menu tab içeriği kontrol\n";
echo "-------------------------------------\n";

$menuTabFile = ROOT . '_y/s/s/tasarim/Theme/tabs/menu.php';
if (file_exists($menuTabFile)) {
    $menuTabContent = file_get_contents($menuTabFile);
    
    $elements = [
        'toggleMenuPreview' => 'Desktop menu toggle butonu',
        'toggleMobileMenuPreview' => 'Mobile menu toggle butonu', 
        'mobileMenuPreview' => 'Mobile menu preview container',
        'menu-preview-container' => 'Menu preview container CSS',
        'mobile-menu-preview-container' => 'Mobile menu preview container CSS'
    ];
    
    foreach ($elements as $element => $description) {
        if (strpos($menuTabContent, $element) !== false) {
            echo "✅ " . $description . " - MEVCUT\n";
        } else {
            echo "❌ " . $description . " - EKSİK\n";
        }
    }
} else {
    echo "❌ menu.php tab dosyası bulunamadı!\n";
}

echo "\n";

// Test 5: Yerel domain kontrol
echo "📋 Test 5: Yerel erişim kontrol\n";
echo "-------------------------------------\n";

$localDomain = "l.erhanozel"; // GetLocalDomain'den aldık
$themeUrl = "http://" . $localDomain . "/_y/s/s/tasarim/Theme.php";

echo "🌐 Tema Sayfası URL: " . $themeUrl . "\n";
echo "📝 Tarayıcıda açıp menü sekmesini kontrol edin:\n";
echo "   1. Menu sekmesine tıklayın\n";
echo "   2. Desktop menu önizlemesinde 'Sabitlik' butonunu test edin\n";
echo "   3. Mobile menu önizlemesinde toggle butonlarını test edin\n";
echo "   4. Renk değişikliklerinin preview'lara yansıdığını kontrol edin\n";

echo "\n";

// Test 6: Log kontrolü
echo "📋 Test 6: Log önerileri\n";
echo "-------------------------------------\n";

echo "🔍 Hata ayıklama için:\n";
echo "   - Tarayıcı Console'unu açın (F12)\n";
echo "   - Network sekmesinde menu.js yüklenip yüklenmediğini kontrol edin\n";
echo "   - Console'da şu mesajları arayın:\n";
echo "     ✅ 'Menu.js yüklendi - Menü önizleme sistemleri hazır'\n";
echo "     ✅ 'initMenuPreviewToggle başlatıldı'\n";
echo "     🔄 'toggleMenuPreview çağrıldı: desktop|mobile'\n";

echo "\n=====================================\n";
echo "🎯 TEST TAMAMLANDI\n";
echo "=====================================\n";
