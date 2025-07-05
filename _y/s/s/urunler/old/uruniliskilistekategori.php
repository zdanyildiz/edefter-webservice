<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="sayfa";

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
}
elseif(S(q("sirala"))==3)
{
	$orderby="sayfalistekategori.kategoriid Desc";
}
elseif(S(q("sirala"))==4)
{
	$orderby="sayfaad,kategoriid Asc";
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
		sayfa.sayfaid,sayfaad,sayfa.benzersizid,sayfaaktif,
		kategoriad,
		resim.resim,resimklasorad,
		urunozellikleri.urunsatisfiyat,
		urunozellikleri.urungununfirsati,
		link,kelime,baslik,aciklama
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
			left join urunozellikleri on urunozellikleri.sayfaid=sayfa.sayfaid
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
        $iliskidurum="";
        $iliskiurunid=$urunler_t["iliskiurunid"];
        $iliskiid=$urunler_t["iliskiid"];
        if(S($iliskiurunid)==$sayfaid)
        {
            $iliskidurum="<span class='ink-reaction btn-xs btn-success'>İlişkili Ürün: VAR</span>";
        }
        ?>
        <tr id="tr<?=$sayfaid?>" data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>">
            <td>
                <label class="checkbox-inline checkbox-styled checkbox-primary">
                    <input name="sayfalar[]" type="checkbox" value="<?=$sayfaid?>">
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
                        action="/_y/s/f/uruniliskiguncelle.php"
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
                                            sayfa.sayfaid,sayfaad,uruniliski.iliskiid 
                                        from 
                                            uruniliski inner join sayfa on 
                                                sayfa.sayfaid=uruniliski.urunid 
                                        where 
                                            iliskiid='".$sayfaid."' or ( iliskiid!=0 and urunid='".$sayfaid."')
                                        
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
                                                $iliskiurunid=$iliskilise_t["iliskiid"];
                                                if($iliskisayfaid!=$sayfaid)
                                                {
                                                    echo '<option value="'.$iliskisayfaid.'">'.$iliskisayfaad.'</option>';
                                                }
                                                elseif($iliskiurunid!=$sayfaid){
                                                    $iliskisayfaad=teksatir("Select sayfaad from sayfa where sayfaid='".$iliskiurunid."'","sayfaad");
                                                    echo '<option value="'.$iliskisayfaid.'" disabled>'.$iliskisayfaad.'</option>';
                                                }
                                                else{
                                                    echo '<option value="'.$iliskisayfaid.'" disabled>'.$iliskisayfaad.'</option>';
                                                }
                                            }
                                        }
                                    }else{die($data->error);}
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
	unset($urunler_d,$urunler_v,$sayfaid,$sayfaad);
?>
