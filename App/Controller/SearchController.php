<?php

/**
 * @var Config $config
 * @var Database $db
 * @var Json $json
 * @var Session $session
 * @var array $routerResult
 */
$helper = $config->Helper;
$json = $config->Json;

$routerResult = $session->getSession("routerResult");
//print_r($routerResult);exit();
$resultQuery = urldecode($routerResult["query"]);
//print_r($resultQuery);exit();

$searchLink = "/?$resultQuery";

$query = str_replace('+', '%2B', $resultQuery);
parse_str($query,$parsedQuery);
array_walk_recursive($parsedQuery, function (&$value) {
    $value = str_replace('%2B', '+', $value);
});

$q = $parsedQuery['q'] ?? '';

$config->includeClass("BannerManager");

$languageID= $parsedQuery['languageID'] ?? 1;

include_once MODEL.'Language.php';
$languageModel = new Language($db,"",$languageID);
$languageCode = $languageModel->getLanguageCode($languageID);
$languageModel->getTranslations($languageCode);

$seoTitle = _arama_sayfasi_arama_baslik;
$seoDescription = _arama_sayfasi_arama_baslik;
$seoLink = "/";
$seoKeywords = _arama_sayfasi_arama_baslik;
$seoImage = "";


$routerResult['seoTitle'] = $seoTitle;
$routerResult['seoDescription'] = $seoDescription;
$routerResult['seoLink'] = $seoLink;
$routerResult['seoKeywords'] = $seoKeywords;
$routerResult['seoImage'] = $seoImage;

$session->updateSession("routerResult",$routerResult);

################# SCHEMA.ORG OLUŞTURMA #######################
$casper = $session->getCasper();
$siteConfig = $casper->getSiteConfig();

$siteConfigInfo = new SiteConfig($db,$languageID);
$siteConfigVersion = $siteConfigInfo->siteConfigVersion;

$currentSiteConfigVersion = $siteConfig['siteConfigVersion'] ?? -1;

if(empty($siteConfig) || $siteConfig["generalSettings"]["dilid"]!=$languageID || $siteConfigVersion!=$currentSiteConfigVersion){
    // BannerManager önbelleğini temizle
    $bannerManager = BannerManager::getInstance();
    $bannerManager->onSiteConfigChange();

    $siteConfigInfo->createSiteConfig();
    $casper->setSiteConfig($siteConfigInfo->getSiteConfig());
    $session->updateSession("casper",$casper);
    $siteConfig = $casper->getSiteConfig();
}

$logoSettings = $casper->getSiteConfig()["logoSettings"] ?? '';

$logoImg = $logoSettings["resim_url"];

$seoImage = empty($seoImage) ? $config->http.$config->hostDomain."/".imgRoot.$logoImg : $seoImage;

$contentData = [
    'title' => $routerResult['seoTitle'],
    'description' => $routerResult['seoDescription'],
    'url' => $routerResult['seoLink'],
    'siteName' => $siteConfig['companySettings']['ayarfirmakisaad'],
    'logo' => $config->http.$config->hostDomain."/".imgRoot.$logoImg
];

$schemaGenerator = new SchemaGenerator();
$schema = $schemaGenerator->generateSchema('search', $contentData);
$casper->setSchema($schema);
$session->updateSession("casper", $casper);


################# GEREKLİ MODELLERİ YÜKLEYELİM ######################

$config->includeClass("ProductSearch");
$config->includeClass("Product");

$content = new ProductSearch($db,$json);

$searchResult = $content->productSearch($resultQuery);

$searchResultProductIDs = $searchResult["searchResultProductIDs"] ?? [];

$searchResultTotalPages = $searchResult["searchResultTotalPages"] ?? 1;

$searchResultCurrentPage = $searchResult["searchResultCurrentPage"] ?? 1;

$searchTotalResults = $searchResult["searchTotalResults"] ?? 0;

$searchResultPerPage = $searchResult["searchResultsPerPage"] ?? 0;

$searchResultUniqID = $searchResult["searchResultUniqID"] ?? "";

$filterData = [];

$productModel = new Product($db, $json);
$searchResultProducts = [];

foreach ($searchResultProductIDs as $searchResultProductId) {

    $searchResultProduct = $productModel->getProductByID($searchResultProductId);

    if(!empty($searchResultProduct)){

        $productVariantProperties = $searchResultProduct[0]["variantProperties"] ?? [];

        if(!empty($productVariantProperties)){

            //jsonu dizi yapalım
            $productVariantProperties = json_decode($productVariantProperties, true);

            foreach ($productVariantProperties as $productVariantProperty) {

                $variantProperties = $productVariantProperty["variantProperties"];

                foreach ($variantProperties as $variantProperty) {

                    $attribute = $variantProperty["attribute"];

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

    $searchResultProducts[] = $searchResultProduct;
}

//boş değer varsa silelim
if(!empty($filterData)){
    foreach ($filterData as $key => $value) {
        $filterData[$key] = array_filter($value, function($item) {
            return !empty($item);
        });
    }
}

//print_r($filterData);exit();

$selectedFilterData = [];
if(!empty($resultQuery)&&!empty($searchResultProducts)&&!empty($filterData)){

    $query = str_replace('+', '%2B', $resultQuery);

    parse_str($query, $parseQuery);

    array_walk_recursive($parsedQuery, function (&$value) {
        $value = str_replace('%2B', '+', $value);
    });

    unset($parseQuery['languageID']);
    unset($parseQuery['q']);
    unset($parseQuery['page']);
    unset($parseQuery['limit']);
    unset($parseQuery['sayfa']);
    unset($parseQuery['dilid']);

    //print_r($parseQuery);exit();

    foreach ($parseQuery as $selectedFilterGroupName => $selectedFilterValue) {
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

        $filterLink = $searchLink."&".$filterGroupName."=".urlencode($filterGroupValue);

        $filterLinks[$filterGroupName][] = [$filterGroupValue => $filterLink];
    }
}

$selectedFilterLinks = [];
foreach ($selectedFilterData as $selectedFilterGroupName => $selectedFilterGroupValues) {

    $selectedFilterLinks[$selectedFilterGroupName] = [];

    foreach ($selectedFilterGroupValues as $selectedFilterGroupValue) {

        $selectedFilterLink = str_replace("&".$selectedFilterGroupName."=".$selectedFilterGroupValue,"", $searchLink);


        $selectedFilterLinks[$selectedFilterGroupName][] = [ $selectedFilterGroupValue => $selectedFilterLink];
    }
}
//print_r($selectedFilterLinks);exit();

$session->addSession("search",[
    "searchResultUniqueID" => $searchResultUniqID,
    "searchResultProducts" => $searchResultProducts,
    "searchResultTotalPages" => $searchResultTotalPages,
    "searchResultCurrentPage" => $searchResultCurrentPage,
    "searchTotalResults" => $searchTotalResults,
    "searchResultsPerPage" => $searchResultPerPage,
    "filterData" => $filterData,
    "filterLinks" => $filterLinks,
    "selectedFilterData" => $selectedFilterData,
    "selectedFilterLinks" => $selectedFilterLinks
]);

$casper = $session->getCasper();

$cssContents = $casper->getCssContents();
$cssContents .= file_get_contents(CSS.'Search/Search.min.css');

$jsContents = $casper->getJsContents();
$jsContents .= file_get_contents(JS.'search.min.js');

$casper->setJsContents($jsContents);
$casper->setCssContents($cssContents);
$session->updateSession("casper",$casper);