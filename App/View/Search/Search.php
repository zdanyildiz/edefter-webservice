<?php
/**
 * @var Session $session
 * @var Casper $casper
 * @var Config $config
 * @var array $routerResult
 * @var Helper $helper
 */
$casper = $session->getCasper();
$helper = $casper->getConfig()->Helper;
$languageCode = $helper->toLowerCase($routerResult["languageCode"]);

$routerResult = $session->getSession("routerResult");
$query = $routerResult["query"];
parse_str($query,$parsedQuery);

// $parsedQuery'den sayfalamayı çıkaralım

unset($parsedQuery['page']);

$query = http_build_query($parsedQuery);
$query = urldecode($query);

$search = $session->getSession("search");

$searchResultProducts = $search["searchResultProducts"];

$searchResultTotalPages = $search["searchResultTotalPages"];
$searchResultCurrentPage = $search["searchResultCurrentPage"];
$searchTotalResults = $search["searchTotalResults"];

$filterData = $search["filterData"];
$filterLinks = $search["filterLinks"];

$selectedFilterData = $search["selectedFilterData"];
$selectedFilterLinks = $search["selectedFilterLinks"];

$configPriceSettings = $casper->getSiteConfig()["priceSettings"][0];
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
        <li class="breadcrumb-item"><a href="#"><?php echo $routerResult["seoTitle"]; ?></a></li>

    </ol>
</nav>
<section class="searchMainContent">
    <?php if(!empty($filterLinks)):?>
    <aside class="searchLeftAside">
        <div class="searchLeftAsideContainer">
            <div class="searchLeftAsideTitle">
                <h2><?php echo $routerResult["seoTitle"]; ?></h2>
            </div>
            <div class="searchLeftAsideContent">
                <?php if(!empty($filterLinks)):?>
                <div class="searchLeftAsideFilterTitle">
                <h2><?=_arama_sayfasi_filtrele_yazi?></h2>
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
                endif;
                ?>
                <?php if(!empty($selectedFilterLinks)):?>
                    <div class="searchLeftAsideFilterTitle">
                        <h2><?=_arama_sayfasi_filtre_temizle_yazi?></h2>
                    </div>
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
                endif;
                ?>
            </div>
        </div>
    </aside>
    <?php endif;?>
    <div class="searchProductContent">
        <div class="searchMainContentContainer">
            <div class="searchMainContentTitle">
                <h1><?php echo _arama_sayfasi_sonuc_yazi.' "'.$parsedQuery["q"].'"' ?>, <?=$searchTotalResults ." "._arama_sayfasi_sonuc_adet_yazi?> ( <?=$searchResultTotalPages .' / '. $searchResultCurrentPage?> )</h1>
            </div>
            <div class="searchMainContentContent">
                <div class="searchMainContentContentContainer">
                    <?php
                    if(!empty($searchResultProducts)) {
                        $pb = 0;
                        foreach ($searchResultProducts as $product) {
                            include VIEW . "Product/ProductBox.php";
                        }
                    }
                    else{
                        echo "<h2>"._arama_sayfasi_sonuc_yok_yazi." ";
                        echo $parsedQuery["q"]."</h2>";
                    }
                    ?>
                </div>
            </div>
            <div class="searchMainPaginationContent">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php
                        if($searchResultTotalPages > 1){
                            for($i=1; $i<=$searchResultTotalPages; $i++){
                                $active = ($searchResultCurrentPage == $i) ? "active" : "";
                                ?>
                                <li class="page-item <?php echo $active; ?>"><a class="page-link" href="?<?php echo $query; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
