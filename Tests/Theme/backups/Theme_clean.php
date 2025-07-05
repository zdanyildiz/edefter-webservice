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
