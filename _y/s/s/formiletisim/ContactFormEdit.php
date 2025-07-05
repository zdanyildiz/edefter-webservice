<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Helper $helper
 */

include_once MODEL .'Admin/AdminForm.php';
$formModel = new AdminForm($db);

$formId = $_GET["id"] ?? 0;
$formId = intval($formId);
$form = null;
if ($formId>0){
    $form = $formModel->getContactFormById($formId);
    if(!is_null($form)){
        $form['adsoyad'] = $helper->decrypt($form['adsoyad'], $config->key);
        $form['telefon'] = $helper->decrypt($form['telefon'], $config->key);
        $form['eposta'] = $helper->decrypt($form['eposta'], $config->key);

        if($form['formbildirim']==1){
            $formModel->markAsRead($formId);
        }

    }
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
        <title>İltişim Formu Düzenleı Pozitif Eticaret</title>
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
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/summernote/summernote.min.css">

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
                            <li class="active">İletişim Formu Düzenle</li>
                        </ol>
                    </div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
                                        <?php
                                        if(!is_null($form)){
                                            ?>
                                                <input type="hidden" name="formId" id="formId" value="<?php echo $form['formid'];?>"">
                                            <div class="form-group no-padding">
                                                <label>Ad Soyad:</label>
                                                <p><?php echo htmlspecialchars($form['adsoyad']); ?></p>
                                            </div>
                                            <div class="form-group no-padding">
                                                <label>Gönderim Tarihi:</label>
                                                <p><?php echo htmlspecialchars($form['tarih']); ?></p>
                                            </div>
                                            <div class="form-group">
                                                <label>E-Posta:</label>
                                                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($form['eposta']); ?>" class="form-control" required>
                                            </div>
                                            <div class="form-group no-padding">
                                                <label>Telefon:</label>
                                                <p><?php echo htmlspecialchars($form['telefon']); ?></p>
                                            </div>
                                            <div class="form-group no-padding">
                                                <label>Mesaj İçeriği:</label>
                                                <p><?php echo nl2br(htmlspecialchars($form['mesaj'])); ?></p>
                                            </div>
                                            <div class="form-group no-padding">
                                                <label>Yanıt Silsilesi:</label>
                                                <ul>
                                                    <?php
                                                    function renderResponses($responses) {
                                                        foreach ($responses as $response) {
                                                            echo "<li>";
                                                            echo "<strong>" . htmlspecialchars($response['tarih']) . ":</strong> " . nl2br(htmlspecialchars($response['mesaj']));
                                                            if (!empty($response['subResponses'])) {
                                                                echo "<ul>";
                                                                renderResponses($response['subResponses']);
                                                                echo "</ul>";
                                                            }
                                                            echo "</li>";
                                                        }
                                                    }

                                                    if (!empty($form['responses'])) {
                                                        renderResponses($form['responses']);
                                                    } else {
                                                        echo "<li>Yanıt bulunamadı.</li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <div class="form-group no-padding">
                                        <textarea class="answer" id="answer">
                                            <?php echo "Sayın " . (isset($form['adsoyad']) ? htmlspecialchars($form['adsoyad']) : ''); ?>
                                        </textarea>
                                        </div>
                                        <div class="form-group">
                                            <button id="answerButton" class="btn btn-primary-bright">Gönder</button>
                                        </div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once(ROOT."/_y/s/b/menu.php");?>

            <div class="modal fade" id="deleteContactFormConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteContactFormConfirmLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">Formu Sil</h4>
                        </div>
                        <div class="modal-body">
                            <p>Formu silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="deleteConfirmButton">Sil</button>
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

        <script src="/_y/assets/js/libs/summernote/summernote.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>


		<script>
			$("#contactFormListphp").addClass("active");

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

            $("#answer").summernote({
                tabsize: 2,
                height: 200,
                minHeight: 200
            });

            $(document).ready(function () {
                $(document).on("click", "#answerButton", function () {
                    // Form verilerini al
                    var formId = $("#formId").val();
                    let email = $("#email").val();
                    let name = '<?=$adminName?>';
                    let summernote = $("#answer").summernote();
                    let answer = summernote.code();

                    // Alanların boş olup olmadığını kontrol et
                    if (email=='' || answer=='') {
                        showAlertModal("Lütfen tüm zorunlu alanları doldurun.", "warning");
                        return;
                    }

                    // AJAX isteği
                    $.ajax({
                        url: "/App/Controller/Admin/AdminFormController.php",
                        type: "POST",
                        data: {
                            action: "addFormResponse",
                            formId: formId,
                            responseData: {
                                name: name,
                                phone:'',
                                email: email,
                                message: answer
                            }
                        },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === "success") {
                                showAlertModal("Yanıt başarıyla eklendi.", "success");
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            } else {
                                showAlertModal(response.message, "danger");
                            }
                        },
                        error: function () {
                            showAlertModal("Bir hata oluştu, lütfen tekrar deneyin.", "danger");
                        }
                    });
                });
            });
		</script>
	</body>
</html>
