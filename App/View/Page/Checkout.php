<?php
/**
 * @var Casper $casper
 * @var Helper $helper
 * @var string $languageCode
 * @var Session $session
 * @var string $paymentLink
 */
$config = $casper->getConfig();
$helper = $config->Helper;
$visitor = $casper->getVisitor();

$visitorCart = $visitor["visitorCart"];

$visitorIsMember = $visitor['visitorIsMember'];
$visitorMemberStatus = $visitorIsMember['memberStatus'];

if($visitorMemberStatus){
    $addresses = $visitor['visitorIsMember']['memberAddress'] ?? [];

    $memberInvoiceName = $visitor['visitorIsMember']['memberInvoiceName'];
    $memberInvoiceTaxOffice = $visitor['visitorIsMember']['memberInvoiceTaxOffice'];
    $memberInvoiceTaxNumber = $visitor['visitorIsMember']['memberInvoiceTaxNumber'];

    $memberFirstName = $visitor['visitorIsMember']['memberFirstName'];
    $memberLastName = $visitor['visitorIsMember']['memberLastName'];

    if(empty($memberFirstName) || empty($memberLastName)){
        $session->addSession('popup', [
            'status' => 'warning',
            'message' => "Eksik bilgileriniz var. Lütfen profilinizi güncelleyiniz.",
            'position' => 'top-right',
            'width' => '300px',
            'height' => '100px',
            'closeButton' => true,
            'autoClose' => false,
            'animation' => true,
        ]);

        $siteConfig = $casper->getSiteConfig();
        $pageLinks = $siteConfig['specificPageLinks'];

        $memberLinkItem = array_filter($pageLinks, function($pageLink) {
            return $pageLink['sayfatip'] == 17;
        });
        $memberLinkItem = reset($memberLinkItem);
        $memberLink = $memberLinkItem['link'];

        echo "<script>window.location.href = '$memberLink';</script>";
    }
}
else{
    $addresses = [];
    $memberInvoiceName = "";
    $memberInvoiceTaxOffice = "";
    $memberInvoiceTaxNumber = "";
}

$paymentButtonDisabled = "";

if(empty($addresses)) {
    $paymentButtonDisabled = "disabled";
}


$countries = $visitor["countries"];

$distanceSalesAgreementLink = $distanceSalesAgreementLink ?? "";

