<?php
/**
 * @var Session $session
 * @var Config $config
 * @var Database $db
 * @var Json $json
 * @var Helper $helper
 * @var Casper $casper
 * @var string $memberLink
 * @var array $visitor
 */

$helper = $config->Helper;
$json = $config->Json;

$routerResult = $session->getSession("routerResult");
//print_r($routerResult);exit();

$query = $routerResult['query'] ?? "";
$orderType = "";
$queryStockCode = "";
if(!empty($query)){
    //query dize olarak geliyor, parse edelim
    parse_str($query, $parsedQuery);

    $queryStockCode = $parsedQuery['q'] ?? "";
    $orderType = $parsedQuery['orderType'] ?? "";

    if(!empty($orderType)){
        $routerResult["query"] = str_replace("&orderType=".$orderType,"",$routerResult["query"]);
    }
}

$languageID = $routerResult['languageID'];
$languageCode = $routerResult['languageCode'];

################# SCHEMA.ORG OLUŞTURMA #######################
$casper = $session->getCasper();

$siteConfig = $casper->getSiteConfig();

$pageLinks = $siteConfig['specificPageLinks'];

foreach ($pageLinks as $pageLink) {
    switch ($pageLink['sayfatip']) {
        case 1:
            $contactLink = $pageLink['link'];
            break;
        case 2:
            $newsLink = $pageLink['link'];
            break;
        case 3:
            $galleryLink = $pageLink['link'];
            break;
        case 4:
            $videoLink = $pageLink['link'];
            break;
        case 5:
            $fileLink = $pageLink['link'];
            break;
        case 6:
            $announcementLink = $pageLink['link'];
            break;
        case 7:
            $productLink = $pageLink['link'];
            break;
        case 8:
            $cartLink = $pageLink['link'];
            break;
        case 9:
            $checkoutLink = $pageLink['link'];
            break;
        case 10:
            $membershipAgreementLink = $pageLink['link'];
            break;
        case 11:
            $dealerLoginLink = $pageLink['link'];
            break;
        case 12:
            $distanceSalesLink = $pageLink['link'];
            break;
        case 13:
            $cookiePolicyLink = $pageLink['link'];
            break;
        case 14:
            $termsAndConditionsLink = $pageLink['link'];
            break;
        case 15:
            $privacyPolicyLink = $pageLink['link'];
            break;
        case 16:
            $brandsLink = $pageLink['link'];
            break;
        case 17:
            $memberLink = $pageLink['link'];
            break;
        case 18:
            $cancelReturnFormLink = $pageLink['link'];
            break;
        case 19:
            $favoriteLink = $pageLink['link'];
            break;
        case 20:
            $catalogsLink = $pageLink['link'];
            break;
        case 21:
            $aboutUsLink = $pageLink['link'];
            break;
        case 22:
            $paymentLink = $pageLink['link'];
            break;
        case 23:
            $generalLink = $pageLink['link'];
            break;
        case 24:
            $blogLink = $pageLink['link'];
            break;
        case 25:
            $kvkkLink = $pageLink['link'];
            break;
    }
}

$visitor = $casper->getVisitor();
//$routerResult = $session->getSession("routerResult");

$companySettings = $siteConfig['companySettings'];

$logoSettings = $casper->getSiteConfig()["logoSettings"];
$logoImg = $logoSettings["resim_url"];

$contentData = [
    'title' => $routerResult['seoTitle'],
    'description' => $routerResult['seoDescription'],
    'url' => $config->http.$config->hostDomain.$routerResult['seoLink'],
    'siteName' => $companySettings['ayarfirmakisaad'],
    'logo' => $config->http.$config->hostDomain.imgRoot.$logoImg
];

$schemaGenerator = new SchemaGenerator();
$schema = $schemaGenerator->generateSchema('page', $contentData);
$casper->setSchema($schema);
$session->updateSession("casper", $casper);


################# GEREKLİ MODELLER ############################

$config->includeClass("BannerManager");

$config->includeClass("Page");
$content = new Page($db,$session);

$config->includeClass('Category');
$categoryModel = new Category($db,$json);

include_once MODEL . 'Video.php';
$videoModel = new Video($db);

include_once MODEL . 'Gallery.php';
$galleryModel = new Gallery($db);

include_once MODEL . 'File.php';
$fileModel = new File($db);

include_once MODEL . 'Image.php';
$imageModel = new Image($db);

################# GENEL JS ve CSS İÇERİĞİ #####################
$jsContents = $casper->getJsContents();

$cssContents = $casper->getCssContents();
$cssContents .= file_get_contents(CSS.'Page/PageMain.min.css');

################# SAYFA İÇERİĞİ #####################

