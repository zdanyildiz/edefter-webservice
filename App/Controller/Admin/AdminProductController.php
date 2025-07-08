<?php

// PhpSpreadsheet kullanarak dosyayı parse et
use \PhpOffice\PhpSpreadsheet\IOFactory;

$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var AdminSession $adminSession
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

include_once MODEL . 'Admin/AdminProduct.php';
$adminProductModel = new AdminProduct($db,$config);

include_once MODEL . 'Admin/AdminProductCategory.php';
$adminProductCategoryModel = new AdminProductCategory($db);

include_once MODEL . 'Admin/GeneralSettings.php';
$generalSettingsModel = new GeneralSettings($db);

// Yeni ProductTransfer Modelini dahil edin
include_once MODEL . 'Admin/AdminProductTransfer.php';
$productTransferModel = new AdminProductTransfer($db);

include_once MODEL . 'Admin/AdminBrand.php';
$adminBrandModel = new AdminBrand($db);

include_once MODEL . 'Admin/AdminPage.php';
$adminPageModel = new AdminPage($db);

include_once MODEL .'Admin/AdminImage.php';
$adminImageModel = new AdminImage($db);

include_once MODEL .'Admin/AdminProductVariant.php';
$adminProductVariantModel = new AdminProductVariant($db);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguageModel = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminSeo.php';
$adminSeoModel = new AdminSeo($db);

include_once MODEL . 'Admin/AdminCurrency.php';
$adminCurrencyModel = new AdminCurrency($db);

include_once MODEL . 'Admin/AdminPriceSettings.php';

// PhpSpreadsheet Autoload
require_once "$documentRoot/vendor/autoload.php";

