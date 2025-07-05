<?php
// Basit test - sadece mevcut çeviri tablolarını kontrol edelim
include_once 'GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", $dbInfo['username'], $dbInfo['password']);

echo "=== MEVCUT ÇEVİRİ SİSTEMİ ANALİZİ ===" . PHP_EOL;
echo "Tarih: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// 1. Language Page Mapping tablosu analizi
echo "1. LANGUAGE_PAGE_MAPPING TABLOSUNDAKİ VERİLER:" . PHP_EOL;
$stmt = $pdo->query("
    SELECT 
        lpm.*,
        s_orig.sayfaad as original_title,
        s_trans.sayfaad as translated_title,
        d.dilad as language_name
    FROM language_page_mapping lpm
    LEFT JOIN sayfa s_orig ON lpm.original_page_id = s_orig.sayfaid
    LEFT JOIN sayfa s_trans ON lpm.translated_page_id = s_trans.sayfaid
    LEFT JOIN dil d ON lpm.dilid = d.dilid
    ORDER BY lpm.id DESC
    LIMIT 5
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "• ID: {$row['id']}" . PHP_EOL;
    echo "  Orijinal Sayfa: {$row['original_page_id']} - {$row['original_title']}" . PHP_EOL;
    echo "  Çeviri Sayfa: {$row['translated_page_id']} - {$row['translated_title']}" . PHP_EOL;
    echo "  Dil: {$row['language_name']} (ID: {$row['dilid']})" . PHP_EOL;
    echo "  Durum: {$row['translation_status']}" . PHP_EOL;
    echo "  Tarih: {$row['last_attempt_date']}" . PHP_EOL;
    echo str_repeat("-", 40) . PHP_EOL;
}

// 2. Sayfa çeviri durumu analizi
echo PHP_EOL . "2. SAYFA ÇEVİRİ DURUM İSTATİSTİKLERİ:" . PHP_EOL;
$stmt = $pdo->query("
    SELECT 
        d.dilad as language_name,
        COUNT(lpm.id) as total_translations,
        SUM(CASE WHEN lpm.translation_status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN lpm.translation_status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN lpm.translation_status = 'failed' THEN 1 ELSE 0 END) as failed
    FROM dil d
    LEFT JOIN language_page_mapping lpm ON d.dilid = lpm.dilid
    WHERE d.dilsil = 0 AND d.dilaktif = 1
    GROUP BY d.dilid
    ORDER BY d.dilid
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "• {$row['language_name']}: Toplam {$row['total_translations']}, Tamamlanan {$row['completed']}, Bekleyen {$row['pending']}, Hatalı {$row['failed']}" . PHP_EOL;
}

// 3. Belirli bir sayfa için çeviri durumunu kontrol
echo PHP_EOL . "3. SAYFA ID 1 İÇİN ÇEVİRİ DURUMU:" . PHP_EOL;
$stmt = $pdo->prepare("
    SELECT 
        lpm.*,
        d.dilad as language_name,
        d.dilkisa as language_code
    FROM language_page_mapping lpm
    LEFT JOIN dil d ON lpm.dilid = d.dilid
    WHERE lpm.original_page_id = ?
    ORDER BY lpm.dilid
");
$stmt->execute([1]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "• {$row['language_name']} ({$row['language_code']}): {$row['translation_status']}" . PHP_EOL;
}

// 4. Ana dildeki sayfalara göre çeviri durumu
echo PHP_EOL . "4. ANA DİLDEKİ SAYFALARIN ÇEVİRİ DURUMU (İlk 10 sayfa):" . PHP_EOL;
$stmt = $pdo->query("
    SELECT 
        s.sayfaid,
        s.sayfaad,
        CASE 
            WHEN lpm.id IS NOT NULL THEN lpm.translation_status
            ELSE 'untranslated'
        END as en_translation_status,
        lpm.translated_page_id,
        lpm.last_attempt_date
    FROM sayfa s
    LEFT JOIN language_page_mapping lpm ON s.sayfaid = lpm.original_page_id AND lpm.dilid = 2
    WHERE s.sayfasil = 0
    ORDER BY s.sayfaid DESC
    LIMIT 10
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "• ID: {$row['sayfaid']} - {$row['sayfaad']}" . PHP_EOL;
    echo "  İngilizce Durum: {$row['en_translation_status']}" . PHP_EOL;
    if ($row['translated_page_id']) {
        echo "  Çeviri ID: {$row['translated_page_id']}" . PHP_EOL;
    }
    echo str_repeat("-", 30) . PHP_EOL;
}

echo PHP_EOL . "✅ Analiz tamamlandı!" . PHP_EOL;
?>
