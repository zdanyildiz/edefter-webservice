<?php
// Test Config yükleme
if (!defined('DIRECTORY_SEPARATOR')) {
    define('DIRECTORY_SEPARATOR', '/');
}
if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
}
if (!defined('APP')) {
    define('APP', ROOT . 'App' . DIRECTORY_SEPARATOR);
}

require_once ROOT . 'vendor/autoload.php';
require_once APP . 'Helpers/Helper.php';
require_once APP . 'Core/Config.php';

// Config sınıfını oluştur
$config = new Config();

// Veritabanı bilgilerini kontrol et
echo "--- Database Connection Info ---\n";
echo "Host: " . ($config->dbServerName ?: "UNDEFINED") . "\n";
echo "User: " . ($config->dbUsername ?: "UNDEFINED") . "\n";
echo "Pass: " . (empty($config->dbPassword) ? "EMPTY" : "***HIDDEN***") . "\n";
echo "DB Name: " . ($config->dbName ?: "UNDEFINED") . "\n";
echo "\n";

echo "--- Environment Info ---\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? "UNDEFINED") . "\n";
echo "Localhost: " . ($config->localhost ? "YES" : "NO") . "\n";

// GetLocalDomain betiğini çalıştır ve sonucu göster
echo "\n--- GetLocalDomain Output ---\n";
$localDomainScript = ROOT . 'Tests/System/GetLocalDomain.php';
if (file_exists($localDomainScript)) {
    echo "Script exists: YES\n";
    echo "Output: " . trim(shell_exec('php ' . escapeshellarg($localDomainScript))) . "\n";
} else {
    echo "Script exists: NO\n";
    echo "Default domain: l.erhanozel\n";
}