if($action == "addProduct"){

    $pageName = $requestData["productName"] ?? null;
    $pageContent = $requestData["productContent"] ?? "";
    $pageOrder = $requestData["productOrder"] ?? 0;
    $pageLink = $requestData["productLink"] ?? null;
    $pageActive = $requestData["productActive"] ?? 0;

    $categoryID = $requestData["productCategoryID"] ?? 0;
    if(empty($categoryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Önce kategori seçiniz'
        ]);
        exit();
    }

    //isim ve bağlantı boş olamaz
    if($pageName == null || $pageLink == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Page name and link can not be empty'
        ]);
        exit();
    }
    //pageLink "/" karakteriyle başlamalı
    if(substr($pageLink,0,1) != "/"){
        $pageLink = "/" . $pageLink;
    }

    $productImages = $requestData['imageID'] ?? null;
    $productFiles = $requestData['fileID'] ?? null;
    $pageVideos = $requestData['pageVideoIDS'] ?? null;

    $createDate = date("Y-m-d H:i:s");
    $updateDate = $createDate;

    $pageUniqID = $helper->createPassword("20","2");

    $insertPageData = [
        'pageUniqID' => $pageUniqID,
        'pageCreateDate' => $createDate,
        'pageUpdateDate' => $updateDate,
        'pageType' => 7,
        'pageName' => $pageName,
        'pageContent' => $pageContent,
        'pageOrder' => $pageOrder,
        'pageLink' => $pageLink,
        'pageActive' => $pageActive,
        'pageDeleted' => 0,
        'pageHit' => 0
    ];

    include_once MODEL . 'Admin/AdminPage.php';
    $adminPageModel = new AdminPage($db);

    //ekleme yapmadan önce aynı kategoride aynı isimde ürün var mı kontrol edelim

    $checkPage = $adminPageModel->checkPage($requestData['productName'],$requestData['productCategoryID']);
    if($checkPage>0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu kategoride aynı isimde ürün var'
        ]);
        exit();
    }

    $adminPageModel->beginTransaction("updateProduct");

    ############### SAYFA EKLE ####################

    $insertPageResult = $adminPageModel->insertPage($insertPageData);

    if($insertPageResult['status'] == 'error'){
        $adminPageModel->rollback("insertProduct/insertPage");
        echo json_encode($insertPageResult);
        exit();
    }

    $pageID = $adminPageModel->getPageIDByUniqID($pageUniqID);

    ############### KATEGORİ EKLE ####################

    $pageCategoryInsertData = [
        'pageID' => $pageID,
        'categoryID' => $requestData['productCategoryID'],
    ];

    $insertPageCategoryResult = $adminPageModel->insertPageCategory($pageCategoryInsertData);

    if($insertPageCategoryResult['status'] == 'error'){
        $adminPageModel->rollback("insertProduct/insertPageCategory");
        echo json_encode($insertPageCategoryResult);
        exit();
    }

    ############### RESİM EKLE ####################

    if(!empty($productImages)){
        $pageImageInsertData = [
            'pageID' => $pageID,
            'imageIDs' => $productImages,
        ];

        $insertPageImageResult = $adminPageModel->insertPageImages($pageImageInsertData);

        if($insertPageImageResult['status'] == 'error'){
            $adminPageModel->rollback("insertProduct/insertPageImages");
            echo json_encode($insertPageImageResult);
            exit();
        }
    }


    ############### DOSYA EKLE ####################

    if(!empty($productFiles))
    {
        $insertFileUpdateData = [
            'pageID' => $pageID,
            'fileIDs' => $productFiles,
        ];

        $insertPageFileResult = $adminPageModel->insertPageFiles($insertFileUpdateData);

        if($insertPageFileResult['status'] == 'error'){
            $adminPageModel->rollback("insertProduct/insertPageFiles");
            echo json_encode($insertPageFileResult);
            exit();
        }
    }

    if(!empty($pageVideos)){
        foreach ($pageVideos as $pageVideo) {
            $insertPageVideoData = [
                'pageID' => $pageID,
                'videoID' => $pageVideo
            ];

            $insertPageVideoResult = $adminPageModel->insertPageVideos($insertPageVideoData);

            if(!$insertPageVideoResult){
                $adminPageModel->rollback("insertPage/insertPageVideos");
                echo json_encode($insertPageVideoResult);
                exit();
            }
        }
    }

    $pageGalleryID = $requestData["pageGalleryID"] ?? 0;
    if($pageGalleryID > 0){
        $addPageGallery = $adminPageModel->addPageGallery($pageID,$pageGalleryID);
    }

    $domain = $generalSettingsModel->getDomainByLanguageID($languageID);

    $link = "";

    include_once MODEL . 'Admin/AdminLanguage.php';
    $adminLanguageModel = new AdminLanguage($db);

    $languageCode = $adminLanguageModel->getLanguageCode($languageID);
    $languageCode = $helper->toLowerCase($languageCode);

    $link = $link . "/" . $languageCode;

    $categoryPath = '';
    $categoryHierarchy = $adminProductCategoryModel->getCategoryHierarchy($requestData['productCategoryID']);

    foreach ($categoryHierarchy as $category) {
        $categoryName = $category['categoryName'];
        $categoryPath .= '/' . $helper->createAdvancedSeoLink($categoryName,$languageCode);
    }

    $link = $link . $categoryPath;

    $link = $link . $requestData['productLink'];

    $seoImages = [];
    if (!empty($pageID)) {
        $productImages = $adminProductModel->getProductImages($pageID);

        if (!empty($productImages)) {
            $seoImages = getSeoImages($productImages, $config, $domain, imgRoot);
        }
    }

    if(!empty($seoImages)){
        //dizeye çevirelim
        $seoImages = implode(", ", $seoImages);
    }
    else{
        $seoImages = '';
    }

    $insertSeoData = [
        'seoUniqID' => $pageUniqID,
        'seoTitle' => $requestData['productSeoTitle'],
        'seoDescription' => $requestData['productSeoDescription'],
        'seoKeywords' => $requestData['productSeoKeywords'],
        'seoLink' => $link,
        'seoOriginalLink' => '',
        'seoImage' => $seoImages
    ];

    $insertSeoResult = $adminSeoModel->insertSeo($insertSeoData);

    if($insertSeoResult['status'] == 'error'){
        $adminPageModel->rollback("insertProduct/insertSeo");
        echo json_encode($insertSeoResult);
        exit();
    }

    $insertProductData = [
        'productID' => $pageID,
        'productSupplierID' => $requestData['productSupplierID'],
        'productBrandID' => $requestData['productBrandID'],
        'productModel' => $requestData['productModel'],
        'productGroupID' => $requestData['productGroupID'],
        'productDescription' => $requestData['productDescription'],
        'productShortDesc' => $requestData['productShortDesc'],
        'productCurrency' => $requestData['productCurrency'],
        'productShowOldPrice' => $requestData['productShowOldPrice'],
        'productInstallment' => $requestData['productInstallment'],
        'productTax' => $requestData['productTax'],
        'productDiscountRate' => $requestData['productDiscountRate'],
        'productSalesQuantity' => $requestData['productSalesQuantity'],
        'productQuantityUnitID' => $requestData['productQuantityUnitID'],
        'productMinimumQuantity' => $requestData['productMinimumQuantity'],
        'productMaximumQuantity' => $requestData['productMaximumQuantity'],
        'productCoefficient' => $requestData['productCoefficient'],
        'productBulkDiscount' => $requestData['productBulkDiscount'] ?? 0,
        'productPriceAsk' => $requestData['productPriceAsk'] ?? 0,
        'variantProperties' => $requestData['productVariants'],
        'productProperties' => $requestData['productProperties'],
        'productStockCode' => $requestData['productStockCode'][0],
        'productGTIN' => $requestData['productGTIN'][0],
        'productMPN' => $requestData['productMPN'][0],
        'productBarcode' => $requestData['productBarcode'][0],
        'productOEM' => $requestData['productOEM'][0],
        'productStock' => $requestData['productStock'][0],
        'productSalePrice' => $requestData['productSalePrice'][0],
        'productDiscountPrice' => $requestData['productDiscountPrice'][0],
        'productDealerPrice' => $requestData['productDealerPrice'][0],
        'productPurchasePrice' => $requestData['productPurchasePrice'][0],
        'productCreditCard' => $requestData['productCreditCard'] ?? 0,
        'productBankTransfer' => $requestData['productBankTransfer'] ?? 0,
        'productCashOnDelivery' => $requestData['productCashOnDelivery'] ?? 0,
        'productDesi' => $requestData['productDesi'],
        'productCargoTime' => $requestData['productCargoTime'],
        'productFixedCargoPrice' => $requestData['productFixedCargoPrice'],
        'productPriceLastDate' => $requestData['productPriceLastDate'],
        'productHomePage' => $requestData['productHomePage'] ?? 0,
        'productDayOpportunity' => $requestData['productDayOpportunity'] ?? 0,
        'productDiscounted' => $requestData['productDiscounted'] ?? 0,
        'productNew' => $requestData['productNew'] ?? 0,
        'productSameDayShipping' => $requestData['productSameDayShipping'] ?? 0,
        'productFreeShipping' => $requestData['productFreeShipping'] ?? 0,
        'productPreOrder' => $requestData['productPreOrder'] ?? 0
    ];

    $insertProductResult = $adminProductModel->insertProduct($insertProductData);

    if($insertProductResult['status'] == 'error'){
        $adminPageModel->rollback("insertProduct/insertProduct");
        echo json_encode($insertProductResult);
        exit();
    }

    $variantProperties = json_decode($requestData['productVariants'], true);
    if (!empty($variantProperties)) {
        foreach ($variantProperties as $variant) {
            if(empty($variant['variantProperties'][0]['attribute']['name']) || $variant['variantProperties'][0]['attribute']['value'] == ''){
                continue;
            }
            $variantData = [
                'variant_id' => $requestData['productID'],
                'variant_stock_code' => $variant['variantStockCode'],
                'variant_quantity' => $variant['variantQuantity'],
                'variant_selling_price' => $variant['variantSellingPrice'],
                'variant_image_ids' => !empty($variant['variantImageIDs']) ? implode(',', $variant['variantImageIDs']) : '',
                'attribute_name' => $variant['variantProperties'][0]['attribute']['name'], // Varyant özellikleri örneği
                'attribute_value' => $variant['variantProperties'][0]['attribute']['value']
            ];

            $insertVariantResult = $adminProductModel->addVariantProperty($variantData);
            if (!$insertVariantResult) {
                $adminPageModel->rollback("insertProduct/insertVariantProperties");
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Varyant özellikleri eklenirken bir hata oluştu.'
                ]);
                exit();
            }
        }
    }

    $productProperties = $requestData['productProperties'];
    if(!empty($productProperties)){

        include_once MODEL . 'Admin/AdminProductProperties.php';
        $adminProductPropertiesModel = new AdminProductProperties($db);

        $productProperties = json_decode($productProperties, true);
        if(!empty($productProperties)){

            foreach ($productProperties as $property) {
                $attribute = $property['attribute'];
                $propertyName = $attribute['name'];
                $propertyValue = $attribute['value'];

                $checkProductProperty = $adminProductPropertiesModel->checkProductProperty($propertyName, $propertyValue);

                if($checkProductProperty['status'] == 'error'){
                    $insertProductProperty = $adminProductPropertiesModel->addProductProperty($propertyName, $propertyValue);

                    if($insertProductProperty['status'] == 'error'){
                        $adminPageModel->rollback("insertProduct/insertProductProperties");
                        echo json_encode($insertProductProperty);
                        exit();
                    }
                }
            }
        }
    }

    $filePath = JSON_DIR . "Category/Pages/";
    //filePath altında kategori $requestData['productCategoryID']."-" ile başlayan bütün jsonları sil
    $files = glob($filePath . $requestData['productCategoryID']."-*");
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    $filePath = JSON_DIR . "Homepage/DiscountedProducts/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Homepage/HomepageProducts/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Homepage/NewProducts/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Homepage/SpecialOffers/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    deleteSearchJson();

    $adminPageModel->commit("insertProduct");

    echo json_encode([
        'status' => 'success',
        'message' => 'Yeni ürün Eklendi',
        'productID' => $pageID,
        'pageUniqID' => $pageUniqID
    ]);
}
elseif( $action == "updateProduct"){

    $productImages = $requestData['imageID'] ?? null;
    $productFiles = $requestData['fileID'] ?? null;
    $pageVideos = $requestData['pageVideoIDS'] ?? null;

    $updateDate = date("Y-m-d H:i:s");

    $updatePageData = [
        'pageUpdateDate' => $updateDate,
        'pageType' => 7,
        'pageID' => $requestData['productID'],
        'pageName' => $requestData['productName'],
        'pageContent' => $requestData['productContent'],
        'pageActive' => $requestData['productActive'],
        'pageLink' => $requestData['productLink'],
    ];

    include_once MODEL . 'Admin/AdminPage.php';
    $adminPageModel = new AdminPage($db);

    $checkPage = $adminPageModel->checkPageWithPageID($requestData['productID'],$requestData['productName'],$requestData['productCategoryID']);
    if($checkPage>0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu kategoride aynı isimde ürün var'
        ]);
        exit();
    }

    $adminPageModel->beginTransaction("updateProduct");

    $updatePageResult = $adminPageModel->updatePage($updatePageData);

    if($updatePageResult['status'] == 'error'){
        $adminPageModel->rollback("updateProduct");
        echo json_encode($updatePageResult);
        exit();
    }

    $adminPageModel->deletePageCategory(['pageID' => $requestData['productID']]);

    $pageCategoryInsertData = [
        'pageID' => $requestData['productID'],
        'categoryID' => $requestData['productCategoryID'],
    ];

    $insertPageCategoryResult = $adminPageModel->insertPageCategory($pageCategoryInsertData);

    if($insertPageCategoryResult['status'] == 'error'){
        $adminPageModel->rollback("updateProduct");
        echo json_encode($insertPageCategoryResult);
        exit();
    }

    $deletePageImagesResult = $adminPageModel->deletePageImages(['pageID' => $requestData['productID']]);

    if(!empty($productImages)){
        $pageImageInsertData = [
            'pageID' => $requestData['productID'],
            'imageIDs' => $productImages,
        ];

        $insertPageImageResult = $adminPageModel->insertPageImages($pageImageInsertData);

        if($insertPageImageResult['status'] == 'error'){
            $adminPageModel->rollback("updateProduct");
            echo json_encode($insertPageImageResult);
            exit();
        }
    }

    $deletePageFilesResult = $adminPageModel->deletePageFiles(['pageID' => $requestData['productID']]);

    if(!empty($productFiles))
    {
        $deleteFileUpdateData = [
            'pageID' => $requestData['productID'],
            'fileIDs' => $productFiles,
        ];

        $deletePageFileResult = $adminPageModel->insertPageFiles($deleteFileUpdateData);

        if($deletePageFileResult['status'] == 'error'){
            $adminPageModel->rollback("updateProduct");
            echo json_encode($deletePageFileResult);
            exit();
        }
    }

    $deletePageVideosResult = $adminPageModel->deletePageVideos(['pageID' => $requestData['productID']]);
    if(!empty($pageVideos)){
        foreach ($pageVideos as $pageVideo) {
            $insertPageVideoData = [
                'pageID' => $requestData['productID'],
                'videoID' => $pageVideo
            ];

            $insertPageVideoResult = $adminPageModel->insertPageVideos($insertPageVideoData);

            if(!$insertPageVideoResult){
                $adminPageModel->rollback("insertPage/insertPageVideos");
                echo json_encode($insertPageVideoResult);
                exit();
            }
        }
    }

    $deletePageGalleryResult = $adminPageModel->deletePageGallery(["pageID"=>$requestData['productID']]);
    $pageGalleryID = $requestData["pageGalleryID"] ?? 0;
    if($pageGalleryID > 0){
        $addPageGallery = $adminPageModel->addPageGallery($requestData['productID'],$pageGalleryID);
    }

    $page = $adminPageModel->getPageUniqIDByID($requestData['productID']);

    if(!empty($page)){
        $pageUniqID = $page;
    }
    else{
        $adminPageModel->rollback("updateProduct");
        echo json_encode([
            'status' => 'error',
            'message' => 'Page not found'
        ]);
        exit();
    }

    $domain = $generalSettingsModel->getDomainByLanguageID($languageID);

    $link = "";

    include_once MODEL . 'Admin/AdminLanguage.php';
    $adminLanguageModel = new AdminLanguage($db);

    $languageCode = $adminLanguageModel->getLanguageCode($languageID);
    $languageCode = $helper->toLowerCase($languageCode);

    $link = $link . "/" . $languageCode;

    $categoryPath = '';
    $categoryHierarchy = $adminProductCategoryModel->getCategoryHierarchy($requestData['productCategoryID']);

    foreach ($categoryHierarchy as $category) {
        $categoryName = $category['categoryName'];
        $categoryPath .= '/' . $helper->createAdvancedSeoLink($categoryName,$languageCode);
    }

    $link = $link . $categoryPath;

    if(substr($requestData['productLink'],0,1) != "/"){
        $requestData['productLink'] = "/" . $requestData['productLink'];
    }

    $link = $link . $requestData['productLink'];

    $productSeoOriginalLink = '';
    $getSeo = $adminSeoModel->getSeoByUniqId($pageUniqID);
    if(!empty($getSeo)){
        $productSeoOriginalLink = $getSeo['seoLink'] ?? $productSeoOriginalLink;
    }

    if ($link !== $productSeoOriginalLink) {
        include_once MODEL . 'Admin/AdminBanner.php';
        $adminBannerModel = new AdminBanner($db);

        $updateBannerLink = [
            'newBannerlink' => $link,
            'oldBannerlink' => $productSeoOriginalLink
        ];

        $updateBannerLinkResult = $adminBannerModel->updateBannerLinkByLink($updateBannerLink);

        include_once MODEL . 'Admin/AdminMenu.php';
        $adminMenuModel = new AdminMenu($db);

        $updateMenuLink = [
            'newMenulink' => $link,
            'oldMenulink' => $productSeoOriginalLink
        ];

        $updateMenuLinkResult = $adminMenuModel->updateMenuLinkByLink($updateMenuLink);

        $updateMenuLinkResult = $adminMenuModel->updateMenuLinkByMenuOrijinalUniqID($pageUniqID, $link);
    }

    $seoImages = [];
    if (!empty($requestData['productID'])) {
        $productImages = $adminProductModel->getProductImages($requestData['productID']);

        if (!empty($productImages)) {
            $seoImages = getSeoImages($productImages, $config, $domain, imgRoot);
        }
    }

    if(!empty($seoImages)){
        //dizeye çevirelim
        $seoImages = implode(", ", $seoImages);
    }
    else{
        $seoImages = '';
    }

    $updateSeoData = [
        'seoUniqID' => $pageUniqID,
        'seoTitle' => $requestData['productSeoTitle'],
        'seoDescription' => $requestData['productSeoDescription'],
        'seoKeywords' => $requestData['productSeoKeywords'],
        'seoLink' => $link,
        'seoImage' => $seoImages
    ];

    if($link !== $productSeoOriginalLink){
        $updateSeoData['seoOriginalLink'] = $productSeoOriginalLink;
    }

    $updateSeoResult = $adminSeoModel->updateSeo($updateSeoData);

    $updateProductData = [
        'productID' => $requestData['productID'],
        'productSupplierID' => $requestData['productSupplierID'],
        'productBrandID' => $requestData['productBrandID'],
        'productModel' => $requestData['productModel'],
        'productGroupID' => $requestData['productGroupID'],
        'productDescription' => $requestData['productDescription'],
        'productShortDesc' => $requestData['productShortDesc'],
        'productCurrency' => $requestData['productCurrency'],
        'productShowOldPrice' => $requestData['productShowOldPrice'],
        'productInstallment' => $requestData['productInstallment'],
        'productTax' => $requestData['productTax'],
        'productDiscountRate' => $requestData['productDiscountRate'],
        'productSalesQuantity' => $requestData['productSalesQuantity'],
        'productQuantityUnitID' => $requestData['productQuantityUnitID'],
        'productMinimumQuantity' => $requestData['productMinimumQuantity'],
        'productMaximumQuantity' => $requestData['productMaximumQuantity'],
        'productCoefficient' => $requestData['productCoefficient'],
        'productBulkDiscount' => $requestData['productBulkDiscount'] ?? 0,
        'productPriceAsk' => $requestData['productPriceAsk'] ?? 0,
        'variantProperties' => $requestData['productVariants'],
        'productProperties' => $requestData['productProperties'],
        'productStockCode' => $requestData['productStockCode'][0],
        'productGTIN' => $requestData['productGTIN'][0],
        'productMPN' => $requestData['productMPN'][0],
        'productBarcode' => $requestData['productBarcode'][0],
        'productOEM' => $requestData['productOEM'][0],
        'productStock' => $requestData['productStock'][0],
        'productSalePrice' => $requestData['productSalePrice'][0],
        'productDiscountPrice' => $requestData['productDiscountPrice'][0],
        'productDealerPrice' => $requestData['productDealerPrice'][0],
        'productPurchasePrice' => $requestData['productPurchasePrice'][0],
        'productCreditCard' => $requestData['productCreditCard'] ?? 0,
        'productBankTransfer' => $requestData['productBankTransfer'] ?? 0,
        'productCashOnDelivery' => $requestData['productCashOnDelivery'] ?? 0,
        'productDesi' => $requestData['productDesi'],
        'productCargoTime' => $requestData['productCargoTime'],
        'productFixedCargoPrice' => $requestData['productFixedCargoPrice'],
        'productPriceLastDate' => $requestData['productPriceLastDate'],
        'productHomePage' => $requestData['productHomePage'] ?? 0,
        'productDayOpportunity' => $requestData['productDayOpportunity'] ?? 0,
        'productDiscounted' => $requestData['productDiscounted'] ?? 0,
        'productNew' => $requestData['productNew'] ?? 0,
        'productSameDayShipping' => $requestData['productSameDayShipping'] ?? 0,
        'productFreeShipping' => $requestData['productFreeShipping'] ?? 0,
        'productPreOrder' => $requestData['productPreOrder'] ?? 0
    ];
    //print_r($requestData['productVariants']);exit;
    $updateProduct = $adminProductModel->updateProduct($updateProductData);

    if($updateProduct['status'] == 'error'){
        $adminPageModel->rollback("updateProduct");
        echo json_encode($updateProduct);
        exit();
    }

    // Yeni varyantları ekleyelim.
    $variantProperties = json_decode($requestData['productVariants'], true);

    $deleteVariantResult = $adminProductModel->deleteVariantProperty($requestData['productID']);

    if (!empty($variantProperties)) {
        foreach ($variantProperties as $variant) {
            foreach ($variant['variantProperties'] as $property) {
                if(empty($property['attribute']['name']) || empty($property['attribute']['value'])){
                    continue;
                }

                $variantData = [
                    'variant_id' => $requestData['productID'],
                    'variant_stock_code' => $variant['variantStockCode'],
                    'variant_quantity' => $variant['variantQuantity'],
                    'variant_selling_price' => $variant['variantSellingPrice'],
                    'variant_image_ids' => !empty($variant['variantImageIDs']) ? implode(',', $variant['variantImageIDs']) : '',
                    'attribute_name' => $property['attribute']['name'], // Varyant özellikleri örneği
                    'attribute_value' => $property['attribute']['value']
                ];

                $productProperties = $requestData['productProperties'];
                if(!empty($productProperties)){

                    include_once MODEL . 'Admin/AdminProductProperties.php';
                    $adminProductPropertiesModel = new AdminProductProperties($db);

                    $productProperties = json_decode($productProperties, true);
                    if(!empty($productProperties)){

                        foreach ($productProperties as $property) {
                            $attribute = $property['attribute'];
                            $propertyName = $attribute['name'];
                            $propertyValue = $attribute['value'];

                            $checkProductProperty = $adminProductPropertiesModel->checkProductProperty($propertyName, $propertyValue);

                            if($checkProductProperty['status'] == 'error'){
                                $insertProductProperty = $adminProductPropertiesModel->addProductProperty($propertyName, $propertyValue);

                                if($insertProductProperty['status'] == 'error'){
                                    $adminPageModel->rollback("insertProduct/insertProductProperties");
                                    echo json_encode($insertProductProperty);
                                    exit();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    deleteJsonByProductId($requestData['productID']);

    deleteJsonByProductUniqID($pageUniqID);

    deleteJsonByCategoryId($requestData['productCategoryID']);

    deleteJsonByLanguageID($languageID);

    deleteSearchJson();

    $adminPageModel->commit("updateProduct");

    echo json_encode([
        'status' => 'success',
        'message' => 'Ürün Güncellendi',
        'productID' => $requestData['productID'],
        'pageUniqID' => $pageUniqID
    ]);

}
elseif ($action == "deleteProduct") {
    $productID = $requestData['productID'] ?? null;
    if (empty($productID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product ID is required'
        ]);
        exit();
    }

    $product = $adminProductModel->getProduct($productID);
    if(empty($product)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Product not found'
        ]);
        exit();
    }

    $productCategoryID = $product['productCategoryID'];
    $productUniqID = $product['productUniqueID'];


    $adminProductModel->beginTransaction();

    $deletePageResult = $adminPageModel->deletePage($productID);
    if(!$deletePageResult){
        $adminProductModel->rollback("deletePage");
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa silinemedi'
        ]);
        exit();
    }

    $deletePageCategoryListResult = $adminPageModel->deletePageCategoryList($productID);
    if(!$deletePageCategoryListResult){
        $adminProductModel->rollback("deletePage/deletePageCategoryList");
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa silinemedi'
        ]);
        exit();
    }

    $deletePageImagesResult = $adminPageModel->deletePageImages(['pageID' => $productID]);

    $deletePageFilesResult = $adminPageModel->deletePageFiles(['pageID' => $productID]);

    $deletePageVideosResult = $adminPageModel->deletePageVideos(['pageID' => $productID]);

    $deletePageGalleryResult = $adminPageModel->deletePageGallery(["pageID"=>$productID]);

    $deletePageSeoResult = $adminSeoModel->deleteSeo($productUniqID);
    if(!$deletePageSeoResult){
        $adminProductModel->rollback("deletePage/deletePageSeo");
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa seo silinemedi'
        ]);
        exit();
    }

    $result = $adminProductModel->deleteProduct($productID);
    if ($result){
        $adminProductModel->commit();

        deleteJsonByProductId($productID);

        deleteJsonByCategoryId($productCategoryID);

        deleteJsonByLanguageID($languageID);

        deleteSearchJson();

        deleteJsonForPage($productUniqID);

        echo json_encode([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
        exit();
    }
    $adminProductModel->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Product delete failed'
    ]);
    exit();
}
elseif( $action == "productSearch"){

    $searchText = $requestData["searchText"] ?? null;
    $languageID = $requestData["languageID"] ?? 1;
    $limit = $requestData["limit"] ?? 20;
    $categoryID = $requestData["categoryID"] ?? null;

    if($searchText == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text is empty'
        ]);
        exit();
    }

    $searchParams = [
        "q" => $searchText,
        "languageID" => $languageID
    ];

    if($categoryID != null){
        $searchParams["categoryID"] = $categoryID;
    }

    $searchResults = $adminProductModel->productSearch($searchParams);

    if(!empty($searchResults)){

        $searchResultProductIDs = $searchResults["searchResultProductIDs"];
        $searchResultProducts = [];

        foreach ($searchResultProductIDs as $searchResultId) {
            $searchResultProducts[] = $adminProductModel->getProductByID($searchResultId);
        }

        $searchResults["searchResultProducts"] = $searchResultProducts;
    }
    else{
        $searchResults = [];
    }

    echo json_encode($searchResults);
    exit();

}
elseif( $action == "getProductCategories"){

    $languageID = $requestData["languageID"] ?? 1;

    $productCategories = $adminProductCategoryModel->getProductCategories($languageID);

    if(empty($productCategories)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Product categories not found'
        ]);
        exit();
    }
    else{
        $productCategories = [
            'status' => 'success',
            'message' => 'Product categories found',
            'productCategories' => $productCategories
        ];
    }

    echo json_encode($productCategories);
    exit();

    }
