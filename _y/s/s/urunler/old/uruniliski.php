<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

if(S(f("toplukategoridegistir"))==1)
{
	$degistirkategorisayfalar=f("degistirkategorisayfalar");
	$degistirkategoriid=f("degistirkategoriid");
	if(!BosMu($degistirkategorisayfalar)&&!BosMu($degistirkategoriid))
	{
		$degistirkategorisayfalar_ayikla=explode(",", $degistirkategorisayfalar);
		foreach ($degistirkategorisayfalar_ayikla as $key => $degistirsayfaid)
		{
			sil("sayfalistekategori","sayfaid='".$degistirsayfaid."'",34);
			ekle("kategoriid,sayfaid",$degistirkategoriid."|*_".$degistirsayfaid,"sayfalistekategori",34);
		}
	}
}
if(S(f("topluurunsil"))==1)
{
	$topluurunsilsayfalar=f("topluurunsilsayfalar");
	if(!BosMu($topluurunsilsayfalar))
	{
		$topluurunsilsayfalar_ayikla=explode(",", $topluurunsilsayfalar);
		foreach ($topluurunsilsayfalar_ayikla as $key => $toplurunsilsayfaid)
		{
			$data->query("UPDATE sayfa SET sayfasil='1' WHERE sayfaid='". $toplurunsilsayfaid ."'");
		}
	}
}
//düzenle
$sayfabaslik="Ürünleri Düzenle";
$formbaslik="ÜRÜN LİSTE";

if(S(f("sayfasirala"))==1)
{
	foreach($_POST['sayfaid'] as $sayfaid)
	{
    	$sayfasira =S(f("sayfasira".$sayfaid));
    	guncelle("sayfasira",$sayfasira,"sayfa","sayfaid='".$sayfaid."'",0);
    }
}
$sqlek="";$katlinkek="";
if(S(q("kategori"))!=0||q("kategori")==-1)
{
	$sqlek=" and sayfalistekategori.kategoriid='".q("kategori")."'";
	$katlinkek="&kategori=".q("kategori");
}
$sayfalar_bitir=50;
$qsimdisayfa=S(q("sayfa"));
if($qsimdisayfa==0 || $qsimdisayfa==1)
{
	$sayfalar_basla=0;
}
else
{
	$sayfalar_basla=($qsimdisayfa-1)*$sayfalar_bitir;
}
$siralamaidlink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=0';
$siralamakatlink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=2';
$siralamaadlink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=4';
$siralamasiralink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=6';
if(S(q("sirala"))==0)
{
	$orderby="sayfa.sayfaid asc";
	$siralamaidlink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=1';
}
elseif(S(q("sirala"))==1)
{
	$orderby="sayfa.sayfaid desc";
}
elseif(S(q("sirala"))==2)
{
	$orderby="sayfalistekategori.kategoriid Asc";
	$siralamakatlink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=3';
}
elseif(S(q("sirala"))==3)
{
	$orderby="sayfalistekategori.kategoriid Desc";
}
elseif(S(q("sirala"))==4)
{
	$orderby="sayfaad,kategoriid Asc";
	$siralamaadlink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=5';
}
elseif(S(q("sirala"))==5)
{
	$orderby="sayfaad Desc";
}
elseif(S(q("sirala"))==6)
{
	$orderby="kategoriid,sayfasira Asc";
	$siralamasiralink='/_y/s/s/urunler/uruniliski.php?sayfa='.$qsimdisayfa.'&sirala=7';
}
elseif(S(q("sirala"))==7)
{
	$orderby="kategoriid,sayfasira Desc";
}

Veri(true);
$urunler_bitir=50;
$urunlertoplamsayfa=0;
$urunlertoplam_s="
	SELECT 
		sayfa.sayfaid
	FROM 
		sayfa
			left join sayfalistekategori on
				sayfalistekategori.sayfaid=sayfa.sayfaid
				left join kategori on 
					kategori.kategoriid=sayfalistekategori.kategoriid
			left join sayfalisteresim on 
				sayfalisteresim.sayfaid=sayfa.sayfaid
				left join resim on 
					resim.resimid=sayfalisteresim.resimid
					Left join resimklasor on
						resimklasor.resimklasorid=resim.resimklasorid 
			left join seo on 
				seo.benzersizid=sayfa.benzersizid
			left join urunozellikleri on 
			    urunozellikleri.sayfaid=sayfa.sayfaid
	WHERE 
		sayfasil='0' and sayfatip='7' $sqlek 
	Group BY 
		sayfa.sayfaid
