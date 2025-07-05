<?php
// Dilid 7 temizleme
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);

echo "Dilid 7 temizleniyor...\n";

// 1. Mapping'leri temizle
$pdo->exec("DELETE FROM language_category_mapping WHERE dilid = 7");
$pdo->exec("DELETE FROM language_page_mapping WHERE dilid = 7");
$pdo->exec("DELETE FROM language_copy_jobs WHERE target_language_id = 7");

// 2. Dilid 7'deki çevrilmiş sayfa ID'lerini bul
$stmt = $pdo->query("
    SELECT DISTINCT translated_page_id 
    FROM language_page_mapping 
    WHERE dilid = 7
");
$translatedPageIds = array_column($stmt->fetchAll(), 'translated_page_id');

// 3. SEO kayıtlarını sil (sayfa benzersizid ile)
if (!empty($translatedPageIds)) {
    $placeholders = str_repeat('?,', count($translatedPageIds) - 1) . '?';
    $stmt = $pdo->prepare("
        DELETE seo FROM seo 
        INNER JOIN sayfa s ON seo.benzersizid = s.benzersizid
        WHERE s.sayfaid IN ($placeholders)
    ");
    $stmt->execute($translatedPageIds);
    echo "SEO kayıtları silindi: " . $stmt->rowCount() . " adet\n";
}

// 4. Sayfa-kategori ilişkilerini sil
$pdo->exec("DELETE slk FROM sayfalistekategori slk INNER JOIN kategori k ON slk.kategoriid = k.kategoriid WHERE k.dilid = 7");
if (!empty($translatedPageIds)) {
    $placeholders = str_repeat('?,', count($translatedPageIds) - 1) . '?';
    $stmt = $pdo->prepare("DELETE FROM sayfalistekategori WHERE sayfaid IN ($placeholders)");
    $stmt->execute($translatedPageIds);
    echo "Sayfa-kategori ilişkileri silindi: " . $stmt->rowCount() . " adet\n";
}

// 5. Sayfaları sil
if (!empty($translatedPageIds)) {
    $placeholders = str_repeat('?,', count($translatedPageIds) - 1) . '?';
    $stmt = $pdo->prepare("DELETE FROM sayfa WHERE sayfaid IN ($placeholders)");
    $stmt->execute($translatedPageIds);
    echo "Sayfalar silindi: " . $stmt->rowCount() . " adet\n";
}

// 6. Kategorileri sil
$stmt = $pdo->query("DELETE FROM kategori WHERE dilid = 7");
echo "Kategoriler silindi: " . $stmt->rowCount() . " adet\n";

// 7. Dili sil
$stmt = $pdo->query("DELETE FROM dil WHERE dilid = 7");
echo "Dil silindi: " . $stmt->rowCount() . " adet\n";

echo "Temizleme tamamlandı.\n";
?>
