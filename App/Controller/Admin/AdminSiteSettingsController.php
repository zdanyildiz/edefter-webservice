<?php

// AdminSiteSettingsController.php

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
$languageID = $requestData["languageID"] ?? null;
if(empty($languageID)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Dil alanı boş olamaz.'
    ]);
    exit();
}
include_once MODEL . 'Admin/AdminSiteSettings.php';
$adminSiteSettings = new AdminSiteSettings($db, $languageID); // language_id = 1

if ($action == "addSiteSettings") {

    $section = $requestData["section"] ?? null;
    $element = $requestData["element"] ?? null;
    $is_visible = $requestData["is_visible"];

    if (empty($section) || empty($element) || empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil, Bölüm ve Öğe alanları doldurulmalıdır!'
        ]);
        exit();
    }

    $adminSiteSettings->beginTransaction();
    $addSuccess = $adminSiteSettings->addSetting($section, $element, $is_visible);

    if (!$addSuccess) {
        $adminSiteSettings->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Ayar eklenirken bir hata oluştu veya ayar zaten mevcut.'
        ]);
        exit();
    }
    $adminSiteSettings->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Yeni ayar başarıyla eklendi!'
    ]);
    exit();
}
elseif ($action == "saveSiteSettings") {
    $ids = $requestData["ids"] ?? null;
    $sections = $requestData["sections"] ?? null;
    $elements = $requestData["elements"] ?? null;

    if (empty($ids) || empty($sections) || empty($elements)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Ayar Bölüm ve Öğe alanları doldurulmalıdır!'
        ]);
        exit();
    }

    $adminSiteSettings->beginTransaction();
    foreach($ids as $id){
        $is_visible = $requestData["is_visibles"][$id] ?? 0;
        Log::adminWrite("Update settings for id: $id, is_visible: $is_visible", "info");
        $updateSuccess = $adminSiteSettings->updateSettings($is_visible, $id);
        if (!$updateSuccess) {
            $adminSiteSettings->rollback();
            echo json_encode([
                'status' => 'error',
                'message' => 'Ayarları güncellerken bir hata oluştu.'
            ]);
            exit();
        }
    }

    $checkSiteConfigVersion = $adminSiteSettings->getSiteConfigVersions($languageID);
    if ($checkSiteConfigVersion) {
        Log::adminWrite("Site konfigürasyon güncelleniyor","info");
        $adminSiteSettings->updateSiteConfigVersion($languageID);
    }
    else{
        Log::adminWrite("Site konfigürasyon ekleniyor","info");
        $adminSiteSettings->addSiteConfigVersion($languageID);
    }

    $adminSiteSettings->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Ayarlar başarıyla güncellendi!'
    ]);
    exit();

}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz action.'
    ]);
    exit();
}

