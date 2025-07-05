<?php

/**
 * @var Session $session
 * @var array $productDetails
 * @var array $page
 * @var Casper $casper
 * @var Helper $helper
 * @var string $pageTitle
 * @var string $pageContent
 * @var string $categories
 * @var string $imageUrls
 * @var string $ampPrefix
 * @var string $ampLayout
 * @var string $ampImgEnd
*/


$routerResult = $session->getSession("routerResult");
$config = $casper->getConfig();
$helper = $config->Helper;
$http = $config->http;
$hostDomain = $config->hostDomain;

$siteConfig = $casper->getSiteConfig();

$companySettings = $siteConfig["companySettings"];
$companyCountryCode = $companySettings['ayarfirmaulkekod'];
$companyGsm = $companySettings['ayarfirmagsm'];

$relatedProduct = $page["relatedProduct"];
$categoryProducts = $page["categoryProducts"];

$pageId = $page["sayfaid"];
$uniqueId = $page["benzersizid"];
$pageCreationDate = $page["sayfatariholustur"];
$pageUpdateDate = $page["sayfatarihguncel"];
$pageType = $page["sayfatip"];
$pageTitle = $page["sayfaad"];
$pageContent = $page["sayfaicerik"];
$pageLink = $page["sayfalink"];
$pageOrder = $page["sayfasira"];
$pageActive = $page["sayfaaktif"];
$pageDeleted = $page["sayfasil"];
$pageHit = $page["sayfahit"];
$categories = $page["kategoriler"];

$pageGallery = $page['pageGallery'] ?? [];
$pageFiles = $page['pageFiles'] ?? [];
$pageVideos = $page['pageVideos'] ?? [];

$imageUrls = $page["resim_url"] ?? "";
$imageUrls = ($imageUrls) ? explode(",",$imageUrls) : [];

$pageIsFavorite = $page["favorites"];

$productDetails = $page["productDetails"];
//print_r($productDetails);exit;
$productID = $productDetails[0]['sayfaid'];
$brandID = $productDetails[0]['markaid'];
$brandName = $productDetails[0]['markaad'];
$productSubTitle = $productDetails[0]['urunaltbaslik'];
$productDescription = $productDetails[0]['urunaciklama'];
$productGift = $productDetails[0]['urunhediye'];
$productShippingTime = $productDetails[0]['urunkargosuresi'];
$productFixedShippingCost = $productDetails[0]['urunsabitkargoucreti'];
$productSellingPrice = $productDetails[0]['urunsatisfiyat'];
$productSellingPriceVal = $productSellingPrice;
//$productSellingPriceVal = str_replace(".","",$productSellingPrice);
//$productSellingPriceVal = str_replace(",",".",$productSellingPriceVal);

$productPriceWithoutDiscount = $productDetails[0]['urunindirimsizfiyat'];
$productSellerPrice = $productDetails[0]['urunbayifiyat'];
$productPurchasePrice = $productDetails[0]['urunalisfiyat'];
$productMarketPrice = $productDetails[0]['urunpazaryerifiyat'];
$productShowDiscount = $productDetails[0]['uruneskifiyatgoster'];
$productInstallment = $productDetails[0]['uruntaksit'];
$productKDV = $productDetails[0]['urunkdv'];
$productStock = $productDetails[0]['urunstok'];
$productStockCode = $productDetails[0]['urunstokkodu'];
$productModel = $productDetails[0]['urunmodel'];
$productDiscountRate = $productDetails[0]['urunindirimorani'];
$productPriceLastDate = $productDetails[0]['urunfiyatsontarih'];
$productOnHomePage = $productDetails[0]['urunanasayfa'];
$productDiscounted = $productDetails[0]['urunindirimde'];
$productNew = $productDetails[0]['urunyeni'];
$productBulkDiscount = $productDetails[0]['uruntopluindirim'];
$productDiscountedShipping = $productDetails[0]['urunanindakargo'];
$productFreeShipping = $productDetails[0]['urunucretsizkargo'];
$productPreOrder = $productDetails[0]['urunonsiparis'];
$productPriceAsk = $productDetails[0]['urunfiyatsor'];
$productShipping = $productDetails[0]['urunkargo'];
$productCurrencyID = $productDetails[0]['urunparabirim'];
$productCurrencySymbol = $productDetails[0]['parabirimsimge'];
$productCurrencyCode = $productDetails[0]['parabirimkod'];
$productGTIN = $productDetails[0]['urungtin'];
$productMPN = $productDetails[0]['urunmpn'];
$productBarcode = $productDetails[0]['urunbarkod'];
$productOEM = $productDetails[0]['urunoem'];
$productDesi = $productDetails[0]['urundesi'];

