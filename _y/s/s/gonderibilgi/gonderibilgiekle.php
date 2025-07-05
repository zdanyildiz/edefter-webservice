<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Eposta Gönderim Bilgisi Ekle";
$formbaslik="Eposta Gönderim Bilgisi Ekle";
$butonisim="EKLE";

$f_ayarfirmaid=f("ayarfirmaid");
$f_gonderimunvan=f("gonderimunvan");
$f_gonderimposta=f("gonderimposta");
$f_gonderimsifre=f("gonderimsifre");

if(S(f("gonderibilgiekle"))==1 && !BosMu($f_gonderimposta) && !Bosmu($f_gonderimsifre) && !Bosmu($f_gonderimunvan))
{
	$sutunlar="gonderimunvan,gonderimposta,gonderimsifre,ayarfirmasil";

	$degerler=$f_gonderimunvan."|*_".$f_gonderimposta."|*_".$f_gonderimsifre."|*_"."0";
	$tablo="ayarfirma";
		if($f_ayarfirmaid==0)
		{
			$f_benzersizid=SifreUret(20,2);
			ekle($sutunlar.",benzersizid",$degerler."|*_".$f_benzersizid,$tablo,35);
			$f_ayarfirmaid = teksatir(" Select ayarfirmaid from ayarfirma Where benzersizid='". $f_benzersizid ."'","ayarfirmaid");
		}
		else
		{
			guncelle($sutunlar,$degerler,$tablo,"ayarfirmaid='".$f_ayarfirmaid."'",35);
		}
}
if(S(q("ayarfirmaid"))!=0)
{
	if(dogrula("ayarfirma","ayarfirmaid='". q("ayarfirmaid") ."'"))
	{
		$butonisim="GÜNCELLE";
		$f_ayarfirmaid=q("ayarfirmaid");
		$f_gonderimunvan=teksatir("Select gonderimunvan From ayarfirma Where ayarfirmaid='". q("ayarfirmaid") ."'","gonderimunvan");
		$f_gonderimposta=teksatir("Select gonderimposta From ayarfirma Where ayarfirmaid='". q("ayarfirmaid") ."'","gonderimposta");
		$f_gonderimsifre=teksatir("Select gonderimsifre From ayarfirma Where ayarfirmaid='". q("ayarfirmaid") ."'","gonderimsifre");
	}
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title><?=$sayfabaslik?></title>

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
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
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
			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="btn ink-reaction btn-raised btn-primary disabled">Ayarlar</li>
							<li class="btn ink-reaction btn-raised btn-primary disabled"><?=$sayfabaslik?></li>
							<li class="active"><a href="/_y/s/s/gonderibilgi/gonderibilgiliste.php" class="btn ink-reaction btn-raised btn-primary">Gönderim Bilgisi Liste</a></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary form-inverse">
										<header><?=$formbaslik?></header>
									</div>
									<form name="formanaliz" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="gonderibilgiekle" value="1">
										<input type="hidden" name="ayarfirmaid" value="<?=$f_ayarfirmaid?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">		

														<div class="col-sm-4">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="gonderimunvan" 
																id="gonderimunvan" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$f_gonderimunvan?>"
																placeholder="Gönderi Ünvan Bilgisini Giriniz" required aria-required="true" >
																<label for="gonderimunvan">Gönderi Ünvan Bilgisini Giriniz</label>
															</div>
														</div>

														<div class="col-sm-4">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="gonderimposta" 
																id="gonderimposta" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$f_gonderimposta?>"
																placeholder="Eposta Yazınız" required aria-required="true" >
																<label for="gonderimposta">Eposta Yazınız</label>
															</div>
														</div>														


														<div class="col-sm-4">
															<div class="form-group floating-label">
															<input 
																type="password" 
																class="form-control" 
																name="gonderimsifre" 
																id="gonderimsifre" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$f_gonderimsifre?>"
																placeholder="Eposta Şifrenizi Yazınız" required aria-required="true" >
																<label for="gonderimsifre">Eposta Şifrenizi Yazınız </label>
															</div>
														</div>	
																													
													</div>
												</div><!--end .col -->
											</div><!--end .row -->
										</div><!--end .card-body -->
										<!-- END DEFAULT FORM ITEMS -->
										<!-- BEGIN FORM FOOTER -->
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
												<button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
											</div><!--end .card-actionbar-row -->
										</div><!--end .card-actionbar -->
										<!-- END FORM FOOTER -->
									</form>
								</div><!--end .card -->
							</div><!--end .col -->
							<!-- END ADD CONTACTS FORM -->
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
		<!-- BEGIN JAVASCRIPT -->

		<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
		<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
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

		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>

		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		
		<script>
			$("#gonderibilgilistephp").addClass("active");

			$('#demo-date-format').datepicker({
			    format: 'yyyy-mm-dd',
			    language: 'tr'	
			});
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>