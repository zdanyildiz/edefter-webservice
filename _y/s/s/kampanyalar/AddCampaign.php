<?php
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */

$buttonName = "Kampanya Ekle";

include_once MODEL . 'Admin/AdminCampaignAndPointsManager.php';
$campaignModel = new AdminCampaignAndPointsManager($db);

$campaignID = $_GET['campaignID'] ?? 0;
$campaignID = intval($campaignID);

if($campaignID > 0) {
    $campaign = $campaignModel->getCampaign($campaignID);
    if(!empty($campaign)) {
        /**
         * id as campaignID,
         * ad as campaignName,
         * aciklama as campaignDescription,
         * campaignStartDate as startDate,
         * campaignEndDate as endDate,
         * tur as campaignType,
         * oncelik as priority
         */
        $campaign = $campaign[0];
        $campaignID = $campaign['campaignID'];
        $campaignName = $campaign['campaignName'];
        $campaignDescription = $campaign['campaignDescription'];
        $campaignStartDate = $campaign['campaignStartDate'];
        $campaignEndDate = $campaign['campaignEndDate'];
        $campaignType = $campaign['campaignType'];
        $campaignPriority = $campaign['campaignPriority'];

        $buttonName = "Kampanya Güncelle";
    }
}
$campaignName = $campaignName ?? "";
$campaignDescription = $campaignDescription ?? "";
$campaignStartDate = $campaignStartDate ?? "";
$campaignEndDate = $campaignEndDate ?? "";
$campaignType = $campaignType ?? "";
$campaignPriority = $campaignPriority ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kampanya Ekle - E-ticaret Yönetim Paneli</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />

    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
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
    <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">Kampanya Ekle</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <form class="form" method="post" id="addCampaignForm">
                    <input type="hidden" name="campaignID" value="<?=$campaignID?>">
                    <input type="hidden" name="campaignPriority" value="<?=$campaignPriority?>">
                    <div class="card">
                        <div class="card-head style-primary">
                            <header>Yeni Kampanya Ekle</header>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="campaignName" class="control-label">Kampanya Adı</label>
                                <input type="text" class="form-control" id="campaignName" name="campaignName" value="<?=$campaignName?>" required>
                            </div>
                            <div class="form-group">
                                <label for="campaignDescription" class="control-label">Kampanya Açıklaması</label>
                                <textarea class="form-control" id="campaignDescription" name="campaignDescription" rows="3"><?=$campaignDescription?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="campaignType" class="control-label">Kampanya Türü</label>
                                <select class="form-control" id="campaignType" name="campaignType" required>
                                    <option value="indirim" <?=$campaignType=="indirim" ? "selected" : ""?>>İndirim</option>
                                    <option value="miktar_indirim" <?=$campaignType=="miktar_indirim" ? "selected" : ""?>>Miktar İndirimi</option>
                                    <option value="paket_indirim" <?=$campaignType=="paket_indirim" ? "selected" : ""?>>Paket İndirimi</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="campaignStartDate" class="control-label">Başlangıç Tarihi</label>
                                        <input type="text" class="form-control datepicker" id="campaignStartDate" name="campaignStartDate" value="<?=$campaignStartDate?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="campaignEndDate" class="control-label">Bitiş Tarihi</label>
                                        <input type="text" class="form-control datepicker" id="campaignEndDate" name="campaignEndDate" value="<?=$campaignEndDate?>" required>
                                    </div>
                                </div>
                            </div>
                            <!-- Kampanya türüne göre ek alanlar buraya eklenebilir -->
                        </div>
                        <div class="card-actionbar">
                            <div class="card-actionbar-row">
                                <button type="submit" class="btn btn-primary ink-reaction"><?=$buttonName?></button>
                            </div>
                        </div>
                    </div>
                </form>
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

<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>


<script>
    $("#addCampaignphp").addClass("active");

    $(document).ready(function() {

        $.fn.datepicker.dates['tr'] = {
            days: ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"],
            daysShort: ["Pzr", "Pzt", "Sal", "Çar", "Per", "Cum", "Cmt"],
            daysMin: ["Pz", "Pt", "Sl", "Çr", "Pr", "Cm", "Ct"],
            months: ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],
            monthsShort: ["Ock", "Şbt", "Mar", "Nis", "May", "Haz", "Tem", "Ağu", "Eyl", "Ekm", "Kas", "Arl"],
            today: "Today",
            clear: "Clear",
            format: "yyyy-mm-dd",
            titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
            weekStart: 0
        };

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'tr'
        });

        $('#addCampaignForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var action = "addCampaign";
            var campaignID = $('input[name="campaignID"]').val();
            if(campaignID > 0) {
                action = "updateCampaign";
            }

            $.ajax({
                url: '/App/Controller/Admin/AdminCampaignController.php?action=' + action,
                type: 'POST',
                data: formData,
                success: function(response) {
                    var jsonResponse = JSON.parse(response);
                    if(jsonResponse.status === "success") {
                        $('#alertModal .card-head').removeClass('style-danger').addClass('style-primary');

                        //form submit etkisiz hale getirelim
                        $('#addCampaignForm').off('submit').submit(function(e) {
                            e.preventDefault();
                        });
                        //iki saniye sonra yönlendirelim
                        setTimeout(function() {
                            window.location.href = '/_y/s/s/kampanyalar/CampaignList.php';
                        }, 2500);
                    } else {
                        $('#alertModal .card-head').removeClass('style-primary').addClass('style-danger');
                    }
                    $('#alertMessage').text(jsonResponse.message);
                    $('#alertModal').modal('show');
                },
                error: function() {
                    $('#alertModal .card-head').removeClass('style-primary').addClass('style-danger');
                    $('#alertMessage').text('Bir hata oluştu. Lütfen tekrar deneyin.');
                    $('#alertModal').modal('show');
                }
            });
        });
    });
</script>
</body>
</html>