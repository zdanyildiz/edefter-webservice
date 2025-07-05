<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$f_ssl=f("ssl");
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="SSL Düzenle";
$formbaslik="SSL DURUMU";
$butonisim="GÜNCELLE";
$sslbilgileri_d=0;
$sslbilgileri_s="
	SELECT
		ssldurum
	FROM
		ayargenel
	WHERE
		genelayarid='1'
";
if($data->query($sslbilgileri_s))
{
	$sslbilgileri_v=$data->query($sslbilgileri_s);
	if($sslbilgileri_v->num_rows>0)$sslbilgileri_d=1;
	if($sslbilgileri_d==1)
	{
		while($sslbilgileri_t = $sslbilgileri_v->fetch_assoc())
		{
			$f_ssl=$sslbilgileri_t["ssldurum"];
		}
		unset($sslbilgileri_t);
	}
	unset($sslbilgileri_d);
}

$data->close(); unset($sslbilgileri_s);


if(S(f("sslekle"))==1 && !BosMu(f("ssl")))
{
	$f_ssl=f("ssl");
	Veri(true);
	
	$sslekle_s= "
		UPDATE
			ayargenel
		SET
			ssldurum	='". $f_ssl ."'
		WHERE 
			genelayarid='1'
	";
	$eylem=3;$formad="SSL Güncelle";
	
	
	if($formhata==0)
	{
		if($data->query($sslekle_s))
		{
			yoneticiislemleri(2,$eylem);
			$formhataaciklama="SSL Durum güncellendi";
		}
		else
		{
			hatalogisle("sslekle",$data->error);
			$formhata=1;
			$formhataaciklama="ssl eklenemedi";
		}
		$data->close(); unset($sslekle_s);
	}
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sistem Panel - SSL Ekle</title>

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
							<li><a href="#">Genel Ayarlar</a></li>
							<li class="active"><?=$sayfabaslik?></li>
						</ol>
					</div>
					<?php if($formhata==0 && S(f("sslekle"))==1){ ?>
					<div class="alert alert-success" role="alert">
						<?=$formhataaciklama?>
					</div>
					<?php }elseif($formhata==1 && S(f("sslekle"))==1) { ?>
					<div class="alert alert-danger" role="alert">
						<?=$formhataaciklama?>
					</div>
					<?php }elseif(S(q("klt"))==1) { ?>
					<div class="alert alert-warning" role="alert">
						Kilit Ekranını kullanabilmek için rakamlardan oluşan 4 haneli bir pin kodu oluşturmalısınız!
					</div>
					<?php } ?>
					<div class="section-body contain-lg">
						<div class="row">

							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
									</div>
									<form class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="sslekle" value="1">
										<div class="col-sm-9">
											<label class="radio-inline radio-styled">
												<input type="radio" name="ssl" value="0" <?php if(S($f_ssl)==0) echo "checked"; ?>><span>YOK</span>
											</label>
											<label class="radio-inline radio-styled">
												<input type="radio" name="ssl" value="1" <?php if(S($f_ssl)==1) echo "checked"; ?>><span>VAR</span>
											</label>
										</div>
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
			$("#ssleklephp").addClass("active");
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>
