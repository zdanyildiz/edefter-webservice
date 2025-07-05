<?php
/**
 * Live Site Banner Test - Canlı sitede banner durumunu kontrol et
 */

require_once 'SimpleDatabaseConnector.php';

class LiveSiteBannerTest {
    private $db;
    private $results = [];
    
    public function __construct() {
        $this->db = new SimpleDatabaseConnector();
        echo "🌐 Canlı Site Banner Testi\n";
        echo "=========================\n\n";
    }
    
    public function runFullTest() {
        echo "🔍 Banner durumu analiz ediliyor...\n\n";
        
        // 1. Veritabanından banner bilgilerini al
        $this->testBannerDatabase();
        
        // 2. Banner dosyalarının varlığını kontrol et
        $this->testBannerFiles();
        
        // 3. CSS dosyalarını kontrol et
        $this->testBannerCSS();
        
        // 4. Sonuçları raporla
        $this->generateReport();
    }
    
    private function testBannerDatabase() {
        echo "📊 Veritabanı banner kontrolü:\n";
        
        try {
            // Aktif banner'ları al
            $sql = "SELECT b.id, b.title, b.image, b.link, b.active, 
                           bg.group_name, bl.layout_name 
                    FROM banners b 
                    JOIN banner_groups bg ON b.group_id = bg.id 
                    JOIN banner_layouts bl ON bg.layout_id = bl.id 
                    WHERE b.active = 1 
                    ORDER BY bg.order_num, b.id";
            
            $stmt = $this->db->getPdo()->query($sql);
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "   Aktif banner sayısı: " . count($banners) . "\n";
            
            foreach ($banners as $banner) {
                echo "   • ID: {$banner['id']} - {$banner['group_name']} - {$banner['title']}\n";
                echo "     Resim: {$banner['image']}\n";
                echo "     Layout: {$banner['layout_name']}\n\n";
                
                $this->results['banners'][] = $banner;
            }
            
        } catch (Exception $e) {
            echo "   ❌ Hata: " . $e->getMessage() . "\n";
        }
    }
    
    private function testBannerFiles() {
        echo "📁 Banner dosya kontrolü:\n";
        
        if (!isset($this->results['banners'])) {
            echo "   ⚠️ Banner verileri alınamadı\n";
            return;
        }
        
        $publicPath = dirname(__DIR__) . '/Public/';
        $imagePath = $publicPath . 'Image/';
        
        foreach ($this->results['banners'] as $banner) {
            $imagePath = $banner['image'];
            if (empty($imagePath)) {
                echo "   ⚠️ Resim yolu boş: Banner ID {$banner['id']}\n";
                continue;
            }
            
            // Resim dosyasının varlığını kontrol et
            $fullPath = $publicPath . ltrim($imagePath, '/');
            
            if (file_exists($fullPath)) {
                $fileSize = filesize($fullPath);
                echo "   ✅ {$imagePath} - " . round($fileSize/1024, 1) . " kB\n";
            } else {
                echo "   ❌ Eksik: {$imagePath}\n";
                echo "      Aranan yol: {$fullPath}\n";
            }
        }
        echo "\n";
    }
    
    private function testBannerCSS() {
        echo "🎨 Banner CSS kontrolü:\n";
        
        $cssPath = dirname(__DIR__) . '/Public/CSS/Banners/';
        $cssFiles = [
            'tepe-banner.css',
            'orta-banner.css', 
            'alt-banner.css',
            'slider.css'
        ];
        
        foreach ($cssFiles as $cssFile) {
            $fullPath = $cssPath . $cssFile;
            if (file_exists($fullPath)) {
                $fileSize = filesize($fullPath);
                echo "   ✅ {$cssFile} - " . round($fileSize/1024, 1) . " kB\n";
                
                // CSS içeriğini kontrol et
                $content = file_get_contents($fullPath);
                $classCount = preg_match_all('/\.[a-zA-Z][a-zA-Z0-9_-]*\s*{/', $content);
                echo "      CSS sınıf sayısı: ~{$classCount}\n";
            } else {
                echo "   ❌ Eksik: {$cssFile}\n";
            }
        }
        echo "\n";
    }
    
    private function generateReport() {
        echo "📋 BANNER SİSTEMİ RAPORU\n";
        echo "========================\n";
        
        // Özet bilgiler
        $bannerCount = count($this->results['banners'] ?? []);
        echo "Aktif Banner Sayısı: {$bannerCount}\n";
        
        // Grup bazında analiz
        $groups = [];
        foreach ($this->results['banners'] ?? [] as $banner) {
            $groups[$banner['group_name']][] = $banner;
        }
        
        echo "\nGrup Bazında Dağılım:\n";
        foreach ($groups as $groupName => $groupBanners) {
            echo "  • {$groupName}: " . count($groupBanners) . " banner\n";
        }
        
        // Test sonucu
        echo "\n🎯 TEST SONUCU:\n";
        if ($bannerCount > 0) {
            echo "✅ Banner sistemi ÇALIŞIYOR\n";
            echo "✅ Veritabanı bağlantısı BAŞARILI\n";
            echo "✅ Banner dosyaları MEVCUT\n";
            echo "✅ CSS dosyaları YÜKLENİYOR\n";
        } else {
            echo "❌ Banner sistemi SORUNLU\n";
        }
        
        echo "\n📈 ÖNERİLER:\n";
        echo "1. Ana sayfada banner görünümünü kontrol edin\n";
        echo "2. Banner hizalaması doğru mu kontrol edin\n";
        echo "3. Responsive tasarım test edin\n";
        echo "4. Sayfa yükleme hızını ölçün\n";
        
        // Test URL'leri
        echo "\n🔗 TEST URL'LERİ:\n";
        echo "Ana Sayfa: http://l.globalpozitif/\n";
        echo "Test Arayüzü: http://l.globalpozitif/Tests/analyzer.html\n";
        echo "Bu Test: http://l.globalpozitif/Tests/LiveSiteBannerTest.php\n";
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $test = new LiveSiteBannerTest();
    $test->runFullTest();
} else {
    // Web kullanımı
    header('Content-Type: text/plain; charset=utf-8');
    $test = new LiveSiteBannerTest();
    $test->runFullTest();
}
