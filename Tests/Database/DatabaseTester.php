<?php
/**
 * Veritabanı Bağlantı Test Dosyası
 * Tests/Database/ dizini için
 */

class DatabaseTester 
{
    private $connectionParams;
    
    public function __construct() 
    {
        $this->connectionParams = [
            'host' => 'localhost',
            'database' => 'yeni.globalpozitif.com.tr', 
            'username' => 'root',
            'password' => 'Global2019*'
        ];
    }
    
    /**
     * Temel bağlantı testi
     */
    public function testConnection()
    {
        echo "🔌 Veritabanı Bağlantı Testi\n";
        echo "============================\n";
        
        try {
            $pdo = new PDO(
                "mysql:host={$this->connectionParams['host']};dbname={$this->connectionParams['database']};charset=utf8",
                $this->connectionParams['username'],
                $this->connectionParams['password']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "✅ Bağlantı başarılı: {$this->connectionParams['database']}\n";
            return $pdo;
            
        } catch (PDOException $e) {
            echo "❌ Bağlantı hatası: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    /**
     * Banner tablolarını kontrol et
     */
    public function checkBannerTables($pdo)
    {
        echo "\n📊 Banner Tabloları Kontrolü\n";
        echo "=============================\n";
        
        $tables = ['banner_types', 'banner_layouts', 'banner_groups', 'banners'];
        
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo "✅ {$table}: {$result['count']} kayıt\n";
                
            } catch (PDOException $e) {
                echo "❌ {$table}: Tablo bulunamadı veya hata\n";
            }
        }
    }
    
    /**
     * Banner verilerinin tutarlılığını kontrol et
     */
    public function checkDataIntegrity($pdo)
    {
        echo "\n🔍 Veri Tutarlılık Kontrolü\n";
        echo "===========================\n";
        
        try {
            // Foreign key kontrolü: banner_layouts -> banner_types
            $stmt = $pdo->query("
                SELECT bl.id, bl.layout_name, bl.type_id 
                FROM banner_layouts bl 
                LEFT JOIN banner_types bt ON bl.type_id = bt.id 
                WHERE bt.id IS NULL
            ");
            
            $orphanLayouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($orphanLayouts)) {
                echo "✅ banner_layouts -> banner_types referansları OK\n";
            } else {
                echo "❌ Orphan layout'lar bulundu: " . count($orphanLayouts) . "\n";
            }
            
        } catch (PDOException $e) {
            echo "❌ Tutarlılık kontrolü hatası: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Tüm testleri çalıştır
     */
    public function runAllTests()
    {
        $pdo = $this->testConnection();
        
        if ($pdo) {
            $this->checkBannerTables($pdo);
            $this->checkDataIntegrity($pdo);
            
            echo "\n🎉 Veritabanı testleri tamamlandı!\n";
        }
    }
}

// Script doğrudan çalıştırılırsa testleri başlat
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $tester = new DatabaseTester();
    $tester->runAllTests();
}
?>
