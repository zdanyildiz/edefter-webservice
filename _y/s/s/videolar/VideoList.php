<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */
/**
 * Columns:
 * video_id int AI PK
 * created_at timestamp
 * updated_at timestamp
 * video_name varchar(100)
 * video_file varchar(255)
 * video_extension varchar(4)
 * video_size varchar(12)
 * video_width int
 * video_height int
 * unique_id char(20)
 * video_iframe text
 * description text
 * is_deleted tinyint(1)
 */

include_once MODEL."Admin/AdminVideo.php";
$videoModel = new AdminVideo($db);

$videos = $videoModel->getVideos();
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Video Liste Pozitif Eticaret</title>
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
							<li class="active">Video Listesi</li>
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
                                                <th>Tür</th>
                                                <th>Düzenle / Sil</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($videos as $video) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $video['video_id']; ?></td>
                                                    <td><?php echo $video['video_name']; ?></td>
                                                    <td><?php echo $video['video_extension']; ?></td>
                                                    <td>
                                                        <a href="/_y/s/s/videolar/AddVideo.php?videoID=<?php echo $video['video_id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                                                        <button class="btn btn-sm btn-danger deleteVideoButton" data-id="<?php echo $video['video_id']; ?>">Sil</button>
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
				</section>
			</div>
			<?php require_once(ROOT."/_y/s/b/menu.php");?>
		</div>
        <div class="modal fade" id="deleteVideoConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteVideoConfirmModalLabel" aria-hidden="true">
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
                        <p id="alertMessage">Video silmek istediğinize emin misiniz?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-danger" id="deleteVideoConfirmButton">Sil</button>
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
			$("#videoListphp").addClass("active");

            $(document).on('click', '.deleteVideoButton', function() {
                const videoID = $(this).data('id');
                $('#deleteVideoConfirmButton').data('id', videoID);
                $('#deleteVideoConfirmModal').modal('show');
            });

            $('#deleteVideoConfirmButton').click(function() {
                const videoID = $(this).data('id');
                const action = "deleteVideo";
                $.ajax({
                    url: '/App/Controller/Admin/AdminVideoController.php',
                    type: 'POST',
                    data: {
                        videoID: videoID,
                        action: action
                    },
                    success: function(response) {
                        const responseJson = JSON.parse(response);
                        if (responseJson.status === "success") {
                            $('#deleteVideoConfirmModal').modal('hide');
                            $('#alertModal .card-head').removeClass('style-danger').addClass('style-success');
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text(responseJson.message);
                            //1,5 sanıye sonra sayfayı yenile
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            $('#deleteVideoConfirmModal').modal('hide');
                            $('#alertModal .card-head').removeClass('style-success').addClass('style-danger');
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text(responseJson.message);
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
