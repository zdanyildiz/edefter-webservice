<?php
/**
 * Tests Ana Index Dosyası
 * 
 * Bu dosya tüm test dosyalarında include edilir
 * Gerekli sınıfları ve yardımcı fonksiyonları yükler
 * 
 * Kullanım:
 * include_once __DIR__ . '/index.php';
 * 
 * @author GitHub Copilot
 * @date 24 Haziran 2025
 */

// Hata raporlamasını aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Proje kök dizinini belirle
define('PROJECT_ROOT', dirname(__DIR__));
define('TESTS_ROOT', __DIR__);

// Test yardımcı sınıflarını yükle
require_once TESTS_ROOT . '/Database/TestDatabase.php';
require_once TESTS_ROOT . '/TestModel/TestLogger.php';
require_once TESTS_ROOT . '/TestModel/TestValidator.php';
require_once TESTS_ROOT . '/TestModel/TestDataGenerator.php';
require_once TESTS_ROOT . '/TestModel/TestAssert.php';

// Sistem test yardımcılarını yükle
require_once TESTS_ROOT . '/System/GetLocalDatabaseInfo.php';

// Test yardımcı fonksiyonları
class TestHelper {
    
    private static $currentTest = null;
    
    /**
     * Test başlığı yazdır
     */
    public static function printTestHeader($testName) {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "TEST: " . $testName . "\n";
        echo str_repeat("=", 60) . "\n";
        
        // Logger'a da kaydet
        TestLogger::testStart($testName);
        TestAssert::resetCounters();
    }
    
    /**
     * Test sonucu yazdır
     */
    public static function printTestResult($success, $message = '') {
        if ($success) {
            echo "✅ BAŞARILI: " . $message . "\n";
            TestLogger::success($message);
        } else {
            echo "❌ BAŞARISIZ: " . $message . "\n";
            TestLogger::error($message);
        }
    }
    
    /**
     * Test bitişi özeti
     */
    public static function printTestSummary($testName) {
        $stats = TestAssert::getStats();
        $success = $stats['failed'] === 0;
        
        echo "\n" . str_repeat("-", 60) . "\n";
        echo "TEST ÖZET: $testName\n";
        echo str_repeat("-", 60) . "\n";
        echo "Toplam Assertion: " . $stats['total'] . "\n";
        echo "Başarılı: " . $stats['passed'] . " ✅\n";
        echo "Başarısız: " . $stats['failed'] . " ❌\n";
        echo str_repeat("-", 60) . "\n";
        
        TestLogger::testEnd($testName, $success);
        return $success;
    }
    
    /**
     * Test bilgisi yazdır
     */
    public static function printTestInfo($info) {
        echo "ℹ️  INFO: " . $info . "\n";
    }
    
    /**
     * Test uyarısı yazdır
     */
    public static function printTestWarning($warning) {
        echo "⚠️  UYARI: " . $warning . "\n";
    }
    
