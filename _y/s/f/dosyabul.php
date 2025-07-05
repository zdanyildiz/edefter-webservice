<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>
	<body>
		<?php
		if(!BosMu(q("r")))
		{
			Veri(true);
			$dosyasql_ek="WHERE dosyaad like '%".q("r")."%'";
			if(q("r")=="hepsi999")$dosyasql_ek="";
			$dosya_d=0;
				$dosya_s="
					SELECT
						dosyaid,dosyaad,dosya,dosyaboyut,dosyauzanti
					FROM 
						dosya 
					$dosyasql_ek
					Order By dosyaad ASC
				";
				if($data->query($dosya_s))
				{
					$dosya_v=$data->query($dosya_s);
					if($dosya_v->num_rows>0)$dosya_d=1;
				}
				unset($dosya_s);
				?>
				<?php
				$aramasonuc="";
				if($dosya_d==1)
				{
					while ($dosya_t=$dosya_v->fetch_assoc()) 
					{
						$dosyaid=$dosya_t["dosyaid"];
						$dosyaad = $dosya_t["dosyaad"];
						$dosya = $dosya_t["dosya"];
						$dosyauzanti=$dosya_t["dosyauzanti"];
						$dosyaboyut = $dosya_t["dosyaboyut"];
						$dosyaboyutyaz='';
						if(round($dosyaboyut/1024)>1024){$dosyaboyutyaz = round(round($dosyaboyut/1024)/1024) ." mb";}else $dosyaboyutyaz= round($dosyaboyut/1024) ." kb";
						$aramasonuc=$aramasonuc.'
						<li class="tile">
							<a 
								class="tile-content ink-reaction dosyasec" 
								data-id="'.$dosyaid.'" 
								data-ad="'.$dosyaad.'" 
								data-boyut="'.$dosyaboyut.'" 
								data-uzanti="'.$dosyauzanti.'" 
								data-link="'.$dosya.'" 
								data-backdrop="false" style="cursor:pointer;">
								<div class="tile-icon">
									<img src="/_y/assets/img/'.$dosyauzanti.'.png" alt="'.$dosyaad.'" />
								</div>
								<div class="tile-text">
									'.$dosyaad.'
									<small>'.$dosyaboyutyaz.'</small>
								</div>
							</a>
						</li>';
					}
					echo '<textarea id="aramasonuclari">'.$aramasonuc.'</textarea>';
				}
		}
		?>
		<script>
			if($("#aramasonuclari"))
			{
				$aramasonuclari=$("#aramasonuclari").val();
				$("#dosyalistekutu",parent.document).html($aramasonuclari);
			}
		</script>
	</body>
</html>