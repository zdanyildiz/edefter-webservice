<?php
/**
 * @var array $visitor
 * @var array $memberInfo
 * @var Helper $helper
 * @var Session $session
 * ævar Casper $casper
 */
$casper = $session->getCasper();

$config = $casper->getConfig();
$helper = $config->Helper;
$visitor = $casper->getVisitor();
$favorites = $visitor['visitorIsMember']['memberFavorites'] ?? [];

$configPriceSettings = $casper->getSiteConfig()["priceSettings"][0];
$configShowDiscount = $configPriceSettings["eskifiyat"];
$configShowPrice = $configPriceSettings["fiyatgoster"];
$configPriceUnit = $configPriceSettings["parabirim"];

$currencyRates = $casper->getSiteConfig()["currencyRates"];
//print_r($currencyRates);exit();

$usdToTry = $currencyRates["usd"];
$eurToTry = $currencyRates["euro"];

$ampPrefix = $config->ampPrefix;
$ampLayout = $config->ampLayout;
$ampImgEnd = $config->ampImgEnd;
?>
<div class="member-container">
    <div class="favorite-product-container">
        <h1><?=_uye_favorilerim_baslik?></h1>
        <?php
        if(!empty($favorites)) {
            $pb = 0;
            foreach ($favorites as $product) {
                include VIEW . "Product/ProductBox.php";
            }
        }
        ?>
    </div>
</div>
