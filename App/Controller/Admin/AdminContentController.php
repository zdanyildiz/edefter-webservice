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

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);
$mainLanguage = $languageModel->getMainLanguageId();

include_once MODEL . 'Admin/AdminPage.php';
$pageModel = new AdminPage($db);

include_once MODEL . 'Admin/AdminCategory.php';
$categoryModel = new AdminCategory($db);

include_once MODEL . 'Admin/AdminProductCategory.php';
$productCategoryModel = new AdminProductCategory($db);

include_once MODEL . 'Admin/AdminSeo.php';
$seoModel = new AdminSeo($db);

if ($action == "searchContent") {
    $languageID = $requestData["languageID"] ?? 1;
    $searchText = $requestData["searchText"] ?? null;

    if (!isset($searchText)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search error'
        ]);
        exit();
    }

    $pageContent = $pageModel->getPageBySearch($languageID, $searchText);
    //print_r($pageContent);exit;
    //sonuç boş değilse pageID,pageUniqID döncecek. Bunları döngüye alıp contentResult dizisine contentType, contentID,contentUniqID diye ekleyelim
    $contentResult = [];
    if (!empty($pageContent)) {
        foreach ($pageContent as $content) {
            $originalPageName = "";
            if($languageID != $mainLanguage){
                $originalPageId = $languageModel->getOriginalPageId($content['pageID']);
                if($originalPageId){
                    $originalPage = $pageModel->getPage($originalPageId[0]['original_page_id']);
                    if($originalPage){
                        $originalPageName = $originalPage['pageName'];
                    }
                }
            }

            $contentResult[] = [
                'contentType' => 'page',
                'contentID' => $content['pageID'],
                'contentTitle' => $content['pageName'],
                'contentOriginalTitle' => $originalPageName,
                'contentUniqID' => $content['pageUniqID'],
                'subCategory' => 0
            ];
        }
    }

    $categoryContent = $categoryModel->getCategoryBySearch($searchText,$languageID);
    if (!empty($categoryContent)) {
        foreach ($categoryContent as $category) {
            $originalCategoryName = "";
            if($languageID != $mainLanguage){
                $originalCategoryId = $languageModel->getOriginalCategoryId($category['categoryID']);
                if($originalCategoryId){
                    $originalCategory = $categoryModel->getCategory($originalCategoryId[0]['original_category_id']);
                    if($originalCategory){
                        $originalCategoryName = $originalCategory['categoryName'];
                    }
                }
            }

            $contentResult[] = [
                'contentType' => 'category',
                'contentID' => $category['categoryID'],
                'contentTitle' => $category['categoryName'],
                'contentOriginalTitle' => $originalCategoryName,
                'contentUniqID' => $category['categoryUniqID'],
                'subCategory' => $categoryModel->getSubCategoryCount($category['categoryID'])
            ];
        }
    }

    $productCategoryContent = $productCategoryModel->getProductCategoryBySearch($searchText,$languageID);
    if (!empty($productCategoryContent)) {
        foreach ($productCategoryContent as $productCategory) {
            $originalCategoryName = "";
            if($languageID != $mainLanguage){
                $originalCategoryId = $languageModel->getOriginalCategoryId($productCategory['productCategoryID']);
                if($originalCategoryId){
                    $originalCategory = $categoryModel->getCategory($originalCategoryId[0]['original_category_id']);
                    if($originalCategory){
                        $originalCategoryName = $originalCategory['categoryName'];
                    }
                }
            }

            $contentResult[] = [
                'contentType' => 'productCategory',
                'contentID' => $productCategory['productCategoryID'],
                'contentTitle' => $productCategory['productCategoryName'],
                'contentOriginalTitle' => $originalCategoryName,
                'contentUniqID' => $productCategory['productCategoryUniqID'],
                'subCategory' => $productCategoryModel->getSubCategoryCount($productCategory['productCategoryID'])
            ];
        }
    }

    //print_r($contentResult);exit;

    //sonuçları döndürelim ve seo baslik ve linklerini alalım
    $contentData = [];

    if(!empty($contentResult)){
        foreach ($contentResult as $content) {
            $contentID = $content['contentID'];
            $contentUniqID = $content['contentUniqID'];
            $contentType = $content['contentType'];
            $contentSeo = $seoModel->getSeoByUniqId($contentUniqID);

            if($contentSeo){
                $contentData[] = [
                    'contentID' => $contentID,
                    'contentUniqID' => $contentUniqID,
                    'contentType' => $contentType,
                    'contentTitle' => $content['contentTitle'],
                    'contentOriginalTitle' => $content['contentOriginalTitle'],
                    'seoTitle' => $contentSeo['seoTitle'],
                    'seoLink' => $contentSeo['seoLink'],
                    'subCategory' => $content['subCategory'] ?? 0
                ];
            }

        }
    }

    if(!empty($contentData)){
        echo json_encode([
            'status' => 'success',
            'contentData' => $contentData
        ]);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'No content found'
        ]);
    }

}
elseif ($action == "searchContentBySearchType"){

    $languageID = $requestData["languageID"] ?? 1;
    $searchType = $requestData["searchType"] ?? null;

    if (!isset($searchType)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search error'
        ]);
        exit();
    }

    $contentResult = [];
    if($searchType == "page"){
        $pageContent = $pageModel->getPagesByLanguageID($languageID);
        if (!empty($pageContent)) {
            foreach ($pageContent as $content) {
                $originalPageName = "";
                if($languageID != $mainLanguage){
                    $originalPageId = $languageModel->getOriginalPageId($content['pageID']);
                    if($originalPageId){
                        $originalPage = $pageModel->getPage($originalPageId[0]['original_page_id']);
                        if($originalPage){
                            $originalPageName = $originalPage['pageName'];
                        }
                    }
                }

                $contentResult[] = [
                    'contentType' => 'page',
                    'contentID' => $content['pageID'],
                    'contentTitle' => $content['pageName'],
                    'contentOriginalTitle' => $originalPageName,
                    'contentUniqID' => $content['pageUniqID'],
                    'subCategory' => 0
                ];
            }
        }
    }
    elseif($searchType == "category"){
        $categoryContent = $categoryModel->getCategories($languageID);
        if (!empty($categoryContent)) {
            foreach ($categoryContent as $category) {
                $originalCategoryName = "";
                if($languageID != $mainLanguage){
                    $originalCategoryId = $languageModel->getOriginalCategoryId($category['categoryID']);
                    if($originalCategoryId){
                        $originalCategory = $categoryModel->getCategory($originalCategoryId[0]['original_category_id']);
                        if($originalCategory){
                            $originalCategoryName = $originalCategory['categoryName'];
                        }
                    }
                }

                $contentResult[] = [
                    'contentType' => 'category',
                    'contentID' => $category['categoryID'],
                    'contentTitle' => $category['categoryName'],
                    'contentOriginalTitle' => $originalCategoryName,
                    'contentUniqID' => $category['categoryUniqID'],
                    'subCategory' => $categoryModel->getSubCategoryCount($category['categoryID'])
                ];
            }
        }
    }
    elseif($searchType == "productCategory"){
        $productCategoryContent = $productCategoryModel->getProductCategories($languageID);
        if (!empty($productCategoryContent)) {
            foreach ($productCategoryContent as $productCategory) {
                $originalCategoryName = "";
                if($languageID != $mainLanguage){
                    $originalCategoryId = $languageModel->getOriginalCategoryId($productCategory['productCategoryID']);
                    if($originalCategoryId){
                        $originalCategory = $categoryModel->getCategory($originalCategoryId[0]['original_category_id']);
                        if($originalCategory){
                            $originalCategoryName = $originalCategory['categoryName'];
                        }
                    }
                }

                $contentResult[] = [
                    'contentType' => 'productCategory',
                    'contentID' => $productCategory['productCategoryID'],
                    'contentTitle' => $productCategory['productCategoryName'],
                    'contentOriginalTitle' => $originalCategoryName,
                    'contentUniqID' => $productCategory['productCategoryUniqID'],
                    'subCategory' => $productCategoryModel->getSubCategoryCount($productCategory['productCategoryID'])
                ];
            }
        }
    }

    //sonuçları döndürelim ve seo baslik ve linklerini alalım
    $contentData = [];

    if(!empty($contentResult)){
        foreach ($contentResult as $content) {
            $contentID = $content['contentID'];
            $contentUniqID = $content['contentUniqID'];
            $contentType = $content['contentType'];
            $contentSeo = $seoModel->getSeoByUniqId($contentUniqID);

            if($contentSeo){
                $contentData[] = [
                    'contentID' => $contentID,
                    'contentUniqID' => $contentUniqID,
                    'contentType' => $contentType,
                    'contentTitle' => $content['contentTitle'],
                    'contentOriginalTitle' => $content['contentOriginalTitle'],
                    'seoTitle' => $contentSeo['seoTitle'],
                    'seoLink' => $contentSeo['seoLink'],
                    'subCategory' => $content['subCategory'] ?? 0
                ];
            }

        }
    }

    if(!empty($contentData)){
        echo json_encode([
            'status' => 'success',
            'contentData' => $contentData
        ]);
    }
    else{
        echo json_encode([
            'status' => 'error',
            'message' => 'No content found'
        ]);
    }


}