<?php
/**
 * @var Config $config
 * @var Database $db
 * @var Helper $helper
 * @var Json $json
 * @var Session $session
 * @var array $requestData
 */

$casper = $session->getCasper();

if (!$casper instanceof Casper) {
    echo "Casper is not here - CartController:15";exit();
}

$config = $casper->getConfig();
$helper = $config->Helper;
$json = $config->Json;


$visitor = $casper->getVisitor();
if(!isset($visitor['visitorUniqID'])){
    header('Location: /?visitorID-None');exit();
}


$action = $requestData['action'] ?? null;

if(!isset($action)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Lütfen gerekli alanları doldurunuz.',
        'memberData' => []
    ]);
    exit();
}

$referrer = $requestData['referrer'];

if ($action == "add") {
    $siteConfig = $casper->getSiteConfig();
    if (empty($siteConfig)) {
        header('Location: /'); exit();
    }

    $pageLinks = $siteConfig['specificPageLinks'];
    foreach ($pageLinks as $pageLink) {
        if ($pageLink['sayfatip'] == 8) {
            $cartLink = $pageLink['link'];
            break;
        }
    }
    foreach ($pageLinks as $pageLink) {
        if ($pageLink['sayfatip'] == 9) {
            $checkOutLink = $pageLink['link'];
            break;
        }
    }
    // Ürün ID, stok kodu ve fiyatı POST verilerinden alınır
    $product_id = $requestData['productID'];
    $stock_code = $requestData['productStockCodeInput'];
    $price = $requestData['productPriceInput'];
    $quantity = $requestData['product-quantity'];
    $visitor = $casper->getVisitor();
    $visitorUniqueID = $visitor['visitorUniqID'];
    //print_r($visitorUniqueID);exit();

    // Cart modeli yüklenir
    require_once MODEL . 'Cart.php';
    $cart = new Cart($db, $helper, $session, $config);

    // Ürün sepete eklenir
    $result = $cart->addCart($product_id, $stock_code, $price, $quantity);
    $resultType = $result['status'];
    $resultMessage = $result['message'];
    if($resultType == "error"){
        $session->addSession('popup', [
            'status' => 'error',
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
            'duration' => 1000
        ]);
    }
    else{

        //{
        //  currency: "USD",
        //  value: 30.03,
        //  items: [
        //    {
        //      item_id: "SKU_12345",
        //      item_name: "Stan and Friends Tee",
        //      coupon: "SUMMER_FUN",
        //      discount: 2.22,
        //      item_variant: "green",
        //      price: 10.01,
        //      quantity: 3
        //    }
        //  ]
        //}
        $cartData = $result['cartData'];

        //$cartData = [
        //            'uyebenzersiz' => $insertData['uyebenzersiz'],
        //            'sepetbenzersiz' => $insertData['sepetbenzersiz'],
        //            'sepetolusturtarih' => $insertData['sepetolusturtarih'],
        //            'sepetguncelletarih' => $insertData['sepetguncelletarih'],
        //            'urunstokkodu' => $insertData['urunstokkodu'],
        //            'urunid' => $insertData['urunid'],
        //            'urunvaryant' => $insertData['urunvaryant'],
        //            'urundesi' => $insertData['urundesi'],
        //            'urunadet' => $insertData['urunadet'],
        //            'urunparabirim' => $insertData['urunparabirim'],
        //            'urunfiyat' => $insertData['urunfiyat'],
        //            'urunkdv' => $insertData['urunkdv'],
        //            'urunkargoucreti' => $insertData['urunkargoucreti'],
        //            'uruniadeadet' => $insertData['uruniadeadet'],
        //            'indirimmiktari' => $insertData['indirimmiktari'],
        //            'indirimaciklamasi' => $insertData['indirimaciklamasi'],
        //            'sepetdurum' => $insertData['sepetdurum'],
        //            'odemedurum' => $insertData['odemedurum'],
        //            'siparisbenzersiz' => $insertData['siparisbenzersiz'],
        //            'sepetsil' => $insertData['sepetsil']
        //        ];

        $currencyID = $cartData['urunparabirim'];

        include_once MODEL ."Currency.php";
        $currencyModel = new Currency($db);
        $currency = $currencyModel->getCurrencySymbolOrCode($currencyID,"code");

        include_once MODEL ."Page.php";
        $pageModel = new Page($db, $session);
        $page = $pageModel->getPageById($cartData['urunid']);
        $item_name = $page['sayfaad'];

        $value = $cartData['urunfiyat'] * $cartData['urunadet'];

        $itemID = $cartData['urunstokkodu'] ?? $stock_code;
        $discount = $cartData['indirimmiktari'];
        $coupon = $cartData['indirimaciklamasi'];
        $itemVariant = $cartData['urunvaryant'] ?? "";
        $price = $cartData['urunfiyat'];
        $quantity = $cartData['urunadet'];

        $items = [
            [
                'item_id' => $itemID,
                'item_name' => $item_name,
                'coupon' => $coupon,
                'discount' => $discount,
                'item_variant' => $itemVariant,
                'price' => $price,
                'quantity' => $quantity
            ]
        ];

        $gTagData = [
            'currency' => $currency,
            'value' => $value,
            'items' => $items
        ];

        $session->addSession('gTagBasket', $gTagData);

        $session->addSession('popup', [
            'status' => $resultType,
            'message' => $resultMessage,
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => true,
            'animation' => true,
            'duration' => 1000
        ]);
    }

    // Kullanıcıyı sepet sayfasına yönlendir
    if($referrer=="json"){
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
    if(isset($requestData["checkoutButton"])){
        header("Location: $checkOutLink");exit();
    }
    header("Location: $cartLink");exit();
}
else if ($action == "update") {
    // Ürün ID, stok kodu ve fiyatı POST verilerinden alınır
    $cart_id = $requestData['cartID'];
    $quantity = $requestData['cartQuantity'];
    if (!isset($cart_id) || !isset($quantity)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
            'cartData' => []
        ]);
        exit();
    }

    // Cart modeli yüklenir
    require_once MODEL . 'Cart.php';
    $cart = new Cart($db, $helper, $session, $config);

    // Ürün sepete eklenir
    $result = $cart->updateCart($cart_id, $quantity);
    if($referrer=="json"){
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
    header("Location: $referrer");exit();
}
else if ($action == "remove") {
    // Ürün ID, stok kodu ve fiyatı POST verilerinden alınır
    $cart_id = $requestData['cartID'];
    if (!isset($cart_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.'
        ]);
        exit();
    }

    // Cart modeli yüklenir
    require_once MODEL . 'Cart.php';
    $cart = new Cart($db, $helper, $session, $config);
    $result = $cart->removeCart($cart_id);

    if($referrer=="json"){
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
    header("Location: $referrer");exit();
}

