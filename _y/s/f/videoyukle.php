<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$videoadi="";
$ekle_d=0;
$rKlasor="v";
if(!empty($_FILES))
{
    $sifretext=sifreuret(5,2);
    $videoadi   = f('videoad');
    $videodizin = "/m/". f("videoklasor");
    $rKlasor=f("videoklasor");
    $ybenzersizid = sifreuret(20,2);
    if (!file_exists($anadizin.$videodizin))
    {
        mkdir($anadizin.$videodizin, 0777, true);
    }
    //die($sifretext);
    $gecicivideo    = $_FILES['file']['tmp_name'];
    $videoad        = $_FILES['file']['name'];
    $uzanti         = pathinfo($videoad, PATHINFO_EXTENSION);
    $yenivideotamad = Duzelt($videoadi)."-".$sifretext.".".strtolower($uzanti);
    $hedef          = $anadizin.$videodizin."/".$yenivideotamad;
    if(move_uploaded_file($gecicivideo,$hedef))
    {	
        chmod($hedef , 0777);
        $simdi=date("Y-m-d H:i:s");
        //$movie = new ffmpeg_movie($hedef);
        $width  = 640;//$movie->getFrameWidth();
        $height = 480;//$movie->getFrameHeight();
        $size=$_FILES['file']['size'];    
        $sutunlar="videoekletarih,videoad,video,videouzanti,videoboyut,benzersizid,vboy,ven";
        $degerler=$simdi."|*_".$videoadi."|*_". $rKlasor."/".$yenivideotamad ."|*_".$uzanti."|*_".$size."|*_".$ybenzersizid."|*_".$width."|*_".$height;
        ekle($sutunlar,$degerler,"video",26);
        $videoid=teksatir("
            SELECT 
                videoid 
            FROM 
                video 
            WHERE 
                benzersizid='". $ybenzersizid ."'
            ","videoid");
        echo json_encode($rKlasor."/".$yenivideotamad."|".$videoid."|".$uzanti."|".$size);
    }
}
?>