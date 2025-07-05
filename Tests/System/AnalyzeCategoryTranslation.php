<?php
// ContentTranslator kategori çeviri sistemi detaylı analizi
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    echo "=== CONTENT TRANSLATOR KATEGORİ ÇEVİRİ ANALİZİ ===\n\n";
    
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Language_category_mapping tablosunu kontrol et
    echo "1. KATEGORİ ÇEVİRİ TABLOSU KONTROL\n";
    echo str_repeat("-", 50) . "\n";
    
    $tables = ['language_category_mapping', 'language_category_translation', 'category_translation', 'kategori_ceviri'];
    $foundTable = null;
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $foundTable = $table;
                echo "✅ Tablo bulundu: $table\n";
                break;
            }
        } catch (Exception $e) {
            // Tablo yok, devam et
        }
    }
    
    if (!$foundTable) {
        echo "❌ Kategori çeviri tablosu bulunamadı!\n";
        echo "Aranan tablolar: " . implode(', ', $tables) . "\n\n";
        
        // Tüm tabloları listele
        echo "Mevcut tablolar:\n";
        $stmt = $pdo->query("SHOW TABLES");
        $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($allTables as $tableName) {
            if (stripos($tableName, 'category') !== false || stripos($tableName, 'kategori') !== false || stripos($tableName, 'language') !== false) {
                echo "- $tableName\n";
            }
        }
    } else {
        // Tablo yapısını kontrol et
        echo "\nTablo yapısı ($foundTable):\n";
        $stmt = $pdo->query("DESCRIBE $foundTable");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // İçerik kontrolü
        echo "\nTablo içeriği (ilk 10 kayıt):\n";
        $stmt = $pdo->query("SELECT * FROM $foundTable LIMIT 10");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($records)) {
            echo "❌ Tablo boş!\n";
        } else {
            foreach ($records as $index => $record) {
                echo "Kayıt " . ($index + 1) . ": " . json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
    }
    
    // 2. AdminLanguage sınıfını kontrol et
    echo "\n\n2. ADMINLANGUAGE SINIFI KONTROL\n";
    echo str_repeat("-", 50) . "\n";
    
    // AdminLanguage.php dosyasında getPendingCategoryTranslations fonksiyonunu ara
    $adminLanguageFile = $documentRoot . '/App/Model/Admin/AdminLanguage.php';
    if (file_exists($adminLanguageFile)) {
        $content = file_get_contents($adminLanguageFile);
        
        if (strpos($content, 'getPendingCategoryTranslations') !== false) {
            echo "✅ AdminLanguage->getPendingCategoryTranslations() fonksiyonu mevcut\n";
            
            // Fonksiyon içeriğini al
            $pattern = '/function\s+getPendingCategoryTranslations\s*\([^)]*\)\s*\{([^}]+(?:\{[^}]*\}[^}]*)*)\}/s';
            if (preg_match($pattern, $content, $matches)) {
                echo "Fonksiyon içeriği:\n";
                echo substr($matches[0], 0, 500) . "...\n";
            }
        } else {
            echo "❌ AdminLanguage->getPendingCategoryTranslations() fonksiyonu bulunamadı!\n";
            
            // Benzer fonksiyonları ara
            echo "Mevcut language/category fonksiyonları:\n";
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                if (stripos($line, 'function') !== false && 
                    (stripos($line, 'category') !== false || stripos($line, 'language') !== false || stripos($line, 'translation') !== false)) {
                    echo "- " . trim($line) . "\n";
                }
            }
        }
    } else {
        echo "❌ AdminLanguage.php dosyası bulunamadı!\n";
    }
    
    // 3. Kategori tablosunu kontrol et
    echo "\n\n3. KATEGORİ TABLOSU KONTROL\n";
    echo str_repeat("-", 50) . "\n";
    
    try {
        $stmt = $pdo->query("DESCRIBE kategori");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Kategori tablosu sütunları:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Kategori örnekleri
        echo "\nKategori örnekleri (ilk 5):\n";
        $stmt = $pdo->query("SELECT kategoriid, kategoriad, kategorilink, dilid FROM kategori LIMIT 5");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($categories as $cat) {
            echo "ID: {$cat['kategoriid']}, Ad: {$cat['kategoriad']}, Link: {$cat['kategorilink']}, Dil: {$cat['dilid']}\n";
        }
        
        // Dil kontrolü
        echo "\nKategorilerin dil dağılımı:\n";
        $stmt = $pdo->query("SELECT dilid, COUNT(*) as count FROM kategori GROUP BY dilid");
        $langStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($langStats as $stat) {
            echo "Dil ID {$stat['dilid']}: {$stat['count']} kategori\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Kategori tablosu hatası: " . $e->getMessage() . "\n";
    }
    
    // 4. Log dosyasını kontrol et
    echo "\n\n4. LOG DOSYASI KONTROL\n";
    echo str_repeat("-", 50) . "\n";
    
    $today = date('Y-m-d');
    $logFile = $documentRoot . "/Public/Log/Admin/$today.log";
    
    if (file_exists($logFile)) {
        echo "✅ Log dosyası mevcut: $today.log\n";
        
        // ContentTranslator loglarını ara
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        
        echo "Son ContentTranslator logları:\n";
        $found = false;
        foreach (array_reverse($lines) as $line) {
            if (stripos($line, 'ContentTranslator') !== false || 
                stripos($line, 'kategori') !== false ||
                stripos($line, 'category') !== false) {
                echo "- $line\n";
                $found = true;
            }
            if ($found && substr_count(implode("\n", array_reverse($lines)), "\n") > 10) break;
        }
        
        if (!$found) {
            echo "❌ ContentTranslator ile ilgili log bulunamadı\n";
        }
        
    } else {
        echo "❌ Log dosyası bulunamadı: $logFile\n";
    }
    
    echo "\n\n=== SONUÇ VE ÖNERİLER ===\n";
    echo "Bu analiz sonucunda kategori çeviri sisteminin durumu belirlendi.\n";
    echo "Eğer kategori çevirileri çalışmıyorsa, yukarıdaki bilgilere göre problemi tespit edebiliriz.\n";

} catch (Exception $e) {
    echo "GENEL HATA: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}
?>
