<?php
/**
 * PowerShell PHP Inline Komut Hata Demonstrasyonu
 * Bu script, PowerShell'de inline PHP komutlarının neden hata verdiğini gösterir
 */

echo "=== PowerShell PHP Inline Komut Hataları ===\n\n";

echo "❌ PowerShell'de ÇALIŞMAYAN inline komutlar:\n";
echo "1. php -r \"echo \\\$test;\" → PowerShell \$ karakterini yanlış yorumlar\n";
echo "2. php -r \"echo 'test';\" → Tırnak işaretleri çakışır\n";
echo "3. php -r \"\\\$arr[key];\" → Köşeli parantezler syntax hatası\n";
echo "4. php -r \"func(); other();\" → Noktalı virgül komut ayırıcı sanılır\n\n";

echo "✅ PowerShell'de ÇALIŞAN yaklaşım:\n";
echo "1. Ayrı .php dosyası oluştur (bu script gibi)\n";
echo "2. PowerShell'de sadece: php path\\to\\script.php\n";
echo "3. Tüm karmaşık PHP kodu dosya içinde yazılır\n\n";

// Gerçek bir örnek: veritabanı bağlantısı
echo "=== Gerçek Örnek: Veritabanı Testi ===\n";

try {
    // Veritabanı bilgilerini al
    include_once 'GetLocalDatabaseInfo.php';
    $dbInfo = getLocalDatabaseInfo();
    
    echo "✅ Veritabanı bilgileri başarıyla alındı:\n";
    echo "Server: {$dbInfo['serverName']}\n";
    echo "Database: {$dbInfo['database']}\n";
    echo "Username: {$dbInfo['username']}\n";
    
    // Bu tür karmaşık işlemler inline komutla ASLA yapılamaz
    $pdo = new PDO(
        "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8",
        $dbInfo['username'],
        $dbInfo['password']
    );
    
    echo "✅ Veritabanı bağlantısı başarılı!\n";
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

echo "\n=== Sonuç ===\n";
echo "PowerShell'de PHP çalıştırırken:\n";
echo "- ASLA 'php -r' kullanma\n";
echo "- Her zaman ayrı .php dosyası oluştur\n";
echo "- Dosya yollarında \\(backslash) kullan\n";
echo "- Komut ayırıcı olarak ; kullan\n";
?>
