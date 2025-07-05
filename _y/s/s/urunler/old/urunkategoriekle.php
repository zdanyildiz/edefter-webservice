<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
$butonisim=" EKLE ";
$formhataaciklama=q("formhataaciklama");
$f_kategoriid=f("kategoriid");
if(S(f("dilid"))!=0){$f_dilid=f("dilid");}else{$f_dilid=$dilid;}
$f_kategoritariholustur=f("kategoritariholustur");
$f_kategoritarihguncel=f("kategoritarihguncel");
$f_ustkategoriid=S(f("ustkategoriid"));
$f_kategoriad=f("kategoriad");
$f_resimid=S(f("resimid"));
//$f_anasayfa=S(f("anasayfa"));
$f_kategoriicerik=f("kategoriicerik");
$f_kategorilink=f("kategorilink");
$f_kategorisira=S(f("kategorisira"));
$f_kategoriaktif=S(f("kategoriaktif"));
$f_kategorisiralama=S(f("kategorisiralama"));
$f_kategorigrup=S(f("kategorigrup"));
$f_kategorisil=S(f("kategorisil"));
$f_seobaslik=f("seobaslik");
$f_seoaciklama=f("seoaciklama");
$f_seokelime=f("seokelime");
if(BosMu(f("benzersizid")))$f_benzersizid=SifreUret(20,2);else $f_benzersizid=f("benzersizid");
$f_kategorikatman=0;
$rad="Resim Adı";
$resim="/_y/assets/img/avatar7.jpg?1404026721";
$f_kategoriekle=f("kategoriekle");

$tepekategoriid="";
function tepekategoribul($strkategoriid)
{
	global $data,$tepekategoriid,$tepekategoriad,$tepekategorilink,$tepekategori;
	if(!$data)Veri(true);
	$ustkatid=teksatir("select ustkategoriid From kategori Where kategoriid='".$strkategoriid."'","ustkategoriid");
	if($ustkatid==0)
	{
		$tepekategoriid=$strkategoriid;
		if(BosMu($tepekategori))$tepekategori=$ustkatid;else $tepekategori=$ustkatid.",".$tepekategori;
		//$tepekategoriad=teksatir("select kategoriad From kategori Where kategoriid='".$strkategoriid."'","kategoriad");
		//$tepekategorilink=teksatir("select link From kategori inner join seo on seo.benzersizid=kategori.benzersizid Where kategoriid='".$strkategoriid."'","link");
	}
	else
	{
		if(BosMu($tepekategori))$tepekategori=$ustkatid;else $tepekategori=$ustkatid.",".$tepekategori;
		tepekategoribul($ustkatid);
	}
}

