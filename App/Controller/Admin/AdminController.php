<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var AdminSession $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */

$action = $requestData['action'] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lütfen gerekli alanları doldurunuz.',
    ]);
    exit();
}

if($action == 'loginWithEmailOrPhone'){

    $email = $requestData['email'] ?? null;
    $phone = $requestData['phone'] ?? null;
    $captcha = $requestData['captcha'] ?? null;
    $sessionCaptcha = $adminSession->getSession('loginCaptcha')['code'];


    if(empty($captcha) || empty($sessionCaptcha) || $captcha != $sessionCaptcha){
        echo json_encode([
            'status' => 'error',
            'message' => 'Güvenlik kodu hatalı.'
        ]);
        exit();
    }
    if(!empty($email)){
        $adminEmailOrPhone = $helper->encrypt($email, $config->key);
    }
    else if(!empty($phone)){
        $adminEmailOrPhone = $helper->encrypt($phone, $config->key);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
        ]);
        exit();
    }

    include_once MODEL . 'Admin/Admin.php';
    $adminModel = new Admin($db);

    $checkAdmin = $adminModel->checkAdmin($adminEmailOrPhone);

    if($checkAdmin['status']=='error'){
        echo json_encode([
            "status" => "error",
            "message" => $checkAdmin['message']
        ]);
        exit();
    }
    if(!empty($email)) {
        //Admin için 5 haneli sadece rakamlardan oluşan şifre oluşturup veritabanına kaydedelim
        $admin = $checkAdmin['admin'];
        $password = rand(10000, 99999);
        $adminID = $admin['yoneticiid'];

        $adminData = [
            'yoneticisifre' => $password,
            'yoneticisifretarih' => date('Y-m-d H:i:s')
        ];

        $updateAdmin = $adminModel->updatePassword($adminID, $adminData);

        if(!$updateAdmin){
            echo json_encode([
                'status' => 'error',
                'message' => 'Şifre oluşturulurken bir hata oluştu.'
            ]);
            exit();
        }

        //Email gönderme işlemi
        include_once Helpers. 'EmailSender.php';
        $emailSender = new EmailSender();

        $emailSubject = $config->hostDomain.' Panel Giriş Şifreniz';

        $languageID = 1; //@todo: dil seçimi yapılacak

        $siteConfig = $adminCasper->getSiteConfig();
        if(empty($siteConfig)){
            include_once MODEL . "Admin/AdminSiteConfig.php";
            $siteConfig = new AdminSiteConfig($db,$languageID);
            $siteConfig = $siteConfig->getSiteConfig();
            $adminCasper->setSiteConfig($siteConfig);
            $adminSession->updateSession("adminCasper",$adminCasper);
        }
        $siteConfig = $adminCasper->getSiteConfig();

        $logoInfo = $siteConfig['logoSettings'];
        $logo = isset($logoInfo['resim_url']) ? $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'] : $config->http.$config->hostDomain.'/_y/assets/img/header.jpg';;

        $companyInfo = $siteConfig['companySettings'] ?? [];

        if(!empty($companyInfo))
        {
            $companyName = $companyInfo['ayarfirmakisaad'];
            $companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
            $companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
            $companyEmail = $companyInfo['ayarfirmaeposta'];
        }
        else{
            $companyName = $config->hostDomain;
            $companyAddress = '';
            $companyPhone = '';
            $companyEmail = '';
        }


        $emailTemplate = file_get_contents(Helpers.'mail-template/adminSendPassword.php');
        $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
        $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
        $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
        $emailTemplate = str_replace("[password]", $password, $emailTemplate);
        $emailTemplate = str_replace("[admin-name-surname]", $helper->decrypt($admin['yoneticiadsoyad'],$config->key), $emailTemplate);
        $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
        $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
        $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);

        $sendMail = $emailSender->sendEmail($email, $helper->decrypt($admin['yoneticiadsoyad'],$config->key), $emailSubject, $emailTemplate);
        if($sendMail){
            echo json_encode([
                'status' => 'success',
                'message' => 'Şifreniz e-posta adresinize gönderildi.<br>5 dakika içinde şifrenizi girmeniz gerekmektedir'
            ]);
            exit();
        }
        else{
            echo json_encode([
                'status' => 'error',
                'message' => 'Şifreniz e-posta adresinize gönderilemedi.'
            ]);
            exit();
        }
    }
    else if(!empty($phone)){
        //SMS gönderme işlemi
    }
}
elseif($action == 'loginWithEmailOrPhoneAndPassword'){

    $email = $requestData['email'] ?? null;
    $phone = $requestData['phone'] ?? null;
    $password = $requestData['password'] ?? null;
    $rememberMe = $requestData['rememberMe'] ?? 0;

    //Log::adminWrite("Giriş işlemi: $email - $phone - $password - $rememberMe","special");

    if(!empty($email)){
        $adminEmailOrPhone = $helper->encrypt($email, $config->key);
    }
    else if(!empty($phone)){
        $adminEmailOrPhone = $helper->encrypt($phone, $config->key);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
        ]);
        exit();
    }

    if(empty($password)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
        ]);
        exit();
    }

    include_once MODEL . 'Admin/Admin.php';
    $adminModel = new Admin($db);

    $adminResult = $adminModel->login($adminEmailOrPhone, $password);

    if($adminResult['status']=='error'){
        echo json_encode([
            'status' => 'error',
            'message' => $adminResult['message']
        ]);
        exit();
    }
    else{
        $adminSession->removeSession('loginCaptcha');
        $admin = $adminResult['admin'];
        $admin['yoneticiadsoyad'] = $helper->decrypt($admin['yoneticiadsoyad'],$config->key);
        $admin['yoneticieposta'] = $helper->decrypt($admin['yoneticieposta'],$config->key);
        $admin['yoneticiceptelefon'] = $helper->decrypt($admin['yoneticiceptelefon'],$config->key);
        $admin['lockedStatus'] = false;

        $adminCasper->setLoginStatus(true);
        $adminCasper->setAdmin($admin);
        $adminSession->updateSession("adminCasper",$adminCasper);

        if($rememberMe==1){
            $adminSession->addCookie("adminCasper",$admin,1);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Giriş başarılı.'
        ]);
        exit();
    }
}
elseif($action == 'loginWithPIN'){

    $adminID = $requestData['adminID'] ?? null;
    $pin = $requestData['adminPin'] ?? null;
    $refUrl = $requestData['refUrl'] ?? null;

    if(empty($adminID) || empty($pin)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
        ]);
        exit();
    }

    include_once MODEL . 'Admin/Admin.php';
    $adminModel = new Admin($db);

    $admin = $adminModel->getAdminWithPIN($adminID, $pin);

    if(empty($admin)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Geçersiz işlem.',
        ]);
        exit();
    }

    $admin['yoneticiadsoyad'] = $helper->decrypt($admin['yoneticiadsoyad'],$config->key);
    $admin['yoneticieposta'] = $helper->decrypt($admin['yoneticieposta'],$config->key);
    $admin['yoneticiceptelefon'] = $helper->decrypt($admin['yoneticiceptelefon'],$config->key);
    $admin['lockedStatus'] = false;

    $adminCasper->setLoginStatus(true);
    $adminCasper->setAdmin($admin);
    $adminSession->updateSession("adminCasper",$adminCasper);

    echo json_encode([
        'status' => 'success',
        'message' => 'Giriş başarılı.',
        'admin' => $admin,
        'refUrl' => $refUrl
    ]);
    exit();

}
elseif($action == "addAdmin"){

    $adminNameSurname = $requestData['adminNameSurname'] ?? null;
    $adminEmail = $requestData['adminEmail'] ?? null;
    $adminPhone = $requestData['adminPhone'] ?? null;
    $adminAuth = $requestData['adminAuth'] ?? null;
    $adminPIN = $requestData['adminPIN'] ?? null;

    if(empty($adminNameSurname) || empty($adminEmail) || empty($adminPhone) || empty($adminPIN) ){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
        ]);
        exit();
    }

    include_once MODEL . 'Admin/Admin.php';
    $adminModel = new Admin($db);

    $adminNameSurname = $helper->encrypt($adminNameSurname, $config->key);
    $adminEmail = $helper->encrypt($adminEmail, $config->key);
    $adminPhone = $helper->encrypt($adminPhone, $config->key);

    $adminKey = $helper->createPassword(20,2);

    $adminImage = $requestData['adminImage'] ?? null;
    $adminPassword = $helper->createPassword(5,2);

    $adminActive = $requestData['adminActive'] ?? 0;

    $adminData = [
        'adminKey' => $adminKey,
        'createDate' => date('Y-m-d H:i:s'),
        'updateDate' => date('Y-m-d H:i:s'),
        'adminAuth' => $adminAuth,
        'adminNameSurname' => $adminNameSurname,
        'adminEmail' => $adminEmail,
        'adminPhone' => $adminPhone,
        'adminImage' => $adminImage,
        'adminPassword' => $adminPassword,
        'adminPasswordDate' => date('Y-m-d H:i:s'),
        'adminPIN' => $adminPIN,
        'adminActive' => $adminActive,
        'adminDeleted' => 0
    ];

    $addAdmin = $adminModel->addAdmin($adminData);

    if($addAdmin){
        echo json_encode([
            'status' => 'success',
            'message' => 'Yönetici eklendi.'
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Yönetici eklenirken bir hata oluştu.'
        ]);
        exit();
    }
}
elseif($action == "updateAdmin"){
    $adminID = $requestData['adminID'] ?? null;
    $adminNameSurname = $requestData['adminNameSurname'] ?? null;
    $adminEmail = $requestData['adminEmail'] ?? null;
    $adminPhone = $requestData['adminPhone'] ?? null;
    $adminAuth = $requestData['adminAuth'] ?? null;
    $adminPIN = $requestData['adminPIN'] ?? null;

    if(empty($adminID) || empty($adminNameSurname) || empty($adminEmail) || empty($adminPhone) || empty($adminPIN) ){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
        ]);
        exit();
    }

    include_once MODEL . 'Admin/Admin.php';
    $adminModel = new Admin($db);

    $adminNameSurname = $helper->encrypt($adminNameSurname, $config->key);
    $adminEmail = $helper->encrypt($adminEmail, $config->key);
    $adminPhone = $helper->encrypt($adminPhone, $config->key);

    $adminImage = $requestData['adminImage'] ?? null;
    $adminActive = $requestData['adminActive'] ?? 0;

    $adminData = [
        'updateDate' => date('Y-m-d H:i:s'),
        'adminAuth' => $adminAuth,
        'adminNameSurname' => $adminNameSurname,
        'adminEmail' => $adminEmail,
        'adminPhone' => $adminPhone,
        'adminImage' => $adminImage,
        'adminPIN' => $adminPIN,
        'adminActive' => $adminActive,
        'adminID' => $adminID
    ];

    $updateAdmin = $adminModel->updateAdmin($adminData);

    if($updateAdmin){
        echo json_encode([
            'status' => 'success',
            'message' => 'Yönetici güncellendi.'
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Yönetici güncellenirken bir hata oluştu.'
        ]);
        exit();
    }

}
elseif($action == "deleteAdmin"){
    $adminID = $requestData['adminID'] ?? null;

    if(empty($adminID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
        ]);
        exit();
    }

    include_once MODEL . 'Admin/Admin.php';
    $adminModel = new Admin($db);

    $deleteAdmin = $adminModel->deleteAdmin($adminID);

    if($deleteAdmin){
        echo json_encode([
            'status' => 'success',
            'message' => 'Yönetici silindi.'
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Yönetici silinirken bir hata oluştu.'
        ]);
        exit();
    }

}
elseif($action == "logOut"){

    $adminSession->deleteCookie("adminCasper");
    $adminSession->deleteAdminCasper();
    echo json_encode([
        'status' => 'success',
        'message' => 'Çıkış yapıldı.'
    ]);
    exit(header('Location: /_y/'));

}
else{
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz işlem.',
    ]);
    exit();
}