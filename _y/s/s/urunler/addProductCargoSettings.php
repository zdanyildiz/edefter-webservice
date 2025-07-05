<div class="row margin-bottom-xxl border-gray" style="background-color: antiquewhite;">
    <div class="col-lg-12"><h4>ÜRÜN KARGO ÖZELLİKLERİ</h4></div>

        <div class="col-lg-3 col-md-4">
            <article class="margin-bottom-xxl">
                <p>Ortalama Teslimat Süresi (Gün)</p>
                <p>Sabit Kargo Ücreti (Kargo ayarlarından alır)</p>
                <p>Ürün görseli ya da detaylarında ücretsiz kargo, aynı gün kargo etiketleri görünsün isterseniz seçili hale getirin.</p>
            </article>
        </div>
        <div class="col-lg-offset-1 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" name="productDesi" id="productDesi" class="form-control" placeholder="3" value="<?=$productDesi?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                                <label for="urundesi">Ürün Desi</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" name="productCargoTime" id="productCargoTime" class="form-control" placeholder="3" value="<?=$productCargoTime?>" data-rule-digits="true" required="" aria-required="true" aria-invalid="false">
                                <label for="urunkargosuresi">Kargo Süresi (Gün)</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" name="productFixedCargoPrice" id="productFixedCargoPrice" class="form-control" placeholder="4.50" value="<?=$productFixedCargoPrice?>" data-rule-number="true" required="" aria-required="true" aria-invalid="false" readonly>
                                <label for="urunsabitkargoucreti">Sabit Kargo Ücreti</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="checkbox checkbox-styled">
                                    <label>
                                        <input name="kargosistem" id="kargosistem" type="checkbox" value="0" <?php if($productFixedCargoPrice="0.00")echo 'checked';?> readonly>
                                        <span>Sistem</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="checkbox checkbox-styled">
                                    <label>
                                        <input name="productSameDayShipping" id="productSameDayShipping" type="checkbox" value="1" <?php if($productSameDayShipping==1)echo 'checked';?>>
                                        <span>Aynı Gün Kargo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="checkbox checkbox-styled">
                                    <label>
                                        <input name="productFreeShipping" id="productFreeShipping" type="checkbox" value="1" <?php if($productFreeShipping==1)echo 'checked';?>>
                                        <span>Ücretsiz Kargo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <em class="text-caption">Ürün Kargo Bilgileri</em>
        </div>

    <div class="row hidden">
        <div class="col-lg-3 col-md-4">
            <article class="margin-bottom-xxl">

                <p>Bu ürünle ilgili gönderim yapabileceğiniz kargoları seçin</p>
            </article>
        </div>
        <div class="col-lg-offset-1 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <?php
                        if(!empty($cargos))
                        {
                            foreach ($cargos as $key => $cargo){
                                $cargoID = $cargo['cargoID'];
                                $cargoName = $cargo['cargoName'];

                                if($productCargo == 0)
                                {
                                    if($key == 0) $cargoCheck = "checked";
                                    else $cargoCheck = "";
                                }
                                else
                                {
                                    if($productCargo == $cargoID) $cargoCheck = "checked";
                                    else $cargoCheck = "";
                                }
                                ?>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="checkbox checkbox-styled">
                                            <label>
                                                <input name="kargo" id="kargo" type="radio" value="<?=$cargoID?>" <?=$cargoCheck?> >
                                                <span><?=$cargoName?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <em class="text-caption">Ürün Anasayfa/Ücretsiz Kargo/Anında Kargo/Yeni ürün</em>
        </div>
    </div>


</div>