elseif( $action == "getProductCategoriesWithInfo"){

    $languageID = $requestData["languageID"] ?? 1;

    $productCategories = $adminProductCategoryModel->getProductCategories($languageID);

    if(empty($productCategories)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Product categories not found'
        ]);
        exit();
    }
    else{
        //getProductCategories sadece üst kategorileri alıyor, aldığımız kategorinin varsa alt kategori sayısını alalım
        foreach ($productCategories as $key => $category) {
            $productCategories[$key]['subCategoryCount'] = $adminProductCategoryModel->getSubCategoryCount($category['productCategoryID']);
            $productCategories[$key]['productCount'] = $adminProductCategoryModel->getCategoryProductTotalByCategoryID($category['productCategoryID']);
        }

        $productCategories = [
            'status' => 'success',
            'message' => 'Product categories found',
            'productCategories' => $productCategories
        ];
    }

    echo json_encode($productCategories);
    exit();

}
elseif( $action == "getSubCategories"){
    $categoryID = $requestData["categoryID"] ?? null;

    if($categoryID == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category ID is empty'
        ]);
        exit();
    }

    $subCategories = $adminProductCategoryModel->getSubCategories($categoryID);

    if(empty($subCategories)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sub categories not found'
        ]);
        exit();
    }
    else{
        $subCategories = [
            'status' => 'success',
            'message' => 'Sub categories found',
            'subCategories' => $subCategories
        ];
    }

    echo json_encode($subCategories);
    exit();


}
elseif( $action == "getSubCategoriesWithInfo"){
    $categoryID = $requestData["categoryID"] ?? null;

    if($categoryID == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category ID is empty'
        ]);
        exit();
    }

    $subCategories = $adminProductCategoryModel->getSubCategories($categoryID);

    if(empty($subCategories)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sub categories not found'
        ]);
        exit();
    }
    else{
        //getSubCategories sadece üst kategorileri alıyor, aldığımız kategorinin varsa alt kategori sayısını alalım
        foreach ($subCategories as $key => $category) {
            $subCategories[$key]['subCategoryCount'] = $adminProductCategoryModel->getSubCategoryCount($category['productCategoryID']);
            $subCategories[$key]['productCount'] = $adminProductCategoryModel->getCategoryProductTotalByCategoryID($category['productCategoryID']);
        }

        $subCategories = [
            'status' => 'success',
            'message' => 'Sub categories found',
            'subCategories' => $subCategories
        ];
    }

    echo json_encode($subCategories);
    exit();


}
elseif($action == "getProductsByCategoryID"){
    $categoryID = $requestData["categoryID"] ?? null;

    if($categoryID == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category ID is empty'
        ]);
        exit();
    }

    $categoryProducts = $adminProductModel->getProductsByCategoryID($categoryID);

    if(empty($categoryProducts)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Products not found'
        ]);
    }
    else{
        foreach($categoryProducts as $key => $value){
            $categoryProducts[$key] = $adminProductModel->getProduct($value['productID']);
        }
        echo json_encode($products = [
            'status' => 'success',
            'message' => 'Products found',
            'products' => $categoryProducts
        ]);
    }

    exit();


}
elseif( $action == "searchProduct"){

    $searchText = $requestData["searchText"] ?? null;
    $languageID = $requestData["languageID"] ?? 1;
    $categoryID = $requestData["categoryID"] ?? null;

    $limit = $requestData["limit"] ?? 20;

    if($searchText == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text is empty'
        ]);
        exit();
    }

    $searchParams = [
        "q" => $searchText,
        "languageID" => $languageID
    ];

    if($categoryID != null){
        $searchParams["categoryID"] = $categoryID;
    }

    $searchResults = $adminProductModel->searchProduct($searchParams);

    if(!empty($searchResults)){

        echo json_encode([
            'status' => 'success',
            'message' => 'Search results found',
            'searchResults' => $searchResults
        ]);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'Search results not found'
        ]);
    }
    exit();

}
elseif( $action == "getProductModels"){

    $searchText = $requestData["searchText"] ?? null;

    if($searchText == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text is empty'
        ]);
        exit();
    }

    $getModels = $adminProductModel->getProductModels($searchText);

    if(empty($getModels)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Models not found'
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'success',
            'message' => 'Models found',
            'models' => $getModels
        ]);
    }
}
elseif( $action == "getProductCategoryWithInfoBySearch"){

    $searchText = $requestData["searchText"] ?? null;
    $languageID = $requestData["languageID"] ?? 1;

    if($searchText == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text is empty'
        ]);
        exit();
    }

    $getCategory = $adminProductCategoryModel->getProductCategoryBySearch($searchText,$languageID);

    if(empty($getCategory)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category not found'
        ]);
        exit();
    }
    else{

        foreach ($getCategory as $key => $category) {
            $getCategory[$key]['subCategoryCount'] = $adminProductCategoryModel->getSubCategoryCount($category['productCategoryID'] ?? 0);
            $getCategory[$key]['productCount'] = $adminProductCategoryModel->getCategoryProductTotalByCategoryID($category['productCategoryID'] ?? 0);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Category found',
            'productCategories' => $getCategory
        ]);
    }


}
elseif( $action == "addProductCategory" || $action == "updateProductCategory"){

    $updateDate = date("Y-m-d H:i:s");
    $categoryID = $requestData["productCategoryID"];
    $productCategoryTopCategoryID = $requestData["productCategoryTopCategoryID"] ?? 0;
    $categoryLayer = $productCategoryTopCategoryID>0 ? 1 : 0;
    $categoryName = $requestData["productCategoryName"];
    $categoryImageID = $requestData["imageID"] ?? null;
    $productCategoryContent = $requestData["productCategoryContent"];
    $categoryLink = $requestData["productCategoryLink"];
    $categoryOrder = $requestData["productCategorySorting"] ?? 0;
    $productCategorySorting = $requestData["productCategorySorting"];
    $productCategoryType = $requestData["productCategoryType"];
    $productCategoryHomePage = $requestData["productCategoryHomePage"] ?? 0;
    $categoryActive = $requestData["productCategoryActive"];
    $categoryDeleted = 0;
    $googleCategory = $requestData["googleCategory"] ?? 0;

    if(!empty($categoryImageID)){
        $categoryImageID = $categoryImageID[0];
    }
    else{
        $categoryImageID = 0;
    }

    $productCategorySeoTitle = $requestData["productCategorySeoTitle"];
    $productCategorySeoDescription = $requestData["productCategorySeoDescription"];
    $productCategorySeoKeywords = $requestData["productCategorySeoKeywords"];

    //action update ise kategoriid boş olamaz
    if($action == "updateProductCategory" && empty($categoryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category ID is empty'
        ]);
        exit();
    }


    if(empty($categoryName) || empty($categoryLink) || empty($productCategorySeoTitle) || empty($productCategorySeoDescription) || empty($productCategorySeoKeywords)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category name, category link, seo title, seo description, seo keywords can not be empty'
        ]);
        exit();
    }

    $updateData = [
        'updateDate' => $updateDate,
        'categoryID' => $categoryID,
        'topCategoryID' => $productCategoryTopCategoryID,
        'categoryLayer' => $categoryLayer,
        'categoryName' => $categoryName,
        'categoryImageID' => $categoryImageID,
        'categoryContent' => $productCategoryContent,
        'categoryLink' => $categoryLink,
        'categoryOrder' => $categoryOrder,
        'categorySorting' => $productCategorySorting,
        'categoryType' => $productCategoryType,
        'categoryHomePage' => $productCategoryHomePage,
        'categoryActive' => $categoryActive,
        'categoryDeleted' => $categoryDeleted
    ];

    if($action == "addProductCategory"){
        $createDate = $updateDate;
        $languageID = $requestData["languageID"] ?? 1;
        $categoryUniqID = $helper->createPassword("20","2");

        $insertData = [
            'languageID' => $languageID,
            'createDate' => $createDate,
            'updateDate' => $updateDate,
            'topCategoryID' => $productCategoryTopCategoryID,
            'categoryLayer' => $categoryLayer,
            'categoryName' => $categoryName,
            'categoryImageID' => $categoryImageID,
            'categoryContent' => $productCategoryContent,
            'categoryLink' => $categoryLink,
            'categoryOrder' => $categoryOrder,
            'categorySorting' => $productCategorySorting,
            'categoryType' => $productCategoryType,
            'categoryHomePage' => $productCategoryHomePage,
            'categoryActive' => $categoryActive,
            'categoryDeleted' => $categoryDeleted,
            'categoryUniqID' => $categoryUniqID,
        ];
    }

    $adminProductCategoryModel->beginTransaction("$action");

    if($action == "addProductCategory"){

        $insertResult = $adminProductCategoryModel->insertProductCategory($insertData);

        if($insertResult['status'] == 'error'){
            $adminProductCategoryModel->rollback("$action / kategori ekle");
            echo json_encode($insertResult);
            exit();
        }

        $categoryID = $insertResult['categoryID'];
    }
    else{

        $adminProductCategoryModel->deleteGoogleCategory($categoryID);
        $updateProductCategoryResult = $adminProductCategoryModel->updateProductCategory($updateData);
        Log::adminWrite('updateProductCategoryResult: '. json_encode($updateProductCategoryResult,JSON_PRETTY_PRINT),"info");
        if($updateProductCategoryResult['status'] == 'error'){
            $adminProductCategoryModel->rollback("$action / kategori güncelle");
            echo json_encode($updateProductCategoryResult);
            exit();
        }

        $categoryUniqID = $adminProductCategoryModel->getCategoryUniqID($categoryID);
    }

    $domain = $generalSettingsModel->getDomainByLanguageID($languageID);

    $link = "";

    include_once MODEL . 'Admin/AdminLanguage.php';
    $adminLanguageModel = new AdminLanguage($db);

    $languageCode = $adminLanguageModel->getLanguageCode($languageID);
    $languageCode = $helper->toLowerCase($languageCode);

    $link = $link . "/" . $languageCode;

    $categoryHierarchy = $adminProductCategoryModel->getCategoryHierarchy($categoryID);

    if (count($categoryHierarchy) > 1) {
        // Birden fazla kategori varsa (yani üst kategoriler varsa)
        $hierarchyCategoryPath = '';
        // Son kategoriyi hariç tut
        for ($i = 0; $i < count($categoryHierarchy) - 1; $i++) {
            $hierarchyCategoryLink = $categoryHierarchy[$i]['categoryLink'];
            if(!empty($hierarchyCategoryLink)){
                $hierarchyCategoryPath .= $hierarchyCategoryLink;
            }
            else{
                $hierarchyCategoryName = $categoryHierarchy[$i]['categoryName'];
                $hierarchyCategoryPath .= '/' . $helper->createAdvancedSeoLink($hierarchyCategoryName,$languageCode);
            }

        }
        $link .= $hierarchyCategoryPath;
    }

    // Son olarak, mevcut kategorinin linkini ekle
    $link .= $categoryLink;

    $seoImage = '';
    if (!empty($categoryID)) {

        $categoryImages = $adminProductCategoryModel->getCategoryImages($categoryID);

        if (!empty($categoryImages)) {
            $seoImage = $config->http.$domain.imgRoot.$categoryImages['imagePath'];
        }
    }


    $categorySeoOriginalLink = '';

    if( $action == "updateProductCategory") {

        //die($categoryUniqID);
        $getSeo = $adminSeoModel->getSeoByUniqId($categoryUniqID);

        if (!empty($getSeo)) {
            $categorySeoOriginalLink = $getSeo['seoLink'] ?? $categorySeoOriginalLink;
        }
        //die("$link | $categorySeoOriginalLink");

        if ($link !== $categorySeoOriginalLink && $categorySeoOriginalLink !== '') {

            include_once MODEL . 'Admin/AdminBanner.php';
            $adminBannerModel = new AdminBanner($db);

            $updateBannerLink = [
                'newBannerlink' => $link,
                'oldBannerlink' => $categorySeoOriginalLink
            ];

            $updateBannerLinkResult = $adminBannerModel->updateBannerLinkByLink($updateBannerLink);
            Log::adminWrite('updateBannerLinkResult: '. json_encode($updateBannerLinkResult,JSON_PRETTY_PRINT),'info');

            $updateBannerLink = [
                'newBannerlink' => $link,
                'oldBannerlink' => $config->http.$domain.$categorySeoOriginalLink
            ];

            $updateBannerLinkResult = $adminBannerModel->updateBannerLinkByLink($updateBannerLink);
            Log::adminWrite('updateBannerLinkResult: '. json_encode($updateBannerLinkResult,JSON_PRETTY_PRINT),'info');

            include_once MODEL . 'Admin/AdminMenu.php';
            $adminMenuModel = new AdminMenu($db);

            $updateMenuLink = [
                'newMenulink' => $link,
                'oldMenulink' => $categorySeoOriginalLink
            ];

            $updateMenuLinkResult = $adminMenuModel->updateMenuLinkByLink($updateMenuLink);
            Log::adminWrite('updateMenuLinkResult: '. json_encode($updateMenuLinkResult,JSON_PRETTY_PRINT),'info');

            $updateDateMenuLinkResult = $adminMenuModel->updateMenuLinkByMenuOrijinalUniqID($categoryUniqID,$link);
            Log::adminWrite('updateDateMenuLinkResult: '. json_encode($updateDateMenuLinkResult,JSON_PRETTY_PRINT),'info');
        }
    }


    $insertSeoData = [
        'seoTitle' => $productCategorySeoTitle,
        'seoDescription' => $productCategorySeoDescription,
        'seoKeywords' => $productCategorySeoKeywords,
        'seoLink' => $link,
        'seoImage' => $seoImage,
        'seoUniqID' => $categoryUniqID
    ];

    if($link !== $categorySeoOriginalLink && $categorySeoOriginalLink !== ''){
        $insertSeoData['seoOriginalLink'] = $categorySeoOriginalLink;
    }

    if( $action == "addProductCategory"){

        $insertSeoData['seoOriginalLink'] = '';
        $insertSeoResult = $adminSeoModel->insertSeo($insertSeoData);

        if($insertSeoResult['status'] == 'error'){
            $adminProductCategoryModel->rollback("$action / Seo");
            echo json_encode($insertSeoResult);
            exit();
        }
    }
    else{

        $updateSeoResult = $adminSeoModel->updateSeo($insertSeoData);
        Log::adminWrite('updateSeoResult: '. json_encode($updateSeoResult,JSON_PRETTY_PRINT),'info');
        if($updateSeoResult['status'] == 'error'){
            $adminProductCategoryModel->rollback("$action /seo");
            echo json_encode($updateSeoResult);
            exit();
        }
    }

    $addGoogleCategory = $adminProductCategoryModel->addGoogleCategory($categoryID, $googleCategory);
    if(!$addGoogleCategory){
        $adminProductCategoryModel->rollback("$action /googleCategory");
        echo json_encode($addGoogleCategory);
        exit();
    }
    $adminProductCategoryModel->commit("updateProductCategory");

    deleteJsonByCategoryId($categoryID);

    deleteSearchJson();

    if($action == "updateProductCategory"){
        $filePath = JSON_DIR . "Menu/menu_".$languageID.".json";
        if(file_exists($filePath)){
            unlink($filePath);
        }
    }

    $message = $action == "addProductCategory" ? "Kategori Kaydedildi" : "Kategori Güncellendi";

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'categoryID' => $categoryID,
        'categoryUniqID' => $categoryUniqID
    ]);
}
elseif ($action == "downloadProductList") {
    // Gerekli dil kimliği kontrolü
    $languageID = $requestData["languageID"] ?? null;

    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID is required'
        ]);
        exit();
    }


    $allProducts = $adminProductModel->getAllProductsForExcel($languageID);

    if (empty($allProducts)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No products found'
        ]);
        exit();
    }

    // ExcelGenerator sınıfını dahil ediyoruz
    include_once MODEL . 'Admin/AdminExcelGenerator.php';
    $excelGenerator = new AdminExcelGenerator();

    // Excel dosyasını oluşturmak için sütun başlıklarını belirliyoruz
    $headers = [
        'Stok Kodu',
        'Ürün Adı',
        'Ürün Detay',
        'Alt Başlık',
        'Kategori Bilgisi (AnaKategori > Kategori > AltKategori)',
        'Marka Adı',
        'Model',
        'Satış Fiyatı',
        'Liste Fiyatı',
        'Para Birimi',
        'KDV',
        'Stok Miktarı',
        'Varyant Bilgileri',
        'Ürün Özellikleri',
        'Görseller',
        'Teslimat Süresi',
        'Ürün Durumu',
        'Barkod',
        'GTIN',
        'MPN',
        'OEM'
    ];

    include_once MODEL . 'Admin/AdminProductCategory.php';
    $adminProductCategoryModel = new AdminProductCategory($db);

    // Her bir ürünü döngüyle işleyin
    foreach ($allProducts as $product) {
        //Log::adminWrite("ProductController: product: " . json_encode($product), "info");
        //ürünün tüm kategorilerini çekelim
        $productCategoryHierarchy = $adminProductCategoryModel->getCategoryHierarchyForExcel($product['productCategoryID']);

        $productProperties = $adminProductModel->formatProductProperties($product['productProperties'] ?? "");

        $productImageUrls = $adminProductModel->getProductImagesForExcel($product['productID'], $config->hostDomain);

        // Varyant bilgilerini JSON olarak ayrıştırın
        $variantData = json_decode($product['productVariants'], true);

        if (json_last_error() === JSON_ERROR_NONE && !empty($variantData)) {
            //Log::adminWrite("ProductController: variantData: " . json_encode($variantData), "info");
            // Her varyantı ayrı bir ürün olarak işleyin
            foreach ($variantData as $variant) {
                //Log::adminWrite("ProductController: variant: " . json_encode($variant['variantProperties']), "info");
                $variantProperties = $adminProductModel->formatVariantProperties($variant['variantProperties']);
                $excelRow = [
                    'productStockCode' => $variant['variantStockCode'] ?? "",
                    'productName' => $product['productName'],
                    'productDescription' => $product['productDescription'],
                    'productShortDescription' => $product['productShortDescription'],
                    'productCategory' => $productCategoryHierarchy,
                    'productBrand' => $product['productBrand'],
                    'productModel' => $product['productModel'],
                    'productSalePrice' => $variant['variantSellingPrice'] ?? "",
                    'productDiscountPrice' => $variant['variantPriceWithoutDiscount'] ?? "",
                    'productCurrency' => $product['productCurrency'],
                    'productTax' =>  $product['productTax']*100,
                    'productStock' => $variant['variantQuantity'] ?? "",
                    'productVariants' => $variantProperties,
                    'productProperties' => $productProperties,
                    'productImages' => $productImageUrls,
                    'productCargo' => $product['productCargo'],
                    'productActive' => $product['productActive'] ? 'Aktif' : 'Pasif',
                    'productBarcode' => $variant['variantBarcode'] ?? "",
                    'productGTIN' => $variant['variantGTIN'] ?? "",
                    'productMPN' => $variant['variantMPN'] ?? "",
                    'productOEM' => $variant['variantOEM'] ?? "",
                ];

                $excelRows[] = $excelRow;
            }
        }
    }

    //Log::adminWrite("ProductController: excelRows: " . json_encode($excelRows), "info");

    // Ürün verilerini Excel dosyasına aktarıyoruz
    $excelGenerator->createExcel($headers, $excelRows, 'ProductList');

    // Excel dosyasını kullanıcıya gönderiyoruz
    //dosya adına tarih zamanı ekleniyoruz
    $excelGenerator->output('product_list_'.date('Y-m-d-H-i-s').'.xlsx');
}
elseif ($action == "uploadProductList") {

    uploadProductList($requestData, $productTransferModel);
}
elseif ($action == "getProductList") {
    $languageID = $requestData['languageID'] ?? null;
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID is required'
        ]);
        exit();
    }
    $productList = $productTransferModel->getTransfersByLanguageID($languageID);
    if (empty($productList)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No product found'
        ]);
        exit();
    }
    echo json_encode([
        'status' => 'success',
        'message' => 'Product list found successfully',
        'productList' => $productList
    ]);
}
elseif ($action == "runTransferProductList") {

    $languageID = $requestData['languageID'] ?? null;
    if (empty($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Language ID is required'
        ]);
        exit();
    }

    $languageCode = $adminLanguageModel->getLanguageCode($languageID);
    Log::adminWrite("ProductController: runTransfer -> languageCode: " . $languageCode, "info");

    $domain = $generalSettingsModel->getDomainByLanguageID($languageID);
    Log::adminWrite("ProductController: runTransfer -> domain: " . $domain, "info");

    $adminPriceSettingsModel = new AdminPriceSettings($db,$languageID);
    $priceSettings = $adminPriceSettingsModel->getPriceSettings();
    if($priceSettings["status"] == "error"){
        echo json_encode([
            'status' => 'error',
            'message' => 'Price settings not found'
        ]);
        exit();
    }
    $priceSettings = $priceSettings["data"];
    Log::adminWrite("ProductController: runTransfer -> priceSettings: " . json_encode($priceSettings), "info");

    $productList = $productTransferModel->getTransferListByLanguageID($languageID);
    if (empty($productList)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Aktarılacak ürün bulunamadı.'
        ]);
        exit();
    }

    $groupedProducts = [];

    foreach ($productList as $product) {
        // Ürünleri model değerine göre grupluyoruz
        $modelKey = $product['model'];

        if (!isset($groupedProducts[$modelKey])) {
            $groupedProducts[$modelKey] = [];
        }

        $groupedProducts[$modelKey][] = $product;
    }
    //Log::adminWrite("ProductController: runTransfer -> groupedProducts: " . json_encode($groupedProducts), "info");exit;

    $errorInfo = "";

    $productTransferModel->beginTransaction("runTransferProductList");

    $i=0;
    foreach ($groupedProducts as $model => $products) {
        $i++;

        $baseProduct = $products[0];

        $productCategoryName = $baseProduct['category_information'];
        Log::adminWrite("ProductController: runTransfer -> productCategoryName: " . $productCategoryName, "info");

        $transferCategoryID = getTransferCategoryID($languageID, $productCategoryName);
        if ($transferCategoryID == 0) {
            $productTransferModel->updateTransferStatus($baseProduct['id'], 2, 'Kategori bulunamadı');
            continue;
        }
        Log::adminWrite("ProductController: runTransfer -> transferCategoryID: " . $transferCategoryID, "info");

        $brandID = $adminBrandModel->getBrandIdByName($baseProduct['brand_name']);
        if ($brandID == 0) {
            //markayı ekleyelim
            $brandData = [
                'brandName' => $helper->escapeHtml($baseProduct['brand_name']),
                'brandDescription' => "",
                'brandUniqID' => $helper->generateUniqID()
            ];
            $brandResponse = $adminBrandModel->addBrand($brandData);
            if ($brandResponse['status'] == 'error') {
                $productTransferModel->updateTransferStatusByModel($baseProduct['model'], 2, 'Marka eklenemedi');
                continue;
            }
            $brandID = $brandResponse['brandID'];
        }
        Log::adminWrite("ProductController: runTransfer -> brandID: " . $brandID, "info");

        Log::adminWrite("ProductController: runTransfer -> baseProduct: " . json_encode($baseProduct), "info");

        //pageUniqID,pageCreateDate,pageType,pageOrder,pageDeleted,pageHit
        $pageLink = $helper->createAdvancedSeoLink($baseProduct['product_label'],$languageCode);
        $pageData = [
            'pageUpdateDate' => date("Y-m-d H:i:s"),
            'pageName' => $baseProduct['product_label'],
            'pageContent' => $baseProduct['long_description'],
            'pageLink' => $pageLink,
            'pageActive' => $baseProduct['product_status']=="Aktif" ? 1 : 0
        ];

        $addPage = 0;
        $pageID = $adminProductModel->getProductIdByStockCode($baseProduct['product_stock_code']);
        Log::adminWrite("ProductController: runTransfer -> pageID: " . $pageID, "info");

        $getSeo = [];
        $productSeoOriginalLink = '';

        if ($pageID == 0) {
            $pageUniqID = $helper->generateUniqID();
            $addPage = 1;
            $pageData['pageUniqID'] = $pageUniqID;
            $pageData['pageCreateDate'] = date("Y-m-d H:i:s");
            $pageData['pageType'] = 7;
            $pageData['pageOrder'] = 0;
            $pageData['pageDeleted'] = 0;
            $pageData['pageHit'] = 0;

            $pageResponse = $adminPageModel->insertPage($pageData);
            if ($pageResponse['status'] == 'error') {
                $productTransferModel->updateTransferStatusByModel($baseProduct['model'], 2, 'Sayfa eklenemedi');
                continue;
            }
            $pageID = $pageResponse['pageID'];
        }
        else {
            $pageData['pageID'] = $pageID;
            $pageResponse = $adminPageModel->updatePage($pageData);
            if ($pageResponse['status'] == 'error') {
                $productTransferModel->updateTransferStatusByModel($baseProduct['model'], 2, 'Sayfa güncellenemedi');
                continue;
            }
            $pageUniqID = $adminPageModel->getPageUniqIDByID($pageID);
            $getSeo = $adminSeoModel->getSeoByUniqId($pageUniqID);
            if(!empty($getSeo)){
                $productSeoOriginalLink = $getSeo['seoLink'] ?? $productSeoOriginalLink;
            }
        }
        Log::adminWrite("ProductController: runTransferProductList - pageID: " . $pageID, "info");

        $adminPageModel->deletePageCategory(['pageID' => $pageID]);
        $categoryResult = $adminPageModel->insertPageCategory(['pageID' => $pageID, 'categoryID' => $transferCategoryID]);

        if ($categoryResult['status'] == 'error') {
            $productTransferModel->updateTransferStatusByModel($baseProduct['model'], 2, 'Sayfa kategoriye eklenemedi eklenemedi');
            Log::adminWrite("ProductController: runTransferProductList - insertPageCategory error: " . $categoryResult['message'], "error");
            continue;
        }

        $imageIDs = [];
        if(!empty($baseProduct['images'])) {
            $productImages = explode(",", $baseProduct['images']);
            $productImageFolderID = $adminImageModel->getImageFolderIDByFolderName("Product");
            foreach ($productImages as $productImage) {

                $imageDownloadResult = $adminImageModel->saveImageFromUrl($productImage, $baseProduct['product_label'], "Product");
                if ($imageDownloadResult['status'] == 'error') {
                    $errorInfo .= " Resimler indirilemedi";
                    Log::adminWrite("ProductController: runTransferProductList - saveImageFromUrl error: " . $imageDownloadResult['message'], "error");
                }
                else{
                    $imageData = [
                        'imageUniqID' => $helper->generateUniqID(),
                        'imagePath' => $imageDownloadResult['imagePath'],
                        'imageFolderID' => $imageDownloadResult['imageFolderID'],
                        'imageFolderName' => $imageDownloadResult['imageFolderName'],
                        'imageName' => $imageDownloadResult['imageName'],
                        'imageWidth' => $imageDownloadResult['imageWidth'],
                        'imageHeight' => $imageDownloadResult['imageHeight']
                    ];

                    $result = $adminImageModel->addImage($imageData);

                    if($result){
                        $imageIDs[] = $result;
                        Log::adminWrite("ProductController: runTransfer -> imageIDs: " . $result, "info");
                    }
                }

            }
        }
        Log::adminWrite("ProductController: runTransferProductList - imageIDs: " . json_encode($imageIDs), "info");

        $seoImages = [];
        if(!empty($imageIDs)){

            $adminPageModel->deletePageImages(['pageID' => $pageID]);

            $pageImageInsertData = [
                'pageID' => $pageID,
                'imageIDs' => $imageIDs
            ];

            $insertPageImageResult = $adminPageModel->insertPageImages($pageImageInsertData);

            if($insertPageImageResult['status'] == 'error'){
                $errorInfo .= " Sayfa resmi güncellenemedi.<br>";
            }
            else{

                if (!empty($pageID)) {
                    $productImages = $adminProductModel->getProductImages($pageID);

                    if (!empty($productImages)) {
                        $seoImages = getSeoImages($productImages, $config, $domain, imgRoot);
                    }
                }
            }
        }

        Log::adminWrite("ProductController: runTransfer -> seoImages: " . json_encode($seoImages), "info");
        if(!empty($seoImages)){
            $seoImages = implode(", ", $seoImages);
        }
        else{
            $seoImages = '';
        }

        $link = "/" . $languageCode;

        $categoryPath = '';
        $categoryHierarchy = $adminProductCategoryModel->getCategoryHierarchy($transferCategoryID);

        foreach ($categoryHierarchy as $category) {
            $categoryName = $category['categoryName'];
            $categoryPath .= '/' . $helper->createAdvancedSeoLink($categoryName,$languageCode);
        }

        $link = $link . $categoryPath;

        $link = $link . $pageLink;

        Log::adminWrite("ProductController: runTransfer -> link: " . $link, "info");
        $seoData = [
            'seoUniqID' => $pageUniqID,
            'seoTitle' => $baseProduct['product_label'],
            'seoDescription' => $baseProduct['product_label'],
            'seoKeywords' => $baseProduct['product_label'],
            'seoLink' => $link,
            'seoImage' => $seoImages
        ];

        if($link !== $productSeoOriginalLink){
            $seoData['seoOriginalLink'] = $productSeoOriginalLink;
        }

        if(!empty($getSeo)){
            $updateSeoResult = $adminSeoModel->updateSeo($seoData);
        }
        else{
            $addSeoResult = $adminSeoModel->insertSeo($seoData);
        }

        $productProperties = !empty($productProperties) ? parseProductProperties($baseProduct['product_features']) :"";

        $variantProperties = [];
        foreach ($products as $product) {
            // Tek varyant için ihtiyaç duyulan veriler
            $variants = processProductVariants($product['variant_information'], $languageCode, $adminProductVariantModel);
            $variantData = [
                'variantID' => $variants["id"],
                'variantName' => createVariantName($product['variant_information']),
                'variantStockCode' => $product['product_stock_code'],
                'variantGTIN' => $product['gtin'],
                'variantMPN' => $product['mpn'],
                'variantBarcode' => $product['barcode'],
                'variantOem' => $product['oem'],
                'variantQuantity' => $product['stock_quantity'],
                'variantSellingPrice' => $product['sale_price'],
                'variantPriceWithoutDiscount' => $product['list_price'],
                'variantSellerPrice' => $product['sale_price'],
                'variantPurchasePrice' => $product['sale_price'],
                'variantDiscountRate' => 0,
                'variantMinQuantity' => 1,
                'variantMaxQuantity' => 999,
                'variantCoefficient' => 1,
                'variantProperties' => $variants["attributes"]
            ];

            // Varyantı variantProperties dizisine ekle
            $variantProperties[] = $variantData;
        }

        if (!empty($variantProperties)) {
            $deleteVariantResult = $adminProductModel->deleteVariantProperty($pageID);
            foreach ($variantProperties as $variant) {
                foreach ($variant['variantProperties'] as $property) {
                    $variantData = [
                        'variant_id' => $pageID,
                        'variant_stock_code' => $variant['variantStockCode'],
                        'variant_quantity' => $variant['variantQuantity'],
                        'variant_selling_price' => $variant['variantSellingPrice'],
                        'variant_image_ids' =>  '',
                        'attribute_name' => $property['attribute']['name'], // Varyant özellikleri örneği
                        'attribute_value' => $property['attribute']['value']
                    ];

                    $insertVariantResult = $adminProductModel->addVariantProperty($variantData);

                    if (!$insertVariantResult) {
                        $adminPageModel->rollback("updateProduct/insertVariantProperties");
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Varyant özellikleri eklenirken bir hata oluştu.'
                        ]);
                        exit();
                    }
                }
            }
        }

        $variantProperties = json_encode($variantProperties, JSON_UNESCAPED_UNICODE);

        $currencyID = $adminCurrencyModel->getCurrencyIDByCode($baseProduct['currency']);
        if(!empty($currencyID)){
            $currencyID = $currencyID[0]['currencyID'];
        }
        else{
            $currencyID = 1;
        }

        $tax = $baseProduct['tax'];
        $tax = intval($tax);
        if($tax == 0){
            $tax = $priceSettings['taxRate'];
        }
        else{
            $tax = round($tax/100,2);
        }

        $productResult = $adminProductModel->getProductByID($pageID);
        if(!empty($productResult)){

            $updateProductData = [
                'productID' => $pageID,
                'productBrandID' => $brandID,
                'productModel' => $baseProduct['model'],
                'productShortDesc' => $baseProduct['short_description'],
                'productCurrency' => $currencyID,
                'productTax' => $tax,
                'variantProperties' => $variantProperties,
                'productProperties' => $productProperties,
                'productStockCode' => $baseProduct['product_stock_code'],
                'productGTIN' => $baseProduct['gtin'],
                'productMPN' => $baseProduct['mpn'],
                'productBarcode' => $baseProduct['barcode'],
                'productOEM' => $baseProduct['oem'],
                'productStock' => $baseProduct['stock_quantity'],
                'productSalePrice' => $baseProduct['sale_price'],
                'productDiscountPrice' => $baseProduct['list_price'],
                'productCargoTime' => $baseProduct['delivery_time']
            ];

            $updateProductResult = $adminProductModel->updateProductByData($updateProductData);
            if($updateProductResult < 0){
                $errorInfo .= " Ürünler güncellenemedi";
                $productTransferModel->updateTransferStatusByModel($baseProduct['model'], 2, $errorInfo);
                continue;
            }
        }
        else {
            $insertProductData = [
                'productID' => $pageID,
                'productSupplierID' => 1,
                'productBrandID' => $brandID,
                'productModel' => $baseProduct['model'],
                'productGroupID' => 0,
                'productDescription' => "",
                'productShortDesc' => $baseProduct['short_description'],
                'productCurrency' => $currencyID,
                'productShowOldPrice' => $priceSettings['showOldPrice'],
                'productInstallment' => $priceSettings['installmentStatus'],
                'productTax' => $tax,
                'productDiscountRate' => 0,
                'productSalesQuantity' => 1,
                'productQuantityUnitID' => 1,
                'productMinimumQuantity' => 1,
                'productMaximumQuantity' => 999,
                'productCoefficient' => 1,
                'productBulkDiscount' =>  0,
                'productPriceAsk' =>  0,
                'variantProperties' => $variantProperties,
                'productProperties' => $productProperties,
                'productStockCode' => $baseProduct['product_stock_code'],
                'productGTIN' => $baseProduct['gtin'],
                'productMPN' => $baseProduct['mpn'],
                'productBarcode' => $baseProduct['barcode'],
                'productOEM' => $baseProduct['oem'],
                'productStock' => $baseProduct['stock_quantity'],
                'productSalePrice' => $baseProduct['sale_price'],
                'productDiscountPrice' => $baseProduct['list_price'],
                'productDealerPrice' => 0,
                'productPurchasePrice' => 0,
                'productCreditCard' => $priceSettings['creditCardStatus'],
                'productBankTransfer' => $priceSettings['bankTransferStatus'],
                'productCashOnDelivery' => $priceSettings['cashOnDeliveryStatus'],
                'productDesi' => $baseProduct['productDesi'] ?? 1,
                'productCargoTime' => $baseProduct['delivery_time'],
                'productFixedCargoPrice' => 0,
                'productPriceLastDate' => (new DateTime())->modify('+1 year')->format('Y-m-d H:i:s'),
                'productHomePage' =>  0,
                'productDayOpportunity' =>  0,
                'productDiscounted' =>  0,
                'productNew' =>  0,
                'productSameDayShipping' => 0,
                'productFreeShipping' => 0,
                'productPreOrder' =>  0
            ];

            $addProductResult = $adminProductModel->insertProduct($insertProductData);
            if($addProductResult['status'] == 'error'){
                $errorInfo .= " Ürünler eklenemedi.<br>";
                $productTransferModel->updateTransferStatusByModel($baseProduct['model'], 2, $errorInfo);
                continue;
            }
        }

        //aktarım durumunu günceller
        $productTransferModel->updateTransferStatusByModel($baseProduct['model'], 1, 'Aktarım başarılı');

        Log::adminWrite("ProductController: runTransfer -> aktarım durumu başarılı: " . $baseProduct['model'], "info");

        deleteJsonByProductId($pageID);

        if($i % 2 == 0){
            $productTransferModel->commit("productTransfer");
            $productTransferModel->beginTransaction("productTransfer");
        }
    }

    $productTransferModel->commit("productTransfer");
    echo json_encode([
        'status' => 'success',
        'message' => 'Ürün aktarımı başarılı'
    ]);
    exit();

}
elseif($action=="getGoogleCategories"){
    $languageCode = $requestData["languageCode"] ?? null;
    if(empty($languageCode)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Language code is required'
        ]);
        exit();
    }
    $upperLanguageCode = strtoupper($languageCode);
    if($languageCode == "en") $upperLanguageCode="US";
    $url = "https://www.google.com/basepages/producttype/taxonomy-with-ids.$languageCode-$upperLanguageCode.txt";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Hata durumunda bilgi almak için
    curl_setopt($ch, CURLOPT_FAILONERROR, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Curl error: ' . curl_error($ch) . ' url:' . $url
        ]);
        exit();
    } else {
        curl_close($ch);

        // Veriyi satırlara ayır
        $lines = explode("\n", $response);

        $categories = [];
        foreach ($lines as $line) {
            // Yorum satırlarını atla
            if (!str_starts_with($line, '#')) {
                // Boşluğa göre ayır
                $parts = explode(' - ', $line, 2);
                if (count($parts) === 2) {
                    $categoryId = $parts[0];
                    $categoryName = $parts[1];
                    $categories[] = [
                        'categoryId' => $categoryId,
                        'categoryName' => $categoryName
                    ];
                }
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Google categories found',
            'categories' => $categories
        ]);
        exit();
    }

}
else{
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

function deleteJsonByProductId($productId){

    $filePath = JSON_DIR . "Product/ProductByID/".$productId.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Product/ProductDetails/".$productId.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Product/RelatedProducts/".$productId.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }
}

