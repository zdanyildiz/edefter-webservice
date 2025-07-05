<?php
// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// Test başlat
TestHelper::startTest('Theme Values Debug Test');

try {
    // Theme.php benzeri değişkenler
    $languageID = 1;
    
    // JSON_DIR path'ini doğru çek
    $projectRoot = dirname(dirname(__DIR__));
    include_once $projectRoot . "/_y/s/global.php";
    
    TestLogger::info("JSON_DIR: " . JSON_DIR);
    
    // JSON dosyasının varlığını kontrol et
    $jsonFile = JSON_DIR . "CSS/index.json";
    TestLogger::info("JSON File path: " . $jsonFile);
    TestAssert::assertTrue(file_exists($jsonFile), 'index.json dosyası mevcut olmalı');
    
    // JSON içeriğini oku
    $content = file_get_contents($jsonFile);
    TestAssert::assertNotEmpty($content, 'JSON dosyası boş olmamalı');
    
    $customCSS = json_decode($content, true);
    TestAssert::assertEquals(JSON_ERROR_NONE, json_last_error(), 'JSON decode hatası olmamalı');
    
    TestLogger::info("customCSS array count: " . count($customCSS));
    
    // Özel olarak header-logo-width değerini kontrol et
    if (isset($customCSS['header-logo-width'])) {
        TestLogger::success("header-logo-width found: " . $customCSS['header-logo-width']);
    } else {
        TestLogger::error("header-logo-width NOT found in customCSS");
    }
    
    // İlk 10 key-value çiftini logla
    $count = 0;
    foreach ($customCSS as $key => $value) {
        if ($count < 10) {
            TestLogger::info("Key: $key = Value: $value");
            $count++;
        }
    }
    
    // Theme.php'de kullanılan sanitize fonksiyonlarını test et
    function sanitizeNumericValue($value, $unit = '', $default = 0) {
        if (empty($value)) {
            return $default;
        }

        // Extract numeric value and potential unit
        if (is_string($value) && preg_match('/^(\d+(?:\.\d+)?)(px|rem|em|%)?$/', $value, $matches)) {
            $numericValue = floatval($matches[1]);
            $numericValue = max(0, $numericValue);
            return !empty($unit) ? $numericValue . $unit : $numericValue;
        }

        // Fallback for purely numeric values without units
        $numericValue = floatval($value);
        $numericValue = max(0, $numericValue);
        return !empty($unit) ? $numericValue . $unit : $numericValue;
    }
    
    // Test sanitizeNumericValue function
    $testValue = $customCSS['header-logo-width'] ?? '150';
    $sanitizedValue = sanitizeNumericValue($testValue);
    TestLogger::info("Original value: $testValue, Sanitized: $sanitizedValue");
    
    TestLogger::success('Tüm testler başarılı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

// Test sonlandır
TestHelper::endTest();
