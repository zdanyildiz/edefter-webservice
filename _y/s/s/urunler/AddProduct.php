<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 */


$formSubmitButtonName = "Ürün Ekle";

include_once MODEL . "Admin/AdminSiteConfig.php";
$siteConfig = new AdminSiteConfig($db,1);
$siteConfig = $siteConfig->getSiteConfig();

################### Genel Fiyat Ayarları ####################

$priceSettings = $siteConfig['priceSettings'][0];
//echo '<pre>';print_r($priceSettings);echo '</pre>';

################### Genel Kredi Kartı Kullanılacak Mı ####################

$generalCreditCardStatus = $priceSettings['kredikarti'];

################### Genel EFT Kullanılacak Mı ####################

$generalEftStatus = $priceSettings['havale'];

################### Kapıda Ödeme Kullanılacak Mı ####################

$generalPayAtTheDoorStatus = $priceSettings['kapidaodeme'];

//site ayar eski fiyat gösterilecek mi?
$showOldPrice = $priceSettings['eskifiyat'];
$installment = $priceSettings['taksit'];
$tax = $priceSettings['kdv'];
/*
$logoInfo = $siteConfig['logoSettings'];
$logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];
*/

include_once MODEL . 'Admin/AdminCargo.php';
$adminCargo = new AdminCargo($db);
$cargos = $adminCargo->getCargos();
$getCargoPrice = $adminCargo->getCargoPrice();
$getCargoPrice = $getCargoPrice[0];
$cargoFixedPrice = $getCargoPrice['fixedPrice'];
$cargoFreeShipping = $getCargoPrice['freeCargo'];
//cargo süresi
$cargoTime = $getCargoPrice['cargoTime'];
//kapıda ödeme ücreti
$cargoCashOnDelivery = $getCargoPrice['cashOnDelivery'];
//cargo ürün adeti
$cargoProductCount = $getCargoPrice['productCount'];

$productID = $_GET['productID'] ?? 0;
if(empty($productID)){
    $productID = 0;
}

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);
$languages = $adminLanguage->getLanguages();

$languageCode = $adminLanguage->getLanguageCode($languageID);

include_once MODEL . 'Admin/AdminProduct.php';
$adminProduct = new AdminProduct($db,$config);

include_once MODEL . 'Admin/AdminProductCategory.php';
$adminProductCategory = new AdminProductCategory($db);

include_once MODEL . 'Admin/AdminSeo.php';
$adminSeo = new AdminSeo($db);

include_once MODEL . 'Admin/AdminVideo.php';
$adminVideo = new AdminVideo($db);

$productGroups = $adminProduct->getProductGroups();

$variantGroups = [];
include_once MODEL . 'Admin/AdminProductVariant.php';
$variantModel = new AdminProductVariant($db);
$variantGroups = $variantModel->getVariantGroups($languageCode);

include_once MODEL . 'Admin/AdminProductQuantityUnit.php';
$adminProductQuantityUnit = new AdminProductQuantityUnit($db);
$productQuantityUnits = $adminProductQuantityUnit->getQuantityUnits();

include_once MODEL . 'Admin/AdminGallery.php';
$adminGallery = new AdminGallery($db);

$categoryHierarchy = [];
$seoKeywords = [];
if($productID > 0){

    $formSubmitButtonName = "Ürün Güncelle";

    $product = $adminProduct->getProduct($productID);
    if(empty($product)){
        header("Location: /_y/s/s/urunler/AddProduct.php");
        exit();
    }

    $productLanguageID = $product['productLanguageID'] ?? 0;
    if($productLanguageID == 0){
        header("Location: /_y/s/s/urunler/AddProduct.php");
        exit();
    }

    $productCategoryID = $product['productCategoryID'];
    $categoryHierarchy = $adminProductCategory->getCategoryHierarchy($productCategoryID);

    $seoKeywords = $adminSeo->getSeoKeywordsByCategoryID($productCategoryID);

    $productID = $product['productID'];
    $productUniqueID = $product['productUniqueID'];
    $productName = $product['productName'];
    $productContent = $product['productContent'];
    if(!empty($productContent)){
        $productContent = html_entity_decode($productContent);
    }
    $productLink = $product['productLink'];
    $productOrder = $product['productOrder'];
    $productActive = $product['productActive'];
    $productUpdateDate = $product['productUpdateDate'];

    $productImages = $product['productImages'];
    $productFiles = $product['productFiles'];
    $productVideos = $product['productVideos'];
    $productGallery = $product['productGallery'];

    $productSeoTitle = $product['productSeoTitle'];
    $productSeoKeywords = $product['productSeoKeywords'];
    $productSeoDescription = $product['productSeoDescription'];
    $productSeoLink = $product['productSeoLink'];

    $productGroupID = $product['productGroupID'];
    $productBrandID = $product['productBrandID'];
    $productSupplierID = $product['productSupplierID'];
    $productSubTitle = $product['productSubTitle'];
    $productShortDesc = $product['productShortDesc'];
    $productDescription = $product['productDescription'];

    $productCargoTime = $product['productCargoTime'];
    $productFixedCargoPrice = $product['productFixedCargoPrice'];
    $productSalePrice = $product['productSalePrice'];

    $productNonDiscountedPrice = $product['productNonDiscountedPrice'];
    $productDealerPrice = $product['productDealerPrice'];
    $productPurchasePrice = $product['productPurchasePrice'];
    $productMarketplacePrice = $product['productMarketplacePrice'];

    $productCurrency = $product['productCurrency'];

    $productInstallment = $product['productInstallment'];
    $productTax = $product['productTax'];
    $productDiscountRate = $product['productDiscountRate'];

    $productStock = $product['productStock'];
    $productStockCode = $product['productStockCode'];
    $productModel = $product['productModel'];

    $productCargo = $product['productCargo'];
    $productDesi = $product['productDesi'];

    $productPriceLastDate = $product['productPriceLastDate'];

    $productDiscountRateShow = $product['productDiscountRateShow'];
    $productShowOldPrice = $product['productShowOldPrice'];
    $productHomePage = $product['productHomePage'];
    $productDiscounted = $product['productDiscounted'];
    $productNew = $product['productNew'];
    $productBulkDiscount = $product['productBulkDiscount'];
    $productSameDayShipping = $product['productDiscountedShipping'];
    $productFreeShipping = $product['productFreeShipping'];
    $productPreOrder = $product['productPreOrder'];
    $productPriceAsk = $product['productPriceAsk'];
    $productDayOpportunity = $product['productDayOpportunity'];

    $productCreditCard = $product['productCreditCard'];
    $productCashOnDelivery = $product['productCashOnDelivery'];
    $productBankTransfer = $product['productBankTransfer'];

    $productSalesQuantity = $product['productSalesQuantity'];

    $productGTIN = $product['productGTIN'];
    $productMPN = $product['productMPN'];
    $productBarcode = $product['productBarcode'];
    $productOEM = $product['productOEM'];


    $productMinimumQuantity = $product['productMinimumQuantity'];
    $productMaximumQuantity = $product['productMaximumQuantity'];
    $productCoefficient = $product['productCoefficient'];
    $productQuantityUnitID = $product['productQuantityUnitID'];
    $productVariantProperties = $product['productVariantProperties'];
    $productProperties = $product['productProperties'];
}

