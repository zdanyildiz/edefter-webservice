<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */
$buttonName = "Ekle";

include_once MODEL . 'Admin/AdminCurrency.php';
$adminCurrency = new AdminCurrency($db);

$currencyID = $_GET["currencyID"] ?? 0;
$currencyID = intval($currencyID);

if($currencyID>0){
    $currency = $adminCurrency->getCurrency($currencyID);
    if(!empty($currency)){
        $currencyName = $currency["currencyName"];
        $currencySymbol = $currency["currencySymbol"];
        $currencyCode = $currency["currencyCode"];
        $buttonName = "Düzenle";
    }
}

$currencyName = $currencyName ?? "";
$currencySymbol = $currencySymbol ?? "";
$currencyCode = $currencyCode ?? "";

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Para Birimi Ekle Düzenle Pozitif Eticaret</title>

        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="your,keywords">
        <meta name="description" content="Short explanation about this website">
        

        
        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
              type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

        <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

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
                            <li class="active">Para Birimi Ekle / Düzenle</li>
                        </ol>
                    </div>

					<div class="section-body contain-lg">
						<div class="row">
							
							<div class="col-md-12">
								<div class="card">

									<form name="addCurrencyForm" id="addCurrencyForm" class="form form-validation form-validate" role="form" method="post" novalidate="novalidate">
										<input type="hidden" name="currencyID"  id="currencyID" value="<?=$currencyID?>">
										
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="currencyName"
																id="currencyName"
																value="<?=$currencyName?>"
																placeholder="Para Birimi Adını Yazın" required aria-required="true" >
																<label for="parabirimad">Para Birimi Adını Yazın</label>
															</div>
														</div>
													</div>
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="currencySymbol"
																id="currencySymbol"
																value="<?=$currencySymbol?>"
																placeholder="Para Birim Simge" required aria-required="true" >
																<label for="parabirisimge">Para Birim Simge ($,€)</label>
															</div>
														</div>
													</div>
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="currencyCode"
																id="currencyCode"
																value="<?=$currencyCode?>"
																placeholder="Para Birim Kodu" required aria-required="true" >
																<label for="parabirimkod">Para Birim Kodu (TRY,USD)</label>
															</div>
														</div>
													</div>
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
			$("#addCurrencyphp").addClass("active");

            $(document).on("submit", "#addCurrencyForm", function (e) {
                e.preventDefault();
                //para birimi adı boş olamaz
                if ($("#currencyName").val() == "") {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text("Para Birimi adı alanı boş olamaz.");
                    $("#alertModal").modal("show");
                    return;
                }
                //para birimi simge boş olamaz
                if ($("#currencySymbol").val() == "") {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text("Para Birimi simgesi alanı boş olamaz.");
                    $("#alertModal").modal("show");
                    return;
                }
                //para birimi kodu boş olamaz
                if ($("#currencyCode").val() == "") {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text("Para Birimi kodu alanı boş olamaz.");
                    $("#alertModal").modal("show");
                    return;
                }

                let currencyID = $("#currencyID").val();
                let action;
                if (currencyID > 0) {
                    action = "updateCurrency";
                } else {
                    action = "addCurrency";
                }
                var form = $(this);
                var formData = form.serialize();
                formData += "&action=" + action;
                $.ajax({
                    url: "/App/Controller/Admin/AdminCurrencyController.php",
                    type: "POST",
                    data: formData,
                    success: function (data) {
                        console.log(data);
                        var response = JSON.parse(data);
                        if(response.status == "success"){
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").text(response.message);
                            $("#alertModal").modal("show");
                            setTimeout(function () {
                                window.location.href = "/_y/s/s/parabirimler/CurrencyList.php";
                            }, 1500);
                        } else {
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").text(response.message);
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });

            const urlParams = new URLSearchParams(window.location.search);
            const refAction = urlParams.get('refAction');
            if(refAction){
                modalMessage="Ürün eklemek için önce para birimi eklemelisiniz";
                $("#alertModal .modal-header").removeClass("bg-success").addClass("bg-danger");
                $("#alertModal #alertMessage").html(modalMessage);
                $("#alertModal").modal("show");
            }

		</script>
		
	</body>
</html>