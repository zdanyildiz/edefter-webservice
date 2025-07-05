<?php
/**
 * @var array $bannerInfo
 * @var object $config
 * @var array $mainBanners
 */
//$bannerInfo dizisi içinden ["bannerKategori"]=1 olanları $mainBanners'a atayalım
$ampStatus = $config->ampStatus;
$ampPrefix = $config->ampPrefix;
$ampLayout = $config->ampLayout;
$ampImgEnd = $config->ampImgEnd;

if(!empty($mainBanners)){
    // Slider içeriğini direk yazdır
    echo $mainBanners;
}
?>