$page = $content->getPageById(0,$routerResult["contentUniqID"]);
$imageUrls = $page['resim_url'];
$imageUrls = ($imageUrls) ? explode(",",$imageUrls) : [];

if(count($imageUrls))$cssContents .= file_get_contents(CSS.'Page/PageModal.min.css');

$pageGallery = $page['pageGallery'] ?? [];
if(!empty($pageGallery)){
    $pageGallery = $pageGallery[0];
    $pageGallery = $galleryModel->getGallery($pageGallery['galleryID']);
    $pageGallery = $pageGallery[0];
    $pageGalleryName = $pageGallery['galleryName'];
    $pageGalleryDescription = $pageGallery['galleryDescription'];
    $pageGalleryImageIds = $galleryModel->getGalleryImages($pageGallery['galleryID']);

    $pageGalleryImages = [];
    if(!empty($pageGalleryImageIds)){
        $cssContents .= file_get_contents(CSS.'Layouts/gallery.min.css');
        foreach($pageGalleryImageIds as $pageGallery){
            $imageID = $pageGallery['imageID'];
            $image = $imageModel->getImageByID($imageID);
            $pageGalleryImages[] = !$image ? [] : $image[0];
        }

        $pageGalleryData = [
            "galleryName" => $pageGalleryName,
            "galleryDescription" => $pageGalleryDescription,
            "galleryImages" => $pageGalleryImages
        ];

        $page['pageGallery'] = $pageGalleryData;
    }
    else{
        $page['pageGallery'] = [];
    }
}

$pageFiles = $page['pageFiles'] ?? [];
if(!empty($pageFiles)){
    $cssContents .= file_get_contents(CSS.'Layouts/file.min.css');
    $files = [];
    foreach($pageFiles as $pageFile){
        $file = $fileModel->getFileById($pageFile['fileID']);
        if(!$file) continue;
        $files[] = $file[0];
    }
    $page['pageFiles'] = $files;
}

$pageVideos = $page['pageVideos'] ?? [];
if(!empty($pageVideos)){
    $cssContents .= file_get_contents(CSS.'Layouts/video.min.css');
    $videos = [];
    foreach($pageVideos as $pageVideo){
        $video = $videoModel->getVideoById($pageVideo['videoID']);
        if(!$video) continue;
        $videos[] = $video[0];
    }
    $page['pageVideos'] = $videos;
}

################# BANNER İÇERİĞİ #########################

// Banner Manager'ı başlat
$bannerManager = BannerManager::getInstance();
$bannerManager->initialize($siteConfig['bannerInfo'], $casper);

// Tüm banner tiplerini render et (cache'li)
$bannerResults = $bannerManager->renderAllBannerTypes($page['sayfaid'], );

$sliderBanners = $bannerResults['types'][1] ?? ['html' => '', 'css' => '', 'js' => ''];
$sliderBannersHtml = $sliderBanners['html'];
$page['sliderBanner'] = $sliderBannersHtml;

$middleBanners = $bannerResults['types'][3] ?? ['html' => '', 'css' => '', 'js' => ''];
$middleBannersHtml = $middleBanners['html'];
$page['middleBanner'] = $middleBannersHtml;

$cssContents .= $bannerResults['all_css'];
$jsContents .= $bannerResults['all_js'];

$page['categoryHierarchy'] = $categoryModel->getCategoryHierarchy($page['kategoriid'], true);

$routeResult = $session->getSession('routerResult');

$query =$routeResult['query'];
$query = is_string($query) ? $query : '';
parse_str($query,$parsedQuery);

$paymentResult = $parsedQuery['paymentResult'] ?? "";

