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


$json = $config->Json;

$helper = $config->Helper;

include_once MODEL . 'Admin/AdminSalesConversionCode.php';
$salesConversionCodeModel = new AdminSalesConversionCode($db);

include_once MODEL . 'Admin/AdminAnalysisCode.php';
$analysisCodeModel = new AdminAnalysisCode($db);

include_once MODEL . 'Admin/AdminCartConversionCode.php';
$cartConversionCodeModel = new AdminCartConversionCode($db);

include_once MODEL . 'Admin/AdminAdConversionCode.php';
$adConversionCodeModel = new AdminAdConversionCode($db);

include_once MODEL . 'Admin/AdminTagManager.php';
$tagManagerModel = new AdminTagManager($db);

// Platform Tracking Manager'ı yükle
include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
$platformTrackingManager = new PlatformTrackingManager($db, $config);

if($action == "getSalesConversionCode") {

    $languageID = $requestData['languageID'] ?? 1;
    $languageID = intval($languageID);

    $salesConversionCode = $salesConversionCodeModel->getSalesConversionCode($languageID);

    echo json_encode([
        'status' => 'success',
        'salesConversionCode' => $salesConversionCode
    ]);
}
elseif($action == "saveSalesConversionCode"){
    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }
    $salesConversionCodeName = $requestData['salesConversionCodeName'] ?? null;
    $salesConversionCodeContent = $requestData['salesConversionCodeContent'] ?? null;
    //ikisi de boş olamaz
    if (empty($salesConversionCodeName) || empty($salesConversionCodeContent)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Alanlar boş olamaz'
        ]);
        exit();
    }

    //$salesConversionCodeContent ile javascript kodları gelecek
    $salesConversionCodeContent = htmlspecialchars($salesConversionCodeContent);

    //kontrol edelim dile göre kayıt var mı?
    $salesConversionCode = $salesConversionCodeModel->getSalesConversionCode($languageID);

    $salesConversionCodeModel->beginTransaction();

    if (!empty($salesConversionCode)) {
        //varsa güncelle
        $data = [
            'languageID' => $languageID,
            'salesConversionCodeName' => $salesConversionCodeName,
            'salesConversionCodeContent' => $salesConversionCodeContent
        ];
        $result = $salesConversionCodeModel->updateSalesConversionCode($data);
    }
    else {
        //yoksa ekle
        $data = [
            'languageID' => $languageID,
            'salesConversionCodeName' => $salesConversionCodeName,
            'salesConversionCodeContent' => $salesConversionCodeContent,
            'uniqueID' => $helper->generateUniqID()
        ];
        $result = $salesConversionCodeModel->addSalesConversionCode($data);
    }

    if ($result) {
        $salesConversionCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Kayıt başarılı'
        ]);
    }
    else {
        $salesConversionCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Kayıt sırasında bir hata oluştu'
        ]);
    }

}
elseif($action == "deleteSalesConversionCode"){
    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $result = $salesConversionCodeModel->deleteSalesConversionCode($languageID);

    $salesConversionCodeModel->beginTransaction();

    if ($result) {
        $salesConversionCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Silme işlemi başarılı'
        ]);
    }
    else {
        $salesConversionCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Silme işlemi sırasında bir hata oluştu'
        ]);
    }
}
elseif($action == "getAnalysisCode"){

    $languageID = $requestData['languageID'] ?? 1;
    $languageID = intval($languageID);

    $analysisCode = $analysisCodeModel->getAnalysisCode($languageID);

    echo json_encode([
        'status' => 'success',
        'analysisCode' => $analysisCode
    ]);
}
elseif($action == "saveAnalysisCode"){

    $languageID = $requestData['languageID'];
    //boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $analysisCodeName = $requestData['analysisCodeName'] ?? null;
    $analysisCodeContent = $requestData['analysisCodeContent'] ?? null;
    $analysisCodeAmp = $requestData['analysisCodeAmp'] ?? null;
    //üçü de boş olamaz
    if (empty($analysisCodeName) || empty($analysisCodeContent) || empty($analysisCodeAmp)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Alanlar boş olamaz'
        ]);
        exit();
    }

    $analysisCodeContent = htmlspecialchars($analysisCodeContent);

    //bu dilde kod var mı bakalım
    $analysisCode = $analysisCodeModel->getAnalysisCode($languageID);

    $analysisCodeModel->beginTransaction();

    if (!empty($analysisCode)) {
        //varsa güncelle
        $data = [
            'languageID' => $languageID,
            'analysisCodeName' => $analysisCodeName,
            'analysisCodeContent' => $analysisCodeContent,
            'analysisCodeAmp' => $analysisCodeAmp
        ];
        $result = $analysisCodeModel->updateAnalysisCode($data);
    }
    else {
        //yoksa ekle
        $data = [
            'languageID' => $languageID,
            'analysisCodeName' => $analysisCodeName,
            'analysisCodeContent' => $analysisCodeContent,
            'analysisCodeAmp' => $analysisCodeAmp,
            'uniqueID' => $helper->generateUniqID()
        ];
        $result = $analysisCodeModel->addAnalysisCode($data);
    }

    if ($result) {
        $analysisCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Kayıt başarılı'
        ]);
    }
    else {
        $analysisCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Kayıt sırasında bir hata oluştu'
        ]);
    }
}
elseif ($action == "deleteAnalysisCode"){
    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $result = $analysisCodeModel->deleteAnalysisCode($languageID);

    $analysisCodeModel->beginTransaction();

    if ($result) {
        $analysisCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Silme işlemi başarılı'
        ]);
    }
    else {
        $analysisCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Silme işlemi sırasında bir hata oluştu'
        ]);
    }

}
elseif($action == "getCartConversionCode"){

    $languageID = $requestData['languageID'] ?? 1;
    $languageID = intval($languageID);

    $cartConversionCode = $cartConversionCodeModel->getCartConversionCode($languageID);

    echo json_encode([
        'status' => 'success',
        'cartConversionCode' => $cartConversionCode
    ]);
}
elseif($action == "saveCartConversionCode"){

    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $cartConversionCodeName = $requestData['cartConversionCodeName'] ?? null;
    $cartConversionCodeContent = $requestData['cartConversionCodeContent'] ?? null;
    //ikisi de boş olamaz
    if (empty($cartConversionCodeName) || empty($cartConversionCodeContent)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Alanlar boş olamaz'
        ]);
        exit();
    }

    $cartConversionCodeContent = htmlspecialchars($cartConversionCodeContent);

    //bu dilde kod var mı bakalım
    $cartConversionCode = $cartConversionCodeModel->getCartConversionCode($languageID);

    $cartConversionCodeModel->beginTransaction();

    if (!empty($cartConversionCode)) {
        //varsa güncelle
        $data = [
            'languageID' => $languageID,
            'cartConversionCodeName' => $cartConversionCodeName,
            'cartConversionCodeContent' => $cartConversionCodeContent
        ];
        $result = $cartConversionCodeModel->updateCartConversionCode($data);
    }
    else {
        //yoksa ekle
        $data = [
            'languageID' => $languageID,
            'cartConversionCodeName' => $cartConversionCodeName,
            'cartConversionCodeContent' => $cartConversionCodeContent,
            'uniqueID' => $helper->generateUniqID()
        ];
        $result = $cartConversionCodeModel->addCartConversionCode($data);
    }

    if ($result) {
        $cartConversionCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Kayıt başarılı'
        ]);
    }
    else {
        $cartConversionCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Kayıt sırasında bir hata oluştu'
        ]);
    }
}
elseif ($action == "deleteCartConversionCode"){
    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $result = $cartConversionCodeModel->deleteCartConversionCode($languageID);

    $cartConversionCodeModel->beginTransaction();

    if ($result) {
        $cartConversionCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Silme işlemi başarılı'
        ]);
    }
    else {
        $cartConversionCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Silme işlemi sırasında bir hata oluştu'
        ]);
    }

}
elseif ($action == "getAdConversionCode"){

    $languageID = $requestData['languageID'] ?? 1;
    $languageID = intval($languageID);

    $adConversionCode = $adConversionCodeModel->getAdConversionCode($languageID);

    echo json_encode([
        'status' => 'success',
        'adConversionCode' => $adConversionCode
    ]);
}
elseif($action == "saveAdConversionCode"){
    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $adConversionCodeName = $requestData['adConversionCodeName'] ?? null;
    $adConversionCodeHead = $requestData['adConversionCodeHead'] ?? null;
    $adConversionCodeContent = $requestData['adConversionCodeContent'] ?? null;
    //ikisi de boş olamaz
    if (empty($adConversionCodeName) || empty($adConversionCodeHead) || empty($adConversionCodeContent)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Alanlar boş olamaz'
        ]);
        exit();
    }

    $adConversionCodeHead = htmlspecialchars($adConversionCodeHead);
    $adConversionCodeContent = htmlspecialchars($adConversionCodeContent);

    //bu dilde kod var mı bakalım
    $adConversionCode = $adConversionCodeModel->getAdConversionCode($languageID);

    $adConversionCodeModel->beginTransaction();

    if (!empty($adConversionCode)) {
        //varsa güncelle
        $data = [
            'languageID' => $languageID,
            'adConversionCodeName' => $adConversionCodeName,
            'adConversionCodeHead' => $adConversionCodeHead,
            'adConversionCodeContent' => $adConversionCodeContent
        ];
        $result = $adConversionCodeModel->updateAdConversionCode($data);
    }
    else {
        //yoksa ekle
        $data = [
            'languageID' => $languageID,
            'adConversionCodeName' => $adConversionCodeName,
            'adConversionCodeHead' => $adConversionCodeHead,
            'adConversionCodeContent' => $adConversionCodeContent,
            'uniqueID' => $helper->generateUniqID()
        ];
        $result = $adConversionCodeModel->addAdConversionCode($data);
    }

    if ($result) {
        $adConversionCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Kayıt başarılı'
        ]);
    }
    else {
        $adConversionCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Kayıt sırasında bir hata oluştu'
        ]);
    }

}
elseif($action == "deleteAdConversionCode"){
    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $result = $adConversionCodeModel->deleteAdConversionCode($languageID);

    $adConversionCodeModel->beginTransaction();

    if ($result) {
        $adConversionCodeModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Silme işlemi başarılı'
        ]);
    }
    else {
        $adConversionCodeModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Silme işlemi sırasında bir hata oluştu'
        ]);
    }

}
elseif ($action == "getTagManager"){

    $languageID = $requestData['languageID'] ?? 1;
    $languageID = intval($languageID);

    $tagManager = $tagManagerModel->getTagManager($languageID);

    echo json_encode([
        'status' => 'success',
        'tagManager' => $tagManager
    ]);
}
elseif ($action == "saveTagManager"){

    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $tagManagerName = $requestData['tagManagerName'] ?? null;
    $tagManagerHead = $requestData['tagManagerHead'] ?? null;
    $tagManagerContent = $requestData['tagManagerContent'] ?? null;
    //ikisi de boş olamaz
    if (empty($tagManagerName) || empty($tagManagerHead) || empty($tagManagerContent)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Alanlar boş olamaz'
        ]);
        exit();
    }

    $tagManagerHead = htmlspecialchars($tagManagerHead);
    $tagManagerContent = htmlspecialchars($tagManagerContent);

    //bu dilde kod var mı bakalım
    $tagManager = $tagManagerModel->getTagManager($languageID);

    $tagManagerModel->beginTransaction();

    if (!empty($tagManager)) {
        //varsa güncelle
        $data = [
            'languageID' => $languageID,
            'tagManagerName' => $tagManagerName,
            'tagManagerHead' => $tagManagerHead,
            'tagManagerContent' => $tagManagerContent
        ];
        $result = $tagManagerModel->updateTagManager($data);
    }
    else {
        //yoksa ekle
        $data = [
            'languageID' => $languageID,
            'tagManagerName' => $tagManagerName,
            'tagManagerHead' => $tagManagerHead,
            'tagManagerContent' => $tagManagerContent,
            'uniqueID' => $helper->generateUniqID()
        ];
        $result = $tagManagerModel->addTagManager($data);
    }

    if ($result) {
        $tagManagerModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Kayıt başarılı'
        ]);
    }
    else {
        $tagManagerModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Kayıt sırasında bir hata oluştu'
        ]);
    }
}
elseif ($action == "deleteTagManager") {
    $languageID = $requestData['languageID'];
    //dil boş olamaz
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil boş olamaz'
        ]);
        exit();
    }

    $result = $tagManagerModel->deleteTagManager($languageID);

    $tagManagerModel->beginTransaction();

    if ($result) {
        $tagManagerModel->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Silme işlemi başarılı'
        ]);
    } else {
        $tagManagerModel->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Silme işlemi sırasında bir hata oluştu'
        ]);
    }
}
// Platform Tracking işlemleri
elseif ($action == "savePlatformTracking") {
    $platform = $requestData['platform'] ?? '';
    $config = $requestData['config'] ?? [];
    $status = intval($requestData['status'] ?? 0);
    $languageID = intval($requestData['languageID'] ?? 1);
    
    // Debug log
    error_log("PlatformTracking Save Debug - Platform: $platform, Status: $status, LanguageID: $languageID");
    error_log("PlatformTracking Save Debug - Config: " . json_encode($config));
    
    if (empty($platform)) {
        error_log("PlatformTracking Error: Platform boş");
        echo json_encode([
            'status' => 'error',
            'message' => 'Platform seçilmedi'
        ]);
        exit();
    }
    
    // Platform konfigürasyonunu ve statusu kaydet
    $result = $platformTrackingManager->savePlatformConfig($platform, $config, $languageID, $status);
    
    if ($result) {
        error_log("PlatformTracking Success: $platform kaydedildi");
        echo json_encode([
            'status' => 'success',
            'message' => 'Platform ayarları kaydedildi'
        ]);
    } else {
        error_log("PlatformTracking Error: $platform kaydedilemedi");
        echo json_encode([
            'status' => 'error',
            'message' => 'Platform ayarları kaydedilemedi'
        ]);
    }
    exit();
}

