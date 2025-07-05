<?php
/**
 * Veritabanı Tablo ve Sütun Kontrol Fonksiyonları
 * Bu dosya, geliştirme sırasında tablo yapılarını kontrol etmek için kullanılır
 * 
 * Kullanım Örnekleri:
 * include_once 'Tests/System/GetTableInfo.php';
 * 
 * // Tek tablo kontrolü
 * $tableInfo = getTableInfo('sayfa');
 * 
 * // Çoklu tablo kontrolü  
 * $tablesInfo = getMultipleTablesInfo(['sayfa', 'dil', 'language_page_mapping']);
 * 
 * // Tablo varlık kontrolü
 * $exists = checkTableExists('language_page_mapping');
 * 
 * // Sütun varlık kontrolü
 * $hasColumn = checkColumnExists('sayfa', 'sayfaad');
 */

require_once 'GetLocalDatabaseInfo.php';

/**
 * Veritabanı bağlantısını getirir
 * @return PDO
 */
function getDatabaseConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dbInfo = getLocalDatabaseInfo();
        $pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", 
                       $dbInfo['username'], $dbInfo['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    return $pdo;
}

/**
 * Belirli bir tablonun yapısını getirir
 * @param string $tableName Tablo adı
 * @return array|false Tablo yapısı veya false
 */
function getTableInfo($tableName) {
    try {
        $pdo = getDatabaseConnection();
        
        // Tablo var mı kontrol et
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tableName]);
        
        if (!$stmt->fetch()) {
            return [
                'exists' => false,
                'table_name' => $tableName,
                'error' => 'Tablo bulunamadı'
            ];
        }
        
        // Tablo yapısını al
        $stmt = $pdo->prepare("DESCRIBE `$tableName`");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'exists' => true,
            'table_name' => $tableName,
            'columns' => $columns,
            'column_count' => count($columns)
        ];
        
    } catch (PDOException $e) {
        return [
            'exists' => false,
            'table_name' => $tableName,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Çoklu tablo bilgilerini getirir
 * @param array $tableNames Tablo adları
 * @return array Tablo bilgileri
 */
function getMultipleTablesInfo($tableNames) {
    $results = [];
    
    foreach ($tableNames as $tableName) {
        $results[$tableName] = getTableInfo($tableName);
    }
    
    return $results;
}

/**
 * Tablonun var olup olmadığını kontrol eder
 * @param string $tableName Tablo adı
 * @return bool
 */
function checkTableExists($tableName) {
    $tableInfo = getTableInfo($tableName);
    return $tableInfo['exists'] ?? false;
}

/**
 * Belirli bir sütunun tabloda var olup olmadığını kontrol eder
 * @param string $tableName Tablo adı
 * @param string $columnName Sütun adı
 * @return bool
 */
function checkColumnExists($tableName, $columnName) {
    $tableInfo = getTableInfo($tableName);
    
    if (!$tableInfo['exists']) {
        return false;
    }
    
    foreach ($tableInfo['columns'] as $column) {
        if ($column['Field'] === $columnName) {
            return true;
        }
    }
    
    return false;
}

/**
 * Tablonun sütun listesini basit array olarak döner
 * @param string $tableName Tablo adı
 * @return array Sütun adları
 */
function getTableColumns($tableName) {
    $tableInfo = getTableInfo($tableName);
    
    if (!$tableInfo['exists']) {
        return [];
    }
    
    return array_column($tableInfo['columns'], 'Field');
}

/**
 * Tablo bilgilerini formatlı şekilde yazdırır
 * @param string $tableName Tablo adı
 * @param bool $showDetails Detayları göster
 */
function printTableInfo($tableName, $showDetails = true) {
    $tableInfo = getTableInfo($tableName);
    
    echo "=== TABLO: $tableName ===" . PHP_EOL;
    
    if (!$tableInfo['exists']) {
        echo "❌ Tablo bulunamadı!" . PHP_EOL;
        if (isset($tableInfo['error'])) {
            echo "Hata: {$tableInfo['error']}" . PHP_EOL;
        }
        return;
    }
    
    echo "✅ Tablo mevcut" . PHP_EOL;
    echo "Sütun Sayısı: {$tableInfo['column_count']}" . PHP_EOL;
    
    if ($showDetails) {
        echo PHP_EOL . "SÜTUNLAR:" . PHP_EOL;
        echo str_pad("Sütun Adı", 25) . str_pad("Tip", 20) . str_pad("Null", 8) . str_pad("Key", 8) . "Default" . PHP_EOL;
        echo str_repeat("-", 70) . PHP_EOL;
        
        foreach ($tableInfo['columns'] as $column) {
            echo str_pad($column['Field'], 25) . 
                 str_pad($column['Type'], 20) . 
                 str_pad($column['Null'], 8) . 
                 str_pad($column['Key'], 8) . 
                 ($column['Default'] ?? 'NULL') . PHP_EOL;
        }
    }
    
    echo PHP_EOL;
}

/**
 * Çoklu tablo bilgilerini formatlı şekilde yazdırır
 * @param array $tableNames Tablo adları
 * @param bool $showDetails Detayları göster
 */
function printMultipleTablesInfo($tableNames, $showDetails = false) {
    echo "=== ÇOKLU TABLO KONTROLÜ ===" . PHP_EOL;
    echo "Tarih: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
    
    foreach ($tableNames as $tableName) {
        printTableInfo($tableName, $showDetails);
    }
    
    echo "✅ Kontrol tamamlandı!" . PHP_EOL;
}

/**
 * CLI kullanımı için ana fonksiyon
 */
function main() {
    // Komut satırı argümanlarını kontrol et
    global $argv;
    
    if (count($argv) < 2) {
        echo "Kullanım: php GetTableInfo.php <tablo_adı> [detay]" . PHP_EOL;
        echo "Örnek: php GetTableInfo.php sayfa" . PHP_EOL;
        echo "Örnek: php GetTableInfo.php sayfa detail" . PHP_EOL;
        return;
    }
    
    $tableName = $argv[1];
    $showDetails = isset($argv[2]) && $argv[2] === 'detail';
    
    printTableInfo($tableName, $showDetails);
}

// CLI'dan direkt çağrılırsa çalıştır
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    main();
}
?>
