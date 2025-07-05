<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */
/**
 * @var adminSession $adminSession
 * @var AdminDatabase $db
 * @var Router $router
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

include_once MODEL."Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminSiteSettings.php';
$adminSiteSettings = new AdminSiteSettings($db, $languageID);

include_once MODEL . 'Admin/AdminSiteConfig.php';
$adminSiteConfig = new AdminSiteConfig($db, $languageID);

include_once MODEL . 'Admin/AdminCompany.php';
$adminCompany = new AdminCompany($db);

include_once MODEL . 'Admin/AdminSocialMedia.php';
$adminSocialMedia = new AdminSocialMedia($db);

include_once MODEL . 'Admin/AdminMenu.php';
$adminMenu = new AdminMenu($db, $languageID);

function deleteAllLanguageJsonFile(){
    $jsonFolder = JSON_DIR . "Language";
    $files = glob($jsonFolder . '/*.json'); // Dizindeki tüm .json dosyalarını al

    foreach($files as $file){
        if(is_file($file)){
            unlink($file); // Dosyayı sil
        }
    }

    global $adminSiteSettings, $languageID;

    $checkSiteConfigVersion = $adminSiteSettings->getSiteConfigVersions($languageID);
    if ($checkSiteConfigVersion) {
        Log::adminWrite("Site konfigürasyon güncelleniyor","info");
        $adminSiteSettings->updateSiteConfigVersion($languageID);
    }
    else{
        Log::adminWrite("Site konfigürasyon ekleniyor","info");
        $adminSiteSettings->addSiteConfigVersion($languageID);
    }
}

if($action == "getLanguage"){
    $language = $adminLanguage->getLanguage($languageID);
    echo json_encode($language);
    exit();
}
elseif ($action == "addLanguage"){

    $languageName = $requestData["languageName"] ?? null;
    $languageCode = $requestData["languageCode"] ?? null;
    $isMainLanguage = $requestData["isMainLanguage"] ?? 0;
    $isActive = $requestData["isActive"] ?? 0;
    $translateWithAI = $requestData['translateWithAI'] ?? 0;

    if(empty($languageName) || empty($languageCode)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Name and code cannot be empty'
        ]);
        exit();
    }

    $adminLanguage->beginTransaction();

    $checkLanguage = $adminLanguage->checkLanguage($languageCode);
    if($checkLanguage["status"] == "success"){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu dil kodu zaten mevcut.'
        ]);
        exit();
    }

    if($isMainLanguage == 1){
        $updateMainLanguage = $adminLanguage->resetMainLanguage();
        if ($updateMainLanguage["status"] == "error") {
            $adminLanguage->rollBack();
            echo json_encode($updateMainLanguage);
            exit();
        }
    }

    $languageUniqID = $helper->generateUniqID();
    $languageAddDate = date("Y-m-d H:i:s");
    $languageUpdateDate = date("Y-m-d H:i:s");

    $languageData = [
        'languageUniqID' => $languageUniqID,
        'languageAddDate' => $languageAddDate,
        'languageUpdateDate' => $languageUpdateDate,
        'languageName' => $languageName,
        'languageCode' => $languageCode,
        'isMainLanguage' => $isMainLanguage,
        'isActive' => $isActive
    ];

    $addLanguage = $adminLanguage->addLanguage($languageData);
    if($addLanguage["status"] == "error") {
        $adminLanguage->rollBack();
        echo json_encode($addLanguage);
        exit();
    }

    $newLanguageID = $addLanguage["languageID"];
    // Ana dil ID'sini dinamik olarak bul
    $mainLanguageID = $adminLanguage->getMainLanguageId();

    //firma bilgilerini çekip yeni dil için kopyalayalım
    $companyData = $adminCompany->getCompanyByLanguageID($mainLanguageID);
    if(empty($companyData)){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Anadilde Firma bilgileri alınırken hata oluştu.'
        ]);
        exit();
    }

    unset($companyData['companyID']);
    $newCompanyData = $companyData;
    $newCompanyData["companyCountryID"] = $companyData["country"];
    $newCompanyData["companyCityID"] = $companyData["city"];
    $newCompanyData["companyCountyID"] = $companyData["county"];
    $newCompanyData["companyAreaID"] = $companyData["area"];
    $newCompanyData["companyNeighborhoodID"] = $companyData["neighborhood"];
    $newCompanyData["companyPostalCode"] = $companyData["postalCode"];
    $newCompanyData["companyAddress"] = $companyData["address"];
    $newCompanyData["companyEmail"] = $companyData["email"];
    $newCompanyData["companyGsm"] = $companyData["gsm"];
    $newCompanyData["companyPhone"] = $companyData["phone"];
    $newCompanyData["companyFax"] = $companyData["fax"];
    $newCompanyData["companyMap"] = $companyData["map"];
    $newCompanyData["companyCountryCode"] = $companyData["countryCode"];
    $newCompanyData["languageID"] = $newLanguageID;
    $newCompanyData['uniqueId'] = $helper->generateUniqID();

    Log::adminWrite("Add Company: ". json_encode($newCompanyData));
    $addCompany = $adminCompany->saveCompany($newCompanyData);
    if($addCompany == false){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Yeni dilde firma bilgileri kopyalanırken hata oluştu.'
        ]);
        exit();
    }

    //logoyu kopyalayalım
    $logoData = $adminCompany->getCompanyLogo($mainLanguageID);
    if(empty($logoData)){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message'
            => 'Anadilde logo bilgileri alınırken hata oluştu.'
        ]);
        exit();
    }

    $addLogo = $adminCompany->addCompanyLogo($newLanguageID,$logoData['logoText'], $logoData['imageID']);
    if($addLogo == false){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Yeni dilde logo eklenirken hata oluştu'
        ]);
        exit();
    }

    //Sosyal Medya hesaplarını kopyalayalım
    $socialMediaData = $adminSocialMedia->getSocialMedia($mainLanguageID);
    if(empty($socialMediaData)){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Anadilde sosyal medya hesapları alınamadı'
            ]);
        exit();
    }

    unset($socialMediaData['ayarsosyalmedyaid']);
    $socialMediaData['languageID'] = $newLanguageID;
    $socialMediaData['socialMediaUniqID'] = $helper->generateUniqID();

    $addSocialMedia = $adminSocialMedia->addSocialMedia($socialMediaData);
    if($addSocialMedia == false){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Yeni dilde sosyal medya hesapları eklenemedi'
            ]);
        exit();
    }

    //menüleri kopyalayalım
    $menuData = $adminMenu->getMenuByLanguage($mainLanguageID);
    if(empty($menuData)){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Anadilde menüler alınamadı'
        ]);
        exit();
    }

    foreach ($menuData as $menu) {
        $newMenu['languageID'] = $newLanguageID;
        $newMenu['contentUniqID'] = $helper->generateUniqID();
        $newMenu['contentOrjUniqID'] = $menu['orjbenzersizid'];
        $newMenu['menuLocation'] = $menu['menukategori'];
        $newMenu['menuParent'] = $menu['ustmenuid'];
        $newMenu['menuLayer'] = $menu['menukatman'];
        $newMenu['menuName'] = $menu['menuad'];
        $newMenu['menuLink'] = $menu['menuLink'];
        $newMenu['menuArea'] = $menu['menusira'];
        $newMenu['getSubCategory'] = $menu['altkategori'];
        $newMenu['menuType'] = $menu['menuType'];
        $addMenu = $adminMenu->saveMenu($newMenu);
        if ($addMenu == false) {
            $adminLanguage->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Yeni dilde menüler eklenemedi'
            ]);
            exit();
        }
    }

    //site ayarlarını kopyalayalım
    $siteSettings = $adminSiteSettings->getSiteSettingsByLanguageID($mainLanguageID);
    if(empty($siteSettings)){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Anadilde site ayarları getirilemedi'
            ]);
        exit();
    }

    foreach ($siteSettings as $setting) {
        
        $addSetting = $adminSiteSettings->addSettingWithLanguageId($setting['section'], $setting['element'], $newLanguageID, $setting['is_visible']);
        if ($addSetting == false) {
            $adminLanguage->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Yeni dilde site ayarları eklenemedi'
            ]);
            exit();
        }
    }

    //genel site ayarları
    $generalSettings = $adminSiteSettings->getGeneralSettings($mainLanguageID);
    if(empty($generalSettings)){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Anadilde genel site ayarları alınamadı'
        ]);
        exit();
    }

    unset($generalSettings['ayargenelid']);
    $generalSettings['dilid'] = $newLanguageID;
    $addGeneralSettings = $adminSiteSettings->addGeneralSettings($generalSettings);
    if($addGeneralSettings == false){
        $adminLanguage->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Yeni dilde genel site ayarları eklenemedi'
        ]);
        exit();
    }


    $jobData = [
        'source_language_id' => $mainLanguageID,
        'target_language_id' => $newLanguageID,
        'translate_with_ai' => $translateWithAI
    ];

    $createJob = $adminLanguage->createCopyJob($jobData);

    if($createJob["status"] == "error") {
        $adminLanguage->rollBack();
        echo json_encode($createJob);
        exit();
    }

    $adminLanguage->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Dil başarıyla eklendi. İçerik yapısı arka planda kopyalanıyor ve çeviriye hazırlanıyor...'
    ]);

}
elseif ($action == "updateLanguage"){

    $languageID = $requestData["languageID"] ?? 0;
    $languageName = $requestData["languageName"] ?? null;
    $languageCode = $requestData["languageCode"] ?? null;
    $isMainLanguage = $requestData["isMainLanguage"] ?? 0;
    $isActive = $requestData["isActive"] ?? 0;

    if($languageID == 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID cannot be empty'
        ]);
        exit();
    }

    if(empty($languageName) || empty($languageCode)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Name and code cannot be empty'
        ]);
        exit();
    }

    //print_r($requestData);exit();

    $adminLanguage->beginTransaction();

    if($isMainLanguage == 1){
        $updateMainLanguage = $adminLanguage->resetMainLanguage();

        if ($updateMainLanguage["status"] == "error") {
            echo json_encode($updateMainLanguage);
            exit();
        }
    }

    $languageUpdateDate = date("Y-m-d H:i:s");

    $languageData = [
        'languageName' => $languageName,
        'languageCode' => $languageCode,
        'isMainLanguage' => $isMainLanguage,
        'isActive' => $isActive,
        'languageUpdateDate' => $languageUpdateDate,
        'languageID' => $languageID
    ];

    $updateLanguage = $adminLanguage->updateLanguage($languageData);

    if($updateLanguage["status"] == "error") {
        $adminLanguage->rollBack();
        echo json_encode($updateLanguage);
        exit();
    }

    $adminLanguage->commit();
    echo json_encode($updateLanguage);
    exit();
}
elseif ($action == "deleteLanguage"){
    $languageID = $requestData["languageID"] ?? 0;

    if($languageID == 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID cannot be empty'
        ]);
        exit();
    }

    $deleteLanguage = $adminLanguage->deleteLanguage($languageID);
    echo json_encode($deleteLanguage);
    exit();
}
elseif ($action == "setLanguage"){
    $languageID = $requestData["languageID"] ?? 0;

    if($languageID == 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID cannot be empty'
        ]);
        exit();
    }

    $_SESSION["languageID"] = $languageID;
    header("Location: ".$referrer);
    exit();

}
elseif ($action == "getConstantWithGroup"){
    $constantGorup = $requestData["constantGorup"] ?? null;
    $languageCode = $requestData["languageCode"] ?? null;

    if(empty($constantGorup)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant Group cannot be empty'
        ]);
        exit();
    }

    $constantRows = $adminLanguage->getLanguageConstantTranslations($languageCode,$constantGorup);

    if(empty($constantRows)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant not found'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'constantRows' => $constantRows
    ]);
    exit();
}
elseif($action == "updateLanguageConstantTranslation"){
    $constantData = $requestData["constantData"] ?? null;

    if(empty($constantData)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Translation Data cannot be empty'
        ]);
        exit();
    }


    $languageCode = $constantData["languageCode"] ?? null;
    $constantGroup = $constantData["constantGroup"] ?? null;
    $constantIDs = $constantData["constantIDs"] ?? null;
    $translationValues = $constantData["translationValues"] ?? null;
    $translationIDs = $constantData["translationIDs"] ?? null;

    if(empty($languageCode) || empty($constantGroup) || empty($constantIDs) || empty($translationValues) || empty($translationIDs)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant Data cannot be empty'
        ]);
        exit();
    }

    $adminLanguage->beginTransaction();

    foreach ($constantIDs as $i => $constantID) {
        //çeviri var mı kontrol edelim
        $translation = $adminLanguage->getLanguageConstantTranslationByID($translationIDs[$i]);

        if(empty($translation)){
            $tarnslationData = [
                'constantID' => $constantID,
                'translationValue' => $translationValues[$i],
                'languageCode' => $languageCode,
            ];

            $addLanguageConstantTranslation = $adminLanguage->addLanguageConstantTranslation($tarnslationData);

            if($addLanguageConstantTranslation["status"] == "error"){
                $adminLanguage->rollBack();
                echo json_encode($addLanguageConstantTranslation);
                exit();
            }
        }
        else{
            $updateLanguageConstantTranslation = $adminLanguage->updateLanguageConstantTranslation([
                'translationValue' => $translationValues[$i],
                'translationID' => $translationIDs[$i]
            ]);

            if($updateLanguageConstantTranslation["status"] == "error"){
                $adminLanguage->rollBack();
                echo json_encode($updateLanguageConstantTranslation);
                exit();
            }
        }
    }

    $adminLanguage->commit();
    deleteAllLanguageJsonFile();
    echo json_encode([
        'status' => 'success',
        'message' => 'Translations updated'
    ]);
    exit();
}
elseif($action == "updateLanguageConstant"){
    $constantData = $requestData["constantData"] ?? null;

    if(empty($constantData)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant Data cannot be empty'
        ]);
        exit();
    }

    $languageCode = $constantData["languageCode"] ?? null;
    $constantGroup = $constantData["constantGroup"] ?? null;
    $constantName = $constantData["constantNames"] ?? null;
    $constantIDs = $constantData["constantIDs"] ?? null;
    $translationValues = $constantData["translationValues"] ?? null;

    if(empty($languageCode) || empty($constantGroup) || empty($constantIDs) || empty($translationValues)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant Data cannot be empty'
        ]);
        exit();
    }

    $adminLanguage->beginTransaction();

    foreach ($constantIDs as $i => $constantID) {
        $updateLanguageConstant = $adminLanguage->updateLanguageConstant([
            'constantID' => $constantID,
            'constantName' => $helper->sanitizeConstantName($constantName[$i]),
            'constantValue' => $translationValues[$i],
            'constantGroup' => $constantGroup
        ]);

        if($updateLanguageConstant["status"] == "error"){
            $adminLanguage->rollBack();
            echo json_encode($updateLanguageConstant);
            exit();
        }
    }

    $adminLanguage->commit();
    deleteAllLanguageJsonFile();
    echo json_encode([
        'status' => 'success',
        'message' => 'Constant updated'
    ]);
    exit();
}
elseif($action == "addLanguageConstant"){
    $constantData = $requestData["constantData"] ?? null;

    if(empty($constantData)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant Data cannot be empty'
        ]);
        exit();
    }

    $constantGroup = $constantData["constantGroup"] ?? null;
    $constantName = $constantData["constantName"] ?? null;
    $translationValue = $constantData["translationValue"] ?? null;

    if(empty($constantGroup) || empty($constantName) || empty($translationValue)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant Data cannot be empty'
        ]);
        exit();
    }

    $constantName = $helper->sanitizeConstantName($constantName);

    $adminLanguage->beginTransaction("addLanguageConstant");

    $checkConstant = $adminLanguage->checkConstant($constantGroup,$constantName);
    if($checkConstant["status"] == "error"){
        //ekleme yapılacak
        $addLanguageConstant = $adminLanguage->addLanguageConstant([
            'constantName' => $constantName,
            'constantValue' => $translationValue,
            'constantGroup' => $constantGroup
        ]);

        if($addLanguageConstant["status"] == "error"){
            $adminLanguage->rollBack();
            echo json_encode($addLanguageConstant);
            exit();
        }

        $message = "Constant added";
    }
    else{

        //güncelleme yapılacak
        $updateLanguageConstant = $adminLanguage->updateLanguageConstant([
            'constantID' => $checkConstant["data"]["constantID"],
            'constantName' => $constantName,
            'constantValue' => $translationValue,
            'constantGroup' => $constantGroup
        ]);

        if($updateLanguageConstant["status"] == "error"){
            $adminLanguage->rollBack();
            echo json_encode($updateLanguageConstant);
            exit();
        }

        $message = "Constant updated";
    }

    $adminLanguage->commit();
    deleteAllLanguageJsonFile();
    echo json_encode([
        'status' => 'success',
        'message' => 'Constant added'
    ]);
    exit();
}
elseif($action == "deleteLanguageConstant"){
    $constantID = $requestData["constantID"] ?? null;

    if(empty($constantID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Constant ID is required'
        ]);
        exit();
    }

    $adminLanguage->beginTransaction();

    $result = $adminLanguage->deleteLanguageConstant($constantID);

    if($result){
        $adminLanguage->commit();
        deleteAllLanguageJsonFile();
        echo json_encode([
            'status' => 'success',
            'message' => 'Constant deleted'
        ]);
        exit();
    }

    $adminLanguage->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Constant delete failed'
    ]);
    exit();
}
elseif($action == "getLanguagesForTranslation"){
    // Çeviri için aktif dilleri al (ana dil hariç)
    try {
        $languages = $adminLanguage->getLanguages();
        $mainLanguageID = null;
        
        // Ana dil ID'sini bul
        foreach ($languages as $lang) {
            if ($lang['isMainLanguage'] == 1) {
                $mainLanguageID = $lang['languageID'];
                break;
            }
        }
        
        if (!$mainLanguageID) {
            $mainLanguageID = 1; // Varsayılan
        }
        
        // Ana dil hariç aktif dilleri filtrele
        $translationLanguages = array_filter($languages, function($lang) use ($mainLanguageID) {
            return $lang['languageID'] != $mainLanguageID && $lang['isActive'] == 1;
        });
        
        // Array indexlerini sıfırla
        $translationLanguages = array_values($translationLanguages);
        
        echo json_encode([
            'status' => 'success',
            'languages' => $translationLanguages
        ]);
        exit();
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Diller alınırken hata oluştu: ' . $e->getMessage()
        ]);
        exit();
    }
}
