<?php
// En basit şekilde sorunu tespit edelim
include_once 'GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", $dbInfo['username'], $dbInfo['password']);

echo "=== FRONTEND JSON ÇIKTISI SİMÜLASYONU ===" . PHP_EOL;

// Frontend'in yapması gereken işlemi simüle edelim

// 1. Sayfa 35 için çeviri durumunu alalım
$pageID = 35;
$mainLanguageID = 1;

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
            AND lpm.original_page_id = ?
        )
    WHERE 
        dil.dilaktif = 1 
        AND dil.dilsil = 0
        AND dil.dilid != ?
    ORDER BY 
        dil.dilsira ASC, dil.dilid ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$pageID, $mainLanguageID]);
$translationDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Sayfa ID: $pageID" . PHP_EOL;
echo "Çeviri Detayları:" . PHP_EOL;

foreach ($translationDetails as $detail) {
    echo "  • Dil: {$detail['languageName']} ({$detail['languageCode']})" . PHP_EOL;
    echo "    Status: " . ($detail['translationStatus'] ?? 'NULL') . PHP_EOL;
    echo "    Translated Page ID: " . ($detail['translatedPageID'] ?? 'NULL') . PHP_EOL;
}

echo PHP_EOL . "=== JAVASCRIPT EXPECTED FORMAT ===" . PHP_EOL;

// JavaScript'in beklediği format
$jsFormat = [
    'pageID' => $pageID,
    'pageName' => 'Anlaşmalı Kurumlar',
    'translationDetails' => $translationDetails
];

echo json_encode($jsFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

echo PHP_EOL . "=== JAVASCRIPT FRONTEND SİMÜLASYONU ===" . PHP_EOL;

// Frontend JavaScript'in generateTranslationStatusHtml fonksiyonunu simüle edelim
function generateTranslationStatusHtml($translationDetails) {
    if (empty($translationDetails)) {
        return '<span class="label label-warning">Çeviri Bilgisi Yok</span>';
    }

    $html = '<div class="translation-badges">';
    foreach ($translationDetails as $detail) {
        $languageCode = $detail['languageCode'];
        $languageName = $detail['languageName'];
        $translationStatus = $detail['translationStatus'];
        
        $badgeClass = 'label-default';
        $icon = 'fa-question';
        $title = 'Bilinmeyen';

        switch($translationStatus) {
            case 'pending':
                $badgeClass = 'label-warning';
                $icon = 'fa-clock-o';
                $title = 'Beklemede';
                break;
            case 'completed':
                $badgeClass = 'label-success';
                $icon = 'fa-check';
                $title = 'Tamamlandı';
                break;
            case 'failed':
                $badgeClass = 'label-danger';
                $icon = 'fa-times';
                $title = 'Başarısız';
                break;
            default:
                if (!$translationStatus) {
                    $badgeClass = 'label-info';
                    $icon = 'fa-plus';
                    $title = 'Çevrilmemiş';
                }
        }

        $html .= '<span class="label ' . $badgeClass . '" title="' . $title . ' - ' . $languageName . '" style="margin-right: 3px;">';
        $html .= '<i class="fa ' . $icon . '"></i> ' . strtoupper($languageCode);
        $html .= '</span>';
    }

    $html .= '</div>';
    return $html;
}

echo "HTML Çıktısı:" . PHP_EOL;
echo generateTranslationStatusHtml($translationDetails) . PHP_EOL;

echo PHP_EOL . "✅ Simülasyon tamamlandı!" . PHP_EOL;
?>
