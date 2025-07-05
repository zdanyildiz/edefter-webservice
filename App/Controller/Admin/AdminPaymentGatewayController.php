<?php
$documentRoot = str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
include_once MODEL . 'Admin/AdminPaymentGateway.php';

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

include_once MODEL . 'Admin/AdminPaymentGateway.php';
$gatewayModel = new AdminPaymentGateway($db);

if ($action == "addProvider") {
    $providerName = $requestData["providerName"] ?? null;
    $providerDescription = $requestData["providerDescription"] ?? null;
    $providerStatus = $requestData["providerStatus"] ?? 'aktif';
    $providerLanguageCode = $requestData["providerLanguageCode"] ?? 'tr';
    $settings = $requestData["dynamicField"] ?? [];

    if (!isset($providerName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Provider name error'
        ]);
        exit();
    }

    $providerData = [
        'name' => $providerName,
        'description' => $providerDescription,
        'status' => $providerStatus,
        'languageCode' => $providerLanguageCode
    ];

    $result = $gatewayModel->createProviderWithSettings($providerData, $settings);
    echo json_encode($result);
}
elseif ($action == "updateProvider")
{
    $providerId = $requestData["providerID"] ?? null;
    $providerName = $requestData["providerName"] ?? null;
    $providerDescription = $requestData["providerDescription"] ?? null;
    $providerStatus = $requestData["providerStatus"] ?? 'aktif';
    $providerLanguageCode = $requestData["providerLanguageCode"] ?? 'tr';
    $settings = $requestData["dynamicField"] ?? [];

    if (!isset($providerId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Provider id error'
        ]);
        exit();
    }

    $providerData = [
        'name' => $providerName,
        'description' => $providerDescription,
        'status' => $providerStatus,
        'languageCode' => $providerLanguageCode
    ];

    $result = $gatewayModel->updateProviderWithSettings($providerId, $providerData, $settings);
    echo json_encode($result);
}
elseif ($action == "deleteProvider") {
    $providerId = $requestData["providerID"] ?? null;

    if (!isset($providerId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Provider id error'
        ]);
        exit();
    }

    $result = $gatewayModel->deleteProvider($providerId);
    echo json_encode($result);
}
elseif ($action == "getProvider") {
    $providerId = $requestData["providerID"] ?? null;

    if (!isset($providerId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Provider id error'
        ]);
        exit();
    }

    $result = $gatewayModel->getProvider($providerId);
    echo json_encode($result);
}
elseif ($action == "getProviders") {
    $result = $gatewayModel->getProviders();
    echo json_encode($result);
}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
}
?>
