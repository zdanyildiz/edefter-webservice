<?php
/**
 * @var Session $session
 * @var Config $config,
 * @var array $routerResult
 */


$casper = $session->getCasper();
$helper = $config->Helper;

$languageCode = $helper->toLowerCase($routerResult["languageCode"]);

$siteConfig = $casper->getSiteConfig();

$pageLinks = $siteConfig['specificPageLinks'];

$checkoutLinkItem = array_filter($pageLinks, function($pageLink) {
    return $pageLink['sayfatip'] == 9;
});
$checkoutLinkItem = reset($checkoutLinkItem);
$checkoutLink = $checkoutLinkItem['link'];

$paymentLinkItem = array_filter($pageLinks, function($pageLink) {
    return $pageLink['sayfatip'] == 22;
});
$paymentLinkItem = reset($paymentLinkItem);
$paymentLink = $paymentLinkItem['link'];

$membershipAgreementLinkItem = array_filter($pageLinks, function($pageLink) {
    return $pageLink['sayfatip'] == 10;
});
$membershipAgreementLinkItem = reset($membershipAgreementLinkItem);
$membershipAgreementLink = $membershipAgreementLinkItem['link'];

//mesafeli satış sözleşmesi linki
$distanceSalesAgreementLinkItem = array_filter($pageLinks, function($pageLink) {
    return $pageLink['sayfatip'] == 12;
});
$distanceSalesAgreementLinkItem = reset($distanceSalesAgreementLinkItem);
$distanceSalesAgreementLink = $distanceSalesAgreementLinkItem['link'];

$seoLink = $routerResult["seoLink"];
$seoTitle = $routerResult["seoTitle"];

$page = $session->getSession("page");
$page['seoLink'] = $seoLink;
//print_r($page);exit;
$pageType = $page["sayfatip"];
$categoryHierarchy = $page["categoryHierarchy"];

$ampStatus = $config->ampStatus;
$ampPrefix = $config->ampPrefix;
$ampLayout = $config->ampLayout;
$ampImgEnd = $config->ampImgEnd;

$bannerInfo = [];

$pageHeaderBanner= array_filter($bannerInfo, function($banner) {
    return $banner['bannerkategori'] === 7;
}) ?? [];
if(count($pageHeaderBanner)>0){
    foreach ($pageHeaderBanner as $banner) {
        $pageHeaderBannerText = (!empty($banner['bannerslogan']) && $banner['bannerslogan'] != "#") ? $banner['bannerslogan'] : $seoTitle;
        $slogan = "";
        foreach ($categoryHierarchy as $category) {
            $slogan .= '<a href="'.$category['link'].'">'.$category['kategoriad'].'</a>';
        }
        $pageHeaderBannerSlogan = (!empty($banner['banneryazi']) && $banner['banneryazi'] != "#") ? $banner['banneryazi'] : $slogan;
    }
    ?>
        <div class="page-header-banner">
            <h1><?=$pageHeaderBannerText?></h1>
            <p><?=$pageHeaderBannerSlogan?></p>
        </div>
    <?php
}
else{
?>
<nav class="breadcrumbContainer">
    <ol class="breadcrumb bg-white">
        <?php
        foreach ($categoryHierarchy as $category) {
            ?><li class="breadcrumb-item"><a href="<?php echo $category['link']; ?>"><?php echo $category['kategoriad']; ?></a></li><?php
        }
        ?><li class="breadcrumb-item active" aria-current="page"><a href="<?=$seoLink?>"><?php echo $seoTitle; ?></a></li>
    </ol>
</nav>
<?php }?>
<?php
$pageData = [
    "casper"=>$casper,
    "config"=>$config,
    "page" => $page,
    "ampStatus"=>$ampStatus,
    "ampPrefix"=>$ampPrefix,
    "ampLayout"=>$ampLayout,
    "ampImgEnd"=>$ampImgEnd,
    "session"=>$session,
    "sliderBanner"=>$page['sliderBanner'],
    "middleBanner"=>$page['middleBanner']
];

if($pageType==7){
    $pageData['productDetails'] = $page["productDetails"];
    $config->loadView("Page/ProductDetail",$pageData);
}
elseif($pageType==8){
    $pageData['checkoutLink'] = $checkoutLink;
    $config->loadView("Page/MyCart",$pageData);
}
elseif($pageType==9){
    $pageData['paymentLink'] = $paymentLink;
    $pageData['distanceSalesAgreementLink'] = $distanceSalesAgreementLink;
    $pageData['languageCode'] = $languageCode;
    $config->loadView("Page/Checkout",$pageData);
}
elseif($pageType==17){
    $pageData['languageCode'] = $languageCode;
    $pageData['query'] = $routerResult['query'];
    $pageData['membershipAgreementLink'] = $membershipAgreementLink;
    $config->loadView("Page/Member",$pageData);
}
elseif ($pageType==18){
    $pageData['languageCode'] = $languageCode;
    $config->loadView("Page/CancellationRefundExchangeResponse",$pageData);
}
elseif ($pageType==19){
    $pageData['languageCode'] = $languageCode;
    $config->loadView("Page/Favorite",$pageData);
}
elseif($pageType==22){

    $pageData['paymentLink'] = $paymentLink;
    $pageData['languageCode'] = $languageCode;
    $pageData['membershipAgreementLink'] = $membershipAgreementLink;
    $orderData = $session->getSession("orderData");
    $pageData['orderData'] = $orderData;

    $config->loadView("Page/Payment",$pageData);

}
elseif($pageType == 24){
    $config->loadView("Page/PageBlog",$pageData);
}
elseif($pageType == 29){
    // Online Randevu Formu
    $pageData['languageCode'] = $languageCode;
    $config->loadView("Page/Appointment",$pageData);
}
elseif($pageType == 30){
    // Online Randevu Formu
    $pageData['languageCode'] = $languageCode;
    $config->loadView("Page/eDefterWeb",$pageData);
}
else{
    $config->loadView("Page/PageDetail",$pageData);
}
?>