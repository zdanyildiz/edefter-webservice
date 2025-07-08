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

$languageID = $requestData["languageID"] ?? 1;
$referrer = $requestData["referrer"] ?? null;
$adminCasper = $adminSession->getAdminCasper();
$config = $adminCasper->getConfig();
$json = $config->Json;

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


include_once MODEL . 'Admin/AdminPage.php';
$adminPageModel = new AdminPage($db);

include_once MODEL . 'Admin/AdminCategory.php';
$adminCategoryModel = new AdminCategory($db);

include_once MODEL . 'Admin/GeneralSettings.php';
$generalSettingsModel = new GeneralSettings($db);

include_once MODEL . 'Admin/AdminSeo.php';
$adminSeoModel = new AdminSeo($db);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguageModel = new AdminLanguage($db);

function _deleteSinglePage($pageID) {
    global $adminPageModel, $adminSeoModel; // Global değişkenlere erişim

    if (empty($pageID)) {
        return ['status' => 'error', 'message' => 'Sayfa ID gereklidir!'];
    }

    $pageUniqID = $adminPageModel->getPageUniqIDByID($pageID);
    $pageCategoryID = $adminPageModel->getPageCategoryID($pageID);

    $adminPageModel->beginTransaction("deletePage");

    $deletePageResult = $adminPageModel->deletePage($pageID);
    if($deletePageResult == -1){
        $adminPageModel->rollback("deletePage");
        return ['status' => 'error', 'message' => 'Sayfa silinemedi 1'];
    }

    $deletePageCategoryListResult = $adminPageModel->deletePageCategoryList($pageID);
    if(!$deletePageCategoryListResult){
        $adminPageModel->rollback("deletePage/deletePageCategoryList");
        return ['status' => 'error', 'message' => 'Sayfa kategori ilişkisi silinemedi'];
    }

    $deletePageImagesResult = $adminPageModel->deletePageImages(['pageID' => $pageID]);

    $deletePageFilesResult = $adminPageModel->deletePageFiles(['pageID' => $pageID]);

    $deletePageVideosResult = $adminPageModel->deletePageVideos(['pageID' => $pageID]);

    $deletePageGalleryResult = $adminPageModel->deletePageGallery(["pageID"=>$pageID]);

    $deletePageSeoResult = $adminSeoModel->deleteSeo($pageUniqID);

    if(!$deletePageSeoResult){
        $adminPageModel->rollback("deletePage/deletePageSeo");
        return ['status' => 'error', 'message' => 'Sayfa SEO silinemedi'];
    }

    $adminPageModel->commit("deletePage");

    deleteJsonForPage($pageUniqID);
    deleteJsonByCategoryId($pageCategoryID);

    $customCssFile = CSS . 'Page/CustomCSS/' . $pageUniqID . '.css';
    if (file_exists($customCssFile)) {
        unlink($customCssFile);
    }

    return ['status' => 'success', 'message' => 'Sayfa başarıyla silindi'];
}

