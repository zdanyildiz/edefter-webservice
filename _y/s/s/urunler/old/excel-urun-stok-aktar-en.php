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
                        <a href="/_y/s/s/urunler/excel-urun-fiyatguncelle-en.php" target="_blank">Örnek dosyayı indirmek için tıklayın.</a>
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
                                        href="#offcanvas-toplufiyaten"
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
                        
                    </tr>
                ";
                foreach ($excelveri as $satirsay => $satir) {
                    echo "<tr>";
                    if ($satirsay == 0) echo "<h3>Aktarım Başlatılıyor ($satirtoplam) Satır</h3>";
                    if (s(q("satir")) != 0) $saybasla = q("satir");
                    if ($satirsay >= $saybasla)
                    {
                        //////////////////////////////////////////////
                        $saybitir++;
                        //////////////////////////////////////////////
                        foreach ($satir as $sutunsay => $sutun)
                        {
                            //if (s(q("sutun")) != 0) $sutunsay = q("sutun");
                            if ($sutunsay == 0)//stok kodu
                            {
                                $stokkodu = $sutun;

                                echo "<td> $stokkodu</td>";
                                $stokkodu=trim($stokkodu,".");
                                $stokkodu=trim($stokkodu," ");
                                $stokkodu2=str_replace(" ",".",$stokkodu);
                                if(strlen($stokkodu)==9){
                                    $stokkodu=substr($stokkodu,0,3).".".substr($stokkodu,3,3).".".substr($stokkodu,6,3);
                                }
                                //die($stokkodu." - ".$stokkodu2);
                            }
                            elseif ($sutunsay == 1)//ürün başlık
                            {
                                $urunbaslik = $sutun;
                                echo "<td>$urunbaslik</td>";
                            }
                            elseif ($sutunsay == 2)//liste fiyatı
                            {
                                $listefiyat = $sutun;
                                $listefiyat = str_replace(",", ".", $listefiyat);

                                if(BosMu($listefiyat)||$listefiyat=="0"||$listefiyat=="0.00")
                                {
                                    $listefiyat="0.00";
                                    $alisfiyat="0.00";
                                    $satisfiyat="0.00";
                                    $indirimsizfiyat="0.00";
                                }
                                else
                                {
                                    //if(!isCurrency($listefiyat))$listefiyat="0.00";
                                    //listefiyat "." sonra çok hane olabilir, yalnızca iki hane alalım
                                    //önce küsürat (.) var mı kontrol edelim

                                    $listefiyatAyir = explode(".", $listefiyat);

                                    if(count($listefiyatAyir)>1)
                                    {
                                        $listefiyat2 = substr($listefiyatAyir[1], 0, 2);
                                        $listefiyat = $listefiyatAyir[0] . "." . $listefiyat2;
                                        $listefiyat=$listefiyat+0.01;
                                        $listefiyat = str_replace(",", ".", $listefiyat);
                                    }
                                    else
                                    {
                                        $listefiyat = $listefiyatAyir[0] . ".00";
                                    }

                                    //$listefiyat = str_replace(",", ".", $listefiyat);
                                    //die($listefiyat);
                                    //$indirimsizfiyat'ı hesaplamak için %20 liste fiyatını arttıralım
                                    $indirimsizfiyat = $listefiyat * 1.2;
                                    $indirimsizfiyat = str_replace(",", ".", $indirimsizfiyat);
                                    $indirimsizfiyatAyir = explode(".", $indirimsizfiyat);
                                    if(count($indirimsizfiyatAyir)>1)
                                    {
                                        $indirimsizfiyat2 = substr($indirimsizfiyatAyir[1], 0, 2);
                                        $indirimsizfiyat = $indirimsizfiyatAyir[0] . "." . $indirimsizfiyat2;
                                        $indirimsizfiyat=$indirimsizfiyat+0.01;
                                        $indirimsizfiyat = str_replace(",", ".", $indirimsizfiyat);
                                    }
                                    else
                                    {
                                        $indirimsizfiyat = $indirimsizfiyatAyir[0] . ".00";
                                    }

                                    //die($listefiyat." - ".$indirimsizfiyat);
                                    //veri tabanında fiyatları tuttuğumuz sütun decimal(8,2) özelliğine sahip olduğu için veri tipini ona göre ayarlayalım
                                    //virgül yerine nokta kullanalım

                                    $indirimsizfiyat = str_replace(",", ".", $indirimsizfiyat);
                                    $alisfiyat = $listefiyat;
                                    $satisfiyat = $listefiyat;
                                }
                                echo "<td>$listefiyat</td>";



                                $sutunlar="urunalisfiyat,urunsatisfiyat,urunindirimsizfiyat";
                                $degerler=$alisfiyat."|*_".$satisfiyat."|*_".$indirimsizfiyat;

                                if(BosMu($stokkodu)&&BosMu($satisfiyat))
                                {
                                    $satirsay=$satirtoplam;
                                    break;
                                }


                                //ürün fiyatları urunozellikleri tablosunda tutuluyor
                                //urunozellikleri tablosundaki sayfaid ile sayfa tablosundaki sayfaid eşleşiyor.
                                //Her sayfa sayfalistekategori tablosundaki sayfaid ile eşleşiyor. Aynı tabloda kategoriid ile kategori tablosundaki kategoriid eşleşiyor.
                                //Kategori tablosunda dilid sütunu var, biz sadece dilid=2 olan ürünlerin fiyatlarını güncelleyeceğiz.
                                //urunozellikleri tablosunda urunstokkodu ile ürün fiyatlarını güncelleyeceğiz.
                                //Şimdi yukarıdaki özellikjlere göre inner joinler ile dilid eşleşmesini ve stokkodu eşleşmesini yapan $where şartlarını yazalım
                                $where="(urunozellikleri.urunstokkodu='".$stokkodu."' or urunozellikleri.urunstokkodu='".$stokkodu2."') and kategori.dilid=2";
                                //yukarıdaki özelliklere göre $tablo ve $joinleri yazalım
                                $tablo="
                                    urunozellikleri 
                                        inner join sayfa on urunozellikleri.sayfaid=sayfa.sayfaid 
                                        inner join sayfalistekategori on sayfa.sayfaid=sayfalistekategori.sayfaid 
                                        inner join kategori on sayfalistekategori.kategoriid=kategori.kategoriid";


                                if (Dogrula($tablo, $where))
                                {
                                    guncelle($sutunlar, $degerler, $tablo, $where, 63);
                                    $durum = "Güncellendi";
                                    //guncelle("urunfiyat",$satisfiyat,"uyesepet","sepetsil='0' and siparisbenzersiz is null and (urunstokkodu='" . $stokkodu . "' or urunstokkodu='" . $stokkodu2 . "')",63);
                                }
                                else
                                {
                                    //ekle($sutunlar,$degerler,"stokkoduaktar",63);
                                    hatalogisle("Stok Kodu Bulunamadı ",$stokkodu);
                                    $durum="hata";
                                }
                            }
                        }
                    }
                    if ($saybitir == 100)
                    {
                        die('<script>window.location.href ="/_y/s/s/urunler/excel-urun-fiyatguncelle-en.php?dosya='.q("dosya").'&satir=' . $satirsay . '";</script>');
                    }
                    if ($satirsay >= ($satirtoplam - 1)) {
                        echo '</tr><td colspan="3"><h3>Ürün verileri aktarıldı</h3></td>';
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
