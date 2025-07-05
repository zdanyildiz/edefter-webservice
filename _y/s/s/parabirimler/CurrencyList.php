<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

include_once MODEL . 'Admin/AdminCurrency.php';
$adminCurrency = new AdminCurrency($db);
$currencies = $adminCurrency->getCurrencies();

$buttonName = "Ekle";


?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Para Birimleri Pozitif Eticaret</title>
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
													<th>Simge</th>
													<th>Kodu</th>
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if(!empty($currencies)){
                                                foreach ($currencies as $currency) {
                                                    $currencyID = $currency["currencyID"];
                                                    $currencyName = $currency["currencyName"];
                                                    $currencySymbol = $currency["currencySymbol"];
                                                    $currencyCode = $currency["currencyCode"];
                                                    $currencyRate = $currency["currencyRate"];
                                                    $currencyRateDate = $currency["currencyRateDate"];
                                                    ?>
                                                    <tr id="tr<?=$currencyID?>">
                                                        <td><?=$currencyID?></td>
                                                        <td><?=$currencyName?></td>
                                                        <td><?=$currencySymbol?></td>
                                                        <td><?=$currencyCode?></td>
                                                        <td>
                                                            <a href="/_y/s/s/parabirimler/AddCurrency.php?currencyID=<?=$currencyID?>" class="btn btn-primary btn-sm">Düzenle</a>
                                                            <button class="btn btn-danger btn-sm deleteCurrency" data-toggle="modal" data-target="#currencyDeleteConfrim" data-currencyid="<?=$currencyID?>">Sil</button>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
											?>
											</tbody>
										</table>
									</div><!--end .card-body -->
								</div><!--end .card -->
							</div><!--end .col -->
						</div><!--end .row -->
						<!-- END VALIDATION FORM WIZARD -->
					</div><!--end .section-body -->
				</section>
			</div>

            <?php require_once(ROOT."/_y/s/b/menu.php");?>

            <div class="modal fade" id="currencyDeleteConfrim" tabindex="-1" role="dialog" aria-labelledby="currencyDeleteConfrimLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="card">
                        <div class="card-head card-head-sm style-danger">
                            <header class="modal-title" id="currencyDeleteConfrimLabel">Para Birimi Silme</header>
                            <div class="tools">
                                <div class="btn-group">
                                    <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                        <i class="fa fa-close"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>Para Birimi silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-danger" id="deleteButton">Sil</button>
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


		</div><!--end #base-->
		<!-- END BASE -->

		<!-- BEGIN JAVASCRIPT -->

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

			$(document).ready(function()
			{
                $('#currencyDeleteConfrim').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget) // Button that triggered the modal
                    var currencyID = button.data('currencyid') // Extract info from data-* attributes
                    var modal = $(this)
                    modal.find('.modal-title').text('Para Birimi Silme')
                    modal.find('.modal-body p').text('Para Birimi silmek istediğinize emin misiniz?')
                    modal.find('#deleteButton').data('currencyid', currencyID)

                    modal.find('#deleteButton').on('click', function () {
                        var currencyID = $(this).data('currencyid');
                        var action = "deleteCurrency";
                        var currencyID = $(this).data('currencyid');
                        $.ajax({
                            url: "/App/Controller/Admin/AdminCurrencyController.php",
                            type: "POST",
                            data: {
                                action: action, currencyID: currencyID
                            },
                            success: function (data) {
                                console.log(data);
                                var response = JSON.parse(data);
                                if (response.status == "success") {
                                    location.reload();
                                } else {
                                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                    $("#alertMessage").text(response.message);
                                    $("#alertModal").modal("show");
                                }
                            }
                        });
                    });
                });
			 });
		</script>
		<script>
			$("#currencyListphp").addClass("active");
		</script>
	</body>
</html>
