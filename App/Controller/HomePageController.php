<?php
/**
 * @var Config $config
 * @var Session $session
 * @var Database $db
 * @var Casper $casper
 */
$helper = $config->Helper;
$json = $config->Json;
$casper = $session->getCasper();
$routerResult = $session->getSession("routerResult");

################# GEREKLİ MODELLER ############################
$config->includeClass("Homepage");
$config->includeClass("Product");
$config->includeClass("BannerManager");

################# HOMEPAGE SINIFI TANIMLANIYOR ################
$homePage = new Homepage($db,$json,$routerResult['languageID']);

$languageID = $homePage->languageID;
$languageCode = $helper->toLowerCase($homePage->languageCode);

$homePageCategoryID = $homePage->homePageCategoryID;
$homePageCategoryContent = $homePage->homePageCategoryContent;
$homePageCategoryImage = $homePage->homePageCategoryImage;

$seoTitle = $homePage->seoTitle;
$seoDescription = $homePage->seoDescription;
$seoLink = $homePage->seoLink;
$seoKeywords = $homePage->seoKeywords;
$seoImage = $homePage->seoImage;

################# Dile göre site sabitlerini alalım ################

$languageModel =new Language($db,$languageCode);
$languageModel->getTranslations($languageCode);

################# SITE CONFIG ##############################
$siteConfig = $casper->getSiteConfig();

$siteConfigInfo = new SiteConfig($db,$languageID);
$siteConfigVersion = $siteConfigInfo->siteConfigVersion;

$currentSiteConfigVersion = $siteConfig['siteConfigVersion'] ?? -1;

if(empty($siteConfig) || $siteConfig["generalSettings"]["dilid"]!=$languageID || $siteConfigVersion!=$currentSiteConfigVersion){
    Log::write("HomePageController.php Dil Faktörü: ".$routerResult["languageID"]."!=".$languageID );
    Log::write("HomePageController.php Config Version: ".$currentSiteConfigVersion."!=".$siteConfigVersion);
    // BannerManager önbelleğini temizle
    $bannerManager = BannerManager::getInstance();
    $bannerManager->initialize([], $casper);
    $bannerManager->onSiteConfigChange();

    $siteConfigInfo->createSiteConfig();
    $casper->setSiteConfig($siteConfigInfo->getSiteConfig());
    $session->updateSession("casper",$casper);
    $siteConfig = $casper->getSiteConfig();
}

################# BANNER BİLGİLERİ ############################
$siteConfig = $casper->getSiteConfig();

################# BANNER MANAGER İLE OPTİMİZE EDİLMİŞ BANNER YÖNETİMİ #######
$casper = $session->getCasper();
$cssContents = $casper->getCssContents();
$jsContents = $casper->getJsContents();


// Banner Manager'ı başlat
$bannerManager = BannerManager::getInstance();
$bannerManager->initialize($siteConfig['bannerInfo'], $casper);

// Tüm banner tiplerini render et (cache'li)
$bannerResults = $bannerManager->renderAllBannerTypes(null, $homePageCategoryID);

// Slider bannerları için
$sliderBanners = $bannerResults['types'][1] ?? ['html' => '', 'css' => '', 'js' => ''];
$sliderBannersHtml = $sliderBanners['html'];

$middleBanners = $bannerResults['types'][3] ?? ['html' => '', 'css' => '', 'js' => ''];
$middleBannersHtml = $middleBanners['html'];

// CSS ve JS'i session'a ekle (tekrar yüklenmesini önlemek için)
$cssContents .= $bannerResults['all_css'];
$jsContents .= $bannerResults['all_js'];

$casper->setCssContents($cssContents);
$casper->setJsContents($jsContents);
$session->updateSession("casper",$casper);

################# SPECIAL OFFER PRODUCTS #####################
$homePageSpecialOfferProducts = $homePage->getSpecialOfferProducts($languageID);

################# HOMEPAGE PRODUCTS ##########################
$homePageHomepageProducts = $homePage->getHomepageProducts($languageID);

################# DISCOUNTED PRODUCTS ########################
$homePageDiscountedProducts = $homePage->getDiscountedProducts($languageID);

################# NEW PRODUCTS ################################
$homePageNewProducts = $homePage->getNewProducts($languageID);

################# SESSION UPDATE ##############################
$session->addSession("mainPage", [
    "homePageCategoryId" => $homePageCategoryID,
    "homePageSlider" => $sliderBannersHtml,
    "homePageMiddleBanner" => $middleBannersHtml,
    "homePageMiddleBanners"=>[],
    "homePageCarouselBanners"=>[],
    "homePageCategoryImage" =>$homePageCategoryImage,
    "homePageCategoryContent"=>$homePageCategoryContent,
    "homePageSpecialOfferProducts"=>$homePageSpecialOfferProducts,
    "homePageHomepageProducts"=>$homePageHomepageProducts,
    "homePageDiscountedProducts"=>$homePageDiscountedProducts,
    "homePageNewProducts"=>$homePageNewProducts
]);

$casper = $session->getCasper();
$cssContents = $casper->getCssContents();
$jsContents = $casper->getJsContents();

################# HOMEPAGE JS İÇERİĞİ ##############################

$jsContents .= file_get_contents(JS.'homePage.min.js');

################# HOMEPAGE CSS İÇERİĞİ #############################

$cssContents .= file_get_contents(CSS.'HomePage/HomePage.min.css');
if(count($homePageSpecialOfferProducts)>0 || count($homePageHomepageProducts)>0 || count($homePageDiscountedProducts)>0 || count($homePageNewProducts)>0){
    $cssContents .= file_get_contents(CSS.'HomePage/Products/HomePageProducts.min.css');
}

################# CASPER UPDATE ##############################
$casper->setJsContents($jsContents);
$casper->setCssContents($cssContents);
$session->updateSession("casper", $casper);

################# SCHEMA.ORG OLUŞTURMA #######################

/*
$routerResult = $session->getSession("routerResult");
*/
$companySettings = $siteConfig['companySettings'];

$logoSettings = $casper->getSiteConfig()["logoSettings"];
$logoImg = $logoSettings["resim_url"] ;

$homePageData = [
    'title' => $seoTitle,
    'description' => $seoDescription,
    'url' => $seoLink,
    'siteName' => $companySettings['ayarfirmakisaad'] ?? "Pozitif Eticaret",
    'logo' => $config->http.$config->hostDomain."/".imgRoot.$logoImg
];

$schemaGenerator = new SchemaGenerator();
$schema = $schemaGenerator->generateSchema('homepage', $homePageData);
$casper->setSchema($schema);
$session->updateSession("casper", $casper);

$seoImage = empty($seoImage) ? $config->http.$config->hostDomain."/".imgRoot.$logoImg : $seoImage;

$routerResult = $session->getSession("routerResult");

$routerResult['languageCode'] = $languageCode;
$routerResult['languageID'] = $languageID;
$routerResult['seoTitle'] = $seoTitle;
$routerResult['seoDescription'] = $seoDescription;
$routerResult['seoKeywords'] = $seoKeywords;
$routerResult['seoImage'] = $seoImage;
$routerResult['seoLink'] = $seoLink;

$session->updateSession("routerResult", $routerResult);
