<?php
/**
 * @var Session $session
 * @var array $bannerInfo
 * @var Casper $casper
 */

################# TANIMLALAR ################################
;
$siteConfig = $casper->getSiteConfig() ?? [];

$generalSettings = $siteConfig["generalSettings"] ?? [];
$siteType = $generalSettings["sitetip"];
$isMemberRegistration = $generalSettings["uyelik"];

$siteSettings = $siteConfig["siteSettings"];

$siteHeaderSettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "header";
});

$headerShowMenu = 0;
foreach($siteHeaderSettings as $siteHeaderSetting){
    if($siteHeaderSetting['element'] == "menu"){
        $headerShowMenu = $siteHeaderSetting['is_visible'] == 1 ? 1 : 0;
    }
}

$siteBodySettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "body";
});

$bodyShowNewsletter=0;
foreach($siteBodySettings as $siteBodySetting){
    if($siteBodySetting['element'] == "newsletter"){
        $bodyShowNewsletter = $siteBodySetting['is_visible'] == 1 ? 1 : 0;
    }
}

$siteFooterSettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "footer";
});

foreach($siteFooterSettings as $siteFooterSetting){
    if($siteFooterSetting['element'] == "logo"){
        $footerShowLogo = $siteFooterSetting['is_visible'] == 1 ? 1 : 0;
    }
}

################# GENEL JS İÇERİĞİ ##########################
$jsContents = file_get_contents(JS.'general.min.js');

################# GENEL CSS İÇERİĞİ ##########################

// Index css
if(isset($_SESSION["previewDesign"]) && $_SESSION["previewDesign"] == true){
    $cssContents = file_get_contents(CSS.'index-preview-'.$languageID.'.css');
}
elseif(file_exists(CSS.'index-'.$languageID.'.css')){
    $cssContents = file_get_contents(CSS.'index-'.$languageID.'.css');
}
else{
    $cssContents = file_get_contents(CSS.'index.min.css');
}

// Body css
$cssContents .= file_get_contents(CSS.'Layouts/body.min.css');

// Header css
$cssContents .= file_get_contents(CSS . 'Layouts/header.min.css');
if($headerShowMenu == 1){
    $cssContents .= "@media (max-width: 1280px){.header{flex-direction:column;}.logo-container{width:auto}}";
}
//Top Menu css
$cssContents .= file_get_contents(CSS . 'Layouts/nav-top.min.css');
// Nav Main Css
$cssContents .= file_get_contents(CSS . 'Layouts/nav-main.min.css');

if($bodyShowNewsletter == 1){
    $cssContents .= file_get_contents(CSS . 'Layouts/newsLetter.min.css');
    $jsContents .= file_get_contents(JS . 'newsletter.min.js');
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
}
// Footer css
$cssContents .= file_get_contents(CSS.'Layouts/footer.min.css');

// Nav Footer css
$cssContents .= file_get_contents(CSS.'Layouts/nav-footer.min.css');
if($footerShowLogo == 1) {
    $logoSettings = $casper->getSiteConfig()["logoSettings"];
    $logoImg = $logoSettings["resim_url"];
    $cssContents .= 'footer::before {content: "";position: absolute;left: 0;bottom: 0;background: url(' . imgRoot . "?imagePath=" . $logoImg . '&width=300) no-repeat bottom left;background-size: auto; opacity: 0.2; background-position: bottom left;width: var(--footer-logo-width);height: var(--footer-logo-height);pointer-events: none;z-index:0}';
}
if($siteType==1 || $isMemberRegistration==1):
    // Aside Left css
    $cssContents .= file_get_contents(CSS . 'Layouts/aside_left.min.css');
endif;

$cssContents .= file_get_contents(CSS . 'Layouts/main.min.css');
$cssContents .= file_get_contents(CSS . 'Layouts/assistant.min.css');

################# VISITOR INF FOR DEVELOPER ON LOCALHOST #####
if($casper->getConfig()->localhost){
    $cssContents .= file_get_contents(CSS . 'Layouts/aside_right_visitor.min.css');
    $jsContents .= file_get_contents(JS . 'rightCartVisitor.min.js');
}

$casper->setJsContents($jsContents);
$casper->setCssContents($cssContents);

$session->updateSession("casper",$casper);