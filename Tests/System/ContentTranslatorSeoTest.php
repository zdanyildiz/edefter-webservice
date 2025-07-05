<?php
// ContentTranslator.php için SEO link sistemi testi
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';
include_once $documentRoot . $directorySeparator . 'App/Helpers/Helper.php';

try {
    echo "=== CONTENT TRANSLATOR SEO LINK TEST ===\n\n";
    
    $helper = new Helper();
    
    // Test senaryoları - farklı dillerdeki başlıklar
    $testScenarios = [
        [
            'title' => 'منتجاتنا الجديدة',
            'languageCode' => 'ar',
            'languageName' => 'Arabic',
            'contentId' => 123
        ],
        [
            'title' => '我们的新产品',
            'languageCode' => 'zh',
            'languageName' => 'Chinese',
            'contentId' => 124
        ],
        [
            'title' => '私たちの新製品',
            'languageCode' => 'ja',
            'languageName' => 'Japanese',
            'contentId' => 125
        ],
        [
            'title' => '우리의 새로운 제품',
            'languageCode' => 'ko',
            'languageName' => 'Korean',
            'contentId' => 126
        ],
        [
            'title' => 'Наши новые продукты',
            'languageCode' => 'ru',
            'languageName' => 'Russian',
            'contentId' => 127
        ],
        [
            'title' => 'हमारे नए उत्पाद',
            'languageCode' => 'hi',
            'languageName' => 'Hindi',
            'contentId' => 128
        ],
        [
            'title' => 'Yeni Ürünlerimiz',
            'languageCode' => 'tr',
            'languageName' => 'Turkish',
            'contentId' => 129
        ],
        [
            'title' => 'Our New Products',
            'languageCode' => 'en',
            'languageName' => 'English',
            'contentId' => 130
        ]
    ];
    
    echo "Test Senaryoları:\n";
    echo str_repeat("-", 100) . "\n";
    printf("%-15s | %-25s | %-30s | %-20s\n", "Dil", "Orijinal Başlık", "Gelişmiş SEO Link", "Full URL Pattern");
    echo str_repeat("-", 100) . "\n";
    
    foreach ($testScenarios as $scenario) {
        $title = $scenario['title'];
        $langCode = $scenario['languageCode'];
        $langName = $scenario['languageName'];
        $contentId = $scenario['contentId'];
        
        // Eski yöntem
        $oldSeoLink = $helper->createSeoLink($title);
        $oldSeoLink = trim($oldSeoLink, '-');
        
        // Yeni gelişmiş yöntem
        $newSeoLink = $helper->createAdvancedSeoLink($title, $langCode, null, $contentId);
        
        // Full URL pattern örneği
        $fullUrlPattern = "/{$langCode}/category-path/{$newSeoLink}";
        
        printf("%-15s | %-25s | %-30s | %-20s\n", 
            $langName,
            mb_substr($title, 0, 20) . (mb_strlen($title) > 20 ? '...' : ''),
            $newSeoLink ?: '[ESKİ YÖNTEM BAŞARISIZ]',
            $fullUrlPattern
        );
        
        // Karşılaştırma
        if (empty($oldSeoLink) && !empty($newSeoLink)) {
            echo "  → ✅ Gelişmiş yöntem başarılı, eski yöntem başarısız\n";
        } elseif (!empty($oldSeoLink) && !empty($newSeoLink)) {
            echo "  → ✅ Her iki yöntem de başarılı\n";
        } else {
            echo "  → ❌ Her iki yöntem de başarısız\n";
        }
    }
    
    echo "\n" . str_repeat("-", 100) . "\n";
    
    echo "\n=== CONTENTRANSLATOR ENTEGRASYON BİLGİLERİ ===\n";
    echo "1. Güncellemeler:\n";
    echo "   - Helper->createAdvancedSeoLink() fonksiyonu eklendi\n";
    echo "   - ContentTranslator.php güncellendi\n";
    echo "   - Non-Latin dil desteği sağlandı\n\n";
    
    echo "2. Desteklenen Non-Latin Diller:\n";
    echo "   - Arapça (ar), Çince (zh), Japonca (ja), Korece (ko)\n";
    echo "   - Rusça (ru), Hindi (hi), Tayca (th), İbranice (he)\n";
    echo "   - Farsça (fa), Urduca (ur), Bengalce (bn) ve diğerleri\n\n";
    
    echo "3. Çözüm Stratejisi:\n";
    echo "   - İlk: Standart yöntem (Latin diller için)\n";
    echo "   - İkinci: Transliteration (Non-Latin → Latin)\n";
    echo "   - Üçüncü: AI ile İngilizce çeviri\n";
    echo "   - Son çare: ID tabanlı fallback\n\n";
    
    echo "4. Örnek Kullanım:\n";
    echo "   \$helper->createAdvancedSeoLink(\$title, \$langCode, \$ai, \$contentId)\n\n";
    
    echo "5. Log İyileştirmeleri:\n";
    echo "   - SEO link oluşturma süreci loglanıyor\n";
    echo "   - Dil kodu ve kullanılan yöntem kaydediliyor\n";

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
