<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */
$buttonName="Galeri Ekle";

include_once MODEL."Admin/AdminGallery.php";
$galleryModel = new AdminGallery($db);

include_once MODEL."Admin/AdminImage.php";
$imageModel = new AdminImage($db);

$galleries = $galleryModel->getGalleryList();

if($galleries["status"] == "success"){

    $galleries = $galleries["data"];
}


?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Galeri Liste Pozitif Eticaret</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />

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
							<li class="active">Galeri Listesi</li>
						</ol>
					</div>
					<div class="section-body contain-lg">

                        <div class="row">
                            <div class="card col-md-8">
                                <div class="card-body ">
                                    <table id="galleryListTable" class="table no-margin">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ad</th>
                                                <th>Sira</th>
                                                <th>Düzenle / Sil</th>
                                                <th>Sırala</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if(!empty($galleries)){
                                                foreach($galleries as $gallery){
                                                    ?>
                                                    <tr>
                                                        <td><?=$gallery["galleryID"]?></td>
                                                        <td><?=$gallery["galleryName"]?></td>
                                                        <td class="galleryOrder"><?=$gallery["galleryOrder"]?></td>
                                                        <td><a href="/_y/s/s/galeriler/AddGallery.php?galleryID=<?=$gallery["galleryID"]?>" class="btn btn-sm btn-primary">Düzenle</a>
                                                            <button class="btn btn-sm btn-danger deleteGalleryButton" data-id="<?=$gallery["galleryID"]?>">Sil</button></td>
                                                        <td>
                                                            <a class="tile-content ink-reaction dragDropGallery" data-id="<?=$gallery["galleryID"]?>" style="cursor:grab"><div class="tile-icon"><i class="fa fa-arrows"></i></div></a>
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
				</section>
			</div>
			<?php require_once(ROOT."/_y/s/b/menu.php");?>
		</div>
        <div class="modal fade" id="deleteGalleryConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteGalleryConfirmModalLabel" aria-hidden="true">
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
                        <p id="alertMessage">Galeriyi silmek istediğinize emin misiniz?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-danger" id="deleteGalleryConfirmButton">Sil</button>
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
			$("#galleryListphp").addClass("active");

            function saveGalleryOrder(galleryID, galleryOrder) {
                return new Promise((resolve, reject) => {
                    const action = "saveGalleryOrder";
                    $.ajax({
                        url: '/App/Controller/Admin/AdminGalleryController.php',
                        type: 'POST',
                        data: {
                            galleryID: galleryID,
                            galleryOrder: galleryOrder,
                            action: action
                        },
                        success: function(response) {
                            console.log(response);
                            response = JSON.parse(response);
                            if (response.status === "success") {
                                console.log(response.message);
                                resolve(true);
                            } else {
                                console.error(response.message);
                                reject(false);
                            }
                        },
                        error: function(error) {
                            console.error(error);
                            reject(error);
                        }
                    });
                });
            }

            $('table#galleryListTable tbody').sortable({
                handle: '.dragDropGallery', // Sürükleme işlemi için kullanılacak eleman
                axis: 'y', // Y ekseni boyunca sıralama
                update: function (event, ui) {
                    // tbody altındaki tüm tr'leri 1 den başlayarak arttıralım ve .galleryOrder class'ına yazdıralım
                    $('table#galleryListTable tbody tr').each(function (index) {

                        let galleryID = $(this).find('.dragDropGallery').data('id');
                        let galleryOrder = index + 1;

                        if(saveGalleryOrder(galleryID, galleryOrder)){
                            $(this).find('.galleryOrder').text(galleryOrder);
                        }
                        else{
                            //alert message gösterip döngüyü durduralım
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text("Sıralama işlemi başarısız oldu");
                            return false;
                        }

                    });
                }
            });

            $(document).on('click', '.deleteGalleryButton', function() {
                const galleryID = $(this).data('id');
                $('#deleteGalleryConfirmButton').data('id', galleryID);
                $('#deleteGalleryConfirmModal').modal('show');
            });

            $('#deleteGalleryConfirmButton').click(function() {
                const galleryID = $(this).data('id');
                const action = "deleteGallery";
                $.ajax({
                    url: '/App/Controller/Admin/AdminGalleryController.php',
                    type: 'POST',
                    data: {
                        galleryID: galleryID,
                        action: action
                    },
                    success: function(response) {
                        const responseJson = JSON.parse(response);
                        if (responseJson.status === "success") {
                            $('#deleteGalleryConfirmModal').modal('hide');
                            $('#alertModal .card-head').removeClass('style-danger').addClass('style-success');
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text(responseJson.message);
                            //1,5 sanıye sonra sayfayı yenile
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            $('#deleteGalleryConfirmModal').modal('hide');
                            $('#alertModal .card-head').removeClass('style-success').addClass('style-danger');
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text(responseJson.message);
                            //1,5 sanıye sonra sayfayı yenile
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
		</script>
	</body>
</html>
