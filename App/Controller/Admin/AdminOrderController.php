<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */

$action = $requestData['action'] ?? '';

if(empty($action)){
    echo json_encode(['status' => 'error', 'message' => 'Action is required']);
    exit();
}

include_once MODEL."Admin/AdminSiteConfig.php";
$siteConfig = new AdminSiteConfig($db, 1);
$siteConfig = $siteConfig->getSiteConfig();
$bankSettings = $siteConfig['bankSettings'];

include_once MODEL."Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

if (!empty($bankSettings)) {

    $creditCardBankName = $bankSettings[0]['ayarbankaad'];
    $creditCardMerchantID = $bankSettings[0]['magazaid'];
    $creditCardMerchantKey = $bankSettings[0]['magazaparola'];
    $creditCardMerchantSalt = $bankSettings[0]['magazaanahtar'];

    if ($creditCardBankName == "paytr") {
        include_once MODEL . 'Payment/PayTR.php';
        $payTR = new PayTR($creditCardMerchantID, $creditCardMerchantKey, $creditCardMerchantSalt);
    }

}

if( $action == "checkPaymentStatus" ) {

    $orderUniqID = $requestData['orderUniqID'] ?? '';

    if(empty($orderUniqID)){
        echo json_encode(['status' => 'error', 'message' => 'Order Uniq ID is required']);
        exit();
    }


    if ($creditCardBankName == "paytr") {
        $checkPaymentStatus = $payTR->checkPaymentStatus($orderUniqID);
        $paymentStatus = $checkPaymentStatus['status'] ?? "";

        if ($paymentStatus == "success") {
            $paymentTotal = $checkPaymentStatus["payment_total"];
            echo json_encode([
                'status' => 'success',
                'message' => 'Ödeme Başarılı',
                'paymentTotal' => $paymentTotal]);
        }
        else{
            echo json_encode([
                'status' => 'error',
                'message' => $checkPaymentStatus['err_no'] . '-' .$checkPaymentStatus['err_msg']
            ]);
        }
    }
}
elseif( $action == "updateOrderStatus" ){

    $orderUniqID = $requestData['orderUniqID'] ?? '';
    $orderStatus = $requestData['orderStatus'] ?? '';
    $sendEmail = $requestData['sendEmail'] ?? 0;


    if(empty($orderUniqID)){
        echo json_encode(['status' => 'error', 'message' => 'Order Uniq ID is required']);
        exit();
    }

    if(!is_numeric($orderStatus)){
        echo json_encode(['status' => 'error', 'message' => 'Order Status is required']);
        exit();
    }

    include_once MODEL."Admin/AdminOrder.php";
    $adminOrder = new AdminOrder($db,$config);

    $adminOrder->beginTransaction("update order status");
    $updateOrderStatus = $adminOrder->updateOrderStatusByUniqID($orderUniqID,$orderStatus);
    Log::adminWrite("update order status result $updateOrderStatus");
    if($updateOrderStatus >= 0){
        $adminOrder->commit("update order status");

        $returnMessage="Sipariş durumu güncellendi. ";
        if($sendEmail == 1){
            $orderInfo = $adminOrder->getOrderByOrderUniqID($orderUniqID);

            $orderNameSurname = $orderInfo['siparisteslimatad']." ". $orderInfo['siparisteslimatsoyad'];
            $orderEmail = $orderInfo['siparisteslimateposta'];
            $orderStatus = $orderInfo['siparisdurum'];
            $orderLanguageCode = $orderInfo['languageCode'];
            $adminLanguage->getTranslations($orderLanguageCode);

            $orderStatus = $adminOrder->getOrderStatus($orderStatus);
            $orderStatusName = $orderStatus['siparisdurumbaslik'];

            $languageID = $adminLanguage->getLanguageID($orderLanguageCode);

            include_once Helpers."EmailSender.php";
            $emailSender = new EmailSender();

            $siteConfig = new AdminSiteConfig($db,$languageID);
            $siteConfig = $siteConfig->siteConfig;

            $companyInfo = $siteConfig['companySettings'];
            $companyName = $companyInfo['ayarfirmakisaad'];
            $companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
            $companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
            $companyEmail = $companyInfo['ayarfirmaeposta'];

            $logoInfo = $siteConfig['logoSettings'];
            $logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

            $myOrderLink = $config->http.$config->hostDomain."/?/control/member/get/orders";

            $emailSubject = "$companyName - Siparişinizin Durumu Değişti";

            $emailTemplate = file_get_contents(Helpers.'mail-template/orderUpdateStatus.php');

            $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
            $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
            $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
            $emailTemplate = str_replace("[my-order-link]", $myOrderLink, $emailTemplate);
            $emailTemplate = str_replace("[member-name-surname]", $orderNameSurname, $emailTemplate);
            $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
            $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
            $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);
            $emailTemplate = str_replace("[orderUniqID]", $orderUniqID, $emailTemplate);
            $emailTemplate = str_replace("[_order_siparis_durum_yazi]", "Sipariş durumunuz $orderStatusName olarak güncellendi", $emailTemplate);
            $emailTemplate = str_replace("[_uyelik_mesajsiparisno_yazi]", _odeme_siparis_no, $emailTemplate);
            $emailTemplate = str_replace("[_uyelik_siparislerim_yazi]", _odeme_siparislerim, $emailTemplate);

            $emailSendResult = $emailSender->sendEmail($orderEmail,$orderNameSurname, $emailSubject, $emailTemplate);
            if($emailSendResult){
                $returnMessage .= "<br>Müşteriye sipariş durum değişikliği postası gönderildi";
            }
            else{
                $returnMessage .= "<br>Müşteriye sipariş durum değişikliği postası gönderilemedi";
            }
        }
        echo json_encode([
            'status' => 'success',
            'message' => $returnMessage
        ]);exit;
    }

    $_SESSION['orders'] = null;
    $adminOrder->rollback("update order status");
    exit(json_encode([
        'status' => 'error',
        'message' => 'Sipariş durumu güncellenemedi'
    ]));
}
elseif( $action == "getOrdersBySearchText" ){

    $searchText = $requestData['q'] ?? '';

    if(empty($searchText)){
        echo json_encode(['status' => 'error', 'message' => 'Search Text is required']);
        exit();
    }

    include_once MODEL."Admin/AdminOrder.php";
    $adminOrder = new AdminOrder($db,$config);

    include_once MODEL."Location.php";
    $location = new Location($db);

    include_once MODEL."Admin/AdminCart.php";
    $adminCart = new AdminCart($db,$config);

    $orders = $adminOrder->getOrdersBySearchText($searchText);

    $newOrders = [];
    foreach ($orders as $order){

        $orderPaymentStatus = $order['siparisodemedurum'];

        switch ($orderPaymentStatus){
            case 0:
                $orderPaymentStatus = "Ödeme Onayı Bekleniyor";
                break;
            case 1:
                $orderPaymentStatus = "ödeme Onaylandı";
                break;
        }

        $orderStatusID = $order['siparisdurum'];

        switch ($orderStatusID){
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
                $cardHeaderStyle = "style-primary";
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

        $orderProductIDs = explode(",", $order['siparisurunidler']);
        $orderProductNames = explode("||", $order['siparisurunadlar']);
        $orderProductStockCodes = $order['siparisurunstokkodlar'] ? explode("||", $order['siparisurunstokkodlar']) : [];
        $orderProductCategories = explode("||", $order['siparisurunkategoriler']);
        $orderProductPrices = explode("||", $order['siparisurunfiyatlar']);
        $orderProductQuantities = explode("||", $order['siparisurunadetler']);

        $orderTotalPrice = $order['siparistoplamtutar'];
        $orderTotalPrice = number_format($orderTotalPrice,2,",",".");

        //sipariş ürünlerini diziye alalım

        $orderProducts = [];

        for ($i = 0; $i < count($orderProductIDs); $i++){
            $orderProductID = $orderProductIDs[$i];
            $orderProductName = $orderProductNames[$i];
            $orderProductName = str_replace(".0000", "", $orderProductName);
            $orderProductStockCode = $orderProductStockCodes[$i] ?? "";
            $orderProductCategory = $orderProductCategories[$i];
            $orderProductPrice = $orderProductPrices[$i];
            $orderProductQuantity = $orderProductQuantities[$i];
            $orderProductQuantity = str_replace(".0000", "", $orderProductQuantity);

            $productVariant = "";
            $orderBasket = $adminCart->getCartByOrderUniqID($orderUniqID,$orderProductStockCode);
            if(!empty($orderBasket)){

                $productVariant = $orderBasket['cartProducts'][0]['productSelectedVariant'] ?? "";
                $productImages = $orderBasket['cartProducts'][0]['productImage'] ?? "";

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

            $productImage = "";
            if(!empty($productImages)){
                $productImage = explode(",",$productImages)[0];
            }

            $productTotalPrice = $orderProductPrice * $orderProductQuantity;
            $productTotalPrice = number_format($productTotalPrice,2,",",".");

            $orderProducts[] = [
                'productID' => $orderProductID,
                'productName' => $orderProductName,
                'productStockCode' => $orderProductStockCode,
                'productCategory' => $orderProductCategory,
                'productPrice' => $orderProductPrice,
                'productQuantity' => $orderProductQuantity,
                'productVariant' => $productVariant,
                'productTotalPrice' => $productTotalPrice,
                'productImage' => $productImage
            ];

        }

        $newOrders[] = [
            'orderUniqID' => $orderUniqID,
            'orderStatusID' => $orderStatusID,
            'orderStatus' => $orderStatus,
            'orderPaymentStatus' => $orderPaymentStatus,
            'orderPaymentType' => $orderPaymentType,
            'orderPaymentIcon' => $orderPaymentIcon,
            'cardHeaderStyle' => $cardHeaderStyle,
            'orderDate' => $orderDate,
            'orderDeliveryName' => $orderDeliveryName,
            'orderDeliverySurname' => $orderDeliverySurname,
            'orderDeliveryEmail' => $orderDeliveryEmail,
            'orderDeliveryGSM' => $orderDeliveryGSM,
            'orderDeliveryTC' => $orderDeliveryTC,
            'orderDeliveryCountry' => $orderDeliveryCountry,
            'orderDeliveryCity' => $orderDeliveryCity,
            'orderDeliveryDistrict' => $orderDeliveryDistrict,
            'orderDeliveryNeighborhood' => $orderDeliveryNeighborhood,
            'orderDeliveryStreet' => $orderDeliveryStreet,
            'orderDeliveryPostalCode' => $orderDeliveryPostalCode,
            'orderDeliveryAddress' => $orderDeliveryAddress,
            'orderInvoiceTitle' => $orderInvoiceTitle,
            'orderInvoiceTaxOffice' => $orderInvoiceTaxOffice,
            'orderInvoiceTaxNumber' => $orderInvoiceTaxNumber,
            'orderInvoiceName' => $orderInvoiceName,
            'orderInvoiceSurname' => $orderInvoiceSurname,
            'orderInvoiceEmail' => $orderInvoiceEmail,
            'orderInvoiceGSM' => $orderInvoiceGSM,
            'orderInvoiceCountry' => $orderInvoiceCountry,
            'orderInvoiceCity' => $orderInvoiceCity,
            'orderInvoiceDistrict' => $orderInvoiceDistrict,
            'orderInvoiceNeighborhood' => $orderInvoiceNeighborhood,
            'orderInvoiceStreet' => $orderInvoiceStreet,
            'orderInvoicePostalCode' => $orderInvoicePostalCode,
            'orderInvoiceAddress' => $orderInvoiceAddress,
            'orderProducts' => $orderProducts,
            'orderTotalPrice' => $orderTotalPrice
        ];
    }

    $orders = $newOrders;

    if($orders){
        echo json_encode(['status' => 'success', 'orders' => $orders]);
    }
    else{
        echo json_encode(['status' => 'error', 'message' => 'No order found']);
    }
}
elseif( $action == "refundPayment" ){

    $orderUniqID = $requestData['orderUniqID'] ?? '';
    $returnAmount = $requestData['returnAmount'] ?? '';
    $referenceNo = $requestData['referenceNo'] ?? '';

    if(empty($orderUniqID)){
        echo json_encode(['status' => 'error', 'message' => 'Order Uniq ID is required']);
        exit();
    }

    if(empty($returnAmount)){
        echo json_encode(['status' => 'error', 'message' => 'Return Amount is required']);
        exit();
    }

    if ($creditCardBankName == "paytr") {

        $refundPayment = $payTR->refundPayment($orderUniqID, $returnAmount, $referenceNo);

        if ($refundPayment['status'] == 'success') {
            echo json_encode([
                'status' => 'success',
                'message' => 'Ödeme İadesi Başarılı'
            ]);
        }
        else{
            echo json_encode([
                'status' => 'error',
                'message' => $refundPayment['err_no'] . '-' .$refundPayment['err_msg']
            ]);
        }
    }

    $_SESSION['orders'] = null;
}
elseif( $action == "addOrder"  || $action == "updateOrder" ){

    $languageID = $requestData['languageID'] ?? 1;

    $language = $adminLanguage->getLanguage($languageID);
    $languageCode = $language['languageCode'];

    include_once MODEL."Admin/AdminLocation.php";
    $adminLocation = new AdminLocation($db);

    include_once MODEL."Admin/AdminProduct.php";
    $adminProduct = new AdminProduct($db,$config);

    $orderUniqID = $requestData['orderUniqID'] ?? '';
    $memberID = $requestData['memberID'] ?? '';
    $cargoID = $requestData['cargoID'] ?? 0;
    if(!is_numeric($cargoID)){
        $cargoID = 0;
    }
    $orderProductList = $requestData['orderProductList'] ?? '';
    $orderDeliveryTC = $requestData['orderDeliveryTC'] ?? '';
    $orderDeliveryName = $requestData['orderDeliveryName'] ?? '';
    $orderDeliverySurname = $requestData['orderDeliverySurname'] ?? '';
    $orderDeliveryPhoneNumber = $requestData['orderDeliveryPhoneNumber'] ?? '';
    $orderDeliveryEmailAddress = $requestData['orderDeliveryEmailAddress'] ?? '';
    $orderDeliveryCountry = $requestData['orderDeliveryCountry'] ?? '';
    $orderDeliveryCity = $requestData['orderDeliveryCity'] ?? '';
    $orderDeliveryDistrict = $requestData['orderDeliveryDistrict'] ?? '';
    $orderDeliveryArea = $requestData['orderDeliveryArea'] ?? '';
    $orderDeliveryNeighborhood = $requestData['orderDeliveryNeighborhood'] ?? '';
    $orderDeliveryAddress = $requestData['orderDeliveryAddress'] ?? '';
    $orderInvoiceTitle = $requestData['orderInvoiceTitle'] ?? '';
    $orderInvoiceTaxOffice = $requestData['orderInvoiceTaxOffice'] ?? '';
    $orderInvoiceTaxNumber = $requestData['orderInvoiceTaxNumber'] ?? '';
    $orderInvoiceName = $requestData['orderInvoiceName'] ?? '';
    $orderInvoiceSurname = $requestData['orderInvoiceSurname'] ?? '';
    $orderInvoicePhoneNumber = $requestData['orderInvoicePhoneNumber'] ?? '';
    $orderInvoiceEmailAddress = $requestData['orderInvoiceEmailAddress'] ?? '';
    $orderInvoiceCountry = $requestData['orderInvoiceCountry'] ?? '';
    $orderInvoiceCity = $requestData['orderInvoiceCity'] ?? '';
    $orderInvoiceDistrict = $requestData['orderInvoiceDistrict'] ?? '';
    $orderInvoiceArea = $requestData['orderInvoiceArea'] ?? '';
    $orderInvoiceNeighborhood = $requestData['orderInvoiceNeighborhood'] ?? '';
    $orderInvoiceAddress = $requestData['orderInvoiceAddress'] ?? '';
    $orderCurrency = $requestData['orderCurrency'] ?? '';
    $orderTotalPrice = $requestData['orderTotalPrice'] ?? '';
    $orderVATPrice = $requestData['orderVATPrice'] ?? '';
    $orderWithoutVATPrice = $requestData['orderWithoutVATPrice'] ?? '';
    $orderCargoPriceIncluded = $requestData['orderCargoPriceIncluded'] ?? '';
    $orderCreditCardSingleChargeDiscountRate = $requestData['orderCreditCardSingleChargeDiscountRate'] ?? '';
    $orderCreditCardSingleChargeDiscountPrice = $requestData['orderCreditCardSingleChargeDiscountPrice'] ?? '';
    $orderBankTransferDiscountRate = $requestData['orderBankTransferDiscountRate'] ?? '';
    $orderBankTransferDiscountPrice = $requestData['orderBankTransferDiscountPrice'] ?? '';
    $orderCargoDiscount = $requestData['orderCargoDiscount'] ?? '';
    $orderCargoDiscountDescription = $requestData['orderCargoDiscountDescription'] ?? '';
    $orderPaymentMethod = $requestData['orderPaymentMethod'] ?? '';
    $orderPaymentStatus = $requestData['orderPaymentStatus'] ?? '';
    $orderStatusID = $requestData['orderStatusID'] ?? '';

    $orderCurrencyCode = $adminProduct->getCurrency($orderCurrency)["parabirimkod"];

    $orderDeliveryPostalCode = $requestData['orderDeliveryPostalCode'] ?? '';
    $orderInvoicePostalCode = $requestData['orderInvoicePostalCode'] ?? '';

    $orderDeliveryCountryCode = $adminLocation->getCountryPhoneCode($orderDeliveryCountry);
    $orderInvoiceCountryCode = $adminLocation->getCountryPhoneCode($orderInvoiceCountry);
    //adres alanları ülke 212 ise boş olamaz değil ise city ve adres alanları boş olamaz
    if($orderDeliveryCountry == 212){
        if(empty($orderDeliveryTC) || empty($orderDeliveryName) || empty($orderDeliverySurname) || empty($orderDeliveryPhoneNumber) || empty($orderDeliveryEmailAddress) || empty($orderDeliveryCountry) || empty($orderDeliveryCity) || empty($orderDeliveryDistrict) || empty($orderDeliveryNeighborhood) || empty($orderDeliveryAddress)){
            echo json_encode(['status' => 'error', 'message' => "
            orderDeliveryTC $orderDeliveryTC,
            orderDeliveryName $orderDeliveryName,
            orderDeliverySurname $orderDeliverySurname,
            orderDeliveryPhoneNumber $orderDeliveryPhoneNumber,
            orderDeliveryEmailAddress $orderDeliveryEmailAddress,
            orderDeliveryCountry $orderDeliveryCountry,
            orderDeliveryCity $orderDeliveryCity,
            orderDeliveryDistrict $orderDeliveryDistrict,
            orderDeliveryNeighborhood $orderDeliveryNeighborhood,
            orderDeliveryAddress $orderDeliveryAddress
            "]);
            exit();
        }

        if(empty($orderDeliveryPostalCode)){
            $orderDeliveryPostalCode = $adminLocation->getPostalCode($orderDeliveryNeighborhood)[0]['ZipCode'];
        }
    }
    else{
        if(empty($orderDeliveryCity) || empty($orderDeliveryAddress)){
            echo json_encode(['status' => 'error', 'message' => 'Delivery Address Fields are required']);
            exit();
        }
    }

    if($orderInvoiceCountry == 212){
        if(empty($orderInvoiceTitle) || empty($orderInvoiceTaxOffice) || empty($orderInvoiceTaxNumber) || empty($orderInvoiceName) || empty($orderInvoiceSurname) || empty($orderInvoicePhoneNumber) || empty($orderInvoiceEmailAddress) || empty($orderInvoiceCountry) || empty($orderInvoiceCity) || empty($orderInvoiceDistrict) || empty($orderInvoiceNeighborhood) || empty($orderInvoiceAddress)){
            echo json_encode(['status' => 'error', 'message' => 'Invoice Address Fields are required']);
            exit();
        }

        $orderInvoicePostalCode = $adminLocation->getPostalCode($orderInvoiceNeighborhood)[0]['ZipCode'];
    }
    else{
        if(empty($orderInvoiceCity) || empty($orderInvoiceAddress)){
            echo json_encode(['status' => 'error', 'message' => 'Invoice Address Fields are required']);
            exit();
        }
    }

    //adres ve fatura ad, soyad, eposta, telefon boş olamaz
    if(empty($orderDeliveryName) || empty($orderDeliverySurname) || empty($orderDeliveryEmailAddress) || empty($orderDeliveryPhoneNumber) || empty($orderInvoiceName) || empty($orderInvoiceSurname) || empty($orderInvoiceEmailAddress) || empty($orderInvoicePhoneNumber)){
        echo json_encode(['status' => 'error', 'message' => 'Name, Surname, Email and Phone Number Fields are required']);
        exit();
    }

    //fatura bilgileri boş olamaz
    if(empty($orderInvoiceTitle) || empty($orderInvoiceTaxOffice) || empty($orderInvoiceTaxNumber)){
        echo json_encode(['status' => 'error', 'message' => 'Invoice Title, Tax Office and Tax Number Fields are required']);
        exit();
    }

    //ürünler boş olamaz
    if(empty($orderProductList)){
        echo json_encode(['status' => 'error', 'message' => 'Product List is required']);
        exit();
    }

    //kalan alanlar boş olamaz
    if(
        empty($orderCurrency) ||
        empty($orderTotalPrice) ||
        $orderVATPrice == "" ||
        empty($orderWithoutVATPrice) ||
        empty($orderCargoPriceIncluded) ||
        $orderCreditCardSingleChargeDiscountRate== "" ||
        empty($orderCreditCardSingleChargeDiscountPrice) ||
        $orderBankTransferDiscountRate == "" ||
        empty($orderBankTransferDiscountPrice) ||
        $orderCargoDiscount == "" ||
        empty($orderPaymentMethod) ||
        $orderStatusID == "")
    {

        echo json_encode(['status' => 'error', 'message' => "
            orderCurrency $orderCurrency,
            orderTotalPrice $orderTotalPrice,
            orderVATPrice $orderVATPrice,
            orderWithoutVATPrice $orderWithoutVATPrice,
            orderCargoPriceIncluded $orderCargoPriceIncluded,
            orderCreditCardSingleChargeDiscountRate $orderCreditCardSingleChargeDiscountRate,
            orderCreditCardSingleChargeDiscountPrice $orderCreditCardSingleChargeDiscountPrice,
            orderBankTransferDiscountRate $orderBankTransferDiscountRate,
            orderBankTransferDiscountPrice $orderBankTransferDiscountPrice,
            orderCargoDiscount $orderCargoDiscount,
            orderPaymentMethod $orderPaymentMethod,
            orderStatusID $orderStatusID
        "]);
        exit();
    }

    //siparisnotyonetici'ye siparişin düzenlendiğine dair not düşelim
    $orderManagerNote = "Sipariş düzenlendi";

    //siparişin sepet bilgilerini çekelim
    include_once MODEL."Admin/AdminCart.php";
    $adminCart = new AdminCart($db,$config);

    include_once MODEL."Admin/AdminMember.php";
    $adminMember = new AdminMember($db);

    $memberInfo = $adminMember->getMemberInfo($memberID,"");

    if(empty($memberInfo)){
        echo json_encode(['status' => 'error', 'message' => 'Member not found ($memberID)']);
        exit();
    }

    $memberUniqID = $memberInfo['memberUniqID'];

    include_once MODEL."Admin/AdminOrder.php";
    $adminOrder = new AdminOrder($db,$config);

    //sipariş ürünlerini döngüye alalım

    $adminOrder->beginTransaction("add/update order");

    foreach ($orderProductList as $orderProduct){

        $productID = $orderProduct['productID'];
        $productStockCode = $orderProduct['stockCode'];
        $productCategory = $orderProduct['category'];
        $productQuantity = $orderProduct['quantity'];
        $productPrice = $orderProduct['price'];
        $productCurrency = $orderProduct['currencyID'];

        $getOrderProduct = $adminProduct->getProductByID($productID);

        if(empty($getOrderProduct)){
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
            exit();
        }
        $getOrderProduct = $getOrderProduct[0];
        $productName = $getOrderProduct['sayfaad'];
        $productTax = $getOrderProduct['urunkdv'];

        $variantProperties = $getOrderProduct['variantProperties'] ?? "";

        $productVariant = "";
        if(!empty($variantProperties)){

            $variantProperties = json_decode($variantProperties,true);

            foreach ($variantProperties as $variantProperty){
                $variantStockCode = $variantProperty['variantStockCode'];

                if($variantStockCode == $productStockCode){
                    $productVariant = $variantProperty['variantProperties'];
                    break;
                }
            }
            $productVariant = json_encode($productVariant);
        }


        $orderProductIDs = empty($orderProductIDs) ? $productID : $orderProductIDs . "," . $productID;
        $orderProductNames = empty($orderProductNames) ? $productName : $orderProductNames . "||" . $productName;
        $orderProductStockCodes = empty($orderProductStockCodes) ? $productStockCode : $orderProductStockCodes . "||" . $productStockCode;
        $orderProductCategories = empty($orderProductCategories) ? $productCategory : $orderProductCategories . "||" . $productCategory;
        $orderProductPrices = empty($orderProductPrices) ? $productPrice : $orderProductPrices . "||" . $productPrice;
        $orderProductQuantities = empty($orderProductQuantities) ? $productQuantity : $orderProductQuantities . "||" . $productQuantity;

        //ürün stok kodu ve ürün id'sine göre sepet bilgilerini çekelim
        $orderCart = $adminCart->getCartByOrderUniqID($orderUniqID,$productStockCode);

        if(!empty($orderCart)){
            foreach ($orderCart['cartProducts'] as $cartProduct){

                $cartUniqID = $cartProduct['cartUniqID'];
                $cartProductID = $cartProduct['productID'];
                $cartProductStockCode = $cartProduct['productSelectedStockCode'];
                $cartProductQuantity = $cartProduct['productQuantity'];

                //sepetteki ürün adeti ile siparişteki ürün adeti aynı değilse, sepet ürün adeti daha çok ise uruniadeadet alanını güncelleyelim
                $returnQuantity = $cartProduct['productReturnQuantity'] ?? 0;
                if($cartProductID == $productID && $cartProductStockCode == $productStockCode && $cartProductQuantity != $productQuantity){
                    if($cartProductQuantity > $productQuantity){
                        $returnQuantity = $returnQuantity + ($cartProductQuantity - $productQuantity);
                    }
                }

                $updateCartData = [
                    'sepetguncelletarih' => date("Y-m-d H:i:s"),
                    'urunid' => $productID,
                    'urunstokkodu' => $productStockCode,
                    'urunadet' => $productQuantity,
                    'uruniadeadet' => $returnQuantity,
                    'urunfiyat' => $productPrice,
                    "urunkdv" => $productTax,
                    "urunvaryant" => $productVariant,
                    "urunparabirim" => $productCurrency
                ];

                $updateCartResult = $adminCart->updateCart($cartUniqID,$updateCartData);

                if($updateCartResult<=0) {
                    $adminOrder->rollback("updateCartError");
                    echo json_encode(['status' => 'error', 'message' => "Sepet Güncelleme Hatası"]);
                    exit();
                }
            }
        }
        else{
            //düzenlenen siparişin ürünleri sepette yok ise yeni sepet ekleyelim
            if(empty($orderUniqID)){
                $orderUniqID = $adminOrder->createOrderUniqID();
            }

            $cartUniqID = $helper->createPassword("20",2);
            $cartDate = date("Y-m-d H:i:s");
            $cartData = [
                'memberUniqID' => $memberUniqID,
                'cartUniqID' => $cartUniqID,
                'cartCreatedDate' => $cartDate,
                'cartUpdatedDate' => $cartDate,
                'productStockCode' => $productStockCode,
                'productID' => $productID,
                'productVariant' => "",
                'productDesi' => 1,
                'productQuantity' => $productQuantity,
                'productCurrencyID' => $productCurrency,
                'productPrice' => $productPrice,
                'productTax' => $productTax,
                'productShippingCost' => 0,
                'productReturnQuantity' => 0,
                'productDiscountAmount' => 0,
                'productDiscountDescription' => "",
                'cartStatus' => 0,
                'paymentStatus' => 0,
                'orderUniqID' => $orderUniqID,
                'cartDeleted' => 0
            ];

            $addCartResul = $adminCart->addCart($cartData);

            if(!$addCartResul){
                $adminOrder->rollback("addCartError");
                echo json_encode(['status' => 'error', 'message' => "Sepet Ekleme Hatası"]);
                exit();
            }
        }

    }

    $orderProductIDs = $orderProductIDs ?? "";
    $orderProductNames = $orderProductNames ?? "";
    $orderProductStockCodes = $orderProductStockCodes ?? "";
    $orderProductCategories = $orderProductCategories ?? "";
    $orderProductPrices = $orderProductPrices ?? "";
    $orderProductQuantities = $orderProductQuantities ?? "";

    $languageCode = strtolower($languageCode);
    $orderData = [
        'uyeid' => $memberID,
        'siparistarihguncelle' => date("Y-m-d H:i:s"),
        'siparisodemeparabirim' => $orderCurrencyCode,
        'siparisodemetaksit' => 1,
        'siparisurunidler' => $orderProductIDs,
        'siparisurunadlar' => $orderProductNames,
        'siparisurunstokkodlar' => $orderProductStockCodes,
        'siparisurunkategoriler' => $orderProductCategories,
        'siparisurunfiyatlar' => $orderProductPrices,
        'siparisurunadetler' => $orderProductQuantities,
        'siparisteslimatad' => $orderDeliveryName,
        'siparisteslimatsoyad' => $orderDeliverySurname,
        'siparisteslimateposta' => $orderDeliveryEmailAddress,
        'siparisteslimatgsm' => $orderDeliveryPhoneNumber,
        'siparisteslimattcno' => $orderDeliveryTC,
        'siparisteslimatadresulke' => $orderDeliveryCountry,
        'siparisteslimatadressehir' => $orderDeliveryCity,
        'siparisteslimatadresilce' => $orderDeliveryDistrict,
        'siparisteslimatadressemt' => $orderDeliveryArea,
        'siparisteslimatadresmahalle' => $orderDeliveryNeighborhood,
        'siparisteslimatadrespostakod' => $orderDeliveryPostalCode,
        'siparisteslimatadresacik' => $orderDeliveryAddress,
        'siparisteslimatadresulkekod' => $orderDeliveryCountryCode,
        'siparisfaturaunvan' => $orderInvoiceTitle,
        'siparisfaturavergidairesi' => $orderInvoiceTaxOffice,
        'siparisfaturavergino' => $orderInvoiceTaxNumber,
        'siparisfaturaad' => $orderInvoiceName,
        'siparisfaturasoyad' => $orderInvoiceSurname,
        'siparisfaturaeposta' => $orderInvoiceEmailAddress,
        'siparisfaturagsm' => $orderInvoicePhoneNumber,
        'siparisfaturaadresulke' => $orderInvoiceCountry,
        'siparisfaturaadressehir' => $orderInvoiceCity,
        'siparisfaturaadresilce' => $orderInvoiceDistrict,
        'siparisfaturaadressemt' => $orderInvoiceArea,
        'siparisfaturaadresmahalle' => $orderInvoiceNeighborhood,
        'siparisfaturaadrespostakod' => $orderInvoicePostalCode,
        'siparisfaturaadresacik' => $orderInvoiceAddress,
        'siparisfaturaadresulkekod' => $orderInvoiceCountryCode,
        'kargoid' => $cargoID,
        'sipariskargofiyat' => 0,
        'siparisnotyonetici' => $orderManagerNote,
        'siparistoplamtutar' => $orderTotalPrice,
        'sipariskdvtutar' => $orderVATPrice,
        'sipariskdvsiztutar' => $orderWithoutVATPrice,
        'sipariskargodahilfiyat' => $orderCargoPriceIncluded,
        'siparistekcekimindirimorani' => $orderCreditCardSingleChargeDiscountRate,
        'siparistekcekimindirimlifiyat' => $orderCreditCardSingleChargeDiscountPrice,
        'siparishavaleorani' => $orderBankTransferDiscountRate,
        'siparishavaleindirimlifiyat' => $orderBankTransferDiscountPrice,
        'sipariskargoindirim' => $orderCargoDiscount,
        'sipariskargoindirimaciklama' => $orderCargoDiscountDescription,
        'siparisodemeyontemi' => $orderPaymentMethod,
        'siparisodemedurum' => $orderPaymentStatus,
        'siparisdurum' => $orderStatusID,
        'languageCode' => $languageCode
    ];


    if($action == "updateOrder"){
        $updateOrder = $adminOrder->updateOrder($orderUniqID,$orderData);

        if($updateOrder>0){
            $adminOrder->commit("orderUpdated");
            echo json_encode(['status' => 'success', 'message' => 'Sipariş güncellendi']);
            exit;
        }
        else{
            $adminOrder->rollBack("orderNotUpdated");
            echo json_encode(['status' => 'error', 'message' => 'Sipaariş güncellenemedi']);
            exit;
        }
    }
    else{
        $orderData['siparisbenzersizid'] = $orderUniqID;
        $orderData['siparistariholustur'] = date("Y-m-d H:i:s");

        $addOrder = $adminOrder->createOrder($orderData);

        if($addOrder){
            $adminOrder->commit("orderAdded");
            echo json_encode(['status' => 'success', 'message' => 'Sipaariş eklendi']);
            exit;
        }
        else{
            $adminOrder->rollBack("orderNotAdded");
            echo json_encode(['status' => 'error', 'message' => 'Sipaariş eklenemedi']);
            exit;
        }
    }

}
elseif( $action == "updateOrderPaymentStatus"){
    $orderUniqID = $requestData['orderUniqID'] ?? '';
    $orderPaymentStatus = $requestData['orderPaymentStatus'] ?? '';

    if(empty($orderUniqID)){
        echo json_encode(['status' => 'error', 'message' => 'Sipariş benzersiz id gerekli']);
        exit();
    }

    if($orderPaymentStatus == ""){
        echo json_encode(['status' => 'error', 'message' => 'Ödeme durumu gerekli']);
        exit();
    }

    include_once MODEL."Admin/AdminOrder.php";
    $adminOrder = new AdminOrder($db,$config);
    $adminOrder->beginTransaction("update order payment status");
    $updateOrderPaymentStatus = $adminOrder->updateOrderPaymentStatus($orderUniqID,$orderPaymentStatus);

    if($updateOrderPaymentStatus>0){
        $adminOrder->commit("orderPaymentStatusUpdated");
        echo json_encode(['status' => 'success', 'message' => 'Sipariş ödeme durumu güncellendi']);
    }
    else{
        $adminOrder->rollBack("orderPaymentStatusNotUpdated");
        echo json_encode(['status' => 'error', 'message' => 'Sipariş ödeme durumu güncellenemedi']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Action is invalid']);
    exit();
}