<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("ilceid"))!=0)
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
			$semt_d=0; $semt_v=""; $semt_s="";
			$semt_s="SELECT AreaID,AreaName FROM yersemt WHERE CountyID='". q("ilceid") ."' ORDER BY AreaName ASC";
			$semt_v=$data->query($semt_s);
			if($semt_v->num_rows>0)$semt_d=1;
			unset($semt_s);
			if($semt_d==1)
			{
				if(S(q("yeni"))==0)
				{
					echo 'if(window.parent.$("#ayarfirmasemtid")){window.parent.$("#ayarfirmasemtid").empty();window.parent.$("#ayarfirmasemtid").append(\'<option value="0">Semt Seçin</option>\');}';
					echo 'if(window.parent.$("#adressemtid")){window.parent.$("#adressemtid").empty();window.parent.$("#adressemtid").append(\'<option value="0">Semt Seçin</option>\');}';
				}else{
					echo 'if(window.parent.$("#yeniadressemtid")){window.parent.$("#yeniadressemtid").empty();window.parent.$("#yeniadressemtid").append(\'<option value="0">Semt Seçin</option>\');}';
				}
				while($semt_t=$semt_v->fetch_assoc())
				{
					$l_semtid=$semt_t["AreaID"];
					$l_semtAd=$semt_t["AreaName"];
					
					$option ='<option value="'. $l_semtid .'">'. $l_semtAd .'</option>';
					if(S(q("yeni"))==0)
					{
						echo 'if(window.parent.$("#ayarfirmasemtid")){window.parent.$("#ayarfirmasemtid").append(\''. $option .'\');}';
						echo 'if(window.parent.$("#adressemtid")){window.parent.$("#adressemtid").append(\''. $option .'\');}';
					}else{
						echo 'if(window.parent.$("#yeniadressemtid")){window.parent.$("#yeniadressemtid").append(\''. $option .'\');}';
					}
				}
				unset($semt_t,$semt_v);
			}
			unset($semt_v);
			?>
		</script>
	</body>
</html>
<?php } ?>