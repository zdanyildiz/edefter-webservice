<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */
$submitButtonName = "Ekle";
$groupID = $_GET["groupID"] ?? 0;
if($groupID == ''){
    $groupID = 0;
}

if($groupID > 0) {
    include_once MODEL . "/Admin/AdminProductGroup.php";
    $adminProductGroup = new AdminProductGroup($db);

    $productGroupResult = $adminProductGroup->getProductGroup($groupID);
    $productGroup = $productGroupResult['data'];

    $productGroupID = $productGroup["productGroupID"];
    $productGroupName = $productGroup["productGroupName"];
    $productGroupDescription = $productGroup["productGroupDescription"];
    $productGroupTaxRate = $productGroup["productTaxRate"];
    $productGroupDiscountRate = $productGroup["productDiscountRate"];
    $productGroupProductDescription = $productGroup["productGroupProductDescription"];
    $productGroupProductShortDesc = $productGroup["productShortDesc"];
    $productGroupProductCargoTime = $productGroup["productGroupDeliveryTime"];
    $submitButtonName = "Güncelle";
}

$productGroupID = $productGroupID ?? 0;
$productGroupName = $productGroupName ?? "";
$productGroupDescription = $productGroupDescription ?? "";
$productGroupTaxRate = $productGroupTaxRate ?? "";
$productGroupDiscountRate = $productGroupDiscountRate ?? "";
$productGroupProductDescription = $productGroupProductDescription ?? "";
$productGroupProductShortDesc = $productGroupProductShortDesc ?? "";
$productGroupProductCargoTime = $productGroupProductCargoTime ?? "";
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Ürün Grupları Pozitif E-Ticaret</title>

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
                            <li class="active">Ürün Grubu Ekle / Güncelle</li>
                        </ol>
                    </div>
					<div class="section-body contain-lg">
						<div class="row">
                            <div class="col-lg-3 col-md-4">
                                <article class="margin-bottom-xxl">
                                    <ul>
                                        Grupta tanımlayıp kaydettiğiniz tüm özellikler, ürün ekleme aşamasında grubu seçerseniz otomatik olarak gelir.
                                    </ul>
                                </article>
                            </div>
                            <div class="col-lg-offset-1 col-md-8">
								<div class="card">
									<form name="productGroupForm" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="productGroupID" value="<?=$productGroupID?>">
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="productGroupName"
																id="productGroupName"
																value="<?=$productGroupName?>"
																placeholder="Örn: %8 KDV'li ürünler" required aria-required="true" >
																<label for="urungrupad">Grup Adını Yazın</label>
															</div>
														</div>
													</div>

													<div class="row">		
														<div class="col-sm-6">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="productGroupTaxRate"
																id="productGroupTaxRate"
																value="<?=$productGroupTaxRate?>"
																placeholder="0.20"
																data-rule-number="true" 
																required="" 
																aria-required="true" 
																aria-invalid="false" >
																<label for="productGroupTaxRate">KDV Girin: %8 için 0.08 (sadece nokta ve rakam)</label>
															</div>
														</div>

														<div class="col-sm-6">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="productGroupDiscountRate"
																id="productGroupDiscountRate"
																value="<?=$productGroupDiscountRate?>"
																placeholder="0.10"
																data-rule-number="true" 
																required="" 
																aria-required="true" 
																aria-invalid="false" >
																<label for="productGroupDiscountRate">İndirim Oranı: %10 için 0.10 (sadece nokta ve rakam)</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-12">
															<div class="form-group">
																<textarea 
																	name="productGroupProductDescription"
																	id="productGroupProductDescription"
																	class="form-control" 
																	rows="1"
																	placeholder
																	style="
																		background-color:#efefef; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=ltrim($productGroupProductDescription)?></textarea>
																<label for="productGroupProductDescription">Ürün alt başlık "Yerli Üretim..."</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-12">
															<div class="form-group">
																<textarea 
																	name="productGroupProductShortDesc"
																	id="productGroupProductShortDesc"
																	class="form-control" 
																	rows="1"
																	placeholder
																	style="
																		background-color:#efefef; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=ltrim($productGroupProductShortDesc)?></textarea>
																<label for="productGroupProductShortDesc">Ürün Kısa Açıklama "Saat 4'de kadar aynı gün gönderim"</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-6">
															<div class="form-group">
																<input 
																	type="text" 
																	name="productGroupProductCargoTime"
																	id="productGroupProductCargoTime"
																	class="form-control" 
																	placeholder="3" 
																	data-rule-number="true" 
																	required="" 
																	aria-required="true" 
																	aria-invalid="false"
																	value="<?=$productGroupProductCargoTime?>">
																<label for="productGroupProductCargoTime">Kargo Süresi (Gün)</label>
															</div>
														</div>
													</div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
																<textarea
                                                                        name="productGroupDescription"
                                                                        id="productGroupDescription"
                                                                        class="form-control"
                                                                        rows="4"
                                                                        placeholder="Bu alan grup hakkında kısa bilgi vermeniz için kullanılır. Boş bırakabilirsiniz."
                                                                        style="
																		background-color:#efefef;
																		width:96%;
																		padding: 10px 1% 10px 1%;
																		margin:10px 0 0 0;
																		border:solid 1px #eee"
                                                                ><?=ltrim($productGroupDescription)?></textarea>
                                                                <label for="productGroupDescription">Grup Açıklama</label>
                                                            </div>
                                                        </div>
                                                    </div>
												</div>
											</div>
										</div>
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<button type="submit" class="btn btn-primary btn-default"><?=$submitButtonName?></button>
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

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
		
		<script>
			$("#addProductGroupphp").addClass("active");

            $(document).on("submit", "form[name='productGroupForm']", function(e) {
                e.preventDefault();

                $('#alertModal .card-head').removeClass('style-success').removeClass('style-danger').addClass('style-danger');
                //başlık boş olamaz
                if($("#productGroupName").val() == "") {
                    $("#alertMessage").text("Grup adı boş olamaz.");
                    $("#alertModal").modal("show");
                    return;
                }
                //vergi oranı ve indirim boş olamaz. 0 ya da 0.99 olabilir.
                if($("#productGroupTaxRate").val() == "" || $("#productGroupDiscountRate").val() == "") {
                    $("#alertMessage").html("Vergi oranı ve indirim oranı boş olamaz. <br> 0 ya da 0.99 olabilir.");
                    $("#alertModal").modal("show");
                    return;
                }

                //sayı kontrolü yapaılım
                if(isNaN($("#productGroupTaxRate").val()) || isNaN($("#productGroupDiscountRate").val())) {
                    $("#alertMessage").html("Vergi oranı ve indirim oranı sayı olmalıdır.");
                    $("#alertModal").modal("show");
                    return;
                }

                //0dan küçük ve 0.99'dan bütyük olamaz
                if($("#productGroupTaxRate").val() < 0 || $("#productGroupTaxRate").val() > 0.99) {
                    $("#alertMessage").html("Vergi oranı 0 ile 0.99 arasında olmalıdır.");
                    $("#alertModal").modal("show");
                    return;
                }

                if($("#productGroupDiscountRate").val() < 0 || $("#productGroupDiscountRate").val() > 0.99) {
                    $("#alertMessage").html("İndirim oranı 0 ile 0.99 arasında olmalıdır.");
                    $("#alertModal").modal("show");
                    return;
                }

                //kargo süresi boş olamaz
                if($("#productGroupProductCargoTime").val() == "") {
                    $("#alertMessage").text("Kargo süresi boş olamaz.");
                    $("#alertModal").modal("show");
                    return;
                }

                //sayı kontrolü yapalım
                if(isNaN($("#productGroupProductCargoTime").val())) {
                    $("#alertMessage").text("Kargo süresi sayı olmalıdır.");
                    $("#alertModal").modal("show");
                    return;
                }


                var form = $(this);
                var formData = form.serialize();
                var action = "addProductGroup";
                var productGroupID = $("input[name='productGroupID']").val();
                if(productGroupID > 0) {
                    action = "updateProductGroup";
                }
                formData += "&action=" + action;
                $.ajax({
                    type: "POST",
                    url: "/App/Controller/Admin/AdminProductGroupController.php",
                    data: formData,
                    success: function(data) {
                        console.log(data);
                        var response = JSON.parse(data);
                        var message = response.message;
                        if(response.status == "success") {

                            productGroupID = response.productGroupID;
                            $("input[name='productGroupID']").val(productGroupID);
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").text(message);
                            $("#alertModal").modal("show");

                            //1.5 saniye sonra yönlendirme yapalım
                            setTimeout(function() {
                                window.location.href = "/_y/s/s/gruplar/productGroupList.php";
                            }, 1500);

                        }
                        else {
                            $("#alertMessage").text(message);
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });
			
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>