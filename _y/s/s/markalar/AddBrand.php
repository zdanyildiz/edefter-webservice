<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 */
$queryBrandID = $_GET["brandID"] ?? 0;
$brandID = intval($queryBrandID);

include_once MODEL . 'Admin/AdminBrand.php';
$brandModel = new AdminBrand($db);

if ($brandID > 0) {
    $brand = $brandModel->getBrand($brandID);
    if ($brand) {
        $brandName = $brand["data"]["markaad"];
        $brandDescription = $brand["data"]["markaaciklama"];
        $brandImage = $brand["data"]["marka_logo"];
        $buttonName = "Güncelle";
    } else {
        $brandName = "";
        $brandDescription = "";
        $brandImage = "";
        $buttonName = "Ekle";
    }
} else {
    $brandName = "";
    $brandDescription = "";
    $brandImage = "";
    $buttonName = "Ekle";
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Marka Ekle Pozitif Eticaret</title>

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

        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />

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
            <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
                    <div class="section-header">
                        <ol class="breadcrumb">
                            <li class="active">Marka Ekle / Güncelle</li>
                        </ol>
                    </div>

					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<form name="addBrandForm" id="addBrandForm" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" id="brandID" name="brandID" value="<?=$brandID?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="brandName" 
																id="brandName" 
																value="<?=$brandName?>"
																placeholder="Marka Adını Yazın" required aria-required="true" >
																<label for="brandName">Marka Adını Yazın</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-12">
															<div class="form-group ">
																<textarea 
																	name="brandDescription" 
																	id="brandDescription" 
																	class="form-control" 
																	rows="2"
																	placeholder
																	style="
																		background-color:#f6f6f6;
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=$brandDescription?></textarea>
																<label for="brandDescription">Marka Açıklama</label>
															</div>
														</div>
													</div>
                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-4"><h4>Marka Logo - Sürükle Bırak</h4></div>
                                                        <div class="col-lg-offset-1 col-md-8">
                                                            <div class="card">

                                                                <div class="btn-group" id="imageButtonContainer" data-toggle="buttons">

                                                                    <label class="btn  btn-primary-bright btn-md"
                                                                           href="#offcanvas-imageUpload"
                                                                           id="addImageByLeftCanvas"
                                                                           data-target="imageBox"
                                                                           data-uploadtarget="Brand"
                                                                           data-toggle="offcanvas">
                                                                        <i class="fa fa-plus fa-fw"></i>
                                                                        Resim Yükle
                                                                    </label>


                                                                    <label class="btn btn-default-light btn-md"
                                                                           href="#offcanvas-imageSearch"
                                                                           id="addImageByRightCanvas"
                                                                           data-target="imageBox" data-toggle="offcanvas">
                                                                        <i class="fa fa-file-image-o fa-fw"></i>
                                                                        Resim Seç
                                                                    </label>
                                                                </div>

                                                                <div class="card-body" id="imageContainer" data-sortable="true" >
                                                                    <?php
                                                                    if (!empty($brandImage)) {?>
                                                                            <div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_1">
                                                                                <input type="hidden" name="brandImage" value="<?=$brandImage?>">
                                                                                <div class="tile-icond">
                                                                                    <img id="image_1" class="size-2" src="<?=imgRoot."?imagePath=".$brandImage?>&width=100&height=100" alt="<?=$brandName?>">
                                                                                </div>
                                                                                <div class="tile-text">
                                                                                    <a
                                                                                        class="btn btn-floating-action ink-reaction removeImage"
                                                                                        data-imageBox="imageBox_1"
                                                                                        data-id="1"
                                                                                        data-toggle="modal"
                                                                                        data-target="#removeImageModal"
                                                                                        title="Kaldır">
                                                                                        <i class="fa fa-trash"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>

                                                                <div class="modal fade" id="removeAllImageModal" tabindex="-1" role="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="removeImageModalClose">×</button>
                                                                                <h4 class="modal-title" id="simpleModalLabel">Resmleri Kaldır</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Tüm Resimleri kaldırmak istediğinize emin misiniz?</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                                                                                <button type="button" class="btn btn-primary" id="removeAllImageButton" data-imagebox="0">Resmleri Kaldır</button>
                                                                            </div>
                                                                        </div><!-- /.modal-content -->
                                                                    </div><!-- /.modal-dialog -->
                                                                </div>
                                                                <div class="modal fade" id="removeImageModal" tabindex="-1" role="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="removeImageModalClose">×</button>
                                                                                <h4 class="modal-title" id="simpleModalLabel">Resmi Kaldır</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Resmi kaldırmak istediğinize emin misiniz?</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                                                                                <button type="button" class="btn btn-primary" id="removeImageButton" data-imagebox="0">Resmi Kaldır</button>
                                                                            </div>
                                                                        </div><!-- /.modal-content -->
                                                                    </div><!-- /.modal-dialog -->
                                                                </div>
                                                            </div>
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

			<?php require_once(ROOT."/_y/s/b/menu.php");?>

            <?php require_once(ROOT."/_y/s/b/rightCanvas.php");?>

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
			$("#addBrandphp").addClass("active");

            let imgRoot = "<?=imgRoot?>";

            $(document).on("submit", "#addBrandForm", function (e) {
                e.preventDefault();
                //brandName boş olamaz
                if ($("#brandName").val() == "") {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text("Marka adı boş olamaz");
                    $("#alertModal").modal("show");
                    return;
                }

                let brandID = $("#brandID").val();
                let action;
                if (brandID > 0) {
                    action = "updateBrand";
                } else {
                    action = "addBrand";
                }

                var form = $(this);
                var formData = form.serialize();
                formData += "&action=" + action;
                $.ajax({
                    url: "/App/Controller/Admin/AdminBrandController.php",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        console.log(response);
                        var data = JSON.parse(response);
                        if (data.status == "success") {
                            var message = data.message;

                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").text(message);
                            $("#alertModal").modal("show");

                            setTimeout(function () {
                                window.location.href = "/_y/s/s/markalar/BrandList.php";
                            }, 1500);

                        } else {

                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").text("Marka eklenirken bir hata oluştu");
                            $("#alertModal").modal("show");
                        }
                    }
                });
            });

            $(document).on('keyup', '#searchImageName', function () {
                $imageName = $(this).val();
                if ($imageName.length > 2) {
                    $.ajax({
                        type: 'GET',
                        url: "/App/Controller/Admin/AdminImageController.php?action=getImagesBySearch&searchText=" + $imageName,
                        dataType: 'json',
                        success: function (data) {
                            $data = data;
                            if ($data.status === "success") {
                                $html = "";
                                for ($i = 0; $i < $data.images.length; $i++) {
                                    $imageID = $data.images[$i].imageID;
                                    $imagePath = $data.images[$i].imagePath;
                                    $imageName = $data.images[$i].imageName;
                                    $imageWidth = $data.images[$i].imageWidth;
                                    $imageHeight = $data.images[$i].imageHeight;
                                    $imageFolderName = $data.images[$i].imageFolderName;

                                    $html += '<li class="tile">' +
                                        '<a class="tile-content ink-reaction selectImage"' +
                                        'data-imageid="' + $imageID + '"' +
                                        'data-imagepath="' + $imageFolderName + '/' + $imagePath + '"' +
                                        'data-imagename="' + $imageName + '"' +
                                        'data-imagewidth="' + $imageWidth + '"' +
                                        'data-imageheight="' + $imageHeight + '"' +
                                        'data-backdrop="false" style="cursor:pointer;">' +
                                        '<div class="tile-icon">' +
                                        '<img src="' + imgRoot + '?imagePath=' + $imageFolderName + '/' + $imagePath + '&width=100&height=100" alt="" />' +
                                        '</div>' +
                                        '<div class="tile-text">' +
                                        $imageName +
                                        '<small>' + $imageFolderName + '</small>' +
                                        '</div>' +
                                        '</a>' +
                                        '</li>';

                                }
                                $("#rightImageListContainer").html($html);
                            }
                        }
                    });
                }
            });

            $(document).on("click", "#selectImageByRightCanvas, #addImageByRightCanvas", function () {
                $imageTarget = $(this).data("target");

                $("#imageTarget").val($imageTarget);
            });

            $(document).on("click", "#uploadImageByLeftCanvas, #addImageByLeftCanvas", function () {
                $imageTarget = $(this).data("target");

                $("#imageTarget").val($imageTarget);

                $uploadTarget = $(this).data("uploadtarget");

                $("#imageFolder").val($uploadTarget);
            });

            $(document).on("click", ".selectImage", function () {

                $imageTarget = $("#imageTarget").val();

                $imageID = $(this).data("imageid");
                $imagePath = $(this).data("imagepath");
                $imageName = $(this).data("imagename");
                $imageWidth = $(this).data("imagewidth");
                $imageHeight = $(this).data("imageheight");

                if ($imageTarget === "pageContent") {

                    //genişliğe göre yükseklik ayarlayalım
                    $imageNewWidth = 300;
                    $imageNewHeight = Math.round($imageHeight / $imageWidth * $imageNewWidth);

                    // Summernote'taki mevcut içeriği alın
                    let summernote = $("#pageContent").summernote();
                    let editorData = summernote.code();
                    console.log(editorData);

                    // Yeni resim HTML'sini oluşturun
                    let newImageHtml = '<img src="' + imgRoot + '?imagePath=' + $imagePath + '&width=' + $imageNewWidth + '&height=' + $imageNewHeight + '" title="' + $imageName + '" width="' + $imageNewWidth + '" height="' + $imageNewHeight + '" >';

                    // Mevcut içeriğe yeni resmi ekleyin
                    summernote.code( editorData + newImageHtml);
                    console.log(newImageHtml);
                } else {

                    $html = $imageBox;
                    $html = $html.replaceAll("[imageID]", $imageID);
                    $html = $html.replaceAll("[imagePath]", $imagePath);
                    $html = $html.replaceAll("[imageName]", $imageName);

                    $("#imageContainer").append($html);
                }
            });

            Dropzone.options.imageDropzone = {
                parallelUploads: 1,
                autoProcessQueue: true,
                addRemoveLinks: true,
                maxFiles: 1,
                maxFilesize: 2,
                dictDefaultMessage: "Resimleri yüklemek için bırakın",
                dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
                dictFallbackText: "Resimleri eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın.",
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

                        //console.log(responseText);

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


                                $html = $imageBox;
                                $html = $html.replaceAll("[imageID]", $imageID);
                                $html = $html.replaceAll("[imagePath]", $imagePath);
                                $html = $html.replaceAll("[imageName]", $imageName);

                                $("#imageContainer").html("");

                                $("#imageContainer").append($html);

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

            $(document).on("click", ".removeImage", function () {
                var targetImageBox = $(this).data("imagebox");
                console.log("remove target: " + targetImageBox);

                // removeImageButton tıklanınca targetImageBox'ı silelim
                $(document).on('click', '#removeImageButton', function () {
                    console.log("remove: " + targetImageBox);
                    $("#" + targetImageBox).remove();
                    $("#removeImageModal").modal("hide");
                });
            });

            const $imageBox = '<div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_[imageID]">' +
                '<input type="hidden" name="brandImage" value="[imagePath]">' +
                '<div class="tile-icond">' +
                '<img id="image_[imageID]" class="size-2" src="' + imgRoot + '?imagePath=[imagePath]&width=100&height=100" alt="[imageName]">' +
                '</div>' +
                '<div class="tile-text">' +
                '<a class="btn btn-floating-action ink-reaction removeImage" data-imageBox="imageBox_[imageID]" data-id="[imageID]" data-toggle="modal" data-target="#removeImageModal" title="Kaldır">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</div>' +
                '</div>';

            const urlParams = new URLSearchParams(window.location.search);
            const refAction = urlParams.get('refAction');
            if(refAction){
                modalMessage="Ürün eklemek için önce Marka eklemelisiniz";
                $("#alertModal .modal-header").removeClass("bg-success").addClass("bg-danger");
                $("#alertModal #alertMessage").html(modalMessage);
                $("#alertModal").modal("show");
            }
		</script>

	</body>
</html>