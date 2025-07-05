<?php

// Phinx için basit veritabanı bilgileri alımı
require_once __DIR__ . '/Tests/System/GetLocalDatabaseInfo.php';

// Veritabanı bilgilerini al
$dbInfo = getLocalDatabaseInfo();

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
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
];            'adapter' => 'mysql',
            'host' => $config->dbServerName,
            'name' => $config->dbName,
            'user' => $config->dbUsername,
            'pass' => $config->dbPassword,
            'port' => 3306,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
