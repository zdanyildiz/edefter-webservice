<?php
/**
 * @var string $membershipAgreementLink
 * @var string $languageCode
 */
?>
<div class="member-container">
    <div class="member-login-and-register-container">
        <div class="member-login-container">
            <h1><?=_giris_form_giris_yazi?></h1>
            <div class="member-login-form-container">
                <form action="/?/control/member/post/login" name="memberLoginForm" id="memberLoginForm" method="post">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="languageCode" value="<?=$languageCode?>">
                    <input type="hidden" name="websites" id="websites-memberLoginForm" value="">
                    <div class="form-group row">
                        <label for="email-memberLoginForm"><?=_giris_form_eposta_yazi?>:</label>
                        <input type="email" class="form-control" id="email-memberLoginForm" name="email" required>
                    </div>
                    <div class="form-group row">
                        <label for="password-memberLoginForm"><?=_giris_form_sifre_yazi?>:</label>
                        <input type="password" class="form-control" id="password-memberLoginForm" name="password" required>
                    </div>
                    <div class="form-group row remember">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember"><?=_giris_form_beni_hatirla_yazi?>.</label>
                    </div>
                    <div class="form-group">
                        <a href="javascript:void(0);" id="remind-password"><?=_giris_form_sifremi_unuttum_yazi?></a>
                    </div>
                    <input type="hidden" name="cf-turnstile-response" id="cf-token-login-form">
                    <button type="submit" class="btn btn-primary"><?=_giris_form_giris_yap_yazi?></button>
                </form>
            </div>
        </div>
        <div class="member-register-container">
            <h1><?=_uye_olun?></h1>
            <div class="member-register-form-container">
                <form name="memberReqisterForm" id="memberReqisterForm" action="/?/control/member/register" method="post">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="languageCode" value="<?=$languageCode?>">
                    <input type="hidden" name="websites" id="websites-memberReqisterForm" value="">
                    <div class="form-group">
                        <label for="name-memberReqisterForm"><?=_uye_ol_form_isim_yazi?> :</label>
                        <input type="text" class="form-control" id="name-memberReqisterForm" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="surname-memberReqisterForm"><?=_uye_ol_form_soyisim_yazi?> :</label>
                        <input type="text" class="form-control" id="surname-memberReqisterForm" name="surname" required>
                    </div>
                    <div class="form-group row">
                        <label for="phone-memberReqisterForm"><?=_uye_ol_form_cep_telefonu_yazi?> :</label>
                        <input type="text" class="form-control" id="phone-memberReqisterForm" name="phone" required>
                    </div>
                    <div class="form-group row">
                        <label for="email-memberReqisterForm"><?=_uye_ol_form_eposta_yazi?>:</label>
                        <input type="email" class="form-control" id="email-memberReqisterForm" name="email" required>
                    </div>
                    <div class="form-group row">
                        <label for="password-memberReqisterForm"><?=_uye_ol_form_sifre_yazi?>:</label>
                        <input type="password" class="form-control" id="password-memberReqisterForm" name="password" required>
                    </div>
                    <div class="form-group row terms">
                        <label for="terms-memberReqisterForm"><input type="checkbox" id="terms-memberReqisterForm" name="terms" required> <?=_uye_ol_form_sozlesme_kabul_yazi?></label>
                    </div>
                    <input type="hidden" name="cf-turnstile-response" id="cf-token-register-form">
                    <div class="form-group row terms"><a href="<?=$membershipAgreementLink?>" target="_blank">*<?=_uye_ol_form_sozlesme_link_yazi?></a></div>
                    <button type="submit" class="btn btn-primary"><?=_uye_ol_form_buton_yazi?></button>
                </form>
                <div class="alert alert-danger"><?=_uye_ol_form_eksiksiz_doldurun_yazi?></div>
            </div>
        </div>
    </div>
</div>
<div class="remind-password-form-modal">
    <span class="close-remind-password-form-modal">&times;</span>
    <div class="remind-password-form-container">
        <h1><?=_giris_form_sifre_hatirlat_baslik_yazi?></h1>
        <form id="remindPasswordForm" action="/?/control/member/post/remindPasswordByEmail" method="post">
            <input type="hidden" name="action" value="remindPasswordByEmail">
            <input type="hidden" name="languageCode" id="languageCode" value="<?= $languageCode ?>">
            <input type="hidden" name="memberID" id="memberID" value="<?= $visitor['visitorIsMember']['memberID'] ?? 0 ?>">
            <input type="hidden" name="websites" id="websites-remindPasswordForm" value="">
            <div class="form-group">
                <label for="email-remindPasswordForm"><?=_giris_form_eposta_yazi?>:</label>
                <input type="email" class="form-control" id="email-remindPasswordForm" name="email" value="" required>
            </div>
            <input type="hidden" name="cf-turnstile-response" id="cf-token-remind-password-form">
            <button type="submit" class="btn btn-primary"><?=_giris_form_sifre_hatirlat_buton_yazi?></button>
        </form>
    </div>
</div>
