<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * Gelişmiş Tema Özelleştirme Sayfası
 * @var AdminDatabase $db
 * @var Config $config
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
} else {
    $imageID = 0;
    $logoImagePath = "../../_y/m/r/Logo/pozitif-eticaret-logo.png";
    $logoText = "pozitif E-Ticaret";
}

include_once MODEL ."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);
$languages = $languageModel->getLanguages();

// Tema yardımcı fonksiyonları
include_once __DIR__ . '/Theme/ThemeUtils.php';

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Gelişmiş Tema Özelleştirme - Admin Panel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Admin Panel CSS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-colorpicker/bootstrap-colorpicker.css" />
      <!-- Tema CSS -->
    <link type="text/css" rel="stylesheet" href="/_y/s/s/tasarim/Theme/css/theme.css" />
    <link type="text/css" rel="stylesheet" href="/_y/s/s/tasarim/Theme/css/theme-editor.css" />
    
    <!-- Özel Tema CSS -->
    <style>
        
        /* Responsive border preview */
        @media (max-width: 768px) {
            .border-preview-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            .border-preview-item .preview-border {
                padding: 10px 8px;
                font-size: 10px;
            }
        }          /* CSS değişkenlerini tanımla */
        :root {
            --primary-color: <?=sanitizeColorValue($customCSS['primary-color'] )?>;
            --primary-light-color: <?=sanitizeColorValue($customCSS['primary-light-color'] )?>;
            --primary-dark-color: <?=sanitizeColorValue($customCSS['primary-dark-color'] )?>;
            --secondary-color: <?=sanitizeColorValue($customCSS['secondary-color'] )?>;
            --secondary-light-color: <?=sanitizeColorValue($customCSS['secondary-light-color'] )?>;
            --secondary-dark-color: <?=sanitizeColorValue($customCSS['secondary-dark-color'] )?>;
            --accent-color: <?=sanitizeColorValue($customCSS['accent-color'] )?>;
            --success-color: <?=sanitizeColorValue($customCSS['success-color'] )?>;
            --info-color: <?=sanitizeColorValue($customCSS['info-color'] )?>;
            --warning-color: <?=sanitizeColorValue($customCSS['warning-color'] )?>;
            --danger-color: <?=sanitizeColorValue($customCSS['danger-color'] )?>;
            /* ========= Arka Plan Renkleri ========= */
            --body-bg-color: <?=sanitizeColorValue($customCSS['body-bg-color'] )?>;
            --content-bg-color: <?=sanitizeColorValue($customCSS['content-bg-color'] )?>;
            --background-primary-color: <?=sanitizeColorValue($customCSS['background-primary-color'] )?>;
            --background-secondary-color: <?=sanitizeColorValue($customCSS['background-secondary-color'] )?>;
            --background-light-color: <?=sanitizeColorValue($customCSS['background-light-color'] )?>;
            --background-dark-color: <?=sanitizeColorValue($customCSS['background-dark-color'] )?>;
            /* Sınır ve Köşe Değişkenleri */
            --border-color: <?=sanitizeColorValue($customCSS['border-color'] )?>;
            --border-light-color: <?=sanitizeColorValue($customCSS['border-light-color'] )?>;
            --border-dark-color: <?=sanitizeColorValue($customCSS['border-dark-color'] )?>;
            --border-style: <?=$customCSS['border-style'] ?>;
            --border-width: <?=$customCSS['border-width'] ?>px;
            --border-radius-base: <?=$customCSS['border-radius-base'] ?>px;
            --card-border-radius: <?=$customCSS['card-border-radius'] ?>px;
            --btn-border-radius: <?=$customCSS['button-border-radius'] ?>px;
            --input-border-radius: <?=$customCSS['input-border-radius'] ?>px;
            
        
        
            
            /* Üst İletişim & Sosyal Medya Değişkenleri */
            --top-contact-and-social-bg-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-bg-color'] )?>;
            --top-contact-and-social-link-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-color'] )?>;
            --top-contact-and-social-link-hover-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-hover-color'] )?>;
            --top-contact-and-social-icon-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-color'] )?>;
            --top-contact-and-social-icon-hover-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-hover-color'] )?>;
            --top-contact-and-social-container-margin-top: <?=$customCSS['top-contact-and-social-container-margin-top'] ?>px;
            
            /* Üst İletişim & Sosyal Medya Mobile Değişkenleri */
            --top-contact-and-social-bg-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-bg-color-mobile'] )?>;
            --top-contact-and-social-link-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-color-mobile'] )?>;
            --top-contact-and-social-link-hover-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-hover-color-mobile'] )?>;
            --top-contact-and-social-icon-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-color-mobile'] )?>;
            --top-contact-and-social-icon-hover-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-hover-color-mobile'] )?>;
            --top-contact-and-social-container-mobile-margin-top: <?=$customCSS['top-contact-and-social-container-mobile-margin-top'] ?>px;
            
            /* Header Değişkenleri */
            --header-bg-color: <?=sanitizeColorValue($customCSS['header-bg-color'] )?>;
            --header-border-width: <?=$customCSS['header-border-width'] ?>px;
            --header-border-color: <?=sanitizeColorValue($customCSS['header-border-color'] )?>;
            --header-padding: <?=$customCSS['header-padding'] ?>px;
            --header-min-height: <?=$customCSS['header-min-height'] ?>px;
            --header-logo-width: <?=sanitizeNumericValue($customCSS['header-logo-width'], 'px')?>;

            /* Header Mobile Değişkenleri */
            --header-mobile-bg-color: <?=sanitizeColorValue($customCSS['header-mobile-bg-color'] )?>;
            --header-mobile-border-width: <?=sanitizeNumericValue($customCSS['header-mobile-border-width'] )?>;
            --header-mobile-border-color: <?=sanitizeColorValue($customCSS['header-mobile-border-color'] )?>;
            --header-mobile-padding: <?=sanitizeNumericValue($customCSS['header-mobile-padding'] )?>;
            --header-mobile-min-height: <?=sanitizeNumericValue($customCSS['header-mobile-min-height'], 'px')?>;
            --header-logo-mobile-width: <?=sanitizeNumericValue($customCSS['header-logo-mobile-width'], 'px')?>>;

            /* Alışveriş İkon Renkleri */
            --shop-menu-container-icon-color-search: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-search'] )?>;
            --shop-menu-container-icon-color-member: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-member'] )?>;
            --shop-menu-container-icon-color-favorites: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-favorites'] )?>;
            --shop-menu-container-icon-color-basket: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-basket'] )?>;
            --shop-menu-container-icon-hover-color: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-hover-color'] )?>;
            --mobile-action-icon-phone-bg-color: <?=sanitizeColorValue($customCSS['mobile-action-icon-phone-bg-color'] )?>;
            --mobile-action-icon-whatsapp-bg-color: <?=sanitizeColorValue($customCSS['mobile-action-icon-whatsapp-bg-color'] )?>;
            --mobile-action-icon-basket-bg-color: <?=sanitizeColorValue($customCSS['mobile-action-icon-basket-bg-color'] )?>;
            --mobile-action-icon-basket-counter-bg-color: <?=sanitizeColorValue($customCSS['mobile-action-icon-basket-counter-bg-color'] )?>;

            --shop-menu-container-mobile-icon-color-search: <?=sanitizeColorValue($customCSS['shop-menu-container-mobile-icon-color-search'] )?>;
            --shop-menu-container-mobile-icon-color-member: <?=sanitizeColorValue($customCSS['shop-menu-container-mobile-icon-color-member'] )?>;
            --shop-menu-container-mobile-icon-color-favorites: <?=sanitizeColorValue($customCSS['shop-menu-container-mobile-icon-color-favorites'] )?>;
            --shop-menu-container-mobile-icon-color-basket: <?=sanitizeColorValue($customCSS['shop-menu-container-mobile-icon-color-basket'] )?>;
            --action-icon-basket-counter-bg-color: <?=sanitizeColorValue($customCSS['action-icon-basket-counter-bg-color'] )?>;
            
            /* Logo Margin Değişkenleri (Desktop) */
            --header-logo-margin-top: <?=$customCSS['header-logo-margin-top'] ?>px;
            --header-logo-margin-right: <?=$customCSS['header-logo-margin-right'] ?>px;
            --header-logo-margin-bottom: <?=$customCSS['header-logo-margin-bottom'] ?>px;
            --header-logo-margin-left: <?=$customCSS['header-logo-margin-left'] ?>px;
            
            /* Logo Margin Değişkenleri (Mobile) */
            --header-mobile-logo-margin-top: <?=$customCSS['header-mobile-logo-margin-top'] ?>px;
            --header-mobile-logo-margin-right: <?=$customCSS['header-mobile-logo-margin-right'] ?>px;
            --header-mobile-logo-margin-bottom: <?=$customCSS['header-mobile-logo-margin-bottom'] ?>px;
            --header-mobile-logo-margin-left: <?=$customCSS['header-mobile-logo-margin-left'] ?>px;
            
            /* Menü Değişkenleri */
            --menu-background-color: <?=sanitizeColorValue($customCSS['menu-background-color'] )?>;
            --menu-text-color: <?=sanitizeColorValue($customCSS['menu-text-color'] )?>;
            --menu-hover-color: <?=sanitizeColorValue($customCSS['menu-hover-color'] )?>;
            --menu-hover-bg-color: <?=sanitizeColorValue($customCSS['menu-hover-bg-color'] )?>;
            --menu-active-color: <?=sanitizeColorValue($customCSS['menu-active-color'] )?>;
            --menu-active-bg-color: <?=sanitizeColorValue($customCSS['menu-active-bg-color'] )?>;
            --menu-font-size: <?=$customCSS['menu-font-size'] ?>px;
            --menu-height: <?=$customCSS['menu-height'] ?>px;
            --menu-padding: <?=$customCSS['menu-padding'] ?>px;
            
            /* Alt Menü (Dropdown) Değişkenleri */
            --submenu-bg-color: <?=sanitizeColorValue($customCSS['submenu-bg-color'] )?>;
            --submenu-text-color: <?=sanitizeColorValue($customCSS['submenu-text-color'] )?>;
            --submenu-hover-color: <?=sanitizeColorValue($customCSS['submenu-hover-color'] )?>;
            --submenu-hover-bg-color: <?=sanitizeColorValue($customCSS['submenu-hover-bg-color'] )?>;
            --submenu-border-color: <?=sanitizeColorValue($customCSS['submenu-border-color'] )?>;
            --submenu-width: <?=$customCSS['submenu-width'] ?>px;
            --submenu-font-size: <?=$customCSS['submenu-font-size'] ?>px;

            /* Mobil Menü Değişkenleri */
            --mobile-menu-background-color: <?=sanitizeColorValue($customCSS['mobile-menu-background-color'] )?>;
            --mobile-menu-text-color: <?=sanitizeColorValue($customCSS['mobile-menu-text-color'] )?>;
            --mobile-menu-hover-color: <?=sanitizeColorValue($customCSS['mobile-menu-hover-color'] )?>;
            --mobile-menu-hover-bg-color: <?=sanitizeColorValue($customCSS['mobile-menu-hover-bg-color'] )?>;
            --mobile-menu-divider-color: <?=sanitizeColorValue($customCSS['mobile-menu-divider-color'] )?>;
            --hamburger-icon-color: <?=sanitizeColorValue($customCSS['hamburger-icon-color'] )?>;
            --mobile-menu-font-size: <?=$customCSS['mobile-menu-font-size'] ?>px;
            --mobile-menu-padding: <?=$customCSS['mobile-menu-padding'] ?>px;

            /* Ürün Kutusu Değişkenleri */
            --product-box-background-color: <?=sanitizeColorValue($customCSS['product-box-background-color'] )?>;
            --product-box-border-color: <?=sanitizeColorValue($customCSS['product-box-border-color'] )?>;
            --product-box-hover-border-color: <?=sanitizeColorValue($customCSS['product-box-hover-border-color'] )?>;
            --product-box-border-radius: <?=$customCSS['product-box-border-radius'] ?>px;
            --product-box-padding: <?=$customCSS['product-box-padding'] ?>px;
            --product-title-color: <?=sanitizeColorValue($customCSS['product-title-color'] )?>;
            --product-price-color: <?=sanitizeColorValue($customCSS['product-price-color'] )?>;
            --product-sale-price-color: <?=sanitizeColorValue($customCSS['product-sale-price-color'] )?>;
            --product-old-price-color: <?=sanitizeColorValue($customCSS['product-old-price-color'] )?>;
            --product-discount-badge-color: <?=sanitizeColorValue($customCSS['product-discount-badge-color'] )?>;
            --add-to-cart-bg-color: <?=sanitizeColorValue($customCSS['add-to-cart-bg-color'] )?>;
            --add-to-cart-text-color: <?=sanitizeColorValue($customCSS['add-to-cart-text-color'] )?>;
            --add-to-cart-hover-bg-color: <?=sanitizeColorValue($customCSS['add-to-cart-hover-bg-color'] )?>;
            --input-height: <?=$customCSS['input-height'] ?>px;
            --input-padding: <?=$customCSS['input-padding'] ?>px;
            --input-border-radius: <?=$customCSS['input-border-radius'] ?>px;
            --btn-padding-y: <?=$customCSS['btn-padding-y'] ?>px;
            --btn-padding-x: <?=$customCSS['btn-padding-x'] ?>px;

            /* ========= Form Elemanları ========= */
            --input-bg-color: <?=sanitizeColorValue($customCSS['input-bg-color'] )?>;
            --input-border-color: <?=sanitizeColorValue($customCSS['input-border-color'] )?>;
            --input-text-color: <?=sanitizeColorValue($customCSS['input-text-color'] )?>;
            --input-placeholder-color: <?=sanitizeColorValue($customCSS['input-placeholder-color'] )?>;
            --btn-primary-bg-color: <?=sanitizeColorValue($customCSS['btn-primary-bg-color'] )?>;
            --btn-primary-text-color: <?=sanitizeColorValue($customCSS['btn-primary-text-color'] )?>;
            --btn-primary-hover-bg-color: <?=sanitizeColorValue($customCSS['btn-primary-hover-bg-color'] )?>;
            --btn-primary-border-color: <?=sanitizeColorValue($customCSS['btn-primary-border-color'] )?>;
            --btn-secondary-bg-color: <?=sanitizeColorValue($customCSS['btn-secondary-bg-color'] )?>;
            --btn-secondary-text-color: <?=sanitizeColorValue($customCSS['btn-secondary-text-color'] )?>;
            --btn-secondary-hover-bg-color: <?=sanitizeColorValue($customCSS['btn-secondary-hover-bg-color'] )?>;
            --btn-outline-color: <?=sanitizeColorValue($customCSS['btn-outline-color'] )?>;
            --form-label-color: <?=sanitizeColorValue($customCSS['form-label-color'] )?>;
            --form-required-color: <?=sanitizeColorValue($customCSS['form-required-color'] )?>;
            --form-error-color: <?=sanitizeColorValue($customCSS['form-error-color'] )?>;
            --form-success-color: <?=sanitizeColorValue($customCSS['form-success-color'] )?>;

            /* Logo Değişkenleri */
            --logo-width: <?=$customCSS['logo-width'] ?>px;
            --logo-height: <?=$customCSS['logo-height'] ?>;
            --logo-margin-top: <?=$customCSS['logo-margin-top'] ?>px;
            --logo-margin-right: <?=$customCSS['logo-margin-right'] ?>px;
            --logo-margin-bottom: <?=$customCSS['logo-margin-bottom'] ?>px;
            --logo-margin-left: <?=$customCSS['logo-margin-left'] ?>px;

            /* Metin Renkleri */
            --text-primary-color: <?=sanitizeColorValue($customCSS['text-primary-color'] )?>;
            --text-secondary-color: <?=sanitizeColorValue($customCSS['text-secondary-color'] )?>;

            /* Footer Değişkenleri */
            --footer-background-color: <?=sanitizeColorValue($customCSS['footer-background-color'] )?>;
            --footer-text-color: <?=sanitizeColorValue($customCSS['footer-text-color'] )?>;
            --footer-link-color: <?=sanitizeColorValue($customCSS['footer-link-color'] )?>;
            --footer-link-hover-color: <?=sanitizeColorValue($customCSS['footer-link-hover-color'] )?>;
            --footer-logo-width: <?=sanitizeNumericValue($customCSS['footer-logo-width'], 'px', 400)?>;
            --footer-logo-height: <?=sanitizeNumericValue($customCSS['footer-logo-height'], 'px', 400)?>;
            --social-icon-color: <?=sanitizeColorValue($customCSS['social-icon-color'] )?>;
            --social-icon-hover-color: <?=sanitizeColorValue($customCSS['social-icon-hover-color'] )?>;
            --social-icon-size: <?=$customCSS['social-icon-size'] ?>px;
            --footer-padding-y: <?=$customCSS['footer-padding-y'] ?>px;
            --footer-font-size: <?=$customCSS['footer-font-size'] ?>px;
            --copyright-padding: <?=$customCSS['copyright-padding'] ?>px;


            /* Copyright Değişkenleri */
            --copyright-background-color: <?=sanitizeColorValue($customCSS['copyright-background-color'] )?>;
            --copyright-text-color: <?=sanitizeColorValue($customCSS['copyright-text-color'] )?>;
            --copyright-link-color: <?=sanitizeColorValue($customCSS['copyright-link-color'] )?>;
            --copyright-border-top-color: <?=sanitizeColorValue($customCSS['copyright-border-top-color'] )?>;

            /* Responsive Değişkenleri */
            --mobile-breakpoint: <?=$customCSS['mobile-breakpoint'] ?>px;
            --tablet-breakpoint: <?=$customCSS['tablet-breakpoint'] ?>px;
            --desktop-breakpoint: <?=$customCSS['desktop-breakpoint'] ?>px;
            --mobile-container-padding: <?=$customCSS['mobile-container-padding'] ?>px;
            --tablet-container-padding: <?=$customCSS['tablet-container-padding'] ?>px;
            --desktop-max-width: <?=$customCSS['desktop-max-width'] ?>px;
            --mobile-base-font-size: <?=$customCSS['mobile-base-font-size'] ?>px;
            --mobile-h1-font-size: <?=$customCSS['mobile-h1-font-size'] ?>px;
            --mobile-line-height: <?=$customCSS['mobile-line-height'] ?>;
            --mobile-section-margin: <?=$customCSS['mobile-section-margin'] ?>px;
            --mobile-card-margin: <?=$customCSS['mobile-card-margin'] ?>px;
            --mobile-button-height: <?=$customCSS['mobile-button-height'] ?>px;
            --touch-target-size: <?=$customCSS['touch-target-size'] ?>px;
            --hide-banner-mobile: <?=($customCSS['hide-banner-mobile'] ?? false) ? 'none' : 'block'?>;
            --hide-sidebar-mobile: <?=($customCSS['hide-sidebar-mobile'] ?? false) ? 'none' : 'block'?>;
            --hide-breadcrumb-mobile: <?=($customCSS['hide-breadcrumb-mobile'] ?? false) ? 'none' : 'block'?>;

            /* Responsive Frame Renkleri */
            --desktop-frame-bg-color: <?=sanitizeColorValue($customCSS['desktop-frame-bg-color'] )?>;
            --tablet-frame-bg-color: <?=sanitizeColorValue($customCSS['tablet-frame-bg-color'] )?>;
            --mobile-frame-bg-color: <?=sanitizeColorValue($customCSS['mobile-frame-bg-color'] )?>;
        }
          /* Üst İletişim & Sosyal Medya Hover Efektleri */
        #topContactPreview span:hover {
            color: var(--top-contact-and-social-link-hover-color, #4285f4) !important;
        }
        
        #topContactPreview i.fa:hover {
            color: var(--top-contact-and-social-icon-hover-color, #4285f4) !important;
        }
        
        /* Mobile Preview Hover Efektleri */
        #mobileHeaderPreview span:hover {
            color: var(--top-contact-and-social-link-hover-color-mobile, var(--top-contact-and-social-link-hover-color, #4285f4)) !important;
        }
        
        #mobileHeaderPreview i.fa:hover {
            color: var(--top-contact-and-social-icon-hover-color-mobile, var(--top-contact-and-social-icon-hover-color, #4285f4)) !important;
        }
        
        /* Dual Preview Hover Efektleri */
        #dualTopContactPreview span:hover,
        #dualMobileHeaderPreview span:hover {
            color: var(--top-contact-and-social-link-hover-color-mobile, var(--top-contact-and-social-link-hover-color) !important;
        }
        
        #dualTopContactPreview i.fa:hover,
        #dualMobileHeaderPreview i.fa:hover {
            color: var(--top-contact-and-social-icon-hover-color-mobile, var(--top-contact-and-social-icon-hover-color, #4285f4)) !important;
        }
        
        /* Alışveriş İkonları Hover Efektleri */        #headerPreviewContent .fa-search:hover,
        #headerPreviewContent .fa-user:hover,
        #headerPreviewContent .fa-heart:hover,
        #headerPreviewContent .fa-shopping-cart:hover {
            color: var(--shop-menu-container-icon-hover-color) !important;
        }        /* Sabitlenmiş Header Önizleme Stilleri - GÜÇLÜ CSS */
        .header-preview-fixed {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 99999 !important;
            background: white !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
            border-radius: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: none !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateY(0) !important;
            animation: none !important; /* Animasyonu devre dışı bırak */
        }
        
        /* Animasyon problemini önle */
        .header-preview-fixed * {
            animation: none !important;
            transition: none !important;
        }
          .header-preview-fixed .card-header {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 10px 15px !important;
        }
        
        .header-preview-fixed .card-body {
            max-height: 350px;
            overflow-y: auto;
            padding: 15px !important;
        }
        
        .header-preview-fixed .theme-preview {
            margin: 10px 0;
            border: 1px solid #ddd;
            min-height: 200px;
        }
        
        /* Mobile Header Preview Fixed Stilleri */
        .mobile-header-preview-fixed {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 99999 !important;
            width: 100% !important;
            margin: 0 !important;
            border-radius: 0 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            animation: slideDownPreview 0.3s ease-out;
        }
        
        .mobile-header-preview-fixed .card-header {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 10px 15px !important;
        }
        
        .mobile-header-preview-fixed .card-body {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px !important;
        }
          .mobile-header-preview-fixed .theme-preview {
            margin: 10px 0;
            border: 1px solid #ddd;
            min-height: 150px;
        }
        
        /* Yan Yana Dual Preview Stilleri */
        .dual-preview-container {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 99999 !important;
            width: 100% !important;
            height: auto !important;
            max-height: 500px !important;
            margin: 0 !important;
            border-radius: 0 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            background: #f8f9fa !important;
            display: flex !important;
            animation: slideDownPreview 0.3s ease-out;
        }
        
        .dual-preview-desktop {
            flex: 1 !important;
            min-width: 60% !important;
            border-right: 2px solid #dee2e6 !important;
            background: white !important;
        }
        
        .dual-preview-mobile {
            flex: 0 0 400px !important;
            max-width: 400px !important;
            background: white !important;
        }
        
        .dual-preview-desktop .card-header,
        .dual-preview-mobile .card-header {
            background: #e9ecef !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 8px 15px !important;
            font-size: 14px !important;
        }
        
        .dual-preview-desktop .card-body,
        .dual-preview-mobile .card-body {
            padding: 15px !important;
            max-height: 400px !important;
            overflow-y: auto !important;
        }
        
        .dual-preview-desktop .theme-preview,
        .dual-preview-mobile .theme-preview {
            margin: 5px 0 !important;
            border: 1px solid #ddd !important;
            min-height: 150px !important;
        }
        
        /* Dual preview için özel close button */
        .dual-preview-close {
            position: absolute !important;
            top: 10px !important;
            right: 15px !important;
            z-index: 100000 !important;
            background: #dc3545 !important;
            color: white !important;
            border: none !important;
            border-radius: 50% !important;
            width: 35px !important;
            height: 35px !important;
            font-size: 16px !important;
            cursor: pointer !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
        }
        
        .dual-preview-close:hover {
            background: #c82333 !important;
            transform: scale(1.1) !important;
        }
          /* Dual preview aktifken body padding */
        body.dual-preview-active {
            padding-top: 520px !important;
            transition: padding-top 0.3s ease;
        }
        
        /* Dual preview aktifken admin header sabitlenmesi */
        body.dual-preview-active #header {
            position: fixed !important;
            top: 0 !important;
            z-index: 99998 !important; /* Dual preview'in (99999) altında */
            width: 100% !important;
        }
        
        /* Dual preview aktifken admin base padding */
        body.dual-preview-active #base {
            padding-top: 60px !important; /* Admin header yüksekliği kadar */
        }
        /* Sabitlenmiş preview için body padding */
        body.header-preview-pinned {
            padding-top: 400px !important;
            transition: padding-top 0.3s ease;
        }
        
        body.mobile-header-preview-pinned {
            padding-top: 450px !important; /* Mobile header daha uzun olduğu için artırdık */
            transition: padding-top 0.3s ease;
        }
        
        /* Admin Header Kontrolü - Preview sabitlendiğinde */
        body.header-preview-pinned #header,
        body.mobile-header-preview-pinned #header {
            top: 0 !important;
            z-index: 99998 !important; /* Preview'ın (99999) altında ama diğerlerinin üstünde */
            position: fixed !important;
        }
        
        /* Admin header sabitlendiğinde content'e extra padding */
        body.header-preview-pinned #base,
        body.mobile-header-preview-pinned #base {
            padding-top: 60px !important; /* Admin header yüksekliği kadar */
        }
        
        /* Animasyon */
        @keyframes slideDownPreview {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideUpPreview {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }
        
        .header-preview-removing {
            animation: slideUpPreview 0.3s ease-in-out;
        }
        
        .mobile-header-preview-removing {
            animation: slideUpPreview 0.3s ease-in-out;
        }
        
        /* Toggle butonları */
        #toggleHeaderPreview,
        #toggleMobileHeaderPreview {
            transition: all 0.3s ease;
        }
        
        #toggleHeaderPreview:hover,
        #toggleMobileHeaderPreview:hover {
            transform: scale(1.1);
        }
        
        .preview-pinned {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
        }
    </style>
