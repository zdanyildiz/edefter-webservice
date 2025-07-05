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
$languageID = $requestData["languageID"] ?? 1;
$referrer = $requestData["referrer"] ?? null;
$adminCasper = $adminSession->getAdminCasper();
$config = $adminCasper->getConfig();
$json = $config->Json;
$helper = $config->Helper;

include_once MODEL . 'Admin/AdminCompany.php';
$companyModel = new AdminCompany($db);

include_once MODEL . 'Admin/AdminSiteSettings.php';
$adminSiteSettings = new AdminSiteSettings($db, $languageID);

function executeWithTransaction($callback)
{
    global $companyModel, $adminSiteSettings, $languageID;
    try {
        $companyModel->beginTransaction();
        $result = $callback();
        if ($result > 0) {

            $checkSiteConfigVersion = $adminSiteSettings->getSiteConfigVersions($languageID);
            if ($checkSiteConfigVersion) {
                Log::adminWrite("Site konfigürasyon güncelleniyor","info");
                $adminSiteSettings->updateSiteConfigVersion($languageID);
            }
            else{
                Log::adminWrite("Site konfigürasyon ekleniyor","info");
                $adminSiteSettings->addSiteConfigVersion($languageID);
            }

            $companyModel->commit();
            return [
                'status' => 'success',
                'message' => 'İşlem başarılı'
            ];
        }
        else {
            $companyModel->rollback();
            return [
                'status' => 'error',
                'message' => 'İşlem başarısız'
            ];
        }
    }
    catch (Exception $e) {
        $companyModel->rollback();
        return [
            'status' => 'error',
            'message' => 'Hata oluştu: ' . $e->getMessage()
        ];
    }
}

switch ($action) {
    case 'getCompany':
        $companyID = $requestData["companyID"] ?? null;
        if(empty($companyID)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Company ID is required'
            ]);
            exit();
        }
        $companyData = $companyModel->getCompany($companyID);
        echo json_encode($companyData);
        break;
    case 'getCompanyByLanguageID':
        $languageID = $requestData["languageID"] ?? 1;
        $companyData = $companyModel->getCompany($languageID);
        echo json_encode($companyData);
        break;
    case 'addCompany':
    case 'updateCompany':
        $companyData = $requestData;
        $isUpdate = $action === 'updateCompany';
        if (!$isUpdate) {
            $companyUniqID = $helper->generateUniqID();
            $companyData["uniqueId"] = $companyUniqID;
        }

        $companyCoordinate = $requestData["companyCoordinate"] ?? null;
        if(!empty($companyCoordinate)){
            $companyCoordinate = explode(",", $companyCoordinate);
            $companyData["latitude"] = $companyCoordinate[0];
            $companyData["longitude"] = $companyCoordinate[1];
        }
        else{
            $companyData["latitude"] = "";
            $companyData["longitude"] = "";
        }

        $result = executeWithTransaction(function() use ($companyModel, $companyData, $isUpdate) {
            return $companyModel->saveCompany($companyData, $isUpdate);
        });

        echo json_encode($result);
        break;
    case 'deleteCompany':
        $companyID = $requestData["companyID"] ?? null;
        $companyID = intval($companyID);
        if ($companyID==0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Company ID is required'
            ]);
            exit();
        }
        $companyModel->beginTransaction();

        $result = $companyModel->deleteCompany($companyID);
        if($result>0) {
            $companyModel->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Firma Bilgileri Silindi'
            ]);
        }
        else{
            $companyModel->rollback();
            echo json_encode([
                'status' => 'error',
                'message' => 'Firma Bilgileri Silinemedi'
            ]);
        }
        break;
    case 'addBranch':
    case 'updateBranch':
        $branchData = $requestData;
        $isUpdate = $action === 'updateBranch';

        if (!$isUpdate) {
            $branchUniqID = $helper->generateUniqID();
            $branchData["uniqueId"] = $branchUniqID;
        }

        // Ana firmanın ID'sini kontrol edin
        $parentCompanyID = $requestData["parentCompanyID"] ?? null;
        if (empty($parentCompanyID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Parent Company ID is required'
            ]);
            exit();
        }

        $companyCoordinate = $requestData["companyCoordinate"] ?? null;
        if(!empty($companyCoordinate)){
            $companyCoordinate = explode(",", $companyCoordinate);
            $branchData["latitude"] = $companyCoordinate[0];
            $branchData["longitude"] = $companyCoordinate[1];
        }
        else{
            $branchData["latitude"] = "";
            $branchData["longitude"] = "";
        }

        $result = executeWithTransaction(function() use ($companyModel, $branchData, $isUpdate) {
            return $companyModel->saveBranch($branchData, $isUpdate);
        });

        echo json_encode($result);
        break;
    case 'addLogo':
        $languageID = $requestData["languageID"] ?? null;
        if(empty($languageID)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Language ID is required'
            ]);
            exit();
        }
        $logoText = $requestData["logoText"] ?? null;
        $imageID = $requestData["imageID"] ?? null;

        if(empty($logoText) || empty($imageID)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Logo text and image ID is required'
            ]);
            exit();
        }
        //dile göre logo yoksa ekleyelim varsa güncelleyelim
        $checkLogo = $companyModel->getCompanyLogo($languageID);
        $result = executeWithTransaction(function() use ($checkLogo, $companyModel, $languageID, $logoText, $imageID) {
            if (!empty($checkLogo)) {
                return $companyModel->updateCompanyLogo($languageID, $logoText, $imageID);
            } else {
                return $companyModel->addCompanyLogo($languageID, $logoText, $imageID);
            }
        });
        echo json_encode($result);
        break;
    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Action error'
        ]);
        break;
}