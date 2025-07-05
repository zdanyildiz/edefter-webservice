<?php
/**
 * Tüm eklenen dil verilerini temizle (3, 4, 5, 6)
 * Gerçekten temiz bir başlangıç için
 */

$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== KAPSAMLI DİL VERİ TEMİZLEME ===\n";
    echo "Hedef Dil ID'leri: 3, 4, 5, 6\n";
    echo "Başlangıç: " . date('Y-m-d H:i:s') . "\n\n";

    // Transaction başlat
    $pdo->beginTransaction();
    
    $deletedCounts = [
        'categories' => 0,
        'pages' => 0,
        'seo_records' => 0,
        'page_category_relations' => 0,
        'category_mappings' => 0,
        'page_mappings' => 0,
        'languages' => 0
    ];

    try {
        // 1. Language mapping kayıtlarını sil
        echo "1. Language mapping kayıtları siliniyor...\n";
          $stmt = $pdo->prepare("DELETE FROM language_category_mapping WHERE dilid IN (3, 4, 5, 6)");
        $stmt->execute();
        $deletedCounts['category_mappings'] = $stmt->rowCount();
        echo "   - Category mapping silindi: {$deletedCounts['category_mappings']} adet\n";
        
        $stmt = $pdo->prepare("DELETE FROM language_page_mapping WHERE dilid IN (3, 4, 5, 6)");
        $stmt->execute();
        $deletedCounts['page_mappings'] = $stmt->rowCount();
        echo "   - Page mapping silindi: {$deletedCounts['page_mappings']} adet\n";        // 2. SEO kayıtlarını sil (çevrilmiş sayfalarla ilişkili)
        echo "2. SEO kayıtları siliniyor...\n";
        $stmt = $pdo->prepare("
            DELETE seo FROM seo 
            INNER JOIN sayfa s ON seo.benzersizid = s.benzersizid
            INNER JOIN language_page_mapping lpm ON s.sayfaid = lpm.translated_page_id 
            WHERE lpm.dilid IN (3, 4, 5, 6)
        ");
        $stmt->execute();
        $deletedCounts['seo_records'] = $stmt->rowCount();
        echo "   - SEO kayıtları silindi: {$deletedCounts['seo_records']} adet\n";

        // 3. Sayfalistekategori kayıtlarını sil
        echo "3. Sayfalistekategori kayıtları siliniyor...\n";
        
        // Önce kategorilerle ilişkili olanları
        $stmt = $pdo->prepare("
            DELETE slk FROM sayfalistekategori slk 
            INNER JOIN kategori k ON slk.kategorid = k.kategorid 
            WHERE k.dilid IN (3, 4, 5, 6)
        ");
        $stmt->execute();
        $tempDeleted = $stmt->rowCount();
        
        // Sonra çevrilmiş sayfalarla ilişkili olanları
        $stmt = $pdo->prepare("
            DELETE slk FROM sayfalistekategori slk 
            INNER JOIN language_page_mapping lpm ON slk.sayfaid = lpm.translated_page_id 
            WHERE lpm.dilid IN (3, 4, 5, 6)
        ");
        $stmt->execute();
        $deletedCounts['page_category_relations'] = $tempDeleted + $stmt->rowCount();
        echo "   - Sayfalistekategori silindi: {$deletedCounts['page_category_relations']} adet\n";

        // 4. Sayfaları sil (çevrilmiş sayfaları)
        echo "4. Sayfalar siliniyor...\n";
        $stmt = $pdo->prepare("
            DELETE s FROM sayfa s 
            INNER JOIN language_page_mapping lpm ON s.sayfaid = lpm.translated_page_id 
            WHERE lpm.dilid IN (3, 4, 5, 6)
        ");
        $stmt->execute();
        $deletedCounts['pages'] = $stmt->rowCount();
        echo "   - Sayfalar silindi: {$deletedCounts['pages']} adet\n";

        // 5. Kategorileri sil (dilid 3, 4, 5, 6 olanları)
        echo "5. Kategoriler siliniyor...\n";
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE dilid IN (3, 4, 5, 6)");
        $stmt->execute();
        $deletedCounts['categories'] = $stmt->rowCount();
        echo "   - Kategoriler silindi: {$deletedCounts['categories']} adet\n";

        // 6. Dilleri sil (id 3, 4, 5, 6 olanları)
        echo "6. Diller siliniyor...\n";
        $stmt = $pdo->prepare("DELETE FROM dil WHERE dilid IN (3, 4, 5, 6)");
        $stmt->execute();
        $deletedCounts['languages'] = $stmt->rowCount();
        echo "   - Diller silindi: {$deletedCounts['languages']} adet\n";

        // Transaction commit
        $pdo->commit();
        
        echo "\n=== KAPSAMLI TEMİZLEME TAMAMLANDI ===\n";
        echo "Bitiş: " . date('Y-m-d H:i:s') . "\n";
        echo "\nSilinen kayıt sayıları:\n";
        foreach ($deletedCounts as $table => $count) {
            echo "- {$table}: {$count} adet\n";
        }
        
        $totalDeleted = array_sum($deletedCounts);
        echo "\nToplam silinen kayıt: {$totalDeleted} adet\n";
        
        echo "\nŞimdi sistem tamamen temiz!\n";
        echo "Sadece dilid 1 (Türkçe) ve 2 (English) kaldı.\n";
        echo "\nKontrol için çalıştırın:\n";
        echo "php Tests\\System\\LanguageProcessMonitor.php\n";

    } catch (Exception $e) {
        // Transaction rollback
        $pdo->rollback();
        echo "HATA: Silme işlemi sırasında hata oluştu: " . $e->getMessage() . "\n";
        echo "Tüm değişiklikler geri alındı.\n";
        throw $e;
    }

} catch (Exception $e) {
    echo "GENEL HATA: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== İŞLEM SONU ===\n";
?>
