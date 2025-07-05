<?php
/**
 * @var array $orderData
 * @var Casper $casper
 * @var Session $session
 * @var array $query
 */


$orderUniqID = $orderData['orderUniqID'];
$paymentSuccessText = _odeme_basarisiz;
$paymentSuccessTitle = explode("||",$paymentSuccessText)[0];
$paymentSuccessText = explode("||",$paymentSuccessText)[1];
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading"><?=$paymentSuccessTitle?>!</h4>
                <p><?=$paymentSuccessText?></p>
                <hr>
            </div>
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
