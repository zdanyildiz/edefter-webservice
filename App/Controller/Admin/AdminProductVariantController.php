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

include_once MODEL . 'Admin/AdminProductVariant.php';
$variantModel = new AdminProductVariant($db);

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

if ($action == "getVariantGroups") {

    $lang = $requestData["languageCode"] ?? "tr";
    $variantGroups = $variantModel->getVariantGroups($lang);
    echo json_encode($variantGroups);
    exit();
}
elseif ($action == "getVariantsByGroupID") {

    $variantGroupID = $requestData["variantGroupID"] ?? null;
    $lang = $requestData["languageCode"] ?? "tr";

    if (!isset($variantGroupID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant group id error'
        ]);
        exit();
    }

    $variants = $variantModel->getVariants($variantGroupID,$lang);
    echo json_encode($variants);
    exit();
}
elseif ($action == "getVariantsGroupByName") {

    $variantGroupName = $requestData["variantGroupName"] ?? null;
    $lang = $requestData["languageCode"] ?? "tr";

    if (!isset($variantGroupName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant group name error'
        ]);
        exit();
    }

    $variants = $variantModel->getVariantsGroupByName($variantGroupName,$lang);
    echo json_encode($variants);
    exit();
}
elseif ($action == "getVariantByName") {

    $variantName = $requestData["variantName"] ?? null;
    $lang = $requestData["languageCode"] ?? "tr";

    if (!isset($variantName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant name error'
        ]);
        exit();
    }

    $variants = $variantModel->getVariantByName($variantName,$lang);
    echo json_encode($variants);
    exit();
}
elseif ($action == "sortVariantGroups"){
    $variantGroupIDs = $requestData["variantGroupIDs"] ?? null;

    if (!isset($variantGroupIDs)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant group ids error'
        ]);
        exit();
    }

    //$variantGroupIDs = explode(",",$variantGroupIDs);

    $result = $variantModel->sortVariantGroups($variantGroupIDs);
    echo json_encode($result);
    exit();
}
elseif ($action == "addVariantGroup"){
    $variantGroupID = $requestData["variantGroupID"] ?? null;
    $variantGroupName = $requestData["variantGroupName"] ?? null;
    $languageCode = $requestData["languageCode"] ?? "tr";

    //boş olamazlar
    if (!isset($variantGroupID) || !isset($variantGroupName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant group id or name error'
        ]);
        exit();
    }


    if($variantGroupID>0){
        $variantModel->beginTransaction();
        $result = $variantModel->updateVariantGroup($variantGroupID,$variantGroupName);
        if($result['status'] == 'success'){
            $variantModel->commit();
        }
        else{
            $variantModel->rollback();
        }
    }
    else{

        $variantGroupUniqID = $helper->createPassword(20,2);

        $result = $variantModel->addVariantGroup($variantGroupName,$variantGroupUniqID);
    }

    echo json_encode($result);
}
elseif ($action == "addVariantGroupTranslate"){
    $variantGroupID = $requestData["variantGroupID"] ?? null;
    $variantGroupName = $requestData["variantGroupName"] ?? null;
    $languageCode = $requestData["languageCode"] ?? null;

    //boş olamazlar
    if (!isset($variantGroupID) || !isset($variantGroupName) || !isset($variantGroupName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant group id, name or language error'
        ]);
        exit();
    }

    $variantModel->beginTransaction();
    $result = $variantModel->addAndUpdateVariantGroupTranslate($variantGroupID,$variantGroupName, $languageCode);
    if($result['status'] == 'success'){
        $variantModel->commit();
    }
    else{
        $variantModel->rollback();
    }
    echo json_encode($result);
    exit();
}
elseif ($action == "deleteVariantGroup"){
    $variantGroupID = $requestData["variantGroupID"] ?? null;

    if (!isset($variantGroupID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant group id error'
        ]);
        exit();
    }

    $variantModel->beginTransaction();

    //varyant grubu sil
    $result = $variantModel->deleteVariantGroup($variantGroupID);

    if($result["status"] == "error"){
        $variantModel->rollBack();
        echo json_encode($result);
        exit();
    }

    //varyant grubu ait varyantları al
    $getVariantsResult = $variantModel->getVariantsByGroupID($variantGroupID);

    if($getVariantsResult["status"] == "success"){
        $variants = $getVariantsResult["data"];

        //varyant gruba ait varyantları sil
        $deleteVariantsResult = $variantModel->deleteVariantsByGroupID($variantGroupID);

        foreach ($variants as $variant){

            $variantID = $variant["variantID"];

            //translate varyantları sil
            $deleteVariantTranslateResult = $variantModel->deleteVariantTranslateByVariantID($variantID);
        }
    }

    //varyant grubun translate karşılıklarını sil
    $deleteVariantGroupTranslateResult = $variantModel->deleteVariantGroupTranslateByVariantGroupID($variantGroupID);

    $variantModel->commit();
    echo json_encode($result);
    exit();

}
elseif ($action == "addVariant"){

    $variantID = $requestData["variantID"] ?? null;
    $variantName = $requestData["variantName"] ?? null;
    $variantGroupID = $requestData["variantGroupID"] ?? null;

    //boş olamazlar
    if (!isset($variantName) || !isset($variantGroupID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Varyant id, name or group id error'
        ]);
        exit();
    }

    $variantModel->beginTransaction();
    $result = $variantModel->addVariant($variantName,$variantGroupID);
    if($result['status'] == 'success'){
        $variantModel->commit();
    }
    else{
        $variantModel->rollback();
    }

    echo json_encode($result);
}
elseif ($action == "updateVariantName"){
    $variantID = $requestData["variantID"] ?? null;
    $variantName = $requestData["variantName"] ?? null;

    //boş olamazlar
    if (!isset($variantID) || !isset($variantName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Varyant id or name error'
        ]);
        exit();
    }

    $variantModel->beginTransaction();
    $result = $variantModel->updateVariant($variantID,$variantName);
    if($result['status'] == 'success'){
        $variantModel->commit();
    }
    else{
        $variantModel->rollback();
    }

    echo json_encode($result);

}
elseif ($action == "deleteVariant"){
    $variantID = $requestData["variantID"] ?? null;

    if (!isset($variantID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant id error'
        ]);
        exit();
    }

    $variantModel->beginTransaction();

    //varyantın translate karşılıklarını sil
    $deleteVariantTranslateResult = $variantModel->deleteVariantTranslateByVariantID($variantID);

    //varyantı sil
    $result = $variantModel->deleteVariant($variantID);

    if($result["status"] == "error"){
        $variantModel->rollBack();
        echo json_encode($result);
        exit();
    }

    $variantModel->commit();
    echo json_encode($result);
    exit();
}
elseif ($action == "addAndUpdateVariantTranslate"){
    $variantID = $requestData["variantID"] ?? null;
    $variantName = $requestData["variantName"] ?? null;
    $languageCode = $requestData["languageCode"] ?? null;

    //boş olamazlar
    if (!isset($variantID) || !isset($variantName) || !isset($languageCode)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Varyant id, name or language error'
        ]);
        exit();
    }

    $variantModel->beginTransaction();
    $result = $variantModel->addAndUpdateVariantTranslate($variantID,$variantName, $languageCode);
    if($result['status'] == 'success'){
        $variantModel->commit();
    }
    else{
        $variantModel->rollback();
    }
    echo json_encode($result);
    exit();
}
elseif ($action == "sortVariants"){
    $variantIDs = $requestData["variantIDs"] ?? null;

    if (!isset($variantIDs)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Variant ids error'
        ]);
        exit();
    }

    //$variantIDs = explode(",",$variantIDs);

    $variantModel->beginTransaction();
    $result = $variantModel->sortVariants($variantIDs);
    if($result['status'] == 'success'){
        $variantModel->commit();
    }
    else{
        $variantModel->rollback();
    }
    echo json_encode($result);
    exit();
}