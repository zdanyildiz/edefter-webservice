<?php
/**
 * Bu betik, InitialSchema migration dosyasını hiç bir SQL komutunu çalıştırmadan
 * "zaten uygulanmış" olarak işaretlemek için kullanılır.
 *
 * Bu, mevcut veritabanınıza dokunmadan Phinx'i kullanmaya başlamanızı sağlar.
 *
 * Kullanım: php Tests/Database/migrate-marker.php
 */

// Proje kök dizini tanımı
define('ROOT', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);

// Domain.php dosyasından yerel domain adını okuma
$domainPhpPath = ROOT . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Domain.php';
if (!file_exists($domainPhpPath)) {
    die("Domain.php dosyası bulunamadı: $domainPhpPath");
}

// Domain.php dosyasının içeriğini okuma
$fileContent = file_get_contents($domainPhpPath);

// 'l.' ile başlayan yerel domain adını bulma
if (!preg_match('/[\'"]l\.[a-zA-Z0-9._-]+[\'"]/', $fileContent, $matches)) {
    die("'l.' ile başlayan yerel alan adı bulunamadı.");
}
$localDomain = trim($matches[0], '\'"');

// Yerel domain adını HTTP_HOST olarak ayarlama
$_SERVER['HTTP_HOST'] = $localDomain;
echo "Yerel domain: {$localDomain}\n";

// Config ve diğer gerekli sınıfları yükleme
require_once ROOT . 'App' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'Helper.php';
require_once ROOT . 'App' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Config.php';
$config = new Config();

echo "Veritabanı bağlantı bilgileri kullanılıyor:\n";
echo "Host: {$config->dbServerName}\n";
echo "Database: {$config->dbName}\n";
echo "User: {$config->dbUsername}\n";

// PDO bağlantısı oluşturma
try {
    $pdo = new PDO(
        "mysql:host={$config->dbServerName};dbname={$config->dbName};charset=utf8",
        $config->dbUsername,
        $config->dbPassword
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Veritabanı bağlantısı başarılı!\n";
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Phinx phinxlog tablosunu oluşturma (eğer yoksa)
$pdo->exec("CREATE TABLE IF NOT EXISTS `phinxlog` (
    `version` BIGINT(20) NOT NULL,
    `migration_name` VARCHAR(100) NULL DEFAULT NULL,
    `start_time` TIMESTAMP NULL DEFAULT NULL,
    `end_time` TIMESTAMP NULL DEFAULT NULL,
    `breakpoint` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

echo "phinxlog tablosu kontrol edildi.\n";

// InitialSchema migration'ının kontrol edilmesi
$version = "20250621131424"; // Migration ID'si
$stmt = $pdo->prepare("SELECT * FROM phinxlog WHERE version = ?");
$stmt->execute([$version]);

if ($stmt->rowCount() === 0) {
    // Migration henüz uygulanmış olarak işaretlenmemiş, işaretliyoruz
    $stmt = $pdo->prepare("INSERT INTO phinxlog (version, migration_name, start_time, end_time, breakpoint) VALUES (?, ?, NOW(), NOW(), 0)");
    $stmt->execute([$version, 'InitialSchema']);
    echo "InitialSchema migration'ı başarıyla 'zaten uygulandı' olarak işaretlendi.\n";
} else {
    echo "InitialSchema migration'ı zaten işaretlenmiş durumda.\n";
}

echo "İşlem tamamlandı.\n";
echo "Artık 'vendor\\bin\\phinx status' komutunu çalıştırarak durumu kontrol edebilirsiniz.\n";
