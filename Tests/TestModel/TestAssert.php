<?php
/**
 * Test Assert Sınıfı
 * 
 * Test sonuçlarını doğrulamak için assertion metodları
 * PHPUnit benzeri assertion sistemi
 * 
 * @author GitHub Copilot
 * @date 24 Haziran 2025
 */

class TestAssert {
    
    private static $assertionCount = 0;
    private static $failedAssertions = 0;
    
    /**
     * Assertion sayacını sıfırla
     */
    public static function resetCounters() {
        self::$assertionCount = 0;
        self::$failedAssertions = 0;
    }
    
    /**
     * Assertion istatistiklerini al
     */
    public static function getStats() {
        return [
            'total' => self::$assertionCount,
            'failed' => self::$failedAssertions,
            'passed' => self::$assertionCount - self::$failedAssertions
        ];
    }
    
    /**
     * İki değerin eşit olduğunu doğrula
     */
    public static function assertEquals($expected, $actual, $message = '') {
        self::$assertionCount++;
        
        if ($expected === $actual) {
            TestLogger::success("Assert Passed: assertEquals" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertEquals" . ($message ? " - $message" : "") . 
                    " | Expected: " . self::valueToString($expected) . 
                    " | Actual: " . self::valueToString($actual);
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * İki değerin eşit olmadığını doğrula
     */
    public static function assertNotEquals($notExpected, $actual, $message = '') {
        self::$assertionCount++;
        
        if ($notExpected !== $actual) {
            TestLogger::success("Assert Passed: assertNotEquals" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertNotEquals" . ($message ? " - $message" : "") . 
                    " | Not Expected: " . self::valueToString($notExpected) . 
                    " | Actual: " . self::valueToString($actual);
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Değerin true olduğunu doğrula
     */
    public static function assertTrue($actual, $message = '') {
        return self::assertEquals(true, $actual, $message ?: 'Value should be true');
    }
    
    /**
     * Değerin false olduğunu doğrula
     */
    public static function assertFalse($actual, $message = '') {
        return self::assertEquals(false, $actual, $message ?: 'Value should be false');
    }
    
    /**
     * Değerin null olduğunu doğrula
     */
    public static function assertNull($actual, $message = '') {
        return self::assertEquals(null, $actual, $message ?: 'Value should be null');
    }
    
    /**
     * Değerin null olmadığını doğrula
     */
    public static function assertNotNull($actual, $message = '') {
        return self::assertNotEquals(null, $actual, $message ?: 'Value should not be null');
    }
    
    /**
     * Değerin empty olduğunu doğrula
     */
    public static function assertEmpty($actual, $message = '') {
        self::$assertionCount++;
        
        if (empty($actual)) {
            TestLogger::success("Assert Passed: assertEmpty" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertEmpty" . ($message ? " - $message" : "") . 
                    " | Actual: " . self::valueToString($actual);
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Değerin empty olmadığını doğrula
     */
    public static function assertNotEmpty($actual, $message = '') {
        self::$assertionCount++;
        
        if (!empty($actual)) {
            TestLogger::success("Assert Passed: assertNotEmpty" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertNotEmpty" . ($message ? " - $message" : "") . 
                    " | Value is empty";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Array içeriğini doğrula
     */
    public static function assertArrayHasKey($key, $array, $message = '') {
        self::$assertionCount++;
        
        if (is_array($array) && array_key_exists($key, $array)) {
            TestLogger::success("Assert Passed: assertArrayHasKey" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertArrayHasKey" . ($message ? " - $message" : "") . 
                    " | Key '$key' not found in array";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * String içeriğini doğrula
     */
    public static function assertStringContains($needle, $haystack, $message = '') {
        self::$assertionCount++;
        
        if (is_string($haystack) && strpos($haystack, $needle) !== false) {
            TestLogger::success("Assert Passed: assertStringContains" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertStringContains" . ($message ? " - $message" : "") . 
                    " | '$needle' not found in '$haystack'";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Sayısal karşılaştırma (büyük)
     */
    public static function assertGreaterThan($expected, $actual, $message = '') {
        self::$assertionCount++;
        
        if (is_numeric($actual) && is_numeric($expected) && $actual > $expected) {
            TestLogger::success("Assert Passed: assertGreaterThan" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertGreaterThan" . ($message ? " - $message" : "") . 
                    " | Expected: $actual > $expected";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Sayısal karşılaştırma (küçük)
     */
    public static function assertLessThan($expected, $actual, $message = '') {
        self::$assertionCount++;
        
        if (is_numeric($actual) && is_numeric($expected) && $actual < $expected) {
            TestLogger::success("Assert Passed: assertLessThan" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertLessThan" . ($message ? " - $message" : "") . 
                    " | Expected: $actual < $expected";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Array boyutu kontrolü
     */
    public static function assertCount($expectedCount, $array, $message = '') {
        self::$assertionCount++;
        
        $actualCount = is_array($array) ? count($array) : 0;
        
        if ($actualCount === $expectedCount) {
            TestLogger::success("Assert Passed: assertCount" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertCount" . ($message ? " - $message" : "") . 
                    " | Expected: $expectedCount | Actual: $actualCount";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Veri tipini doğrula
     */
    public static function assertInstanceOf($expectedType, $actual, $message = '') {
        self::$assertionCount++;
        
        $actualType = gettype($actual);
        if (is_object($actual)) {
            $actualType = get_class($actual);
        }
        
        if ($actualType === $expectedType || (is_object($actual) && $actual instanceof $expectedType)) {
            TestLogger::success("Assert Passed: assertInstanceOf" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertInstanceOf" . ($message ? " - $message" : "") . 
                    " | Expected: $expectedType | Actual: $actualType";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Regex pattern kontrolü
     */
    public static function assertMatchesRegex($pattern, $string, $message = '') {
        self::$assertionCount++;
        
        if (preg_match($pattern, $string)) {
            TestLogger::success("Assert Passed: assertMatchesRegex" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertMatchesRegex" . ($message ? " - $message" : "") . 
                    " | Pattern: $pattern | String: $string";
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * JSON format kontrolü
     */
    public static function assertJson($jsonString, $message = '') {
        self::$assertionCount++;
        
        json_decode($jsonString);
        $jsonValid = (json_last_error() === JSON_ERROR_NONE);
        
        if ($jsonValid) {
            TestLogger::success("Assert Passed: assertJson" . ($message ? " - $message" : ""));
            return true;
        } else {
            self::$failedAssertions++;
            $error = "Assert Failed: assertJson" . ($message ? " - $message" : "") . 
                    " | Invalid JSON: " . json_last_error_msg();
            TestLogger::error($error);
            return false;
        }
    }
    
    /**
     * Değeri string'e çevir (debug için)
     */
    private static function valueToString($value) {
        if (is_null($value)) {
            return 'null';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            return 'Array(' . count($value) . ')';
        } elseif (is_object($value)) {
            return get_class($value) . ' Object';
        } else {
            return (string) $value;
        }
    }
    
    /**
     * Test sonuçlarını özetle
     */
    public static function summary() {
        $stats = self::getStats();
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "TEST ASSERTION ÖZET\n";
        echo str_repeat("=", 60) . "\n";
        echo "Toplam Assertion: " . $stats['total'] . "\n";
        echo "Başarılı: " . $stats['passed'] . " ✅\n";
        echo "Başarısız: " . $stats['failed'] . " ❌\n";
        
        if ($stats['failed'] === 0) {
            echo "\n🎉 TÜM TESTLER BAŞARILI!\n";
        } else {
            echo "\n⚠️ " . $stats['failed'] . " TEST BAŞARISIZ!\n";
        }
        
        echo str_repeat("=", 60) . "\n";
        
        TestLogger::info("Test assertion özeti", $stats);
        
        return $stats['failed'] === 0;
    }
}
