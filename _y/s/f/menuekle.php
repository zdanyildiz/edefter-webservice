<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
$f_dilid=$dilid;
$f_menuyer=f("ymenuyer");
$f_ustmenuid=f("ustmenuid");
$f_menukatman=f("menukatman");
$f_menuad=f("menugorunenad");
$f_menulink=f("menulink");
$f_menusira=f("ymenusira");
$f_nebenzersizid=f("nebenzersizid");
$f_benzersizid=f("benzersizid");
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>
	<body>
		<?php
		if(!BosMu($f_menuad) && S($f_menusira)!=0)
		{
			Veri(true);
			$tablo="menu";
			$sutunlar="dilid,menuyer,ustmenuid,menukatman,menuad,menulink,menusira,benzersizid,nebenzersizid";
			$degerler=$f_dilid."|*_".$f_menuyer."|*_".$f_ustmenuid."|*_".$f_menukatman."|*_".$f_menuad."|*_".$f_menulink."|*_".$f_menusira."|*_".$f_benzersizid."|*_".$f_nebenzersizid;
			if(BosMu($f_benzersizid))
			{
				$f_benzersizid=SifreUret(20,2);
				$f_nebenzersizid=$f_benzersizid;
				$degerler=$f_dilid."|*_".$f_menuyer."|*_".$f_ustmenuid."|*_".$f_menukatman."|*_".$f_menuad."|*_".$f_menulink."|*_".$f_menusira."|*_".$f_benzersizid."|*_".$f_nebenzersizid;
				ekle($sutunlar,$degerler,$tablo,13);
			}
			else
			{
				$sabit="benzersizid='".$f_benzersizid."'";
				guncelle($sutunlar,$degerler,$tablo,$sabit,13);
				echo '<script>$("#ustmenu'.$f_menusira.' option:selected", parent.document).remove();</script>';
			}

			echo '
				<script>
					$("#ustmenu'.$f_menusira.'", parent.document).append($("<option>", {
					    value: "'.$f_nebenzersizid.'",
					    text: "'.$f_menuad.'"
					}));
					$("#btn-popup-sil-kapat",parent.document).click();
				</script>
			';
		}
		?>
	</body>
</html>