<?php
// ContentTranslator Real-time Monitoring & Control Script
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

function displayMenu() {
    echo "\n=== CONTENTTRANSLATOR KONTROL PANELİ ===\n";
    echo "1. Genel Durum Görüntüle\n";
    echo "2. Detaylı Değişiklik Takibi\n";
    echo "3. ContentTranslator'ı Çalıştır\n";
    echo "4. Logları Temizle\n";
    echo "5. Çeviri Mapping'lerini Sıfırla\n";
    echo "6. Canlı Log Takibi (5 saniye)\n";
    echo "0. Çıkış\n";
    echo "Seçiminiz: ";
}

function getGeneralStatus($pdo) {
    echo "\n=== GENEL DURUM ===\n";
    echo "Zaman: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Kategoriler
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kategori WHERE kategorisil != 1");
    $categoryCount = $stmt->fetch()['total'];
    echo "📁 Aktif Kategoriler: {$categoryCount}\n";
    
    // Sayfalar
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sayfa WHERE sayfasil != 1");
    $pageCount = $stmt->fetch()['total'];
    echo "📄 Aktif Sayfalar: {$pageCount}\n";
    
    // Çeviri mappingler
    $stmt = $pdo->query("SELECT translation_status, COUNT(*) as count FROM language_category_mapping GROUP BY translation_status");
    $categoryMappings = $stmt->fetchAll();
    echo "🔄 Kategori Çeviri Durumu:\n";
    foreach ($categoryMappings as $mapping) {
        echo "   - {$mapping['translation_status']}: {$mapping['count']} adet\n";
    }
    
    $stmt = $pdo->query("SELECT translation_status, COUNT(*) as count FROM language_page_mapping GROUP BY translation_status");
    $pageMappings = $stmt->fetchAll();
    echo "🔄 Sayfa Çeviri Durumu:\n";
    foreach ($pageMappings as $mapping) {
        echo "   - {$mapping['translation_status']}: {$mapping['count']} adet\n";
    }
    
    // Aktif diller
    $stmt = $pdo->query("SELECT dilad, dilkisa FROM dil WHERE dilaktif = 1 ORDER BY dilid");
    $languages = $stmt->fetchAll();
    echo "🌍 Aktif Diller: ";
    foreach ($languages as $i => $lang) {
        echo ($i > 0 ? ', ' : '') . $lang['dilad'] . " ({$lang['dilkisa']})";
    }
    echo "\n";
}

function runContentTranslator() {
    global $documentRoot;
    echo "\n=== CONTENTTRANSLATOR ÇALIŞTIRILIYOR ===\n";
    $contentTranslatorPath = $documentRoot . "/App/Cron/ContentTranslator.php";
    
    if (file_exists($contentTranslatorPath)) {
        echo "ContentTranslator çalıştırılıyor...\n";
        
        // PHP-CLI ile ContentTranslator'ı çalıştır
        $command = "php \"$contentTranslatorPath\"";
        echo "Komut: $command\n\n";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);
        
        echo "=== ÇIKTI ===\n";
        foreach ($output as $line) {
            echo $line . "\n";
        }
        echo "\n=== BİTTİ (Çıkış Kodu: $returnCode) ===\n";
    } else {
        echo "HATA: ContentTranslator.php bulunamadı: $contentTranslatorPath\n";
    }
}

function clearLogs() {
    global $documentRoot;
    echo "\n=== LOGLAR TEMİZLENİYOR ===\n";
    
    $logFiles = [
        $documentRoot . "/Public/Log/Admin/" . date('Y-m-d') . ".log",
        $documentRoot . "/Public/Log/" . date('Y-m-d') . ".log",
        $documentRoot . "/Public/Log/errors.log"
    ];
    
    foreach ($logFiles as $logFile) {
        if (file_exists($logFile)) {
            if (unlink($logFile)) {
                echo "✅ Silindi: " . basename($logFile) . "\n";
            } else {
                echo "❌ Silinemedi: " . basename($logFile) . "\n";
            }
        } else {
            echo "ℹ️  Bulunamadı: " . basename($logFile) . "\n";
        }
    }
}

function resetTranslationMappings($pdo) {
    echo "\n=== ÇEVİRİ MAPPING'LERİ SIFIRLANLIYOR ===\n";
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->query("DELETE FROM language_category_mapping");
        $categoryDeleted = $stmt->rowCount();
        
        $stmt = $pdo->query("DELETE FROM language_page_mapping");
        $pageDeleted = $stmt->rowCount();
        
        $pdo->commit();
        
        echo "✅ Kategori mapping'leri silindi: {$categoryDeleted} adet\n";
        echo "✅ Sayfa mapping'leri silindi: {$pageDeleted} adet\n";
        
    } catch (Exception $e) {
        $pdo->rollback();
        echo "❌ HATA: " . $e->getMessage() . "\n";
    }
}

function liveLogs() {
    global $documentRoot;
    echo "\n=== CANLI LOG TAKİBİ (5 SANİYE) ===\n";
    echo "CTRL+C ile çıkabilirsiniz...\n\n";
    
    $logFile = $documentRoot . "/Public/Log/Admin/" . date('Y-m-d') . ".log";
    $lastSize = file_exists($logFile) ? filesize($logFile) : 0;
    
    for ($i = 0; $i < 12; $i++) { // 5 saniye x 12 = 1 dakika
        if (file_exists($logFile)) {
            $currentSize = filesize($logFile);
            if ($currentSize > $lastSize) {
                $handle = fopen($logFile, 'r');
                fseek($handle, $lastSize);
                while (($line = fgets($handle)) !== false) {
                    if (strpos($line, 'ContentTranslator') !== false) {
                        echo "[" . date('H:i:s') . "] " . trim($line) . "\n";
                    }
                }
                fclose($handle);
                $lastSize = $currentSize;
            }
        }
        
        echo ".";
        sleep(5);
    }
    echo "\n\nLog takibi tamamlandı.\n";
}

// Ana program
try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    while (true) {
        displayMenu();
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1':
                getGeneralStatus($pdo);
                break;
            case '2':
                echo "\nDetaylı monitoring çalıştırılıyor...\n";
                exec("php Tests\\System\\LanguageDetailMonitor.php", $output);
                foreach ($output as $line) echo $line . "\n";
                break;
            case '3':
                runContentTranslator();
                break;
            case '4':
                clearLogs();
                break;
            case '5':
                resetTranslationMappings($pdo);
                break;
            case '6':
                liveLogs();
                break;
            case '0':
                echo "Çıkılıyor...\n";
                exit(0);
            default:
                echo "Geçersiz seçim!\n";
        }
        
        echo "\nDevam etmek için ENTER'a basın...";
        fgets(STDIN);
    }

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
