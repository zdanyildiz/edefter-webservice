<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 * @var Helper $helper
 */

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminBannerModel.php';

$adminBannerCreateModel = new AdminBannerCreateModel($db);
$bannerTypeModel = new AdminBannerTypeModel($db);
$bannerGroupModel = new AdminBannerGroupModel($db);
$bannerLayoutModel = new AdminBannerLayoutModel($db);
$bannerAllLayouts = $bannerLayoutModel->getAllLayouts();
$bannerStyleModel = new AdminBannerStyleModel($db);
$adminBannerDisplayRulesModel = new AdminBannerDisplayRulesModel($db);
$adminBannerModel = new AdminBannerModel($db);

include_once MODEL . 'Admin/AdminPage.php';
include_once MODEL . 'Admin/AdminCategory.php';
$adminPageModel = new AdminPage($db);
$adminCategoryModel = new AdminCategory($db);

$languageId = $_GET["languageID"] ?? $_SESSION["languageID"] ?? 1;
$languageID = intval($languageId);

$languages = $languageModel->getLanguages();

$bannerGroupID = $_GET["bannerGroupID"] ?? 0;
$bannerGroupID = intval($bannerGroupID);

$buttonName = "Kaydet";
$bannerBaseImage = "/_y/assets/img/header.jpg";

if($bannerGroupID > 0) {
    $bannerGroup = $bannerGroupModel->getGroupById($bannerGroupID);
    if($bannerGroup){
        $bannerGroup = $bannerGroup[0];
        $bannerGroupName = $bannerGroup["group_name"];
        $bannerGroupTitle = $bannerGroup["group_title"];
        $bannerGroupDesc = $bannerGroup["group_desc"];
        $bannerLayoutID = $bannerGroup["layout_id"];
        $bannerGroupKind = $bannerGroup['group_kind'] ?? "text_and_image";
        $bannerGroupView = $bannerGroup['group_view'] ?? "single";
        $bannerColumns = $bannerGroup["columns"] ?? 1;
        $bannerGroupStyleClass = $bannerGroup["style_class"];
        $bannerGroupBackgroundColor = $bannerGroup['background_color'];
        $bannerGroupTitleColor = $bannerGroup['group_title_color'];
        $bannerGroupDescColor = $bannerGroup['group_desc_color'];
        $bannerGroupFullSize = $bannerGroup['group_full_size'] ?? 1;
        $customCss = $bannerGroup["custom_css"];
        $bannerVisibilityStart = $bannerGroup["visibility_start"];
        $bannerVisibilityStart = strtotime($bannerVisibilityStart) > 0 ? date("Y.m.d", strtotime($bannerVisibilityStart)) : "";
        $bannerVisibilityEnd = $bannerGroup["visibility_end"];
        $bannerVisibilityEnd = strtotime($bannerVisibilityEnd) > 0 ? date("Y.m.d", strtotime($bannerVisibilityEnd)) : "";
        $bannerDuration = $bannerGroup["banner_duration"] ?? 0;
        $bannerFullSize = $bannerGroup['banner_full_size'] ?? 0;
        $buttonName = "Güncelle";

        $bannerDisplayRules = $adminBannerDisplayRulesModel->getDisplayRuleByGroupId($bannerGroupID);
        //print_r($bannerDisplayRules);exit;

        foreach ($bannerDisplayRules as $rule) {
            //print_r($rule);exit;
            $bannerLanguageCode = $rule["language_code"];
            $bannerTypeID = $rule["type_id"];
            if(!empty($rule["page_id"])) {
                $bannerDisplayPageIDs[] = $rule["page_id"];
            }
            elseif(!empty($rule["category_id"])) {
                $bannerDisplayCategoryIDs[] = $rule["category_id"];
            }
        }

        $bannerLayout = $bannerLayoutModel->getLayoutById($bannerLayoutID);
        if($bannerLayout){
            //echo '<pre>';print_r($bannerLayout);exit;
            $bannerLayout = $bannerLayout[0];
            $bannerLayoutGroup = $bannerLayout["layout_group"] ?? "";
            $bannerLayoutView = $bannerLayout["layout_view"] ?? "";
            $bannerLayoutName = $bannerLayout["layout_name"] ?? "";
            $bannerLayoutDescription = $bannerLayout["description"] ?? "";
            $bannerLayoutMaxBanners = $bannerLayout["max_banners"] ?? 1;
        }

        $bannerLayouts = $bannerLayoutModel->getLayoutsByTypeId($bannerTypeID);

        $bannerType = $bannerTypeModel->getTypeById($bannerTypeID);
        $bannerType = $bannerType[0];
        $bannerTypeName = $bannerType["type_name"];
        $bannerTypeDescription = $bannerType["description"];

        $banners = $adminBannerModel->getBannersByGroupID($bannerGroupID);
    }
}

$bannerLayoutID = $bannerLayoutID ?? 0;
$bannerGroupName = $bannerGroupName ?? $helper->createPassword(8,2);
$bannerGroupTitle = $bannerGroupTitle ?? "";
$bannerGroupDesc = $bannerGroupDesc ?? "";
$bannerGroupKind = $bannerGroupKind ?? "text_and_image";
$bannerGroupView = $bannerGroupView ?? "single";
$bannerColumns = $bannerColumns ?? 1;
$bannerGroupStyleClass = $bannerGroupStyleClass ?? "";
$bannerGroupBackgroundColor = $bannerGroupBackgroundColor ?? "";
$bannerGroupTitleColor = $bannerGroupTitleColor ?? "";
$bannerGroupDescColor = $bannerGroupDescColor ?? "";
$bannerGroupFullSize = $bannerGroupFullSize ?? 1;
$bannerFullSize = $bannerFullSize ?? 0;
$customCss = $customCss ?? "";
$bannerVisibilityStart = $bannerVisibilityStart ?? "";
$bannerVisibilityEnd = $bannerVisibilityEnd ?? "";
$bannerDuration = $bannerDuration ?? 0;
$bannerLayoutGroup = $bannerLayoutGroup ?? "";
$sampleBannerImage = !empty($bannerLayoutGroup) ? "$bannerLayoutGroup.jpg" : "bos.jpg";
$bannerLayoutView = $bannerLayoutView ?? "single";
$bannerLayoutName = $bannerLayoutName ?? "";
$bannerLayoutDescription = $bannerLayoutDescription ?? "";
$bannerLayoutMaxBanners = $bannerLayoutMaxBanners ?? 1;
$bannerTypeID = $bannerTypeID ?? 0;
$bannerTypeName = $bannerTypeName ?? "";
$bannerTypeDescription = $bannerTypeDescription ?? "";
$banners = $banners ?? [];
$bannerLayouts = $bannerLayouts ?? [];

$bannerLanguageCode = $bannerLanguageCode ?? "";
$bannerDisplayPageIDs = $bannerDisplayPageIDs ?? [];
$bannerDisplayCategoryIDs = $bannerDisplayCategoryIDs ?? [];



if($bannerLanguageCode != "") {
    $languageID = $languageModel->getLanguageId($bannerLanguageCode);
}

$bannerTypes = $bannerTypeModel->getAllTypes();

