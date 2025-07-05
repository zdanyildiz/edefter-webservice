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


include_once MODEL . 'Admin/AdminMenu.php';
$menuModel = new AdminMenu($db);

if ($action == "deleteMenu") {
    $languageID = $requestData["languageID"] ?? '';
    $menuLocation = $requestData["menuLocation"] ?? -1;

    //location ve language boş olamaz
    if (empty($languageID) || $menuLocation < 0 ) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Menü bilgileri boş olamaz. LanguageID: '.$languageID.' Location: '.$menuLocation
        ]);
        exit();
    }

    $menuModel->beginTransaction("deleteMenu");

    $deleteMenu = $menuModel->deleteMenu($languageID, $menuLocation);
    if(!$deleteMenu){
        $menuModel->rollback("deleteMenu");
        echo json_encode([
            'status' => 'error',
            'message' => 'Menü silinemedi ya da zaten boş'
        ]);
        exit();
    }
    elseif($deleteMenu==0){
        $menuModel->rollback("deleteMenu");
        echo json_encode([
            'status' => 'success',
            'message' => 'Silinecek bir şey yok'
        ]);
        exit();
    }

    $menuModel->commit("deleteMenu");

    $filePath = JSON_DIR ."Menu/menu_".$languageID.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Menü silindi'
    ]);
    exit();
}
elseif ($action == "saveMenu") {

    //print_r($requestData);exit;

    $languageID = $requestData["languageID"] ?? '';
    $menuLocation = $requestData["menuLocation"] ?? -1;
    $menuData = $requestData["menuData"] ?? [];
    //location ve language boş olamaz
    if (empty($languageID) || $menuLocation < 0 || empty($menuData)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Menü bilgileri boş olamaz'
        ]);
        exit();
    }

    $menuModel->beginTransaction();

    $menuModel->deleteMenu($languageID, $menuLocation);

    foreach ($menuData as $menu) {

        $parent = $menuModel->getMenuByLocation([
            "languageID" => $languageID,
            "menuLocation" => $menuLocation,
            "menuArea" => $menu["menuArea"]
        ]);

        if($parent){
            $parentID = $parent["menuid"];
        }
        else{
            $parentID = 0;
        }

        $menu["menuParent"] = $parentID;

        $result = $menuModel->saveMenu($menu);

        if(!$result){
            $menuModel->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Menü kaydedilemedi'
            ]);
            exit();
        }
    }

    $menuModel->commit();

    $filePath = JSON_DIR ."Menu/menu_".$languageID.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Menü kaydedildi'
    ]);
    exit();
}
