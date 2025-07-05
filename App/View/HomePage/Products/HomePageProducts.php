<?php
/**
 * @var array $homePageNewProducts
 * @var array $homePageSpecialOfferProducts
 * @var array $homePageHomepageProducts
 * @var array $homePageDiscountedProducts
 * @var array $configPriceSettings
 * @var array $currencyRates
 * @var string $ampPrefix
 * @var string $ampLayout
 * @var string $ampImgEnd
 */

$configShowDiscount = $configPriceSettings["eskifiyat"] ?? 1;
$configShowPrice = $configPriceSettings["fiyatgoster"] ?? 1;
$configPriceUnit = $configPriceSettings["parabirim"] ?? 1;

$usdToTry = $currencyRates["usd"] ?? 1;
$eurToTry = $currencyRates["euro"] ?? 1;

?>
<?php
if(!empty($homePageNewProducts) && count($homePageNewProducts)>0):
    ?>
    <section class="homepage-new-products-container">
        <h1><?=_anasayfa_tab_yeni_urun_yazi?></h1>
        <?php
        $pb = 0;
        foreach ($homePageNewProducts as $product):
            include VIEW . "Product/ProductBox.php";
        endforeach;
        ?>
    </section>
    <?php
endif;
?>
<?php
//print_r($homePageSpecialOfferProducts);
if(count($homePageSpecialOfferProducts)>0):
    ?>
    <section class="homepage-special-offer-products-container">
        <h1><?=_anasayfa_baslik_firsat_urunleri_yazi?></h1>
        <?php
        $pb = 0;
        foreach ($homePageSpecialOfferProducts as $product):
            include VIEW . "Product/ProductBox.php";
        endforeach;
        ?>
    </section>
    <?php
endif;
?>
<?php
if(count($homePageHomepageProducts)>0):
    ?>
    <section class="homepage-homepage-products-container">
        <h1><?=_anasayfa_sizin_icin_sectiklerimiz_yazi?></h1>
        <?php
        $pb = 0;
        foreach ($homePageHomepageProducts as $product):
            include VIEW . "Product/ProductBox.php";
        endforeach;
        ?>
    </section>
    <?php
endif;
?>
<?php
if(count($homePageDiscountedProducts)>0):
    ?>
    <section class="homepage-discounted-products-container">
        <h1><?=_anasayfa_tab_indirimli_yazi?></h1>
        <?php
        $pb = 0;
        foreach ($homePageDiscountedProducts as $product):
            include VIEW . "Product/ProductBox.php";
        endforeach;
        ?>
    </section>
    <?php
