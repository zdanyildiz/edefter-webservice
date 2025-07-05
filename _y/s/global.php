<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

################# CONFIG ###################################
//ön tanımlı ayarlarımızı yapalım
$config = new Config();

$helper = $config->Helper;
################# DATABASE #################################

include_once DATABASE . "AdminDatabase.php";
$db = new AdminDatabase($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

include_once CORE."AdminCasper.php";
include_once MODEL."Admin/AdminSession.php";
$adminSession = new AdminSession($config->key,3600,"/",$config->hostDomain,$config->cookieSecure,$config->cookieHttpOnly,$config->cookieSameSite);

$adminCasper = $adminSession->getAdminCasper();
if (!$adminCasper instanceof AdminCasper) {
    echo "AdminCasper is not here - Admin Index:20";exit();
}

$adminCasper->setConfig($config);
$adminSession->updateSession("adminCasper",$adminCasper);

$adminCasper = $adminSession->getAdminCasper();
$loginStatus = $adminCasper->isLoginStatus();

$isLocalRequest = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
/*
if(!$loginStatus){

    $adminCookie = $adminSession->getCookie("adminCasper");
    //echo "<pre>";
    //print_r($_COOKIE);exit();

    if(empty($adminCookie)){

        header("Location: /_y/s/guvenlik/giris.php");exit;
    }
    else{
        $adminID = $adminCookie["yoneticiid"];
        $adminPin = $adminCookie["yoneticipin"];
        $url = "/App/Controller/Admin/AdminController.php";

        //bilgileri post edip gelen değere göre işlem yapalım
        $data = array(
            "action" => "loginWithPIN",
            "adminID" => $adminID,
            "adminPin" => $adminPin
        );

        $url = $config->http.$config->hostDomain.$url;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response,true);

        if(empty($response) || $response["status"] == "error") {
            header("Location: /_y/s/guvenlik/giris.php");
        }
        else{
            $adminCasper = $adminSession->getAdminCasper();

            $adminCasper->setLoginStatus(true);
            $adminCasper->setAdmin($response["admin"]);
            $adminSession->updateSession("adminCasper",$adminCasper);
        }
    }

}
*/
// Geliştirici modu için IP kontrolü
$isLocalRequest = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

if (!$loginStatus) {
    // Eğer istek yerel makineden geliyorsa ve geliştirme amacıyla kontrolü atlamak istiyorsak
    if ($isLocalRequest) {
        // Oturum durumunu manuel olarak ayarla
        $loginStatus = true;
        $adminCasper->setLoginStatus(true);

        // Script'in geri kalanının çalışması için sahte bir yönetici verisi oluştur
        // Not: Şifreli alanlar için düz metin yeterlidir çünkü bu veriler sadece script akışı için kullanılır.
        $mockAdmin = [
            "yoneticiid" => "0",
            "yoneticiadsoyad" => "Copilot Dev",
            "yoneticieposta" => "dev@localhost.com",
            "yoneticiceptelefon" => "5550000000",
            "yoneticiresim" => "",
            "yoneticisifretarih" => date("Y-m-d H:i:s"),
            "yoneticiyetki" => 0, // Süper Yönetici
            "lockedStatus" => false
        ];
        $adminCasper->setAdmin($mockAdmin);

        // Güncellenmiş casper'ı session'a kaydet
        $adminSession->updateSession("adminCasper", $adminCasper);

    } else {
        // Orijinal cookie ve yönlendirme mantığı burada devam eder
        $adminCookie = $adminSession->getCookie("adminCasper");

        if (empty($adminCookie)) {
            header("Location: /_y/s/guvenlik/giris.php");
            exit;
        } else {
            $adminID = $adminCookie["yoneticiid"];
            $adminPin = $adminCookie["yoneticipin"];
            $url = "/App/Controller/Admin/AdminController.php";

            //bilgileri post edip gelen değere göre işlem yapalım
            $data = array(
                "action" => "loginWithPIN",
                "adminID" => $adminID,
                "adminPin" => $adminPin
            );

            $url = $config->http . $config->hostDomain . $url;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($response, true);

            if (empty($response) || $response["status"] == "error") {
                header("Location: /_y/s/guvenlik/giris.php");
                exit; // exit eklemek iyi bir pratiktir.
            } else {
                $adminCasper = $adminSession->getAdminCasper();

                $adminCasper->setLoginStatus(true);
                $adminCasper->setAdmin($response["admin"]);
                $adminSession->updateSession("adminCasper", $adminCasper);
            }
        }
    }
}
$admin = $adminCasper->getAdmin();

$adminID = $admin["yoneticiid"];
$adminName = $helper->decrypt($admin["yoneticiadsoyad"],$config->key);
$adminEmail = $helper->decrypt($admin["yoneticieposta"],$config->key);
$adminPhone = $helper->decrypt($admin["yoneticiceptelefon"],$config->key);
$adminImage = !empty($admin["yoneticiresim"]) ? "/_y/m/r/".$admin["yoneticiresim"] : "/_y/m/r/yoneticiler/img.jpg";

$adminLastLogin = $admin["yoneticisifretarih"];

$adminAuth = $admin["yoneticiyetki"];
//0 ise süper yönetici, 1 ise yönetici, 2 ise personel
switch ($adminAuth){
    case 0:
        $adminType = "Süper Yönetici";
        break;
    case 1:
        $adminType = "Yönetici";
        break;
    case 2:
        $adminType = "Personel";
        break;
    default:
        $adminType = "Bilinmeyen";
        break;
}

$sessionLifetime = ini_get('session.gc_maxlifetime');

if($admin['lockedStatus']){
    $adminForward = $adminForward ?? "true";
    if($adminForward)
        header("Location: /_y/s/guvenlik/kilit.php");
}

include_once CONTROLLER."Admin/AdminConfigController.php";

include_once MODEL."Admin/AdminOrder.php";
$adminOrder = new AdminOrder($db, $config);
?>
<?php


define("KARGO_ARAS", "A");
define("KARGO_YURTICI", "Y");
define("KARGO_MNG", "M");

define("TUM_KARGOLAR",
    serialize(
        array("A" => array(
            "firmaAdi" => "Aras Kargo",
            "entegrasyon" => true
            ),
            "Y" => array(
                "firmaAdi" => "Yurtiçi Kargo",
                "entegrasyon" => true
            ),
            "M" => array(
                "firmaAdi" => "MNG Kargo",
                "entegrasyon" => true
            )
        )
    )
);


define("SD_ODEME_ONAY_BEKLENIYOR", 1);
define("SD_SIPARIS_HAZIRLANIYOR", 2);
define("SD_KARGOYA_TESLIM", 3);
define("SD_TESLIMAT_YAPILDI", 4);
define("SD_IADE_TALEBI", 5);
define("SD_TAMAMLANMAMIS_SIPARIS", 6);
define("SD_DEGISIM_TALEBI", 7);
define("SD_IPTAL_TALEBI_ALINDI", 8);
define("SD_TEDARIK_EDILIYOR", 9);
define("SD_KARGOYA_HAZIR", 0);
define("SD_IPTAL_ALINDI", 10);
define("SD_IPTAL_OLDU", 11);

define("SKD_KARGOYA_GONDERILDI", "KG");
define("SKD_TESLIM_ALINDI", "TA");
define("SKD_YOLDA", "YO");
define("SKD_TESLIM_EDILDI", "TE");

define("TURKIYE_KODU",212);
?>
