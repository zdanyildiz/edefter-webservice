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

include_once MODEL . "Admin/AdminCategory.php";
$adminCategory = new AdminCategory($db);

include_once MODEL . 'Admin/GeneralSettings.php';
$generalSettingsModel = new GeneralSettings($db);

if($action == "getCategory") {
    $categoryID = $requestData['categoryID'] ?? '';

    if(empty($categoryID) or !is_numeric($categoryID)){
        echo json_encode(['status' => 'error', 'message' => 'Category ID is required']);
        exit();
    }

    $category = $adminCategory->getCategoryByIdOrUniqId($categoryID,"");
    if(empty($category)){
        echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        exit();
    }
    echo json_encode(['status' => 'success', 'category' => $category]);
}
elseif($action == "getSubCategory"){
    $categoryID = $requestData['categoryID'] ?? '';

    if(empty($categoryID) or !is_numeric($categoryID)){
        echo json_encode(['status' => 'error', 'message' => 'Category ID is required']);
        exit();
    }

    $subCategories = $adminCategory->getSubCategory($categoryID);
    if(empty($subCategories)){
        echo json_encode(['status' => 'error', 'message' => 'Sub Categories not found']);
        exit();
    }

    echo json_encode(['status' => 'success', 'subCategories' => $subCategories]);
    exit();
}
elseif( $action == "getSubCategoriesWithInfo"){
    $categoryID = $requestData["categoryID"] ?? null;
    $languageID = $requestData["languageID"] ?? 1; // Dil ID'sini al
    $searchText = $requestData["searchText"] ?? '';
    $translationFilter = $requestData["translationFilter"] ?? 'all';

    if($categoryID == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category ID is empty'
        ]);
        exit();
    }

    // getCategoriesWithTranslationStatus metodunu kullanarak alt kategorileri ve çeviri durumlarını al
    $subCategories = $adminCategory->getCategoriesWithTranslationStatus($languageID, $translationFilter, $searchText, $categoryID);

    if(empty($subCategories)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sub categories not found'
        ]);
        exit();
    }
    else{
        echo json_encode([
            'status' => 'success',
            'message' => 'Sub categories found',
            'subCategories' => $subCategories
        ]);
    }
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

    $subCategories = $adminCategory->getSubCategories($categoryID);

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
elseif( $action == "addCategory" || $action == "updateCategory"){

    $updateDate = date("Y-m-d H:i:s");
    $categoryID = $requestData["categoryID"];
    $categoryTopCategoryID = $requestData["categoryTopCategoryID"] ?? 0;
    $categoryLayer = $categoryTopCategoryID>0 ? 1 : 0;
    $categoryName = $requestData["categoryName"];
    $categoryImageID = $requestData["imageID"] ?? null;
    $categoryContent = $requestData["categoryContent"];
    $categoryLink = $requestData["categoryLink"];
    $categoryOrder = $requestData["categorySorting"] ?? 0;
    $categorySorting = $requestData["categorySorting"];
    $categoryType = $requestData["categoryType"];
    $categoryHomePage = $requestData["categoryHomePage"] ?? 0;
    $categoryActive = $requestData["categoryActive"];
    $categoryDeleted = 0;
    $customCSS = $requestData["customCSS"] ?? "";

    if(!empty($categoryImageID)){
        $categoryImageID = $categoryImageID[0];
    }
    else{
        $categoryImageID = 0;
    }

    $categorySeoTitle = $requestData["categorySeoTitle"];
    $categorySeoDescription = $requestData["categorySeoDescription"];
    $categorySeoKeywords = $requestData["categorySeoKeywords"];


    //action update ise kategoriid boş olamaz
    if($action == "updateCategory" && empty($categoryID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category ID is empty'
        ]);
        exit();
    }


    if(empty($categoryName) || empty($categoryLink) || empty($categorySeoTitle) || empty($categorySeoDescription) || empty($categorySeoKeywords)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category name, category link, seo title, seo description, seo keywords can not be empty'
        ]);
        exit();
    }

    $updateData = [
        'updateDate' => $updateDate,
        'categoryID' => $categoryID,
        'topCategoryID' => $categoryTopCategoryID,
        'categoryLayer' => $categoryLayer,
        'categoryName' => $categoryName,
        'categoryImageID' => $categoryImageID,
        'categoryContent' => $categoryContent,
        'categoryLink' => $categoryLink,
        'categoryOrder' => $categoryOrder,
        'categorySorting' => $categorySorting,
        'categoryType' => $categoryType,
        'categoryHomePage' => $categoryHomePage,
        'categoryActive' => $categoryActive,
        'categoryDeleted' => $categoryDeleted
    ];

    if($action == "addCategory"){
        $createDate = $updateDate;
        $languageID = $requestData["languageID"] ?? 1;
        $categoryUniqID = $helper->createPassword("20","2");

        $insertData = [
            'languageID' => $languageID,
            'createDate' => $createDate,
            'updateDate' => $updateDate,
            'topCategoryID' => $categoryTopCategoryID,
            'categoryLayer' => $categoryLayer,
            'categoryName' => $categoryName,
            'categoryImageID' => $categoryImageID,
            'categoryContent' => $categoryContent,
            'categoryLink' => $categoryLink,
            'categoryOrder' => $categoryOrder,
            'categorySorting' => $categorySorting,
            'categoryType' => $categoryType,
            'categoryHomePage' => $categoryHomePage,
            'categoryActive' => $categoryActive,
            'categoryDeleted' => $categoryDeleted,
            'categoryUniqID' => $categoryUniqID,
        ];
    }

    $adminCategory->beginTransaction("$action");

    if($action == "addCategory"){

        $insertResult = $adminCategory->insertCategory($insertData);

        if($insertResult['status'] == 'error'){
            $adminCategory->rollback("$action / kategori ekle");
            echo json_encode($insertResult);
            exit();
        }

        $categoryID = $insertResult['categoryID'];
    }
    else{

        $updateCategoryResult = $adminCategory->updateCategory($updateData);
        Log::adminWrite('updateCategoryResult: '. json_encode($updateCategoryResult,JSON_PRETTY_PRINT),"info");
        if($updateCategoryResult['status'] == 'error'){
            $adminCategory->rollback("$action / kategori güncelle");
            echo json_encode($updateCategoryResult);
            exit();
        }

        $categoryUniqID = $adminCategory->getCategoryUniqID($categoryID);
    }

    $domain = $generalSettingsModel->getDomainByLanguageID($languageID);

    $link = "";

    include_once MODEL . 'Admin/AdminLanguage.php';
    $adminLanguageModel = new AdminLanguage($db);

    $languageCode = $adminLanguageModel->getLanguageCode($languageID);
    $languageCode = $helper->toLowerCase($languageCode);

    $link = $link . "/" . $languageCode;

    $categoryHierarchy = $adminCategory->getCategoryHierarchy($categoryID);

    //print_r($categoryHierarchy);exit;

    $categoryPath = '';
    foreach ($categoryHierarchy as $category) {
        if($category['categoryID'] == $categoryID){
            continue;
        }

        $categoryName = $category['categoryName'];
        $categoryPath .= '/' . $helper->createAdvancedSeoLink($categoryName,$languageCode);
    }

    //die($categoryPath);

    $link = $link . $categoryPath;

    //die($link);

    $link = $link . $categoryLink;

    //die($link);

    include_once MODEL . 'Admin/AdminSeo.php';
    $adminSeoModel = new AdminSeo($db);

    $seoImage = '';
    if (!empty($categoryID)) {

        $categoryImages = $adminCategory->getCategoryImages($categoryID);

        if (!empty($categoryImages)) {
            $seoImage = $config->http.$domain.imgRoot.$categoryImages['imagePath'];
        }
    }


    $categorySeoOriginalLink = '';

    if( $action == "updateCategory") {

        $getSeo = $adminSeoModel->getSeoByUniqId($categoryUniqID);

        if (!empty($getSeo)) {
            $categorySeoOriginalLink = $getSeo['seoLink'] ?? $categorySeoOriginalLink;
        }

        if ($link !== $categorySeoOriginalLink && $categorySeoOriginalLink !== '') {

            include_once MODEL . 'Admin/AdminBanner.php';
            $adminBannerModel = new AdminBanner($db);

            $updateBannerLink = [
                'newBannerlink' => $link,
                'oldBannerlink' => $categorySeoOriginalLink
            ];

            $updateBannerLinkResult = $adminBannerModel->updateBannerLinkByLink($updateBannerLink);

            $updateBannerLink = [
                'newBannerlink' => $link,
                'oldBannerlink' => $config->http.$domain.$categorySeoOriginalLink
            ];

            $updateBannerLinkResult = $adminBannerModel->updateBannerLinkByLink($updateBannerLink);

            include_once MODEL . 'Admin/AdminMenu.php';
            $adminMenuModel = new AdminMenu($db);

            $updateMenuLink = [
                'newMenulink' => $link,
                'oldMenulink' => $categorySeoOriginalLink
            ];

            $updateMenuLinkResult = $adminMenuModel->updateMenuLinkByLink($updateMenuLink);

            $updateMenuLinkResult = $adminMenuModel->updateMenuLinkByMenuOrijinalUniqID($categoryUniqID,$link);
        }
    }


    $insertSeoData = [
        'seoTitle' => $categorySeoTitle,
        'seoDescription' => $categorySeoDescription,
        'seoKeywords' => $categorySeoKeywords,
        'seoLink' => $link,
        'seoImage' => $seoImage,
        'seoUniqID' => $categoryUniqID
    ];

    if($link !== $categorySeoOriginalLink && $categorySeoOriginalLink !== ''){
        $insertSeoData['seoOriginalLink'] = $categorySeoOriginalLink;
    }

    if( $action == "addCategory"){

        $insertSeoData['seoOriginalLink'] = '';
        $insertSeoResult = $adminSeoModel->insertSeo($insertSeoData);

        if($insertSeoResult['status'] == 'error'){
            $adminCategory->rollback("$action / Seo");
            echo json_encode($insertSeoResult);
            exit();
        }
    }
    else{

        $updateSeoResult = $adminSeoModel->updateSeo($insertSeoData);
        Log::adminWrite('updateSeoResult: '. json_encode($updateSeoResult,JSON_PRETTY_PRINT),'info');
        if($updateSeoResult['status'] == 'error'){
            $adminCategory->rollback("$action /seo");
            echo json_encode($updateSeoResult);
            exit();
        }
    }

    $adminCategory->commit("updateCategory");

    $filePath = JSON_DIR . "Category/".$categoryUniqID.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    //$filePath = JSON_DIR . "Category/Pages altında kategori $categoryID-" ile başlayan bütün jsonları sil
    $filePath = JSON_DIR . "Category/Pages/";
    $files = glob($filePath . $categoryID."-*");
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    //$filePath = JSON_DIR . "Category/Subcategories altında kategori id ve üst kategori id .json olan dosyaları sil
    $filePath = JSON_DIR . "Category/Subcategories/";
    $files = glob($filePath . $categoryID.".json");
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    $filePath = JSON_DIR . "Category/Subcategories/".$categoryTopCategoryID.".json";
    if(file_exists($filePath)){
        unlink($filePath);
    }

    if($action == "updateCategory"){
        $filePath = JSON_DIR . "Menu/menu_".$languageID.".json";
        if(file_exists($filePath)){
            unlink($filePath);
        }
    }

    $customCssFile = CSS . 'Category/CustomCSS/' . $categoryUniqID . '.css';
    if(!empty($customCSS)){
        $customCssDir = CSS . 'Category/CustomCSS/';
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



    $message = $action == "addCategory" ? "Kategori Kaydedildi" : "Kategori Güncellendi";

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'categoryID' => $categoryID,
        'categoryUniqID' => $categoryUniqID
    ]);
}
elseif( $action == "getCategoryWithInfoBySearch"){

    $searchText = $requestData["searchText"] ?? null;
    $languageID = $requestData["languageID"] ?? 1;

    if($searchText == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Search text is empty'
        ]);
        exit();
    }

    $getCategory = $adminCategory->getCategoryBySearch($searchText,$languageID);

    if(empty($getCategory)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Category not found'
        ]);
        exit();
    }
    else{

        foreach ($getCategory as $key => $category) {
            $getCategory[$key]['subCategoryCount'] = $adminCategory->getSubCategoryCount($category['categoryID']);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Category found',
            'categories' => $getCategory
        ]);
    }


}
elseif( $action == "getCategoriesWithInfo"){

    $languageID = $requestData["languageID"] ?? 1;

    $categories = $adminCategory->getCategories($languageID);

    if(empty($categories)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Product categories not found'
        ]);
        exit();
    }
    else{
        //getProductCategories sadece üst kategorileri alıyor, aldığımız kategorinin varsa alt kategori sayısını alalım
        foreach ($categories as $key => $category) {
            $categories[$key]['subCategoryCount'] = $adminCategory->getSubCategoryCount($category['categoryID']);
        }

        $categories = [
            'status' => 'success',
            'message' => 'Product categories found',
            'productCategories' => $categories
        ];
    }

    echo json_encode($categories);
    exit();

}
elseif( $action == "getCategories"){

    $languageID = $requestData["languageID"] ?? 1;

    $categories = $adminCategory->getCategories($languageID);

    if(empty($categories)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Product categories not found'
        ]);
        exit();
    }
    else{
        $categories = [
            'status' => 'success',
            'message' => 'Product categories found',
            'categories' => $categories
        ];
    }

    echo json_encode($categories);
    exit();

}
elseif ($action == "deleteCategory"){
    $categoryID = $requestData["categoryID"] ?? null;
    if ($categoryID == null){
        echo json_encode([
            'status' => 'error',
            'message' => 'Kategori id boş olamaz'
        ]);
        exit();
    }

    $isThereSubCategory = $adminCategory->getSubCategoryCount($categoryID);
    if ($isThereSubCategory > 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu kategoriye ait alt kategoriler bulunmaktadır. Lütfen önce alt kategorileri siliniz.'
        ]);
        exit();
    }

    $isTherePages = $adminCategory->getCategoryPageCount($categoryID);

    if ($isTherePages > 0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu kategoriye ait sayfalar bulunmaktadır. Lütfen önce sayfaları siliniz.'
        ]);
        exit();
    }

    $adminCategory->beginTransaction("deleteCategory");

    $deleteCategory = $adminCategory->deleteCategory($categoryID);
    if(!$deleteCategory){
        $adminCategory->rollback("deleteCategory");
        echo json_encode([
            'status' => 'error',
            'message' => 'Kategori silinemedi'
        ]);
        exit();
    }

    deleteJsonByCategoryId($categoryID);

    $adminCategory->commit("deleteCategory");

    echo json_encode([
        'status' => 'success',
        'message' => 'Kategori başarıyla silindi'
    ]);

}
elseif($action == "getCategoriesWithTranslationStatus"){
    // Kategorileri çeviri durumu ile birlikte al
    $searchText = $requestData["searchText"] ?? "";
    $translationFilter = $requestData["translationFilter"] ?? "all";
    $parentCategoryID = $requestData["parentCategoryID"] ?? 0;
    
    include_once MODEL . 'Admin/AdminLanguage.php';
    $adminLanguageModel = new AdminLanguage($db);
    
    try {
        $categories = $adminCategory->getCategoriesWithTranslationStatus($languageID, $translationFilter, $searchText, $parentCategoryID);
        
        echo json_encode([
            'status' => 'success',
            'categories' => $categories
        ]);
        exit();
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Kategoriler alınırken hata oluştu: ' . $e->getMessage()
        ]);
        exit();
    }
}
elseif($action == "triggerCategoryTranslation"){
    // Kategori çevirisi tetikleme
    $categoryIDs = $requestData["categoryIDs"] ?? [];
    $targetLanguageIDs = $requestData["targetLanguageIDs"] ?? [];
    $sourceLanguageID = $requestData["sourceLanguageID"] ?? null;
    
    if (empty($categoryIDs) || empty($targetLanguageIDs) || empty($sourceLanguageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Kategori ID\'leri, kaynak dil ve hedef diller gereklidir'
        ]);
        exit();
    }
    
    include_once MODEL . 'Admin/AdminLanguage.php';
    $adminLanguageModel = new AdminLanguage($db);
    
    try {
        $results = [];
        foreach ($categoryIDs as $categoryID) {
            foreach ($targetLanguageIDs as $targetLanguageID) {
                $translationResult = $adminLanguageModel->processCategoryTranslation(
                    $categoryID,
                    $targetLanguageID
                );
                $results[] = [
                    'categoryID' => $categoryID,
                    'targetLanguageID' => $targetLanguageID,
                    'result' => $translationResult
                ];
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Kategori çeviri işlemleri başlatıldı.',
            'results' => $results
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Kategori çevirisi başlatılırken hata oluştu: ' . $e->getMessage()
        ]);
    }
}
?>
