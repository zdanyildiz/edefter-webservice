<?php
use phpseclib3\Net\SSH2;

header("Content-Type: text/html; charset=utf-8");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

function parseDomain($domain)
{
    // 'http://', 'https://', 'www.' ön eklerini temizle
    $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $domain);

    // Eğer ".tr" uzantısı varsa geçici olarak sakla ve domain adından kaldır
    $trSuffix = "";
    if (substr($domain, -3) === ".tr") {
        $trSuffix = ".tr";
        $domain = substr($domain, 0, -3); // ".tr" kısmını çıkar
    }

    // Alan adı temizlenmiş haliyle en az iki nokta olmalı
    $domainParts = explode('.', $domain);

    if (count($domainParts) < 2) {
        return ['error' => 'Geçersiz domain formatı'];
    }

    // Subdomain'i bulmak için ilk noktadan öncesini alalım
    $subDomain = count($domainParts) > 2 ? implode('.', array_slice($domainParts, 0, -2)) : '';

    // Ana domain, son iki parçayı alarak oluşturulur
    $mainDomain = implode('.', array_slice($domainParts, -2)) . $trSuffix;

    return [
        'subdomain' => $subDomain,
        'mainDomain' => $mainDomain
    ];
}

function executeGitClone($serverIp, $sshUser, $sshPassword, $repositoryUrl, $targetDirectory)
{
    try {
        // SSH bağlantısı oluştur
        $ssh = new SSH2($serverIp);

        // Sunucuya giriş yap
        if (!$ssh->login($sshUser, $sshPassword)) {
            throw new Exception('SSH oturumu açılamadı. Lütfen kullanıcı adı ve şifrenizi kontrol edin.');
        }

        // Git klonlama komutunu çalıştır
        $gitCloneCommand = "git clone $repositoryUrl $targetDirectory";
        $output = $ssh->exec($gitCloneCommand);

        // Çıktıları kontrol et
        if (strpos($output, 'fatal') !== false) {
            throw new Exception("Git klonlama sırasında hata oluştu: $output");
        }

        // App/Config klasörüne yazma izni verme
        $configDirectory = "$targetDirectory/App/Config";
        $permissionCommand = "chmod -R 775 $configDirectory && chown -R www-data:www-data $configDirectory";
        $permissionOutput = $ssh->exec($permissionCommand);

        // Admin klasörüne yazma izni verme
        $configDirectory = "$targetDirectory/Setup";
        $permissionCommand = "chmod -R 775 $configDirectory && chown -R www-data:www-data $configDirectory";
        $permissionOutput = $ssh->exec($permissionCommand);

        // Hata kontrolü
        if (strpos($permissionOutput, 'Permission denied') !== false) {
            throw new Exception("İzin değiştirme sırasında hata oluştu: $permissionOutput");
        }
        return "Git klonlama işlemi başarıyla tamamlandı:\n";

    } catch (Exception $e) {
        echo "Bir hata oluştu: " . $e->getMessage() . "\n";
    }
}

function sendFilesToRemoteServer($localFiles, $remoteUrl)
{
    $postData = [];

    // Tüm dosyaların içeriklerini oku
    foreach ($localFiles as $fileName => $filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("Dosya bulunamadı: $filePath");
        }
        $postData[$fileName] = file_get_contents($filePath);
    }

    // cURL ile POST isteği gönder
    $ch = curl_init($remoteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl Hatası: ' . curl_error($ch));
    }
    curl_close($ch);

    $response = json_decode($response, true);

    if ($response['status'] !== 'success') {
        throw new Exception("Uzak sunucuda hata: " . $response['message']);
    }

    return $response['message'];
}

