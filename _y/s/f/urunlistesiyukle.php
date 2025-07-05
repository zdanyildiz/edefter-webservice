<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$dosyaadi="";
$ekle_d=0;
$rKlasor="d";
if(!empty($_FILES))
{
    $sifretext=sifreuret(5,2);
    $dosyaadi   = "urun";
    $dosyadizin = "/m/r/havuz";
    $ybenzersizid = sifreuret(20,2);
    
    if (!file_exists($anadizin.$dosyadizin))
    {
        mkdir($anadizin.$dosyadizin, 0777, true);
    }

    $gecicidosya    = $_FILES['file']['tmp_name'];
    $dosyaad        = $_FILES['file']['name'];
    $size           = $_FILES['file']['size'];
    $uzanti         = pathinfo($dosyaad, PATHINFO_EXTENSION);
    $yenidosyatamad = Duzelt($dosyaadi).".".strtolower($uzanti);
    $hedef          = $anadizin.$dosyadizin."/".$yenidosyatamad;

    //die($hedef);
    //Veri(true);
    if(move_uploaded_file($gecicidosya,$hedef))
    {
        echo json_encode($yenidosyatamad."|0|".$uzanti."|".$size);
    }
}
?>
