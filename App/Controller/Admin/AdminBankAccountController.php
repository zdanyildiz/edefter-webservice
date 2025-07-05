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

include_once MODEL . 'Admin/AdminBankAccount.php';
$bankAccountModel = new AdminBankAccount($db);

if ($action == "addBankAccount") {

    $languageID = $requestData["languageID"] ?? 1;
    $bankName = $requestData["bankName"] ?? null;
    $accountName = $requestData["accountName"] ?? null;
    $branchName = $requestData["branchName"] ?? null;
    $accountNumber = $requestData["accountNumber"] ?? null;
    $ibanNumber = $requestData["ibanNumber"] ?? null;
    $uniqueID = $helper->createPassword(20,2);

    $addData = [
        "languageID" => $languageID,
        "bankName" => $bankName,
        "accountName" => $accountName,
        "branchName" => $branchName,
        "accountNumber" => $accountNumber,
        "ibanNumber" => $ibanNumber,
        "uniqueID" => $uniqueID
    ];

    $result = $bankAccountModel->addBankAccount($addData);

    echo json_encode($result);
    exit();
}
elseif ($action == "getBankAccounts") {
    $getBankAccountsData = [
        "languageID" => $languageID
    ];

    $result = $bankAccountModel->getBankAccounts($getBankAccountsData);

    echo json_encode($result);
    exit();
}
elseif ($action == "deleteBankAccount") {
    $bankAccountID = $requestData["bankAccountID"] ?? null;

    $deleteData = [
        "bankAccountID" => $bankAccountID
    ];

    $result = $bankAccountModel->deleteBankAccount($deleteData);

    echo json_encode($result);
    exit();
}
elseif ($action == "updateBankAccount") {
    $bankAccountID = $requestData["bankAccountID"] ?? null;
    $bankName = $requestData["bankName"] ?? null;
    $accountName = $requestData["accountName"] ?? null;
    $branchName = $requestData["branchName"] ?? null;
    $accountNumber = $requestData["accountNumber"] ?? null;
    $ibanNumber = $requestData["ibanNumber"] ?? null;

    //hiçbiri boş olamaz
    if (empty($bankAccountID) || empty($bankName) || empty($accountName) || empty($branchName) || empty($accountNumber) || empty($ibanNumber)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tüm Alanlar doldurulmalıdır'
        ]);
        exit();
    }

    $updateData = [
        "bankAccountID" => $bankAccountID,
        "bankName" => $bankName,
        "accountName" => $accountName,
        "branchName" => $branchName,
        "accountNumber" => $accountNumber,
        "ibanNumber" => $ibanNumber
    ];

    $result = $bankAccountModel->updateBankAccount($updateData);

    echo json_encode($result);
    exit();
}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}
