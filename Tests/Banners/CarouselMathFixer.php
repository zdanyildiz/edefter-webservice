<?php
/**
 * Carousel Math Fix - Banner kaydırma mesafesi problemi çözümü
 * 
 * Problem: calculateItemWidth() fonksiyonu yanlış scroll miktarı hesaplıyor
 * Çözüm: Gerçek item genişliğini ölçerek doğru scroll miktarını hesapla
 */

class CarouselMathFixer {
    
    public function fixCarouselJS() {
        $bannerControllerPath = __DIR__ . '/../../App/Controller/BannerController.php';
        
        if (!file_exists($bannerControllerPath)) {
            echo "❌ BannerController.php bulunamadı!\n";
            return false;
        }
        
        echo "🔧 Carousel matematik hatası düzeltiliyor...\n";
        
        $content = file_get_contents($bannerControllerPath);
        
        // Eski calculateItemWidth fonksiyonunu bul
        $oldCalculateFunction = $this->findOldCalculateFunction($content);
        
        if ($oldCalculateFunction) {
            echo "✅ Eski calculateItemWidth fonksiyonu bulundu\n";
            
            // Yeni fonksiyonu oluştur
            $newCalculateFunction = $this->createNewCalculateFunction();
            
            // Değiştir
            $newContent = str_replace($oldCalculateFunction, $newCalculateFunction, $content);
            
            // Dosyayı kaydet
            file_put_contents($bannerControllerPath, $newContent);
            
            echo "✅ calculateItemWidth fonksiyonu düzeltildi\n";
            echo "📝 Değişiklikler:\n";
            echo "   - Gerçek item genişliği ölçülüyor\n";
            echo "   - Gap/margin hesaplaması eklendi\n";
            echo "   - Responsive kontrol iyileştirildi\n";
            echo "   - Hata toleransı artırıldı\n";
            
            return true;
        } else {
            echo "❌ calculateItemWidth fonksiyonu bulunamadı\n";
            return false;
        }
    }
    
    private function findOldCalculateFunction($content) {
        // calculateItemWidth fonksiyonunu bulma regex pattern
        $pattern = '/(\s+)(\/\/ Item genişliğini doğru hesapla.*?return scrollAmount;.*?};)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            return $matches[0];
        }
        
        // Alternatif pattern
        $pattern2 = '/(\s+)(const calculateItemWidth = \(\) => \{.*?return.*?;.*?\};)/s';
        
        if (preg_match($pattern2, $content, $matches)) {
            return $matches[0];
        }
        
        return false;
    }
    
    private function createNewCalculateFunction() {
        return "
                    // Item genişliğini ve kaydırma mesafesini doğru hesapla
                    const calculateItemWidth = () => {
                        const firstItem = items[0];
                        const containerWidth = carouselContainer.clientWidth;
                        const containerComputedStyle = window.getComputedStyle(carouselContainer);
                        
                        // Gerçek item genişliğini ölç
                        const itemRect = firstItem.getBoundingClientRect();
                        const itemStyle = window.getComputedStyle(firstItem);
                        
                        // Item genişliği + margin hesapla
                        const itemWidth = itemRect.width;
                        const itemMarginLeft = parseFloat(itemStyle.marginLeft) || 0;
                        const itemMarginRight = parseFloat(itemStyle.marginRight) || 0;
                        const itemGap = parseFloat(containerComputedStyle.gap) || 0;
                        
                        // Toplam item boyutu (genişlik + margin + gap)
                        const totalItemWidth = itemWidth + itemMarginLeft + itemMarginRight + itemGap;
                        
                        console.log('[Carousel] Item ölçüleri:', {
                            itemWidth: itemWidth,
                            marginLeft: itemMarginLeft,
                            marginRight: itemMarginRight,
                            gap: itemGap,
                            totalItemWidth: totalItemWidth,
                            containerWidth: containerWidth
                        });
                        
                        // Görünür item sayısını belirle (CSS'deki class'lara göre)
                        let visibleItems = 1;
                        if (bannerGroup.classList.contains('triple')) visibleItems = 3;
                        else if (bannerGroup.classList.contains('double')) visibleItems = 2;
                        else if (bannerGroup.classList.contains('quad')) visibleItems = 4;
                        else if (bannerGroup.classList.contains('quinary')) visibleItems = 5;
                        
                        // Responsive kontrol - daha hassas
                        const currentWidth = window.innerWidth;
                        if (currentWidth <= 480) {
                            visibleItems = 1;
                        } else if (currentWidth <= 768) {
                            visibleItems = Math.min(visibleItems, 2);
                        } else if (currentWidth <= 992) {
                            visibleItems = Math.min(visibleItems, 3);
                        }
                        
                        // Kaydırma miktarını hesapla
                        // Tek item kaydırmak istiyorsak: totalItemWidth
                        // Görünür alanın tamamını kaydırmak istiyorsak: containerWidth
                        
                        // Varsayılan: tek item kaydır
                        let scrollAmount = totalItemWidth;
                        
                        // Eğer item çok büyükse veya tek item görünüyorsa, container genişliği kullan
                        if (visibleItems === 1 || totalItemWidth > containerWidth * 0.8) {
                            scrollAmount = containerWidth;
                        }
                        
                        // Minimum scroll miktarı (çok küçük kaydırmalar için)
                        scrollAmount = Math.max(scrollAmount, 50);
                        
                        console.log('[Carousel] Scroll miktarı hesaplandı:', {
                            scrollAmount: scrollAmount,
                            visibleItems: visibleItems,
                            calculationMethod: totalItemWidth > containerWidth * 0.8 ? 'container-width' : 'item-width'
                        });
                        
                        return Math.round(scrollAmount);
                    };";
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $fixer = new CarouselMathFixer();
    $fixer->fixCarouselJS();
}
?>
