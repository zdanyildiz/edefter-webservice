<?php
function isCurrency($number)
{
    return preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $number);
}
if(BosMu(q("dosya")))
{
    ?>
    <div class="section-body contain-lg">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="text-primary">Excel Dosyanızı Yükleyin</h1>
            </div><!--end .col -->
            <div class="col-lg-8">
                <article class="margin-bottom-xxl">
                    <p class="lead">
                        sadece xls,xlsx uzantılı olabilir.
                    </p>
                    <p class="lead">
                        <a href="/_y/s/s/urunler/excel-urun-fiyatguncelle.php" target="_blank">Örnek dosyayı indirmek için tıklayın.</a>
                    </p>
                </article>
            </div>
        </div>
        <!-- form class="form" method="post" -->
        <h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <article class="margin-bottom-xxl">
                    <h4>Excel Dosyası</h4>
                    <p><code>Sadece ".xls, .xlsx" uzantılı dosyalar</code></p>
                </article>
            </div>
            <div class="col-lg-offset-1 col-md-8">
                <div class="card">
                    <div class="card-head style-primary">
                        <hedaer> <span style="padding: 0 0 0 20px;font-size: 16px">Ürün Listesi Yükle</span></hedaer>
                    </div>
                    <div class="card-body" id="dosyagovde">
                        <div class="form-group floating-label" id="dosyakutu1">
                            <div class="margin-bottom-xxl">
                                <div class="pull-left width-3 clearfix hidden-xs" id="dkon">
                                    <img id="dyer" class="img-circle size-2" src="<?=$dekleresim?>" alt="">
                                </div>
                                <h1 class="text-light no-margin" id="dad">Liste</h1>
                                <h5>Yeni Ekle</h5>
                                <div class="hbox-column v-top col-md-1">
                                    <a
                                        class="btn btn-floating-action ink-reaction"
                                        href="#offcanvas-toplufiyat"
                                        id="dyeniekle"
                                        data-dosyakutu="dosyakutu1"
                                        data-toggle="offcanvas"
                                        title="ekle">
                                        <i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <em class="text-caption">ürün listesi yükleyin</em>
                </div>
            </div>
            <!--/form -->
        </div>
        <div class="modal fade in" id="textModal" tabindex="-1" role="dialog" aria-labelledby="textModalLabel" aria-hidden="false">
            <div class="modal-backdrop fade in" style="height: 1019px;"></div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="textModalLabel">Ürün Listesi Yükleme</h4>
                    </div>
                    <div class="modal-body">
                        <p>DOSYA YÜKLENDİ</p>
                        <span><a id="adimlink" href="#">Buradan devam edin</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
if(!BosMu(q("dosya")))
{
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/m/r/havuz/" . q("dosya")))
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/_y/exceloku/src/SimpleXLSX.php';
        $xlsx = "";
        $excelveri = "";
        $satir = "";
        if ($xlsx = SimpleXLSX::parse($_SERVER['DOCUMENT_ROOT'] . "/m/r/havuz/" . q("dosya")))
        {
            $excelveri = $xlsx->rows();
            if (isset($excelveri))
            {
                echo '
                <div class="table-responsive">
                    <div id="datatable2_wrapper" class="dataTables_wrapper no-footer">
                        <table class="table no-margin table-hover">
                ';
                $satirtoplam = count($excelveri);
                $saybasla = 0;
                $saybitir = 0;
                if ($saybasla >= $satirtoplam) {
                    echo '<h3>Toplu Güncelleme Tamamlandı</h3>';
                    //die('<script>window.location.href ="/_y/s/s/excel/excel-urun-yukle.php?adim=8&dosya=/m/r/havuz/urun.xlsx";</script>');
                }
                echo "
                    <tr>
                        <td>Stok Kodu</td>
                        <td>Ürün Adı</td>
                        <td>Alış Fiyat</td>
                        <td>Satış Fiyat</td>
                        <td>İndirimsiz Fiyat</td>
                        <td>Oran</td>
                    </tr>
                ";
                foreach ($excelveri as $satirsay => $satir) {
                    echo "<tr>";
                    if ($satirsay == 0) echo "<h3>Aktarım Başlatılıyor ($satirtoplam) Satır</h3>";
                    if (s(q("satir")) != 0) $saybasla = q("satir");
                    if ($satirsay >= $saybasla)
                    {
                        if($satirsay==0)
                            continue;

                        $urunmalzemeid=0;
                        $urunmalzemegrupid=0;
                        $urunrenkid=0;
                        $urunrenkgrupid=0;
                        $urunbedenid=0;
                        $urunbedengrupid=0;
                        //////////////////////////////////////////////
                        $saybitir++;
                        //////////////////////////////////////////////
                        foreach ($satir as $sutunsay => $sutun)
                        {
                            //if (s(q("sutun")) != 0) $sutunsay = q("sutun");
                            if ($sutunsay == 0)//stok kodu
                            {
                                $stokkodu = $sutun;

                                echo "<td>$stokkodu</td>";
                            }
                            /*elseif ($sutunsay == 1)//Ölçü
                            {
                                $urunbeden = $sutun;
                                if(!BosMu($urunbeden))
                                {
                                    if(trim($urunbeden)!="-")
                                    {
                                        $urunbedenbilgi=coksatir("SELECT urunbedenid,urunbedengrupid from urunbeden where urunbedensil=0 and urunbedenad='".$urunbeden."'");
                                        if(is_array($urunbedenbilgi))
                                        {
                                            $urunbedenid=$urunbedenbilgi["urunbedenid"];
                                            $urunbedengrupid=$urunbedenbilgi["urunbedengrupid"];
                                        }
                                    }
                                }
                                echo "<td>$urunbeden [$urunbedenid]</td>";
                            }
                            elseif ($sutunsay == 2)//Malzeme
                            {
                                $urunmalzeme = $sutun;
                                if(!BosMu($urunmalzeme))
                                {
                                    if(trim($urunmalzeme)!="-")
                                    {
                                        $urunmalzemebilgi=coksatir("SELECT urunmalzemeid,urunmalzemegrupid from urunmalzeme where urunmalzemesil=0 and urunmalzemead='".$urunmalzeme."'");
                                        if(is_array($urunmalzemebilgi))
                                        {
                                            $urunmalzemeid=$urunmalzemebilgi["urunmalzemeid"];
                                            $urunmalzemegrupid=$urunmalzemebilgi["urunmalzemegrupid"];
                                        }
                                    }
                                }
                                echo "<td>$urunmalzeme [$urunmalzemeid]</td>";
                            }
                            elseif ($sutunsay == 3)//renk
                            {
                                $urunrenk = $sutun;
                                if(!BosMu($urunrenk))
                                {
                                    if(trim($urunrenk)!="-")
                                    {
                                        $urunrenkbilgi=coksatir("SELECT urunrenkid,urunrenkgrupid from urunrenk where urunrenksil=0 and urunrenkad='".$urunrenk."'");
                                        if(is_array($urunrenkbilgi))
                                        {
                                            $urunrenkid=$urunrenkbilgi["urunrenkid"];
                                            $urunrenkgrupid=$urunrenkbilgi["urunrenkgrupid"];
                                        }
                                    }
                                }
                                echo "<td>$urunrenk [$urunrenkid]</td>";
                            }*/
                            elseif ($sutunsay == 1)//ürün başlık
                            {
                                $urunbaslik = $sutun;
                                echo "<td>$urunbaslik</td>";
                            }
                            elseif ($sutunsay == 2)//liste fiyatı
                            {
                                $listefiyat = $sutun;
                                if(BosMu($listefiyat)||$listefiyat=="0"||$listefiyat=="0.00"||$listefiyat=="0,00")
                                {
                                    $listefiyat="0.00";
                                    $alisfiyat="0.00";
                                    $satisfiyat="0.00";
                                    $indirimsizfiyat="0.00";
                                }
                            }
                            elseif ($sutunsay == 3)//oran
                            {
                                $oran = "1.".$sutun;
                                if($listefiyat!="0.00")
                                {
                                    $listefiyat = str_replace(',', '.',$listefiyat);
                                    if(is_numeric($listefiyat))
                                    {
                                        $alisfiyat = $listefiyat*1.20;
                                        $alisfiyat = number_format($alisfiyat, 2, '.', '');

                                        $satisfiyat = $listefiyat*$oran;
                                        $satisfiyat = $satisfiyat*1.20;
                                        $satisfiyat = number_format($satisfiyat, 2, '.', '');

                                        $indirimsizfiyat = (($listefiyat*$oran)*1.25)*1.20;
                                        $indirimsizfiyat = number_format($indirimsizfiyat, 2, '.', '');
                                    }
                                    else
                                    {
                                        $alisfiyat=$listefiyat;
                                        $satisfiyat=$listefiyat;
                                        $indirimsizfiyat=$listefiyat;
                                    }
                                }
                                else
                                {
                                    $alisfiyat=$listefiyat;
                                    $satisfiyat=$listefiyat;
                                    $indirimsizfiyat=$listefiyat;
                                }

                                if(!isCurrency($listefiyat))$listefiyat="0.00";
                                echo "<td>$alisfiyat</td>";
                                echo "<td>$satisfiyat</td>";
                                echo "<td>$indirimsizfiyat</td>";
                                //if(!BosMu($altbaslik))hatalogisle("Dikkat $stokkodu :",$altbaslik);

                                $sutunlar="urunalisfiyat,urunsatisfiyat,urunindirimsizfiyat,urunparabirim,urunaltbaslik";
                                $degerler=$alisfiyat."|*_".$satisfiyat."|*_".$indirimsizfiyat."|*_3|*_".addslashes($urunbaslik);
                                echo "<td>$oran</td>";
                                if(BosMu($stokkodu)&&BosMu($satisfiyat))
                                {
                                    $satirsay=$satirtoplam;
                                    break;
                                }
                                $stokkodu=trim($stokkodu,".");
                                $stokkodu2=str_replace("."," ",$stokkodu);
                                $stokkodu3=str_replace(" ",".",$stokkodu);
                                $stokkodu4=str_replace(".","",$stokkodu);
                                $stokkodu5=str_replace(" ","",$stokkodu);
                                if(strlen($stokkodu)==9){
                                    $stokkodu6=substr($stokkodu,0,3).".".substr($stokkodu,3,3).".".substr($stokkodu,6,3);
                                $stokkodu7=substr($stokkodu,0,3)." ".substr($stokkodu,3,3)." ".substr($stokkodu,6,3);
                                }
                                else
                                {
                                    $stokkodu6=$stokkodu;
                                    $stokkodu7=$stokkodu;
                                }
                                if (Dogrula("urunozellikleri", "urunstokkodu='" . $stokkodu . "' or urunstokkodu='" . $stokkodu2 . "' or urunstokkodu='" . $stokkodu3 . "' or urunstokkodu='" . $stokkodu4 . "' or urunstokkodu='" . $stokkodu5 . "' or urunstokkodu='" . $stokkodu6 . "' or urunstokkodu='" . $stokkodu7 . "'"))
                                {
                                    guncelle($sutunlar, $degerler, "urunozellikleri", "urunstokkodu='" . $stokkodu . "' or urunstokkodu='" . $stokkodu2 . "' or urunstokkodu='" . $stokkodu3 . "' or urunstokkodu='" . $stokkodu4 . "' or urunstokkodu='" . $stokkodu5 . "' or urunstokkodu='" . $stokkodu6 . "' or urunstokkodu='" . $stokkodu7 . "'", 63);
                                    $durum = "Güncellendi";
                                    guncelle("urunfiyat",$satisfiyat,"uyesepet","sepetsil='0' and siparisbenzersiz is null and (urunstokkodu='" . $stokkodu . "' or urunstokkodu='" . $stokkodu2 . "' or urunstokkodu='" . $stokkodu3 . "' or urunstokkodu='" . $stokkodu4 . "' or urunstokkodu='" . $stokkodu5 . "' or urunstokkodu='" . $stokkodu6 . "' or urunstokkodu='" . $stokkodu7 . "')",63);
                                }
                                else
                                {
                                    //ekle($sutunlar,$degerler,"stokkoduaktar",63);
                                    hatalogisle("Stok Kodu Bulunamadı ",$stokkodu);
                                    $durum="hata";
                                }
                            }
                            /*elseif ($sutunsay == 6)//alış fiyatı
                            {
                                $alisfiyat = $sutun;
                                if(BosMu($alisfiyat)||$alisfiyat=="0"||$alisfiyat=="0.00"||$alisfiyat=="0,00")
                                {
                                    $listefiyat="0.00";
                                }
                                else
                                {
                                    $alisfiyat = str_replace( ',', '.',$alisfiyat);
                                    $alisfiyat = $alisfiyat*1.18;
                                    $alisfiyat = number_format($alisfiyat, 2, '.', ',');
                                }
                                if(!isCurrency($alisfiyat))$alisfiyat="0.00";
                                echo "<td>$alisfiyat</td>";
                            }
                            elseif ($sutunsay == 7)//satış fiyatı
                            {
                                $satisfiyat = $sutun;
                                if(BosMu($satisfiyat)||$satisfiyat=="0"||$satisfiyat=="0.00"||$satisfiyat=="0,00")
                                {
                                    $satisfiyat="0.00";
                                }
                                else
                                {
                                    $satisfiyat = str_replace( ',', '.',$satisfiyat);
                                    $satisfiyat = $satisfiyat*1.18;
                                    $satisfiyat = number_format($satisfiyat, 2, '.', ',');
                                }
                                if(!isCurrency($satisfiyat))$satisfiyat="0.00";
                                echo "<td>$satisfiyat</td>";
                            }*/
                            /*elseif ($sutunsay == 8)//link
                            {
                                $urunlink = $sutun;
                                $model_konum = mb_strrchr($urunlink, "-");
                                $model = str_replace("-", "", $model_konum);
                                $model = str_replace(".html", "", $model);
                                echo "<td> $model </td>";
                            }
                            elseif($sutunsay == 9)//altbaslik
                            {
                                $altbaslik = $sutun;
                                if(Bosmu($stokkodu))
                                {
                                    die('Yükleme başarılı');
                                }
                                else
                                {
                                    $stokkodu=trim($stokkodu);
                                    $durum="";
                                    //echo "<td>$urunlink</td>";
                                    $sutunlar = "
                                        urunindirimsizfiyat,
                                        urunalisfiyat,
                                        urunsatisfiyat
                                    ";
                                    $degerler =
                                        $listefiyat . "|*_" .
                                        $alisfiyat . "|*_" .
                                        $satisfiyat;
                                    //aynı stokkodları eklenmiyor.
                                    if (Dogrula("urunozellikleri", "urunstokkodu='" . $stokkodu . "'"))
                                    {
                                        guncelle($sutunlar, $degerler, "urunozellikleri", "urunstokkodu='" . $stokkodu . "'", 63);
                                        $durum = "Güncellendi";
                                    }
                                    else
                                    {
                                        //ekle($sutunlar,$degerler,"stokkoduaktar",63);
                                        hatalogisle("Stok Kodu Bulunamadı",$stokkodu);
                                        $durum="Eklendi";
                                    }
                                    echo "<td>$durum</td>";$durum="";
                                    //echo "$satirsay ) $durum - $stokkodu - $urunmalzeme - $urunbeden - $urunrenk - $urunbaslik - $alisfiyat - $satisfiyat - $listefiyat - $urunlink ".'<br>';
                                    $alisfiyat="";
                                    $satisfiyat="";
                                    $listefiyat="";
                                    $urunlink="";
                                    $stokkodu="";
                                }
                            }*/
                        }
                    }
                    if ($saybitir == 100)
                    {
                        die('<script>window.location.href ="/_y/s/s/urunler/excel-urun-fiyatguncelle.php?dosya='.q("dosya").'&satir=' . $satirsay . '";</script>');
                    }
                    if ($satirsay >= ($satirtoplam - 1)) {
                        echo '</tr><td colspan="6"><h3>Ürün verileri aktarıldı</h3></td>';
                    }
                    echo "</tr>";
                }
                echo '</table>
            </div>
        </div>';
            }
        }
        else
        {
            echo SimpleXLSX::parseError();
        }
    }
    else
    {
        echo "dosya bulunamadı";
    }
}
?>