if($action == "addPage"){
    $pageCategoryID = $requestData["pageCategoryID"] ?? null;
    $pageType = $requestData["pageType"] ?? null;
    $pageStatus = $requestData["pageStatus"] ?? 0;
    $pageName = $requestData["pageName"] ?? null;
    $pageContent = $requestData["pageContent"] ?? "";
    $pageLink = $requestData["pageLink"] ?? null;
    $pageOrder = $requestData["pageOrder"] ?? 0;
    $customCSS = $requestData["customCSS"] ?? "";

    $checkPage = $adminPageModel->checkPage($pageName,$pageCategoryID);
    if($checkPage>0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu kategoride aynı isimde sayfa var'
        ]);
        exit();
    }

    $pageImages = $requestData['imageID'] ?? null;
    $pageFiles = $requestData['fileID'] ?? null;
    $pageVideos = $requestData['pageVideoIDS'] ?? null;

    $createDate = date("Y-m-d H:i:s");
    $updateDate = $createDate;

    $pageUniqID = $helper->createPassword("20","2");

    $customCssFile = CSS . 'Page/CustomCSS/' . $pageUniqID . '.css';
    if(!empty($customCSS)){
        $customCssDir = CSS . 'Page/CustomCSS/';
        if (!is_dir($customCssDir)) {
            mkdir($customCssDir, 0755, true);
        }
        // customCSS içeriğini dosyaya yaz
        file_put_contents($customCssFile, $customCSS);
    }
    else{
        if (file_exists($customCssFile)) {
            unlink($customCssFile);
        }
    }

    $pageSeoTitle = $requestData["pageSeoTitle"] ?? null;
    $pageSeoDescription = $requestData["pageSeoDescription"] ?? null;
    $pageSeoKeywords = $requestData["pageSeoKeywords"] ?? null;
    
    //kategori, sayfa başlığı, sayfa tipi, sayfa bağlantısı, ve seo içerikleri boş olamaz
    if(empty($pageCategoryID) || empty($pageName) || empty($pageType) || empty($pageLink) || empty($pageSeoTitle) || empty($pageSeoDescription) || empty($pageSeoKeywords)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen zorunlu alanları doldurunuz!'
        ]);
        exit();
    }

    if(substr($pageLink,0,1) != "/"){
        $pageLink = "/" . $pageLink;
    }

    $insertPageData = [
        'pageUniqID' => $pageUniqID,
        'pageCreateDate' => $createDate,
        'pageUpdateDate' => $updateDate,
        'pageType' => $pageType,
        'pageName' => $pageName,
        'pageContent' => $pageContent,
        'pageOrder' => $pageOrder ?? 0,
        'pageLink' => $pageLink,
        'pageActive' => $pageStatus,
        'pageDeleted' => 0,
        'pageHit' => 0
    ];
    
    $adminPageModel->beginTransaction("addPage");

    ############### SAYFA EKLE ####################

    $insertPageResult = $adminPageModel->insertPage($insertPageData);

    if($insertPageResult['status'] == 'error'){
        $adminPageModel->rollback("insertPage");
        echo json_encode($insertPageResult);
        exit();
    }

    $pageID = $adminPageModel->getPageIDByUniqID($pageUniqID);

    ############### KATEGORİ EKLE ####################

    $pageCategoryInsertData = [
        'pageID' => $pageID,
        'categoryID' => $pageCategoryID,
    ];

    $insertPageCategoryResult = $adminPageModel->insertPageCategory($pageCategoryInsertData);

    if($insertPageCategoryResult['status'] == 'error'){
        $adminPageModel->rollback("insertPage/insertPageCategory");
        echo json_encode($insertPageCategoryResult);
        exit();
    }

    $languageID = $adminCategoryModel->getCategoryLanguageID($pageCategoryID);

    ############### RESİM EKLE ####################

    if(!empty($pageImages)){
        $pageImageInsertData = [
            'pageID' => $pageID,
            'imageIDs' => $pageImages,
        ];

        $insertPageImageResult = $adminPageModel->insertPageImages($pageImageInsertData);

        if($insertPageImageResult['status'] == 'error'){
            $adminPageModel->rollback("insertPage/insertPageImages");
            echo json_encode($insertPageImageResult);
            exit();
        }
    }

    ############### DOSYA EKLE ####################

    if(!empty($pageFiles))
    {
        $insertFileUpdateData = [
            'pageID' => $pageID,
            'fileIDs' => $pageFiles,
        ];

        $insertPageFileResult = $adminPageModel->insertPageFiles($insertFileUpdateData);

        if($insertPageFileResult['status'] == 'error'){
            $adminPageModel->rollback("insertPage/insertPageFiles");
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

    $languageCode = $adminLanguageModel->getLanguageCode($languageID);
    $languageCode = $helper->toLowerCase($languageCode);

    $link = $link . "/" . $languageCode;

    $categoryPath = '';
    $categoryHierarchy = $adminCategoryModel->getCategoryHierarchy($pageCategoryID);

    foreach ($categoryHierarchy as $category) {
        $categoryName = $category['categoryName'];
        $categoryPath .= '/' . $helper->createAdvancedSeoLink($categoryName,$languageCode);
    }

    $link = $link . $categoryPath;

    $link = $link . $pageLink;

    include_once MODEL . 'Admin/AdminSeo.php';
    $adminSeoModel = new AdminSeo($db);

    $seoImages = [];
    if (!empty($pageID)) {
        $pageImages = $adminPageModel->getPageImages($pageID);

        if (!empty($pageImages)) {
            $seoImages = getSeoImages($pageImages, $config, $domain, imgRoot);
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
        'seoTitle' => $pageSeoTitle,
        'seoDescription' => $pageSeoDescription,
        'seoKeywords' => $pageSeoKeywords,
        'seoLink' => $link,
        'seoOriginalLink' => '',
        'seoImage' => $seoImages
    ];

    $insertSeoResult = $adminSeoModel->insertSeo($insertSeoData);

    if($insertSeoResult['status'] == 'error'){
        $adminPageModel->rollback("insertPage/insertSeo");
        echo json_encode($insertSeoResult);
        exit();
    }

    $files = glob(JSON_DIR . 'Category/Pages/'.$pageCategoryID.'-*');

    foreach($files as $file){
        unlink($file);
    }

    $adminPageModel->commit("insertPage");

    echo json_encode([
        'status' => 'success',
        'message' => 'Sayfa başarıyla eklendi',
        'pageID' => $pageID,
        'pageUniqID' => $pageUniqID
    ]);
}
elseif($action == "updatePage"){

    $pageID = $requestData["pageID"] ?? null;
    $pageCategoryID = $requestData["pageCategoryID"] ?? null;
    $pageType = $requestData["pageType"] ?? null;
    $pageStatus = $requestData["pageStatus"] ?? 0;
    $pageName = $requestData["pageName"] ?? null;
    $pageContent = $requestData["pageContent"] ?? "";
    $pageLink = $requestData["pageLink"] ?? null;
    $pageOrder = $requestData["pageOrder"] ?? 0;
    $customCSS = $requestData["customCSS"] ?? "";
    

    $checkPage = $adminPageModel->checkPageWithPageID($pageID, $pageName,$pageCategoryID);
    if($checkPage>0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu kategoride aynı isimde sayfa var'
        ]);
        exit();
    }

    $pageImages = $requestData['imageID'] ?? null;
    $pageFiles = $requestData['fileID'] ?? null;
    $pageVideos = $requestData['pageVideoIDS'] ?? null;

    $updateDate = date("Y-m-d H:i:s");

    $pageSeoTitle = $requestData["pageSeoTitle"] ?? null;
    $pageSeoDescription = $requestData["pageSeoDescription"] ?? null;
    $pageSeoKeywords = $requestData["pageSeoKeywords"] ?? null;

    //kategori, sayfa başlığı, sayfa tipi, sayfa bağlantısı, ve seo içerikleri boş olamaz
    if(empty($pageCategoryID) || empty($pageName) || empty($pageType) || empty($pageLink) || empty($pageSeoTitle) || empty($pageSeoDescription) || empty($pageSeoKeywords)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen zorunlu alanları doldurunuz!'
        ]);
        exit();
    }

    if(substr($pageLink,0,1) != "/"){
        $pageLink = "/" . $pageLink;
    }

    $updatePageData = [
        'pageUpdateDate' => $updateDate,
        'pageType' => $pageType,
        'pageName' => $pageName,
        'pageContent' => $pageContent,
        'pageOrder' => $pageOrder ?? 0,
        'pageLink' => $pageLink,
        'pageActive' => $pageStatus,
        'pageID' => $pageID
    ];

    $adminPageModel->beginTransaction("updatePage");

    ############### SAYFA GÜNCELLE ####################

    $updatePageResult = $adminPageModel->updatePage($updatePageData);

    if($updatePageResult['status'] == 'error') {
        $adminPageModel->rollback("updatePage");
        echo json_encode($updatePageResult);
        exit();
    }

    ############### KATEGORİ GÜNCELLE ####################

    $pageCategory = $adminPageModel->getPageCategory($pageID);

    $pageCategoryUpdateData = [
        'categoryID' => $pageCategoryID,
        'pageID' => $pageID
    ];

    if(!$pageCategory){
        $insertPageCategoryResult = $adminPageModel->insertPageCategory($pageCategoryUpdateData);
        if($insertPageCategoryResult['status'] == 'error'){
            $adminPageModel->rollback("updatePage/insertPageCategory");
            echo json_encode($insertPageCategoryResult);
            exit();
        }
    }
    else{
        $updatePageCategoryResult = $adminPageModel->updatePageCategory($pageCategoryUpdateData);

        if($updatePageCategoryResult['status'] == 'error'){
            $adminPageModel->rollback("updatePage/updatePageCategory");
            echo json_encode($updatePageCategoryResult);
            exit();
        }
    }

    

    ############### RESİM GÜNCELLE ####################

    $deletePageImagesResult = $adminPageModel->deletePageImages(['pageID' => $pageID]);

    if(!empty($pageImages)){

        $adminPageModel->deletePageImages(['pageID' => $pageID]);

        $pageImageInsertData = [
            'pageID' => $pageID,
            'imageIDs' => $pageImages,
        ];

        $insertPageImageResult = $adminPageModel->insertPageImages($pageImageInsertData);

        if($insertPageImageResult['status'] == 'error'){
            $adminPageModel->rollback("updatePage/insertPageImages");
            echo json_encode($insertPageImageResult);
            exit();
        }
    }

    ############### DOSYA GÜNCELLE ####################

    if(!empty($pageFiles)){

        $adminPageModel->deletePageFiles(['pageID' => $pageID]);

        $insertFileUpdateData = [
            'pageID' => $pageID,
            'fileIDs' => $pageFiles,
        ];

        $insertPageFileResult = $adminPageModel->insertPageFiles($insertFileUpdateData);

        if($insertPageFileResult['status'] == 'error'){
            $adminPageModel->rollback("updatePage/insertPageFiles");
            echo json_encode($insertPageFileResult);
            exit();
        }
    }

    $deletePageVideosResult = $adminPageModel->deletePageVideos(['pageID' => $pageID]);
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

    $deletePageGalleryResult = $adminPageModel->deletePageGallery(["pageID"=>$pageID]);
    $pageGalleryID = $requestData["pageGalleryID"] ?? 0;
    if($pageGalleryID > 0){
        $addPageGallery = $adminPageModel->addPageGallery($pageID,$pageGalleryID);
    }

    $domain = $generalSettingsModel->getDomainByLanguageID($languageID);

    $link = "";

    $languageCode = $adminLanguageModel->getLanguageCode($languageID);
    $languageCode = $helper->toLowerCase($languageCode);

    $link = $link . "/" . $languageCode;

    $categoryPath = '';
    $categoryHierarchy = $adminCategoryModel->getCategoryHierarchy($pageCategoryID);

    foreach ($categoryHierarchy as $category) {
        $categoryName = $category['categoryName'];
        $categoryPath .= '/' . $helper->createAdvancedSeoLink($categoryName,$languageCode);
    }

    $link = $link . $categoryPath;

    $link = $link . $pageLink;

    include_once MODEL . 'Admin/AdminSeo.php';
    $adminSeoModel = new AdminSeo($db);

    $seoImages = [];

    $pageImages = $adminPageModel->getPageImages($pageID);

    if (!empty($pageImages)) {
        $seoImages = getSeoImages($pageImages, $config, $domain, imgRoot);
    }


    if(!empty($seoImages)){
        //dizeye çevirelim
        $seoImages = implode(", ", $seoImages);
    }
    else{
        $seoImages = '';
    }

    $pageUniqID = $adminPageModel->getPageUniqIDByID($pageID);
    $seoOriginalLink = $adminSeoModel->getSeoOriginalLink($pageUniqID);

    $insertSeoData = [
        'seoUniqID' => $pageUniqID,
        'seoTitle' => $pageSeoTitle,
        'seoDescription' => $pageSeoDescription,
        'seoKeywords' => $pageSeoKeywords,
        'seoLink' => $link,
        'seoImage' => $seoImages
    ];

    
    if(!empty($seoOriginalLink) && $link != $seoOriginalLink){

        $insertSeoData['seoOriginalLink'] = $seoOriginalLink;

        include_once MODEL . 'Admin/AdminMenu.php';
        $adminMenuModel = new AdminMenu($db);

        $updateMenuLink = [
            'newMenulink' => $link,
            'oldMenulink' => $seoOriginalLink
        ];

        $updateMenuLinkResult = $adminMenuModel->updateMenuLinkByLink($updateMenuLink);

        $updateMenuLinkResult = $adminMenuModel->updateMenuLinkByMenuOrijinalUniqID($pageUniqID,$link);
    }

    if(!$adminSeoModel->getSeoByUniqId($pageUniqID)){
        $insertSeoData['seoOriginalLink'] = $link; 
        $insertSeoResult = $adminSeoModel->insertSeo($insertSeoData);
    }
    else{
        $insertSeoResult = $adminSeoModel->updateSeo($insertSeoData);
    }

    if($insertSeoResult['status'] == 'error'){
        $adminPageModel->rollback("insertPage/insertSeo");
        echo json_encode($insertSeoResult);
        exit();
    }

    $adminPageModel->commit("updatePage");

    $customCssFile = CSS . 'Page/CustomCSS/' . $pageUniqID . '.css';
    if(!empty($customCSS)){
        $customCssDir = CSS . 'Page/CustomCSS/';
        if (!is_dir($customCssDir)) {
            mkdir($customCssDir, 0755, true);
        }
        // customCSS içeriğini dosyaya yaz
        file_put_contents($customCssFile, $customCSS);
    }
    else{
        if (file_exists($customCssFile)) {
            unlink($customCssFile);
        }
    }

    $filePath = JSON_DIR . 'Page/'.$pageUniqID.'.json';
    if(file_exists($filePath)){
        unlink($filePath);
    }

    //Json /Category/Pages altında $pageCategoryID- ile başlayan tüm json dosyalarını silelim. örn: 3411-0098e38a1dba2c510b43e0f815a35474.json

    $files = glob(JSON_DIR . 'Category/Pages/'.$pageCategoryID.'-*');

    foreach($files as $file){
        unlink($file);
    }

    $filePath = JSON_DIR . "Menu/menu_".$languageID.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Sayfa başarıyla güncellendi',
        'pageID' => $pageID,
        'pageUniqID' => $pageUniqID
    ]);
    exit;

}
elseif($action == "getPagesByCategoryID"){
    $pageCategoryID = $requestData["pageCategoryID"] ?? null;

    $pagesResult = $adminPageModel->getPagesByCategoryID($pageCategoryID);
    //print_r($pagesResult);

    if(!empty($pagesResult)){
        echo json_encode([
            'status' => 'success',
            'pages' => $pagesResult
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Sayfa bulunamadı'
    ]);
}
elseif($action == "getPageByID"){
    $pageID = $requestData["pageID"] ?? null;

    $pageResult = $adminPageModel->getPage($pageID);

    if(!empty($pageResult)){
        echo json_encode([
            'status' => 'success',
            'page' => $pageResult
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Sayfa bulunamadı'
    ]);
}
elseif($action == "deletePage"){
    $pageID = $requestData["pageID"] ?? null;
    echo json_encode(_deleteSinglePage($pageID));
    exit();
}
elseif($action == "deletePages"){
    $pageIDs = $requestData["pageIDs"] ?? [];
    if (empty($pageIDs)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Silinecek sayfa ID\'leri gereklidir!'
        ]);
        exit();
    }

    $results = [];
    foreach ($pageIDs as $pageID) {
        $results[] = _deleteSinglePage($pageID);
    }

    // Tüm işlemlerin sonucunu özetle
    $successfulDeletions = array_filter($results, function($r) { return $r['status'] === 'success'; });
    $failedDeletions = array_filter($results, function($r) { return $r['status'] === 'error'; });

    if (empty($failedDeletions)) {
        echo json_encode([
            'status' => 'success',
            'message' => count($successfulDeletions) . ' adet sayfa başarıyla silindi. 2'
        ]);
    }
    elseif (empty($successfulDeletions)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Hiçbir sayfa silinemedi. 3'
        ]);
    } else {
        echo json_encode([
            'status' => 'partial_success',
            'message' => count($successfulDeletions) . ' adet sayfa silindi, ' . count($failedDeletions) . ' adet sayfa silinemedi. 4'
        ]);
    }
    exit();
}
elseif($action == "savePageOrder"){
    //pageID,pageOrder

    $pageOrder = $requestData["pageOrder"] ?? null;
    $pageID = $requestData["pageID"] ?? null;

    //ikisi de boş olamaz
    if(empty($pageOrder) || empty($pageID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen zorunlu alanları doldurunuz!'
        ]);
        exit();
    }

    $adminPageModel->beginTransaction("savePageOrder");

    $updatePageOrderData = [
        'pageOrder' => $pageOrder,
        'pageID' => $pageID
    ];

    $updatePageOrderResult = $adminPageModel->updatePageOrder($updatePageOrderData);

    if($updatePageOrderResult['status'] == 'error'){
        $adminPageModel->rollback("savePageOrder");
        echo json_encode($updatePageOrderResult);
        exit();
    }

    $adminPageModel->commit("savePageOrder");

    echo json_encode([
        'status' => 'success',
        'message' => 'Sayfa sırası başarıyla güncellendi'
    ]);
}
elseif($action == "searchPage"){
    $searchText = $requestData["searchText"] ?? null;
    $languageID = $requestData["languageID"] ?? 1;

    if(empty($searchText)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen zorunlu alanları doldurunuz!'
        ]);
        exit();
    }

    $pagesResult = $adminPageModel->getPageBySearch($languageID,$searchText);

    if(!empty($pagesResult)){

        echo json_encode([
            'status' => 'success',
            'pages' => $pagesResult
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Sayfa bulunamadı'
    ]);
    exit();
}
elseif($action == "getAllPages"){
    $languageID = $requestData["languageID"] ?? 1;

    $pagesResult = $adminPageModel->getAllPages($languageID);

    if(!empty($pagesResult)){

        echo json_encode([
            'status' => 'success',
            'pages' => $pagesResult
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Sayfa bulunamadı'
    ]);
    exit();
}
elseif($action == "getPagesWithTranslationStatus"){
    $languageID = $requestData["languageID"] ?? 1;
    $translationFilter = $requestData["translationFilter"] ?? 'all';
    $targetLanguageID = $requestData["targetLanguageID"] ?? null;
    $categoryID = $requestData["categoryID"] ?? null;
    $searchText = $requestData["searchText"] ?? null;
    
    if ($translationFilter === 'all') {
        $pagesResult = $adminPageModel->getAllPagesWithTranslationStatus($languageID);
    } else {
        $pagesResult = $adminPageModel->getPagesByTranslationStatus($languageID, $translationFilter, $targetLanguageID);
    }

    // Kategori filtresi uygula
    if (!empty($categoryID) && $categoryID > 0) {
        $pagesResult = array_filter($pagesResult, function($page) use ($categoryID) {
            return $page['pageCategoryID'] == $categoryID;
        });
    }

    // Arama filtresi uygula
    if (!empty($searchText)) {
        $pagesResult = array_filter($pagesResult, function($page) use ($searchText) {
            return stripos($page['pageName'], $searchText) !== false;
        });
    }
    if (empty($pagesResult) && !empty($searchText)) {
        $mainLanguageID = $adminLanguageModel->getMainLanguageID();
        if ($languageID != $mainLanguageID) {
            $pagesResult = $adminPageModel->getPageBySearch($languageID, $searchText);
        }
    }

    if(!empty($pagesResult)){
        // Her sayfa için çeviri durumu detaylarını ekle
        foreach ($pagesResult as &$page) {
            if (isset($page['pageID'])) {
                // Modelden gelen isMainLanguage değeri, sayfanın ait olduğu kategorinin dilinin ana dil olup olmadığını belirtir.
                // Bu değeri doğrudan kullanıyoruz.
                $isMainLanguage = (bool)($page['isMainLanguage'] ?? false);
                $page['isMainLanguage'] = $isMainLanguage; // Frontend'e gönderilecek nihai değer

                if ($isMainLanguage) {
                    // Ana dildeyse çeviri durumunu getir
                    $page['translationDetails'] = $adminPageModel->getPageTranslationStatus($page['pageID']);
                    $page['mainLanguageEquivalent'] = null; // Ana dildeki sayfanın ana dil karşılığı olmaz
                } else {
                    // Ana dilde değilse, ana dil karşılığını bul
                    $mainLangEquivalent = $adminPageModel->getMainLanguageEquivalent($page['pageID'], $languageID);
                    $page['mainLanguageEquivalent'] = $mainLangEquivalent;

                    // Eğer ana dil karşılığı varsa (yani bu sayfa ana dildeki bir sayfanın çevirisi ise),
                    // orijinal sayfanın bu dile olan çeviri durumunu getir.
                    if ($mainLangEquivalent) {
                        $translationStatus = $adminPageModel->getSpecificTranslationStatus($mainLangEquivalent['mainPageID'], $languageID);
                        $page['translationDetails'] = $translationStatus ? [$translationStatus] : [];
                    } else {
                        // Eğer ana dil karşılığı yoksa (yani bu sayfa ikincil dilde orijinal bir sayfa ise),
                        // bu sayfanın diğer dillere olan çeviri durumunu getir.
                        $page['translationDetails'] = $adminPageModel->getPageTranslationStatus($page['pageID']);
                    }
                }
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'pages' => array_values($pagesResult) // Array'i yeniden indeksle
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'warning',
        'message' => 'Sayfa bulunamadı.'
    ]);
}
elseif($action == "getPageTranslationStatus"){
    $pageID = $requestData["pageID"] ?? null;
    
    if (empty($pageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa ID gereklidir'
        ]);
        exit();
    }
    
    $translationStatus = $adminPageModel->getPageTranslationStatus($pageID);
    
    echo json_encode([
        'status' => 'success',
        'translationStatus' => $translationStatus
    ]);
}
elseif($action == "triggerTranslation"){
    // Sayfa çevirisi tetikleme
    $pageID = $requestData["pageID"] ?? null;
    $targetLanguageIDs = $requestData["targetLanguageIDs"] ?? [];
    $translateWithAI = $requestData["translateWithAI"] ?? true;
    
    if (empty($pageID) || empty($targetLanguageIDs)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sayfa ID ve hedef diller gereklidir'
        ]);
        exit();
    }
    
    
    try {
        $results = [];
        $allCategoryResults = [];
        
        foreach ($targetLanguageIDs as $targetLanguageID) {
            // Model katmanındaki yeni metodu kullan - kategori kontrolü dahil
            $translationResult = $adminLanguageModel->processPageTranslation(
                $pageID, 
                $targetLanguageID, 
                $translateWithAI
            );
            
            if ($translationResult['status'] === 'success') {
                $results[] = [
                    'targetLanguageID' => $targetLanguageID,
                    'result' => 'success',
                    'pageAction' => $translationResult['pageAction'],
                    'processedCategories' => $translationResult['processedCategories'],
                    'translationStatus' => $translationResult['translationStatus']
                ];
                
                // Kategori sonuçlarını topla
                $allCategoryResults = array_merge($allCategoryResults, $translationResult['processedCategories']);
                
            } else {
                $results[] = [
                    'targetLanguageID' => $targetLanguageID,
                    'result' => 'error',
                    'message' => $translationResult['message']
                ];
            }
        }
        
        // Başarılı işlemler
        $successfulTranslations = array_filter($results, function($r) { return $r['result'] === 'success'; });
        $failedTranslations = array_filter($results, function($r) { return $r['result'] === 'error'; });
        
        // Kategori işlem özeti
        $copiedCategories = array_filter($allCategoryResults, function($c) { return $c['action'] === 'copied'; });
        $existingCategories = array_filter($allCategoryResults, function($c) { return $c['action'] === 'existing'; });
        
        $totalSuccessful = count($successfulTranslations);
        $totalFailed = count($failedTranslations);
        $totalCopiedCategories = count($copiedCategories);
        $totalExistingCategories = count($existingCategories);
        
        $message = "Çeviri işlemi tamamlandı. ";
        $message .= "{$totalSuccessful} sayfa başarıyla işlendi";
        if ($totalFailed > 0) {
            $message .= ", {$totalFailed} sayfa işlenemedi";
        }
        $message .= ". {$totalCopiedCategories} kategori kopyalandı, {$totalExistingCategories} kategori zaten mevcuttu.";
        
        echo json_encode([
            'status' => $totalFailed === 0 ? 'success' : 'partial_success',
            'message' => $message,
            'results' => $results,
            'summary' => [
                'totalPages' => count($targetLanguageIDs),
                'successfulPages' => $totalSuccessful,
                'failedPages' => $totalFailed,
                'copiedCategories' => $totalCopiedCategories,
                'existingCategories' => $totalExistingCategories
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Çeviri işlemi sırasında hata oluştu: ' . $e->getMessage()
        ]);
    }
}
elseif($action == "getTranslationSummary"){
    // Çeviri özeti
    $languageID = $requestData["languageID"] ?? 1;
    $languages = $adminLanguageModel->getLanguages();
    
    // Ana dil ID'sini dinamik olarak al
    $mainLanguageID = null;
    foreach ($languages as $lang) {
        if ($lang['isMainLanguage'] == 1) {
            $mainLanguageID = $lang['languageID'];
            break;
        }
    }
    
    if (!$mainLanguageID) {
        $mainLanguageID = 1; // Varsayılan
    }
    
    $summary = [];
    $totalPages = 0;
    
    // Ana dildeki toplam sayfa sayısı
    $sql = "SELECT COUNT(*) as total FROM sayfa s 
            INNER JOIN sayfalistekategori slk ON s.sayfaid = slk.sayfaid 
            INNER JOIN kategori k ON slk.kategoriid = k.kategoriid 
            WHERE s.sayfasil = 0 AND s.sayfaaktif = 1 AND k.dilid = :languageID";
    
    $totalResult = $db->select($sql, ['languageID' => $languageID]);
    if (!empty($totalResult)) {
        $totalPages = $totalResult[0]['total'];
    }
    
    foreach ($languages as $language) {
        if ($language['languageID'] == $languageID) continue; // Ana dili atla
        
        // Bu dil için çeviri istatistikleri
        $sql = "SELECT 
                    COUNT(CASE WHEN lpm.translation_status = 'completed' THEN 1 END) as completed,
                    COUNT(CASE WHEN lpm.translation_status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN lpm.translation_status = 'failed' THEN 1 END) as failed,
                    COUNT(lpm.id) as total_mappings
                FROM sayfa s 
                INNER JOIN sayfalistekategori slk ON s.sayfaid = slk.sayfaid 
                INNER JOIN kategori k ON slk.kategoriid = k.kategoriid 
                LEFT JOIN language_page_mapping lpm ON (s.sayfaid = lpm.original_page_id AND lpm.dilid = :targetLanguageID)
                WHERE s.sayfasil = 0 AND s.sayfaaktif = 1 AND k.dilid = :languageID";
        
        $stats = $db->select($sql, [
            'languageID' => $languageID,
            'targetLanguageID' => $language['languageID']
        ])[0] ?? [];
        
        $completed = (int)($stats['completed'] ?? 0);
        $pending = (int)($stats['pending'] ?? 0);
        $failed = (int)($stats['failed'] ?? 0);
        $untranslated = $totalPages - $completed - $pending - $failed;
        
        $summary[] = [
            'languageID' => $language['languageID'],
            'languageName' => $language['languageName'],
            'languageCode' => $language['languageCode'],
            'completed' => $completed,
            'pending' => $pending,
            'failed' => $failed,
            'untranslated' => max(0, $untranslated),
            'total' => $totalPages,
            'completionRate' => $totalPages > 0 ? round(($completed / $totalPages) * 100, 1) : 0
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'summary' => $summary,
        'totalPages' => $totalPages,
        'mainLanguageID' => $mainLanguageID
    ]);
}
elseif($action == "createSeoLink"){
    $title = $requestData["title"] ?? null;
    $languageCode = $requestData["languageCode"] ?? 'tr';

    if(empty($title)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Başlık boş olamaz'
        ]);
        exit();
    }

    include_once Helpers . 'Helper.php';
    $helper = new Helper();

    $seoLink = $helper->createAdvancedSeoLink($title, $languageCode);

    echo json_encode([
        'status' => 'success',
        'seoLink' => $seoLink
    ]);
    exit();
}

function deleteJsonForPage($pageUniqID){
    $filePath = JSON_DIR . 'Page/'.$pageUniqID.'.json';
    if(file_exists($filePath)){
        unlink($filePath);
    }
}


function deleteJsonByCategoryId($categoryId){
    global $adminCategoryModel;
    $categoryUniqID = $adminCategoryModel->getCategoryUniqID($categoryId);

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

    $productCategoryTopCategoryID = $adminCategoryModel->getCategory($categoryId)['topCategoryID'] ?? null;
    if($productCategoryTopCategoryID) {
        $filePath = JSON_DIR . "Category/Subcategories/" . $productCategoryTopCategoryID . ".json";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}