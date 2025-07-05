<?php 

$urun_marka_s="
	SELECT 
		markaad
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0
	GROUP BY 
		markaad
";
if($data->query($urun_marka_s))
{
	$urun_marka_v=$data->query($urun_marka_s);unset($urun_marka_s);
	if($urun_marka_v->num_rows>0)
	{
		echo "<h1>Marka Aktarımı</h1><br><hr><br>";
		while ($urun_marka_t=$urun_marka_v->fetch_assoc())
		{
			$markaad=$urun_marka_t["markaad"];
			echo $markaad." - <br>";
			$markaid=teksatir("SELECT markaid FROM urunmarka WHERE markasil='0' AND markaad='".$markaad."'","markaid");
			if(S($markaid)==0)
			{
				$simdi=date("Y-m-d H:i:s");
				$benzersizid=SifreUret(20,2);
				$sutunlar="
					markatariholustur,
					markatarihguncel,
					markaad,
					markaaciklama,
					markaindirim,
					markataksit,
					markapromosyontutari,
					markasil,
					benzersizid
				";
				$degerler=
					$simdi."|*_".
					$simdi."|*_".
					$markaad."|*_".
					""."|*_".
					"0"."|*_".
					"0"."|*_".
					"0"."|*_".
					"0"."|*_".
					$benzersizid
				;
				ekle($sutunlar,$degerler,"urunmarka",35);
				$markaid=teksatir("SELECT markaid FROM urunmarka WHERE benzersizid='".$benzersizid."'","markaid");
			}
			guncelle("marka","$markaid","urunaktar","markaad='".$markaad."'",35);
			
		}unset($urun_marka_t);
	}else{echo "<h1>Marka Aktarımı Tamamlandı</h1><p>Aktarılacak yeni marka yok</p><br><hr><br>";}unset($urun_marka_v);
}else{hatalogisle("Ürün Aktar - Marka",$data->error);}
?>