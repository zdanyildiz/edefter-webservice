<?php
// Sayfa-dil ilişkisi analizi
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);

    echo "=== SAYFA-DİL İLİŞKİSİ ANALİZİ ===\n";

    // language_page_mapping tablosu
    echo "language_page_mapping tablosu:\n";
    $stmt = $pdo->query("SELECT * FROM language_page_mapping LIMIT 3");
    $mappings = $stmt->fetchAll();
    foreach ($mappings as $mapping) {
        echo "  - ID: {$mapping['id']}, Original: {$mapping['original_page_id']}, Translated: {$mapping['translated_page_id']}, DilID: {$mapping['dilid']}\n";
    }

    // Sayfa tablosunda benzersizid ile ilişkili sayfalar
    echo "\nSayfa tablosundan örnekler:\n";
    $stmt = $pdo->query("SELECT sayfaid, sayfaad, benzersizid FROM sayfa LIMIT 3");
    $pages = $stmt->fetchAll();
    foreach ($pages as $page) {
        echo "  - ID: {$page['sayfaid']}, Ad: {$page['sayfaad']}, Benzersiz: {$page['benzersizid']}\n";
    }

    // Hangi sayfalar hangi dillerle ilişkili
    echo "\nDilid 3, 4, 5, 6 ile ilişkili sayfalar:\n";
    $stmt = $pdo->query("
        SELECT DISTINCT lpm.dilid, COUNT(*) as sayfa_sayisi
        FROM language_page_mapping lpm
        WHERE lpm.dilid IN (3, 4, 5, 6)
        GROUP BY lpm.dilid
    ");
    $dilSayilar = $stmt->fetchAll();
    foreach ($dilSayilar as $dil) {
        echo "  - DilID {$dil['dilid']}: {$dil['sayfa_sayisi']} sayfa\n";
    }

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
