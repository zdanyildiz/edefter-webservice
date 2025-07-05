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
            $data->query("DELETE FROM sayfalistekategori WHERE sayfaid='". $toplurunsilsayfaid ."'");
            $data->query("DELETE FROM sayfalisteresim WHERE sayfaid='". $toplurunsilsayfaid ."'");
            $data->query("DELETE FROM urunozellikleri WHERE sayfaid='". $toplurunsilsayfaid ."'");
            $f_benzersizid=teksatir(" Select benzersizid from sayfa Where sayfaid='". $toplurunsilsayfaid ."'","benzersizid");
            $data->query("DELETE FROM seo WHERE benzersizid='". $f_benzersizid ."'");
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
$sqlek="";$sqtablolek="";$katlinkek="";
if(S(q("kategori"))!=0||q("kategori")==-1)
{
	$sqlek=" and sayfalistekategori.kategoriid='".q("kategori")."'";
	$katlinkek="&kategori=".q("kategori");
    $sqtablolek=" INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid ";
}
if(S(q("resim"))==1)
{
    $sqlek=" and isnull(sayfalisteresim.resimid) ";
    $sqtablolek=" INNER JOIN sayfalisteresim ON sayfalisteresim.sayfaid=sayfa.sayfaid ";
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
$siralamaidlink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=0';
$siralamakatlink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=2';
$siralamaadlink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=4';
$siralamasiralink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=6';
if(S(q("sirala"))==0)
{
	$orderby="sayfa.sayfaid asc";
	$siralamaidlink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=1';
}
elseif(S(q("sirala"))==1)
{
	$orderby="sayfa.sayfaid desc";
}
elseif(S(q("sirala"))==2)
{
	$orderby="sayfalistekategori.kategoriid Asc";
	$siralamakatlink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=3';
    $sqtablolek=" INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid ";
}
elseif(S(q("sirala"))==3)
{
	$orderby="sayfalistekategori.kategoriid Desc";
    $sqtablolek=" INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid ";
}
elseif(S(q("sirala"))==4)
{
	$orderby="sayfaad Asc";
	$siralamaadlink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=5';
}
elseif(S(q("sirala"))==5)
{
	$orderby="sayfaad Desc";
}
elseif(S(q("sirala"))==6)
{
	$orderby="kategoriid,sayfasira Asc";
	$siralamasiralink='/_y/s/s/urunler/urunliste.php?sayfa='.$qsimdisayfa.'&sirala=7';
    $sqtablolek=" INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid ";
}
elseif(S(q("sirala"))==7)
{
	$orderby="kategoriid,sayfasira Desc";
    $sqtablolek=" INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid ";
}
$urunal=1;
$urunler_d=0;$urunlertoplam=0;$urunler_bitir=50;$urunlertoplamsayfa=0;
if($qsimdisayfa==0||$qsimdisayfa==1)$urunler_basla=0;else$urunler_basla=($qsimdisayfa-1)*$urunler_bitir;
$filtre="";
if(isset($_COOKIE["urunlertoplam"])){$urunlertoplam=$_COOKIE["urunlertoplam"];$filtre=" LIMIT $urunler_basla, $urunler_bitir";}

Veri(true);

    $urunlertoplam_s="
        SELECT 
            sayfa.sayfaid,sayfaad,sayfaaktif,sayfa.benzersizid
        FROM 
            sayfa $sqtablolek
        WHERE 
            sayfasil='1' and sayfatip='7' $sqlek
        Group BY 
            sayfa.sayfaid
    ";
    if($data->query($urunlertoplam_s))
    {
        $urunler_v=$data->query($urunlertoplam_s);
        $urunlertoplam=$urunler_v->num_rows;
        if($urunlertoplam>0){
            $urunler_d=1;setcookie("urunlertoplam", $urunlertoplam, time()+60*60*24*365, "/");
        }else{setcookie("urunlertoplam", 0, time()-60*60*24*365, "/");}
    }else{hatalogisle("Ürün Liste toplam",$data->error);}unset($urunlertoplam_s);

$urunlertoplamsayfa=ceil($urunlertoplam/$urunler_bitir);
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
												if($qsimdisayfa>1)$i=$urunler_basla;
												if($urunler_d==1)
												{
													while($urunler_t=$urunler_v->fetch_assoc())
													{
														$i++;$fiyatyaz="";$resimklasorad="";
														//die("$urunler_basla - $urunler_bitir");
														if($i>=$urunler_basla)
                                                        {
                                                            $sayfaid=$urunler_t["sayfaid"];
                                                            $sayfaad=$urunler_t["sayfaad"];
                                                            $sayfaaktif=$urunler_t["sayfaaktif"];
                                                            $benzersizid=$urunler_t["benzersizid"];

                                                            $urunbilgi=coksatir("SELECT kategoriad,urunmodel,urunsatisfiyat,urungununfirsati,link,baslik,aciklama,kelime from urunozellikleri INNER JOIN sayfa ON sayfa.sayfaid=urunozellikleri.sayfaid LEFT JOIN seo ON seo.benzersizid=sayfa.benzersizid INNER JOIN sayfalistekategori ON sayfalistekategori.sayfaid=urunozellikleri.sayfaid INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid WHERE urunozellikleri.sayfaid='".$sayfaid."'");
                                                            if(!BosMu($urunbilgi))
                                                            {
                                                                $kategoriad=$urunbilgi["kategoriad"];
                                                                $model=$urunbilgi["urunmodel"];
                                                                $urunsatisfiyat=$urunbilgi["urunsatisfiyat"];
                                                                $urungununfirsati=$urunbilgi["urungununfirsati"];

                                                                $seolink=$urunbilgi["link"];
                                                                $seobaslik=$urunbilgi["baslik"];
                                                                $seoaciklama=$urunbilgi["aciklama"];
                                                                $seokelime=$urunbilgi["kelime"];
                                                            }
                                                            unset($urunbilgi);
                                                            $resimbilgi=coksatir("SELECT resim.resim,resimklasorad FROM sayfalisteresim INNER JOIN resim ON resim.resimid=sayfalisteresim.resimid INNER JOIN resimklasor ON resimklasor.resimklasorid=resim.resimklasorid Where sayfalisteresim.sayfaid='".$sayfaid."' Group By resim.resimid order by sayfalisteresimid ASC");
                                                            if(!BosMu($resimbilgi))
                                                            {
                                                                $resim=$resimbilgi["resim"];
                                                                $resimklasorad=$resimbilgi["resimklasorad"];
                                                            }
                                                            unset($resimbilgi);
                                                            $resimsayisi=teksatir("Select count(*) as resimsayisi from sayfalisteresim where sayfaid='".$sayfaid."'","resimsayisi");
                                                            $urunsayisi=teksatir("Select sum(urunstok) as urunsayisi from urunozellikleri where sayfaid='".$sayfaid."'","urunsayisi");
                                                            ?>
                                                            <tr id="tr<?=$sayfaid?>" data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>">
                                                                <td></td>
                                                                <td class="firsat text-center ">

                                                                </td>
                                                                <td><?=$i?>)</td>
                                                                <td><i class="fa fa-trash"></i></td>
                                                                <td data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>" class="urunsatir"><?=$sayfaad?> (resim: <?="$resimsayisi"?>) (id:<?=$sayfaid?>)</td>
                                                                <td><?=$kategoriad?></td>
                                                                <td><?=$urunsatisfiyat;?></td>
                                                                <td><?=$urunsayisi?></td>
                                                                <td>


                                                                </td>
                                                                <td class="bilgi  text-center">

                                                                </td>
                                                                <td>

                                                                </td>
                                                            </tr>
                                                            <tr id="trgizli<?=$sayfaid?>" style="display:none" class="style-accent-bright">
                                                                <form
                                                                        class="form form-validation form-validate"
                                                                        action="/_y/s/f/urunguncellex.php"
                                                                        method="post"
                                                                        target="_islem"
                                                                        novalidate="novalidate">
                                                                    <input type="hidden" name="seo" value="1">
                                                                    <input type="hidden" name="sayfaid" value="<?=$sayfaid?>">
                                                                    <input type="hidden" name="benzersizid" value="<?=$benzersizid?>">
                                                                    <td colspan="11">
                                                                        <?php
                                                                        $resimsira_resimler="";$resimsira_resimidler="";
                                                                        $resimsira_s="
                                                                        Select resim.resimid,resim from sayfalisteresim inner join resim on resim.resimid=sayfalisteresim.resimid where sayfaid='".$sayfaid."' Group by resim.resimid order by sayfalisteresimid ASC
                                                                    ";
                                                                        if($data->query($resimsira_s))
                                                                        {
                                                                            $resimsira_v=$data->query($resimsira_s);
                                                                            if($resimsira_v->num_rows>0)
                                                                            {
                                                                                while($resimsira_t=$resimsira_v->fetch_assoc())
                                                                                {
                                                                                    $resimsira_resimid=$resimsira_t["resimid"];
                                                                                    $resimsira_resim=$resimsira_t["resim"];
                                                                                    $resimsira_resimler=$resimsira_resimler.','.$resimsira_resim;
                                                                                    $resimsira_resimidler=$resimsira_resimidler.','.$resimsira_resimid;
                                                                                }
                                                                                $resimsira_resimler=trim($resimsira_resimler,",");
                                                                                $resimsira_resimidler=trim($resimsira_resimidler,",");
                                                                                ?>
                                                                                <div class="card row" style="margin-left: 5px; margin-right: 5px">
                                                                                    <div class="card-body">
                                                                                        <div class="col-sm-4" style="display:none">
                                                                                            <div class="form-group">
                                                                                                <input
                                                                                                        type="text"
                                                                                                        name="urunbaslik"
                                                                                                        id="resimler<?=$sayfaid?>"
                                                                                                        class="form-control"
                                                                                                        value="<?=$resimsira_resimidler?>"
                                                                                                        data-rule-minlength="5"
                                                                                                        maxlength="65"
                                                                                                        aria-invalid="false"
                                                                                                        required aria-required="true">
                                                                                                <label for="urunbaslik<?=$sayfaid?>"
                                                                                                       style="margin-top:-10px">Resim Sıra</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-8">
                                                                                            <div class="form-group">
                                                                                                <ul class="rsirala" id="ul<?=$sayfaid?>">
                                                                                                    <?php
                                                                                                    $resimsira_resim_ayikla=explode(",",$resimsira_resimler);
                                                                                                    $resimsira_id_ayikla=explode(",",$resimsira_resimidler);
                                                                                                    foreach ($resimsira_resim_ayikla as $x => $resimsira_tekresim)
                                                                                                    {
                                                                                                        echo '<a href="/m/r/urun/'.$resimsira_tekresim.'" target="_blank"><img src="/m/r/?resim=urun/'.$resimsira_tekresim.'&g=100&y=100" style="border:solid 1px #ccc" data-id="'.$resimsira_id_ayikla[$x].'"></a>';
                                                                                                    }
                                                                                                    ?></ul>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-4">
                                                                                            <div class="form-group">
                                                                                                <button name="rdegistir" type="button" data-id="<?=$sayfaid?>" class="resimsira btn btn-primary btn-sm">Resim Sıra Değiştir</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                        }else{hatalogisle("Ürün Liste Resim",$data->error);}?>
                                                                        <?php
                                                                        $orjinallink="";
                                                                        if(!BosMu($orjinallink)){$orjinallink=teksatir("select link from urunaktar where model='".$model."'","link");
                                                                        echo '<div class="form-group col-sm-12" style="z-index:5"><a href="'.$orjinallink.'" target="_blank">Orjinal Sayfayı Gör</a></div>';}?>
                                                                        <div class="form-group">
                                                                            <a
                                                                                    data-id="trgizli<?=$sayfaid?>"
                                                                                    data-ustid="tr<?=$sayfaid?>"
                                                                                    class="urunsatiralt btn ink-reaction btn-raised btn-xs style-danger"
                                                                            >KAPAT (x)
                                                                            </a>
                                                                        </div>
                                                                        <!-- SEO GÜNCELLE -->
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
                                                                                                class="btn btn-primary btn-default" disabled>SEO GÜNCELLE</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- /SEO GÜNCELLE -->
                                                                    </td>
                                                                </form>
                                                            </tr>
                                                            <?php
                                                            $varyant_s="
                                                                SELECT
                                                                    urunozellikid,urunstok,urunsatisfiyat,urunindirimsizfiyat,urunbayifiyat,urunalisfiyat,
                                                                    urunbedenad,urunrenkad,urunmalzemead,urunstokkodu,urunindirimorani,
                                                                    urunozellikleri.urunbedenid,
                                                                    urunozellikleri.urunrenkid,
                                                                    urunozellikleri.urunmalzemeid,
                                                                    urunmodel,sayfaid
                                                                FROM 
                                                                    urunozellikleri
                                                                        left join urunbeden on urunbeden.urunbedenid=urunozellikleri.urunbedenid
                                                                        left join urunrenk on urunrenk.urunrenkid=urunozellikleri.urunrenkid
                                                                        left join urunmalzeme on urunmalzeme.urunmalzemeid=urunozellikleri.urunmalzemeid
                                                                        
                                                                where
                                                                    sayfaid='".$sayfaid."'
                                                                Order By urunstokkodu ASC
                                                            ";
                                                            if($data->query($varyant_s))
                                                            {
                                                                ?>
                                                                <tr id="2trgizli<?=$sayfaid?>" style="display:none" class="style-accent-bright">
                                                                    <td colspan="11">
                                                                        <form
                                                                            class="form form-validation form-validate"
                                                                            action="/_y/s/f/urunguncelle.php"
                                                                            method="post"
                                                                            target="_islem"
                                                                            novalidate="novalidate">
                                                                            <input type="hidden" name="fiyatstok" value="1">
                                                                            <input type="hidden" name="sayfaid" value="<?=$sayfaid?>">
                                                                            <?php
                                                                            $varyant_v=$data->query($varyant_s);unset($varyant_s);
                                                                            if($varyant_v->num_rows>0)
                                                                            {
                                                                                while ($varyant_t=$varyant_v->fetch_assoc())
                                                                                {
                                                                                    $urunozellikid=$varyant_t["urunozellikid"];
                                                                                    $urunstok=$varyant_t["urunstok"];
                                                                                    $urunsatisfiyat=$varyant_t["urunsatisfiyat"];
                                                                                    $urunindirimsizfiyat=$varyant_t["urunindirimsizfiyat"];
                                                                                    $urunbayifiyat=$varyant_t["urunbayifiyat"];
                                                                                    $urunalisfiyat=$varyant_t["urunalisfiyat"];
                                                                                    $urunbedenid=$varyant_t["urunbedenid"];
                                                                                    $urunrenkid=$varyant_t["urunrenkid"];
                                                                                    $urunmalzemeid=$varyant_t["urunmalzemeid"];
                                                                                    $urunbedenad=$varyant_t["urunbedenad"];
                                                                                    $urunrenkad=$varyant_t["urunrenkad"];
                                                                                    $urunmalzemead=$varyant_t["urunmalzemead"];
                                                                                    $urunstokkodu=$varyant_t["urunstokkodu"];
                                                                                    $urunindirimorani=$varyant_t["urunindirimorani"];
                                                                                    $urunmodel=$varyant_t["urunmodel"];
                                                                                    $urunsayfaid=$varyant_t["sayfaid"];
                                                                                    ?>
                                                                                    <div class="card row" style="margin-left: 5px; margin-right: 5px">
                                                                                        <div class="card-head">
                                                                                            <div class="col-sm-12" style="padding-bottom: 25px">
                                                                                                <div class="form-group col-sm-4">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunmodel"
                                                                                                            id="urunmodel<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            value="<?=$urunmodel?>">
                                                                                                </div>
                                                                                                <div class="form-group col-sm-4">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_sayfaid"
                                                                                                            id="sayfaid<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            value="<?=$urunsayfaid?>">
                                                                                                </div>
                                                                                                <div class="form-group col-sm-4">
                                                                                                    <button
                                                                                                            type="button"
                                                                                                            name="<?=$urunozellikid?>_button_v"
                                                                                                            id="button_v<?=$urunozellikid?>"
                                                                                                            data-id="<?=$urunozellikid?>"
                                                                                                            class="btn btn-primary btn-default modelbuton"">Değiştir</button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-3 style-primary-bright">
                                                                                                <div class="form-group">
                                                                                                    <input type="hidden" name="urunbedenid<?=$urunozellikid?>" value="<?=$urunbedenid?>>">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunbedenad"
                                                                                                            id="urunbedenad<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            value="<?=$urunbedenad?>"
                                                                                                            readonly>
                                                                                                    <label for="urunbedenad<?=$urunozellikid?>"
                                                                                                           style="margin-top:-10px">Ölçü</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-3 style-primary-bright">
                                                                                                <div class="form-group">
                                                                                                    <input type="hidden" name="urunrenkid<?=$urunozellikid?>" value="<?=$urunrenkid?>>">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunrenkad"
                                                                                                            id="urunrenkad<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            value="<?=$urunrenkad?>"
                                                                                                            readonly>
                                                                                                    <label for="urunrenkad<?=$urunozellikid?>"
                                                                                                           style="margin-top:-10px">Renk</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-3 style-primary-bright">
                                                                                                <div class="form-group">
                                                                                                    <input type="hidden" name="urunmalzemeid<?=$urunozellikid?>" value="<?=$urunmalzemeid?>>">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunmalzemead"
                                                                                                            id="urunmalzemead<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            value="<?=$urunmalzemead?>"
                                                                                                            readonly>
                                                                                                    <label for="urunmalzemead<?=$urunozellikid?>"
                                                                                                           style="margin-top:-10px">Malzeme</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-3 style-primary-bright">
                                                                                                <div class="form-group">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunstokkodu"
                                                                                                            id="urunstokkodu<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            value="<?=$urunstokkodu?>"
                                                                                                            readonly>
                                                                                                    <label for="urunstokkodu<?=$urunozellikid?>"
                                                                                                           style="margin-top:-10px">Stokkodu</label>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" style="margin-top:20px">
                                                                                            <div class="col-sm-2">
                                                                                                <div class="form-group">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunalisfiyat"
                                                                                                            id="urunalisfiyat<?=$sayfaid?>"
                                                                                                            class="form-control"
                                                                                                            placeholder="49.99"
                                                                                                            value="<?=$urunalisfiyat?>"
                                                                                                            data-rule-number="true">
                                                                                                    <label for="urunalisfiyat<?=$urunozellikid?>"
                                                                                                           style="margin-top:-20px">Ürün Alış Fiyat (Sadece siz görebilirsiniz)</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-2">
                                                                                                <div class="form-group">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunsatisfiyat"
                                                                                                            id="urunsatisfiyat<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            placeholder="99.99"
                                                                                                            value="<?=$urunsatisfiyat?>"
                                                                                                            data-rule-number="true"
                                                                                                            required=""
                                                                                                            aria-required="true"
                                                                                                            aria-invalid="false">
                                                                                                    <label for="urunfiyat<?=$urunozellikid?>"
                                                                                                           style="margin-top:-20px">Ürün Satış Fiyat</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-2">
                                                                                                <div class="form-group">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunindirimsizfiyat"
                                                                                                            id="urunindirimlifiyat<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            placeholder="79.99"
                                                                                                            value="<?=$urunindirimsizfiyat?>"
                                                                                                            data-rule-number="true"
                                                                                                            required
                                                                                                            aria-required="true"
                                                                                                            aria-invalid="false">
                                                                                                    <label for="urunindirimsizfiyat<?=$urunozellikid?>"
                                                                                                           style="margin-top:-20px">Ürün İnd.SİZ Fiyat</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-2">
                                                                                                <div class="form-group">
                                                                                                    <input type="text"
                                                                                                           name="<?=$urunozellikid?>_urunbayifiyat"
                                                                                                           id="urunbayifiyat<?=$urunozellikid?>"
                                                                                                           class="form-control"
                                                                                                           placeholder="79.99"
                                                                                                           value="<?=$urunbayifiyat?>"
                                                                                                           data-rule-number="true"
                                                                                                           required
                                                                                                           aria-required="true"
                                                                                                           aria-invalid="false" >
                                                                                                    <label for="urunbayifiyat<?=$urunozellikid?>"
                                                                                                           style="margin-top:-20px">Ürün Bayi Fiyat</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-2">
                                                                                                <div class="form-group">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunstok"
                                                                                                            id="urunstok<?=$sayfaid?>"
                                                                                                            class="form-control"
                                                                                                            placeholder="Ürün Stok 20"
                                                                                                            value="<?=$urunstok?>"
                                                                                                            data-rule-digits="true">
                                                                                                    <label for="urunstok<?=$urunozellikid?>"
                                                                                                           style="margin-top:-20px">Ürün Stok</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-2">
                                                                                                <div class="form-group">
                                                                                                    <input
                                                                                                            type="text"
                                                                                                            name="<?=$urunozellikid?>_urunindirimorani"
                                                                                                            id="urunindirimorani<?=$urunozellikid?>"
                                                                                                            class="form-control"
                                                                                                            placeholder="0.15"
                                                                                                            value="<?=$urunindirimorani?>"
                                                                                                            data-rule-number="true"
                                                                                                            required
                                                                                                            aria-required="true"
                                                                                                            aria-invalid="false">
                                                                                                    <label for="urunindirimorani<?=$urunozellikid?>"
                                                                                                           style="margin-top:-20px">Ürün İndirim %10 için 0.10</label>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <?php
                                                                                }?>
                                                                                <div class="card-actionbar">
                                                                                    <div class="card-actionbar-row">
                                                                                        <button
                                                                                            type="submit"
                                                                                            class="btn btn-primary btn-default" disabled>FİYAT/STOK GÜNCELLE</button>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </form></td>
                                                                </tr>
                                                            <?php }else{hatalogisle("urunliste varyant",$data->error);}?>
                                                            <?php
                                                            if($i>=($urunler_basla+$urunler_bitir))break;
                                                        }
													}unset($urunler_t);
												}
												unset($urunler_d,$urunler_v,$sayfaid,$sayfaad);
												?>
												<tr><td colspan="11"><?php 
													if($qsimdisayfa==0)$qsimdisayfa=1;
													sayfala("urunliste.php?sirala=".q("sirala")."&resim=".S(q("resim")),$urunlertoplamsayfa,$qsimdisayfa);
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
        <div class="modal fade" id="yukleniyor" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn-popup-yukleniyor-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="simpleModalLabel">Lütfen Bekleyiniz</h4>
                    </div>
                    <div class="modal-body">
                        <p>Ürünler Yükleniyor</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="urunkopyalapencere" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn-popup-topluurunsil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="simpleModalLabel">Ürün kopyala</h4>
                    </div>
                    <form name="urunkopyalaform" id="urunkopyalaform" method="get" action="/_y/s/s/urunler/urunekle.php?">
                        <input type="hidden" name="sayfaid" id="urunkopyalaid" value="0">
                        <input type="hidden" name="kopyala" value="1">
                        <div class="modal-body">
                            <p>Seçilen ürünü kopyalamak istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary" id="urunkopyalabuton">KOPYALA</button>
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
                $(document).on("click",".modelbuton",function(){
                    $degisid = $(this).data("id");//alert($degisid);
                    $degissayfaid=$("#sayfaid"+$degisid).val();
                    $degismodel=$("#urunmodel"+$degisid).val();
                    $.ajax({
                        type: 'GET',
                        url: "modeldegis.php?sayfaid=" + $degissayfaid + "&model=" + $degismodel +"&urunozellikid=" + $degisid,
                        dataType: "html",
                        success: function (data)
                        {
                            if ($.trim(data))
                            {
                                alert(data);
                            } else {
                                alert("");
                            }
                        }
                    });
                });
                $(document).on("click", 'a#urunsil', function () {
                    $silid = $(this).data("id");//alert($silid);
                });
                $(document).on("click", '#silbutton', function ()
                {
                    $('#_islem').attr('src', "/_y/s/f/sil.php?sil=urun&id=" + $silid);
                });
                $(document).on("click", 'a#urunkopyala', function () {
                    $urunkopyalaid=0;
                    $('input[name="sayfalar[]"]:checked').each(function () {
                        if ($urunkopyalaid == "") {
                            $urunkopyalaid = this.value;
                            $kopyahata=0;
                        } else {
                            alert("Sadece tek ürün kopyalayabilirsiniz");
                            //$("#urunkopyalapencere").modal("hide");
                            $kopyahata=1;
                        }
                    });
                    if( $urunkopyalaid==0){$kopyahata=1; alert("Kopyalamak için bir ürün seçmelisiniz!");}
                    if($kopyahata==0){ $("#urunkopyalapencere").modal("show"); $("#urunkopyalaid").val($urunkopyalaid);}
                });
                $(document).on("click", ".urunsatir", function () {
                    $satir = $(this).data("id");
                    $ustsatir = $(this).data("ustid");
                    //$("#tbody"+$ustsatir).show();
                    $("#" + $satir).show();
                    $("#2" + $satir).show();
                    $("#" + $ustsatir).addClass("style-accent-light");
                    //$("#"+$ustsatir).css("background-color","#ddd");
                    location.replace("#" + $ustsatir);
                });
                $(document).on("click", ".urunsatiralt", function ()
                {
                    $satir = $(this).data("id");
                    $("#" + $satir).hide();
                    $("#2" + $satir).hide();
                    $ustsatir = $(this).data("ustid");
                    //$("#"+$ustsatir).css("background-color","#fff");
                });
                $(document).on("keyup", "#q", function ()
                {
                    if ($("#q").val().length >= 3)
                    {
                        //$('#_islem').attr('src', "/_y/s/f/urunbul.php?q="+$("#q").val());
                        $q = $("#q").val();
                        $deger = $("#kategoriid").val();

                        $("#yukleniyor").modal("show");
                        $.ajax({
                            type: 'GET',
                            url: "urunbul.php?q=" + $q + "&kategori=" + $deger,
                            dataType: "html",
                            success: function (data) {

                                if ($.trim(data)) {
                                    $("tbody").html(data);
                                    $(".rsirala").sortable();
                                    $("#yukleniyor").modal("hide");
                                } else {
                                    $("tbody").html("");
                                    $("#yukleniyor").modal("hide");
                                }
                            }
                        });
                    }
                });

                $(document).on('click', '#kategoridivler select', function ()
                {
                    $katman = $(this).data("id");
                    $toplamkatman = $("div[id*='kategoridiv']").length;

                    $deger = $(this).find('option:selected').val();
                    $("#kategoriid").val($deger);
                    $("#yukleniyor").modal("show");
                    $.ajax({
                        type: 'GET',
                        url: "urunlistekategori.php?kategori=" + $deger,
                        dataType: "html",
                        success: function (data)
                        {
                            if ($.trim(data)) {
                                $("tbody").html(data);
                                $(".rsirala").sortable();
                                $("#yukleniyor").modal("hide");
                            } else {
                                $("tbody").html("");
                                $("#yukleniyor").modal("hide");
                            }
                        }
                    });
                    $.ajax({
                        type: 'GET',
                        url: "kategorigetir.php?id=" + $deger,
                        dataType: "html",
                        success: function (data) {
                            if ($.trim(data)) {
                                $katman = $katman + 1;
                                for ($i = $katman; $i < $toplamkatman; $i++) {
                                    if ($("#kategoridiv" + $i).length) {
                                        $("#kategoridiv" + $i).remove();
                                    }
                                }

                                $("#kategoridivler").append('<div class="col-sm-6 form-group floating-label" id="kategoridiv' + $katman + '">Yükleniyor</div>');
                                $("#kategoridiv" + $katman).html(data);
                                $("#kategoridiv" + $katman + " select").attr('data-id', $katman);
                            } else {
                                $i = $katman + 1;
                                $("#kategoridiv" + $i).remove();
                            }
                        }
                    });
                });
                $(document).on("click", "#kategoridegistirbutton", function () {
                    $("#degistirkategorisayfalar").val("");
                    $degistirkategorisayfalar = "";
                    $('input[name="sayfalar[]"]:checked').each(function () {
                        if ($degistirkategorisayfalar == "") {
                            $degistirkategorisayfalar = this.value;
                        } else {
                            $degistirkategorisayfalar = $degistirkategorisayfalar + "," + this.value;
                        }
                    });
                    $("#degistirkategorisayfalar").val($degistirkategorisayfalar);
                    if ($("#degistirkategoriid").val() == 0) {
                        alert("Bir Kategori Seçmelisiniz");
                    } else if ($("#degistirkategorisayfalar").val() == "") {
                        alert("En Az Bir Ürün Seçmelisiniz");
                    } else {
                        $('#toplukategoridegistirform')[0].submit();
                    }
                });
                $(document).on("click", "#topluurunsilbutton", function () {
                    $("#topluurunsilsayfalar").val("");
                    $topluurunsilsayfalar = "";
                    $('input[name="sayfalar[]"]:checked').each(function () {
                        if ($topluurunsilsayfalar == "") {
                            $topluurunsilsayfalar = this.value;
                        } else {
                            $topluurunsilsayfalar = $topluurunsilsayfalar + "," + this.value;
                        }
                    });
                    $("#topluurunsilsayfalar").val($topluurunsilsayfalar);
                    if ($("#topluurunsilsayfalar").val() == "") {
                        alert("En Az Bir Ürün Seçmelisiniz");
                    } else {
                        $('#topluurunsilform')[0].submit();
                    }
                });
                $(document).on('click', '#degistirkategoridivler select', function () {
                    $katman = $(this).data("id");
                    $toplamkatman = $("div[id*='degistirkategoridiv']").length;

                    $deger = $(this).find('option:selected').val();
                    $("#degistirkategoriid").val($deger);
                    $.ajax({
                        type: 'GET',
                        url: "kategorigetir.php?id=" + $deger,
                        dataType: "html",
                        success: function (data) {
                            if ($.trim(data)) {
                                $katman = $katman + 1;
                                for ($i = $katman; $i < $toplamkatman; $i++) {
                                    if ($("#degistirkategoridiv" + $i).length) {
                                        $("#degistirkategoridiv" + $i).remove();
                                    }
                                }

                                $("#degistirkategoridivler").append('<div class="col-sm-6 form-group floating-label" id="degistirkategoridiv' + $katman + '">Yükleniyor</div>');
                                $("#degistirkategoridiv" + $katman).html(data);
                                $("#degistirkategoridiv" + $katman + " select").attr('data-id', $katman);
                            } else {
                                $i = $katman + 1;
                                $("#degistirkategoridiv" + $i).remove();
                            }
                        }
                    });
                });
                $(".rsirala").sortable();
                $(document).on("click",'.resimsira',function()
                {
                    $sayfaid = $(this).data('id');
                    var imageids_arr = [];
                    // get image ids order
                    $('#ul'+$sayfaid+' img').each(function()
                    {
                        var id = $(this).data('id');
                        imageids_arr.push(id);
                    });
                    //alert(imageids_arr);
                    $.ajax({
                        type: 'GET',
                        url:"urunresimsira.php?sayfaid="+$sayfaid+"&resimler="+imageids_arr,
                        dataType: "html",
                        success: function(data)
                        {
                            if($.trim(data))
                            {
                                if(data=="ok")alert("resim sıralaması değiştirildi");else alert(data);
                            }
                            else
                            {
                                alert("resim sıralaması değiştirilemedi");//$("tbody").html("");
                            }
                        }
                    });
                    // AJAX request
                    /*
                    $.ajax({
                        url: 'ajaxfile.php',
                        type: 'post',
                        data: {imageids: imageids_arr},
                        success: function(response){

                            alert('Save successfully.');
                        }
                    });
                    */
                });
            });

			$("#silinenurunlistephp").addClass("active");
		</script>
	</body>
</html>
