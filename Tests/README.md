# Test Framework Dokümantasyonu

Bu dokümantasyon, **Pozitif Eticaret** projesi için oluşturulan özel test framework'ünün kullanımını açıklar.

## 📚 Framework Bileşenleri

### Temel Sınıflar

1. **TestDatabase** - Veritabanı işlemleri
2. **TestLogger** - Log yönetimi
3. **TestValidator** - Veri doğrulama
4. **TestDataGenerator** - Test verisi üretimi
5. **TestAssert** - Assertion kontrolü
6. **TestHelper** - Yardımcı fonksiyonlar

## 🚀 Hızlı Başlangıç

### Test Dosyası Oluşturma

```php
<?php
// Test framework'ünü yükle
include_once __DIR__ . '/index.php';

// Test başlat
TestHelper::printTestHeader('Test Adı');

try {
    // Test kodlarınız burada
    $db = new TestDatabase();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // Test işlemleri...
    
} catch (Exception $e) {
    TestLogger::error('Test hatası', ['error' => $e->getMessage()]);
}

// Test sonuçlarını özetle
$success = TestHelper::printTestSummary('Test Adı');
TestAssert::summary();
exit($success ? 0 : 1);
```

## 🗄️ TestDatabase Kullanımı

### Veritabanı Bağlantısı
```php
$db = new TestDatabase();
```

### Tablo İşlemleri
```php
// Tablo varlığı kontrolü
if ($db->tableExists('users')) {
    echo "Tablo mevcut";
}

// Sütun varlığı kontrolü
if ($db->columnExists('users', 'email')) {
    echo "Sütun mevcut";
}

// Tablo bilgilerini al
$tableInfo = $db->getTableInfo('users');
```

### Veri İşlemleri
```php
// Güvenli sorgu
$stmt = $db->safeQuery("SELECT * FROM users WHERE id = ?", [1]);
$user = $stmt->fetch();

// Bulk insert
$testData = [
    ['name' => 'User 1', 'email' => 'user1@test.com'],
    ['name' => 'User 2', 'email' => 'user2@test.com']
];
$db->insertTestData('users', $testData);

// Tablo temizle
$db->truncateTable('test_table');
$db->dropTable('test_table');
```

## 📝 TestLogger Kullanımı

### Log Türleri
```php
TestLogger::info('Bilgi mesajı');
TestLogger::warning('Uyarı mesajı');
TestLogger::error('Hata mesajı');
TestLogger::success('Başarı mesajı');
TestLogger::debug('Debug mesajı');
```

### Test Spesifik Loglar
```php
TestLogger::testStart('Test Adı');
TestLogger::testEnd('Test Adı', true); // başarılı
TestLogger::sql('SELECT * FROM users', ['id' => 1]);
TestLogger::http('POST', '/api/users', ['name' => 'Test']);
```

### Log Yönetimi
```php
// Log temizle
TestLogger::clearLog();

// Son 50 satırı oku
$logs = TestLogger::readLog(50);

// Log istatistikleri
$stats = TestLogger::getLogStats();
```

## ✅ TestValidator Kullanımı

### Tekil Doğrulamalar
```php
TestValidator::validateEmail('test@example.com'); // true
TestValidator::validatePhone('05551234567'); // true
TestValidator::validateUrl('https://example.com'); // true
TestValidator::validateDate('2025-01-01'); // true
TestValidator::validateJson('{"test": true}'); // true
```

### Veri Türü ve Sınır Kontrolleri
```php
TestValidator::validateNumeric(25, 18, 65); // min-max kontrol
TestValidator::validateStringLength('Test', 2, 100); // uzunluk kontrol
TestValidator::validateDatabaseId(123); // pozitif integer
```

### Güvenlik Kontrolleri
```php
TestValidator::checkSqlInjectionRisk($input);
TestValidator::checkXssRisk($input);
```

### Batch Validation
```php
$data = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'age' => 25
];

$rules = [
    'name' => ['required' => true, 'length' => ['min' => 2]],
    'email' => ['required' => true, 'email' => true],
    'age' => ['numeric' => ['min' => 18, 'max' => 120]]
];

$result = TestValidator::validateBatch($data, $rules);
// true = geçerli, array = hata listesi
```

## 🎲 TestDataGenerator Kullanımı

### Rastgele Veri Üretimi
```php
// Temel veri türleri
$randomString = TestDataGenerator::randomString(10);
$randomNumber = TestDataGenerator::randomNumber(1, 100);
$randomFloat = TestDataGenerator::randomFloat(0, 100, 2);
$randomBoolean = TestDataGenerator::randomBoolean();

// Türkçe veriler
$name = TestDataGenerator::randomTurkishName();
$surname = TestDataGenerator::randomTurkishSurname();
$fullName = TestDataGenerator::randomFullName();
$city = TestDataGenerator::randomCity();

// İletişim bilgileri
$email = TestDataGenerator::randomEmail();
$phone = TestDataGenerator::randomPhone();
$address = TestDataGenerator::randomAddress();

// Tarih/Zaman
$date = TestDataGenerator::randomDate('2020-01-01', '2025-12-31');
$datetime = TestDataGenerator::randomDateTime();
```

