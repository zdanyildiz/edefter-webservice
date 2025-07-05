<?php
// Helper sınıfına eklenecek gelişmiş SEO link sistemi

// Test modunda Helper sınıfını dahil et
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
    $directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
    include_once $documentRoot . $directorySeparator . 'App/Helpers/Helper.php';
}

class AdvancedSeoHelper {
    
    /**
     * Non-Latin karakterleri Latin karakterlere çeviren transliteration tablosu
     */
    private static $transliterationMap = [
        // Arapça
        'ا' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j', 'ح' => 'h', 'خ' => 'kh',
        'د' => 'd', 'ذ' => 'dh', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's',
        'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q',
        'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ه' => 'h', 'و' => 'w', 'ي' => 'y',
        'ة' => 'h', 'ى' => 'a', 'ء' => 'a',
        
        // Rus alfabesi
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        
        // Çince (Pinyin benzeri)
        '中' => 'zhong', '文' => 'wen', '标' => 'biao', '题' => 'ti', '示' => 'shi', '例' => 'li',
        
        // Japonca (Romaji benzeri)
        'あ' => 'a', 'い' => 'i', 'う' => 'u', 'え' => 'e', 'お' => 'o',
        'か' => 'ka', 'き' => 'ki', 'く' => 'ku', 'け' => 'ke', 'こ' => 'ko',
        'が' => 'ga', 'ぎ' => 'gi', 'ぐ' => 'gu', 'げ' => 'ge', 'ご' => 'go',
        'さ' => 'sa', 'し' => 'shi', 'す' => 'su', 'せ' => 'se', 'そ' => 'so',
        'ざ' => 'za', 'じ' => 'ji', 'ず' => 'zu', 'ぜ' => 'ze', 'ぞ' => 'zo',
        'た' => 'ta', 'ち' => 'chi', 'つ' => 'tsu', 'て' => 'te', 'と' => 'to',
        'だ' => 'da', 'ぢ' => 'ji', 'づ' => 'zu', 'で' => 'de', 'ど' => 'do',
        'な' => 'na', 'に' => 'ni', 'ぬ' => 'nu', 'ね' => 'ne', 'の' => 'no',
        'は' => 'ha', 'ひ' => 'hi', 'ふ' => 'fu', 'へ' => 'he', 'ほ' => 'ho',
        'ば' => 'ba', 'び' => 'bi', 'ぶ' => 'bu', 'べ' => 'be', 'ぼ' => 'bo',
        'ぱ' => 'pa', 'ぴ' => 'pi', 'ぷ' => 'pu', 'ぺ' => 'pe', 'ぽ' => 'po',
        'ま' => 'ma', 'み' => 'mi', 'む' => 'mu', 'め' => 'me', 'も' => 'mo',
        'や' => 'ya', 'ゆ' => 'yu', 'よ' => 'yo',
        'ら' => 'ra', 'り' => 'ri', 'る' => 'ru', 'れ' => 're', 'ろ' => 'ro',
        'わ' => 'wa', 'ゐ' => 'wi', 'ゑ' => 'we', 'を' => 'wo', 'ん' => 'n',
        
        // Kanji yaygın karakterler
        '日' => 'nichi', '本' => 'hon', '語' => 'go', '人' => 'jin', '時' => 'ji',
        
        // Korece
        '한' => 'han', '국' => 'guk', '어' => 'eo', '제' => 'je', '목' => 'mok', '예' => 'ye',
        
        // Hindi (Devanagari)
        'अ' => 'a', 'आ' => 'aa', 'इ' => 'i', 'ई' => 'ii', 'उ' => 'u', 'ऊ' => 'uu',
        'ए' => 'e', 'ऐ' => 'ai', 'ओ' => 'o', 'औ' => 'au',
        'क' => 'ka', 'ख' => 'kha', 'ग' => 'ga', 'घ' => 'gha', 'ङ' => 'nga',
        'च' => 'cha', 'छ' => 'chha', 'ज' => 'ja', 'झ' => 'jha', 'ञ' => 'nya',
        'ट' => 'ta', 'ठ' => 'tha', 'ड' => 'da', 'ढ' => 'dha', 'ण' => 'na',
        'त' => 'ta', 'थ' => 'tha', 'द' => 'da', 'ध' => 'dha', 'न' => 'na',
        'प' => 'pa', 'फ' => 'pha', 'ब' => 'ba', 'भ' => 'bha', 'म' => 'ma',
        'य' => 'ya', 'र' => 'ra', 'ल' => 'la', 'व' => 'va',
        'श' => 'sha', 'ष' => 'sha', 'स' => 'sa', 'ह' => 'ha',
        
        // İbranice
        'א' => 'a', 'ב' => 'b', 'ג' => 'g', 'ד' => 'd', 'ה' => 'h', 'ו' => 'v',
        'ז' => 'z', 'ח' => 'ch', 'ט' => 't', 'י' => 'y', 'כ' => 'k', 'ל' => 'l',
        'מ' => 'm', 'ן' => 'n', 'נ' => 'n', 'ס' => 's', 'ע' => 'a', 'פ' => 'p',
        'ץ' => 'ts', 'צ' => 'ts', 'ק' => 'k', 'ר' => 'r', 'ש' => 'sh', 'ת' => 't',
        
        // Tayca
        'ก' => 'k', 'ข' => 'kh', 'ค' => 'kh', 'ง' => 'ng', 'จ' => 'ch', 'ฉ' => 'ch',
        'ช' => 'ch', 'ซ' => 's', 'ฌ' => 'ch', 'ญ' => 'y', 'ด' => 'd', 'ต' => 't',
        'ถ' => 'th', 'ท' => 'th', 'ธ' => 'th', 'น' => 'n', 'บ' => 'b', 'ป' => 'p',
        'ผ' => 'ph', 'ฝ' => 'f', 'พ' => 'ph', 'ฟ' => 'f', 'ภ' => 'ph', 'ม' => 'm',
        'ย' => 'y', 'ร' => 'r', 'ล' => 'l', 'ว' => 'w', 'ศ' => 's', 'ษ' => 's',
        'ส' => 's', 'ห' => 'h', 'ฬ' => 'l', 'อ' => 'o', 'ฮ' => 'h',
        'า' => 'a', 'ิ' => 'i', 'ี' => 'i', 'ึ' => 'ue', 'ื' => 'ue', 'ุ' => 'u',
        'ู' => 'u', 'เ' => 'e', 'แ' => 'ae', 'โ' => 'o', 'ใ' => 'ai', 'ไ' => 'ai'
    ];
    
