
								 		
								 		
								 		
								 		
								 		
								 		
								 		
//////////////////////
$sutunlar="";$degerler="";
								 	if($kategoriid!=0 && $markaid!=0 && !BosMu($stokkodu) && !BosMu($model) && !BosMu($baslik))
							 		{
							 			echo "$kategoriad ($kategoriid) | $markaad ($markaid) | $model | $baslik <br>";
							 			$simdi=date("Y-m-d H:i:s");
							 			$sutunlar="sayfatarihguncel,sayfatip,sayfaad,sayfaicerik,sayfalink,sayfasira,sayfaaktif,sayfasil";
							 			$degerler=$simdi."|*_7|*_".addslashes($baslik)."|*_".addslashes($aciklama)."|*_|*_0|*_1|*_0";
							 			if(!Dogrula("urunozellikleri","urunmodel='".addslashes($model)."' and sayfaid='".$sayfaid."'"))
							 			{
							 				$sayfaid=0;
							 			}
							 			if(Dogrula("urunozellikleri","urunmodel='".addslashes($model)."' and urunstokkodu='".$stokkodu."'"))
							 			{
							 				$sayfaid=teksatir("SELECT sayfaid FROM urunozellikleri WHERE urunmodel='".addslashes($model)."' and urunstokkodu='".$stokkodu."'","sayfaid");
							 			}
							 			if($sayfaid!=0)
							 			{
							 				/*guncelle($sutunlar,$degerler,"sayfa","sayfaid='".$sayfaid."'",37);
							 				$benzersizid=teksatir("SELECT benzersizid FROM sayfa WHERE sayfaid='".$sayfaid."'","benzersizid");
							 				echo "Sayfa Güncellendi ID: $sayfaid<br>";
							 				$data->query("DELETE FROM sayfalistekategori WHERE sayfaid='". $sayfaid ."'");
											$data->query("DELETE FROM urunozellikleri WHERE sayfaid='". $sayfaid ."'");
											$data->query("DELETE FROM seo WHERE benzersizid='". $benzersizid ."'");*/
							 			}
							 			else
							 			{
							 				$benzersizid=SifreUret(20,2);
							 				$sutunlar="benzersizid,sayfatariholustur,".$sutunlar;
							 				$degerler=$benzersizid."|*_".$simdi."|*_".$degerler;
							 				if(!Dogrula("sayfa","sayfaad='".addslashes($baslik)."' and sayfaicerik='".addslashes($aciklama)."' and sayfaaktif='1' and sayfasil='0'"))ekle($sutunlar,$degerler,"sayfa",37);
							 				$sayfaid=teksatir("SELECT sayfaid FROM sayfa WHERE benzersizid='".$benzersizid."'","sayfaid");
							 				echo "Sayfa Eklendi ID: ($sayfaid)<br>";
							 			}
							 			if(!Dogrula("sayfalistekategori","sayfaid='".$sayfaid."' and kategoriid='".$kategoriid."'"))ekle("kategoriid,sayfaid",$kategoriid."|*_".$sayfaid,"sayfalistekategori",56);
							 			echo "Kategori Listesi Eklendi <br>";
										$seobaslik=mb_substr(addslashes($baslik),0,65,"UTF-8");
							 			$seoaciklama="Kategori: $kategoriad, Marka: $markaad, Model: $model, Ürün: $seobaslik, Fiyat:$fiyat $parabirim";
							 			$seokelime="$kategoriad,$markaad,$model,$fiyat";
							 			$seolink=DuzeltS(K($kategoriad."/".$markaad."/".$seobaslik))."/".$sayfaid."s.html";
							 			if(S($sayfaid)!=0 and !Dogrula("seo","baslik='".$seobaslik."' and aciklama='".$seoaciklama."' and kelime='".$seokelime."' and link='".$seolink."'"))ekle("benzersizid,baslik,aciklama,kelime,link,resim",$benzersizid."|*_".$seobaslik."|*_".$seoaciklama."|*_".$seokelime."|*_".$seolink."|*_","seo",56);
							 			echo "SEO Eklendi<br>";

							 			if(!BosMu($resimler))
							 			{
							 				$resimayikla=explode(",", $resimler);
								 			$resimtoplam=count($resimayikla);
								 			
								 			if(S($resimtoplam)>1)
								 			{
								 				for ($ri=0; $ri < $resimtoplam; $ri++)
								 				{ 
								 					$resim=$resimayikla[$ri];
								 					$orjinal=$resim;
								 					if(substr($resim, 0, 4)=="http")
								 					{
														/*$resimek=SifreUret(5,2);
														$uzanti=substr($resim, strrpos($resim, "."), strlen($resim)-strrpos($resim, "."));
														$img = $_SERVER['DOCUMENT_ROOT'].'/m/r/urun/'.Duzelt(K($seobaslik))."_".$resimek.$uzanti;
														file_put_contents($img, file_get_contents($resim));
														$resim=Duzelt(K($seobaslik))."_".$resimek.$uzanti;*/
								 					}
								 					$sutunlar="";$degerler="";
								 					$benzersizid=SifreUret(20,2);
								 					$sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
								 					$degerler=$resimklasor."|*_".addslashes($seobaslik)."|*_".$resim."|*_0|*_0|*_".$benzersizid."|*_".$orjinal;
								 					if(!Dogrula("resim","orjinal='".$orjinal."'"))
								 					{
								 						//ekle($sutunlar,$degerler,"resim",26);
									 					//$resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$benzersizid."'","resimid");
									 					echo "Resim ($resim) Eklendi ResimID: $resimid<br>";
									 					//ekle("sayfaid,resimid",$sayfaid."|*_".$resimid,"sayfalisteresim",26);
									 					echo "Sayfa Resim Liste Eklendi SayfaID: $sayfaid ResimID: $resimid<br>";
								 					}
								 				}
								 			}
								 			else
								 			{
								 				$orjinal=$resimler;
								 				if(substr($resimler, 0, 4)=="http")
							 					{
													/*$resimek=SifreUret(5,2);
													$uzanti=substr($resimler, strrpos($resimler, "."), strlen($resimler)-strrpos($resimler, "."));
													$img = $_SERVER['DOCUMENT_ROOT'].'/m/r/urun/'.Duzelt(K($seobaslik))."_".$resimek.$uzanti;
													file_put_contents($img, file_get_contents($resimler));
													$resimler=Duzelt(K($seobaslik))."_".$resimek.$uzanti;*/
							 					}
							 					$sutunlar="";$degerler="";
							 					$benzersizid=SifreUret(20,2);
							 					$sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid,orjinal";
							 					$degerler=$resimklasor."|*_".addslashes($seobaslik)."|*_".$resimler."|*_0|*_0|*_".$benzersizid."|*_".$orjinal;
							 					/*if(!Dogrula("resim","orjinal='".$orjinal."'"))
							 					{
							 						ekle($sutunlar,$degerler,"resim",26);
								 					$resimid=teksatir("SELECT resimid FROM resim WHERE benzersizid='".$benzersizid."'","resimid");
								 					echo "Resim (n:$resimler) Eklendi ResimID: $resimid<br>";
									 				ekle("sayfaid,resimid",$sayfaid."|*_".$resimid,"sayfalisteresim",26);
									 				echo "Sayfa Resim Liste Eklendi SayfaID: $sayfaid ResimID: $resimid<br>";
							 					}*/
								 			}
							 			}
								 		$sutunlar="";$degerler="";
							 			$satisbitistarih = date("Y-m-d", strtotime($satisbitistarih));
							 			$sutunlar="sayfaid,urungrupid,markaid,tedarikciid,urunmodel,urunstokkodu,urunstok,urunsatisfiyat,urunalisfiyat,urunindirimsizfiyat,urunbayifiyat,urunkdv,urunhediye,uruntaksit,urunaciklama,urunkargosuresi,urunsabitkargoucreti,uruneskifiyatgoster,urunindirimorani,urunfiyatsontarih,urunanasayfa,urunindirimde,urunyeni,uruntopluindirim,urunanindakargo,urunucretsizkargo,urunonsiparis,urunfiyatsor,urunkargo,urunparabirim,urungununfirsati,urunkredikarti,urunkapidaodeme,urunhavaleodeme,urunsatisadet,urunindirimoranigoster,urunbedenid,urunbedengrupid,urunrenkgrupid,urunrenkid";
							 			$degerler=$sayfaid."|*_".$urunrenkgrupid."|*_".$markaid."|*_".$tedarikciid."|*_".$model."|*_".$stokkodu."|*_".$stok."|*_".$fiyat."|*_".$urunalisfiyat."|*_".$urunindirimsizfiyat."|*_".$urunbayifiyat."|*_".$urunkdv."|*_".$urunhediye."|*_".$uruntaksit."|*_".$altbaslik."|*_".$urunkargosuresi."|*_".$urunsabitkargoucreti."|*_".$uruneskifiyatgoster."|*_".$urunindirimorani."|*_".$satisbitistarih."|*_".$urunanasayfa."|*_".$urunindirimde."|*_".$urunyeni."|*_".$uruntopluindirim."|*_".$urunanindakargo."|*_".$urunucretsizkargo."|*_".$urunonsiparis."|*_".$urunfiyatsor."|*_".$urunkargo."|*_".$urunparabirimid."|*_".$urungununfirsati."|*_".$urunkredikarti."|*_".$urunkapidaodeme."|*_".$urunhavaleodeme."|*_".$urunsatisadet."|*_".$urunindirimoranigoster."|*_".$urunbedenid."|*_".$urunbedengrupid."|*_".$urunrenkid."|*_".$urunrenkgrupid;
							 			//if(S($sayfaid)!=0)ekle($sutunlar,$degerler,"urunozellikleri",63);
							 			echo "Ürün Özellikleri Eklendi<br><hr><br>";
							 			$resimler="";$sayfaid=0;$kategoriid=0;$markaid=0;$stokkodu="";$model="";$baslik="";
							 		}