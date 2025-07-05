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
        $campaignType = $campaign['campaignType'];

        if($campaignType == "miktar_indirim"){
            $quantityDiscounts = $campaignModel->getQuantityDiscount($campaignID);
        }

        $buttonName = "Kampanya Güncelle";
    }
}
$campaignName = $campaignName ?? "";
$campaignDescription = $campaignDescription ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kampanya Miktar İndirim Düzenle - Eticaret Yönetim Paneli</title>
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
                    <li class="active">Kampanya Ekle</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <form class="form" method="post" id="addQuantityDiscountForm">
                    <input type="hidden" name="campaignID" id="campaignID" value="<?=$campaignID?>">
                    <div class="card">
                        <div class="card-head style-primary">
                            <header>Yeni Kampanya Ekle</header>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <h4><?=$campaignName?></h4>
                            </div>
                            <div class="form-group">
                                <div class="alert alert-info"><?=$campaignDescription?></div>
                            </div>
                            <div id="quantityDiscountContainer">
                            <?php
                            if(count($quantityDiscounts)>0):
                                $i=0;
                            foreach ($quantityDiscounts as $quantityDiscount){
                                $i++;
                            ?>
                            <div class="row" id="quantity-discount-<?=$i?>">
                                <div class="col-md-3">
                                    Kaç Adet ve Üzeri<br>Yalnızca rakam giriniz.
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="quantity<?=$i?>" class="control-label">Miktar Sınır</label>
                                        <input type="text" class="form-control" id="quantity<?=$i?>" name="quantity[]" value="<?=$quantityDiscount["quantityLimit"]?>" required>
                                    </div>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-3">
                                    % Kaç İndirim<br>Yalnızca rakam ve nokta giriniz.
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="discount<?=$i?>" class="control-label">İndirim Oranı</label>
                                        <input type="text" class="form-control" id="discount<?=$i?>" name="discount" value="<?=$quantityDiscount["discountRate"]?>" required>
                                    </div>
                                </div>
                            </div>
                            <?php }
                            else:?>
                                <div class="row" id="quantity-discount-1">
                                    <div class="col-md-3">
                                        Kaç Adet ve Üzeri<br>Yalnızca rakam giriniz.
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="quantity1" class="control-label">Miktar Sınır</label>
                                            <input type="text" class="form-control" id="quantity1" name="quantity[]" value="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3">
                                        % Kaç İndirim<br>Yalnızca rakam ve nokta giriniz.
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="discount1" class="control-label">İndirim Oranı</label>
                                            <input type="text" class="form-control" id="discount1" name="discount" value="" required>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-actionbar">
                            <div class="card-actionbar-row">
                                <button type="button" class="btn btn-primary-bright ink-reaction" id="addQuantityDiscount" style="float:left">İndirim Alanı Ekle</button>
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

        $('#addQuantityDiscount').on('click', function() {
            var quantityDiscountCount = $('.row[id^="quantity-discount-"]').length;
            var newQuantityDiscountCount = quantityDiscountCount + 1;
            var newQuantityDiscount = '<div class="row" id="quantity-discount-' + newQuantityDiscountCount + '">' +
                '<div class="col-md-3">Kaç Adet ve Üzeri<br>Yalnızca rakam giriniz.</div>' +
                '<div class="col-md-2">' +
                '<div class="form-group">' +
                '<label for="quantity' + newQuantityDiscountCount + '" class="control-label">Miktar Sınır</label>' +
                '<input type="text" class="form-control" id="quantity' + newQuantityDiscountCount + '" name="quantity[]" value="" required>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-1"></div>' +
                '<div class="col-md-3">% Kaç İndirim<br>Yalnızca rakam ve nokta giriniz.</div>' +
                '<div class="col-md-2">' +
                '<div class="form-group" id="discount' + newQuantityDiscountCount + '">' +
                '<label for="discount' + newQuantityDiscountCount + '" class="control-label">İndirim Oranı</label>' +
                '<input type="text" class="form-control" id="discount' + newQuantityDiscountCount + '" name="discount" value="" required>' +
                '</div>' +
                '</div>' +
                '</div>';
            $('#quantityDiscountContainer').append(newQuantityDiscount);
        });

        $('#addQuantityDiscountForm').on('submit', function(e) {
            e.preventDefault();

            //tüm quantity'leri dizi halinde alalım
            var quantities = [];
            var quantityVal=0;
            $('input[name^="quantity"]').each(function() {
                quantityVal = $(this).val();
                //boş olamaz
                if(quantityVal == ""){
                    $('#alertModal .card-head').removeClass('style-primary').addClass('style-danger');
                    $('#alertMessage').text('Miktar belirtmelisiniz.');
                    $('#alertModal').modal('show');
                    return;
                }
                //yalnızca rakam olabilir
                if(isNaN(quantityVal)){
                    $('#alertModal .card-head').removeClass('style-primary').addClass('style-danger');
                    $('#alertMessage').text('Miktar yalnızca rakam olabilir.');
                    $('#alertModal').modal('show');
                    return;
                }
                quantities.push(quantityVal);
            });

            //tüm discountları dizi halinde alalım
            var discounts = [];
            var discountVal=0;
            $('input[name^="discount"]').each(function() {
                discountVal = $(this).val();
                //boş olamaz
                if(discountVal == ""){
                    $('#alertModal .card-head').removeClass('style-primary').addClass('style-danger');
                    $('#alertMessage').text('İndirim belirtmelisiniz.');
                    $('#alertModal').modal('show');
                    return;
                }
                //yalnızca rakam ve nokta olabilir
                if(isNaN(discountVal)){
                    $('#alertModal .card-head').removeClass('style-primary').addClass('style-danger');
                    $('#alertMessage').text('İndirim yalnızca rakam ve nokta olabilir.');
                    $('#alertModal').modal('show');
                    return;
                }

                discounts.push(discountVal);
            });

            var campaignID = $('input[name="campaignID"]').val();
            //0 olamaz
            if(campaignID == 0){
                $('#alertModal .card-head').removeClass('style-primary').addClass('style-danger');
                $('#alertMessage').text('Kampanya ID belirtmelisiniz.');
                $('#alertModal').modal('show');
                return;
            }

            var formData = $(this).serializeArray();

            // 'quantities' dizisinin her bir elemanını formData'ya ekleyin
            $.each(quantities, function(index, value) {
                formData.push({name: "quantities[]", value: value});
            });

            // 'discounts' dizisinin her bir elemanını formData'ya ekleyin
            $.each(discounts, function(index, value) {
                formData.push({name: "discounts[]", value: value});
            });

            var action = "addQuantityDiscount";

            $.ajax({
                url: '/App/Controller/Admin/AdminCampaignController.php?action=' + action,
                type: 'POST',
                data: formData,
                success: function(response) {
                    //console.log(response);
                    var jsonResponse = JSON.parse(response);
                    if(jsonResponse.status === "success") {
                        $('#alertModal .card-head').removeClass('style-danger').addClass('style-primary');
                        //1.5 saniye sonra sayfayı yönlendir
                        setTimeout(function() {
                            window.location.href = "/_y/s/s/kampanyalar/CampaignList.php";
                        }, 1500);
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