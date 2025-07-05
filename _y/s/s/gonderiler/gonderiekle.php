<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Gönderi Oluştur";
$formbaslik="Gönderi Oluşturma Sayfası";
$butonisim="EKLE";

$f_gonderiid=f("gonderiid");
$f_gonderibaslik=f("gonderibaslik");
$f_gonderiicerik=f("gonderiicerik");
$f_gonderitur=S(f("gonderitur"));
$f_epostalar=f("epostalar");
$f_gruplar=f("gruplar");
if(S(f("gonderiekle"))==1 && !BosMu($f_gonderibaslik))
{
	$simdi=date("Y-m-d H:i:s");
	$sutunlar="gonderiguncellemetarihi,
		gonderibaslik,
		gonderitur,
		gonderiicerik,
		gonderisil";

	$degerler=$simdi."|*_".
		$f_gonderibaslik."|*_".
		$f_gonderitur."|*_".
		$f_gonderiicerik."|*_".
		"0";
	$tablo="gonderi";

	
	if($formhata==0)
	{
		if(S($f_gonderiid)==0)
		{
			$f_benzersizid=SifreUret(20,2);
			$sutunlar=$sutunlar.",gonderiolusturmatarihi,gonderibenzersizid";
			$degerler=$degerler."|*_".$simdi."|*_".$f_benzersizid;

			ekle($sutunlar,$degerler,$tablo,35);

			$f_gonderiid = teksatir(" Select gonderiid from rehbergrup Where rehbergrupbenzersizid='". $f_benzersizid ."'","rehbergrupid");
		}
		else
		{
			guncelle($sutunlar,$degerler,$tablo," rehbergrupid='". $f_gonderiid ."' ",35);
		}
	}
}
elseif(S(f("gonderiekle"))==1 && BosMu($f_gonderibaslik))
{
	$formhata=1;
	$formhataaciklama="Gönderi Başlığı Boş olamaz";
}
if(S(q("gonderiid"))!=0)
{
	if(dogrula("gonderi","gonderiid='". q("gonderiid") ."'"))
	{
		$butonisim="GÜNCELLE";
		$f_gonderiid=q("gonderiid");
		$gonderigetir=coksatir("Select * From gonderi Where gonderiid='". q("gonderiid") ."'");
		if(!BosMu($gonderigetir_v))
		{
			$f_gonderibaslik=$gonderigetir["gonderibaslik"];
			$f_gonderitur=$gonderigetir["gonderitur"];
			$f_gonderiicerik=$gonderigetir["gonderiicerik"];
		}
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
							<li class="active"><a href="/_y/s/s/gonderiler/gonderiliste.php" class="btn ink-reaction btn-raised btn-primary">Gönderi Liste</a></li>
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
										<input type="hidden" name="gonderiekle" value="1">
										<input type="hidden" name="gonderid" value="<?=$f_gonderiid?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">															
														<div class="col-sm-8">
															<div class="form-group floating-label">
															<select class="form-control static dirty">
																<option value="0" <?php if (S($f_gonderitur)==0){echo "selected";}?>>Eposta Gönderisi</option>
																<option value="1" <?php if (S($f_gonderitur)==1){echo "selected";}?>>SMS Gönderisi</option>
															</select>
															</div>
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="gonderibaslik" 
																id="gonderibaslik" 
																value="<?=$f_gonderibaslik?>"
																placeholder="Gönderi Başlığı Giriniz" required aria-required="true" >
																<label for="gonderibaslik">Gönderi Başlığı Giriniz</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-8">
															<div class="form-group floating-label">
																<textarea id="ckeditor" name="gonderiicerik" rows="40" style="height: 400px"><?=$f_gonderiicerik?></textarea>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-8">
															<div class="form-group floating-label">
																<textarea id="epostalar" name="epostalar" rows="10" style="width:100%;height:100px"><?=$f_epostalar?></textarea>
															</div>
														</div>
													</div>
													<div class="row">
														<?php 
															$rehber_grup_s="
																SELECT 
																	rehbergrupid,rehbergrupad
																FROM 
																	rehbergrup
																WHERE 
																	rehbergrupsil='0'
															";
															if($data->query($rehber_grup_s))
															{
																$rehber_grup_v=$data->query($rehber_grup_s);unset($rehber_grup_s);
																if($rehber_grup_v->num_rows>0)
																{
																	while($rehber_grup_t=$rehber_grup_v->fetch_assoc())
																	{
																		$rehbergrupad=$rehber_grup_t["rehbergrupad"];
																		$rehbergrupid=$rehber_grup_t["rehbergrupid"];
																		echo '
																			<div class="col-sm-4">
																				<label class="checkbox-inline checkbox-styled">
																					<input type="checkbox" name="gruplar" value="'. $rehbergrupid .'">
																					<span>'. $rehbergrupad .'</span>
																				</label>
																			</div>
																		';
																	}unset($rehbergrupad,$rehbergrupid,$rehber_grup_t);
																}unset($rehber_grup_v);
															}
															else
															{
																hatalogisle("Rehber Grup Liste",$data->error);
															}
														?>
													</div>
													<div id="divc"></div>
												</div>
											</div>
										</div>
										<div class="card-actionbar col-sm-4">
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

		<script src="/_y/assets/js/libs/ckeditor/ckeditor.js"></script>
		<script src="/_y/assets/js/libs/ckeditor/adapters/jquery.js"></script>
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
		
		<script src="/_y/assets/js/libs/ckeditor/ckeditor.js"></script>
		<script src="/_y/assets/js/libs/ckeditor/adapters/jquery.js"></script>
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormEditors.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		<script src="/_y/assets/js/panel/resim-dosya-video.js?v=006"></script>
		
		<script>
			$("#gonderieklephp").addClass("active");

			$('#demo-date-format').datepicker({
			    format: 'yyyy-mm-dd',
			    language: 'tr'	
			});
		</script>
		<script>
			var countChecked = function()
			{
			  var n = $( "input:checked" ).length;
			  $cVal=$(this).val();
			  $( "#divc" ).text( n + (n === 1 ? " is" : " are") + " ("+ $cVal +") checked!" );
			};
			countChecked();
			 
			$( "input[type=checkbox]" ).on( "click", countChecked );
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>