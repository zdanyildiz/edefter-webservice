<?php
// Tests/System/CheckJobsTable.php
// language_copy_jobs tablosunun şemasını kontrol eden script

include_once 'GetLocalDatabaseInfo.php';

// Database bağlantısı kur
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== LANGUAGE_COPY_JOBS TABLO ŞEMASI ===\n\n";

try {
    $sql = "DESCRIBE language_copy_jobs";
    $schema = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Tablo Şeması:\n";
    echo "=============\n";
    foreach ($schema as $column) {
        echo "{$column['Field']} | {$column['Type']} | Null: {$column['Null']} | Default: {$column['Default']}\n";
    }
    
    echo "\nÖrnek Veriler:\n";
    echo "==============\n";
    $sql = "SELECT * FROM language_copy_jobs ORDER BY id DESC LIMIT 3";
    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($data as $row) {
        echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
?>
