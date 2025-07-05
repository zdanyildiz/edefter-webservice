<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$buttonName = "Ekle";

$languageID = $_GET["languageID"] ?? 1;
$languageID = intval($languageID);

include_once MODEL."/Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);
$languages = $adminLanguage->getLanguages();

$providerID = $_GET["providerID"] ?? 0;
$providerID = intval($providerID);

if($providerID > 0) {
    include_once MODEL."/Admin/AdminPaymentGateway.php";
    $adminPaymentGateway = new AdminPaymentGateway($db);
    $paymentProvider = $adminPaymentGateway->getProvider($providerID);
    if(!empty($paymentProvider)){
        $providerID = $paymentProvider["id"];
        $providerName = $paymentProvider["name"];
        $paymentDescription = $paymentProvider["description"];
        $providerStatus = $paymentProvider["status"];
        $providerLanguageCode = $paymentProvider["languageCode"];
        $providerSettings = $paymentProvider["settings"];
        $buttonName = "Güncelle";
    }


    //paytr sanal pos değişkenleri
    //merchantID, merchantKey, merchantSalt, storeKey

    //garantibpay sanal pos değişkenleri
    //merchantID, terminalID, provUserID, provUserPassword, garantiPayProvUserID, garantiPayProvUserPassword, storeKey

    //iyzico sanal pos değişkenleri
    //api, key, apiadres
}
$providerID = $providerID ?? 0;
$providerName = $providerName ?? "";
$paymentDescription = $paymentDescription ?? "";
$providerStatus = $providerStatus ?? 1;
$providerLanguageCode = $providerLanguageCode ?? "tr";
$providerSettings = $providerSettings ?? [];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Ödeme Aracısı Ekle Pozitif ETicaret</title>

    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">
    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<!-- BEGIN HEADER-->
