<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("adresid"))!=0)
{
	veri(true);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
	</head>
	<body>
		<script>
			<?php
			$adresler_d=0;
			$adresler_s="select adresbaslik,adresad,adressoyad,adrestcno,adresulke,adressehir,adresilce,adressemt,adresmahalle,postakod,adresacik,adrestelefon from uyeadres where adressil='0' and adresid='".q("adresid")."' ";
			$adresler_v=$data->query($adresler_s);
			unset($adresler_s);
			if($adresler_v->num_rows>0)$adresler_d=1;
			if($adresler_d==1)
			{
				while($adresler_t=$adresler_v->fetch_assoc())
				{
					$l_adresbaslik=$adresler_t["adresbaslik"];
                    $l_adresad=$adresler_t["adresad"];
                    $l_adressoyad=$adresler_t["adressoyad"];
                    $l_adrestcno=$adresler_t["adrestcno"];
					$l_adresulkeid=$adresler_t["adresulke"];
					$l_adresulkead=teksatir(" Select CountryName from yerulke Where CountryID='". $l_adresulkeid ."'","CountryName");
					$l_adressehir=$adresler_t["adressehir"];
					$l_adresilce=$adresler_t["adresilce"];
					$l_adressemt=$adresler_t["adressemt"];
					$l_adresmahalle=$adresler_t["adresmahalle"];
					$l_postakod=$adresler_t["postakod"];
					$l_adresacik=$adresler_t["adresacik"];
					$l_adrestelefon=$adresler_t["adrestelefon"];
			
					echo 'if(window.parent.$("#yeniadresbaslik")){window.parent.$("#yeniadresbaslik").val(\''.$l_adresbaslik.'\');}';
                    echo 'if(window.parent.$("#yeniadresad")){window.parent.$("#yeniadresad").val(\''.$l_adresad.'\');}';
                    echo 'if(window.parent.$("#yeniadressoyad")){window.parent.$("#yeniadressoyad").val(\''.$l_adressoyad.'\');}';
                    echo 'if(window.parent.$("#yeniadrestcno")){window.parent.$("#yeniadrestcno").val(\''.$l_adrestcno.'\');}';
					echo 'if(window.parent.$("#yeniadrespostakod")){window.parent.$("#yeniadrespostakod").val(\''.$l_postakod.'\');}';
					echo 'if(window.parent.$("#yeniadresacik")){window.parent.$("#yeniadresacik").val(\''.$l_adresacik.'\');}';
					echo 'if(window.parent.$("#yeniadrestelefon")){window.parent.$("#yeniadrestelefon").val(\''.$l_adrestelefon.'\');}';
					echo 'if(window.parent.$("#yeniadresulkeid")){window.parent.$("#yeniadresulkeid").append(\'<option value="'.$l_adresulkeid.'" selected>'.$l_adresulkead.'</option>\');}';
					if(S($l_adresulkeid)==212)
					{
						echo '
						if(window.parent.$("#yeniadressehirid")){window.parent.$("#yeniadressehirid").show();}
						if(window.parent.$("#yeniadresilceid")){window.parent.$("#yeniadresilceid").show();}
						if(window.parent.$("#yeniadressemtid")){window.parent.$("#yeniadressemtid").show();}
						if(window.parent.$("#yeniadresmahalleid")){window.parent.$("#yeniadresmahalleid").show();}
						if(window.parent.$("#ayenidressehir")){window.parent.$("#yeniadressehir").hide();}
						if(window.parent.$("#yeniadressehir")){window.parent.$("#yeniadressehir").val("");}
						if(window.parent.$("#yeniadresilce")){window.parent.$("#yeniadresilce").hide();}
						if(window.parent.$("#yeniadresilce")){window.parent.$("#yeniadresilce").val("");}
						if(window.parent.$("#yeniadressemt")){window.parent.$("#yeniadressemt").hide();}
						if(window.parent.$("#yeniadressemt")){window.parent.$("#yeniadressemt").val("");}
						if(window.parent.$("#yeniadresmahalle")){window.parent.$("#yeniadresmahalle").hide();}
						if(window.parent.$("#yeniadresmahalle")){window.parent.$("#yeniadresmahalle").val("");}
						';
						$adressehirad=teksatir(" Select CityName from yersehir Where CityID='". $l_adressehir ."'","CityName");
						echo 'if(window.parent.$("#yeniadressehirid")){window.parent.$("#yeniadressehirid").append(\'<option value="'.$l_adressehir.'" selected>'.$adressehirad.'</option>\');}';
						
						$adresilcead=teksatir(" Select CountyName from yerilce Where CountyID='". $l_adresilce ."'","CountyName");
						echo 'if(window.parent.$("#yeniadresilceid")){window.parent.$("#yeniadresilceid").append(\'<option value="'.$l_adresilce.'" selected>'.$adresilcead.'</option>\');}';
						
						$adressemtad=teksatir(" Select AreaName from yersemt Where AreaID='". $l_adressemt ."'","AreaName");
						echo 'if(window.parent.$("#yeniadressemtid")){window.parent.$("#yeniadressemtid").append(\'<option value="'.$l_adressemt.'" selected>'.$adressemtad.'</option>\');}';
						
						$adresmahallead=teksatir(" Select NeighborhoodName from yermahalle Where NeighborhoodID='". $l_adresmahalle ."'","NeighborhoodName");
						echo 'if(window.parent.$("#yeniadresmahalleid")){window.parent.$("#yeniadresmahalleid").append(\'<option value="'.$l_adresmahalle.'" selected>'.$adresmahallead.'</option>\');}';
					}
					else
					{
						echo '
						if(window.parent.$("#yeniadressehirid")){$("#yeniadressehirid").hide();}
						if(window.parent.$("#yeniadressehirid")){$("#yeniadressehirid").empty();}
						if(window.parent.$("#yeniadresilceid")){$("#yeniadresilceid").hide();}
						if(window.parent.$("#yeniadresilceid")){$("#yeniadresilceid").empty();}
						if(window.parent.$("#yeniadressemtid")){$("#yeniadressemtid").hide();}
						if(window.parent.$("#yeniadressemtid")){$("#yeniadressemtid").empty();}
						if(window.parent.$("#yeniadresmahalleid")){$("#yeniadresmahalleid").hide();}
						if(window.parent.$("#yeniadresmahalleid")){$("#yeniadresmahalleid").empty();}
						if(window.parent.$("#ayeniadressehir")){$("#yeniadressehir").show();}
						if(window.parent.$("#yeniadresilce")){$("#yeniadresilce").show();}
						if(window.parent.$("#yeniadressemt")){$("#yeniadressemt").show();}
						if(window.parent.$("#yeniadresmahalle")){$("#yeniadresmahalle").show();}
						';
						echo 'if(window.parent.$("#yeniadressehir")){window.parent.$("#yeniadressehir").val(\''.$l_adressehir.'\');}';
						echo 'if(window.parent.$("#yeniadresilce")){window.parent.$("#yeniadresilce").val(\''.$l_adresilce.'\');}';
						echo 'if(window.parent.$("#yeniadressemt")){window.parent.$("#yeniadressemt").val(\''.$l_adressemt.'\');}';
						echo 'if(window.parent.$("#yeniadresmahalle")){window.parent.$("#yeniadresmahalle").val(\''.$l_adresmahalle.'\');}';
					}
					
				}
				unset($adresler_t,$adresler_v);
			}
			unset($adresler_v);
			?>
		</script>
	</body>
</html>
<?php } ?>