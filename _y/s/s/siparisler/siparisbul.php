<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");

$formhata = 0;
$formhataaciklama = "";
$formtablo = "uyesiparis";

//düzenle
$sayfabaslik = "Siparişleri Düzenle";
$formbaslik = "Sipariş Liste";
$siparisurunbedenler = "";
$sipariskargotakip = "";
$sipariskargodurum = "";
$siparisek = "";
$siparis_sql_ek="(
        uyesiparis.siparisbenzersizid           like '%".q("q")."%' or 
        uye.uyeadsoyad                          like '%".q("q")."%' or 
        uyesiparis.siparisteslimatad            like '%".q("q")."%' or 
        uyesiparis.siparisteslimatsoyad         like '%".q("q")."%' or
        uyesiparis.siparisfaturaunvan           like '%".q("q")."%' or 
        uyesiparis.siparisteslimateposta        like '%".q("q")."%' or 
        uyesiparis.siparistariholustur          like '%".q("q")."%'
        ) 
        and ";

if(q("tip")==99)
{
    //kredi kartı
    $siparis_sql_ek=" siparisodemedurum='1' and siparisdurum='2' and siparissil='0' ";
}
elseif(q("tip")==98)
{
    //banka havalesi
    $siparis_sql_ek="siparisodemeyontemi='bh' and siparisodemedurum='2' and siparissil='0' ";
}
elseif(q("tip")==97)
{
    $siparis_sql_ek="siparisodemedurum='3' and siparisdurum='2' and siparissil='0'";
    //kapıda ödeme

}
elseif(q("tip")==96)
{
    $siparis_sql_ek="siparisodemedurum='0' and siparisodemeyontemi='kk' and siparisdurum!='11' and siparissil='0'";
    //kapıda ödeme
}

if(q("tip")!=99 &&q("tip")!=98&&q("tip")!=97&&q("tip")!=96&&q("tip")!=100)
{
    $siparis_sql_ek=$siparis_sql_ek." siparisdurum='".S(q("tip"))."' and siparissil='0' ";
}
if(S(q("tip"))==100) {
    $siparis_sql_ek = $siparis_sql_ek." siparisid!='0'";
}


if(!BosMu(f("gsiparisid")))
{
	$siparisid=f("gsiparisid");
	$siparisdurumid=f("siparisdurumid");
	$kargoid=f("kargoid");
	$sipariskargoserino=f("sipariskargoserino");
	$sipariskargotakip=f("sipariskargotakip");
	$siparisnotyonetici=f("siparisnotyonetici");
	$siparisnotalici=f("siparisnotalici");
	guncelle("siparisdurumid,kargoid,sipariskargoserino,sipariskargotakip,siparisnotyonetici,siparisnotalici",$siparisdurumid."|*_".$kargoid."|*_".$sipariskargoserino."|*_".$sipariskargotakip."|*_".$siparisnotyonetici."|*_".$siparisnotalici,"uyesiparis","siparisbenzersizid='".$siparisid."'");
}
$firmabilgiler_s="SELECT ayarfirmaad,ayarfirmatelefon,ayarfirmavergidairesi,ayarfirmavergino FROM ayarfirma LIMIT 1";
if($data->query($firmabilgiler_s))
{
    $firmabilgiler_v=$data->query($firmabilgiler_s);unset($firmabilgiler_s);
    if($firmabilgiler_v->num_rows>0)
    {
        while($firmabilgiler_t=$firmabilgiler_v->fetch_assoc())
        {
            $ayarfirmaad=$firmabilgiler_t["ayarfirmaad"];
            $ayarfirmatelefon=$firmabilgiler_t["ayarfirmatelefon"];
            $ayarfirmavergidairesi=$firmabilgiler_t["ayarfirmavergidairesi"];
            $ayarfirmavergino=$firmabilgiler_t["ayarfirmavergino"];
        }unset($firmabilgiler_t);
    }else{hatalogisle("hata",$data->error);}
    unset($firmabilgiler_v);
}
$sayfalar_bitir=50;
$qsimdisayfa=S(q("sayfa"));
if($qsimdisayfa<1)
{
    $sayfalar_basla=0;
}
else
{
    $sayfalar_basla=($qsimdisayfa-1)*$sayfalar_bitir;
}
$siparisdurum_s="
	Select 
		uyesiparis.*,
		urunparabirim.parabirimsimge,uyesiparisdurum.siparisdurumbaslik,uyesiparisdurum.siparisdurumid as siparisDurumIdForSiparis, kargoCode,
		uye.uyeid,uyeadsoyad,uyeeposta,uyetelefon,sipariskargofiyat,siparisKargoSevkiyatYapildi
	from 
		uyesiparis 
			inner join urunparabirim on urunparabirim.parabirimkod=uyesiparis.siparisodemeparabirim
			inner join uyesiparisdurum on uyesiparisdurum.siparisdurumid=uyesiparis.siparisdurum
			inner join uye on uye.uyeid=uyesiparis.uyeid
	Where 
		$siparis_sql_ek
	Order By siparistarihguncelle Desc";
