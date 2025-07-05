<?php
// Tests/System/CheckDilTable.php
// Dil tablosunun şemasını kontrol eden script

include_once 'GetLocalDatabaseInfo.php';

// Database bağlantısı kur
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== DİL TABLOSU ŞEMA KONTROLÜ ===\n\n";

try {
    // Tablo var mı kontrol et
    $sql = "SHOW TABLES LIKE 'dil'";
    $result = $pdo->query($sql)->fetchAll();
    
    if (empty($result)) {
        echo "❌ 'dil' tablosu bulunamadı!\n";
        
        // Alternatif tablo isimlerini kontrol et
        $alternativeTables = ['language', 'languages', 'lang', 'dilim'];
        echo "\nAlternatif tablo isimleri kontrol ediliyor:\n";
        
        foreach ($alternativeTables as $tableName) {
            $sql = "SHOW TABLES LIKE '$tableName'";
            $result = $pdo->query($sql)->fetchAll();
            if (!empty($result)) {
                echo "✅ '{$tableName}' tablosu bulundu!\n";
                
                // Bu tablonun şemasını göster
                $sql = "DESCRIBE $tableName";
                $schema = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                echo "\n'{$tableName}' tablosu şeması:\n";
                foreach ($schema as $column) {
                    echo "- {$column['Field']} ({$column['Type']})\n";
                }
            }
        }
        exit();
    }
    
    echo "✅ 'dil' tablosu bulundu!\n\n";
    
    // Dil tablosunun şemasını göster
    $sql = "DESCRIBE dil";
    $schema = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    echo "DİL TABLOSU ŞEMA:\n";
    echo "==================\n";
    foreach ($schema as $column) {
        echo "{$column['Field']} | {$column['Type']} | Null: {$column['Null']} | Default: {$column['Default']}\n";
    }
    
    echo "\n";
    
    // Örnek veri göster
    $sql = "SELECT * FROM dil LIMIT 5";
    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    echo "ÖRNEK VERİLER:\n";
    echo "==============\n";
    foreach ($data as $row) {
        echo "Kayıt: " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
?>