################# SAYFA TÜRLERİNE GÖRE JS vs CSS İÇERİĞİ #####
if($page['sayfatip'] == 1){
    $cssContents .= file_get_contents(CSS.'Page/Contact.min.css');
    $jsContents .= file_get_contents(JS.'contact.min.js');

    if (!defined('CLOUDFLARE_SITE_KEY')) {
        $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
        $defaultSiteKey = $cloudflareConfig['default']['site_key'];
        $currentHostname = $_SERVER['HTTP_HOST'];
        if (isset($cloudflareConfig['sites'][$currentHostname])) {
            $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['site_key'];
        }
        define('CLOUDFLARE_SITE_KEY', $defaultSiteKey);
    }
}
elseif($page['sayfatip'] == 29){
    // Online Randevu Formu
    $cssContents .= file_get_contents(CSS.'Page/Appointment.min.css');
    $jsContents .= file_get_contents(JS.'appointment.min.js');

    if (!defined('CLOUDFLARE_SITE_KEY')) {
        $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
        $defaultSiteKey = $cloudflareConfig['default']['site_key'];
        $currentHostname = $_SERVER['HTTP_HOST'];
        if (isset($cloudflareConfig['sites'][$currentHostname])) {
            $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['site_key'];
        }
        define('CLOUDFLARE_SITE_KEY', $defaultSiteKey);
    }
}
elseif($page['sayfatip'] == 7) {

    ################# ÜRÜN DETAY SAYFASI İÇİN GEREKLİ MODELLER ####
    $config->includeClass('Product');
    $productModel = new Product($db,$json);

    $page['productDetails'] = $productModel->getProductDetails($page['sayfaid'], $languageCode);
    //print_r($page['productDetails']);exit();
    //$productModel->setAllProductVariants();

    ################# ÜRÜN SAYFASI İÇİN SCHEMA.ORG OLUŞTURMA ####

    $productImages = $page['resim_url'] ?? "";
    //Product/img1.jpg,Product/img2.jpg,Product/img3.jpg.. şeklinde virgülle string olarak geliyor. başına domain de ekleyelim
    if(!empty($productImages)) {
        $productImages = explode(",", $productImages);
        //$productImages = $productImages[0];
        //$productImages = $config->http.$config->hostDomain.imgRoot.$productImages;

        foreach ($productImages as $i => $productImage) {
            $productImages[$i] = $config->http . $config->hostDomain . imgRoot . $productImage;
        }
        //$productImages = implode(",",$productImages);
    }

    $configPriceSettings = $casper->getSiteConfig()["priceSettings"][0];
    $configShowDiscount = $configPriceSettings["eskifiyat"];
    $configShowPrice = $configPriceSettings["fiyatgoster"];
    $configPriceUnit = $configPriceSettings["parabirim"];
    $currencyRates = $casper->getSiteConfig()["currencyRates"];

    $usdToTry = $currencyRates["usd"];
    $eurToTry = $currencyRates["euro"];

    $productCurrencyID = $page['productDetails'][0]['urunparabirim'];
    $productCurrencySymbol = $page['productDetails'][0]['parabirimsimge'];
    $productCurrencyCode = $page['productDetails'][0]['parabirimkod'];

    $productCurrencyRates = 1;

    $productSalesPrice = $page['productDetails'][0]['urunsatisfiyat'];
    $productWithoutDiscountPrice = $page['productDetails'][0]['urunindirimsizfiyat'];


    $variantProperties = [];
    if($productCurrencyID!=$configPriceUnit){
        switch ($configPriceUnit) {
            case 1:
                //genel para birimi ayarı 1 tl, 2 usd, 3 euro
                //ürün para birimi farklı ise $usdToTry,$eurToTry değerleri ile dönüştürme yapacağız
                if ($productCurrencyID == 2) {
                    $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * $usdToTry :"0.00";
                    $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * $usdToTry : "0.00";
                    $productCurrencyRates = $usdToTry;
                } elseif ($productCurrencyID == 3) {
                    $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * $eurToTry : "0.00";
                    $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * $eurToTry : "0.00";
                    $productCurrencyRates = $eurToTry;
                }
                $productCurrencySymbol = "₺";
                $productCurrencyCode = "TRY";
                $productCurrencyID = 1;
                break;
            case 2:
                if ($productCurrencyID == 1) {
                    $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice / $usdToTry : "0.00";
                    $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice / $usdToTry : "0.00";
                    $productCurrencyRates = $usdToTry;
                }
                elseif ($productCurrencyID == 3) {
                    $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * ($eurToTry / $usdToTry) : "0.00";
                    $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * ($eurToTry / $usdToTry) : "0.00";
                    $productCurrencyRates = $eurToTry / $usdToTry;
                }
                $productCurrencySymbol = "$";
                $productCurrencyCode = "USD";
                $productCurrencyID = 2;
                break;
            case 3:
                if ($productCurrencyID == 1) {
                    $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice / $eurToTry : "0.00";
                    $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice / $eurToTry : "0.00";
                    $productCurrencyRates = $eurToTry;
                }
                elseif ($productCurrencyID == 2) {
                    $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * ($usdToTry / $eurToTry) : "0.00";
                    $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * ($usdToTry / $eurToTry) : "0.00";
                    $productCurrencyRates = $usdToTry / $eurToTry;
                }
                $productCurrencySymbol = "€";
                $productCurrencyCode = "EUR";
                $productCurrencyID = 3;
                break;
        }

        $productSalesPrice = $helper->formatCurrency($productSalesPrice);
        $productWithoutDiscountPrice= $helper->formatCurrency($productWithoutDiscountPrice);

        $page['productDetails'][0]['urunparabirim'] = $productCurrencyID;
        $page['productDetails'][0]['parabirimsimge'] = $productCurrencySymbol;
        $page['productDetails'][0]['parabirimkod'] = $productCurrencyCode;

        $page['productDetails'][0]['urunsatisfiyat'] = $productSalesPrice;
        $page['productDetails'][0]['urunindirimsizfiyat'] = $productWithoutDiscountPrice;
    }

    $page['relatedProduct'] = $productModel->getRelatedProducts($page['sayfaid'], $languageCode);
    //print_r($page['relatedProduct']);exit();

    $variantProperties = json_decode($page['productDetails'][0]['variantProperties'],true);
    //print_r($variantProperties);exit();
    foreach ($variantProperties as $i => $variantProperty) {
        $variantProperties[$i]['variantCurrencyID'] = $productCurrencyID;
        $variantProperties[$i]['variantCurrencyCode'] = $productCurrencyCode;
        $variantProperties[$i]['variantCurrencySymbol'] = $productCurrencySymbol;

        $variantSellPrice = floatval($variantProperty['variantSellingPrice']) * floatval($productCurrencyRates);
        $variantSellPrice = $helper->formatCurrency($variantSellPrice);

        $variantWithoutDiscountPrice = floatval($variantProperty['variantPriceWithoutDiscount']) * floatval($productCurrencyRates);
        $variantWithoutDiscountPrice = $helper->formatCurrency($variantWithoutDiscountPrice);

        $variantProperties[$i]['variantSellingPrice'] = $variantSellPrice;
        $variantProperties[$i]['variantPriceWithoutDiscount'] = $variantWithoutDiscountPrice;

        $variantStockCode = $variantProperties[$i]['variantStockCode'];

        if($variantStockCode == $queryStockCode){
            $page['productDetails'][0]['urunsatisfiyat'] = $variantSellPrice;
            $page['productDetails'][0]['urunindirimsizfiyat'] = $variantWithoutDiscountPrice;

            $page['productDetails'][0]['urunstokkodu'] = $variantStockCode;

            $contentData = [
                'title' => $routerResult['seoTitle'],
                'description' => $routerResult['seoDescription'],
                'url' => $config->http.$config->hostDomain.$routerResult['seoLink'],
                'siteName' => $companySettings['ayarfirmakisaad'],
                'logo' => $config->http.$config->hostDomain.imgRoot.$logoImg,
                'image'=> $productImages,
                'brand' => $page['productDetails'][0]['markaad'],
                'priceCurrency' => $productCurrencyCode,
                'price' => $variantSellPrice
            ];
        }
    }
    //print_r($variantProperties);exit();
    $page['productDetails'][0]['variantProperties'] = json_encode($variantProperties);

    if(empty($queryStockCode)){
        $contentData = [
            'title' => $routerResult['seoTitle'],
            'description' => $routerResult['seoDescription'],
            'url' => $config->http.$config->hostDomain.$routerResult['seoLink'],
            'siteName' => $companySettings['ayarfirmakisaad'],
            'logo' => $config->http.$config->hostDomain.imgRoot.$logoImg,
            'image'=> $productImages,
            'brand' => $page['productDetails'][0]['markaad'],
            'priceCurrency' => $productCurrencyCode,
            'price' => $productSalesPrice
        ];
    }


    $schemaGenerator = new SchemaGenerator();
    $schema = $schemaGenerator->generateSchema('product', $contentData);
    $casper->setSchema($schema);
    $session->updateSession("casper", $casper);

    $category = $categoryModel->getCategoryByIdOrUniqId($page['kategoriid'],"");
    $categorySortBy = $category[0]["kategorisiralama"] ?? "";
    $categoryPages = $categoryModel->getPagesOfCategory($page['kategoriid'], "",$categorySortBy);
    $page['categoryPages'] = $categoryPages;
    foreach ($categoryPages as $i => $categoryPage) {
        $categoryProducts[$i] = $productModel->getProductByID($categoryPage['sayfaid']);
    }
    $page['categoryProducts'] = $categoryProducts;


    // kapmaya kontrolü
    $config->includeClass('Campaign');
    $campaignModel = new Campaign($db);
    $campaignControl = $campaignModel->checkCampaign(
        $page['productDetails'][0]['sayfaid'],
        $page['kategoriid'],
        $page['productDetails'][0]['markaid'],
        $page['productDetails'][0]['tedarikciid']);

    $page['campaign'] = $campaignControl;

    $campaignData = []; $discount = 0;
    if(!empty($campaignControl)){
        $discount = 1;
        foreach ($campaignControl as $campaign){
            if($campaign['tur'] == "miktar_indirim"){
                $campaignData[$campaign['miktar_sinir']] = $campaign['indirim_orani'];
            }
        }
    }

    require_once MODEL . 'Member.php';
    $member = new Member($db);
    $visitorUniqID = $visitor['visitorUniqID'];
    $favoritesControl = $member->getFavoritesControl($visitorUniqID,$routerResult["contentUniqID"]);
    if(!$favoritesControl){
        $page['favorites'] = 0;
    }
    else{
        $page['favorites'] = 1;
    }

    if(!empty($variantProperties)){
        $jsContents .= file_get_contents(JS.'productDetailVariantSelect.min.js');
        $jsContents .= 'const jsonData = '.json_encode($variantProperties).';';
        $jsContents .= 'const filterClass = new ProductVariantSelector(jsonData); ';
    }

    $jsContents .= 'const campaignData = '.json_encode($campaignData).';';
    $jsContents .= 'const discount = '.$discount.';';
    $jsContents .= file_get_contents(JS.'productDetailVariantQuantity.min.js');
    $jsContents .= "let productQuantity = new ProductQuantity('".$page['productDetails'][0]['urunminimummiktar']."', '".$page['productDetails'][0]['urunkatsayi']."', '".$page['productDetails'][0]['urunsatisfiyat']."','".$productCurrencySymbol."',discount,campaignData);";
    $jsContents .= file_get_contents(JS.'productDetails.min.js');

    $cssContents .= file_get_contents(CSS.'Page/ProductDetails.min.css');
}
elseif($page['sayfatip'] == 8){
    $cssContents .= file_get_contents(CSS.'Page/MyCart.min.css');
    $visitorCart = isset($casper->getVisitor()["visitorCart"]) ? $casper->getVisitor()["visitorCart"] : [];
    if(!empty($visitorCart)){
        $cssContents .= file_get_contents(CSS.'Page/MyCartDetails.min.css');
    }
}
elseif($page['sayfatip'] == 9){

    ################# ÖDEME KONTROL (CHECKOUT) #####

    $jsContents .= file_get_contents(JS.'checkoutAddressForm.min.js');
    $jsContents .= file_get_contents(JS.'checkoutProductSelect.min.js');
    $jsContents .= file_get_contents(JS.'checkoutSubmit.min.js');
    $cssContents .= file_get_contents(CSS.'Page/Checkout.min.css');
}
elseif($page["sayfatip"]==17){

    if (!defined('CLOUDFLARE_SITE_KEY')) {
        $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);

        $defaultSiteKey = $cloudflareConfig['default']['site_key'];

        $currentHostname = $_SERVER['HTTP_HOST'];

        if (isset($cloudflareConfig['sites'][$currentHostname])) {
            define('CLOUDFLARE_SITE_KEY', $cloudflareConfig['sites'][$currentHostname]['site_key']);
        } else {
            define('CLOUDFLARE_SITE_KEY', $defaultSiteKey);
        }
    }

    $visitor = $casper->getVisitor();

    if($visitor['visitorIsMember']['memberStatus']) {

        if($routerResult["query"]=="orders"){

            $memberID = $visitor['visitorIsMember']['memberID'];
            $config->includeClass('Member');
            $member = new Member($db);

            $orders = $member->getOrders($memberID, $orderType);

            $ordersConvertData = [];

            if(!empty($orders)){
                $config->includeClass('Product');
                $product = new Product($db,$json);

                $config->includeClass('Location');
                $location = new Location($db);

                foreach ($orders as $key => $order){

                    //orderUniqID
                    $ordersConvertData[$key]['orderUniqID'] = $order['siparisbenzersizid'];
                    //orderCreateDate
                    $ordersConvertData[$key]['orderCreateDate'] = $order['siparistariholustur'];
                    //orderStatusID
                    $ordersConvertData[$key]['orderStatusID'] = $order['siparisdurum'];
                    //orderStatusTitle
                    $ordersConvertData[$key]['orderStatusTitle'] = $order['siparisdurumbaslik'];
                    //orderTotalPrice
                    $ordersConvertData[$key]['orderTotalPrice'] = $order['siparistoplamtutar'];
                    //siparisparabirim
                    $ordersConvertData[$key]['orderCurrencyCode'] = $order['siparisodemeparabirim'];
                    //ödeme tipi
                    $orderPaymentType = $order['siparisodemeyontemi'];
                    //kk,bh,ko olabilir
                    switch ($orderPaymentType){
                        case "kk":
                            $orderPaymentType = "Kredi Kartı";
                            break;
                        case "bh":
                            $orderPaymentType = "Banka Havalesi";
                            break;
                        case "ko":
                            $orderPaymentType = "Kapıda Ödeme";
                            break;
                        default:
                            $orderPaymentType = "Diğer";
                    }

                    $ordersConvertData[$key]['orderPaymentType'] = $orderPaymentType;

                    //ödeme durumu
                    $orderPaymentStatus = $order['siparisodemedurum'];
                    //0 ve 1 olabilir
                    switch ($orderPaymentStatus){
                        case 0:
                            $orderPaymentStatus = "Ödeme Bekleniyor";
                            break;
                        case 1:
                            $orderPaymentStatus = "Ödendi";
                            break;
                        default:
                            $orderPaymentStatus = "Diğer";
                    }

                    $ordersConvertData[$key]['orderPaymentStatus'] = $orderPaymentStatus;

                    $ordersConvertData[$key]['orderDeliveryAddressName'] = $order['siparisteslimatad'] . " " . $order['siparisteslimatsoyad'];
                    $ordersConvertData[$key]['orderDeliveryAddressCountry'] = $location->getCountryNameById($order['siparisteslimatadresulke']);
                    $ordersConvertData[$key]['orderDeliveryAddressCity'] = $location->getCityNameById($order['siparisteslimatadressehir']);
                    $ordersConvertData[$key]['orderDeliveryAddressCounty'] = $location->getCountyNameById($order['siparisteslimatadresilce']);
                    $ordersConvertData[$key]['orderDeliveryAddressArea'] = $location->getAreaNameById($order['siparisteslimatadressemt']);
                    $ordersConvertData[$key]['orderDeliveryAddressNeighborhood'] = $location->getNeighborhoodNameById($order['siparisteslimatadresmahalle']);
                    $ordersConvertData[$key]['orderDeliveryAddressPostalCode'] = $order['siparisteslimatadrespostakod'];
                    $ordersConvertData[$key]['orderDeliveryAddressStreet'] = $order['siparisteslimatadresacik'];

                    if(is_numeric($order['siparisteslimatadresulke'])){
                        $ordersConvertData[$key]['orderDeliveryAddressCountryPhoneCode'] = $location->getCountryPhoneCode($order['siparisteslimatadresulke']);
                    }
                    else{
                        $ordersConvertData[$key]['orderDeliveryAddressCountryPhoneCode'] = $location->getCountryPhoneCodeByCountryName($order['siparisteslimatadresulke']);
                    }

                    $ordersConvertData[$key]['orderInvoiceName'] = $order['siparisfaturaunvan'];
                    $ordersConvertData[$key]['orderInvoiceTaxOffice'] = $order['siparisfaturavergidairesi'];
                    $ordersConvertData[$key]['orderInvoiceTaxNumber'] = $order['siparisfaturavergino'];

                    $ordersConvertData[$key]['orderInvoiceName'] = $order['siparisfaturaad'].' '. $order['siparisfaturasoyad'];
                    $ordersConvertData[$key]['orderInvoiceEmail'] = $order['siparisfaturaeposta'];
                    $ordersConvertData[$key]['orderInvoicePhone'] = $order['siparisfaturagsm'];
                    $ordersConvertData[$key]['orderInvoiceAddressCountry'] = $location->getCountryNameById($order['siparisfaturaadresulke']);
                    $ordersConvertData[$key]['orderInvoiceAddressCity'] = $location->getCityNameById($order['siparisfaturaadressehir']);
                    $ordersConvertData[$key]['orderInvoiceAddressCounty'] = $location->getCountyNameById($order['siparisfaturaadresilce']);
                    $ordersConvertData[$key]['orderInvoiceAddressArea'] = $location->getAreaNameById($order['siparisfaturaadressemt']);
                    $ordersConvertData[$key]['orderInvoiceAddressNeighborhood'] = $location->getNeighborhoodNameById($order['siparisfaturaadresmahalle']);
                    $ordersConvertData[$key]['orderInvoiceAddressPostalCode'] = $order['siparisfaturaadrespostakod'];
                    $ordersConvertData[$key]['orderInvoiceAddressStreet'] = $order['siparisfaturaadresacik'];

                    $orderProductIDs = explode(",", $order['siparisurunidler']);
                    $orderProductNames = explode("||", $order['siparisurunadlar']);
                    $orderProductStockCodes = (!empty($order['siparisurunstokkodlar'])) ? explode("||", $order['siparisurunstokkodlar']):[];
                    $orderProductCategories = explode("||", $order['siparisurunkategoriler']);
                    $orderProductPrices = explode("||", $order['siparisurunfiyatlar']);
                    $orderProductQuantities = explode("||", $order['siparisurunadetler']);

                    $orderProducts = [];

                    foreach ($orderProductIDs as $i => $orderProductID){
                        $productUnitName = $product->getProductUnitNameByProductID($orderProductID);
                        $orderProducts[] = [
                            // beden renk malzeme için doğrulama yapalım olmayabilir
                            'productID' => $orderProductID,
                            'productName' => $orderProductNames[$i],
                            'productCategory' => $orderProductCategories[$i],
                            'productPrice' => $orderProductPrices[$i],
                            'productQuantity' => str_replace(".0000","",$orderProductQuantities[$i]),
                            'productUnitName' => $productUnitName,
                            'productImages' => $product->getProductImages($orderProductID)
                        ];
                        if(isset($orderProductStockCodes[$i])){
                            $orderProducts[$i]['productStockCode'] = $orderProductStockCodes[$i];
                        }
                    }
                    $ordersConvertData[$key]['orderProducts'] = $orderProducts;
                }
            }

            $visitor['visitorIsMember']['memberOrders'] = $ordersConvertData;
            $casper->setVisitor($visitor);
            $session->updateSession('casper', $casper);

            $jsContents .= file_get_contents(JS . 'order.min.js');
            $cssContents .= file_get_contents(CSS.'Page/Member/Order.min.css');
        }
        elseif($routerResult["query"]=="message"){
            $cssContents .= file_get_contents(CSS.'Page/Member/Message.min.css');
        }
        else{

            $jsContents .= file_get_contents(JS . 'memberUpdateFormValidate.min.js');
            $cssContents .= file_get_contents(CSS . 'Page/Member/Profile.min.css');

            if($routerResult["query"]=="updateAddress"){

                $memberInfo = $visitor['visitorIsMember'];
                $addresses = $memberInfo['memberAddress'];

                if($addresses['adresulke']==212) {
                    $jsContents .= 'window.onload = async function() {
                    var countryID = document.querySelector("#addressCountry").value;
                    var languageCode = document.querySelector("#languageCode").value;
                    
                    // Eğer ülke ID\'si 212 ise, ilgili select elementlerini doldur
                    if (countryID == 212) {
                        //document.querySelector("#addressCountry").value = 0;
                        //1 saniyelik bir bekleme süresi
                        await new Promise(r => setTimeout(r, 200));
                        var event = new Event("change");
                        document.querySelector("#addressCountry").dispatchEvent(event);
                        await new Promise(r => setTimeout(r, 500));
                        document.querySelector("#addressCity").value = '.$addresses['adressehir'].';
                        document.querySelector("#addressCity").dispatchEvent(event);
                        await new Promise(r => setTimeout(r, 500));
                        document.querySelector("#addressCounty").value = '.$addresses['adresilce'].';
                        document.querySelector("#addressCounty").dispatchEvent(event);
                        await new Promise(r => setTimeout(r, 500));
                        document.querySelector("#addressArea").value = '.$addresses['adressemt'].';
                        document.querySelector("#addressArea").dispatchEvent(event);
                        await new Promise(r => setTimeout(r, 500));
                        document.querySelector("#addressNeighborhood").value = '.$addresses['adresmahalle'].';
                        document.querySelector("#addressNeighborhood").dispatchEvent(event);
                    }
                };';
                }
            }
        }
    }
    else{
        $jsContents .= file_get_contents(JS.'login.min.js');
        $cssContents .= file_get_contents(CSS . 'Page/Member/Login.min.css');
    }

}
elseif($page["sayfatip"]==18){

    $cssContents .= file_get_contents(CSS . 'Page/CancellationRefundExchangeResponse.min.css');

    $memberStatus = $visitor['visitorIsMember']['memberStatus'];

    if ($memberStatus) {
        $memberID = $visitor['visitorIsMember']['memberID'];

        require_once MODEL . 'Member.php';
        $member = new Member($db);
        $orders = $member->getOrdersForCancellationRefundExchangeResponse($memberID);

        $visitor['visitorIsMember']['memberOrders'] = $orders;

        $casper->setVisitor($visitor);
        $session->updateSession('casper', $casper);
    }
    else{
        exit(header("Location: $memberLink"));
    }
}
elseif($page["sayfatip"]==19){
    $jsContents .= file_get_contents(JS.'favorite.min.js');
    $cssContents .= file_get_contents(CSS.'Page/Favorite.min.css');
    require_once MODEL . 'Member.php';
    $member = new Member($db);
    $visitorUniqID = $visitor['visitorUniqID'];
    $favorites = $member->getFavorites($visitorUniqID);

    if(!empty($favorites)){
        $config->includeClass('Product');
        $favoriteProduct = new Product($db, $json);
        $favoriteProducts = [];
        foreach ($favorites as $key => $favorite){
            $productUniqID = $favorite['productUniqID'];
            $product = $favoriteProduct->getProductByUniqID($productUniqID);
            $favoriteProducts[] = $product;
        }
        $visitor['visitorIsMember']['memberFavorites'] = $favoriteProducts;
        $casper->setVisitor($visitor);
        $session->updateSession("casper",$casper);
    }
}
elseif($page["sayfatip"]==22){

    ################# öDEME #############################

    $memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;

    if($memberStatus){
        $jsContents .= file_get_contents(JS.'payment.min.js');
        $cssContents .= file_get_contents(CSS.'Page/Payment.min.css');
    }
    else{

        if (!defined('CLOUDFLARE_SITE_KEY')) {
            $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);

            $defaultSiteKey = $cloudflareConfig['default']['site_key'];

            $currentHostname = $_SERVER['HTTP_HOST'];

            if (isset($cloudflareConfig['sites'][$currentHostname])) {
                define('CLOUDFLARE_SITE_KEY', $cloudflareConfig['sites'][$currentHostname]['site_key']);
            } else {
                define('CLOUDFLARE_SITE_KEY', $defaultSiteKey);
            }
        }

        $jsContents .= file_get_contents(JS.'login.min.js');
        $cssContents .= file_get_contents(CSS . 'Page/Member/Login.min.css');
    }

}
else{
    $cssContents .= file_get_contents(CSS.'Page/PageDetail.min.css');
    $jsContents .= file_get_contents(JS.'pageDetails.min.js');
}

