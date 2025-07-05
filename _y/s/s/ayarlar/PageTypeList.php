<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

include_once MODEL . 'Admin/AdminPageType.php';
$pageTypeModel = new AdminPageType($db);
$pageTypes = $pageTypeModel->getPageTypes();

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
        <title>Sayfa Tipleri Pozitif Eticaret</title>
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
                            <li class="active">Para Birimleri</li>
                        </ol>
                    </div>

					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>#</th>
													<th>Ad</th>
                                                    <th>Yetki</th>
                                                    <th>Görünüm</th>
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if(!empty($pageTypes))
                                            {
                                                foreach($pageTypes as $pageType)
                                                {
                                                    $pageTypeID = $pageType['pageTypeID'];
                                                    $pageTypeName = $pageType['pageTypeName'];
                                                    $pageTypePermission = $pageType['pageTypePermission'];
                                                    $pageTypeView = $pageType['pageTypeView'];
                                                    $pageTypeDeleted = $pageType['pageTypeDeleted'];
                                                    ?>
                                                    <tr id="tr<?=$pageTypeID?>">
                                                        <td><?=$pageTypeID?></td>
                                                        <td><?=$pageTypeName?></td>
                                                        <td><?=$pageTypePermission?></td>
                                                        <td><?=$pageTypeView?></td>
                                                        <td>
                                                            <a
                                                                href="/_y/s/s/ayarlar/AddPageType.php?pageTypeID=<?=$pageTypeID?>"
                                                                class="btn btn-icon-toggle"
                                                                data-toggle="tooltip"
                                                                data-placement="top"
                                                                data-original-title="Düzenle">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <?php
                                                            //admin yetkisi yoksa silme yapamasın
                                                            if($adminAuth==0){
                                                            ?>
                                                            <a  id="pageTypeDelete"
                                                                href="#textModal"
                                                                class="btn btn-icon-toggle"
                                                                data-id="<?=$pageTypeID?>"
                                                                data-toggle="modal"
                                                                data-placement="top"
                                                                data-original-title="Sil"
                                                                data-target="#pageTypeDeleteModal"
                                                                data-backdrop="true">
                                                                <i class="fa fa-trash-o"></i>
                                                            </a>
                                                                <?php
                                                            }
                                                            ?>
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

            <div class="modal fade" id="pageTypeDeleteModal" tabindex="-1" role="dialog" aria-labelledby="pageTypeDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">Sayfa Tip Sil</h4>
                        </div>
                        <div class="modal-body">
                            <p>Sayfa tipini silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="deletePageTypeButton">Sil</button>
                        </div>
                    </div>
                </div>
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
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
        <script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
        <script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>
		<script>
			
		</script>
		<script>
			$("#pageTypeListphp").addClass("active");

            $(document).ready(function () {
                $(document).on("click","#pageTypeDelete",function () {
                    var pageTypeID = $(this).data("id");
                    $("#pageTypeID").val(pageTypeID);
                    $("#deletePageTypeButton").data("id",pageTypeID);
                });

                $(document).on("click","#deletePageTypeButton",function () {
                    var pageTypeID = $(this).data("id");
                    var action = "deletePageType";
                    $.ajax({
                        url: "/App/Controller/Admin/AdminPageTypeController.php",
                        type: "POST",
                        data: {
                            action: action,
                            pageTypeID: pageTypeID
                        },
                        success: function (data) {
                            var response = JSON.parse(data);
                            if(response.status == "success"){
                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                $("#alertMessage").text(response.message);
                                $("#alertModal").modal("show");
                                setTimeout(function(){
                                    window.location.href = "/_y/s/s/ayarlar/PageTypeList.php";
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
            });
		</script>
	</body>
</html>
