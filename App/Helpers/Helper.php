<?php

class Helper
{
    function encrypt(string $data, string $key): string
    {
        if (empty($data))return "";
        if (empty($key)) {
            //throw new InvalidArgumentException('Data and key must not be null or empty.');
            Log::write("Data and key must not be null or empty.", "error");
            return $data;
        }

        $method = 'AES-256-CBC';
        $key = hash('sha256', $key);
        $iv = substr(hash('sha256', $key), 0, 16);
        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted;
    }

    function decrypt(mixed $data, string $key): string
    {
        $dataOriginal = $data;
        if (empty($data))return "";
        if (empty($key)) {
            //throw new InvalidArgumentException('Data and key must not be null or empty.');
            Log::write("Data and key must not be null or empty.", "error");
            return $data;
        }

        $method = 'AES-256-CBC';
        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length($method);
        $key = hash('sha256', $key);
        $iv = substr(hash('sha256', $key), 0, 16);
        $decrypt = openssl_decrypt(substr($data,16), $method, $key, OPENSSL_RAW_DATA, $iv);

        if(empty($decrypt)) $decrypt = $dataOriginal;
        return $decrypt;
    }

    // Boş kontrolü yapılıyor
    // Değer boş ise true döndürür, değilse false döndürür.
    public static function isEmpty($data) : bool
    {
        if (is_array($data)) {
            return empty($data);
        } else if (is_string($data)) {
            return trim($data) === '';
        } else if (is_numeric($data)) {
            return false;
        }

        return empty($data);
    }

