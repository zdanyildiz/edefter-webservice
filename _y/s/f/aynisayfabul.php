<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>
	<body>
	<?php
	
	Veri(true);
	$urunler_d=0;
	$urunler_s="
		SELECT 
			a.sayfaid,a.sayfaad,kategoriad,kategori.kategoriid
		FROM 
			sayfa a
				JOIN (SELECT sayfaad, sayfaid, COUNT(*) FROM sayfa GROUP BY sayfaad HAVING count(*) > 1 ) b
					ON a.sayfaad = b.sayfaad
				INNER JOIN
					sayfalistekategori on
						sayfalistekategori.sayfaid=a.sayfaid
					INNER JOIN kategori ON kategori.kategoriid=sayfalistekategori.kategoriid
		ORDER BY a.sayfaad
	";
	if(!$data->query($urunler_s)){die($data->error);}
	$urunler_v=$data->query($urunler_s);
	if($urunler_v->num_rows>0)$urunler_d=1;
	unset($urunler_s);

	$aramasonuc="";
	if($urunler_d==1)
	{
		while ($urunler_t=$urunler_v->fetch_assoc()) 
		{
			$i++;
			$sayfaid=$urunler_t["sayfaid"];
			$kategoriad=$urunler_t["kategoriad"];
			$kategoriid=$urunler_t["kategoriid"];
			$sayfaad=$urunler_t["sayfaad"];
			$aramasonuc=$aramasonuc.'<tr id="tr'.$sayfaid.'"><td>'.$i.')</td><td><a href="/kategori/'.$kategoriid.'m.html" target="_blank">'.$kategoriad.' <i class="fa fa-external-link"></i></a></td><td><a href="/sayfa/'.$sayfaid.'s.html" title="Sayfayı Gör" target="_blank">'.$sayfaad.'<i class="fa fa-external-link"></i></a></td><td> ID: '.$sayfaid.' <a href="/_y/s/s/sayfalar/AddPage.php?sayfaid='.$sayfaid.'" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a><a id="sayfasil" href="#textModal" class="btn btn-icon-toggle" data-id="'.$sayfaid.'" data-toggle="modal" data-placement="top" data-original-title="Sil" data-target="#simpleModal" data-backdrop="true"><i class="fa fa-trash-o"></i></a></a></td></tr>';
		}
		//echo '<textarea id="aramasonuclari">'.$aramasonuc.'</textarea>';
		//gelen veri satır başlıkları ile temizle
	}
	unset($urunler_d,$urunler_v,$urunlerid,$urunlerdilid,$urunlerad,$urunlerdil);
	
	?>
		<script>
			if($("#aramasonuclari"))
			{
				//$aramasonuclari=$("#aramasonuclari").val();
				$("tbody",parent.document).html('<?=$aramasonuc?>');
			}
		</script>
	</body>
</html>