if($page['sayfatip'] == 24){
    $category = $categoryModel->getCategoryByIdOrUniqId($page['kategoriid'],"");
    $categorySortBy = $category[0]["kategorisiralama"] ?? "";
    $categoryPages = $categoryModel->getPagesOfCategory($page['kategoriid'], "", $categorySortBy);

    $config->includeClass('SeoModel');
    $seoModel = new SeoModel($db);

    foreach ($categoryPages as $i => $categoryPage) {
        $pageDetails = $content->getPageById($categoryPage['sayfaid'],"");

        $pageSeo = $seoModel->getSeoByUniqId($pageDetails['benzersizid']);
        $pageSeoLink = $pageSeo['seoLink'] ?? "";

        $pageDetails['seoLink'] = $pageSeoLink;

        $categoryPages[$i]["pageDetails"] = $pageDetails;
    }
    $page['categoryPages'] = $categoryPages;

    $cssContents .= file_get_contents(CSS.'Page/PageBlog.min.css');
}

$pageUniqID = $page['benzersizid'];
$customCssFile = CSS . 'Page/CustomCSS/' . $pageUniqID . '.css';
if(file_exists($customCssFile)){
    $cssContents .= file_get_contents($customCssFile);
}

################# CASPER GÜNCELLEME ##########################
$casper = $session->getCasper();
$casper->setCssContents($cssContents);
$casper->setJsContents($jsContents);
$session->updateSession("casper",$casper);


