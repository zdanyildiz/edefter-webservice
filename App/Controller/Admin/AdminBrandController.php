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

include_once MODEL . 'Admin/AdminBrand.php';
$brandModel = new AdminBrand($db);

if ($action == "getBrands") {

    $brands = $brandModel->getAllBrands();
    echo json_encode($brands);
    exit();
}
elseif ($action == "getBrand") {

    $brandID = $requestData["brandID"] ?? null;

    if (!isset($brandID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Brand id error'
        ]);
        exit();
    }

    $brand = $brandModel->getBrand($brandID);
    echo json_encode($brand);
    exit();
}
elseif ($action == "addBrand") {

    $brandName = $requestData["brandName"] ?? null;
    $brandDescription = $requestData["brandDescription"] ?? null;
    $brandUniqID = $helper->createPassword(20,2);
    $brandImage = $requestData["brandImage"] ?? null;

    if (!isset($brandName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Brand name error'
        ]);
        exit();
    }

    $addData = [
        'brandName' => $brandName,
        'brandDescription' => $brandDescription,
        'brandImage' => $brandImage,
        'brandUniqID' => $brandUniqID
    ];

    $brandModel->beginTransaction();

    $result = $brandModel->addBrand($addData);

    if($result['status'] == 'success') {
        $brandModel->commit();
    } else {
        $brandModel->rollBack();
    }

    echo json_encode($result);
}
elseif ($action == "updateBrand") {

    $brandID = $requestData["brandID"] ?? null;
    $brandName = $requestData["brandName"] ?? null;
    $brandDescription = $requestData["brandDescription"] ?? null;
    $brandImage = $requestData["brandImage"] ?? null;

    if (!isset($brandID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Brand id error'
        ]);
        exit();
    }

    if (!isset($brandName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Brand name error'
        ]);
        exit();
    }

    $updateData = [
        'brandID' => $brandID,
        'brandName' => $brandName,
        'brandImage' => $brandImage,
        'brandDescription' => $brandDescription
    ];

    $brandModel->beginTransaction();

    $result = $brandModel->updateBrand($updateData);

    if($result['status'] == 'success') {
        $brandModel->commit();
    } else {
        $brandModel->rollBack();
    }

    echo json_encode($result);
}
elseif ($action == "deleteBrand") {

    $brandID = $requestData["brandID"] ?? null;

    if (!isset($brandID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Brand id error'
        ]);
        exit();
    }

    $brandModel->beginTransaction();

    $result = $brandModel->deleteBrand($brandID);

    if($result['status'] == 'success') {
        $brandModel->commit();
    } else {
        $brandModel->rollBack();
    }

    echo json_encode($result);
}