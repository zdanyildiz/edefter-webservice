# Pozitif Eticaret Test Sistemi - Fonksiyon Referansı

## 🎯 Ana Framework

### Tests/index.php (Framework Loader)
```php
include_once __DIR__ . '/index.php';  // Tüm test sınıflarını yükler
```

---

## 🧪 Test Framework (Tests/TestModel/)

### TestAssert.php - Assertion Kontrolleri
```php
TestAssert::assertEquals($expected, $actual, $message)
TestAssert::assertNotEquals($notExpected, $actual, $message)
TestAssert::assertTrue($actual, $message)
TestAssert::assertFalse($actual, $message)
TestAssert::assertNull($actual, $message)
TestAssert::assertNotNull($actual, $message)
TestAssert::assertEmpty($actual, $message)
TestAssert::assertNotEmpty($actual, $message)
TestAssert::assertArrayHasKey($key, $array, $message)
TestAssert::assertStringContains($needle, $haystack, $message)
TestAssert::assertGreaterThan($expected, $actual, $message)
TestAssert::assertLessThan($expected, $actual, $message)
TestAssert::assertCount($expectedCount, $array, $message)
TestAssert::assertInstanceOf($expectedType, $actual, $message)
TestAssert::assertMatchesRegex($pattern, $string, $message)
TestAssert::assertJson($jsonString, $message)
TestAssert::getStats()                    // Test istatistikleri
TestAssert::resetCounters()               // Sayaçları sıfırla
TestAssert::summary()                     // Özet raporu (true/false)
```

### TestDataGenerator.php - Test Verisi Üretimi
```php
TestDataGenerator::randomString($length)
TestDataGenerator::randomNumber($min, $max)
TestDataGenerator::randomFloat($min, $max, $decimals)
TestDataGenerator::randomBoolean()
TestDataGenerator::randomTurkishName()
TestDataGenerator::randomTurkishSurname()
TestDataGenerator::randomFullName()
TestDataGenerator::randomCity()
TestDataGenerator::randomEmail()
TestDataGenerator::randomPhone()
TestDataGenerator::randomAddress()
TestDataGenerator::randomDate($startDate, $endDate)
TestDataGenerator::randomDateTime()
TestDataGenerator::randomUser()           // Kompleks kullanıcı verisi
TestDataGenerator::generateMultiple($type, $count)
```

### TestLogger.php - Log Yönetimi
```php
TestLogger::info($message, $context)
TestLogger::success($message, $context)
TestLogger::error($message, $context)
TestLogger::warning($message, $context)
TestLogger::debug($message, $context)
TestLogger::testStart($testName)
TestLogger::testEnd($testName, $success)
TestLogger::sql($query, $params)
TestLogger::http($method, $url, $data)
TestLogger::clearLog()
TestLogger::readLog($lines)
TestLogger::getLogStats()
```

### TestValidator.php - Veri Doğrulama
```php
TestValidator::validateEmail($email)
TestValidator::validatePhone($phone)
TestValidator::validateUrl($url)
TestValidator::validateDate($date)
TestValidator::validateJson($json)
TestValidator::validateNumeric($value, $min, $max)
TestValidator::validateStringLength($string, $min, $max)
TestValidator::validateDatabaseId($id)
TestValidator::checkSqlInjectionRisk($input)
TestValidator::checkXssRisk($input)
TestValidator::validateBatch($data, $rules)    // Toplu doğrulama
```

### TestDatabase.php - Veritabanı İşlemleri
```php
TestDatabase::getInstance()               // Singleton pattern
TestDatabase::tableExists($tableName)
TestDatabase::columnExists($tableName, $columnName)
TestDatabase::getTableInfo($tableName)
TestDatabase::safeQuery($sql, $params)
TestDatabase::insertTestData($table, $data)
TestDatabase::truncateTable($tableName)
TestDatabase::dropTable($tableName)
```

---

## 🛠️ Sistem Araçları (Tests/System/)

### TestCleaner.php - Dosya Temizleme
```php
TestCleaner::cleanFiles($files, $dryRun)
TestCleaner::listFiles($folder, $extensions)
TestCleaner::cleanTempFiles($dryRun)
TestCleaner::cleanOldLogs($daysOld, $dryRun)
TestCleaner::cleanAllTempFiles($dryRun)
```

### TestMover.php - Dosya Organizasyonu
```php
TestMover::moveFiles($moveMap, $dryRun, $updateReferences)
TestMover::organizeTestFramework($dryRun)
TestMover::handleWebRequest($action, $params)
```

### TestCurl.php - HTTP Test Araçları
```php
TestCurl::get($url, $headers)
TestCurl::post($url, $data, $headers)
TestCurl::put($url, $data, $headers)
TestCurl::delete($url, $headers)
TestCurl::testEndpoint($url, $method, $data)
```

---

## 🚀 Hızlı Başlangıç Komutları

### Test Framework
```powershell
# Ana test örneği çalıştır
php Tests\example_test.php

# Test framework durumunu kontrol et
php Tests\index.php
```

### Temizleme İşlemleri
```powershell
# Hızlı temizlik (önerilen)
php Tests\System\quick_clean.php

# Manuel temizlik
php Tests\System\TestCleaner.php temp
php Tests\System\TestCleaner.php logs 30
```

### Organizasyon İşlemleri
```powershell
# Hızlı organizasyon
php Tests\System\quick_organize.php

# Manuel organizasyon
php Tests\System\TestMover.php organize
```

---

## 📝 Test Dosyası Şablonu

```php
<?php
include_once __DIR__ . '/index.php';

TestHelper::startTest('Test Adı');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // Test kodları...
    
    TestLogger::success('Test başarılı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

TestHelper::endTest();
```

---

## 🎯 PowerShell Komut Formatı

**⚠️ KRİTİK**: Tüm terminal komutları PowerShell formatında:
- Komut ayırıcı: `;` (PowerShell)
- Dosya yolu: `\` (Windows)
- **ASLA** `php -r` inline komut kullanma

---

## 🧹 Test Temizleme Sistemi

### Korumalı Dosyalar (Silinmez)
- index.php, README.md, example_test.php
- TestAssert.php, TestDataGenerator.php, TestLogger.php
- TestRunner.php, TestValidator.php

### Hızlı Temizleme
```powershell
php Tests\System\quick_clean.php    # Otomatik güvenli temizlik
```

---

## 📊 Log Dosyaları

- **Test Log**: `Tests/Logs/test_YYYY-MM-DD.log`
- **Site Log**: `Public/Log/YYYY-MM-DD.log`
- **Admin Log**: `Public/Log/Admin/YYYY-MM-DD.log`

---

## 🔧 Geliştirme Kuralları

### ✅ Test Dosyası OLUŞTUR:
- Veritabanı CRUD işlemleri
- Ödeme sistemi entegrasyonları
- Email gönderme sistemleri
- API entegrasyonları

### ❌ Test Dosyası OLUŞTURMA:
- CSS stil sorunları
- JavaScript fonksiyon hataları
- HTML UI sorunları
- Bootstrap/jQuery entegrasyon sorunları

### 🚫 Ana Proje Dosyalarına Müdahale Yasağı
- App/, Public/, _y/ klasörlerine müdahale edilemez
- Sadece Tests/ klasörü altında çalışma yapılabilir

---

*Bu referans MCP sunucu mantığıyla oluşturulmuştur. İhtiyaç duyulduğunda ilgili fonksiyon çağrılabilir.*
