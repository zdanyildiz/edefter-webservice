<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("kategoriid"))!=0)
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
			$kelimeler_s="
				Select 
					kelime 
				From 
					seo 
						inner join sayfa on 
							sayfa.benzersizid=seo.benzersizid 
							inner join sayfalistekategori on 
								sayfalistekategori.sayfaid=sayfa.sayfaid 
								inner join kategori on 
									kategori.kategoriid=sayfalistekategori.kategoriid
				Where sayfalistekategori.kategoriid='".q("kategoriid")."'

			";
			$seokelimeler="";
			if($data->query($kelimeler_s))
			{
				$kelimeler_v=$data->query($kelimeler_s);
				if($kelimeler_v->num_rows>0)
				{															
					while ($kelimeler_t=$kelimeler_v->fetch_assoc())
					{
						$kelimesatir=$kelimeler_t["kelime"];
						$kelimesatir=ltrim($kelimesatir,",");
						$kelimesatir=rtrim($kelimesatir,",");
						$kelimesatir=str_replace(",,", ",", $kelimesatir);
						if(!BosMu($kelimesatir))
						{
							$seotumkelimeler = explode(",", $kelimesatir);
							foreach($seotumkelimeler as $kelime)
							{
								if(strpos($seokelimeler, $kelime)===false)
								{
echo 'window.parent.$(".bootstrap-tagsinput").append(\'<span class="tag label label-info" data-key="'.$kelime.'" style="white-space:normal;font-size:85%">'.$kelime.'</span>\');';
								}
							}
							$seokelimeler=$seokelimeler.",".$kelimesatir;
						}
					}
				}
			}
			?>
		</script>
	</body>
</html>
<?php } ?>