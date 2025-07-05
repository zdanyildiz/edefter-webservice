<?php

class AdminLog
{
    private $logDir;

    public function __construct()
    {
        $this->logDir = $_SERVER['DOCUMENT_ROOT'] . '/Public/Log/';
    }

    public function getLogs($type = 'site', $date = '', $name = '')
    {
        $logPath = $this->logDir . ($type == 'admin' ? 'Admin/' : '') . $date . ($name ? "-$name" : '') . '.log';
        if (!file_exists($logPath)) {
            return [];
        }

        return file($logPath);
    }

    Public function readMultiLineErrorLogs()
    {
        $filePath = LOG_DIR . 'errors.log';
        $logs = [];
        if (!file_exists($filePath)) {
            return $logs;
        }

        $file = fopen($filePath, 'r');
        if (!$file) {
            return $logs;
        }

        $currentLog = '';
        while (($line = fgets($file)) !== false) {
            if (preg_match('/^\[.*\]/', $line)) {
                if ($currentLog) {
                    $logs[] = $currentLog;
                }
                $currentLog = $line;
            } else {
                $currentLog .= $line;
            }
        }

        if ($currentLog) {
            $logs[] = $currentLog;
        }

        fclose($file);
        return $logs;
    }
}