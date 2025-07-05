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

$languageCode = $adminLanguage->getLanguageCode($languageID);

$providerID = $_GET["providerID"] ?? 0;
$providerID = intval($providerID);

include_once MODEL."/Admin/AdminPaymentGateway.php";
$adminPaymentGateway = new AdminPaymentGateway($db);
$providers = $adminPaymentGateway->getProviders($languageCode);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Ödeme Aracısı Liste Pozitif Eticaret</title>
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
                    <li class="active">Ödeme Aracısı Liste</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body ">
                                <div class="form-group">
                                    <!-- dil listesi gelecek -->
                                    <select name="providerLanguageCode" id="providerLanguageCode" class="form-control">
                                        <?php foreach($languages as $language){
                                            $selected = "";
                                            if($language['languageCode'] == $languageCode) {
                                                $selected = "selected";
                                            }
                                            ?>
                                            <option value="<?php echo $language['languageID']; ?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <p class="help-block">ÖDEME ARACISI İÇİN DİL SEÇİN!</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body ">
                                <table class="table no-margin">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Banka Adı</th>
                                        <th>Durumu</th>
                                        <th>İşlem</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($providers as $provider) {
                                        ?>
                                        <tr>
                                            <td><?php echo $provider["id"]; ?></td>
                                            <td><?php echo $provider["name"]; ?></td>
                                            <td><?php echo $provider["status"] == 1 ? "Aktif" : "Pasif"; ?></td>
                                            <td>
                                                <a href="/_y/s/s/odemeyontemi/AddPaymentGateway.php?providerID=<?php echo $provider["id"]; ?>" class="btn btn-xs btn-primary">Düzenle</a>
                                                <button class="btn btn-xs btn-danger deleteProviderButton" data-id="<?=$provider["id"];?>">Sil</button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- BEGIN MENUBAR-->
    <?php require_once(ROOT."/_y/s/b/menu.php");?>
    <!-- END MENUBAR -->

    <div class="modal fade" id="deleteProviderConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteProviderConfirmModalLabel" aria-hidden="true">
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
                    <p id="alertMessage">Ödeme Aracısını silmek istediğinize emin misiniz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-danger" id="deleteProviderConfirmButton">Sil</button>
                </div>
            </div>
        </div>
    </div>

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
<!-- END BASE -->

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
    $("#paymentGatewayListphp").addClass("active");
    
    $(document).ready(function()
    {
        $(document).on("click", ".deleteProviderButton", function()
        {
            var providerID = $(this).data("id");
            $("#deleteProviderConfirmModal").modal("show");
            $("#deleteProviderConfirmButton").click(function()
            {
                $.ajax({
                    url: "/App/Controller/Admin/AdminPaymentGatewayController.php",
                    type: "POST",
                    data: {
                        providerID: providerID,action: "deleteProvider"
                    },
                    success: function(response)
                    {
                        var data = JSON.parse(response);
                        if (data.status == "success")
                        {
                            window.location.reload();
                        }
                        else
                        {
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").text(data.message);
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });
        });

        $("#providerLanguageCode").change(function()
        {
            window.location.href = "/_y/s/s/odemeyontemi/PaymentGatewayList.php?languageID=" + $(this).val();
        });
        
    });
    
</script>
</body>
</html>
