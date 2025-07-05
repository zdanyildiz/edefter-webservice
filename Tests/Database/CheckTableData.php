<?php
/**
 * Veritabanı Tablo Veri Kontrol Aracı
 * 
 * Bu araç herhangi bir tablodaki verileri hızlıca kontrol eder
 * database.sql dosyasını açmak yerine doğrudan veritabanından okur
 * 
 * Kullanım: 
 * - Tüm tabloları listele: php Tests\Database\CheckTableData.php
 * - Belirli tabloyu kontrol et: php Tests\Database\CheckTableData.php [table_name]
 * - Belirli ID'yi kontrol et: php Tests\Database\CheckTableData.php [table_name] [id]
 */

// Gerekli dosyaları dahil et
require_once __DIR__ . '/../../App/Core/Config.php';
require_once __DIR__ . '/../../App/Database/Database.php';

try {
    // Veritabanı bağlantısı
    $config = new Config();
    $db = new Database();
    $connection = $db->getConnection();
    
    // Komut satırı argümanlarını kontrol et
    $tableName = isset($argv[1]) ? $argv[1] : null;
    $recordId = isset($argv[2]) ? (int)$argv[2] : null;
    
    if (!$tableName) {
        // Tüm tabloları listele
        $query = "SHOW TABLES";
        $stmt = $connection->prepare($query);
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "=== VERİTABANI TABLOLARI ===\n";
        foreach ($tables as $table) {
            // Her tablo için kayıt sayısını al
            $countQuery = "SELECT COUNT(*) as count FROM `$table`";
            $countStmt = $connection->prepare($countQuery);
            $countStmt->execute();
            $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            echo sprintf("%-30s (%d kayıt)\n", $table, $count);
        }
        echo "\n💡 Belirli tablo için: php Tests\\Database\\CheckTableData.php [table_name]\n";
        echo "💡 Belirli kayıt için: php Tests\\Database\\CheckTableData.php [table_name] [id]\n";
        return;
    }
    
    // Tablo var mı kontrol et
    $checkQuery = "SHOW TABLES LIKE :table";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bindParam(':table', $tableName);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() == 0) {
        echo "❌ '$tableName' tablosu bulunamadı!\n";
        return;
    }
    
    if ($recordId) {
        // Belirli ID'yi kontrol et
        $query = "SELECT * FROM `$tableName` WHERE id = :id";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':id', $recordId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "=== $tableName TABLOSU - ID: $recordId ===\n";
            foreach ($result as $column => $value) {
                echo sprintf("%-20s: %s\n", $column, $value);
            }
        } else {
            echo "❌ $tableName tablosunda ID $recordId bulunamadı!\n";
        }
    } else {
        // Tablo yapısını ve örnek verileri göster
        $descQuery = "DESCRIBE `$tableName`";
        $descStmt = $connection->prepare($descQuery);
        $descStmt->execute();
        $columns = $descStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "=== $tableName TABLO YAPISI ===\n";
        echo sprintf("%-20s %-15s %-8s %-8s %-15s %-10s\n", 
            "Sütun", "Tür", "Null", "Key", "Default", "Extra");
        echo str_repeat("-", 80) . "\n";
        
        foreach ($columns as $column) {
            echo sprintf("%-20s %-15s %-8s %-8s %-15s %-10s\n",
                $column['Field'],
                $column['Type'],
                $column['Null'],
                $column['Key'],
                $column['Default'] ?? 'NULL',
                $column['Extra']
            );
        }
        
        // Son 10 kaydı göster
        $dataQuery = "SELECT * FROM `$tableName` ORDER BY id DESC LIMIT 10";
        $dataStmt = $connection->prepare($dataQuery);
        $dataStmt->execute();
        $records = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($records) {
            echo "\n=== SON 10 KAYIT ===\n";
            // Sütun başlıklarını yazdır
            $firstRecord = $records[0];
            foreach (array_keys($firstRecord) as $header) {
                echo sprintf("%-15s ", substr($header, 0, 14));
            }
            echo "\n" . str_repeat("-", count($firstRecord) * 16) . "\n";
            
            // Kayıtları yazdır
            foreach ($records as $record) {
                foreach ($record as $value) {
                    $displayValue = $value;
                    if (strlen($displayValue) > 14) {
                        $displayValue = substr($displayValue, 0, 11) . '...';
                    }
                    echo sprintf("%-15s ", $displayValue);
                }
                echo "\n";
            }
        } else {
            echo "\n📝 Bu tabloda henüz veri yok.\n";
        }
        
        // Toplam kayıt sayısı
        $countQuery = "SELECT COUNT(*) as count FROM `$tableName`";
        $countStmt = $connection->prepare($countQuery);
        $countStmt->execute();
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "\n📊 Toplam kayıt sayısı: $totalCount\n";
        echo "💡 Belirli kayıt için: php Tests\\Database\\CheckTableData.php $tableName [id]\n";
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
