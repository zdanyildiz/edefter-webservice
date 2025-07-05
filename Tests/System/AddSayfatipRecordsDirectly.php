<?php
/**
 * AddSayfatipRecordsDirectly.php - Doğrudan SQL ile sayfatip kayıtları ekleme
 * 
 * Phinx migration yerine, doğrudan veritabanına kayıt ekleme
 */

// GetLocalDatabaseInfo.php dosyasını include et
include_once __DIR__ . DIRECTORY_SEPARATOR . 'GetLocalDatabaseInfo.php';

try {
    // Veritabanı bağlantısı kur
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== SAYFATIP KAYITLARI EKLEME ===\n\n";
    
    // Eklenecek kayıtlar
    $records = [
        ['sayfatipad' => 'Referanslar', 'yetki' => 1, 'gorunum' => 1, 'sayfatipsil' => 0],
        ['sayfatipad' => 'Online Randevu', 'yetki' => 1, 'gorunum' => 1, 'sayfatipsil' => 0]
    ];
    
    foreach ($records as $record) {
        // Önce kayıt var mı kontrol et
        $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM sayfatip WHERE sayfatipad = ?");
        $checkStmt->execute([$record['sayfatipad']]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            echo "⚠️  '{$record['sayfatipad']}' zaten mevcut - atlanıyor\n";
            continue;
        }
        
        // Kayıt yoksa ekle
        $insertStmt = $pdo->prepare("
            INSERT INTO sayfatip (sayfatipad, yetki, gorunum, sayfatipsil) 
            VALUES (?, ?, ?, ?)
        ");
        
        $success = $insertStmt->execute([
            $record['sayfatipad'],
            $record['yetki'],
            $record['gorunum'],
            $record['sayfatipsil']
        ]);
        
        if ($success) {
            $newId = $pdo->lastInsertId();
            echo "✅ '{$record['sayfatipad']}' eklendi (ID: {$newId})\n";
        } else {
            echo "❌ '{$record['sayfatipad']}' eklenemedi\n";
        }
    }
    
    echo "\n=== GÜNCEL SAYFATIP TABLOSU ===\n";
    $stmt = $pdo->query("SELECT * FROM sayfatip ORDER BY sayfatipid DESC LIMIT 5");
    $lastRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($lastRecords as $record) {
        echo "ID: {$record['sayfatipid']} - Ad: '{$record['sayfatipad']}' - Yetki: {$record['yetki']} - Görünüm: {$record['gorunum']} - Sil: {$record['sayfatipsil']}\n";
    }
    
    // Migration kaydı ekle (isteğe bağlı)
    echo "\n=== PHINX MIGRATION KAYDI ===\n";
    try {
        $migrationStmt = $pdo->prepare("
            INSERT INTO phinxlog (version, migration_name, start_time, end_time, breakpoint) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $migrationStmt->execute([
            '20250622110536',
            'AddSayfatipRecords',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
            0
        ]);
        
        echo "✅ Phinx migration kaydı eklendi\n";
    } catch (PDOException $e) {
        echo "⚠️  Phinx migration kaydı eklenemedi (tablo yoksa normal): " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Veritabanı hatası: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Genel hata: " . $e->getMessage() . "\n";
}
