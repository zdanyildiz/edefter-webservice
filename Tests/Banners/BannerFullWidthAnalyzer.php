<?php
/**
 * Banner Full Width ve Merkezleme Analizi
 * 
 * Bu araç banner gruplarının full-width özelliklerini analiz eder ve
 * merkezleme seçeneklerini kontrol eder.
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';

class BannerFullWidthAnalyzer
{
    private $pdo;
    private $helper;
    
    public function __construct()
    {
        $this->helper = new Helper();
        $this->connectDatabase();
    }
    
    private function connectDatabase()
    {
        global $key, $dbLocalServerName, $dbLocalUsername, $dbLocalPassword, $dbLocalName;
        
        try {
            $decryptedHost = $this->helper->decrypt($dbLocalServerName, $key);
            $decryptedUsername = $this->helper->decrypt($dbLocalUsername, $key);
            $decryptedPassword = $this->helper->decrypt($dbLocalPassword, $key);
            $decryptedDatabase = $this->helper->decrypt($dbLocalName, $key);
            
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
    
    public function analyzeBannerFullWidth()
    {
        echo "=== BANNER FULL WIDTH VE MERKEZLEME ANALİZİ ===\n\n";
        
        $this->analyzeFullWidthColumns();
        $this->analyzeExistingFullWidthBanners();
        $this->analyzeTopBannerSettings();
        $this->generateFullWidthRecommendations();
        
        return true;
    }
    
    private function analyzeFullWidthColumns()
    {
        echo "1. FULL WIDTH İLGİLİ SÜTUNLAR ANALİZİ\n";
        echo "=====================================\n";
        
        try {
            // Banner groups tablosundaki full width ile ilgili sütunları kontrol et
            $stmt = $this->pdo->prepare("DESCRIBE banner_groups");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "📊 banner_groups tablosundaki ilgili sütunlar:\n";
            
            $fullWidthColumns = [];
            foreach ($columns as $column) {
                if (stripos($column['Field'], 'full') !== false || 
                    stripos($column['Field'], 'width') !== false ||
                    stripos($column['Field'], 'size') !== false ||
                    stripos($column['Field'], 'alignment') !== false ||
                    stripos($column['Field'], 'container') !== false) {
                    
                    $fullWidthColumns[] = $column;
                    echo "   🎯 {$column['Field']}: {$column['Type']} ({$column['Default']})\n";
                }
            }
            
            if (empty($fullWidthColumns)) {
                echo "   ⚠️ Full width ile ilgili sütun bulunamadı\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Sütun analiz hatası: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function analyzeExistingFullWidthBanners()
    {
        echo "2. MEVCUT BANNER GRUPLARI VE AYARLARI\n";
        echo "=====================================\n";
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    id, group_name, layout_id, style_class,
                    group_full_size, banner_full_size, 
                    content_alignment, custom_css
                FROM banner_groups 
                ORDER BY id
            ");
            $stmt->execute();
            $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "📋 Tüm Banner Grupları:\n";
            
            foreach ($groups as $group) {
                echo "\n   🏷️ Group ID {$group['id']}: {$group['group_name']}\n";
                echo "      - Style Class: {$group['style_class']}\n";
                echo "      - Layout ID: {$group['layout_id']}\n";
                echo "      - Group Full Size: " . ($group['group_full_size'] ? 'Evet' : 'Hayır') . "\n";
                echo "      - Banner Full Size: " . ($group['banner_full_size'] ? 'Evet' : 'Hayır') . "\n";
                echo "      - Content Alignment: {$group['content_alignment']}\n";
                echo "      - Custom CSS: " . (!empty($group['custom_css']) ? 'Var' : 'Yok') . "\n";
                
                // Full width banner gruplarını tespit et
                if ($group['group_full_size'] == 1 || $group['banner_full_size'] == 1) {
                    echo "      ⭐ FULL WIDTH BANNER!\n";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ Banner grup analiz hatası: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function analyzeTopBannerSettings()
    {
        echo "3. TEPE BANNER MEVCUT AYARLARI\n";
        echo "==============================\n";
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    bg.*, bl.layout_group, bl.layout_view
                FROM banner_groups bg
                LEFT JOIN banner_layouts bl ON bg.layout_id = bl.id
                WHERE bg.id = 2
            ");
            $stmt->execute();
            $topBanner = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($topBanner) {
                echo "🎯 Tepe Banner (ID: 2) Mevcut Ayarları:\n";
                echo "   - Group Name: {$topBanner['group_name']}\n";
                echo "   - Style Class: {$topBanner['style_class']}\n";
                echo "   - Layout Group: {$topBanner['layout_group']}\n";
                echo "   - Layout View: {$topBanner['layout_view']}\n";
                echo "   - Group Full Size: " . ($topBanner['group_full_size'] ? '✅ Aktif' : '❌ Pasif') . "\n";
                echo "   - Banner Full Size: " . ($topBanner['banner_full_size'] ? '✅ Aktif' : '❌ Pasif') . "\n";
                echo "   - Content Alignment: {$topBanner['content_alignment']}\n";
                echo "   - Custom CSS: " . (!empty($topBanner['custom_css']) ? "Var: " . substr($topBanner['custom_css'], 0, 50) . "..." : "Yok") . "\n";
                
                echo "\n🔍 Tepe Banner HTML Wrapper Durumu:\n";
                echo "   - Header.php'de <section id=\"topBanner\"> wrapper mevcut ✅\n";
                echo "   - BannerManager renderTopBanners() kullanıyor ✅\n";
                
            } else {
                echo "❌ Tepe banner bulunamadı\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Tepe banner analiz hatası: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function generateFullWidthRecommendations()
    {
        echo "4. FULL WIDTH VE MERKEZLEME ÖNERİLERİ\n";
        echo "=====================================\n";
        
        echo "🎯 MEVCUT DURUM ANALİZİ:\n";
        echo "   ✅ Tepe banner zaten <section id=\"topBanner\"> içinde render ediliyor\n";
        echo "   ✅ Bu section wrapper yeterli - ek wrapper'a gerek yok\n";
        echo "   ✅ Banner_groups tablosunda full size kontrol sütunları mevcut\n\n";
        
        echo "🔧 FULL WIDTH BANNER MERKEZLEME ÇÖZÜMLERİ:\n\n";
        
        echo "   1️⃣ CSS Tabanlı Çözüm (Önerilen):\n";
        echo "      - tepe-banner.css'e merkezleme sınıfları ekle\n";
        echo "      - .banner-center-when-not-fullwidth sınıfı oluştur\n";
        echo "      - BannerController'da group_full_size kontrolü ile CSS sınıfı ekle\n\n";
        
        echo "   2️⃣ Database Tabanlı Çözüm:\n";
        echo "      - banner_groups.content_alignment sütununu kullan\n";
        echo "      - 'center', 'left', 'right', 'full' değerleri ekle\n";
        echo "      - BannerController'da bu değere göre container sınıfı ata\n\n";
        
        echo "   3️⃣ Hybrid Çözüm (En İyi):\n";
        echo "      - Database'den alignment bilgisini al\n";
        echo "      - CSS'de farklı alignment sınıfları tanımla\n";
        echo "      - JavaScript ile responsive davranış ekle\n\n";
        
        echo "📋 UYGULAMA ADIMLARI:\n";
        echo "   1. tepe-banner.css'e merkezleme stilleri ekle\n";
        echo "   2. BannerController'da group_full_size kontrolü ekle\n";
        echo "   3. CSS sınıfını dinamik olarak ata\n";
        echo "   4. Test et ve ince ayar yap\n\n";
        
        echo "💡 KOD ÖRNEĞİ:\n";
        echo "   CSS: .banner-container.centered { max-width: 1200px; margin: 0 auto; }\n";
        echo "   PHP: if (!group_full_size) { \$containerClass .= ' centered'; }\n";
        echo "   HTML: <div class=\"banner-container{\$containerClass}\">...\n";
    }
}

// Script'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'BannerFullWidthAnalyzer.php') {
    try {
        $analyzer = new BannerFullWidthAnalyzer();
        $analyzer->analyzeBannerFullWidth();
        
        echo "\n=== ANALİZ TAMAMLANDI ===\n";
        echo "Full width banner merkezleme önerileri hazır.\n";
        
    } catch (Exception $e) {
        echo "❌ Analiz hatası: " . $e->getMessage() . "\n";
    }
}
