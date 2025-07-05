<?php
/**
 * Banner System Health Monitor
 * Banner sisteminin sağlığını sürekli kontrol eden monitoring sistemi
 */

class BannerSystemMonitor {
    
    private $config;
    private $logFile;
      public function __construct() {
        require_once __DIR__ . '/../../App/Core/Config.php';
        $this->config = new Config();
        $this->logFile = __DIR__ . '/../../Public/Log/banner_health.log';
    }
    
    /**
     * Banner sisteminin genel sağlığını kontrol et
     */
    public function checkSystemHealth() {
        $results = [
            'timestamp' => date('Y-m-d H:i:s'),
            'css_files' => $this->checkCSSFiles(),
            'js_functionality' => $this->checkJSFunctionality(),
            'database_connectivity' => $this->checkDatabaseConnection(),
            'image_accessibility' => $this->checkImageAccess(),
            'overall_status' => 'unknown'
        ];
        
        // Genel durumu belirle
        $failedChecks = 0;
        foreach (['css_files', 'js_functionality', 'database_connectivity', 'image_accessibility'] as $check) {
            if (!$results[$check]['status']) {
                $failedChecks++;
            }
        }
        
        if ($failedChecks === 0) {
            $results['overall_status'] = 'healthy';
        } elseif ($failedChecks <= 2) {
            $results['overall_status'] = 'warning';
        } else {
            $results['overall_status'] = 'critical';
        }
        
        $this->logResults($results);
        return $results;
    }
      /**
     * CSS dosyalarının durumunu kontrol et
     */
    private function checkCSSFiles() {
        $cssPath = __DIR__ . '/../../Public/CSS/Banners/';
        $criticalFiles = ['Carousel.css', 'Slider.css', 'Grid.css'];
        
        $results = [
            'status' => true,
            'details' => [],
            'missing_files' => []
        ];
        
        foreach ($criticalFiles as $file) {
            $fullPath = $cssPath . $file;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                $results['details'][$file] = [
                    'exists' => true,
                    'size' => $size,
                    'readable' => is_readable($fullPath)
                ];
                
                // Çok küçük dosyalar şüpheli
                if ($size < 100) {
                    $results['status'] = false;
                    $results['details'][$file]['warning'] = 'File too small, might be corrupted';
                }
            } else {
                $results['status'] = false;
                $results['missing_files'][] = $file;
                $results['details'][$file] = ['exists' => false];
            }
        }
        
        return $results;
    }
    
    /**
     * JavaScript fonksiyonalitesini test et
     */
    private function checkJSFunctionality() {
        // Bu gerçek ortamda browser automation ile yapılabilir
        // Şimdilik CSS class varlığını kontrol edelim
        
        $carouselCSS = __DIR__ . '/../../Public/CSS/Banners/Carousel.css';
        if (!file_exists($carouselCSS)) {
            return [
                'status' => false,
                'error' => 'Carousel.css not found'
            ];
        }
        
        $cssContent = file_get_contents($carouselCSS);
        $requiredClasses = ['.carousel-controls', '.carousel-btn', '.carousel-prev', '.carousel-next'];
        $missingClasses = [];
        
        foreach ($requiredClasses as $class) {
            if (strpos($cssContent, $class) === false) {
                $missingClasses[] = $class;
            }
        }
        
        return [
            'status' => empty($missingClasses),
            'missing_classes' => $missingClasses,
            'css_size' => strlen($cssContent)
        ];
    }
      /**
     * Veritabanı bağlantısını test et
     */
    private function checkDatabaseConnection() {
        try {
            // Basit DB connection test
            $pdo = new PDO(
                "mysql:host=" . $this->config->dbServerName . ";dbname=" . $this->config->dbName . ";charset=utf8",
                $this->config->dbUsername,
                $this->config->dbPassword
            );
            
            // Basit bir sorgu ile test et
            $stmt = $pdo->query("SELECT 1");
            $result = $stmt->fetch();
            
            return [
                'status' => true,
                'connection_time' => microtime(true),
                'test_query' => 'SUCCESS'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    }
    
    /**
     * Banner resimlerinin erişilebilirliğini test et
     */
    private function checkImageAccess() {
        $imagePath = __DIR__ . '/../../Public/Image/Banner/';
        
        if (!is_dir($imagePath)) {
            return [
                'status' => false,
                'error' => 'Banner image directory not found'
            ];
        }
        
        $imageFiles = glob($imagePath . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $totalImages = count($imageFiles);
        
        // İlk 5 resmi test et
        $testedImages = array_slice($imageFiles, 0, 5);
        $accessibleImages = 0;
        
        foreach ($testedImages as $image) {
            if (is_readable($image) && filesize($image) > 0) {
                $accessibleImages++;
            }
        }
        
        return [
            'status' => $accessibleImages === count($testedImages),
            'total_images' => $totalImages,
            'tested_images' => count($testedImages),
            'accessible_images' => $accessibleImages
        ];
    }
    
    /**
     * Sonuçları logla
     */
    private function logResults($results) {
        $logEntry = "[" . $results['timestamp'] . "] Banner System Health: " . $results['overall_status'] . "\n";
        $logEntry .= json_encode($results, JSON_PRETTY_PRINT) . "\n\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * CLI için sonuçları formatla
     */
    public function displayResults($results) {
        echo "=== BANNER SYSTEM HEALTH CHECK ===\n";
        echo "Timestamp: " . $results['timestamp'] . "\n";
        echo "Overall Status: " . strtoupper($results['overall_status']) . "\n\n";
        
        foreach (['css_files', 'js_functionality', 'database_connectivity', 'image_accessibility'] as $check) {
            $status = $results[$check]['status'] ? 'PASS' : 'FAIL';
            echo ucfirst(str_replace('_', ' ', $check)) . ": " . $status . "\n";
            
            if (!$results[$check]['status']) {
                echo "  Details: " . json_encode($results[$check]) . "\n";
            }
        }
        
        echo "\nLog saved to: " . $this->logFile . "\n";
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $monitor = new BannerSystemMonitor();
    $results = $monitor->checkSystemHealth();
    $monitor->displayResults($results);
}
?>
