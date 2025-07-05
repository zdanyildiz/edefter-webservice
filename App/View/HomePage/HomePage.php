<?php
/**
 * @var Session $session
 * @var Config $config
 * @var Casper $casper
 * @var Helper $helper
 * @var array $routerResult
 */
$casper = $session->getCasper();
$routerResult = $session->getSession("routerResult");
$mainPage = $session->getSession("mainPage");
//echo '<pre>';print_r($mainPage);exit;

if(isset($mainPage['homePageSlider']) && !empty($mainPage['homePageSlider'])){
    echo $mainPage['homePageSlider'];
}

$homePageCategoryContent = $mainPage['homePageCategoryContent'];
$homePageCategoryImage = $mainPage['homePageCategoryImage'];

$homePageCategoryTitle = $routerResult["seoTitle"];
?><?php if (!empty($homePageCategoryContent)): ?>
    <div class="homePageMainContentContainer">
            <div class="homePageMainContentText">
                <?php if (!empty($homePageCategoryImage)): ?>
                    <figure>
                        <img src="<?php echo imgRoot."?imagePath=".$homePageCategoryImage."&width=450" ?>" alt="<?php echo $homePageCategoryTitle; ?>">
                    </figure>
                <?php endif; ?>
                <div class="homePageContentText">
                    <?php echo $homePageCategoryContent; ?>
                </div>
            </div>

    </div>
<?php endif; ?>
<?php
if(isset($mainPage['homePageMiddleBanner']) && !empty($mainPage['homePageMiddleBanner'])){
    echo $mainPage['homePageMiddleBanner'];
}

$homePageProductsData = [
    "homePageNewProducts" => $mainPage['homePageNewProducts'],
    "homePageSpecialOfferProducts" => $mainPage['homePageSpecialOfferProducts'],
    "homePageHomepageProducts" => $mainPage['homePageHomepageProducts'],
    "homePageDiscountedProducts" => $mainPage['homePageDiscountedProducts'],
    "configPriceSettings" => $casper->getSiteConfig()["priceSettings"][0] ?? [],
    "currencyRates" => $casper->getSiteConfig()["currencyRates"] ?? [],
    "ampPrefix" => $config->ampPrefix,
    "ampLayout" => $config->ampLayout,
    "ampImgEnd" => $config->ampImgEnd
];
$config->loadView("HomePage/Products/HomePageProducts",$homePageProductsData);

$homePageCarouselBanners = $mainPage['homePageCarouselBanners'] ?? [];
$config->loadView("HomePage/Banners/HomePageCarousel",["carouselBanner"=>$homePageCarouselBanners, "config"=>$config]);