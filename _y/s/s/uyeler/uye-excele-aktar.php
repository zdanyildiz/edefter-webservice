<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
require_once($_SERVER['DOCUMENT_ROOT']."/_y/excel/excelyaz.php");

$urunliste_s = '
    select 
       uyeadsoyad,uyeeposta,uyetelefon,uyeolusturmatarih,uyefaturavergino,uyefaturavergidairesi,uyefaturaad,uyetip,uyesifre,
           CityName as sehir,CountyName as ilce,AreaName as semt,NeighborhoodName as mahalle,adresacik,adresbaslik
    from 
        uye
            left join uyeadres on uyeadres.uyeid=uye.uyeid
            left join yersehir on yersehir.CityID=uyeadres.adressehir
            left join yerilce on yerilce.CountyID=uyeadres.adresilce
            left join yersemt on yersemt.AreaID=uyeadres.adressemt
            left join yermahalle on yermahalle.NeighborhoodID=uyeadres.adresmahalle
            where uyesil=0 AND uyeaktif=1 
    Group By  adresbaslik
    Order by uyetip,uyeadsoyad ASC
    ';
if($data->query($urunliste_s))
{
    $urunliste_v=$data->query($urunliste_s);
    $books = [
        ['Üye Olma Tarihi', 'Üye Ad','Üye Soyad', 'Üye Tip', 'Üye E-posta', 'Üye Telefon','Üye Şifre','Üye Fatura Ünvan', 'Vergi Dairesi', 'Vergi No','Ades Başlık','İl','İlçe','Semt','Mahalle','Adres' ],
    ];

    while ($urunliste_t = $urunliste_v->fetch_assoc())
    {   $uyetip="Üye";$uyetelefon="";$uyead="";$uyesoyad="";$uyesifre="";
        if(!BosMu($urunliste_t["uyeadsoyad"]))
        {
            if(strpos($urunliste_t["uyeadsoyad"]," ")!==false)
            {
                $uyead=explode($urunliste_t["uyeadsoyad"]," ")[0];
                $uyesoyad=explode($urunliste_t["uyeadsoyad"]," ")[1];
            }
            if(!BosMu($urunliste_t["uyesifre"]))
            {
                $uyesifre=coz($urunliste_t["uyesifre"],$anahtarkod);
            }
        }
        if($urunliste_t["uyetip"]==1){$uyetip="Bayi";}
        if(!BosMu($urunliste_t["uyetelefon"])){$uyetelefon=Coz($urunliste_t["uyetelefon"],$anahtarkod);}
        $style="";
        $book = array($urunliste_t["uyeolusturmatarih"],$uyead,$uyesoyad,$uyetip,Coz($urunliste_t["uyeeposta"],$anahtarkod),$uyetelefon,$uyesifre,$urunliste_t["uyefaturaad"],$urunliste_t["uyefaturavergidairesi"],$urunliste_t["uyefaturavergino"],$urunliste_t["adresbaslik"],$urunliste_t["sehir"],$urunliste_t["ilce"],$urunliste_t["semt"],$urunliste_t["mahalle"],$urunliste_t["adresacik"]);
        array_push($books, $book);
    }
    SimpleXLSXGen::fromArray( $books )->downloadAs('uyelistesi.xlsx');
}else{die($data->error);}
?>