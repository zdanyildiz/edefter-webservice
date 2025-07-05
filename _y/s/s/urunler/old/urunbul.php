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
if(!BosMu(q("q")))
{
	$sqlek=$sqlek." and (sayfaad like '%".q("q")."%' or urunmodel like '%".q("q")."%' or urunstokkodu like '%".q("q")."%')";
	$katlinkek=$katlinkek."&q=".q("q");
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
		urunozellikleri.urunsatisfiyat,urunozellikleri.urunmodel,
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
		?>
		<tr><td colspan="3"></td><td colspan="8">Toplam: <?=$urunlertoplam?> ürün ve <?=$urunlertoplamsayfa?> sayfa</td></tr>
		<?php
		while ($urunler_t=$urunler_v->fetch_assoc()) 
		{
			$i++;$fiyatyaz="";$resimklasorad="";
			$benzersizid=$urunler_t["benzersizid"];
			$sayfaid=$urunler_t["sayfaid"];
			$sayfaad=$urunler_t["sayfaad"];
			$sayfaaktif=$urunler_t["sayfaaktif"];
			$kategoriad=$urunler_t["kategoriad"];
			
			$resim=$urunler_t["resim"];
			if(BosMu($resim))$resim="bos.jpg";else $resimklasorad=$urunler_t["resimklasorad"];

			$urunsatisfiyat=$urunler_t["urunsatisfiyat"];
			$urungununfirsati=$urunler_t["urungununfirsati"];

			$seolink=$urunler_t["link"];
			$seobaslik=$urunler_t["baslik"];
			$seoaciklama=$urunler_t["aciklama"];
			$seokelime=$urunler_t["kelime"];
			
			$resimsayisi=teksatir("Select count(*) as resimsayisi from sayfalisteresim where sayfaid='".$sayfaid."'","resimsayisi");
			$urunsayisi=teksatir("Select sum(urunstok) as urunsayisi from urunozellikleri where sayfaid='".$sayfaid."'","urunsayisi");
			?>
			<tr id="tr<?=$sayfaid?>" data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>">
				<td><input name="sayfalar[]" type="checkbox" value="<?=$sayfaid?>"></td>
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
				<td><?=$i?>) </td>
				<td><img src="<?="/m/r/?resim=$resimklasorad"."/"."$resim"?>&g=70&y=70" width="50" height="40"></td>
				<td data-id="trgizli<?=$sayfaid?>" data-ustid="tr<?=$sayfaid?>" class="urunsatir"><?=$sayfaad?> (resim: <?="$resimsayisi"?>) (id:<?=$sayfaid?>)</td>
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
				<form 
					class="form form-validation form-validate" 
					action="/_y/s/f/urunguncelle.php" 
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
                                                        echo '<img src="/m/r/?resim=urun/'.$resimsira_tekresim.'&g=100&y=100" style="border:solid 1px #ccc" data-id="'.$resimsira_id_ayikla[$x].'">';
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
                            <?php }}else{echo $data->error;}?>
                        <?php $model=$seolink=$urunler_t["urunmodel"];
                        $orjinallink=teksatir("select link from urunaktar where model='".$model."'","link");
                        echo '<div class="form-group col-sm-12" style="z-index:5"><a href="'.$orjinallink.'" target="_blank">Orjinal Sayfayı Gör</a></div>';?>
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
											class="btn btn-primary btn-default">SEO GÜNCELLE</button>
									</div>
								</div>
							</div>
						</div>
					</td>
				</form>
			</tr>
			<?php
            $varyant_s="
                SELECT
                    urunozellikid,urunstok,urunsatisfiyat,urunindirimsizfiyat,urunbayifiyat,urunalisfiyat,
                    urunbedenad,urunrenkad,urunmalzemead,urunstokkodu,urunindirimorani
                FROM 
                    urunozellikleri
                        left join urunbeden on urunbeden.urunbedenid=urunozellikleri.urunbedenid
                        left join urunrenk on urunrenk.urunrenkid=urunozellikleri.urunrenkid
                        left join urunmalzeme on urunmalzeme.urunmalzemeid=urunozellikleri.urunmalzemeid
                        
                where
                    sayfaid='".$sayfaid."'
			";
            if($data->query($varyant_s))
            {
                ?>
                <tr id="2trgizli<?=$sayfaid?>" style="display:none" class="style-accent-bright">
                    <td colspan="11"><form
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
                                    $urunbedenad=$varyant_t["urunbedenad"];
                                    $urunrenkad=$varyant_t["urunrenkad"];
                                    $urunmalzemead=$varyant_t["urunmalzemead"];
                                    $urunstokkodu=$varyant_t["urunstokkodu"];
                                    $urunindirimorani=$varyant_t["urunindirimorani"];
                                    ?>
                                    <div class="card row" style="margin-left: 5px; margin-right: 5px">
                                        <div class="card-head">
                                            <div class="col-sm-3 style-primary-bright">
                                                <div class="form-group">
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
		}unset($urunler_t);
		?><tr><td colspan="11"><?php 
		if($qsimdisayfa==0)$qsimdisayfa=1;
		sayfala("urunliste.php?kategori=".q("kategori"),$urunlertoplamsayfa,$qsimdisayfa);
		?></td></tr><?php
	}
	unset($urunler_d,$urunler_v,$sayfaid,$sayfaad);
?>
												