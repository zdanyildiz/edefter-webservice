<?php
/**
 * Banner System Health Monitor - Simplified Version
 * Banner sisteminin sağlığını kontrol eden basit monitoring sistemi
 */

class BannerHealthChecker {
    
    public function checkBannerHealth() {
        echo "=== BANNER SYSTEM HEALTH CHECK ===\n";
        echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";
        
        $this->checkCarouselCSS();
        $this->checkCarouselJS();
        $this->checkBannerImages();
        
        echo "\n=== HEALTH CHECK COMPLETED ===\n";
    }
    
    private function checkCarouselCSS() {
        echo "1. Checking Carousel CSS...\n";
        
        $cssFile = __DIR__ . '/../../Public/CSS/Banners/Carousel.css';
        
        if (!file_exists($cssFile)) {
            echo "   ❌ FAIL: Carousel.css not found\n";
            return;
        }
        
        $cssContent = file_get_contents($cssFile);
        $cssSize = strlen($cssContent);
        
        echo "   ✅ File exists (Size: {$cssSize} bytes)\n";
        
        // Kritik CSS sınıflarını kontrol et
        $requiredClasses = ['.carousel-controls', '.carousel-btn', '.carousel-prev', '.carousel-next'];
        $missingClasses = [];
        
        foreach ($requiredClasses as $class) {
            if (strpos($cssContent, $class) === false) {
                $missingClasses[] = $class;
            }
        }
        
        if (empty($missingClasses)) {
            echo "   ✅ All required CSS classes found\n";
        } else {
            echo "   ❌ Missing CSS classes: " . implode(', ', $missingClasses) . "\n";
        }
        
        // Z-index kontrolü
        if (strpos($cssContent, 'z-index') !== false) {
            echo "   ✅ Z-index properties found\n";
        } else {
            echo "   ⚠️  WARNING: No z-index properties found\n";
        }
    }
    
    private function checkCarouselJS() {
        echo "\n2. Checking Carousel JavaScript (via BannerController)...\n";
        
        $controllerFile = __DIR__ . '/../../App/Controller/BannerController.php';
        
        if (!file_exists($controllerFile)) {
            echo "   ❌ FAIL: BannerController.php not found\n";
            return;
        }
        
        $controllerContent = file_get_contents($controllerFile);
        
        // JavaScript metotlarını kontrol et
        $jsChecks = [
            'getCarouselJS' => 'getCarouselJS method',
            'addEventListener' => 'Event listeners setup',
            'carousel-btn' => 'Button selectors',
            'onclick' => 'Click handlers'
        ];
        
        foreach ($jsChecks as $search => $description) {
            if (strpos($controllerContent, $search) !== false) {
                echo "   ✅ {$description} found\n";
            } else {
                echo "   ❌ {$description} missing\n";
            }
        }
    }
    
    private function checkBannerImages() {
        echo "\n3. Checking Banner Images...\n";
        
        $imagePath = __DIR__ . '/../../Public/Image/Banner/';
        
        if (!is_dir($imagePath)) {
            echo "   ❌ FAIL: Banner image directory not found\n";
            return;
        }
        
        $imageFiles = glob($imagePath . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $totalImages = count($imageFiles);
        
        echo "   ✅ Banner directory exists\n";
        echo "   📁 Total images found: {$totalImages}\n";
        
        if ($totalImages > 0) {
            // İlk birkaç resmi kontrol et
            $sampleImages = array_slice($imageFiles, 0, 3);
            foreach ($sampleImages as $image) {
                $size = filesize($image);
                $name = basename($image);
                echo "   📷 {$name} ({$size} bytes)\n";
            }
        }
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $checker = new BannerHealthChecker();
    $checker->checkBannerHealth();
}
?>
