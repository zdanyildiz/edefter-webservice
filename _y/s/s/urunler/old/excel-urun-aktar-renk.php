<?php 

$urun_urunrenk_s="
	SELECT 
		renkad
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0
	GROUP BY 
		renkad
";
if($data->query($urun_urunrenk_s))
{
	$urun_urunrenk_v=$data->query($urun_urunrenk_s);unset($urun_urunrenk_s);
	if($urun_urunrenk_v->num_rows>0)
	{
		echo "<h1>Renk Aktarımı</h1><br><hr><br>";
		while ($urun_urunrenk_t=$urun_urunrenk_v->fetch_assoc())
		{
			$urunrenkad=$urun_urunrenk_t["renkad"];
			if(!BosMu($urunrenkad))
			{
				echo $urunrenkad." <br>";
				$urunrenkid=teksatir("SELECT urunrenkid FROM urunrenk WHERE urunrenksil='0' AND urunrenkad='".$urunrenkad."'","urunrenkid");
				if(S($urunrenkid)==0)
				{
					$sutunlar="
						urunrenkad,
						urunrenkgrupid,
						urunrenksil
					";
					$degerler=
						$urunrenkad."|*_".
						"1"."|*_".
						"0"
					;
					ekle($sutunlar,$degerler,"urunrenk",35);
					$urunrenkid=teksatir("SELECT urunrenkid FROM urunrenk WHERE urunrenksil='0' AND urunrenkad='".$urunrenkad."'","urunrenkid");
				}
				guncelle("renkid,renkgrupid","$urunrenkid|*_1","urunaktar","renkad='".$urunrenkad."'",35);
			}
		}unset($urun_urunrenk_t);
	}else{echo "<h1>Renk Aktarımı Tamamlandı</h1><p>Aktarılacak yeni renk yok</p><br><hr><br>";}unset($urun_urunrenk_v);
}else{hatalogisle("Ürün Aktar - urunrenk",$data->error);}
?>