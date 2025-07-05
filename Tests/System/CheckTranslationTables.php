<?php
// Çeviri tablolarını kontrol et
include_once 'GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", $dbInfo['username'], $dbInfo['password']);

echo "=== ÇEVİRİ TABLOSU KONTROLÜ ===" . PHP_EOL;
echo "Tarih: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// Ana dil kontrolü
echo "--- ANA DİL KONTROLÜ ---" . PHP_EOL;
$stmt = $pdo->query("SELECT dilid, dilad, dilkisa, anadil FROM dil WHERE anadil = 1");
$mainLanguage = $stmt->fetch(PDO::FETCH_ASSOC);
if ($mainLanguage) {
    echo "Ana Dil: {$mainLanguage['dilad']} ({$mainLanguage['dilkisa']}) - ID: {$mainLanguage['dilid']}" . PHP_EOL;
} else {
    echo "⚠️ Ana dil bulunamadı!" . PHP_EOL;
}

echo PHP_EOL . "--- MEVCUT DİLLER ---" . PHP_EOL;
$stmt = $pdo->query("SELECT dilid, dilad, dilkisa, anadil, dilaktif FROM dil WHERE dilsil = 0 ORDER BY dilsira, dilid");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $isMain = $row['anadil'] ? ' (ANA DİL)' : '';
    $isActive = $row['dilaktif'] ? ' ✅' : ' ❌';
    echo "• {$row['dilad']} ({$row['dilkisa']}) - ID: {$row['dilid']}{$isMain}{$isActive}" . PHP_EOL;
}

// language_page_mapping tablosu kontrolü
echo PHP_EOL . "--- LANGUAGE_PAGE_MAPPING TABLOSU ---" . PHP_EOL;
$stmt = $pdo->query("SHOW TABLES LIKE 'language_page_mapping'");
if ($stmt->rowCount() > 0) {
    echo "✅ Tablo mevcut" . PHP_EOL;
    
    // Yapısını göster
    echo "Sütunlar:" . PHP_EOL;
    $stmt = $pdo->query("DESCRIBE language_page_mapping");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} | {$row['Type']}" . PHP_EOL;
    }
    
    // Örnek kayıtlar
    echo PHP_EOL . "Örnek kayıtlar:" . PHP_EOL;
    $stmt = $pdo->query("SELECT * FROM language_page_mapping LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  " . json_encode($row) . PHP_EOL;
    }
    
    // İstatistikler
    echo PHP_EOL . "İstatistikler:" . PHP_EOL;
    $stmt = $pdo->query("SELECT 
        translation_status, 
        COUNT(*) as count 
        FROM language_page_mapping 
        GROUP BY translation_status");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['translation_status']}: {$row['count']} kayıt" . PHP_EOL;
    }
} else {
    echo "❌ language_page_mapping tablosu bulunamadı!" . PHP_EOL;
}

// language_category_mapping tablosu kontrolü
echo PHP_EOL . "--- LANGUAGE_CATEGORY_MAPPING TABLOSU ---" . PHP_EOL;
$stmt = $pdo->query("SHOW TABLES LIKE 'language_category_mapping'");
if ($stmt->rowCount() > 0) {
    echo "✅ Tablo mevcut" . PHP_EOL;
    
    // Yapısını göster
    echo "Sütunlar:" . PHP_EOL;
    $stmt = $pdo->query("DESCRIBE language_category_mapping");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} | {$row['Type']}" . PHP_EOL;
    }
    
    // Örnek kayıtlar
    echo PHP_EOL . "Örnek kayıtlar:" . PHP_EOL;
    $stmt = $pdo->query("SELECT * FROM language_category_mapping LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  " . json_encode($row) . PHP_EOL;
    }
    
    // İstatistikler
    echo PHP_EOL . "İstatistikler:" . PHP_EOL;
    $stmt = $pdo->query("SELECT 
        translation_status, 
        COUNT(*) as count 
        FROM language_category_mapping 
        GROUP BY translation_status");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['translation_status']}: {$row['count']} kayıt" . PHP_EOL;
    }
} else {
    echo "❌ language_category_mapping tablosu bulunamadı!" . PHP_EOL;
}

// Sayfa tablosu analizi
echo PHP_EOL . "--- SAYFA TABLOSU ANALİZİ ---" . PHP_EOL;
$stmt = $pdo->query("SELECT COUNT(*) as total FROM sayfa WHERE sayfasil = 0 AND sayfaaktif = 1");
$pageCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "Toplam aktif sayfa: {$pageCount}" . PHP_EOL;

// Ana dile göre sayfa sayısı
if ($mainLanguage) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM sayfa s 
        INNER JOIN sayfalistekategori slk ON s.sayfaid = slk.sayfaid 
        INNER JOIN kategori k ON slk.kategoriid = k.kategoriid 
        WHERE s.sayfasil = 0 AND s.sayfaaktif = 1 AND k.dilid = ?
    ");
    $stmt->execute([$mainLanguage['dilid']]);
    $mainLangPageCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Ana dildeki sayfa sayısı: {$mainLangPageCount}" . PHP_EOL;
}

echo PHP_EOL . "=== KONTROL TAMAMLANDI ===" . PHP_EOL;