function deleteJsonByCategoryId($categoryId){
    global $adminProductCategoryModel;
    $categoryUniqID = $adminProductCategoryModel->getCategoryUniqID($categoryId);

    $filePath = JSON_DIR . "Category/".$categoryUniqID.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    //$filePath = JSON_DIR . "Category/Pages altında kategori $categoryID-" ile başlayan bütün jsonları sil
    $filePath = JSON_DIR . "Category/Pages/";
    $files = glob($filePath . $categoryId."-*");
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    //$filePath = JSON_DIR . "Category/Subcategories altında kategori id ve üst kategori id .json olan dosyaları sil
    $filePath = JSON_DIR . "Category/Subcategories/";
    $files = glob($filePath . $categoryId.".json");
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    $productCategoryTopCategoryID = $adminProductCategoryModel->getProductCategory($categoryId)['topCategoryID'];
    $filePath = JSON_DIR . "Category/Subcategories/".$productCategoryTopCategoryID.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }
}

function deleteJsonByLanguageID($languageID)
{
    $filePath = JSON_DIR . "Homepage/DiscountedProducts/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Homepage/HomepageProducts/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Homepage/NewProducts/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    $filePath = JSON_DIR . "Homepage/SpecialOffers/$languageID.json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

}
function uploadProductList($requestData, AdminProductTransfer $productTransferModel)
{
    // Dosya yüklemesi kontrolü
    if (!isset($_FILES['file'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No file uploaded.'
        ]);
        exit();
    }

    $file = $_FILES['file'];

    // Dosya hatası kontrolü
    if ($file['error'] !== UPLOAD_ERR_OK) {
        Log::adminWrite("ProductController: uploadProductList - file error: " . $file['error'], "error");
        echo json_encode([
            'status' => 'error',
            'message' => 'File upload error.'
        ]);
        exit();
    }

    // Dosya formatını kontrol et (CSV veya Excel)
    $allowedExtensions = ['csv', 'xlsx', 'xls'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid file format. Only CSV and Excel files are allowed.'
        ]);
        exit();
    }

    // Dosyanın geçici konumunu al
    $tmpFilePath = $file['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($tmpFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'File parsing error: ' . $e->getMessage()
        ]);
        exit();
    }

    // Başlık satırını al ve doğrula
    $headers = array_shift($rows);

    // Beklenen başlıklar (ingilizce ve anlaşılır)
    $expectedHeaders = [
        'A' => 'Product Uniq ID',
        'B' => 'Product Name (label)',
        'C' => 'Long Description (details)',
        'D' => 'Short Description',
        'E' => 'Category Information (Main Category > Category > Subcategory)',
        'F' => 'Brand Name',
        'G' => 'Model',
        'H' => 'Sale Price',
        'I' => 'List Price',
        'J' => 'Currency',
        'K' => 'Stock Quantity',
        'L' => 'Variant Information',
        'M' => 'Product Features',
        'N' => 'Images',
        'O' => 'Delivery Time',
        'P' => 'Product Status',
        'Q' => 'Barcode',
        'R' => 'GTIN',
        'S' => 'MPN'
    ];

    /*foreach ($expectedHeaders as $column => $headerName) {
        if (!isset($headers[$column]) || trim($headers[$column]) != $headerName) {
            echo json_encode([
                'status' => 'error',
                'message' => "Invalid header in column {$column}. Expected '{$headerName}'."
            ]);
            exit();
        }
    }*/

    $truncateTransferTable = $productTransferModel->truncateTransferTable();
    if (!$truncateTransferTable) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Transfer table could not be truncated'
        ]);
        exit();
    }
    $languageID = $requestData['languageID'] ?? 1;
    // Her bir satırı işle
    foreach ($rows as $rowNumber => $row) {
        // Satır verilerini map et
        $productData = [
            'language_id' => $languageID,
            'product_stock_code' => isset($row['A']) ? trim($row['A']) : null,
            'product_label' => isset($row['B']) ? trim($row['B']) : null,
            'long_description' => isset($row['C']) ? trim($row['C']) : '',
            'short_description' => isset($row['D']) ? trim($row['D']) : '',
            'category_information' => isset($row['E']) ? trim($row['E']) : '',
            'brand_name' => isset($row['F']) ? trim($row['F']) : '',
            'model' => isset($row['G']) ? trim($row['G']) : '',
            'sale_price' => isset($row['H']) ? floatval($row['H']) : 0.00,
            'list_price' => isset($row['I']) ? floatval($row['I']) : 0.00,
            'currency' => isset($row['J']) ? trim($row['J']) : 'TRY',
            'tax' => isset($row['K']) ? intval($row['K']) : 0,
            'stock_quantity' => isset($row['L']) ? trim($row['L']) : '',
            'variant_information' => isset($row['M']) ? trim($row['M']) : '',
            'product_features' => isset($row['N']) ? trim($row['N']) : '',
            'images' => isset($row['O']) ? trim($row['O']) : 3,
            'delivery_time' => isset($row['P']) ? trim($row['P']) : '',
            'product_status' => isset($row['Q']) ? trim($row['Q']) : '',
            'barcode' => isset($row['R']) ? trim($row['R']) : '',
            'gtin' => isset($row['S']) ? trim($row['S']) : '',
            'mpn' => isset($row['T']) ? trim($row['T']) : '',
            'oem' => isset($row['U']) ? trim($row['U']) : '',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        //product_stock_code,product_label,category_information,brand_name,model,product_status boş olamaz
        if (empty($productData['product_stock_code']) || empty($productData['product_label']) || empty($productData['category_information']) || empty($productData['brand_name']) || empty($productData['model']) || empty($productData['product_status'])) {
            echo json_encode([
                'status' => 'error',
                'message' => "Row {$rowNumber}: Product ID and Product Name are required."
            ]);
            exit();
        }

        //product_status != Aktif ise Pasif olarak ayarla
        if($productData['product_status'] != "Aktif"){
            $productData['product_status'] = "Pasif";
        }

        //fiyat son iki rakamdan önce . ya da , var mı kontrol edelim
        if(strpos($productData['sale_price'],".") !== false || strpos($productData['sale_price'],",") !== false){
            $productData['sale_price'] = str_replace(",","",$productData['sale_price']);
            $productData['sale_price'] = str_replace(".","",$productData['sale_price']);
            //son 2 rakam öncesine "." ekle
            $salePriceLength = strlen($productData['sale_price']);
            $productData['sale_price'] = substr($productData['sale_price'],0,$salePriceLength-2).".".substr($productData['sale_price'],$salePriceLength-2);
        }

        if(strpos($productData['list_price'],".") !== false || strpos($productData['list_price'],",") !== false){
            $productData['list_price'] = str_replace(",","",$productData['list_price']);
            $productData['list_price'] = str_replace(".","",$productData['list_price']);
            $listPriceLength = strlen($productData['list_price']);
            $productData['list_price'] = substr($productData['list_price'],0,$listPriceLength-2).".".substr($productData['list_price'],$listPriceLength-2);
        }

        // Veriyi product_transfer tablosuna ekle
        $result = $productTransferModel->create($productData);

        if ($result['status'] == 'error') {
            echo json_encode([
                'status' => 'error',
                'message' => "Row {$rowNumber}: " . $result['message']
            ]);
            exit();
        }
    }

    // Başarı mesajı ve yönlendirme
    echo json_encode([
        'status' => 'success',
        'message' => 'Dosya yüklemesi başarılı.'
    ]);
    exit();
}