    // Güvenli bir şekilde GET verisini alıyor
    public static function get($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return htmlspecialchars($_GET[$name]);
        }
        return $default;
    }

    // Güvenli bir şekilde POST verisini alıyor
    public static function post($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return htmlspecialchars($_POST[$name]);
        }
        return $default;
    }

    // Sayı kontrolü yapıyor ve sayı ise değeri döndürüyor, değilse 0 döndürüyor
    public static function getNumericValue($input)
    {
        return is_numeric($input) ? $input : 0;
    }

    //büyük harften küçük harfe çevirici
    public static function toLowerCase($string)
    {
        return mb_strtolower($string, 'UTF-8');
    }

    public function turkish_to_lower($string) {
        $search = ['İ', 'I', 'Ş', 'Ğ', 'Ü', 'Ö', 'Ç'];
        $replace = ['i', 'ı', 'ş', 'ğ', 'ü', 'ö', 'ç'];
        $string = str_replace($search, $replace, $string);
        return $this->toLowerCase($string);
    }

    //küçük harften büyük harfe çevirici
    public static function toUpperCase($string)
    {
        return mb_strtoupper($string, 'UTF-8');
    }

    public function turkish_to_upper($string) {
        $replace = ['İ', 'I', 'Ş', 'Ğ', 'Ü', 'Ö', 'Ç'];
        $search = ['i', 'ı', 'ş', 'ğ', 'ü', 'ö', 'ç'];
        $string = str_replace($search, $replace, $string);
        return $this->toUpperCase($string);
    }

    //türkçe karakterden ingilizce karaktere çevirici
    public static function trToEn($string)
    {
        $search = array('Ç', 'ç', 'Ğ', 'ğ', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü','�');
        $replace = array('c', 'c', 'g', 'g', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u','-');
        $string = str_replace($search, $replace, $string);

        $string = preg_replace('/[^\x20-\x7E]/', '-', $string);

        return $string;
    }

    public static function cleanString($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9-_]/', ' ', $string);
        // Boşlukları '-' ile değiştir
        $string = str_replace(' ', '-', $string);

        // Ardışık '-' karakterlerini tek '-' karakterine dönüştür
        $string = preg_replace('/-+/', '-', $string);

        return $string;
    }
    public static function formatCurrency($number)
    {
        //boşsa veriyi geri dön
        if (empty($number)) {
            return $number;
        }
        //sayı değilse veriyi geri dön
        if (!is_numeric($number)) {
            return $number;
        }
        return number_format($number, 2, ',', '.');
    }

    function createPassword($value,$type){

        if($type==0) $chars = "0123456789";
        if($type==1) $chars = "ABCDEFGHJKMNPRSTUVYZQWX";
        if($type==2) $chars = "ABCDEFGHJKMNPRSTUVYZQWX23456789";
        if($type==3) $chars = "abcdefghjklmnoprstuvyzqxABCDEFGHJKLMNOPRSTUVYZQWX0123456789%=*";
        unset($Nasil);
        return substr(str_shuffle($chars),0,$value);
    }

    public function generateUniqID(){
        return $this->createPassword(20,2);
    }

    function getIP(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = file_get_contents('https://api.ipify.org') ?? $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function writeToArray($array){
        echo "<pre>";
        echo json_encode($array, JSON_PRETTY_PRINT);
        echo "</pre>";
    }

    public function writeToObject($object){
        echo "<pre>";
        echo json_encode($object, JSON_PRETTY_PRINT);
        echo "</pre>";
    }

    public function printMessages($messages) {
        foreach ($messages as $message) {
            echo '<div class="message-card">';
            echo '<h2>';
            echo $message['mesajkonusu'];
            echo '</h2>';
            echo '<p>';
            echo $message['mesajicerik'];
            echo '</p>';

            // Cevaplar varsa yazdır
            if (isset($message['answer'])) {
                $this->printAnswers($message['answer']);
            }

            echo '</div>';
        }
    }

    public function printAnswers($answers) {
        foreach ($answers as $answer) {
            echo '<div class="message-card">';
            echo '<h2>';
            echo $answer['mesajkonusu'];
            echo '</h2>';
            echo '<p>';
            echo $answer['mesajicerik'];
            echo '</p>';

            // Cevapların cevapları varsa yazdır
            if (isset($answer['subAnswer'])) {
                $this->printAnswers($answer['subAnswer']);
            }

            echo '</div>';
        }
    }

    public function printCountries($countries,$addressCountryID=null) {
        echo '<option value="212">Türkiye</option>';
        foreach ($countries as $country) {
            $selected = $addressCountryID == $country['CountryID'] ? 'selected' : '';
            echo '
            <option 
                value="' . $country['CountryID'] . '"
                data-binary-code="' . $country['BinaryCode'] . '"
                data-triple-code="' . $country['TripleCode'] . '"
                data-phone-code="' . $country['PhoneCode'] . '"
                '.$selected.'
                >' . $country['CountryName'] . '</option>';
        }
    }

    //resimleri yeniden adlandırmak için ingilizce karakterleri ve -,_ karakterleri koruyan diğerlerini silen bir fonksiyon yazınız.
    public function sanitizeImageName($imageName){
        if(empty($imageName)) return "";
        $imageName = $this->toLowerCase($imageName);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9-_.]/', '_', $imageName);
        return $sanitizedName;
    }

    public function createSeoLink($title){
        $title = $this->trToEn($title);
        $title = $this->toLowerCase($title);
        $title = preg_replace('/[^a-zA-Z0-9-]/', '-', $title);
        $title = preg_replace('/-+/', '-', $title);
        return $title;
    }

    /**
     * Gelişmiş SEO link oluşturma - Non-Latin dilleri destekler
     * 
     * @param string $title - Başlık
     * @param string $languageCode - Dil kodu (tr, en, ar, etc.)
     * @param object|null $ai - AI servisi (İngilizce çeviri için)
     * @param int|null $fallbackId - Fallback için ID
     * @return string
     */
    public function createAdvancedSeoLink($title, $languageCode = 'tr', $ai = null, $fallbackId = null) {
        
        // Boş kontrol
        if (empty($title)) {
            return $fallbackId ? "content-$fallbackId" : 'content';
        }
        
        // İlk olarak standart yöntem deneyelim
        $standardLink = $this->createSeoLink($title);
        $standardLink = trim($standardLink, '-');
        
        // Eğer standart yöntem başarılıysa ve yeterli uzunluktaysa kullan
        if (!empty($standardLink) && strlen($standardLink) >= 3) {
            return $standardLink;
        }
        
        // Non-Latin dil kontrolü
        $nonLatinLanguages = [
            'ar' => 'Arabic', 'zh' => 'Chinese', 'zh-cn' => 'Chinese Simplified',
            'zh-tw' => 'Chinese Traditional', 'ja' => 'Japanese', 'ko' => 'Korean',
            'ru' => 'Russian', 'hi' => 'Hindi', 'th' => 'Thai', 'he' => 'Hebrew',
            'fa' => 'Persian', 'ur' => 'Urdu', 'bn' => 'Bengali', 'ta' => 'Tamil',
            'te' => 'Telugu', 'ml' => 'Malayalam', 'kn' => 'Kannada', 'gu' => 'Gujarati',
            'pa' => 'Punjabi', 'ne' => 'Nepali', 'si' => 'Sinhala', 'my' => 'Myanmar',
            'km' => 'Khmer', 'lo' => 'Lao', 'ka' => 'Georgian', 'hy' => 'Armenian',
            'am' => 'Amharic', 'ti' => 'Tigrinya'
        ];
        
        if (!isset($nonLatinLanguages[$languageCode])) {
            // Latin tabanlı dil ama sorun var, fallback kullan
            return $fallbackId ? "content-$fallbackId" : $this->generateRandomSlug($title);
        }
        
        // Transliteration dene
        $transliteratedTitle = $this->transliterate($title);
        if (!empty($transliteratedTitle) && strlen($transliteratedTitle) >= 3) {
            $seoLink = $this->cleanTransliteratedText($transliteratedTitle);
            if (!empty($seoLink)) {
                return $seoLink;
            }
        }
        
        // AI ile İngilizce çeviri dene (eğer AI servis mevcutsa)
        if ($ai && method_exists($ai, 'translateConstant')) {
            try {
                $englishTitle = $ai->translateConstant($title, 'English');
                if (!empty($englishTitle)) {
                    $englishSeoLink = $this->createSeoLink($englishTitle);
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
        return $fallbackId ? "content-$fallbackId" : $this->generateRandomSlug($title);
    }
    
    /**
     * Karakterleri transliterate et
     */
    private function transliterate($text) {
        // Transliteration haritası
        $transliterationMap = [
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
            
            // Çince (Sık kullanılan karakterler)
            '中' => 'zhong', '文' => 'wen', '标' => 'biao', '题' => 'ti', '示' => 'shi', '例' => 'li',
            
            // Japonca Hiragana
            'あ' => 'a', 'い' => 'i', 'う' => 'u', 'え' => 'e', 'お' => 'o',
            'か' => 'ka', 'き' => 'ki', 'く' => 'ku', 'け' => 'ke', 'こ' => 'ko',
            'さ' => 'sa', 'し' => 'shi', 'す' => 'su', 'せ' => 'se', 'そ' => 'so',
            'た' => 'ta', 'ち' => 'chi', 'つ' => 'tsu', 'て' => 'te', 'と' => 'to',
            'な' => 'na', 'に' => 'ni', 'ぬ' => 'nu', 'ね' => 'ne', 'の' => 'no',
            'は' => 'ha', 'ひ' => 'hi', 'ふ' => 'fu', 'へ' => 'he', 'ほ' => 'ho',
            'ま' => 'ma', 'み' => 'mi', 'む' => 'mu', 'め' => 'me', 'も' => 'mo',
            'や' => 'ya', 'ゆ' => 'yu', 'よ' => 'yo',
            'ら' => 'ra', 'り' => 'ri', 'る' => 'ru', 'れ' => 're', 'ろ' => 'ro',
            'わ' => 'wa', 'を' => 'wo', 'ん' => 'n',
            
            // Kanji yaygın karakterler
            '日' => 'nichi', '本' => 'hon', '語' => 'go', '人' => 'jin', '時' => 'ji',
            
            // Korece
            '한' => 'han', '국' => 'guk', '어' => 'eo', '제' => 'je', '목' => 'mok', '예' => 'ye',
            
            // Hindi (Devanagari) - Temel karakterler
            'अ' => 'a', 'आ' => 'aa', 'इ' => 'i', 'ई' => 'ii', 'उ' => 'u', 'ऊ' => 'uu',
            'ए' => 'e', 'ऐ' => 'ai', 'ओ' => 'o', 'औ' => 'au',
            'क' => 'ka', 'ख' => 'kha', 'ग' => 'ga', 'घ' => 'gha',
            'च' => 'cha', 'छ' => 'chha', 'ज' => 'ja', 'झ' => 'jha',
            'त' => 'ta', 'थ' => 'tha', 'द' => 'da', 'ध' => 'dha', 'न' => 'na',
            'प' => 'pa', 'फ' => 'pha', 'ब' => 'ba', 'भ' => 'bha', 'म' => 'ma',
            'य' => 'ya', 'र' => 'ra', 'ल' => 'la', 'व' => 'va',
            'श' => 'sha', 'ष' => 'sha', 'स' => 'sa', 'ह' => 'ha',
            
            // İbranice
            'א' => 'a', 'ב' => 'b', 'ג' => 'g', 'ד' => 'd', 'ה' => 'h', 'ו' => 'v',
            'ז' => 'z', 'ח' => 'ch', 'ט' => 't', 'י' => 'y', 'כ' => 'k', 'ל' => 'l',
            'מ' => 'm', 'ן' => 'n', 'נ' => 'n', 'ס' => 's', 'ע' => 'a', 'פ' => 'p',
            'ץ' => 'ts', 'צ' => 'ts', 'ק' => 'k', 'ר' => 'r', 'ש' => 'sh', 'ת' => 't',
            
            // Tayca - Temel karakterler
            'ก' => 'k', 'ข' => 'kh', 'ค' => 'kh', 'ง' => 'ng', 'จ' => 'ch', 'ฉ' => 'ch',
            'ช' => 'ch', 'ซ' => 's', 'ด' => 'd', 'ต' => 't', 'ถ' => 'th', 'ท' => 'th',
            'น' => 'n', 'บ' => 'b', 'ป' => 'p', 'ผ' => 'ph', 'ฟ' => 'f', 'พ' => 'ph',
            'ม' => 'm', 'ย' => 'y', 'ร' => 'r', 'ล' => 'l', 'ว' => 'w',
            'ส' => 's', 'ห' => 'h', 'อ' => 'o', 'ฮ' => 'h'
        ];
        
        $result = '';
        $textArray = mb_str_split($text, 1, 'UTF-8');
        
        foreach ($textArray as $char) {
            if (isset($transliterationMap[$char])) {
                $result .= $transliterationMap[$char];
            } elseif (preg_match('/[a-zA-Z0-9\s\-_]/', $char)) {
                // Latin karakter, sayı, boşluk veya özel karakterler geçsin
                $result .= $char;
            } else {
                // Bilinmeyen karakter, '-' yap
                $result .= '-';
            }
        }
        
        return $result;
    }
    
    /**
     * Transliterate edilmiş metni temizle
     */
    private function cleanTransliteratedText($text) {
        // Küçük harfe çevir
        $text = $this->toLowerCase($text);
        
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
    private function generateRandomSlug($originalTitle) {
        $randomString = 'content-' . bin2hex(random_bytes(4));
        return $randomString;
    }

    // E-posta maskeleme
    public function maskEmail($email) {
        $email = trim($email);

        // Geçerli bir e-posta olup olmadığını kontrol et
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mail_parts = explode("@", $email);
            $domain = array_pop($mail_parts);
            $name = implode("@", $mail_parts);
            $masked_name = substr($name, 0, 2) . str_repeat("*", strlen($name) - 2);
            return $masked_name . "@" . $domain;
        }

        // Eğer geçerli değilse boş döndür
        return '';
    }

    // Telefon numarası maskeleme
    public function maskPhone($phone) {
        // Boşlukları temizle
        $phone = str_replace(' ', '', trim($phone));

        // Telefon numarasının en az 10 haneli ve sadece rakamlardan oluştuğunu kontrol et
        if (preg_match('/^\d{10,}$/', $phone)) {
            return substr($phone, 0, 2) . str_repeat("*", 4) . substr($phone, -4);
        }

        // Eğer geçerli değilse boş döndür
        return '';
    }

    // CSRF Token oluşturma
    public function generateCsrfToken() {
        // Benzersiz bir token oluştur
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Güçlü bir rastgele token
        }
        return $_SESSION['csrf_token'];
    }

    // CSRF Token doğrulama
    public function verifyCsrfToken($token) {
        // Oturumdaki token ile formdan gelen token'i karşılaştır
        if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        }
        Log::write("csrf_token", "error");
        return false;
    }

    // CSRF Token'ı geçersiz kılma
    public function invalidateCsrfToken() {
        // Token'i geçersiz kıl
        unset($_SESSION['csrf_token']);
    }

    // XSS'ye karşı HTML güvenli hale getirme
    public function escapeHtml($data) {
        // Eğer veri boş ise boş döndür
        if (empty($data)) {
            return '';
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    // E-posta adresi gibi verileri temizleme (sanitizing)
    public function sanitizeEmail($email) {
        // Eğer e-posta boş ise boş döndür
        if (empty($email)) {
            return '';
        }
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    // URL verilerini temizleme
    public function sanitizeUrl($url) {
        // Eğer URL boş ise boş döndür
        if (empty($url)) {
            return '';
        }
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    // Sayısal veri girişlerini temizleme
    public function sanitizeNumber($number) {
        // Eğer sayı boş ise boş döndür
        if (empty($number)) {
            return '';
        }
        return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
    }

    //sadece ingilizce karakterler, alt tire ve rakamlar içerebilir. Gelen değerin ilk değeri "_" olmalıdır.
    public function sanitizeConstantName($constantName){
        if (empty($constantName)) return "";

        // Tüm karakterleri küçük harfe dönüştür
        $constantName = $this->toLowerCase($constantName);

        // Türkçe karakterleri İngilizce karakterlere çevir
        $constantName = $this->trToEn($constantName);

        // Sadece İngilizce karakterler, alt tire (_) ve rakamlara izin ver
        $sanitizedName = preg_replace('/[^a-zA-Z0-9_]/', '_', $constantName);

        // İlk karakterin "_" olması gerekiyor, değilse ekle
        if (substr($sanitizedName, 0, 1) !== '_') {
            $sanitizedName = '_' . $sanitizedName;
        }

        return $sanitizedName;
    }

    public function passwordValidator($password)
    {
        //en az 8 karakter olmalıdır
        if (strlen($password) < 8) {
            return false;
        }

        //en fazla 20 karakter olmalıdır
        if (strlen($password) > 20) {
            return false;
        }

        //en az bir küçük harf, en az bir büyük harf ve en az bir sayı olmalıdır
        if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return false;
        }

        return true;
    }

    public function jsonErrorResponse($message) {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit();
    }

    public function jsonWarningResponse($message) {
        echo json_encode([
            'status' => 'warning',
            'message' => $message
        ]);
        exit();
    }

    public function jsonSuccessResponse($message, $data = []) {
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }
}
