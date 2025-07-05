<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Global Pozitif Yönetici Giriş</title>
    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/rickshaw/rickshaw.css?1422792967" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/morris/morris.core.css?1420463396" />
    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<!-- BEGIN LOGIN SECTION -->
<?php

?>
<section class="section-account">
    <div class="img-backdrop" style="background-image: url('/_y/assets/img/header.jpg')"></div>
    <div class="spacer"></div>
    <div class="contain-sm card card-transparent">
        <div class="col-sm-6 card-body stepContainer" style="background-color: #fff; margin-top: 20px">

                <div class="" >
                    <div id="stepOne">
                        <span class="text-lg text-bold text-primary">PANEL GİRİŞİ 1. ADIM</span>
                        <br/><br/>
                        <form id="loginFormStepOne" class="form form-validation form-validate" action="" accept-charset="utf-8" method="post">
                            <div class="form-group">
                                <input type="email" class="form-control" id="email" name="email" required="" aria-required="true">
                                <label for="email">Yönetici Eposta Adresiniz</label>
                            </div>
                            <div class="form-group">
                                <div>
                                    <label class="radio-inline radio-styled">
                                        <input type="radio" name="verificationMethod" value="1" checked=""><span>Eposta</span>
                                    </label>
                                    <label class="radio-inline radio-styled">
                                        <input type="radio" name="verificationMethod" value="2"><span>SMS</span>
                                    </label>
                                    <p class="help-block">Doğrulama yöntemini seçin</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-content">
                                        <figure id="securityCodeContainer"><img src="/_y/captcha/1.php?captchaName=loginCaptcha" height="45" width="96" style="height: 45px;width:auto"></figure>
                                    </div>
                                    <span class="input-group-addon"> - </span>
                                    <div class="input-group-content">
                                        <button id="refreshSecurityCode" type="button" class="btn ink-reaction btn-flat btn-primary">KODU YENİLE</button>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" id="securityCode" name="securityCode" data-rule-minlength="5" maxlength="5" required="" aria-required="true" aria-invalid="true" data-rule-digits="true" value="">
                                <label for="password">Güvenlik Kodu </label>
                                <p class="help-block">Yukarıda gördüğünüz rakamları girin</p>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-6 text-right">
                                    <button id="loginFormStepOneButton" class="btn btn-primary btn-raised" type="submit">ŞİFRE GÖNDER</button>
                                </div><!--end .col -->
                            </div><!--end .row -->

                        </form>
                    </div>
                    <div class="hidden" id="stepTwo">
                        <span class="text-lg text-bold text-primary">PANEL GİRİŞİ 2. ADIM</span>
                        <br/><br/>
                        <form id="loginFormStepTwo" class="form floating-label form-validation form-validate" action="" accept-charset="utf-8" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" id="password" name="password" data-rule-minlength="5" maxlength="5" required="" aria-required="true" aria-invalid="true" data-rule-digits="true">
                                <label for="password">Gelen şifreyi girin</label>
                            </div>
                            <!-- beni hatırla -->
                            <div class="form-group">
                                <div class="checkbox checkbox-styled">
                                    <label>
                                        <input type="checkbox" name="rememberMe" checked="" value="1"><span>Beni Hatırla</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="">
                                    <button id="loginFormStepTwoButton" class="btn btn-primary btn-raised" type="submit">GİRİŞ YAP</button>
                                </div>
                            </div>

                            <div class="form-group">
                            <h4 id="timerContainer" class="text-primary text-bold lead">Kalan Süre : <span id="timer"></span></h4>
                            </div>
                            <em class="text-caption">Süre dolduktan sonra tekrar <a href="/_y/s/guvenlik/giris.php"> > giriş < </a> ekranına yönlendirileceksiniz<br>Yeni şifre talep edebilirsiniz</em>
                        </form>
                    </div>
                </div>

        </div><!--end .card-body -->
    </div><!--end .card -->
