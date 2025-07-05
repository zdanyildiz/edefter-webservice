<?php
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();

$pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", 
               $dbInfo['username'], $dbInfo['password']);

echo "=== DİL TABLOSU YAPISI ===\n";
$stmt = $pdo->query('DESCRIBE dil');
while($row = $stmt->fetch()) {
    echo $row['Field'] . ' | ' . $row['Type'] . "\n";
}

echo "\n=== ÖRNEK DİL KAYITLARI ===\n";
$stmt = $pdo->query('SELECT * FROM dil LIMIT 3');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n=== SAYFA TABLOSU YAPISI ===\n";
$stmt = $pdo->query('DESCRIBE sayfa');
while($row = $stmt->fetch()) {
    echo $row['Field'] . ' | ' . $row['Type'] . "\n";
}
?>
