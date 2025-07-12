<body>
<?php
/**
 * @var Session $session
 * @var Database $db
 * @var Config $config
 * @var Json $json
 * @var int $languageID
 * @var Popup $popup
 * @var string $cookiePolicyLink
 * @var string $languageCode
 */

################# TANIMLAMALAR ###########################
$routerResult = $session->getSession("routerResult");
$casper = $session->getCasper();
$config = $casper->getConfig();
$json = $config->Json;
$helper = $config->Helper;
$siteConfig = $casper->getSiteConfig();
$generalSettings = $siteConfig["generalSettings"];
$siteType = $generalSettings["sitetip"];
$siteSettings = $siteConfig['siteSettings'];

$http = $config->http;
$hostDomain = $config->hostDomain;

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

$companySettings = $siteConfig["companySettings"];
$companyCountryCode = $companySettings['ayarfirmaulkekod'] ?? "";
$companyGsm = $companySettings['ayarfirmagsm'] ?? "";
$socialMediaSettings = $siteConfig["socialMediaSettings"];
$bannerInfo = $siteConfig["bannerInfo"];

$allMenu = $casper->getAllMenu();

$adminCasper = $session->getSession("adminCasper") ?? [];

$allMenu = new Menu($db,$config,$json,$languageID);
$casper->setAllMenu($allMenu);
$session->updateSession("casper",$casper);

$siteHeaderSettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "header";
});
$headerShowMenu = 0;
foreach ($siteHeaderSettings as $siteSetting) {
    if ($siteSetting['element'] == "menu") {
        $headerShowMenu = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
}

################# HEADER ###################################

$headerData = [
    "session" => $session,
    "casper"=>$casper,
    "companySettings"=>$companySettings,
    "bannerInfo"=>$bannerInfo,
    "config"=>$config,
    "languageID"=>$languageID,
    "languageCode"=>$languageCode,
    "memberLink"=>$memberLink,
    'favoriteLink'=>$favoriteLink,
    "cartLink"=>$cartLink,
    "generalSettings"=>$generalSettings,
    "siteSettings"=>$siteSettings,
    "db"=>$db,
    "allMenu"=>$allMenu
];
$config->loadView("Layouts/header",$headerData);

################# MAIN MENU ################################
if($headerShowMenu == 0){
    $navMainData = [
        "allMenu"=>$allMenu
    ];
    $config->loadView("Layouts/nav-main",$navMainData);
}

################# MAIN ##################################

$maindata = [
    "config"=>$config,
    "session"=>$session,
];
$config->loadView("Layouts/main",$maindata);

################# FOOTER ##################################

$footerData = [
    "config"=>$config,
    "allMenu"=>$allMenu,
    "companySettings"=>$companySettings,
    "socialMediaSettings"=>$siteConfig["socialMediaSettings"],
    "siteSettings"=>$siteSettings,
];
$config->loadView("Layouts/footer",$footerData);

$bannerPageID = null; $bannerCategoryID = null;
if($session->getSession("page")!=[]){
    $bannerPageID = $session->getSession("page")['sayfaid'];
}
if($session->getSession("category")!=[]){
    $bannerCategoryID = $session->getSession("category")['category']['kategoriid'];
}
if($session->getSession("mainPage")!=[]){
    $bannerCategoryID = $session->getSession("mainPage")["homePageCategoryId"];
}

$bannerManager = BannerManager::getInstance();
$popupBannerResults = $bannerManager->getPopupBanners($bannerPageID, $bannerCategoryID);
//echo '<pre>';print_r($popupBannerResults);echo '</pre>';
if(isset($popupBannerResults['groupId']) && !empty($popupBannerResults['groupId'])){
//die("bgid:".$popupBannerResults['groupId']);
    $popupBannerGroupId = $popupBannerResults['groupId'];
    $popupBannerCookies = $session->getCookie("popupBanner-".$popupBannerGroupId);

    if(empty($popupBannerCookies)){
        $popupBannerHtml = $popupBannerResults['html'];

        if(!empty($popupBannerHtml)){
            echo '<div id="PopupBanner">';
            echo '<button id="popupBannerClose" data-banner-group-id="'.$popupBannerGroupId.'" class="popup-banner-close btn">&times;</button>';
            echo $popupBannerHtml;
            echo '</div>';
        }
    }
}

$isPopup = $session->getSession('popup');

if (!empty($isPopup)) {
    //echo "<pre>";
    //print_r($isPopup);

    $session->addSession('popupBanner', 1);
    include_once Helpers."Popup.php";
    $popup = new Popup($isPopup['status'], $isPopup['message'], $isPopup['position'], $isPopup["width"], $isPopup["height"], $isPopup['closeButton'], $isPopup['autoClose'], $isPopup['animation'],$isPopup['duration'] ?? "");
    $css = $popup->popupCss();
    echo "<style>".$css."</style>";
    echo $popup->show();
}

$bodySiteSettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "body";
});

