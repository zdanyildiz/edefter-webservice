<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("mahalleid"))!=0)
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
			$mahalle_s="SELECT ZipCode FROM yermahalle WHERE NeighborhoodID='". q("mahalleid") ."' ";
			$mahalle_v=$data->query($mahalle_s);
			if($mahalle_v->num_rows>0)$mahalle_d=1;
			unset($mahalle_s);
			if($mahalle_d==1)
			{
				while($mahalle_t=$mahalle_v->fetch_assoc())
				{
					$l_mahalleid=$mahalle_t["ZipCode"];
					if(S(q("yeni"))==0)
					{
						echo 'if(window.parent.$("#ayarfirmapostakod"))window.parent.$("#ayarfirmapostakod").val("'. $l_mahalleid .'");';
						echo 'if(window.parent.$("#adrespostakod"))window.parent.$("#adrespostakod").val("'. $l_mahalleid .'");';
					}else{
						echo 'if(window.parent.$("#yeniadrespostakod"))window.parent.$("#yeniadrespostakod").val("'. $l_mahalleid .'");';
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