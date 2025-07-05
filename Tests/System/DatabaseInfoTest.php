<?php
/**
 * DatabaseInfoTest.php - GetLocalDatabaseInfo fonksiyonunu test eden örnek
 * 
 * Bu dosya, GetLocalDatabaseInfo.php dosyasının nasıl include edilerek
 * kullanılabileceğini gösterir.
 */

echo "=== VERİTABANI BİLGİLERİ TEST ETTİRME ===\n\n";

// GetLocalDatabaseInfo.php dosyasını include et
include_once __DIR__ . DIRECTORY_SEPARATOR . 'GetLocalDatabaseInfo.php';

// Fonksiyonu çağır
$dbInfo = getLocalDatabaseInfo();

echo "1. FONKSİYON ÇAĞRISI SONUCU:\n";
echo "   Server: " . $dbInfo['serverName'] . "\n";
echo "   Database: " . $dbInfo['database'] . "\n";
echo "   Username: " . $dbInfo['username'] . "\n";
echo "   Password: " . str_repeat('*', strlen($dbInfo['password'])) . " (gizlendi)\n\n";

echo "2. PDO BAĞLANTI TESTI:\n";
try {
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "   ✅ Veritabanı bağlantısı başarılı!\n";    // Basit bir test sorgusu
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   📊 Aktif Veritabanı: " . $result['current_db'] . "\n";
    
    // Kullanıcı bilgisini ayrı sorgu ile alalım
    try {
        $stmt2 = $pdo->query("SELECT USER() as current_user");
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        echo "   👤 Aktif Kullanıcı: " . $result2['current_user'] . "\n";
    } catch (PDOException $e2) {
        echo "   👤 Kullanıcı bilgisi alınamadı: " . $e2->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo "   ❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "\n";
}

echo "\n3. JSON ÇIKTI:\n";
echo json_encode($dbInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
