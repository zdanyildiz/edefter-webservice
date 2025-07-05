<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
if(S(q("urungrupid"))!=0)
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
			$urungrup_d=0; $urungrup_v=""; $urungrup_s="";
			$urungrup_s="SELECT * FROM urungrup WHERE urungrupid='". q("urungrupid") ."' ";
			$urungrup_v=$data->query($urungrup_s);
			if($urungrup_v->num_rows>0)$urungrup_d=1;
			unset($urungrup_s);
			if($urungrup_d==1)
			{
				while($urungrup_t=$urungrup_v->fetch_assoc())
				{
					$l_urungrupkdv=$urungrup_t["urungrupkdv"];
					$l_urungrupindirim=$urungrup_t["urungrupindirim"];if(strlen("$l_urungrupindirim")==3)$l_urungrupindirim=$l_urungrupindirim."0";
					$l_urungrupfiyateski=$urungrup_t["urungrupfiyateski"];
					$l_urungrupfiyatsontarih=$urungrup_t["urungrupfiyatsontarih"];
					$l_urungruptaksit=$urungrup_t["urungruptaksit"];
					$l_urungruphediye=$urungrup_t["urungruphediye"];
					$l_urungrupaciklamakisa=$urungrup_t["urungrupaciklamakisa"];
					$l_urungrupkargosuresi=$urungrup_t["urungrupkargosuresi"];
					$l_urungrupsabitkargoucreti=$urungrup_t["urungrupsabitkargoucreti"];
					
					echo "$('#urunkdv',parent.document).val('". $l_urungrupkdv ."');".PHP_EOL;
					echo "$('#urunindirim',parent.document).val('". $l_urungrupindirim ."');".PHP_EOL;
					echo "$('#urunfiyatsontarih',parent.document).val('". $l_urungrupfiyatsontarih ."');".PHP_EOL;
					echo "$('#uruntaksit',parent.document).val('". $l_urungruptaksit ."');".PHP_EOL;
					echo "$('#urunhediye',parent.document).val('". $l_urungruphediye ."');".PHP_EOL;
					echo "$('#urunaciklama',parent.document).val('". $l_urungrupaciklamakisa ."');".PHP_EOL;
					echo "$('#urunkargosuresi',parent.document).val('". $l_urungrupkargosuresi ."');".PHP_EOL;
					echo "$('#urunsabitkargoucreti',parent.document).val('". $l_urungrupsabitkargoucreti ."');".PHP_EOL;
					if(S($l_urungrupfiyateski)==1)echo "$('#uruneskifiyatevet',parent.document).prop('checked', true);";
				}
				unset($urungrup_t,$urungrup_v);
			}
			unset($urungrup_v);
			?>
		</script>
	</body>
</html>
<?php } ?>