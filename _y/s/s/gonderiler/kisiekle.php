<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="kisiler";
$formbaslik="Kişi Listesi";
$butonisim="EKLE";

$f_rehberid=f("rehberid");
$f_rehbergrupid=f("rehbergrupid");
$f_rehberadsoyad=f("rehberadsoyad");
$f_rehbereposta=f("rehbereposta");
$f_rehbergsm=f("rehbergsm");
$f_rehberaktif=f("rehberaktif");

if (s($f_rehbergsm)==0||strlen($f_rehbergsm)!=10)
{
	$f_rehbergsm="";
}

if(S(f("rehberekle"))==1)
{
	if(S($f_rehbergrupid)==0)
    {
        $formhata=1;
        $formhataaciklama="DİKKAT:Lütfen rehbere ekleyeceğiniz kişi/ler için bir grup seçin.";
    }
    elseif (s($f_rehbergsm)==0 && BosMu($f_rehbereposta))
	{
		$formhata=1;
		$formhataaciklama="DİKKAT:Eposta veya Telefon Numarasından En Az Birinin Girilmesi Zorunludur.";
	}
	elseif(!BosMu($f_rehbergsm) && (S($f_rehbergsm)==0 || strlen($f_rehbergsm)!=10))
    {
        $formhata=1;
        $formhataaciklama="DİKKAT:Hatalaı telefon girdiniz, lütfen düzeltin";
    }
    elseif(S($f_rehberid)==0 && !BosMu($f_rehbergsm) && dogrula("rehber","rehbergsm='". sifrele($f_rehbergsm,$anahtarkod) ."'"))
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bu grupta Gsm no ( $f_rehbergsm ) zaten kayıtlı.<br><br>
			<a href='/_y/s/s/gonderiler/kisiliste.php'> > Kişi Listesine git <</a><br>";
    }
	elseif(S($f_rehberid)==0 && !BosMu($f_rehbereposta) && dogrula("rehber","rehbereposta='". sifrele($f_rehbereposta,$anahtarkod) ."'"))
	{
			$formhata=1;
			$formhataaciklama="DİKKAT: Bu grupta E-posta ( $f_rehbereposta ) zaten kayıtlı.<br><br>
			<a href='/_y/s/s/gonderiler/kisiliste.php'> > Kişi Listesine git <</a><br>";
	}

	if($formhata==0)
	{
        $sutunlar="rehbergrupid,
            rehberadsoyad,
            rehbereposta,
            rehbergsm,
            rehberaktif,
            rehbersil
        ";

        $degerler=$f_rehbergrupid."|*_".
            $f_rehberadsoyad."|*_".
            sifrele($f_rehbereposta,$anahtarkod)."|*_".
            sifrele($f_rehbergsm,$anahtarkod)."|*_".
            S($f_rehberaktif)."|*_".
            "0";

        $tablo="rehber";

		if(S($f_rehberid)==0)
		{
			$f_benzersizid=SifreUret(20,2);
			ekle($sutunlar.",rehberbenzersizid",$degerler."|*_".$f_benzersizid,$tablo,35);
			$f_rehberid = teksatir(" Select rehberid from rehber Where rehberbenzersizid='". $f_benzersizid ."'","rehberid");
		}
		else
		{
			guncelle($sutunlar,$degerler,$tablo," rehberid='". $f_rehberid ."' ",35);
		}
	}
}
echo sifrele("a*87B_54ad/7",$anahtarkod);
if(S(q("rehberid"))!=0)
{
	if(dogrula("rehber","rehberid='". q("rehberid") ."'"))
	{
		$butonisim="GÜNCELLE";
		$f_rehberid=q("rehberid");
		$rehber_s="
			SELECT * FROM rehber WHERE rehberid='". $f_rehberid ."'
		";
		if($data->query($rehber_s))
		{
			$rehber_v=$data->query($rehber_s);unset($rehber_s);
			if($rehber_v->num_rows>0)
			{
				while($rehber_t=$rehber_v->fetch_assoc()) 
				{
					$f_rehbergrupid=$rehber_t["rehbergrupid"];
					$f_rehberadsoyad=$rehber_t["rehberadsoyad"];
					$f_rehbereposta=$rehber_t["rehbereposta"];
					$f_rehbergsm=$rehber_t["rehbergsm"];
					$f_rehberaktif=$rehber_t["rehberaktif"];
				}unset($rehber_t);
			}unset($rehber_v);
		}else{hatalogisle("Kişi güncelleme",$data->error);}
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
							<li class="active"><a href="/_y/s/s/gonderiler/kisiliste.php" class="btn ink-reaction btn-raised btn-primary">Kişi Liste</a></li>
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
										<input type="hidden" name="rehberekle" value="1">
										<input type="hidden" name="rehberid" value="<?=$f_rehberid?>">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
													<div class="row">															
														<div class="col-sm-6">
															<div class="form-group floating-label">
															<select class="form-control static dirty" name="rehbergrupid" id="rehbergrupid">
																<option value="0">Grup Seçiniz</option>
																<?php 

																$rehbergrup_s="
																SELECT 
																	rehbergrupid,rehbergrupad
																FROM 

																	rehbergrup 
																WHERE rehbergrupsil='0' 

																order by rehbergrupad ASC

																";
																if ($data->query($rehbergrup_s))
																{
																	$rehbergrup_v=$data->query($rehbergrup_s);unset($rehbergrup_s);
																	if ($rehbergrup_v->num_rows>0)
																	{
																		while ($rehbergrup_t=$rehbergrup_v->fetch_assoc())
																		{
																			$rehbergrupid=$rehbergrup_t['rehbergrupid'];
																			$rehbergrupad=$rehbergrup_t['rehbergrupad'];
																			?>
																			<option value="<?=$rehbergrupid?>" <?php if($f_rehbergrupid==$rehbergrupid)echo "selected";?>><?=$rehbergrupad?></option>
																			<?php

																		}
																	}unset($rehbergrup_v);

																}else{
																	hatalogisle("Kişi Ekle-Rehber Grup",$data->error);
																}



																 ?>
																
															</select>
															</div>
															<div class="form-group floating-label">
															<input 
																type="text" 
																class="form-control" 
																name="rehberadsoyad" 
																id="rehberadsoyad" 
																value="<?=$f_rehberadsoyad?>"
																placeholder="Adını ve Soyadını Yazın"  >
																<label for="rehberadsoyad">Adını ve Soyadını Yazın</label>
															</div>
															<div class="form-group floating-label">
															<input 
																type="email" 
																class="form-control" 
																name="rehbereposta" 
																id="rehbereposta" 
																value="<?=$f_rehbereposta?>"
																placeholder="Kişi Epostasını Giriniz"  >
																<label for="rehbereposta">Kişi Epostasını Giriniz</label>
															</div>
															<div class="form-group floating-label">
																<input 
																	type="tel" 
																	class="form-control" 
																	name="rehbergsm" 
																	id="rehbergsm" 
																	value="<?=$f_rehbergsm?>"
																	placeholder="Kişinin Telefon Numarasaını Giriniz"  >
																	<label for="rehbergsm">Kişinin Telefon Numarasaını Giriniz</label>
															</div>
															<div class="form-group floating-label">
																		<label class="radio-inline radio-styled">
																	<input type="checkbox" name="rehberaktif" value="1" <?php if($f_rehberaktif==1)echo 'checked';?>><span>Tanıtım Onayı</span>
																</label>
															</div>

														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-actionbar">
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
		<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
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
		
		<script>
			$("#rehbereklephp").addClass("active");

			$('#demo-date-format').datepicker({
			    format: 'yyyy-mm-dd',
			    language: 'tr'	
			});
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>