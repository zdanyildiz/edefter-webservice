<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
if($_POST){
    $value = strip_tags(trim($_POST['urunad']));
    $arama="SELECT sayfaad, sayfaid FROM sayfa WHERE sayfaad like '%$value%' and sayfasil='0' and sayfaaktif='1' and sayfatip='7' GROUP BY sayfaad limit 30";
    if($data->query($arama))
    {
        $arama_v=$data->query($arama);unset($arama);
        if($arama_v->num_rows>0)
        {
            while($arama_t=$arama_v->fetch_assoc())
            {
                echo '
                        <li 
                            style="display:block;width:100%;cursor:pointer" 
                            data-id="'.$arama_t["sayfaid"].'" 
                            class="iliskisonuc form-control"
                        >'.$arama_t["sayfaad"].'</li>';
            }unset($arama_t);
        }
        else
        {
            echo '<p>Hiç bir sonuç bulunamadı!! </p>';
        }
        unset($arama_v);
    }
    else{die($data->error);}


}
?>