elseif ($action == "saveAllPlatforms") {
    $platforms = json_decode($requestData['platforms'] ?? '{}', true);
    $languageID = intval($requestData['languageID'] ?? 1);
    
    // Debug log
    error_log("SaveAllPlatforms Debug - LanguageID: $languageID");
    error_log("SaveAllPlatforms Debug - Platforms: " . json_encode($platforms));
    
    if (empty($platforms)) {
        error_log("SaveAllPlatforms Error: Platform verisi boş");
        echo json_encode([
            'status' => 'error',
            'message' => 'Platform verisi bulunamadı'
        ]);
        exit();
    }
    
    $successCount = 0;
    $totalCount = count($platforms);
    $errors = [];
    
    foreach ($platforms as $platform => $data) {
        $config = $data['config'] ?? [];
        $status = intval($data['status'] ?? 0);
        
        error_log("SaveAllPlatforms - Processing: $platform, Status: $status");
        
        if ($platformTrackingManager->savePlatformConfig($platform, $config, $languageID, $status)) {
            $successCount++;
            error_log("SaveAllPlatforms - Success: $platform");
        } else {
            $errors[] = $platform;
            error_log("SaveAllPlatforms - Failed: $platform");
        }
    }
    
    error_log("SaveAllPlatforms Result - Success: $successCount/$totalCount, Errors: " . implode(', ', $errors));
    
    if ($successCount === $totalCount) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Tüm platform ayarları kaydedildi'
        ]);
    } else {
        echo json_encode([
            'status' => 'partial',
            'message' => $successCount . '/' . $totalCount . ' platform kaydedildi',
            'errors' => $errors
        ]);
    }
    exit();
}

