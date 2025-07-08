<?php
/**
 * @var Session $session
 * @var Database $db
 * @var array $requestData
 */


$casper = $session->getCasper();

if (!$casper instanceof Casper) {
    echo "Casper is not here - CartController:15";exit();
}

$config = $casper->getConfig();
$helper = $config->Helper;
$json = $config->Json;

$visitor = $casper->getVisitor();
if(!isset($visitor['visitorUniqID'])){
    header('Location: /?visitorID-None');exit();
}

// Kullanım sınırlaması kontrolü için gerekli değişkenler
$memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;
$userIdentifier = $memberStatus ? 
    $visitor['visitorIsMember']['memberID'] : 
    $visitor['visitorUniqID'];
$userType = $memberStatus ? 'member' : 'visitor';

// EDefterUsage modelini yükle
require_once MODEL . 'EDefterUsage.php';
$usageModel = new EDefterUsage($db);

$action = $requestData['action'] ?? null;

if(!isset($action)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error.',
        'memberData' => []
    ]);
    exit();
}

// Geçici dizin ayarı (opsiyonel)
// Sunucuda özel bir geçici dizin kullanmak isterseniz:
$upload_tmp_dir = FILE . 'temp_uploads';
if (!is_dir($upload_tmp_dir)) {
    mkdir($upload_tmp_dir, 0777, true);
}
ini_set('upload_tmp_dir', $upload_tmp_dir);

header('Content-Type: application/json');

if ($action == "process" && isset($_FILES['xml_file']) && isset($requestData['type'])) {
    
    // Kullanım sınırı kontrolü
    if ($usageModel->isLimitExceeded($userIdentifier, $userType)) {
        $usageInfo = $usageModel->getUsageInfo($userIdentifier, $userType);
        $limitMessage = $userType === 'member' ? 
            "Günlük işlem sınırınız (20) dolmuştur." : 
            "Günlük işlem sınırınız (5) dolmuştur. Üye olarak 20 işlem yapabilirsiniz.";
        
        echo json_encode([
            'success' => false, 
            'errors' => [$limitMessage],
            'usage_info' => $usageInfo
        ]);
        exit;
    }
    $type = $requestData['type'];
    $xslt_files = [
        'Berat' => 'berat.xslt',
        'DefterRaporu' => 'defterraporu.xslt',
        'Kebir' => 'kebir.xslt',
        'Yevmiye' => 'yevmiye.xslt'
    ];

    if (!isset($xslt_files[$type])) {
        echo json_encode(['success' => false, 'errors' => ['Geçersiz dönüşüm türü.']]);
        exit;
    }

    $xslt_file = FILE . $xslt_files[$type];
    $output_dir = FILE . "outputs";

    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0777, true);
    }

    $results = [];
    $errors = [];

    if (!file_exists($xslt_file)) {
        $errors[] = "Hata: $xslt_file dosyası bulunamadı!";
    } elseif (!class_exists('XSLTProcessor')) {
        $errors[] = "Hata: XSLTProcessor sınıfı bulunamadı. Lütfen php-xsl uzantısını etkinleştirin!";
    } else {
        foreach ($_FILES['xml_file']['tmp_name'] as $index => $tmp_file) {
            $file_name = $_FILES['xml_file']['name'][$index];

            // Dosya yükleme hatalarını kontrol et
            $error_code = $_FILES['xml_file']['error'][$index];
            if ($error_code !== UPLOAD_ERR_OK) {
                $errors[] = "Hata: '{$file_name}' yüklenirken hata oluştu. Hata kodu: {$error_code}";
                continue; // Hata varsa diğer dosyaya geç
            }

            // Önceki ve sonraki işlemler:
            if (pathinfo($file_name, PATHINFO_EXTENSION) !== 'xml') {
                $errors[] = "Hata: '$file_name' yalnızca XML dosyaları kabul edilir!";
                continue;
            }

            $random_prefix = rand(1000, 9999);
            $output_file = "$output_dir/{$random_prefix}_output_$type.html";
            $relative_output_file = "/Public/File/outputs/{$random_prefix}_output_$type.html";

            try {
                if (empty($tmp_file) || !file_exists($tmp_file)) {
                    $errors[] = "Hata: '$file_name' için geçici dosya bulunamadı veya boş!";
                    continue;
                }

                // XML yükleme ve hata kontrolü
                $xml = new DOMDocument();
                libxml_use_internal_errors(true);
                $xmlLoaded = $xml->load($tmp_file);
                $xmlErrors = libxml_get_errors();
                libxml_clear_errors();

                if (!$xmlLoaded || !empty($xmlErrors)) {
                    foreach ($xmlErrors as $error) {
                        $errors[] = "XML Hata: $file_name - Satır $error->line: " . trim($error->message);
                    }
                    if (!$xmlLoaded) $errors[] = "XML Hata: $file_name - Dosya yüklenemedi!";
                    continue;
                }

                // XSLT işlemleri
                $xslt = new DOMDocument();
                libxml_use_internal_errors(true);
                $xsltLoaded = $xslt->load($xslt_file);
                $xsltErrors = libxml_get_errors();
                libxml_clear_errors();

                if (!$xsltLoaded || !empty($xsltErrors)) {
                    foreach ($xsltErrors as $error) {
                        $errors[] = "XSLT Hata: $xslt_file - Satır $error->line: " . trim($error->message);
                    }
                    if (!$xsltLoaded) $errors[] = "XSLT Hata: $xslt_file - Dosya yüklenemedi!";
                    continue;
                }

                $proc = new XSLTProcessor();
                if (!$proc->importStylesheet($xslt)) {
                    $errors[] = "XSLT Import Hata: $xslt_file - " . $proc->getLastError();
                    continue;
                }

                $result = $proc->transformToDoc($xml);
                if ($result === false) {
                    $errors[] = "Dönüşüm Hata: $file_name - " . $proc->getLastError();
                    continue;
                }

                if (!$result->save($output_file)) {
                    $errors[] = "Kaydetme Hata: $output_file - Klasör izinlerini kontrol edin!";
                    continue;
                }

                $results[] = "<div class='result-item'>Çıktı oluşturuldu: <a href='$relative_output_file' download>İndir ($file_name)</a> | <a href='$relative_output_file' target='_blank'>Görüntüle</a></div>";

            } catch (Exception $e) {
                $errors[] = "Beklenmeyen Hata: $file_name - " . $e->getMessage();
            }
        }

    }

    // JSON Yanıt
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
    } elseif (!empty($results)) {
        // Başarılı işlem - kullanım sayacını artır
        $usageModel->incrementUsage($userIdentifier, $userType);
        
        // Güncel kullanım bilgilerini al
        $usageInfo = $usageModel->getUsageInfo($userIdentifier, $userType);
        
        echo json_encode([
            'success' => true, 
            'results' => $results,
            'usage_info' => $usageInfo
        ]);
    } else {
        echo json_encode(['success' => false, 'errors' => ['Bilinmeyen bir hata oluştu.']]);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'errors' => ['Geçersiz istek.']]);
    exit;
}