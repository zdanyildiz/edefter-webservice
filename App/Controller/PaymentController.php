<?php
/**
 * @var Database $db
 * @var Helper $helper
 * @var Session $session
 * @var array $requestData
 */

$casper = $session->getCasper();

if (!$casper instanceof Casper) {
    echo "Casper is not here - PaymentController:15";exit();
}

$config = $casper->getConfig();
$helper = $config->Helper;

//echo $hash = base64_encode( hash_hmac('sha256', "SPRFDT00000000790"."AgZaBW6yzGtt4Bhr"."success"."1", "U2iKjBdRyYKALsKa", true) );
function checkAction($action)
{
    $validActions = ['checkPayment', 'bankSubmit'];
    if (!in_array($action, $validActions)) {
        Log::write("PaymentController:ACTION error", "error");
        exit("PaymentController:ACTION error");
    }
    return true;
}

function checkPaymentMethod($bankName)
{
    $validPaymentMethods = ['iyzico', 'paytr', 'bankTransfer'];
    if (!in_array($bankName, $validPaymentMethods)) {
        Log::write("PaymentController:PAYMENT METHOD error bankName: $bankName", "error");
        exit("PaymentController:PAYMENT METHOD error");
    }
    return true;
}

function getOrderUniqID($action, $bankName, $requestData)
{
    if ($action == "checkPayment") {
        if ($bankName == "paytr") {
            return $requestData['merchant_oid'] ?? null;
        }
        elseif ($bankName == "iyzico"){
            return $requestData['paymentConversationId'] ?? null;
        }
    } elseif ($action == "bankSubmit") {
        return $requestData['orderUniqID'] ?? null;
    }
    return null;
}

$action = $requestData['action'] ?? null;
Log::write("ACTION: $action", "success");
checkAction($action);

$bankName = $requestData['bankName'] ?? null;
Log::write("BANK NAME: $bankName", "success");
checkPaymentMethod($bankName);

if($action=="checkPayment") {
    $requestData = $_POST;
}


$orderUniqID = getOrderUniqID($action, $bankName, $requestData);
Log::write("ORDER ID: $orderUniqID", "success");
//echo "ORDER ID: $orderUniqID";

if(empty($orderUniqID)){
    Log::write("PaymentController:ORDER ID error action: $action bankName: $bankName", "error");
    exit("PaymentController:ORDER ID error");
}

include_once MODEL . 'Order.php';
$order = new Order($db, $session);

$orderInfo= $order->getOrderByOrderUniqID($orderUniqID)[0];

if(empty($orderInfo)){
    Log::write("PaymentController:ORDER INFO error action: $action bankName: $bankName orderUniqID: $orderUniqID", "error");
    exit("PaymentController:ORDER INFO error");
}

$orderStatus = $orderInfo['siparisdurum'];
Log::write("ORDER STATUS: $orderStatus", "success");
//echo "ORDER STATUS: $orderStatus";

$orderPaymentStatus = $orderInfo['siparisodemedurum'];
Log::write("PAYMENT STATUS: $orderPaymentStatus", "success");
//echo "PAYMENT STATUS: $orderPaymentStatus";


if($orderStatus != 6 or $orderPaymentStatus != 0){
    Log::write("PaymentController:Order Status or Payment Status changed action: $action bankName: $bankName orderUniqID: $orderUniqID", "error");
    echo "OK";exit();
}


$orderID = $orderInfo['siparisid'];
$languageCode = $orderInfo['languageCode'];

$language = new Language($db, $languageCode);
$language->getTranslations($languageCode);
$languageID = $language->getLanguageID($languageCode);

$siteConfigInfo = new SiteConfig($db,$languageID);
$siteConfigInfo->createSiteConfig();
$siteConfig = $siteConfigInfo->getSiteConfig();
$bankSettings = $siteConfig['bankSettings'];

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

$orderDelete = 0;
$cartDelete = 1;

$bankHtml = "";

$bankErrorMessage="";

if($bankName == "paytr"){

    $hash = base64_encode( hash_hmac('sha256', $requestData['merchant_oid'].$creditCardMerchantSalt.$requestData['status'].$requestData['total_amount'], $creditCardMerchantKey, true) );

    if($hash != $requestData['hash']){
        Log::write("PAYTR HASH error", "error");
        die("PAYTR HASH error");
    }


    $orderPaymentMethod = 'kk';
    $orderTotalAmount = $requestData['total_amount'] ?? 0;
    $orderTotalAmount = $orderTotalAmount / 100;
    $orderTotalAmount = number_format($orderTotalAmount, 2, '.', '');

    if( $requestData['status'] == 'success' ) {
        $orderStatus = 1;
        $orderPaymentStatus = 1;

    }
    else {
        $orderStatus = 6;
        $orderPaymentStatus = 0;
        $orderDelete = 1;
        $cartDelete = 0;

        $bankErrorMessage = $requestData['failed_reason_code'] ." - ".$requestData['failed_reason_msg'];

        Log::write("Ödemeye Onay Verilmedi: ".$requestData['failed_reason_code'] ." - ".$requestData['failed_reason_msg'], "warning");
    }
}
elseif($bankName == "iyzico") {

    $orderPaymentMethod = 'kk';
    $paymentId = $requestData['paymentId'] ?? 0;
    $status =$requestData['status'];
    if( $requestData['status'] == 'success' ) {
        $orderStatus = 1;
        $orderPaymentStatus = 1;
    }
    else {
        $orderStatus = 6;
        $orderPaymentStatus = 0;
        $orderDelete = 1;
        $cartDelete = 0;

        Log::write("Ödemeye Onay Verilmedi: ", "warning");
    }
}
elseif($bankName == "bankTransfer"){

    $eftInfo = $siteConfig['eftInfo'];

    $bankHtml = '<p>'._odeme_havale_odeme_aciklama.'</p>';
    if(!empty($eftInfo)) {

        foreach ($eftInfo as $info) {
            $eftBankName = $info['bankaad'];
            $eftBankAccountName = $info['hesapadi'];
            $eftBankAccountNumber = $info['hesapno'];
            $eftBankBranchCode = $info['hesapsube'];
            $eftBankIBAN = $info['ibanno'];

            $bankHtml .= "<p>$eftBankName</p>";
            $bankHtml .= "<p>$eftBankAccountName</p>";
            $bankHtml .= "<p>$eftBankBranchCode - $eftBankAccountNumber</p>";
            $bankHtml .= "<p>$eftBankIBAN</p>";
            $bankHtml .= "<hr>";
        }
    }
    else{

        $cartDelete = 0;

        $bankErrorMessage = "EFT INFO error";

        Log::write("PaymentController: EFT INFO error action: $action bankName: $bankName orderUniqID: $orderUniqID", "error");
    }

    $orderStatus = 1;
    $orderPaymentStatus = 0;
    $orderPaymentMethod = 'bh';
    $orderTotalAmount = $orderInfo['siparistoplamtutar'];
}

