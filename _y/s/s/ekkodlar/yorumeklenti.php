<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Ek Kodlar";
$formbaslik="Yorum Kodları";
$butonisim="GÜNCELLE";

$f_ayaryorumid=f("ayaryorumid");
$f_dilid=S(f("dilid"));
$f_yorumkod=f("yorumkod");

if(S(f("yorum"))==1)
{
	$f_benzersizid=sifreuret(20,3);
	$sutunlar="DilID,yorum";
	$degerler=$f_dilid."|*_".$f_yorumkod;
	$tablo="ayaryorum";

	if(S($f_ayaryorumid)==0)
	{
		ekle($sutunlar.",benzersizid",$degerler."|*_".$f_benzersizid,$tablo,9);
		$f_ayaryorumid = teksatir(" Select ayaryorumid from ayaryorum Where benzersizid='". $f_benzersizid ."'","ayaryorumid");
	}
	else
	{
		guncelle($sutunlar,$degerler,$tablo," ayaryorumid='". $f_ayaryorumid ."' ",9);
	}
}
if(S(q("ayaryorumid"))!=0)
{
	if(dogrula("ayaryorum","ayaryorumid='". q("ayaryorumid") ."'"))
	{
		$f_ayaryorumid=q("ayaryorumid");
		$f_dilid=teksatir("Select DilID From ayaryorum Where ayaryorumid='". q("ayaryorumid") ."'","DilID");
		$f_yorumkod=teksatir("Select yorum From ayaryorum Where ayaryorumid='". q("ayaryorumid") ."'","yorum");
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
							<li class="active"><a href="/_y/s/s/ekkodlar/yorumeklentiliste.php" class="btn ink-reaction btn-raised btn-primary">Yorum Kodları Liste</a></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head">
										<header><?=$formbaslik?></header>
									</div>
									<form name="formyorum" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="yorum" value="1">
										<input type="hidden" name="ayaryorumid" value="<?=$f_ayaryorumid?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body style-primary form-inverse">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">
														<div class="form-group">
															<select id="dilid" name="dilid" class="form-control">
															<?php
															if(!isset($data))Veri(true);
															$dil_d=0; $dil_v=""; $dil_s="";
															$dil_s="SELECT dilid,dilad,dilkisa FROM dil Where dilsil='0' and dilaktif='1'";
															$dil_v=$data->query($dil_s);
															if($dil_v->num_rows>0)$dil_d=1;
															unset($dil_s);
															if($dil_d==1)
															{
																while($dil_t=$dil_v->fetch_assoc())
																{
																	$l_dilid=$dil_t["dilid"];
																	$l_dAd=$dil_t["dilad"];
																	$l_dKisa=$dil_t["dilkisa"];
															?>
																	<option value="<?=$l_dilid?>" <?php if($l_dilid==$f_dilid)echo "selected"; ?> ><?=$l_dAd?> (<?=$l_dKisa?>)</option>
															<?php
																}
																unset($dil_t,$dil_v);
															}
															unset($dil_v);
															?>
															</select>
															<label for="Age2">AYAR İÇİN DİL SEÇİN</label>
															<p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
														</div>
													</div>
													<div class="row">
														<div class="form-group floating-label">
															<textarea name="yorumkod" id="yorumkod" class="form-control" rows="2" placeholder="yorum kodunu yapıştırın"><?=$f_yorumkod?></textarea>
															<label for="yorum">yorum</label>
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

		<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
		<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
		<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
		<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
		<script src="/_y/assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
		<script src="/_y/assets/js/libs/moment/moment.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-rating/bootstrap-rating-input.min.js"></script>
		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

		<script src="/_y/assets/js/libs/toastr/toastr.js"></script>
		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		<script>
			$("#yorumeklentiphp").addClass("active");
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>