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

if($action == "addGeneralSettings"){

    $languageID = $requestData["languageID"] ?? "";
    $domain = $requestData["domain"] ?? "";
    $siteType = $requestData["siteType"] ?? "";
    $isMemberRegistration = $requestData["isMemberRegistration"] ?? "";

    if (empty($languageID) || empty($domain)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Bilgiler boş olamaz'
        ]);
        exit();
    }

    include_once MODEL . 'Admin/GeneralSettings.php';
    $generalSettings = new GeneralSettings($db);

    $getGeneralSettings = $generalSettings->getGeneralSettings($languageID);

    if(empty($getGeneralSettings)){
        $addGeneralSettings = $generalSettings->addGeneralSettings($languageID, $domain, $siteType, $isMemberRegistration);
    }else{
        $addGeneralSettings = $generalSettings->updateGeneralSettings($languageID, $domain, $siteType, $isMemberRegistration);
    }

    if($addGeneralSettings['status'] == 'error'){
        echo json_encode([
            'status' => 'error',
            'message' => $addGeneralSettings['message']
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Genel ayarlar uygulandı'
    ]);
    exit;
}