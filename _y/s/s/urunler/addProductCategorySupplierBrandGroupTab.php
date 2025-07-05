<?php
/**
 * @var $productID
 * @var $productLanguageID
 * @var $languageID
 * @var $languages
 * @var $productCategoryID
 * @var $categories
 * @var $productSupplierID
 * @var $suppliers
 * @var $productBrandID
 * @var $brands
 * @var $productGroupID
 * @var $productGroups
 * @var $productActive
 * @var $productModel
 * @var Helper $helper
 * @var Config $config
 */
?>
<div class="form-group">
    <select name="languageID" id="languageID" class="form-control">
        <option value="0">Dil Seçin</option>
        <?php foreach($languages as $language){
            $selected = $language['languageID'] == $productLanguageID ? 'selected' : '';
            ?>
            <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
        <?php } ?>
    </select>
    <p class="help-block">KATEGORİ LİSTELEME İÇİN DİL SEÇİN!</p>
</div>
<div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
    <div class="col-lg-12">
        <h4>Kategori - Marka - Tedarikçi - Ürün Model</h4>
    </div>
	<div class="col-lg-3 col-md-4">
		<article class="margin-bottom-xxl">
			<p>
				Gireceğiniz ürün bir Kategoriye ait olmalıdır. (örn: Ürünler/Cep Telefonu)
			</p>
			<p>
				Bir Marka seçmelisiniz. Lütfen Marka seçin! (örn: Samsung, Apple)
			</p>
			<p>
				Gruba tanımladığınız bilgiler (kdv, açıklama, parabirimi) otomatik alınır ve girişleri kolaylaştırır. (ayakkabılar, usb bellekler) <a href="/_y/s/s/gruplar/grupekle.php" class="primary">Buradan grup tanımlayabilirsiniz</a>
			</p>
			<p>Yayın alanından Pasif konumunu seçerseniz ürün kaydedilir fakat siz aktif edene kadar görüntülenmez.</p>
		</article>
	</div>
	<div class="col-lg-offset-1 col-md-8">
		<div class="card">
			<div class="card-body">
                <div class="row" id="categoryContainer">
                    <input type="hidden" name="productCategoryID" id="productCategoryID" value="<?=$productCategoryID?>">
                    <div id="categoryList0" class="categoryList col-sm-6 form-group floating-label">
                        <select data-layer="0" class="col-sm-12 form-control">

                        </select>
                        <p class="help-block">Kategori Seçin</p>
                    </div>
                </div>
				<div class="row">
                    <!-- TEDARİKÇİ -->
                    <div class="col-sm-4">
                        <div class="form-group">
                            <select id="productSupplierID" name="productSupplierID" class="form-control">
                            <?php
                            if(!empty($suppliers)){
                                foreach($suppliers as $supplier){
                                    $supplier['supplierTitle'] = $helper->decrypt($supplier['supplierTitle'], $config->key);
                                    $selected = $supplier['supplierID'] == $productSupplierID ? 'selected' : '';
                                    echo '<option value="'.$supplier['supplierID'].'" '.$selected.'>'.$supplier['supplierTitle'].'</option>';
                                }
                            }
                            else{
                                echo '<option value="0">Tedarikçi Yok</option>';
                            }
                            ?>
                        </select>
                            <label for="productSupplierID">Tedarikçi Seçin</label>
                        </div>
                    </div>
					<!-- MARKA -->
					<div class="col-sm-4">
                        <div class="form-group">
                            <select id="productBrandID" name="productBrandID" class="form-control">
                                <?php
                                if(!empty($brands)){
                                    foreach($brands as $brand){
                                        $selected = $brand['brandID'] == $productBrandID ? 'selected' : '';
                                        echo '<option value="'.$brand['brandID'].'" '.$selected.'>'.$brand['brandName'].'</option>';
                                    }
                                }
                                else{
                                    echo '<option value="0">Marka Yok</option>';
                                }
                                ?>
                            </select>
                            <label for="productBrandID">Marka Seçin</label>
                        </div>
					</div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" class="form-control" name="productModel" id="productModel" required aria-required="true" value="<?=$productModel?>" />
                            <label for="productModel">Ürün Model</label>
                        </div>
                        <div id="modelSearchResult" class="card divider-full-bleed hidden" style="position: absolute;z-index: 2;left:0;top:55px;width: 100%"></div>
                    </div>
				</div>
				<div class="row">

                    <div class="col-sm-2"><div class="form-group">Yayınlansın mı</div></div>
                    <div class="col-sm-4">
                        <div style="padding-top: 10px">
                            <label class="radio-inline radio-styled">
                                <input type="radio" name="productActive" value="1" <?= $productActive==1 ? "checked" : '';?>><span>Aktif</span>
                            </label>
                            <label class="radio-inline radio-styled">
                                <input type="radio" name="productActive" value="0" <?= $productActive=0 ? "checked" : '';?>><span>Pasif</span>
                            </label>
                        </div>
                    </div>
                    <!-- GRUP -->
                    <div class="col-sm-4">
                        <select id="productGroupID" name="productGroupID" class="form-control">
                            <option value="0">Ürün grubu Seçin</option>
                            <?php
                            if(!empty($productGroups)){
                                foreach($productGroups as $productGroup){
                                    $selected = $productGroup['productGroupID'] == $productGroupID ? 'selected' : '';
                                    echo '<option value="'.$productGroup['productGroupID'].'" '.$selected.'>'.$productGroup['productGroupName'].'</option>';
                                }
                            }
                            ?>
                        </select>
                        <label for="productGroupID">Ürün Grubu Seçin</label>
                    </div>

				</div>
			</div>
		</div>
		<em class="text-caption">Temel özellikleri seçin</em>
	</div>
</div>