$siparisler=coksatir_arr($siparisdurum_s);?>
<?php
$tumtutarlar =0;
$tumadetler=0;
$tumadet = 0;
$tumtutar = 0;
$si=0;$siparisdurum_d=0;
if(!BosMu($siparisler))
{
    $siparisdurum_d=1;
    foreach($siparisler as $siparisdurum_t)
    {
        $si++;
        if($si>=$sayfalar_basla)
        {
            $geneltoplam=0;$geneltoplamliste=0;
            $siparisid = $siparisdurum_t["siparisid"];
            $siparistariholustur = $siparisdurum_t["siparistariholustur"];
            $siparistarihguncelle = $siparisdurum_t["siparistarihguncelle"];
            $siparisbenzersizid = $siparisdurum_t["siparisbenzersizid"];

            $uyeadsoyad = $siparisdurum_t["uyeadsoyad"];
            if(!BosMu($siparisdurum_t["uyeeposta"])){$uyeeposta = coz($siparisdurum_t["uyeeposta"], $anahtarkod);} else {$uyeeposta="";}
            if(!BosMu($siparisdurum_t["uyetelefon"])){$uyetelefon=coz($siparisdurum_t["uyetelefon"],$anahtarkod);} else {$uyetelefon="";}
            $siparisodemeparabirim = $siparisdurum_t["siparisodemeparabirim"];
            //if($siparisodemeparabirim=="TRY")$siparisodemeparabirim=TL;
            $siparisodemetaksit = $siparisdurum_t["siparisodemetaksit"];
            $siparistoplamtutar = $siparisdurum_t["siparistoplamtutar"];

            $siparisteslimatad = $siparisdurum_t["siparisteslimatad"];
            $siparisteslimatsoyad = $siparisdurum_t["siparisteslimatsoyad"];
            $siparisteslimatgsm = $siparisdurum_t["siparisteslimatgsm"];
            $siparisteslimateposta = $siparisdurum_t["siparisteslimateposta"];
            $siparisteslimattcno = $siparisdurum_t["siparisteslimattcno"];
            $siparisteslimatadresacik = $siparisdurum_t["siparisteslimatadresacik"];
            $siparisip = $siparisdurum_t["siparisip"];
            $siparisdurumbaslik = $siparisdurum_t["siparisdurumbaslik"];
            $parabirimsimge = $siparisdurum_t["parabirimsimge"];

            $siparisteslimatmahallead = teksatir("select NeighborhoodName from yermahalle Where NeighborhoodID='" . $siparisdurum_t["siparisteslimatadresmahalle"] . "'", "NeighborhoodName");
            $siparisteslimatsemtad = teksatir("select AreaName from yersemt Where AreaID='" . $siparisdurum_t["siparisteslimatadressemt"] . "'", "AreaName");
            $siparisteslimatilcead = teksatir("select CountyName from yerilce Where CountyID='" . $siparisdurum_t["siparisteslimatadresilce"] . "'", "CountyName");
            $siparisteslimatsehirad = teksatir("select CityName from yersehir Where CityID='" . $siparisdurum_t["siparisteslimatadressehir"] . "'", "CityName");
            $siparisteslimatulkead = teksatir("select CountryName from yerulke Where CountryID='" . $siparisdurum_t["siparisteslimatadresulke"] . "'", "CountryName");

            $siparisfaturaad = $siparisdurum_t["siparisfaturaad"];
            $siparisfaturasoyad = $siparisdurum_t["siparisfaturasoyad"];
            $siparisfaturagsm = $siparisdurum_t["siparisfaturagsm"];
            $siparisfaturamahallead = teksatir("select NeighborhoodName from yermahalle Where NeighborhoodID='" . $siparisdurum_t["siparisfaturaadresmahalle"] . "'", "NeighborhoodName");
            $siparisfaturasemtad = teksatir("select AreaName from yersemt Where AreaID='" . $siparisdurum_t["siparisfaturaadressemt"] . "'", "AreaName");
            $siparisfaturailcead = teksatir("select CountyName from yerilce Where CountyID='" . $siparisdurum_t["siparisfaturaadresilce"] . "'", "CountyName");

            $siparisfaturasehirad = teksatir("select CityName from yersehir Where CityID='" . $siparisdurum_t["siparisfaturaadressehir"] . "'", "CityName");
            $siparisfaturaulkead = teksatir("select CountryName from yerulke Where CountryID='" . $siparisdurum_t["siparisfaturaadresulke"] . "'", "CountryName");
            $siparisfaturaadresacik = $siparisdurum_t["siparisfaturaadresacik"];

            $siparisfaturaunvan = $siparisdurum_t["siparisfaturaunvan"];
            $siparisfaturavergidairesi = $siparisdurum_t["siparisfaturavergidairesi"];
            $siparisfaturavergino = $siparisdurum_t["siparisfaturavergino"];

            $siparisurunidler = $siparisdurum_t["siparisurunidler"];
            $siparisurunadlar = $siparisdurum_t["siparisurunadlar"];
            $siparisurunstokkodlar = $siparisdurum_t["siparisurunstokkodlar"];

            $siparisurunkategoriler = $siparisdurum_t["siparisurunkategoriler"];
            $siparisurunfiyatlar = $siparisdurum_t["siparisurunfiyatlar"];
            $siparisurunadetler = $siparisdurum_t["siparisurunadetler"];
            $siparisurunbedenler = $siparisdurum_t["siparisurunbedenler"];
            $siparisurunrenkler = $siparisdurum_t["siparisurunrenkler"];
            $siparisurunmalzemeler = $siparisdurum_t["siparisurunmalzemeler"];
            $siparisodemeyontemi = $siparisdurum_t["siparisodemeyontemi"];
            $siparisdurum = $siparisdurum_t["siparisdurum"];
            $siparisodemedurum = $siparisdurum_t["siparisodemedurum"];
            $sipariskargoserino = $siparisdurum_t["sipariskargoserino"];
            $siparisKargoFirmaCode = $siparisdurum_t["kargoCode"];
            $siparisKargoSevkiyatYapildi = $siparisdurum_t["siparisKargoSevkiyatYapildi"];
            $sipariskargotakip = $siparisdurum_t["sipariskargotakip"];
            $sipariskargodurum = $siparisdurum_t["sipariskargodurum"];
            $sipariskargofiyat = $siparisdurum_t["sipariskargofiyat"];
            $tempBarcodeNumber = $siparisdurum_t["tempBarcodeNumber"];
            $siparisKargoBarcode = $siparisdurum_t["siparisKargoBarcode"];
            $siparisnotalici = $siparisdurum_t["siparisnotalici"];
            if (!BosMu($siparisnotalici)) $siparisnotyonetici = str_replace('"', '', $siparisnotalici);
            $siparisnotyonetici = $siparisdurum_t["siparisnotyonetici"];
            if (!BosMu($siparisnotyonetici)) $siparisnotyonetici = str_replace('"', '', $siparisnotyonetici);

            if(!BosMu($siparisurunstokkodlar)) $siparisurunstokkodlarr= explode("||", $siparisurunstokkodlar);
            $siparisurunidlerr = explode(",", $siparisurunidler);
            $siparisurunadlarr = explode("||", $siparisurunadlar);
            $siparisurunkategorilerr = explode("||", $siparisurunkategoriler);
            $siparisurunfiyatlarr = explode("||", $siparisurunfiyatlar);
            $siparisurunadetlerr = explode("||", $siparisurunadetler);

            $siparisurunrenklerr = explode("||", $siparisurunrenkler);
            $siparisurunmalzemelerr = explode("||", $siparisurunmalzemeler);
            $siparisurunbedenlerr = explode("||", $siparisurunbedenler);
            $siparisuruntoplam = count($siparisurunidlerr);

            $siparisDurumIdForSiparis = $siparisdurum_t["siparisDurumIdForSiparis"];
            $siparisdekont=$siparisdurum_t["siparisdekont"];
            $header_style="text-success";
            $siparisdurumid=$siparisdurum_t["siparisdurum"];
            if($siparisdurumid==1)
            {
                $header_style="text-accent";
            }
            elseif($siparisdurumid==2)
            {
                $header_style="text-primary-light";
            }
            elseif($siparisdurumid==3)
            {
                $header_style="text-accent-light";
            }
            elseif($siparisdurumid==5)
            {
                $header_style="text-gray-bright";
            }
            elseif($siparisdurumid==6)
            {
                $header_style="text-danger";
            }
            elseif($siparisdurumid==7)
            {
                $header_style="text-gray-bright";
            }
            elseif($siparisdurumid==8)
            {
                $header_style="text-accent-bright";
            }
            elseif($siparisdurumid==9)
            {
                $header_style="text-warning";
            }
            elseif($siparisdurumid==0)
            {
                $header_style="text-primary-dark";
            }
            elseif($siparisdurumid==10)
            {
                $header_style="text-gray-light";
            }
            elseif($siparisdurumid==11)
            {
                $header_style="text-danger";
            }

            if($siparisodemeyontemi=="kk")
            {
                $odemeyontemisimge="credit-card";
                $odemeyontemiyazi="Kredi Kartı";
            }
            else{
                $odemeyontemisimge="bank";
                $odemeyontemiyazi="Banka Havalesi";
            }
            ?>
            <div id="div<?=$siparisid?>" class="card panel row">
                <div class="card-head collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#accordion-<?=$siparisid?>" aria-expanded="false">
                    <header style="font-size: 14px">
                        <datetime class="text-bold"><?=Tarih($siparistarihguncelle,1)?></datetime> |
                        <span class="text-medium"><?=$siparisbenzersizid?></span> |
                        <span class="text-sm"><?=BK($siparisfaturaunvan)?></span>
                    </header>
                    <div class="tools">
                        <span class="<?=$header_style?>" style="position:absolute;right:45px"><?=$siparisdurumbaslik?></span>
                        <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                    </div>
                </div>
                <div id="accordion-<?=$siparisid?>" class="collapse" aria-expanded="false">
                    <div class="row no-margin">
                        <!-- Ürün -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-head">
                                    <header>Sipariş bilgileri</header>
                                    <div class="tools">
                                        <i class="fa fa-<?=$odemeyontemisimge?>" title="Ödeme Yöntemi: <?=$siparisodemeyontemi?>"><em style="margin-left:10px"><?=$odemeyontemiyazi?></em></i>
                                        <div class="btn-group">
                                            <a class="btn btn-icon-toggle btn-collapse"><i class="fa fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                </div><!--end .card-head -->
                                <div class="card-body" style="min-height:338px">
                                    <ul class="list divider-full-bleed">
                                        <?php
                                        foreach($siparisurunidlerr as $i => $siparisurunid)
                                        {
                                            $urunresmi="";
                                            $urunresim="
                                                                        SELECT 
                                                                            resim.resim 
                                                                        FROM 
                                                                            resim 
                                                                        INNER JOIN 
                                                                            sayfalisteresim on sayfalisteresim.resimid=resim.resimid 
                                                                        Where 
                                                                              sayfaid='".$siparisurunid."'";
                                            $urunresim=coksatir($urunresim);
                                            if(!BosMu($urunresim))
                                            {
                                                $urunresmi='<img src="/m/r/?resim=urun/'.$urunresim["resim"].'&g=70">';
                                            }
                                            $siparissepetdurum="";
                                            $siparissepet_sql="
                                                                        SELECT
                                                                            *
                                                                        FROM
                                                                            uyesepet
                                                                        WHERE
                                                                            sepetsil=1 and
                                                                            siparisbenzersiz='".$siparisbenzersizid."' and
                                                                            urunstokkodu='".$siparisurunstokkodlarr[$i]."'
                                                                    ";
                                            $siparissepet=coksatir($siparissepet_sql);
                                            if(BosMu($siparissepet))
                                            {
                                                $siparissepetdurum="<span class='text-danger'>[Dikkat bu ürün Sepette Yok]</span>";
                                            }
                                            ?>
                                            <li class="tile">
                                                <div class="tile-content">
                                                    <div class="tile-icon">
                                                        <?=$urunresmi?>
                                                    </div>
                                                    <div class="tile-text" style="font-size: 13px">
                                                        <a class="ink-reaction" target="_blank" href="/urun/<?=$siparisurunid?>s.html?q=<?=$siparisurunstokkodlarr[$i]?>"><?=$siparisurunadlarr[$i]?> <?=$siparissepetdurum?><br></a>
                                                        <?=$siparisurunstokkodlarr[$i]?><br>
                                                        <?=$siparisurunmalzemelerr[$i]?><br>
                                                        <?=$siparisurunrenklerr[$i]?><br>
                                                        <?=$siparisurunbedenlerr[$i]?><br>
                                                        <?=$siparisurunfiyatlarr[$i]?> <?=$parabirimsimge?>*<?=$siparisurunadetlerr[$i]?> =
                                                        <?=$siparisurunfiyatlarr[$i]*$siparisurunadetlerr[$i]?> <?=$parabirimsimge?><br>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div><!--end .card-body -->
                            </div><!--end .card -->
                            <em class="text-caption text-danger text-right" style="font-weight: 500">Sipariş Toplam = <?=$siparistoplamtutar?> <?=$parabirimsimge?></em>
                        </div>
                        <!-- Kargo -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-head">
                                    <header>Kargo Bilgileri</header>
                                    <div class="tools">
                                        <div class="btn-group">
                                            <a class="btn btn-icon-toggle btn-collapse"><i class="fa fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                </div><!--end .card-head -->
                                <div class="card-body" style="min-height:300px">
                                    <ul class="list divider-full-bleed">
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="md md-verified-user"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisteslimattcno?>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisteslimatad?> <?=$siparisteslimatsoyad?>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="fa fa-phone"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisteslimatgsm?>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="fa fa-envelope"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisteslimateposta?>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="md md-location-on"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisteslimatmahallead?><br>
                                                    <?=$siparisteslimatadresacik?><br>
                                                    <?=$siparisteslimatilcead?> / <?=$siparisteslimatsehirad?>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div><!--end .card-body -->
                            </div><!--end .card -->
                        </div>
                    </div>
                    <div class="row no-margin">
                        <!-- Sepet -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-head">
                                    <header>Üye Sepeti</header>
                                </div>
                                <div class="card-body nano has-scrollbar" style="min-height:338px">
                                    <div class="nano-content">
                                        <?php
                                        $sepettoplamtutar=0;
                                        $uyesepet_sql="
                                                                            SELECT 
                                                                                uyesepet.*,sayfaad
                                                                            FROM
                                                                                uyesepet
                                                                                INNER JOIN uye ON uye.benzersizid=uyesepet.uyebenzersiz
                                                                                INNER JOIN sayfa ON sayfa.sayfaid=uyesepet.urunid
                                                                            
                                                                            WHERE
                                                                                uye.uyeid='".$siparisdurum_t["uyeid"]."' and uyesepet.siparisbenzersiz='".$siparisbenzersizid."'
                                                                            ORDER BY sepetolusturtarih desc    
                                                                        ";
                                        $uyesepetler=coksatir_arr($uyesepet_sql);
                                        if(!BosMu($uyesepetler))
                                        {
                                            echo '<ul class="list divider-full-bleed">';
                                            foreach($uyesepetler as $uyesepet)
                                            {
                                                $uyesepetdurum="Sepet Duruyor";
                                                if($uyesepet["sepetsil"]==1)$uyesepetdurum="Sepet Silinmiş";
                                                ?>
                                                <li class="tile" style="margin-bottom: 25px">
                                                    <div class="tile-content">
                                                        <div class="tile-icon">
                                                            <?=$urunresmi?>
                                                        </div>
                                                        <div class="tile-text" style="font-size: 13px">
                                                            <a target="_blank" href="/urun/<?=$uyesepet["urunid"]?>s.html?q=<?=$uyesepet["urunstokkodu"]?>"><?=$uyesepet["sayfaad"]?> <sup><?=$uyesepet["sepetguncelletarih"]?></sup></a><br>
                                                            <?=$uyesepet["urunstokkodu"]?><br>
                                                            <?=$uyesepet["urunmalzeme"]?><br>
                                                            <?=$uyesepet["urunrenk"]?><br>
                                                            <?=$uyesepet["urunbeden"]?><br>
                                                            <?=$uyesepet["urunfiyat"]?> <?=$parabirimsimge?>*<?=$uyesepet["urunadet"]?> =
                                                            <?=$uyesepet["urunfiyat"]*$uyesepet["urunadet"]?> <?=$parabirimsimge?><br>
                                                            Sepet Durum: <?=$uyesepetdurum?> |<?=$uyesepet["siparisbenzersiz"]?>
                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                                $sepettoplamtutar=$sepettoplamtutar+($uyesepet["urunfiyat"]*$uyesepet["urunadet"]);
                                            }
                                            echo '</ul>';
                                        }
                                        ?>
                                    </div>
                                    <div class="nano-pane">
                                        <div class="nano-slider"></div>
                                    </div>
                                </div>
                            </div>
                            <em class="text-caption text-danger text-right" style="font-weight: 500">Sepet Toplam = <?=$sepettoplamtutar?> <?=$parabirimsimge?></em>
                        </div>
                        <!-- Fatura -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-head">
                                    <header>Fatura Bilgileri</header>
                                    <div class="tools">
                                        <div class="btn-group">
                                            <a class="btn btn-icon-toggle btn-collapse"><i class="fa fa-angle-down"></i></a>
                                        </div>
                                    </div>
                                </div><!--end .card-head -->
                                <div class="card-body" style="min-height:300px;">
                                    <ul class="list divider-full-bleed">
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisfaturaad?> <?=$siparisfaturasoyad?>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="fa fa-phone"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisfaturagsm?>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="md md-location-on"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisfaturamahallead?><br>
                                                    <?=$siparisfaturaadresacik?><br>
                                                    <?=$siparisfaturailcead?> / <?=$siparisfaturasehirad?>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="tile">
                                            <div class="tile-content">
                                                <div class="tile-icon">
                                                    <i class="fa fa-building"></i>
                                                </div>
                                                <div class="tile-text" style="font-size: 13px">
                                                    <?=$siparisfaturaunvan?><br>
                                                    <?=$siparisfaturavergidairesi?><br>
                                                    <?=$siparisfaturavergino?>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div><!--end .card-body -->
                            </div><!--end .card -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="submit"
                                class="btn ink-reaction btn-raised btn-primary btnspr"
                                data-id="<?= $siparisbenzersizid ?>"
                                data-odemeyontemi="<?= $siparisodemeyontemi ?>"
                                data-odemedurum="<?= $siparisodemedurum ?>"
                                data-siparisdurum="<?= $siparisdurum ?>"
                                data-kargoserino="<?= $sipariskargoserino ?>"
                                data-kargoteslimatid="<?= $sipariskargotakip ?>"
                                data-notalici="<?= $siparisnotalici ?>"
                                data-notyonetici="<?= $siparisnotyonetici ?>"
                                data-toggle="modal"
                                data-placement="top"
                                data-target="#simpleModal" data-backdrop="true">Güncelle
                        </button>
                        <button class="siparisyazdir" data-siparisid="<?=$siparisbenzersizid?>"><img src="/tema/img/s/print.png" style="width:15px;margin-right:5px">Sipariş Yazdır</button>
                        <?php
                        if($siparisodemeyontemi=="bh")
                        {
                            if(!BosMu($siparisdekont))
                            {
                                ?>
                                <a class="btn ink-reaction btn-raised btn-primary-dark" href="/m/r/dekontlar/<?=$siparisdekont?>" target="_blank">Dekont Görüntüleyin</a>
                                <?php
                            }
                        }
                        ?>
                        <?php
                        if(!BosMu($siparisKargoFirmaCode))
                        {
                            if (((int)$siparisDurumIdForSiparis) >= SD_KARGOYA_TESLIM) {
                                ?>
                                <button
                                        type="submit"
                                        class="btn ink-reaction btn-raised btn-info kargoTakipDialogButton"
                                    <?= empty($sipariskargotakip) ? "disabled" : "" ?>
                                        data-id="<?= $siparisbenzersizid ?>"
                                        data-kargoteslimatid="<?= $sipariskargotakip ?>"
                                        data-original-title="Kargo Takip Bilgisi">Kargo Takip
                                </button>
                            <?php } ?>
                            <?php if (((int)$siparisDurumIdForSiparis) == SD_KARGOYA_HAZIR) {?>
                            <button
                                    type="button"
                                    class="btn ink-reaction btn-raised btn-info sevkiyatBaslatButton"
                                <?= !unserialize(TUM_KARGOLAR)[$siparisKargoFirmaCode]["entegrasyon"] || $siparisKargoSevkiyatYapildi ? "disabled" : "" ?>
                                    data-id="<?= $siparisbenzersizid ?>"
                                    data-kargoTeslimatId="<?= $sipariskargotakip ?>">Sevkiyatı Başlat
                            </button>
                            <button
                                    type="button"
                                    class="btn ink-reaction btn-raised btn-accent-bright barkodYazdirButton"
                                <?= !unserialize(TUM_KARGOLAR)[$siparisKargoFirmaCode]["entegrasyon"] || !$siparisKargoSevkiyatYapildi ? "disabled" : "" ?>
                                    data-kargoCode="<?=$siparisKargoFirmaCode?>"
                                    data-id="<?= $siparisbenzersizid ?>"
                                    data-kargoTeslimatId="<?= $sipariskargotakip ?>"><i class="fa fa-print m-r-md"></i>Barkod Yazdır
                            </button>
                        <?php }
                        }?>
                    </div>
                </div>
            </div>

            <section class="model" id="siparisyazdir-<?=$siparisbenzersizid?>">
                <section class="model-icerik">
                    <span class="kapat">&times;</span>
                    <section class="faturabilgiler">
                        <img src="<?=$logoresim?>" width="<?=$logogenislik?>" height="<?=$logoyukseklik?>" class="sitelogo">
                        <li><span>Sipariş ID</span><?=$siparisbenzersizid?></li>
                        <li><span>İşlem Tarihi</span><?= substr($siparistariholustur, 0, 19) ?></li>
                        <li><span>Ödeme Türü</span><?=B($siparisodemeyontemi)?></li>
                        <li><span></span></li>
                        <li><span>ÜRÜN BİLGİLERİ</span></li>
                        <?php
                        for($i=0; $i<$siparisuruntoplam;$i++)
                        {
                            $tumtutar=0;$tumadet=0;

                            if(!BosMu($siparisurunstokkodlar))
                            {
                                $stokkodu = $siparisurunstokkodlarr[$i];
                            }
                            else
                            {
                                $stokkodu = teksatir(" Select urunstokkodu from uyesepet WHERE siparisbenzersiz='". $siparisbenzersizid ."' and urunid='".$siparisurunidlerr[$i]."' and urunfiyat='".$siparisurunfiyatlarr[$i]."' and urunadet='".$siparisurunadetlerr[$i]."'","urunstokkodu");
                            }

                            echo "
                                                            <li><span>Stok Kodu</span>".$stokkodu."</li>
                                                            <li><span>Ürün</span>".$siparisurunadlarr[$i]."</li>";

                            if(count($siparisurunrenklerr)==$siparisuruntoplam)
                                echo "<li><span>Renk</span>".$siparisurunrenklerr[$i]."</li> ";
                            if(count($siparisurunbedenlerr)==$siparisuruntoplam)
                                echo "<li><span>Ölçü</span>".$siparisurunbedenlerr[$i]."</li> ";
                            $siparisurunfiyatlarr[$i]*$siparisurunadetlerr[$i];
                            if(count($siparisurunbedenlerr)==$siparisuruntoplam)
                                echo "<li><span>Malzeme</span>".$siparisurunmalzemelerr[$i]."</li> ";
                            echo "<li><span>Adet</span>".$siparisurunadetlerr[$i]."</li>";;
                            echo "<li><span>Fiyat</span>".$siparisurunfiyatlarr[$i]." $parabirimsimge</li>";
                            echo "<li><span>Toplam Fiyat</span>".$siparisurunfiyatlarr[$i]*$siparisurunadetlerr[$i]." $parabirimsimge</li><li><hr></li>";
                            $geneltoplam+=$siparisurunfiyatlarr[$i]*$siparisurunadetlerr[$i];
                        }
                        ?>
                        <!-- li><span></span></li -->
                        <li><span>Genel Toplam</span><?=number_format($geneltoplam,2)." ".$parabirimsimge?></li>
                        <!-- li><span></span></li>
                                                        <li><span>Ad Soyad</span><?=$uyeadsoyad?> </li -->
                        <li><span>KARGO BİLGİLERİ</span></li>
                        <?php if(!BosMu($tempBarcodeNumber)){?>
                            <li><span><?=$tempBarcodeNumber?></span>
                                <?php if(!BosMu($siparisKargoBarcode))echo '<img src="'.json_decode($siparisKargoBarcode)->Images->base64Binary.'" >'?>
                            </li>
                        <?php }?>
                        <li><span>Alıcı İsim</span><?=$siparisteslimatad." ".$siparisteslimatsoyad?></li>
                        <li><span>Alıcı Telefon</span><?=$siparisteslimatgsm?></li>
                        <li><span>Alıcı Eposta</span><?=$uyeeposta?></li>
                        <li><span>Alıcı TC</span><?=$siparisteslimattcno?></li>
                        <li><span>Alıcı Adres</span><?=$siparisteslimatmahallead." ".$siparisteslimatadresacik." ".$siparisteslimatsemtad." / ".$siparisteslimatilcead." / ".$siparisteslimatsehirad?></li>
                        <li><span></span></li>
                        <li><span>FATURA BİLGİLERİ</span></li>
                        <li><span>Fatura Ünvan</span><?=$siparisfaturaunvan?></li>
                        <li><span>Vergi Dairesi</span><?=$siparisfaturavergidairesi?></li>
                        <li><span>Vergi No</span><?=$siparisfaturavergino?></li>
                        <li><span>Fatura Kargo İsim</span><?=$siparisfaturaad." ".$siparisteslimatsoyad?></li>
                        <li><span>Fatura Kargo Telefon</span><?=$siparisfaturagsm?></li>
                        <li><span>Fatura Kargo Adres</span><?=$siparisfaturamahallead." ".$siparisfaturaadresacik." ".$siparisfaturasemtad." / ".$siparisfaturailcead."/ ".$siparisfaturasehirad?></li>
                        <li></li>
                        <liclass="yz"><button class="yazdir" style="font-size:15px"><img src="/tema/img/s/print.png" style="width:15px;margin-right:3px">Yazdır</button></li>
                        <style>
                            body {
                                font-family: "Roboto", sans-serif, Helvetica, Arial, sans-serif;
                                font-size: 13px;
                                line-height: 1.846153846;
                                color: #313534;
                                background-color: #ffffff;
                            }
                            .faturabilgiler li{list-style:none}
                            .faturabilgiler li span{width:150px;font-weight:bold;display:inline-block}
                            img.sitelogo{margin-top:10px;margin-bottom:20px;width:300px;height:auto}
                        </style>
                    </section>
                </section>
            </section>
            <?php
            if($si==$sayfalar_bitir)break;
        }
    }
    $siparistoplam=count($siparisler);
    if($siparistoplam>20)
    {
        $toplamsayfa=ceil($siparistoplam/50);
        if($qsimdisayfa==0)$qsimdisayfa=1;
        sayfala("OrderList.php?tip=".q("tip"),$toplamsayfa,$qsimdisayfa);
    }
}
unset($siparisdurum_v, $sayfaid, $sayfaadl);
?>