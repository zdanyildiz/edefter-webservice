<?php
// AdminPageController'ın getPagesWithTranslationStatus metodunu test edelim
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';

include_once MODEL . 'Admin/AdminPage.php';
$adminPageModel = new AdminPage($db);

// Test parametreleri
$languageID = 1; // Türkçe
$translationFilter = 'all';

echo "=== ADMINPAGECONTROLLER TEST ===" . PHP_EOL;
echo "Language ID: $languageID" . PHP_EOL;
echo "Translation Filter: $translationFilter" . PHP_EOL . PHP_EOL;

// getAllPagesWithTranslationStatus metodunu çağır
$pagesResult = $adminPageModel->getAllPagesWithTranslationStatus($languageID);

echo "Bulunan sayfa sayısı: " . count($pagesResult) . PHP_EOL . PHP_EOL;

// İlk 3 sayfayı kontrol edelim
$counter = 0;
foreach ($pagesResult as &$page) {
    if ($counter >= 3) break;
    
    echo "=== SAYFA " . ($counter + 1) . " ===" . PHP_EOL;
    echo "Page ID: {$page['pageID']}" . PHP_EOL;
    echo "Page Name: {$page['pageName']}" . PHP_EOL;
    
    // Çeviri durumu detaylarını ekle
    if (isset($page['pageID'])) {
        $page['translationDetails'] = $adminPageModel->getPageTranslationStatus($page['pageID']);
        echo "Translation Details:" . PHP_EOL;
        
        if (empty($page['translationDetails'])) {
            echo "  Çeviri detayı bulunamadı!" . PHP_EOL;
        } else {
            foreach ($page['translationDetails'] as $detail) {
                echo "  • Dil: {$detail['languageName']} ({$detail['languageCode']})" . PHP_EOL;
                echo "    Status: " . ($detail['translationStatus'] ?? 'NULL') . PHP_EOL;
                echo "    Translated Page ID: " . ($detail['translatedPageID'] ?? 'NULL') . PHP_EOL;
            }
        }
    }
    
    echo str_repeat("-", 50) . PHP_EOL;
    $counter++;
}

echo PHP_EOL . "✅ Test tamamlandı!" . PHP_EOL;
?>
