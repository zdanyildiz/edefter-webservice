<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$model=trim(q("model"));
if( S(q("sayfaid"))!=0&&!BosMu($model)&&S(q("urunozellikid")) )
{
    Veri(true);
    $sayfaid=S(q("sayfaid"));

    $urunozellikid=S(q("urunozellikid"));
    //sil("sayfalisteresim","sayfaid='".$sayfaid."'",0);

    guncelle("sayfaid,urunmodel",$sayfaid."|*_".$model,"urunozellikleri","urunozellikid='".$urunozellikid."'",1);

    echo "ok";
}
?>
