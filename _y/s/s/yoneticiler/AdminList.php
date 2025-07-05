<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Helper $helper
 * @var int $adminID
 * @var int $adminAuth
 */


include_once MODEL . 'Admin/Admin.php';
$adminModel = new Admin($db);

$admins = $adminModel->getAdmins();
//adminID,adminNameSurname,adminEmail,adminAuth,adminActive
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Material Admin - Compose mail</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
              type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

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
							<li class="active">Yönetici Liste</li>
						</ol>
					</div>	
					<div class="section-body">
						<div class="card">

							<div class="card-body">
								<table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ad Soyad</th>
                                            <th>E-Posta</th>
                                            <th>Yetki</th>
                                            <th>Aktif</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($admins as $admin) {
                                            if($adminAuth <=$admin["adminAuth"]):
                                            ?>
                                            <tr>
                                                <td><?php echo $admin["adminID"]; ?></td>
                                                <td><?php echo $helper->decrypt($admin["adminNameSurname"],$config->key); ?></td>
                                                <td><?php echo $helper->decrypt($admin["adminEmail"],$config->key); ?></td>
                                                <td><?php echo ($admin["adminAuth"]) == 0 ? "Süper Yönetici" : ($admin["adminAuth"] == 1 ? "Yönetici" : "Kullanıcı"); ?></td>
                                                <td><?php echo $admin["adminActive"] ? "Aktif" : "Pasif"; ?></td>
                                                <td>
                                                    <a href="/_y/s/s/yoneticiler/AddAdmin.php?adminID=<?php echo $admin["adminID"]; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                                                    <?php if($adminID!=$admin["adminID"]): ?>
                                                    <a href="#" class="btn btn-sm btn-danger" onclick="deleteAdmin(<?php echo $admin["adminID"]; ?>)">Sil</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php
                                        endif;
                                        }
                                        ?>
                                </table>
							</div>
						</div>
					</div>
				</section>
			</div>
			<div 
				class="modal fade" 
				id="simpleModal" 
				tabindex="-1" 
				role="dialog" 
				aria-labelledby="simpleModalLabel" 
				aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" id="btn-popup-sil-kapat" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="simpleModalLabel">Dikkat!</h4>
						</div>
						<div class="modal-body">
							<p>Yöneticiyi SİLMEK istediğinize emin misiniz?</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">İptal Et</button>
							<button type="button" class="btn btn-primary" id="silbutton">SİL</button>
						</div>
					</div>
				</div>
			</div>
			<?php require_once(ROOT."/_y/s/b/menu.php");?>

            <div class="modal fade" id="deleteAdminConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteAdminConfirmModalLabel" aria-hidden="true">
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
                            <p>Yöneticiyi silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                            <button type="button" class="btn btn-danger" id="deleteAdminConfirmButton">Sil</button>
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

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
		

		<script>
			$("#adminListphp").addClass("active");

            function deleteAdmin(adminID) {
                $("#deleteAdminConfirmModal").modal("show");
                $("#deleteAdminConfirmButton").attr("onclick", "deleteAdminConfirm(" + adminID + ")");
            }

            function deleteAdminConfirm(adminID) {
                $.ajax({
                    url: "/App/Controller/Admin/AdminController.php",
                    type: "POST",
                    data: {
                        adminID: adminID,
                        action: "deleteAdmin"
                    },
                    success: function (response) {
                        jsonResponse = JSON.parse(response);
                        $("#deleteAdminConfirmModal").modal("hide");
                        if (jsonResponse.status == "success") {
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").text("Yönetici başarıyla silindi.");
                            $("#alertModal").modal("show");
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").text("Yönetici silinirken bir hata oluştu.");
                            $("#alertModal").modal("show");
                        }
                    }
                });
            }
		</script>
	</body>
</html>
