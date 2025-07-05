<?php
/**
 * Dinamik CSS Generator - Tema verilerinden CSS dosyası oluşturur
 * Bu dosya AdminDesignController.php tarafından çağrılır
 */

function generateDynamicCSS($themeData, $languageID) {
    $css = "/* Dinamik Tema CSS - Dil ID: {$languageID} */\n";
    $css .= "/* Oluşturulma Tarihi: " . date('Y-m-d H:i:s') . " */\n\n";
    
    // Tema verilerini temizle
    $cleanedThemeData = cleanThemeData($themeData);
    
    // CSS Custom Properties oluştur
    $css .= ":root {\n";
    
    foreach ($cleanedThemeData as $key => $value) {
        // Sadece tema değişkenlerini al
        if (!in_array($key, ['action', 'languageID'])) {
            // Değişken adını CSS formatına çevir
            $cssVar = '--' . $key;
            
            // Değeri temizle ve formatla
            $cssValue = trim($value);
            
            // Eğer değer var() referansı içeriyorsa, o şekilde bırak
            if (strpos($cssValue, 'var(') === false && strpos($cssValue, 'rgba(') === false) {
                // Renk değerlerini kontrol et
                if (preg_match('/^#[a-fA-F0-9]{6}$/', $cssValue) || 
                    preg_match('/^#[a-fA-F0-9]{3}$/', $cssValue)) {
                    // Geçerli hex renk
                } elseif (strpos($cssValue, 'px') === false && is_numeric(str_replace('px', '', $cssValue))) {
                    // Sayısal değer ise px ekle (eğer zaten yoksa)
                    if (!preg_match('/\d+px$/', $cssValue) && 
                        !preg_match('/\d+%$/', $cssValue) && 
                        !preg_match('/\d+em$/', $cssValue) && 
                        !preg_match('/\d+rem$/', $cssValue)) {
                        $cssValue .= 'px';
                    }
                }
            }
            
            $css .= "    {$cssVar}: {$cssValue};\n";
        }
    }
    
    $css .= "}\n\n";
    
    // Tema özel stilleri ekle
    $css .= generateThemeSpecificStyles($cleanedThemeData);
    
    return $css;
}

function cleanThemeData($themeData) {
    $cleanedData = [];
    
    foreach ($themeData as $key => $value) {
        if (in_array($key, ['action', 'languageID'])) {
            continue;
        }
        
        // Renk değerlerini temizle
        if (strpos($key, 'color') !== false || strpos($key, 'bg') !== false) {
            $cleanedData[$key] = sanitizeColorForCSS($value);
        } else {
            $cleanedData[$key] = $value;
        }
    }
    
    return $cleanedData;
}

function sanitizeColorForCSS($color) {
    if (empty($color)) {
        return '#ffffff';
    }
    
    // RGBA değerlerini hex'e çevir
    if (strpos($color, 'rgba(') === 0) {
        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([0-9.]+)\)/', $color, $matches);
        if (count($matches) >= 4) {
            $r = intval($matches[1]);
            $g = intval($matches[2]);
            $b = intval($matches[3]);
            $alpha = floatval($matches[4]);
            
            // Alpha değeri 1'den küçükse hex'e çeviremeyiz, rgba olarak bırak
            if ($alpha < 1) {
                return $color;
            } else {
                return sprintf('#%02x%02x%02x', $r, $g, $b);
            }
        }
        return '#cccccc';
    }
    
    // RGB değerlerini hex'e çevir
    if (strpos($color, 'rgb(') === 0) {
        preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $color, $matches);
        if (count($matches) >= 4) {
            $r = intval($matches[1]);
            $g = intval($matches[2]);
            $b = intval($matches[3]);
            return sprintf('#%02x%02x%02x', $r, $g, $b);
        }
        return '#cccccc';
    }
    
    // Geçerli hex renk kontrolü
    if (preg_match('/^#[a-fA-F0-9]{6}$/', $color) || preg_match('/^#[a-fA-F0-9]{3}$/', $color)) {
        return $color;
    }
    
    return '#ffffff';
}

function generateThemeSpecificStyles($themeData) {
    $css = "/* Tema Özel Stilleri */\n\n";
    
    // Responsive stilleri
    $css .= generateResponsiveStyles($themeData);
    
    // Component stilleri
    $css .= generateComponentStyles($themeData);
    
    // Hover efektleri
    $css .= generateHoverEffects($themeData);
    
    return $css;
}

function generateResponsiveStyles($themeData) {
    $css = "/* Responsive Stiller */\n";
    
    // Mobil stiller
    $css .= "@media (max-width: 768px) {\n";
    $css .= "    :root {\n";
    
    // Mobil için özel değişkenler varsa ekle
    if (isset($themeData['header-mobile-min-height'])) {
        $css .= "        --header-min-height: var(--header-mobile-min-height);\n";
    }
    
    if (isset($themeData['header-logo-mobile-width'])) {
        $css .= "        --header-logo-width: var(--header-logo-mobile-width);\n";
    }
    
    $css .= "    }\n";
    $css .= "}\n\n";
    
    // Tablet stiller
    $css .= "@media (min-width: 769px) and (max-width: 1024px) {\n";
    $css .= "    /* Tablet özel stiller */\n";
    $css .= "}\n\n";
    
    return $css;
}