$productMinimumQuantity = $productDetails[0]['urunminimummiktar'];
$productMinimumQuantity = str_replace(".0000","",$productMinimumQuantity);
$productMaximumQuantity = $productDetails[0]['urunmaksimummiktar'];
$productMaximumQuantity = str_replace(".0000","",$productMaximumQuantity);
$productCoefficient = $productDetails[0]['urunkatsayi'];
$productCoefficient = str_replace(".0000","",$productCoefficient);

$productQuantityUnitID = $productDetails[0]['urunmiktarbirimid'];
$productQuantityUnitName = $productDetails[0]['urunmiktarbirimadi'];
$productVariantProperties = $productDetails[0]['variantProperties'];

$productProperties = $productDetails[0]['product_properties'] ?? [];//[product_properties] => [{"attribute":{"name":"Gramaj","value":"300Gr"}},{"attribute":{"name":"Yaldız","value":"Var"}}]

$configPriceSettings = $casper->getSiteConfig()["priceSettings"][0];
$configShowDiscount = $configPriceSettings["eskifiyat"];
$configShowPrice = $configPriceSettings["fiyatgoster"];
$configPriceUnit = $configPriceSettings["parabirim"];

$currencyRates = $casper->getSiteConfig()["currencyRates"];


$usdToTry = $currencyRates["usd"];
$eurToTry = $currencyRates["euro"];

$productCampaign=$page['campaign'];
?>

