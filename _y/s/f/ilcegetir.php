<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("sehirid"))!=0)
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
			$ilce_d=0; $ilce_v=""; $ilce_s="";
			$ilce_s="SELECT CountyID,CountyName FROM yerilce WHERE CityID='". q("sehirid") ."' ORDER BY CountyName ASC";
			if($data->query($ilce_s))
			{
				$ilce_v=$data->query($ilce_s);
				if($ilce_v->num_rows>0)$ilce_d=1;
				unset($ilce_s);
				if($ilce_d==1)
				{
					if(S(q("yeni"))==0)
					{
						echo 'if(window.parent.$("#ayarfirmailceid")){window.parent.$("#ayarfirmailceid").empty();window.parent.$("#ayarfirmailceid").append(\'<option value="0">İlçe Seçin</option>\');}';
						echo 'if(window.parent.$("#adresilceid")){window.parent.$("#adresilceid").empty();window.parent.$("#adresilceid").append(\'<option value="0">İlçe Seçin</option>\');}';
					}
					else
					{
						echo 'if(window.parent.$("#yeniadresilceid")){window.parent.$("#yeniadresilceid").empty();window.parent.$("#yeniadresilceid").append(\'<option value="0">İlçe Seçin</option>\');}';
					}
					while($ilce_t=$ilce_v->fetch_assoc())
					{
						$l_ilceid=$ilce_t["CountyID"];
						$l_ilceAd=$ilce_t["CountyName"];

						$option ='<option value="'. $l_ilceid .'">'. $l_ilceAd .'</option>';
						if(S(q("yeni"))==0)
						{
							echo 'if(window.parent.$("#ayarfirmailceid")){window.parent.$("#ayarfirmailceid").append(\''. $option .'\');}';
							echo 'if(window.parent.$("#adresilceid")){window.parent.$("#adresilceid").append(\''. $option .'\');}';
						}else{
							echo 'if(window.parent.$("#yeniadresilceid")){window.parent.$("#yeniadresilceid").append(\''. $option .'\');}';
						}
				
					}
					unset($ilce_t,$ilce_v);
				}
				unset($ilce_v);
			}
			?>
		</script>
	</body>
</html>
<?php } ?>