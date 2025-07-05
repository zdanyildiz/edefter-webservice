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
function stockCodeFormat($text)
{
    // Metin uzunluğunu alın
    $length = strlen($text);

    // Metin içinde nokta var mı diye kontrol edin
    if (strpos($text, '.') !== false) {
        // Eğer metin içinde nokta varsa, fonksiyonu tamamlayın
        return $text;
    }

    // Nokta sayısını bulun
    $dot_count = floor($length / 3);

    // Eğer nokta sayısı 2'den fazla ise, nokta sayısını 2'ye indirin
    if ($dot_count > 2) {
        $dot_count = 2;
    }

    // Nokta sayısı kadar nokta ekleyin
    for ($i = 1; $i <= $dot_count; $i++) {
        $text = substr_replace($text, '.', $i * 3 + $i - 1, 0);
    }
    return $text;

}
if(!BosMu(q("dosya")))
{
    $inputFileName = $_SERVER['DOCUMENT_ROOT'] . "/m/r/havuz/" .q("dosya");
    if(file_exists($inputFileName))
    {

        require_once $_SERVER['DOCUMENT_ROOT'] . '/_y/PHPExcel/Autoloader.php';
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            for ($row = 2; $row <= $highestRow; $row++) {
                $urunstokkod = stockCodeFormat(trim($worksheet->getCellByColumnAndRow(1, $row)->getValue()));

                $urunaltbaslik = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());
                $urunalisfiyat = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue());

                // Ürün satış fiyatını hesaplayın
                $urunsatisfiyat = number_format($urunalisfiyat * 1.20 * 1.15,2,".","");
                $urunindirimsizfiyat = number_format($urunsatisfiyat * 1.2,2,".","");

                echo "$urunstokkod - $urunalisfiyat - $urunsatisfiyat - $urunindirimsizfiyat <br>";
            }
        }
    }
    else
    {
        echo "dosya bulunamadı";
    }
}
?>
