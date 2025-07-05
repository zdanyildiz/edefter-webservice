<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$f_sehirid=q("sehirid");
$f_ilceid=q("ilceid");
$f_semtid=q("semtid");
$f_mahalleid=f("mahalleid");
$f_mahallead="";
$f_postakodu=f("ZipCode");
//düzenle
$sayfabaslik="Mahalle Düzenle";
$formbaslik="Mahalle Ekle";
$butonisim="KAYDET";

if(S(f("mahalleekle"))==1 && !BosMu(f("NeighborhoodName")) && S(f("AreaID"))!=0)
{
	$f_mahallead=f("NeighborhoodName");
    $f_semtid=f("AreaID");

	Veri(true);


	if(!Dogrula("yermahalle","AreaID='".$f_semtid."' and NeighborhoodName='".$f_mahallead."'")&& S(f("mahalleduzenle"))==0)
	{
        $formhata=0;
        $formhataaciklama="Yeni Mahalle Eklendi";
        ekle("NeighborhoodName,AreaID,ZipCode",$f_mahallead."|*_".$f_semtid."|*_".$f_postakodu,"yermahalle",0);
	}
	elseif(Dogrula("yermahalle","NeighborhoodID='".S($f_mahalleid)."'") && S(f("mahalleduzenle"))==1 && S($f_postakodu)!=0)
    {
        $formhata=0;
        $formhataaciklama="Mahalle Güncellendi";
        guncelle("NeighborhoodName,ZipCode",$f_mahallead."|*_".$f_postakodu,"yermahalle","NeighborhoodID='".S($f_mahalleid)."'",0);
    }
	elseif(Dogrula("yermahalle","NeighborhoodID='".S($f_mahalleid)."'") && S(f("mahalleduzenle"))==1 && S($f_postakodu)==0)
    {
        $formhata=1;
        $formhataaciklama="Lütfen Mahallenin Posta Kodunu Girin";
    }
	else
    {
        $formhata=1;
        $formhataaciklama="Bu Semte kayıtlı aynı isimde Mahalle var";
    }
	/**
     * mahalleId büyüktür 73342 düzenlemeye izin ver
     */
}

