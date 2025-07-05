<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */
$buttonName="Galeri Ekle";

$galleryID = $_GET["galleryID"] ?? 0;
$galleryID = intval($galleryID);

include_once MODEL."Admin/AdminGallery.php";
$gallery = new AdminGallery($db);

include_once MODEL."Admin/AdminImage.php";
$image = new AdminImage($db);

if($galleryID > 0){
    $galleryData = $gallery->getGallery($galleryID);
    if(!empty($galleryData)){
        $galleryData = $galleryData[0];
        $galleryID = $galleryData["galleryID"];
        $galleryUniqID = $galleryData["galleryUniqID"];
        $galleryName = $galleryData["galleryName"];
        $galleryDescription = $galleryData["galleryDescription"];
        $galleryOrder = $galleryData["galleryOrder"];
        $galleryShowInCategory = $galleryData["galleryShowInCategory"];
        $galleryDeleted = $galleryData["galleryDeleted"];

        $galleryImageIDs = $gallery->getGalleryImages($galleryID);

        if($galleryImageIDs){
            $galleryImages = [];
            foreach($galleryImageIDs as $imageID){
                $imageData = $image->getImageByID($imageID["imageID"]);
                if(!empty($imageData)){
                    $galleryImages[] = $imageData[0];
                }
            }
        }

        $buttonName = "Galeri Düzenle";
    }
}

