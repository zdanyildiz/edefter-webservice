<?php
/**
 * Banner Manager Test Dosyası
 * Tests/Banners/ dizini için ana test sınıfı
 */

// CLI ortamı için gerekli tanımlar
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));

// Ana proje dosyalarını include et
require_once dirname(dirname(__DIR__)) . '/App/Database/Database.php';
require_once dirname(dirname(__DIR__)) . '/App/Core/Config.php';
require_once dirname(dirname(__DIR__)) . '/App/Core/BannerManager.php';
require_once dirname(dirname(__DIR__)) . '/App/Controller/BannerController.php';

class BannerTester 
{
    private $db;
    private $config;
    private $bannerManager;
    
    public function __construct() 
    {
        echo "🚀 Banner Test Sistemi Başlatılıyor...\n";
        
        try {
            // Veritabanı bağlantısı
            $this->db = new Database('localhost', 'yeni.globalpozitif.com.tr', 'root', 'Global2019*');
            echo "✅ Veritabanı bağlantısı başarılı\n";
            
            // BannerManager başlat
            $this->bannerManager = BannerManager::getInstance();
            echo "✅ BannerManager başlatıldı\n";
            
        } catch (Exception $e) {
            echo "❌ Hata: " . $e->getMessage() . "\n";
            exit;
        }
    }
    
    /**
     * Banner Manager singleton test
     */
    public function testSingleton()
    {
        echo "\n📋 Singleton Test:\n";
        
        $manager1 = BannerManager::getInstance();
        $manager2 = BannerManager::getInstance();
        
        if ($manager1 === $manager2) {
            echo "✅ Singleton pattern çalışıyor\n";
        } else {
            echo "❌ Singleton pattern hatası\n";
        }
    }
      /**
     * Banner verilerini çekme testi
     */
    public function testBannerData()
    {
        echo "\n📋 Banner Veri Testi:\n";
        
        try {
            // banner_layouts tablosunu test et
            $layouts = $this->db->select("SELECT COUNT(*) as total FROM banner_layouts");
            $layoutCount = $layouts[0]['total'] ?? 0;
            
            echo "✅ Banner Layouts: {$layoutCount} kayıt\n";
            
            // banner_groups tablosunu test et (varsa)
            $groups = $this->db->select("SELECT COUNT(*) as total FROM banner_groups");
            $groupCount = $groups[0]['total'] ?? 0;
            
            echo "✅ Banner Groups: {$groupCount} kayıt\n";
            
        } catch (Exception $e) {
            echo "❌ Veri testi hatası: " . $e->getMessage() . "\n";
        }
    }
      /**
     * Cache sistemini test et
     */
    public function testCache()
    {
        echo "\n📋 Cache Test:\n";
        
        // Mock banner verileri
        $mockBannerInfo = [
            [
                'id' => 1,
                'type_id' => 1,
                'page_id' => null,
                'category_id' => null,
                'layout_name' => 'Test Layout'
            ]
        ];
        
        // Gerçek Casper sınıfını include et ve mock oluştur
        require_once dirname(dirname(__DIR__)) . '/App/Core/Casper.php';
        
        // Mock Casper nesnesi oluştur
        $mockCasper = new Casper();
        
        try {
            // BannerManager'ı başlat
            $this->bannerManager->initialize($mockBannerInfo, $mockCasper);
            echo "✅ Cache başlatıldı\n";
            
            // Cache temizleme testi
            $this->bannerManager->clearCache();
            echo "✅ Cache temizlendi\n";
            
        } catch (Exception $e) {
            echo "❌ Cache testi hatası: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Tüm testleri çalıştır
     */
    public function runAllTests()
    {
        echo "🧪 BANNER TESt SİSTEMİ\n";
        echo "========================\n";
        
        $this->testSingleton();
        $this->testBannerData();
        $this->testCache();
        
        echo "\n🎉 Tüm testler tamamlandı!\n";
    }
}

// Script doğrudan çalıştırılırsa testleri başlat
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $tester = new BannerTester();
    $tester->runAllTests();
}
?>