foreach ($bodySiteSettings as $siteSetting) {
    if($siteSetting['element'] == "newsletter"){
        $bodyShowNewsletter = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "whatsapp"){
        $bodyShowWhatsapp = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
    if($siteSetting['element'] == "assistant"){
        $bodyShowAssistant = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
}

if($bodyShowWhatsapp == 1){
?>
    <a href="https://wa.me/<?=$companyCountryCode.$companyGsm?>?text=<?=urlencode($http.$hostDomain.$routerResult['seoLink'])?>" class="store-whatsapp" title="<?=_body_whatsapp_title?>" target="_blank">
        <svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="340 -40 640.000000 640.000000" preserveAspectRatio="none"><g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" fill="#ffffff" stroke="none"><path d="M6255 6844 c-540 -35 -1107 -229 -1555 -532 -473 -320 -848 -752 -1091 -1256 -133 -276 -216 -536 -273 -856 -43 -240 -52 -602 -22 -880 40 -374 177 -822 362 -1188 l53 -103 -123 -367 c-68 -202 -191 -570 -274 -818 -84 -249 -152 -459 -152 -469 0 -9 13 -22 29 -28 26 -10 29 -14 24 -45 -6 -32 -5 -34 18 -27 41 13 936 298 1314 420 198 63 368 115 378 115 9 0 52 -17 95 -39 366 -184 756 -294 1171 -332 164 -14 498 -7 659 16 954 132 1766 659 2268 1468 163 264 318 632 401 952 79 307 117 688 96 982 -54 781 -356 1473 -881 2017 -509 527 -1157 853 -1895 952 -108 14 -482 26 -600 18z m391 -684 c357 -29 650 -108 959 -259 419 -206 770 -514 1030 -906 200 -301 323 -625 371 -979 23 -168 23 -508 0 -680 -163 -1209 -1161 -2141 -2372 -2217 -427 -26 -824 44 -1212 214 -107 47 -284 143 -339 183 -17 13 -39 24 -49 24 -9 0 -222 -65 -472 -145 -250 -80 -456 -145 -457 -143 -2 2 62 197 141 433 79 237 144 442 144 458 0 16 -18 53 -44 90 -418 599 -554 1426 -351 2127 45 152 82 245 155 390 200 391 505 732 880 982 473 316 1064 472 1616 428z"/><path d="M5323 5236 c-23 -7 -56 -23 -75 -34 -51 -32 -199 -190 -245 -262 -147 -229 -180 -534 -92 -832 67 -225 149 -397 299 -629 190 -292 313 -450 510 -653 296 -305 545 -476 927 -635 282 -118 490 -185 607 -197 81 -8 258 20 362 58 144 52 309 168 373 262 64 96 130 313 138 457 l6 95 -31 36 c-22 24 -112 78 -294 176 -432 232 -487 254 -555 218 -17 -8 -81 -73 -141 -143 -178 -207 -215 -243 -245 -243 -38 0 -287 127 -403 205 -135 92 -223 166 -334 281 -132 137 -275 333 -355 486 l-18 36 72 79 c95 101 134 162 172 268 39 108 37 141 -20 290 -51 133 -92 243 -163 434 -58 157 -101 221 -161 240 -57 17 -287 22 -334 7z"/></g></svg>
    </a>
<?php
}
?>
<?php
if($bodyShowAssistant == 1){
?>
    <div id="assistant-icon" class="assistant-icon">
        <img src="/_y/m/r/Logo/assistant-logo.png" alt="Assistant Icon">
    </div>
    <div id="assistant-chat" class="assistant-chat">
        <div class="chat-header">
            Pozitif Asistan
            <span id="assistant-minimize" class="assistant-minimize">&minus;</span>
            <span id="assistant-close" class="assistant-close btn">&times;</span>
        </div>
        <div id="chat-messages" class="chat-messages"></div>
        <div class="chat-input-container">
            <input type="text" id="user-input" class="chat-input" placeholder="Mesajınızı yazın...">
            <div id="waiting-animation" class="waiting-animation" style="display: none;"></div>
        </div>
    </div>
    <?php
}
?>
<?php
//Log::write("Tüm Çerezler: " . json_encode($_COOKIE), "special");
include_once MODEL .'Session.php';
$session = new Session($config->key,3600,"/",$config->hostDomain,$config->cookieSecure,$config->cookieHttpOnly,$config->cookieSameSite);
$cookieConsent = $session->getCookie("cookieConsent");
$tagManager = $siteConfig['tagManager'][0] ?? "";
$hasTagManager = isset($tagManager['tag_manager_content']);
echo isset($tagManager['tag_manager_content']) ? html_entity_decode($tagManager['tag_manager_content']) : "";
if(empty($cookieConsent)){
    ?>
    <div id="cookie-consent-modal">
        <p><?=str_replace('[cookiePolicyLink]', $cookiePolicyLink, _body_cerez_on_yazi); ?></p>
        <button id="accept-cookies"><?=_body_cerez_onay?></button>
        <button id="decline-cookies"><?=_body_cerez_ret?></button>
        <button id="settings-cookies"><?=_body_cerez_ayar?></button>
    </div>
    <div id="cookie-consent-popup-modal">
        <div id="cookie-consent-popup-modal-content">
            <div id="cookie-consent-popup-modal-header">
                <h2><?=_body_cerez_ayar?></h2>
                <button id="cookie-consent-popup-modal-close" class="cookie-consent-popup-modal-close btn">X</button>
            </div>
            <div id="cookie-consent-popup-modal-body">
                <div id="cookie-consent-popup-modal-body-content">
                    <!-- Çerez Ayarları Başlığı -->
                    <p class="title"><?=_body_cerez_ayar_baslik?></p>

                    <!-- Genel Çerez Açıklaması -->
                    <p><?=_body_cerez_ayar_yazi?></p>

                    <details>
                        <input type="checkbox" id="essential_cookies" name="essential_cookies" value="1" checked>
                        <summary><?=_body_cerez_ayar_zorunlu_cerez_baslik?> <span><?=_body_cerez_ayar_zorunlu_cerez_kabul?></span></summary>
                        <p><?=_body_cerez_ayar_zorunlu_cerez_yazi?></p>
                    </details>

                    <details>
                        <summary>
                            <?=_body_cerez_ayar_zorunlu_reklam_pazarlama_baslik?>
                            <input type="checkbox" id="ad_user_data" name="ad_user_data" value="1" checked>
                            <label class="toggle" for="ad_user_data"><span></span></label>
                        </summary>
                        <p><?=_body_cerez_ayar_zorunlu_reklam_pazarlama_yazi?></p>
                    </details>

                    <details>
                        <summary>
                            <?=_body_cerez_ayar_zorunlu_analitik_baslik?>
                            <input type="checkbox" id="analytics_storage" name="analytics_storage" value="1" checked>
                            <label class="toggle" for="analytics_storage"><span></span></label>
                        </summary>
                        <p><?=_body_cerez_ayar_zorunlu_analitik_yazi?></p>
                    </details>

                    <details>
                        <summary>
                            <?=_body_cerez_ayar_islevsel_cerez_baslik?>
                            <input type="checkbox" id="ad_storage" name="ad_storage" value="1" checked>
                            <label class="toggle" for="ad_storage"><span></span></label>
                        </summary>
                        <p><?=_body_cerez_ayar_islevsel_cerez_yazi?></p>
                    </details>

                    <div class="button-container">
                        <button id="cookie-consent-popup-modal-close" class="cookie-consent-popup-modal-close btn"><?=_body_cerez_ayar_button_kapat?></button>
                        <button id="cookie-consent-popup-modal-accept" class="btn><?=_body_cerez_ayar_button_kaydet?></button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <style>
        #cookie-consent-modal {
            display: block;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 20px;
            z-index: 1000;
        }

        #cookie-consent-modal a {
            color: #fff;
            text-shadow: 0 0 2px #000;
            text-decoration: underline;
        }

        #cookie-consent-modal button {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            background-color: #000;
            color: white;
            cursor: pointer;
        }

        #cookie-consent-modal button:hover {
            background-color: #45a049;
        }

        #cookie-consent-modal p {
            margin: 0 0 10px 0;
        }

        #cookie-consent-popup-modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 20px;
            z-index: 1000;
        }

        #cookie-consent-popup-modal-content {
            position: relative;
            background-color: #fefefe;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #888;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        #cookie-consent-popup-modal-header {
            padding: 10px;
            background-color: #000;
            border-radius: 5px 5px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #cookie-consent-popup-modal-header h2 {
            font-size: 16px;
            color: #fff;
        }

        #cookie-consent-popup-modal-header button {
            cursor: pointer;
            background-color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
        }

        #cookie-consent-popup-modal-body {
            padding: 20px;
        }

        #cookie-consent-popup-modal-body-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        #cookie-consent-popup-modal-body-content .title {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            font-weight: bold;
        }

        #cookie-consent-popup-modal-body-content .subTitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        #cookie-consent-popup-modal-body-content p {
            font-size: var(--font-size-small);
            color: #666;
            margin-bottom: 10px;
            text-align: left;
            margin-top: 15px;
        }

        #cookie-consent-popup-modal-body-content details {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            width: 100%;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        #cookie-consent-popup-modal-body-content details summary {
            font-size: var(--font-size-small);
            color: #333;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        #cookie-consent-popup-modal-body-content details>summary>span{font-size: var(--font-size-small);color: #666;font-weight: normal;float: right}
        #cookie-consent-popup-modal-body-content details[open] summary::before {
            content: "-";
            padding-right: 5px;
        }

        #cookie-consent-popup-modal-body-content summary::before {
            content: "+";
            padding-right: 5px;
            transition: transform 0.3s ease;
        }

        #cookie-consent-popup-modal-body-content details[open] summary::before {
            transform: rotate(180deg);
        }

        /* Toggle Switch Styles */
        #cookie-consent-popup-modal-body-content .toggle-container {
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        #cookie-consent-popup-modal-body-content input[type="checkbox"] {
            display: none;
        }

        #cookie-consent-popup-modal-body-content .toggle {
            position: relative;
            width: 40px;
            height: 20px;
            background-color: #ddd;
            border-radius: 20px;
            cursor: pointer;
            margin-left: 10px;
        }

        #cookie-consent-popup-modal-body-content .toggle span {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            background-color: #fff;
            border-radius: 50%;
            transition: 0.4s;
        }

        #cookie-consent-popup-modal-body-content input:checked + .toggle {
            background-color: #4F2EDC;
        }

        #cookie-consent-popup-modal-body-content input:checked + .toggle span {
            transform: translateX(20px);
        }

        #cookie-consent-popup-modal-body-content label {
            font-size: 14px;
            color: #333;
            cursor: pointer;
            margin-right: 10px;
        }

        /* Button Styles */
        #cookie-consent-popup-modal-body-content button {
            padding: 10px 20px;
            border: none;
            background-color: #000;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: var(--font-size-small);
        }

        #cookie-consent-popup-modal-body-content button:hover {
            background-color: #45a049;
        }


    </style>
