<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

/**
 * @todo firma logo al, sipariş durum güncelleme yapılacak, sipariş düzenleme ve oluşturma yapılacak, en son iş kargo takip
 * @todo e-ticaret fiyat ayarlarına havale indirim oranları, tek çekim indirim oranları eklenecek
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");

include_once MODEL . "Location.php";
$location = new Location($db);

$countries = $location->getAllCountries();


include_once MODEL . "Admin/AdminCargo.php";
$adminCargo = new AdminCargo($db);
$cargos = $adminCargo->getCargos();

include_once MODEL . "Admin/AdminOrder.php";
$adminOrder = new AdminOrder($db, $config);

$orderStatuses = $adminOrder->getOrderStatuses();

include_once MODEL . "Admin/AdminCart.php";
$adminCart = new AdminCart($db,$config);

include_once MODEL . "Admin/AdminProduct.php";
$adminProduct = new AdminProduct($db,$config);

$currencies = $adminProduct->getCurrencies();
//parabirimkur boş ise 1 yapalım, tekrar $currencies dizisine atalım
foreach ($currencies as $key => $currency){
    if(empty($currency['parabirimkur'])){
        $currencies[$key]['parabirimkur'] = 1;
    }
}

$uniqID = $_GET['uniqid'] ?? "";

$order = (!empty($uniqID)) ? $adminOrder->getOrderByOrderUniqID($uniqID) : [];

$orderID = $order['siparisid'] ?? "";
$orderUniqID = $order['siparisbenzersizid'] ?? "";

$orderMemberID = $order['uyeid'] ?? "";

include_once MODEL . "Admin/AdminMember.php";
$adminMember = new AdminMember($db);
$memberAddress = $adminMember->getAddresses($orderMemberID);

$orderPaymentStatus = $order['siparisodemedurum'] ?? 0;
$orderStatus = $order['siparisdurum'] ?? "";
$orderPaymentType = $order['siparisodemeyontemi'] ?? "";

$orderInvoiceTitle = $order['siparisfaturaunvan'] ?? "";
$orderInvoiceTaxOffice = $order['siparisfaturavergidairesi'] ?? "";
$orderInvoiceTaxNumber = $order['siparisfaturavergino'] ?? "";

$orderDeliveryName = $order['siparisteslimatad'] ?? "";
$orderDeliverySurname = $order['siparisteslimatsoyad'] ?? "";
$orderDeliveryEmail = $order['siparisteslimateposta'] ?? "";
$orderDeliveryGSM = $order['siparisteslimatgsm'] ?? "";
$orderDeliveryTC = $order['siparisteslimattcno'] ?? "";

$orderDeliveryCountry = $order['siparisteslimatadresulke'] ?? "";
$orderDeliveryCountry = $location->getCountryNameById($orderDeliveryCountry);

$orderDeliveryCity = $order['siparisteslimatadressehir'] ?? "";
$orderDeliveryCity = $location->getCityNameById($orderDeliveryCity);

$orderDeliveryDistrict = $order['siparisteslimatadresilce'] ?? "";
$orderDeliveryDistrict = $location->getCountyNameById($orderDeliveryDistrict);

$orderDeliveryArea = $order['siparisteslimatadressemt'] ?? "";
$orderDeliveryArea = $location->getAreaNameById($orderDeliveryArea);

$orderDeliveryNeighborhood = $order['siparisteslimatadresmahalle'] ?? "";
$orderDeliveryNeighborhood = $location->getNeighborhoodNameById($orderDeliveryNeighborhood);

$orderDeliveryPostalCode = $order['siparisteslimatadrespostakod'] ?? "";
$orderDeliveryAddress = $order['siparisteslimatadresacik'] ?? "";

$orderInvoiceName = $order['siparisfaturaad'] ?? "";
$orderInvoiceSurname = $order['siparisfaturasoyad'] ?? "";
$orderInvoiceEmail = $order['siparisfaturaeposta'] ?? "";
$orderInvoiceGSM = $order['siparisfaturagsm'] ?? "";

$orderInvoiceCountry = $order['siparisfaturaadresulke'] ?? "";
$orderInvoiceCountry = $location->getCountryNameById($orderInvoiceCountry);

$orderInvoiceCity = $order['siparisfaturaadressehir'] ?? "";
$orderInvoiceCity = $location->getCityNameById($orderInvoiceCity);

$orderInvoiceDistrict = $order['siparisfaturaadresilce'] ?? "";
$orderInvoiceDistrict = $location->getCountyNameById($orderInvoiceDistrict);

$orderInvoiceArea = $order['siparisfaturaadressemt'] ?? "";
$orderInvoiceArea = $location->getAreaNameById($orderInvoiceArea);

$orderInvoiceNeighborhood  = $order['siparisfaturaadresmahalle'] ?? "";
$orderInvoiceNeighborhood = $location->getNeighborhoodNameById($orderInvoiceNeighborhood);

$orderInvoicePostalCode = $order['siparisfaturaadrespostakod'] ?? "";
$orderInvoiceAddress = $order['siparisfaturaadresacik'] ?? "";

$orderDate = $order['siparistariholustur'] ?? "";
$orderUpdateDate = $order['siparistarihguncelle'] ?? "";
$orderCurrency = $order['siparisodemeparabirim'] ?? "";
$orderInstallment = $order['siparisodemetaksit'] ?? "";

$orderProductIDs = $order['siparisurunidler'] ?? '';
$orderProductIDs = explode(",",$orderProductIDs);
$orderProductNames = $order['siparisurunadlar'] ?? '';
$orderProductNames = explode("||",$orderProductNames);
$orderProductStockCodes = $order['siparisurunstokkodlar'] ?? '';
$orderProductStockCodes = explode("||",$orderProductStockCodes);
$orderProductCategories = $order['siparisurunkategoriler'] ?? '';
$orderProductCategories = explode("||",$orderProductCategories);
$orderProductPrices = $order['siparisurunfiyatlar'] ?? '';
$orderProductPrices = explode("||",$orderProductPrices);
$orderProductQuantities = $order['siparisurunadetler'] ?? '';
$orderProductQuantities = explode("||",$orderProductQuantities);

//contries["CountryName"] ile orderDeliveryCountry'i karşılaştırıp countryID alalım ona göre selectler için şehir ve ilçe vb getirelim
foreach ($countries as $country){
    if($country['CountryName'] == $orderDeliveryCountry){
        $orderDeliveryCountryID = $country['CountryID'];
        break;
    }
}

$cities = isset($orderDeliveryCountryID) ? $location->getCity($orderDeliveryCountryID) : [];
//cityID yakalayalım
foreach ($cities as $city){
    if($city['CityName'] == $orderDeliveryCity){
        $orderDeliveryCityID = $city['CityID'];
        break;
    }
}

$counties = isset($orderDeliveryCityID) ? $location->getCounty($orderDeliveryCityID) : [];
//countyID yakalayalım
foreach ($counties as $county){
    if($county['CountyName'] == $orderDeliveryDistrict){
        $orderDeliveryCountyID = $county['CountyID'];
        break;
    }
}

$areas = isset($orderDeliveryCountyID) ? $location->getArea($orderDeliveryCountyID) : [];
//areaID yakalayalım
foreach ($areas as $area){
    if($area['AreaName'] == $orderDeliveryArea){
        $orderDeliveryAreaID = $area['AreaID'];
        break;
    }
}

$neighborhoods = isset($orderDeliveryAreaID) ? $location->getNeighborhood($orderDeliveryAreaID) : [];

//teslimat adres ülke, şehir, ilçe, semt, mahalle bilgileri farklı ise onlar içinde country, city, county, area, neighborhood bilgilerini çekelim

if($orderDeliveryCountry != $orderInvoiceCountry){
    foreach ($countries as $country){
        if($country['CountryName'] == $orderInvoiceCountry){
            $orderInvoiceCountryID = $country['CountryID'];
            break;
        }
    }
}

if($orderDeliveryCity != $orderInvoiceCity){
    $invoiceCities = isset($orderInvoiceCountryID) ? $location->getCity($orderInvoiceCountryID) : [];
    foreach ($invoiceCities as $city){
        if($city['CityName'] == $orderInvoiceCity){
            $orderInvoiceCityID = $city['CityID'];
            break;
        }
    }
}
else{
    $invoiceCities = $cities;
}

if($orderDeliveryDistrict != $orderInvoiceDistrict){
    $invoiceCounties = isset($orderInvoiceCityID) ? $location->getCounty($orderInvoiceCityID) : [];
    foreach ($invoiceCounties as $county){
        if($county['CountyName'] == $orderInvoiceDistrict){
            $orderInvoiceCountyID = $county['CountyID'];
            break;
        }
    }
}
else{
    $invoiceCounties = $counties;
}

if($orderDeliveryArea != $orderInvoiceArea){
    $invoiceAreas = isset($orderInvoiceCountyID) ? $location->getArea($orderInvoiceCountyID) : [];
    foreach ($invoiceAreas as $area){
        if($area['AreaName'] == $orderInvoiceArea){
            $orderInvoiceAreaID = $area['AreaID'];
            break;
        }
    }
}
else{
    $invoiceAreas = $areas;
}

if($orderDeliveryNeighborhood != $orderInvoiceNeighborhood){
    $invoiceNeighborhoods = isset($orderInvoiceAreaID) ? $location->getNeighborhood($orderInvoiceAreaID) : [];
}
else{
    $invoiceNeighborhoods = $neighborhoods;
}


$cargoID = $order['kargoid'] ?? "";
$cargoPrice = $order['sipariskargofiyat'] ?? "";
$cargoDate = $order['sipariskargotarih'] ?? "";
$cargoSerialNumber = $order['sipariskargoserino'] ?? "";
$cargoStatus = $order['sipariskargodurum'] ?? "";
$cargoTracking = $order['sipariskargotakip'] ?? "";

$orderDeliveryID = $order['siparisteslimatid'] ?? "";
$orderNoteCustomer = $order['siparisnotalici'] ?? "";
$orderNoteAdmin = $order['siparisnotyonetici'] ?? "";

$orderTotalPrice = $order['siparistoplamtutar'] ?? "";

$orderVATPrice = $order['sipariskdvtutar'] ?? "";
$orderWithoutVATPrice = $order['sipariskdvsiztutar'] ?? "";

$orderCargoPriceIncluded = $order['sipariskargodahilfiyat'] ?? "";

$orderCreditCardSingleChargeDiscountRate = $order['siparistekcekimindirimorani'] ?? 0;
$orderCreditCardSingleChargeDiscountPrice = $order['siparistekcekimindirimlifiyat'] ?? "";
$orderBankTransferDiscountRate = $order['siparishavaleorani'] ?? 0;
$orderBankTransferDiscountPrice = $order['siparishavaleindirimlifiyat'] ?? "";
$orderCargoDiscount = $order['sipariskargoindirim'] ?? 0;
$orderCargoDiscountDescription = $order['sipariskargoindirimaciklama'] ?? "";
$orderPointDiscount = $order['siparispuanindirim'] ?? "";
$orderPointBefore = $order['siparispuanonceki'] ?? "";
$orderPointSpent = $order['siparispuanharcanan'] ?? "";
$orderPointEarned = $order['siparispuankazanilan'] ?? "";
$orderPointLeft = $order['siparispuankalan'] ?? "";

$orderPaymentMethod = $order['siparisodemeyontemi'] ?? "";

$orderIP = $order['siparisip'] ?? "";
$orderReceipt = $order['siparisdekont'] ?? "";
$cargoCode = $order['kargoCode'] ?? "";
$orderCargoBarcode = $order['siparisKargoBarcode'] ?? "";
$tempBarcodeNumber = $order['tempBarcodeNumber'] ?? "";
$orderCargoShipped = $order['siparisKargoSevkiyatYapildi'] ?? "";
$cargoCode = $order['kargokod'] ?? "";
$orderDeleted = $order['siparissil'] ?? "";
$languageCode = $order['languageCode'] ?? "tr";

include_once MODEL . "Admin/AdminLanguage.php";
$language = new AdminLanguage($db,$languageCode);
$languageID = $language->getLanguageID($languageCode);
$languages = $language->getLanguages();


include_once MODEL . "Admin/AdminSiteConfig.php";
$siteConfig = new AdminSiteConfig($db,1);
$siteConfig = $siteConfig->getSiteConfig();

################### Genel Fiyat Ayarları ####################

$priceSettings = $siteConfig['priceSettings'][0];

//havale indirim oranı
$bankTransferDiscountRate = $priceSettings['havale_indirim_orani'];

//kredi kartı tek çekim indirim oranı [tekcekim_indirim_orani]
$creditCardSingleChargeDiscountRate = $priceSettings['tekcekim_indirim_orani'];

################### Genel Kredi Kartı Kullanılacak Mı ####################

$generalCreditCardStatus = $priceSettings['kredikarti'];


################### Kredi Kartı Aracı Firma Bilgileri ####################
$paymentMethods = [];
$creditCardStatus = false;
$bankSettings = $siteConfig['bankSettings'];
if($generalCreditCardStatus){

    $paymentMethods[] = ["kk"=>"Kredi Kartı"];
    if(!empty($bankSettings)){

        $creditCardBankName = $bankSettings[0]['ayarbankaad'];
        $creditCardMerchantID = $bankSettings[0]['magazaid'];
        $creditCardMerchantKey = $bankSettings[0]['magazaparola'];
        $creditCardMerchantSalt = $bankSettings[0]['magazaanahtar'];

        $creditCardStatus = true;
    }
}


################### Genel EFT Kullanılacak Mı ####################

$generalEftStatus = $priceSettings['havale'];

################### EFT Banka Bilgileri ####################

$eftInfo = $siteConfig['eftInfo'];
$eftStatus = false;
if($generalEftStatus){
    if(!empty($eftInfo)){

        $paymentMethods[] = ["bh"=>"Banka Havalesi"];
        /*
        $eftBankName = $eftInfo[0]['bankaad'];
        $eftAccountName = $eftInfo[0]['hesapadi'];
        $eftAccountBranch = $eftInfo[0]['hesapsube'];
        $eftAccountNumber = $eftInfo[0]['hesapno'];
        $eftIBAN = $eftInfo[0]['ibanno'];
        */

        $eftStatus = true;

    }
}

