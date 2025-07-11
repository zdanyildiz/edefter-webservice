<?php
/**
 * @var Database $db
 * @var Helper $helper
 * @var Config $config
 * @var array $requestData
 * @var Json $json
 */

header("Content-Type: application/json; charset=utf-8");

$action = $requestData['action'] ?? null;
if(!isset($action)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Lütfen gerekli alanları doldurunuz.',
        'memberData' => []
    ]);
    exit();
}

require_once MODEL . 'Member.php';
$member = new Member($db);

require_once APP .'Webservice/MemberModel.php';
$user = new MemberModel($db);

function returnAndExit($status, $message, $memberData = []){
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'memberData' => $memberData
    ]);
    exit();
}

if ($action == "checkLastOrderByEmailAndPassword"){

    $email = $requestData["email"] ?? "";
    $password = $requestData["password"] ?? "";
    $computerID = $_GET['computerId'] ?? "";

    if(empty($email) || empty($password)){
        
        $status = 'error';
        $message ='E-posta ve şifre boş olamaz';
        returnAndExit($status,$message);
    }

    if(empty($computerID)){
        $status = 'error';
        $message ='Bilgisayar id boş olamaz';
        returnAndExit($status,$message);
    }

    $encryptedEmail = $helper->encrypt($email,$config->key);
    $encryptedPassword = $helper->encrypt($password,$config->key);

    $memberResult = $member->login($encryptedEmail,$encryptedPassword);
    if(!$memberResult){
        $status = 'error';
        $message ='E-posta veya şifre hatalı';
        returnAndExit($status,$message);
    }

    $memberResult=$memberResult[0];
    $isMemberActive = $memberResult["uyeaktif"];
    if($isMemberActive == 0){
        $status = 'error';
        $message ='Üyeliğiniz aktif değil, lütfen e-posta doğrulaması yapın veya https://e-defter.globalpozitif.com.tr adresinden destek alın';
        returnAndExit($status,$message);
    }

    $memberID = $memberResult["uyeid"];

    $lastOrder = $member->getMemberLastOrder($memberID);

    if(!$lastOrder){
        // Sipariş bulunamadı, deneme modu kontrol et
        $trialUser = $user->checkTrialUser($memberID);
        
        if (!$trialUser) {
            // Deneme kaydı yok, yeni deneme başlat
            $addTrialResult = $user->addTrialUser($memberID, 30); // 30 günlük deneme
            if (!$addTrialResult) {
                $status = 'error';
                $message = 'Deneme süresi başlatılamadı, lütfen tekrar deneyin';
                returnAndExit($status, $message);
            }
            
            // Yeni deneme kullanıcısı için session kontrolleri
            $userSessionLog = $user->checkSessionAttemptByUserID($memberID);
            if($userSessionLog){
                $userSessionCount = count($userSessionLog);
                if($userSessionCount >= 3){
                    $status = 'error';
                    $message ='Bu hesap birden çok cihazda kullanılıyor. \nGiriş yapamazsınız. \nKayıtlı adresinizden info@globalpozitif.com.tr ile iletişime geçin';
                    returnAndExit($status,$message);
                }
            }

            // Kullanıcı session kontrolleri
            $checkUser = $user->checkUserByUserId($memberID);
            $userSession = $user->checkUsers($memberID, $computerID);

            if($checkUser && !$userSession){
                $updateResult = $user->updateComputerId($memberID,$computerID);
                if(!$updateResult){
                    $status = 'error';
                    $message ='Bir hata oluştu, daha sonra tekrar deneyin';
                    returnAndExit($status,$message);
                }

                $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
                if(!$checkLogUser){
                    $addLogResult = $user->logSessionAttempt($memberID,$computerID);
                    if(!$addLogResult){
                        $status = 'error';
                        $message ='Bir hata oluştu, daha sonra tekrar deneyin';
                        returnAndExit($status,$message);
                    }
                }
                
                $status = 'error';
                $message ='Bu hesap başka bir cihazda kullanılıyor. Lütfen tekrar giriş yapın.';
                returnAndExit($status,$message);
            }
            elseif (!$checkUser && !$userSession) {
                $addUserResult = $user->addUsers($memberID, $computerID);
                if (!$addUserResult) {
                    $status = 'error';
                    $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
                    returnAndExit($status, $message);
                }

                $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
                if(!$checkLogUser) {
                    $addLogResult = $user->logSessionAttempt($memberID, $computerID);
                    if (!$addLogResult) {
                        $status = 'error';
                        $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
                        returnAndExit($status, $message);
                    }
                }
            }
            
            // Deneme süresi başarıyla başlatıldı
            $trialEndDate = date('Y-m-d H:i:s', strtotime('+30 days'));
            echo json_encode([
                'status' => 'success',
                'message' => 'Deneme süresi başlatıldı (30 gün)',
                'expireTime' => $trialEndDate,
                'keyCode' => $config->key,
                'isTrial' => true
            ]);
            exit;
        } else {
            // Deneme kaydı var, süre kontrolü yap
            $trialUser = $trialUser[0];
            $isExpired = $user->isTrialExpired($memberID);
            
            if ($isExpired) {
                $status = 'error';
                $message = 'Deneme süreniz sona ermiştir. Lütfen https://e-defter.globalpozitif.com.tr adresinden satın alın';
                returnAndExit($status, $message);
            }
            
            // Deneme süresi aktif, session kontrolleri
            $userSessionLog = $user->checkSessionAttemptByUserID($memberID);
            if($userSessionLog){
                $userSessionCount = count($userSessionLog);
                if($userSessionCount >= 3){
                    $status = 'error';
                    $message ='Bu hesap birden çok cihazda kullanılıyor. \nGiriş yapamazsınız. \nKayıtlı adresinizden info@globalpozitif.com.tr ile iletişime geçin';
                    returnAndExit($status,$message);
                }
            }

            $checkUser = $user->checkUserByUserId($memberID);
            $userSession = $user->checkUsers($memberID, $computerID);

            if($checkUser && !$userSession){
                $updateResult = $user->updateComputerId($memberID,$computerID);
                if(!$updateResult){
                    $status = 'error';
                    $message ='Bir hata oluştu, daha sonra tekrar deneyin';
                    returnAndExit($status,$message);
                }

                $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
                if(!$checkLogUser){
                    $addLogResult = $user->logSessionAttempt($memberID,$computerID);
                    if(!$addLogResult){
                        $status = 'error';
                        $message ='Bir hata oluştu, daha sonra tekrar deneyin';
                        returnAndExit($status,$message);
                    }
                }
                
                $status = 'error';
                $message ='Bu hesap başka bir cihazda kullanılıyor. Lütfen tekrar giriş yapın.';
                returnAndExit($status,$message);
            }
            elseif (!$checkUser && !$userSession) {
                $addUserResult = $user->addUsers($memberID, $computerID);
                if (!$addUserResult) {
                    $status = 'error';
                    $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
                    returnAndExit($status, $message);
                }

                $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
                if(!$checkLogUser) {
                    $addLogResult = $user->logSessionAttempt($memberID, $computerID);
                    if (!$addLogResult) {
                        $status = 'error';
                        $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
                        returnAndExit($status, $message);
                    }
                }
            }
            
            // Deneme süresi devam ediyor
            echo json_encode([
                'status' => 'success',
                'message' => 'Deneme süresi devam ediyor',
                'expireTime' => $trialUser['trial_end_date'],
                'keyCode' => $config->key,
                'isTrial' => true
            ]);
            exit;
        }
    }

    $lastOrder = $lastOrder[0];
    $lastOrderStatus = $lastOrder['siparisdurum'];
    $lastOrderPaymentStatus = $lastOrder['siparisodemedurum'];
    $lastOrderDate = $lastOrder['siparistariholustur'];

    if($lastOrderPaymentStatus == 0){
        $status = 'error';
        $message ='Ödeme bekleniyor';
        returnAndExit($status,$message);
    }

    if($lastOrderStatus==4){
        $orderDate = strtotime($lastOrderDate);
        $currentDate = time();
        $dateDifference = $currentDate - $orderDate;

        if($dateDifference <= 31536000){ // 1 yıl = 365 gün * 24 saat * 60 dakika * 60 saniye
            $userSessionLog = $user->checkSessionAttemptByUserID($memberID);
            if($userSessionLog){
                $userSessionCount = count($userSessionLog);
                if($userSessionCount >= 3){
                    $status = 'error';
                    $message ='Bu hesap birden çok cihazda kullanılıyor. \nGiriş yapamazsınız. \nKayıtlı adresinizden info@globalpozitif.com.tr ile iletişime geçin';
                    returnAndExit($status,$message);
                }
            }

            //kullanıcı daha önce giriş yapmış mı?
            $checkUser = $user->checkUserByUserId($memberID);

            //kullanıcının bilgisayar id'si kayıtlı mı?
            $userSession = $user->checkUsers($memberID, $computerID);

            //kullanıcı kaydı var fakat computer id eşleşmiyor
            if($checkUser && !$userSession){
                $updateResult = $user->updateComputerId($memberID,$computerID);
                if(!$updateResult){
                    $status = 'error';
                    $message ='Bir hata oluştu, daha sonra tekrar deneyin';
                    returnAndExit($status,$message);
                }

                $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
                if(!$checkLogUser){
                    $addLogResult = $user->logSessionAttempt($memberID,$computerID);
                    if(!$addLogResult){
                        $status = 'error';
                        $message ='Bir hata oluştu, daha sonra tekrar deneyin';
                        returnAndExit($status,$message);
                    }
                }
                
                $status = 'error';
                $message ='Bu hesap başka bir cihazda kullanılıyor. Lütfen tekrar giriş yapın.';
                returnAndExit($status,$message);
            }
            // Kullanıcı ve bilgisayar ID'si kayıtlı değilse, kaydet
            elseif (!$checkUser && !$userSession) {

                $addUserResult = $user->addUsers($memberID, $computerID);
                if (!$addUserResult) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Bir hata oluştu, daha sonra tekrar deneyin'
                    ]);
                    exit;
                }

                $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
                if(!$checkLogUser) {
                    $addLogResult = $user->logSessionAttempt($memberID, $computerID);
                    if (!$addLogResult) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Bir hata oluştu, daha sonra tekrar deneyin'
                        ]);
                        exit;
                    }
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Giriş Başarılı',
                'expireTime' => date("Y-m-d H:i:s", strtotime("+1 year", $orderDate)),
                'keyCode' => $config->key
            ]);
            exit;
        }
        else{
            $status = 'error';
            $message ='Lisansınız sona ermiştir';
            returnAndExit($status,$message);
        }
    }
    else{
        $lastOrderStatusDesc=$member->getOrderStatus($lastOrderStatus);
        if($lastOrderStatusDesc){
            $lastOrderStatusDesc=$lastOrderStatusDesc[0];
            $status = 'error';
            $message = $lastOrderStatusDesc['siparisdurumbaslik'];
            returnAndExit($status,$message);
        }
    }

    $status = 'error';
    $message ='Bir hata oluştu, daha sonra tekrar deneyin';
    returnAndExit($status,$message);

} //validateComputerId
elseif ($action == "validateComputerId"){
    $computerID = $requestData['computerId'] ?? "";
    $email = $requestData['email'] ?? "";

    if(empty($email)){
        $status = 'error';
        $message ='E-posta boş olamaz';
        returnAndExit($status,$message);
    }

    if(empty($computerID)){
        $status = 'error';
        $message ='Bilgisayar id boş olamaz';
        returnAndExit($status,$message);
    }

    $encryptedEmail = $helper->encrypt($email,$config->key);
    $memberResult = $member->getMemberInfoByEmail($encryptedEmail);


    if(!$memberResult){
        $status = 'error';
        $message ='Üyeliğiniz bulunamadı';
        returnAndExit($status,$message);
    }

    $memberResult = $memberResult[0];
    $memberID = $memberResult['uyeid'];

    $userSessionLog = $user->checkSessionAttemptByUserID($memberID);
    if($userSessionLog){
        $userSessionCount = count($userSessionLog);
        if($userSessionCount >= 3){
            $status = 'error';
            $message = 'Bu hesap birden çok cihazda kullanılıyor. \nGiriş yapamazsınız. \nKayıtlı adresinizden info@globalpozitif.com.tr ile iletişime geçin';
            returnAndExit($status,$message);
        }
    }

    $checkUser = $user->checkUserByUserId($memberID);
    if(!$checkUser){
        $addUserResult = $user->addUsers($memberID,$computerID);
        if(!$addUserResult){
            $status = 'error';
            $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
            returnAndExit($status,$message);
        }

        $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
        if(!$checkLogUser) {
            $addLogResult = $user->logSessionAttempt($memberID, $computerID);
            if (!$addLogResult) {
                $status = 'error';
                $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
                returnAndExit($status,$message);
            }
        }

        $status = 'success';
        $message = 'Doğrulama Başarılı';
        returnAndExit($status,$message);
    }

    $userSession = $user->checkUsers($memberID, $computerID);

    if (!$userSession) {
        $updateResult = $user->updateComputerId($memberID, $computerID);
        if (!$updateResult) {
            $status = 'error';
            $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
            returnAndExit($status,$message);
        }

        $checkLogUser = $user->checkSessionAttempt($memberID,$computerID);
        if(!$checkLogUser) {
            $addLogResult = $user->logSessionAttempt($memberID, $computerID);
            if (!$addLogResult) {
                $status = 'error';
                $message = 'Bir hata oluştu, daha sonra tekrar deneyin';
                returnAndExit($status,$message);
            }
        }

        $status = 'error';
        $message = 'Bu hesap başka bir cihazda kullanılıyor. Lütfen tekrar giriş yapın.';
        returnAndExit($status,$message);
    }
    else{
        $status = 'success';
        $message = 'Doğrulama Başarılı';
        returnAndExit($status,$message);
    }
}