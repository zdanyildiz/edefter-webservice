<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
// Refactoring test dahil et
include_once __DIR__ . '/test-refactoring.php';
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
include_once ROOT . '/_y/s/s/tasarim/ThemeUtils.php';

// Tema yapılandırma fonksiyonları
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
            CSS . "index.css",
            CSS . "index-theme.css"
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

function resolveVariables($customCSS) {
    $resolved = $customCSS;
    $changed = true;

    while ($changed) {
        $changed = false;
        foreach ($resolved as $key => $value) {
            if (strpos($value, 'var(--') !== false) {
                preg_match_all('/var\(--([^)]+)\)/', $value, $matches);
                foreach ($matches[1] as $index => $varName) {
                    if (isset($resolved[$varName])) {
                        $value = str_replace($matches[0][$index], $resolved[$varName], $value);
                        $changed = true;
                    }
                }
                $resolved[$key] = $value;
            }
        }    }
    
    return $resolved;
}

// Hazır tema şablonları
function getPredefinedThemes() {
    return [
        'default' => [
            'name' => 'Varsayılan (Google Tema)',
            'description' => 'Modern, temiz ve profesyonel görünüm',
            'primary-color' => '#4285f4',
            'accent-color' => '#fbbc05',
            'success-color' => '#34a853',
            'danger-color' => '#ea4335'
        ],
        'dark' => [
            'name' => 'Koyu Tema',
            'description' => 'Göz yormayan koyu renk paleti',
            'primary-color' => '#8ab4f8',
            'accent-color' => '#fdd663',
            'body-bg-color' => '#202124',
            'content-bg-color' => '#292a2d'
        ],
        'blue-turquoise' => [
            'name' => 'Mavi-Turkuaz',
            'description' => 'Deniz esintili renk harmonisi',
            'primary-color' => '#0288d1',
            'accent-color' => '#26a69a'
        ],
        'green-nature' => [
            'name' => 'Yeşil-Doğal',
            'description' => 'Doğa dostu yeşil tonları',
            'primary-color' => '#43a047',
            'accent-color' => '#ffb300'
        ],
        'warm-orange' => [
            'name' => 'Sıcak Turuncu',
            'description' => 'Enerjik ve dinamik görünüm',
            'primary-color' => '#f57c00',
            'accent-color' => '#7e57c2'
        ],
        'purple-pink' => [
            'name' => 'Mor-Pembe',
            'description' => 'Feminen ve zarif renk paleti',
            'primary-color' => '#7b1fa2',
            'accent-color' => '#ec407a'
        ],
        'pastel' => [
            'name' => 'Pastel',
            'description' => 'Yumuşak ve rahatlatıcı renkler',
            'primary-color' => '#81c784',
            'accent-color' => '#ffcc80'
        ],
        'corporate' => [
            'name' => 'Kurumsal',
            'description' => 'Ciddi ve güvenilir kurumsal görünüm',
            'primary-color' => '#1565c0',
            'accent-color' => '#546e7a'
        ]
    ];
}

$customCSS = getCustomCSS($languageID);
if (!empty($customCSS)) {
    $customCSS = resolveVariables($customCSS);
      // Tüm değerleri güvenli hale getir
    foreach ($customCSS as $key => $value) {
        if (strpos($key, 'color') !== false || strpos($key, 'background') !== false || strpos($key, 'border-color') !== false) {
            $customCSS[$key] = sanitizeColorValue($value);
        } elseif (strpos($key, 'border-radius') !== false || strpos($key, 'spacing') !== false || strpos($key, 'font-size') !== false || strpos($key, 'width') !== false || strpos($key, 'height') !== false) {
            // Sayısal değerler için özel temizleme
            $customCSS[$key] = sanitizeNumericValue($value);
        } elseif (is_string($value) && preg_match('/\d+(px|rem|em|%)/', $value)) {
            // Diğer ölçü birimli değerler
            $customCSS[$key] = sanitizeNumericValue($value);
        }
    }
}

