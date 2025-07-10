<?php
/**
 * @var Session $session
 * @var Casper $casper
 * @var Helper $helper
 * @var array $siteConfig
 * @var Config $config
 * @var string $checkoutLink
 */
$helper = $config->Helper;


$rightCartShow=true;

$visitorCart = isset($casper->getVisitor()["visitorCart"]) ? $casper->getVisitor()["visitorCart"] : [];
$routerResult=$session->getSession("routerResult");

$languageCode = $helper->toLowerCase($routerResult["languageCode"]);

if($routerResult["contentName"]=="Page"){
    $page=$session->getSession("page");
    if($page['sayfatip']==8 || $page['sayfatip']==9 || $page['sayfatip']==22){
        $rightCartShow=false;
    }
}

if(empty($visitorCart)){
    $rightCartShow=false;
}

$pageLinks = $siteConfig['specificPageLinks'];
foreach ($pageLinks as $pageLink) {
    if ($pageLink['sayfatip'] == 8) {
        $cartLink = $pageLink['link'];
    break;
    }
}
?>
<?php if ($rightCartShow) { ?>
<aside class="aside-right-cart" data-languagecode="<?=$languageCode?>">
    <div class="mycart-container">
        <div class="cart-header">
            <h1><a href="<?=$cartLink?>"><?=_sepet_sepete_git_yazi?></a></h1>
            <span class="aside-right-cart-close">X</span>
        </div>
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
                $productMinQuantity = !empty($productMinQuantity) ? str_replace(".0000", "", $productMinQuantity) : $productMinQuantity;

                $productMaxQuantity = $product['productMaxQuantity'];
                $productMaxQuantity = !empty($productMaxQuantity) ? str_replace(".0000", "", $productMaxQuantity) : $productMaxQuantity;

                $productCoefficient = $product['productCoefficient'];
                $productCoefficient = !empty($productCoefficient) ? str_replace(".0000", "", $productCoefficient) : $productCoefficient;

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

                $imageRoot = "/Public/Image/bos.jpg";
                if(!empty($product['productImage'])){
                    $productImage = explode(", ", $product['productImage'])[0];
                    $imageRoot = imgRoot."?imagePath=".trim($productImage)."&width=100";
                }

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
                                        data-productCoefficient="<?=$productCoefficient?>">
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
                    <li class="total-discount"><?=_sepet_sepet_toplam_tutar_yazi?> <i><?php echo $helper->formatCurrency($totalDiscountAmount, 2); ?> <?=$productCurrencySymbol?></i></li>
                    <li class="total-discounted-price"><?=_sepet_indirimli_toplam_tutar_yazi?> <?php echo $helper->formatCurrency($totalPrice-$totalDiscountAmount, 2); ?> <?=$productCurrencySymbol?></li>
                <?php } ?>
            </ul>
            <a href="<?=$cartLink?>"><?=_sepet_sepete_git_yazi?></a>
            <a href="<?=$checkoutLink?>"><?=_sepet_odeme_yap_yazi?></a>
        </div>
    </div>
</aside>
<?php } ?>
<?php

