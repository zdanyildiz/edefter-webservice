<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php"); ?>
<?php
//düzenle
$sayfabaslik = "Sistem Logları";
$formbaslik = "Sistem Logları";

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title><?= $formbaslik ?></title>
    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet"
          href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/wizard/wizard.css?1425466601"/>
    <!-- END STYLESHEETS -->


    <link type="text/css" rel="stylesheet" href="/_y/assets/js/libs/DataTables/jquery.dataTables.min.css"/>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">

<!-- BEGIN HEADER-->
<?php require_once($anadizin . "/_y/s/b/header.php"); ?>
<!-- END HEADER-->

<!-- BEGIN BASE-->
<div id="base">

    <!-- BEGIN CONTENT-->
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="btn ink-reaction btn-raised btn-primary disabled">Ayarlar</li>
                    <li class="btn ink-reaction btn-raised btn-primary disabled">Sistem Logları</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <!-- BEGIN ADD CONTACTS FORM -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-head style-primary">
                                <header><?= $formbaslik ?></header>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 small-padding ">
                                    <div class="col-md-4 col-12">Log tipi seçmelisiniz.</div>
                                    <div class="form-group col-md-8 col-12">
                                        <div class="form-group  radio-inline">
                                            <label class="text-bold" for="logTipiSiteId">Site Logları</label>
                                            <input id="logTipiSiteId" name="logTipi" type="radio" class="form-control"
                                                   checked value="site">
                                        </div>
                                        <div class="form-group radio-inline">
                                            <label class="text-bold" for="logTipiPanelId">Panel Logları</label>
                                            <input id="logTipiPanelId" name="logTipi" type="radio" class="form-control"
                                                   value="panel">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 form-group ">
                                    <div class="col-md-4 col-12">Hangi tarihler arasındaki logları görüntülemek
                                        istiyorsunuz?
                                    </div>
                                    <div class="col-md-8 col-12">
                                        <div class="col-md-12 small-padding">
                                            <div class="btn btn-info" id="bugun">Bugün</div>
                                            <div class="btn btn-info" id="dun">Dün</div>
                                            <div class="btn btn-info" id="sonUcGun">Son 3 Gün</div>
                                            <div class="btn btn-info" id="buHafta">Son 1 Hafta</div>
                                            <div class="btn btn-info" id="buAy">Son 30 Gün</div>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="date" name="baslangicTarihi" id="baslangicTarihiId"/> -
                                            <input type="date" name="bitisTarihi" id="bitisTarihiId"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 form-group ">
                                    <div class="col-md-4 col-12"></div>
                                    <div class="col-md-8 col-12">
                                        <div id="loglariGetir" class="btn btn-success">
                                            <i class="fa fa-filter"></i>
                                            Filtrele
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--end .card -->
                    </div><!--end .col -->
                    <!-- END ADD CONTACTS FORM -->
                </div><!--end .row -->
                <!-- BEGIN VALIDATION FORM WIZARD -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body ">
                                <table id="sistemLoglariDataTable" class="col-md-12 table no-margin table-hover">
                                    <thead>
                                    <tr role="row">
                                        <th width="90px">Tarih</th>
                                        <th>Benzersiz ID</th>
                                        <th>IP/Kullanıcı Adı</th>
                                        <th>Sayfa/Sorgu</th>
                                        <th>Sorgu/Durum</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once($anadizin . "/_y/s/b/menu.php"); ?>

</div>

<iframe name="_islem" id="_islem" class="hidden"></iframe>
<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
<script src="/_y/assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="/_y/assets/js/libs/moment/moment.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="/_y/assets/js/libs/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="/_y/assets/js/libs/bootstrap-rating/bootstrap-rating-input.min.js"></script>
<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

<script src="/_y/assets/js/libs/toastr/toastr.js"></script>
<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>
<script src="/_y/assets/js/core/demo/Demo.js"></script>
<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>

<!-- END JAVASCRIPT -->
<script src="/_y/assets/js/libs/wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="/_y/assets/js/core/demo/DemoFormWizard.js"></script>


<script src="/_y/assets/js/libs/DataTables/jquery.dataTables.min.js"></script>
<style>
    #sistemLoglariDataTable>tbody>tr>td:first-child{width:100px;border-right:solid 1px #ddd}
    #sistemLoglariDataTable>tbody>tr>td:nth-child(2){width:244px}
    #sistemLoglariDataTable>tbody>tr>td:nth-child(3){width:128px !important;}
    #sistemLoglariDataTable>tbody>tr>td:nth-child(4){width:128px !important;}
    #sistemLoglariDataTable>tbody>tr>td:nth-child(5){width:717px !important;}