$galleryUniqID = $galleryUniqID ?? "";
$galleryName = $galleryName ?? "";
$galleryDescription = $galleryDescription ?? "";
$galleryOrder = $galleryOrder ?? 0;
$galleryShowInCategory = $galleryShowInCategory ?? 0;
$galleryDeleted = $galleryDeleted ?? 0;
$galleryOrdering = $galleryOrdering ?? 0;
$galleryImages = $galleryImages ?? [];
?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>Galeri Ekle Pozitif Eticaret</title>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/multi-select/multi-select.css?1424887857" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />

        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-tagsinput/bootstrap-tagsinput.css?1424887862" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/typeahead/typeahead.css?1424887863" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />

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
							<li class="active">Resim Galerisi Ekle / Düzenle</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-12">
								<h1 class="text-primary">Sitenizin Resim Galeri Adını girin</h1>
							</div>
							<div class="col-lg-8">
								<article class="margin-bottom-xxl">
									<p class="lead">
										Örn: (Ana Galeri, Ürünler, Gezi).
									</p>
								</article>
							</div>
						</div>
						<form id="addGalleryForm" class="form" method="post">
							<input type="hidden" name="galleryID" 	id="galleryID" 	value="<?=$galleryID?>">
							<input type="hidden" name="galleryUniqID" 	id="galleryUniqID" 	value="<?=$galleryUniqID?>">
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Resim Galeri Temel Bilgiler</h4><p></p>
										<p>
											Galeri Adı Boş Olamaz
										</p>
										<br>
										<p>Galeri sayfasında görüntülenmek üzere bir açıklama ekleyebilirsiniz</p>
									</article>
								</div>
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div class="form-group">
												<input type="text" name="galleryName" id="galleryName" class="form-control" placeholder="Örn:Ürünler" value="<?=$galleryName?>" aria-invalid="false" required="" aria-required="true">
												<label>Resim Galeri Adı</label>
											</div>
											<div class="form-group">
												<textarea 
													id="galleryDescription" 
													name="galleryDescription" 
													placeholder="Galeri konusunu ve ya içeriğini yazabilirsiniz"
													class="form-control"  
													rows="5"
													><?=$galleryDescription?></textarea>
													<label for="galleryDescription">Galeri Açıklama</label>
											</div>
										</div>
									</div>
									<em class="text-caption">Galeri Temel özellikleri seçin</em>
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Galeri Resimleri</h4>										
										<p>Galeride görüntülenmek üzere bir resim seçin </p>
									</article>
								</div>

								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
                                        <div class="btn-group" id="imageButtonContainer" data-toggle="buttons">

                                            <label class="btn  btn-primary-bright btn-md"
                                                   href="#offcanvas-imageUpload"
                                                   id="addImageByLeftCanvas"
                                                   data-target="imageBox"
                                                   data-uploadtarget="Gallery"
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
                                            if(!empty($galleryImages))
                                            {

                                                foreach($galleryImages as $galleryImage)
                                                {
                                                    $imageID = $galleryImage["imageID"];
                                                    $imageName = $galleryImage["imageName"];
                                                    $imageUrl = $galleryImage["imageFolderName"]."/".$galleryImage["imagePath"];
                                                    if(empty($imageName) || empty($imageID) || empty($imageUrl)) continue;

                                                    ?>
                                                    <div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_<?=$imageID?>">
                                                        <input type="hidden" name="imageID[]" value="<?=$imageID?>">
                                                        <div class="tile-icond">
                                                            <img id="image_<?=$imageID?>" class="size-2" src="<?=imgRoot."?imagePath=".$imageUrl?>&width=100&height=100" alt="<?=$imageName?>">
                                                        </div>
                                                        <div class="tile-text">
                                                            <a
                                                                    class="btn btn-floating-action ink-reaction removeImage"
                                                                    data-imageBox="imageBox_<?=$imageID?>"
                                                                    data-id="<?=$imageID?>"
                                                                    data-toggle="modal"
                                                                    data-target="#removeImageModal"
                                                                    title="Kaldır">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
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
									<em class="text-caption">Görünüm Resimleri</em>
								</div>
							</div>
							<div class="card-actionbar">
								<div class="card-actionbar-row">
									<button type="submit" class="btn btn-primary btn-default"><?=$buttonName?></button>
								</div>
							</div>
						</form>
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

        <style>
            #imageContainer,#fileContainer{
                min-width: 100%;
                display: flex;
                flex-wrap: wrap;
                align-content: center; justify-content: flex-start;align-items: flex-start; gap: 10px;
            }
            .imageBox,.filebox {
                box-sizing: border-box;
                box-shadow: 0 0 0 1px #ccc;
                padding: 5px; min-width: 100px;
            }
            .imageBox img, .fileBox img {
                -webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
                -webkit-transition: -webkit-box-shadow 0.15s ease-out;
                -moz-transition: -moz-box-shadow 0.15s ease-out;
                -o-transition: -o-box-shadow 0.15s ease-out;
                transition: box-shadow 0.15s ease-out;
                margin-bottom: 5px;
            }
        </style>

        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
        <script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>


        <script src="/_y/assets/js/libs/ckeditor/build/ckeditor.js"></script>
        <script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
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
			$("#addGalleryphp").addClass("active");

            let imgRoot = "<?=imgRoot?>";

            $(document).ready(function() {
                //resim arama #imageName klavyeden 3 harf yazılırsa arama başlatalım
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

                //dosya arama #fileName klavyeden 3 harf yazılırsa arama başlatalım
                $(document).on('keyup', '#searchFileName', function () {
                    $fileName = $(this).val();
                    if ($fileName.length > 2) {
                        $.ajax({
                            type: 'GET',
                            url: "/App/Controller/Admin/AdminFileController.php?action=getFilesBySearch&searchText=" + $fileName,
                            dataType: 'json',
                            success: function (data) {
                                $data = data;
                                if ($data.status === "success") {
                                    $html = "";
                                    for ($i = 0; $i < $data.files.length; $i++) {
                                        $fileID = $data.files[$i].fileID;
                                        $filePath = $data.files[$i].filePath;
                                        $fileName = $data.files[$i].fileName;
                                        $fileExtension = $data.files[$i].fileExtension;
                                        $fileFolderName = $data.files[$i].fileFolderName;
                                        $fileImage = fileRoot + "?fileExtension=" + $fileExtension + ".png";

                                        $html += '<li class="tile">' +
                                            '<a class="tile-content ink-reaction selectFile"' +
                                            'data-fileid="' + $fileID + '"' +
                                            'data-filepath="' + $filePath + '"' +
                                            'data-filename="' + $fileName + '"' +
                                            'data-fileextension="' + $fileExtension + '"' +
                                            'data-backdrop="false" style="cursor:pointer;">' +
                                            '<div class="tile-icon">' +
                                            '<img src="' + $fileImage + '.png" alt="' + $fileName + '" />' +
                                            '</div>' +
                                            '<div class="tile-text">' +
                                            $fileName +
                                            '<small>' + $fileExtension + '</small>' +
                                            '</div>' +
                                            '</a>' +
                                            '</li>';

                                    }
                                    $("#rightFileListContainer").html($html);
                                }
                            }
                        });
                    }
                });

                //#selectImageByRightCanvas tıklandığında data-target değerini alıp #imageTarget'a atayalım
                $(document).on("click", "#selectImageByRightCanvas, #addImageByRightCanvas", function () {
                    $imageTarget = $(this).data("target");

                    $("#imageTarget").val($imageTarget);
                });

                //#uploadImageByLeftCanvas tıklandığında data-uploadtarget değerini alıp #imageFolder'a atayalım
                $(document).on("click", "#uploadImageByLeftCanvas, #addImageByLeftCanvas", function () {
                    $imageTarget = $(this).data("target");

                    $("#imageTarget").val($imageTarget);

                    $uploadTarget = $(this).data("uploadtarget");

                    $("#imageFolder").val($uploadTarget);
                });

                //imageBox
                const $imageBox = '<div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_[imageID]">' +
                    '<input type="hidden" name="imageID[]" value="[imageID]">' +
                    '<div class="tile-icond">' +
                    '<img id="image_[imageID]" class="size-2" src="' + imgRoot + '?imagePath=[imagePath]&width=100&height=100" alt="[imageName]">' +
                    '</div>' +
                    '<div class="tile-text">' +
                    '<a class="btn btn-floating-action ink-reaction removeImage" data-imageBox="imageBox_[imageID]" data-id="[imageID]" data-toggle="modal" data-target="#removeImageModal" title="Kaldır">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>' +
                    '</div>' +
                    '</div>';

                $(document).on("click", ".selectImage", function () {

                    $imageTarget = $("#imageTarget").val();

                    $imageID = $(this).data("imageid");
                    $imagePath = $(this).data("imagepath");
                    $imageName = $(this).data("imagename");
                    $imageWidth = $(this).data("imagewidth");
                    $imageHeight = $(this).data("imageheight");

                    $html = $imageBox;
                    $html = $html.replaceAll("[imageID]", $imageID);
                    $html = $html.replaceAll("[imagePath]", $imagePath);
                    $html = $html.replaceAll("[imageName]", $imageName);

                    $("#imageContainer").append($html);

                });

                Dropzone.options.imageDropzone = {
                    parallelUploads: 10,
                    autoProcessQueue: true,
                    addRemoveLinks: true,
                    maxFiles: 10,
                    maxFilesize: 150,
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

                //.removeImage linkini dinleyelim
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

                //#removeAllImages tıklanınca tüm resimleri silelim
                $(document).on("click", "#removeAllImages", function () {
                    $("#removeAllImageModal").modal("show");
                });

                //#removeAllImageButton tıklanınca tüm resimleri silelim
                $(document).on("click", "#removeAllImageButton", function () {
                    $(".imageBox").remove();
                    $("#removeAllImageModal").modal("hide");
                });

                $(document).on("submit", "#addGalleryForm", function (e) {
                    e.preventDefault();
                    $galleryName = $("#galleryName").val();
                    if ($galleryName === "") {
                        $("#alertMessage").html("Galeri Adı Boş Olamaz");
                        $("#alertModal").modal("show");
                    } else {

                        $imageCount = $(".imageBox").length;
                        if ($imageCount === 0) {
                            $("#alertMessage").html("En az 1 tane resim eklemelisiniz");
                            $("#alertModal").modal("show");
                            return;
                        }

                        let action = "addGallery";

                        let galleryID = $("#galleryID").val();

                        if(galleryID > 0){
                            action = "updateGallery";
                        }

                        let formData = $(this).serialize();

                        formData += "&action=" + action;

                        $.ajax({
                            type: 'POST',
                            url: "/App/Controller/Admin/AdminGalleryController.php",
                            data: formData,
                            dataType: 'json',
                            success: function (data) {
                                console.log(data);
                                $data = data;
                                if ($data.status === "success") {
                                    $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                    $("#alertMessage").html($data.message);
                                    $("#alertModal").modal("show");

                                    //1,5 saniye sonra sayfayı yönlendirelim
                                    setTimeout(function () {
                                        window.location.href = "/_y/s/s/galeriler/GalleryList.php";
                                    }, 1500);
                                } else {
                                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                    $("#alertMessage").html($data.message);
                                    $("#alertModal").modal("show");
                                }
                            }
                        });
                    }
                });
            });
		</script>

	</body>
</html>