function getTransferCategoryID($languageID, $transferCategory)
{
    global $adminProductCategoryModel;

    // Kategori yolunu diziye dönüştür
    $transferCategoryArray = explode(">", $transferCategory);

    // Üst kategori ID'sini 0 olarak başlat
    $parentCategoryID = 0;

    // Kategorileri sırayla işle
    foreach ($transferCategoryArray as $categoryName) {
        $categoryName = trim($categoryName);
        Log::adminWrite("ProductController: getTransferCategoryID -> categoryName: " . $categoryName, "info");
        // Kategori ID'sini al
        $categoryID = $adminProductCategoryModel->getCategoryIdByLanguageIdAndParentIdAndName($languageID, $parentCategoryID, $categoryName);

        if ($categoryID == 0) {
            return 0;
        }

        // Bir sonraki kategori seviyesi için üst kategori ID'sini güncelle
        $parentCategoryID = $categoryID;

        // En son bulunan kategori ID'sini güncel tut
        $lastCategoryID = $categoryID;
    }

    // En alt kategori ID'sini döndür
    return $lastCategoryID;
}

function processProductVariants($productVariants, $languageCode, AdminProductVariant $adminProductVariantModel) {
    global $helper;
    $variants = [
        "id" => "", // Tüm varyant ID'lerini birleştirerek oluşturacağımız alan
        "attributes" => [] // Tüm attribute'ları buraya ekleyeceğiz
    ];
    Log::adminWrite("ProductController: processProductVariants -> productVariants: " . $productVariants, "info");

    // Trim brackets and split by `],[` to get each variant group
    $variantGroups = explode('],[', trim($productVariants, '[]'));

    $variantIDs = []; // ID'leri geçici olarak toplamak için bir dizi

    foreach ($variantGroups as $variantGroup) {
        if(empty($variantGroup)){
            continue;
        }
        //$variantGroup içinde ":" içeren var mı kontrol edelim
        if(!str_contains($variantGroup, ":")){
            continue;
        }
        // Split by `:` to separate the group name and value
        list($groupName, $variantValue) = explode(':', $variantGroup);
        if(empty($groupName) || empty($variantValue)){
            continue;
        }
        // Trim potential extra spaces
        $groupName = trim($groupName);
        $variantValue = trim($variantValue);

        // Get or create variant group
        $existingGroup = $adminProductVariantModel->getVariantsGroupByName($groupName, $languageCode);
        if ($existingGroup['status'] === 'error') {
            $groupResponse = $adminProductVariantModel->addVariantGroup($groupName, $helper->generateUniqID());
            if ($groupResponse['status'] !== 'success') {
                throw new Exception('Failed to create new variant group: ' . $groupResponse['message']);
            }
            $variantGroupID = $groupResponse['variantGroupID'];
        } else {
            $variantGroupID = $existingGroup['data'][0]['variantGroupID'];
        }

        // Get or create variant
        $existingVariant = $adminProductVariantModel->getVariantByGroupIDAndName($variantGroupID, $variantValue);
        if ($existingVariant['status'] === 'error') {
            $variantResponse = $adminProductVariantModel->addVariant($variantValue, $variantGroupID);
            if ($variantResponse['status'] !== 'success') {
                throw new Exception('Failed to create new variant: ' . $variantResponse['message']);
            }
            $variantID = $variantResponse['variantID'];
        } else {
            $variantID = $existingVariant['data'][0]['variantID'];
        }

        // ID'leri toplamak için birleştiriyoruz
        $variantIDs[] = $variantGroupID . "-" . $variantID;

        // Attributes listesine her bir varyantı ekliyoruz
        $variants["attributes"][] = ['attribute'=>[
            "name" => $groupName,
            "value" => $variantValue
        ]];
    }

    // Tüm ID'leri tek bir string halinde birleştiriyoruz
    $variants["id"] = implode("_", $variantIDs);

    return $variants;
}

