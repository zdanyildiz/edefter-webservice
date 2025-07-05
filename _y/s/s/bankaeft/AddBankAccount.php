<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$buttonName = "Ekle";

$languageID = $_GET["languageID"] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

$languages = $adminLanguage->getLanguages();

include_once MODEL . 'Admin/AdminBankAccount.php';
$adminBankAccount = new AdminBankAccount($db);

$bankAccountID = $_GET["bankAccountID"] ?? 0;
$bankAccountID = intval($bankAccountID);

if ($bankAccountID > 0) {
    $bankAccount = $adminBankAccount->getBankAccount($bankAccountID);
    if($bankAccount['status'] == 'success'){
        $bankAccount = $bankAccount['data'];
        $bankAccountLanguageID = $bankAccount["languageID"];
        $bankAccountID = $bankAccount["bankAccountID"];
        $accountName = $bankAccount["accountName"];
        $bankName = $bankAccount["bankName"];
        $branchName = $bankAccount["branchName"];
        $accountNumber = $bankAccount["accountNumber"];
        $ibanNumber = $bankAccount["ibanNumber"];
        $buttonName = "Güncelle";
    }

}

$bankAccountID = $bankAccountID ?? 0;
$accountName = $accountName ?? "";
$bankName = $bankName ?? "";
$branchName = $branchName ?? "";
$accountNumber = $accountNumber ?? "";
$ibanNumber = $ibanNumber ?? "";
$bankAccountLanguageID = $bankAccountLanguageID ?? 0;

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Banka Hesabı Ekle Pozitif Eticaret</title>

		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<!-- END META -->

		<!-- BEGIN STYLESHEETS -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
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
                            <li class="active">Banka Hesabı Ekle / Düzenle</li>
                        </ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">

									<form name="addBankAccountForm" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="bankAccountID" value="<?=$bankAccountID?>">
										<div class="card-body <?php echo ($bankAccountID>0) ? "hidden" : "" ?>">
                                            <div class="form-group">
                                                <select name="languageID" id="languageID" class="form-control">
                                                    <option value="0">Dil Seçin</option>
                                                    <?php foreach($languages as $language){
                                                        $selected = $language['languageID'] == $bankAccountLanguageID ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <p class="help-block">Banka Hesabı Eklemek için dil seçin!</p>
                                            </div>
                                        </div>
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													
													<div class="row">		

														<div class="col-sm-8">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="accountName" 
																id="accountName" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$accountName?>"
																placeholder="Hesap Adını Giriniz" required aria-required="true" >
																<label for="accountName">Hesap Adını Giriniz</label>
															</div>
														</div>
                                                    </div>

                                                    <div class="row">

														<div class="col-sm-3">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="bankName" 
																id="bankName" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$bankName?>"
																placeholder="Banka Adını Yazın" required aria-required="true" >
																<label for="bankName">Banka Adını Yazın</label>
															</div>
														</div>
                                                    </div>

                                                    <div class="row">


														<div class="col-sm-3">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="branchName" 
																id="branchName" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$branchName?>"
																placeholder="Hesap Şube Numarasını Giriniz" required aria-required="true" >
																<label for="branchName">Hesap Şube Numarasını Giriniz</label>
															</div>
														</div>	
														<div class="col-sm-3">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="accountNumber" 
																id="accountNumber" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$accountNumber?>"
																placeholder="Hesap Numarasını Giriniz" required aria-required="true" >
																<label for="accountNumber">Hesap Numarasını Giriniz</label>
															</div>
														</div>
                                                    </div>

                                                    <div class="row">



														<div class="col-sm-3">
															<div class="form-group">
															<input 
																type="text" 
																class="form-control" 
																name="ibanNumber" 
																id="ibanNumber" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$ibanNumber?>"
																placeholder="Banka İban Numarasını Giriniz" required aria-required="true" >
																<label for="ibanNumber">Banka İban Numarasını Giriniz</label>
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
		<!-- END BASE -->
        
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
			$("#addBankAccountphp").addClass("active");

            //form dinleyelim, hiçbir alan boş olamaz, id 0 değilse action parametresi update olacak
            $("form[name='addBankAccountForm']").submit(function(e){
                e.preventDefault();
                //alertModal card-head success silelim danger atayalım
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");

                var languageID = $("#languageID").val();
                var accountName = $("#accountName").val();
                var bankName = $("#bankName").val();
                var branchName = $("#branchName").val();
                var accountNumber = $("#accountNumber").val();
                var ibanNumber = $("#ibanNumber").val();
                var bankAccountID = $("input[name='bankAccountID']").val();

                //languageID 0'dan büyük olmalı, diğerleri boş olamaz
                if(languageID == 0 || accountName == "" || bankName == "" || branchName == "" || accountNumber == "" || ibanNumber == ""){
                    $("#alertMessage").html("Tüm Alanlar doldurulmalıdır");
                    $("#alertModal").modal("show");
                    return;
                }

                var action = "addBankAccount";
                if(bankAccountID > 0){
                    action = "updateBankAccount";
                }
                $.ajax({
                    url: "/App/Controller/Admin/AdminBankAccountController.php",
                    type: "POST",
                    data: {
                        action: action,
                        languageID: languageID,
                        accountName: accountName,
                        bankName: bankName,
                        branchName: branchName,
                        accountNumber: accountNumber,
                        ibanNumber: ibanNumber,
                        bankAccountID: bankAccountID
                    },
                    success: function(response){
                        var data = JSON.parse(response);
                        if(data.status == "success"){
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").html(data.message);
                            $("#alertModal").modal("show");
                            setTimeout(function(){
                                window.location.href = "/_y/s/s/bankaeft/BankAccountsList.php";
                            }, 2000);
                        }else{
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