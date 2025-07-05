<div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
    <div class="col-lg-12"><h4>ÜRÜN FİYAT ÖZELLİKLERİ</h4></div>
    <div class="col-lg-3 col-md-4">
        <article class="margin-bottom-xxl">
            <ul>
                <li>İndirim oranı girerseniz satış fiyatı üzerinden indirim uygulanır</li>
                <!--li>Satış fiyatının yanında üstü çizili olarak liste fiyatı da gösterilsin mi?</li -->
                <li>Üründen en az / en fazla kaç adet alınabilir?</li>
                <li>Ürünün artış kat sayısı nedir?<br>(ürün 6 metre ve katları satılıyorsa 6 yazın)</li>
                <li>Ürünlerinize toplu indirim yaparken bu ürünü indirim dışı tutmak için seçiniz?</li>
                <li>Ürününüz ön siparişe açık mı? Stok bittiğinde de sipariş verilebilsin mi?</li>
                <li>Ürün fiyatı "Fiyat Sorunuz" olarak görünsün mü?</li>
            </ul>
        </article>
    </div>
    <div class="col-lg-offset-1 col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <select id="productCurrency" name="productCurrency" class="form-control">
                                <?php
                                foreach($currencies as $currency){
                                    $selected = ($currency['currencyID'] == $productCurrency) ? 'selected' : '';
                                    echo '<option value="'.$currency['currencyID'].'" '.$selected.'>'.$currency['currencyName'].'</option>';
                                }
                                ?>
                            </select>
                            <label for="parabirimid">Ürün Satış Para Birimi Seçin</label>
                        </div>
                    </div>
                    <div class="col-sm-3 hidden" style="margin-top:20px">
                        <label class="radio-inline radio-styled">
                            <input type="radio"
                                   name="productShowOldPrice" id="productShowOldPrice" value="0"
                                <?= $productShowOldPrice==0 ? 'checked' : '' ?>>
                            <span>Eski Fiyat Gösterme</span>
                        </label>
                        <label class="radio-inline radio-styled">
                            <input type="radio"
                                   name="productShowOldPrice" id="productShowOldPriceYes"
                                   value="1"
                                <?= $productShowOldPrice==1 ? 'checked' : '' ?>>
                            <span>Eski Fiyat Göster <sup style="text-decoration:line-through"> 120 TL</sup> </span>
                        </label>
                    </div>
                    <div class="col-sm-3 hidden">
                        <div class="form-group">
                            <input type="text"
                                   name="productInstallment" id="productInstallment"
                                   class="form-control"
                                   placeholder="9"
                                   value="<?=$productInstallment?>"
                                   data-rule-number="true"
                                   required=""
                                   aria-required="true"
                                   aria-invalid="false">
                            <label for="productInstallment">Ürün Taksit</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                type="text"
                                name="productTax"
                                id="productTax"
                                class="form-control"
                                placeholder="0.20"
                                value="<?=$productTax?>"
                                data-rule-number="true"
                                required=""
                                aria-required="true"
                                aria-invalid="false">
                            <label for="productTax" required aria-required="true">KDV Oranı</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="text" name="productDiscountRate" id="productDiscountRate" class="form-control" placeholder="0.15" value="<?=$productDiscountRate?>"  data-rule-number="true" required="" aria-required="true" aria-invalid="false">
                            <label for="productDiscountRate">Ürün İndirim %10 için 0.10</label>
                        </div>
                    </div>
                    <div class="col-sm-3 hidden">
                        <div class="form-group">
                            <input
                                type="text"
                                name="productSalesQuantity"
                                id="productSalesQuantity"
                                class="form-control"
                                placeholder="99"
                                value="<?=$productSalesQuantity?>"
                                data-rule-digits="true"
                                required=""
                                aria-required="true"
                                aria-invalid="false">
                            <label for="productSalesQuantity">adet satılmıştır</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <select id="productQuantityUnitID" name="productQuantityUnitID" class="form-control">
                                <option value="0">Miktar Birim Seçin</option>
                                <?php
                                foreach($productQuantityUnits as $quantityUnit){
                                    $selected = ($quantityUnit['quantityUnitID'] == $productQuantityUnitID) ? 'selected' : '';
                                    echo '<option value="'.$quantityUnit['quantityUnitID'].'" '.$selected.'>'.$quantityUnit['quantityUnitName'].'</option>';
                                }
                                ?>
                            </select>
                            <label for="productQuantityUnitID">Miktar Birim Seçin</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                type="text"
                                name="productMinimumQuantity"
                                id="productMinimumQuantity"
                                class="form-control"
                                placeholder="1"
                                value="<?=$productMinimumQuantity?>"
                                data-rule-number="true"
                                required=""
                                aria-required="true"
                                aria-invalid="false">
                            <label for="productMinimumQuantity">Ürün Minimum Satış Miktarı</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input
                                type="text"
                                name="productMaximumQuantity"
                                id="productMaximumQuantity"
                                class="form-control"
                                placeholder="5"
                                value="<?=$productMaximumQuantity?>"
                                data-rule-number="true"
                                required=""
                                aria-required="true"
                                aria-invalid="false">
                            <label for="productMaximumQuantity" required aria-required="true">Ürün Maksimum Satış Miktarı</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="text"
                                   name="productCoefficient"
                                   id="productCoefficient"
                                   class="form-control"
                                   placeholder="1"
                                   value="<?=$productCoefficient?>"
                                   data-rule-number="true" required=""
                                   aria-required="true" aria-invalid="false">
                            <label for="productCoefficient">Ürün Artış Kat Sayısı</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <div class="checkbox checkbox-styled">
                                <label>
                                    <input name="productBulkDiscount" id="productBulkDiscount" type="checkbox" value="1" <?php if($productBulkDiscount==1)echo 'checked';?>>
                                    <span>Toplu İndirim Dışı!</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <div class="checkbox checkbox-styled">
                                <label>
                                    <input name="productPreOrder" id="productPreOrder" type="checkbox" value="1" <?php if($productPreOrder==1)echo 'checked';?>>
                                    <span>Ön Sipariş verilebilir</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="checkbox checkbox-styled">
                                <label>
                                    <input name="productPriceAsk" id="productPriceAsk" type="checkbox" value="1" <?php if($productPriceAsk==1)echo 'checked';?>>
                                    <span>Fiyat Sorunuz olarak gelsin</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 hidden">
                        <div class="form-group">
                            <div class="checkbox checkbox-styled">
                                <label>
                                    <input name="productDiscountRateShow" id="productDiscountRateShow" type="checkbox" value="1" <?php if($productDiscountRateShow==1)echo 'checked';?>>
                                    <span>İndirim oranı Göster</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <em class="text-caption">Ürün Fiyat/Stok/Taksit</em>
    </div>
</div>