if(S($f_kategoriekle)==1)
{
	if(!$data)Veri(true);
	$simdi=date("Y-m-d H:i:s");
	if(S($f_ustkategoriid)!=0)
	{
		$f_kategorikatman=teksatir(" Select kategorikatman from kategori Where kategoriid='". $f_ustkategoriid ."'","kategorikatman");
		$f_kategorikatman=S($f_kategorikatman)+1;
	}else{$f_kategorikatman=0;}
	if(S($f_kategoriid)!=0)
	{
		$kategoriekle_s="
			UPDATE
				kategori
			SET
				dilid 						= '". $f_dilid ."',
				kategoritarihguncel			= '". $simdi ."',
				ustkategoriid				= '". $f_ustkategoriid ."',
				kategorikatman				= '". S($f_kategorikatman) ."',
				kategoriad					= '". $f_kategoriad ."',
				resimid						= '". $f_resimid ."',
				kategoriicerik				= '". $f_kategoriicerik ."',
				kategorilink				= '". $f_kategorilink ."',
				kategorisira				= '". $f_kategorisira ."',
				kategorisiralama			= '". $f_kategorisiralama ."',
				kategoriaktif				= '". $f_kategoriaktif ."',
				kategorigrup				= '". $f_kategorigrup ."',
				Anasayfa					= '0',
				kategorisil					= '0'
			WHERE 
				kategoriID					='".$f_kategoriid."'
		";
		$eylem=3;$formad="Kategori Güncelle";
	}
	else
	{
		$kategoriekle_s = "
			INSERT INTO kategori 
			(
				dilid,
				kategoritariholustur,
				kategoritarihguncel,
				ustkategoriid,
				kategorikatman,
				kategoriad,
				resimid,
				kategoriicerik,
				kategorilink,
				kategorisira,
				kategoriaktif,
				kategorisiralama,
				kategorigrup,
				anasayfa,
				kategorisil,
				benzersizid
			)
			VALUES 
			(
				'".$f_dilid."',
				'".$simdi."',
				'".$simdi."',
				'".$f_ustkategoriid."',
				'".$f_kategorikatman."',
				'".$f_kategoriad."',
				'".$f_resimid."',
				'".$f_kategoriicerik."',
				'".$f_kategorilink."',
				'".$f_kategorisira."',
				'".$f_kategoriaktif."',
				'".$f_kategorisiralama."',
				'".$f_kategorigrup."',
				'0',
				'0',
				'". $f_benzersizid ."'
			)
			";
		$eylem=1;$formad="Kategori Ekle";
	}
	if($formhata==0)
	{
		if($data->query($kategoriekle_s))
		{
			yoneticiislemleri(22,$eylem);
			if(S($f_kategoriid)!=0)
			{
				$formhataaciklama="Kategori güncellendi";
				$f_benzersizid=teksatir(" Select benzersizid from kategori Where kategoriid='". $f_kategoriid ."'","benzersizid");
			}
			else
			{
				$formhataaciklama="Yeni Kategori eklendi";
				$f_kategoriid=teksatir(" Select kategoriid from kategori Where benzersizid='". $f_benzersizid ."'","kategoriid");
			}
			$data->query("DELETE FROM seo WHERE benzersizid='". $f_benzersizid ."'");
			$seoresim="";
			if(!BosMu($f_resimid))
			{
				$f_resimid=rtrim($f_resimid,",");
				$f_resimler = explode(",", $f_resimid);
				foreach($f_resimler as $ekleresim)
				{
					$resim=teksatir(" Select resim from resim Where resimid='". $ekleresim ."'","resim");
					$resimklasorad=teksatir("SELECT resimklasorad FROM resimklasor INNER JOIN resim ON resim.resimklasorid=resimklasor.resimklasorid Where resimid='". $ekleresim ."'","resimklasorad");
					if(BosMu($seoresim))$seoresim="/m/r/".$resimklasorad."/".$resim;else$seoresim=$seoresim.","."/m/r/".$resimklasorad."/".$resim;
					$resim="";$resimklasorad="";
				}
			}
			$sutunlar="benzersizid,
				baslik,
				aciklama,
				kelime,
				link,
				resim";
			if(!BosMu($f_kategorilink) && S($f_kategorilink)==0){$seolink=$f_kategorilink;}else{$seolink="/".Duzelt(K($f_kategoriad))."/".$f_kategoriid."m.html";}
			$degerler=$f_benzersizid."|*_".
				mb_substr($f_seobaslik,0,65,'UTF-8')."|*_".
				mb_substr($f_seoaciklama,0,200,'UTF-8')."|*_".
				mb_substr($f_seokelime,0,255,'UTF-8')."|*_".
				$seolink."|*_".
				$seoresim;
			ekle($sutunlar,$degerler,"seo",24);
			exit(header('Location: /_y/s/s/urunler/urunkategoriekle.php?formhataaciklama='.$formhataaciklama.'&kategoriid='. $f_kategoriid));
			//if(!BosMu(f("refurl"))) header('Location: /_y/s/guvenlik/kilit.php?refurl='. f("refurl"));
		}
		else
		{
			hatalogisle("ÜrünKategoriEkle",$data->error);
			$formhata=1;
			if(S($f_kategoriid)!=0) $formhataaciklama="Kategori güncellenemedi"; else $formhataaciklama="Yeni Kategori eklenemedi";
		}
		unset($kategoriekle_s);
	}
}
if(S(q("kategoriid"))!=0)
{
	if(!$data)Veri(true);
	$kategoribilgileri_d=0;
	$kategoribilgileri_s=
	"
		SELECT
			dilid,
			ustkategoriid,
			kategoriad,
			resimid,
			kategoriicerik,
			kategorilink,
			kategorisira,
			kategorisiralama,
			kategoriaktif,
			kategorigrup,
			anasayfa,
			kategorisil,
			benzersizid
		FROM
			kategori
		WHERE
			kategorisil='0' and kategoriid='".q("kategoriid")."'
	";
	if($data->query($kategoribilgileri_s))
	{
		$kategoribilgileri_v=$data->query($kategoribilgileri_s);
		if($kategoribilgileri_v->num_rows>0)$kategoribilgileri_d=1;
		if($kategoribilgileri_d==1)
		{
			$butonisim=" GÜNCELLE ";
			while($kategoribilgileri_t = $kategoribilgileri_v->fetch_assoc())
			{
				$f_kategoriid 		=q("kategoriid");
				$f_dilid 			=$kategoribilgileri_t["dilid"];
				$f_ustkategoriid 	=$kategoribilgileri_t["ustkategoriid"];
				$f_kategoriad 		=$kategoribilgileri_t["kategoriad"];
				$f_resimid 			=$kategoribilgileri_t["resimid"];
				$f_kategoriicerik 	=$kategoribilgileri_t["kategoriicerik"];
				$f_kategorilink 	=$kategoribilgileri_t["kategorilink"];
				$f_kategorisira 	=$kategoribilgileri_t["kategorisira"];
				$f_kategorisiralama	=$kategoribilgileri_t["kategorisiralama"];
				$f_kategoriaktif 	=$kategoribilgileri_t["kategoriaktif"];
				$f_kategorigrup 	=$kategoribilgileri_t["kategorigrup"];
				$f_anasayfa 		=$kategoribilgileri_t["anasayfa"];
				$f_kategorisil 		=$kategoribilgileri_t["kategorisil"];
				$f_benzersizid		=$kategoribilgileri_t["benzersizid"];
				$formbaslik="Kategori Bilgileri";
			}
			unset($kategoribilgileri_t);
			$resim_d=0;
			$resim_s="
				SELECT
					resimklasorad,resimad,resim
				FROM 
					resim 
						inner join resimklasor 
							on resimklasor.resimklasorid=resim.resimklasorid
				WHERE resimid='".$f_resimid."'
			";
			if($data->query($resim_s))
			{
				$resim_v=$data->query($resim_s);
				if($resim_v->num_rows>0)$resim_d=1;
			}
			unset($resim_s);
			
			if($resim_d==1)
			{
				while ($resim_t=$resim_v->fetch_assoc()) 
				{
					$rad = $resim_t["resimad"];
					$resim = "/m/r/".$resim_t["resimklasorad"]."/".$resim_t["resim"];
				}
			}

			$kategoriseo_d=0;
			$kategoriseo_s="
				Select 
					baslik,aciklama,kelime 
				From 
					seo
				where 
					benzersizid='". $f_benzersizid ."'";
			if($data->query($kategoriseo_s))
			{
				$kategoriseo_v=$data->query($kategoriseo_s);unset($kategoriseo_s);
				if($kategoriseo_v->num_rows>0)$kategoriseo_d=1;
				
				if($kategoriseo_d==1)
				{
					while($kategoriseo_t = $kategoriseo_v->fetch_assoc())
					{
						$f_seobaslik=stripslashes($kategoriseo_t["baslik"]);
						$f_seoaciklama=stripslashes($kategoriseo_t["aciklama"]);
						$f_seokelime=stripslashes($kategoriseo_t["kelime"]);
					}
					unset($kategoriseo_t);
				}
			}
			unset($kategoriseo_v,$kategoriseo_d);
		}
		unset($kategoribilgileri_d);
	}
}
if(S($f_ustkategoriid)!=0)
{
	tepekategoribul($f_ustkategoriid);
}

