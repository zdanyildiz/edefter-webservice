<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("semtid"))!=0)
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
			$mahalle_d=0; $mahalle_v=""; $mahalle_s="";
			$mahalle_s="SELECT NeighborhoodID,NeighborhoodName FROM yermahalle WHERE AreaID='". q("semtid") ."' ORDER BY NeighborhoodName ASC";
			$mahalle_v=$data->query($mahalle_s);
			if($mahalle_v->num_rows>0)$mahalle_d=1;
			unset($mahalle_s);
			if($mahalle_d==1)
			{
				if(S(q("yeni"))==0)
				{
					echo 'if(window.parent.$("#ayarfirmamahalleid")){window.parent.$("#ayarfirmamahalleid").empty();window.parent.$("#ayarfirmamahalleid").append(\'<option value="0">Mahalle Seçin</option>\');}';
					echo 'if(window.parent.$("#adresmahalleid")){window.parent.$("#adresmahalleid").empty();window.parent.$("#adresmahalleid").append(\'<option value="0">Mahalle Seçin</option>\');}';
				}else{
					echo 'if(window.parent.$("#yeniadresmahalleid")){window.parent.$("#yeniadresmahalleid").empty();window.parent.$("#yeniadresmahalleid").append(\'<option value="0">Mahalle Seçin</option>\');}';
				}
				while($mahalle_t=$mahalle_v->fetch_assoc())
				{
					$l_mahalleid=$mahalle_t["NeighborhoodID"];
					$l_mahalleAd=$mahalle_t["NeighborhoodName"];
			
					$option ='<option value="'. $l_mahalleid .'">'. $l_mahalleAd .' </option>';
					if(S(q("yeni"))==0)
					{
						echo 'if(window.parent.$("#ayarfirmamahalleid")){window.parent.$("#ayarfirmamahalleid").append(\''. $option .'\');}';
						echo 'if(window.parent.$("#adresmahalleid")){window.parent.$("#adresmahalleid").append(\''. $option .'\');}';
					}else{
						echo 'if(window.parent.$("#yeniadresmahalleid")){window.parent.$("#yeniadresmahalleid").append(\''. $option .'\');}';
					}
				}
				unset($mahalle_t,$mahalle_v);
			}
			unset($mahalle_v);
			?>
		</script>
	</body>
</html>
<?php } ?>