";
if($data->query($urunlertoplam_s))
{
	$urunlertoplam=$data->query($urunlertoplam_s)->num_rows;unset($urunlertoplam_s);
}else{hatalogisle("Ürün Liste toplam",$data->error);}
$urunlertoplamsayfa=ceil($urunlertoplam/$urunler_bitir);

if($qsimdisayfa==0||$qsimdisayfa==1)$urunler_basla=0;else$urunler_basla=($qsimdisayfa-1)*$urunler_bitir;

$urunler_d=0;
$urunler_s="
	SELECT 
		sayfa.sayfaid,sayfaad,sayfa.benzersizid,sayfaaktif,
		kategoriad,
		resim.resim,resimklasorad,
		urunozellikleri.urunsatisfiyat,
		urunozellikleri.urungununfirsati,
		link,kelime,baslik,aciklama,
		uruniliski.urunid as iliskiurunid,iliskiid
	FROM 
		sayfa
			left join sayfalistekategori on
				sayfalistekategori.sayfaid=sayfa.sayfaid
				left join kategori on 
					kategori.kategoriid=sayfalistekategori.kategoriid
			left join sayfalisteresim on 
				sayfalisteresim.sayfaid=sayfa.sayfaid
				left join resim on 
					resim.resimid=sayfalisteresim.resimid
					Left join resimklasor on
						resimklasor.resimklasorid=resim.resimklasorid 
			left join seo on 
				seo.benzersizid=sayfa.benzersizid
			left join urunozellikleri on 
			    urunozellikleri.sayfaid=sayfa.sayfaid
			left join uruniliski on 
			    uruniliski.urunid=sayfa.sayfaid
	WHERE 
		sayfasil='0' and sayfatip='7' $sqlek 
	Group BY 
		sayfa.sayfaid
	ORDER BY 
		$orderby 
	LIMIT $urunler_basla, $urunler_bitir
