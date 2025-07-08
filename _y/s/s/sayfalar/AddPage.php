<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
require_once(ROOT."/App/Helpers/Helper.php");
$helper = new Helper();
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var int $adminAuth
 */

$buttonName = "Sayfa Ekle";

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

$languages = $adminLanguage->getLanguages();

$pageID = $_GET['pageID'] ?? 0;
$pageID = intval($pageID);

include_once MODEL . 'Admin/AdminCategory.php';
$adminCategory = new AdminCategory($db);

include_once MODEL . 'Admin/AdminPage.php';
$adminPage = new AdminPage($db);

include_once MODEL . 'Admin/AdminCategory.php';
$adminCategory = new AdminCategory($db);

include_once MODEL . 'Admin/AdminGallery.php';
$adminGallery = new AdminGallery($db);

include_once MODEL . 'Admin/AdminVideo.php';
$adminVideo = new AdminVideo($db);

$pageTypes = $adminPage->getPageTypes();

$pageTypes = array_filter($pageTypes, function($type) use ($adminAuth){
    return $adminAuth <= $type['pageTypePermission'];
});


$disabled = '';

if($pageID>0){

    $page = $adminPage->getPage($pageID);
    
    if(!empty($page)) {
        
        $pageID = $page['pageID'];
        $pageUniqID = $page['pageUniqID'];
        $pageCreateDate = $page['pageCreateDate'];
        $pageUpdateDate = $page['pageUpdateDate'];
        $pageType = $page['pageType'];
        $pageName = $page['pageName'];
        $pageContent = $page['pageContent'];
        $pageLink = $page['pageLink'];
        $pageOrder = $page['pageOrder'];
        $pageStatus = $page['pageActive'];
        $pageDeleted = $page['pageDeleted'];
        $pageHit = $page['pageHit'];
        $pageImages = $page['pageImages'];
        $pageCategories = $page['pageCategories'];
        $pageCategoryID = $page['pageCategoryID'];
        $pageTypePermission = $page['pageTypePermission'];
        $pageFiles = $page['pageFiles'];
        $pageGallery = $page['pageGallery'];
        $pageVideos = $page['pageVideos'];

        $pageCategory = $adminCategory->getCategory($pageCategoryID);
        if(!empty($pageCategory)) {
            $pageLanguageID = $pageCategory['languageID'];

            $categoryHierarchy = $adminCategory->getCategoryHierarchy($pageCategoryID);
        }
        
        //adminseo
        include_once MODEL . 'Admin/AdminSeo.php';
        $adminSeo = new AdminSeo($db);
        
        $pageSeo = $adminSeo->getSeoByUniqId($pageUniqID);
        
        if(!empty($pageSeo)) {
            $pageSeoTitle = $pageSeo['seoTitle'];
            $pageSeoDescription = $pageSeo['seoDescription'];
            $pageSeoKeywords = $pageSeo['seoKeywords'];
            $pageSeoLink = $pageSeo['seoLink'];
            $pageSeoOriginalLink = $pageSeo['seoOriginalLink'];
            $pageSeoImage = $pageSeo['seoImage'];
        }

    
        $buttonName = "Sayfa Güncelle";

        if($pageTypePermission==0){
            $disabled = '';

            if($adminAuth == 0) $disabled = '';
        }

        $customCssDir = CSS . 'Page/CustomCSS/';
        if (!is_dir($customCssDir)) {
            mkdir($customCssDir, 0755, true);
        }

        $customCssFile = CSS . 'Page/CustomCSS/' . $pageUniqID . '.css';
        if (file_exists($customCssFile)) {
            $customCSS = file_get_contents($customCssFile);
        }
    }
}

$pageUniqID = $pageUniqID ?? "";
$pageType = $pageType ?? 23;
$pageName = $pageName ?? "";
$pageContent = $pageContent ?? "";
$pageLink = $pageLink ?? "";
$pageOrder = $pageOrder ?? 0;
$pageStatus = $pageStatus ?? 1;
$pageDeleted = $pageDeleted ?? 0;
$pageHit = $pageHit ?? 0;
$pageImages = $pageImages ?? "";
$pageCategories = $pageCategories ?? "";
$pageCategoryID = $pageCategoryID ?? 0;
$pageFiles = $pageFiles ?? "";
$pageGallery = $pageGallery ?? "";
$pageVideos = $pageVideos ?? "";
$customCSS = $customCSS ?? "";

$pageLanguageID = $pageLanguageID ?? $languageID;

$categoryHierarchy = $categoryHierarchy ?? [];

$pageSeoTitle = $pageSeoTitle ?? "";
$pageSeoDescription = $pageSeoDescription ?? "";
$pageSeoKeywords = $pageSeoKeywords ?? "";
$pageSeoLink = $pageSeoLink ?? "";
$pageSeoOriginalLink = $pageSeoOriginalLink ?? "";
$pageSeoImage = $pageSeoImage ?? "";

