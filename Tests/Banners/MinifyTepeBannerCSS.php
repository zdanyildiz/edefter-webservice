<?php
/**
 * CSS Minifier - tepe-banner.css'i minify eder
 */

echo "CSS Minify İşlemi Başlatılıyor...\n";
echo "==================================\n\n";

$sourceFile = 'Public/CSS/Banners/tepe-banner.css';
$targetFile = 'Public/CSS/Banners/tepe-banner.min.css';

if (!file_exists($sourceFile)) {
    echo "❌ Kaynak dosya bulunamadı: $sourceFile\n";
    exit(1);
}

$css = file_get_contents($sourceFile);
$originalSize = strlen($css);

echo "📁 Kaynak dosya: $sourceFile ($originalSize bytes)\n";

// CSS Minify fonksiyonu
function minifyCSS($css) {
    // Yorumları kaldır
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Gereksiz boşlukları kaldır
    $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
    
    // Çoklu boşlukları tek boşluk yap
    $css = preg_replace('/\s+/', ' ', $css);
    
    // CSS kuralları etrafındaki boşlukları kaldır
    $css = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;', ', ', ' ,'], ['{', '{', '}', '}', ':', ':', ';', ';', ',', ','], $css);
    
    // Son noktalı virgülü kaldır
    $css = rtrim($css, ';');
    
    return trim($css);
}

// CSS'i minify et
$minifiedCSS = minifyCSS($css);
$minifiedSize = strlen($minifiedCSS);

// Ortalama için ek CSS kuralları ekle
$centeringCSS = "
/* Tepe Banner Ortalama Düzeltmeleri */
.banner-group-2 {
    margin: 0 auto !important;
    max-width: 1400px !important;
}
.banner-group-2 .banner-container {
    margin: 0 auto !important;
    text-align: center !important;
}
.banner-type-tepe-banner .banner-item {
    text-align: center !important;
}
";

// Ek CSS'i de minify et
$centeringCSSMinified = minifyCSS($centeringCSS);

// Birleştir
$finalCSS = $minifiedCSS;
if (!empty($centeringCSSMinified)) {
    $finalCSS .= $centeringCSSMinified;
}

$finalSize = strlen($finalCSS);

// Dosyaya yaz
file_put_contents($targetFile, $finalCSS);

echo "📝 Minify edilmiş dosya: $targetFile ($finalSize bytes)\n";
echo "📊 Boyut azalması: " . ($originalSize - $finalSize) . " bytes (" . round((($originalSize - $finalSize) / $originalSize) * 100, 2) . "%)\n";

echo "\n✅ CSS minify işlemi tamamlandı!\n";

// İçeriği kontrol et
echo "\n📋 Minified CSS'in ilk 200 karakteri:\n";
echo "----------------------------------------\n";
echo substr($finalCSS, 0, 200) . "...\n";

echo "\n📋 Ortalama kuralları eklendi:\n";
echo "------------------------------\n";
if (strpos($finalCSS, 'margin:0 auto') !== false) {
    echo "✅ margin:0 auto kuralı ✓\n";
}
if (strpos($finalCSS, 'text-align:center') !== false) {
    echo "✅ text-align:center kuralı ✓\n";
}
if (strpos($finalCSS, 'banner-group-2') !== false) {
    echo "✅ banner-group-2 selector ✓\n";
}

echo "\nTamamlandı!\n";
?>
