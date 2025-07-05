<?php
// Tests/System/AITranslationTest.php
// AI çeviri sisteminin yeni disclaimer ile testini yapalım

echo "=== AI ÇEVIRI TEST (DISCLAIMER İLE) ===\n";
echo "Test Zamanı: " . date('Y-m-d H:i:s') . "\n\n";

// Config ve veritabanı bağlantısı
include_once 'App/Core/Config.php';
$config = new Config();

include_once DATABASE . "AdminDatabase.php";
$db = new AdminDatabase($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

include_once MODEL.'Admin/AdminChatCompletion.php';
$adminChatCompletion = new AdminChatCompletion($db, 1);

// Test HTML içeriği
$testHtmlContent = '<p>Bu bir test sayfasıdır.</p>
<p><strong>Önemli:</strong> Bu içerik yapay zeka ile çevrilmiştir.</p>
<ul>
<li>Birinci madde</li>
<li>İkinci madde</li>
</ul>
<p>Şirket adresi: [firmaadres]</p>';

echo "--- ORIJINAL HTML ---\n";
echo $testHtmlContent . "\n\n";

echo "--- İNGILIZCE ÇEVIRISİ (YENİ DISCLAIMER İLE) ---\n";
try {
    $translatedContent = $adminChatCompletion->translateHtmlContent($testHtmlContent, "English");
    echo $translatedContent . "\n\n";
    
    // Disclaimer kontrolü
    if (strpos($translatedContent, 'artificial intelligence') !== false || 
        strpos($translatedContent, 'AI') !== false) {
        echo "✅ BAŞARILI: AI disclaimer eklendi!\n";
    } else {
        echo "⚠️ UYARI: AI disclaimer tespit edilemedi.\n";
    }
    
} catch (Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
}

echo "\n=== TEST TAMAMLANDI ===\n";
?>
