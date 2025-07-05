<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="sayfa";

//düzenle
$sayfabaslik="Ürünleri Düzenle";
$formbaslik="ÜRÜN LİSTE";

Veri(true);
$urunler_d=0;
$urunler_s="
	SELECT 
		sayfa.sayfaid,sayfa.benzersizid,sayfaad,
		kategoriad,sayfalistekategori.kategoriid,
		resim.resim,resimklasorad,
		sayfaaktif,urunsatisfiyat,urunindirimsizfiyat,urunbayifiyat,urunalisfiyat,urunindirimorani,urunstok,urungununfirsati,
		baslik,aciklama,kelime,link,
		markaad
	FROM 
		sayfa
			INNER JOIN
				sayfalistekategori on
					sayfalistekategori.sayfaid=sayfa.sayfaid
					INNER JOIN kategori on
						kategori.kategoriid=sayfalistekategori.kategoriid
			LEFT JOIN sayfalisteresim on
				sayfa.sayfaid=sayfalisteresim.sayfaid
				INNER JOIN resim on
					resim.resimid=sayfalisteresim.resimid
					INNER JOIN resimklasor on
						resimklasor.resimklasorid=resim.resimklasorid 
			LEFT JOIN urunozellikleri on
				urunozellikleri.sayfaid=sayfa.sayfaid
				LEFT JOIN urunmarka on
					urunmarka.markaid=urunozellikleri.markaid
			LEFT JOIN seo on
				seo.benzersizid=sayfa.benzersizid
	WHERE 
		sayfasil='0' and sayfatip='7' and kategorigrup='7' 
	GROUP BY 
		sayfa.sayfaid 
	ORDER BY 
		sayfalistekategori.kategoriid asc,sayfaid ASC";
