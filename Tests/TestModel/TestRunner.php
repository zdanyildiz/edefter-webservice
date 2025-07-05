<?php
/**
 * Ana Test Runner
 * Tüm testleri tek yerden çalıştırmak için
 */

// CLI ortamı için gerekli tanımlar
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);

echo "🧪 PROJE TEST SİSTEMİ\n";
echo "======================\n";
echo "Tarih: " . date('Y-m-d H:i:s') . "\n\n";

// Test dosyalarını include et
require_once 'Database/DatabaseTester.php';
require_once 'Banners/BannerTester.php';

/**
 * Ana test runner sınıfı
 */
class TestRunner 
{
    private $tests = [];
    
    public function __construct()
    {
        $this->tests = [
            'database' => new DatabaseTester(),
            'banner' => new BannerTester()
        ];
    }
    
    /**
     * Belirli bir testi çalıştır
     */
    public function runTest($testName)
    {
        if (isset($this->tests[$testName])) {
            echo "🏃‍♂️ {$testName} testi çalıştırılıyor...\n\n";
            $this->tests[$testName]->runAllTests();
            echo "\n" . str_repeat("-", 50) . "\n\n";
        } else {
            echo "❌ Test bulunamadı: {$testName}\n";
        }
    }
    
    /**
     * Tüm testleri çalıştır
     */
    public function runAllTests()
    {
        foreach ($this->tests as $testName => $testInstance) {
            $this->runTest($testName);
        }
        
        echo "🎊 TÜM TESTLER TAMAMLANDI!\n";
    }
    
    /**
     * Kullanılabilir testleri listele
     */
    public function listTests()
    {
        echo "📋 Kullanılabilir Testler:\n";
        foreach (array_keys($this->tests) as $testName) {
            echo "- {$testName}\n";
        }
    }
}

// Komut satırı argümanlarını kontrol et
if (isset($argv[1])) {
    $testRunner = new TestRunner();
    
    switch ($argv[1]) {
        case 'list':
            $testRunner->listTests();
            break;
        case 'all':
            $testRunner->runAllTests();
            break;
        default:
            $testRunner->runTest($argv[1]);
            break;
    }
} else {
    echo "Kullanım:\n";
    echo "php TestRunner.php [test_adı|all|list]\n\n";
    echo "Örnekler:\n";
    echo "php TestRunner.php all       # Tüm testleri çalıştır\n";
    echo "php TestRunner.php database  # Sadece veritabanı testini çalıştır\n";
    echo "php TestRunner.php banner    # Sadece banner testini çalıştır\n";
    echo "php TestRunner.php list      # Kullanılabilir testleri listele\n";
}
?>
