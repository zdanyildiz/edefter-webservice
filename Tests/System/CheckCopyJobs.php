<?php
// Copy job'ları kontrol et
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);

echo "=== LANGUAGE COPY JOBS ===\n";
$stmt = $pdo->query("SELECT * FROM language_copy_jobs ORDER BY id DESC LIMIT 5");
$jobs = $stmt->fetchAll();
foreach ($jobs as $job) {
    echo "ID: {$job['id']}, Source: {$job['source_language_id']}, Target: {$job['target_language_id']}, Status: {$job['status']}\n";
}

echo "\n=== ANA DİL KONTROLÜ ===\n";
$stmt = $pdo->query("SELECT dilid, dilad, anadil FROM dil WHERE dilaktif = 1 ORDER BY dilid");
$languages = $stmt->fetchAll();
foreach ($languages as $lang) {
    $anadil = $lang['anadil'] == 1 ? ' (ANA DİL)' : '';
    echo "ID: {$lang['dilid']}, Ad: {$lang['dilad']}{$anadil}\n";
}
?>
