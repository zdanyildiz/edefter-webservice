<?php
if(S($f_sayfaekle)==1 && $formhata==0)
{
    if(f("kargo"))
    {
        foreach(f("kargo") as $f_kargo)
        {
            $f_kargo.=",";
        }
        $f_kargo=trim($f_kargo,",");
    }
	if( S(f("beden"))!=0  || S(f("renk"))!=0 || S(f("malzeme"))!=0 )
	{
		if(
			!empty($_POST['urunsatisfiyat_varyant']) &&
			!empty($_POST['urunindirimsizfiyat_varyant']) &&
			!empty($_POST['urunbayifiyat_varyant']) &&
			!empty($_POST['urunstok_varyant']) &&
			!empty($_POST['urunstokkodu_varyant'])
		)
		{
			$urunsatisfiyat_varyantlar="";
			$urunindirimsizfiyat_varyantlar="";
			$urunbayifiyat_varyantlar="";
			$urunalisfiyat_varyantlar="";
			$urunstok_varyantlar="";
			$urunstokkodu_varyantlar="";
			$urunbeden_varyantlar="";
			$urunrenk_varyantlar="";
            $urunmalzeme_varyantlar="";
            $urunpin_varyantlar="";

			foreach($_POST['urunsatisfiyat_varyant'] as $urunsatisfiyat_varyant)
			{
		    	if(BosMu($urunsatisfiyat_varyantlar)){$urunsatisfiyat_varyantlar=$urunsatisfiyat_varyant;}else{$urunsatisfiyat_varyantlar=$urunsatisfiyat_varyantlar.",".$urunsatisfiyat_varyant;}
		    }
		    foreach($_POST['urunindirimsizfiyat_varyant'] as $urunindirimsizfiyat_varyant)
			{
		    	if(BosMu($urunindirimsizfiyat_varyantlar)){$urunindirimsizfiyat_varyantlar=$urunindirimsizfiyat_varyant;}else{$urunindirimsizfiyat_varyantlar=$urunindirimsizfiyat_varyantlar.",".$urunindirimsizfiyat_varyant;}
		    }
		    foreach($_POST['urunbayifiyat_varyant'] as $urunbayifiyat_varyant)
			{
		    	if(BosMu($urunbayifiyat_varyantlar)){$urunbayifiyat_varyantlar=$urunbayifiyat_varyant;}else{$urunbayifiyat_varyantlar=$urunbayifiyat_varyantlar.",".$urunbayifiyat_varyant;}
		    }
		    foreach($_POST['urunalisfiyat_varyant'] as $urunalisfiyat_varyant)
			{
		    	if(BosMu($urunalisfiyat_varyantlar)){$urunalisfiyat_varyantlar=$urunalisfiyat_varyant;}else{$urunalisfiyat_varyantlar=$urunalisfiyat_varyantlar.",".$urunalisfiyat_varyant;}
		    }
		    foreach($_POST['urunstok_varyant'] as $urunstok_varyant)
			{
		    	if(BosMu($urunstok_varyantlar)){$urunstok_varyantlar=$urunstok_varyant;}else{$urunstok_varyantlar=$urunstok_varyantlar.",".$urunstok_varyant;}
		    }
		    foreach($_POST['urunstokkodu_varyant'] as $urunstokkodu_varyant)
			{
		    	if(BosMu($urunstokkodu_varyantlar)){$urunstokkodu_varyantlar=$urunstokkodu_varyant;}else{$urunstokkodu_varyantlar=$urunstokkodu_varyantlar.",".$urunstokkodu_varyant;}
		    }
		    if(isset($_POST['urunbedenlerid']))
		    {
			    foreach($_POST['urunbedenlerid'] as $urunbedenler)
				{
			    	if(BosMu($urunbeden_varyantlar)){$urunbeden_varyantlar=$urunbedenler;}else{$urunbeden_varyantlar=$urunbeden_varyantlar.",".$urunbedenler;}
			    }
			}
		    if(isset($_POST['urunrenklerid']))
		    {
		    	foreach($_POST['urunrenklerid'] as $urunrenkler)
				{
			    	if(BosMu($urunrenk_varyantlar)){$urunrenk_varyantlar=$urunrenkler;}else{$urunrenk_varyantlar=$urunrenk_varyantlar.",".$urunrenkler;}
			    }
		    }
            if(isset($_POST['urunmalzemelerid']))
            {
                foreach($_POST['urunmalzemelerid'] as $urunmalzemeler)
                {
                    if(BosMu($urunmalzeme_varyantlar)){$urunmalzeme_varyantlar=$urunmalzemeler;}else{$urunmalzeme_varyantlar=$urunmalzeme_varyantlar.",".$urunmalzemeler;}
                }
            }
            if(isset($_POST['urunpinlerid']))
            {
                foreach($_POST['urunpinlerid'] as $urunpinler)
                {
                    if(BosMu($urunpin_varyantlar)){$urunpin_varyantlar=$urunpinler;}else{$urunpin_varyantlar=$urunpin_varyantlar.",".$urunpinler;}
                }
            }
		}
		else
		{
			$formhata=1;$formhataaciklama="Varyasyon seçili ise Varyant değerleri boş olamaz";
		}
	}
	elseif(f("beden")==0 && f("renk")==0 && f("malzeme")==0 && f("pin")==0 )
	{
		foreach($_POST['urunsatisfiyat_varyant'] as $urunsatisfiyat_varyant)
		{
			$f_urunsatisfiyat=$urunsatisfiyat_varyant;
		}
		foreach($_POST['urunindirimsizfiyat_varyant'] as $urunindirimsizfiyat_varyant)
		{
			$f_urunindirimsizfiyat=$urunindirimsizfiyat_varyant;
		}
		foreach($_POST['urunalisfiyat_varyant'] as $urunalisfiyat_varyant)
		{
			$f_urunalisfiyat=$urunalisfiyat_varyant;
		}
		foreach($_POST['urunbayifiyat_varyant'] as $urunbayifiyat_varyant)
		{
			$f_urunbayifiyat=$urunbayifiyat_varyant;
		}
		foreach($_POST['urunstok_varyant'] as $urunstok_varyant)
		{
			$f_urunstok=$urunstok_varyant;
		}
		foreach($_POST['urunstokkodu_varyant'] as $urunstokkodu_varyant)
		{
			$f_urunstokkodu=$urunstokkodu_varyant;
		}
		if(BosMu($f_urunsatisfiyat))$f_urunsatisfiyat="0.00";else $f_urunsatisfiyat=str_replace(",", ".", $f_urunsatisfiyat);
		if(BosMu($f_urunindirimsizfiyat))$f_urunindirimsizfiyat="0.00";else $f_urunindirimsizfiyat=str_replace(",", ".", $f_urunindirimsizfiyat);
		if(BosMu($f_urunalisfiyat))$f_urunalisfiyat="0.00";else $f_urunalisfiyat=str_replace(",", ".", $f_urunalisfiyat);
		if(BosMu($f_urunbayifiyat))$f_urunbayifiyat="0.00";else $f_urunbayifiyat=str_replace(",", ".", $f_urunbayifiyat);
	}
	if($formhata==0)
	{
		$simdi=date("Y-m-d H:i:s");

		if(S($f_sayfaid)!=0)
		{
			$sayfaekle_s=
			"
				UPDATE
					sayfa
				SET
					benzersizid				= :f_benzersizid,
					sayfatarihguncel		= :simdi,
					sayfatip				= :f_sayfagrup,
					sayfaad					= :f_sayfaad,
					sayfaicerik				= :f_sayfaicerik,
					sayfalink				= :f_sayfalink,
					sayfasira				= :f_sayfasira,
					sayfaaktif				= :f_sayfaaktif,
					sayfasil				= '0'
				WHERE 
					sayfaID					='".$f_sayfaid."'
			";
			$eylem=3;$formad="Sayfa Güncelle";
		}
		else
		{
			$sayfaekle_s = "
				INSERT INTO sayfa 
				(
					benzersizid,
					sayfatariholustur,
					sayfatarihguncel,
					sayfatip,
					sayfaad,
					sayfaicerik,
					sayfalink,
					sayfasira,
					sayfaaktif,
					sayfasil
				)
				VALUES 
				(
					:f_benzersizid,
					:simdi,
					:simdi,
					:f_sayfagrup,
					:f_sayfaad,
					:f_sayfaicerik,
					:f_sayfalink,
					:f_sayfasira,
					:f_sayfaaktif,
					'0'
				)
				";
			$eylem=1;$formad="Sayfa Ekle";
		}

		$params = array(
			':f_benzersizid'	=> $f_benzersizid,
			':simdi'			=> $simdi,
			':f_sayfagrup'		=> $f_sayfagrup,
			':f_sayfaad'		=> $f_sayfaad,
			':f_sayfaicerik'	=> $f_sayfaicerik,
			':f_sayfalink'		=> $f_sayfalink,
			':f_sayfasira'		=> $f_sayfasira,
			':f_sayfaaktif'		=> $f_sayfaaktif
		);

		$db->begintransaction();

		if(S($f_sayfaid)!=0){
			if($pageStatus = $db->update($sayfaekle_s,$params)){
				$db->commit();

				$formhataaciklama="Sayfa güncellendi";
				$db->delete("DELETE FROM sayfalistekategori WHERE sayfaid=:f_sayfaid",[":f_sayfaid"=>$f_sayfaid]);
				$db->delete("DELETE FROM sayfalisteresim WHERE sayfaid=:f_sayfaid",[":f_sayfaid"=>$f_sayfaid]);
				$db->delete("DELETE FROM urunozellikleri WHERE sayfaid=:f_sayfaid",[":f_sayfaid"=>$f_sayfaid]);
				$db->delete("DELETE FROM seo WHERE benzersizid=:f_benzersizid",[":f_benzersizid"=>$f_benzersizid]);

			}else{
				$db->rollback();
			}
		}
		else{
			if($pageStatus = $db->insert($sayfaekle_s,$params)){
				$db->commit();
				$formhataaciklama="Yeni sayfa eklendi";
				$f_sayfaid=teksatir(" Select sayfaid from sayfa Where benzersizid='". $f_benzersizid ."'","sayfaid");
			}else{
				$db->rollback();
			}
		}

		if($pageStatus >0 )
		{

			yoneticiislemleri(24,$eylem);

			if(S(f("beden"))==0 && S(f("renk"))==0 && S(f("malzeme"))==0 )
			{
				$urunsutunlar = "
					sayfaid,
					urungrupid,
					markaid,
					tedarikciid,
					urunhediye,
					urunaciklama,
					urunkargosuresi,
					urunsabitkargoucreti,
					urunindirimsizfiyat,
					urunsatisfiyat,
					urunbayifiyat,
					urunalisfiyat,
					uruneskifiyatgoster,
					uruntaksit,
					urunkdv,
					urunstok,
					urunstokkodu,
					urunmodel,
					urunbedengrupid,
					urunbedenid,
					urunrenkgrupid,
					urunrenkid,
					urunmalzemegrupid,
					urunmalzemeid,
					urunpinid,
					urunindirimorani,
					urunfiyatsontarih,
					urunanasayfa,
					urunindirimde,
					urunyeni,
					uruntopluindirim,
					urunanindakargo,
					urunucretsizkargo,
					urunonsiparis,
					urunfiyatsor,
					urunkargo,
					urunparabirim,
					urungununfirsati,							
					urunkredikarti,
					urunkapidaodeme,
					urunhavaleodeme,
					urunsatisadet,
					urunindirimoranigoster,
					urundesi,
					urunminimummiktar,
					urunmaksimummiktar,
					urunkatsayi,
					urunmiktarbirimid
					";
				$urundegerler =
					$f_sayfaid."|*_".
					$f_urungrupid."|*_".
					$f_markaid."|*_".
					$f_tedarikciid."|*_".
					$f_urunhediye."|*_".
					$f_urunaciklama."|*_".
					$f_urunkargosuresi."|*_".
					$f_urunsabitkargoucreti."|*_".
					$f_urunindirimsizfiyat."|*_".
					$f_urunsatisfiyat."|*_".
					$f_urunbayifiyat."|*_".
					$f_urunalisfiyat."|*_".
					$f_uruneskifiyatgoster."|*_".
					$f_uruntaksit."|*_".
					$f_urunkdv."|*_".
					$f_urunstok."|*_".
					$f_urunstokkodu."|*_".
					$f_urunmodel."|*_".
					"0|*_".
					"0|*_".
					"0|*_".
					$f_urunrenkid."|*_".
                    "0|*_".
                    "0|*_".
                    "0|*_".
					$f_urunindirimorani."|*_".
					$f_urunfiyatsontarih."|*_".
					$f_urunanasayfa."|*_".
					$f_urunindirimde."|*_".
					$f_urunyeni."|*_".
					$f_uruntopluindirim."|*_".
					$f_urunanindakargo."|*_".
					$f_urunucretsizkargo."|*_".
					$f_urunonsiparis."|*_".
					$f_urunfiyatsor."|*_".
					$f_kargo."|*_".
					$f_urunparabirim."|*_".
					$f_urungununfirsati."|*_".
					$f_urunkredikarti."|*_".
					$f_urunkapidaodeme."|*_".
					$f_urunhavaleodeme."|*_".
					$f_urunsatisadet."|*_".
					$f_urunindirimoranigoster."|*_".
                    $f_urundesi."|*_".
                    $f_urunminimummiktar."|*_".
                    $f_urunmaksimummiktar."|*_".
                    $f_urunkatsayi."|*_".
					$f_urunmiktarbirimid
					;

				ekle($urunsutunlar,$urundegerler,"urunozellikleri",58);
			}
			else
			{
				$i=0;
				$urunsatisfiyatayir 		= explode(',',$urunsatisfiyat_varyantlar);
				$urunindirimsizfiyatayir 	= explode(',',$urunindirimsizfiyat_varyantlar);
				$urunbayifiyatayir 			= explode(',',$urunbayifiyat_varyantlar);
				$urunalisfiyatayir 			= explode(',',$urunalisfiyat_varyantlar);
				$urunstokayir 				= explode(',',$urunstok_varyantlar);
				$urunstokkoduayir 			= explode(',',$urunstokkodu_varyantlar);
				$urunbedenidayir 			= explode(',',$urunbeden_varyantlar);
				$urunrenkidayir 			= explode(',',$urunrenk_varyantlar);
                $urunmalzemeidayir 			= explode(',',$urunmalzeme_varyantlar);
                $urunpinidayir 			    = explode(',',$urunpin_varyantlar);

				foreach($urunsatisfiyatayir as $f_urunsatisfiyat)
				{
					$f_urunindirimsizfiyat=$urunindirimsizfiyatayir[$i];
					$f_urunbayifiyat=$urunbayifiyatayir[$i];
					$f_urunalisfiyat=$urunalisfiyatayir[$i];
					$f_urunstok=S($urunstokayir[$i]);
					$f_urunstokkodu=$urunstokkoduayir[$i];
					$f_urunbedenid=S($urunbedenidayir[$i]);
                    $f_urunpinid=S($urunpinidayir[$i]);
                    if(S($f_urunbedenid)!=0)$f_urunbedengrupid=teksatir("SELECT urunbedengrupid FROM urunbeden WHERE urunbedenid='".$f_urunbedenid."'","urunbedengrupid");
					$f_urunrenkid=S($urunrenkidayir[$i]);
                    if(S($f_urunrenkid)!=0)$f_urunrenkgrupid=teksatir("SELECT urunrenkgrupid FROM urunrenk WHERE urunrenkid='".$f_urunrenkid."'","urunrenkgrupid");
                    $f_urunmalzemeid=S($urunmalzemeidayir[$i]);
                    if(S($f_urunmalzemeid)!=0)$f_urunmalzemegrupid=teksatir("SELECT urunmalzemegrupid FROM urunmalzeme WHERE urunmalzemeid='".$f_urunmalzemeid."'","urunmalzemegrupid");
                    //die($f_urunindirimsizfiyat);
					$urunsutunlar = "
						sayfaid,
						urungrupid,
						markaid,
						tedarikciid,
						urunhediye,
						urunaciklama,
						urunkargosuresi,
						urunsabitkargoucreti,
						urunindirimsizfiyat,
						urunsatisfiyat,
						urunbayifiyat,
						urunalisfiyat,
						uruneskifiyatgoster,
						uruntaksit,
						urunkdv,
						urunstok,
						urunstokkodu,
						urunmodel,
						urunbedengrupid,
						urunbedenid,
						urunrenkgrupid,
						urunrenkid,
						urunmalzemegrupid,
						urunmalzemeid,
						urunpinid,
						urunindirimorani,
						urunfiyatsontarih,
						urunanasayfa,
						urunindirimde,
						urunyeni,
						uruntopluindirim,
						urunanindakargo,
						urunucretsizkargo,
						urunonsiparis,
						urunfiyatsor,
						urunkargo,
						urunparabirim,
						urungununfirsati,							
						urunkredikarti,
						urunkapidaodeme,
						urunhavaleodeme,
						urunsatisadet,
						urunindirimoranigoster,
                        urundesi,
                        urunminimummiktar,
                        urunmaksimummiktar,
                        urunkatsayi,
                        urunmiktarbirimid
					";
					$urundegerler =
						$f_sayfaid."|*_".
						$f_urungrupid."|*_".
						$f_markaid."|*_".
						$f_tedarikciid."|*_".
						$f_urunhediye."|*_".
						$f_urunaciklama."|*_".
						$f_urunkargosuresi."|*_".
						$f_urunsabitkargoucreti."|*_".
						$f_urunindirimsizfiyat."|*_".
						$f_urunsatisfiyat."|*_".
						$f_urunbayifiyat."|*_".
						$f_urunalisfiyat."|*_".
						$f_uruneskifiyatgoster."|*_".
						$f_uruntaksit."|*_".
						$f_urunkdv."|*_".
						$f_urunstok."|*_".
						$f_urunstokkodu."|*_".
						$f_urunmodel."|*_".
						$f_urunbedengrupid."|*_".
						$f_urunbedenid."|*_".
						$f_urunrenkgrupid."|*_".
						$f_urunrenkid."|*_".
                        $f_urunmalzemegrupid."|*_".
                        $f_urunmalzemeid."|*_".
                        $f_urunpinid."|*_".
						$f_urunindirimorani."|*_".
						$f_urunfiyatsontarih."|*_".
						$f_urunanasayfa."|*_".
						$f_urunindirimde."|*_".
						$f_urunyeni."|*_".
						$f_uruntopluindirim."|*_".
						$f_urunanindakargo."|*_".
						$f_urunucretsizkargo."|*_".
						$f_urunonsiparis."|*_".
						$f_urunfiyatsor."|*_".
						$f_kargo."|*_".
						$f_urunparabirim."|*_".
						$f_urungununfirsati."|*_".
						$f_urunkredikarti."|*_".
						$f_urunkapidaodeme."|*_".
						$f_urunhavaleodeme."|*_".
						$f_urunsatisadet."|*_".
						$f_urunindirimoranigoster."|*_".
                        $f_urundesi."|*_".
                        $f_urunminimummiktar."|*_".
                        $f_urunmaksimummiktar."|*_".
                        $f_urunkatsayi."|*_".
						$f_urunmiktarbirimid
						;
					ekle($urunsutunlar,$urundegerler,"urunozellikleri",58);
					$i++;
				}
			}

            $seoresim="";
            if(f("resimid"))
            {
                foreach(f("resimid") as $ekleresim)
                {
                    ekle("sayfaid,resimid",$f_sayfaid."|*_".$ekleresim,"sayfalisteresim",57);
                    $resim=teksatir(" Select resim from resim Where resimid='". $ekleresim ."'","resim");
                    $seoresim.=","."/Public/Image/".$resim;$resim="";
					$seoresim = trim($seoresim,",");
                }
            }
			if(!BosMu($f_kategoriid))
			{
				$f_kategoriid=rtrim($f_kategoriid,",");

				$f_kategoriler = explode(",", $f_kategoriid);
				foreach($f_kategoriler as $eklekategori)
				{
					ekle("sayfaid,kategoriid",$f_sayfaid."|*_".$eklekategori,"sayfalistekategori",56);
					$kategoriad=teksatir(" Select kategoriad from kategori Where kategoriid='". $eklekategori ."'","kategoriad");
					kategorial($eklekategori);
				}
			}
			$sutunlar="benzersizid,
			baslik,
			aciklama,
			kelime,
			link,
			resim";
			$tumkategori="";
			//kategoridizin($f_kategoriid);
			$markaad=teksatir("select markaad from urunmarka where markaid='".$f_markaid."'","markaad");
			$seolink=DuzeltS(K($tumkategori."/".$markaad."/".$f_seobaslik))."/".$f_sayfaid."s.html";
			$degerler=$f_benzersizid."|*_".
				mb_substr($f_seobaslik,0,65,'UTF-8')."|*_".
				mb_substr($f_seoaciklama,0,200,'UTF-8')."|*_".
				mb_substr($f_seokelime,0,255,'UTF-8')."|*_".
				$seolink."|*_".
				$seoresim;
			ekle($sutunlar,$degerler,"seo",24);
            $kopyala=0;
            if(!Bosmu(f("kopyala")))$kopyala=1;
            header('Location: /_y/s/s/urunler/urunekle.php?formhataaciklama='.$formhataaciklama.'&sayfaid='. $f_sayfaid."&kopyala=$kopyala");
            header('Location: /_y/s/s/urunler/urunekle.php?formhataaciklama='.$formhataaciklama.'&sayfaid='. $f_sayfaid."&kopyala=$kopyala");
		}
		else
		{
			//hatalogisle("sayfaEkle",$data->error);
			$formhata=1;
			if(S($f_sayfaid)!=0) $formhataaciklama="Ürün güncellenemedi"; else $formhataaciklama="Yeni ürün eklenemedi";
		}
		unset($sayfaekle_s);
	}
}
?>
