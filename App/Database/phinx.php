<?php
/**
 * Phinx Konfigürasyon Dosyası - Dinamik Veritabanı Bağlantısı
 * 
 * Bu dosya App/Database/PhinxDatabaseInfo.php sistemini kullanarak dinamik olarak
 * veritabanı bilgilerini alır ve farklı projelerde kullanılabilir hale getirir.
 */

// PhinxDatabaseInfo.php sistemini dahil et
require_once __DIR__ . '/PhinxDatabaseInfo.php';

// Veritabanı bilgilerini dinamik olarak al
$dbInfo = getPhinxDatabaseInfo();

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $dbInfo['serverName'],
            'name' => $dbInfo['database'],
            'user' => $dbInfo['username'],
            'pass' => $dbInfo['password'],
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
