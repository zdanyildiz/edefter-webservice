<?php
/**
 * @var object $config
 * @var string $imgRoot
 * @var array $carouselBanner;
 */

$ampStatus = $config->ampStatus;
$ampPrefix = $config->ampPrefix;
$ampLayout = $config->ampLayout;
$ampImgEnd = $config->ampImgEnd;


$totalBanners = count($carouselBanner);
if($totalBanners>0){
    ?>
    <section class="carousel">
        <div class="carousel-inner">
        <?php
        $counter = 1;
        //aktif itemi tam ortadaki yapalım
        $activeItem = $totalBanners/2;
        $activeItem = intval($activeItem)+1;
        foreach ($carouselBanner as $banner){
            $onlyImage = false;
            $bannerSlogan = $banner["bannerslogan"];
            $bannerText = $banner["banneryazi"];

            if(($bannerSlogan=="#" || empty($bannerSlogan)) && ($bannerText=="#") || empty($bannerText)){
                $onlyImage = true;
            }

            $bannerLink = $banner["bannerlink"];
            $bannerImage = $banner["bannerresim"];
            $bannerButton = $banner["banner_button"];
            $alignmentClass = ($counter % 2 == 0) ? "right-aligned" : "left-aligned";
            //button class
            $banner_button_location = $banner["banner_button_location"];
            //1-üst sol, 2-Üst Orta, 3-Üst Sağ, 4-orta Sol, 5-Orta, 6-Orta Sağ, 7-Alt Sol, 8-Alt Orta, 9-Alt Sağ
            $buttonClass = "";
            switch ($banner_button_location){
                case 1:
                    $buttonClass = "top-left";
                    break;
                case 2:
                    $buttonClass = "top-center";
                    break;
                case 3:
                    $buttonClass = "top-right";
                    break;
                case 4:
                    $buttonClass = "middle-left";
                    break;
                case 5:
                    $buttonClass = "middle-center";
                    break;
                case 6:
                    $buttonClass = "middle-right";
                    break;
                case 7:
                    $buttonClass = "bottom-left";
                    break;
                case 8:
                    $buttonClass = "bottom-center";
                    break;
                case 9:
                    $buttonClass = "bottom-right";
                    break;
                default:
                    $buttonClass = "middle-center";
                    break;
            }
            if($onlyImage == true) $alignmentClass = "only-image";
            $activeClass = ($counter == $activeItem) ? "active" : "";
            ?>
            <div class="carousel-item <?=$activeClass?>" data-item"<?=$counter?>">
                <a href="<?=$bannerLink?>" title="<?=str_replace('"',"'",$bannerSlogan)?>">
                    <<?=$ampPrefix?>img
                        src="<?=imgRoot."?imagePath=".$bannerImage?>"
                        alt="<?=$bannerSlogan?>"
                        width="100"
                        height="100"
                        <?=$ampLayout?>><?=$ampImgEnd?>
                </a>
                <div class="carousel-content">
                    <?php if(!empty($bannerSlogan)&&$bannerSlogan!="#"):?><h2><?=$bannerSlogan?></h2><?php endif;?>
                    <?php if(!empty($bannerText)&&$bannerText!="#"):?><p><?=$bannerText?></p><?php endif;?>
                    <?php if(!empty($bannerButton)&&$bannerButton!="#"):?><a href="<?=$bannerLink?>" title="<?=$bannerButton?>" class="btn <?=$buttonClass?>"><?=$bannerButton?></a><?php endif;?>
                </div>
            </div>
            <?php
            $counter++;
        }
        ?>
        </div>
        <button class="carousel-control prev" onclick="void(0);">&#8249;</button>
        <button class="carousel-control next" onclick="void(0);">&#8250;</button>
    </section>
    <?php
}
?>