    /**
     * Dil kodlarına göre non-Latin dil tanımlama
     */
    private static $nonLatinLanguages = [
        'ar' => 'Arabic',
        'zh' => 'Chinese', 
        'zh-cn' => 'Chinese Simplified',
        'zh-tw' => 'Chinese Traditional',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'ru' => 'Russian',
        'hi' => 'Hindi',
        'th' => 'Thai',
        'he' => 'Hebrew',
        'fa' => 'Persian',
        'ur' => 'Urdu',
        'bn' => 'Bengali',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'ml' => 'Malayalam',
        'kn' => 'Kannada',
        'gu' => 'Gujarati',
        'pa' => 'Punjabi',
        'ne' => 'Nepali',
        'si' => 'Sinhala',
        'my' => 'Myanmar',
        'km' => 'Khmer',
        'lo' => 'Lao',
        'ka' => 'Georgian',
        'hy' => 'Armenian',
        'am' => 'Amharic',
        'ti' => 'Tigrinya'
    ];
    
    /**
     * İleri seviye SEO link oluşturma
     * 
     * @param string $title - Başlık
     * @param string $languageCode - Dil kodu (tr, en, ar, etc.)
     * @param AdminChatCompletion|null $ai - AI servisi (İngilizce çeviri için)
     * @param int|null $fallbackId - Fallback için ID
     * @return string
     */
    public static function createAdvancedSeoLink($title, $languageCode = 'tr', $ai = null, $fallbackId = null) {
        
        // Boş kontrol
        if (empty($title)) {
            return $fallbackId ? "content-$fallbackId" : 'content';
        }
        
        // İlk olarak standart yöntem deneyelim
        $helper = new Helper();
        $standardLink = $helper->createSeoLink($title);
        $standardLink = trim($standardLink, '-');
        
        // Eğer standart yöntem başarılıysa ve yeterli uzunluktaysa kullan
        if (!empty($standardLink) && strlen($standardLink) >= 3) {
            return $standardLink;
        }
        
        // Non-Latin dil kontrolü
        if (!isset(self::$nonLatinLanguages[$languageCode])) {
            // Latin tabanlı dil ama sorun var, fallback kullan
            return $fallbackId ? "content-$fallbackId" : self::generateRandomSlug($title);
        }
        
        // Transliteration dene
        $transliteratedTitle = self::transliterate($title);
        if (!empty($transliteratedTitle) && strlen($transliteratedTitle) >= 3) {
            $seoLink = self::cleanTransliteratedText($transliteratedTitle);
            if (!empty($seoLink)) {
                return $seoLink;
            }
        }
        
        // AI ile İngilizce çeviri dene (eğer AI servis mevcutsa)
        if ($ai) {
            try {
                $englishTitle = $ai->translateConstant($title, 'English');
                if (!empty($englishTitle)) {
                    $englishSeoLink = $helper->createSeoLink($englishTitle);
                    $englishSeoLink = trim($englishSeoLink, '-');
                    if (!empty($englishSeoLink) && strlen($englishSeoLink) >= 3) {
                        return $englishSeoLink;
                    }
                }
            } catch (Exception $e) {
                // AI çeviri başarısız, devam et
            }
        }
        
        // Son çare: ID tabanlı veya random slug
        return $fallbackId ? "content-$fallbackId" : self::generateRandomSlug($title);
    }
    
