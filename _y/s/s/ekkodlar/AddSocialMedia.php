<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$buttonName = "Güncelle";

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/AdminSocialMedia.php';
$socialMediaModel = new AdminSocialMedia($db);

$socialMedia = $socialMediaModel->getSocialMedia($languageID);
if(!empty($socialMedia)) {
    $facebook = $socialMedia["facebook"];
    $twitter = $socialMedia["twitter"];
    $gplus = $socialMedia["googleplus"];
    $instagram = $socialMedia["instagram"];
    $linkedin = $socialMedia["linkedin"];
    $youtube = $socialMedia["youtube"];
    $skype = $socialMedia["skype"];
    $blog = $socialMedia["blog"];
    $socialMediaUniqID = $socialMedia["benzersizid"];
}

$facebook = $facebook ?? "";
$twitter = $twitter ?? "";
$gplus = $gplus ?? "";
$instagram = $instagram ?? "";
$linkedin = $linkedin ?? "";
$youtube = $youtube ?? "";
$skype = $skype ?? "";
$blog = $blog ?? "";
$socialMediaUniqID = $socialMediaUniqID ?? "";

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sosyal Medya Hesabı Ekle / Düzenle Pozitif Eticaret</title>

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
							<li class="active">SosyalMedya Hesaplarınız</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">

                            <div class="col-lg-3 col-md-4">
                                <article class="margin-bottom-xxl">
                                    <h4>Sosyal Medya Adreslerinizi girin</h4><p></p>
                                    <p>
                                        Ziyaretçilerinize sosyal medya hesaplarınızı göstermek için sosyal medya adreslerinizi girin.
                                    </p>
                                    <p> Eğer farklı ülkeler için farklı sosyal medya hesaplarınız varsa, dil seçimi yaparak ziyaretçilerinize diline uygun sosyal medya hesaplarınızı gösterebilirsiniz. </p>
                                </article>
                            </div>
                            <div class="col-lg-offset-1 col-md-8">
								<div class="card">
									<form name="addSocialMediaForm" class="form" role="form" method="post">
                                        <input type="hidden" name="socialMediaUniqID" value="<?=$socialMediaUniqID?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">
														<div class="form-group">
															<select id="languageID" name="languageID" class="form-control">
															<?php
															foreach ($languages as $lang) {
                                                                $selected = $lang["languageID"] == $languageID ? "selected" : "";
                                                                echo "<option value='{$lang["languageID"]}' $selected>{$lang["languageName"]}</option>";
                                                            }
															?>
															</select>
															<p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
														</div>
													</div>
													<div class="row">
														<div class="form-group floating-label">
															<textarea name="facebook" id="facebook" class="form-control" rows="2" ><?=$facebook?></textarea>
															<label for="facebook">Facebook</label>
														</div>
													</div>
													<div class="row">
														<div class="form-group floating-label">
															<textarea name="twitter" id="twitter" class="form-control" rows="2" ><?=$twitter?></textarea>
															<label for="twitter">Twitter</label>
														</div>
													</div>

													<div class="row">
														<div class="form-group floating-label">
															<textarea name="instagram" id="instagram" class="form-control" rows="2" ><?=$instagram?></textarea>
															<label for="instagram">Instagram</label>
														</div>
													</div><!--end .row -->
													<div class="row">
														<div class="form-group floating-label">
															<textarea name="linkedin" id="linkedin" class="form-control" rows="2"><?=$linkedin?></textarea>
															<label for="linkedin">LinkedIn</label>
														</div>
													</div><!--end .row -->
													<div class="row">
														<div class="form-group floating-label">
															<textarea name="youtube" id="youtube" class="form-control" rows="2"><?=$youtube?></textarea>
															<label for="linkedin">Youtube</label>
														</div>
													</div>

													<div class="row">
														<div class="form-group floating-label">
															<textarea name="blog" id="blog" class="form-control" rows="2" ><?=$blog?></textarea>
															<label for="blog">Blog</label>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<button type="submit" class="btn btn-primary btn-default"><?=$buttonName?></button>
											</div>
										</div>
										
									</form>
								</div>
							</div>

						</div>
					</div>
				</section>
			</div>
			<!-- END CONTENT -->
			<!-- BEGIN MENUBAR-->
			<?php require_once(ROOT."/_y/s/b/menu.php");?>
			<!-- END MENUBAR -->

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

        <style>
            textarea.form-control{
                background-color: #f6f6f6;
                width: 96%;
                padding: 10px 1% 10px 1%;
                margin: 10px 0 0 0;
                border: solid 1px #eee;
            }
        </style>

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
            $("#addSocialMediaphp").addClass("active");

            //form bilgilerini /App/Controller/Admin/AdminSocialMediaController.php dosyasına gönder
            $("form[name='addSocialMediaForm']").submit(function(e){
                e.preventDefault();
                var form = $(this);
                var url = "/App/Controller/Admin/AdminSocialMediaController.php";
                var data = form.serialize();
                var action = "addSocialMedia"
                data += "&action=" + action;
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(data)
                    {
                        var response = JSON.parse(data);
                        if(response.status == "success"){
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success")
                            $("#alertMessage").html(response.message);
                            $("#alertModal").modal("show");
                            //1 saniye sonra kapat
                            setTimeout(function(){
                                $("#alertModal").modal("hide");
                            }, 1000);
                        }else{
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger")
                            $("#alertMessage").html(response.message);
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>