$productLanguageID = $productLanguageID ?? $languageID;

$productCategoryID = $productCategoryID ?? 0;

$productID = $productID ?? 0;
$productUniqueID = $productUniqueID ?? "";
$productName = $productName ?? "";
$productContent = $productContent ?? "";
$productLink = $productLink ?? "";
$productOrder = $productOrder ?? 0;
$productActive = $productActive ?? 1;
$productUpdateDate = $productUpdateDate ?? date('Y-m-d H:i:s');

$productImages = $productImages ?? "";
$productFiles = $productFiles ?? "";

$productVideos = $productVideos ?? "";
$productGallery = $productGallery ?? "";
if(!empty($productGallery)){
    $productGallery = $adminGallery->getGallery($productGallery[0]['galleryID']);
}

$productSeoTitle = $productSeoTitle ?? "";
$productSeoKeywords = $productSeoKeywords ?? "";
$productSeoDescription = $productSeoDescription ?? "";
$productSeoLink = $productSeoLink ?? "";

$productGroupID = $productGroupID ?? 0;
$productBrandID = $productBrandID ?? 1;
$productSupplierID = $productSupplierID ?? 1;
$productSubTitle = $productSubTitle ?? "";
$productShortDesc = $productShortDesc ?? "";
$productDescription = $productDescription ?? "";

$productCargo = $productCargo ?? 0;
$productCargoTime = $productCargoTime ?? $cargoTime;
$productFreeShipping = $productFreeShipping ?? $cargoFreeShipping;
$productFixedCargoPrice = $productFixedCargoPrice ?? $cargoFixedPrice;
$productDesi = $productDesi ?? 1;
$productDesi = str_replace(".0000","",$productDesi);

$productSalePrice = $productSalePrice ?? 0;
$productNonDiscountedPrice = $productNonDiscountedPrice ?? 0;
$productDealerPrice = $productDealerPrice ?? 0;
$productPurchasePrice = $productPurchasePrice ?? 0;
$productMarketplacePrice = $productMarketplacePrice ?? 0;

$productShowOldPrice = $productShowOldPrice ?? $showOldPrice;
$productInstallment = $productInstallment ?? $installment;
$productTax = $productTax ?? $tax;

$productStock = $productStock ?? 0;
$productStockCode = $productStockCode ?? "";
$productModel = $productModel ?? "";

$productDiscountRate = $productDiscountRate ?? 0;

//$productPriceLastDate tanımlı değilsen bugünden 1 yıl sonrası olabilir
$productPriceLastDate = $productPriceLastDate ?? date('Y-m-d H:i:s', strtotime('+1 year'));
//ilk 10 haneyi alalım
$productPriceLastDate = substr($productPriceLastDate, 0, 10);


$productHomePage = $productHomePage ?? 0;
$productDiscounted = $productDiscounted ?? 0;
$productNew = $productNew ?? 0;
$productBulkDiscount = $productBulkDiscount ?? 0;
$productSameDayShipping = $productSameDayShipping ?? 0;

$productPreOrder = $productPreOrder ?? 0;
$productPriceAsk = $productPriceAsk ?? 0;

$productCurrency = $productCurrency ?? 1;

$productDayOpportunity = $productDayOpportunity ?? 0;
$productCreditCard = $productCreditCard ?? ($generalCreditCardStatus ? 1 : 0);
$productCashOnDelivery = $productCashOnDelivery ?? ($generalPayAtTheDoorStatus ? 1 : 0);
$productBankTransfer = $productBankTransfer ?? ($generalEftStatus ? 1 : 0);
$productSalesQuantity = $productSalesQuantity ?? 0;
$productDiscountRateShow = $productDiscountRateShow ?? 0;

$productGTIN = $productGTIN ?? "";
$productMPN = $productMPN ?? "";
$productBarcode = $productBarcode ?? "";
$productOEM = $productOEM ?? "";

$productMinimumQuantity = $productMinimumQuantity ?? 1;
$productMinimumQuantity = str_replace(".0000","",$productMinimumQuantity);
$productMaximumQuantity = $productMaximumQuantity ?? 9999;
$productMaximumQuantity = str_replace(".0000","",$productMaximumQuantity);
$productCoefficient = $productCoefficient ?? 1;
$productCoefficient = str_replace(".0000","",$productCoefficient);

$productQuantityUnitID = $productQuantityUnitID ?? 1;
$productVariantProperties = $productVariantProperties ?? "";

if(!empty($productVariantProperties)){
    foreach ($productVariantProperties as $i => $variant) {
        $generalVariantIDs=[];

        foreach ($variant['variantProperties'] as $j => $property) {
            $variantGroupInfo = $variantModel->getVariantsGroupByName($property['attribute']['name']);

            if($variantGroupInfo['status'] == 'success'){
                $variantGroupID = $variantGroupInfo['data'][0]['variantGroupID'];
            }
            $variantGroupID = $variantGroupID ?? 0;

            $variantInfo = $variantModel->getVariantByName($property['attribute']['value']);

            if($variantInfo['status'] == 'success'){
                $variantAttributeID = $variantInfo['data'][0]['variantID'];
            }

            $variantAttributeID = $variantAttributeID ?? 0;

            $variantAttributeID = $variantGroupID ."-". $variantAttributeID;
            $productVariantProperties[$i]['variantProperties'][$j]['attribute']['id'] = $variantAttributeID;

            //$variantAttributeID'leri array push yapalım sonra bunları küçükten büyüğe sıralayalım
            $generalVariantIDs[] = $variantAttributeID;
        }
        sort($generalVariantIDs);
        //hepsini birleştirip string yapalım
        $generalVariantID = implode('_',$generalVariantIDs);
        $productVariantProperties[$i]['variantID'] = $generalVariantID;

    }
}

$productProperties = $productProperties ?? "";

include_once MODEL . 'Admin/AdminCurrency.php';
$adminCurrency = new AdminCurrency($db);
$currencies = $adminCurrency->getCurrencies();

