<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$baseLogo = "/Public/Image/Theme/logo.png";
$languageID = $_GET["languageID"] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/AdminCompany.php';
$companyModel = new AdminCompany($db);

$logo = $companyModel->getCompanyLogo($languageID);

if(!empty($logo)){
    $imageID = $logo["imageID"];
    $imagePath = $logo["imagePath"];
    $logoText = $logo["logoText"];
}

$imageID = $imageID ?? 0;
$imagePath = $imagePath ?? "";
$logoText = $logoText ?? "";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Logo Ekle Pozitif Eticaret</title>
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

        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.min.css"/>

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
			<?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Logo Ekle / Düzenle</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<form id="addLogoForm" class="form form-validation form-validate" method="post" novalidate="novalidate">
							<input type="hidden" name="imageID" id="imageID" value="<?=$imageID?>">
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Logo Dil</h4><p></p>
										<p>
											Ekleyeceğiniz Logo için Dil Seçin
										</p>
									</article>
								</div>
								<div class="col-lg-offset-1 col-md-8">
									<div class="form-group">
										<select id="languageID" name="languageID" class="form-control">
										<?php
										foreach ($languages as $language) {
                                            $selected = $languageID == $language["languageID"] ? "selected" : "";
                                            echo "<option value='{$language["languageID"]}' $selected>{$language["languageName"]}</option>";
                                        }
										?>
										</select>
										<p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
									</div>
								</div>
							</div>
						
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Logo Slogan</h4>					
										<p>Marka adı ya da alan adınız olabilir</p>
										<p></p>
										<h4>Logo Görseli</h4>
										<p></p>
                                        <a
                                                class="btn btn-primary-bright"
                                                href="#offcanvas-imageUpload"
                                                id="addLogoByLeftCanvas"
                                                data-target="logoContainer"
                                                data-uploadtarget="Logo"
                                                data-toggle="offcanvas"
                                                title="Logo Yükle">
                                            Logo Yükle
                                        </a>
									</article>
								</div>
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div class="col-md-12">
												<div class="form-group">
													<input type="text" name="logoText" id="logoText" class="form-control" placeholder="Logo Yazi" data-rule-minlength="3" maxlength="50" aria-invalid="false" required="" aria-required="true" value="<?=$logoText?>">
													<label for="logoText">Logo Yazı</label>
												</div>
											</div>
											
											<div class="col-md-12">
												<div class="form-group">
													<label for="logoresim">Logo Resim</label>
												</div>
												<div class="form-group">
													<img src="<?=!(empty($imagePath)) ? imgRoot.$imagePath : $baseLogo?>" id="imgPath" style="max-width:100%">
												</div>
											</div>
											
										</div>
									</div>
									<em class="text-caption">Logo</em>
								</div>
							</div>
							
							<div class="card-actionbar">
								<div class="card-actionbar-row">
									<button type="submit" class="btn btn-primary btn-default">Kaydet</button>
								</div>
							</div>
						</form>
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
        <script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

		<script>
			$("#addLogophp").addClass("active");

            let imgRoot = "<?=imgRoot?>";

            $(document).on("click", "#addLogoByLeftCanvas", function(){
                $languageName = $("#languageID option:selected").text();
                $("#imageName").val($languageName+ " Logo");

                $imageTarget = $(this).data("target");
                $("#imageTarget").val($imageTarget);

                $uploadTarget = $(this).data("uploadtarget");
                $("#imageFolder").val($uploadTarget);
            });

            Dropzone.options.imageDropzone = {
                parallelUploads: 1,
                autoProcessQueue: true,
                addRemoveLinks: true,
                maxFiles: 1,
                maxFilesize: 3,
                dictDefaultMessage: "Resimleri yüklemek için bırakın",
                dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
                dictFallbackText: "Resimleri eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
                dictFileTooBig: "Resim çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
                dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
                dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
                dictCancelUpload: "İptal Et",
                dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
                dictRemoveFile: "Resim Sil",
                dictRemoveFileConfirmation: null,
                dictMaxFilesExceeded: "Daha fazla resim yükleyemezsiniz.",
                acceptedFiles: ".jpeg,.jpg,.png,.webp",
                //resimler adı imageName inputu boşsa yükleme yapmayalım
                accept: function (file, done) {

                    var imageName = $("#imageName").val();

                    if (imageName === "") {

                        $("#runImageDropzoneContainer").removeClass("hidden");
                        $("#imageName").parent().addClass("bg-danger");

                    } else {

                        $("#formImageName").val(imageName);
                        done();
                    }

                    $("#runImageDropzone").on("click", function (e) {

                        var imageName = $("#imageName").val();
                        if (imageName === "") {

                            $("#imageName").focus();

                        } else {

                            $("#formImageName").val(imageName);

                            done();
                        }
                    });


                },
                removedfile: function (file) {
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                init: function () {

                    this.on("success", function (file, responseText) {

                        console.log(responseText);

                        var responseObject = JSON.parse(responseText);

                        $status = responseObject.status;
                        //console.log("status:"+$status);

                        if ($status === "success") {
                            //resim bilgileri imageResults içinde dönüyor, birden fazla olabilir
                            $imageResults = responseObject.imageResults;
                            //console.log($imageResults);

                            $imageTarget = $("#imageTarget").val();

                            for ($i = 0; $i < $imageResults.length; $i++) {
                                $imageID = $imageResults[$i].imageData.imageID;
                                $imagePath = $imageResults[$i].imageData.imageFolderName + "/" + $imageResults[$i].imageData.imagePath;
                                $imageName = $imageResults[$i].imageData.imageName;
                                $imageWidth = $imageResults[$i].imageData.imageWidth;
                                $imageHeight = $imageResults[$i].imageData.imageHeight;

                                $("#imgPath").attr("src", imgRoot + $imagePath );
                                $("#imageID").val($imageID);
                            }

                            //dropzone'a eklenen resimleri silelim
                            this.removeAllFiles();
                            //offcanvas kapat
                            $("#offcanvas-imageUploadOff").click();
                        } else {
                            //hata mesajını burada işleyebilirsiniz
                            console.log(responseText);
                        }

                    });
                    this.on("error", function (file, responseText) {
                        // Hata mesajını burada işleyebilirsiniz
                        console.log(responseText);
                    });
                }
            };

            $("#addLogoForm").submit(function(e){
                e.preventDefault();
                var formData = $("#addLogoForm").serializeArray();
                var action = "addLogo";
                var languageID = $("#languageID").val();
                var logoText = $("#logoText").val();
                var imageID = $("#imageID").val();
                //hiçbiri boş olamaz

                formData.push({name: "action", value: action});

                //form data konsolda görelim
                console.log(formData);

                $.ajax({
                    type: "POST",
                    url: "/App/Controller/Admin/AdminCompanyController.php",
                    data: formData,
                    success: function(data){
                        console.log(data);
                        var response = JSON.parse(data);
                        if(response.status === "success"){
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").html("Logo başarıyla eklendi");
                            $("#alertModal").modal("show");

                            setTimeout(function(){
                                window.location.href = "/_y/s/s/tasarim/AddLogo.php";
                            }, 1500);

                        }else{
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").html("Logo eklenirken bir hata oluştu");
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });

            $(document).on("change", "#languageID", function(){
                let languageID = $(this).val();
                window.location.href = "/_y/s/s/tasarim/AddLogo.php?languageID="+languageID;
            });
		</script>

	</body>
</html>