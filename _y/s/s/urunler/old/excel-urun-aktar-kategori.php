<?php 
function excelkategoriisle($kategori)
{
	global $dilid;
	//üstkategoriler var mı bakalım
	$kategoriler=explode(">",$kategori);
	$kategoritoplam=count($kategoriler);
	if($kategoritoplam==1)
	{
		$kategoriad=trim($kategori);

		//KATEGORİ EKLENMEMİŞSE KATEGORİ EKLE
		if
		(
			!dogrula(
				"kategori",
				"
					kategorisil='0' and 
					kategoriaktif='1' and 
					kategorigrup='7' and 
					kategorikatman = '0' and
					kategoriad='". addslashes($kategoriad)."'
				"
			)
		)
		{
			$sutunlar="
				dilid,
				kategoritariholustur,
				kategoritarihguncel,
				ustkategoriid,
				kategorikatman,
				kategoriad,
				kategorigrup,
				kategoriaktif,
				kategorisil,
				benzersizid
			";

			$simdi=date("Y-m-d H:i:s");$benzersizid=SifreUret(20,2);
			$degerler=
				$dilid."|*_".
				$simdi."|*_".
				$simdi."|*_".
				"0|*_".
				"0|*_"
				.addslashes($kategoriad)."|*_".
				"7|*_".
				"1|*_".
				"0|*_".
				$benzersizid
			;

			ekle($sutunlar,$degerler,"kategori",34);

			$kategoriid=teksatir("
				SELECT 
					kategoriid 
				FROM 
					kategori 
				WHERE 
					benzersizid='". $benzersizid ."'",
				"kategoriid"
			);
			//ürün aktar kategoriid güncelle
			guncelle("kategoriid","$kategoriid","urunaktar","kategoriad='".$kategori."'",34);
			//Yeni kategori seo oluştur
			ekle(
					"benzersizid,baslik,aciklama,kelime,link",
					$benzersizid."|*_".
					addslashes($kategoriad)."|*_".
					addslashes($kategoriad)."|*_".
					addslashes($kategoriad)."|*_".
					"/".addslashes($kategoriad)."/".$kategoriid."m.html",
					"seo",
					34
				);
			echo "<br>Yeni eklenen kategori: $kategoriad <br>";
		}
		else
		{
			//kategori varsa id'sini alalım
			$kategoriid=teksatir("
				SELECT 
					kategoriid 
				FROM 
					kategori 
				WHERE 
					kategoriad='". $kategori ."' and kategorigrup='7' and kategorikatman='0'",
				"kategoriid"
			);
			//ürün aktar kategoriid güncelle
			guncelle("kategoriid","$kategoriid","urunaktar","kategoriad='".$kategori."'",34);
		}
	}
	elseif($kategoritoplam>1)
	{
		//üstkategoriler varsa örn:Erkek>Ayakkabı>Spor Ayakkabı
		$ustkategoriid=0;
		for ($kategorisay=0; $kategorisay < $kategoritoplam; $kategorisay++)
		{
			$kategoriad=$kategoriler[$kategorisay];
			$kategoriad=trim($kategoriad);
            $ustkategoriid=0;
			//kategori eklenmemişse ekle
			if(
				!dogrula(
					"kategori",
					"
						kategorisil='0' and 
						kategoriaktif='1' and 
						kategorigrup='7' and 
						kategorikatman='". $kategorisay ."' and 
						kategoriad='". addslashes($kategoriad)."'
					")
			)
			{

				$sutunlar="
					dilid,
					kategoritariholustur,
					kategoritarihguncel,
					ustkategoriid,
					kategorikatman,
					kategoriad,
					resimid,
					kategorigrup,
					kategoriaktif,
					kategorisil,
					benzersizid
				";
				
				$simdi=date("Y-m-d H:i:s");
				$benzersizid=SifreUret(20,2);

				if($kategorisay>0)
                {
                    $ustkategoriid=teksatir("
					SELECT 
						kategoriid 
					FROM 
						kategori 
					WHERE 
						kategorisil='0' and kategoriaktif='1' and kategoriad='". $kategoriler[$kategorisay-1] ."'",
                        "kategoriid"
                    );
                }
				$degerler=
					$dilid."|*_".
					$simdi."|*_".
					$simdi."|*_".
					"$ustkategoriid|*_".
					$kategorisay."|*_".
					addslashes($kategoriad).
					"|*_".
					"0|*_".
					"7|*_".
					"1|*_".
					"0|*_".
					$benzersizid;

				ekle($sutunlar,$degerler,"kategori","34");
                $kategoriid=teksatir("SELECT kategoriid from kategori where benzersizid='".$benzersizid."'","kategoriid");
				ekle("benzersizid,baslik,aciklama,kelime,link",$benzersizid."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_".addslashes($kategoriad)."|*_"."/".duzelt($kategoriad)."/".$kategoriid."m.html","seo",34);
				
				echo "<br>Yeni eklenen kategori: $kategoriad <br>";
			}
			else
			{
			    $kategoriid=teksatir("SELECT kategoriid from kategori where kategorisil='0' and kategoriaktif='1' and kategoriad='".$kategoriad."'","kategoriid");
				//kategori varsa üstkategori id'yi alalım
                if($kategorisay>0)
                {
                    $ustkategoriid=teksatir("
					SELECT 
						kategoriid 
					FROM 
						kategori 
					WHERE 
						kategorisil='0' and kategoriaktif='1' and kategoriad='". trim($kategoriler[$kategorisay-1]) ."'",
                        "kategoriid"
                    );
                    guncelle("ustkategoriid","$ustkategoriid","kategori","kategoriad='".$kategoriad."'",34);
                }
			}
			if($kategorisay==($kategoritoplam-1))
			{
				guncelle("kategoriid","$kategoriid","urunaktar","kategoriad='".$kategori."'",34);
			}
		}
	}
}
$urunaktarKategori_s="
	SELECT 
		kategoriad
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0 and kategoriad!='' and kategoriad is not null 
	GROUP BY 
		kategoriad
";
if($data->query($urunaktarKategori_s))
{
	$urunaktarKategori_v=$data->query($urunaktarKategori_s);unset($urunaktarKategori_s);
	if($urunaktarKategori_v->num_rows>0)
	{
		echo "<h1>Kategori Aktarımı</h1><br><hr><br>";
		while ($urunaktarKategori_t=$urunaktarKategori_v->fetch_assoc())
		{
			$kategoriad=$urunaktarKategori_t["kategoriad"];
			echo $kategoriad." - ";
			excelkategoriisle($kategoriad);
		}unset($urunaktarKategori_t);
	}else{echo "<h1>Kategori Aktarımı tamamlandı</h1><p>Aktarılacak yeni kategori Yok</p><br><hr><br>";}unset($urunaktarKategori_v);
}else{hatalogisle("Ürün Aktar - Kategori",$data->error);}
?>