<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/AdminCompany.php';
$companyModel = new AdminCompany($db);

$parentCompanyID = 0;
$companyData = $companyModel->getCompanyByLanguageID($languageID);
if(!empty($companyData)) {
    
    $parentCompanyID = $companyData['companyID'];
}

$branchData = [];
if($parentCompanyID > 0) {
    $branchData = $companyModel->getBranchesByCompanyID($parentCompanyID);
}

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
        <title>Firma Bilgileri Pozitif Eticaret</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="your,keywords">
        <meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/select2/select2.css?1424887856" />

        <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

        <!--[if lt IE 9]>
        <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
        <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
        <![endif]-->
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		<div id="base">
			<div id="content">
				<section>
                    <div class="section-header">
                        <ol class="breadcrumb">
                            <li class="active">Firma Bilgileriniz</li>
                        </ol>
                    </div>

					<div class="section-body contain-lg">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <select id="languageID" name="languageID" class="form-control">
                                    <?php
                                    if(!empty($languages)){
                                        foreach ($languages as $lang){
                                            ?>
                                            <option value="<?=$lang['languageID']?>" <?php if($lang['languageID'] == $languageID) echo "selected"; ?>><?=$lang['languageName']?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
                            </div>
                        </div>
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>#</th>
													<th>Firma Ad</th>
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if(!empty($branchData)){
                                                foreach ($branchData as $branch){
                                                    $branchID = $branch['branchID'];
                                                    $branchName = $branch['branchName'];
                                                    $branchShortName = $branch['branchShortName'];
                                                    ?>
                                                    <tr id="tr<?=$branchID?>">
                                                        <td><?=$branchID?></td>
                                                        <td><?=$branchShortName?></td>
                                                        <td>
                                                            <a href="/_y/s/s/firmabilgileri/AddCompanySettings.php?action=updateBranch&companyID=<?=$branchID?>" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                                                            <a
                                                                id="deleteBranch"
                                                                href="#deleteCompanyComfirm"
                                                                class="btn btn-icon-toggle"
                                                                data-id="<?=$branchID?>"
                                                                data-toggle="modal"
                                                                data-placement="top"
                                                                data-original-title="Sil"
                                                                data-target="#deleteCompanyComfirm"
                                                                data-backdrop="true">
                                                                <i class="fa fa-trash-o"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
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
			<?php require_once(ROOT."/_y/s/b/menu.php");?>

            <div class="modal fade" id="deleteCompanyComfirm" tabindex="-1" role="dialog" aria-labelledby="deleteCompanyComfirmLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">Firma Bilgisi Sil</h4>
                        </div>
                        <div class="modal-body">
                            <p>Firma Bilgisini silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="deleteButton">Sil</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
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
        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

        <script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
        <script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>

        <script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

		<script>
			$("#companySettingsListphp").addClass("active");

            $(document).on("click","#deleteBranch",function(){
                var branchID = $(this).data("id");
                $("#deleteButton").data("id",branchID);
            });

            $(document).on("click","#deleteButton",function(){
                var companyID = $(this).data("id");
                var action = "deleteCompany";
                $.ajax({
                    url: "/App/Controller/Admin/AdminCompanyController.php",
                    type: "POST",
                    data: {
                        action: action,
                        companyID: companyID
                    },
                    success: function(data){
                        console.log(data);
                        var response = JSON.parse(data);
                        if(response.status == "success"){
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").text(response.message);
                            $("#alertModal").modal("show");
                            setTimeout(function(){
                                window.location.href = "/_y/s/s/firmabilgileri/CompanySettingsList.php";
                            },1000);
                        }
                        else{
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").text(response.message);
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });
		</script>
	</body>
</html>
