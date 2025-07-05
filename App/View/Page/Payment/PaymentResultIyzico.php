<div class="container">
    <div class="row">
        <div class="col-12">
<?php

/**
 * @var array $orderData
 * @var Casper $casper
 * @var Session $session
 * @var array $query
 */

$orderData = $session->getSession("orderData");
$siteConfig = $casper->getSiteConfig();
$bankSettings = $siteConfig['bankSettings'] ?? "";

if(empty($orderData)){
    Log::write("İyzico ödeme sonuç orderData alınamadı");
    echo "<p>Bir hata oluştu lütfen daha sonra tekrar deneyin</p>";
}
elseif(empty($bankSettings)){
    Log::write("İyzico ödeme sonuç banka bilgileri alınamadı");
    echo "<p>Bir hata oluştu lütfen daha sonra tekrar deneyin</p>";
}
else{
    $creditCardStatus = true;
    $creditCardBankName="";
    $creditCardMerchantID="";
    $creditCardMerchantSalt="";
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
    }

    if($creditCardMerchantKey == "" || $creditCardMerchantSalt=""){
        Log::write("İyzico ödeme sonuç bilgileri alınamadı");
        echo "<p>Bir hata oluştu lütfen daha sonra tekrar deneyin</p>";
    }
    else
    {
        require_once(ROOT. 'vendor/iyzico/iyzipay-php/IyzipayBootstrap.php');
        IyzipayBootstrap::init();
        $options = new \Iyzipay\Options();
        $options->setApiKey($creditCardMerchantKey);
        $options->setSecretKey($creditCardMerchantSalt);
        $options->setBaseUrl("https://api.iyzipay.com");

        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);

        $token = $orderData['token'];
        $request->setToken($token);

        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Config::options());

        if($checkoutForm->getStatus()=="success")
        {
            if($checkoutForm->getPaymentStatus()=="SUCCESS")
            {
                $orderUniqID = $orderData['orderUniqID'];
                $paymentSuccessText = _odeme_basarili;
                echo "<h3>$paymentSuccessText</h3>";
                echo "<p>". _odeme_siparis_no .": $orderUniqID</p>";
            }
            else
            {
                Log::write("Iyzico Ödeme Sonuç: ".$checkoutForm->getErrorMessage());
                echo '
                    <h3>'._odeme_basarisiz.'</h3>
                    <p>'.$checkoutForm->getErrorMessage().'</p>
                ';
            }
        }
        else
        {
            echo '
                <h3>'._odeme_basarisiz.'</h3>
                <p>'.$checkoutForm->getErrorMessage().'</p>
            ';
        }
    }
}
?>
        </div>
    </div>
</div>
<style>
    .container {
        margin-top: 50px;
        padding: 20px 15%;
    }
    .alert {
        padding: 20px;
    }
    .alert-heading {
        margin-bottom: 20px;
    }
</style>