<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$urunindirimorani=0;
$sqlek="";
$f_kategoriid 	= S(f("kategoriid"));
if($f_kategoriid!=0){$sqlek=" and sayfalistekategori.kategoriid='".$f_kategoriid."'";}
$f_markaid 		= S(f("markaid"));
if($f_markaid!=0){$sqlek=$sqlek." and urunozellikleri.markaid='".$f_markaid."'";}
$f_tedarikciid 		= S(f("tedarikciid"));
if($f_tedarikciid!=0){$sqlek=$sqlek." and urunozellikleri.tedarikciid='".$f_tedarikciid."'";}
$f_para 	 	= Para(f("para"));
$f_oran 		= Para(f("oran"));

$f_islemtipi 	= S(f("islemtipi"));
$f_fiyatok 		= S(f("fiyatok"));

$f_indirimsizfiyat=S(f("indirimsizfiyat"));
$f_satisfiyat=S(f("satisfiyat"));
$f_bayifiyat=S(f("bayifiyat"));
$f_tumfiyat=S(f("tumfiyat"));
$f_alisfiyat=S(f("alisfiyat"));
//düzenle
Veri(true);
$sayfabaslik="Toplu Fiyat Güncelle";
$formbaslik ="Fiyat Ayarları";
$butonisim 	="GÜNCELLE";
if(S(f("toplufiyatguncelle"))==1)
{
    if($f_para=="0.00"&&$f_oran=="0.00")
    {
        $formhataaciklama="Lütfen geçerli bir para ya da yüzde değeri girin";
    }
    elseif($f_islemtipi==0)
    {
        $formhataaciklama="Lütfen indirim ya da zam seçeneklerinden birini seçin";
    }
    elseif($f_fiyatok==0)
    {
        $formhataaciklama="Lütfen fiyat değişikliği için para ya da yüzde seçeneklerinden birini seçin";
    }
    elseif($f_indirimsizfiyat==0&&$f_satisfiyat==0&&$f_bayifiyat==0&&$f_tumfiyat==0&&$f_alisfiyat==0)
    {
        $formhataaciklama="Lütfen fiyat değişikliğinin hangi fiyatlara uygulanacağını seçin. (Alış,Satış,İndirimsiz Fiyat,Bayi fiyatı ya da tümü)";
    }
    else
    {
        $urunler_s="
            Select 
                sayfa.sayfaid as urunid,
                urunalisfiyat,urunsatisfiyat,urunindirimsizfiyat,urunbayifiyat,urunindirimorani,urunstokkodu
            From 
                sayfa
                    inner join sayfalistekategori on 
                        sayfalistekategori.sayfaid=sayfa.sayfaid 
                        inner join kategori on 
                            kategori.kategoriid=sayfalistekategori.kategoriid 
                    inner join urunozellikleri on 
                        urunozellikleri.sayfaid=sayfa.sayfaid
            where 
                sayfasil='0' and sayfaaktif='1' $sqlek
        ";
        //die($urunler_s);
        if($data->query($urunler_s))
        {
            $urunler_v=$data->query($urunler_s); unset($urunler_s);
            if($urunler_v->num_rows>0)
            {
                while ($urunler_t=$urunler_v->fetch_assoc())
                {
                    $urunid=$urunler_t["urunid"];
                    $urunsatisfiyat=$urunler_t["urunsatisfiyat"];
                    $urunindirimsizfiyat=$urunler_t["urunindirimsizfiyat"];
                    $urunbayifiyat=$urunler_t["urunbayifiyat"];
                    $urunalisfiyat=$urunler_t["urunalisfiyat"];
                    $urunstokkodu=$urunler_t["urunstokkodu"];
                    if($f_islemtipi==1)
                    {
                        //indirim
                        if($f_indirimsizfiyat==1 || $f_tumfiyat==1)
                        {
                            if($f_fiyatok==1)
                            {
                                $urunindirimsizfiyat=$urunindirimsizfiyat-$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunindirimsizfiyat=$urunindirimsizfiyat*(1-$f_oran);
                            }
                            //$urunindirimsizfiyat=number_format($urunindirimsizfiyat, 2, '.', ',');
                        }
                        if($f_satisfiyat==1 || $f_tumfiyat==1)
                        {
                            if($f_fiyatok==1)
                            {
                                $urunsatisfiyat=$urunsatisfiyat-$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunsatisfiyat=$urunsatisfiyat*(1-$f_oran);
                            }
                            //$urunsatisfiyat=number_format($urunsatisfiyat, 2, '.', ',');
                        }
                        if($f_bayifiyat==1 || $f_tumfiyat==1)
                        {
                            if($f_fiyatok==1)
                            {
                                $urunbayifiyat=$urunbayifiyat-$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunbayifiyat=$urunbayifiyat*(1-$f_oran);
                            }
                            //$urunbayifiyat=number_format($urunbayifiyat, 2, '.', ',');
                        }
                        if($f_alisfiyat==1 || $f_tumfiyat==1)
                        {
                            if($f_fiyatok==1)
                            {
                                $urunalisfiyat=$urunalisfiyat-$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunalisfiyat=$urunalisfiyat*(1-$f_oran);
                            }
                            //$urunalisfiyat=number_format($urunalisfiyat, 2, '.', ',');
                        }
                    }
                    elseif($f_islemtipi==2)
                    {
                        //die("zam");//zam
                        if($f_indirimsizfiyat==1 || $f_tumfiyat==1)
                        {
                            //die("indirimsizfiyat");
                            if($f_fiyatok==1)
                            {
                                $urunindirimsizfiyat=$urunindirimsizfiyat+$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunindirimsizfiyat=$urunindirimsizfiyat*(1+$f_oran);
                            }
                            //$urunindirimsizfiyat=number_format($urunindirimsizfiyat, 2, '.', ',');
                        }
                        if($f_satisfiyat==1 || $f_tumfiyat==1)
                        {
                            //die("satisfiyat");
                            if($f_fiyatok==1)
                            {
                                $urunsatisfiyat=$urunsatisfiyat+$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunsatisfiyat=$urunsatisfiyat*(1+$f_oran);
                            }
                            //$urunsatisfiyat=number_format($urunsatisfiyat, 2, '.', ',');
                        }
                        if($f_bayifiyat==1 || $f_tumfiyat==1)
                        {
                            //die("bayifiyat");
                            if($f_fiyatok==1)
                            {
                                $urunbayifiyat=$urunbayifiyat+$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunbayifiyat=$urunbayifiyat*(1+$f_oran);
                            }
                            //$urunbayifiyat=number_format($urunbayifiyat, 2, '.', ',');
                        }
                        if($f_alisfiyat==1 || $f_tumfiyat==1)
                        {
                            //die("alisfiyat");
                            if($f_fiyatok==1)
                            {
                                $urunalisfiyat=$urunalisfiyat+$f_para;
                            }
                            elseif($f_fiyatok==2)
                            {
                                $urunalisfiyat=$urunalisfiyat*(1+$f_oran);
                            }
                            //$urunalisfiyat=number_format($urunalisfiyat, 2, '.', ',');
                        }
                    }
                    $urunalisfiyat=number_format($urunalisfiyat, 2, '.', '');
                    $urunbayifiyat=number_format($urunbayifiyat, 2, '.', '');
                    $urunsatisfiyat=number_format($urunsatisfiyat, 2, '.', '');
                    $urunindirimsizfiyat=number_format($urunindirimsizfiyat, 2, '.', '');

                    if($urunindirimsizfiyat!=0&&$urunindirimsizfiyat!="0.00")
                    {
                        $urunindirimorani=1-($urunsatisfiyat/$urunindirimsizfiyat);
                        if($urunsatisfiyat!="0.00")
                        {
                            $urunindirimorani=round(($urunindirimsizfiyat/$urunsatisfiyat)-1,2);
                        }
                        else
                        {
                            $urunindirimorani=0;
                        }
                    }
                    if(strlen("$urunindirimorani")==3)$urunindirimorani=$urunindirimorani."0";
                    if(strlen("$urunindirimorani")>4)$urunindirimorani=substr($urunindirimorani, 0,4);

                    $tablo="urunozellikleri";
                    $sutunlar="urunalisfiyat,urunsatisfiyat,urunbayifiyat,urunindirimsizfiyat,urunindirimorani";
                    $degerler=$urunalisfiyat."|*_".$urunsatisfiyat."|*_".$urunbayifiyat."|*_".$urunindirimsizfiyat."|*_".$urunindirimorani;
                    guncelle($sutunlar,$degerler,$tablo," urunstokkodu='". $urunstokkodu ."' ",39);
                }
                $formhataaciklama.=" Toplam ".$urunler_v->num_rows." adet ürüne uygulandı";
                unset($urunler_t);
            }
            else
            {
                $formhata=1;
                $formhataaciklama=" Bu kategori, marka ve ya tedarikçiye göre ürün bulunamadı";
            }
            unset($urunler_v);
        }
        else{hatalogisle("Toplu Fiyat",$data->error);}
    }
}
function kategoriliste($ustkategoriid,$kategorikatman,$urunkategorisecid)
{
	global $data;
	$urunkategori_d=0;
	$urunkategori_s="
		SELECT 
			kategori.kategoriid,kategoriad,dilid,kategorikatman 
		FROM 
			kategori 
			
		WHERE 
			kategorisil='0' and kategorigrup='7' and kategorikatman='".S($kategorikatman)."' and ustkategoriid='".S($ustkategoriid)."'
		
		ORDER BY 
			kategorikatman asc,kategori.kategoriid ASC";
	$urunkategori_v=$data->query($urunkategori_s);
	if($urunkategori_v->num_rows>0)$urunkategori_d=1;
	unset($urunkategori_s);

	if($urunkategori_d==1)
	{
		while ($urunkategori_t=$urunkategori_v->fetch_assoc())
		{
			$urunkategoridilid=$urunkategori_t["dilid"];
			$urunkategoriid=$urunkategori_t["kategoriid"];
			$urunkategoriad=$urunkategori_t["kategoriad"];
			$urunkategoridil=teksatir("select dilad from dil where dilid='". $urunkategoridilid ."'","dilad");
			$kategorikatman=$urunkategori_t["kategorikatman"];
			$katmanek=""; $secili="";
			if(S($urunkategoriid)==S($urunkategorisecid))$secili="selected";
			if($kategorikatman==1)
			{
				$katmanek=" -";
			}
			elseif($kategorikatman==2)
			{
				$katmanek=" --";
			}
			elseif($kategorikatman==3)
			{
				$katmanek=" ---";
			}
			if($kategorikatman==0)$kategoristyle=' style="font-weight:bold"';else $kategoristyle='';
			echo '
				<option value="'.$urunkategoriid.'" '.$kategoristyle.' '.$secili.' >
					'.$katmanek.' '.$urunkategoriad.'
				</option>
			';
			kategoriliste($urunkategoriid,S($kategorikatman)+1,$urunkategorisecid);
		}
	}
	unset($urunkategori_d,$urunkategori_v,$urunkategoriid,$urunkategoridilid,$urunkategoriad,$urunkategoridil);
}
if(BosMu($f_para))$f_para="0";
if(BosMu($f_oran))$f_oran="0.00";
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>SYM - Toplu Fiyat Güncelle</title>

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
							<li><a href="#">Genel Ayarlar</a></li>
							<li class="active"><?=$sayfabaslik?></li>
						</ol>
					</div>
					<?php
						if($formhata==0 && S(f("toplufiyatguncelle"))==1)
						{ ?>
							<div class="alert alert-success" role="alert">
								<?=$formhataaciklama?>
							</div>
						<?php
						}
						elseif($formhata==1 && S(f("toplufiyatguncelle"))==1)
						{ ?>
						<div class="alert alert-danger" role="alert">
							<?=$formhataaciklama?>
						</div>
						<?php } ?>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-offset-1 col-md-8">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
									</div>
									<form id="form_toplufiyat" class="form form-validation form-validate" role="form" method="post" novalidate="novalidate">
										<input type="hidden" name="toplufiyatguncelle" value="1">
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">
														<div class="col-md-3">
															<div class="form-group floating-label">
																<select id="kategoriid" name="kategoriid" class="form-control">
                                                                    <option value="0">KATEGORİ SEÇİN</option>
																	<?php kategoriliste(0,0,$f_kategoriid); ?>
																</select>
																<label for="menugrup">Kategori Seçin</label>
															</div>
														</div>
														<div class="col-sm-3">
															<div class="form-group floating-label">
																<select id="markaid" name="markaid" class="form-control">
                                                                    <option value="0">MARKA SEÇİN</option>
																<?php
																	if(!isset($data))Veri(true);
																	$marka_d=0; $marka_v=""; $marka_s="";
																	$marka_s="
																		SELECT 
																			urunmarka.markaid,markaad 
																		FROM 
																			urunmarka 
																			inner join urunozellikleri on urunozellikleri.markaid=urunmarka.markaid
																		Where 
																			markasil='0' 
																		Group By markaid
																	";
																	$marka_v=$data->query($marka_s);
																	if($marka_v -> num_rows > 0) $marka_d=1;
																	unset($marka_s);
																	if($marka_d==1)
																	{
																		while($marka_t=$marka_v->fetch_assoc())
																		{
																			$l_markaid = $marka_t["markaid"];
																			$l_markaad   = $marka_t["markaad"];
																			?>
																			<option value="<?=$l_markaid?>" <?php if($f_markaid==$l_markaid)echo 'selected';?>>
																				<?=$l_markaad?>
																			</option>
																			<?php
																		}
																		unset($marka_t,$marka_v);
																	}
																	unset($marka_v);
																?>
																</select>
																<label for="markaid">Marka Seçin</label>
															</div>
														</div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <select id="tedarikciid" name="tedarikciid" class="form-control">
                                                                    <option value="0">TEDARİKÇİ SEÇİN</option>
                                                                    <?php
                                                                    if(!isset($data))Veri(true);
                                                                    $tedarikci_d=0; $tedarikci_v=""; $tedarikci_s="";
                                                                    $tedarikci_s="
																		SELECT 
																			uruntedarikci.tedarikciid,tedarikciad 
																		FROM 
																			uruntedarikci 
																			inner join urunozellikleri on urunozellikleri.tedarikciid=uruntedarikci.tedarikciid
																		Where 
																			tedarikcisil='0' 
																		Group By tedarikciid
																	";
                                                                    $tedarikci_v=$data->query($tedarikci_s);
                                                                    if($tedarikci_v -> num_rows > 0) $tedarikci_d=1;
                                                                    unset($tedarikci_s);
                                                                    if($tedarikci_d==1)
                                                                    {
                                                                        while($tedarikci_t=$tedarikci_v->fetch_assoc())
                                                                        {
                                                                            $l_tedarikciid = $tedarikci_t["tedarikciid"];
                                                                            $l_tedarikciad   = $tedarikci_t["tedarikciad"];
                                                                            ?>
                                                                            <option value="<?=$l_tedarikciid?>" <?php if($f_tedarikciid==$l_tedarikciid)echo 'selected';?>>
                                                                                <?=$l_tedarikciad?>
                                                                            </option>
                                                                            <?php
                                                                        }
                                                                        unset($tedarikci_t,$tedarikci_v);
                                                                    }
                                                                    unset($tedarikci_v);
                                                                    ?>
                                                                </select>
                                                                <label for="tedarikciid">Tedarikci Seçin</label>
                                                            </div>
                                                        </div>
													</div>
													<div class="row">
														<div class="col-md-3">
															<label class="checkbox checkbox-styled">
																<input
																	type="checkbox"
																	name="alisfiyat" id="alisfiyat"
                                                                    value="1" >
																<span>Alış Fiyatına</span>
															</label>
														</div>
														<div class="col-md-3">
															<label class="checkbox checkbox-styled">
																<input
																	type="checkbox"
																	name="satisfiyat" id="satisfiyat"
																	value="1" >
																<span>Satış Fiyatına</span>
															</label>
														</div>
														<div class="col-md-3">
															<label class="checkbox checkbox-styled">
																<input
																	type="checkbox"
																	name="bayifiyat" id="bayifiyat"
																	value="1" >
																<span>Bayi Fiyatına</span>
															</label>
														</div>
														<div class="col-md-3">
															<label class="checkbox checkbox-styled">
																<input
																	type="checkbox"
																	name="indirimsizfiyat" id="indirimsizfiyat"
																	value="1" >
																<span>İndirimsiz Fiyata</span>
															</label>
														</div>
														<div class="col-md-3">
															<label class="checkbox checkbox-styled">
																<input
																	type="checkbox"
																	name="tumfiyat" id="tumfiyat"
																	value="1" >
																<span>Tüm fiyatlara</span>
															</label>
														</div>
													</div>
													<div class="row">
														<div class="col-md-3">

																<label class="checkbox checkbox-styled">
																	<input
																		type="radio"
																		name="islemtipi"
																		value="1" <?php if($f_islemtipi==1)echo 'checked ';?>>
																	<span>İndirim</span>
																</label>

														</div>
														<div class="col-md-9">
															<label class="checkbox checkbox-styled">
																<input
																	type="radio"
																	name="islemtipi"
																	value="2"  <?php if($f_islemtipi==2)echo 'checked ';?>>
																<span>Artış </span>
															</label>
														</div>
													</div>
													<div class="row">
														<div class="col-md-3">
															<label class="checkbox checkbox-styled">
																<input
																	type="radio"
																	name="fiyatok"
																	value="1" <?php if($f_fiyatok==1)echo 'checked ';?>>
																<span>Para Güncelle </span>
															</label>
														</div>
														<div class="col-md-9">
															<input type="text" name="para" id="para" value="<?=$f_para?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false" placeholder="55.99">
															<span>Para (55.99 = 55 TL 99 Kuruş)</span>
														</div>
													</div>
													<div class="row">
														<div class="col-md-3">
															<label class="checkbox checkbox-styled">
																<input
																	type="radio"
																	name="fiyatok"
																	value="2" <?php if($f_fiyatok==2)echo 'checked ';?>>
																<span>Oran Güncelle </span>
															</label>
														</div>
														<div class="col-md-9">
															<input type="text" name="oran" id="oran" value="<?=$f_oran?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false" placeholder="0.10">
															<span>Oran (0.10 = %10)</span>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
												<a class="btn btn-primary btn-default toplufiyatguncelle"
                                                   href="#textModal"
                                                   data-toggle="modal"
                                                   data-target="#simpleModal"
                                                   data-backdrop="true"
                                                ><?=$butonisim?></a>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
            <div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">TOPLU FİYAT GÜNCELLE</h4>
                        </div>
                        <div class="modal-body">
                            <p>TÜM ÜRÜNLER GÜNCELLENECEK?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="guncellebutton">ONAY</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>

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
			$("#toplufiyatguncellephp").addClass("active");
			$(document).on("click",".toplufiyatguncelle",function()
            {
                $kategoriid=$( "#kategoriid option:selected" ).val();
                $markaid=$("#markaid option:selected").val();
                $tedarikciid=$("#tedarikciid option:selected").val();
                /**/

                if($kategoriid!=0)
                {
                    $kategoriad=$( "#kategoriid option:selected" ).text();
                    $formaciklama="<p>Seçilen Kategori ("+$kategoriad+") Güncellenecek</p>";
                }
                if($markaid!=0)
                {
                    $markaad=$( "#markaid option:selected" ).text();
                    $formaciklama+="<p>Seçilen Marka ("+$markaad+") Güncellenecek</p>";
                }
                if($tedarikciid!=0)
                {
                    $tedarikciad=$( "#tedarikciid option:selected" ).text();
                    $formaciklama+="<p>Seçilen Marka ("+$tedarikciad+") Güncellenecek</p>";
                }
                if($('input[name=fiyatok]').is(':checked')==false)
                {
                    $formaciklama='<p>Fiyat değişikliği para olarak mı oran olarak mı güncellenecek?</p><p> Lütfen Seçin</p>';
                    $(".modal-body").html($formaciklama);
                    $("#guncellebutton").addClass("disabled");
                    return false;
                }
                else
                {
                    if($('input[name=fiyatok]:checked').val()==1)
                    {
                        $formaciklama+='<p>'+ $("#para").val() +' tutarında</p>';
                    }
                    else
                    {
                        $oran=$("#oran").val().replace("0.","%");
                        $oran=$oran.replace("1.","%1");
                        $oran=$oran.replace("2.","%2");
                        $oran=$oran.replace("3.","%3");
                        $formaciklama+='<p>'+ $oran +' oranında</p>';
                    }
                }
                if($('input[name=islemtipi]').is(':checked')==false)
                {
                    $formaciklama='<p>Fiyat değişikliği tipi seçin. Artış/İndirim.</p>';
                    $(".modal-body").html($formaciklama);
                    $("#guncellebutton").addClass("disabled");
                    return false;
                }
                else
                {
                    if($('input[name=islemtipi]:checked').val()==1)
                    {
                        $formaciklama+='<p>Düşecek.</p>';
                    }
                    else
                    {
                        $formaciklama+='<p>Artacak.</p>';
                    }
                }

                if($kategoriid!=0||$markaid!=0||$tedarikciid!=0)
                {
                    $formaciklama+='<p>Lütfen dikkatli olun, bu işlem geri alınamaz.</p>';
                    $(".modal-body").html($formaciklama);
                }
                else
                {
                    $formaciklama='<p>Ürün fiyatları seçilen kategori, marka ve tedarikçi bilgisine göre güncellenecektir.</p>';
                    $formaciklama=$formaciklama+'<p>Lütfen dikkatli olun, bu işlem geri alınamaz.</p>';
                    $(".modal-body").html($formaciklama);
                }
                $("#guncellebutton").removeClass("disabled");
            });
            $(document).on("click","#guncellebutton",function()
            {
                $("#form_toplufiyat").submit();
                $("#guncellebutton").addClass("disabled");
            });
		</script>
	</body>
</html>