if($config->localhost){?>
<aside class="aside-right-visitor">
    <div class="visitor-header">
        <h1>Ziayertçi Bilgileri</h1>
        <span class="aside-right-visitor-close btn">X</span>
    </div>
    <?php if($routerResult["contentName"]=="Page"){?>
    <div style="padding:10px;width:100%;overflow: auto"><pre><?php print_r($page)?></pre></div>
    <?php }?>
    <div class="visitor-member-container">
    <?php
    $visitorCookie = $session->getCookie("visitor");
    if(!empty($visitorCookie)):
    ?>
        <div class="visitor-container">
            <div class="visitor-inf">
                <div class="visitor-inf-visitorUniqID">
                    <span class="visitor-inf-title">Ziyaretçi UniqID:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorUniqID']?></span>
                </div>
                <div class="visitor-inf-visitorEntryTime">
                    <span class="visitor-inf-title">Ziyaretçi Giriş Zamanı:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorEntryTime']?></span>
                </div>
                <div class="visitor-inf-visitorIP">
                    <span class="visitor-inf-title">Ziyaretçi IP:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorIP']?></span>
                </div>
                <div class="visitor-inf-visitorIsMember">
                    <span class="visitor-inf-title">Ziyaretçi Üye mi:</span>
                    <span class="visitor-inf-value"><?=($visitorCookie['visitorIsMember']['memberStatus']) ? "Evet" : "Hayır"?></span>
                </div>
                <?php if($visitorCookie['visitorIsMember']['memberStatus']): ?>
                <div class="visitor-inf-memberID">
                    <span class="visitor-inf-title">Üye ID:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorIsMember']['memberID']?></span>
                </div>
                <div class="visitor-inf-memberType">
                    <span class="visitor-inf-title">Üye Tipi:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorIsMember']['memberType']?></span>
                </div>
                <div class="visitor-inf-memberName">
                    <span class="visitor-inf-title">Üye Adı:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorIsMember']['memberName'] ?? ''?></span>
                </div>
                <div class="visitor-inf-memberEmail">
                    <span class="visitor-inf-title">Üye E-Posta:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorIsMember']['memberEmail']?></span>
                </div>
                <?php endif; ?>
                <div class="visitor-inf-visitorVisitCount">
                    <span class="visitor-inf-title">Ziyaretçi Ziyaret Sayısı:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorVisitCount']?></span>
                </div>
                <div class="visitor-inf-visitorLanguage">
                    <span class="visitor-inf-title">Ziyaretçi Dil:</span>
                    <span class="visitor-inf-value"><?=$visitorCookie['visitorLanguage']?></span>
                </div>
                <div class="visitor-inf-visitorRemember">
                    <span class="visitor-inf-title">Ziyaretçi Hatırla:</span>
                    <span class="visitor-inf-value"><?=($visitorCookie['visitorRemember']) ? "Evet" : "Hayır"?></span>
                </div>
                <div class="visitor-inf-visitorGetCart">
                    <span class="visitor-inf-title">Ziyaretçi Sepet Bilgisi:</span>
                    <span class="visitor-inf-value"><?=($visitorCookie['visitorGetCart']) ? "Evet" : "Hayır"?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
        $visitorSession = $casper->getVisitor();
        if(!empty($visitorSession)):
        ?>
        <div class="member-container">
            <div class="member-inf">
                <div class="member-inf-visitorUniqID">
                    <span class="member-inf-title">Ziyaretçi UniqID:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorUniqID']?></span>
                </div>
                <div class="member-inf-visitorEntryTime">
                    <span class="member-inf-title">Ziyaretçi Giriş Zamanı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorEntryTime']?></span>
                </div>
                <div class="member-inf-visitorIP">
                    <span class="member-inf-title">Ziyaretçi IP:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIP']?></span>
                </div>
                <div class="member-inf-visitorBrowser">
                    <span class="member-inf-title">Ziyaretçi Tarayıcı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorBrowser']?></span>
                </div>
                <div class="member-inf-visitorIsMember">
                    <span class="member-inf-title">Ziyaretçi Üye mi:</span>
                    <span class="member-inf-value"><?=($visitorSession['visitorIsMember']['memberStatus']) ? "Evet" : "Hayır"?></span>
                </div>
                <?php if($visitorSession['visitorIsMember']['memberStatus']): ?>
                <div class="member-inf-memberID">
                    <span class="member-inf-title">Üye ID:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberID']?></span>
                </div>
                <div class="member-inf-memberUniqID">
                    <span class="member-inf-title">Üye UniqID:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberUniqID']?></span>
                </div>
                <div class="member-inf-memberCreateDate">
                    <span class="member-inf-title">Üye Oluşturma Zamanı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberCreateDate']?></span>
                </div>
                <div class="member-inf-memberUpdateDate">
                    <span class="member-inf-title">Üye Güncelleme Zamanı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberUpdateDate']?></span>
                </div>
                <div class="member-inf-memberType">
                    <span class="member-inf-title">Üye Tipi:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberType']?></span>
                </div>
                <div class="member-inf-memberName">
                    <span class="member-inf-title">Üye Adı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberName']?></span>
                </div>
                <div class="member-inf-memberFirstName">
                    <span class="member-inf-title">Üye Adı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberFirstName']?></span>
                </div>
                <div class="member-inf-memberLastName">
                    <span class="member-inf-title">Üye Soyadı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberLastName']?></span>
                </div>
                <div class="member-inf-memberEmail">
                    <span class="member-inf-title">Üye E-Posta:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberEmail']?></span>
                </div>
                <div class="member-inf-memberPhone">
                    <span class="member-inf-title">Üye Telefon:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberPhone']?></span>
                </div>
                <div class="member-inf-memberDescription">
                    <span class="member-inf-title">Üye Açıklama:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberDescription']?></span>
                </div>
                <div class="member-inf-memberInvoiceName">
                    <span class="member-inf-title">Üye Fatura Adı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberInvoiceName']?></span>
                </div>
                <div class="member-inf-memberInvoiceTaxOffice">
                    <span class="member-inf-title">Üye Fatura Vergi Dairesi:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberInvoiceTaxOffice']?></span>
                </div>
                <div class="member-inf-memberInvoiceTaxNumber">
                    <span class="member-inf-title">Üye Fatura Vergi Numarası:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberInvoiceTaxNumber']?></span>
                </div>
                <div class="member-inf-memberActive">
                    <span class="member-inf-title">Üye Aktif mi:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorIsMember']['memberActive']?></span>
                </div>
                <?php endif; ?>
                <div class="member-inf-visitorVisitCount">
                    <span class="member-inf-title">Ziyaretçi Ziyaret Sayısı:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorVisitCount']?></span>
                </div>
                <div class="member-inf-visitorLanguage">
                    <span class="member-inf-title">Ziyaretçi Dil:</span>
                    <span class="member-inf-value"><?=$visitorSession['visitorLanguage']?></span>
                </div>
                <div class="member-inf-visitorGeo">
                    <span class="member-inf-title">Ziyaretçi Coğrafi Bilgiler:</span>
                    <span class="member-inf-value">
                    </span>
                </div>

                <?php
                if(isset($visitorSession['visitorGeo']) && is_array($visitorSession['visitorGeo'])) {
                    foreach ($visitorSession['visitorGeo'] as $key => $value) {
                        //echo $key.": ".$value."<br>";
                        echo "<div class='member-inf-visitorGeo'>" .
                            "<span class='member-inf-title'>" . $key . ":</span>" .
                            "<span class='member-inf-value'>" . $value . "</span>" .
                            "</div>";
                    }
                }
                ?>
                <div class="member-inf-visitorRemember">
                    <span class="member-inf-title">Ziyaretçi Hatırla:</span>
                    <span class="member-inf-value"><?=($visitorSession['visitorRemember']) ? "Evet" : "Hayır"?></span>
                </div>
                <div class="member-inf-visitorGetCart">
                    <span class="member-inf-title">Ziyaretçi Sepet Al:</span>
                    <span class="member-inf-value"><?=($visitorSession['visitorGetCart']) ? "Evet" : "Hayır"?></span>
                </div>

            </div>
        </div>
    <?php endif; ?>
    </div>
</aside>
<button id="show-aside-right-visitor" class="show-aside-right-visitor"><label class="arrow-label"><</label></button>
<?php } ?>