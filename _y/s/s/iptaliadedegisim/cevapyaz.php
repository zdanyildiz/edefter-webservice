<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Mesajlar";
$formbaslik="Mesajlar";
$butonisim="CEVAPLA";
$qtalep=$_GET["talepid"]; 
$iptaliadedegisim_s="SELECT talepid,uyeid,siparisid,degisimtur,iadenedeni,iadeaciklama,urunid,tarih FROM iptaliadedegisim WHERE talepid='".$qtalep."' ORDER BY talepid ASC";
$iptaliadedegisim_v=$data->query($iptaliadedegisim_s);
if($iptaliadedegisim_v->num_rows>0)$iptaliadedegisim_d=1;
unset($iptaliadedegisim_s);
if($iptaliadedegisim_d==1)
			{
				guncelle("talepbildirim","1","iptaliadedegisim","talepid='".$qtalep."'",35);
				while ($iptaliadedegisim_t=$iptaliadedegisim_v->fetch_assoc()) 
				{
					$talepid=$iptaliadedegisim_t["talepid"];
					$uyeid=$iptaliadedegisim_t["uyeid"];
					$siparisid=$iptaliadedegisim_t["siparisid"];	
					$urunid=$iptaliadedegisim_t["urunid"];
					$iadenedeni=$iptaliadedegisim_t["iadenedeni"];
					$iadeaciklama=$iptaliadedegisim_t["iadeaciklama"];
					$degisimtur=$iptaliadedegisim_t["degisimtur"];	
					$tarih=$iptaliadedegisim_t["tarih"];										
					$uyebilgi=coksatir("SELECT uyeadsoyad,uyetelefon FROM uye WHERE uyeid='".$uyeid."' ");
					if($uyebilgi){
                        $uyeadsoyad=$uyebilgi["uyeadsoyad"];
                        $uyetelefon=$uyebilgi["uyetelefon"];
                        if(!BosMu($uyetelefon))$uyetelefon=coz($uyetelefon,$anahtarkod);
                    }
				}			
			}

