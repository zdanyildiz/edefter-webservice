<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
$f_sayfaid=S(f("sayfaid"));
if($f_sayfaid!=0 && S(f("uruniliski"))==1)
{
	Veri(true);
	sil("uruniliski","urunid='".$f_sayfaid."' and iliskiid='0'",0);
    sil("uruniliski","iliskiid='".$f_sayfaid."' ",0);
    if (!empty($_POST['iliskiliurunler']))
    {
        ekle("iliskiid,urunid","0|*_".$f_sayfaid,"uruniliski",0);
        foreach ($_POST['iliskiliurunler'] as $iliskiid)
        {
            if(S($iliskiid)!=0)
            {
                ekle("iliskiid,urunid",$f_sayfaid."|*_".$iliskiid,"uruniliski",0);
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
	</head>
	<body>
		<script>
				<?php 

                    echo '
                    $("#trgizli'.$f_sayfaid.'",parent.document).hide();
                    $("#2trgizli'.$f_sayfaid.'",parent.document).hide();
                    $("#iliskiguncelle",parent.document).modal("show");
                    $("#sonuclar'.$f_sayfaid.'",parent.document).hide();
                    setTimeout(function() {$("#iliskiguncelle",parent.document).modal("hide");}, 1000);
                    ';
				?>
		</script>
	</body>
</html>