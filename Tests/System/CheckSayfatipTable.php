<?php
/**
 * CheckSayfatipTable.php - Sayfatip tablosunun yapısını kontrol eden script
 */

// GetLocalDatabaseInfo.php dosyasını include et
include_once __DIR__ . DIRECTORY_SEPARATOR . 'GetLocalDatabaseInfo.php';

try {
    // Veritabanı bağlantısı kur
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== SAYFATIP TABLOSU ANALİZİ ===\n\n";
    
    // 1. Tablo yapısını incele
    echo "1. TABLO YAPISI:\n";
    $stmt = $pdo->query("DESCRIBE sayfatip");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "   - {$column['Field']}: {$column['Type']} " . 
             ($column['Null'] === 'NO' ? '(NOT NULL)' : '(NULL OK)') . 
             ($column['Default'] !== null ? " DEFAULT '{$column['Default']}'" : '') . "\n";
    }
    
    // 2. Mevcut kayıtları listele
    echo "\n2. MEVCUT KAYITLAR:\n";
    $stmt = $pdo->query("SELECT * FROM sayfatip ORDER BY sayfatipid");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($records)) {
        echo "   Tabloda kayıt bulunamadı.\n";
    } else {
        foreach ($records as $record) {
            echo "   ID: {$record['sayfatipid']} - Ad: '{$record['sayfatipad']}' - Yetki: {$record['yetki']} - Görünüm: {$record['gorunum']} - Sil: {$record['sayfatipsil']}\n";
        }
    }
    
    // 3. Eklenecek kayıtları kontrol et
    echo "\n3. EKLENECEK KAYITLARI KONTROL:\n";
    $newRecords = ['Referanslar', 'Online Randevu'];
    
    foreach ($newRecords as $newRecord) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM sayfatip WHERE sayfatipad = ?");
        $stmt->execute([$newRecord]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            echo "   ⚠️  '{$newRecord}' zaten mevcut - atlanacak\n";
        } else {
            echo "   ✅ '{$newRecord}' eklenebilir\n";
        }
    }
    
    // 4. Sonraki adımlar
    echo "\n4. SONRAKI ADIMLAR:\n";
    echo "   - Migration oluştur: vendor\\bin\\phinx create AddSayfatipRecords\n";
    echo "   - Migration dosyasını düzenle\n";
    echo "   - Migration çalıştır: vendor\\bin\\phinx migrate\n";
    
} catch (PDOException $e) {
    echo "❌ Veritabanı hatası: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Genel hata: " . $e->getMessage() . "\n";
}
