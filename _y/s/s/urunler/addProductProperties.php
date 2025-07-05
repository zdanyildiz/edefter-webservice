<!-- Ek özellikleri -->
<div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
    <div class="col-lg-12"><h4>EK ÖZELLİKLERİ</h4><p>Varyantlar dışında da ürünlerinize ek özellikler ekleyebilirsiniz. Örneğin ürününüzün kumaş cinsini, boyutlarını ya da şeklini öne çıkarmak isteyebilirsiniz.</p></div>
    <div class="col-lg-3 col-md-4">
        <article class="margin-bottom-xxl">

            <p>Her satıra bir özellik gelmelidir</p>
            <p>Özellik değerlerini "<strong>:</strong>" ile ayırın</p>

            <p style="font-weight: bold">Boyut : 120*22*70<br>
                Şekli : Oval<br>
                Malzeme : Deri<br>
                Kaplama : Plastik
                ... gibi</p>
        </article>
    </div>
    <div class="col-lg-offset-1 col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-content" style="position:relative">
                                    <input type="text" id="addProductProperties" class="form-control" placeholder="Malzeme:Deri">
                                    <label for="addProductProperties">Özellik adı:değeri yazın</label>
                                    <ul class="hidden list divider-full-bleed" id="productPropertiesResults" style="position:absolute;width:100%;z-index:9;display:inline-block;left:0;top:50px;background-color:#f9f9f9;overflow-x: hidden"></ul>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-primary addProductPropertiesButton" type="button" ><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" style="margin-top:20px">
                        <ul id="addedProperties" data-sortable="true" class="list divider-full-bleed">
                            <?php
                            if(!empty($productProperties)){
                                foreach ($productProperties as $productProperty){
                                    $attributeID = $productProperty['attribute']['id'] ?? rand(10000,99999);
                                    ?>
                                    <li class="tile" id="property_<?=$attributeID?>">
                                        <input type="hidden" name="productProperties[]" class="getProductProperties" value="<?=$productProperty['attribute']['name']?>:<?=$productProperty['attribute']['value']?>">
                                        <div class="col-sm-8">
                                            <div class="tile-text"><?=$productProperty['attribute']['name']?>:<?=$productProperty['attribute']['value']?></div>
                                        </div>
                                        <div class="col-sm-4">
                                            <a class="tile-content ink-reaction dragDropProperty" style="cursor:grab">
                                                <div class="tile-icon"><i class="fa fa-arrows"></i></div>
                                            </a>
                                            <a class="tile-content ink-reaction removeProperty" style="cursor: pointer" data-id="<?=$attributeID?>">
                                                <div class="tile-icon"><i class="fa fa-trash text-danger"></i></div>
                                            </a>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <em class="text-caption">Ek özellikler</em>
    </div>
</div>
