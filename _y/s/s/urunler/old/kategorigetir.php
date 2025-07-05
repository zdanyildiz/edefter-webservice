<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php 
	$ustkategoriid=S(q("id"));

	 $sorgu="SELECT * FROM  kategori where kategoriaktif='1' and kategorisil='0' and kategorigrup='7' and ustkategoriid='".$ustkategoriid."' and ustkategoriid!='0' order by kategoriad asc";
	 if($db->select($sorgu))
	 {
	 	$sonuc=$db->select($sorgu);
	 	$sonuctoplam=count($sonuc);
	 	if ($sonuctoplam>0) {
			echo '<select class="form-control" size="5" data-id="">';
				foreach($sonuc as $sonucliste){
					$selected="";
					$kategoriad=$sonucliste["kategoriad"];
					$kategoriid=$sonucliste["kategoriid"];
					if($kategoriid==S(q("katid")))$selected=" selected";
					echo '<option value="'.$kategoriid.'" '.$selected.'>'.$kategoriad.'</option>';
				}
			echo '</select>';
	 	}
	 }
?>
