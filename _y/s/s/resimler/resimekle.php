<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
if(!isset($_SESSION['sayfabenzersizid']) || BosMu($_SESSION['sayfabenzersizid']))$_SESSION['sayfabenzersizid'] = SifreUret(20,2);
?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>Material Admin - Form advanced</title>

		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<!-- END META -->

		<!-- BEGIN STYLESHEETS -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/select2/select2.css?1424887856" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/multi-select/multi-select.css?1424887857" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-colorpicker/bootstrap-colorpicker.css?1424887860" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-tagsinput/bootstrap-tagsinput.css?1424887862" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/typeahead/typeahead.css?1424887863" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/dropzone/dropzone-theme.css?1424887864" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/summernote/summernote.css?1425218701" />
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">

		<!-- BEGIN HEADER-->
		<?php require_once($anadizin."/_y/s/b/header.php");?>
		<!-- END HEADER-->

		<!-- BEGIN BASE-->
		<div id="base">

			<!-- BEGIN OFFCANVAS LEFT -->
			<div class="offcanvas">
				<!-- sol popup -->
				<div id="offcanvas-left" class="offcanvas-pane width-12" >
					<div class="offcanvas-head">
						<header>Yeni Resim Yükleyin</header>
						<div class="offcanvas-tools">
							<a id="solcanvas" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
								<i class="md md-close"></i>
							</a>
						</div>
					</div>
					<div class="offcanvas-body">
						<div class="card">				
							<div class="card-body no-padding">
								<form action="/_y/s/f/topluresimyukle.php" target="_islem" class="dropzone dz-clickable" id="myawesomedropzone">
									<div class="form-group">
										<input type="hidden" name="resimklasor" value="sayfa">
										<div class="dz-message">
											<h3>Resmi Sürükleyin ve Bırakın veya Tıklayın.</h3>
											<em>En fazla <strong>20 (100MB)</strong> resim seçin</em>
										</div>
									</div>
								</form>
							</div><!--end .card-body -->
						</div>
					</div>
					<div class="force-padding stick-bottom-right">
						<a class="btn btn-floating-action btn-default-dark" href="#offcanvas-demo-size3" data-toggle="offcanvas">
							<i class="md md-arrow-back"></i>
						</a>
					</div>
				</div>
				<!-- //sol popop-->
			</div><!--end .offcanvas-->
			<!-- END OFFCANVAS LEFT -->

			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">TOPLU RESİM EKLE</li>
						</ol>
					</div>
					<div class="section-body contain-lg">

						<!-- BEGIN INTRO -->
						<div class="row">
							<div class="col-lg-12">
								<h1 class="text-primary">Resimleri soldaki panele bırakın</h1>
							</div><!--end .col -->
							<div class="col-lg-8">
								<article class="margin-bottom-xxl">
									<p class="lead">
										Sürükle Bırak!
									</p>
									<p class="alert-warning lead"> Yükleme başarılı olduğunda yükleme penceresi otomatik kapanacaktır!</p>
									<a 
										class="btn btn-floating-action ink-reaction" 
										href="#offcanvas-left" 
										id="yeniekle"
										data-resimkutu="#" 
										data-id="#" 
										data-toggle="offcanvas" 
										title="ekle">
										<i class="fa fa-plus"></i>
									</a>
								</article>
							</div><!--end .col -->
						</div><!--end .row -->
					</div><!--end .section-body -->
				</section>
			</div><!--end #content-->
			<!-- END CONTENT -->
			<!-- BEGIN MENUBAR-->
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
			<!-- END MENUBAR -->
			<!-- BEGIN OFFCANVAS RIGHT -->
			<?php require_once($anadizin."/_y/s/b/sagpopup.php");?>
			<!-- END OFFCANVAS RIGHT -->
		</div><!--end #base-->
		<!-- END BASE -->
		<div 
			class="modal fade in" id="textModal" tabindex="-1" role="dialog" 
			aria-labelledby="textModalLabel" aria-hidden="false">
			<div class="modal-backdrop fade in" style="height: 1019px;"></div>
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="textModalLabel">Resim Yükleme</h4>
					</div>
					<div class="modal-body">
						<p>RESİM YÜKLENİYOR</p>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<!-- BEGIN JAVASCRIPT -->
		<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
		<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.4.1.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

		<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
		<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>

		<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
		<script src="/_y/assets/js/libs/select2/select2.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

		<script src="/_y/assets/js/libs/multi-select/jquery.multi-select.js"></script>
		<script src="/_y/assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
		<script src="/_y/assets/js/libs/moment/moment.min.js"></script>

		<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-rating/bootstrap-rating-input.min.js"></script>

		<script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>

		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>
		
		<script src="/_y/assets/js/libs/ckeditor/adapters/jquery.js"></script>
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		<!-- END JAVASCRIPT -->
		<script>
			$("#resimeklephp").addClass("active");
		</script>
		<script type="text/javascript">
			
			Dropzone.autoProcessQueue= true;
			Dropzone.options.myawesomedropzone =
			{
				parallelUploads: 10,
				autoProcessQueue: true,
				addRemoveLinks: true,
				maxFiles: 20,
				maxFilesize: 100,
				dictDefaultMessage: "Resimleriı yüklemek için bırakın",
				dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
				dictFallbackText: "Resimlerinizi eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
				dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
				dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
				dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
				dictCancelUpload: "İptal Et",
				dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
				dictRemoveFile: "Dosya Sil",
				dictRemoveFileConfirmation: null,
				dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
				
				removedfile: function(file)
				{ 
					var _ref;
					return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
				},
				init: function()
			    {
			        this.on("success", function(file, responseText)
			        {
			            $yuklemesonuc=responseText.replace('"', '');
			            $yuklemesonuc=$yuklemesonuc.replace('"', '');
			            $yuklemesonuc=$yuklemesonuc.replace("\\", '');
			            
						if($yuklemesonuc=="true")
						{
							$("#solcanvas").click();
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Başarılı");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
							
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert alert-warning");
							
							this.removeAllFiles();
							//$("#textModal").css("display","none");
							$("#textModal").delay(1000).fadeOut('slow');
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleniyor");
						}
						else
						{
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert alert-success");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Hatası");
						}
						
			        });
			        this.on("addedfile", function(file)
			        {
			        	$("#textModal").show();
			        	$("#textModal").css("display","block");
			        	
					});
			    }
			};
			</script>
			
			<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
	</body>
</html>