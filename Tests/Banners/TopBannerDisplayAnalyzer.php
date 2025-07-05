<?php
/**
 * Tepe-Banner Görünüm Analizi
 * Veri tabanından tepe-banner verilerini çeker ve görünüm sorunlarını analiz eder
 */

require_once __DIR__ . '/../../App/Database/Database.php';

class TopBannerDisplayAnalyzer {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function analyzeTopBannerDisplay() {
        echo "=== TEPE-BANNER GÖRÜNÜM ANALİZİ ===\n\n";
        
        // 1. Aktif tepe-banner bilgilerini çek
        $this->analyzeActiveBanners();
        
        // 2. Banner grup bilgilerini analiz et
        $this->analyzeBannerGroups();
        
        // 3. CSS atamalarını kontrol et
        $this->analyzeCSSAssignments();
        
        // 4. Layout bilgilerini kontrol et
        $this->analyzeLayouts();
        
        // 5. İçerik alanlarını kontrol et
        $this->analyzeContentFields();
    }

    private function analyzeActiveBanners() {
        echo "1. AKTİF TEPE-BANNER VERİLERİ:\n";
        echo str_repeat("-", 50) . "\n";
        
        $query = "SELECT b.*, bt.type_name, bg.group_name, bl.layout_name 
                  FROM banners b 
                  LEFT JOIN banner_types bt ON b.type_id = bt.id 
                  LEFT JOIN banner_groups bg ON b.group_id = bg.id 
                  LEFT JOIN banner_layouts bl ON b.layout_id = bl.id 
                  WHERE bt.type_name = 'tepe-banner' AND b.active = 1 
                  ORDER BY b.order_position";
        
        $result = $this->db->select($query);
        
        if (empty($result)) {
            echo "❌ Aktif tepe-banner bulunamadı!\n\n";
            return;
        }
        
        foreach ($result as $banner) {
            echo "Banner ID: {$banner['id']}\n";
            echo "Başlık: " . ($banner['title'] ?: '❌ BOŞ') . "\n";
            echo "İçerik: " . ($banner['content'] ? substr($banner['content'], 0, 100) . '...' : '❌ BOŞ') . "\n";
            echo "Resim: " . ($banner['image'] ?: '❌ BOŞ') . "\n";
            echo "Link: " . ($banner['link'] ?: '❌ BOŞ') . "\n";
            echo "Buton Metni: " . ($banner['button_text'] ?: '❌ BOŞ') . "\n";
            echo "CSS Sınıfı: " . ($banner['css_class'] ?: '❌ BOŞ') . "\n";
            echo "Layout: " . ($banner['layout_name'] ?: '❌ BOŞ') . "\n";
            echo "Grup: " . ($banner['group_name'] ?: '❌ BOŞ') . "\n";
            echo "Sıra: {$banner['order_position']}\n";
            echo "Aktif: " . ($banner['active'] ? '✅ Evet' : '❌ Hayır') . "\n";
            echo str_repeat("-", 30) . "\n";
        }
        echo "\n";
    }

    private function analyzeBannerGroups() {
        echo "2. BANNER GRUP BİLGİLERİ:\n";
        echo str_repeat("-", 50) . "\n";
        
        $query = "SELECT bg.*, COUNT(b.id) as banner_count 
                  FROM banner_groups bg 
                  LEFT JOIN banners b ON bg.id = b.group_id AND b.active = 1 
                  LEFT JOIN banner_types bt ON b.type_id = bt.id 
                  WHERE bt.type_name = 'tepe-banner' OR bg.group_name LIKE '%tepe%' OR bg.group_name LIKE '%top%'
                  GROUP BY bg.id";
        
        $result = $this->db->select($query);
        
        foreach ($result as $group) {
            echo "Grup ID: {$group['id']}\n";
            echo "Grup Adı: {$group['group_name']}\n";
            echo "Açıklama: " . ($group['description'] ?: 'Yok') . "\n";
            echo "Aktif Banner Sayısı: {$group['banner_count']}\n";
            echo "CSS Sınıfı: " . ($group['css_class'] ?: 'Yok') . "\n";
            echo str_repeat("-", 30) . "\n";
        }
        echo "\n";
    }

