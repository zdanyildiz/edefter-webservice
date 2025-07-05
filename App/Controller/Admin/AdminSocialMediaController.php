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

include_once MODEL.'Admin/AdminSocialMedia.php';
$adminSocialMedia = new AdminSocialMedia($db);

$languageID = $requestData["languageID"] ?? null;
if (!isset($languageID)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Language ID cannot be empty'
    ]);
    exit();
}

$getSocialMedia = $adminSocialMedia->getSocialMedia($languageID);
if (empty($getSocialMedia)) {
    $action = "addSocialMedia";
    $requestData["socialMediaUniqID"] = $helper->generateUniqID();
}
else {
    $action = "updateSocialMedia";
}

if($action == "addSocialMedia"){
    $socialMediaData = [
        "languageID" => $requestData["languageID"],
        "facebook" => $requestData["facebook"] ?? "",
        "twitter" => $requestData["twitter"] ?? "",
        "googleplus" => $requestData["googleplus"] ?? "",
        "instagram" => $requestData["instagram"] ?? "",
        "linkedin" => $requestData["linkedin"] ?? "",
        "youtube" => $requestData["youtube"] ?? "",
        "pinterest" => $requestData["pinterest"] ?? "",
        "skype" => $requestData["skype"] ?? "",
        "blog" => $requestData["blog"] ?? "",
        "socialMediaUniqID" => $requestData["socialMediaUniqID"] ?? ""
    ];

    //languageID boş olamaz
    if (empty($socialMediaData["languageID"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID cannot be empty'
        ]);
        exit();
    }
    $adminSocialMedia->beginTransaction();
    $result = $adminSocialMedia->addSocialMedia($socialMediaData);

    if (!$result) {
        $adminSocialMedia->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Bir hata oluştu'
        ]);
    } else {
        $adminSocialMedia->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Social media hesapları güncellendi'
        ]);
    }
}
if($action == "updateSocialMedia"){
    $socialMediaData = [
        "languageID" => $requestData["languageID"],
        "facebook" => $requestData["facebook"] ?? "",
        "twitter" => $requestData["twitter"] ?? "",
        "googleplus" => $requestData["googleplus"] ?? "",
        "instagram" => $requestData["instagram"] ?? "",
        "linkedin" => $requestData["linkedin"] ?? "",
        "youtube" => $requestData["youtube"] ?? "",
        "pinterest" => $requestData["pinterest"] ?? "",
        "skype" => $requestData["skype"] ?? "",
        "blog" => $requestData["blog"] ?? ""
    ];

    //languageID boş olamaz
    if (empty($socialMediaData["languageID"])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID cannot be empty'
        ]);
        exit();
    }
    $adminSocialMedia->beginTransaction();
    $result = $adminSocialMedia->updateSocialMedia($socialMediaData);

    if ($result<=0) {
        $adminSocialMedia->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Bir hata oluştu'
        ]);
    } else {
        $adminSocialMedia->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Social media hesapları güncellendi'
        ]);
    }
}
else if($action == "getSocialMedia"){
    $languageID = $requestData["languageID"];
    $result = $adminSocialMedia->getSocialMedia($languageID);

    if (!empty($result)) {
        echo json_encode([
            'status' => 'success',
            'data' => $result
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Social media not found'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
}