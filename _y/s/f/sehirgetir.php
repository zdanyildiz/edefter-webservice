<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("ulkeid"))!=0)
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
			$sehir_d=0; $sehir_v=""; $sehir_s="";
			$sehir_s="SELECT CityID,CityName FROM yersehir WHERE CountryID='". q("ulkeid") ."' ORDER BY CityName ASC";
			$sehir_v=$data->query($sehir_s);
			if($sehir_v->num_rows>0)$sehir_d=1;
			unset($sehir_s);
			if($sehir_d==1)
			{
				if(S(q("yeni"))==0)
				{
					echo 'if(window.parent.$("#ayarfirmasehirid")){window.parent.$("#ayarfirmasehirid").empty();window.parent.$("#ayarfirmasehirid").append(\'<option value="0">Şehir Seçin</option>\');}';
					echo 'if(window.parent.$("#adressehirid")){window.parent.$("#adressehirid").empty();window.parent.$("#adressehirid").append(\'<option value="0">Şehir Seçin</option>\');}';
				}
				else
				{
					echo 'if(window.parent.$("#yeniadressehirid")){window.parent.$("#yeniadressehirid").empty();window.parent.$("#yeniadressehirid").append(\'<option value="0">Şehir Seçin</option>\');}';
				}
				while($sehir_t=$sehir_v->fetch_assoc())
				{
					$l_sehirid=$sehir_t["CityID"];
					$l_sehirAd=$sehir_t["CityName"];
			
					$option ='<option value="'. $l_sehirid .'">'. $l_sehirAd .'</option>';
					if(S(q("yeni"))==0)
					{
						echo 'if(window.parent.$("#ayarfirmasehirid")){window.parent.$("#ayarfirmasehirid").append(\''. $option .'\');}';
						echo 'if(window.parent.$("#adressehirid")){window.parent.$("#adressehirid").append(\''. $option .'\');}';
					}
					else
					{
						echo 'if(window.parent.$("#yeniadressehirid")){window.parent.$("#yeniadressehirid").append(\''. $option .'\');}';
					}
				}
				unset($sehir_t,$sehir_v);
			}
			unset($sehir_v);
			?>
		</script>
	</body>
</html>
<?php } ?>