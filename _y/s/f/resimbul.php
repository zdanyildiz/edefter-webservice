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
			$kelimeayir=explode(" ", q("r"));
			$sqlek="";
			foreach ($kelimeayir as $i => $kelime)
			{
				$sqlek.=" or resimad like '%".$kelime."%' or resim like '%".$kelime."%' or resimad like '%".Duzelt($kelime)."%' or resim like '%".Duzelt($kelime)."%' ";
			}

			$resimsql_ek="WHERE resimad like '%".q("r")."%' or resim like '%".q("r")."%' $sqlek ";
			//die($resimsql_ek);
			if(q("r")=="hepsi999")$resimsql_ek="";
			$resim_d=0;
				$resim_s=
				"
					SELECT
						resimid,resimklasorad,resimad,resim,ren,rboy
					FROM 
						resim 
							inner join resimklasor 
								on resimklasor.resimklasorid=resim.resimklasorid
					$resimsql_ek
					Order By resimad ASC
				";
				if($data->query($resim_s))
				{
					$resim_v=$data->query($resim_s);
					if($resim_v->num_rows>0)$resim_d=1;
				}
				unset($resim_s);
				?>
				<?php
				$aramasonuc="";
				if($resim_d==1)
				{
					$ii=0;
					while ($resim_t=$resim_v->fetch_assoc()) 
					{
						$ii++;
						$resimid=$resim_t["resimid"];
						$resimad = $resim_t["resimad"];
						$resim = $resim_t["resim"];
						$resimklasorad=$resim_t["resimklasorad"];
						$ren = $resim_t["ren"];
						$rboy = $resim_t["rboy"];
						
						$aramasonuc=$aramasonuc.'
						<li class="tile">
							<a 
								class="tile-content ink-reaction resimsec" 
								data-id="'.$resimid.'" 
								data-ad="'.$resimad.'" 
								data-en="'.$ren.'" 
								data-boy="'.$rboy.'" 
								data-link="'.$resimklasorad.'/'.$resim.'" 
								data-backdrop="false" style="cursor:pointer;">
								<div class="tile-icon">
									<img src="/m/r/'.$resimklasorad.'/'.$resim.'" alt="" />
								</div>
								<div class="tile-text">
									'.$resimad.'
									<small>'.$resimklasorad.' ('.$ii.')</small>
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
				$("#resimlistekutu",parent.document).html($aramasonuclari);
			}
		</script>
	</body>
</html>