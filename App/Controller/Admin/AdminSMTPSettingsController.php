<?php

$documentRoot = str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
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

$adminCasper = $adminSession->getAdminCasper();
$config = $adminCasper->getConfig();
$json = $config->Json;
$helper = $config->Helper;

include_once MODEL . 'Admin/AdminSMTPSettings.php';
$smtpSettings = new AdminSMTPSettings($db);



if ($action == "saveSMTPSettings") {

    $smtpData = [
        'email' => $requestData["email"] ?? '',
        'password' => $helper->encrypt($requestData["password"], $config->key),
        'host' => $requestData["host"] ?? '',
        'port' => $requestData["port"] ?? '',
        'encryption' => $requestData["encryption"] ?? '',
        'languageID' => $requestData["languageID"] ?? '',
        'senderName' => $requestData["senderName"] ?? ''
    ];

    if (empty($smtpData["email"]) || empty($smtpData["password"]) || empty($smtpData["host"]) || empty($smtpData["port"]) || empty($smtpData["encryption"]) || empty($smtpData["languageID"]) || empty($smtpData["senderName"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tüm alanlar gereklidir'
        ]);
        exit();
    }

    Log::adminWrite("SMTP settings data: " . json_encode($smtpData), "info");
    $smtpSettings->beginTransaction("saveSMTPSettings");
    $settingsID = $requestData["id"] ?? 0;

    if ($settingsID > 0) {
        $smtpData["id"] = $settingsID;
        $result = $smtpSettings->updateSMTPSettings($smtpData);
    } else {
        $result = $smtpSettings->addSMTPSettings($smtpData);
    }

    if ($result) {
        $smtpSettings->commit("saveSMTPSettings");
        echo json_encode([
            'status' => 'success',
            'message' => 'SMTP settings saved successfully',
            'id' => $settingsID > 0 ? $settingsID : $result
        ]);
        exit();
    }

    $smtpSettings->rollback("saveSMTPSettings");
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to save SMTP settings'
    ]);
    exit();
} elseif ($action == "getSMTPSettings") {
    $languageID = $requestData["languageID"] ?? null;
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID is required'
        ]);
        exit();
    }
    $data = $smtpSettings->getSMTPSettings($languageID);
    if ($data) {
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No settings found'
        ]);
    }
    exit();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}
?>