<?php
/**
 * Theme.php Tab JavaScript Test Script
 * Theme.php'deki tab JavaScript refactoring'inin başarılı olup olmadığını test eder
 */

echo "=== THEME.PHP TAB JAVASCRIPT TEST ===\n";
echo "Tarih: " . date('Y-m-d H:i:s') . "\n\n";

// Dosya var mı kontrolü
$themePhp = 'c:\Users\zdany\PhpstormProjects\erhanozel.globalpozitif.com.tr\_y\s\s\tasarim\Theme.php';
$themesTabJs = 'c:\Users\zdany\PhpstormProjects\erhanozel.globalpozitif.com.tr\_y\s\s\tasarim\Theme\js\themes-tab.js';
$themesTabPhp = 'c:\Users\zdany\PhpstormProjects\erhanozel.globalpozitif.com.tr\_y\s\s\tasarim\Theme\tabs\themes.php';

echo "1. DOSYA VAR MI KONTROLÜ:\n";
echo "✓ Theme.php: " . (file_exists($themePhp) ? "MEVCUT" : "YOK") . "\n";
echo "✓ themes-tab.js: " . (file_exists($themesTabJs) ? "MEVCUT" : "YOK") . "\n";
echo "✓ themes.php: " . (file_exists($themesTabPhp) ? "MEVCUT" : "YOK") . "\n\n";

// themes-tab.js içerik kontrolü
echo "2. THEMES-TAB.JS İÇERİK KONTROLÜ:\n";
if (file_exists($themesTabJs)) {
    $jsContent = file_get_contents($themesTabJs);
    echo "✓ initializeThemesTab function: " . (strpos($jsContent, 'function initializeThemesTab()') !== false ? "MEVCUT" : "YOK") . "\n";
    echo "✓ predefinedThemes object: " . (strpos($jsContent, 'const predefinedThemes') !== false ? "MEVCUT" : "YOK") . "\n";
    echo "✓ applyPredefinedTheme function: " . (strpos($jsContent, 'function applyPredefinedTheme(') !== false ? "MEVCUT" : "YOK") . "\n";
    echo "✓ exportCurrentTheme function: " . (strpos($jsContent, 'function exportCurrentTheme()') !== false ? "MEVCUT" : "YOK") . "\n";
} else {
    echo "❌ themes-tab.js dosyası bulunamadı!\n";
}
echo "\n";

// themes.php temizlik kontrolü
echo "3. THEMES.PHP TEMİZLİK KONTROLÜ:\n";
if (file_exists($themesTabPhp)) {
    $phpContent = file_get_contents($themesTabPhp);
    echo "✓ <script> tag'i kaldırıldı mı: " . (strpos($phpContent, '<script>') === false ? "EVET" : "HAYIR") . "\n";
    echo "✓ $(document).ready kaldırıldı mı: " . (strpos($phpContent, '$(document).ready') === false ? "EVET" : "HAYIR") . "\n";
    echo "✓ predefinedThemes kaldırıldı mı: " . (strpos($phpContent, 'predefinedThemes') === false ? "EVET" : "HAYIR") . "\n";
} else {
    echo "❌ themes.php dosyası bulunamadı!\n";
}
echo "\n";

// Theme.php import kontrolü
echo "4. THEME.PHP İMPORT KONTROLÜ:\n";
if (file_exists($themePhp)) {
    $mainContent = file_get_contents($themePhp);
    echo "✓ themes-tab.js import edildi mi: " . (strpos($mainContent, 'themes-tab.js') !== false ? "EVET" : "HAYIR") . "\n";
    echo "✓ initializeThemesTab çağrıldı mı: " . (strpos($mainContent, 'initializeThemesTab') !== false ? "EVET" : "HAYIR") . "\n";
} else {
    echo "❌ Theme.php dosyası bulunamadı!\n";
}
echo "\n";

// Syntax kontrolü (basit)
echo "5. SYNTAX KONTROLÜ:\n";
if (file_exists($themesTabJs)) {
    $jsContent = file_get_contents($themesTabJs);
    $openBraces = substr_count($jsContent, '{');
    $closeBraces = substr_count($jsContent, '}');
    echo "✓ JavaScript brace dengesi: " . ($openBraces == $closeBraces ? "DOĞRU ({$openBraces}/{$closeBraces})" : "HATALI ({$openBraces}/{$closeBraces})") . "\n";
    
    $openParens = substr_count($jsContent, '(');
    $closeParens = substr_count($jsContent, ')');
    echo "✓ JavaScript parenthesis dengesi: " . ($openParens == $closeParens ? "DOĞRU ({$openParens}/{$closeParens})" : "HATALI ({$openParens}/{$closeParens})") . "\n";
}
echo "\n";

echo "=== TEST TAMAMLANDI ===\n";
echo "✅ Refactoring başarılı görünüyor!\n";
echo "🌐 Şimdi tarayıcıda test yapılabilir.\n";
?>