include_once MODEL . 'Admin/AdminProductQuantityUnit.php';
$adminProductQuantityUnit = new AdminProductQuantityUnit($db);
$quantityUnits = $adminProductQuantityUnit->getQuantityUnits();

include_once MODEL . 'Admin/AdminCategory.php';
$adminProductCategory = new AdminCategory($db);
$categories = $adminProductCategory->getAllProductCategories($languageID);
if(empty($categories)){
    header("Location: /_y/s/s/urunler/AddProductCategory.php?refAction=AddProduct");
    exit();
}

include_once MODEL . 'Admin/AdminSupplier.php';
$adminSupplier = new AdminSupplier($db, $config);
$suppliers = $adminSupplier->getAllSuppliers();
if(empty($suppliers)){
    header("Location: /_y/s/s/tedarikciler/AddSupplier.php?refAction=AddProduct");
    exit();
}

include_once MODEL . 'Admin/AdminBrand.php';
$adminBrand = new AdminBrand($db);
$brands = $adminBrand->getAllBrands();

if(empty($brands)){
    header("Location: /_y/s/s/markalar/AddBrand.php?refAction=AddProduct");
    exit();
}


?><!DOCTYPE html>
<html lang="tr">
<head>
    <title>Ürün Ekle Pozitif ETicaret</title>

    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/multi-select/multi-select.css?1424887857" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/summernote/summernote.min.css">

    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-tagsinput/bootstrap-tagsinput.css?1424887862" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/typeahead/typeahead.css?1424887863" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once(ROOT."_y/s/b/header.php");?>
<div id="base">
    <?php require_once(ROOT."_y/s/b/leftCanvas.php");?>
    <div id="loader" class="hidden">
        <img class="loader-img" src="/_y/assets/img/loading.gif" width="64" height="64" alt="Loading...">
    </div>

    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">ÜRÜN EKLE</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <input type="hidden" name="productUniqID" value="<?=$productUniqueID?>">
                <form class="form form-validation form-validate" method="post" id="addProductForm">
                    <input type="hidden" name="productID" id="productID" value="<?=$productID?>">
                    <div class="card-actionbar">
                        <div class="card-actionbar-row">
                            <button type="button" name="submitAndCopy" id="submitAndCopy" value="1" class="btn btn-primary-bright btn-default"><?=$formSubmitButtonName?> & Kopyala</button>
                            <button type="button" name="submit" id="submit" value="1" class="btn btn-primary btn-default"><?=$formSubmitButtonName?></button>
                        </div>
                    </div>
                    <?php if($variantGroups["status"]=="error"):?>
                    <div id="variantWarning" class="alert alert-callout">
                        <strong>Varyant Uyarısı | Hiç varyant grubu eklenmemiş</strong><a class="btn btn-icon-toggle btn-close" id="closeVariantWarningButton"><i class="md md-close"></i></a>
                        <p>Varyantlı ürünleriniz varsa Renk, Beden... önce <a href="/_y/s/s/varyasyonlar/AddVariantGroup.php" class="btn btn-sm"><strong>Vartyant Grubu ekleme</strong></a> sayfasından yeni grup ekleyin</p>
                    </div>
                    <?php endif;?>
                    <div class="card">
                        <div class="card-head">
                            <ul class="nav nav-tabs" data-toggle="tabs">
                                <li class="active"><a href="#tabCategory">KATEGORİ - MARKA</a></li>
                                <li class=""><a href="#tabContent">İÇERİK</a></li>
                                <li class=""><a href="#tabMedia">MEDYA</a></li>

                                <li class=""><a href="#tabProductVariant">VARYANT</a></li>
                                <li class=""><a href="#tabPriceSettings">FİYAT AYAR</a></li>

                                <li class=""><a href="#tabProductProperties">EK ÖZELLİK</a></li>
                                <li class=""><a href="#tabCargoSettings">KARGO</a></li>
                                <li class=""><a href="#tabShowcase">VİTRİN</a></li>
                                <li class="hidden"><a href="#tabPaymentSettings">ÖDEME</a></li>

                                <li class=""><a href="#tabSeoSettings">SEO</a></li>
                            </ul>

                        </div>
                        <div class="card-body tab-content">
                            <div class="tab-pane active" id="tabCategory">
                                <!-- KATEGORİ - MARKA - GRUP  - TEDARİKÇİ -->
                                <?php require_once(ROOT."_y/s/s/urunler/addProductCategorySupplierBrandGroupTab.php");?>
                            </div>
                            <div class="tab-pane" id="tabContent">
                                <!-- ÜRÜN İÇERİK BAŞLA -->
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductContentTab.php");?>
                            </div>
                            <div class="tab-pane" id="tabMedia">
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductMediaTab.php");?>
                            </div>
                            <div class="tab-pane" id="tabPriceSettings">
                                <!-- ÜRÜN FİYAT BAŞLA -->
                                <!-- Fiyat üst özellikleri -->
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductPriceSettingTab.php");?>
                            </div>
                            <div class="tab-pane" id="tabProductVariant">
                                <!-- ÜRÜN VARYANT BAŞLA -->
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductVariantTab.php");?>
                            </div>
                            <div class="tab-pane" id="tabProductProperties">
                                <!-- ÜRÜN EK ÖZELLİK BAŞLA -->
                                <?php require_once(ROOT."/_y/s/s/urunler/addProductProperties.php");?>
                            </div>
                            <div class="tab-pane" id="tabShowcase">
                                <!-- ÜRÜN DİĞER günün fırsatı,anasayfa vs BAŞLA -->
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductShowcase.php");?>
                            </div>
                            <div class="tab-pane" id="tabPaymentSettings">
                                <!-- ÜRÜN ÖDEME ÖZELLİKLERİ BAŞLA -->
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductPaymentSettings.php");?>
                            </div>
                            <div class="tab-pane" id="tabCargoSettings">
                                <!-- ÜRÜN GENEL ÖZELLİKLERİ (kargo fiyat, süre, hediye, promosyon) BAŞLA -->
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductCargoSettings.php");?>
                            </div>
                            <div class="tab-pane" id="tabSeoSettings">
                                <!-- ÜRÜN SEO BAŞLA -->
                                <?php require_once(ROOT . "_y/s/s/urunler/addProductSeoSettings.php");?>
                            </div>

                        </div>
                    </div>

                    <div id="copyProductWarning" class="card card-bordered style-primary-bright hidden">
                        <div class="card-head">
                            <header>DİKKAT !</header>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-callout">
                                <strong>Ürün Kopyalama Uyarısı</strong>
                                <p>Ürün kopyalama işlemi yapılırken ürün adı, stok, stok kodu ve fiyat bilgilerini kontrol etmeyi unutmayın!</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."_y/s/b/menu.php");?>
    <?php require_once(ROOT."_y/s/b/rightCanvas.php");?>

    <div class="offcanvas">
        <div id="offcanvas-variant" class="offcanvas-pane width-12" style="width:800px;overflow: hidden">
            <div class="offcanvas-head">
                <h4>ÜRÜN SEÇENEKLERİ - RENK - BEDEN - MALZEME - ŞEKİL - ÖLÇÜ vb. gibi seçenekler ekleyebilirsiniz</h4>
                <div class="offcanvas-tools">
                    <a class="btn btn-icon-toggle btn-default-light pull-right closeCanvas" data-dismiss="offcanvas">
                        <i class="md md-close"></i>
                    </a>
                </div>
            </div>

            <div class="offcanvas-body">
                <div id="variantGroupContainer" class="row"></div>
                <div style="display:block;width: 100%;height: 20px;margin-bottom: 30px;"></div>
            </div>
            <div class="row margin-bottom-lg">
                <div class="col-md-12" style="    position: absolute;
    z-index: 2;
    right: 5px;
    bottom: -10px;
    width: 100%;">
                    <button id="createVariant" type="button"
                            class="btn btn-block ink-reaction btn-primary-dark margin-bottom-lg">Seçenekleri Oluştur</button>
                </div>
            </div>
        </div>
    </div>

    <!-- alert uyarıları için modal oluşturalım -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-danger">
                    <header class="modal-title" id="alertModalLabel">Uyarı</header>
                    <div class="tools">
                        <div class="btn-group">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p id="alertMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/summernote/summernote.min.js"></script>

