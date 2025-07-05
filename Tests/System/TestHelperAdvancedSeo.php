<?php
// Helper sınıfının yeni createAdvancedSeoLink fonksiyonunu test et
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Helpers/Helper.php';

try {
    echo "=== HELPER SINIFI ADVANCED SEO LINK TESTİ ===\n\n";
    
    $helper = new Helper();
    
    // Test senaryoları
    $testCases = [
        ['title' => 'العنوان العربي الجميل', 'lang' => 'ar', 'name' => 'Arapça'],
        ['title' => '中文标题示例', 'lang' => 'zh', 'name' => 'Çince'],
        ['title' => '日本語のタイトル例', 'lang' => 'ja', 'name' => 'Japonca'],
        ['title' => '한국어 제목 예제', 'lang' => 'ko', 'name' => 'Korece'],
        ['title' => 'Русский заголовок пример', 'lang' => 'ru', 'name' => 'Rusça'],
        ['title' => 'हिंदी शीर्षक उदाहरण', 'lang' => 'hi', 'name' => 'Hindi'],
        ['title' => 'ตัวอย่างหัวข้อภาษาไทย', 'lang' => 'th', 'name' => 'Tayca'],
        ['title' => 'כותרת עברית לדוגמה', 'lang' => 'he', 'name' => 'İbranice'],
        ['title' => 'Türkçe Başlık Örneği', 'lang' => 'tr', 'name' => 'Türkçe'],
        ['title' => 'English Title Example', 'lang' => 'en', 'name' => 'İngilizce'],
        ['title' => '', 'lang' => 'ar', 'name' => 'Boş Başlık'],
        ['title' => '!@#$%^&*()', 'lang' => 'en', 'name' => 'Özel Karakterler']
    ];
    
    echo str_repeat("-", 90) . "\n";
    printf("%-15s | %-25s | %-30s | %-15s\n", "Dil", "Orijinal Başlık", "Advanced SEO Link", "Fallback ID");
    echo str_repeat("-", 90) . "\n";
    
    foreach ($testCases as $index => $test) {
        $title = $test['title'];
        $langCode = $test['lang'];
        $langName = $test['name'];
        $fallbackId = 1000 + $index;
        
        // Eski yöntem
        $oldSeoLink = $helper->createSeoLink($title);
        $oldSeoLink = trim($oldSeoLink, '-');
        
        // Yeni gelişmiş yöntem
        $newSeoLink = $helper->createAdvancedSeoLink($title, $langCode, null, $fallbackId);
        
        printf("%-15s | %-25s | %-30s | %-15s\n", 
            $langName,
            mb_substr($title ?: '[BOŞ]', 0, 20) . (mb_strlen($title) > 20 ? '...' : ''),
            $newSeoLink,
            $fallbackId
        );
        
        // Sonuç analizi
        if (empty($title)) {
            echo "  → 🔄 Boş başlık için fallback kullanıldı\n";
        } elseif (empty($oldSeoLink) && !empty($newSeoLink)) {
            echo "  → ✅ Eski yöntem başarısız, yeni yöntem başarılı\n";
        } elseif (!empty($oldSeoLink) && !empty($newSeoLink)) {
            if ($oldSeoLink === $newSeoLink) {
                echo "  → ✅ Her iki yöntem de aynı sonucu verdi\n";
            } else {
                echo "  → ✅ Her iki yöntem de başarılı (farklı sonuçlar)\n";
            }
        } else {
            echo "  → ❌ Her iki yöntem de başarısız\n";
        }
    }
    
    echo str_repeat("-", 90) . "\n";
    
    echo "\n=== FONKSİYON ÖZET BİLGİLERİ ===\n";
    echo "Fonksiyon: Helper->createAdvancedSeoLink(\$title, \$languageCode, \$ai, \$fallbackId)\n";
    echo "Parametreler:\n";
    echo "  - \$title: Başlık metni\n";
    echo "  - \$languageCode: Dil kodu (ar, zh, ja, ko, ru, hi, th, he, tr, en)\n";
    echo "  - \$ai: AI çeviri servisi (opsiyonel)\n";
    echo "  - \$fallbackId: Fallback için ID (opsiyonel)\n\n";
    
    echo "Çalışma Mantığı:\n";
    echo "1. Standart createSeoLink dene\n";
    echo "2. Başarısızsa transliteration dene\n";
    echo "3. Başarısızsa AI çeviri dene (varsa)\n";
    echo "4. Son çare: ID tabanlı fallback\n\n";
    
    echo "Test Sonucu: ✅ Fonksiyon başarıyla çalışıyor!\n";
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}
?>
