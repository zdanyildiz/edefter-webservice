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
        echo "  - ID: {$cat['kategoriid']}, Ad: {$cat['kategoriad']}, Dil: {$cat['dilid']}\n";
    }

    if (!empty($targetCategories)) {
        $categoryIds = array_column($targetCategories, 'kategoriid');
        $categoryUniqIds = array_column($targetCategories, 'benzersizid');

        // ADIM 2: Bu kategorilerle ilişkili sayfalari bul
        $targetPageIds = [];
        if (!empty($categoryIds)) {
            $categoryPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $stmt = $pdo->prepare("
                SELECT DISTINCT sayfaid 
                FROM sayfalistekategori 
                WHERE kategoriid IN ($categoryPlaceholders)
            ");
            $stmt->execute($categoryIds);
            $targetPageIds = array_column($stmt->fetchAll(), 'sayfaid');
            
            echo "\nADIM 2: Kategorilerle ilişkili sayfalar bulundu: " . count($targetPageIds) . " adet\n";
        }

        // ADIM 3: Sayfa detaylarını al
        $targetPages = [];
        if (!empty($targetPageIds)) {
            $pageIdPlaceholders = implode(',', array_fill(0, count($targetPageIds), '?'));
            $stmt = $pdo->prepare("
                SELECT sayfaid, sayfaad, benzersizid 
                FROM sayfa 
                WHERE sayfaid IN ($pageIdPlaceholders)
            ");
            $stmt->execute($targetPageIds);
            $targetPages = $stmt->fetchAll();

            echo "ADIM 3: Silinecek sayfalar: " . count($targetPages) . " adet\n";
            foreach ($targetPages as $page) {
                echo "  - ID: {$page['sayfaid']}, Ad: {$page['sayfaad']}\n";
            }
        }        // ADIM 4: ÖNCE çeviri mapping'lerini temizle (foreign key constraint için)
        $languagePlaceholders = implode(',', array_fill(0, count($targetLanguageIds), '?'));
        
        // Kategori mappingleri
        $stmt = $pdo->prepare("DELETE FROM language_category_mapping WHERE dilid IN ($languagePlaceholders)");
        $stmt->execute($targetLanguageIds);
        $totalDeleted['category_mappings'] = $stmt->rowCount();
        echo "\nADIM 4a: Kategori çeviri mappingleri silindi: " . $totalDeleted['category_mappings'] . " adet\n";
        
        // Sayfa mappingleri
        $stmt = $pdo->prepare("DELETE FROM language_page_mapping WHERE dilid IN ($languagePlaceholders)");
        $stmt->execute($targetLanguageIds);
        $totalDeleted['page_mappings'] = $stmt->rowCount();
        echo "ADIM 4b: Sayfa çeviri mappingleri silindi: " . $totalDeleted['page_mappings'] . " adet\n";

        // ADIM 5: SEO kayıtlarını sil
        $pageUniqIds = array_column($targetPages, 'benzersizid');
        $allUniqIds = array_merge($categoryUniqIds, $pageUniqIds);
        $allUniqIds = array_filter($allUniqIds);

        if (!empty($allUniqIds)) {
            $uniqIdPlaceholders = implode(',', array_fill(0, count($allUniqIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM seo WHERE benzersizid IN ($uniqIdPlaceholders)");
            $stmt->execute($allUniqIds);
            $totalDeleted['seo_records'] = $stmt->rowCount();
            echo "ADIM 5: SEO kayıtları silindi: " . $totalDeleted['seo_records'] . " adet\n";
        }

        // ADIM 6: Sayfa-kategori ilişkilerini sil
        if (!empty($categoryIds)) {
            $categoryPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM sayfalistekategori WHERE kategoriid IN ($categoryPlaceholders)");
            $stmt->execute($categoryIds);
            $totalDeleted['page_category_relations'] = $stmt->rowCount();
            echo "ADIM 6: Sayfa-kategori ilişkileri silindi: " . $totalDeleted['page_category_relations'] . " adet\n";
        }

        // ADIM 7: Sayfaları sil
        if (!empty($targetPageIds)) {
            $pageIdPlaceholders = implode(',', array_fill(0, count($targetPageIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM sayfa WHERE sayfaid IN ($pageIdPlaceholders)");
            $stmt->execute($targetPageIds);
            $totalDeleted['pages'] = $stmt->rowCount();
            echo "ADIM 7: Sayfalar silindi: " . $totalDeleted['pages'] . " adet\n";
        }

        // ADIM 8: Kategorileri sil
        $categoryIdPlaceholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE kategoriid IN ($categoryIdPlaceholders)");
        $stmt->execute($categoryIds);
        $totalDeleted['categories'] = $stmt->rowCount();
        echo "ADIM 8: Kategoriler silindi: " . $totalDeleted['categories'] . " adet\n";
    } else {
        // Kategoriler yokken de mapping'leri temizleyelim
        $languagePlaceholders = implode(',', array_fill(0, count($targetLanguageIds), '?'));
        
        $stmt = $pdo->prepare("DELETE FROM language_category_mapping WHERE dilid IN ($languagePlaceholders)");
        $stmt->execute($targetLanguageIds);
        $totalDeleted['category_mappings'] = $stmt->rowCount();
        echo "\nADIM 4a: Kategori çeviri mappingleri silindi: " . $totalDeleted['category_mappings'] . " adet\n";
        
        $stmt = $pdo->prepare("DELETE FROM language_page_mapping WHERE dilid IN ($languagePlaceholders)");
        $stmt->execute($targetLanguageIds);
        $totalDeleted['page_mappings'] = $stmt->rowCount();
        echo "ADIM 4b: Sayfa çeviri mappingleri silindi: " . $totalDeleted['page_mappings'] . " adet\n";
    }

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

} catch (Exception $e) {
    $pdo->rollback();
    echo "❌ HATA: " . $e->getMessage() . "\n";
    echo "Tüm işlemler geri alındı.\n";
}
?>
