<?php
/**
 * Banner Tipleri ve CSS Yüklemelerini Test Et
 */
echo "Banner Tipleri ve CSS Test\n";
echo "===========================\n\n";

// Banner türlerini simüle et
$bannerTypes = [
    ['type_id' => 1, 'type_name' => 'Slider'],
    ['type_id' => 2, 'type_name' => 'Tepe Banner'],
    ['type_id' => 3, 'type_name' => 'Orta Banner'],
    ['type_id' => 4, 'type_name' => 'Alt Banner'],
    ['type_id' => 5, 'type_name' => 'Karşılama Banner (Popup)'],
    ['type_id' => 6, 'type_name' => 'Carousel']
];

// Helper fonksiyonlarını simüle et
function turkish_to_lower($text) {
    $tr_chars = ['İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç'];
    $en_chars = ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'];
    return str_replace($tr_chars, $en_chars, $text);
}

function trToEn($text) {
    $tr_chars = ['İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç'];
    $en_chars = ['I', 'G', 'U', 'S', 'O', 'C', 'i', 'g', 'u', 's', 'o', 'c'];
    return str_replace($tr_chars, $en_chars, $text);
}

echo "Banner Tip İsimleri ve CSS Dosya Yolları:\n";
echo "-------------------------------------------\n";

foreach ($bannerTypes as $type) {
    $typeName = turkish_to_lower($type['type_name']);
    $typeName = trToEn($typeName);
    $typeName = strtolower(str_replace(' ', '-', $typeName));
    $cssPath = "Public/CSS/Banners/{$typeName}.min.css";
    
    echo "Tip ID: {$type['type_id']}\n";
    echo "Orijinal İsim: {$type['type_name']}\n";
    echo "Dönüştürülmüş İsim: {$typeName}\n";
    echo "CSS Dosya Yolu: {$cssPath}\n";
    
    if (file_exists($cssPath)) {
        echo "✓ CSS Dosyası Mevcut\n";
    } else {
        echo "✗ CSS Dosyası Bulunamadı\n";
    }
    echo "---\n";
}

echo "\nMevcut CSS Dosyaları:\n";
echo "---------------------\n";

$cssDir = 'Public/CSS/Banners/';
$files = scandir($cssDir);
foreach ($files as $file) {
    if (strpos($file, '.min.css') !== false) {
        echo "- {$file}\n";
    }
}

echo "\nTepe Banner CSS İçeriği Kontrolü:\n";
echo "----------------------------------\n";

$tepeMinCss = 'Public/CSS/Banners/tepe-banner.min.css';
if (file_exists($tepeMinCss)) {
    $content = file_get_contents($tepeMinCss);
    $size = strlen($content);
    echo "✓ tepe-banner.min.css mevcut ({$size} karakter)\n";
    
    // Ortalama CSS'i kontrol et
    if (strpos($content, 'text-align') !== false) {
        echo "✓ text-align kuralı var\n";
    } else {
        echo "✗ text-align kuralı yok\n";
    }
    
    if (strpos($content, 'margin') !== false) {
        echo "✓ margin kuralı var\n";
    } else {
        echo "✗ margin kuralı yok\n";
    }
    
    if (strpos($content, 'center') !== false) {
        echo "✓ center değeri var\n";
    } else {
        echo "✗ center değeri yok\n";
    }
} else {
    echo "✗ tepe-banner.min.css bulunamadı\n";
    
    // Normal CSS'i kontrol et
    $tepeCss = 'Public/CSS/Banners/tepe-banner.css';
    if (file_exists($tepeCss)) {
        echo "ℹ tepe-banner.css mevcut, minified versiyonu oluşturulmalı\n";
    }
}

echo "\nTamamlandı.\n";
?>
