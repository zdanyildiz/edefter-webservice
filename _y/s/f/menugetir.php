<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>
	<body>
		<?php
		if(S(q("menuyer"))!=0 && !BosMu(q("nebenzersizid")))
		{
			Veri(true);
			$menu_s="
				SELECT 
					menulink,benzersizid 
				FROM 
					menu 
				WHERE 
					menuyer='". q("menuyer") ."' AND nebenzersizid='". q("nebenzersizid") ."'
			";
			if($data->query($menu_s))
			{
				$menu_v=$data->query($menu_s);unset($menu_s);
				if($menu_v->num_rows>0)
				{
					$menulink="";$benzersizid="";
					while ($menu_t=$menu_v->fetch_assoc())
					{
						$menulink=$menu_t["menulink"];
						$benzersizid=$menu_t["benzersizid"];
					}unset($menu_t);
					echo '
						<script>
							$("#menulink",parent.document).val("'.$menulink.'");
							$("#benzersizid",parent.document).val("'.$benzersizid.'");
						</script>
					';unset($menulink,$benzersizid);
				}
				else
				{
					$menu_s="
						SELECT 
							link,benzersizid 
						FROM 
							seo 
						WHERE 
							benzersizid='". q("nebenzersizid") ."'
					";
					if($data->query($menu_s))
					{
						$menu_v=$data->query($menu_s);unset($menu_s);
						if($menu_v->num_rows>0)
						{
							$menulink="";$benzersizid="";
							while ($menu_t=$menu_v->fetch_assoc())
							{
								$menulink=$menu_t["link"];
								$benzersizid=$menu_t["benzersizid"];
							}unset($menu_t);
							echo '
								<script>
									$("#menulink",parent.document).val("'.$menulink.'");
									$("#benzersizid",parent.document).val("'.$benzersizid.'");
								</script>
							';unset($menulink,$benzersizid);
						}else{echo '<script>alert("Menü Bulunamadı");</script>';}
					}else{hatalogisle("menü getir seo",$data->error);}
				}unset($menu_v);
			}else{hatalogisle("menügetir",$data->error);}
		}
		?>
	</body>
</html>