################### Kapıda Ödeme Kullanılacak Mı ####################

$generalPayAtTheDoorStatus = $priceSettings['kapidaodeme'];

if($generalPayAtTheDoorStatus){
    $paymentMethods[] = ["ko"=>"Kapıda Ödeme"];
}

$logoInfo = $siteConfig['logoSettings'];
$logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Pozitif Panel - Sipariş Düzenle</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet"
          href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once(ROOT . "/_y/s/b/header.php"); ?>
<div id="base">
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li><a href="#">Siparişler</a></li>
                    <li class="active"><small class="text-xs">Sipariş Düzenle</small></li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-head style-primary">
                                <header>Sipariş</header>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" style="background-color: whitesmoke">
                                <div class="card-head">
                                    <div class="tools">
                                        <!-- Sipariş için dil seçeneği getirelim -->
                                        <select class="form-control" id="languageID" name="languageID">
                                            <option value="">Dil Seçiniz</option>
                                            <?php foreach ($languages as $lang){
                                                $selected = ($languageCode == $helper->toLowerCase($lang['languageCode'])) ? "selected" : "";
                                                echo '<option value="'.$lang['languageID'].'" '.$selected.'>'.$lang['languageName'].'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                    <header>Sipariş Düzenle <?=$orderUniqID?></header>
                                </div>
                                <div class="card-body">
                                    <form id="orderForm" class="form" method="post">
                                        <input type="hidden" name="orderID" value="<?php echo $orderID; ?>">
                                        <input type="hidden" name="orderUniqID" id="orderUniqID" value="<?php echo $orderUniqID; ?>">
                                        <input type="hidden" name="orderMemberID" id="memberID" value="<?php echo $orderMemberID; ?>">
                                        <!-- fatura bilgileri -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-head card-head-sm">
                                                        <header>Fatura Bilgileri</header>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="orderInvoiceTitle"
                                                                       name="orderInvoiceTitle" value="<?php echo $orderInvoiceTitle; ?>">
                                                                <label for="orderInvoiceTitle">Fatura Ünvanı</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="orderInvoiceTaxOffice"
                                                                       name="orderInvoiceTaxOffice" value="<?php echo $orderInvoiceTaxOffice; ?>">
                                                                <label for="orderInvoiceTaxOffice">Vergi Dairesi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="orderInvoiceTaxNumber"
                                                                       name="orderInvoiceTaxNumber" value="<?php echo $orderInvoiceTaxNumber; ?>">
                                                                <label for="orderInvoiceTaxNumber">Vergi Numarası</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="firmSearchResult" class="hidden"></div>
                                            </div>
                                        </div>

                                        <!--  ürün bilgileri -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-head card-head-sm">
                                                        <header>Ürün Bilgileri</header>
                                                    </div>
                                                    <div class="row">
                                                        <!-- ürün arama için input ekleyelim -->
                                                        <div class="col-md-12 card-body" style="padding-bottom: 0">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="productSearch"
                                                                       name="productSearch" placeholder="Ürün Ara">
                                                                <label for="productSearch">Ürün adı, stokkodu</label>
                                                            </div>
                                                            <!-- ürün arama sonucu -->
                                                            <div class="col-md-12" id="productSearchResult">
                                                                <div class="card">
                                                                    <div class="card-head card-head-sm">
                                                                        <header>Ürün Arama Sonuçları</header>
                                                                        <!-- arama sonucu kapatma butonu ekleyelim -->
                                                                        <div class="tools">
                                                                            <a href="javascript:void(0);" class="btn btn-danger" id="closeProductSearchResult">
                                                                                <i class="fa fa-remove"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="tile">
                                                                                    <div class="tile-content">
                                                                                        <div class="tile-icon">
                                                                                            <img src="<?php echo $logo; ?>" width="100" alt="Ürün Resmi" class="img-responsive" style="height: auto">
                                                                                        </div>
                                                                                        <div class="tile-text">
                                                                                            <h4>Ürün Adı</h4>
                                                                                            <small>Ürün Açıklama</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body" id="orderProductList">
                                                    <?php
                                                    $productTotalTax = 0;
                                                    if(!empty($orderProductIDs[0])){

                                                        for ($i = 0; $i < count($orderProductIDs); $i++){
                                                            $orderProductID = $orderProductIDs[$i];
                                                            $orderProductName = $orderProductNames[$i];
                                                            $orderProductName = str_replace(".0000", "", $orderProductName);
                                                            $orderProductStockCode = $orderProductStockCodes[$i] ?? "";
                                                            $orderProductCategory = $orderProductCategories[$i];
                                                            $orderProductPrice = $orderProductPrices[$i];
                                                            $orderProductQuantity = $orderProductQuantities[$i];
                                                            $orderProductQuantity = str_replace(".0000", "", $orderProductQuantity);

                                                            $productImages = "";
                                                            $productTaxRate = 0;
                                                            $productVariant = "";
                                                            $productMinimumQuantity = 1;
                                                            $productMaximumQuantity = 9999;
                                                            $productCoefficient = 1;
                                                            $productQuantityUnitName = "Adet";
                                                            $productShippingCost = 0;
                                                            $productCurrencyID = 1;

                                                            $orderBasket = $adminCart->getCartByOrderUniqID($orderUniqID,$orderProductStockCode);

                                                            if(!empty($orderBasket)){
                                                                //echo '<pre>';print_r($orderBasket);echo '</pre>';

                                                                $productMinimumQuantity = $orderBasket['cartProducts'][0]['productMinimumQuantity'] ?? $productMinimumQuantity;
                                                                $productMaximumQuantity = $orderBasket['cartProducts'][0]['productMaximumQuantity'] ?? $productMaximumQuantity;
                                                                $productCoefficient = $orderBasket['cartProducts'][0]['productCoefficient'] ?? $productCoefficient;
                                                                $productCoefficient = str_replace(".0000", "", $productCoefficient);
                                                                $productQuantityUnitName = $orderBasket['cartProducts'][0]['productQuantityUnitName'] ?? $productQuantityUnitName;

                                                                $productShippingCost = $orderBasket['cartProducts'][0]['productShippingCost'] ?? 0;

                                                                $productTaxRate = $orderBasket['cartProducts'][0]['productTax'] ?? $productTaxRate;

                                                                $productVariant = $orderBasket['cartProducts'][0]['productSelectedVariant'] ?? $productVariant;
                                                                $productImages = $orderBasket['cartProducts'][0]['productImage'] ?? "";

                                                                //ürün para birimi
                                                                $productCurrencyID = $orderBasket['cartProducts'][0]['productCurrencyID'] ?? "";

                                                                if(!empty($productVariant)){
                                                                    $productVariant = json_decode($productVariant,true);
                                                                    $productVariant = array_map(function($item){
                                                                        return $item['attribute']['name'] . ": " . $item['attribute']['value'];
                                                                    },$productVariant);
                                                                    $productVariant = implode(", ",$productVariant);
                                                                }
                                                                else{
                                                                    $productVariant = "";
                                                                }
                                                            }

                                                            $productTax = ($orderProductPrice * $productTaxRate);
                                                            $productTotalTax += $productTax;
                                                            //die("$productTaxRate - $orderProductPrice - $productTax - $productTotalTax");

                                                            $productImage = "";
                                                            if(!empty($productImages)){
                                                                $productImage = explode(",",$productImages)[0];
                                                            }

                                                            $productTotalPrice = $orderProductPrice * $orderProductQuantity;
                                                            $productTotalPrice = number_format($productTotalPrice,2,".","");
                                                            ?>
                                                            <div class="row card" data-cartid="<?php echo $orderProductStockCode; ?>">
                                                                <input type="hidden" name="orderProductID[]" value="<?php echo $orderProductID; ?>">
                                                                <div class="tile col-md-12">
                                                                    <div class="tile-content">
                                                                        <div class="tile-icon col-md-1">
                                                                            <img src="<?php echo imgRoot.'?imagePath='.$productImage; ?>&width=100&height=100" alt="<?=$orderProductName?>" class="img-responsive">
                                                                        </div>
                                                                        <div class="tile-text col-md-9">
                                                                            <h4><?php echo $orderProductName; ?></h4>
                                                                            <small><?php echo $productVariant; ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tools">
                                                                        <div class="btn-group" style="float: right">
                                                                            <a href="javascript:void(0);" class="btn btn-danger" id="removeProduct" data-id="<?php echo $orderProductStockCode; ?>">
                                                                                <i class="fa fa-remove"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" id="orderProductStockCode"
                                                                               name="orderProductStockCode[]" value="<?php echo $orderProductStockCode; ?>" readonly>
                                                                        <label for="orderProductStockCode">Stok Kodu</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" id="orderProductCategory"
                                                                               name="orderProductCategory[]" value="<?php echo $orderProductCategory; ?>" readonly>
                                                                        <label for="orderProductCategory">Kategori</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" id="orderProductPrice"
                                                                               name="orderProductPrice[]" value="<?php echo $orderProductPrice; ?>"
                                                                               data-taxrate="<?php echo $productTaxRate; ?>"
                                                                        >
                                                                        <label for="orderProductPrice">Fiyat</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" id="orderProductQuantity"
                                                                               name="orderProductQuantity[]"
                                                                               data-minquantity="<?php echo $productMinimumQuantity; ?>"
                                                                               data-maxquantity="<?php echo $productMaximumQuantity; ?>"
                                                                               data-coefficient="<?php echo $productCoefficient; ?>"
                                                                               data-taxrate="<?php echo $productTaxRate; ?>"
                                                                               data-shippingcost="<?php echo $productShippingCost; ?>"
                                                                               value="<?php echo $orderProductQuantity; ?>">
                                                                        <label for="orderProductQuantity"><?=$productQuantityUnitName?></label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" id="orderProductTotalPrice"
                                                                               name="orderProductTotalPrice" value="<?php echo $productTotalPrice; ?>">
                                                                        <label for="orderProductTotalPrice">Toplam Fiyat</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <select class="form-control" id="orderProductCurrencyID"
                                                                                name="orderProductCurrencyID[]">
                                                                            <option value="">Para Birimi Seçiniz</option>
                                                                            <?php foreach ($currencies as $currency){
                                                                                $exchangerate = $currency['parabirimkur'] ?? 1;
                                                                                $selected = ($productCurrencyID == $currency['parabirimid']) ? "selected" : "";
                                                                                echo '<option value="'.$currency['parabirimid'].'" 
                                                                        data-exchangerate="'.$exchangerate.'" 
                                                                        data-originalcurrencyid="'.$productCurrencyID.'" 
                                                                        data-originalprice="'.$orderProductPrice.'" 
                                                                        '.$selected.'>'.$currency['parabirimad'].'</option>';
                                                                            } ?>
                                                                        </select>
                                                                        <label for="orderProductCurrencyID">Para Birimi</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    }
                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- kargo bilgileri -->
                                        <div class="row cargoContainer">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-head card-head-sm">
                                                        <header>Kargo Bilgileri</header>
                                                        <!-- üyenin kayıtlı adreslerini id ve ad olarak selectBox'a yazdıralım -->
                                                        <div class="tools">
                                                            <!-- $memberAdress foreach yapalım -->
                                                            <select class="form-control" id="memberDeliveryAddressID" name="memberDeliveryAddressID">
                                                                <option value="">Teslimat Adresi Seçiniz</option>
                                                                <?php foreach ($memberAddress as $address){
                                                                    echo '<option value="'.$address['addressID'].'">'.$address['addressTitle'].'</option>';
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- kargo -->
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <!-- cargos select'i yazdıralım, siparişle uyan varsa seçili yapalım -->
                                                                <div class="form-group">
                                                                    <select class="form-control" id="cargoID" name="cargoID">
                                                                        <option value="">Kargo Seçiniz</option>
                                                                        <?php foreach ($cargos as $cargo){
                                                                            $selected = ($cargoID == $cargo['kargoid']) ? "selected" : "";
                                                                            echo '<option value="'.$cargo['kargoid'].'" '.$selected.'>'.$cargo['kargoad'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="cargoID">Kargo</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- teslimat Kişi bilgileri -->
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderDeliveryTC"
                                                                           name="orderDeliveryTC" value="<?php echo $orderDeliveryTC; ?>">
                                                                    <label for="orderDeliveryTC">TC No</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderDeliveryName"
                                                                           name="orderDeliveryName" value="<?php echo $orderDeliveryName; ?>">
                                                                    <label for="orderDeliveryName">Ad</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderDeliverySurname"
                                                                           name="orderDeliverySurname" value="<?php echo $orderDeliverySurname; ?>">
                                                                    <label for="orderDeliverySurname">Soyad</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderDeliveryEmail"
                                                                           name="orderDeliveryEmail" value="<?php echo $orderDeliveryEmail; ?>">
                                                                    <label for="orderDeliveryEmail">E-Posta</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderDeliveryGSM"
                                                                           name="orderDeliveryGSM" value="<?php echo $orderDeliveryGSM; ?>">
                                                                    <label for="orderDeliveryGSM">GSM</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- teslimat Adres bilgileri -->
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderDeliveryCountry"
                                                                            name="orderDeliveryCountry">
                                                                        <option value="">Ülke Seçiniz</option>
                                                                        <?php foreach ($countries as $country){
                                                                            $selected = ($orderDeliveryCountry == $country['CountryName']) ? "selected" : "";
                                                                            echo '<option value="'.$country['CountryID'].'" '.$selected.'>'.$country['CountryName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderDeliveryCountry">Ülke</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderDeliveryCity"
                                                                            name="orderDeliveryCity">
                                                                        <option value="">Şehir Seçiniz</option>
                                                                        <?php foreach ($cities as $city){
                                                                            $selected = ($orderDeliveryCity == $city['CityName']) ? "selected" : "";
                                                                            echo '<option value="'.$city['CityID'].'" '.$selected.'>'.$city['CityName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderDeliveryCity">Şehir</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderDeliveryDistrict"
                                                                            name="orderDeliveryDistrict">
                                                                        <option value="">İlçe Seçiniz</option>
                                                                        <?php foreach ($counties as $county){
                                                                            $selected = ($orderDeliveryDistrict == $county['CountyName']) ? "selected" : "";
                                                                            echo '<option value="'.$county['CountyID'].'" '.$selected.'>'.$county['CountyName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderDeliveryDistrict">İlçe</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderDeliveryArea"
                                                                            name="orderDeliveryArea">
                                                                        <option value="">Semt Seçiniz</option>
                                                                        <?php foreach ($areas as $area){
                                                                            $selected = ($orderDeliveryArea == $area['AreaName']) ? "selected" : "";
                                                                            echo '<option value="'.$area['AreaID'].'" '.$selected.'>'.$area['AreaName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderDeliveryArea">Semt</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderDeliveryNeighborhood"
                                                                            name="orderDeliveryNeighborhood">
                                                                        <option value="">Mahalle Seçiniz</option>
                                                                        <?php foreach ($neighborhoods as $neighborhood){
                                                                            $selected = ($orderDeliveryNeighborhood == $neighborhood['NeighborhoodName']) ? "selected" : "";
                                                                            echo '<option value="'.$neighborhood['NeighborhoodID'].'" '.$selected.'>'.$neighborhood['NeighborhoodName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderDeliveryNeighborhood">Mahalle</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderDeliveryAddress"
                                                                           name="orderDeliveryAddress" value="<?php echo $orderDeliveryAddress; ?>">
                                                                    <label for="orderDeliveryAddress">Cadde/Sokak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fatura teslimat Bilgileri -->
                                        <div class="row invoiceContainer">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-head card-head-sm">
                                                        <header>Fatura Kargo Bilgileri</header>
                                                        <!-- üyenin kayıtlı adreslerini id ve ad olarak selectBox'a yazdıralım -->
                                                        <div class="tools">
                                                            <!-- $memberAdress foreach yapalım -->
                                                            <select class="form-control" id="memberInvoiceAddressID" name="memberInvoiceAddressID">
                                                                <option value="">Fatura Adresi Seçiniz</option>
                                                                <?php foreach ($memberAddress as $address){
                                                                    echo '<option value="'.$address['addressID'].'">'.$address['addressTitle'].'</option>';
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderInvoiceName"
                                                                           name="orderInvoiceName" value="<?php echo $orderInvoiceName; ?>">
                                                                    <label for="orderInvoiceName">Ad</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderInvoiceSurname"
                                                                           name="orderInvoiceSurname" value="<?php echo $orderInvoiceSurname; ?>">
                                                                    <label for="orderInvoiceSurname">Soyad</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderInvoiceEmail"
                                                                           name="orderInvoiceEmail" value="<?php echo $orderInvoiceEmail; ?>">
                                                                    <label for="orderInvoiceEmail">E-Posta</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderInvoiceGSM"
                                                                           name="orderInvoiceGSM" value="<?php echo $orderInvoiceGSM; ?>">
                                                                    <label for="orderInvoiceGSM">GSM</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderInvoiceCountry"
                                                                            name="orderInvoiceCountry">
                                                                        <option value="">Ülke Seçiniz</option>
                                                                        <?php foreach ($countries as $country){
                                                                            $selected = ($orderInvoiceCountry == $country['CountryName']) ? "selected" : "";
                                                                            echo '<option value="'.$country['CountryID'].'" '.$selected.'>'.$country['CountryName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderInvoiceCountry">Ülke</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderInvoiceCity"
                                                                            name="orderInvoiceCity">
                                                                        <option value="">Şehir Seçiniz</option>
                                                                        <?php foreach ($invoiceCities as $city){
                                                                            $selected = ($orderInvoiceCity == $city['CityName']) ? "selected" : "";
                                                                            echo '<option value="'.$city['CityID'].'" '.$selected.'>'.$city['CityName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderInvoiceCity">Şehir</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderInvoiceDistrict"
                                                                            name="orderInvoiceDistrict">
                                                                        <option value="">İlçe Seçiniz</option>
                                                                        <?php foreach ($invoiceCounties as $county){
                                                                            $selected = ($orderInvoiceDistrict == $county['CountyName']) ? "selected" : "";
                                                                            echo '<option value="'.$county['CountyID'].'" '.$selected.'>'.$county['CountyName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderInvoiceDistrict">İlçe</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderInvoiceArea"
                                                                            name="orderInvoiceArea">
                                                                        <option value="">Semt Seçiniz</option>
                                                                        <?php foreach ($invoiceAreas as $area){
                                                                            $selected = ($orderInvoiceArea == $area['AreaName']) ? "selected" : "";
                                                                            echo '<option value="'.$area['AreaID'].'" '.$selected.'>'.$area['AreaName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderInvoiceArea">Semt</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderInvoiceNeighborhood"
                                                                            name="orderInvoiceNeighborhood">
                                                                        <option value="">Mahalle Seçiniz</option>
                                                                        <?php foreach ($invoiceNeighborhoods as $neighborhood){
                                                                            $selected = ($orderInvoiceNeighborhood == $neighborhood['NeighborhoodName']) ? "selected" : "";
                                                                            echo '<option value="'.$neighborhood['NeighborhoodID'].'" '.$selected.'>'.$neighborhood['NeighborhoodName'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderInvoiceNeighborhood">Mahalle</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderInvoiceAddress"
                                                                           name="orderInvoiceAddress" value="<?php echo $orderInvoiceAddress; ?>">
                                                                    <label for="orderInvoiceAddress">Cadde/Sokak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ödeme bilgileri -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-head card-head-sm">
                                                        <header>Ödeme Bilgileri</header>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderTotalPrice"
                                                                           name="orderTotalPrice" value="<?php echo $orderTotalPrice; ?>" readonly>
                                                                    <label for="orderTotalPrice">Toplam Tutar</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderVATPrice"
                                                                           name="orderVATPrice" value="<?php echo  $orderVATPrice; ?>" readonly>
                                                                    <label for="orderVATPrice">KDV Tutarı</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderWithoutVATPrice"
                                                                           name="orderWithoutVATPrice" value="<?php echo $orderWithoutVATPrice; ?>" readonly>
                                                                    <label for="orderWithoutVATPrice">KDV'siz Tutar</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderCargoPriceIncluded"
                                                                           name="orderCargoPriceIncluded" value="<?php echo $orderCargoPriceIncluded; ?>" readonly>
                                                                    <label for="orderCargoPriceIncluded">Kargo Dahil Fiyat</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderCreditCardSingleChargeDiscountRate"
                                                                           name="orderCreditCardSingleChargeDiscountRate" value="<?php echo $orderCreditCardSingleChargeDiscountRate; ?>">
                                                                    <label for="orderCreditCardSingleChargeDiscountRate">Tek Çekim İndirim Oranı</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderCreditCardSingleChargeDiscountPrice"
                                                                           name="orderCreditCardSingleChargeDiscountPrice" value="<?php echo $orderCreditCardSingleChargeDiscountPrice; ?>" readonly>
                                                                    <label for="orderCreditCardSingleChargeDiscountPrice">Tek Çekim İndirim Fiyatı</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderBankTransferDiscountRate"
                                                                           name="orderBankTransferDiscountRate" value="<?php echo $orderBankTransferDiscountRate; ?>">
                                                                    <label for="orderBankTransferDiscountRate">Havale İndirim Oranı</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderBankTransferDiscountPrice"
                                                                           name="orderBankTransferDiscountPrice" value="<?php echo $orderBankTransferDiscountPrice; ?>" readonly>
                                                                    <label for="orderBankTransferDiscountPrice">Havale İndirim Fiyatı</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderCargoDiscount"
                                                                           name="orderCargoDiscount" value="<?php echo $orderCargoDiscount; ?>">
                                                                    <label for="orderCargoDiscount">Kargo İndirim</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderCargoDiscountDescription"
                                                                           name="orderCargoDiscountDescription" value="<?php echo $orderCargoDiscountDescription; ?>">
                                                                    <label for="orderCargoDiscountDescription">Kargo İndirim Açıklama</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderCurrency"
                                                                            name="orderCurrency">
                                                                        <option value="">Para Birimi Seçiniz</option>
                                                                        <?php foreach ($currencies as $currency){
                                                                            $exchangerate = $currency['parabirimkur'] ?? 1;
                                                                            $selected = ($orderCurrency == $currency['parabirimkod']) ? "selected" : "";
                                                                            echo '<option value="'.$currency['parabirimid'].'" data-exchangerate="'.$exchangerate.'" '.$selected.'>'.$currency['parabirimad'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderCurrency">Para Birimi</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderPointBefore"
                                                                           name="orderPointBefore" value="<?php echo $orderPointBefore; ?>">
                                                                    <label for="orderPointBefore">Önceki Puan</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="orderPointAfter"
                                                                           name="orderPointAfter" value="">
                                                                    <label for="orderPointAfter">Sonraki Puan</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        -->
                                                        <!-- ödeme yöntemi -->
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderPaymentMethod"
                                                                            name="orderPaymentMethod">
                                                                        <option value="">Ödeme Yöntemi Seçiniz</option>
                                                                        <?php
                                                                            foreach ($paymentMethods as $paymentMethod){
                                                                                foreach ($paymentMethod as $key => $value){
                                                                                    $selected = ($orderPaymentMethod == $key) ? "selected" : "";
                                                                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                                                                }

                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderPaymentMethod">Ödeme Yöntemi</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderPaymentStatus"
                                                                            name="orderPaymentStatus">
                                                                        <option value="0">Ödeme Durumu Seçiniz</option>
                                                                        <option value="0" <?=$orderPaymentStatus==0 ? "selected" : ""?>>Ödeme Alınmadı</option>
                                                                        <option value="1" <?=$orderPaymentStatus==1 ? "selected" : ""?>>Ödeme Alındı</option>
                                                                    </select>
                                                                    <label for="orderPaymentStatus">Ödeme Durumu</label>
                                                                </div>
                                                            </div>
                                                            <!-- sipariş durumlarını getirelim siparisdurumid,siparisdurumbaslik -->
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select class="form-control" id="orderStatusID"
                                                                            name="orderStatusID">
                                                                        <option value="">Sipariş Durumu Seçiniz</option>
                                                                        <?php foreach ($orderStatuses as $status){
                                                                            $selected = ($orderStatus == $status['siparisdurumid']) ? "selected" : "";
                                                                            echo '<option value="'.$status['siparisdurumid'].'" '.$selected.'>'.$status['siparisdurumbaslik'].'</option>';
                                                                        } ?>
                                                                    </select>
                                                                    <label for="orderStatusID">Sipariş Durumu</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- kaydet butonu -->
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <button type="submit" class="btn btn-primary" id="saveOrder" style="float: right">Kaydet</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php require_once(ROOT . "/_y/s/b/menu.php"); ?>

<!-- form uyarılarını göstermek için modal oluşturalım -->
<div class="modal fade" id="formAlertModal" tabindex="-1" role="dialog" aria-labelledby="formAlertModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="formAlertModalLabel">Uyarı</h4>
            </div>
            <div class="modal-body">
                <p id="formAlertText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<style>
    #productSearchResult{
        display: none;
        background-color: antiquewhite;
        box-shadow: 0 0 10px 0 rgba(0,0,0,0.5)
    }
    #closeProductSearchResult{
        z-index: 7;
        position: absolute;
        right: 0;
        top: -60px;
    }
    #firmSearchResult{
        background-color: antiquewhite;
        box-shadow: 0 0 10px 0 rgba(0,0,0,0.5);
        z-index: 7;
        position: absolute;
        width: 100%;
        top:200px
    }
    @media (max-width: 768px) {
        .row.card>.tile.col-md-12>.tools{
            position: absolute;
            right: 0;
            top: 0;
            z-index: 9;
        }
    }

</style>
<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<script>

    $("#navCreateOrder").addClass("active");

   //sayfa tamamen yüklendikten sonra javascript kodlarımızı çalıştıralım
   $(document).ready(function() {

       //php ile çektiğimiz parabirimleriniad, dövüzkur, id vs. bilgilerini json_encode ile js'ye aktaralım
       let currencies = <?php echo json_encode($currencies); ?>;

       //orderInvoiceTitle ve orderInvoiceTaxNumber dinleyelim. Kayıtlı bir müşteri varsa bilgilerini dolduralım
       $(document).on("keyup","#orderInvoiceTitle,#orderInvoiceTaxNumber",function(){

           var searchText = $(this).val();

           if(searchText.length < 2){
               return false;
           }

           $.ajax({
               url: "/App/Controller/Admin/AdminMemberController.php",
               type: "POST",
               data: {action: "memberSearch", searchText: searchText},
               success: function (response) {
                   //console.log(response);
                   var data = JSON.parse(response);
                   if(data.status == "success"){

                       $("#firmSearchResult").html("");
                       var memberList = data.memberList;
                       //arama kapatma butonu ekleyelim
                       var memberListHTML = '<div class="col-md-12" style="margin-bottom:10px">';
                       //ayeni firma butonu yapalım
                       memberListHTML += '<a href="javascript:void(0);" class="btn btn-warning" id="newFirm" style="float:left">';
                       memberListHTML += '<i class="fa fa-plus"></i> ';
                       memberListHTML += ' Yeni Firma Ekle</a>';
                       //arama kapatma butonu yapalım
                       memberListHTML += '<a href="javascript:void(0);" class="btn btn-danger" id="closeFirmSearchResult" style="float:right">';
                       memberListHTML += '<i class="fa fa-remove"></i> ';
                       memberListHTML += '</a>';
                       memberListHTML += '</div>';

                       for(var i = 0; i < memberList.length; i++){
                           var member = memberList[i];

                           var memberID = member.memberID;

                           var memberIdentityNo = member.memberIdentityNo;

                           var memberName = member.memberName;
                           //console.log(memberName);
                           var memberSurname = member.memberSurname;
                           //console.log(memberSurname);
                           var memberEmail = member.memberEmail;
                           //console.log(memberEmail);
                           var memberPhone = member.memberPhone;
                           //console.log(memberPhone);
                           var memberInvoiceName = member.memberInvoiceName;
                           //console.log(memberInvoiceName);
                           var memberInvoiceTaxOffice = member.memberInvoiceTaxOffice;
                           //console.log(memberInvoiceTaxOffice);
                           var memberInvoiceTaxNumber = member.memberInvoiceTaxNumber;
                           //console.log(memberInvoiceTaxNumber);

                           memberListHTML += '<div class="col-md-12 memberContainer" style="background-color: whitesmoke;margin-bottom: 10px">';
                           //bir sırada unvan, vergi no, vergi dairesi, alt sırada ad, soyad, email, telefon
                           //sağ üst köşede seçim butonu olsun, buton üzerinde data-memberInvoiceName,data-memberInvoiceTaxOffice,data-memberInvoiceTaxNumber parametreleri olsun
                           memberListHTML += '<div class="tools">';
                           memberListHTML += '<div class="btn-group" style="float: right">';
                           memberListHTML += '<a href="javascript:void(0);" class="btn btn-primary" id="selectMember" ';
                           memberListHTML += 'data-memberInvoiceName="'+memberInvoiceName+'" ';
                           memberListHTML += 'data-memberInvoiceTaxOffice="'+memberInvoiceTaxOffice+'" ';
                           memberListHTML += 'data-memberInvoiceTaxNumber="'+memberInvoiceTaxNumber+'" ';
                           memberListHTML += 'data-memberIdentityNo="'+memberIdentityNo+'" ';
                           memberListHTML += 'data-memberName="'+memberName+'" ';
                           memberListHTML += 'data-memberSurname="'+memberSurname+'" ';
                           memberListHTML += 'data-memberEmail="'+memberEmail+'" ';
                           memberListHTML += 'data-memberPhone="'+memberPhone+'" ';
                           memberListHTML += 'data-memberid="'+memberID+'">';
                           memberListHTML += '<i class="fa fa-check"></i>';
                           memberListHTML += '</a>';
                           memberListHTML += '</div>';
                           memberListHTML += '</div>';
                           memberListHTML += '<div class="tile">';
                           memberListHTML += '<div class="tile-content">';
                           memberListHTML += '<div class="tile-text">';
                           memberListHTML += '<h4>'+memberInvoiceName+'</h4>';
                           memberListHTML += '<small>'+memberInvoiceTaxNumber+' - '+memberInvoiceTaxOffice+'</small>';
                           memberListHTML += '</div>';
                           memberListHTML += '<div class="tile-text">';
                           memberListHTML += '<h4>'+memberName+' '+memberSurname+'</h4>';
                           memberListHTML += '<small>'+memberEmail+' - '+memberPhone+'</small>';
                           memberListHTML += '</div>';
                           memberListHTML += '</div>';
                           memberListHTML += '</div>';
                           memberListHTML += '</div>';
                           memberListHTML += '</div>';
                           //sonuçları ayıralım
                           memberListHTML += '<hr class="border-black">';

                       }

                       $("#firmSearchResult").html(memberListHTML);
                       $("#firmSearchResult").removeClass("hidden");
                   }
               }
           });
       });

       //#selectMember dinleyelim
       $(document).on("click","#selectMember",function(){
           var memberID = $(this).data("memberid");

           $("#memberID").val(memberID);

           updateMemberAddressSelectBox(memberID);

           //orderInvoice ve orderDelivery tc kimlik ad soyad bilgilerini dolduralım
           var memberIdentityNo = $(this).data("memberidentityno");
           var memberName = $(this).data("membername");
           var memberSurname = $(this).data("membersurname");
           var memberEmail = $(this).data("memberemail");
           var memberPhone = $(this).data("memberphone");

           $("#orderDeliveryTC").val(memberIdentityNo);
           $("#orderDeliveryName").val(memberName);
           $("#orderDeliverySurname").val(memberSurname);
           $("#orderDeliveryEmail").val(memberEmail);
           $("#orderDeliveryGSM").val(memberPhone);

           $("#orderInvoiceName").val(memberName);
           $("#orderInvoiceSurname").val(memberSurname);
           $("#orderInvoiceEmail").val(memberEmail);
           $("#orderInvoiceGSM").val(memberPhone);


           //orderInvoiceName, orderInvoiceTaxOffice, orderInvoiceTaxNumber bilgilerini dolduralım

           var memberInvoiceName = $(this).data("memberinvoicename");
           var memberInvoiceTaxOffice = $(this).data("memberinvoicetaxoffice");
           var memberInvoiceTaxNumber = $(this).data("memberinvoicetaxnumber");

           $("#orderInvoiceTitle").val(memberInvoiceName);
           $("#orderInvoiceTaxOffice").val(memberInvoiceTaxOffice);
           $("#orderInvoiceTaxNumber").val(memberInvoiceTaxNumber);

           $("#firmSearchResult").html("");
           $("#firmSearchResult").addClass("hidden");
       });

       //üye seçimi yapılınca memberDeliveryAddressID ve memberInvoiceAddressID selectBox'ını dolduralım
       function updateMemberAddressSelectBox(memberID){
           $.ajax({
               url: "/App/Controller/Admin/AdminMemberController.php",
               type: "POST",
               data: {action: "memberAddressList", memberID: memberID},
               success: function (response) {
                   console.log(response);
                   var data = JSON.parse(response);
                   if(data.status == "success"){
                       var memberAddressList = data.addressList;
                       var memberAddressSelectBox = '';
                       for(var i = 0; i < memberAddressList.length; i++){
                           var memberAddress = memberAddressList[i];
                           var memberAddressID = memberAddress.addressID;
                           var memberAddressTitle = memberAddress.addressTitle;
                           memberAddressSelectBox += '<option value="'+memberAddressID+'">'+memberAddressTitle+'</option>';
                       }
                       var memberDeliveryAddressSelectBox = '<option value="">Teslimat Adresi Seçiniz</option>' + memberAddressSelectBox;
                       $("#memberDeliveryAddressID").html("");
                       $("#memberDeliveryAddressID").html(memberDeliveryAddressSelectBox);

                       var memberInvoiceAddressSelectBox = '<option value="">Fatura Adresi Seçiniz</option>' + memberAddressSelectBox;
                       $("#memberInvoiceAddressID").html("");
                       $("#memberInvoiceAddressID").html(memberInvoiceAddressSelectBox);

                       //her ikisininde ilk adresi seçili olsun,ilk option adres seçiniz yani ikinciyi alacağız
                       $("#memberDeliveryAddressID option:eq(1)").prop("selected", true);
                       $("#memberDeliveryAddressID option:eq(1)").prop("selected", true).trigger('change');

                       $("#memberInvoiceAddressID option:eq(1)").prop("selected", true);
                       $("#memberInvoiceAddressID option:eq(1)").prop("selected", true).trigger('change');
                   }
               }
           });
       }

       //#closeFirmSearchResult dinleyelim
       $(document).on("click","#closeFirmSearchResult,#newFirm",function(){
           $("#memberID").val("");
           $("#firmSearchResult").html("");
           $("#firmSearchResult").addClass("hidden");
       });

       //havale indirim oranı
       let bankTransferDiscountRate = parseFloat(<?php echo $bankTransferDiscountRate; ?>);

       //tek çekim indirim oranı
       let creditCardSingleChargeDiscountRate = parseFloat(<?php echo $creditCardSingleChargeDiscountRate; ?>);

       //closeProductSearchResult
       $(document).on("click","#closeProductSearchResult",function(){
           //arama inputu temizleyelim
           $("#productSearch").val("");
           //içeriği de temizleyelim
           $("#productSearchResult").html("");
           $("#productSearchResult").hide();
       });

       //productSearch Dinleyelim
       $("#productSearch").on("keyup",function(){

           var search = $(this).val();
           //2 karakterden büyükse ajax ile ürün arayalım

           if(search.length < 2){
               $("#productSearchResult").hide();
               return false;
           }

           var languageID = $("#languageID").val();

           $.ajax({
               url: "/App/Controller/Admin/AdminProductController.php",
               type: "POST",
               data: {action: "productSearch", searchText: search, languageID: languageID},
               success: function (response) {

                   //sonuçlar içinden sayfa adını, sayfa kategorisini, ilk resmi ve variantProperties'yi alalım
                   var searchResult = JSON.parse(response);
                   var searchResultProducts = searchResult.searchResultProducts;

                   var productSearchResult = $("#productSearchResult");
                   productSearchResult.empty();
                   productSearchResult.show();

                   for (var i = 0; i < searchResultProducts.length; i++){
                       var product = searchResultProducts[i][0];
                       var productID = product.sayfaid;
                       var productLink = product.link;
                       var productImage = product.resim_url.split(",")[0];
                       var productName = product.sayfaad;
                       var productCategory = product.kategoriad;
                       var productVariant = product.variantProperties;
                       var productTaxRate = product.urunkdv;

                       //console.log(product);
                       var productMinQuantity = product.urunminimummiktar;
                       productMinQuantity = productMinQuantity.replace(".0000","");
                       var productMaxQuantity = product.urunmaksimummiktar;
                       productMaxQuantity = productMaxQuantity.replace(".0000","");
                       var productCoefficient = product.urunkatsayi;
                       productCoefficient = productCoefficient.replace(".0000","");
                       var productUnitName = product.urunmiktarbirimadi;
                       var productShippinCost = product.urunsabitkargoucreti;

                       var productCurrency = product.parabirimkod;
                       var productCurrencyName = product.parabirimad;
                       //productCurrency ile eşleşen para biriminin kurunu alalım
                       var exchangeRate = 1;
                       var productCurrencyID = 1;
                       for (var j = 0; j < currencies.length; j++){
                           var currency = currencies[j];
                           if(currency.parabirimkod === productCurrency){
                               exchangeRate = currency.parabirimkur;
                               productCurrencyID = currency.parabirimid;
                               break;
                           }
                       }

                       //varyant özelliklerini döndürelim her bir varyan özelliğinin fiyatını ve stok kodunu yazdıralım
                       var variantProperties = JSON.parse(productVariant);
                       var variantPropertiesHTML = "";
                       var searchStockCode = search; // Arama teriminizi buraya girin
                       var matchFound = false;

                       for (var j = 0; j < variantProperties.length; j++){
                           var variantProperty = variantProperties[j];
                           var variantPrice = variantProperty.variantSellingPrice;
                           var variantStockCode = variantProperty.variantStockCode;

                           //variantStockCode ile eşleşen varyantın attribute name ve value değerlerini string olarak alalım

                           var variantAttributes = variantProperty.variantProperties;
                           var variantAttributesHTML = "";
                           for (var k = 0; k < variantAttributes.length; k++){
                               var attribute = variantAttributes[k].attribute;
                               var attributeName = attribute.name;
                               var attributeValue = attribute.value;
                               variantAttributesHTML += attributeName + ": " + attributeValue + ", ";
                           }


                           var currentHTML = '<div class="row card" data-cartid="'+ variantStockCode +'">' +
                               '<input type="hidden" name="orderProductID[]" value="' + productID + '">'+
                               '<div class="tile col-md-12">' +
                               '<div class="tile-content">' +
                               '<div class="tile-icon col-md-1">' +
                               '<img src="<?=imgRoot?>?imagePath=' + productImage + '&width=100&height=100" alt="' + productName + '" class="img-responsive">' +
                               '</div>' +
                               '<div class="tile-text col-md-9">' +
                               '<h4>' + productName + '</h4>' +
                               '<small>' + variantAttributesHTML + '</small>' +
                               '</div>' +
                               '</div>' +
                               '<div class="tools">' +
                               '<div class="btn-group" style="float: right">' +
                               '<a href="#" class="btn btn-danger" id="removeProduct" data-id="' + variantStockCode + '">' +
                               '<i class="fa fa-remove"></i>' +
                               '</a>' +
                               '</div>' +
                               '</div>' +
                               '</div>' +
                               '<div class="col-md-2">' +
                               '<div class="form-group">' +
                               '<input type="text" class="form-control" id="orderProductStockCode" name="orderProductStockCode[]" value="' + variantStockCode + '">' +
                               '<label for="orderProductStockCode">Stok Kodu</label>' +
                               '</div>' +
                               '</div>' +
                               '<div class="col-md-2">' +
                               '<div class="form-group">' +
                               '<input type="text" class="form-control" id="orderProductCategory" name="orderProductCategory[]" value="' + productCategory + '">' +
                               '<label for="orderProductCategory">Kategori</label>' +
                               '</div>' +
                               '</div>' +
                               '<div class="col-md-2">' +
                               '<div class="form-group">' +
                               '<input type="text" class="form-control" id="orderProductPrice" name="orderProductPrice[]" value="' + variantPrice + '">' +
                               '<label for="orderProductPrice">Fiyat</label>' +
                               '</div>' +
                               '</div>' +
                               '<div class="col-md-2">' +
                               '<div class="form-group">' +
                               '<input type="text" class="form-control" id="orderProductQuantity" name="orderProductQuantity[]" data-minquantity="'+ productMinQuantity +'" data-maxquantity="'+ productMaxQuantity +'" data-coefficient="' + productCoefficient + '" data-taxrate="'+productTaxRate+'" data-shippingcost="'+productShippinCost+'"  value="1">' +
                               '<label for="orderProductQuantity">' + productUnitName + '</label>' +
                               '</div>' +
                               '</div>' +
                               '<div class="col-md-2">' +
                               '<div class="form-group">' +
                               '<input type="text" class="form-control" id="orderProductTotalPrice" name="orderProductTotalPrice" value="' + variantPrice + '">' +
                               '<label for="orderProductTotalPrice">Toplam Fiyat</label>' +
                               '</div>' +
                               '</div>' +
                               //para birimi select içinde yazalım
                               '<div class="col-md-2">' +
                               '<div class="form-group">' +
                               '<select class="form-control" id="orderProductCurrencyID" name="orderProductCurrencyID[]">' +
                               '<option value="">Para Birimi Seçiniz</option>';
                           for (var k = 0; k < currencies.length; k++){
                               var currency = currencies[k];
                               var currencyCode = currency.parabirimkod;
                               var currencyName = currency.parabirimad;
                               var selected = (productCurrency === currencyCode) ? "selected" : "";
                               currentHTML += '<option value="'+currency.parabirimid+'" ';
                               currentHTML += 'data-exchangerate="'+currency.parabirimkur+'" ';
                               //ürünün parabirim kodunu ve fiyatını data'ya ekleyelim
                               currentHTML += 'data-originalcurrencyid="'+productCurrencyID+'" ';
                               currentHTML += 'data-originalprice="'+variantPrice+'" ';
                               currentHTML += selected+'>'+currencyName+'</option>';
                           }
                           currentHTML += '</select>' +
                               '<label for="orderProductCurrency">Para Birimi</label>' +
                               '</div>' +
                               '</div>' +
                               '</div>';


                           if (variantStockCode === searchStockCode) {
                               variantPropertiesHTML = currentHTML; // Eşleşen sonucu en öne al
                               matchFound = true;
                               break;
                           } else if (!matchFound) {
                               variantPropertiesHTML += currentHTML; // Eşleşme bulunmazsa sonuçları biriktir
                           }
                       }

                       productSearchResult.empty();
                       if (variantPropertiesHTML !== "") {
                           // arama sonucu kapatma butonu ekleyelim
                           productSearchResult.append('<a href="#" id="closeProductSearchResult" class="btn btn-danger" style="float:right">Aramayı Kapat</a>');
                           productSearchResult.append(variantPropertiesHTML);
                       }

                   }
               }
           });
       });

       //arama sonuçlarından ürün tıklandığında ilgili ürünü #orderProductList'e append edelim
       $(document).on("click","#productSearchResult>.card",function(){
           var product = $(this);
           //product tam istediğimiz formatta geliyor, tüm html özellikleriyle append edebiliriz
           var productData = product.clone();
           $("#orderProductList").append(productData);

           //arama sonuçlarını temizleyip divi gizleyelim
           $("#productSearch").val("");
           $("#productSearchResult").empty();
           $("#productSearchResult").hide();

           updateTotalPrice();
       });

       //removeProduct butonuna tıklandığında ilgili ürünü #orderProductList'ten silelim
       $(document).on("click","#removeProduct",function(e){
           e.preventDefault();
           var productID = $(this).data("id");
           var product = $("#orderProductList>.card[data-cartid='"+productID+"']");
           product.remove();
       });

       //orderProductCurrencyID değiştiğinde doviz kuruna göre fiyatı hesaplayalım
       $(document).on("change","#orderProductCurrencyID",function(){

           var selectedCurrencyID = $(this).val();
           var selectedCurrency = $(this).find("option:selected").data("exchangerate");

           //selectedCurrency, selectedCurrencyID'nin tl kurunu verir. Yani selectedCurrencyID euroysa euro'nun tl karşılığını verir

           //ürünün orijinal parabirimini alalım
           var originalCurrencyID = $(this).find("option:selected").data("originalcurrencyid");
           //console.log(originalCurrencyID);
           //orijinal para biriminin döviz kurunu alalım
           var getOriginalCurrency = currencies.find(currency => currency.parabirimid === originalCurrencyID);
           //console.log(getOriginalCurrency);
           var originalCurrency = getOriginalCurrency.parabirimkur;
           //console.log(originalCurrency);

           //ürünün orijinal fiyatını alalım
           var originalPrice = $(this).find("option:selected").data("originalprice");

           //ürünün orijinal fiyatını orijinal döviz kuruna bölüp seçilen döviz kuruna çarparak yeni fiyatı bulalım
           var newPrice = (originalPrice * originalCurrency) / selectedCurrency;

           //fiyatı 2 haneli yapalım
           newPrice = newPrice.toFixed(2);

           //fiyatı güncelleyelim
           $(this).closest(".card").find("#orderProductPrice").val(newPrice);
           //triger change eventini çağıralım
           $(this).closest(".card").find("#orderProductPrice").trigger("change");

       });

       //orderCurrency dinleyelim ve değiştiğinde tüm ürünlerin para birimini seçilen yapalım ve triger change eventini çağıralım
       $(document).on("change","#orderCurrency",function(){
           var selectedCurrencyID = $(this).val();
           if(selectedCurrencyID === ""){
               return false;
           }

           //tüm ürünlerin para birimini seçilen yapalım
           $("#orderProductList>.card").each(function(){
               $(this).find("#orderProductCurrencyID").val(selectedCurrencyID);
               $(this).find("#orderProductCurrencyID").trigger("change");
           });
       });

       //öncelikle quantity hesaplama fonksiyonumuzu yazalım

       function checkQuantity(quantity, minQuantity, maxQuantity, coefficient){
           //hepsinin sayı olduğunu doğrulayalım, küsüratlı sayılarla işlem yapacağımız için parseFloat kullanıyoruz

           quantity = parseFloat(quantity);
           minQuantity = parseFloat(minQuantity);
           maxQuantity = parseFloat(maxQuantity);
           coefficient = parseFloat(coefficient);

           if(quantity < minQuantity){
               return minQuantity;
           }else if(quantity > maxQuantity){
               return maxQuantity;
           }else{
               var remainder = quantity % coefficient;
               if (remainder !== 0){
                   return quantity + coefficient - remainder; // En yakın katsayıya yuvarla
               }else{
                   return quantity;
               }
           }
       }

       function calculateTotalPrice(price, quantity){
           totalPrice = price * quantity;
           return totalPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
       }

       //siparişteki ürünlerin adetleri değiştiğinde total price'i hesaplayalım

       $(document).on("change","#orderProductQuantity",function(){
           var quantity = $(this).val();
           //console.log(quantity);
           var minQuantity = $(this).data("minquantity");
           //console.log(minQuantity);
           var maxQuantity = $(this).data("maxquantity");
           //console.log(maxQuantity);
           var coefficient = $(this).data("coefficient");
           //console.log(coefficient);
           var price = $(this).closest(".card").find("#orderProductPrice").val();

           var checkedQuantity = checkQuantity(quantity, minQuantity, maxQuantity, coefficient);
           $(this).val(checkedQuantity);

           var totalPrice = calculateTotalPrice(price, checkedQuantity);
           $(this).closest(".card").find("#orderProductTotalPrice").val(totalPrice);

           //siparişteki tüm ürünlerin toplamını almak için update fonksiyonunu çağıralım
           updateTotalPrice();
       });

       //ürün fiyatı değiştirilirse total price'i hesaplayalım

       $(document).on("change","#orderProductPrice",function(){
           var price = $(this).val();
           var quantity = $(this).closest(".card").find("#orderProductQuantity").val();

           var totalPrice = calculateTotalPrice(price, quantity);
           $(this).closest(".card").find("#orderProductTotalPrice").val(totalPrice);

           //siparişteki tüm ürünlerin toplamını almak için update fonksiyonunu çağıralım
           updateTotalPrice();
       });

       //ürün toplam fiyatı değiştirilirse total price'i hesaplayalım

       $(document).on("change","#orderProductTotalPrice",function(){
           var totalPrice = $(this).val();
           var quantity = $(this).closest(".card").find("#orderProductQuantity").val();

           var price = (totalPrice / quantity).toFixed(2);
           $(this).closest(".card").find("#orderProductPrice").val(price);

           //siparişteki tüm ürünlerin toplamını almak için update fonksiyonunu çağıralım
           updateTotalPrice();
       });

       //hem kargo hem de fatura adresleri için ülke, şehir, ilçe, semt ve mahalle seçimlerinde değişiklik olduğunda adresleri güncelleyelim

       $(document).on("change",".cargoContainer select, .invoiceContainer select",function(){

           var thisContainer = $(this).closest(".cargoContainer, .invoiceContainer");
           var containerPrefix = thisContainer.hasClass("cargoContainer") ? "Delivery" : "Invoice";
           var selectNames = [
               "order" + containerPrefix + "Country",
               "order" + containerPrefix + "City",
               "order" + containerPrefix + "District",
               "order" + containerPrefix + "Area",
               "order" + containerPrefix + "Neighborhood"
           ];

           var selectedSelectName = $(this).attr("name");
           var selectedSelectIndex = selectNames.indexOf(selectedSelectName);

           var action = "";

           if (selectedSelectIndex === 0) {
               // Ülke seçildi
               var countryID = $(this).val();
               action = "getCity";
               convertInputSelect(thisContainer, selectNames, countryID);
           } else if (selectedSelectIndex > 0) {
               // Şehir, ilçe, semt veya mahalle seçildi
               action = ["getCounty", "getArea", "getNeighborhood", "getPostalCode"][selectedSelectIndex - 1];
           }

           if (action === "") {
               return;
           }

           var locationID = $(this).val();

           if(selectedSelectIndex != 4){

               var targetSelectSelector = selectNames[selectedSelectIndex + 1];
               var targetSelect = thisContainer.find("select[name='" + targetSelectSelector + "']");
               getLocationData(action, locationID, targetSelect);
           }
           else{
               var target = "order"+ containerPrefix +"Address";
               getPostalCode(locationID, target);
           }

       });

       //memberDeliveryAddressID ve memberInvoiceAddressID selectlerinde değişiklik olduğunda adresleri güncelleyelim
       $(document).on("change","#memberDeliveryAddressID, #memberInvoiceAddressID",function(){
           var thisContainer = $(this).closest(".cargoContainer, .invoiceContainer");
           var containerPrefix = thisContainer.hasClass("cargoContainer") ? "Delivery" : "Invoice";
           var selectNames = [
               "order" + containerPrefix + "Country",
               "order" + containerPrefix + "City",
               "order" + containerPrefix + "District",
               "order" + containerPrefix + "Area",
               "order" + containerPrefix + "Neighborhood"
           ];

           var addressID = $(this).val();

           if(addressID == ""){
               return false;
           }

           var memberID = $("#memberID").val();

           $.ajax({
               url: '/App/Controller/Admin/AdminMemberController.php', // API endpoint URL
               type: 'POST',
               data: {
                   action: "getMemberAddressForOrder",
                   addressID: addressID,
                   memberID: memberID
               },
               success: function (data) {
                   //console.log(data);
                   var data = JSON.parse(data);
                   if (data.status == "success") {

                       //{"status":"success","country":{"id":"212","name":"T\u00fcrkiye"},"city":{"id":"40","name":"\u0130STANBUL"},"district":{"id":"459","name":"Beylikd\u00fcz\u00fc"},"area":{"id":"1166","name":"G\u00fcrp\u0131nar"},"neighborhood":{"id":"35446","name":"Adnan Kahveci Mah"},"address":"PK: 34528","postalCode":"34528"}

                       var country = data.country;
                       var countryID = country.id;
                       var countryName = country.name;

                       var city = data.city;
                       var cityID = city.id;
                       var cityName = city.name;

                       var district = data.district;
                       var districtID = district.id;
                       var districtName = district.name;

                       var area = data.area;
                       var areaID = area.id;
                       var areaName = area.name;

                       var neighborhood = data.neighborhood;
                       var neighborhoodID = neighborhood.id;
                       var neighborhoodName = neighborhood.name;

                       var address = data.address;
                       var postalCode = data.postalCode;

                       //ülke select'ini dolduralım
                       var countrySelect = thisContainer.find("select[name='" + selectNames[0] + "']");

                       //countryID'yi seçili yapalım
                       countrySelect.val(countryID);

                       if(countryID == 212){
                           //şehir select'ini dolduralım
                           var citySelect = thisContainer.find("select[name='" + selectNames[1] + "']");
                           citySelect.empty();
                           option = '<option value="'+ cityID +'">'+ cityName +'</option>';
                           citySelect.append(option);

                           //ilçe select'ini dolduralım
                           var districtSelect = thisContainer.find("select[name='" + selectNames[2] + "']");
                           districtSelect.empty();
                           option = '<option value="'+ districtID +'">'+ districtName +'</option>';
                           districtSelect.append(option);

                           //semt select'ini dolduralım
                           var areaSelect = thisContainer.find("select[name='" + selectNames[3] + "']");
                           areaSelect.empty();
                           option = '<option value="'+ areaID +'">'+ areaName +'</option>';
                           areaSelect.append(option);

                           //mahalle select'ini dolduralım
                           var neighborhoodSelect = thisContainer.find("select[name='" + selectNames[4] + "']");
                           neighborhoodSelect.empty();
                           option = '<option value="'+ neighborhoodID +'">'+ neighborhoodName +'</option>';
                           neighborhoodSelect.append(option);
                       }
                       else{
                           //ülke türkiye değilse tüm selectler input olmuş olacak, inputları dolduralım
                           var cityInput = thisContainer.find("input[name='order" + containerPrefix + "City']");
                           cityInput.val(cityName);

                           var districtInput = thisContainer.find("input[name='order" + containerPrefix + "District']");
                           districtInput.val(districtName);

                           var areaInput = thisContainer.find("input[name='order" + containerPrefix + "Area']");
                           areaInput.val(areaName);

                           var neighborhoodInput = thisContainer.find("input[name='order" + containerPrefix + "Neighborhood']");
                           neighborhoodInput.val(neighborhoodName);
                       }

                       //adres ve posta kodunu dolduralım
                       var addressInput = thisContainer.find("input[name='order" + containerPrefix + "Address']");
                       addressInput.val(address);
                   }
               },
               error: function (jqXHR, textStatus, errorThrown) {
                   // handle error
                   console.error(textStatus, errorThrown);
               }
           });
       });

       $(document).on("change","#orderCreditCardSingleChargeDiscountRate, #orderBankTransferDiscountRate",function(){

           var creditCardSingleChargeDiscountRate = $("#orderCreditCardSingleChargeDiscountRate").val();
           creditCardSingleChargeDiscountRate = creditCardSingleChargeDiscountRate.replace(",",".");
           $("#orderCreditCardSingleChargeDiscountRate").val(creditCardSingleChargeDiscountRate);
           creditCardSingleChargeDiscountRate = parseFloat(creditCardSingleChargeDiscountRate);

           var bankTransferDiscountRate = $("#orderBankTransferDiscountRate").val();
           bankTransferDiscountRate = bankTransferDiscountRate.replace(",",".");
           $("#orderBankTransferDiscountRate").val(bankTransferDiscountRate);
           bankTransferDiscountRate = parseFloat(bankTransferDiscountRate);

           var orderTotalPrice = $("#orderTotalPrice").val();
           orderTotalPrice = orderTotalPrice.replace(",",".");
           $("#orderTotalPrice").val(orderTotalPrice);
           orderTotalPrice = parseFloat(orderTotalPrice);

           var orderCreditCardSingleChargeDiscountPrice = orderTotalPrice * (1 - creditCardSingleChargeDiscountRate);
           var orderBankTransferDiscountPrice = orderTotalPrice * (1 - bankTransferDiscountRate);

           $("#orderCreditCardSingleChargeDiscountPrice").val(orderCreditCardSingleChargeDiscountPrice.toFixed(2));
           $("#orderBankTransferDiscountPrice").val(orderBankTransferDiscountPrice.toFixed(2));
       });

       //#orderCargoDiscount dinleyelim, değişiklik olduğunda kargo dahil toplam fiyatı hesaplayalım
       $(document).on("change","#orderCargoDiscount",function(){
           var orderCargoDiscount = $(this).val();
           orderCargoDiscount = orderCargoDiscount.replace(",",".");
           $(this).val(orderCargoDiscount);
           orderCargoDiscount = parseFloat(orderCargoDiscount);

           var orderTotalPrice = $("#orderTotalPrice").val();
           orderTotalPrice = parseFloat(orderTotalPrice);

           var orderCargoDiscountPrice = orderTotalPrice - orderCargoDiscount;

           $("#orderCargoDiscountPrice").val(orderCargoDiscountPrice.toFixed(2));
       });

       function getPostalCode(locationID, target){
           $.ajax({
               url: '/App/Controller/Admin/AdminLocationController.php', // API endpoint URL
               type: 'POST',
               data: {
                   action: "getPostalCode",
                   id: locationID
               },
               success: function(data) {
                   //console.log(data);
                   var data = JSON.parse(data);
                   if(data.status == "success"){

                       var postalCode = data.postalCode;

                       //Hedef input value içinde "PK: 34528" şablona uyan bir değer varsa bulup bizimki ile değiştirelim yoksa value sonuna değeri ekleyelim

                       var targetInput = $("#"+target);
                       var targetValue = targetInput.val();

                       //value içinde "PK: ile başlayan ifade var mı bulalım"
                       var pattern = /PK: \d{5}/;
                       var match = targetValue.match(pattern);
                       if(match !== null){
                           //bulduğumuz değeri değiştirelim
                           targetValue = targetValue.replace(pattern, "PK: " + postalCode);
                       }
                       else{
                           //bulamadık sonuna ekleyelim
                           targetValue += " PK: " + postalCode;
                       }

                       targetInput.val(targetValue);



                   }
               },
               error: function(jqXHR, textStatus, errorThrown) {
                   // handle error
                   console.error(textStatus, errorThrown);
               }
           });
       }

       function getLocationData(action, locationID, targetSelect) {

           if( action == ""){
               return;
           }

           if(action == "getCity" && locationID != 212)
           {
               return;
           }

           /*console.log("action:" + action);
           console.log("locationID:" + locationID);
           console.log("targetSelect:", targetSelect);*/

           $.ajax({
               url: '/App/Controller/Admin/AdminLocationController.php', // API endpoint URL
               type: 'POST',
               data: {
                   action: action,
                   id: locationID
               },
               success: function(data) {
                   //console.log(data);

                   targetSelect.empty();

                   var data = JSON.parse(data);

                   if(data.status == "success"){
                       var locations = data.location;
                       var option = '<option value="">Seçiniz</option>';
                       targetSelect.append(option);
                       for(var i = 0; i < locations.length; i++){
                           var location = locations[i];
                           var locationID = location.id;
                           var locationName = location.name;

                           option = '<option value="'+locationID+'">'+locationName+'</option>';
                           targetSelect.append(option);
                       }
                   }
               },
               error: function(jqXHR, textStatus, errorThrown) {
                   // handle error
                   console.error(textStatus, errorThrown);
               }
           });
       }

       function convertInputSelect(thisContainer, selectNames, countryID) {
           for(var i = 1; i < selectNames.length; i++){
               var selectName = selectNames[i];
               if(countryID !== "212"){
                   // Türkiye seçilmemişse diğer selectleri input yapalım
                   var select = thisContainer.find("select[name='"+selectName+"']");
                   select.replaceWith('<input type="text" class="form-control" name="'+selectName+'" id="'+selectName+'">');
               }
               else{
                   // Önce select kontrolü yapalım input olmuş ise tekrar select'e çevirelim
                   var select = thisContainer.find("input[name='"+selectName+"']");
                   if(select.length > 0){
                       select.replaceWith('<select class="form-control" name="'+selectName+'" id="'+selectName+'"><option value="">Seçiniz</option></select>');
                   }
               }
           }
       }

       function updateTotalPrice(){
           var totalPrice = 0;
           var totalTax = 0;
           var totalShippingCost = 0;
           $("#orderProductList>.card").each(function(){
               var price = $(this).find("#orderProductPrice").val();
               price = parseFloat(price);

               var quantity = $(this).find("#orderProductQuantity").val();
               quantity = parseFloat(quantity);

               var total = price * quantity;

               totalPrice += total;

               //quantity data-taxrate'i okuyalım
               var taxRate = $(this).find("#orderProductQuantity").data("taxrate");
               taxRate = parseFloat(taxRate);

               totalTax += total * taxRate;

               var shippingCost = $(this).find("#orderProductQuantity").data("shippingcost");
               shippingCost = parseFloat(shippingCost);

               totalShippingCost += shippingCost;
           });

           var orderTotalPriceWithoutVat = totalPrice - totalTax;

           var orderTotalPrice = totalPrice + totalShippingCost;

           var orderTotalPriceWithCargoIncluded = orderTotalPrice + totalShippingCost;
           //console.log("kargolu toplam fiyat" + orderTotalPriceWithCargoIncluded);

           var orderCreditCardSingleChargeDiscountPrice = orderTotalPrice * (1 - creditCardSingleChargeDiscountRate);

           var orderBankTransferDiscountPrice = orderTotalPrice * (1 - bankTransferDiscountRate);

           $("#orderTotalPrice").val(totalPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
           $("#orderVATPrice").val(totalTax.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
           $("#orderWithoutVATPrice").val(orderTotalPriceWithoutVat.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
           $("#orderCargoPriceIncluded").val((orderTotalPriceWithCargoIncluded).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
           $("#orderCreditCardSingleChargeDiscountPrice").val(orderCreditCardSingleChargeDiscountPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
           $("#orderBankTransferDiscountPrice").val(orderBankTransferDiscountPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
       }

       function validateTCKimlik(value) {
           value = value.toString();
           if(value==="11111111111")
           {
               return true;
           }
           else if(value.length!== 11)
           {
               return false;
           }
           var isEleven = /^[0-9]{11}$/.test(value);
           var totalX = 0;
           for (var i = 0; i < 10; i++)
           {
               totalX += Number(value.substr(i, 1));
           }
           var isRuleX = totalX % 10 == value.substr(10,1);
           var totalY1 = 0;
           var totalY2 = 0;
           for (var i = 0; i < 10; i+=2)
           {
               totalY1 += Number(value.substr(i, 1));
           }
           for (var i = 1; i < 10; i+=2)
           {
               totalY2 += Number(value.substr(i, 1));
           }
           var isRuleY = ((totalY1 * 7) - totalY2) % 10 == value.substr(9,0);
           return isEleven && isRuleX && isRuleY;
       }

       function validateVergiNo(value) {
           if (value.length === 10)
           {
               if(value=="2222222222")
               {
                   return true;
               }
               let v = []
               let lastDigit = Number(value.charAt(9))
               for (let i = 0; i < 9; i++) {
                   let tmp = (Number(value.charAt(i)) + (9 - i)) % 10
                   v[i] = (tmp * 2 ** (9 - i)) % 9
                   if (tmp !== 0 && v[i] === 0) v[i] = 9
               }
               let sum = v.reduce((a, b) => a + b, 0) % 10
               return (10 - (sum % 10)) % 10 === lastDigit
           }
           if (value.length === 11){
               return validateTCKimlik(value)
           }
           return false
       }

       function validatePhoneNumber(phoneNumber) {
           // Telefon numarasının 10 haneli olup olmadığını kontrol edin.
           if (phoneNumber.length !== 10) {
               return false;
           }
           // Telefon numarasının +90, 90 veya 0 ile başlamadığını kontrol edin.
           if (phoneNumber.startsWith("+90") || phoneNumber.startsWith("90") || phoneNumber.startsWith("0") || phoneNumber.startsWith("+")) {
               return false;
           }
           // Telefon numarasının sadece sayılardan oluştuğunu kontrol edin.
           if (!/^\d+$/.test(phoneNumber)) {
               return false;
           }
           // Telefon numarası geçerlidir.
           return true;
       }

       function validateEmailAddress(email) {
           // E-posta adresinin '@' karakteri içerip içermediğini kontrol edin.
           if (!email.includes("@")) {
               return false;
           }

           // E-posta adresinin '.' karakteri içerip içermediğini kontrol edin.
           if (!email.includes(".")) {
               return false;
           }

           // E-posta adresinin geçerli bir formatta olup olmadığını kontrol edin.
           const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
           if (!regex.test(email)) {
               return false;
           }

           // E-posta adresi geçerlidir.
           return true;
       }

       //form submit dinleyelim
       $(document).on("submit","#orderForm",function(e) {
           e.preventDefault();

           //faturabilgilerini alalım
           var orderInvoiceTitle = $("#orderInvoiceTitle").val();
           var orderInvoiceTaxOffice = $("#orderInvoiceTaxOffice").val();
           var orderInvoiceTaxNumber = $("#orderInvoiceTaxNumber").val();

           var cargoID = $("#cargoID").val();

           //fatura bilgileri boş olamaz
           if(orderInvoiceTitle == "" || orderInvoiceTaxOffice == "" || orderInvoiceTaxNumber == ""){
               
               $("#formAlertModal").modal("show");
               
               $("#formAlertText").text("Fatura bilgileri alanları boş bırakılamaz.");
               return false;
           }

           //vergi numarası 10 haneliyse vergi doğrulaması 11 haneliyse tc doğrulaması yapalım, ikisi de değilse uyarı verelim
           if(orderInvoiceTaxNumber.length == 10){
               if(!validateVergiNo(orderInvoiceTaxNumber)){
                   
                   $("#formAlertModal").modal("show");
                   
                   $("#formAlertText").html("Geçerli bir Vergi Numarası giriniz.<br>Geçerli bir vergi numarası yok ise 2222222222 giriniz.");
                   return false;
               }
           }
           else if(orderInvoiceTaxNumber.length == 11){
               if(!validateTCKimlik(orderInvoiceTaxNumber)){
                   
                   $("#formAlertModal").modal("show");
                   
                   $("#formAlertText").html("Geçerli bir TC Kimlik Numarası giriniz.<br>Geçerli bir tc yok ise 11111111111 giriniz.");
                   return false;
               }
           }
           else{
               
               $("#formAlertModal").modal("show");
               
               $("#formAlertText").text("Geçerli bir Vergi Numarası veya TC Kimlik Numarası giriniz.");
               return false;
           }

           var orderUniqID = $("#orderUniqID").val();
           console.log(orderUniqID);
           var memberID = $("#memberID").val();


           var orderProductList = [];
           //tüm ürünler aynı parabirimi olmalı,birbirinden farklı parabirimleri varsa uyarı verelim, duralım
           var currencyID = $("#orderProductList>.card:first").find("select[name='orderProductCurrencyID[]']").val();
           var currencyDifferent = false;

           $("#orderProductList>.card").each(function () {

               var orderProduct = {};
               orderProduct.productID = $(this).find("input[name='orderProductID[]']").val();
               orderProduct.stockCode = $(this).find("input[name='orderProductStockCode[]']").val();
               orderProduct.category = $(this).find("input[name='orderProductCategory[]']").val();
               orderProduct.price = $(this).find("input[name='orderProductPrice[]']").val();
               orderProduct.quantity = $(this).find("input[name='orderProductQuantity[]']").val();
               orderProduct.totalPrice = $(this).find("input[name='orderProductTotalPrice']").val();
               orderProduct.currencyID = $(this).find("select[name='orderProductCurrencyID[]']").val();

               //parabirim kontrolü
               if(orderProduct.currencyID != currencyID){
                   currencyDifferent = true;
                   //bir ürünün para birimine focus olalım
                   $(this).find("select[name='orderProductCurrencyID[]']").focus();
                   return false;
               }

               orderProductList.push(orderProduct);
           });

           //ürünler boş olamaz
           if(orderProductList.length == 0){
               
               $("#formAlertModal").modal("show");
               
               $("#formAlertText").text("Siparişte en az bir ürün olmalıdır.");
               return false;
           }

           if(currencyDifferent){
               
               $("#formAlertModal").modal("show");
               
               $("#formAlertText").text("Siparişteki tüm ürünler aynı para birimi olmalıdır.");
               return false;
           }

           var orderDeliveryTC = $("#orderDeliveryTC").val();
           var orderDeliveryName = $("#orderDeliveryName").val();
           var orderDeliverySurname = $("#orderDeliverySurname").val();
           var orderDeliveryPhoneNumber = $("#orderDeliveryGSM").val();
           var orderDeliveryEmailAddress = $("#orderDeliveryEmail").val();

           //tc, ad, soyad, telefon, email boş olamaz
           if(orderDeliveryTC == "" || orderDeliveryName == "" || orderDeliverySurname == "" || orderDeliveryPhoneNumber == "" || orderDeliveryEmailAddress == ""){
               
               $("#formAlertModal").modal("show");
               
               $("#formAlertText").text("Teslimat bilgileri alanları boş bırakılamaz.");
               return false;
           }

           //tc doğrulayalım
           if(!validateTCKimlik(orderDeliveryTC)){
               
               $("#formAlertModal").modal("show");
               

               $("#formAlertText").html("Geçerli bir TC Kimlik Numarası giriniz.<br>Geçerli bir tc yok ise 11111111111 giriniz.");
               return false;
           }


           var orderDeliveryCountry = $("#orderDeliveryCountry").val();
           var orderDeliveryCity = $("#orderDeliveryCity").val();
           var orderDeliveryDistrict = $("#orderDeliveryDistrict").val();
           var orderDeliveryArea = $("#orderDeliveryArea").val();
           var orderDeliveryNeighborhood = $("#orderDeliveryNeighborhood").val();
           var orderDeliveryAddress = $("#orderDeliveryAddress").val();

           //ülke 212 ise şehir, ilçe, semt ve mahalle select'leri boş olamaz
           if(orderDeliveryCountry == 212){
               if(orderDeliveryCity == "" || orderDeliveryDistrict == "" || orderDeliveryArea == "" || orderDeliveryNeighborhood == ""){
                   
                   $("#formAlertModal").modal("show");
                   
                   $("#formAlertText").text("Teslimat bilgileri alanları boş bırakılamaz.");
                   return false;
               }
           }
           else{
               //ülke türkiye değilse adres boş olamaz
               if(orderDeliveryAddress == "" || orderDeliveryCity == ""){
                   
                   $("#formAlertModal").modal("show");
                   
                   $("#formAlertText").text("Teslimat bilgileri [şehir, adres] alanları boş bırakılamaz.");
                   return false;
               }
           }

           var orderInvoiceName = $("#orderInvoiceName").val();
           var orderInvoiceSurname = $("#orderInvoiceSurname").val();
           var orderInvoicePhoneNumber = $("#orderInvoiceGSM").val();
           var orderInvoiceEmailAddress = $("#orderInvoiceEmail").val();

           //ad, soyad, telefon, email boş olamaz
           if(orderInvoiceName == "" || orderInvoiceSurname == "" || orderInvoicePhoneNumber == "" || orderInvoiceEmailAddress == ""){
               
               $("#formAlertModal").modal("show");
               
               $("#formAlertText").text("Fatura bilgileri alanları boş bırakılamaz.");
               return false;
           }

           var orderInvoiceCountry = $("#orderInvoiceCountry").val();
           var orderInvoiceCity = $("#orderInvoiceCity").val();
           var orderInvoiceDistrict = $("#orderInvoiceDistrict").val();
           var orderInvoiceArea = $("#orderInvoiceArea").val();
           var orderInvoiceNeighborhood = $("#orderInvoiceNeighborhood").val();
           var orderInvoiceAddress = $("#orderInvoiceAddress").val();

           //ülke 212 ise şehir, ilçe, semt ve mahalle select'leri boş olamaz
           if(orderInvoiceCountry == 212){
               if(orderInvoiceCity == "" || orderInvoiceDistrict == "" || orderInvoiceArea == "" || orderInvoiceNeighborhood == ""){
                   
                   $("#formAlertModal").modal("show");
                   
                   $("#formAlertText").text("Fatura bilgileri alanları boş bırakılamaz.");
                   return false;
               }
           }
           else{
               //ülke türkiye değilse adres boş olamaz
               if(orderInvoiceAddress == "" || orderInvoiceCity == ""){
                   
                   $("#formAlertModal").modal("show");
                   
                   $("#formAlertText").text("Fatura bilgileri [şehir, adres] alanları boş bırakılamaz.");
                   return false;
               }
           }

           var orderCurrency = $("#orderCurrency").val();
           orderCurrency = orderCurrency.replace(",","");
           var orderTotalPrice = $("#orderTotalPrice").val();
              orderTotalPrice = orderTotalPrice.replace(",","");
           var orderVATPrice = $("#orderVATPrice").val();
              orderVATPrice = orderVATPrice.replace(",","");
           var orderWithoutVATPrice = $("#orderWithoutVATPrice").val();
                orderWithoutVATPrice = orderWithoutVATPrice.replace(",","");
           var orderCargoPriceIncluded = $("#orderCargoPriceIncluded").val();
                orderCargoPriceIncluded = orderCargoPriceIncluded.replace(",","");
           var orderCreditCardSingleChargeDiscountRate = $("#orderCreditCardSingleChargeDiscountRate").val();
                orderCreditCardSingleChargeDiscountRate = orderCreditCardSingleChargeDiscountRate.replace(",","");
           var orderCreditCardSingleChargeDiscountPrice = $("#orderCreditCardSingleChargeDiscountPrice").val();
                orderCreditCardSingleChargeDiscountPrice = orderCreditCardSingleChargeDiscountPrice.replace(",","");
           var orderBankTransferDiscountRate = $("#orderBankTransferDiscountRate").val();
                orderBankTransferDiscountRate = orderBankTransferDiscountRate.replace(",","");
           var orderBankTransferDiscountPrice = $("#orderBankTransferDiscountPrice").val();
                orderBankTransferDiscountPrice = orderBankTransferDiscountPrice.replace(",","");
           var orderCargoDiscount = $("#orderCargoDiscount").val();
                orderCargoDiscount = orderCargoDiscount.replace(",","");

           var orderCargoDiscountDescription = $("#orderCargoDiscountDescription").val();

           var orderPaymentMethod = $("#orderPaymentMethod").val();
           if (orderPaymentMethod === "" || orderPaymentMethod === "undefined") {
               $("#formAlertModal").modal("show");
               $("#formAlertText").text("Ödeme yöntemi seçmelisiniz.");
               return false;
           }

           var orderPaymentStatus = $("#orderPaymentStatus").val();
           var orderStatusID = $("#orderStatusID").val();
           if (isNaN(orderStatusID) || orderStatusID === "") {
               $("#formAlertModal").modal("show");
               $("#formAlertText").text("Sipariş durumu seçmelisiniz.");
               return false;
           }

           var action = "addOrder";
           if(orderUniqID != ""){
               action = "updateOrder";
           }
           //formu gönderelim
           $.ajax({
               url: '/App/Controller/Admin/AdminOrderController.php', // API endpoint URL
               type: 'POST',
               data: {
                   action: action,
                   orderUniqID: orderUniqID,
                   memberID: memberID,
                   cargoID: cargoID,
                   orderProductList: orderProductList,
                   orderDeliveryTC: orderDeliveryTC,
                   orderDeliveryName: orderDeliveryName,
                   orderDeliverySurname: orderDeliverySurname,
                   orderDeliveryPhoneNumber: orderDeliveryPhoneNumber,
                   orderDeliveryEmailAddress: orderDeliveryEmailAddress,
                   orderDeliveryCountry: orderDeliveryCountry,
                   orderDeliveryCity: orderDeliveryCity,
                   orderDeliveryDistrict: orderDeliveryDistrict,
                   orderDeliveryArea: orderDeliveryArea,
                   orderDeliveryNeighborhood: orderDeliveryNeighborhood,
                   orderDeliveryAddress: orderDeliveryAddress,
                   orderInvoiceTitle: orderInvoiceTitle,
                   orderInvoiceTaxOffice: orderInvoiceTaxOffice,
                   orderInvoiceTaxNumber: orderInvoiceTaxNumber,
                   orderInvoiceName: orderInvoiceName,
                   orderInvoiceSurname: orderInvoiceSurname,
                   orderInvoicePhoneNumber: orderInvoicePhoneNumber,
                   orderInvoiceEmailAddress: orderInvoiceEmailAddress,
                   orderInvoiceCountry: orderInvoiceCountry,
                   orderInvoiceCity: orderInvoiceCity,
                   orderInvoiceDistrict: orderInvoiceDistrict,
                   orderInvoiceArea: orderInvoiceArea,
                   orderInvoiceNeighborhood: orderInvoiceNeighborhood,
                   orderInvoiceAddress: orderInvoiceAddress,
                   orderCurrency: orderCurrency,
                   orderTotalPrice: orderTotalPrice,
                   orderVATPrice: orderVATPrice,
                   orderWithoutVATPrice: orderWithoutVATPrice,
                   orderCargoPriceIncluded: orderCargoPriceIncluded,
                   orderCreditCardSingleChargeDiscountRate: orderCreditCardSingleChargeDiscountRate,
                   orderCreditCardSingleChargeDiscountPrice: orderCreditCardSingleChargeDiscountPrice,
                   orderBankTransferDiscountRate: orderBankTransferDiscountRate,
                   orderBankTransferDiscountPrice: orderBankTransferDiscountPrice,
                   orderCargoDiscount: orderCargoDiscount,
                   orderCargoDiscountDescription: orderCargoDiscountDescription,
                   orderPaymentMethod: orderPaymentMethod,
                     orderPaymentStatus: orderPaymentStatus,
                   orderStatusID: orderStatusID
               },
               success: function (data) {
                   console.log(data);
                   var data = JSON.parse(data);
                   if (data.status == "success") {
                       
                       $("#formAlertModal").modal("show");
                       
                       $("#formAlertText").text("Sipariş başarıyla eklendi.");

                       setTimeout(function() {
                           $("#formAlertModal").modal("hide");
                       }, 1500);

                       //sipariş liste sayfasına gidelim
                       //window.location.href = "OrderList.php";
                   } else {
                       var message = data.message;
                       
                       $("#formAlertModal").modal("show");
                       
                       $("#formAlertText").text(message);
                   }
               },
               error: function (jqXHR, textStatus, errorThrown) {
                   // handle error
                   console.error(textStatus, errorThrown);
               }
           });
       });
   });
</script>

</body>
</html>