### Kompleks Veri Oluşturma
```php
// Tek kullanıcı
$user = TestDataGenerator::randomUser();
/*
Array(
    'name' => 'Ahmet',
    'surname' => 'Yılmaz',
    'email' => 'ahmet123@gmail.com',
    'phone' => '05551234567',
    'city' => 'İstanbul',
    'address' => 'Atatürk Caddesi No:15, İstanbul',
    'birth_date' => '1990-05-15',
    'created_at' => '2025-01-15 14:30:25'
)
*/

// Çoklu veri
$users = TestDataGenerator::generateMultiple('user', 10);
$products = TestDataGenerator::generateMultiple('product', 5);
$orders = TestDataGenerator::generateMultiple('order', 20);
```

## 🧪 TestAssert Kullanımı

### Temel Assertions
```php
TestAssert::assertEquals($expected, $actual, 'Mesaj');
TestAssert::assertNotEquals($notExpected, $actual);
TestAssert::assertTrue($value);
TestAssert::assertFalse($value);
TestAssert::assertNull($value);
TestAssert::assertNotNull($value);
TestAssert::assertEmpty($value);
TestAssert::assertNotEmpty($value);
```

### Array ve String Assertions
```php
TestAssert::assertArrayHasKey('key', $array);
TestAssert::assertStringContains('needle', 'haystack');
TestAssert::assertCount(5, $array);
```

### Sayısal Karşılaştırmalar
```php
TestAssert::assertGreaterThan(10, $actual);
TestAssert::assertLessThan(100, $actual);
```

### Özel Assertions
```php
TestAssert::assertInstanceOf('PDO', $dbConnection);
TestAssert::assertMatchesRegex('/^[a-z]+$/', $string);
TestAssert::assertJson($jsonString);
```

### İstatistikler ve Özet
```php
// Anlık istatistikler
$stats = TestAssert::getStats();
// Array('total' => 15, 'failed' => 2, 'passed' => 13)

// Sayacları sıfırla
TestAssert::resetCounters();

// Özet raporu
$allPassed = TestAssert::summary(); // true/false döner
```

## 🛠️ TestHelper Kullanımı

### Test Yaşam Döngüsü
```php
// Test başlat
TestHelper::printTestHeader('Test Adı');

// Test sonucu göster
TestHelper::printTestResult(true, 'İşlem başarılı');
TestHelper::printTestResult(false, 'İşlem başarısız');

// Test bitir ve özet göster
$success = TestHelper::printTestSummary('Test Adı');
```

## 📁 Dosya Yapısı

```
Tests/
├── index.php                 # Ana framework loader
├── TestDatabase.php          # Veritabanı sınıfı
├── TestLogger.php           # Log sınıfı
├── TestValidator.php        # Doğrulama sınıfı
├── TestDataGenerator.php    # Veri üretimi sınıfı
├── TestAssert.php          # Assertion sınıfı
├── example_test.php        # Örnek test dosyası
├── Logs/                   # Log dosyaları
│   └── test_2025-06-24.log
├── Database/               # DB yardımcıları
└── [ModuleName]/          # Modül testleri
    └── module_test.php
```

## 🎯 En İyi Pratikler

### 1. Test Dosyası Şablonu
```php
<?php
include_once __DIR__ . '/index.php';

TestHelper::printTestHeader('Test Adı');

try {
    // Test kodları
    
} catch (Exception $e) {
    TestLogger::error('Beklenmedik hata', ['error' => $e->getMessage()]);
}

$success = TestHelper::printTestSummary('Test Adı');
TestAssert::summary();
exit($success ? 0 : 1);
```

### 2. Assertion Kullanımı
- Her test durumu için uygun assertion kullanın
- Açıklayıcı mesajlar yazın
- Kritik noktalarda null kontrolü yapın

### 3. Log Kullanımı
- Test adımlarını info ile kaydedin
- Hataları ayrıntılı olarak loglayin
- SQL sorgularını sql() ile kaydedin

### 4. Veri Üretimi
- Gerçekçi test verisi kullanın
- Türkçe karakter desteğinden faydalanın
- Validation kurallarına uygun veri üretin

### 5. Veritabanı İşlemleri
- Test tablosu isimleri unique olsun
- Test sonunda temizlik yapın
- Transaction kullanmayı unutmayın

## 🔧 Gelişmiş Özellikler

### Custom Validator Ekleme
```php
// TestValidator sınıfına ekleme yapabilirsiniz
public static function validateTCKN($tcknStr) {
    // TCKN doğrulama algoritması
    return $isValid;
}
```

### Custom Data Generator
```php
// TestDataGenerator'a özel metodlar
public static function randomTCKN() {
    // Geçerli TCKN üret
    return $tcknString;
}
```

### Log Filtering
```php
// Logs klasöründen belirli tarihteki logları filtrele
$todayLogs = TestLogger::readLog(100);
$errorLogs = array_filter($todayLogs, function($line) {
    return strpos($line, '[ERROR]') !== false;
});
```

## 🚨 Dikkat Edilmesi Gerekenler

1. **Ana Proje Dosyalarına Müdahale Etmeyin** - Test framework tamamen bağımsızdır
2. **Test Verilerini Temizleyin** - Test sonunda oluşturulan verileri silin
3. **Log Boyutunu Kontrol Edin** - Uzun testlerde log dosyaları büyüyebilir
4. **Assertion Sayısını Takip Edin** - Çok fazla assertion performansı etkileyebilir
5. **Exception Handling** - Her test dosyasında try-catch kullanın

Bu framework sayesinde test yazımı kolaylaşacak ve kod tekrarları önlenecektir. Yeni test dosyaları oluştururken bu dokümantasyonu referans alabilirsiniz.
