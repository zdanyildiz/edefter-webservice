<?php
function tumkategoriler($ustkategoriid)
{
	global $data;
	$kategori_s="select 
				kategoriid,kategoriad,benzersizid,kategorikatman
			from 
				kategori 
			where 
				ustkategoriid='".$ustkategoriid."' and 
				kategoriaktif='1' and 
				kategorisil='0' ";
	$kategori_v=$data->query($kategori_s);
	if($kategori_v->num_rows>0)
	{
		while ($kategori_t=$kategori_v->fetch_assoc()) 
		{
			$kategoriid 	=$kategori_t["kategoriid"];
			$kategoriad 	=$kategori_t["kategoriad"];
			$kategorikatman =$kategori_t["kategorikatman"];
			if($kategorikatman==1)
			{
				$kategoriad = " -".$kategoriad;
			}elseif($kategorikatman==2)
			{
				$kategoriad = " --".$kategoriad;
			}elseif($kategorikatman==3)
			{
				$kategoriad = " ---".$kategoriad;
			}
			$kategoribenzersiz=$kategori_t["benzersizid"];
			echo '<option value="'.$kategoribenzersiz.'">'.$kategoriad.'</option>';
			tumkategoriler($kategoriid);
		}
	}
}
function tumsayfalar()
{
	global $data;
	$sayfa_s="select 
				sayfaid,sayfaad,benzersizid
			from 
				sayfa 
			where 
				sayfaaktif='1' and 
				sayfasil='0' ";
	$sayfa_v=$data->query($sayfa_s);
	if($sayfa_v->num_rows>0)
	{
		while ($sayfa_t=$sayfa_v->fetch_assoc()) 
		{
			$sayfaid 	=$sayfa_t["sayfaid"];
			$sayfaad 	=$sayfa_t["sayfaad"];
			$sayfabenzersiz=$sayfa_t["benzersizid"];
			echo '<option value="'.$sayfabenzersiz.'">'.$sayfaad.'</option>';
		}
	}
}
?>