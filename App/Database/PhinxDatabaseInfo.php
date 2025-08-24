<?php
/**
 * PhinxDatabaseInfo.php - Phinx için Özel Veritabanı Bilgisi Çözücü
 *
 * Bu dosya sadece Phinx için tasarlanmış basit bir veritabanı bilgisi çözücüdür.
 * GetLocalDatabaseInfo.php'den farklı olarak global değişkenlere bağımlı değildir.
 */

function getPhinxDatabaseInfo() {
    // EnvHelper'ı yükle
    require_once dirname(__DIR__) . '/Helpers/EnvHelper.php';

    // .env dosyasını yükle
    EnvHelper::load();

    // .env dosyasından veritabanı bilgilerini al
    $dbInfo = [
        'serverName' => EnvHelper::get('DB_LOCAL_HOST', 'localhost'),
        'username' => EnvHelper::get('DB_LOCAL_USERNAME', 'root'),
        'password' => EnvHelper::get('DB_LOCAL_PASSWORD', ''),
        'database' => EnvHelper::get('DB_LOCAL_DATABASE', 'test_db')
    ];

    return $dbInfo;
}