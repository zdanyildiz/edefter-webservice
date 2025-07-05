<div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
    <div class="col-lg-12">
        <h4>ÜRÜN SEÇENEKLERİ - RENK - BEDEN - MALZEME - ŞEKİL - ÖLÇÜ vb. gibi seçenekler ekleyebilirsiniz</h4>
    </div>
    <div class="col-md-12 card">
        <div class="row card-body">
            <div class="checkbox checkbox-styled">
                <label>
                    <input name="digersecenek" id="digersecenek" class="digersecenek" type="checkbox" value="1" <?php if(isset($urun_varyant)) echo 'checked'?>>
                    <span>Farklı Seçenekler Ekle</span>
                </label>
            </div>
            <div class="row yenisecenekdiv <?php if(!isset($urun_varyant)) echo 'hidden'?>">
                <div class="form-group floating-label col-md-12">
                    <?php
                    if(!empty($variantGroups)){
                        foreach ($variantGroups as $variantGroup){
                            $variantGroupID = $variantGroup['variantGroupID'];
                            $variantGroupName = $variantGroup['variantGroupName'];

                            if(!empty($productVariantProperties)){
                                foreach ($productVariantProperties as $productVariantProperty){
                                    $variantID = $productVariantProperty['variantID'];
                                    $variantName = $productVariantProperty['variantName'];
                                    $variantProperties = $productVariantProperty['variantProperties'];

                                    if(!empty($variantProperties)){
                                        foreach ($variantProperties as $variantProperty){
                                            $attribute = $variantProperty['attribute'];
                                            $attributeName = $attribute['name'];
                                            $attributeValue = $attribute['value'];

                                            if($attributeName == $variantGroupName){
                                                echo '<div class="col-md-4">
                                                        <label class="checkbox-inline checkbox-styled checkbox-primary">
                                                            <input class="vg_goster" id="i_'.$variantGroupID.'" type="checkbox" value="'.$variantGroupID.'" data-VG="'.$variantGroupName.'" checked><span>'.$variantGroupName.'</span>
                                                        </label>
                                                    </div>';
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else{
                        ?>
                        <div class="form-group floating-label col-md-4">
                            <div class="input-group">
                                <div class="input-group-content">
                                    <input type="text" class="form-control" id="yenisecenekgrupad" value="">
                                    <label for="yenisecenekgrupad">Seçeneği buraya yazın</label>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-default yenisecenekgrupbuton" type="button">EKLE</button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="row secenekgrupkutu">
            <div class="col-md-12 secenekgrupdiv">
                <?php
                if(!empty($variantGroups)){
                    foreach ($variantGroups as $variantGroup){
                        $varyantgrup_id=$variantGroup["variantGroupID"];
                        $urunekozellik_grupad=$variantGroup["variantGroupName"];
                        $urunekozellik_grupid=Duzelt($urunekozellik_grupad);
                        $hidden_yaz="hidden";
                        if(isset($productVariantProperties)&&!empty($productVariantProperties))
                        {
                            foreach ($productVariantProperties as $productVariantProperty){
                                $variantProperties = $productVariantProperty['variantProperties'];

                                if(!empty($variantProperties)){
                                    foreach ($variantProperties as $variantProperty){
                                        $attribute = $variantProperty['attribute'];
                                        $attributeName = $attribute['name'];

                                        if($attributeName == $urunekozellik_grupad){
                                            $hidden_yaz="";
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        echo '
                            <div class="col-md-4 border-gray varyantgrup margin-bottom-xxl '.$hidden_yaz.'" id="div'.$urunekozellik_grupid.'" style="height:300px;overflow-y: auto">
                                <div class="form-group has-success has-feedback">
                                    <label for="groupbutton17" class="col-sm-12 control-label opacity-100">
                                        <span class="secenekgrupad">'.$urunekozellik_grupad.'</span> değerlerini seçin
                                    </label>
                                    <div class="col-sm-12 margin-bottom-xxl hidden">
                                        <div class="input-group">
                                            <div class="input-group-content">
                                                <input type="text" name="yenisecenekad" class="form-control" id="'.$urunekozellik_grupid.'">
                                                <div class="form-control-line"></div>
                                            </div>
                                            <div class="input-group-btn">
                                                <button class="btn btn-success altsecenekekle" type="button" data-id="'.$urunekozellik_grupid.'">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <button class="btn btn-danger secenekgrupsil hidden" type="button" data-id="'.$urunekozellik_grupid.'">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <ul class="list divider-full-bleed"  id="ul'.$urunekozellik_grupid.'">
                                ';

                                $urun_varyant="";
                                if(isset($productVariantProperties)&&!empty($productVariantProperties))
                                {
                                    foreach ($productVariantProperties as $productVariantProperty){
                                        $variantProperties = $productVariantProperty['variantProperties'];

                                        if(!empty($variantProperties)){
                                            foreach ($variantProperties as $variantProperty){
                                                $attribute = $variantProperty['attribute'];
                                                $attributeName = $attribute['name'];
                                                $attributeValue = $attribute['value'];

                                                if($attributeName == $urunekozellik_grupad){
                                                    $urun_varyant=$productVariantProperty;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                if(isset($urun_varyant)&&!empty($urun_varyant))
                                {

                                    $urunekozellik_id=sifreuret(5,1);
                                    $urunekozellik_grupid="G0";
                                    $urun_varyant_ozellik="";
                                    $check_yaz="";
                                    $aktif="";
                                    if(isset($urun_varyant['variantProperties'])&&!empty($urun_varyant['variantProperties']))
                                    {
                                        foreach ($urun_varyant['variantProperties'] as $variantProperty){
                                            $attribute = $variantProperty['attribute'];
                                            $attributeName = $attribute['name'];
                                            $attributeValue = $attribute['value'];

                                            if($attributeName == $urunekozellik_grupad){
                                                $urunekozellik_grupid=$urunekozellik_grupad;
                                                $urunekozellik_id=duzelt($attributeValue);
                                                $urun_varyant_ozellik=$attributeValue;
                                                $check_yaz="checked";
                                                $aktif="active";
                                            }

                                            echo '
                                            <li class="col-md-12 tile '.$aktif.'" id="li' . $urunekozellik_id . '">
                                                <input type="hidden" name="' . $urunekozellik_grupid . '_yenisecenekid[]" value="' . $urunekozellik_id . '">
                                                <input type="hidden" name="' . $urunekozellik_grupid . '_yenisecenekad[]" value="' . $attributeValue . '">
                                                <div class="tile-text hidden">' . $attributeValue . '</div>
                                                <label class="checkbox-inline checkbox-styled checkbox-primary">
                                                    <input class="li_aktif" id="c_'.$urunekozellik_id.'" type="checkbox" value="'.$urunekozellik_id.'" data-vg="'.$urunekozellik_grupid.'" '.$check_yaz.'>
                                                    <span>' . $attributeValue . '</span>
                                                </label>
                                            </li>';
                                        }
                                    }

                                }
                                    echo '
                                        <input type="hidden" name="yenisecenekgrupad[]" value="'.$urunekozellik_grupad.'">
                                        <input type="hidden" name="yenisecenekgrupid[]" value="'.$urunekozellik_grupid.'">
                                    </ul>
                                </div>
                            </div>';

                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div class="row margin-bottom-lg">
    <div class="col-lg-3 col-md-4"></div>
    <div class="col-lg-offset-1 col-lg-3 col-md-4 margin-bottom-lg ">
        <button type="button" class="btn btn-block ink-reaction btn-default-dark <?php if(!isset($urun_varyant)){?>disabled<?php }?> varyantolustur margin-bottom-lg">Seçenekleri Oluştur</button>
    </div>
</div>
<div class="row varyant_satir_kutu_satir">
    <div class="col-lg-12 col-md-12 margin-bottom-lg">
        <div class="card">
            <div class="ui-sortable card-body varyant_satir_kutu">
                <?php
                if(isset($productVariantProperties)&&!empty($productVariantProperties))
                {
                    foreach ($productVariantProperties as $productVariantProperty){
                        $variantID = $productVariantProperty['variantID'];
                        $variantName = $productVariantProperty['variantName'];
                        $variantProperties = $productVariantProperty['variantProperties'];

                        if(!empty($variantProperties)){
                            foreach ($variantProperties as $variantProperty){
                                $attribute = $variantProperty['attribute'];
                                $attributeName = $attribute['name'];
                                $attributeValue = $attribute['value'];

                                if($attributeName == $urunekozellik_grupad){
                                    $urunekozellik_grupid=$urunekozellik_grupad;
                                    $urunekozellik_id=duzelt($attributeValue);
                                    $urun_varyant_ozellik=$attributeValue;
                                }
                            }
                        }
                        
                        if(isset($productVariantProperty['variantSellingPrice']))
                        {
                            $productSalePrice=$productVariantProperty['variantSellingPrice'];
                        }
                        if(isset($productVariantProperty['variantPriceWithoutDiscount']))
                        {
                            $productNonDiscountedPrice=$productVariantProperty['variantPriceWithoutDiscount'];
                        }
                        if(isset($productVariantProperty['variantStockCode']))
                        {
                            $productStockCode=$productVariantProperty['variantStockCode'];
                        }
                        if(isset($productVariantProperty['variantQuantity']))
                        {
                            $productStock=$productVariantProperty['variantQuantity'];
                        }
                        if(isset($productVariantProperty['variantGTIN']))
                        {
                            $f_urungtin=$productVariantProperty['variantGTIN'];
                        }
                        if(isset($productVariantProperty['variantMPN']))
                        {
                            $f_urunmpn=$productVariantProperty['variantMPN'];
                        }
                        if(isset($productVariantProperty['variantBarkod']))
                        {
                            $f_urunbarkod=$productVariantProperty['variantBarkod'];
                        }
                        if(isset($productVariantProperty['variantOEM']))
                        {
                            $f_urunoem=$productVariantProperty['variantOEM'];
                        }
                        $variantImage_sql="
                            Select 
                                resim.resimid,resim.resim,rk.resimklasorad
                            from 
                                sayfalisteresim 
                                    inner join resim on resim.resimid=sayfalisteresim.resimid
                                    inner join resimklasor rk on resim.resimklasorid = rk.resimklasorid
                            where 
                                sayfaid=$productID and stockcode ='$productStockCode'
                        ";
                        
                        $variantImage=$db->select($variantImage_sql);
                        
                        if($variantImage)
                        {
                            $variantImage=$variantImage[0];
                            
                            $variant_image_id=$variantImage['resimid'];
                            $variant_image='<img class="variantImage img-thumbnail" data-stockcode="'.$productStockCode.'" src="/m/r/?resim='.$variantImage['resimklasorad'].'/'.$variantImage['resim'].'&g=98&y=110" width="98" height="110" style="cursor:pointer">
                            <input type="hidden" class="variantImageInput" data-stockcode="'.$productStockCode.'" name="variantImage[]" value="'.$variant_image_id.'">';
                        }
                        else
                        {
                            $variant_image_id=0;
                            $variant_image='<img class="variantImage img-thumbnail" data-stockcode="'.$productStockCode.'" src="/m/r/logo_anasayfa.png" width="98" height="110" style="cursor:pointer">
                            <input type="hidden" class="variantImageInput" data-stockcode="'.$productStockCode.'" name="variantImage[]" value="'.$variant_image_id.'">';
                        }
                        echo '
                            <div id="varyant_satir_'.$urunekozellik_grupid.'-'.$urunekozellik_id.'" class="varyant_satir border-gray hover" style="padding:5px;display:inline-block">
                                <div class="col-sm-2">
                                    <div class="form-group varyant_ozellikler_div">
                                        <input type="hidden"
                                            name="urunekozellikid[]" id="varyant_ozellikler_'.$urunekozellik_grupid.'-'.$urunekozellik_id.'" value="'.$urunekozellik_grupid.'-'.$urunekozellik_id.'">
                                        <input type="hidden" name="varyantekozellikler[]" id="varyantekozellikler_'.$urunekozellik_grupid.'-'.$urunekozellik_id.'" value="'.$urun_varyant_ozellik.'">
                                        <label for="varyant_ozellikler_'.$urunekozellik_grupid.'-'.$urunekozellik_id.'">Özellikler<br>'.$urunekozellik_grupid.'-'.$urunekozellik_id.'</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_stokkodu_div">
                                        <input type="text"
                                            name="urunstokkodu[]" id="varyant_stokkodu_'.$urunekozellik_id.'" value="'.$productStockCode.'"
                                            autocomplete="off" class="form-control text-danger" aria-invalid="false" required aria-required="true">
                                        <label for="varyant_stokkodu_'.$urunekozellik_id.'">Stok Kodu</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_oem_div">
                                        <input type="text"
                                            name="urunoem[]" id="varyant_oem_'.$urunekozellik_id.'" value="'.$f_urunoem.'"
                                            autocomplete="off" class="form-control text-danger" aria-invalid="false">
                                        <label for="varyant_oem_'.$urunekozellik_id.'">OEM</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_gtin_div">
                                        <input type="text"
                                            name="urungtin[]" id="varyant_gtin_'.$urunekozellik_id.'" value="'.$f_urungtin.'"
                                            autocomplete="off" class="form-control text-danger" aria-invalid="false">
                                        <label for="varyant_gtin_'.$urunekozellik_id.'">GTIN</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_mpn_div">
                                        <input type="text"
                                            name="urunmpn[]" id="varyant_mpn_'.$urunekozellik_id.'" value="'.$f_urunmpn.'"
                                            autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                        <label for="varyant_mpn_'.$urunekozellik_id.'">MPN</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_barkod_div">
                                        <input type="text"
                                            name="urunbarkod[]" id="varyant_barkod_'.$urunekozellik_id.'" value="'.$f_urunbarkod.'"
                                            autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                        <label for="varyant_barkod_'.$urunekozellik_id.'">Barkod</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <a class="btn btn-flat ink-reaction varyant_satir_sil" data-id="'.$urunekozellik_id.'" data-ul="'.$urunekozellik_grupid.'">
                                        <i class="md md-delete"></i>
                                    </a>
                                    '.$variant_image.' 
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_stok_div">
                                        <input type="text" data-rule-digits="true"
                                            name="urunstok[]" id="varyant_stok_'.$urunekozellik_id.'" value="'.$productStock.'"
                                            autocomplete="off"  class="form-control text-danger"  aria-invalid="true" required aria-required="true">
                                        <label for="varyant_stok_'.$urunekozellik_id.'">STOK</label>
                                    </div>
                                </div>
                                 <div class="col-sm-2">
                                    <div class="form-group varyant_satisfiyat_div">
                                        <input type="text" data-rule-number="true"
                                            name="urunsatisfiyat[]" id="varyant_satisfiyat_'.$urunekozellik_id.'" value="'.$productSalePrice.'"
                                            autocomplete="off"  class="form-control text-danger"  aria-invalid="true" required aria-required="true">
                                        <label for="varyant_satisfiyat_'.$urunekozellik_id.'">Satış Fiyat</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_alisfiyat_div">
                                        <input type="text" data-rule-number="true"
                                            name="urunalisfiyat[]" id="varyant_alisfiyat_'.$urunekozellik_id.'" value="'.$productPurchasePrice.'"
                                            autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                        <label for="varyant_alisfiyat_'.$urunekozellik_id.'">Alış Fiyat</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_bayifiyat_div">
                                        <input type="text" data-rule-number="true"
                                            name="urunbayifiyat[]" id="varyant_bayifiyat_'.$urunekozellik_id.'" value="'.$productDealerPrice.'"
                                            autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                        <label for="varyant_bayifiyat_'.$urunekozellik_id.'">Bayi Fiyat</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group varyant_indirimsizfiyat_div">
                                        <input type="text" data-rule-number="true"
                                            name="urunindirimsizfiyat[]" id="varyant_indirimsizfiyat_'.$urunekozellik_id.'" value="'.$productNonDiscountedPrice.'"
                                            autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                        <label for="varyant_indirimsizfiyat_'.$urunekozellik_id.'">İndirimsiz Fiyat</label>
                                    </div>
                                </div>
                            </div>';
                    }
                }
                else
                {
                    ?>
                    <div id="varyant_satir_" class="varyant_satir border-gray hover" style="padding:5px;display:inline-block">
                        <div class="col-sm-2">
                            <div class="form-group varyant_ozellikler_div">
                                <input type="hidden"
                                       name="urunekozellikid[]" id="varyant_ozellikler_" value="">
                                <label for="varyant_ozellikler_'.$urunekozellik_id.'">Özellikler<br></label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_stokkodu_div">
                                <input type="text"
                                       name="urunstokkodu[]" id="varyant_stokkodu_" value="<?=$productStockCode?>"
                                       autocomplete="off" class="form-control text-danger" aria-invalid="false" required aria-required="true">
                                <label for="varyant_stokkodu_">Stok Kodu</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_oem_div">
                                <input type="text"
                                       name="urunoem[]" id="varyant_oem_" value=""
                                       autocomplete="off" class="form-control text-danger" aria-invalid="false">
                                <label for="varyant_oem_">OEM</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_gtin_div">
                                <input type="text"
                                       name="urungtin[]" id="varyant_gtin_" value=""
                                       autocomplete="off" class="form-control text-danger" aria-invalid="false">
                                <label for="varyant_gtin_">GTIN</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_mpn_div">
                                <input type="text"
                                       name="urunmpn[]" id="varyant_mpn_" value=""
                                       autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                <label for="[varyant_mpn_input_id]">MPN</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_barkod_div">
                                <input type="text"
                                       name="urunbarkod[]" id="varyant_barkod_" value=""
                                       autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                <label for="[varyant_barkod_input_id]">Barkod</label>
                            </div>
                        </div>

                        <div class="col-sm-2"></div>

                        <div class="col-sm-2">
                            <div class="form-group varyant_stok_div">
                                <input type="text" data-rule-digits="true"
                                       name="urunstok[]" id="varyant_stok_" value="<?=$productStock?>"
                                       autocomplete="off"  class="form-control text-danger"  aria-invalid="true" required aria-required="true">
                                <label for="varyant_stok_">STOK</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_satisfiyat_div">
                                <input type="text" data-rule-number="true"
                                       name="urunsatisfiyat[]" id="varyant_satisfiyat_" value="<?=$productSalePrice?>"
                                       autocomplete="off"  class="form-control text-danger"  aria-invalid="true" required aria-required="true">
                                <label for="[varyant_satisfiyat_input_id]">Satış Fiyat</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_alisfiyat_div">
                                <input type="text" data-rule-number="true"
                                       name="urunalisfiyat[]" id="varyant_alisfiyat_" value="<?=$productPurchasePrice?>"
                                       autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                <label for="varyant_alisfiyat_">Alış Fiyat</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_bayifiyat_div">
                                <input type="text" data-rule-number="true"
                                       name="urunbayifiyat[]" id="varyant_bayifiyat_" value="<?=$productDealerPrice?>"
                                       autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                <label for="varyant_bayifiyat_">Bayi Fiyat</label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group varyant_indirimsizfiyat_div">
                                <input type="text" data-rule-number="true"
                                       name="urunindirimsizfiyat[]" id="varyant_indirimsizfiyat_" value="<?=$productNonDiscountedPrice?>"
                                       autocomplete="off"  class="form-control text-danger"  aria-invalid="false">
                                <label for="varyant_indirimsizfiyat_">İndirimsiz Fiyat</label>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
<style>.varyant_ozellikler_div,.varyant_ozellikler_div label{color:#242424;opacity: 1}</style>
