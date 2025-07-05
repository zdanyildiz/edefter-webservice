<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

$json = $config->Json;

include_once MODEL."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);
$languages = $languageModel->getLanguages();

$productCategoryLanguageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;

$submitButtonTitle = "Kategori Ekle";

$categoryID = $_GET['categoryID'] ?? 0;
$categoryID = is_numeric($categoryID) ? $categoryID : 0;


//sayfa Tipleri ve kayegori tipleri aynı olduğu için aynı modeli kullanıyoruz
//kategori tiplerini çağıracağız
//ana kategorileri çekeceğiz

include_once MODEL."Admin/AdminProductCategory.php";
$productCategoryModel = new AdminProductCategory($db);

$allProductCategories = $productCategoryModel->getProductCategories($productCategoryLanguageID);

if($categoryID > 0){
    $submitButtonTitle = "Kategori Düzenle";
    $productCategory = $productCategoryModel->getProductCategory($categoryID);

    $productCategoryID = $productCategory['categoryID'];
    $productCategoryTopCategoryID = $productCategory['topCategoryID'];
    $productCategoryName = $productCategory['categoryName'];
    $productCategoryUniqID = $productCategory['categoryUniqID'];
    $productCategoryContent = $productCategory['categoryContent'];
    $productCategoryImageID = $productCategory['categoryImageID'];
    $productCategoryImage = $productCategory['categoryImage'];
    $productCategoryOrder = $productCategory['categoryOrder'];
    $productCategorySorting = $productCategory['categorySorting'];
    $productCategoryType = $productCategory['categoryType'];
    $productCategoryHomePage = $productCategory['homePage'];
    $productCategoryActive = $productCategory['categoryActive'];
    $productCategoryDeleted = $productCategory['categoryDeleted'];
    $productCategoryLink = $productCategory['categoryLink'];
    $productCategoryLanguageID = $productCategory['languageID'];

    $productCategorySeoTitle = $productCategory['categorySeoTitle'];
    $productCategorySeoDescription = $productCategory['categorySeoDescription'];
    $productCategorySeoKeywords = $productCategory['categorySeoKeywords'];

    $productCategoryHierarchy = $productCategoryModel->getCategoryHierarchy($productCategoryID);
    $googleCategory = $productCategoryModel->getGoogleCategoryName($productCategoryID);

    if(!empty($googleCategory)){
        $googleCategoryName = $googleCategory[0]["merchant_category_name"];
        $googleCategoryName = $googleCategoryName!="0" ? $googleCategoryName : "";
    }
}

$productCategoryID = $productCategoryID ?? 0;
$productCategoryTopCategoryID = $productCategoryTopCategoryID ?? 0;
$productCategoryName = $productCategoryName ?? "";
$productCategoryUniqID = $productCategoryUniqID ?? "";
$productCategoryContent = $productCategoryContent ?? "";
$productCategoryImageID = $productCategoryImageID ?? 0;
$productCategoryImage = $productCategoryImage ?? "";
$productCategoryOrder = $productCategoryOrder ?? 0;
$productCategorySorting = $productCategorySorting ?? 0;
$productCategoryType = $productCategoryType ?? 7;
$productCategoryHomePage = $productCategoryHomePage ?? 0;
$productCategoryActive = $productCategoryActive ?? 1;
$productCategoryDeleted = $productCategoryDeleted ?? 0;
$productCategoryLink = $productCategoryLink ?? "";
$productCategoryLanguageID = $productCategoryLanguageID ?? 0;

