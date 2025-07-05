<?php
// Çeviri tablolarını kontrol eden script
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();

try {
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Çeviri tablosu yapısını kontrol et
    echo "=== ÇEVİRİ TABLOSU KONTROLÜ ===\n";
    
    $tables = ['ceviritablosu', 'ceviritablodurum', 'kategori', 'sayfalistekategori', 'seo'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("DESCRIBE {$table}");
            echo "\n=== {$table} TABLOSU YAPISI ===\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo $row['Field'] . " | " . $row['Type'] . "\n";
            }
            
            // Örnek kayıtları göster
            $stmt = $pdo->query("SELECT * FROM {$table} LIMIT 3");
            echo "=== ÖRNEK KAYITLAR ===\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
            }
        } catch (Exception $e) {
            echo "HATA - {$table}: " . $e->getMessage() . "\n";
        }
    }
    
    // Ana dil kontrolü
    echo "\n=== ANA DİL KONTROLÜ ===\n";
    $stmt = $pdo->query("SELECT dilid, dilad, anadil FROM dil WHERE anadil = 1");
    $mainLanguage = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($mainLanguage) {
        echo "Ana Dil: " . $mainLanguage['dilad'] . " (ID: " . $mainLanguage['dilid'] . ")\n";
    } else {
        echo "Ana dil bulunamadı!\n";
    }
    
    // Çeviri durumları kontrolü
    echo "\n=== ÇEVİRİ DURUMLARI ===\n";
    $stmt = $pdo->query("SELECT * FROM ceviritablodurum");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['ceviritablodurumid'] . " - " . $row['ceviritablodurumad'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage() . "\n";
}
