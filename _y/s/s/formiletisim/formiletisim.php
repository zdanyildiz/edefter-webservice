<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/*
 * formiletisim tablosuna formcevapid sütunu eklendi. (INT), Boş olabilir, varsayılan değer 0
 * */
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik ="İletişim Formu Mesajları";
$formbaslik ="İletişim Formu Mesajları";
$butonisim ="CEVAPLA";

$f_mesajtarih="";
$f_uyeadsoyad=f("adsoyad");
$f_mesajicerik=f("mesaj");;
$f_uyeeposta="";
$f_uyetelefon="";
$f_cevap=f("mesajicerik");
$f_cevapadsoyad="";

$f_mesajid=s(f('mesajid'));
$qmesajid=s(q('mesajid'));
if($qmesajid!=0)
{
    $f_mesajid=$qmesajid;
	guncelle("formbildirim","1","formiletisim","formid='".$qmesajid."'",35);$formhataaciklama="";
	$mesajgetir_s="SELECT * FROM formiletisim WHERE formid='".$qmesajid."' ";
	if($data->query($mesajgetir_s))
	{
		$mesajgetir_v=$data->query($mesajgetir_s);unset($mesajgetir_s);
		if($mesajgetir_v->num_rows>0)
		{
			while ($mesajgetir_t=$mesajgetir_v->fetch_assoc())
            {
                $f_mesajid=$mesajgetir_t["formid"];
			    $f_uyeadsoyad=$mesajgetir_t["adsoyad"];
                $f_uyeeposta=$mesajgetir_t["eposta"];
				$f_mesajicerik=$mesajgetir_t["mesaj"];
				$f_mesajtarih=Tarih($mesajgetir_t["tarih"],1);
			}
		}
	}
	else{hatalogisle('mesajgetir_s',$data->error);}
}

if(f('cevapla')==1)
{
    //$yoneticioturum_adsoyad
    $yoneticioturum_eposta=coz($yoneticioturum_eposta,$anahtarkod);
    $simdi=date("Y-m-d H:i:s");

    $f_uyeeposta=f("uyeeposta");
    $f_mesajkonu=f("mesajkonu");

    $firmabilgileri = coksatir("SELECT ayarfirmaeposta FROM ayarfirma WHERE ayarfirmasil='0'", "");
    $ayarfirmaeposta = $firmabilgileri["ayarfirmaeposta"];

    $tablo='formiletisim';
    $sutunlar="formcevapid,tarih,adsoyad,telefon,eposta,mesaj,formbildirim,formsil";
    $degerler=$f_mesajid."|*_".$simdi."|*_".$yoneticioturum_adsoyad."|*_|*_".$yoneticioturum_eposta."|*_".$f_cevap."|*_1|*_0";
    ekle($sutunlar,$degerler,$tablo,"0");

    $Kime=$f_uyeeposta.",".$ayarfirmaeposta;
    $mailKonu=$f_mesajkonu;
    $mailIcerik="$f_cevap<br>-----------------------------<br>$f_mesajicerik";
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
									<input type="hidden" name="mesajid" value="<?=$f_mesajid?>"
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
																	value="<?=$f_uyeadsoyad?>"
																	ReadOnly
																	placeholder="Ad Soyad" required aria-required="true" >
																<label for="uyeadsoyad">Adı Soyad</label>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="form-group floating-label">
																<input 
																	type="text" 
																	class="form-control" 
																	name="uyeeposta"
																	id="uyeeposta"
																	value="<?=$f_uyeeposta?>"
																	ReadOnly
																	placeholder="Eposta" required aria-required="true" >
																<label for="uyeeposta">Eposta</label>
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
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="mesajkonu"
                                                                        id="mesajkonu"
                                                                        value="Re: <?=$f_mesajtarih?> tarihli mesajınıza cevaben"
                                                                        placeholder="Mesaj Konusu"
                                                                        required aria-required="true" >
                                                                <label for="mesajkonu">Mesaj Konusu</label>
                                                            </div>
                                                        </div>
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="mesaj" 
																	id="mesaj" 
																	class="form-control" 
																	rows="6"
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
                                                        <?php
                                                        $formcevaplar_s="
                                                            SELECT  
                                                                *
                                                            FROM
                                                                formiletisim
                                                            WHERE
                                                                formcevapid='".$qmesajid."'
                                                            ORDER BY 
                                                                formid ASC
                                                        ";
                                                        if($data->query($formcevaplar_s))
                                                        {
                                                            $formcevaplar_v=$data->query($formcevaplar_s);
                                                            if($formcevaplar_v->num_rows>0)
                                                            {
                                                                while($formcevaplar_t=$formcevaplar_v->fetch_assoc())
                                                                {
                                                                    $f_formcevapadsoyad=$formcevaplar_t["adsoyad"];
                                                                    $f_formcevaptarih=$formcevaplar_t["tarih"];
                                                                    $f_formcevap=$formcevaplar_t["mesaj"];
                                                                ?>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group floating-label">
                                                                        <input
                                                                                type="text"
                                                                                class="form-control"
                                                                                name="cevapadsoyad"
                                                                                id="cevapadsoyad"
                                                                                value="<?=$f_formcevapadsoyad?>"
                                                                                ReadOnly
                                                                                placeholder="Ad Soyad" required aria-required="true" >
                                                                        <label for="cevapuyeadsoyad">Cevaplayan Adı Soyad</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group floating-label">
                                                                        <input
                                                                                type="text"
                                                                                class="form-control"
                                                                                name="cevaptarih"
                                                                                id="cevaptarih"
                                                                                value="<?=$f_formcevaptarih?>"
                                                                                ReadOnly
                                                                                placeholder="Ad Soyad" required aria-required="true" >
                                                                        <label for="cevapuyeadsoyad">Cevaplayan Adı Soyad</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <div class="form-group floating-label">
                                                                    <textarea
                                                                            name="mesajicerik"
                                                                            id="mesajicerik"
                                                                            class="form-control"
                                                                            rows="4"
                                                                            style="
                                                                            width:96%;
                                                                            padding: 10px 1% 10px 1%;
                                                                            margin:10px 0 0 0;
                                                                            border:solid 1px #eee"
                                                                            ReadOnly
                                                                    ><?=$f_formcevap?></textarea>
                                                                        <label for="mesajicerik">Cevap Yaz</label>
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                            }
                                                        }?>
														<div class="col-sm-12">
															<div class="form-group floating-label">
																<textarea 
																	name="mesajicerik" 
																	id="mesajicerik" 
																	class="form-control" 
																	rows="4"
																	style="
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
			$("#formiletisimphp").addClass("active");
		</script>
		<script>
			$(".modal-dialog").css({"width":"80%"});
		</script>
	</body>
</html>