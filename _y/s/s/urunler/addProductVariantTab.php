<?php
/**
* @var array $variantGroups
 * @var array $productVariantProperties
 * @var string $productStockCode
 * @var string $productGTIN
 * @var string $productMPN
 * @var string $productBarcode
 * @var string $productOEM
 * @var string $productStock
 * @var string $productSalePrice
 * @var string $productNonDiscountedPrice
 * @var string $productDealerPrice
 * @var string $productPurchasePrice
 * @var AdminProductVariant $variantModel
 */
?>
<div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
    <div class="col-lg-12">
        <h4>ÜRÜN SEÇENEKLERİ - RENK - BEDEN - MALZEME - ŞEKİL - ÖLÇÜ vb. gibi seçenekler ekleyebilirsiniz</h4>
    </div>
    <div class="col-md-12 card">
        <div class="col-md-6 card-body">
            <div class="checkbox checkbox-styled">
                <label>
                    <input name="isVariant" id="isVariant" class="isVariant" type="checkbox" value="1" <?php if(!empty($productVariantProperties)) echo 'checked'?>>
                    <span>Farklı Seçenekler Ekle</span>
                </label>
            </div>
        </div>
        <div class="col-md-6 card-body">
            <a href="#offcanvas-variant" data-toggle="offcanvas" class="btn btn-primary ink-reaction pull-right" id="showVariantGroup">Seçenekleri Göster</a>
        </div>
    </div>
</div>

