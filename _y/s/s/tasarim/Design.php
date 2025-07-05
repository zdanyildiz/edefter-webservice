<?php  require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminCompany.php';
$companyModel = new AdminCompany($db);

$logo = $companyModel->getCompanyLogo($languageID);

if(!empty($logo)){
    $imageID = $logo["imageID"];
    $logoImagePath = $logo["imagePath"];
    $logoText = $logo["logoText"];
}
else{
    $imageID = 0;
    $logoImagePath = "../../_y/m/r/Logo/pozitif-eticaret-logo.png";
    $logoText = "pozitif E-Ticaret";
}

include_once MODEL ."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

function getCustomCSS($languageID) {
    $files = [
        JSON_DIR . "CSS/custom-" . $languageID . ".json",
        JSON_DIR . "CSS/index-" . $languageID . ".json",
        JSON_DIR . "CSS/index.json"
    ];

    $customCSS = [];

    foreach ($files as $file) {
        if (file_exists($file)) {
            $customCSS = json_decode(file_get_contents($file), true);
            if (!empty($customCSS)) {
                break;
            }
        }
    }

    // Eğer JSON dosyalarında veri yoksa, CSS dosyasından oku
    if (empty($customCSS)) {
        $cssFiles = [
            CSS . "index-" . $languageID . ".css",
            CSS . "index.css"
        ];
        
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $customCSS = convertCSSToJSON($cssFile);
                if (!empty($customCSS)) {
                    break;
                }
            }
        }
    }

    return $customCSS;
}

function convertCSSToJSON($cssFile) {
    $css_content = file_get_contents($cssFile);
    preg_match_all('/--([^:]+):\s*([^;]+);/', $css_content, $matches, PREG_SET_ORDER);

    $json_array = [];
    foreach ($matches as $match) {
        $key = trim($match[1]);
        $value = trim($match[2]);
        $value = trim($value, "'\"");
        $json_array[$key] = $value;
    }

    return $json_array;
}

/**
 * CSS değişken referanslarını gerçek değerleriyle değiştirir
 * Örn: var(--content-bg-color) -> #fff
 */
function resolveVariables($customCSS) {
    $resolved = $customCSS;
    $changed = true;

    // Tüm referanslar çözülene kadar tekrarlayalım
    while ($changed) {
        $changed = false;

        // Tüm değerleri kontrol et
        foreach ($resolved as $key => $value) {
            // Değer var() referansı içeriyor mu?
            if (strpos($value, 'var(--') !== false) {
                // Referans edilen değişken adını çıkar
                preg_match_all('/var\(--([^)]+)\)/', $value, $matches);

                foreach ($matches[1] as $index => $varName) {
                    // Referans edilen değişkenin değerini bul
                    if (isset($resolved[$varName])) {
                        // Referansı gerçek değerle değiştir
                        $value = str_replace($matches[0][$index], $resolved[$varName], $value);
                        $changed = true;
                    }
                }

                $resolved[$key] = $value;
            }
        }
    }

    return $resolved;
}

$customCSS = getCustomCSS($languageID);

