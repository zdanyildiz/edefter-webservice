<?php
/**
 * Güvenli Reset Scripti (SAFE_RESET) - AJAX Versiyonu v4 (Detaylı Loglama)
 *
 * Bu script, her adımı detaylı bir şekilde loglayarak işlemin şeffaf bir şekilde
 * takip edilmesini sağlar. Gelen veriler, yapılan kontroller ve işlemlerin sonuçları
 * adım adım raporlanır.
 */

ob_start();
header('Content-Type: application/json');

require_once __DIR__ . '/Plesk.php';
require_once __DIR__ . '/../App/Core/Log.php'; // Log sınıfını da kullanalım

$logOutput = [];
$response = [
    'status'  => 'error',
    'message' => 'Bilinmeyen bir hata oluştu.',
    'log'     => ''
];

$hasErrors = false; // Başarı durumu takibi için

function addToLog($message, $type = 'INFO') {
    global $logOutput;
    $logEntry = sprintf('[%s] [%s] %s', date('Y-m-d H:i:s.u'), $type, $message);
    $logOutput[] = $logEntry;
    Log::write($logEntry, 'reset'); // Kalıcı bir log dosyasına da yazalım
}

try {
    addToLog('===== GÜVENLİ RESET İŞLEMİ BAŞLATILDI =====');

    // --- Adım 1: Gelen Form Verilerini Loglama ---
    addToLog('Formdan gelen veriler alınıyor ve loglanıyor...');
    $postDataForLog = $_POST;
    // Güvenlik için şifreleri logdan çıkaralım
    if (isset($postDataForLog['password'])) $postDataForLog['password'] = '[GİZLENDİ]';
    if (isset($postDataForLog['localPassword'])) $postDataForLog['localPassword'] = '[GİZLENDİ]';
    addToLog("Gelen POST verisi: " . json_encode($postDataForLog, JSON_PRETTY_PRINT));

    $domainToDelete = $_POST['domain'] ?? '';
    $localDbName = $_POST['localDatabaseName'] ?? '';
    $localDbUser = $_POST['localUsername'] ?? 'root';
    $localDbPass = $_POST['localPassword'] ?? '';
    $localDbHost = $_POST['localServerUrl'] ?? 'localhost';
    $deletePleskDomain = ($_POST['deletePleskDomain'] ?? 'false') === 'true';
    $jsonConfigString = $_POST['jsonConfig'] ?? '{}';

    if (empty($domainToDelete) || empty($localDbName)) {
        throw new Exception('Gerekli bilgiler (domain, local database name) formdan alınamadı.');
    }

    // --- Adım 2: Plesk Domain Silme (Eğer istenmişse) ---
    addToLog('--- Plesk Domain Silme Adımı Başlatılıyor ---');
    if ($deletePleskDomain) {
        addToLog('Plesk domain silme onayı verildi.', 'ACTION');
        
        // JSON string kontrolü düzeltildi
        if (empty(trim($jsonConfigString))) {
            addToLog('Plesk silme işlemi için JSON konfigürasyonu boş, adım atlanıyor.', 'WARNING');
            $hasErrors = true;
        } else {
            $jsonConfig = json_decode($jsonConfigString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                addToLog('Plesk silme işlemi için gönderilen JSON konfigürasyonu geçersiz: ' . json_last_error_msg(), 'ERROR');
                $hasErrors = true;
            } else {
                $pleskConfig = $jsonConfig['plesk'] ?? [];
                $pleskServerUrl = $pleskConfig['server_url'] ?? null;
                $pleskUser = $pleskConfig['primary_user'] ?? null;
                $pleskPassword = $pleskConfig['primary_password'] ?? null;

                if ($pleskServerUrl && $pleskUser && $pleskPassword) {
                    try {
                        addToLog("Plesk API'ye bağlanılıyor: {$pleskServerUrl}");
                        $plesk = new PleskAPI($pleskServerUrl, $pleskUser, $pleskPassword);
                        
                        // Önce .env dosyasından PLESK_DOMAIN_ID'yi oku
                        $pleskDomainId = null;
                        $envPath = dirname(__DIR__) . '/.env';
                        if (file_exists($envPath)) {
                            $envContent = file_get_contents($envPath);
                            if (preg_match('/^PLESK_DOMAIN_ID=(.*)$/m', $envContent, $matches)) {
                                $pleskDomainId = trim($matches[1]);
                                addToLog("PLESK_DOMAIN_ID .env dosyasında bulundu: '{$pleskDomainId}'");
                            }
                        }

                        // Domain ID ile silmeyi öncelikle dene
                        if (!empty($pleskDomainId) && $pleskDomainId !== '') {
                            addToLog("Plesk Domain ID'si ile silme deneniyor: {$pleskDomainId}", 'ACTION');
                            if ($plesk->deleteDomainById($pleskDomainId)) {
                                addToLog("Plesk domain ID '{$pleskDomainId}' başarıyla silindi.", 'SUCCESS');
                            } else {
                                addToLog("Plesk domain ID '{$pleskDomainId}' silinemedi. Domain adı ile deneniyor...", 'WARNING');
                                // ID ile silinmediyse, domain adı ile dene
                                if ($plesk->deleteDomain($domainToDelete)) {
                                    addToLog("Plesk domain '{$domainToDelete}' domain adı ile başarıyla silindi.", 'SUCCESS');
                                } else {
                                    addToLog("Plesk domain '{$domainToDelete}' hiçbir yöntemle silinemedi.", 'ERROR');
                                    $hasErrors = true;
                                }
                            }
                        } else {
                            addToLog("PLESK_DOMAIN_ID .env dosyasında bulunamadı veya boş. Domain adı ile silme deneniyor: '{$domainToDelete}'", 'WARNING');
                            if ($plesk->deleteDomain($domainToDelete)) {
                                addToLog("Plesk domain '{$domainToDelete}' başarıyla silindi.", 'SUCCESS');
                            } else {
                                addToLog("Plesk domain '{$domainToDelete}' domain adı ile silinemedi.", 'ERROR');
                                $hasErrors = true;
                            }
                        }
                    } catch (Exception $e) {
                        addToLog("Plesk API işlemi sırasında bir hata oluştu: " . $e->getMessage(), 'ERROR');
                        $hasErrors = true;
                    }
                } else {
                    addToLog('JSON içinde Plesk API bilgileri (server_url, primary_user, primary_password) eksik. Plesk silme işlemi atlandı.', 'WARNING');
                    $hasErrors = true;
                }
            }
        }
    } else {
        addToLog('Plesk domain silme onayı verilmedi, adım atlanıyor.');
    }

    // --- Adım 3: Yerel Veritabanını Sil ---
    addToLog('--- Yerel Veritabanı Silme Adımı Başlatılıyor ---');
    try {
        // Veritabanı adını, create.php'deki mantıkla aynı şekilde sanitize et
        $sanitizedDbName = str_replace('.', '_', $localDbName);
        addToLog("Formdan gelen veritabanı adı '{$localDbName}', standartlaştırılmış arama adı: '{$sanitizedDbName}'");

        $conn = new mysqli($localDbHost, $localDbUser, $localDbPass);
        if ($conn->connect_error) {
            throw new Exception("MySQL bağlantı hatası: " . $conn->connect_error);
        }

        $escapedDbName = $conn->real_escape_string($sanitizedDbName);
        $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$escapedDbName}'");

        if ($result && $result->num_rows > 0) {
            addToLog("Veritabanı '{$sanitizedDbName}' bulundu. Silme komutu gönderiliyor...", 'ACTION');
            if ($conn->query("DROP DATABASE `{$escapedDbName}`")) {
                addToLog("Yerel veritabanı '{$sanitizedDbName}' başarıyla silindi.", 'SUCCESS');
            } else {
                addToLog("Veritabanı '{$sanitizedDbName}' silinemedi. Hata: " . $conn->error, 'ERROR');
                $hasErrors = true;
            }
        } else {
            addToLog("Veritabanı '{$sanitizedDbName}' bulunamadı, adım atlanıyor.");
        }
        $conn->close();
    } catch (Exception $e) {
        addToLog("Veritabanı işlemi sırasında bir istisna oluştu: " . $e->getMessage(), 'ERROR');
        $hasErrors = true;
    }

    // --- Adım 4: .env ve Diğer Dosyaları Temizle ---
    addToLog('--- Dosya Temizleme Adımı Başlatılıyor ---');
    $filesToClean = [
        dirname(__DIR__) . '/.env',
        __DIR__ . '/debug_create.log',
        dirname(__DIR__) . '/robots.txt'
    ];

    foreach ($filesToClean as $file) {
        addToLog("Dosya kontrol ediliyor: {$file}");
        if (file_exists($file)) {
            addToLog("Dosya bulundu. Siliniyor...", 'ACTION');
            if (unlink($file)) {
                addToLog("'" . basename($file) . "' dosyası başarıyla silindi.", 'SUCCESS');
            } else {
                addToLog("'" . basename($file) . "' dosyası silinemedi. İzinleri kontrol edin.", 'ERROR');
                $hasErrors = true;
            }
        } else {
            addToLog("'" . basename($file) . "' dosyası bulunamadı, atlanıyor.");
        }
    }
    
    // Başarı durumu kontrolü düzeltildi
    if ($hasErrors) {
        addToLog('===== GÜVENLİ RESET İŞLEMİ HATALARLA TAMAMLANDI =====', 'WARNING');
        $response['status'] = 'warning';
        $response['message'] = 'Sistem sıfırlama işlemi tamamlandı ancak bazı adımlarda hatalar oluştu. Detaylar için logları inceleyin.';
    } else {
        addToLog('===== GÜVENLİ RESET İŞLEMİ BAŞARIYLA TAMAMLANDI =====', 'SUCCESS');
        $response['status'] = 'success';
        $response['message'] = 'Sistem sıfırlama işlemi başarıyla tamamlandı.';
    }

} catch (Exception $e) {
    addToLog("Kritik bir hata oluştu: " . $e->getMessage(), 'FATAL');
    $response['message'] = 'Reset işlemi sırasında kritik bir hata oluştu: ' . $e->getMessage();
    $hasErrors = true;
}

$strayOutput = ob_get_clean();
if (!empty(trim($strayOutput))) {
    addToLog("YAKALANAN BEKLENMEDİK SUNUCU ÇIKTISI (PHP HATASI OLABİLİR):\n---\n" . trim($strayOutput) . "\n---", 'FATAL');
    $response['status'] = 'error';
    $response['message'] = 'Sunucu tarafında beklenmedik bir çıktı (PHP hatası) oluştu. Detaylar için logları kontrol edin.';
}

$response['log'] = implode("\n", $logOutput);
echo json_encode($response);

?>