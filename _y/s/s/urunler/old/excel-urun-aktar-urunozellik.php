<?php 
$formhata=0;
$formhataaciklama="";
$urun_sayfa_s="
	SELECT 
		urunid,
		altbaslik,
		marka,
		model,
		stokkodu,
		stok,
		fiyat,
		indirimsizfiyat,
		alisfiyat,
		parabirimid,renkgrupid,renkid,bedengrupid,bedenid,malzemegrupid,malzemeid,stok,aktif
	FROM 
		urunaktar
	WHERE 
		aktarimonay=1
	GROUP BY 
		stokkodu
";
if($data->query($urun_sayfa_s))
{
	$urun_sayfa_v=$data->query($urun_sayfa_s);unset($urun_sayfa_s);
	if($urun_sayfa_v->num_rows>0)
	{
		echo "<h1>Ürün özellik Aktarımı</h1><br><hr><br>";
		$sayfaid=0;
		$urungrupid=0;
 		$markaid=1;
 		$tedarikciid=1;
 		$urunhediye="";
		
		$urunkargosuresi=3;
		$urunsabitkargoucreti="0.00";
		
 		$fiyat="0.00";
 		$urunindirimsizfiyat="0.00";
 		$urunbayifiyat="0.00";
 		$urunalisfiyat="0.00";
 		$uruneskifiyatgoster=$eskifiyatgenel;
 		$uruntaksit=$taksitgenel;
 		$urunkdv=$kdvgenel;
		$stok=0;
 		$stokkodu="";
 		$model="";
 		$urunindirimorani="0";
 		$urunaciklama="";

 		$simdi=date("Y-m-d H:i:s");
 		$satisbaslangictarih=$simdi;
 		$satisbitistarih=date("Y-m-d",strtotime(date("Y-m-d") . " + 365 day")); 		
 		
 		$urunanasayfa=0;
 		$urunindirimde=0;
 		$urunyeni=0;
 		$uruntopluindirim=1;
 		$urunanindakargo=0;
 		$urunucretsizkargo=1;
 		$urunonsiparis=0;
 		$urunfiyatsor=1;
 		$urunkargo=0;
 		$urungununfirsati=0;
 		$urunkredikarti=$kredikartigenel;
 		$urunkapidaodeme=$kapidaodemegenel;
 		$urunhavaleodeme=$havaleodemegenel;
 		$urunsatisadet=0;
 		$urunindirimoranigoster=0;
 		$urunbedenid=0;
 		$urunbedengrupid=1;
 		$urunrenkgrupid=1;
 		$urunrenkid=0;
        $urunmalzemegrupid=1;
        $urunmalzemeid=0;

 		$aktif=0;
 		$urunparabirimid=1;

		$urunsay=0;
		while ($urun_sayfa_t=$urun_sayfa_v->fetch_assoc())
		{
			$urunsay++;
			$sayfaid 				=$urun_sayfa_t["urunid"];
			if(S($sayfaid)!=0)
			{				
				$markaid 			=$urun_sayfa_t["marka"];

				$stokkodu 			=$urun_sayfa_t["stokkodu"];
				$model 				=$urun_sayfa_t["model"];

				$stok 				=$urun_sayfa_t["stok"];

                $fiyat=$urun_sayfa_t["fiyat"];;
                $urunindirimsizfiyat=$urun_sayfa_t["indirimsizfiyat"];
                $urunbayifiyat="0.00";
                $urunalisfiyat=$urun_sayfa_t["alisfiyat"];

				$urunaciklama 		=$urun_sayfa_t["altbaslik"];

				$fiyat 				=$urun_sayfa_t["fiyat"];
				$urunindirimsizfiyat=$urun_sayfa_t["indirimsizfiyat"];
				$urunparabirimid 	=$urun_sayfa_t["parabirimid"];

				$urunrenkid 		=$urun_sayfa_t["renkid"];
				$urunbedenid 		=$urun_sayfa_t["bedenid"];
				$urunrenkgrupid 	=$urun_sayfa_t["renkgrupid"];
				$urunbedengrupid 	=$urun_sayfa_t["bedengrupid"];
                $urunmalzemeid 	    =$urun_sayfa_t["malzemeid"];
                $urunmalzemegrupid 	=$urun_sayfa_t["malzemegrupid"];

				$aktif 				=$urun_sayfa_t["aktif"];

				$sutunlar="sayfaid,urungrupid,markaid,tedarikciid,urunmodel,urunstokkodu,urunstok,urunsatisfiyat,urunalisfiyat,urunindirimsizfiyat,urunbayifiyat,urunkdv,urunhediye,uruntaksit,urunaciklama,urunkargosuresi,urunsabitkargoucreti,uruneskifiyatgoster,urunindirimorani,urunfiyatsontarih,urunanasayfa,urunindirimde,urunyeni,uruntopluindirim,urunanindakargo,urunucretsizkargo,urunonsiparis,urunfiyatsor,urunkargo,urunparabirim,urungununfirsati,urunkredikarti,urunkapidaodeme,urunhavaleodeme,urunsatisadet,urunindirimoranigoster,urunbedenid,urunbedengrupid,urunrenkid,urunrenkgrupid,urunmalzemeid,urunmalzemegrupid";

				$degerler=$sayfaid."|*_".$urungrupid."|*_".$markaid."|*_".$tedarikciid."|*_".$model."|*_".$stokkodu."|*_".$stok."|*_".$fiyat."|*_".$urunalisfiyat."|*_".$urunindirimsizfiyat."|*_".$urunbayifiyat."|*_".$urunkdv."|*_".$urunhediye."|*_".$uruntaksit."|*_".$urunaciklama."|*_".$urunkargosuresi."|*_".$urunsabitkargoucreti."|*_".$uruneskifiyatgoster."|*_".$urunindirimorani."|*_".$satisbitistarih."|*_".$urunanasayfa."|*_".$urunindirimde."|*_".$urunyeni."|*_".$uruntopluindirim."|*_".$urunanindakargo."|*_".$urunucretsizkargo."|*_".$urunonsiparis."|*_".$urunfiyatsor."|*_".$urunkargo."|*_".$urunparabirimid."|*_".$urungununfirsati."|*_".$urunkredikarti."|*_".$urunkapidaodeme."|*_".$urunhavaleodeme."|*_".$urunsatisadet."|*_".$urunindirimoranigoster."|*_".$urunbedenid."|*_".$urunbedengrupid."|*_".$urunrenkid."|*_".$urunrenkgrupid."|*_".$urunmalzemeid."|*_".$urunmalzemegrupid;
				if(dogrula("urunozellikleri","urunstokkodu='".$stokkodu."'"))
				{
					$sutunlar="
						sayfaid,
						markaid,
						urunmodel,
						urunstok,
						urunsatisfiyat,
						urunindirimsizfiyat,
						urunalisfiyat,
						urunbayifiyat,
						urunaciklama,
						urunparabirim,
						urunbedenid,
						urunbedengrupid,
						urunrenkid,
						urunrenkgrupid,
						urunmalzemeid,
						urunmalzemegrupid
					";

					$degerler=
						$sayfaid."|*_".
						$markaid."|*_".
						$model."|*_".
						$stok."|*_".
						$fiyat."|*_".
						$urunindirimsizfiyat."|*_".
                        $urunalisfiyat."|*_".
                        $urunbayifiyat."|*_".
						$urunaciklama."|*_".		
						$urunparabirimid."|*_".
						$urunbedenid."|*_".
						$urunbedengrupid."|*_".
						$urunrenkid."|*_".
						$urunrenkgrupid."|*_".
                        $urunmalzemeid."|*_".
                        $urunmalzemegrupid
						;
					guncelle($sutunlar,$degerler,"urunozellikleri","urunstokkodu='".$stokkodu."'",0);
                    if($urunsay==1)echo '<br>güncelleniyor..<br>lütfen bekleyiniz';
				}
				else
				{
					ekle($sutunlar,$degerler,"urunozellikleri",0);
                    if($urunsay==1)echo '<br>ekleniyor..<br>lütfen bekleyiniz';
				}
				
				guncelle("sayfaaktif","$aktif","sayfa","sayfaid='".$sayfaid."'",0);
				guncelle("aktarimonay","2","urunaktar","stokkodu='".$stokkodu."'",0);
                if($urunsay>=100)die('<script>window.location.href ="/_y/s/s/urunler/excel-urun-yukle.php?adim=11";</script>');
			}
			else
			{
				echo "Sayfa Adı / Ürün Başlığı boş olamaz<br>";
			}
		}unset($urun_sayfa_t);
	}unset($urun_sayfa_v);
}else{hatalogisle("Ürün Aktar - Marka",$data->error);}
?>