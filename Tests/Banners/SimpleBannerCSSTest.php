<?php
/**
 * Basit Banner CSS Test - OpenSSL'e ihtiyaç duymadan
 * Banner CSS dosyalarını kontrol eder ve sorunları tespit eder
 */

echo "Banner CSS Test Başlatılıyor...\n";
echo "=====================================\n\n";

// CSS dosyalarının varlığını kontrol et
$cssFiles = [
    'Public/CSS/Banners/tepe-banner.css',
    'Public/CSS/Banners/banner-fixes.css',
    'Public/CSS/Banners/orta-banner.css',
    'Public/CSS/Banners/alt-banner.css'
];

echo "1. CSS Dosyaları Kontrolü:\n";
echo "----------------------------\n";

foreach ($cssFiles as $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "✓ $file ($size bytes)\n";
    } else {
        echo "✗ $file (Dosya bulunamadı)\n";
    }
}

echo "\n2. Banner Fixes CSS İçeriği:\n";
echo "------------------------------\n";

if (file_exists('Public/CSS/Banners/banner-fixes.css')) {
    $fixesContent = file_get_contents('Public/CSS/Banners/banner-fixes.css');
    
    // Tepe banner ortalama kurallarını kontrol et
    if (strpos($fixesContent, 'banner-group-2') !== false) {
        echo "✓ banner-group-2 kuralı mevcut\n";
    } else {
        echo "✗ banner-group-2 kuralı eksik\n";
    }
    
    if (strpos($fixesContent, 'text-align: center') !== false) {
        echo "✓ text-align: center kuralı mevcut\n";
    } else {
        echo "✗ text-align: center kuralı eksik\n";
    }
    
    if (strpos($fixesContent, 'margin: 0 auto') !== false) {
        echo "✓ margin: 0 auto kuralı mevcut\n";
    } else {
        echo "✗ margin: 0 auto kuralı eksik\n";
    }
}

echo "\n3. Ana Site Layout Kontrolü:\n";
echo "------------------------------\n";

// Ana layout dosyalarını kontrol et
$layoutFiles = [
    '_y/index.php',
    'index.php',
    'App/View/index.php'
];

foreach ($layoutFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file bulundu\n";
        
        $content = file_get_contents($file);
        
        // CSS include kontrolü
        if (strpos($content, 'banner-fixes.css') !== false) {
            echo "  ✓ banner-fixes.css dahil edilmiş\n";
        } else {
            echo "  ✗ banner-fixes.css dahil edilmemiş\n";
        }
        
        if (strpos($content, 'tepe-banner.css') !== false) {
            echo "  ✓ tepe-banner.css dahil edilmiş\n";
        } else {
            echo "  ✗ tepe-banner.css dahil edilmemiş\n";
        }
    }
}

echo "\n4. Banner HTML Yapısı Önerisi:\n";
echo "--------------------------------\n";

echo "Tepe banner için önerilen HTML yapısı:\n\n";
echo "<div class=\"banner-group-2 top-banner\">\n";
echo "    <div class=\"banner-container\">\n";
echo "        <div class=\"banner-item\">\n";
echo "            <img src=\"banner-resmi.jpg\" alt=\"Banner\">\n";
echo "            <div class=\"banner-title\">Banner Başlığı</div>\n";
echo "        </div>\n";
echo "    </div>\n";
echo "</div>\n\n";

echo "5. CSS Dahil Etme Önerisi:\n";
echo "---------------------------\n";
echo "Ana layout'a şu satırları ekleyin:\n\n";
echo "<link rel=\"stylesheet\" href=\"Public/CSS/Banners/tepe-banner.css\">\n";
echo "<link rel=\"stylesheet\" href=\"Public/CSS/Banners/banner-fixes.css\">\n\n";

echo "Test tamamlandı.\n";
?>
