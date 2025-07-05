<?php
/**
 * Phinx Migration Durumunu Manuel Ayarlama Scripti
 *
 * Bu betik, halihazırda veritabanında bulunan tabloları oluşturmaya çalışan
 * ilk migration'ı "tamamlandı" olarak işaretlemek için kullanılır.
 */

// Bağımlılıkları dahil et
if (!defined('DIRECTORY_SEPARATOR')) {
    define('DIRECTORY_SEPARATOR', '/');
}
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}
if (!defined('APP')) {
    define('APP', ROOT . 'App' . DIRECTORY_SEPARATOR);
}
if (!defined('CONF')) {
    define('CONF', APP . 'Config' . DIRECTORY_SEPARATOR);
}

require_once ROOT . 'vendor/autoload.php';
require_once APP . 'Helpers/Helper.php';
require_once CONF . 'Key.php';
require_once CONF . 'Sql.php';
require_once APP . 'Core/Config.php';

// HTTP_HOST ayarla
$localDomainScript = ROOT . 'Tests/System/GetLocalDomain.php';
if (file_exists($localDomainScript)) {
    $_SERVER['HTTP_HOST'] = trim(shell_exec('php ' . escapeshellarg($localDomainScript)));
} else {
    $_SERVER['HTTP_HOST'] = 'l.erhanozel';
    echo "UYARI: GetLocalDomain.php bulunamadı, varsayılan l.erhanozel kullanılıyor.\n";
}

try {
    // Config sınıfı örneği oluştur
    $config = new Config();

    // Veritabanı bağlantısı
    $dsn = "mysql:host={$config->dbServerName};dbname={$config->dbName};charset=utf8";
    $pdo = new PDO($dsn, $config->dbUsername, $config->dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Veritabanına bağlandı: {$config->dbName}\n";

    // phinxlog tablosunu kontrol et
    $stmt = $pdo->query("SHOW TABLES LIKE 'phinxlog'");
    $tableExists = ($stmt->rowCount() > 0);

    if (!$tableExists) {
        // Tablo yoksa oluştur
        echo "phinxlog tablosu bulunamadı, oluşturuluyor...\n";

        $sql = "CREATE TABLE `phinxlog` (
            `version` bigint(20) NOT NULL,
            `migration_name` varchar(100) DEFAULT NULL,
            `start_time` timestamp NULL DEFAULT NULL,
            `end_time` timestamp NULL DEFAULT NULL,
            `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`version`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $pdo->exec($sql);
        echo "phinxlog tablosu oluşturuldu.\n";
    }

    // İlk migration'ı tamamlandı olarak işaretle
    $initialMigrationVersion = '20250621131424'; // InitialSchema migration ID

    // Önce kaydın olup olmadığını kontrol et
    $stmt = $pdo->prepare("SELECT * FROM phinxlog WHERE version = :version");
    $stmt->execute(['version' => $initialMigrationVersion]);

    if ($stmt->rowCount() == 0) {
        // Kayıt yoksa ekle
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO phinxlog (version, migration_name, start_time, end_time, breakpoint) 
                VALUES (:version, :name, :start, :end, :break)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'version' => $initialMigrationVersion,
            'name' => 'InitialSchema',
            'start' => $now,
            'end' => $now,
            'break' => 0
        ]);

        echo "İlk migration (InitialSchema) tamamlandı olarak işaretlendi.\n";
    } else {
        echo "İlk migration zaten işaretlenmiş.\n";
    }

    echo "\nİşlem tamamlandı. Şimdi şu komutu çalıştırabilirsiniz:\n";
    echo "vendor\\bin\\phinx status -c phinx-config.php\n";

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
    echo "Dosya: " . $e->getFile() . " Satır: " . $e->getLine() . "\n";
}
