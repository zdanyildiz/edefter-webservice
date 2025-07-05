<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Kargolar";
$formbaslik="Kargo";
$butonisim="EKLE";

$f_kargoid=f("kargoid");
$f_kargoad=f("kargoad");
$f_kargoaciklama=f("kargoaciklama");
$f_kargoresim=f("kargoresim");
$f_kargoCode=f("kargoCode");
$f_kargotakiplink=f("kargotakiplink");

if(S(f("kargoekle"))==1 && !BosMu($f_kargoad))
{
	$sutunlar="kargoad,
		kargoaciklama,
		kargoresim,
		kargotakiplink,
		kargoCode,
		kargosil";

		$degerler=$f_kargoad."|*_".
		$f_kargoaciklama."|*_".
		$f_kargoresim."|*_".
		$f_kargotakiplink."|*_".
        $f_kargoCode."|*_".
        "0";
	$tablo="kargo";

	if(dogrula("kargo","kargoad='". $f_kargoad ."'") && S($f_kargoid)==0)
	{
		$formhata=1;
		$formhataaciklama="DİKKAT: Bu isimde ( $f_kargoad ) zaten grup var. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/kargolar/kargoliste.php'> > Kargo Listesine git <</a><br>";
	}
	if($formhata==0)
	{
		if(S($f_kargoid)==0)
		{
			$f_benzersizid=SifreUret(20,2);
			ekle($sutunlar.",benzersizid",$degerler."|*_".$f_benzersizid,$tablo,35);
			$f_kargoid = teksatir(" Select kargoid from kargo Where benzersizid='". $f_benzersizid ."'","kargoid");
		}
		else
		{
			guncelle($sutunlar,$degerler,$tablo," kargoid='". $f_kargoid ."' ",35);
		}
	}
}
if(S(q("kargoid"))!=0)
{
	if(dogrula("kargo","kargoid='". q("kargoid") ."'"))
	{
		$butonisim="GÜNCELLE";
		$f_kargoid=q("kargoid");
		$f_kargoad=teksatir("Select kargoad From kargo Where kargoid='". q("kargoid") ."'","kargoad");
		$f_kargoaciklama=teksatir("Select kargoaciklama From kargo Where kargoid='". q("kargoid") ."'","kargoaciklama");
		$f_kargoresim=teksatir("Select kargoresim From kargo Where kargoid='". q("kargoid") ."'","kargoresim");
		$f_kargoCode=teksatir("Select kargoCode From kargo Where kargoid='". q("kargoid") ."'","kargoCode");
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
							<li class="active"><a href="/_y/s/s/gruplar/grupliste.php" class="btn ink-reaction btn-raised btn-primary">Kargo Liste</a></li>
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
										<input type="hidden" name="kargoekle" value="1">
										<input type="hidden" name="kargoid" value="<?=$f_kargoid?>">
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
																name="kargoad" 
																id="kargoad" 
																aria-invalid="false"
																required aria-required="true"
																value="<?=$f_kargoad?>"
																placeholder="Kargo Adını Yazın" required aria-required="true" >
																<label for="kargoad">Kargo Adını Yazın</label>
															</div>
														</div>
													</div>
                                                    <div class="row">
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input
																type="text"
																class="form-control"
																name="kargoCode"
																id="kargoCode"
																aria-invalid="false"
																required aria-required="true"
																value="<?=$f_kargoCode?>"
																placeholder="Kargo Kodu Yazın" required aria-required="true" >
																<label for="kargoCode">Kargo Kodu Yazın</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-6">
															<div class="form-group floating-label">
																<textarea 
																	name="kargoaciklama" 
																	id="kargoaciklama" 
																	class="form-control" 
																	rows="4" 
																	placeholder
																	style="
																		background-color:#efefef; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=ltrim($f_kargoaciklama)?></textarea>
																<label for="kargoaciklama">Kargo Açıklama</label>
															</div>
														</div>
														<div class="col-sm-6">
															<div class="form-group floating-label">
																<textarea 
																	name="kargoatakiplink" 
																	id="kargotakiplink" 
																	class="form-control" 
																	rows="4" 
																	placeholder
																	style="
																		background-color:#efefef; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=ltrim($f_kargotakiplink)?></textarea>
																<label for="kargotakiplink">Kargo Takip Sayfası</label>
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
			$("#kargoeklephp").addClass("active");

			$('#demo-date-format').datepicker({
			    format: 'yyyy-mm-dd',
			    language: 'tr'	
			});
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>