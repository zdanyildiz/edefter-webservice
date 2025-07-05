<?php
/**
 * @var string $pageSeoLink
 * @var string $pageTitle
 * @var string $pageContent
 * @var array $imageUrls
 * foreach ($categoryPages as $page) {
 * $page = $page['pageDetails'] ?? [];
 * $pageTitle = $page['sayfaad'];
 * $pageContent = $page['sayfaicerik'];
 * $imageUrls = $page['resim_url'];
 * $imageUrls = ($imageUrls) ? explode(",",$imageUrls) : [];
 * $pageSeoLink = $page['seoLink'];
 * include VIEW . "Page/PageBox.php";
 * }
 */

//şuan for döngüsünün içindeyiz. sayfa kutu döndürelim. Başlık, resim, link, linke tıklanınca sayfaya gitsin
?>
<div class="page-box">
    <div class="page-box-content">
        <?php
        if (count($imageUrls) > 0) {
            ?>
            <div class="page-box-image">
                <img src="<?php echo imgRoot."?imagePath=". $imageUrls[0]; ?>&width=250" alt="<?php echo $pageTitle; ?>" title="<?php echo $pageTitle; ?>" />
            </div>
            <?php
        }
        ?>
    </div>
    <div class="page-box-title">
        <h2><?php echo $pageTitle; ?></h2>
    </div>
    <div class="page-box-link">
        <a href="<?php echo $pageSeoLink; ?>"><?=_kategori_devamini_oku_yazi?></a>
    </div>
</div>