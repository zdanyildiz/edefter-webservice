<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
$f_sayfaid=S(f("sayfaid"));$guncellemesonuc="";
if($f_sayfaid!=0 && S(f("seo"))==1)
{
	Veri(true);
	$f_urunbaslik=f("urunbaslik");
	if(!BosMu($f_urunbaslik))
	{
		$tablo="sayfa";
		$simdi==date("Y-m-d H:i:s");
		$sutunlar='sayfatarihguncel,sayfaad';
		$degerler=$simdi."|*_".$f_urunbaslik;
		guncelle($sutunlar,$degerler,$tablo," sayfaid='". $f_sayfaid ."' ",37,3);
		$guncellemesonuc="Ürün Başlığı güncellendi";
	}
	else
	{
		$guncellemesonuc="!->Ürün Başlığı boş olamaz";
	}
	$f_benzersizid=f("benzersizid");
	$f_seobaslik=f("seobaslik");
	$f_seoaciklama=f("seoaciklama");
	$f_seokelime=f("seokelime");
	if(!BosMu($f_benzersizid) && !BosMu($f_seobaslik) && !BosMu($f_seoaciklama) && !BosMu($f_seokelime))
	{
		$tablo="seo";
		$sutunlar='baslik,aciklama,kelime';
		$degerler=$f_seobaslik."|*_".$f_seoaciklama."|*_".$f_seokelime;
		guncelle($sutunlar,$degerler,$tablo," benzersizid='". $f_benzersizid ."' ",37,3);
		$guncellemesonuc=$guncellemesonuc."\\nSEO özellikleri güncellendi";
	}
	else
	{
		$guncellemesonuc=$guncellemesonuc."\\n!->SEO Özellikleri boş olamaz";
	}
}
if($f_sayfaid!=0 && S(f("fiyatstok"))==1)
{
	Veri(true);
	$varyant_s="
		Select urunozellikid from urunozellikleri where sayfaid='".$f_sayfaid."'
	";
	if($data->query($varyant_s))
	{
		$varyant_v=$data->query($varyant_s);unset($varyant_s);
		if($varyant_v->num_rows>0)
		{
			while ($varyant_t=$varyant_v->fetch_assoc())
			{
				$urunozellikid=$varyant_t["urunozellikid"];
				$f_urunsatisfiyat=f($urunozellikid."_urunsatisfiyat");
				$f_urunindirimsizfiyat=f($urunozellikid."_urunindirimsizfiyat");
				$f_urunindirimorani=f($urunozellikid."_urunindirimorani");
				$f_urunbayifiyat=f($urunozellikid."_urunbayifiyat");
				$f_urunalisfiyat=f($urunozellikid."_urunalisfiyat");
				$f_urunstok=f($urunozellikid."_urunstok");
				if(!BosMu($f_urunsatisfiyat) && !BosMu($f_urunindirimsizfiyat) && !BosMu($f_urunindirimorani) && !BosMu($f_urunbayifiyat) && !BosMu($f_urunalisfiyat) && !BosMu($f_urunstok))
				{
					if(is_numeric($f_urunsatisfiyat) && is_numeric($f_urunindirimsizfiyat) && is_numeric($f_urunindirimorani) && is_numeric($f_urunbayifiyat) && is_numeric($f_urunalisfiyat))
					{
						$tablo="urunozellikleri";
						$sutunlar='urunsatisfiyat,urunindirimsizfiyat,urunindirimorani,urunbayifiyat,urunalisfiyat,urunstok';
						$degerler=$f_urunsatisfiyat."|*_".$f_urunindirimsizfiyat."|*_".$f_urunindirimorani."|*_".$f_urunbayifiyat."|*_".$f_urunalisfiyat."|*_".S($f_urunstok);
						guncelle($sutunlar,$degerler,$tablo," urunozellikid='". $urunozellikid ."' ",37,3);
						$guncellemesonuc=$guncellemesonuc."\\nÜrün özellikleri güncellendi";
					}
					else
					{
						$guncellemesonuc=$guncellemesonuc."\\n!->Ürün Fiyat Özellikleri yalnızca Rakam ve Nokta . olabilir \\n!-> $f_urunsatisfiyat \\n!-> $f_urunindirimsizfiyat \\n!-> $f_urunindirimorani \\n!-> $f_urunbayifiyat \\n!-> $f_urunalisfiyat \\n!-> $f_urunstok";
					}
				}
				else
				{
					$guncellemesonuc=$guncellemesonuc."\\n!-> $urunozellikid $f_urunsatisfiyat $f_urunindirimsizfiyat $f_urunindirimorani $f_urunbayifiyat $f_urunalisfiyat $f_urunstok Ürün Özellikleri boş olamaz";
				}
			}unset($varyant_t);
		}unset($varyant_v);
	}else{hatalogisle("ürün liste, varyant güncelle",$data->error);}
}
?><!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>
	<body>
		<script>
				alert('<?=$guncellemesonuc?>');
				<?php 
					$iğne   = '!';
					$konum = strpos($guncellemesonuc, $iğne);
					if($konum===false)
					{
						echo '
						$("#trgizli'.$f_sayfaid.'",parent.document).hide();
						$("#2trgizli'.$f_sayfaid.'",parent.document).hide();
						';
					}
				?>
		</script>
	</body>
</html>