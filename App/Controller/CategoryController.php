<?php
/**
 * @var Session $session
 * @var Config $config
 * @var Database $db
 * @var Json $json
 * @var array $routerResult
 */

################# SCHEMA.ORG OLUŞTURMA #######################
$helper = $config->Helper;
$json = $config->Json;
$casper = $session->getCasper();
$siteConfig = $casper->getSiteConfig();
$routerResult = $session->getSession("routerResult");
//print_r($routerResult);exit();

$config->includeClass("BannerManager");

$resultQuery = $routerResult["query"];
//print_r($resultQuery);exit();

$categoryLink = !empty(($resultQuery)) ? $routerResult["seoLink"]."?".$resultQuery : $routerResult["seoLink"];

$companySettings = $siteConfig['companySettings'];

$logoSettings = $casper->getSiteConfig()["logoSettings"];
$logoImg = $logoSettings["resim_url"];

$contentData = [
    'title' => $routerResult['seoTitle'],
    'description' => $routerResult['seoDescription'],
    'url' => $routerResult['seoLink'],
    'siteName' => $companySettings['ayarfirmakisaad'],
    'logo' => $config->http.$config->hostDomain."/".imgRoot.$logoImg
];

$schemaGenerator = new SchemaGenerator();
$schema = $schemaGenerator->generateSchema('category', $contentData);
$casper->setSchema($schema);
$session->updateSession("casper", $casper);

################# GET CASPER #################################
$casper = $session->getCasper();

################# GEREKLİ MODELLER ############################

$config->includeClass("Category");
$content = new Category($db,$json);
$category = $content->getCategoryByIdOrUniqId(0,$routerResult["contentUniqID"]);
if(!empty($category)){
    $category = $category[0];
}

$categoryType = $category["kategorigrup"];

################# GET SUBCATEGORIES ############################
$subCategories = $content->getSubCategories($category["kategoriid"]);

$subCategoriesDetails = [];
if(!empty($subCategories)){
    foreach ($subCategories as $i => $subCategory) {
        $subCategoriesDetails[$i] = $content->getCategoryByIdOrUniqId($subCategory["kategoriid"],"")[0] ?? [];
    }
}


$category["subCategories"] = $subCategoriesDetails;

################# GET CATEGORY HIERARCHY ########################
$categoryHierarchy = $content->getCategoryHierarchy($category["kategoriid"],true);
$category["categoryHierarchy"] = $categoryHierarchy;


################# GET PAGES OF CATEGORY #########################
$query = urldecode($resultQuery);

$query = str_replace('+', '*|', $query);

parse_str($query,$parsedQuery);
array_walk_recursive($parsedQuery, function (&$value) {
    $value = str_replace('*|', '+', $value);
});
$categoryPagesSort = $category['kategorisiralama'];
$categoryPages = $content->getPagesOfCategory($category["kategoriid"],$parsedQuery,$categoryPagesSort);

################# IF CATEGORY IS PRODUCT ########################
$casper = $session->getCasper();
$cssContents = $casper->getCssContents();
$jsContents = $casper->getJsContents();

################# BANNER İÇERİĞİ #########################

// Banner Manager'ı başlat
$bannerManager = BannerManager::getInstance();
$bannerManager->initialize($siteConfig['bannerInfo'], $casper);

// Tüm banner tiplerini render et (cache'li)
$bannerResults = $bannerManager->renderAllBannerTypes(null,$category["kategoriid"] );

$sliderBanners = $bannerResults['types'][1] ?? ['html' => '', 'css' => '', 'js' => ''];
$sliderBannersHtml = $sliderBanners['html'];
$category['sliderBanner'] = $sliderBannersHtml;

$middleBanners = $bannerResults['types'][3] ?? ['html' => '', 'css' => '', 'js' => ''];
$middleBannersHtml = $middleBanners['html'];
$category['middleBanner'] = $middleBannersHtml;

$cssContents .= $bannerResults['all_css'];
$jsContents .= $bannerResults['all_js'];

