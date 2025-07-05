<?php
/**
 * @var string $creditCardBankName
 * @var array $orderData
 * @var Config $config
 */

$paymentData = $orderData['paymentData'] ?? [];
Log::write("Payment Data: ". json_encode($paymentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),"info");

if(!empty($paymentData)) {
    $eftInfo = $orderData['paymentData']['eftInfo'];
    $payAtTheDoorStatus = $orderData['paymentData']['payAtTheDoorStatus'];
    $eftStatus = $orderData['paymentData']['eftStatus'];
    $creditCardStatus = $orderData['paymentData']['creditCardStatus'];

    $orderUniqID = $orderData['orderUniqID'];
    $languageCode = $orderData['languageCode'];
?>
<div class="payment-container">
    <div class="tab">
        <?php if($creditCardStatus):?>
            <button class="tablinks active" onclick="openPayment(event, 'CreditCard')"><?=_odeme_kredi_karti_odeme_yazi?></button>
        <?php endif;?>
        <?php if($eftStatus):?>
            <button class="tablinks <?=!$creditCardStatus ? "active" : ""?>" onclick="openPayment(event, 'EFT')"><?=_odeme_havale_odeme_yazi?></button>
        <?php endif;?>
        <?php if($payAtTheDoorStatus):?>
            <button class="tablinks <?=!$creditCardStatus && !$eftStatus ? "active" : ""?>" onclick="openPayment(event, 'PayAtDoor')"><?=_odeme_kapida_odeme_yazi?></button>
        <?php endif;?>
    </div>
    <?php if($creditCardStatus):?>
        <div id="CreditCard" class="tabcontent active">
            <h3><?=_odeme_kredi_karti_odeme_baslik?></h3>
            <div class="payment-credit-card-container">
                <?php echo _odeme_kredi_karti_odeme_aciklama;?>
            </div>

            <?php
            if($creditCardBankName=="paytr") {
                $config->loadView("Page/Payment/PayTR",['token'=> $orderData['token']]);
            }
            elseif ($creditCardBankName=="iyzico"){
                Log::write("checkOutFormContent: ". json_encode($orderData['checkoutFormContent'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),"info");
                $config->loadView("Page/Payment/Iyzico",['checkoutFormContent'=> $orderData['checkoutFormContent']]);
            }
            ?>
        </div>
    <?php endif;?>
    <?php if($eftStatus):?>
        <div id="EFT" class="tabcontent <?=!$creditCardStatus ? "active" : ""?>">
            <h3><?=_odeme_banka_havalesi_odeme_baslik?></h3>
            <div class="payment-bank-container">
                <?php echo _odeme_havale_odeme_aciklama;?>
                <?=_odeme_havale_siparis_kodu_yazi?>
                <?php
                    foreach ($eftInfo as $bank) {
                        echo '<div class="bank-container">';
                        echo "<h1 class='bankName'>".$bank['bankaad']."</h1>";
                        echo "<p class='accountName'>".$bank['hesapadi']."</p>";
                        echo "<p class='bank-branch'>".$bank['hesapsube']." - ".$bank['hesapno']."</p>";
                        echo "<p class='bank-iban'>".$bank['ibanno']."</p>";
                        echo '</div>';
                    }
                ?>
            </div>
            <div class="payment-bank-button-container">
                <a href="/?/control/payment/get/bankSubmit&orderUniqID=<?=$orderUniqID?>&languageCode=<?=$languageCode?>&bankName=bankTransfer" id="payment-bank-button" class="btn btn-primary"><?=_odeme_siparisi_onayla_havale?></a>
            </div>
        </div>
    <?php endif;?>
    <?php if($payAtTheDoorStatus):?>
        <div id="PayAtDoor" class="tabcontent <?=!$creditCardStatus && !$eftStatus ? "active" : ""?>">
            <h3><?=_odeme_kapida_odeme_baslik?>></h3>
            <?=_odeme_kapida_odeme_aciklama?>>
            <div class="payment-pay-at-the-door-button-container">
                <a href="/?/control/payment/get/payAtTheDoorSubmit&orderUniqID=<?=$orderUniqID?>&languageCode=<?=$languageCode?>" id="payment-bank-button" class="btn btn-primary"><?=_odeme_siparisi_onayla_kapida_odeme?></a>
            </div>
        </div>
    <?php endif;?>
</div>
<?php } ?>