if(f('cevapla')==1)
{	 
	$formhata=0;
	$formhataaciklama="";
	$cevapid=f("cevapid");	
	$mesajicerik=f("mesajicerik");
	$uyeid=f("uyeid");
	$simdi=date("Y-m-d H:i:s");
	$uyeeposta=teksatir("SELECT uyeeposta FROM uye WHERE uyeid='".$uyeid."' ","uyeeposta");
	$uyeeposta=coz($uyeeposta,$anahtarkod);
	if(!BosMu($mesajicerik))
	{
		$tablo='iptaliadedegisim';
		$sutunlar="talepsil,iadeaciklama,urunid,tarih,uyeid,cevapid,talepbildirim";
		$degerler="0|*_".$mesajicerik."|*_".$urunid."|*_".$simdi."|*_0|*_".$cevapid."|*_0";
		ekle($sutunlar,$degerler,$tablo,0);
		$mailicerik='<br><b>Gönderim Tarihi:</b> '.$simdi.'<br><b> Sipariş Numara: </b>'.$siparisid.'<br><b>Ürün İd: </b>'.$urunid.' <br><b>Değişim Türü:</b> '.$degisimtur.'<br><b>Mesaj İçeriği:</b> '.$iadenedeni.'<br><b>FİRMA CEVAP:</b> '.$mesajicerik.'
		';
		MailGonder($uyeeposta,"$siteDomain İptal İade Cevap", $mailicerik);
		$formhata=0;
		$formhataaciklama="<p>Mesajınız gönderilmiştir.</p>";
		$uyaribaslik="İşlem Başarılı";
		$_SESSION["formhata"]=$uyaribaslik.'||'.$formhataaciklama;
		//exit(header("Location: ".$hesabimlink."?mesajlarim=1#uyari"));
	}
	else
	{
		$formhata=1;$formhataaciklama="<p>Mesaj içeriği boş olamaz.</p>";
		$uyaribaslik="Dikkat";
		$_SESSION["formhata"]=$uyaribaslik.'||'.$formhataaciklama;
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
							<li class="active"><a href="/_y/s/s/uyeler/sorusor.php" class="btn ink-reaction btn-raised btn-primary">uye Liste</a></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-md-12">
								<form name="formanaliz" id="formanaliz" class="form form-validation form-validate" role="form" method="post">
									<input type="hidden" name="cevapla" value="1">
									<input type="hidden" name="cevapid" value="<?=$talepid?>">
									<input type="hidden" name="uyeid" value="<?=$uyeid?>">
									<div class="card">
										<div class="card-head style-primary form-inverse">
											<header><?=$formbaslik?></header>
										</div>
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">	
														<div class="col-sm-12">
															<div class="form-group floating-label">							
																	Sipariş Numarası :<a href="/_y/s/s/siparisler/OrderList.php?siparisno=<?=$siparisid?>"> <?=$siparisid?></a>			
															</div>
														</div>														
														<div class="col-sm-3">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="uyeadsoyad" 
																	id="uyeadsoyad" 
																	value="<?=$uyeadsoyad?>"
																	ReadOnly
																	placeholder="Üye Yetkili Adını Soyadını Yazın" required aria-required="true" >
																<label for="uyeadsoyad">Üye Yetkili Adını Soyadını Yazın</label>
															</div>
														</div>

															<div class="col-sm-3">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="degisimtur" 
																	id="degisimtur" 
																	value="Re: <?=$degisimtur?>"
																	ReadOnly
																	placeholder="Mesaj Konusu" 
																	required aria-required="true" >
																<label for="degisimtur">Değişim Türü</label>
															</div>
														</div>	

														<div class="col-sm-3">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="iadenedeni" 
																	id="iadenedeni" 
																	value="Re: <?=$iadenedeni?>"
																	ReadOnly
																	placeholder="Mesaj Konusu" 
																	required aria-required="true" >
																<label for="iadenedeni">İade Nedeni</label>
															</div>
														</div>	
											
														<div class="col-sm-3">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="tarih" 
																	id="tarih" 
																	value="<?=$tarih?>"
																	ReadOnly
																	placeholder="Talep Tarih" required aria-required="true" >
																<label for="mesajtarih">Talep Tarihi</label>
															</div>
														</div>
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="iadeaciklama" 
																	id="iadeaciklama" 
																	class="form-control" 
																	rows="2" 
																	ReadOnly
																	maxlength="255"
																	style="
																		background-color:#fff; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	><?=ltrim($iadeaciklama)?></textarea>
																<label for="mesaj">Mesaj İçeriği</label>
															</div>
														</div>
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="mesajicerik" 
																	id="mesajicerik" 
																	class="form-control" 
																	rows="2" 
																	maxlength="255"
																	style="
																		background-color:#efefef; 
																		width:96%; 
																		padding: 10px 1% 10px 1%; 
																		margin:10px 0 0 0; 
																		border:solid 1px #eee" 
																	></textarea>
																<label for="mesajicerik">Cevap Yaz</label>
															</div>
														</div>
														<div class="col-sm-12">
															<div class="form-group floating-label"><button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>							
								</form>
							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>

		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
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
			$("#degisimphp").addClass("active");
		</script>
		<script>
			$( "#adresulkeid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				if(str==212)
				{
			    	$("#adressehirid").show();
					$("#adresilceid").show();
					$("#adressemtid").show();
					$("#adresmahalleid").show();
					$("#adressehir").hide();
					$("#adressehir").val("");
					$("#adresilce").hide();
					$("#adresilce").val("");
					$("#adressemt").hide();
					$("#adressemt").val("");
					$("#adresmahalle").hide();
					$("#adresmahalle").val("");
			    	$("#_islem").attr("src", "/_y/s/f/sehirgetir.php?ulkeid="+str);
				}
				else
				{
					$("#adressehirid").hide();
					$("#adressehirid").empty();
					$("#adresilceid").hide();
					$("#adresilceid").empty();
					$("#adressemtid").hide();
					$("#adressemtid").empty();
					$("#adresmahalleid").hide();
					$("#adresmahalleid").empty();
					$("#adressehir").show();
					$("#adresilce").show();
					$("#adressemt").show();
					$("#adresmahalle").show();
				}
			});
			$( "#adressehirid" ).click('change',function()
			{
				$("#adressemtid").empty();
				$("#adresmahalleid").empty();
				var str = "";
				str = $("#adressehirid option:selected").val();
			    $("#_islem").attr("src", "/_y/s/f/ilcegetir.php?sehirid="+str);
			});
			$( "#adresilceid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				$("#adresmahalleid").empty();
			    $("#_islem").attr("src", "/_y/s/f/semtgetir.php?ilceid="+str);
			});
			$( "#adressemtid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/mahallegetir.php?semtid="+str);
			});
			$( "#adresmahalleid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/postakodgetir.php?mahalleid="+str);
			});
			$( "#yeniadresulkeid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				if(str==212)
				{
			    	$("#yeniadressehirid").show();
					$("#yeniadresilceid").show();
					$("#yeniadressemtid").show();
					$("#yeniadresmahalleid").show();
					$("#yeniadressehir").hide();
					$("#yeniadressehir").val("");
					$("#yeniadresilce").hide();
					$("#yeniadresilce").val("");
					$("#yeniadressemt").hide();
					$("#yeniadressemt").val("");
					$("#yeniadresmahalle").hide();
					$("#yeniadresmahalle").val("");
			    	$("#_islem").attr("src", "/_y/s/f/sehirgetir.php?yeni=1&ulkeid="+str);
				}
				else
				{
					$("#yeniadressehirid").hide();
					$("#yeniadressehirid").empty();
					$("#yeniadresilceid").hide();
					$("#yeniadresilceid").empty();
					$("#yeniadressemtid").hide();
					$("#yeniadressemtid").empty();
					$("#yeniadresmahalleid").hide();
					$("#yeniadresmahalleid").empty();
					$("#yeniadressehir").show();
					$("#yeniadresilce").show();
					$("#yeniadressemt").show();
					$("#yeniadresmahalle").show();
				}
			});
			$( "#yeniadressehirid" ).click('change',function()
			{
				$("#yeniadressemtid").empty();
				$("#yeniadresmahalleid").empty();
				var str = "";
				str = $("#yeniadressehirid option:selected").val();
			    $("#_islem").attr("src", "/_y/s/f/ilcegetir.php?yeni=1&sehirid="+str);
			});
			$( "#yeniadresilceid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				$("#yeniadresmahalleid").empty();
			    $("#_islem").attr("src", "/_y/s/f/semtgetir.php?yeni=1&ilceid="+str);
			});
			$( "#yeniadressemtid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/mahallegetir.php?yeni=1&semtid="+str);
			});
			$( "#yeniadresmahalleid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/postakodgetir.php?yeni=1&mahalleid="+str);
			});
			$("#adresekle").on("click",function()
			{
				$("#yeniadresid").val(0);
				$("#yeniadressehirid").hide();
				$("#yeniadressehirid").empty();
				$("#yeniadresilceid").hide();
				$("#yeniadresilceid").empty();
				$("#yeniadressemtid").hide();
				$("#yeniadressemtid").empty();
				$("#yeniadresmahalleid").hide();
				$("#yeniadresmahalleid").empty();
				$("#yeniadressehir").show();
				$("#yeniadresilce").show();
				$("#yeniadressemt").show();
				$("#yeniadresmahalle").show();
				$("#yeniadressehir").val("");
				$("#yeniadresilce").val("");
				$("#yeniadressemt").val("");
				$("#yeniadresmahalle").val("");
				$("#yeniadresbaslik").val("");
				$("#yeniadrespostakod").val("");
				$("#yeniadrestelefon").val("");
				$("#yeniadresacik").val("");
				$("#yeniadresulkeid option:first").attr('selected','selected');

			});
			$(".btn.btn-icon-toggle").on("click", function()
			{
				$adresid=$(this).data("id");
				$("#yeniadresid").val($adresid);
				$("#yeniadressehirid").hide();
				$("#yeniadressehirid").empty();
				$("#yeniadresilceid").hide();
				$("#yeniadresilceid").empty();
				$("#yeniadressemtid").hide();
				$("#yeniadressemtid").empty();
				$("#yeniadresmahalleid").hide();
				$("#yeniadresmahalleid").empty();
				$("#yeniadressehir").show();
				$("#yeniadresilce").show();
				$("#yeniadressemt").show();
				$("#yeniadresmahalle").show();
				$("#yeniadressehir").val("");
				$("#yeniadresilce").val("");
				$("#yeniadressemt").val("");
				$("#yeniadresmahalle").val("");
				$("#yeniadresbaslik").val("");
				$("#yeniadrespostakod").val("");
				$("#yeniadrestelefon").val("");
				$("#yeniadresacik").val("");
				$("#_islem").attr("src", "/_y/s/f/adresgetir.php?adresid="+$adresid);

			});

			$(".modal-dialog").css({"width":"80%"});
		</script>
	</body>
</html>