################# ÖDEME KONTROL SAYFASI ÜLKE BİLGİLERİ #############################
if($page['sayfatip'] == 9) {

    ################# ÖDEME SAYFASI İÇİN ADRES ALALIM #####
    $visitor = $casper->getVisitor();
    $visitorUniqID = $visitor['visitorUniqID'];

    $location = new Location($db);

    if($visitor['visitorIsMember']['memberStatus']){

        $config->includeClass('Member');
        $memberModel = new Member($db);

        $memberID = $visitor['visitorIsMember']['memberID'];

        $addresses = $memberModel->getAddress($memberID);

        if(!empty($addresses)){
            $jsContents = $casper->getJsContents();
            $jsContents .= file_get_contents(JS.'checkoutSelectAddress.min.js');
            $casper->setJsContents($jsContents);

            foreach ($addresses as $key => $address){
                $addresses[$key]['adresulke'] = $location->getCountryNameById($address['adresulke']);
                $addresses[$key]['adressehir'] = $location->getCityNameById($address['adressehir']);
                $addresses[$key]['adresilce'] = $location->getCountyNameById($address['adresilce']);
                $addresses[$key]['adressemt'] = $location->getAreaNameById($address['adressemt']);
                $addresses[$key]['adresmahalle'] = $location->getNeighborhoodNameById($address['adresmahalle']);
            }

            $visitor['visitorIsMember']['memberAddress'] = $addresses;

            $casper->setVisitor($visitor);
            $session->updateSession("casper",$casper);
        }



    }

    ################# ÖDEME SAYFASI İÇİN ÜLKE ALALIM #####
    //üye ya da değil alınması gerekiyor

    $visitor = $casper->getVisitor();

    $countries = $location->getAllCountries();

    $visitor['countries'] = $countries;

    ################# ÖDEME SAYFASI İÇİN SEPET GÜNCELLEYELİM #####

    include_once MODEL.'Cart.php';
    $cart = new Cart($db,$helper,$session,$config);

    $cartInfo = $cart->getCart($visitorUniqID);

    // üyenin sepet bilgileri oturuma kaydedilir
    $visitor['visitorCart'] = $cartInfo;

    $casper->setVisitor($visitor);

    $session->updateSession("casper",$casper);
}


################# SESSION UPDATE #############################
$session->addSession("page", $page);
