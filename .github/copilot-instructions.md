# GitHub Copilot Pozitif Eticaret Proje Talimatları

Bu dosya, **pozitif Eticaret** projesi için GitHub Copilot asistanının proje yapısını, standartları ve geliştirme süreçlerini anlamasını sağlar.

---

## 🚀 Proje Genel Bilgileri

### Geliştirme Ortamı
Kod yazarken aşağıdaki teknolojileri kullanıyoruz:
- **Backend**: PHP 8.3.4, MySQL
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Web Server**: IIS (Windows 11)
- **IDE**: Visual Studio Code
- **Shell**: PowerShell
- **Dependency Manager**: Composer
- **Database Migration**: Phinx

### Proje Yapısı
```
├── App/
│   ├── Core/           # Config, Router, Casper, BannerManager
│   ├── Controller/     # MVC Controller katmanı
│   ├── Model/          # MVC Model katmanı
│   ├── View/           # MVC View katmanı
│   ├── Database/       # Migration ve database.sql
│   └── Helpers/        # Yardımcı sınıflar
├── Public/             # Statik dosyalar (CSS, JS, Image)
├── _y/                 # Admin Panel
├── Tests/              # Test dosyaları ve analiz scriptleri
└── vendor/             # Composer bağımlılıkları
```

---

## ⚙️ Sistem Mimarisi

### Ana Konfigürasyon Sistemi
Proje güvenli bir şifreleme sistemi kullanır:

1. **Domain.php** → Yerel/canlı domain kontrolü (`l.*` = yerel)
2. **Key.php** → Şifreleme anahtarı
3. **Sql.php** → Şifrelenmiş DB bilgileri
4. **Helper.php** → Şifre çözme (`decrypt()` metodu)
5. **Config.php** → Ana konfigürasyon ve sistem sabitleri

### İstek Yaşam Döngüsü
```
index.php → Config → Database → Router → Controller → Model → View → HTML
```

### Temel Sınıflar
- **Config**: Ana konfigürasyon, DB bağlantı bilgileri, sistem sabitleri
- **Router**: URL analizi, content routing (`contentType`, `languageID`)
- **Casper**: Session tabanlı veri yönetimi (kullanıcı, sepet, site ayarları)
- **BannerManager**: Banner sistemi (Singleton pattern, cache)
- **Database**: PDO tabanlı veritabanı bağlantısı

---

## 💻 Geliştirme Ortamı

### PowerShell Komut Formatı
**⚠️ ÖNEMLİ**: Tüm terminal komutları PowerShell formatında olmalıdır:

**Kurallar:**
- Komut ayırıcı: `;` (PowerShell), `&&` DEĞİL
- Dosya yolu: `\` (Windows), `/` DEĞİL
- **ASLA** `php -r` inline komut kullanma (PowerShell syntax hatası)

### Yerel Ortam
- **Domain Tespiti**: `php Tests\System\GetLocalDomain.php`
- **Test Framework**: `php Tests\example_test.php` (Veritabanı bağlantısı ve tablo kontrolleri dahil)

### Test Framework Kullanımı
**Artık veritabanı işlemleri için Tests/index.php kullanın:**
```php
// Test framework'ü yükle
include_once __DIR__ . '/index.php';

// Veritabanı bağlantısı
$db = TestDatabase::getInstance();

// Tablo varlığı kontrolü
if ($db->tableExists('tablename')) {
    echo "Tablo mevcut";
}

// Tablo bilgilerini al
$tableInfo = $db->getTableInfo('tablename');

// Sütun varlığı kontrolü  
if ($db->columnExists('tablename', 'columnname')) {
    echo "Sütun mevcut";
}
```

---

## 🗄️ Veritabanı Yönetimi

### Migration Sistemi (Phinx)
**⚠️ KRİTİK**: Asla `database.sql` direkt düzenleme yapmayın!

```powershell
# Migration oluştur
vendor\bin\phinx create MigrationName -c App\Database\phinx.php

# Migration çalıştır
vendor\bin\phinx migrate -c App\Database\phinx.php

# Durum kontrol
vendor\bin\phinx status -c App\Database\phinx.php
```

### Tablo Kontrol Protokolü
**Her model/controller/test geliştirmeden ÖNCE mutlaka tablo ve sütun kontrolü yapın:**

```php
include_once 'Tests/index.php';