function addGitRepoToDomain(
    $serverIp,
    $sshUser,
    $sshPassword,
    $domain,
    $repoName,
    $remoteUrl = null,
    $deploymentPath = '/httpdocs',
    $deploymentMode = 'auto',
    $skipSslVerification = false
) {
    // SSH ile sunucuya bağlan
    $ssh = new SSH2($serverIp);
    if (!$ssh->login($sshUser, $sshPassword)) {
        throw new Exception('SSH bağlantısı yapılamadı. Kullanıcı adı/şifreyi kontrol edin.');
    }

    // Temel komutu oluştur
    // Örnek: plesk ext git --create -domain example.com -name example-repo
    $command = "plesk ext git --create -domain " . escapeshellarg($domain) . " -name " . escapeshellarg($repoName);

    // Remote repo eklemek istiyorsanız
    if ($remoteUrl) {
        $command .= " -remote-url " . escapeshellarg($remoteUrl);
    }

    // Deployment path ayarlama (varsayılan: /httpdocs)
    if ($deploymentPath) {
        $command .= " -deployment-path " . escapeshellarg($deploymentPath);
    }

    // Deployment mode ayarlama (auto|manual|none)
    if ($deploymentMode) {
        $command .= " -deployment-mode " . escapeshellarg($deploymentMode);
    }

    // SSL doğrulamasını atlamak istiyorsanız
    if ($skipSslVerification) {
        $command .= " -skip-ssl-verification true";
    }

    // Komutu çalıştır
    $output = $ssh->exec($command);

    // Çıktıda hata kontrolü yap
    if (stripos($output, 'error') !== false || stripos($output, 'failed') !== false) {
        Log::write("Git deposu oluşturulurken hata oluştu: $output", "error");
        return false;
    }

    return "Git deposu '$repoName' başarıyla '$domain' alan adına eklendi. Plesk panelinde kontrol edebilirsiniz.";
}

function runComposerCommand($serverIp, $sshUser, $sshPassword, $domain, $command = 'install') {
    $targetDirectory = "/var/www/vhosts/$domain/httpdocs";

    // Plesk'te kullandığınız PHP sürüm yolunu doğrulayın
    $phpPath = "/opt/plesk/php/8.3/bin/php";
    // Plesk composer phar dosyasının tam yolu
    $composerPharPath = "/usr/local/psa/var/modules/composer/composer.phar";

    $ssh = new SSH2($serverIp);
    if (!$ssh->login($sshUser, $sshPassword)) {
        throw new Exception('SSH bağlantısı başarısız. Kullanıcı adı/şifreyi kontrol edin.');
    }

    // Tam yollarla komutu oluşturuyoruz
    //cd /var/www/vhosts/meteplastic.pozitifeticaret.com/httpdocs
    //cd /var/www/vhosts/meteplastic.pozitifeticaret.com/httpdocs COMPOSER_ALLOW_SUPERUSER=1 /opt/plesk/php/8.3/bin/php /usr/local/psa/var/modules/composer/composer.phar install
    $composerCommand = "cd $targetDirectory && COMPOSER_ALLOW_SUPERUSER=1 $phpPath $composerPharPath $command";

    $output = $ssh->exec($composerCommand);

    if (stripos($output, 'error') !== false || stripos($output, 'Could not') !== false) {
        Log::write("Composer komutunu çalıştırırken hata oluştu: $output", "error");
        return false;
    }

    return "Composer '$command' komutu başarıyla çalıştı:\n" . $output;
}


require '../vendor/autoload.php';

$serverIP = "213.238.172.149";
$pleskUser = "admin";
$pleskPassword = "1q2w3e4r1232.!*";

$sshUser = 'root';
$sshPassword = '1q2w3e4r1232';

$action = $_POST['action'];

$domain = $_POST['domain'];
$domains = $_POST['domains'];
$domains = "$domain,$domains";
$domains = explode(",", $domains);

$domainPageContent="[";
foreach ($domains as $key => $value) {
    $domainPageContent .= "'$value',";
}
$domainPageContent = rtrim($domainPageContent, ",");
$domains = "<?php\n\$domain=$domainPageContent];";

$key = $_POST['keyCode'];
$keyPageContent = "<?php \$key=\"$key\";?>";

$serverUrl = $_POST['serverUrl'];
$databaseName = $_POST['databaseName'];
$username = $_POST['username'];
$password = $_POST['password'];

$localServerUrl = $_POST['localServerUrl'];
$localDatabaseName = $_POST['localDatabaseName'];
$localUsername = $_POST['localUsername'];
$localPassword = $_POST['localPassword'];

$root = $_SERVER['DOCUMENT_ROOT'];

include_once "$root/App/Core/Log.php";
include_once "$root/Setup/Plesk.php";
include_once "$root/Setup/FtpClient.php";

