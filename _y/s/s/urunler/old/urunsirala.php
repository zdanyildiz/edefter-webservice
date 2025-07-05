<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$i=0;
foreach($_POST['sayfaid'] as $sayfaid)
{
    $sayfasira =$_POST['sayfasira'][$i];
    guncelle("sayfasira",$sayfasira,"sayfa","sayfaid='".$sayfaid."'",0);
    $i++;
}
header('Content-type: application/json');
echo json_encode('success');
?>
												