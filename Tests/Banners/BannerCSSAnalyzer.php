<?php
/**
 * Banner CSS Dosyalarını Analiz Eden Test Scripti
 * 
 * Bu script banner CSS dosyalarını analiz eder ve:
 * - Mevcut CSS dosyalarının durumunu kontrol eder
 * - Minified dosyalarla normal dosyaları karşılaştırır
 * - CSS optimizasyon önerileri sunar
 * - Responsive tasarım uyumluluğunu kontrol eder
 * - Dinamik CSS entegrasyonu için gerekli değişiklikleri önerir
 */

class BannerCSSAnalyzer
{
    private $bannerPath;
    private $results = [];
    
    public function __construct()
    {
        $this->bannerPath = dirname(__DIR__, 2) . '/Public/CSS/Banners/';
    }
    
    public function analyze()
    {
        echo "=== BANNER CSS DOSYALARI ANALİZİ ===\n\n";
        
        $this->analyzeBannerFiles();
        $this->checkMinifiedFiles();
        $this->analyzeResponsiveSupport();
        $this->analyzeDynamicCSSSupport();
        $this->generateRecommendations();
        
        return $this->results;
    }
    
    private function analyzeBannerFiles()
    {
        echo "1. Banner CSS Dosyaları Taraması...\n";
        
        $files = glob($this->bannerPath . '*.css');
        $bannerTypes = [];
        
        foreach ($files as $file) {
            $filename = basename($file);
            
            // Minified dosyaları ayır
            if (strpos($filename, '.min.css') !== false) {
                continue;
            }
            
            $bannerType = str_replace('.css', '', $filename);
            $bannerTypes[] = $bannerType;
            
            $size = filesize($file);
            $content = file_get_contents($file);
            $lines = count(explode("\n", $content));
            
            $this->results['files'][$bannerType] = [
                'file' => $filename,
                'size' => $size,
                'lines' => $lines,
                'hasMinified' => file_exists($this->bannerPath . $bannerType . '.min.css'),
                'hasResponsive' => $this->checkResponsive($content),
                'hasDynamicVars' => $this->checkDynamicVariables($content),
                'modernCSS' => $this->checkModernCSS($content)
            ];
        }
        
        echo "   Toplam " . count($bannerTypes) . " banner türü bulundu.\n";
        echo "   Banner türleri: " . implode(', ', $bannerTypes) . "\n\n";
    }
    
    private function checkMinifiedFiles()
    {
        echo "2. Minified Dosya Kontrolü...\n";
        
        foreach ($this->results['files'] as $type => $info) {
            if (!$info['hasMinified']) {
                echo "   ⚠️  {$type}.min.css dosyası eksik!\n";
            } else {
                $minSize = filesize($this->bannerPath . $type . '.min.css');
                $compression = round((1 - $minSize / $info['size']) * 100, 1);
                echo "   ✅ {$type}.min.css mevcut (Sıkıştırma: %{$compression})\n";
            }
        }
        echo "\n";
    }
    
    private function analyzeResponsiveSupport()
    {
        echo "3. Responsive Tasarım Desteği...\n";
        
        foreach ($this->results['files'] as $type => $info) {
            if ($info['hasResponsive']) {
                echo "   ✅ {$type}: Responsive destekli\n";
            } else {
                echo "   ❌ {$type}: Responsive desteği eksik\n";
            }
        }
        echo "\n";
    }
    
    private function analyzeDynamicCSSSupport()
    {
        echo "4. Dinamik CSS Desteği...\n";
        
        foreach ($this->results['files'] as $type => $info) {
            if ($info['hasDynamicVars']) {
                echo "   ✅ {$type}: Dinamik değişkenler mevcut\n";
            } else {
                echo "   ❌ {$type}: Dinamik CSS desteği eksik\n";
            }
        }
        echo "\n";
    }
    
    private function checkResponsive($content)
    {
        // Media queries ve responsive özellikler arıyoruz
        $patterns = [
            '/@media\s*\([^)]+\)/',
            '/max-width\s*:\s*[0-9]+px/',
            '/min-width\s*:\s*[0-9]+px/',
            '/flex\s*:/',
            '/grid\s*:/',
            '/clamp\s*\(/',
            '/vh|vw|vmin|vmax/',
            '/rem|em/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        return false;
    }
    
    private function checkDynamicVariables($content)
    {
        // CSS custom properties ve PHP değişken entegrasyonu arıyoruz
        $patterns = [
            '/--[a-zA-Z-]+\s*:/',
            '/var\s*\(\s*--[a-zA-Z-]+/',
            '/\$[a-zA-Z_][a-zA-Z0-9_]*/',
            '/<\?php/',
            '/echo\s+\$/',
            '/\{\{\s*\$[a-zA-Z_]/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        return false;
    }
    
    private function checkModernCSS($content)
    {
        // Modern CSS özellikleri arıyoruz
        $patterns = [
            '/display\s*:\s*grid/',
            '/display\s*:\s*flex/',
            '/object-fit\s*:/',
            '/transition\s*:/',
            '/transform\s*:/',
            '/backdrop-filter\s*:/',
            '/filter\s*:/',
            '/clip-path\s*:/',
            '/aspect-ratio\s*:/'
        ];
        
        $score = 0;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $score++;
            }
        }
        
        return $score >= 3; // En az 3 modern özellik varsa modern sayıyoruz
    }
    
    private function generateRecommendations()
    {
        echo "5. İyileştirme Önerileri...\n";
        
        $needsImprovement = [];
        
        foreach ($this->results['files'] as $type => $info) {
            $issues = [];
            
            if (!$info['hasMinified']) {
                $issues[] = 'Minified dosya eksik';
            }
            
            if (!$info['hasResponsive']) {
                $issues[] = 'Responsive tasarım desteği eksik';
            }
            
            if (!$info['hasDynamicVars']) {
                $issues[] = 'Dinamik CSS desteği eksik';
            }
            
            if (!$info['modernCSS']) {
                $issues[] = 'Modern CSS özellikleri eksik';
            }
            
            if (!empty($issues)) {
                $needsImprovement[$type] = $issues;
                echo "   📋 {$type}: " . implode(', ', $issues) . "\n";
            }
        }
        
        if (empty($needsImprovement)) {
            echo "   ✅ Tüm banner CSS dosyaları güncel ve optimize!\n";
        } else {
            echo "\n   🔧 Öncelik sırası:\n";
            echo "      1. tepe-banner ✅ (Zaten iyileştirildi)\n";
            echo "      2. slider - Çok kullanılıyor, responsive iyileştirme gerekli\n";
            echo "      3. alt-banner - Modern CSS gerekli\n";
            echo "      4. orta-banner - Minimal, genişletilmeli\n";
            echo "      5. Diğer özel banner türleri\n";
        }
    }
}

// Test çalıştırma
if (basename($_SERVER['PHP_SELF']) === 'BannerCSSAnalyzer.php') {
    $analyzer = new BannerCSSAnalyzer();
    $results = $analyzer->analyze();
    
    echo "\n=== ANALİZ TAMAMLANDI ===\n";
    echo "Sonuçlar \$results değişkeninde saklandı.\n";
    echo "Detaylı rapor için var_dump(\$results) kullanabilirsiniz.\n";
}
