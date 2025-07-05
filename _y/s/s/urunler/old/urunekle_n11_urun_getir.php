<?php
require_once ($anadizin."/_y/s/s/pazaryeri/n11/n11_class.php");
require_once ($anadizin."/_y/s/s/pazaryeri/n11/n11_api.php");
$n11_urun = $n11->GetProductBySellerCode(q("stokkodu"));
if($n11_urun->result->status=="success")
{
    $f_urunstokkodu=q("stokkodu");
    $f_urunmodel=$f_urunstokkodu;
    $f_urunparabirim=$n11_urun->product->currencyType;
    $f_urunsatisfiyat=$n11_urun->product->displayPrice;
    $f_urunindirimsizfiyat=$n11_urun->product->price;

    $f_sayfaad=$n11_urun->product->title;
    $f_sayfaicerik=$n11_urun->product->description;
    $f_urunaciklama=$n11_urun->product->subtitle;
    $f_urun_n11_kategoriid=$n11_urun->product->category->id;

    $siteid=teksatir_PY("Select siteid From siteler Where domain='".$ayardomain."'","siteid");
    if(S($f_urun_n11_kategoriid)!=0)
    {
        $f_kategoriid=teksatir_PY("select sitekategoriid From kategoriler where n11='1' and siteid='".$siteid."' and pazaryerikategoriid='".$f_urun_n11_kategoriid."'","sitekategoriid");
        if(S($f_kategoriid)==0)
        {

        }
        else
        {
            tepekategoribul($f_kategoriid);
        }
    }

    $f_urunkargosuresi=$n11_urun->product->preparingDay;
    $f_urunfiyatsontarih=$n11_urun->product->saleEndDate;

    $f_urunfiyatsontarih=date("Y-m-d", strtotime($f_urunfiyatsontarih));

    if(isset($n11_urun->product->discount))
    {
        $f_urunindirimorani=$n11_urun->product->discount->value;
    }

    if(isset($n11_urun->product->images))
    {
        $f_resimid="";$f_resimidler="";$f_resimadlar="";
        foreach ($n11_urun->product->images->image as $image)
        {
            if(isset($image->url))
            {
                //echo $image->url."<br>";
                $img = explode("/", $image->url);
                $toplam = count($img);
                $resim = $img[$toplam - 1];
                $resimbilgi = coksatir("
                SELECT 
                    resim.resim,resimklasorad,resimid,resimad
                FROM 
                     resim
                        inner join resimklasor on
                            resimklasor.resimklasorid=resim.resimklasorid 
                    WHERE orjinal='" . $image->url . "'");
                if (!BosMu($resimbilgi)) {
                    $resimid = $resimbilgi["resimid"];
                    $resim = $resimbilgi["resim"];
                    $resimklasorad = $resimbilgi["resimklasorad"];
                    $resimad = $resimbilgi["resimad"];
                }
                else
                {
                    if (@copy($image->url, $anadizin . '/m/r/urun/' . $resim))
                    {
                        $resimklasorad = "urun";
                        $resimad = $f_sayfaad;
                        $rbenzersizid = sifreuret(20, 2);
                        if ($data->query("INSERT INTO resim(resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal) VALUES('2','" . $resimad . "','" . $resim . "','1000','1000','" . $rbenzersizid . "','" . $image->url . "')")) {
                            $resimid = teksatir("SELECT resimid FROM resim WHERE benzersizid='" . $rbenzersizid . "'", "resimid");
                        }
                        else
                        {
                            hatalogisle("N11 resim ekle",$data->error);
                        }
                    }
                }

                if (BosMu($f_resimid)) $f_resimid = "$resimklasorad/$resim"; else$f_resimid = "$f_resimid,$resimklasorad/$resim";
                if (BosMu($f_resimidler)) $f_resimidler = $resimid; else$f_resimidler = "$f_resimidler,$resimid";
                if (BosMu($f_resimadlar)) $f_resimadlar = $resimad; else$f_resimadlar = "$f_resimadlar||$resimad";
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
                            hatalogisle("N11 resim ekle",$data->error);
                        }
                    }
                }

                if (BosMu($f_resimid)) $f_resimid = "$resimklasorad/$resim"; else$f_resimid = "$f_resimid,$resimklasorad/$resim";
                if (BosMu($f_resimidler)) $f_resimidler = $resimid; else$f_resimidler = "$f_resimidler,$resimid";
                if (BosMu($f_resimadlar)) $f_resimadlar = $resimad; else$f_resimadlar = "$f_resimadlar||$resimad";
            }
        }
    }

    $f_seobaslik=$f_sayfaad;
    $f_seoaciklama=$f_urunaciklama;
    if(isset($n11_urun->product->category->name))$f_seokelime=$n11_urun->product->category->name;

    //Varyant özelliklerini al, standart formata göre array yap
    unset($urun_varyant);
    $urun_varyant["stockItems"]=array();

    $urunler=$n11_urun->product->stockItems;

    if(isset($urunler->stockItem->quantity))
    {
        $stockItem_data=[
            "sellerStockCode"=>$f_urunstokkodu,
            "optionPrice"=>$urunler->stockItem->optionPrice,
            "currencyAmount"=>$urunler->stockItem->currencyAmount,
            "displayPrice"=>$urunler->stockItem->displayPrice,
            "id"=>$urunler->stockItem->id,
            "quantity"=>$urunler->stockItem->quantity
        ];

        array_push($urun_varyant["stockItems"],["stockItem"=>$stockItem_data]);
    }
    else
    {
        foreach($urunler->stockItem as $stockItem)
        {
            //echo "<pre>";print_r($stockItem);echo "</pre>";die();
            $urun_attributes=array();
            if(isset($stockItem->attributes))
            {
                foreach($stockItem->attributes as $attribute)
                {
                    if(isset($attribute->name))
                    {
                        $attribute_data=[
                            "name"=>$attribute->name,
                            "value"=>$attribute->value
                        ];
                        array_push($urun_attributes,["attribute"=>$attribute_data]);
                    }
                    else
                    {
                        foreach($stockItem->attributes->attribute as $attribute)
                        {
                            $attribute_data=[
                                "name"=>$attribute->name,
                                "value"=>$attribute->value
                            ];
                            array_push($urun_attributes,["attribute"=>$attribute_data]);
                        }
                    }

                }
            }

            if(isset($stockItem->sellerStockCode))$f_urunstokkodu=$stockItem->sellerStockCode;
            $stockItem_data=[
                "sellerStockCode"=>$f_urunstokkodu,
                "optionPrice"=>$stockItem->optionPrice,
                "currencyAmount"=>$stockItem->currencyAmount,
                "displayPrice"=>$stockItem->displayPrice,
                "id"=>$stockItem->id,
                "quantity"=>$stockItem->quantity,
                "attributes"=>$urun_attributes
            ];
            unset($urun_attributes);
            array_push($urun_varyant["stockItems"],["stockItem"=>$stockItem_data]);
        }
    }

    //Ürün ek özelliklerini al, standart formata göre array yap
    $urun_ekozellikler["attributes"]=array();
    if(isset($n11_urun->product->attributes))
    {
        $urunler=$n11_urun->product->attributes;
        if(isset($urunler->attribute->name))
        {
            $attribute_data=[
                "name"=>$urunler->attribute->name,
                "value"=>$urunler->attribute->value
            ];
            array_push($urun_ekozellikler["attributes"],["attribute"=>$attribute_data]);
        }
        else
        {
            foreach($urunler->attribute as $attribute)
            {

                if(isset($attribute->name))
                {
                    $attribute_data=[
                        "name"=>$attribute->name,
                        "value"=>$attribute->value
                    ];
                    array_push($urun_ekozellikler["attributes"],["attribute"=>$attribute_data]);
                }

            }
        }

    }
    //echo "<pre>";print_r($urun_varyant);echo "</pre>";die();
}
?>