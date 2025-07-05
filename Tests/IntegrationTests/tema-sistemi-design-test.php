<?php
/**
 * Tema Sistemi Test - Design.php Entegrasyonu
 * index.css'deki değişkenleri okuyup JSON'a dönüştürme testi
 */

// Test için basit path'ler tanımlayalım
define('CSS', 'Public/CSS/');
define('JSON_DIR', 'Public/Json/');

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
    $iterations = 0;
    $maxIterations = 10; // Sonsuz döngüyü önlemek için

    while ($changed && $iterations < $maxIterations) {
        $changed = false;
        $iterations++;

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

function getCustomCSS($languageID = 1) {
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
                echo "✓ JSON dosyası bulundu: $file\n";
                break;
            }
        }
    }

    if (empty($customCSS)) {
        $cssFiles = [
            CSS . "index-" . $languageID . ".css",
            CSS . "index.css"
        ];
        
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                echo "✓ CSS dosyası bulundu: $cssFile\n";
                $customCSS = convertCSSToJSON($cssFile);
                if (!empty($customCSS)) {
                    break;
                }
            }
        }
    }

    return $customCSS;
}

echo "🎨 Tema Sistemi Test Başlatılıyor...\n";
echo "=====================================\n\n";

// CSS dosyası varlığını kontrol et
$cssFile = CSS . "index.css";
if (file_exists($cssFile)) {
    echo "✓ index.css dosyası mevcut\n";
    $fileSize = filesize($cssFile);
    echo "  Dosya boyutu: $fileSize bytes\n\n";
} else {
    echo "✗ index.css dosyası bulunamadı\n";
    exit;
}

// CSS'i JSON'a dönüştür
echo "📝 CSS'den JSON'a dönüştürme testi:\n";
echo "------------------------------------\n";

$customCSS = getCustomCSS(1);

if (!empty($customCSS)) {
    echo "✓ CSS başarıyla JSON'a dönüştürüldü\n";
    echo "  Toplam değişken sayısı: " . count($customCSS) . "\n\n";
    
    // Önemli değişkenleri göster
    echo "🔧 Temel Tema Değişkenleri:\n";
    echo "----------------------------\n";
    
    $importantVars = [
        'primary-color',
        'secondary-color', 
        'accent-color',
        'body-bg-color',
        'content-bg-color',
        'body-text-color',
        'content-max-width',
        'font-size-normal',
        'border-radius-base',
        'transition-speed'
    ];
    
    foreach ($importantVars as $var) {
        if (isset($customCSS[$var])) {
            echo "  --$var: " . $customCSS[$var] . "\n";
        }
    }
    
    echo "\n🔄 CSS Değişken Referanslarını Çözme:\n";
    echo "-------------------------------------\n";
    
    $resolved = resolveVariables($customCSS);
    
    echo "✓ Değişken referansları çözüldü\n";
    echo "  Çözülmüş değişken örnekleri:\n";
    
    foreach ($importantVars as $var) {
        if (isset($resolved[$var]) && $resolved[$var] !== $customCSS[$var]) {
            echo "  --$var: " . $customCSS[$var] . " → " . $resolved[$var] . "\n";
        }
    }
    
    // JSON dosyası olarak kaydetme testi
    echo "\n💾 JSON Dosyası Kaydetme Testi:\n";
    echo "--------------------------------\n";
    
    if (!file_exists(JSON_DIR.'CSS/')) {
        mkdir(JSON_DIR.'CSS/', 0777, true);
        echo "✓ CSS dizini oluşturuldu\n";
    }
    
    $testJsonFile = JSON_DIR.'CSS/test-theme.json';
    $jsonContent = json_encode($resolved, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($testJsonFile, $jsonContent)) {
        echo "✓ Test JSON dosyası başarıyla kaydedildi: $testJsonFile\n";
        echo "  Dosya boyutu: " . filesize($testJsonFile) . " bytes\n";
    } else {
        echo "✗ JSON dosyası kaydedilemedi\n";
    }
    
    echo "\n🧮 Özel Hesaplamalar Testi:\n";
    echo "----------------------------\n";
    
    // Product box width hesaplaması
    if (isset($resolved['homepage-product-box-width'])) {
        $width = str_replace("%", "", $resolved['homepage-product-box-width']);
        $width = intval($width);
        $calculatedWidth = 100 / ($width + 2);
        echo "  Ana sayfa ürün kutusu genişliği: {$resolved['homepage-product-box-width']}\n";
        echo "  Hesaplanmış grid genişliği: {$calculatedWidth}%\n";
    }
    
    if (isset($resolved['category-product-box-width'])) {
        $width = str_replace("%", "", $resolved['category-product-box-width']);
        $width = intval($width);
        $calculatedWidth = 100 / ($width + 2);
        echo "  Kategori ürün kutusu genişliği: {$resolved['category-product-box-width']}\n";
        echo "  Hesaplanmış grid genişliği: {$calculatedWidth}%\n";
    }
    
} else {
    echo "✗ CSS JSON'a dönüştürülemedi\n";
}

echo "\n✅ Test tamamlandı!\n";
?>
