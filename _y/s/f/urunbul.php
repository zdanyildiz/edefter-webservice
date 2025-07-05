<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>
	<body>
	<?php
	if(!BosMu(q("q")))
	{
		Veri(true);
		$urunler_d=0;
		$urunler_s="
			SELECT 
				sayfa.sayfaid,sayfaad,sayfa.benzersizid,sayfaaktif,
				kategoriad,
				resim.resim,resimklasorad,
				urunozellikleri.urunsatisfiyat,
				urunozellikleri.urungununfirsati,
				link,kelime,baslik,aciklama
			FROM 
				kategori 
					INNER JOIN
						sayfalistekategori on
							sayfalistekategori.kategoriid=kategori.kategoriid
						INNER JOIN sayfa on
							sayfa.sayfaid=sayfalistekategori.sayfaid
							LEFT JOIN sayfalisteresim on
								sayfa.sayfaid=sayfalisteresim.sayfaid
								Left JOIN resim on
									resim.resimid=sayfalisteresim.resimid
									Left JOIN resimklasor on
										resimklasor.resimklasorid=resim.resimklasorid 
						LEFT JOIN urunozellikleri on
							urunozellikleri.sayfaid=sayfa.sayfaid
						inner join seo on seo.benzersizid=sayfa.benzersizid
			WHERE 
				(sayfasil='0' and kategorigrup='7') and 
				(sayfaad like '%". q("q") ."%' or urunmodel like '%". q("q") ."%' or urunstokkodu like '%". q("q") ."%')
			Group BY 
				sayfa.sayfaid
			ORDER BY 
				sayfaaktif desc,sayfalistekategori.kategoriid ASC,resim asc,urunmodel ASC
			lımıt 50
		";
		if(!$data->query($urunler_s)){die($data->error);}
		$urunler_v=$data->query($urunler_s);unset($urunler_s);
		if($urunler_v->num_rows>0)$urunler_d=1;

		$aramasonuc="";
		if($urunler_d==1)
		{
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
				if(S($sayfaaktif==1)){$urunaktifstyle='style-info';$urunaktifsimge='<i class="aktif md md-thumb-up" title="Aktif"></i>';}else{$urunaktifstyle='style-danger';$urunaktifsimge='<i class="aktif md md-error" title="Pasif"></i>';}
				if(S($urungununfirsati)==1){$gununfirsaticss='style-warning';$gununfirsatiyaz='<i class="md md-grade" title="Günün Fırsatı"></i>';}else{$gununfirsaticss='';$gununfirsatiyaz='';}

				$aramasonuc=$aramasonuc.'<tr id="tr'.$sayfaid.'" data-id="trgizli'.$sayfaid.'" data-ustid="tr'.$sayfaid.'"><td class="text-center '.$gununfirsaticss.'>'.$gununfirsatiyaz.'</td><td>'.$i.') '.$sayfaid.'</td><td><img src="/m/r/?resim='. $resimklasorad .'/'. $resim .'&g=70&y=70" width="50" height="40"></td><td data-id="trgizli'.$sayfaid.'" data-ustid="tr'.$sayfaid.'" class="urunsatir">'.$sayfaad.' (resim: '.$resimsayisi.')</td><td>'.$kategoriad.'</td><td>'.$urunsatisfiyat.'</td><td>'.$urunsayisi.'</td><td><a href="/_y/s/s/urunler/urunekle.php?sayfaid='.$sayfaid.'" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a><a id="urunsil" href="#textModal" class="btn btn-icon-toggle" data-id="'.$sayfaid.'" data-toggle="modal" data-placement="top" data-original-title="Sil" data-target="#simpleModal" data-backdrop="true"><i class="fa fa-trash-o"></i></a></td><td class="bilgi '.$urunaktifstyle.' text-center"><a href="/_y/s/f/sil.php?sil=urunaktif&id='.$sayfaid.'" target="_islem">'.$urunaktifsimge.'</a></td><td><a href="'.$seolink.'" title="Sayfayı Gör" target="_blank"><i class="fa fa-external-link"></i></a></td></tr><tr id="trgizli'.$sayfaid.'" style="display:none" class="style-accent-bright"><form class="form form-validation form-validate" action="/_y/s/f/urunguncelle.php" method="post" target="_islem" novalidate="novalidate"><input type="hidden" name="seo" value="1"><input type="hidden" name="sayfaid" value="'.$sayfaid.'"><input type="hidden" name="benzersizid" value="'.$benzersizid.'"><td colspan="10"><div class="form-group"><a data-id="trgizli'.$sayfaid.'" data-ustid="tr'.$sayfaid.'" class="urunsatiralt btn ink-reaction btn-raised btn-xs style-danger">KAPAT (x)</a></div><div class="card row" style="margin-left: 5px; margin-right: 5px"><div class="card-body"><div class="form-group"><input  type="text"  name="urunbaslik"  id="urunbaslik'.$sayfaid.'"  class="form-control"  placeholder="Ürün Başlık"  value="'.$sayfaad.'"  data-rule-minlength="5" maxlength="65" aria-invalid="false" required aria-required="true"><label for="urunbaslik'.$sayfaid.'"  style="margin-top:-10px">Ürün Başlık</label></div></div></div><div class="card row" style="margin-left: 5px; margin-right: 5px"><div class="card-body"><div class="form-group"><input  type="text"  name="seobaslik"  id="seobaslik'.$sayfaid.'"  class="form-control"  placeholder="Adidas Terrex Swift Solo Erkek Siyah Spor Ayakkabı - D67031"  value="'.$seobaslik.'"  data-rule-minlength="5" maxlength="65" aria-invalid="false" required aria-required="true"><label for="seobaslik'.$sayfaid.'"  style="margin-top:-10px">SEO Başlık</label></div><div class="form-group"><textarea  id="seoaciklama'.$sayfaid.'"  name="seoaciklama"  placeholder="Adidas TERREX Swift Solo D67031 Outdoor Siyah Fitness Erkek Spor Ayakkabı orjinal ürün, ücretsiz kargo ve peşin ödeme indirimi ve kredi kartı taksit seçenekleri ile en uygun fiyata burada" class="form-control"   rows="3" data-rule-minlength="25" maxlength="200" aria-invalid="false" required aria-required="true">'.$seoaciklama.'</textarea><label for="seoaciklama'.$sayfaid.'" style="margin-top:-10px">SEO Açıklama</label></div><div class="form-group"><textarea  id="seokelime'.$sayfaid.'"  name="seokelime" class="form-control"  placeholder="adidas ayakkabı,adidas spor ayakkabı,adidas terrex swift solo,adidas siyah spor ayakkabı,erkek siyah spor ayakkabı"  rows="2" data-rule-minlength="6" maxlength="255" aria-invalid="false" required aria-required="true">'.$seokelime.'</textarea><label for="seokelime'.$sayfaid.'" style="margin-top:-10px">SEO Kelimeler</label></div><div class="card-actionbar"><div class="card-actionbar-row"><button type="submit" class="btn btn-primary btn-default">SEO GÜNCELLE</button></div></div></div></div></td></form></tr>';
				$varyant_s="
					SELECT
						urunozellikid,urunstok,urunsatisfiyat,urunindirimsizfiyat,urunbayifiyat,urunalisfiyat,
						urunbedenad,urunstokkodu,urunindirimorani
					FROM 
						urunozellikleri
							left join urunbeden on urunbeden.urunbedenid=urunozellikleri.urunbedenid
					where
						sayfaid='".$sayfaid."'
				";
				if($data->query($varyant_s))
				{
					$varyantyazbasla='';$varyantyaz='';$varyantyazbitir='';
					$varyantyazbasla='<tr id="2trgizli'.$sayfaid.'" style="display:none" class="style-accent-bright"><td colspan="10"><form class="form form-validation form-validate" action="/_y/s/f/urunguncelle.php" method="post" target="_islem" novalidate="novalidate"><input type="hidden" name="fiyatstok" value="1"><input type="hidden" name="sayfaid" value="'.$sayfaid.'">';

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
									$urunstokkodu=$varyant_t["urunstokkodu"];
									$urunindirimorani=$varyant_t["urunindirimorani"];
									$varyantyaz=$varyantyaz.'<div class="card row" style="margin-left: 5px; margin-right: 5px"><div class="card-head"><div class="col-sm-3 style-primary-bright"><div class="form-group"><input type="text" name="'. $urunozellikid .'_urunbedenad" id="urunbedenad'. $urunozellikid .'" class="form-control" value="'. $urunbedenad .'" readonly><label for="urunbedenad'. $urunozellikid .'" style="margin-top:-10px">Beden</label></div></div><div class="col-sm-3"><div class="form-group"><input type="text" name="'. $urunozellikid .'_urunsatisfiyat" id="urunsatisfiyat'. $urunozellikid .'" class="form-control" placeholder="99.99" value="'. $urunsatisfiyat .'" data-rule-number="true" required="" aria-required="true" aria-invalid="false"><label for="urunfiyat'. $urunozellikid .'" style="margin-top:-10px">Ürün Satış Fiyat</label></div></div><div class="col-sm-3"><div class="form-group"><input type="text" name="'. $urunozellikid .'_urunindirimsizfiyat" id="urunindirimlifiyat'. $urunozellikid .'" class="form-control" placeholder="79.99" value="'. $urunindirimsizfiyat .'" data-rule-number="true" required aria-required="true" aria-invalid="false"><label for="urunindirimsizfiyat'. $urunozellikid .'" style="margin-top:-10px">Ürün İnd.SİZ Fiyat</label></div></div><div class="col-sm-3"><div class="form-group"><input type="text" name="'. $urunozellikid .'_urunindirimorani" id="urunindirimorani'. $urunozellikid .'" class="form-control" placeholder="0.15" value="'.$urunindirimorani.'" data-rule-number="true" required aria-required="true" aria-invalid="false"><label for="urunindirimorani'. $urunozellikid .'" style="margin-top:-10px">Ürün İndirim %10 için 0.10</label></div></div><div class="col-sm-3 style-primary-bright"><div class="form-group"><input type="text" name="'. $urunozellikid .'_urunstokkodu" id="urunstokkodu'. $urunozellikid .'" class="form-control" value="'. $urunstokkodu .'" readonly><label for="urunstokkodu<'. $urunozellikid .'" style="margin-top:-10px">Stokkodu</label></div></div><div class="col-sm-3"><div class="form-group"><input type="text" name="'. $urunozellikid .'_urunbayifiyat" id="urunbayifiyat'. $urunozellikid .'" class="form-control" placeholder="79.99" value="'. $urunbayifiyat .'" data-rule-number="true" required aria-required="true" aria-invalid="false" ><label for="urunbayifiyat'. $urunozellikid .'" style="margin-top:-10px">Ürün Bayi Fiyat</label></div></div><div class="col-sm-3"><div class="form-group"><input type="text" name="'. $urunozellikid .'_urunalisfiyat" id="urunalisfiyat'.$sayfaid.'" class="form-control" placeholder="49.99" value="'.$urunalisfiyat.'" data-rule-number="true"><label for="urunalisfiyat'. $urunozellikid .'" style="margin-top:-10px">Ürün Alış Fiyat (Sadece siz görebilirsiniz)</label></div></div><div class="col-sm-3"><div class="form-group"> <input type="text" name="'. $urunozellikid .'_urunstok" id="urunstok'.$sayfaid.'" class="form-control" placeholder="Ürün Stok 20" value="'. $urunstok .'" data-rule-digits="true"><label for="urunstok'. $urunozellikid .'" style="margin-top:-10px">Ürün Stok</label></div></div></div></div>';
								}
								$varyantyaz=$varyantyaz.'<div class="card-actionbar"><div class="card-actionbar-row"><button type="submit" class="btn btn-primary btn-default">FİYAT/STOK GÜNCELLE</button></div></div>';
							}			
					$varyantyazbitir='</form></td></tr>';
					$aramasonuc=$aramasonuc.$varyantyazbasla.$varyantyaz.$varyantyazbitir;
				}else{die($data->error);}
			}
			//echo '<textarea id="aramasonuclari">'.$aramasonuc.'</textarea>';
			//gelen veri satır başlıkları ile temizle
		}
		unset($urunler_d,$urunler_v,$urunlerid,$urunlerdilid,$urunlerad,$urunlerdil);
	}
	?>
		<script>if($("#aramasonuclari")){$("tbody",parent.document).html('<?=$aramasonuc?>');}</script>
	</body>
</html>