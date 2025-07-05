<?php
/**
 * Test Validator Sınıfı
 * 
 * Test verilerini doğrulama ve kontrol için kullanılır
 * Ortak doğrulama kurallarını içerir
 * 
 * @author GitHub Copilot
 * @date 24 Haziran 2025
 */

class TestValidator {
    
    /**
     * E-posta adresini doğrula
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Telefon numarasını doğrula (Türkiye formatı)
     */
    public static function validatePhone($phone) {
        // Türkiye telefon formatları: +90, 0, 90 ile başlayan
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) == 11 && substr($phone, 0, 1) == '0') {
            return true; // 05xxxxxxxxx
        }
        
        if (strlen($phone) == 12 && substr($phone, 0, 2) == '90') {
            return true; // 905xxxxxxxxx
        }
        
        if (strlen($phone) == 10 && substr($phone, 0, 1) == '5') {
            return true; // 5xxxxxxxxx
        }
        
        return false;
    }
    
    /**
     * URL doğrula
     */
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * IP adresi doğrula
     */
    public static function validateIP($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    
    /**
     * Tarih formatı doğrula
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * JSON formatı doğrula
     */
    public static function validateJson($json) {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Sayısal değer doğrula
     */
    public static function validateNumeric($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }
        
        $value = (float) $value;
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * String uzunluğu doğrula
     */
    public static function validateStringLength($string, $minLength = 0, $maxLength = null) {
        $length = strlen($string);
        
        if ($length < $minLength) {
            return false;
        }
        
        if ($maxLength !== null && $length > $maxLength) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Required alanlar kontrolü
     */
    public static function validateRequired($data, $requiredFields) {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        return empty($missing) ? true : $missing;
    }
    
    /**
     * Veri tipi kontrolü
     */
    public static function validateDataType($value, $expectedType) {
        switch (strtolower($expectedType)) {
            case 'string':
                return is_string($value);
            case 'integer':
            case 'int':
                return is_int($value);
            case 'float':
            case 'double':
                return is_float($value);
            case 'boolean':
            case 'bool':
                return is_bool($value);
            case 'array':
                return is_array($value);
            case 'object':
                return is_object($value);
            case 'null':
                return is_null($value);
            default:
                return false;
        }
    }
    
    /**
     * Veritabanı ID doğrula
     */
    public static function validateDatabaseId($id) {
        return is_numeric($id) && $id > 0 && $id == (int) $id;
    }
    
    /**
     * Hash doğrula (MD5, SHA1, SHA256)
     */
    public static function validateHash($hash, $type = 'md5') {
        $lengths = [
            'md5' => 32,
            'sha1' => 40,
            'sha256' => 64
        ];
        
        if (!isset($lengths[$type])) {
            return false;
        }
        
        return strlen($hash) === $lengths[$type] && ctype_xdigit($hash);
    }
    
    /**
     * Türkçe karakter kontrolü
     */
    public static function containsTurkishChars($text) {
        return preg_match('/[çğıöşüÇĞIİÖŞÜ]/', $text) === 1;
    }
    
    /**
     * SQL Injection riski kontrolü
     */
    public static function checkSqlInjectionRisk($input) {
        $dangerous = [
            'select', 'insert', 'update', 'delete', 'drop', 'create',
            'alter', 'truncate', 'union', 'exec', 'script', '--',
            ';', '/*', '*/', 'xp_', 'sp_'
        ];
        
        $input = strtolower($input);
        
        foreach ($dangerous as $pattern) {
            if (strpos($input, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * XSS riski kontrolü
     */
    public static function checkXssRisk($input) {
        $dangerous = [
            '<script', '</script>', 'javascript:', 'onclick=',
            'onload=', 'onerror=', 'onmouseover=', 'eval(',
            'document.cookie', 'document.write'
        ];
        
        $input = strtolower($input);
        
        foreach ($dangerous as $pattern) {
            if (strpos($input, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Toplu veri doğrulama
     */
    public static function validateBatch($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = isset($data[$field]) ? $data[$field] : null;
            
            foreach ($fieldRules as $rule => $params) {
                $valid = true;
                
                switch ($rule) {
                    case 'required':
                        $valid = !empty($value);
                        break;
                    case 'email':
                        $valid = self::validateEmail($value);
                        break;
                    case 'phone':
                        $valid = self::validatePhone($value);
                        break;
                    case 'url':
                        $valid = self::validateUrl($value);
                        break;
                    case 'numeric':
                        $min = isset($params['min']) ? $params['min'] : null;
                        $max = isset($params['max']) ? $params['max'] : null;
                        $valid = self::validateNumeric($value, $min, $max);
                        break;
                    case 'length':
                        $min = isset($params['min']) ? $params['min'] : 0;
                        $max = isset($params['max']) ? $params['max'] : null;
                        $valid = self::validateStringLength($value, $min, $max);
                        break;
                    case 'type':
                        $valid = self::validateDataType($value, $params);
                        break;
                }
                
                if (!$valid) {
                    if (!isset($errors[$field])) {
                        $errors[$field] = [];
                    }
                    $errors[$field][] = $rule;
                }
            }
        }
          return empty($errors) ? true : $errors;
    }
    
    /**
     * Parola güvenlik seviyesi hesapla (0-5 arası)
     */
    public static function getPasswordStrength($password) {
        $score = 0;
        
        // Uzunluk kontrolü
        if (strlen($password) >= 8) {
            $score++;
        }
        
        // Büyük harf kontrolü
        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        }
        
        // Küçük harf kontrolü
        if (preg_match('/[a-z]/', $password)) {
            $score++;
        }
        
        // Sayı kontrolü
        if (preg_match('/[0-9]/', $password)) {
            $score++;
        }
        
        // Özel karakter kontrolü
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score++;
        }
        
        return $score;
    }
}
