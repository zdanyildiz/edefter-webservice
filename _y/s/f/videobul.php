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
			$videosql_ek="WHERE videoad like '%".q("r")."%'";
			if(q("r")=="hepsi999")$videosql_ek="";
			$video_d=0;
				$video_s="
					SELECT
						videoid,videoad,video,videoboyut,videouzanti
					FROM 
						video 
					$videosql_ek
					Order By videoad ASC
				";
				if($data->query($video_s))
				{
					$video_v=$data->query($video_s);
					if($video_v->num_rows>0)$video_d=1;
				}
				unset($video_s);
				?>
				<?php
				$aramasonuc="";
				if($video_d==1)
				{
					while ($video_t=$video_v->fetch_assoc()) 
					{
						$videoid=$video_t["videoid"];
						$videoad = $video_t["videoad"];
						$video = $video_t["video"];
						$videouzanti=$video_t["videouzanti"];
						if(BosMu($videouzanti))$videouzanti="youtube";
						$videoboyut = $video_t["videoboyut"];
						$videoboyutyaz='';
						if(round($videoboyut/1024)>1024){$videoboyutyaz = round(round($videoboyut/1024)/1024) ." mb";}else $videoboyutyaz= round($videoboyut/1024) ." kb";
						$aramasonuc=$aramasonuc.'
						<li class="tile">
							<a 
								class="tile-content ink-reaction videosec" 
								data-id="'.$videoid.'" 
								data-ad="'.$videoad.'" 
								data-boyut="'.$videoboyut.'" 
								data-uzanti="'.$videouzanti.'" 
								data-link="'.$video.'" 
								data-backdrop="false" style="cursor:pointer;">
								<div class="tile-icon">
									<img src="/_y/assets/img/'.$videouzanti.'.png" alt="'.$videoad.'" />
								</div>
								<div class="tile-text">
									'.$videoad.'
									<small>'.$videoboyutyaz.'</small>
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
				$("#videolistekutu",parent.document).html($aramasonuclari);
			}
		</script>
	</body>
</html>