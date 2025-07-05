<?php
$tepekategoriid=0;$tepekategori="";
function tepekategoribul($strkategoriid)
{
	global $db,$tepekategoriid,$tepekategoriad,$tepekategorilink,$tepekategori;
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
	$sayfabilgileri_d=0;
	$sayfabilgileri_s="
		SELECT
			benzersizid,
			sayfatip,
			sayfaad,
			sayfaicerik,
			sayfalink,
			sayfasira,
			sayfaaktif,
			sayfasil
		FROM
			sayfa
		WHERE
			sayfasil='0' and sayfa.sayfaid='".$f_sayfaid."'
	";
	if($db->select($sayfabilgileri_s))
	{
		$sayfabilgileri_v=$db->select($sayfabilgileri_s);
		if($sayfabilgileri_v)$sayfabilgileri_d=1;
		if($sayfabilgileri_d==1)
		{
			$butonisim=" GÜNCELLE ";
            if(s(q("kopyala"))==1){$butonisim="KAYDET";}
			foreach($sayfabilgileri_v as $sayfabilgileri_t)
			{
				$f_sayfaid 		=S(q("sayfaid"));
				$f_benzersizid	=$sayfabilgileri_t["benzersizid"];
				$f_sayfagrup 	=$sayfabilgileri_t["sayfatip"];
				$f_sayfaad 		=$sayfabilgileri_t["sayfaad"];
				$f_sayfaicerik 	=$sayfabilgileri_t["sayfaicerik"];
				if(!BosMu($f_sayfaicerik)){$sayfaicerik=htmlspecialchars_decode($f_sayfaicerik);}
				$f_sayfalink 	=$sayfabilgileri_t["sayfalink"];
				$f_sayfasira 	=$sayfabilgileri_t["sayfasira"];
				$f_sayfaaktif 	=$sayfabilgileri_t["sayfaaktif"];
				$f_sayfasil 	=$sayfabilgileri_t["sayfasil"];

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
				foreach($ozelliksql_v as $ozelliksql_t)
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
					$f_urunbedenid 			= $ozelliksql_t["urunbedenid"];
					$f_urunbedengrupid 		= $ozelliksql_t["urunbedengrupid"];
					$f_urunmodel	 		= $ozelliksql_t["urunmodel"];
					$f_urunrenkid 			= $ozelliksql_t["urunrenkid"];
					$f_urunrenkgrupid 		= $ozelliksql_t["urunrenkgrupid"];
                    $f_urunmalzemeid 		= $ozelliksql_t["urunmalzemeid"];
                    $f_urunmalzemegrupid 	= $ozelliksql_t["urunmalzemegrupid"];
                    $f_urunpinid 			= $ozelliksql_t["urunpinid"];

                    $f_urundesi 		    = $ozelliksql_t["urundesi"];
                    $f_urunminimummiktar 	= $ozelliksql_t["urunminimummiktar"];
                    $f_urunmaksimummiktar 	= $ozelliksql_t["urunmaksimummiktar"];
                    $f_urunkatsayi 			= $ozelliksql_t["urunkatsayi"];
					$f_urunmiktarbirimid	= $ozelliksql_t["urunmiktarbirimid"];
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