<div class="productContainer">
    <div class="product-images">
        <div class="product-image">
            <figure>
                <?php if(!empty($imageUrls)): ?>
                <<?=$ampPrefix?>img width="100%" height="500"  src="<?php echo imgRoot."?imagePath=".trim($imageUrls[0])?>&width=825&height=620" alt="<?php echo $pageTitle ?>" <?=$ampLayout?>><?=$ampImgEnd?>
                <?php endif; ?>
            </figure>
        </div>
        <div class="product-images-thumbs">
            <?php
            if(!empty($imageUrls)):
                foreach($imageUrls as $imageUrl){
                    ?>
                    <div class="product-image-thumb">
                        <figure>
                            <<?=$ampPrefix?>img width="100" height="100" class="thumbnail" src="<?php echo imgRoot."?imagePath=".trim($imageUrl)."&width=100&height=100" ?>" data-src="<?php echo imgRoot."?imagePath=".trim($imageUrl)."&width=825&height=620";?>" alt="<?php echo $pageTitle ?>" <?=$ampLayout?>><?=$ampImgEnd?>
                        </figure>
                    </div>
                    <?php
                }
            endif;
            ?>
        </div>
    </div>
    <div class="product-details">
        <form action="/?/control/cart/post/add" method="post" id="productDetailForm">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="productID" value="<?php echo $productID; ?>">
            <input type="hidden" name="productPriceInput" id="productPriceInput" value="<?=$productSellingPriceVal?>">
            <input type="hidden" name="productStockCodeInput" id="productStockCodeInput" value="<?=$productStockCode?>">
            <h1 class="product-title"><?php echo $pageTitle; ?></h1>
            <?php if($configShowPrice==1): ?>
            <div class="product-price">
                <span class="price <?php
                    if($productSellingPrice=="0,00") echo'ask-price';?>" id="productSellingPrice"><?php
                    if($productSellingPrice!="0,00" && $productSellingPrice!="0.00"){ echo $productCurrencySymbol ." ". $helper->formatCurrency($productSellingPrice);}else{echo _urun_detay_urun_fiyat_sor;} ?></span>
                <span class="price-discount" id="productWithoutDiscountPrice"><?php
                    if($productShowDiscount==1&&$productPriceWithoutDiscount!="0,00"&&$productPriceWithoutDiscount!="0.00"){ ?>
                        <?php echo $productCurrencySymbol; ?> <?php echo $helper->formatCurrency($productPriceWithoutDiscount); ?>
                    <?php } ?>
                </span>
            </div>
            <?php endif; ?>
            <div class="product-description">
                <p id="product-subtitle"><?php echo $productDescription; ?></p>
                <p><?php echo $productGift; ?></p>
            </div>
            <div class="product-attributes">
                <div class="product-attribute">
                    <span class="attribute-name"><?=_urun_detay_urun_kategori_yazi?> :</span>
                    <span class="attribute-value"><?php echo $categories; ?></span>
                </div>
                <?php if(!empty($brandName)):?>
                <div class="product-attribute">
                    <span class="attribute-name"><?php echo _urun_detay_urun_marka_yazi; ?> :</span>
                    <span class="attribute-value"><?php echo $brandName; ?></span>
                </div>
                <?php endif; ?>
                <div class="product-attribute">
                    <span class="attribute-name"><?=_urun_detay_urun_model_yazi?> :</span>
                    <span class="attribute-value"><?php echo $productModel; ?></span>
                </div>
                <div class="product-attribute">
                    <span class="attribute-name"><?=_urun_detay_urun_stok_kod_yazi?> :</span>
                    <span class="attribute-value" id="productStockCode"><?php echo $productStockCode; ?></span>
                </div>
            </div>
            <div id="productVariantSelect"></div>
            <?php if($configShowPrice==1): ?>
            <div class="product-quantity">
                <label for="product-quantity"><?=_urun_detay_urun_miktar_yazi?>:</label>
                <input class="product-quantity-decrement-button" type="button" value="-">
                <input type="text" id="product-quantity" name="product-quantity" value="<?=$productMinimumQuantity?>" data-min="<?=$productMinimumQuantity?>" data-max="<?=$productMaximumQuantity?>">
                <input class="product-quantity-increment-button" type="button" value="+">
                <span class="unit-quantity-name"><?=$productQuantityUnitName?></span>
            </div>
            <div class="product-total-price-container"></div>
            <div class="product-actions">
                <button id="addToCartButton" name="addToCartButton" class="btn btn-primary  <?php if($productSellingPrice=="0,00"||$productSellingPrice=="0.00") echo 'disabled';?>"><?=_urun_detay_sepete_ekle_buton?></button>
                <button id="checkoutButton" name="checkoutButton" class="btn btn-secondary <?php if($productSellingPrice=="0,00"||$productSellingPrice=="0.00"||$configShowPrice==0) echo 'disabled';?>"><?=_urun_detay_hemen_al_yazi?></button>
            </div>
            <?php endif; ?>
            <div class="product-attributes">
                <!-- div class="product-attribute">
                    <span class="attribute-name"><?=_urun_detay_kargo_bilgi_yazi?></span>
                    <span class="attribute-value"><?php echo $productShippingTime; ?> </span>
                </div>
                <div class="product-attribute">
                    <span class="attribute-name">Kargo Ücreti:</span>
                    <span class="attribute-value"><?php echo $productFixedShippingCost; ?></span>
                </div -->
                <?php if(!empty($productProperties)){
                    $productProperties = json_decode($productProperties, true);
                    echo '<div class="product-attributes">';
                    echo '<h3>'._urun_detay_urun_ozellikleri.'</h3>';
                    echo '</div>';
                    foreach ($productProperties as $attribute){
                        if($attribute){
                            echo '<div class="product-attribute">
                                <span class="attribute-name">' .$attribute['attribute']['name'].': </span>
                                <span class="attribute-value"> ' . $attribute['attribute']['value']."</span>
                            </div>";
                        }
                    }
                }?>
            </div>
            <?php if(!empty($productCampaign)): //print_r($productCampaign);exit; ?>
                <div class="product-campaign">
                    <h2 class="campaign-title"><?=$productCampaign[0]['ad']?></h2>
                    <div class="campaign-descripton"><?=$productCampaign[0]['aciklama']?></div>
                </div>
            <?php endif; ?>
            <div class="share-buttons">
                <span class="share-label"><?=_urun_detay_paylas_begen_yazi?>:</span>
                <a href="https://wa.me/?text=<?=urlencode($http.$hostDomain.$routerResult['seoLink'])?>" class="share-btn whatsapp" title="Share on WhatsApp" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="340 -40 640.000000 640.000000" preserveAspectRatio="none"><g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M6255 6844 c-540 -35 -1107 -229 -1555 -532 -473 -320 -848 -752 -1091 -1256 -133 -276 -216 -536 -273 -856 -43 -240 -52 -602 -22 -880 40 -374 177 -822 362 -1188 l53 -103 -123 -367 c-68 -202 -191 -570 -274 -818 -84 -249 -152 -459 -152 -469 0 -9 13 -22 29 -28 26 -10 29 -14 24 -45 -6 -32 -5 -34 18 -27 41 13 936 298 1314 420 198 63 368 115 378 115 9 0 52 -17 95 -39 366 -184 756 -294 1171 -332 164 -14 498 -7 659 16 954 132 1766 659 2266 1468 163 264 318 632 401 952 79 307 117 688 96 982 -54 781 -356 1473 -881 2017 -509 527 -1157 853 -1895 952 -108 14 -482 26 -600 18z m391 -684 c357 -29 650 -108 959 -259 419 -206 770 -514 1030 -906 200 -301 323 -625 371 -979 23 -168 23 -508 0 -680 -163 -1209 -1161 -2141 -2372 -2217 -427 -26 -824 44 -1212 214 -107 47 -284 143 -339 183 -17 13 -39 24 -49 24 -9 0 -222 -65 -472 -145 -250 -80 -456 -145 -457 -143 -2 2 62 197 141 433 79 237 144 442 144 458 0 16 -18 53 -44 90 -418 599 -554 1426 -351 2127 45 152 82 245 155 390 200 391 505 732 880 982 473 316 1064 472 1616 428z"/><path d="M5323 5236 c-23 -7 -56 -23 -75 -34 -51 -32 -199 -190 -245 -262 -147 -229 -180 -534 -92 -832 67 -225 149 -397 299 -629 190 -292 313 -450 510 -653 296 -305 545 -476 927 -635 282 -118 490 -185 607 -197 81 -8 258 20 362 58 144 52 309 168 373 262 64 96 130 313 138 457 l6 95 -31 36 c-22 24 -112 78 -294 176 -432 232 -487 254 -555 218 -17 -8 -81 -73 -141 -143 -178 -207 -215 -243 -245 -243 -38 0 -287 127 -403 205 -135 92 -223 166 -334 281 -132 137 -275 333 -355 486 l-18 36 72 79 c95 101 134 162 172 268 39 108 37 141 -20 290 -51 133 -92 243 -163 434 -58 157 -101 221 -161 240 -57 17 -287 22 -334 7z"/></g></svg>
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?=urlencode($http.$hostDomain.$routerResult['seoLink'])?>" class="share-btn twitter" title="Share on Twitter" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="350 350 300 250" preserveAspectRatio="none"><g transform="translate(0.000000,640.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M5195 2781 c-189 -54 -340 -197 -413 -389 -23 -59 -26 -82 -26 -201 l-1 -134 -77 6 c-185 15 -393 74 -583 167 -204 100 -360 218 -508 383 -38 43 -72 77 -77 77 -4 0 -17 -22 -29 -49 -70 -164 -69 -360 3 -516 32 -69 106 -164 163 -210 l48 -38 -52 6 c-29 3 -81 18 -115 32 -35 15 -74 29 -87 32 -24 6 -24 6 -17 -60 24 -236 195 -449 414 -517 31 -10 50 -20 43 -24 -6 -4 -61 -6 -121 -5 -60 1 -113 0 -116 -4 -10 -10 16 -73 56 -136 101 -155 257 -256 435 -279 l60 -8 -55 -36 c-209 -135 -472 -208 -707 -195 -83 5 -104 3 -114 -9 -16 -19 -20 -16 131 -93 228 -117 457 -177 715 -187 590 -25 1099 212 1447 671 219 290 357 695 358 1053 0 50 4 92 9 92 28 0 293 281 279 295 -2 3 -27 -4 -55 -15 -51 -19 -179 -53 -243 -64 l-35 -6 30 22 c69 49 161 155 197 228 21 41 36 76 34 78 -2 2 -27 -8 -55 -22 -76 -39 -146 -66 -240 -92 l-84 -23 -51 45 c-60 53 -166 107 -251 129 -80 20 -233 18 -310 -4z"></path></g></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?=urlencode($http.$hostDomain.$routerResult['seoLink'])?>" class="share-btn facebook" title="Share on Facebook" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="0 0 1280.000000 1275.000000" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,1275.000000) scale(0.100000,-0.100000)" stroke="none"><path d="M1280 12735 c-308 -46 -560 -186 -772 -430 -185 -211 -338 -501 -459 -868 l-49 -149 3 -5006 2 -5007 23 -97 c62 -271 169 -458 366 -645 52 -49 136 -117 188 -152 207 -140 529 -272 851 -350 l127 -31 4983 3 4982 2 100 23 c595 137 971 631 1154 1516 l21 99 0 4776 0 4776 -25 105 c-191 788 -547 1255 -1068 1400 -189 53 100 50 -5320 49 -3998 -1 -5039 -4 -5107 -14z m8855 -1376 c39 -22 60 -46 74 -88 8 -24 11 -211 11 -634 0 -586 -1 -602 -21 -643 -15 -31 -32 -48 -63 -63 -41 -20 -58 -21 -485 -21 l-443 0 -61 -32 c-94 -50 -176 -137 -225 -238 -68 -139 -76 -203 -77 -575 0 -311 1 -321 22 -361 14 -26 36 -48 60 -60 36 -18 74 -19 666 -22 l627 -3 0 -594 0 -595 -618 0 -618 0 -44 -22 c-34 -18 -51 -35 -70 -73 l-25 -49 -3 -2908 -2 -2908 -943 0 c-897 0 -944 1 -983 19 -25 12 -50 33 -65 57 l-24 39 -3 2923 -2 2922 -600 0 -600 0 0 524 c0 347 4 534 11 553 13 35 45 70 84 91 26 15 88 18 505 22 377 4 480 8 500 19 37 20 71 53 83 81 7 16 13 170 18 415 4 276 11 425 23 510 55 399 163 713 333 970 92 139 283 329 423 421 292 194 645 302 1090 333 63 5 408 8 765 7 583 -1 653 -3 680 -17z"/></g></svg>
                </a>
                <a href="mailto:?subject=<?=urlencode($http.$hostDomain)?>&amp;body=<?=urlencode($http.$hostDomain.$routerResult['seoLink'])?>" class="share-btn email" title="Share via Email" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" height="40px" width="40px" id="Capa_1" viewBox="0 0 75.294 75.294" xml:space="preserve"><g><path d="M66.097,12.089h-56.9C4.126,12.089,0,16.215,0,21.286v32.722c0,5.071,4.126,9.197,9.197,9.197h56.9   c5.071,0,9.197-4.126,9.197-9.197V21.287C75.295,16.215,71.169,12.089,66.097,12.089z M61.603,18.089L37.647,33.523L13.691,18.089   H61.603z M66.097,57.206h-56.9C7.434,57.206,6,55.771,6,54.009V21.457l29.796,19.16c0.04,0.025,0.083,0.042,0.124,0.065   c0.043,0.024,0.087,0.047,0.131,0.069c0.231,0.119,0.469,0.215,0.712,0.278c0.025,0.007,0.05,0.01,0.075,0.016   c0.267,0.063,0.537,0.102,0.807,0.102c0.001,0,0.002,0,0.002,0c0.002,0,0.003,0,0.004,0c0.27,0,0.54-0.038,0.807-0.102   c0.025-0.006,0.05-0.009,0.075-0.016c0.243-0.063,0.48-0.159,0.712-0.278c0.044-0.022,0.088-0.045,0.131-0.069   c0.041-0.023,0.084-0.04,0.124-0.065l29.796-19.16v32.551C69.295,55.771,67.86,57.206,66.097,57.206z"/></g></svg>
                </a>
                <?php if($pageIsFavorite==1){
                    $productFavoriteLink = "/?/control/member/get/removeFavorite&productUniqID=".$uniqueId;
                    $productFavoriteCss = "addedFav";
                    $productFavoriteTitle = _urun_detay_fav_cikar_yazi;
                }else{
                    $productFavoriteLink = "/?/control/member/get/addFavorite&productUniqID=".$uniqueId;
                    $productFavoriteCss = "fav";
                    $productFavoriteTitle = _urun_detay_fav_ekle_yazi;
                }
                ?>
                <a href="<?=$productFavoriteLink?>" class="share-btn <?=$productFavoriteCss?>" title="<?=$productFavoriteTitle?>" target="_self">
                    <svg class="svg-icon svg-icon-favorites" width="40px" height="40px" x="0px" y="0px" viewBox="0 0 512 512"  xml:space="preserve"><path d="M490.4,231.9C447.6,314.1,333.1,426,268.4,485.6c-6.9,6.3-17.5,6.3-24.5,0C178.9,426,64.4,314.1,21.6,231.9   c-94-181,143-301.6,234.4-120.7C347.4-69.8,584.3,50.9,490.4,231.9z"/></svg>
                </a>
            </div>
        </form>
    </div>