elseif ($action == "previewPlatformTracking") {
    $platform = $requestData['platform'] ?? '';
    $config = $requestData['config'] ?? [];
    $languageID = intval($requestData['languageID'] ?? 1);
    
    if (empty($platform)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Platform seçilmedi'
        ]);
        exit();
    }
    
    // Geçici olarak konfigürasyonu kaydet ve kodu oluştur
    $tempResult = $platformTrackingManager->savePlatformConfig($platform, $config, $languageID);
    
    if ($tempResult) {
        $code = $platformTrackingManager->generateHeadCodes($languageID);
        echo json_encode([
            'status' => 'success',
            'code' => $code
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Önizleme oluşturulamadı'
        ]);
    }
    exit();
}

elseif ($action == "previewAllPlatforms") {
    $languageID = intval($requestData['languageID'] ?? 1);
    
    $headCodes = $platformTrackingManager->generateHeadCodes($languageID);
    
    echo json_encode([
        'status' => 'success',
        'code' => $headCodes
    ]);
    exit();
}

elseif ($action == "generateConversionCode") {
    $platform = $requestData['platform'] ?? '';
    $eventType = $requestData['eventType'] ?? 'purchase';
    $eventData = $requestData['eventData'] ?? [];
    $languageID = intval($requestData['languageID'] ?? 1);
    
    if (empty($platform)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Platform seçilmedi'
        ]);
        exit();
    }
    
    $conversionCode = $platformTrackingManager->generateConversionCode($platform, $eventType, $eventData, $languageID);
    
    echo json_encode([
        'status' => 'success',
        'code' => $conversionCode
    ]);
    exit();
}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
}
exit();