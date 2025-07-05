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

include_once MODEL . 'Admin/AdminPriceSettings.php';
$priceSettingsModel = new AdminPriceSettings($db, $languageID);
if($action == "addPriceSettings"){

    $showPriceStatus = $requestData["showPriceStatus"] ?? 0;
    $showPriceToDealer = $requestData["showPriceToDealer"] ?? 0;
    $showOldPrice = $requestData["showOldPrice"] ?? 0;
    $currencyID = $requestData["currencyID"] ?? 1;
    $installmentStatus = $requestData["installmentStatus"] ?? 0;
    $taxRate = $requestData["taxRate"] ?? 0;
    $singlePaymentDiscountRate = $requestData["singlePaymentDiscountRate"] ?? 0;
    $bankTransferDiscountRate = $requestData["bankTransferDiscountRate"] ?? 0;
    $creditCardStatus = $requestData["creditCardStatus"] ?? 0;
    $cashOnDeliveryStatus = $requestData["cashOnDeliveryStatus"] ?? 0;
    $bankTransferStatus = $requestData["bankTransferStatus"] ?? 0;


    $addData = [
        "languageID" => $languageID,
        "showPriceStatus" => $showPriceStatus,
        "showPriceToDealer" => $showPriceToDealer,
        "showOldPrice" => $showOldPrice,
        "currencyID" => $currencyID,
        "installmentStatus" => $installmentStatus,
        "taxRate" => $taxRate,
        "creditCardStatus" => $creditCardStatus,
        "cashOnDeliveryStatus" => $cashOnDeliveryStatus,
        "bankTransferStatus" => $bankTransferStatus,
        "singlePaymentDiscountRate" => $singlePaymentDiscountRate,
        "bankTransferDiscountRate" => $bankTransferDiscountRate,
    ];

    $priceSetting = $priceSettingsModel->getPriceSettings();

    $priceSettingsModel->beginTransaction();

    if($priceSetting['status'] == 'success'){
        $addPriceSettings = $priceSettingsModel->updatePriceSettings($addData);
    }
    else{
        $addPriceSettings = $priceSettingsModel->addPriceSettings($addData);
    }

    if ($addPriceSettings['status'] == 'success') {
        $priceSettingsModel->commit();
    } else {
        $priceSettingsModel->rollBack();
    }

    echo json_encode($addPriceSettings);
}
else{
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}