if (!empty($customCSS)) {
    $customCSS = resolveVariables($customCSS);

    $homepageProductBoxWidth = $customCSS['homepage-product-box-width'] ?? '';
    $homepageProductBoxWidth = str_replace("%", "", $homepageProductBoxWidth);
    $homepageProductBoxWidth = intval($homepageProductBoxWidth);
    $homepageProductBoxWidth += 2;
    $homepageProductBoxWidth = 100 / $homepageProductBoxWidth;
    $customCSS['calculated-homepage-product-box-width'] = $homepageProductBoxWidth;

    $categoryProductBoxWidth = $customCSS['category-product-box-width'] ?? '';
    $categoryProductBoxWidth = str_replace("%", "", $categoryProductBoxWidth);
    $categoryProductBoxWidth = intval($categoryProductBoxWidth);
    $categoryProductBoxWidth += 2;
    $categoryProductBoxWidth = 100 / $categoryProductBoxWidth;
    $customCSS['calculated-category-product-box-width'] = $categoryProductBoxWidth;


    echo '<!--';
    print_r($customCSS);
    echo '-->';
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Site Tasarımı Pozitif Eticaret</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />

		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-colorpicker/bootstrap-colorpicker.css?1422823362" />


		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."/_y/s/b/header.php");?>

		<div id="base">

			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Site Tasarım</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
                        <div class="row">
                            <div class="col-md-2 style-default-bright" style="margin-left:13px">
                                <div class="form-group">
                                    <select id="languageID" name="languageID" class="form-control">
                                        <?php
                                        foreach ($languages as $lang) {
                                            $selected = $lang['languageID'] == $languageID ? 'selected' : '';
                                            echo '<option value="'.$lang['languageID'].'" '.$selected.'>'.$lang['languageName'].'</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="help-block">Dil Seçin</p>
                                </div>
                            </div>
                        </div>
						<div class="row">
							<div class="col-md-12">
								<form name="designForm" id="designForm" class="form form-validation form-validate" role="form" method="post" action="#">
									<div class="card">
                                        <div class="card-head">
                                            <ul class="nav nav-tabs" data-toggle="tabs">
                                                <li class="active">
                                                    <a class="active" id="general-tab" data-toggle="tab" href="#general" role="tab">Genel Görünüm</a>
                                                </li>
                                                <li>
                                                    <a id="header-tab" data-toggle="tab" href="#siteHeader" role="tab">Header Görünümü</a>
                                                </li>
                                                <li>
                                                    <a id="header-tab" data-toggle="tab" href="#menu" role="tab">Menü Görünümü</a>
                                                </li>
                                                <li>
                                                    <a id="homepage-tab" data-toggle="tab" href="#homepage" role="tab">Ana Sayfa Görünümü</a>
                                                </li>
                                                <li>
                                                    <a id="footer-tab" data-toggle="tab" href="#footer" role="tab">Footer Görünümü</a>
                                                </li>
                                                <li>
                                                    <a id="header-tab" data-toggle="tab" href="#modal" role="tab">Modal</a>
                                                </li>
                                                <li>
                                                    <a id="homepage-tab" data-toggle="tab" href="#form" role="tab">Form</a>
                                                </li>
                                                <li>
                                                    <a id="footer-tab" data-toggle="tab" href="#productBox" role="tab">Anasayfa Ürün Kutu</a>
                                                </li>
                                                <li>
                                                    <a id="footer-tab" data-toggle="tab" href="#productBoxWithCategory" role="tab">Kategori Ürün Kutu</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="card-body tab-content">
                                            <div class="tab-pane active" id="general">
                                                <!-- Genel Renk Ayarları -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Genel Renk Ayarları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="primary-color">Ana Renk</label>
                                                            <input type="text" name="primary-color" id="primary-color" value="<?=$customCSS['primary-color'] ?>" class="form-control color-picker">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="secondary-color">İkincil Renk</label>
                                                            <input type="text" name="secondary-color" id="secondary-color" value="<?=$customCSS['secondary-color'] ?>" class="form-control color-picker">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="accent-color">Vurgu Rengi</label>
                                                            <input type="text" name="accent-color" id="accent-color" value="<?=$customCSS['accent-color']?>" class="form-control color-picker">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="color-demo" style="padding: 20px;">
                                                            <div id="primary-color-demo" class="generalColorBox">Ana Renk</div>
                                                            <div id="secondary-color-demo" class="generalColorBox">İkincil Renk</div>
                                                            <div id="accent-color-demo" class="generalColorBox">Vurgu Rengi</div>
                                                            <style>
                                                                .generalColorBox{
                                                                    width: 100%; height: 50px; margin-bottom: 10px; box-sizing: border-box; padding: 10px;
                                                                }
                                                            </style>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Genel Site Rengi -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Genel Site Renk ve Yazı Boyut Ayarları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="body-bg-color" id="body-bg-color" value="<?=$customCSS['body-bg-color'] ?>" class="form-control bscp" >
                                                                <label for="body-bg-color">Site Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="body-text-color" id="body-text-color" value="<?=$customCSS['body-text-color'] ?>" class="form-control bscp" >
                                                                <label for="body-text-color">Genel Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="a-color" id="a-color" value="<?=$customCSS['a-color']?>" class="form-control bscp" >
                                                                <label for="a-color">Genel Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="a-hover-color" id="a-hover-color" value="<?=$customCSS['a-hover-color'] ?>" class="form-control bscp" >
                                                                <label for="a-hover-color">Genel Link Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="content-max-width">Maksimum İçerik Genişliği:</label>
                                                                <select id="content-max-width" name="content-max-width" class="form-control">
                                                                    <option value="100%" <?=$customCSS['content-max-width']=="100%" ? "selected" : "" ?>>100%</option>
                                                                    <option value="1600px" <?=$customCSS['content-max-width']=="1600px" ? "selected" : "" ?>>1600px</option>
                                                                    <option value="1400px" <?=$customCSS['content-max-width']=="1400px" ? "selected" : "" ?>>1400px</option>
                                                                    <option value="1200px" <?=$customCSS['content-max-width']=="1200px" ? "selected" : "" ?>>1200px</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="content-bg-color" id="content-bg-color" value="<?=$customCSS['content-bg-color'] ?>" class="form-control bscp" >
                                                                <label for="content-bg-color">İçerik Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-3">
                                                            <input type="text" name="font-size-small" id="font-size-small" value="<?=$customCSS['font-size-small'] ?>" class="form-control hidden">
                                                            <div class="form-group">

                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="font-size-small-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="font-size-small-slider-value"><?=$customCSS["font-size-small"]?></div>
                                                                </div>
                                                                <label for="font-size-small-slider">Küçük Font Boyutu</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3"><input type="text" name="font-size-normal" id="font-size-normal" value="<?=$customCSS['font-size-normal'] ?>" class="form-control hidden">
                                                            <div class="form-group">

                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="font-size-normal-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="font-size-normal-slider-value"><?=$customCSS["font-size-normal"]?></div>
                                                                </div>
                                                                <label for="font-size-normal-slider">Normal Font Boyutu</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3"><input type="text" name="font-size-large" id="font-size-large" value="<?=$customCSS['font-size-large']?>" class="form-control hidden">
                                                            <div class="form-group">

                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="font-size-large-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="font-size-large-slider-value"><?=$customCSS["font-size-large"]?></div>
                                                                </div>
                                                                <label for="font-size-large-slider">Büyük Font Boyutu</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3"><input type="text" name="font-size-xlarge" id="font-size-xlarge" value="<?=$customCSS['font-size-xlarge'] ?>" class="form-control hidden">
                                                            <div class="form-group">

                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="font-size-xlarge-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="font-size-xlarge-slider-value"><?=$customCSS["font-size-xlarge"]?></div>
                                                                </div>
                                                                <label for="font-size-xlarge-slider">Çok Büyük Font Boyutu</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div id="sample-site-general" style="background-color: <?=$customCSS['body-bg-color'] ?>; color: <?=$customCSS['body-text-color'] ?>; padding: 10px;">
                                                            <div id="content-bg-color-demo" style="
                                                                margin:10px auto;
                                                                border:solid 1px #ccc;
                                                                box-sizing: border-box;
                                                                width: 80%;
                                                                background-color: <?=$customCSS['content-bg-color']?>;
                                                                padding:10px"
                                                            >
                                                                <p id="sample-font-small" style="font-size: <?=$customCSS['font-size-small'] ?>;">Küçük font örneği</p>
                                                                <p id="sample-font-normal" style="font-size: <?=$customCSS['font-size-normal']?>;">Normal font örneği</p>
                                                                <p id="sample-font-large" style="font-size: <?=$customCSS['font-size-large']?>;">Büyük font örneği</p>
                                                                <p id="sample-font-xlarge" style="font-size: <?=$customCSS['font-size-xlarge'] ?>;">Çok büyük font örneği</p>

                                                                <a href="#" id="sample-site-link" style="font-size: <?=$customCSS['font-size-normal'] ?>;color: <?=$customCSS['a-color']?>;">Örnek link</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="siteHeader">
                                                <!-- iletişim - sosyal medya -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Tepe İletişim ve Sosyal Medya</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-bg-color" id="top-contact-and-social-bg-color" value="<?=$customCSS['top-contact-and-social-bg-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-bg-color">Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-link-color" id="top-contact-and-social-link-color" value="<?=$customCSS['top-contact-and-social-link-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-link-color">Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-link-hover-color" id="top-contact-and-social-link-hover-color" value="<?=$customCSS['top-contact-and-social-link-hover-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-link-hover-color">Link Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-icon-color" id="top-contact-and-social-icon-color" value="<?=$customCSS['top-contact-and-social-icon-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-icon-color">İkon Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-icon-hover-color" id="top-contact-and-social-icon-hover-color" value="<?=$customCSS['top-contact-and-social-icon-hover-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-icon-hover-color">İkon Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-container-margin-top" id="top-contact-and-social-container-margin-top" value="<?=$customCSS['top-contact-and-social-container-margin-top'] ?? 0?>" class="form-control">
                                                                <label for="top-contact-and-social-container-margin-top">SM ve İletişim ikonları üst boşluk</label>
                                                                <span class="form-text text-muted">Örnek: 20px</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="sample-contact-social" style="background-color: <?=$customCSS['top-contact-and-social-bg-color']?>; padding: 5px 10px; display: inline-block; width: 100%;  line-height: 40px;">
                                                            <div class="col-sm-6" style="text-align: left;">
                                                                <i class="fa fa-facebook sample-social-icon btn" style="color: <?=$customCSS['top-contact-and-social-icon-color']?>;"></i>
                                                                <i class="fa fa-twitter sample-social-icon btn" style="color: <?=$customCSS['top-contact-and-social-icon-color']?>;"></i>
                                                                <i class="fa fa-youtube sample-social-icon btn" style="color: <?=$customCSS['top-contact-and-social-icon-color']?>;"></i>
                                                            </div>
                                                            <div class="col-sm-6" style="text-align: right;">
                                                                <div>
                                                                    <a href="#" class="sample-contact-link btn" style="color: <?=$customCSS['top-contact-and-social-link-color']?>;">
                                                                        <i class="fa fa-phone sample-social-icon"></i> Telefon
                                                                    </a>
                                                                    <a href="#" class="sample-contact-link btn" style="color: <?=$customCSS['top-contact-and-social-link-color']?>;">
                                                                        <i class="fa fa-whatsapp sample-social-icon"></i> Whatsapp
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-bg-color-mobile" id="top-contact-and-social-bg-color-mobile" value="<?=$customCSS['top-contact-and-social-bg-color-mobile'] ?? $customCSS['top-contact-and-social-bg-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-bg-color-mobile">Mobil Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-link-color-mobile" id="top-contact-and-social-link-color-mobile" value="<?=$customCSS['top-contact-and-social-link-color-mobile'] ?? $customCSS['top-contact-and-social-link-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-link-color-mobile">Mobil Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-link-hover-color-mobile" id="top-contact-and-social-link-hover-color-mobile" value="<?=$customCSS['top-contact-and-social-link-hover-color-mobile'] ?? $customCSS['top-contact-and-social-link-hover-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-link-hover-color-mobile">Mobil Link Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-icon-color-mobile" id="top-contact-and-social-icon-color-mobile" value="<?=$customCSS['top-contact-and-social-icon-color-mobile'] ?? $customCSS['top-contact-and-social-icon-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-icon-color-mobile">Mobil İkon Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-icon-hover-color-mobile" id="top-contact-and-social-icon-hover-color-mobile" value="<?=$customCSS['top-contact-and-social-icon-hover-color-mobile'] ?? $customCSS['top-contact-and-social-icon-hover-color']?>" class="form-control bscp" >
                                                                <label for="top-contact-and-social-icon-hover-color-mobile">Mobil İkon Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-contact-and-social-container-mobile-margin-top" id="top-contact-and-social-container-mobile-margin-top" value="<?=$customCSS['top-contact-and-social-container-mobile-margin-top'] ?? "80px"?>" class="form-control">
                                                                <label for="top-contact-and-social-container-mobile-margin-top">Mobil SM ve İletişim ikonları üst boşluk</label>
                                                                <span class="form-text text-muted">Örnek: 80px</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- header -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Header - Alışveriş Menü İkonları - Logo</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" name="header-bg-color" id="header-bg-color" value="<?=$customCSS['header-bg-color']?>" class="form-control bscp" >
                                                                <label for="header-bg-color">Header Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-min-height" id="header-min-height" value="<?=$customCSS['header-min-height']?>" class="form-control" >
                                                                <label for="header-min-height">Header Minimum Yükseklik</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-min-height-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-min-height-slider-value"><?=$customCSS['header-min-height']?></div>
                                                                </div>
                                                                <label for="header-min-height-slider">Header Minimum Yükseklik</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-mobile-min-height-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-mobile-min-height-slider-value"><?=$customCSS['header-mobile-min-height']?></div>
                                                                </div>
                                                                <label for="header-mobile-min-height-slider">Header Mobil Minimum Yükseklik</label>
                                                            </div>
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-mobile-min-height" id="header-mobile-min-height" value="<?=$customCSS['header-mobile-min-height']?>" class="form-control" >
                                                                <label for="header-mobile-min-height">Header Mobil Minimum Yükseklik</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- header alış veriş menüsü -->
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="shop-menu-container-icon-color-search" id="shop-menu-container-icon-color-search" value="<?=$customCSS['shop-menu-container-icon-color-search']?>" class="form-control bscp" >
                                                                <label for="shop-menu-container-icon-color-search">Arama İkonu Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="shop-menu-container-icon-color-member" id="shop-menu-container-icon-color-member" value="<?=$customCSS['shop-menu-container-icon-color-member']?>" class="form-control bscp" >
                                                                <label for="shop-menu-container-icon-color-member">Üye İkonu Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="shop-menu-container-icon-color-favorites" id="shop-menu-container-icon-color-favorites" value="<?=$customCSS['shop-menu-container-icon-color-favorites']?>" class="form-control bscp" >
                                                                <label for="shop-menu-container-icon-color-favorites">Favoriler İkonu Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="shop-menu-container-icon-color-basket" id="shop-menu-container-icon-color-basket" value="<?=$customCSS['shop-menu-container-icon-color-basket']?>" class="form-control bscp" >
                                                                <label for="shop-menu-container-icon-color-basket">Sepet İkonu Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="shop-menu-container-icon-hover-color" id="shop-menu-container-icon-hover-color" value="<?=$customCSS['shop-menu-container-icon-hover-color']?>" class="form-control bscp" >
                                                                <label for="shop-menu-container-icon-hover-color">İkonlar Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="sample-header" class="card" style="background-color: <?=$customCSS['header-bg-color']?>; padding: 10px;">
                                                            <div id="sample-header-logo" class="col-md-3" style="min-height: <?=$customCSS['header-min-height']?>">
                                                                <img src="<?=imgRoot.$logoImagePath?>" alt="Logo" style="width: 150px; height: auto;">
                                                            </div>
                                                            <div id="sample-header-search" class="col-md-6">
                                                                <!-- göstermelik arama kutusu yapalım -->
                                                                <div class="form-group">
                                                                    <div class="col-md-8">
                                                                        <input type="text" class="form-control" placeholder="Arama yapın">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <i class="fa fa-search"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="sample-header-shop" class="col-md-3">
                                                                <i class="fa fa-search" id="sample-icon-search" style="color: <?=$customCSS['shop-menu-container-icon-color-search']?>; font-size: 24px; margin-right: 10px;"></i>
                                                                <i class="fa fa-user" id="sample-icon-member" style="color: <?=$customCSS['shop-menu-container-icon-color-member']?>; font-size: 24px; margin-right: 10px;"></i>
                                                                <i class="fa fa-heart" id="sample-icon-favorites" style="color: <?=$customCSS['shop-menu-container-icon-color-favorites']?>; font-size: 24px; margin-right: 10px;"></i>
                                                                <i class="fa fa-shopping-cart" id="sample-icon-basket" style="color: <?=$customCSS['shop-menu-container-icon-color-basket']?>; font-size: 24px;"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- logo-->
                                                <div class="card-body">
                                                    <?php
                                                    $headerLogoWidth = $customCSS['header-logo-width'];
                                                    $headerLogoMobileWidth = $customCSS['header-logo-mobile-width'];

                                                    $headerLogoMarginTop = isset($customCSS['header-logo-margin-top']) ? str_replace("px", "", $customCSS['header-logo-margin-top']) : 0;
                                                    $headerLogoMarginRight = isset($customCSS['header-logo-margin-right']) ? str_replace("px", "", $customCSS['header-logo-margin-right']) : 0;
                                                    $headerLogoMarginBottom = isset($customCSS['header-logo-margin-bottom']) ? str_replace("px", "", $customCSS['header-logo-margin-bottom']) : 0;
                                                    $headerLogoMarginLeft = isset($customCSS['header-logo-margin-left']) ? str_replace("px", "", $customCSS['header-logo-margin-left']) : 0;

                                                    $headerLogoMarginTop = intval($headerLogoMarginTop);
                                                    $headerLogoMarginRight = intval($headerLogoMarginRight);
                                                    $headerLogoMarginBottom = intval($headerLogoMarginBottom);
                                                    $headerLogoMarginLeft = intval($headerLogoMarginLeft);



                                                    $headerMobileLogoMarginTop = isset($customCSS['header-mobile-logo-margin-top']) ? str_replace("px", "", $customCSS['header-mobile-logo-margin-top']) : 0;
                                                    $headerMobileLogoMarginRight = isset($customCSS['header-mobile-logo-margin-right']) ? str_replace("px", "", $customCSS['header-mobile-logo-margin-right']) : 0;
                                                    $headerMobileLogoMarginBottom = isset($customCSS['header-mobile-logo-margin-bottom']) ? str_replace("px", "", $customCSS['header-mobile-logo-margin-bottom']) : 0;
                                                    $headerMobileLogoMarginLeft = isset($customCSS['header-mobile-logo-margin-left']) ? str_replace("px", "", $customCSS['header-mobile-logo-margin-left']) : 0;

                                                    ?>
                                                    <div class="col-md-6">
                                                        <div class="col-md-12">
                                                            <div class="col-md-6">
                                                                <div class="form-group hidden">
                                                                    <input type="text" name="header-logo-width" id="header-logo-width" value="<?=$headerLogoWidth?>" class="form-control" >
                                                                    <label for="header-logo-width">Logo Genişliği</label>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <div class="input-group-content form-control-static">
                                                                            <div id="header-logo-width-slider"></div>
                                                                        </div>
                                                                        <div class="input-group-addon" id="header-logo-width-slider-value"><?=$headerLogoWidth?></div>
                                                                    </div>
                                                                    <label for="header-logo-width-slider">Logo Genişliği</label>
                                                                </div>
                                                                <img id="header-logo" src="<?=imgRoot.$logoImagePath?>" alt="Logo" style="width: <?=$headerLogoWidth?>; height: auto;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-logo-margin-top" id="header-logo-margin-top" value="<?=$headerLogoMarginTop?>px" class="form-control" >
                                                                <label for="header-logo-margin-top">Logo Üst Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-logo-margin-top-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-logo-margin-top-slider-value"><?=$headerLogoMarginTop?></div>
                                                                </div>
                                                                <label for="header-logo-margin-top-slider">Logo Üst Kenar Boşluğu</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-logo-margin-right" id="header-logo-margin-right" value="<?=$headerLogoMarginRight?>px" class="form-control" >
                                                                <label for="header-logo-margin-right">Logo Sağ Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-logo-margin-right-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-logo-margin-right-slider-value"><?=$headerLogoMarginRight?></div>
                                                                </div>
                                                                <label for="header-logo-margin-right-slider">Logo Sağ Kenar Boşluğu</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-logo-margin-bottom" id="header-logo-margin-bottom" value="<?=$headerLogoMarginBottom?>px" class="form-control" >
                                                                <label for="header-logo-margin-bottom">Logo Alt Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-logo-margin-bottom-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-logo-margin-bottom-slider-value"><?=$headerLogoMarginBottom?></div>
                                                                </div>
                                                                <label for="header-logo-margin-bottom-slider">Logo Alt Kenar Boşluğu</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-logo-margin-left" id="header-logo-margin-left" value="<?=$headerLogoMarginLeft?>px" class="form-control" >
                                                                <label for="header-logo-margin-left">Logo Sol Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-logo-margin-left-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-logo-margin-left-slider-value"><?=$headerLogoMarginLeft?></div>
                                                                </div>
                                                                <label for="header-logo-margin-left-slider">Logo Sol Kenar Boşluğu</label>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-logo-mobile-width" id="header-logo-mobile-width" value="<?=$headerLogoMobileWidth?>" class="form-control" >
                                                                <label for="header-logo-mobile-width">Mobil Logo Genişliği</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-logo-mobile-width-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-logo-mobile-width-slider-value"><?=$headerLogoMobileWidth?></div>
                                                                </div>
                                                                <label for="header-logo-mobile-width-slider">Mobil Logo Genişliği</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <img id="header-logo-mobile" src="<?=imgRoot.$logoImagePath?>" alt="Logo" style="width: <?=$headerLogoMobileWidth?>; height: auto;">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-mobile-logo-margin-top" id="header-mobile-logo-margin-top" value="<?=$headerMobileLogoMarginTop?>" class="form-control" >
                                                                <label for="header-mobile-logo-margin-top">Mobil Logo Üst Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-mobile-logo-margin-top-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-mobile-logo-margin-top-slider-value"><?=$headerMobileLogoMarginTop?></div>
                                                                </div>
                                                                <label for="header-mobile-logo-margin-top-slider">Mobil Logo Üst Kenar Boşluğu</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-mobile-logo-margin-right" id="header-logo-margin-right" value="<?=$headerMobileLogoMarginRight?>" class="form-control" >
                                                                <label for="header-mobile-logo-margin-right">Mobil Logo Sağ Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-mobile-logo-margin-right-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-mobile-logo-margin-right-slider-value"><?=$headerMobileLogoMarginRight?></div>
                                                                </div>
                                                                <label for="header-mobile-logo-margin-right-slider">Mobil Logo Sağ Kenar Boşluğu</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-mobile-logo-margin-bottom" id="header-mobile-logo-margin-bottom" value="<?=$headerMobileLogoMarginBottom?>" class="form-control" >
                                                                <label for="header-mobile-logo-margin-bottom">Mobil Logo Alt Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-mobile-logo-margin-bottom-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-mobile-logo-margin-bottom-slider-value"><?=$headerMobileLogoMarginBottom?></div>
                                                                </div>
                                                                <label for="header-mobile-logo-margin-bottom-slider">Mobil Logo Alt Kenar Boşluğu</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group hidden">
                                                                <input type="text" name="header-mobile-logo-margin-left" id="header-mobile-logo-margin-left" value="<?=$headerMobileLogoMarginLeft?>" class="form-control" >
                                                                <label for="header-logo-margin-left">Mobile Logo Sol Kenar Boşluğu</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="header-mobile-logo-margin-left-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="header-mobile-logo-margin-left-slider-value"><?=$headerMobileLogoMarginLeft?></div>
                                                                </div>
                                                                <label for="header-mobile-logo-margin-left-slider">Mobil Logo Sol Kenar Boşluğu</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="tab-pane" id="menu">
                                                <!--Menü -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Ana Menü</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-bg-color" id="main-menu-bg-color" value="<?=$customCSS['main-menu-bg-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-bg-color">Menü Kutu Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-link-bg-color" id="main-menu-link-bg-color" value="<?=$customCSS['main-menu-link-bg-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-bg-color">Menü Link Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-link-hover-bg-color" id="main-menu-link-hover-bg-color" value="<?=$customCSS['main-menu-link-hover-bg-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-link-hover-bg-color">Menü Link Hover Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-link-color" id="main-menu-link-color" value="<?=$customCSS['main-menu-link-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-link-color">Menü Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-link-hover-color" id="main-menu-link-hover-color" value="<?=$customCSS['main-menu-link-hover-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-link-hover-color">Menü Link Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <?php
                                                            $fontSizeMainMenu = $customCSS['font-size-main-menu'] ? str_replace("px", "", $customCSS['font-size-main-menu']) : $customCSS['font-size-normal'];
                                                            ?>
                                                            <input type="text" name="font-size-main-menu" id="font-size-main-menu" value="<?=$fontSizeMainMenu?>px" class="form-control hidden">
                                                            <div class="form-group">

                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="font-size-main-menu-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="font-size-main-menu-slider-value"><?=$fontSizeMainMenu?>px</div>
                                                                </div>
                                                                <label for="font-size-main-menu-slider">Menü Font Boyutu</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-ul-bg-color" id="main-menu-ul-bg-color" value="<?=$customCSS['main-menu-ul-bg-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-ul-bg-color">Alt Menü Kutu Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-ul-submenu-link-bg-color" id="main-menu-ul-submenu-link-bg-color" value="<?=$customCSS['main-menu-ul-submenu-link-bg-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-ul-submenu-link-bg-color">Alt Menü Link Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-ul-submenu-link-hover-bg-color" id="main-menu-ul-submenu-link-hover-bg-color" value="<?=$customCSS['main-menu-ul-submenu-link-hover-bg-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-ul-submenu-link-hover-bg-color">Alt Menü Link Hover Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-ul-submenu-link-color" id="main-menu-ul-submenu-link-color" value="<?=$customCSS['main-menu-ul-submenu-link-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-ul-submenu-link-color">Alt Menü Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="main-menu-ul-submenu-link-hover-color" id="main-menu-ul-submenu-link-hover-color" value="<?=$customCSS['main-menu-ul-submenu-link-hover-color']?>" class="form-control bscp" >
                                                                <label for="main-menu-ul-submenu-link-hover-color">Alt Menü Link Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <?php
                                                            $fontSizeMainMenuMobile = $customCSS['font-size-main-submenu'] ? str_replace("px","", $customCSS['font-size-main-submenu']) : $customCSS['font-size-normal'];
                                                            ?>
                                                            <input type="text" name="font-size-main-submenu" id="font-size-main-submenu" value="<?=$fontSizeMainMenuMobile?>px" class="form-control hidden">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="font-size-main-submenu-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="font-size-main-submenu-slider-value"><?=$fontSizeMainMenuMobile?></div>
                                                                </div>
                                                                <label for="font-size-main-submenu-slider">Alt Menü Font Boyutu</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-1"></div>
                                                        <div class="col-md-8">
                                                            <ul id="sample-menu">
                                                                <li><a href="#" >Menü Öğesi 1</a></li>
                                                                <li><a href="#">Menü Öğesi 2</a></li>
                                                                <li>
                                                                    <a href="#">Menü Öğesi 3</a>
                                                                    <ul>
                                                                        <li><a href="#">Alt Menü Öğesi 1</a></li>
                                                                        <li><a href="#">Alt Menü Öğesi 2</a></li>
                                                                    </ul>
                                                                </li>
                                                                <li><a href="#">Menü Öğesi 4</a></li>
                                                            </ul>
                                                            <style>
                                                                ul#sample-menu  {
                                                                    background-color: <?=$customCSS['main-menu-link-bg-color']?>;
                                                                    padding: 10px; width: 100%; list-style: none;
                                                                    min-height: 55px;
                                                                }
                                                                ul#sample-menu li{padding: 5px 10px}
                                                                ul#sample-menu>li{
                                                                    background-color: <?=$customCSS['main-menu-bg-color']?>;
                                                                    float: left; max-width: 200px;display: inline-block;margin-left:5px
                                                                }
                                                                ul#sample-menu>li:hover{
                                                                    background-color: <?=$customCSS['main-menu-link-hover-bg-color']?>;
                                                                }
                                                                ul#sample-menu>li>a {
                                                                    color: <?=$customCSS['main-menu-link-color']?>;
                                                                }
                                                                ul#sample-menu>li> a:hover {
                                                                    color: <?=$customCSS['main-menu-link-hover-color']?>;
                                                                }

                                                                ul#sample-menu ul {
                                                                    background-color: <?=$customCSS['main-menu-ul-bg-color']?>; list-style: none;
                                                                    padding: 5px 10px;
                                                                }
                                                                ul#sample-menu ul li {
                                                                    background-color: <?=$customCSS['main-menu-ul-submenu-link-bg-color']?>; margin-bottom: 5px;
                                                                }
                                                                ul#sample-menu ul li:hover {
                                                                    background-color: <?=$customCSS['main-menu-ul-submenu-link-hover-bg-color']?>;
                                                                }
                                                                ul#sample-menu ul li a {
                                                                    color: <?=$customCSS['main-menu-ul-submenu-link-color']?>;
                                                                }
                                                                ul#sample-menu ul li a:hover {
                                                                    color: <?=$customCSS['main-menu-ul-submenu-link-hover-color']?>;
                                                                }
                                                            </style>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="homepage">
                                                <!-- Anasayfa -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Ana Sayfa Başlığı</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <input type="text" name="homepage-h1-color" id="homepage-h1-color" value="<?=$customCSS['homepage-h1-color']?>" class="form-control bscp" >
                                                            <label for="homepage-h1-color">Ana Sayfa Başlık Rengi</label>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" name="homepage-h1-font-size" id="homepage-h1-font-size" value="<?=$customCSS['homepage-h1-font-size']?>" class="form-control hidden" >
                                                            <div class="input-group">
                                                                <div class="input-group-content form-control-static">
                                                                    <div id="homepage-h1-font-size-slider"></div>
                                                                </div>
                                                                <div class="input-group-addon" id="homepage-h1-font-size-value"><?=$customCSS["homepage-h1-font-size"]?></div>
                                                            </div>
                                                            <label for="homepage-h1-font-size">Ana Sayfa Başlık Boyutu</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1"></div>
                                                    <div class="col-md-8">
                                                        <h1 id="sample-homepage-h1" style="color: <?=$customCSS['homepage-h1-color']?>; font-size: <?=$customCSS['homepage-h1-font-size']?>;">Fırsat ürünleri Başlık</h1>
                                                    </div>
                                                </div>
                                                <!-- Banner Alanları -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Banner Alanları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="card-head card-head-xs bg-info">
                                                        <header>Üst Banner Görünümü</header>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-banner-bg-color" id="top-banner-bg-color" value="<?=$customCSS['top-banner-bg-color']?>" class="form-control bscp" >
                                                                <label for="top-banner-bg-color">Üst Banner Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-banner-h1-color" id="top-banner-h1-color" value="<?=$customCSS['top-banner-h1-color']?>" class="form-control bscp" >
                                                                <label for="top-banner-h1-color">Üst Banner Başlık Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2"><input type="text" name="top-banner-h1-font-size" id="top-banner-h1-font-size" value="<?=$customCSS['top-banner-h1-font-size']?>" class="form-control hidden">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="top-banner-h1-font-size-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="top-banner-h1-font-size-slider-value"><?=$customCSS["top-banner-h1-font-size"]?></div>
                                                                </div>
                                                                <label for="top-banner-h1-font-size-slider">Banner Başlık Yazı Boyutu</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="top-banner-p-color" id="top-banner-p-color" value="<?=$customCSS['top-banner-p-color']?>" class="form-control bscp" >
                                                                <label for="top-banner-p-color">Üst Banner Paragraf Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2"><input type="text" name="top-banner-p-font-size" id="top-banner-p-font-size" value="<?=$customCSS['top-banner-p-font-size']?>" class="form-control hidden">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-content form-control-static">
                                                                        <div id="top-banner-p-font-size-slider"></div>
                                                                    </div>
                                                                    <div class="input-group-addon" id="top-banner-p-font-size-slider-value"><?=$customCSS["top-banner-p-font-size"]?></div>
                                                                </div>
                                                                <label for="top-banner-hp-font-size-slider">Banner İçerik Yazı Boyutu</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div id="sample-top-banner" style="background-color: <?=$customCSS['top-banner-bg-color']?>; padding: 10px; text-align: center">
                                                            <h1 style="color: <?=$customCSS['top-banner-h1-color']?>; font-size: <?=$customCSS['top-banner-h1-font-size']?>">Örnek Banner Başlığı</h1>
                                                            <p style="color: <?=$customCSS['top-banner-p-color']?>; font-size: <?=$customCSS['top-banner-p-font-size']?>">Örnek Banner Metni</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="card-head card-head-xs bg-info">
                                                        <header>Orta Bannerlar</header>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <select name="middle-content-banner-width" id="middle-content-banner-width"  class="form-control" >
                                                                    <option value="100%" <?=$customCSS['middle-content-banner-width'] == "100%" ? "selected" : ""?>>Tam Boy</option>
                                                                    <option value="50%" <?=$customCSS['middle-content-banner-width'] == "50%" ? "selected" : ""?>>İkili</option>
                                                                    <option value="25%" <?=$customCSS['middle-content-banner-width'] == "25%" ? "selected" : ""?>>Dörtlü</option>
                                                                    <option value="16%" <?=$customCSS['middle-content-banner-width'] == "16%" ? "selected" : ""?>>Altılı</option>
                                                                </select>
                                                                <label for="middle-content-banner-width">Orta İçerik Banner Genişliği</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <?php
                                                            //swich case yapısı ile banner genişliğine göre div sayısı ve classları belirlenecek
                                                            switch ($customCSS['middle-content-banner-width']) {
                                                                case '100%':
                                                                    $bannerCount = 1;
                                                                    $bannerClass = "col-md-12";
                                                                    break;
                                                                case '50%':
                                                                    $bannerCount = 2;
                                                                    $bannerClass = "col-md-6";
                                                                    break;
                                                                case '25%':
                                                                    $bannerCount = 4;
                                                                    $bannerClass = "col-md-3";
                                                                    break;
                                                                case '16%':
                                                                    $bannerCount = 6;
                                                                    $bannerClass = "col-md-2";
                                                                    break;
                                                                default:
                                                                    $bannerCount = 1;
                                                                    $bannerClass = "col-md-12";
                                                                    break;
                                                            }                                                 ?>
                                                            <div class="<?=$bannerClass?> bg-warning sampleMiddleBanner" style="height: 100px; margin:5px 0"> Banner </div>
                                                            <div class="<?=$bannerClass?> bg-danger sampleMiddleBanner" style="height: 100px; margin:5px 0"> Banner </div>
                                                            <div class="<?=$bannerClass?> bg-info sampleMiddleBanner" style="height: 100px; margin:5px 0"> Banner </div>
                                                            <div class="<?=$bannerClass?> bg-success sampleMiddleBanner" style="height: 100px; margin:5px 0"> Banner </div>
                                                            <div class="<?=$bannerClass?> bg-danger sampleMiddleBanner" style="height: 100px; margin:5px 0"> Banner </div>
                                                            <div class="<?=$bannerClass?> bg-warning sampleMiddleBanner" style="height: 100px; margin:5px 0"> Banner </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="card-head card-head-xs bg-info">
                                                        <header>Alt Banner Görünümü</header>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <select name="bottom-banner-width" id="bottom-banner-width" class="form-control">
                                                                    <option value="100%" <?=$customCSS['bottom-banner-width'] == "100%" ? "selected" : "" ?>>Tam Boy</option>
                                                                    <option value="50%" <?=$customCSS['bottom-banner-width'] == "50%" ? "selected" : "" ?>>Yarım Boy</option>
                                                                    <option value="25%" <?=$customCSS['bottom-banner-width'] == "25%" ? "selected" : "" ?>>Dörtte Bir</option>
                                                                    <option value="10%" <?=$customCSS['bottom-banner-width'] == "10%" ? "selected" : "" ?>>Küçük</option>
                                                                </select>
                                                                <label for="bottom-banner-width">Alt Banner Genişliği</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <?php
                                                            //swich case yapısı ile banner genişliğine göre div sayısı ve classları belirlenecek
                                                            switch ($customCSS['bottom-banner-width']) {
                                                                case '100%':
                                                                    $bannerCount = 1;
                                                                    $bannerClass = "col-md-12";
                                                                    break;
                                                                case '50%':
                                                                    $bannerCount = 2;
                                                                    $bannerClass = "col-md-6";
                                                                    break;
                                                                case '25%':
                                                                    $bannerCount = 4;
                                                                    $bannerClass = "col-md-3";
                                                                    break;
                                                                case '10%':
                                                                    $bannerCount = 10;
                                                                    $bannerClass = "col-md-1";
                                                                    break;
                                                                default:
                                                                    $bannerCount = 1;
                                                                    $bannerClass = "col-md-12";
                                                                    break;
                                                            }
                                                            ?>
                                                            <div class="bottomBannerSample <?=$bannerClass?>" style="background-color: #f0f0f0; height: 100px; padding: 10px; float:none; margin:0 auto;">Alt Banner</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="productBox">
                                                <!-- ürün kutu-->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Anasayfa Ürün Kutuları Görünümü</header>
                                                </div>
                                                <div class="card-body">

                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="homepage-product-box-bg-color" id="homepage-product-box-bg-color" value="<?=$customCSS['homepage-product-box-bg-color']?>" class="form-control bscp" >
                                                                <label for="homepage-product-box-bg-color">Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="homepage-product-box-hover-bg-color" id="homepage-product-box-hover-bg-color" value="<?=$customCSS['homepage-product-box-hover-bg-color']?>" class="form-control bscp" >
                                                                <label for="homepage-product-box-hover-bg-color">Hover Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="homepage-product-box-link-color" id="homepage-product-box-link-color" value="<?=$customCSS['homepage-product-box-link-color']?>" class="form-control bscp" >
                                                                <label for="homepage-product-box-link-color">Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="homepage-product-box-price-color" id="homepage-product-box-price-color" value="<?=$customCSS['homepage-product-box-price-color']?>" class="form-control bscp" >
                                                                <label for="homepage-product-box-link-color">Fiyat Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="homepage-product-box-color" id="homepage-product-box-color" value="<?=$customCSS['homepage-product-box-color']?>" class="form-control bscp" >
                                                                <label for="homepage-product-box-color">Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select name="homepage-product-box-width" id="homepage-product-box-width" class="form-control">
                                                                <option value="48%" <?=$homepageProductBoxWidth == 2 ? "selected" : ""?>>2</option>
                                                                <option value="31%" <?=$homepageProductBoxWidth == 3 ? "selected" : ""?>>3</option>
                                                                <option value="23%" <?=$homepageProductBoxWidth == 4 ? "selected" : ""?>>4</option>
                                                                <option value="18%" <?=$homepageProductBoxWidth == 5 ? "selected" : ""?>>5</option>
                                                                <option value="14.6%" <?=$homepageProductBoxWidth == 6 ? "selected" : ""?>>6</option>
                                                            </select>
                                                            <label for="homepage-product-box-width">Bir satırda kaç ürün olsun</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div id="productBoxContainer">
                                                            <?php for ($i=1; $i <= $homepageProductBoxWidth; $i++) { ?>
                                                                <div
                                                                        class="sample-product-box"
                                                                        style="background-color: <?=$customCSS['homepage-product-box-bg-color']?>;
                                                                            color: <?=$customCSS['homepage-product-box-color']?>; width: <?=$customCSS['homepage-product-box-width']?>;
                                                                            margin: 10px 1%;">
                                                                    <img src="/_y/assets/img/img1.jpg" alt="Ürün Resmi" style="width: 100%; height: auto">
                                                                    <a href="#" class="sample-product-link" style="color: <?=$customCSS['homepage-product-box-link-color']?>;">Ürün Başlık</a>
                                                                    <p style="color: <?=$customCSS['homepage-product-box-price-color']?>">100 TL</p>
                                                                    <span style="<?=$customCSS['homepage-product-box-color']?>">Ürün Kategori</span>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="productBoxWithCategory">
                                                <!-- ürün kutu-->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Arama Ürün Kutuları Görünümü</header>
                                                </div>
                                                <div class="card-body">

                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="category-product-box-bg-color" id="category-product-box-bg-color" value="<?=$customCSS['category-product-box-bg-color']?>" class="form-control bscp" >
                                                                <label for="category-product-box-bg-color">Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="category-product-box-hover-bg-color" id="category-product-box-hover-bg-color" value="<?=$customCSS['category-product-box-hover-bg-color']?>" class="form-control bscp" >
                                                                <label for="category-product-box-hover-bg-color">Hover Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="category-product-box-link-color" id="category-product-box-link-color" value="<?=$customCSS['category-product-box-link-color']?>" class="form-control bscp" >
                                                                <label for="category-product-box-link-color">Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="category-product-box-price-color" id="category-product-box-price-color" value="<?=$customCSS['category-product-box-price-color']?>" class="form-control bscp" >
                                                                <label for="category-product-box-link-color">Fiyat Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="category-product-box-color" id="category-product-box-color" value="<?=$customCSS['category-product-box-color']?>" class="form-control bscp" >
                                                                <label for="category-product-box-color">Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select name="category-product-box-width" id="category-product-box-width" class="form-control">
                                                                <option value="48%" <?=$categoryProductBoxWidth == 2 ? "selected" : ""?>>2</option>
                                                                <option value="31%" <?=$categoryProductBoxWidth == 3 ? "selected" : ""?>>3</option>
                                                                <option value="23%" <?=$categoryProductBoxWidth == 4 ? "selected" : ""?>>4</option>
                                                                <option value="18%" <?=$categoryProductBoxWidth == 5 ? "selected" : ""?>>5</option>
                                                                <option value="14.6%" <?=$categoryProductBoxWidth == 6 ? "selected" : ""?>>6</option>
                                                            </select>
                                                            <label for="homepage-product-box-width">Bir satırda kaç ürün olsun</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div id="categoryProductBoxContainer">
                                                            <?php for ($i=1; $i <= $categoryProductBoxWidth; $i++) { ?>
                                                                <div
                                                                    class="sample-category-product-box"
                                                                    style="background-color: <?=$customCSS['category-product-box-bg-color']?>;
                                                                            color: <?=$customCSS['category-product-box-color']?>; width: <?=$customCSS['category-product-box-width']?>;
                                                                            margin: 10px 1%;">
                                                                    <img src="/_y/assets/img/img1.jpg" alt="Ürün Resmi" style="width: 100%; height: auto">
                                                                    <a href="#" class="sample-category-product-link" style="color: <?=$customCSS['category-product-box-link-color']?>;">Ürün Başlık</a>
                                                                    <p style="color: <?=$customCSS['category-product-box-price-color']?>">100 TL</p>
                                                                    <span style="<?=$customCSS['category-product-box-color']?>">Ürün Kategori</span>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="footer">
                                                <!-- footer rengi -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Footer Ayarları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-bg-color" id="footer-bg-color" value="<?=$customCSS['footer-bg-color']?>" class="form-control bscp" >
                                                                <label for="footer-bg-color">Footer Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-text-color" id="footer-text-color" value="<?=$customCSS['footer-text-color'] ?>" class="form-control bscp" >
                                                                <label for="footer-text-color">Footer Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-link-color" id="footer-link-color" value="<?=$customCSS['footer-link-color'] ?>" class="form-control bscp" >
                                                                <label for="footer-link-color">Footer Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-menu-bg-color" id="footer-menu-bg-color" value="<?=$customCSS['footer-menu-bg-color']?>" class="form-control bscp" >
                                                                <label for="footer-menu-bg-color">Footer Menü Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-menu-link-color" id="footer-menu-link-color" value="<?=$customCSS['footer-menu-link-color'] ?>" class="form-control bscp" >
                                                                <label for="footer-menu-link-color">Footer Menü Link Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-menu-link-hover-color" id="footer-menu-link-hover-color" value="<?=$customCSS['footer-menu-link-hover-color']?>" class="form-control bscp" >
                                                                <label for="footer-menu-link-hover-color">Footer Menü Link Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="sample-footer"
                                                             style="background-color: <?=$customCSS['footer-bg-color']?>;
                                                             color: <?=$customCSS['footer-text-color'] ?>; padding: 10px;">
                                                            <p>Örnek footer metni - <a href="#" style="<?=$customCSS['footer-link-color']?>">Örnek Link metni</a></p>
                                                            <div id="sample-footer-menu"
                                                                 style="background-color: <?=$customCSS['footer-menu-bg-color']?>; padding: 5px;">
                                                                <a href="#" class="sample-footer-link"
                                                                   style="color: <?=$customCSS['footer-menu-link-color'] ?>;">Footer Menü Öğesi 1</a>
                                                                <a href="#" class="sample-footer-link"
                                                                   style="color: <?=$customCSS['footer-menu-link-color'] ?>; margin-left: 10px;">Footer Menü Öğesi 2</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-logo-width" id="footer-logo-width" value="<?=$customCSS["footer-logo-width"] ?? 400 ?>" class="form-control">
                                                                <label for="footer-logo-width">Footer Logo Genişliği (px)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="footer-logo-height" id="footer-logo-height" value="<?=$customCSS["footer-logo-height"] ?? 400 ?>" class="form-control">
                                                                <label for="footer-logo-height">Footer Logo Yüksekliği (px)</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="form">
                                                <!-- input -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Form Alanları</header>
                                                </div>
                                                <div class="card-body">

                                                    <?php
                                                    $inputBoderColor = (!empty($customCSS['input-border'])) ? str_replace("1px solid ","", $customCSS['input-border']) : "#dedede";
                                                    ?>

                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="input-color" id="input-color" value="<?=$customCSS['input-color']?>" class="form-control bscp" >
                                                                <label for="input-color">Input Yazı Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="input-bg-color" id="input-bg-color" value="<?=$customCSS['input-bg-color']?>" class="form-control bscp" >
                                                                <label for="input-bg-color">Input Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="input-focus-color" id="input-focus-color" value="<?=$customCSS['input-focus-color']?>" class="form-control bscp" >
                                                                <label for="input-focus-color">Input Focus Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="input-border" id="input-border" value="<?=$inputBoderColor?>" class="form-control bscp" >
                                                                <label for="input-border">Input Border</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="form-label-color" id="form-label-color" value="<?=$customCSS['form-label-color']?>" class="form-control bscp" >
                                                                <label for="form-label-color">İnput Label Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="form-placeholder-color" id="form-placeholder-color" value="<?=$customCSS['form-placeholder-color']?>" class="form-control bscp" >
                                                                <label for="form-placeholder-color">İnput Placeholder Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="select-text-color" id="select-text-color" value="<?=$customCSS['select-text-color']?>" class="form-control bscp" >
                                                                <label for="select-text-color">Select Yazı Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="select-bg-color" id="select-bg-color" value="<?=$customCSS['select-bg-color']?>" class="form-control bscp" >
                                                                <label for="select-bg-color">Select Arkaplan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="select-focus-color" id="select-focus-color" value="<?=$customCSS['select-focus-color']?>" class="form-control bscp" >
                                                                <label for="select-focus-color">Select Focus Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="form-error-color" id="form-error-color" value="<?=$customCSS['form-error-color']?>" class="form-control bscp" >
                                                                <label for="form-error-color">Form Hata Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="form-success-color" id="form-success-color" value="<?=$customCSS['form-success-color']?>" class="form-control bscp" >
                                                                <label for="form-success-color">Form Hata Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="button-color" id="button-color" value="<?=$customCSS['button-color']?>" class="form-control bscp" >
                                                                <label for="button-color">Buton Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="button-hover-color" id="button-hover-color" value="<?=$customCSS['button-hover-color']?>" class="form-control bscp" >
                                                                <label for="button-hover-color">Buton Hover Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="button-disabled-color" id="button-disabled-color" value="<?=$customCSS['button-disabled-color']?>" class="form-control bscp" >
                                                                <label for="button-disabled-color">Devre Dışı Buton Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="button-text-color" id="button-text-color" value="<?=$customCSS['button-text-color']?>" class="form-control bscp" >
                                                                <label for="button-text-color">Buton Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="input" id="input" value="Yazı" class="form-control input" style="background-color: <?=$customCSS['input-bg-color']?>; color: <?=$customCSS['input-color']?>; border: <?=$customCSS['input-border']?>;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="input" id="input1" placeholder="placeholder" class="form-control input" style="background-color: <?=$customCSS['input-bg-color']?>; color: <?=$customCSS['input-color']?>; border: <?=$customCSS['input-border']?>;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <select name="select" id="select" class="form-control input" style="background-color: <?=$customCSS['select-bg-color']?>; color: <?=$customCSS['select-text-color']?>; border: <?=$customCSS['border'] ?? '1px solid #dedede'?>;">
                                                                    <option value="1">Seçenek 1</option>
                                                                    <option value="2">Seçenek 2</option>
                                                                    <option value="3">Seçenek 3</option>
                                                                </select>
                                                                <div><label id="sample-label" >Label</label></div>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <textarea name="textarea" id="textarea" class="form-control input" style="background-color: <?=$customCSS['input-bg-color']?>; color: <?=$customCSS['input-color']?>; border: <?=$customCSS['input-border']?>;">Textarea</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <button id="sample-button" class="btn" style="background-color: <?=$customCSS["button-color"]?>;color:<?=$customCSS["button-text-color"]?>;">Örnek Buton</button>
                                                        <button id="sample-button-disabled" class="btn" disabled>Devre Dışı Buton</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="modal">
                                                <!-- Modal Ayarları -->
                                                <div class="card-head bg-info card-head-sm">
                                                    <header>Modal Ayarları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-6">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" name="overlay-bg-color" id="overlay-bg-color" data-color-format="rgba" value="<?=$customCSS['overlay-bg-color'] ?>" class="form-control bscp">
                                                                <label for="overlay-bg-color">Overlay Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" name="modal-bg-color" id="modal-bg-color" value="<?=$customCSS['modal-bg-color']?>" class="form-control bscp">
                                                                <label for="modal-bg-color">Modal Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" name="modal-text-color" id="modal-text-color" value="<?=$customCSS['modal-text-color'] ?>" class="form-control bscp">
                                                                <label for="modal-text-color">Modal Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="sample-modal-overlay" style="background-color: <?=$customCSS['overlay-bg-color'] ?>; padding: 10px;">
                                                            <div id="sample-modal" style="background-color: <?=$customCSS['modal-bg-color'] ?>; color: <?=$customCSS['modal-text-color'] ?>; padding: 10px;">
                                                                <h4>Örnek Modal Başlığı</h4>
                                                                <p>Örnek modal içeriği</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tooltip Ayarları -->
                                                <div class="card-head bg-warning">
                                                    <header>Tooltip Ayarları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-6">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="tooltip-bg-color" id="tooltip-bg-color" value="<?=$customCSS['tooltip-bg-color']?>" class="form-control bscp">
                                                                <label for="tooltip-bg-color">Tooltip Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="text" name="tooltip-text-color" id="tooltip-text-color" value="<?=$customCSS['tooltip-text-color']?>" class="form-control bscp">
                                                                <label for="tooltip-text-color">Tooltip Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="tooltip-container" style="position: relative; padding: 20px; border: 1px solid #ccc;">
                                                            <span id="sample-tooltip-trigger">Üzerime gelin</span>
                                                            <div id="sample-tooltip" style="position: absolute; top: 100%; left: 0; background-color: <?=$customCSS['tooltip-bg-color'] ?>; color: <?=$customCSS['tooltip-text-color']?>; padding: 5px 10px; border-radius: 3px; display: none;">
                                                                Bu bir örnek tooltip
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Pagination Ayarları -->
                                                <div class="card-head bg-warning">
                                                    <header>Sayfalama Ayarları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="pagination-bg-color" id="pagination-bg-color" value="<?=$customCSS['pagination-bg-color']?>" class="form-control bscp">
                                                                <label for="pagination-bg-color">Pagination Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="pagination-text-color" id="pagination-text-color" value="<?=$customCSS['pagination-text-color']?>" class="form-control bscp">
                                                                <label for="pagination-text-color">Pagination Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="pagination-active-bg-color" id="pagination-active-bg-color" value="<?=$customCSS['pagination-active-bg-color'] ?>" class="form-control bscp">
                                                                <label for="pagination-active-bg-color">Aktif Sayfa Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="pagination-active-text-color" id="pagination-active-text-color" value="<?=$customCSS['pagination-active-text-color']?>" class="form-control bscp">
                                                                <label for="pagination-active-text-color">Aktif Sayfa Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div id="sample-pagination" style="display: flex; justify-content: center; align-items: center;">
                                                            <a href="#" class="pagination-item" style="margin: 0 5px; padding: 5px 10px; text-decoration: none; background-color: <?=$customCSS['pagination-bg-color']?>; color: <?=$customCSS['pagination-text-color']?>;">1</a>
                                                            <a href="#" class="pagination-item active" style="margin: 0 5px; padding: 5px 10px; text-decoration: none; background-color: <?=$customCSS['pagination-active-bg-color'] ?>; color: <?=$customCSS['pagination-active-text-color']?>;">2</a>
                                                            <a href="#" class="pagination-item" style="margin: 0 5px; padding: 5px 10px; text-decoration: none; background-color: <?=$customCSS['pagination-bg-color']?>; color: <?=$customCSS['pagination-text-color'] ?>;">3</a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Alert ve Bildirimler Ayarları -->
                                                <div class="card-head bg-warning">
                                                    <header>Alert ve Bildirimler Ayarları</header>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="alert-success-bg" id="alert-success-bg" value="<?=$customCSS['alert-success-bg'] ?>" class="form-control bscp">
                                                                <label for="alert-success-bg">Başarılı Alert Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="alert-success-text" id="alert-success-text" value="<?=$customCSS['alert-success-text']?>" class="form-control bscp">
                                                                <label for="alert-success-text">Başarılı Alert Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="alert-warning-bg" id="alert-warning-bg" value="<?=$customCSS['alert-warning-bg']?>" class="form-control bscp">
                                                                <label for="alert-warning-bg">Uyarı Alert Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="alert-warning-text" id="alert-warning-text" value="<?=$customCSS['alert-warning-text']?>" class="form-control bscp">
                                                                <label for="alert-warning-text">Uyarı Alert Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="alert-danger-bg" id="alert-danger-bg" value="<?=$customCSS['alert-danger-bg'] ?>" class="form-control bscp">
                                                                <label for="alert-danger-bg">Hata Alert Arka Plan Rengi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <input type="text" name="alert-danger-text" id="alert-danger-text" value="<?=$customCSS['alert-danger-text'] ?>" class="form-control bscp">
                                                                <label for="alert-danger-text">Hata Alert Metin Rengi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div id="sample-alert-success" style="background-color: <?=$customCSS['alert-success-bg'] ?>; color: <?=$customCSS['alert-success-text']?>; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
                                                            Bu bir başarılı alert örneğidir.
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div id="sample-alert-warning" style="background-color: <?=$customCSS['alert-warning-bg']?>; color: <?=$customCSS['alert-warning-text']?>; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
                                                            Bu bir uyarı alert örneğidir.
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div id="sample-alert-danger" style="background-color: <?=$customCSS['alert-danger-bg'] ?>; color: <?=$customCSS['alert-danger-text'] ?>; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
                                                            Bu bir hata alert örneğidir.
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if($adminAuth == 0):?>
                                                    <!-- Kutu Gölgesi (Box Shadow) Ayarları -->
                                                    <div class="card-head bg-warning">
                                                        <header>Kutu Gölgesi Ayarları</header>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="box-shadow" id="box-shadow" value="<?=$customCSS['box-shadow'] ?>" data-color-format="rgba" class="form-control bscp">
                                                                <label for="box-shadow">Kutu Gölgesi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1"></div>
                                                        <div class="col-md-3">
                                                            <div id="box-shadow-demo" style="width: 200px; height: 100px; background-color: #fff; padding: 20px; box-shadow: <?=$customCSS['box-shadow'] ?>;">
                                                                Kutu Gölgesi Örneği
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Metin Gölgesi (Text Shadow) Ayarları -->
                                                    <div class="card-head bg-warning">
                                                        <header>Metin Gölgesi Ayarları</header>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="text-shadow" id="text-shadow" data-color-format="rgba" value="<?=$customCSS['text-shadow'] ?>" class="form-control bscp">
                                                                <label for="text-shadow">Metin Gölgesi</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1"></div>
                                                        <div class="col-md-3">
                                                            <h2 id="text-shadow-demo" style="font-size: 24px; color: #333; text-shadow: <?=$customCSS['text-shadow'] ?>;">
                                                                Metin Gölgesi Örneği
                                                            </h2>
                                                        </div>
                                                    </div>

                                                    <!-- Breakpoint Ayarları -->
                                                    <div class="card-head bg-warning">
                                                        <header>Breakpoint Ayarları</header>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="breakpoint-sm">Küçük Ekran (SM) Breakpoint</label>
                                                                <input type="text" name="breakpoint-sm" id="breakpoint-sm" value="<?=$customCSS['breakpoint-sm']?>" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="breakpoint-md">Orta Ekran (MD) Breakpoint</label>
                                                                <input type="text" name="breakpoint-md" id="breakpoint-md" value="<?=$customCSS['breakpoint-md']?>" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="breakpoint-lg">Büyük Ekran (LG) Breakpoint</label>
                                                                <input type="text" name="breakpoint-lg" id="breakpoint-lg" value="<?=$customCSS['breakpoint-lg']?>" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="breakpoint-xl">Çok Büyük Ekran (XL) Breakpoint</label>
                                                                <input type="text" name="breakpoint-xl" id="breakpoint-xl" value="<?=$customCSS['breakpoint-xl']?>" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div id="breakpoint-demo" style="width: 100%; height: 50px; background-color: #f0f0f0; text-align: center; line-height: 50px;">
                                                                Mevcut Ekran Boyutu: <span id="current-breakpoint"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif;?>
                                            </div>
                                        </div>

									</div>

                                    <div class="card-footer">
                                        <button type="button"
                                                id="resetButton"
                                                class="btn btn-warning"
                                                href="#resetDesignConfirmModal"
                                                data-toggle="modal"
                                                style="float: left;">Sıfırla</button>
                                        <button type="submit" id="submitButton" class="btn btn-primary" style="float: right">Kaydet</button>
                                        <button type="button" id="previewButton" class="btn btn-primary-bright" style="margin-right: 40px;float:right">Ön izle</button>

                                    </div>
								</form>
							</div>
						</div>
					</div>
				</section>
			</div>

			<?php require_once(ROOT."/_y/s/b/menu.php");?>

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

            <div class="modal fade" id="resetDesignConfirmModal" tabindex="-1" role="dialog" aria-labelledby="resetDesignConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="card">
                        <div class="card-head card-head-sm style-warning">
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
                            <p id="alertMessage">Tüm değişiklikleri geri almak istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                            <button type="button" class="btn btn-danger" id="resetDesignConfirmButton">Sıfırla</button>
                        </div>
                    </div>
                </div>
            </div>
		</div>

        <style>
            .input:focus {
                outline: 2px solid var(--input-focus-color);
                outline-style: solid;
            }
            .sample-product-box{
                float: left;
                display: inline-block;
            }
            .sample-category-product-box{
                float: left;
                display: inline-block;
            }
        </style>

        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

		<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
		<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
        <script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

		<script>
			$("#designphp").addClass("active");

            //header

            $("#header-logo-width-slider").slider({
                min: 40,
                max: 300,
                value: <?=str_replace("px","",$headerLogoWidth)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-logo-width-slider-value').text(ui.value + 'px');
                    $('#header-logo-width').val(ui.value + 'px');
                    $('#header-logo').css('width', ui.value + 'px');
                    //console.log($('#header-logo-width').val());
                }
            });

            $("#header-logo-mobile-width-slider").slider({
                min: 40,
                max: 150,
                value: <?=str_replace("px","",$headerLogoMobileWidth)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-logo-mobile-width-slider-value').text(ui.value + 'px');
                    $('#header-logo-mobile-width').val(ui.value + 'px');
                    $('#header-logo-mobile').css('width', ui.value + 'px');
                    //console.log($('#header-logo-mobile-width').val());
                }
            });

            $("#header-logo-margin-top-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerLogoMarginTop)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-logo-margin-top-slider-value').text(ui.value + 'px');
                    $('#header-logo-margin-top').val(ui.value + 'px');
                    $('#header-logo').css('margin-top', ui.value + 'px');
                    //console.log($('#header-logo-margin-top').val());
                }
            });

            $("#header-logo-margin-bottom-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerLogoMarginBottom)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-logo-margin-bottom-slider-value').text(ui.value + 'px');
                    $('#header-logo-margin-bottom').val(ui.value + 'px');
                    $('#header-logo').css('margin-bottom', ui.value + 'px');
                    //console.log($('#header-logo-margin-bottom').val());
                }
            });

            $("#header-logo-margin-left-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerLogoMarginLeft)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-logo-margin-left-slider-value').text(ui.value + 'px');
                    $('#header-logo-margin-left').val(ui.value + 'px');
                    $('#header-logo').css('margin-left', ui.value + 'px');
                    //console.log($('#header-logo-margin-left').val());
                }
            });

            $("#header-logo-margin-right-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerLogoMarginRight)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-logo-margin-right-slider-value').text(ui.value + 'px');
                    $('#header-logo-margin-right').val(ui.value + 'px');
                    $('#header-logo').css('margin-right', ui.value + 'px');
                    //console.log($('#header-logo-margin-right').val());
                }
            });

            $("#header-mobile-logo-margin-top-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerMobileLogoMarginTop)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-mobile-logo-margin-top-slider-value').text(ui.value + 'px');
                    $('#header-mobile-logo-margin-top').val(ui.value + 'px');
                    $('#header-logo-mobile').css('margin-top', ui.value + 'px');
                    //console.log($('#header-mobile-logo-margin-top').val());
                }
            });

            $("#header-mobile-logo-margin-bottom-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerMobileLogoMarginBottom)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-mobile-logo-margin-bottom-slider-value').text(ui.value + 'px');
                    $('#header-mobile-logo-margin-bottom').val(ui.value + 'px');
                    $('#header-logo-mobile').css('margin-bottom', ui.value + 'px');
                    //console.log($('#header-mobile-logo-margin-bottom').val());
                }
            });

            $("#header-mobile-logo-margin-left-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerMobileLogoMarginLeft)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-mobile-logo-margin-left-slider-value').text(ui.value + 'px');
                    $('#header-mobile-logo-margin-left').val(ui.value + 'px');
                    $('#header-logo-mobile').css('margin-left', ui.value + 'px');
                    //console.log($('#header-mobile-logo-margin-left').val());
                }
            });

            $("#header-mobile-logo-margin-right-slider").slider({
                min: 0,
                max: 50,
                value: <?=str_replace("px","",$headerMobileLogoMarginRight)?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-mobile-logo-margin-right-slider-value').text(ui.value + 'px');
                    $('#header-mobile-logo-margin-right').val(ui.value + 'px');
                    $('#header-logo-mobile').css('margin-right', ui.value + 'px');
                    //console.log($('#header-mobile-logo-margin-right').val());
                }
            });

            $("#header-min-height-slider").slider({
                min: 40,
                max: 200,
                value: <?=str_replace("px","",$customCSS['header-min-height'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-min-height-slider-value').text(ui.value + 'px');
                    $('#header-min-height').val(ui.value + 'px');
                    $('#sample-header-logo').css('min-height', ui.value + 'px');
                    //console.log($('#header-min-height').val());
                }
            });

            $("#header-mobile-min-height-slider").slider({
                min: 40,
                max: 200,
                value: <?=str_replace("px","",$customCSS['header-mobile-min-height'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#header-mobile-min-height-slider-value').text(ui.value + 'px');
                    $('#header-mobile-min-height').val(ui.value + 'px');
                    //$('#sample-header-logo-mobile').css('min-height', ui.value + 'px');
                    //console.log($('#header-mobile-min-height').val());
                }
            });

            //Home Page

            $("#homepage-h1-font-size-slider").slider({
                min: 12,
                max: 48,
                value: <?=str_replace("px","",$customCSS['homepage-h1-font-size'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#homepage-h1-font-size-value').text(ui.value + 'px');
                    $('#homepage-h1-font-size').val(ui.value + 'px');
                    $('#sample-homepage-h1').attr(
                        'style', 'font-size: ' + ui.value + 'px;' +
                        'color: ' + $('#homepage-h1-color').val() + ';'
                    );
                }
            });

            // Genel Görünüm

            $("#font-size-small-slider").slider({
                min: 9,
                max: 13,
                value: <?=str_replace("px","",$customCSS['font-size-small'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#font-size-small-slider-value').text(ui.value + 'px');
                    $('#font-size-small').val(ui.value + 'px');
                    $('#sample-font-small').attr(
                        'style', 'font-size: ' + ui.value + 'px;' +
                        'color: ' + $('#body-text-color').val() + ';'
                    );
                }
            });

            $("#font-size-normal-slider").slider({
                min: 14,
                max: 22,
                value: <?=str_replace("px","",$customCSS['font-size-normal'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#font-size-normal-slider-value').text(ui.value + 'px');
                    $('#font-size-normal').val(ui.value + 'px');
                    $('#sample-font-normal').attr(
                        'style', 'font-size: ' + ui.value + 'px;'
                    );
                    $("#sample-site-link").attr(
                        'style', 'font-size: ' + ui.value + 'px;' +
                        'color: ' + $('#a-color').val() + ';'
                    );
                }
            });

            $("#font-size-large-slider").slider({
                min: 19,
                max: 28,
                value: <?=str_replace("px","",$customCSS['font-size-large'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#font-size-large-slider-value').text(ui.value + 'px');
                    $('#font-size-large').val(ui.value + 'px');
                    $('#sample-font-large').attr(
                        'style', 'font-size: ' + ui.value + 'px;' +
                        'color: ' + $('#body-text-color').val() + ';'
                    );
                }
            });

            $("#font-size-xlarge-slider").slider({
                min: 29,
                max: 36,
                value: <?=str_replace("px","",$customCSS['font-size-xlarge'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#font-size-xlarge-slider-value').text(ui.value + 'px');
                    $('#font-size-xlarge').val(ui.value + 'px');
                    $('#sample-font-xlarge').attr(
                        'style', 'font-size: ' + ui.value + 'px;' +
                        'color: ' + $('#body-text-color').val() + ';'
                    );
                }
            });

            $("#top-banner-h1-font-size-slider").slider({
                min: 14,
                max: 40,
                value: <?=str_replace("px","",$customCSS['top-banner-h1-font-size'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#top-banner-h1-font-size-slider-value').text(ui.value + 'px');
                    $('#top-banner-h1-font-size').val(ui.value + 'px');
                    $('#sample-top-banner h1').attr(
                        'style', 'font-size: ' + ui.value + 'px;' +
                        'color: ' + $('#top-banner-h1-color').val() + ';'
                    );
                }
            });

            $("#top-banner-p-font-size-slider").slider({
                min: 12,
                max: 24,
                value: <?=str_replace("px","",$customCSS['top-banner-p-font-size'])?>,
                slide: function(event, ui) {
                    // ui.value slider'ın mevcut değerini içerir
                    $('#top-banner-p-font-size-slider-value').text(ui.value + 'px');
                    $('#top-banner-p-font-size').val(ui.value + 'px');
                    $('#sample-top-banner p').attr(
                        'style', 'font-size: ' + ui.value + 'px;' +
                        'color: ' + $('#top-banner-p-color').val() + ';'
                    );
                }
            });


            $('.bscp').colorpicker();

            function hsbToRgb(h, s, b) {
                var r, g, b;
                var i = Math.floor(h * 6);
                var f = h * 6 - i;
                var p = b * (1 - s);
                var q = b * (1 - f * s);
                var t = b * (1 - (1 - f) * s);
                switch(i % 6){
                    case 0: r = b, g = t, b = p; break;
                    case 1: r = q, g = b, b = p; break;
                    case 2: r = p, g = b, b = t; break;
                    case 3: r = p, g = q, b = b; break;
                    case 4: r = t, g = p, b = b; break;
                    case 5: r = b, g = p, b = q; break;
                }
                return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
            }

            function rgbToHex(r, g, b) {
                r = r.toString(16);
                g = g.toString(16);
                b = b.toString(16);

                if (r.length == 1)
                    r = "0" + r;
                if (g.length == 1)
                    g = "0" + g;
                if (b.length == 1)
                    b = "0" + b;

                return r + g + b;
            }

            // Yardımcı fonksiyon: Rengi RGBA formatına çevirir
            function toRGBA(color) {
                if (typeof color === 'object' && color.hasOwnProperty('r') && color.hasOwnProperty('g') && color.hasOwnProperty('b')) {
                    var a = color.hasOwnProperty('a') ? color.a : 1;
                    return 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + a + ')';
                } else if (typeof color === 'string') {
                    // Eğer renk zaten string olarak RGBA formatındaysa
                    return color;
                }
                // Diğer durumlar için (örneğin hex kodu) varsayılan değer
                return 'rgba(0,0,0,0.5)';
            }

            function updateBreakpointDemo() {
                var width = $(window).width();
                var breakpoint = '';

                if (width < parseInt($("#breakpoint-sm").val())) {
                    breakpoint = 'XS';
                } else if (width < parseInt($("#breakpoint-md").val())) {
                    breakpoint = 'SM';
                } else if (width < parseInt($("#breakpoint-lg").val())) {
                    breakpoint = 'MD';
                } else if (width < parseInt($("#breakpoint-xl").val())) {
                    breakpoint = 'LG';
                } else {
                    breakpoint = 'XL';
                }

                $("#current-breakpoint").text(width + 'px (' + breakpoint + ')');
            }

            // Genel Renk Ayarları için
            $(".color-picker").colorpicker();

            function updateColorDemo() {
                $("#primary-color-demo").css('background-color', $("#primary-color").val());
                $("#secondary-color-demo").css('background-color', $("#secondary-color").val());
                $("#accent-color-demo").css('background-color', $("#accent-color").val());

                // Metin rengini otomatik ayarla
                setTextColor("#primary-color-demo");
                setTextColor("#secondary-color-demo");
                setTextColor("#accent-color-demo");
            }

            function setTextColor(elementId) {
                var bgColor = $(elementId).css('background-color');
                var rgb = bgColor.replace(/^(rgb|rgba)\(/, '').replace(/\)$/, '').replace(/\s/g, '').split(',');
                var brightness = (parseInt(rgb[0]) * 299 + parseInt(rgb[1]) * 587 + parseInt(rgb[2]) * 114) / 1000;
                $(elementId).css('color', (brightness > 125) ? '#000' : '#fff');
            }

            function updateFontSize($input) {
                var id = $input.attr('id');
                var size = $input.val();

                // Eğer kullanıcı 'px' eklemediyse, otomatik olarak ekleyelim
                if (size.indexOf('px') === -1) {
                    size += 'px';
                    $input.val(size);
                }

                $('#sample-' + id.replace('font-size-', 'font-')).css('font-size', size);
                $input.next('.font-size-value').text(size);
            }

            // Sayfa yüklendiğinde mevcut değerleri uygula
            $(document).ready(function() {

                // Genel Site Ayarları için

                $("#primary-color, #secondary-color, #accent-color").on('keyup', function() {
                    updateColorDemo();
                });

                $("#primary-color, #secondary-color, #accent-color").on('changeColor', function() {
                    updateColorDemo();
                });

                $("#body-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-site-general').css('background-color', event.color.toHex());
                });
                $("#body-bg-color").on('keyup', function() {
                    $('#sample-site-general').css('background-color', this.value);
                });

                $("#body-text-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-site-general').css('color', event.color.toHex());
                });
                $("#body-text-color").on('keyup', function() {
                    $('#sample-site-general').css('color', this.value);
                });

                $("#a-color, #a-hover-color").colorpicker().on('changeColor', function() {
                    $('#sample-site-link').css('color', $("#a-color").val());
                    $('#sample-site-link').hover(
                        function() {
                            $(this).css('color', $("#a-hover-color").val());
                        },
                        function() {
                            $(this).css('color', $("#a-color").val());
                        }
                    );
                });
                $("#a-color, #a-hover-color").on('keyup', function() {
                    $('#sample-site-link').css('color', $("#a-color").val());
                    $('#sample-site-link').hover(
                        function() {
                            $(this).css('color', $("#a-hover-color").val());
                        },
                        function() {
                            $(this).css('color', $("#a-color").val());
                        }
                    );
                });

                $("#content-bg-color").colorpicker().on('changeColor', function() {
                    $('#content-bg-color-demo').css('background-color', $("#content-bg-color").val());
                });
                $("#content-bg-color").on('keyup', function() {
                    $('#content-bg-color-demo').css('background-color', $("#content-bg-color").val());
                });

                // Font Size Ayarları için
                $("#font-size-small, #font-size-normal, #font-size-large, #font-size-xlarge").on('input', function() {
                    updateFontSize($(this));
                });

                $("#font-size-small, #font-size-normal, #font-size-large, #font-size-xlarge").each(function() {
                    $(this).after('<div class="input-group-addon font-size-value">' + $(this).val() + '</div>');
                });

                $("#font-size-small, #font-size-normal, #font-size-large, #font-size-xlarge").each(function() {
                    updateFontSize($(this));
                });

                // Header için
                $("#header-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-header').css('background-color', event.color.toHex());
                });
                $("#header-bg-color").on('keyup', function() {
                    $('#sample-header').css('background-color', this.value);
                });

                // Alışveriş Menüsü İkonları için
                $("#shop-menu-container-icon-color-search").colorpicker().on('changeColor', function(event) {
                    $('#sample-icon-search').css('color', event.color.toHex());
                });

                $("#shop-menu-container-icon-color-member").colorpicker().on('changeColor', function(event) {
                    $('#sample-icon-member').css('color', event.color.toHex());
                });

                $("#shop-menu-container-icon-color-favorites").colorpicker().on('changeColor', function(event) {
                    $('#sample-icon-favorites').css('color', event.color.toHex());
                });

                $("#shop-menu-container-icon-color-basket").colorpicker().on('changeColor', function(event) {
                    $('#sample-icon-basket').css('color', event.color.toHex());
                });


                $('#sample-icon-basket').hover(
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-hover-color").val());
                    },
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-color-basket").val());
                    }
                );
                $('#sample-icon-favorites').hover(
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-hover-color").val());
                    },
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-color-favorites").val());
                    }
                );
                $('#sample-icon-member').hover(
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-hover-color").val());
                    },
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-color-member").val());
                    }
                );
                $('#sample-icon-search').hover(
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-hover-color").val());
                    },
                    function() {
                        $(this).css('color', $("#shop-menu-container-icon-color-search").val());
                    }
                );


                $("#select-text-color").colorpicker().on('changeColor', function(event) {
                    $('#select').css('color', event.color.toHex());
                });

                $("#select-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#select').css('background-color', event.color.toHex());
                });

                $("#select-focus-color").colorpicker().on('changeColor', function(event) {
                    $('#select').focus(function() {
                        $(this).css('outline-color', event.color.toHex());
                    });
                });

                $("#select").focus(function() {
                    $(this).css('color', $("#select-text-color").val());
                    $(this).css('background-color', $("#select-bg-color").val());
                    $(this).css('outline-color', $("#select-focus-color").val());
                });
                $("#select").blur(function() {
                    $(this).css('color', $("#select-text-color").val());
                    $(this).css('background-color', $("#select-bg-color").val());
                    $(this).css('outline-color', $("#select-focus-color").val());
                });

                // label
                $("#label-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-label').css('color', event.color.toHex());
                });

                //placeHolder
                $("#placeholder-color").colorpicker().on('changeColor', function(event) {
                    //$('#input1').attr('placeholder', 'Örnek Metin');
                    $('#input1').css('color', event.color.toHex());
                });

                $("#input-color").colorpicker().on('changeColor', function(event) {
                    // Convert HSB to RGB
                    var rgb = hsbToRgb(event.color.value.h, event.color.value.s, event.color.value.b);
                    // Convert RGB to hexadecimal
                    var hex = rgbToHex(rgb.r, rgb.g, rgb.b);

                    $('.input').attr('style',
                        'color: #' + hex + ';' +
                        'background-color: ' + $("#input-bg-color").val() + ';' +
                        'border: 1px solid ' + $("#input-border").val() + ';' +
                        'outline-color: ' + $("#input-focus-color").val() + ';'
                    );
                });

                $("#input-bg-color").colorpicker().on('changeColor', function(event) {
                    $('.input').attr('style',
                        'color: ' + $("#input-color").val() + ';' +
                        'background-color: ' + event.color.toHex() + ';' +
                        'border: 1px solid ' + $("#input-border").val() + ';' +
                        'outline-color: ' + $("#input-focus-color").val() + ';'
                    );
                });

                $("#input-focus-color").colorpicker().on('changeColor', function(event) {
                    $('.input').attr('style',
                        'color: ' + $("#input-color").val() + ';' +
                        'background-color: ' + $("#input-bg-color").val() + ';' +
                        'border: 1px solid ' + $("#input-border").val() + ';' +
                        'outline-color: ' + event.color.toHex() + ';'
                    );
                });

                $("#input-border").colorpicker().on('changeColor', function(event) {
                    $('.input').attr('style',
                        'color: ' + $("#input-color").val() + ';' +
                        'background-color: ' + $("#input-bg-color").val() + ';' +
                        'border: 1px solid ' + event.color.toHex() + ';' +
                        'outline-color: ' + $("#input-focus-color").val() + ';'
                    );
                });

                $(".input").focus(function() {
                    $(this).attr('style',
                        'color: ' + $("#input-color").val() + ';' +
                        'background-color: ' + $("#input-bg-color").val() + ';' +
                        'border: 1px solid ' + $("#input-border").val() + ';' +
                        'outline-color: ' + $("#input-focus-color").val() + ';' +
                        'outline-style: solid;'
                    );
                });

                //undocus
                $("#input").blur(function() {
                    $(this).attr('style',
                        'color: ' + $("#input-color").val() + ';' +
                        'background-color: ' + $("#input-bg-color").val() + ';' +
                        'border: 1px solid ' + $("#input-border").val() + ';' +
                        'outline-color: ' + $("#input-focus-color").val() + ';' +
                        'outline-style: none;'
                    );
                });

                // Butonlar için
                $("#button-color, #button-text-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-button').css({
                        'background-color': $("#button-color").val(),
                        'color': $("#button-text-color").val()
                    });
                });

                $("#button-hover-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-button').hover(
                        function() {
                            $(this).css('background-color', $("#button-hover-color").val());
                        },
                        function() {
                            $(this).css('background-color', $("#button-color").val());
                        }
                    );
                });

                $("#button-disabled-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-button-disabled').css('background-color', event.color.toHex());
                });

                // Ana menü için
                $("#main-menu-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-menu').css('background-color', event.color.toHex());
                });
                $("#main-menu-link-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-menu>li').css('background-color', event.color.toHex());
                });
                $("#main-menu-link-hover-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-menu>li').hover(
                        function() {
                            $(this).css('background-color', $("#main-menu-link-hover-bg-color").val());
                        },
                        function() {
                            $(this).css('background-color', $("#main-menu-link-bg-color").val());
                        }
                    );
                });

                //MENÜ LİNK BOYUT VE RENK AYARI
                const menuSettings = {
                    linkColor: $('#main-menu-link-color').val(),
                    hoverColor: $('#main-menu-link-hover-color').val(),
                    fontSize: '<?=$fontSizeMainMenu?>'
                };

                // Renk seçici için genel bir fonksiyon
                function applyMenuStyles() {
                    $('#sample-menu>li>a').css('color', menuSettings.linkColor);
                    $('#sample-menu>li>a').hover(
                        function() {
                            $(this).css('color', menuSettings.hoverColor);
                        },
                        function() {
                            $(this).css('color', menuSettings.linkColor);
                        }
                    );
                }

                // Renk seçicileri dinleme
                $("#main-menu-link-color").colorpicker().on('changeColor', function(event) {
                    menuSettings.linkColor = event.color.toHex();
                    applyMenuStyles();
                });

                $("#main-menu-link-hover-color").colorpicker().on('changeColor', function(event) {
                    menuSettings.hoverColor = event.color.toHex();
                    applyMenuStyles();
                });

                // Font boyutu slider'ı
                $("#font-size-main-menu-slider").slider({
                    min: 12,
                    max: 22,
                    value: parseInt(menuSettings.fontSize),
                    slide: function(event, ui) {
                        menuSettings.fontSize = ui.value + 'px';
                        $("#font-size-main-menu").val(menuSettings.fontSize);
                        $('#font-size-main-menu-slider-value').text(menuSettings.fontSize);
                        $('#sample-menu>li>a').css({
                            'font-size': menuSettings.fontSize,
                            'color': menuSettings.linkColor
                        });
                    }
                });

                // Başlangıçta stilleri uygula
                applyMenuStyles();
                $('#font-size-main-menu-slider-value').text(menuSettings.fontSize);
                $('#sample-font-menu').css('font-size', menuSettings.fontSize);

                // Alt menü ayarları
                const subMenuSettings = {
                    linkColor: $('#main-menu-ul-submenu-link-color').val(),
                    hoverColor: $('#main-menu-ul-submenu-link-hover-color').val(),
                    fontSize: '<?=$fontSizeMainMenuMobile?>'
                };

                // Alt menü için stiller
                function applySubMenuStyles() {
                    $('#sample-menu ul li a').css('color', subMenuSettings.linkColor);
                    $('#sample-menu ul li a').hover(
                        function() {
                            $(this).css('color', subMenuSettings.hoverColor);
                        },
                        function() {
                            $(this).css('color', subMenuSettings.linkColor);
                        }
                    );
                }

                // Alt menü renk seçiciler
                $("#main-menu-ul-submenu-link-color").colorpicker().on('changeColor', function(event) {
                    subMenuSettings.linkColor = event.color.toHex();
                    applySubMenuStyles();
                });

                $("#main-menu-ul-submenu-link-hover-color").colorpicker().on('changeColor', function(event) {
                    subMenuSettings.hoverColor = event.color.toHex();
                    applySubMenuStyles();
                });

                // Alt menü font boyutu
                $("#font-size-main-submenu-slider").slider({
                    min: 12,
                    max: 22,
                    value: parseInt(subMenuSettings.fontSize),
                    slide: function(event, ui) {
                        subMenuSettings.fontSize = ui.value + 'px';
                        $("#font-size-main-submenu").val(subMenuSettings.fontSize);
                        $('#font-size-main-submenu-slider-value').text(subMenuSettings.fontSize);
                        $('#sample-menu ul li a').css('font-size', subMenuSettings.fontSize);
                    }
                });

                applySubMenuStyles();
                $('#font-size-main-submenu-slider-value').text(subMenuSettings.fontSize);
                $('#sample-font-submenu').css('font-size', subMenuSettings.fontSize);

                $("#main-menu-ul-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-menu ul').css('background-color', event.color.toHex());
                });
                $("#main-menu-ul-submenu-link-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-menu ul li').css('background-color', event.color.toHex());
                });
                $("#main-menu-link-hover-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-menu>li>ul>li').hover(
                        function() {
                            $(this).css('background-color', $("#main-menu-ul-submenu-link-hover-bg-colorr").val());
                        },
                        function() {
                            $(this).css('background-color', $("#main-menu-ul-submenu-link-bg-color").val());
                        }
                    );
                });

                // button
                $("#button-color, #button-text-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-button').css({
                        'background-color': $("#button-color").val(),
                        'color': $("#button-text-color").val()
                    });
                });

                $("#button-hover-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-button').hover(
                        function() {
                            $(this).css('background-color', $("#button-hover-color").val());
                        },
                        function() {
                            $(this).css('background-color', $("#button-color").val());
                        }
                    );
                });

                $("#button-disabled-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-button-disabled').css('background-color', event.color.toHex());
                });

                // İletişim ve Sosyal Medya için
                $("#top-contact-and-social-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-contact-social').css('background-color', event.color.toHex());
                });
                $("#top-contact-and-social-bg-color").on('keyup', function() {
                    $('#sample-contact-social').css('background-color', this.value);
                });

                $("#top-contact-and-social-link-color, #top-contact-and-social-link-hover-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-contact-link').css('color', $("#top-contact-and-social-link-color").val());
                    $('.sample-contact-link').hover(
                        function() {
                            $(this).css('color', $("#top-contact-and-social-link-hover-color").val());
                        },
                        function() {
                            $(this).css('color', $("#top-contact-and-social-link-color").val());
                        }
                    );
                });

                $("#top-contact-and-social-icon-color, #top-contact-and-social-icon-hover-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-social-icon').css('color', $("#top-contact-and-social-icon-color").val());
                    $('.sample-social-icon').hover(
                        function() {
                            $(this).css('color', $("#top-contact-and-social-icon-hover-color").val());
                        },
                        function() {
                            $(this).css('color', $("#top-contact-and-social-icon-color").val());
                        }
                    );
                });

                // Ana Menü için
                $("#main-menu-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-main-menu').css('background-color', event.color.toHex());
                });

                $("#main-menu-link-color, #main-menu-link-hover-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-menu-link').css('color', $("#main-menu-link-color").val());
                    $('.sample-menu-link').hover(
                        function() {
                            $(this).css('color', $("#main-menu-link-hover-color").val());
                        },
                        function() {
                            $(this).css('color', $("#main-menu-link-color").val());
                        }
                    );
                });

                $("#main-menu-ul-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-main-menu ul').css('background-color', event.color.toHex());
                });

                // Ürün Kutusu için
                $("#homepage-product-box-bg-color, #homepage-product-box-hover-bg-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-product-box').css('background-color', $("#homepage-product-box-bg-color").val());
                    $('.sample-product-box').hover(
                        function() {
                            $(this).css('background-color', $("#homepage-product-box-hover-bg-color").val());
                        },
                        function() {
                            $(this).css('background-color', $("#homepage-product-box-bg-color").val());
                        }
                    );
                });

                $("#homepage-product-box-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-product-box span').css('color', event.color.toHex());
                });

                $("#homepage-product-box-link-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-product-link').css('color', event.color.toHex());
                });

                $("#homepage-product-box-price-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-product-box p').css('color', event.color.toHex());
                });

                $(document).on('change', "#homepage-product-box-width", function() {
                    //seçilen değeri alalım, bir tane .sample-product-box kopyalayıp seçilen değer kadar append edelim
                    var width = $(this).val();
                    //seçilen değerin metnini alalım
                    var length = $(this).find('option:selected').text();
                    console.log(length);
                    var sampleProductBox = $('.sample-product-box').eq(0).clone();
                    $('#productBoxContainer').html('');
                    for (var i = 1; i <= length; i++) {
                        $("#productBoxContainer").append(sampleProductBox.clone());
                    }

                    $('.sample-product-box').css('width', width);
                });

                // Kategori Kutusu için
                $("#category-product-box-bg-color, #category-product-box-hover-bg-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-category-product-box').css('background-color', $("#category-product-box-bg-color").val());
                    $('.sample-category-product-box').hover(
                        function() {
                            $(this).css('background-color', $("#category-product-box-hover-bg-color").val());
                        },
                        function() {
                            $(this).css('background-color', $("#category-product-box-bg-color").val());
                        }
                    );
                });

                $("#category-product-box-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-category-product-box span').css('color', event.color.toHex());
                });

                $("#category-product-box-link-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-category-product-link').css('color', event.color.toHex());
                });

                $("#category-product-box-price-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-category-product-box p').css('color', event.color.toHex());
                });

                $(document).on('change', "#category-product-box-width", function() {
                    //seçilen değeri alalım, bir tane .sample-product-box kopyalayıp seçilen değer kadar append edelim
                    var width = $(this).val();
                    //seçilen değerin metnini alalım
                    var length = $(this).find('option:selected').text();
                    console.log(length);
                    var sampleProductBox = $('.sample-category-product-box').eq(0).clone();
                    $('#categoryProductBoxContainer').html('');
                    for (var i = 1; i <= length; i++) {
                        $("#categoryProductBoxContainer").append(sampleProductBox.clone());
                    }

                    $('.sample-category-product-box').css('width', width);
                });

                // Banner alanları için
                $("#top-banner-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-top-banner').css('background-color', event.color.toHex());
                });

                $("#top-banner-h1-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-top-banner h1').css('color', event.color.toHex());
                });

                $("#top-banner-p-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-top-banner p').css('color', event.color.toHex());
                });

                $("#top-banner-h1-font-size").on('input', function() {
                    $('#sample-top-banner h1').css('font-size', $(this).val());
                });

                $("#top-banner-p-font-size").on('input', function() {
                    $('#sample-top-banner p').css('font-size', $(this).val());
                });

                //#middle-content-banner-width select değişimi . sampleMiddleBanner classına uygulanacak
                //değer 100% col-md-12 olacak, 50% ise col-md-6 olacak, 25% ise col-md-4 olacak, 16 ise col-md-2 olacak
                $(document).on('change', "#middle-content-banner-width", function() {
                    var width = $(this).val();
                    //console.log(width);
                    var sampleMiddleBanner = $('.sampleMiddleBanner');
                    if (width == '100%') {
                        sampleMiddleBanner.removeClass('col-md-6 col-md-3 col-md-2');
                        sampleMiddleBanner.addClass('col-md-12');
                    } else if (width == '50%') {
                        sampleMiddleBanner.removeClass('col-md-12 col-md-3 col-md-2');
                        sampleMiddleBanner.addClass('col-md-6');
                    } else if (width == '25%') {
                        sampleMiddleBanner.removeClass('col-md-12 col-md-6 col-md-2');
                        sampleMiddleBanner.addClass('col-md-3');
                    } else if (width == '16%') {
                        sampleMiddleBanner.removeClass('col-md-12 col-md-6 col-md-3');
                        sampleMiddleBanner.addClass('col-md-2');
                    }
                });

                // #bottom-banner-width select değişimi .bottomBannerSample
                // classına uygulanacak değer 100% col-md-12 olacak, 50% ise col-md-6 olacak, 25% ise col-md-4 olacak, 16 ise col-md-2 olacak
                $(document).on('change', "#bottom-banner-width", function() {
                    var width = $(this).val();
                    //console.log(width);
                    var bottomBannerSample = $('.bottomBannerSample');
                    if (width == '100%') {
                        bottomBannerSample.removeClass('col-md-6 col-md-4 col-md-2');
                        bottomBannerSample.addClass('col-md-12');
                    } else if (width == '50%') {
                        bottomBannerSample.removeClass('col-md-12 col-md-4 col-md-2');
                        bottomBannerSample.addClass('col-md-6');
                    } else if (width == '25%') {
                        bottomBannerSample.removeClass('col-md-12 col-md-6 col-md-2');
                        bottomBannerSample.addClass('col-md-3');
                    } else if (width == '10%') {
                        bottomBannerSample.removeClass('col-md-12 col-md-6 col-md-4');
                        bottomBannerSample.addClass('col-md-2');
                    }
                });


                $("#bottom-banner-width").on('input', function() {
                    // Bu örnekte görsel bir değişiklik yapmıyoruz, ancak gerçek uygulamada bu değerleri kullanabilirsiniz
                    console.log($(this).attr('id') + ' değeri: ' + $(this).val());
                });


                // Ana Sayfa Başlığı için
                $("#homepage-h1-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-homepage-h1').css('color', event.color.toHex());
                });

                $("#homepage-h1-font-size").on('input', function() {
                    $('#sample-homepage-h1').css('font-size', $(this).val());
                });

                // Footer Ayarları için
                $("#footer-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-footer').css('background-color', event.color.toHex());
                });
                $("#footer-bg-color").on('keyup', function() {
                    $('#sample-footer').css('background-color', this.value);
                });

                $("#footer-text-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-footer').css('color', event.color.toHex());
                });

                $("#footer-link-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-footer p a').css('color', event.color.toHex());
                });

                $("#footer-menu-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-footer-menu').css('background-color', event.color.toHex());
                });
                $("footer-menu-bg-color").on('keyup', function() {
                    $('#sample-footer-menu').css('background-color', this.value);
                });

                $("#footer-menu-link-color, #footer-menu-link-hover-color").colorpicker().on('changeColor', function(event) {
                    $('.sample-footer-link').css('color', $("#footer-menu-link-color").val());
                    $('.sample-footer-link').hover(
                        function() {
                            $(this).css('color', $("#footer-menu-link-hover-color").val());
                        },
                        function() {
                            $(this).css('color', $("#footer-menu-link-color").val());
                        }
                    );
                });

                // Modal Ayarları için
                $("#overlay-bg-color").colorpicker({
                    format: 'rgba'
                }).on('changeColor', function(event) {
                    var rgba = toRGBA(event.color.toRGB());
                    $('#sample-modal-overlay').css('background-color', rgba);
                });

                $("#modal-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-modal').css('background-color', event.color.toHex());
                });

                $("#modal-text-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-modal').css('color', event.color.toHex());
                });

                // Tooltip Ayarları için
                $("#tooltip-bg-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-tooltip').css('background-color', event.color.toHex());
                });

                $("#tooltip-text-color").colorpicker().on('changeColor', function(event) {
                    $('#sample-tooltip').css('color', event.color.toHex());
                });

                // Tooltip göster/gizle fonksiyonları
                $('#sample-tooltip-trigger').hover(
                    function() {
                        $('#sample-tooltip').fadeIn(200);
                    },
                    function() {
                        $('#sample-tooltip').fadeOut(200);
                    }
                );

                // Pagination Ayarları için
                $("#pagination-bg-color").colorpicker().on('changeColor', function(event) {
                    $('.pagination-item:not(.active)').css('background-color', event.color.toHex());
                });

                $("#pagination-text-color").colorpicker().on('changeColor', function(event) {
                    $('.pagination-item:not(.active)').css('color', event.color.toHex());
                });

                $("#pagination-active-bg-color").colorpicker().on('changeColor', function(event) {
                    $('.pagination-item.active').css('background-color', event.color.toHex());
                });

                $("#pagination-active-text-color").colorpicker().on('changeColor', function(event) {
                    $('.pagination-item.active').css('color', event.color.toHex());
                });

                // Alert ve Bildirimler Ayarları için
                $("#alert-success-bg").colorpicker().on('changeColor', function(event) {
                    $('#sample-alert-success').css('background-color', event.color.toHex());
                });

                $("#alert-success-text").colorpicker().on('changeColor', function(event) {
                    $('#sample-alert-success').css('color', event.color.toHex());
                });

                $("#alert-warning-bg").colorpicker().on('changeColor', function(event) {
                    $('#sample-alert-warning').css('background-color', event.color.toHex());
                });

                $("#alert-warning-text").colorpicker().on('changeColor', function(event) {
                    $('#sample-alert-warning').css('color', event.color.toHex());
                });

                $("#alert-danger-bg").colorpicker().on('changeColor', function(event) {
                    $('#sample-alert-danger').css('background-color', event.color.toHex());
                });

                $("#alert-danger-text").colorpicker().on('changeColor', function(event) {
                    $('#sample-alert-danger').css('color', event.color.toHex());
                });

                $("#box-shadow").colorpicker().on('changeColor', function(event) {
                    $('#box-shadow-demo').css('box-shadow', '0 0 10px ' + event.color.toHex());
                });

                $("#text-shadow").colorpicker().on('changeColor', function(event) {
                    $('#text-shadow-demo').css('text-shadow', '2px 2px 2px ' + event.color.toHex());
                });

                // Breakpoint Ayarları için
                $("#breakpoint-sm, #breakpoint-md, #breakpoint-lg, #breakpoint-xl").on('input', function() {
                    updateBreakpointDemo();
                });

                $(window).resize(function() {
                    updateBreakpointDemo();
                });

                // Sayfa yüklendiğinde mevcut değeri uygula
                updateBreakpointDemo();
                updateColorDemo();

                //form için enter tuşunu iptal edelim
                $(document).on('keypress', 'form', function(e) {
                    if (e.which == 13) {
                        return false;
                    }
                });

                $(document).on("click","#previewButton, #submitButton", function(e) {
                    e.preventDefault();
                    let languageID = $("#languageID").val();
                    //footer-logo-width sonunda px veya % yoksa sayı mı kontrol edelim.
                    let footerLogoWidth = $("#footer-logo-width").val();
                    if(footerLogoWidth.indexOf("px") === -1 && footerLogoWidth.indexOf("%") === -1) {
                        //sayı mı kontrol edelim
                        if(isNaN(footerLogoWidth)) {
                            alert("Footer Logo Genişlik değeri geçerli bir sayı olmalıdır.");
                            return false;
                        }
                        $("#footer-logo-width").val(footerLogoWidth+"px");
                    }
                    else{
                        //px ve % replace edip sayı mı kontrol edelim
                        footerLogoWidth = footerLogoWidth.replace("px", "");
                        footerLogoWidth = footerLogoWidth.replace("%", "");
                        if(isNaN(footerLogoWidth)) {
                            alert("Footer Logo Genişlik değeri geçerli bir sayı olmalıdır.");
                            return false;
                        }
                    }

                    let footerLogoHeight = $("#footer-logo-height").val();
                    if(footerLogoHeight.indexOf("px") === -1 && footerLogoHeight.indexOf("%") === -1) {
                        //sayı mı kontrol edelim
                        if(isNaN(footerLogoHeight)) {
                            alert("Footer Logo Yükseklik değeri geçerli bir sayı olmalıdır.");
                            return false;
                        }
                        $("#footer-logo-height").val(footerLogoHeight+"px");
                    }
                    else{
                        //px ve % replace edip sayı mı kontrol edelim
                        footerLogoHeight = footerLogoHeight.replace("px", "");
                        footerLogoHeight = footerLogoHeight.replace("%", "");
                        if(isNaN(footerLogoHeight)) {
                            alert("Footer Logo Yükseklik değeri geçerli bir sayı olmalıdır.");
                            return false;
                        }
                    }

                    var form = $("#designForm");
                    form.append('<input type="hidden" name="languageID" value="'+languageID+'">');
                    var url = "/App/Controller/Admin/AdminDesignController.php";
                    var data = form.serialize();
                    var buttonId = $(this).attr('id');

                    var action = 'saveDesign';
                    if(buttonId === 'previewButton') {
                        action = 'savePreviewDesign';
                    }

                    data += '&action=' + action;
                    console.log(data);
                    $.post(url, data, function(response) {
                        var json = JSON.parse(response);
                        if (json.status === 'success') {
                            $("#alertModal .card-head").removeClass('style-danger').addClass('style-success');
                            $("#alertMessage").text(json.message);
                            $("#alertModal").modal('show');
                            //1 saniye sonra sayfayı yenile
                            setTimeout(function() {
                                $("#alertModal").modal('hide');
                            }, 1000);
                        } else {
                            $("#alertModal .card-head").removeClass('style-success').addClass('style-danger');
                            $("#alertMessage").text(json.message);
                            $("#alertModal").modal('show');
                        }
                    });
                });

                $(document).on("click","#resetDesignConfirmButton", function(e) {
                    e.preventDefault();
                    let languageID = $("#languageID").val();
                    var form = $("#designForm");
                    form.append('<input type="hidden" name="languageID" value="'+languageID+'">');
                    var url = "/App/Controller/Admin/AdminDesignController.php";
                    var data = form.serialize();
                    data += '&action=resetDesign';
                    console.log(data);
                    $("#resetDesignConfirmModal").modal('hide');
                    $.post(url, data, function(response) {
                        var json = JSON.parse(response);
                        if (json.status === 'success') {
                            $("#alertModal .card-head").removeClass('style-danger').addClass('style-success');
                            $("#alertMessage").text(json.message);
                            $("#alertModal").modal('show');
                            //1 saniye sonra sayfayı yenile
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            $("#alertModal .card-head").removeClass('style-success').addClass('style-danger');
                            $("#alertMessage").text(json.message);
                            $("#alertModal").modal('show');
                        }
                    });
                });
            });

		</script>
	</body>
</html>
