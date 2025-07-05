<?php  require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL ."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();


include_once MODEL . 'Admin/AdminTagManager.php';
$tagManagerModel = new AdminTagManager($db);

$tagManager = $tagManagerModel->getTagManager($languageID);
if(!empty($tagManager)){
    $tagManagerID = $tagManager[0]['tag_manager_id'];
    $tagManagerName = $tagManager[0]['tag_manager_name'];
    $tagManagerHead = $tagManager[0]['tag_manager_head'];
    $tagManagerContent = $tagManager[0]['tag_manager_content'];
}
$tagManagerID = $tagManagerID ?? 0;
$tagManagerName = $tagManagerName ?? '';
$tagManagerHead = $tagManagerHead ?? '';
$tagManagerContent = $tagManagerContent ?? '';
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Tag Manager Kodu Ekle Pozitif Eticaret</title>
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
							<li class="active">Etiket Yöneticisi Kodu</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<form name="tagManagerForm"  id="tagManagerForm" class="form form-validation form-validate" role="form" method="post">
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">
														<div class="col-sm-6">
															<div class="form-group">
																<select id="languageID" name="languageID" class="form-control">
																<?php
																	foreach($languages as $lang){
                                                                        $selected = $lang['languageID'] == $languageID ? 'selected' : '';
                                                                        echo '<option value="'.$lang['languageID'].'" '.$selected.'>'.$lang['languageName'].'</option>';
                                                                    }
																?>
																</select>
																<p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
															</div>
														</div>
														<div class="col-sm-3" style="margin-top:15px">
															<input 
																type="text" 
																class="form-control" 
																name="tagManagerName"
																id="tagManagerName"
																value="<?=$tagManagerName?>"
																placeholder="Örn: Google-Facebook" >
															<label for="tagManagerName">Etiket Yönetici Adını Yazın</label>
														</div>
													</div>
                                                    <div class="row">
                                                        <div class="form-group floating-label">
															<textarea
                                                                    name="tagManagerHead"
                                                                    id="tagManagerHead"
                                                                    class="form-control"
                                                                    rows="7"
                                                                    placeholder="Etiket Yöneticisi Head kısmını yapıştırın"
                                                                    style="
																	background-color:#efefef;
																	width:96%;
																	padding: 10px 1% 10px 1%;
																	margin:10px 0 0 0;
																	border:solid 1px #eee"
                                                            ><?=trim($tagManagerHead)?></textarea>
                                                            <label for="tagManagerHead" style="margin-top:-20px">Etiket Yöneticisi Head Kodu</label>
                                                        </div>
                                                    </div>
													<div class="row">
														<div class="form-group floating-label">
															<textarea 
																name="tagManagerContent"
																id="tagManagerContent"
																class="form-control" 
																rows="7" 
																placeholder="Etiket Yöneticisi Body kısmını yapıştırın"
																style="
																	background-color:#efefef; 
																	width:96%; 
																	padding: 10px 1% 10px 1%; 
																	margin:10px 0 0 0; 
																	border:solid 1px #eee" 
																><?=trim($tagManagerContent)?></textarea>
															<label for="tagManagerContent" style="margin-top:-20px">Etiket Yöneticisi Body Kodu</label>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<button id="saveButton" type="submit" class="btn btn-primary btn-default">Kaydet</button>
											</div>
										</div>
									</form>
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
        <script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

		<script>
			$("#addTagManagerphp").addClass("active");

            //languageID değiştiğinde
            $("#languageID").change(function(){
                var languageID = $(this).val();
                window.location.href = "/_y/s/s/ekkodlar/AddTagManager.php?languageID="+languageID;
            });

            //Form submit
            $("#tagManagerForm").submit(function(e){
                e.preventDefault();
                var tagManagerName = $("#tagManagerName").val();
                var tagManagerHead = $("#tagManagerHead").val();
                var tagManagerContent = $("#tagManagerContent").val();
                var languageID = $("#languageID").val();
                if(tagManagerName == ""){
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").html("Etiket Yöneticisi Adını Girin");
                    $("#alertModal").modal("show");
                    return false;
                }
                if(tagManagerHead == ""){
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").html("Etiket Yöneticisi Head Bölümünü Girin");
                    $("#alertModal").modal("show");
                    return false;
                }
                if(tagManagerContent == ""){
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").html("Etiket Yöneticisi Body Bölümünü Girin");
                    $("#alertModal").modal("show");
                    return false;
                }
                $.ajax({
                    url: "/App/Controller/Admin/AdminPluginsController.php",
                    type: "POST",
                    data: {
                        action: "saveTagManager",
                        tagManagerID: <?=$tagManagerID?>,
                        tagManagerName: tagManagerName,
                        tagManagerHead: tagManagerHead,
                        tagManagerContent: tagManagerContent,
                        languageID: languageID
                    },
                    success: function(response){
                        console.log(response);
                        var data = JSON.parse(response);
                        if(data.status == "success"){
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").html("Kaydedildi");
                            $("#alertModal").modal("show");

                            setTimeout(function(){
                                $("#alertModal").modal("hide");
                            },1000);
                        }
                        else{
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").html("Kaydedilemedi");
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>