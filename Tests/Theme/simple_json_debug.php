<?php
// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// Test başlat
TestHelper::startTest('Simple Theme JSON Debug Test');

try {
    // JSON dosyasını doğrudan oku
    $projectRoot = dirname(dirname(__DIR__));
    $jsonFile = $projectRoot . "/Public/Json/CSS/index.json";
    
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
        TestLogger::info("header-logo-width type: " . gettype($customCSS['header-logo-width']));
    } else {
        TestLogger::error("header-logo-width NOT found in customCSS");
    }
    
    // Logo ile ilgili tüm değerleri göster
    $logoKeys = array_filter(array_keys($customCSS), function($key) {
        return strpos($key, 'logo') !== false;
    });
    
    TestLogger::info("Logo related keys found: " . count($logoKeys));
    foreach ($logoKeys as $key) {
        TestLogger::info("Logo key: $key = " . $customCSS[$key]);
    }
    
    // Header ile ilgili tüm değerleri göster
    $headerKeys = array_filter(array_keys($customCSS), function($key) {
        return strpos($key, 'header-') !== false;
    });
    
    TestLogger::info("Header related keys found: " . count($headerKeys));
    foreach ($headerKeys as $key) {
        TestLogger::info("Header key: $key = " . $customCSS[$key]);
    }
    
    // sanitizeNumericValue fonksiyonunu test et
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
    
    // Test sanitizeNumericValue function with actual value
    if (isset($customCSS['header-logo-width'])) {
        $testValue = $customCSS['header-logo-width'];
        $sanitizedValue = sanitizeNumericValue($testValue);
        TestLogger::info("Original value: '$testValue', Sanitized: '$sanitizedValue'");
        
        // Test with fallback
        $sanitizedWithFallback = sanitizeNumericValue($testValue) ?: '150';
        TestLogger::info("With fallback: '$sanitizedWithFallback'");
    }
    
    TestLogger::success('Tüm testler başarılı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

// Test sonlandır
TestHelper::endTest();
