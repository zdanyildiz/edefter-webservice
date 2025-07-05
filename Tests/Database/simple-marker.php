<?php
/**
 * Basit Phinx Migration İşaretleyici
 *
 * Bu betik, mevcut veritabanınızı bozmadan Phinx migration sistemine geçmenizi sağlar.
 * Veritabanına doğrudan bağlanır, phinxlog tablosunu oluşturur ve InitialSchema migration'ını
 * "zaten uygulanmış" olarak işaretler.
 *
 * Kullanım:
 * 1) Tüm parametreleri girerek:
 *    php Tests/Database/simple-marker.php HOSTNAME USERNAME PASSWORD DBNAME
 *
 * 2) Yerel ortamdaki Config.php yapılandırmasını kullanarak:
 *    php Tests/Database/simple-marker.php --local
 *
 * Not: --local parametresi kullanıldığında, sistem Tests/System/GetLocalDomain.php
 * betiği aracılığıyla yerel domain adını otomatik olarak tespit eder ve
 * Config.php'yi kullanarak yerel veritabanı bağlantı bilgilerini alır.
 */

// Varsayılan parametreler
$useLocalConfig = false;
$host = null;
$user = null;
$pass = null;
$dbname = null;

// Komut satırı parametrelerini kontrol et
if ($argc < 2) {
    echo "Kullanım: php Tests/Database/simple-marker.php HOSTNAME USERNAME PASSWORD DBNAME\n";
    echo "VEYA yerel yapılandırmayı kullanmak için: php Tests/Database/simple-marker.php --local\n";
    exit(1);
}

if ($argv[1] === "--local") {
    $useLocalConfig = true;
    echo "Yerel yapılandırma kullanılacak.\n";

    // Yerel domain adını tespit et
    $localDomainScript = dirname(__DIR__) . '/System/GetLocalDomain.php';
    if (!file_exists($localDomainScript)) {
        die("HATA: GetLocalDomain.php bulunamadı: $localDomainScript\n");
    }

    // GetLocalDomain.php betiğini çalıştır
    $command = escapeshellcmd("php " . $localDomainScript);
    $localDomain = trim(shell_exec($command));

    if (empty($localDomain)) {
        die("HATA: Yerel domain adı tespit edilemedi. $localDomainScript betiğini kontrol edin.\n");
    }

    // Domain adını ekrana yazdır
    echo "Tespit edilen yerel domain: $localDomain\n";

    // Config.php ve diğer dosyaları yükle
    $projectRoot = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;

    $_SERVER['HTTP_HOST'] = $localDomain; // Config sınıfının doğru ayarları yüklemesi için

    require_once $projectRoot . 'App/Helpers/Helper.php';
    require_once $projectRoot . 'App/Core/Config.php';

    // Config sınıfı örneğini oluştur
    $config = new Config();

    // Config'den veritabanı bağlantı bilgilerini al
    $host = $config->dbServerName;
    $user = $config->dbUsername;
    $pass = $config->dbPassword;
    $dbname = $config->dbName;

    echo "Yerel veritabanı bağlantı bilgileri başarıyla alındı.\n";
} else {
    // Manuel olarak verilen parametreleri kullan
    $host = $argv[1];
    $user = $argv[2];
    $pass = $argv[3];
    $dbname = $argv[4];
}

echo "Veritabanı: $dbname @ $host (Kullanıcı: $user)\n";

try {
    // Veritabanına bağlan
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Veritabanı bağlantısı başarılı!\n";

    // phinxlog tablosunu oluştur (eğer yoksa)
    $sql = "CREATE TABLE IF NOT EXISTS `phinxlog` (
        `version` BIGINT(20) NOT NULL,
        `migration_name` VARCHAR(100) NULL DEFAULT NULL,
        `start_time` TIMESTAMP NULL DEFAULT NULL,
        `end_time` TIMESTAMP NULL DEFAULT NULL,
        `breakpoint` TINYINT(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`version`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $pdo->exec($sql);
    echo "phinxlog tablosu oluşturuldu veya mevcut.\n";

    // Migration ID'si (db/migrations/ klasöründeki dosya adından)
    $version = "20250621131424";
    $name = "InitialSchema";

    // Bu migration zaten işaretli mi kontrol et
    $stmt = $pdo->prepare("SELECT * FROM `phinxlog` WHERE `version` = ?");
    $stmt->execute([$version]);

    if ($stmt->rowCount() > 0) {
        echo "Migration '$name' ($version) zaten işaretli.\n";
    } else {
        // Migration'ı işaretle
        $stmt = $pdo->prepare(
            "INSERT INTO `phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) 
            VALUES (?, ?, NOW(), NOW(), 0)"
        );
        $stmt->execute([$version, $name]);

        echo "Migration '$name' ($version) başarıyla işaretlendi!\n";
    }

    echo "\nBu işlem başarıyla tamamlandı. Artık aşağıdaki komutu çalıştırarak kontrol edebilirsiniz:\n";
    echo "vendor\\bin\\phinx status\n\n";

    echo "Bundan sonra yeni veritabanı değişikliklerinizi şöyle yapabilirsiniz:\n";
    echo "1. vendor\\bin\\phinx create YeniMigrationAdi\n";
    echo "2. Oluşturulan dosyayı düzenleyin\n";
    echo "3. vendor\\bin\\phinx migrate\n";

} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage() . "\n");
}
