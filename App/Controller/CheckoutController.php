<?php
/**
 * @var Session $session
 * @var Database $db
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

$siteConfig = $casper->getSiteConfig();
if(empty($siteConfig)){
    header('Location: /?siteConfig-None');exit();
}
//Log::write('siteConfig '. json_encode($siteConfig), 'info');
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

$memberLinkItem = array_filter($pageLinks, function($pageLink) {
    return $pageLink['sayfatip'] == 17;
});
$memberLinkItem = reset($memberLinkItem);
$memberLink = $memberLinkItem['link'];

$action = $requestData['action'] ?? null;

if(!isset($action)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error.',
        'memberData' => []
    ]);
    exit();
}

$returnLink = $requestData["referrer"];


$languageCode = $requestData["languageCode"] ?? "";
if(empty($languageCode)){
    $languageCode = (isset($routerResult["languageCode"])) ? $helper->toLowerCase($routerResult["languageCode"]) : "tr";
}


$languageModel =new Language($db,$languageCode);
$languageModel->getTranslations($languageCode);

//gtag('event', 'purchase', {
//    transaction_id: '{$transaction_id}',
//    value: {$value},
//    tax: {$tax},
//    shipping: {$shipping},
//    currency: '{$currency}',
//    coupon: '{$coupon}',
//    items: [
//     {
//      item_id: '{$item_id}',
//      item_name: '{$item_name}',
//      coupon: '{$item_coupon}',
//      discount: {$item_discount},
//      price: {$item_price},
//      quantity: {$item_quantity}
//    }
//   ]
//});


if($action == "submit"){

    $csrfToken = $requestData['csrf_token'] ?? "";

    if(!$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf Login"
        ]);
        exit();
    }

    $cargoAddressID = $requestData['cargoAddressID'];
    $invoiceAddressID = $requestData['invoiceAddressID'];
    $cartItems = $requestData['cartItem'];
    $invoiceName = $requestData['invoiceName'];
    $invoiceTaxOffice = $requestData['invoiceTaxOffice'];
    $invoiceTaxNumber = $requestData['invoiceTaxNumber'];
    $customerNote = $requestData['customerNote'];
    $languageCode = $requestData['languageCode'];

    if(empty($cargoAddressID) || empty($invoiceAddressID)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen fatura ve kargo adresi seçiniz.',
            'cartData' => []
        ]);
        exit();
    }

    if(empty($invoiceName) || empty($invoiceTaxOffice) || empty($invoiceTaxNumber)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen fatura bilgilerinizi eksiksiz doldurunuz.',
            'cartData' => []
        ]);
        exit();
    }

    if(empty($cartItems)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sepetinizde ödeme için seçili ürün bulunmamaktadır.'
        ]);
        exit();
    }

    ################### Genel Fiyat Ayarları ####################

    $priceSettings = $siteConfig['priceSettings'][0];

    ################### Genel Kredi Kartı Kullanılacak Mı ####################

    $generalCreditCardStatus = $priceSettings['kredikarti'];


    ################### Kredi Kartı Aracı Firma Bilgileri ####################
    $bankSettings = $siteConfig['bankSettings'];

    $creditCardStatus = false;

    $creditCardBankName="";
    $creditCardMerchantID="";
    $creditCardMerchantSalt="";
    $creditCardMerchantKey="";
    //Log::write('bankSettings '. json_encode($bankSettings), 'info');
    if($generalCreditCardStatus){

        if(!empty($bankSettings)){
            $creditCardStatus = true;
            foreach ($bankSettings as $bank){
                $creditCardBankName = $bank['name'];
                if($creditCardBankName=="iyzico"){
                    if ($bank['key'] == "apiKey") {
                        $creditCardMerchantKey = $bank['value'];
                    }
                    elseif($bank['key'] == "secretKey"){
                        $creditCardMerchantSalt = $bank['value'];
                    }
                }
                elseif($creditCardBankName=="paytr") {
                    if($bank['key'] == "merchant_id"){
                        $creditCardMerchantID = $bank['value'];
                    }
                    elseif($bank['key'] == "merchant_key"){
                        $creditCardMerchantKey = $bank['value'];
                    }
                    elseif($bank['key'] == "merchant_salt"){
                        $creditCardMerchantSalt = $bank['value'];
                    }
                }
            }
        }
    }


    ################### Genel EFT Kullanılacak Mı ####################

    $generalEftStatus = $priceSettings['havale'];

    ################### EFT Banka Bilgileri ####################

    $eftInfo = $siteConfig['eftInfo'];
    $eftStatus = false;
    if($generalEftStatus){
        if(!empty($eftInfo)){

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


    ################### Session Ödeme Bilgileri ####################

    $paymentData =[
        "creditCardStatus"=>$creditCardStatus,
        "eftStatus"=>$eftStatus,
        "payAtTheDoorStatus"=>$generalPayAtTheDoorStatus,
        "eftInfo"=>$eftInfo,
        "creditCardBankName" => $creditCardBankName
    ];

    ################### Seçilen Sepet Bilgileri ####################

    include_once MODEL.'Cart.php';
    $cartModel = new Cart( $db, $helper, $session, $config);

    $productCurrencyID = $priceSettings['parabirim'];

    $orderProductIds = [];
    $orderProductNames = [];
    $orderProductStockCodes = [];
    $orderProductCategories = [];
    $orderProductPrices = [];
    $orderProductQuantities = [];

    ################### Seçilen ürün Kategori Bilgileri ####################

    include_once MODEL.'Category.php';
    $categoryModel = new Category($db,$json);

    $orderTotalPrice = 0;
    $orderTotalPriceWithoutTax = 0;
    $orderTotalPriceWithDiscount = 0;
    $orderTotalDiscount = 0;
    $orderTotalTax = 0;

    $configPriceSettings = $siteConfig["priceSettings"][0];
    $configPriceUnit = $configPriceSettings["parabirim"];

    // Yerli firmalar döviz alış verişine izin vermediğinden sitede ürünleri gösterdiğimiz para birim ne olursa olsun TL'ye çeviriyoruz.
    $configPriceUnit=1;

    $currencyRates = $casper->getSiteConfig()["currencyRates"];
    $usdToTry = $currencyRates["usd"];
    $eurToTry = $currencyRates["euro"];

    $gTagItems = [];
    //sepetten sipariş için ürün bilgilerini alalım
    foreach ($cartItems as $i => $cartUniqID){

        $cartInfo = $cartModel->getCartByUniqID($cartUniqID);

        $cartProductPrice = $cartInfo['urunfiyat'];

        $productCurrencyID = $cartInfo['urunparabirim'];

        switch ($productCurrencyID) {
            case 2: // USD to TRY
                $cartProductPrice = $cartProductPrice * $usdToTry;
                $productCurrencyID = 1;
                break;
            case 3: // EUR to TRY
                $cartProductPrice = $cartProductPrice * $eurToTry;
                $productCurrencyID = 1;
                break;
            default: // TRY ise değişiklik yapmıyoruz
                break;
        }

        $cartProductPrice = number_format($cartProductPrice, 2, '.', '');

        $cartInfo['urunfiyat'] = $cartProductPrice;
        $cartInfo['urunparabirim'] = $productCurrencyID;


        array_push($orderProductIds, $cartInfo['urunid']);
        array_push($orderProductNames, $cartInfo['sayfaad']);
        array_push($orderProductStockCodes, $cartInfo['urunstokkodu']);

        $categoryName = $categoryModel->getCategoryByPageID($cartInfo['urunid'],"")['kategoriad'] ?? "Kategori Alınamadı";
        array_push($orderProductCategories, $categoryName);

        array_push($orderProductPrices, $cartProductPrice);
        array_push($orderProductQuantities, $cartInfo['urunadet']);

        $cartItems[$i] = $cartInfo;

        //sepet toplam fiyatı
        $cartTotalPrice = $cartInfo['urunfiyat'] * $cartInfo['urunadet'];

        //sepet toplam kdvsiz fiyat
        $cartTotalPriceWithoutTax = $cartTotalPrice / (1 + $cartInfo['urunkdv']);

        //sipariş toplam kdvsiz fiyat
        $orderTotalPriceWithoutTax += $cartTotalPriceWithoutTax;

        $cartTotalTax = $cartTotalPrice - $cartTotalPriceWithoutTax;
        //sipariş toplam kdv
        $orderTotalTax += $cartTotalTax;

        //sepet indirim miktarı
        $cartDiscount = $cartInfo['indirimmiktari'];

        //sipariş toplam indirim
        $orderTotalDiscount += $cartDiscount;

        //sipariş toplam fiyat
        $orderTotalPrice += $cartTotalPrice;

        $gTagItems[] = [
            "item_id" => $cartInfo['urunstokkodu'],
            "item_name" => $cartInfo['sayfaad'],
            "coupon" => $cartInfo['indirimaciklamasi'],
            "discount" => $cartDiscount,
            "price" => $cartInfo['urunfiyat'],
            "quantity" => $cartInfo['urunadet']
        ];
    }

    //sipariş toplam indirimli fiyat
    $orderTotalPriceWithDiscount = $orderTotalPrice - $orderTotalDiscount;

    //fiyat ayarlarından kredi kartı tek çekim indirim oranı ve banka havalesi indirim oranlarını alalım
    $creditCardDiscountRate = $priceSettings['tekcekim_indirim_orani'];

    //sipariş tek çekim indirimli fiyat
    $orderTotalPriceWithCreditCardDiscount = $orderTotalPrice * (1 - $creditCardDiscountRate);

    $eftDiscountRate = $priceSettings['havale_indirim_orani'];

    //sipariş banka havalesi indirimli fiyat
    $orderTotalPriceWithEftDiscount = $orderTotalPrice * (1 - $eftDiscountRate);

    $orderTotalPrice = number_format($orderTotalPrice, 2, '.', '');
    $orderTotalPriceWithDiscount = number_format($orderTotalPriceWithDiscount, 2, '.', '');
    $orderTotalTax = number_format($orderTotalTax, 2, '.', '');
    $orderTotalPriceWithoutTax = number_format($orderTotalPriceWithoutTax, 2, '.', '');
    $orderTotalPriceWithCreditCardDiscount = number_format($orderTotalPriceWithCreditCardDiscount, 2, '.', '');
    $orderTotalPriceWithEftDiscount = number_format($orderTotalPriceWithEftDiscount, 2, '.', '');

    ################### Seçilen ürün Para Birimi Bilgileri ####################

    include_once MODEL.'Currency.php';
    $currencyModel = new Currency($db);

    $currencyCode = $currencyModel->getCurrencySymbolOrCode($productCurrencyID, 'code');

    $paymentData['currencyCode'] = $currencyCode;

    $orderData = [
        "cargoAddressID"=>$cargoAddressID,
        "invoiceAddressID"=>$invoiceAddressID,
        "cartItems"=>$cartItems,
        "paymentData"=>$paymentData,
        "token"=>""
    ];

    ################### Kargo Adresi Bilgileri ####################

    $visitorAddresses = $visitor['visitorIsMember']['memberAddress'] ?? [];

    $cargoRecipientEmail = $visitor['visitorIsMember']['memberEmail'];
    $cargoAddress = "";
    $cargoRecipientName = "";
    $cargoRecipientSurname = "";
    $cargoRecipientTelephone = "";
    $cargoRecipientIdentificationNumber = "";
    $cargoCountry = "";
    $cargoCity = "";
    $cargoCounty = "";
    $cargoArea = "";
    $cargoNeighborhood = "";
    $cargoPostCode = "";
    $cargoCountryCode = "";

    foreach ($visitorAddresses as $address){
        if($address['adresid'] == $cargoAddressID){
            $cargoAddress = $address['adresacik'];
            $cargoRecipientName = $address['adresad'];
            $cargoRecipientSurname = $address['adressoyad'];
            $cargoRecipientTelephone = $address['adrestelefon'];
            $cargoRecipientIdentificationNumber = $address['adrestcno'];
            $cargoCountry = $address['adresulke'];
            $cargoCity = $address['adressehir'];
            $cargoCounty = $address['adresilce'];
            $cargoArea = $address['adressemt'];
            $cargoNeighborhood = $address['adresmahalle'];
            $cargoPostCode = $address['postakod'];
            $cargoCountryCode = $address['adresulkekod'];
        }
    }

    ################### Fatura Adresi Bilgileri ####################

    $invoiceRecipientEmail = $visitor['visitorIsMember']['memberEmail'];
    $invoiceAddress = "";
    $invoiceRecipientName = "";
    $invoiceRecipientSurname = "";
    $invoiceRecipientTelephone = "";
    $invoiceRecipientIdentificationNumber = "";
    $invoiceCountry = "";
    $invoiceCity = "";
    $invoiceCounty = "";
    $invoiceArea = "";
    $invoiceNeighborhood = "";
    $invoicePostCode = "";
    $invoiceCountryCode = "";

    foreach ($visitorAddresses as $address){
        if($address['adresid'] == $invoiceAddressID){
            $invoiceAddress = $address['adresacik'];
            $invoiceRecipientName = $address['adresad'];
            $invoiceRecipientSurname = $address['adressoyad'];
            $invoiceRecipientTelephone = $address['adrestelefon'];
            $invoiceRecipientIdentificationNumber = $address['adrestcno'];
            $invoiceCountry = $address['adresulke'];
            $invoiceCity = $address['adressehir'];
            $invoiceCounty = $address['adresilce'];
            $invoiceArea = $address['adressemt'];
            $invoiceNeighborhood = $address['adresmahalle'];
            $invoicePostCode = $address['postakod'];
            $invoiceCountryCode = $address['adresulkekod'];
        }
    }

    ################### Add Order to Database ####################

    include_once MODEL.'Order.php';
    $orderModel = new Order($db,$session);

    //bensersiz sipariş id oluştur
    $orderUniqID = $orderModel->createOrderUniqID();
    $orderData['orderUniqID'] = $orderUniqID;

    $orderProductIds = implode(",",$orderProductIds);
    $orderProductNames = implode("||",$orderProductNames);
    $orderProductStockCodes = implode("||",$orderProductStockCodes);
    $orderProductCategories = implode("||",$orderProductCategories);
    $orderProductPrices = implode("||",$orderProductPrices);
    $orderProductQuantities = implode("||",$orderProductQuantities);

    $email = $visitor['visitorIsMember']['memberEmail'];
    $ip = $visitor['visitorIP'];

    $orderAddData =[
        "uyeid"=>$visitor['visitorIsMember']['memberID'],
        "siparisbenzersizid"=>$orderUniqID,
        "siparistariholustur"=>date("Y-m-d H:i:s"),
        "siparistarihguncelle"=>date("Y-m-d H:i:s"),
        "siparisodemeparabirim"=>$currencyCode,
        "siparisodemetaksit"=>1,
        "siparisurunidler"=>$orderProductIds,
        "siparisurunadlar"=>$orderProductNames,
        "siparisurunstokkodlar"=>$orderProductStockCodes,
        "siparisurunkategoriler"=>$orderProductCategories,
        "siparisurunfiyatlar"=>$orderProductPrices,
        "siparisurunadetler"=>$orderProductQuantities,
        "siparisteslimatad"=>$cargoRecipientName,
        "siparisteslimatsoyad"=>$cargoRecipientSurname,
        "siparisteslimateposta"=>$visitor['visitorIsMember']['memberEmail'],
        "siparisteslimatgsm"=>$cargoRecipientTelephone,
        "siparisteslimattcno"=>$cargoRecipientIdentificationNumber,
        "siparisteslimatadresulke"=>$cargoCountry,
        "siparisteslimatadressehir"=>$cargoCity,
        "siparisteslimatadresilce"=>$cargoCounty,
        "siparisteslimatadressemt"=>$cargoArea,
        "siparisteslimatadresmahalle"=>$cargoNeighborhood,
        "siparisteslimatadrespostakod"=>$cargoPostCode,
        "siparisteslimatadresacik"=>$cargoAddress,
        "siparisteslimatadresulkekod"=>$cargoCountryCode,
        "siparisfaturaunvan"=>$invoiceName,
        "siparisfaturavergidairesi"=>$invoiceTaxOffice,
        "siparisfaturavergino"=>$invoiceTaxNumber,
        "siparisfaturaad"=>$invoiceRecipientName,
        "siparisfaturasoyad"=>$invoiceRecipientSurname,
        "siparisfaturaeposta"=>$email,
        "siparisfaturagsm"=>$invoiceRecipientTelephone,
        "siparisfaturaadresulke"=>$invoiceCountry,
        "siparisfaturaadressehir"=>$invoiceCity,
        "siparisfaturaadresilce"=>$invoiceCounty,
        "siparisfaturaadressemt"=>$invoiceArea,
        "siparisfaturaadresmahalle"=>$invoiceNeighborhood,
        "siparisfaturapostakod"=>$invoicePostCode,
        "siparisfaturaadresacik"=>$invoiceAddress,
        "siparisfaturaadresulkekod"=>$invoiceCountryCode,
        "kargoid"=>0,
        "sipariskargofiyat"=>0,
        "sipariskargotarih"=>null,
        "sipariskargoserino"=>"",
        "sipariskargodurum"=>"",
        "sipariskargotakip"=>"",
        "siparisteslimatid"=>"",
        "siparisnotalici"=>$customerNote,
        "siparisnotyonetici"=>"",
        "siparistoplamtutar"=>$orderTotalPrice,
        "sipariskdvtutar"=>$orderTotalTax,
        "sipariskdvsiztutar"=>$orderTotalPriceWithoutTax,
        "sipariskargodahilfiyat"=>$orderTotalPrice,
        "siparistekcekimindirimorani"=>$creditCardDiscountRate,
        "siparistekcekimindirimlifiyat"=>$orderTotalPriceWithCreditCardDiscount,
        "siparishavaorani"=>$eftDiscountRate,
        "siparishavaleindirimlifiyat"=>$orderTotalPriceWithEftDiscount,
        "sipariskargoindirim"=>0,
        "sipariskargoindirimaciklama"=>"",
        "siparispuanindirim"=>0,
        "siparispuanonceki"=>0,
        "siparispuanharcanan"=>0,
        "siparispuankazanilan"=>100,
        "siparispuankalan"=>100,
        "siparisodemeyontemi"=>"",
        "siparisodemedurum"=>0,
        "siparisdurum"=>6,
        "siparisip"=>$ip,
        "siparisdekont"=>"",
        "kargoCode"=>"",
        "siparisKargoBarcode"=>"",
        "tempBarcodeNumber"=>"",
        "siparissevkiyatyapildi"=>0,
        "kargokod"=>"",
        "siparissil"=>0,
        "languageCode"=>$languageCode
    ];

    $orderAddStatus = $orderModel->createOrder($orderAddData);
    if(!$orderAddStatus){
        echo json_encode([
            'status' => 'error',
            'message' => 'Sipariş oluşturulurken bir hata oluştu. Lütfen tekrar deneyiniz.',
            'cartData' => []
        ]);
        exit();
    }
    //orderData ile addOrderData birleştirelim
    $orderData = array_merge($orderData,$orderAddData);

    //ziyaretçi seçilen sepetini orderUniqID ile güncelleyelim

    foreach ($cartItems as $cartItem){
        $cartModel->updateCartByCartUniqID($cartItem['sepetbenzersiz'], ['siparisbenzersiz'=>$orderUniqID]);
    }

    $orderReturnUrl = $config->http.$config->hostDomain.$paymentLink;
    $orderData['orderReturnUrl'] = $orderReturnUrl;

    $cargoAddress = $cargoNeighborhood." ".$cargoAddress." ".$cargoArea." ".$cargoPostCode." ".$cargoCounty." ".$cargoCity." ".$cargoCountry;
    $orderData['cargoAddress'] = $cargoAddress;

    $orderData['languageCode'] = $languageCode;

    $orderData['currencyCode'] = $currencyCode;

    //ödeme kuruluşu bir sipariş id ile bir kere işlem yapıyor, daha önce işlem yapılmışsa yeni sipariş id oluşturalım
    if(!empty($bankSettings) && $creditCardStatus) {
        if($creditCardBankName=="paytr") {

            include_once MODEL . 'Payment/PayTR.php';
            $payTR = new PayTR($creditCardMerchantID, $creditCardMerchantKey, $creditCardMerchantSalt);
            $checkPaymentStatus = $payTR->checkPaymentStatus($orderUniqID);

            if($checkPaymentStatus['status']=="success"){
                $orderUniqID = $orderModel->createOrderUniqID();
                $orderData['orderUniqID'] = $orderUniqID;
            }

            $token = $payTR->sendPaymentRequest($visitor,$orderData);

            if($token!=false){
                $orderData['token'] = $token;
            }
        }
        elseif($creditCardBankName == "iyzico"){
            require_once(ROOT. 'vendor/iyzico/iyzipay-php/IyzipayBootstrap.php');
            IyzipayBootstrap::init();
            $options = new \Iyzipay\Options();
            $options->setApiKey($creditCardMerchantKey);
            $options->setSecretKey($creditCardMerchantSalt);
            $options->setBaseUrl("https://api.iyzipay.com");

            $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId($orderUniqID);
            $request->setPrice($orderTotalPrice);
            $request->setPaidPrice($orderTotalPrice);
            $request->setCurrency(\Iyzipay\Model\Currency::TL);

            $request->setCallbackUrl($orderReturnUrl.'?paymentResult=iyzico');
            $request->setEnabledInstallments(array(2, 3, 6, 9, 12));

            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId($visitor['visitorIsMember']['memberID']);
            $buyer->setName($cargoRecipientName);
            $buyer->setSurname($cargoRecipientSurname);
            $buyer->setGsmNumber($cargoRecipientTelephone);
            $buyer->setEmail($visitor['visitorIsMember']['memberEmail']);
            $buyer->setIdentityNumber($cargoRecipientIdentificationNumber);
            $buyer->setRegistrationAddress($invoiceAddress);
            $buyer->setIp($ip);
            $buyer->setCity($cargoCity);
            $buyer->setCountry($cargoCountry);
            $buyer->setZipCode($cargoPostCode);
            $request->setBuyer($buyer);

            $shippingAddress = new \Iyzipay\Model\Address();
            $shippingAddress->setContactName($cargoRecipientName . ' ' . $cargoRecipientSurname);
            $shippingAddress->setCity($cargoCity);
            $shippingAddress->setCountry($cargoCountry);
            $shippingAddress->setAddress($cargoAddress);
            $shippingAddress->setZipCode($cargoPostCode);
            $request->setShippingAddress($shippingAddress);

            $billingAddress = new \Iyzipay\Model\Address();
            $billingAddress->setContactName($invoiceRecipientName . ' ' . $invoiceRecipientSurname);
            $billingAddress->setCity($invoiceCity);
            $billingAddress->setCountry($invoiceCountry);
            $billingAddress->setAddress($invoiceAddress);
            $billingAddress->setZipCode($invoicePostCode);
            $request->setBillingAddress($billingAddress);

            $orderProductIds = explode(",",$orderProductIds);
            $orderProductNames = explode("||",$orderProductNames);
            $orderProductStockCodes = explode("||",$orderProductStockCodes);
            $orderProductCategories = explode("||",$orderProductCategories);
            $orderProductPrices = explode("||",$orderProductPrices);
            $orderProductQuantities = explode("||",$orderProductQuantities);

            $basketItems = array();

            foreach ($orderProductIds as $i => $orderProductId){
                $basketItem = new \Iyzipay\Model\BasketItem();
                $basketItem->setId($orderProductStockCodes[$i]);
                $basketItem->setName($orderProductNames[$i]);
                $basketItem->setCategory1($orderProductCategories[$i]);
                $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                $basketItem->setPrice($orderProductPrices[$i]);
                $basketItems[$i] = $basketItem;
            }
            $request->setBasketItems($basketItems);

            $payment = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
            if($payment->getStatus()=="failure")
            {
                Log::write("Iyzico Ödeme Oluşturma Hatası: " .$payment->getErrorMessage(), "error");
                echo json_encode([
                    'status' => 'error',
                    'message' => $payment->getErrorMessage()
                ]);
                exit();
            }
            else
            {
                $token = $payment->getToken();
                Log::write("ıyzico Token: " .$token, "info");
                $orderData['checkoutFormContent'] = $payment->getCheckoutFormContent();
                $orderData['token']=$token;
            }
        }
    }

    ################### Add Order to Session ####################

    $gTag = [
        "transaction_id" => $orderUniqID,
        "value" => $orderTotalPrice,
        "tax" => $orderTotalTax,
        "shipping" => 0,
        "currency" => $currencyCode,
        "coupon" => "",
        "items" => $gTagItems
    ];

    $session->addSession("gTag",$gTag);

    $session->addSession("orderData",$orderData);

    echo json_encode([
        'status' => 'success',
        'message' => 'Sipariş bilgileriniz işleniyor.'
    ]);
    exit();
}
elseif($action == "resumeOrder"){

    $orderUniqID = $requestData['orderUniqID'];

    $location = new Location($db);

    include_once MODEL.'Order.php';
    $orderModel = new Order($db,$session);

    $orderData = $orderModel->getOrderByOrderUniqID($orderUniqID)[0];

    if(empty($orderData)){
        $session->addSession('popup', [
            'status' => 'error',
            'message' => "Sipariş bilgileri alınamadı.",
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);

        header('Location: '.$memberLink.'?orders');exit();
    }

    $orderStatus = $orderData['siparisdurum'];
    if($orderStatus != 6){
        $session->addSession('popup', [
            'status' => 'error',
            'message' => "Sipariş bilgileri alınamadı.",
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);

        header('Location: '.$memberLink.'?orders');exit();
    }

    $orderData['orderUniqID'] = $orderUniqID;
    $orderData['orderReturnUrl'] = $config->http.$config->hostDomain.$paymentLink;

    $orderData['cartItems'] = [];
    include_once MODEL.'Cart.php';
    $cartModel = new Cart( $db, $helper, $session, $config);
    $cartItems = $cartModel->getCartUniqIDByOrderUniqID($orderUniqID);

    if(empty($cartItems)){
        $session->addSession('popup', [
            'status' => 'error',
            'message' => "Siparişe ait ürün bilgileri alınamadı.<br>Sipariş tamamlanamaz",
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);
        //siparisil=1 yapalım
        $orderModel->deleteOrderByUniqID($orderUniqID);

        header('Location: '.$memberLink.'?orders');exit();
    }
    foreach ($cartItems as $i => $cartItem){
        $cartUniqID = $cartItem['sepetbenzersiz'];
        $cartInfo = $cartModel->getCartByUniqID($cartUniqID);
        $cartItems[$i] = $cartInfo;
    }

    $orderData['cartItems'] = $cartItems;

    $cargoNeighborhood = $location->getNeighborhoodNameById($orderData['siparisteslimatadresmahalle']);
    $cargoAddress = $orderData['siparisteslimatadresacik'];
    $cargoArea =  $location->getAreaNameById($orderData['siparisteslimatadressemt']);
    $cargoPostCode =  $orderData['siparisteslimatadrespostakod'];
    $cargoCounty =  $location->getCountyNameById($orderData['siparisteslimatadresilce']);
    $cargoCity =  $location->getCityNameById($orderData['siparisteslimatadressehir']);
    $cargoCountry =  $location->getCountryNameById($orderData['siparisteslimatadresulke']);

    $cargoAddress = $cargoNeighborhood." ".$cargoAddress." ".$cargoArea." ".$cargoPostCode." ".$cargoCounty." ".$cargoCity." ".$cargoCountry;
    $orderData['cargoAddress'] = $cargoAddress;

    ################### Genel Fiyat Ayarları ####################

    $priceSettings = $siteConfig['priceSettings'][0];

    ################### Genel Kredi Kartı Kullanılacak Mı ####################

    $generalCreditCardStatus = $priceSettings['kredikarti'];


    ################### Kredi Kartı Aracı Firma Bilgileri ####################

    $creditCardStatus = false;
    $bankSettings = $siteConfig['bankSettings'];
    $creditCardBankName="";
    $creditCardMerchantID="";
    $creditCardMerchantSalt="";
    $creditCardMerchantKey="";
    //Log::write('bankSettings '. json_encode($bankSettings), 'info');
    if($generalCreditCardStatus){

        if(!empty($bankSettings)){
            $creditCardStatus = true;
            foreach ($bankSettings as $bank){
                $creditCardBankName = $bank['name'];

                if($creditCardBankName=="iyzico"){
                    if ($bank['key'] == "apiKey") {
                        $creditCardMerchantKey = $bank['value'];
                    }
                    elseif($bank['key'] == "secretKey"){
                        $creditCardMerchantSalt = $bank['value'];
                    }
                }
                elseif($creditCardBankName=="paytr") {
                    if($bank['key'] == "merchant_id"){
                        $creditCardMerchantID = $bank['value'];
                    }
                    elseif($bank['key'] == "merchant_key"){
                        $creditCardMerchantKey = $bank['value'];
                    }
                    elseif($bank['key'] == "merchant_salt"){
                        $creditCardMerchantSalt = $bank['value'];
                    }
                }
            }
        }
    }

    ################### Genel EFT Kullanılacak Mı ####################

    $generalEftStatus = $priceSettings['havale'];

    ################### EFT Banka Bilgileri ####################

    $eftInfo = $siteConfig['eftInfo'];
    $eftStatus = false;
    if($generalEftStatus){
        if(!empty($eftInfo)){
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

    ################### Session Ödeme Bilgileri ####################

    $paymentData =[
        "creditCardStatus"=>$creditCardStatus,
        "eftStatus"=>$eftStatus,
        "payAtTheDoorStatus"=>$generalPayAtTheDoorStatus,
        "eftInfo"=>$eftInfo
    ];

    $orderData['token'] = "";
    if(!empty($bankSettings) && $creditCardStatus) {
        $paymentData['creditCardBankName'] = $creditCardBankName;
        /*$paymentData['creditCardMerchantID'] = $creditCardMerchantID;
        $paymentData['creditCardMerchantKey'] = $creditCardMerchantKey;
        $paymentData['creditCardMerchantSalt'] = $creditCardMerchantSalt;*/

        if($creditCardBankName=="paytr") {

            include_once MODEL . 'Payment/PayTR.php';
            $payTR = new PayTR($creditCardMerchantID, $creditCardMerchantKey, $creditCardMerchantSalt);
            $checkPaymentStatus = $payTR->checkPaymentStatus($orderUniqID);

            if($checkPaymentStatus['status']=="success"){
                $orderUniqID = $orderModel->createOrderUniqID();
                $orderData['orderUniqID'] = $orderUniqID;
            }

            $token = $payTR->sendPaymentRequest($visitor,$orderData);

            if($token!=false){
                $orderData['token'] = $token;
            }
        }
        elseif($creditCardBankName == "iyzico"){
            require_once(ROOT. 'vendor/iyzico/iyzipay-php/IyzipayBootstrap.php');
            IyzipayBootstrap::init();
            $options = new \Iyzipay\Options();
            $options->setApiKey($creditCardMerchantKey);
            $options->setSecretKey($creditCardMerchantSalt);
            $options->setBaseUrl("https://api.iyzipay.com");

            $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId($orderUniqID);
            $request->setPrice($orderData['siparistoplamtutar']);
            $request->setPaidPrice($orderData['siparistoplamtutar']);
            $request->setCurrency(\Iyzipay\Model\Currency::TL);

            $request->setCallbackUrl($orderData['returnUrl'].'?paymentResult=iyzico');
            $request->setEnabledInstallments(array(2, 3, 6, 9, 12));

            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId($visitor['visitorIsMember']['memberID']);
            $buyer->setName($visitor['visitorIsMember']['memberName']);
            $buyer->setSurname($visitor['visitorIsMember']['memberSurname']);;
            $buyer->setGsmNumber($visitor['visitorIsMember']['memberPhone']);
            $buyer->setEmail($visitor['visitorIsMember']['memberEmail']);
            $buyer->setIdentityNumber($visitor['visitorIsMember']['memberIdentityNumber']);;
            $buyer->setRegistrationAddress($cargoAddress);
            $buyer->setIp($visitor['visitorIP']);
            $buyer->setCity($cargoCity);
            $buyer->setCountry($cargoCountry);
            $buyer->setZipCode($cargoPostCode);
            $request->setBuyer($buyer);

            $shippingAddress = new \Iyzipay\Model\Address();
            $shippingAddress->setContactName($orderData['siparisteslimatad'] . ' ' . $orderData['siparisteslimatsoyad']);
            $shippingAddress->setCity($cargoCity);
            $shippingAddress->setCountry($cargoCountry);
            $shippingAddress->setAddress($cargoAddress);
            $shippingAddress->setZipCode($cargoPostCode);
            $request->setShippingAddress($shippingAddress);

            $billingAddress = new \Iyzipay\Model\Address();
            $billingAddress->setContactName($orderData['siparisfaturaad'] . ' ' . $orderData['siparisfaturasoyad']);
            $billingAddress->setCity($orderData['siparisfaturaadressehir']);;
            $billingAddress->setCountry($orderData['siparisfaturaadresulke']);
            $billingAddress->setAddress($orderData['siparisfaturaadresacik']);
            $billingAddress->setZipCode($orderData['siparisfaturaadrespostakod']);
            $request->setBillingAddress($billingAddress);

            $basketItems = array();

            include_once MODEL.'Product.php';
            $productModel = new Product($db,$config->json);

            foreach ($orderData['cartItems'] as $i => $cartItem){
                $productID = $cartItem['urunid'];
                $product = $productModel->getProductByID($productID)[0];
                $productName = $product['sayfaad'];
                $productCategory = $product['kategoriad'];
                $basketItem = new \Iyzipay\Model\BasketItem();
                $basketItem->setId($cartItem['urunstokkodu']);
                $basketItem->setName($productName);
                $basketItem->setCategory1($productCategory);
                $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                $basketItem->setPrice($cartItem['urunfiyat']);
                $basketItems[$i] = $basketItem;
            }
            $request->setBasketItems($basketItems);

            $payment = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
            if($payment->getStatus()=="failure")
            {
                Log::write("Iyzico Ödeme Oluşturma Hatası: " .$payment->getErrorMessage(), "error");
                echo json_encode([
                    'status' => 'error',
                    'message' => $payment->getErrorMessage()
                ]);
                exit();
            }
            else
            {
                $token = $payment->getToken();
                $orderData['checkoutFormContent'] = $payment->getCheckoutFormContent();
                $orderData['token']=$token;
            }
        }
    }

    $orderData['paymentData'] = $paymentData;

    $session->addSession("orderData",$orderData);
    header('Location: '.$paymentLink);exit();
}
