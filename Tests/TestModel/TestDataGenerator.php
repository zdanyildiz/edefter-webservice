<?php
/**
 * Test Data Generator Sınıfı
 * 
 * Test verileri oluşturmak için kullanılır
 * Sahte veri üretimi ve test senaryoları için
 * 
 * @author GitHub Copilot
 * @date 24 Haziran 2025
 */

class TestDataGenerator {
    
    private static $turkishNames = [
        'Ahmet', 'Mehmet', 'Ali', 'Mustafa', 'Hasan', 'Hüseyin', 'İbrahim', 'İsmail',
        'Fatma', 'Ayşe', 'Emine', 'Hatice', 'Zeynep', 'Elif', 'Meryem', 'Özlem'
    ];
    
    private static $turkishSurnames = [
        'Yılmaz', 'Kaya', 'Demir', 'Şahin', 'Çelik', 'Öztürk', 'Aydın', 'Özkan',
        'Arslan', 'Doğan', 'Kibar', 'Çetin', 'Kara', 'Koç', 'Kurt', 'Özdemir'
    ];
    
    private static $cities = [
        'İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya', 'Adana', 'Konya', 'Gaziantep',
        'Mersin', 'Diyarbakır', 'Kayseri', 'Eskişehir', 'Urfa', 'Malatya', 'Erzurum', 'Van'
    ];
    