function generateBannerBox($generateBannerData)
{
    $n = $generateBannerData["key"];
    $bannerSlogan = $generateBannerData["title"];
    $bannerText = $generateBannerData["content"];
    $bannerImage = $generateBannerData["image"];
    $bannerLink = $generateBannerData["link"];
    $bannerActive = $generateBannerData["active"];
    $bannerInputImage = ($bannerImage!="/_y/assets/img/header.jpg") ? $bannerImage : "";
    $bannerActiveChecked = $bannerActive == 1 ? "checked" : "";

    $bannerHeightSize = $generateBannerData["bannerHeightSize"];
    $bannerBgColor = $generateBannerData["bannerBgColor"];
    $bannerContentBoxBgColor = $generateBannerData["bannerContentBoxBgColor"];
    $bannerTitleColor = $generateBannerData["bannerTitleColor"];
    $bannerTitleFontSize = $generateBannerData["bannerTitleFontSize"];
    $bannerContentColor = $generateBannerData["bannerContentColor"];
    $bannerContentFontSize = $generateBannerData["bannerContentFontSize"];
    $bannerShowButton = $generateBannerData["bannerShowButton"] == 1 ? "checked" : "";
    $bannerButtonTitle = $generateBannerData["bannerButtonTitle"];
    $bannerButtonLocation = $generateBannerData["bannerButtonLocation"];
    $bannerButtonBgColor = $generateBannerData["bannerButtonBgColor"];
    $bannerButtonTextColor = $generateBannerData["bannerButtonTextColor"];
    $bannerButtonHoverBgColor = $generateBannerData["bannerButtonHoverBackground"];
    $bannerButtonTextHoverColor = $generateBannerData["bannerButtonHoverTextColor"];
    $bannerButtonTextSize = $generateBannerData["bannerButtonTextSize"];

    $bannerButtonLocation0Selected = $bannerButtonLocation==0 ? "selected" : "";
    $bannerButtonLocation1Selected = $bannerButtonLocation==1 ? "selected" : "";
    $bannerButtonLocation2Selected = $bannerButtonLocation==2 ? "selected" : "";
    $bannerButtonLocation3Selected = $bannerButtonLocation==3 ? "selected" : "";
    $bannerButtonLocation4Selected = $bannerButtonLocation==4 ? "selected" : "";
    $bannerButtonLocation5Selected = $bannerButtonLocation==5 ? "selected" : "";
    $bannerButtonLocation6Selected = $bannerButtonLocation==6 ? "selected" : "";
    $bannerButtonLocation7Selected = $bannerButtonLocation==7 ? "selected" : "";
    $bannerButtonLocation8Selected = $bannerButtonLocation==8 ? "selected" : "";
    $bannerButtonLocation9Selected = $bannerButtonLocation==9 ? "selected" : "";

    return <<<HTML
        <div class="card panel" id="card-panel-{$n}">
            <div class="card-head style-accent-bright collapsed">
                <header
                       data-toggle="collapse"
                       data-parent="#bannerContainer"
                       data-target="#accordion{$n}-container">Banner {$n} Özellikleri
                </header>
                <div class="tools">
                    <a class="btn btn-icon-toggle removeBannerBox" data-id="{$n}"><i class="fa fa-trash"></i></a>
                    <a class="btn btn-icon-toggle"><i class="fa fa-arrows"></i></a>
                    <a class="btn btn-icon-toggle"
                       data-toggle="collapse"
                       data-parent="#bannerContainer"
                       data-target="#accordion{$n}-container"><i class="fa fa-angle-down"></i>
                    </a>
                </div>
            </div>
            <div id="accordion{$n}-container" class="collapse">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <!-- Banner Başlığı -->
                            <div class="form-group">
                                <label for="bannerSlogan-{$n}" class="control-label">Banner Başlığı</label>
                                <input type="text" class="form-control bannerSlogan" id="bannerSlogan-{$n}" name="bannerSlogan[]" value="{$bannerSlogan}">
                            </div>
                            <div class="form-group">
                                <label for="bannerButton-{$n}" class="control-label">Buton Yazısı</label>
                                <input type="text" class="form-control" id="bannerButton-{$n}" name="bannerButton[]" value="{$bannerButtonTitle}">
                                <p class="text-sm">Boş bırakılırsa buton görünmez.</p>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <!-- Banner Yazısı -->
                            <div class="form-group">
                                <label for="bannerText-{$n}" class="control-label">Banner Yazısı</label>
                                <textarea class="form-control bannerText" id="bannerText-{$n}" name="bannerText[]">{$bannerText}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="checkbox checkbox-styled">
                                    <label for="bannerActive-{$n}">
                                        <input type="checkbox" id="bannerActive-{$n}" name="bannerActive[]" {$bannerActiveChecked} value="1">
                                        <span>Banner Aktif</span>
                                    </label>
                                    <p class="text-sm">Bu seçenek işaretliyse banner yayında olacaktır.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <!-- Banner Linki -->
                            <div class="col-md-1">
                                <a id="searchContentButton-{$n}" class="searchContentButton btn btn-sm active" href="#" data-id="{$n}" title="Kategori/Sayfa Seç">
                                    <i class="fa fa-list"></i>
                                </a>
                            </div>
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label for="bannerLink" class="control-label">Banner Bağlantısı</label>
                                    <input type="text" class="form-control" id="bannerLink-{$n}" name="bannerLink[]" value="{$bannerLink}">
                                    <p class="text-sm">Banner Tıklanınca gidecek sayfa adresi. Aynı sayfada kalmak için # girin</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Banner Görseli-->
                    <div class="tools text-right">
                        <label
                            class="btn removeBannerImage"
                            href="#"
                            data-id="{$n}"
                            title="Görseli Sil">
                            <i class="fa fa-trash"></i>
                            Banner Görseli Temizle
                        </label>

                        <label
                            class="btn uploadImageByLeftCanvas"
                            
                            id="uploadImageByLeftCanvas-{$n}"
                            data-target="card-panel-{$n}"
                            data-uploadtarget="Banner"
                            
                            title="Yeni Resim Yükle">
                            <i class="fa fa-upload"></i>
                            Banner Resmi Yükle
                        </label>

                        <label
                            class="btn selectImageByRightCanvas"
                            
                            id="selectImageByRightCanvas-{$n}"
                            data-target="card-panel-{$n}"
                            
                            title="Listeden Resim Seç">
                            <i class="fa fa-file-image-o"></i>
                            Banner Resmi Seç
                        </label>
                    </div>
                    <div class="form-group hidden">
                        <label for="bannerImage" class="control-label">Banner Görseli</label>
                        <img class="bannerImage" id="bannerImage-{$n}" src="/Public/Image/{$bannerImage}" alt="Banner Görseli" style="max-width: 100%; height: auto">
                        <input type="hidden" id="bannerImage" name="bannerImage[]" value="{$bannerImage}">
                    </div>
                    <div class="row"><div class="card-body"></div></div>
                    <div class="row " id="bannerCustomizationContainer-{$n}">
                        <div class="card-head-sm">
                            <div class="col-md-6">
                                <header>Banner Görünümü</header>
                                <p class="text-sm">Boş bırakılan değerler dikkate alınmaz. Banner varsayılan ayarlarla görünür.</p>
                            </div>
                            <div class="col-md-6">
                                <header>Buton Görünümü</header>
                                <label for="showButton-{$n}" class="text-sm">
                                    <input type="checkbox" id="showButton-{$n}" name="showButton[]" {$bannerShowButton} value="1">
                                    <span>Buton Ekle</span>
                                </label>
                            </div>
                        </div>
                        <div class="row"></div>
                        <div class="card-body">
                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerBgColorContainer-{$n}" class="input-group colorpicker-component bannerBgColorContainer" data-color-format="rgba" data-color="{$bannerBgColor}">
                                            <div class="input-group-content">
                                                <label for="bannerBgColor-{$n}" class="control-label">Banner Arkaplan Rengi</label>
                                                <input type="text" class="form-control" id="bannerBgColor-{$n}" name="bannerBgColor[]" value="{$bannerBgColor}">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerContentBoxBgColorContainer-{$n}" class="input-group colorpicker-component bannerContentBoxBgColorContainer" data-color-format="rgba" data-color="{$bannerContentBoxBgColor}">
                                            <div class="input-group-content">
                                                <label for="bannerContentBoxBgColor-{$n}" class="control-label">Yazılar Arkaplan Rengi</label>
                                                <input type="text" class="form-control" id="bannerContentBoxBgColor-{$n}" name="bannerContentBoxBgColor[]" value="{$bannerContentBoxBgColor}">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerTitleColorContainer-{$n}" class="input-group colorpicker-component bannerTitleColorContainer" data-color-format="rgba" data-color="{$bannerTitleColor}">
                                            <div class="input-group-content">
                                                <label for="titleFontColor-{$n}" class="control-label">Başlık Yazı Rengi</label>
                                                <input type="text" class="form-control" id="titleFontColor-{$n}" name="titleFontColor[]" value="{$bannerTitleColor}">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerContentColorContainer-{$n}" class="input-group colorpicker-component bannerContentColorContainer" data-color-format="rgba" data-color="{$bannerContentColor}">
                                            <div class="input-group-content">
                                                <label for="bannerContentFontColor-{$n}" class="control-label">İçerik Yazı Rengi</label>
                                                <input type="text" class="form-control" id="bannerContentFontColor-{$n}" name="bannerContentFontColor[]" value="{$bannerContentColor}">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="titleFontSize-{$n}" name="titleFontSize[]" value="{$bannerTitleFontSize}">
                                        <span class="text-sm" for="titleFontSize">Başlık Yazı Boyutu</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="bannerContentFontSize-{$n}" name="bannerContentFontSize[]" value="{$bannerContentFontSize}">
                                        <span for="contentFontSize">İçerik Yazı Boyutu</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="bannerHeightSize-{$n}" name="bannerHeightSize[]" value="{$bannerHeightSize}">
                                        <span for="contentFontSize">Banner Yüksekliği</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Banner Buton -->
                                <div class="row" id="bannerButtonCustomizationContainer-{$n}">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonColorContainer-{$n}" class="input-group colorpicker-component bannerButtonColorContainer" data-color-format="rgba" data-color="{$bannerButtonBgColor}">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonColor-{$n}" class="control-label">Buton Arkaplan Rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonBgColor-{$n}" name="bannerButtonBgColor[]" value="{$bannerButtonBgColor}">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonHoverColorContainer-{$n}" class="input-group colorpicker-component bannerButtonHoverColorContainer" data-color-format="rgba" data-color="{$bannerButtonHoverBgColor}">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonHoverColor-{$n}" class="control-label">Buton Arkaplan Değişim Rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonHoverBgColor-{$n}" name="bannerButtonHoverBgColor[]" value="{$bannerButtonHoverBgColor}">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonTextColorContainer-{$n}" class="input-group colorpicker-component bannerButtonTextColorContainer" data-color-format="rgba" data-color="{$bannerButtonTextColor}">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonTextColor-{$n}" class="control-label">Buton Yazı Rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonTextColor-{$n}" name="bannerButtonTextColor[]" value="{$bannerButtonTextColor}">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonTextHoverColorContainer-{$n}" class="input-group colorpicker-component bannerButtonTextHoverColorContainer" data-color-format="rgba" data-color="{$bannerButtonTextHoverColor}">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonTextColor-{$n}" class="control-label">Buton Yazı Değişim rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonTextHoverColor-{$n}" name="bannerButtonTextHoverColor[]" value="{$bannerButtonTextHoverColor}">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"></div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bannerButtonTextSize">Buton Yazı Boyutu</label>
                                            <input type="number" class="form-control" id="bannerButtonTextSize-{$n}" name="bannerButtonTextSize[]" value="{$bannerButtonTextSize}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bannerButtonLocation" class="control-label">Buton Konumu</label>
                                            <select name="bannerButtonLocation[]" id="bannerButtonLocation-{$n}" class="form-control">
                                                <option value="0" {$bannerButtonLocation0Selected}>Varsayılan</option>
                                                <option value="1" {$bannerButtonLocation1Selected}>Üst Sol</option>
                                                <option value="2" {$bannerButtonLocation2Selected}>Üst Orta</option>
                                                <option value="3" {$bannerButtonLocation3Selected}>Üst Sağ</option>
                                                <option value="4" {$bannerButtonLocation4Selected}>Orta Sol</option>
                                                <option value="5" {$bannerButtonLocation5Selected}>Orta</option>
                                                <option value="6" {$bannerButtonLocation6Selected}>Orta Sağ</option>
                                                <option value="7" {$bannerButtonLocation7Selected}>Alt Sol</option>
                                                <option value="8" {$bannerButtonLocation8Selected}>Alt Orta</option>
                                                <option value="9" {$bannerButtonLocation9Selected}>Alt Sağ</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    HTML;
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$bannerGroupName?> Düzenle Pozitif Eticaret</title>
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
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/nestable/nestable.css?1423393667" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/summernote/summernote.min.css">
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-datepicker/datepicker3.css?1424887858" />

    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-colorpicker/bootstrap-colorpicker.css?1422823362" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
    <style>
        /*#bannerPreviewButton{
            position: fixed;
            right: 25px;
            top: 75px;
        }*/
        .bannerLabel{position:absolute;left:0;top:0;background-color: rgba(255,255,255,.3)}

        #previewPanel{
            border: 1px dotted #ccc;
            padding: 10px 0;
            margin:10px 0;
            box-sizing: border-box;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            width: 100%;
            overflow: hidden;
            gap:10px;
        }

        /* Genel Slider Önizleme Konteyneri (JS ile oluşturuluyor) */
        .preview-slider-container {
            display: flex;
            overflow-x: auto; /* Tüm slaytları görmek için yatay kaydırma */
            gap: 15px;
            padding: 10px;
            border: 1px dashed #ccc;
            background-color: #f8f9fa;
            min-height: 250px; /* İçeriğe göre ayarlayın */
            align-items: stretch; /* Slayt yüksekliklerini eşitle */
            width: 100%;
            box-sizing: border-box;
        }
        #previewPanel.carousel {
            display: flex;
            overflow-x: auto; /* Tüm slaytları görmek için yatay kaydırma */
            gap: 15px;
            padding: 10px;
            border: 1px dashed #ccc;
            background-color: #f8f9fa;
            min-height: 250px; /* İçeriğe göre ayarlayın */
            align-items: stretch; /* Slayt yüksekliklerini eşitle */
            width: 100%;
            box-sizing: border-box;
        }

        /* Tüm Slaytlar için Temel Stiller */
        .slide {
            position: relative;
            display: flex; /* İçeriği yönetmek için */
            flex-direction: column; /* İçeriği dikey sırala */
            background-color: #fff; /* Varsayılan arkaplan */
            box-sizing: border-box;
            /* .bannerLabel için */
        }

        /* Button location styles */
        .location-0  { /* Alt Orta */
            position: relative;
        }
        .location-1 { /* Üst Sol */
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .location-2  { /* Üst Orta */
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .location-3  { /* Üst Sağ */
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .location-4  { /* Orta Sol */
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
        }
        .location-5  { /* Orta */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .location-6  { /* Orta Sağ */
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }
        .location-7  { /* Alt Sol */
            position: absolute;
            bottom: 10px;
            left: 10px;
        }
        .location-8 { /* Alt Orta */
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .location-9  { /* Alt Sağ */
            position: absolute;
            bottom: 10px;
            right: 10px;
        }

        .single{width:100%;min-width: 100%}
        .double{
            width:calc(50% - 10px); min-width:calc(50% - 10px);
        }
        .triple{
            width:calc(33.33% - 10px);min-width:calc(33.33% - 10px);
        }
        .quad{
            width:calc(25% - 10px);min-width:calc(25% - 10px)
        }
        .quinary{
            width:calc(20% - 10px);min-width:calc(20% - 10px)
        }

    </style>
    <?php
    if($bannerTypeID == 1){
        echo '<link type="text/css" id="slideBoxCss" rel="stylesheet" href="CSS/Carousel.min.css">';
        echo '<link type="text/css" id="slideFullWidthCss" rel="stylesheet" href="CSS/SlideFullWidth.min.css">';
    }
    elseif ($bannerTypeID == 2){
        echo '<link type="text/css" id="topBannerCss" rel="stylesheet" href="CSS/TopBanner.min.css">';
    }
    elseif ($bannerTypeID == 3){
        // Orta Banner için stil dosyalarını Public/CSS/Banners/ altından yükle
        $ortaBannerStyles = [
            'Carousel',
            'BgImageCenterText',
            'FadeFeatureCard',
            'HoverCardBanner',
            'IconFeatureCard',
            'ImageLeftBanner',
            'ImageRightBanner',
            'ImageTextOverlayBottom',
            'ProfileCardBanner'
        ];
        foreach ($ortaBannerStyles as $styleName) {
            echo '<link type="text/css" id="'.$styleName.'" rel="stylesheet" href="CSS/'.$styleName.'.css">';
        }
    }
    elseif ($bannerTypeID == 4){
        echo '<link type="text/css" id="topBannerCss" rel="stylesheet" href="CSS/BottomBanner.min.css">';
    }
    elseif ($bannerTypeID == 5){
        echo '<link type="text/css" id="topBannerCss" rel="stylesheet" href="CSS/PopupBanner.min.css">';
    }
    ?>
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once(ROOT."/_y/s/b/header.php");?>
<div id="base">
    <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">BANNER EKLE / DÜZENLE </li>
                </ol>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form" method="post" id="addBannerForm">
                            <input type="hidden" name="bannerGroupID" id="bannerGroupID" value="<?=$bannerGroupID?>">
                            <div class="card">
                                <div class="card-head">
                                    <ul class="nav nav-tabs" data-toggle="tabs">
                                        <li class="col-md-2">
                                            <div class="form-group">
                                                <!-- label for="languageID" class="control-label">Banner Grubu Gösterim Dili</label -->
                                                <select name="languageID" id="languageID" class="form-control">
                                                    <?php foreach ($languages as $lang) { ?>
                                                        <option value="<?=$lang["languageID"]?>" <?php if($lang["languageID"] == $languageID) echo "selected"; ?>><?=$lang["languageName"]?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </li>
                                        <li class="active">
                                            <a class="active" id="general-tab" data-toggle="tab" href="#bannerType" role="tab">Banner Grubu Tipi</a>
                                        </li>
                                        <li>
                                            <a class="active" id="group-tab" data-toggle="tab" href="#bannerGroup" role="tab">Banner Grubu Görünümü</a>
                                        </li>
                                        <li>
                                            <a id="header-tab" data-toggle="tab" href="#bannerContainer" role="tab">Banner İçeriği</a>
                                        </li>
                                        <li>
                                            <a id="homepage-tab" data-toggle="tab" href="#bannerCssContainer" role="tab">Yayınla</a>
                                        </li>
                                        <li>
                                            <button
                                                type="button"
                                                class="btn btn-warning <?php if($bannerGroupID==0)echo 'hidden'?>"
                                                id="bannerPreviewButton"
                                                href="#offcanvasBanner"
                                                data-toggle="offcanvas"
                                                data-backdrop="false"
                                            >Ön izleme</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body tab-content">
                                    <!-- banner-group -->
                                    <div class="card-body tab-pane active" id="bannerType">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="bannerGroupName"></label>
                                                    <input type="text" name="bannerGroupName" id="bannerGroupName" class="form-control" value="<?=$bannerGroupName?>" required>
                                                    <p class="text-sm">Banner Grup Adı. Düzenlerken hangi bannerı düzenlediğnizi bilmenizi sağlar. Örn: Anasayfa Orta </p>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="bannerTypeID">Banner Tipi seçin:<?=$bannerTypeID?></label>
                                                    <select name="bannerTypeID" id="bannerTypeID" class="form-control">
                                                        <option value="0">Banner Tipi Seçin</option>
                                                        <?php foreach ($bannerTypes as $bannerType) {
                                                            $bannerTypeSelected = $bannerTypeID == $bannerType["id"] ? "selected" : "";
                                                            ?>
                                                            <option value="<?=$bannerType["id"]?>" <?=$bannerTypeSelected?>><?=$bannerType["type_name"]?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group <?=$bannerGroupID==0 ? "hidden" : ""?>" id="bannerKindContainer">
                                                    <label for="bannerKind">Banner Türü:</label>
                                                    <select name="bannerKind" id="bannerKind" class="form-control">
                                                        <option value="0">Banner Türü Seçin</option>
                                                        <option value="text_and_image" <?=($bannerGroupKind=="text_and_image") ? "selected" : ""?>>Görsel ve Metinler</option>
                                                        <option value="only_image" <?=($bannerGroupKind=="only_image") ? "selected" : ""?>>Sadece Görsel</option>
                                                        <option value="only_text" <?=($bannerGroupKind=="only_text") ? "selected" : ""?>>Sadece Metinler</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group <?=$bannerGroupID==0 ? "hidden" : ""?>" id="bannerLayoutIDContainer">
                                                    <label for="bannerLayoutID">Banner Düzenini Seçin:</label>
                                                    <select name="bannerLayoutID" id="bannerLayoutID" class="form-control">
                                                        <option value="">Banner Düzenini Seçin</option>
                                                        <?php foreach($bannerLayouts as $bannerLayout) {
                                                            $bannerLayoutSelected = ($bannerGroupStyleClass == $bannerLayout["layout_group"]) ? "selected" : "";
                                                            echo '<option value="'.$bannerLayout["id"].'" 
                                                            data-group="'.$bannerLayout["layout_group"].'" 
                                                            data-view="'.$bannerLayout["layout_view"].'" 
                                                            data-columns="'.$bannerLayout['columns'].'" 
                                                            data-maxbanners="'.$bannerLayout['max_banners'].'" 
                                                            data-desc="'.$bannerLayout['description'].'" 
                                                            '.$bannerLayoutSelected.'>'.$bannerLayout["layout_name"].'</option>';
                                                            }
                                                            ?>
                                                    </select>
                                                    <p id="bannerLayoutDescription" class=" <?=!empty($bannerLayouts) ? '' : 'hidden'?>"><?=$bannerLayoutDescription?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div id="bannerViewContainer" class="<?=($bannerLayoutView == "single") ? "hidden" : ""?>">
                                                    <div class="form-group">
                                                        <label for="bannerView">Banner Görünümü Seçin:</label>
                                                        <select name="bannerView" id="bannerView" class="form-control">
                                                            <option value="">Banner Düzenini Seçin</option>
                                                            <option value="single" <?=($bannerLayoutView == "single" || $bannerGroupView == "single") ? "selected" : ""?>>Tekli Görünüm</option>
                                                            <option value="double" <?=($bannerLayoutView == "multi" && $bannerGroupView == "double") ? "selected" : ""?>>İkili Görünüm</option>
                                                            <option value="triple" <?=($bannerLayoutView == "multi" && $bannerGroupView == "triple") ? "selected" : ""?>>Üçlü Görünüm</option>
                                                            <option value="quad" <?=($bannerLayoutView == "multi" && $bannerGroupView == "quad") ? "selected" : ""?>>Dörtlü görünüm</option>
                                                            <option value="quinary" <?=($bannerLayoutView == "multi" && $bannerGroupView == "quinary") ? "selected" : ""?>>Beşli Görünüm</option>
                                                        </select>
                                                        <p id="bannerLayoutDescription" class=" <?=!empty($bannerLayouts) ? '' : 'hidden'?>">Yanyana kaç banner görüneceğini seçin</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row"></div>
                                            <div class="col-md-2">
                                                <div class="form-group <?=$bannerGroupID==0 ? "hidden" : ""?>" id="bannerLayoutColumnsContainer">
                                                    <input type="number" id="bannerLayoutColumns" name="bannerLayoutColumns" value="<?=$bannerColumns?>" class="form-control">
                                                    <label for="bannerLayoutColumns">Eklenecek Banner Sayısı</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group <?=$bannerGroupID==0 ? "hidden" : ""?>" id="bannerLayoutMaxBannersContainer">
                                                    <input type="number" id="bannerLayoutMaxBanners" name="bannerLayoutMaxBanners" value="<?=$bannerLayoutMaxBanners?>" readonly class="form-control">
                                                    <label for="bannerLayoutMaxBanners">Max Banner Sayısı</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary btn-sm <?=$bannerGroupID==0 ? "hidden" : ""?>" id="addBannerBox">Seçime Göre Banner Alanı Ekle</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <img src="IMG/<?=$sampleBannerImage?>" style="width:100%; height:auto" id="sampleBannerImage">
                                        </div>
                                    </div>

                                    <!-- banner group -->
                                    <div id="bannerGroup" class="card-body tab-pane">
                                        <div class="col-md-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="bannerGrouptitle"></label>
                                                    <input type="text" name="bannerGroupTitle" id="bannerGroupTitle" class="form-control" value="<?=$bannerGroupTitle?>" placeholder="Banner Grup Başlığı">
                                                    <p class="text-sm">Başlık. Banner grubunun üzerinde görünür (boş olabilir). </p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="bannerGroupDesc"></label>
                                                    <textarea name="bannerGroupDesc" id="bannerGroupDesc" class="form-control" placeholder="Banner Grup Kısa Yazı"><?=$bannerGroupDesc?></textarea>
                                                    <p class="text-sm">Başlığın altında görünür (boş olabilir). </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="text" name="banner-group-bg-color" id="banner-group-bg-color" value="<?=$bannerGroupBackgroundColor?>" class="form-control bscp">
                                                    <label for="banner-group-bg-color">Banner Grubu Arkaplan Rengi</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="text" name="banner-group-title-color" id="banner-group-title-color" value="<?=$bannerGroupTitleColor?>" class="form-control bscp" >
                                                    <label for="banner-group-title-color">Banner Grubu Başlık Rengi</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="text" name="banner-group-desc-color" id="banner-group-desc-color" value="<?=$bannerGroupDescColor?>" class="form-control bscp" >
                                                    <label for="banner-group-desc-color">Banner Grubu Açıklama Yazı Rengi</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="banner-group-full-size">Banner Grup Görünüm Türü:</label>
                                                <select name="banner-group-full-size" id="banner-group-full-size" class="form-control">
                                                    <option value="1" <?=($bannerGroupFullSize==1) ? "selected" : ""?>>Tam Ekran</option>
                                                    <option value="0" <?=($bannerGroupFullSize==0) ? "selected" : ""?>>İçerik Boyutu</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="banner-full-size">Banner Görünüm Türü:</label>
                                                <select name="banner-full-size" id="banner-full-size" class="form-control">
                                                    <option value="1" <?=($bannerFullSize==1) ? "selected" : ""?>>Tam Ekran</option>
                                                    <option value="0" <?=($bannerFullSize==0) ? "selected" : ""?>>İçerik Boyutu</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- banner-container -->
                                    <div id="bannerContainer" class="card-body tab-pane">
                                        <?php
                                        foreach ($banners as $key => $banner) {
                                            $bannerStyleID = $banner["style_id"];
                                            $bannerStyle = $bannerStyleModel->getStyleById($bannerStyleID);
                                            if($bannerStyle) {
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
                                            else{
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

                                            $generateBannerData = [
                                                "key" => $key+1,
                                                "title" => $banner["title"],
                                                "content" => $banner["content"],
                                                "image" => $banner["image"],
                                                "link" => $banner["link"],
                                                "active" => $banner["active"],
                                                "bannerHeightSize" => $bannerHeightSize,
                                                "bannerBgColor" => $bannerBgColor,
                                                "bannerContentBoxBgColor" => $bannerContentBoxBgColor,
                                                "bannerTitleColor" => $bannerTitleColor,
                                                "bannerTitleFontSize" => $bannerTitleFontSize,
                                                "bannerContentColor" => $bannerContentColor,
                                                "bannerContentFontSize" => $bannerContentFontSize,
                                                "bannerShowButton" => $bannerShowButton,
                                                "bannerButtonTitle" => $bannerButtonTitle,
                                                "bannerButtonLocation" => $bannerButtonLocation,
                                                "bannerButtonBgColor" => $bannerButtonBgColor,
                                                "bannerButtonTextColor" => $bannerButtonTextColor,
                                                "bannerButtonHoverBackground" => $bannerButtonHoverBackground,
                                                "bannerButtonHoverTextColor" => $bannerButtonHoverTextColor,
                                                "bannerButtonTextSize" => $bannerButtonTextSize
                                            ];
                                            echo generateBannerBox($generateBannerData);
                                        }
                                        ?>
                                    </div>

                                    <!-- banner grubu için ek css alanı -->
                                    <div class="card-body tab-pane" id="bannerCssContainer">
                                        <!-- bannerlar için nerede gösterileceğini seç (tüm site, kategoriler, sayfalar) -->
                                        <div id="languageContainer" class="card-body">
                                        
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                            
                                                    <label for="bannerLocation">Bannerın Gösterileceği Yer</label>
                                                    <select name="bannerLocation" id="bannerLocation" class="form-control">
                                                        <option value="0" <?=(empty($bannerDisplayPageIDs) && empty($bannerDisplayCategoryIDs)) ? "selected" : "" ?>>Tüm Site</option>
                                                        <option value="1" <?=(!empty($bannerDisplayCategoryIDs) && empty($bannerDisplayPageIDs)) ? "selected" : "" ?>>Kategoriler</option>
                                                        <option value="2" <?=(!empty($bannerDisplayPageIDs) && empty($bannerDisplayCategoryIDs)) ? "selected" : "" ?>>Sayfalar</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="bannerStartDate">Gösterim Başlangıç Tarihi</label>
                                                    <input type="text" name="bannerStartDate" id="bannerStartDate" class="form-control datepicker" value="<?=$bannerVisibilityStart?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="bannerEndDate">Gösterim Bitiş Tarihi</label>
                                                    <input type="text" name="bannerEndDate" id="bannerEndDate" class="form-control datepicker" value="<?=$bannerVisibilityEnd?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2 hidden">
                                                <div class="form-group">
                                                    <label for="bannerDuration">Banner Süresi</label>
                                                    <input type="text" name="bannerDuration" id="bannerDuration" class="form-control" value="<?=$bannerDuration?>">
                                                    <p class="text-sm">Açılış banner ve Slayt için geçerlidir. 0 varsayılan değer</p>
                                                </div>
                                            </div>
                                            <!-- kayıt butonu -->
                                            <div id="saveButtonContainer" class="col-md-2 <?=!empty($banners) ? '' : 'hidden'?>">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary"><?=$buttonName?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- kategori ya da sayfa seçilirse bunları listeleyip seçecek bir alan yapalım -->
                                        <div id="bannerDisplayLocationContainer" class="card-body <?=(empty($bannerDisplayPageIDs) && empty($bannerDisplayCategoryIDs)) ? "hidden" : "" ?>">
                                            <div class="form-group">
                                                <label for="bannerDisplayLocation">Gösterim yerleri seçin</label>
                                                <select name="bannerDisplayLocation" id="bannerDisplayLocation" class="form-control" multiple aria-multiselectable="true">
                                                    <?php if(!empty($bannerDisplayPageIDs)): ?>
                                                        <?php
                                                        foreach($bannerDisplayPageIDs as $pageID) {
                                                            $page = $adminPageModel->getPage($pageID);
                                                            //print_r($page);
                                                            if(!empty($page)) {
                                                                $pageID = $page['pageID'];
                                                                $pageName = $page['pageName'];
                                                                ?>
                                                                <option value="<?=$pageID?>" selected><?=$pageName?></option>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                    <?php endif; ?>
                                                    <?php if(!empty($bannerDisplayCategoryIDs)): ?>
                                                        <?php foreach($bannerDisplayCategoryIDs as $categoryID): ?>
                                                            <?php 
                                                                $category = $adminCategoryModel->getCategoryByIdOrUniqId($categoryID,"");
                                                                //print_r($category);
                                                                if(!empty($category)) {
                                                                    $categoryID = $category['categoryID'];
                                                                    $categoryName = $category['categoryName'];
                                                                    ?>
                                                                    <option value="<?=$categoryID?>" selected><?=$categoryName?></option>
                                                                <?php
                                                                } 
                                                                else {
                                                                    continue; // Eğer kategori bulunamazsa bu döngüden çık
                                                                }
                                                            ?>  
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="bannerCss">Banner Grubu için ek CSS</label>
                                            <textarea name="bannerCss" id="bannerCss" class="form-control style-default-light" rows="5" style="padding:5px"><?=$customCss?></textarea>
                                            <div class="code">
                                                Örnek CSS Sınıf Yapısı (Seçilen banner sitili): <span id="className" class="text-danger"></span>
                                                <pre>
        /* Ana içerik alanı */
         .banner-container{
              .banner-item {
              ├─  .banner-image {
              │    /* Görsel sarmalayıcı */
              │    └─ img {
              │         /* Görsel etiketi */
              │       }
              └─ } .content-box {
              │    /* Metin ve buton sarmalayıcı */
              │
                   ├─  .title {
                   │    /* Başlık */
                   │  }
                   ├─  .content {
                   │    /* İçerik metni */
                   │  }
                   └─  .button-container {
                   │    /* Buton sarmalayıcı */
                        └─ banner-button {
                             /* Buton etiketi */
                           }
                        }
                    }
                }
            }
                </pre>

                                                <em>Not: Bu yapı, seçilen banner düzenine ('Yalnız Metin', 'Metin ve Görsel' vb.) ve uygulanan stile göre farklılık gösterebilir. Tarayıcının geliştirici araçları (incele/inspect) ile gerçek yapıyı kontrol edebilirsiniz.</em>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>
    <?php require_once(ROOT."/_y/s/b/rightCanvas.php");?>

    <label
            class="btn hidden"
            href="#offcanvas-imageUpload"
            id="uploadImageByLeftCanvas2"
            data-uploadtarget="Banner"
            data-toggle="offcanvas"
            title="Yeni Resim Yükle"></label>
    <label
            class="btn hidden"
            href="#offcanvas-imageSearch"
            id="selectImageByRightCanvas2"
            data-toggle="offcanvas"
            title="Listeden Resim Seç"></label>

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
    <a href="#offcanvas-searchContent"
        id="searchContentButton2"
        data-toggle="offcanvas"
        data-backdrop="false"
        class="hidden"
    title="Kategori/Sayfa Seç"></a>

    <style>
        #searchContentResult li,.menuAreaContainer li{
            min-height: 30px; line-height: 30px;display: inline-flex;width: 100%;
        }
        .menuAreaContainer li{
            background-color: #f9f9f9; min-height: 30px; line-height: 30px; border-bottom: solid 1px #999;
        }
        .menuAreaContainer{
            background: aliceblue;border: 1px solid #ccc;
        }
        .menuAreaContainer ul{
            min-height: 150px;
        }
    </style>

<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/summernote/summernote.min.js"></script>
<script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>

<script src="/_y/assets/js/libs/nestable/jquery.nestable.js"></script>
<script src="/_y/assets/js/libs/select2/select2.js"></script>
<script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<!-- Türkçe dil dosyasını ekleyin -->
<script src="/_y/assets/js/libs/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>
<script src="/_y/assets/js/panel/ChangeAlertModalHeaderColor.min.js"></script>
<script src="JS/BannerImage.min.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
<script>
    $("#addBannerphp").addClass("active");
    let imgRoot = "<?=imgRoot?>";

    $("#bannerContainer").sortable({
        handle: '.fa.fa-arrows',
        items: '.card.panel',
        opacity: 0.8,
        cursor: 'move',
        axis: 'y',
        update: function () {
            let data = $(this).sortable('serialize');
            console.log(data);
        }
    });

    let bannerImage = "/_y/assets/img/header.jpg";

    let bannerBox = `
        <div class="card panel" id="card-panel-[n]">
            <div class="card-head style-accent-bright collapsed">
                <header
                       data-toggle="collapse"
                       data-parent="#bannerContainer"
                       data-target="#accordion[n]-container">Banner [n] Özellikleri
                </header>
                <div class="tools">
                    <a class="btn btn-icon-toggle removeBannerBox" data-id="[n]"><i class="fa fa-trash"></i></a>
                    <a class="btn btn-icon-toggle"><i class="fa fa-arrows"></i></a>
                    <a class="btn btn-icon-toggle"
                       data-toggle="collapse"
                       data-parent="#bannerContainer"
                       data-target="#accordion[n]-container"><i class="fa fa-angle-down"></i>
                    </a>
                </div>
            </div>
            <div id="accordion[n]-container" class="collapse">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <!-- Banner Başlığı -->
                            <div class="form-group">
                                <label for="bannerSlogan-[n]" class="control-label">Banner Başlığı</label>
                                <input type="text" class="form-control bannerSlogan" id="bannerSlogan-[n]" name="bannerSlogan[]" value="Banner Başlığı">
                            </div>
                            <div class="form-group">
                                <label for="bannerButton-[n]" class="control-label">Buton Yazısı</label>
                                <input type="text" class="form-control" id="bannerButton-[n]" name="bannerButton[]" value="Detaylar">
                                <p class="text-sm">Boş bırakılırsa buton görünmez.</p>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <!-- Banner Yazısı -->
                            <div class="form-group">
                                <label for="bannerText-[n]" class="control-label">Banner Yazısı</label>
                                <textarea class="form-control bannerText" id="bannerText-[n]" name="bannerText[]">Banner İçerik Yazısı</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                            <div class="checkbox checkbox-styled">
                                <label for="bannerActive-[n]">
                                    <input type="checkbox" id="bannerActive-[n]" name="bannerActive[]" checked value="1">
                                    <span>Banner Aktif</span>
                                </label>
                                <p class="text-sm">Bu seçenek işaretliyse banner yayında olacaktır.</p>
                            </div>
                        </div>
                    </div>
                        <div class="col-md-7">
                            <!-- Banner Linki -->
                            <div class="col-md-1">
                                <a id="searchContentButton-[n]" class="searchContentButton btn btn-sm active" href="#" data-id="[n]" title="Kategori/Sayfa Seç">
                                    <i class="fa fa-list"></i>
                                </a>
                            </div>
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label for="bannerLink" class="control-label">Banner Bağlantısı</label>
                                    <input type="text" class="form-control" id="bannerLink-[n]" name="bannerLink[]" value="#">
                                    <p class="text-sm">Banner Tıklanınca gidecek sayfa adresi. Aynı sayfada kalmak için # girin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Görseli-->
                    <div class="tools text-right">
                        <label
                            class="btn removeBannerImage"
                            href="#"
                            data-id="[n]"
                            title="Görseli Sil">
                            <i class="fa fa-trash"></i>
                            Banner Görseli Temizle
                        </label>

                        <label
                                class="btn uploadImageByLeftCanvas"

                                id="uploadImageByLeftCanvas-[n]"
                                data-target="card-panel-[n]"
                                data-uploadtarget="Banner"

                                title="Yeni Resim Yükle">
                            <i class="fa fa-upload"></i>
                            Banner Resmi Yükle
                        </label>

                        <label
                            class="btn selectImageByRightCanvas"

                            id="selectImageByRightCanvas-[n]"
                            data-target="card-panel-[n]"

                            title="Listeden Resim Seç">
                            <i class="fa fa-file-image-o"></i>
                            Banner Resmi Seç
                        </label>
                    </div>
                    <div class="form-group hidden">
                        <label for="bannerImage" class="control-label">Banner Görseli</label>
                        <img class="bannerImage" id="bannerImage-[n]" src="${bannerImage}" alt="Banner Görseli" style="max-width: 100%; height: auto">
                        <input type="hidden" id="bannerImage" name="bannerImage[]" value="">
                    </div>
                    <div class="row"><div class="card-body"></div></div>
                    <div class="row " id="bannerCustomizationContainer-[n]">
                        <div class="card-head-sm">
                            <div class="col-md-6">
                                <header>Banner Görünümü</header>
                                <p class="text-sm">Boş bırakılan değerler dikkate alınmaz. Banner varsayılan ayarlarla görünür.</p>
                            </div>
                            <div class="col-md-6">
                                <header>Buton Görünümü</header>
                                <label for="showButton-[n]" class="text-sm">
                                    <input type="checkbox" id="showButton-[n]" name="showButton[]" checked value="1">
                                    <span>Buton Ekle</span>
                                </label>
                            </div>
                        </div>
                        <div class="row"></div>
                        <div class="card-body">
                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerBgColorContainer-[n]" class="input-group colorpicker-component bannerBgColorContainer" data-color-format="rgba" data-color="rgba(255,243,0,1)">
                                            <div class="input-group-content">
                                                <label for="bannerBgColor-[n]" class="control-label">Banner Arkaplan Rengi</label>
                                                <input type="text" class="form-control" id="bannerBgColor-[n]" name="bannerBgColor[]" value="rgba(255,243,0,1)">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerContentBoxBgColorContainer-[n]" class="input-group colorpicker-component bannerContentBoxBgColorContainer" data-color-format="rgba" data-color="rgba(255,255,255,1)">
                                            <div class="input-group-content">
                                                <label for="bannerContentBoxBgColor-[n]" class="control-label">Yazılar Arkaplan Rengi</label>
                                                <input type="text" class="form-control" id="bannerContentBoxBgColor-[n]" name="bannerContentBoxBgColor[]" value="rgba(255,255,255,1)">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerTitleColorContainer-[n]" class="input-group colorpicker-component bannerTitleColorContainer" data-color-format="rgba" data-color="rgba(0,0,0,1)">
                                            <div class="input-group-content">
                                                <label for="titleFontColor-[n]" class="control-label">Başlık Yazı Rengi</label>
                                                <input type="text" class="form-control" id="titleFontColor-[n]" name="titleFontColor[]" value="rgba(0,0,0,1)">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div id="bannerContentColorContainer-[n]" class="input-group colorpicker-component bannerContentColorContainer" data-color-format="rgba" data-color="rgba(0,0,0,1)">
                                            <div class="input-group-content">
                                                <label for="bannerContentFontColor-[n]" class="control-label">İçerik Yazı Rengi</label>
                                                <input type="text" class="form-control" id="bannerContentFontColor-[n]" name="bannerContentFontColor[]" value="rgba(0,0,0,1)">
                                            </div>
                                            <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="titleFontSize-[n]" name="titleFontSize[]" value="24">
                                        <span class="text-sm" for="titleFontSize">Başlık Yazı Boyutu</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="bannerContentFontSize-[n]" name="bannerContentFontSize[]" value="18">
                                        <span for="contentFontSize">İçerik Yazı Boyutu</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="bannerHeightSize-[n]" name="bannerHeightSize[]" value="120">
                                        <span for="contentFontSize">Banner Yüksekliği</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Banner Buton -->
                                <div class="row" id="bannerButtonCustomizationContainer-[n]">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonColorContainer-[n]" class="input-group colorpicker-component bannerButtonColorContainer" data-color-format="rgba" data-color="rgba(0,0,0,1)">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonColor-[n]" class="control-label">Buton Arkaplan Rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonBgColor-[n]" name="bannerButtonBgColor[]" value="rgba(0,0,0,1)">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonHoverColorContainer-[n]" class="input-group colorpicker-component bannerButtonHoverColorContainer" data-color-format="rgba" data-color="rgba(255,255,255,1)">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonHoverColor-[n]" class="control-label">Buton Arkaplan Değişim Rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonHoverBgColor-[n]" name="bannerButtonHoverBgColor[]" value="rgba(255,255,255,1)">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonTextColorContainer-[n]" class="input-group colorpicker-component bannerButtonTextColorContainer" data-color-format="rgba" data-color="rgba(255,255,255,1)">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonTextColor-[n]" class="control-label">Buton Yazı Rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonTextColor-[n]" name="bannerButtonTextColor[]" value="rgba(255,255,255,1)">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div id="bannerButtonTextHoverColorContainer-[n]" class="input-group colorpicker-component bannerButtonTextHoverColorContainer" data-color-format="rgba" data-color="rgba(0,0,0,1)">
                                                <div class="input-group-content">
                                                    <label for="bannerButtonTextColor-[n]" class="control-label">Buton Yazı Değişim Rengi</label>
                                                    <input type="text" class="form-control" id="bannerButtonTextHoverColor-[n]" name="bannerButtonTextHoverColor[]" value="rgba(0,0,0,1)">
                                                </div>
                                                <div class="input-group-addon"><i style="border:solid 1px #000;"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"></div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bannerButtonTextSize">Buton Yazı Boyutu</label>
                                            <input type="number" class="form-control" id="bannerButtonTextSize-[n]" name="bannerButtonTextSize[]" value="18">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bannerButtonLocation" class="control-label">Buton Konumu</label>
                                            <select name="bannerButtonLocation[]" id="bannerButtonLocation-[n]" class="form-control">
                                                <option value="0" selected>Varsayılan</option>
                                                <option value="1">Üst Sol</option>
                                                <option value="2">Üst Orta</option>
                                                <option value="3">Üst Sağ</option>
                                                <option value="4">Orta Sol</option>
                                                <option value="5">Orta</option>
                                                <option value="6">Orta Sağ</option>
                                                <option value="7">Alt Sol</option>
                                                <option value="8">Alt Orta</option>
                                                <option value="9">Alt Sağ</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    let bannerBox_onlyText = `
        <div id="bannerContainer-[n]" class="[class]">
            <span class="bannerLabel">Banner [n]</span>
            <div class="banner-content-container" id="banner-content-container-[n]" >
                <div class="banner-content-box" id="bannerContentBox-[n]">
                    <h2 id="bannerSlogan-[n]" class="bannerTitle">Banner Başlığı</h2>
                    <div id="bannerContent-[n]" class="bannerContent">Banner İçerik Yazısı</div>
                    <div id="bannerButtonContainer-[n]" class="bannerButton location-0">
                        <button type="button" id="bannerButton-[n]">Detaylar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    let bannerBox_onlyImage = `
        <div id="bannerContainer-[n]" class="[class] onlyImage">
            <span class="bannerLabel">Banner [n]</span>
            <div class="banner-content-container" id="banner-content-container-[n]" >
                <img src="/_y/m/r/banner/banner_slider1.jpg" id="bannerImage-[n]" alt="Banner Görseli" style="max-width: 100%; height: auto">
                <div id="bannerButtonContainer-[n]" class="bannerButton location-0">
                    <button type="button" id="bannerButton-[n]">Detaylar</button>
                </div>
            </div>
        </div>
    `;
    let bannerBox_TextAndImage = `
        <div id="bannerContainer-[n]" class="[class]">
            <span class="bannerLabel">Banner [n]</span>
            <div class="banner-content-container" id="banner-content-container-[n]" >
                <div class="banner-image-wrapper">
                    <img src="/_y/m/r/banner/banner_kare.jpg" id="bannerImage-[n]" alt="Banner Görseli">
                </div>
                <div class="banner-content-box" id="bannerContentBox-[n]">
                    <h2 id="bannerSlogan-[n]" class="bannerTitle">Banner Başlığı</h2>
                    <div id="bannerContent-[n]" class="bannerContent">Banner İçerik Yazısı</div>
                    <div id="bannerButtonContainer-[n]" class="bannerButton location-0">
                        <button type="button" id="bannerButton-[n]">Detaylar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    const bannerAllLayouts = {<?php
        // Gruplamak için geçici dizi oluştur
        $groupedLayouts = [];

        foreach ($bannerAllLayouts as $layout) {
            $typeId = $layout['type_id'];
            $layoutGroup = $layout['layout_group'];

            // Bu type_id için bir grup yoksa oluştur
            if (!isset($groupedLayouts[$typeId])) {
                $groupedLayouts[$typeId] = [];
            }

            // Layout group daha önce eklenmemişse ekle
            $exists = false;
            foreach ($groupedLayouts[$typeId] as $item) {
                if ($item['value'] === $layoutGroup) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $groupedLayouts[$typeId][] = [
                    'value' => $layoutGroup,
                    'text' => $layout['layout_name']
                ];
            }
        }

        // JavaScript nesnesi formatında çıktı al
        foreach ($groupedLayouts as $typeId => $layouts) {
            echo "'$typeId': " . json_encode($layouts) . ",\n";
        }
        ?>};

    function loadCSS(bannerTypeID){
        if(bannerTypeID == 1){
            // Slayt CSS dosyalarını dinamik olarak yükle
            if (!document.getElementById('slideBoxCss')) {
                const slideBoxCss = document.createElement('link');
                slideBoxCss.id = 'slideBoxCss';
                slideBoxCss.rel = 'stylesheet';
                slideBoxCss.type = 'text/css';
                slideBoxCss.href = 'CSS/SlideBox.min.css';
                document.head.appendChild(slideBoxCss);
            }

            if (!document.getElementById('slideFullWidthCss')) {
                const slideFullWidthCss = document.createElement('link');
                slideFullWidthCss.id = 'slideFullWidthCss';
                slideFullWidthCss.rel = 'stylesheet';
                slideFullWidthCss.type = 'text/css';
                slideFullWidthCss.href = 'CSS/SlideFullWidth.min.css';
                document.head.appendChild(slideFullWidthCss);
            }
        }
        else if(bannerTypeID == 2){
            // Tepe banner CSS dosyasını yükle
            if (!document.getElementById('topBannerCss')) {
                const topBannerCss = document.createElement('link');
                topBannerCss.id = 'topBannerCss';
                topBannerCss.rel = 'stylesheet';
                topBannerCss.type = 'text/css';
                topBannerCss.href = 'CSS/TopBanner.min.css';
                document.head.appendChild(topBannerCss);
            }
        }
        else if(bannerTypeID == 3){
            // Orta Banner css dosyalarını toplu yükleyeceğiz.
            let MiddebannerCss = [
                "BgImageCenterText","FadeFeatureCard","HoverCardBanner","IconFeatureCard","ImageLeftBanner","ImageRightBanner","ImageTextOverlayBottom","ProfileCardBanner","Carousel"
            ];
            for (let i = 0; i < MiddebannerCss.length; i++) {
                if (!document.getElementById(MiddebannerCss[i] + 'Css')) {
                    const middebannerCss = document.createElement('link');
                    middebannerCss.id = MiddebannerCss[i];
                    middebannerCss.rel = 'stylesheet';
                    middebannerCss.type = 'text/css';
                    middebannerCss.href = 'CSS/' + MiddebannerCss[i] + ".min.css";
                    document.head.appendChild(middebannerCss);
                }
            }
        }
        else if(bannerTypeID == 4){
            // altpe banner CSS dosyasını yükle
            if (!document.getElementById('bottomBannerCss')) {
                const bottomBannerCss = document.createElement('link');
                bottomBannerCss.id = 'bottomBannerCss';
                bottomBannerCss.rel = 'stylesheet';
                bottomBannerCss.type = 'text/css';
                bottomBannerCss.href = 'CSS/BottomBanner.min.css';
                document.head.appendChild(bottomBannerCss);
            }

            if (!document.getElementById('carouselBannerCss')) {
                const carouselBannerCss = document.createElement('link');
                carouselBannerCss.id = 'carouselBannerCss';
                carouselBannerCss.rel = 'stylesheet';
                carouselBannerCss.type = 'text/css';
                carouselBannerCss.href = 'CSS/Carousel.min.css';
                document.head.appendChild(carouselBannerCss);
            }
        }
        else if(bannerTypeID == 5){
            // Popup banner CSS dosyasını yükle
            if (!document.getElementById('popupBannerCss')) {
                const popupBannerCss = document.createElement('link');
                popupBannerCss.id = 'bottomBannerCss';
                popupBannerCss.rel = 'stylesheet';
                popupBannerCss.type = 'text/css';
                popupBannerCss.href = 'CSS/PopupBanner.min.css';
                document.head.appendChild(popupBannerCss);
            }
        }
    }

    /*const bannerStylesByType = {
        '1': [ // bannerTypeID 1 için stil
            { value: 'fullwidth', text: 'Tam Ekran Slayt', image:'IMG/slider.jpg'},
            { value: 'box-double', text: 'Carousel 2li Görünüm', image:'IMG/carousel-2.jpg'},
            { value: 'box-triple', text: 'Carousel 3lü Görünüm', image:'IMG/carousel-3.jpg'},
            { value: 'box-quad', text: 'Carousel 4lü Görünüm', image:'IMG/carousel-4.jpg'}
        ],
        '2': [ // bannerTypeID 2 için stil
            { value: 'top-banner', text: 'Arkaplan Resim ve Yazı Ortalı', image:'IMG/top-banner.jpg'}
        ],
        '3': [ // bannerTypeID 3 için stiller
            { value: 'Carousel', text: 'Kayan Banner', image:'IMG/carousel-4.jpg.jpg' },
            { value: 'ImageRightBanner', text: 'Resim Sağda, Yazı Solda', image:'IMG/ImageRightBanner.jpg' },
            { value: 'ImageLeftBanner', text: 'Resim Solda, Yazı Sağda', image:'IMG/ImageLeftBanner.jpg' },
            { value: 'HoverCardBanner', text: 'Kart Üzerine Gelince İçerik', image:'IMG/HoverCardBanner.jpg' },
            { value: 'ProfileCard', text: 'Profil Kartı Görünümü', image:'IMG/ProfileCard.jpg' },
            { value: 'IconFeatureCard', text: 'Icon Özellik Kartı', image:'IMG/IconFeatureCard.jpg' },
            { value: 'FadeFeatureCard', text: 'Fade Özellik Kartı', image:'IMG/FadeFeatureCard.jpg'},
            { value: 'BgImageCenterText', text: 'Arkaplan Resimli Ortalanmış İçerik', image:'IMG/BgImageCenterText.jpg' },
            { value: 'ImageTextOverlayBottom', text: 'Resim Üzeri Alt Bant Metin', image:'IMG/ImageTextOverlayBottom.jpg' }
        ],
        '4': [ // bannerTypeID 4 için stil
            { value: 'bottom-banner', text: 'Arkaplan Resim ve Yazı Ortalı', image:'IMG/bottom-banner.jpg'}
        ],
        '5': [ // bannerTypeID 5 için stil
            { value: 'popup-banner', text: 'Arkaplan Resim ve Yazı Ortalı'},
            { value: 'Carousel', text: 'Kayan Banner', image:'IMG/carousel-4.jpg' }
        ]
    };*/

    function observeImageSrcChange(bannerCount) {
        const sourceImage = $("#card-panel-" + bannerCount + " #bannerImage-" + bannerCount);

        if(sourceImage.length > 0){ // Elemanın var olup olmadığını kontrol et
            console.log("kaynak img var:" + sourceImage.attr("id"));
        } else {
            console.error("Kaynak resim bulunamadı: #card-panel-" + bannerCount + " #bannerImage-" + bannerCount);
            return; // Eğer eleman yoksa fonksiyonu sonlandır
        }

        const targetImage = $("#previewPanel #bannerImage-" + bannerCount);

        if(targetImage.length > 0){
            console.log("hedef img var:" + targetImage.attr("id"));
        }
        else {
            console.error("Hedef resim bulunamadı: #previewPanel #bannerImage-" + bannerCount);
            //return; // Eğer eleman yoksa fonksiyonu sonlandır
        }

        const targetContainer = $("#bannerContainer-" + bannerCount);
        if(targetContainer.length > 0){
            console.log("hedef container var:" + targetContainer.attr("id"));
        } else {
            console.error("Hedef container bulunamadı: #bannerContainer-" + bannerCount);
            return; // Eğer eleman yoksa fonksiyonu sonlandır
        }

        // Gözlemci callback fonksiyonu
        const callback = function(mutationsList, observer) {
            for(const mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                    const newSrc = mutation.target.src; // Değişen img etiketinin yeni src'si
                    console.log('Banner ' + bannerCount + ' src değişti:', newSrc);

                    let bannerTypeID = $("#bannerTypeID").val();
                    console.log("bannerTypeID: " + bannerTypeID);

                    let bannerLayout = $("#bannerLayoutID");
                    let selectedOption = bannerLayout.find("option:selected");

                    let bannerLayoutID = bannerLayout.val();
                    console.log("bannerLayoutSelectedID: " + bannerLayoutID);

                    let layoutGroup = selectedOption.data("group");
                    console.log("layoutGroup: " + layoutGroup);

                    let layoutView = selectedOption.data("view");
                    console.log("layoutView: " + layoutView);

                    let imageTarget = "";

                    if(layoutGroup === "text"){
                        imageTarget = "background";
                    }
                    else{
                        imageTarget = "src";
                        if(bannerTypeID == 2){
                            imageTarget = "background";
                        }
                    }

                    console.log("imageTarget: " + imageTarget);

                    // Önizleme alanının arkaplanını güncelle

                    // src boş değilse veya sayfanın kendi adresi değilse
                    if(imageTarget === "background"){
                        targetContainer.css("background-image", "url(" + newSrc + ")");
                    }
                    else{
                        targetImage.attr("src",newSrc);
                    }
                    console.log('Önizleme güncellendi for banner ' + bannerCount);

                }
            }
        };

        // Gözlemci örneği oluştur
        const observer = new MutationObserver(callback);

        // Gözlemciyi yapılandır: Sadece 'src' özniteliğindeki değişiklikleri izle
        const config = { attributes: true, attributeFilter: ['src'] };

        // sourceImage'in bir jQuery nesnesi olup olmadığını kontrol et
        if (sourceImage.length > 0) {
            // DOM Node'una erişmek için .get(0) kullan
            observer.observe(sourceImage[0], config);
            console.log('MutationObserver başlatıldı for banner ' + bannerCount);
        } else {
            console.error('Banner ' + bannerCount + ' için #bannerImage-' + bannerCount + ' bulunamadı!');
        }
    }

    $(document).ready(function(){
        $('select').select2();

        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd",
            startDate: "today",
            language: "tr"
        });

        let today = new Date();
        let nextYear = new Date();
        nextYear.setFullYear(today.getFullYear() + 1);

        let formatDate = function(date) {
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            return date.getFullYear() + "-" + month + "-" + day;
        };

        <?php if($bannerVisibilityStart === ""){ ?>

        $('#bannerStartDate').datepicker('setDate', formatDate(today));
        $('#bannerEndDate').datepicker('setDate', formatDate(nextYear));

        <?php } ?>

        function createContentListItem(contentType, contentID, contentUniqID, seoTitle, seoLink, targetMenu, subCategory, contentOriginalTitle="") {
            console.log(contentType, contentID, contentUniqID, seoTitle, seoLink, targetMenu, subCategory);
            let contentList = '<li ';
            contentList += 'data-id="' + contentID + '" ';
            contentList += 'data-uniqid="'+ targetMenu + '-' + contentUniqID + '" ';
            contentList += 'data-orjuniqid="' + contentUniqID + '" ';
            contentList += 'data-link="' + seoLink + '"';
            contentList += 'data-type="' + contentType + '"> ';
            contentList += '<div class="col-md-2">';
            contentList += '<a class="btn btn-flat ink-reaction btn-default handle"><i class="fa fa-link"></i></a>';
            contentList += '</div>';
            if(contentOriginalTitle !== ""){
                contentOriginalTitle = '<br><label class="text-sm text-default-light">['+ contentOriginalTitle +']</label>';
            }
            if (subCategory>0) {
                contentList += "<div class='col-md-6'><div class='checkbox checkbox-styled tile-text'><label><input type='checkbox'><span>"+ seoTitle +"</span></label></div></div>";
            } else {
                contentList += '<div class="col-md-6"><span>'+ seoTitle +  contentOriginalTitle +'</span> </div>';
            }
            contentList += '<div class="col-md-2">';
            contentList += '<a class="btn btn-flat ink-reaction btn-default editMenu"><i class="fa fa-pencil"></i></a>';
            contentList += '</div>';
            contentList += '<div class="col-md-2">';
            contentList += '<a class="btn btn-flat ink-reaction btn-default menuDelete"><i class="fa fa-trash"></i></a>';
            contentList += '</div>';
            contentList += '</li>';
            return contentList;
        }

        $(document).on('click', '.searchContentButton', function(e) {
            e.preventDefault();
            var targetMenu = $(this).data("id");
            $("#targetMenu").val(targetMenu);
            
            $("#searchContentButton2").click();
            
        });

        $(document).on("keyup","#searchContent",function () {
            var searchText = $(this).val();
            var languageID = $("#languageID").val();
            var targetMenu = $("#targetMenu").val();
            $.ajax({
                url: "/App/Controller/Admin/AdminContentController.php",
                type: "GET",
                data: {
                    action: "searchContent",
                    searchText: searchText,
                    languageID: languageID
                },
                success: function (response) {
                    console.log(response);
                    data = JSON.parse(response);
                    if (data.status === "success") {
                        var contentResult = data.contentData;
                        var contentList = "";
                        for (var i = 0; i < contentResult.length; i++) {
                            let contentType = contentResult[i].contentType;
                            let contentID = contentResult[i].contentID;
                            let contentUniqID = contentResult[i].contentUniqID;
                            let contentTitle = contentResult[i].contentTitle;
                            let seoTitle = contentResult[i].seoTitle;
                            let seoLink = contentResult[i].seoLink;
                            let subCategory = contentResult[i].subCategory;
                            let contentOriginalTitle =  contentResult[i].contentOriginalTitle;

                            contentList += createContentListItem(contentType, contentID, contentUniqID, contentTitle, seoLink, targetMenu, subCategory,contentOriginalTitle);
                        }
                        $("#searchContentResult ul").html(contentList);
                    }
                }
            });
        });

        //#searchType value category ise kategori listeleyelim, page ise sayfa listeleyelim. listeleme işlemini dile göre yapalım
        $(document).on("change","#searchType",function () {
            var targetMenu = $("#targetMenu").val();
            var searchType = $(this).val();
            var languageID = $("#languageID").val();
            var searchText = $("#searchContent").val();
            $.ajax({
                url: "/App/Controller/Admin/AdminContentController.php",
                type: "GET",
                data: {
                    action: "searchContentBySearchType",
                    searchText: searchText,
                    languageID: languageID,
                    searchType: searchType
                },
                success: function (response) {
                    console.log(response);
                    data = JSON.parse(response);
                    if (data.status == "success") {
                        var contentResult = data.contentData;
                        var contentList = "";
                        for (var i = 0; i < contentResult.length; i++) {
                            let contentType = contentResult[i].contentType;
                            let contentID = contentResult[i].contentID;
                            let contentUniqID = contentResult[i].contentUniqID;
                            let contentTitle = contentResult[i].contentTitle;
                            let seoTitle = contentResult[i].seoTitle;
                            let seoLink = contentResult[i].seoLink;
                            let subCategory = contentResult[i].subCategory;

                            contentList += createContentListItem(contentType, contentID, contentUniqID, contentTitle, seoLink, targetMenu, subCategory);
                        }
                        $("#searchContentResult ul").html(contentList);
                    }
                }
            });
        });

        $(document).on("click", "#searchContentResult li", function () {
            var targetMenu = $("#targetMenu").val();
            var link = $(this).data("link");
            $("#bannerLink-" + targetMenu).val(link);
            $("#offcanvas-searchContentOff").click();
        });

        $(document).on('click', '.removeBannerBox', function(e) {
            let dataID = $(this).data("id");
            $("#card-panel-"+dataID).remove();
            $("#previewPanel #bannerContainer-" + dataID).remove();
        });

        $(document).on("click", "#bannerPreviewButton", function (){
            $(".bannerText").each(function(index, element){
                let i = index + 1;
                console.log("i: " + i);
                let summernote = $('#bannerText-' + i).summernote();
                const bannerText = summernote.code();
                console.log(bannerText);
                $("#bannerContent-" + i).html(bannerText);
            });

            let bannerStyle = $("#bannerStyle");
            if(bannerStyle.val() === "Carousel"){
                $("#previewPanel").addClass("carousel");
            }
        });

        //#bannerLocation değiştiğinde bannerDisplayLocation alanını dolduralım
        $(document).on("change", "#bannerLocation", function(){
            let bannerLocation = $(this).val();
            let bannerDisplayLocation = $("#bannerDisplayLocation");
            bannerDisplayLocation.html("");

            let languageID = $("#languageID").val();

            //bannerDisplayLocation multiple select2 olarak çalışıyor. select2 seçimleri varsa temizleyelim
            bannerDisplayLocation.select2("destroy");
            bannerDisplayLocation.select2();

            if(bannerLocation !== "0"){
                $("#bannerDisplayLocationContainer").removeClass("hidden");
            }
            else{
                $("#bannerDisplayLocationContainer").addClass("hidden");
            }

            if(bannerLocation === "1"){
                //kategorileri getir
                $.ajax({
                    type: 'POST',
                    url: "/App/Controller/Admin/AdminCategoryController.php",
                    data: {action: "getCategories",languageID: languageID},
                    dataType: 'json',
                    success: function (data) {
                        $data = data;
                        if ($data.status === "success") {
                            $categories = $data.categories;
                            $html = "";
                            for ($i = 0; $i < $categories.length; $i++) {
                                $categoryID = $categories[$i].categoryID;
                                $categoryName = $categories[$i].categoryName;
                                $html += '<option value="' + $categoryID + '">' + $categoryName + '</option>';
                            }
                            bannerDisplayLocation.html($html);
                        }
                        else{
                            console.log($data);
                        }
                    }
                });
            }
            else if(bannerLocation === "2"){
                //sayfaları getir
                $.ajax({
                    type: 'POST',
                    url: "/App/Controller/Admin/AdminPageController.php",
                    data: {action: "getAllPages",languageID: languageID},
                    dataType: 'json',
                    success: function (data) {
                        $data = data;
                        if ($data.status === "success") {
                            $pages = $data.pages;
                            $html = "";
                            for ($i = 0; $i < $pages.length; $i++) {
                                $pageID = $pages[$i].pageID;
                                $pageTitle = $pages[$i].pageTitle;
                                $html += '<option value="' + $pageID + '">' + $pageTitle + '</option>';
                            }
                            bannerDisplayLocation.html($html);
                        }
                        else{
                            console.log($data);
                        }
                    }
                });
            }
        });

        //form submit
        $(document).on("submit", "#addBannerForm", function(e){
            e.preventDefault();
            let isValid = true;

            //bannerGroupName boş olamaz
            let bannerGroupName = $("#bannerGroupName").val();
            if(bannerGroupName === ""){
                changeAlertModalHeaderColor("danger");
                $("#alertMessage").html("Banner Grubu adı boş olamaz");
                $("#alertModal").modal("show");
                return false;
            }

            //bannerTypeID 0 olamaz
            let bannerTypeID = $("#bannerTypeID").val();
            if(bannerTypeID === "0"){
                changeAlertModalHeaderColor("danger");
                $("#alertMessage").html("Banner Türü seçmelisiniz");
                $("#alertModal").modal("show");
                return false;
            }

            //bannerLayoutID 0 olamaz
            let bannerLayoutID = $("#bannerLayoutID").val();
            if(bannerLayoutID === "0"){
                changeAlertModalHeaderColor("danger");
                $("#alertMessage").html("Banner Düzeni seçmelisiniz");
                $("#alertModal").modal("show");
                return false;
            }

            let bannerStyleClass = $('#bannerStyle').val();

            const bannerPanels = document.querySelectorAll('.card.panel');
            let i=0;
            bannerPanels.forEach(panel => {
                i++;
                let summernote = $('#bannerText-' + i).summernote();
                const bannerText = summernote.code().trim();
                console.log("bannerText: " + bannerText);
                $("#bannerText-" + i).val(bannerText);
                const bannerSlogan = panel.querySelector('input[name="bannerSlogan[]"]')?.value.trim();
                console.log("bannerSlogan: " + bannerSlogan);
                const bannerImage = panel.querySelector('input[name="bannerImage[]"]')?.value.trim();
                console.log("bannerImage: " + bannerImage);

                // Kullanıcıya görsel bir hata mesajı göstermek için
                const header = panel.querySelector('.card-head header');
                // Eğer üçü birden boşsa, geçersiz form
                if (!bannerText && !bannerSlogan && !bannerImage) {
                    isValid = false;

                    if (header) {
                        header.style.color = 'red'; // Başlığı kırmızı yaparak vurgulayabilirsiniz
                    }
                }
                else{
                    if (header) {
                        header.style.color = '';
                    }
                }
            });

            if (!isValid) {
                changeAlertModalHeaderColor("danger");
                $("#alertMessage").html("Her bir bannerda en az bir alan (Başlık, Yazı veya Görsel) doldurulmalıdır!");
                $("#alertModal").modal("show");
                return false;
            }

            let formData = new FormData(this);

            // select2 ile seçilen değerleri al
            let selectedValues = $('#bannerDisplayLocation').val();
            console.log(selectedValues);
            formData.append('bannerDisplayLocation', JSON.stringify(selectedValues));

            let action = "addBanner";
            let bannerGroupID = $('#bannerGroupID').val();
            if(bannerGroupID > 0){
                action = "updateBanner";
                formData.append("bannerGroupID", bannerGroupID);
            }
            formData.append("bannerStyleClass", bannerStyleClass);
            formData.append("action", action);

            $.ajax({
                type: 'POST',
                url: "/App/Controller/Admin/AdminBannerModelController.php",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    $data = data;
                    if ($data.status === "success") {
                        $('#bannerGroupID').val($data.bannerGroupID);
                        changeAlertModalHeaderColor("success");
                        $("#alertMessage").html($data.message);
                        $("#alertModal").modal("show");
                        setTimeout(function(){
                            $("#alertModal").modal("hide");
                        }, 500);
                    }
                    else{
                        console.log($data);
                        changeAlertModalHeaderColor("danger");
                        $("#alertMessage").html($data.message);
                        $("#alertModal").modal("show");
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        // Olay delegasyonu kullanarak değişiklikleri yakalayın
        $(document).on("change", "[id^=bannerBgColorContainer-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerBgColor = $("#bannerBgColor-" + bannerCount).val();
            let bannerContainer = $("#previewPanel #bannerContainer-" + bannerCount);
            if(bannerBgColor===""){
                bannerBgColor = "transparent";
            }
            console.log("banner bgColor:" + bannerBgColor);
            bannerContainer.css("background-color", bannerBgColor);
        });

        $(document).on("change", "[id^=bannerContentBoxBgColorContainer-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerContentBoxBgColor = $("#bannerContentBoxBgColor-" + bannerCount).val();
            let bannerContentBox = $("#previewPanel #bannerContentBox-" + bannerCount);

            if(bannerContentBoxBgColor===""){
                bannerContentBoxBgColor = "transparent";
                bannerContentBox.css("box-shadow", "none");
            }
            else{
                bannerContentBox.css("box-shadow", "0 4px 8px rgba(0,0,0,0.1)");
            }
            bannerContentBox.css("background-color", bannerContentBoxBgColor);
        });

        $(document).on("change", "[id^=titleFontColor-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerTitleColor = $("#titleFontColor-" + bannerCount).val();
            let bannerSlogan = $("#previewPanel #bannerSlogan-" + bannerCount);
            bannerSlogan.css("color", bannerTitleColor);

        });

        $(document).on("change", "[id^=bannerContentFontColor-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerContentColor = $("#bannerContentFontColor-" + bannerCount).val();
            let bannerContent = $("#previewPanel #bannerContent-" + bannerCount);
            bannerContent.css("color", bannerContentColor);
        });

        $(document).on("change", "[id^=titleFontSize-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let titleFontSize = $("#titleFontSize-" + bannerCount).val();
            let bannerSlogan = $("#previewPanel #bannerSlogan-" + bannerCount);
            bannerSlogan.css("font-size", titleFontSize + "px");
        });

        $(document).on("change", "[id^=bannerContentFontSize-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let contentFontSize = $("#bannerContentFontSize-" + bannerCount).val();
            let bannerContent = $("#previewPanel #bannerContent-" + bannerCount);
            bannerContent.css("font-size", contentFontSize + "px");
        });

        $(document).on("change", "[id^=bannerButtonBgColor-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerButtonBgColor = $("#bannerButtonBgColor-" + bannerCount).val();
            let bannerButton = $("#previewPanel #bannerButton-" + bannerCount);
            bannerButton.css("background-color", bannerButtonBgColor);
        });

        $(document).on("change", "[id^=bannerButtonTextColor-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerButtonTextColor = $("#bannerButtonTextColor-" + bannerCount).val();
            let bannerButton = $("#previewPanel #bannerButton-" + bannerCount);
            bannerButton.css("color", bannerButtonTextColor);
        });

        $(document).on("mouseover", "[id^=bannerButtonContainer-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerButtonHoverBgColor = $("#bannerButtonHoverBgColor-" + bannerCount).val();
            let bannerButton = $("#previewPanel #bannerButton-" + bannerCount);
            bannerButton.css("background-color", bannerButtonHoverBgColor);

            let bannerButtonTextHoverColor = $("#bannerButtonTextHoverColor-" + bannerCount).val();
            bannerButton.css("color", bannerButtonTextHoverColor);
        });

        $(document).on("mouseout", "[id^=bannerButtonContainer-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerButtonBgColor = $("#bannerButtonBgColor-" + bannerCount).val();
            let bannerButton = $("#previewPanel #bannerButton-" + bannerCount);
            bannerButton.css("background-color", bannerButtonBgColor);

            let bannerButtonTextColor = $("#bannerButtonTextColor-" + bannerCount).val();
            bannerButton.css("color", bannerButtonTextColor);
        });

        $(document).on("change", "[id^=bannerButtonTextSize-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerButtonTextSize = $("#bannerButtonTextSize-" + bannerCount).val();
            let bannerButton = $("#previewPanel #bannerButton-" + bannerCount);
            bannerButton.css("font-size", bannerButtonTextSize + "px");
        });

        $(document).on("change", "[id^=bannerSlogan-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerSloganVal = $("#card-panel-" + bannerCount + " #bannerSlogan-" + bannerCount).val();
            let bannerSloganElement = $("#previewPanel #bannerSlogan-" + bannerCount);
            bannerSloganElement.html(bannerSloganVal);
        });

        $(document).on("change", "[id^=bannerHeightSize-]", function (){
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerHeightSize = $("#bannerHeightSize-" + bannerCount).val();
            let bannerContainer = $("#previewPanel #bannerContainer-" + bannerCount);
            bannerContainer.css("min-height", bannerHeightSize + "px");
        });

        $(document).on("change", "[id^=bannerButton-]", function () {
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerButtonVal = $("#bannerButton-" + bannerCount).val();
            let bannerButtonElement = $("#previewPanel #bannerButton-" + bannerCount);
            bannerButtonElement.html(bannerButtonVal);
        });

        $(document).on("change", "[id^=showButton-]", function (){
            let BannerCount = $(this).attr('id').split('-')[1];
            if(this.checked){
                $("#previewPanel #bannerButtonContainer-" + BannerCount).removeClass("hidden");
            }
            else{
                $("#previewPanel #bannerButtonContainer-" + BannerCount).addClass("hidden");
            }
        });

        $(document).on("change", "[id^=bannerButtonLocation-]", function (){
            let bannerCount = $(this).attr('id').split('-')[1];
            let bannerButtonLocation = $(this).val();
            let bannerButton = $("#previewPanel #bannerButtonContainer-" + bannerCount);
            let buttonContainer = $('#previewPanel #bannerButtonContainer-' + bannerCount);
            let contentBox = $('#previewPanel #bannerContentBox-' + bannerCount);
            let mainContainer = $('#previewPanel #bannerContainer-' + bannerCount);

            if(bannerButtonLocation == 0){
                contentBox.append(buttonContainer);
            }
            else{
                //buttonContainer 'ı contentBox içinden alıp main konteynır içine ekleyelim
                mainContainer.append(buttonContainer);
            }

            // Remove existing location classes
            bannerButton.removeClass(function (index, className) {
                return (className.match(/(^|\s)location-\S+/g) || []).join(' ');
            });

            // Add the new location class
            bannerButton.addClass("location-" + bannerButtonLocation);

            // Log the current location class for debugging purposes
            console.log("Banner " + bannerCount + " button location class updated to: location-" + bannerButtonLocation);
        });

        $(document).on('classChange', '#offcanvasBanner', function () {
            if ($(this).hasClass('active')) {
                $('.section-body.contain-lg').css('margin-left', '0');

                let windowWidth = $(window).width();
                //console.log("Pencere Genişliği: " + windowWidth); // Genişliği kontrol et

                // Genişlikten 100px çıkar (bu sizin özel mantığınız)
                let adjustedWidth = windowWidth;
                //console.log("Ayarlanmış Genişlik/Pozisyon: " + adjustedWidth);

                $(this).css({
                    "transform": "translate(-" + adjustedWidth + "px, 0px)",
                    "width": adjustedWidth + "px"
                });
            } else {
                $('.section-body.contain-lg').css('margin-left', 'auto');
            }
        });

        $(document).on('showPicker.colorpicker', ".bannerBgColorContainer, .bannerContentBoxBgColorContainer", function () {
            const buttonId = "clearColorButton";
            const container = $(this).find('.input-group-addon');

            // Eğer buton zaten eklenmişse tekrar ekleme
            if (!container.find(`#${buttonId}`).length) {
                const clearButton = $(`<button id="${buttonId}" type="button" class="btn btn-sm btn-danger" style="margin-left: -120px; position:absolute; z-index:1000">Temizle</button>`);
                container.append(clearButton);

                // Buton tıklama olayı
                clearButton.on('click', function (event) {
                    event.stopPropagation(); // Olayın yayılmasını engelle
                    event.preventDefault(); // Varsayılan davranışı engelle
                    const input = $(this).closest('.input-group').find('input.form-control');
                    if (input.length) {
                        input.val(''); // Input değerini sıfırla
                        input.colorpicker('setValue', ''); // Colorpicker API ile sıfırla
                        input.trigger('change'); // Değişikliği tetikle
                    } else {
                        console.error('Input bulunamadı!');
                    }
                });
            }
        });

        $(document).on('hidePicker.colorpicker', function () {
            setTimeout(() => {
                $("#clearColorButton").remove();
            }, 150); // Butonu biraz gecikmeyle sil
        });


        // MutationObserver ile class değişikliklerini takip edelim
        const observer = new MutationObserver(function (mutationsList) {
            mutationsList.forEach(function (mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    $('#offcanvasBanner').trigger('classChange');
                }
            });
        });

        observer.observe(document.getElementById('offcanvasBanner'), { attributes: true });

        $(".bannerBgColorContainer").colorpicker();
        $(".bannerContentBoxBgColorContainer").colorpicker();
        $(".bannerButtonColorContainer").colorpicker();
        $(".bannerButtonTextColorContainer").colorpicker();
        $(".bannerButtonHoverColorContainer").colorpicker();
        $(".bannerButtonTextHoverColorContainer").colorpicker();
        $(".bannerTitleColorContainer").colorpicker();
        $(".bannerContentColorContainer").colorpicker();
        $(".bscp").colorpicker();

        <?php if(!empty($banners)):?>
        $('.bannerText').summernote({
            tabsize: 2,
            height: 200,
            minHeight: 200,

            onChange: function(contents, $editable) {
                let textId = $(this).attr("id");
                console.log('#bannerText-' + textId);

                let bannerContent = $("#bannerContent-" + textId);
                if (bannerContent.length > 0) {
                    bannerContent.html(contents.trim());
                    console.log('Önizleme alanı güncellendi.');
                } else {
                    console.warn('Önizleme alanı (#bannerContent-' + textId + ') bulunamadı.');
                }
            }

        });

        $('#bannerContainer .bannerImage[id^="bannerImage-"]').each(function() {
            let bannerId = $(this).attr('id');
            console.log("bannerId: " + bannerId);
            if (bannerId) {
                let bannerCount = bannerId.split('-')[1];
                if (bannerCount) {
                    observeImageSrcChange(bannerCount);
                }
            }
        });
        <?php endif?>
    });
</script>
<script>

    //sayfa yüklendikten sonra bannerStyle radio kontrol edelim.
    $(document).ready(function(){

        //#bannerTypeID değiştiğinde bannerLayoutları getirelim
        $(document).on("change", "#bannerTypeID", function(){
            let bannerTypeID = $(this).val();

            if(bannerTypeID == 0){
                return false;
            }

            $("#bannerPreviewButton").addClass("hidden");
            $("#addBannerBox").addClass("hidden");
            $("#bannerContainer").html("");
            $("#bannerKindContainer").removeClass("hidden");
            $("#bannerLayoutIDContainer").removeClass("hidden");
            $('#bannerKind').select2('readonly', false);

            let bannerKind = $("#bannerKind");
            //bannerKind ilk seçeneği seçili yapalım
            bannerKind.val("text_and_image").trigger("change");

            let $bannerStyleContainer = $("#bannerStyleContainer");
            let styles = bannerAllLayouts[bannerTypeID] || []; // İlgili stilleri al veya boş dizi ata
            let $bannerStyleSelect = $("#bannerStyle");
            $bannerStyleSelect.html(''); // Önceki seçenekleri temizle

            let bannerLayoutMaxBannersContainer = $("#bannerLayoutMaxBannersContainer");
            let bannerLayoutColumnsContainer = $("#bannerLayoutColumnsContainer");
            bannerLayoutMaxBannersContainer.removeClass("hidden");
            bannerLayoutColumnsContainer.removeClass("hidden");


            if (styles.length > 0) {
                // Yeni stil seçeneklerini ekle
                styles.forEach(style => {
                    // Düzenleme modunda önceden seçili stili işaretle (initialStyle data attribute'u ile)
                    let selected = ($bannerStyleContainer.data('initial-style') === style.value) ? 'selected' : '';
                    $bannerStyleSelect.append(`<option value="${style.value}" ${selected}>${style.text}</option>`);
                });
                $bannerStyleContainer.removeClass("hidden"); // Stil seçicisini göster
                // Select2 kullanıyorsanız, seçenekler eklendikten sonra güncelleyin
                $bannerStyleSelect.select2();
                //ilk opt seçelim
                $bannerStyleSelect.val($bannerStyleContainer.data('initial-style')).trigger('change');
            }
            else {
                $bannerStyleContainer.addClass("hidden"); // İlgili stil yoksa gizle
            }

            // Tip değiştiğinde önizlemedeki özel stil sınıflarını sıfırla
            resetPreviewBannerStyles();
            // Stil seçimini de sıfırla
            $bannerStyleSelect.val("").trigger('change'); // .trigger('change') select2'yi de günceller

            $("#bannerLayoutID").html("");

            loadCSS(bannerTypeID)

            $.ajax({
                type: 'POST',
                url: "/App/Controller/Admin/AdminBannerModelController.php",
                data: {action: "getBannerLayouts", bannerTypeID: bannerTypeID},
                dataType: 'json',
                success: function (data) {
                    $data = data;
                    if ($data.status === "success") {
                        $bannerLayouts = $data.bannerLayouts;
                        $html = '';
                        $firstLayout = $bannerLayouts[0].id;
                        $firstMaxBanners = $bannerLayouts[0].max_banners;
                        $firstLayoutGroup = $bannerLayouts[0].layout_group;
                        for ($i = 0; $i < $bannerLayouts.length; $i++) {
                            $bannerLayoutID = $bannerLayouts[$i].id;
                            $bannerLayoutGroup = $bannerLayouts[$i].layout_group;
                            $bannerLayoutView = $bannerLayouts[$i].layout_view;
                            $bannerLayoutName = $bannerLayouts[$i].layout_name;
                            $bannerLayoutDescription = $bannerLayouts[$i].description;
                            $bannerColumns = $bannerLayouts[$i].columns;
                            $bannerMaxBanners = $bannerLayouts[$i].max_banners;
                            $html += '<option value="' + $bannerLayoutID + '" data-group="' + $bannerLayoutGroup + '" data-view="' + $bannerLayoutView + '" data-desc="' + $bannerLayoutDescription + '" data-columns="' + $bannerColumns + '" data-maxbanners="' + $bannerMaxBanners + '">' + $bannerLayoutName + '</option>';
                        }
                        let bannerLayout = $("#bannerLayoutID");
                        bannerLayout.html($html);
                        //ilk opt seçelim
                        bannerLayout.val($firstLayout).trigger('change');
                        if($firstMaxBanners == 1){
                            $("#bannerLayoutColumns").attr("readonly","true")
                        }
                        else{
                            $("#bannerLayoutColumns").removeAttr("readonly")
                        }

                        $("#sampleBannerImage").attr("src","IMG/" + $firstLayoutGroup + ".jpg")
                    }
                    else{
                        console.log($data);
                    }
                }
            });
        });

        $(document).on("change", "#bannerKind", function(){
            let bannerLayoutID = $("#bannerLayoutID");
            bannerLayoutID.removeClass("hidden");
        });

        $(document).on("change", "#bannerView", function(){
            let bannerView = $("#bannerView");
            let bannerViewValue = bannerView.val();
            let bannerTotal = $("#bannerContainer .card.panel").length;
            for(let i =1; i <= bannerTotal; i++ ){
                let bannerContainer = $("#previewPanel #bannerContainer-" + i);
                if(bannerContainer.length > 0){
                    bannerContainer.removeClass("single double triple quad quinary");
                    bannerContainer.addClass(bannerViewValue);
                }
            }
        });


        //#layout değiştiğinde description alanına yazalım
        $(document).on("change", "#bannerLayoutID", function(){

            let $selectedOption = $(this).find("option:selected");
            let desc = $selectedOption.data("desc");
            let columns = $selectedOption.data("columns");
            let maxBanners = $selectedOption.data("maxbanners");
            let dataGroup = $selectedOption.data("group");
            let dataView = $selectedOption.data("view");
            let bannerLayoutDescription = $("#bannerLayoutDescription");
            let bannerViewContainer = $("#bannerViewContainer");

            let bannerView = $("#bannerView");

            if(dataView === "multi"){
                bannerViewContainer.removeClass("hidden");
            }
            else{
                bannerView.val("single").trigger("change");
                bannerViewContainer.addClass("hidden");
            }

            $("#addBannerBox").removeClass("hidden");
            let bannerGroupID = $("#bannerGroupID");
            if(bannerGroupID < 1){
                $("#bannerLayoutColumns").val(columns);
            }

            if(maxBanners == 1){
                $("#bannerLayoutColumns").attr("readonly","true")
            }
            else{
                $("#bannerLayoutColumns").removeAttr("readonly")
            }


            let $bannerStyle = $("#bannerStyle");
            // Sadece #bannerStyle'ın mevcut değeri dataGroup'tan farklıysa güncelle ve tetikle
            if ($bannerStyle.val() !== dataGroup) {
                $bannerStyle.val(dataGroup).trigger('change'); // Select2 kullanıyorsanız 'change.select2' de olabilir
            }

            bannerLayoutDescription.removeClass("hidden");
            bannerLayoutDescription.html(desc);
            $("#bannerLayoutMaxBanners").val(maxBanners);
            $("#sampleBannerImage").attr("src","IMG/" + dataGroup + ".jpg");

        });

        // Banner Stili (bannerStyle) değiştiğinde önizlemeyi güncelle
        $(document).on("change", "#bannerStyle", function() {
            let selectedStyleClass = $(this).val(); // Bu, #bannerLayoutID'deki data-group ile eşleşecek değerdir

            console.log("selectedStyleClass: " + selectedStyleClass);

            // #bannerLayoutID'yi güncelle
            let $bannerLayoutID = $("#bannerLayoutID");
            // #bannerLayoutID içinde data-group değeri selectedStyleClass olan option'ı bul
            let $targetLayoutOption = $bannerLayoutID.find('option[data-group="' + selectedStyleClass + '"]');
            if ($targetLayoutOption.length > 0) {
                let targetLayoutIDValue = $targetLayoutOption.val(); // Bulunan option'ın asıl value'su (ID)
                // Sadece #bannerLayoutID'nin mevcut değeri targetLayoutIDValue'dan farklıysa güncelle ve tetikle
                if ($bannerLayoutID.val() !== targetLayoutIDValue) {
                    $bannerLayoutID.val(targetLayoutIDValue).trigger('change'); // Select2 kullanıyorsanız 'change.select2' de olabilir
                }
            }

            $("#className").text("." + selectedStyleClass);
            applyPreviewBannerStyle(selectedStyleClass);


        });

        // Önizleme bannerlarındaki tüm özel stil sınıflarını temizleyen fonksiyon
        function resetPreviewBannerStyles() {
            $("#sampleBannerImage").attr("src","IMG/bos.jpg");
            let $previewBanners = $("#previewPanel > div"); // Önizlemedeki ana banner div'leri
            if($('#previewPanel > div.preview-slider-container').length > 0){
                $previewBanners = $('.preview-slider-container > div');
            }
            let allClassesToRemove = [];

            // bannerStylesByType içindeki tüm olası stil sınıflarını topla
            Object.values(bannerAllLayouts).forEach(styleArray => {
                styleArray.forEach(style => {
                    if (style.value) { // Boş olmayan değerleri ekle
                        allClassesToRemove.push(style.value);
                    }
                });
            });

            // Toplanan tüm sınıfları kaldır
            if (allClassesToRemove.length > 0) {
                $previewBanners.removeClass(allClassesToRemove.join(' '));
                console.log("Önizleme stilleri sıfırlandı.");
            }
            // Gerekirse, layout'tan gelen temel sınıfları burada tekrar ekleyebilirsiniz
            // Örneğin: let layoutViewClass = $("#bannerLayoutID option:selected").data('view');
            // if(layoutViewClass) { $previewBanners.addClass(layoutViewClass); }
        }

        // Seçilen stil sınıfını önizleme bannerlarına uygulayan fonksiyon
        function applyPreviewBannerStyle(styleClass) {
            resetPreviewBannerStyles(); // Önce mevcut tüm özel stilleri kaldır
            let $previewBanners = $("#previewPanel > div");

            if($('#previewPanel > div.preview-slider-container').length > 0){
                $previewBanners = $('.preview-slider-container > div');
            }

            if (styleClass) {
                $("#className").text("." + styleClass);
                $previewBanners.addClass(styleClass);
                $previewBanners.addClass(styleClass);
                $("#sampleBannerImage").attr("src","IMG/" + styleClass + ".jpg");
                console.log(`Önizlemeye stil uygulandı: ${styleClass}`);
            }
            else {
                console.log("Varsayılan stil uygulandı veya stil seçilmedi.");
                // Gerekirse varsayılan layoutView sınıfını burada ekleyebilirsiniz
            }
        }

        // --- Sayfa Yüklendiğinde Başlangıç Ayarları ---

        // Eğer düzenleme modundaysanız (bannerGroupID > 0), PHP tarafında
        // banner grubuna ait kaydedilmiş stil sınıfını bir data attribute'una ekleyin.
        // Örnek PHP (HTML kısmında bannerStyleContainer'a ekleyin):
        // data-initial-style="<?= htmlspecialchars($bannerGroupStyleClass ?? '') ?>"

        // Sayfa yüklendiğinde mevcut banner tipi için stilleri doldur ve seçili stili uygula
        let initialTypeID = $("#bannerTypeID").val();
        let initialStyle = $("#bannerStyleContainer").data('initial-style'); // PHP'den gelen data attribute'unu oku

        if (initialTypeID && initialTypeID !== '0') {
            // Başlangıç tipi için stil seçeneklerini doldur (change event'ini tetiklemeden)
            let initialStyles = bannerAllLayouts[initialTypeID] || [];
            if (initialStyles.length > 0) {
                $("#bannerStyle").html('<option value="">Stil Seçin</option>'); // Temizle
                initialStyles.forEach(style => {
                    let selected = (style.value === initialStyle) ? 'selected' : '';
                    $("#bannerStyle").append(`<option value="${style.value}" ${selected}>${style.text}</option>`);
                });
                $("#bannerStyleContainer").removeClass("hidden");
                $("#bannerStyle").val(initialStyle); // Kayıtlı stili seçili yap
                // Select2'yi başlat/güncelle
                $("#bannerStyle").select2();
            }
        }

        // Başlangıçta seçili olan stili önizlemeye uygula
        if (initialStyle) {
            applyPreviewBannerStyle(initialStyle);
        }

        // #addBannerBox tıklandığında bannerBox ekle
        $(document).on("click", "#addBannerBox", function(){
            $("#bannerPreviewButton").removeClass("hidden");

            let bannerTypeID = $("#bannerTypeID").val();
            console.log("bannerTypeID: " + bannerTypeID);
            if(bannerTypeID == 0){
                changeAlertModalHeaderColor("danger");
                $("#alertMessage").html("Banner Tpi Seçin");
                $("#alertModal").modal("show");
                return false;
            }

            let bannerKind = $("#bannerKind");
            let bannerKindSelectedOption = bannerKind.find("option:selected");
            let bannerKindValue = bannerKindSelectedOption.val();
            console.log("bannerKindValue: " + bannerKindValue);

            if(bannerKindValue == 0){
                changeAlertModalHeaderColor("danger");
                $("#alertMessage").html("Banner Tipi Seçin");
                $("#alertModal").modal("show");
                return false;
            }


            let bannerLayout = $("#bannerLayoutID");
            let layoutSelectedOption = bannerLayout.find("option:selected");

            let bannerLayoutSelectedID = bannerLayout.val();
            console.log("bannerLayoutSelectedID: " + bannerLayoutSelectedID);

            //carousel,ImageRightBanner vs
            let layoutGroup = layoutSelectedOption.data("group");
            console.log("layoutGroup: " + layoutGroup);

            //single ya da muşti döner
            let layoutView = $("#bannerView").val();
            console.log("layoutView: " + layoutView);

            $("#languageContainer").removeClass("hidden");
            $("#saveButtonContainer").removeClass("hidden");

            let bannerCount = $("#bannerContainer .card.panel").length;
            console.log("bannerCount: " + bannerCount);

            let bannerLayoutMaxBanners = $("#bannerLayoutMaxBanners").val();
            console.log("bannerLayoutMaxBanners: " + bannerLayoutMaxBanners);

            if(bannerCount >= bannerLayoutMaxBanners){
                changeAlertModalHeaderColor("warning");
                $("#alertMessage").html("Bu banner düzeni için maksimum banner sayısına ulaştınız");
                $("#alertModal").modal("show");
                return false;
            }

            let bannerLayoutColumns = parseInt($("#bannerLayoutColumns").val());
            console.log("bannerLayoutColumns: " + bannerLayoutColumns);

            if(bannerCount > 0 ){
                bannerLayoutColumns = bannerLayoutColumns - bannerCount;
            }


            // --- Slider Özel Mantığı Başlangıcı ---
            let isSlider = (bannerTypeID === '1' || layoutGroup === "Carousel");
            let $previewPanel = $("#previewPanel");
            let $sliderContainer; // Genel slider konteyneri için

            if (isSlider) {
                // Slider ise, genel slider konteynerini bul veya oluştur
                $sliderContainer = $previewPanel.find('.preview-slider-container');
                if ($sliderContainer.length === 0) {
                    // Temel flex ve overflow stillerini buraya ekleyebilir veya CSS'de tanımlayabiliriz
                    $previewPanel.html('<div class="preview-slider-container" style="display: flex; overflow-x: auto; gap: 15px; padding: 10px; border: 1px dashed #ccc; background-color: #f8f9fa; min-height: 250px; align-items: stretch;"></div>');
                    $sliderContainer = $previewPanel.find('.preview-slider-container');
                }
                // Slider için ana panele layoutView sınıfı ekleme (artık gereksiz)
                $previewPanel.removeClass('single double triple quad quinary');
            }
            else {
                // Slider değilse, önizleme panelini temizle (sadece ilk banner eklenirken)
                let bannerCountCheck = $("#bannerContainer .card.panel").length; // Mevcut banner sayısını kontrol et
                if (bannerCountCheck === 0) {
                    $previewPanel.html('');
                    //$previewPanel.addClass(layoutView);
                }
            }
            // --- Slider Özel Mantığı Sonu ---


            //bannerLayoutColumns sayısı kadar banner ekleyelim
            for (let i = 0; i < bannerLayoutColumns; i++) {
                bannerCount++;
                console.log("bannerCount: " + bannerCount);

                if (bannerCount > bannerLayoutMaxBanners) {
                    changeAlertModalHeaderColor("warning");
                    $("#alertMessage").html("Bu banner düzeni için maksimum banner sayısına ulaştınız");
                    $("#alertModal").modal("show");
                    // Eklenen son banner'ı geri alabiliriz (opsiyonel)
                    // $("#card-panel-" + bannerCount).remove();
                    return false;
                }

                // 1. Form Alanını Ekle (Bu kısım değişmedi)
                let newBannerForm = bannerBox.replace(/\[n\]/g, bannerCount);
                $("#bannerContainer").append(newBannerForm);

                // 2. Önizleme Alanını Ekle/Güncelle
                let newBannerPreviewHTML = "";
                let $newBannerID = "#card-panel-" + bannerCount; // Formdaki ID
                let previewBaseClass = ""; // Slider olmayanlar için
                let isOnlyImage = false;

                if (bannerKindValue === "only_text") {
                    newBannerPreviewHTML = bannerBox_onlyText;
                    //previewBaseClass = "middle-content-banner"; // Varsayılan veya tipe göre değişebilir
                }
                else if (bannerKindValue === "text_and_image") {
                    newBannerPreviewHTML = bannerBox_TextAndImage;
                    //previewBaseClass = "ImageRightBanner"; // Varsayılan veya tipe göre değişebilir
                }
                else { // image
                    newBannerPreviewHTML = bannerBox_onlyImage;
                    //previewBaseClass = "onlyImage"; // Sadece görsel olduğunu belirtmek için
                    isOnlyImage = true;
                    // Formdaki metin alanlarını gizle (Bu kısım değişmedi)
                    $($newBannerID + " .bannerText").val("").parent().addClass("hidden");
                    $($newBannerID + " .bannerSlogan").val("").parent().addClass("hidden");
                }

                // Banner tipine göre previewBaseClass'ı override et (Slider hariç)
                /*if (!isSlider) {
                    if(bannerTypeID == 2){ // Tepe Banner
                        previewBaseClass = "top-banner";
                        // Tepe banner text_and_image ise bile sadece text gibi davranıyor
                        if (layoutGroup === "text_and_image") {
                            newBannerPreviewHTML = bannerBox_onlyText;
                            isOnlyImage = false;
                        }
                    }
                    else {
                        console.log("Bilinmeyen bannerTypeID için previewBaseClass ayarlanmadı.");
                    }
                }*/

                // [n] değerlerini değiştir
                newBannerPreviewHTML = newBannerPreviewHTML.replace(/\[n\]/g, bannerCount);
                newBannerPreviewHTML = newBannerPreviewHTML.replace(/\[class\]/g, layoutView + " " + bannerKindValue);

                // --- Slider/Normal Banner Ayrımı ---
                if (isSlider) {
                    // Slider ise:
                    // HTML string'ini jQuery nesnesine çevir, 'slide' sınıfı ekle ve wrapper'a ekle
                    let $slide = $(newBannerPreviewHTML); // HTML'i parse et
                    //$slide.addClass('slide').addClass(layoutView); // 'slide' sınıfını ekle
                    if (isOnlyImage) {
                        //$slide.addClass('onlyImage'); // Sadece görselse ek sınıf
                    }
                    // Slider içindeki slaytlar için layoutView sınıfına gerek yok
                    //$slide.removeClass('[class] single double triple quad quinary');
                    $sliderContainer.append($slide); // Slider wrapper'ına ekle
                }
                else {
                    // Slider değilse (Mevcut Mantık):
                    // Stil seçicisinden gelen sınıfı al (örn: ImageRightBanner)
                    //let selectedStyleClass = $("#bannerStyle").val() || previewBaseClass; // Stil seçilmemişse layoutGroup'a göre belirlenen kullanılır

                    // Grid sınıfını (single, double vb.) ekle
                    //let finalClassName = selectedStyleClass + " " + layoutView; // layoutView burada single, double etc.

                    // [class] yerine finalClassName'i ekle
                    //newBannerPreviewHTML = newBannerPreviewHTML.replace(/\[class\]/g, finalClassName.trim());

                    $previewPanel.append(newBannerPreviewHTML); // Doğrudan ana panele ekle

                    // Özel durumlar (örn: Tepe banner arkaplanı)
                    if(bannerTypeID == 2 && bannerLayoutSelectedID == 1){
                        //$("#bannerContainer-" + bannerCount).css("background-image", "url('')");
                    }
                }
                // --- Ayrım Sonu ---


                // Colorpicker ve Summernote başlatmaları (Bu kısım değişmedi)
                $("#bannerBgColorContainer-" + bannerCount).colorpicker();
                $("#bannerContentBoxBgColorContainer-" + bannerCount).colorpicker();
                $("#bannerButtonColorContainer-" + bannerCount).colorpicker();
                $("#bannerButtonTextColorContainer-" + bannerCount).colorpicker();
                $("#bannerButtonHoverColorContainer-" + bannerCount).colorpicker();
                $("#bannerButtonTextHoverColorContainer-" + bannerCount).colorpicker();
                $("#bannerTitleColorContainer-" + bannerCount).colorpicker();
                $("#bannerContentColorContainer-" + bannerCount).colorpicker();

                // Summernote'u ID bazlı başlatma (Döngü içinde ID'yi kullanarak)
                let summernoteSourceId = 'bannerText-' + bannerCount;
                $('#' + summernoteSourceId).summernote({
                    tabsize: 2,
                    height: 200,
                    minHeight: 200
                });


                // MutationObserver'ı başlatma (Bu kısım değişmedi)
                // ID'yi kullanarak doğru elemanı seçtiğinizden emin olun
                let imageObserverTargetId = 'bannerImage-' + bannerCount;
                // Form içindeki gizli img elemanını hedef alıyoruz
                let $formImageElement = $('#card-panel-' + bannerCount).find('#' + imageObserverTargetId);
                if ($formImageElement.length) {
                    observeImageSrcChange(bannerCount); // Fonksiyonu çağır
                }
                else {
                    console.warn("MutationObserver için kaynak resim bulunamadı: #card-panel-" + bannerCount + " #" + imageObserverTargetId);
                }

            } // for döngüsü sonu

            let selectedStyleClass = $("#bannerStyle").val();
            if (selectedStyleClass) {
                applyPreviewBannerStyle(selectedStyleClass); // Yeni eklenenlere de uygula
            }
            else{
                // Eğer seçili stil yoksa veya boşsa, ilk geçerli seçeneği bul
                let $firstOption = $("#bannerStyle option[value!='']:first"); // Değeri boş olmayan ilk option'ı seç

                if ($firstOption.length > 0) {
                    selectedStyleClass = $firstOption.val(); // İlk seçeneğin değerini al
                    $("#bannerStyle").val(selectedStyleClass).trigger('change.select2'); // Select kutusunu güncelle ve Select2'yi tetikle
                    console.log("Seçili stil yoktu, ilk stil seçildi: " + selectedStyleClass);
                    applyPreviewBannerStyle(selectedStyleClass); // İlk stili uygula
                }
                else {
                    console.log("Uygulanacak geçerli bir stil seçeneği bulunamadı.");
                    // İsteğe bağlı: Stil seçeneği hiç yoksa varsayılan bir sınıfı uygula veya hata ver
                    // resetPreviewBannerStyles(); // Veya tüm stilleri temizle
                }
            }

        });

        <?php
        if($bannerGroupID>0){
            echo '$("#bannerPreviewButton").removeClass("hidden");';
            echo "$('#bannerTypeID').select2('readonly', true);";
            echo "$('#bannerKind').select2('readonly', true);";
        }
        ?>
    });
</script>

</body>
</html>