<?php 

$urun_urunmalzeme_s="
	SELECT 
		malzemead
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0
	GROUP BY 
		malzemead
";
if($data->query($urun_urunmalzeme_s))
{
	$urun_urunmalzeme_v=$data->query($urun_urunmalzeme_s);unset($urun_urunmalzeme_s);
	if($urun_urunmalzeme_v->num_rows>0)
	{
		echo "<h1>Malzeme Aktarımı</h1><br><hr><br>";
		while ($urun_urunmalzeme_t=$urun_urunmalzeme_v->fetch_assoc())
		{
			$urunmalzemead=$urun_urunmalzeme_t["malzemead"];
			if(!BosMu($urunmalzemead))
			{
				echo $urunmalzemead." <br>";
				$urunmalzemeid=teksatir("SELECT urunmalzemeid FROM urunmalzeme WHERE urunmalzemesil='0' AND urunmalzemead='".$urunmalzemead."'","urunmalzemeid");
				if(S($urunmalzemeid)==0)
				{
					$sutunlar="
						urunmalzemead,
						urunmalzemegrupid,
						urunmalzemesil
					";
					$degerler=
						$urunmalzemead."|*_".
						"1"."|*_".
						"0"
					;
					ekle($sutunlar,$degerler,"urunmalzeme",35);
					$urunmalzemeid=teksatir("SELECT urunmalzemeid FROM urunmalzeme WHERE urunmalzemesil='0' AND urunmalzemead='".$urunmalzemead."'","urunmalzemeid");
				}
				guncelle("malzemeid,malzemegrupid","$urunmalzemeid|*_1","urunaktar","malzemead='".$urunmalzemead."'",35);
			}
		}unset($urun_urunmalzeme_t);
	}else{echo "<h1>malzeme Aktarımı Tamamlandı</h1><p>Aktarılacak yeni malzeme yok</p><br><hr><br>";}unset($urun_urunmalzeme_v);
}else{hatalogisle("Ürün Aktar - urunmalzeme",$data->error);}
?>