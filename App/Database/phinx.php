<?php
/**
 * Phinx Configuration File
 *
 * Bu dosya Phinx migration ve seed sisteminin konfigürasyon ayarlarını içerir.
 * ENV dosyasından veritabanı bilgilerini okur ve ortam ayarlarını yapar.
 */

// ENV helper'ı yükle
$rootPath = dirname(dirname(__DIR__));
require_once $rootPath . '/App/Helpers/EnvHelper.php';

// ENV dosyası varsa yükle
if (file_exists($rootPath . '/.env')) {
    EnvHelper::load();
}

return [
    'paths' => [
        'migrations' => $rootPath . '/App/Database/migrations',
        'seeds' => $rootPath . '/App/Database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => EnvHelper::get('DB_HOST', 'localhost'),
            'name' => EnvHelper::get('DB_DATABASE', 'test_db'),
            'user' => EnvHelper::get('DB_USERNAME', 'root'),
            'pass' => EnvHelper::get('DB_PASSWORD', ''),
            'port' => '3306',
            'charset' => 'utf8',
            'table_prefix' => '',
            'table_suffix' => '',
        ],
        'production' => [
            'adapter' => 'mysql',
            'host' => EnvHelper::get('DB_HOST', 'localhost'),
            'name' => EnvHelper::get('DB_DATABASE', 'production_db'),
            'user' => EnvHelper::get('DB_USERNAME', 'root'),
            'pass' => EnvHelper::get('DB_PASSWORD', ''),
            'port' => '3306',
            'charset' => 'utf8',
            'table_prefix' => '',
            'table_suffix' => '',
        ]
    ],
    'version_order' => 'creation'
];
