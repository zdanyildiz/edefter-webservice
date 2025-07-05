<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

/**
 * @todo sipariş düzenleme ve oluşturma yapılacak, en son iş kargo takip
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");

include_once MODEL . "Location.php";
$location = new Location($db);

include_once MODEL . "Admin/AdminOrder.php";
$adminOrder = new AdminOrder($db, $config);

$getOrderStatus = $adminOrder->getOrderStatuses();

include_once MODEL . "Admin/AdminCart.php";
$adminCart = new AdminCart($db,$config);

include_once MODEL . "Admin/AdminSiteConfig.php";
$siteConfig = new AdminSiteConfig($db,1);
$siteConfig = $siteConfig->getSiteConfig();
$companySettings = $siteConfig["companySettings"];
$configFirmName = $companySettings['ayarfirmakisaad'];
$bankSettings = $siteConfig['bankSettings'];

$logoInfo = $siteConfig['logoSettings'];
$logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

$creditCardBankName = "";
if(!empty($bankSettings)){

    $creditCardBankName = $bankSettings[0]['ayarbankaad'];
    $creditCardMerchantID = $bankSettings[0]['magazaid'];
    $creditCardMerchantKey = $bankSettings[0]['magazaparola'];
    $creditCardMerchantSalt = $bankSettings[0]['magazaanahtar'];

    if($creditCardBankName=="paytr") {
        include_once MODEL . 'Payment/PayTR.php';
        $payTR = new PayTR($creditCardMerchantID, $creditCardMerchantKey, $creditCardMerchantSalt);

    }

}


$orderType = $_GET['orderStatus'] ?? 0;

//oturumda çağırdığımız siparişler ($orders) var mı bakalım, varsa ve sipariş tipi aynıysa bir daha veri tabanından çekmeyelim
//önce oturumun adına karar verelim ve neleri taşıyacağımıza bakalım (toplam sipariş sayısı, sipariş bilgileri) sayfalama da yapalım

$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;


$sessionOrders = $_SESSION['orders'] ?? null;

if(isset($_GET["clear"])){
    unset($_SESSION['orders']);
    unset($sessionOrders);
}

$sessionOrdersType = "";
$sessionOrdersCount = 0;
if(!empty($sessionOrders)){
    $sessionOrdersType = $sessionOrders['type'] ?? "";
    $sessionOrdersCount = $sessionOrders['count'] ?? 0;
}

if($sessionOrdersType != $orderType) {
    if($orderType==99)
    {
        $OrderListsayfa="navOrdersByCreditCard";
        //kredi kartı
        $orders = $adminOrder->getOrdersByPaymentTypeAndOrderStatus('kk',1);
    }
    elseif($orderType==98)
    {
        $OrderListsayfa="navOrderByEft";
        //banka havalesi
        $orders = $adminOrder->getOrdersByPaymentTypeAndOrderStatus('bh',1);
    }
    elseif($orderType==97)
    {
        $OrderListsayfa="navOrdersByPaymentAtTheDoor";
        $orders = $adminOrder->getOrdersByPaymentTypeAndOrderStatus('ko',1);
        //kapıda ödeme

    }
    elseif($orderType==96)
    {
        $OrderListsayfa="OrderListyeniphp";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(0,6);
    }
    elseif($orderType==0)
    {
        $OrderListsayfa="navOrdersReadyToShip";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(1,0);
    }
    elseif($orderType==1)
    {
        $OrderListsayfa="OrderListyenibankaphp";
    }
    elseif($orderType==2)
    {
        $OrderListsayfa="navPreparedOrders";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(1,2);
    }
    elseif($orderType==3)
    {
        $OrderListsayfa="navOrdersShipped";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(1,3);
    }
    elseif($orderType==4)
    {
        $OrderListsayfa="navDeliveredorders";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(1,4);
    }
    elseif($orderType==5)
    {
        $OrderListsayfa="OrderListiadetalebiphp";
    }
    elseif($orderType==7)
    {
        $OrderListsayfa="OrderListdegisimtalebiphp";
    }
    elseif($orderType==8)
    {
        $OrderListsayfa="OrderListiitalalepphp";
    }
    elseif($orderType==9)
    {
        $OrderListsayfa="navSupplyExpectedOrders";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(1,9);
    }
    elseif($orderType==10)
    {
        $OrderListsayfa="navReturnedOrders";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(1,10);
    }
    elseif($orderType==11)
    {
        $OrderListsayfa="navCanceledOrders";
        $orders = $adminOrder->getOrdersByPaymentStatusAndOrderStatus(1,11);
    }
    elseif($orderType==100)
    {
        $OrderListsayfa="navAllOrders";
        $orders = $adminOrder->getAllOrders();
    }

    $count = count($orders);

    $_SESSION['orders'] = [
        'type' => $orderType,
        'count' => $count,
        'orders' => $orders,
        'OrderListsayfa' => $OrderListsayfa
    ];

}
else{
    $orders = $sessionOrders['orders'];
    $count = $sessionOrders['count'];
    $OrderListsayfa = $sessionOrders['OrderListsayfa'];
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Pozitif Panel - Sipariş Liste</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">
    <style>
        @media (max-width: 768px){
            .mobileClear{
                float: none;
                margin: 0 auto;
                clear: both;
            }
            }
        }
    </style>

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
                    <li class="active"><small class="text-xs">Sipariş Liste</small></li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-head style-primary">
                                <header>Siparişler</header>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body ">
                                <div class="form-group">
                                    <input type="text" name="q" id="q" class="form-control" placeholder="Ara: Sipariş No, Ad, Soyad, Telefon, Firma, Eposta, TC, Vergi No" value="">
                                </div>
                                <div class="form-group">
                                    <a href="/_y/s/s/siparisler/orderList.php_orderStatus=<?=$orderType?>">Sıfırla</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div id="orderList" class="card style-default">
                                <?php
                                $ii = 0;
                                foreach ($orders as $order){

                                    $ii++;
                                    //sayfalama yapalım
                                    if($ii <= $offset){
                                        continue;
                                    }
                                    $orderPaymentStatus = $order['siparisodemedurum'];

                                    switch ($orderPaymentStatus){
                                        case 0:
                                            $orderPaymentStatus = "Ödeme Onayı Bekleniyor";
                                            break;
                                        case 1:
                                            $orderPaymentStatus = "Ödeme Onaylandı";
                                            break;
                                    }

                                    $orderStatus = $order['siparisdurum'];

                                    switch ($orderStatus){
                                        case 0:
                                            $orderStatus = "Kargo Hazırlanıyor";
                                            $cardHeaderStyle = "style-primary";
                                            break;
                                        case 1:
                                            $orderStatus = "Ödeme Onayı Bekleniyor";
                                            $cardHeaderStyle = "style-warning";
                                            break;
                                        case 2:
                                            $orderStatus = "Siparişiniz Hazırlanıyor";
                                            $cardHeaderStyle = "style-info";
                                            break;
                                        case 3:
                                            $orderStatus = "Sipariş Kargoya Teslim Edildi";
                                            $cardHeaderStyle = "style-success";
                                            break;
                                        case 4:
                                            $orderStatus = "Teslimat Yapıldı";
                                            $cardHeaderStyle = "style-primary";
                                            break;
                                        case 5:
                                            $orderStatus = "İade Talebi Alındı";
                                            $cardHeaderStyle = "style-primary";
                                            break;
                                        case 6:
                                            $orderStatus = "Tamamlanamamış Sipariş";
                                            $cardHeaderStyle = "style-danger";
                                            break;
                                        case 7:
                                            $orderStatus = "Değişim Talebi Alındı";
                                            $cardHeaderStyle = "style-primary";
                                            break;
                                        case 8:
                                            $orderStatus = "İptal Talebi Alındı";
                                            $cardHeaderStyle = "style-primary";
                                            break;
                                        case 9:
                                            $orderStatus = "Tedarik Ediliyor";
                                            $cardHeaderStyle = "style-warning";
                                            break;
                                        case 10:
                                            $orderStatus = "İade Alındı";
                                            $cardHeaderStyle = "style-gray";
                                            break;
                                        case 11:
                                            $orderStatus = "İptal oldu";
                                            $cardHeaderStyle = "style-gray-bright";
                                            break;
                                    }

                                    $orderPaymentType = $order['siparisodemeyontemi'];

                                    switch ($orderPaymentType){
                                        case 'kk':
                                            $orderPaymentType = "Kredi Kartı";
                                            $orderPaymentIcon = "fa-credit-card";
                                            break;
                                        case 'bh':
                                            $orderPaymentType = "Banka Havalesi";
                                            $orderPaymentIcon = "fa-bank";
                                            break;
                                        case 'ko':
                                            $orderPaymentType = "Kapıda Ödeme";
                                            $orderPaymentIcon = "fa-money";
                                            break;
                                        default:
                                            $orderPaymentType = "Ödeme Tipi Bulunamadı";
                                            $orderPaymentIcon = "fa-question";
                                    }

                                    $orderUniqID = $order['siparisbenzersizid'];

                                    //siparisFaturaUnvan (ingizlice yazalım)
                                    $orderInvoiceTitle = $order['siparisfaturaunvan'];

                                    $orderDate = $order['siparistariholustur'];

                                    $orderDeliveryName = $order['siparisteslimatad'];
                                    $orderDeliverySurname = $order['siparisteslimatsoyad'];
                                    $orderDeliveryEmail = $order['siparisteslimateposta'];
                                    $orderDeliveryGSM = $order['siparisteslimatgsm'];
                                    $orderDeliveryTC = $order['siparisteslimattcno'];
                                    $orderDeliveryCountry = $location->getCountryNameById($order['siparisteslimatadresulke']);
                                    $orderDeliveryCity = $location->getCityNameById($order['siparisteslimatadressehir']);
                                    $orderDeliveryDistrict = $location->getCountyNameById($order['siparisteslimatadresilce']);
                                    $orderDeliveryNeighborhood = $location->getAreaNameById($order['siparisteslimatadressemt']);
                                    $orderDeliveryStreet = $location->getNeighborhoodNameById($order['siparisteslimatadresmahalle']);
                                    $orderDeliveryPostalCode = $order['siparisteslimatadrespostakod'];
                                    $orderDeliveryAddress = $order['siparisteslimatadresacik'];

                                    $orderInvoiceTitle = $order['siparisfaturaunvan'];
                                    $orderInvoiceTaxOffice = $order['siparisfaturavergidairesi'];
                                    $orderInvoiceTaxNumber = $order['siparisfaturavergino'];
                                    $orderInvoiceName = $order['siparisfaturaad'];
                                    $orderInvoiceSurname = $order['siparisfaturasoyad'];
                                    $orderInvoiceEmail = $order['siparisfaturaeposta'];
                                    $orderInvoiceGSM = $order['siparisfaturagsm'];
                                    $orderInvoiceCountry = $location->getCountryNameById($order['siparisfaturaadresulke']);
                                    $orderInvoiceCity = $location->getCityNameById($order['siparisfaturaadressehir']);
                                    $orderInvoiceDistrict = $location->getCountyNameById($order['siparisfaturaadresilce']);
                                    $orderInvoiceNeighborhood = $location->getAreaNameById($order['siparisfaturaadressemt']);
                                    $orderInvoiceStreet = $location->getNeighborhoodNameById($order['siparisfaturaadresmahalle']);
                                    $orderInvoicePostalCode = $order['siparisfaturaadrespostakod'];
                                    $orderInvoiceAddress = $order['siparisfaturaadresacik'];


                                    ?>
                                    <div class="col-md-12 panel-group no-padding" id="accordion-<?=$orderUniqID?>" style="margin-bottom: 0">
                                        <div class="card card-bordered panel" id="card-<?=$orderUniqID?>">

                                            <div class="card-head collapsed" >
                                                <div class="tools hidden-print">
                                                    <div class="btn-group">
                                                        <a class="btn btn-lg" href="/_y/s/s/siparisler/CreateOrder.php?uniqid=<?=$orderUniqID?>"><i class="fa fa-pencil"></i></a>
                                                        <a class="btn btn-lg btn-print" data-id="<?=$orderUniqID?>"><i class="fa fa-print"></i></a>
                                                        <a class="btn btn-icon-toggle btn-collapse btn-lg <?=$cardHeaderStyle?>" data-toggle="collapse" data-parent="#accordion-<?=$orderUniqID?>" data-target="#accordion-<?=$orderUniqID?>-1"><i class="fa fa-angle-down"></i></a>
                                                    </div>
                                                </div>
                                                <header class="col-md-10" style="font-size: 15px">
                                                    <div class="col-md-4">
                                                        <i class="fa <?=$orderPaymentIcon?>"></i>
                                                        <?=$orderUniqID?>
                                                    </div>
                                                     <div class="col-md-6">
                                                         <?=substr($orderInvoiceTitle, 0, 50)?>...
                                                     </div>
                                                        <div class="col-md-2">
                                                            <?=substr($orderDate, 0,16)?>
                                                        </div>
                                                </header>
                                            </div>

                                            <div id="accordion-<?=$orderUniqID?>-1" class="collapse">
                                                <div class="card-body style-default-bright">
                                                    <div class="row">
                                                        <div id="cargoCard" class="col-sm-6">
                                                            <div class="card">
                                                                <div class="card-head card-head-xs ">
                                                                    <header><i class="fa fa-plane"></i> Kargo Bilgileri</header>
                                                                </div>
                                                                <div class="card-body">
                                                                    <ul class="list divider-full-bleed">
                                                                        <li>
                                                                            <strong>TC:</strong> <?=$orderDeliveryTC?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Ad Soyad:</strong> <?=$orderDeliveryName?> <?=$orderDeliverySurname?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>GSM - E-Posta:</strong> <?=$orderDeliveryGSM?> <?=$orderDeliveryEmail?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Şehir - İlçe:</strong> <?=$orderDeliveryCity?> <?=$orderDeliveryDistrict?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Semt - Mahalle:</strong> <?=$orderDeliveryNeighborhood?> <?=$orderDeliveryStreet?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Adres:</strong> <?=$orderDeliveryAddress?> <?=$orderDeliveryPostalCode?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Ülke:</strong> <?=$orderDeliveryCountry?>
                                                                        </li>
                                                                        <li> - </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="invoiceCard" class="col-sm-6">
                                                            <div class="card">
                                                                <div class="card-head card-head-xs ">
                                                                    <header><i class="fa fa-file-pdf-o"></i>  Fatura Bilgileri</header>
                                                                </div>
                                                                <div class="card-body">
                                                                    <ul class="list divider-full-bleed">
                                                                        <li>
                                                                            <strong>Fatura Ünvan:</strong> <?=$orderInvoiceTitle?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Vergi Dairesi:</strong> <?=$orderInvoiceTaxOffice?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Vergi No:</strong> <?=$orderInvoiceTaxNumber?>
                                                                        <li>
                                                                        <li>
                                                                            <strong>Ad Soyad:</strong> <?=$orderInvoiceName?> <?=$orderInvoiceSurname?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>GSM - E-Posta:</strong> <?=$orderInvoiceGSM?> <?=$orderInvoiceEmail?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Ülke - Şehir - İlçe:</strong> <?=$orderInvoiceCountry?> <?=$orderInvoiceCity?> <?=$orderInvoiceDistrict?>
                                                                        </li>
                                                                        <li>
                                                                            <strong>Semt - Mahalle - Adres:</strong> <?=$orderInvoiceNeighborhood?> <?=$orderInvoiceStreet?> <?=$orderInvoiceAddress?> <?=$orderInvoicePostalCode?>
                                                                        </li>

                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php

                                                    $orderProductIDs = explode(",", $order['siparisurunidler']);
                                                    $orderProductNames = explode("||", $order['siparisurunadlar']);
                                                    $orderProductStockCodes = explode("||", $order['siparisurunstokkodlar']);
                                                    $orderProductCategories = explode("||", $order['siparisurunkategoriler']);
                                                    $orderProductPrices = explode("||", $order['siparisurunfiyatlar']);
                                                    $orderProductQuantities = explode("||", $order['siparisurunadetler']);
                                                    ?>
                                                    <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>Ürün Adı</th>
                                                            <th>Stok Kodu</th>
                                                            <th>Fiyat</th>
                                                            <th>Adet</th>
                                                            <th>Toplam</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $productDiscountAmount = 0;
                                                        $productDiscountDescription="";
                                                        //toplam indirim miktarı
                                                        $totalDiscountAmount = 0;
                                                        for ($i = 0; $i < count($orderProductIDs); $i++){
                                                            $orderProductID = $orderProductIDs[$i];
                                                            $orderProductName = $orderProductNames[$i];
                                                            $orderProductName = str_replace(".0000", "", $orderProductName);
                                                            $orderProductStockCode = $orderProductStockCodes[$i];
                                                            $orderProductCategory = $orderProductCategories[$i];
                                                            $orderProductPrice = $orderProductPrices[$i];
                                                            $orderProductQuantity = $orderProductQuantities[$i];
                                                            $orderProductQuantity = str_replace(".0000", "", $orderProductQuantity);

                                                            $productVariant = "";
                                                            $orderBasket = $adminCart->getCartByOrderUniqID($orderUniqID,$orderProductStockCode);
                                                            if(!empty($orderBasket)){

                                                                $productVariant = $orderBasket['cartProducts'][0]['productSelectedVariant'];
                                                                $productImages = $orderBasket['cartProducts'][0]['productImage'] ?? "";

                                                                $productDiscountAmount = $orderBasket['cartProducts'][0]['productDiscountAmount'] ?? 0;
                                                                $totalDiscountAmount += $productDiscountAmount;
                                                                $productDiscountDescription = $orderBasket['cartProducts'][0]['productDiscountDescription'] ?? "";

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
                                                            ?>
                                                            <tr>
                                                                <td class="no-padding list">

                                                                    <div class="tile">
                                                                        <div class="tile-content col-md-10">
                                                                            <?php
                                                                            if(!empty($productImages)){
                                                                                $productImage = explode(",",$productImages)[0];
                                                                                echo '
                                                                                    <div class="tile-icon">
                                                                                        <img src="'.imgRoot.'?imagePath='.$productImage.'&width=100&height=100">
                                                                                    </div>
                                                                                    ';
                                                                            }
                                                                            ?>
                                                                            <div class="tile-text">
                                                                                    <div class="text-xs"><?=$orderProductName?></div>
                                                                                    <?php if(!empty($productVariant)):?>
                                                                                        <small><?=$productVariant?></small>
                                                                                    <?php endif; ?>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </td>
                                                                <td><?=$orderProductStockCode?></td>
                                                                <td><?=$orderProductPrice?>TL</td>
                                                                <td><?=$orderProductQuantity?></td>
                                                                <td><?=number_format($orderProductPrice * $orderProductQuantity,2,".","")?>TL</td>
                                                            </tr>
                                                            <!-- indirim miktarı boş değilse -->
                                                            <?php if($productDiscountAmount > 0): ?>
                                                                <tr class="bg-warning">
                                                                    <td colspan="2" class="text-right">İndirim</td>
                                                                    <td colspan="2"><?=$productDiscountDescription?></td>
                                                                    <td><?=number_format($productDiscountAmount,2,".","")?>TL</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <!-- toplam indirim miktarı -->
                                                            <?php if($totalDiscountAmount > 0): ?>
                                                                <tr class="bg-danger">
                                                                    <td colspan="4" class="text-right">Toplam İndirim</td>
                                                                    <td><?=number_format($totalDiscountAmount,2,".","")?>TL</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <div class="row hidden-print">
                                                                        <div class="col-md-6">
                                                                            <div class="col-md-5 mobileClear">
                                                                                <strong style="float: left;height: 36px;line-height: 36px;text-align: center"><?=$orderStatus?></strong>
                                                                            </div>
                                                                            <div class="col-md-6 mobileClear">
                                                                                <div class="form-group">
                                                                                    <button
                                                                                            data-id="<?=$orderUniqID?>"
                                                                                            data-orderstatus="<?=$order['siparisdurum']?>"
                                                                                            class="btn btn-default-dark btn-sm updateOrderStatus" style="float: left">Sipariş durumunu değiştir
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php if($orderPaymentType == "Banka Havalesi" || $orderPaymentType == "Kapıda Ödeme"): ?>
                                                                        <div class="col-md-6">
                                                                            <div class="col-md-5 mobileClear">
                                                                                <strong style="float: left;height: 36px;line-height: 36px;text-align: center"><?=$orderPaymentStatus?></strong>
                                                                            </div>
                                                                            <div class="col-md-6 mobileClear">
                                                                                <div class="form-group">
                                                                                    <button
                                                                                            data-id="<?=$orderUniqID?>"
                                                                                            data-orderstatus="<?=$order['siparisodemedurum']?>"
                                                                                            class="btn btn-primary-bright btn-sm updateOrderPaymentStatus" style="float: left">Ödeme durumunu değiştir
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                                <td class="bg-success">Toplam</td>
                                                                <td class="bg-success"><?=number_format($order['siparistoplamtutar'],2,",",".")?>TL</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    </div>
                                                    <?php if($orderPaymentType == "Kredi Kartı"): ?>
                                                    <div class="row hidden-print">
                                                            <div class="col-md-9" style="z-index: 9">
                                                                <button id="checkPayment" data-id="<?=$orderUniqID?>" type="button" class="btn ink-reaction btn-xs btn-primary" style="float: right">Ödeme Kontrol Et</button>
                                                            </div>
                                                            <div id="paymentStatus-<?=$orderUniqID?>" class="paymentStatus text-primary col-md-2">Durum</div>
                                                            <div id="paymentTotal-<?=$orderUniqID?>" class="paymentTotal text-primary col-md-1">Tutar</div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                        </div>
                                        <em class="text-caption" style="margin-bottom:0"><?=$orderPaymentType?> |</em>
                                    </div>
                                    <?php
                                    //sayfalama yapalım
                                    if($ii >= $offset + $limit){
                                        break;
                                    }
                                }
                                ?>
                            <!-- pagination -->
                            <div class="row hidden-print">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <ul class="pagination">
                                            <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=<?=$orderType?>&limit=<?=$limit?>&page=1">İlk</a></li>
                                            <?php
                                            $pageCount = ceil($count / $limit);
                                            for ($i = 1; $i <= $pageCount; $i++){
                                                $active = $page == $i ? "active" : "";
                                                echo "<li class='$active'><a href='/_y/s/s/siparisler/OrderList.php?orderStatus=$orderType&limit=$limit&page=$i'>$i</a></li>";
                                            }
                                            ?>
                                            <li><a href="/_y/s/s/siparisler/OrderList.php?orderStatus=<?=$orderType?>&limit=<?=$limit?>&page=<?=$pageCount?>">Son</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php require_once(ROOT . "/_y/s/b/menu.php"); ?>
</div>
<!-- Modal dialog oluşturalım -->
<div class="modal fade" id="orderStatusUpdateContainer" tabindex="-1" role="dialog" aria-labelledby="modal-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Sipariş Durumunu Değiştirin</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="orderUniqID" id="hiddenOrderUniqID">
                <div class="form-group">
                    <select class="form-control" id="orderStatus" name="orderStatus">
                        <option value="">Sipariş Durumu Seçin</option>
                        <?php
                        foreach ($getOrderStatus as $status){
                            echo "<option value='{$status['siparisdurumid']}'>{$status['siparisdurumbaslik']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <!-- iptal ya da iade durumunda para iadesi için input ekleyelim -->
                <div class="form-group hidden" id="orderRefundAmountContainer">
                    <input type="text" class="form-control" id="orderRefundAmount" name="orderRefundAmount" placeholder="İade Tutarı (Opsiyonel)">
                </div>
                <div class="form-group">
                    <!-- sipariş durum değişikliği müşteriye e-posta olarak gitsin mi diye bir checkbox koyalım -->
                    <label class="checkbox-inline">
                        <input type="checkbox" name="sendEmail" value="">Sipariş durumunu müşteriye e-posta gönder (Opsiyonel)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat" data-dismiss="modal">Kapat</button>
                <button type="button" id="sendOrderStatus" class="btn btn-flat btn-primary">Kaydet</button>
            </div>
        </div>
    </div>
</div>
<!-- orderStatusUpdate sonucunu bildirecek alert yapalım -->
<div class="modal fade" id="orderStatusUpdateResultContainer" tabindex="-1" role="dialog" aria-labelledby="modal-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Sipariş Durumu Güncelleme Sonucu</h4>
            </div>
            <div class="modal-body">
                <div id="orderStatusUpdateResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<!-- ödeme durumunu değiştirmek için modal -->
<div class="modal fade" id="paymentStatusUpdateContainer" tabindex="-1" role="dialog" aria-labelledby="modal-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Ödeme Durumunu Değiştirin</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="orderUniqID" id="hiddenOrderUniqID">
                <div class="form-group">
                    <select class="form-control" id="orderPaymentStatus" name="orderPaymentStatus">
                        <option value="0">Ödeme Onayı Bekleniyor</option>
                        <option value="1">Ödeme Onaylandı</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat" data-dismiss="modal">Kapat</button>
                <button type="button" id="sendPaymentStatus" class="btn btn-flat btn-primary">
                    Kaydet
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ödeme durumunu değiştirmek için modal sonucunu bildirecek alert yapalım -->
<div class="modal fade" id="paymentStatusUpdateResultContainer" tabindex="-1" role="dialog" aria-labelledby="modal-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">
                    Ödeme Durumu Güncelleme Sonucu
                </h4>
            </div>
            <div class="modal-body">
                <div id="paymentStatusUpdateResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat" data-dismiss="modal">Kapat</button>
                </div>
        </div>
    </div>
</div>



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
    $("#<?=$OrderListsayfa?>").addClass("active");

    $(document).ready(function (){

        $(document).on("click",".btn-print",function(){

            var orderUniqID = $(this).data("id");

            var cardHeader = $("#card-"+orderUniqID+" .card-head").html();

            var degistir='"col-md-10"';
            cardHeader = cardHeader.replace(degistir, '"col-md-6" style="float:right"');

            var logoContainer = '<div class="col-md-6" style="float:left"><img id="logoImage" src="<?=$logo?>" style="width: 200px; height: auto;" alt=<?=$configFirmName?>"></div>';

            cardHeader = '<div class="row">' + logoContainer + cardHeader + '</div>';

            var cardBody = $("#card-"+orderUniqID+" .card-body").html();

            degistir = 'id="cargoCard" class="col-sm-6"';
            cardBody = cardBody.replace(degistir, 'style="width:50%;float:left;box-sizing:border-box"');

            degistir = 'id="invoiceCard" class="col-sm-6"';
            cardBody = cardBody.replace(degistir, 'style="width:50%;float:left;box-sizing:border-box"');

            var printContents = "<div class='card'>"+cardHeader+cardBody+"</div>";


            var logoImage = new Image();
            logoImage.src = "<?=$logo?>";
            logoImage.onload = function() {
                var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                if (isMobile) {
                    var HeadContent = document.head.innerHTML;
                    var printWindow = window.open('', '_blank');
                    printWindow.document.write('<html><head>' + HeadContent + '</head><body>');
                    printWindow.document.write(printContents);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.print();
                } else {
                    var originalContents = document.body.innerHTML;

                    document.body.innerHTML = printContents;

                    window.print();
                    document.body.innerHTML = originalContents;
                }
            };
        });

        //#checkPayment dinleyelim. data-id ile sipariş id'sini alalım, AdminOrderController ile ödeme durumunu kontrol edelim. parametre action ve orderUniqID olsun

        $(document).on("click","#checkPayment",function(){
            var orderUniqID = $(this).data("id");
            var action = "checkPaymentStatus";
            var paymentStatus = $("#paymentStatus-"+orderUniqID);
            var paymentTotal = $("#paymentTotal-"+orderUniqID);

            $.ajax({
                url: "/App/Controller/Admin/AdminOrderController.php",
                type: "POST",
                data: {action: action, orderUniqID: orderUniqID},
                success: function(response){
                    //console.log(response);
                    var data = JSON.parse(response);
                    if(data.status === "success"){
                        paymentStatus.text(data.message);
                        paymentTotal.text(data.paymentTotal);
                    }
                    else{
                        paymentStatus.text(data.status);
                        paymentTotal.text(data.message);
                    }
                }
            });
        });

        //#orderStatusUpdateContainer içindeki #orderStatus'i değiştirdiğimizde #orderRefundAmount'ı gösterelim
        $(document).on("change","#orderStatus",function(){
            var orderStatus = $(this).val();
            var orderRefundAmountContainer = $("#orderRefundAmountContainer");

            if(orderStatus == 10 || orderStatus == 11){
                orderRefundAmountContainer.removeClass("hidden");
            }
            else{
                orderRefundAmountContainer.addClass("hidden");
            }
        });

        //#orderRefundAmount değeri 1.00 şeklinde olmalı harf yada virgül kabul etmemeli
        $(document).on("keyup","#orderRefundAmount",function(){
            var orderRefundAmount = $(this).val();
            var orderRefundAmountContainer = $("#orderRefundAmountContainer");

            if(orderRefundAmount.match(/^[0-9]+(\.[0-9]{1,2})?$/)){
                orderRefundAmountContainer.removeClass("has-error");
            }
            else{
                orderRefundAmountContainer.addClass("has-error");
                orderRefundAmount = orderRefundAmount.replace(/[^0-9.]/g, "");
                $(this).val(orderRefundAmount);
            }
        });

        //button.updateOrderStatus dinleyelim. data-id ile sipariş id'sini alalım, AdminOrderController ile sipariş durumunu güncelleyelim. parametre action ve orderUniqID olsun

        $(document).on("click",".updateOrderStatus",function(){
            var orderUniqID = $(this).data("id");
            $("#orderStatusUpdateContainer #hiddenOrderUniqID").val(orderUniqID);

            var action = "updateOrderStatus";

            var orderStatus = $(this).data("orderstatus");

            //#orderStatus'i seçili yapalım
            $("#orderStatusUpdateContainer #orderStatus").val(orderStatus);

            $("#orderStatusUpdateContainer").modal("show");
        });

        $(document).on("click","#sendOrderStatus",function(){

            var orderUniqID = $("#orderStatusUpdateContainer #hiddenOrderUniqID").val();
            var orderStatus = $("#orderStatus").val();
            var message = "";

            if(orderStatus == 10 || orderStatus == 11){

                var orderRefundAmount = $("#orderRefundAmount").val();
                if(orderRefundAmount != "") {

                    if (!orderRefundAmount.match(/^[0-9]+(\.[0-9]{1,2})?$/)) {
                        $("#orderRefundAmountContainer").addClass("has-error");
                        return false;
                    }

                    var newAction = "refundPayment";

                    $.ajax({
                        url: "/App/Controller/Admin/AdminOrderController.php",
                        type: "POST",
                        data: {
                            action: newAction,
                            orderUniqID: orderUniqID,
                            orderStatus: orderStatus,
                            returnAmount: orderRefundAmount
                        },
                        success: function (response) {
                            //console.log(response);
                            var data = JSON.parse(response);
                            if (data.status === "success") {

                                message = data.message;
                            } else {

                                message = data.message;
                            }
                        }
                    });
                }
            }

            if(message !== ""){
                message = message + " <br>";
            }

            var sendEmail = $("input[name='sendEmail']").is(":checked") ? 1 : 0;
            var action = "updateOrderStatus";

            $.ajax({
                url: "/App/Controller/Admin/AdminOrderController.php",
                type: "POST",
                data: {action: action, orderUniqID: orderUniqID, orderStatus: orderStatus, sendEmail: sendEmail},
                success: function(response){
                    console.log(response);

                    //modalı kapatalım
                    $("#orderStatusUpdateContainer").modal("hide");

                    //sonuç modalını gösterelim
                    $("#orderStatusUpdateResultContainer").modal("show");

                    var data = JSON.parse(response);

                    $("#orderStatusUpdateResult").html(message + data.message);

                    if(data.status === "success"){
                        $("#orderStatusUpdateResultContainer .modal-header").removeClass("bg-danger").addClass("bg-primary");
                        //3 saniye sonra sayfayı yenileyelim
                        setTimeout(function(){
                            location.reload();
                        },3000);

                    }
                    else{
                        $("#orderStatusUpdateResultContainer .modal-header").removeClass("bg-primary").addClass("bg-danger");
                        //3 saniye sonra modalı kapatalım
                        setTimeout(function(){
                            $("#orderStatusUpdateResultContainer").modal("hide");
                        },3000);
                    }
                }
            });
        });

        //#sendOrderPaymentStatus dinleyelim. data-id ile sipariş id'sini alalım, AdminOrderController ile sipariş durumunu güncelleyelim. parametre action ve orderUniqID olsun
        $(document).on("click",".updateOrderPaymentStatus",function (){
            var orderUniqID = $(this).data("id");
            $("#paymentStatusUpdateContainer #hiddenOrderUniqID").val(orderUniqID);
            var action = "updateOrderPaymentStatus";
            var orderPaymentStatus = $(this).data("orderstatus");
            $("#paymentStatusUpdateContainer #orderPaymentStatus").val(orderPaymentStatus);

            $("#paymentStatusUpdateContainer").modal("show");
        });

        $(document).on("click","#sendPaymentStatus",function () {
            var orderUniqID = $("#paymentStatusUpdateContainer #hiddenOrderUniqID").val();
            var orderPaymentStatus = $("#paymentStatusUpdateContainer #orderPaymentStatus").val();

            var action = "updateOrderPaymentStatus";
            $.ajax({
                url: "/App/Controller/Admin/AdminOrderController.php",
                type: "POST",
                data: {action: action, orderUniqID: orderUniqID, orderPaymentStatus: orderPaymentStatus},
                success: function (response) {
                    console.log(response);
                    //modalı kapatalım
                    $("#paymentStatusUpdateContainer").modal("hide");
                    //sonuç modalını gösterelim
                    $("#paymentStatusUpdateResultContainer").modal("show");
                    var data = JSON.parse(response);
                    $("#paymentStatusUpdateResult").html(data.message);
                    if (data.status === "success") {
                        $("#paymentStatusUpdateResultContainer .modal-header").removeClass("bg-danger").addClass("bg-primary");
                        //3 saniye sonra sayfayı yenileyelim
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    } else {
                        $("#paymentStatusUpdateResultContainer .modal-header").removeClass("bg-primary").addClass("bg-danger");
                        //3 saniye sonra modalı kapatal
                        setTimeout(function () {
                            $("#paymentStatusUpdateResultContainer").modal("hide");
                        }, 3000);
                    }
                }
            });
        });

        $(document).on("keyup","#q",function(){
            var q = $(this).val();
            //3 karakterden büyükse arama yapalım
            if(q.length > 2){

                var url = "/App/Controller/Admin/AdminOrderController.php";
                var action = "getOrdersBySearchText";
                var data = {action: action, q: q};

                $.ajax({
                    url: url,
                    type: "POST",
                    data: data,
                    success: function(response) {
                        //console.log(response);
                        var data = JSON.parse(response);
                        if (data.status === "success") {

                            var orders = data.orders;
                            var html = "";
                            for (var i = 0; i < orders.length; i++) {
                                var order = orders[i];
                                var orderUniqID = order.orderUniqID;
                                var orderStatusID = order.orderStatusID;
                                var orderStatus = order.orderStatus;
                                var orderPaymentStatus = order.orderPaymentStatus;
                                var orderPaymentType = order.orderPaymentType;
                                var orderPaymentIcon = order.orderPaymentIcon;
                                var cardHeaderStyle = order.cardHeaderStyle;
                                var orderDate = order.orderDate;
                                var orderDeliveryName = order.orderDeliveryName;
                                var orderDeliverySurname = order.orderDeliverySurname;
                                var orderDeliveryEmail = order.orderDeliveryEmail;
                                var orderDeliveryGSM = order.orderDeliveryGSM;
                                var orderDeliveryTC = order.orderDeliveryTC;
                                var orderDeliveryCountry = order.orderDeliveryCountry;
                                var orderDeliveryCity = order.orderDeliveryCity;
                                var orderDeliveryDistrict = order.orderDeliveryDistrict;
                                var orderDeliveryNeighborhood = order.orderDeliveryNeighborhood;
                                var orderDeliveryStreet = order.orderDeliveryStreet;
                                var orderDeliveryPostalCode = order.orderDeliveryPostalCode;
                                var orderDeliveryAddress = order.orderDeliveryAddress;
                                var orderInvoiceTitle = order.orderInvoiceTitle;
                                var orderInvoiceTaxOffice = order.orderInvoiceTaxOffice;
                                var orderInvoiceTaxNumber = order.orderInvoiceTaxNumber;
                                var orderInvoiceName = order.orderInvoiceName;
                                var orderInvoiceSurname = order.orderInvoiceSurname;
                                var orderInvoiceEmail = order.orderInvoiceEmail;
                                var orderInvoiceGSM = order.orderInvoiceGSM;
                                var orderInvoiceCountry = order.orderInvoiceCountry;
                                var orderInvoiceCity = order.orderInvoiceCity;
                                var orderInvoiceDistrict = order.orderInvoiceDistrict;
                                var orderInvoiceNeighborhood = order.orderInvoiceNeighborhood;
                                var orderInvoiceStreet = order.orderInvoiceStreet;
                                var orderInvoicePostalCode = order.orderInvoicePostalCode;
                                var orderInvoiceAddress = order.orderInvoiceAddress;
                                var orderProducts = order.orderProducts;
                                var orderTotalPrice = order.orderTotalPrice;

                                html += '<div class="col-md-12 panel-group no-padding" id="accordion-' + orderUniqID + '" style="margin-bottom: 0">';
                                html += '<div class="card card-bordered panel" id="card-' + orderUniqID + '">';
                                html += '<div class="card-head collapsed">';
                                html += '<div class="tools hidden-print">';
                                html += '<div class="btn-group">';
                                html += '<a class="btn btn-lg" href=' + orderUniqID + '"/_y/s/s/siparisler/CreateOrder.php?uniqid="><i class="fa fa-pencil"></i></a>';
                                html += '<a class="btn btn-lg btn-print" data-id="' + orderUniqID + '"><i class="fa fa-print"></i></a>';
                                html += '<a class="btn btn-icon-toggle btn-collapse btn-lg ' + cardHeaderStyle + '" data-toggle="collapse" data-parent="#accordion-' + orderUniqID + '" data-target="#accordion-' + orderUniqID + '-1"><i class="fa fa-angle-down"></i></a>';
                                html += '</div>';
                                html += '</div>';
                                html += '<header class="col-md-10" style="font-size: 15px">';
                                html += '<div class="col-md-4">';
                                html += '<i class="fa ' + orderPaymentIcon + '"></i>';
                                html += ' ' + orderUniqID;
                                html += '</div>';
                                html += '<div class="col-md-6">';
                                html += orderInvoiceTitle.substr(0, 50) + '...';
                                html += '</div>';
                                html += '<div class="col-md-2">';
                                html += orderDate.substr(0, 16);
                                html += '</div>';
                                html += '</header>';
                                html += '</div>';
                                html += '<div id="accordion-' + orderUniqID + '-1" class="collapse">';
                                html += '<div class="card-body style-default-bright">';
                                html += '<div class="row">';
                                html += '<div id="cargoCard" class="col-sm-6">';
                                html += '<div class="card">';
                                html += '<div class="card-head card-head-xs ">';
                                html += '<header><i class="fa fa-plane"></i> Kargo Bilgileri</header>';
                                html += '</div>';
                                html += '<div class="card-body">';
                                html += '<ul class="list divider-full-bleed">';
                                html += '<li>';

                                html += '<strong>Ad Soyad:</strong> ' + orderDeliveryName + ' ' + orderDeliverySurname;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>GSM - E-Posta:</strong> ' + orderDeliveryGSM + ' ' + orderDeliveryEmail;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Şehir - İlçe:</strong> ' + orderDeliveryCity + ' ' + orderDeliveryDistrict;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Semt - Mahalle:</strong> ' + orderDeliveryNeighborhood + ' ' + orderDeliveryStreet;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Adres:</strong> ' + orderDeliveryAddress + ' ' + orderDeliveryPostalCode;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Ülke:</strong> ' + orderDeliveryCountry;
                                html += '</li>';
                                html += '<li> - </li>';
                                html += '</ul>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '<div id="invoiceCard" class="col-sm-6">';
                                html += '<div class="card">';
                                html += '<div class="card-head card-head-xs ">';
                                html += '<header><i class="fa fa-file-pdf-o"></i>  Fatura Bilgileri</header>';
                                html += '</div>';
                                html += '<div class="card-body">';
                                html += '<ul class="list divider-full-bleed">';
                                html += '<li>';
                                html += '<strong>Fatura Ünvan:</strong> ' + orderInvoiceTitle;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Vergi Dairesi:</strong> ' + orderInvoiceTaxOffice;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Vergi No:</strong> ' + orderInvoiceTaxNumber;
                                html += '<li>';
                                html += '<li>';
                                html += '<strong>Ad Soyad:</strong> ' + orderInvoiceName + ' ' + orderInvoiceSurname;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>GSM - E-Posta:</strong> ' + orderInvoiceGSM + ' ' + orderInvoiceEmail;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Ülke - Şehir - İlçe:</strong> ' + orderInvoiceCountry + ' ' + orderInvoiceCity + ' ' + orderInvoiceDistrict;
                                html += '</li>';
                                html += '<li>';
                                html += '<strong>Semt - Mahalle - Adres:</strong> ' + orderInvoiceNeighborhood + ' ' + orderInvoiceStreet + ' ' + orderInvoiceAddress + ' ' + orderInvoicePostalCode;
                                html += '</li>';
                                html += '</ul>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '<div class="table-responsive">';
                                html += '<table class="table table-bordered table-striped">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th>Ürün Adı</th>';
                                html += '<th>Stok Kodu</th>';
                                html += '<th>Fiyat</th>';
                                html += '<th>Adet</th>';
                                html += '<th>Toplam</th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                for (var j = 0; j < orderProducts.length; j++) {
                                    var orderProduct = orderProducts[j];
                                    var orderProductID = orderProduct.productID;
                                    var orderProductName = orderProduct.productName;
                                    var orderProductStockCode = orderProduct.productStockCode;
                                    var orderProductCategory = orderProduct.productCategory;
                                    var orderProductPrice = orderProduct.productPrice;
                                    var orderProductQuantity = orderProduct.productQuantity;
                                    var productVariant = orderProduct.productVariant;
                                    var productTotalPrice = orderProduct.productTotalPrice;
                                    var productImage = orderProduct.productImage;

                                    html += '<tr>';
                                    html += '<td class="no-padding list">';
                                    html += '<div class="tile">';
                                    html += '<div class="tile-content col-md-10">';
                                    if (productImage !== "") {
                                        html += '<div class="tile-icon">';
                                        html += '<img src="<?=imgRoot?>?imagePath=' + productImage + '&width=100&height=100">';
                                        html += '</div>';
                                    }
                                    html += '<div class="tile-text">';
                                    html += '<div class="text-xs">' + orderProductName + '</div>';
                                    if (productVariant !== "") {
                                        html += '<small>' + productVariant + '</small>';
                                    }
                                    html += '</div>';
                                    html += '</div>';
                                    html += '</div>';
                                    html += '</td>';
                                    html += '<td>' + orderProductStockCode + '</td>';
                                    html += '<td>' + orderProductPrice + 'TL</td>';
                                    html += '<td>' + orderProductQuantity + '</td>';
                                    html += '<td>' + productTotalPrice + 'TL</td>';
                                    html += '</tr>';
                                }
                                html += '</tbody>';
                                html += '<tfoot>';
                                html += '<tr>';
                                html += '<td colspan="3">';
                                html += '<div class="row hidden-print">';
                                html += '<div class="col-sm-3 mobileClear">';
                                html += '<strong style="float: left;height: 36px;line-height: 36px;text-align: center">' + orderStatus + '</strong>';
                                html += '</div>';
                                html += '<div class="col-sm-5 mobileClear">';
                                html += '<div class="form-group">';
                                html += '<button data-id="' + cardHeaderStyle + '" data-orderstatus="' + orderStatusID + '" class="btn btn-default-dark updateOrderStatus" style="float: left">Sipariş durumunu değiştir</button>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</td>';
                                html += '<td class="bg-success">Toplam</td>';
                                html += '<td class="bg-success">' + orderTotalPrice + 'TL</td>';
                                html += '</tr>';
                                html += '</table>';

                                if (orderPaymentType === "Kredi Kartı") {
                                    html += '<div class="row hidden-print">';
                                    html += '<div class="col-md-9" style="z-index:9">';
                                    html += '<button id="checkPayment" data-id="' + orderUniqID + '" type="button" class="btn ink-reaction btn-xs btn-primary" style="float: right">Ödeme Kontrol Et</button>';
                                    html += '</div>';
                                    html += '<div id="paymentStatus-' + orderUniqID + '" class="paymentStatus text-primary col-md-2">Durum</div>';
                                    html += '<div id="paymentTotal-' + orderUniqID + '" class="paymentTotal text-primary col-md-1">Tutar</div>';
                                    html += '</div>';
                                }

                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '<em class="text-caption" style="margin-bottom:0">' + orderPaymentType + ' |</em>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            }
                            $("#orderList").html(html);
                        }
                    }
                });
            }
        });
    });
</script>

</body>
</html>