<script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>


<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<style>
    #loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .loader-img {
        display: block;
        margin: 0 auto;
        width: 64px;
        height: auto;
    }

    #imageContainer,#fileContainer{
        min-width: 100%;
        display: flex;
        flex-wrap: wrap;
        align-content: center; justify-content: flex-start;align-items: flex-start; gap: 10px;
    }
    .imageBox,.filebox {
        box-sizing: border-box;
        box-shadow: 0 0 0 1px #ccc;
        padding: 5px;
    }
    .imageBox img, .fileBox img {
        -webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
        -webkit-transition: -webkit-box-shadow 0.15s ease-out;
        -moz-transition: -moz-box-shadow 0.15s ease-out;
        -o-transition: -o-box-shadow 0.15s ease-out;
        transition: box-shadow 0.15s ease-out;
        margin-bottom: 5px;
    }

    .variantContainer{
        width: 100%;
        height: 200px;
        overflow-y: auto;
    }
    #variantContainer .form-group > label, #variantContainer .form-group .control-label,#variantGroupContainer .form-group > label, #variantGroupContainer .form-group .control-label{
        opacity: 1;
    }
    #variantContainer .row{
        background-color: #fff;
        border-bottom: 1px solid dimgray;
        margin-bottom: 10px;
    }
    /*.variantContainer altındaki checked olmuş inputlardan sonra gelen span'ın rengini değiştirelim */
    .variantContainer input:checked + span{
        color: #f00;
    }
</style>

