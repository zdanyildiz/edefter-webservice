<?php

class EnvHelper {
    private static $envData = [];
    private static $loaded = false;

    /**
     * .env dosyasını yükler
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(dirname(dirname(__FILE__))) . '/.env';
        }

        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Yorum satırlarını atla
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // = işareti yoksa atla
            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Tırnak işaretlerini kaldır
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }

            self::$envData[$key] = $value;
            
            // Ortam değişkeni olarak da ayarla
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }

        self::$loaded = true;
    }

    /**
     * Env değerini getirir
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        return self::$envData[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /**
     * .env dosyasının var olup olmadığını kontrol eder
     */
    public static function exists($path = null) {
        if ($path === null) {
            $path = dirname(dirname(dirname(__FILE__))) . '/.env';
        }
        return file_exists($path);
    }

    /**
     * Gerekli env değerlerinin var olup olmadığını kontrol eder
     */
    public static function checkRequired($required = []) {
        $missing = [];
        
        foreach ($required as $key) {
            if (self::get($key) === null) {
                $missing[] = $key;
            }
        }
        
        return $missing;
    }

    /**
     * .env dosyası oluşturur
     */
    public static function create($data, $path = null) {
        if ($path === null) {
            $path = dirname(dirname(dirname(__FILE__))) . '/.env';
        }

        $content = '';
        $sections = [
            'Application' => ['APP_KEY', 'APP_ENV', 'APP_DEBUG'],
            'Database Production' => ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'],
            'Database Local' => ['DB_LOCAL_HOST', 'DB_LOCAL_DATABASE', 'DB_LOCAL_USERNAME', 'DB_LOCAL_PASSWORD'],
            'Domains' => ['APP_DOMAIN', 'APP_DOMAINS'],
            'External Services' => ['CLOUDFLARE_API_KEY', 'CLOUDFLARE_EMAIL', 'CLOUDFLARE_ACCOUNT_ID', 
                                   'PLESK_SERVER', 'PLESK_USER', 'PLESK_PASSWORD'],
            'Server Configuration' => ['SERVER_IP', 'SSH_USERNAME', 'SSH_PASSWORD'],
            'GitHub Configuration' => ['GITHUB_TOKEN']
        ];

        foreach ($sections as $section => $keys) {
            $sectionHasValues = false;
            $sectionContent = "# $section\n";
            
            foreach ($keys as $key) {
                if (isset($data[$key])) {
                    $sectionContent .= "$key=" . $data[$key] . "\n";
                    $sectionHasValues = true;
                }
            }
            
            if ($sectionHasValues) {
                $content .= $sectionContent . "\n";
            }
        }

        $result = @file_put_contents($path, trim($content));

        if ($result === false) {
            $error = error_get_last();
            throw new \Exception(".env dosyası yazılamadı: " . ($error['message'] ?? 'Bilinmeyen bir dosya sistemi hatası. İzinleri kontrol edin.'));
        }

        return true;
    }
}
?>