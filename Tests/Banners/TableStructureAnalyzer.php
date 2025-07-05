<?php
/**
 * Banner Tablo Yapısı Analiz Scripti
 */

try {
    $pdo = new PDO("mysql:host=localhost;dbname=yeni.globalpozitif.com.tr;charset=utf8mb4", 'root', 'Global2019*', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "=== BANNER TABLOLARI YAPISINI ÖĞRENME ===\n\n";
    
    // Önce hangi tabloların banner ile ilgili olduğunu bulalım
    $stmt = $pdo->query("SHOW TABLES LIKE '%banner%'");
    $bannerTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Banner ile ilgili tablolar:\n";
    foreach ($bannerTables as $table) {
        echo "- {$table}\n";
    }
    echo "\n";
    
    // Her tablo için yapıyı göster
    foreach ($bannerTables as $table) {
        echo "📊 {$table} TABLOSU:\n";
        echo str_repeat("=", 40) . "\n";
        
        $stmt = $pdo->query("DESCRIBE {$table}");
        while ($row = $stmt->fetch()) {
            echo sprintf("%-20s | %-15s | %s", 
                $row['Field'], 
                $row['Type'], 
                ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL')
            );
            if ($row['Key']) echo " | {$row['Key']}";
            if ($row['Default'] !== null) echo " | DEFAULT: {$row['Default']}";
            echo "\n";
        }
        
        // Örnek veri göster
        echo "\nÖrnek veriler (ilk 3 kayıt):\n";
        $stmt = $pdo->query("SELECT * FROM {$table} LIMIT 3");
        $sampleData = $stmt->fetchAll();
        if (!empty($sampleData)) {
            $columns = array_keys($sampleData[0]);
            echo "Kolonlar: " . implode(', ', $columns) . "\n";
            foreach ($sampleData as $index => $row) {
                echo "Kayıt " . ($index + 1) . ": ";
                foreach ($row as $key => $value) {
                    if (strlen($value) > 30) {
                        $value = substr($value, 0, 30) . '...';
                    }
                    echo "{$key}='{$value}' ";
                }
                echo "\n";
            }
        }
        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
