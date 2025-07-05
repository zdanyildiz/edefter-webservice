<?php
// Dil ekleme sürecinde detaylı değişiklikleri takip eden script
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== DETAYLI DEĞİŞİKLİK TAKİP SİSTEMİ ===\n";
    echo "Zaman: " . date('Y-m-d H:i:s') . "\n\n";    // 1. Yeni eklenen kategoriler (son 10 dakika)
    $stmt = $pdo->query("
        SELECT kategoriid, kategoriad, dilid, kategoritariholustur 
        FROM kategori 
        WHERE kategoritariholustur >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ORDER BY kategoritariholustur DESC
    ");
    $newCategories = $stmt->fetchAll();
    
    echo "1. SON 10 DAKİKADA EKLENEN KATEGORİLER (" . count($newCategories) . " adet):\n";
    if (count($newCategories) > 0) {
        foreach ($newCategories as $cat) {
            echo "   - ID: {$cat['kategoriid']}, Ad: {$cat['kategoriad']}, Dil: {$cat['dilid']}, Zaman: {$cat['kategoritariholustur']}\n";
        }
    } else {
        echo "   Hiç yeni kategori eklenmedi.\n";
    }

    // 2. Yeni eklenen sayfalar (son 10 dakika)
    $stmt = $pdo->query("
        SELECT sayfaid, sayfaad, sayfatariholustur 
        FROM sayfa 
        WHERE sayfatariholustur >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ORDER BY sayfatariholustur DESC
    ");
    $newPages = $stmt->fetchAll();
    
    echo "\n2. SON 10 DAKİKADA EKLENEN SAYFALAR (" . count($newPages) . " adet):\n";
    if (count($newPages) > 0) {
        foreach ($newPages as $page) {
            echo "   - ID: {$page['sayfaid']}, Ad: {$page['sayfaad']}, Zaman: {$page['sayfatariholustur']}\n";
        }
    } else {
        echo "   Hiç yeni sayfa eklenmedi.\n";
    }

    // 3. Yeni çeviri mapping'leri
    $stmt = $pdo->query("
        SELECT lcm.*, 
               k1.kategoriad as original_name,
               k2.kategoriad as translated_name,
               d.dilad as language_name
        FROM language_category_mapping lcm
        LEFT JOIN kategori k1 ON lcm.original_category_id = k1.kategoriid 
        LEFT JOIN kategori k2 ON lcm.translated_category_id = k2.kategoriid
        LEFT JOIN dil d ON lcm.dilid = d.dilid
        WHERE lcm.last_attempt_date >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) OR lcm.last_attempt_date IS NULL
        ORDER BY lcm.id DESC
        LIMIT 10
    ");
    $categoryMappings = $stmt->fetchAll();
    
    echo "\n3. SON KATEGORİ ÇEVİRİ MAPPING'LERİ (" . count($categoryMappings) . " adet):\n";
    if (count($categoryMappings) > 0) {
        foreach ($categoryMappings as $mapping) {
            echo "   - ID: {$mapping['id']}, Status: {$mapping['translation_status']}\n";
            echo "     Orijinal: {$mapping['original_name']} (ID: {$mapping['original_category_id']})\n";
            echo "     Çeviri: {$mapping['translated_name']} (ID: {$mapping['translated_category_id']})\n";
            echo "     Dil: {$mapping['language_name']}\n";
            if ($mapping['error_message']) {
                echo "     Hata: {$mapping['error_message']}\n";
            }
            echo "\n";
        }
    } else {
        echo "   Hiç kategori mapping bulunamadı.\n";
    }    // 4. Yeni SEO kayıtları
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM seo");
    $seoCount = $stmt->fetch()['total'];
    echo "\n4. TOPLAM SEO KAYITLARI: {$seoCount} adet\n";

    // 5. ContentTranslator logları (son 10 dakika)
    $logFile = $documentRoot . $directorySeparator . 'Public/Log/Admin/' . date('Y-m-d') . '.log';
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        
        $recentLogs = [];
        $tenMinutesAgo = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        
        foreach ($lines as $line) {
            if (strpos($line, 'ContentTranslator') !== false) {
                // Log satırından zaman bilgisini çıkar
                if (preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
                    if ($matches[1] >= $tenMinutesAgo) {
                        $recentLogs[] = $line;
                    }
                }
            }
        }
        
        echo "\n5. SON 10 DAKİKADA CONTENTTRANSLATOR LOGLARI (" . count($recentLogs) . " adet):\n";
        if (count($recentLogs) > 0) {
            foreach ($recentLogs as $log) {
                echo "   " . trim($log) . "\n";
            }
        } else {
            echo "   Hiç log bulunamadı.\n";
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Bu scripti tekrar çalıştırmak için:\n";
    echo "php Tests\\System\\LanguageDetailMonitor.php\n";
    echo str_repeat("=", 60) . "\n";

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
