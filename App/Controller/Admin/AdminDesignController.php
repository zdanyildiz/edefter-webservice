<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */


$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}


$json = $config->Json;
$helper = $config->Helper;

if ($action == "saveDesign" || $action == "savePreviewDesign"){

    $languageID = $requestData["languageID"] ?? null;
    if (!isset($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil seçin'
        ]);
        exit();
    }
    $languageID = intval($languageID);

    $primaryColor = $requestData["primary-color"] ?? null;
    $primaryLightColor = $requestData["primary-light-color"] ?? null;
    $primaryDarkColor = $requestData["primary-dark-color"] ?? null;
    $secondaryColor = $requestData["secondary-color"] ?? null;
    $secondaryLightColor = $requestData["secondary-light-color"] ?? null;
    $secondaryDarkColor = $requestData["secondary-dark-color"] ?? null;
    $accentColor = $requestData["accent-color"] ?? null;
    $successColor = $requestData["success-color"] ?? null;
    $infoColor = $requestData["info-color"] ?? null;
    $warningColor = $requestData["warning-color"] ?? null;
    $dangerColor = $requestData["danger-color"] ?? null;

    // Yeni tema değişkenleri
    $textPrimaryColor = $requestData["text-primary-color"] ?? null;
    $textSecondaryColor = $requestData["text-secondary-color"] ?? null;
    $textMutedColor = $requestData["text-muted-color"] ?? null;
    $textLightColor = $requestData["text-light-color"] ?? null;
    $textDarkColor = $requestData["text-dark-color"] ?? null;
    $linkColor = $requestData["link-color"] ?? null;
    $linkHoverColor = $requestData["link-hover-color"] ?? null;

    $backgroundPrimaryColor = $requestData["background-primary-color"] ?? null;
    $backgroundSecondaryColor = $requestData["background-secondary-color"] ?? null;
    $backgroundLightColor = $requestData["background-light-color"] ?? null;
    $backgroundDarkColor = $requestData["background-dark-color"] ?? null;

    $borderColor = $requestData["border-color"] ?? null;
    $borderLightColor = $requestData["border-light-color"] ?? null;
    $borderDarkColor = $requestData["border-dark-color"] ?? null;
    $borderRadiusBase = $requestData["border-radius-base"] ?? null;
    $borderRadiusSm = $requestData["border-radius-sm"] ?? null;
    $borderRadiusLg = $requestData["border-radius-lg"] ?? null;

    $bodyBgColor = $requestData["body-bg-color"] ?? null;
    $bodyTextColor = $requestData["body-text-color"] ?? null;

    $contentMaxWidth = $requestData["content-max-width"] ?? null;
    $contentBgColor = $requestData["content-bg-color"] ?? null;

    $aColor = $requestData["a-color"] ?? null;
    $aHoverColor = $requestData["a-hover-color"] ?? null;

    $border = $requestData["border"] ?? null;

    // Gelen veriyi al
    $topContactAndSocialContainerMarginTop = $requestData["top-contact-and-social-container-margin-top"];

// "px" veya "PX" ifadesini kaldır
    $topContactAndSocialContainerMarginTop = str_ireplace("px", "", $topContactAndSocialContainerMarginTop);

// Değer sayı değilse veya negatifse 0 yap
    $topContactAndSocialContainerMarginTop = intval($topContactAndSocialContainerMarginTop);
    if ($topContactAndSocialContainerMarginTop < 0) {
        $topContactAndSocialContainerMarginTop = 0;
    }

