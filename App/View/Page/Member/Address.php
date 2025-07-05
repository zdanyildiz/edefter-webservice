<?php
/**
 * @var array $visitor
 * @var string $languageCode
 * @var Helper $helper
 */

$memberInfo = $visitor['visitorIsMember'];

$addresses = $memberInfo['memberAddress'];
$countries = $memberInfo['countries'];

$identificationNumber = $memberInfo['memberIdentificationNumber'];
$memberID = $memberInfo['memberID'];
$memberUniqID = $memberInfo['memberUniqID'];
$memberCreateDate = $memberInfo['memberCreateDate'];
$memberUpdateDate = $memberInfo['memberUpdateDate'];
$memberType = $memberInfo['memberType'];
$memberName = $memberInfo['memberName'];
$memberFirstName = $memberInfo['memberFirstName'];
$memberLastName = $memberInfo['memberLastName'];
$memberEmail = $memberInfo['memberEmail'];
$memberPhone = $memberInfo['memberPhone'];
$memberDescription = $memberInfo['memberDescription'];
$memberInvoiceName = $memberInfo['memberInvoiceName'];
$memberInvoiceTaxOffice = $memberInfo['memberInvoiceTaxOffice'];
$memberInvoiceTaxNumber = $memberInfo['memberInvoiceTaxNumber'];
$memberActive = $memberInfo['memberActive'];
?>
<div class="member-container">
    <div class="address-card-container">
        <h1><?=_uye_sayfasi_adreslerim_yazi?></h1>
        <?php foreach ($addresses as $address) { ?>
            <div class="address-card">
                <h2><?=$address['adresbaslik']?></h2>
                <p><?=$address['adresad']?> <?=$address['adressoyad']?></p>
                <p><?=$address['adresulke']?>, <?=$address['adressehir']?>, <?=$address['adresilce']?>, <?=$address['adressemt']?>, <?=$address['adresmahalle']?></p>
                <p><?=$address['postakod']?></p>
                <p><?=$address['adresacik']?></p>
                <p><?=$address['adrestelefon']?></p>
                <a href="/?/control/member/get/getAddressByID&addressID=<?=$address['adresid']?>&languageCode=<?=$languageCode?>" class="btn btn-warning"><?=_uye_sayfasi_guncelle_yazi?></a>
                <a href="/?/control/member/get/deleteAddress&addressID=<?=$address['adresid']?>&languageCode=<?=$languageCode?>" class="btn btn-danger" data-languagecode="<?=$languageCode?>"><?=_uye_sayfasi_sil_yazi?></a>
            </div>
        <?php } ?>
    </div>
    <div class="member-address-container">
        <div class="member-address-form-container">
            <h1><?=_form_adres_baslik_yazi?></h1>
            <form action="/?/control/member/post/addAddress" method="post">
                <input type="hidden" name="action" value="addAddress">
                <input type="hidden" name="languageCode" id="languageCode" value="<?= $languageCode ?>">
                <div class="form-group">
                    <label for="addressTitle"><?=_form_adres_baslik_yazi?>:</label>
                    <input type="text" class="form-control" id="addressTitle" name="addressTitle" required>
                    <small class="form-text text-muted">*<?=_form_adres_baslik_yazi?>.</small>
                </div>
                <div class="form-group">
                    <label for="identificationNumber"><?=_form_adres_tc_yazi?>:</label>
                    <input type="text" class="form-control" id="identificationNumber" name="identificationNumber" required>
                    <small class="form-text text-muted">*<?=_form_adres_tc_yazi?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressName"><?=_form_adres_ad_yazi?>:</label>
                    <input type="text" class="form-control" id="addressName" name="addressName" required>
                    <small class="form-text text-muted">*<?=_form_adres_ad_yazi?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressSurname"><?=_form_adres_soyad_yazi?>:</label>
                    <input type="text" class="form-control" id="addressSurname" name="addressSurname" required>
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
                <div class="form-group">
                    <label for="addressCity"><?=_form_adres_sehir?>:</label>
                    <input type="text" class="form-control" id="addressCityName" name="addressCityName" required>
                    <select type="text" class="form-control" id="addressCity" name="addressCity" required></select>
                    <small class="form-text text-muted">*<?=_form_adres_sehir?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressCounty"><?=_form_adres_ilce?>:</label>
                    <input type="text" class="form-control" id="addressCountyName" name="addressCountyName" required>
                    <select type="text" class="form-control" id="addressCounty" name="addressCounty" required></select>
                    <small class="form-text text-muted">*<?=_form_adres_ilce?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressArea"><?=_form_adres_semt?>:</label>
                    <input type="text" class="form-control" id="addressAreaName" name="addressAreaName" required>
                    <select type="text" class="form-control" id="addressArea" name="addressArea" required></select>
                    <small class="form-text text-muted">*<?=_form_adres_semt?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressNeighborhood"><?=_form_adres_mahalle?>:</label>
                    <input type="text" class="form-control" id="addressNeighborhoodName" name="addressNeighborhoodName" required>
                    <select type="text" class="form-control" id="addressNeighborhood" name="addressNeighborhood" required></select>
                    <small class="form-text text-muted">*<?=_form_adres_mahalle?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressStreet"><?=_form_adres_sokak?>:</label>
                    <input type="text" class="form-control" id="addressStreet" name="addressStreet" required>
                    <small class="form-text text-muted">*<?=_form_adres_sokak?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressPostalCode"><?=_form_adres_posta_kod?>:</label>
                    <input type="text" class="form-control" id="addressPostalCode" name="addressPostalCode" required>
                    <small class="form-text text-muted">*<?=_form_adres_posta_kod?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressPhone"><?=_form_adres_cep_yazi?>:</label>
                    <input type="text" class="form-control" id="addressPhone" name="addressPhone" required>
                    <small class="form-text text-muted">*<?=_form_adres_cep_yazi?>.</small>
                </div>
                <button type="submit" class="btn btn-primary"><?=_uye_sayfasi_form_adres_ekle_buton?></button>
            </form>
        </div>
    </div>
</div>

