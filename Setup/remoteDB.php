<?php
header("Content-Type: text/html; charset=utf-8");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);

date_default_timezone_set('Europe/Istanbul');
setlocale(LC_TIME, "turkish");
setlocale(LC_ALL,'turkish');

function encrypt(string $data, string $key): string
{
    if (empty($data))return "";
    if (empty($key)) {
        throw new InvalidArgumentException('Data and key must not be null or empty.');
    }

    $method = 'AES-256-CBC';
    $key = hash('sha256', $key);
    $iv = substr(hash('sha256', $key), 0, 16);
    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    $encrypted = base64_encode($iv . $encrypted);

    return $encrypted;
}
function decrypt(string $data, string $key): string
{
    $dataOriginal = $data;
    if (empty($data))return "";
    if (empty($key)) {
        throw new InvalidArgumentException('Data and key must not be null or empty.');
    }

    $method = 'AES-256-CBC';
    $data = base64_decode($data);
    $ivSize = openssl_cipher_iv_length($method);
    $key = hash('sha256', $key);
    $iv = substr(hash('sha256', $key), 0, 16);
    $decrypt = openssl_decrypt(substr($data,16), $method, $key, OPENSSL_RAW_DATA, $iv);

    if(empty($decrypt)) $decrypt = $dataOriginal;
    return $decrypt;
}

function createPassword($value,$type){

    if($type==0) $chars = "0123456789";
    if($type==1) $chars = "ABCDEFGHJKMNPRSTUVYZQWX";
    if($type==2) $chars = "ABCDEFGHJKMNPRSTUVYZQWX23456789";
    if($type==3) $chars = "abcdefghjklmnoprstuvyzqxABCDEFGHJKLMNOPRSTUVYZQWX0123456789%=*";
    unset($Nasil);
    return substr(str_shuffle($chars),0,$value);
}

$root = $_SERVER['DOCUMENT_ROOT'];

include_once "$root/App/Core/Log.php";
include_once "$root/App/Config/Domain.php";
include_once "$root/App/Config/Key.php";
include_once "$root/App/Config/Sql.php";

// 3. Hata yakalayıcı
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $message = "Error [$errno] $errstr in $errfile on line $errline";
    Log::write($message, 'error', 'remoteDB');
});

// 4. İstisna yakalayıcı
set_exception_handler(function(Throwable $ex) {
    $message = "Uncaught Exception [{$ex->getCode()}] {$ex->getMessage()} in {$ex->getFile()} on line {$ex->getLine()}";
    Log::write($message, 'error', 'remoteDB');
});

// 5. Fatal hataları yakalamak için shutdown fonksiyonu
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = "Fatal Error [{$err['type']}] {$err['message']} in {$err['file']} on line {$err['line']}";
        Log::write($message, 'error', 'remoteDB');
    }
});

/**
 * @var string $dbServerName
 * @var string $dbName
 * @var string $dbUsername
 * @var string $dbPassword
 * @var string $key
 * @var string $domain
 * @var string $dbLocalServerName
 * @var string $dbLocalName
 * @var string $dbLocalUsername
 * @var string $dbLocalPassword
 */

$dbServerName = decrypt($dbServerName, $key);
$dbName = decrypt($dbName, $key);
$dbUsername = decrypt($dbUsername, $key);
$dbPassword = decrypt($dbPassword, $key);


$domain = $domain[0];
include "$root/App/Database/Database.php";
$db=new Database($dbServerName, $dbName, $dbUsername, $dbPassword);

// SQL dosyasını okuma ve sorguları çalıştırma
$sqlFilePath = $root . '/App/Database/database.sql';

//veri tabanı sql dosyası var mo kontrol edelim
$result = $db->runSqlFile($sqlFilePath);
if (!file_exists($sqlFilePath)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "SQL dosyası bulunamadı."
        ]
    );
    exit;
}

if ($result['status'] === 'error') {
    echo "Hata: " . $result['message'] . "\n";
    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $error) {
            echo "Sorgu: " . $error['query'] . "\n";
            echo "Hata: " . $error['error'] . "\n";
        }
    }
}
//ilk yöneticiyi girelim
$adminKey = createPassword(20,2);
$adminCreateDate = date("Y-m-d H:i:s");
$adminUpdateDate = date("Y-m-d H:i:s");
$adminAuth = 0;
$adminFullName = "Pozitif Eticaret";
$adminFullName = encrypt($adminFullName, $key);
$adminEmail = "info@pozitifeticaret.com";
$adminEmail = encrypt($adminEmail, $key);
$adminPhone = "5312631827";
$adminPhone = encrypt($adminPhone, $key);
$adminPassword = createPassword(5,3);
$adminPin = "1234";
$adminIsActive = 1;
$adminIsDeleted = 0;

