<?php
// AdminPage model test scripti
include_once 'GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();

// Admin sınıflarını include et
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Database/AdminDatabase.php';
include_once $documentRoot . $directorySeparator . 'App/Model/Admin/AdminPage.php';

$db = new AdminDatabase($dbInfo['serverName'], $dbInfo['username'], $dbInfo['password'], $dbInfo['database']);
$adminPage = new AdminPage($db);

echo "=== ADMINPAGE MODELİ TEST ===" . PHP_EOL;
echo "Tarih: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// Test 1: Ana dil ID'sini getir
echo "--- Test 1: Ana Dil ID Testi ---" . PHP_EOL;
$reflection = new ReflectionClass($adminPage);
$method = $reflection->getMethod('getMainLanguageID');
$method->setAccessible(true);
$mainLanguageID = $method->invoke($adminPage);
echo "Ana dil ID: {$mainLanguageID}" . PHP_EOL . PHP_EOL;

// Test 2: Çeviri durumu ile sayfaları getir
echo "--- Test 2: Çeviri Durumu ile Sayfalar ---" . PHP_EOL;
$pages = $adminPage->getAllPagesWithTranslationStatus($mainLanguageID);
if ($pages) {
    echo "Toplam sayfa sayısı: " . count($pages) . PHP_EOL;
    echo "İlk 3 sayfa:" . PHP_EOL;
    foreach (array_slice($pages, 0, 3) as $page) {
        echo "  - ID: {$page['pageID']}, Ad: {$page['pageName']}, Kategori: {$page['pageCategoryName']}" . PHP_EOL;
    }
} else {
    echo "Sayfa bulunamadı" . PHP_EOL;
}

echo PHP_EOL . "--- Test 3: Sayfa Çeviri Durumu ---" . PHP_EOL;
if ($pages && count($pages) > 0) {
    $firstPage = $pages[0];
    $translationStatus = $adminPage->getPageTranslationStatus($firstPage['pageID']);
    echo "Sayfa: {$firstPage['pageName']} (ID: {$firstPage['pageID']})" . PHP_EOL;
    echo "Çeviri durumları:" . PHP_EOL;
    
    if ($translationStatus) {
        foreach ($translationStatus as $status) {
            $statusText = $status['translationStatus'] ?? 'Çevrilmemiş';
            echo "  - {$status['languageName']} ({$status['languageCode']}): {$statusText}" . PHP_EOL;
        }
    } else {
        echo "  Çeviri durumu bilgisi yok" . PHP_EOL;
    }
}

echo PHP_EOL . "--- Test 4: Çeviri Filtresi ---" . PHP_EOL;
$untranslatedPages = $adminPage->getPagesByTranslationStatus($mainLanguageID, 'untranslated', 2);
if ($untranslatedPages) {
    echo "Çevrilmemiş sayfa sayısı (İngilizce için): " . count($untranslatedPages) . PHP_EOL;
    if (count($untranslatedPages) > 0) {
        echo "İlk 3 çevrilmemiş sayfa:" . PHP_EOL;
        foreach (array_slice($untranslatedPages, 0, 3) as $page) {
            echo "  - ID: {$page['pageID']}, Ad: {$page['pageName']}" . PHP_EOL;
        }
    }
} else {
    echo "Çevrilmemiş sayfa bulunamadı" . PHP_EOL;
}

echo PHP_EOL . "=== TEST TAMAMLANDI ===" . PHP_EOL;
