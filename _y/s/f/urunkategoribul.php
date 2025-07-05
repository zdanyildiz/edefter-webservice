<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
function kategoriliste($ustkategoriid,$kategorikatman)
{
    global $data;
    $urunkategori_d=0;
    $urunkategori_s="
		SELECT 
			kategoriid,kategoriad,dilid,kategorikatman 
		FROM 
			kategori 
		WHERE 
			kategorisil='0' and kategorigrup='7' and 
			kategorikatman='".S($kategorikatman)."' and ustkategoriid='".S($ustkategoriid)."'
		ORDER BY 
			kategorikatman asc,kategoriid ASC";
    $urunkategori_v=$data->query($urunkategori_s);
    if($urunkategori_v->num_rows>0)$urunkategori_d=1;
    unset($urunkategori_s);

    if($urunkategori_d==1)
    {
        while ($urunkategori_t=$urunkategori_v->fetch_assoc())
        {
            $urunkategoridilid=$urunkategori_t["dilid"];
            $urunkategoriid=$urunkategori_t["kategoriid"];
            $urunkategoriad=$urunkategori_t["kategoriad"];
            $urunkategoridil=teksatir("select dilad from dil where dilid='". $urunkategoridilid ."'","dilad");
            $kategorikatman=$urunkategori_t["kategorikatman"];
            $katmanek="";
            if($kategorikatman==1)
            {
                $katmanek=" -";
            }
            elseif($kategorikatman==2)
            {
                $katmanek=" --";
            }
            elseif($kategorikatman==3)
            {
                $katmanek=" ---";
            }
            if($kategorikatman==0)$kategoristyle=' style="font-weight:bold"';else $kategoristyle='';
            $aramasonuc=$aramasonuc.
                '<tr id="tr'.$urunkategoriid.'">
				<td>'.$urunkategoriid.'</td>
				<td>'.$urunkategoridil.'</td>
				<td '.$kategoristyle.'>'.$katmanek.$urunkategoriad.'</td>
				<td>
					<a href="/_y/s/s/kategoriler/AddCategory.php?kategoriid='.$urunkategoriid.'" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
					<a id="kategorisil"
						href="#textModal"
						class="btn btn-icon-toggle"
						data-id="'.$urunkategoriid.'" 
						data-toggle="modal"
						data-placement="top"
						data-original-title="Sil" 
						data-target="#simpleModal"
						data-backdrop="true">
						<i class="fa fa-trash-o"></i></a>
				</td>
			</tr>';
            kategoriliste($urunkategoriid,S($kategorikatman)+1);
        }
        return $aramasonuc;
    }
    unset($urunkategori_d,$urunkategori_v,$urunkategoriid,$urunkategoridilid,$urunkategoriad,$urunkategoridil);
}?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<?php
if(!BosMu(q("q")))
{
    Veri(true);
    $urunkategori_d=0;
    $urunkategori_s="
        SELECT 
            kategoriid,kategoriad,dilid,kategorikatman 
        FROM 
            kategori 
        WHERE 
            kategorisil='0' and kategorigrup='7' and kategoriad like '%". q("q") ."%'
        ORDER BY 
            kategoriid ASC
	";
    if($data->query($urunkategori_s))
    {
        $urunkategori_v=$data->query($urunkategori_s);
        if($urunkategori_v->num_rows>0)$urunkategori_d=1;
        unset($urunkategori_s);
    }
    else
    {
        die($data->error);
    }

    $aramasonuc="";
    if($urunkategori_d==1)
    {
        while ($urunkategori_t=$urunkategori_v->fetch_assoc())
        {
            $urunkategoridilid=$urunkategori_t["dilid"];
            $urunkategoriid=$urunkategori_t["kategoriid"];
            $urunkategoriad=$urunkategori_t["kategoriad"];
            $urunkategoridil=teksatir("select dilad from dil where dilid='". $urunkategoridilid ."'","dilad");
            $kategorikatman=$urunkategori_t["kategorikatman"];
            if($kategorikatman==1)
            {
                $katmanek=" -";
            }
            elseif($kategorikatman==2)
            {
                $katmanek=" --";
            }
            elseif($kategorikatman==3)
            {
                $katmanek=" ---";
            }
            if($kategorikatman==0)$kategoristyle=' style="font-weight:bold"';else $kategoristyle='';

            $aramasonuc=$aramasonuc.
                '<details
                    class="row form-group kategori"
                    id="tr<?=$urunkategoriid?>"
                    data-id="'.$urunkategoriid.'">
                    <summary style="outline:none;">'.$urunkategoriad.'</summary>
                <a href="/_y/s/s/urunler/urunkategoriekle.php?kategoriid='.$urunkategoriid.'" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                <a id="kategorisil"
                   href="#textModal"
                   class="btn btn-icon-toggle"
                   data-id="'.$urunkategoriid.'"
                   data-toggle="modal"
                   data-placement="top"
                   data-original-title="Sil"
                   data-target="#simpleModal"
                   data-backdrop="true">
                    <i class="fa fa-trash-o"></i></a>
                </details>
                <div class="row a'.$urunkategoriid.'"></div>';
            kategoriliste($urunkategoriid,S($kategorikatman)+1);
        }
        echo '<textarea id="aramasonuclari">'.$aramasonuc.'</textarea>';
    }
    unset($urunkategori_d,$urunkategori_v,$urunkategoriid,$urunkategoridilid,$urunkategoriad,$urunkategoridil);
}
?>
<script>
    if($("#aramasonuclari"))
    {
        $aramasonuclari=$("#aramasonuclari").val();
        $(".aramasonuc",parent.document).html($aramasonuclari);
    }
</script>
</body>
</html>