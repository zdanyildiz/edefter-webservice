<?php

$documentRoot = str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
include_once MODEL . 'Admin/AdminPageType.php';
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

$pageTypeModel = new AdminPageType($db);

if ($action == "addPageType") {
    $pageTypeName = $requestData['pageTypeName'] ?? '';
    $pageTypePermission = $requestData['pageTypePermission'] ?? 0;
    $pageTypeView = $requestData['pageTypeView'] ?? 1;

    if (empty($pageTypeName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa Tip adı boş olamaz'
        ]);
        exit();
    }

    try {
        $pageTypeModel->beginTransaction();
        $pageTypeID = $pageTypeModel->createPageType($pageTypeName, $pageTypePermission, $pageTypeView);
        $pageTypeModel->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Page Type created successfully',
            'pageTypeID' => $pageTypeID
        ]);
    } catch (Exception $e) {
        $pageTypeModel->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa Tipi Oluştrulamadı: ' . $e->getMessage()
        ]);
    }

} elseif ($action == "updatePageType") {
    $pageTypeID = $requestData['pageTypeID'] ?? null;
    $pageTypeName = $requestData['pageTypeName'] ?? '';
    $pageTypePermission = $requestData['pageTypePermission'] ?? 0;
    $pageTypeView = $requestData['pageTypeView'] ?? 1;

    if (!$pageTypeID || empty($pageTypeName)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa tip id veya adı alanı boş olamaz'
        ]);
        exit();
    }

    try {
        $pageTypeModel->beginTransaction();
        $result = $pageTypeModel->updatePageType($pageTypeID, $pageTypeName, $pageTypePermission, $pageTypeView);
        $pageTypeModel->commit();

        if ($result > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Sayfa tip başarıyla güncellendi'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sayfa tip güncellenemedi'
            ]);
        }
    } catch (Exception $e) {
        $pageTypeModel->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa tip güncellenemedi: ' . $e->getMessage()
        ]);
    }

} elseif ($action == "deletePageType") {
    $pageTypeID = $requestData['pageTypeID'] ?? null;

    if (!$pageTypeID) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa tip ID alanı boş olamaz'
        ]);
        exit();
    }

    try {
        $pageTypeModel->beginTransaction();
        $result = $pageTypeModel->deletePageType($pageTypeID);
        $pageTypeModel->commit();

        if ($result > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Sayfa tip başarıyla silindi'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sayfa tip silinemedi'
            ]);
        }
    } catch (Exception $e) {
        $pageTypeModel->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa tip silinemedi: ' . $e->getMessage()
        ]);
    }

} elseif ($action == "getPageTypes") {
    $includeDeleted = $requestData['includeDeleted'] ?? false;
    $pageTypes = $pageTypeModel->getPageTypes($includeDeleted);

    echo json_encode([
        'status' => 'success',
        'data' => $pageTypes
    ]);

} elseif ($action == "getPageTypeById") {
    $pageTypeID = $requestData['pageTypeID'] ?? null;

    if (!$pageTypeID) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa tip ID alanı boş olamaz'
        ]);
        exit();
    }

    $pageType = $pageTypeModel->getPageTypeById($pageTypeID);

    if ($pageType) {
        echo json_encode([
            'status' => 'success',
            'data' => $pageType
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa tip bulunamadı'
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz işlem'
    ]);
}