</style>

<script>

    var baslangicTarihiId = "#baslangicTarihiId";
    var bitisTarihiId = "#bitisTarihiId";

    $(document).ready(function () {
        $(document).on("click", "#loglariGetir", function () {

            var logTipiValue = $('input[name=logTipi]:checked').val();
            var baslangicTarihi = $(baslangicTarihiId);
            var bitisTarihi = $(bitisTarihiId);
            //var bitisTarihi = $(bitisTarihiId);
            var baslangicTarihiLong = Date.parse(baslangicTarihi.val()  +" GMT");
            var bitisTarihiLong = Date.parse(bitisTarihi.val()  +" GMT");

            if (!baslangicTarihiLong || baslangicTarihiLong == 0) {
                alert("Başlangıç tarihi seçiniz");
            } else if (!bitisTarihiLong || bitisTarihiLong == 0) {
                alert("Bitiş tarihi seçiniz");
            } else if (baslangicTarihiLong > bitisTarihiLong) {
                alert("Başlangıç tarihi bitiş tarihinden büyük olamaz.");
            } else if (bitisTarihiLong - baslangicTarihiLong > 24 * 60 * 60 * 1000 * 31) {
                alert("Başlangıç ile bitiş tarihi arası en fazla 31 gün olabilir.");
            } else {
                //herşey düzgün ise logları getirecez.
                console.log("logTipi=" + logTipiValue + "&baslangicTarihi=" + baslangicTarihi.val() + "&bitisTarihi=" + bitisTarihi.val())
                $.ajax({
                    data: "logTipi=" + logTipiValue + "&baslangicTarihi=" + baslangicTarihiLong + "&bitisTarihi=" + bitisTarihiLong,
                    method: "POST",
                    url: "sistemLoglariController.php",
                })
                    .done(function (xhr)
                    {
                        //console.log(xhr);
                        //xhr = xhr.replace(/(\r\n|\n|\r)/gm, "");
                        //alert(xhr);
                        $('#sistemLoglariDataTable').DataTable({
                            destroy: true,
                            data: JSON.parse(xhr)
                        });
                    });
            }
        });

        $(document).on("click", "#bugun", function () {
            var baslangicTarihi = $(baslangicTarihiId);
            var bitisTarihi = $(bitisTarihiId);
            baslangicTarihi.val(new Date().toISOString().substr(0, 10))
            bitisTarihi.val(new Date().toISOString().substr(0, 10))
        });
        $(document).on("click", "#dun", function () {
            var baslangicTarihi = $(baslangicTarihiId);
            var bitisTarihi = $(bitisTarihiId);
            baslangicTarihi.val(new Date(new Date().getTime() - (24 * 60 * 60 * 1000)).toISOString().substr(0, 10))
            bitisTarihi.val(new Date(new Date().getTime() - (24 * 60 * 60 * 1000)).toISOString().substr(0, 10))
        });
        $(document).on("click", "#sonUcGun", function () {
            var baslangicTarihi = $(baslangicTarihiId);
            var bitisTarihi = $(bitisTarihiId);
            baslangicTarihi.val(new Date(new Date().getTime() - (3 * 24 * 60 * 60 * 1000)).toISOString().substr(0, 10))
            bitisTarihi.val(new Date().toISOString().substr(0, 10))
        });
        $(document).on("click", "#buHafta", function () {
            var baslangicTarihi = $(baslangicTarihiId);
            var bitisTarihi = $(bitisTarihiId);
            baslangicTarihi.val(new Date(new Date().getTime() - (7 * 24 * 60 * 60 * 1000)).toISOString().substr(0, 10))
            bitisTarihi.val(new Date().toISOString().substr(0, 10))
        });
        $(document).on("click", "#buAy", function () {
            var baslangicTarihi = $(baslangicTarihiId);
            var bitisTarihi = $(bitisTarihiId);
            baslangicTarihi.val(new Date(new Date().getTime() - (30 * 24 * 60 * 60 * 1000)).toISOString().substr(0, 10))
            bitisTarihi.val(new Date().toISOString().substr(0, 10))
        });
        $("#bugun").trigger("click");

    });
</script>
</body>
</html>
