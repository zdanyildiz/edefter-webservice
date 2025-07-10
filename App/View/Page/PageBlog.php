<?php
/**
 * @var array $page
 * @var $view
 * @var $config
 * @var Casper $casper
 * @var array $bannerInfo
 */
$config = $casper->getConfig();
$helper = $config->Helper;
$pageTitle = $page['sayfaad'];
$pageContent = $page['sayfaicerik'];
$imageUrls = $page['resim_url'];
$imageUrls = ($imageUrls) ? explode(",",$imageUrls) : [];

$sliderBanner = array_filter($bannerInfo, function($banner){
    return $banner['bannerkategori'] === 1;
}) ?? [];

$middleContentBanner = array_filter($bannerInfo, function($banner) {
    return $banner['bannerkategori'] === 3;
}) ?? [];

$carouselBanner = array_filter($bannerInfo, function($banner) {
    return $banner['bannerkategori'] === 6;
}) ?? [];

$pageGallery = $page['pageGallery'] ?? [];
$pageFiles = $page['pageFiles'] ?? [];
$pageVideos = $page['pageVideos'] ?? [];
?>
<section class="pageMainContent">
    <div class="page-container">
        <?php
        $config->loadView("Page/Banners/PageSlider",["slider"=>$sliderBanner, "config"=>$config]);
        ?>
        <div class="page-title">
            <h1><?php echo $pageTitle; ?></h1>
        </div>
        <div class="page-content">
            <?php
            if (count($imageUrls) > 0) {
                ?>
                <figure class="page-image">
                    <img src="<?php echo imgRoot."?imagePath=". $imageUrls[0]; ?>&height=450" alt="<?php echo $pageTitle; ?>" title="<?php echo $pageTitle; ?>" id="firstImage" />
                    <?php
                    if (count($imageUrls) > 1) {?>
                        <div class="thumbnail-container">
                        <?php
                        for ($i = 0; $i < count($imageUrls); $i++) {
                            ?>
                            <img src="<?php echo imgRoot."?imagePath=". trim($imageUrls[$i]); ?>&height=75" alt="<?php echo $pageTitle; ?>" title="<?php echo $pageTitle; ?>" class="thumbnail" data-src="<?php echo imgRoot."?imagePath=". trim($imageUrls[$i]); ?>" />
                            <?php
                        }
                        ?>
                        </div>
                    <?php }
                    ?>
                    <figcaption><?php echo $pageTitle; ?></figcaption>
                </figure>
                <?php
            }
            ?>
            <div class="page-text">
                <?php echo (!empty($pageContent)) ? htmlspecialchars_decode($pageContent) : ''; ?>
            </div>
        </div>
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
                $fileFolderName = "Page";//$pageFile['fileFolderName'];
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
        $config->loadView("Page/Banners/PageCarousel",["carouselBanner"=>$carouselBanner, "config"=>$config]);

        $config->loadView("Page/Banners/PageMiddleContentBanner",["middleContentBanner"=>$middleContentBanner, "config"=>$config]);
        ?>
    </div>
    <aside class="pageRightAside">
        <div class="pageRightAsideContainer">
            <div class="pageRightAsideTitle">
                <h2><?php echo $page["kategoriler"]; ?></h2>
            </div>

            <div class="pageRightAsideContent summary">
                <h3></h3>
                <?php
                foreach ($page['categoryPages'] as $categoryPage) {

                    $categoryPageDetail = $categoryPage['pageDetails'] ?? [];
                    $categoryPageTitle = $categoryPageDetail['sayfaad'];
                    $categoryPageSeoLink = $categoryPageDetail['seoLink'];
                    $active = ($categoryPageSeoLink == $page['seoLink']) ? 'active' : '';
                    ?>
                    <li class="<?=$active?>"><a href="<?php echo $categoryPageSeoLink; ?>"><?php echo $categoryPageTitle; ?></a></li>
                    <?php
                }
                ?>
            </div>
        </div>
    </aside>
</div>
<?php if (count($imageUrls) > 0) {?>
<div id="pageModal" class="modal">
    <div class="modal-content">
        <span class="close btn">&times;</span>
        <a class="prev">&#10094;</a>
        <img class="modal-img" src="" alt="">
        <a class="next">&#10095;</a>
    </div>
</div>
<?php }?>