<?php
/**
 * Tepe Banner Analiz Aracı
 * 
 * Bu script tepe banner'ın sorunlarını tespit eder:
 * - Veritabanından banner verilerini çeker
 * - HTML yapısını analiz eder
 * - CSS dosyalarını kontrol eder
 * - Layout ve görüntüleme sorunlarını tespit eder
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
    }    private function connectDatabase()
    {
        global $key, $dbLocalServerName, $dbLocalUsername, $dbLocalPassword, $dbLocalName;
        
        try {
            // Key.php'den encryption key ve Sql.php'den encrypted bilgileri al
            echo "🔓 Database şifreli bilgileri çözülüyor...\n";
            
            $decryptedHost = $this->helper->decrypt($dbLocalServerName, $key);
            $decryptedUsername = $this->helper->decrypt($dbLocalUsername, $key);
            $decryptedPassword = $this->helper->decrypt($dbLocalPassword, $key);
            $decryptedDatabase = $this->helper->decrypt($dbLocalName, $key);
            
            echo "   🔍 Çözülmüş bilgiler:\n";
            echo "   Host: '{$decryptedHost}'\n";
            echo "   Database: '{$decryptedDatabase}'\n";
            echo "   Username: '{$decryptedUsername}'\n";
            echo "   Password: '{$decryptedPassword}'\n\n";
            
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
        echo "=== TEPE BANNER ANALİZ ARACI ===\n\n";
        
        $this->analyzeBannerData();
        $this->analyzeBannerGroups();
        $this->analyzeHTMLStructure();
        $this->analyzeCSSFiles();
        $this->generateReport();
        
        return $this->results;
    }
    
    private function analyzeBannerData()
    {
        echo "1. Banner Verilerini Analiz Ediyor...\n";
        
        try {
            // Banner tablolarını listele
            $tables = $this->pdo->query("SHOW TABLES LIKE '%banner%'")->fetchAll(PDO::FETCH_COLUMN);
            echo "   Banner tabloları: " . implode(', ', $tables) . "\n";
            
            // Banner_layouts tablosunu analiz et
            if (in_array('banner_layouts', $tables)) {
                $layouts = $this->pdo->query("SELECT * FROM banner_layouts WHERE name LIKE '%tepe%' OR name LIKE '%top%'")->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($layouts)) {
                    foreach ($layouts as $layout) {
                        echo "   📋 Layout: {$layout['name']}\n";
                        echo "      - ID: {$layout['id']}\n";
                        echo "      - CSS: {$layout['css_file']}\n";
                        echo "      - Template: {$layout['template_file']}\n";
                        echo "      - Active: " . ($layout['is_active'] ? 'Yes' : 'No') . "\n\n";
                        
                        $this->results['layouts'][] = $layout;
                    }
                } else {
                    echo "   ⚠️ Tepe banner layout'u bulunamadı!\n";
                }
            }
            
            // Banner_groups tablosunu analiz et
            if (in_array('banner_groups', $tables)) {
                $groups = $this->pdo->query("SELECT * FROM banner_groups WHERE group_name LIKE '%tepe%' OR group_name LIKE '%top%'")->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($groups)) {
                    foreach ($groups as $group) {
                        echo "   🏷️ Group: {$group['group_name']}\n";
                        echo "      - ID: {$group['id']}\n";
                        echo "      - Position: {$group['position']}\n";
                        echo "      - Active: " . ($group['is_active'] ? 'Yes' : 'No') . "\n\n";
                        
                        $this->results['groups'][] = $group;
                    }
                }
            }
            
            // Banners tablosunu analiz et
            if (in_array('banners', $tables)) {
                $banners = $this->pdo->query("SELECT * FROM banners WHERE type_id IN (SELECT id FROM banner_layouts WHERE name LIKE '%tepe%' OR name LIKE '%top%') AND is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($banners)) {
                    foreach ($banners as $banner) {
                        echo "   🎯 Banner: {$banner['title']}\n";
                        echo "      - ID: {$banner['id']}\n";
                        echo "      - Image: " . ($banner['image_path'] ? 'Yes' : 'No') . "\n";
                        echo "      - Content: " . ($banner['content'] ? 'Yes' : 'No') . "\n";
                        echo "      - Button: " . ($banner['button_text'] ? 'Yes' : 'No') . "\n";
                        echo "      - Link: " . ($banner['link_url'] ? 'Yes' : 'No') . "\n\n";
                        
                        $this->results['banners'][] = $banner;
                    }
                } else {
                    echo "   ⚠️ Aktif tepe banner bulunamadı!\n";
                }
            }
            
        } catch (Exception $e) {
            echo "   ❌ Veri analiz hatası: " . $e->getMessage() . "\n";
        }
    }
    
    private function analyzeBannerGroups()
    {
        echo "2. Banner Gruplarını Kontrol Ediyor...\n";
        
        try {
            // Site config'den banner bilgilerini al
            $bannerInfo = $this->pdo->query("SELECT config_value FROM site_config WHERE config_key = 'banner_info'")->fetchColumn();
            
            if ($bannerInfo) {
                $bannerData = json_decode($bannerInfo, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "   📊 Site Config'den banner bilgileri:\n";
                    
                    foreach ($bannerData as $position => $data) {
                        if (strpos(strtolower($position), 'tepe') !== false || strpos(strtolower($position), 'top') !== false) {
                            echo "      🎯 Position: {$position}\n";
                            echo "         - Group ID: {$data['group_id']}\n";
                            echo "         - Layout ID: {$data['layout_id']}\n";
                            
                            $this->results['config'][$position] = $data;
                        }
                    }
                } else {
                    echo "   ⚠️ Banner info JSON parse hatası\n";
                }
            } else {
                echo "   ⚠️ Site config'de banner_info bulunamadı\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ Banner group analiz hatası: " . $e->getMessage() . "\n";
        }
    }
    
    private function analyzeHTMLStructure()
    {
        echo "3. HTML Yapısını Kontrol Ediyor...\n";
        
        $viewFiles = [
            'App/View/header.php',
            '_y/s/header.php', 
            'App/View/banner/tepe-banner.php'
        ];
        
        foreach ($viewFiles as $file) {
            $fullPath = dirname(__DIR__, 2) . '/' . $file;
            
            if (file_exists($fullPath)) {
                echo "   📁 {$file} - Mevcut\n";
                
                $content = file_get_contents($fullPath);
                
                // Tepe banner çağrılarını ara
                if (preg_match_all('/BannerManager|renderBanner|tepe-banner|banner-type-tepe/i', $content, $matches)) {
                    echo "      🔍 Banner referansları: " . implode(', ', array_unique($matches[0])) . "\n";
                }
                
                // CSS sınıflarını ara
                if (preg_match_all('/class=["\']([^"\']*banner[^"\']*)["\']/', $content, $matches)) {
                    echo "      🎨 CSS sınıfları: " . implode(', ', array_unique($matches[1])) . "\n";
                }
                
            } else {
                echo "   ❌ {$file} - Bulunamadı\n";
            }
        }
    }
    
    private function analyzeCSSFiles()
    {
        echo "4. CSS Dosyalarını Kontrol Ediyor...\n";
        
        $cssFiles = [
            'Public/CSS/Banners/tepe-banner.css',
            'Public/CSS/Banners/tepe-banner.min.css'
        ];
        
        foreach ($cssFiles as $file) {
            $fullPath = dirname(__DIR__, 2) . '/' . $file;
            
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "   📄 {$file} - {$size} bytes\n";
                
                $content = file_get_contents($fullPath);
                
                // CSS sınıflarını say
                preg_match_all('/\.[a-zA-Z][a-zA-Z0-9_-]*/', $content, $matches);
                $classes = array_unique($matches[0]);
                echo "      🎨 CSS sınıf sayısı: " . count($classes) . "\n";
                
                // Responsive kontrolü
                $hasResponsive = preg_match('/@media/', $content);
                echo "      📱 Responsive: " . ($hasResponsive ? 'Yes' : 'No') . "\n";
                
                // CSS Variables kontrolü
                $hasVariables = preg_match('/--[a-zA-Z-]+\s*:/', $content);
                echo "      🔧 CSS Variables: " . ($hasVariables ? 'Yes' : 'No') . "\n";
                
            } else {
                echo "   ❌ {$file} - Bulunamadı\n";
            }
        }
    }
    
    private function generateReport()
    {
        echo "\n5. SORUN TESPİT RAPORU:\n";
        echo "========================\n";
        
        $issues = [];
        $recommendations = [];
        
        // Banner verisi kontrolü
        if (empty($this->results['banners'])) {
            $issues[] = "❌ Aktif tepe banner verisi bulunamadı";
            $recommendations[] = "🔧 Veritabanında tepe banner verilerini kontrol edin";
        }
        
        // Layout kontrolü
        if (empty($this->results['layouts'])) {
            $issues[] = "❌ Tepe banner layout'u tanımlanmamış";
            $recommendations[] = "🔧 banner_layouts tablosuna tepe banner layout'u ekleyin";
        }
        
        // CSS dosya kontrolü
        $cssPath = dirname(__DIR__, 2) . '/Public/CSS/Banners/tepe-banner.css';
        if (!file_exists($cssPath)) {
            $issues[] = "❌ tepe-banner.css dosyası bulunamadı";
            $recommendations[] = "🔧 CSS dosyasının yolunu kontrol edin";
        }
        
        if (!empty($issues)) {
            echo "🚨 Tespit Edilen Sorunlar:\n";
            foreach ($issues as $issue) {
                echo "   {$issue}\n";
            }
            
            echo "\n💡 Öneriler:\n";
            foreach ($recommendations as $rec) {
                echo "   {$rec}\n";
            }
        } else {
            echo "✅ Banner sistemi düzgün görünüyor\n";
        }
        
        // Tablo standardizasyon önerileri
        echo "\n📊 TABLO STANDARDIZASYON ÖNERİLERİ:\n";
        echo "=====================================\n";
        echo "🔄 Mevcut tablo yapısını daha anlamlı hale getirmek için:\n";
        echo "   • banner_layouts -> banner_types (daha açık)\n";
        echo "   • banner_groups -> banner_positions (konum odaklı)\n";
        echo "   • banners tablosunda sütun isimleri:\n";
        echo "     - image_path -> image_url\n";
        echo "     - link_url -> target_url\n";
        echo "     - is_active -> status\n";
        echo "     - created_at, updated_at sütunları ekle\n";
    }
}

// Script'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'TopBannerAnalyzer.php') {
    try {
        $analyzer = new TopBannerAnalyzer();
        $results = $analyzer->analyze();
        
        echo "\n=== ANALİZ TAMAMLANDI ===\n";
        echo "Detaylı sonuçlar için \$results değişkenini inceleyebilirsiniz.\n";
        
    } catch (Exception $e) {
        echo "❌ Analiz hatası: " . $e->getMessage() . "\n";
    }
}
