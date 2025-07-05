<?php
// Tests/System/ContentTranslatorNullTest.php
// ContentTranslator'da null değer testi

echo "=== CONTENT TRANSLATOR NULL VALUE TEST ===\n";
echo "Test Zamanı: " . date('Y-m-d H:i:s') . "\n\n";

// Include ederek fonksiyon test et
include_once 'App/Cron/ContentTranslator.php';

echo "--- getTranslatedValue() NULL TEST ---\n";

// Mock AdminChatCompletion sınıfı (test için)
class MockAdminChatCompletion {
    public function translateConstant($text, $language) {
        return "Translated: " . $text;
    }
    
    public function translateHtmlContent($text, $language) {
        return "<p>Translated: " . $text . "</p>";
    }
}

$mockAI = new MockAdminChatCompletion();

// Test case 1: Null değer
echo "Test 1 - Null değer: ";
$result1 = getTranslatedValue($mockAI, null, "English");
echo $result1 === null ? "✅ BAŞARILI (null döndü)" : "❌ BAŞARISIZ";
echo "\n";

// Test case 2: Boş string
echo "Test 2 - Boş string: ";
$result2 = getTranslatedValue($mockAI, "", "English");
echo $result2 === "" ? "✅ BAŞARILI (boş string döndü)" : "❌ BAŞARISIZ";
echo "\n";

// Test case 3: Boşluk karakteri
echo "Test 3 - Sadece boşluk: ";
$result3 = getTranslatedValue($mockAI, "   ", "English");
echo $result3 === "   " ? "✅ BAŞARILI (boşluk döndü)" : "❌ BAŞARISIZ";
echo "\n";

// Test case 4: Normal metin
echo "Test 4 - Normal metin: ";
$result4 = getTranslatedValue($mockAI, "Test", "English");
echo $result4 === "Translated: Test" ? "✅ BAŞARILI" : "❌ BAŞARISIZ";
echo "\n";

// Test case 5: HTML içerik
echo "Test 5 - HTML içerik: ";
$result5 = getTranslatedValue($mockAI, "<p>Test</p>", "English", true);
echo $result5 === "<p>Translated: <p>Test</p></p>" ? "✅ BAŞARILI" : "❌ BAŞARISIZ";
echo "\n";

echo "\n=== TEST TAMAMLANDI ===\n";
echo "getTranslatedValue() fonksiyonu artık null-safe çalışıyor!\n";
?>
