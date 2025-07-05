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

$ampPrefix = $config->ampPrefix;
$ampLayout = $config->ampLayout;
$ampImgEnd = $config->ampImgEnd;

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
<section class="categoryMainContent">
    <aside class="categoryLeftAside">
        <div class="categoryLeftAsideContainer">
            <?php if(!empty($subCategories)):?>
                <div class="categoryLeftAsideTitle">
                    <h2><?php echo $categoryInfo["kategoriad"]; ?><?php echo 'sub'; print_r($subCategories); ?></h2>
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
        </div>
    </aside>
    <div class="categoryProductContent">
        <div class="categoryMainContentContainer">
            <div class="categoryMainContentTitle">
                <h1><?php echo $categoryInfo["kategoriad"]; ?></h1>
            </div>
            <!-- categori içerik metni boş değilse içeriği getirelim -->
            <?php if (!empty($categoryInfo["kategoriicerik"])): ?>
                <div class="categoryMainContentText">
                    <!-- resim_url de boşdeğilse kategori resmini de ekleyelim -->
                    <?php if (!empty($categoryInfo["resim_url"])): ?>
                        <figure>
                            <img src="<?php echo imgRoot."?imagePath=".$categoryInfo["resim_url"]."&width=200&height=200"; ?>" alt="<?php echo $categoryInfo["kategoriad"]; ?>">
                        </figure>
                    <?php endif; ?>
                    <div class="categoryContentText">
                        <?php echo $categoryInfo["kategoriicerik"]; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="categoryMainContentContent">
                <div class="categoryMainContentContentContainer">
                    <?php
                    $previousCategoryName = null;
                    $pb = 0;
                    foreach ($categoryPages as $product) {
                        $product = $product['productDetails'];

                        $productCategoryName = $product[0]['kategoriad'];
                        if ($productCategoryName != $categoryInfo["kategoriad"]) {
                            // Eğer önceki kategori adı ile şimdiki kategori adı aynı değilse yeni bir kategori başlığı yazdır
                            if ($previousCategoryName != $productCategoryName) {
                                echo "<h2>" . $productCategoryName . "</h2>";
                                $previousCategoryName = $productCategoryName;
                            }
                        }
                        include VIEW . "Product/ProductBox.php";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
