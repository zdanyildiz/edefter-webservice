<?php
/**
 * Site Settings Tablosu Kontrol Aracı
 * 
 * Bu araç site_settings tablosundaki verileri kontrol eder
 * database.sql dosyasını açmak yerine doğrudan veritabanından okur
 * 
 * Kullanım: php Tests\Database\CheckSiteSettings.php [id]
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
    $specificId = isset($argv[1]) ? (int)$argv[1] : null;
    
    if ($specificId) {
        // Belirli ID'yi kontrol et
        $query = "SELECT * FROM site_settings WHERE id = :id";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':id', $specificId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "=== SITE SETTINGS ID: $specificId ===\n";
            foreach ($result as $column => $value) {
                echo "$column: $value\n";
            }
        } else {
            echo "❌ ID $specificId bulunamadı!\n";
        }
    } else {
        // Tüm kayıtları listele
        $query = "SELECT id, setting_name, setting_value, language_id FROM site_settings ORDER BY id";
        $stmt = $connection->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "=== TÜM SITE SETTINGS ===\n";
        echo sprintf("%-4s %-30s %-50s %-5s\n", "ID", "Setting Name", "Setting Value", "Lang");
        echo str_repeat("-", 90) . "\n";
        
        foreach ($results as $row) {
            $settingValue = strlen($row['setting_value']) > 47 ? 
                substr($row['setting_value'], 0, 47) . '...' : 
                $row['setting_value'];
            
            echo sprintf("%-4d %-30s %-50s %-5s\n", 
                $row['id'], 
                $row['setting_name'], 
                $settingValue, 
                $row['language_id']
            );
        }
        
        echo "\n📋 Toplam kayıt sayısı: " . count($results) . "\n";
        echo "💡 Belirli ID detayları için: php Tests\\Database\\CheckSiteSettings.php [id]\n";
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
