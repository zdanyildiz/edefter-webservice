<?php
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
require_once($_SERVER['DOCUMENT_ROOT']."/_y/excel/excelyaz.php");
    $simdi=date("Y-m-d H:i:s");
    $urunliste_s = '
    select 
        sayfa.sayfaid as urunid,
        kategoriad,
        markaad,
        urunstokkodu as stokkodu,
        urunmodel as model,
        sayfaad as baslik,
        urunhediye AS altbaslik,
        sayfaicerik as aciklama,
        urunalisfiyat as alisfiyat,
        urunsatisfiyat as fiyat,
        urunindirimsizfiyat as indirimsizfiyat,
        urunbayifiyat as bayifiyat,
        urunstok as stok,
        parabirimad as parabirim,
        urunrenkad as renkad,
        urunbedenad as bedenad,
        urunmalzemead as malzemead,
        sayfaaktif,urunozellikleri.urunmiktarbirimid
    from 
        urunozellikleri 
            inner join sayfa on sayfa.sayfaid=urunozellikleri.sayfaid
            inner join sayfalistekategori on sayfalistekategori.sayfaid=sayfa.sayfaid
            inner join kategori on kategori.kategoriid=sayfalistekategori.kategoriid
        inner join urunmarka on urunmarka.markaid=urunozellikleri.markaid
        inner join urunparabirim on urunparabirim.parabirimid=urunozellikleri.urunparabirim
        left join urunrenk on urunrenk.urunrenkid=urunozellikleri.urunrenkid
        left join urunbeden on urunbeden.urunbedenid=urunozellikleri.urunbedenid
        left join urunmalzeme on urunmalzeme.urunmalzemeid=urunozellikleri.urunmalzemeid
        left join urunmiktarbirim on urunmiktarbirim.urunmiktarbirimid=urunozellikleri.urunmiktarbirimid
    Order by kategoriad,stokkodu ASC
    ';
    if($data->query($urunliste_s))
    {
        $urunliste_v=$data->query($urunliste_s);
        $books = [
            ['urunid', 'kategoriad', 'markaad', 'stokkodu', 'model','baslik', 'altbaslik', 'aciklama', 'alisfiyat', 'fiyat','indirimsizfiyat', 'bayifiyat', 'stok', 'parabirim', 'renkad', 'bedenad','malzemead', 'resimklasor', 'resim', 'dosya', 'video', 'link', 'aktif','urunbirim','urunresim' ],
        ];

        while ($urunliste_t = $urunliste_v->fetch_assoc())
        {
            $urunbirim="ADT";
            if($urunliste_t["urunmiktarbirimid"]==17)$urunbirim="M";
            $uruncaiklama=!empty($urunliste_t["kategoriad"])?$urunliste_t["kategoriad"].' - ':'';
            $uruncaiklama.=!empty($urunliste_t["markaad"])?$urunliste_t["markaad"].' - ':'';
            $uruncaiklama.=!empty($urunliste_t["altbaslik"])?$urunliste_t["altbaslik"].' - ':'';
            $uruncaiklama.=!empty($urunliste_t["malzemead"])?$urunliste_t["malzemead"].' - ':'';
            $uruncaiklama.=!empty($urunliste_t["renkad"])?$urunliste_t["renkad"].' - ':'';
            $uruncaiklama.=!empty($urunliste_t["bedenad"])?$urunliste_t["bedenad"].' - ':'';

            $urunresim_s = 'select resim from sayfalisteresim inner join resim on resim.resimid=sayfalisteresim.resimid where sayfaid='.$urunliste_t["urunid"].' order by sayfalisteresimid ASC Limit 1';
            $urunresim_result=coksatir($urunresim_s);
            $urunresim='';
            if(count($urunresim_result)>0){
                $urunresim="https://www.makinaelemanlari.com/m/r/urun/".$urunresim_result["resim"];
            }

            if($urunliste_t["sayfaaktif"]==0)$style='style="background-color:#dcdcdc"';
            $book = array($urunliste_t["urunid"],$urunliste_t["kategoriad"],$urunliste_t["markaad"],$urunliste_t["stokkodu"],$urunliste_t["model"],$urunliste_t["baslik"],$urunliste_t["altbaslik"],$uruncaiklama,$urunliste_t["alisfiyat"],$urunliste_t["fiyat"],$urunliste_t["indirimsizfiyat"],$urunliste_t["bayifiyat"],$urunliste_t["stok"],$urunliste_t["parabirim"],$urunliste_t["renkad"],$urunliste_t["bedenad"],$urunliste_t["malzemead"],'','','','','',$urunliste_t["sayfaaktif"],$urunbirim,$urunresim);
            array_push($books,$book);
        }
        SimpleXLSXGen::fromArray( $books )->downloadAs('urunlistesi-'.$simdi.'.xlsx');

    }else{die($data->error);}
?>
