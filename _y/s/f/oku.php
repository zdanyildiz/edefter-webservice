<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$resimsayfa = $_SERVER['DOCUMENT_ROOT'].'/s/x/menu/6BN1234FJ8XYH9VTE723.xml';
if (file_exists($resimsayfa))
{
    $resimsayfaic = file_get_contents($resimsayfa);
    $resimsayfaic = str_replace('<?xml version="1.0" encoding="utf-8"?>', "", $resimsayfaic);
    $resimsayfaic = str_replace('<resimler>', "", $resimsayfaic);
    $resimsayfaic = str_replace('</resimler>', "", $resimsayfaic);
    echo str_replace('<', '', $resimsayfaic);
}
else
{
    die("yok");
}
/*
$resimsayfaicerik='<?xml version="1.0" encoding="utf-8"?>
<resimler>
    '. $resimsayfaic .'
    <resim>
        <resimid>'. $resimid .'</resimid>
        <rad>'. $yeniresimad .'</rad>
        <ryer>'. str_replace($resimdizin,"",$hedef) .'</ryer>
        <ren>'. $rEn  .'</ren>
        <rboy>'. $rBoy .'</rboy>
    </resim>
</resimler>';
file_put_contents($resimsayfa,$resimsayfaicerik);
*/

?>