$orderUpdateData = [
    'siparisdurum' => $orderStatus,
    'siparisodemedurum' => $orderPaymentStatus,
    'siparistarihguncelle' => date('Y-m-d H:i:s'),
    'siparisodemeyontemi' => $orderPaymentMethod,
    'siparistoplamtutar' => $orderTotalAmount,
    'siparissil' => $orderDelete
];

//işleyişe göre sipariş durumunu güncelliyoruz
$updateOrder = $order->updateOrder($orderUniqID, $orderUpdateData);

include_once MODEL . 'Cart.php';
$cart = new Cart($db, $helper, $session, $config);

//sepeti güncelleyelim
$cart->updateCartByOrderUniqID($orderUniqID, ['sepetsil' => $cartDelete]);

if($cartDelete == 0){

    $cart->updateCartByOrderUniqID($orderUniqID, ['siparisbenzersiz' => ""]);

    Log::write($bankErrorMessage, "error");

    if($bankName == "paytr"){
        echo "OK";exit();
    }
    elseif($bankName == "bankTransfer"){
        echo json_encode(['status' => 'error', 'message' => "Sipariş tamamlanamadı"]);
        exit();
    }
}

//sipariş ürünlerini alalım

$orderProducts = $cart->getCartByOrderUniqID($orderUniqID);

//ürün stoğu güncellenecek

$memberID = $orderInfo['uyeid'];
include_once MODEL . 'Member.php';
$member = new Member($db);
Log::write("Member ID: $memberID", "success");
//exit();
$memberInfo = $member->getMemberInfo($memberID,"");
$memberInfo = $memberInfo[0];
$memberNameSurname = $memberInfo['uyeadsoyad'];

$memberName = $memberInfo['uyead'];
$memberName = $helper->decrypt($memberName, $config->key);

$memberSurname = $memberInfo['uyesoyad'];
$memberSurname = $helper->decrypt($memberSurname, $config->key);

$memberEmail = $memberInfo['uyeeposta'];
$memberEmail = $helper->decrypt($memberEmail, $config->key);

$orderHtml = $cart->getCartHtmlForMail($orderProducts);

include_once Helpers. 'EmailSender.php';
$emailSender = new EmailSender();

$companyInfo = $siteConfig['companySettings'];
$companyName = $companyInfo['ayarfirmakisaad'];
$companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
$companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
$companyEmail = $companyInfo['ayarfirmaeposta'];

$logoInfo = $siteConfig['logoSettings'];
$logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

$myOrderLink = $config->http.$config->hostDomain."/?/control/member/get/orders";

$emailSubject = "$companyName - "._odeme_siparis_basarili_yazi;

$emailTemplate = file_get_contents(Helpers.'mail-template/order.php');
$emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
$emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
$emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
$emailTemplate = str_replace("[my-order-link]", $myOrderLink, $emailTemplate);
$emailTemplate = str_replace("[member-name-surname]", $memberNameSurname, $emailTemplate);
$emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
$emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
$emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);
$emailTemplate = str_replace("[orderUniqID]", $orderUniqID, $emailTemplate);
$emailTemplate = str_replace("[orderHtml]", $orderHtml, $emailTemplate);
$emailTemplate = str_replace("[_odeme_siparisbasari_yazi]", _odeme_siparis_basarili_yazi, $emailTemplate);
$emailTemplate = str_replace("[_uyelik_mesajsiparisno_yazi]", _odeme_siparis_no, $emailTemplate);
$emailTemplate = str_replace("[_uyelik_siparislerim_yazi]", _odeme_siparislerim, $emailTemplate);
$emailTemplate = str_replace("[bankHtml]", $bankHtml, $emailTemplate);

$emailSender->sendEmail($memberEmail,$memberNameSurname, $emailSubject, $emailTemplate);

$emailSender->sendEmail($companyEmail,$companyName, $emailSubject, $emailTemplate);

Log::write("Ödeme Onaylandı - $orderUniqID", "success");

$casper = $session->getCasper();
$visitor = $casper->getVisitor();

$visitor['visitorCart'] = [];
$visitor['visitorGetCart'] = false;

$casper->setVisitor($visitor);
$session->updateSession('casper', $casper);

if($bankName == "paytr"){
    echo "OK";exit();
}
elseif($bankName == "bankTransfer"){
    $session->addSession('salesStatus', ["status" => "success"]);
    echo json_encode(['status' => 'success', 'message' => _odeme_siparis_eposta_basarili]);
    exit();
}
Log::write("PaymentController:END, bankName:$bankName", "success");