/*$siteConfig = $casper->getSiteConfig();
print_r($siteConfig);*/
?>
<div class="payment-container">
    <h1><?=_odeme_sayfasi_hemenal_yazi?></h1>
    <div class="payment-user-and-order-container">
        <div class="payment-user-container">
            <div class="address-card-container">
                <h1 class="addAddress"><?=_odeme_sayfasi_yeni_adres_ekle_yazi?></h1>
                <?php if(!empty($addresses)): ?>
                    <div class="address-card">
                    <div class="invoice-container">
                        <div class="form-group row">
                            <label for="invoiceName"><?=_odeme_sayfasi_fatura_unvan_yazi?>:</label>
                            <input type="text" class="form-control" id="invoiceName" name="invoiceName" value="<?= $memberInvoiceName ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="invoiceTaxOffice"><?=_odeme_sayfasi_vergi_dairesi_yazi?>:</label>
                            <input type="text" class="form-control" id="invoiceTaxOffice" name="invoiceTaxOffice" value="<?= $memberInvoiceTaxOffice ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="invoiceTaxNumber"><?=_odeme_sayfasi_vergi_no_yazi?>:</label>
                            <input type="text" class="form-control" id="invoiceTaxNumber" name="invoiceTaxNumber" value="<?= $memberInvoiceTaxNumber ?>" required>
                        </div>
                    </div>
                    </div>
                    <?php foreach ($addresses as $address) { ?>
                    <div class="address-card" id="address-<?=$address['adresid']?>">
                        <h2><?=$address['adresbaslik']?></h2>
                        <p><?=$address['adresad']?> <?=$address['adressoyad']?> - <?=$address['adrestelefon']?></p>
                        <p><?=$address['adresulke']?>, <?=$address['adressehir']?>, <?=$address['adresilce']?>, <?=$address['adressemt']?>, <?=$address['adresmahalle']?></p>
                        <p><?=$address['adresacik']?> <?=$address['postakod']?></p>

                        <label class="cargo"><input type="radio" name="cargoAddressID" value="<?=$address['adresid']?>"> <?=_odeme_sayfasi_kargo_adresi_olarak_kullan_yazi?></label>

                        <label class="invoice"><input type="radio" name="invoiceAddressID" value="<?=$address['adresid']?>"> <?=_odeme_sayfasi_fatura_adresi_olarak_kullan_yazi?></label>
                    </div>
                <?php } ?>
                <?php else: ?>
                    <div class="alert alert-warning"><?=_odeme_sayfasi_devam_etmek_icin_giris_yapin_yazi?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="payment-order-container">
            <h1>Sepetim</h1>
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
                        $imageRoot = imgRoot."?imagePath=".trim($productImage)."&width=100&height=100"

                       ?>
                        <div class="cart-item cart-checked" id="cart-item-<?=$cartUniqID?>">
                            <div class="cart-item-checkbox">
                                <input class="tcbx-3" type="checkbox"
                                       name="cartItem[]"
                                       id="checkbox-<?=$cartUniqID?>" data-cartuniqid="<?=$cartUniqID?>"
                                       value="<?=$cartUniqID?>"
                                       checked>
                                <label for="checkbox-<?=$cartUniqID?>" class="toggle"><span></span></label>
                                <style>
                                    .cart-item-checkbox #checkbox-<?=$cartUniqID?>:checked + .toggle:before {
                                        background: #947ADA;
                                    }
                                    .cart-item-checkbox #checkbox-<?=$cartUniqID?>:checked + .toggle span {
                                        background: #4F2EDC;
                                        transform: translateX(20px);
                                        transition: all 0.2s cubic-bezier(0.8, 0.4, 0.3, 1.25), background 0.15s ease;
                                        box-shadow: 0 3px 8px rgba(79, 46, 220, 0.2);
                                    }
                                    .cart-item-checkbox #checkbox-<?=$cartUniqID?>:checked + .toggle span:before {
                                        transform: scale(1);
                                        opacity: 0;
                                        transition: all 0.4s ease;
                                    }
                                </style>
                            </div>
                            <div class="cart-image-container">
                                <img src="<?php echo $imageRoot; ?>" class="cart-item-image" alt="<?php echo $productName; ?>" loading="lazy" width="100" height="100">
                            </div>
                            <div class="cart-item-details">
                                <div class="cart-item-title"> <?php echo $productName; ?></div>
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
                            <div class="cart-item-price" id="price-<?=$cartUniqID?>">
                                <?=$productCurrencySymbol?> <?=$helper->formatCurrency($productPrice)?> x <?=$productQuantity?> <?=$productUnitName?>
                            </div>
                            <div class="cart-totals"
                                 id="cart-totals-<?=$cartUniqID?>"
                                 data-totalprice="<?=str_replace(",",".",$productTotalPrice)?>"
                                 data-discountamount="<?=$productDiscountAmount?>"
                                 data-quantity="<?=$productQuantity?>"
                                 data-currencysymbol="<?=$productCurrencySymbol?>"
                            >
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
                        <li class="total-quantity"><?=_sepet_toplam_urun_adedi?>: <?php echo $totalQuantity; ?></li>
                        <li class="total-price"><?=_sepet_sepet_toplam_tutar_yazi?> <?php echo $helper->formatCurrency($totalPrice, 2); ?> <?=$productCurrencySymbol?></li>
                        <?php if($totalDiscountAmount>0){ ?>
                            <li class="total-discount"><?=_sepet_indirim_toplam_tutar_yazi?> <i><?php echo $helper->formatCurrency($totalDiscountAmount, 2); ?> <?=$productCurrencySymbol?></i></li>
                            <li class="total-discounted-price"><?=_sepet_indirimli_toplam_tutar_yazi?> <?php echo $helper->formatCurrency($totalPrice-$totalDiscountAmount, 2); ?> <?=$productCurrencySymbol?></li>
                        <?php } ?>
                    </ul>

                    <div class="form-container">
                        <div class="form-group row">
                            <label for="customerNote"><?=_odeme_sayfasi_musteri_notu_yazi?>:</label>
                            <textarea class="form-control" id="customerNote" name="customerNote"></textarea>
                        </div>

                        <div class="form-group row">
                            <label for="acceptContract" class="link">
                                <input type="checkbox" class="form-control" id="acceptContract" name="acceptContract" required><a href="<?=$distanceSalesAgreementLink?>" target="_blank"><?=_odeme_sayfasi_sozlesme_sartlari_kabul_yazi?></a></label>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" id="csrf_token-paymentForm" value="<?=$helper->generateCsrfToken()?>">
                    <a href="<?=$paymentLink?>" id="submit" class="<?=$paymentButtonDisabled?>" data-languageCode="<?=$languageCode?>"><?=_sepet_odeme_yap_yazi?></a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <p><?=_sepet_sepetim_urun_yok_yazi?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="address-form-modal">
    <span class="close-address-form-modal btn">&times;</span>
    <div class="address-form-container">
        <h1><?=_form_adres_baslik_yazi?></h1>

        <form id="paymentAddressForm" action="/?/control/member/post/addAddress" method="post">
            <h1>* <?=_odeme_sayfasi_form_tum_alan_zorunlu_yazi?></h1>
            <input type="hidden" name="action" value="addAddress">
            <input type="hidden" name="languageCode" id="languageCode" value="<?= $languageCode ?>">
            <input type="hidden" name="memberID" id="memberID" value="<?= $visitor['visitorIsMember']['memberID'] ?? 0 ?>">
            <input type="hidden" name="csrf_token" id="csrf_token-paymentAddressForm" value="<?=$helper->generateCsrfToken()?>">
            <div class="form-group">
                <label for="telephone"><?=_form_adres_cep_yazi?>:</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="<?= $visitor['visitorIsMember']['memberPhone'] ?? ""?>" required>
                <small class="form-text text-muted">*<?=_form_adres_cep_yazi?>.</small>
            </div>
            <div class="form-group">
                <label for="email"><?=_form_adres_eposta_yazi?>:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $visitor['visitorIsMember']['memberEmail'] ?? ""?>" required>
                <small class="form-text text-muted">*<?=_form_adres_eposta_yazi?>.</small>
            </div>
            <div class="form-group row">
                <span><?=_odeme_sayfasi_form_yeni_adres_ekle_yazi?></span>
            </div>
            <div class="form-group">
                <label for="addressTitle"><?=_odeme_sayfasi_form_adres_baslik_yazi?>:</label>
                <input type="text" class="form-control" id="addressTitle" name="addressTitle" required>
                <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_baslik_yazi?>.</small>
            </div>
            <div class="form-group">
                <label for="identificationNumber"><?=_form_adres_tc_yazi?></label>
                <input type="text" class="form-control" id="identificationNumber" name="identificationNumber" value="<?=$visitor['visitorIsMember']['memberIdentificationNumber'] ?? ""?>" required>
                <small class="form-text text-muted">*<?=_form_adres_tc_yazi?>.</small>
            </div>
            <div class="form-group">
                <label for="name"><?=_form_adres_ad_yazi?>:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $visitor['visitorIsMember']['memberFirstName'] ?? ''?>" required>
                <small class="form-text text-muted">*<?=_form_adres_ad_yazi?>.</small>
            </div>
            <div class="form-group">
                <label for="surname"><?=_form_adres_soyad_yazi?>:</label>
                <input type="text" class="form-control" id="surname" name="surname" value="<?= $visitor['visitorIsMember']['memberLastName'] ?? ''?>" required>
                <small class="form-text text-muted">*<?=_form_adres_soyad_yazi?>.</small>
            </div>
            <div class="form-group">
                <label for="addressCountry"><?=_form_adres_ulke?>:</label>
                <select class="form-control" id="addressCountry" name="addressCountry" required>
                    <option value="">Ülkenizi seçiniz</option>
                    <?php $helper->printCountries($countries) ?>
                </select>
                <small class="form-text text-muted">*<?=_form_adres_ulke?>.</small>
            </div>
            <div class="form-group" id="addressCityContainer">
                <label for="addressCity"><?=_form_adres_sehir?>:</label>
                <select class="form-control" id="addressCity" name="addressCity" required></select>
                <small class="form-text text-muted">*<?=_form_adres_sehir?>.</small>
            </div>
            <div class="form-group" id="addressCountyContainer">
                <label for="addressCounty"><?=_form_adres_ilce?>:</label>
                <select class="form-control" id="addressCounty" name="addressCounty" required></select>
                <small class="form-text text-muted">*<?=_form_adres_ilce?>.</small>
            </div>
            <div class="form-group" id="addressAreaContainer">
                <label for="addressArea"><?=_form_adres_semt?>:</label>
                <select class="form-control" id="addressArea" name="addressArea" required></select>
                <small class="form-text text-muted">*<?=_form_adres_semt?>.</small>
            </div>
            <div class="form-group" id="addressNeighborhoodContainer">
                <label for="addressNeighborhood"><?=_form_adres_mahalle?>:</label>
                <select class="form-control" id="addressNeighborhood" name="addressNeighborhood" required></select>
                <small class="form-text text-muted">*<?=_form_adres_mahalle?>.</small>
            </div>
            <div class="form-group">
                <label for="addressPostalCode"><?=_form_adres_posta_kod?>:</label>
                <input type="text" class="form-control" id="addressPostalCode" name="addressPostalCode" required>
                <small class="form-text text-muted">*<?=_form_adres_posta_kod?>.</small>
            </div>
            <div class="form-group row">
                <label for="addressStreet"><?=_form_adres_sokak?>:</label>
                <input type="text" class="form-control" id="addressStreet" name="addressStreet" required>
                <small class="form-text text-muted">*<?=_form_adres_sokak?>.</small>
            </div>
            <div class="form-group row">
                <span><?=_odeme_sayfasi_form_fatura_bilgileri_yazi?></span>
            </div>
            <div class="form-group row">
                <label for="invoiceName"><?=_form_fatura_unvan_yazi?></label>
                <input type="text" class="form-control" id="invoiceName" name="invoiceName" value="<?=$memberInvoiceName?>" required>
                <small class="form-text text-muted">*<?=_form_fatura_unvan_yazi?>.</small>
            </div>
            <div class="form-group">
                <label for="invoiceTaxOffice"><?=_form_fatura_vergi_dairesi_yazi?></label>
                <input type="text" class="form-control" id="invoiceTaxOffice" name="invoiceTaxOffice" value="<?=$memberInvoiceTaxOffice?>" required>
                <small class="form-text text-muted">*<?=_form_fatura_vergi_dairesi_yazi?>.</small>
            </div>
            <div class="form-group">
                <label for="invoiceTaxNumber"><?=_form_fatura_vergi_no_yazi?></label>
                <input type="text" class="form-control" id="invoiceTaxNumber" name="invoiceTaxNumber" value="<?=$memberInvoiceTaxNumber?>" required>
                <small class="form-text text-muted">*<?=_form_fatura_vergi_no_yazi?>.</small>
            </div>
            <button type="submit" class="btn btn-primary"><?=_odeme_sayfasi_form_buton_yazi?></button>
        </form>
    </div>
</div>