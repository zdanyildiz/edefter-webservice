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

    $requestData['border-radius-sm'] =  "4";               /* Küçük köşe yuvarlaklığı */
    $requestData['border-radius-lg'] =  "16";              /* Büyük köşe yuvarlaklığı */
    $requestData['border-radius-pill'] =  "500";           /* Pill buton için yuvarlaklık */

    /* ========= Gölge ve Derinlik ========= */
    $requestData['box-shadow-base'] =  "0 2px 10px rgba(0, 0, 0, 0.075)";
    $requestData['box-shadow-sm'] =  "0 1px 3px rgba(0, 0, 0, 0.05)";
    $requestData['box-shadow-md'] =  "0 4px 6px rgba(0, 0, 0, 0.1)";
    $requestData['box-shadow-lg'] =  "0 10px 25px rgba(0, 0, 0, 0.15)";
    $requestData['box-shadow'] =  "0 2px 5px rgba(0, 0, 0, 0.1)";
    $requestData['text-shadow'] =  "1px 1px 2px rgba(0, 0, 0, 0.1)";

    /* ========= Boşluk ve Ölçüler ========= */
    $requestData['spacing-xs'] =  "4";
    $requestData['spacing-sm'] =  "8";
    $requestData['spacing-md'] =  "16";
    $requestData['spacing-lg'] =  "24";
    $requestData['spacing-xl'] =  "32";
    $requestData['spacing-xxl'] =  "48";

    $requestData['alert-danger-text'] =  "#ffffff";

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
                    $px_keys = ['width', 'height', 'size', 'radius', 'padding', 'margin', 'spacing', 'breakpoint'];
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