if($action == "createDomain"){
    //"$root/App/Config/" klasör yok ise bu dosyayı oluşturalım
    if(!file_exists("$root/App/Config/")){
        mkdir("$root/App/Config/", 0777, true);
    }
    if(file_put_contents("$root/App/Config/Domain.php", $domains)){
        echo json_encode(
            [
                "status" => "success",
                "message" => "<br>Domainler kaydedildi."
            ]
        );
    }
    else{
        echo json_encode(
            [
                "status" => "error",
                "message" => "Domainlar kaydedilemedi."
            ]
        );
    }

    $robots = "User-agent: *\n";
    $robots .= "allow:/\n";
    $robots .= "allow:/Public/Image\n";
    $robots .= "disallow:/App/\n";

    file_put_contents("$root/robots.txt", $robots);
}
elseif($action == "createKey"){
    if(file_put_contents("$root/App/Config/Key.php", $keyPageContent)){
        echo json_encode(
            [
                "status" => "success",
                "message" => "Key kaydedildi."
            ]
        );
    }
    else{
        echo json_encode(
            [
                "status" => "error",
                "message" => "Key kaydedilemedi."
            ]
        );
    }
}
elseif($action == "createSql"){
    //local bilgilerle veri tabanını oluşturalım
    // MySQL bağlantısı oluştur
    $conn = new mysqli($localServerUrl, $localUsername, $localPassword);

    // Bağlantıyı kontrol et
    if ($conn->connect_error) {
        echo json_encode(
            [
                "status" => "error",
                "message" => "Bağlantı hatası: " . $conn->connect_error
            ]
        );
        exit;
    }

    $db_check_query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$localDatabaseName'";
    $db_check_result = $conn->query($db_check_query);

    $message = "";

    if ($db_check_result->num_rows > 0) {
        $message = "Veritabanı mevcut.<br>";
    }
    else
    {
        $message = "Veritabanı mevcut değil.<br>";
        $sql = "CREATE DATABASE `$localDatabaseName`";

        // Veritabanını oluştur
        if ($conn->query($sql) === TRUE) {
            $message .= "Veritabanı oluşturuldu.<br>";
        }
        else
        {
            echo json_encode(
                [
                    "status" => "error",
                    "message" => "Veritabanı oluşturulamadı: " . $conn->error
                ]
            );
            exit;
        }


        if($conn->select_db($localDatabaseName)){
            $message .= "Veritabanı seçildi.<br>";
        }
        else{
            echo json_encode(
                [
                    "status" => "error",
                    "message" => "Veritabanı seçilemedi: " . $conn->error
                ]
            );
            exit;
        }


        // SQL dosyasını okuma ve sorguları çalıştırma
        $sqlFilePath = $root . '/App/Database/database.sql';
        //veri tabanı sql dosyası var mo kontrol edelim
        if (!file_exists($sqlFilePath)) {
            echo json_encode(
                [
                    "status" => "error",
                    "message" => "SQL dosyası bulunamadı."
                ]
            );
            exit;
        }
        $sqlFile = fopen($sqlFilePath, 'r');

        if (!$sqlFile) {
            echo json_encode(
                [
                    "status" => "error",
                    "message" => "SQL dosyası açılamadı."
                ]
            );
        }

        $sql = '';
        $sqlErrors = []; // Hataları toplamak için bir dizi

        while (($line = fgets($sqlFile)) !== false) {
            $line = trim($line);

            // Yorumları ve boş satırları geç
            if (substr($line, 0, 2) === '--' || $line === '') {
                continue;
            }

            // Sorguyu birleştir
            $sql .= $line . " ";

            // Sorgu tamamlandığında çalıştır
            if (substr($line, -1) === ';') {
                $trimmedSql = trim($sql);
                if (!empty($trimmedSql)) {
                    //echo "Çalıştırılan Sorgu: " . $trimmedSql . "\n"; // Debug amaçlı
                    if ($conn->query($trimmedSql) === false) {
                        // Hata varsa, hatayı ve sorguyu topluyoruz
                        $sqlErrors[] = [
                            "query" => $trimmedSql,
                            "error" => $conn->error
                        ];
                    }
                }
                $sql = ''; // Bir sonraki sorgu için temizleme
            }
        }

        // Döngü tamamlandıktan sonra kalan sorguyu çalıştır
        $trimmedSql = trim($sql);
        if (!empty($trimmedSql)) {
            //echo "Çalıştırılan Son Sorgu: " . $trimmedSql . "\n"; // Debug amaçlı
            if ($conn->query($trimmedSql) === false) {
                $sqlErrors[] = [
                    "query" => $trimmedSql,
                    "error" => $conn->error
                ];
            }
        }

        // Hataları kontrol et
        if (!empty($sqlErrors)) {
            foreach ($sqlErrors as $error) {
                echo "Hata: " . $error['error'] . "\n";
                echo "Sorgu: " . $error['query'] . "\n";
            }
            exit;
        }
        // Bağlantıyı kapat
        $conn->close();

        include "$root/App/Database/Database.php";

        $db=new Database($localServerUrl, $localDatabaseName, $localUsername, $localPassword);

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

        if($db->insert($sql, $adminData)){
            $message .= "Yönetici başarıyla eklendi.<br>";
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
            $message .= "Yönetici başarıyla eklendi.<br>";
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

        if($db->insert($sql, $generalSettingsData)){
            $message .= "Genel ayarlar başarıyla eklendi.<br>";
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
    }

    $serverUrl = encrypt($serverUrl, $key);

    $databaseName = encrypt($databaseName, $key);
    $username = encrypt($username, $key);
    $password = encrypt($password, $key);

    $localServerUrl = encrypt($localServerUrl, $key);
    $localDatabaseName = encrypt($localDatabaseName, $key);
    $localUsername = encrypt($localUsername, $key);
    $localPassword = encrypt($localPassword, $key);

    $sql = "<?php\n";
    $sql .= "\$dbServerName=\"$serverUrl\";\n";
    $sql .= "\$dbName=\"$databaseName\";\n";
    $sql .= "\$dbUsername=\"$username\";\n";
    $sql .= "\$dbPassword=\"$password\";\n";
    $sql .= "\$dbLocalServerName=\"$localServerUrl\";\n";
    $sql .= "\$dbLocalName=\"$localDatabaseName\";\n";
    $sql .= "\$dbLocalUsername=\"$localUsername\";\n";
    $sql .= "\$dbLocalPassword=\"$localPassword\";\n";

    if(file_put_contents("$root/App/Config/Sql.php", $sql)){
        $message .= "Sql sayfası oluşturuldu<br>";
        echo json_encode(
            [
                "status" => "success",
                "message" => $message
            ]
        );
        exit;
    }
    else{
        echo json_encode(
            [
                "status" => "error",
                "message" => "Sql dosyası kaydedilemedi."
            ]
        );
    }
}
elseif($action == "createCloudflare"){
    //echo json_encode(["status"=>"success","message"=>"Cloud Flare işlemi atlandı"]);exit;
    include_once "$root/Setup/CloudflareAPI.php"; // Bu dosyanın CloudflareAPI sınıfını içerdiğini varsayıyoruz

    $apiKey = "cb2491e24286f3666868cfe1bb7ec6cfe0c75"; // Token
    $email = "zdanyildiz@gmail.com"; // Cloudflare hesabınızın emaili

    $cloudflare = new CloudflareAPI($apiKey, $email);

    $domain = $_POST['domain'];

    $domainParts = parseDomain($domain);
    if (isset($domainParts['error'])) {
        echo json_encode(
            [
                "status" => "error",
                "message" => $domainParts['error']
            ]
        );
        exit;
    }

    $mainDomain = $domainParts['mainDomain'];
    $subDomain = $domainParts['subdomain'];

    Log::write("Cloudflare domain: $mainDomain", "info");
    Log::write("Cloudflare subdomain: $subDomain", "info");

    $zoneId = $cloudflare->getZoneIdByDomain($mainDomain);
    Log::write("Cloudflare zoneId: $zoneId", "info");

    $finalMessage = ""; // Başarı mesajlarını toplamak için

    if (!empty($zoneId) && !empty($subDomain)) {
        $response = $cloudflare->addSubdomain($zoneId, $subDomain, $mainDomain, $serverIP, 1200, true);
        // CloudflareAPI->addSubdomain metodunun yanıtını kontrol edin, başarılıysa mesaj ekleyin
        if (isset($response['success']) && $response['success'] === true) {
            $finalMessage .= "Cloud Flare Subdomain başarıyla eklendi.<br>";
        } else {
            $finalMessage .= "Cloud Flare Subdomain eklenirken hata oluştu: " . ($response['errors'][0]['message'] ?? 'Bilinmeyen hata') . "<br>";
        }
    }
    elseif (!empty($zoneId) && empty($subDomain)) {
        // Ana domain zaten Cloudflare'de, DNS kaydı eklemeye gerek yok (eğer A kaydı zaten doğruysa)
        // $finalMessage .= "Cloud Flare Site zaten mevcut.<br>"; // Bu mesajı isterseniz ekleyebilirsiniz.
    }
    elseif (empty($zoneId)) {
        $response = $cloudflare->addSite($mainDomain);
        Log::write("Cloudflare sonuç: ".json_encode($response), "info");
        if(!isset($response['result']['id'])){
            echo json_encode(
                [
                    "status" => "error",
                    "message" => "Cloudflare'e site eklenemedi: " . ($response['errors'][0]['message'] ?? 'Bilinmeyen hata')
                ]
            );
            exit;
        }
        $zoneId =  $response['result']['id'];
        $finalMessage .= "Cloud Flare Site başarıyla eklendi.<br>";

        // Yeni site eklendikten sonra, A kaydını (ana domain için @ veya mainDomain) serverIP'ye yönlendirin
        $dnsRecordName = $mainDomain; // Ana domain için
        $dnsAddResponse = $cloudflare->addDNSRecord($zoneId, "A", $dnsRecordName, $serverIP, 1, true); // TTL 1 (Auto), Proxied true
        if(isset($dnsAddResponse['success']) && $dnsAddResponse['success'] === true) {
            $finalMessage .= "$dnsRecordName için A kaydı başarıyla eklendi/güncellendi.<br>";
        } else {
            $finalMessage .= "$dnsRecordName için A kaydı eklenirken/güncellenirken hata oluştu: " . ($dnsAddResponse['errors'][0]['message'] ?? 'Bilinmeyen hata') . "<br>";
        }

        if(!empty($subDomain)){
            $subdomainAddResponse = $cloudflare->addSubdomain($zoneId, $subDomain, $mainDomain, $serverIP, 1, true); // TTL 1 (Auto), Proxied true
            if (isset($subdomainAddResponse['success']) && $subdomainAddResponse['success'] === true) {
                $finalMessage .= "$subDomain.$mainDomain için Cloud Flare Subdomain başarıyla eklendi.<br>";
            } else {
                $finalMessage .= "$subDomain.$mainDomain için Cloud Flare Subdomain eklenirken hata oluştu: " . ($subdomainAddResponse['errors'][0]['message'] ?? 'Bilinmeyen hata') . "<br>";
            }
        }
    }
    else{ // Bu durum normalde oluşmamalı, önceki if/elseif'ler kapsamalı
        echo json_encode(
            [
                "status" => "error",
                "message" => "Cloudflare Site veya subdomain için beklenmedik durum."
            ]
        );
        exit;
    }

    // Her durumda (yeni site veya mevcut site), Turnstile anahtarlarını kontrol et/oluştur
    // $mainDomain'in doğru olduğundan emin olun. parseDomain'den gelen $mainDomain kullanılmalı.
    $cfAccountID = "d5dce006c272113d679537e54c20eb03"; // Account ID'niz
    $turnstileInfo = $cloudflare->getOrCreateTurnstileWidget($cfAccountID, $mainDomain, "$mainDomain Turnstile");

    Log::write("Turnstile Info: ".json_encode($turnstileInfo), "info");

    if (isset($turnstileInfo['success']) && $turnstileInfo['success']) {
        $finalMessage .= "Turnstile widget işlemi başarılı (" . $turnstileInfo['action'] . ").<br>";

        $cloudFlareJsonPath = $root . "/App/Config/CloudFlare.json"; //
        $cloudFlareConfig = [];

        if (file_exists($cloudFlareJsonPath)) {
            $jsonContent = file_get_contents($cloudFlareJsonPath);
            $cloudFlareConfig = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::write("CloudFlare.json okuma/decode hatası: " . json_last_error_msg(), "error");
                // Hata durumunda mevcut config'i boşaltabilir veya varsayılan bir yapı kullanabilirsiniz.
                // Şimdilik, decode hatası olursa config'i boş bir array olarak bırakıyoruz.
                $cloudFlareConfig = ['default' => new stdClass(), 'sites' => new stdClass()]; // stdClass daha uygun olabilir eğer JSON'da boş obje {} ise
            }
        }
        else {
            // Dosya yoksa, temel yapıyı oluştur
            $cloudFlareConfig = [
                "default" => new stdClass(), // veya gerekli default anahtarlar
                "sites" => new stdClass()   // siteler için boş bir obje
            ];
            $finalMessage .= "CloudFlare.json dosyası bulunamadı, yenisi oluşturulacak.<br>";
        }

        // sites anahtarının bir obje (veya array) olduğundan emin olalım
        if (!isset($cloudFlareConfig['sites']) || !is_array($cloudFlareConfig['sites'])) {
            // Eğer JSON'da "sites": {} şeklinde ise, PHP'de array'e dönüşür.
            // Eğer "sites" hiç yoksa veya null ise, burası çalışır.
            $cloudFlareConfig['sites'] = []; // array olarak başlatmak daha güvenli
        }

        $siteKeyToStore = $turnstileInfo['sitekey'];
        $secretKeyToStore = null;

        if ($turnstileInfo['action'] === 'created' && isset($turnstileInfo['secret'])) {
            $secretKeyToStore = $turnstileInfo['secret'];
        } elseif ($turnstileInfo['action'] === 'found') {
            // Widget bulundu, mevcut secret'ı JSON'dan korumaya çalış
            if (isset($cloudFlareConfig['sites'][$mainDomain]['secret_key'])) {
                $secretKeyToStore = $cloudFlareConfig['sites'][$mainDomain]['secret_key'];
            } else {
                // JSON'da da secret yoksa, null kalacak veya bir not eklenebilir.
                // $finalMessage .= "$mainDomain için mevcut secret JSON'da bulunamadı.<br>";
            }
        }

        // Anahtarları CloudFlare.json'a ekle/güncelle
        // Anahtar isimlerinin JSON dosyanızdaki gibi olduğundan emin olun ("site_key", "secret_key")
        $cloudFlareConfig['sites'][$mainDomain] = [
            "site_key" => $siteKeyToStore,
            "secret_key" => $secretKeyToStore
        ];

        // default anahtarları da güncelleyebiliriz (opsiyonel, isteğe bağlı)
        // Örneğin, son oluşturulanı default yapabilirsiniz:
        // $cloudFlareConfig['default']['site_key'] = $siteKeyToStore;
        // $cloudFlareConfig['default']['secret_key'] = $secretKeyToStore;

        if (file_put_contents($cloudFlareJsonPath, json_encode($cloudFlareConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
            $finalMessage .= "CloudFlare.json dosyası $mainDomain için güncellendi.<br>";
            Log::write("CloudFlare.json dosyası $mainDomain için güncellendi.", "info");
        } else {
            $finalMessage .= "CloudFlare.json dosyası güncellenirken hata oluştu.<br>";
            Log::write("CloudFlare.json dosyası güncellenirken hata oluştu.", "error");
        }

    }
    else {
        $finalMessage .= "Turnstile widget işlemi başarısız oldu: " . ($turnstileInfo['message'] ?? 'Bilinmeyen hata') . "<br>";
        if(isset($turnstileInfo['details'])) {
            Log::write("Turnstile Hata Detayları: ".json_encode($turnstileInfo['details']), "error");
        }
    }

    echo json_encode(
        [
            "status" => "success", // Genel işlem başarılı kabul edilebilir, detaylar mesajda
            "message" => $finalMessage
        ]
    );
    // exit; // Zaten sonda exit var, buradaki gereksiz olabilir.
}
elseif($action == "createSite"){
    try {

        $plesk = new PleskAPI("https://plesk.globalpozitif.com.tr", $pleskUser, $pleskPassword);

        // Önce DNS kaydını kontrol et
        /*Log::write("DNS kaydını kontrol ediyoruz");
        $dnsCheck = $plesk->checkDnsRecord($domain);

        // Eğer DNS kaydı varsa, önce onu sil
        if ($dnsCheck['exists']) {
            Log::write("Plesk üzerinde dns kaydı var");
            $plesk->deleteDnsRecord($domain);
        }
        else{
            Log::write("Plesk üzerinde dns kaydı yok");
        }*/


        $domainResult = $plesk->createDomain($domain, $username, $password, $serverIP, "Pozitif Plan", 8);

        if (!isset($domainResult['id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Domain oluşturulamadı: " . json_encode($domainResult)
            ]);
            exit;
        }

        $message = "Plesk Domain oluşturuldu.<br>";

        $domainId = $domainResult['id'];

        $dbResult = $plesk->createDatabase($domainId, $databaseName, 1);

        if (!isset($dbResult['id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Veritabanı oluşturulamadı: " . json_encode($dbResult)
            ]);
            exit;
        }

        $message .= "Plesk Veritabanı oluşturuldu.<br>";

        $dbId = $dbResult['id'];

        // Veritabanı kullanıcısı oluşturma
        $userResult = $plesk->createDatabaseUser($dbId, $username, $password, $domainId);

        if (!isset($userResult['id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Veritabanı kullanıcısı oluşturulamadı: " . json_encode($userResult)
            ]);
            exit;
        }

        $message .= "Plesk Veritabanı kullanıcısı oluşturuldu.<br>";

        echo json_encode([
            "status" => "success",
            "message" => $message
        ]);


    }
    catch (Exception $e) {
        echo json_encode(
            [
                "status" => "error",
                "message" => $e->getMessage()
            ]
        );
    }
}
elseif ($action == "setupSite1") {
    try {

        $plesk = new PleskAPI("https://plesk.globalpozitif.com.tr:8443", $pleskUser, $pleskPassword);
        $ftpUser = substr($username, 0, 32);
        $ftp = new FTPClient($serverIP, $ftpUser, $password);
        $ftp->deleteFile("/httpdocs/index.html");
        $ftp->close();

        echo json_encode([
            "status" => "success",
            "message" => "FTP index sayfası silindi."
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }

    exit;
}
elseif ($action == "setupSite2") {

    $repositoryUrl = "https://" . getenv('GITHUB_TOKEN') . "@github.com/zdanyildiz/eticaret.git";
    $targetDirectory = "/var/www/vhosts/$domain/httpdocs";

    $message = addGitRepoToDomain($serverIP, $sshUser, $sshPassword, $domain, "eticaret", $repositoryUrl);

    if (!$message) {
        json_encode([
            "status" => "error",
            "message" => "Git deposu eklenemedi."
        ]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "message" => $message
    ]);
    exit;
}
elseif ($action == "setupSite3") {

    $message = runComposerCommand($serverIP, $sshUser, $sshPassword, $domain);

    if(!$message) {
        json_encode([
            "status" => "error",
            "message" => "Composer komutu çalıştırılamadı."
        ]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "message" => $message
    ]);
}
elseif ($action == "setupSite4") {

    try {

        $pleskHelper = new PleskSSHHelper("https://plesk.globalpozitif.com.tr:8443", $pleskUser, $pleskPassword);
        $pleskHelper->changeOwner($domain, "/var/www/vhosts/$domain/httpdocs", $username, "psacln");
        $message = "Sahiplik başarıyla değiştirildi.";

    } catch (Exception $e) {
        Log::write("Sahiplik değiştirilirken hata oluştu: " . $e->getMessage(), "error");
        $message = "Ftp User Bil: " . $e->getMessage() . "<br>";
        /*echo json_encode([
            "status" => "error",
            "message" => $message
        ]);
        exit;*/
    }

    echo json_encode([
        "status" => "success",
        "message" => $message
    ]);
    exit;
}
elseif ($action == "setupSite5"){
    $message = "";
    try {

        $ftpUser = substr($username, 0, 32);
        $ftp = new FTPClient($serverIP, $ftpUser, $password);

        $fileNames = ["Domain.php", "Key.php", "Sql.php"];
        foreach ($fileNames as $value) {
            $localPath = "$root/App/Config/$value";
            $remotePath = "/httpdocs/App/Config/$value";

            if (!file_exists($localPath)) {
                throw new Exception("Dosya bulunamadı: $localPath");
            }

            $message .= $ftp->uploadFile($localPath, $remotePath);
        }

        $ftp->close();
    } catch (Exception $e) {

       $message .= "Hata: " . $e->getMessage();
    }

    $message .= "Config dosyaları yüklendi.<br>";

    echo json_encode([
        "status" => "success",
        "message" => $message
    ]);
}
elseif ($action == "remoteDB"){
    //alanadı/Admin/remoteDB.php sayfasını uzaktan çalıştıralım.
    $url = "https://$domain/Setup/remoteDB.php";
    Log::write("Uzaktan çalıştırma: $url", "info");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // ssl false
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        Log::write("Hata: " . curl_error($ch), "error");
    }

    curl_close($ch);

    echo $response;
}
exit;