<?php }?>
<?php
$casper = $session->getCasper();
$jsContents = $casper->getJsContents();

################# JS İÇİNDEKİ DİL SABİTLERİNİ DEĞİŞTİRELİM ###

//GENERAL PRODUCT QUANTITY
$jsContents = str_replace("[_toplam_urun_modeli]",_sepet_toplam_urun_modeli,$jsContents);
$jsContents = str_replace("[_toplam_urun_adedi]",_sepet_toplam_urun_adedi,$jsContents);
$jsContents = str_replace("[_uyelik_sepettoplamtutar_yazi]",_sepet_sepet_toplam_tutar_yazi,$jsContents);
$jsContents = str_replace("[_sepet_indirim_toplam_tutar_yazi]",_sepet_indirim_toplam_tutar_yazi,$jsContents);
$jsContents = str_replace("[_sepet_indiriml_toplam_tutar]",_sepet_indirimli_toplam_tutar_yazi,$jsContents);
$jsContents = str_replace("[_sepet_indirim_tutar_yazi]",_sepet_indirim_tutar_yazi,$jsContents);

$visitor = $casper->getVisitor();
$visitorUniqID=$visitor['visitorUniqID'];
$jsContents .= "const visitorUniqID = '".$visitorUniqID."';";
$memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;
if($bodyShowAssistant == 1) {
    $jsContents .= file_get_contents(JS . 'assistant.min.js');
}
if(!empty($jsContents)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {" .
            $jsContents;
            $shouldLoadTurnstile = false;
            if(($bodyShowNewsletter == 1 || $routerResult["contentName"]=="Page") && !$config->localhost) {


                if ($bodyShowNewsletter == 1) {
                    $shouldLoadTurnstile = true;
                }
                else {
                    $page = $session->getSession("page");
                    $pageType = $page["sayfatip"];

                    if ($pageType == 1 || $pageType == 17 || ($pageType == 22 && !$memberStatus || $pageType == 29)) {
                        $shouldLoadTurnstile = true;

                        $visitor = $casper->getVisitor();
                        if ($visitor['visitorIsMember']['memberStatus']) {
                            $shouldLoadTurnstile = false;
                        }
                    }
                }

                if ($shouldLoadTurnstile) {
                    echo '
                        var style = document.createElement("style");
                        style.innerHTML = `
                            #turnstile-container {
                                position: fixed;
                                bottom: 10px;
                                left: 50%;
                                transform: translateX(-50%);
                                z-index: 1000;
                            }
            
                            @media (min-width: 768px) {
                                #turnstile-container {
                                    top: 50%;
                                    left: auto;
                                    right: 10px;
                                    transform: translateY(-50%);
                                }
                            }
                        `;
                        document.head.appendChild(style);
                    ';
                    echo '
                        var turnstileContainer = document.createElement("div");
                        turnstileContainer.id = "turnstile-container";
                        turnstileContainer.setAttribute("data-sitekey", "'.CLOUDFLARE_SITE_KEY.'");
                        turnstileContainer.setAttribute("data-language", languageCode);
                        document.body.appendChild(turnstileContainer);
                    ';
                    echo 'loadScript("https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback");';

                    echo '
                        window.onloadTurnstileCallback = function() {
                            const turnstileContainer = document.getElementById("turnstile-container");
                            turnstile.render(turnstileContainer, {
                                sitekey: "'.CLOUDFLARE_SITE_KEY.'",
                                callback: (token) => {
                                    const formIds = [
                                        "cf-token-newsletter-form",
                                        "cf-token-login-form",
                                        "cf-token-register-form",
                                        "cf-token-remind-password-form",
                                        "cf-token-contact-form",
                                        "cf-token-appointment-form"
                                    ];
                                    
                                    formIds.forEach(function(id) {
                                        const input = document.getElementById(id);
                                        if (input) {
                                            input.value = token;
                                            var csrfInput = document.createElement("input");
                                            csrfInput.type = "hidden";
                                            csrfInput.name = "csrf_token";
                                            csrfInput.id = input.id.replace("cf-token", "csrf_token");
                                            csrfInput.value = "'. $helper->generateCsrfToken() .'";
                                            input.parentNode.insertBefore(csrfInput, input.nextSibling);
                                        }
                                    });
                                }
                            });
                        };
                    ';
                }

                echo "
                    function loadScript(src) {
                        var script = document.createElement('script');
                        script.src = src;
                        script.async = true;
                        script.defer = true;
                        document.head.appendChild(script);
                    }
                ";
            }

        echo "});</script>";
}
$casper->setJsContents("");
$session->updateSession("casper",$casper);

if(isset($_SESSION['passwordReset'])){
    unset($_SESSION['passwordReset']);
}

$siteConfig = $casper->getSiteConfig();
$siteHeaderSettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "header";
});
foreach ($siteHeaderSettings as $siteSetting) {
    if($siteSetting['element'] == "language") {
        ?><script>
        const languageSelect = document.getElementById('languageSelect');
        if (languageSelect) {
            languageSelect.addEventListener('change', function() {
                const selectedOption = languageSelect.options[languageSelect.selectedIndex];
                const newPath = selectedOption.getAttribute('data-link');
                //console.log("Yeni dil seçildi:", newPath);
                window.location.href = newPath;
            });
        }</script>
        <?php
    }
}
?>
<?php if(empty($cookieConsent)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($hasTagManager): ?>
            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'analytics_storage': 'denied'
            });
        <?php endif; ?>
        // İlk çerez uyarısı modalı (cookie-consent-modal) butonları
        var consentModal = document.getElementById('cookie-consent-modal');
        var acceptButton = document.getElementById('accept-cookies');
        var declineButton = document.getElementById('decline-cookies');
        var settingsButton = document.getElementById('settings-cookies');

        // Çerez tercihleri modalı (cookie-consent-popup-modal) butonları
        var consentPopupModal = document.getElementById('cookie-consent-popup-modal');
        var popupAcceptButton = document.getElementById('cookie-consent-popup-modal-accept');
        var popupCloseButtons = document.querySelectorAll('.cookie-consent-popup-modal-close');

        // Çerez tercihleri dizi olarak tutulacak
        var cookieConsent = {
            'essential_cookies': true, // Zorunlu çerezler her zaman aktif
            'ad_user_data': true,
            'ad_personalization': true,
            'ad_storage': true,
            'analytics_storage': true
        };

        // İlk defa gelen ziyaretçi için modal göster
        if (!document.cookie.includes("cookieConsent")) {
            consentModal.style.display = 'block';
        }

        // Kabul Et butonuna tıklama
        acceptButton.addEventListener('click', function () {
            // Tüm çerezlere izin verildi
            setAllCookieConsent(true);
            saveCookieConsent();
            consentModal.style.display = 'none';
        });

        // Reddet butonuna tıklama
        declineButton.addEventListener('click', function () {
            // Zorunlu çerezler hariç hepsi reddedildi
            setAllCookieConsent(false);
            cookieConsent.essential_cookies = true; // Zorunlu çerezler etkin kalacak
            saveCookieConsent();
            consentModal.style.display = 'none';
        });

        // Kişiselleştir butonuna tıklama
        settingsButton.addEventListener('click', function () {
            consentModal.style.display = 'none';
            consentPopupModal.style.display = 'block';
        });

        // Tercihler modalındaki Kapat butonuna tıklama
        popupCloseButtons.forEach(function(button) {
            button.addEventListener('click', function () {
                consentPopupModal.style.display = 'none';
                consentModal.style.display = 'block'; // İlk uyarıya geri dön
            });
        });

        // Tercihleri Kaydet butonuna tıklama
        popupAcceptButton.addEventListener('click', function () {
            // Kullanıcı tercihlerini modal içindeki checkbox'lardan al
            cookieConsent.ad_user_data = document.getElementById('ad_user_data').checked;
            cookieConsent.ad_personalization = document.getElementById('ad_user_data').checked;
            cookieConsent.ad_storage = document.getElementById('ad_storage').checked;
            cookieConsent.analytics_storage = document.getElementById('analytics_storage').checked;

            saveCookieConsent();
            consentPopupModal.style.display = 'none';
        });

        // Tüm çerez izinlerini ayarlayan fonksiyon
        function setAllCookieConsent(status) {
            cookieConsent.ad_user_data = status;
            cookieConsent.ad_personalization = status;
            cookieConsent.ad_storage = status;
            cookieConsent.analytics_storage = status;
        }

        // Çerez tercihlerini kaydeden fonksiyon
        function saveCookieConsent() {
            console.log("Çerez tercihler kaydediliyor:", cookieConsent);
            var uri = "/?/control/cookie/get/createCookie&name=cookieConsent&value=" + encodeURIComponent(JSON.stringify(cookieConsent));

            fetch(uri, {
                method: 'GET',
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json(); // Yanıtı JSON olarak işle
                })
                .then(data => {
                    console.log(data); // JSON yanıtını konsolda göster
                    if (data.status === "success") {
                        console.log("Çerez tercihler başarıyla kaydedildi.");
                    } else {
                        console.error("Çerez tercihler kaydedilirken hata oluştu:", data.message);
                    }
                })
                .then(() => {
                    // Tag Manager varsa güncelle
                    <?php if ($hasTagManager): ?>
                    gtag('consent', 'update', {
                        'ad_user_data': cookieConsent.ad_user_data ? 'granted' : 'denied',
                        'ad_personalization': cookieConsent.ad_personalization ? 'granted' : 'denied',
                        'ad_storage': cookieConsent.ad_storage ? 'granted' : 'denied',
                        'analytics_storage': cookieConsent.analytics_storage ? 'granted' : 'denied'
                    });
                    <?php endif; ?>
                })
                .catch(error => {
                    console.error("Çerez tercihler kaydedilirken hata oluştu:", error);
                });
        }
    });
