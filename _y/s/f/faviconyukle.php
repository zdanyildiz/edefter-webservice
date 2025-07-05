<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$ekle_d=0;$rKlasor="favicon";$rEn = 0;$rBoy = 0;
if(!empty($_FILES))
{
    $geciciresim    = $_FILES['file']['tmp_name'];
    $resimad        = $_FILES['file']['name'];
    $uzanti         = pathinfo($resimad, PATHINFO_EXTENSION);
    $sifretext=sifreuret(5,2);
    $resimdizin  = "/m/r/".$rKlasor;
    $yeniresimad=str_replace(".".$uzanti, "", $resimad);
    $yeniresimtamad = Duzelt($yeniresimad)."-".$sifretext.".".strtolower($uzanti);
    $hedef = $anadizin.$resimdizin."/".$yeniresimtamad;
    
    if (!file_exists($anadizin.$resimdizin)){mkdir($anadizin.$resimdizin, 0777, true);}
    if(move_uploaded_file($geciciresim,$hedef))
    {	
        chmod($hedef , 0777);
        
        $image_info = getimagesize($hedef);
        $rEn        = $image_info[0];
        $rBoy       = $image_info[1];

        $klasordogrula=false;
        $tablo="resimklasor";
        $degerler="resimklasorad='". $rKlasor ."'";
        $klasordogrula=dogrula($tablo,$degerler);
        $formhata=0;

        if($klasordogrula==false)
        {
            $tablo="resimklasor";
            $sutunlar="resimklasorad";
            $degerler="$rKlasor";
            $eylem=26;
            ekle($sutunlar,$degerler,$tablo,$eylem);
        }
        if($formhata==0)
        {
            $ybenzersizid = "12345678901234567890";
            $resimklasorid = teksatir("SELECT resimklasorid FROM resimklasor WHERE resimklasorad='". $rKlasor ."'","resimklasorid");
            
            $sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid";
            $degerler=$resimklasorid."|*_". $yeniresimad ."|*_". $yeniresimtamad ."|*_".$rEn.
            "|*_".$rBoy."|*_".$ybenzersizid;

            if(dogrula("resim","benzersizid='".$ybenzersizid."'"))
            {
                guncelle("resim",$yeniresimtamad,"resim","benzersizid='".$ybenzersizid."'",26);
            }
            else
            {
                ekle($sutunlar,$degerler,"resim",26); 
            }

            $resimid=teksatir("
                SELECT 
                    resimid 
                FROM 
                    resim 
                WHERE 
                    resim='". $yeniresimtamad ."' 
                    and 
                    benzersizid='". $ybenzersizid ."'
                ","resimid");
            
            echo json_encode("true");
        }
        else
        {
            echo json_encode("false");
        }
    }
}
?>
