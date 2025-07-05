<?php
/**
 * Test Logger Sınıfı
 * 
 * Test işlemleri için özel log sistemi
 * Ana proje log sistemine müdahale etmez
 * 
 * @author GitHub Copilot
 * @date 24 Haziran 2025
 */

class TestLogger {
    
    private static $logDir;
    private static $logFile;
    private static $instance = null;
    
    /**
     * @var bool Silent mode - log ama konsola yazdırma
     */
    public static $silentMode = false;
    
    /**
     * Singleton pattern
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        self::$logDir = TESTS_ROOT . '/Logs';
        
        // Log dizinini oluştur
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        
        // Bugünün log dosyası
        self::$logFile = self::$logDir . '/test_' . date('Y-m-d') . '.log';
    }
      /**
     * Log yazdır
     */
    public static function log($level, $message, $context = []) {
        $logger = self::getInstance();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        
        $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        // Dosyaya yaz
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Console'a da yazdır (silent mode değilse)
        if (!self::$silentMode) {
            echo $logEntry;
        }
    }
    
    /**
     * Info log
     */
    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }
    
    /**
     * Warning log
     */
    public static function warning($message, $context = []) {
        self::log('WARNING', $message, $context);
    }
    
    /**
     * Error log
     */
    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }
    
    /**
     * Success log
     */
    public static function success($message, $context = []) {
        self::log('SUCCESS', $message, $context);
    }
    
    /**
     * Debug log
     */
    public static function debug($message, $context = []) {
        self::log('DEBUG', $message, $context);
    }
    
    /**
     * Test başlangıcı log
     */
    public static function testStart($testName) {
        self::log('TEST_START', "=== $testName BAŞLADI ===");
    }
    
    /**
     * Test bitişi log
     */
    public static function testEnd($testName, $success = true) {
        $status = $success ? 'BAŞARILI' : 'BAŞARISIZ';
        self::log('TEST_END', "=== $testName $status ===");
    }
    
    /**
     * SQL sorgusu log
     */
    public static function sql($query, $params = []) {
        $message = "SQL: $query";
        if (!empty($params)) {
            $message .= " | Params: " . json_encode($params);
        }
        self::log('SQL', $message);
    }
    
    /**
     * HTTP isteği log
     */
    public static function http($method, $url, $data = []) {
        $message = "HTTP $method: $url";
        if (!empty($data)) {
            $message .= " | Data: " . json_encode($data);
        }
        self::log('HTTP', $message);
    }
    
    /**
     * Log dosyasını temizle
     */
    public static function clearLog() {
        if (file_exists(self::$logFile)) {
            unlink(self::$logFile);
        }
    }
    
    /**
     * Log dosyasını oku
     */
    public static function readLog($lines = 50) {
        if (!file_exists(self::$logFile)) {
            return [];
        }
        
        $logContent = file_get_contents(self::$logFile);
        $logLines = explode(PHP_EOL, $logContent);
        
        // Son N satırı al
        return array_slice(array_filter($logLines), -$lines);
    }
    
    /**
     * Tüm log dosyalarını listele
     */
    public static function getLogFiles() {
        $files = [];
        if (is_dir(self::$logDir)) {
            $files = glob(self::$logDir . '/test_*.log');
        }
        return $files;
    }
    
    /**
     * Log istatistikleri
     */
    public static function getLogStats($logFile = null) {
        $file = $logFile ?: self::$logFile;
        
        if (!file_exists($file)) {
            return [
                'total' => 0,
                'info' => 0,
                'warning' => 0,
                'error' => 0,
                'success' => 0,
                'debug' => 0
            ];
        }
        
        $content = file_get_contents($file);
        
        return [
            'total' => substr_count($content, PHP_EOL),
            'info' => substr_count($content, '[INFO]'),
            'warning' => substr_count($content, '[WARNING]'),
            'error' => substr_count($content, '[ERROR]'),
            'success' => substr_count($content, '[SUCCESS]'),
            'debug' => substr_count($content, '[DEBUG]')
        ];
    }
}
