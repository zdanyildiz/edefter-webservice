<?php
require_once ($anadizin."/_y/s/s/pazaryeri/GG/GG_class.php");
require_once ($anadizin."/_y/s/s/pazaryeri/GG/GG_api.php");
$GG_urun = $GG->getProduct(q("productid"),q("stokkodu"));
if($GG_urun->ackCode=="success")
{
    $f_urunstokkodu = isset($GG_urun->productDetail->itemId) ? $GG_urun->productDetail->itemId : $GG_urun->productDetail->productId;
    $f_urunmodel=$f_urunstokkodu;
    $f_urun_GG_kategoriid = $GG_urun->productDetail->product->categoryCode;
    $siteid = $py_data->query("Select siteid From siteler Where domain='" . $ayardomain . "'")->fetch_assoc()["siteid"];
    if (!BosMu($f_urun_GG_kategoriid)) {
        $f_kategoriid = $py_data->query("select sitekategoriid From kategoriler where gittigidiyor='1' and siteid='" . $siteid . "' and pazaryerikategoriid='" . $f_urun_GG_kategoriid . "'")->fetch_assoc()["sitekategoriid"];
        tepekategoribul($f_kategoriid);
    }

    $f_sayfaad=$GG_urun->productDetail->product->title;
    $f_sayfaicerik=$GG_urun->productDetail->product->description;
    $f_urunsatisfiyat=$GG_urun->productDetail->product->buyNowPrice;
    $f_urunstok=$GG_urun->productDetail->product->productCount;
    if(isset($GG_urun->productDetail->product->specs))
    {
        $urun_ekozellikler["attributes"]=array();

        $urunler=$GG_urun->productDetail->product->specs;
        foreach($urunler->spec as $attribute)
        {
            $attribute_data=[
                "name"=>$attribute->name,
                "value"=>$attribute->value
            ];
            array_push($urun_ekozellikler["attributes"],["attribute"=>$attribute_data]);
        }
    }

    if(isset($GG_urun->productDetail->product->photos->photo))
    {
        foreach($GG_urun->productDetail->product->photos->photo as $image)
        {
            //echo $image->url."<br>";
            if(isset($image->url))
            {
                $img=explode("/",$image->url);
                $toplam=count($img);
                $resim=$img[$toplam-1];
                $resimbilgi=coksatir("
                    SELECT 
                        resim.resim,resimklasorad,resimid,resimad
                    FROM 
                         resim
                            inner join resimklasor on
                                resimklasor.resimklasorid=resim.resimklasorid 
                        WHERE orjinal='".$image->url."'");
                if(!BosMu($resimbilgi))
                {
                    $resimid=$resimbilgi["resimid"];
                    $resim=$resimbilgi["resim"];
                    $resimklasorad=$resimbilgi["resimklasorad"];
                    $resimad=$resimbilgi["resimad"];
                }
                else
                {
                    if(@copy($image->url, $anadizin.'/m/r/urun/'.$resim))
                    {
                        $resimklasorad="urun";
                        $resimad=$f_sayfaad;
                        $rbenzersizid=sifreuret(20,2);
                        if($data->query("INSERT INTO resim(resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal) VALUES('2','".$resimad."','".$resim."','1000','1000','".$rbenzersizid."','".$image->url."')"))
                        {
                            $resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$rbenzersizid."'","resimid");
                        }
                        else
                        {
                            hatalogisle("GG resim ekle",$data->error);
                        }
                    }
                }

                if(BosMu($f_resimid))$f_resimid="$resimklasorad/$resim";else$f_resimid="$f_resimid,$resimklasorad/$resim";
                if(BosMu($f_resimidler))$f_resimidler=$resimid;else$f_resimidler="$f_resimidler,$resimid";
                if(BosMu($f_resimadlar))$f_resimadlar=$resimad;else$f_resimadlar="$f_resimadlar||$resimad";
            }
            elseif(!BosMu($image))
            {
                $img = explode("/", $image);
                $toplam = count($img);
                $resim = $img[$toplam - 1];
                $resimbilgi = coksatir("
                SELECT 
                    resim.resim,resimklasorad,resimid,resimad
                FROM 
                     resim
                        inner join resimklasor on
                            resimklasor.resimklasorid=resim.resimklasorid 
                    WHERE orjinal='" . $image . "'");
                if (!BosMu($resimbilgi))
                {
                    $resimid = $resimbilgi["resimid"];
                    $resim = $resimbilgi["resim"];
                    $resimklasorad = $resimbilgi["resimklasorad"];
                    $resimad = $resimbilgi["resimad"];
                }
                else
                {
                    if (@copy($image, $anadizin . '/m/r/urun/' . $resim))
                    {
                        $resimklasorad = "urun";
                        $resimad = $f_sayfaad;
                        $rbenzersizid = sifreuret(20, 2);
                        if ($data->query("INSERT INTO resim(resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal) VALUES('2','" . $resimad . "','" . $resim . "','1000','1000','" . $rbenzersizid . "','" . $image . "')"))
                        {
                            $resimid = teksatir("SELECT resimid FROM resim WHERE benzersizid='" . $rbenzersizid . "'", "resimid");
                        }
                        else
                        {
                            hatalogisle("GG resim ekle",$data->error);
                        }
                    }
                }

                if (BosMu($f_resimid)) $f_resimid = "$resimklasorad/$resim"; else$f_resimid = "$f_resimid,$resimklasorad/$resim";
                if (BosMu($f_resimidler)) $f_resimidler = $resimid; else$f_resimidler = "$f_resimidler,$resimid";
                if (BosMu($f_resimadlar)) $f_resimadlar = $resimad; else$f_resimadlar = "$f_resimadlar||$resimad";
            }
        }
    }
    //die(print_r($GG_urun->productDetail));
}else{
    die("GittiGidiyor Kaynağından ürün alınamadı");
}
?>