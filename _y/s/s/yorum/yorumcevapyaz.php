<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
$formhata=0;
$formhataaciklama="";
//düzenle
$sayfabaslik="Yorumlar";
$formbaslik="Yorumlar";
$butonisim="CEVAPLA";
$qyorumid=q("yorumid"); 
$yorumicerik="";
$yorumcevapid="";
$yorumtarih="";
$sayfaad="";
$uyeadsoyad="";
if(s($qyorumid)!=0) 
{
	$yorum_s="SELECT yorumid,yorumurunid,yorumcevapid,yorumicerik,yorumsil,uyeid,yorumtarih FROM yorum WHERE yorumid='".$qyorumid."' ORDER BY yorumid ASC";
	$yorum_v=$data->query($yorum_s);
if($yorum_v->num_rows>0)$yorum_d=1;
	unset($yorum_s);
	if($yorum_d==1)
			{
				guncelle("yorumbildirim","1","yorum","yorumid='".$qyorumid."'",35);
				while ($yorum_t=$yorum_v->fetch_assoc()) 
				{
					$yorumid=$yorum_t["yorumid"];
					$uyeid=$yorum_t["uyeid"];
					$yorumurunid=$yorum_t["yorumurunid"];
					$yorumcevapid=$yorum_t["yorumcevapid"];
					$yorumicerik=$yorum_t["yorumicerik"];
					$yorumtarih=$yorum_t["yorumtarih"];			
					$sayfaad=teksatir("SELECT sayfaad FROM sayfa WHERE sayfaid='". $yorumurunid ."'","sayfaad");
					$uyebilgi=coksatir("SELECT uyeadsoyad,uyetelefon FROM uye WHERE uyeid='".$uyeid."' ");
					if($uyebilgi){
                        $uyeadsoyad=$uyebilgi["uyeadsoyad"];
                        $uyetelefon=$uyebilgi["uyetelefon"];
                        $uyetelefon=coz($uyetelefon,$anahtarkod);
					}
        }
			}
}
if(f('cevapla')==1)
{	 
	$formhata=0;
	$formhataaciklama="";
	$yorumcevapid=f("yorumcevapid");	
	$yorumicerik=f("yorumicerik");
	$uyeid=f("uyeid");
	$simdi=date("Y-m-d H:i:s");
	$sayfaad=teksatir("SELECT sayfaad FROM sayfa WHERE sayfaid='". $yorumurunid ."'","sayfaad");
	$uyeeposta=teksatir("SELECT uyeeposta FROM uye WHERE uyeid='".$uyeid."' ","uyeeposta");
	$uyeeposta=coz($uyeeposta,$anahtarkod);
	if(!BosMu($yorumicerik))
	{
		$tablo='yorum';
		$sutunlar="yorumsil,yorumicerik,yorumtarih,uyeid,yorumcevapid,yorumurunid,yorumbildirim";
		$degerler="0|*_".$yorumicerik."|*_".$simdi."|*_0|*_".$yorumcevapid."|*_".$yorumurunid."|*_1";
		ekle($sutunlar,$degerler,$tablo,0);
		$mailicerik='<br><b>Gönderim Tarihi:</b> '.$simdi.'<br><b>Yorum Cevap:</b>'.$yorumicerik.'<br><b>Yorum Yapılan Ürün Adı</b>'.$sayfaad.'
		';
		MailGonder($uyeeposta,"$siteDomain Yorumunuza Cevap Var", $mailicerik);
		$formhata=0;
		$formhataaciklama="Mesajınız gönderilmiştir.";
		$uyaribaslik="İşlem Başarılı";
		$_SESSION["formhata"]=$uyaribaslik.'||'.$formhataaciklama;
		exit(header("Location:/_y/s/s/yorum/yorumliste.php?formhataaciklama=$formhataaciklama"));
	}
	else
	{
		$formhata=1;$formhataaciklama="Mesaj içeriği boş olamaz.";
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
									<input type="hidden" name="yorumcevapid" value="<?=$yorumid?>">
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
														<div class="col-sm-4">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="uyeadsoyad" 
																	id="uyeadsoyad" 
																	value="<?=$uyeadsoyad?>"
																	ReadOnly
																	placeholder="Yorum Yapan Üye Ad Soyad" required aria-required="true" >
																<label for="uyeadsoyad">Yorum Yapan Üye Ad Soyad</label>
															</div>
														</div>

														<div class="col-sm-6">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="sayfaad" 
																	id="sayfaad" 
																	value="<?=$sayfaad?>"
																	ReadOnly
																	placeholder="Yorum Yapılan Ürün Adı" 
																	required aria-required="true" >
																<label for="sayfaad">Yorum Yapılan Ürün Adı</label>
															</div>
														</div>	
											
														<div class="col-sm-2">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="yorumtarih" 
																	id="yorumtarih" 
																	value="<?=$yorumtarih?>"
																	ReadOnly
																	placeholder="Yorum Tarihi" required aria-required="true" >
																<label for="yorumtarih">Yorum Tarihi</label>
															</div>
														</div>
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="yorumicerikg" 
																	id="yorumicerikg" 
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
																	><?=ltrim($yorumicerik)?></textarea>
																<label for="yorumicerikg">Yorum İçeriği</label>
															</div>
														</div>
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="yorumicerik" 
																	id="yorumicerik" 
																	class="form-control" 
																	rows="2" 
																	maxlength="255"
																	style="background-color:#efefef;width:96%;padding: 10px 1% 10px 1%;margin:10px 0 0 0;border:solid 1px #eee"></textarea>
																<label for="yorumicerik">Yorumu Cevapla</label>
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
			$("#yorumlistephp").addClass("active");
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