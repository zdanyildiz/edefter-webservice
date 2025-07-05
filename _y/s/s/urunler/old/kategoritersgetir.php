<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php 
	$f_kategoriid=S(q("id"));

	Veri(true);
	$ustkatid=teksatir("select ustkategoriid From kategori Where kategoriid='".$f_kategoriid."'","ustkategoriid");
	 $sorgu="SELECT * FROM  kategori where kategoriaktif='1' and kategorisil='0' and kategorigrup='7' and ustkategoriid='".$ustkatid."' order by kategoriad asc";
	 if($data->query($sorgu))
	 {
	 	$sonuc=$data->query($sorgu);
	 	$sonuctoplam=$sonuc->num_rows;
	 	if ($sonuctoplam>0) {
		echo '<select class="form-control" size="5">';
	 		while($sonucliste=$sonuc->fetch_assoc()){
	 			$kategorisec="";
	 			$kategoriad=$sonucliste["kategoriad"];
	 			$kategoriid=$sonucliste["kategoriid"];
	 			if($f_kategoriid==$kategoriid)$kategorisec="selected";
	 			echo '<option value="'.$kategoriid.'" '.$kategorisec.'>'.$kategoriad.'</option>';
	 		}
		echo '</select>';
	 	}
	 		 }else{
	 	die($data->error);
	 };

?>
