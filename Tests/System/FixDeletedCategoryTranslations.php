<?php
// Silinmiş kategori çevirilerini düzelt ve yeniden aktif et
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    echo "=== SİLİNMİŞ KATEGORİ ÇEVİRİLERİNİ DÜZELT ===\n\n";
    
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Önce durumu kontrol et
    echo "1. MEVCUT DURUM\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("
        SELECT k.kategoriid, k.kategoriad, k.kategoriaktif, k.kategorisil, k.dilid
        FROM kategori k
        WHERE k.kategoriid IN (6, 7, 8, 9)
        ORDER BY k.kategoriid
    ");
    
    $problematicCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($problematicCategories as $cat) {
        echo "ID: {$cat['kategoriid']}, Ad: '{$cat['kategoriad']}', Aktif: {$cat['kategoriaktif']}, Silinmiş: {$cat['kategorisil']}, Dil: {$cat['dilid']}\n";
    }
    
    // Kullanıcıdan onay al
    echo "\n2. DÜZELTME İŞLEMİ\n";
    echo str_repeat("-", 50) . "\n";
    echo "Bu kategorileri aktif hale getirmek istiyor musunuz? (y/n): ";
    
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($response) !== 'y') {
        echo "İşlem iptal edildi.\n";
        exit;
    }
    
    // Kategorileri aktif et
    $pdo->beginTransaction();
    
    try {
        $updateStmt = $pdo->prepare("
            UPDATE kategori 
            SET kategoriaktif = 1, kategorisil = 0, kategoritarihguncel = NOW()
            WHERE kategoriid IN (6, 7, 8, 9)
        ");
        
        $updateStmt->execute();
        $affectedRows = $updateStmt->rowCount();
        
        echo "✅ {$affectedRows} kategori aktif hale getirildi.\n";
        
        // Failed durumundaki mapping'leri pending'e çevir
        $mappingStmt = $pdo->prepare("
            UPDATE language_category_mapping 
            SET translation_status = 'pending', error_message = NULL
            WHERE id IN (4, 5, 6, 7)
        ");
        
        $mappingStmt->execute();
        $mappingRows = $mappingStmt->rowCount();
        
        echo "✅ {$mappingRows} çeviri mapping'i pending durumuna alındı.\n";
        
        $pdo->commit();
        
        echo "\n3. GÜNCEL DURUM\n";
        echo str_repeat("-", 50) . "\n";
        
        // Güncellenen durumu göster
        $stmt = $pdo->query("
            SELECT k.kategoriid, k.kategoriad, k.kategoriaktif, k.kategorisil, k.dilid
            FROM kategori k
            WHERE k.kategoriid IN (6, 7, 8, 9)
            ORDER BY k.kategoriid
        ");
        
        $updatedCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($updatedCategories as $cat) {
            echo "ID: {$cat['kategoriid']}, Ad: '{$cat['kategoriad']}', Aktif: {$cat['kategoriaktif']}, Silinmiş: {$cat['kategorisil']}, Dil: {$cat['dilid']}\n";
        }
        
        // Pending mapping'leri göster
        echo "\n4. BEKLEYEN ÇEVİRİLER\n";
        echo str_repeat("-", 50) . "\n";
        
        $stmt = $pdo->query("
            SELECT 
                lcm.id,
                lcm.original_category_id,
                lcm.translated_category_id,
                lcm.translation_status,
                k_orig.kategoriad as original_title,
                k_trans.kategoriad as translated_title,
                d.dilad as language_name
            FROM language_category_mapping lcm
            LEFT JOIN kategori k_orig ON lcm.original_category_id = k_orig.kategoriid
            LEFT JOIN kategori k_trans ON lcm.translated_category_id = k_trans.kategoriid
            LEFT JOIN dil d ON lcm.dilid = d.dilid
            WHERE lcm.translation_status = 'pending'
            ORDER BY lcm.id
        ");
        
        $pendingTranslations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($pendingTranslations)) {
            echo "❌ Bekleyen çeviri bulunamadı.\n";
        } else {
            echo "✅ Bekleyen çeviri sayısı: " . count($pendingTranslations) . "\n";
            foreach ($pendingTranslations as $pending) {
                echo "Mapping ID: {$pending['id']} - '{$pending['original_title']}' → '{$pending['translated_title']}' ({$pending['language_name']})\n";
            }
        }
        
        echo "\n5. SONRAKI ADIMLAR\n";
        echo str_repeat("-", 50) . "\n";
        echo "✅ Kategoriler aktif hale getirildi\n";
        echo "✅ Çeviri mapping'leri pending durumuna alındı\n";
        echo "🔄 Şimdi ContentTranslator.php'yi çalıştırın\n";
        echo "📋 Çeviri işlemi otomatik olarak başlayacak\n";
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
