<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var int $adminAuth
 */
include_once MODEL . 'Admin/AdminHomePage.php';
$adminHomePage = new AdminHomePage($db);

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

$languages = $adminLanguage->getLanguages();

$languageCode = $adminLanguage->getLanguageCode($languageID);
$blocks = $adminHomePage->getBlocks($languageCode);

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Anasayfa Düzenle Pozitif Eticaret</title>
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
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/nestable/nestable.css?1423393667" />
        
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		<div id="base">
            <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Ana Sayfa Yönetimi</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
                        <form id="homePageForm" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <select name="languageID" id="languageID" class="form-control">
                                                    <option value="0">Dil Seçin</option>
                                                    <?php foreach($languages as $language){
                                                        $selected = $language['languageID'] == $languageID ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <p class="help-block">ANA SAYFA DÜZENLEME İÇİN DİL SEÇİN!</p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-head">
                                    <header>Bloklar</header>
                                    <p class="help-block">Blokları sürekle bırak yöntemiyle sıralayabilirsiniz</p>
                                </div>
                                <div class="card-body" id="blockContainer" data-sortable="true">
                                    <?php if(count($blocks)>0):?>
                                    <?php foreach ($blocks as $block):?>
                                        <div class="block-item border-black margin-bottom-lg" data-block-id="<?= $block['id'] ?>" style="padding: 10px">
                                            <h4><?= $block['type'] ?></h4>
                                            <p><?= json_decode($block['content'])->title ?? "Başlık Yok" ?></p>
                                            <?php if (!empty(json_decode($block['content'])->link)): ?>
                                                <a href="<?= json_decode($block['content'])->link ?>" target="_blank" class="btn btn-warning">Düzenle</a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" disabled>Düzenleme Yok</button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-danger blockStatusButton" data-id="<?=$block['id']?>"><?=$block['is_active'] ? 'Pasif Yap' : 'Aktif Yap'?></button>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php else:?>
                                        <button id="initializeDefaults" class="btn btn-info">Varsayılan Blokları Yükle</button>
                                    <?php endif;?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Kaydet</button>
                        </form>
					</div>
				</section>
			</div>

			<?php require_once(ROOT."/_y/s/b/menu.php");?>

            <div class="modal fade" id="editBlockModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Blok Düzenle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editBlockForm">
                                <input type="hidden" name="blockID" id="blockID">
                                <div class="form-group">
                                    <label for="blockTitle">Başlık</label>
                                    <input type="text" class="form-control" name="blockTitle" id="blockTitle" required>
                                </div>
                                <div class="form-group">
                                    <label for="blockContent">İçerik</label>
                                    <textarea class="form-control" name="blockContent" id="blockContent" rows="3" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="saveBlockChanges">Kaydet</button>
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
        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/nestable/jquery.nestable.js"></script>
        <script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
		<!-- END JAVASCRIPT -->
		<script>
			$("#homePageDesignphp").addClass("active");

            $(document).ready(function () {
                // Blok sıralama için sortable
                $("#blockContainer").sortable({
                    update: function () {
                        let order = [];
                        $(".block-item").each(function () {
                            order.push($(this).data("block-id"));
                        });

                        // AJAX ile sıralama bilgisi gönderme
                        $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                            action: 'reorderBlocks',
                            blockOrder: order
                        }, function (response) {
                            alert(response.message);
                        }, 'json');
                    }
                });

                // Yeni blok ekleme
                $("#addBlockButton").click(function () {
                    // Yeni blok ekleme modalı açılır
                });

                $("#saveBlockChanges").click(function () {
                    const blockID = $("#blockID").val();
                    const blockTitle = $("#blockTitle").val();
                    const blockContent = $("#blockContent").val();

                    // AJAX ile düzenlenen verileri gönder
                    $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                        action: 'updateBlock',
                        blockID: blockID,
                        content: JSON.stringify({ title: blockTitle, description: blockContent })
                    }, function (response) {
                        alert(response.message);
                        if (response.status === "success") {
                            location.reload();
                        }
                    }, 'json');
                });

                // Blok düzenleme
                $(document).on("click", ".editBlockButton", function () {
                    const blockID = $(this).data("id");

                    // AJAX ile mevcut blok bilgilerini al
                    $.post('/App/Controller/Admin/AdminHomePageController.php', {
                        action: 'getBlock',
                        blockID: blockID
                    }, function (response) {
                        if (response.status === "success") {
                            // Form alanlarını doldur
                            $("#blockID").val(response.data.id);
                            $("#blockTitle").val(response.data.content.title);
                            $("#blockContent").val(response.data.content.description);
                            $("#editBlockModal").modal("show");
                        } else {
                            alert(response.message);
                        }
                    }, 'json');
                });

                // Blok silme
                $(".deleteBlockButton").click(function () {
                    let blockId = $(this).closest('.block-item').data('block-id');
                    if (confirm("Bu bloğu silmek istediğinize emin misiniz?")) {
                        $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                            action: 'deleteBlock',
                            blockID: blockId
                        }, function (response) {
                            alert(response.message);
                            location.reload();
                        }, 'json');
                    }
                });

                $("#initializeDefaults").click(function(e) {
                    e.preventDefault();
                    //seçili dilin languagecode data idsini al
                    let languageCode = $("#languageID option:selected").data('languagecode');
                    if (!languageCode) {
                        $("#alertMessage").text("Lütfen bir dil seçin.");
                        $("#alertModal").modal('show');
                        return;
                    }
                    $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                        action: 'initializeDefaults',
                        language: languageCode
                    }, function(response) {
                        console.log(response);
                        alert(response.message);
                        location.reload();
                    }, 'json');
                });
            });
        </script>
	</body>
</html>