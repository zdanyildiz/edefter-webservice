<?php
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */

include_once MODEL . 'Admin/AdminCampaignAndPointsManager.php';
$campaignModel = new AdminCampaignAndPointsManager($db);

$campaings = $campaignModel->getCampaigns();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kampanya Liste - E-ticaret Yönetim Paneli</title>
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
    <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">Kampanya Listesi</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="table">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Kampanya Adı</th>
                            <th>Kampanya Açıklaması</th>
                            <th>Kampanya Tipi</th>
                            <th>Kampanya Öncelik</th>
                            <th>Başlangıç Tarihi</th>
                            <th>Bitiş Tarihi</th>
                            <th>İşlemler</th>
                        </thead>
                        <tbody>
                        <?php foreach ($campaings as $campaign) { ?>
                            <tr>
                                <td><?php echo $campaign['campaignName']; ?></td>
                                <td><?php echo substr($campaign['campaignDescription'],0,50); ?></td>
                                <td><?php
                                    if($campaign['campaignType'] != "miktar_indirim"){
                                        echo $campaign['campaignType'];
                                    }
                                    else{
                                        echo '<a href="EditQuantityDiscount.php?campaignID='.$campaign['campaignID'].'" class="btn btn-sm btn-primary-bright">'.$campaign['campaignType'].' düzenle</a>';
                                    }
                                ?></td>
                                <td><?php echo $campaign['campaignPriority']; ?></td>
                                <td><?php echo $campaign['campaignStartDate']; ?></td>
                                <td><?php echo $campaign['campaignEndDate']; ?></td>
                                <td>
                                    <a href="/_y/s/s/kampanyalar/AddCampaign.php?campaignID=<?php echo $campaign['campaignID']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                                    <a href="#" data-id="<?php echo $campaign['campaignID']; ?>" class="btn btn-sm btn-danger">Sil</a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
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
    $("#campaignListphp").addClass("active");

    $(document).ready(function() {


    });
</script>
</body>
</html>