/*function kategoriliste($ustkategoriid,$kategorikatman,$urunkategorisecid)
{
	global $data;$sqlek="";
	if(!$data)Veri(true);
	if(S(q("kategoriid"))!=0){$sqlek=" and kategoriid!='".q("kategoriid")."' ";}
	$urunkategori_s="
		SELECT 
			kategoriid,kategoriad,dilid,kategorikatman 
		FROM 
			kategori 
		WHERE 
			kategorisil='0' and 
			kategorigrup='7' and kategorikatman='".S($kategorikatman)."' and ustkategoriid='".S($ustkategoriid)."' $sqlek
		ORDER BY 
			kategorikatman asc,kategoriid ASC";
	if($data->query($urunkategori_s))
	{
		$urunkategori_v=$data->query($urunkategori_s);unset($urunkategori_s);
		if($urunkategori_v->num_rows>0)
		{
			while ($urunkategori_t=$urunkategori_v->fetch_assoc()) 
			{
				$urunkategoridilid=$urunkategori_t["dilid"];
				$urunkategoriid=$urunkategori_t["kategoriid"];
				$urunkategoriad=$urunkategori_t["kategoriad"];
				$urunkategoridil=teksatir("select dilad from dil where DilID='". $urunkategoridilid ."'","dilad");
				$kategorikatman=$urunkategori_t["kategorikatman"];
				$katmanek=""; $secili="";
				if($urunkategoriid==$urunkategorisecid)$secili="selected";
				if($kategorikatman==1)
				{
					$katmanek=" -";
				}
				elseif($kategorikatman==2)
				{
					$katmanek=" --";
				}
				elseif($kategorikatman==3)
				{
					$katmanek=" ---";
				}
				if($kategorikatman==0)$kategoristyle=' style="font-weight:bold"';else $kategoristyle='';
				echo '
					<option value="'.$urunkategoriid.'" '.$kategoristyle.' '.$secili.' >
						'.$katmanek.' '.$urunkategoriad.'
					</option>
				';
				kategoriliste($urunkategoriid,S($kategorikatman)+1,$urunkategorisecid);
			}unset($urunkategori_t,$urunkategoriid,$urunkategoridilid,$urunkategoriad,$urunkategoridil);
		}unset($urunkategori_v);
	}else{die($data->error);}
}*/
?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>SYM Panel - Kategori Ekle</title>

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
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/select2/select2.css?1424887856" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/multi-select/multi-select.css?1424887857" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-colorpicker/bootstrap-colorpicker.css?1424887860" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-tagsinput/bootstrap-tagsinput.css?1424887862" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/typeahead/typeahead.css?1424887863" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/dropzone/dropzone-theme.css?1424887864" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/summernote/summernote.css?1425218701" />
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

			<!-- BEGIN OFFCANVAS LEFT -->
			<div class="offcanvas">
				<!-- sol popup -->
				<div id="offcanvas-left" class="offcanvas-pane width-12" >
					<div class="offcanvas-head">
						<header>Yeni Resim Yükleyin</header>
						<div class="offcanvas-tools">
							<a id="resimyuklepencerekapat" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
								<i class="md md-close"></i>
							</a>
						</div>
					</div>
					<div class="offcanvas-body">
						<div class="card">				
							<div class="card-body no-padding">
								<form action="/_y/s/f/yukle.php" target="_islem" class="dropzone dz-clickable" id="myawesomedropzone">
									<div class="form-group" style="padding-top: 20px">
										<input type="text" name="resimad" id="resimad" class="form-control" placeholder="Resim Adını Girin" required="" data-rule-minlength="2" aria-required="true" aria-describedby="resimad-error" aria-invalid="true" style="background-color:#efefef;cursor:text">
										<label for="resimad">Önce Resim Adı Girin</label>
									</div>
									<div class="form-group">
										<input type="hidden" name="resimklasor" value="menu">
										<div class="dz-message">
											<h3>Resmi Sürükleyin ve Bırakın veya Tıklayın.</h3>
											<em>En fazla <strong>1 (2MB)</strong> resim seçin</em>
										</div>
									</div>
								</form>
							</div><!--end .card-body -->
						</div>
					</div>
					<div class="force-padding stick-bottom-right">
						<a class="btn btn-floating-action btn-default-dark" href="#offcanvas-demo-size3" data-toggle="offcanvas">
							<i class="md md-arrow-back"></i>
						</a>
					</div>
				</div>
				<!-- //sol popop-->
			</div><!--end .offcanvas-->
			<!-- END OFFCANVAS LEFT -->

			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">KATEGORİ EKLE</li>
						</ol>
					</div>
					<div class="section-body contain-lg">

						<!-- BEGIN INTRO -->
						<div class="row">
							<div class="col-lg-12">
								<h1 class="text-primary">Sitenizin tüm kategorilerini sırası ile tanımlayın</h1>
							</div><!--end .col -->
							<div class="col-lg-8">
								<article class="margin-bottom-xxl">
									<p class="lead">
										Örn: (Anasayfa, Hakkımızda, İletişim).
									</p>
								</article>
							</div><!--end .col -->
						</div><!--end .row -->
						<!-- END INTRO -->
						<form class="form" method="post">
							<input type="hidden" name="kategoriekle" 	id="kategoriekle" 	value="1">
							<input type="hidden" name="kategoriid" 		id="kategoriid" 	value="<?=$f_kategoriid?>">
							<input type="hidden" name="ustkategoriid" 	id="ustkategoriid" 	value="<?=$f_ustkategoriid?>">
							<input type="hidden" name="resimid" 		id="resimid" 		value="<?=$f_resimid?>">
							
							<div class="row">
								<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Kategori Dil</h4><p></p>
										<p>
											Ekleyeceğiniz Kategori için Dil Seçin
										</p>
									</article>
								</div>
								<div class="col-lg-offset-1 col-md-8">
									<div class="form-group">
										<select id="dilid" name="dilid" class="form-control">
										<?php
										if(!$data)Veri(true);
										$dil_d=0; $dil_v=""; $dil_s="";
										$dil_s="SELECT dilid,dilad,dilkisa FROM dil Where dilsil='0' and dilaktif='1'";
										if($data->query($dil_s))
										{
											$dil_v=$data->query($dil_s);unset($dil_s);
											if($dil_v->num_rows>0)
											{
												while($dil_t=$dil_v->fetch_assoc())
												{
													$l_dilid=$dil_t["dilid"];
													$l_dAd=$dil_t["dilad"];
													$l_dKisa=$dil_t["dilkisa"];
													?>
													<option value="<?=$l_dilid?>" <?php if($l_dilid==$f_dilid)echo "selected"; ?> ><?=$l_dAd?> (<?=$l_dKisa?>)</option>
													<?php
												}unset($dil_t);
											}unset($dil_v);
										}else{die($data->error);}
										?>
										</select>
										<label for="Age2">KATEGORİ İÇİN DİL SEÇİN</label>
										<p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Kategori Temel Bilgiler</h4><p></p>
										<p>
											Gireceğiniz KATEGORİ bir ALT-KATEGORİ mi? Daha önceden girilmiş bir kategori altına gelecekse Üst Kategorisini seçin! (örn: Ürünler/Cep Telefonu)
										</p>
										<br>
										<p>Yayın alanından Pasif konumunu seçerseniz kategori kaydedilir fakat siz aktif edene kadar görüntülenmez.</p>
									</article>
								</div><!--end .col -->
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div>ÜST KATEGORİ SEÇİN</div>
											<div class="row form-group floating-label" id="kategoridivler">
												<div id="kategoridiv0" class="col-sm-6 form-group floating-label">
													<?php 
														$sorgu="
															SELECT 
																* 
															FROM  
																kategori 
															WHERE 
																kategoriaktif='1' and 
																kategorisil='0' and 
																kategorigrup='7' and 
																ustkategoriid='0' 
														";
														if($data->query($sorgu))
														{
														 	$sonuc=$data->query($sorgu);
														 	$sonuctoplam=$sonuc->num_rows;
														 	if ($sonuctoplam>0)
														 	{
																echo '<select class="form-control" size="5" data-id="0"><option value="0" selected>Üst Kategori Yok</option>';
														 		while($sonucliste=$sonuc->fetch_assoc())
														 		{
														 			$kategorisec="";
														 			$kategoriad=$sonucliste["kategoriad"];
														 			$kategoriid=$sonucliste["kategoriid"];
														 			if($kategoriid==$tepekategoriid)$kategorisec="selected";
														 			echo '<option value="'.$kategoriid.'" '.$kategorisec.'>'.$kategoriad.'</option>';
														 		}
																echo '</select>';
														 	}
														}else{die($data->error);}
													?>
												</div>
											</div>
											<div class="form-group">
												<input type="text" name="kategoriad" id="kategoriad" class="form-control" placeholder="Örn:Cep Telefonları" value="<?=$f_kategoriad?>">
												<label>Kategori Adı</label>
											</div>
											<div class="form-group">
												<label class="col-sm-3 control-label">Yayınlansın mı</label>
												<div class="col-sm-9">
													<label class="radio-inline radio-styled">
														<input type="radio" name="kategoriaktif" value="1" <?php if(S($f_kategoriaktif)==1) echo "checked";?>><span>Aktif</span>
													</label>
													<label class="radio-inline radio-styled">
														<input type="radio" name="kategoriaktif" value="0" <?php if(S($f_kategoriaktif)==0) echo "checked";?>><span>Pasif</span>
													</label>	
												</div><!--end .col -->
											</div>
										</div><!--end .card-body -->
									</div><!--end .card -->
									<em class="text-caption">Temel özellikleri seçin</em>
								</div><!--end .col -->
							</div><!--end .row -->
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Kategori Görünümü</h4>
										<p>İlgili Kategorinin grubunu seçin. Tıklandığında sayfa özellikleri ona göre yüklenecek</p>
										<p>&nbsp;</p>
										<p>Kategori altına eklenen sayfaların nasıl sıralanacağını seçin</p>
										<p>&nbsp;</p>
										<p>Dilerseniz kategori sayfasında görüntülenmek üzere bir resim seçebilirsiniz </p>
									</article>
								</div><!--end .col -->
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div class="form-group floating-label">
												<select id="kategorigrup" name="kategorigrup" class="form-control">
											        <option value="7" <?php if(S($f_kategorigrup)==7) echo "selected";?>>Ürün</option>
												</select>
												<label for="menugrup">Kategori Grubunu Seçin</label>
											</div>
											<div class="form-group floating-label">
												<select id="kategorisiralama" name="kategorisiralama" class="form-control">
													<option value="">&nbsp;</option>
													<option value="0" <?php if(S($f_kategorisiralama)==0) echo "selected";?>>İlk Eklenen En Üste</option>
											        <option value="1" <?php if(S($f_kategorisiralama)==1) echo "selected";?>>Son Eklenen En Üste</option>
											        <option value="2" <?php if(S($f_kategorisiralama)==2) echo "selected";?>>Güncelleme Tarihi Eskiden Yeniye</option>
											        <option value="3" <?php if(S($f_kategorisiralama)==3) echo "selected";?>>Güncelleme Tarihi Yeniden Eskiye</option>
											        <option value="4" <?php if(S($f_kategorisiralama)==4) echo "selected";?>>Sayfa Sırası Küçükten Büyüğe</option>
											        <option value="5" <?php if(S($f_kategorisiralama)==5) echo "selected";?>>Sayfa Sırası Büyükten Küçüğe</option>
											        <option value="6" <?php if(S($f_kategorisiralama)==6) echo "selected";?>>Sayfa Adı A-Z</option>
											        <option value="7" <?php if(S($f_kategorisiralama)==7) echo "selected";?>>Sayfa Adı Z-A</option>
												</select>
												<label for="menugrup">Kategori Sayfaları Sıralaması</label>
											</div>
											<div class="form-group floating-label">
												<div class="margin-bottom-xxl">
													<div class="pull-left width-3 clearfix hidden-xs">
														<img id="ryer" class="img-circle size-2" src="<?=$resim?>" alt="">
													</div>
													<h1 class="text-light no-margin" id="rad"><?=$rad?></h1>
													<h5>Kategori Resmi</h5>
													<div class="hbox-column v-top col-md-1">
														<a 
															class="btn btn-floating-action ink-reaction" 
															id="sillink" 
															data-toggle="modal" 
															data-target="#simpleModal" 
															data-id="<?=$f_resimid?>" 
															title="sil">
															<i class="fa fa-trash"></i>
														</a>
														&nbsp;&nbsp;&nbsp;
														<a 
															class="btn btn-floating-action ink-reaction" 
															href="#offcanvas-search" 
															data-toggle="offcanvas" 
															title="seç">
															<i class="fa fa-file-image-o"></i></a>
														&nbsp;&nbsp;&nbsp;
														<a 
															class="btn btn-floating-action ink-reaction" 
															href="#offcanvas-left" 
															data-toggle="offcanvas" 
															title="ekle">
															<i class="fa fa-plus"></i></a>
														<a 
															class="btn btn-floating-action ink-reaction" 
															id="resimok" 
															href="#textModal" 
															data-toggle="modal" 
															data-backdrop="false" 
															style="display: none;"></a>
													</div>
												</div>
											</div>
											<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" id="btn-popup-sil-kapat" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h4 class="modal-title" id="simpleModalLabel">Resmi Sil</h4>
														</div>
														<div class="modal-body">
															<p>Resmi silmek istediğinize emin misiniz?</p>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
															<button type="button" class="btn btn-primary" id="silbutton">Resmi Sil</button>
														</div>
													</div><!-- /.modal-content -->
												</div><!-- /.modal-dialog -->
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
													</div><!-- /.modal-content -->
												</div><!-- /.modal-dialog -->
											</div>

										</div><!--end .card-body -->
									</div><!--end .card -->
									<em class="text-caption">Görünüm özellikleri seçin</em>
								</div><!--end .col -->
							</div><!--end .row -->
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Kategori İçeriği</h4><p></p>
										<p>Kategori bağlantısı tıklanınca farklı bir sayfaya gitmesini istiyorsanız buraya adres girin aksi halde boş bırakın</p>
										<p>&nbsp;</p>
										<p>Yayın alanından Pasif konumunu seçerseniz kategori kaydedilir fakat siz aktif edene kadar görüntülenmez.</p>
									</article>
								</div><!--end .col -->
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div class="form-group">
												<textarea name="kategorilink" id="kategorilink" class="form-control" rows="1" placeholder="http://www......"><?=$f_kategorilink?></textarea>
												<label for="kategorilink">Kategori Bağlantısı</label>
											</div>
											<div class="card-body no-padding">
												<textarea id="summernote" name="kategoriicerik"><?=$f_kategoriicerik?></textarea>
											</div>
										</div><!--end .card-body -->
									</div><!--end .card -->
									<em class="text-caption">Kategori İçeriği/Açıklama</em>
								</div><!--end .col -->
							</div><!--end .row -->
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Google Arama SEO Seçenekleri</h4><p></p>
										<p>
											<strong>SEO Başlığı;</strong> Bu sayfayı google'da nasıl ararlar? <code>(En fazla 65 karakter)</code>
										</p>
										<p><strong>SEO Açıklaması;</strong> bu sayfada neyden bahsedilmektedir. Kısa özet <code>(En fazla 200 karakter)</code></p>
										<p><strong>SEO Kelimeler;</strong> sayfa içeriği ile alakalı küçük harfle ve virgül ile ayrılmış kelimeler girin. (marka ürün,renk ürün,cinsiyet ürün,marka cinsiyet...) <code>(En fazla 255 karakter)</code></p>
									</article>
								</div><!--end .col -->
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
											<div class="form-group">
												<input 
													type="text" 
													name="seobaslik" 
													id="seobaslik" 
													class="form-control" 
													placeholder="xxx nedir | xxx neden olur | xxx'in belirtileri" 
													value="<?=$f_seobaslik?>" 
													data-rule-minlength="5"
													maxlength="65"
													aria-invalid="false"
													required aria-required="true">
												<label for="seobaslik">SEO Başlık</label>
											</div>
											<div class="form-group">
												<textarea 
													id="seoaciklama" 
													name="seoaciklama" 
													placeholder="xxx nedenleri, belirtileri ve çözümü hakkındaki tüm bilgilere bu sayfadan ulaşabilirsiniz"
													class="form-control"  
													rows="3"
													data-rule-minlength="25"
													maxlength="200"
													aria-invalid="false"
													required aria-required="true"><?=$f_seoaciklama?></textarea>
													<label for="seoaciklama">SEO Açıklama</label>
											</div>
											<div class="form-group">
												<textarea 
													id="seokelime" 
													name="seokelime"
													class="form-control" 
													placeholder="xxx nedir,xxx neden olur,xxx belirtileri" 
													rows="2"
													data-rule-minlength="6"
													maxlength="255"
													aria-invalid="false"
													required aria-required="true"><?=$f_seokelime?></textarea>
													<label for="seokelime">SEO Kelimeler</label>
											</div>

										</div>
									</div>
									<em class="text-caption">Sayfa İçeriği/Açıklama</em>
								</div>
							</div>
							<div class="card-actionbar">
								<div class="card-actionbar-row">
									<button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
								</div><!--end .card-actionbar-row -->
							</div>
						</form>
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
		<script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
		<script src="/_y/assets/js/libs/ckeditor/ckeditor.js"></script>
		<script src="/_y/assets/js/libs/ckeditor/adapters/jquery.js"></script>
		<script src="/_y/assets/js/libs/summernote/summernote.min.js"></script>
		<script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>
		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormEditors.js"></script>
		<!-- END JAVASCRIPT -->
		<script>
			$("#urunkategorieklephp").addClass("active");
		</script>
		<script type="text/javascript">
			Dropzone.autoProcessQueue= true;
			Dropzone.options.myawesomedropzone =
			{
				parallelUploads: 10,
				autoProcessQueue: true,
				addRemoveLinks: true,
				maxFiles: 1,
				maxFilesize: 5,
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
			            //alert($resimadi);
			            $("#resimid").val($resimid);
			            d=$.now();
			            $resim="/m/r/"+$resimadi+"?"+d;
			            $("#ryer").attr("src",$resim);
			            
			            if($("#textModal").css('display') == 'none')
			            {
							this.removeFile(file);
							$("#rad").text($("#resimad").val());
							$("#resimad").val("");
							$("#resimyuklepencerekapat").click();
							$("#resimok").click();
							$("#textModal > div.modal-dialog > div > div.modal-body > p").text("Resim Yükleme Başarılı");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
							$("#textModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-success");
				        	
				        	$("#resimhata").val(1);
						}
			        });
			        this.on("addedfile", function(file)
			        {
			        	if($("#resimad").val().length<2)
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
			$("#summernote").summernote({
			  height: 300
			});

			$( "a.resimsec" ).live( "click",function()
			{
				$resimid 	=$( this ).data( "id" );
				$resimlink 	=$( this ).data( "link" );
				$resimad 	=$( this ).data( "ad" );
				$("#resimid").val($resimid);
				$("#rad").text($resimad);
			    d=$.now();
			    $resim="/m/r/"+$resimlink+"?"+d;
			    $("#ryer").attr("src",$resim);
			});
			
			$silid=0;
			$(document).ready(function()
			{
				$('a#sillink').click(function ()
				{
					//$silid=$(this).data("id");
				});
				$('#silbutton').click(function ()
				{
					$("#resimid").val(0);
					$("#rad").text("Resim Adı");
				    d=$.now();
				    $resim="/_y/assets/img/avatar7.jpg?"+d;
				    $("#ryer").attr("src",$resim);
				    $("#btn-popup-sil-kapat").click();
					//$('#_islem').attr('src', "/_y/s/f/sil.php?sil=resim&id="+$silid);
				});
			 });
			//$('#summernote').text($('#summernote').text() + " zafer");
		</script>

		<script type="text/javascript">
			$(document).on('click', '#kategoridivler select', function()
			{
				$katman=$(this).data("id");
				$toplamkatman = $("div[id*='kategoridiv']").length;

				$deger=$(this).find('option:selected').val();
				if($deger>0)
				{
					$("#ustkategoriid").val($deger);
					$.ajax({
						type: 'GET', 
						url:"kategorigetir.php?id="+$deger,
						dataType: "html",
						success: function(data)
						{   
							if($.trim(data))
							{
								$katman=$katman+1;
								for($i=$katman;$i<$toplamkatman;$i++)
								{
									if($("#kategoridiv"+$i).length)
									{
										$("#kategoridiv"+$i).remove();
									}
								}
								
								$("#kategoridivler").append('<div class="col-sm-6 form-group floating-label" id="kategoridiv'+$katman+'">Yükleniyor</div>');
								$("#kategoridiv"+$katman).html(data);
								$("#kategoridiv"+$katman+" select").attr('data-id', $katman);
							}
							else
							{
								$i=$katman+1;
								$("#kategoridiv"+$i).remove();
							}
						}
					});
				}
				else
				{
					for ($i = 1; $i < $toplamkatman+10; $i++)
					{
						$("#kategoridiv"+$i).remove();
					}
				}
			});
			<?php 
				if(S($tepekategoriid)!=0)
				{
					?>
					function kategoriekle($katm,$kat,$val)
					{
						$katman=$katm;
						$deger=$kat;
						
						$.ajax({
							type: 'GET', 
							url:"kategorigetir.php?katid="+$val+"&id="+$deger,
							dataType: "html",
							success: function(data)
							{   
								if($.trim(data))
								{
									$katman=$katman+1;
									
									$("#kategoridivler").append('<div class="col-sm-6 form-group floating-label" id="kategoridiv'+$katman+'">Yükleniyor</div>');
									$("#kategoridiv"+$katman).html(data);
									if($kat!=<?=$f_ustkategoriid?>)$("#kategoridiv"+$katman+" select").attr('data-id', $katman);
								}
								else
								{
									$i=$katman+1;
									$("#kategoridiv"+$i).remove();
								}
							}
						});
					}
					$kategoriler="<?=$tepekategori.",".$f_ustkategoriid?>";
					$kategoriler=$kategoriler.replace("0,", "");
					$kategori_ayir = $kategoriler.split(',');
					$.each( $kategori_ayir, function( index, value )
					{
					    if(index<$kategori_ayir.length-1)
					    {
					    	$val=$kategori_ayir[index+1];
					    }
					    else
					    {
					    	$val=<?=$f_ustkategoriid?>;
					    }
					    kategoriekle(index,value,$val);
					});
					<?php 
				}
			?>
		</script>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
	</body>
</html>