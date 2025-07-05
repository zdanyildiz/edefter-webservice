<?php
/**
 * Test Curl Helper
 * 
 * HTTP isteklerini test etmek için kullanılan yardımcı sınıf.
 * Web API'lerini terminal üzerinden test etmeye yarar.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

class TestCurl
{
    /**
     * @var string Base URL
     */
    private static $baseUrl;
    
    /**
     * @var array Default headers
     */
    private static $defaultHeaders = [
        'Accept: application/json',
        'Content-Type: application/json',
        'User-Agent: TestCurl/1.0'
    ];
    
    /**
     * Sınıf başlatma
     */
    public static function init()
    {
        // Yerel domain'i otomatik tespit et
        try {
            $domain = self::getLocalDomain();
            self::$baseUrl = "http://$domain";
            TestLogger::info('TestCurl başlatıldı: ' . self::$baseUrl);
        } catch (Exception $e) {
            self::$baseUrl = 'http://localhost';
            TestLogger::warning('Domain tespit edilemedi, localhost kullanılıyor: ' . $e->getMessage());
        }
    }
    
    /**
     * GET isteği gönder
     * 
     * @param string $url Endpoint URL
     * @param array $params Query parametreleri
     * @param array $headers Ek headerlar
     * @return array Response data
     */
    public static function get($url, $params = [], $headers = [])
    {
        self::init();
        
        // Query string oluştur
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $url .= (strpos($url, '?') !== false ? '&' : '?') . $queryString;
        }
        
        $fullUrl = self::$baseUrl . $url;
        
        TestLogger::info("GET İsteği: $fullUrl");
        
        return self::makeRequest('GET', $fullUrl, null, $headers);
    }
    
    /**
     * POST isteği gönder
     * 
     * @param string $url Endpoint URL
     * @param array $data POST data
     * @param array $headers Ek headerlar
     * @return array Response data
     */
    public static function post($url, $data = [], $headers = [])
    {
        self::init();
        
        $fullUrl = self::$baseUrl . $url;
        $postData = is_array($data) ? json_encode($data) : $data;
        
        TestLogger::info("POST İsteği: $fullUrl");
        TestLogger::info("POST Data: " . $postData);
        
        return self::makeRequest('POST', $fullUrl, $postData, $headers);
    }
      /**
     * HTTP isteği gönder
     * 
     * @param string $method HTTP method
     * @param string $url Full URL
     * @param string|null $data Request data
     * @param array $headers Ek headerlar
     * @return array Response data
     */
    private static function makeRequest($method, $url, $data = null, $headers = [])
    {
        $startTime = microtime(true);
        
        try {
            // URL bağlantısını önce kontrol et
            $urlParts = parse_url($url);
            if (!$urlParts || !isset($urlParts['host'])) {
                throw new Exception("Geçersiz URL: $url");
            }
            
            // Domain erişilebilirlik kontrolü
            if (!self::checkDomainReachable($urlParts['host'])) {
                throw new Exception("Domain erişilemez: {$urlParts['host']}");
            }
            
            $ch = curl_init();
            
            // Curl seçenekleri (daha detaylı debug)
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => array_merge(self::$defaultHeaders, $headers),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT => 'TestCurl/1.0 (PHP/' . PHP_VERSION . ')',
                CURLOPT_VERBOSE => false,
                CURLOPT_HEADER => false
            ]);
            
            // Method'a göre özel ayarlar
            switch (strtoupper($method)) {
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);
                    if ($data !== null) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    }
                    break;
                    
                case 'PUT':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    if ($data !== null) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    }
                    break;
                    
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
            }
              // İsteği gönder
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            
            // Detaylı curl bilgisi
            $curlInfo = [
                'url' => $effectiveUrl,
                'http_code' => $httpCode,
                'content_type' => $contentType,
                'total_time' => $totalTime,
                'connect_time' => curl_getinfo($ch, CURLINFO_CONNECT_TIME),
                'size_download' => curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD),
                'primary_ip' => curl_getinfo($ch, CURLINFO_PRIMARY_IP),
                'primary_port' => curl_getinfo($ch, CURLINFO_PRIMARY_PORT)
            ];
            
            // Curl hatası kontrolü
            if (curl_error($ch)) {
                $error = curl_error($ch);
                $errorCode = curl_errno($ch);
                curl_close($ch);
                throw new Exception("Curl hatası (#$errorCode): $error");
            }
              curl_close($ch);
            
            $endTime = microtime(true);
            $requestTime = round(($endTime - $startTime) * 1000, 2); // ms
            
            // Response'u parse et
            $parsedResponse = self::parseResponse($response, $contentType);
            
            // Log kaydı
            TestLogger::info("Response Code: $httpCode");
            TestLogger::info("Content Type: $contentType");
            TestLogger::info("Request Time: {$requestTime}ms");
            TestLogger::info("Effective URL: " . $curlInfo['url']);
            TestLogger::info("Primary IP: {$curlInfo['primary_ip']}:{$curlInfo['primary_port']}");
            
            if ($httpCode >= 400) {
                TestLogger::error("HTTP Hatası: $httpCode");
            } else {
                TestLogger::success("İstek başarılı: $httpCode");
            }
            
            return [
                'success' => $httpCode < 400,
                'http_code' => $httpCode,
                'content_type' => $contentType,
                'request_time' => $requestTime,
                'response' => $parsedResponse,
                'raw_response' => $response,
                'url' => $url,
                'effective_url' => $curlInfo['url'],
                'method' => $method,
                'curl_info' => $curlInfo
            ];
            
        } catch (Exception $e) {
            TestLogger::error("Request hatası: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'url' => $url,
                'method' => $method
            ];
        }
    }
    
    /**
     * Response'u parse et
     * 
     * @param string $response Raw response
     * @param string $contentType Content type
     * @return mixed Parsed response
     */
    private static function parseResponse($response, $contentType)
    {
        // JSON response
        if (strpos($contentType, 'application/json') !== false || 
            strpos($contentType, 'text/json') !== false) {
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            } else {
                TestLogger::warning("JSON parse hatası: " . json_last_error_msg());
                return $response;
            }
        }
        
        // HTML response
        if (strpos($contentType, 'text/html') !== false) {
            // HTML'i temizle ve özet çıkar
            $cleanText = strip_tags($response);
            $cleanText = preg_replace('/\s+/', ' ', $cleanText);
            return [
                'type' => 'html',
                'length' => strlen($response),
                'preview' => substr(trim($cleanText), 0, 200) . '...'
            ];
        }
        
        // Plain text
        if (strpos($contentType, 'text/plain') !== false) {
            return [
                'type' => 'text',
                'content' => $response
            ];
        }
        
        // Diğer durumlarda raw response
        return $response;
    }
    
    /**
     * Yerel domain'i tespit et
     * 
     * @return string Local domain
     */
    private static function getLocalDomain()
    {
        $rootDir = dirname(__DIR__, 2);
        $configFile = $rootDir . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Domain.php';
        
        if (!file_exists($configFile)) {
            throw new Exception("Domain.php dosyası bulunamadı: $configFile");
        }
        
        $fileContent = file_get_contents($configFile);
        
        if (preg_match('/[\'"]l\.[a-zA-Z0-9._-]+[\'"]/', $fileContent, $matches)) {
            return trim($matches[0], '\'"');
        } else {
            throw new Exception("'l.' ile başlayan yerel domain bulunamadı.");
        }
    }
    
    /**
     * Test endpoint'lerini hızlı test et
     * 
     * @param array $endpoints Test edilecek endpoint'ler
     * @return array Test sonuçları
     */
    public static function testEndpoints($endpoints)
    {
        self::init();
        
        $results = [];
        
        TestLogger::info("Endpoint testleri başlatılıyor...");
        
        foreach ($endpoints as $name => $config) {
            echo "\n" . str_repeat("-", 50) . "\n";
            echo "🧪 Test: $name\n";
            echo str_repeat("-", 50) . "\n";
            
            $method = $config['method'] ?? 'GET';
            $url = $config['url'];
            $params = $config['params'] ?? [];
            $data = $config['data'] ?? [];
            
            if (strtoupper($method) === 'GET') {
                $result = self::get($url, $params);
            } else {
                $result = self::post($url, $data);
            }
            
            $results[$name] = $result;
            
            // Sonucu yazdır
            if ($result['success']) {
                echo "✅ Başarılı - HTTP {$result['http_code']} ({$result['request_time']}ms)\n";
                
                if (isset($result['response']) && is_array($result['response'])) {
                    echo "📄 Response Preview:\n";
                    echo json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                }
            } else {
                echo "❌ Başarısız - " . ($result['error'] ?? 'Bilinmeyen hata') . "\n";
                if (isset($result['http_code'])) {
                    echo "   HTTP Code: {$result['http_code']}\n";
                }
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 Test Özeti\n";
        echo str_repeat("=", 60) . "\n";
        
        $successful = array_filter($results, fn($r) => $r['success']);
        $failed = array_filter($results, fn($r) => !$r['success']);
        
        echo "✅ Başarılı: " . count($successful) . "\n";
        echo "❌ Başarısız: " . count($failed) . "\n";
        echo "📊 Toplam: " . count($results) . "\n";
        
        return $results;
    }
    
    /**
     * TestMover API'sini test et
     * 
     * @return array Test sonuçları
     */
    public static function testMoverAPI()
    {
        $endpoints = [
            'status' => [
                'method' => 'GET',
                'url' => '/Tests/System/TestMover.php',
                'params' => ['action' => 'status']
            ],
            'list' => [
                'method' => 'GET', 
                'url' => '/Tests/System/TestMover.php',
                'params' => ['action' => 'list']
            ],
            'organize_dry' => [
                'method' => 'GET',
                'url' => '/Tests/System/TestMover.php', 
                'params' => ['action' => 'organize', 'dry' => 'true']
            ]
        ];
        
        return self::testEndpoints($endpoints);
    }
    
    /**
     * Domain erişilebilirlik kontrolü
     * 
     * @param string $host Host adı
     * @param int $port Port numarası
     * @return bool Erişilebilir mi?
     */
    private static function checkDomainReachable($host, $port = 80)
    {
        // localhost ve 127.0.0.1 için özel kontrol
        if (in_array($host, ['localhost', '127.0.0.1'])) {
            return true;
        }
        
        // l. ile başlayan yerel domainler için özel kontrol
        if (strpos($host, 'l.') === 0) {
            // hosts dosyasında tanımlı olabilir, kabul et
            return true;
        }
        
        // Normal domain için DNS kontrolü
        $dnsCheck = gethostbyname($host);
        if ($dnsCheck === $host) {
            // DNS çözümlemesi başarısız, ancak yerel network kontrol et
            TestLogger::warning("DNS çözümlemesi başarısız: $host");
            return true; // Yerel development için toleranslı yaklaşım
        }
        
        return true;
    }
    
    /**
     * Sistem bilgilerini yazdır
     * 
     * @return array Sistem bilgileri
     */
    public static function getSystemInfo()
    {
        self::init();
        
        $info = [
            'php_version' => PHP_VERSION,
            'curl_version' => curl_version(),
            'base_url' => self::$baseUrl,
            'os' => PHP_OS,
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
            'memory_peak' => round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB'
        ];
        
        return $info;
    }
    
    /**
     * Debug bilgilerini yazdır
     */
    public static function debug()
    {
        $info = self::getSystemInfo();
        
        echo "🔧 TestCurl Debug Bilgileri\n";
        echo str_repeat("=", 40) . "\n";
        echo "PHP Version: {$info['php_version']}\n";
        echo "CURL Version: {$info['curl_version']['version']}\n";
        echo "Base URL: {$info['base_url']}\n";
        echo "OS: {$info['os']}\n";
        echo "Memory Usage: {$info['memory_usage']}\n";
        echo "Memory Peak: {$info['memory_peak']}\n";
        echo "Timestamp: {$info['timestamp']}\n";
        echo str_repeat("=", 40) . "\n";
        
        // Test domain erişilebilirliği
        try {
            $testUrl = self::$baseUrl . '/';
            echo "\n🌐 Domain Erişilebilirlik Testi\n";
            echo "Test URL: $testUrl\n";
            
            $result = self::get('/');
            
            if ($result['success']) {
                echo "✅ Domain erişilebilir\n";
                echo "HTTP Code: {$result['http_code']}\n";
                echo "Response Time: {$result['request_time']}ms\n";
                if (isset($result['curl_info']['primary_ip'])) {
                    echo "IP: {$result['curl_info']['primary_ip']}\n";
                }
            } else {
                echo "❌ Domain erişilemiyor\n";
                echo "Hata: " . ($result['error'] ?? 'Bilinmeyen') . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Test hatası: " . $e->getMessage() . "\n";
        }
    }
}

// Komut satırından çalıştırılıyorsa
if (isset($argv) && basename(__FILE__) == basename($argv[0])) {
    echo "🌐 Test Curl Helper\n";
    echo "==================\n\n";
    
    if (isset($argv[1])) {
        switch ($argv[1]) {
            case 'debug':
                TestCurl::debug();
                break;
                
            case 'info':
                $info = TestCurl::getSystemInfo();
                echo "📊 Sistem Bilgileri:\n";
                foreach ($info as $key => $value) {
                    if (is_array($value)) {
                        echo "$key: " . json_encode($value) . "\n";
                    } else {
                        echo "$key: $value\n";
                    }
                }
                break;
                
            case 'mover':
                echo "🔧 TestMover API testi başlatılıyor...\n";
                TestCurl::testMoverAPI();
                break;
                
            case 'get':
                if (!isset($argv[2])) {
                    echo "Kullanım: php TestCurl.php get <url> [param1=value1] [param2=value2]\n";
                    break;
                }
                
                $url = $argv[2];
                $params = [];
                
                // Parametreleri parse et
                for ($i = 3; $i < count($argv); $i++) {
                    if (strpos($argv[$i], '=') !== false) {
                        list($key, $value) = explode('=', $argv[$i], 2);
                        $params[$key] = $value;
                    }
                }
                
                echo "GET isteği gönderiliyor: $url\n";
                if (!empty($params)) {
                    echo "Parametreler: " . json_encode($params) . "\n";
                }
                
                $result = TestCurl::get($url, $params);
                
                if ($result['success']) {
                    echo "\n✅ Başarılı (HTTP {$result['http_code']}, {$result['request_time']}ms):\n";
                    
                    if (isset($result['curl_info']['primary_ip'])) {
                        echo "IP: {$result['curl_info']['primary_ip']}:{$result['curl_info']['primary_port']}\n";
                    }
                    
                    if (isset($result['response']) && is_array($result['response'])) {
                        echo "Response:\n";
                        echo json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                    } else {
                        echo "Raw Response: " . substr($result['raw_response'], 0, 500) . "...\n";
                    }
                } else {
                    echo "\n❌ Hata:\n";
                    echo "Error: " . ($result['error'] ?? 'Bilinmeyen') . "\n";
                    if (isset($result['http_code'])) {
                        echo "HTTP Code: {$result['http_code']}\n";
                    }
                }
                break;
                
            case 'test':
                if (!isset($argv[2])) {
                    echo "Test URL'si gerekli\n";
                    echo "Kullanım: php TestCurl.php test <url>\n";
                    break;
                }
                
                $url = $argv[2];
                echo "Test ediliyor: $url\n";
                
                $result = TestCurl::get($url);
                
                echo "\n📊 Test Sonucu:\n";
                echo "HTTP Code: " . ($result['http_code'] ?? 'N/A') . "\n";
                echo "Response Time: " . ($result['request_time'] ?? 'N/A') . "ms\n";
                echo "Success: " . ($result['success'] ? 'Evet' : 'Hayır') . "\n";
                echo "Content Type: " . ($result['content_type'] ?? 'N/A') . "\n";
                
                if (isset($result['curl_info']['primary_ip'])) {
                    echo "IP: {$result['curl_info']['primary_ip']}:{$result['curl_info']['primary_port']}\n";
                }
                
                if (!$result['success'] && isset($result['error'])) {
                    echo "Hata: " . $result['error'] . "\n";
                }
                
                // Response preview
                if ($result['success'] && isset($result['response'])) {
                    echo "\nResponse Preview:\n";
                    if (is_array($result['response'])) {
                        echo json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                    } else {
                        echo substr($result['raw_response'], 0, 200) . "...\n";
                    }
                }
                break;
                
            default:
                echo "Kullanım:\n";
                echo "  php TestCurl.php debug                    # Sistem debug bilgileri\n";
                echo "  php TestCurl.php info                     # Sistem bilgileri\n";
                echo "  php TestCurl.php mover                    # TestMover API'sini test et\n";
                echo "  php TestCurl.php get <url> [params]      # GET isteği gönder\n";
                echo "  php TestCurl.php test <url>              # URL'yi hızlı test et\n";
                echo "\nÖrnekler:\n";
                echo "  php TestCurl.php get /Tests/System/TestMover.php action=status\n";
                echo "  php TestCurl.php test /Tests/System/test_organizer.html\n";
        }
    } else {
        echo "Kullanım:\n";
        echo "  php TestCurl.php debug                    # Sistem debug bilgileri\n";
        echo "  php TestCurl.php info                     # Sistem bilgileri\n";
        echo "  php TestCurl.php mover                    # TestMover API'sini test et\n";
        echo "  php TestCurl.php get <url> [params]      # GET isteği gönder\n";  
        echo "  php TestCurl.php test <url>              # URL'yi hızlı test et\n";
        echo "\nÖrnekler:\n";
        echo "  php TestCurl.php get /Tests/System/TestMover.php action=status\n";
        echo "  php TestCurl.php test /Tests/System/test_organizer.html\n";
    }
}