    /**
     * Karakterleri transliterate et
     */
    private static function transliterate($text) {
        $result = '';
        $textArray = mb_str_split($text, 1, 'UTF-8');
        
        foreach ($textArray as $char) {
            if (isset(self::$transliterationMap[$char])) {
                $result .= self::$transliterationMap[$char];
            } elseif (preg_match('/[a-zA-Z0-9\s\-_]/', $char)) {
                // Latin karakter, sayı, boşluk veya özel karakterler geçsin
                $result .= $char;
            } else {
                // Bilinmeyen karakter, atla veya '-' yap
                $result .= '-';
            }
        }
        
        return $result;
    }
    
    /**
     * Transliterate edilmiş metni temizle
     */
    private static function cleanTransliteratedText($text) {
        // Küçük harfe çevir
        $text = mb_strtolower($text, 'UTF-8');
        
        // Boşlukları '-' yap
        $text = preg_replace('/\s+/', '-', $text);
        
        // Sadece harf, sayı ve '-' kalsın
        $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
        
        // Ardışık '-' karakterlerini tek '-' yap
        $text = preg_replace('/-+/', '-', $text);
        
        // Baş ve sondaki '-' karakterlerini temizle
        $text = trim($text, '-');
        
        return $text;
    }
    
    /**
     * Random slug oluştur (son çare)
     */
    private static function generateRandomSlug($originalTitle) {
        $length = min(mb_strlen($originalTitle, 'UTF-8'), 10);
        $randomString = 'content-' . bin2hex(random_bytes(4));
        return $randomString;
    }
    
    /**
     * Bir dil kodunun non-Latin olup olmadığını kontrol et
     */
    public static function isNonLatinLanguage($languageCode) {
        return isset(self::$nonLatinLanguages[$languageCode]);
    }
    
    /**
     * Desteklenen non-Latin dillerin listesi
     */
    public static function getSupportedNonLatinLanguages() {
        return self::$nonLatinLanguages;
    }
}

// Test scriptleri
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    echo "=== GELİŞMİŞ SEO LINK SİSTEMİ TESTİ ===\n\n";
    
    $testCases = [
        ['العنوان العربي الجميل', 'ar', 'Arapça'],
        ['中文标题示例', 'zh', 'Çince'],
        ['日本語のタイトル例', 'ja', 'Japonca'],
        ['한국어 제목 예제', 'ko', 'Korece'],
        ['Русский заголовок пример', 'ru', 'Rusça'],
        ['हिंदी शीर्षक उदाहरण', 'hi', 'Hindi'],
        ['ตัวอย่างหัวข้อภาษาไทย', 'th', 'Tayca'],
        ['כותרת עברית לדוגמה', 'he', 'İbranice'],
        ['Türkçe Başlık Örnegi', 'tr', 'Türkçe'],
        ['English Title Example', 'en', 'İngilizce']
    ];
    
    echo str_repeat("-", 80) . "\n";
    printf("%-15s | %-25s | %-30s\n", "Dil", "Orijinal", "Gelişmiş SEO Link");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($testCases as $test) {
        $title = $test[0];
        $langCode = $test[1];
        $langName = $test[2];
        $fallbackId = rand(100, 999);
        
        $seoLink = AdvancedSeoHelper::createAdvancedSeoLink($title, $langCode, null, $fallbackId);
        
        printf("%-15s | %-25s | %-30s\n", 
            $langName, 
            mb_substr($title, 0, 20) . (mb_strlen($title) > 20 ? '...' : ''),
            $seoLink
        );
    }
    
    echo str_repeat("-", 80) . "\n";
    echo "\n=== DESTEKLENEN NON-LATIN DİLLER ===\n";
    $nonLatinLangs = AdvancedSeoHelper::getSupportedNonLatinLanguages();
    foreach ($nonLatinLangs as $code => $name) {
        echo "- $code: $name\n";
    }
}
?>
