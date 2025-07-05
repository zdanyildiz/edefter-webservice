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

include_once MODEL . 'Admin/AdminSiteSettings.php';
$adminSiteSettings = new AdminSiteSettings($db, $languageID);

include_once MODEL . 'Admin/AdminCampaignAndPointsManager.php';
$campaignModel = new AdminCampaignAndPointsManager($db);

if($action == "addCampaign"){

    $campaignName = $requestData["campaignName"] ?? null;
    //boş olamaz
    if(empty($campaignName)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya adı belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignDescription = $requestData["campaignDescription"] ?? null;
    $campaignType = $requestData["campaignType"] ?? null;
    //boş olamaz
    if(empty($campaignType)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya türü belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignStartDate = $requestData["campaignStartDate"] ?? null;
    //boş olamaz
    if(empty($campaignStartDate)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya başlangıç tarihi belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignEndDate = $requestData["campaignEndDate"] ?? null;
    //boş ve başlangıçtan küçük olamaz
    if(empty($campaignEndDate)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya bitiş tarihi belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignPriority = $requestData["campaignPriority"] ?? 0;

    $campaignData = [
        'campaignName' => $campaignName,
        'campaignDescription' => $campaignDescription,
        'campaignType' => $campaignType,
        'campaignStartDate' => $campaignStartDate,
        'campaignEndDate' => $campaignEndDate,
        'campaignPriority' => $campaignPriority
    ];

    $campaignModel->beginTransaction();

    $campaignResult = $campaignModel->addCampaign($campaignData);

    if($campaignResult) {

        $checkSiteConfigVersion = $adminSiteSettings->getSiteConfigVersions($languageID);
        if ($checkSiteConfigVersion) {
            Log::adminWrite("Site konfigürasyon güncelleniyor","info");
            $adminSiteSettings->updateSiteConfigVersion($languageID);
        }
        else{
            Log::adminWrite("Site konfigürasyon ekleniyor","info");
            $adminSiteSettings->addSiteConfigVersion($languageID);
        }

        $campaignModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Kampanya başarıyla eklendi.'
        ]);
        exit();
    }
    else{
        $campaignModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya eklenirken bir hata oluştu.'
        ]);
    }

}
elseif($action == "updateCampaign"){

    $campaignID = $requestData["campaignID"] ?? null;
    //boş olamaz
    if(empty($campaignID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya ID belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignName = $requestData["campaignName"] ?? null;
    //boş olamaz
    if(empty($campaignName)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya adı belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignDescription = $requestData["campaignDescription"] ?? null;
    $campaignType = $requestData["campaignType"] ?? null;
    //boş olamaz
    if(empty($campaignType)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya türü belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignStartDate = $requestData["campaignStartDate"] ?? null;
    //boş olamaz
    if(empty($campaignStartDate)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya başlangıç tarihi belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignEndDate = $requestData["campaignEndDate"] ?? null;
    //boş ve başlangıçtan küçük olamaz
    if(empty($campaignEndDate)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya bitiş tarihi belirtmelisiniz.'
        ]);
        exit();
    }

    $campaignPriority = $requestData["campaignPriority"] ?? 0;

    $campaignData = [
        'campaignID' => $campaignID,
        'campaignName' => $campaignName,
        'campaignDescription' => $campaignDescription,
        'campaignType' => $campaignType,
        'campaignStartDate' => $campaignStartDate,
        'campaignEndDate' => $campaignEndDate,
        'campaignPriority' => $campaignPriority
    ];

    $campaignModel->beginTransaction();

    $campaignResult = $campaignModel->updateCampaign($campaignData);

    if($campaignResult == -1) {
        $campaignModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya güncellenirken bir hata oluştu.'
        ]);
    }else{
        if($campaignResult == 0){
            $campaignModel->rollBack();
        }else{

            $checkSiteConfigVersion = $adminSiteSettings->getSiteConfigVersions($languageID);
            if ($checkSiteConfigVersion) {
                Log::adminWrite("Site konfigürasyon güncelleniyor","info");
                $adminSiteSettings->updateSiteConfigVersion($languageID);
            }
            else{
                Log::adminWrite("Site konfigürasyon ekleniyor","info");
                $adminSiteSettings->addSiteConfigVersion($languageID);
            }

            $campaignModel->commit();
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Kampanya başarıyla güncellendi.'
        ]);
        exit();
    }
}
elseif($action == "addQuantityDiscount"){

    $capmaignID = $requestData["campaignID"] ?? null;
    //boş olamaz
    if(empty($capmaignID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kampanya ID belirtmelisiniz.'
        ]);
        exit();
    }

    $quantities = $requestData["quantities"] ?? null;
    //boş olamaz
    if(empty($quantities)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Miktar belirtmelisiniz.'
        ]);
        exit();
    }

    $discounts = $requestData["discounts"] ?? null;
    //boş olamaz
    if(empty($discounts)){
        echo json_encode([
            'status' => 'error',
            'message' => 'İndirim belirtmelisiniz.'
        ]);
        exit();
    }


    $campaignModel->beginTransaction();

    $campaignModel->deleteQuantityDiscount($capmaignID);

    $errorFlag = false;
    //foreach ile her defasında bir satır ekleyelim
    foreach($quantities as $key => $quantity){
        $quantityDiscountData = [
            'campaignID' => $capmaignID,
            'quantityLimit' => $quantity,
            'discountRate' => $discounts[$key]
        ];

        $quantityDiscountResult = $campaignModel->addQuantityDiscount($quantityDiscountData);

        if(!$quantityDiscountResult){
            $errorFlag = true;
            break;
        }
    }

    if($errorFlag){
        $campaignModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Miktar indirim eklenirken bir hata oluştu.'
        ]);
    }else{
        $campaignModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Miktar indirim başarıyla eklendi.'
        ]);
        exit();
    }


}