<?php require_once(ROOT."/_y/s/b/header.php");?>
<!-- END HEADER-->
<!-- BEGIN BASE-->
<div id="base">
    <!-- BEGIN CONTENT-->
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">Ödeme Sağlayıcısı Ekle / Güncelle</a></li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <!-- BEGIN ADD CONTACTS FORM -->
                    <div class="col-md-12">
                        <div class="card">
                            <form name="addPaymentMethodForm" id="addPaymentMethodForm" class="form" role="form" method="post">
                                <input type="hidden" name="providerID" id="providerID" value="<?=$providerID?>">
                                <!-- BEGIN DEFAULT FORM ITEMS -->
                                <div class="card">
                                    <div class="card-body ">
                                        <div class="form-group">
                                            <!-- dil listesi gelecek -->
                                            <select name="providerLanguageCode" id="providerLanguageCode" class="form-control">
                                                <?php foreach($languages as $language){
                                                    $selected = "";
                                                    if($language['languageCode'] == $providerLanguageCode) {
                                                        $selected = "selected";
                                                    }
                                                    ?>
                                                    <option value="<?php echo $language['languageCode']; ?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <p class="help-block">ÖDEME ARACISI İÇİN DİL SEÇİN!</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                name="providerName"
                                                                id="providerName"
                                                                value="<?=$providerName?>"
                                                                placeholder="Sağlayıcı Adını Yazın"  >
                                                        <label for="providerName">Sağlayıcı Adını Yazın</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <textarea
                                                                class="form-control"
                                                                name="providerDescription"
                                                                id="providerDescription"
                                                                placeholder="Açıklama"><?=$paymentDescription?></textarea>
                                                        <label for="providerDescription">Açıklama</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="radio-inline radio-styled">
                                                        <input type="radio" name="providerStatus" value="1" <?php if($providerStatus==1) echo "checked";?>><span>Aktif</span>
                                                    </label>
                                                    <label class="radio-inline radio-styled">
                                                        <input type="radio" name="providerStatus" value="0" <?php if($providerStatus==0) echo "checked";?>><span>Pasif</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row card-body">
                                        <!-- Dinamik Alanlar İçin Boş Bir Konteyner -->
                                        <div id="dynamic-fields-container">
                                            <?php
                                            if(!empty($providerSettings)){
                                                foreach($providerSettings as $setting){
                                                    ?>
                                                    <div class="row dynamic-field">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="dynamicField[<?=$setting['id']?>][key]"
                                                                        value="<?=$setting['key']?>"
                                                                        placeholder="Özellik Adı">
                                                                <label for="dynamicField[<?=$setting['id']?>][key]">Özellik Adı</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="dynamicField[<?=$setting['id']?>][value]"
                                                                        value="<?=$setting['value']?>"
                                                                        placeholder="Özellik Değeri">
                                                                <label for="dynamicField[<?=$setting['id']?>][value]">Özellik Değeri</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <!-- Dinamik Alan Ekleme Butonu -->
                                        <div class="card-actionbar">
                                            <div class="card-actionbar-row">
                                                <button type="button" id="add-field-button" class="btn btn-secondary btn-sm btn-accent-light" style="float: left;margin-top: 20px">Yeni Alan Ekle</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-actionbar">
                                        <div class="card-actionbar-row">
                                            <button type="submit" class="btn btn-primary btn-default"><?=$buttonName?></button>
                                        </div>
                                    </div>
                            
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- END CONTENT -->

    <!-- BEGIN MENUBAR-->
    <?php require_once(ROOT."/_y/s/b/menu.php");?>
    <!-- END MENUBAR -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-danger">
                    <header class="modal-title" id="alertModalLabel">Uyarı</header>
                    <div class="tools">
                        <div class="btn-group">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p id="alertMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- BEGIN JAVASCRIPT -->

<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>
<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<script>
    $("#addPaymentGatewayphp").addClass("active");

    $(document).ready(function() {
        var fieldCount = 0; // Dinamik alanların sayısını takip eder

        $(document).on("click", '#add-field-button', function() {
            fieldCount++;
            var fieldHtml = `
            <div class="row dynamic-field">
                <div class="col-sm-6">
                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control"
                            name="dynamicField[` + fieldCount + `][key]"
                            placeholder="Özellik Adı">
                        <label for="dynamicField[` + fieldCount + `][key]">Özellik Adı</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input
                            type="text"
                            class="form-control"
                            name="dynamicField[` + fieldCount + `][value]"
                            value="Değeri buraya girin"
                            placeholder="Özellik Değeri">
                        <label for="dynamicField[` + fieldCount + `][value]">Özellik Değeri</label>
                    </div>
                </div>
            </div>`;
            $('#dynamic-fields-container').append(fieldHtml);
        });

        $(document).on("submit", "#addPaymentMethodForm", function(e) {
            e.preventDefault();

            // Sağlayıcı adı kontrolü
            if($("#paymentProviderName").val() == ""){
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Sağlayıcı adı boş olamaz");
                $("#alertModal").modal("show");
                return;
            }

            // .dynamic-field varsa inputları boş olamaz
            var valid = true;
            $(".dynamic-field").each(function() {
                var key = $(this).find("input[name$='[key]']").val();
                var value = $(this).find("input[name$='[value]']").val();
                //ikiside boşsa satırı silelim
                if (key == "" && value == "") {
                    $(this).remove();
                    //bir sonraki döngü devam etsin
                    return true;
                }
                if(key == "" || value == "") {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text("Özellik adı ve değeri boş olamaz");
                    $("#alertModal").modal("show");
                    valid = false;
                    return false; // .each döngüsünü durdur
                } else {
                    // Başında ve sonunda boşluk varsa temizle
                    key = key.trim();
                    value = value.trim();
                    $(this).find("input[name$='[key]']").val(key);
                    $(this).find("input[name$='[value]']").val(value);
                }
            });

            if (!valid) {
                return; // Form gönderimini durdur
            }
            //dynamic-field en az 2 tane olmalı
            if($(".dynamic-field").length < 2){
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("En az 2 özellik eklemelisiniz");
                $("#alertModal").modal("show");
                return;
            }

            var action = "addProvider";
            var providerID = $("#providerID").val();
            if(providerID > 0){
                action = "updateProvider";
            }

            var form = $(this);
            var formData = form.serialize();
            formData += "&action=" + action;

            var url = "/App/Controller/Admin/AdminPaymentGatewayController.php";
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function (data) {
                    var response = JSON.parse(data);
                    if (response.status == "success") {
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                        $("#alertMessage").text(response.message);
                        $("#alertModal").modal("show");
                        setTimeout(function() {
                            window.location.href = "/_y/s/s/odemeyontemi/PaymentGatewayList.php";
                        }, 2000);
                    } else {
                        $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                        $("#alertMessage").text(response.message);
                        $("#alertModal").modal("show");
                    }
                }
            });
        });

    });

</script>
<!-- END JAVASCRIPT -->
</body>
</html>
