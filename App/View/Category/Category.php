<?php
/**
 * @var Session $session
 * @var string $languageCode
 * @var Config $config
 * @var Casper $casper
 * @var string $languageCode
 */
$casper = $session->getCasper();
$routerResult = $session->getSession("routerResult");
$resultQuery = $routerResult["query"];

$category = $session->getSession("category");
//print_r($session);exit;
$categoryInfo = $category["category"];
//print_r($categoryInfo);
$categoryType = $categoryInfo["kategorigrup"];

$subCategories = $categoryInfo["subCategories"];

$categoryHierarchy = $categoryInfo["categoryHierarchy"];

$categoryPages = $category["categoryPages"];

$categoryFilterData = $category["filterData"];
$filterLinks = $category["filterLinks"];

$categorySelectedFilterData = $category["selectedFilterData"];
$selectedFilterLinks = $category["selectedFilterLinks"];

$siteConfig = $casper->getSiteConfig();
$configPriceSettings = $siteConfig["priceSettings"][0];
$configShowDiscount = $configPriceSettings["eskifiyat"];
$configShowPrice = $configPriceSettings["fiyatgoster"];
$configPriceUnit = $configPriceSettings["parabirim"];

$currencyRates = $casper->getSiteConfig()["currencyRates"];

$usdToTry = $currencyRates["usd"];
$eurToTry = $currencyRates["euro"];

$ampStatus = $config->ampStatus;
$ampPrefix = $config->ampPrefix;
$ampLayout = $config->ampLayout;
$ampImgEnd = $config->ampImgEnd;

$bannerInfo = [];