if($data->query($urunler_s))
{
	$urunler_v=$data->query($urunler_s);unset($urunler_s);if($urunler_v->num_rows>0)$urunler_d=1;
}else{die($data->error);}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sistem Panel - Ürün Liste</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/wizard/wizard.css?1425466601" />
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once($anadizin."/_y/s/b/header.php");?>
		<div id="base">
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li><a href="#">SAYFA BİLGİLERİ</a></li>
							<li class="active"><?=$sayfabaslik?></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
									</div>
								</div>
							</div>
						</div>
						<div  class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<div class="form-group">
											<input type="text" name="q" id="q" class="form-control" placeholder="Arama:Ürün Başlığını yazın" value="">
										</div>
										<div class="form-group">
											<a href="/_y/s/s/urunler/urunliste.php">Sıfırla</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>F</th>
													<th>#</th>
													<th>Resim</th>
													<th>Ad</th>
													<th>Kategori</th>
													<th>Fiyat</th>
													<th>İşlem</th>
													<th>Aktif</th>
													<th>Gör</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if($urunler_d==1)
											{
												while ($urunler_t=$urunler_v->fetch_assoc()) 
												{
													$fiyatyaz="";
													$benzersizid=$urunler_t["benzersizid"];
													$sayfaid=$urunler_t["sayfaid"];
													$sayfaad=$urunler_t["sayfaad"];
													$sayfaaktif=$urunler_t["sayfaaktif"];

													$kategoriad=$urunler_t["kategoriad"];
													$kategoriid=$urunler_t["kategoriid"];

													$markaad=$urunler_t["markaad"];

													$resimklasorad=$urunler_t["resimklasorad"];$resim=$urunler_t["resim"];
													
													$urunsatisfiyat=$urunler_t["urunsatisfiyat"];
													$urunindirimsizfiyat=$urunler_t["urunindirimsizfiyat"];
													$urunbayifiyat=$urunler_t["urunbayifiyat"];
													$urunalisfiyat=$urunler_t["urunalisfiyat"];
													$urunindirimorani=$urunler_t["urunindirimorani"];
													$urunstok=$urunler_t["urunstok"];
													$urungununfirsati=$urunler_t["urungununfirsati"];

													$seobaslik=$urunler_t["baslik"];
													$seoaciklama=$urunler_t["aciklama"];
													$seokelime=$urunler_t["kelime"];
													$tumkategori="";
													kategoridizin($kategoriid);
													//
													$seolink=DuzeltS($tumkategori)."/".DuzeltS($markaad."/".$sayfaad)."/".$sayfaid."s.html";
													guncelle("link",$seolink,"seo","benzersizid='".$benzersizid."'",56);
													//$urunler_t["link"];			
													?>
													<tr id="tr<?=$sayfaid?>" data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>" class="urunsatir">
														<td class="text-center <?php if(S($urungununfirsati)==1){?>style-warning<?php }?>"><?php if(S($urungununfirsati)==1){?><i class="md md-grade" title="Günün Fırsatı"></i><?php }?></td>
														<td><?=$sayfaid?></td>
														<td>
															<img src="<?="/m/r/?resim=$resimklasorad"."/"."$resim"?>&g=70&y=70" width="50" height="40">
														</td>
														<td><?=$sayfaad?></td>
														<td><?=$kategoriad?></td>
														<td><?=$urunsatisfiyat;?></td>	
														<td>
															<a 
																href="/_y/s/s/urunler/urunekle.php?sayfaid=<?=$sayfaid?>" 
																class="btn btn-icon-toggle" 
																data-toggle="tooltip" 
																data-placement="top" 
																data-original-title="Düzenle">
																<i class="fa fa-pencil"></i>
															</a>
															<a 
																id="urunsil"
																href="#textModal"
																class="btn btn-icon-toggle"
																data-id="<?=$sayfaid?>" 
																data-toggle="modal"
																data-placement="top"
																data-original-title="Sil" 
																data-target="#simpleModal"
																data-backdrop="true">
																<i class="fa fa-trash-o"></i></a>
															</a>
														</td>
														<td 
															class="<?php if(S($sayfaaktif==1)){?>style-info<?php }else{?>style-danger<?php }?> text-center">
															<?php if(S($sayfaaktif==1)){?><i class="md md-thumb-up" title="Aktif"></i><?php }else{?>
															<i class="md md-error" title="Pasif"></i><?php }?>
														</td>
														<td>
															<a 
																href="<?=$seolink?>" 
																title="Sayfayı Gör" target="_blank">
																<i class="fa fa-external-link"></i>
															</a>
														</td>
													</tr>
													<tr id="trgizli<?=$sayfaid?>" style="display:none;background-color:#ddd">
														<form 
															class="form form-validation form-validate" 
															action="/_y/s/f/urunguncelle.php" 
															method="post" 
															target="_islem" 
															novalidate="novalidate">
															<input type="hidden" name="sayfaid" value="<?=$sayfaid?>">
															<input type="hidden" name="benzersizid" value="<?=$benzersizid?>">
															<td colspan="9">
																<div class="form-group">
																	<a 
																		data-id="trgizli<?=$sayfaid?>" 
																		data-ustid="tr<?=$sayfaid?>" 
																		class="urunsatiralt" 
																		style="cursor:pointer;color:#f00">KAPAT (x)
																	</a>
																</div>
																<div class="card row" style="margin-left: 5px; margin-right: 5px">
																	<div class="card-body">
																		<div class="form-group">
																			<input 
																				type="text" 
																				name="urunbaslik" 
																				id="urunbaslik<?=$sayfaid?>" 
																				class="form-control" 
																				placeholder="Ürün Başlık" 
																				value="<?=$sayfaad?>" 
																				data-rule-minlength="5"
																				maxlength="65"
																				aria-invalid="false"
																				required aria-required="true">
																			<label for="urunbaslik<?=$sayfaid?>" 
																				style="margin-top:-10px">Ürün Başlık</label>
																		</div>
																	</div>
																</div>
																<div class="card row" style="margin-left: 5px; margin-right: 5px">
																	<div class="card-body">
																		<div class="col-sm-3">
																			<div class="form-group">
																				<input 
																					type="text" 
																					name="urunsatisfiyat" 
																					id="urunsatisfiyat<?=$sayfaid?>" 
																					class="form-control" 
																					placeholder="99.99" 
																					value="<?=$urunsatisfiyat?>" 
																					data-rule-number="true" 
																					required="" 
																					aria-required="true" 
																					aria-invalid="false">
																				<label for="urunfiyat<?=$sayfaid?>" 
																					style="margin-top:-10px">Ürün Satış Fiyat</label>
																			</div>
																		</div>
																		<div class="col-sm-3">
																			<div class="form-group">
																				<input 
																					type="text" 
																					name="urunindirimsizfiyat" 
																					id="urunindirimlifiyat<?=$sayfaid?>" 
																					class="form-control" 
																					placeholder="79.99" 
																					value="<?=$urunindirimsizfiyat?>" 
																					data-rule-number="true" 
																					required
																					aria-required="true" 
																					aria-invalid="false">
																				<label for="urunindirimsizfiyat<?=$sayfaid?>" 
																					style="margin-top:-10px">Ürün İnd.SİZ Fiyat</label>
																			</div>
																		</div>
																		<div class="col-sm-6">
																			<div class="form-group">
																				<input 
																					type="text" 
																					name="urunindirimorani" 
																					id="urunindirimorani<?=$sayfaid?>" 
																					class="form-control" 
																					placeholder="0.15" 
																					value="<?=$urunindirimorani?>" 
																					data-rule-number="true" 
																					required 
																					aria-required="true" 
																					aria-invalid="false">
																				<label for="urunindirimorani<?=$sayfaid?>" 
																					style="margin-top:-10px">Ürün İndirim %10 için 0.10</label>
																			</div>
																		</div>
																		<div class="col-sm-3">
																			<div class="form-group">
																				<input type="text" 
																					name="urunbayifiyat" 
																					id="urunbayifiyat<?=$sayfaid?>" 
																					class="form-control" 
																					placeholder="79.99" 
																					value="<?=$urunbayifiyat?>" 
																					data-rule-number="true" 
																					required 
																					aria-required="true" 
																					aria-invalid="false" >
																				<label for="urunbayifiyat<?=$sayfaid?>" 
																					style="margin-top:-10px">Ürün Bayi Fiyat</label>
																			</div>
																		</div>
																		<div class="col-sm-3">
																			<div class="form-group">
																				<input 
																					type="text" 
																					name="urunalisfiyat" 
																					id="urunalisfiyat<?=$sayfaid?>" 
																					class="form-control" 
																					placeholder="49.99" 
																					value="<?=$urunalisfiyat?>" 
																					data-rule-number="true">
																				<label for="urunalisfiyat<?=$sayfaid?>" 
																					style="margin-top:-10px">Ürün Alış Fiyat (Sadece siz görebilirsiniz)</label>
																			</div>
																		</div>
																		<div class="col-sm-6">
																			<div class="form-group">
																				<input 
																					type="text" 
																					name="urunstok" 
																					id="urunstok<?=$sayfaid?>" 
																					class="form-control" 
																					placeholder="Ürün Stok 20" 
																					value="<?=$urunstok?>" 
																					data-rule-digits="true">
																				<label for="urunstok<?=$sayfaid?>" 
																					style="margin-top:-10px">Ürün Stok</label>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="card row" style="margin-left: 5px; margin-right: 5px">
																	<div class="card-body">
																		<div class="form-group">
																			<input 
																				type="text" 
																				name="seobaslik" 
																				id="seobaslik<?=$sayfaid?>" 
																				class="form-control" 
																				placeholder="Adidas Terrex Swift Solo Erkek Siyah Spor Ayakkabı - D67031" 
																				value="<?=$seobaslik?>" 
																				data-rule-minlength="5"
																				maxlength="65"
																				aria-invalid="false"
																				required aria-required="true">
																			<label for="seobaslik<?=$sayfaid?>" 
																				style="margin-top:-10px">SEO Başlık</label>
																		</div>
																		<div class="form-group">
																			<textarea 
																				id="seoaciklama<?=$sayfaid?>" 
																				name="seoaciklama" 
																				placeholder="Adidas TERREX Swift Solo D67031 Outdoor Siyah Fitness Erkek Spor Ayakkabı orjinal ürün, ücretsiz kargo ve peşin ödeme indirimi ve kredi kartı taksit seçenekleri ile en uygun fiyata burada"
																				class="form-control"  
																				rows="3"
																				data-rule-minlength="25"
																				maxlength="200"
																				aria-invalid="false"
																				required aria-required="true"><?=$seoaciklama?></textarea>
																				<label for="seoaciklama<?=$sayfaid?>" 
																					style="margin-top:-10px">SEO Açıklama</label>
																		</div>
																		<div class="form-group">
																			<textarea 
																				id="seokelime<?=$sayfaid?>" 
																				name="seokelime"
																				class="form-control" 
																				placeholder="adidas ayakkabı,adidas spor ayakkabı,adidas terrex swift solo,adidas siyah spor ayakkabı,erkek siyah spor ayakkabı" 
																				rows="2"
																				data-rule-minlength="6"
																				maxlength="255"
																				aria-invalid="false"
																				required aria-required="true"><?=$seokelime?></textarea>
																				<label for="seokelime<?=$sayfaid?>" 
																					style="margin-top:-10px">SEO Kelimeler</label>
																		</div>
																		<div class="card-actionbar">
																			<div class="card-actionbar-row">
																				<button 
																					type="submit" 
																					class="btn btn-primary btn-default">GÜNCELLE</button>
																			</div>
																		</div>
																	</div>
																</div>
															</td>
														</form>
													</tr>
													<?php
												}unset($urunler_t);
											}
											unset($urunler_d,$urunler_v,$sayfaid,$sayfaadl);
											?>
											</tbody>
										</table>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>
		<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="simpleModalLabel">Ürün Sil</h4>
					</div>
					<div class="modal-body">
						<p>Ürünü silmek istediğinize emin misiniz?</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
						<button type="button" class="btn btn-primary" id="silbutton">Sil</button>
					</div>
				</div>
			</div>
		</div>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
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

		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		
		<script>
			$silid=0;
			$(document).ready(function()
			{
				$('a#urunsil').live("click",function ()
				{
					$silid=$(this).data("id");//alert($silid);
				});
				$('#silbutton').on("click",function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=urun&id="+$silid);
				});
			 });
			$(".urunsatir").live("click",function()
			{
				$satir=$(this).data("id");
				$ustsatir=$(this).data("ustid");
				$("#"+$satir).show();
				$("#"+$ustsatir).css("background-color","#ddd");
				location.replace("#"+$ustsatir);
			});
			$(".urunsatiralt").live("click",function()
			{
				$satir=$(this).data("id");
				$("#"+$satir).hide();
				$ustsatir=$(this).data("ustid");
				$("#"+$ustsatir).css("background-color","#fff");
			});
			$( "#q" ).keypress(function() 
			{
				if($("#q").val().length>=3)
				{
					$('#_islem').attr('src', "/_y/s/f/urunbul.php?q="+$("#q").val());
				}
			});
		</script>
		<script>
			$("#urunlistephp").addClass("active");
		</script>
	</body>
</html>
