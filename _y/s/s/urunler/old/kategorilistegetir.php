<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$ustkategoriid=S(q("id"));

$kategoriliste_sorgu="
		SELECT 
			kategoriid,kategoriad,dilid 
		FROM 
			kategori 
		WHERE 
			kategorisil='0' and kategoriaktif='1' and kategorigrup='7' and ustkategoriid='".$ustkategoriid."'
		Order By 
			Kategoriid ASC
	";
if($data->query($kategoriliste_sorgu))
{
    $kategoriliste_sonuc=$data->query($kategoriliste_sorgu);unset($kategoriliste_sorgu);
    if($kategoriliste_sonuc->num_rows>0)
    {
        while ($kategoriliste_veri=$kategoriliste_sonuc->fetch_assoc())
        {
            $kategoriliste_id=$kategoriliste_veri["kategoriid"];
            $kategoriliste_ad=$kategoriliste_veri["kategoriad"];
            $altkategorivarmi=teksatir("SELECT ustkategoriid From kategori Where kategorisil='0' and ustkategoriid='".$kategoriliste_id."'","ustkategoriid");
            ?>
            <details class="form-group kategori" id="tr<?=$kategoriliste_id?>" data-id="<?=$kategoriliste_id?>" style="margin-left:25px;">
                <summary style="outline:none;background-color:#eee;border-bottom:solid 1px #ccc"><?=$kategoriliste_ad?></summary>
                <a href="/_y/s/s/urunler/urunkategoriekle.php?kategoriid=<?=$kategoriliste_id?>" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                <a id="kategorisil"
                   href="#textModal"
                   class="btn btn-icon-toggle"
                   data-id="<?=$kategoriliste_id?>"
                   data-toggle="modal"
                   data-placement="top"
                   data-original-title="Sil"
                   data-target="#simpleModal"
                   data-backdrop="true">
                    <i class="fa fa-trash-o"></i></a>
            </details>
            <div class="a<?=$kategoriliste_id?>" style="margin-left:25px;"></div>
            <?php
        }
    }
}else{hatalogisle("Kategori Liste",$data->error);}
?>