<?php
/**
 * @var AdminDatabase $db
 */
include_once MODEL.'Admin/AdminImage.php';
$imageModel = new AdminImage($db);
//$images = $imageModel->getProductImages(50,1);
$images = [];

include_once MODEL.'Admin/AdminFile.php';
$fileModel = new AdminFile($db);
//$files = $fileModel->getFiles(50,1);
$files = [];
?>
<div class="offcanvas">
	<div id="offcanvas-imageSearch" class="offcanvas-pane width-8">
        <input type="hidden" id="imageTarget" value="">
		<div class="offcanvas-head">
			<header class="text-primary">Tüm Resimler</header>
			<div class="offcanvas-tools">
				<a id="offcanvas-imageSearch" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
					<i class="md md-close"></i>
				</a>
			</div>			
		</div>

        <div class="form-group">
            <input type="text" class="form-control" name="searchImageName" id="searchImageName" placeholder="Resim Ara" style="padding: 5px">
        </div>

		<div class="offcanvas-body no-padding">
			<ul id="rightImageListContainer" class="list">
                <?php
				if(!empty($images))
				{
					foreach ($images as $image)
					{
                        $imageID = $image['imageID'];
                        $imageName = $image['imageName'];
                        $imagePath = $image['imagePath'];
                        $imageFolderName = $image['imageFolderName'];
                        $imageWidth = $image['imageWidth'];
                        $imageHeight = $image['imageHeight'];
                    ?>
                    <li class="tile">
                        <a 	class="tile-content ink-reaction selectImage"
                            data-imageid="<?=$imageID?>"
                            data-imagepath="<?=$imageFolderName.'/'.$imagePath?>"
                            data-imagename="<?=$imageName?>"
                            data-imagewidth="<?=$imageWidth?>"
                            data-imageheight="<?=$imageHeight?>"
                            data-backdrop="false" style="cursor:pointer;">
                            <div class="tile-icon">
                                <img src="<?=imgRoot."?imagePath=".$imageFolderName."/".$imagePath?>&width=100&height=100" alt="" />
                            </div>
                            <div class="tile-text">
                                <?=$imageName?>
                                <small><?=$imageFolderName?></small>
                            </div>
                        </a>
                    </li>
                    <?php
					}
				}
				?>
			</ul>
		</div>
	</div>
	<div id="offcanvas-fileSearch" class="offcanvas-pane width-8">
        <input type="hidden" id="fileTarget" value="">
        <div class="offcanvas-head">
            <header class="text-primary">Tüm Dosyalar</header>
            <div class="offcanvas-tools">
                <a id="offcanvas-fileSearch" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                    <i class="md md-close"></i>
                </a>
            </div>
        </div>

        <div class="form-group">
            <input type="text" class="form-control" name="searchFileName" id="searchFileName" placeholder="Dosya Ara" style="padding: 5px">
        </div>

		<div class="offcanvas-body no-padding">
			<ul id="rightFileListContainer" class="list">
				<?php
                if(!empty($files))
                {

                    foreach ($files as $file)
                    {
                        $fileID = $file['fileID'];
                        $fileName = $file['fileName'];
                        $filePath = $file['filePath'];
                        $fileFolderName = $file['fileFolderName'];
                        $fileExtension = $file['fileExtension'];
                        $fileImage = fileRoot."?fileExtension=".$fileExtension;
                        ?>
                        <li class="tile">
                            <a 	class="tile-content ink-reaction selectFile"
                                  data-fileid="<?=$fileID?>"
                                  data-filepath="<?=$fileFolderName.'/'.$filePath?>"
                                  data-filefoldername="<?=$fileFolderName?>"
                                  data-filename="<?=$fileName?>"
                                    data-fileextension="<?=$fileExtension?>"
                                  data-backdrop="false" style="cursor:pointer;">
                                <div class="tile-icon">
                                    <img src="<?=$fileImage?>" alt="" />
                                </div>
                                <div class="tile-text">
                                    <?=$fileName?>
                                    <small><?=$fileFolderName?></small>
                                </div>
                            </a>
                        </li>
                        <?php
                    }
                }
				?>
			</ul>
		</div>
	</div>
    <div id="offcanvasBanner" class="offcanvas-pane width-100">
        <input type="hidden" id="fileTarget" value="">
        <div class="offcanvas-head">
            <header class="text-primary">Banner Önizle</header>
            <div class="offcanvas-tools">
                <div class="col-md-2">
                </div>
                <div class="col-md-7">
                    <div class="form-group" id="bannerStyleContainer" data-initial-style="<?= htmlspecialchars($bannerGroupStyleClass ?? '') ?>">
                        <select id="bannerStyle" class="form-control"></select>
                    </div>
                </div>

                <div class="col-md-3">
                    <a id="offcanvasBannerClose" class="btn btn-icon-toggle btn-default-light pull-right" data-dismiss="offcanvas">
                        <i class="md md-close"></i>
                    </a>
                </div>
            </div>
        </div>
        <div id="previewPanel" class="card-body">
        <?php
        /**
         * @var array $banners
         * @var int $bannerGroupID
         * @var int $bannerTypeID
         * @var int $bannerLayoutID
         * @var BannerStyleModel $bannerStyleModel;
         * @var string $bannerGroupKind;
         * @var string $bannerLayoutGroup
         * @var string $bannerGroupView
         * @var string $bannerGroupStyleClass
         */

        $bannerGroupID = $bannerGroupID ?? 0;
        $bannerGroupID = intval($bannerGroupID);

        if($bannerGroupID > 0) {
            if($bannerTypeID == 1) {
                echo '<div class="preview-slider-container" style="display: flex; overflow-x: auto; gap: 15px; padding: 10px; border: 1px dashed #ccc; background-color: #f8f9fa; min-height: 250px; align-items: stretch;">';
            }

            $bannerBox_onlyText = <<<HTML
                <div id="bannerContainer-[n]" class="[class]" style="min-height: [banner-height]; background-color: [banner-background-color]; background-image: [banner-background-image];">
                    <span class="bannerLabel">Banner [n]</span>
                    <div class="banner-content-container" id="banner-content-container-[n]">
                        <div class="banner-content-box" id="bannerContentBox-[n]" style="background-color:[banner-content-box-color];">
                            <h2 id="bannerSlogan-[n]" class="bannerTitle" style="color:[banner-title-color]; font-size: [banner-title-font-size];">[banner-title]</h2>
                            <div id="bannerContent-[n]" class="bannerContent" style="color:[banner-content-color]; font-size: [banner-content-font-size];">[banner-content]</div>
                            [button-inside]
                        </div>
                        [button-outside]
                    </div>
                </div>
            HTML;
            $bannerBox_onlyImage = <<<HTML
                <div id="bannerContainer-[n]" class="[class] onlyImage" style="height:[banner-height]; background-color:[banner-background-color];">
                    <span class="bannerLabel">Banner [n]</span>
                    <div class="banner-content-container" id="banner-content-container-[n]">
                        <img src="[image]" id="bannerImage-[n]" alt="[button-title]" style="max-width: 100%; height: auto">
                        <div id="bannerButtonContainer-[n]" class="bannerButton location-[location]">
                            <button type="button" id="bannerButton-[n]" style="background-color:[buton-bgColor]; color:[button-text-color]; font-size:[button-text-size];">[button-title]</button>
                        </div>
                    </div>
                </div>
            HTML;
            $bannerBox_TextAndImage = <<<HTML
                <div id="bannerContainer-[n]" class="[class]" style="min-height: [banner-height]; background-color: [banner-background-color];">
                    <span class="bannerLabel">Banner [n]</span>
                    <div class="banner-content-container" id="banner-content-container-[n]">
                        <div class="banner-image-wrapper">
                          <img src="[image]" id="bannerImage-1" class="bannerImage" alt="Çalışkanlığımızla Öne Çıkıyoruz">
                        </div>
                        <div class="banner-content-box" id="bannerContentBox-[n]" style="background-color:[banner-content-box-color];">
                            <h2 id="bannerSlogan-[n]" class="bannerTitle" style="color:[banner-title-color]; font-size: [banner-title-font-size];">[banner-title]</h2>
                            <div id="bannerContent-[n]" class="bannerContent" style="color:[banner-content-color]; font-size: [banner-content-font-size];">[banner-content]</div>
                            [button-inside]
                        </div>
                        [button-outside]
                    </div>
                </div>
            HTML;
            $bannerButton = <<<HTML
                <div id="bannerButtonContainer-[n]" class="bannerButton location-[location]">
                    <button type="button" id="bannerButton-[n]" style="background-color:[buton-bgColor]; color:[button-text-color]; font-size:[button-text-size];">[button-title]</button>
                </div>
            HTML;

            /*if($bannerTypeID == 1){
                $bannerGroupStyleClass = 'slide '. $bannerGroupStyleClass;
            }*/
            $bannerClass = $bannerGroupStyleClass;

            $bannerClass = $bannerClass . " " . $bannerGroupView;

            foreach ($banners as $key => $banner) {
                $bannerStyleID = $banner["style_id"];
                $bannerStyle = $bannerStyleModel->getStyleById($bannerStyleID);
                $bannerTitle = $banner["title"];
                $bannerContent = $banner["content"];
                $bannerImage = !empty($banner["image"]) ? "/Public/Image/" . $banner["image"] : "";

                if ($bannerStyle) {
                    $bannerStyle = $bannerStyle[0];
                    $bannerHeightSize = $bannerStyle['banner_height_size'];
                    $bannerBgColor = $bannerStyle["background_color"];
                    $bannerContentBoxBgColor = $bannerStyle["content_box_bg_color"];
                    $bannerTitleColor = $bannerStyle["title_color"];
                    $bannerTitleFontSize = $bannerStyle["title_size"];
                    $bannerContentColor = $bannerStyle["content_color"];
                    $bannerContentFontSize = $bannerStyle["content_size"];
                    $bannerShowButton = $bannerStyle["show_button"];
                    $bannerButtonTitle = $bannerStyle["button_title"];
                    $bannerButtonLocation = $bannerStyle["button_location"];
                    $bannerButtonBgColor = $bannerStyle["button_background"];
                    $bannerButtonTextColor = $bannerStyle["button_color"];
                    $bannerButtonHoverBackground = $bannerStyle["button_hover_background"];
                    $bannerButtonHoverTextColor = $bannerStyle["button_hover_color"];
                    $bannerButtonTextSize = $bannerStyle["button_size"];
                }
                else {
                    $bannerHeightSize = 120;
                    $bannerBgColor = "rgba(255,243,0,1)";
                    $bannerContentBoxBgColor = "rgba(255,255,255,1)";
                    $bannerTitleColor = "rgba(0,0,0,1)";
                    $bannerTitleFontSize = 24;
                    $bannerContentColor = "rgba(0,0,0,1)";
                    $bannerContentFontSize = 18;
                    $bannerShowButton = 1;
                    $bannerButtonTitle = "Detaylar";
                    $bannerButtonLocation = 0;
                    $bannerButtonBgColor = "rgba(0,0,0,1)";
                    $bannerButtonTextColor = "rgba(255,255,255,1)";
                    $bannerButtonHoverBackground = "gba(255,255,255,1)";
                    $bannerButtonHoverTextColor = "rgba(255,255,255,1)";
                    $bannerButtonTextSize = 18;
                }

                if ($bannerGroupKind == "only_text") {
                    $bannerBox = $bannerBox_onlyText;
                }
                elseif ($bannerGroupKind == "text_and_image") {
                    $bannerBox = $bannerBox_TextAndImage;
                }
                else {
                    $bannerBox = $bannerBox_onlyImage;
                }

                if($bannerShowButton != 1){
                    $bannerButton = str_replace('class="bannerButton', 'class="bannerButton hidden', $bannerButton);
                }

                if ($bannerButtonLocation == 0) {
                    $bannerBox = str_replace("[button-inside]", $bannerButton, $bannerBox);
                    $bannerBox = str_replace("[button-outside]", "", $bannerBox);
                }
                else {
                    $bannerBox = str_replace("[button-outside]", $bannerButton, $bannerBox);
                    $bannerBox = str_replace("[button-inside]", "", $bannerBox);
                }


                $bannerCount = $key + 1;
                $bannerBox = str_replace("[n]", $bannerCount, $bannerBox);
                $bannerBox = str_replace("[class]", $bannerClass, $bannerBox);
                $bannerBox = str_replace("[location]", $bannerButtonLocation, $bannerBox);
                $bannerBox = str_replace("[banner-title]", $bannerTitle, $bannerBox);
                $bannerBox = str_replace("[banner-content]", $bannerContent, $bannerBox);
                $bannerBox = str_replace("[button-title]", $bannerButtonTitle, $bannerBox);
                $bannerBox = str_replace("[image]", $bannerImage, $bannerBox);
                $bannerBox = str_replace("[buton-bgColor]", $bannerButtonBgColor, $bannerBox);
                $bannerBox = str_replace("[button-text-color]", $bannerButtonTextColor, $bannerBox);
                $bannerBox = str_replace("[button-hover-bgColor]", $bannerButtonHoverBackground, $bannerBox);
                $bannerBox = str_replace("[button-hover-color]", $bannerButtonHoverTextColor, $bannerBox);
                $bannerBox = str_replace("[button-text-size]", $bannerButtonTextSize . "px", $bannerBox);
                $bannerBox = str_replace("[banner-height]", $bannerHeightSize . "px", $bannerBox);
                $bannerBox = str_replace("[banner-background-color]", $bannerBgColor, $bannerBox);
                $bannerBox = str_replace("[banner-content-box-color]", $bannerContentBoxBgColor, $bannerBox);
                $bannerBox = str_replace("[banner-title-color]", $bannerTitleColor, $bannerBox);
                $bannerBox = str_replace("[banner-title-font-size]", $bannerTitleFontSize . "px", $bannerBox);
                $bannerBox = str_replace("[banner-content-color]", $bannerContentColor, $bannerBox);
                $bannerBox = str_replace("[banner-content-font-size]", $bannerContentFontSize . "px", $bannerBox);
                $bannerBox = str_replace("[banner-background-image]", "url($bannerImage)", $bannerBox);

                echo $bannerBox;
            }

            if($bannerTypeID == 1) {
                echo '</div>';
            }
        }
        ?>
        </div>
    </div>
</div>