";
if($data->query($urunler_s))
{
	$urunler_v=$data->query($urunler_s);unset($urunler_s);
	if($urunler_v->num_rows>0)
	{
		$urunler_d=1;

	}
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
		<style>
			.gizlitr {
			    position: absolute;
			    width: 100%;
			    height: 100%;
			    z-index: 999;
			    left: 0;
			    top: 0;display: none;
		}
		</style>
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
							<li><a href="#">ÜRÜN BİLGİLERİ</a></li>
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
										<div class="row" id="kategoridivler">
											<input type="hidden" name="kategoriid" id="kategoriid" value="<?=q("kategori")?>">
											<div id="kategoridiv0" class="col-sm-6 form-group floating-label">
												<?php
													$sorgu="SELECT * FROM  kategori where kategoriaktif='1' and kategorisil='0' and kategorigrup='7' and ustkategoriid='0' ";
													 if($data->query($sorgu))
													 {
													 	$sonuc=$data->query($sorgu);
													 	$sonuctoplam=$sonuc->num_rows;
													 	if ($sonuctoplam>0)
													 	{
															echo '<select class="form-control" size="5" data-id="0">';
													 		while($sonucliste=$sonuc->fetch_assoc()){
													 			$kategorisec="";
													 			$kategoriad=$sonucliste["kategoriad"];
													 			$kategoriid=$sonucliste["kategoriid"];
													 			if($kategoriid==q("kategoriid"))$kategorisec="selected";
													 			echo '<option value="'.$kategoriid.'" '.$kategorisec.'>'.$kategoriad.'</option>';
													 		}
															echo '</select>';
													 	}
													 		 }else{
													 	die($data->error);
													 };
												?>
											</div>
										</div>
										<div class="form-group">
											<input type="text" name="q" id="q" class="form-control" placeholder="Arama:Ürün Başlığını yazın" value="">
										</div>
										<div class="form-group">
											<div class="col-md-6">
												<span class="btn ink-reaction btn-xs btn-flat col-md-3">Listeyi:</span>
												<div class="col-md-1"></div>
												<a href="/_y/s/s/urunler/uruniliski.php" class=" btn ink-reaction btn-raised btn-xs btn-warning col-md-3">Sıfırla</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body">
										<div class="table-responsive">
											<table class="table no-margin">
												<thead>
													<tr>
														<th>Seç</th>
														<th>F</th>
														<th class="sorting"><a href="<?=$siralamaidlink.$katlinkek?>">#</a></th>
														<th>Resim</th>
														<th class="sorting"><a href="<?=$siralamaadlink.$katlinkek?>">Ad</a></th>
														<th class="sorting"><a href="<?=$siralamakatlink.$katlinkek?>">Kategori</a></th>
														<th>Fiyat</th>
														<th>Stok</th>
														<th>İşlem</th>
														<th>Aktif</th>
														<th>Gör</th>
													</tr>
												</thead>
												<tbody>
													<tr><td colspan="3"></td><td colspan="8">Toplam: <?=$urunlertoplam?> ürün ve <?=$urunlertoplamsayfa?> sayfa</td></tr>
												<?php
												$i=0;
												if($qsimdisayfa>1)$i=($urunler_basla*2)-$urunler_bitir;
												if($urunler_d==1)
												{
													while($urunler_t=$urunler_v->fetch_assoc())
													{
														$i++;$fiyatyaz="";$resimklasorad="";
														$benzersizid=$urunler_t["benzersizid"];
														$sayfaid=$urunler_t["sayfaid"];
														$sayfaad=$urunler_t["sayfaad"];
														$sayfaaktif=$urunler_t["sayfaaktif"];
														$kategoriad=$urunler_t["kategoriad"];

														$resim=$urunler_t["resim"];
                                                        if(BosMu($resim))
                                                        {
                                                            $resim="bos.jpg";
                                                        }
                                                        else
                                                        {
                                                            $resimbilgi=coksatir("SELECT resim.resim,resimklasorad from sayfa 
                                                            inner join sayfalisteresim on 
                                                                            sayfalisteresim.sayfaid=sayfa.sayfaid
                                                                            inner join resim on 
                                                                                resim.resimid=sayfalisteresim.resimid
                                                                                inner join resimklasor on
                                                                                    resimklasor.resimklasorid=resim.resimklasorid 
                                                            WHERE sayfa.sayfaid='".$sayfaid."' order by sayfalisteresimid asc");
                                                            if(!BosMu($resimbilgi))
                                                            {
                                                                $resim=$resimbilgi["resim"];
                                                                $resimklasorad=$resimbilgi["resimklasorad"];
                                                            }
                                                            unset($resimbilgi);
                                                        }

														$urunsatisfiyat=$urunler_t["urunsatisfiyat"];
														$urungununfirsati=$urunler_t["urungununfirsati"];

														$seolink=$urunler_t["link"];
														$seobaslik=$urunler_t["baslik"];
														$seoaciklama=$urunler_t["aciklama"];
														$seokelime=$urunler_t["kelime"];

														$resimsayisi=teksatir("Select count(*) as resimsayisi from sayfalisteresim where sayfaid='".$sayfaid."'","resimsayisi");
														$urunsayisi=teksatir("Select sum(urunstok) as urunsayisi from urunozellikleri where sayfaid='".$sayfaid."'","urunsayisi");
														$iliskidurum="YOK";
														$iliskiurunid=$urunler_t["iliskiurunid"];
                                                        $iliskiid=$urunler_t["iliskiid"];
                                                        if(dogrula("uruniliki","urunid='$sayfaid' and iliskiid='0'"))
                                                        {
                                                            $iliskidurum="<span class='ink-reaction btn-xs btn-success'>İlişkili Ürün: VAR</span>";
                                                        }
														?>
														<tr id="tr<?=$sayfaid?>" data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>">
															<td>
                                                                <label class="checkbox-inline checkbox-styled checkbox-primary">
                                                                    <input name="sayfalar[]" type="checkbox" value="<?=$sayfaid?>" disabled>
                                                                </label>
                                                            </td>
															<td class="firsat text-center <?php if(S($urungununfirsati)==1){?>style-warning<?php }else{?>style-default<?php }?>">
																<?php if(S($urungununfirsati)==1){?>
																	<a href="/_y/s/f/sil.php?sil=urunfirsat&id=<?=$sayfaid?>&islem=0" target="_islem">
																		<i class="md md-grade" title="Fırsat Ürünleri Çıkart"></i>
																	</a>
																<?php }else{?>
																	<a href="/_y/s/f/sil.php?sil=urunfirsat&id=<?=$sayfaid?>&islem=1" target="_islem">
																		<i class="md md-grade" title="Fırsat Ürünleri Ekle"></i>
																	</a>
																<?php }?>
															</td>
															<td><?=$i?>)</td>
															<td>
                                                                <img
                                                                    src="<?="/m/r/?resim=$resimklasorad"."/"."$resim"?>&g=70&y=70"
                                                                    width="50" height="40">
                                                            </td>
															<td data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>" class="urunsatir"><?=$iliskidurum?> <?=$sayfaad?> resim sayısı: <?="$resimsayisi"?> Ürün id:<?=$sayfaid?> </td>
															<td><?=$kategoriad?></td>
															<td><?=$urunsatisfiyat;?></td>
															<td><?=$urunsayisi?></td>
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
																	<i class="fa fa-trash-o"></i>
																</a>
															</td>
															<td
																class="bilgi <?php if(S($sayfaaktif==1)){?>style-info<?php }else{?>style-danger<?php }?> text-center">
																<a href="/_y/s/f/sil.php?sil=urunaktif&id=<?=$sayfaid?>" target="_islem">
																<?php if(S($sayfaaktif==1)){?><i class="aktif md md-thumb-up" title="Aktif"></i><?php }else{?>
																<i class="aktif md md-error" title="Pasif"></i><?php }?></a>
															</td>
															<td>
																<a
																	href="<?=$seolink?>"
																	title="Sayfayı Gör"
																	target="_blank">
																	<i class="fa fa-external-link"></i>
																</a>
															</td>
														</tr>

														<tr id="trgizli<?=$sayfaid?>" style="display:none" class="style-accent-bright">
                                                            <td colspan="11">
                                                                <form
                                                                        id="formiliskiliurunler<?=$sayfaid?>"
                                                                        class="form form-validation form-validate"
                                                                        action="uruniliskiguncelle.php"
                                                                        method="post"
                                                                        target="_islem"
                                                                        novalidate="novalidate">
                                                                    <input type="hidden" name="uruniliski" value="1">
                                                                    <input type="hidden" name="sayfaid" value="<?=$sayfaid?>">
                                                                    <div class="form-group">
																		<a
																			data-id="trgizli<?=$sayfaid?>"
																			data-ustid="tr<?=$sayfaid?>"
																			class="urunsatiralt btn ink-reaction btn-raised btn-xs style-danger"
																			>KAPAT (x)
																		</a>
																	</div>
																	<div class="card row" style="margin-left: 5px; margin-right: 5px">
																		<div class="card-body">
																			<div class="form-group">
																				<input
																					type="text"
																					name="urunbaslik"
                                                                                    data-id="<?=$sayfaid?>"
																					id="urunbaslik<?=$sayfaid?>"
																					class="form-control urunad"
																					placeholder="Ürün Adı Girin"
																					value=""
																					data-rule-minlength="5"
																					maxlength="65"
																					aria-invalid="false"
																					required aria-required="true">
																				<label for="urunbaslik<?=$sayfaid?>"
																					style="margin-top:-10px">Ürün Adı Girin</label>
																			</div>
																		</div>
                                                                        <ul style="position:absolute;width:100%;z-index:2;display:none;background-color:#fff"" id="sonuclar<?=$sayfaid?>"></ul>
																	</div>
																	<div class="card row" style="margin-left: 5px; margin-right: 5px">
																		<div class="card-body">
																			<div>
                                                                                <select ondblclick="Cikar('iliskiliurunler<?=$sayfaid?>')"
                                                                                    class="form-control dirty"
                                                                                    size="5"  aria-invalid="false"
																					name="iliskiliurunler[]"
																					id="iliskiliurunler<?=$sayfaid?>"
                                                                                        multiple>
                                                                                    <?php
                                                                                        $iliskilise_s="
                                                                                            Select 
                                                                                                sayfa.sayfaid,sayfaad 
                                                                                            from 
                                                                                                uruniliski inner join sayfa on 
                                                                                                    sayfa.sayfaid=uruniliski.urunid 
                                                                                            where 
                                                                                                (iliskiid='".$sayfaid."' and sayfaid!='0') or (sayfaid='".$sayfaid."')
                                                                                        ";
                                                                                        if($data->query($iliskilise_s))
                                                                                        {
                                                                                            $iliskilise_v=$data->query($iliskilise_s);
                                                                                            if($iliskilise_v->num_rows>0)
                                                                                            {
                                                                                                while ($iliskilise_t=$iliskilise_v->fetch_assoc())
                                                                                                {
                                                                                                    $iliskisayfaid=$iliskilise_t["sayfaid"];
                                                                                                    $iliskisayfaad=$iliskilise_t["sayfaad"];
                                                                                                    echo '<option value="'.$iliskisayfaid.'">'.$iliskisayfaad.'</option>';
                                                                                                }
                                                                                            }
                                                                                        }else{die($data->error);}
                                                                                    ?>
                                                                                    ?></select>
																				<label
                                                                                    for="iliskiliurunler<?=$sayfaid?>"
																					style="margin-top:-10px">İlişkili Ürünler</label>
																			</div>
																			<div class="card-actionbar">
																				<div class="card-actionbar-row">
																					<button
																						type="button"
                                                                                        data-id="<?=$sayfaid?>"
																						class="btn btn-primary btn-default btniliski">ÜRÜN İLŞKİ GÜNCELLE</button>
																				</div>
																			</div>
																		</div>
																	</div>
                                                                </form>
                                                            </td>
														</tr>
														<?php
													}unset($urunler_t);
												}
												unset($urunler_d,$urunler_v,$sayfaid,$sayfaad);
												?>
												<tr><td colspan="11"><?php
													if($qsimdisayfa==0)$qsimdisayfa=1;
													sayfala("uruniliski.php?sirala=".q("sirala"),$urunlertoplamsayfa,$qsimdisayfa);
													?></td></tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>
        <div class="modal fade" id="iliskiguncelle" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="simpleModalLabel">Güncelleme</h4>
                    </div>
                    <div class="modal-body">
                        <p>Başarılı</p>
                    </div>
                </div>
            </div>
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
		<div class="modal fade" id="kategoridegistirpencere" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="btn-popup-kategoridegistir-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="simpleModalLabel">Toplu Kategori Değiştir</h4>
					</div>
					<form name="toplukategoridegistirform" id="toplukategoridegistirform" method="post">
						<input type="hidden" name="toplukategoridegistir" value="1">
						<div class="modal-body">
							<p>Lütfen Kategori Seçin</p>
							<div class="row" id="degistirkategoridivler">
								<input type="hidden" name="degistirkategoriid" id="degistirkategoriid" value="0">
								<input type="hidden" name="degistirkategorisayfalar" id="degistirkategorisayfalar" value="">
								<div id="degistirkategoridiv0" class="col-sm-6 form-group floating-label">
									<?php
										$sorgu="SELECT * FROM  kategori where kategoriaktif='1' and kategorisil='0' and kategorigrup='7' and ustkategoriid='0' ";
										if($data->query($sorgu))
										{
										 	$sonuc=$data->query($sorgu);
										 	$sonuctoplam=$sonuc->num_rows;
										 	if ($sonuctoplam>0)
										 	{
												echo '<select class="form-control" size="5" data-id="0">';
										 		while($sonucliste=$sonuc->fetch_assoc()){
										 			$kategorisec="";
										 			$kategoriad=$sonucliste["kategoriad"];
										 			$kategoriid=$sonucliste["kategoriid"];
										 			if($kategoriid==q("kategoriid"))$kategorisec="selected";
										 			echo '<option value="'.$kategoriid.'" '.$kategorisec.'>'.$kategoriad.'</option>';
										 		}
												echo '</select>';
										 	}
										 		 }else{
										 	die($data->error);
										};
									?>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
							<button type="button" class="btn btn-primary" id="kategoridegistirbutton">DEĞİŞTİR</button>
						</div>
					</form>
				</div>
			</div>
		</div>
        <a
                id="iliskiguncellelink"
                href="#textModal"
                class="btn btn-icon-toggle"
                data-toggle="modal"
                data-placement="top"
                data-original-title="Sil"
                data-target="#iliskiguncelle"
                data-backdrop="true">
        </a>
		<?php /*TOPLU SİL POPUP*/?>
		<div class="modal fade" id="topluurunsilpencere" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="btn-popup-topluurunsil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="simpleModalLabel">Toplu Ürün Sil</h4>
					</div>
					<form name="topluurunsilform" id="topluurunsilform" method="post">
						<input type="hidden" name="topluurunsil" value="1">
						<input type="hidden" name="topluurunsilsayfalar" id="topluurunsilsayfalar" value="">
						<div class="modal-body">
							<p>Seçilen ürünleri silmek istediğinize emin misiniz?</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
							<button type="button" class="btn btn-primary" id="topluurunsilbutton">SİL</button>
						</div>
					</form>
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
				$(document).on("click",'a#urunsil',function ()
				{
					$silid=$(this).data("id");//alert($silid);
				});
				$(document).on("document",'#silbutton',function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=urun&id="+$silid);
				});
			 });
			$(document).on("click", ".urunsatir",function()
			{
				$satir=$(this).data("id");
				$ustsatir=$(this).data("ustid");
				//$("#tbody"+$ustsatir).show();
				$("#"+$satir).show();
				$("#2"+$satir).show();
				$("#"+$ustsatir).addClass("style-accent-light");
				//$("#"+$ustsatir).css("background-color","#ddd");
				location.replace("#"+$ustsatir);
			});
			$(document).on("click",".urunsatiralt",function()
			{
				$satir=$(this).data("id");
				$("#"+$satir).hide();
				$("#2"+$satir).hide();
				$ustsatir=$(this).data("ustid");
				//$("#"+$ustsatir).css("background-color","#fff");
			});
			$( "#q" ).keyup(function()
			{
				if($("#q").val().length>=3)
				{
                    /*
                    *@iliskili ürün arama yap
                     */
					//$('#_islem').attr('src', "/_y/s/f/urunbul.php?q="+$("#q").val());
					$q=$("#q").val();
					$deger=$("#kategoriid").val();
					$.ajax({
						type: 'GET',
						url:"uruniliskili_urunbul.php?q="+$q+"&kategori="+$deger,
						dataType: "html",
						success: function(data)
						{
							if($.trim(data))
							{
								$("tbody").html(data);
							}
							else
							{
								$("tbody").html("");
							}
						}
					});
				}
			});
			/*$( "#kategori" ).change(function()
			{
				window.location.href = "/_y/s/s/urunler/uruniliski.php?kategori="+$("#kategori").val();
			});*/
			$(document).on('click', '#kategoridivler select', function()
			{
				$katman=$(this).data("id");
				$toplamkatman = $("div[id*='kategoridiv']").length;

				$deger=$(this).find('option:selected').val();
				$("#kategoriid").val($deger);
				$.ajax({
					type: 'GET',
					url:"uruniliskilistekategori.php?kategori="+$deger,
					dataType: "html",
					success: function(data)
					{
						if($.trim(data))
						{
							$("tbody").html(data);
						}
						else
						{
							$("tbody").html("");
						}
					}
				});
				$.ajax({
					type: 'GET',
					url:"kategorigetir.php?id="+$deger,
					dataType: "html",
					success: function(data)
					{
						if($.trim(data))
						{
							$katman=$katman+1;
							for($i=$katman;$i<$toplamkatman;$i++)
							{
								if($("#kategoridiv"+$i).length)
								{
									$("#kategoridiv"+$i).remove();
								}
							}

							$("#kategoridivler").append('<div class="col-sm-6 form-group floating-label" id="kategoridiv'+$katman+'">Yükleniyor</div>');
							$("#kategoridiv"+$katman).html(data);
							$("#kategoridiv"+$katman+" select").attr('data-id', $katman);
						}
						else
						{
							$i=$katman+1;
							$("#kategoridiv"+$i).remove();
						}
					}
				});
			});
			/*$(document).on("click", "#kategoridegistirbutton", function()
			{
				$("#degistirkategorisayfalar").val("");
				$degistirkategorisayfalar="";
				$('input[name="sayfalar[]"]:checked').each(function()
				{
					if($degistirkategorisayfalar=="")
					{
						$degistirkategorisayfalar=this.value;
					}
					else
					{
						$degistirkategorisayfalar=$degistirkategorisayfalar +","+ this.value;
					}
				});
				$("#degistirkategorisayfalar").val($degistirkategorisayfalar);
				if($("#degistirkategoriid").val()==0)
				{
					alert("Bir Kategori Seçmelisiniz");
				}else if($("#degistirkategorisayfalar").val()=="")
				{
					alert("En Az Bir Ürün Seçmelisiniz");
				}
				else
				{
					$('#toplukategoridegistirform')[0].submit();
				}
			});
			$(document).on("click", "#topluurunsilbutton", function()
			{
				$("#topluurunsilsayfalar").val("");
				$topluurunsilsayfalar="";
				$('input[name="sayfalar[]"]:checked').each(function()
				{
					if($topluurunsilsayfalar=="")
					{
						$topluurunsilsayfalar=this.value;
					}
					else
					{
						$topluurunsilsayfalar=$topluurunsilsayfalar +","+ this.value;
					}
				});
				$("#topluurunsilsayfalar").val($topluurunsilsayfalar);
				if($("#topluurunsilsayfalar").val()=="")
				{
					alert("En Az Bir Ürün Seçmelisiniz");
				}
				else
				{
					$('#topluurunsilform')[0].submit();
				}
			});
			$(document).on('click', '#degistirkategoridivler select', function()
			{
				$katman=$(this).data("id");
				$toplamkatman = $("div[id*='degistirkategoridiv']").length;

				$deger=$(this).find('option:selected').val();
				$("#degistirkategoriid").val($deger);
				$.ajax({
					type: 'GET',
					url:"kategorigetir.php?id="+$deger,
					dataType: "html",
					success: function(data)
					{
						if($.trim(data))
						{
							$katman=$katman+1;
							for($i=$katman;$i<$toplamkatman;$i++)
							{
								if($("#degistirkategoridiv"+$i).length)
								{
									$("#degistirkategoridiv"+$i).remove();
								}
							}

							$("#degistirkategoridivler").append('<div class="col-sm-6 form-group floating-label" id="degistirkategoridiv'+$katman+'">Yükleniyor</div>');
							$("#degistirkategoridiv"+$katman).html(data);
							$("#degistirkategoridiv"+$katman+" select").attr('data-id', $katman);
						}
						else
						{
							$i=$katman+1;
							$("#degistirkategoridiv"+$i).remove();
						}
					}
				});
			});*/
		</script>
		<script>
            $(document).ready(function()
            {
                $sayfaid=0;
                $(document).on("keyup",".urunad",function()
                {
                    // Veriyi alalım
                    $sayfaid=$(this).attr('data-id');
                    var value = $(this).val();
                    var deger = value;

                   $.ajax({
                        type: "POST",
                        data: "urunad="+deger,
                        url: "urunadara.php",
                        success: function(cevap){
                            if(cevap == "yok")
                            {
                                $("#sonuclar"+$sayfaid).show();
                                $("#sonuclar"+$sayfaid).html(cevap);
                            }
                            else if(cevap == " ")
                            {
                                $("#sonuclar"+$sayfaid).hide();
                            }
                            else
                            {
                                $("#sonuclar"+$sayfaid).show();
                                $("#sonuclar"+$sayfaid).html('<button type="button" class="sonuckapat btn ink-reaction btn-warning btn-xs" style="float:left;margin-top:-40px"><i class="md md-close no-margin" style="margin:0"></i> Sonuçları Kapat</button><button type="button" data-id="'+$sayfaid+'" class="btn btn-primary btn-default btniliski btn-xs" style="float:right;margin-top:-40px">ÜRÜN İLŞKİ GÜNCELLE</button>'+cevap);
                            }
                        }
                    });
                });
                $(document).on("click",".iliskisonuc",function()
                {
                    $value = $(this).attr('data-id');
                    $text = $(this).text();
                    $('#iliskiliurunler'+$sayfaid).append($('<option>',{
                        value: $value,
                        text: $text
                    }));
                    $('#urunbaslik'+$sayfaid).val("");

                    //$("#sonuclar"+$sayfaid).hide();
                    $(this).hide('slow');
                });
                $(document).on("click",".sonuckapat",function()
                {
                    $("#sonuclar"+$sayfaid).hide();
                });
                $(document).on("click",".btniliski",function()
                {
                    $id=$(this).data("id");
                    $('#iliskiliurunler'+ $id +' option').each(function()
                    {
                        $(this).attr('selected', 'selected');
                    });
                    $('#formiliskiliurunler'+ $id).submit();
                });
            });
            function Cikar(nereden)
            {
                $('#'+ nereden +' option:selected').remove();
            }
			$("#uruniliskiphp").addClass("active");
		</script>
	</body>
</html>
