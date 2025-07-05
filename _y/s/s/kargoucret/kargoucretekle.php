<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$butonisim="Kaydet";
//düzenle
$sayfabaslik="Kargo Ücreti Düzenle";
$formbaslik="Kargo Ücreti Düzenle";

$f_kargoucretid=f("kargoucretid");
$f_kargosabitucret=f("kargosabitucret");
$f_kargosuresi=f("kargosuresi");
$f_kargoucretsiz=f("kargoucretsiz");
$f_kargourunadet=S(f("kargourunadet"));
$f_kargokapidaek=f("kargokapidaek");

if(S(f("kargoekle"))==1)
{
    $sutunlar="kargosabitucret,
        kargosuresi,
		kargoucretsiz,
		kargourunadet,
		kargokapidaek";

    $degerler=$f_kargosabitucret."|*_".
        $f_kargosuresi."|*_".
        $f_kargoucretsiz."|*_".
        $f_kargourunadet."|*_".
        $f_kargokapidaek;

    $tablo="kargoucret";

    $urunsutunlar="urunsabitkargoucreti,urunkargosuresi";
    $urundeger=$f_kargosabitucret."|*_".$f_kargosuresi;
    $uruntablo="urunozellikleri";

    if($formhata==0)
    {
        guncelle($sutunlar,$degerler,$tablo," kargoucretid='". $f_kargoucretid ."' ",35);
        guncelle($urunsutunlar,$urundeger,$uruntablo,"1",35);
    }
}
if (S(q("kargoucretid")) != 0)
{
    if (dogrula("kargoucret", "kargoucretid='" . q("kargoucretid") . "'"))
    {
        $butonisim = "GÜNCELLE";
        $f_kargoucretid = q("kargoucretid");
        $f_kargosabitucret = teksatir("Select kargosabitucret From kargoucret Where kargoucretid='" . q("kargoucretid") . "'", "kargosabitucret");
        $f_kargosuresi = teksatir("Select kargosuresi From kargoucret Where kargoucretid='" . q("kargoucretid") . "'", "kargosuresi");
        $f_kargoucretsiz = teksatir("Select kargoucretsiz From kargoucret Where kargoucretid='" . q("kargoucretid") . "'", "kargoucretsiz");
        $f_kargourunadet = teksatir("Select kargourunadet From kargoucret Where kargoucretid='" . q("kargoucretid") . "'", "kargourunadet");
        $f_kargokapidaek = teksatir("Select kargokapidaek From kargoucret Where kargoucretid='" . q("kargoucretid") . "'","kargokapidaek");
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
										<input type="hidden" name="kargoucretid" value="<?=$f_kargoucretid?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">

														<div class="col-sm-3">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="kargosabitucret"
																id="kargosabitucret"
																aria-invalid="false"
																required aria-required="true"
																value="<?=$f_kargosabitucret?>"
																placeholder="Kargo Sabit Ücretini Yazın" required aria-required="true" >
																<label for="kargosabitucret">Kargo Sabit Ücretini Yazın</label>
															</div>
														</div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="kargosuresi"
                                                                        id="kargosuresi"
                                                                        aria-invalid="false"
                                                                        required aria-required="true"
                                                                        value="<?=$f_kargosuresi?>"
                                                                        placeholder="Kargo Süresi" required aria-required="true" >
                                                                <label for="kargosuresi">Kargo Süresi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="kargoucretsiz"
                                                                        id="kargoucretsiz"
                                                                        aria-invalid="false"
                                                                        required aria-required="true"
                                                                        value="<?=$f_kargoucretsiz?>"
                                                                        placeholder="X TL Üzeri Ücretsiz Kargo" required aria-required="true" >
                                                                <label for="kargoucretsiz">X TL Üzeri Ücretsiz Kargo</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="kargokapidaek"
                                                                        id="kargokapidaek"
                                                                        aria-invalid="false"
                                                                        required aria-required="true"
                                                                        value="<?=$f_kargokapidaek?>"
                                                                        placeholder="Kargo Kapida Ek Ücret" required aria-required="true" >
                                                                <label for="kargokapidaek">Kargo Kapida Ek Ücret</label>
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
			$("#kargoucretphp").addClass("active");

			$('#demo-date-format').datepicker({
			    format: 'yyyy-mm-dd',
			    language: 'tr'	
			});
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>