<script type="text/javascript">
    $("#addProductphp").addClass("active");

    let imgRoot = "<?=imgRoot?>";
    let fileRoot = "<?=fileRoot?>";


    let categoryHierarchy = '<?=!empty($categoryHierarchy) ? json_encode($categoryHierarchy) : '[]'?>';
    categoryHierarchy = JSON.parse(categoryHierarchy);
    let categoryHierarchyLength = categoryHierarchy.length;

    <?php $jsonEncodedVariants = !empty($productVariantProperties) ? json_encode($productVariantProperties, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : '[]';?>
    let productVariants = <?=$jsonEncodedVariants?>;

    $("#productContent").summernote({
        tabsize: 2,
        height: 400,
        minHeight: 400

    });

</script>
<script src="/_y/assets/js/panel/addProduct.js?v=<?=rand(2000,99999)?>"></script>
<script>
    $(document).ready(function(){

        const alertModal = $("#alertModal");
        const alertMessage = $("#alertMessage");

        <?php if($variantGroups["status"]=="error"):?>
        document.getElementById("closeVariantWarningButton").addEventListener("click", function() {
            document.getElementById("variantWarning").style.display = "none";
            localStorage.setItem("variantWarningClosed", "true");
        });

        if(localStorage.getItem("variantWarningClosed")=="true"){
            document.getElementById("variantWarning").style.display = "none";
        }
        <?php endif;?>

        <?php if($productID == 0){?>

        $("#showVariantGroup").addClass("disabled");

        var languageID = $("#languageID").val();

        if(languageID == 0){
            $('#languageID').val($('#languageID option[value!="0"]').first().val()).change();
        }
        else{
            $('#languageID').trigger('change');
        }

        <?php }else{?>

        $("#createVariant").removeClass("hidden");

        <?php }?>

        setLanguageWithProduct();

        categoriesMobileShow();

        //submitAndCopy ve submit id'li butonlar tıklanınca form kontrol yapalım
        $(document).on("click","#submitAndCopy, #submit",function() {

            $(".videoResult").html("");
            $("#alertModal .card-head.card-head-sm").removeClass("style-success").addClass("style-danger");

            let buttonID = $(this).attr("id");

            //##### Kategori - Marka - Tedarikçi - Ürün Model #####//
            //categoryContainer içindeki boş olmayan son select seçilmiş olmalı
            $categoryContainer = $("#categoryContainer");
            $categorySelect = $categoryContainer.find("select");
            $categorySelectLength = $categorySelect.length;
            console.log($categorySelectLength);

            //en sonuncuya bakalım seçili öğe var mı
            $categorySelect.each(function(index){
                $selectedOption = $(this).find("option:selected");
                if($selectedOption.length > 0){
                    $selectedOption = $selectedOption.val();
                    if($selectedOption === "0"){
                        //alertModal gösterelim
                        alertMessage.html("Kategori seçimi yapmadınız.");
                        alertModal.modal("show");
                        //kategoritabı aktif yapalım
                        $("a[href='#tabCategory']").click();
                        return false;
                    }
                    $("#productCategoryID").val($selectedOption);
                }
                else{
                    //alertModal gösterelim
                    alertMessage.html("Kategori seçimi yapmadınız.");
                    alertModal.modal("show");
                    //kategoritabı aktif yapalım
                    $("a[href='#tabCategory']").click();
                    return false;
                }
            });

            //ürün model alanı boş olamaz
            $productModel = $("#productModel").val();
            if($productModel === ""){
                //alertModal gösterelim
                alertMessage.html("Ürün model alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabCategory']").click();
                return false;
            }

            //##### Ürün Başlık - Altbaşlık - 2. altbaşlık #####//

            //productName boş olamaz
            $productName = $("#productName").val();
            if($productName === ""){
                //alertModal gösterelim
                alertMessage.html("Ürün adı alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabContent']").click();
                return false;
            }

            //productStockCode[],productStock[],productSalePrice[] boş olamaz. Yalnızca rakam olmalı ve ondalık ayıracı "." olmalı ve ondalık en fazla 2 haneli olmalıdır
            let hasError = false; // Hata bayrağı

            $productStockCode = $("input[name='productStockCode[]']");
            $productStock = $("input[name='productStock[]']");
            $productSalePrice = $("input[name='productSalePrice[]']");

            $productStockCode.each(function(index){
                $productStockCodeValue = $(this).val();
                if($productStockCodeValue == ""){
                    //alertModal gösterelim
                    alertMessage.html("Stok kodu alanı boş olamaz.");
                    alertModal.modal("show");
                    //kategoritabı aktif yapalım
                    $("a[href='#tabProductVariant']").click();
                    hasError = true;
                    return false;
                }
            });

            $productStock.each(function(index){
                $productStockValue = $(this).val();
                //virgül varsa nokta yapalım
                $productStockValue = $productStockValue.replace(",",".");
                //noktalı değeri set edelim
                $(this).val($productStockValue);

                if($productStockValue == ""){
                    //alertModal gösterelim
                    alertMessage.html("Stok miktarı alanı boş olamaz.");
                    alertModal.modal("show");
                    //kategoritabı aktif yapalım
                    $("a[href='#tabProductVariant']").click();
                    hasError = true;
                    return false;
                }
                else if(isNaN($productStockValue)){
                    //alertModal gösterelim
                    alertMessage.html("Stok miktarı sayı olmalıdır.");
                    alertModal.modal("show");
                    //kategoritabı aktif yapalım
                    $("a[href='#tabProductVariant']").click();
                    hasError = true;
                    return false;
                }
            });

            $productSalePrice.each(function(index){
                $productSalePriceValue = $(this).val();
                //virgül varsa nokta yapalım
                $productSalePriceValue = $productSalePriceValue.replace(",",".");
                //noktalı değeri set edelim
                $(this).val($productSalePriceValue);

                if($productSalePriceValue == ""){
                    //alertModal gösterelim
                    alertMessage.html("Satış fiyatı alanı boş olamaz.");
                    alertModal.modal("show");
                    //kategoritabı aktif yapalım
                    $("a[href='#tabProductVariant']").click();
                    hasError = true;
                    return false;
                }
                else if(isNaN($productSalePriceValue)){
                    //alertModal gösterelim
                    alertMessage.html("Satış fiyatı sayı olmalıdır.");
                    alertModal.modal("show");
                    //kategoritabı aktif yapalım
                    $("a[href='#tabProductVariant']").click();
                    hasError = true;
                    return false;
                }
                else{
                    //ondalık kontrol
                    $productSalePriceValue = $productSalePriceValue.toString();
                    $productSalePriceValue = $productSalePriceValue.split(".");
                    if($productSalePriceValue.length > 1){
                        if($productSalePriceValue[1].length > 2){
                            //alertModal gösterelim
                            alertMessage.html("Satış fiyatı en fazla 2 haneli ondalık olabilir.");
                            alertModal.modal("show");
                            //kategoritabı aktif yapalım
                            $("a[href='#tabProductVariant']").click();
                            hasError = true;
                            return false;
                        }
                    }
                }
            });

            //productDiscountPrice[],productDealerPrice[],productPurchasePrice[] bu değerler boş olabilir. Boş değilse sayı olmalı ve ondalık ayıracı "." olmalı ve ondalık en fazla 2 haneli olmalıdır

            $productDealerPrice = $("input[name='productDealerPrice[]']");
            $productPurchasePrice = $("input[name='productPurchasePrice[]']");
            $productDiscountPrice = $("input[name='productDiscountPrice[]']");

            $productDealerPrice.each(function(index){
                $productDealerPriceValue = $(this).val();
                //boş ise 0 yapalım
                if($productDealerPriceValue == ""){
                    $productDealerPriceValue = "0";
                }
                //virgül varsa nokta yapalım
                $productDealerPriceValue = $productDealerPriceValue.replace(",",".");
                //noktalı değeri set edelim
                $(this).val($productDealerPriceValue);

                if($productDealerPriceValue != ""){
                    if(isNaN($productDealerPriceValue)){
                        //alertModal gösterelim
                        alertMessage.html("Bayi fiyatı sayı olmalıdır.");
                        alertModal.modal("show");
                        //kategoritabı aktif yapalım
                        $("a[href='#tabProductVariant']").click();
                        hasError = true;
                        return false;
                    }
                    else{
                        //ondalık kontrol
                        $productDealerPriceValue = $productDealerPriceValue.toString();
                        $productDealerPriceValue = $productDealerPriceValue.split(".");
                        if($productDealerPriceValue.length > 1){
                            if($productDealerPriceValue[1].length > 2){
                                //alertModal gösterelim
                                alertMessage.html("Bayi fiyatı en fazla 2 haneli ondalık olabilir.");
                                alertModal.modal("show");
                                //kategoritabı aktif yapalım
                                $("a[href='#tabProductVariant']").click();
                                hasError = true;
                                return false;
                            }
                        }
                    }
                }
            });

            $productPurchasePrice.each(function(index){
                $productPurchasePriceValue = $(this).val();
                //boş ise 0 yapalım
                if($productPurchasePriceValue == ""){
                    $productPurchasePriceValue = "0";
                }
                //virgül varsa nokta yapalım
                $productPurchasePriceValue = $productPurchasePriceValue.replace(",",".");
                //noktalı değeri set edelim
                $(this).val($productPurchasePriceValue);

                if($productPurchasePriceValue != ""){
                    if(isNaN($productPurchasePriceValue)){
                        //alertModal gösterelim
                        alertMessage.html("Alış fiyatı sayı olmalıdır.");
                        alertModal.modal("show");
                        //kategoritabı aktif yapalım
                        $("a[href='#tabProductVariant']").click();
                        hasError = true;
                        return false;
                    }
                    else{
                        //ondalık kontrol
                        $productPurchasePriceValue = $productPurchasePriceValue.toString();
                        $productPurchasePriceValue = $productPurchasePriceValue.split(".");
                        if($productPurchasePriceValue.length > 1){
                            if($productPurchasePriceValue[1].length > 2){
                                //alertModal gösterelim
                                alertMessage.html("Alış fiyatı en fazla 2 haneli ondalık olabilir.");
                                alertModal.modal("show");
                                //kategoritabı aktif yapalım
                                $("a[href='#tabProductVariant']").click();
                                hasError = true;
                                return false;
                            }
                        }
                    }
                }
            });

            $productDiscountPrice.each(function(index){
                $productDiscountPriceValue = $(this).val();
                //boş ise 0 yapalım
                if($productDiscountPriceValue == ""){
                    $productDiscountPriceValue = "0";
                }
                //virgül varsa nokta yapalım
                $productDiscountPriceValue = $productDiscountPriceValue.replace(",",".");
                //noktalı değeri set edelim
                $(this).val($productDiscountPriceValue);

                if($productDiscountPriceValue != ""){
                    if(isNaN($productDiscountPriceValue)){
                        //alertModal gösterelim
                        alertMessage.html("İndirimsiz satış fiyatı sayı olmalıdır.");
                        alertModal.modal("show");
                        //kategoritabı aktif yapalım
                        $("a[href='#tabProductVariant']").click();
                        hasError = true;
                        return false;
                    }
                    else{
                        //ondalık kontrol
                        $productDiscountPriceValue = $productDiscountPriceValue.toString();
                        $productDiscountPriceValue = $productDiscountPriceValue.split(".");
                        if($productDiscountPriceValue.length > 1){
                            if($productDiscountPriceValue[1].length > 2){
                                //alertModal gösterelim
                                alertMessage.html("İndirimsiz satış fiyatı en fazla 2 haneli ondalık olabilir.");
                                alertModal.modal("show");
                                //kategoritabı aktif yapalım
                                $("a[href='#tabProductVariant']").click();
                                hasError = true;
                                return false;
                            }
                        }
                    }
                }
            });

            if(hasError){
                return false;
            }

            //productInstallment boş olamaz 0 ve 12 arasında tam sayı olmalı
            $productInstallment = $("#productInstallment").val();
            if($productInstallment === ""){
                //alertModal gösterelim
                alertMessage.html("Taksit sayısı alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabPriceSettings']").click();
                return false;
            }
            else if($productInstallment < 0 || $productInstallment > 12){
                //alertModal gösterelim
                alertMessage.html("Taksit sayısı 0 ile 12 arasında olmalıdır.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabPriceSettings']").click();
                return false;
            }
            else{
                //productInstallment 0 ve 12 arasında tam sayı olmalı
                if($productInstallment % 1 != 0){
                    //alertModal gösterelim
                    alertMessage.html("Taksit sayısı tam sayı olmalıdır.");
                    alertModal.modal("show");
                    //kategoritabı aktif yapalım
                    $("a[href='#tabPriceSettings']").click();
                    return false;
                }
            }

            //productTax boş olamaz 0.01 ve 0.99 arasında sayı olmalı
            $productTax = $("#productTax").val();
            if($productTax === ""){
                //alertModal gösterelim
                alertMessage.html("KDV oranı alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabPriceSettings']").click();
                return false;
            }
            else if($productTax < 0.01 || $productTax > 0.99){
                //alertModal gösterelim
                alertMessage.html("KDV oranı 0.01 ile 0.99 arasında olmalıdır.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabPriceSettings']").click();
                return false;
            }

            //productMinimumQuantity boş olamaz,en az 1 olmalı, ondalık sayı olabilir
            $productMinimumQuantity = $("#productMinimumQuantity").val();
            //virgül varsa nokta yapalım
            $productMinimumQuantity = $productMinimumQuantity.replace(",",".");
            //noktalı değeri set edelim

            if($productMinimumQuantity === ""){
                //alertModal gösterelim
                alertMessage.html("Minimum miktar alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabProductVariant']").click();
                return false;
            }
            else if($productMinimumQuantity < 1){
                //alertModal gösterelim
                alertMessage.html("Minimum miktar 1'den küçük olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabProductVariant']").click();
                return false;
            }

            //productMaximumQuantity boş olamaz, 1 sayı girilmeli
            $productMaximumQuantity = $("#productMaximumQuantity").val();
            //virgül varsa nokta yapalım
            $productMaximumQuantity = $productMaximumQuantity.replace(",",".");
            //noktalı değeri set edelim
            $("#productMaximumQuantity").val($productMaximumQuantity);

            if($productMaximumQuantity === ""){
                //alertModal gösterelim
                alertMessage.html("Maksimum miktar alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabProductVariant']").click();
                return false;
            }
            //sayı mı kontrol edelim
            else if(isNaN($productMaximumQuantity)){
                //alertModal gösterelim
                alertMessage.html("Maksimum miktar sayı olmalıdır.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabProductVariant']").click();
                return false;
            }

            //productCoefficient boş olamaz, 1 sayı girilmeli
            $productCoefficient = $("#productCoefficient").val();
            //virgül varsa nokta yapalım
            $productCoefficient = $productCoefficient.replace(",",".");
            //noktalı değeri set edelim
            $("#productCoefficient").val($productCoefficient);

            if($productCoefficient === ""){
                //alertModal gösterelim
                alertMessage.html("Katsayı alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabProductVariant']").click();
                return false;
            }
            //sayı mı kontrol edelim
            else if(isNaN($productCoefficient)){
                //alertModal gösterelim
                alertMessage.html("Katsayı sayı olmalıdır.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabProductVariant']").click();
                return false;
            }

            //productDesi,productCargoTime boş olamaz
            $productDesi = $("#productDesi").val();
            if($productDesi === ""){
                //alertModal gösterelim
                alertMessage.html("Desi alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabCargoSettings']").click();
                return false;
            }

            $productCargoTime = $("#productCargoTime").val();
            if($productCargoTime === ""){
                //alertModal gösterelim
                alertMessage.html("Kargo süresi alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabCargoSettings']").click();
                return false;
            }

            //productUpdateDate boş olamaz
            $productUpdateDate = $("#productUpdateDate").val();
            if($productUpdateDate === ""){
                //alertModal gösterelim
                alertMessage.html("Güncelleme tarihi alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabCategory']").click();
                return false;
            }

            //productPriceLastDate
            $productPriceLastDate = $("#productPriceLastDate").val();
            if($productPriceLastDate === ""){
                //alertModal gösterelim
                alertMessage.html("Fiyat geçerlilik tarihi alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabPriceSettings']").click();
                return false;
            }

            processVariantGroupNames();
            processProductProperties();
            createSeoDescription();

            //productSeoTitle boş olamaz
            $productSeoTitle = $("#productSeoTitle").val();
            if($productSeoTitle === ""){
                //alertModal gösterelim
                alertMessage.html("SEO başlık alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabSeoSettings']").click();
                return false;
            }

            //productSeoKeywords boş olamaz
            $productSeoKeywords = $("#productSeoKeywords").val();
            if($productSeoKeywords === ""){
                //alertModal gösterelim
                alertMessage.html("SEO anahtar kelimeler alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabSeoSettings']").click();
                return false;
            }

            //productSeoDescription boş olamaz
            $productSeoDescription = $("#productSeoDescription").val();
            if($productSeoDescription === ""){
                //alertModal gösterelim
                alertMessage.html("SEO açıklama alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabSeoSettings']").click();
                return false;
            }

            //productSeoLink boş olamaz
            $productSeoLink = $("#productLink").val();
            if($productSeoLink === ""){
                //alertModal gösterelim
                alertMessage.html("SEO link alanı boş olamaz.");
                alertModal.modal("show");
                //kategoritabı aktif yapalım
                $("a[href='#tabSeoSettings']").click();
                return false;
            }

            //kontrollerimizi yaptık, formu gönderelim
            //Ajax ile formu gönderelim

            let summernote = $("#productContent").summernote();
            let productContent = summernote.code();
            $("#productContent").val(productContent);

            var formElement = document.getElementById('addProductForm');
            var formData = new FormData(formElement);

            var action = "addProduct";
            var productID = $("#productID").val();

            if(productID != 0){
                action = "updateProduct";
            }

            formData.append("action", action);


            let productProperties = [];
            let productPropertyInputs = $(".getProductProperties");
            $.each(productPropertyInputs, function(index, propertyInput) {
                let property = $(propertyInput).val().split(":");
                productProperties.push({
                    attribute: {
                        name: property[0],
                        value: property[1]
                    }
                });
            });

            formData.append("productProperties", JSON.stringify(productProperties));

            //name="variantID[]" olan inputları alalım
            let variantIDs = $("input[name='variantID[]']");
            let stockCodes = $("input[name='productStockCode[]']");
            let gtins = $("input[name='productGTIN[]']");
            let mpns = $("input[name='productMPN[]']");
            let barcodes = $("input[name='productBarcode[]']");
            let oems = $("input[name='productOEM[]']");
            let stocks = $("input[name='productStock[]']");
            let salePrices = $("input[name='productSalePrice[]']");
            let discountPrices = $("input[name='productDiscountPrice[]']");
            let dealerPrices = $("input[name='productDealerPrice[]']");
            let purchasePrices = $("input[name='productPurchasePrice[]']");
            let variantGroupNameDivs = $(`.getVariantGroupName`);
            let discountRates = $("#productDiscountRate").val();
            let minQuantities = $("#productMinimumQuantity").val();
            let maxQuantities = $("#productMaximumQuantity").val();
            let coefficients = $("#productCoefficient").val();

            // Varyant verilerini tutmak için bir dizi oluştur
            let variants = [];


            $.each(variantIDs, function(index, variant) {
                let variantProperties = [];
                let variantID = $(variant).val();
                let variantName = $(variantGroupNameDivs[index]).text();
                let stockCode = $(stockCodes[index]).val();
                let gtin = $(gtins[index]).val();
                let mpn = $(mpns[index]).val();
                let barcode = $(barcodes[index]).val();
                let oem = $(oems[index]).val();
                let stock = $(stocks[index]).val();
                let salePrice = $(salePrices[index]).val();
                let discountPrice = $(discountPrices[index]).val();
                let dealerPrice = $(dealerPrices[index]).val();
                let purchasePrice = $(purchasePrices[index]).val();

                let variantImageIDs = [];

                // Varyant özelliklerini alıp bir dizi oluşturalım
                let propertyInputs = $(`input[name='variantProperties[${variantID}]']`);
                $.each(propertyInputs, function(index, propertyInput) {
                    if($(propertyInput).val() === ""){
                        return true;
                    }
                    console.log("özellik input" + $(propertyInput).val());
                    let property = $(propertyInput).val().split("|");
                    //push etmeden aynı isimde aynı değer eklenmiş mi kontrol edelim
                    let isExist = false;
                    $.each(variantProperties, function(index, variantProperty) {
                        if(variantProperty.attribute.name == property[0] && variantProperty.attribute.value == property[1]){
                            isExist = true;
                        }
                    });
                    if(isExist){
                        return true;
                    }
                    variantProperties.push({
                        attribute: {
                            name: property[0],
                            value: property[1]
                        }
                    });
                });

                // Varyant objesini oluşturalım
                let variantData = {
                    variantID: variantID,
                    variantName: variantName,
                    variantStockCode: stockCode,
                    variantGTIN: gtin,
                    variantMPN: mpn,
                    variantBarcode: barcode,
                    variantOEM: oem,
                    variantQuantity: stock,
                    variantSellingPrice: salePrice,
                    variantPriceWithoutDiscount: discountPrice,
                    variantSellerPrice: dealerPrice,
                    variantPurchasePrice: purchasePrice,
                    variantDiscountRate: discountRates,
                    variantMinQuantity: minQuantities,
                    variantMaxQuantity: maxQuantities,
                    variantCoefficient: coefficients,
                    variantProperties: variantProperties,
                    variantImageIDs: variantImageIDs
                };

                // Varyant objesini variants dizisine ekleyelim
                variants.push(variantData);
            });

            // Oluşturduğumuz varyantları forma ekleyelim
            formData.append("productVariants", JSON.stringify(variants));

            for (var pair of formData.entries()) {
                console.log(pair[0]+ ', ' + pair[1]);
            }

            var languageID = $("#languageID").val();

            $.ajax({
                type: "POST",
                url: "/App/Controller/Admin/AdminProductController.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);
                    if(response.status === "success"){

                        productID = response.productID;
                        $("#productID").val(productID);

                        $("#alertModal .card-head.card-head-sm").removeClass("style-danger").addClass("style-success");
                        alertMessage.html(response.message);
                        alertModal.modal("show");
                        //kategoritabı aktif yapalım
                        $("a[href='#tabCategory']").click();

                        if(buttonID === "submitAndCopy"){

                            $("#productID").val(0);

                            $("#submitAndCopy").html("ÜRÜN EKLE & KOPYALA");

                            $("#submit").html("ÜRÜN EKLE");

                            $("#copyProductWarning").removeClass("hidden");

                            //modalı 1,5 saniye sonra kapatalım
                            setTimeout(function(){
                                alertModal.modal("hide");
                            },1500);
                        }
                        else{
                            //1 saniye sonra yönlendirme yapalım
                            setTimeout(function(){
                                alertModal.modal("hide");
                                //window.location.href = "/_y/s/s/urunler/AddProduct.php?languageID="+languageID;
                            },1500);
                        }
                    }
                    else{
                        $("#alertModal .card-head.card-head-sm").removeClass("style-success").addClass("style-danger");

                        alertMessage.html(response.message);
                        alertModal.modal("show");
                    }
                }
            });

        });

        $(document).on("click", "#createSeo", function() {
            // Ürün bilgilerini al
            var productName = $("#productName").val();
            var summernote = $("#productContent").summernote();
            var productContent = summernote.code();
            var languageCode = $("#languageID option:selected").data("languagecode");

            // Son seçili kategori adını al
            var $categorySelect = $("#categoryContainer").find("select").last();
            var selectedCategoryName = $categorySelect.find("option:selected").text().trim();

            if (!selectedCategoryName || selectedCategoryName === "Seçiniz") {
                alertMessage.html("Kategori seçimi yapmadınız.");
                alertModal.modal("show");
                $("a[href='#tabCategory']").click();
                return;
            }

            if (!productName || !productContent) {
                alertMessage.html("Ürün başlık ve açıklama bilgileri boş olamaz.");
                alertModal.modal("show");
                return;
            }

            //alert modal lütfen bekleyiniz yazdıralım
            alertModal.modal("show");
            alertMessage.html("Lütfen bekleyiniz...");

            // SEO verisi oluşturmak için AJAX isteği
            $.ajax({
                url: "/App/Controller/Admin/AdminChatCompletionController.php",
                type: "POST",
                data: {
                    action: "productSeoGenerator",
                    title: productName,
                    description: productContent,
                    category: selectedCategoryName, // Son seçili kategori adı
                    language: languageCode
                },
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);

                    if (response.status === "error") {
                        $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                        alertMessage.html(response.message);
                        alertModal.modal("show");
                    } else {

                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                        var seoData = JSON.parse(response.data);
                        alertModal.modal("hide");
                        // SEO verilerini alanlara yaz
                        $("#productSeoTitle").val(seoData.seoTitle);
                        $("#productSeoDescription").val(seoData.seoDescription);
                        $("#productSeoKeywords").val(seoData.seoKeywords);

                        alertMessage.html("SEO içerikleri başarıyla oluşturuldu.");
                        //alertModal.modal("show");
                    }
                },
                error: function() {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    alertMessage.html("Bir hata oluştu, lütfen tekrar deneyin.");
                    alertModal.modal("show");
                }
            });
        });

        $(document).on("click","#productContentCreateButton",function(){
            var contentDescription = $("#productInf").val();
            var languageCode = $("#languageID option:selected").data("languagecode");
            var action = "productContentGenerator";
            $("#productContentCreateModal").modal("hide");
            $("#productInf").val("");
            alertMessage.html("Ürün içeriği üretimi başlatılıyor, lütfen bekleyiniz...");
            alertModal.modal("show");
            $.ajax({
                url: "/App/Controller/Admin/AdminChatCompletionController.php",
                type: "POST",
                data: {
                    action: action,
                    contentDescription: contentDescription,
                    language: languageCode
                },
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);
                    if(response.status === "error") {
                        $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                        alertMessage.html(response.message);
                        alertModal.modal("show");
                    } else {
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                        //alertMessage.html("Ürün içeriği üretimi başarılı");
                        alertModal.modal("hide");

                        let summernote = $('#productContent').summernote();
                        let editorData = summernote.code();
                        summernote.code(editorData + response.data);
                    }
                },
                error: function() {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    alertMessage.html("Bir hata oluştu, lütfen tekrar deneyin.");
                    alertModal.modal("show");
                }
            });
        });

        $(document).on("keyup", "#galleryName", function () {
            const galleryName = $(this).val();
            if (galleryName.length > 2) {
                $.ajax({
                    type: 'GET',
                    url: "/App/Controller/Admin/AdminGalleryController.php?action=searchGallery&searchText=" + galleryName,
                    dataType: "html",
                    success: function(data)
                    {
                        data = JSON.parse(data);
                        if(data.status === "success") {
                            const galleryResult = data.data;
                            $(".galleryResult").html("");
                            if(galleryResult.length > 0) {
                                galleryResult.forEach((gallery) => {
                                    $galleryID = gallery.galleryID;
                                    $galleryName = gallery.galleryName;

                                    $galleryBox = '<div class="col-lg-6 selectGallery" style="padding: 10px 0">'+
                                        '<label class="radio-inline radio-styled">'+
                                        '<input type="radio" name="pageGalleryID" value="'+$galleryID+'" checked>'+
                                        '<span>'+$galleryName+'</span>'+
                                        '</label>'+
                                        '</div>';
                                    $(".galleryResult").append($galleryBox);
                                });
                            }
                        }
                    },
                    error: function() {
                        console.log("Search gallery error");
                    }
                });
            }
        });

        $(document).on("click","#noGallery",function (){
            $("#galleryResult").html("Galeri Seçilmedi");
            $("#galleryName").val("");
            $(".selectedGallery").html("");
        });

        $(document).on("click",".selectGallery",function (){
            $(".selectedGallery").html("");
            $selected = $(this).html();
            $(this).remove();
            $(".selectedGallery").append($selected);
            $("#galleryName").val("");
            $("#galleryResult").html("");
        });

        $(document).on("keyup", "#videoName", function () {
            let videoName = $(this).val();
            if (videoName.length > 2) {
                $.ajax({
                    type: 'GET',
                    url: "/App/Controller/Admin/AdminVideoController.php?action=searchVideo&searchText=" + videoName,
                    dataType: "html",
                    success: function(data)
                    {
                        console.log(data);
                        data = JSON.parse(data);
                        if(data.status === "success") {
                            $videoResult = data.data;
                            if($videoResult.length > 0) {
                                $(".videoResult").html("");
                                $videoResult.forEach(($video) => {
                                    $videoID = $video.video_id;
                                    $videoName = $video.video_name;
                                    $videoResultHtml = '<div class="col-md-6 selectVideo"><div class="col-md-12 checkbox checkbox-styled"><label><input type="checkbox" name="pageVideoIDS[]" value="'+$videoID+'" checked><span>'+$videoName+'</span></label><div></div>';
                                    $(".videoResult").append($videoResultHtml);
                                });
                            }
                        }
                    },
                    error: function() {
                        console.log("Search video error");
                    }
                });
            }
        });

        $(document).on("click",".selectVideo",function (){
            $selected = $(this).html();
            $(this).remove();
            $(".selectedVideos").append($selected);
            $("#videoName").val("");
            $("#videoResult").html("");
        });

    });
</script>
</body>
</html>
