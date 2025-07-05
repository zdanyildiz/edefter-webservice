<?php
/**
 * Banner Layout Debug Aracı
 * Layout bilgilerini detaylı kontrol eder
 */

$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';

$helper = new Helper();

// Database bağlantısı
global $key, $dbLocalServerName, $dbLocalUsername, $dbLocalPassword, $dbLocalName;

$decryptedHost = $helper->decrypt($dbLocalServerName, $key);
$decryptedUsername = $helper->decrypt($dbLocalUsername, $key);
$decryptedPassword = $helper->decrypt($dbLocalPassword, $key);
$decryptedDatabase = $helper->decrypt($dbLocalName, $key);

$pdo = new PDO(
    "mysql:host={$decryptedHost};dbname={$decryptedDatabase};charset=utf8mb4",
    $decryptedUsername,
    $decryptedPassword,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "=== BANNER LAYOUT DEBUG ===\n\n";

// Layout ID 3'ü detaylı kontrol et
$stmt = $pdo->prepare("SELECT * FROM banner_layouts WHERE id = 3");
$stmt->execute();
$layout = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Layout ID 3 bilgileri:\n";
foreach ($layout as $key => $value) {
    echo "  {$key}: {$value}\n";
}

echo "\n=== BANNER GRUP VE LAYOUT İLİŞKİSİ ===\n";

// Tepe banner grubu ve layout'unu join'le getir
$stmt = $pdo->prepare("
    SELECT 
        bg.id as group_id,
        bg.group_name,
        bg.layout_id,
        bg.style_class,
        bl.layout_group,
        bl.layout_view,
        bl.layout_name,
        bl.type_id
    FROM banner_groups bg
    LEFT JOIN banner_layouts bl ON bg.layout_id = bl.id
    WHERE bg.id = 2
");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Join sonucu:\n";
foreach ($result as $key => $value) {
    echo "  {$key}: {$value}\n";
}

echo "\n=== BANNER VERİLERİ ===\n";

$stmt = $pdo->prepare("SELECT * FROM banners WHERE group_id = 2 AND active = 1");
$stmt->execute();
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($banners as $banner) {
    echo "Banner ID {$banner['id']}:\n";
    echo "  title: {$banner['title']}\n";
    echo "  content: " . substr($banner['content'], 0, 50) . "...\n";
    echo "  image: {$banner['image']}\n";
    echo "  link: {$banner['link']}\n";
    echo "  style_id: {$banner['style_id']}\n";
    echo "  active: {$banner['active']}\n";
    echo "\n";
}

echo "=== LAYOUT_GROUP DEĞERLERİ ===\n";
$stmt = $pdo->prepare("SELECT DISTINCT layout_group FROM banner_layouts");
$stmt->execute();
$layoutGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Mevcut layout_group değerleri:\n";
foreach ($layoutGroups as $group) {
    echo "  - '{$group}'\n";
}
