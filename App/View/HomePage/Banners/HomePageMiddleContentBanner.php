<?php
/**
 * @var array $bannerInfo
 * @var object $config
 * @var string imgRoot
 * @var array $mainMiddleContentBanners;
 */
//$bannerInfo dizisi içinden ["bannerKategori"]=1 olanları $mainBanners'a atayalım
$ampStatus = $config->ampStatus;
$ampPrefix = $config->ampPrefix;
$ampLayout = $config->ampLayout;
$ampImgEnd = $config->ampImgEnd;
;

//print_r($mainMiddleContentBanners);exit();
$totalBanners = count($mainMiddleContentBanners);
if($totalBanners>0){
    ?>
    <section class="middleContentBannersContainer">
    <?php
    $counter = 1;
    foreach ($mainMiddleContentBanners as $banner){
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
        ?>
        <div class="<?=$alignmentClass?>">
            <a href="<?=$bannerLink?>" title="<?=$bannerSlogan?>">
                <<?=$ampPrefix?>img
                    src="<?=imgRoot."?imagePath=".$bannerImage?>"
                    alt="<?=$bannerSlogan?>"
                    width="100"
                    height="100"
                    <?=$ampLayout?>><?=$ampImgEnd?>
            </a>
            <div>
                <?php if(!empty($bannerSlogan)&&$bannerSlogan!="#"):?><h2><?=$bannerSlogan?></h2><?php endif;?>
                <?php if(!empty($bannerText)&&$bannerText!="#"):?><p><?=$bannerText?></p><?php endif;?>
                <?php if(!empty($bannerButton)&&$bannerButton!="#"):?><a href="<?=$bannerLink?>" title="<?=$bannerButton?>" class="btn <?=$buttonClass?>"><?=$bannerButton?></a><?php endif;?>
            </div>
        </div>
        <?php
        $counter++;
    }
    ?>
    </section>
    <?php
}
?>
