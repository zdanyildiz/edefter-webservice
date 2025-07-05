<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$f_resimid="";$f_resimidler="";$f_resimadlar="";
$butonisim=" EKLE ";
$formhataaciklama=q("formhataaciklama");
$f_resimgaleriid=f("resimgaleriid");
if(S(f("dilid"))!=0){$f_dilid=f("dilid");}else{$f_dilid=$dilid;}
$f_resimgaleritariholustur=f("resimgaleritariholustur");
$f_resimgaleritarihguncel=f("resimgaleritarihguncel");
$f_resimgaleriad=f("resimgaleriad");
$f_resimgaleriaciklama=f("resimgaleriaciklama");
$f_resimgalerisil=S(f("resimgalerisil"));
$f_benzersizid=f("benzersizid");if(BosMu($f_benzersizid))$f_benzersizid=SifreUret(20,2);
$f_resimid=f("resimid");
$rad="Resim Adı";
$ekleresim="/_y/assets/img/avatar7.jpg";
$f_resimgaleriekle=f("resimgaleriekle");

if(S($f_resimgaleriekle)==1)
{
	Veri(true);
	$simdi=date("Y-m-d H:i:s");
	
	if(S($f_resimgaleriid)!=0)
	{
		$resimgaleriekle_s=
		"
			UPDATE
				resimgaleri
			SET
				benzersizid				= '". $f_benzersizid ."',
				resimgaleritarihguncel	= '". $simdi ."',
				resimgaleriad			= '". $f_resimgaleriad ."',
				resimgaleriaciklama		= '". $f_resimgaleriaciklama ."',
				resimgalerisil			= '0'
			WHERE 
				resimgaleriID			='".$f_resimgaleriid."'
		";
		$eylem=3;$formad="resimgaleri Güncelle";
	}
	else
	{
		$resimgaleriekle_s = "
			INSERT INTO resimgaleri 
			(
				benzersizid,
				resimgaleritariholustur,
				resimgaleritarihguncel,
				resimgaleriad,
				resimgaleriaciklama,
				resimgalerisil
			)
			VALUES 
			(
				'". $f_benzersizid ."',
				'". $simdi ."',
				'". $simdi ."',
				'". $f_resimgaleriad ."',
				'". $f_resimgaleriaciklama ."',
				'0'
			)
			";
		$eylem=1;$formad="Galeri Ekle";
	}
	if($formhata==0)
	{
		if($data->query($resimgaleriekle_s))
		{
			yoneticiislemleri(27,$eylem);
			if(S($f_resimgaleriid)!=0)
			{
				$formhataaciklama="Resim Galerisi Güncellendi";
				$data->query("DELETE FROM resimgaleriliste WHERE resimgaleriid='". $f_resimgaleriid ."'");
			}
			else
			{
				$formhataaciklama="Yeni Resim Galerisi Eklendi";
			}
			$f_resimgaleriid=teksatir(" Select resimgaleriid from resimgaleri Where benzersizid='". $f_benzersizid ."'","resimgaleriid");
			
			
			$seoresim="";
			if(!BosMu($f_resimid))
			{
				$f_resimid=rtrim($f_resimid,",");
				$f_resimler = explode(",", $f_resimid);
				foreach($f_resimler as $ekleresim)
				{
					ekle("resimgaleriid,resimid",$f_resimgaleriid."|*_".$ekleresim,"resimgaleriliste",27);
					$resim=teksatir(" Select resim from resim Where resimid='". $ekleresim ."'","resim");
					$resim="";
				}
			}
			
			header('Location: /_y/s/s/galeriler/AddGallery.php?formhataaciklama='.$formhataaciklama.'&resimgaleriid='. $f_resimgaleriid);
		}
		else
		{
			hatalogisle("resimgaleriekle",$data->error);
			$formhata=1;
			if(S($f_resimgaleriid)!=0) $formhataaciklama="resim galeri güncellenemedi"; else $formhataaciklama="Yeni resim galeri eklenemedi";
		}
		$data->close(); unset($resimgaleriekle_s);
	}
}
if(S(q("resimgaleriid"))!=0)
{
	Veri(true);
	$resimgaleribilgileri_d=0;
	$resimgaleribilgileri_s=
	"
		SELECT
			benzersizid,
			resimgaleriad,
			resimgaleriaciklama,
			resimgalerisil
		FROM
			resimgaleri
		WHERE
			resimgaleriid='".S(q("resimgaleriid"))."'
	";
	if($data->query($resimgaleribilgileri_s))
	{
		$resimgaleribilgileri_v=$data->query($resimgaleribilgileri_s);
		if($resimgaleribilgileri_v->num_rows>0)$resimgaleribilgileri_d=1;
		if($resimgaleribilgileri_d==1)
		{
			$butonisim=" GÜNCELLE ";
			while($resimgaleribilgileri_t = $resimgaleribilgileri_v->fetch_assoc())
			{
				$f_resimgaleriid 		=S(q("resimgaleriid"));
				$f_benzersizid			=$resimgaleribilgileri_t["benzersizid"];
				$f_resimgaleriad 		=$resimgaleribilgileri_t["resimgaleriad"];
				$f_resimgaleriaciklama 	=$resimgaleribilgileri_t["resimgaleriaciklama"];
				$f_resimgalerisil 		=$resimgaleribilgileri_t["resimgalerisil"];
				
				$formbaslik="Resim Galeri Bilgileri";
			}
			unset($resimgaleribilgileri_t);
		}
		unset($resimgaleribilgileri_d);
		$resimgaleriresimler_d=0;
		$resimgaleriresimler_s="
			Select 
				resimklasorad,resim.resim,resim.resimid,resimad 
			From 
				resimgaleriliste 
					inner join resim on 
						resim.resimid=resimgaleriliste.resimid 
					inner join resimklasor on 
						resimklasor.resimklasorid=resim.resimklasorid 
			where 
				resimgaleriliste.resimgaleriid='". $f_resimgaleriid ."'";
		$resimgaleriresimler_v=$data->query($resimgaleriresimler_s);
		if($resimgaleriresimler_v->num_rows>0)$resimgaleriresimler_d=1;
		
		if($resimgaleriresimler_d==1)
		{
			while($resimgaleriresimler_t = $resimgaleriresimler_v->fetch_assoc())
			{
				$resim=$resimgaleriresimler_t["resim"];
				$resimklasorad=$resimgaleriresimler_t["resimklasorad"];
				$resimid=$resimgaleriresimler_t["resimid"];
				$resimad=$resimgaleriresimler_t["resimad"];
				if(BosMu($f_resimid))$f_resimid="$resimklasorad/$resim";else$f_resimid="$f_resimid,$resimklasorad/$resim";
				if(BosMu($f_resimidler))$f_resimidler=$resimid;else$f_resimidler="$f_resimidler,$resimid";
				if(BosMu($f_resimadlar))$f_resimadlar=$resimad;else$f_resimadlar="$f_resimadlar||$resimad";
			}
		}
		unset($resimgaleriresimler_s);			
	}
}
?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>Material Admin - Form advanced</title>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

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

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once($anadizin."/_y/s/b/header.php");?>
		<div id="base">
			<div class="offcanvas">
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
								<form action="/_y/s/f/yukle.php" target="_islem" class="dropzone dz-clickable" id="myawesomedropzone">
									<div class="form-group">
										<input type="text" name="resimad" id="resimad" class="form-control" placeholder="" required="" data-rule-minlength="2" aria-required="true" aria-describedby="resimad-error" aria-invalid="true" value="<?=$f_resimgaleriad?>" style="height: 35px; line-height: 35px;display:none">
										<label for="resimad">Önce Galeri Adı Girin</label>
									</div>
									<div class="form-group">
										<input type="hidden" name="resimklasor" value="galeri">
										<div class="dz-message">
											<h3>Resmi Sürükleyin ve Bırakın veya Tıklayın.</h3>
											<em>En fazla <strong>50 (100MB)</strong> resim seçin</em>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="force-padding stick-bottom-right">
						<a class="btn btn-floating-action btn-default-dark" href="#offcanvas-demo-size3" data-toggle="offcanvas">
							<i class="md md-arrow-back"></i>
						</a>
					</div>
				</div>
				<!-- //sol popop-->
			</div>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Resim Galeri EKLE</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-12">
								<h1 class="text-primary">Sitenizin Resim Galeri Adını girin</h1>
							</div>
							<div class="col-lg-8">
								<article class="margin-bottom-xxl">
									<p class="lead">
										Örn: (Ana Galeri, Ürünler, Gezi).
									</p>
								</article>
							</div>
						</div>
						<form class="form" method="post">
							<input type="hidden" name="resimgaleriekle" id="resimgaleriekle" 	value="1">
							<input type="hidden" name="resimgaleriid" 	id="resimgaleriid" 	value="<?=$f_resimgaleriid?>">
							<input type="hidden" name="resimid" 		id="resimid" 		value="<?=$f_resimidler?>">
							<input type="hidden" name="benzersizid" 	id="benzersizid" 	value="<?=$f_benzersizid?>">
							<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Resim Galeri Temel Bilgiler</h4><p></p>
										<p>
											Galeri Adı Boş Olamaz
										</p>
										<br>
										<p>Galeri sayfasında görüntülenmek üzere bir açıklama ekleyebilirsiniz</p>
									</article>
								</div>
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div class="form-group">
												<input type="text" name="resimgaleriad" id="resimgaleriad" class="form-control" placeholder="Örn:Ürünler" value="<?=$f_resimgaleriad?>" aria-invalid="false" required="" aria-required="true">
												<label>Resim Galeri Adı</label>
											</div>
											<div class="form-group">
												<textarea 
													id="resimgaleriaciklama" 
													name="resimgaleriaciklama" 
													placeholder="Galeri konusunu ve ya içeriğini yazabilirsiniz"
													class="form-control"  
													rows="5"
													><?=$f_resimgaleriaciklama?></textarea>
													<label for="resimgaleriaciklama">Galeri Açıklama</label>
											</div>
										</div>
									</div>
									<em class="text-caption">Galeri Temel özellikleri seçin</em>
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Galeri Resimleri</h4>										
										<p>Galeride görüntülenmek üzere bir resim seçin </p>
									</article>
								</div>

								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-head style-primary">
											<hedaer> <span style="padding: 0 0 0 20px;font-size: 16px">Yeni Resim Yükle</span></hedaer>
											<div class="tools">
												<a id="yenikutu" class="btn btn-floating-action btn-default-light"><i class="fa fa-plus"></i></a>
											</div>
										</div>
										<div class="card-body" id="resimgovde">
											<?php
											if(!BosMu($f_resimid))
											{
												$f_resimid=rtrim($f_resimid,",");
												$f_resimler = explode(",", $f_resimid);
												$resimid = explode(",", $f_resimidler);
												$rad = explode("||", $f_resimadlar);
												$resimkutusay=0;
												//die($f_resimid."<br>".$f_resimidler);
												foreach($f_resimler as $index => $ekleresim)
												{
													$resimkutusay++;
												?>
													<div class="form-group floating-label" id="resimkutu<?=$resimkutusay?>">
														<div class="margin-bottom-xxl">
															<div class="pull-left width-3 clearfix hidden-xs" id="rkon">
																<img id="ryer" class="img-circle size-2" src="/m/r/<?=$ekleresim?>" alt="">
															</div>
															<h1 class="text-light no-margin" id="rad"><?=$rad[$index]?></h1>
															<h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5>
															<div class="hbox-column v-top col-md-1">
																<a 
																	class="btn btn-floating-action ink-reaction" 
																	id="sillink"
																	data-resimkutu="resimkutu<?=$resimkutusay?>" 
																	data-id="<?=$resimid[$index]?>"
																	data-toggle="modal" 
																	data-target="#simpleModal" 
																	title="sil">
																	<i class="fa fa-trash"></i>
																</a>
																&nbsp;&nbsp;&nbsp;
																<a 
																	class="btn btn-floating-action ink-reaction"
																	id="hazirekle"
																	data-resimkutu="resimkutu<?=$resimkutusay?>" 
																	data-id="<?=$resimid[$index]?>" 
																	href="#offcanvas-search" 
																	data-toggle="offcanvas"
																	title="seç">
																	<i class="fa fa-file-image-o"></i></a>
																&nbsp;&nbsp;&nbsp;
																<a 
																	class="btn btn-floating-action ink-reaction" 
																	href="#offcanvas-left" 
																	id="yeniekle"
																	data-id="<?=$resimid[$index]?>" 
																	data-toggle="offcanvas" 
																	title="ekle">
																	<i class="fa fa-plus"></i></a>
															</div>
														</div>
													</div>
											<?php
												}
											}else{?>
												<div class="form-group floating-label" id="resimkutu1">
													<div class="margin-bottom-xxl">
														<div class="pull-left width-3 clearfix hidden-xs" id="rkon">
															<img id="ryer" class="img-circle size-2" src="<?=$ekleresim?>" alt="">
														</div>
														<h1 class="text-light no-margin" id="rad"><?=$rad?></h1>
														<h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5>
														<div class="hbox-column v-top col-md-1">
															<a 
																class="btn btn-floating-action ink-reaction" 
																id="sillink" 
																data-id="0"
																data-resimkutu="resimkutu1"
																data-toggle="modal" 
																data-target="#simpleModal" 
																title="sil">
																<i class="fa fa-trash"></i>
															</a>
															&nbsp;&nbsp;&nbsp;
															<a 
																class="btn btn-floating-action ink-reaction" 
																href="#offcanvas-search" 
																id="hazirekle"
																data-resimkutu="resimkutu1"
																data-toggle="offcanvas" 
																title="seç">
																<i class="fa fa-file-image-o"></i></a>
															&nbsp;&nbsp;&nbsp;
															<a 
																class="btn btn-floating-action ink-reaction" 
																href="#offcanvas-left" 
																id="yeniekle"
																data-resimkutu="resimkutu1"
																data-toggle="offcanvas" 
																title="ekle">
																<i class="fa fa-plus"></i></a>
														</div>
													</div>
												</div>	
											<?php }
											?>
											<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="btn-popup-sil-kapat"×</button>
															<h4 class="modal-title" id="simpleModalLabel">Resmi Sil</h4>
														</div>
														<div class="modal-body">
															<p>Resmi silmek istediğinize emin misiniz?</p>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
															<button type="button" class="btn btn-primary" id="silbutton">Resmi Sil</button>
														</div>
													</div>
												</div>
											</div>

											<div class="modal fade in" id="textModal" tabindex="-1" role="dialog" aria-labelledby="textModalLabel" aria-hidden="false"><div class="modal-backdrop fade in" style="height: 1019px;"></div>
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h4 class="modal-title" id="textModalLabel">Resim Yükleme</h4>
														</div>
														<div class="modal-body">
															<p>RESİM YÜKLENDİ</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<em class="text-caption">Görünüm özellikleri seçin</em>
								</div>
							</div>
							<div class="card-actionbar">
								<div class="card-actionbar-row">
									<button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
								</div>
							</div>
						</form>
					</div>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
			<?php require_once($anadizin."/_y/s/b/sagpopup.php");?>
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
		
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		<!-- END JAVASCRIPT -->
		<script>
			$("#galerieklephp").addClass("active");
		</script>
		<script type="text/javascript">
			$resimkutu=0;
			$resimustekle=0;
			$yeniresimadi="";
			$kutular=new Array();
			$kutular=["resimkutu1","resimkutu2","resimkutu3","resimkutu4","resimkutu5","resimkutu6","resimkutu7","resimkutu8","resimkutu9","resimkutu10","resimkutu11","resimkutu12","resimkutu13","resimkutu14","resimkutu15","resimkutu16","resimkutu17","resimkutu18","resimkutu19","resimkutu20","resimkutu21","resimkutu22","resimkutu23","resimkutu24","resimkutu25","resimkutu26","resimkutu27","resimkutu28","resimkutu29","resimkutu30","resimkutu31","resimkutu32","resimkutu33","resimkutu34","resimkutu35","resimkutu36","resimkutu37","resimkutu38","resimkutu39","resimkutu40","resimkutu41","resimkutu42","resimkutu43","resimkutu44","resimkutu45","resimkutu46","resimkutu47","resimkutu48","resimkutu49","resimkutu50"];
			function cokluresim($cid,$cadi)
			{
	            jQuery.each( $kutular, function( i, val )
				{
					if($( "#" + val ).length)
				  	{
				  		//resimkutu var
				  		//resim atanmış mı? id-ye bak
				  		
				  		$dataid=$( "#" + val +" #sillink" ).data("id");
						if(parseInt($dataid)==0)
				  		{
				  			
				  			if($("#resimid").val()=="")
				            {
								$("#resimid").val($cid);
				            }
				            else
				            {
				            	$("#resimid").val($("#resimid").val()+","+$cid);
				            }

				  			$("#"+ val +" #sillink").attr("data-id",$cid);

				            d=$.now();  $resim="/m/r/"+$cadi+"?"+d;
				            $("#"+ val +" #ryer").attr("src",$resim);

				            if($yeniresimadi=="")$yeniresimadi=$("#resimad").val();
				            $("#"+ val +" #rad").text($yeniresimadi);

				            $kutular.splice($kutular.indexOf(val),1);
				            $( "#yenikutu" ).click();
				           	//$yeniid=i+10;
				            $("#"+ val +" #sillink").attr("data-resimkutu",val+i);
				            $("#"+ val +" #hazirekle").attr("data-resimkutu",val+i);
				            $("#"+ val +" #yeniekle").attr("data-resimkutu",val+i);
				            $( "#" + val ).attr("id", val+i);
				            
				           // $( "#" + val ).attr("data", val+i);
				            return false;
				  		}
				  	}			
				});
			}
			Dropzone.autoProcessQueue= true;
			Dropzone.options.myawesomedropzone =
			{
				parallelUploads: 50,
				autoProcessQueue: true,
				addRemoveLinks: true,
				maxFiles: 50,
				maxFilesize: 100,
				dictDefaultMessage: "Dosyaları yüklemek için bırakın",
				dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
				dictFallbackText: "Dosyalarınızı eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
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
			            $resimadi=responseText.replace('"', '');
			            $resimadi=$resimadi.replace('"', '');
			            $resimadi=$resimadi.replace("\\", '');
			            
			            res = $resimadi.split("|");
			            $resimadi=res[0];
			            $resimid=res[1];
			            $ren=res[2];
			            $rboy=res[3];
			            //alert($resimadi+"*"+$resimid+"*"+$ren+"*"+$rboy);
			            cokluresim($resimid,$resimadi);

			            if($("#textModal").css('display') == 'none')
			            {
							
							$("#resimyuklepencerekapat").click();
							$("#solcanvas").click();

							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Başarılı");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
							this.removeAllFiles();
							//$("#resimad").val("");
						}
			        });
			        this.on("addedfile", function(file)
			        {
			        	$("#resimad").val($("#resimgaleriad").val());
			        	if($("#resimad").val().length<1)
			        	{
			        		if($("#textModal").css('display') == 'none')
			        		{
								$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Adı Girin (En az 3 harf)");
								$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
								$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
				        		$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
				        		$("#resimok").click();
				        		this.removeFile(file);
			        		}
			        	}
					});
			    }
			};		
			$( "#yenikutu" ).live( "click",function()
			{
				$kutudurum=0;
				$kutular=new Array();
				$kutular=["resimkutu1","resimkutu2","resimkutu3","resimkutu4","resimkutu5","resimkutu6","resimkutu7","resimkutu8","resimkutu9","resimkutu10","resimkutu11","resimkutu12","resimkutu13","resimkutu14","resimkutu15","resimkutu16","resimkutu17","resimkutu18","resimkutu19","resimkutu20","resimkutu21","resimkutu22","resimkutu23","resimkutu24","resimkutu25","resimkutu26","resimkutu27","resimkutu28","resimkutu29","resimkutu30","resimkutu31","resimkutu32","resimkutu33","resimkutu34","resimkutu35","resimkutu36","resimkutu37","resimkutu38","resimkutu39","resimkutu40","resimkutu41","resimkutu42","resimkutu43","resimkutu44","resimkutu45","resimkutu46","resimkutu47","resimkutu48","resimkutu49","resimkutu50"];
				jQuery.each( $kutular, function( i, val )
				{
				  if($( "#" + val ).length){$kutudurum=1;return false;}

				});
				if($kutudurum==1)
				{
					var $div = $('div[id^="resimkutu"]:last');
					var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
					var $klon = $div.clone().prop('id', 'resimkutu'+num );

					$div.addClass( "form-group floating-label" );
					$div.after( $klon.html('<div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="rkon"><img id="ryer" class="img-circle size-2" src="/_y/assets/img/avatar7.jpg" ></div><h1 class="text-light no-margin" id="rad">Resim Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="sillink" data-resimkutu="resimkutu'+num+'" data-id="0" data-toggle="modal" data-target="#simpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-search" id="hazirekle" data-resimkutu="resimkutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-left" data-toggle="offcanvas" id="yeniekle" data-resimkutu="resimkutu'+num+'" title="ekle"><i class="fa fa-plus"></i></a></div></div>') );
				}
				else
				{
					var $div = $('#resimgovde');
					var num = 1;
					
					$div.append('<div id="resimkutu'+num+'"><div class="margin-bottom-xxl"><div class="pull-left width-3 clearfix hidden-xs" id="rkon"><img id="ryer" class="img-circle size-2" src="/_y/assets/img/avatar7.jpg" ></div><h1 class="text-light no-margin" id="rad">Resim Adı '+num+'</h1><h5>&nbsp; &nbsp; &nbsp; &nbsp; Sil &nbsp; &nbsp; &nbsp; Hazır Ekle &nbsp; Yeni Ekle</h5><div class="hbox-column v-top col-md-1"><a class="btn btn-floating-action ink-reaction" id="sillink" data-resimkutu="resimkutu'+num+'" data-id="0" data-toggle="modal" data-target="#simpleModal" title="sil"><i class="fa fa-trash"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-search" id="hazirekle" data-resimkutu="resimkutu'+num+'" data-toggle="offcanvas" title="seç"><i class="fa fa-file-image-o"></i></a> &nbsp;&nbsp;&nbsp; <a class="btn btn-floating-action ink-reaction" href="#offcanvas-left" data-toggle="offcanvas" id="yeniekle" data-resimkutu="resimkutu'+num+'" title="ekle"><i class="fa fa-plus"></i></a></div></div></div>');
				}
			});
			$( "#yeniekle" ).live( "click",function()
			{
				$resimustekle=0;
				$resimkutu 	=$( this ).data( "resimkutu" );
			});
			$( "#hazirekle" ).live( "click",function()
			{
				$resimustekle=0;
				$resimkutu 	=$( this ).data( "resimkutu" );
			});
			$( "#syeniekle" ).live( "click",function()
			{
				$resimustekle=1;
			});
			$( "#shazirekle" ).live( "click",function()
			{
				$resimustekle=1;
			});
			$( "a.resimsec" ).live( "click",function()
			{
				$resimid 	=$( this ).data( "id" );
				$resimlink 	=$( this ).data( "link" );
				$resimad 	=$( this ).data( "ad" );
				$ren 		=$( this ).data( "en" );
				$rboy	 	=$( this ).data( "boy" );

				if($resimustekle==0)
				{
					if($("#resimid").val()=="")
					{
						$("#resimid").val($resimid);
						$("#"+ $resimkutu +" #rad").text($resimad);
						$("#"+ $resimkutu +" #sillink").attr("data-id",$resimid);
						"#sillink"
					    d=$.now();
					    $resim="/m/r/"+$resimlink+"?"+d;
					    $("#"+ $resimkutu +" #ryer").attr("src",$resim);
					    $("#sagcanvas").click();
					}
					else
					{
						$data = $("#resimid").val();
						$arr = $data.split(',');
						$ekledurum=1;
						if($arr.length>0)
						{
							for(var i=0; i< $arr.length; i++)
							{
						      if($arr[i]==$resimid)
						      {
						      	$ekledurum=0;
						      }
						    }
						}
						if($ekledurum==1)
						{
							$("#resimid").val($("#resimid").val()+","+$resimid);
							$("#"+ $resimkutu +" #rad").text($resimad);
							$("#"+ $resimkutu +" #sillink").attr("data-id",$resimid);
						    d=$.now();
						    $resim="/m/r/"+$resimlink+"?"+d;
						    $("#"+ $resimkutu +" #ryer").attr("src",$resim);
						    $("#sagcanvas").click();
						}
						else
						{
							alert("Bu resim Zaten Ekli '"+$resimad+"' [ "+$resimid+" ] ");
						}
						//$("#resimid").val($yenideger);
					}
				}
				else
				{
					$rboy=Math.round((300/$ren)*$rboy);
					$ren=300;
					CKEDITOR.instances.ckeditor.insertHtml('<img src="/m/r/'+$resimlink+'" title="'+$resimad+'" width="'+$ren+'" height="'+$rboy+'" >')
					//$('#summernote').code($('#summernote').code()+'<img src="/m/r/'+$resimlink+'" title="'+$resimad+'" width="'+$ren+'" height="'+$rboy+'" >');
					$("#sagcanvas").click();
				}		
			});
			$silid=0;
			$sildiv="";
			$('a#sillink').live( "click",function ()
			{
				$silid=$(this).data("id");
				$sildiv=$( this ).data( "resimkutu" );
				//alert($sildiv);
			});
			$('#silbutton').live( "click",function ()
			{
				$data = $("#resimid").val();
				$arr=new Array();
				$arr.push($data);
				$arr = $data.split(',');
				$ekleid = new Array();
				//alert($silid);
				//alert($data);
				if($silid!=0)
				{
					if($arr.length>0)
					{
						for(var i=0; i< $arr.length; i++)
						{
					      if($arr[i]!=$silid)
					      {
								if(jQuery.inArray( $arr[i], $ekleid )<0){$ekleid.push($arr[i]);}
					      }
					    }
					    //alert($ekleid);
						$("#resimid").val($ekleid);
						
						//$silid=0;
					}
				}	
				$("#"+$sildiv).remove();
				$("#btn-popup-sil-kapat").click();
			});	
			$("#resimgaleriad").focusout(function()
			{
				$("#resimad").val($("#resimgaleriad").val());
			});
			</script>
			<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
	</body>
</html>