// 0'dan büyükse "px" ekle
    $topContactAndSocialContainerMarginTop = $topContactAndSocialContainerMarginTop > 0 ? $topContactAndSocialContainerMarginTop . "px" : "0";


    $topContactAndSocialContainerMobileMarginTop = $requestData["top-contact-and-social-container-mobile-margin-top"] ?? 0;
    $topContactAndSocialContainerMobileMarginTop = str_ireplace("px", "", $topContactAndSocialContainerMobileMarginTop);
    $topContactAndSocialContainerMobileMarginTop = intval($topContactAndSocialContainerMobileMarginTop);
    if ($topContactAndSocialContainerMobileMarginTop < 0) {
        $topContactAndSocialContainerMobileMarginTop = 0;
    }
    $topContactAndSocialContainerMobileMarginTop = $topContactAndSocialContainerMobileMarginTop > 0 ? $topContactAndSocialContainerMobileMarginTop . "px" : "0";

    $topContactAndSocialBgColor = $requestData["top-contact-and-social-bg-color"] ?? null;
    $topContactAndSocialLinkColor = $requestData["top-contact-and-social-link-color"] ?? null;
    $topContactAndSocialLinkHoverColor = $requestData["top-contact-and-social-link-hover-color"] ?? null;
    $topContactAndSocialIconColor = $requestData["top-contact-and-social-icon-color"] ?? null;
    $topContactAndSocialIconHoverColor = $requestData["top-contact-and-social-icon-hover-color"] ?? null;

    $topContactAndSocialBgColorMobile = $requestData["top-contact-and-social-bg-color-mobile"] ?? null;
    $topContactAndSocialLinkColorMobile = $requestData["top-contact-and-social-link-color-mobile"] ?? null;
    $topContactAndSocialLinkHoverColorMobile = $requestData["top-contact-and-social-link-hover-color-mobile"] ?? null;
    $topContactAndSocialIconColorMobile = $requestData["top-contact-and-social-icon-color-mobile"] ?? null;
    $topContactAndSocialIconHoverColorMobile = $requestData["top-contact-and-social-icon-hover-color-mobile"] ?? null;

    $headerBgColor = $requestData["header-bg-color"] ?? null;
    $headerLogoWidth = $requestData["header-logo-width"] ?? null;
    $headerLogoMobileWidth = $requestData["header-logo-mobile-width"] ?? null;

    $headerMinHeight = $requestData["header-min-height"] ?? null;
    $headerMobileMinHeight = $requestData["header-mobile-min-height"] ?? null;

    $headerLogoMarginTop = $requestData["header-logo-margin-top"] ?? 0;
    $headerLogoMarginBottom = $requestData["header-logo-margin-bottom"] ?? 0;
    $headerLogoMarginLeft = $requestData["header-logo-margin-left"] ?? 0;
    $headerLogoMarginRight = $requestData["header-logo-margin-right"] ?? 0;

    $headerLogoMargin = $headerLogoMarginTop." ".$headerLogoMarginRight." ".$headerLogoMarginBottom." ".$headerLogoMarginLeft;

    $headerMobileLogoMarginTop = $requestData["header-mobile-logo-margin-top"] ?? 0;
    $headerMobileLogoMarginBottom = $requestData["header-mobile-logo-margin-bottom"] ?? 0;
    $headerMobileLogoMarginLeft = $requestData["header-mobile-logo-margin-left"] ?? 0;
    $headerMobileLogoMarginRight = $requestData["header-mobile-logo-margin-right"] ?? 0;

    $headerMobileLogoMargin = $headerMobileLogoMarginTop." ".$headerMobileLogoMarginRight." ".$headerMobileLogoMarginBottom." ".$headerMobileLogoMarginLeft;

    $shopMenuContainerIconColorSearch = $requestData["shop-menu-container-icon-color-search"] ?? null;
    $shopMenuContainerIconColorMember = $requestData["shop-menu-container-icon-color-member"] ?? null;
    $shopMenuContainerIconColorFavorites = $requestData["shop-menu-container-icon-color-favorites"] ?? null;
    $shopMenuContainerIconColorBasket = $requestData["shop-menu-container-icon-color-basket"] ?? null;
    $shopMenuContainerIconHoverColor = $requestData["shop-menu-container-icon-hover-color"] ?? null;

    $mainMenuBgColor = $requestData["main-menu-bg-color"] ?? null;
    $mainMenuLinkBgColor = $requestData["main-menu-link-bg-color"] ?? null;
    $mainMenuLinkHoverBgColor = $requestData["main-menu-link-hover-bg-color"] ?? null;
    $mainMenuLinkColor = $requestData["main-menu-link-color"] ?? null;
    $mainMenuLinkHoverColor = $requestData["main-menu-link-hover-color"] ?? null;
    $mainMenuUlBgColor = $requestData["main-menu-ul-bg-color"] ?? null;
    $mainMenuUlSubmenuLinkColor = $requestData["main-menu-ul-submenu-link-color"] ?? null;
    $mainMenuUlSubmenuLinkHoverColor = $requestData["main-menu-ul-submenu-link-hover-color"] ?? null;
    $mainMenuUlSubmenuLinkBgColor = $requestData["main-menu-ul-submenu-link-bg-color"] ?? null;
    $mainMenuUlSubmenuLinkHoverBgColor = $requestData["main-menu-ul-submenu-link-hover-bg-color"] ?? null;
    $mainMenuLinkFontSize = $requestData["font-size-main-menu"] ?? null;
    $mainMenuUlSubmenuLinkFontSize = $requestData["font-size-main-submenu"] ?? null;

    // New comprehensive menu variables
    $menuBackgroundColor = $requestData["menu-background-color"] ?? null;
    $menuTextColor = $requestData["menu-text-color"] ?? null;
    $menuHoverColor = $requestData["menu-hover-color"] ?? null;
    $menuHoverBgColor = $requestData["menu-hover-bg-color"] ?? null;
    $menuActiveColor = $requestData["menu-active-color"] ?? null;
    $menuActiveBgColor = $requestData["menu-active-bg-color"] ?? null;
    $menuFontSize = $requestData["menu-font-size"] ?? null;
    $menuHeight = $requestData["menu-height"] ?? null;
    $menuPadding = $requestData["menu-padding"] ?? null;
    
    // Submenu variables
    $submenuBgColor = $requestData["submenu-bg-color"] ?? null;
    $submenuTextColor = $requestData["submenu-text-color"] ?? null;
    $submenuHoverColor = $requestData["submenu-hover-color"] ?? null;
    $submenuHoverBgColor = $requestData["submenu-hover-bg-color"] ?? null;
    $submenuFontSize = $requestData["submenu-font-size"] ?? null;
    $submenuBorderColor = $requestData["submenu-border-color"] ?? null;
    $submenuWidth = $requestData["submenu-width"] ?? null;
    
    // Mobile menu variables
    $mobileMenuBgColor = $requestData["mobile-menu-background-color"] ?? null;
    $mobileMenuTextColor = $requestData["mobile-menu-text-color"] ?? null;
    $mobileMenuHoverColor = $requestData["mobile-menu-hover-color"] ?? null;
    $mobileMenuHoverBgColor = $requestData["mobile-menu-hover-bg-color"] ?? null;
    $mobileMenuActiveColor = $requestData["mobile-menu-active-color"] ?? null;
    $mobileMenuActiveBgColor = $requestData["mobile-menu-active-bg-color"] ?? null;
    $mobileMenuFontSize = $requestData["mobile-menu-font-size"] ?? null;
    $mobileMenuPadding = $requestData["mobile-menu-padding"] ?? null;
    $mobileMenuDividerColor = $requestData["mobile-menu-divider-color"] ?? null;
    $hamburgerIconColor = $requestData["hamburger-icon-color"] ?? null;
    
    // Mobile submenu variables
    $mobileSubmenuBgColor = $requestData["mobile-submenu-bg-color"] ?? null;
    $mobileSubmenuTextColor = $requestData["mobile-submenu-text-color"] ?? null;
    $mobileSubmenuHoverColor = $requestData["mobile-submenu-hover-color"] ?? null;
    $mobileSubmenuHoverBgColor = $requestData["mobile-submenu-hover-bg-color"] ?? null;

    $homepageH1Color = $requestData["homepage-h1-color"] ?? null;
    $homepageH1FontSize = $requestData["homepage-h1-font-size"] ?? null;
    $homepageProductBoxBgColor = $requestData["homepage-product-box-bg-color"] ?? null;
    $homepageProductBoxHoverBgColor = $requestData["homepage-product-box-hover-bg-color"] ?? null;
    $homepageProductBoxColor = $requestData["homepage-product-box-color"] ?? null;
    $homepageProductBoxLinkColor = $requestData["homepage-product-box-link-color"] ?? null;
    $homepageProductBoxWidth = $requestData["homepage-product-box-width"] ?? null;
    $homepageProductBoxPriceColor = $requestData["homepage-product-box-price-color"] ?? null;

    $categoryProductBoxBgColor = $requestData["category-product-box-bg-color"] ?? null;
    $categoryProductBoxHoverBgColor = $requestData["category-product-box-hover-bg-color"] ?? null;
    $categoryProductBoxColor = $requestData["category-product-box-color"] ?? null;
    $categoryProductBoxLinkColor = $requestData["category-product-box-link-color"] ?? null;
    $categoryProductBoxWidth = $requestData["category-product-box-width"] ?? null;
    $categoryProductBoxPriceColor = $requestData["category-product-box-price-color"] ?? null;

    $topBannerBgColor = $requestData["top-banner-bg-color"] ?? null;
    $topBannerH1Color = $requestData["top-banner-h1-color"] ?? null;
    $topBannerPColor = $requestData["top-banner-p-color"] ?? null;
    $topBannerH1FontSize = $requestData["top-banner-h1-font-size"] ?? null;
    $topBannerPFontSize = $requestData["top-banner-p-font-size"] ?? null;
    $middleContentBannerWidth = $requestData["middle-content-banner-width"] ?? null;
    $bottomBannerWidth = $requestData["bottom-banner-width"] ?? null;

    $buttonColor = $requestData["button-color"] ?? null;
    $buttonHoverColor = $requestData["button-hover-color"] ?? null;
    $buttonDisabledColor = $requestData["button-disabled-color"] ?? null;
    $buttonTextColor = $requestData["button-text-color"] ?? null;

    $inputColor = $requestData["input-color"] ?? null;
    $inputBgColor = $requestData["input-bg-color"] ?? null;
    $inputFocusColor = $requestData["input-focus-color"] ?? null;
    $inputBorder = $requestData["input-border"] ?? null;
    $selectTextColor = $requestData["select-text-color"] ?? null;
    $selectBgColor = $requestData["select-bg-color"] ?? null;
    $selectFocusColor = $requestData["select-focus-color"] ?? null;
    $formLabelColor = $requestData["form-label-color"] ?? null;
    $formPlaceholderColor = $requestData["form-placeholder-color"] ?? null;
    $formErrorColor = $requestData["form-error-color"] ?? null;
    $formSuccessColor = $requestData["form-success-color"] ?? null;

    $overlayBgColor = $requestData["overlay-bg-color"] ?? null;
    $modalBgColor = $requestData["modal-bg-color"] ?? null;
    $modalTextColor = $requestData["modal-text-color"] ?? null;

    $tooltipBgColor = $requestData["tooltip-bg-color"] ?? null;
    $tooltipTextColor = $requestData["tooltip-text-color"] ?? null;

    $paginationBgColor = $requestData["pagination-bg-color"] ?? null;
    $paginationActiveBgColor = $requestData["pagination-active-bg-color"] ?? null;
    $paginationTextColor = $requestData["pagination-text-color"] ?? null;
    $paginationActiveTextColor = $requestData["pagination-active-text-color"] ?? null;

    $alertSuccessBg = $requestData["alert-success-bg"] ?? null;
    $alertSuccessText = $requestData["alert-success-text"] ?? null;
    $alertWarningBg = $requestData["alert-warning-bg"] ?? null;
    $alertWarningText = $requestData["alert-warning-text"] ?? null;
    $alertDangerBg = $requestData["alert-danger-bg"] ?? null;
    $alertDangerText = $requestData["alert-danger-text"] ?? null;

    $footerBgColor = $requestData["footer-bg-color"] ?? null;
    $footerTextColor = $requestData["footer-text-color"] ?? null;
    $footerLinkColor = $requestData["footer-link-color"] ?? null;
    $footerMenuBgColor = $requestData["footer-menu-bg-color"] ?? null;
    $footerMenuLinkColor = $requestData["footer-menu-link-color"] ?? null;
    $footerMenuLinkHoverColor = $requestData["footer-menu-link-hover-color"] ?? null;

    $boxShadow = $requestData["box-shadow"] ?? null;
    $textShadow = $requestData["text-shadow"] ?? null;
    $transitionSpeed = $requestData["transition-speed"] ?? null;
    $transitionTiming = $requestData["transition-timing"] ?? null;

    $breakpointSm = $requestData["breakpoint-sm"] ?? null;
    $breakpointMd = $requestData["breakpoint-md"] ?? null;
    $breakpointLg = $requestData["breakpoint-lg"] ?? null;
    $breakpointXl = $requestData["breakpoint-xl"] ?? null;

    $fontSizeSmall = $requestData["font-size-small"] ?? null;
    $fontSizeNormal = $requestData["font-size-normal"] ?? null;
    $fontSizeLarge = $requestData["font-size-large"] ?? null;
    $fontSizeXLarge = $requestData["font-size-xlarge"] ?? null;

    $spacing_xs =  "4";
    $spacing_sm = "8";
    $spacing_md = "16";
    $spacing_lg = "24";
    $spacing_xl = "32";
    $spacing_xxl = "48";

    $themeConfig = [
        'primary-color' => $primaryColor,
        'primary-light-color' => $primaryLightColor,
        'primary-dark-color' => $primaryDarkColor,
        'secondary-color' => $secondaryColor,
        'secondary-light-color' => $secondaryLightColor,
        'secondary-dark-color' => $secondaryDarkColor,
        'accent-color' => $accentColor,
        'success-color' => $successColor,
        'info-color' => $infoColor,
        'warning-color' => $warningColor,
        'danger-color' => $dangerColor,
        'text-primary-color' => $textPrimaryColor,
        'text-secondary-color' => $textSecondaryColor,
        'text-muted-color' => $textMutedColor,
        'text-light-color' => $textLightColor,
        'text-dark-color' => $textDarkColor,
        'link-color' => $linkColor,
        'link-hover-color' => $linkHoverColor,
        'background-primary-color' => $backgroundPrimaryColor,
        'background-secondary-color' => $backgroundSecondaryColor,
        'background-light-color' => $backgroundLightColor,
        'background-dark-color' => $backgroundDarkColor,
        'border-color' => $borderColor,
        'border-light-color' => $borderLightColor,
        'border-dark-color' => $borderDarkColor,
        'border-radius-base' => $borderRadiusBase,
        'border-radius-sm' => $borderRadiusSm,
        'border-radius-lg' => $borderRadiusLg,
        'body-bg-color' => $bodyBgColor,
        'body-text-color' => $bodyTextColor,
        'content-max-width' => $contentMaxWidth,
        'content-bg-color' => $contentBgColor,
        'a-color' => $aColor,
        'a-hover-color' => $aHoverColor,
        'border' => $border,
        'top-contact-and-social-container-margin-top' => $topContactAndSocialContainerMarginTop,
        'top-contact-and-social-container-mobile-margin-top' => $topContactAndSocialContainerMobileMarginTop,
        'top-contact-and-social-bg-color' => $topContactAndSocialBgColor,
        'top-contact-and-social-link-color' => $topContactAndSocialLinkColor,
        'top-contact-and-social-link-hover-color' => $topContactAndSocialLinkHoverColor,
        'top-contact-and-social-icon-color' => $topContactAndSocialIconColor,
        'top-contact-and-social-icon-hover-color' => $topContactAndSocialIconHoverColor,
        'top-contact-and-social-bg-color-mobile' => $topContactAndSocialBgColorMobile,
        'top-contact-and-social-link-color-mobile' => $topContactAndSocialLinkColorMobile,
        'top-contact-and-social-link-hover-color-mobile' => $topContactAndSocialLinkHoverColorMobile,
        'top-contact-and-social-icon-color-mobile' => $topContactAndSocialIconColorMobile,
        'top-contact-and-social-icon-hover-color-mobile' => $topContactAndSocialIconHoverColorMobile,
        'header-bg-color' => $headerBgColor,
        'header-logo-width' => $headerLogoWidth,
        'header-logo-mobile-width' => $headerLogoMobileWidth,
        'header-min-height' => $headerMinHeight,
        'header-mobile-min-height' => $headerMobileMinHeight,
        'header-logo-margin' => $headerLogoMargin,
        'header-mobile-logo-margin' => $headerMobileLogoMargin,
        'shop-menu-container-icon-color-search' => $shopMenuContainerIconColorSearch,
        'shop-menu-container-icon-color-member' => $shopMenuContainerIconColorMember,
        'shop-menu-container-icon-color-favorites' => $shopMenuContainerIconColorFavorites,
        'shop-menu-container-icon-color-basket' => $shopMenuContainerIconColorBasket,
        'shop-menu-container-icon-hover-color' => $shopMenuContainerIconHoverColor,
        'main-menu-bg-color' => $mainMenuBgColor,
        'main-menu-link-bg-color' => $mainMenuLinkBgColor,
        'main-menu-link-hover-bg-color' => $mainMenuLinkHoverBgColor,
        'main-menu-link-color' => $mainMenuLinkColor,
        'main-menu-link-hover-color' => $mainMenuLinkHoverColor,
        'main-menu-ul-bg-color' => $mainMenuUlBgColor,
        'main-menu-ul-submenu-link-color' => $mainMenuUlSubmenuLinkColor,
        'main-menu-ul-submenu-link-hover-color' => $mainMenuUlSubmenuLinkHoverColor,
        'main-menu-ul-submenu-link-bg-color' => $mainMenuUlSubmenuLinkBgColor,
        'main-menu-ul-submenu-link-hover-bg-color' => $mainMenuUlSubmenuLinkHoverBgColor,
        'main-menu-link-font-size' => $mainMenuLinkFontSize,
        'main-menu-ul-submenu-link-font-size' => $mainMenuUlSubmenuLinkFontSize,
        
        // New comprehensive menu variables
        'menu-background-color' => $menuBackgroundColor,
        'menu-text-color' => $menuTextColor,
        'menu-hover-color' => $menuHoverColor,
        'menu-hover-bg-color' => $menuHoverBgColor,
        'menu-active-color' => $menuActiveColor,
        'menu-active-bg-color' => $menuActiveBgColor,
        'menu-font-size' => $menuFontSize,
        'menu-height' => $menuHeight,
        'menu-padding' => $menuPadding,
        
        // Submenu variables
        'submenu-bg-color' => $submenuBgColor,
        'submenu-text-color' => $submenuTextColor,
        'submenu-hover-color' => $submenuHoverColor,
        'submenu-hover-bg-color' => $submenuHoverBgColor,
        'submenu-font-size' => $submenuFontSize,
        'submenu-border-color' => $submenuBorderColor,
        'submenu-width' => $submenuWidth,
        
        // Mobile menu variables
        'mobile-menu-background-color' => $mobileMenuBgColor,
        'mobile-menu-text-color' => $mobileMenuTextColor,
        'mobile-menu-hover-color' => $mobileMenuHoverColor,
        'mobile-menu-hover-bg-color' => $mobileMenuHoverBgColor,
        'mobile-menu-active-color' => $mobileMenuActiveColor,
        'mobile-menu-active-bg-color' => $mobileMenuActiveBgColor,
        'mobile-menu-font-size' => $mobileMenuFontSize,
        'mobile-menu-padding' => $mobileMenuPadding,
        'mobile-menu-divider-color' => $mobileMenuDividerColor,
        'hamburger-icon-color' => $hamburgerIconColor,
        
        // Mobile submenu variables
        'mobile-submenu-bg-color' => $mobileSubmenuBgColor,
        'mobile-submenu-text-color' => $mobileSubmenuTextColor,
        'mobile-submenu-hover-color' => $mobileSubmenuHoverColor,
        'mobile-submenu-hover-bg-color' => $mobileSubmenuHoverBgColor,

        'spacing-xs' => $spacing_xs,
        'spacing-sm' => $spacing_sm,
        'spacing-md' => $spacing_md,
        'spacing-lg' => $spacing_lg,
        'spacing-xl' => $spacing_xl,
        'spacing-xxl' => $spacing_xxl,

        "font-size-xs" => "10",
        "font-size-small" => "12",
        "font-size-normal" => "16",
        "font-size-large" => "20",
        "font-size-xlarge" => "24",
        "font-size-xxlarge" => "32",
    ];

    $requestData['spacing-xs'] = $spacing_xs;
    $requestData['spacing-sm'] = $spacing_sm;
    $requestData['spacing-md'] = $spacing_md;
    $requestData['spacing-lg'] = $spacing_lg;
    $requestData['spacing-xl'] = $spacing_xl;
    $requestData['spacing-xxl'] = $spacing_xxl;

    $requestData['font-size-xs'] = "10";
    $requestData['font-size-small'] = "12";
    $requestData['font-size-normal'] = "14";
    $requestData['font-size-large'] = "18";
    $requestData['font-size-xlarge'] = "22";
    $requestData['font-size-xxlarge'] = "26";

    $requestData['homepage-product-box-width'] =  "18%";
    $requestData['category-product-box-width'] =  "23%";
    $requestData['search-product-box-width'] =  "23%";
    $requestData['page-product-box-width'] =  "23%";

    unset($requestData["action"]);
    unset($requestData["languageID"]);
    // JSON'a dönüştürme
    $jsonConfig = json_encode($requestData, JSON_PRETTY_PRINT);

    $fileName = 'index-'.$languageID;

    if($action == "savePreviewDesign"){
        $fileName = 'index-preview-'.$languageID;
        $_SESSION['previewDesign'] = true;
    }
    else{
        $_SESSION['previewDesign'] = "";
        unset($_SESSION['previewDesign']);

        if(file_exists(JSON_DIR.'CSS/index-preview-'.$languageID.'.json')){
            unlink(JSON_DIR.'CSS/index-preview-'.$languageID.'.json');
        }

        if(file_exists(CSS.'index-preview-'.$languageID.'.css')){
            unlink(CSS.'index-preview-'.$languageID.'.css');
        }
    }

    //JSON_DIR.'CSS/ folder yoksa oluşturalım
    if (!file_exists(JSON_DIR.'CSS/')) {
        mkdir(JSON_DIR.'CSS/', 0777, true);
    }

    // JSON dosyasına yazma, yazma başarılıysa başarılı dön
    if (file_put_contents(JSON_DIR.'CSS/'.$fileName.'.json', $jsonConfig)) {
        // CSS dizesi oluştur
        $cssContent = json_decode($jsonConfig, true);
        $css = ":root {\n";

        foreach ($cssContent as $key => $value) {
            // Null değerleri atla
            if ($value !== null) {
                $formattedValue = $value;

                // Sayısal değerler için birim ekleme mantığı
                if (is_numeric($value) && $value != 0) {
                    // Belirli anahtar kelimeler için px ekle
                    $px_keys = ['width', 'height', 'size', 'radius', 'padding', 'margin', 'spacing'];
                    $add_px = false;
                    foreach ($px_keys as $px_key) {
                        if (strpos($key, $px_key) !== false) {
                            $add_px = true;
                            break;
                        }
                    }

                    // İstisnalar: Birim eklenmeyecek anahtar kelimeler veya zaten birim içeren değerler
                    $no_unit_keys = ['aspect-ratio', 'line-height', 'font-weight', 'opacity', 'z-index', 'speed', 'timing'];
                    $has_unit = preg_match('/(px|%|em|rem|vh|vw|ch|ex|cm|mm|in|pt|pc)$/', $value);

                    if ($add_px && !$has_unit && !in_array($key, $no_unit_keys)) {
                        $formattedValue .= 'px';
                    }
                }
                // Eğer değer bir string ve 'var(--' ile başlıyorsa, olduğu gibi bırak
                elseif (is_string($value) && strpos($value, 'var(--') === 0) {
                    // Do nothing, leave as is
                }
                // Eğer değer bir string ve 'rgba' ile başlıyorsa, olduğu gibi bırak
                elseif (is_string($value) && strpos($value, 'rgba(') === 0) {
                    // Do nothing, leave as is
                }
                // Eğer değer bir string ve 'solid', 'dashed' gibi anahtar kelimeler içeriyorsa, olduğu gibi bırak
                elseif (is_string($value) && in_array($value, ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'ease', 'linear', 'ease-in', 'ease-out', 'ease-in-out'])) {
                    // Do nothing, leave as is
                }
                // Eğer değer bir string ve aspect-ratio ise tırnak içine al
                elseif ($key === 'product-image-aspect-ratio' && is_string($value)) {
                    $formattedValue = $value;
                }


                $css .= "    --{$key}: {$formattedValue};\n";
            }
        }

        $css .= "}\n";

        // CSS dosyasına yazma, yazma başarılıysa başarılı dön
        if (file_put_contents(CSS.$fileName.'.css', $css)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Tasarım kaydedildi'
            ]);
        }
        else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tasarım Kaydedilemedi'
            ]);
            $_SESSION['previewDesign'] = "";
            unset($_SESSION['previewDesign']);
        }

    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tasarım Kaydedilemedi'
        ]);
        $_SESSION['previewDesign'] = "";
        unset($_SESSION['previewDesign']);
    }

}
elseif($action == "resetDesign"){
    $languageID = $requestData["languageID"] ?? null;
    if (!isset($languageID)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dil seçin'
        ]);
        exit();
    }
    $languageID = intval($languageID);

    if(file_exists(JSON_DIR.'CSS/index-'.$languageID.'.json')){
        unlink(JSON_DIR.'CSS/index-'.$languageID.'.json');
    }
    if(file_exists(JSON_DIR.'CSS/index-preview-'.$languageID.'.json')){
        unlink(JSON_DIR.'CSS/index-preview-'.$languageID.'.json');
    }
    if(file_exists(CSS.'index-'.$languageID.'.css')){
        unlink(CSS.'index-'.$languageID.'.css');
    }
    if(file_exists(CSS.'index-preview-'.$languageID.'.css')){
        unlink(CSS.'index-preview-'.$languageID.'.css');
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Tasarım ayarları sıfırlandı'
    ]);

}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
}
exit();