    private static $domains = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'yandex.com'
    ];
    
    /**
     * Rastgele string oluştur
     */
    public static function randomString($length = 10, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
        $string = '';
        $charsLength = strlen($chars);
        
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[rand(0, $charsLength - 1)];
        }
        
        return $string;
    }
    
    /**
     * Rastgele sayı oluştur
     */
    public static function randomNumber($min = 1, $max = 100) {
        return rand($min, $max);
    }
    
    /**
     * Rastgele float oluştur
     */
    public static function randomFloat($min = 0, $max = 100, $decimals = 2) {
        $number = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return round($number, $decimals);
    }
    
    /**
     * Rastgele Türkçe isim oluştur
     */
    public static function randomTurkishName() {
        return self::$turkishNames[array_rand(self::$turkishNames)];
    }
    
    /**
     * Rastgele Türkçe soyisim oluştur
     */
    public static function randomTurkishSurname() {
        return self::$turkishSurnames[array_rand(self::$turkishSurnames)];
    }
    
    /**
     * Rastgele tam isim oluştur
     */
    public static function randomFullName() {
        return self::randomTurkishName() . ' ' . self::randomTurkishSurname();
    }
    
    /**
     * Rastgele e-posta adresi oluştur
     */
    public static function randomEmail($name = null) {
        if (!$name) {
            $name = strtolower(self::randomTurkishName());
        } else {
            $name = strtolower($name);
        }
        
        $domain = self::$domains[array_rand(self::$domains)];
        $number = rand(1, 999);
        
        return $name . $number . '@' . $domain;
    }
    
    /**
     * Rastgele telefon numarası oluştur
     */
    public static function randomPhone() {
        $prefixes = ['50', '51', '52', '53', '54', '55', '56', '57', '58', '59'];
        $prefix = $prefixes[array_rand($prefixes)];
        
        return '0' . $prefix . rand(1000000, 9999999);
    }
    
    /**
     * Rastgele şehir oluştur
     */
    public static function randomCity() {
        return self::$cities[array_rand(self::$cities)];
    }
    
    /**
     * Rastgele adres oluştur
     */
    public static function randomAddress() {
        $streets = ['Atatürk', 'İnönü', 'Cumhuriyet', 'Gazi', 'Mimar Sinan', 'Barbaros'];
        $types = ['Caddesi', 'Sokağı', 'Bulvarı'];
        
        $street = $streets[array_rand($streets)];
        $type = $types[array_rand($types)];
        $number = rand(1, 200);
        $city = self::randomCity();
        
        return "$street $type No:$number, $city";
    }
    
    /**
     * Rastgele tarih oluştur
     */
    public static function randomDate($startDate = '2020-01-01', $endDate = '2025-12-31') {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        
        $randomTimestamp = rand($start, $end);
        return date('Y-m-d', $randomTimestamp);
    }
    
    /**
     * Rastgele datetime oluştur
     */
    public static function randomDateTime($startDate = '2020-01-01', $endDate = '2025-12-31') {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        
        $randomTimestamp = rand($start, $end);
        return date('Y-m-d H:i:s', $randomTimestamp);
    }
    
    /**
     * Rastgele boolean oluştur
     */
    public static function randomBoolean() {
        return rand(0, 1) === 1;
    }
    
    /**
     * Rastgele URL oluştur
     */
    public static function randomUrl() {
        $protocols = ['http', 'https'];
        $domains = ['example.com', 'test.com', 'demo.org', 'sample.net'];
        $paths = ['', '/page', '/about', '/contact', '/products'];
        
        $protocol = $protocols[array_rand($protocols)];
        $domain = $domains[array_rand($domains)];
        $path = $paths[array_rand($paths)];
        
        return $protocol . '://' . $domain . $path;
    }
    
    /**
     * Rastgele IP adresi oluştur
     */
    public static function randomIP() {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255);
    }
    
    /**
     * Rastgele slug oluştur
     */
    public static function randomSlug($length = 8) {
        $words = ['test', 'demo', 'sample', 'example', 'page', 'item', 'data', 'content'];
        $word = $words[array_rand($words)];
        $number = rand(1, 999);
        
        return $word . '-' . $number;
    }
    
    /**
     * Rastgele kategori oluştur
     */
    public static function randomCategory() {
        $categories = [
            'Elektronik', 'Giyim', 'Ev & Yaşam', 'Spor', 'Kitap', 'Oyuncak',
            'Kozmetik', 'Otomotiv', 'Bahçe', 'Mücevher', 'Sağlık', 'Gıda'
        ];
        
        return $categories[array_rand($categories)];
    }
    
    /**
     * Rastgele ürün adı oluştur
     */
    public static function randomProductName() {
        $adjectives = ['Premium', 'Kaliteli', 'Özel', 'Lüks', 'Modern', 'Klasik'];
        $products = ['Ürün', 'Malzeme', 'Aksesuar', 'Parça', 'Sistem', 'Cihaz'];
        
        $adjective = $adjectives[array_rand($adjectives)];
        $product = $products[array_rand($products)];
        $number = rand(1, 999);
        
        return "$adjective $product $number";
    }
    
    /**
     * Rastgele fiyat oluştur
     */
    public static function randomPrice($min = 10, $max = 1000, $currency = 'TL') {
        $price = self::randomFloat($min, $max, 2);
        return number_format($price, 2, ',', '.') . ' ' . $currency;
    }
    
    /**
     * Rastgele kullanıcı verisi oluştur
     */
    public static function randomUser() {
        return [
            'name' => self::randomTurkishName(),
            'surname' => self::randomTurkishSurname(),
            'email' => self::randomEmail(),
            'phone' => self::randomPhone(),
            'city' => self::randomCity(),
            'address' => self::randomAddress(),
            'birth_date' => self::randomDate('1970-01-01', '2005-12-31'),
            'created_at' => self::randomDateTime()
        ];
    }
    
    /**
     * Rastgele ürün verisi oluştur
     */
    public static function randomProduct() {
        return [
            'name' => self::randomProductName(),
            'category' => self::randomCategory(),
            'price' => self::randomFloat(10, 1000, 2),
            'stock' => self::randomNumber(0, 100),
            'description' => 'Bu bir test ürünüdür. ' . self::randomString(50),
            'slug' => self::randomSlug(),
            'active' => self::randomBoolean(),
            'created_at' => self::randomDateTime()
        ];
    }
    
    /**
     * Rastgele sipariş verisi oluştur
     */
    public static function randomOrder() {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        return [
            'order_number' => 'ORD-' . rand(10000, 99999),
            'user_id' => self::randomNumber(1, 100),
            'total' => self::randomFloat(50, 500, 2),
            'status' => $statuses[array_rand($statuses)],
            'created_at' => self::randomDateTime(),
            'shipping_address' => self::randomAddress()
        ];
    }
    
    /**
     * Test kullanıcı verisi oluştur
     */
    public static function generateUserData() {
        $firstName = self::$turkishNames[array_rand(self::$turkishNames)];
        $lastName = self::$turkishSurnames[array_rand(self::$turkishSurnames)];
        $domain = self::$domains[array_rand(self::$domains)];
        
        return [
            'name' => $firstName,
            'surname' => $lastName,
            'fullName' => $firstName . ' ' . $lastName,
            'email' => strtolower($firstName . '.' . $lastName . rand(1, 999) . '@' . $domain),
            'phone' => '055' . rand(1000000, 9999999),
            'city' => self::$cities[array_rand(self::$cities)],
            'age' => rand(18, 65),
            'password' => self::randomString(8) . rand(10, 99)
        ];
    }
    
    /**
     * Çoklu test verisi oluştur
     */
    public static function generateMultiple($type, $count = 10) {
        $data = [];
        
        for ($i = 0; $i < $count; $i++) {
            switch ($type) {
                case 'user':
                    $data[] = self::randomUser();
                    break;
                case 'product':
                    $data[] = self::randomProduct();
                    break;
                case 'order':
                    $data[] = self::randomOrder();
                    break;
                default:
                    break;
            }
        }
        
        return $data;
    }
}
