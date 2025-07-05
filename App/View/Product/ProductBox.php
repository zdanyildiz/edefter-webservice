<?php
/**
 * @var array $product
 * @var int $configPriceUnit
 * @var int $configShowDiscount
 * @var int $configShowPrice
 * @var Helper $helper
 * @var int $usdToTry
 * @var int $eurToTry
 * @var string $imgRoot
 * @var string $ampPrefix
 * @var string $ampLayout
 * @var string $ampImgEnd
 * @var int $pb
 */
global $helper;
//print_r($product);exit();
$product = $product[0] ?? [];
if(empty($product)){
    return '';
}
$productName = $product['sayfaad'];
$productLink = $product['link'];
$productImages = $product['resim_url'];
$productSalesPrice = $product['urunsatisfiyat'];
$productWithoutDiscountPrice = $product['urunindirimsizfiyat'];
$productCurrencyID = $product['urunparabirim'];
$productCurrencySymbol = $product['parabirimsimge'];
$productCurrencyCode = $product['parabirimkod'];
$productCategoryName = $product['kategoriad'];
$productCategoryLink = $product['kategorilink'];
// ürün aynı gün cargo
$productSameDayCargo = $product['urunanindakargo'];
// ücretsiz kargo
$productFreeCargo = $product['urunucretsizkargo'];
// indirim
$productDiscount = $product['urunindirimde'];
// yeni ürün
$productNew = $product['urunyeni'];

if($productCurrencyID!=$configPriceUnit && $configShowPrice==1){
    switch ($configPriceUnit) {
        case 1:
            //genel para birimi ayarı 1 tl, 2 usd, 3 euro
            //ürün para birimi farklı ise $usdToTry,$eurToTry değerleri ile dönüştürme yapacağız
            if ($productCurrencyID == 2) {
                $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * $usdToTry :"0.00";
                $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * $usdToTry : "0.00";
            } elseif ($productCurrencyID == 3) {
                $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * $eurToTry : "0.00";
                $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * $eurToTry : "0.00";
            }
            $productCurrencySymbol = "₺";
            $productCurrencyID = 1;
            break;
        case 2:
            if ($productCurrencyID == 1) {
                $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice / $usdToTry : "0.00";
                $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice / $usdToTry : "0.00";
            } elseif ($productCurrencyID == 3) {
                $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * ($eurToTry / $usdToTry) : "0.00";
                $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * ($eurToTry / $usdToTry) : "0.00";
            }
            $productCurrencySymbol = "$";
            $productCurrencyID = 2;
            break;
        case 3:
            if ($productCurrencyID == 1) {
                $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice / $eurToTry : "0.00";
                $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice / $eurToTry : "0.00";
            } elseif ($productCurrencyID == 2) {
                $productWithoutDiscountPrice = ($productWithoutDiscountPrice!="0.00") ? $productWithoutDiscountPrice * ($usdToTry / $eurToTry) : "0.00";
                $productSalesPrice = ($productSalesPrice!="0.00") ? $productSalesPrice * ($usdToTry / $eurToTry) : "0.00";
            }
            $productCurrencySymbol = "€";
            $productCurrencyID = 3;
            break;
    }
}
$productSalesPrice = $helper->formatCurrency($productSalesPrice);
$productWithoutDiscountPrice= $helper->formatCurrency($productWithoutDiscountPrice);
//her iki üründe bir aşağıdan başlasın
/**
 * @var int $i
 */
$pb++;
if($pb==3){
    $pb=1;
}
$productNewLineClass = ($pb==1) ? "none" : "block";

?>
<div class="product-box <?=$productNewLineClass?>">
    <a href="<?php echo $productLink ?>">
        <figure class="product-image-container">
            <?php
            if(!empty($productImages)){
                foreach (explode(",",$productImages) as $i => $productImage):
                    $displayProperty = ($i==0) ? "block" : "none";
                    ?>
                    <<?=$ampPrefix?>img height="330" width="330" class="product-image <?=$displayProperty?>" src="<?php echo imgRoot."?imagePath=".trim($productImage) ?>&width=330" alt="<?php echo $productName ?>" <?=$ampLayout?>><?=$ampImgEnd?>
                <?php if($i==1) break;
                endforeach; ?>

                <div class="thumbnail-container">
                    <?php
                    foreach (explode(",",$productImages) as $i => $productImage):
                        ?>
                        <<?=$ampPrefix?>img width="50" height="50" class="thumbnail" src="<?php echo imgRoot."?imagePath=".trim($productImage)."&width=100&height=100" ?>" data-src="<?php echo imgRoot."?imagePath=".trim($productImage)."&width=330&height=300" ?>" alt="<?php echo $productName ?>" <?=$ampLayout?>><?=$ampImgEnd?>
                    <?php if($i==4) break;
                    endforeach; ?>
                </div>
            <?php   }?>
            <figcaption class="product-image-overlay">
                <h3 class="product-name"><?php echo $productName .$pb?></h3>
                <?php if($productDiscount==1){ ?>
                <span class="product-image-overlay-text discount"><?=_urun_kutu_etiket_indirim_yazi?></span>
                <?php }?>
                <?php if($productNew==1){ ?>
                <span class="product-image-overlay-text new"><?=_urun_kutu_etiket_yeni_yazi?></span>
                <?php }?>
                <?php if($productSameDayCargo==1){ ?>
                <span class="product-image-overlay-text sameDayCargo"><?=_urun_kutu_etiket_ayni_gun_kargo?></span>
                <?php }?>
                <?php if($productFreeCargo==1){ ?>
                <span class="product-image-overlay-text freeCargo"><?=_urun_kutu_etiket_ucretsiz_kargo_yazi?></span>
                <?php }?>
            </figcaption>
        </figure>
        <h3 class="product-name"><?php echo $productName ?></h3>
        <p class="product-price-container">
            <?php echo ($productWithoutDiscountPrice!="0,00" && $productWithoutDiscountPrice!="0.00" && $configShowDiscount==1 && $configShowPrice==1) ? "<span>".$productWithoutDiscountPrice." ".$productCurrencySymbol."</span>" : ""; ?>
            <?php echo ($configShowPrice == 1)
                ? ($productSalesPrice != "0.00" && $productSalesPrice != "0,00"
                    ? $productSalesPrice . " " . $productCurrencySymbol
                    : _urun_kutu_fiyat_sor_yazi)
                : ""; ?>
        </p>
    </a>
    <div class="product-category">
        <a href="<?php echo $productCategoryLink ?>">
            <?php echo $productCategoryName ?>
        </a>
    </div>
</div>
