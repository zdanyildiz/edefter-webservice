<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php

$butonisim=" EKLE ";
$formhataaciklama=q("formhataaciklama");

$dad="Dosya Adı"; $dekleresim="/_y/assets/img/file.png";

?><!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Toplu Ürün Ekle</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
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
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once($anadizin."/_y/s/b/header.php");?>
		<div id="base">
			<?php require_once($anadizin."/_y/s/b/solpopup.php");?>
			<div id="content">
				<section <?php if(S(q("adim"))==1)echo 'class="style-default-bright"';?>>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">TOPLU ÜRÜN EKLE</li>
						</ol>
					</div>
					
					<?php if(S(q("adim"))==0)
					{
						?>
						<div class="section-body contain-lg">
							<div class="row">
								<div class="col-lg-12">
									<h1 class="text-primary">Excel Dosyanızı Yükleyin</h1>
								</div><!--end .col -->
								<div class="col-lg-8">
									<article class="margin-bottom-xxl">
										<p class="lead">
											sadece xls,xlsx uzantılı olabilir.
										</p>
										<p class="lead">
											<a href="urunler.xlsx" target="_blank">Örnek dosyayı indirmek için tıklayın.</a>
										</p>
									</article>
								</div>
							</div>
							<!-- form class="form" method="post" -->
								<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
								<div class="row">
									<div class="col-lg-3 col-md-4">
										<article class="margin-bottom-xxl">
											<h4>Excel Dosyası</h4>										
											<p><code>Sadece ".xls, .xlsx" uzantılı dosyalar</code></p>
										</article>
									</div>
									<div class="col-lg-offset-1 col-md-8">
										<div class="card">
											<div class="card-head style-primary">
												<hedaer> <span style="padding: 0 0 0 20px;font-size: 16px">Ürün Listesi Yükle</span></hedaer>
											</div>
											<div class="card-body" id="dosyagovde">
												<div class="form-group floating-label" id="dosyakutu1">
													<div class="margin-bottom-xxl">
														<div class="pull-left width-3 clearfix hidden-xs" id="dkon">
															<img id="dyer" class="img-circle size-2" src="<?=$dekleresim?>" alt="">
														</div>
														<h1 class="text-light no-margin" id="dad">Liste</h1>
														<h5>Yeni Ekle</h5>
														<div class="hbox-column v-top col-md-1">
															<a 
																class="btn btn-floating-action ink-reaction" 
																href="#offcanvas-topluurun" 
																id="dyeniekle"
																data-dosyakutu="dosyakutu1"
																data-toggle="offcanvas" 
																title="ekle">
																<i class="fa fa-plus"></i></a>
														</div>
													</div>
												</div>
										</div>
										<em class="text-caption">ürün listesi yükleyin</em>
									</div>
								</div>							
							<!--/form -->
						</div>
						<div class="modal fade in" id="textModal" tabindex="-1" role="dialog" aria-labelledby="textModalLabel" aria-hidden="false">
							<div class="modal-backdrop fade in" style="height: 1019px;"></div>
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											<h4 class="modal-title" id="textModalLabel">Ürün Listesi Yükleme</h4>
										</div>
										<div class="modal-body">
											<p>DOSYA YÜKLENDİ</p>
											<span><a id="adimlink" href="#">Buradan devam edin</a></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					elseif(S(q("adim"))==1 && !BosMu(q("dosya")))
					{
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						echo '
						<div class="card">
							<div class="card-head style-primary">
								<header><h3>Ürün Listenizi Kontrol Edin</h3><br>
								<p>Sorun görünmüyorsa devam edin (ilk 10 satır izleniyor)</p></header>
							</div>
						</div>		
						';
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							
							$excelveri=$xlsx->rows();
							$satirtoplam=count($excelveri);
							foreach ($excelveri as $satirsay => $satir)
							{
								$basliklar="Ürünid,Kategori,Marka,Stok Kodu,Model,Başlık,Alt Başlık,Açıklama,Fiyat,İndirimsiz Fiyat,Satış Başlangıç Tarihi,Satış Bitiş Tarihi,Stok Miktari,Ürün Onayı,Para Birimi,Renk,Beden,Resimler";
								$baslikayir=explode(",", $basliklar);
								if($satirsay==0)
								{
									foreach ($satir as $basliksay => $sutun)
									{
										$sira=$basliksay+1;
										
							 			if($sutun!=$baslikayir[$basliksay])
							 			{
							 				echo '
							 					<h3>İlk sutun '.$sira.'. sıra başlığı '.$baslikayir[$basliksay].' olmalıdır</h3>
							 					<p>Başlıklar birebir örtüşmezse toplu ürün yükleme yapamazsınız!</p>
							 					<a href="urunler.xlsx" target="_blank">Örnek dosyayı indirmek için tıklayın.</a>
							 					<p class="text-danger">BAŞLIKLARI DEĞİŞTİRMEYİNİZ</p>
							 					<a class="text-success" href="/_y/s/s/excel/excel-urun-yukle.php">Yeniden Yükleme Yapın!.</a>
							 				';
							 				unset($excelveri);
							 				break;
							 			}
								 		
									}
								}
									
							}
							if(isset($excelveri))
							{
								echo '<div class="table-responsive">
								<div id="datatable2_wrapper" class="dataTables_wrapper no-footer">
									<table class="table no-margin table-hover">';
								foreach ($excelveri as $satirsay => $satir)
								{
								 	
								 	if($satirsay<=10)
								 	{
									 	if($satirsay==0)echo '<thead>';else echo '<tr>';
									 	foreach ($satir as $sutunsay => $sutun)
									 	{
									 		if($satirsay==0)
									 		{
									 			echo "<th>".$sutun."</th>";
									 		}
									 		else
									 		{
									 			if($sutunsay==7)$sutun=mb_substr(strip_tags($sutun),0,100,"UTF-8");
									 			if($sutunsay==3||$sutunsay==4||$sutunsay==5)$sutun=mb_substr(strip_tags($sutun),0,50,"UTF-8");
									 			if($sutunsay==16)
									 			{
									 				$sutun=str_replace('"', "", $sutun);
									 				$sutun=str_replace(',', ".jpg,", $sutun);
									 				$sutun=explode(",", $sutun)[0];
									 				$sutun='<img class="img-circle width-1"src="/m/r/urun/'.$sutun.'" >';
									 			}
									 			echo "<td>".$sutun."</td>";
									 		}
									 	}
									 	if($satirsay==0)echo '</thead>';else echo '</tr>';
									}
								}
								echo '</table></div></div>';
								echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx">DEVAM EDİN</a>';
							}
								
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==2 && !BosMu(q("dosya")))
					{
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						$xlsx="";$excelveri="";$satir="";
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							$excelveri=$xlsx->rows();
							$satirtoplam=count($excelveri);
							$saybasla=0;$saybitir=0;
							if(s(q("satir"))!=0)$saybasla=S(q("satir"));
							foreach ($excelveri as $satirsay => $satir)
							{ 	
							 	if($satirsay==0)echo "<h3>Kategoriler Alınıyor ($satirtoplam) Ürün</h3>";
							 	
							 	if($satirsay>$saybasla)
							 	{
							 		$saybitir++;
							 		foreach ($satir as $sutunsay => $sutun)
								 	{
								 		if($sutunsay==1 && !BosMu($sutun))//kategori ekle
								 		{
								 			$kategoriler=explode(">",$sutun);
								 			$kategoritoplam=count($kategoriler);
								 			if($kategoritoplam>1)
								 			{
								 				$ustkategoriid=0;
								 				for ($kategorisay=0; $kategorisay < $kategoritoplam; $kategorisay++)
								 				{
								 					$kategoriad=$kategoriler[$kategorisay];
								 					$kategoriad=trim($kategoriad);

								 					if($kategorisay==0)
								 					{
								 						//$ustkategoriid=teksatir("SELECT kategoriid FROM kategori WHERE kategorisil='0' and kategoriaktif='1' and kategoriad='". addslashes($kategoriad)."'","kategoriid");

								 						if(!dogrula("kategori","kategorisil='0' and kategoriaktif='1' and kategorigrup='7' and kategoriad='". addslashes($kategoriad)."'"))
								 						{
															$sutunlar="dilid,kategoritariholustur,kategoritarihguncel,ustkategoriid,kategorikatman,kategoriad,kategorigrup,kategoriaktif,kategorisil,benzersizid";
															$simdi=date("Y-m-d H:i:s");$benzersizid=SifreUret(20,2);
															$degerler=$dilid."|*_".$simdi."|*_".$simdi."|*_0|*_0|*_".addslashes($kategoriad)."|*_7|*_1|*_0|*_".$benzersizid;
								 							ekle($sutunlar,$degerler,"kategori","34");
								 							$ustkategoriid=teksatir("SELECT kategoriid FROM kategori WHERE benzersizid='". $benzersizid ."'","kategoriid");
								 							ekle("benzersizid,baslik,aciklama,kelime,link",$benzersizid."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_"."/".duzelt($kategoriad)."/".$ustkategoriid."m.html");
								 							//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir=".$say));
								 						}
								 					}
								 					elseif($kategorisay==1)
								 					{
								 						//$kategoriid=teksatir("SELECT kategoriid FROM kategori WHERE kategorisil='0' and kategoriaktif='1' and kategoriad='". addslashes($kategoriad)."'","kategoriid");

								 						if(!dogrula("kategori","kategorisil='0' and kategoriaktif='1' and kategorigrup='7' and kategoriad='". addslashes($kategoriad)."'"))
								 						{
															$sutunlar="dilid,kategoritariholustur,kategoritarihguncel,ustkategoriid,kategorikatman,kategoriad,kategorigrup,kategoriaktif,kategorisil,benzersizid";
															$simdi=date("Y-m-d H:i:s");$benzersizid=SifreUret(20,2);
															$degerler=$dilid."|*_".$simdi."|*_".$simdi."|*_".S($ustkategoriid)."|*_1|*_".addslashes($kategoriad)."|*_7|*_1|*_0|*_".$benzersizid;
								 							ekle($sutunlar,$degerler,"kategori","34");
								 							$kategoriid=teksatir("SELECT kategoriid FROM kategori WHERE benzersizid='". $benzersizid ."'","kategoriid");
								 							ekle("benzersizid,baslik,aciklama,kelime,link",$benzersizid."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_"."/".duzelt($kategoriad)."/".$kategoriid."m.html");
								 							//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir=".$say));
								 						}
								 					}
								 				}
								 			}
								 			else
								 			{
								 				$kategoriad=trim($sutun);
								 				//$kategoriid=teksatir("SELECT kategoriid FROM kategori WHERE kategorisil='0' and kategoriaktif='1' and kategorigrup='7' and kategoriad='". addslashes($kategoriad)."'","kategoriid");
								 				if(!dogrula("kategori","kategorisil='0' and kategoriaktif='1' and kategorigrup='7' and kategoriad='". addslashes($kategoriad)."'"))
						 						{
													$sutunlar="dilid,kategoritariholustur,kategoritarihguncel,ustkategoriid,kategorikatman,kategoriad,kategorigrup,kategoriaktif,kategorisil,benzersizid";
													$simdi=date("Y-m-d H:i:s");$benzersizid=SifreUret(20,2);
													$degerler=$dilid."|*_".$simdi."|*_".$simdi."|*_0|*_0|*_".addslashes($kategoriad)."|*_7|*_1|*_0|*_".$benzersizid;
						 							ekle($sutunlar,$degerler,"kategori","34");
						 							$kategoriid=teksatir("SELECT kategoriid FROM kategori WHERE benzersizid='". $benzersizid ."'","kategoriid");
						 							ekle("benzersizid,baslik,aciklama,kelime,link",$benzersizid."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_"."/".addslashes($kategoriad)."/".$kategoriid."m.html");
						 							//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir=".$say));
						 						}
								 			}
								 		}
								 	}
								 	if($saybitir==100)
								 	{
								 		die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir='.$satirsay.'";</script>');
								 		//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir=".$satirsay));
								 	}
							 	}
							 	if($satirsay==($satirtoplam-1))
						 		{
						 			echo '<h3>Kategori Aktarım Tamamlandı</h3>';
						 			die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=3&dosya=/m/r/havuz/urun.xlsx";</script>');
						 			//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=3&dosya=/m/r/havuz/urun.xlsx"));
						 		}		
							}
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==3 && !BosMu(q("dosya")))
					{
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						$xlsx="";$excelveri="";$satir="";
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							$excelveri=$xlsx->rows();
							$satirtoplam=count($excelveri);
							$saybasla=0;$saybitir=0;
							if(s(q("satir"))!=0)$saybasla=S(q("satir"));
							foreach ($excelveri as $satirsay => $satir)
							{ 	
							 	if($satirsay==0)echo "<h3>Markalar Alınıyor ($satirtoplam) Ürün</h3>";
							 	
							 	if($satirsay>$saybasla)
							 	{
							 		$saybitir++;
							 		foreach ($satir as $sutunsay => $sutun)
								 	{
								 		if($sutunsay==2 && !BosMu($sutun))//marka ekle
								 		{
								 			//$markaad=$sutun;
								 			//$markaid=teksatir("SELECT markaid FROM urunmarka WHERE markasil='0' and markaad='". addslashes($sutun)."'","markaid");
							 				if(!Dogrula("urunmarka","markasil='0' and markaad='". addslashes($sutun)."'"))
					 						{
												$sutunlar="markatariholustur,markatarihguncel,markaad,markaindirim,markataksit,markapromosyontutari,markasil,benzersizid";
												$simdi=date("Y-m-d H:i:s");$benzersizid=SifreUret(20,2);
												$degerler=$simdi."|*_".$simdi."|*_".addslashes($sutun)."|*_0|*_0|*_0|*_0|*_".$benzersizid;
					 							ekle($sutunlar,$degerler,"urunmarka",34);
					 							//$markaid=teksatir("SELECT markaid FROM urunmarka WHERE benzersizid='". $benzersizid ."'","markaid");
					 							//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&sutun=2&say=".$say));
					 						}
								 		}
								 	}
								 	if($saybitir==100)
								 	{
								 		die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=3&dosya=/m/r/havuz/urun.xlsx&satir='.$satirsay.'";</script>');
								 		//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir=".$satirsay));
								 	}
							 	}
							 	if($satirsay==($satirtoplam-1))
						 		{
						 			echo '<h3>Marka Aktarım Tamamlandı</h3>';
						 			die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=4&dosya=/m/r/havuz/urun.xlsx";</script>');
						 			//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=3&dosya=/m/r/havuz/urun.xlsx"));
						 		}		
							}
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==4 && !BosMu(q("dosya")))
					{
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						$xlsx="";$excelveri="";$satir="";
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							$excelveri=$xlsx->rows();
							$satirtoplam=count($excelveri);
							$saybasla=0;$saybitir=0;
							if(s(q("satir"))!=0)$saybasla=S(q("satir"));
							foreach ($excelveri as $satirsay => $satir)
							{ 	
							 	if($satirsay==0)echo "<h3>Para Birimleri Alınıyor ($satirtoplam) Ürün</h3>";
							 	
							 	if($satirsay>$saybasla)
							 	{
							 		$saybitir++;
							 		foreach ($satir as $sutunsay => $sutun)
								 	{
								 		if($sutunsay==14 && !BosMu($sutun))//parabirim
								 		{
								 			$parabirim=$sutun;
								 			if(!Dogrula("urunparabirim","parabirimad='".addslashes($parabirim)."'"))
								 			{
								 				ekle("parabirimad,parabirimsimge,parabirimkod,parabirimsil",addslashes($parabirim)."|*_@|*_xxx|*_0","urunparabirim",0);
								 				$parabirimid=teksatir("SELECT parabirimid FROM urunparabirim WHERE parabirimad='".addslashes($parabirim)."' and parabirimsil='0' and parabirimsimge='@'","parabirimid");
								 			}
								 		}
								 	}
								 	if($saybitir==100)
								 	{
								 		die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=4&dosya=/m/r/havuz/urun.xlsx&satir='.$satirsay.'";</script>');
								 		//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir=".$satirsay));
								 	}
							 	}
							 	if($satirsay==($satirtoplam-1))
						 		{
						 			echo '<h3>Para Birim Aktarım Tamamlandı</h3>';
						 			die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=5&dosya=/m/r/havuz/urun.xlsx";</script>');
						 			//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=3&dosya=/m/r/havuz/urun.xlsx"));
						 		}		
							}
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==5 && !BosMu(q("dosya")))
					{
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						$xlsx="";$excelveri="";$satir="";
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							$excelveri=$xlsx->rows();
							$satirtoplam=count($excelveri);
							$saybasla=0;$saybitir=0;
							if(s(q("satir"))!=0)$saybasla=S(q("satir"));
							foreach ($excelveri as $satirsay => $satir)
							{ 	
							 	if($satirsay==0)echo "<h3>Renkler Alınıyor ($satirtoplam) Ürün</h3>";
							 	
							 	if($satirsay>$saybasla)
							 	{
							 		$saybitir++;
							 		foreach ($satir as $sutunsay => $sutun)
								 	{
								 		if($sutunsay==15 && !BosMu($sutun))
								 		{
								 			$renk=$sutun;
								 			if(!Dogrula("urunrenk","urunrenkad='".addslashes($renk)."'"))
								 			{
								 				if(!Dogrula("urunrenkgrup","urunrenkgrupad='RENKLER'"))
									 			{
									 				ekle("benzersizid,urunrenkgrupad,urunrenkgrupsil","000000.renkler000000|*_RENKLER|*_0","urunrenkgrup",0);
									 			}
									 			$urunrenkgrupid=teksatir("SELECT urunrenkgrupid From urunrenkgrup Where benzersizid='000000.renkler000000'","urunrenkgrupid");
									 			ekle("urunrenkad,urunrenkgrupid,urunrenksil",$renk."|*_".$urunrenkgrupid."|*_0","urunrenk",0);
								 			}
								 		}
								 	}
								 	if($saybitir==100)
								 	{
								 		die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=5&dosya=/m/r/havuz/urun.xlsx&satir='.$satirsay.'";</script>');
								 		//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx&satir=".$satirsay));
								 	}
							 	}
							 	if($satirsay==($satirtoplam-1))
						 		{
						 			echo '<h3>Renk Aktarım Tamamlandı</h3>';
						 			die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=6&dosya=/m/r/havuz/urun.xlsx";</script>');
						 			//exit(header("Location: /_y/s/s/excel/excel-urun-yukle.php?adim=3&dosya=/m/r/havuz/urun.xlsx"));
						 		}		
							}
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==6 && !BosMu(q("dosya")))
					{
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						$xlsx="";$excelveri="";$satir="";
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							$excelveri=$xlsx->rows();
							$satirtoplam=count($excelveri);
							$saybasla=0;$saybitir=0;
							if(s(q("satir"))!=0)$saybasla=S(q("satir"));
							foreach ($excelveri as $satirsay => $satir)
							{ 	
							 	if($satirsay==0)echo "<h3>Bedenler Alınıyor ($satirtoplam) Ürün</h3>";
							 	
							 	if($satirsay>$saybasla)
							 	{
							 		$saybitir++;
							 		foreach ($satir as $sutunsay => $sutun)
								 	{
								 		if($sutunsay==16 && !BosMu($sutun))
								 		{
								 			$beden=$sutun;
								 			if(!Dogrula("urunbeden","urunbedenad='".addslashes($beden)."'"))
								 			{
								 				if(!Dogrula("urunbedengrup","urunbedengrupad='BEDENLER'"))
									 			{
									 				ekle("benzersizid,urunbedengrupad,urunbedengrupsil","00000.bedenler000000|*_BEDENLER|*_0","urunbedengrup",0);
									 			}
									 			$urunbedengrupid=teksatir("SELECT urunbedengrupid From urunbedengrup Where benzersizid='00000.bedenler000000'","urunbedengrupid");
									 			ekle("urunbedenad,urunbedengrupid,urunbedensil",$beden."|*_".$urunbedengrupid."|*_0","urunbeden",0);
								 			}
								 		}
								 	}
								 	if($saybitir==100)
								 	{
								 		die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=6&dosya=/m/r/havuz/urun.xlsx&satir='.$satirsay.'";</script>');
								 	}
							 	}
							 	if($satirsay==($satirtoplam-1))
						 		{
						 			echo '<h3>Beden Aktarım Tamamlandı</h3>';
						 			die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=7&dosya=/m/r/havuz/urun.xlsx";</script>');
						 		}		
							}
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==7 && !BosMu(q("dosya")))
					{
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						$xlsx="";$excelveri="";$satir="";
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							$excelveri=$xlsx->rows();
							$satirtoplam=count($excelveri);
							$saybasla=0;$saybitir=0;
							foreach ($excelveri as $satirsay => $satir)
							{ 	
							 	
							 	if($satirsay==0)echo "<h3>Aktarım Başlatılıyor ($satirtoplam) Ürün</h3>";
							 	if(s(q("satir"))!=0)$saybasla=q("satir");
							 	if($satirsay>$saybasla)
							 	{
							 		//////////////////////////////////////////////
							 		$saybitir++;
							 		$simdi=date("Y-m-d H:i:s");
							 		$sayfaid=0;$kategoriid=0;$ustkategoriid=0;$markaid=0;$stokkodu="";$model="";$baslik="";$altbaslik="";
							 		$aciklama="";$fiyat="0.00";$satisbaslangictarih=$simdi;
							 		$satisbitistarih=date("Y-m-d",strtotime(date("Y-m-d") . " + 365 day"));$stok=0;
							 		$aktif=0;$urunparabirimid=1;$parabirim="TL";$renk="";$beden="";$resimler="";
							 		$urungrupid=0;$tedarikciid=1;$urunalisfiyat="0.00";$urunindirimsizfiyat="0.00";
							 		$urunbayifiyat="0.00";$urunkdv=$kdvgenel;$urunhediye="";$uruntaksit=$taksitgenel;$urunkargosuresi=3;
							 		$urunsabitkargoucreti="0.00";$uruneskifiyatgoster=$eskifiyatgenel;$urunindirimorani="0";
							 		$urunanasayfa=0;$urunindirimde=0;$urunyeni=0;$uruntopluindirim=1;$urunanindakargo=0;
							 		$urunucretsizkargo=1;$urunonsiparis=0;$urunfiyatsor=1;$urunkargo=0;
							 		$urunparabirim=$parabirimgenel;$urungununfirsati=0;$urunkredikarti=$kredikartigenel;
							 		$urunkapidaodeme=$kapidaodemegenel;$urunhavaleodeme=$havaleodemegenel;$urunsatisadet=0;
							 		$urunindirimoranigoster=0;$urunbedenid=0;$urunbedengrupid=0;$urunrenkgrupid=0;
							 		$urunrenkid=0;$resimklasor=2;
							 		//////////////////////////////////////////////
							 		foreach ($satir as $sutunsay => $sutun)
								 	{
								 		if(s(q("sutun"))!=0)$sutunsay=q("sutun");
								 		if($sutunsay==0 && !BosMu($sutun))
								 		{
								 			$sayfaid=S($sutun);
								 		}
								 		elseif($sutunsay==1 && !BosMu($sutun))//kategori ekle
								 		{
								 			$kategoriler=explode(">",$sutun);
								 			$kategoritoplam=count($kategoriler);
								 			if($kategoritoplam>1)
								 			{
								 				$ustkategoriid=0;
								 				for ($kategorisay=0; $kategorisay < $kategoritoplam; $kategorisay++)
								 				{
								 					$kategoriad=$kategoriler[$kategorisay];
								 					$kategoriad=trim($kategoriad);

								 					if($kategorisay==0)
								 					{
								 						$ustkategoriid=teksatir("SELECT kategoriid FROM kategori WHERE kategorisil='0' and kategoriaktif='1' and kategoriad='". addslashes($kategoriad)."'","kategoriid");
								 					}
								 					elseif($kategorisay==1)
								 					{
								 						$kategoriid=teksatir("SELECT kategoriid FROM kategori WHERE kategorisil='0' and kategoriaktif='1' and kategoriad='". addslashes($kategoriad)."'","kategoriid");
								 					}
								 				}
								 			}
								 			else
								 			{
								 				$kategoriad=trim($sutun);
								 				$kategoriid=teksatir("SELECT kategoriid FROM kategori WHERE kategorisil='0' and kategoriaktif='1' and kategorigrup='7' and kategoriad='". addslashes($kategoriad)."'","kategoriid");
								 			}
								 		}
								 		elseif($sutunsay==2 && !BosMu($sutun))//marka ekle
								 		{
								 			$markaad=$sutun;
								 			$markaid=teksatir("SELECT markaid FROM urunmarka WHERE markasil='0' and markaad='". addslashes($sutun)."'","markaid");
								 		}
								 		elseif($sutunsay==3 && !BosMu($sutun))
								 		{
								 			$stokkodu=$sutun;
								 		}
								 		elseif($sutunsay==4 && !BosMu($sutun))
								 		{
								 			$model=$sutun;
								 		}
								 		elseif($sutunsay==5 && !BosMu($sutun))
								 		{
								 			$baslik=$sutun;
								 		}
								 		elseif($sutunsay==6 && !BosMu($sutun))
								 		{
								 			$altbaslik=$sutun;
								 		}
								 		elseif($sutunsay==7 && !BosMu($sutun))
								 		{
								 			$aciklama=$sutun;
								 		}
								 		elseif($sutunsay==8 && !BosMu($sutun))//fiyat
								 		{
								 			$fiyat=$sutun;
								 			//$urunindirimsizfiyat=round(($fiyat*1.3),2);
								 			//$fiyat=str_replace(",", ".", $fiyat);
								 			//$urunindirimsizfiyat=str_replace(",", ".", $urunindirimsizfiyat);
								 			//die("f:$fiyat if: $urunindirimsizfiyat");
								 		}
								 		elseif($sutunsay==9 && !BosMu($sutun))//fiyat
								 		{
								 			$urunindirimsizfiyat=$sutun;
								 		}
								 		elseif($sutunsay==10 && !BosMu($sutun))
								 		{
								 			$satisbaslangictarih=$sutun;
								 		}
								 		elseif($sutunsay==11 && !BosMu($sutun))
								 		{
								 			$satisbitistarih=$sutun;
								 		}
								 		elseif($sutunsay==12 && !BosMu($sutun))//stok sayısı
								 		{
								 			$stok=$sutun;
								 		}
								 		elseif($sutunsay==13 && !BosMu($sutun))
								 		{
								 			$aktif=$sutun;
								 		}
								 		elseif($sutunsay==14 && !BosMu($sutun))//parabirim
								 		{
								 			$parabirim=$sutun;
								 			$urunparabirimid=teksatir("SELECT parabirimid FROM urunparabirim WHERE parabirimad='".$parabirim."' and parabirimsil='0'","parabirimid");
								 			//die("P:$urunparabirimid ");								 			
								 		}
								 		elseif($sutunsay==15 && !BosMu($sutun))
								 		{
								 			$renk=$sutun;
								 			$urunrenkid=teksatir("SELECT urunrenkid FROM urunrenk WHERE urunrenkad='".$renk."' and urunrenksil='0'","urunrenkid");
								 			$urunrenkgrupid=teksatir("SELECT urunrenkgrupid From urunrenk Where urunrenkid='".$urunrenkid."'","urunrenkgrupid");
								 			//die("r:$urunrenkid rg:$urunrenkgrupid ");
								 		}
								 		elseif($sutunsay==16 && !BosMu($sutun))
								 		{
								 			$beden=$sutun;
								 			$urunbedenid=teksatir("SELECT urunbedenid FROM urunbeden WHERE urunbedenad='".$beden."' and urunbedensil='0'","urunbedenid");
								 			$urunbedengrupid=teksatir("SELECT urunbedengrupid From urunbeden Where urunbedenid='".$urunbedenid."'","urunbedengrupid");
								 			//die("b:$urunbedenid bg:$urunbedengrupid ");
								 		}
								 		elseif($sutunsay==17 && !BosMu($sutun))
								 		{
								 			$resimler="";
								 			$resimler=$sutun;
								 			$resimler=str_replace(',', '.jpg,', $resimler);
								 			$resimler=$resimler.".jpg";
								 			$resimler=str_replace('"', '', $resimler);
								 		}
								 		//echo "S) $sutunsay <br>";
								 		$sutunlar="";$degerler="";
								 		if($sutunsay==17)
								 		{
								 			if($kategoriid!=0 && $markaid!=0 && !BosMu($stokkodu) && !BosMu($model) && !BosMu($baslik))
									 		{
									 			//echo "$satirsay ) $kategoriad ($kategoriid) | $markaad ($markaid) | $model | s: $stokkodu | $baslik <br>";
									 			/*$sayfaid=teksatir("
									 				SELECT 
									 					sayfa.sayfaid 
									 				FROM 
									 					sayfa 
									 						inner join urunozellikleri on 
									 							urunozellikleri.sayfaid=sayfa.sayfaid 
									 				WHERE 
									 					sayfaad='".addslashes($baslik)."' and 
									 					sayfatip='7' and 
									 					sayfaaktif='1' and 
									 					sayfasil='0' and 
									 					urunmodel='".addslashes($model)."' and 
									 					urunrenkid='".$urunrenkid."'
									 			","sayfaid");*/
									 			//echo "sayfaid: $sayfaid <br>";
									 			
									 			/*$simdi=date("Y-m-d H:i:s");
									 			$sutunlar="sayfatarihguncel,sayfatip,sayfaad,sayfaicerik,sayfalink,sayfasira,sayfaaktif,sayfasil";
									 			$degerler=$simdi."|*_7|*_".addslashes($baslik)."|*_".addslashes($aciklama)."|*_|*_0|*_1|*_0";
									 			
									 			if(S($sayfaid)!=0)
									 			{
									 				guncelle($sutunlar,$degerler,"sayfa","sayfaid='".$sayfaid."'",37);
									 				$benzersizid=teksatir("SELECT benzersizid FROM sayfa WHERE sayfaid='".$sayfaid."'","benzersizid");
									 				echo "Sayfa Güncellendi ID: $sayfaid<br>";
									 				$data->query("DELETE FROM sayfalistekategori WHERE sayfaid='". $sayfaid ."'");
													//$data->query("DELETE FROM urunozellikleri WHERE sayfaid='". $sayfaid ."'");
													$data->query("DELETE FROM seo WHERE benzersizid='". $benzersizid ."'");
													echo "sayfaid adım 3<br>";
									 			}
									 			else
									 			{
									 				$benzersizid=SifreUret(20,2);
									 				$sutunlar="benzersizid,sayfatariholustur,".$sutunlar;
									 				$degerler=$benzersizid."|*_".$simdi."|*_".$degerler;
									 				
									 				ekle($sutunlar,$degerler,"sayfa",37);
										 			$sayfaid=teksatir("SELECT sayfaid FROM sayfa WHERE benzersizid='".$benzersizid."'","sayfaid");
										 			echo "sayfaid adım 4<br>";
										 			
									 				echo "Sayfa Eklendi ID: ($sayfaid)<br>";
									 			}*/
									 			//die("id: $sayfaid");
									 			/*if(!Dogrula("sayfalistekategori","sayfaid='".$sayfaid."' and kategoriid='".$kategoriid."'"))ekle("kategoriid,sayfaid",$kategoriid."|*_".$sayfaid,"sayfalistekategori",56);
									 			echo "Kategori Listesi Eklendi <br>";
												$seobaslik=mb_substr(addslashes($baslik),0,65,"UTF-8");
									 			$seoaciklama="Kategori: $kategoriad, Marka: $markaad, Model: $model, Ürün: $seobaslik, Fiyat:$fiyat $parabirim";
									 			$seokelime="$kategoriad,$markaad,$model,$fiyat";
									 			$seolink=DuzeltS(K($kategoriad."/".$markaad."/".$seobaslik))."/".$sayfaid."s.html";
									 			if(S($sayfaid)!=0 and !Dogrula("seo","baslik='".$seobaslik."' and aciklama='".$seoaciklama."' and kelime='".$seokelime."' and link='".$seolink."'"))ekle("benzersizid,baslik,aciklama,kelime,link,resim",$benzersizid."|*_".$seobaslik."|*_".$seoaciklama."|*_".$seokelime."|*_".$seolink."|*_","seo",56);
									 			echo "SEO Eklendi<br>";*/
									 			//die("resim: $resimler");
									 			
									 			/*if(!BosMu($resimler))
									 			{
									 				$resimayikla=explode(",", $resimler);
										 			$resimtoplam=count($resimayikla);
										 			
										 			if(S($resimtoplam)>1)
										 			{
										 				for ($ri=0; $ri < $resimtoplam; $ri++)
										 				{ 
										 					$resim=$resimayikla[$ri];
										 					$orjinal=$resim;
										 					if(substr($resim, 0, 4)=="http")
										 					{
																//$resimek=SifreUret(5,2);
																//$uzanti=substr($resim, strrpos($resim, "."), strlen($resim)-strrpos($resim, "."));
																//$img = $_SERVER['DOCUMENT_ROOT'].'/m/r/urun/'.Duzelt(K($seobaslik))."_".$resimek.$uzanti;
																//file_put_contents($img, file_get_contents($resim));
																//$resim=Duzelt(K($seobaslik))."_".$resimek.$uzanti;
										 					}
										 					$sutunlar="";$degerler="";
										 					$benzersizid=SifreUret(20,2);
										 					$sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
										 					$degerler=$resimklasor."|*_".addslashes($seobaslik)."|*_".$resim."|*_0|*_0|*_".$benzersizid."|*_".$orjinal;
										 					if(!Dogrula("resim","orjinal='".$orjinal."'"))
										 					{
										 						if(file_exists($anadizin . "/m/r/urun/".$resim))
										 						{
																	ekle($sutunlar,$degerler,"resim",26);
												 					$resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$benzersizid."'","resimid");
												 					echo "Resim ($resim) Eklendi ResimID: $resimid<br>";
												 					ekle("sayfaid,resimid",$sayfaid."|*_".$resimid,"sayfalisteresim",26);
												 					echo "Sayfa Resim Liste Eklendi SayfaID: $sayfaid ResimID: $resimid<br>";
										 						}
										 						
										 					}
										 					else
										 					{
										 						$resimid=teksatir("SELECT resimid FROM resim WHERE orjinal='".$orjinal."'","resimid");
										 						if(file_exists($anadizin . "/m/r/urun/".$resim))
										 						{
										 							ekle("sayfaid,resimid",$sayfaid."|*_".$resimid,"sayfalisteresim",26);
										 						}
										 						else
										 						{
										 							$data->query("DELETE FROM sayfalisteresim WHERE resimid='". $resimid ."'");
																	$data->query("DELETE FROM resimgaleriliste WHERE resimid='". $resimid ."'");
																	$data->query("UPDATE kategori SET resimid='0' WHERE resimid='". $resimid ."'");
																	$data->query("DELETE FROM resim WHERE resimid='". $resimid ."'");
										 						}
										 					}
										 				}
										 			}
										 			else
										 			{
										 				$orjinal=$resimler;
										 				if(substr($resimler, 0, 4)=="http")
									 					{
															//$resimek=SifreUret(5,2);
															//$uzanti=substr($resimler, strrpos($resimler, "."), strlen($resimler)-strrpos($resimler, "."));
															//$img = $_SERVER['DOCUMENT_ROOT'].'/m/r/urun/'.Duzelt(K($seobaslik))."_".$resimek.$uzanti;
															//file_put_contents($img, file_get_contents($resimler));
															//$resimler=Duzelt(K($seobaslik))."_".$resimek.$uzanti;
									 					}
									 					$sutunlar="";$degerler="";
									 					$benzersizid=SifreUret(20,2);
									 					$sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
									 					$degerler=$resimklasor."|*_".addslashes($seobaslik)."|*_".$resimler."|*_0|*_0|*_".$benzersizid."|*_".$orjinal;
									 					if(!Dogrula("resim","orjinal='".$orjinal."'"))
									 					{
									 						ekle($sutunlar,$degerler,"resim",26);
										 					$resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$benzersizid."'","resimid");
										 					echo "Resim (n:$resimler) Eklendi ResimID: $resimid<br>";
											 				ekle("sayfaid,resimid",$sayfaid."|*_".$resimid,"sayfalisteresim",26);
											 				echo "Sayfa Resim Liste Eklendi SayfaID: $sayfaid ResimID: $resimid<br>";
									 					}
										 			}
									 			}*/
										 		
										 		/*if(!dogrula("urunozellikleri","sayfaid='".$sayfaid."' and urunstokkodu='".$stokkodu."'"))
										 		{*/
										 			/*$satisbitistarih = date("Y-m-d", strtotime($satisbitistarih));
										 			$sutunlar="sayfaid,urungrupid,markaid,tedarikciid,urunmodel,urunstokkodu,urunstok,urunsatisfiyat,urunalisfiyat,urunindirimsizfiyat,urunbayifiyat,urunkdv,urunhediye,uruntaksit,urunaciklama,urunkargosuresi,urunsabitkargoucreti,uruneskifiyatgoster,urunindirimorani,urunfiyatsontarih,urunanasayfa,urunindirimde,urunyeni,uruntopluindirim,urunanindakargo,urunucretsizkargo,urunonsiparis,urunfiyatsor,urunkargo,urunparabirim,urungununfirsati,urunkredikarti,urunkapidaodeme,urunhavaleodeme,urunsatisadet,urunindirimoranigoster,urunbedenid,urunbedengrupid,urunrenkgrupid,urunrenkid";

										 			$degerler=$sayfaid."|*_".$urunrenkgrupid."|*_".$markaid."|*_".$tedarikciid."|*_".$model."|*_".$stokkodu."|*_".$stok."|*_".$fiyat."|*_".$urunalisfiyat."|*_".$urunindirimsizfiyat."|*_".$urunbayifiyat."|*_".$urunkdv."|*_".$urunhediye."|*_".$uruntaksit."|*_".$altbaslik."|*_".$urunkargosuresi."|*_".$urunsabitkargoucreti."|*_".$uruneskifiyatgoster."|*_".$urunindirimorani."|*_".$satisbitistarih."|*_".$urunanasayfa."|*_".$urunindirimde."|*_".$urunyeni."|*_".$uruntopluindirim."|*_".$urunanindakargo."|*_".$urunucretsizkargo."|*_".$urunonsiparis."|*_".$urunfiyatsor."|*_".$urunkargo."|*_".$urunparabirimid."|*_".$urungununfirsati."|*_".$urunkredikarti."|*_".$urunkapidaodeme."|*_".$urunhavaleodeme."|*_".$urunsatisadet."|*_".$urunindirimoranigoster."|*_".$urunbedenid."|*_".$urunbedengrupid."|*_".$urunrenkid."|*_".$urunrenkgrupid;*/

										 			//ekle($sutunlar,$degerler,"urunozellikleri",63);

										 			//echo $satisbaslangictarih."|*_".$satisbitistarih."<br>";
										 			$sutunlar="urunid,kategoriid,marka,model,stokkodu,baslik,altbaslik,aciklama,fiyat,indirimsizfiyat,satisbaslangictarih,satisbitistarih,stok,aktif,parabirimid,renkgrupid,renkid,bedengrupid,bedenid,resim,aktarimonay";
										 			$degerler=$sayfaid."|*_".$kategoriid."|*_".$markaid."|*_".addslashes($model)."|*_".addslashes($stokkodu)."|*_".addslashes($baslik)."|*_".addslashes($altbaslik)."|*_".addslashes($aciklama)."|*_".$fiyat."|*_".$urunindirimsizfiyat."|*_".$satisbaslangictarih."|*_".$satisbitistarih."|*_".$stok."|*_".$aktif."|*_".$urunparabirimid."|*_".$urunrenkgrupid."|*_".$urunrenkid."|*_".$urunbedengrupid."|*_".$urunbedenid."|*_".addslashes($resimler)."|*_0";
										 			if(dogrula("urunaktar","stokkodu='".$stokkodu."'"))
										 			{
										 				guncelle($sutunlar,$degerler,"urunaktar","stokkodu='".$stokkodu."'",63);
										 			}
										 			else
										 			{
										 				ekle($sutunlar,$degerler,"urunaktar",63);
										 			}
										 			//echo "$sayfaid ürünü $stokkodu Ürün Özellikleri Eklendi<br><hr><br>";
										 		/*}
										 		else
										 		{
										 			guncelle($sutunlar,$degerler,"urunozellikleri","urunstokkodu='".$stokkodu."'",63);
										 		}*/
									 		}
									 		$resimler="";$orjinal="";$sayfaid=0;$kategoriid=0;$markaid=0;$stokkodu="";$model="";$baslik="";
								 		}	
								 	}

								 	if($saybitir==500)
								 	{
								 		//die("bitti");
								 		die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=7&dosya=/m/r/havuz/urun.xlsx&satir='.$satirsay.'";</script>');
								 	}
							 	}
							 	if($satirsay==($satirtoplam-1))
							 	{
							 		echo '<h3>Aktarım Tamamlandı</h3>';
							 		die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=8";</script>');
							 	}
							}
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==8)
					{
						$aktarim_s="
							SELECT 
								urunid,urunaktar.kategoriid,marka,model,stokkodu,baslik,altbaslik,aciklama,fiyat,indirimsizfiyat,
								satisbaslangictarih,satisbitistarih,stok,aktif,urunaktar.parabirimid,
								renkgrupid,renkid,bedengrupid,bedenid,resim,
								kategoriad,
								markaad,
								parabirimad
							FROM 
								urunaktar
								INNER JOIN kategori 
									ON kategori.kategoriid=urunaktar.kategoriid
								INNER JOIN urunmarka 
									ON urunmarka.markaid=urunaktar.marka
								INNER JOIN urunparabirim 
									ON urunparabirim.parabirimid=urunaktar.parabirimid
							WHERE
								aktarimonay='0'
							GROUP BY
								aktarimid
							ORDER BY
								aktarimid ASC
							LIMIT 1,1
						";
						if($data->query($aktarim_s))
						{
							$aktarim_v=$data->query($aktarim_s);unset($aktarim_s);
							if($aktarim_v->num_rows>0)
							{
								$simdi=date("Y-m-d H:i:s");
						 		$urunid=0;$kategoriid=0;$ustkategoriid=0;$markaid=0;$stokkodu="";$model="";$baslik="";$altbaslik="";
						 		$aciklama="";$fiyat="0.00";$satisbaslangictarih=$simdi;
						 		$satisbitistarih=date("Y-m-d",strtotime(date("Y-m-d") . " + 365 day"));$stok=0;
						 		$aktif=0;$urunparabirimid=1;$parabirim="TL";$renk="";$beden="";$resimler="";
						 		$urungrupid=0;$tedarikciid=1;$urunalisfiyat="0.00";$urunindirimsizfiyat="0.00";
						 		$urunbayifiyat="0.00";$urunkdv=$kdvgenel;$urunhediye="";$uruntaksit=$taksitgenel;$urunkargosuresi=3;
						 		$urunsabitkargoucreti="0.00";$uruneskifiyatgoster=$eskifiyatgenel;$urunindirimorani="0";
						 		$urunanasayfa=0;$urunindirimde=0;$urunyeni=0;$uruntopluindirim=1;$urunanindakargo=0;
						 		$urunucretsizkargo=1;$urunonsiparis=0;$urunfiyatsor=1;$urunkargo=0;
						 		$urunparabirim=$parabirimgenel;$urungununfirsati=0;$urunkredikarti=$kredikartigenel;
						 		$urunkapidaodeme=$kapidaodemegenel;$urunhavaleodeme=$havaleodemegenel;$urunsatisadet=0;
						 		$urunindirimoranigoster=0;$urunbedenid=0;$urunbedengrupid=0;$urunrenkgrupid=0;
						 		$urunrenkid=0;$resimklasor=2;
								while ($aktarim_t=$aktarim_v->fetch_assoc())
								{
									$urunid=$aktarim_t["urunid"];
									$kategoriid=$aktarim_t["kategoriid"];
									$marka=$aktarim_t["marka"];
									$model=$aktarim_t["model"];
									$stokkodu=$aktarim_t["stokkodu"];
									$baslik=$aktarim_t["baslik"];
									$altbaslik=$aktarim_t["altbaslik"];
									$aciklama=$aktarim_t["aciklama"];
									$fiyat=$aktarim_t["fiyat"];
									$urunindirimsizfiyat=$aktarim_t["indirimsizfiyat"];
									$satisbaslangictarih=$aktarim_t["satisbaslangictarih"];
									$satisbitistarih=$aktarim_t["satisbitistarih"];
									$stok=$aktarim_t["stok"];
									$aktif=$aktarim_t["aktif"];
									$parabirimid=$aktarim_t["parabirimid"];
									$urunrenkgrupid=$aktarim_t["renkgrupid"];
									$urunrenkid=$aktarim_t["renkid"];
									$urunbedengrupid=$aktarim_t["bedengrupid"];
									$urunbedenid=$aktarim_t["bedenid"];
									$resimler=$aktarim_t["resim"];

									$kategoriad=$aktarim_t["kategoriad"];
									$markaad=$aktarim_t["markaad"];
									$parabirimad=$aktarim_t["parabirimad"];
									echo "Ürün id ". S($urunid) ." <br>";
								}
								if(S($urunid)==0)
								{
									$simdi=date("Y-m-d H:i:s");
								 	$sayfasutunlar="sayfatarihguncel,sayfatip,sayfaad,sayfaicerik,sayfalink,sayfasira,sayfaaktif,sayfasil";
								 	$sayfadegerler=$simdi."|*_7|*_".$baslik." ".$model."|*_".$aciklama."|*_|*_0|*_1|*_0";

									echo "Ürün id boş stoğa göre bakılıyor<br> $sayfadegerler <br>";

									$urunid=teksatir("SELECT sayfaid FROM sayfa WHERE sayfaad='".$baslik." ".$model."' and sayfatip='7' and sayfasil='0'","sayfaid");
									
									if(S($urunid)==0)
									{
										echo "Ürün id boş yeni sayfa ekleniyor<br>";
										$sayfabenzersizid=SifreUret(20,2);
						 				$sayfasutunlar="benzersizid,sayfatariholustur,".$sayfasutunlar;
						 				$sayfadegerler=$sayfabenzersizid."|*_".$simdi."|*_".$sayfadegerler;
						 				
						 				ekle($sayfasutunlar,$sayfadegerler,"sayfa",37);
							 			$urunid=teksatir("SELECT sayfaid FROM sayfa WHERE benzersizid='".$sayfabenzersizid."'","sayfaid");
							 			echo "Sayfa Eklendi. yeni ürün id: $urunid";
									}
									else
									{
										$sayfabenzersizid=teksatir("SELECT benzersizid FROM sayfa WHERE sayfaid='".$urunid."'","benzersizid");
									}
								}
								else
								{
									echo "Ürün id boş değil, sayfa güncelleme yapılacak<br>";
									guncelle($sayfasutunlar,$sayfadegerler,"sayfa","sayfaid='".$urunid."'",37);
							 		$sayfabenzersizid=teksatir("SELECT benzersizid FROM sayfa WHERE sayfaid='".$urunid."'","benzersizid");
								}

								if(!dogrula("sayfalistekategori","kategoriid='".$kategoriid."' and sayfaid='".$urunid."'"))
								{
									$data->query("DELETE FROM sayfalistekategori WHERE sayfaid='". $urunid ."'");
									ekle("kategoriid,sayfaid",$kategoriid."|*_".$urunid,"sayfalistekategori",56);
								}

								
								// RESİMLER ->
								if(!BosMu($resimler))
					 			{
					 				$resimayikla=explode(",", $resimler);
						 			$resimtoplam=count($resimayikla);
						 			$resimsutunlar="";$resimdegerler="";
						 			if(S($resimtoplam)>1)
						 			{
						 				for ($ri=0; $ri < $resimtoplam; $ri++)
						 				{ 
						 					$resim=$resimayikla[$ri];
						 					$orjinal=$resim;
						 					if(substr($resim, 0, 4)=="http")
						 					{
												//$resimek=SifreUret(5,2);
												//$uzanti=substr($resim, strrpos($resim, "."), strlen($resim)-strrpos($resim, "."));
												//$img = $_SERVER['DOCUMENT_ROOT'].'/m/r/urun/'.Duzelt(K($seobaslik))."_".$resimek.$uzanti;
												//file_put_contents($img, file_get_contents($resim));
												//$resim=Duzelt(K($seobaslik))."_".$resimek.$uzanti;
						 					}
						 					if(file_exists($anadizin . "/m/r/urun/".$resim))
						 					{
						 						if(!Dogrula("resim","orjinal='".$orjinal."'"))
							 					{
							 						$resimbenzersizid=SifreUret(20,2);
						 							$resimsutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
						 							$resimdegerler=$resimklasor."|*_".$baslik."|*_".$resim."|*_0|*_0|*_".$resimbenzersizid."|*_".$orjinal;
													ekle($resimsutunlar,$resimdegerler,"resim",26);
								 					$resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$resimbenzersizid."'","resimid");
								 					ekle("sayfaid,resimid",$urunid."|*_".$resimid,"sayfalisteresim",26);
							 						
							 					}
							 					else
							 					{
							 						$resimid=teksatir("SELECT resimid FROM resim WHERE orjinal='".$orjinal."'","resimid");
							 						if(!dogrula("sayfalisteresim","resimid='".$resimid."' and sayfaid='".$urunid."'")){ekle("sayfaid,resimid",$urunid."|*_".$resimid,"sayfalisteresim",26);}
							 					}
						 					}
						 					elseif(dogrula("resim","orjinal='".$orjinal."'"))
						 					{
						 						$resimid=teksatir("SELECT resimid FROM resim WHERE orjinal='".$orjinal."'","resimid");
						 						$data->query("DELETE FROM sayfalisteresim WHERE resimid='". $resimid ."'");
												$data->query("DELETE FROM resimgaleriliste WHERE resimid='". $resimid ."'");
												$data->query("UPDATE kategori SET resimid='0' WHERE resimid='". $resimid ."'");
												$data->query("DELETE FROM resim WHERE resimid='". $resimid ."'");
						 					}	
						 				}
						 			}
						 			else
						 			{
						 				$orjinal=$resimler;
						 				if(substr($resimler, 0, 4)=="http")
					 					{
											//$resimek=SifreUret(5,2);
											//$uzanti=substr($resimler, strrpos($resimler, "."), strlen($resimler)-strrpos($resimler, "."));
											//$img = $_SERVER['DOCUMENT_ROOT'].'/m/r/urun/'.Duzelt(K($seobaslik))."_".$resimek.$uzanti;
											//file_put_contents($img, file_get_contents($resimler));
											//$resimler=Duzelt(K($seobaslik))."_".$resimek.$uzanti;
					 					}
					 					if(file_exists($anadizin . "/m/r/urun/".$resimler))
					 					{
					 						if(!Dogrula("resim","orjinal='".$orjinal."'"))
						 					{
						 						$resimbenzersizid=SifreUret(20,2);
							 					$resimsutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
							 					$resimdegerler=$resimklasor."|*_".$baslik."|*_".$resimler."|*_0|*_0|*_".$resimbenzersizid."|*_".$orjinal;

						 						ekle($resimsutunlar,$resimdegerler,"resim",26);
							 					$resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$resimbenzersizid."'","resimid");
								 				ekle("sayfaid,resimid",$urunid."|*_".$resimid,"sayfalisteresim",26);
						 					}
						 					else
						 					{
						 						$resimid=teksatir("SELECT resimid FROM resim WHERE orjinal='".$orjinal."'","resimid");
						 						if(!dogrula("sayfalisteresim","resimid='".$resimid."' and sayfaid='".$urunid."'")){ekle("sayfaid,resimid",$urunid."|*_".$resimid,"sayfalisteresim",26);}			
						 					}
					 					}
							 			elseif(dogrula("resim","orjinal='".$orjinal."'"))
					 					{
					 						$resimid=teksatir("SELECT resimid FROM resim WHERE orjinal='".$orjinal."'","resimid");
					 						$data->query("DELETE FROM sayfalisteresim WHERE resimid='". $resimid ."'");
											$data->query("DELETE FROM resimgaleriliste WHERE resimid='". $resimid ."'");
											$data->query("UPDATE kategori SET resimid='0' WHERE resimid='". $resimid ."'");
											$data->query("DELETE FROM resim WHERE resimid='". $resimid ."'");
					 					}
						 			}
					 			}
								// RESİMLER <-

								$seobaslik=mb_substr($baslik,0,65,"UTF-8");
							 	$seoaciklama="Kategori: $kategoriad, Marka: $markaad, Model: $model, Ürün: $seobaslik, Fiyat:$fiyat $parabirimad";
							 	$seokelime="$kategoriad,$markaad,$model,$fiyat";
							 	$seolink=DuzeltS(K($kategoriad."/".$markaad."/".$seobaslik))."/".$urunid."s.html";
							 	if(!dogrula("seo","benzersizid='".$sayfabenzersizid."'"))
							 	{
							 		ekle("benzersizid,baslik,aciklama,kelime,link,resim",$sayfabenzersizid."|*_".$seobaslik."|*_".$seoaciklama."|*_".$seokelime."|*_".$seolink."|*_","seo",56);
							 	}
							 	else
							 	{
							 		guncelle("baslik,aciklama,kelime,link,resim",$seobaslik."|*_".$seoaciklama."|*_".$seokelime."|*_".$seolink."|*_","seo","benzersizid='".$sayfabenzersizid."'",56);
							 	}

							 	$sutunlar="sayfaid,urungrupid,markaid,tedarikciid,urunmodel,urunstokkodu,urunstok,urunsatisfiyat,urunalisfiyat,urunindirimsizfiyat,urunbayifiyat,urunkdv,urunhediye,uruntaksit,urunaciklama,urunkargosuresi,urunsabitkargoucreti,uruneskifiyatgoster,urunindirimorani,urunfiyatsontarih,urunanasayfa,urunindirimde,urunyeni,uruntopluindirim,urunanindakargo,urunucretsizkargo,urunonsiparis,urunfiyatsor,urunkargo,urunparabirim,urungununfirsati,urunkredikarti,urunkapidaodeme,urunhavaleodeme,urunsatisadet,urunindirimoranigoster,urunbedenid,urunbedengrupid,urunrenkgrupid,urunrenkid";

								 $degerler=$urunid."|*_".$urunrenkgrupid."|*_".$markaid."|*_".$tedarikciid."|*_".$model."|*_".$stokkodu."|*_".$stok."|*_".$fiyat."|*_".$urunalisfiyat."|*_".$urunindirimsizfiyat."|*_".$urunbayifiyat."|*_".$urunkdv."|*_".$urunhediye."|*_".$uruntaksit."|*_".$altbaslik."|*_".$urunkargosuresi."|*_".$urunsabitkargoucreti."|*_".$uruneskifiyatgoster."|*_".$urunindirimorani."|*_".$satisbitistarih."|*_".$urunanasayfa."|*_".$urunindirimde."|*_".$urunyeni."|*_".$uruntopluindirim."|*_".$urunanindakargo."|*_".$urunucretsizkargo."|*_".$urunonsiparis."|*_".$urunfiyatsor."|*_".$urunkargo."|*_".$urunparabirimid."|*_".$urungununfirsati."|*_".$urunkredikarti."|*_".$urunkapidaodeme."|*_".$urunhavaleodeme."|*_".$urunsatisadet."|*_".$urunindirimoranigoster."|*_".$urunbedenid."|*_".$urunbedengrupid."|*_".$urunrenkid."|*_".$urunrenkgrupid;
								$data->query("DELETE FROM urunozellikleri WHERE urunstokkodu='". $stokkodu ."'");
							 	ekle($sutunlar,$degerler,"urunozellikleri",63);

							 	guncelle("aktarimonay","1","urunaktar","stokkodu='".$stokkodu."'",65);
							 	
							 	echo 'ürünler ekleniyor... lütfen bekleyiniz...';
							 	die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=8";</script>');
							}
							else
							{
								die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=9";</script>');
							}
						}else{hatalogisle("aktarimliste",$data->error);}
					}
					?>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>
		<div id="ckeditor" style="display: none;"></div>
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
		<script src="/_y/assets/js/panel/resim-dosya-video.js?v=004"></script>
		<!-- END JAVASCRIPT -->
		<script>
			$("#excelurunyuklephp").addClass("active");
		</script>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
	</body>
</html>