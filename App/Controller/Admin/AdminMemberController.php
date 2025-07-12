<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */



$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

$languageID = $requestData["languageID"] ?? 1;

$referrer = $requestData["referrer"] ?? null;

$adminCasper = $adminSession->getAdminCasper();

$config = $adminCasper->getConfig();

$json = $config->Json;

$helper = $config->Helper;

include_once MODEL . 'Admin/AdminMember.php';
$adminMember = new AdminMember($db);

include_once MODEL . 'Admin/AdminLocation.php';
$location = new AdminLocation($db);

if($action == "addMember" || $action == "updateMember") {

    $memberData = [
        'memberUpdateDate' => date("Y-m-d H:i:s"),
        'memberType' => $requestData["memberType"] ?? 1,
        'memberIdentityNo' => $helper->encrypt($requestData["memberIdentityNo"], $config->key),
        'memberName' => $helper->encrypt($requestData["memberName"], $config->key),
        'memberSurname' => $helper->encrypt($requestData["memberSurname"], $config->key),
        'memberEmail' => $helper->encrypt($requestData["memberEmail"], $config->key),
        'memberPassword' => $helper->encrypt($requestData["memberPassword"], $config->key),
        'memberPhone' => $helper->encrypt($requestData["memberPhone"], $config->key),
        'memberDescription' => $requestData["memberDescription"],
        'memberInvoiceName' => $helper->encrypt($requestData["memberInvoiceName"], $config->key),
        'memberInvoiceTaxOffice' => $helper->encrypt($requestData["memberInvoiceTaxOffice"], $config->key),
        'memberInvoiceTaxNumber' => $helper->encrypt($requestData["memberInvoiceTaxNumber"], $config->key),
        'memberActive' => $requestData["memberActive"] ?? 1,
        'memberDeleted' => $requestData["memberDeleted"] ?? 0
    ];

    $adminMember->beginTransaction($action);
    $memberID = $requestData["memberID"] ?? 0;
    if ($action == "updateMember" && $memberID == 0) {
        $adminMember->commit();
        echo json_encode([
            'status' => 'error',
            'message' => 'Member ID is required'
        ]);
        exit();
    }
    elseif ($action == "updateMember" && $memberID > 0) {
        $memberData["memberID"] = $memberID;
        $memberResult = $adminMember->updateMember($memberData);
    }
    elseif ($action == "addMember"){
        //uye telefon ve eposta daha önce kayıtlı mı kontrol edelim
        $memberPhone = $requestData["memberPhone"] ?? null;
        $memberEmail = $requestData["memberEmail"] ?? null;
        if($memberPhone == "" && $memberEmail == ""){
            echo json_encode([
                'status' => 'error',
                'message' => 'Telefon ve E-Posta alanlarından en az biri dolu olmalıdır'
            ]);
            exit();
        }

        $memberCheck = $adminMember->getMemberByEmailOrPhone($memberEmail, $memberPhone);
        if(!empty($memberCheck)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Bu telefon veya e-posta zaten kayıtlı'
            ]);
            exit();
        }


        $memberData["memberCreateDate"] = date("Y-m-d H:i:s");
        $memberData["memberUniqID"] = $helper->generateUniqID();
        //print_r($memberData);exit;

        $memberResult = $adminMember->addMember($memberData);
        $memberID = $memberResult;
    }

    if($memberResult>0){
        $adminMember->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Member added successfully',
            'memberID' => $memberID
        ]);
        exit;
    }

    $adminMember->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Üye kaydetme başarısız'
    ]);
    exit;

}
elseif($action == "deleteMember"){
    $memberID = $requestData["memberID"] ?? null;
    if(empty($memberID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Member ID is required'
        ]);
        exit();
    }
    $adminMember->beginTransaction();
    $memberResult = $adminMember->deleteMember($memberID);
    if($memberResult>0){
        $adminMember->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Üye başarıyla silindi'
        ]);
        exit;
    }
    $adminMember->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Üye silme başarısız'
    ]);
    exit;
}
elseif($action == "memberSearch"){

    $searchText = $requestData["searchText"] ?? null;

    if(!isset($searchText)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text error'
        ]);
        exit();
    }

    $encryptSearchText = $helper->encrypt($searchText, $config->key);

    $params = [
        'searchText' => $searchText,
        'encryptSearchText' => $encryptSearchText
    ];

    $result = $adminMember->memberSearch($params);

    if($result['status'] == 'success'){

        $memberList = $result['memberList'];

        $memberList = array_map(function($member) use ($helper, $config){

            return [
                'memberID' => $member['uyeid'],
                'memberUniqID' => $member['benzersizid'],
                'memberCreateDate' => $member['uyeolusturmatarih'],
                'memberUpdateDate' => $member['uyeguncellemetarih'],
                'memberType' => $member['uyetip'],
                'memberIdentityNo' => !empty($member['uyetcno']) ? $member['uyetcno'] : '',
                'memberNameSurname' => $member['uyeadsoyad'],
                'memberName' => !empty($member['uyead']) ? $helper->decrypt($member['uyead'], $config->key) : '',
                'memberSurname' => !empty($member['uyesoyad']) ? $helper->decrypt($member['uyesoyad'], $config->key) : '',
                'memberEmail' => !empty($member['uyeeposta']) ? $helper->decrypt($member['uyeeposta'], $config->key) : '',
                'memberPassword' => $member['uyesifre'],
                'memberPhone' => !empty($member['uyetelefon']) ? $helper->decrypt($member['uyetelefon'], $config->key) : '',
                'memberDescription' => $member['uyeaciklama'],
                'memberInvoiceName' => $member['uyefaturaad'],
                'memberInvoiceTaxOffice' => $member['uyefaturavergidairesi'],
                'memberInvoiceTaxNumber' => $member['uyefaturavergino'],
                'isActive' => $member['uyeaktif'],
                'isDeleted' => $member['uyesil']
            ];
        }, $memberList);

        echo json_encode([
            'status' => 'success',
            'memberList' => $memberList
        ]);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'No member found'
        ]);
    }
    exit();
}
elseif($action == "getMemberAddressForOrder"){
    $memberID = $requestData["memberID"] ?? null;
    $addressID = $requestData["addressID"] ?? null;

    if(!isset($memberID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Member ID error'
        ]);
        exit();
    }

    if(!isset($addressID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Address ID error'
        ]);
        exit();
    }

    $result = $adminMember->getAddressByID($memberID, $addressID);

    if(!empty($result)){

        $countryID = $result['adresulke'];

        include_once MODEL . "Location.php";
        $location = new Location($db);

        $countryName = $location->getCountryNameById($countryID);

        if($countryID == 212){
            //türkiye seçilmiş, şehir, ilçe, semt ve mahalle isimlerini idlere göre çek
            $cityID = $result['adressehir'];
            $districtID = $result['adresilce'];
            $areaID = $result['adressemt'];
            $neighborhoodID = $result['adresmahalle'];


            $cityName = $location->getCityNameById($cityID);
            $districtName = $location->getCountyNameById($districtID);
            $areaName = $location->getAreaNameById($areaID);
            $neighborhoodName = $location->getNeighborhoodNameById($neighborhoodID);
        }
        else{
            $cityName = $result['adressehir'];
            $districtName = $result['adresilce'];
            $areaName = $result['adressemt'];
            $neighborhoodName = $result['adresmahalle'];

            $cityID = 0;
            $districtID = 0;
            $areaID = 0;
            $neighborhoodID = 0;
        }

        $address = $result['adresacik'];
        $postalCode = $result['postakod'];

        echo json_encode([
            'status' => 'success',
            'country' => [
                'id' => $countryID,
                'name' => $countryName
            ],
            'city' => [
                'id' => $cityID,
                'name' => $cityName
            ],
            'district' => [
                'id' => $districtID,
                'name' => $districtName
            ],
            'area' => [
                'id' => $areaID,
                'name' => $areaName
            ],
            'neighborhood' => [
                'id' => $neighborhoodID,
                'name' => $neighborhoodName
            ],
            'address' => $address,
            'postalCode' => $postalCode
        ]);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'No address found'
        ]);
    }
}
elseif($action == "memberAddressList"){

    $memberID = $requestData["memberID"] ?? null;

    if(!isset($memberID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Member ID error'
        ]);
        exit();
    }

    $result = $adminMember->getAddresses($memberID);

    if(!empty($result)){
        $result = array_map(function($address){
            return [
                'addressTitle' => $address['adresbaslik'], // 'Ev', 'İş', 'Diğer
                'addressID' => $address['adresid'],
                'addressName' => $address['adresad'],
                'addressCountry' => $address['adresulke'],
                'addressCity' => $address['adressehir'],
                'addressDistrict' => $address['adresilce'],
                'addressArea' => $address['adressemt'],
                'addressNeighborhood' => $address['adresmahalle'],
                'address' => $address['adresacik'],
                'postalCode' => $address['postakod']
            ];
        }, $result);

        echo json_encode([
            'status' => 'success',
            'addressList' => $result
        ]);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'No address found'
        ]);
    }
}
elseif($action == "checkMemberNameAndSurname"){

    $members = $adminMember->getMembers();

    if(!empty($members)){
        //adı ya da soyadı boş olan üyeleri seçelim
        foreach($members as $member){
            $memberID = $member['uyeid'];
            $memberName = $member['uyead'];
            $memberSurname = $member['uyesoyad'];
            $memberNameSurname = $member['uyeadsoyad'];
            $memberNameSurname = trim($memberNameSurname);

            if(empty($memberName) || empty($memberSurname)) {
                // $memberNameSurname boşluklara ayıralım son boşluk sonrasını soyad kabul edelim
                $memberNameSurnameArray = explode(" ", $memberNameSurname);

                // Son elemanı soyad olarak al
                $memberSurname = array_pop($memberNameSurnameArray);

                // Geri kalan kısmı isim olarak al
                $memberName = implode(" ", $memberNameSurnameArray);

                echo "Member ID: $memberID, Name: $memberName, Surname: $memberSurname<br>";

                $encryptMemberName = $helper->encrypt($memberName, $config->key);
                $encryptMemberSurname = $helper->encrypt($memberSurname, $config->key);

                $result = $adminMember->updateMemberNameAndSurname($memberID,$encryptMemberName,$encryptMemberSurname);

                if($result['status']== 'success'){
                    echo "Member ID: $memberID, Name: $memberName, Surname: $memberSurname updated<br>";
                }
                else {
                    echo "<br>Member ID: $memberID, Name: $memberName, Surname: $memberSurname not updated<br>";
                }
            }
        }
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'No member found'
        ]);
    }
}
elseif($action == "addAddress" || $action == "updateAddress") {

    $memberID = $requestData["addressMemberID"] ?? null;
    if (empty($memberID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Member ID alanı boş olamaz'
        ]);
        exit();
    }
    $addressID = $requestData["addressID"] ?? null;
    $addressTitle = $requestData["addressTitle"] ?? null;
    $addressContactName = $requestData["addressContactName"] ?? null;
    $addressContactSurname = $requestData["addressContactSurname"] ?? null;
    $addressContactIdentityNumber = $requestData["addressContactIdentityNumber"] ?? null;
    $addressContactPhone = $requestData["addressContactPhone"] ?? null;

    $addressDeliveryCountryID = $requestData["addressDeliveryCountryID"] ?? null;
    $addressDeliveryCityID = $requestData["addressDeliveryCityID"] ?? null;
    $addressDeliveryDistrictID = $requestData["addressDeliveryDistrictID"] ?? null;

    $addressDeliveryAreaID = $requestData["addressDeliveryAreaID"] ?? null;
    $addressDeliveryNeighborhoodID = $requestData["addressDeliveryNeighborhoodID"] ?? null;

    $addressDeliveryPostalCode = $requestData["addressDeliveryPostalCode"] ?? null;
    $addressDeliveryStreet = $requestData["addressDeliveryStreet"] ?? null;

    //başlık, ad soyad, tc no, telefon, açık adres kontrolü
    if (empty($addressTitle) || empty($addressContactName) || empty($addressContactSurname) || empty($addressContactIdentityNumber) || empty($addressContactPhone) || empty($addressDeliveryStreet)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Alanlar boş olamaz'
        ]);
        exit();
    }

    //ülke ve şehir kontrolü
    if ($addressDeliveryCountryID == 212) {
        if (empty($addressDeliveryCityID) || empty($addressDeliveryDistrictID) || empty($addressDeliveryAreaID) || empty($addressDeliveryNeighborhoodID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Türkiye için alanlar boş olamaz'
            ]);
            exit();
        }
    } else {
        if (empty($addressDeliveryCityID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Şehir alanı boş olamaz'
            ]);
            exit();
        }
    }

    // Adres ekleme işlemi yapılırken üst yapı değerleri eklenir

    $member = $adminMember->getMemberInfo($memberID);
    if (empty($member)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Üye ID alanı geçersiz'
        ]);
        exit();
    }

    $adminMember->beginTransaction();

    $addressDeliveryCountryCode = $location->getCountryPhoneCode($addressDeliveryCountryID);

    $addressContactIdentityNumber = $helper->encrypt($addressContactIdentityNumber, $config->key);
    $addressContactName = $helper->encrypt($addressContactName, $config->key);
    $addressContactSurname = $helper->encrypt($addressContactSurname, $config->key);
    $addressContactPhone = $helper->encrypt($addressContactPhone, $config->key);
    $addressDeliveryStreet = $helper->encrypt($addressDeliveryStreet, $config->key);

    $addressData = [
        'memberID' => $memberID,
        'addressTitle' => $addressTitle,
        'addressContactName' => $addressContactName,
        'addressContactSurname' => $addressContactSurname,
        'addressContactIdentityNumber' => $addressContactIdentityNumber,
        'addressContactPhone' => $addressContactPhone,
        'addressDeliveryCountryID' => $addressDeliveryCountryID,
        'addressDeliveryCityID' => $addressDeliveryCityID,
        'addressDeliveryDistrictID' => $addressDeliveryDistrictID,
        'addressDeliveryAreaID' => $addressDeliveryAreaID,
        'addressDeliveryNeighborhoodID' => $addressDeliveryNeighborhoodID,
        'addressDeliveryPostalCode' => $addressDeliveryPostalCode,
        'addressDeliveryCountryCode' => $addressDeliveryCountryCode,
        'addressDeliveryStreet' => $addressDeliveryStreet,
        'addressDeleted' => 0
    ];

    if ($action == "addAddress") {
        $addressID = $adminMember->addAddress($addressData);
        $adminMember->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Adres başarıyla eklendi',
            'addressID' => $addressID
        ]);
        exit();
    }
    elseif ($action == "updateAddress") {
        $addressData["addressID"] = $addressID;
        $adminMember->updateAddress($addressData);
        $adminMember->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Adres başarıyla güncellendi'
        ]);
        exit();
    }

}
elseif($action == "updateMemberPassword"){
    $memberID = $requestData["memberID"] ?? null;
    if(empty($memberID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Member ID alanı boş olamaz'
        ]);
        exit();
    }
    $adminMember->beginTransaction();
    $memberData = $adminMember->getMemberInfo($memberID);
    if(!empty($memberData)){
        $memberName = $memberData['memberName'];
        $memberName = $helper->decrypt($memberName, $config->key);
        $memberSurname = $memberData['memberSurname'];
        $memberSurname = $helper->decrypt($memberSurname, $config->key);
        $memberEmail = $memberData['memberEmail'];
        $memberEmailDecrypt = $helper->decrypt($memberEmail, $config->key);
        $memberID = $memberData['memberID'];
        $memberPassword = $helper->createPassword(5,2);
        $memberPasswordEncrypt = $helper->encrypt($memberPassword, $config->key);


        $updatePasswordResult = $adminMember->updateMemberPassword($memberID, $memberPasswordEncrypt);
        if($updatePasswordResult>0){

            //Email gönderme işlemi
            include_once Helpers. 'EmailSender.php';
            $emailSender = new EmailSender();

            $emailSubject = $config->hostDomain.' Sitesi Giriş Şifreniz';

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

            $reminderToken = $memberEmail . $memberPasswordEncrypt;
            $reminderLink  = $config->http.$config->hostDomain."/?/control/member/get/passwordReset&email=".$memberEmailDecrypt."&token=".$reminderToken;


            $emailTemplate = file_get_contents(Helpers.'mail-template/passwordResetDoneTR.php');
            $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
            $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
            $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
            $emailTemplate = str_replace("[password-reset-link]", $reminderLink, $emailTemplate);
            $emailTemplate = str_replace("[password]", $memberPassword, $emailTemplate);
            $emailTemplate = str_replace("[member-name-surname]", $memberName." ".$memberSurname, $emailTemplate);
            $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
            $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
            $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);

            $sendMail = $emailSender->sendEmail($memberEmailDecrypt, $memberName." ".$memberSurname, $emailSubject, $emailTemplate);
            if($sendMail){
                $adminMember->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Şifre başarıyla güncellendi. Üyeye bildirim e-postası gönderildi.'
                ]);
                exit();
            }
            else{
                $adminMember->rollback();
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Şifre güncellenemedi'
                ]);
                exit();
            }

        }
        else{
            $adminMember->rollback();
            echo json_encode([
                'status' => 'error',
                'message' => 'Şifre güncellenemedi'
            ]);
            exit();
        }
    }
}
elseif($action == "deleteAddress"){
    $addressID = $requestData["addressID"] ?? null;
    $memberID = $requestData["memberID"] ?? null;
    
    if(empty($addressID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Adres ID alanı boş olamaz'
        ]);
        exit();
    }
    
    if(empty($memberID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Üye ID alanı boş olamaz'
        ]);
        exit();
    }
    
    // Adresin bu üyeye ait olup olmadığını kontrol et
    $address = $adminMember->getAddressByID($memberID, $addressID);
    if(empty($address)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Adres bulunamadı veya bu üyeye ait değil'
        ]);
        exit();
    }
    
    $adminMember->beginTransaction();
    
    $addressInfo = [
        'addressID' => $addressID,
        'memberID' => $memberID
    ];
    
    $deleteResult = $adminMember->deleteAddress($addressInfo);
    
    if($deleteResult > 0){
        $adminMember->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Adres başarıyla silindi'
        ]);
        exit();
    }
    else{
        $adminMember->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Adres silinirken bir hata oluştu'
        ]);
        exit();
    }
}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}
