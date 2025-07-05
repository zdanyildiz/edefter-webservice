<?php
// Tests/Database/CheckLanguageTables.php
// Dil çeviri tablolarının yapısını kontrol etmek için

$documentRoot = str_replace("\\","/",realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);

include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();

echo "=== DIL ÇEVİRİ TABLOLARI KONTROL ===\n";
echo "Tarih: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Veritabanı bağlantısı başarılı\n";
    echo "Veritabanı: {$dbInfo['database']}\n\n";

    // Dil ile ilgili tabloları bul
    $stmt = $pdo->query("SHOW TABLES LIKE '%language%'");
    $languageTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "--- DIL İLE İLGİLİ TABLOLAR ---\n";
    if (empty($languageTables)) {
        echo "❌ Dil ile ilgili tablo bulunamadı\n";
        
        // Alternatif arama - 'dil' içeren tablolar
        $stmt = $pdo->query("SHOW TABLES LIKE '%dil%'");
        $dilTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($dilTables)) {
            echo "\n--- 'DIL' İÇEREN TABLOLAR ---\n";
            foreach ($dilTables as $table) {
                echo "📋 {$table}\n";
            }
        }
        
        // Mapping ile ilgili tablolar
        $stmt = $pdo->query("SHOW TABLES LIKE '%mapping%'");
        $mappingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($mappingTables)) {
            echo "\n--- 'MAPPING' İÇEREN TABLOLAR ---\n";
            foreach ($mappingTables as $table) {
                echo "📋 {$table}\n";
            }
        }
        
    } else {
        foreach ($languageTables as $table) {
            echo "📋 {$table}\n";
        }
    }

    // Tüm tabloları listele ve dil/çeviri ile ilgili olanları tespit et
    echo "\n--- TÜM TABLOLAR (DIL/ÇEVİRİ ANAHTAR KELİMELERİ) ---\n";
    $allTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $keywords = ['dil', 'language', 'translate', 'mapping', 'category', 'page', 'seo'];
    
    foreach ($allTables as $table) {
        foreach ($keywords as $keyword) {
            if (stripos($table, $keyword) !== false) {
                echo "🔍 {$table} (içerik: {$keyword})\n";
                break;
            }
        }
    }

    // Eğer language_category_mapping tablosu varsa yapısını göster
    if (in_array('language_category_mapping', $allTables)) {
        echo "\n--- LANGUAGE_CATEGORY_MAPPING TABLO YAPISI ---\n";
        $stmt = $pdo->query("DESCRIBE language_category_mapping");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']}\n";
        }
        
        // Örnek kayıtlar
        $stmt = $pdo->query("SELECT * FROM language_category_mapping LIMIT 3");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($records)) {
            echo "\n--- ÖRNEK KAYITLAR ---\n";
            foreach ($records as $record) {
                echo "  " . json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
    }

    // Eğer language_page_mapping tablosu varsa yapısını göster
    if (in_array('language_page_mapping', $allTables)) {
        echo "\n--- LANGUAGE_PAGE_MAPPING TABLO YAPISI ---\n";
        $stmt = $pdo->query("DESCRIBE language_page_mapping");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  {$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']}\n";
        }
        
        // Örnek kayıtlar
        $stmt = $pdo->query("SELECT * FROM language_page_mapping LIMIT 3");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($records)) {
            echo "\n--- ÖRNEK KAYITLAR ---\n";
            foreach ($records as $record) {
                echo "  " . json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
    }

} catch (Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
}

echo "\n=== KONTROL TAMAMLANDI ===\n";
?>
