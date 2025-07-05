<?php
// Kategori mapping analizi
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);

echo "=== KATEGORİ MAPPING ANALİZİ ===\n";
$stmt = $pdo->query("
    SELECT 
        lcm.id, lcm.original_category_id, lcm.translated_category_id, lcm.dilid,
        k1.kategoriad as original_name, k1.dilid as original_lang,
        k2.kategoriad as translated_name, k2.dilid as translated_lang
    FROM language_category_mapping lcm
    LEFT JOIN kategori k1 ON lcm.original_category_id = k1.kategoriid
    LEFT JOIN kategori k2 ON lcm.translated_category_id = k2.kategoriid
    ORDER BY lcm.id DESC
");
$mappings = $stmt->fetchAll();
foreach ($mappings as $m) {
    echo "ID: {$m['id']}, Original: {$m['original_name']} (Lang: {$m['original_lang']}) -> Translated: {$m['translated_name']} (Lang: {$m['translated_lang']}) | Target Lang: {$m['dilid']}\n";
}

echo "\n=== TÜM KATEGORİLER DİL DAĞILIMI ===\n";
$stmt = $pdo->query("SELECT dilid, COUNT(*) as count FROM kategori WHERE kategorisil = 0 GROUP BY dilid ORDER BY dilid");
$categoryLangs = $stmt->fetchAll();
foreach ($categoryLangs as $cl) {
    echo "Dil ID {$cl['dilid']}: {$cl['count']} kategori\n";
}
?>
