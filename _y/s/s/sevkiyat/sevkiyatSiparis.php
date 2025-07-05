<?php
/**
 * @author         adulger
 * @email          abdulkerimdulger@gmail.com
 * @date           11.Mar.2020
 */
?>

    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content">

            <?php
            //setOrder view açar.
            if (isset($_REQUEST["sevkiyat"])) {
                require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");
                require_once($_SERVER["DOCUMENT_ROOT"] . "/sistem/fonksiyon/fonksiyon.php");

                switch ($_REQUEST["sevkiyat"]) {
                    case KARGO_ARAS || KARGO_MNG:
                        $siparisbenzersizid = f("siparisbenzersizid");

                        $siparisSql = "Select *	from uyesiparis Where siparisbenzersizid='" . $siparisbenzersizid . "'";

                        $siparisQuery = $data->query($siparisSql);
                        if ($siparisQuery->num_rows == 1) {
                            $siparis = $siparisQuery->fetch_assoc();

                            $siparisteslimatad = $siparis["siparisteslimatad"];
                            $siparisteslimatsoyad = $siparis["siparisteslimatsoyad"];
                            $siparisteslimatgsm = $siparis["siparisteslimatgsm"];
                            $siparisteslimatadressehir = $siparis["siparisteslimatadressehir"];
                            $siparisteslimatadresilce = $siparis["siparisteslimatadresilce"];
                            $siparisteslimatadressemt = $siparis["siparisteslimatadressemt"];
                            $siparisteslimatadresmahalle = $siparis["siparisteslimatadresmahalle"];
                            $siparisteslimatadresacik = $siparis["siparisteslimatadresacik"];
                            $siparisKargoFirmaCode = $siparis["kargoCode"];
                            $tempBarcodeNumber = $siparis["tempBarcodeNumber"];
                            ?>
                            <div class="modal-header">
                                <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">×
                                </button>
                                <h4 class="modal-title" id="simpleModalLabel">Sevkiyat İşlemleri</h4>
                            </div>
                            <div class="modal-body">

                                <fieldset>
                                    <legend>Sipariş Bilgileri</legend>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Sipariş Kodu :</div>
                                            <div
                                                    class="col-md-7"><?= $siparisbenzersizid ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Tc Kimlik No :</div>
                                            <div
                                                    class="col-md-7">***********
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Ad Soyad :</div>
                                            <div
                                                    class="col-md-7"><?= $siparisteslimatad . " " . $siparisteslimatsoyad ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Telefon :</div>
                                            <div class="col-md-7"><?= $siparisteslimatgsm ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">İl :</div>
                                            <div class="col-md-7"><?= $siparisteslimatadressehir ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">İlce :</div>
                                            <div class="col-md-7"><?= $siparisteslimatadresilce ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Sipariş Kargo Firması :</div>
                                            <div
                                                    class="col-md-7"><?= unserialize(TUM_KARGOLAR)[$siparisKargoFirmaCode]["firmaAdi"] ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Semt :</div>
                                            <div class="col-md-7"><?= $siparisteslimatadressemt ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Mahalle :</div>
                                            <div class="col-md-7"><?= $siparisteslimatadresmahalle ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right text-bold">Adresi :</div>
                                            <div class="col-md-7"><?= $siparisteslimatadresacik ?></div>
                                        </div>
                                    </div>
                                </fieldset>
                                <hr>
                                <fieldset>
                                    <legend>Kargo Bilgileri</legend>
                                    <div class="col-md-12">
                                        <div class="col-md-2 text-right text-bold">Kargo Firması :</div>
                                        <div class="col-md-10">
                                            <div class="col-md-8">
                                                <select id="siparisKargoFirmaCode" name="siparisKargoFirmaCode"
                                                        class="form-control" required=""
                                                        aria-required="true" aria-invalid="false">
                                                    <?php
                                                    $tumKargolar = unserialize(TUM_KARGOLAR);
                                                    foreach ($tumKargolar as $kargoCode => $kargo) {
                                                        if ($kargo["entegrasyon"]) {
                                                            ?>
                                                            <option <?= ($kargoCode == $siparisKargoFirmaCode) ? "selected" : "" ?>
                                                                    value="<?= $kargoCode ?>"><?= $kargo["firmaAdi"] ?></option>
                                                        <?php }
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="kargoForm kargoForm-<?= $_REQUEST["sevkiyat"] ?>">

                                        <!--                            <div class="col-md-6">-->
                                        <!--                                <div class="row">-->
                                        <!--                                    <div class="col-md-5 text-right text-bold">Kargo Fiyatı :</div>-->
                                        <!--                                    <div class="col-md-7">-->
                                        <!--                                        --><?php
                                        //                                        if ($sipariskargofiyat > 0) {
                                        //                                            echo "Ücret Alıcıdan";
                                        //                                        } else {
                                        //                                            echo "Peşin Ödeme";
                                        //                                        }
                                        //
                                        ?>
                                        <!--                                    </div>-->
                                        <!--                                </div>-->
                                        <!---->
                                        <!--                            </div>-->
                                        <!--                            <div class="col-md-6">-->
                                        <!--                                <div class="row">-->
                                        <!--                                    <div class="col-md-5 text-right text-bold">Gönderi Tipi :</div>-->
                                        <!--                                    <div class="col-md-7">-->
                                        <!--                                        --><?php
                                        //                                        if (!isset($siparisdurum_t["siparisteslimatadresulke"]) || $siparisdurum_t["siparisteslimatadresulke"] == 90) {
                                        //                                            echo "Yurtiçi Gönderi";
                                        //                                        } else {
                                        //                                            echo "Yurtdışı Gönderi";
                                        //                                        }
                                        //
                                        ?>
                                        <!--                                    </div>-->
                                        <!--                                </div>-->
                                        <!--                            </div>-->
                                        <hr>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 text-right text-bold">Kargo Desi:</div>
                                                <div class="col-md-7">
                                                    <input type="number" name="kargoDesi" id="kargoDesi"
                                                           class="form-control"
                                                           min="0" value="0" step=".01">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5 text-right text-bold">Ürün Ağırlık(Kg) :</div>
                                                <div class="col-md-7">
                                                    <input type="number" name="kargoAgirlik" id="kargoAgirlik"
                                                           class="form-control" min="0" value="0" step=".01">
                                                </div>
                                            </div>
                                            <!--                                <div class="row">-->
                                            <!--                                    <div class="col-md-5 text-right text-bold">Özel Alan :</div>-->
                                            <!--                                    <div class="col-md-7">-->
                                            <!--                                        <textarea type="text" name="kargoOzelAlan" class="form-control" maxlength="200" rows="2" cols="20"></textarea>-->
                                            <!--                                    </div>-->
                                            <!--                                </div>-->
                                        </div>

                                        <div class="col-md-12">&nbsp;</div>
                                        <div class="col-sm-12">
                                            <div class="form-group text-right">
                                                <button type="button" class="btn btn-primary"
                                                        data-checkString="<?= md5("prefix" . $siparisbenzersizid . "suffix") ?>"
                                                        data-benzersizId="<?= $siparisbenzersizid ?>"
                                                        id="sevkiyatGerceklestir">Bilgileri Kargoya Gönder
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </fieldset>

                            </div>
                            <div class="modal-footer sevkiyatSonuc"></div>
                            <?php

                        } else {
                            echo "Sipariş bulunamadı";
                        }
                        break;
                }
            }

            //Barcode view açar.
            if (isset($_REQUEST["kargoCode"])) {
                require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");
                require_once($_SERVER["DOCUMENT_ROOT"] . "/sistem/fonksiyon/fonksiyon.php");

                $siparisbenzersizid = f("siparisbenzersizid");

                $siparisSql = "Select *	from uyesiparis Where siparisbenzersizid='" . $siparisbenzersizid . "'";

                $siparisQuery = $data->query($siparisSql);
                if ($siparisQuery->num_rows == 1) {
                    $siparis = $siparisQuery->fetch_object();
                    ?>
                    <div class="modal-header">
                        <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">×
                        </button>
                        <h4 class="modal-title" id="simpleModalLabel">Barcode Yazdır</h4>
                        <div class="form-group text-center">
                            <button type="button"
                                    class="btn btn-primary barkodOlusturButton <?= $siparis->siparisKargoBarcode != null ? "hidden" : "" ?>"
                                    data-id="<?= $siparisbenzersizid ?>"
                                    data-kargoCode="<?= $siparis->kargoCode ?>"
                                    data-checkString="<?= md5("prefix" . $siparisbenzersizid . "suffix") ?>"
                                    data-tempBarcodeNumber="<?= $siparis->tempBarcodeNumber ?>"
                                    id="barkodOlusturButton"><i class="fa fa-barcode m-r-md"></i>Barkod Oluştur
                            </button>

                            <button type="button"
                                    class="btn btn-warning text-default-dark <?= $siparis->siparisKargoBarcode == null ? "disabled" : "" ?> printBarcodeButton"
                                    onclick="printImg()"><i
                                        class="fa fa-print m-r-md"></i>Barkod Yazdır
                            </button>
                            <button type="button" class="btn btn-success disabled barcodeSonrasiSiparisIlerletButton"><i
                                        class="fa fa-mail-forward m-r-md"></i>Siparişi İlerlet
                            </button>
                        </div>

                    </div>

                <?php
                //Barcode dialog kodlarıdır.
                switch ($_REQUEST["kargoCode"]) {
                case KARGO_ARAS:
                ?>
                    <script type="text/javascript">
                        function printImg() {
                            Pagelink = "about:blank";
                            var pwa = window.open(Pagelink, "_new");
                            pwa.document.open();
                            pwa.document.write("<img src='" + document.getElementsByTagName("img")[0].src + "' />");
                            pwa.document.close();
                            pwa.print();
                        }
                    </script>

                    <div class="row modal-body barcodeModel">
                        <div class="col-sm-12 barcodeImage text-center">
                            <?php
                            if ($siparis->siparisKargoBarcode != null) {
                                ?>
                                <img id="mainImg" src="<?= json_decode($siparis->siparisKargoBarcode)->Images->base64Binary ?>"/>
                            <?php }
                            ?>
                        </div>
                    </div>
                <?php
                break;
                case KARGO_YURTICI:
                ?>
                    <div class="row modal-body barcodeModel">
                        <div class="col-sm-12 barcodeImage text-center">
                            <?php
                            if ($siparis->siparisKargoBarcode != null) {
                                ?>
                                <img id="mainImg" src="<?= json_decode($siparis->siparisKargoBarcode)->Images->base64Binary ?>"/>
                            <?php }
                            ?>
                        </div>
                    </div>
                <?php
                break;
                case KARGO_MNG:
                $firmaAyarBilgileri = firmaAyarBilgileriForKargo(0);
                ?>
                    <script type="text/javascript">
                        function printImg() {
                            Pagelink = "about:blank";
                            var pwa = window.open(Pagelink, "popup", "top=200,width=420, height=405, toolbar=no, menubar=no, resizable=no, location=no, fullscreen=no");
                            pwa.document.open();
                            pwa.document.write($(".barcodeModel").html());
                            pwa.document.close();
                            pwa.print();
                        }
                    </script>

                    <div class="row modal-body barcodeModel">
                        <h3 style="text-align: center"><?= unserialize(TUM_KARGOLAR)[KARGO_MNG]["firmaAdi"] ?></h3>
                        <p style="text-align: right"><?= date("d.m.Y H:i:s") ?></p>
                        <div class="col-sm-12 barcodeImage text-center" style="text-align: center;">
                            <?php
                            if ($siparis->siparisKargoBarcode != null) {
                                ?>
                                <img id="mainImg" src="<?= json_decode($siparis->siparisKargoBarcode)->Images->base64Binary ?>"/>
                                <p><?= $siparis->tempBarcodeNumber ?></p>
                            <?php }
                            ?>
                        </div>

                        <div class="col-md-8 col-md-offset-3">
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">Gönderici Bilgileri</legend>
                                <div class="control-group">
                                    <label class="control-label" style="width: 10%">Adı :</label>
                                    <label class="control-label" style="width: 89%"><?= $firmaAyarBilgileri["firmaAd"] ?></label>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" style="width: 10%">Adresi :</label>
                                    <label class="control-label" style="width: 89%%"><?= $firmaAyarBilgileri["firmaAdres"] ?></label>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" style="width: 10%">Telefon :</label>
                                    <label class="control-label" style="width: 89%%"><?= $firmaAyarBilgileri["firmaGsm"] ?></label>
                                </div>
                            </fieldset>

                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">Alıcı Bilgileri</legend>
                                <div class="control-group">
                                    <label class="control-label" style="width: 10%">Adı :</label>
                                    <label class="control-label" style="width: 89%"><?= $siparis->siparisteslimatad . " " . $siparis->siparisteslimatsoyad ?></label>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" style="width: 10%">Adresi :</label>
                                    <label class="control-label" style="width: 89%"><?= $siparis->siparisteslimatadresacik . " " . $siparis->siparisteslimatadresilce . "/" . $siparis->siparisteslimatadressehir ?> </label>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" style="width: 10%">Telefon :</label>
                                    <label class="control-label" style="width: 89%"><?= $siparis->siparisteslimatgsm ?> </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <?php
                    break;
                }
                } else {
                    echo "Barkod Kayıt Bulunamadı";
                }
            }
            ?>
        </div>
    </div>
<?php
function firmaAyarBilgileriForKargo($dilid)
{
    global $data;
    if (!$data) Veri(true);

    $sqlAyarFirma = "select * from ayarfirma where dilid=1 and ayarfirmasil=0";
    if ($data->query($sqlAyarFirma)) {
        $ayarfirma_v = $data->query($sqlAyarFirma);
        unset($sqlAyarFirma);
        if ($ayarfirma_v->num_rows > 0) {
            while ($ayarfirma_t = $ayarfirma_v->fetch_assoc()) {
                $firmaad = $ayarfirma_t["ayarfirmaad"];
                $firmaadres = $ayarfirma_t["ayarfirmaadres"];
                $firmagsm = $ayarfirma_t["ayarfirmagsm"];

                return [
                    "firmaAd" => $firmaad,
                    "firmaAdres" => $firmaadres,
                    "firmaGsm" => $firmagsm
                ];
            }
            unset($ayarfirma_t);
        }
        unset($ayarfirma_v);
    } else {
        hatalogisle("Ayar Firma Al", $data->error);
    }

}

?>

<script type="text/javascript">
    function printImg() {
        Pagelink = "about:blank";
        var pwa = window.open(Pagelink, "popup", "top=200,width=420, height=405, toolbar=no, menubar=no, resizable=no, location=no, fullscreen=no");
        pwa.document.open();
        pwa.document.write($(".barcodeModel").html());
        pwa.document.close();
        pwa.print();
    }
</script>
