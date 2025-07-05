<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$languageID = $_GET["languageID"] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

$languages = $adminLanguage->getLanguages();

include_once MODEL . 'Admin/AdminBankAccount.php';
$adminBankAccount = new AdminBankAccount($db);

$bankAccounts = $adminBankAccount->getBankAccounts(["languageID" => $languageID]);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Banka Hesapları Pozitif Eticaret</title>
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
                            <li class="active">Banka Bilgileri</li>
                        </ol>
                    </div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="languageID" id="languageID" class="form-control">
                                                <option value="0">Dil Seçin</option>
                                                <?php foreach($languages as $language){
                                                    $selected = $languageID == $language['languageID'] ? "selected" : "";
                                                    ?>
                                                    <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <p class="help-block">Banka Hesapları görüntülemek için dil seçin!</p>
                                        </div>
                                    </div>

									<div class="card-body ">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>#</th>
													<th>Hesap Adı</th>
													<th>Banka Adı</th>
													<th>Hesap Şube Numarası</th>
													<th>Hesap Numarası</th>
													<th>Iban Numarası</th>								
													<th>İşlem</th>

												</tr>
											</thead>
											<tbody>
											<?php
                                            foreach ($bankAccounts as $bankAccount) {

                                                $bankAccountID = $bankAccount["bankAccountID"];
                                                $bankName = $bankAccount["bankName"];
                                                $accountName = substr($bankAccount["accountName"], 0, 40);
                                                $branchName = $bankAccount["branchName"];
                                                $accountNumber = $bankAccount["accountNumber"];
                                                $ibanNumber = substr($bankAccount["ibanNumber"], 0, 4) . "******" . substr($bankAccount["ibanNumber"], -6);
                                                $uniqueID = $bankAccount["uniqueID"];
                                                ?>
                                                <tr>
                                                    <td><?php echo $bankAccountID; ?></td>
                                                    <td><?php echo $accountName; ?></td>
                                                    <td><?php echo $bankName; ?></td>
                                                    <td><?php echo $branchName; ?></td>
                                                    <td><?php echo $accountNumber; ?></td>
                                                    <td><?php echo $ibanNumber; ?></td>
                                                    <td>
                                                        <a href="/_y/s/s/bankaeft/AddBankAccount.php?bankAccountID=<?php echo $bankAccountID; ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                                        <button class="btn btn-danger btn-sm deleteBankAccount" data-toggle="modal" data-target="#bankAccountDeleteConfrim" data-bankaccountid="<?php echo $bankAccountID; ?>">Sil</button>
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
            <div class="modal fade" id="bankAccountDeleteConfrim" tabindex="-1" role="dialog" aria-labelledby="bankAccountDeleteConfrimLabel" aria-hidden="true">
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
                            <p id="alertMessage">Banka Hesabını silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                            <button type="button" class="btn btn-danger" id="deleteProductGroup">Sil</button>
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
			$("#bankAccountsListphp").addClass("active");

            //dilid değiştiğinde sayfayı yenile
            $("#languageID").change(function(){
                var languageID = $(this).val();
                window.location.href = "/_y/s/s/bankaeft/BankAccountsList.php?languageID="+languageID;
            });

            $(".deleteBankAccount").click(function(){
                var bankAccountID = $(this).data("bankaccountid");
                $("#deleteProductGroup").data("bankaccountid", bankAccountID);
            });

            $("#deleteProductGroup").click(function(){
                var bankAccountID = $(this).data("bankaccountid");
                $.ajax({
                    url: "/App/Controller/Admin/AdminBankAccountController.php",
                    type: "POST",
                    data: {
                        action: "deleteBankAccount",
                        bankAccountID: bankAccountID
                    },
                    success: function(response){
                        var result = JSON.parse(response);
                        if(result.status == "success"){
                            window.location.reload();
                        }
                    }
                });
            });
		</script>
	</body>
</html>