</div>
 <div class="product-description-container">
     <?php if(!empty($pageContent)): ?>
     <div class="product-long-description">
         <h2><?=_urun_detay_urun_aciklama_yazi?></h2>
         <?php
         $pageContent = html_entity_decode($pageContent);
         echo $pageContent;
         ?>
     </div>
        <?php endif; ?>
     <?php $productVariantJson = json_decode($productVariantProperties,true);
     if(count($productVariantJson)>0
     || !empty($pageFiles) || !empty($pageVideos)){?>
     <div class="product-related-product variants">
         <?php
         if(count($productVariantJson)>0) echo '<h2>'._urun_detay_urun_secenekleri.'</h2>';
             foreach($productVariantJson as $productVariant){

                 echo '<div class="product-attributes">';
                 if(!empty($productVariant['variantName'])) {
                     echo '<div class="product-attribute"><span class="attribute-name">' . $productVariant['variantName'] . "</span></div>";
                 }
                 echo '<div class="product-attribute"><span class="attribute-name">' ._urun_detay_urun_stok_kod_yazi. " " .$productVariant['variantStockCode']."</span></div>";
                 /*if($productVariant['variantProperties']){
                     foreach ($productVariant['variantProperties'] as $attribute){
                         if($attribute['attribute']){
                             echo '<div class="product-attribute"><span class="attribute-name">' .$attribute['attribute']['name'].'</span><span class="attribute-value">' . $attribute['attribute']['value']."</span></div>";
                         }
                     }
                 }*/
                 echo '</div>';
             }
            ?>
         <?php
         if(!empty($pageFiles)){
             ?>
             <div class="page-file-container">
                 <?php
                 foreach ($pageFiles as $pageFile){
                     $fileID = $pageFile['fileID'];
                     $fileName = $pageFile['fileName'];
                     $filePath = $pageFile['filePath'];
                     $fileSize = $pageFile['fileSize'];
                     $fileExtension = $pageFile['fileExtension'];
                     $fileFolderName = "Product";//$pageFile['fileFolderName'];
                     $fileIcon = fileRoot. $fileExtension . ".png";
                     ?>
                     <a class="page-file-box" href="<?php echo fileRoot.$fileFolderName . '/' . $filePath; ?>" target="_blank">
                         <img src="<?php echo $fileIcon; ?>" alt="<?php echo $fileName; ?>" width="50" height="50">
                         <span><?php echo $fileName; ?></span>
                     </a>
                     <?php
                 }
                 ?>
             </div>
             <?php
         }
         if(!empty($pageVideos)){
             ?>
             <div class="page-video-container">
                 <?php
                 //Array ( [0] => Array ( [video_id] => 1 [created_at] => 2024-12-12 16:33:29 [updated_at] => 2024-12-12 16:33:29 [video_name] => Food Containers Anime [video_file] => [video_extension] => [video_size] => [video_width] => 0 [video_height] => 0 [unique_id] => FCK85YTADQU9MPRBH36J [video_iframe] => https://www.youtube.com/embed/FCK85YTADQU9MPRBH36J?rel=0 [description] => Food Containers Anime [is_deleted] => 0 ) )
                 foreach($pageVideos as $pageVideo) {
                     $videoIframe = $pageVideo['video_iframe'];
                     $videoName = $pageVideo['video_name'];
                     $videoID = $pageVideo['video_id'];
                     ?>
                     <div class="page-video-box">
                         <div class="page-video-title"><?=$videoName?></div>
                         <div class="page-video-iframe">
                             <?=$videoIframe?>
                         </div>
                     </div>
                     <?php
                 }
                 ?>
             </div>
             <?php
         }
         ?>
     </div>
     <?php } ?>
     <?php if(count($relatedProduct)>0){ ?>
        <div class="product-related-product">
            <h2><?=_urun_detay_iliskili_urunler_yazi?></h2>
            <div class="product-list">
                <?php
                //print_r($relatedProduct);exit();
                // her iki üründe bir aşağıdaki satırdan başlasın
                $pb = 0;
                foreach($relatedProduct as $product){
                    include VIEW . "Product/ProductBox.php";
                }
                ?>
            </div>
        </div>
     <?php } ?>
 </div>