    private function analyzeCSSAssignments() {
        echo "3. CSS ATAMA ANALİZİ:\n";
        echo str_repeat("-", 50) . "\n";
        
        // Banner'larda kullanılan CSS sınıfları
        $query = "SELECT DISTINCT b.css_class, COUNT(*) as usage_count 
                  FROM banners b 
                  LEFT JOIN banner_types bt ON b.type_id = bt.id 
                  WHERE bt.type_name = 'tepe-banner' AND b.active = 1 
                  GROUP BY b.css_class";
        
        $result = $this->db->select($query);
        
        echo "Banner CSS Sınıfları:\n";
        foreach ($result as $css) {
            echo "- " . ($css['css_class'] ?: 'CSS Yok') . " (Kullanım: {$css['usage_count']})\n";
        }
        
        // Grup CSS sınıfları
        $query = "SELECT DISTINCT bg.css_class, COUNT(b.id) as banner_count 
                  FROM banner_groups bg 
                  LEFT JOIN banners b ON bg.id = b.group_id 
                  LEFT JOIN banner_types bt ON b.type_id = bt.id 
                  WHERE bt.type_name = 'tepe-banner' 
                  GROUP BY bg.css_class";
        
        $result = $this->db->select($query);
        
        echo "\nGrup CSS Sınıfları:\n";
        foreach ($result as $css) {
            echo "- " . ($css['css_class'] ?: 'CSS Yok') . " (Banner Sayısı: {$css['banner_count']})\n";
        }
        echo "\n";
    }

    private function analyzeLayouts() {
        echo "4. LAYOUT ANALİZİ:\n";
        echo str_repeat("-", 50) . "\n";
        
        $query = "SELECT bl.*, COUNT(b.id) as usage_count 
                  FROM banner_layouts bl 
                  LEFT JOIN banners b ON bl.id = b.layout_id 
                  LEFT JOIN banner_types bt ON b.type_id = bt.id 
                  WHERE bt.type_name = 'tepe-banner' OR bl.layout_name LIKE '%tepe%' OR bl.layout_name LIKE '%top%'
                  GROUP BY bl.id";
        
        $result = $this->db->select($query);
        
        foreach ($result as $layout) {
            echo "Layout ID: {$layout['id']}\n";
            echo "Layout Adı: {$layout['layout_name']}\n";
            echo "Açıklama: " . ($layout['description'] ?: 'Yok') . "\n";
            echo "Template: " . ($layout['template_file'] ?: 'Yok') . "\n";
            echo "CSS: " . ($layout['css_class'] ?: 'Yok') . "\n";
            echo "Kullanım: {$layout['usage_count']} banner\n";
            echo str_repeat("-", 30) . "\n";
        }
        echo "\n";
    }

    private function analyzeContentFields() {
        echo "5. İÇERİK ALANI ANALİZİ:\n";
        echo str_repeat("-", 50) . "\n";
        
        $query = "SELECT 
                    COUNT(*) as total_banners,
                    COUNT(CASE WHEN title != '' AND title IS NOT NULL THEN 1 END) as has_title,
                    COUNT(CASE WHEN content != '' AND content IS NOT NULL THEN 1 END) as has_content,
                    COUNT(CASE WHEN image != '' AND image IS NOT NULL THEN 1 END) as has_image,
                    COUNT(CASE WHEN link != '' AND link IS NOT NULL THEN 1 END) as has_link,
                    COUNT(CASE WHEN button_text != '' AND button_text IS NOT NULL THEN 1 END) as has_button
                  FROM banners b 
                  LEFT JOIN banner_types bt ON b.type_id = bt.id 
                  WHERE bt.type_name = 'tepe-banner' AND b.active = 1";
        
        $result = $this->db->select($query);
        
        if (!empty($result)) {
            $stats = $result[0];
            echo "Toplam Aktif Tepe-Banner: {$stats['total_banners']}\n";
            echo "Başlık Olan: {$stats['has_title']} / {$stats['total_banners']}\n";
            echo "İçerik Olan: {$stats['has_content']} / {$stats['total_banners']}\n";
            echo "Resim Olan: {$stats['has_image']} / {$stats['total_banners']}\n";
            echo "Link Olan: {$stats['has_link']} / {$stats['total_banners']}\n";
            echo "Buton Metni Olan: {$stats['has_button']} / {$stats['total_banners']}\n";
        }
        
        echo "\n=== ÖNERİLER ===\n";
        
        // Eksik alanları kontrol et
        $query = "SELECT id, title, content, image, button_text 
                  FROM banners b 
                  LEFT JOIN banner_types bt ON b.type_id = bt.id 
                  WHERE bt.type_name = 'tepe-banner' AND b.active = 1 
                  AND (title IS NULL OR title = '' OR content IS NULL OR content = '' OR image IS NULL OR image = '')";
        
        $incomplete = $this->db->select($query);
        
        if (!empty($incomplete)) {
            echo "❌ Eksik bilgili banner'lar:\n";
            foreach ($incomplete as $banner) {
                echo "- Banner ID {$banner['id']}: ";
                $missing = [];
                if (!$banner['title']) $missing[] = 'başlık';
                if (!$banner['content']) $missing[] = 'içerik';
                if (!$banner['image']) $missing[] = 'resim';
                echo implode(', ', $missing) . " eksik\n";
            }
        } else {
            echo "✅ Tüm banner'larda temel bilgiler mevcut\n";
        }
        
        echo "\n";
    }
}

// Analizi çalıştır
$analyzer = new TopBannerDisplayAnalyzer();
$analyzer->analyzeTopBannerDisplay();
?>
