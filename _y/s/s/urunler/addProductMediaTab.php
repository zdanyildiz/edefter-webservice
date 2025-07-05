<?php
/**
 * @var AdminVideo $adminVideo
 */
?>
<!-- ÜRÜN RESİM -->
<div class="row margin-bottom-xxl border-gray">
    <div class="col-lg-12"><h4>Ürün Görselleri - Sürükle Bırak - Tut Sırala</h4></div>
    <div class="col-md-12">
        <div class="card">

                <div class="btn-group" id="imageButtonContainer" data-toggle="buttons">

                    <label class="btn  btn-primary-bright btn-md"
                           href="#offcanvas-imageUpload"
                           id="addImageByLeftCanvas"
                           data-target="imageBox"
                           data-uploadtarget="Product"
                           data-toggle="offcanvas">
                        <i class="fa fa-plus fa-fw"></i>
                        Resim Yükle
                    </label>


                    <label class="btn btn-default-light btn-md"
                           href="#offcanvas-imageSearch"
                           id="addImageByRightCanvas"
                           data-target="imageBox" data-toggle="offcanvas">
                        <i class="fa fa-file-image-o fa-fw"></i>
                        Resim Seç
                    </label>
                </div>

            <div class="card-body" id="imageContainer" data-sortable="true" >
                <?php
                if(!empty($productImages))
                {
                    //echo'<pre>';print_r($productImages);echo'</pre>';
                    //imageName:resim1, imageID:1, imageUrl:klasor1/resim1.jpg; imageName:resim2, imageID:2, imageUrl:klasor2/resim2.jpg; imageName:resim3, imageID:3, imageUrl:klasor3/resim3.jpg
                    $productImages = explode("||", $productImages);

                    //imageName:bağlantı, imageID:9880, imageUrl:Product/baglanti-JKRNZ.jpg;
                    // imageName:baglanti, imageID:10167, imageUrl:Product/baglanti-CUNBP.jpg;
                    // imageName:48,3 ARA BAĞLANTI TAKOZU, imageID:11593, imageUrl:Product/48-3-ara-baglantı-takozu-8NAEU.png

                    foreach($productImages as $productImage)
                    {
                        $productImage = explode("|", $productImage);

                        $imageName = explode(":", $productImage[0]);
                        $imageName = $imageName[1] ?? '';

                        $imageID = explode(":", $productImage[1]);
                        $imageID = $imageID[1] ?? '';

                        $imageUrl = explode(":", $productImage[2]);
                        $imageUrl = $imageUrl[1] ?? '';
                        //herhangi biri boşsa atlayalım
                        if(empty($imageName) || empty($imageID) || empty($imageUrl)) continue;

                        ?>
                        <div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_<?=$imageID?>">
                            <input type="hidden" name="imageID[]" value="<?=$imageID?>">
                            <div class="tile-icond">
                                <img id="image_<?=$imageID?>" class="size-2" src="<?=imgRoot."?imagePath=".$imageUrl?>&width=100&height=100" alt="<?=$imageName?>">
                            </div>
                            <div class="tile-text">
                                <a
                                    class="btn btn-floating-action ink-reaction removeImage"
                                    data-imageBox="imageBox_<?=$imageID?>"
                                    data-id="<?=$imageID?>"
                                    data-toggle="modal"
                                    data-target="#removeImageModal"
                                    title="Kaldır">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <div class="modal fade" id="removeAllImageModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="removeImageModalClose">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">Resmleri Kaldır</h4>
                        </div>
                        <div class="modal-body">
                            <p>Tüm Resimleri kaldırmak istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="removeAllImageButton" data-imagebox="0">Resmleri Kaldır</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
            <div class="modal fade" id="removeImageModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="removeImageModalClose">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">Resmi Kaldır</h4>
                        </div>
                        <div class="modal-body">
                            <p>Resmi kaldırmak istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="removeImageButton" data-imagebox="0">Resmi Kaldır</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <em class="text-caption">Medyaları düzenleyin</em>
            </div>
            <div class="col-sm-6">
                <a href="javascript:void(0)" id="removeAllImages" class="btn ink-reaction btn-flat btn-xs btn-danger" style="float:right;">Tüm Resimleri Kaldır</a>
            </div>
        </div>

    </div>
</div>
<!-- ÜRÜN DOSYA -->
<div class="card margin-bottom-xxl border-gray">
    <div class="col-md-12"><h4>Ürün Dosyaları - Sürükle Bırak - Tut Sırala</h4></div>
    <div class="col-md-12">
        <div class="card">
            <div class="btn-group" id="fileButtonContainer" data-toggle="buttons">

                <label class="btn btn-primary-bright btn-md"
                       href="#offcanvas-fileUpload"
                       id="addFileByLeftCanvas"
                       data-target="fileBox"
                       data-uploadtarget="Product"
                       data-toggle="offcanvas">
                    <i class="fa fa-plus fa-fw"></i>
                    Dosya Yükle
                </label>

                <label class="btn btn-default-light" href="#offcanvas-fileSearch" id="addFileByRightCanvas" data-target="fileBox" data-toggle="offcanvas">
                    <i class="fa fa-file-image-o fa-fw"></i>
                    Dosya Seç
                </label>

            </div>

            <div class="card-body" id="fileContainer" data-sortable="true">
                <?php
                if(!empty($productFiles))
                {
                    //fileName:dosya1, fileID:1, file:dosya1.doc, fileExtension:doc; fileName:dosya2, fileID:2, file:dosya2.pdf, fileExtension:pdf; fileName:dosya3, fileID:3, file:dosya3.xls, fileExtension:xls

                    $productFiles = explode(";", $productFiles);

                    foreach($productFiles as $pageFile)
                    {
                        $pageFile = explode(",", $pageFile);

                        $fileName = explode(":", $pageFile[0]);
                        $fileName = $fileName[1];

                        $fileID = explode(":", $pageFile[1]);
                        $fileID = $fileID[1];

                        $file = explode(":", $pageFile[2]);
                        $file = $file[1];

                        $fileExtension = explode(":", $pageFile[3]);
                        $fileExtension = $fileExtension[1];

                        ?>
                        <div class="col-md-1 text-center fileBox" style="cursor:grab" id="fileBox_<?=$fileID?>">
                            <input type="hidden" name="fileID[]" value="<?=$fileID?>">
                            <div class="tile-icond">
                                <a href="<?=fileRoot."?filePath=".$file?>" target="_blank">
                                    <img id="file_<?=$fileID?>" class="size-2" src="<?=fileRoot."?fileExtension=".$fileExtension?>" alt="<?=$fileName?>">
                                </a>
                            </div>
                            <div class="tile-text">
                                <a
                                    class="btn btn-floating-action ink-reaction removeFile"
                                    data-fileBox="fileBox_<?=$fileID?>"
                                    data-id="<?=$fileID?>"
                                    data-toggle="modal"
                                    data-target="#removeFileModal"
                                    title="Kaldır">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <div class="modal fade" id="removeAllFileModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="removeImageModalClose">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">Dosyaları Kaldır</h4>
                        </div>
                        <div class="modal-body">
                            <p>Tüm Dosyaları kaldırmak istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="removeAllImageButton" data-imagebox="0">Dosyaları Kaldır</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
            <div class="modal fade" id="removeFileModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="removeImageModalClose">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">Dosyayı Kaldır</h4>
                        </div>
                        <div class="modal-body">
                            <p>Dosyayı kaldırmak istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="removeImageButton" data-imagebox="0">Dosyayı Kaldır</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div>
    </div>
</div>
<!--gallery-->
<div class="row">
    <div class="col-lg-3 col-md-4">
        <article class="margin-bottom-xxl">
            <h4>Galeriler</h4>
            <p>Dilerseniz Sayfada görüntülenmek üzere bir galeri seçebilirsiniz </p>
            <p><code>Sayfa için ancak 1 galeri seçebilirsiniz</code></p>
        </article>
    </div>
    <div class="col-lg-offset-1 col-md-8">
        <div class="card">
            <div class="card-head card-head-sm style-primary-bright">
                <header> Sayfaya Resim Galerisi Ekle</span></header>
            </div>
            <div class="card-body" id="galleryContainer">
                <div class="col-lg-4">
                    <label class="radio-inline radio-styled">
                        <input type="radio" name="pageGalleryID" id="noGallery" value="0" checked><span>Yok</span>
                    </label>
                </div>
                <?php
                if(!empty($productGallery)){?>
                    <div class="col-lg-6 selectedGallery" style="padding: 10px 0">
                        <label class="radio-inline radio-styled">
                            <input type="radio" name="pageGalleryID" value="<?=$productGallery[0]['galleryID']?>" checked>
                            <span><?=$productGallery[0]['galleryName']?></span>
                        </label>
                    </div>
                <?php }else{?>
                    <div class="col-lg-6 selectedGallery" style="padding: 10px 0"></div>
                <?php }?>
                <div class="col-lg-12">
                    <input type="text" name="galleryName" value="" id="galleryName" placeholder="Galeri adı yazın" class="form-control">
                </div>
                <div class="galleryResult col-md-12" style="margin-top: 10px"></div>
            </div>
        </div>
    </div>
</div>
<!-- video -->
<div class="row">
    <div class="col-lg-3 col-md-4">
        <article class="margin-bottom-xxl">
            <h4>Videolar</h4>
            <p>Sayfada görüntülenmek üzere videolar seçebilirsiniz </p>
        </article>
    </div>
    <div class="col-lg-offset-1 col-md-8">
        <div class="card">
            <div class="card-head card-head-sm style-primary-bright">
                <header> Sayfaya Video Ekle</span></header>
            </div>
            <div class="card-body" id="videoContainer">
                <?php
                if(!empty($productVideos)){?>
                    <div class="col-lg-12 selectedVideos" style="padding: 10px 0" data-sortable="true">
                        <?php foreach($productVideos as $pageVideo){

                            $video = $adminVideo->getVideoById($pageVideo['videoID']);
                            if(!empty($video)){
                                $video = $video[0];
                                //print_r($video);
                                $videoName = $video['video_name'];
                                $videoID = $video['video_id'];
                            }
                            else{
                                continue;
                            }
                            ?>
                            <div class="col-md-12 checkbox checkbox-styled">
                                <label>
                                    <input type="checkbox" name="pageVideoIDS[]" value="<?=$videoID?>" checked>
                                    <span><?=$videoName?></span>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                <?php }else{?>
                    <div class="col-lg-12 selectedVideos" style="padding: 10px 0" data-sortable="true"></div>
                <?php }?>
                <div class="col-lg-12">
                    <input type="text" name="videoName" value="" id="videoName" placeholder="Video adı yazın" class="form-control">
                </div>
                <div class="videoResult col-md-12" style="margin-top: 10px"></div>
            </div>
        </div>
    </div>
</div>