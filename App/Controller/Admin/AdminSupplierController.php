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

include_once MODEL . 'Admin/AdminSupplier.php';
$supplierModel = new AdminSupplier($db);

if($action == "addSupplier"){

    $supplierTitle = $requestData["supplierTitle"] ?? null;
    //boş olamaz
    if(empty($supplierTitle)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tedarikçi Kısa Ad alanı boş olamaz'
        ]);
        exit();
    }

    $supplierEmail = $requestData["supplierEmail"] ?? null;
    if(empty($supplierEmail)){
        $supplierPassword = "@";
    }

    $supplierIdentityNumber = $requestData["supplierIdentityNumber"] ?? null;
    $supplierName = $requestData["supplierName"] ?? null;
    $supplierSurname = $requestData["supplierSurname"] ?? null;

    $supplierPhoneNumber = $requestData["supplierPhoneNumber"] ?? null;
    $supplierPassword = $requestData["supplierPassword"] ?? null;
    if(empty($supplierPassword)){
        $supplierPassword = "1A2b3c4d5f";
    }
    $supplierDescription = $requestData["supplierDescription"] ?? null;
    $supplierInvoiceTitle = $requestData["supplierInvoiceTitle"] ?? null;
    $supplierTaxOffice = $requestData["supplierTaxOffice"] ?? null;
    $supplierTaxNumber = $requestData["supplierTaxNumber"] ?? null;


    $supplierIdentityNumber = $helper->encrypt($supplierIdentityNumber,$config->key);
    $supplierTitle = $helper->encrypt($supplierTitle,$config->key);
    $supplierName = $helper->encrypt($supplierName,$config->key);
    $supplierSurname = $helper->encrypt($supplierSurname,$config->key);
    $supplierEmail = $helper->encrypt($supplierEmail,$config->key);
    $supplierPhoneNumber = $helper->encrypt($supplierPhoneNumber,$config->key);
    $supplierPassword = $helper->encrypt($supplierPassword,$config->key);
    $supplierInvoiceTitle = $helper->encrypt($supplierInvoiceTitle,$config->key);
    $supplierTaxOffice = $helper->encrypt($supplierTaxOffice,$config->key);
    $supplierTaxNumber = $helper->encrypt($supplierTaxNumber,$config->key);

    $supplierUniqID = $helper->createPassword(20,2);

    $supplierData =[
        'supplierUniqID' => $supplierUniqID,
        'supplierIdentityNumber' => $supplierIdentityNumber,
        'supplierType' => 2, // 2 tedarikçi
        'supplierTitle' => $supplierTitle,
        'supplierName' => $supplierName,
        'supplierSurname' => $supplierSurname,
        'supplierEmail' => $supplierEmail,
        'supplierPhoneNumber' => $supplierPhoneNumber,
        'supplierPassword' => $supplierPassword,
        'supplierDescription' => $supplierDescription,
        'supplierInvoiceTitle' => $supplierInvoiceTitle,
        'supplierTaxOffice' => $supplierTaxOffice,
        'supplierTaxNumber' => $supplierTaxNumber,
    ];

    $result = $supplierModel->addSupplier($supplierData);

    if($result){
        echo json_encode([
            'status' => 'success',
            'message' => 'Tedarikçi başarıyla eklendi'
        ]);
    }else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Tedarikçi eklenirken bir hata oluştu'
        ]);
    }
}
elseif($action == "updateSupplier"){

    $supplierID = $requestData["supplierID"] ?? null;
    if(empty($supplierID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tedarikçi Kısa Ad alanı boş olamaz'
        ]);
        exit();
    }

    $supplierTitle = $requestData["supplierTitle"] ?? null;
    if(empty($supplierTitle)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kısa Ad boş olamaz'
        ]);
        exit();
    }

    $supplierEmail = $requestData["supplierEmail"] ?? null;
    if(empty($supplierEmail)){
        $supplierEmail = "@";
    }

    $supplierIdentityNumber = $requestData["supplierIdentityNumber"] ?? null;
    $supplierName = $requestData["supplierName"] ?? null;
    $supplierSurname = $requestData["supplierSurname"] ?? null;
    $supplierPhoneNumber = $requestData["supplierPhoneNumber"] ?? null;
    $supplierPassword = $requestData["supplierPassword"] ?? null;
    if(empty($supplierPassword)){
        $supplierPassword = "1A2b3c4d5f";
    }
    $supplierDescription = $requestData["supplierDescription"] ?? null;
    $supplierInvoiceTitle = $requestData["supplierInvoiceTitle"] ?? null;
    $supplierTaxOffice = $requestData["supplierTaxOffice"] ?? null;
    $supplierTaxNumber = $requestData["supplierTaxNumber"] ?? null;
    $supplierIsActive = $requestData["supplierIsActive"] ?? 0;

    $supplierIdentityNumber = $helper->encrypt($supplierIdentityNumber,$config->key);
    $supplierTitle = $helper->encrypt($supplierTitle,$config->key);
    $supplierName = $helper->encrypt($supplierName,$config->key);
    $supplierSurname = $helper->encrypt($supplierSurname,$config->key);
    $supplierEmail = $helper->encrypt($supplierEmail,$config->key);
    $supplierPhoneNumber = $helper->encrypt($supplierPhoneNumber,$config->key);
    $supplierPassword = $helper->encrypt($supplierPassword,$config->key);
    $supplierInvoiceTitle = $helper->encrypt($supplierInvoiceTitle,$config->key);
    $supplierTaxOffice = $helper->encrypt($supplierTaxOffice,$config->key);
    $supplierTaxNumber = $helper->encrypt($supplierTaxNumber,$config->key);

    $supplierData =[
        'supplierID' => $supplierID,
        'supplierIdentityNumber' => $supplierIdentityNumber,
        'supplierTitle' => $supplierTitle,
        'supplierName' => $supplierName,
        'supplierSurname' => $supplierSurname,
        'supplierEmail' => $supplierEmail,
        'supplierPhoneNumber' => $supplierPhoneNumber,
        'supplierPassword' => $supplierPassword,
        'supplierDescription' => $supplierDescription,
        'supplierInvoiceTitle' => $supplierInvoiceTitle,
        'supplierTaxOffice' => $supplierTaxOffice,
        'supplierTaxNumber' => $supplierTaxNumber,
        'supplierIsActive' => $supplierIsActive
    ];

    $result = $supplierModel->updateSupplier($supplierData);

    if($result){
        echo json_encode([
            'status' => 'success',
            'message' => 'Tedarikçi başarıyla güncellendi'
        ]);
    }else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Tedarikçi güncellenirken bir hata oluştu'
        ]);
    }
}
elseif($action == "deleteSupplier"){

    $supplierID = $requestData["supplierID"] ?? null;
    if(empty($supplierID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Tedarikçi ID boş olamaz'
        ]);
        exit();
    }

    $result = $supplierModel->deleteSupplier($supplierID);

    if($result){
        echo json_encode([
            'status' => 'success',
            'message' => 'Tedarikçi başarıyla silindi'
        ]);
    }else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Tedarikçi silinirken bir hata oluştu'
        ]);
    }
}
else{
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}
