<?php

class EnvChecker {
    
    private static function loadDefaults() {
        $defaultsFile = __DIR__ . '/../Config/env-default.json';
        if (!file_exists($defaultsFile)) {
            return null;
        }
        
        $content = file_get_contents($defaultsFile);
        return json_decode($content, true);
    }
    
    public static function checkMissingKeys() {
        $config = self::loadDefaults();
        if (!$config) {
            return ['error' => 'env-default.json bulunamadı'];
        }
        
        $missing = [];
        
        // AI servisleri için gerekli alanları kontrol et
        foreach ($config['required_for_ai'] as $key) {
            if (empty($_ENV[$key])) {
                $missing['ai'][] = $key;
            }
        }
        
        // Email servisleri için gerekli alanları kontrol et
        foreach ($config['required_for_email'] as $key) {
            if (empty($_ENV[$key])) {
                $missing['email'][] = $key;
            }
        }
        
        return $missing;
    }
    
    public static function getSetupWarnings() {
        $missing = self::checkMissingKeys();
        $warnings = [];
        
        if (!empty($missing['ai'])) {
            $warnings[] = [
                'type' => 'ai',
                'message' => "AI özellikleri çalışmayabilir",
                'missing' => $missing['ai']
            ];
        }
        
        if (!empty($missing['email'])) {
            $warnings[] = [
                'type' => 'email', 
                'message' => "Email gönderimi çalışmayabilir",
                'missing' => $missing['email']
            ];
        }
        
        return $warnings;
    }
    
    public static function getServiceStatus() {
        $missing = self::checkMissingKeys();
        
        return [
            'ai_ready' => empty($missing['ai']),
            'email_ready' => empty($missing['email']),
            'warnings' => self::getSetupWarnings()
        ];
    }
}