function generateComponentStyles($themeData) {
    $css = "/* Component Stilleri */\n\n";
    
    // Header stiller
    $css .= ".header {\n";
    $css .= "    background-color: var(--header-bg-color, var(--content-bg-color));\n";
    $css .= "    min-height: var(--header-min-height, 80px);\n";
    $css .= "}\n\n";
    
    // Logo stiller
    $css .= ".header .logo img {\n";
    $css .= "    width: var(--header-logo-width, 150px);\n";
    $css .= "    margin: var(--header-logo-margin, 0);\n";
    $css .= "}\n\n";
    
    // Ana menü stiller
    $css .= ".main-menu {\n";
    $css .= "    background-color: var(--main-menu-bg-color, var(--content-bg-color));\n";
    $css .= "}\n\n";
    
    $css .= ".main-menu a {\n";
    $css .= "    color: var(--main-menu-link-color, var(--text-primary-color));\n";
    $css .= "    background-color: var(--main-menu-link-bg-color, transparent);\n";
    $css .= "    font-size: var(--font-size-main-menu, var(--font-size-normal));\n";
    $css .= "}\n\n";
    
    // Buton stiller
    $css .= ".btn-primary {\n";
    $css .= "    background-color: var(--button-color, var(--primary-color));\n";
    $css .= "    color: var(--button-text-color, var(--text-light-color));\n";
    $css .= "    border: none;\n";
    $css .= "    border-radius: var(--border-radius-base, 8px);\n";
    $css .= "}\n\n";
    
    // Form stiller
    $css .= ".form-control {\n";
    $css .= "    background-color: var(--input-bg-color, var(--content-bg-color));\n";
    $css .= "    color: var(--input-color, var(--text-primary-color));\n";
    $css .= "    border: var(--input-border, 1px solid var(--border-color));\n";
    $css .= "    border-radius: var(--border-radius-base, 8px);\n";
    $css .= "}\n\n";
    
    // Ürün kutuları
    $css .= ".product-box {\n";
    $css .= "    background-color: var(--homepage-product-box-bg-color, var(--content-bg-color));\n";
    $css .= "    color: var(--homepage-product-box-color, var(--text-primary-color));\n";
    $css .= "    border: 1px solid var(--border-color);\n";
    $css .= "    border-radius: var(--border-radius-base, 8px);\n";
    $css .= "    box-shadow: var(--box-shadow-base, 0 2px 10px rgba(0, 0, 0, 0.075));\n";
    $css .= "}\n\n";
    
    // Footer stiller
    $css .= ".footer {\n";
    $css .= "    background-color: var(--footer-bg-color, var(--background-secondary-color));\n";
    $css .= "    color: var(--footer-text-color, var(--text-secondary-color));\n";
    $css .= "}\n\n";
    
    return $css;
}

function generateHoverEffects($themeData) {
    $css = "/* Hover Efektleri */\n\n";
    
    // Ana menü hover
    $css .= ".main-menu a:hover {\n";
    $css .= "    color: var(--main-menu-link-hover-color, var(--primary-color));\n";
    $css .= "    background-color: var(--main-menu-link-hover-bg-color, var(--background-light-color));\n";
    $css .= "}\n\n";
    
    // Buton hover
    $css .= ".btn-primary:hover {\n";
    $css .= "    background-color: var(--button-hover-color, var(--primary-dark-color));\n";
    $css .= "    transform: translateY(-1px);\n";
    $css .= "}\n\n";
    
    // Ürün kutusu hover
    $css .= ".product-box:hover {\n";
    $css .= "    background-color: var(--homepage-product-box-hover-bg-color, var(--background-light-color));\n";
    $css .= "    transform: translateY(-2px);\n";
    $css .= "    box-shadow: var(--box-shadow-lg, 0 10px 25px rgba(0, 0, 0, 0.15));\n";
    $css .= "}\n\n";
    
    // Link hover
    $css .= "a:hover {\n";
    $css .= "    color: var(--link-hover-color, var(--primary-color));\n";
    $css .= "}\n\n";
    
    // Form focus
    $css .= ".form-control:focus {\n";
    $css .= "    border-color: var(--input-focus-border, var(--primary-color));\n";
    $css .= "    box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb, 66, 133, 244), 0.25);\n";
    $css .= "}\n\n";
    
    return $css;
}

function saveDynamicCSS($cssContent, $languageID, $isPreview = false) {
    $fileName = $isPreview ? "index-preview-{$languageID}.css" : "index-{$languageID}.css";
    $filePath = CSS . $fileName;
    
    // CSS dizini yoksa oluştur
    if (!file_exists(CSS)) {
        mkdir(CSS, 0755, true);
    }
    
    // CSS dosyasını kaydet
    return file_put_contents($filePath, $cssContent);
}

function generateColorVariations($color) {
    // Hex rengi RGB'ye çevir
    $color = ltrim($color, '#');
    $r = hexdec(substr($color, 0, 2));
    $g = hexdec(substr($color, 2, 2));
    $b = hexdec(substr($color, 4, 2));
    
    // Açık ton (20% daha açık)
    $lightR = min(255, $r + (255 - $r) * 0.2);
    $lightG = min(255, $g + (255 - $g) * 0.2);
    $lightB = min(255, $b + (255 - $b) * 0.2);
    
    // Koyu ton (20% daha koyu)
    $darkR = max(0, $r * 0.8);
    $darkG = max(0, $g * 0.8);
    $darkB = max(0, $b * 0.8);
    
    return [
        'light' => sprintf('#%02x%02x%02x', $lightR, $lightG, $lightB),
        'dark' => sprintf('#%02x%02x%02x', $darkR, $darkG, $darkB),
        'rgb' => "$r, $g, $b"
    ];
}

function optimizeCSS($css) {
    // CSS optimizasyonu
    $css = preg_replace('/\/\*.*?\*\//s', '', $css); // Yorumları kaldır
    $css = preg_replace('/\s+/', ' ', $css); // Fazla boşlukları kaldır
    $css = str_replace([' {', '{ ', ' }', '} ', ' ;', '; ', ' :', ': '], ['{', '{', '}', '}', ';', ';', ':', ':'], $css);
    return trim($css);
}
?>
