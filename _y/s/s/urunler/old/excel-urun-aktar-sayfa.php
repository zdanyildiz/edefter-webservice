<?php 
$formhata=0;
$formhataaciklama="";
$urun_sayfa_s="
	SELECT 
		urunid,baslik,aciklama,kategoriid,kategoriad,markaad,fiyat,parabirimad,renkad,malzemead,bedenad,resim,model,video,dosya,renkid
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0
	GROUP BY 
		model,renkid
";
if($data->query($urun_sayfa_s))
{
	$urun_sayfa_v=$data->query($urun_sayfa_s);unset($urun_sayfa_s);
	if($urun_sayfa_v->num_rows>0)
	{
		echo "<h1>Sayfa Aktarımı</h1><br><hr><br>";
		$sayfatip=7;
		$sayfasil=0;
		$sayfalink="";
		$sayfasira=0;
		$sayfaaktif=0;
		$urunsay=0;
		while ($urun_sayfa_t=$urun_sayfa_v->fetch_assoc())
		{
			$urunsay++;
			$baslik=$urun_sayfa_t["baslik"];
			
			if(!BosMu($baslik))
			{
				$simdi=date("Y-m-d H:i:s");
				$sayfatarihguncelle=$simdi;

				$urunid=$urun_sayfa_t["urunid"];
				$aciklama=$urun_sayfa_t["aciklama"];
				$kategoriid=$urun_sayfa_t["kategoriid"];
				$kategoriad=$urun_sayfa_t["kategoriad"];
				$markaad=$urun_sayfa_t["markaad"];
				$fiyat=$urun_sayfa_t["fiyat"];
				$parabirimad=$urun_sayfa_t["parabirimad"];
				$renkad=$urun_sayfa_t["renkad"];
				$malzemead=$urun_sayfa_t["malzemead"];
				$bedenad=$urun_sayfa_t["bedenad"];
				$resim=$urun_sayfa_t["resim"];
				$model=$urun_sayfa_t["model"];
                $video=$urun_sayfa_t["video"];
                $dosya=$urun_sayfa_t["dosya"];
                $renkid=$urun_sayfa_t["renkid"];
                if(!Bosmu($video))
                {
                    $video_ayir=explode(",",$video);
                    foreach ($video_ayir as $vid)
                    {
                        $aciklama=$aciklama.'<iframe width="700" height="450" src="https://www.youtube.com/embed/'.$vid.'" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" class="youtubevideo"></iframe> ';
                    }
                }

                /*
                if(!BosMu($dosya))$aciklama=$aciklama.'<br><a href="https://www.alhan-cagri.com/dosyalar/'.$dosya.'" target="_blank" class="katalog">Kataloğu indirmek için tıklayın</a>';
                $aciklama=$aciklama.'
                    <a href="https://www.alhan-cagri.com/1/kategori/4597/sertifikalar.html" target="_blank" class="sertifikalar">Sertifikalarımızı görmek için tıklayın</a>
                ';
                */

				$sayfaekle_sutunlar="
					sayfatarihguncel,
					sayfatip,
					sayfaad,
					sayfaicerik,
					sayfalink,
					sayfasira,
					sayfaaktif,
					sayfasil
				";
				$sayfaekle_degerler=
					$sayfatarihguncelle."|*_".
					$sayfatip."|*_".
					$baslik."|*_".
					$aciklama."|*_".
					$sayfalink."|*_".
					$sayfasira."|*_".
					$sayfaaktif."|*_".
					$sayfasil
				;
				if(S($urunid)==0)
				{
					//sayfa ilk defa ekleniyor
					$benzersizid=SifreUret(20,2);
					$sayfatariholustur=$simdi;
					$sayfaekle_sutunlar="benzersizid,sayfatariholustur,$sayfaekle_sutunlar";
					$sayfaekle_degerler="$benzersizid|*_$sayfatariholustur|*_$sayfaekle_degerler";
					//die($sayfaekle_degerler);
					ekle($sayfaekle_sutunlar,$sayfaekle_degerler,"sayfa",0);
					if($formhata==1)die($formhataaciklama);
					$urunid=teksatir("SELECT sayfaid FROM sayfa WHERE benzersizid='".$benzersizid."'","sayfaid");
					if($formhata==1)die($formhataaciklama);
                    if($urunsay==1)echo '<br>ekleniyor..<br>lütfen bekleyiniz';
				}
				else
				{
					//ürün daha önceden eklenmiş güncelleme yapılacak
					guncelle($sayfaekle_sutunlar,$sayfaekle_degerler,"sayfa","sayfaid='".$urunid."'",0);
					if($formhata==1)die($formhataaciklama);
					//ürün daha önceden bir kategori ile ilişkilendirilmişse kaldıralım
					sil("sayfalistekategori","sayfaid='".$urunid."'",0);
					if($formhata==1)die($formhataaciklama);
					//seo için benzersizid alalım
					$benzersizid=teksatir("SELECT benzersizid FROM sayfa WHERE sayfaid='".$urunid."'","benzersizid");
					if($formhata==1)die($formhataaciklama);
                    if($urunsay==1)echo '<br>güncelleniyor..<br>lütfen bekleyiniz';
				}
				//modele göre ilgili modellerin hangi ürüne ait olduğunu güncelle
				guncelle("urunid","$urunid","urunaktar","model='".$model."' and renkid='".$renkid."' ",0);
				if($formhata==1)die($formhataaciklama);
				//ürün ile kategori ilişkilendir
				ekle("kategoriid,sayfaid","$kategoriid|*_$urunid","sayfalistekategori",0);
				if($formhata==1)die($formhataaciklama);
				//var olan seo yapısını sil yeni oluştur
				sil("seo","benzersizid='".$benzersizid."'",0);
				if($formhata==1)die($formhataaciklama);

				$seolink=str_replace(">", "/", $kategoriad);
				$seolink=TR(K($seolink))."/".TR(K($baslik));

				$seolink=str_replace(" ", "-", $seolink);
				$seolink=$seolink."/".$urunid."s.html";

				$seobaslik=$baslik;
				$seoaciklama="Kategori: $kategoriad, Marka: $markaad, Model: $model, Renk: $renkad, Ölçü: $bedenad, Malzeme: $malzemead, Fiyat: $fiyat $parabirimad, Ürün: $baslik";
				$seokelime="$kategoriad,$markaad,$renkad,$model,$bedenad,$malzemead";

				ekle("benzersizid,baslik,aciklama,kelime,link,orjinallink,resim","$benzersizid|*_$seobaslik|*_$seoaciklama|*_$seokelime|*_$seolink|*_|*_","seo",0);
				if($formhata==1)die($formhataaciklama);

				//resimleri sayfalisteresim tablosuna ekleyerek ürünlerle ilişkilendirelim
				if(!BosMu($resim))
				{
					sil("sayfalisteresim","sayfaid='".$urunid."'",0);
					$resimayikla=explode(",", $resim);
 					foreach ($resimayikla as $ri => $urunresim)
 					{
 						if(S($urunresim)!=0)
 						{
 							ekle("sayfaid,resimid","$urunid|*_$urunresim","sayfalisteresim",0);
 							if($formhata==1)die($formhataaciklama);
 						}
 					}
				}
                guncelle("aktarimonay",1,"urunaktar","model='".$model."' and renkid='".$renkid."' ",0);
				if($urunsay==100)die('<script>window.location.href ="/_y/s/s/urunler/excel-urun-yukle.php?adim=10";</script>');
			}
			else
			{
				echo "Sayfa Adı / Ürün Başlığı boş olamaz<br>";
                guncelle("aktarimonay",3,"urunaktar","model='".$model."' and renkid='".$renkid."' ",0);
			}
		}echo "<h2>Sayfa Aktarımı Tamamlandı</h2><p>Ürün özellik ekleme adımına devam edin</p><br><hr><br>";unset($urun_sayfa_t);
	}else{echo "<h1>Sayfa Aktarımı Tamamlandı</h1><p>Aktarılacak yeni sayfa yok</p><br><hr><br>";}unset($urun_sayfa_v);
}else{hatalogisle("Ürün Aktar - Sayfa",$data->error);}
?>