$categoryHeaderBanner= array_filter($bannerInfo, function($banner) {
    return $banner['bannerkategori'] === 7;
}) ?? [];
if(count($categoryHeaderBanner)>0){
    foreach ($categoryHeaderBanner as $banner) {
        $categoryHeaderBannerText = (!empty($banner['bannerslogan']) && $banner['bannerslogan'] != "#") ? $banner['bannerslogan'] : $category['kategoriad'];
        $slogan = "";
        foreach ($categoryHierarchy as $category) {
            $slogan .= '<a href="'.$category['link'].'">'.$category['kategoriad'].'</a>';
        }
        $categoryHeaderBannerSlogan = (!empty($banner['banneryazi']) && $banner['banneryazi'] != "#") ? $banner['banneryazi'] : $slogan;
    }
    ?>
    <div class="page-header-banner">
        <h1><?=$categoryHeaderBannerText?></h1>
        <p><?=$categoryHeaderBannerSlogan?></p>
    </div>
    <?php
}
else{
?>
<nav class="breadcrumbContainer">
    <ol class="breadcrumb bg-white">
        <?php
        foreach ($categoryHierarchy as $category) {
            ?>
            <li class="breadcrumb-item"><a href="<?php echo $category['link']; ?>"><?php echo $category['kategoriad']; ?></a></li>
            <?php
        }
        ?>
    </ol>
</nav>
<?php }?>
<section class="categoryMainContent">
    <?php if(($categoryType == 7 && !empty($subCategories) && !empty($filterLinks) ) || ($categoryType == 24 || $categoryType == 26) ):?>
    <aside class="categoryLeftAside">
        <div class="categoryLeftAsideContainer">
            <?php if(!empty($subCategories)):?>
                <div class="categoryLeftAsideTitle">
                    <h2><?php echo $categoryInfo["kategoriad"]; ?></h2>
                </div>
                <div class="categoryLeftAsideContent">
                    <ul class="categoryLeftAsideMenu">
                        <?php
                        foreach ($subCategories as $subCategory) {
                            ?>
                            <li><a href="<?php echo $subCategory['link']; ?>"><?php echo $subCategory['kategoriad']; ?></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php endif;?>
            <?php if(!empty($filterLinks)):?>
                <div class="categoryLeftAsideContent">
                    <div class="categoryLeftAsideFilterTitle">
                        <h2><?=_kategori_filtrele_yazi?></h2>
                    </div>
                    <?php
                    foreach ($filterLinks as $filterGroupName => $filterGroupValues) {
                        ?>
                        <h3><?php echo ucfirst($filterGroupName); ?></h3>
                        <ul class="categoryLeftAsideMenu">
                            <?php
                            foreach ($filterGroupValues as $filterGroupValue) {
                                foreach ($filterGroupValue as $filterName => $filterLink) {
                                    ?>
                                    <li><a href="<?php echo $filterLink; ?>"><?php echo $filterName; ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
            <?php endif;?>
            <?php if(!empty($selectedFilterLinks)){ ?>
                <div class="categoryLeftAsideFilterTitle">
                    <h2><?=_kategori_filtre_temizle_yazi?></h2>
                </div>
                <div class="categoryLeftAsideContent">
                    <?php
                    foreach ($selectedFilterLinks as $selectedFilterGroupName => $selectedFilterGroupValues) {
                        ?>
                        <h3><?php echo ucfirst($selectedFilterGroupName); ?></h3>
                        <ul class="categoryLeftAsideMenu">
                            <?php
                            foreach ($selectedFilterGroupValues as $selectedFilterGroupValue) {
                                foreach ($selectedFilterGroupValue as $selectedFilterName => $selectedFilterLink) {
                                    ?>
                                    <li class="clear-filter"><a href="<?php echo $selectedFilterLink; ?>"><?php echo $selectedFilterName; ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
            <?php } ?>
            <?php //<!-- kategori tipi sss ya da blog ise sayfa başlıklarını gösterelim -->
            if(($categoryType == 26 || $categoryType == 24)){
                ?>
                <div class="categoryLeftAsideContent summary" data-category-group="<?=$categoryInfo["kategorigrup"]?>">
                    <h3></h3>
                <?php
                foreach ($categoryPages as $page) {
                    $page = $page['pageDetails'] ?? [];
                    $pageTitle = $page['sayfaad'];
                    $pageSeoLink = $page['seoLink'];
                    ?>
                    <li><a href="<?php echo $pageSeoLink; ?>"><?php echo $pageTitle; ?></a></li>
                    <?php
                }
                ?>
                </div>
                <?php
            }?>
        </div>
    </aside>
    <?php endif;?>
    <div class="categoryProductContent">
        <?php if(!empty($categoryInfo['sliderBanner'])):?>
        <div class="categoryMainContentContent">
            <?php echo ($categoryInfo['sliderBanner']) ? $categoryInfo['sliderBanner'] : '';?>
        </div>
        <?php endif;?>
        <div class="categoryMainContentContainer">
            <div class="categoryMainContentTitle">
                <h1><?php echo $categoryInfo["kategoriad"]; ?></h1>
            </div>
            <?php if (!empty($categoryInfo["kategoriicerik"])): ?>
                <div class="categoryMainContentText">
                    <div class="categoryContentText">
                        <?php if (!empty($categoryInfo["resim_url"])): ?>
                            <figure>
                                <img src="<?php echo imgRoot."?imagePath=".$categoryInfo["resim_url"]."&width=450"; ?>" alt="<?php echo $categoryInfo["kategoriad"]; ?>">
                            </figure>
                        <?php endif; ?>
                        <?php echo $categoryInfo["kategoriicerik"]; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php echo ($categoryInfo['middleBanner']) ? $categoryInfo['middleBanner'] : '';?>
        </div>
        <div class="categoryMainContentContent">
            <div class="categoryMainContentContentContainer">
                <?php
                if($categoryInfo["kategorigrup"] == 7){
                    $previousCategoryName = null;
                    $pb = 0;
                    foreach ($categoryPages as $product) {
                        $product = $product['productDetails'] ?? [];

                        $productCategoryName = $product[0]['kategoriad'] ?? "";
                        if ($productCategoryName != $categoryInfo["kategoriad"]) {
                            // Eğer önceki kategori adı ile şimdiki kategori adı aynı değilse yeni bir kategori başlığı yazdır
                            if ($previousCategoryName != $productCategoryName) {
                                echo "<h2>" . $productCategoryName . "</h2>";
                                $previousCategoryName = $productCategoryName;
                            }
                        }
                        include VIEW . "Product/ProductBox.php";
                    }
                }
                elseif($categoryInfo["kategorigrup"] != 26 && !empty($categoryPages)){ //blog
                    foreach ($categoryPages as $page) {
                        $page = $page['pageDetails'] ?? [];
                        $pageTitle = $page['sayfaad'];
                        $pageContent = $page['sayfaicerik'];
                        $imageUrls = $page['resim_url'];
                        $imageUrls = ($imageUrls) ? explode(",",$imageUrls) : [];
                        $pageSeoLink = $page['seoLink'];
                        include VIEW . "Category/BlogPageBox.php";
                    }
                }
                ?>
            </div>
            <!-- kategori tipi 26 ise SSS içerikleri gösterilecek details ve summary kullanacağız -->
            <?php
            if($categoryType == 26){
                foreach ($categoryPages as $page) {

                    $page = $page['pageDetails'] ?? [];
                    $pageTitle = $page['sayfaad'];
                    $pageContent = $page['sayfaicerik'];
                    $imageUrls = $page['resim_url'];
                    $imageUrls = ($imageUrls) ? explode(",",$imageUrls) : [];
                    $pageSeoLink = $page['seoLink'];
                    ?>
                    <details>
                        <summary><?php echo $pageTitle; ?></summary>
                        <div class="categorySummaryContentText">
                            <?php echo $pageContent; ?>
                        </div>
                    </details>
                    <?php
                }
            }?>
        </div>
    </div>
</section>
