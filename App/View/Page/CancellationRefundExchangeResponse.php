<?php
/**
 * @var array $page
 * @var $view
 * @var $config
 * @var Casper $casper
 * @var array $bannerInfo
 * @var string $languageCode
 */
$config = $casper->getConfig();
$helper = $config->Helper;
$pageTitle = $page['sayfaad'];
$pageContent = $page['sayfaicerik'];
$visitor = $casper->getVisitor();
?>
<div class="CancellationRefundExchangeResponse-container">
    <div class="CancellationRefundExchangeResponse-form-container">
        <h1><?=$pageTitle?></h1>
        <div class="member-login-form-container">
            <form action="/?/control/member/post/cancellationRefundExchangeResponse" name="cancellationRefundExchangeResponseForm" id="cancellationRefundExchangeResponseForm" method="post">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="languageCode" value="<?=$languageCode?>">
                <input type="hidden" name="websites" id="websites-memberLoginForm" value="">
                <div class="form-group row">
                    <label for="email-memberLoginForm"><?=_sol_uyelik_siparislerim_yazi?>:</label>
                    <select name="orders" id="orders-cancellationRefundExchangeResponseForm">
                    <?php
                        $memberOrders = $visitor['visitorIsMember']['memberOrders'];
                        foreach ($memberOrders as $order) {
                            ?>
                            <option value="<?=$order['siparisbenzersizid']?>"><?=$order['siparisbenzersizid']?></option>
                        <?php
                        }

                    ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="CancellationRefundExchangeResponse-content-container">
        <?php echo (!empty($pageContent)) ? htmlspecialchars_decode($pageContent) : ''; ?>
    </div>
</div>