<?php
if(!empty($pageGallery)){
$galleryName = $pageGallery['galleryName'];
$galleryDescription = $pageGallery['galleryDescription'];
$galleryImages = $pageGallery['galleryImages'];
?>
<div class="galleryConteyner">
    <div class="galleryTitle">
        <h2><?=$galleryName?></h2>
    </div>
    <?php if(!empty($galleryDescription)):?>
        <div class="galleryDescription">
            <p><?=$galleryDescription?></p>
        </div>
    <?php endif;?>
    <div class="galleryImages">
        <?php
        foreach($galleryImages as $galleryImage) {
            $imageID = $galleryImage['imageID'];
            $galleryImageFolderName = $galleryImage['imageFolderName'];
            $galleryImagePath = $galleryImage['imagePath'];
            $galleryImageName = $galleryImage['imageName'];
            $galleryImageWidth = $galleryImage['imageWidth'];
            $galleryImageHeight = $galleryImage['imageHeight'];
            ?>
            <div class="galleryImage">
                <img class="thumbnail" src="<?=imgRoot."?imagePath=".$galleryImageFolderName.'/'.$galleryImagePath?>&width=300" alt="<?=$galleryImageName?>" width="300"   data-src="<?php echo imgRoot."?imagePath=". $galleryImageFolderName.'/'.$galleryImagePath; ?>">
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    }
?>
<div class="product-category-products">
    <div class="product-list">
        <?php
        // her iki üründe bir aşağıdaki satırdan başlasın
        $pb = 0;
        foreach($categoryProducts as $productCounter => $product){
            include VIEW . "Product/ProductBox.php";
            if($productCounter==4){
                break;
            }
        }
        ?>
    </div>
</div>

<!-- Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <a class="prev">&#10094;</a>
        <img class="modal-img" src="" alt="">
        <a class="next">&#10095;</a>
    </div>
</div>
