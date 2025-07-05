<?php
// Başarısız kategori çevirilerini detaylı analiz et
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    echo "=== BAŞARISIZ KATEGORİ ÇEVİRİLERİ ANALİZİ ===\n\n";
    
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Başarısız çevirileri listele
    echo "1. BAŞARISIZ ÇEVİRİLER\n";
    echo str_repeat("-", 60) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            lcm.*,
            k_orig.kategoriad as original_title,
            k_orig.kategoriaktif as original_active,
            k_orig.kategorisil as original_deleted,
            k_trans.kategoriad as translated_title,
            k_trans.kategoriaktif as translated_active,
            k_trans.kategorisil as translated_deleted,
            d.dilad as language_name
        FROM language_category_mapping lcm
        LEFT JOIN kategori k_orig ON lcm.original_category_id = k_orig.kategoriid
        LEFT JOIN kategori k_trans ON lcm.translated_category_id = k_trans.kategoriid
        LEFT JOIN dil d ON lcm.dilid = d.dilid
        WHERE lcm.translation_status = 'failed'
        ORDER BY lcm.id
    ");
    
    $failedTranslations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($failedTranslations)) {
        echo "✅ Başarısız çeviri bulunamadı!\n";
    } else {
        foreach ($failedTranslations as $failed) {
            echo "Mapping ID: {$failed['id']}\n";
            echo "Original ID: {$failed['original_category_id']} → Translated ID: {$failed['translated_category_id']}\n";
            echo "Dil: {$failed['language_name']} (ID: {$failed['dilid']})\n";
            echo "Hata: {$failed['error_message']}\n";
            
            // Orijinal kategori durumu
            if ($failed['original_title']) {
                echo "Orijinal: '{$failed['original_title']}' (Aktif: {$failed['original_active']}, Silinmiş: {$failed['original_deleted']})\n";
            } else {
                echo "❌ Orijinal kategori bulunamadı!\n";
            }
            
            // Çeviri kategori durumu  
            if ($failed['translated_title']) {
                echo "Çeviri: '{$failed['translated_title']}' (Aktif: {$failed['translated_active']}, Silinmiş: {$failed['translated_deleted']})\n";
            } else {
                echo "❌ Çeviri kategori bulunamadı!\n";
            }
            
            echo str_repeat("-", 40) . "\n";
        }
    }
    
    // 2. Pending çevirileri kontrol et
    echo "\n2. BEKLEYEN ÇEVİRİLER\n";
    echo str_repeat("-", 60) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            lcm.*,
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
        echo "❌ Bekleyen çeviri bulunamadı!\n";
        echo "Bu durum ContentTranslator'ın çalışmadığını gösterebilir.\n";
    } else {
        foreach ($pendingTranslations as $pending) {
            echo "Mapping ID: {$pending['id']}\n";
            echo "Original: '{$pending['original_title']}' (ID: {$pending['original_category_id']})\n";
            echo "Translated: '{$pending['translated_title']}' (ID: {$pending['translated_category_id']})\n";
            echo "Dil: {$pending['language_name']}\n";
            echo str_repeat("-", 40) . "\n";
        }
    }
    
    // 3. Başarılı çevirileri kontrol et
    echo "\n3. BAŞARILI ÇEVİRİLER\n";
    echo str_repeat("-", 60) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            lcm.*,
            k_orig.kategoriad as original_title,
            k_trans.kategoriad as translated_title,
            d.dilad as language_name
        FROM language_category_mapping lcm
        LEFT JOIN kategori k_orig ON lcm.original_category_id = k_orig.kategoriid
        LEFT JOIN kategori k_trans ON lcm.translated_category_id = k_trans.kategoriid
        LEFT JOIN dil d ON lcm.dilid = d.dilid
        WHERE lcm.translation_status = 'completed'
        ORDER BY lcm.id
    ");
    
    $completedTranslations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($completedTranslations)) {
        echo "❌ Başarılı çeviri bulunamadı!\n";
    } else {
        echo "✅ Başarılı çeviri sayısı: " . count($completedTranslations) . "\n";
        foreach ($completedTranslations as $completed) {
            $origTitle = $completed['original_title'] ?: '[BOŞ]';
            $transTitle = $completed['translated_title'] ?: '[BOŞ]';
            echo "'{$origTitle}' → '{$transTitle}' ({$completed['language_name']})\n";
        }
    }
    
    // 4. ContentTranslator çalışıyor mu kontrol et
    echo "\n\n4. CONTENTTRANSLATOR DURUM KONTROL\n";
    echo str_repeat("-", 60) . "\n";
    
    // Son 1 saatteki aktiviteyi kontrol et
    $stmt = $pdo->query("
        SELECT COUNT(*) as recent_activity 
        FROM language_category_mapping 
        WHERE last_attempt_date >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $recentActivity = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($recentActivity['recent_activity'] > 0) {
        echo "✅ Son 1 saatte {$recentActivity['recent_activity']} çeviri denemesi yapıldı\n";
    } else {
        echo "❌ Son 1 saatte çeviri aktivitesi yok\n";
        echo "ContentTranslator çalışmıyor olabilir!\n";
    }
    
    // 5. Çözüm önerileri
    echo "\n\n5. ÇÖZÜM ÖNERİLERİ\n";
    echo str_repeat("-", 60) . "\n";
    
    if (!empty($failedTranslations)) {
        echo "❌ Başarısız çeviriler için:\n";
        echo "1. Silinmiş kategorileri temizle\n";
        echo "2. Eksik kategori kayıtlarını düzelt\n";
        echo "3. Failed durumunu pending'e çevir\n\n";
    }
    
    if (empty($pendingTranslations)) {
        echo "❌ Bekleyen çeviri yok:\n";
        echo "1. Yeni çeviri kayıtları oluştur\n";
        echo "2. ContentTranslator'ı manuel çalıştır\n\n";
    }
    
    echo "📋 Genel öneriler:\n";
    echo "- ContentTranslator.php'yi tarayıcıda çalıştır\n";
    echo "- Log dosyalarını kontrol et\n";
    echo "- Kategori-dil eşleştirmelerini doğrula\n";

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