// Tablo varlık kontrolü
$db = TestDatabase::getInstance();
if (!$db->tableExists('sayfa')) {
    throw new Exception('Sayfa tablosu bulunamadı!');
}

// Sütun varlık kontrolü
if (!$db->columnExists('sayfa', 'sayfaad')) {
    throw new Exception('sayfaad sütunu bulunamadı!');
}
```

---

## 🧪 Test Stratejisi

### ⚠️ KRİTİK TEST KURALLARI

#### 🚫 PROJE DOSYALARINA MÜDAHALE YASAĞİ
**Test işlemleri sırasında ana proje dosyalarına (App/, Public/, _y/) KESİNLİKLE müdahale edilemez!**
- Config.php, Database.php, Controller'lar, Model'ler değiştirilemez
- Test çalıştırmak için bile ana proje dosyalarına ekleme yapılamaz
- Sadece Tests/ klasörü altında çalışma yapılabilir

#### 📊 Test Veritabanı Bağlantısı
**Tüm test işlemlerinde sadece şu kaynak kullanılır:**
- **Test DB Sınıfı**: `Tests/Database/TestDatabase.php`
- Ana proje Database sınıfına müdahale edilmez

#### 🧪 Test Index Kullanımı
**Her yeni test dosyası oluştururken:**
- **Test Index**: `include_once __DIR__ . '/index.php';` ile başlayın
- **Test Helper**: `TestHelper` sınıfını kullanın
- **Örnek Test**: `Tests/example_test.php` dosyasını referans alın
- Tekrar eden kod yazımından kaçının, ortak fonksiyonları kullanın

#### 📋 Test Dosyası İçerik Kontrolü
**⚠️ KRİTİK**: Test dosyası düzenleme/çalıştırma öncesi MUTLAKA yapın:
- **Dosya İçerik Kontrolü**: Her test dosyası işlemi öncesi `read_file` ile içeriği kontrol edin
- **Boş Dosya Kontrolü**: Dosya boşsa veya silinmişse yeniden oluşturun
- **Syntax Kontrolü**: PHP syntax hatalarını kontrol edin
- **Dependencies**: Gerekli include/require dosyalarının varlığını doğrulayın
- **Test Öncesi Doğrulama**: Dosya çalıştırmadan önce geçerli PHP kodu olduğunu onaylayın

### Test Dosyası Oluşturma Kuralları

#### ❌ Test Dosyası OLUŞTURMAYIN:
- CSS stil sorunları (renk, layout, spacing)
- JavaScript fonksiyon tanımlı değil hataları
- HTML UI sorunları (form görünümü, tab sistemi)
- Bootstrap/jQuery entegrasyon sorunları

#### ✅ Test Dosyası OLUŞTURUN:
- Veritabanı CRUD işlemleri
- Ödeme sistemi entegrasyonları
- Email gönderme sistemleri
- API entegrasyonları
- Multi-step formlar
- Güvenlik sistemleri

### Test Dosya Konumları
- **Sistem testleri**: `Tests/System/`
- **Modül testleri**: `Tests/[ModuleName]/`
- **Geçici dosyalar**: `Tests/Temp/`
- **Test Veritabanı**: `Tests/Database/`

---

## 📊 Tamamlanan Sistemler

### ✅ Tam Dokümantasyonlu Sistemler
1. **Banner Sistemi** - `Tests/Banners/banner_prompt.md`
2. **Product Sistemi** - `Tests/Products/product_prompt.md`
3. **Member Sistemi** - `Tests/Members/member_prompt.md`
4. **Order/Payment Sistemi** - `Tests/Orders/order_prompt.md`
5. **Cart Sistemi** - `Tests/Carts/cart_prompt.md`
6. **Category Sistemi** - `Tests/Categorys/category_prompt.md`
7. **HomePage Sistemi** - `Tests/HomePages/homepage_prompt.md`

### 🔄 Gelecek Sistemler
- SEO Sistemi
- Email/Notification Sistemi
- File/Media Sistemi
- Language/Multi-language Sistemi
- Admin Panel Core

---

## 📝 Kodlama Standartları

### PHP Standartları
- **PSR-12** kodlama standardı
- **camelCase** değişkenler, **PascalCase** sınıflar
- **phpDoc** yorum blokları (Türkçe)
- **PDO prepared statements** (SQL Injection koruması)

### Güvenlik
- Kullanıcı girdilerini asla doğrudan SQL'e eklemeyin
- XSS koruması için `htmlspecialchars()`
- CSRF token kontrolleri
- Şifrelenmiş veritabanı bilgileri sistemi

### Dosya Organizasyonu
- Controller: `App/Controller/`
- Model: `App/Model/`
- View: `App/View/`
- Helper: `App/Helpers/`
- Test: `Tests/[ModuleName]/`

---

## 🔧 Yardımcı Araçlar

### Hızlı Erişim Komutları
```powershell
# Yerel domain öğren
php Tests\System\GetLocalDomain.php