</script>
<?php endif; ?>
<?php
$adConversionCode = $siteConfig['adConversionCode'][0] ?? "";
echo isset($adConversionCode['ad_conversion_code_content']) ? html_entity_decode($adConversionCode['ad_conversion_code_content']) : "";

// Platform Tracking Conversion Codes
$documentRoot = $_SERVER['DOCUMENT_ROOT'];
include_once $documentRoot . '/App/Helpers/PlatformTrackingManager.php';
$platformTrackingManager = new PlatformTrackingManager($db, $config);
$platformConversionCodes = $platformTrackingManager->generateConversionCodes($languageID);
if (!empty($platformConversionCodes)) {
    echo $platformConversionCodes;
}

$gTagBasket = $session->getSession('gTagBasket') ?? [];

if(isset($gTagBasket['currency'])){
    echo 'currency var';
    $cartConversionCode = $siteConfig['cartConversionCode'][0] ?? "";
    $cartConversionCode = isset($cartConversionCode['cart_conversion_code']) ? html_entity_decode($cartConversionCode['cart_conversion_code']) : "";

    if(!empty($cartConversionCode)){
        
        $currency = $gTagBasket['currency'];
        $value = $gTagBasket['value'];
        $items_json = json_encode($gTagBasket['items']); // JSON formatına çevir

        // Placeholder'ları değişkenlerle değiştir
        $cartConversionCode = str_replace(
            ["{\$currency}", "{\$value}", "{items}"],
            [$currency, $value, $items_json],
            $cartConversionCode
        );
        // Dinamik olarak oluşturulmuş kodu ekrana bas
        echo $cartConversionCode;
    }

    $session->removeSession('gTagBasket');
}

