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
					
					<?php //Ürün Listesi Yüklendi
					if(S(q("adim"))==0)
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
											<a href="/_y/s/s/urunler/excel-urun-fiyatguncelle.php" target="_blank">Örnek dosyayı indirmek için tıklayın.</a>
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
					if(S(q("adim"))==1 && !BosMu(q("dosya")))
					{
						//ürün listesi kontrol
						require_once $_SERVER['DOCUMENT_ROOT'].'/_y/exceloku/src/SimpleXLSX.php';
						echo '
							<div class="card">
								<div class="card-head style-primary">
									<header><h3>Ürün Listenizi Kontrol Edin</h3><br>
									<p>Sorun görünmüyorsa devam edin (ilk 20 satır izleniyor)</p></header>
								</div>
							</div>		
						';
						if ( $xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'].q("dosya")) )
						{
							
							$excelveri=$xlsx->rows();
							if(isset($excelveri))
							{
								$satirtoplam=count($excelveri)-1;
								echo '<div class="row">Toplam '.$satirtoplam.' satır.</div>';
								echo '
								<div class="table-responsive">
									<div id="datatable2_wrapper" class="dataTables_wrapper no-footer">
										<table class="table no-margin table-hover">
								';
									foreach ($excelveri as $satirsay => $satir)
									{
									 	if($satirsay<=20)
									 	{
										 	if($satirsay==0)echo '<thead>';else echo '<tr>';
										 	foreach ($satir as $sutunsay => $sutun)
										 	{
										 		if($sutunsay<=23)
										 		{
											 		if($satirsay==0)
											 		{
											 			echo "<th>".$sutun."</th>";
											 		}
											 		else
											 		{
											 			if($sutunsay==7)$sutun=mb_substr(strip_tags($sutun),0,100,"UTF-8");
											 			if($sutunsay==17)$resimklasor=$sutun;
											 			if($sutunsay==18)
											 			{
											 				if(!BosMu($sutun))
                                                            {
                                                                $resim=explode(",", $sutun);
                                                                $sutun='<img class="img-circle width-1"src="'.$resimklasor.'/'.$resim[0].'" >';
                                                            }
											 				else{
                                                                $sutun='Boş';
                                                            }

											 				
											 			}
											 			echo "<td>".$sutun."</td>";
											 		}
											 	}
										 	}
										 	if($satirsay==0)echo '</thead>';else echo '</tr>';
										}
									}
								echo '
										</table>
									</div>
								</div>';
								echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=2&dosya=/m/r/havuz/urun.xlsx">DEVAM EDİN</a>';
							}
						}
						else{echo SimpleXLSX::parseError();}
					}
					elseif(S(q("adim"))==2 && !BosMu(q("dosya")))
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar.php");
					}
					elseif(S(q("adim"))==3)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-kategori.php");
						echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=4">DEVAM EDİN</a>';
					}
					elseif(S(q("adim"))==4)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-marka.php");
						echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=5">DEVAM EDİN</a>';
					}
					elseif(S(q("adim"))==5)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-parabirim.php");
						echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=6">DEVAM EDİN</a>';
					}
					elseif(S(q("adim"))==6)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-renk.php");
						echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=7">DEVAM EDİN</a>';
					}
					elseif(S(q("adim"))==7)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-beden.php");
						echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=8">DEVAM EDİN</a>';
					}
                    elseif(S(q("adim"))==8)
                    {
                        require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-malzeme.php");
                        echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=9">DEVAM EDİN</a>';
                    }
					elseif(S(q("adim"))==9)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-resim.php");
						echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=10">DEVAM EDİN</a>';
					}
					elseif(S(q("adim"))==10)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-sayfa.php");
						echo '<br><a class="btn ink-reaction btn-raised btn-primary" href="/_y/s/s/urunler/excel-urun-yukle.php?adim=11">DEVAM EDİN</a>';
					}
					elseif(S(q("adim"))==11)
					{
						require_once($anadizin."/_y/s/s/urunler/excel-urun-aktar-urunozellik.php");
						echo '<br>Ürün Aktarma İşlemi Tamamlanmıştır';
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
			$("#topluurunyuklephp").addClass("active");
		</script>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
	</body>
</html>