endif;
?>
<?php if(count($homePageNewProducts)>0 || count($homePageSpecialOfferProducts)>0 || count($homePageHomepageProducts)>0 || count($homePageDiscountedProducts)>0): ?>
<div class="our-support-services-container">
    <div>
        <h2><?=_anasayfa_hizli_kargo?></h2>
        <svg width="40" height="40" x="0px" y="0px" viewBox="0 0 512 512" >
            <g>
                <g>
                    <path d="M509.14,261.159c0.203-0.198,0.332-0.318,0.332-0.318l-75.734-86.458c0,0-0.185,0.08-0.496,0.206    c-1.871-2.251-4.691-3.685-7.846-3.685h-65.199v-61.178c0-5.632-4.566-10.199-10.199-10.199H29.3    c-5.633,0-10.199,4.567-10.199,10.199v224.04h-8.902C4.566,333.766,0,338.333,0,343.965s4.566,10.199,10.199,10.199h8.902v11.919    c0,5.632,4.566,10.199,10.199,10.199h20.099c4.661,20.687,23.172,36.191,45.247,36.191c22.075,0,40.586-15.504,45.248-36.191    h239.251v-0.001c4.661,20.687,23.172,36.191,45.247,36.191c22.075,0,40.586-15.504,45.248-36.191h32.162    c5.633,0,10.199-4.567,10.199-10.199v-97.851C512,265.482,510.908,262.993,509.14,261.159z M94.646,392.074    c-14.332,0-25.992-11.661-25.992-25.992c0-14.331,11.66-25.992,25.992-25.992c14.332,0,25.993,11.661,25.993,25.992    C120.638,380.413,108.979,392.074,94.646,392.074z M339.799,355.882H139.893c-4.661-20.687-23.172-36.191-45.248-36.191    c-22.075,0-40.586,15.504-45.247,36.191h-9.899v-44.947h300.299V355.882z M339.799,290.537H39.499V119.924h300.299V290.537z     M435.596,207.46l44.299,50.572h-44.299V207.46z M424.391,392.074c-14.332,0-25.992-11.661-25.992-25.992    c0-14.331,11.66-25.992,25.992-25.992c14.332,0,25.993,11.661,25.993,25.992C450.384,380.413,438.723,392.074,424.391,392.074z     M491.602,355.882h-21.963c-4.661-20.687-23.172-36.191-45.248-36.191c-22.075,0-40.586,15.504-45.247,36.191h-18.947V191.301h55    v76.929c0,5.632,4.566,10.199,10.199,10.199h66.205V355.882z"/>
                </g>
            </g>
            <g>
                <g>
                    <path d="M315.155,259.059h-85.673c-5.633,0-10.199,4.567-10.199,10.199s4.566,10.199,10.199,10.199h85.673    c5.633,0,10.199-4.567,10.199-10.199S320.788,259.059,315.155,259.059z"/>
                </g>
            </g>
            <g>
                <g>
                    <path d="M197.865,259.059h-6.12c-5.633,0-10.199,4.567-10.199,10.199s4.566,10.199,10.199,10.199h6.12    c5.633,0,10.199-4.567,10.199-10.199S203.498,259.059,197.865,259.059z"/>
                </g>
            </g>

    </div>
    <div>
        <h2><?=_anasayfa_musteri_hizmetleri?></h2>
        <svg height="40px" width="40px" viewBox="0 0 32 32" xml:space="preserve">
            <path d="M16,2C9.4,2,4,7.4,4,14v3c0,2.8,2.2,5,5,5c0.6,0,1-0.4,1-1v-8c0-0.6-0.4-1-1-1c-1.1,0-2.1,0.4-2.9,1c0.5-5,4.8-9,9.9-9
                s9.4,3.9,9.9,9c-0.8-0.6-1.8-1-2.9-1c-0.6,0-1,0.4-1,1v8c0,0.6,0.4,1,1,1c0.6,0,1.3-0.1,1.8-0.4c-1.1,2-2.8,3.6-4.9,4.5
                c-0.2-1.2-1.2-2.2-2.5-2.2c-1.3,0-2.4,1.1-2.4,2.4c0,0.7,0.3,1.4,0.9,1.9c0.5,0.4,1,0.6,1.6,0.6c0.1,0,0.3,0,0.4,0
                C23.6,28,28,22.9,28,17v-3C28,7.4,22.6,2,16,2z"/>
            </svg>
    </div>
    <div>
        <h2><?=_anasayfa_guvenli_alis_veris?></h2>
        <svg width="40" height="40" viewBox="0 0 846.66 846.66">
            <g id="Layer_x0020_1">
                <path class="fil0" d="M79.25 105.11l688.16 0c39.11,0 71.23,32.12 71.23,71.23l0 493.98c0,39.11 -32.13,71.23 -71.23,71.23l-688.16 0c-39.1,0 -71.22,-32.12 -71.22,-71.23l0 -493.98c0,-39.11 32.12,-71.23 71.22,-71.23zm-29.7 115.78l747.56 0 0 -44.55c0,-16.41 -13.3,-29.7 -29.7,-29.7l-688.16 0c-16.4,0 -29.7,13.29 -29.7,29.7l0 44.55zm747.56 70.24l-747.56 0 0 379.19c0,16.41 13.3,29.7 29.7,29.7l688.16 0c16.4,0 29.7,-13.29 29.7,-29.7l0 -379.19zm-248.58 188.85l174.42 0c11.47,0 20.77,9.3 20.77,20.77l0 126.66c0,11.47 -9.3,20.76 -20.77,20.76l-174.42 0c-11.46,0 -20.76,-9.29 -20.76,-20.76l0 -126.66c0,-11.47 9.3,-20.77 20.76,-20.77zm153.66 41.53l-132.89 0 0 85.14 132.89 0 0 -85.14zm-584.11 -51.17c-27.31,0 -27.31,-41.52 0,-41.52l214.82 0c27.31,0 27.31,41.52 0,41.52l-214.82 0zm0 -78.49c-27.31,0 -27.31,-41.53 0,-41.53l214.82 0c27.31,0 27.31,41.53 0,41.53l-214.82 0z"/>
            </g>
        </svg>
    </div>
    <div>
        <h2><?=_anasayfa_kolay_iade?></h2>
        <svg width="40px" height="40px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M486.4 422.4c0-42.667-34.133-76.8-76.8-76.8s-76.8 34.133-76.8 76.8c0 42.667 34.133 76.8 76.8 76.8s76.8-34.133 76.8-76.8zm-106.667 0c0-21.333 17.067-34.133 34.133-34.133s34.133 17.067 34.133 34.133-17.067 34.133-34.133 34.133-34.133-12.8-34.133-34.133zm226.134 251.733c42.667 0 76.8-34.133 76.8-76.8s-34.133-76.8-76.8-76.8-76.8 34.133-76.8 76.8 34.133 76.8 76.8 76.8zm0-110.933c17.067 0 34.133 17.067 34.133 34.133 0 21.333-17.067 34.133-34.133 34.133s-34.133-17.067-34.133-34.133c0-21.333 17.067-34.133 34.133-34.133zM396.8 652.8c4.267 0 12.8-4.267 17.067-8.533l217.6-226.133c8.533-8.533 8.533-21.333 0-29.867s-21.333-8.533-29.867 0L384 614.4c-8.533 8.533-8.533 21.333 0 29.867 0 4.267 4.267 8.533 12.8 8.533z"/><path d="M955.733 512c0-247.467-200.533-448-448-448C264.533 64 64 264.533 64 512c0 238.933 187.733 435.2 426.667 448 12.8 0 21.333-8.533 21.333-21.333s-8.533-21.333-21.333-21.333C273.067 908.801 102.4 729.601 102.4 512.001c0-221.867 179.2-405.333 405.333-405.333s405.333 179.2 405.333 405.333c0 187.733-128 349.867-311.467 392.533l17.067-29.867c4.267-8.533 4.267-21.333-8.533-29.867-8.533-4.267-21.333-4.267-29.867 8.533l-34.133 59.733c-4.267 0-4.267 4.267-8.533 8.533s-4.267 8.533-4.267 12.8 4.267 17.067 8.533 21.333c4.267 0 8.533 4.267 12.8 4.267l59.733 34.133c4.267 0 8.533 4.267 12.8 4.267 8.533 0 12.8-4.267 17.067-8.533 4.267-8.533 4.267-21.333-8.533-29.867l-17.067-12.8c200.533-55.467 337.067-230.4 337.067-435.2z"/></svg>
    </div>
</div>
<?php endif; ?>