<?php
// Tests/System/CheckMappingTables.php
// Mapping tablolarının şemasını kontrol eden script

include_once 'GetLocalDatabaseInfo.php';

// Database bağlantısı kur
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== MAPPING TABLOLARI ŞEMA KONTROLÜ ===\n\n";

$tables = ['language_category_mapping', 'language_page_mapping'];

foreach ($tables as $tableName) {
    echo "TABLO: {$tableName}\n";
    echo str_repeat("=", strlen($tableName) + 7) . "\n";
    
    try {
        $sql = "DESCRIBE {$tableName}";
        $schema = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Şema:\n";
        foreach ($schema as $column) {
            echo "  {$column['Field']} | {$column['Type']} | Null: {$column['Null']}\n";
        }
        
        echo "\nÖrnek Veriler:\n";
        $sql = "SELECT * FROM {$tableName} LIMIT 3";
        $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($data as $row) {
            echo "  " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Hata: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}
?>
