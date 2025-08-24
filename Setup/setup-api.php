<?php

namespace Setup;

use Exception;
use Setup\Core\SetupManager;

// --- KAPSAMLI LOGLAMA MEKANİZMASI ---
$logFilePath = __DIR__ . '/setup.log';
// Her yeni istek için log dosyasına ayırıcı bir başlık ekle
file_put_contents($logFilePath, "\n--- NEW REQUEST: " . date('Y-m-d H:i:s') . " ---\n", FILE_APPEND);

// Tüm hataları göstermeyi ve loglamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hataları ekrana basma, sadece logla
ini_set('log_errors', 1);
ini_set('error_log', $logFilePath);

// Ölümcül hataları bile yakalamak için bir shutdown fonksiyonu kaydet
register_shutdown_function(function() use ($logFilePath) {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_PARSE])) {
        $errorMessage = "FATAL ERROR: {$error['message']} in {$error['file']} on line {$error['line']}";
        file_put_contents($logFilePath, date('[H:i:s] ') . $errorMessage . "\n", FILE_APPEND);
    }
});

// --- ANA KURULUM MANTIĞI ---

// Gelen isteği logla
$rawInput = file_get_contents('php://input');
file_put_contents($logFilePath, date('[H:i:s] ') . "Request Body: " . ($rawInput ?: '(empty)') . "\n", FILE_APPEND);

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Core/SetupManager.php';
require_once __DIR__ . '/Core/SetupStep.php';

// Yüklenen sınıfları takip et
$loadedClasses = [];

// Step sınıflarını otomatik yükle - güvenli versiyon
spl_autoload_register(function ($class) use (&$loadedClasses) {
    // Sadece Setup\Steps namespace'ini işle
    $prefix = 'Setup\\Steps\\';
    $base_dir = __DIR__ . '/Steps/';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Zaten yüklenmişse tekrar yükleme
    if (in_array($class, $loadedClasses) || class_exists($class, false)) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
        $loadedClasses[] = $class;
    }
});

try {
    $input = json_decode($rawInput, true) ?? [];

    $action = $input['action'] ?? 'getSteps';
    $config = $input['config'] ?? [];

    $manager = new SetupManager($config);

    // Adımları ekle
    $manager->addStep(new \Setup\Steps\EnvSetupStep($manager));
    $manager->addStep(new \Setup\Steps\LocalDatabaseStep($manager));
    $manager->addStep(new \Setup\Steps\CloudflareStep($manager));
    $manager->addStep(new \Setup\Steps\PleskStep($manager));
    $manager->addStep(new \Setup\Steps\DeploymentStep($manager));
    $manager->addStep(new \Setup\Steps\RemoteDatabaseStep($manager));

    $response = [];

    if ($action === 'getSteps') {
        $steps = $manager->getSteps();
        $stepData = [];
        foreach ($steps as $index => $step) {
            $stepData[] = [
                'index' => $index,
                'name' => $step->getName(),
                'description' => $step->getDescription(),
                'status' => 'pending'
            ];
        }
        $response = ['status' => 'success', 'steps' => $stepData];
    } elseif ($action === 'runStep') {
        $stepIndex = $input['stepIndex'] ?? -1;
        $steps = $manager->getSteps();

        if (!isset($steps[$stepIndex])) {
            throw new Exception("Geçersiz adım indeksi: $stepIndex");
        }

        $step = $steps[$stepIndex];
        $response = $step->run();
        $response['log'] = $manager->getLog();

    } else {
        throw new Exception("Geçersiz eylem: $action");
    }

    $jsonResponse = json_encode($response);
    file_put_contents($logFilePath, date('[H:i:s] ') . "Successful Response: " . $jsonResponse . "\n", FILE_APPEND);
    echo $jsonResponse;

} catch (Exception $e) {
    $errorMessage = "Caught Exception: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}";
    file_put_contents($logFilePath, date('[H:i:s] ') . $errorMessage . "\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}