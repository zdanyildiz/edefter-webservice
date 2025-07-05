<?php
/**
 * @var Casper $casper
 * @var Helper $helper
 * @var string $checkoutLink
 */
$config = $casper->getConfig();
$helper = $config->Helper;
//sepet sayfası görünümünü yapalım
//print_r($session->getSession("visitor"));
$visitorCart = isset($casper->getVisitor()["visitorCart"]) ? $casper->getVisitor()["visitorCart"] : [];
//print_r($visitorCart);
//print_r($visitorCart);exit();
?>
<div class="mycart-container">
    <div class="cart-header">
        <h1><?=_sepet_sayfasi_sepetim_yazi?></h1>
    </div>
    <?php if(!empty($visitorCart)):?>
    <div class="cart-items">
        <?php
        $totalQuantity = 0;
        $totalPrice = 0;
        $totalDesi = 0;
        $totalModel = 0;
        $totalDiscountAmount = 0;
        foreach ($visitorCart['cartProducts'] as $product) {
            $totalModel++;
            $cartUniqID = $product['cartUniqID'];
            $productID = $product['productID'];
            $productName = $product['productName'];
            $productStockCode = $product['productSelectedStockCode'];
            $productLink = $product['productLink'];
            $productPrice = $product['productPrice'];
            $productQuantity = $product['productQuantity'];
            $productQuantity = str_replace(".0000", "", $productQuantity);
            $productDesi = $product['productDesi'];

            $productMinQuantity = $product['productMinQuantity'];
            $productMinQuantity = str_replace(".0000", "", $productMinQuantity);

            $productMaxQuantity = $product['productMaxQuantity'];
            $productMaxQuantity = str_replace(".0000", "", $productMaxQuantity);

            $productCoefficient = $product['productCoefficient'];
            $productCoefficient = str_replace(".0000", "", $productCoefficient);

            $productUnitName = $product['productUnitName'];

            $productCurrencyID = $product['productCurrencyID'];
            $productCurrencySymbol = $product['productCurrencySymbol'];
            $productCurrencyCode = $product['productCurrencyCode'];

            $productSelectedVariant = $product['productSelectedVariant'];

            $productDiscountAmount = $product['productDiscountAmount'];
            $productDiscountDescription = $product['productDiscountDescription'];

            $totalDiscountAmount += $productDiscountAmount;


            $productTotalDesi = $productDesi * $productQuantity;
            $totalDesi += $productTotalDesi;

            $totalQuantity += $productQuantity;
            $productTotalPrice = $productPrice * $productQuantity;

            $totalPrice += $productTotalPrice;

            $productImage = explode(", ", $product['productImage'])[0];
            $imageRoot = imgRoot."?imagePath=".trim($productImage)."&width=150";

            ?>
            <div class="cart-item" id="cart-item-<?=$cartUniqID?>">
                <div class="cart-image-container">
                    <a href="<?php echo $productLink; ?>" class="cart-item-image-link" >
                        <img src="<?php echo $imageRoot; ?>" class="cart-item-image" alt="<?php echo $productName; ?>" loading="lazy" width="150" height="150">
                    </a>
                </div>
                <div class="cart-item-details">
                    <a href="<?php echo $productLink; ?>" class="cart-item-title">
                        <?php echo $productName; ?>
                    </a>
                    <div class='cart-item-variant-text'><?=_sepet_urun_stok_kod_yazi?> <?=$productStockCode?></div>
                    <?php
                    if(!empty($productSelectedVariant)){
                        $productSelectedVariant = json_decode($productSelectedVariant, true);
                        foreach ($productSelectedVariant as $variant) {
                            echo "<div class='cart-item-variant-text'>".$variant['attribute']['name'].": ".$variant['attribute']['value']."<br></div>";
                        }
                    }
                    ?>
                </div>
                <div class="cart-item-price" id="price-<?=$cartUniqID?>"><?=$productCurrencySymbol?> <?=$helper->formatCurrency($productPrice)?></div>
                <div class="cart-item-quantity">
                    <div class="cart-item-quantity-wrapper">
                        <div class="quantity" title="Quantity">
                            <button class="qtyBtn minus" name="minus" id="minus-<?=$cartUniqID?>" data-cartID="<?=$cartUniqID?>"><i>-</i></button>
                            <input
                                    class="quantity-input qty"
                                    type="number" name="productQuantity"
                                    value="<?php echo $productQuantity; ?>"
                                    min="<?=$productMinQuantity?>" max="<?=$productMaxQuantity?>"
                                    id="quantity-<?=$cartUniqID?>"
                                    data-productCoefficient="<?=$productCoefficient?>"
                            >
                            <button class="qtyBtn plus" name="plus" id="plus-<?=$cartUniqID?>" data-cartID="<?=$cartUniqID?>"><i>+</i></button>
                        </div>
                        <span class="productUnitName"><?=$productUnitName?></span>
                        <button id="Remove-<?=$cartUniqID?>" class="removeMb">
                            <a href="#" class="remove" ><?=_sepet_urun_sil_yazi?></a>
                        </button>
                    </div>
                </div>
                <div class="cart-totals" id="cart-totals-<?=$cartUniqID?>" data-totalprice="<?=str_replace(",",".",$productTotalPrice)?>" data-discountamount="<?=$productDiscountAmount?>">
                    <span class="cart-item-total-price"><?=$productCurrencySymbol?> <?=$helper->formatCurrency($productTotalPrice)?></span>
                    <?php if($productDiscountAmount>0){ ?>
                        <span class="cart-item-discount-description"><?=$productDiscountDescription?></span>
                        <span class="cart-item-discount-amount"><?=$productCurrencySymbol?> <?=$helper->formatCurrency($productDiscountAmount)?></span>
                        <span class="cart-item-discounted-price"><i><?=$productCurrencySymbol?> <?=$helper->formatCurrency($productTotalPrice-$productDiscountAmount)?></i></span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="cart-summary">
        <ul>
            <li class="total-model"><?=_sepet_toplam_urun_modeli?>: <?php echo $totalModel; ?></li>
            <li class="total-quantitiy"><?=_sepet_toplam_urun_adedi?>: <?php echo $totalQuantity; ?></li>
            <li class="total-price"><?=_sepet_sepet_toplam_tutar_yazi?> <?php echo $helper->formatCurrency($totalPrice, 2); ?> <?=$productCurrencySymbol?></li>
            <?php if($totalDiscountAmount>0){ ?>
                <li class="total-discount"><?=_sepet_indirim_toplam_tutar_yazi?> <i><?php echo $helper->formatCurrency($totalDiscountAmount, 2); ?> <?=$productCurrencySymbol?></i></li>
                <li class="total-discounted-price"><?=_sepet_indirimli_toplam_tutar_yazi?> <?php echo $helper->formatCurrency($totalPrice-$totalDiscountAmount, 2); ?> <?=$productCurrencySymbol?></li>
            <?php } ?>
        </ul>
        <a href="<?=$checkoutLink?>"><?=_sepet_odeme_yap_yazi?></a>
    </div>
    <?php else: ?>
        <div class="cart-items">
            <p><?=_sepet_sepetim_urun_yok_yazi?></p>
        </div>
    <?php endif; ?>
</div>
