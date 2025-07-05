<?php
/**
 * Carousel Banner Düzeltme Scripti
 * Bu script carousel butonlarının çalışmama problemini çözer
 */

// Proje kök dizinini belirle
$projectRoot = realpath(__DIR__ . '/../../');

echo "<!DOCTYPE html>\n";
echo "<html lang='tr'>\n";
echo "<head>\n";
echo "<meta charset='UTF-8'>\n";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "<title>Carousel Düzeltme</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }\n";
echo ".fix-container { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }\n";
echo ".fix-container h3 { color: #333; margin-top: 0; }\n";
echo ".code-block { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; font-family: 'Courier New', monospace; font-size: 12px; }\n";
echo ".success { background: #d4edda; color: #155724; border-color: #c3e6cb; }\n";
echo ".warning { background: #fff3cd; color: #856404; border-color: #ffeaa7; }\n";
echo ".error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";

echo "<div class='fix-container'>\n";
echo "<h3>🔧 Carousel Düzeltme Scripti</h3>\n";
echo "<p><strong>Tarih:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
echo "</div>\n";

// 1. Carousel.css dosyasını kontrol et
$carouselCSSPath = $projectRoot . '/Public/CSS/Banners/Carousel.css';
echo "<div class='fix-container'>\n";
echo "<h3>📁 Dosya Kontrolleri</h3>\n";

if (file_exists($carouselCSSPath)) {
    echo "<div class='code-block success'>✅ Carousel.css dosyası bulundu: " . $carouselCSSPath . "</div>\n";
    $cssSize = filesize($carouselCSSPath);
    echo "<div class='code-block'>📏 Dosya boyutu: " . $cssSize . " bytes</div>\n";
} else {
    echo "<div class='code-block error'>❌ Carousel.css dosyası bulunamadı!</div>\n";
}

// 2. BannerController.php dosyasını kontrol et
$bannerControllerPath = $projectRoot . '/App/Controller/BannerController.php';
if (file_exists($bannerControllerPath)) {
    echo "<div class='code-block success'>✅ BannerController.php dosyası bulundu</div>\n";
    
    // Carousel JS fonksiyonunu kontrol et
    $controllerContent = file_get_contents($bannerControllerPath);
    if (strpos($controllerContent, 'getCarouselJS') !== false) {
        echo "<div class='code-block success'>✅ getCarouselJS fonksiyonu mevcut</div>\n";
    } else {
        echo "<div class='code-block error'>❌ getCarouselJS fonksiyonu bulunamadı!</div>\n";
    }
} else {
    echo "<div class='code-block error'>❌ BannerController.php dosyası bulunamadı!</div>\n";
}

echo "</div>\n";

// 3. CSS Düzeltme önerileri
echo "<div class='fix-container'>\n";
echo "<h3>🎨 CSS Düzeltme Önerileri</h3>\n";

$fixedCSS = "
/* Carousel Buton Düzeltmeleri - Critical Fix */
.carousel-controls {
    position: absolute !important;
    top: 50% !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    height: 0 !important;
    z-index: 1000 !important;
    pointer-events: none !important;
    transform: translateY(-50%) !important;
    display: flex !important;
    justify-content: space-between !important;
    padding: 0 20px !important;
    box-sizing: border-box !important;
}

.carousel-controls .prev-carousel,
.carousel-controls .next-carousel {
    position: relative !important;
    z-index: 1001 !important;
    pointer-events: auto !important;
    cursor: pointer !important;
    
    /* Görsel iyileştirmeler */
    background-color: rgba(0, 0, 0, 0.8) !important;
    color: #ffffff !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 50% !important;
    width: 50px !important;
    height: 50px !important;
    
    /* Flexbox merkezleme */
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    
    /* Font ve boyut */
    font-size: 18px !important;
    font-weight: bold !important;
    line-height: 1 !important;
    
    /* Geçiş efektleri */
    transition: all 0.3s ease !important;
    opacity: 0.8 !important;
    
    /* Gölge efekti */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
    
    /* Tarayıcı uyumluluğu */
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
}

