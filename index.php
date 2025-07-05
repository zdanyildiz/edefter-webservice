<?php //phpinfo();exit;
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
require_once $documentRoot . $directorySeparator . '/vendor/autoload.php';
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

################# CONFIG ###################################
//ön tanımlı ayarlarımızı yapalım
$config = new Config();

################# DATABASE #################################
$db=new Database($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

################# ROUTER ##################################
$router = new Router($config);
Log::write("router: ". json_encode($router), "info");
//print_r($router);exit();
$contentType = $router->contentType;
$languageID = $router->languageID;

if($contentType=="WEBSERVICE")
{
    $contentName = $router->contentName;
    $controllerName = $router->controllerName;
    $requestData = $router->requestData;
    $helper = $config->Helper;
    include_once 'App/Webservice/'.ucfirst($controllerName).'.php';
    exit;
}
elseif($contentType=="ADMIN")
{
    $contentName = $router->contentName;
    $controllerLoader = new ControllerLoader();

    $controllerName = ucfirst($contentName);
    $controllerLoader->loadController($controllerName, ["config"=>$config ,"requestData" => $router->requestData]);


    exit();
}
//die($config->hostDomain);
################# SESSION ##################################
//oturum başlatalım
$session = new Session($config->key,3600,"/",$config->hostDomain,$config->cookieSecure,$config->cookieHttpOnly,$config->cookieSameSite);

################# CASPER ###################################
//oturum başlattık,casper'ı alalım
$casper = $session->getCasper();

if (!$casper instanceof Casper) {
    echo "Casper is not here - Index:30";exit();
}

$casper->setConfig($config);
$session->updateSession("casper",$casper);

################# HELPER ##################################
$helper = $casper->getConfig()->Helper;

################# JSON ####################################
$json = $casper->getConfig()->Json;

################# siteConfig ####################################
$siteConfig = $casper->getSiteConfig();
$currentSiteConfigVersion = $siteConfig['siteConfigVersion'] ?? -1;

################# VISITOR #################################
include_once CONTROLLER .'VisitorController.php';

################# CASPER WITH VISITOR ######################

$casper = $session->getCasper();

/**
 * @var boolean $getMemberInfo;
 */


if($getMemberInfo){
    //print_r($getMember);exit;

    $visitor = $casper->getVisitor();
    $config->includeClass('Member');
    $member = new Member($db);
    $memberData = $member->getMemberInfo(0,$visitor['visitorUniqID']);

    if(!$memberData){
        $visitor['visitorIsMember']["memberStatus"] = false;
    }
    else{
        $memberData = $memberData[0];
        $memberDataConvert = [
            'memberStatus' => true,
            'identificationNumber'=> (!empty($memberData['uyetcno'])) ? $helper->decrypt($memberData['uyetcno'], $config->key) : "",
            'memberID' => $memberData['uyeid'],
            'memberUniqID' => $memberData['benzersizid'],
            'memberCreateDate' => $memberData['uyeolusturmatarih'],
            'memberUpdateDate' => $memberData['uyeguncellemetarih'],
            'memberType' => $memberData['uyetip'],
            'memberName' => (!empty($memberData['memberTitle'])) ? $helper->decrypt($memberData['memberTitle'], $config->key) : "" ,
            'memberFirstName' => (!empty($memberData['uyead'])) ? $helper->decrypt($memberData['uyead'], $config->key) : "" ,
            'memberLastName' => (!empty($memberData['uyesoyad'])) ?$helper->decrypt($memberData['uyesoyad'], $config->key) : "",
            'memberEmail' => (!empty($memberData['uyeeposta'])) ? $helper->decrypt($memberData['uyeeposta'], $config->key) :"",
            'memberPhone' => ($memberData['uyetelefon']) ? $helper->decrypt($memberData['uyetelefon'], $config->key) : "",
            'memberDescription' => $memberData['uyeaciklama'],
            'memberInvoiceName' => $helper->decrypt($memberData['uyefaturaad'], $config->key),
            'memberInvoiceTaxOffice' => $helper->decrypt($memberData['uyefaturavergidairesi'], $config->key) ,
            'memberInvoiceTaxNumber' => $helper->decrypt($memberData['uyefaturavergino'], $config->key) ,
            'memberActive' => $memberData['uyeaktif']
        ];
        // oturumdaki ziyaretçi bilgileri güncellenir
        $visitor['visitorIsMember'] = $memberDataConvert;

        $memberID = $visitor['visitorIsMember']['memberID'];

        $addresses = $member->getAddress($memberID);

        $location = new Location($db);

        foreach ($addresses as $key => $address){
            $addresses[$key]['adresulke'] = $location->getCountryNameById($address['adresulke']);
            $addresses[$key]['adressehir'] = $location->getCityNameById($address['adressehir']);
            $addresses[$key]['adresilce'] = $location->getCountyNameById($address['adresilce']);
            $addresses[$key]['adressemt'] = $location->getAreaNameById($address['adressemt']);
            $addresses[$key]['adresmahalle'] = $location->getNeighborhoodNameById($address['adresmahalle']);
        }

        $visitor['visitorIsMember']['memberAddress'] = $addresses;
        $visitor['visitorIsMember']['countries'] = $location->getAllCountries();
    }

    $casper->setVisitor($visitor);
    $session->updateSession('casper', $casper);
}

################# CONTROLLER İSTENMİŞ ######################

if($contentType=="CONTROLLER")
{
    $contentName = $router->contentName;

    $controllerLoader = new ControllerLoader();
    $controllerData = [
        "config" => $config,
        "json" => $json,
        "session" => $session,
        "db" => $db,
        "requestData" => $router->requestData
    ];

    Log::write("IndexController: contentName: $contentName", "info");

    $controllerName = ucfirst($contentName);
    $controllerLoader->loadController($controllerName, $controllerData);
}

$session->addSession("routerResult",(array) $router);
$routerResult = $session->getSession("routerResult");
//Log::write("IndexController: routerResult: ".json_encode($routerResult), "info");

$config->includeClass("BannerManager");

if($contentType=="CONTENT"){
    // ilk gelen contentName HomePage, Search ya da boş olabilir
    $contentName = $router->contentName;

    //boş ise seo sorgulayacağız
    if(empty($contentName)){
        //boş ise seo'da url sorgulayarak ne talep edilmiş bulalım
        include_once Helpers . "DetectContent.php";

        $detectContent = new DetectContent($db,$router);

        $routerResult = (array)$detectContent->getRouter();

        $session->addSession("routerResult",$routerResult);
        $routerResult = $session->getSession("routerResult");

        $contentName = $routerResult["contentName"];
        $languageID = $routerResult["languageID"];
        $languageCode = $routerResult["languageCode"];
        $languageModel = new Language($db,$languageCode);

        if($routerResult["seoTitle"] == "404"){
            Log::write("404 error","error");
            $getMainLanguage = $languageModel->getMainLanguages();

            if($getMainLanguage){
                $languageID = $getMainLanguage[0]['dilid'];
                $languageCode = $getMainLanguage[0]['dilkisa'];

                $routerResult['languageID'] = $languageID;
                $routerResult['languageCode'] = $languageCode;

                $session->addSession("routerResult",$routerResult);
            }
        }

        $languageModel->getTranslations($languageCode);

        $siteConfigInfo = new SiteConfig($db,$languageID);
        $siteConfigVersion = $siteConfigInfo->siteConfigVersion;

        if(empty($siteConfig) || $routerResult["languageID"]!=$languageID || $currentSiteConfigVersion!=$siteConfigVersion){
            Log::write("İndex.php:230 Dil Faktörü: ".$routerResult["languageID"]."!=".$languageID );
            Log::write("İndex.php:231 Config Version: ".$currentSiteConfigVersion."!=".$siteConfigVersion);
            // BannerManager önbelleğini temizle
            $bannerManager = BannerManager::getInstance();
            $bannerManager->initialize([], $casper);
            $bannerManager->onSiteConfigChange();

            $siteConfigInfo->createSiteConfig();
            $casper->setSiteConfig($siteConfigInfo->getSiteConfig());
            $session->updateSession("casper",$casper);
            $siteConfig = $casper->getSiteConfig();
        }
    }
    else{

        $routerQuery = $routerResult["query"] ?? "";
        parse_str($routerQuery, $resultQuery);

        //resultQuery içinde languageID var mı bakalım
        if(!isset($resultQuery['languageID'])){

            $languageModel = new Language($db,"",1);
            $getMainLanguage = $languageModel->getMainLanguages();

            if($getMainLanguage){
                $routerResult['languageID'] = $getMainLanguage[0]['dilid'];
                $routerResult['languageCode'] = $getMainLanguage[0]['dilkisa'];

                $session->addSession("routerResult",$routerResult);

                $languageModel->getTranslations($routerResult['languageCode']);

                $siteConfigInfo = new SiteConfig($db,$languageID);
                $siteConfigVersion = $siteConfigInfo->siteConfigVersion;

                if(empty($siteConfig) || $routerResult["languageID"]!=$languageID || $currentSiteConfigVersion!=$siteConfigVersion){
                    //log tutup hangi değişiklik tetiklemiş bakalım.
                    Log::write("İndex.php Dil Faktörü: ".$routerResult["languageID"]."!=".$languageID );
                    Log::write("İndex.php Config Version: ".$currentSiteConfigVersion."!=".$siteConfigVersion);
                    // BannerManager önbelleğini temizle
                    $bannerManager = BannerManager::getInstance();
                    $bannerManager->initialize([], $casper);
                    $bannerManager->onSiteConfigChange();

                    $siteConfigInfo->createSiteConfig();
                    $casper->setSiteConfig($siteConfigInfo->getSiteConfig());
                    $session->updateSession("casper",$casper);
                    $siteConfig = $casper->getSiteConfig();
                }
            }
        }
    }

    // Temel css ve js dosyalarını yükleyelim
    include_once Helpers . "assetLoader.php";

    $controllerLoader = new ControllerLoader();
    $controllerData = [
        "config" => $config,
        "session" => $session,
        "db" => $db
    ];
    //Log::write("IndexController: contentName: $contentName", "info");
    $controllerName = ucfirst($contentName) . "Controller";
    $controllerLoader->loadController($controllerName, $controllerData);
}
else{
    Log::write("IndexController: contentName: ". json_encode($routerResult), "error");
    die("içerik belirlenemedi");
}
//die($contentName);
//bu aşamada hangi içerik istenmiş belli oldu
//$contentName HomePage, Page, Category, Serach olabilir

$routerResult = $session->getSession("routerResult");

$languageID = $routerResult["languageID"];
$languageCode = $routerResult["languageCode"];

$rightCartShow=true;

$visitor = $casper->getVisitor();
if(isset($visitor['visitorGetCart']) && $visitor['visitorGetCart']){

    include_once MODEL."Cart.php";
    $cart = new Cart($db,$helper,$session,$config);
    $visitorCart = $cart->getCart($visitor['visitorUniqID']);

    $visitor['visitorCart'] = $visitorCart;
    $visitor['visitorGetCart'] = false;

    $casper->setVisitor($visitor);
    $session->updateSession("casper",$casper);
}


################# VISITOR CART JS ve CSS İÇERİĞİ #############
$visitorCart = $casper->getVisitor()['visitorCart'];
if (!empty($visitorCart)) {
    $jsContents = $casper->getJsContents();
    $jsContents .= file_get_contents(JS . 'generalProductQuantity.min.js');
    $casper->setJsContents($jsContents);

    $cssContents = $casper->getCssContents();
}
else{
    $rightCartShow=false;
}

################# POPUP #####################################
$isPopup = $session->getSession('popup');
if (!empty($isPopup)) {
    include_once Helpers . "Popup.php";

    $popup = new Popup($isPopup['status'], $isPopup['message'], $isPopup['position'], "300px", "150px", $isPopup['closeButton'], $isPopup['autoClose'], $isPopup['animation'],);

    $cssContents = $casper->getCssContents();
    $cssContents .= $popup->popupCss();
    $casper->setCssContents($cssContents);

    $jsContents = $casper->getJsContents();
    $jsContents .= file_get_contents(JS . '/popup.min.js');
    $casper->setJsContents($jsContents);

    $session->updateSession("casper", $casper);
}


################# ASIDE RIGHT CART JS ve CSS İÇERİĞİ #########
if($routerResult["contentName"]=="Page"){
    $page=$session->getSession("page");
    if($page['sayfatip']==8||$page['sayfatip']==9||$page['sayfatip']==22){
        $rightCartShow=false;
    }
}
if ($rightCartShow) {
    $jsContents = $casper->getJsContents();
    $jsContents .= file_get_contents(JS . 'rightCart.min.js');
    $casper->setJsContents($jsContents);

    $cssContents = $casper->getCssContents();
    $cssContents .= file_get_contents(CSS . 'Layouts/aside_right.min.css');
    $casper->setCssContents($cssContents);

    $session->updateSession("casper", $casper);
}

################# INDEX #####################################

$indexData = [
    "config"=>$config,
    "session"=>$session,
    "db"=>$db,
    "languageCode"=>$languageCode,
    "languageID"=>$languageID
];
$config->loadView("index",$indexData);

################# SESSION CLEANING ###########################

if(!empty($session->getSession("page"))){
    $session->removeSession("page");
}
if(!empty($session->getSession("category"))){
    $session->removeSession("category");
}
if(!empty($session->getSession("mainPage"))){
    $session->removeSession("mainPage");
}
if($routerResult["contentType"]!="Search"){
    if(!empty($session->getSession("search"))){
        $session->removeSession("search");
    }
}
$config->includeClass("BannerManager");
$bannerManager = BannerManager::getInstance();
$bannerManager->initialize([], $casper);
$bannerManager->clearCache();

if(!empty($session->getSession("popup"))){
    $session->removeSession("popup");
}
