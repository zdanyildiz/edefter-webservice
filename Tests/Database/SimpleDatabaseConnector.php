<?php
/**
 * Simple Database Connector - CLI için basitleştirilmiş veritabanı bağlantısı
 */

class SimpleDatabaseConnector {
    private $pdo;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            // Config dosyalarını doğrudan okuyalım
            $projectRoot = dirname(__DIR__);
            
            // Key dosyasını oku
            include $projectRoot . '/App/Config/Key.php';
            
            // SQL dosyasını oku
            include $projectRoot . '/App/Config/Sql.php';
            
            // Helper sınıfını yükle
            include $projectRoot . '/App/Helpers/Helper.php';
            $helper = new Helper();
            
            // Localhost bilgilerini çöz (l. ile başlayan domainler için)
            $host = $helper->decrypt($dbLocalServerName, $key);
            $username = $helper->decrypt($dbLocalUsername, $key);
            $password = $helper->decrypt($dbLocalPassword, $key);
            $database = $helper->decrypt($dbLocalName, $key);
            
            $dsn = "mysql:host={$host};dbname={$database};charset=utf8";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "✅ Veritabanı bağlantısı başarılı\n";
            echo "📊 Host: {$host}\n";
            echo "📊 Database: {$database}\n";
            echo "-----------------------------------\n\n";
            
        } catch (Exception $e) {
            echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function getPdo() {
        return $this->pdo;
    }
    
    public function listTables() {
        try {
            $sql = "SHOW TABLES";
            $stmt = $this->pdo->query($sql);
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $tables;
        } catch (PDOException $e) {
            echo "⚠️ Tablolar sorgulanamadı: " . $e->getMessage() . "\n";
            return [];
        }
    }
    
    public function getTableStructure($tableName) {
        try {
            $sql = "DESCRIBE `{$tableName}`";
            $stmt = $this->pdo->query($sql);
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $columns;
        } catch (PDOException $e) {
            echo "⚠️ Tablo yapısı alınamadı: {$tableName} - " . $e->getMessage() . "\n";
            return [];
        }
    }
}

// CLI testi
if (php_sapi_name() === 'cli' && isset($argv[0]) && basename($argv[0]) === 'SimpleDatabaseConnector.php') {
    echo "🔍 Veritabanı Bağlantı Testi\n";
    echo "============================\n";
    
    $db = new SimpleDatabaseConnector();
    $tables = $db->listTables();
    
    echo "📋 Veritabanındaki tablolar (" . count($tables) . " adet):\n";
    foreach ($tables as $table) {
        echo "  • {$table}\n";
    }
    
    // Birkaç önemli tablonun yapısını göster
    $importantTables = ['banners', 'products', 'members', 'orders'];
    foreach ($importantTables as $table) {
        if (in_array($table, $tables)) {
            echo "\n🔍 {$table} tablosu yapısı:\n";
            $structure = $db->getTableStructure($table);
            foreach ($structure as $column) {
                echo "   {$column['Field']} | {$column['Type']} | {$column['Null']} | {$column['Key']}\n";
            }
        }
    }
}
