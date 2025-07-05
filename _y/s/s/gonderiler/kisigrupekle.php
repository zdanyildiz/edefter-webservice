<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Rehber Gruplar";
$formbaslik="Kişi Grupları";
$butonisim="EKLE";

$f_rehbergrupid=f("rehbergrupid");
$f_rehbergrupad=f("rehbergrupad");
$f_rehbergrupaciklama=f("rehbergrupaciklama");



if(S(f("rehbergrupekle"))==1 && !BosMu($f_rehbergrupad))
{
	$sutunlar="rehbergrupad,
		rehbergrupaciklama,
		rehbergrupaktif,
		rehbergrupsil";

	$degerler=$f_rehbergrupad."|*_".
		$f_rehbergrupaciklama."|*_".
		"1|*_0";
	$tablo="rehbergrup";

	if(dogrula("rehbergrup","rehbergrupad='". $f_rehbergrupad ."'") && S($f_rehbergrupid)==0)
	{
		$formhata=1;
		$formhataaciklama="DİKKAT: Bu isimde ( $f_rehbergrupad ) zaten grup var. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/gruplar/grupliste.php'> > Grup Listesine git <</a><br>";
	}
	if($formhata==0)
	{
		if(S($f_rehbergrupid)==0)
		{
			$f_benzersizid=SifreUret(20,2);

			$sutunlar=$sutunlar.",rehbergrupbenzersizid";
			$degerler=$degerler."|*_".$f_benzersizid;

			ekle($sutunlar,$degerler,$tablo,35);

			$f_rehbergrupid = teksatir(" Select rehbergrupid from rehbergrup Where rehbergrupbenzersizid='". $f_benzersizid ."'","rehbergrupid");
		}
		else
		{
			guncelle($sutunlar,$degerler,$tablo," rehbergrupid='". $f_rehbergrupid ."' ",35);
		}
	}
}
if(S(q("rehbergrupid"))!=0)
{
	if(dogrula("rehbergrup","rehbergrupid='". q("rehbergrupid") ."'"))
	{
		$butonisim="GÜNCELLE";
		$f_rehbergrupid=q("rehbergrupid");
		$f_rehbergrupad=teksatir("Select rehbergrupad From rehbergrup Where rehbergrupid='". q("rehbergrupid") ."'","rehbergrupad");
		$f_rehbergrupaciklama=teksatir("Select rehbergrupaciklama From rehbergrup Where rehbergrupid='". q("rehbergrupid") ."'","rehbergrupaciklama");
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
							<li class="active"><a href="/_y/s/s/gruplar/grupliste.php" class="btn ink-reaction btn-raised btn-primary">grup Liste</a></li>
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
										<input type="hidden" name="rehbergrupekle" value="1">
										<input type="hidden" name="rehbergrupid" value="<?=$f_rehbergrupid?>">
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
																name="rehbergrupad" 
																id="rehbergrupad" 
																value="<?=$f_rehbergrupad?>"
																placeholder="Grup Adını Yazın" required aria-required="true" >
																<label for="rehbergrupad">Grup Adını Yazın</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="rehbergrupaciklama" 
																	id="rehbergrupaciklama" 
																	class="form-control" 
																	rows="4" 
																	placeholder
																	style="
																		background-color:#efefef; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=ltrim($f_rehbergrupaciklama)?></textarea>
																<label for="grupaciklama">Grup Açıklama</label>
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
			$("#rehbergrupeklephp").addClass("active");

			$('#demo-date-format').datepicker({
			    format: 'yyyy-mm-dd',
			    language: 'tr'	
			});
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>