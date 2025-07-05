<?php
/**
 * PhinxDatabaseInfo.php - Phinx için Özel Veritabanı Bilgisi Çözücü
 * 
 * Bu dosya sadece Phinx için tasarlanmış basit bir veritabanı bilgisi çözücüdür.
 * GetLocalDatabaseInfo.php'den farklı olarak global değişkenlere bağımlı değildir.
 */

function getPhinxDatabaseInfo() {
    // Root dizinine çıkılması gerekiyor (App/Database'den 2 level yukarı)
    $rootDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
    
    // Gerekli dosya yolları
    $keyFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Key.php';
    $sqlFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Sql.php';
    $helperFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'Helper.php';
    
    // Varsayılan değerler
    $dbInfo = [
        'serverName' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'database_name'
    ];
    
    try {
        // Dosyaları include et
        if (file_exists($keyFile)) {
            include $keyFile; // $key değişkenini yükler
        }
        if (file_exists($sqlFile)) {
            include $sqlFile; // DB değişkenlerini yükler
        }
        if (file_exists($helperFile)) {
            include $helperFile; // Helper sınıfını yükler
        }
        
        // Şifre çözme işlemi
        if (class_exists('Helper') && isset($key, $dbLocalServerName, $dbLocalUsername, $dbLocalPassword, $dbLocalName)) {
            $helper = new Helper();
            
            $dbInfo = [
                'serverName' => $helper->decrypt($dbLocalServerName, $key),
                'username' => $helper->decrypt($dbLocalUsername, $key),
                'password' => $helper->decrypt($dbLocalPassword, $key),
                'database' => $helper->decrypt($dbLocalName, $key)
            ];
        }
    } catch (Exception $e) {
        error_log("PhinxDatabaseInfo: " . $e->getMessage());
    }
    
    return $dbInfo;
}
