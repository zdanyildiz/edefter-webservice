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
/**
 * @var adminSession $adminSession
 * @var AdminDatabase $db
 * @var Router $router
 */


$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

include_once MODEL."Admin/AdminGallery.php";
$galleryModel = new AdminGallery($db);

include_once MODEL."Admin/AdminImage.php";
$image = new AdminImage($db);

if($action == "getGalleryList"){
    $result = $galleryModel->getGalleryList();
    echo json_encode($result);
    exit();
}
elseif($action == "getGallery"){
    $galleryID = $requestData["galleryID"] ?? null;
    if(!isset($galleryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery ID error'
        ]);
        exit();
    }
    $result = $galleryModel->getGallery($galleryID);
    if(!$result){
        echo json_encode([
            'status' => 'error',
            'message' => 'Galleri bulunamadı'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit();
}
elseif($action == "getGalleryImages"){
    $galleryID = $requestData["galleryID"] ?? null;
    if(!isset($galleryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery ID error'
        ]);
        exit();
    }
    $result = $galleryModel->getGalleryImages($galleryID);
    if(!$result){
        echo json_encode([
            'status' => 'error',
            'message' => 'Galleri bulunamadı'
        ]);
        exit();
    }
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    exit();
}
elseif($action == "addGallery"){

    $galleryCreatedDate = date("Y-m-d H:i:s");
    $galleryUpdatedDate = date("Y-m-d H:i:s");
    $galleryUniqID = $helper->generateUniqID();
    $galleryName = $requestData["galleryName"] ?? null;
    $galleryDescription = $requestData["galleryDescription"] ?? null;
    $galleryOrder = $requestData["galleryOrder"] ?? 0;
    $galleryShowInCategory = $requestData["galleryShowInCategory"] ?? 0;
    $galleryDeleted = $requestData["galleryDeleted"] ?? 0;
    $galleryOrdering = $requestData["galleryOrdering"] ?? 0;
    //requestData: {"action":"addGallery","galleryID":"0","galleryUniqID":"","galleryName":"Test Galeri","galleryDescription":"Test galeri a\u00e7\u0131klama","imageID":["1","2"]}
    $imageIDs = $requestData["imageID"] ?? [];


    if(!isset($galleryName)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery name error'
        ]);
        exit();
    }

    if(count($imageIDs) == 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery image error'
        ]);
        exit();
    }

    $addGalleryData = [
        "galleryUniqID" => $galleryUniqID,
        "galleryName" => $galleryName,
        "galleryDescription" => $galleryDescription,
        "galleryCreatedDate" => $galleryCreatedDate,
        "galleryUpdatedDate" => $galleryUpdatedDate,
        "galleryOrder" => $galleryOrder,
        "galleryShowInCategory" => $galleryShowInCategory,
        "galleryDeleted" => $galleryDeleted,
        "galleryOrdering" => $galleryOrdering
    ];

    $galleryModel->beginTransaction();

    $result = $galleryModel->addGallery($addGalleryData);

    if($result["status"] == "success"){
        $galleryID = $result["galleryID"];

        $addGalleryImageData =[
            "galleryID" => $galleryID,
            "imageIDs" => $imageIDs
        ];

        $result = $galleryModel->addGalleryImage($addGalleryImageData);

        if($result["status"] == "success"){
            $galleryModel->commit();
        }
        else{
            $galleryModel->rollBack();
        }

        echo json_encode($result);
        exit();
    }
    else{
        $galleryModel->rollBack();
        echo json_encode($result);
        exit();
    }

}
elseif($action == "updateGallery"){

    $galleryID = $requestData["galleryID"] ?? null;
    $galleryUpdatedDate = date("Y-m-d H:i:s");
    $galleryName = $requestData["galleryName"] ?? null;
    $galleryDescription = $requestData["galleryDescription"] ?? null;
    $galleryOrder = $requestData["galleryOrder"] ?? 0;
    $galleryShowInCategory = $requestData["galleryShowInCategory"] ?? 0;
    $galleryOrdering = $requestData["galleryOrdering"] ?? 0;
    $imageIDs = $requestData["imageID"] ?? [];

    if(!isset($galleryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery ID error'
        ]);
        exit();
    }

    if(!isset($galleryName)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery name error'
        ]);
        exit();
    }

    if(count($imageIDs) == 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery image error'
        ]);
        exit();
    }

    $updateGalleryData = [
        "galleryID" => $galleryID,
        "galleryName" => $galleryName,
        "galleryDescription" => $galleryDescription,
        "galleryUpdatedDate" => $galleryUpdatedDate,
        "galleryOrder" => $galleryOrder,
        "galleryShowInCategory" => $galleryShowInCategory,
        "galleryOrdering" => $galleryOrdering
    ];

    $galleryModel->beginTransaction();

    $result = $galleryModel->updateGallery($updateGalleryData);

    if($result["status"] == "success"){

        $galleryModel->deleteGalleryImage($galleryID);

        $addGalleryImageData =[
            "galleryID" => $galleryID,
            "imageIDs" => $imageIDs
        ];

        $result = $galleryModel->addGalleryImage($addGalleryImageData);

        if($result["status"] == "success"){
            $galleryModel->commit();
        }
        else{
            $galleryModel->rollBack();
        }

        echo json_encode($result);
        exit();
    }
    else{
        $galleryModel->rollBack();
        echo json_encode($result);
        exit();
    }
}
elseif($action == "deleteGallery"){
    $galleryID = $requestData["galleryID"] ?? null;
    if(!isset($galleryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery ID error'
        ]);
        exit();
    }

    $galleryModel->beginTransaction();

    $result = $galleryModel->deleteGallery($galleryID);

    if($result["status"] == "success"){
        $galleryModel->commit();
    }
    else{
        $galleryModel->rollBack();
    }

    echo json_encode($result);
    exit();
}
elseif($action == "saveGalleryOrder"){
    //pageID,pageOrder

    $galleryOrder = $requestData["galleryOrder"] ?? null;
    $galleryID = $requestData["galleryID"] ?? null;

    //ikisi de boş olamaz
    if(empty($galleryOrder) || empty($galleryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen zorunlu alanları doldurunuz!'
        ]);
        exit();
    }

    $galleryModel->beginTransaction();

    $updatePageOrderData = [
        'galleryOrder' => $galleryOrder,
        'galleryID' => $galleryID
    ];

    $updateGalleryOrderResult = $galleryModel->updateGalleryOrder($updatePageOrderData);

    if($updateGalleryOrderResult['status'] == 'error'){
        $galleryModel->rollback();
        echo json_encode($updateGalleryOrderResult);
        exit();
    }

    $galleryModel->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Galeri sırası başarıyla güncellendi'
    ]);
}
elseif ($action == "searchGallery") {

    $searchText = $requestData["searchText"] ?? null;

    if (empty($searchText)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text is empty'
        ]);
        exit();
    }


    $searchGalleryResult = $galleryModel->searchGallery($searchText);

    if (!$searchGalleryResult) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search gallery error'
        ]);
        exit();
    }


    echo json_encode([
        'status' => 'success',
        'data' => $searchGalleryResult
    ]);
}