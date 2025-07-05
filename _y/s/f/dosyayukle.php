<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$dosyaadi="";
$ekle_d=0;
$rKlasor="d";
if(!empty($_FILES))
{
    $sifretext=sifreuret(5,2);
    $dosyaadi   = f('dosyaad');
    $dosyadizin = "/m/". f("dosyaklasor");
    $rKlasor=f("dosyaklasor");
    $ybenzersizid = sifreuret(20,2);
    
    if (!file_exists($anadizin.$dosyadizin))
    {
        mkdir($anadizin.$dosyadizin, 0777, true);
    }

    $gecicidosya    = $_FILES['file']['tmp_name'];
    $dosyaad        = $_FILES['file']['name'];
    $uzanti         = pathinfo($dosyaad, PATHINFO_EXTENSION);
    $yenidosyatamad = Duzelt($dosyaadi)."-".$sifretext.".".strtolower($uzanti);
    $hedef          = $anadizin.$dosyadizin."/".$yenidosyatamad;

    //die($hedef);
    //Veri(true);
    if(move_uploaded_file($gecicidosya,$hedef))
    {	
        chmod($hedef , 0777);
        $simdi=date("Y-m-d H:i:s");
        $size=$_FILES['file']['size'];    
        $sutunlar="dosyaekletarih,dosyaad,dosya,dosyauzanti,dosyaboyut,benzersizid";
        $degerler=$simdi."|*_".$dosyaadi."|*_". $rKlasor."/".$yenidosyatamad ."|*_".$uzanti."|*_".$size."|*_".$ybenzersizid;
        ekle($sutunlar,$degerler,"dosya",26);
        $dosyaid=teksatir("
            SELECT 
                dosyaid 
            FROM 
                dosya 
            WHERE 
                benzersizid='". $ybenzersizid ."'
            ","dosyaid");
        
        echo json_encode($rKlasor."/".$yenidosyatamad."|".$dosyaid."|".$uzanti."|".$size);
            
        
    }
}
?>
