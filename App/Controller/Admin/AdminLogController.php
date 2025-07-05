<?php
$documentRoot = str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
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

$adminCasper = $adminSession->getAdminCasper();
$config = $adminCasper->getConfig();
$json = $config->Json;
$helper = $config->Helper;

include_once MODEL . 'Admin/AdminLog.php';

$adminLogController = new AdminLogController();

if($action == 'getLogs') {

    $logs = $adminLogController->index($requestData);

    if (empty($logs)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Log bulunamadı'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'logs' => $logs
    ]);
    exit();
}
elseif($action == 'readMultiLineErrorLogs') {
    $logs = $adminLogController->readMultiLineErrorLogs();
    echo json_encode([
        'status' => 'success',
        'logs' => $logs
    ]);
    exit();
}
elseif($action == 'deleteLog'){
    $name = $requestData['name'] ?? '';
    $logType = $requestData['logType'] ?? '';

    //boş olamazlar
    if(empty($name) || empty($logType)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Name or type is empty'
        ]);
        exit();
    }

    $logPath = LOG_DIR . ($logType == 'admin' ? 'Admin/' : '') . $name . '.log';
    if(file_exists($logPath)){
        unlink($logPath);
        echo json_encode([
            'status' => 'success',
            'message' => 'Log silindi'
        ]);
        exit();
    }
    else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Log bulunamadı'
        ]);
        exit();
    }

}
elseif ($action == 'deleteSystemLog') {
    $filePath = LOG_DIR . 'errors.log';
    if (file_exists($filePath)) {
        unlink($filePath);
        echo json_encode([
            'status' => 'success',
            'message' => 'Log silindi'
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Log bulunamadı'
        ]);
        exit();
    }
}
else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}
class AdminLogController
{
    private $logModel;
    public $status = '';

    public function __construct()
    {
        $this->logModel = new AdminLog();
    }

    public function index($requestData = [])
    {
        $type = $requestData['type'] ?? 'site';
        $date = $requestData['date'] ?? date('Y-m-d');

        $this->status = $requestData['status'] ?? '';

        $name = $requestData['name'] ?? '';

        $logs = $this->logModel->getLogs($type, $date, $name);

        if (empty($logs)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Log bulunamadı'
            ]);exit;
        }
        return $this->parseLogs($logs);
    }

    //gelen logları parse edip döndürür
    public function parseLogs($logs)
    {
        $newLog = [];
        $currentLog = null;
        $status = $this->status;

        foreach ($logs as $line) {
            if (empty($line)) {
                continue;
            }

            // Örnek log başlangıç formatı: [2024-11-22 16:23:24 - info]
            if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} - (\w+)]/', $line, $matches)) {
                // Yeni bir log satırı başladığında önceki logu kaydet
                if ($currentLog) {
                    $newLog[] = $currentLog;
                }

                if($status && $status != $matches[1]) {
                    continue;
                }
                $currentLog = [
                    'date' => substr($line, 1, 10),
                    'time' => substr($line, 12, 8),
                    'statusMessage' => $matches[1],
                    'message' => trim(substr($line, strpos($line, ']') + 1))
                ];
            } else {
                // Tarih yoksa, bu satırı mevcut logun mesajına ekle
                if ($currentLog) {
                    $currentLog['message'] .= "\n" . trim($line);
                }
            }
        }

        // Döngü sonunda son logu da ekle
        if ($currentLog) {
            $newLog[] = $currentLog;
        }

        return $newLog;
    }

    public function readMultiLineErrorLogs()
    {
        $logs = $this->logModel->readMultiLineErrorLogs();
        return $this->systemLogParser($logs);
    }

    public function systemLogParser($logs)
    {
        $parsedLogs = [];
        $currentLog = '';

        // Regular expression for detecting the start of a log entry
        $logStartRegex = '/^\[\d{2}-[a-zA-Z]{3}-\d{4} \d{2}:\d{2}:\d{2} [^\]]+\]/';

        foreach ($logs as $line) {
            if (preg_match($logStartRegex, $line)) {
                // If a new log starts, save the current log and start a new one
                if (!empty($currentLog)) {
                    $parsedLogs[] = $this->extractDateAndMessage($currentLog);
                }
                $currentLog = $line;
            } else {
                // Append continuation lines to the current log
                $currentLog .= ' ' . $line;
            }
        }

        // Add the last log if it exists
        if (!empty($currentLog)) {
            $parsedLogs[] = $this->extractDateAndMessage($currentLog);
        }

        return $parsedLogs;
    }

    private function extractDateAndMessage($logLine)
    {
        $dateRegex = '/^\[(\d{2}-[a-zA-Z]{3}-\d{4} \d{2}:\d{2}:\d{2} [^\]]+)\]/';
        if (preg_match($dateRegex, $logLine, $matches)) {
            $date = $matches[1];
            $message = trim(str_replace($matches[0], '', $logLine));
            return [
                'date' => $date,
                'message' => $message,
            ];
        }

        // If the log doesn't match the expected format, return it as raw
        return [
            'date' => null,
            'message' => trim($logLine),
        ];
    }

}