$predefinedThemes = getPredefinedThemes();
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
    <link type="text/css" rel="stylesheet" href="/Public/CSS/index-theme.css" />
    <link type="text/css" rel="stylesheet" href="/_y/s/s/tasarim/Theme/css/theme-editor.css" />
    
    <!-- Özel Tema CSS -->
    <style>
        /* Tema Önizleme Alanı */
        .theme-preview {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            min-height: 300px;
            background: var(--body-bg-color, #f8f9fa);
            position: relative;
            overflow: hidden;
        }
        
        .theme-preview iframe {
            width: 100%;
            height: 400px;
            border: none;
            border-radius: 6px;
        }
        
        /* Sekmeli Panel */
        .theme-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
        }
        
        .theme-tabs .nav-tabs {
            border-bottom: none;
        }
        
        .theme-tabs .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
        }
        
        .theme-tabs .nav-tabs .nav-link.active {
            color: #007bff;
            border-bottom-color: #007bff;
            background: none;
        }
        
        .theme-tabs .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #007bff;
        }
        
        /* Tab Sistemi için özel CSS */
        .tab-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            background: #fff;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block !important;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #fff;
            border-color: #ddd #ddd #fff;
            color: #495057;
        }
        
        /* Tema Kartları */
        .theme-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .theme-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0,123,255,0.1);
        }
        
        .theme-card.active {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .theme-preview-colors {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }
        
        .theme-preview-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        /* Form Grupları */
        .form-group-enhanced {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .form-group-enhanced .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        /* Slider Grupları */
        .slider-group {
            margin-bottom: 20px;
        }
        
        .slider-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .slider-value {
            background: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }
          /* Renk Seçici Grupları - Çakışmayı önle */
        .color-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .color-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .color-input-group label {
            flex: 1;
            font-weight: 500;
            text-align: right;
        }
        
        .color-input-group .form-control[type="color"] {
            flex: 0 0 80px;
            height: 40px;
            padding: 2px;
            border: 2px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
        }
        
        /* Bootstrap colorpicker devre dışı bırak */
        .colorpicker {
            display: none !important;
        }
        
        .colorpicker-element {
            display: none !important;
        }
        
        /* Buton Grupları */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-theme-save {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            font-weight: 600;
        }
        
        .btn-theme-preview {
            background: linear-gradient(45deg, #007bff, #6610f2);
            border: none;
            color: white;
            font-weight: 600;
        }
        
        .btn-theme-reset {
            background: linear-gradient(45deg, #dc3545, #fd7e14);
            border: none;
            color: white;
            font-weight: 600;
        }
        
        /* Responsive Önizleme */
        .responsive-preview {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .responsive-preview button {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
        }
        
        .responsive-preview button.active {
            background: #007bff;
            color: white;
        }
        
        /* Yardım Tooltipleri */
        .help-tooltip {
            display: inline-block;
            margin-left: 5px;
            color: #6c757d;
            cursor: help;
        }
        
        /* Tema İçe/Dışa Aktarma */
        .theme-import-export {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
        }
          .theme-json-editor {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            min-height: 200px;
        }
        
        /* Sınır Önizleme */
        .border-preview-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
        
        .border-preview-item {
            margin: 10px 0;
        }
        
        .preview-border {
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }
          .preview-border:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);        }
        
        /* Sınır Önizleme Kutuları */
        .border-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        
        .border-preview-item {
            position: relative;
        }
        
        .border-preview-item .preview-border {
            transition: all 0.3s ease;
            border: 1px solid #ddd;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.3;
            cursor: default;
            user-select: none;
        }
        
        .border-preview-item .preview-border:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .border-preview-item .preview-border small {
            display: block;
            font-size: 9px;
            opacity: 0.8;
            margin-top: 2px;
        }
        
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
            /* Sınır ve Köşe Değişkenleri */
            --border-width: <?=$customCSS['border-width'] ?? '1'?>px;
            --border-color: <?=sanitizeColorValue($customCSS['border-color'] ?? '#dadce0')?>;
            --border-style: <?=$customCSS['border-style'] ?? 'solid'?>;
            --border-radius-base: <?=$customCSS['border-radius-base'] ?? '8'?>px;
            --card-border-radius: <?=$customCSS['card-border-radius'] ?? '12'?>px;
            --button-border-radius: <?=$customCSS['button-border-radius'] ?? '6'?>px;
            --input-border-radius: <?=$customCSS['input-border-radius'] ?? '4'?>px;
            --border-light-color: <?=sanitizeColorValue($customCSS['border-light-color'] ?? '#e9ecef')?>;
            --content-bg-color: <?=sanitizeColorValue($customCSS['content-bg-color'] ?? '#ffffff')?>;
            --primary-color: <?=sanitizeColorValue($customCSS['primary-color'] ?? '#4285f4')?>;
            --danger-color: <?=sanitizeColorValue($customCSS['danger-color'] ?? '#ea4335')?>;
            
            /* Üst İletişim & Sosyal Medya Değişkenleri */
            --top-contact-and-social-bg-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-bg-color'] ?? '#f8f9fa')?>;
            --top-contact-and-social-link-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-color'] ?? '#5f6368')?>;
            --top-contact-and-social-link-hover-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-hover-color'] ?? '#4285f4')?>;
            --top-contact-and-social-icon-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-color'] ?? '#5f6368')?>;
            --top-contact-and-social-icon-hover-color: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-hover-color'] ?? '#4285f4')?>;
            --top-contact-and-social-container-margin-top: <?=$customCSS['top-contact-and-social-container-margin-top'] ?? '0'?>px;
            
            /* Üst İletişim & Sosyal Medya Mobile Değişkenleri */
            --top-contact-and-social-bg-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-bg-color-mobile'] ?? $customCSS['top-contact-and-social-bg-color'] ?? '#f8f9fa')?>;
            --top-contact-and-social-link-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-color-mobile'] ?? $customCSS['top-contact-and-social-link-color'] ?? '#5f6368')?>;
            --top-contact-and-social-link-hover-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-link-hover-color-mobile'] ?? $customCSS['top-contact-and-social-link-hover-color'] ?? '#4285f4')?>;
            --top-contact-and-social-icon-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-color-mobile'] ?? $customCSS['top-contact-and-social-icon-color'] ?? '#5f6368')?>;
            --top-contact-and-social-icon-hover-color-mobile: <?=sanitizeColorValue($customCSS['top-contact-and-social-icon-hover-color-mobile'] ?? $customCSS['top-contact-and-social-icon-hover-color'] ?? '#4285f4')?>;
            --top-contact-and-social-container-mobile-margin-top: <?=$customCSS['top-contact-and-social-container-mobile-margin-top'] ?? '80'?>px;
            
            /* Header Değişkenleri */
            --header-bg-color: <?=sanitizeColorValue($customCSS['header-bg-color'] ?? '#ffffff')?>;
            --header-border-width: <?=$customCSS['header-border-width'] ?? '1'?>px;
            --header-border-color: <?=sanitizeColorValue($customCSS['header-border-color'] ?? '#e9ecef')?>;
            --header-padding: <?=$customCSS['header-padding'] ?? '15'?>px;
            --header-min-height: <?=$customCSS['header-min-height'] ?? '80'?>px;
            --header-logo-width: <?=$customCSS['header-logo-width'] ?? '150'?>px;
            
            /* Header Mobile Değişkenleri */
            --header-mobile-bg-color: <?=sanitizeColorValue($customCSS['header-mobile-bg-color'] ?? $customCSS['header-bg-color'] ?? '#ffffff')?>;
            --header-mobile-border-width: <?=$customCSS['header-mobile-border-width'] ?? $customCSS['header-border-width'] ?? '1'?>px;
            --header-mobile-border-color: <?=sanitizeColorValue($customCSS['header-mobile-border-color'] ?? $customCSS['header-border-color'] ?? '#e9ecef')?>;
            --header-mobile-padding: <?=$customCSS['header-mobile-padding'] ?? $customCSS['header-padding'] ?? '15'?>px;
            --header-mobile-min-height: <?=$customCSS['header-mobile-min-height'] ?? '60'?>px;
            --header-mobile-logo-width: <?=$customCSS['header-mobile-logo-width'] ?? '100'?>px;
            
            /* Alışveriş İkon Renkleri */
            --shop-menu-container-icon-color-search: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-search'] ?? '#333333')?>;
            --shop-menu-container-icon-color-member: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-member'] ?? '#333333')?>;
            --shop-menu-container-icon-color-favorites: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-favorites'] ?? '#333333')?>;
            --shop-menu-container-icon-color-basket: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-basket'] ?? '#333333')?>;
            --shop-menu-container-icon-hover-color: <?=sanitizeColorValue($customCSS['shop-menu-container-icon-hover-color'] ?? '#4285f4')?>;
            
            /* Logo Margin Değişkenleri (Desktop) */
            --header-logo-margin-top: <?=$customCSS['header-logo-margin-top'] ?? '0'?>px;
            --header-logo-margin-right: <?=$customCSS['header-logo-margin-right'] ?? '0'?>px;
            --header-logo-margin-bottom: <?=$customCSS['header-logo-margin-bottom'] ?? '0'?>px;
            --header-logo-margin-left: <?=$customCSS['header-logo-margin-left'] ?? '0'?>px;
            
            /* Logo Margin Değişkenleri (Mobile) */
            --header-mobile-logo-margin-top: <?=$customCSS['header-mobile-logo-margin-top'] ?? '0'?>px;
            --header-mobile-logo-margin-right: <?=$customCSS['header-mobile-logo-margin-right'] ?? '0'?>px;
            --header-mobile-logo-margin-bottom: <?=$customCSS['header-mobile-logo-margin-bottom'] ?? '0'?>px;
            --header-mobile-logo-margin-left: <?=$customCSS['header-mobile-logo-margin-left'] ?? '0'?>px;
            
            /* Menü Değişkenleri */
            --menu-bg-color: <?=sanitizeColorValue($customCSS['menu-bg-color'] ?? '#ffffff')?>;
            --menu-text-color: <?=sanitizeColorValue($customCSS['menu-text-color'] ?? '#333333')?>;
            --menu-hover-color: <?=sanitizeColorValue($customCSS['menu-hover-color'] ?? '#4285f4')?>;
            --menu-hover-bg-color: <?=sanitizeColorValue($customCSS['menu-hover-bg-color'] ?? '#f8f9ff')?>;
            --menu-active-color: <?=sanitizeColorValue($customCSS['menu-active-color'] ?? '#4285f4')?>;
            --menu-active-bg-color: <?=sanitizeColorValue($customCSS['menu-active-bg-color'] ?? '#e3f2fd')?>;
            --menu-font-size: <?=$customCSS['menu-font-size'] ?? '16'?>px;
            --menu-height: <?=$customCSS['menu-height'] ?? '50'?>px;
            --menu-padding: <?=$customCSS['menu-padding'] ?? '15'?>px;
            
            /* Dropdown Menü Değişkenleri */
            --dropdown-bg-color: <?=sanitizeColorValue($customCSS['dropdown-bg-color'] ?? '#ffffff')?>;
            --dropdown-text-color: <?=sanitizeColorValue($customCSS['dropdown-text-color'] ?? '#333333')?>;
            --dropdown-hover-bg-color: <?=sanitizeColorValue($customCSS['dropdown-hover-bg-color'] ?? '#f8f9fa')?>;
            --dropdown-border-color: <?=sanitizeColorValue($customCSS['dropdown-border-color'] ?? '#e9ecef')?>;
            --dropdown-min-width: <?=$customCSS['dropdown-min-width'] ?? '200'?>px;
            --dropdown-padding: <?=$customCSS['dropdown-padding'] ?? '10'?>px;
            --dropdown-border-radius: <?=$customCSS['dropdown-border-radius'] ?? '6'?>px;
            
            /* Mobile Menü Değişkenleri */
            --mobile-menu-bg-color: <?=sanitizeColorValue($customCSS['mobile-menu-bg-color'] ?? '#ffffff')?>;
            --mobile-toggle-color: <?=sanitizeColorValue($customCSS['mobile-toggle-color'] ?? '#333333')?>;
            --mobile-toggle-size: <?=$customCSS['mobile-toggle-size'] ?? '24'?>px;
            --mobile-overlay-color: <?=sanitizeColorValue($customCSS['mobile-overlay-color'] ?? 'rgba(0,0,0,0.5)')?>;
              /* Metin Renkleri */
            --text-primary-color: <?=sanitizeColorValue($customCSS['text-primary-color'] ?? '#202124')?>;
            --text-secondary-color: <?=sanitizeColorValue($customCSS['text-secondary-color'] ?? '#5f6368')?>;
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
            color: var(--top-contact-and-social-link-hover-color-mobile, var(--top-contact-and-social-link-hover-color, #4285f4)) !important;
        }
        
        #dualTopContactPreview i.fa:hover,
        #dualMobileHeaderPreview i.fa:hover {
            color: var(--top-contact-and-social-icon-hover-color-mobile, var(--top-contact-and-social-icon-hover-color, #4285f4)) !important;
        }
        
        /* Alışveriş İkonları Hover Efektleri */        #headerPreviewContent .fa-search:hover,
        #headerPreviewContent .fa-user:hover,
        #headerPreviewContent .fa-heart:hover,
        #headerPreviewContent .fa-shopping-cart:hover {
            color: var(--shop-menu-container-icon-hover-color, #4285f4) !important;
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
        }/* Sabitlenmiş preview için body padding */
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
                            </li>                            <li class="nav-item" role="presentation">
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
                                <button class="nav-link" id="banners-tab" data-toggle="tab" data-target="#banners-panel" type="button" role="tab">
                                    <i class="fa fa-image"></i> Banner & İçerik
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="forms-tab" data-toggle="tab" data-target="#forms-panel" type="button" role="tab">
                                    <i class="fa fa-edit"></i> Form & Butonlar
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="responsive-tab" data-toggle="tab" data-target="#responsive-panel" type="button" role="tab">
                                    <i class="fa fa-mobile"></i> Responsive
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="footer-tab" data-toggle="tab" data-target="#footer-panel" type="button" role="tab">
                                    <i class="fa fa-window-minimize"></i> Footer & Diğer
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
                        
                        <!-- Banners Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/banners.php'; ?>
                        
                        <!-- Forms Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/forms.php'; ?>
                        
                        <!-- Responsive Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/responsive.php'; ?>
                          <!-- Footer Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/footer.php'; ?>
                        
                        <!-- Themes Sekmesi -->
                        <?php include __DIR__ . '/Theme/tabs/themes.php'; ?>
                    </div>                </form>
                
                <!-- Tema Kaydetme Butonları -->
                <div class="card">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-palette"></i> Temel Renkler</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="color-group">
                                                <div class="color-input-group">
                                                    <label>Ana Renk (Primary)</label>
                                                    <input type="color" name="primary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['primary-color'] ?? '#4285f4')?>" data-fallback="#4285f4">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>Ana Renk Açık Ton</label>
                                                    <input type="color" name="primary-light-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['primary-light-color'] ?? '#74a9ff')?>">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>Ana Renk Koyu Ton</label>
                                                    <input type="color" name="primary-dark-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['primary-dark-color'] ?? '#0d5bdd')?>">
                                                </div><div class="color-input-group">
                                                    <label>İkincil Renk (Secondary)</label>
                                                    <input type="color" name="secondary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['secondary-color'] ?? '#cccccc')?>">
                                                </div>                                                <div class="color-input-group">
                                                    <label>Vurgu Rengi (Accent)</label>
                                                    <input type="color" name="accent-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['accent-color'] ?? '#fbbc05')?>">
                                                </div>
                                            </div>
                                            <div class="row"> <hr></div>
                                            <div class="color-group">
                                                <div class="row"><div class="color-input-group col-md-3">
                                                        <label>Başarı Rengi</label>
                                                        <input type="color" name="success-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['success-color'] ?? '#34a853')?>">
                                                    </div>
                                                    <div class="color-input-group col-md-3">
                                                        <label>Uyarı Rengi</label>
                                                        <input type="color" name="warning-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['warning-color'] ?? '#ffab40')?>">
                                                    </div>
                                                    <div class="color-input-group col-md-3">
                                                        <label>Hata Rengi</label>
                                                        <input type="color" name="danger-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['danger-color'] ?? '#ea4335')?>">
                                                    </div> </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-font"></i> Metin Renkleri</h4>
                                        </div>
                                        <div class="card-body">
                                             <div class="color-group">
                                                <div class="color-input-group">
                                                    <label>Ana Metin Rengi</label>
                                                    <input type="color" name="text-primary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-primary-color'] ?? '#202124')?>">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>İkincil Metin Rengi</label>
                                                    <input type="color" name="text-secondary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-secondary-color'] ?? '#5f6368')?>">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>Soluk Metin Rengi</label>
                                                    <input type="color" name="text-muted-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-muted-color'] ?? '#9aa0a6')?>">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>Bağlantı Rengi</label>
                                                    <input type="color" name="link-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['link-color'] ?? '#1a73e8')?>">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>Bağlantı Hover Rengi</label>
                                                    <input type="color" name="link-hover-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['link-hover-color'] ?? '#174ea6')?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-square"></i> Arka Plan Renkleri</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="color-group">
                                                <div class="color-input-group">
                                                    <label>Site Arka Planı</label>
                                                    <input type="color" name="body-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['body-bg-color'] ?? '#f8f9fa')?>">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>İçerik Arka Planı</label>
                                                    <input type="color" name="content-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['content-bg-color'] ?? '#ffffff')?>" data-fallback="#ffffff">
                                                </div>

                                                <div class="color-input-group">
                                                    <label>Ana (Vurgulu) Arka Plan</label>
                                                    <input type="color" name="background-primary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['background-primary-color'] ?? '#ffffff')?>">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>İkincil (Alt Bilgi) Arka Plan</label>
                                                    <input type="color" name="background-secondary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['background-secondary-color'] ?? '#f8f9fa')?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-eye"></i> Renk Önizleme</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="theme-preview" id="colorPreview">
                                                <div style="padding: 20px;">
                                                    <h3 style="color: var(--primary-color);">Ana Başlık</h3>
                                                    <p style="color: var(--text-primary-color);">Bu bir örnek metin paragrafıdır.</p>
                                                    <p style="color: var(--text-secondary-color);">Bu da ikincil metin örneğidir.</p>
                                                    <a href="#" style="color: var(--link-color);">Örnek bağlantı</a>
                                                    <div style="margin-top: 15px;">
                                                        <button style="background: var(--primary-color); color: white; border: none; padding: 8px 16px; border-radius: 4px;">Ana Buton</button>
                                                        <button style="background: var(--accent-color); color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-left: 10px;">Vurgu Buton</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-border-style"></i> Sınır & Köşe Ayarları</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Sınır Rengi</label>
                                                        <input type="color" name="border-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['border-color'] ?? '#dadce0')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>İkincil Sınır Rengi</label>
                                                        <input type="color" name="border-light-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['border-light-color'] ?? '#e9ecef')?>">
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Sınır Stili</label>
                                                        <select name="border-style" class="form-control">
                                                            <option value="solid" <?=($customCSS['border-style'] ?? 'solid') == 'solid' ? 'selected' : ''?>>Düz (Solid)</option>
                                                            <option value="dashed" <?=($customCSS['border-style'] ?? 'solid') == 'dashed' ? 'selected' : ''?>>Kesikli (Dashed)</option>
                                                            <option value="dotted" <?=($customCSS['border-style'] ?? 'solid') == 'dotted' ? 'selected' : ''?>>Noktalı (Dotted)</option>
                                                            <option value="double" <?=($customCSS['border-style'] ?? 'solid') == 'double' ? 'selected' : ''?>>Çift Çizgi (Double)</option>
                                                            <option value="groove" <?=($customCSS['border-style'] ?? 'solid') == 'groove' ? 'selected' : ''?>>Oyuklu (Groove)</option>
                                                            <option value="ridge" <?=($customCSS['border-style'] ?? 'solid') == 'ridge' ? 'selected' : ''?>>Çıkıntılı (Ridge)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Sınır Genişliği (px)</label>
                                                        <input type="number" name="border-width" class="form-control" min="0" max="10" value="<?=sanitizeNumericValue($customCSS['border-width'] ?? '1', 'px', 1)?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Köşe Yuvarlaklığı (px)</label>
                                                        <input type="number" name="border-radius-base" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['border-radius-base'] ?? '8', 'px', 8)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Kart Köşe Yuvarlaklığı (px)</label>
                                                        <input type="number" name="card-border-radius" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['card-border-radius'] ?? '12', 'px', 12)?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Input Köşe Yuvarlaklığı (px)</label>
                                                        <input type="number" name="input-border-radius" class="form-control" min="0" max="25" value="<?=sanitizeNumericValue($customCSS['input-border-radius'] ?? '4', 'px', 4)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Buton Köşe Yuvarlaklığı (px)</label>
                                                        <input type="number" name="button-border-radius" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['button-border-radius'] ?? '6', 'px', 6)?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">

                                    <!-- Sınır Önizleme -->
                                    <div class="mt-3">
                                        <h6><i class="fa fa-eye"></i> Sınır & Köşe Önizleme</h6>
                                        <div class="border-preview-container">
                                            <!-- Genel Sınır (border-radius-base) -->
                                            <div class="border-preview-item" id="borderPreviewGeneral">
                                                <div class="preview-border" style="
                                                        border: var(--border-width, 1px) var(--border-style, solid) var(--border-color, #dadce0);
                                                        border-radius: var(--border-radius-base, 8px);
                                                        padding: 15px;
                                                        margin: 8px;
                                                        background: var(--content-bg-color, #ffffff);
                                                        color: var(--text-primary-color, #333);
                                                        text-align: center;
                                                        font-size: 12px;
                                                    ">
                                                    Genel Köşe<br>
                                                    <small>(border-radius-base)</small>
                                                </div>
                                            </div>

                                            <!-- Kart Sınırı (card-border-radius) -->
                                            <div class="border-preview-item" id="borderPreviewCard">
                                                <div class="preview-border" style="
                                                        border: var(--border-width, 1px) var(--border-style, solid) var(--border-light-color, #e9ecef);
                                                        border-radius: var(--card-border-radius, 12px);
                                                        padding: 15px;
                                                        margin: 8px;
                                                        background: var(--content-bg-color, #ffffff);
                                                        color: var(--text-primary-color, #333);
                                                        text-align: center;
                                                        font-size: 12px;
                                                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                    ">
                                                    Kart Köşesi<br>
                                                    <small>(card-border-radius)</small>
                                                </div>
                                            </div>

                                            <!-- Input Sınırı (input-border-radius) -->
                                            <div class="border-preview-item" id="borderPreviewInput">
                                                <div class="preview-border" style="
                                                        border: var(--border-width, 1px) var(--border-style, solid) var(--border-color, #dadce0);
                                                        border-radius: var(--input-border-radius, 4px);
                                                        padding: 8px 12px;
                                                        margin: 8px;
                                                        background: var(--content-bg-color, #ffffff);
                                                        color: var(--text-secondary-color, #666);
                                                        text-align: center;
                                                        font-size: 12px;
                                                        min-height: 35px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                    ">
                                                    Input Köşesi<br>
                                                    <small>(input-border-radius)</small>
                                                </div>
                                            </div>

                                            <!-- Buton Sınırı (button-border-radius) -->
                                            <div class="border-preview-item" id="borderPreviewButton">
                                                <div class="preview-border" style="
                                                        border: var(--border-width, 1px) var(--border-style, solid) var(--primary-color, #4285f4);
                                                        border-radius: var(--button-border-radius, 6px);
                                                        padding: 10px 15px;
                                                        margin: 8px;
                                                        background: var(--primary-color, #4285f4);
                                                        color: white;
                                                        text-align: center;
                                                        font-size: 12px;
                                                        font-weight: bold;
                                                        cursor: pointer;
                                                        transition: all 0.3s ease;
                                                    ">
                                                    Buton Köşesi<br>
                                                    <small>(button-border-radius)</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sınır Stili Karşılaştırma -->                                        <div class="mt-3">
                                            <small class="text-muted">💡 Sınır stili tüm örneklere uygulanır, her biri farklı köşe yuvarlaklığını gösterir</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <!-- Header Önizleme -->                                    
                                     <div class="card" id="headerPreviewCard">                                        
                                        <div class="card-header">
                                            <h4>
                                                <i class="fa fa-eye"></i> Header Önizleme
                                                <div class="float-right">
                                                    <button type="button" class="btn btn-sm btn-outline-info mr-2" id="openDualPreview" title="Desktop ve Mobile önizlemeyi yan yana göster" onclick="return false;">
                                                        <i class="fa fa-columns"></i> Yan Yana
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="toggleHeaderPreview" title="Header önizlemeyi sayfanın üstüne sabitle/kaldır" onclick="return false;">
                                                        <i class="fa fa-expand" id="headerPreviewToggleIcon"></i>
                                                    </button>
                                                </div>
                                            </h4>
                                        </div>                                        <div class="card-body">
                                            <div class="theme-preview" id="headerPreview">                                                <!-- Üst İletişim Çubuğu -->
                                                <div id="topContactPreview" style="
                                                    background: var(--top-contact-and-social-bg-color, #f8f9fa);
                                                    padding: 6px 16px;
                                                    border-bottom: 1px solid var(--border-light-color, #e9ecef);
                                                    display: flex;
                                                    justify-content: space-between;
                                                    align-items: center;
                                                    font-size: 12px;
                                                    min-height: 32px;
                                                    max-height: 40px;
                                                ">
                                                    <div style="display: flex; gap: 15px; align-items: center;">
                                                        <span style="color: var(--top-contact-and-social-link-color, #5f6368); display: flex; align-items: center; gap: 5px;">
                                                            <i class="fa fa-phone" style="color: var(--top-contact-and-social-icon-color, #5f6368); font-size: 11px;"></i>
                                                            <span>0212 555 0000</span>
                                                        </span>
                                                        <span style="color: var(--top-contact-and-social-link-color, #5f6368); display: flex; align-items: center; gap: 5px;">
                                                            <i class="fa fa-envelope" style="color: var(--top-contact-and-social-icon-color, #5f6368); font-size: 11px;"></i>
                                                            <span>info@example.com</span>
                                                        </span>
                                                    </div>
                                                    <div style="display: flex; gap: 8px; align-items: center;">
                                                        <i class="fa fa-facebook" style="color: var(--top-contact-and-social-icon-color, #5f6368); cursor: pointer; font-size: 11px;"></i>
                                                        <i class="fa fa-twitter" style="color: var(--top-contact-and-social-icon-color, #5f6368); cursor: pointer; font-size: 11px;"></i>
                                                        <i class="fa fa-instagram" style="color: var(--top-contact-and-social-icon-color, #5f6368); cursor: pointer; font-size: 11px;"></i>
                                                    </div></div>
                                                
                                                <!-- Ana Header Alanı -->
                                                <div id="headerPreviewContent" style="
                                                    background: var(--header-bg-color, #ffffff);
                                                    border-bottom: var(--header-border-width, 1px) solid var(--header-border-color, #e9ecef);
                                                    padding: var(--header-padding, 15px);
                                                    min-height: var(--header-min-height, 80px);
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: space-between;
                                                ">
                                                    <div style="
                                                        width: var(--header-logo-width, 150px);
                                                        height: 40px;
                                                        background: var(--primary-color, #4285f4);
                                                        border-radius: 4px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        color: white;
                                                        font-weight: bold;
                                                        margin: var(--header-logo-margin-top, 0) var(--header-logo-margin-right, 0) var(--header-logo-margin-bottom, 0) var(--header-logo-margin-left, 0);
                                                    ">LOGO</div>
                                                    
                                                    <div style="flex: 1; text-align: center;">
                                                        <input type="text" placeholder="Ürün arayın..." style="
                                                            width: 70%;
                                                            padding: 8px 12px;
                                                            border: 1px solid var(--border-color, #dadce0);
                                                            border-radius: 4px;
                                                        ">
                                                    </div>
                                                    
                                                    <div style="display: flex; gap: 15px;">
                                                        <i class="fa fa-search" style="color: var(--shop-menu-container-icon-color-search, #333333); font-size: 18px; cursor: pointer;"></i>
                                                        <i class="fa fa-user" style="color: var(--shop-menu-container-icon-color-member, #333333); font-size: 18px; cursor: pointer;"></i>
                                                        <i class="fa fa-heart" style="color: var(--shop-menu-container-icon-color-favorites, #333333); font-size: 18px; cursor: pointer;"></i>
                                                        <i class="fa fa-shopping-cart" style="color: var(--shop-menu-container-icon-color-basket, #333333); font-size: 18px; cursor: pointer; position: relative;">
                                                            <span style="
                                                                position: absolute;
                                                                top: -8px;
                                                                right: -8px;
                                                                background: var(--danger-color, #ea4335);
                                                                color: white;
                                                                border-radius: 50%;
                                                                width: 16px;
                                                                height: 16px;
                                                                font-size: 10px;
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: center;
                                                            ">3</span>
                                                        </i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-md-6">                           
                                    <!-- Mobile Header Önizleme -->
                                    <div class="card" id="mobileHeaderPreviewCard">                                        
                                        <div class="card-header">
                                            <h4>
                                                <i class="fa fa-mobile"></i> Mobile Header Önizleme
                                                <div class="float-right">
                                                    <button type="button" class="btn btn-sm btn-outline-info mr-2" id="openDualPreviewFromMobile" title="Desktop ve Mobile önizlemeyi yan yana göster" onclick="return false;">
                                                        <i class="fa fa-columns"></i> Yan Yana
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success" id="toggleMobileHeaderPreview" title="Mobile Header önizlemeyi sayfanın üstüne sabitle/kaldır" onclick="return false;">
                                                        <i class="fa fa-expand" id="mobileHeaderPreviewToggleIcon"></i>
                                                    </button>
                                                </div>
                                            </h4>
                                        </div>                                        <div class="card-body">                                              <div class="theme-preview" id="mobileHeaderPreview">                                                <!-- Mobil Üst İletişim Alanı -->
                                                <div style="
                                                    background: var(--top-contact-and-social-bg-color-mobile, var(--top-contact-and-social-bg-color, #f8f9fa));
                                                    padding: 4px 15px;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: space-between;
                                                    font-size: 10px;
                                                    border-bottom: 1px solid rgba(0,0,0,0.1);
                                                    min-height: 28px;
                                                    max-height: 32px;
                                                ">
                                                    <div style="
                                                        display: flex;
                                                        align-items: center;
                                                        gap: 6px;
                                                        color: var(--top-contact-and-social-link-color-mobile, var(--top-contact-and-social-link-color, #5f6368));
                                                    ">
                                                        <i class="fa fa-phone" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                                                        <span>0212 XXX XX XX</span>
                                                    </div>
                                                    <div style="
                                                        display: flex;
                                                        align-items: center;
                                                        gap: 5px;
                                                        color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368));
                                                    ">                                                        <i class="fa fa-facebook" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                                                        <i class="fa fa-instagram" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                                                        <i class="fa fa-whatsapp" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                                                    </div>
                                                </div>                                                
                                                <!-- Ana Header Alanı -->
                                                <div style="
                                                    background: var(--header-mobile-bg-color, #ffffff);
                                                    border-bottom: var(--header-mobile-border-width, 1px) solid var(--header-mobile-border-color, #e9ecef);
                                                    padding: var(--header-mobile-padding, 15px);
                                                    min-height: var(--header-mobile-min-height, 60px);
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: space-between;
                                                ">
                                                    <!-- Menü -->
                                                    <div style="
                                                        width: 24px;
                                                        height: 24px;
                                                        background: var(--text-primary-color, #333333);
                                                        border-radius: 2px;
                                                        position: relative;
                                                        cursor: pointer;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                    ">
                                                        <div style="
                                                            color: white;
                                                            font-size: 12px;
                                                            line-height: 1;
                                                        ">☰</div>                                                    </div>
                                                    
                                                    <!-- Logo -->
                                                    <div style="
                                                        width: var(--header-mobile-logo-width, 100px);
                                                        height: 30px;
                                                        background: var(--primary-color, #4285f4);
                                                        border-radius: 4px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        color: white;
                                                        font-size: 12px;
                                                        font-weight: bold;
                                                        margin: var(--header-mobile-logo-margin-top, 0) var(--header-mobile-logo-margin-right, 0) var(--header-mobile-logo-margin-bottom, 0) var(--header-mobile-logo-margin-left, 0);
                                                    ">LOGO</div>
                                                      <!-- Eylem İkonları -->
                                                    <div style="
                                                        display: flex;
                                                        align-items: center;
                                                        gap: var(--mobile-action-icon-gap, 12px);
                                                    ">
                                                        <!-- Telefon -->
                                                        <div style="
                                                            width: var(--mobile-action-icon-size, 32px);
                                                            height: var(--mobile-action-icon-size, 32px);
                                                            background: var(--mobile-action-icon-phone-bg-color, #28a745);
                                                            border-radius: 50%;
                                                            display: flex;
                                                            align-items: center;
                                                            justify-content: center;
                                                            cursor: pointer;
                                                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                        ">
                                                            <i class="fa fa-phone" style="color: white; font-size: 12px;"></i>                                                        </div>
                                                        
                                                        <!-- WhatsApp -->
                                                        <div style="
                                                            width: var(--mobile-action-icon-size, 32px);
                                                            height: var(--mobile-action-icon-size, 32px);
                                                            background: var(--mobile-action-icon-whatsapp-bg-color, #25d366);
                                                            border-radius: 50%;
                                                            display: flex;
                                                            align-items: center;
                                                            justify-content: center;
                                                            cursor: pointer;
                                                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                        ">
                                                            <i class="fa fa-whatsapp" style="color: white; font-size: 12px;"></i>                                                        </div>
                                                          <!-- Sepet -->
                                                        <div style="
                                                            width: var(--mobile-action-icon-size, 32px);
                                                            height: var(--mobile-action-icon-size, 32px);
                                                            background: var(--mobile-action-icon-basket-bg-color, #4285f4);
                                                            border-radius: 50%;
                                                            display: flex;
                                                            align-items: center;
                                                            justify-content: center;
                                                            cursor: pointer;
                                                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                            position: relative;
                                                        ">                                                            <i class="fa fa-shopping-cart" style="color: white; font-size: 11px;"></i>
                                                            <!-- Sayaç -->
                                                            <div style="
                                                                position: absolute;
                                                                top: -4px;
                                                                right: -4px;
                                                                width: 16px;
                                                                height: 16px;
                                                                background: var(--mobile-action-icon-basket-counter-bg-color, #dc3545);
                                                                border-radius: 50%;
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: center;
                                                                color: white;
                                                                font-size: 8px;
                                                                font-weight: bold;
                                                            ">3</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                    
                                    </div>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Üst İletişim ve Sosyal Medya -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-phone"></i> Üst İletişim & Sosyal Medya</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="col-md-6">
                                                <h5><i class="fa fa-desktop"></i> Desktop Ayarları</h5>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Arkaplan Rengi</label>
                                                            <input type="color" name="top-contact-and-social-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-bg-color'] ?? '#f8f9fa')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Link Rengi</label>
                                                            <input type="color" name="top-contact-and-social-link-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-link-color'] ?? '#5f6368')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Link Hover Rengi</label>
                                                            <input type="color" name="top-contact-and-social-link-hover-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-link-hover-color'] ?? '#4285f4')?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>İkon Rengi</label>
                                                            <input type="color" name="top-contact-and-social-icon-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-icon-color'] ?? '#5f6368')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>İkon Hover Rengi</label>
                                                            <input type="color" name="top-contact-and-social-icon-hover-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-icon-hover-color'] ?? '#4285f4')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Üst Boşluk (px)</label>
                                                            <input type="number" name="top-contact-and-social-container-margin-top" class="form-control" min="0" max="100" value="<?=sanitizeNumericValue($customCSS['top-contact-and-social-container-margin-top'] ?? '0', 'px', 0)?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5><i class="fa fa-mobile"></i> Mobile Ayarları</h5>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Mobil Arkaplan Rengi</label>
                                                            <input type="color" name="top-contact-and-social-bg-color-mobile" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-bg-color-mobile'] ?? $customCSS['top-contact-and-social-bg-color'] ?? '#f8f9fa')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Mobil Link Rengi</label>
                                                            <input type="color" name="top-contact-and-social-link-color-mobile" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-link-color-mobile'] ?? $customCSS['top-contact-and-social-link-color'] ?? '#5f6368')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Mobil Link Hover Rengi</label>
                                                            <input type="color" name="top-contact-and-social-link-hover-color-mobile" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-link-hover-color-mobile'] ?? $customCSS['top-contact-and-social-link-hover-color'] ?? '#4285f4')?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Mobil İkon Rengi</label>
                                                            <input type="color" name="top-contact-and-social-icon-color-mobile" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-icon-color-mobile'] ?? $customCSS['top-contact-and-social-icon-color'] ?? '#5f6368')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Mobil İkon Hover Rengi</label>
                                                            <input type="color" name="top-contact-and-social-icon-hover-color-mobile" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['top-contact-and-social-icon-hover-color-mobile'] ?? $customCSS['top-contact-and-social-icon-hover-color'] ?? '#4285f4')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Mobil Üst Boşluk (px)</label>
                                                            <input type="number" name="top-contact-and-social-container-mobile-margin-top" class="form-control" min="0" max="150" value="<?=sanitizeNumericValue($customCSS['top-contact-and-social-container-mobile-margin-top'] ?? '80', 'px', 80)?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <!-- Alışveriş İkonları -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-shopping-cart"></i> Alışveriş İkonları</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-desktop"></i> Desktop Alışveriş İkonları</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Arama İkonu Rengi</label>
                                                                <input type="color" name="shop-menu-container-icon-color-search" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-search'] ?? '#333333')?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Üye İkonu Rengi</label>
                                                                <input type="color" name="shop-menu-container-icon-color-member" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-member'] ?? '#333333')?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Favoriler İkonu Rengi</label>
                                                                <input type="color" name="shop-menu-container-icon-color-favorites" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-favorites'] ?? '#333333')?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Sepet İkonu Rengi</label>
                                                                <input type="color" name="shop-menu-container-icon-color-basket" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['shop-menu-container-icon-color-basket'] ?? '#333333')?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Desktop İkonlar Hover Rengi</label>
                                                        <input type="color" name="shop-menu-container-icon-hover-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['shop-menu-container-icon-hover-color'] ?? '#4285f4')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-mobile"></i> Mobile Alışveriş İkonları</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Telefon İkonu Arkaplan</label>
                                                                <input type="color" name="mobile-action-icon-phone-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-action-icon-phone-bg-color'] ?? '#28a745')?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>WhatsApp İkonu Arkaplan</label>
                                                                <input type="color" name="mobile-action-icon-whatsapp-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-action-icon-whatsapp-bg-color'] ?? '#25d366')?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Sepet İkonu Arkaplan</label>
                                                                <input type="color" name="mobile-action-icon-basket-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-action-icon-basket-bg-color'] ?? '#4285f4')?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Sepet Sayacı Arkaplan</label>
                                                                <input type="color" name="mobile-action-icon-basket-counter-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-action-icon-basket-counter-bg-color'] ?? '#dc3545')?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Mobile İkon Boyutu (px)</label>
                                                                <input type="number" name="mobile-action-icon-size" class="form-control" min="24" max="48" value="<?=sanitizeNumericValue($customCSS['mobile-action-icon-size'] ?? '32', 'px', 32)?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Mobile İkonlar Arası Boşluk (px)</label>
                                                                <input type="number" name="mobile-action-icon-gap" class="form-control" min="8" max="20" value="<?=sanitizeNumericValue($customCSS['mobile-action-icon-gap'] ?? '12', 'px', 12)?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Logo Detay Ayarları -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-image"></i> Logo Detay Ayarları</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-desktop"></i> Desktop Logo</h5>
                                                    <div class="form-group">
                                                        <label>Logo Genişliği (px)</label>
                                                        <input type="number" name="header-logo-width" class="form-control" min="80" max="300" value="<?=sanitizeNumericValue($customCSS['header-logo-width'] ?? '150', 'px', 150)?>">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Üst Margin (px)</label>
                                                                <input type="number" name="header-logo-margin-top" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-logo-margin-top'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Sağ Margin (px)</label>
                                                                <input type="number" name="header-logo-margin-right" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-logo-margin-right'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Alt Margin (px)</label>
                                                                <input type="number" name="header-logo-margin-bottom" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-logo-margin-bottom'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Sol Margin (px)</label>
                                                                <input type="number" name="header-logo-margin-left" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-logo-margin-left'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5><i class="fa fa-mobile"></i> Mobile Logo</h5>
                                                    <div class="form-group">
                                                        <label>Mobil Logo Genişliği (px)</label>
                                                        <input type="number" name="header-mobile-logo-width" class="form-control" min="60" max="200" value="<?=sanitizeNumericValue($customCSS['header-mobile-logo-width'] ?? '100', 'px', 100)?>">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Mobil Üst Margin (px)</label>
                                                                <input type="number" name="header-mobile-logo-margin-top" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-mobile-logo-margin-top'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Mobil Sağ Margin (px)</label>
                                                                <input type="number" name="header-mobile-logo-margin-right" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-mobile-logo-margin-right'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Mobil Alt Margin (px)</label>
                                                                <input type="number" name="header-mobile-logo-margin-bottom" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-mobile-logo-margin-bottom'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Mobil Sol Margin (px)</label>
                                                                <input type="number" name="header-mobile-logo-margin-left" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-mobile-logo-margin-left'] ?? '0', 'px', 0)?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Header Ana Görünüm -->
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4><i class="fa fa-window-maximize"></i> Header Ana Görünüm</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Header Arka Plan Rengi</label>
                                                            <input type="color" name="header-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['header-bg-color'] ?? '#ffffff')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Header Yüksekliği (px)</label>
                                                            <input type="number" name="header-min-height" class="form-control" min="50" max="150" value="<?=sanitizeNumericValue($customCSS['header-min-height'] ?? '80', 'px', 80)?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Header Padding (px)</label>
                                                            <input type="number" name="header-padding" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-padding'] ?? '15', 'px', 15)?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Header Sınır Genişliği (px)</label>
                                                            <input type="number" name="header-border-width" class="form-control" min="0" max="10" value="<?=sanitizeNumericValue($customCSS['header-border-width'] ?? '1', 'px', 1)?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Header Sınır Rengi</label>
                                                    <input type="color" name="header-border-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['header-border-color'] ?? '#e9ecef')?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Header Mobile Ayarları -->
                                     <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4><i class="fa fa-mobile"></i> Header Mobile Görünüm</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Header Mobile Arka Plan Rengi</label>
                                                            <input type="color" name="header-mobile-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['header-mobile-bg-color'] ?? '#ffffff')?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Mobil Header Yüksekliği (px)</label>
                                                            <input type="number" name="header-mobile-min-height" class="form-control" min="40" max="100" value="<?=sanitizeNumericValue($customCSS['header-mobile-min-height'] ?? '60', 'px', 60)?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Header Mobil Padding (px)</label>
                                                            <input type="number" name="header-mobile-padding" class="form-control" min="0" max="50" value="<?=sanitizeNumericValue($customCSS['header-mobile-padding'] ?? '15', 'px', 15)?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Header Mobile Sınır Genişliği (px)</label>
                                                            <input type="number" name="header-mobile-border-width" class="form-control" min="0" max="10" value="<?=sanitizeNumericValue($customCSS['header-mobile-border-width'] ?? '1', 'px', 1)?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Header Mobile Sınır Rengi</label>
                                                    <input type="color" name="header-mobile-border-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['header-mobile-border-color'] ?? '#e9ecef')?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hızlı Header Temaları -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-magic"></i> Hızlı Header Temaları</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="theme-card" onclick="applyHeaderTheme('modern')">
                                                        <h6>Modern</h6>
                                                        <div class="theme-preview-colors">
                                                            <div class="theme-preview-color" style="background: #ffffff;"></div>
                                                            <div class="theme-preview-color" style="background: #4285f4;"></div>
                                                            <div class="theme-preview-color" style="background: #333333;"></div>
                                                        </div>
                                                        <small>Temiz ve minimal tasarım</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="theme-card" onclick="applyHeaderTheme('dark')">
                                                        <h6>Koyu Tema</h6>
                                                        <div class="theme-preview-colors">
                                                            <div class="theme-preview-color" style="background: #212529;"></div>
                                                            <div class="theme-preview-color" style="background: #6c757d;"></div>
                                                            <div class="theme-preview-color" style="background: #ffffff;"></div>
                                                        </div>
                                                        <small>Koyu ve şık görünüm</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="theme-card" onclick="applyHeaderTheme('corporate')">
                                                        <h6>Kurumsal</h6>
                                                        <div class="theme-preview-colors">
                                                            <div class="theme-preview-color" style="background: #1565c0;"></div>
                                                            <div class="theme-preview-color" style="background: #ffffff;"></div>
                                                            <div class="theme-preview-color" style="background: #546e7a;"></div>
                                                        </div>
                                                        <small>Profesyonel kurumsal</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="theme-card" onclick="applyHeaderTheme('gradient')">
                                                        <h6>Gradient</h6>
                                                        <div class="theme-preview-colors">
                                                            <div class="theme-preview-color" style="background: linear-gradient(45deg, #667eea, #764ba2);"></div>
                                                            <div class="theme-preview-color" style="background: #ffffff;"></div>
                                                            <div class="theme-preview-color" style="background: #f8f9fa;"></div>
                                                        </div>
                                                        <small>Renkli gradient efekt</small>
                                                    </div>
                                                </div>                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Desktop Menü Önizleme -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-desktop"></i> Desktop Menü Önizleme</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="theme-preview" id="desktopMenuPreview">
                                                <!-- Desktop Menü Header -->
                                                <div style="
                                                    background: var(--header-bg-color, #ffffff);
                                                    border-bottom: var(--header-border-width, 1px) solid var(--header-border-color, #e9ecef);
                                                    padding: var(--header-padding, 15px);
                                                    min-height: var(--header-min-height, 80px);
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: space-between;
                                                    margin-bottom: 10px;
                                                ">
                                                    <div style="
                                                        width: var(--header-logo-width, 150px);
                                                        height: 40px;
                                                        background: var(--primary-color, #4285f4);
                                                        border-radius: 4px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        color: white;
                                                        font-weight: bold;
                                                        font-size: 12px;
                                                    ">LOGO</div>
                                                    <div style="display: flex; gap: 20px;">                                                        <span style="
                                                            color: var(--menu-text-color, #333333);
                                                            font-size: var(--menu-font-size, 16px);
                                                            padding: var(--menu-padding, 8px) 16px;
                                                            cursor: pointer;
                                                            transition: all 0.3s ease;
                                                            border-radius: 4px;
                                                        " onmouseover="
                                                            this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--menu-hover-color') || '#4285f4';
                                                            this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--menu-hover-bg-color') || '#f8f9ff';
                                                        " onmouseout="
                                                            this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--menu-text-color') || '#333333';
                                                            this.style.background = 'transparent';
                                                        ">Ana Sayfa</span>
                                                        <span style="
                                                            color: var(--menu-active-color, #4285f4);
                                                            font-size: var(--menu-font-size, 16px);
                                                            padding: var(--menu-padding, 8px) 16px;
                                                            background: var(--menu-active-bg-color, #e3f2fd);
                                                            border-radius: 4px;
                                                            cursor: pointer;
                                                        ">Ürünler ▼</span>                                                        <span style="
                                                            color: var(--menu-text-color, #333333);
                                                            font-size: var(--menu-font-size, 16px);
                                                            padding: var(--menu-padding, 8px) 16px;
                                                            cursor: pointer;
                                                            transition: all 0.3s ease;
                                                            border-radius: 4px;
                                                        " onmouseover="
                                                            this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--menu-hover-color') || '#4285f4';
                                                            this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--menu-hover-bg-color') || '#f8f9ff';
                                                        " onmouseout="
                                                            this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--menu-text-color') || '#333333';
                                                            this.style.background = 'transparent';
                                                        ">Hakkımızda</span>
                                                        <span style="
                                                            color: var(--menu-text-color, #333333);
                                                            font-size: var(--menu-font-size, 16px);
                                                            padding: var(--menu-padding, 8px) 16px;
                                                            cursor: pointer;
                                                            transition: all 0.3s ease;
                                                            border-radius: 4px;
                                                        " onmouseover="
                                                            this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--menu-hover-color') || '#4285f4';
                                                            this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--menu-hover-bg-color') || '#f8f9ff';
                                                        " onmouseout="
                                                            this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--menu-text-color') || '#333333';
                                                            this.style.background = 'transparent';
                                                        ">İletişim</span>
                                                    </div>
                                                </div>
                                                <!-- Dropdown Menü Örneği -->
                                                <div style="
                                                    background: var(--dropdown-bg-color, #ffffff);
                                                    border: 1px solid var(--dropdown-border-color, #e9ecef);
                                                    border-radius: var(--dropdown-border-radius, 6px);
                                                    padding: var(--dropdown-padding, 10px);
                                                    min-width: var(--dropdown-min-width, 200px);
                                                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                                                    margin-left: 200px;
                                                ">                                                    <div style="
                                                        color: var(--dropdown-text-color, #333333);
                                                        padding: 8px 12px;
                                                        cursor: pointer;
                                                        border-radius: 4px;
                                                        transition: all 0.3s ease;
                                                        font-size: 14px;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-hover-bg-color') || '#f8f9fa';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-hover-text-color') || '#333333';
                                                    " onmouseout="
                                                        this.style.background = 'transparent';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-text-color') || '#333333';
                                                    ">Kategori 1</div>
                                                    <div style="
                                                        color: var(--dropdown-text-color, #333333);
                                                        padding: 8px 12px;
                                                        cursor: pointer;
                                                        border-radius: 4px;
                                                        transition: all 0.3s ease;
                                                        font-size: 14px;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-hover-bg-color') || '#f8f9fa';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-hover-text-color') || '#333333';
                                                    " onmouseout="
                                                        this.style.background = 'transparent';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-text-color') || '#333333';
                                                    ">Kategori 2</div>
                                                    <div style="
                                                        color: var(--dropdown-text-color, #333333);
                                                        padding: 8px 12px;
                                                        cursor: pointer;
                                                        border-radius: 4px;
                                                        transition: all 0.3s ease;
                                                        font-size: 14px;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-hover-bg-color') || '#f8f9fa';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-hover-text-color') || '#333333';
                                                    " onmouseout="
                                                        this.style.background = 'transparent';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--dropdown-text-color') || '#333333';
                                                    ">Kategori 3</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Mobile Menü Önizleme -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-mobile"></i> Mobile Menü Önizleme</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="theme-preview" id="mobileMenuPreview">
                                                <!-- Mobile Header -->
                                                <div style="
                                                    background: var(--header-mobile-bg-color, var(--header-bg-color, #ffffff));
                                                    border-bottom: var(--header-mobile-border-width, var(--header-border-width, 1px)) solid var(--header-mobile-border-color, var(--header-border-color, #e9ecef));
                                                    padding: var(--header-mobile-padding, var(--header-padding, 15px));
                                                    min-height: var(--header-mobile-min-height, 60px);
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: space-between;
                                                    margin-bottom: 10px;
                                                ">
                                                    <!-- Mobile Toggle -->
                                                    <div style="
                                                        width: var(--mobile-toggle-size, 24px);
                                                        height: var(--mobile-toggle-size, 24px);
                                                        background: var(--mobile-toggle-color, #333333);
                                                        border-radius: 3px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        cursor: pointer;
                                                    ">
                                                        <div style="
                                                            color: white;
                                                            font-size: 10px;
                                                            line-height: 1;
                                                        ">☰</div>
                                                    </div>
                                                    
                                                    <!-- Mobile Logo -->
                                                    <div style="
                                                        width: var(--header-mobile-logo-width, 100px);
                                                        height: 30px;
                                                        background: var(--primary-color, #4285f4);
                                                        border-radius: 4px;
                                                        display: flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        color: white;
                                                        font-size: 10px;
                                                        font-weight: bold;
                                                    ">LOGO</div>
                                                    
                                                    <!-- Mobile Actions -->
                                                    <div style="display: flex; gap: 8px;">
                                                        <div style="width: 24px; height: 24px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fa fa-phone" style="color: white; font-size: 10px;"></i>
                                                        </div>
                                                        <div style="width: 24px; height: 24px; background: #4285f4; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fa fa-shopping-cart" style="color: white; font-size: 10px;"></i>
                                                        </div>
                                                    </div>
                                                </div>                                                <!-- Mobile Menü Paneli -->
                                                <div style="
                                                    background: var(--mobile-menu-bg-color, #ffffff);
                                                    border: 1px solid var(--mobile-dropdown-border-color, var(--dropdown-border-color, #e9ecef));
                                                    border-radius: var(--mobile-dropdown-border-radius, var(--dropdown-border-radius, 6px));
                                                    overflow: hidden;
                                                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                                                ">                                                    <div style="
                                                        color: var(--mobile-menu-text-color, #333333);
                                                        padding: var(--mobile-menu-padding, 12px) 16px;
                                                        border-bottom: 1px solid var(--mobile-dropdown-border-color, var(--dropdown-border-color, #e9ecef));
                                                        cursor: pointer;
                                                        transition: all 0.3s ease;
                                                        font-size: var(--mobile-menu-font-size, 16px);
                                                        min-height: var(--mobile-menu-height, 50px);
                                                        display: flex;
                                                        align-items: center;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-hover-bg-color') || '#f8f9ff';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-hover-color') || '#4285f4';
                                                    " onmouseout="
                                                        this.style.background = 'transparent';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-text-color') || '#333333';
                                                    ">📱 Ana Sayfa</div>
                                                    <div style="
                                                        color: var(--mobile-menu-active-color, #4285f4);
                                                        background: var(--mobile-menu-active-bg-color, #e3f2fd);
                                                        padding: var(--mobile-menu-padding, 12px) 16px;
                                                        border-bottom: 1px solid var(--mobile-dropdown-border-color, var(--dropdown-border-color, #e9ecef));
                                                        cursor: pointer;
                                                        font-size: var(--mobile-menu-font-size, 16px);
                                                        min-height: var(--mobile-menu-height, 50px);
                                                        display: flex;
                                                        align-items: center;
                                                    ">🛍️ Ürünler ▼</div>
                                                    <div style="
                                                        color: var(--mobile-dropdown-text-color, #333333);
                                                        background: var(--mobile-dropdown-bg-color, #ffffff);
                                                        padding: var(--mobile-dropdown-padding, 8px) var(--mobile-dropdown-indent, 32px);
                                                        border-bottom: 1px solid var(--mobile-dropdown-border-color, var(--dropdown-border-color, #e9ecef));
                                                        cursor: pointer;
                                                        transition: all 0.3s ease;
                                                        font-size: calc(var(--mobile-menu-font-size, 16px) - 2px);
                                                        border-left: 3px solid var(--primary-color, #4285f4);                                                        min-height: calc(var(--mobile-menu-height, 50px) * 0.8);
                                                        display: flex;
                                                        align-items: center;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-hover-bg-color') || '#f8f9fa';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-hover-text-color') || '#333333';
                                                    " onmouseout="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-bg-color') || '#ffffff';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-text-color') || '#333333';
                                                    ">→ Kategori 1</div>
                                                    <div style="
                                                        color: var(--mobile-dropdown-text-color, #333333);
                                                        background: var(--mobile-dropdown-bg-color, #ffffff);
                                                        padding: var(--mobile-dropdown-padding, 8px) var(--mobile-dropdown-indent, 32px);
                                                        border-bottom: 1px solid var(--mobile-dropdown-border-color, var(--dropdown-border-color, #e9ecef));
                                                        cursor: pointer;
                                                        transition: all 0.3s ease;
                                                        font-size: calc(var(--mobile-menu-font-size, 16px) - 2px);
                                                        border-left: 3px solid var(--primary-color, #4285f4);
                                                        min-height: calc(var(--mobile-menu-height, 50px) * 0.8);
                                                        display: flex;
                                                        align-items: center;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-hover-bg-color') || '#f8f9fa';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-hover-text-color') || '#333333';
                                                    " onmouseout="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-bg-color') || '#ffffff';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-dropdown-text-color') || '#333333';
                                                    ">→ Kategori 2</div>                                                    <div style="
                                                        color: var(--mobile-menu-text-color, #333333);
                                                        padding: var(--mobile-menu-padding, 12px) 16px;
                                                        border-bottom: 1px solid var(--mobile-dropdown-border-color, var(--dropdown-border-color, #e9ecef));
                                                        cursor: pointer;
                                                        transition: all 0.3s ease;
                                                        font-size: var(--mobile-menu-font-size, 16px);
                                                        min-height: var(--mobile-menu-height, 50px);
                                                        display: flex;
                                                        align-items: center;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-hover-bg-color') || '#f8f9ff';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-hover-color') || '#4285f4';
                                                    " onmouseout="
                                                        this.style.background = 'transparent';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-text-color') || '#333333';
                                                    ">ℹ️ Hakkımızda</div>
                                                    <div style="
                                                        color: var(--mobile-menu-text-color, #333333);
                                                        padding: var(--mobile-menu-padding, 12px) 16px;
                                                        cursor: pointer;
                                                        transition: all 0.3s ease;
                                                        font-size: var(--mobile-menu-font-size, 16px);
                                                        min-height: var(--mobile-menu-height, 50px);
                                                        display: flex;
                                                        align-items: center;
                                                    " onmouseover="
                                                        this.style.background = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-hover-bg-color') || '#f8f9ff';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-hover-color') || '#4285f4';
                                                    " onmouseout="
                                                        this.style.background = 'transparent';
                                                        this.style.color = getComputedStyle(document.documentElement).getPropertyValue('--mobile-menu-text-color') || '#333333';
                                                    ">📧 İletişim</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Menü Görünüm Ayarları -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-list"></i> Ana Menü Görünümü</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Menü Arka Plan Rengi</label>
                                                        <input type="color" name="menu-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['menu-bg-color'] ?? '#ffffff')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Menü Metin Rengi</label>
                                                        <input type="color" name="menu-text-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['menu-text-color'] ?? '#333333')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Menü Hover Rengi</label>
                                                        <input type="color" name="menu-hover-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['menu-hover-color'] ?? '#4285f4')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Menü Hover Arka Plan</label>
                                                        <input type="color" name="menu-hover-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['menu-hover-bg-color'] ?? '#f8f9ff')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Aktif Menü Rengi</label>
                                                        <input type="color" name="menu-active-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['menu-active-color'] ?? '#4285f4')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Aktif Menü Arka Plan</label>
                                                        <input type="color" name="menu-active-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['menu-active-bg-color'] ?? '#e3f2fd')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Menü Yazı Boyutu (px)</label>
                                                        <input type="number" name="menu-font-size" class="form-control" min="10" max="24" value="<?=sanitizeNumericValue($customCSS['menu-font-size'] ?? '16', 'px', 16)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Menü Yüksekliği (px)</label>
                                                        <input type="number" name="menu-height" class="form-control" min="30" max="80" value="<?=sanitizeNumericValue($customCSS['menu-height'] ?? '50', 'px', 50)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Menü Padding (px)</label>
                                                        <input type="number" name="menu-padding" class="form-control" min="5" max="30" value="<?=sanitizeNumericValue($customCSS['menu-padding'] ?? '15', 'px', 15)?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alt Menü (Dropdown) Ayarları -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-caret-down"></i> Alt Menü (Dropdown) Ayarları</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dropdown Arka Plan</label>
                                                        <input type="color" name="dropdown-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['dropdown-bg-color'] ?? '#ffffff')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dropdown Metin Rengi</label>
                                                        <input type="color" name="dropdown-text-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['dropdown-text-color'] ?? '#333333')?>">
                                                    </div>
                                                </div>
                                            </div>                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dropdown Hover Arka Plan</label>
                                                        <input type="color" name="dropdown-hover-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['dropdown-hover-bg-color'] ?? '#f8f9fa')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dropdown Hover Metin Rengi</label>
                                                        <input type="color" name="dropdown-hover-text-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['dropdown-hover-text-color'] ?? '#333333')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dropdown Sınır Rengi</label>
                                                        <input type="color" name="dropdown-border-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['dropdown-border-color'] ?? '#e9ecef')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- Boş alan -->
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Dropdown Genişliği (px)</label>
                                                        <input type="number" name="dropdown-min-width" class="form-control" min="150" max="400" value="<?=sanitizeNumericValue($customCSS['dropdown-min-width'] ?? '200', 'px', 200)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Dropdown Padding (px)</label>
                                                        <input type="number" name="dropdown-padding" class="form-control" min="5" max="25" value="<?=sanitizeNumericValue($customCSS['dropdown-padding'] ?? '10', 'px', 10)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Dropdown Köşe Yuvarlaklığı (px)</label>
                                                        <input type="number" name="dropdown-border-radius" class="form-control" min="0" max="20" value="<?=sanitizeNumericValue($customCSS['dropdown-border-radius'] ?? '6', 'px', 6)?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                <div class="col-md-6">
                                    <!-- Mobile Menü Ayarları -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-mobile"></i> Mobile Menü Ayarları</h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Mobile Menü Görünüm -->
                                            <h5><i class="fa fa-list"></i> Mobile Ana Menü</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Arka Plan</label>
                                                        <input type="color" name="mobile-menu-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-menu-bg-color'] ?? '#ffffff')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Metin Rengi</label>
                                                        <input type="color" name="mobile-menu-text-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-menu-text-color'] ?? '#333333')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Hover Rengi</label>
                                                        <input type="color" name="mobile-menu-hover-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-menu-hover-color'] ?? '#4285f4')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Hover Arka Plan</label>
                                                        <input type="color" name="mobile-menu-hover-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-menu-hover-bg-color'] ?? '#f8f9ff')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Aktif Menü Rengi</label>
                                                        <input type="color" name="mobile-menu-active-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-menu-active-color'] ?? '#4285f4')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Aktif Menü Arka Plan</label>
                                                        <input type="color" name="mobile-menu-active-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-menu-active-bg-color'] ?? '#e3f2fd')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Yazı Boyutu (px)</label>
                                                        <input type="number" name="mobile-menu-font-size" class="form-control" min="10" max="24" value="<?=sanitizeNumericValue($customCSS['mobile-menu-font-size'] ?? '16', 'px', 16)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Yüksekliği (px)</label>
                                                        <input type="number" name="mobile-menu-height" class="form-control" min="30" max="80" value="<?=sanitizeNumericValue($customCSS['mobile-menu-height'] ?? '50', 'px', 50)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Padding (px)</label>
                                                        <input type="number" name="mobile-menu-padding" class="form-control" min="5" max="30" value="<?=sanitizeNumericValue($customCSS['mobile-menu-padding'] ?? '12', 'px', 12)?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <!-- Mobile Alt Menü (Dropdown) -->
                                            <h5><i class="fa fa-caret-down"></i> Mobile Alt Menü</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Arka Plan</label>
                                                        <input type="color" name="mobile-dropdown-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-dropdown-bg-color'] ?? '#ffffff')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Metin Rengi</label>
                                                        <input type="color" name="mobile-dropdown-text-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-dropdown-text-color'] ?? '#333333')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Hover Arka Plan</label>
                                                        <input type="color" name="mobile-dropdown-hover-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-dropdown-hover-bg-color'] ?? '#f8f9fa')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Hover Metin Rengi</label>
                                                        <input type="color" name="mobile-dropdown-hover-text-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-dropdown-hover-text-color'] ?? '#333333')?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Sınır Rengi</label>
                                                        <input type="color" name="mobile-dropdown-border-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-dropdown-border-color'] ?? '#e9ecef')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Padding (px)</label>
                                                        <input type="number" name="mobile-dropdown-padding" class="form-control" min="5" max="25" value="<?=sanitizeNumericValue($customCSS['mobile-dropdown-padding'] ?? '8', 'px', 8)?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Köşe Yuvarlaklığı (px)</label>
                                                        <input type="number" name="mobile-dropdown-border-radius" class="form-control" min="0" max="20" value="<?=sanitizeNumericValue($customCSS['mobile-dropdown-border-radius'] ?? '6', 'px', 6)?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Dropdown Girintisi (px)</label>
                                                        <input type="number" name="mobile-dropdown-indent" class="form-control" min="10" max="50" value="<?=sanitizeNumericValue($customCSS['mobile-dropdown-indent'] ?? '32', 'px', 32)?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <!-- Mobile Toggle Ayarları -->
                                            <h5><i class="fa fa-bars"></i> Mobile Toggle</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Toggle Rengi</label>
                                                        <input type="color" name="mobile-toggle-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-toggle-color'] ?? '#333333')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Toggle Boyutu (px)</label>
                                                        <input type="number" name="mobile-toggle-size" class="form-control" min="16" max="32" value="<?=sanitizeNumericValue($customCSS['mobile-toggle-size'] ?? '24', 'px', 24)?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Overlay Rengi</label>
                                                        <input type="color" name="mobile-overlay-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['mobile-overlay-color'] ?? 'rgba(0,0,0,0.5)')?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile Menü Animasyon Süresi (ms)</label>
                                                        <input type="number" name="mobile-menu-animation-duration" class="form-control" min="100" max="800" value="<?=sanitizeNumericValue($customCSS['mobile-menu-animation-duration'] ?? '300', 'ms', 300)?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                </div>
                                
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-shopping-cart"></i> Ürün Kutu Görünümü</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Bu sekme yakında geliştirilecektir. Ürün kutularının tasarımı, renkleri ve düzeni burada yönetilebilecektir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-eye"></i> Ürün Önizleme</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="theme-preview">
                                                <p class="text-muted">Ürün kutu önizlemesi yakında eklenecektir.</p>
                                            </div>                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-image"></i> Banner Görünümü</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Bu sekme yakında geliştirilecektir. Banner stilleri, animasyonları ve içerik görünümü burada yönetilebilecektir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-eye"></i> Banner Önizleme</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="theme-preview">
                                                <p class="text-muted">Banner önizlemesi yakında eklenecektir.</p>                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-edit"></i> Form & Buton Görünümü</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Bu sekme yakında geliştirilecektir. Form stilleri, buton tasarımları ve input görünümleri burada yönetilebilecektir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-eye"></i> Form Önizleme</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="theme-preview">                                                <p class="text-muted">Form önizlemesi yakında eklenecektir.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-mobile"></i> Responsive Ayarlar</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Bu sekme yakında geliştirilecektir. Mobil ve tablet uyumluluğu, breakpoint'ler ve responsive davranışlar burada yönetilebilecektir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-eye"></i> Responsive Önizleme</h4>
                                        </div>
                                        <div class="card-body">                                            <div class="theme-preview">
                                                <p class="text-muted">Responsive önizleme yakında eklenecektir.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-window-minimize"></i> Footer Görünümü</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Bu sekme yakında geliştirilecektir. Footer tasarımı, alt menüler ve sayfa altı içerikleri burada yönetilebilecektir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4><i class="fa fa-eye"></i> Footer Önizleme</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="theme-preview">
                                                <p class="text-muted">Footer önizlemesi yakında eklenecektir.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hazır Temalar Sekmesi -->
                        <div class="tab-pane fade" id="themes-panel" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h4><i class="fa fa-magic"></i> Hazır Tema Şablonları</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($predefinedThemes as $themeKey => $theme): ?>  
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="theme-card" data-theme="<?=$themeKey?>" onclick="applyPredefinedTheme('<?=$themeKey?>')">
                                                <h5><?=$theme['name']?></h5>
                                                <p class="text-muted"><?=$theme['description']?></p>
                                                <div class="theme-preview-colors">
                                                    <div class="theme-preview-color" style="background: <?=$theme['primary-color']?>;"></div>
                                                    <?php if(isset($theme['accent-color'])): ?>
                                                    <div class="theme-preview-color" style="background: <?=$theme['accent-color']?>;"></div>
                                                    <?php endif; ?>
                                                    <?php if(isset($theme['success-color'])): ?>
                                                    <div class="theme-preview-color" style="background: <?=$theme['success-color']?>;"></div>
                                                    <?php endif; ?>
                                                    <?php if(isset($theme['danger-color'])): ?>
                                                    <div class="theme-preview-color" style="background: <?=$theme['danger-color']?>;"></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
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

    <!-- JavaScript -->    <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
    <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>    <script src="/_y/assets/js/core/source/App.js"></script>
    <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
    
    <!-- Gelişmiş Tema Düzenleyici JavaScript - Modüler Yapı -->
    <script src="/_y/s/s/tasarim/Theme/js/core.js"></script>
    <script src="/_y/s/s/tasarim/Theme/js/header.js"></script>
    <script src="/_y/s/s/tasarim/theme-editor.js"></script><script>
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
            
            // İlk sekmeyi aktif yap
            $('.nav-tabs .nav-link:first').addClass('active');
            $('.tab-content .tab-pane:first').addClass('active in');
            
            // Tab değişikliklerini yönet
            $('.nav-tabs .nav-link').click(function(e) {
                e.preventDefault();
                
                const target = $(this).attr('data-target');
                
                // Tüm sekmeleri pasif yap
                $('.nav-tabs .nav-link').removeClass('active');
                $('.tab-pane').removeClass('active in show');
                
                // Seçilen sekmeyi aktif yap
                $(this).addClass('active');
                $(target).addClass('active in');
                
                console.log('Tab switched to:', target);
            });
              // Form başlatma
            validateAllInputs();
            
            // ThemeEditor theme-editor.js tarafından başlatılacak
            // İlk yüklemede önizlemeyi güncelle
            if (typeof window.themeEditorInstance !== 'undefined') {
                setTimeout(() => {
                    window.themeEditorInstance.updatePreview();
                    console.log('🚀 İlk yüklemede önizleme güncellendi');
                }, 500);
            }
        });        // Renk input değerlerini kontrol et ve düzelt
        function validateColorInputs() {
            $('.color-picker').each(function() {
                const $input = $(this);
                let value = $input.val();
                
                // Boş değer kontrolü
                if (!value || value.trim() === '') {
                    value = '#ffffff';
                }
                
                // # işareti yoksa ekle
                if (value && !value.startsWith('#')) {
                    value = '#' + value;
                }
                
                // Geçersiz değerleri düzelt
                if (!isValidHexColor(value)) {
                    const fallbackColor = $input.data('fallback') || '#ffffff';
                    $input.val(fallbackColor);
                    console.log('Fixed invalid color value:', value, 'to', fallbackColor);
                } else if ($input.val() !== value) {
                    $input.val(value);
                    console.log('Formatted color value:', $input.val(), 'to', value);
                }
                
                // HTML5 color input için ek doğrulama
                try {
                    // Tarayıcının color input desteği varsa test et
                    const testInput = document.createElement('input');
                    testInput.type = 'color';
                    testInput.value = value;
                    
                    // Eğer değer ayarlanamadıysa fallback kullan
                    if (testInput.value !== value) {
                        const fallbackColor = $input.data('fallback') || '#ffffff';
                        $input.val(fallbackColor);
                        console.log('Browser rejected color value:', value, 'using fallback:', fallbackColor);
                    }
                } catch (e) {
                    console.log('Color input validation error:', e);
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
        }
        
        // Tüm input'ları kontrol et
        function validateAllInputs() {
            validateColorInputs();
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
        // TAB MODÜL JAVASCRIPT KODLARI - KONSOLIDE
        // ==========================================
        
        // Banner Tab JavaScript
        function initBannersTab() {
            // Opacity slider değerlerini güncelle
            $('input[name="banner-overlay-opacity"]').on('input', function() {
                $('#overlay-opacity-value').text($(this).val());
            });
            
            $('input[name="card-shadow-opacity"]').on('input', function() {
                $('#shadow-opacity-value').text($(this).val());
            });
        }
        
        // Forms Tab JavaScript  
        function initFormsTab() {
            // Form önizleme interaktif öğeler
            $('.preview-input, .preview-textarea').focus(function() {
                $('.error-message').hide();
                $('.success-message').hide();
            });
            
            $('.btn-primary-preview').click(function() {
                $('.error-message').hide();
                $('.success-message').show().delay(3000).fadeOut();
            });
            
            $('.btn-secondary-preview').click(function() {
                $('.success-message').hide();
                $('.error-message').show().delay(3000).fadeOut();
            });
            
            $('.btn-outline-preview').click(function() {
                $('.preview-input, .preview-textarea').val('');
                $('.error-message, .success-message').hide();
            });
        }
        
        // Responsive Tab JavaScript
        function initResponsiveTab() {
            // Responsive preview device switcher
            $('.responsive-preview-tabs .btn').click(function() {
                const device = $(this).data('device');
                
                // Button states
                $('.responsive-preview-tabs .btn').removeClass('active');
                $(this).addClass('active');
                
                // Frame visibility
                $('.preview-frame').removeClass('active');
                $(`.${device}-frame`).addClass('active');
            });
        }
        
        // Themes Tab JavaScript
        function initThemesTab() {
            // Tema kartlarına tıklama olayı
            $('.theme-card').click(function() {
                $('.theme-card').removeClass('active');
                $(this).addClass('active');
                
                const theme = $(this).data('theme');
                // updateThemePreview(theme); // Bu fonksiyon daha sonra eklenecek
            });
            
            // Tema uygulama butonları
            $('.apply-theme-btn').click(function(e) {
                e.stopPropagation();
                const themeName = $(this).data('theme');
                // applyPredefinedTheme(themeName); // Bu fonksiyon daha sonra eklenecek
                console.log('Theme apply:', themeName);
            });
            
            // İlk tema kartını aktif yap
            $('.theme-card:first').addClass('active');
        }
        
        // Tüm tab modüllerini başlat
        $(document).ready(function() {
            console.log('🔧 Tab modülleri başlatılıyor...');
            
            // 1 saniye gecikme ile tab modüllerini başlat (DOM hazır olmasını bekle)
            setTimeout(() => {
                initBannersTab();
                initFormsTab(); 
                initResponsiveTab();
                initThemesTab();
                console.log('✅ Tüm tab modülleri başlatıldı');
            }, 1000);
        });
        
        // Global Theme Functions (Placeholder)
        window.exportCurrentTheme = function() {
            console.log('📤 Export theme - yakında eklenecek');
        };
        
        window.importThemeFile = function() {
            console.log('📥 Import theme - yakında eklenecek');
        };
        
        window.applyPredefinedTheme = function(themeName) {
            console.log('🎨 Apply theme:', themeName, '- yakında eklenecek');
        };
        
    </script>
</body>
</html>
