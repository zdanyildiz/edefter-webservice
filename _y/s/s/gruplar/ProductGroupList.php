<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

include_once MODEL . "/Admin/AdminProductGroup.php";
$adminProductGroup = new AdminProductGroup($db);

$productGroupsResult = $adminProductGroup->getProductGroups();

if($productGroupsResult['status'] === "success") {
    $productGroups = $productGroupsResult['data'];
}
$productGroups = $productGroups ?? [];
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Ürün Grup Liste Pozitif E-Ticaret</title>
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
                            <li class="active">Ürün Grubu Liste</li>
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
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
                                            foreach ($productGroups as $productGroup) {
                                                ?>
                                                <tr>
                                                    <td><?=$productGroup['productGroupID']?></td>
                                                    <td><?=$productGroup['productGroupName']?></td>
                                                    <td>
                                                        <a href="/_y/s/s/gruplar/AddProductGroup.php?groupID=<?=$productGroup['productGroupID']?>" class="btn ink-reaction btn-raised btn-primary btn-sm">Düzenle</a>
                                                        <a href="javascript:void(0)" data-id="<?=$productGroup['productGroupID']?>" class="btn ink-reaction btn-raised btn-danger btn-sm">Sil</a>
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
                            <p id="alertMessage">Grubu silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                            <button type="button" class="btn btn-danger" id="deleteProductGroup">Sil</button>
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
			$("#productGroupListphp").addClass("active");

            $(".btn-danger").click(function () {
                var groupID = $(this).data("id");
                $("#alertModal").modal("show");
                $("#deleteProductGroup").data("id", groupID);
            });

            $("#deleteProductGroup").click(function () {
                var groupID = $(this).data("id");
                var action = "deleteProductGroup";
                $.ajax({
                    url: "/App/Controller/Admin/AdminProductGroupController.php",
                    type: "POST",
                    data: {
                        groupID: groupID,
                        action: action
                    },
                    success: function (response) {
                        console.log(response);
                        var data = JSON.parse(response);
                        if (data.status === "success") {
                            window.location.reload();
                        }
                        else {
                            $("#alertMessage").text(data.message);
                        }
                    }
                });
            });
		</script>
	</body>
</html>
