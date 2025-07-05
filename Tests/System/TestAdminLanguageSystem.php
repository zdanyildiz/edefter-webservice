<?php
// Kök dizinden başlayalım
$rootPath = dirname(dirname(__DIR__));


require_once $rootPath . '/App/Database/AdminDatabase.php';
require_once $rootPath . '/App/Model/Admin/AdminLanguage.php';
require_once 'GetLocalDatabaseInfo.php';

// Veritabanı bilgilerini al
$dbInfo = getLocalDatabaseInfo();

// AdminDatabase nesnesi oluştur (doğru parametreler ile)
$db = new AdminDatabase($dbInfo['serverName'], $dbInfo['database'], $dbInfo['username'], $dbInfo['password']);
$adminLanguage = new AdminLanguage($db);

echo "=== ADMIN LANGUAGE SİSTEMİ TEST ===" . PHP_EOL;
echo "Tarih: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

try {
    // 1. Dilleri listele
    echo "1. MEVCUT DİLLER:" . PHP_EOL;
    $languages = $adminLanguage->getLanguages();
    foreach ($languages as $lang) {
        $main = $lang['isMainLanguage'] ? ' (ANA DİL)' : '';
        $active = $lang['isActive'] ? ' ✅' : ' ❌';
        echo "• {$lang['languageName']} ({$lang['languageCode']}) - ID: {$lang['languageID']}{$main}{$active}" . PHP_EOL;
    }
    
    // 2. Ana dil ID'sini kontrol et
    echo PHP_EOL . "2. ANA DİL ID'Sİ:" . PHP_EOL;
    $mainLangId = $adminLanguage->getMainLanguageId();
    echo "Ana dil ID: $mainLangId" . PHP_EOL;
    
    // 3. Bekleyen sayfa çevirilerini kontrol et
    echo PHP_EOL . "3. BEKLEYEN SAYFA ÇEVİRİLERİ:" . PHP_EOL;
    $pendingPages = $adminLanguage->getPendingPageTranslations(3);
    if (empty($pendingPages)) {
        echo "Bekleyen sayfa çevirisi yok." . PHP_EOL;
    } else {
        foreach ($pendingPages as $page) {
            echo "• ID: {$page['id']}, Orijinal: {$page['original_page_id']}, Çeviri: {$page['translated_page_id']}, Dil: {$page['language_name']}" . PHP_EOL;
        }
    }
    
    // 4. Çeviri istatistiklerini kontrol et
    echo PHP_EOL . "4. ÇEVİRİ İSTATİSTİKLERİ:" . PHP_EOL;
    $stats = $adminLanguage->getTranslationStatistics();
    foreach ($stats as $stat) {
        echo "• {$stat['language_name']}: Toplam {$stat['total_translations']}, Tamamlanan {$stat['completed']}, Bekleyen {$stat['pending']}, Hatalı {$stat['failed']}" . PHP_EOL;
    }
    
    // 5. Belirli bir sayfa için çeviri durumunu kontrol et
    echo PHP_EOL . "5. SAYFA ÇEVİRİ DURUMU (Sayfa ID: 1):" . PHP_EOL;
    $pageTranslations = $adminLanguage->getPageTranslationStatus(1);
    if (empty($pageTranslations)) {
        echo "Bu sayfa için çeviri kaydı yok." . PHP_EOL;
    } else {
        foreach ($pageTranslations as $trans) {
            echo "• {$trans['language_name']}: {$trans['translation_status']}" . PHP_EOL;
        }
    }
    
    // 6. Çeviri için uygun sayfaları listele (İngilizce için)
    echo PHP_EOL . "6. İNGİLİZCE ÇEVİRİ İÇİN SAYFALAR (İlk 5):" . PHP_EOL;
    $pagesForTranslation = $adminLanguage->getPagesForTranslation(2, 5);
    foreach ($pagesForTranslation as $page) {
        echo "• ID: {$page['sayfaid']}, Başlık: {$page['sayfabaslik']}, Durum: {$page['translation_status']}" . PHP_EOL;
    }
    
    echo PHP_EOL . "✅ Test başarıyla tamamlandı!" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
?>
