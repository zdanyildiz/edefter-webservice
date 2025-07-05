<?php
/**
 * @var Database $db
 * @var Helper $helper
 * @var Session $session
 * @var array $requestData
 * @var Json $json
 */

/**
 * @todo şifre güncelleme bildirimi gelmiyor
 */
$casper = $session->getCasper();

if (!$casper instanceof Casper) {
    echo "Casper is not here - MemberController:15";exit();
}

$config = $casper->getConfig();
$helper = $config->Helper;

$routerResult = $session->getSession("routerResult");

if(empty($routerResult)){
    $router = new Router($config);
    //$routerResult = $router->getHomepageContent(0);
    $session->addSession("routerResult",$routerResult);
}

$languageID = $routerResult["languageID"] ?? 1;
$languageCode = (isset($routerResult["languageCode"])) ? $helper->toLowerCase($routerResult["languageCode"]) : "tr";

$languageModel =new Language($db,$languageCode);
$languageModel->getTranslations($languageCode);

$siteConfig = $casper->getSiteConfig();
$currentSiteConfigVersion = $siteConfig['siteConfigVersion'] ?? -1;

$siteConfigInfo = new SiteConfig($db,$languageID);
$siteConfigVersion = $siteConfigInfo->siteConfigVersion;

if(empty($siteConfig) || $siteConfig["generalSettings"]["dilid"]!=$languageID || $siteConfigVersion!=$currentSiteConfigVersion){
    $siteConfigInfo->createSiteConfig();
    $casper->setSiteConfig($siteConfigInfo->getSiteConfig());
    $session->updateSession("casper",$casper);
    $siteConfig = $casper->getSiteConfig();
}

//Log::write("MemberController->Casper ziyaretçi bilgisi isteniyor","special");

$visitor = $casper->getVisitor();
if(!isset($visitor['visitorUniqID'])){

    //Log::write("MemberController->Casper Ziyaretçi bilgisi boş","special");

    header('Location: /?visitorID-None');exit();
}

$siteConfig = $casper->getSiteConfig();
$pageLinks = $siteConfig['specificPageLinks'];

foreach ($pageLinks as $pageLink) {
    switch ($pageLink['sayfatip']) {
        case 1:
            $contactLink = $pageLink['link'];
            break;
        case 2:
            $newsLink = $pageLink['link'];
            break;
        case 3:
            $galleryLink = $pageLink['link'];
            break;
        case 4:
            $videoLink = $pageLink['link'];
            break;
        case 5:
            $fileLink = $pageLink['link'];
            break;
        case 6:
            $announcementLink = $pageLink['link'];
            break;
        case 7:
            $productLink = $pageLink['link'];
            break;
        case 8:
            $cartLink = $pageLink['link'];
            break;
        case 9:
            $checkoutLink = $pageLink['link'];
            break;
        case 10:
            $membershipAgreementLink = $pageLink['link'];
            break;
        case 11:
            $dealerLoginLink = $pageLink['link'];
            break;
        case 12:
            $distanceSalesLink = $pageLink['link'];
            break;
        case 13:
            $cookiePolicyLink = $pageLink['link'];
            break;
        case 14:
            $termsAndConditionsLink = $pageLink['link'];
            break;
        case 15:
            $privacyPolicyLink = $pageLink['link'];
            break;
        case 16:
            $brandsLink = $pageLink['link'];
            break;
        case 17:
            $memberLink = $pageLink['link'];
            break;
        case 18:
            $cancelReturnFormLink = $pageLink['link'];
            break;
        case 19:
            $favoriteLink = $pageLink['link'];
            break;
        case 20:
            $catalogsLink = $pageLink['link'];
            break;
        case 21:
            $aboutUsLink = $pageLink['link'];
            break;
        case 22:
            $paymentLink = $pageLink['link'];
            break;
        case 23:
            $generalLink = $pageLink['link'];
            break;
        case 24:
            $blogLink = $pageLink['link'];
            break;
        case 25:
            $kvkkLink = $pageLink['link'];
            break;
    }
}

$action = $requestData['action'] ?? null;
if(!isset($action)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Lütfen gerekli alanları doldurunuz.',
        'memberData' => []
    ]);
    exit();
}

$returnLink = $requestData["referrer"];

$memberStatus = $visitor['visitorIsMember']['memberStatus'];

// Member modeli yüklenir
require_once MODEL . 'Member.php';
$member = new Member($db);

if ($memberStatus) {
    //header("Location: " . $returnLink."?memberError"); exit();
    $memberID = $visitor['visitorIsMember']['memberID'];
    $config->includeClass('Member');
    $member = new Member($db);

}
else{
    $memberID = 0;
}