function createVariantName($variantString): string {
    // Örnek: "[Renk:Mavi],[Beden:L]" -> "Renk: Mavi | Beden: L"
    $variantGroups = explode('],[', trim($variantString, '[]'));
    $names = array_map(function ($group) {
        list($name, $value) = explode(':', $group);
        return "$name: $value";
    }, $variantGroups);

    return implode(" | ", $names);
}

function parseProductProperties($productProperties) {
    $propertyArray = [];

    // Trim brackets and split by `],[` to get each property
    $properties = explode('],[', trim($productProperties, '[]'));

    foreach ($properties as $property) {
        // Split each property by `:` to separate name and value
        list($name, $value) = explode(':', $property);

        // Trim potential extra spaces
        $name = trim($name);
        $value = trim($value);

        // Build the formatted attribute array
        $propertyArray[] = [
            'attribute' => [
                'name' => $name,
                'value' => $value
            ]
        ];
    }

    return json_encode($propertyArray, JSON_UNESCAPED_UNICODE);
}

function getSeoImages($pageImages, $config, $domain, $imgRoot): array {
    if (empty($pageImages)) {
        return [];
    }
    $pageImages = explode("||", $pageImages);
    return array_map(function ($image) use ($config, $domain, $imgRoot) {
        $details = [];
        $parts = explode("|", $image);
        foreach ($parts as $part) {
            $explodedPart = explode(':', $part, 2);
            if (count($explodedPart) === 2) {
                list($key, $value) = $explodedPart;
                $details[$key] = trim($value);
            }
        }
        $imageUrl = $details['imageUrl'] ?? '';
        return $config->http . $domain . $imgRoot . $imageUrl;
    }, $pageImages);
}

function deleteSearchJson(){
    $folderName = JSON_DIR . "Search";
    //varsa altındaki tüm json uzantılı dosyaları sil
    if (is_dir($folderName)) {
        $files = glob($folderName . "/*.json");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

}

function deleteJsonForPage($pageUniqID){
    $filePath = JSON_DIR . 'Page/'.$pageUniqID.'.json';
    if(file_exists($filePath)){
        unlink($filePath);
    }
}

function deleteJsonByProductUniqID($productUniqID){
    $filePath = JSON_DIR . 'Page/'.$productUniqID.'.json';
    if(file_exists($filePath)){
        unlink($filePath);
    }
}
