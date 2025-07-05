<?php
include_once 'GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", $dbInfo['username'], $dbInfo['password']);

// Belirli bir sayfa için çeviri durumu kontrolü
$pageID = 35; // Anlaşmalı Kurumlar sayfası

echo "=== SAYFA ID $pageID İÇİN ÇEVİRİ DURUMU DEBUG ===" . PHP_EOL;

// AdminPage.getPageTranslationStatus metodunu simüle edelim
$mainLanguageID = 1; // Ana dil Türkçe

$sql = "
    SELECT 
        dil.dilid as languageID,
        dil.dilad as languageName,
        dil.dilkisa as languageCode,
        lpm.translation_status as translationStatus,
        lpm.last_attempt_date as translationDate,
        lpm.error_message as errorMessage,
        lpm.translated_page_id as translatedPageID
    FROM 
        dil
        LEFT JOIN language_page_mapping lpm ON (
            dil.dilid = lpm.dilid 
            AND lpm.original_page_id = :originalPageID
        )
    WHERE 
        dil.dilaktif = 1 
        AND dil.dilsil = 0
        AND dil.dilid != :mainLanguageID  -- Ana dil hariç
    ORDER BY 
        dil.dilsira ASC, dil.dilid ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'originalPageID' => $pageID,
    'mainLanguageID' => $mainLanguageID
]);

echo "SQL SORGUSU:" . PHP_EOL;
echo $sql . PHP_EOL . PHP_EOL;

echo "PARAMETRELER:" . PHP_EOL;
echo "originalPageID: $pageID" . PHP_EOL;
echo "mainLanguageID: $mainLanguageID" . PHP_EOL . PHP_EOL;

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "SONUÇLAR:" . PHP_EOL;
foreach ($results as $row) {
    echo "• Dil: {$row['languageName']} ({$row['languageCode']})" . PHP_EOL;
    echo "  Translation Status: " . ($row['translationStatus'] ?? 'NULL') . PHP_EOL;
    echo "  Translated Page ID: " . ($row['translatedPageID'] ?? 'NULL') . PHP_EOL;
    echo "  Translation Date: " . ($row['translationDate'] ?? 'NULL') . PHP_EOL;
    echo str_repeat("-", 40) . PHP_EOL;
}

// Ayrıca bu sayfa için mapping kaydını kontrol edelim
echo PHP_EOL . "=== MAPPING KAYITLARI ===" . PHP_EOL;
$stmt = $pdo->prepare("SELECT * FROM language_page_mapping WHERE original_page_id = ?");
$stmt->execute([$pageID]);
$mappings = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($mappings as $mapping) {
    echo "• Mapping ID: {$mapping['id']}" . PHP_EOL;
    echo "  Original Page ID: {$mapping['original_page_id']}" . PHP_EOL;
    echo "  Translated Page ID: {$mapping['translated_page_id']}" . PHP_EOL;
    echo "  Language ID: {$mapping['dilid']}" . PHP_EOL;
    echo "  Status: {$mapping['translation_status']}" . PHP_EOL;
    echo str_repeat("-", 40) . PHP_EOL;
}

echo PHP_EOL . "✅ Debug tamamlandı!" . PHP_EOL;
?>