.carousel-controls .prev-carousel:hover,
.carousel-controls .next-carousel:hover {
    background-color: rgba(0, 0, 0, 0.95) !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
    opacity: 1 !important;
    transform: scale(1.1) !important;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4) !important;
}

.carousel-controls .prev-carousel:active,
.carousel-controls .next-carousel:active {
    transform: scale(0.95) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
}

/* Carousel container düzeltmeleri */
.carousel-container {
    position: relative !important;
    overflow-x: auto !important;
    scroll-behavior: smooth !important;
    -webkit-overflow-scrolling: touch !important;
    scrollbar-width: none !important;
}

.carousel-container::-webkit-scrollbar {
    display: none !important;
}

/* Banner grup düzeltmeleri */
.banner-group-1 {
    position: relative !important;
}

/* Responsive iyileştirmeler */
@media (max-width: 768px) {
    .carousel-controls {
        padding: 0 10px !important;
    }
    
    .carousel-controls .prev-carousel,
    .carousel-controls .next-carousel {
        width: 40px !important;
        height: 40px !important;
        font-size: 14px !important;
    }
}

@media (max-width: 480px) {
    .carousel-controls {
        padding: 0 5px !important;
    }
    
    .carousel-controls .prev-carousel,
    .carousel-controls .next-carousel {
        width: 35px !important;
        height: 35px !important;
        font-size: 12px !important;
    }
}
";

echo "<div class='code-block'>" . htmlspecialchars($fixedCSS) . "</div>\n";
echo "</div>\n";

// 4. CSS dosyasını güncelle
echo "<div class='fix-container'>\n";
echo "<h3>💾 CSS Dosyası Güncelleme</h3>\n";

if (file_exists($carouselCSSPath)) {
    // Mevcut CSS'i oku
    $currentCSS = file_get_contents($carouselCSSPath);
    
    // Düzeltme CSS'ini ekle
    $updatedCSS = $currentCSS . "\n\n" . $fixedCSS;
    
    // Dosyayı güncelle
    if (file_put_contents($carouselCSSPath, $updatedCSS)) {
        echo "<div class='code-block success'>✅ Carousel.css dosyası başarıyla güncellendi!</div>\n";
        echo "<div class='code-block'>📝 Eklenen CSS boyutu: " . strlen($fixedCSS) . " karakter</div>\n";
    } else {
        echo "<div class='code-block error'>❌ CSS dosyası güncellenemedi!</div>\n";
    }
} else {
    echo "<div class='code-block error'>❌ CSS dosyası bulunamadığı için güncelleme yapılamadı!</div>\n";
}

echo "</div>\n";

// 5. Test önerileri
echo "<div class='fix-container'>\n";
echo "<h3>🧪 Test Önerileri</h3>\n";
echo "<ol>\n";
echo "<li><strong>Ana siteyi yenileyin:</strong> <a href='http://l.erhanozel' target='_blank'>http://l.erhanozel</a></li>\n";
echo "<li><strong>Carousel butonlarına tıklayın</strong></li>\n";
echo "<li><strong>Console'da hata kontrol edin:</strong> F12 > Console</li>\n";
echo "<li><strong>Buton test komutu:</strong> <code>document.querySelector('.next-carousel').click()</code></li>\n";
echo "</ol>\n";
echo "</div>\n";

// 6. Debug bilgileri
echo "<div class='fix-container'>\n";
echo "<h3>🔍 Debug Bilgileri</h3>\n";
echo "<div class='code-block'>\n";
echo "Proje dizini: " . $projectRoot . "\n";
echo "CSS dosyası: " . $carouselCSSPath . "\n";
echo "Controller dosyası: " . $bannerControllerPath . "\n";
echo "Script çalışma zamanı: " . date('Y-m-d H:i:s') . "\n";
echo "</div>\n";
echo "</div>\n";

echo "</body>\n";
echo "</html>\n";
?>