<div class="row variantContainerContainer">
    <div class="col-lg-12 col-md-12 margin-bottom-lg">
        <div class="card">
            <div id="variantContainer" class="ui-sortable card-body" data-sortable="true">
                <?php
                    //$productVariantProperties = Array
                //(
                //    [0] => Array
                //        (
                //            [variantID] => 27219
                //            [variantName] => 40X8X55 SABİT PLASTİK PABUÇ
                //            [variantCurrencyID] => 3
                //            [variantCurrencyCode] => EUR
                //            [variantCurrencySymbol] => €
                //            [variantSellingPrice] => 1.38
                //            [variantPriceWithoutDiscount] => 1.73
                //            [variantSellerPrice] => 0.00
                //            [variantDiscountRate] => 20
                //            [variantQuantity] => 999
                //            [variantStockCode] => 400.400.855
                //            [variantMinQuantity] => 1.0000
                //            [variantCoefficient] => 1.0000
                //            [variantProperties] => Array
                //                (
                //                    [0] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Malzeme
                //                                    [value] => Güçlendirilmiş Polyamid (PA) + Çinko Kaplama (ZN)
                //                                )
                //
                //                        )
                //
                //                    [1] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Renk
                //                                    [value] => Siyah+Metal
                //                                )
                //
                //                        )
                //
                //                    [2] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Ölçü
                //                                    [value] => 40X8X55
                //                                )
                //
                //                        )
                //
                //                )
                //
                //        )
                //
                //    [1] => Array
                //        (
                //            [variantID] => 27220
                //            [variantName] => 50X8X55 SABİT PLASTİK PABUÇ
                //            [variantCurrencyID] => 3
                //            [variantCurrencyCode] => EUR
                //            [variantCurrencySymbol] => €
                //            [variantSellingPrice] => 1.59
                //            [variantPriceWithoutDiscount] => 1.98
                //            [variantSellerPrice] => 0.00
                //            [variantDiscountRate] => 20
                //            [variantQuantity] => 979
                //            [variantStockCode] => 400.500.855
                //            [variantMinQuantity] => 1.0000
                //            [variantCoefficient] => 1.0000
                //            [variantProperties] => Array
                //                (
                //                    [0] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Malzeme
                //                                    [value] => Güçlendirilmiş Polyamid (PA) + Çinko Kaplama (ZN)
                //                                )
                //
                //                        )
                //
                //                    [1] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Renk
                //                                    [value] => Siyah+Metal
                //                                )
                //
                //                        )
                //
                //                    [2] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Ölçü
                //                                    [value] => 50X8X55
                //                                )
                //
                //                        )
                //
                //                )
                //
                //        )
                //
                //    [2] => Array
                //        (
                //            [variantID] => 27221
                //            [variantName] => 50X10X55 SABİT PLASTİK PABUÇ
                //            [variantCurrencyID] => 3
                //            [variantCurrencyCode] => EUR
                //            [variantCurrencySymbol] => €
                //            [variantSellingPrice] => 1.79
                //            [variantPriceWithoutDiscount] => 2.24
                //            [variantSellerPrice] => 0.00
                //            [variantDiscountRate] => 20
                //            [variantQuantity] => 991
                //            [variantStockCode] => 400.501.055
                //            [variantMinQuantity] => 1.0000
                //            [variantCoefficient] => 1.0000
                //            [variantProperties] => Array
                //                (
                //                    [0] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Malzeme
                //                                    [value] => Güçlendirilmiş Polyamid (PA) + Çinko Kaplama (ZN)
                //                                )
                //
                //                        )
                //
                //                    [1] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Renk
                //                                    [value] => Siyah+Metal
                //                                )
                //
                //                        )
                //
                //                    [2] => Array
                //                        (
                //                            [attribute] => Array
                //                                (
                //                                    [name] => Ölçü
                //                                    [value] => 50X10X55
                //                                )
                //
                //                        )
                //
                //                )
                //
                //        )
                //
                //)
                if(!empty($productVariantProperties)){
                    foreach ($productVariantProperties as $variant) {
                        $variantID = $variant['variantID'];
                        $variantName = $variant['variantName'];
                        $variantStockCode = $variant['variantStockCode'];
                        $variantQuantity = $variant['variantQuantity'];
                        $variantSellingPrice = $variant['variantSellingPrice'];
                        $variantPriceWithoutDiscount = $variant['variantPriceWithoutDiscount'];
                        $variantSellerPrice = $variant['variantSellerPrice'];
                        $variantPurchasePrice = $variant['variantPurchasePrice'] ?? 0;

                        $properties = '';$hiddenInput = '';

                        foreach ($variant['variantProperties'] as $property) {

                            $variantAttributeID = $property['attribute']['id'];

                            $properties .= $property['attribute']['name'] . ': ' . $property['attribute']['value'] . ' | ';
                            $hiddenInput .= '<input type="hidden" id="'.$variantAttributeID.'" name="variantProperties['.$variantID.']" value="'.$property['attribute']['name'].'|'.$property['attribute']['value'].'">';
                        }

                        $properties = rtrim($properties, ' | ');
                        $hiddenInput .= '<input type="hidden" name="variantID[]" value="'.$variantID.'">';

                        echo <<<HTML
                        <div class="row" id="variant-$variantID" style="background-color:white">
                            $hiddenInput
                           <div class="getVariantGroupName col-sm-8 form-group text-bold text-primary">$properties</div>
                           <div class="col-sm-4 form-group text-right">
                               <a class="btn btn-floating-action ink-reaction dragDropVariant" data-variantid="$variantID" title="Sırala">
                                   <i class="fa fa-arrows"></i>
                               </a>
                               <a class="btn btn-floating-action ink-reaction removeVariant" data-variantid="$variantID" title="Kaldır">
                                   <i class="fa fa-trash"></i>
                               </a>
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="StockCode-$variantID">Stok Kodu</label>
                               <input type="text" name="productStockCode[]" id="StockCode-$variantID" class="form-control" required="" value="$variantStockCode">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="GTIN-$variantID">GTIN</label>
                               <input type="text" name="productGTIN[]" id="GTIN-$variantID" class="form-control" required="" value="">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="MPN-$variantID">MPN</label>
                               <input type="text" name="productMPN[]" id="MPN-$variantID" class="form-control" required="" value="">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="Barcode-$variantID">Barkod</label>
                               <input type="text" name="productBarcode[]" id="Barcode-$variantID" class="form-control" required="" value="">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="OEM-$variantID">OEM</label>
                               <input type="text" name="productOEM[]" id="OEM-$variantID" class="form-control" required="" value="">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="Stock-$variantID">Stok Adeti</label>
                               <input type="text" name="productStock[]" id="Stock-$variantID" class="form-control" required="" value="$variantQuantity">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="SalePrice-$variantID">Satış Fiyatı</label>
                               <input type="text" name="productSalePrice[]" id="SalePrice-$variantID" class="form-control" required="" value="$variantSellingPrice">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="DiscountPrice-$variantID">İndirimsiz Satış Fiyatı</label>
                               <input type="text" name="productDiscountPrice[]" id="DiscountPrice-$variantID" class="form-control" required="" value="$variantPriceWithoutDiscount">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="DealerPrice-$variantID">Bayi Fiyatı</label>
                               <input type="text" name="productDealerPrice[]" id="DealerPrice-$variantID" class="form-control" required="" value="$variantSellerPrice">
                           </div>
                           <div class="col-sm-2 form-group">
                               <label for="PurchasePrice-$variantID">Alış Fiyatı</label>
                               <input type="text" name="productPurchasePrice[]" id="PurchasePrice-$variantID" class="form-control" required="" value="$variantPurchasePrice">
                           </div>
                        </div>
                        HTML;
                    }
                }
                else{
                    echo <<<HTML
                    <div class="row" id="variant-no-variant">
                        <div class="getVariantGroupName col-sm-8 form-group text-bold text-primary"></div>
                        <div class="col-sm-4 form-group text-right">
                            <a class="btn btn-floating-action ink-reaction dragDropVariant" data-variantid="no-variant" title="Sırala">
                                <i class="fa fa-arrows"></i>
                            </a>
                            <a class="btn btn-floating-action ink-reaction removeVariant" data-variantid="no-variant" title="Kaldır">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="StockCode-no-variant">Stok Kodu</label>
                            <input type="text" name="productStockCode[]" id="StockCode-no-variant" class="form-control" required="" value="$productStockCode">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="GTIN-no-variant">GTIN</label>
                            <input type="text" name="productGTIN[]" id="GTIN-no-variant" class="form-control" required="" value="$productGTIN">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="MPN-no-variant">MPN</label>
                            <input type="text" name="productMPN[]" id="MPN-no-variant" class="form-control" required="" value="$productMPN">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="Barcode-no-variant">Barkod</label>
                            <input type="text" name="productBarcode[]" id="Barcode-no-variant" class="form-control" required="" value="$productBarcode">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="OEM-no-variant">OEM</label>
                            <input type="text" name="productOEM[]" id="OEM-no-variant" class="form-control" required="" value="$productOEM">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="Stock-no-variant">Stok Adeti</label>
                            <input type="text" name="productStock[]" id="Stock-no-variant" class="form-control" required="" value="$productStock">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="SalePrice-no-variant">Satış Fiyatı</label>
                            <input type="text" name="productSalePrice[]" id="SalePrice-no-variant" class="form-control" required="" value="$productSalePrice">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="DiscountPrice-no-variant">İndirimsiz Satış Fiyatı</label>
                            <input type="text" name="productDiscountPrice[]" id="DiscountPrice-no-variant" class="form-control" required="" value="$productNonDiscountedPrice">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="DealerPrice-no-variant">Bayi Fiyatı</label>
                            <input type="text" name="productDealerPrice[]" id="DealerPrice-no-variant" class="form-control" required="" value="$productDealerPrice">
                        </div>
                        <div class="col-sm-2 form-group">
                            <label for="PurchasePrice-no-variant">Alış Fiyatı</label>
                            <input type="text" name="productPurchasePrice[]" id="PurchasePrice-no-variant" class="form-control" required="" value="$productPurchasePrice">
                        </div>
                        <input type="hidden" name="variantID[]" value="no-variant">
                        <input type="hidden" name="variantProperties[no-variant]" value="">
                    </div>
                    HTML;
                }
                ?>
            </div>
        </div>
    </div>
</div>
