<?php
$root = $_SERVER['DOCUMENT_ROOT'];
//LOG_DIR tanımlı değilse
if (!defined('LOG_DIR')) {
    define('LOG_DIR', $root . '/Public/Log/');
}
class Log
{

    /**
     * Writes a message to the log.
     *
     * @param string $message The message to write.
     * @param string $type The type of log message (e.g. "warning", "error").
     * @param string $name Optional custom name for the log file.
     */
    public static function write($message, $type = "info", $name = null)
    {
        $date = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');
        $logFileName = (!empty($name)) ? "$date-$name" : $date;
        $logfile = LOG_DIR . $logFileName . ".log";
        $log = "[{$dateTime} - {$type}] {$message}" . PHP_EOL;

        $dir = dirname($logfile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($logfile, $log, FILE_APPEND);
    }

    public static function adminWrite($message, $type = "info", $name = null)
    {
        $date = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');
        $logFileName = (!empty($name)) ? "$date-$name" : $date;
        $logfile = LOG_DIR . "Admin/" . $logFileName . ".log";
        $log = "[{$dateTime} - {$type}] {$message}" . PHP_EOL;

        $dir = dirname($logfile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($logfile, $log, FILE_APPEND);
    }
}