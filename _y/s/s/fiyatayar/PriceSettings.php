<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL."/Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);
$languages = $adminLanguage->getLanguages();

include_once MODEL . 'Admin/AdminCurrency.php';
$adminCurrency = new AdminCurrency($db);
$currencies = $adminCurrency->getCurrencies();

$buttonName = "Ekle";

include_once MODEL."/Admin/AdminPriceSettings.php";
$adminPriceSettings = new AdminPriceSettings($db, $languageID);
$priceSettings = $adminPriceSettings->getPriceSettings();
if($priceSettings['status'] == "success"){
    $priceSettings = $priceSettings['data'];

    $showPriceStatus = $priceSettings['showPriceStatus'];
    $showPriceToDealer = $priceSettings['showPriceToDealer'];
    $showOldPrice = $priceSettings['showOldPrice'];
    $creditCardStatus = $priceSettings['creditCardStatus'];
    $cashOnDeliveryStatus = $priceSettings['cashOnDeliveryStatus'];
    $bankTransferStatus = $priceSettings['bankTransferStatus'];
    $currencyID = $priceSettings['currencyID'];
    $installmentStatus = $priceSettings['installmentStatus'];
    $taxRate = $priceSettings['taxRate'];
    $singlePaymentDiscountRate = $priceSettings['singlePaymentDiscountRate'];
    $bankTransferDiscountRate = $priceSettings['bankTransferDiscountRate'];
    $buttonName = "Güncelle";
}

$showPriceStatus = $showPriceStatus ?? 1;
$showPriceToDealer = $showPriceToDealer ?? 0;
$showOldPrice = $showOldPrice ?? 0;
$creditCardStatus = $creditCardStatus ?? 1;
$cashOnDeliveryStatus = $cashOnDeliveryStatus ?? 0;
$bankTransferStatus = $bankTransferStatus ?? 1;
$currencyID = $currencyID ?? 1;
$installmentStatus = $installmentStatus ?? 1;
$taxRate = $taxRate ?? 0.20;
$singlePaymentDiscountRate = $singlePaymentDiscountRate ?? 0;
$bankTransferDiscountRate = $bankTransferDiscountRate ?? 0;


