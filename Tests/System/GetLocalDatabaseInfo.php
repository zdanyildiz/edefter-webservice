<?php
/**
 * GetLocalDatabaseInfo.php - Yerel Veritabanı Bilgileri Bulucu Betiği
 *
 * Bu betik, App/Config/Sql.php ve Key.php dosyalarını okuyarak şifrelenmiş
 * yerel veritabanı bilgilerini çözer ve döndürür.
 * 
 * ⚠️ PORTABLE TASARIM: Bu betik farklı projelerde de kullanılabilir.
 * Sadece aynı klasör yapısına sahip projelerde çalışır:
 * - App/Config/Sql.php (şifrelenmiş DB bilgileri)
 * - App/Config/Key.php (şifre çözme anahtarı)
 * - App/Helpers/Helper.php (decrypt metodu)
 * 
 * GetLocalDomain.php benzeri yapıda tasarlandı.
 * 
 * Kullanımı: 
 * - Doğrudan çalıştırma: php Tests/System/GetLocalDatabaseInfo.php
 * - Include ederek: include 'Tests/System/GetLocalDatabaseInfo.php'; getLocalDatabaseInfo();
 * - Phinx.php'de: Dinamik veritabanı konfigürasyonu için kullanılır
 */

// Root dizini belirleme
$rootDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;

// Gerekli dosya yolları
$sqlFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Sql.php';
$keyFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Key.php';
$helperFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'Helper.php';

// Dosya varlık kontrolü
$requiredFiles = [
    'Key.php' => $keyFile,
    'Sql.php' => $sqlFile,
    'Helper.php' => $helperFile
];

foreach ($requiredFiles as $name => $path) {
    if (!file_exists($path)) {
        echo "HATA: {$name} dosyası bulunamadı: {$path}\n";
        exit(1);
    }
}

// Dosyaları include et
include_once $keyFile;      // $key değişkenini yükler
include_once $sqlFile;      // Encrypted DB değişkenlerini yükler
include_once $helperFile;   // Helper sınıfını yükler

// Helper örneği oluştur
$helper = new Helper();

/**
 * Yerel veritabanı bilgilerini döndüren fonksiyon
 * @return array Yerel veritabanı bilgileri (serverName, username, password, database)
 */
function getLocalDatabaseInfo() {
    global $helper, $key, $dbLocalName, $dbLocalUsername, $dbLocalPassword, $dbLocalServerName;
    
    try {
        return [
            'serverName' => $helper->decrypt($dbLocalServerName, $key),
            'username' => $helper->decrypt($dbLocalUsername, $key),
            'password' => $helper->decrypt($dbLocalPassword, $key),
            'database' => $helper->decrypt($dbLocalName, $key)
        ];
    } catch (Exception $e) {
        echo "HATA: Veritabanı bilgileri çözülemedi: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Eğer dosya doğrudan çalıştırılırsa bilgileri göster
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    $dbInfo = getLocalDatabaseInfo();
    
    echo "=== YEREL VERİTABANI BİLGİLERİ ===\n";
    echo "Server: " . $dbInfo['serverName'] . "\n";
    echo "Username: " . $dbInfo['username'] . "\n";
    echo "Password: " . $dbInfo['password'] . "\n";
    echo "Database: " . $dbInfo['database'] . "\n";
}