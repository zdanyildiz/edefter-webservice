<?php
/**
 * Test Database Sınıfı
 * 
 * Ana proje veritabanına müdahale etmeden test işlemleri için kullanılır
 * Güvenli şekilde veritabanı bağlantısı sağlar
 * 
 * @author GitHub Copilot
 * @date 24 Haziran 2025
 */

class TestDatabase extends PDO {
    
    private static $instance = null;
    private $host;
    private $database;
    private $username;
    private $password;
    private $charset = 'utf8mb4';
    
    /**
     * Singleton getInstance metodu
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Veritabanı bağlantısını kur
     */
    public function __construct() {
        $this->loadDatabaseConfig();
        
        $dsn = "mysql:host=$this->host;dbname=$this->database;charset=$this->charset";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            parent::__construct($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new Exception("Test veritabanı bağlantısı kurulamadı: " . $e->getMessage());
        }
    }
      /**
     * Veritabanı konfigürasyonunu yükle
     */
    private function loadDatabaseConfig() {
        // Eğer getLocalDatabaseInfo fonksiyonu mevcutsa onu kullan
        if (function_exists('getLocalDatabaseInfo')) {
            try {
                $dbInfo = getLocalDatabaseInfo();
                $this->host = $dbInfo['serverName'];
                $this->database = $dbInfo['database'];
                $this->username = $dbInfo['username'];
                $this->password = $dbInfo['password'];
                return;
            } catch (Exception $e) {
                // Hata durumunda default ayarlara geç
            }
        }
        
        // Fallback: Manuel ayarlar
        $this->setDefaultConfig();
    }
      /**
     * Default veritabanı ayarlarını kullan
     */
    private function setDefaultConfig() {
        $this->host = 'localhost';
        $this->database = 'test_db';
        $this->username = 'root';
        $this->password = '';
    }
    
    /**
     * Güvenli sorgu çalıştır
     */
    public function safeQuery($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Sorgu hatası: " . $e->getMessage() . " | SQL: " . $sql);
        }
    }
    
    /**
     * Tablo var mı kontrol et
     */
    public function tableExists($tableName) {
        try {
            $stmt = $this->safeQuery("SHOW TABLES LIKE ?", [$tableName]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Sütun var mı kontrol et
     */
    public function columnExists($tableName, $columnName) {
        try {
            $stmt = $this->safeQuery("SHOW COLUMNS FROM `$tableName` LIKE ?", [$columnName]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Tablo bilgilerini al
     */
    public function getTableInfo($tableName) {
        try {
            $stmt = $this->safeQuery("DESCRIBE `$tableName`");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Test verileri ekle (bulk insert)
     */
    public function insertTestData($tableName, $data) {
        if (empty($data)) {
            return false;
        }
        
        try {
            $columns = array_keys($data[0]);
            $placeholders = ':' . implode(', :', $columns);
            $columnList = '`' . implode('`, `', $columns) . '`';
            
            $sql = "INSERT INTO `$tableName` ($columnList) VALUES ($placeholders)";
            $stmt = $this->prepare($sql);
            
            $this->beginTransaction();
            
            foreach ($data as $row) {
                $stmt->execute($row);
            }
            
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            throw new Exception("Test verisi eklenemedi: " . $e->getMessage());
        }
    }
    
    /**
     * Test tablosunu temizle
     */
    public function truncateTable($tableName) {
        try {
            $this->exec("TRUNCATE TABLE `$tableName`");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Test tablosunu sil
     */
    public function dropTable($tableName) {
        try {
            $this->exec("DROP TABLE IF EXISTS `$tableName`");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * PDO bağlantısını döndür
     */
    public function getConnection() {
        return $this;
    }

    /**
     * Belirli bir tablonun CREATE TABLE ifadesini döndürür.
     */
    public function getCreateTableStatement($tableName): ?string
    {
        try {
            $stmt = $this->query("SHOW CREATE TABLE `{$tableName}`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['Create Table'] ?? null;
        } catch (Exception $e) {
            error_log("CREATE TABLE ifadesi alınamadı for {$tableName}: " . $e->getMessage());
            return null;
        }
    }
}
