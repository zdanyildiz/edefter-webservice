<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Helper $helper
 */

$helper = $config->Helper;

$addAdminID = $_GET["adminID"] ?? 0;
$addAdminID = intval($addAdminID);

include_once MODEL . 'Admin/Admin.php';
$adminModel = new Admin($db);

$addAdmin = $adminModel->getAdmin($addAdminID);

if(!empty($addAdmin)) {
    $addAdminKey = $addAdmin['adminKey'];
    $addAdminID = $addAdmin['adminID'];
    $addAdminNameSurname = $helper->decrypt($addAdmin['adminNameSurname'], $config->key);
    $addAdminPIN = $addAdmin['adminPIN'];
    $addAdminPhone = $helper->decrypt($addAdmin['adminPhone'], $config->key);
    $addAdminEmail = $helper->decrypt($addAdmin['adminEmail'], $config->key);
    $addAdminAuth = $addAdmin['adminAuth'];
    $addAdminActive = $addAdmin['adminActive'];
    $addAdminImage = $addAdmin['adminImage'];
    $buttonName = "Güncelle";
}

$addAdminKey = $addAdminKey ?? "";
$addAdminID = $addAdminID ?? 0;
$addAdminNameSurname = $addAdminNameSurname ?? "";
$addAdminPIN = $addAdminPIN ?? "";
$addAdminPhone = $addAdminPhone ?? "";
$addAdminEmail = $addAdminEmail ?? "";
$addAdminAuth = $addAdminAuth ?? 0;
$addAdminActive = $addAdminActive ?? 0;
$addAdminDeleted = $addAdminDeleted ?? 0;
$addAdminImage = $addAdminImage ?? "yoneticiler/img.jpg";
$buttonName = $buttonName ?? "Ekle";

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Yönetici Ekle / Düzenle Pozitif Eticaret</title>

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
    
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		
		<div id="base">
            <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Yönetici Ekle / Düzenle</li>
						</ol>
					</div>
					
					<div class="section-body contain-lg">
						<div class="row">

                            <div class="col-lg-3 col-md-4">
                                <article class="margin-bottom-xxl">
                                    <h4>Yönetici Bilgileri</h4><p></p>
                                    <p>
                                        Bu sayfadan siteniz için yeni bir yönetici ekleyebilir ve yöneticilerin bilgilerini düzenleyebilirsiniz.
                                    </p>
                                    <h4>İki Adımlı Doğrulama</h4>
                                    <p>Yöneticilerin bir şifresi olmaz, panele girerken anlık şifre üretilir. Üretilen şifre yöneticinin e-posta ya da telefonuna gönderilir.</p>
                                    <p>Anlık üretilen şifre 5 dakika içinde girilmelidir aksi halde geçersiz olur ve yeni bir şifre üretilmesi gerekir.</p>
                                    <h4>PIN Nedir?</h4>
                                    <p>Her yöneticinin 4 haneli pin kodu olmalıdır. Pin kodu sayesinde yönetici paneldeyken geçici olarak ekran başından kalktığında sağ üst köşede bulunan yönetici menüsünden ekranı kitleyebilir ve tekrar panele girmek istediğinde anlık şifre üretmek ve e-posta beklemek zorunda kalmaz. Pin kodunu girerek paneli kullanmaya tekrar devam edebilir</p>
                                </article>
                            </div>
                            <div class="col-lg-offset-1 col-md-8">
								<div class="card">
									<form id="addAdminForm" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="adminKey" value="<?=$addAdminKey?>">
										<input type="hidden" name="adminID" id="adminID" value="<?=$addAdminID?>">
										<input type="hidden" name="adminImage" id="adminImage" value="<?=$addAdminImage?>">
										
										<div class="card-body style-primary form-inverse">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group floating-label">
																<input type="text" class="form-control input-lg" id="adminNameSurname" name="adminNameSurname" value="<?=$addAdminNameSurname?>" required="" aria-required="true">
																<label for="adminNameSurname">İsim Soyisim</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group floating-label">
																<input type="text" class="form-control" id="adminPIN" name="adminPIN" value="<?=$addAdminPIN?>" data-rule-minlength="4" maxlength="4" required="" aria-required="true" aria-describedby="adminPIN-error">
																<label for="adminPIN">PIN Kodu</label>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group floating-label">
																<input type="text" class="form-control" id="adminPINr" name="adminPINr" value="<?=$addAdminPIN?>" data-rule-minlength="4" maxlength="4" required="" aria-required="true" data-rule-equalto="#adminPIN">
																<label for="adminPINr">PIN Kodu Tekrar</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-head">
											<p class="text-bold" style="margin-left: 15px">DİĞER BİLGİLER</p>
										</div>
										<div class="card-body">
											<div>
												<div class="row">
													<div class="col-md-8">
                                                        <div class="form-group">
                                                            <input type="tel" class="form-control" id="adminPhone" name="adminPhone" value="<?=$addAdminPhone?>">
                                                            <label for="adminPhone">Cep Telefonu</label>
                                                            <p class="help-block">Başında 0 ve boşluk kullanmayınız! Cep telefonu: 5321234567</p>
														</div>
														<div class="form-group">
															<input type="email" class="form-control" id="adminEmail" name="adminEmail" value="<?=$addAdminEmail?>" required="" aria-required="true">
															<label for="adminEmail">E-Posta</label>
														</div>
														<div class="form-group">
															<select name="adminAuth" id="adminAuth" class="form-control">
																<?php if($addAdminAuth==0){ ?>
																<option value="0" <?php if($addAdminAuth==0) echo "selected"; ?>>Süper Yönetici</option>
																<?php } ?>
																<option value="1" <?php if($addAdminAuth==1) echo "selected"; ?>>Yönetici</option>
																<option value="2" <?php if($addAdminAuth==2) echo "selected"; ?>>Kullanıcı</option>
															</select>
															<label for="yetki">Yönetici Yetki </label>
														</div>

														<div class="row <?php if($adminID == $addAdminID) echo 'hidden'; ?>">
															<div class="col-md-8">
																<div class="form-group">
																	<div class="checkbox checkbox-styled">
																	<label>
																	<input id="adminActive" name="adminActive" type="checkbox" value="1" <?php if($addAdminActive==1) echo "checked"; ?>>
																	<span>Aktif</span>
																	</label>
																	</div>
																</div>
															</div>
														</div>
													</div>

													<div class="col-md-4">
														<div class="form-group">
															<div>
                                                                <img id="adminImageContainer" src="/_y/m/r/<?=$addAdminImage?>">
                                                            </div>
															<a class="btn btn-sm btn-primary-bright"
                                                               href="#offcanvas-imageUpload"
                                                               id="uploadImageByLeftCanvas"
                                                               data-target="adminImageContainer"
                                                               data-uploadtarget="Admin"
                                                               data-toggle="offcanvas">
																Resim Ekle
															</a>
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
            #adminImageContainer{
                width: 100%;
                height: auto;
            }
        </style>
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
			$("#addAdminphp").addClass("active");

            function validatePhoneNumber(phoneNumber) {
                // Telefon numarasının 10 haneli olup olmadığını kontrol edin.
                if (phoneNumber.length !== 10) {
                    return false;
                }
                // Telefon numarasının +90, 90 veya 0 ile başlamadığını kontrol edin.
                if (phoneNumber.startsWith("+90") || phoneNumber.startsWith("90") || phoneNumber.startsWith("0")) {
                    return false;
                }
                // Telefon numarasının sadece sayılardan oluştuğunu kontrol edin.
                if (!/^\d+$/.test(phoneNumber)) {
                    return false;
                }
                // Telefon numarası geçerlidir.
                return true;;
            }
            function validateEmailAddress(email) {
                // E-posta adresinin '@' karakteri içerip içermediğini kontrol edin.
                if (!email.includes("@")) {
                    return false;
                }

                // E-posta adresinin '.' karakteri içerip içermediğini kontrol edin.
                if (!email.includes(".")) {
                    return false;
                }

                // E-posta adresinin geçerli bir formatta olup olmadığını kontrol edin.
                const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (!regex.test(email)) {
                    return false;
                }

                // E-posta adresi geçerlidir.
                return true;
            }

            $(document).on("click", "#uploadImageByLeftCanvas, #addImageByLeftCanvas", function () {
                $imageTarget = $(this).data("target");

                $("#imageTarget").val($imageTarget);

                $uploadTarget = $(this).data("uploadtarget");

                $("#imageFolder").val($uploadTarget);

                $("#imageName").val($("#adminNameSurname").val());
            });

            Dropzone.options.imageDropzone = {
                parallelUploads: 1,
                autoProcessQueue: true,
                addRemoveLinks: true,
                maxFiles: 1,
                maxFilesize: 2,
                dictDefaultMessage: "Resmi yüklemek için bırakın",
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

                            for ($i = 0; $i < $imageResults.length; $i++) {
                                $imageID = $imageResults[$i].imageData.imageID;
                                $imagePath = $imageResults[$i].imageData.imageFolderName + "/" + $imageResults[$i].imageData.imagePath;
                                $imageName = $imageResults[$i].imageData.imageName;
                                $imageWidth = $imageResults[$i].imageData.imageWidth;
                                $imageHeight = $imageResults[$i].imageData.imageHeight;

                                $("#adminImage").val($imagePath);
                                $("#adminImageContainer").attr("src", "/_y/m/r/" + $imagePath);
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

            //form submit, hiçbir alan boş olamaz
            $("#addAdminForm").submit(function(e){
                e.preventDefault();
                var adminNameSurname = $("#adminNameSurname").val();
                var adminPIN = $("#adminPIN").val();
                var adminPINr = $("#adminPINr").val();
                var adminPhone = $("#adminPhone").val();
                var adminEmail = $("#adminEmail").val();
                var adminAuth = $("#adminAuth").val();
                var adminActive = $("#adminActive").is(":checked") ? 1 : 0;
                var adminImage = $("#adminImage").val();

                if(adminNameSurname=="" || adminPIN=="" || adminPINr=="" || adminPhone=="" || adminEmail=="" || adminAuth==""){
                    $("#alertMessage").html("Lütfen tüm alanları doldurunuz.");
                    $("#alertModal").modal("show");
                    return false;
                }
                //pin kodu 4 haneli ve rakam olabilir
                if(adminPIN.length!=4 || isNaN(adminPIN)){
                    $("#alertMessage").html("PIN kodu 4 haneli ve rakam olmalıdır.");
                    $("#alertModal").modal("show");
                    return false;
                }
                if(adminPIN!=adminPINr){
                    $("#alertMessage").html("PIN kodları uyuşmuyor.");
                    $("#alertModal").modal("show");
                    return false;
                }

                //telefon doğrulayalım
                if(!validatePhoneNumber(adminPhone)){
                    $("#alertMessage").html("Telefon numarası geçerli değil.");
                    $("#alertModal").modal("show");
                    return false;
                }

                //email doğrulayalım
                if(!validateEmailAddress(adminEmail)){
                    $("#alertMessage").html("E-posta adresi geçerli değil.");
                    $("#alertModal").modal("show");
                    return false;
                }

                var action = "addAdmin";

                var adminID = $("#adminID").val();
                if(adminID>0){
                    action = "updateAdmin";
                }

                $.ajax({
                    url: "/App/Controller/Admin/AdminController.php",
                    type: "POST",
                    data: {
                        adminID: adminID,
                        adminNameSurname: adminNameSurname,
                        adminPIN: adminPIN,
                        adminPhone: adminPhone,
                        adminEmail: adminEmail,
                        adminAuth: adminAuth,
                        adminActive: adminActive,
                        adminImage: adminImage,
                        action: action
                    },
                    success: function(data){
                        var response = JSON.parse(data);
                        if(response.status=="error"){
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $("#alertMessage").html(response.message);
                            $("#alertModal").modal("show");
                        }
                        else{
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $("#alertMessage").html(response.message);
                            $("#alertModal").modal("show");
                            //1 saniye sonra yönlendirme yapalım
                            setTimeout(function(){
                                window.location.href = "/_y/s/s/yoneticiler/AdminList.php";
                            },1000);
                        }
                    }
                });
            });
		</script>
	</body>
</html>