$productCategoryHierarchy = $productCategoryHierarchy ?? [];
$productCategorySeoTitle = $productCategorySeoTitle ?? "";
$productCategorySeoDescription = $productCategorySeoDescription ?? "";
$productCategorySeoKeywords = $productCategorySeoKeywords ?? "";
$googleCategoryName = $googleCategoryName ?? "";
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Pozitif Panel - Ürün Kategori</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
              type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/summernote/summernote.min.css">
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1422823376"/>

        <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."_y/s/b/header.php");?>
		<div id="base">
            <?php require_once(ROOT."_y/s/b/leftCanvas.php");?>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Ürün Kategori Ekle / Düzenle</li>
						</ol>
					</div>
					<div class="section-body contain-lg">

                        <form class="form" id="addAndUpdateProductCategory" method="post">
                            <input type="hidden" name="productCategoryID" id="productCategoryID" value="<?=$productCategoryID?>">
                            <input type="hidden" name="productCategoryTopCategoryID" id="productCategoryTopCategoryID" value="<?=$productCategoryTopCategoryID?>">
                            <input type="hidden" name="productCategoryType" id="productCategoryType" value="<?=$productCategoryType?>">

                            <div  class="row" <?=($productCategoryLanguageID!=0 && $productCategoryID!=0) ? 'hidden':''?>>
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl">
                                        <h4>Dil Seçimi</h4><p></p>
                                        <p>
                                        Kategori ekleyeceğiniz dili seçin
                                        </p>
                                    </article>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-body ">
                                            <div class="form-group">
                                                <!-- dil listesi gelecek -->
                                                <select name="languageID" id="languageID" class="form-control">
                                                    <option value="0">Dil Seçin</option>
                                                    <?php foreach($languages as $language){
                                                        $selected = "";
                                                        if($language['languageID'] == $productCategoryLanguageID) {
                                                            $selected = "selected";
                                                        }
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
                                        <h4>Kategori Temel Bilgiler</h4>
                                        <p></p>
                                        <?php if($productCategoryLanguageID!=0&&$productCategoryID!=0):?>
                                        <p class="alert alert-callout">
                                            Kategori güncellemesi yapıyorsunuz!<br>Kategori adını, içeriğini, anasayfa durumunu ve yayın durumunu değiştirebilirsiniz.<br>
                                            Kategori düzenlemesi yaparken alt kategorileri etkilememek için dikkatli olunuz!<br>
                                            Düzenleme işleminde kategori dili değiştirilemez!
                                        </p>
                                        <?php else:?>
                                            <p class="alert alert-callout">
                                                Yeni kategori ekliyosunuz!<br>Kategori adını, içeriğini, anasayfa durumunu ve yayın durumunu belirleyebilirsiniz.<br>
                                            </p>
                                        <?php endif;?>
                                        <br>
                                        <p>Yayın alanından Pasif konumunu seçerseniz kategori kaydedilir fakat siz aktif edene kadar görüntülenmez.<br>Kategoriyi ana sayfa olarak ayarlarsanız, site adresiniz yazıldığında anasayfa yerine kategori sayfanız açılır</p>

                                    </article>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div>ÜST KATEGORİ SEÇİN</div>
                                            <div class="row form-group floating-label" id="categoryContainer">
                                                <div id="categoryList0" class="categoryList col-sm-6 form-group floating-label">
                                                    <select data-layer="0" class="col-sm-12 form-control">
                                                    </select>
                                                    <p class="help-block">Kategori Seçin</p>
                                                </div>
                                            </div>
                                            <div id="topCategoryWarning" class="alert alert-callout hidden">
                                                <strong>Üst kategori Uyarısı</strong>
                                                <p>Kategori <span class="text-danger"> [topCategory] </span> altında yer alacaktır!</p>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="productCategoryName" id="productCategoryName" class="form-control" placeholder="Örn:Cep Telefonları" value="<?=$productCategoryName?>">
                                                <label>Kategori Adı</label>
                                            </div>
                                            <div>
                                                <label class="col-sm-3 control-label">Yayınlansın mı</label>
                                                <div class="col-sm-9">
                                                    <div class="radio radio-styled">
                                                        <label class="radio-inline radio-styled">
                                                            <input type="radio" name="productCategoryActive" value="1" <?=$productCategoryActive==1 ? 'checked':''?>><span>Aktif</span>
                                                        </label>

                                                        <label class="radio-inline radio-styled">
                                                            <input type="radio" name="productCategoryActive" value="0" <?=$productCategoryActive==0 ? 'checked':''?>><span>Pasif</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="col-sm-3 control-label">Anasayfa olarak ayarlansın</label>
                                                <div class="col-sm-9">
                                                    <div class="radio radio-styled">
                                                        <label class="radio-inline radio-styled">
                                                            <input type="radio" name="productCategoryHomePage" value="1" <?=$productCategoryHomePage==1 ? 'checked':''?>><span>Evet</span>
                                                        </label>

                                                        <label class="radio-inline radio-styled">
                                                            <input type="radio" name="productCategoryHomePage" value="0" <?=$productCategoryHomePage==0 ? 'checked':''?>><span>Hayır</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <em class="text-caption">Temel özellikleri seçin</em>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl">
                                        <h4>Kategori İçeriği</h4><p></p>
                                        <p>Kategori içeriğinizi yazabilir, bir belgeden kopyala yapıştır yapabilirsiniz.</p>
                                        <p>Kategori için yeni bir resim yükleyip/seçebilir, ayrıca kategori içerik alanına birden fazla resim yükleyip seçebilirsiniz.</p>
                                        <div class="btn-group" id="productContentCreateButtonContainer" data-toggle="buttons" style="margin-top:30px">
                                            <label
                                                    class="btn btn-primary btn-md"
                                                    data-toggle="modal"
                                                    data-target="#productContentCreateModal"
                                                    title="Yapay Zeka ile İçerik Üretin">
                                                <i class="fa fa-connectdevelop fa-fw"></i>
                                                AI İçerik Üretici
                                            </label>
                                        </div>
                                        <div class="modal fade" id="productContentCreateModal" tabindex="-1" role="dialog" aria-labelledby="productContentCreateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button id="btn-popup-alert-close" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                        <h4 class="modal-title" id="productContentCreateModalLabel">Yapay Zeka ile İçerik Üretin</h4>
                                                    </div>
                                                    <div class="modal-body">

                                                        <p><strong>Ürün kategorisi içeriği üretmek için, kategorinin ne tür ürünleri kapsadığını ve bu ürünlerin özelliklerini açıklayan kısa bilgiler verin. Bu kategoriye giren ürünlerin kullanıcıya nasıl fayda sağlayacağını veya hangi özellikleri öne çıkardığını birkaç cümle ile ifade edin. Bu açıklama, kategori sayfasında ziyaretçilere kategori hakkında genel bilgi sunacak bir içerik oluşturmak için kullanılacaktır.</strong></p>
                                                        <div class="card-body">
                                                            <strong>Örnek Metin</strong><br>
                                                            <p><strong>Örnek 1:</strong> "Erkek gömlekleri kategorisi, hem klasik hem de günlük tarza uygun çeşitli gömlek modellerini içerir. Rahat kesim, kaliteli kumaş ve farklı renk seçenekleri ile her tarza hitap eder."</p>

                                                            <p><strong>Örnek 2:</strong> "Su geçirmez montlar kategorisi, soğuk ve yağışlı havalar için özel olarak tasarlanmış montlardan oluşur. Nefes alabilir kumaş yapısı ve şık tasarımları ile sıcak kalmayı sağlarken aynı zamanda rahat bir kullanım sunar."</p>

                                                            <p><strong>Örnek 3:</strong> "Çocuk oyuncakları kategorisi, çocukların eğlenirken öğrenmelerine yardımcı olacak güvenli ve eğitici oyuncaklardan oluşur. Her yaş grubuna uygun oyuncak çeşitleri, çocukların motor becerilerini ve hayal güçlerini geliştirmeye yönelik tasarlanmıştır."</p>
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
                                                        <button type="button" class="btn btn-primary" id="productContentCreateButton">Ürün Kategorisi İçeriği Üret</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-head">
                                            <div class="btn-group" id="contentImageButtonContainer" data-toggle="buttons">
                                                <label
                                                        class="btn btn-primary-bright btn-md"
                                                        href="#offcanvas-imageUpload"
                                                        id="uploadImageByLeftCanvas"
                                                        data-target="productCategoryContent"
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
                                                        data-target="productCategoryContent"
                                                        data-toggle="offcanvas"
                                                        title="Listeden Resim Seç">
                                                    <i class="fa fa-file-image-o fa-fw"></i>
                                                    İçeriğe Resim Seç
                                                </label>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body no-padding">
                                                <textarea id="productCategoryContent" name="productCategoryContent"><?=$productCategoryContent?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <em class="text-caption">Kategori İçeriği/Açıklama</em>
                                </div>
                            </div>

                            <div class="row margin-bottom-xxl">
                                <div class="col-lg-3 col-md-4">
                                    <h4>Tek Görsel - Sürükle Bırak</h4>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
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
                                            if(!empty($productCategoryImage))
                                            {
                                                ?>
                                                <div class="col-md-1 text-center imageBox" style="cursor:grab" id="imageBox_<?=$productCategoryImageID?>">
                                                    <input type="hidden" name="imageID[]" value="<?=$productCategoryImageID?>">
                                                    <div class="tile-icond">
                                                        <img id="image_<?=$productCategoryImageID?>" class="size-2" src="<?=imgRoot."?imagePath=".$productCategoryImage?>&width=100&height=100" alt="<?=$productCategoryName?>">
                                                    </div>
                                                    <div class="tile-text">
                                                        <a
                                                                class="btn btn-floating-action ink-reaction removeImage"
                                                                data-imageBox="imageBox_<?=$productCategoryImageID?>"
                                                                data-id="<?=$productCategoryImageID?>"
                                                                data-toggle="modal"
                                                                data-target="#removeImageModal"
                                                                title="Kaldır">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php
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
                                        <div class="col-sm-6 hidden">
                                            <a href="javascript:void(0)" id="removeAllImages" class="btn ink-reaction btn-flat btn-xs btn-danger" style="float:right;">Tüm Resimleri Kaldır</a>
                                        </div>
                                    </div>

                                </div>
                                </div>
                            </div>
                            <!-- kategori ürünleri sıralama tipi -->
                            <div class="row">
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl">
                                        <h4>Kategori Ürün Sıralama Seçenekleri</h4><p></p>
                                        <p>
                                            <strong>Ürün Sıralama</strong> 
                                        Bu kategoriye ekleyeceğiniz ürünlerin varsayılan olarak sıralamasını belirleyebilirsiniz. Kategori sayfası açıldığında son eklenen en üste, alfabetik sıralama ya da güncelleme tarihine göre sıralama tipi seçebilirsiniz.
                                    </article>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group floating-label">
                                                <select id="productCategorySorting" name="productCategorySorting" class="form-control">
                                                    <option value="0" <?php if($productCategorySorting==0) echo "selected";?>>İlk Eklenen En Üste</option>
                                                    <option value="1" <?php if($productCategorySorting==1) echo "selected";?>>Son Eklenen En Üste</option>
                                                    <option value="2" <?php if($productCategorySorting==2) echo "selected";?>>Güncelleme Tarihi Eskiden Yeniye</option>
                                                    <option value="3" <?php if($productCategorySorting==3) echo "selected";?>>Güncelleme Tarihi Yeniden Eskiye</option>
                                                    <option value="4" <?php if($productCategorySorting==4) echo "selected";?>>Sayfa Sırası Küçükten Büyüğe</option>
                                                    <option value="5" <?php if($productCategorySorting==5) echo "selected";?>>Sayfa Sırası Büyükten Küçüğe</option>
                                                    <option value="6" <?php if($productCategorySorting==6) echo "selected";?>>Sayfa Adı A-Z</option>
                                                    <option value="7" <?php if($productCategorySorting==7) echo "selected";?>>Sayfa Adı Z-A</option>
                                                </select>
                                                <label for="productCategorySorting">Kategori Sayfaları Sıralaması</label>
                                            </div>

                                        </div>
                                    </div>
                                    <em class="text-caption">Varsayılan Ürün Sıralaması</em>
                                </div>
                            </div>
                            <!-- kategori ürünleri sıralama tipi -->
                            <div class="row">
                                <div class="col-lg-3 col-md-4">
                                    <article class="margin-bottom-xxl">
                                        <h4>Google Arama SEO ve Alışveriş Seçenekleri</h4><p></p>
                                        <p>
                                            Ürünlerinizin <strong>Google Alışveriş Sekmesinde</strong> görünmesi için Google'ın kategori seçeneğini kullanın.<br>
                                            <strong>SEO İçeriği</strong> Seo içeriğini oluştururken yalnızca site ve kategorinizle alakalı metinler kullanın.
                                            <code>Aklınıza gelen her kelimeyi seo'ya girmek sitenizi üst sıralara çıkarmaz, aksine yanıltıcı içerik diye etiketlenebilir ve arama sonuçlarında performans alamayabilirsiniz.</code>
                                        </p>
                                        <div class="btn-group" id="seoInfoButtonContainer" data-toggle="buttons">
                                            <label
                                                    class="btn btn-primary-bright btn-md"
                                                    data-toggle="modal"
                                                    data-target="#seoInfoModal"
                                                    title="SEO Bilgilendirme">
                                                <i class="fa fa-info fa-fw"></i>
                                                SEO Bilgilendirme
                                            </label>
                                        </div>
                                    </article>
                                </div>
                                <div class="col-lg-offset-1 col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <select name="googleCategory" id="googleCategory" class="form-control">
                                                    <?php if(!empty($googleCategoryName)):?>
                                                        <option value="<?=$googleCategoryName?>" selected><?=$googleCategoryName?></option>
                                                    <?php else:?>
                                                    <option value="0">Google Kategori Seçimi</option>
                                                    <?php endif;?>
                                                </select>
                                                <label for="googleCategory">Google Alışveriş Kategorisi Seçin</label>
                                            </div>
                                            <div class="form-group">
                                                <textarea name="productCategoryLink" id="productCategoryLink" class="form-control" rows="1" placeholder="otomatik oluşturulur."><?=$productCategoryLink?></textarea>
                                                <label for="kategorilink">Kategori Bağlantısı</label>
                                            </div>
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        name="productCategorySeoTitle"
                                                        id="productCategorySeoTitle"
                                                        class="form-control"
                                                        placeholder="Arama moturu sonuçlarında görünen başlık"
                                                        value="<?=$productCategorySeoTitle?>"
                                                        data-rule-minlength="5"
                                                        maxlength="100"
                                                        aria-invalid="false"
                                                        required aria-required="true">
                                                <label for="productCategorySeoTitle">SEO Başlık</label>
                                            </div>
                                            <div class="form-group">
												<textarea
                                                        id="productCategorySeoDescription"
                                                        name="productCategorySeoDescription"
                                                        placeholder="Arama motoru sonuçlarında görünen açıklama"
                                                        class="form-control"
                                                        rows="3"
                                                        data-rule-minlength="25"
                                                        maxlength="200"
                                                        aria-invalid="false"
                                                        required aria-required="true"><?=$productCategorySeoDescription?></textarea>
                                                <label for="productCategorySeoDescription">SEO Açıklama</label>
                                            </div>
                                            <div class="form-group">
												<textarea
                                                        id="productCategorySeoKeywords"
                                                        name="productCategorySeoKeywords"
                                                        class="form-control"
                                                        placeholder="İçeriğinizle alakalı kelimeler girin. (marka ürün,renk ürün,cinsiyet ürün,marka cinsiyet...)"
                                                        rows="2"
                                                        data-rule-minlength="6"
                                                        maxlength="255"
                                                        aria-invalid="false"
                                                        required aria-required="true"><?=$productCategorySeoKeywords?></textarea>
                                                <label for="productCategorySeoKeywords">SEO Kelimeler</label>
                                            </div>
                                            <div class="form-group">
                                                <button id="createSeo" type="button" class="btn btn-primary-bright btn-sm">AI Seo Oluşturucu</button>
                                            </div>
                                        </div>
                                    </div>
                                    <em class="text-caption">Seo Başlık/Açıklama/Anahtar Kelime</em>
                                </div>
                            </div>
                            <div class="card-actionbar">
                                <div class="card-actionbar-row">
                                    <button type="submit" class="btn btn-primary btn-default"><?=$submitButtonTitle?></button>
                                </div>
                            </div>
                        </form>

					</div>
				</section>
			</div>
			<?php require_once(ROOT."_y/s/b/menu.php");?>
            <?php require_once(ROOT."_y/s/b/rightCanvas.php");?>
		</div>

        <div class="modal fade" id="seoInfoModal" tabindex="-1" role="dialog" aria-labelledby="seoInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title" id="seoInfoModalLabel">SEO Bilgilendirme</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <p><strong># SEO Nedir?</strong><br>
                            SEO, web sitenizin arama motorlarında daha görünür hale gelmesini sağlamak için yapılan çalışmalardır. Amaç, arama sonuçlarında daha üst sıralarda yer almak ve organik trafiği artırmaktır.
                        </p>
                        <p><strong># SEO Başlığı Nasıl Olmalı?</strong><br>
                            SEO başlığı, sayfanızın arama motoru sonuçlarında görünen başlıktır. Kullanıcıların dikkatini çeker ve sayfanın içeriği hakkında bilgi verir. Başlığınız 50-60 karakter arasında olmalı ve anahtar kelimeleri içermelidir.
                        </p>
                        <p><strong># SEO Açıklaması Nasıl Olmalı?</strong><br>
                            Meta açıklama, sayfanızın arama sonuçlarında görünen kısa özetidir. Bu açıklama, kullanıcıları sayfanıza tıklamaya teşvik eder. 150-160 karakter arasında olmalı, sayfanın içeriğini doğru bir şekilde özetlemeli ve anahtar kelimeleri içermelidir.

                        </p>
                        <p><strong># Anahtar Kelime Seçimi Nasıl Olmalı?</strong><br>
                            Anahtar kelimeler, kullanıcıların arama motorlarında kullanabileceği terimlerdir. Ürün veya hizmetinizle ilgili en önemli ve popüler kelimeleri seçin. Anahtar kelimeleri doğal bir şekilde içeriğe dahil edin ve aşırıya kaçmaktan kaçının.

                        </p>
                        <p><strong># SEO İçerikleri Nasıl Olmalı?</strong><br>
                            İçeriğiniz, hem kullanıcılar hem de arama motorları için değerli olmalıdır. Bilgilendirici, özgün ve kaliteli içerikler oluşturun. Anahtar kelimeleri mantıklı bir şekilde kullanın ve okuyucularınıza fayda sağlayan bilgiler sunun.

                        </p>
                        <p><strong># URL Yapısı Nasıl Olmalı?</strong><br>
                            URL yapınız, kısa, açıklayıcı ve anahtar kelimeleri içermelidir. Örneğin, "www.siteadi.com/urun-adi" gibi basit ve anlaşılır URL'ler tercih edilmelidir. URL'lerde gereksiz karakterlerden kaçının.

                        </p>
                        <p><strong># Görsel Optimizasyonu Nasıl Olmalı?</strong><br>
                            Görseller, sayfa yüklenme hızını ve kullanıcı deneyimini etkiler. Görsellerinizi optimize ederek hızlı yüklenmelerini sağlayın. Ayrıca, görsel alt metinlerinde (alt text) anahtar kelimeleri kullanarak arama motorlarının görsellerinizi anlamasını kolaylaştırın.

                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn-popup-alert-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="alertModalLabel">Uyarı</h4>
                    </div>
                    <div class="modal-body">
                        <p id="alertMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
        <script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>

        <script src="/_y/assets/js/libs/summernote/summernote.min.js"></script>
        <script src="/_y/assets/js/libs/select2/select2.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

        <script>
            $("#addProductCategoryphp").addClass("active");

            const alertModal = $("#alertModal");
            const alertMessage = $("#alertMessage");

            let imgRoot = "<?=imgRoot?>";
            let fileRoot = "<?=fileRoot?>";

            let categoryHierarchy = '<?=!empty($productCategoryHierarchy) ? json_encode($productCategoryHierarchy) : '[]'?>';
            categoryHierarchy = JSON.parse(categoryHierarchy);
            let categoryHierarchyLength = categoryHierarchy.length;
            const topCategoryWarning = $('#topCategoryWarning');

            $("#productCategoryContent").summernote({
                tabsize: 2,
                height: 400,
                minHeight: 400

            });

            function getGoogleCategoryData(languageCode) {
                const url = "/App/Controller/Admin/AdminProductController.php?action=getGoogleCategories&languageCode=" + languageCode;

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(response) {
                        response = JSON.parse(response);
                        if(response.status === "error"){
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            alertMessage.html(response.message);
                            alertModal.modal("show");
                            return;
                        }
                        const categories = response.categories;

                        //kategorileri ayrıştırıp #googleCategory selection option olarak ekleyelim
                        for(let i = 0; i < categories.length; i++){
                            const category = categories[i];
                            $("#googleCategory").append("<option value='"+category.categoryName+"'>"+category.categoryName+"</option>");
                        }
                    }
                });
            }

            function createSeoLink($string){
                $string = $string.toLowerCase();
                $string = $string.replace(/ğ/g, "g");
                $string = $string.replace(/ü/g, "u");
                $string = $string.replace(/ş/g, "s");
                $string = $string.replace(/ı/g, "i");
                $string = $string.replace(/ö/g, "o");
                $string = $string.replace(/ç/g, "c");
                $string = $string.replace(/ /g, "-");
                $string = $string.replace(/[^a-z0-9-]/g, "");

                //yanyana gelmiş birden fazla - karakterini tek - karakterine dönüştürelim
                $string = $string.replace(/-+/g, "-");
                return $string;
            }

            async function loadCategories(selectedElement) {
                const layer = selectedElement.data('layer');
                const categoryID = selectedElement.val();
                const action = "getSubCategories";


                if (categoryID > 0) {
                    try {
                        const response = await $.ajax({
                            url: '/App/Controller/Admin/AdminProductController.php',
                            type: 'POST',
                            data: {
                                categoryID: categoryID,
                                action: action
                            }
                        });

                        const data = JSON.parse(response);
                        const { status, subCategories } = data;

                        if (status === "success") {
                            $("#productCategoryTopCategoryID").val(categoryID);
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
                            categoryList.append('<option value="0" selected>Alt Kategori Seçilmedi</option>');
                            $.each(subCategories, function (index, category) {
                                categoryList.append(`<option value="${category.productCategoryID}">${category.productCategoryName}</option>`);
                            });
                        } else {
                            $(`#categoryList${layer + 1}`).remove();
                        }
                    }
                    catch (error) {
                        console.error(`Error loading categories: ${error.message}`);
                    }
                }
                else {
                    handleFailure();
                }
            }

            async function changeLanguageID() {
                const languageID = $('#languageID').val();
                const languageCode = $('#languageID option:selected').data("languagecode");
                const action = "getProductCategories";
                const categoryContainer = $('#categoryContainer select');
                const categoryList = $('#categoryList0 select');

                categoryContainer.empty();

                <?php
                if(empty($allProductCategories)):
                ?>
                $("#categoryList0 select").append("<option value='0'>Üst Kategori yok</option>");
                <?php
                endif;
                ?>

                if (languageID > 0) {
                    try {
                        const response = await $.ajax({
                            url: '/App/Controller/Admin/AdminProductController.php',
                            type: 'POST',
                            data: {
                                languageID: languageID,
                                action: action
                            }
                        });

                        const { status, productCategories: categories } = JSON.parse(response);

                        if (status === "success") {
                            $("#productCategoryTopCategoryID").val(0);
                            categoryList.empty();
                            categoryList.append('<option value="0" selected>Üst Kategori Yok</option>');
                            categories.forEach(({ productCategoryID, productCategoryName }) => {
                                categoryList.append(`<option value="${productCategoryID}">${productCategoryName}</option>`);
                            });
                            getGoogleCategoryData(languageCode);
                            handleFailure();
                            console.log("Dil değişikliği başarılı, kategoriler yüklendi");
                        }
                        else {
                            handleFailure();
                        }
                    }
                    catch (error) {
                        console.error(error);
                        handleFailure();
                    }
                }
                else {
                    handleFailure();
                }
            }

            function handleFailure() {
                $("#productCategoryTopCategoryID").val(0);
                topCategoryWarning.find('p').html('Kategori <span class="text-danger">ANA KATEGORİ</span> olacaktır!');
                topCategoryWarning.addClass('hidden');
                $('#categoryContainer .categoryList').each(function (index, element) {
                    let layer = $(element).find('select').data('layer');
                    if (layer > 0) {
                        $(element).remove();
                        console.log("Kategori silindi: " + layer);
                    }
                });

                $('#categoryContainer select').each(function (index, element) {
                    lastCategoryList = $(element);
                });


                if (lastCategoryList.find('option').length === 0) {
                    console.log("Son kategori listesi boş");
                    lastCategoryList.append('<option value="0" selected>Üst Kategori Yok</option>');
                }
            }

            async function selectCategory(i) {
                console.log("Kategori seçiliyor: " + categoryHierarchy[i]);
                const categoryID = categoryHierarchy[i].categoryID;
                console.log("Kategori seçiliyor: " + categoryID);

                const categoryListSelector = `#categoryList${i} select`;
                const selectedElement = $(categoryListSelector);

                //$("#productCategoryTopCategoryID").val(categoryID);
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

            function lastSelect() {
                let productCategoryID = $('#productCategoryID');
                let lastCategoryList;

                if (categoryHierarchyLength > 0) {
                    lastCategoryList = $('#categoryList' + (categoryHierarchyLength - 1) + ' select');
                }
                else {
                    // each ile liste sayalım
                    $('#categoryContainer select').each(function (index, element) {
                        lastCategoryList = $(element);
                    });
                }

                if (productCategoryID.val() != 0) {
                    lastCategoryList.find('option[value="' + productCategoryID.val() + '"]').remove();
                }


                let topCategory = lastCategoryList.find('option:selected');
                let topCategoryText;

                if (topCategory.val() != 0 && topCategory.val() != "") {
                    topCategoryText = topCategory.text();
                } else {
                    categoryHierarchyLength = $('#categoryContainer select').length;
                    topCategory = $('#categoryList' + (categoryHierarchyLength - 2) + ' select').find('option:selected');
                    topCategoryText = topCategory.text();
                }

                if (topCategoryText == "Üst Kategori Yok" || topCategoryText == "Alt Kategori Seçilmedi") {
                    topCategoryWarning.addClass('hidden');
                } else {
                    topCategoryWarning.find('p').html('Kategori <span class="text-danger">' + topCategoryText + '</span> altında yer alacaktır!');
                    topCategoryWarning.removeClass('hidden');
                }

                $('#categoryContainer select').each(function (index, element) {
                    lastCategoryList = $(element);
                });

                if (lastCategoryList.find('option').length === 0) {
                    console.log("Son kategori listesi boş");
                    lastCategoryList.append('<option value="0" selected>Üst Kategori Yok</option>');
                }
            }

            $(document).ready(function() {

                $(document).on("change", "#languageID", function () {
                    changeLanguageID().then(() => {
                        console.log("Dil değişikliği başarılı");
                    });
                });

                $(document).on('change', '#categoryContainer select', async function () {
                    let selectedElement = $(this);
                    //#productCategoryTopCategoryID seçilen değeri inputa atayalım
                    $("#productCategoryTopCategoryID").val(selectedElement.val());
                    await loadCategories(selectedElement);
                    lastSelect();
                });

                <?php if($productCategoryID == 0&&$productCategoryLanguageID==0):?>
                $('#languageID').val($('#languageID option[value!="0"]').first().val()).change();
                <?php elseif($productCategoryLanguageID != 0):?>
                $('#languageID').change();
                <?php else:?>
                changeLanguageID().then(() => {
                    selectCategories().then(() => {
                        console.log("Kategoriler seçildi");
                        lastSelect();
                        if($('#productCategoryTopCategoryID').val() == $("#productCategoryID").val() ){
                            $('#productCategoryTopCategoryID').val(0);
                        }
                    });
                });
                <?php endif;?>

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

                    if ($imageTarget === "productCategoryContent") {

                        //genişliğe göre yükseklik ayarlayalım
                        $imageNewWidth = 300;
                        $imageNewHeight = Math.round($imageHeight / $imageWidth * $imageNewWidth);

                        // Summernote'taki mevcut içeriği alın
                        let summernote = $("#productCategoryContent").summernote();
                        let editorData = summernote.code();
                        console.log(editorData);

                        // Yeni resim HTML'sini oluşturun
                        let newImageHtml = '<img src="' + imgRoot + '?imagePath=' + $imagePath + '&width=' + $imageNewWidth + '&height=' + $imageNewHeight + '" title="' + $imageName + '" width="' + $imageNewWidth + '" height="' + $imageNewHeight + '" >';

                        // Mevcut içeriğe yeni resmi ekleyin
                        summernote.code( editorData + newImageHtml);
                    }
                    else {

                        $html = $imageBox;
                        $html = $html.replaceAll("[imageID]", $imageID);
                        $html = $html.replaceAll("[imagePath]", $imagePath);
                        $html = $html.replaceAll("[imageName]", $imageName);

                        $("#imageContainer").append($html);
                    }
                });


                Dropzone.options.imageDropzone = {
                    parallelUploads: 1,
                    autoProcessQueue: true,
                    addRemoveLinks: true,
                    maxFiles: 1,
                    maxFilesize: 3,
                    dictDefaultMessage: "Resimleri yüklemek için bırakın",
                    dictFallbackMessage: "Tarayıcınız sürükle ve bırak dosyaları yüklemeyi desteklemez.",
                    dictFallbackText: "Resimleri eski günlerde olduğu gibi yüklemek için lütfen aşağıdaki geri dönüş formunu kullanın..",
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
                        this.on("proccesing", function (file) {
                            console.log("proccesing");

                            var imageTarget = $("#imageTarget").val();

                        });
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

                                    if ($imageTarget === "productCategoryContent") {

                                        //genişliğe göre yükseklik ayarlayalım
                                        $imageNewWidth = 300;
                                        $imageNewHeight = Math.round($imageHeight / $imageWidth * $imageNewWidth);

                                        let summernote = $('#productCategoryContent').summernote();
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
                    $("#removeImageButton").off('click').on("click", function () {
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

                //#addAndUpdateProductCategory form submit dinleyelim
                $(document).on("submit", "#addAndUpdateProductCategory", function (e) {
                    e.preventDefault();

                    $("#alertModal .modal-header").removeClass("bg-success").addClass("bg-danger");

                    let categoryID = $('#productCategoryID').val();
                    let action = categoryID > 0 ? "updateProductCategory" : "addProductCategory";

                    //productCategoryTopCategoryID
                    let productCategoryTopCategoryID = $('#productCategoryTopCategoryID').val();

                    //kategori ile ğst kategori aynı olamaz
                    if(productCategoryTopCategoryID == categoryID && categoryID > 0){
                        $("#alertMessage").html("Kategori ile üst kategori aynı olamaz!");
                        $("#alertModal").modal("show");
                        return;
                    }

                    //üstkategori doğrulaması yapalım. öncelikle kaç tane selectbox var sayalım
                    let categoryCount = $('#categoryContainer select').length;

                    //sondan başlayarak en son seçilen kategoriyi fakat değer 0 olmayanı alalım
                    let selectedCategoryID = 0;
                    for(let i = categoryCount - 1; i >= 0; i--){
                        let selectedCategory = $('#categoryList' + i + ' select').val();
                        if(selectedCategory > 0){
                            selectedCategoryID = selectedCategory;
                            break;
                        }
                    }

                    //eğer seçilen kategori ile üst kategori aynı değilse hata verelim
                    if(selectedCategoryID != productCategoryTopCategoryID){
                        $("#alertMessage").html("Üst kategori seçimini kontrol ediniz");
                        $("#alertModal").modal("show");
                        return;
                    }

                    //kategori adı boş olamaz
                    let productCategoryName = $('#productCategoryName').val();
                    if(productCategoryName == ""){
                        $("#alertMessage").html("Kategori adı boş olamaz!");
                        $("#alertModal").modal("show");
                        return;
                    }

                    //productCategoryLink boş olamaz
                    let productCategoryLink = $('#productCategoryLink').val();
                    if(productCategoryLink == ""){
                        var link = "/" + createSeoLink(productCategoryName);
                        $('#productCategoryLink').val(link);
                    }

                    //productCategorySeoTitle boş olamaz
                    let productCategorySeoTitle = $('#productCategorySeoTitle').val();
                    if(productCategorySeoTitle == ""){
                        $("#alertMessage").html("SEO başlık boş olamaz!");
                        $("#alertModal").modal("show");
                        return;
                    }

                    //productCategorySeoDescription boş olamaz
                    let productCategorySeoDescription = $('#productCategorySeoDescription').val();
                    if(productCategorySeoDescription == ""){
                        $("#alertMessage").html("SEO açıklama boş olamaz!");
                        $("#alertModal").modal("show");
                        return;
                    }

                    //productCategorySeoKeywords boş olamaz
                    let productCategorySeoKeywords = $('#productCategorySeoKeywords').val();
                    if(productCategorySeoKeywords == ""){
                        $("#alertMessage").html("SEO anahtar kelimeler boş olamaz!");
                        $("#alertModal").modal("show");
                        return;
                    }

                    let summernote = $("#productCategoryContent").summernote();
                    let productCategoryContent = summernote.code();
                    $("#productCategoryContent").val(productCategoryContent);

                    var form = $(this);
                    var data = form.serialize();
                    data += "&action=" + action;

                    var url = "/App/Controller/Admin/AdminProductController.php";

                    var languageID = $('#languageID').val();

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: function (response) {
                            var responseObject = JSON.parse(response);
                            var status = responseObject.status;
                            var message = responseObject.message;

                            if (status === "success") {
                                $("#alertModal .modal-header").removeClass("bg-danger").addClass("bg-success");
                                $("#alertMessage").html(message);
                                $("#alertModal").modal("show");
                                $categoryID = responseObject.categoryID;
                                //1,5 saniye sonra yönledirme yapalım
                                setTimeout(function(){
                                    window.location.href = "/_y/s/s/urunler/ProductCategoryList.php?languageID="+languageID;
                                }, 1500);
                            } else {
                                $("#alertModal .modal-header").removeClass("bg-success").addClass("bg-danger");

                                if(action == "updateProductCategory") {
                                    message += "<br>Ürünlerin kategori bilgilerini kontrol ediniz!";
                                }

                                $("#alertMessage").html(message);
                                $("#alertModal").modal("show");
                            }
                        }
                    });
                });

                //kategori adı dinleyelim, yazarken seo başlık ve link oluşturalım
                $(document).on("keyup", "#productCategoryName", function () {
                    let productCategoryName = $(this).val();
                    let productCategoryLink = createSeoLink(productCategoryName);
                    let productCategorySeoTitle = productCategoryName;


                    $('#productCategoryLink').val("/" + productCategoryLink);
                    $('#productCategorySeoTitle').val(productCategorySeoTitle);

                });

                $(document).on("click","#productContentCreateButton",function(){
                    var contentDescription = $("#contentInf").val();
                    var languageCode = $("#languageID option:selected").data("languagecode");
                    var action = "productCategoryContentGenerator";
                    $("#productContentCreateModal").modal("hide");
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

                                let summernote = $('#productCategoryContent').summernote();
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
                    var productCategoryName = $("#productCategoryName").val();
                    var summernote = $("#productCategoryContent").summernote();
                    var productCategoryContent = summernote.code();
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

                    if (!productCategoryName || !productCategoryContent) {
                        alertMessage.html("Ürün başlık ve açıklama bilgileri boş olamaz.");
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
                            action: "productCategorySeoGenerator",
                            title: productCategoryName,
                            description: productCategoryContent,
                            category: selectedCategoryName, // Son seçili kategori adı
                            language: languageCode
                        },
                        success: function(response) {
                            console.log(response);
                            response = JSON.parse(response);
                            alertModal.modal("hide");
                            if (response.status === "error") {
                                alertMessage.html(response.message);
                                alertModal.modal("show");
                            } else {

                                var seoData = JSON.parse(response.data);

                                // SEO verilerini alanlara yaz
                                $("#productCategorySeoTitle").val(seoData.seoTitle);
                                $("#productCategorySeoDescription").val(seoData.seoDescription);
                                $("#productCategorySeoKeywords").val(seoData.seoKeywords);

                                alertMessage.html("SEO içerikleri başarıyla oluşturuldu.");
                                //alertModal.modal("show");
                            }
                        },
                        error: function() {
                            alertMessage.html("Bir hata oluştu, lütfen tekrar deneyin.");
                            alertModal.modal("show");
                        }
                    });
                });

                setTimeout(function(){
                    $('select').select2();
                }, 1000);

                const urlParams = new URLSearchParams(window.location.search);
                const refAction = urlParams.get('refAction');
                if(refAction){
                    modalMessage="Ürün eklemek için önce ürün kategorisi eklemelisiniz";
                    $("#alertModal .modal-header").removeClass("bg-success").addClass("bg-danger");
                    $("#alertModal #alertMessage").html(modalMessage);
                    $("#alertModal").modal("show");
                }
            });
        </script>
	</body>
</html>
