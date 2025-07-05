<?php

/**
 * @var Database $db
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

$languageCode = $requestData["languageCode"];

include_once MODEL . 'LanguageModel.php';
$languageModel = new Language($db,$languageCode, "");

include_once MODEL . 'BannerModel.php';
$bannerTypeModel = new BannerTypeModel($db);
$bannerLayoutModel = new BannerLayoutModel($db);
$bannerGroupModel = new BannerGroupModel($db);
$bannerStyleModel = new BannerStyleModel($db);
$bannerDisplayRulesModel = new BannerDisplayRulesModel($db);
$bannerModel = new BannerModel($db);

//action getBannerTypes koşılunu yazalım
if($action == "getDisplayRulesByLanguageCode") {
    return $bannerDisplayRulesModel->getDisplayRulesByLanguageCode($languageCode);
}
elseif($action == "getGroupById"){
    return $bannerGroupModel->getGroupById($requestData["id"]);
}
elseif($action == "getBannersByGroupID"){
    return $bannerModel->getBannersByGroupID($requestData["id"]);
}
elseif($action == "getStyleById"){
    return $bannerStyleModel->getStyleById($requestData["id"]);
}