    /**
     * Test veritabanı bağlantısı al
     */
    public static function getTestDatabase() {
        try {
            return new TestDatabase();
        } catch (Exception $e) {
            self::printTestResult(false, "Veritabanı bağlantısı kurulamadı: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Tablo varlığını kontrol et
     */
    public static function checkTableExists($tableName, $db = null) {
        if (!$db) {
            $db = self::getTestDatabase();
        }
        
        if (!$db) {
            return false;
        }
        
        try {
            $stmt = $db->query("SHOW TABLES LIKE '$tableName'");
            $result = $stmt->fetch();
            return !empty($result);
        } catch (Exception $e) {
            self::printTestWarning("Tablo kontrolü başarısız: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sütun varlığını kontrol et
     */
    public static function checkColumnExists($tableName, $columnName, $db = null) {
        if (!$db) {
            $db = self::getTestDatabase();
        }
        
        if (!$db) {
            return false;
        }
        
        try {
            $stmt = $db->query("SHOW COLUMNS FROM $tableName LIKE '$columnName'");
            $result = $stmt->fetch();
            return !empty($result);
        } catch (Exception $e) {
            self::printTestWarning("Sütun kontrolü başarısız: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test tablosu oluştur (geçici test verileri için)
     */
    public static function createTestTable($tableName, $structure, $db = null) {
        if (!$db) {
            $db = self::getTestDatabase();
        }
        
        if (!$db) {
            return false;
        }
        
        try {
            // Önce tabloyu sil (varsa)
            $db->exec("DROP TABLE IF EXISTS $tableName");
            
            // Yeni tabloyu oluştur
            $db->exec($structure);
            
            self::printTestInfo("Test tablosu '$tableName' oluşturuldu");
            return true;
        } catch (Exception $e) {
            self::printTestResult(false, "Test tablosu oluşturulamadı: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test tablosunu temizle
     */
    public static function cleanupTestTable($tableName, $db = null) {
        if (!$db) {
            $db = self::getTestDatabase();
        }
        
        if (!$db) {
            return false;
        }
        
        try {
            $db->exec("DROP TABLE IF EXISTS $tableName");
            self::printTestInfo("Test tablosu '$tableName' temizlendi");
            return true;
        } catch (Exception $e) {
            self::printTestWarning("Test tablosu temizlenemedi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test verilerini ekle
     */
    public static function insertTestData($tableName, $data, $db = null) {
        if (!$db) {
            $db = self::getTestDatabase();
        }
        
        if (!$db || empty($data)) {
            return false;
        }
        
        try {
            $columns = array_keys($data[0]);
            $placeholders = ':' . implode(', :', $columns);
            $columnList = implode(', ', $columns);
            
            $sql = "INSERT INTO $tableName ($columnList) VALUES ($placeholders)";
            $stmt = $db->prepare($sql);
            
            $insertedCount = 0;
            foreach ($data as $row) {
                if ($stmt->execute($row)) {
                    $insertedCount++;
                }
            }
            
            self::printTestInfo("$insertedCount test verisi '$tableName' tablosuna eklendi");
            return true;
        } catch (Exception $e) {
            self::printTestResult(false, "Test verisi eklenemedi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test başlat
     */
    public static function startTest($testName) {
        self::$currentTest = $testName;
        self::printTestHeader($testName);
    }
    
    /**
     * Test bitir
     */
    public static function endTest() {
        if (self::$currentTest) {
            self::printTestSummary(self::$currentTest);
            self::$currentTest = null;
        }
    }
    
    /**
     * Başarı mesajı
     */
    public static function success($message) {
        self::printTestResult(true, $message);
    }
    
    /**
     * Hata mesajı
     */
    public static function error($message) {
        self::printTestResult(false, $message);
    }
    
    /**
     * Bilgi mesajı
     */
    public static function info($message) {
        self::printTestInfo($message);
    }
}

// Test başlangıç mesajı (sadece CLI modunda)
if (!defined('TEST_INDEX_LOADED') && !isset($_SERVER['HTTP_HOST'])) {
    define('TEST_INDEX_LOADED', true);
    echo "\n📋 Test Framework Yüklendi - " . date('d.m.Y H:i:s') . "\n";
    echo "🔧 Proje Kök: " . PROJECT_ROOT . "\n";
    echo "🧪 Test Kök: " . TESTS_ROOT . "\n";
    
    // Yüklenen sınıflar
    echo "📚 Yüklenen Test Sınıfları:\n";
    echo "   - TestDatabase (Veritabanı işlemleri)\n";
    echo "   - TestLogger (Log yönetimi)\n";
    echo "   - TestValidator (Veri doğrulama)\n";
    echo "   - TestDataGenerator (Test verisi üretimi)\n";    echo "   - TestAssert (Assertion kontrolü)\n";
    echo "   - TestHelper (Yardımcı fonksiyonlar)\n";
    
    // Veritabanı bağlantısını test et (sadece CLI modunda)
    try {
        $testDb = new TestDatabase();
        TestHelper::printTestResult(true, "Test veritabanı bağlantısı kuruldu");
        TestLogger::info("Test veritabanı bağlantısı başarılı");
    } catch (Exception $e) {
        TestHelper::printTestResult(false, "Test veritabanı bağlantısı kurulamadı: " . $e->getMessage());
        TestLogger::error("Test veritabanı bağlantı hatası", ['error' => $e->getMessage()]);
    }
    echo "\n";
} elseif (!defined('TEST_INDEX_LOADED')) {
    // Web modunda sadece tanımlayıcıyı set et
    define('TEST_INDEX_LOADED', true);
}