$pageSession = $session->getSession('page') ?? [];
if(!empty($pageSession)){

    $salesStatusSession = $session->getSession('salesStatus') ?? [];

    if(!empty($salesStatusSession)){
        $gTagSession = $session->getSession('gTag') ?? [];

        if(!empty($gTagSession)){

            $transaction_id = $gTagSession['transaction_id'];
            $value = $gTagSession['value'];
            $tax = $gTagSession['tax'];
            $shipping = $gTagSession['shipping'];
            $currency = $gTagSession['currency'];
            $coupon = $gTagSession['coupon'];
            $items_json = json_encode($gTagSession['items']); // JSON formatına çevir

            $salesConversionCodeSettings = $siteConfig['salesConversionCodeSettings'][0] ?? "";
            $salesConversionCode = isset($salesConversionCodeSettings['satisdonusumkod']) ? html_entity_decode($salesConversionCodeSettings['satisdonusumkod']) : "";

            if(!empty($salesConversionCode)){

                // Placeholder'ları değişkenlerle değiştir
                $salesConversionCode = str_replace(
                    ["{\$transaction_id}", "{value}", "{tax}", "{shipping}", "{currency}", "{coupon}", "{items}"],
                    [$transaction_id, $value, $tax, $shipping, $currency, $coupon, $items_json],
                    $salesConversionCode
                );
                // Dinamik olarak oluşturulmuş kodu ekrana bas
                echo $salesConversionCode;

            }

            $session->removeSession('gTag');
        }

        $session->removeSession('salesStatus');
    }
}
?>
</body>
