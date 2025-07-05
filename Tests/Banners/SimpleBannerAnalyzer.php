<?php
/**
 * Basit Banner Analiz Scripti
 * Direct PDO connection kullanarak banner verilerini analiz eder
 */

class SimpleBannerAnalyzer
{
    private $pdo;
    
    public function __construct()
    {
        try {
            $dsn = "mysql:host=localhost;dbname=yeni.globalpozitif.com.tr;charset=utf8mb4";
            $this->pdo = new PDO($dsn, 'root', 'Global2019*', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    public function analyze()
    {
        echo "=== TEPE BANNER HIZLI ANALİZ ===\n\n";
        
        $this->showBannerTypes();
        $this->showBannerLayouts();
        $this->showBannerGroups();
        $this->showTopBanners();
        $this->checkFiles();
    }
    
    private function showBannerTypes()
    {
        echo "1. BANNER TİPLERİ\n";
        echo "================\n";
        
        $stmt = $this->pdo->query("SELECT * FROM banner_types ORDER BY id");
        while ($row = $stmt->fetch()) {
            echo "ID: {$row['id']} | {$row['name']} | {$row['slug']}\n";
        }
        echo "\n";
    }
    
    private function showBannerLayouts()
    {
        echo "2. BANNER LAYOUTLARI\n";
        echo "===================\n";
        
        $stmt = $this->pdo->query("
            SELECT bl.*, bt.name as type_name 
            FROM banner_layouts bl 
            JOIN banner_types bt ON bl.type_id = bt.id 
            ORDER BY bl.id
        ");
        
        while ($row = $stmt->fetch()) {
            echo "Layout ID: {$row['id']}\n";
            echo "  Tip: {$row['type_name']}\n";
            echo "  Ad: {$row['name']}\n";
            echo "  Template: {$row['template_file']}\n";
            echo "  CSS: {$row['css_file']}\n";
            echo "  Pozisyon: {$row['position']}\n";
            echo "  Aktif: " . ($row['is_active'] ? 'Evet' : 'Hayır') . "\n";
            echo "  Config: {$row['layout_config']}\n";
            echo "  ----\n";
        }
        echo "\n";
    }
    
    private function showBannerGroups()
    {
        echo "3. BANNER GRUPLARI\n";
        echo "=================\n";
        
        $stmt = $this->pdo->query("
            SELECT bg.*, bl.name as layout_name, bl.template_file, bl.css_file, bt.name as type_name
            FROM banner_groups bg
            JOIN banner_layouts bl ON bg.layout_id = bl.id
            JOIN banner_types bt ON bl.type_id = bt.id
            ORDER BY bg.id
        ");
        
        while ($row = $stmt->fetch()) {
            echo "Grup ID: {$row['id']}\n";
            echo "  Ad: {$row['name']}\n";
            echo "  Tip: {$row['type_name']}\n";
            echo "  Layout: {$row['layout_name']}\n";
            echo "  Template: {$row['template_file']}\n";
            echo "  CSS: {$row['css_file']}\n";
            echo "  Aktif: " . ($row['is_active'] ? 'Evet' : 'Hayır') . "\n";
            echo "  ----\n";
        }
        echo "\n";
    }
    
    private function showTopBanners()
    {
        echo "4. AKTİF BANNERLAR\n";
        echo "=================\n";
        
        $stmt = $this->pdo->query("
            SELECT b.*, bg.name as group_name, bl.name as layout_name, 
                   bl.template_file, bl.css_file, bt.name as type_name, bl.position
            FROM banners b
            JOIN banner_groups bg ON b.group_id = bg.id
            JOIN banner_layouts bl ON bg.layout_id = bl.id
            JOIN banner_types bt ON bl.type_id = bt.id
            WHERE b.is_active = 1
            ORDER BY bl.position, b.sort_order
        ");
        
        $bannerCount = 0;
        while ($row = $stmt->fetch()) {
            $bannerCount++;
            echo "🎯 BANNER #{$bannerCount} - ID: {$row['id']}\n";
            echo "  Tip: {$row['type_name']}\n";
            echo "  Grup: {$row['group_name']}\n";
            echo "  Layout: {$row['layout_name']}\n";
            echo "  Pozisyon: {$row['position']}\n";
            echo "  Template: {$row['template_file']}\n";
            echo "  CSS: {$row['css_file']}\n";
            echo "  ----\n";
            echo "  📝 İÇERİK:\n";
            echo "    Başlık: " . (!empty($row['title']) ? "✅ '{$row['title']}'" : "❌ BOŞ") . "\n";
            echo "    İçerik: " . (!empty($row['content']) ? "✅ " . strlen($row['content']) . " karakter" : "❌ BOŞ") . "\n";
            echo "    Resim: " . (!empty($row['image_url']) ? "✅ '{$row['image_url']}'" : "❌ BOŞ") . "\n";
            echo "    Link: " . (!empty($row['link_url']) ? "✅ '{$row['link_url']}'" : "❌ BOŞ") . "\n";
            echo "    Buton: " . (!empty($row['button_text']) ? "✅ '{$row['button_text']}'" : "❌ BOŞ") . "\n";
            echo "    Özel CSS: " . (!empty($row['custom_css']) ? "✅ Mevcut" : "❌ Yok") . "\n";
            echo "  ----\n";
            echo "  📅 ZAMANLAMA:\n";
            echo "    Başlangıç: {$row['start_date']}\n";
            echo "    Bitiş: {$row['end_date']}\n";
            echo "    Sıra: {$row['sort_order']}\n";
            echo "  ====\n\n";
        }
        
        echo "Toplam Aktif Banner: {$bannerCount}\n\n";
    }
    
    private function checkFiles()
    {
        echo "5. DOSYA KONTROL\n";
        echo "===============\n";
        
        // CSS dosyalarını kontrol et
        $cssPath = dirname(__DIR__, 2) . '/Public/CSS/Banners/';
        echo "📁 CSS Klasörü: {$cssPath}\n";
        
        if (is_dir($cssPath)) {
            $cssFiles = glob($cssPath . '*.css');
            echo "CSS Dosyaları (" . count($cssFiles) . "):\n";
            foreach ($cssFiles as $file) {
                $size = filesize($file);
                echo "  ✅ " . basename($file) . " ({$size} bytes)\n";
            }
        } else {
            echo "❌ CSS klasörü bulunamadı!\n";
        }
        
        echo "\n";
        
        // Template dosyalarını kontrol et
        $templatePath = dirname(__DIR__, 2) . '/App/View/';
        echo "📁 Template Klasörü: {$templatePath}\n";
        
        if (is_dir($templatePath)) {
            echo "✅ Template klasörü mevcut\n";
        } else {
            echo "❌ Template klasörü bulunamadı!\n";
        }
    }
    
    public function getTableStructure($tableName)
    {
        echo "\n📊 {$tableName} TABLO YAPISI:\n";
        echo str_repeat("=", 30) . "\n";
        
        $stmt = $this->pdo->query("DESCRIBE {$tableName}");
        while ($row = $stmt->fetch()) {
            echo "{$row['Field']} | {$row['Type']} | " . 
                 ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . 
                 ($row['Key'] ? " | {$row['Key']}" : '') . "\n";
        }
        echo "\n";
    }
}

// Analizi çalıştır
if (basename($_SERVER['PHP_SELF']) === 'SimpleBannerAnalyzer.php') {
    try {
        $analyzer = new SimpleBannerAnalyzer();
        $analyzer->analyze();
        
        // Tablo yapılarını da göster
        $tables = ['banner_types', 'banner_layouts', 'banner_groups', 'banners'];
        foreach ($tables as $table) {
            $analyzer->getTableStructure($table);
        }
        
        echo "✅ Analiz tamamlandı!\n";
    } catch (Exception $e) {
        echo "❌ Hata: " . $e->getMessage() . "\n";
    }
}
