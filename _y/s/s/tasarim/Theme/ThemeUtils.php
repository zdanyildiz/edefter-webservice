<?php
/**
 * Tema Yardımcı Fonksiyonları
 * Tema değerlerini temizlemek ve formatlamak için kullanılır
 * 
 * NOT: sanitizeColorValue ve sanitizeNumericValue fonksiyonları
 * Theme.php dosyasında tanımlanmıştır. Çifte tanımlama hatası
 * yaşanmaması için buradan kaldırılmıştır.
 */

/**
 * Tema verilerini temizle ve güvenli hale getir
 */
function cleanThemeData($themeData) {
    $cleanedData = [];
    
    foreach ($themeData as $key => $value) {
        if (in_array($key, ['action', 'languageID'])) {
            continue;
        }
        
        // Renk değerlerini temizle
        if (strpos($key, 'color') !== false || strpos($key, 'background') !== false || strpos($key, 'border-color') !== false) {
            $cleanedData[$key] = sanitizeColorValue($value);
        } elseif (strpos($key, 'border-radius') !== false || strpos($key, 'spacing') !== false || strpos($key, 'font-size') !== false || strpos($key, 'width') !== false || strpos($key, 'height') !== false) {
            // Sayısal değerler için özel temizleme
            $cleanedData[$key] = sanitizeNumericValue($value);
        } elseif (is_string($value) && preg_match('/\d+(px|rem|em|%)/', $value)) {
            // Diğer ölçü birimli değerler
            $cleanedData[$key] = sanitizeNumericValue($value);
        } else {
            $cleanedData[$key] = $value;
        }
    }
    
    return $cleanedData;
}

/**
 * Tema JSON dosyası kaydetme
 */
function saveThemeJSON($data, $languageID) {
    $jsonFile = JSON_DIR . "CSS/custom-" . $languageID . ".json";
    
    // Dizin yoksa oluştur
    $directory = dirname($jsonFile);
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    $cleanedData = cleanThemeData($data);
    
    $result = file_put_contents($jsonFile, json_encode($cleanedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    if ($result === false) {
        throw new Exception("Tema dosyası kaydedilemedi: " . $jsonFile);
    }
    
    return true;
}

/**
 * Tema JSON dosyası yükleme
 */
function loadThemeJSON($languageID) {
    $files = [
        JSON_DIR . "CSS/custom-" . $languageID . ".json",
        JSON_DIR . "CSS/index-" . $languageID . ".json",
        JSON_DIR . "CSS/index.json"
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            if (!empty($data)) {
                return $data;
            }
        }
    }
    
    return [];
}

// Renk ve sayısal değer sanitizasyon fonksiyonları
function sanitizeColorValue($value, $default = '') {
    if (empty($value)) {
        return $default;
    }
    
    $value = strtolower(trim($value));
    
    // Geçerli hex renk formatı kontrolü
    if (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $value)) {
        return $value;
    }
    
    // RGB formatı varsa hex'e çevir
    if (preg_match('/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/', $value, $matches)) {
        $r = dechex(min(255, max(0, intval($matches[1]))));
        $g = dechex(min(255, max(0, intval($matches[2]))));
        $b = dechex(min(255, max(0, intval($matches[3]))));
        return '#' . str_pad($r, 2, '0', STR_PAD_LEFT) . str_pad($g, 2, '0', STR_PAD_LEFT) . str_pad($b, 2, '0', STR_PAD_LEFT);
    }
    
    // CSS renk adları
    $colorNames = [
        'white' => '#ffffff', 'black' => '#000000', 'red' => '#ff0000',
        'green' => '#008000', 'blue' => '#0000ff', 'yellow' => '#ffff00',
        'cyan' => '#00ffff', 'magenta' => '#ff00ff', 'silver' => '#c0c0c0',
        'gray' => '#808080', 'grey' => '#808080', 'orange' => '#ffa500',
        'purple' => '#800080', 'navy' => '#000080'
    ];
    
    if (isset($colorNames[$value])) {
        return $colorNames[$value];
    }
    
    return $default;
}

function sanitizeNumericValue($value, $unit = '', $default = "") {
    if (empty($value)) {
        return $default;
    }

    // Extract numeric value and potential unit
    if (is_string($value) && preg_match('/^(\d+(?:\.\d+)?)(px|rem|em|%)?$/', $value, $matches)) {
        $numericValue = floatval($matches[1]);

        // Prevent negative values
        $numericValue = max(0, $numericValue);

        // If a specific unit is provided, append it.
        // If no specific unit is provided (e.g., for <input type="number">), return only the numeric value.
        return !empty($unit) ? $numericValue . $unit : $numericValue;
    }

    // Fallback for purely numeric values without units (e.g., '1.5' for line-height)
    $numericValue = floatval($value);
    $numericValue = max(0, $numericValue);
    return !empty($unit) ? $numericValue . $unit : $numericValue;
}

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
            $content = file_get_contents($file);
            $customCSS = json_decode($content, true);
            
            // JSON decode hatası kontrolü
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error in Theme.php for file $file: " . json_last_error_msg());
                continue;
            }
            
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

    return $customCSS ?? [];
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
        }
    }
    
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
//print_r($customCSS); // Debug amaçlı çıktıyı kaldırabilirsiniz
$predefinedThemes = getPredefinedThemes();
?>
