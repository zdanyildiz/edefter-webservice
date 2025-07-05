<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 */
include_once MODEL."Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

$getLanguages = $adminLanguage->getLanguages();
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Dil Listesi Pozitif Eticaret</title>

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
        <link type="text/css" rel="stylesheet"
              href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
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
							<li class="active">Diller</li>
						</ol>
					</div>
					
					
					<div class="section-body contain-lg">

						<!-- BEGIN VALIDATION FORM WIZARD -->
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>#</th>
													<th>Dil</th>
													<th>Dil Kisa Ad</th>
                                                    <th>Dil Sıra</th>
                                                    <th>Dil Durumu</th>
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
                                            //getLanguages() method returns an array of languages
                                            foreach($getLanguages as $language){
                                                $languageID = $language['languageID'];
                                                $languageName = $language['languageName'];
                                                $languageCode = $language['languageCode'];
                                                $isMainLanguage = $language['isMainLanguage'];
                                                $isActive = $language['isActive'];
                                                $languageOrder = $language['languageOrder'];

                                            ?>
                                            <tr id="tr<?=$languageID?>">
                                                <td><?=$languageID?></td>
                                                <td><?=$languageName?></td>
                                                <td><?=$languageCode?></td>
                                                <td><?=$languageOrder?></td>
                                                <td>
                                                    <?php
                                                    if($isActive == 1){
                                                        echo "Aktif";
                                                    }else{
                                                        echo "Pasif";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="/_y/s/s/diller/AddLanguage.php?languageID=<?=$languageID?>&lang" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                                                    <a
                                                        id="dilsil"
                                                        href="#deleteLanguageConfirmModal"
                                                        class="btn btn-icon-toggle"
                                                        data-id="<?=$languageID?>"
                                                        data-toggle="modal"
                                                        data-placement="top"
                                                        data-original-title="Sil"
                                                        data-target="#deleteLanguageConfirmModal"
                                                        data-backdrop="true">
                                                            <i class="fa fa-trash-o"></i>
                                                    </a>
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
			<?php require_once(ROOT."/_y/s/b/menu.php");?>
		</div>

        <div class="modal fade" id="deleteLanguageConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteLanguageConfirmModalLabel" aria-hidden="true">
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
                        <p id="alertMessage">Dili silmek istediğinize emin misiniz?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-danger" id="deleteBrandConfirmButton">Sil</button>
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

		<script>
			$("#languageListphp").addClass("active");

            //deleteLanguageConfirmModal
            $('#deleteLanguageConfirmModal').on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var languageID = button.data('id');
                var tr = $("#tr"+languageID);
                var languageName = tr.find("td:eq(1)").text();
                $("#alertMessage").text(languageName + " dilini silmek istediğinize emin misiniz?");
                $("#deleteBrandConfirmButton").data("id", languageID);
            });

            //deleteBrandConfirmButton
            $("#deleteBrandConfirmButton").click(function(){
                var languageID = $(this).data("id");
                $.ajax({
                    url: "/App/Controller/Admin/AdminLanguageController.php",
                    type: "POST",
                    data: {
                        languageID: languageID, action: "deleteLanguage"
                    },
                    success: function(data){
                        var response = JSON.parse(data);
                        if(response.status == "success"){
                            $("#tr"+languageID).remove();
                            $("#deleteLanguageConfirmModal").modal("hide");
                        }else{
                            alert(response.message);
                        }
                    }
                });
            });
		</script>
	</body>
</html>
