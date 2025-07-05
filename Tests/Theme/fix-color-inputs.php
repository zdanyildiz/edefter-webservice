#!/usr/bin/env php
<?php
/**
 * Renk Input Fallback Özelliği Ekleyici
 * Tüm color input'larına data-fallback özelliği ekler
 */

$themeTabsDir = __DIR__ . '/Theme/tabs/';
$tabFiles = glob($themeTabsDir . '*.php');

echo "🎨 Renk Input Fallback Ekleme Başlatıldı...\n";

foreach ($tabFiles as $tabFile) {
    $filename = basename($tabFile);
    echo "📁 İşleniyor: $filename\n";
    
    $content = file_get_contents($tabFile);
    $originalContent = $content;
    
    // Renk input'larını bul ve data-fallback ekle
    $pattern = '/(<input[^>]*type=["\']color["\'][^>]*value=["\']<?=sanitizeColorValue\(\$customCSS\[["\']([^"\']+)["\'][^)]*\)\?>[^>]*)(>)/i';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $fullInput = $matches[1];
        $colorVariable = $matches[2];
        $closeTag = $matches[3];
        
        // Zaten data-fallback varsa atlama
        if (strpos($fullInput, 'data-fallback') !== false) {
            return $matches[0];
        }
        
        // Default değeri çıkar
        preg_match('/\?\?\s*["\']([^"\']+)["\']/', $fullInput, $defaultMatches);
        $defaultColor = $defaultMatches[1] ?? '#ffffff';
        
        // data-fallback ekle
        return $fullInput . ' data-fallback="' . $defaultColor . '"' . $closeTag;
    }, $content);
    
    // Değişiklik varsa kaydet
    if ($content !== $originalContent) {
        file_put_contents($tabFile, $content);
        echo "   ✅ Güncellendi\n";
    } else {
        echo "   ℹ️  Değişiklik yok\n";
    }
}

echo "\n🎯 Tamamlandı! Tüm renk input'larında data-fallback kontrol edildi.\n";
?>
