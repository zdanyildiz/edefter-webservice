<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);
$languages = $languageModel->getLanguages();

include_once MODEL."Admin/AdminProduct.php";
$productModel = new AdminProduct($db,$config);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Excel Ürün Liste Pozitif Eticaret</title>
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

        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />
        <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."_y/s/b/header.php");?>
		<div id="base">
            <?php require_once(ROOT."_y/s/b/leftCanvas.php");?>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Excel Ürün Liste</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <!-- dil listesi gelecek -->
                                            <select name="languageID" id="languageID" class="form-control">
                                                <option value="0">Dil Seçin</option>
                                                <?php foreach($languages as $language){
                                                    $selected = $language['languageID'] == $languageID ? "selected" : "";
                                                    ?>
                                                    <option value="<?php echo $language['languageID']; ?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <p class="help-block">İndirilecek / Yüklenecek Ürün Listesi için Dil Seçin!</p>
                                        </div>
                                        <div class="form-group">
                                            <button id="downloadProductListButton" type="button" class="btn btn-success btn-sm">Excel Ürün Listesini İndir</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-head">
                                        <header class="card-head-title">Yeni ürün yüklemek, ürünlerinizi güncellemek için excel dosyasını yükleyin. <a href="/_y/assets/file/product_list_template.xlsx" id="downloadProductList" class="btn btn-primary-bright btn-sm" style="position: absolute;right: 20px">Örnek Şablonu İndir</a>
                                        </header>
                                    </div>
                                    <div class="card-body ">
                                        <div class="form-group">
                                            <button href="#offcanvas-productListUpload" id="uploadProductListButton" data-toggle="offcanvas" type="button" class="btn btn-warning btn-sm">Excel Ürün Listesini Yükle</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="copyProductWarning" class="card card-bordered style-primary-bright">
                                    <div class="card-head">
                                        <header>DİKKAT !</header>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-callout">
                                            <strong>EXCEL YÜKLEME UYARISI</strong>
                                            <p>Toplu ürün yüklemek için önce ürün kategorilerinizi oluşturmanız gerekir</p>
                                            <p>Yüklediğiniz ürünün stok kodu ürün listenizde varsa ilgili ürün bilgileri güncellenir, yoksa yeni ürün eklenir</p>
                                            <p>Ürünler seçili dilde eklenir / güncellenir.</p>
                                            <p>Görseli olan ürünler güncellenirken excel şablonunuzdaki resim alanı boş olsa bile eklediğiniz ürünler silinmez</p>
                                            <p>Hatalı görsel linkleri olsa dahi ürünler görselsiz olarak eklenir / güncellenir</p>
                                            <p>Bu adımda ürünler sisteme kaydedilir ve ürün aktarma sayfasına yönlendirilirsiniz. Ürün aktarım işlemlerini Ürün Aktarım sayfasından yapabilirsiniz.</p>
                                            <p>Excel Ürün Listesini İndir butonuyla seçtiğiniz dildeki ürünleri indirip, gerekli düzenlemelerden sonra hepsini birden güncelleyebilirsiniz.</p>
                                            <p>Yalnızca fiyat güncellemesi yapacaksanız, <a href="/_y/s/s/urunler/UpdateProductPrice.php" class="btn btn-primary-bright btn-sm">Toplu Fiyat Güncelle</a> Sayfasından yapabilirsiniz.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</section>
			</div>
			<?php require_once(ROOT."_y/s/b/menu.php");?>
		</div>

        <!-- alert uyarıları için modal oluşturalım -->
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn-popup-alert-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="alertModalLabel">Uyarı</h4>
                    </div>
                    <div class="modal-body">
                        <p id="alertMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /*.breadcrumb > li.active{font-size: inherit}*/
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
            $("#downloadProductListphp").addClass("active");
            //sayfa tamamen yüklendikten sonra çalışacak kodlar
            function categoriesMobileShow(){
                //ekran çözünürlüğü 1024'ten küçükse
                if($(window).width() <= 1024){

                    $("#productList img").css({"width":"50px","height":"50px"});
                }
            }

            $(document).ready(function() {
                $("#downloadProductListButton").click(function(){
                    var languageID = $("#languageID").val();
                    var action = "downloadProductList";

                    // Modal'ı yükleniyor mesajı olarak ayarla ve göster
                    $("#alertModalLabel").text("Bilgi");
                    $("#alertMessage").text("Ürün listesi oluşturuluyor, lütfen bekleyiniz...");
                    $("#alertModal").modal("show");

                    window.location.href = "/App/Controller/Admin/AdminProductController.php?action=" + action + "&languageID=" + languageID

                    //3 saniye sonra modalı kapatalım
                    setTimeout(function(){
                        $("#alertModal").modal("hide");
                      }, 3000);
                    return false;

                });

                $("#uploadProductListButton").click(function(){
                    const languageID = $("#languageID").val();
                    $("#productListUploadLanguageID").val(languageID);
                });

                Dropzone.options.productListDropzone = {
                    parallelUploads: 1,
                    autoProcessQueue: true,
                    addRemoveLinks: true,
                    maxFiles: 1,
                    maxFilesize: 10,
                    dictDefaultMessage: "Dosyaları yüklemek için bırakın",
                    dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
                    dictFallbackText: "Dosyaları eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
                    dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
                    dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
                    dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
                    dictCancelUpload: "İptal Et",
                    dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
                    dictRemoveFile: "Dosya Sil",
                    dictRemoveFileConfirmation: null,
                    dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
                    acceptedFiles: ".xls,.xlsx",
                    //dosyalar adı fileName inputu boşsa yükleme yapmayalım
                    accept: function (file, done) {

                        console.log("Dosya Dosya Accept fonksiyonu çağırıldı.");
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

                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                $("#alertMessage").text(responseObject.message);
                                $("#alertModal").modal("show");
                                setTimeout(function(){
                                    window.location.href = "/_y/s/s/urunler/ProductTransfer.php";
                                },1500);

                                //dropzone'a eklenen dosyaları silelim
                                this.removeAllFiles();
                                //offcanvas kapat
                                $("#offcanvas-productListUpload").click();
                            }
                            else {
                                //hata mesajını burada işleyebilirsiniz
                                console.log(responseText);
                                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                $("#alertMessage").text(responseObject.message);
                                $("#alertModal").modal("show");
                            }

                        });
                        this.on("error", function (file, responseText) {
                            // Hata mesajını burada işleyebilirsiniz
                            console.log(responseText);
                        });
                    }
                };
            });
        </script>
	</body>
</html>
