<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Mesajlar";
$formbaslik="Mesajlar";
$butonisim="CEVAPLA";
$f_uyeadsoyad="";
$f_mesajkonu="";
$f_mesajicerik="";
$f_siparis="";
$f_mesajtarih="";
$f_mesajicerik="";
$f_cevap=f("mesajicerik");
$f_mesajid=s(f('mesajid'));
$qmesajid=s(q('mesajid'));
if($qmesajid!=0)
{
    $f_mesajid=$qmesajid;
	guncelle("mesajbildirim","1","sorusor","mesajid='".$qmesajid."'",35);
	$mesajgetir_s="SELECT sorusor.*,uyeadsoyad,uyeeposta FROM sorusor INNER JOIN uye on uye.uyeid=sorusor.uyeid WHERE mesajid='".$qmesajid."' ";
	if($data->query($mesajgetir_s))
	{	
		$mesajgetir_v=$data->query($mesajgetir_s);unset($mesajgetir_s);
		if($mesajgetir_v->num_rows>0)
		{
			while ($mesajgetir_t=$mesajgetir_v->fetch_assoc())
            {
                $f_uyeid=$mesajgetir_t["uyeid"];
			    $f_uyeadsoyad=$mesajgetir_t["uyeadsoyad"];
                $f_uyeeposta=coz($mesajgetir_t["uyeeposta"],$anahtarkod);
				$f_mesajicerik=$mesajgetir_t["mesajicerik"];
				$f_mesajkonu=$mesajgetir_t["mesajkonusu"];	
				$f_siparisno=$mesajgetir_t["siparisno"];
				$f_mesajtarih=$mesajgetir_t["mesajtarih"];
                $f_cevap=teksatir("SELECT mesajicerik FROM sorusor WHERE cevapid='".$qmesajid."'","mesajicerik");
			}
		}
		$f_uyeadsoyad;
	}
	else{hatalogisle('mesajgetir_s',$data->error);}
}

if(f('cevapla')==1)
{
    $simdi=date("Y-m-d H:i:s");
    $mesajgetir_t=coksatir("SELECT sorusor.*,uyeadsoyad,uyeeposta FROM sorusor INNER JOIN uye on uye.uyeid=sorusor.uyeid WHERE mesajid='".$f_mesajid."' ");
    $f_uyeid=$mesajgetir_t["uyeid"];
    $f_uyeeposta=coz($mesajgetir_t["uyeeposta"],$anahtarkod);
    $f_mesajkonu="RE ".$mesajgetir_t["mesajkonusu"];
    $f_siparisno=$mesajgetir_t["siparisno"];
    $f_urunad=$mesajgetir_t["urunad"];

    $firmabilgileri = coksatir("SELECT ayarfirmaeposta FROM ayarfirma WHERE ayarfirmasil='0'", "");
    $ayarfirmaeposta = $firmabilgileri["ayarfirmaeposta"];

    $tablo='sorusor';
    $sutunlar="uyeid,cevapid,mesajkonusu,mesajicerik,mesajtarih,mesajsil,siparisno,urunad,mesajbildirim";
    $degerler="0|*_".$f_mesajid."|*_".$f_mesajkonu."|*_".$f_cevap."|*_".$simdi."|*_0|*_".$f_siparisno."|*_".$f_urunad."|*_0";
    ekle($sutunlar,$degerler,$tablo,"0");

    $Kime=$f_uyeeposta.",".$ayarfirmaeposta;
    $mailKonu="Üye Mesajları Cevabı";
    $mailIcerik="Sorunuz Yanıtlanmıştır<br>$f_cevap";
    MailGonder($Kime,$f_mesajkonu,$mailIcerik);

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
									<input type="hidden" name="mesajid" value="<?=$f_mesajid?>">
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
																	Sipariş Numarası : <a href="/_y/s/s/siparisler/OrderList.php?siparisno=<?=$f_siparisno?>"><?=$f_siparisno?></a>
															</div>
														</div>														
														<div class="col-sm-4">
															<div class="form-group floating-label">
                                                                <a href="/_y/s/s/uyeler/AddMember.php?uyeid=<?=$f_uyeid?>" target="_blank">
																<input 
																	type="text" 
																	class="form-control" 
																	name="uyeadsoyad" 
																	id="uyeadsoyad" 
																	value="<?=$f_uyeadsoyad?>"
																	ReadOnly
																	placeholder="Üye Yetkili Adını Soyadını Yazın" required aria-required="true" >
																<label for="uyeadsoyad">Üye Yetkili Adını Soyadını Yazın</label>
                                                                </a>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="mesajkonu" 
																	id="mesajkonu" 
																	value="Re: <?=$f_mesajkonu?>"
																	ReadOnly
																	placeholder="Mesaj Konusu" 
																	required aria-required="true" >
																<label for="mesajkonu">Mesaj Konusu</label>
															</div>
														</div>	
											
														<div class="col-sm-4">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="tarih" 
																	id="tarih" 
																	value="<?=$f_mesajtarih?>"
																	ReadOnly
																	placeholder="Mesaj Tarih" required aria-required="true" >
																<label for="mesajtarih">Mesaj Tarihi</label>
															</div>
														</div>

														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="mesaj" 
																	id="mesaj" 
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
																	><?=ltrim($f_mesajicerik)?></textarea>
																<label for="mesaj">Gelen Mesaj</label>
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
																	><?=$f_cevap?></textarea>
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
			$("#sorusorphp").addClass("active");
		</script>
		<script>
			$(".modal-dialog").css({"width":"80%"});
		</script>
	</body>
</html>