<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Bedenler";
$formbaslik="Ürün Bedenleri";
$butonisim="EKLE";

$f_urunbedengrupid=f("urunbedengrupid");
$f_urunbedenid=f("urunbedenid");
$f_urunbedenad=f("urunbedenad");
$f_urunbedensira=S(f("urunbedensira"));

if(S(f("urunbedenekle"))==1 && !BosMu($f_urunbedenad))
{
	$sutunlar="urunbedengrupid,urunbedenad,urunbedensira,urunbedensil";
	$degerler=$f_urunbedengrupid."|*_".$f_urunbedenad."|*_".$f_urunbedensira."|*_"."0";
	$tablo="urunbeden";

	if(S($f_urunbedengrupid)==0)
	{
		$formhata=1;
		$formhataaciklama="DİKKAT: Bir beden grubu seçmelisiniz<br><br>";
	}
	elseif(dogrula("urunbeden","urunbedenad='". $f_urunbedenad ."' and urunbedengrupid='". $f_urunbedengrupid ."' and urunbedensil='0'") && S($f_urunbedenid)==0)
	{
		$formhata=1;
		$formhataaciklama="DİKKAT: Bu isimde ( $f_urunbedenad ) zaten beden var. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/varyasyonlar/bedenliste.php'> > Beden Listesine git <</a><br>";
	}
	if($formhata==0)
	{
		if(S($f_urunbedenid)==0)
		{
			ekle($sutunlar,$degerler,$tablo,35);
			$f_urunbedenid=teksatir("SELECT urunbedenid FROM urunbeden WHERE urunbedengrupid='".$f_urunbedengrupid."' and urunbedenad='".$f_urunbedenad."'","urunbedenid");
		}
		else
		{
			guncelle($sutunlar,$degerler,$tablo," urunbedenid='". $f_urunbedenid ."' ",35);
		}
	}
}
if(S(q("urunbedenid"))!=0)
{
	if(dogrula("urunbeden","urunbedenid='". q("urunbedenid") ."'"))
	{
		$butonisim="GÜNCELLE";
		$f_urunbedenid=q("urunbedenid");
		$f_urunbedenad=teksatir("Select urunbedenad From urunbeden Where urunbedenid='". q("urunbedenid") ."'","urunbedenad");
		$f_urunbedensira=teksatir("Select urunbedensira From urunbeden Where urunbedenid='". q("urunbedenid") ."'","urunbedensira");
		$f_urunbedengrupid=teksatir("Select urunbedengrupid From urunbeden Where urunbedenid='". q("urunbedenid") ."'","urunbedengrupid");
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
							<li class="active"><a href="/_y/s/s/varyasyonlar/bedenliste.php" class="btn ink-reaction btn-raised btn-primary">Beden Liste</a></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary form-inverse">
										<header><?=$formbaslik?></header>
                                        <div class="tools">
                                            <a href="/_y/s/s/varyasyonlar/bedenekle.php?bedengrupid=<?=$f_urunbedengrupid?>" id="yenikutu" class="btn btn-floating-action btn-default-light"><i class="fa fa-plus"></i></a></div>
									</div>
									<form name="formanaliz" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="urunbedenekle" value="1">
										<input type="hidden" name="urunbedenid" value="<?=$f_urunbedenid?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">
														<div class="col-sm-6">
															<select id="urunbedengrupid" name="urunbedengrupid" class="form-control">
																<option value="0">Beden Grubu Seçin</option>
																<?php
																if(!isset($data))Veri(true);
																	$urungrup_d=0; $urungrup_v=""; $urungrup_s="";
																	$urungrup_s="
																		SELECT 
																			urunbedengrupid,urunbedengrupad 
																		FROM 
																			urunbedengrup 
																		Where 
																			urunbedengrupsil='0' 
																	";
																	$urungrup_v=$data->query($urungrup_s);
																	if($urungrup_v -> num_rows > 0) $urungrup_d=1;
																	unset($urungrup_s);
																	if($urungrup_d==1)
																	{
																		while($urungrup_t=$urungrup_v->fetch_assoc())
																		{
																			$l_urunbedengrupid = $urungrup_t["urunbedengrupid"];
																			$l_urunbedengrupad = $urungrup_t["urunbedengrupad"];
																			?>
																			<option value="<?=$l_urunbedengrupid?>" <?php if(S($l_urunbedengrupid)==S($f_urunbedengrupid)||($l_urunbedengrupid==S(q("bedengrupid")))) echo "selected"; ?> >
																				<?=$l_urunbedengrupad?>
																			</option>
																			<?php
																		}
																		unset($urungrup_t,$urungrup_v);
																	}
																	unset($urungrup_v);
																?>
															</select>
															<label for="urunbedengrupid">Beden Grubu Seçin</label>
														</div>
													</div>
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="urunbedenad" 
																id="urunbedenad" 
																value="<?=$f_urunbedenad?>"
																placeholder="Ürün Bedeni Yazın" required aria-required="true" >
																<label for="urunbedenad">Ürün Bedeni Yazın</label>
															</div>
														</div>
													</div>
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="urunbedensira" 
																id="urunbedensira" 
																value="<?=$f_urunbedensira?>"
																placeholder="Ürün Beden Sırası" required aria-required="true" >
																<label for="urunbedenad">Ürün Beden Sırası</label>
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
			$("#bedeneklephp").addClass("active");
            $(document).ready(function(){
                $(document).on("change","#urunbedengrupid",function(){
                    $id=$("#urunbedengrupid").val();
                    $("#yenikutu").attr("href","/_y/s/s/varyasyonlar/bedenekle.php?bedengrupid="+$id);
                });
            });
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>