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

include_once MODEL . 'Admin/AdminCurrency.php';
$adminCurrency = new AdminCurrency($db);

if($action == "addCurrency"){
    $currencyName = $requestData["currencyName"] ?? null;
    $currencySymbol = $requestData["currencySymbol"] ?? null;
    $currencyCode = $requestData["currencyCode"] ?? null;
    $currencyRate = $requestData["currencyRate"] ?? 1;

    if(empty($currencyName) || empty($currencySymbol) || empty($currencyCode) || empty($currencyRate)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tüm alanları doldurunuz!'
        ]);
        exit();
    }

    $currencyData = [
        'currencyName' => $currencyName,
        'currencySymbol' => $currencySymbol,
        'currencyCode' => $currencyCode,
        'currencyRate' => $currencyRate
    ];

    $adminCurrency->beginTransaction();

    $result = $adminCurrency->addCurrency($currencyData);

    if($result){
        $adminCurrency->commit();

        $adminCurrency->updateCurrencyRates();

        echo json_encode([
            'status' => 'success',
            'message' => 'Para birimi eklendi'
        ]);
        exit();
    }
    else{
        $adminCurrency->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Para birimi eklenirken bir hata oluştu'
        ]);
        exit();
    }
}
elseif($action == "updateCurrency"){
    $currencyID = $requestData["currencyID"] ?? null;
    $currencyName = $requestData["currencyName"] ?? null;
    $currencySymbol = $requestData["currencySymbol"] ?? null;
    $currencyCode = $requestData["currencyCode"] ?? null;
    $currencyRate = $requestData["currencyRate"] ?? 1;

    if(empty($currencyID) || empty($currencyName) || empty($currencySymbol) || empty($currencyCode) || empty($currencyRate)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tüm alanları doldurunuz!'
        ]);
        exit();
    }

    $currencyData = [
        'currencyID' => $currencyID,
        'currencyName' => $currencyName,
        'currencySymbol' => $currencySymbol,
        'currencyCode' => $currencyCode,
        'currencyRate' => $currencyRate
    ];

    $adminCurrency->beginTransaction();

    $result = $adminCurrency->updateCurrency($currencyData);

    if($result){
        $adminCurrency->commit();

        $adminCurrency->updateCurrencyRates();

        echo json_encode([
            'status' => 'success',
            'message' => 'Para birimi güncellendi'
        ]);
        exit();
    }
    else{
        $adminCurrency->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Para birimi güncellenirken bir hata oluştu'
        ]);
        exit();
    }
}
elseif($action == "deleteCurrency"){
    $currencyID = $requestData["currencyID"] ?? null;

    if(empty($currencyID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Para birimi ID alanı boş olamaz'
        ]);
        exit();
    }

    $adminCurrency->beginTransaction();

    $result = $adminCurrency->deleteCurrency($currencyID);

    if($result){
        $adminCurrency->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Para birimi silindi'
        ]);
        exit();
    }
    else{
        $adminCurrency->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Para birimi silinirken bir hata oluştu'
        ]);
        exit();
    }
}