<?php
/**
 * @var AdminDatabase $db

 */ require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<?php
if(S(q("id"))!=0)
{
    $trsil=0;
    $rsil=0;
    $divgizle=0;
    $aktif=0;
    //firma sil

    if(q("sil")=="firma")
    {
        $db->beginTransaction();
        $db->select("UPDATE ayarfirma SET ayarfirmasil='1' WHERE ayarfirmaid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="tedarikci")
    {
        $db->beginTransaction();
        $db->select("UPDATE uye SET uyesil='1' WHERE uyeid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="sayfatip")
    {
        $db->beginTransaction();
        $db->select("UPDATE sayfatip SET sayfatipsil='1' WHERE sayfatipid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="bayi")
    {
        $db->select("UPDATE uye SET uyesil='1' WHERE uyeid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="kategori")
    {
        $db->beginTransaction();
        $db->select("UPDATE kategori SET kategorisil='1' WHERE kategoriid='". q("id") ."'");
        $f_benzersizid=teksatir(" Select benzersizid from kategori Where kategoriid='". q("id") ."'","benzersizid");
        $db->select("DELETE FROM seo WHERE benzersizid='". $f_benzersizid ."'");
        $trsil=1;
    }
    elseif(q("sil")=="marka")
    {
        $db->beginTransaction();
        $db->select("UPDATE urunmarka SET markasil='1' WHERE markaid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="grup")
    {
        $db->beginTransaction();
        $db->select("UPDATE urungrup SET urungrupsil='1' WHERE urungrupid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="galeri")
    {
        $db->beginTransaction();
        $db->select("UPDATE resimgaleri SET resimgalerisil='1' WHERE resimgaleriid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="rehbergrup")
    {
        $db->beginTransaction();
        $db->select("UPDATE rehbergrup SET rehbergrupsil='1' WHERE rehbergrupid='". q("id") ."'");
        $db->select("UPDATE rehber SET rehbersil='1' WHERE rehbergrupid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="rehber")
    {
        $db->beginTransaction();
        $db->select("UPDATE rehber SET rehbersil='1' WHERE rehberid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="bedengrup")
    {
        $db->beginTransaction();
        $db->select("UPDATE urunbedengrup SET urunbedengrupsil='1' WHERE urunbedengrupid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="beden")
    {
        $db->beginTransaction();
        $db->select("UPDATE urunbeden SET urunbedensil='1' WHERE urunbedenid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="renkgrup")
    {
        $db->select("UPDATE urunrenkgrup SET urunrenkgrupsil='1' WHERE urunrenkgrupid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="renk")
    {
        $db->beginTransaction();
        $db->select("UPDATE urunrenk SET urunrenksil='1' WHERE urunrenkid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="malzemegrup")
    {
        $db->beginTransaction();
        $db->select("UPDATE urunmalzemegrup SET urunmalzemegrupsil='1' WHERE urunmalzemegrupid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="malzeme")
    {
        $db->select("UPDATE urunmalzeme SET urunmalzemesil='1' WHERE urunmalzemeid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="kargo")
    {
        $db->beginTransaction();
        $db->select("UPDATE kargo SET kargosil='1' WHERE kargoid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="banka")
    {
        $db->beginTransaction();
        $db->select("UPDATE ayarbanka SET ayarbankasil='1',ayarbankaaktif='0' WHERE ayarbankaid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="formiletisim")
    {
        $db->select("UPDATE formiletisim SET formsil='1' WHERE formid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="bankaeft")
    {
        $db->beginTransaction();
        $db->select("UPDATE bankaeft SET bankaeftsil='1' WHERE bankaeftid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="iptaliadedegisim")
    {
        $db->beginTransaction();
        $db->select("UPDATE iptaliadedegisim SET talepsil='1' WHERE talepid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="yorum")
    {
        $db->select("UPDATE yorum SET yorumsil='1' WHERE yorumid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="soru")
    {
        $db->beginTransaction();
        $db->select("UPDATE sorusor SET mesajsil='1' WHERE mesajid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="yonetici")
    {
        $db->beginTransaction();
        $db->select("UPDATE yoneticiler SET yoneticisil='1' WHERE yoneticiid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="parabirim")
    {
        $db->beginTransaction();
        $db->select("UPDATE urunparabirim SET parabirimsil='1' WHERE parabirimid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="ayaranaliz")
    {
        $db->beginTransaction();
        $db->select("UPDATE ayaranaliz SET ayaranalizsil='1' WHERE ayaranalizid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="satisdonusumkod")
    {
        $db->beginTransaction();
        $db->select("UPDATE satisdonusumkodu SET satisdonusumkodsil='1' WHERE satisdonusumkodid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="urun")
    {
        $db->beginTransaction();
        $db->select("UPDATE sayfa SET sayfasil='1' WHERE sayfaid='". q("id") ."'");
        $db->select("DELETE FROM sayfalistekategori WHERE sayfaid='". q("id") ."'");
        $db->select("DELETE FROM sayfalisteresim WHERE sayfaid='". q("id") ."'");
        $db->select("DELETE FROM urunozellikleri WHERE sayfaid='". q("id") ."'");
        $f_benzersizid=teksatir(" Select benzersizid from sayfa Where sayfaid='". q("id") ."'","benzersizid");
        $db->select("DELETE FROM seo WHERE benzersizid='". $f_benzersizid ."'");
        $trsil=1;
    }
    elseif(q("sil")=="urunaktif")
    {
        $db->beginTransaction();
        $sayfaaktif=teksatir(" Select sayfaaktif from sayfa Where sayfaid='". q("id") ."'","sayfaaktif");
        if($sayfaaktif==0)$aktif=1;else $aktif=0;
        $db->select("UPDATE sayfa SET sayfaaktif='".$aktif."' WHERE sayfaid='". q("id") ."'");
    }
    elseif(q("sil")=="urunfirsat")
    {
        $db->beginTransaction();
        $islem=S(q("islem"));
        $db->select("UPDATE urunozellikleri SET urungununfirsati='".$islem."' WHERE sayfaid='". q("id") ."'");
    }
    elseif(q("sil")=="sayfa")
    {
        $db->beginTransaction();
        $db->select("UPDATE sayfa SET sayfasil='1' WHERE sayfaid='". q("id") ."'");
        $db->select("DELETE FROM sayfalistekategori WHERE sayfaid='". q("id") ."'");
        $db->select("DELETE FROM sayfalisteresim WHERE sayfaid='". q("id") ."'");
        $f_benzersizid=teksatir(" Select benzersizid from sayfa Where sayfaid='". q("id") ."'","benzersizid");
        $db->select("DELETE FROM seo WHERE benzersizid='". $f_benzersizid ."'");
        $db->select("DELETE FROM seo WHERE benzersizid='". $f_benzersizid ."'");
        $trsil=1;
    }
    elseif(q("sil")=="resim")
    {
        $db->beginTransaction();
        if (file_exists($_SERVER['DOCUMENT_ROOT'].q("resim")))
        {
            unlink($_SERVER['DOCUMENT_ROOT'].q("resim"));

        }$db->select("DELETE FROM resim WHERE resimid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="dosya")
    {
        $db->beginTransaction();
        if (file_exists($_SERVER['DOCUMENT_ROOT'].q("dosya")))
        {
            unlink($_SERVER['DOCUMENT_ROOT'].q("dosya"));

        }$db->select("DELETE FROM dosya WHERE dosyaid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="video")
    {
        $db->beginTransaction();
        if (file_exists($_SERVER['DOCUMENT_ROOT'].q("video")))
        {
            unlink($_SERVER['DOCUMENT_ROOT'].q("video"));

        }$db->select("DELETE FROM video WHERE videoid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("sil")=="sosyalmedya")
    {
        $db->beginTransaction();
        $db->select("DELETE FROM ayarsosyalmedya WHERE ayarsosyalmedyaid='". q("id") ."'");
        $trsil=1;
    }
    elseif(q("degistir")=="resimad")
    {
        $db->beginTransaction();
        $db->select("UPDATE resim SET resimad='".q("ad")."' WHERE resimid='". q("id") ."'");
        $divgizle=1;
    }
    elseif(q("degistir")=="dosyaad")
    {
        $db->beginTransaction();
        $db->select("UPDATE dosya SET dosyaad='".q("ad")."' WHERE dosyaid='". q("id") ."'");
        $divgizle=1;
    }
    elseif(q("degistir")=="videoad")
    {
        $db->beginTransaction();
        $db->select("UPDATE video SET videoad='".q("ad")."' WHERE videoid='". q("id") ."'");
        $divgizle=1;
    }
    elseif(q("sil")=="ekozellik")
    {
        $db->beginTransaction();
        if($db->delete("DELETE FROM urunekozellikler WHERE ekozellikid=:id", array("id" => q("id")))){
            $db->commit();
        }
        else{
            $db->rollback();
        }
        die("ok");
    }
    elseif(q("sil")=="varyant")
    {
        $db->beginTransaction();
        if($db->delete("DELETE FROM urunvaryant WHERE varyantid=:id", array("id" => q("id")))){
            $db->commit();
        }
        else{
            $db->rollback();
        }
        die("ok");
    }
    elseif(q("sil")=="varyantgrup")
    {
        $db->beginTransaction();
        if($db->delete("DELETE FROM urunvaryantgrup WHERE varyantgrupid=:id", array("id" => q("id")))){
            $db->delete("DELETE FROM urunvaryant WHERE varyantgrupid=:id", array("id" => q("id")));
            $db->commit();
        }
        else{
            $db->rollback();
        }

        die("ok");
    }
    if($divgizle==1)
    {
        echo '
				<script>
					if($("#d_r'. q("id") .'",parent.document))
					{
						$("#s'. q("id") .'",parent.document).text("'.q("ad").'");
						$("#d_r'. q("id") .'",parent.document).delay(500).fadeOut();
					}
				</script>';
    }
    if($trsil==1)
    {
        echo '
				<script>
					$("#btn-popup-sil-kapat",parent.document).click();
					if($("#tr'. q("id") .'",parent.document))
					{
						$("#tr'. q("id") .'",parent.document).addClass("alert alert-danger");
						$("#tr'. q("id") .'",parent.document).delay(500).fadeOut();
					}
				</script>';
    }
    if($rsil==1)
    {
        echo '
				<script>
					$("#btn-popup-sil-kapat",parent.document).click();
					$("#resimid",parent.document).val(0);
					$("#rad",parent.document).text("Resim Adı");
			    	d=$.now();
			    	$resim="/_y/assets/img/avatar7.jpg?"+d;
			    	$("#ryer",parent.document).attr("src",$resim);
				</script>
				';
    }
    if(q("sil")=="urunaktif")
    {
        if($aktif==0)
        {
            echo '<script>
						$( "#tr'.q("id").' i.aktif",parent.document ).removeClass( "md-thumb-up" );
						$( "#tr'.q("id").' i.aktif",parent.document ).addClass( "md-error" );
						$( "#tr'.q("id").' td.bilgi",parent.document ).removeClass( "style-info" );
						$( "#tr'.q("id").' td.bilgi",parent.document ).addClass( "style-danger" );
						</script>';
        }
        else
        {
            echo '<script>
					$( "#tr'.q("id").' i.aktif",parent.document ).removeClass( "md-error" );
					$( "#tr'.q("id").' i.aktif",parent.document ).addClass( "md-thumb-up" );
					$( "#tr'.q("id").' td.bilgi",parent.document ).removeClass( "style-danger" );
					$( "#tr'.q("id").' td.bilgi",parent.document ).addClass( "style-info" );
					</script>';
        }
        #tr7325 > td.style-info.text-center > i
    }
    if(q("sil")=="urunfirsat")
    {
        if($islem==0)
        {
            echo '<script>
						$( "#tr'.q("id").' td.firsat",parent.document ).removeClass( "style-warning" );
						$( "#tr'.q("id").' td.firsat",parent.document ).addClass( "style-default" );
						</script>';
        }
        else
        {
            echo '<script>
					$( "#tr'.q("id").' td.firsat",parent.document ).removeClass( "style-default" );
					$( "#tr'.q("id").' td.firsat",parent.document ).addClass( "style-warning" );
					</script>';
        }
        if($islem==0)$islem=1;else $islem=0;
        echo '<script>$( "#tr'.q("id").' td.firsat a",parent.document ).attr("href", "/_y/s/f/sil.php?sil=urunfirsat&id='.q("id").'&islem='.$islem.'");</script>';
        #tr7325 > td.style-info.text-center > i
    }
}
?>
<!--script>
    $("#tr2",parent.document).addClass("alert alert-danger");
    $("#tr2",parent.document).delay(1000).fadeOut();
</script-->
</body>
</html>