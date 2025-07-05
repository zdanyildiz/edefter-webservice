<?php
/**
 * @var AdminSession $adminSession
 * @var int $adminID
 * @var string $adminName
 * @var string $adminEmail
 * @var string $adminPhone
 * @var string $adminImage
 * @var int $adminAuth
 * @var string $adminType
 * @var string $adminLastLogin
 */

$adminForward = false;
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");

$adminCasper = $adminSession->getAdminCasper();

$admin = $adminCasper->getAdmin();
$admin["lockedStatus"] = true;

$adminCasper->setAdmin($admin);
$adminSession->updateSession("adminCasper",$adminCasper);

$refUrl = $_GET["refUrl"] ?? "/_y/";
$adminCookie = $adminSession->getCookie("adminCasper");
//print_r($adminCookie);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Pozitif E-Ticaret - Locked</title>

		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<!-- END META -->

		<!-- BEGIN STYLESHEETS -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">

		<!-- BEGIN LOCKED SECTION -->
		<section class="section-account">
			<div class="img-backdrop" style="background-image: url('/_y/assets/img/header.jpg')"></div>
			<div class="spacer"></div>
			<div class="card contain-xs style-transparent">
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">
							<img class="img-circle" src="<?=$adminImage?>" alt="" />
							<h2><?=$adminName?></h2>
							<form id="loginFormWithPIN" class="form form-validation form-validate" action="" accept-charset="utf-8" method="post">
							<input type="hidden" name="yoneticiid" value="<?=$adminID?>">
							<input type="hidden" name="refUrl" value="<?=urldecode($refUrl)?>">
								<div class="form-group floating-label">
									<div class="input-group">
										<div class="input-group-content">
											<input type="password" id="adminPin" class="form-control" name="adminPin" data-rule-rangelength="[4, 4]" data-rule-number="true" required="" aria-required="true" aria-invalid="true">
											<label for="password">Pin Kodu</label>
											<p class="help-block"><a href="/_y/s/guvenlik/cikis.php"><?=$adminName?> değil misin?</a></p>
										</div>
										<div class="input-group-btn">
											<button class="btn btn-floating-action btn-primary" type="submit"><i class="fa fa-unlock"></i></button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

			</div><!--end .card -->
		</section>
		<!-- END LOCKED SECTION -->
        <div class="modal fade" id="loginResult" tabindex="-1" role="dialog" aria-labelledby="loginResult" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="simpleModalLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
		<!-- BEGIN JAVASCRIPT -->

        <script src="/_y/assets/js/libs/jquery/jquery-3.7.1.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-3.3.2.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

		<!-- END JAVASCRIPT -->
        <script>
            $(document).ready(function() {
                function renewSessionAjax() {
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', '/_y/s/guvenlik/reNewSession.php', true); // Oturumu yenileyen PHP script'inin yolunu belirtin
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            console.log("Oturum yenilendi");
                        }
                    };
                    xhr.send();
                }
                //renewSessionAjax her 5 dakika bir çağrılacak
                setInterval(renewSessionAjax, 300000);

                function showModal(status, title, message) {
                    var modal = $('#loginResult');
                    var modalHeader = modal.find('.modal-header');
                    modalHeader.addClass('text-' + status);

                    modal.find('.modal-title').text(title);
                    modal.find('.modal-body p').text(message);

                    modal.modal('show');
                }

                $("#loginFormWithPIN").on("submit", function(e){
                    e.preventDefault();

                    var pin = $("#adminPin").val();
                    if(pin.length !== 4){
                        showModal("danger", "Hata", "Pin kodu 4 haneli olmalıdır.");
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        url: "/App/Controller/Admin/AdminController.php?action=loginWithPIN",
                        data: {
                            adminID: <?=$adminID?>,
                            adminPin: pin,
                            refUrl: "<?=urlencode($refUrl)?>"
                        },
                        success: function(response){
                            console.log(response);
                            response = JSON.parse(response);
                            if(response.status === "success"){
                                refUrl = response.refUrl;
                                //urlDecode yapalım
                                refUrl = decodeURIComponent(refUrl);
                                window.location.href = refUrl;
                            }else{
                                showModal("danger", "Hata", response.message);
                            }
                        }
                    });
                });
            });
        </script>
        <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
	</body>
</html>
