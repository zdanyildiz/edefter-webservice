<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
if($_POST){
    $stokkodu = strip_tags(trim($_POST['stokkodu']));

    $q1 = str_replace(" ","",$stokkodu);
    $q2 = str_replace(".","",$stokkodu);
    $q3 = str_replace("."," ",$stokkodu);
    $q4 = str_replace(" ",".",$stokkodu);

    $qkelimeyeni=str_replace(substr($stokkodu,0,3),substr($stokkodu,0,3).".",$stokkodu);
    $qkelimeyeni=str_replace(substr($stokkodu,-3),".".substr($stokkodu,-3),$qkelimeyeni);
    $sqlstokkoduek=" and (
        urunstokkodu like '%".$q1."%' or
        urunstokkodu like '%".$q2."%' or
        urunstokkodu like '%".$q3."%' or
        urunstokkodu like '%".$q4."%' or
        urunstokkodu like '%".$qkelimeyeni."%' or
        urunstokkodu like '%".$stokkodu."%'
    )";


    $urunid = $_POST['urunid'];
    $arama="SELECT 
       urunstokkodu,sayfaid 
    FROM 
         urunozellikleri 
    WHERE 
         sayfaid!='".$urunid."'  $sqlstokkoduek
    Order BY urunstokkodu limit 3";
    if($data->query($arama))
    {
        $arama_v=$data->query($arama);unset($arama);
        if($arama_v->num_rows>0)
        {
            while($arama_t=$arama_v->fetch_assoc())
            {
               echo $arama_t["urunstokkodu"].' '.$arama_t["sayfaid"].' ';
            }unset($arama_t);
        }
        else
        {
            echo 'yok';
        }
        unset($arama_v);
    }
    else{die($data->error);}
}else{die("yok");}
?>
