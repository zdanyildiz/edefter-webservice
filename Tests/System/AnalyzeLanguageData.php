<?php
// Dil ID'lerine göre mevcut veri dağılımını kontrol et
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== DİL ID'LERİNE GÖRE VERİ DAĞILIMI ===\n";
    echo "Zaman: " . date('Y-m-d H:i:s') . "\n\n";

    // Kategoriler dil ID'sine göre
    $stmt = $pdo->query("
        SELECT dilid, COUNT(*) as count 
        FROM kategori 
        WHERE kategorisil != 1 
        GROUP BY dilid 
        ORDER BY dilid
    ");
    $categoryByLang = $stmt->fetchAll();
    
    echo "1. KATEGORİLER DİL DAĞILIMI:\n";
    foreach ($categoryByLang as $row) {
        echo "   - Dil ID {$row['dilid']}: {$row['count']} kategori\n";
    }

    // Sayfalistekategori üzerinden sayfa-kategori ilişkileri
    $stmt = $pdo->query("
        SELECT k.dilid, COUNT(DISTINCT slk.sayfaid) as page_count, COUNT(*) as relation_count
        FROM sayfalistekategori slk
        JOIN kategori k ON slk.kategoriid = k.kategoriid
        WHERE k.kategorisil != 1
        GROUP BY k.dilid
        ORDER BY k.dilid
    ");
    $pageRelationByLang = $stmt->fetchAll();
    
    echo "\n2. SAYFA-KATEGORİ İLİŞKİLERİ DİL DAĞILIMI:\n";
    foreach ($pageRelationByLang as $row) {
        echo "   - Dil ID {$row['dilid']}: {$row['page_count']} sayfa, {$row['relation_count']} ilişki\n";
    }

    // Toplam sayfa sayıları
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sayfa WHERE sayfasil != 1");
    $totalPages = $stmt->fetch()['total'];
    echo "\n3. TOPLAM AKTİF SAYFA: {$totalPages}\n";

    // Çeviri mappingleri
    $stmt = $pdo->query("
        SELECT dilid, COUNT(*) as count 
        FROM language_category_mapping 
        GROUP BY dilid 
        ORDER BY dilid
    ");
    $categoryMappingByLang = $stmt->fetchAll();
    
    echo "\n4. KATEGORİ ÇEVİRİ MAPPING DİL DAĞILIMI:\n";
    foreach ($categoryMappingByLang as $row) {
        echo "   - Dil ID {$row['dilid']}: {$row['count']} mapping\n";
    }

    $stmt = $pdo->query("
        SELECT dilid, COUNT(*) as count 
        FROM language_page_mapping 
        GROUP BY dilid 
        ORDER BY dilid
    ");
    $pageMappingByLang = $stmt->fetchAll();
    
    echo "\n5. SAYFA ÇEVİRİ MAPPING DİL DAĞILIMI:\n";
    foreach ($pageMappingByLang as $row) {
        echo "   - Dil ID {$row['dilid']}: {$row['count']} mapping\n";
    }

    // Hedef dil ID'leri analizi
    $targetLanguageIds = [3, 4, 5];
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "HEDEFLENENEKVERİ ANALİZİ (Dil ID: 3, 4, 5)\n";
    echo str_repeat("=", 50) . "\n";

    foreach ($targetLanguageIds as $langId) {
        echo "\n📋 DİL ID {$langId} ANALİZİ:\n";
        
        // Bu dildeki kategoriler
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM kategori WHERE dilid = ? AND kategorisil != 1");
        $stmt->execute([$langId]);
        $catCount = $stmt->fetch()['count'];
        echo "   - Kategoriler: {$catCount} adet\n";
        
        // Bu dildeki kategori mappingleri
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM language_category_mapping WHERE dilid = ?");
        $stmt->execute([$langId]);
        $catMappingCount = $stmt->fetch()['count'];
        echo "   - Kategori Mappingleri: {$catMappingCount} adet\n";
        
        // Bu dildeki sayfa mappingleri
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM language_page_mapping WHERE dilid = ?");
        $stmt->execute([$langId]);
        $pageMappingCount = $stmt->fetch()['count'];
        echo "   - Sayfa Mappingleri: {$pageMappingCount} adet\n";
        
        // Bu dil kategorileriyle ilişkili sayfalar
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT slk.sayfaid) as count 
            FROM sayfalistekategori slk
            JOIN kategori k ON slk.kategoriid = k.kategoriid
            WHERE k.dilid = ? AND k.kategorisil != 1
        ");
        $stmt->execute([$langId]);
        $relatedPageCount = $stmt->fetch()['count'];
        echo "   - İlişkili Sayfalar: {$relatedPageCount} adet\n";
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Temizleme scriptini çalıştırmak için:\n";
    echo "php Tests\\System\\CleanupLanguageData.php\n";
    echo str_repeat("=", 50) . "\n";

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
