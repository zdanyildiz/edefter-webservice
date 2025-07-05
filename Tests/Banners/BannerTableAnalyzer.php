<?php
/**
 * Banner Tablo Yapısı Kontrol Aracı
 * 
 * Veritabanı tablolarının yapısını detaylı olarak kontrol eder
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';

class BannerTableAnalyzer
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
            
            echo "✅ Database bağlantısı başarılı\n\n";
            
        } catch (Exception $e) {
            echo "❌ Database bağlantı hatası: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function analyzeTables()
    {
        echo "=== BANNER TABLO YAPISI ANALİZİ ===\n\n";
        
        $tables = ['banners', 'banner_layouts', 'banner_groups'];
        
        foreach ($tables as $table) {
            $this->analyzeTable($table);
        }
    }
    
    private function analyzeTable($tableName)
    {
        echo "📋 {$tableName} Tablosu:\n";
        echo "═══════════════════════════════════\n";
        
        try {
            // Tablo yapısını kontrol et
            $stmt = $this->pdo->query("DESCRIBE {$tableName}");
            $columns = $stmt->fetchAll();
            
            echo "Sütunlar:\n";
            foreach ($columns as $column) {
                echo "  • {$column['Field']} ({$column['Type']}) ";
                echo $column['Null'] === 'YES' ? '[NULL]' : '[NOT NULL]';
                echo $column['Key'] === 'PRI' ? ' [PRIMARY KEY]' : '';
                echo $column['Default'] ? " [DEFAULT: {$column['Default']}]" : '';
                echo "\n";
            }
            
            // Örnek verileri göster
            $stmt = $this->pdo->query("SELECT * FROM {$tableName} LIMIT 3");
            $data = $stmt->fetchAll();
            
            echo "\nÖrnek Veriler:\n";
            if (empty($data)) {
                echo "  (Tablo boş)\n";
            } else {
                foreach ($data as $row) {
                    echo "  - ID: " . ($row['id'] ?? 'YOK') . "\n";
                    foreach ($row as $key => $value) {
                        if ($key !== 'id' && !is_null($value) && $value !== '') {
                            $displayValue = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
                            echo "    {$key}: {$displayValue}\n";
                        }
                    }
                    echo "    ---\n";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ Tablo analiz hatası: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function suggestStandardization()
    {
        echo "🔧 TABLO STANDARDIZASYON ÖNERİLERİ:\n";
        echo "═══════════════════════════════════════\n\n";
        
        echo "1. 📋 BANNER_LAYOUTS Tablosu İyileştirmeleri:\n";
        echo "   Mevcut sütunları kontrol edin ve şunları ekleyin:\n";
        echo "   • name VARCHAR(255) - Layout adı\n";
        echo "   • type VARCHAR(100) - Banner tipi (tepe-banner, slider, vs.)\n";
        echo "   • html_template TEXT - HTML şablonu\n";
        echo "   • css_class VARCHAR(255) - CSS sınıfı\n";
        echo "   • is_active TINYINT(1) DEFAULT 1\n";
        echo "   • created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n";
        echo "   • updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n\n";
        
        echo "2. 📁 BANNER_GROUPS Tablosu İyileştirmeleri:\n";
        echo "   • name VARCHAR(255) - Group adı\n";
        echo "   • position VARCHAR(100) - Pozisyon (tepe, orta, alt)\n";
        echo "   • order_index INT - Sıralama\n";
        echo "   • is_active TINYINT(1) DEFAULT 1\n";
        echo "   • created_at, updated_at sütunları\n\n";
        
        echo "3. 🖼️  BANNERS Tablosu İyileştirmeleri:\n";
        echo "   • title VARCHAR(255) - Banner başlığı\n";
        echo "   • content TEXT - Banner içeriği\n";
        echo "   • image_path VARCHAR(500) - Görsel yolu\n";
        echo "   • link_url VARCHAR(500) - Hedef URL\n";
        echo "   • button_text VARCHAR(100) - Buton metni\n";
        echo "   • type_id INT - banner_layouts foreign key\n";
        echo "   • group_id INT - banner_groups foreign key\n";
        echo "   • page_id INT NULL - Belirli sayfa için\n";
        echo "   • category_id INT NULL - Belirli kategori için\n";
        echo "   • order_index INT DEFAULT 0\n";
        echo "   • is_active TINYINT(1) DEFAULT 1\n";
        echo "   • start_date DATE NULL\n";
        echo "   • end_date DATE NULL\n";
        echo "   • created_at, updated_at sütunları\n\n";
    }
}

// Script çalıştırma
if (basename($_SERVER['PHP_SELF']) === 'BannerTableAnalyzer.php') {
    $analyzer = new BannerTableAnalyzer();
    $analyzer->analyzeTables();
    $analyzer->suggestStandardization();
    echo "=== ANALİZ TAMAMLANDI ===\n";
}
