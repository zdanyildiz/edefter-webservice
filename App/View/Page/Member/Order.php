<?php
/**
 * @var array $visitor
 * @var Helper $helper
 * @var string $memberPageLink
 */

$orders=$visitor['visitorIsMember']['memberOrders'];
//$helper->writeToArray($orders);
?>
<div class="member-container">
    <div class="order-search-container">
        <form action="" method="post">
            <input type="text" name="orderSearch" id="orderSearch" placeholder="Sipariş No">
            <button type="submit">Ara</button>
        </form>
        <div class="order-filter">
            <ul name="orderFilter" id="orderFilter">
                <li><a href="<?=$memberPageLink?>?orders">Tüm Siparişler</a></li>
                <li><a href="<?=$memberPageLink?>?orders&orderType=4">Teslim edilenler</a></li>
                <li><a href="<?=$memberPageLink?>?orders&orderType=1">Ödeme Onayı Beklenenler</a></li>
                <li><a href="<?=$memberPageLink?>?orders&orderType=2">Hazırlananlar</a></li>
                <li><a href="<?=$memberPageLink?>?orders&orderType=6">Tamamlanmamışlar</a></li>
                <li><a href="<?=$memberPageLink?>?orders&orderType=11">İptal Edilenler</a></li>
                <li><a href="<?=$memberPageLink?>?orders&orderType=10">İade Edilenler</a></li>
            </ul>
        </div>
    </div>
    <div class="order-card-container">
        <?php
        if(!empty($orders)){
            //echo '<pre>';
            //print_r($orders);
            foreach ($orders as $order){
            $orderDate = strtotime($order['orderCreateDate']); // Sipariş tarihini zaman damgasına çevir
            $currentDate = time(); // Şu anki zaman damgası
            $dateDifference = $currentDate - $orderDate; // Zaman farkını hesapla

            if ($dateDifference > 15 * 24 * 60 * 60) { // 15 günün saniye cinsinden karşılığı
                $cancellationRefundExchangeResponseStatus = false;
            } else {
                $cancellationRefundExchangeResponseStatus = true;
            }
                ?>
                <div class="order-card">
                    <details class="order-card-header">
                        <summary class="order-card-title">
                            <div class="order-uniqid">
                                <span><?php echo $order['orderUniqID'];?></span>
                            </div>
                            <div class="order-date">
                                <span><?php echo $order['orderCreateDate'];?></span>
                            </div>
                            <div class="order-status">
                                <span><?php echo $order['orderStatusTitle'];?></span>
                            </div>
                            <div class="order-total">
                                <span>
                                    <?php echo $helper->formatCurrency($order['orderTotalPrice']);?> <?php echo $order['orderCurrencyCode'];?>
                                </span>
                            </div>
                        </summary>
                        <div class="order-card-body">
                            <div class="order-products">
                                <?php
                                $orderProducts=$order['orderProducts'];
                                foreach ($orderProducts as $orderProduct){
                                    $productImage = explode(",",$orderProduct['productImages']['resim_url'])[0];
                                    ?>
                                    <div class="order-product">
                                        <div class="order-product-image">
                                            <img src="<?php echo imgRoot."?imagePath=".trim($productImage)."&width=150" ?>" alt="">
                                        </div>
                                        <div class="order-product-info">
                                            <div class="order-product-name">
                                                <span><?php echo $orderProduct['productName'];?></span>
                                            </div>
                                            <div class="order-product-quantity-price">
                                                <span>
                                                    <?php echo $orderProduct['productQuantity'];?> <?=$orderProduct['productUnitName']?> x
                                                    <?php echo $orderProduct['productPrice'];?> <?php echo $order['orderCurrencyCode'];?> : <?php echo $helper->formatCurrency($orderProduct['productQuantity']*$orderProduct['productPrice']);?> <?php echo $order['orderCurrencyCode'];?>
                                                </span>
                                            </div>
                                        </div>
                                        <?php if($cancellationRefundExchangeResponseStatus):?>
                                        <div class="order-cancellation-refund-exchange-response-container">
                                            <button class="btn btn-primary cancellationRefundExchangeResponseButton" data-orderuniqid="<?php echo $order['orderUniqID'];?>"><?=_sol_uyelik_iptal_iade_degisim_yazi?></button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="order-address-invoice-container">
                                <div class="order-address">
                                    <div class="order-address-title">
                                        <span>Teslimat Adresi</span>
                                    </div>
                                    <div class="order-address-content">
                                        <span><?php echo $order['orderDeliveryAddressName'];?></span>
                                        <span><?php echo $order['orderDeliveryAddressCountry'];?></span>
                                        <span><?php echo $order['orderDeliveryAddressCity'];?></span>
                                        <span><?php echo $order['orderDeliveryAddressCounty'];?></span>
                                        <span><?php echo $order['orderDeliveryAddressArea'];?></span>
                                        <span><?php echo $order['orderDeliveryAddressNeighborhood'];?></span>
                                        <span><?php echo $order['orderDeliveryAddressStreet'];?></span>
                                        <span><?php echo $order['orderDeliveryAddressPostalCode'];?></span>
                                    </div>
                                </div>
                                <div class="order-invoice">
                                    <div class="order-invoice-title">
                                        <span>Fatura Adresi</span>
                                    </div>
                                    <div class="order-invoice-content">
                                        <span><?php echo $order['orderInvoiceName'];?></span>
                                        <span><?php echo $order['orderInvoiceAddressCountry'];?></span>
                                        <span><?php echo $order['orderInvoiceAddressCity'];?></span>
                                        <span><?php echo $order['orderInvoiceAddressCounty'];?></span>
                                        <span><?php echo $order['orderInvoiceAddressArea'];?></span>
                                        <span><?php echo $order['orderInvoiceAddressNeighborhood'];?></span>
                                        <span><?php echo $order['orderInvoiceAddressStreet'];?></span>
                                        <span><?php echo $order['orderInvoiceAddressPostalCode'];?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="order-payment">
                                <div class="order-payment-title">
                                    <span>Ödeme Bilgileri</span>
                                </div>
                                <div class="order-payment-content">
                                    <span>Ödeme Tipi : <?php echo $order['orderPaymentType'];?></span>
                                    <span>Ödeme Durumu : <?php echo $order['orderPaymentStatus'];?></span>
                                </div>
                                <?php if($order['orderStatusID'] == 6): ?>
                                <div class="order-payment-content">
                                    <span><a href="/?/control/checkout/get/resumeOrder&orderUniqID=<?=$order['orderUniqID']?>">Siparişi Tamamla</a></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </details>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
<div id="return-form-popup" class="return-form-popup hidden">
    <div class="popup-content">
        <h3>İptal/İade/Değişim Talebi</h3>
        <form id="return-form">
            <input type="hidden" name="orderUniqID" id="orderUniqID">
            <input type="hidden" name="csrf_token" id="csrf-token-return-form" value="<?php echo $helper->generateCsrfToken();?>">
            <div id="return-form-product-list">
                ürün seçin
            </div>
            <label for="request">Talebinizin:</label>
            <select name="request" id="request" required>
                <option value="Değişim">Değişim</option>
                <option value="İade">İade</option>
                <option value="İptal">İptal</option>
            </select>
            <label for="reason">Talebinizin Sebebi:</label>
            <select name="reason" id="reason" required>
                <option value="Kusurlu Ürün">Kusurlu Ürün</option>
                <option value="Yanlış Ürün">Yanlış Ürün</option>
                <option value="Diğer">Diğer</option>
            </select>
            <label for="description">Açıklama:</label>
            <textarea name="description" id="description" rows="4"></textarea>
            <button type="submit">Gönder</button>
            <button type="button" class="close-popup btn">Kapat</button>
        </form>
    </div>
</div>