if(S(q("mahalleid"))>73342)
{
    $mahalle_bilgi=coksatir("
        SELECT 
               yerilce.CityID,yersemt.CountyID,yersemt.AreaID,NeighborhoodName,ZipCode
        FROM 
             yermahalle 
                 inner join yersemt ON yersemt.AreaID=yermahalle.AreaID 
                 inner join yerilce ON yerilce.CountyID=yersemt.CountyID 
        WHERE 
              NeighborhoodID='".q("mahalleid")."'");
    if(!BosMu($mahalle_bilgi))
    {
        $f_sehirid=$mahalle_bilgi["CityID"];
        $f_ilceid=$mahalle_bilgi["CountyID"];
        $f_semtid=$mahalle_bilgi["AreaID"];
        $f_mahallead=$mahalle_bilgi["NeighborhoodName"];
        $f_postakodu=$mahalle_bilgi["ZipCode"];
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
					<?php if($formhata==0 && S(f("mahalleekle"))==1){ ?>
					<div class="alert alert-success" role="alert">
						<?=$formhataaciklama?>
					</div>
					<?php }elseif($formhata==1 && S(f("mahalleekle"))==1) { ?>
					<div class="alert alert-danger" role="alert">
						<?=$formhataaciklama?>
					</div>
					<?php }?>
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
									</div>
									<form class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="mahalleekle" value="1">
                                        <?php if(S(q("mahalleid"))!=0){?><input type="hidden" name="mahalleduzenle" value="1"><input type="hidden" name="mahalleid" value="<?=q("mahalleid")?>"><?php }?>
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="card-body style-primary form-inverse">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select id="CityID" name="CityID" class="form-control">
                                                                    <?php
                                                                    if(!$data)Veri(true);
                                                                    $sql_ek="";
                                                                    //if(S($f_sehirid)>0)$sql_ek=" and CityID='".$f_sehirid."'";
                                                                    $sehir_s="SELECT CityID,CityName FROM yersehir Where CountryID=212 $sql_ek ";
                                                                    if($data->query($sehir_s))
                                                                    {
                                                                        $sehir_v=$data->query($sehir_s);unset($sehir_s);
                                                                        if($sehir_v->num_rows>0)
                                                                        {
                                                                            if(S($f_Sehirid)==0)echo '<option value="0">Önce Şehir Seçiniz</option>';
                                                                            while($sehir_t=$sehir_v->fetch_assoc())
                                                                            {
                                                                                $l_sehirid=$sehir_t["CityID"];
                                                                                $l_sehirad=$sehir_t["CityName"];
                                                                                ?>
                                                                                <option value="<?=$l_sehirid?>" <?php if($l_sehirid==$f_sehirid)echo 'selected';?>><?=$l_sehirad?> </option>
                                                                                <?php
                                                                            }unset($sehir_t);
                                                                        }unset($sehir_v);
                                                                    }else{die($data->error);}
                                                                    ?>
                                                                </select>
                                                                <label for="CityID">ŞEHİRLER (İLÇE İÇİN ŞEHİR SEÇİN)</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select id="CountyID" name="CountyID" class="form-control">
                                                                    <?php
                                                                    if(!$data)Veri(true);

                                                                    $sqlek=" Where CityID='".S($f_sehirid)."'";
                                                                    $ilce_s="SELECT CountyID,CountyName FROM yerilce  $sqlek ";
                                                                    if($data->query($ilce_s))
                                                                    {
                                                                        $ilce_v=$data->query($ilce_s);unset($ilce_s);
                                                                        if($ilce_v->num_rows>0)
                                                                        {
                                                                            if(S($f_ilceid)==0)echo '<option value="0">Önce İlçe Seçiniz</option>';
                                                                            while($ilce_t=$ilce_v->fetch_assoc())
                                                                            {
                                                                                $l_ilceid=$ilce_t["CountyID"];
                                                                                $l_ilcead=$ilce_t["CountyName"];
                                                                                ?>
                                                                                <option value="<?=$l_ilceid?>" <?php if(S($f_ilceid)==$l_ilceid)echo 'selected';?>><?=$l_ilcead?> </option>
                                                                                <?php
                                                                            }unset($ilce_t);
                                                                        }unset($ilce_v);
                                                                    }else{die($data->error);}
                                                                    ?>
                                                                </select>
                                                                <label for="CountyID">İLÇELER (SEMT İÇİN İLÇE SEÇİN)</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <select id="AreaID" name="AreaID" class="form-control">
                                                                    <?php
                                                                    if(!$data)Veri(true);

                                                                    $sqlek=" Where CountyID='".S($f_ilceid)."'";
                                                                    $semt_s="SELECT AreaID,AreaName FROM yersemt  $sqlek ";
                                                                    if($data->query($semt_s))
                                                                    {
                                                                        $semt_v=$data->query($semt_s);unset($semt_s);
                                                                        if($semt_v->num_rows>0)
                                                                        {
                                                                            while($semt_t=$semt_v->fetch_assoc())
                                                                            {
                                                                                $l_semtid=$semt_t["AreaID"];
                                                                                $l_semtad=$semt_t["AreaName"];
                                                                                ?>
                                                                                <option value="<?=$l_semtid?>"><?=$l_semtad?> </option>
                                                                                <?php
                                                                            }unset($semt_t);
                                                                        }unset($semt_v);
                                                                    }else{die($data->error);}
                                                                    ?>
                                                                </select>
                                                                <label for="CountyID">SEMTLER (MAHALLE İÇİN SEMT SEÇİN)</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
														<div class="col-md-4">
															<div class="form-group floating-label">
																<input type="text" class="form-control" id="NeighborhoodName" name="NeighborhoodName" value="<?=$f_mahallead?>" required="" aria-required="true" aria-invalid="true">
																<label for="AreaName">Mahalle Adı</label>
															</div>
														</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group floating-label">
                                                                <input type="text" class="form-control" id="ZipCode" name="ZipCode" value="<?=$f_postakodu?>" required="" aria-required="true" aria-invalid="true">
                                                                <label for="AreaName">Posta Kodu</label>
                                                            </div>
                                                        </div>
                                                    </div>
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
			$("#mahalleeklephp").addClass("active");
            $("#CityID").on("change",function()
            {
                $sehirid=$(this).val();
                window.location.href="/_y/s/s/bolgeler/mahalleekle.php?sehirid="+$sehirid;
            });
            $("#CountyID").on("change",function()
            {
                $ilceid=$(this).val();
                window.location.href="/_y/s/s/bolgeler/mahalleekle.php?sehirid=<?=$f_sehirid?>&ilceid="+$ilceid;
            });
		</script>
		<!-- END JAVASCRIPT -->
	</body>
</html>