if($category["kategorigrup"]==7){

    ################# CSS VE JS DOSYALARI ########################

    $cssContents .= file_get_contents(CSS.'ProductCategory/ProductCategory.min.css');

    ################# GEREKLİ MODELLER ############################

    $config->includeClass('Product');
    $productModel = new Product($db,$json);


    ################# GET FILTER DATA #############################
    $filterData = [];

    foreach ($categoryPages as $i => $categoryPage) {

        $productDetails = $productModel->getProductByID($categoryPage['sayfaid']);

        $categoryPages[$i]["productDetails"] = $productDetails;

        if(!empty($productDetails)){
            $productVariantProperties = $productDetails[0]["variantProperties"] ?? [];

            if(!empty($productVariantProperties)){

                //jsonu dizi yapalım
                $productVariantProperties = json_decode($productVariantProperties, true);

                foreach ($productVariantProperties as $productVariantProperty) {

                    $variantProperties = $productVariantProperty["variantProperties"];

                    foreach ($variantProperties as $variantProperty) {
//Log::write("CategoryController: getCategoryByPageID -> variantProperty: " . json_encode($productDetails), "info");
                        $attribute = $variantProperty["attribute"] ?? [];
                        if(empty($attribute)){
                            continue;
                        }

                        $attributeName = $attribute["name"];
                        $attributeValue = $attribute["value"];

                        if (!isset($filterData[$attributeName])) {
                            $filterData[$attributeName] = [];
                        }

                        //aynı değerlerin tekrar eklenmemesi için kontrol
                        if (!in_array($attributeValue, $filterData[$attributeName])) {
                            $filterData[$attributeName][] = $attributeValue;
                        }

                    }
                }
            }
        }
    }

    //print_r($filterData);exit();

    if(!empty($filterData)){
        foreach ($filterData as $key => $value) {
            $filterData[$key] = array_filter($value, function($item) {
                return !empty($item);
            });
        }
    }

    //print_r($filterData);exit();

    ################# GET SELECTED FILTER DATA ####################
    $selectedFilterData = [];
    if(!empty($resultQuery)&&!empty($categoryPages)&&!empty($filterData)){

        foreach ($parsedQuery as $selectedFilterGroupName => $selectedFilterValue) {
            //yakalama ve ayıklamaya başlayalım
            if(isset($filterData[$selectedFilterGroupName])){

                //seçilen filtre değerini filtre datasından çıkaralım
                $filterData[$selectedFilterGroupName] = array_diff($filterData[$selectedFilterGroupName],[$selectedFilterValue]);
            }

            $selectedFilterData[$selectedFilterGroupName] = [$selectedFilterValue];
        }
    }
    //print_r($selectedFilterData);exit();


    $filterLinks = [];
    foreach ($filterData as $filterGroupName => $filterGroupValues) {

        $filterLinks[$filterGroupName] = [];

        foreach ($filterGroupValues as $filterGroupValue) {
            // Filtreyi encode et
            $encodedFilter = urlencode($filterGroupValue);
            $filterLink = $filterGroupName . "=" . $encodedFilter;

            // Geçici filtre linkini oluştur
            $tempFilterLink = $categoryLink;

            // Mevcut query string'inden bu filtreyi kaldır
            $tempFilterLink = str_replace("&" . $filterLink, "", $tempFilterLink);
            $tempFilterLink = str_replace("?" . $filterLink, "", $tempFilterLink);

            // Geçici link query string içeriyorsa
            if (strpos($tempFilterLink, '?') !== false) {
                // Sonda "?" varsa "&" ekleyelim
                $filterLink = $tempFilterLink . "&" . $filterLink;
            } else {
                // Yoksa "?" ekleyelim
                $filterLink = $tempFilterLink . "?" . $filterLink;
            }

            // Filtre linklerini array'e ekle
            $filterLinks[$filterGroupName][] = [$filterGroupValue => $filterLink];
        }
    }


    $selectedFilterLinks = [];
    //$categoryLink = urldecode($categoryLink);

    foreach ($selectedFilterData as $selectedFilterGroupName => $selectedFilterGroupValues) {

        $selectedFilterLinks[$selectedFilterGroupName] = [];

        foreach ($selectedFilterGroupValues as $selectedFilterGroupValue) {

            $selectedFilterLink = $selectedFilterGroupName . "=" . $selectedFilterGroupValue;

            $selectedFilterLinkEncode = urlencode($selectedFilterGroupName) . "=" . urlencode($selectedFilterGroupValue);

            if(str_contains($categoryLink, $selectedFilterLink)){
                $selectedFilterLink = str_replace($selectedFilterLink,"", $categoryLink);
            }else{
                $selectedFilterLink = str_replace($selectedFilterLinkEncode,"", $categoryLink);
            }

            $selectedFilterLink = str_replace("?&", "?", $selectedFilterLink);
            $selectedFilterLink = str_replace("&&", "&", $selectedFilterLink);

            $selectedFilterLinks[$selectedFilterGroupName][] = [ $selectedFilterGroupValue => $selectedFilterLink];
        }
    }

    //print_r($selectedFilterLinks);exit();
}
else{

    ################# CSS VE JS DOSYALARI ########################

    $cssContents .= file_get_contents(CSS.'Category/Category.min.css');

    //blog ve sss sayfaları için css

    if ($categoryType == 24) {
        $cssContents .= file_get_contents(CSS.'Category/BlogPageBox.min.css');
    }
    if($categoryType == 26 || $categoryType == 24){
        $cssContents .= file_get_contents(CSS.'Category/Details-Summary.min.css');
    }

    $filterData = [];
    $filterLinks = [];
    $selectedFilterData = [];
    $selectedFilterLinks = [];

    $config->includeClass('Page');
    $pageModel = new Page($db,$session);

    $config->includeClass('SeoModel');
    $seoModel = new SeoModel($db);

    foreach ($categoryPages as $i => $categoryPage) {
        $pageDetails = $pageModel->getPageById($categoryPage['sayfaid'],"");

        $pageSeo = $seoModel->getSeoByUniqId($pageDetails['benzersizid']);
        $pageSeoLink = $pageSeo['seoLink'] ?? "";

        $pageDetails['seoLink'] = $pageSeoLink;

        $categoryPages[$i]["pageDetails"] = $pageDetails;
    }
}

$categoryUniqID = $routerResult["contentUniqID"];
$customCssFile = CSS . 'Category/CustomCSS/' . $categoryUniqID . '.css';
if(file_exists($customCssFile)){
    $cssContents .= file_get_contents($customCssFile);
}

################# UPDATE SESSION ##############################
$session->addSession("category", [
        "category"=>$category,
        "categoryPages"=>$categoryPages,
        "filterData"=>$filterData,
        "filterLinks"=>$filterLinks,
        "selectedFilterData"=>$selectedFilterData,
        "selectedFilterLinks"=>$selectedFilterLinks
    ]
);

################# UPDATE CASPER ##############################
$casper->setCssContents($cssContents);
$casper->setJsContents($jsContents);
$session->updateSession("casper",$casper);