$adminData = [
    "adminKey" => $adminKey,
    "adminCreateDate" => $adminCreateDate,
    "adminUpdateDate" => $adminUpdateDate,
    "adminAuth" => $adminAuth,
    "adminFullName" => $adminFullName,
    "adminEmail" => $adminEmail,
    "adminPhone" => $adminPhone,
    "adminPassword" => $adminPassword,
    "adminPin" => $adminPin,
    "adminIsActive" => $adminIsActive,
    "adminIsDeleted" => $adminIsDeleted
];

$sql = "
    INSERT INTO yoneticiler
    (yoneticianahtar, olusturmatarihi, guncellemetarihi, yoneticiyetki, yoneticiadsoyad, yoneticieposta, yoneticiceptelefon, yoneticisifre, yoneticipin, yoneticiaktif, yoneticisil)
    VALUES
    ( :adminKey, :adminCreateDate, :adminUpdateDate, :adminAuth, :adminFullName, :adminEmail, :adminPhone, :adminPassword, :adminPin, :adminIsActive, :adminIsDeleted)
";

$message = "";
if($db->insert($sql, $adminData)){
    $message .= "Sunucu Üzerinde yönetici başarıyla eklendi.<br>";
}
else{
    echo json_encode(
        [
            "status" => "error",
            "message" => "Veritabanı oluşturuldu ama yönetici eklenemedi."
        ]
    );
    exit();
}

$adminKey = createPassword(20,2);
$adminCreateDate = date("Y-m-d H:i:s");
$adminUpdateDate = date("Y-m-d H:i:s");
$adminAuth = 1;
$adminFullName = "Müjde Danyıldız";
$adminFullName = encrypt($adminFullName, $key);
$adminEmail = "mujdedanyildiz@gmail.com";
$adminEmail = encrypt($adminEmail, $key);
$adminPhone = "5307723631";
$adminPhone = encrypt($adminPhone, $key);
$adminPassword = createPassword(5,3);
$adminPin = "1234";
$adminIsActive = 1;
$adminIsDeleted = 0;

$adminData = [
    "adminKey" => $adminKey,
    "adminCreateDate" => $adminCreateDate,
    "adminUpdateDate" => $adminUpdateDate,
    "adminAuth" => $adminAuth,
    "adminFullName" => $adminFullName,
    "adminEmail" => $adminEmail,
    "adminPhone" => $adminPhone,
    "adminPassword" => $adminPassword,
    "adminPin" => $adminPin,
    "adminIsActive" => $adminIsActive,
    "adminIsDeleted" => $adminIsDeleted
];

$sql = "
    INSERT INTO yoneticiler
    (yoneticianahtar, olusturmatarihi, guncellemetarihi, yoneticiyetki, yoneticiadsoyad, yoneticieposta, yoneticiceptelefon, yoneticisifre, yoneticipin, yoneticiaktif, yoneticisil)
    VALUES
    ( :adminKey, :adminCreateDate, :adminUpdateDate, :adminAuth, :adminFullName, :adminEmail, :adminPhone, :adminPassword, :adminPin, :adminIsActive, :adminIsDeleted)
";

if($db->insert($sql, $adminData)){
    $message .= "Sunucu üzerinde yönetici başarıyla eklendi.<br>";
}
else{
    echo json_encode(
        [
            "status" => "error",
            "message" => "Veritabanı oluşturuldu ama yönetici eklenemedi."
        ]
    );
    exit();
}


$generalSettingsData = [
    "domain" => $domain,
    "ssldurum" => 1,
    "sitetip" => 1,
    "cokludil" => 1,
    "uyelik" => 1,
    "dilid" => 1
];

$sql = "
    INSERT INTO ayargenel
    (domain, ssldurum, sitetip, cokludil, uyelik, dilid)
    VALUES
    (:domain, :ssldurum, :sitetip, :cokludil, :uyelik, :dilid)
";

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;

        if (is_dir($filePath)) {
            // Alt klasörü recursive olarak sil
            deleteDirectory($filePath);
        } else {
            // Dosyayı sil
            unlink($filePath);
        }
    }

    // Boş klasörü sil
    return rmdir($dir);
}

if($db->insert($sql, $generalSettingsData)){

    ////////////////////////////////

    // Geçici olarak oluşturulan dizinler, işlem sonunda silinecek
    $setupDir = $root . '/Setup/';
    $testDir = $root . '/Tests/';

    // Dizinleri sil
    if (is_dir($setupDir)) {
        array_map('unlink', glob("$setupDir/*.*"));
        rmdir($setupDir);
    }
    if (is_dir($testDir)) {
        deleteDirectory($testDir);
    }

    ///////////////////////////////

    $message .= "Sunucu üzerinde genel ayarlar başarıyla eklendi.<br>";
    echo json_encode(
        [
            "status" => "success",
            "message" => $message
        ]
    );exit;
}
else{
    echo json_encode(
        [
            "status" => "error",
            "message" => "Veritabanı oluşturuldu ama genel ayarlar eklenemedi."
        ]
    );
    exit();
}