<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 * @var Helper $helper
 */

$buttonName = "Dil Ekle";

include_once MODEL."Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

$allLanguages = $adminLanguage->getAllLanguages();

$languageCode = $_GET['languageCode'] ?? "tr";
$languageID = $_GET['languageID'] ?? 0;

if($languageID > 0){
    $buttonName = "Dil Düzenle";
    $language = $adminLanguage->getLanguage($languageID);

    if(!empty($language))
    {
        $languageCode = $language['languageCode'];
        $languageName = $language['languageName'];
        $isMainLanguage = $language['isMainLanguage'];
        $isActive = $language['isActive'];
    }
}

$languageID = $languageID ?? 0;
$languageCode = $languageCode ?? "tr";
$languageName = $languageName ?? "";
$isMainLanguage = $isMainLanguage ?? 0;
$isActive = $isActive ?? 0;

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Dil Ekle/Düzenle Pozitif Eticaret</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
              type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
        <link type="text/css" rel="stylesheet"
              href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />

        <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
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
							<li class="active">Dil Ekle</li>
						</ol>
					</div>

					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-md-9">
								<div class="card">
									<form id="addLanguageForm" class="form form-validation form-validate" role="form" method="post">
                                        <input type="hidden" name="languageID" id="languageID" value="<?=$languageID?>">
										<div class="card-body ">
											<div class="row">

												<div class="col-xs-12">
													<div class="row">
														<div class="col-md-12">
															<div class="form-group floating-label">
																<select id="language" name="language" class="form-control" required="" aria-required="true">
																	<?php
																		$languages = explode("*", $allLanguages);
                                                                        foreach($languages as $selectLanguage){
                                                                            $selectLanguageExp = explode("|", $selectLanguage);
                                                                            $selectLanguageName = $selectLanguageExp[0];
                                                                            $selectLanguageCode = $selectLanguageExp[1];

                                                                            $selected = "";
                                                                            if($selectLanguageCode == $helper->toLowerCase($languageCode)){
                                                                                $selected = "selected";
                                                                            }
																		?>
																		<option value="<?=$selectLanguageCode?>" <?=$selected?>><?=$selectLanguageName?></option>
																	<?php } ?>
																</select>
																<label for="dil">Dil Seçin</label>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<div class="checkbox checkbox-inline checkbox-styled">
																	<label>
																		<input type="checkbox" name="isMainLanguage" id="isMainLanguage" value="1" <?=$isMainLanguage == 1 ? "checked" : ""?>>
																		<span>Ana Dil mi?</span>
																	</label>
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<div class="checkbox checkbox-styled">
																	<label>
																		<input type="checkbox" name="isActive" id="isActive" value="1" <?=$isActive == 1 ? "checked" : ""?>>
																		<span>Aktif mi?</span>
																	</label>
																</div>
															</div>
														</div>
                                                        <div class="row"></div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="checkbox checkbox-styled">
                                                                    <label>
                                                                        <input type="checkbox" name="translateWithAI" id="translateWithAI" value="1" >
                                                                        <span>Kategori ve sayfaları yapay zeka ile çevir?</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="checkbox checkbox-styled">
                                                                    <label>
                                                                        <input type="checkbox" name="copyBanner" id="copyBanner" value="1" >
                                                                        <span>Tüm Banner'ları kopyala</span>
                                                                    </label>
                                                                </div>
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

            <div class="modal fade" id="formAlertModal" tabindex="-1" role="dialog" aria-labelledby="formAlertModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="formAlertModalLabel">Uyarı</h4>
                        </div>
                        <div class="modal-body">
                            <p id="formAlertText"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>
		</div>

        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

        <script src="/_y/assets/js/libs/select2/select2.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

		<script>
			$("#addLanguagephp").addClass("active");

            //addLanguageForm submit
            $("#addLanguageForm").submit(function(e){
                e.preventDefault();

                //#language value'su code olacak, text'i ise name olacak
                var languageCode = $("#language").val();
                var languageName = $("#language option:selected").text();
                var isMainLanguage = $("#isMainLanguage").is(":checked") ? 1 : 0;
                var isActive = $("#isActive").is(":checked") ? 1 : 0;
                var action = "addLanguage";
                var languageID = $("#languageID").val();

                if(languageID > 0){
                    action = "updateLanguage";
                }
                var translateWithAI = $("#translateWithAI").is(":checked") ? 1 : 0;

                var formData = {
                    action: action,
                    languageName: languageName,
                    languageCode: languageCode,
                    isMainLanguage: isMainLanguage,
                    isActive: isActive,
                    languageID: languageID,
                    translateWithAI: translateWithAI
                };

                $.ajax({
                    url: "/App/Controller/Admin/AdminLanguageController.php",
                    type: "POST",
                    data: formData,
                    success: function(data){
                        console.log(data);
                        var response = JSON.parse(data);
                        if(response.status == "success"){
                            $("#formAlertText").text(response.message);
                            $("#formAlertModal").modal("show");
                            setTimeout(function(){
                                window.location.href = "/_y/s/s/diller/LanguageList.php";
                            }, 1500);
                        }else{
                            $("#formAlertText").text(response.message);
                            $("#formAlertModal").modal("show");
                        }
                    }
                });
            });

            $('select').select2();
		</script>
	</body>
</html>
