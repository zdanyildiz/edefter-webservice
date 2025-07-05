<?php
/**
 * @var Session $session
 */

$routerResult = $session->getSession("routerResult");
$casper = $session->getCasper();
$config = $casper->getConfig();

$siteConfig = $casper->getSiteConfig();
$generalSettings = $siteConfig["generalSettings"];
$siteType = $generalSettings["sitetip"];
$isMemberRegistration = $generalSettings["uyelik"];
$siteSettings = $siteConfig['siteSettings'];

$helper = $config->Helper;

$siteConfig = $casper->getSiteConfig();
$bannerInfo = $siteConfig["bannerInfo"];

$pageLinks = $siteConfig['specificPageLinks'];

if($siteType==1 || $isMemberRegistration==1):
    $memberLinkItem = array_filter($pageLinks, function($pageLink) {
        return $pageLink['sayfatip'] == 17;
    });

    $memberLinkItem = reset($memberLinkItem);
    $memberLink = $memberLinkItem['link'];

    $config->loadView("Layouts/aside_left",["casper"=>$casper,"memberLink"=>$memberLink]);
endif;
?>
<main>
    <?php
    if($routerResult["contentName"]=="HomePage") {

        $homePageData = [
            "config"=>$config,
            "session"=>$session,
            "routerResult"=>$routerResult,
            "bannerInfo"=>$bannerInfo,
        ];

        $config->loadView("HomePage/HomePage",$homePageData);
    }
    elseif($routerResult["contentName"]=="Page") {

        $pageData = [
            "config"=>$config,
            "session"=>$session,
            "languageCode"=>$routerResult["languageCode"],
            "routerResult"=>$routerResult,
            "bannerInfo"=>$bannerInfo,
        ];

        $config->loadView("Page/PageMain",$pageData);

    }
    elseif($routerResult["contentName"]=="Category") {
        $categoryData = [
            "casper"=>$casper,
            "config"=>$config,
            "session"=>$session,
            "languageCode"=>$routerResult["languageCode"],
            "bannerInfo"=>$bannerInfo,
        ];
        $config->loadView("Category/Category",$categoryData);
    }
    elseif($routerResult["contentName"]=="Search") {
        $searchData = [
            "casper"=>$casper,
            "config"=>$config,
            "session"=>$session,
            "routerResult"=>$routerResult
        ];
        $config->loadView("Search/Search",$searchData);
    }

    if($routerResult["contentName"]!=="Search"){
        $bottomBannerData = ["bannerInfo"=>$bannerInfo,"config"=>$config];
        $config->loadView("Layouts/bottomBanner",$bottomBannerData);
    }
    ?>
    <?php
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
    $bottomBannerResult = $bannerManager->getBottomBanners($bannerPageID, $bannerCategoryID);
    $bottomBannersHtml = $bottomBannerResult['html'];
    ?>
    <?php
    if(!empty($bottomBannersHtml)){?>
        <div id="banner-type-alt-banner"><?=$bottomBannersHtml?></div>
    <?php }?>
    <?php
    $siteBodySettings = array_filter($siteSettings, function($siteSetting) {
        return $siteSetting['section'] == "body";
    });
    foreach ($siteBodySettings as $siteSetting) {
        if ($siteSetting['element'] == "newsletter") {
            $bodyShowNewsletter = $siteSetting['is_visible'] == 1 ? 1 : 0;
        }
    }

    if($bodyShowNewsletter == 1) {
    ?>
    <div class="newsletter-container">
        <div class="newsletter_block">
            <h5><?=_body_newsletter_title?></h5>
            <p><?=_body_newsletter_text?></p>
            <form class="newsletter" id="newsletterForm" action="/?/control/form/post/newsletterForm" method="POST">
                <input type="text" name="namesurname" class="form-control" required placeholder="<?=_body_newsletter_name_placeholder?>">
                <input type="email" name="email" class="form-control" required placeholder="<?=_body_newsletter_email_placeholder?>">
                <input type="hidden" name="cf-turnstile-response" id="cf-token-newsletter-form">
                <button type="submit" class="educate-btn sm">
                    <span class="educate-btn__curve"></span>
                    <span class="educate-btn__text"><?=_body_newsletter_button_text?></span>
                </button>
            </form>
        </div>
    </div>
    <?php } ?>
</main>
<?php

$checkoutLinkItem = array_filter($pageLinks, function($pageLink) {
    return $pageLink['sayfatip'] == 9;
});

$checkoutLinkItem = reset($checkoutLinkItem);
$checkoutLink = $checkoutLinkItem['link'];

$asideRightData = ["session"=>$session,"casper"=>$casper,"config"=>$config,"siteConfig"=>$siteConfig,"checkoutLink"=>$checkoutLink];
$config->loadView("Layouts/aside_right",$asideRightData);