</section>
<!-- END LOGIN SECTION -->
<!-- BEGIN SIMPLE MODAL MARKUP -->
<div class="modal fade" id="loginResult" tabindex="-1" role="dialog" aria-labelledby="loginResult" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="simpleModalLabel"></h4>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- BEGIN JAVASCRIPT -->
<script src="/_y/assets/js/libs/jquery/jquery-3.7.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-3.3.2.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>

<script src="/_y/assets/js/core/source/AppNavigation.js"></script>

<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
<style>
    .stepContainer {
        margin:0 auto; float:none;
    }
</style>
<!-- END JAVASCRIPT -->
<script>
    $(document).ready(function() {

        function updateEmailInput(type, name, placeholder, labelText) {
            $('#email').attr('type', type);
            $('#email').attr('name', name);
            $('#email').attr('placeholder', placeholder);
            $('label[for="email"]').text(labelText);
            $('#email').val('');
        }

        function showModal(status, title, message) {
            var modal = $('#loginResult');
            var modalHeader = modal.find('.modal-header');
            modalHeader.addClass('text-' + status);

            modal.find('.modal-title').text(title);
            modal.find('.modal-body p').text(message);

            modal.modal('show');
        }

        $('input[name="verificationMethod"]').on('change', function() {
            var value = $(this).val();
            if (value === 2) {
                updateEmailInput('tel', 'phone', 'Telefon Numaranız', 'Telefon Numaranız');
            } else {
                updateEmailInput('email', 'email', 'Eposta Adresiniz', 'Eposta Adresiniz');
            }
        });

        $('#loginFormStepOne').on('submit', function(e) {
            e.preventDefault();
            var email = $('#email').val();
            var captcha = $('#securityCode').val();

            //email ya da captcha boşsa işlem yapma
            if (email === '' || captcha === '') {
                return false;
            }

            //botonu disable yapalım
            $('#loginFormStepOneButton').attr('disabled', 'disabled');

            $.ajax({
                url: '/?/admin/admin/post/loginWithEmailOrPhone',
                type: 'POST',
                data: {
                    email: email,
                    captcha: captcha
                },
                success: function(response) {
                    console.log(response);
                    var data = JSON.parse(response);
                    if (data.status === 'error') {
                        showModal('danger', 'Hata', data.message);
                        $('#loginFormStepOneButton').attr('disabled', false);
                    }
                    else {
                        //stepTwo'yu göster
                        $('#stepOne').addClass('hidden');
                        $('#stepTwo').removeClass('hidden');

                        //timer başlasın
                        var timer = 300;
                        var interval = setInterval(function() {
                            timer--;
                            $('#timer').text(timer);
                            if (timer === 0) {
                                clearInterval(interval);
                                window.location.href = '/_y/s/guvenlik/giris.php';
                            }
                        }, 1000);
                    }
                }
            });
        });

        $('#loginFormStepTwo').on('submit', function(e) {
            e.preventDefault();
            var password = $('#password').val();
            var email = $('#email').val();
            var rememberMe = $('input[name="rememberMe"]').is(':checked') ? 1 : 0;

            $.ajax({
                url: '/?/admin/admin/post/loginWithEmailOrPhoneAndPassword',
                type: 'POST',
                data: {
                    password: password,
                    email: email,
                    rememberMe: rememberMe
                },
                success: function (response) {
                    console.log(response);
                    var data = JSON.parse(response);
                    if (data.status === 'error') {
                        showModal('danger', 'Hata', data.message);
                    } else {
                        showModal('success', 'Başarılı', data.message);
                        window.location.href = '/_y/';
                    }
                }
            });
        });

        $("#refreshSecurityCode").on("click", function(e)
        {
            $("#securityCodeContainer").html('<img src="/_y/captcha/1.php?captchaName=loginCaptcha" height="45" width="96" style="height: 45px;width:auto">');
        });
    });
</script>
</body>
</html>