# Test framework'ü çalıştır (veritabanı + tablo kontrolleri dahil)
php Tests\example_test.php

# Özel test dosyası çalıştır
php Tests\[ModuleName]\test_file.php

# Log dosyalarını kontrol et
Get-Content "Public\Log\$(Get-Date -Format 'yyyy-MM-dd').log" -Tail 10

# Test loglarını kontrol et
Get-Content "Tests\Logs\test_$(Get-Date -Format 'yyyy-MM-dd').log" -Tail 10
```

### Log Dosyaları
- **Site Log**: `Public/Log/YYYY-MM-DD.log`
- **Admin Log**: `Public/Log/Admin/YYYY-MM-DD.log`
- **Sistem Log**: `Public/Log/errors.log`

---

## 🎯 Geliştirme Süreci

### Yeni Özellik Geliştirme
1. **Analiz**: İlgili sistem prompt dosyasını incele
2. **Tablo Kontrolü**: Tablo/sütun yapısını kontrol et
3. **Model Oluştur**: Gerekirse yeni Model sınıfı
4. **Controller Geliştir**: İş mantığı ve routing
5. **View Tasarla**: Kullanıcı arayüzü
6. **Test**: İlgili test dosyalarını çalıştır
7. **Dokümantasyon**: Prompt dosyasını güncelle

### Hata Ayıklama
1. **Log Kontrol**: İlgili log dosyalarını incele
2. **Tablo Kontrol**: Veritabanı yapısını doğrula
3. **Test Script**: Sorunlu modül için test scripti çalıştır
4. **Browser DevTools**: Frontend sorunları için

---

## 🔗 Önemli Bağlantılar

- **Ana Site**: `http://[local_domain]/`
- **Admin Panel**: `http://[local_domain]/_y/`
- **Test Arayüzü**: `Tests/Analyzer/analyzer.html`
- **Database Schema**: `App/Database/database.sql`

---

## Copilot Instructions geliştirme
- Test ortamı cli, terminal, powershell, `php` hatalarında, hata tekrarından kaçınmak için görevinize ara verip bu dosyayı kısa, kesin ve net bilgilerle güncelleyin. Daha sonra görevinize devam edin.

## 🧪 Test Framework Özeti
**2025-06-24 tarihinde test framework başarıyla tamamlandı:**

### Temel Sınıflar
- **TestDatabase**: Veritabanı işlemleri (Singleton pattern, tableExists, columnExists)
- **TestLogger**: Log yönetimi (günlük log dosyaları, Tests/Logs/)
- **TestValidator**: Veri doğrulama (email, telefon, parola güvenliği)
- **TestDataGenerator**: Test verisi üretimi (Türkçe isimler, adresler)
- **TestAssert**: Assertion kontrolü (PHPUnit benzeri, 9 farklı assertion)
- **TestHelper**: Yardımcı fonksiyonlar (test yaşam döngüsü)

### Kullanım
```php
include_once __DIR__ . '/index.php';  // Tek satır ile framework yükle
TestHelper::startTest('Test Adı');     // Test başlat
// Test kodları...
TestHelper::endTest();                 // Test bitir ve özet
```

### Test Sonucu
- **Komut**: `php Tests\example_test.php`
- **Durum**: 9/9 assertion başarılı ✅
- **Log**: Otomatik `Tests/Logs/test_YYYY-MM-DD.log`
- **Özellik**: Veritabanı + tablo kontrolü entegre

*Bu dokümantasyon sürekli güncellenmektedir. Yeni sistemler keşfedildikçe ilgili bölümler güncellenecektir.*

---

