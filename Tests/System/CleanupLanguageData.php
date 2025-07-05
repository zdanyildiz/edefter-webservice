<?php
// Belirli dil ID'leri için tüm ilişkili verileri temizleme scripti
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

// Silinecek dil ID'leri
$targetLanguageIds = [3, 4, 5];

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== LANGUAGE CLEANUP SCRIPT ===\n";
    echo "Hedef Dil ID'leri: " . implode(', ', $targetLanguageIds) . "\n";
    echo "Zaman: " . date('Y-m-d H:i:s') . "\n\n";

    // İlk olarak sayfalistekategori tablosunu kontrol edelim
    $stmt = $pdo->query("SHOW TABLES LIKE 'sayfalistekategori'");
    $sayfalistekategoriExists = $stmt->rowCount() > 0;
    
    if (!$sayfalistekategoriExists) {
        echo "⚠️  sayfalistekategori tablosu bulunamadı, sadece kategori ve sayfa tabloları kontrol edilecek.\n\n";
    }

    $pdo->beginTransaction();
    
    $totalDeleted = [
        'categories' => 0,
        'pages' => 0,
        'seo_records' => 0,
        'page_category_relations' => 0,
        'category_mappings' => 0,
        'page_mappings' => 0
    ];

    // ADIM 1: Hedef dil ID'lerindeki kategorileri bul
    $placeholders = implode(',', array_fill(0, count($targetLanguageIds), '?'));
    $stmt = $pdo->prepare("
        SELECT kategoriid, kategoriad, dilid, benzersizid 
        FROM kategori 
        WHERE dilid IN ($placeholders) AND kategorisil != 1
    ");
    $stmt->execute($targetLanguageIds);
    $targetCategories = $stmt->fetchAll();

    echo "ADIM 1: Hedef kategoriler bulundu: " . count($targetCategories) . " adet\n";
    foreach ($targetCategories as $cat) {
        echo "  - ID: {$cat['kategoriid']}, Ad: {$cat['kategoriad']}, Dil: {$cat['dilid']}, UniqID: {$cat['benzersizid']}\n";
    }

    // ADIM 2: Bu kategorilerin benzersizid'lerini topla
    $categoryUniqIds = array_column($targetCategories, 'benzersizid');
    $categoryIds = array_column($targetCategories, 'kategoriid');

    if (!empty($categoryIds)) {
        // ADIM 3: sayfalistekategori tablosunda ilişkili sayfaları bul (eğer tablo varsa)
        $relatedPageIds = [];
        if ($sayfalistekategoriExists) {
            $categoryPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $stmt = $pdo->prepare("
                SELECT DISTINCT sayfaid 
                FROM sayfalistekategori 
                WHERE kategoriid IN ($categoryPlaceholders)
            ");
            $stmt->execute($categoryIds);
            $relatedPageIds = array_column($stmt->fetchAll(), 'sayfaid');
            
            echo "\nADIM 3: Kategorilerle ilişkili sayfalar bulundu: " . count($relatedPageIds) . " adet\n";
            if (!empty($relatedPageIds)) {
                foreach ($relatedPageIds as $pageId) {
                    echo "  - Sayfa ID: {$pageId}\n";
                }
            }
        }        // ADIM 4: Hedef dil ID'lerindeki kategorilerle ilişkili sayfalari bul
        $targetPageIds = [];
        if ($sayfalistekategoriExists && !empty($categoryIds)) {
            $categoryPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $stmt = $pdo->prepare("
                SELECT DISTINCT sayfaid 
                FROM sayfalistekategori 
                WHERE kategoriid IN ($categoryPlaceholders)
            ");
            $stmt->execute($categoryIds);
            $targetPageIds = array_column($stmt->fetchAll(), 'sayfaid');
            
            echo "\nADIM 4: Kategorilerle ilişkili sayfalar bulundu: " . count($targetPageIds) . " adet\n";
            if (!empty($targetPageIds)) {
                foreach ($targetPageIds as $pageId) {
                    echo "  - Sayfa ID: {$pageId}\n";
                }
            }
        }

        // ADIM 5: Bu sayfalarin detaylarını al
        $targetPages = [];
        if (!empty($targetPageIds)) {
            $pageIdPlaceholders = implode(',', array_fill(0, count($targetPageIds), '?'));
            $stmt = $pdo->prepare("
                SELECT sayfaid, sayfaad, benzersizid 
                FROM sayfa 
                WHERE sayfaid IN ($pageIdPlaceholders) AND sayfasil != 1
            ");
            $stmt->execute($targetPageIds);
            $targetPages = $stmt->fetchAll();

            echo "\nADIM 5: Silinecek sayfalar bulundu: " . count($targetPages) . " adet\n";
            foreach ($targetPages as $page) {
                echo "  - ID: {$page['sayfaid']}, Ad: {$page['sayfaad']}, UniqID: {$page['benzersizid']}\n";
            }
        }        // ADIM 6: Tüm benzersizid'leri topla (kategori + sayfa)
        $pageUniqIds = array_column($targetPages, 'benzersizid');
        $allUniqIds = array_merge($categoryUniqIds, $pageUniqIds);
        $allUniqIds = array_filter($allUniqIds); // Null değerleri temizle

        echo "\nADIM 6: Toplam benzersizid sayısı: " . count($allUniqIds) . "\n";

        // ADIM 7: SEO kayıtlarını sil
        if (!empty($allUniqIds)) {
            $uniqIdPlaceholders = implode(',', array_fill(0, count($allUniqIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM seo WHERE benzersizid IN ($uniqIdPlaceholders)");
            $stmt->execute($allUniqIds);
            $totalDeleted['seo_records'] = $stmt->rowCount();
            echo "✅ SEO kayıtları silindi: " . $totalDeleted['seo_records'] . " adet\n";
        }

        // ADIM 7: sayfalistekategori ilişkilerini sil (eğer tablo varsa)
        if ($sayfalistekategoriExists && !empty($categoryIds)) {
            $categoryPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM sayfalistekategori WHERE kategoriid IN ($categoryPlaceholders)");
            $stmt->execute($categoryIds);
            $totalDeleted['page_category_relations'] = $stmt->rowCount();
            echo "✅ Sayfa-kategori ilişkileri silindi: " . $totalDeleted['page_category_relations'] . " adet\n";
        }

        // ADIM 8: Sayfaları sil
        $targetPageIds = array_column($targetPages, 'sayfaid');
        if (!empty($targetPageIds)) {
            $pageIdPlaceholders = implode(',', array_fill(0, count($targetPageIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM sayfa WHERE sayfaid IN ($pageIdPlaceholders)");
            $stmt->execute($targetPageIds);
            $totalDeleted['pages'] = $stmt->rowCount();
            echo "✅ Sayfalar silindi: " . $totalDeleted['pages'] . " adet\n";
        }

        // ADIM 9: Kategorileri sil
        $categoryIdPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE kategoriid IN ($categoryIdPlaceholders)");
        $stmt->execute($categoryIds);
        $totalDeleted['categories'] = $stmt->rowCount();
        echo "✅ Kategoriler silindi: " . $totalDeleted['categories'] . " adet\n";
    }

    // ADIM 10: Çeviri mapping'lerini temizle
    $languagePlaceholders = implode(',', array_fill(0, count($targetLanguageIds), '?'));
    
    // Kategori mappingleri
    $stmt = $pdo->prepare("DELETE FROM language_category_mapping WHERE dilid IN ($languagePlaceholders)");
    $stmt->execute($targetLanguageIds);
    $totalDeleted['category_mappings'] = $stmt->rowCount();
    echo "✅ Kategori çeviri mappingleri silindi: " . $totalDeleted['category_mappings'] . " adet\n";
    
    // Sayfa mappingleri
    $stmt = $pdo->prepare("DELETE FROM language_page_mapping WHERE dilid IN ($languagePlaceholders)");
    $stmt->execute($targetLanguageIds);
    $totalDeleted['page_mappings'] = $stmt->rowCount();
    echo "✅ Sayfa çeviri mappingleri silindi: " . $totalDeleted['page_mappings'] . " adet\n";

    $pdo->commit();
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "TEMİZLEME TAMAMLANDI!\n";
    echo "Toplam Silinen Kayıtlar:\n";
    echo "- Kategoriler: " . $totalDeleted['categories'] . "\n";
    echo "- Sayfalar: " . $totalDeleted['pages'] . "\n";
    echo "- SEO Kayıtları: " . $totalDeleted['seo_records'] . "\n";
    echo "- Sayfa-Kategori İlişkileri: " . $totalDeleted['page_category_relations'] . "\n";
    echo "- Kategori Çeviri Mappingleri: " . $totalDeleted['category_mappings'] . "\n";
    echo "- Sayfa Çeviri Mappingleri: " . $totalDeleted['page_mappings'] . "\n";
    echo str_repeat("=", 60) . "\n";

    echo "\nMevcut durumu kontrol etmek için:\n";
    echo "php Tests\\System\\LanguageProcessMonitor.php\n";

} catch (Exception $e) {
    $pdo->rollback();
    echo "❌ HATA: " . $e->getMessage() . "\n";
    echo "Tüm işlemler geri alındı.\n";
}
?>
