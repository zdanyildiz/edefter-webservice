<?php
include_once 'GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", $dbInfo['username'], $dbInfo['password']);

echo "=== SAYFA TABLOSU YAPISI ===" . PHP_EOL;
$stmt = $pdo->query('DESCRIBE sayfa');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' | ' . $row['Type'] . PHP_EOL;
}

echo PHP_EOL . "=== DİL TABLOSU YAPISI ===" . PHP_EOL;
$stmt = $pdo->query('DESCRIBE dil');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' | ' . $row['Type'] . PHP_EOL;
}
?>