if(!empty($pageGallery)){
    $pageGallery = $adminGallery->getGallery($pageGallery[0]['galleryID']);
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sayfa Ekle Pozitif Eticaret</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
        <!-- END META -->
        <link rel="preload" href="/_y/assets/css/fonts/fontawesome-webfont.woff2?v=4.3.0" as="font" type="font/woff2" crossorigin="anonymous">

        <!-- BEGIN STYLESHEETS -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/multi-select/multi-select.css?1424887857" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/summernote/summernote.min.css">

        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-tagsinput/bootstrap-tagsinput.css?1424887862" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/typeahead/typeahead.css?1424887863" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />
        
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		<div id="base">
            <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">SAYFA EKLE</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<form id="addPageForm" class="form" method="post">
							<input type="hidden" name="pageID" 	id="pageID" value="<?=$pageID?>">
							<input type="hidden" name="pageUniqID" id="pageUniqID" value="<?=$pageUniqID?>">
                            <input type="hidden" name="pageCategoryID" id="pageCategoryID" value="<?=$pageCategoryID?>">
							<div class="card-actionbar">
								<div class="card-actionbar-row">
									<button type="submit" class="btn btn-primary btn-default <?=$disabled?>"><?=$buttonName?></button>
								</div>
							</div>

                            <div class="row">
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl"></article>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-1 col-md-8">
                                        <div class="card">
                                            <div class="card-body">
                                                <select name="languageID" id="languageID" class="form-control">
                                                    <option value="0">Dil Seçin</option>
                                                    <?php foreach($languages as $language){
                                                        $selected = $language['languageID'] == $pageLanguageID ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                    <?php } ?>
                                                    </select>
                                                    <p class="help-block">KATEGORİ LİSTELEME İÇİN DİL SEÇİN!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Sayfa Temel Bilgiler</h4><p></p>
										<p>
											Gireceğiniz sayfa bir Kategoriye ait olmalıdır. Lütfen Kategori seçin!
										</p>
                                        <h4>Sayfa Görünümü</h4>
                                        <p>İlgili sayfanin tipini belirleyin. Görüntülendiğinde sayfa özellikleri ona göre yüklenecek</p>
										<p>Yayın alanından Pasif konumunu seçerseniz sayfa kaydedilir fakat siz aktif edene kadar görüntülenmez.</p>
									</article>
								</div>
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
                                            <div class="row" id="categoryContainer">
                                                <div id="categoryList0" class="categoryList col-sm-6 form-group floating-label">
                                                    <select data-layer="0" class="col-sm-12 form-control">

                                                    </select>
                                                    <p class="help-block">Kategori Seçin</p>
                                                </div>
                                            </div>

                                            <div class="form-group floating-label">
                                                <select id="pageType" name="pageType" class="form-control">
                                                    <?php
                                                    foreach ($pageTypes as $type) {
                                                        $selected = $type['pageTypeID'] == $pageType ? 'selected' : '';
                                                        echo '<option value="'.$type['pageTypeID'].'" '.$selected.'>'.$type['pageTypeName'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <label for="pageType">Sayfa Tipini Seçin</label>
                                            </div>
											<div class="form-group">
												<label class="col-sm-3 control-label">Yayınlansın mı</label>
												<div class="col-sm-9">
													<label class="radio-inline radio-styled">
														<input type="radio" name="pageStatus" value="1" <?php if($pageStatus==1) echo "checked";?>><span>Aktif</span>
													</label>
													<label class="radio-inline radio-styled">
														<input type="radio" name="pageStatus" value="0" <?php if($pageStatus==0) echo "checked";?>><span>Pasif</span>
													</label>	
												</div>
											</div>
										</div>
									</div>
									<em class="text-caption">Temel özellikleri seçin</em>
								</div>
							</div>

                            <div class="row margin-bottom-xxl">
                                <div class="col-lg-12">
                                    <h4>Sayfa İçeriği</h4>
                                </div>
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl">
                                        <p>
                                            Sayfa adı girin!
                                        </p>
                                        <p>Sayfa içeriğini anlaşılır ve akıcı okumaya uygun girin</p>

                                        <!-- Bilgilendirme Modalı için Button-->
                                        <div class="btn-group" id="contentHelpButtonContainer" data-toggle="buttons">
                                            <label
                                                    class="btn btn-primary-bright btn-md"
                                                    data-toggle="modal"
                                                    data-target="#pageContentHelpModal"
                                                    title="Sayfa İçeriği Hakkında Bilgi">
                                                <i class="fa fa-info fa-fw"></i>
                                                Bilgilendirme
                                            </label>
                                        </div>

                                        <div class="btn-group" id="contentCreateButtonContainer" data-toggle="buttons" style="margin-top:30px">
                                            <label
                                                    class="btn btn-primary btn-md"
                                                    data-toggle="modal"
                                                    data-target="#contentCreateModal"
                                                    title="Yapay Zeka ile İçerik Üretin">
                                                <i class="fa fa-connectdevelop fa-fw"></i>
                                                AI İçerik Üretici
                                            </label>
                                        </div>
                                        <div class="modal fade" id="contentCreateModal" tabindex="-1" role="dialog" aria-labelledby="contentCreateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button id="btn-popup-alert-close" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                        <h4 class="modal-title" id="contentCreateModalLabel">Yapay Zeka ile İçerik Üretin</h4>
                                                    </div>
                                                    <div class="modal-body">

                                                        <p><strong>Bilgilendirme sayfası içeriği üretmek için, konunun ne hakkında olduğunu ve sayfa ziyaretçilerine nasıl fayda sağlayacağını açıklayan kısa ve net bilgiler yazınız. Konuyla ilgili anahtar noktaları belirterek, ziyaretçilerin sorularını yanıtlayacak veya ihtiyaç duydukları bilgileri sağlayacak birkaç cümle ile özetleyin</strong></p>
                                                        <div class="card-body">
                                                            <strong>Örnek Metin</strong><br>
                                                            <p><strong>Örnek 1:</strong> "Geri dönüşümün çevreye olan etkileri hakkında bilgi veren bir sayfa hazırlamak istiyorum. Özellikle evde yapılabilecek basit geri dönüşüm uygulamalarını ve çevresel faydalarını öne çıkarmak istiyorum."</p>

                                                            <p><strong>Örnek 2:</strong> "Sağlıklı beslenme için gerekli vitamin ve minerallerin önemi hakkında bilgi veren bir içerik istiyorum. Hangi besinlerin hangi vitaminleri sağladığını ve bunların vücut sağlığına katkılarını açıklayan kısa bir yazı olabilir."</p>

                                                            <p><strong>Örnek 3:</strong> "Güvenli internet kullanımı hakkında bilgilendirici bir sayfa oluşturmak istiyorum. Çocuklar için güvenli internet kullanımını destekleyen ipuçlarını ve ebeveynlerin alabileceği önlemleri açıklayan bir içerik üretmek istiyorum."</p>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <textarea class="form-control" id="contentInf" name="contentInf" rows="3" placeholder="İçerik cümlenizi yazın" style="
                                                                    background-color:#efefef;
                                                                    width:96%;
                                                                    padding: 10px 1% 10px 1%;
                                                                    margin:10px 0 0 0;
                                                                    border:solid 1px #eee"
                                                                ></textarea>
                                                                <label for="contentInf">İçerik Cümlesi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                                                        <button type="button" class="btn btn-primary" id="contentCreateButton">Sayfa İçeriği Üret</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($pageType==1):?>
                                            <p>İletişim sayfası orijinal içerik</p>
                                            <div class="bg-warning" style="padding: 5px 10px">
                                            <p></p>
                                            &lt;div class="contactInfo"&gt;
                                            [firmaad]
                                            [telefon]
                                            [mail]
                                            [whatsapp]
                                            [adres]
                                            &lt;/div&gt;<br>
                                            [iletisimform]<br>

                                            [sosyalmedya]<br>

                                            [firmaharita]
                                            </div>
                                        <?php endif;?>
                                    </article>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        name="pageName"
                                                        id="pageName"
                                                        class="form-control"
                                                        placeholder="SAYFA BAŞLIĞINI GİRİN"
                                                        value="<?=$pageName?>"
                                                        required aria-required="true">
                                                <label for="pageName">Sayfa Adı</label>
                                                <input type="hidden" class="hidden" name="pageOrder" id="pageOrder" value="<?=$pageOrder?>">
                                            </div>
                                            <div class="form-group no-padding">
                                                <textarea
                                                        id="pageContent"
                                                        name="pageContent"
                                                        rows="40"
                                                        style="height: 500px"><?=$pageContent?></textarea>
                                            </div>
                                            <div class="btn-group" id="contentImageButtonContainer" data-toggle="buttons">
                                                <label
                                                        class="btn btn-primary-bright btn-md"
                                                        href="#offcanvas-imageUpload"
                                                        id="uploadImageByLeftCanvas"
                                                        data-target="pageContent"
                                                        data-uploadtarget="Page"
                                                        data-toggle="offcanvas"
                                                        title="Yeni Resim Yükle">
                                                    <i class="fa fa-plus fa-fw"></i>
                                                    İçeriğe Resim Yükle
                                                </label>

                                                <label
                                                        class="btn btn-default-light btn-md"
                                                        href="#offcanvas-imageSearch"
                                                        id="selectImageByRightCanvas"
                                                        data-target="pageContent"
                                                        data-toggle="offcanvas"
                                                        title="Listeden Resim Seç">
                                                    <i class="fa fa-file-image-o fa-fw"></i>
                                                    İçeriğe Resim Seç
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                    <em class="text-caption">Sayfa İçeriği/Açıklama <button class="btn btn-sm" type="button" id="removeDivTagButton">Div etiketi sil</button></em>
                                </div>
                            </div>
                            <!-- ürün başlık, içerik ve görsellerin seo ve kullanıcı deneyimine etkisini açıklayan yardım modalını ekleyelim -->
                            <div class="modal fade" id="pageContentHelpModal" tabindex="-1" role="dialog" aria-labelledby="pageContentHelpModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title" id="pageContentHelpModalLabel">Sayfa İçeriği Hakkında Bilgi</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong># Sayfa Başlığı Nasıl Olmalı?</strong><br>
                                                Sayfa başlığı, içeriğinizin ne olduğunu açıkça belirtmelidir. Anahtar kelimeleri içermeli ve 50-60 karakter arasında olmalıdır. Başlığınız, arama motorlarında ürününüzü kolayca bulunabilir hale getirir ve kullanıcıların dikkatini çeker.
                                                **Etkisi:** Doğru başlıklar, arama motoru sonuçlarında daha üst sıralarda yer almanızı sağlar ve kullanıcıların sayfayı tıklama olasılığını artırır.

                                            </p>
                                            <p><strong># Sayfa İçeriği Nasıl Olmalı?</strong><br>
                                                Sayfa içeriği, kullanıcıların içerik hakkında detaylı bilgi almasını sağlar. Açıklamalar, özgün ve bilgilendirici olmalı, konunun özelliklerini ve faydalarını net bir şekilde belirtmelidir. Anahtar kelimeleri doğal bir şekilde kullanarak SEO'ya uygun hale getirin.
                                                **Etkisi:** Kaliteli ve bilgilendirici içerikler, kullanıcıların sitede kalma süresi kararını etkiler ve arama motorlarında daha iyi sıralamalara sahip olmanızı sağlar.

                                            </p>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<!-- resim dosya video -->
                            <!-- Sayfa RESİM -->
                            <div class="row">
                                <div class="col-lg-3 col-md-4"><h4>Sayfa Görselleri - Sürükle Bırak - Tut Sırala</h4></div>
                                <div class="col-lg-offset-1 col-md-8">

                                    <div class="card">

                                        <div class="btn-group" id="imageButtonContainer" data-toggle="buttons">

                                            <label class="btn  btn-primary-bright btn-md"
                                                   href="#offcanvas-imageUpload"
                                                   id="addImageByLeftCanvas"
                                                   data-target="imageBox"
                                                   data-uploadtarget="Page"
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
                                            if (!empty($pageImages)) {
                                                //echo'<pre>';print_r($pageImages);echo'</pre>';
                                                //imageName:resim1, imageID:1, imageUrl:klasor1/resim1.jpg; imageName:resim2, imageID:2, imageUrl:klasor2/resim2.jpg; imageName:resim3, imageID:3, imageUrl:klasor3/resim3.jpg
                                                $pageImages = explode("||", $pageImages);

                                                foreach ($pageImages as $pageImage) {
                                                    $pageImageParts = explode("|", $pageImage);

                                                    // En az 3 eleman var mı kontrol edelim
                                                    if (count($pageImageParts) < 3) {
                                                        continue; // Eksik bilgi varsa döngüyü atla
                                                    }

                                                    $imageName = explode(":", $pageImageParts[0]);
                                                    $imageName = $imageName[1] ?? '';

                                                    $imageID = explode(":", $pageImageParts[1]);
                                                    $imageID = $imageID[1] ?? '';

                                                    $imageUrl = explode(":", $pageImageParts[2]);
                                                    $imageUrl = $imageUrl[1] ?? '';

                                                    // Herhangi biri boşsa atlayalım
                                                    if (empty($imageName) || empty($imageID) || empty($imageUrl)) {
                                                        continue;
                                                    }

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

                            <!-- Sayfa DOSYA -->
                            <div class="row">
                                <div class="col-lg-3 col-md-4"><h4>Sayfa Dosyaları - Sürükle Bırak - Tut Sırala</h4></div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="btn-group" id="fileButtonContainer" data-toggle="buttons">

                                            <label class="btn btn-primary-bright btn-md"
                                                   href="#offcanvas-fileUpload"
                                                   id="addFileByLeftCanvas"
                                                   data-target="fileBox"
                                                   data-uploadtarget="Page"
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
                                            if(!empty($pageFiles))
                                            {
                                                //fileName:dosya1, fileID:1, file:dosya1.doc, fileExtension:doc; fileName:dosya2, fileID:2, file:dosya2.pdf, fileExtension:pdf; fileName:dosya3, fileID:3, file:dosya3.xls, fileExtension:xls

                                                $pageFiles = explode(";", $pageFiles);

                                                foreach($pageFiles as $pageFile)
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
                                                        <button type="button" class="btn btn-primary" id="removeFileButton" data-imagebox="0">Dosyayı Kaldır</button>
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
                                            <div class="col-lg-6">
                                                <label class="radio-inline radio-styled">
                                                    <input type="radio" name="pageGalleryID" id="noGallery" value="0" checked><span>Yok</span>
                                                </label>
                                            </div>
                                            <?php
                                            if(!empty($pageGallery)){?>
                                                <div class="col-lg-6 selectedGallery" style="padding: 10px 0">
                                                    <label class="radio-inline radio-styled">
                                                        <input type="radio" name="pageGalleryID" value="<?=$pageGallery[0]['galleryID']?>" checked>
                                                        <span><?=$pageGallery[0]['galleryName']?></span>
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

                            <!--video-->
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
                                            if(!empty($pageVideos)){?>
                                                <div class="col-lg-12 selectedVideos" style="padding: 10px 0" data-sortable="true">
                                                <?php foreach($pageVideos as $pageVideo){

                                                    $video = $adminVideo->getVideoById($pageVideo['videoID']);
                                                    if(!empty($video)){
                                                        $video = $video[0];
                                                        $videoName = $video['video_name'];
                                                        $videoID = $pageVideo['videoID'];
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

                            <!-- customCSS -->
                            <div class="row">
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl">
                                        <h4>Ek Stil Kodları</h4>
                                        <p>Sayfaya özel özelleştirmeler yapabilirsiniz</p>
                                    </article>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <textarea name="customCSS" id="customCSS" class="form-control" rows="7" placeholder=""><?=$customCSS?></textarea>
                                                <label for="customCSS">Ek Stil Kodları</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

							<!-- seo -->
							<div class="row">
								<div class="col-lg-3 col-md-4">
									<article class="margin-bottom-xxl">
										<h4>Google Arama SEO Seçenekleri</h4><p></p>
										<p>
											<strong>SEO Başlığı;</strong> Bu sayfayı google'da nasıl ararlar? <code>(En fazla 65 karakter)</code>
										</p>
										<p><strong>SEO Açıklaması;</strong> bu sayfada neyden bahsedilmektedir. Kısa özet <code>(En fazla 200 karakter)</code></p>
										<p><strong>SEO Kelimeler;</strong> sayfa içeriği ile alakalı küçük harfle ve virgül ile ayrılmış kelimeler girin. (marka ürün,renk ürün,cinsiyet ürün,marka cinsiyet.) <code>(En fazla 255 karakter)</code></p>
									</article>
								</div><!--end .col -->
								<div class="col-lg-offset-1 col-md-8">
									<div class="card">
										<div class="card-body">
                                            <div class="form-group">
                                                <textarea name="pageLink" id="pageLink" class="form-control" rows="1" placeholder="/"><?=$pageLink?></textarea>
                                                <label for="pageLink">Sayfa Bağlantısı</label>
                                            </div>
											<div class="form-group">
												<input 
													type="text" 
													name="pageSeoTitle" 
													id="pageSeoTitle" 
													class="form-control" 
													placeholder="xxx nedir | xxx neden olur | xxx'in belirtileri" 
													value="<?=$pageSeoTitle?>" 
													data-rule-minlength="5"
													maxlength="100"
													aria-invalid="false"
													required aria-required="true">
												<label for="pageSeoTitle">SEO Başlık</label>
											</div>
											<div class="form-group">
												<textarea 
													id="pageSeoDescription" 
													name="pageSeoDescription" 
													placeholder="xxx nedenleri, belirtileri ve çözümü hakkındaki tüm bilgilere bu sayfadan ulaşabilirsiniz"
													class="form-control"  
													rows="3"
													data-rule-minlength="25"
													maxlength="200"
													aria-invalid="false"
													required aria-required="true"><?=$pageSeoDescription?></textarea>
													<label for="pageSeoDescription">SEO Açıklama</label>
											</div>
											<div class="form-group">
												<textarea 
													id="pageSeoKeywords" 
													name="pageSeoKeywords"
													class="form-control" 
													placeholder="xxx nedir,xxx neden olur,xxx belirtileri" 
													rows="2"
													data-rule-minlength="6"
													maxlength="255"
													aria-invalid="false"
													required aria-required="true"><?=$pageSeoKeywords?></textarea>
													<label for="pageSeoKeywords">SEO Kelimeler</label>
											</div>
                                            <div class="form-group">
                                                <button id="createSeo" type="button" class="btn btn-primary-bright btn-sm">AI Seo Oluşturucu</button>
                                            </div>
										</div>
									</div>
									<em class="text-caption">Sayfa İçeriği/Açıklama</em>
                                </div>
							</div>
							<div class="card-actionbar">
								<div class="card-actionbar-row">
                                    <div class="checkbox checkbox-styled">
                                        <label>
                                            <input type="checkbox" name="saveAndAdd" id="saveAndAdd" value="1"><span>Kaydet ve Eklemeye devam et</span>
                                        </label>
                                    </div>
									<button type="submit" class="btn btn-primary btn-default <?=$disabled?>"><?=$buttonName?></button>
								</div>
							</div>
						</form>
					</div>
				</section>
			</div>

			<?php require_once(ROOT."/_y/s/b/menu.php");?>
            
            <?php require_once(ROOT."/_y/s/b/rightCanvas.php");?>

            <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="card">
                        <div class="card-head card-head-sm style-danger">
                            <header class="modal-title" id="alertModalLabel">Uyarı</header>
                            <div class="tools">
                                <div class="btn-group">
                                    <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                        <i class="fa fa-close"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p id="alertMessage"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>
		</div>

        <style>
            #imageContainer,#fileContainer{
                min-width: 100%;
                display: flex;
                flex-wrap: wrap;
                align-content: center; justify-content: flex-start;align-items: flex-start; gap: 10px;
            }
            .imageBox,.filebox {
                box-sizing: border-box;
                box-shadow: 0 0 0 1px #ccc;
                padding: 5px; min-width: 100px;
            }
            .imageBox img, .fileBox img {
                -webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.33);
                -webkit-transition: -webkit-box-shadow 0.15s ease-out;
                -moz-transition: -moz-box-shadow 0.15s ease-out;
                -o-transition: -o-box-shadow 0.15s ease-out;
                transition: box-shadow 0.15s ease-out;
                margin-bottom: 5px;
            }
        </style>

        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/summernote/summernote.min.js"></script>
        <script src="/_y/assets/js/libs/select2/select2.js"></script>
        <script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>

        <script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
        <script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
		<!-- END JAVASCRIPT -->
		<script>
			$("#addPagephp").addClass("active");

            const alertModal = $("#alertModal");
            const alertMessage = $("#alertMessage");

            let imgRoot = "<?=imgRoot?>";
            let fileRoot = "<?=fileRoot?>";

            let categoryHierarchy = '<?=!empty($categoryHierarchy) ? json_encode($categoryHierarchy) : '[]'?>';
            categoryHierarchy = JSON.parse(categoryHierarchy);
            let categoryHierarchyLength = categoryHierarchy.length;
            $("#pageContent").summernote({
                tabsize: 2,
                height: 400,
                minHeight: 400,
                callbacks: {
                    onChange: function(contents, $editable) {
                        // Boş HTML etiketlerini temizle
                        let cleanContent = contents.trim();
                        
                        // Eğer sadece boş HTML etiketleri varsa içeriği tamamen temizle
                        if (cleanContent === '' || cleanContent === '<p></p>' || cleanContent === '<br>' || cleanContent === '<p><br></p>' || cleanContent === '&nbsp;') {
                            // Infinite loop'u önlemek için timeout kullan
                            setTimeout(() => {
                                if ($(this).summernote('code').trim() !== '') {
                                    $(this).summernote('code', '');
                                }
                            }, 100);
                        }
                    },
                    onBlur: function() {
                        // Focus kaybında da temizlik yap
                        let content = $(this).summernote('code');
                        let cleanContent = content.trim();
                        
                        if (cleanContent === '' || cleanContent === '<p></p>' || cleanContent === '<br>' || cleanContent === '<p><br></p>' || cleanContent === '&nbsp;') {
                            $(this).summernote('code', '');
                        }
                    }
                }
            });

            // Summernote içeriğini temizleyen yardımcı fonksiyon
            function cleanSummernoteContent(content) {
                if (!content) return '';
                
                let cleanContent = content.trim();
                
                // Sadece boş HTML etiketleri varsa empty string döndür
                if (cleanContent === '' || cleanContent === '<p></p>' || cleanContent === '<br>' || cleanContent === '<p><br></p>' || cleanContent === '&nbsp;') {
                    return '';
                }
                
                return cleanContent;
            }

            function createSeoLink(title, languageCode) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '/App/Controller/Admin/AdminPageController.php',
                        type: 'POST',
                        data: {
                            action: 'createSeoLink',
                            title: title,
                            languageCode: languageCode
                        },
                        success: function(response) {
                            const data = JSON.parse(response);
                            if (data.status === 'success') {
                                resolve(data.seoLink);
                            } else {
                                reject(data.message);
                            }
                        },
                        error: function(error) {
                            reject('SEO link oluşturulurken bir hata oluştu.');
                        }
                    });
                });
            }

            async function changeLanguageID() {
                const languageID = $('#languageID').val();
                const action = "getCategories";
                const categoryContainer = $('#categoryContainer select');
                const modal = $('#alertModal');
                const categoryList = $('#categoryList0 select');

                categoryContainer.empty();

                if (languageID > 0) {
                    try {
                        const response = await $.ajax({
                            url: '/App/Controller/Admin/AdminCategoryController.php',
                            type: 'POST',
                            data: {
                                languageID: languageID,
                                action: action
                            }
                        });

                        const { status, message, categories: categories } = JSON.parse(response);

                        if (status === "success") {
                            $("#pageCategoryID").val(0);
                            categoryList.empty();
                            categoryList.append('<option value="0" selected>Kategori Seçin</option>');
                            categories.forEach(({ categoryID, categoryName }) => {
                                categoryList.append(`<option value="${categoryID}">${categoryName}</option>`);
                            });
                            console.log("Dil değişikliği başarılı, kategoriler yüklendi");
                            //url'de categoryID varsa kategoriyi seçelim
                            const urlParams = new URLSearchParams(window.location.search);
                            const categoryID = urlParams.get('categoryID');
                            if (categoryID) {
                                categoryList.val(categoryID);
                                //kategori değişikliği olayını tetikleyelim
                                categoryList.trigger('change');
                            }
                            const pageType = urlParams.get('pageType');
                            if (pageType) {
                                $("#pageType").val(pageType);
                            }
                        } else {
                            modal.find('#alertMessage').text(message);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                }
            }

            async function loadCategories(selectedElement) {
                const layer = selectedElement.data('layer');
                const categoryID = selectedElement.val();
                const action = "getSubCategories";

                if (categoryID > 0) {
                    try {
                        const response = await $.ajax({
                            url: '/App/Controller/Admin/AdminCategoryController.php',
                            type: 'POST',
                            data: {
                                categoryID: categoryID,
                                action: action
                            }
                        });
                        console.log(response);
                        const data = JSON.parse(response);
                        const { status, subCategories } = data;

                        if (status === "success") {
                            const categoryContainer = $('#categoryContainer');
                            const newLayer = layer + 1;
                            const categoryListId = 'categoryList' + newLayer;
                            const categoryListSelector = '#' + categoryListId;

                            if (!$(categoryListSelector).length) {
                                const newCategoryList = `
                                <div id="${categoryListId}" class="categoryList col-sm-6 form-group floating-label">
                                    <select data-layer="${newLayer}" class="col-sm-12 form-control"></select>
                                    <p class="help-block">Alt kategori Seçin</p>
                                </div>`;
                                categoryContainer.append(newCategoryList);
                            }

                            const categoryList = $(categoryListSelector + ' select');
                            categoryList.empty();
                            categoryList.append('<option value="0" selected>Alt Kategori Seçin</option>');
                            $.each(subCategories, function (index, category) {
                                categoryList.append(`<option value="${category.categoryID}">${category.categoryName}</option>`);
                            });

                        } else {
                            $(`#categoryList${layer + 1}`).remove();
                        }
                    } catch (error) {
                        console.error(`Error loading categories: ${error.message}`);
                    }
                }
            }

            async function selectCategory(i) {
                console.log("Kategori seçiliyor: " + categoryHierarchy[i]);
                const categoryID = categoryHierarchy[i].categoryID;
                console.log("Kategori seçiliyor: " + categoryID);

                const categoryListSelector = `#categoryList${i} select`;
                const selectedElement = $(categoryListSelector);

                $("#pageCategoryID").val(categoryID);
                selectedElement.val(categoryID);

                await loadCategories(selectedElement);
            }

            async function selectCategories() {
                const categoryHierarchyLength = categoryHierarchy.length;
                for(let i = 0; i < categoryHierarchyLength; i++){
                    console.log("Kategori seçiliyor: " + `#categoryList${i} select`);
                    await selectCategory(i);
                }
                console.log("Kategoriler seçildi");
            }

            async function setLanguageWithProduct() {

                changeLanguageID().then(() => {
                    selectCategories();
                });
            }

            $(document).ready(function(){

                $(document).on('change', '#categoryContainer select', async function () {
                    let selectedElement = $(this);
                    await loadCategories(selectedElement);

                    let selectedCategoryID = 0;
                    $('#categoryContainer select').each(function () {
                        let categoryID = $(this).val();
                        if (categoryID > 0) {
                            selectedCategoryID = categoryID;
                        }
                    });
                    $('#pageCategoryID').val(selectedCategoryID);
                });

                //resim arama #imageName klavyeden 3 harf yazılırsa arama başlatalım
                $(document).on('keyup', '#searchImageName', function () {
                    $imageName = $(this).val();
                    if ($imageName.length > 2) {
                        $.ajax({
                            type: 'GET',
                            url: "/App/Controller/Admin/AdminImageController.php?action=getImagesBySearch&searchText=" + $imageName,
                            dataType: 'json',
                            success: function (data) {
                                $data = data;
                                if ($data.status === "success") {
                                    $html = "";
                                    for ($i = 0; $i < $data.images.length; $i++) {
                                        $imageID = $data.images[$i].imageID;
                                        $imagePath = $data.images[$i].imagePath;
                                        $imageName = $data.images[$i].imageName;
                                        $imageWidth = $data.images[$i].imageWidth;
                                        $imageHeight = $data.images[$i].imageHeight;
                                        $imageFolderName = $data.images[$i].imageFolderName;

                                        $html += '<li class="tile">' +
                                            '<a class="tile-content ink-reaction selectImage"' +
                                            'data-imageid="' + $imageID + '"' +
                                            'data-imagepath="' + $imageFolderName + '/' + $imagePath + '"' +
                                            'data-imagename="' + $imageName + '"' +
                                            'data-imagewidth="' + $imageWidth + '"' +
                                            'data-imageheight="' + $imageHeight + '"' +
                                            'data-backdrop="false" style="cursor:pointer;">' +
                                            '<div class="tile-icon">' +
                                            '<img src="' + imgRoot + '?imagePath=' + $imageFolderName + '/' + $imagePath + '&width=100&height=100" alt="" />' +
                                            '</div>' +
                                            '<div class="tile-text">' +
                                            $imageName +
                                            '<small>' + $imageFolderName + '</small>' +
                                            '</div>' +
                                            '</a>' +
                                            '</li>';

                                    }
                                    $("#rightImageListContainer").html($html);
                                }
                            }
                        });
                    }
                });

                //dosya arama #fileName klavyeden 3 harf yazılırsa arama başlatalım
                $(document).on('keyup', '#searchFileName', function () {
                    $fileName = $(this).val();
                    if ($fileName.length > 2) {
                        $.ajax({
                            type: 'GET',
                            url: "/App/Controller/Admin/AdminFileController.php?action=getFilesBySearch&searchText=" + $fileName,
                            dataType: 'json',
                            success: function (data) {
                                console.log(data);
                                $data = data;
                                if ($data.status === "success") {
                                    $html = "";
                                    for ($i = 0; $i < $data.files.length; $i++) {
                                        $fileID = $data.files[$i].fileID;
                                        $filePath = $data.files[$i].filePath;
                                        $fileName = $data.files[$i].fileName;
                                        $fileExtension = $data.files[$i].fileExtension;
                                        $fileFolderName = $data.files[$i].fileFolderName;
                                        $fileImage = fileRoot + "?fileExtension=" + $fileExtension + ".png";

                                        $html += '<li class="tile">' +
                                            '<a class="tile-content ink-reaction selectFile"' +
                                            'data-fileid="' + $fileID + '"' +
                                            'data-filepath="' + $filePath + '"' +
                                            'data-filename="' + $fileName + '"' +
                                            'data-fileextension="' + $fileExtension + '"' +
                                            'data-backdrop="false" style="cursor:pointer;">' +
                                            '<div class="tile-icon">' +
                                            '<img src="' + $fileImage + '.png" alt="' + $fileName + '" />' +
                                            '</div>' +
                                            '<div class="tile-text">' +
                                            $fileName +
                                            '<small>' + $fileExtension + '</small>' +
                                            '</div>' +
                                            '</a>' +
                                            '</li>';

                                    }
                                    $("#rightFileListContainer").html($html);
                                }
                            }
                        });
                    }
                });

                //#selectImageByRightCanvas tıklandığında data-target değerini alıp #imageTarget'a atayalım
                $(document).on("click", "#selectImageByRightCanvas, #addImageByRightCanvas", function () {
                    $imageTarget = $(this).data("target");

                    $("#imageTarget").val($imageTarget);
                });

                //#uploadImageByLeftCanvas tıklandığında data-uploadtarget değerini alıp #imageFolder'a atayalım
                $(document).on("click", "#uploadImageByLeftCanvas, #addImageByLeftCanvas", function () {
                    $imageTarget = $(this).data("target");

                    $("#imageTarget").val($imageTarget);

                    $uploadTarget = $(this).data("uploadtarget");

                    $("#imageFolder").val($uploadTarget);
                });

                //imageBox
                const $imageBox = '<div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_[imageID]">' +
                    '<input type="hidden" name="imageID[]" value="[imageID]">' +
                    '<div class="tile-icond">' +
                    '<img id="image_[imageID]" class="size-2" src="' + imgRoot + '?imagePath=[imagePath]&width=100&height=100" alt="[imageName]">' +
                    '</div>' +
                    '<div class="tile-text">' +
                    '<a class="btn btn-floating-action ink-reaction removeImage" data-imageBox="imageBox_[imageID]" data-id="[imageID]" data-toggle="modal" data-target="#removeImageModal" title="Kaldır">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>' +
                    '</div>' +
                    '</div>';

                $(document).on("click", ".selectImage", function () {

                    $imageTarget = $("#imageTarget").val();

                    $imageID = $(this).data("imageid");
                    $imagePath = $(this).data("imagepath");
                    $imageName = $(this).data("imagename");
                    $imageWidth = $(this).data("imagewidth");
                    $imageHeight = $(this).data("imageheight");

                    if ($imageTarget === "pageContent") {

                        //genişliğe göre yükseklik ayarlayalım
                        $imageNewWidth = 300;
                        $imageNewHeight = Math.round($imageHeight / $imageWidth * $imageNewWidth);

                        // Summernote'taki mevcut içeriği alın
                        let summernote = $("#pageContent").summernote();
                        let editorData = summernote.code();
                        console.log(editorData);

                        // Yeni resim HTML'sini oluşturun
                        let newImageHtml = '<img src="' + imgRoot + '?imagePath=' + $imagePath + '&width=' + $imageNewWidth + '&height=' + $imageNewHeight + '" title="' + $imageName + '" width="' + $imageNewWidth + '" height="' + $imageNewHeight + '" >';

                        // Mevcut içeriğe yeni resmi ekleyin
                        summernote.code( editorData + newImageHtml);
                        console.log(newImageHtml);
                    } else {

                        $html = $imageBox;
                        $html = $html.replaceAll("[imageID]", $imageID);
                        $html = $html.replaceAll("[imagePath]", $imagePath);
                        $html = $html.replaceAll("[imageName]", $imageName);

                        $("#imageContainer").append($html);
                    }
                });

                Dropzone.options.imageDropzone = {
                    parallelUploads: 10,
                    autoProcessQueue: true,
                    addRemoveLinks: true,
                    maxFiles: 10,
                    maxFilesize: 150,
                    dictDefaultMessage: "Resimleri yüklemek için bırakın",
                    dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
                    dictFallbackText: "Resimleri eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın.",
                    dictFileTooBig: "Resim çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
                    dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
                    dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
                    dictCancelUpload: "İptal Et",
                    dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
                    dictRemoveFile: "Resim Sil",
                    dictRemoveFileConfirmation: null,
                    dictMaxFilesExceeded: "Daha fazla resim yükleyemezsiniz.",
                    acceptedFiles: ".jpeg,.jpg,.png,.webp",
                    //resimler adı imageName inputu boşsa yükleme yapmayalım
                    accept: function (file, done) {

                        var imageName = $("#imageName").val();

                        if (imageName === "") {

                            $("#runImageDropzoneContainer").removeClass("hidden");
                            $("#imageName").parent().addClass("bg-danger");

                        } else {

                            $("#formImageName").val(imageName);
                            done();
                        }

                        $("#runImageDropzone").on("click", function (e) {

                            var imageName = $("#imageName").val();
                            if (imageName === "") {

                                $("#imageName").focus();

                            } else {

                                $("#formImageName").val(imageName);

                                done();
                            }
                        });


                    },
                    removedfile: function (file) {
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
                    init: function () {

                        this.on("success", function (file, responseText) {

                            //console.log(responseText);

                            var responseObject = JSON.parse(responseText);

                            $status = responseObject.status;
                            //console.log("status:"+$status);

                            if ($status === "success") {
                                //resim bilgileri imageResults içinde dönüyor, birden fazla olabilir
                                $imageResults = responseObject.imageResults;
                                //console.log($imageResults);

                                $imageTarget = $("#imageTarget").val();

                                for ($i = 0; $i < $imageResults.length; $i++) {
                                    $imageID = $imageResults[$i].imageData.imageID;
                                    $imagePath = $imageResults[$i].imageData.imageFolderName + "/" + $imageResults[$i].imageData.imagePath;
                                    $imageName = $imageResults[$i].imageData.imageName;
                                    $imageWidth = $imageResults[$i].imageData.imageWidth;
                                    $imageHeight = $imageResults[$i].imageData.imageHeight;

                                    if ($imageTarget === "pageContent") {

                                        //genişliğe göre yükseklik ayarlayalım
                                        $imageNewWidth = 300;
                                        $imageNewHeight = Math.round($imageHeight / $imageWidth * $imageNewWidth);

                                        // Summernote'taki mevcut içeriği alın
                                        let summernote = $('#pageContent').summernote();
                                        let editorData = summernote.code();

                                        // Yeni resim HTML'sini oluşturun
                                        let newImageHtml = '<img src="' + imgRoot + '?imagePath=' + $imagePath + '&width=' + $imageNewWidth + '&height=' + $imageNewHeight + '" title="' + $imageName + '" width="' + $imageNewWidth + '" height="' + $imageNewHeight + '" >';

                                        // Mevcut içeriğe yeni resmi ekleyin
                                        summernote.code(editorData + newImageHtml);
                                    } else {

                                        $html = $imageBox;
                                        $html = $html.replaceAll("[imageID]", $imageID);
                                        $html = $html.replaceAll("[imagePath]", $imagePath);
                                        $html = $html.replaceAll("[imageName]", $imageName);

                                        $("#imageContainer").append($html);
                                    }
                                }

                                //dropzone'a eklenen resimleri silelim
                                this.removeAllFiles();
                                //offcanvas kapat
                                $("#offcanvas-imageUploadOff").click();
                            } else {
                                //hata mesajını burada işleyebilirsiniz
                                console.log(responseText);
                            }

                        });
                        this.on("error", function (file, responseText) {
                            // Hata mesajını burada işleyebilirsiniz
                            console.log(responseText);
                        });
                    }
                };

                //.removeImage linkini dinleyelim
                $(document).on("click", ".removeImage", function () {
                    var targetImageBox = $(this).data("imagebox");
                    console.log("remove target: " + targetImageBox);

                    // removeImageButton tıklanınca targetImageBox'ı silelim
                    $(document).on('click', '#removeImageButton', function () {
                        console.log("remove: " + targetImageBox);
                        $("#" + targetImageBox).remove();
                        $("#removeImageModal").modal("hide");
                    });
                });

                //#removeAllImages tıklanınca tüm resimleri silelim
                $(document).on("click", "#removeAllImages", function () {
                    $("#removeAllImageModal").modal("show");
                });

                //#removeAllImageButton tıklanınca tüm resimleri silelim
                $(document).on("click", "#removeAllImageButton", function () {
                    $(".imageBox").remove();
                    $("#removeAllImageModal").modal("hide");
                });

                //fileBox
                $fileBox = '<div class="col-md-1 text-center fileBox" style="cursor:grab" id="fileBox_[fileID]">' +
                    '<input type="hidden" name="fileID[]" value="[fileID]">' +
                    '<div class="tile-icond">' +
                    '<img id="file_[fileID]" class="size-2" src="[fileImage]" alt="[fileName]">' +
                    '</div>' +
                    '<div class="tile-text"> [fileName] ' +
                    '<a class="btn btn-floating-action ink-reaction removeFile" data-fileBox="fileBox_[fileID]" data-id="[fileID]" data-toggle="modal" data-target="#removeFileModal" title="Kaldır">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>' +
                    '</div>' +
                    '</div>';

                //#selectImageByRightCanvas tıklandığında data-target değerini alıp #imageTarget'a atayalım
                $(document).on("click", "#selectFileByRightCanvas, #addFileByRightCanvas", function () {
                    $fileTarget = $(this).data("target");

                    $("#fileTarget").val($fileTarget);
                });

                $(document).on("click", "#uploadFileByLeftCanvas, #addFileByLeftCanvas", function () {
                    $fileTarget = $(this).data("target");

                    $("#fileTarget").val($fileTarget);

                    $uploadTarget = $(this).data("uploadtarget");

                    $("#fileFolder").val($uploadTarget);
                });

                $(document).on("click", ".selectFile", function () {

                    $fileTarget = $("#fileTarget").val();

                    $fileID = $(this).data("fileid");
                    $filePath = $(this).data("filepath");
                    $fileName = $(this).data("filename");
                    $fileExtension = $(this).data("fileextension");
                    $fileImage = fileRoot + '?fileExtension=' + $fileExtension;

                    if ($fileTarget === "pageContent") {

                        //dosyayı uzantısına göre görsele çevirelim ve bağlantı oluşturalım
                        $fileHtml = '<a href="' + $fileImage + '" class="fileLink" target="_blank">' +
                            '<img src="' + $fileImage + '" alt="' + $fileName + '" title="' + $fileName + '">' +
                            '</a>';

                        let summernote = $('#pageContent').summernote();
                        let editorData = summernote.code();
                        summernote.code(editorData + $fileHtml);
                    } else {

                        $html = $fileBox;
                        $html = $html.replaceAll("[fileID]", $fileID);
                        $html = $html.replaceAll("[filePath]", $filePath);
                        $html = $html.replaceAll("[fileName]", $fileName);
                        $html = $html.replaceAll("[fileExtension]", $fileExtension);
                        $html = $html.replaceAll("[fileImage]", $fileImage);

                        $("#fileContainer").append($html);
                    }
                });

                Dropzone.options.fileDropzone = {
                    parallelUploads: 10,
                    autoProcessQueue: true,
                    addRemoveLinks: true,
                    maxFiles: 10,
                    maxFilesize: 150,
                    dictDefaultMessage: "Dosyaları yüklemek için bırakın",
                    dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
                    dictFallbackText: "Dosyaları eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın.",
                    dictFileTooBig: "Dosya çok büyük ({{filesize}}MiB). Maksimum dosya boyutu: {{maxFilesize}}MiB.",
                    dictInvalidFileType: "Bu tür dosyalar yükleyemezsiniz.",
                    dictResponseError: "Sunucu {{statusCode}} koduyla yanıt verdi.",
                    dictCancelUpload: "İptal Et",
                    dictCancelUploadConfirmation: "Bu yüklemeyi iptal etmek istediğinizden emin misiniz?",
                    dictRemoveFile: "Dosya Sil",
                    dictRemoveFileConfirmation: null,
                    dictMaxFilesExceeded: "Daha fazla dosya yükleyemezsiniz.",
                    acceptedFiles: ".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.csv,.xml,.excel,.odf,.odp",
                    //dosyalar adı fileName inputu boşsa yükleme yapmayalım
                    accept: function (file, done) {

                        var fileName = $("#fileName").val();

                        if (fileName === "") {

                            $("#runFileDropzoneContainer").removeClass("hidden");
                            $("#fileName").parent().addClass("bg-danger");
                            //done("Dosya adını giriniz");
                        } else {

                            $("#formFileName").val(fileName);
                            done();
                        }

                        $("#runFileDropzone").on("click", function (e) {

                            var fileName = $("#fileName").val()

                            if (fileName === "") {

                                $("#fileName").focus();

                                console.log("Dosya adını giriniz");
                            } else {

                                $("#formFileName").val(fileName);
                                console.log("Dosya adı girildi");
                                done();
                            }
                        });


                    },
                    removedfile: function (file) {
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
                    init: function () {

                        this.on("success", function (file, responseText) {

                            //console.log(responseText);

                            var responseObject = JSON.parse(responseText);

                            $status = responseObject.status;
                            //console.log("status:"+$status);

                            if ($status === "success") {
                                //dosya bilgileri fileResults içinde dönüyor, birden fazla olabilir
                                $fileResults = responseObject.fileResults;
                                //console.log($fileResults);

                                $fileTarget = $("#fileTarget").val();

                                for ($i = 0; $i < $fileResults.length; $i++) {
                                    $fileID = $fileResults[$i].fileData.fileID;
                                    $fileName = $fileResults[$i].fileData.fileName;
                                    $fileExtension = $fileResults[$i].fileData.fileExtension;
                                    $fileFolderName = $fileResults[$i].fileData.fileFolderName;
                                    $fileImage = fileRoot + "?fileExtension=" + $fileExtension;
                                    $filePath = $fileFolderName + "/" + $fileResults[$i].fileData.filePath;


                                    if ($fileTarget === "pageContent") {

                                        //dosyayı uzantısına göre görsele çevirelim ve bağlantı oluşturalım
                                        $fileHtml = '<a href="' + fileRoot + $filePath + '" class="fileLink" target="_blank">' +
                                            '<img src="' + $fileImage + '" alt="' + $fileName + '" title="' + $fileName + '">' +
                                            '</a>';

                                        let summernote = $('#pageContent').summernote();
                                        let editorData = summernote.code();
                                        summernote.code( editorData + $fileHtml);
                                    } else {

                                        $html = $fileBox;
                                        $html = $html.replaceAll("[fileID]", $fileID);
                                        $html = $html.replaceAll("[filePath]", $filePath);
                                        $html = $html.replaceAll("[fileName]", $fileName);
                                        $html = $html.replaceAll("[fileExtension]", $fileExtension);
                                        $html = $html.replaceAll("[fileImage]", $fileImage);

                                        $("#fileContainer").append($html);
                                    }
                                }

                                //dropzone'a eklenen dosyaları silelim
                                this.removeAllFiles();
                                //offcanvas kapat
                                $("#offcanvas-fileUploadOff").click();
                            } else {
                                //hata mesajını burada işleyebilirsiniz
                                console.log(responseText);
                                $("#alertMessage").text(responseText);
                                $("#alertModal").modal("show");
                            }

                        });
                        this.on("error", function (file, responseText) {
                            // Hata mesajını burada işleyebilirsiniz
                            console.log(responseText);
                            $("#alertMessage").text(responseText);
                            $("#alertModal").modal("show");
                        });
                    }
                };

                $(document).on("click", ".removeFile", function () {
                    var targetFileBox = $(this).data("filebox");
                    console.log("remove target: " + targetFileBox);

                    // removeImageButton tıklanınca targetImageBox'ı silelim
                    $(document).on('click', '#removeFileButton', function () {
                        console.log("remove: " + targetFileBox);
                        $("#" + targetFileBox).remove();
                        $("#removeFileModal").modal("hide");
                    });
                });

                $(document).on("keyup", "#pageName", function () {
                    let pageName = $(this).val();
                    $("#pageSeoTitle").val(pageName);
                    $("#imageName").val(pageName);

                    let languageCode = $('#languageID option:selected').data('languagecode');

                    createSeoLink(pageName, languageCode)
                        .then(seoLink => {
                            $("#pageLink").val("/" + seoLink);
                        })
                        .catch(error => {
                            console.error(error);
                        });
                });

                //$('#languageID').val(<?="$languageID"?>);
                setLanguageWithProduct();

                //dil değiştiğinde kategorileri yükle
                $(document).on('change', '#languageID', function () {
                    changeLanguageID();
                });                
                $(document).on("submit", "#addPageForm", function(e){
                    
                    e.preventDefault();
                    //form öğelerini konsol'a yazdıralım
                    //console.log("Form Array:", $("#addPageForm").serializeArray());

                    $(".videoResult").html("");
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    //lütfen bekleyiniz yazıp modalı gösterelim
                    $("#alertMessage").text("Lütfen bekleyiniz");
                    $("#alertModal").modal("show");

                    let summernote = $("#pageContent").summernote();
                    let pageContent = summernote.code();
                    
                    // Boş HTML etiketlerini temizle
                    pageContent = pageContent.trim();
                    
                    // Tamamen boşsa empty string yap
                    if (pageContent === '<p></p>' || pageContent === '<br>' || pageContent === '<p><br></p>' || pageContent === '&nbsp;') {
                        pageContent = '';
                    }
                    
                    $("#pageContent").val(pageContent);

                    //kategori 0 olamaz
                    let pageCategoryID = $("#pageCategoryID").val();
                    if (pageCategoryID === "0") {
                        $("#alertMessage").text("Kategori seçin");
                        $("#alertModal").modal("show");
                        return;
                    }

                    $("#pageName").on("blur", function () {
                        let pageName = $(this).val();
                        if (pageName === "") {
                            $("#alertMessage").text("Sayfa adı boş olamaz");
                            $("#alertModal").modal("show");
                        } else {
                            $(this).parent().removeClass("bg-danger");
                        }
                    });
                    //page-link boş olamaz
                    $("#pageLink").on("blur", function () {
                        let pageLink = $(this).val();
                        if (pageLink === "") {
                            $("#alertMessage").text("Sayfa linki boş olamaz");
                            $("#alertModal").modal("show");
                        } else {
                            $(this).parent().removeClass("bg-danger");
                        }
                    });

                    //seo alanları boş olamaz
                    $("#pageSeoTitle").on("blur", function () {
                        let pageSeoTitle = $(this).val();
                        if (pageSeoTitle === "") {
                            $("#alertMessage").text("SEO Başlık boş olamaz");
                            $("#alertModal").modal("show");
                        } else {
                            $(this).parent().removeClass("bg-danger");
                        }
                    });

                    $("#pageSeoDescription").on("blur", function () {
                        let pageSeoDescription = $(this).val();
                        if (pageSeoDescription === "") {
                            $("#alertMessage").text("SEO Açıklama boş olamaz");
                            $("#alertModal").modal("show");
                        } else {
                            $(this).parent().removeClass("bg-danger");
                        }
                    });

                    $("#pageSeoKeywords").on("blur", function () {
                        let pageSeoKeywords = $(this).val();
                        if (pageSeoKeywords === "") {
                            $("#alertMessage").text("SEO Kelimeler boş olamaz");
                            $("#alertModal").modal("show");
                        } else {
                            $(this).parent().removeClass("bg-danger");
                        }
                    });

                    let action = "addPage";

                    let pageID = $("#pageID").val();

                    if (pageID > 0) {
                        action = "updatePage";
                    }

                    

                    //console.log(action);
                    $.ajax({
                        type: 'POST',
                        url: '/App/Controller/Admin/AdminPageController.php',
                        data: $("#addPageForm").serialize() + "&action=" + action,
                        success: function (data) {
                            console.log(data);
                            data = JSON.parse(data);
                            if (data.status === "success") {
                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                $("#alertMessage").text(data.message);
                                $("#alertModal").modal("show");
                                //1,5 san. sonra sayfayı yönlendirelim
                                setTimeout(function () {
                                    //#saveAndAdd seçiliyse sayfayı yenilemeyelim
                                    if ($("#saveAndAdd").is(":checked")) {
                                        window.location.href = "/_y/s/s/sayfalar/AddPage.php?languageID=" + $("#languageID").val() + "&categoryID=" + $("#pageCategoryID").val() +"&pageType=" + $("#pageType").val();
                                    } else {
                                        window.location.href = "/_y/s/s/sayfalar/PageList.php?languageID=" + $("#languageID").val() + "&categoryID=" + $("#pageCategoryID").val();
                                    }
                                }, 1500);
                            } else {
                                $("#alertMessage").text(data.message);
                                $("#alertModal").modal("show");
                            }
                        }
                    });
                });

                $(document).on("click","#contentCreateButton",function(){
                    var contentDescription = $("#contentInf").val();
                    var languageCode = $("#languageID option:selected").data("languagecode");
                    var action = "generalPageContentGenerator";
                    $("#contentCreateModal").modal("hide");
                    $("#contentInf").val("");
                    alertMessage.html("içerik üretimi başlatılıyor, lütfen bekleyiniz...");
                    alertModal.modal("show");
                    $.ajax({
                        url: "/App/Controller/Admin/AdminChatCompletionController.php",
                        type: "POST",
                        data: {
                            action: action,
                            contentDescription: contentDescription,
                            language: languageCode
                        },
                        success: function(response) {
                            console.log(response);
                            response = JSON.parse(response);
                            if(response.status === "error") {
                                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                alertMessage.html(response.message);
                                alertModal.modal("show");
                            } else {
                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                //alertMessage.html("Ürün içeriği üretimi başarılı");
                                alertModal.modal("hide");

                                let summernote = $('#pageContent').summernote();
                                let editorData = summernote.code();
                                summernote.code(editorData + response.data);
                            }
                        },
                        error: function() {
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            alertMessage.html("Bir hata oluştu, lütfen tekrar deneyin.");
                            alertModal.modal("show");
                        }
                    });
                });

                $(document).on("click", "#createSeo", function() {
                    // Ürün bilgilerini al
                    var pageName = $("#pageName").val();
                    var summernote = $("#pageContent").summernote();
                    var pageContent = summernote.code();
                    var languageCode = $("#languageID option:selected").data("languagecode");

                    // Son seçili kategori adını al
                    var $categorySelect = $("#categoryContainer").find("select").last();
                    var selectedCategoryName = $categorySelect.find("option:selected").text().trim();

                    if (!selectedCategoryName || selectedCategoryName === "Seçiniz") {
                        alertMessage.html("Kategori seçimi yapmadınız.");
                        alertModal.modal("show");
                        $("a[href='#tabCategory']").click();
                        return;
                    }

                    if (!pageName || !pageContent) {
                        alertMessage.html("Sayfa başlık ve açıklama bilgileri boş olamaz.");
                        alertModal.modal("show");
                        return;
                    }

                    //alert modal lütfen bekleyiniz yazdıralım
                    alertModal.modal("show");
                    alertMessage.html("Lütfen bekleyiniz...");

                    // SEO verisi oluşturmak için AJAX isteği
                    $.ajax({
                        url: "/App/Controller/Admin/AdminChatCompletionController.php",
                        type: "POST",
                        data: {
                            action: "generalPageSeoGenerator",
                            title: pageName,
                            description: pageContent,
                            category: selectedCategoryName, // Son seçili kategori adı
                            language: languageCode
                        },
                        success: function(response) {
                            console.log(response);
                            response = JSON.parse(response);

                            if (response.status === "error") {
                                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                alertMessage.html(response.message);
                                alertModal.modal("show");
                            } else {

                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                var seoData = JSON.parse(response.data);
                                alertModal.modal("hide");
                                // SEO verilerini alanlara yaz
                                $("#pageSeoTitle").val(seoData.seoTitle);
                                $("#pageSeoDescription").val(seoData.seoDescription);
                                $("#pageSeoKeywords").val(seoData.seoKeywords);

                                alertMessage.html("SEO içerikleri başarıyla oluşturuldu.");
                                //alertModal.modal("show");
                            }
                        },
                        error: function() {
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            alertMessage.html("Bir hata oluştu, lütfen tekrar deneyin.");
                            alertModal.modal("show");
                        }
                    });
                });

                $(document).on("keyup", "#galleryName", function () {
                    const galleryName = $(this).val();
                    if (galleryName.length > 2) {
                        $.ajax({
                            type: 'GET',
                            url: "/App/Controller/Admin/AdminGalleryController.php?action=searchGallery&searchText=" + galleryName,
                            dataType: "html",
                            success: function(data)
                            {
                                data = JSON.parse(data);
                                if(data.status === "success") {
                                    const galleryResult = data.data;
                                    $(".galleryResult").html("");
                                    if(galleryResult.length > 0) {
                                        galleryResult.forEach((gallery) => {
                                            $galleryID = gallery.galleryID;
                                            $galleryName = gallery.galleryName;

                                            $galleryBox = '<div class="col-lg-6 selectGallery" style="padding: 10px 0">'+
                                                '<label class="radio-inline radio-styled">'+
                                                '<input type="radio" name="pageGalleryID" value="'+$galleryID+'" checked>'+
                                                    '<span>'+$galleryName+'</span>'+
                                                '</label>'+
                                                '</div>';
                                            $(".galleryResult").append($galleryBox);
                                        });
                                    }
                                }
                            },
                            error: function() {
                                console.log("Search gallery error");
                            }
                        });
                    }
                });

                $(document).on("click","#noGallery",function (){
                    $("#galleryResult").html("Galeri Seçilmedi");
                    $("#galleryName").val("");
                    $(".selectedGallery").html("");
                });

                $(document).on("click",".selectGallery",function (){
                    $(".selectedGallery").html("");
                    $selected = $(this).html();
                    $(this).remove();
                    $(".selectedGallery").append($selected);
                    $("#galleryName").val("");
                    $("#galleryResult").html("");
                });

                $(document).on("keyup", "#videoName", function () {
                    let videoName = $(this).val();
                    if (videoName.length > 2) {
                        $.ajax({
                            type: 'GET',
                            url: "/App/Controller/Admin/AdminVideoController.php?action=searchVideo&searchText=" + videoName,
                            dataType: "html",
                            success: function(data)
                            {
                                console.log(data);
                                data = JSON.parse(data);
                                if(data.status === "success") {
                                    $videoResult = data.data;
                                    if($videoResult.length > 0) {
                                        $(".videoResult").html("");
                                        $videoResult.forEach(($video) => {
                                            $videoID = $video.video_id;
                                            $videoName = $video.video_name;
                                            $videoResultHtml = '<div class="col-md-6 selectVideo"><div class="col-md-12 checkbox checkbox-styled"><label><input type="checkbox" name="pageVideoIDS[]" value="'+$videoID+'" checked><span>'+$videoName+'</span></label><div></div>';
                                            $(".videoResult").append($videoResultHtml);
                                        });
                                    }
                                }
                            },
                            error: function() {
                                console.log("Search video error");
                            }
                        });
                    }
                });

                $(document).on("click",".selectVideo",function (){
                    $selected = $(this).html();
                    $(this).remove();
                    $(".selectedVideos").append($selected);
                    $("#videoName").val("");
                    $("#videoResult").html("");
                });

                setTimeout(function(){
                    $('select').select2();
                }, 1000);

                $(document).on("click","#removeDivTagButton", function(){

                    let summernote = $("#pageContent").summernote();
                    let pageContent = summernote.code();
                    if(pageContent !== ""){
                        let modifiedContent = pageContent;

                        modifiedContent = modifiedContent.replace(/<div>/g, "");
                        modifiedContent = modifiedContent.replace(/<\/div>/g, "");

                        summernote.code(modifiedContent);
                    }
                });
            });
        </script>
	</body>
</html>