</head>
<body class="menubar-hoverable header-fixed">
    <?php require_once(ROOT."/_y/s/b/header.php");?>
    <section id="base">
        <div id="content">
            <div class="container-fluid">
                <div class="section-header">
                    <h1>🎨 Gelişmiş Tema Özelleştirme</h1>
                    <p class="lead">Sitenizin görünümünü istediğiniz gibi özelleştirin</p>
                </div>

                <!-- Dil Seçimi -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tema Düzenlenecek Dil:</label>
                                <select id="languageSelect" class="form-control">
                                    <?php foreach ($languages as $language): ?>
                                        <option value="<?=$language['languageID']?>" <?=($languageID == $language['languageID']) ? 'selected' : ''?>>
                                            <?=$language['languageName']?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Site Önizleme:</label>
                                <div class="button-group">
                                    <button type="button" class="btn btn-info" onclick="openPreview()">
                                        <i class="fa fa-eye"></i> Siteyi Önizle
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="openPreview(true)">
                                        <i class="fa fa-mobile"></i> Mobil Önizleme
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ana Tema Formu -->
                <form id="themeForm">
                    <input type="hidden" name="languageID" value="<?=$languageID?>">
                    
                    <!-- Sekmeli Yapı -->
                    <div class="theme-tabs">
                        <ul class="nav nav-tabs" id="themeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-toggle="tab" data-target="#general-panel" type="button" role="tab">
                                    <i class="fa fa-palette"></i> Genel Görünüm
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="header-tab" data-toggle="tab" data-target="#header-panel" type="button" role="tab">
                                    <i class="fa fa-window-maximize"></i> Header
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="menu-tab" data-toggle="tab" data-target="#menu-panel" type="button" role="tab">
                                    <i class="fa fa-list"></i> Menü
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="products-tab" data-toggle="tab" data-target="#products-panel" type="button" role="tab">
                                    <i class="fa fa-shopping-cart"></i> Ürün Kutuları
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="forms-tab" data-toggle="tab" data-target="#forms-panel" type="button" role="tab">
                                    <i class="fa fa-edit"></i> Form & Butonlar
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="footer-tab" data-toggle="tab" data-target="#footer-panel" type="button" role="tab">
                                    <i class="fa fa-window-minimize"></i> Footer & Diğer
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="responsive-tab" data-toggle="tab" data-target="#responsive-panel" type="button" role="tab">
                                    <i class="fa fa-mobile"></i> Responsive
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="themes-tab" data-toggle="tab" data-target="#themes-panel" type="button" role="tab">
                                    <i class="fa fa-magic"></i> Hazır Temalar
                                </button>
                            </li>
                        </ul>
                    </div>                    
                    <!-- Sekme İçerikleri -->
                    <div class="tab-content" id="themeTabContent">
                        <!-- Genel Görünüm Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/colors.php'; ?>
                        
                        <!-- Header Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/header.php'; ?>
                        
                        <!-- Menu Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/menu.php'; ?>
                          <!-- Products Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/products.php'; ?>
                        
                        <!-- Forms Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/forms.php'; ?>

                        <!-- Footer Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/footer.php'; ?>

                        <!-- Responsive Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/responsive.php'; ?>

                          <!-- Themes Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/themes.php'; ?>
                    </div>
                </form>
                
                <!-- Tema Kaydetme Butonları -->
                <div class="card">
                    <div class="card-body">
                        <div class="button-group">
                            <button type="button" class="btn btn-theme-save btn-lg" onclick="saveTheme()">
                                <i class="fa fa-save"></i> Temayı Kaydet
                            </button>
                            <button type="button" class="btn btn-theme-preview btn-lg" onclick="previewTheme()">
                                <i class="fa fa-eye"></i> Canlı Önizleme
                            </button>
                            <button type="button" class="btn btn-theme-reset btn-lg" onclick="resetTheme()">
                                <i class="fa fa-refresh"></i> Sıfırla
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <?php require_once(ROOT."/_y/s/b/menu.php");?>
    </div>

    <!-- JavaScript -->    
     <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
    <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
    <script src="/_y/assets/js/core/source/App.js"></script>
    <script src="/_y/assets/js/core/source/AppNavigation.js"></script>      <!-- Gelişmiş Tema Düzenleyici JavaScript - Modüler Yapı -->
    <script src="/_y/s/s/tasarim/Theme/js/core.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/header.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/menu.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/products.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/forms.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/footer.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/responsive.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/theme-editor.js" defer></script>
    <script src="/_y/s/s/tasarim/Theme/js/themes-tab.js" defer></script>
    
    <script>
        // Tema JavaScript fonksiyonları
        $(document).ready(function() {
            console.log('Theme.php DOM ready - Tab sistemi başlatılıyor...');
            
            // Menü aktif hale getirme
            $("#themephp").addClass("active");
            
            // Dil değişikliği
            $('#languageSelect').change(function() {
                window.location.href = '?languageID=' + $(this).val();
            });
            
            // Sayfa yüklendiğinde değer kontrolü
            validateAllInputs();
            
            // Bootstrap tabs manuel başlatma
            try {
                $('#themeTabs button[data-toggle="tab"]').tab();
            } catch(e) {
                console.log('Bootstrap tab plugin bulunamadı, manuel başlatma yapılıyor...');
                // Manuel tab sistemi
                $('#themeTabs button[data-toggle="tab"]').click(function(e) {
                    e.preventDefault();
                    
                    // Tüm tabları deaktive et
                    $('#themeTabs .nav-link').removeClass('active');
                    $('.tab-pane').removeClass('active in show');
                    
                    // Tıklanan tab'ı aktive et
                    $(this).addClass('active');
                    const target = $(this).attr('data-target');
                    $(target).addClass('active in');
                    
                    console.log('Manual tab switched to:', target);
                });
            }
            
            // Tab değişikliği olaylarını dinle
            $('#themeTabs button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                console.log('Tab changed to:', e.target.id);
                // İlgili tab'a gözel işlemler yapılabilir
            });
              // Bootstrap colorpicker'ı devre dışı bırak - Sadece HTML5 color input kullan
            $('.color-picker').off('colorpicker');
            
            // İlk sekmeyi doğru şekilde aktif yap
            setTimeout(() => {
                // Önce tümünü temizle
                $('.nav-tabs .nav-link').removeClass('active');
                $('.tab-pane').removeClass('active in show');
                
                // İlk sekmeyi aktif yap
                $('.nav-tabs .nav-link:first').addClass('active');
                $('.tab-content .tab-pane:first').addClass('active in show');
                
                console.log('✅ İlk sekme (#general-panel) aktif hale getirildi');
            }, 100);
              // Tab değişikliklerini yönet
            $('.nav-tabs .nav-link').click(function(e) {
                e.preventDefault();
                
                const target = $(this).attr('data-target');
                
                console.log('🔄 Sekme değiştirildi:', target);
                
                // Tüm sekmeleri pasif yap
                $('.nav-tabs .nav-link').removeClass('active');
                $('.tab-pane').removeClass('active in show');
                
                // Seçilen sekmeyi aktif yap
                $(this).addClass('active');
                $(target).addClass('active in show'); // 'show' class'ını da ekle
                
                console.log('✅ Sekme aktif hale getirildi:', target);
            });
              // Form başlatma
            validateAllInputs();
              // ThemeEditor theme-editor.js tarafından başlatılacak            // İlk yüklemede önizlemeyi güncelle - güvenli şekilde
            setTimeout(() => {
                if (typeof window.themeEditorInstance !== 'undefined' && window.themeEditorInstance) {
                    try {
                        window.themeEditorInstance.updatePreview();
                        console.log('🚀 İlk yüklemede önizleme güncellendi');
                    } catch (error) {
                        console.warn('⚠️ Preview güncelleme hatası:', error.message);
                    }
                } else {
                    console.warn('⚠️ ThemeEditor instance henüz hazır değil');
                }
            }, 1000); // 1 saniye bekle ki tüm script'ler yüklenmiş olsun
            
            // Themes tab'ı jQuery yüklendikten sonra initialize et
            if (typeof initializeThemesTab === 'function') {
                console.log('🎨 Themes tab initialize ediliyor...');
                initializeThemesTab();
            } else {
                console.warn('⚠️ initializeThemesTab function not found!');
            }
        });// Güçlü renk değeri validasyonu ve düzeltme fonksiyonu
        function validateAllColorInputs() {
            $('.color-picker').each(function() {
                const $input = $(this);
                let value = $input.val();
                let originalValue = value;
                
                // Boş değer kontrolü
                if (!value || value.trim() === '') {
                    value = '#ffffff';
                }
                
                // Çeşitli renk formatlarını normalize et
                value = normalizeColorValue(value);
                
                // Son kontrol: Geçerli hex değeri mi?
                if (!isValidHexColor(value)) {
                    const fallbackColor = $input.data('fallback') || $input.attr('data-default') || '#ffffff';
                    value = fallbackColor;
                    console.warn('🎨 Geçersiz renk değeri düzeltildi:', originalValue, '→', value);
                }
                
                // Değer değiştiyse güncelle
                if (value !== originalValue) {
                    $input.val(value);
                }
                try {
                    const testInput = document.createElement('input');
                    testInput.type = 'color';
                    testInput.value = value;
                    
                    if (testInput.value !== value) {
                        const fallbackColor = $input.data('fallback') || '#ffffff';
                        $input.val(fallbackColor);
                        console.log('Tarayıcı uyumsuzluğu düzeltildi:', value, '→', fallbackColor);
                    }
                } catch (e) {
                    console.log('Color input validation hatası:', e);
                }
            });
        }
        
        // Sayısal input değerlerini kontrol et ve düzelt
        function validateNumericInputs() {
            $('input[type="number"]').each(function() {
                const $input = $(this);
                const value = $input.val();
                
                // Geçersiz değerleri düzelt
                if (value && isNaN(parseFloat(value))) {
                    const fallbackValue = $input.data('fallback') || '0';
                    $input.val(fallbackValue);
                    console.log('Fixed invalid numeric value:', value, 'to', fallbackValue);
                }
            });
        }        // Renk normalizasyon fonksiyonu
        function normalizeColorValue(value) {
            if (!value) return '#ffffff';
            
            value = value.toString().trim().toLowerCase();
            
            // CSS renk adlarını hex'e çevir
            const colorMap = {
                'white': '#ffffff', 'black': '#000000', 'red': '#ff0000',
                'green': '#008000', 'blue': '#0000ff', 'yellow': '#ffff00',
                'cyan': '#00ffff', 'magenta': '#ff00ff', 'silver': '#c0c0c0',
                'gray': '#808080', 'grey': '#808080', 'orange': '#ffa500',
                'purple': '#800080', 'navy': '#000080', 'transparent': '#ffffff'
            };
            
            if (colorMap[value]) {
                return colorMap[value];
            }
            
            // # işareti ekle
            if (value && !value.startsWith('#')) {
                value = '#' + value;
            }
            
            // 3 haneli hex'i 6 haneli yap
            if (/^#[0-9a-fA-F]{3}$/.test(value)) {
                const r = value.charAt(1);
                const g = value.charAt(2);
                const b = value.charAt(3);
                return '#' + r + r + g + g + b + b;
            }
            
            // RGB/RGBA değerlerini parse et
            if (value.includes('rgb')) {
                const matches = value.match(/(\d+)/g);
                if (matches && matches.length >= 3) {
                    const r = Math.min(255, parseInt(matches[0]));
                    const g = Math.min(255, parseInt(matches[1]));
                    const b = Math.min(255, parseInt(matches[2]));
                    
                    // padStart yerine manuel padding (eski tarayıcı uyumluluğu)
                    const toHex = (num) => {
                        const hex = num.toString(16);
                        return hex.length === 1 ? '0' + hex : hex;
                    };
                    
                    return '#' + toHex(r) + toHex(g) + toHex(b);
                }
            }
            
            return value;
        }
        
        // Tüm input'ları kontrol et
        function validateAllInputs() {
            validateAllColorInputs();
            validateNumericInputs();
        }
          // Hex renk doğrulama
        function isValidHexColor(hex) {
            return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
        }
        
        // Debug: Header Preview Toggle Test
        $(document).ready(function() {
            console.log('🔍 Header Preview Toggle Debug Başlatıldı');
            
            // Buton kontrolü
            const headerBtn = $('#toggleHeaderPreview');
            const mobileBtn = $('#toggleMobileHeaderPreview');
            
            console.log('📋 Buton kontrolü:', {
                headerBtn: headerBtn.length,
                mobileBtn: mobileBtn.length,
                themeEditor: typeof window.themeEditorInstance
            });
              // Manuel test event'leri - KALDIRILDI (Çakışma yaratıyordu)
            /*
            $('#toggleHeaderPreview').on('click', function(e) {
                e.preventDefault();
                console.log('🖱️ MANUEL: Desktop header buton tıklandı');
                
                if (window.themeEditorInstance) {
                    window.themeEditorInstance.toggleHeaderPreview('desktop');
                } else {
                    console.error('❌ themeEditorInstance bulunamadı!');
                }
            });
            
            $('#toggleMobileHeaderPreview').on('click', function(e) {
                e.preventDefault();
                console.log('🖱️ MANUEL: Mobile header buton tıklandı');
                
                if (window.themeEditorInstance) {
                    window.themeEditorInstance.toggleHeaderPreview('mobile');
                } else {
                    console.error('❌ themeEditorInstance bulunamadı!');
                }
            });
            */
              // 2 saniye sonra instance kontrolü
            setTimeout(() => {
                console.log('⏰ Gecikmeli instance kontrolü:', {
                    themeEditorInstance: typeof window.themeEditorInstance,
                    methods: window.themeEditorInstance ? Object.getOwnPropertyNames(Object.getPrototypeOf(window.themeEditorInstance)) : 'N/A'
                });
            }, 2000);
            
            // BASIT TEST FONKSİYONU
            window.testHeaderPin = function() {
                console.log('🧪 TEST: Header pin test başlatıldı');
                const $card = $('#headerPreviewCard');
                
                console.log('📋 Test card durumu:', {
                    exists: $card.length,
                    visible: $card.is(':visible'),
                    classes: $card.attr('class')
                });
                
                // Manuel olarak fixed class ekle
                $card.addClass('header-preview-fixed');
                $('body').addClass('header-preview-pinned');
                
                console.log('✅ Manuel fixed class eklendi');
                
                setTimeout(() => {
                    console.log('⏰ 3 saniye sonra durum:', {
                        hasFixedClass: $card.hasClass('header-preview-fixed'),
                        position: $card.css('position'),
                        top: $card.css('top'),
                        zIndex: $card.css('z-index')
                    });
                }, 3000);
            };
              console.log('🧪 Test fonksiyonu hazır: window.testHeaderPin()');
        });
          // ==========================================
        // TEMA TAB MODÜL SİSTEMİ - GLOBAL FUNCTIONS
        // ==========================================
        
        // Global Theme Functions (Modernized)
        window.exportCurrentTheme = function() {
            if (typeof exportCurrentTheme === 'function') {
                exportCurrentTheme();
            } else {
                console.log('📤 Export theme function not yet loaded');
            }
        };
        
        window.importThemeFile = function() {
            if (typeof importThemeFromFile === 'function') {
                document.getElementById('import-theme-file').click();
            } else {
                console.log('📥 Import theme function not yet loaded');
            }
        };
        
        window.applyPredefinedTheme = function(themeName) {
            if (typeof applyPredefinedTheme === 'function') {
                applyPredefinedTheme(themeName);
            } else {
                console.log('🎨 Apply theme function not yet loaded for:', themeName);
            }
        };
        
    </script>
<!-- Tema Onay Modalı -->
<div class="modal fade" id="themeApplyConfirmModal" tabindex="-1" aria-labelledby="themeApplyConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="themeApplyConfirmModalLabel">Tema Uygulama Onayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Seçtiğiniz temayı uygulamak istediğinizden emin misiniz?</p>
                <p class="text-danger"><strong>Mevcut ayarlarınız bu tema ile değişecektir.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="confirmThemeApplyBtn">Uygula</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
