<?php
/**
 * @var array $bannerInfo
 * @var object $config
 * @var array $categorySliderBanner
 * @var int $ampStatus
 * @var string $ampPrefix
 * @var string $ampLayout
 * @var string $ampImgEnd

 */


$totalSlides = count($categorySliderBanner);
if($totalSlides>0){
?>
<section id="slider" class="homeSlider slider">
    <h3>Home Slider</h3>
    <?php
    for ($i = 1; $i <= $totalSlides; $i++) {
        echo '<input ' . (($i === 1) ? 'checked' : '') . ' value="' . $i . '" type="radio" name="slider" id="slide' . $i . '" />';
    }
    ?>
    <div id="slides" class="slides">
        <div id="overflow" class="overflow">
            <div class="inner">
                <?php
                $i = 0;
                foreach($categorySliderBanner as $banner) {
                    //print_r($banner);exit;
                    $i++;
                    $bannerSlogan = $banner["bannerslogan"];
                    $bannerText = $banner["banneryazi"];
                    $bannerLink = $banner["bannerlink"];
                    $bannerImage = $banner["bannerresim"];
                    $bannerButtonText = $banner["banner_button"];
                    $bannerButtonLocation = $banner["banner_button_location"];

                    // checks for local/external image source
                    if (!str_contains($bannerImage, "http://") && !str_contains($bannerImage, "https://")) {
                        // if condition specific to AMP (Accelerated Mobile Pages) is true
                        if ($ampStatus) {
                            $bannerImage = 'srcset="' . imgRoot."?imagePath=".$bannerImage . '&width=480 480w,
                        ' . imgRoot."?imagePath=".$bannerImage .'&width=800 800w,
                        ' . imgRoot."?imagePath=".$bannerImage .'&width=1024 1024w,
                        ' . imgRoot."?imagePath=".$bannerImage .'&width=1240 1240w" 
                        src="' . imgRoot."?imagePath=".imgRoot."?imagePath=".$bannerImage . '&width=1240"';
                        }
                        else {
                            $bannerImage = 'data-srcset="' . imgRoot."?imagePath=".$bannerImage . '&width=480 480w,
                        ' . imgRoot."?imagePath=".$bannerImage .'&width=800 800w,
                        ' . imgRoot."?imagePath=".$bannerImage .'&width=1024 1024w,
                        ' . imgRoot."?imagePath=".$bannerImage .'&width=1240 1240w"
                        data-src="' . imgRoot."?imagePath=".$bannerImage . '&width=1240" 
                        src="'.imgRoot."?imagePath=".$bannerImage.'"';
                        }
                    } else {
                        $bannerImage = 'src="' . $bannerImage . '"';
                    }

                    ?>
                    <a href="<?= $bannerLink ?>" title="<?= $bannerSlogan ?>" id="aslide<?= $i ?>">
                        <article>
                            <<?=$ampPrefix?>img <?= $bannerImage ?>
                                alt="<?= $bannerSlogan ?>" width="1240" height="350"
                                <?=$ampLayout?>><?=$ampImgEnd?>
                            <?php if(!empty($bannerSlogan) && $bannerSlogan !== "#" && !empty($bannerText) && $bannerText !== "#"):?>
                                <div class="info">
                                    <h3><?= $bannerSlogan ?></h3>
                                    <?= $bannerText ?>
                                    <?php
                                    if(!empty($bannerButtonText) && $bannerButtonText !== "#"):?>
                                        <button class="btn btn-primary btn-lg"><?= $bannerButtonText ?></button>
                                    <?php endif;?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php if($totalSlides>1): ?>
    <div id="controls">
        <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
            <label for="slide<?=$i?>"><svg width="50px" height="50px" xmlns="http://www.w3.org/2000/svg" viewBox="2 2 19 19" fill="currentColor" class="slider-label" preserveAspectRatio="none"><path d="M15.54,11.29,9.88,5.64a1,1,0,0,0-1.42,0,1,1,0,0,0,0,1.41l4.95,5L8.46,17a1,1,0,0,0,0,1.41,1,1,0,0,0,.71.3,1,1,0,0,0,.71-.3l5.66-5.65A1,1,0,0,0,15.54,11.29Z"></path></svg></label>
        <?php endfor; ?>
    </div>
    <div id="active">
        <?php $i=0;foreach ($categorySliderBanner as $key => $banner): $i++;?>
            <label for="slide<?=$i?>">
                <<?=$ampPrefix?>img src="<?=imgRoot."?imagePath=".$banner["bannerresim"]?>&width=70" width="70" height="40" <?=$ampLayout?>><?=$ampImgEnd?>
            </label>
        <?php endforeach; ?>
    </div>
    <?php endif;?>
</section>
<?php }?>