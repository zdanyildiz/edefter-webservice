<?php  require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 * @var Helper $helper
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL ."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/AdminSMTPSettings.php';
$smtpSettingsModel = new AdminSMTPSettings($db);

$smtpSettings = $smtpSettingsModel->getSMTPSettings($languageID);
if(!empty($smtpSettings)){
    $smtpSettings = $smtpSettings[0];
    $smtpSettingsID = $smtpSettings['id'];
    $email = $smtpSettings['email'];
    $password = $smtpSettings['password'];
    $password = $helper->decrypt($password, $config->key);
    $host = $smtpSettings['host'];
    $port = $smtpSettings['port'];
    $encryption = $smtpSettings['encryption'];
    $senderName = $smtpSettings['sender_name'];
}

$smtpSettingsID = $smtpSettingsID ?? 0;
$email = $email ?? '';
$password = $password ?? '';
$host = $host ?? '';
$port = $port ?? '';
$encryption = $encryption ?? '';
$senderName = $senderName ?? '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>SMTP Ayarları Ekle Pozitif Eticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once(ROOT."/_y/s/b/header.php");?>
<div id="base">
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">SMTP Ayarları</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <form name="smtpSettingsForm"  id="smtpSettingsForm" class="form form-validation form-validate" role="form" method="post">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <select id="languageID" name="languageID" class="form-control">
                                                            <?php
                                                            foreach($languages as $lang){
                                                                $selected = $lang['languageID'] == $languageID ? 'selected' : '';
                                                                echo '<option value="'.$lang['languageID'].'" '.$selected.'>'.$lang['languageName'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                name="senderName"
                                                                id="senderName"
                                                                value="<?=$senderName?>"
                                                                placeholder="Gönderici Adı" >
                                                        <label for="email">Gönderici Adı</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                name="host"
                                                                id="host"
                                                                value="<?=$host?>"
                                                                placeholder="SMTP Host" >
                                                        <label for="host">SMTP Host</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            name="email"
                                                            id="email"
                                                            value="<?=$email?>"
                                                            placeholder="E-posta adresi" >
                                                        <label for="email">E-posta Adresi</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                                type="password"
                                                                class="form-control"
                                                                name="password"
                                                                id="password"
                                                                value="<?=$password?>"
                                                                placeholder="Şifre" >
                                                        <label for="password">Şifre</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            name="port"
                                                            id="port"
                                                            value="<?=$port?>"
                                                            placeholder="SMTP Port" >
                                                        <label for="port">SMTP Port</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            name="encryption"
                                                            id="encryption"
                                                            value="<?=$encryption?>"
                                                            placeholder="Şifreleme Türü" >
                                                        <label for="encryption">Şifreleme Türü</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-actionbar">
                                    <div class="card-actionbar-row">
                                        <button id="saveButton" type="submit" class="btn btn-primary btn-default">Kaydet</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>

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

<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<script>
    $("#addSMTPSettingsphp").addClass("active");

    // languageID değiştiğinde
    $("#languageID").change(function(){
        var languageID = $(this).val();
        window.location.href = "/_y/s/s/genelayarlar/AddSMTPSettings.php?languageID=" + languageID;
    });

    // Form submit
    $("#smtpSettingsForm").submit(function(e){
        e.preventDefault();
        var email = $("#email").val();
        var password = $("#password").val();
        var host = $("#host").val();
        var port = $("#port").val();
        var senderName = $("#senderName").val();
        var languageID = $("#languageID").val();
        var encryption = $("#encryption").val();
        if(senderName === ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").html("Gönderici Adı Girin");
            $("#alertModal").modal("show");
            return false;
        }
        if(email === ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").html("E-posta Adresi Girin");
            $("#alertModal").modal("show");
            return false;
        }
        if(password === ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").html("Şifre Girin");
            $("#alertModal").modal("show");
            return false;
        }
        if(host === ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").html("SMTP Host Girin");
            $("#alertModal").modal("show");
            return false;
        }
        if(port === ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").html("SMTP Port Girin");
            $("#alertModal").modal("show");
            return false;
        }
        if(encryption === ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").html("Şifreleme Türü Girin");
            $("#alertModal").modal("show");
            return false;
        }
        $.ajax({
            url: "/App/Controller/Admin/AdminSMTPSettingsController.php",
            type: "POST",
            data: {
                action: "saveSMTPSettings",
                id: <?=$smtpSettingsID?>,
                email: email,
                password: password,
                host: host,
                port: port,
                senderName: senderName,
                languageID: languageID,
                encryption: encryption
            },
            success: function(response){
                console.log(response);
                var data = JSON.parse(response);
                if(data.status === "success"){
                    $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                    $("#alertMessage").html("Kaydedildi");
                    $("#alertModal").modal("show");

                    setTimeout(function(){
                        $("#alertModal").modal("hide");
                    }, 1000);
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").html(data.message);
                    $("#alertModal").modal("show");
                }
            }
        });
    });
</script>
<!-- END JAVASCRIPT -->
</body>
</html>