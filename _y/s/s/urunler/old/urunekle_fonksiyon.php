<?php

if(S($f_sayfaekle)==1 && $formhata==0)
{
    $urunparabirimsimge=teksatir("SELECT parabirimsimge FROM urunparabirim WHERE parabirimid='".$f_urunparabirim."'","parabirimsimge");

    $varyant_grup_secenek_json="";
    if(f("yenisecenekgrupid"))
    {
        $varyant_grup_secenek = [];
        //gelen varyantları ayıklıyoruz
        foreach (f("urunekozellikid") as $say => $urun_varyant_ekozellik)
        {
            if(f("otovaryant")==1)
            {
                $stokkodu_olustur=$f_urunmodel."-".f("urunekozellikid")[$say];
                $varyant_grup_secenek['stockItems'][$say]['stockItem']['sellerStockCode'] =$stokkodu_olustur;
            }
            else
            {
                $varyant_grup_secenek['stockItems'][$say]['stockItem']['sellerStockCode'] = f("urunstokkodu")[$say];
            }

            if(isset(f("variantImage")[$say])){
                $varyant_grup_secenek['stockItems'][$say]['stockItem']['variantImage'] = f("variantImage")[$say];
            }


            $varyant_grup_secenek['stockItems'][$say]['stockItem']['displayPrice'] = f("urunsatisfiyat")[$say];
            $varyant_grup_secenek['stockItems'][$say]['stockItem']['quantity'] = f("urunstok")[$say];
            $varyant_grup_secenek['stockItems'][$say]['stockItem']['parabirimsimge'] = $urunparabirimsimge;
            $varyant_grup_secenek['stockItems'][$say]['stockItem']['parabirimid'] = $f_urunparabirim;

            $varyant_grup_secenek['stockItems'][$say]['stockItem']['optionPrice']=f("urunindirimsizfiyat")[$say];

            $varyant_grup_secenek['stockItems'][$say]['stockItem']['gtin'] = f("urungtin")[$say];

            $varyant_grup_secenek['stockItems'][$say]['stockItem']['mpn'] = f("urunmpn")[$say];

            $varyant_grup_secenek['stockItems'][$say]['stockItem']['oem'] = f("urunoem")[$say];

            $varyant_grup_secenek['stockItems'][$say]['stockItem']['barkod'] = f("urunbarkod")[$say];

            if(isset(f("varyantekozellikler")[$say]))
            {
                $varyant_eksecenekler=f("varyantekozellikler")[$say];
                //örnek değer : RENK:KAHVERENGİ,ÖLÇÜ:1.2Mt
                $varyant_eksecenek_ayir=explode(",",$varyant_eksecenekler);
                foreach ($varyant_eksecenek_ayir as $i => $varyant_eksecenek)
                {
                    //echo $varyant_eksecenek."<br>";
                    //örnek değer RENK:KAHVERENGİ
                    $varyant_secenek_ayir=explode(":",$varyant_eksecenek);
                    $varyant_secenek_ad=addcslashes($varyant_secenek_ayir[0],"'");
                    $varyant_secenek_ad=addcslashes($varyant_secenek_ad,'"');
                    //örnek değer RENK
                    $varyant_secenek_deger=addcslashes($varyant_secenek_ayir[1],"'");
                    $varyant_secenek_deger=addcslashes($varyant_secenek_deger,'"');
                    //örnek değer KAHVERENGİ

                    $varyant_grup_secenek['stockItems'][$say]['stockItem']['attributes'][$i]["attribute"]["name"] = $varyant_secenek_ad;
                    $varyant_grup_secenek['stockItems'][$say]['stockItem']['attributes'][$i]["attribute"]["value"] = $varyant_secenek_deger;
                }
            }
        }
        $varyant_grup_secenek_json=json_encode($varyant_grup_secenek, JSON_UNESCAPED_UNICODE);
        $varyant_grup_secenek_json=str_replace("\\\\","\\",$varyant_grup_secenek_json);
    }
    //die(print_r($varyant_grup_secenek_json));

    $seo_ek_aciklama="";
    $urunekozellik_arr_json="";
    if(f("urunekozellik"))
    {
        $urunekozellik_arr = [];

        foreach (f("urunekozellik") as $say => $urunekozellik) {
            if(strpos($urunekozellik, ":") !== false) {
                $urunekozellik_ayir = explode(':', $urunekozellik);
                if(!BosMu($urunekozellik_ayir[0]) && !BosMu($urunekozellik_ayir[1])) {
                    $attribute_name = trim($urunekozellik_ayir[0]);
                    $attribute_value = trim($urunekozellik_ayir[1]);
                    $urunekozellik_arr['attributes'][] = array('name' => $attribute_name, 'value' => $attribute_value);
                }
            }
        }
        $urunekozellik_arr_json=json_encode($urunekozellik_arr, JSON_UNESCAPED_UNICODE);
        $urunekozellik_arr_json=str_replace("\\\\","\\",$urunekozellik_arr_json);
    }
    if(!BosMu($seo_ek_aciklama))$seo_ek_aciklama=addslashes($seo_ek_aciklama);
    //print_r($urunekozellik_arr_json);die();
	if($formhata==0)
	{
        $eskiseo_link="";
		$simdi=date("Y-m-d H:i:s");
        $f_urunfiyatsontarih=date("Y-m-d", strtotime($f_urunfiyatsontarih));

		if(S($f_sayfaid)!=0)
		{
			$sayfaekle_s=
			"
				UPDATE
					sayfa
				SET
					benzersizid				= '". $f_benzersizid ."',
					sayfatarihguncel		= '". $simdi ."',
					sayfatip				= '". $f_sayfagrup ."',
					sayfaad					= '". $f_sayfaad ."',
					sayfaicerik				= '". $f_sayfaicerik ."',
					sayfalink				= '". $f_sayfalink ."',
					sayfasira				= '". $f_sayfasira ."',
					sayfaaktif				= '". $f_sayfaaktif ."',
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
					'". $f_benzersizid ."',
					'". $simdi ."',
					'". $simdi ."',
					'". $f_sayfagrup ."',
					'". $f_sayfaad ."',
					'". $f_sayfaicerik ."',
					'". $f_sayfalink ."',
					'". $f_sayfasira ."',
					'". $f_sayfaaktif ."',
					'0'
				)
				";
			$eylem=1;$formad="Sayfa Ekle";
		}

		if($data->query($sayfaekle_s))
		{
			yoneticiislemleri(24,$eylem);
			if(S($f_sayfaid)!=0)
			{
				$formhataaciklama="Sayfa güncellendi";
				$data->query("DELETE FROM sayfalistekategori WHERE sayfaid='". $f_sayfaid ."'");
				$data->query("DELETE FROM sayfalisteresim WHERE sayfaid='". $f_sayfaid ."'");
				$data->query("DELETE FROM urunozellikleri WHERE sayfaid='". $f_sayfaid ."'");
				$data->query("DELETE FROM seo WHERE benzersizid='". $f_benzersizid ."'");

                $f_benzersizid=teksatir(" Select benzersizid from sayfa Where sayfaid='". $f_sayfaid ."'","benzersizid");
                $eskiseo_link=teksatir(" Select link from seo Where benzersizid='". $f_benzersizid ."'","link");
			}
			else
			{
				$formhataaciklama="Yeni sayfa eklendi";
				$f_sayfaid=teksatir(" Select sayfaid from sayfa Where benzersizid='". $f_benzersizid ."'","sayfaid");
			}

            sil("sayfalistedosya","sayfaid='".$f_sayfaid."'",0);
            if(!BosMu($f_dosyaid))
            {
                $f_dosyaid=rtrim($f_dosyaid,",");
                $dosyaekle_ayikla = explode(",", $f_dosyaid);
                foreach($dosyaekle_ayikla as $eklenecekdosya)
                {
                    ekle("sayfaid,dosyaid",$f_sayfaid."|*_".$eklenecekdosya,"sayfalistedosya",57);
                }
            }

            sil("sayfalistevideo","sayfaid='".$f_sayfaid."'",0);
            if(!BosMu($f_videoid))
            {
                $f_videoid=rtrim($f_videoid,",");
                $videoekle_ayikla = explode(",", $f_videoid);
                foreach($videoekle_ayikla as $eklenecekvideo)
                {
                    ekle("sayfaid,videoid",$f_sayfaid."|*_".$eklenecekvideo,"sayfalistevideo",57);
                }
            }

            sil("sayfalistegaleri","sayfaid='".$f_sayfaid."'",0);
            if(S($f_galeriid)!=0)
            {
                ekle("sayfaid,resimgaleriid",$f_sayfaid."|*_".$f_galeriid,"sayfalistegaleri",57);
            }

            $seoresim="";
            if(f("resimid"))
            {
                foreach(f("resimid") as $ekleresim)
                {
                    ekle("sayfaid,resimid",$f_sayfaid."|*_".$ekleresim,"sayfalisteresim",57);
                    $resim=teksatir(" Select resim from resim Where resimid='". $ekleresim ."'","resim");
                    $seoresim.=","."/m/r/urun/".$resim;$resim="";
                }
            }

            foreach ($f_urunstokkodu as $i => $varyant_stokkodu)
            {
                //$data->query("DELETE FROM urunozellikleri WHERE urunstokkodu='". $varyant_stokkodu ."'");
                //echo $varyant_stokkodu.'<br>';
                //burada stokkodu,gtin,mpn,satisfiyat,stok,alisfiyat,bayifiyat,indirmszi fiyat gelecek

                $f_urunsatisfiyat=f("urunsatisfiyat")[$i];
                $f_urunindirimsizfiyat=f("urunindirimsizfiyat")[$i];
                if(BosMu($f_urunindirimsizfiyat))$f_urunindirimsizfiyat="0.00";

                $f_urunbayifiyat=f("urunbayifiyat")[$i];
                if(BosMu($f_urunbayifiyat))$f_urunbayifiyat="0";

                $f_urunalisfiyat=f("urunalisfiyat")[$i];
                if(BosMu($f_urunalisfiyat))$f_urunalisfiyat="0";

                $f_urunoem=f("urunoem")[$i];
                $f_urungtin=f("urungtin")[$i];
                $f_urunmpn=f("urunmpn")[$i];
                $f_urunbarkod=f("urunbarkod")[$i];

                $f_urunstok=S(f("urunstok")[$i]);
                $f_urunstokkodu=$varyant_stokkodu;

                $variantImageID=isset(f("variantImage")[$i])?f("variantImage")[$i]:0;
                if($variantImageID>0)
                {
                    guncelle("stockcode",$f_urunstokkodu,"sayfalisteresim","sayfaid='".$f_sayfaid."' and resimid='".$variantImageID."'",0);
                }


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
                    urunekvaryantozellik,
                    urunekozellik,
                    urunoem,
                    urungtin,
                    urunmpn,
                    urunbarkod,
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
                    $f_urunindirimorani."|*_".
                    date($f_urunfiyatsontarih)."|*_".
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
                    $varyant_grup_secenek_json."|*_".
                    $urunekozellik_arr_json."|*_".
                    $f_urunoem."|*_".
                    $f_urungtin."|*_".
                    $f_urunmpn."|*_".
                    $f_urunbarkod."|*_".
                    $f_urundesi."|*_".
                    $f_urunminimummiktar."|*_".
                    $f_urunmaksimummiktar."|*_".
                    $f_urunkatsayi."|*_".
                    $f_urunmiktarbirimid
                ;
                    ;
                ekle($urunsutunlar,$urundegerler,"urunozellikleri",58);
            }

            ekle("sayfaid,kategoriid",$f_sayfaid."|*_".$f_kategoriid,"sayfalistekategori",56);
            $kategoriad=teksatir(" Select kategoriad from kategori Where kategoriid='". $f_kategoriid ."'","kategoriad");
            kategorial($f_kategoriid);

			$sutunlar="benzersizid,
			baslik,
			aciklama,
			kelime,
			link,
			resim";
			$tumkategori="";

			$f_seobaslik=$f_sayfaad;
			$markaad=teksatir("select markaad from urunmarka where markaid='".$f_markaid."'","markaad");
			$f_seoaciklama="Kategori:$kategoriad, Ürün:$f_sayfaad $seo_ek_aciklama, fiyat:$f_urunsatisfiyat, Marka:$markaad";

            if(!BosMu($f_sayfalink))
            {
                if(substr($f_sayfalink,0,1)!="/")$f_sayfalink="/".$f_sayfalink;
                tepekategorilinkbul($f_kategoriid);
                $seolink=$tepekategoriyazdir;
                $seolink=str_replace("//","/",$seolink);
                //die("slink: $seolink <br> katlink: $f_kategorilink");
                $f_dilkisa=teksatir(" Select dilkisa from dil Where dilid='". $f_dilid ."'","dilkisa");
                $seolink.=$f_sayfalink;
                $seolink="/".Duzelt(K($f_dilkisa)).$seolink;
            }
            else
            {
                //$seolink=DuzeltS(K($tumkategori."/".$markaad."/".$f_seobaslik))."/".$f_sayfaid."s.html";
                $f_sayfalink="/".Duzelt(K($f_sayfaad));
                tepekategorilinkbul($f_kategoriid);
                $seolink=$tepekategoriyazdir;
                $seolink=str_replace("//","/",$seolink);
                //die("slink: $seolink <br> katlink: $f_kategorilink");
                $f_dilkisa=teksatir(" Select dilkisa from dil Where dilid='". $f_dilid ."'","dilkisa");
                $seolink.=$f_sayfalink;
                $seolink="/".Duzelt(K($f_dilkisa)).$seolink;
            }

            guncelle("bannerlink",$seolink,"banner","bannerlink='".$f_sayfalink."'",0);
            guncelle("menulink",$seolink,"menu","menulink='".$f_sayfalink."'",0);
            if(!BosMu($eskiseo_link))
            {
                guncelle("bannerlink",$seolink,"banner","bannerlink='".$eskiseo_link."'",0);
                guncelle("menulink",$seolink,"menu","menulink='".$eskiseo_link."'",0);
            }

			$degerler=$f_benzersizid."|*_".
				mb_substr($f_seobaslik,0,65,'UTF-8')."|*_".
				mb_substr($f_seoaciklama,0,200,'UTF-8')."|*_".
				mb_substr($f_seokelime,0,255,'UTF-8')."|*_".
				$seolink."|*_".
				$seoresim;
			ekle($sutunlar,$degerler,"seo",24);

            //TABLOYA GİRİLENLERİ XML'YE ÇEVİR VE SIRALA
            $menuicerik='';
            //menü xml yaz
            for ($myi=0; $myi<5; $myi++)
            {
                if($myi==0)$menukategoriad="tepemenu";
                if($myi==1)$menukategoriad="ustmenu";
                if($myi==2)$menukategoriad="solmenu";
                if($myi==3)$menukategoriad="sagmenu";
                if($myi==4)$menukategoriad="altmenu";
                $menuyaz='';
                $menu_s="
                    SELECT 
                        * 
                    FROM
                        menu 
                    WHERE 
                        dilid='".$f_dilid."' and menukategori='".$myi."' and menukatman='0' and ustmenuid='0' 
                    ORDER By
                        menusira ASC
                ";
                $menu_v=$data->query($menu_s);
                if($data->query($menu_s))
                {
                    if($menu_v->num_rows>0)
                    {
                        while ($menu_t=$menu_v->fetch_assoc())
                        {
                            $menuid 		=$menu_t["menuid"];
                            $menuad 		=$menu_t["menuad"];
                            $menukatman 	=$menu_t["menukatman"];
                            $ymenukategori 	=$menu_t["menukategori"];
                            $altkategori 	=$menu_t["altkategori"];
                            $orjbenzersizid	=$menu_t["orjbenzersizid"];
                            if($altkategori==1)$altkategoriyaz="true";else $altkategoriyaz="false";
                            $menuyaz= $menuyaz.'
                            <menu>
                                <menualtkategori>'.$altkategoriyaz.'</menualtkategori>
                                <menuorjbenzersiz>'.$orjbenzersizid.'</menuorjbenzersiz>
                                <menuid>'.$menuid.'</menuid>
                                <menuad><![CDATA['. stripcslashes($menuad) .']]></menuad>
                                <menulink>'.$menu_t["menulink"].'</menulink>';
                            $altmenuyaz=menuxmlgetir($ymenukategori,$menukatman+1,$menuid);

                            $menuyaz= $menuyaz.$altmenuyaz.'
                            </menu>
                            ';
                        }
                        $menuicerik.='
                        <'.$menukategoriad.'>
                        '.$menuyaz.'
                        </'.$menukategoriad.'>';
                    }
                }else{hatalogisle("$menukategoriad xml yaz",$data->error);}
            }

            $menuxml = $anadizin.'/sistem/xml/icerik/menu-'.$f_dilid.'.xml';
            if (!file_exists($anadizin.'/sistem/xml/icerik'))
            {
                mkdir($anadizin.'/sistem/xml/icerik', 0777, true);
            }
            $menuicerik='<?xml version="1.0" encoding="utf-8"?>
            <icerik>
                <menuler>
                    '.$menuicerik.'
                </menuler>
            </icerik>';
            file_put_contents($menuxml,$menuicerik);
            unset($menuxml,$menuicerik);

            if(f("pazaryeri"))
            {
                $n11ekle=0;$GGekle=0;
                foreach(f("pazaryeri") as $f_pazaryeriad)
                {
                    if($f_pazaryeriad=="n11")
                    {
                        $n11ekle=1;
                    }
                    if($f_pazaryeriad=="gittigidiyor")
                    {
                        $GGekle=1;
                    }
                }
                if(S($n11ekle)==1&&S($GGekle)==1)
                {
                    header('Location: /_y/s/s/pazaryeri/n11/n11_urunekle.php?formhataaciklama='.$formhataaciklama.'&sayfaid='. $f_sayfaid.'&gg=1&productid='.f("productid"));
                    die();
                }
                if(S($n11ekle)==1)
                {
                    header('Location: /_y/s/s/pazaryeri/n11/n11_urunekle.php?formhataaciklama='.$formhataaciklama.'&sayfaid='. $f_sayfaid);die();
                }
                if(S($GGekle)==1)
                {
                    header('Location: /_y/s/s/pazaryeri/GG/GG_urunekle.php?formhataaciklama='.$formhataaciklama.'&sayfaid='. $f_sayfaid.'&productid='.f("productid"));die();
                }
            }

            $kopyala=0;
            if(!Bosmu(f("kopyala")))$kopyala=1;
            header('Location: /_y/s/s/urunler/urunekle.php?formhataaciklama='.$formhataaciklama.'&sayfaid='. $f_sayfaid."&kopyala=$kopyala");
		}
		else
		{
			hatalogisle("sayfaEkle",$data->error);
			$formhata=1;
			if(S($f_sayfaid)!=0) $formhataaciklama="Ürün güncellenemedi"; else $formhataaciklama="Yeni ürün eklenemedi";
		}
		unset($sayfaekle_s);
	}
}
?>
