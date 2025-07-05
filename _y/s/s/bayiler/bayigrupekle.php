<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Bayi Gruplar";
$formbaslik="Bayi Grupları";
$butonisim="EKLE";

$f_uyebayigrupid=f("uyebayigrupid");
$f_uyebayigrupad=f("uyebayigrupad");
$f_uyebayigrupaciklama=f("uyebayigrupaciklama");
$f_uyebayigrupindirim=f("uyebayigrupindirim");


if(S(f("uyebayigrupekle"))==1 && !BosMu($f_uyebayigrupad))
{
	$sutunlar="uyebayigrupad,
		uyebayigrupaciklama,
		uyebayigrupindirim,
		uyebayigrupsil";
	$degerler=$f_uyebayigrupad."|*_".
		$f_uyebayigrupaciklama."|*_".
		$f_uyebayigrupindirim."|*_".
		"0";
	$tablo="uyebayigrup";

	if(dogrula("uyebayigrup","uyebayigrupad='". $f_uyebayigrupad ."'") && S($f_uyebayigrupid)==0)
	{
		$formhata=1;
		$formhataaciklama="DİKKAT: Bu isimde ( $f_uyebayigrupad ) zaten grup var. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/bayiler/bayigrupliste.php'> > Bayi Grup Listesine git <</a><br>";
	}
	if($formhata==0)
	{
		if(S($f_uyebayigrupid)==0)
		{
			$f_benzersizid=SifreUret(20,2);
			ekle($sutunlar.",benzersizid",$degerler."|*_".$f_benzersizid,$tablo,60);
			$f_uyebayigrupid = teksatir(" Select uyebayigrupid from uyebayigrup Where benzersizid='". $f_benzersizid ."'","uyebayigrupid");
		}
		else
		{
			guncelle($sutunlar,$degerler,$tablo," uyebayigrupid='". $f_uyebayigrupid ."' ",60);
		}
	}
}
if(S(q("uyebayigrupid"))!=0)
{
	if(dogrula("uyebayigrup","uyebayigrupid='". q("uyebayigrupid") ."'"))
	{
		$butonisim="GÜNCELLE";
		$f_uyebayigrupid=q("uyebayigrupid");
		$f_uyebayigrupad=teksatir("Select uyebayigrupad From uyebayigrup Where uyebayigrupid='". q("uyebayigrupid") ."'","uyebayigrupad");
		$f_uyebayigrupaciklama=teksatir("Select uyebayigrupaciklama From uyebayigrup Where uyebayigrupid='". q("uyebayigrupid") ."'","uyebayigrupaciklama");
		$f_uyebayigrupindirim=teksatir("Select uyebayigrupindirim From uyebayigrup Where uyebayigrupid='". q("uyebayigrupid") ."'","uyebayigrupindirim");
	}
}
if(strlen("$f_uyebayigrupindirim")==3)$f_uyebayigrupindirim=$f_uyebayigrupindirim."0";
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
							<li class="active"><a href="/_y/s/s/bayiler/bayigrupliste.php" class="btn ink-reaction btn-raised btn-primary">Bayi Grup Liste</a></li>
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
										<input type="hidden" name="uyebayigrupekle" value="1">
										<input type="hidden" name="uyebayigrupid" value="<?=$f_uyebayigrupid?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="uyebayigrupad" 
																id="uyebayigrupad" 
																value="<?=$f_uyebayigrupad?>"
																placeholder="Bayi Grup Adını Yazın" required aria-required="true" >
																<label for="uyebayigrupad">Bayi Grup Adını Yazın</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="uyebayigrupaciklama" 
																	id="uyebayigrupaciklama" 
																	class="form-control" 
																	rows="4" 
																	placeholder
																	style="
																		background-color:#efefef; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=ltrim($f_uyebayigrupaciklama)?></textarea>
																<label for="grupaciklama">Bayi Grup Açıklama</label>
															</div>
														</div>
													</div>
													
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="uyebayigrupindirim" 
																id="uyebayigrupindirim" 
																value="<?=$f_uyebayigrupindirim?>"
																placeholder="Bayi Grup indirim oranı %10 için 0.10"
																data-rule-number="true" 
																required="" 
																aria-required="true" 
																aria-invalid="false" >
																<label for="uyebayigrupindirim">Bayi Grup indirim oranı %10 için 0.10 | %25 için 0.25</label>
															</div>
														</div>
													</div>
													
												</div>
											</div>
										</div>
										
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
												<button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
											</div>
										</div>
										
									</form>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>

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
			$("#bayigrupeklephp").addClass("active");

			$('#demo-date-format').datepicker({
			    format: 'yyyy-mm-dd',
			    language: 'tr'	
			});
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>