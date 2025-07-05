<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php

if(S(q("sayfaid"))!=0&&!BosMu(q("resimler")))
{
    Veri(true);
    $sayfaid=q("sayfaid");
    $resimler=trim(q("resimler"),",");
    $resimler=str_replace(",,",",",$resimler);
    $resim_ayikla=explode(",",$resimler);
    sil("sayfalisteresim","sayfaid='".$sayfaid."'",0);
    foreach ($resim_ayikla as $resimid)
    {
        ekle("sayfaid,resimid",$sayfaid."|*_".$resimid,"sayfalisteresim",1);
    }
    echo "ok";
}
?>