## 🧪 Test Framework Kullanım Kılavuzu

### Tests/index.php Kullanımı
**Her test dosyası bu yapıyı takip etmelidir:**

```php
<?php
// Test framework'ünü yükle (tek satır ile tüm sınıflar yüklenir)
include_once __DIR__ . '/index.php';

// Test başlat
TestHelper::startTest('Test Adı');

try {
    // Veritabanı bağlantısı
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'DB bağlantısı kurulmalı');
    
    // Tablo kontrolleri
    TestAssert::assertTrue($db->tableExists('users'), 'users tablosu mevcut olmalı');
    
    // Test verisi oluştur
    $testUser = TestDataGenerator::generateUserData();
    TestAssert::assertNotEmpty($testUser['email'], 'Email boş olmamalı');
    
    // Validasyon testleri
    TestAssert::assertTrue(
        TestValidator::validateEmail($testUser['email']), 
        'Email formatı geçerli olmalı'
    );
    
    TestLogger::success('Tüm testler başarılı');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

// Test sonlandır
TestHelper::endTest();
```

### Framework Avantajları
- **Tek Include**: `Tests/index.php` ile tüm sınıflar yüklenir
- **Auto-logging**: Tüm işlemler otomatik loglanır (`Tests/Logs/`)
- **DB Güvenliği**: Ana proje DB'sine müdahale etmez
- **Türkçe Destek**: Test verileri Türkçe karakter destekli
- **Assertion System**: PHPUnit benzeri assertion kontrolü
- **Data Generation**: Gerçekçi test verisi otomatik üretimi

### Test Yaşam Döngüsü
1. `include_once __DIR__ . '/index.php'` → Framework yükle
2. `TestHelper::startTest()` → Test başlat ve sayaçları sıfırla
3. Test kodları → TestAssert ile doğrulama
4. `TestHelper::endTest()` → Özet raporu ve sonuç

### Mevcut Sınıflar ve Metodlar
- **TestDatabase**: `getInstance()`, `tableExists()`, `columnExists()`, `getTableInfo()`
- **TestLogger**: `info()`, `success()`, `error()`, `warning()`, `sql()`
- **TestValidator**: `validateEmail()`, `validatePhone()`, `getPasswordStrength()`
- **TestDataGenerator**: `generateUserData()`, `randomUser()`, `randomEmail()`
- **TestAssert**: `assertTrue()`, `assertEquals()`, `assertNotNull()`, `assertCount()`
- **TestHelper**: `startTest()`, `endTest()`, `success()`, `error()`, `info()`

*Bu dokümantasyon sürekli güncellenmektedir. Yeni sistemler keşfedildikçe ilgili bölümler güncellenecektir.*

---

## 🧹 Test Temizleme Sistemi

### Test Dosya Temizleyici (TestCleaner)
**2025-06-24 tarihinde test dosya temizleme sistemi tamamlandı:**

#### Temel Özellikler
- **Güvenli Silme**: Sadece Tests/ klasörü içindeki dosyaları siler
- **Korumalı Dosyalar**: Önemli sistem dosyaları otomatik korunur
- **Dry Run**: Önce kontrol, sonra gerçek silme
- **Otomatik Log**: Tüm işlemler `Tests/Logs/` altında loglanır
- **Dosya Filtresi**: Sadece izin verilen uzantılar (.php, .txt, .log, .json, .xml, .csv, .html, .md, .css, .js)

#### Korumalı Dosyalar (Silinmez)
```php
'index.php', 'README.md', 'TestAssert.php', 'TestDataGenerator.php', 
'TestLogger.php', 'TestRunner.php', 'TestValidator.php', 'example_test.php'
```

### Kullanım Yöntemleri

#### 1️⃣ Hızlı Temizleme (Önerilen)
```powershell
# Tüm geçici dosyaları otomatik temizle
php Tests\System\quick_clean.php
```
**Özellikler:**
- Temp/ klasörünü tamamen temizler
- temp_, debug_, test_, old_ ile başlayan dosyaları siler
- 30 günden eski log dosyalarını temizler
- Dry run + gerçek silme kombinasyonu

