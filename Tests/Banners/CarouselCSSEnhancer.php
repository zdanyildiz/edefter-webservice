<?php
/**
 * Carousel CSS Enhancement - Banner düzgün görünümü için CSS iyileştirmesi
 * 
 * Problemler:
 * 1. Banner item'lar tam genişlikte görünmüyor
 * 2. Padding/margin'lar scroll hesaplamasını etkiliyor
 * 3. Container genişliği ve item genişlikleri uyumsuz
 */

class CarouselCSSEnhancer {
    
    public function enhanceCarouselCSS() {
        $cssFile = __DIR__ . '/../../Public/CSS/Banners/Carousel.css';
        
        if (!file_exists($cssFile)) {
            echo "❌ Carousel.css bulunamadı!\n";
            return false;
        }
        
        echo "🎨 Carousel CSS iyileştiriliyor...\n";
        
        // CSS iyileştirmelerini ekle
        $additionalCSS = $this->getEnhancedCSS();
        
        // Mevcut CSS'i oku
        $currentCSS = file_get_contents($cssFile);
        
        // Eğer bu iyileştirmeler zaten eklenmemişse ekle
        if (strpos($currentCSS, '/* === CAROUSEL MATH FIX ENHANCEMENTS ===') === false) {
            file_put_contents($cssFile, $currentCSS . "\n" . $additionalCSS);
            echo "✅ CSS iyileştirmeleri eklendi\n";
        } else {
            echo "ℹ️  CSS iyileştirmeleri zaten mevcut\n";
        }
        
        echo "📝 Eklenen iyileştirmeler:\n";
        echo "   - Container genişlik hesaplaması düzeltildi\n";
        echo "   - Item flex stilleri optimize edildi\n";
        echo "   - Gap ve padding tutarlılığı sağlandı\n";
        echo "   - Responsive görünüm iyileştirildi\n";
        echo "   - Scroll snap desteği eklendi\n";
        
        return true;
    }
    
    private function getEnhancedCSS() {
        return '
/* === CAROUSEL MATH FIX ENHANCEMENTS === */
/* Banner kaydırma problemi için CSS düzeltmeleri */

/* Carousel container için gelişmiş ayarlar */
.carousel-container {
    /* Scroll davranışını optimize et */
    scroll-snap-type: x mandatory !important;
    
    /* Container\'ın genişliğini netleştir */
    width: 100% !important;
    max-width: 100% !important;
    
    /* Flex ayarlarını optimize et */
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    
    /* Gap sistemi ile düzenli aralık */
    gap: 0 !important; /* Gap\'i sıfırla, padding ile kontrol et */
    
    /* Overflow kontrol */
    overflow-x: auto !important;
    overflow-y: hidden !important;
    
    /* Scrollbar gizleme */
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}

.carousel-container::-webkit-scrollbar {
    display: none !important;
}

/* Banner item\'ları için optimize flex ayarları */
.carousel-container .banner-item {
    /* Flex shrink\'i engelle - önemli! */
    flex-shrink: 0 !important;
    flex-grow: 0 !important;
    
    /* Box-sizing\'i garanti et */
    box-sizing: border-box !important;
    
    /* Scroll snap davranışı */
    scroll-snap-align: start !important;
    
    /* Padding tutarlılığı */
    padding: 10px !important;
    
    /* Minimum genişlik garantisi */
    min-width: 0 !important;
}

/* Genişlik sınıfları için daha güçlü seçiciler */
.Carousel.single .carousel-container .banner-item {
    flex-basis: calc(100% - 20px) !important;
    width: calc(100% - 20px) !important;
    max-width: calc(100% - 20px) !important;
}

.Carousel.double .carousel-container .banner-item {
    flex-basis: calc(50% - 20px) !important;
    width: calc(50% - 20px) !important;
    max-width: calc(50% - 20px) !important;
}

.Carousel.triple .carousel-container .banner-item {
    flex-basis: calc(33.333% - 20px) !important;
    width: calc(33.333% - 20px) !important;
    max-width: calc(33.333% - 20px) !important;
}

.Carousel.quad .carousel-container .banner-item {
    flex-basis: calc(25% - 20px) !important;
    width: calc(25% - 20px) !important;
    max-width: calc(25% - 20px) !important;
}

.Carousel.quinary .carousel-container .banner-item {
    flex-basis: calc(20% - 20px) !important;
    width: calc(20% - 20px) !important;
    max-width: calc(20% - 20px) !important;
}

/* Responsive düzeltmeler */
@media (max-width: 992px) {
    .Carousel.quad .carousel-container .banner-item,
    .Carousel.quinary .carousel-container .banner-item {
        flex-basis: calc(33.333% - 20px) !important;
        width: calc(33.333% - 20px) !important;
        max-width: calc(33.333% - 20px) !important;
    }
}

@media (max-width: 768px) {
    .Carousel.triple .carousel-container .banner-item,
    .Carousel.quad .carousel-container .banner-item,
    .Carousel.quinary .carousel-container .banner-item {
        flex-basis: calc(50% - 20px) !important;
        width: calc(50% - 20px) !important;
        max-width: calc(50% - 20px) !important;
    }
}

@media (max-width: 480px) {
    .Carousel .carousel-container .banner-item {
        flex-basis: calc(100% - 20px) !important;
        width: calc(100% - 20px) !important;
        max-width: calc(100% - 20px) !important;
    }
}

/* Banner içeriği için ayarlar */
.carousel-container .banner-item .banner-image {
    width: 100% !important;
    height: auto !important;
    min-height: 200px !important;
}

.carousel-container .banner-item .banner-image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

/* İçerik kutusu için tutarlı boyutlar */
.carousel-container .banner-item .content-box {
    display: flex !important;
    flex-direction: column !important;
    flex-grow: 1 !important;
    padding: 15px !important;
    margin: 0 !important;
}

/* Başlık ve içerik için optimize stilleri */
.carousel-container .banner-item .content-box .title {
    margin: 0 0 10px 0 !important;
    font-size: 1.1em !important;
    font-weight: bold !important;
    line-height: 1.3 !important;
}

.carousel-container .banner-item .content-box .content {
    flex-grow: 1 !important;
    margin: 0 !important;
    line-height: 1.4 !important;
    font-size: 0.9em !important;
}

/* Debug mode - geliştirme sırasında aktif edilebilir */
/*
.carousel-container {
    border: 2px solid red !important;
}
.carousel-container .banner-item {
    border: 1px solid blue !important;
}
.carousel-container .banner-item .content-box {
    border: 1px solid green !important;
}
*/
';
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $enhancer = new CarouselCSSEnhancer();
    $enhancer->enhanceCarouselCSS();
}
?>
