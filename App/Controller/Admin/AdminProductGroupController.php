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

include_once MODEL. 'Admin/AdminProductGroup.php';
$adminProductGroup = new AdminProductGroup($db);

switch ($action) {
    case 'getProductGroup':
        $groupID = $requestData['groupID'] ?? null;
        if (!isset($groupID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Group ID is required'
            ]);
            exit();
        }

        $productGroup = $adminProductGroup->getProductGroup($groupID);
        echo json_encode($productGroup);
        break;

    case 'getProductGroups':
        $productGroups = $adminProductGroup->getProductGroups();
        echo json_encode($productGroups);
        break;

    case 'addProductGroup':

        $productGroupName = $requestData['productGroupName'] ?? null;
        $productGroupTaxRate = $requestData['productGroupTaxRate'] ?? null;
        $productGroupDiscountRate = $requestData['productGroupDiscountRate'] ?? null;
        $urungrupkargosuresi = $requestData['productGroupProductCargoTime'] ?? null;


        if (!isset($productGroupName) || !isset($productGroupTaxRate) || !isset($productGroupDiscountRate) || !isset($urungrupkargosuresi)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Grup adı, kdv oranı, indirim oranı ve kargo süresi boş olamaz'
            ]);
            exit();
        }

        if (!is_numeric($productGroupTaxRate) || !is_numeric($productGroupDiscountRate) || !is_numeric($urungrupkargosuresi)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'KDV oranı, indirim oranı ve kargo süresi sayısal olmalıdır'
            ]);
            exit();
        }

        $productGroupDescription = $requestData['productGroupDescription'] ?? '';
        $productGroupProductDescription = $requestData['productGroupProductDescription'] ?? '';
        $productGroupProductShortDesc = $requestData['productGroupProductShortDesc'] ?? '';

        $productGroupUniqID = $helper->createPassword(20,2);
        $productGroupData = [
            'productGroupName' => $productGroupName,
            'productGroupDescription' => $productGroupDescription,
            'productGroupTaxRate' => $productGroupTaxRate,
            'productGroupDiscountRate' => $productGroupDiscountRate,
            'productGroupShowDiscountedPrice' => 0,
            'productGroupProductPriceLastDate' => date('Y-m-d H:i:s'),
            'productGroupInstallment' => 0,
            'productGroupProductDescription' => $productGroupProductDescription,
            'productGroupProductShortDesc' => $productGroupProductShortDesc,
            'productGroupProductCargoTime' => $urungrupkargosuresi,
            'productGroupFixedShippingCost' => 0,
            'productGroupIsDeleted' => 0,
            'productGroupUniqID' => $productGroupUniqID
        ];

        $productAddGroupResult = $adminProductGroup->addProductGroup($productGroupData);
        echo json_encode($productAddGroupResult);
        break;

    case 'updateProductGroup':

        $productGroupID = $requestData['productGroupID'] ?? 0;
        $productGroupName = $requestData['productGroupName'] ?? null;
        $productGroupTaxRate = $requestData['productGroupTaxRate'] ?? null;
        $productGroupDiscountRate = $requestData['productGroupDiscountRate'] ?? null;
        $urungrupkargosuresi = $requestData['productGroupProductCargoTime'] ?? null;


        if (empty($productGroupID) || !isset($productGroupName) || !isset($productGroupTaxRate) || !isset($productGroupDiscountRate) || !isset($urungrupkargosuresi)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Grup ID, grup adı, kdv oranı, indirim oranı ve kargo süresi boş olamaz'
            ]);
            exit();
        }

        if (!is_numeric($productGroupTaxRate) || !is_numeric($productGroupDiscountRate) || !is_numeric($urungrupkargosuresi)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'KDV oranı, indirim oranı ve kargo süresi sayısal olmalıdır'
            ]);
            exit();
        }

        $productGroupDescription = $requestData['productGroupDescription'] ?? '';
        $productGroupProductDescription = $requestData['productGroupProductDescription'] ?? '';
        $productGroupProductShortDesc = $requestData['productGroupProductShortDesc'] ?? '';

        $productGroupData = [
            'productGroupID' => $productGroupID,
            'productGroupName' => $productGroupName,
            'productGroupDescription' => $productGroupDescription,
            'productGroupTaxRate' => $productGroupTaxRate,
            'productGroupDiscountRate' => $productGroupDiscountRate,
            'productGroupShowDiscountedPrice' => 0,
            'productGroupProductPriceLastDate' => date('Y-m-d H:i:s'),
            'productGroupInstallment' => 0,
            'productGroupProductDescription' => $productGroupProductDescription,
            'productGroupProductShortDesc' => $productGroupProductShortDesc,
            'productGroupProductCargoTime' => $urungrupkargosuresi,
            'productGroupFixedShippingCost' => 0,
            'productGroupIsDeleted' => 0
        ];

        $productGroupResult = $adminProductGroup->updateProductGroup($productGroupData);
        echo json_encode($productGroupResult);
        break;

    case 'deleteProductGroup':
        $groupID = $requestData['groupID'] ?? null;
        if (!isset($groupID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Group ID boş olamaz'
            ]);
            exit();
        }

        $productGroup = $adminProductGroup->deleteProductGroup($groupID);
        echo json_encode($productGroup);
        break;

    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Action error'
        ]);
        break;
}