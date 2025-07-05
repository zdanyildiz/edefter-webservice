<?php
/**
 * @var array $memberStatus
 * @var array $visitor
 * @var string $languageCode
 * @var Config $config
 */
//echo "<pre>";print_r($_SESSION);exit;
if ($memberStatus) {
    $memberInfo = $visitor['visitorIsMember'];
    $identificationNumber = $memberInfo['memberIdentificationNumber'] ?? '';
    $memberID = $memberInfo['memberID'];
    $memberUniqID = $memberInfo['memberUniqID'] ?? '';
    $memberCreateDate = $memberInfo['memberCreateDate'] ?? '';
    $memberUpdateDate = $memberInfo['memberUpdateDate'] ?? '';
    $memberType = $memberInfo['memberType'] ?? '';
    $memberName = $memberInfo['memberName'] ?? '';
    $memberFirstName = $memberInfo['memberFirstName'] ?? '';
    $memberLastName = $memberInfo['memberLastName'] ?? '';
    $memberEmail = $memberInfo['memberEmail'] ?? '';
    $memberPhone = $memberInfo['memberPhone'] ?? '';
    $memberDescription = $memberInfo['memberDescription'] ?? '';
    $memberInvoiceName = $memberInfo['memberInvoiceName'] ?? '';
    $memberInvoiceTaxOffice = $memberInfo['memberInvoiceTaxOffice'] ?? '';
    $memberInvoiceTaxNumber = $memberInfo['memberInvoiceTaxNumber'] ?? '';
    $memberActive = $memberInfo['memberActive'] ?? '';
}
?>
<div class="member-container">
    <div class="member-user-and-password-container">
    <div class="member-card-container">
        <h1>Bilgilerim</h1>
        <div class="member-card">
            <form action="/?/control/member/post/updateMember" method="post">
                <input type="hidden" name="action" value="updateMember">
                <input type="hidden" name="memberID" value="<?= $memberID ?>">
                <input type="hidden" name="languageCode" value="<?= $languageCode ?>">
                <input type="hidden" name="csrf_token" id="csrf_token-memberUpdateForm" value="<?=$helper->generateCsrfToken()?>">
                <div class="form-group row">
                    <label for="identificationNumber"><?=_form_adres_tc_yazi?>:</label>
                    <input type="text" class="form-control" id="identificationNumber" name="identificationNumber" value="<?= $identificationNumber ?>" required>
                </div>
                <div class="form-group">
                    <label for="name"><?=_form_adres_ad_yazi?>:</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $memberFirstName ?>" required>
                </div>
                <div class="form-group">
                    <label for="surname"><?=_form_adres_soyad_yazi?>:</label>
                    <input type="text" class="form-control" id="surname" name="surname" value="<?= $memberLastName ?>" required>
                </div>
                <div class="form-group">
                    <label for="telephone"><?=_form_adres_cep_yazi?>:</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?= $memberPhone ?>" required>
                </div>
                <div class="form-group">
                    <label for="email"><?=_form_adres_eposta_yazi?>:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $memberEmail ?>" required>
                </div>
                <div class="form-group row">
                    <label for="invoiceName"><?=_form_fatura_unvan_yazi?>:</label>
                    <input type="text" class="form-control" id="invoiceName" name="invoiceName" value="<?= $memberInvoiceName ?>" required>
                </div>
                <div class="form-group">
                    <label for="invoiceTaxOffice"><?=_form_fatura_vergi_dairesi_yazi?>:</label>
                    <input type="text" class="form-control" id="invoiceTaxOffice" name="invoiceTaxOffice" value="<?= $memberInvoiceTaxOffice ?>" required>
                </div>
                <div class="form-group">
                    <label for="invoiceTaxNumber"><?=_form_fatura_vergi_no_yazi?>:</label>
                    <input type="text" class="form-control" id="invoiceTaxNumber" name="invoiceTaxNumber" value="<?= $memberInvoiceTaxNumber ?>" required>
                </div>
                <button type="submit" class="btn btn-primary"><?=_form_buton_yazi?></button>
            </form>
        </div>
    </div>
    <div class="member-password-container">
        <h1><?=_form_sifre_degistir?></h1>
        <form action="/?/control/member/post/updatePassword" method="post">
            <input type="hidden" name="action" value="updatePassword">
            <input type="hidden" name="memberID" value="<?= $memberID ?>">
            <input type="hidden" name="languageCode" value="<?= $languageCode ?>">
            <input type="hidden" name="csrf_token" id="csrf_token-UpdatePasswordForm" value="<?=$helper->generateCsrfToken()?>">
            <div class="form-group row">
                <label for="password"><?=_uyelik_form_eski_sifre?>:</label>
                <input type="password" class="form-control" id="password" name="password" value="<?=$_SESSION['passwordReset'] ?? ''?>" required>
            </div>
            <div class="form-group">
                <label for="newPassword"><?=_uyelik_form_yeni_sifre?>:</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
            </div>
            <div class="form-group">
                <label for="newPasswordRe"><?=_uyelik_form_yeni_sifre_tekrar?>:</label>
                <input type="password" class="form-control" id="newPasswordRe" name="newPasswordRe" required>
            </div>
            <div class="form-group">
                <a href="javascript:void(0);" id="remind-password-in-profile"><?=_giris_form_sifremi_unuttum_yazi?></a>
            </div>
            <button type="submit" class="btn btn-primary"><?=_giris_form_sifre_hatirlat_buton_yazi?></button>
        </form>
    </div>
    </div>
</div>
<div class="remind-password-form-modal">
    <span class="close-remind-password-form-modal">&times;</span>
    <div class="remind-password-form-container">
        <h1><?=_giris_form_sifre_hatirlat_baslik_yazi?></h1>

        <form id="remindPasswordForm" action="/?/control/member/post/remindPasswordByEmailWithUserID" method="post">
            <input type="hidden" name="action" value="remindPasswordByEmailWithUserID">
            <input type="hidden" name="languageCode" id="languageCode" value="<?= $languageCode ?>">
            <input type="hidden" name="memberID" id="memberID" value="<?= $visitor['visitorIsMember']['memberID'] ?>">
            <input type="hidden" name="csrf_token" id="csrf_token-reminPasswordForm" value="<?=$helper->generateCsrfToken()?>">
            <div class="form-group">
                <label for="email-remindPasswordFormInProfile"><?=_uye_ol_form_eposta_yazi?>:</label>
                <input type="email" class="form-control" name="email" id="email-remindPasswordFormInProfile" value="<?= $memberEmail ?>" readonly required>
            </div>

            <button type="submit" class="btn btn-primary"><?=_giris_form_sifre_hatirlat_buton_yazi?></button>
        </form>
    </div>
</div>
