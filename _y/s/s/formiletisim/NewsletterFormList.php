<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Helper $helper
 */

include_once MODEL .'Admin/AdminForm.php';
$formModel = new AdminForm($db);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
        <title>Bülten Formları Pozitif Eticaret</title>
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
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		<div id="base">
			<div id="content">
				<section>
                    <div class="section-header">
                        <ol class="breadcrumb">
                            <li class="active">İletişim Formları</li>
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
									                <th>E-Posta</th>
									                <th>İşlem</th>
									            </tr>
									        </thead>
									        <tbody>
									            <?php
									            $currentPage = $_GET['page'] ?? 1;
									            $limit = 10;
									            $offset = ($currentPage - 1) * $limit;

									            $newsletterForms = $formModel->getNewsletterForms('id DESC', $limit, $offset);

									            if (!empty($newsletterForms)) {
									                foreach ($newsletterForms as $index => $form) {
									                    $form['name'] = $helper->decrypt($form['name'], $config->key);
									                    $form['email'] = $helper->decrypt($form['email'], $config->key);
									                    echo "<tr>";
									                    echo "<td>" . ($offset + $index + 1) . "</td>";
									                    echo "<td>" . htmlspecialchars($form['name']) . "</td>";
									                    echo "<td>" . htmlspecialchars($form['email']) . "</td>";
									                    echo "<td><a href='javascript:void(0);' data-id='" . $form['id'] . "' data-email='". $form['email'] ."' class='deleteNewsletterLink'>Sil</a></td>";
									                    echo "</tr>";
									                }
									            } else {
									                echo "<tr><td colspan='4'>Kayıt bulunamadı</td></tr>";
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

            <div class="modal fade" id="deleteNewsletterFormConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteNewsletterFormConfirmLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">Kişi Sil</h4>
                        </div>
                        <div class="modal-body">
                            <p>Kişiyi silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="deleteNewsletterConfirmButton">Sil</button>
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
			$("#newsletterFormListphp").addClass("active");

            function showAlertModal(message,status="danger"){
                let cardHead = $("#alertModal .card-head");

                if(status === "danger"){
                    cardHead.removeClass("style-success");
                    cardHead.removeClass("style-warning");
                    cardHead.addClass("style-danger");
                }
                else if(status === "warning"){
                    cardHead.removeClass("style-success");
                    cardHead.removeClass("style-danger");
                    cardHead.addClass("style-danger");
                }
                else{
                    cardHead.removeClass("style-danger");
                    cardHead.removeClass("style-warning");
                    cardHead.addClass("style-success");
                }

                $("#alertMessage").html(message);
                $("#alertModal").modal("show");
            }

            $(document).ready(function () {
                // Silme butonuna tıklama olayını dinle
                $(document).on('click', '.deleteNewsletterLink', function () {
                    var itemId = $(this).data('id'); // Silinecek öğenin ID'sini al
                    var itemEmail = $(this).data('email');
                    $('#deleteNewsletterConfirmButton').data('id', itemId);
                    $('#deleteNewsletterConfirmButton').data('email', itemEmail);
                    $('#deleteNewsletterFormConfirm').modal('show'); // Onay modalını göster
                });

                // Onay butonuna tıklama olayını dinle
                $(document).on('click', '#deleteNewsletterConfirmButton', function () {
                    var itemId = $(this).data('id'); // Silinecek öğenin ID'sini al
                    var itemEmail = $(this).data('email');
                    $.ajax({
                        url: '/App/Controller/Admin/AdminFormController.php', // Silme işlemi için URL
                        type: 'POST',
                        data: {
                            action: 'deleteNewsletterForm', // İşlem adı
                            id: itemId ,
                            email: itemEmail
                        },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') {
                                showAlertModal("success","Silme İşlemi Başarılı")
                                location.reload(); // Sayfayı yenile
                            } else {
                                showAlertModal("error",'Silme işlemi başarısız: ' + response.message);
                            }
                        },
                        error: function () {
                            alert('Bir hata oluştu, lütfen tekrar deneyin.');
                        }
                    });
                });
            });

		</script>
	</body>
</html>
