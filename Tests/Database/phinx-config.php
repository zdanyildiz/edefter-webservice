<?php
/**
 * Phinx Migration Sistemi için Basit Yapılandırma Scripti
 *
 * Bu betik, Phinx'in veritabanına düzgün bağlanabilmesi için basit bir wrapper sağlar.
 * Config sınıfı ile oluşabilecek karmaşık hataları atlar ve doğrudan veritabanı bilgilerini tanımlar.
 */

// Key.php'den encryption key'i al
$keyFile = __DIR__ . '/App/Config/Key.php';
if (file_exists($keyFile)) {
    require_once $keyFile;
}

// Helper sınıfını dahil et (decrypt fonksiyonu için)
require_once __DIR__ . '/App/Helpers/Helper.php';
$helper = new Helper();

// Sql.php'den encrypted credentials'ları yükle
$sqlFile = __DIR__ . '/App/Config/Sql.php';
if (file_exists($sqlFile)) {
    require_once $sqlFile;
}

// Yerel domaini kontrol et
$localDomainScript = __DIR__ . '/Tests/System/GetLocalDomain.php';
if (file_exists($localDomainScript)) {
    $localDomain = trim(shell_exec('php ' . escapeshellarg($localDomainScript)));
} else {
    $localDomain = 'l.erhanozel';
}

// Localhost check - eğer 'l.' ile başlıyorsa yerel veritabanı bağlantısı kullan
$isLocalhost = (strpos($localDomain, 'l.') === 0);

// Değişkenleri kontrol et ve varsayılan değerler ekle
if (!isset($dbLocalServerName) || !isset($dbLocalUsername) || !isset($dbLocalPassword) || !isset($dbLocalName)) {
    // Yerel veritabanı bilgileri tanımlanmamışsa, varsayılan değerler
    $dbHost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName = 'erhanozel';
} else {
    // Veritabanı bilgilerini çöz (decrypt)
    if ($isLocalhost) {
        $dbHost = isset($key) ? $helper->decrypt($dbLocalServerName, $key) : $dbLocalServerName;
        $dbUsername = isset($key) ? $helper->decrypt($dbLocalUsername, $key) : $dbLocalUsername;
        $dbPassword = isset($key) ? $helper->decrypt($dbLocalPassword, $key) : $dbLocalPassword;
        $dbName = isset($key) ? $helper->decrypt($dbLocalName, $key) : $dbLocalName;
    } else {
        $dbHost = isset($key) ? $helper->decrypt($dbServerName, $key) : $dbServerName;
        $dbUsername = isset($key) ? $helper->decrypt($dbUsername, $key) : $dbUsername;
        $dbPassword = isset($key) ? $helper->decrypt($dbPassword, $key) : $dbPassword;
        $dbName = isset($key) ? $helper->decrypt($dbName, $key) : $dbName;
    }
}

// Phinx konfigürasyon ayarlarını döndür
return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $dbHost,
            'name' => $dbName,
            'user' => $dbUsername,
            'pass' => $dbPassword,
            'port' => 3306,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
