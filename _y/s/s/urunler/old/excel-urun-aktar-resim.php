<?php 

$urun_resim_s="
	SELECT 
		resimklasor,baslik,resim,aktarimid
	FROM 
		urunaktar
	WHERE 
		aktarimonay=0 and resimaktar='0' and (resim!='' or resimklasor!='')
	LIMIT 1
";
if($data->query($urun_resim_s))
{
	$urun_resim_v=$data->query($urun_resim_s);unset($urun_resim_s);
	if($urun_resim_v->num_rows>0)
	{
		echo "<h1>Resim Aktarımı</h1><br><hr><br>";
		$i=0;
		while ($urun_resim_t=$urun_resim_v->fetch_assoc())
		{
            $i++;$resimklasor="";$resimler="";$urunaktar_resimler="";
            $aktarimid=$urun_resim_t["aktarimid"];
            $resimklasor=$urun_resim_t["resimklasor"];
            $resimler=$urun_resim_t["resim"];
            $resimler=trim($resimler,",");
            //die("1: $resimler");
			if(!BosMu($resimklasor)&&BosMu($resimler))
			{
				$baslik=$urun_resim_t["baslik"];
	 			
	 			//adı 1 olan resim varsa ilk resmimiz olacak
				if($anadizin.'/m/r/urun/'.$resimklasor."/1.jpg")
				{
					$resimler="1.jpg";
				}
				elseif($anadizin.'/m/r/urun/'.$resimklasor."/1.jpeg")
				{
					$resimler="1.jpeg";
				}
				elseif($anadizin.'/m/r/urun/'.$resimklasor."/1.png")
				{
					$resimler="1.png";
				}
				elseif($anadizin.'/m/r/urun/'.$resimklasor."/1.gif")
				{
					$resimler="1.gif";
				}
				elseif($anadizin.'/m/r/urun/'.$resimklasor."/1.JPG")
				{
					$resimler="1.JPG";
				}
				elseif($anadizin.'/m/r/urun/'.$resimklasor."/1.JPEG")
				{
					$resimler="1.JPEG";
				}
				elseif($anadizin.'/m/r/urun/'.$resimklasor."/1.PNG")
				{
					$resimler="1.PNG";
				}
				elseif($anadizin.'/m/r/urun/'.$resimklasor."/1.GIF")
				{
					$resimler="1.GIF";
				}
				
				$files = glob($anadizin.'/m/r/urun/'.$resimklasor.'/*.{jpg,png,gif,jpeg,JPG,PNG,JPEG}', GLOB_BRACE);
				foreach($files as $file)
				{
					$file=str_replace($anadizin.'/m/r/urun/'.$resimklasor."/", "", $file);
					if(
					  	
				  		$file !='1.jpg'  &&
					  	$file !='1.jpeg' &&
					  	$file !='1.png'  &&
					  	$file !='1.gif'  &&
					  	$file !='1.JPG'  &&
					  	$file !='1.JPEG' &&
					  	$file !='1.PNG'  &&
					  	$file !='1.GIF'
						  
					)
					{
						$resimler=$resimler.','.$file;
					}
				}
				$resimler=trim($resimler,",");
				//echo $i.") $resimklasor / ".$resimler."<br>";
				
				if(!BosMu($resimler))
	 			{
	 				$resimayikla=explode(",", $resimler);
	 				foreach ($resimayikla as $ri => $resim)
	 				{
	 					//resmin orjinal klasör ve resim adına göre resim daha önceden eklenmiş mi bakalım
	 					if(!Dogrula("resim","orjinal='".$resimklasor."-".$resim."'"))
		 				{
		 					//resmi klasörde doğrulayalım
		 					if(file_exists($anadizin.'/m/r/urun/'.$resimklasor.'/'.$resim))
		 					{
								//resim daha önce kaydedilmemişse yeniden adlandıralım
								$rbenzersizid=SifreUret(5,2);
			 					$yeniresim = Duzelt($baslik."-".$ri."-".$rbenzersizid."-".$resim);
			 					
			 					//resmi önceki klasöründen urunler tablosuna yeni adıyla kopyalayalım
			 					copy($anadizin.'/m/r/urun/'.$resimklasor.'/'.$resim, $anadizin.'/m/r/urun/'.$yeniresim);
			 					//resim kopyalanmış mı kontrol edelim
			 					if(file_exists($anadizin . "/m/r/urun/".$yeniresim))
		 						{
									//resmi veritabanına ekleyelim
									$sutunlar="";$degerler="";
				 					$benzersizid=SifreUret(20,2);
				 					$sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
				 					$degerler="2|*_".addslashes($baslik)."|*_".$yeniresim."|*_0|*_0|*_".$benzersizid."|*_".$resimklasor."-".$resim;

									ekle($sutunlar,$degerler,"resim",26);
									if($formhata==1)die($formhataaciklama);
									//eklediğimiz resmin id'sini alalım ki sayfaid'sine göre ürünle eşleştirelim
				 					$resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$benzersizid."'","resimid");
				 					if($formhata==1)die($formhataaciklama);
				 					echo "Resim ($resim) Eklendi ResimID: $resimid<br>";
				 					$urunaktar_resimler=$urunaktar_resimler.",".$resimid;
		 						}else{echo "resim taşınamamış ama bulunamadı $resim - $yeniresim <br>";}
		 					}
			 				else{echo "resim orijinal klasöründe bulunamadı $resim <br>";}
		 				}
		 				else
	 					{
	 						//resim daha önce eklenmişse ürün ile eşleştirmek için resminid'sini alalım
	 						$resimid=teksatir("SELECT resimid FROM resim WHERE orjinal='".$resimklasor."-".$resim."'","resimid");
	 						if($formhata==1)die($formhataaciklama);
	 						//resmin dosya adını öğrenelim
	 						$resim=teksatir("SELECT resim FROM resim WHERE orjinal='".$resimklasor."-".$resim."'","resim");
	 						if($formhata==1)die($formhataaciklama);
	 						//böyle bir resim urun klasöründe var mı kontrol edelim
	 						if(file_exists($anadizin . "/m/r/urun/".$resim))
	 						{
	 							$urunaktar_resimler=$urunaktar_resimler.",".$resimid;
	 						}
	 						else
	 						{
	 							//resim urunler klasöründen kaldırılmışsa olası tüm tablolardan kaldıralım
	 							Sil("sayfalisteresim","resimid='". $resimid ."'");
								Sil("resimgaleriliste","resimid='". $resimid ."'");
								$data->query("UPDATE kategori SET resimid='0' WHERE resimid='". $resimid ."'");
								Sil("resim","resimid='". $resimid ."'");
	 						}
	 					}
	 				}
	 				$urunaktar_resimler=trim($urunaktar_resimler,",");
	 				//resim sütununa $urunaktar_resimler değişkenini ekleyerek urunaktar tablosunda güncelleyelim
	 				guncelle("resim","$urunaktar_resimler","urunaktar","resimklasor='".$resimklasor."'",65);
                    guncelle("resimaktar",1,"urunaktar","aktarimid='".$aktarimid."'",65);
                    if($i==1)die('<script>window.location.href ="/_y/s/s/urunler/excel-urun-yukle.php?adim=9";</script>');
	 			}
			}
			elseif(!BosMu($resimler))
            {
                $baslik=$urun_resim_t["baslik"];
                //echo $i.") $resimklasor / ".$resimler."<br>";
                $resimayikla=explode(",",$resimler);

                foreach ($resimayikla as $ri => $resim)
                {
                    //resmin orjinal klasör ve resim adına göre resim daha önceden eklenmiş mi bakalım
                    if(!Dogrula("resim","orjinal='".$resimklasor."-".$resim."'"))
                    {
                        //resmi uzakta doğrulayalım
                        $resim=str_replace(" ","%20",$resim);
                        $imgurl=$resimklasor.$resim;
                        if(substr($resim,-4)=="html")
                        {
                            $imgsize="";
                        }
                        else
                        {
                            $imgsize=@getimagesize($resimklasor.$resim);
                        }

                        if(!is_array($imgsize))
                        {
                            // resim yok
                            echo "resim orijinal klasöründe bulunamadı $resim <br>";
                        }
                        else
                        {
                            //resim daha önce kaydedilmemişse yeniden adlandıralım
                            $rbenzersizid=SifreUret(5,2);
                            $yeniresim = Duzelt($baslik."-".$ri."-".$rbenzersizid."-".$resim);
                            //die($imgurl."<br>".$anadizin.'/m/r/urun/'.$yeniresim);
                            //resmi önceki klasöründen urunler tablosuna yeni adıyla kopyalayalım
                            copy($imgurl, $anadizin.'/m/r/urun/'.$yeniresim);
                            //resim kopyalanmış mı kontrol edelim
                            if(file_exists($anadizin . "/m/r/urun/".$yeniresim))
                            {
                                //resmi veritabanına ekleyelim
                                $sutunlar="";$degerler="";
                                $benzersizid=SifreUret(20,2);
                                list($width, $height) = getimagesize($anadizin . "/m/r/urun/".$yeniresim);
                                $sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
                                $degerler="2|*_".addslashes($baslik)."|*_".$yeniresim."|*_".S($width)."|*_".S($height)."|*_".$benzersizid."|*_".$resimklasor."-".$resim;

                                ekle($sutunlar,$degerler,"resim",26);
                                if($formhata==1)die($formhataaciklama);
                                //eklediğimiz resmin id'sini alalım ki sayfaid'sine göre ürünle eşleştirelim
                                $resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$benzersizid."'","resimid");
                                if($formhata==1)die($formhataaciklama);
                                echo "Resim ($resim) Eklendi ResimID: $resimid<br>";
                                $urunaktar_resimler=$urunaktar_resimler.",".$resimid;
                            }else{echo "resim taşınamamış ama bulunamadı $resim - $yeniresim <br>";}
                        }
                    }
                    else
                    {
                        //resim daha önce eklenmişse ürün ile eşleştirmek için resminid'sini alalım
                        $resimBilgileri=coksatir("SELECT resimid,resim FROM resim WHERE orjinal='".$resimklasor."-".$resim."'");
                        if($formhata==1)die($formhataaciklama);
                        //resmin dosya adını öğrenelim
                        if($formhata==1)die($formhataaciklama);
                        //böyle bir resim urun klasöründe var mı kontrol edelim
                        if(file_exists($anadizin . "/m/r/urun/".$resimBilgileri["resim"]))
                        {
                            $urunaktar_resimler=$urunaktar_resimler.",".$resimBilgileri["resimid"];
                        }
                        else
                        {
                            //resim urunler klasöründen kaldırılmışsa olası tüm tablolardan kaldıralım
                            Sil("sayfalisteresim","resimid=". $resimBilgileri["resimid"],0);
                            Sil("resimgaleriliste","resimid=". $resimBilgileri["resimid"],0);
                            $data->query("UPDATE kategori SET resimid=0 WHERE resimid=". $resimBilgileri["resimid"]);
                            Sil("resim","resimid=". $resimBilgileri["resimid"],0);
                        }
                    }
                }
                $urunaktar_resimler=trim($urunaktar_resimler,",");
                //resim sütununa $urunaktar_resimler değişkenini ekleyerek urunaktar tablosunda güncelleyelim
                guncelle("resim","$urunaktar_resimler","urunaktar","aktarimid='".$aktarimid."'",65);
                guncelle("resimaktar",1,"urunaktar","aktarimid='".$aktarimid."'",65);
                if($i==1)die('<script>window.location.href ="/_y/s/s/urunler/excel-urun-yukle.php?adim=9";</script>');
            }
		}echo "<h2>Aktarımı Tamamlandı</h2><p>Sayfa ekleme adımına devam edin</p><br><hr><br>";unset($urun_resim_t);
	}else{echo "<h1>Resim Aktarımı Tamamlandı</h1><p>Aktarılacak yeni resim yok</p><br><hr><br>";}unset($urun_resim_v);
}else{hatalogisle("Ürün Aktar - Resim",$data->error);}
?>