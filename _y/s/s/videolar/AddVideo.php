<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */
$buttonName="Video Ekle";

$videoID = $_GET["videoID"] ?? 0;
$videoID = intval($videoID);

include_once MODEL."Admin/AdminVideo.php";
$videoModel = new AdminVideo($db);

if($videoID > 0){
    $videoData = $videoModel->getVideoById($videoID);
    if(!empty($videoData)){
        $videoData = $videoData[0];
        $videoID = $videoData["video_id"];
        $videoUniqID = $videoData["unique_id"];
        $videoName = $videoData["video_name"];
        $videoDescription = $videoData["description"];
        $videoFile = $videoData["video_file"];
        $videoExtension = $videoData["video_extension"];
        $videoSize = $videoData["video_size"];
        $videoWidth = $videoData["video_width"];
        $videoHeight = $videoData["video_height"];
        $videoIframe = $videoData["video_iframe"];

        $buttonName = "Video Düzenle";
    }
}

$videoUniqID = $videoUniqID ?? "";
$videoName = $videoName ?? "";
$videoDescription = $videoDescription ?? "";
$videoFile = $videoFile ?? "";
$videoExtension = $videoExtension ?? "";
$videoSize = $videoSize ?? "";
$videoWidth = $videoWidth ?? "";
$videoHeight = $videoHeight ?? "";
$videoIframe = $videoIframe ?? "";
?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>Video Ekle Pozitif Eticaret</title>

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
							<li class="active">Resim Videosi Ekle / Düzenle</li>
						</ol>
					</div>
					<div class="section-body contain-lg">

						<form id="addVideoForm" class="form" method="post">
							<input type="hidden" name="videoID" 	id="videoID" 	value="<?=$videoID?>">
							<input type="hidden" name="videoFile" 	id="videoFile" 	value="<?=$videoFile?>">
                            <input type="hidden" name="videoExtension" id="videoExtension" value="<?=$videoExtension?>">
                            <input type="hidden" name="videoSize" id="videoSize" value="<?=$videoSize?>">
                            <input type="hidden" name="videoWidth" id="videoWidth" value="<?=$videoWidth?>">
                            <input type="hidden" name="videoHeight" id="videoHeight" value="<?=$videoHeight?>">

                            <div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Video Temel Bilgiler</h4><p></p>
										<p>
											Video Adı Boş Olamaz
										</p>
										<br>
										<p>Video sayfasında görüntülenmek üzere bir açıklama ekleyebilirsiniz</p>
									</article>
								</div>
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div class="form-group">
												<input type="text" name="videoName" id="videoName" class="form-control" placeholder="Örn:Ürünler" value="<?=$videoName?>" aria-invalid="false" required="" aria-required="true">
												<label>Video Adı</label>
											</div>
											<div class="form-group">
												<textarea 
													id="videoDescription" 
													name="videoDescription" 
													placeholder="Video konusunu ve ya içeriğini yazabilirsiniz"
													class="form-control"  
													rows="5"
													><?=$videoDescription?></textarea>
													<label for="videoDescription">Video Açıklama</label>
											</div>
										</div>
									</div>
									<em class="text-caption">Video Temel özellikleri seçin</em>
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Video Yükleyin</h4>										
										<p>Yüklemek için bir video seçin </p>
									</article>
								</div>

								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
                                        <div class="btn-group" id="imageButtonContainer" data-toggle="buttons">

                                            <label class="btn  btn-primary-bright btn-md"
                                                   href="#offcanvas-videoUpload"
                                                   id="addVideoByLeftCanvas"
                                                   data-uploadtarget="Video"
                                                   data-toggle="offcanvas">
                                                <i class="fa fa-plus fa-fw"></i>
                                                Video Yükle
                                            </label>
                                            
                                        </div>

                                        <div class="card-body" id="videoContainer" data-sortable="true" >
                                            <?php
                                            ?>
                                        </div>

									</div>
									<em class="text-caption">Eklenen Video</em>
								</div>
							</div>

                            <div class="row">
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl">
                                        <h4>Harici Video Ekleyin</h4>
                                        <p>Youtube gibi bir platformdan video iframe kodu yapıştırın </p>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#copyVideoCodeInfoModal">Nasıl Yapılır?</a>
                                    </article>
                                </div>

                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <textarea
                                                    id="videoIframe"
                                                    name="videoIframe"
                                                    placeholder="Video iframe kodunu yapıştırın"
                                                    class="form-control"
                                                    rows="5"
                                                    ><?=$videoIframe?></textarea>
                                                    <label for="videoIframe">Video İframe Kodu</label>
                                            </div>
                                        </div>
                                    </div>
                                    <em class="text-caption">Harici Video</em>
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

            <div class="modal fade" id="copyVideoCodeInfoModal" tabindex="-1" role="dialog" aria-labelledby="copyVideoCodeInfoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title" id="locationSelectInfoModalLabel">Youtube Video Iframe Kodu Nasıl Alınır?</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <p><strong># Videonuzu Bulun</strong><br>
                                <a href="https://www.youtube.com/" class="text-primary border-lg" target="_blank">youtube.com</a>'adresine gidin.<br>
                                Arama alanına video adını yazın ve aratın.<br>
                                Bulduğunuz videonun altındaki <strong>Paylaş</strong> butonuna tıklayın ve açılan menüden en solda yer alan <strong>Yerleştir</strong> butonuna tıklayın.<br>
                                Gelen pancereden <strong>Kopyala</strong> bağlantısına tıklayın.<br>
                                Bu sayfadaki <strong>Video Iframe Kodu</strong> alanına yapıştırın alanına yapıştırın.
                            </p>
                            <img src="/_y/assets/img/video_ekle_1.jpg" width="100%" style="width: 100%; height: auto">
                            <img src="/_y/assets/img/video_ekle_2.jpg" width="100%" style="width: 100%; height: auto">
                            <img src="/_y/assets/img/video_ekle_3.jpg" width="100%" style="width: 100%; height: auto">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
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
        <script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>

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
			$("#addVideophp").addClass("active");

            $(document).ready(function() {

                Dropzone.options.videoDropzone = {
                    parallelUploads: 1,
                    autoProcessQueue: true,
                    addRemoveLinks: true,
                    maxFiles: 1,
                    maxFilesize: 150,
                    dictDefaultMessage: "Video yüklemek için bırakın",
                    dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
                    dictFallbackText: "Resimleri eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
                    dictFileTooBig: "Resim çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
                    dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
                    dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
                    dictCancelUpload: "İptal Et",
                    dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
                    dictRemoveFile: "Video Sil",
                    dictRemoveFileConfirmation: null,
                    dictMaxFilesExceeded: "Daha fazla video yükleyemezsiniz.",
                    acceptedFiles: ".mp4, .webm, .mov, .avi",
                    //resimler adı imageName inputu boşsa yükleme yapmayalım
                    accept: function (file, done) {
                        console.log("accept");
                        done();
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
                                let videoData = responseObject.videoData;

                                let $videoFile = videoData.video_file;
                                let $videoExtension = videoData.video_extension;
                                let $videoSize = videoData.video_size;
                                let $videoWidth = videoData.video_width;
                                let $videoHeight = videoData.video_height;


                                $("#videoFile").val($videoFile);
                                $("#videoExtension").val($videoExtension);
                                $("#videoSize").val($videoSize);
                                $("#videoWidth").val($videoWidth);
                                $("#videoHeight").val($videoHeight);

                                //videoid için 5 haneli sayı üretelim
                                let $videoID = Math.floor(Math.random() * 90000) + 10000;


                                let $videoBox = '<div class="videoBox" id="videoBox' + $videoID + '">';
                                $videoBox += '<video width="320" height="240" controls>';
                                $videoBox += '<source src="/Public/' + $videoFile + '" type="video/' + $videoExtension + '">';
                                $videoBox += 'Tarayıcınız video etiketini desteklemiyor.';
                                $videoBox += '</video>';
                                $videoBox += '<a href="javascript:void(0);" class="removeVideo" data-videobox="videoBox' + $videoID + '">Videoyu Sil</a>';
                                $videoBox += '</div>';

                                $("#videoContainer").append($videoBox);


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

                //addVideoByLeftCanvas tıklandığında videoName alanı boşsa canvas açılmasın
                $("#addVideoByLeftCanvas").click(function () {

                    $videoName = $("#videoName").val();
                    if($videoName == ""){

                        $("#offcanvas-videoUploadOff").click();

                        $("#alertMessage").html("Video Adı Boş Olamaz");
                        $("#alertModal").modal("show");

                        return false;
                    }

                    $("#uploadVideoName").val($videoName);
                });

                //videoSil
                $(document).on("click", ".removeVideo", function () {
                    $videoBox = $(this).data("videobox");
                    $("#" + $videoBox).remove();
                    $("#videoFile").val("");
                    $("#videoExtension").val("");
                    $("#videoSize").val("");
                    $("#videoWidth").val("");
                    $("#videoHeight").val("");
                });

                $("#addVideoForm").submit(function (e) {
                    e.preventDefault();

                    $action = "addVideo";
                    $videoID = $("#videoID").val();

                    if($videoID > 0){
                        $action = "updateVideo";
                    }

                    $videoName = $("#videoName").val();
                    $videoDescription = $("#videoDescription").val();
                    $videoIframe = $("#videoIframe").val();
                    $videoFile = $("#videoFile").val();
                    $videoExtension = $("#videoExtension").val();
                    $videoSize = $("#videoSize").val();
                    $videoWidth = $("#videoWidth").val();
                    $videoHeight = $("#videoHeight").val();


                    if($videoName == ""){
                        alert("Video Adı Boş Olamaz");
                        return false;
                    }

                    //file ve iframe aynı anda boş olamaz
                    if($videoFile == "" && $videoIframe == ""){
                        $("#alertMessage").html("Dosya yükleyin ya da iframe kodu girin");
                        return false;
                    }

                    $formData = $(this).serializeArray();
                    $formData.push({name: "action", value: $action});

                    $.ajax({
                        url: "/App/Controller/Admin/AdminVideoController.php",
                        type: "POST",
                        data: $formData,
                        success: function (response) {
                            console.log(response);
                            $response = JSON.parse(response);
                            $status = $response.status;
                            $message = $response.message;

                            if($status == "success"){
                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                $("#alertMessage").html($message);
                                $("#alertModal").modal("show");
                                //1,5 sanise sonra sayfayı yönlendir
                                setTimeout(function () {
                                    window.location.href = "/_y/s/s/videolar/VideoList.php";
                                }, 1500);
                            }else{
                                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                $("#alertMessage").html($message);
                                $("#alertModal").modal("show");
                            }
                        },
                        error: function (response) {
                            console.log(response);
                        }
                    });
                });

            });
		</script>

	</body>
</html>