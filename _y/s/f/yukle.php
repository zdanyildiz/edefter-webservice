<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$resimadi="";
$ekle_d=0;
$rKlasor="";
$rEn = 0;
$rBoy = 0;
if(!empty($_FILES))
{
    $geciciresim    = $_FILES['file']['tmp_name'];
    $resimad        = $_FILES['file']['name'];
    $uzanti         = pathinfo($resimad, PATHINFO_EXTENSION);
    $sifretext=sifreuret(5,2);

    //logyaz("resim yükleme adım 1","$geciciresim - $resimad - $uzanti ");

    if(!BosMu(f('yoneticiresimisim')))
    {
        $resimadi  = f('yoneticiresimisim');
        $resimdizin = "/_y/m/r/yoneticiler";
    }
    elseif(!BosMu(f('resimad')))
    {
        $resimadi   = f('resimad');
        $resimdizin = "/m/r/". f("resimklasor");
        $rKlasor=f("resimklasor");
        $ekle_d=1;

        if(!BosMu(f("resimklasor"))){$resimdizin  = "/m/r/".f("resimklasor");}else{$resimdizin  = "/m/r/diger";}
    }

    //logyaz("resim yükleme adım 2","$resimadi - $resimdizin ");

    if(!BosMu($resimadi)){$yeniresimad = $resimadi;}else{$yeniresimad="Boş";}
    if (!file_exists($anadizin.$resimdizin)){mkdir($anadizin.$resimdizin, 0777, true);}

    if(!BosMu(f('yoneticiresimisim')))
    {
        $yeniresimtamad = $yeniresimad.".".$uzanti;
    }
    else
    {
        $yeniresimtamad = Duzelt($yeniresimad)."-".$sifretext.".".strtolower($uzanti);
    }
    $hedef = $anadizin.$resimdizin."/".$yeniresimtamad;

    //logyaz("resim yükleme adım 3","$hedef ");

    if(!move_uploaded_file($geciciresim,$hedef))
    {
        print_r($_FILES);
    }
    else
    {
        //logyaz("resim yükleme adım 4","resim yükle $geciciresim -> $hedef ");
        chmod($hedef , 0777);
        //echo 'resim boyutları alınacak<br>';
        if($image_info = @getimagesize($hedef))
        {
            $rEn        = $image_info[0];
            $rBoy       = $image_info[1];
            //echo "G: $rEn , Y:$rBoy <br>";
            //logyaz("resim yükleme adım 5","en-boy: $rEn - $rBoy ");
            if($rEn>1920)
            {
                $ratio = $rEn/$rBoy;
                $target_filename = $hedef;
                if( $ratio > 1)
                {
                    $new_width = 1920;
                    $new_height = 1920/$ratio;
                } else {
                    $new_width = 1920*$ratio;
                    $new_height = 1920;
                }
                //echo "YG: $new_width , YY: $new_height<br>";
                $src = imagecreatefromstring( file_get_contents( $hedef ) );
                $dst = imagecreatetruecolor( $new_width, $new_height );
                imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $rEn, $rBoy );
                imagedestroy( $src );
                if(strtolower($uzanti)=="png"||strtolower($uzanti)=="gif")
                {
                    imagepng( $dst, $target_filename );
                }
                else
                {
                    imagejpeg($dst, $target_filename);
                }
                imagedestroy( $dst );
            }
        }
        else
        {
            //echo 'resim boyutları okunamadı<br>';
            //logyaz("resim yükleme adım 5","en-boy: Alınamadı");
        }

        if(!BosMu(f('yoneticiresimisim')))
        {
            echo json_encode($yeniresimtamad);
        }
        else
        {

            $klasordogrula=false;
            $tablo="resimklasor";
            $degerler="resimklasorad='". $rKlasor ."'";
            $klasordogrula=dogrula($tablo,$degerler);
            $formhata=0;

            //formdan gelen klasör adı veri tabanında yoksa oluştur
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
                $ybenzersizid = sifreuret(20,2);
                $resimklasorid = teksatir("SELECT resimklasorid FROM resimklasor WHERE resimklasorad='". $rKlasor ."'","resimklasorid");

                $sutunlar="resimklasorid,resimad,resim,ren,rboy,benzersizid";
                $degerler=$resimklasorid."|*_". $yeniresimad ."|*_". $yeniresimtamad ."|*_".$rEn.
                    "|*_".$rBoy."|*_".$ybenzersizid;
                ekle($sutunlar,$degerler,"resim",26);
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

                echo json_encode($rKlasor."/".$yeniresimtamad."|".$resimid."|".$rEn."|".$rBoy);
            }
        }
    }
}
?>
