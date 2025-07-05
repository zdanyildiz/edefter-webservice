<?php
/**
 * @var array $orderData
 * @var Casper $casper
 * @var Session $session
 * @var array $query
 */

$visitor = $casper->getVisitor();
$visitor['visitorGetCart'] = true;
$casper->setVisitor($visitor);
$session->updateSession("casper",$casper);

$orderUniqID = $orderData['orderUniqID'];
$paymentSuccessText = _odeme_basarili;

$session->addSession('salesStatus', ["status" => "success"]);
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success" role="alert">
                <p class="alert-heading"><?=$paymentSuccessText?>!</p>
                <hr>
                <p class="mb-0"><a href="/?/control/member/get/orders"><?=_odeme_siparis_no?> <?=$orderUniqID?></a></p>
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
