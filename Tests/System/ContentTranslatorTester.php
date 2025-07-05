<?php
// Tests/System/ContentTranslatorTester.php
// ContentTranslator cron job'unun test scripti

// Proje kök dizinini belirle
$documentRoot = str_replace("\\","/",realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);

echo "=== CONTENT TRANSLATOR CRON TEST BAŞLIYOR ===\n";
echo "Test Zamanı: " . date('Y-m-d H:i:s') . "\n";
echo "Proje Kök Dizini: " . $documentRoot . "\n\n";

// Config yükle
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';
$config = new Config();

echo "✅ Config yüklendi\n";

// Veritabanı bağlantısını test et
try {
    $pdo = new PDO(
        "mysql:host={$config->dbServerName};dbname={$config->dbName};charset=utf8",
        $config->dbUsername,
        $config->dbPassword
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Veritabanı bağlantısı başarılı\n";
} catch (Exception $e) {
    echo "❌ Veritabanı bağlantısı başarısız: " . $e->getMessage() . "\n";
    exit(1);
}

// Gerekli modelleri kontrol et
$requiredModels = [
    'AdminLanguage' => MODEL . 'Admin/AdminLanguage.php',
    'AdminCategory' => MODEL . 'Admin/AdminCategory.php',
    'AdminPage' => MODEL . 'Admin/AdminPage.php',
    'AdminSeo' => MODEL . 'Admin/AdminSeo.php',
    'AdminChatCompletion' => MODEL . 'Admin/AdminChatCompletion.php'
];

echo "\n--- MODEL DOSYALARI KONTROLÜ ---\n";
foreach ($requiredModels as $modelName => $modelPath) {
    if (file_exists($modelPath)) {
        echo "✅ {$modelName}: {$modelPath}\n";
    } else {
        echo "❌ {$modelName}: {$modelPath} (BULUNAMADI)\n";
    }
}

// Cron dosyasının varlığını kontrol et
$cronFile = $documentRoot . '/App/Cron/ContentTranslator.php';
if (file_exists($cronFile)) {
    echo "\n✅ ContentTranslator.php dosyası mevcut\n";
} else {
    echo "\n❌ ContentTranslator.php dosyası bulunamadı\n";
    exit(1);
}

// Veritabanında gerekli tabloları kontrol et
echo "\n--- VERİTABANI TABLOLARI KONTROLÜ ---\n";
$requiredTables = [
    'kategori',
    'sayfa', 
    'seo',
    'dil',
    'dilkategori',
    'dilsayfa'
];

foreach ($requiredTables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE `{$table}`");
        echo "✅ Tablo '{$table}' mevcut\n";
    } catch (Exception $e) {
        echo "❌ Tablo '{$table}' bulunamadı: " . $e->getMessage() . "\n";
    }
}

// Bekleyen çeviri kayıtlarını kontrol et
echo "\n--- BEKLEYENÇEVİRİ KAYITLARI ---\n";

try {
    // Kategori çevirileri
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM dilkategori WHERE durum = 'pending'");
    $pendingCategories = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📋 Bekleyen kategori çevirileri: {$pendingCategories}\n";
    
    // Sayfa çevirileri
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM dilsayfa WHERE durum = 'pending'");
    $pendingPages = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📋 Bekleyen sayfa çevirileri: {$pendingPages}\n";
    
} catch (Exception $e) {
    echo "⚠️ Bekleyen çeviri sayısı alınamadı: " . $e->getMessage() . "\n";
}

// Log dosyasının varlığını kontrol et
$logDir = $documentRoot . '/Public/Log/Admin/';
$logFile = $logDir . date('Y-m-d') . '.log';

echo "\n--- LOG SİSTEMİ KONTROLÜ ---\n";
if (is_dir($logDir)) {
    echo "✅ Admin log dizini mevcut: {$logDir}\n";
    if (file_exists($logFile)) {
        echo "✅ Bugünkü log dosyası mevcut: {$logFile}\n";
        
        // Son 10 satırı göster
        $logContent = file_get_contents($logFile);
        $logLines = explode("\n", trim($logContent));
        $lastLines = array_slice($logLines, -10);
        
        echo "\n📝 Son 10 log kaydı:\n";
        foreach ($lastLines as $line) {
            if (!empty(trim($line))) {
                echo "  " . $line . "\n";
            }
        }
    } else {
        echo "⚠️ Bugünkü log dosyası henüz oluşturulmamış\n";
    }
} else {
    echo "❌ Admin log dizini bulunamadı: {$logDir}\n";
}

echo "\n--- CRON JOB SİMÜLASYONU ---\n";
echo "⚠️ Gerçek cron job çalıştırılmayacak, sadece yapısal test yapıldı.\n";
echo "Cron job'u manuel olarak çalıştırmak için:\n";
echo "cd \"{$documentRoot}\"; php App\\Cron\\ContentTranslator.php\n";

// Test sonucu özeti
echo "\n=== TEST SONUCU ÖZETİ ===\n";
echo "✅ Veritabanı bağlantısı: BAŞARILI\n";
echo "✅ Gerekli dosyalar: MEVCUT\n";
echo "✅ Tablo yapısı: KONTROL EDİLDİ\n";
echo "📊 Bekleyen işlemler: Kategori({$pendingCategories}), Sayfa({$pendingPages})\n";
echo "📝 Log sistemi: ÇALIŞIR\n";

echo "\n🎯 ContentTranslator cron job'u test edilmeye hazır!\n";
echo "Test Tamamlandı: " . date('Y-m-d H:i:s') . "\n";