?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Fiyat Ayarları Pozitif Eticaret</title>

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
                            <li class="active">Fiyat Ayarları</li>
                        </ol>
					</div>
					
					<div class="section-body contain-lg">
						<div class="row">
                            <div class="col-lg-3 col-md-4">
                                <!-- ayarlarla ilgili açıklama yapalım -->
                                <div class="card">
                                    <div class="card-body">
                                        <p>Fiyat Ayarları her dil için <strong>ayRıdır</strong>. Türkçe için TL para birimi ile ürünlerinizi gösterirken İngilizce için dolar ya da euro seçebilirsiniz</p>
                                        <p></p>
                                        <p>Ürünlerde fiyat göster seçeneği ile ürünlerinizde fiyat gösterip göstermeme seçeneğini belirleyebilirsiniz</p>
                                        <p></p>
                                        <p>Ürün fiyatlarını sadece bayilere gösterebilirsiniz</p>
                                        <p></p>
                                        <p>Üstü çizili fiyatlar göstererek indirim vurgusu yapabilirsiniz</p>
                                        <p></p>
                                        <p>Hangi türlerde ödeme alacaksınız? Ödeme kanallarınızı belirlerin</p>
                                        <p></p>
                                        <p>Sık kullandığınız KDV oranını, satışlardaki maksimum taksit sayısını, kredi kartı ile ödemelerde tek çekim indirim oranını, havale ödemelerinde uygulayacağınız indirim oranlarını buradan belirleyebilirsiniz.</p>
                                    </div>
                                </div>
                            </div>
							<div class="col-lg-offset-1 col-md-8">
									
                                <form id="priceSttingsForm" class="form form-validation form-validate" role="form" method="post" novalidate="novalidate">
                                    <div class="card">
                                        <div class="card-head">
                                            <header>Fiyat Ayarları</header>
                                        </div>
                                        <div class="row margin-bottom-xxl"></div>
                                        <div class="col-md-1 text-default-bright">-</div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <select id="languageID" name="languageID" class="form-control">
                                                <?php
                                                    foreach($languages as $language){
                                                        $selected = "";
                                                        if($languageID == $language["languageID"]){
                                                            $selected = "selected";
                                                        }
                                                        echo "<option value='".$language["languageID"]."' $selected>".$language["languageName"]."</option>";
                                                    }
                                                ?>
                                                </select>
                                                <label for="languageID">Ayarların uygulanacağı dili seçin</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-default-bright">-</div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select id="currencyID" name="currencyID" class="form-control">
                                                    <?php
                                                    foreach($currencies as $currency){
                                                        $selected = "";
                                                        if($currencyID == $currency["currencyID"]){
                                                            $selected = "selected";
                                                        }
                                                        echo "<option value='".$currency["currencyID"]."' $selected>".$currency["currencyName"]."</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label for="currencyID">Genel Para Birimi Seçin</label>
                                            </div>
                                        </div>
                                        <div class="row"></div>
                                        <div class="card-body">

                                            <div class="col-md-6">

                                                    <label class="checkbox checkbox-styled">
                                                        <input
                                                            type="checkbox"
                                                            name="showPriceStatus"
                                                            value="1"
                                                            <?php if($showPriceStatus == 1) echo "checked"; ?>>
                                                        <span>Ürünlerde Fiyat Göster</span>
                                                    </label>

                                            </div>
                                            <div class="col-md-6">

                                                    <label class="checkbox checkbox-styled">
                                                        <input
                                                            type="checkbox"
                                                            name="showPriceToDealer"
                                                            value="1"
                                                            <?php if($showPriceToDealer ==1 ) echo "checked"; ?>>
                                                        <span>Fiyatları sadece bayiler görsün</span>
                                                    </label>

                                            </div>
                                            <div class="row margin-bottom-xxl"></div>
                                            <div class="col-md-6">

                                                    <label class="checkbox checkbox-styled">
                                                        <input
                                                            type="checkbox"
                                                            name="showOldPrice"
                                                            value="1"
                                                            <?php if($showOldPrice == 1) echo "checked"; ?>>
                                                        <span>Ürünlerde Üstü çizili Eski Fiyat Göster
                                                            <span style="text-decoration:line-through;float:right;margin-left:15px"> 120 TL</span>
                                                        </span>
                                                    </label>

                                            </div>
                                            <div class="row margin-bottom-xxl"></div>
                                            <div class="col-md-4">

                                                    <label class="checkbox checkbox-styled">
                                                        <input
                                                            type="checkbox"
                                                            name="creditCardStatus"
                                                            value="1"
                                                            <?php if($creditCardStatus == 1) echo "checked"; ?>>
                                                        <span>Kredi Kartı ile Ödeme</span>
                                                    </label>

                                            </div>
                                            <div class="col-md-4">

                                                    <label class="checkbox checkbox-styled">
                                                        <input
                                                            type="checkbox"
                                                            name="cashOnDeliveryStatus"
                                                            value="1"
                                                            <?php if($cashOnDeliveryStatus == 1) echo "checked"; ?>>
                                                        <span>Kapıda Ödeme </span>
                                                    </label>

                                            </div>
                                            <div class="col-md-4">

                                                    <label class="checkbox checkbox-styled">
                                                        <input
                                                            type="checkbox"
                                                            name="bankTransferStatus"
                                                            value="1"
                                                            <?php if($bankTransferStatus == 1) echo "checked"; ?>>
                                                        <span>Havale ile Ödeme</span>
                                                    </label>

                                            </div>
                                            <div class="row margin-bottom-xxl"></div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" name="installmentStatus" id="installmentStatus" class="form-control" value="<?=$installmentStatus?>" data-rule-digits="true" required="" aria-required="true" aria-invalid="false">
                                                    <label>Genel Taksit Sayısı</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" name="taxRate" id="taxRate" class="form-control" value="<?=$taxRate?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                                    <label>Genel KDV Oranı</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" name="singlePaymentDiscountRate" id="singlePaymentDiscountRate" class="form-control" value="<?=$singlePaymentDiscountRate?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                                    <label>KK Tek Çekim İndirim Oranı</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" name="bankTransferDiscountRate" id="bankTransferDiscountRate" class="form-control" value="<?=$bankTransferDiscountRate?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                                    <label>Havale İndirim Oranı (0.01)</label>
                                                </div>
                                            </div>
                                            <div class="row"></div>
                                            <div class="card-actionbar">
                                        <div class="card-actionbar-row">
                                            <button type="submit" class="btn btn-primary btn-default"><?=$buttonName?></button>
                                        </div>
                                    </div>
                                        </div>
                                    </div>
                                </form>

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

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

		<script>
			$("#priceSettingsphp").addClass("active");

            $("#languageID").on("change",function(){
                $languageID=$(this).val();
                window.location.href="/_y/s/s/fiyatayar/PriceSettings.php?languageID="+$languageID;
            });

            //priceSttingsForm submit dinleyelim
            $("#priceSttingsForm").on("submit",function(e){
                e.preventDefault();
                $("#alertModal .card-head").removeClass("style-succes").addClass("style-danger");

                let installmentStatus = $("#installmentStatus").val();
                if(installmentStatus == "" || isNaN(installmentStatus) || installmentStatus < 0 || installmentStatus > 12){
                    $("#alertMessage").html("Genel Taksit Sayısı Boş Olamaz, Sayı Olmalı, 0 ya da 1 değeri taksit yok demek, en fazla 12 olabilir");
                    $("#alertModal").modal("show");
                    return;
                }

                let taxRate = $("#taxRate").val();
                if(taxRate == "" || isNaN(taxRate) || taxRate < 0 || taxRate > 0.99){
                    $("#alertMessage").html("KDV Oranı Boş Olamaz, 0 ile 0.99 arasında olmalı, yalnızca nokta kullanılabilir");
                    $("#alertModal").modal("show");
                    return;
                }

                let singlePaymentDiscountRate = $("#singlePaymentDiscountRate").val();
                if(singlePaymentDiscountRate == "" || isNaN(singlePaymentDiscountRate) || singlePaymentDiscountRate < 0 || singlePaymentDiscountRate > 0.99){
                    $("#alertMessage").html("Tek Çekim İndirim Oranı Boş Olamaz, 0 ile 0.99 arasında olmalı, yalnızca nokta kullanılabilir");
                    $("#alertModal").modal("show");
                    return;
                }

                let bankTransferDiscountRate = $("#bankTransferDiscountRate").val();
                if(bankTransferDiscountRate == "" || isNaN(bankTransferDiscountRate) || bankTransferDiscountRate < 0 || bankTransferDiscountRate > 0.99){
                    $("#alertMessage").html("Havale İndirim Oranı Boş Olamaz, 0 ile 0.99 arasında olmalı, yalnızca nokta kullanılabilir");
                    $("#alertModal").modal("show");
                    return;
                }

                $form = $(this);
                $formData = $form.serialize();

                let action = "addPriceSettings";

                $formData += "&action="+action;

                $.ajax({
                    url:"/App/Controller/Admin/AdminPriceSettingsController.php",
                    type:"POST",
                    data:$formData,
                    success:function(response){
                        console.log(response);

                        response = JSON.parse(response);
                        if(response.status == "error"){

                            $("#alertModal .card-head").removeClass("style-succes").addClass("style-danger");
                            $("#alertMessage").html(response.message);
                            $("#alertModal").modal("show");
                        }
                        else{

                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").html(response.message);
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });
		</script>
	</body>
</html>