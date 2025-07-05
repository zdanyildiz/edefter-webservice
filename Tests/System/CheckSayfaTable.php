<?php
// Tests/System/CheckSayfaTable.php
// Sayfa tablosunun şemasını kontrol eden script

include_once 'GetLocalDatabaseInfo.php';

// Database bağlantısı kur
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== SAYFA TABLOSU ŞEMA KONTROLÜ ===\n\n";

try {
    $sql = "DESCRIBE sayfa";
    $schema = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Sayfa Tablosu Şeması:\n";
    echo "=====================\n";
    foreach ($schema as $column) {
        echo "{$column['Field']} | {$column['Type']} | Null: {$column['Null']}\n";
    }
    
    echo "\nÖrnek Veri:\n";
    echo "===========\n";
    $sql = "SELECT * FROM sayfa LIMIT 1";
    $data = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        foreach ($data as $field => $value) {
            echo "{$field}: " . (strlen($value) > 50 ? substr($value, 0, 50) . "..." : $value) . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
?>
