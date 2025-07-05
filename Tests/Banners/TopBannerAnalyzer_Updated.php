<?php
/**
 * Tepe Banner Gerçek Analiz Aracı - Canlı DB Yapısına Göre
 * 
 * Canlı veritabanından aldığınız bilgilere göre güncellenmiş analiz aracı:
 * - Group ID 2: Tepe Banner (mevcut)
 * - Layout ID 3: "Arkaplan Resim ve Yazı Ortalı" (mevcut)
 * - Banner verilerini kontrol eder
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';

class TopBannerAnalyzer
{
    private $pdo;
    private $helper;
    private $results = [];
    
    public function __construct()
    {
        $this->helper = new Helper();
        $this->connectDatabase();
    }
    
    private function connectDatabase()
    {
        global $key, $dbLocalServerName, $dbLocalUsername, $dbLocalPassword, $dbLocalName;
        
        try {
            // Key.php'den encryption key ve Sql.php'den encrypted bilgileri al
            echo "🔓 Database şifreli bilgileri çözülüyor...\n";
            
            $decryptedHost = $this->helper->decrypt($dbLocalServerName, $key);
            $decryptedUsername = $this->helper->decrypt($dbLocalUsername, $key);
            $decryptedPassword = $this->helper->decrypt($dbLocalPassword, $key);
            $decryptedDatabase = $this->helper->decrypt($dbLocalName, $key);
            
            echo "   🔍 Host: '{$decryptedHost}', DB: '{$decryptedDatabase}'\n\n";
            
            $this->pdo = new PDO(
                "mysql:host={$decryptedHost};dbname={$decryptedDatabase};charset=utf8mb4",
                $decryptedUsername,
                $decryptedPassword,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            echo "✅ Veritabanı bağlantısı başarılı\n\n";
            
        } catch (Exception $e) {
            echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function analyze()
    {
        echo "=== TEPE BANNER GERÇEK ANALİZ ARACI ===\n\n";
        
        $this->checkTopBannerGroup();
        $this->checkTopBannerLayout();
        $this->checkTopBannerData();
        $this->checkTopBannerHTML();
        $this->checkTopBannerCSS();
        $this->generateTroubleshootingReport();
        
        return $this->results;
    }
    
    private function checkTopBannerGroup()
    {
        echo "1. TEPE BANNER GRUP KONTROLÜ (ID: 2)\n";
        echo "=====================================\n";
          try {
            $stmt = $this->pdo->prepare("SELECT * FROM banner_groups WHERE id = 2");
            $stmt->execute();
            $group = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($group) {
                echo "✅ Tepe Banner Grubu bulundu:\n";
                echo "   - ID: {$group['id']}\n";
                echo "   - Grup Adı: '{$group['group_name']}'\n";
                echo "   - Grup Başlığı: '{$group['group_title']}'\n";
                echo "   - Layout ID: {$group['layout_id']}\n";
                echo "   - Görünüm Tipi: {$group['group_view']}\n";
                echo "   - Sütun Sayısı: {$group['columns']}\n";
                echo "   - Style Class: '{$group['style_class']}'\n";
                echo "   - Aktivasyon: " . ($group['visibility_start'] <= date('Y-m-d H:i:s') && $group['visibility_end'] >= date('Y-m-d H:i:s') ? '✅ Aktif' : '❌ Pasif') . "\n";
                
                $this->results['group'] = $group;
            } else {
                echo "❌ Tepe Banner Grubu (ID: 2) bulunamadı!\n";
                $this->results['group'] = null;
            }
            
        } catch (Exception $e) {
            echo "❌ Grup kontrol hatası: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function checkTopBannerLayout()
    {
        echo "2. TEPE BANNER LAYOUT KONTROLÜ (ID: 3)\n";
        echo "=======================================\n";
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM banner_layouts WHERE id = 3");
            $stmt->execute();
            $layout = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($layout) {
                echo "✅ Layout bulundu:\n";
                echo "   - ID: {$layout['id']}\n";
                echo "   - Layout Grup: '{$layout['layout_group']}'\n";
                echo "   - Layout Görünüm: '{$layout['layout_view']}'\n";
                echo "   - Type ID: {$layout['type_id']}\n";
                echo "   - Layout Adı: '{$layout['layout_name']}'\n";
                echo "   - Açıklama: '{$layout['description']}'\n";
                echo "   - Sütun Sayısı: {$layout['columns']}\n";
                echo "   - Max Banner: {$layout['max_banners']}\n";
                
                $this->results['layout'] = $layout;
            } else {
                echo "❌ Layout (ID: 3) bulunamadı!\n";
                $this->results['layout'] = null;
            }
            
        } catch (Exception $e) {
            echo "❌ Layout kontrol hatası: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function checkTopBannerData()
    {
        echo "3. TEPE BANNER VERİ KONTROLÜ (Group ID: 2)\n";
        echo "==========================================\n";
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM banners WHERE group_id = 2 ORDER BY id");
            $stmt->execute();
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($banners)) {
                echo "✅ " . count($banners) . " adet banner bulundu:\n\n";
                
                foreach ($banners as $banner) {
                    echo "   🎯 Banner ID: {$banner['id']}\n";
                    echo "      - Başlık: '{$banner['title']}'\n";
                    echo "      - İçerik: '" . (strlen($banner['content']) > 50 ? substr($banner['content'], 0, 50) . '...' : $banner['content']) . "'\n";
                    echo "      - Görsel: " . (!empty($banner['image']) ? "✅ {$banner['image']}" : "❌ Yok") . "\n";
                    echo "      - Link: " . (!empty($banner['link']) ? "✅ {$banner['link']}" : "❌ Yok") . "\n";
                    echo "      - Style ID: {$banner['style_id']}\n";
                    echo "      - Aktif: " . ($banner['active'] == 1 ? "✅ Evet" : "❌ Hayır") . "\n";
                    echo "      - Oluşturma: {$banner['created_at']}\n";
                    echo "      - Güncellenme: {$banner['updated_at']}\n";
                    echo "      ---\n";
                }
                
                $this->results['banners'] = $banners;
                $this->results['active_banners'] = array_filter($banners, function($b) { return $b['active'] == 1; });
                
                echo "📊 Özet:\n";
                echo "   - Toplam banner: " . count($banners) . "\n";
                echo "   - Aktif banner: " . count($this->results['active_banners']) . "\n";
                echo "   - Görselli banner: " . count(array_filter($banners, function($b) { return !empty($b['image']); })) . "\n";
                echo "   - Linkli banner: " . count(array_filter($banners, function($b) { return !empty($b['link']); })) . "\n";
                
            } else {
                echo "❌ Group ID 2'ye ait banner bulunamadı!\n";
                $this->results['banners'] = [];
            }
            
        } catch (Exception $e) {
            echo "❌ Banner veri kontrol hatası: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function checkTopBannerHTML()
    {
        echo "4. HTML TEMPLATE KONTROLÜ\n";
        echo "=========================\n";
        
        $htmlFiles = [
            '_y/s/b/top-banner.html',
            '_y/s/b/tepe-banner.html',
            'App/View/banners/top-banner.php',
            'App/View/banners/tepe-banner.php'
        ];
        
        $foundFiles = [];
        
        foreach ($htmlFiles as $file) {
            $fullPath = dirname(__DIR__, 2) . '/' . $file;
            
            if (file_exists($fullPath)) {
                $foundFiles[] = $file;
                echo "   ✅ {$file} - Mevcut\n";
                
                $content = file_get_contents($fullPath);
                $size = strlen($content);
                echo "      📄 Dosya boyutu: {$size} byte\n";
                
                // HTML yapısını kontrol et
                if (preg_match('/<div[^>]*class=["\'][^"\']*banner[^"\']*["\']/', $content)) {
                    echo "      🎨 Banner CSS sınıfları mevcut\n";
                }
                
                if (preg_match('/\{.*title.*\}|\{.*content.*\}|\{.*image.*\}/', $content)) {
                    echo "      🔧 Template değişkenleri mevcut\n";
                }
                
            } else {
                echo "   ❌ {$file} - Bulunamadı\n";
            }
        }
        
        $this->results['html_files'] = $foundFiles;
        echo "\n";
    }
    
    private function checkTopBannerCSS()
    {
        echo "5. CSS DOSYA KONTROLÜ\n";
        echo "=====================\n";
        
        $cssFiles = [
            'Public/CSS/Banners/tepe-banner.css',
            'Public/CSS/Banners/top-banner.css',
            'Public/CSS/Banners/tepe-banner.min.css'
        ];
        
        $foundCSS = [];
        
        foreach ($cssFiles as $file) {
            $fullPath = dirname(__DIR__, 2) . '/' . $file;
            
            if (file_exists($fullPath)) {
                $foundCSS[] = $file;
                $size = filesize($fullPath);
                echo "   ✅ {$file} - {$size} bytes\n";
                
                $content = file_get_contents($fullPath);
                
                // CSS özellikleri kontrol et
                $classCount = preg_match_all('/\.[a-zA-Z][a-zA-Z0-9_-]*\s*\{/', $content, $matches);
                echo "      🎨 CSS sınıf sayısı: {$classCount}\n";
                
                $hasResponsive = preg_match('/@media/', $content);
                echo "      📱 Responsive: " . ($hasResponsive ? 'Var' : 'Yok') . "\n";
                
                $hasVariables = preg_match('/--[a-zA-Z-]+\s*:/', $content);
                echo "      🔧 CSS Variables: " . ($hasVariables ? 'Var' : 'Yok') . "\n";
                
                // Tepe banner spesifik sınıfları
                $hasTopBannerClasses = preg_match('/\.banner-type-tepe|\.tepe-banner|\.top-banner/', $content);
                echo "      🎯 Tepe Banner sınıfları: " . ($hasTopBannerClasses ? 'Var' : 'Yok') . "\n";
                
            } else {
                echo "   ❌ {$file} - Bulunamadı\n";
            }
        }
        
        $this->results['css_files'] = $foundCSS;
        echo "\n";
    }
    
    private function generateTroubleshootingReport()
    {
        echo "6. SORUN GİDERME RAPORU\n";
        echo "=======================\n";
        
        $issues = [];
        $solutions = [];
        
        // Grup kontrolü
        if (!$this->results['group']) {
            $issues[] = "❌ Tepe Banner grubu (ID: 2) mevcut değil";
            $solutions[] = "🔧 banner_groups tablosuna ID 2 ile tepe banner grubu ekleyin";
        }
        
        // Layout kontrolü
        if (!$this->results['layout']) {
            $issues[] = "❌ Banner layout (ID: 3) mevcut değil";
            $solutions[] = "🔧 banner_layouts tablosuna ID 3 ile layout ekleyin";
        }
        
        // Banner veri kontrolü
        if (empty($this->results['banners'])) {
            $issues[] = "❌ Group ID 2'ye ait banner verisi yok";
            $solutions[] = "🔧 banners tablosuna group_id=2 ile banner verisi ekleyin";
        } else if (empty($this->results['active_banners'])) {
            $issues[] = "⚠️ Aktif banner bulunamadı";
            $solutions[] = "🔧 Mevcut bannerlarda active=1 yapın";
        }
        
        // HTML template kontrolü
        if (empty($this->results['html_files'])) {
            $issues[] = "❌ Tepe banner HTML template dosyası bulunamadı";
            $solutions[] = "🔧 _y/s/b/ klasörüne tepe-banner.html template ekleyin";
        }
        
        // CSS kontrolü
        if (empty($this->results['css_files'])) {
            $issues[] = "❌ Tepe banner CSS dosyası bulunamadı";
            $solutions[] = "🔧 Public/CSS/Banners/tepe-banner.css dosyasını kontrol edin";
        }
        
        // Rapor çıktısı
        if (!empty($issues)) {
            echo "🚨 TESPİT EDİLEN SORUNLAR:\n";
            foreach ($issues as $issue) {
                echo "   {$issue}\n";
            }
            
            echo "\n💡 ÖNERİLEN ÇÖZÜMLER:\n";
            foreach ($solutions as $solution) {
                echo "   {$solution}\n";
            }
        } else {
            echo "✅ Tepe banner sistemi eksiksiz görünüyor!\n";
            echo "   Eğer banner görünmüyorsa:\n";
            echo "   1. BannerController'da group_id=2 renderlanıyor mu kontrol edin\n";
            echo "   2. CSS dosyasında .banner-type-tepe-banner sınıfları kontrol edin\n";
            echo "   3. HTML template'inde değişkenler doğru map ediliyor mu kontrol edin\n";
        }
        
        echo "\n📋 SONRAKİ ADIMLAR:\n";
        echo "   1. Banner render edilme süreci takip edilmeli\n";
        echo "   2. CSS dosyasında alignment sorunları kontrol edilmeli\n";
        echo "   3. JavaScript/jQuery ile banner etkileşimleri test edilmeli\n";
        echo "   4. Responsive davranış kontrol edilmeli\n";
    }
}

// Script'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'TopBannerAnalyzer_Updated.php') {
    try {
        $analyzer = new TopBannerAnalyzer();
        $results = $analyzer->analyze();
        
        echo "\n=== ANALİZ TAMAMLANDI ===\n";
        echo "Gerçek veritabanı yapısına göre analiz sonuçları hazır.\n";
        
    } catch (Exception $e) {
        echo "❌ Analiz hatası: " . $e->getMessage() . "\n";
    }
}
