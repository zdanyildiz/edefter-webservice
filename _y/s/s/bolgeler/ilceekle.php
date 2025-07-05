<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$f_sehirid=0;
$f_ilceid=f("ilceid");
$f_ilcead="";
//düzenle
$sayfabaslik="İlçe Düzenle";
$formbaslik="İlçe Ekle";
$butonisim="KAYDET";

if(S(f("ilceekle"))==1 && !BosMu(f("CountyName")) && S(f("CityID"))!=0)
{
	$f_ilcead=f("CountyName");
    $f_sehirid=f("CityID");
	Veri(true);

	
	if(!Dogrula("yerilce","CityID='".$f_sehirid."' and CountyName='".$f_ilcead."'")&&S(f("ilceduzenle"))==0)
	{
        $formhata=0;
        $formhataaciklama="Yeni İlçe Eklendi";
        ekle("CountyName,CityID",$f_ilcead."|*_".$f_sehirid,"yerilce",0);
	}
	elseif(Dogrula("yerilce","CountyID='".$f_ilceid."'")&&S(f("ilceduzenle"))==1)
    {
        $formhata=0;
        $formhataaciklama="İlçe Güncellendi";
        guncelle("CountyName",$f_ilcead,"yerilce","CountyID='".$f_ilceid."'",0);
    }
	else
    {
        $formhata=1;
        $formhataaciklama="Bu şehre kayıtlı aynı isimde ilçe var";
    }
	/**
     * İlçeId büyüktür 973 düzenlemeye izin ver
     */
}

if(S(q("ilceid"))>973)
{
    $ilce_bilgi=coksatir("SELECT CityID,CountyName FROM yerilce WHERE CountyID='".q("ilceid")."'");
    if(!BosMu($ilce_bilgi))
    {
        $f_sehirid=$ilce_bilgi["CityID"];
        $f_ilcead=$ilce_bilgi["CountyName"];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Material Admin - Compose mail</title>

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
                            <li>Genel Ayarlar</li>
                            <li>Bölgeler</li>
							<li><?=$sayfabaslik?></li>
						</ol>
					</div>
					<?php if($formhata==0 && S(f("ilceekle"))==1){ ?>
					<div class="alert alert-success" role="alert">
						<?=$formhataaciklama?>
					</div>
					<?php }elseif($formhata==1 && S(f("ilceekle"))==1) { ?>
					<div class="alert alert-danger" role="alert">
						<?=$formhataaciklama?>
					</div>
					<?php } ?>
					<div class="section-body contain-lg">
						<div class="row">

							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
									</div>
									<form class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="ilceekle" value="1">
                                        <?php if(S(q("ilceid"))!=0){?><input type="hidden" name="ilceduzenle" value="1"><input type="hidden" name="ilceid" value="<?=q("ilceid")?>"><?php }?>
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body style-primary form-inverse">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <select id="CityID" name="CityID" class="form-control">
                                                                    <?php
                                                                    if(!$data)Veri(true);
                                                                    $sql_ek="";
                                                                    if(S($f_sehirid)>0)$sqlek=" and CityID='".$f_sehirid."'";
                                                                    $sehir_s="SELECT CityID,CityName FROM yersehir Where CountryID=212 $sqlek ";
                                                                    if($data->query($sehir_s))
                                                                    {
                                                                        $sehir_v=$data->query($sehir_s);unset($sehir_s);
                                                                        if($sehir_v->num_rows>0)
                                                                        {
                                                                            while($sehir_t=$sehir_v->fetch_assoc())
                                                                            {
                                                                                $l_sehirid=$sehir_t["CityID"];
                                                                                $l_sehirad=$sehir_t["CityName"];
                                                                                ?>
                                                                                <option value="<?=$l_sehirid?>"><?=$l_sehirad?> </option>
                                                                                <?php
                                                                            }unset($sehir_t);
                                                                        }unset($sehir_v);
                                                                    }else{die($data->error);}
                                                                    ?>
                                                                </select>
                                                                <label for="Age2">ŞEHİRLER (İLÇE İÇİN ŞEHİR SEÇİN)</label>
                                                                <p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ ŞEHİR İLE UYUMLU OLMASINA DİKKAT EDİN!</p>
                                                            </div>
                                                        </div>
														<div class="col-md-6">
															<div class="form-group floating-label">
																<input type="text" class="form-control" id="CountyName" name="CountyName" value="<?=$f_ilcead?>" required="" aria-required="true" aria-invalid="true">
																<label for="CountyName">İlçe Adı</label>
															</div>
														</div><!--end .col -->
													</div><!--end .row -->
												</div><!--end .col -->
											</div><!--end .row -->
										</div><!--end .card-body -->
										<!-- END DEFAULT FORM ITEMS -->


										<!-- BEGIN FORM FOOTER -->
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
												<button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
											</div><!--end .card-actionbar-row -->
										</div><!--end .card-actionbar -->
										<!-- END FORM FOOTER -->
										
									</form>
								</div><!--end .card -->
							</div><!--end .col -->
							<!-- END ADD CONTACTS FORM -->

						</div><!--end .row -->
					</div><!--end .section-body -->
				</section>
			</div><!--end #content-->
			<!-- END CONTENT -->
			<!-- BEGIN MENUBAR-->
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
			<!-- END MENUBAR -->
		</div><!--end #base-->
		<!-- END BASE -->
		<!-- BEGIN JAVASCRIPT -->

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
			$("#ilceeklephp").addClass("active");
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>
