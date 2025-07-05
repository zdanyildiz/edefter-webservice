<?php
$tepekategoriid=0;$tepekategori="";
function tepekategoribul($strkategoriid)
{
    global $tepekategoriid,$tepekategoriad,$tepekategorilink,$tepekategori;
    
    $ustkatid=teksatir("select ustkategoriid From kategori Where kategoriid='".$strkategoriid."'","ustkategoriid");
    if($ustkatid==0)
    {
        $tepekategoriid=$strkategoriid;
        if(BosMu($tepekategori))$tepekategori=$ustkatid;else $tepekategori=$ustkatid.",".$tepekategori;
        //$tepekategoriad=teksatir("select kategoriad From kategori Where kategoriid='".$strkategoriid."'","kategoriad");
        //$tepekategorilink=teksatir("select link From kategori inner join seo on seo.benzersizid=kategori.benzersizid Where kategoriid='".$strkategoriid."'","link");
    }
    else
    {
        if(BosMu($tepekategori))$tepekategori=$ustkatid;else $tepekategori=$ustkatid.",".$tepekategori;
        tepekategoribul($ustkatid);
    }
}
if(S(q("sayfaid"))!=0)
{
    $f_sayfaid=S(q("sayfaid"));
    //ürün temel özelliklerini al
    $sayfabilgileri_d=0;
    $sayfabilgileri_s="
		SELECT
			sayfa.benzersizid,
			sayfatip,
			sayfaad,
			sayfaicerik,
			sayfalink,
			sayfasira,
			sayfaaktif,
			sayfasil,
		    kategori.kategoriid,dilid
		FROM
			sayfa
		        left join sayfalistekategori ON sayfalistekategori.sayfaid=sayfa.sayfaid
		        left join kategori ON kategori.kategoriid=sayfalistekategori.kategoriid
		WHERE
			sayfasil='0' and sayfa.sayfaid='".$f_sayfaid."'
		Group By sayfa.sayfaid	
	";
    if($db->select($sayfabilgileri_s))
    {
        $sayfabilgileri_v=$db->select($sayfabilgileri_s);
        if($sayfabilgileri_v)$sayfabilgileri_d=1;
        if($sayfabilgileri_d==1)
        {
            $butonisim=" GÜNCELLE ";
            if(s(q("kopyala"))==1){$butonisim="KAYDET";}
            foreach($sayfabilgileri_v as $sayfabilgileri_t )
            {
                $f_sayfaid 		=S(q("sayfaid"));
                $f_benzersizid	=$sayfabilgileri_t["benzersizid"];
                $f_sayfagrup 	=$sayfabilgileri_t["sayfatip"];
                $f_sayfaad 		=$sayfabilgileri_t["sayfaad"];
                $f_sayfaicerik 	=$sayfabilgileri_t["sayfaicerik"];
                if(!BosMu($f_sayfaicerik)){$f_sayfaicerik=html_entity_decode($f_sayfaicerik);}/*die($f_sayfaicerik);*/
                $f_sayfalink 	=$sayfabilgileri_t["sayfalink"];
                if(BosMu($f_sayfalink))$f_sayfalink="/".Duzelt(K($f_sayfaad));
                $f_sayfasira 	=$sayfabilgileri_t["sayfasira"];
                $f_sayfaaktif 	=$sayfabilgileri_t["sayfaaktif"];
                $f_sayfasil 	=$sayfabilgileri_t["sayfasil"];
                $f_kategoriid 	=$sayfabilgileri_t["kategoriid"];
                if(S($sayfabilgileri_t["dilid"])!=0)$f_dilid =$sayfabilgileri_t["dilid"];
                $formbaslik="Ürün Bilgileri";
            }
            unset($sayfabilgileri_t);
            $f_kategoriid=teksatir("
				select 
					kategori.kategoriid 
				from
					sayfa 
						inner join 
							sayfalistekategori on sayfalistekategori.sayfaid=sayfa.sayfaid 
						inner join 
							kategori on kategori.kategoriid=sayfalistekategori.kategoriid 
					where 
						sayfa.sayfaid='". S(q("sayfaid")) ."'"
                ,"kategoriid");

            $ozelliksql_d=0;
            $ozelliksql_s=" SELECT * FROM urunozellikleri WHERE sayfaid='".$f_sayfaid."' ";
            $ozelliksql_v=$db->select($ozelliksql_s);
            unset($ozelliksql_s);
            if($ozelliksql_v)$ozelliksql_d=1;

            if($ozelliksql_d==1)
            {
                foreach($ozelliksql_v as $ozelliksql_t )
                {
                    $f_markaid				=$ozelliksql_t["markaid"];
                    $f_tedarikciid			=$ozelliksql_t["tedarikciid"];
                    $f_urungrupid			=$ozelliksql_t["urungrupid"];
                    $f_urunhediye			=$ozelliksql_t["urunhediye"];
                    $f_urunaciklama			=$ozelliksql_t["urunaciklama"];

                    $f_urunkargosuresi		=$ozelliksql_t["urunkargosuresi"];
                    $f_urunsabitkargoucreti	=$ozelliksql_t["urunsabitkargoucreti"];

                    $f_urunindirimsizfiyat 	=$ozelliksql_t["urunindirimsizfiyat"];
                    $f_urunsatisfiyat		=$ozelliksql_t["urunsatisfiyat"];
                    $f_urunbayifiyat 		=$ozelliksql_t["urunbayifiyat"];
                    $f_urunalisfiyat 		=$ozelliksql_t["urunalisfiyat"];
                    $f_uruneskifiyatgoster 	=$ozelliksql_t["uruneskifiyatgoster"];

                    $f_uruntaksit 			=$ozelliksql_t["uruntaksit"];
                    $f_urunkdv 				=$ozelliksql_t["urunkdv"];
                    $f_urunstok 			=$ozelliksql_t["urunstok"];
                    $f_urunstokkodu			=$ozelliksql_t["urunstokkodu"];
                    $f_urunindirimorani 	=$ozelliksql_t["urunindirimorani"];

                    $f_urunfiyatsontarih 	=$ozelliksql_t["urunfiyatsontarih"];

                    $f_urunanasayfa 		=$ozelliksql_t["urunanasayfa"];
                    $f_urunindirimde		=$ozelliksql_t["urunindirimde"];
                    $f_urunyeni 			=$ozelliksql_t["urunyeni"];
                    $f_uruntopluindirim 	=$ozelliksql_t["uruntopluindirim"];
                    $f_urunanindakargo 		=$ozelliksql_t["urunanindakargo"];
                    $f_urunucretsizkargo 	=$ozelliksql_t["urunucretsizkargo"];
                    $f_urunonsiparis 		=$ozelliksql_t["urunonsiparis"];
                    $f_urunfiyatsor 		=$ozelliksql_t["urunfiyatsor"];
                    $f_urunparabirim 		=$ozelliksql_t["urunparabirim"];
                    $f_urungununfirsati		=$ozelliksql_t["urungununfirsati"];
                    $f_kargo 				=$ozelliksql_t["urunkargo"];
                    $f_urunkredikarti		=$ozelliksql_t["urunkredikarti"];
                    $f_urunkapidaodeme		=$ozelliksql_t["urunkapidaodeme"];
                    $f_urunhavaleodeme		=$ozelliksql_t["urunhavaleodeme"];
                    $f_urunsatisadet		=$ozelliksql_t["urunsatisadet"];
                    $f_urunindirimoranigoster=$ozelliksql_t["urunindirimoranigoster"];
                    $f_urunmodel	 		= $ozelliksql_t["urunmodel"];
                    $f_urunekvaryantozellik = $ozelliksql_t["variantProperties"];
                    $f_urunekozellik        = $ozelliksql_t["product_properties"];
                    $f_urungtin             = $ozelliksql_t["urungtin"];
                    $f_urunmpn              = $ozelliksql_t["urunmpn"];
                    $f_urunbarKod           = $ozelliksql_t["urunbarkod"];
                    $f_urundesi             = $ozelliksql_t["urundesi"];

                    $f_urunminimummiktar    = $ozelliksql_t["urunminimummiktar"];
                    $f_urunmaksimummiktar   = $ozelliksql_t["urunmaksimummiktar"];
                    $f_urunkatsayi          = $ozelliksql_t["urunkatsayi"];
                    $f_urunmiktarbirimid    = $ozelliksql_t["urunmiktarbirimid"];
                }

                if(!BosMu($f_urunekvaryantozellik))
                {
                    $varyant_decode=json_decode($f_urunekvaryantozellik,true);
                    $urun_varyant=$varyant_decode;
                }
                else
                {
                    $urun_varyant=[];
                    $urun_varyant['stockItems'][0]['stockItem']['sellerStockCode']=$f_urunstokkodu;
                    $urun_varyant['stockItems'][0]['stockItem']['optionPrice']=$f_urunindirimsizfiyat;
                    $urun_varyant['stockItems'][0]['stockItem']['displayPrice']=$f_urunsatisfiyat;
                    $urun_varyant['stockItems'][0]['stockItem']['quantity']=$f_urunstok;
                    $urun_varyant['stockItems'][0]['stockItem']['n11CatalogId']='';
                    $urun_varyant['stockItems'][0]['stockItem']['attributes']=[];
                    if(!BosMu($f_urungtin))$urun_varyant['stockItems']['stockItem'][0]['urungtin']=$f_urungtin;
                    if(!BosMu($f_urunmpn))$urun_varyant['stockItems']['stockItem'][0]['urunmpn']=$f_urunmpn;
                }

                if(!BosMu($f_urunekozellik))
                {
                    $urunekozellik_decode=json_decode($f_urunekozellik,true);
                    $urun_ekozellikler=$urunekozellik_decode;
                }
            }
            unset($ozelliksql_d,$ozelliksql_v,$ozelliksql_t);
        }
        unset($sayfabilgileri_d);
        $sayfaresimler_d=0;
        $sayfaresimler_s="
			Select 
				resimklasorad,resim.resim,resim.resimid,resimad 
			From 
				sayfalisteresim 
					inner join resim on 
						resim.resimid=sayfalisteresim.resimid 
					inner join resimklasor on 
						resimklasor.resimklasorid=resim.resimklasorid 
			where 
				sayfalisteresim.sayfaid='". $f_sayfaid ."'";
        $sayfaresimler_v=$db->select($sayfaresimler_s);
        if($sayfaresimler_v)$sayfaresimler_d=1;
        if($sayfaresimler_d==1)
        {
            $f_resimid="";
            foreach($sayfaresimler_v as $sayfaresimler_t)
            {
                $resim=$sayfaresimler_t["resim"];
                $resimklasorad=$sayfaresimler_t["resimklasorad"];
                $resimid=$sayfaresimler_t["resimid"];
                $resimad=$sayfaresimler_t["resimad"];
                if(BosMu($f_resimid))$f_resimid="$resimklasorad/$resim";else$f_resimid="$f_resimid,$resimklasorad/$resim";
                if(BosMu($f_resimidler))$f_resimidler=$resimid;else$f_resimidler="$f_resimidler,$resimid";
                if(BosMu($f_resimadlar))$f_resimadlar=$resimad;else$f_resimadlar="$f_resimadlar||$resimad";
            }
        }
        unset($sayfaresimler_s);

        $sayfadosyalar_s="
			Select 
				dosya.dosya,dosya.dosyaid,dosyaad,dosyauzanti 
			From 
				sayfalistedosya 
					inner join dosya on 
						dosya.dosyaid=sayfalistedosya.dosyaid 
			where 
				sayfalistedosya.sayfaid='". $f_sayfaid ."'";

        $sayfadosyalar_v=$db->select($sayfadosyalar_s);

        if($sayfadosyalar_v)
        {
            $sayfadosyalar_d=1;
            $sayfadosyalar=$sayfadosyalar_v;
            unset($sayfadosyalar_t);

        }

        $sayfavideolar_s="
			Select 
				video.video,video.videoid,videoad,videouzanti 
			From 
				sayfalistevideo 
					inner join video on 
						video.videoid=sayfalistevideo.videoid 
			where 
				sayfalistevideo.sayfaid='". $f_sayfaid ."'";

        $sayfavideolar_v=$db->select($sayfavideolar_s);

        if($db->select($sayfavideolar_v))
        {

            if($sayfavideolar_v)$sayfavideolar_d=1;
            if($sayfavideolar_d==1)
            {
                $sayfavideolar_d=1;
                $sayfavideolar=$sayfavideolar_v;
            }
            unset($sayfavideolar_v);
        }

        $sayfaseo_d=0;
        $sayfaseo_s="
			Select 
				baslik,aciklama,kelime 
			From 
				seo
			where 
				benzersizid='". $f_benzersizid ."'";
        $sayfaseo_v=$db->select($sayfaseo_s);
        if($sayfaseo_v)$sayfaseo_d=1;

        if($sayfaseo_d==1)
        {
            foreach($sayfaseo_v as $sayfaseo_t)
            {
                $f_seobaslik=stripslashes($sayfaseo_t["baslik"]);
                $f_seoaciklama=stripslashes($sayfaseo_t["aciklama"]);
                $f_seokelime=stripslashes($sayfaseo_t["kelime"]);
            }
        }
    }
    if(S($f_kategoriid)!=0)
    {
        tepekategoribul($f_kategoriid);
    }
}
?>
