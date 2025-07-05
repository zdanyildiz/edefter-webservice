<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var AdminSession $adminSession
 * @var Helper $helper
 */


$requestData = array_merge($_GET, $_POST);

$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

$helper = $config->Helper;

$referrer = $requestData["referrer"] ?? null;

$adminCasper = $adminSession->getAdminCasper();

$config = $adminCasper->getConfig();

$json = $config->Json;

include_once MODEL . 'Admin/AdminProductProperties.php';
$propertiesModel = new AdminProductProperties($db);

if ($action == "getProductProperties") {

    $properties = $propertiesModel->getProductProperties();
    echo json_encode($properties);
    exit();
}
elseif ($action == "addProductProperty") {

    $productPropertyName = $requestData["productPropertyName"] ?? null;
    $productPropertyValue = $requestData["productPropertyValue"] ?? null;

    if (!isset($productPropertyName) || !isset($productPropertyValue)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product property name or value is missing'
        ]);
        exit();
    }

    $addProperty = $propertiesModel->addProductProperty($productPropertyName, $productPropertyValue);

    echo json_encode($addProperty);
    exit();
}
//updateProductProperty
elseif ($action == "updateProductProperty") {

    $productPropertyID = $requestData["productPropertyID"] ?? null;
    $productPropertyName = $requestData["productPropertyName"] ?? null;
    $productPropertyValue = $requestData["productPropertyValue"] ?? null;

    if (!isset($productPropertyID) || !isset($productPropertyName) || !isset($productPropertyValue)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product property ID, name or value is missing'
        ]);
        exit();
    }

    $updateProperty = $propertiesModel->updateProductProperty($productPropertyID, $productPropertyName, $productPropertyValue);

    echo json_encode($updateProperty);
    exit();
}
//getProductPropertyByID
elseif ($action == "getProductPropertyByID") {

    $productPropertyID = $requestData["productPropertyID"] ?? null;

    if (!isset($productPropertyID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product property ID is missing'
        ]);
        exit();
    }

    $property = $propertiesModel->getProductPropertyByID($productPropertyID);

    echo json_encode($property);
    exit();
}
//searchProductProperty
elseif ($action == "searchProductProperty") {

    $search = $requestData["searchText"] ?? null;
    $lang = $requestData["languageCode"] ?? null;

    if (!isset($search)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search is missing'
        ]);
        exit();
    }

    $properties = $propertiesModel->searchProductProperty($search, $lang);

    echo json_encode($properties);
    exit();
}