<?php
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$languageId = $_GET["languageID"] ?? $_SESSION["languageID"] ?? 1;
$languageID = intval($languageId);

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/GeneralSettings.php';
$generalSettingsModel = new GeneralSettings($db);

$domain = "";
$siteType = 0;
$isMemberRegistration = 0;
$buttonName = "Ekle";

if($languageID > 0){
    $generalSettings = $generalSettingsModel->getGeneralSettings($languageID);
    if(!empty($generalSettings)) {
        $generalSettings = $generalSettings[0];
        $domain = $generalSettings['domain'];
        $siteType = $generalSettings['sitetip'];
        $isMemberRegistration = $generalSettings['uyelik'];

        $buttonName = "Güncelle";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Site Ayarları Pozitif E-Ticaret</title>

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
							<li class="active">Site Ayarları</li>
						</ol>
					</div>

					<div class="section-body contain-lg">

						<div class="row">
                            <div class="card-body">
                                <div class="alert alert-callout">
                                    <strong>Alt alan adları ekleyin!</strong>
                                    <p>Sitenizin farklı dilleri için farklı alt alan adları kullanmak isteyebilirsiniz.<br>
                                        Örneğin İngilizce dil için en.site.com, Türkçe dil için www.site.com gibi.</p>
                                </div>
                            </div>

							<div class="col-md-12">
								<div class="card">
									
									<form id="addGeneralSettingsForm" class="form form-validation form-validate" role="form" method="post">

										<div class="card-body">
											<div class="row">
												<div class="col-6">
                                                    <div class="form-group">
                                                        <select id="languageID" name="languageID" class="form-control">
                                                            <?php
                                                            foreach ($languages as $language){
                                                                $selected = $languageID == $language['languageID'] ? "selected" : "";
                                                                echo "<option value='".$language['languageID']."' $selected>".$language['languageName']."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <label for="languageID">Genel ayarlar için dil seçin</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <select id="siteType" name="siteType" class="form-control">
                                                            <option value="0" <?=$siteType == 0 ? "selected" : ""?>>Kurumsal</option>
                                                            <option value="1" <?=$siteType == 1 ? "selected" : ""?>>E-Ticaret</option>
                                                        </select>
                                                        <label for="siteType">Site Tipi</label>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <select id="isMemberRegistration" name="isMemberRegistration" class="form-control">
                                                            <option value="0" <?=$isMemberRegistration == 0 ? "selected" : ""?>>Yok</option>
                                                            <option value="1" <?=$isMemberRegistration == 1 ? "selected" : ""?>>Var</option>
                                                        </select>
                                                        <label for="isMemberRegistration">Üye Kayıt</label>
                                                    </div>
                                                </div>
                                            </div>
											<div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group floating-label">
                                                        <input type="text" class="form-control" id="domain" name="domain" value="<?=$domain?>" required="" aria-required="true" aria-invalid="true" aria-describedby="domain-error">
                                                        <label for="company">Domain</label>
                                                    </div>
                                                </div><!--end .col -->
                                                <div class="col-md-6">
                                                    <div class="form-group floating-label">
                                                        <input type="text" class="form-control" id="domainr" name="domainr" value="<?=$domain?>" required="" aria-required="true" aria-invalid="true" data-rule-equalto="#domain">
                                                        <label for="functiontitle">Domain Tekrar</label>
                                                    </div>
                                                </div>
                                            </div>
										</div>

										<!-- BEGIN FORM FOOTER -->
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
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
			<!-- END CONTENT -->
            
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
			$("#addGeneralSettingsphp").addClass("active");

            //dil değişirse
            $("#languageID").change(function(){
                var languageID = $(this).val();
                //AddGeneralSettings.php?languageID=1
                window.location.href = "/_y/s/s/genelayarlar/AddGeneralSettings.php?languageID="+languageID;
            });

            $("#addGeneralSettingsForm").submit(function(e){
                e.preventDefault();
                var languageID = $("#languageID").val();
                var domain = $("#domain").val();
                var domainr = $("#domainr").val();
                var siteType = $("#siteType").val();
                var isMemberRegistration = $("#isMemberRegistration").val();

                //domain boş olamaz
                if(domain === ""){
                    $("#alertMessage").text("Domain boş olamaz.");
                    $("#alertModal").modal("show");
                    return;
                }

                if(domain !== domainr){
                    $("#alertMessage").text("Domainler uyuşmuyor.");
                    $("#alertModal").modal("show");
                    return;
                }

                $.ajax({
                    url: "/App/Controller/Admin/AdminGeneralSettingsController.php",
                    type: "POST",
                    data: {
                        languageID: languageID,
                        domain: domain,
                        action: "addGeneralSettings",
                        siteType: siteType,
                        isMemberRegistration: isMemberRegistration
                    },
                    success: function(data){
                        console.log(data);
                        var response = JSON.parse(data);
                        if(response.status === "success"){
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").text(response.message);
                            $("#alertModal").modal("show");
                            setTimeout(function(){
                                $("#alertModal").modal("hide");
                            },1500);
                        }else{
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").text(response.message);
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });

		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>
