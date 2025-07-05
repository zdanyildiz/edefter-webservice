<?php 

$urun_parabirim_s="
	SELECT 
		parabirimad
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0
	GROUP BY 
		parabirimad
";
if($data->query($urun_parabirim_s))
{
	$urun_parabirim_v=$data->query($urun_parabirim_s);unset($urun_parabirim_s);
	if($urun_parabirim_v->num_rows>0)
	{
		echo "<h1>Para Birim Aktarımı</h1><br><hr><br>";
		while ($urun_parabirim_t=$urun_parabirim_v->fetch_assoc())
		{
			$parabirimad=trim($urun_parabirim_t["parabirimad"]);
			echo $parabirimad." - <br>";
			$parabirimid=teksatir("SELECT parabirimid FROM urunparabirim WHERE parabirimsil='0' AND parabirimad='".$parabirimad."'","parabirimid");
			if(S($parabirimid)==0)
			{
				$sutunlar="
					parabirimad,
					parabirimsimge,
					parabirimkod,
					parabirimsil
				";
				$degerler=
                    $parabirimad."|*_".
					"@"."|*_".
                    $parabirimad."|*_".
					"0"
				;
				ekle($sutunlar,$degerler,"urunparabirim",35);
				$parabirimid=teksatir("SELECT parabirimid FROM urunparabirim WHERE parabirimsil='0' AND parabirimkod='".$parabirimad."'","parabirimid");
			}
			guncelle("parabirimid","$parabirimid","urunaktar","parabirimad='".$parabirimad."'",35);
			
		}unset($urun_parabirim_t);
	}else{echo "<h1>Para Birim Aktarımı Tamamlandı</h1><p>Aktarılacak yeni para birimi yok</p><br><hr><br>";}unset($urun_parabirim_v);
}else{hatalogisle("Ürün Aktar - Parabirim",$data->error);}
?>