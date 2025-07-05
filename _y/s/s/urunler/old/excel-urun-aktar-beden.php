<?php 

$urun_urunbeden_s="
	SELECT 
		bedenad
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0
	GROUP BY 
		bedenad
";
if($data->query($urun_urunbeden_s))
{
	$urun_urunbeden_v=$data->query($urun_urunbeden_s);unset($urun_urunbeden_s);
	if($urun_urunbeden_v->num_rows>0)
	{
		echo "<h1>Beden Aktarımı</h1><br><hr><br>";
		while ($urun_urunbeden_t=$urun_urunbeden_v->fetch_assoc())
		{
			$urunbedenad=$urun_urunbeden_t["bedenad"];
			if(!BosMu($urunbedenad))
			{
				echo $urunbedenad." <br>";
				$urunbedenid=teksatir("SELECT urunbedenid FROM urunbeden WHERE urunbedensil='0' AND urunbedenad='".$urunbedenad."'","urunbedenid");
				if(S($urunbedenid)==0)
				{
					$sutunlar="
						urunbedenad,
						urunbedengrupid,
						urunbedensil
					";
					$degerler=
						$urunbedenad."|*_".
						"1"."|*_".
						"0"
					;
					ekle($sutunlar,$degerler,"urunbeden",35);
					$urunbedenid=teksatir("SELECT urunbedenid FROM urunbeden WHERE urunbedensil='0' AND urunbedenad='".$urunbedenad."'","urunbedenid");
				}
				guncelle("bedenid,bedengrupid","$urunbedenid|*_1","urunaktar","bedenad='".$urunbedenad."'",35);
			}
		}unset($urun_urunbeden_t);
	}else{echo "<h1>Beden Aktarımı Tamamlandı</h1><p>Aktarılacak yeni beden yok</p><br><hr><br>";}unset($urun_urunbeden_v);
}else{hatalogisle("Ürün Aktar - urunbeden",$data->error);}
?>