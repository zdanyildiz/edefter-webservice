<?php
/**
 * Basit ve güvenli language cleanup - mapping tablosu üzerinden
 */

$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== BASİT LANGUAGE CLEANUP ===\n";
    echo "Hedef Dil ID'leri: 3, 4, 5, 6\n";
    echo "Başlangıç: " . date('Y-m-d H:i:s') . "\n\n";

    // Transaction başlat
    $pdo->beginTransaction();
    
    try {
        // 1. Çevrilmiş sayfa ID'lerini topla
        echo "1. Çevrilmiş sayfa ID'leri toplanıyor...\n";
        $stmt = $pdo->prepare("
            SELECT DISTINCT translated_page_id 
            FROM language_page_mapping 
            WHERE dilid IN (3, 4, 5, 6)
        ");
        $stmt->execute();
        $translatedPageIds = array_column($stmt->fetchAll(), 'translated_page_id');
        echo "   - Çevrilmiş sayfa ID'leri: " . count($translatedPageIds) . " adet\n";

        // 2. Çevrilmiş kategori ID'lerini topla
        echo "2. Çevrilmiş kategori ID'leri toplanıyor...\n";
        $stmt = $pdo->prepare("
            SELECT DISTINCT translated_category_id 
            FROM language_category_mapping 
            WHERE dilid IN (3, 4, 5, 6)
        ");
        $stmt->execute();
        $translatedCategoryIds = array_column($stmt->fetchAll(), 'translated_category_id');
        echo "   - Çevrilmiş kategori ID'leri: " . count($translatedCategoryIds) . " adet\n";

        // 3. Önce mapping'leri sil
        echo "3. Mapping'ler siliniyor...\n";
        $stmt = $pdo->prepare("DELETE FROM language_category_mapping WHERE dilid IN (3, 4, 5, 6)");
        $stmt->execute();
        echo "   - Category mapping silindi: " . $stmt->rowCount() . " adet\n";
        
        $stmt = $pdo->prepare("DELETE FROM language_page_mapping WHERE dilid IN (3, 4, 5, 6)");
        $stmt->execute();
        echo "   - Page mapping silindi: " . $stmt->rowCount() . " adet\n";

        // 4. SEO kayıtlarını sil
        if (!empty($translatedPageIds)) {
            echo "4. SEO kayıtları siliniyor...\n";
            $placeholders = str_repeat('?,', count($translatedPageIds) - 1) . '?';
            $stmt = $pdo->prepare("
                DELETE seo FROM seo 
                INNER JOIN sayfa s ON seo.benzersizid = s.benzersizid
                WHERE s.sayfaid IN ($placeholders)
            ");
            $stmt->execute($translatedPageIds);
            echo "   - SEO kayıtları silindi: " . $stmt->rowCount() . " adet\n";
        }

        // 5. Sayfalistekategori kayıtlarını sil
        if (!empty($translatedPageIds)) {
            echo "5. Sayfalistekategori kayıtları siliniyor...\n";
            $placeholders = str_repeat('?,', count($translatedPageIds) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM sayfalistekategori WHERE sayfaid IN ($placeholders)");
            $stmt->execute($translatedPageIds);
            echo "   - Sayfa ilişkileri silindi: " . $stmt->rowCount() . " adet\n";
        }

        if (!empty($translatedCategoryIds)) {
            $placeholders = str_repeat('?,', count($translatedCategoryIds) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM sayfalistekategori WHERE kategoriid IN ($placeholders)");
            $stmt->execute($translatedCategoryIds);
            echo "   - Kategori ilişkileri silindi: " . $stmt->rowCount() . " adet\n";
        }

        // 6. Sayfaları sil
        if (!empty($translatedPageIds)) {
            echo "6. Sayfalar siliniyor...\n";
            $placeholders = str_repeat('?,', count($translatedPageIds) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM sayfa WHERE sayfaid IN ($placeholders)");
            $stmt->execute($translatedPageIds);
            echo "   - Sayfalar silindi: " . $stmt->rowCount() . " adet\n";
        }

        // 7. Kategorileri sil
        if (!empty($translatedCategoryIds)) {
            echo "7. Kategoriler siliniyor...\n";
            $placeholders = str_repeat('?,', count($translatedCategoryIds) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE kategoriid IN ($placeholders)");
            $stmt->execute($translatedCategoryIds);
            echo "   - Kategoriler silindi: " . $stmt->rowCount() . " adet\n";
        }        // 8. Language copy jobs temizle
        echo "8. Language copy jobs temizleniyor...\n";
        $stmt = $pdo->prepare("DELETE FROM language_copy_jobs WHERE target_language_id IN (3, 4, 5, 6)");
        $stmt->execute();
        echo "   - Language copy jobs silindi: " . $stmt->rowCount() . " adet\n";

        // 9. Dilleri sil
        echo "9. Diller siliniyor...\n";
        $stmt = $pdo->prepare("DELETE FROM dil WHERE dilid IN (3, 4, 5, 6)");
        $stmt->execute();
        echo "   - Diller silindi: " . $stmt->rowCount() . " adet\n";

        // Transaction commit
        $pdo->commit();
        
        echo "\n=== TEMİZLEME BAŞARIYLA TAMAMLANDI ===\n";
        echo "Bitiş: " . date('Y-m-d H:i:s') . "\n";
        echo "\nSistem artık sadece Türkçe (ID: 1) ve English (ID: 2) dilleri içeriyor.\n";
        echo "\nKontrol için çalıştırın:\n";
        echo "php Tests\\System\\LanguageProcessMonitor.php\n";

    } catch (Exception $e) {
        // Transaction rollback
        $pdo->rollback();
        echo "HATA: " . $e->getMessage() . "\n";
        echo "Tüm değişiklikler geri alındı.\n";
        throw $e;
    }

} catch (Exception $e) {
    echo "GENEL HATA: " . $e->getMessage() . "\n";
}
?>
