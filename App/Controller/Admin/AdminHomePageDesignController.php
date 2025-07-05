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


include_once MODEL . "Admin/AdminHomePage.php";

$adminHomePage = new AdminHomePage($db);

$response = [
    "status" => "error",
    "message" => "Geçersiz işlem."
];

$action = $requestData['action'] ?? null;
Log::adminWrite("AdminHomePageDesignController -> action: " . $action);
try {
    switch ($action) {
        case "addBlock":
            $type = $requestData['type'] ?? null;
            $content = $requestData['content'] ?? [];
            $languageID = intval($requestData['languageID'] ?? 1);

            if (!$type || empty($content)) {
                throw new Exception("Blok türü veya içerik eksik.");
            }

            $blockID = $adminHomePage->addBlock($type, $content, $languageID);

            if ($blockID) {
                $response = [
                    "status" => "success",
                    "message" => "Blok başarıyla eklendi.",
                    "blockID" => $blockID
                ];
            } else {
                throw new Exception("Blok eklenirken bir hata oluştu.");
            }
            break;

        case "updateBlock":
            $blockID = intval($requestData['blockID'] ?? 0);
            $content = $requestData['content'] ?? [];

            if (!$blockID || empty($content)) {
                throw new Exception("Blok ID veya içerik eksik.");
            }

            $updated = $adminHomePage->updateBlock($blockID, $content);

            if ($updated) {
                $response = [
                    "status" => "success",
                    "message" => "Blok başarıyla güncellendi."
                ];
            } else {
                throw new Exception("Blok güncellenirken bir hata oluştu.");
            }
            break;

        case "deleteBlock":
            $blockID = intval($requestData['blockID'] ?? 0);

            if (!$blockID) {
                throw new Exception("Blok ID eksik.");
            }

            $deleted = $adminHomePage->deleteBlock($blockID);

            if ($deleted) {
                $response = [
                    "status" => "success",
                    "message" => "Blok başarıyla silindi."
                ];
            } else {
                throw new Exception("Blok silinirken bir hata oluştu.");
            }
            break;

        case "reorderBlocks":
            $blockOrder = $requestData['blockOrder'] ?? [];

            if (empty($blockOrder)) {
                throw new Exception("Blok sıralama bilgisi eksik.");
            }

            $reordered = $adminHomePage->reorderBlocks($blockOrder);

            if ($reordered) {
                $response = [
                    "status" => "success",
                    "message" => "Bloklar başarıyla sıralandı."
                ];
            } else {
                throw new Exception("Bloklar sıralanırken bir hata oluştu.");
            }
            break;

        case "initializeDefaults":
            $language = $requestData['language'] ?? 'tr';
            $initialized = $adminHomePage->initializeDefaultBlocks($language);

            if ($initialized) {
                $response = [
                    "status" => "success",
                    "message" => "Varsayılan bloklar başarıyla oluşturuldu."
                ];
            } else {
                $response = [
                    "status" => "info",
                    "message" => "Bloklar zaten mevcut."
                ];
            }
            break;

        case "getBlock":
            $blockID = intval($requestData['blockID'] ?? 0);

            if (!$blockID) {
                throw new Exception("Blok ID eksik.");
            }

            $block = $adminHomePage->getBlockByID($blockID);

            if ($block) {
                $response = [
                    "status" => "success",
                    "data" => $block
                ];
            } else {
                throw new Exception("Blok bulunamadı.");
            }
            break;

        case 'getProductGroups':
            try {
                $groups = $adminHomePage->getProductGroups();
                if (empty($groups)) {
                    // Eğer gruplar yoksa bir hata yanıtı döndür ve çık
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Gruplar bulunamadı.'
                    ]);
                    exit; // Daha fazla işlem yapılmasını önlemek için çık
                }

                // Eğer gruplar varsa başarı yanıtını döndür
                echo json_encode([
                    'status' => 'success',
                    'data' => $groups
                ]);
                exit;
            } catch (Exception $e) {
                // Herhangi bir hata oluşursa, hata mesajını döndür
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
                exit;
            }

        case 'addProductGroup':
            $data = json_decode($requestData['data'], true);
            $result = $adminHomePage->addProductGroup($data['type'], $data['title'], $data['product_count'], $data['product_ids']);
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Grup başarıyla eklendi.' : 'Grup eklenirken hata oluştu.'
            ]);
            break;

        case 'updateProductGroup':
            $id = intval($requestData['id']);
            $title = $requestData['title'];
            $productCount = intval($requestData['product_count']);
            $productIds = $requestData['product_ids'];
            $result = $adminHomePage->updateProductGroup($id, $title, $productCount, $productIds);
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Grup başarıyla güncellendi.' : 'Grup güncellenirken hata oluştu.'
            ]);
            break;

        case 'deleteProductGroup':
            $id = intval($requestData['id']);
            $result = $adminHomePage->deleteProductGroup($id);
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Grup başarıyla silindi.' : 'Grup silinirken hata oluştu.'
            ]);
            break;
        case 'getProductGroup':
            $groupId = $_POST['group_id'] ?? null;
            if (!$groupId) {
                echo json_encode(['status' => 'error', 'message' => 'Grup ID gerekli.']);
                exit;
            }

            $group = $adminHomePage->getProductGroup($groupId);
            if (!$group) {
                echo json_encode(['status' => 'error', 'message' => 'Grup bulunamadı.']);
                exit;
            }

            echo json_encode(['status' => 'success', 'data' => $group]);
            exit;


        case 'initializeDefaultProductGroups':
            $result = $adminHomePage->initializeDefaultProductGroups();
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Varsayılan gruplar başarıyla eklendi.' : 'Gruplar zaten mevcut.'
            ]);
            break;

        default:
            throw new Exception("Geçersiz işlem.");
    }
} catch (Exception $e) {
    $response["message"] = $e->getMessage();
}

header("Content-Type: application/json");
echo json_encode($response);
