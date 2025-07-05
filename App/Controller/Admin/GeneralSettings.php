<?php
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Helper $helper
 * @var Json $json
 * @var Session $session
 * @var array $requestData
 */

$admin = $session->getSession("admin");

$action = $requestData['action'] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lütfen gerekli alanları doldurunuz.',
    ]);
    exit();
}

$referrer = $requestData['referrer'];

if($action == 'getDomains'){
    include_once MODEL . 'Admin/GeneralSettings.php';
    $generalSettings = new GeneralSettings($db);
    $domains = $generalSettings->getDomains();
}