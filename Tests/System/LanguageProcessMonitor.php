<?php
// Dil ekleme sürecini monitör eden sistem
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== DİL EKLEME SÜRECİ MONİTÖRÜ ===\n";
    echo "Zaman: " . date('Y-m-d H:i:s') . "\n\n";

    // 1. Mevcut kategori sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kategori WHERE kategorisil != 1");
    $categoryCount = $stmt->fetch()['total'];
    echo "1. MEVCUT KATEGORİLER: {$categoryCount} adet\n";

    // 2. Mevcut sayfa sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sayfa WHERE sayfasil != 1");
    $pageCount = $stmt->fetch()['total'];
    echo "2. MEVCUT SAYFALAR: {$pageCount} adet\n";

    // 3. Mevcut SEO kayıtları
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM seo");
    $seoCount = $stmt->fetch()['total'];
    echo "3. MEVCUT SEO KAYITLARI: {$seoCount} adet\n";

    // 4. Aktif diller
    $stmt = $pdo->query("SELECT dilid, dilad, dilkisa FROM dil WHERE dilaktif = 1 AND dilsil != 1 ORDER BY dilid");
    $languages = $stmt->fetchAll();
    echo "4. AKTİF DİLLER:\n";
    foreach ($languages as $lang) {
        echo "   - {$lang['dilid']}: {$lang['dilad']} ({$lang['dilkisa']})\n";
    }

    // 5. Çeviri mapping durumu
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM language_category_mapping");
    $categoryMappingCount = $stmt->fetch()['total'];
    echo "5. KATEGORİ ÇEVİRİ MAPPING: {$categoryMappingCount} adet\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM language_page_mapping");
    $pageMappingCount = $stmt->fetch()['total'];
    echo "6. SAYFA ÇEVİRİ MAPPING: {$pageMappingCount} adet\n";    // 6. Bekleyen çeviriler
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM language_category_mapping WHERE translation_status = 'pending'");
    $pendingCategoryCount = $stmt->fetch()['total'];
    echo "7. BEKLEYEN KATEGORİ ÇEVİRİLERİ: {$pendingCategoryCount} adet\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM language_page_mapping WHERE translation_status = 'pending'");
    $pendingPageCount = $stmt->fetch()['total'];
    echo "8. BEKLEYEN SAYFA ÇEVİRİLERİ: {$pendingPageCount} adet\n";

    // 7. Son log durumu
    $logFile = $documentRoot . $directorySeparator . 'Public/Log/Admin/' . date('Y-m-d') . '.log';
    if (file_exists($logFile)) {
        $logLines = file($logFile);
        $contentTranslatorLogs = array_filter($logLines, function($line) {
            return strpos($line, 'ContentTranslator') !== false;
        });
        echo "9. BUGÜNKÜ CONTENTTRANSLATOR LOGLARI: " . count($contentTranslatorLogs) . " adet\n";
        
        if (count($contentTranslatorLogs) > 0) {
            echo "   Son log: " . trim(end($contentTranslatorLogs)) . "\n";
        }
    } else {
        echo "9. BUGÜNKÜ LOG DOSYASI: Bulunamadı\n";
    }

    echo "\n=== İŞLEM ÖNCESİ SNAPSHOT ALINDI ===\n";
    echo "Şimdi admin panelden:\n";
    echo "1. Logları temizleyebilirsiniz\n";
    echo "2. Kategorileri silebilirsiniz\n";
    echo "3. Sayfaları silebilirsiniz\n";
    echo "4. Yeni dil ekleyebilirsiniz\n\n";
    
    echo "Her adımdan sonra bu scripti tekrar çalıştırarak değişiklikleri takip edebilirsiniz:\n";
    echo "php Tests\\System\\LanguageProcessMonitor.php\n";

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
