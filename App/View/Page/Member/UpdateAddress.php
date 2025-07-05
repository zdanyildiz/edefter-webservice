<?php
/**
 * @var $visitor
 * @var $languageCode
 * @var $visitorCart
 * @var $rightCartShow
 * @var $memberLink
 * @var Helper $helper
 */
$memberInfo = $visitor['visitorIsMember'];

$addresses = $memberInfo['memberAddress'];
$countries = $memberInfo['countries'];
//$helper->writeToArray($addresses);

$addressID = $addresses['adresid'];
$addressTitle = $addresses['adresbaslik'];
$identificationNumber = $addresses['adrestcno'];
$addressName = $addresses['adresad'];
$addressSurname = $addresses['adressoyad'];
$addressCountry = $addresses['adresulke'];
$addressCity = $addresses['adressehir'];
$addressCounty = $addresses['adresilce'];
$addressArea = $addresses['adressemt'];
$addressNeighborhood = $addresses['adresmahalle'];
$addressStreet = $addresses['adresacik'];
$addressPostalCode = $addresses['postakod'];
$addressPhone = $addresses['adrestelefon'];
$addressCountryCode = $addresses['adresulkekod'];
$addressCityCode = $addresses['adressil'];


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
    <div class="member-address-container">
        <div class="member-address-form-container">
            <h1><?=_odeme_sayfasi_yeni_adres_ekle_yazi?></h1>
            <form action="/?/control/member/post/updateAddress" method="post">
                <input type="hidden" name="action" value="updateAddress">
                <input type="hidden" name="languageCode" id="languageCode" value="<?= $languageCode ?>">
                <input type="hidden" name="addressID" id="addressID" value="<?= $addressID ?>">
                <div class="form-group">
                    <label for="addressTitle"><?=_odeme_sayfasi_form_adres_baslik_yazi?>:</label>
                    <input type="text" value="<?=$addressTitle?>" class="form-control" id="addressTitle" name="addressTitle" required>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_baslik_yazi?>.</small>
                </div>
                <div class="form-group">
                    <label for="identificationNumber"><?=_odeme_sayfasi_form_adres_tc_yazi?>:</label>
                    <input type="text" value="<?=$identificationNumber?>" class="form-control" id="identificationNumber" name="identificationNumber" required>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_tc_yazi?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressName"><?=_odeme_sayfasi_form_adres_isim_yazi?>:</label>
                    <input type="text" value="<?=$addressName?>" class="form-control" id="addressName" name="addressName" required>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_isim_yazi?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressSurname"><?=_odeme_sayfasi_form_adres_soyisim_yazi?>:</label>
                    <input type="text" value="<?=$addressSurname?>" class="form-control" id="addressSurname" name="addressSurname" required>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_soyisim_yazi?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressCountry"><?=_odeme_sayfasi_form_adres_ulke?>:</label>
                    <select class="form-control" id="addressCountry" name="addressCountry" required>
                        <option value="">Ülkenizi seçiniz</option>
                        <?php $helper->printCountries($countries,$addressCountry) ?>
                    </select>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_ulke?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressCity"><?=_odeme_sayfasi_form_adres_sehir?>:</label>
                    <input type="text" value="<?=$addressCity?>" class="form-control" id="addressCityName" name="addressCityName" required>
                    <select type="text" class="form-control" id="addressCity" name="addressCity" required></select>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_sehir?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressCounty"><?=_odeme_sayfasi_form_adres_ilce?>:</label>
                    <input type="text" value="<?=$addressCounty?>" class="form-control" id="addressCountyName" name="addressCountyName" required>
                    <select type="text" class="form-control" id="addressCounty" name="addressCounty" required></select>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_ilce?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressArea"><?=_odeme_sayfasi_form_adres_semt?>:</label>
                    <input type="text" value="<?=$addressArea?>" class="form-control" id="addressAreaName" name="addressAreaName" required>
                    <select type="text" class="form-control" id="addressArea" name="addressArea" required></select>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_semt?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressNeighborhood"><?=_odeme_sayfasi_form_adres_mahalle?>:</label>
                    <input type="text" value="<?=$addressNeighborhood?>" class="form-control" id="addressNeighborhoodName" name="addressNeighborhoodName" required>
                    <select type="text" class="form-control" id="addressNeighborhood" name="addressNeighborhood" required></select>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_mahalle?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressStreet"><?=_odeme_sayfasi_form_adres_sokak?>:</label>
                    <input type="text" value="<?=$addressStreet?>" class="form-control" id="addressStreet" name="addressStreet" required>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_sokak?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressPostalCode"><?=_odeme_sayfasi_form_adres_posta_kod?>:</label>
                    <input type="text" value="<?=$addressPostalCode?>" class="form-control" id="addressPostalCode" name="addressPostalCode" required>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_posta_kod?>.</small>
                </div>
                <div class="form-group">
                    <label for="addressPhone"><?=_odeme_sayfasi_form_adres_cep_telefon_yazi?>:</label>
                    <input type="text" value="<?=$addressPhone?>" class="form-control" id="addressPhone" name="addressPhone" required>
                    <small class="form-text text-muted">*<?=_odeme_sayfasi_form_adres_cep_telefon_yazi?>.</small>
                </div>
                <button type="submit" class="btn btn-primary"><?=_odeme_sayfasi_form_buton_yazi?></button>
            </form>
        </div>
    </div>
</div>