#### 2️⃣ Manuel Dosya Listesi ile Temizleme
```php
include_once 'Tests/System/TestCleaner.php';

$files = [
    'Temp/debug_output.txt',
    'Orders/temp_order_test.php',
    'Products/old_product_test.php'
];

// Önce kontrol et
TestCleaner::cleanFiles($files, true);  // Dry run

// Gerçek silme
TestCleaner::cleanFiles($files, false);
```

#### 3️⃣ Klasör Bazlı Temizleme
```php
// Temp klasörünü temizle
TestCleaner::cleanTempFiles();          // Gerçek silme
TestCleaner::cleanTempFiles(true);      // Dry run

// Eski logları temizle
TestCleaner::cleanOldLogs(30);          // 30 günden eski
TestCleaner::cleanOldLogs(7, true);     // 7 günlük dry run
```

#### 4️⃣ Komut Satırı Kullanımı
```powershell
# Temp dosyalarını sil
php Tests\System\TestCleaner.php temp

# 30 günden eski logları sil
php Tests\System\TestCleaner.php logs

# 7 günden eski logları sil
php Tests\System\TestCleaner.php logs 7

# Tüm modüllerdeki geçici dosyaları sil
php Tests\System\TestCleaner.php all-temp

# Sadece temp kontrolü (dry run)
php Tests\System\TestCleaner.php dry-run

# Tüm modüller kontrolü (dry run)
php Tests\System\TestCleaner.php dry-all
```

### Test Dosyaları

#### 📁 Mevcut Dosyalar
- **TestCleaner.php**: Ana temizleyici sınıfı (`Tests/System/TestCleaner.php`)
- **quick_clean.php**: Hızlı temizleme scripti (`Tests/System/quick_clean.php`)
- **test_cleaner_example.php**: Detaylı kullanım örnekleri (`Tests/System/test_cleaner_example.php`)

#### 🔧 Yardımcı Metodlar
```php
TestCleaner::listFiles('Temp');                    // Dosya listele
TestCleaner::listFiles('Orders', ['php', 'txt']);  // Uzantı filtreli
TestCleaner::cleanFiles($files, $dryRun);          // Manuel temizle
TestCleaner::cleanTempFiles($dryRun);              // Temp temizle
TestCleaner::cleanOldLogs($days, $dryRun);         // Log temizle
TestCleaner::cleanAllTempFiles($dryRun);           // Tüm modüller temizle
```

### Güvenlik ve Öneriler

#### ✅ Güvenli Kullanım
- **Her zaman Dry Run**: Önce `true` parametresi ile kontrol edin
- **Relative Path**: Tests/ klasörüne göre dosya yolları verin
- **Backup**: Önemli dosyaları yedekleyin
- **Log Kontrol**: `Tests/Logs/` klasöründen işlem loglarını kontrol edin

#### ⚠️ Dikkat Edilecekler
- **Geri Alınamaz**: Silinen dosyalar geri getirilemez
- **Korumalı Dosyalar**: Sistem ana dosyaları otomatik korunur
- **Tests Klasörü**: Sadece Tests/ altındaki dosyalar silinir
- **Uzantı Kontrolü**: İzin verilmeyen uzantılar (.exe, .dll vb.) atlanır

### Örnek Kullanım Senaryoları

#### 🧪 Test Sonrası Temizlik
```powershell
# Test tamamlandıktan sonra hızlı temizlik
php Tests\System\quick_clean.php
```

#### 🗂️ Belirli Modül Temizliği
```php
// Sadece Orders modülündeki geçici dosyalar
$orderTempFiles = array_filter(
    TestCleaner::listFiles('Orders'), 
    fn($file) => strpos(basename($file), 'temp_') === 0
);
TestCleaner::cleanFiles($orderTempFiles);
```

#### 📜 Log Temizliği
```powershell
# Haftalık log temizliği
php Tests\System\TestCleaner.php logs 7
```

### Otomasyona Entegrasyon

#### PowerShell Script Örneği
```powershell
# Her test öncesi otomatik temizlik
Write-Host "🧹 Test ortamı temizleniyor..."
php Tests\System\quick_clean.php
Write-Host "✅ Test ortamı hazır!"
```

#### Batch Script Örneği
```batch
@echo off
echo 🧹 Test ortamı temizleniyor...
php Tests\System\quick_clean.php
echo ✅ Test ortamı hazır!
pause
```

*Test temizleme sistemi aktif ve hazır. Düzenli kullanım önerilir.*