if ($action == "register")
{
    $websites = $_GET['websites'] ?? $requestData['websites'];
    if (!empty($websites)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $csrfToken = $requestData['csrf_token'] ?? null;

    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf"
        ]);
        exit();
    }

    $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
    $defaultSecretKey = $cloudflareConfig['default']['secret_key'];
    $currentHostname = $_SERVER['HTTP_HOST'];
    if (isset($cloudflareConfig['sites'][$currentHostname])) {
        $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['secret_key'];
    }

    $token = $requestData['cf-turnstile-response']; // Turnstile token
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $data = [
        'secret' => $defaultSecretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'], // Opsiyonel
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $verification = json_decode($result);

    if (!$verification->success) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $visitorUniqID = $visitor['visitorUniqID'];
    $name = $requestData['name'] ?? "";
    $surname = $requestData['surname'] ?? "";
    $email = $requestData['email'] ?? "";
    $phone = $requestData['phone'] ?? "";
    $password = $requestData['password'] ?? "";


    if (empty($name) || empty($surname) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_eksiksiz_doldurun_yazi
        ]);
        exit();
    }

    $password = $helper->encrypt($password, $config->key);
    $memberTitle = $helper->encrypt($name . " " . $surname, $config->key);
    $name = $helper->encrypt($name, $config->key);
    $surname = $helper->encrypt($surname, $config->key);
    $email = $helper->encrypt($email, $config->key);
    $phone = $helper->encrypt($phone, $config->key);

    $checkMemeber = $member->getMemberInfoByEmail($email);
    if($checkMemeber){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_kaydi_basarisiz." "._uye_eposta_kayitli
        ]);
        exit();
    }

    $checkMemeber = $member->getMemberInfoByTelephone($phone);
    if($checkMemeber){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_kaydi_basarisiz." "._uye_telefon_kayitli
        ]);
        exit();
    }

    $memberPostData = [
        'benzersizid' => $visitorUniqID,
        'uyeolusturmatarih' => date("Y-m-d H:i:s"),
        'uyeguncellemetarih' => date("Y-m-d H:i:s"),
        'uyetip' => 1,
        'uyetcno' => '',
        'memberTitle' => $memberTitle,
        'uyead' => $name,
        'uyesoyad' => $surname,
        'uyetelefon' => $phone,
        'uyeeposta' => $email,
        'uyesifre' => $password,
        'uyeaktif' =>0,
        'uyesil' => 0
    ];

    $member->beginTransaction();

    $result = $member->register($memberPostData);

    if(!$result){
        $member->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_basarisiz_yanit
        ]);
        exit();
    }

    $member->commit();

    require_once Helpers . 'EmailSender.php';
    $emailSender = new EmailSender();
    $emailSubject = _uye_ol_eposta_konu;

    $companyInfo = $siteConfig['companySettings'];
    $companyName = $companyInfo['ayarfirmakisaad'];
    $companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
    $companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
    $companyEmail = $companyInfo['ayarfirmaeposta'];

    $logoInfo = $siteConfig['logoSettings'];
    $logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

    $verificationLink = $config->http.$config->hostDomain."/?/control/member/get/verificationCode&userId=$visitorUniqID&email=".urlencode($email);;

    $emailTemplate = file_get_contents(Helpers.'mail-template/newMember.php');
    $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
    $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
    $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
    $emailTemplate = str_replace("[email-title]", _uye_ol_eposta_title, $emailTemplate);
    $emailTemplate = str_replace("[email-description]", _uye_ol_eposta_aciklama, $emailTemplate);
    $emailTemplate = str_replace("[verificationLink]", $verificationLink, $emailTemplate);
    $emailTemplate = str_replace("[email-verification-button]", _uye_ol_eposta_dogrula_buton, $emailTemplate);
    $emailTemplate = str_replace("[email-verification-button-description]", _uye_ol_eposta_dogrula_buton_aciklama, $emailTemplate);
    $emailTemplate = str_replace("[email-end-description]", _uye_ol_eposta_bitis_yazi, $emailTemplate);
    $emailTemplate = str_replace("[name-surname]", $requestData['name']." ".$requestData['surname'], $emailTemplate);
    $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
    $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
    $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);

    $mailResult = $emailSender->sendEmail($requestData['email'],$requestData['name']." ".$requestData['surname'], $emailSubject, $emailTemplate);
    if(!$mailResult){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_eposta_gonderim_basarisiz
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'message' => _uye_ol_eposta_gonderim_basarili
    ]);
    exit();
}
elseif ($action == "login") {

    Log::write("Login return Link: $returnLink");
    $websites = $_GET['websites'] ?? $requestData['websites'];
    if (!empty($websites)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." websites Login"
        ]);
        exit();
    }

    $csrfToken = $requestData['csrf_token'] ?? null;

    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf Login"
        ]);
        exit();
    }

    $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
    $defaultSecretKey = $cloudflareConfig['default']['secret_key'];
    $currentHostname = $_SERVER['HTTP_HOST'];
    if (isset($cloudflareConfig['sites'][$currentHostname])) {
        $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['secret_key'];
    }

    $token = $requestData['cf-turnstile-response']; // Turnstile token
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $data = [
        'secret' => $defaultSecretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'], // Opsiyonel
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $verification = json_decode($result);

    if (!$verification->success) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." Cloudflare Login"
        ]);
        exit();
    }

    if (!isset($requestData['email']) || !isset($requestData['password'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
            'memberData' => []
        ]);
        exit();
    }
    $email = $requestData['email'];
    $password = $requestData['password'];

    // verileri şifreleyelim
    $email = $helper->encrypt($email, $config->key);
    $password = $helper->encrypt($password, $config->key);

    // Üye girişi yapılır
    $result = $member->login($email, $password);

    if($result){

        $memberData = $result[0];
        $memberDataConvert = [
            'memberStatus' => true,
            'memberIdentificationNumber'=> (!empty($memberData['uyetcno'])) ? $helper->decrypt($memberData['uyetcno'], $config->key) : "",
            'memberID' => $memberData['uyeid'],
            'memberUniqID' => $memberData['benzersizid'],
            'memberCreateDate' => $memberData['uyeolusturmatarih'],
            'memberUpdateDate' => $memberData['uyeguncellemetarih'],
            'memberType' => $memberData['uyetip'],
            'memberTitle' => $memberData['memberTitle'],
            'memberFirstName' => (!empty($memberData['uyead'])) ? $helper->decrypt($memberData['uyead'], $config->key) : "" ,
            'memberLastName' => (!empty($memberData['uyesoyad'])) ?$helper->decrypt($memberData['uyesoyad'], $config->key) : "",
            'memberEmail' => (!empty($memberData['uyeeposta'])) ? $helper->decrypt($memberData['uyeeposta'], $config->key) :"",
            'memberPhone' => ($memberData['uyetelefon']) ? $helper->decrypt($memberData['uyetelefon'], $config->key) : "",
            'memberDescription' => $memberData['uyeaciklama'],
            'memberInvoiceName' => $helper->decrypt($memberData['uyefaturaad'], $config->key),
            'memberInvoiceTaxOffice' => $helper->decrypt($memberData['uyefaturavergidairesi'], $config->key),
            'memberInvoiceTaxNumber' => $helper->decrypt($memberData['uyefaturavergino'], $config->key),
            'memberActive' => $memberData['uyeaktif']
        ];

        $visitor = $casper->getVisitor();
        //print_r($visitor);exit();

        // oturumdaki ziyaretçi bilgileri alınır
        $visitorUniqID = $visitor['visitorUniqID'];

        // oturumdaki ziyaretçi benzersizid'si ile üye benzersizid'si eşleştirilir
        $visitor['visitorUniqID'] = $memberData['benzersizid'];

        // oturumdaki ziyaretçi bilgileri güncellenir
        $visitor['visitorIsMember'] = $memberDataConvert;

        // sepetteki ziyaretçi benzersizid ile üye benzersizid'si eşleştirilir
        $config->includeClass('Cart');
        $cart = new Cart($db, $helper, $session, $config);
        $cart->updateCartFromVisitorUniqIDtoMemberUniqID($visitorUniqID, $memberData['benzersizid']);

        // üyenin sepet bilgileri alınır
        $cartInfo = $cart->getCart($memberData['benzersizid']);

        // üyenin sepet bilgileri oturuma kaydedilir
        $visitor['visitorCart'] = $cartInfo;

        // oturumdan aldığımız ziyaretçi bilgilerini güncelle
        $visitor['visitorEntryTime'] = date("Y-m-d H:i:s");

        $remember = isset($requestData['remember']) ? true : false;
        $visitor['visitorRemember'] = $remember;


        $session = new Session($config->key, 3600, "/", $config->hostDomain, $config->cookieSecure, $config->cookieHttpOnly, $config->cookieSameSite);

        $casper->setVisitor($visitor);
        $session->updateSession('casper', $casper);

        $cookieVisitor = $visitor;

        unset($cookieVisitor['visitorCart']);
        unset($cookieVisitor['visitorIsMember']['memberIdentificationNumber']);
        unset($cookieVisitor['visitorIsMember']['memberUniqID']);
        unset($cookieVisitor['visitorIsMember']['memberCreateDate']);
        unset($cookieVisitor['visitorIsMember']['memberUpdateDate']);

        unset($cookieVisitor['visitorIsMember']['memberName']);
        unset($cookieVisitor['visitorIsMember']['memberFirstName']);
        unset($cookieVisitor['visitorIsMember']['memberLastName']);

        unset($cookieVisitor['visitorIsMember']['memberDescription']);
        unset($cookieVisitor['visitorIsMember']['memberInvoiceName']);
        unset($cookieVisitor['visitorIsMember']['memberInvoiceTaxOffice']);
        unset($cookieVisitor['visitorIsMember']['memberInvoiceTaxNumber']);

        unset($cookieVisitor['visitorIsMember']['memberActive']);

        $session->addCookie('visitor', $cookieVisitor, 12);

        //geldiği sayfaya geri döndür
        if($returnLink=="json"){
            echo json_encode([
                'status' => 'success',
                'message' => 'Giriş yapıldı.',
                'memberData' => []
            ]);
            exit();
        }
        $session->addSession('popup', [
            'status' => 'success',
            'message' => 'Giriş yapıldı.',
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
        ]);
        header("Location: ".$returnLink);
        exit();
    }


    //geldiği sayfaya geri döndür
    if($returnLink=="json"){
        echo json_encode([
            'status' => 'error',
            'message' => 'E-posta ya da Şifre Hatası',
            'memberData' => []
        ]);
        exit();
    }

    $session->addSession('popup', [
        'status' => 'error',
        'message' => 'E-posta ya da Şifre Hatası',
        'position' => 'top-right',
        'width' => '300px',
        'height' => '100px',
        'closeButton' => true,
        'autoClose' => false,
        'animation' => true,
    ]);
    header("Location: ".$returnLink);
    exit();
}
elseif ($action == "logout") {

    $visitor = $casper->getVisitor();
    $visitor['visitorRemember'] = false;
    $visitor['visitorIsMember'] = ['memberStatus' => false];

    unset($visitor['visitorIsMember']['memberIdentificationNumber']);
    unset($visitor['visitorIsMember']['memberUniqID']);
    unset($visitor['visitorIsMember']['memberCreateDate']);
    unset($visitor['visitorIsMember']['memberUpdateDate']);
    unset($visitor['visitorIsMember']['memberName']);
    unset($visitor['visitorIsMember']['memberFirstName']);
    unset($visitor['visitorIsMember']['memberLastName']);
    unset($visitor['visitorIsMember']['memberDescription']);
    unset($visitor['visitorIsMember']['memberInvoiceName']);
    unset($visitor['visitorIsMember']['memberInvoiceTaxOffice']);
    unset($visitor['visitorIsMember']['memberInvoiceTaxNumber']);
    unset($visitor['visitorIsMember']['memberActive']);

    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);

    $visitorCookie = $session->getCookie('visitor');
    $visitorCookie['visitorIsMember'] = ['memberStatus' => false];
    $visitorCookie['visitorRemember'] = false;
    $visitorCookie['visitorGeo']=[];
    //echo '<pre>';print_r($visitor);print_r($visitorCookie);exit();

    $session->addCookie('visitor', $visitorCookie, 1);

    if($returnLink=="json"){
        echo json_encode([
            'status' => 'success',
            'message' => 'Çıkış yapıldı.',
            'memberData' => []
        ]);
        exit();
    }
    header("Location: ".$returnLink);exit();
}
elseif ($action == "remindPasswordByEmail"){

    $websites = $_GET['websites'] ?? $requestData['websites'];
    if (!empty($websites)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $csrfToken = $requestData['csrf_token'] ?? null;

    //csrf token kontrolü yapalım
    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf"
        ]);
        exit();
    }

    $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
    $defaultSecretKey = $cloudflareConfig['default']['secret_key'];
    $currentHostname = $_SERVER['HTTP_HOST'];
    if (isset($cloudflareConfig['sites'][$currentHostname])) {
        $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['secret_key'];
    }

    $token = $requestData['cf-turnstile-response']; // Turnstile token
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $data = [
        'secret' => $defaultSecretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'], // Opsiyonel
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $verification = json_decode($result);

    if (!$verification->success) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $email = $requestData['email'];

    if (!isset($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz. [e-posta]',
            'memberData' => []
        ]);
        exit();
    }

    $encryptedEmail = $helper->encrypt($email, $config->key);

    // Üye bilgileri getirilir
    $result = $member->getMemberInfoByEmail($encryptedEmail);
    if (!$result) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_eposta_kayitli_degil
        ]);
        exit();
    }

    $memberData = $result[0];
    $email = $memberData['uyeeposta'];
    $password = $memberData['uyesifre'];
    $memberName = $memberData['uyead'];
    $memberName = $helper->decrypt($memberName, $config->key);
    $memberSurname = $memberData['uyesoyad'];
    $memberSurname = $helper->decrypt($memberSurname, $config->key);

    if(empty($password)){
        $member->beginTransaction();
        $newPassword = $helper->encrypt($helper->createPassword(8,2), $config->key);
        $passwordUpdateResult = $member->updatePasswordByEmail($encryptedEmail,$newPassword);

        if($passwordUpdateResult <= 0){
            $member->rollback();
            echo json_encode([
                'status' => 'error',
                'message' => _uye_sifre_guncellenemedi
            ]);
            exit();
        }
        $member->commit();
        $password = $newPassword;
    }


    $reminderToken = $encryptedEmail . $password;
    $reminderLink  = $config->http.$config->hostDomain."/?/control/member/get/passwordReset&email=".$requestData['email']."&token=".$reminderToken;

    include_once Helpers. 'EmailSender.php';
    $emailSender = new EmailSender();

    $emailSubject = _uye_sifre_sifirlama;

    $companyInfo = $casper->getSiteConfig()['companySettings'];
    $companyName = $companyInfo['ayarfirmakisaad'];
    $companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
    $companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
    $companyEmail = $companyInfo['ayarfirmaeposta'];

    $logoInfo = $casper->getSiteConfig()['logoSettings'];
    $logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

    $emailTemplate = file_get_contents(Helpers.'mail-template/passwordResetTR.php');
    $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
    $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
    $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
    $emailTemplate = str_replace("[password-reset-link]", $reminderLink, $emailTemplate);
    $emailTemplate = str_replace("[member-name-surname]", $memberName." ".$memberSurname, $emailTemplate);
    $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
    $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
    $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);

    $sendMail = $emailSender->sendEmail($requestData['email'], $memberName." ".$memberSurname, $emailSubject, $emailTemplate);
    if($sendMail){
        echo json_encode([
            'status' => 'success',
            'message' => _uye_sifre_sifirlama_eposta_sonuc
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => _uye_sifre_sifirlama_eposta_sonuc_basarisiz
        ]);
        exit();
    }

}
elseif ($action == "remindPasswordByEmailWithUserID"){

    $csrfToken = $requestData['csrf_token'] ?? null;

    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf"
        ]);
        exit();
    }

    $email = $requestData['email'];

    if (!isset($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz. [e-posta]',
            'memberData' => []
        ]);
        exit();
    }

    $encryptedEmail = $helper->encrypt($email, $config->key);

    // Üye bilgileri getirilir
    $result = $member->getMemberInfoByEmail($encryptedEmail);
    if (!$result) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_eposta_kayitli_degil
        ]);
        exit();
    }

    $memberData = $result[0];
    $email = $memberData['uyeeposta'];
    $password = $memberData['uyesifre'];
    $memberName = $memberData['uyead'];
    $memberName = $helper->decrypt($memberName, $config->key);
    $memberSurname = $memberData['uyesoyad'];
    $memberSurname = $helper->decrypt($memberSurname, $config->key);

    if(empty($password)){
        $member->beginTransaction();
        $newPassword = $helper->encrypt($helper->createPassword(8,2), $config->key);
        $passwordUpdateResult = $member->updatePasswordByEmail($encryptedEmail,$newPassword);

        if($passwordUpdateResult <= 0){
            $member->rollback();
            echo json_encode([
                'status' => 'error',
                'message' => _uye_sifre_guncellenemedi
            ]);
            exit();
        }
        $member->commit();
        $password = $newPassword;
    }


    $reminderToken = $encryptedEmail . $password;
    $reminderLink  = $config->http.$config->hostDomain."/?/control/member/get/passwordReset&email=".$requestData['email']."&token=".$reminderToken;

    include_once Helpers. 'EmailSender.php';
    $emailSender = new EmailSender();

    $emailSubject = _uye_sifre_sifirlama;

    $companyInfo = $casper->getSiteConfig()['companySettings'];
    $companyName = $companyInfo['ayarfirmakisaad'];
    $companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
    $companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
    $companyEmail = $companyInfo['ayarfirmaeposta'];

    $logoInfo = $casper->getSiteConfig()['logoSettings'];
    $logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

    $emailTemplate = file_get_contents(Helpers.'mail-template/passwordResetTR.php');
    $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
    $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
    $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
    $emailTemplate = str_replace("[password-reset-link]", $reminderLink, $emailTemplate);
    $emailTemplate = str_replace("[member-name-surname]", $memberName." ".$memberSurname, $emailTemplate);
    $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
    $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
    $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);

    $sendMail = $emailSender->sendEmail($requestData['email'], $memberName." ".$memberSurname, $emailSubject, $emailTemplate);
    if($sendMail){
        echo json_encode([
            'status' => 'success',
            'message' => _uye_sifre_sifirlama_eposta_sonuc
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => _uye_sifre_sifirlama_eposta_sonuc_basarisiz
        ]);
        exit();
    }

}
elseif ($action == "updateMember") {

    $csrfToken = $requestData['csrf_token'] ?? null;

    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf"
        ]);
        exit();
    }
    //die("updateMember");
    $visitor = $casper->getVisitor();
    $memberID = $visitor['visitorIsMember']['memberID'];

    $identificationNumber = $requestData['memberIdentificationNumber'] ?? "";
    $name = $requestData['name'] ?? "";
    $surname = $requestData['surname'] ?? "";
    $email = $requestData['email'] ?? "";
    $telephone = $requestData['telephone'] ?? "";
    $invoiceName = $requestData['invoiceName'] ?? "";
    $invoiceTaxOffice = $requestData['invoiceTaxOffice'] ?? "";
    $invoiceTaxNumber = $requestData['invoiceTaxNumber'] ?? "";

    if (empty($identificationNumber) || empty($name) || empty($surname) || empty($email) || empty($telephone) || empty($invoiceName) || empty($invoiceTaxOffice) || empty($invoiceTaxNumber)) {
        //die("$returnLink");
        $session->addSession('popup', [
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);
        header("Location: " . $returnLink);
        exit();
    }


    $memberData = [
        'uyeid' => $memberID,
        'uyeguncellemetarih' => date("Y-m-d H:i:s"),
        'uyetcno' => $helper->encrypt($requestData['identificationNumber'], $config->key),
        'memberTitle' => $requestData['name'] . " " . $requestData['surname'],
        'uyead' => $helper->encrypt($requestData['name'],$config->key),
        'uyesoyad' => $helper->encrypt($requestData['surname'],$config->key),
        'uyeeposta' => $helper->encrypt($requestData['email'],$config->key),
        'uyetelefon' => $helper->encrypt($requestData['telephone'],$config->key),
        'uyefaturaad' => $helper->encrypt($requestData['invoiceName'],$config->key),
        'uyefaturavergidairesi' => $helper->encrypt($requestData['invoiceTaxOffice'],$config->key),
        'uyefaturavergino' => $helper->encrypt($requestData['invoiceTaxNumber'],$config->key)
    ];

    $member->beginTransaction();

    $result = $member->update($memberData);

    if ($result >= 0) {
        $member->commit();
        $resultType = 'success';
        $resultMessage =  _uye_guncelle_form_basarili_yanit;
    }
    else {
        $member->rollBack();
        $resultType = 'error';
        $resultMessage = _uye_guncelle_form_basarisiz_yanit;
    }

    if ($resultType == "success") {

        $memberData = $member->getMemberInfo($memberID);
        $memberData = $memberData[0];
        $visitor = $casper->getVisitor();

        $visitor['visitorIsMember'] = [
            'memberStatus' => true,
            'memberIdentificationNumber'=> $helper->decrypt($memberData['uyetcno'], $config->key),
            'memberID' => $memberData['uyeid'],
            'memberUniqID' => $memberData['benzersizid'],
            'memberCreateDate' => $memberData['uyeolusturmatarih'],
            'memberUpdateDate' => $memberData['uyeguncellemetarih'],
            'memberType' => $memberData['uyetip'],
            'memberTitle' => $memberData['memberTitle'],
            'memberFirstName' => $helper->decrypt($memberData['uyead'], $config->key),
            'memberLastName' => $helper->decrypt($memberData['uyesoyad'], $config->key),
            'memberEmail' => $helper->decrypt($memberData['uyeeposta'], $config->key),
            'memberPhone' => $helper->decrypt($memberData['uyetelefon'], $config->key),
            'memberDescription' => $memberData['uyeaciklama'],
            'memberInvoiceName' => $helper->decrypt($memberData['uyefaturaad'], $config->key),
            'memberInvoiceTaxOffice' => $helper->decrypt($memberData['uyefaturavergidairesi'], $config->key),
            'memberInvoiceTaxNumber' => $helper->decrypt($memberData['uyefaturavergino'], $config->key),
            'memberActive' => $memberData['uyeaktif']
        ];

        $casper->setVisitor($visitor);
        $session->updateSession('casper', $casper);

        unset($visitor['visitorIsMember']['memberIdentificationNumber']);
        unset($visitor['visitorIsMember']['memberUniqID']);
        unset($visitor['visitorIsMember']['memberCreateDate']);
        unset($visitor['visitorIsMember']['memberUpdateDate']);
        unset($visitor['visitorIsMember']['memberName']);
        unset($visitor['visitorIsMember']['memberFirstName']);
        unset($visitor['visitorIsMember']['memberLastName']);
        unset($visitor['visitorIsMember']['memberDescription']);
        unset($visitor['visitorIsMember']['memberInvoiceName']);
        unset($visitor['visitorIsMember']['memberInvoiceTaxOffice']);
        unset($visitor['visitorIsMember']['memberInvoiceTaxNumber']);
        unset($visitor['visitorIsMember']['memberActive']);

        $session->addCookie('visitor', $visitor, 1);
        if($returnLink!='json'){
            $session->addSession('popup', [
                'status' => $resultType,
                'message' => $resultMessage,
                'position' => 'top-right',
                'width' => '300px',
                'height' => '100px',
                'closeButton' => true,
                'autoClose' => true,
                'animation' => true,
            ]);
        }
    }
    else
    {
        if($returnLink!='json'){
            $session->addSession('popup', [
                'status' => $resultType,
                'message' => $resultMessage,
                'position' => 'top-right',
                'width' => '300px',
                'height' => '100px',
                'closeButton' => true,
                'autoClose' => false,
                'animation' => true,
            ]);
        }
    }
    if($returnLink == 'json'){
        echo json_encode([
            'status' => $resultType,
            'message' => $resultMessage,
            'memberData' => []
        ]);
        exit();
    }


    //$session->removeSession('postData');
    header("Location: " . $returnLink."?profile");
    exit();
}
elseif ($action == "updatePassword"){
    $visitor = $casper->getVisitor();
    $memberID = $visitor['visitorIsMember']['memberID'];
    $memberData = [
        'memberID' => $memberID,
        'password' => $helper->encrypt($requestData['password'],$config->key),
        'newPassword' => $helper->encrypt($requestData['newPassword'],$config->key)
    ];

    $result = $member->updatePassword($memberData);
    //print_r($result);exit();
    $resultType = $result['status'];
    $resultMessage = $result['message'];
    if ($resultType == "success") {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
        ]);
        /*echo json_encode([
            'status' => 'success',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }
    else {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);
        /*echo json_encode([
            'status' => 'error',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }
    //$session->removeSession('postData');
    header("Location: " . $returnLink."?profile");
    exit();
}
elseif ($action == "addAddress"){

    $csrfToken = $requestData['csrf_token'] ?? null;

    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        $session->addSession('popup', [
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf"
        ]);
        header("Location: " . $returnLink);
        exit();
    }

    $visitor = $casper->getVisitor();
    $visitorIsMember = $visitor['visitorIsMember'];
    $visitorMemberStatus = $visitorIsMember['memberStatus'];

    if($visitorMemberStatus){
        $memberID = $visitor['visitorIsMember']['memberID'];
    }
    else
    {
        //checkout'tan gelen form

        $password = $helper->createPassword(8, 2);
        $encryptedPassword = $helper->encrypt($password, $config->key);
        $encryptedEmail = $helper->encrypt($requestData['email'], $config->key);
        $memberIdentityNo = $requestData['identificationNumber'];
        $encryptedIdentityNo = $helper->encrypt($memberIdentityNo, $config->key);
        $memberTitle = $requestData['name'] .' '.$requestData['surname'];
        $encryptedMemberTitle = $helper->encrypt($memberTitle, $config->key);
        $encryptedName = $helper->encrypt($requestData['name'], $config->key);
        $encryptedSurname = $helper->encrypt($requestData['surname'], $config->key);
        $encryptedTelephone = $helper->encrypt($requestData['telephone'], $config->key);
        $encryptedInvoiceName = $helper->encrypt($requestData['invoiceName'], $config->key);
        $encryptedInvoiceTaxOffice = $helper->encrypt($requestData['invoiceTaxOffice'], $config->key);
        $encryptedInvoiceTaxNumber = $helper->encrypt($requestData['invoiceTaxNumber'], $config->key);

        $existingMember = $member->getMemberInfoByEmail($encryptedEmail);
        if (!empty($existingMember)) {
            $memberID = $existingMember[0]['uyeid'];
        }
        else {

            $memberPostData = [
                'benzersizid' => $helper->createPassword(20, 2),
                'uyeolusturmatarih' => date("Y-m-d H:i:s"),
                'uyeguncellemetarih' => date("Y-m-d H:i:s"),
                'uyetip' => 1,
                'uyetcno' => $encryptedIdentityNo,
                'memberTitle' => $encryptedMemberTitle,
                'uyead' => $encryptedName,
                'uyesoyad' => $encryptedSurname,
                'uyetelefon' => $encryptedTelephone,
                'uyeeposta' => $encryptedEmail,
                'uyesifre' => $encryptedPassword,
                'uyefaturaad' => $encryptedInvoiceName,
                'uyefaturavergidairesi' => $encryptedInvoiceTaxOffice,
                'uyefaturavergino' => $encryptedInvoiceTaxNumber,
                'uyeaktif' => 0,
                'uyesil' => 0
            ];

            $member->beginTransaction();

            $result = $member->registerWithCheckout($memberPostData);

            if (!$result) {
                $member->rollback();
                echo json_encode([
                    'status' => 'error',
                    'message' => _uye_kaydi_basarisiz,
                    'memberData' => []
                ]);
                exit();
            }
            else {
                $member->commit();
                $memberData = $member->getMemberInfo($result,"");
                if(!$memberData){
                    echo json_encode([
                        'status' => 'error',
                        'message' => _uye_kaydi_basarisiz,
                        'memberData' => []
                    ]);
                    exit();
                }
                $memberData = $memberData[0];
                $memberDataConvert = [
                    'memberStatus' => true,
                    'identificationNumber'=> $memberData['uyetcno'],
                    'memberID' => $memberData['uyeid'],
                    'memberUniqID' => $memberData['benzersizid'],
                    'memberCreateDate' => $memberData['uyeolusturmatarih'],
                    'memberUpdateDate' => $memberData['uyeguncellemetarih'],
                    'memberType' => $memberData['uyetip'],
                    'memberTitle' => $memberData['memberTitle'],
                    'memberFirstName' => $helper->decrypt($memberData['uyead'], $config->key),
                    'memberLastName' => $helper->decrypt($memberData['uyesoyad'], $config->key),
                    'memberEmail' => $helper->decrypt($memberData['uyeeposta'], $config->key),
                    'memberPhone' => $helper->decrypt($memberData['uyetelefon'], $config->key),
                    'memberDescription' => $memberData['uyeaciklama'],
                    'memberInvoiceName' => $helper->decrypt($memberData['uyefaturaad'], $config->key),
                    'memberInvoiceTaxOffice' => $helper->decrypt($memberData['uyefaturavergidairesi'], $config->key),
                    'memberInvoiceTaxNumber' => $helper->decrypt($memberData['uyefaturavergino'], $config->key),
                    'memberActive' => $memberData['uyeaktif']
                ];

                $memberID = $memberData['uyeid'];

                $visitor['visitorIsMember'] = $memberDataConvert;

                $visitor['visitorEntryTime'] = date("Y-m-d H:i:s");



                // oturumdaki ziyaretçi bilgileri güncellenir
                $cookieVisitor = $visitor;

                $session = new Session($config->key, 3600, "/", $config->hostDomain, $config->cookieSecure, $config->cookieHttpOnly, $config->cookieSameSite);
                $session->addCookie('visitor', $cookieVisitor, 12);

                $casper->setVisitor($visitor);
                $session->updateSession('casper', $casper);
            }
        }
    }

    $addressPostData = [
        'uyeid' => $memberID,
        'adresbaslik' => $requestData['addressTitle'],
        'adrestcno' => $requestData['identificationNumber'],
        'adresad' => $requestData['name'],
        'adressoyad' => $requestData['surname'],
        'adresulke' => $requestData['addressCountry'],
        'adressehir' => $requestData['addressCity'],
        'adresilce' => $requestData['addressCounty'],
        'adressemt' => $requestData['addressArea'],
        'adresmahalle' => $requestData['addressNeighborhood'],
        'postakod' => $requestData['addressPostalCode'],
        'adresacik' => $requestData['addressStreet'],
        'adrestelefon' => $requestData['telephone'],
    ];

    // Location modeli yüklenir

    $location = new Location($db);
    $countryPhoneCode = $location->getCountryPhoneCode($requestData['addressCountry']);
    $addressPostData['adresulkekod'] = $countryPhoneCode;

    // Adres eklenir

    $result = $member->addAddress($addressPostData);
    $resultType = $result['status'];
    $result = $result['message'];
    if ($resultType == "error") {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $result,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);
        /*echo json_encode([
            'status' => 'error',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }
    else {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $result,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
        ]);
        /*echo json_encode([
            'status' => 'success',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }

    //$session->removeSession('postData');
    $returnLink = "/?/control/member/get/address";
    header("Location: " . $returnLink);exit();
}
elseif ($action == "updateAddress"){
    $addressInfo = [
        'adresbaslik' => $requestData['addressTitle'],
        'adrestcno' => $requestData['identificationNumber'],
        'adresad' => $requestData['addressName'],
        'adressoyad' => $requestData['addressSurname'],
        'adresulke' => $requestData['addressCountry'],
        'adressehir' => $requestData['addressCity'],
        'adresilce' => $requestData['addressCounty'],
        'adressemt' => $requestData['addressArea'],
        'adresmahalle' => $requestData['addressNeighborhood'],
        'postakod' => $requestData['addressPostalCode'],
        'adresacik' => $requestData['addressStreet'],
        'adrestelefon' => $requestData['addressPhone'],
        'adresid' => $requestData['addressID']
    ];
    $location = new Location($db);
    $countryPhoneCode = $location->getCountryPhoneCode($requestData['addressCountry']);
    $addressInfo['adresulkekod'] = $countryPhoneCode;

    // Adres güncellenir
    $result = $member->updateAddress($addressInfo);
    $resultType = $result['status'];
    $result = $result['message'];
    if ($resultType == "error") {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $result,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);
        /*echo json_encode([
            'status' => 'error',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }
    else {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $result,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
        ]);
        /*echo json_encode([
            'status' => 'success',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }
    //$session->removeSession('postData');
    //$returnLink = "/?/control/member/get/getAddresses";
    header("Location: " . $returnLink);exit();
}
elseif ($action == "getAddresses"){
    //echo "address";exit();
    $addresses = $member->getAddress($memberID);
    $location = new Location($db);
    foreach ($addresses as $key => $address){
        $addresses[$key]['adresulke'] = $location->getCountryNameById($address['adresulke']);
        $addresses[$key]['adressehir'] = $location->getCityNameById($address['adressehir']);
        $addresses[$key]['adresilce'] = $location->getCountyNameById($address['adresilce']);
        $addresses[$key]['adressemt'] = $location->getAreaNameById($address['adressemt']);
        $addresses[$key]['adresmahalle'] = $location->getNeighborhoodNameById($address['adresmahalle']);
    }

    $visitor['visitorIsMember']['memberAddress'] = $addresses;
    $visitor['visitorIsMember']['countries'] = $location->getAllCountries();
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);
    header("Location: " . $memberLink."?address"); exit();
}
elseif ($action == "getAddressByID"){
    $addressID = $requestData['addressID'];
    $address = $member->getAddressByID($memberID,$addressID);
    $location = new Location($db);

    if($address['adresulke']!=212){
        $address['adressehir'] = $location->getCityNameById($address['adressehir']);
        $address['adresilce'] = $location->getCountyNameById($address['adresilce']);
        $address['adressemt'] = $location->getAreaNameById($address['adressemt']);
        $address['adresmahalle'] = $location->getNeighborhoodNameById($address['adresmahalle']);
    }

    $visitor['visitorIsMember']['memberAddress'] = $address;
    $visitor['visitorIsMember']['countries'] = $location->getAllCountries();
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);
    header("Location: " . $memberLink."?updateAddress"); exit();
}
elseif ($action == "deleteAddress"){
    if(!isset($requestData['addressID'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Adres ID bilgisi eksik.',
            'memberData' => []
        ]);
        exit();
    }
    $addressInfo = [
        'addressID' => $requestData['addressID'],
        'memberID' => $memberID
    ];

    // Adres silinir
    $result = $member->deleteAddress($addressInfo);

    $resultType = $result['status'];
    $resultMessage = $result['message'];
    if ($resultType == "error") {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);

        /*echo json_encode([
            'status' => 'error',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }
    else {
        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '150px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
        ]);

        /*echo json_encode([
            'status' => 'success',
            'message' => $result,
            'memberData' => []
        ]);
        exit();*/
    }
    $returnLink = "/?/control/member/get/address";
    header("Location: " . $returnLink);exit();
}
elseif ($action == "addFavorite"){

    $productUniqID = $requestData['productUniqID'];
    $visitorUniqID = $visitor['visitorUniqID'];

    $checkFavoriteResult = $member->getFavoritesControl($visitorUniqID, $productUniqID);

    if(!$checkFavoriteResult){
        $member->beginTransaction("addFavorite");
        $result = $member->addFavorite($visitorUniqID, $productUniqID);

        if(!$result){
            $member->rollback("addFavorite");
            $resultType = 'error';
            $resultMessage = _uye_favori_ekle_basarisiz_yanit;
        }
        else{
            $member->commit("addFavorite");
            $resultType = 'success';
            $resultMessage = _uye_favori_ekle_basarili_yanit;
        }

        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
        ]);


    }
    else{
        $pageFavorite = $checkFavoriteResult[0]['pageFavorite'];
        if($pageFavorite == 0){
            $member->beginTransaction();
            $result = $member->updateFavoriteByProductUniqID($visitorUniqID, $productUniqID);

            if($result<0){
                $member->rollback();
                $resultType = 'error';
                $resultMessage = _uye_favori_ekle_basarisiz_yanit;
            }
            else{
                $member->commit();
                $resultType = 'success';
                $resultMessage = _uye_favori_ekle_basarili_yanit;
            }
        }
        else{
            $resultType = 'success';
            $resultMessage = _uye_favori_ekle_basarili_yanit;
        }

        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
        ]);
    }

    $favorites = $member->getFavorites($visitorUniqID);
    $favoriteProducts = [];
    if(!empty($favorites)){
        $config->includeClass('Product');
        $favoriteProduct = new Product($db, $json);

        foreach ($favorites as $key => $favorite){
            $productUniqID = $favorite['productUniqID'];
            $product = $favoriteProduct->getProductByUniqID($productUniqID);
            $favoriteProducts[] = $product;
        }
    }
    $visitor['visitorIsMember']['memberFavorites'] = $favoriteProducts;
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);

    header("Location: " . $returnLink);exit();
}
elseif ($action == "removeFavorite"){
    $productUniqID = $requestData['productUniqID'] ?? null;

    $visitorUniqID = $visitor['visitorUniqID'];
    $member->beginTransaction("removeFavorite");
    $result = $member->deleteFavorite($visitorUniqID, $productUniqID);

    if(!$result){
        $member->rollback("removeFavorite");
        $resultType = 'error';
            $resultMessage = _uye_favori_cikar_basarisiz_yanit;
    }
    else{
        $member->commit("removeFavorite");
        $resultType ='success';
        $resultMessage = _uye_favori_cikar_basarili_yanit;
    }

    $session->addSession('popup', [
        'status' => $resultType,
        'message' => $resultMessage,
        'position' => 'top-right',
        'width' => '300px',
        'height' => '100px',
        'closeButton' => true,
        'autoClose' => true,
        'animation' => true,
    ]);

    $favorites = $member->getFavorites($visitorUniqID);
    $favoriteProducts = [];
    if(!empty($favorites)){
        $config->includeClass('Product');
        $favoriteProduct = new Product($db, $json);

        foreach ($favorites as $key => $favorite){
            $productUniqID = $favorite['productUniqID'];
            $product = $favoriteProduct->getProductByUniqID($productUniqID);
            $favoriteProducts[] = $product;
        }
    }
    $visitor['visitorIsMember']['memberFavorites'] = $favoriteProducts;
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);

    header("Location: " . $returnLink);exit();
}
elseif ($action == "getMemberInfo"){
    $memberData = [
        'uyeid' => $requestData['uyeid']
    ];

    // Üye bilgileri getirilir
    $result = $member->getMemberInfo($memberData);
    if(!$result){
        echo json_encode([
            'status' => 'error',
            'message' => 'Üye bilgileri getirilemedi.',
            'memberData' => []
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'success',
            'message' => 'Üye bilgileri başarıyla getirildi.',
            'memberData' => []
        ]);
        exit();
    }
}
elseif ($action == "profile"){

    if($memberStatus)
    {
        $memberData = $member->getMemberInfo($visitor['visitorIsMember']['memberID']);
        $memberData = $memberData[0];
        $memberDataConvert = [
            'memberStatus' => true,
            'memberIdentificationNumber'=> (!empty($memberData['uyetcno'])) ? $helper->decrypt($memberData['uyetcno'], $config->key) : "",
            'memberID' => $memberData['uyeid'],
            'memberUniqID' => $memberData['benzersizid'],
            'memberCreateDate' => $memberData['uyeolusturmatarih'],
            'memberUpdateDate' => $memberData['uyeguncellemetarih'],
            'memberType' => $memberData['uyetip'],
            'memberTitle' => $memberData['memberTitle'],
            'memberFirstName' => (!empty($memberData['uyead'])) ? $helper->decrypt($memberData['uyead'], $config->key) : "" ,
            'memberLastName' => (!empty($memberData['uyesoyad'])) ?$helper->decrypt($memberData['uyesoyad'], $config->key) : "",
            'memberEmail' => (!empty($memberData['uyeeposta'])) ? $helper->decrypt($memberData['uyeeposta'], $config->key) :"",
            'memberPhone' => ($memberData['uyetelefon']) ? $helper->decrypt($memberData['uyetelefon'], $config->key) : "",
            'memberDescription' => $memberData['uyeaciklama'],
            'memberInvoiceName' => $helper->decrypt($memberData['uyefaturaad'], $config->key),
            'memberInvoiceTaxOffice' => $helper->decrypt($memberData['uyefaturavergidairesi'], $config->key),
            'memberInvoiceTaxNumber' => $helper->decrypt($memberData['uyefaturavergino'], $config->key),
            'memberActive' => $memberData['uyeaktif']
        ];

        $visitor['visitorIsMember'] = $memberDataConvert;
        $casper->setVisitor($visitor);
        $session->updateSession('casper', $casper);
    }
    header("Location: $memberLink?profile");exit();
}
elseif ($action == "cart"){
    //echo "cart";exit();
    header("Location: $cartLink");exit();
}
elseif ($action == "getCart"){

    // Log::write("Action getCart içindeyiz","special");

    $visitor = $casper->getVisitor();

    // oturumdaki ziyaretçi bilgileri alınır

    $visitorUniqID = $visitor['visitorUniqID'];

    // sepetteki ziyaretçi benzersizid ile üye benzersizid'si eşleştirilir

    $config->includeClass('Cart');
    $cart = new Cart($db, $helper, $session, $config);
    $cartInfo = $cart->getCart($visitorUniqID);

    //$jsonCartInfo = json_encode($cartInfo, JSON_UNESCAPED_UNICODE);
    //Log::write("Sepet bilgileri: $jsonCartInfo","special");

    // üyenin sepet bilgileri oturuma kaydedilir
    $visitor['visitorCart'] = $cartInfo;
    $visitor['visitorGetCart'] = false;

    //Log::write("bir daha yönlendirme olmasın diye visitorGetCart durumu false yapıldı","special");

    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);

    //Log::write("MemberController->ziyaretçi tekrar casper'a setlendi ve casper güncellendi","special");
    //Log::write("MemberController->Anasayfaya yönlendiriliyoruz","special");

    header("Location: /");exit();
}
elseif ($action == "orders"){

    $orderType = $requestData['orderType'] ?? "";
    $orders = $member->getOrders($memberID, $orderType);

    if(!empty($orders)){
        $config->includeClass('Product');
        $product = new Product($db,$json);

        $config->includeClass('Location');
        $location = new Location($db);

        $ordersConvertData = [];
        foreach ($orders as $key => $order){

            //orderUniqID
            $ordersConvertData[$key]['orderUniqID'] = $order['siparisbenzersizid'];
            //orderCreateDate
            $ordersConvertData[$key]['orderCreateDate'] = $order['siparistariholustur'];
            //orderStatusID
            $ordersConvertData[$key]['orderStatusID'] = $order['siparisdurum'];
            //orderStatusTitle
            $ordersConvertData[$key]['orderStatusTitle'] = $order['siparisdurumbaslik'];
            //orderTotalPrice
            $ordersConvertData[$key]['orderTotalPrice'] = $order['siparistoplamtutar'];
            //siparisparabirim
            $ordersConvertData[$key]['orderCurrencyCode'] = $order['siparisodemeparabirim'];
            //ödeme tipi
            $orderPaymentType = $order['siparisodemeyontemi'];
            //kk,bh,ko olabilir
            switch ($orderPaymentType){
                case "kk":
                    $orderPaymentType = "Kredi Kartı";
                    break;
                case "bh":
                    $orderPaymentType = "Banka Havalesi";
                    break;
                case "ko":
                    $orderPaymentType = "Kapıda Ödeme";
                    break;
                default:
                    $orderPaymentType = "Diğer";
            }

            $ordersConvertData[$key]['orderPaymentType'] = $orderPaymentType;

            //ödeme durumu
            $orderPaymentStatus = $order['siparisodemedurum'];
            //0 ve 1 olabilir
            switch ($orderPaymentStatus){
                case 0:
                    $orderPaymentStatus = "Ödeme Bekleniyor";
                    break;
                case 1:
                    $orderPaymentStatus = "Ödendi";
                    break;
                default:
                    $orderPaymentStatus = "Diğer";
            }

            $ordersConvertData[$key]['orderPaymentStatus'] = $orderPaymentStatus;

            $ordersConvertData[$key]['orderDeliveryAddressName'] = $order['siparisteslimatad'] . " " . $order['siparisteslimatsoyad'];
            $ordersConvertData[$key]['orderDeliveryAddressCountry'] = $location->getCountryNameById($order['siparisteslimatadresulke']);
            $ordersConvertData[$key]['orderDeliveryAddressCity'] = $location->getCityNameById($order['siparisteslimatadressehir']);
            $ordersConvertData[$key]['orderDeliveryAddressCounty'] = $location->getCountyNameById($order['siparisteslimatadresilce']);
            $ordersConvertData[$key]['orderDeliveryAddressArea'] = $location->getAreaNameById($order['siparisteslimatadressemt']);
            $ordersConvertData[$key]['orderDeliveryAddressNeighborhood'] = $location->getNeighborhoodNameById($order['siparisteslimatadresmahalle']);
            $ordersConvertData[$key]['orderDeliveryAddressPostalCode'] = $order['siparisteslimatadrespostakod'];
            $ordersConvertData[$key]['orderDeliveryAddressStreet'] = $order['siparisteslimatadresacik'];

            if(is_numeric($order['siparisteslimatadresulke'])){
                $ordersConvertData[$key]['orderDeliveryAddressCountryPhoneCode'] = $location->getCountryPhoneCode($order['siparisteslimatadresulke']);
            }
            else{
                $ordersConvertData[$key]['orderDeliveryAddressCountryPhoneCode'] = $location->getCountryPhoneCodeByCountryName($order['siparisteslimatadresulke']);
            }

            $ordersConvertData[$key]['orderInvoiceName'] = $order['siparisfaturaunvan'];
            $ordersConvertData[$key]['orderInvoiceTaxOffice'] = $order['siparisfaturavergidairesi'];
            $ordersConvertData[$key]['orderInvoiceTaxNumber'] = $order['siparisfaturavergino'];

            $ordersConvertData[$key]['orderInvoiceName'] = $order['siparisfaturaad'].' '. $order['siparisfaturasoyad'];
            $ordersConvertData[$key]['orderInvoiceEmail'] = $order['siparisfaturaeposta'];
            $ordersConvertData[$key]['orderInvoicePhone'] = $order['siparisfaturagsm'];
            $ordersConvertData[$key]['orderInvoiceAddressCountry'] = $location->getCountryNameById($order['siparisfaturaadresulke']);
            $ordersConvertData[$key]['orderInvoiceAddressCity'] = $location->getCityNameById($order['siparisfaturaadressehir']);
            $ordersConvertData[$key]['orderInvoiceAddressCounty'] = $location->getCountyNameById($order['siparisfaturaadresilce']);
            $ordersConvertData[$key]['orderInvoiceAddressArea'] = $location->getAreaNameById($order['siparisfaturaadressemt']);
            $ordersConvertData[$key]['orderInvoiceAddressNeighborhood'] = $location->getNeighborhoodNameById($order['siparisfaturaadresmahalle']);
            $ordersConvertData[$key]['orderInvoiceAddressPostalCode'] = $order['siparisfaturaadrespostakod'];
            $ordersConvertData[$key]['orderInvoiceAddressStreet'] = $order['siparisfaturaadresacik'];

            $orderProductIDs = explode(",", $order['siparisurunidler']);
            $orderProductNames = explode("||", $order['siparisurunadlar']);
            $orderProductStockCodes = (!empty($order['siparisurunstokkodlar'])) ? explode("||", $order['siparisurunstokkodlar']):[];
            $orderProductCategories = explode("||", $order['siparisurunkategoriler']);
            $orderProductPrices = explode("||", $order['siparisurunfiyatlar']);
            $orderProductQuantities = explode("||", $order['siparisurunadetler']);

            $orderProducts = [];

            foreach ($orderProductIDs as $i => $orderProductID){
                $productUnitName = $product->getProductUnitNameByProductID($orderProductID);
                $orderProducts[] = [
                    // beden renk malzeme için doğrulama yapalım olmayabilir
                    'productID' => $orderProductID,
                    'productName' => $orderProductNames[$i],
                    'productCategory' => $orderProductCategories[$i],
                    'productPrice' => $orderProductPrices[$i],
                    'productQuantity' => str_replace(".0000","",$orderProductQuantities[$i]),
                    'productUnitName' => $productUnitName,
                    'productImages' => $product->getProductImages($orderProductID)
                ];
                if(isset($orderProductStockCodes[$i])){
                    $orderProducts[$i]['productStockCode'] = $orderProductStockCodes[$i];
                }
            }
            $ordersConvertData[$key]['orderProducts'] = $orderProducts;
        }
    }

    $visitor['visitorIsMember']['memberOrders'] = $ordersConvertData;
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);
    header("Location: " . $memberLink."?orders");exit();
}
elseif ($action == "getOrderProducts"){
    $csrfToken = $requestData['csrf_token'] ?? null;

    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        $helper->jsonErrorResponse(_uye_ol_form_spam_yazi ." csrf getOrderProducts");
    }

    $orderUniqID = $requestData['orderUniqID'] ?? "null";
    if (is_null($orderUniqID)){
        $helper->jsonErrorResponse("Sipariş bulunamadı");
    }

    $order = $member->getOrderByOrderUniqID($orderUniqID);
    if(!$order){
        $helper->jsonErrorResponse("Sipariş Bulunamadı");
    }

    $order = $order[0];

    $config->includeClass('Product');
    $product = new Product($db,$json);

    $orderProductIDs = explode(",", $order['siparisurunidler']);
    $orderProductNames = explode("||", $order['siparisurunadlar']);
    $orderProductStockCodes = (!empty($order['siparisurunstokkodlar'])) ? explode("||", $order['siparisurunstokkodlar']):[];
    $orderProductCategories = explode("||", $order['siparisurunkategoriler']);
    $orderProductPrices = explode("||", $order['siparisurunfiyatlar']);
    $orderProductQuantities = explode("||", $order['siparisurunadetler']);

    $orderProducts = [];

    foreach ($orderProductIDs as $i => $orderProductID){
        //birim adı, metre, adet vs
        $productUnitName = $product->getProductUnitNameByProductID($orderProductID);

        $orderProducts[] = [
            // beden renk malzeme için doğrulama yapalım olmayabilir
            'productID' => $orderProductID,
            'productName' => $orderProductNames[$i],
            'productCategory' => $orderProductCategories[$i],
            'productPrice' => $orderProductPrices[$i],
            'productQuantity' => str_replace(".0000","",$orderProductQuantities[$i]),
            'productUnitName' => $productUnitName,
            'productImages' => $product->getProductImages($orderProductID)
        ];
        if(isset($orderProductStockCodes[$i])){
            $orderProducts[$i]['productStockCode'] = $orderProductStockCodes[$i];
        }
    }

    $helper->jsonSuccessResponse("ürün listesi alındı",$orderProducts);

}
elseif ($action == "message"){
    $messages = $member->getMessages($memberID);
    $visitor['visitorIsMember']['memberMessages'] = $messages;
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);
    header("Location: " . $memberLink."?message");exit();
}
elseif ($action == "favorite"){

    $memberUniqID = $visitor['visitorUniqID'];
    $favorites = $member->getFavorites($memberUniqID);
    $favoriteProducts = [];
    if(!empty($favorites)){
        $config->includeClass('Product');
        $favoriteProduct = new Product($db, $json);

        foreach ($favorites as $key => $favorite){
            $productUniqID = $favorite['productUniqID'];
            $product = $favoriteProduct->getProductByUniqID($productUniqID);
            $favoriteProducts[] = $product;
        }
    }
    $visitor['visitorIsMember']['memberFavorites'] = $favoriteProducts;
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);
    header("Location: " . $memberLink."?favorite"); exit();
}
elseif ($action == "cancellation-refund-exchange"){

    $cancellationRefundExchange = $member->getCancellationRefundExchangeRequest($memberID);
    $visitor['visitorIsMember']['memberCancellationRefundExchange'] = $cancellationRefundExchange;
    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);

    exit(header("Location: " . $memberLink."?cancellation-refund-exchange"));
}
elseif ($action == "addCancellationRefundExchange"){
    $csrfToken = $requestData['csrf_token'] ?? null;

    if(is_null($csrfToken) || !$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf Login"
        ]);
        exit();
    }

    if($memberID==0){
        $helper->jsonErrorResponse("Üye bulunamadı");
    }

    $orderUniqID = $requestData['orderUniqID'] ?? null;
    if(is_null($orderUniqID)){
        $helper->jsonErrorResponse("orderUniqID boş olamaz");
    }

    $products = $requestData['products'] ?? null;
    if(is_null($products)){
        $helper->jsonErrorResponse("En az bir ürün seçmelisiniz");
    }

    $products = implode(",", $products);

    $request = $requestData['request'] ?? null;
    $reason = $requestData['reason'] ?? null;
    if(is_null($reason) || is_null($request)){
        $helper->jsonErrorResponse("İstek tipi ve istek nedeni boş olamaz");
    }

    $description = $requestData['description'];

    $member->beginTransaction("iptal-iade-değişim");
    $result = $member->addCancellationRefundExchangeRequest([
            'uyeid' => $memberID,
            'siparisid' => $orderUniqID,
            'urunid' => $products,
            'degisimtur' => $request,
            'iadenedeni' => $reason,
            'iadeaciklama' => $description
        ]);

    if(!$result) {
        $member->rollback("iptal-iade-değişim");
        $helper->jsonErrorResponse("bir hata oluştu, lütfen daha sonra tekrar deneyin");
    }

    $member->commit("iptal-iade-değişim");
    $helper->jsonSuccessResponse("Talebiniz başarıyla alındı");
}
elseif ($action == "checkUser"){

    $email = $requestData['email'] ?? null;
    $telephone = $requestData['telephone'] ?? null;

    if($email != null){
        $email = $helper->encrypt($email, $config->key);

        // Üye bilgileri getirilir
        $result = $member->getMemberInfoByEmail($email);

        if (!$result) {
            echo json_encode([
                'status' => 'error',
                'message' => _uye_eposta_kayitli_degil,
                'memberData' => []
            ]);
            exit();
        }
        else {
            echo json_encode([
                'status' => 'success',
                'message' => _uye_eposta_kayitli,
                'memberData' => []
            ]);
            exit();
        }
    }

    if($telephone != null){
        $telephone = $helper->encrypt($telephone, $config->key);

        // Üye bilgileri getirilir
        $result = $member->getMemberInfoByTelephone($telephone);
        if(!empty($result)) {

            echo json_encode([
                'status' => 'error',
                'message' => _uye_telefon_kayitli,
                'memberData' => []
            ]);
            exit();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => _uye_telefon_kayitli_degil,
                'memberData' => []
            ]);
            exit();
        }
    }
}
elseif ($action=="passwordReset"){
    $email = $requestData['email'] ?? null;
    $token = $requestData['token'] ?? null;

    if($email != null && $token != null) {
        $email = $helper->encrypt($email, $config->key);
        $token = str_replace(" ","+", $token);
        $token = str_replace($email, "", $token);

        // Üye bilgileri getirilir
        $result = $member->getMemberInfoByEmail($email);

        if (!$result) {

            $session->addSession('popup', [
                'status' => 'error',
                'message' => 'Hatalı bağlantı',
                'position' => 'top-right',
                'width' => '300px',
                'height' => '100px',
                'closeButton' => true,
                'autoClose' => false,
                'animation' => true,
            ]);
            header("Location: " . $returnLink . "?actionErrorPasswordReset");
            exit();

        }
        else {

            $memberData = $result[0];

            $password = $memberData['uyesifre'];

            if($password!=$token){
                header("Location: /?emailResetTokenError");
            }

            $userUpdateTime = $memberData['uyeguncellemetarih'];
            //üzerinden 24 saat geçmiş i bakalım

            $userUpdateTime = strtotime($userUpdateTime);
            $currentTime = time();

            $timeDifference = $currentTime - $userUpdateTime;

            if($timeDifference > 86400){
                $session->addSession('popup', [
                    'status' => 'error',
                    'message' => 'Bağlantı zaman aşımına uğradı. Lütfen tekrar deneyiniz.',
                    'position' => 'top-right',
                    'width' => '300px',
                    'height' => '100px',
                    'closeButton' => true,
                    'autoClose' => false,
                    'animation' => true,
                ]);
                header("Location: /?emailResetTimeError");
            }

            $memberDataConvert = [
                'memberStatus' => true,
                'identificationNumber' => $helper->decrypt($memberData['uyetcno'], $config->key),
                'memberID' => $memberData['uyeid'],
                'memberUniqID' => $memberData['benzersizid'],
                'memberCreateDate' => $memberData['uyeolusturmatarih'],
                'memberUpdateDate' => $memberData['uyeguncellemetarih'],
                'memberType' => $memberData['uyetip'],
                'memberTitle' => $memberData['memberTitle'],
                'memberFirstName' => $helper->decrypt($memberData['uyead'], $config->key),
                'memberLastName' => $helper->decrypt($memberData['uyesoyad'], $config->key),
                'memberEmail' => $helper->decrypt($memberData['uyeeposta'], $config->key),
                'memberPhone' => $helper->decrypt($memberData['uyetelefon'], $config->key),
                'memberDescription' => $memberData['uyeaciklama'],
                'memberInvoiceName' => $helper->decrypt($memberData['uyefaturaad'], $config->key),
                'memberInvoiceTaxOffice' => $helper->decrypt($memberData['uyefaturavergidairesi'], $config->key),
                'memberInvoiceTaxNumber' => $helper->decrypt($memberData['uyefaturavergino'], $config->key),
                'memberActive' => $memberData['uyeaktif']
            ];

            $memberID = $memberData['uyeid'];

            $visitor['visitorIsMember'] = $memberDataConvert;

            $visitor['visitorEntryTime'] = date("Y-m-d H:i:s");

            // oturumdaki z iyaretçi bilgileri güncellenir
            $cookieVisitor = $visitor;

            $session = new Session($config->key, 3600, "/", $config->hostDomain, $config->cookieSecure, $config->cookieHttpOnly, $config->cookieSameSite);
            $session->addCookie('visitor', $cookieVisitor, 12);

            $casper->setVisitor($visitor);
            $session->updateSession('casper', $casper);

            $memberPassword = $helper->decrypt($memberData['uyesifre'], $config->key);
            $session->addSession('passwordReset',$memberPassword);

            $session->addSession('popup', [
                'status' => 'warning',
                'message' => 'Şifre değiştir formu ile şifrenizi değiştirebilirsiniz.',
                'position' => 'top-right',
                'width' => '300px',
                'height' => '100px',
                'closeButton' => true,
                'autoClose' => false,
                'animation' => true,
            ]);

            header("Location: " . $memberLink . "?profile");
            exit();

        }
    }
}
elseif ($action == "verificationCode") {
    $userId = $requestData['userId'] ?? null;
    $email = $requestData['email'] ?? null;

    if (empty($userId) || empty($email)) {

        $session->addSession('popup', [
            'status' => 'error',
            'message' => _uye_ol_form_eposta_dogrulama_hata,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '200px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);
        header("Location: /");exit();
    }

    $email = urldecode($email);
    $email = str_replace(" ","+", $email);
    $user = $member->verificationCode($email,$userId);
    if(!$user){
        $session->addSession('popup', [
            'status' => 'error',
            'message' => _uye_ol_form_eposta_dogrulama_hata,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '200px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);
        header("Location: /");exit();
    }

    $user = $user[0];
    $isActive = $user['uyeaktif'];
    if($isActive == 0){
        $member->beginTransaction();
        $result =$member->updateMemberStatus($userId,1);
        if($result<0){
            $member->rollback();
            $session->addSession('popup', [
                'status' => 'error',
                'message' => _uye_ol_form_eposta_dogrulama_hata,
                'position' => 'top-right',
                'width' => '300px',
                'height' => '200px',
                'closeButton' => true,
                'autoClose' => false,
                'animation' => true,
            ]);
            header("Location: /");exit();
        }
        else{$member->commit();}
    }

    $session->addSession('popup', [
        'status' => 'success',
        'message' => _uye_ol_eposta_dogrulama_tamam,
        'position' => 'top-right',
        'width' => '300px',
        'height' => '200px',
        'closeButton' => true,
        'autoClose' => false,
        'animation' => true,
    ]);
    header("Location: /");exit();

}
elseif ($action == "checkLastOrderByEmailAndPassword"){
    header("Content-Type: application/json; charset=utf-8");
    $email = $requestData["email"] ?? "";
    $password = $requestData["password"] ?? "";

    if(empty($email) || empty($password)){
        echo json_encode([
            'status'=>'error',
            'message'=>'E-posta ve şifre boş olamaz'
        ]);
        exit;
    }

    $encryptedEmail = $helper->encrypt($email,$config->key);
    $encryptedPassword = $helper->encrypt($password,$config->key);

    $memberResult = $member->login($encryptedEmail,$encryptedPassword);
    if(!$memberResult){
        echo json_encode([
            'status'=>'error',
            'message'=>'E-posta veya şifre hatalı'
        ]);
        exit;
    }

    $memberID = $memberResult["uyeid"];

    $lastOrder = $member->getMemberLastOrder($memberID);
    if(!$lastOrder){
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu hesaba ait sipariş bulunamadı'
        ]);exit;
    }

    $lastOrder = $lastOrder[0];
    $lastOrderStatus = $lastOrder['siparisdurum'];
    $lastOrderPaymentStatus = $lastOrder['siparisodemedurum'];
    $lastOrderDate = $lastOrder['siparistariholustur'];

    if($lastOrderPaymentStatus == 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Ödeme bekleniyor'
        ]);exit;
    }

    if($lastOrderStatus==4){
        $orderDate = strtotime($lastOrderDate);
        $currentDate = time();
        $dateDifference = $currentDate - $orderDate;

        if($dateDifference <= 31536000){ // 1 yıl = 365 gün * 24 saat * 60 dakika * 60 saniye
            echo json_encode([
                'status' => 'success',
                'message' => 'Giriş Başarılı',
                'expireTime' => date("Y-m-d H:i:s", strtotime("+1 year", $orderDate))
            ]);exit;
        }
        else{
            echo json_encode([
                'status'=>'error',
                'message'=>'Lisansınız tarihi itibariyle sona ermiştir'
            ]);exit;
        }
    }
    else{
        $lastOrderStatusDesc=$member->getOrderStatus($lastOrderStatus);
        if($lastOrderStatusDesc){
            echo json_encode([
                'status'=>'warning',
                'message'=>$lastOrderStatusDesc[0]['siparisdurumbaslik']
            ]);exit;
        }
    }

    echo json_encode([
        'status'=>'error',
        'message'=>'Bir hata oluştu, daha sonra tekrar deneyin'
    ]);exit;
}
else{
    header("Location: " . $returnLink."?actionErrorNoAction");exit();
}
