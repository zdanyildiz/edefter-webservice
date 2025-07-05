# Test Sistem Araçları

Bu dizin, test framework'ünü yönetmek ve organize etmek için geliştirilmiş yardımcı araçları içerir.

## 🛠️ Mevcut Araçlar

### 1. TestCurl.php - HTTP İstek Yardımcısı
API'leri test etmek ve HTTP istekleri göndermek için kullanılır.

```powershell
# Sistem debug bilgileri
php Tests\System\TestCurl.php debug

# Sistem bilgileri
php Tests\System\TestCurl.php info

# GET isteği gönder
php Tests\System\TestCurl.php get /path/to/api action=status

# URL'yi hızlı test et
php Tests\System\TestCurl.php test /path/to/page

# TestMover API'sini test et
php Tests\System\TestCurl.php mover
```

### 2. TestMover.php - Dosya Taşıma ve Organizasyon
Test framework dosyalarını organize eder ve referansları günceller.

```powershell
# Framework dosyalarını organize et
php Tests\System\TestMover.php framework

# Dry run (sadece kontrol)
php Tests\System\TestMover.php dry-framework
```

**Web API Endpoint'leri:**
- `GET /Tests/System/TestMover.php?action=status` - Sistem durumu
- `GET /Tests/System/TestMover.php?action=organize&dry=true` - Dry run organize
- `GET /Tests/System/TestMover.php?action=organize` - Gerçek organize
- `GET /Tests/System/TestMover.php?action=list` - Dosya listele

### 3. TestCleaner.php - Dosya Temizleme
Test dosyalarını güvenli şekilde temizler.

```powershell
# Temp dosyalarını temizle
php Tests\System\TestCleaner.php temp

# Eski logları temizle (30 gün)
php Tests\System\TestCleaner.php logs

# Tüm geçici dosyaları temizle
php Tests\System\TestCleaner.php all-temp

# Dry run (sadece kontrol)
php Tests\System\TestCleaner.php dry-run
```

### 4. quick_clean.php - Hızlı Temizlik
Tek komutla kapsamlı temizlik yapar.

```powershell
# Hızlı temizlik
php Tests\System\quick_clean.php
```

### 5. GetLocalDomain.php - Domain Tespit
Yerel domain'i tespit eder.

```powershell
# Yerel domain öğren
php Tests\System\GetLocalDomain.php
```

## 🔧 Kurulum Sonrası Durum

✅ **Framework Dosyaları Organize Edildi:**
- `TestAssert.php` → `TestModel/TestAssert.php`
- `TestDataGenerator.php` → `TestModel/TestDataGenerator.php`
- `TestLogger.php` → `TestModel/TestLogger.php`
- `TestRunner.php` → `TestModel/TestRunner.php`
- `TestValidator.php` → `TestModel/TestValidator.php`

✅ **Referanslar Güncellendi:**
- `Tests/index.php` - Framework loader
- `Tests/System/TestCleaner.php` - Temizlik scriptleri
- `Tests/System/TestMover.php` - Taşıma scriptleri
- `Tests/System/quick_organize.php` - Hızlı organize

✅ **API Endpoint'leri Aktif:**
- HTTP/JSON tabanlı API'ler çalışıyor
- Web tabanlı dosya yönetimi mümkün
- CLI ve web uyumlu

## 🧪 Test Edilmiş Senaryolar

1. **TestCurl ile API Testleri**
   - GET istekleri: ✅ Çalışıyor
   - JSON parsing: ✅ Çalışıyor
   - Hata yakalama: ✅ Çalışıyor
   - Response detayları: ✅ Çalışıyor

2. **TestMover ile Dosya Organizasyonu**
   - Dry run: ✅ Çalışıyor
   - Gerçek taşıma: ✅ Çalışıyor
   - Referans güncelleme: ✅ Çalışıyor
   - Backup oluşturma: ✅ Çalışıyor

3. **Framework Entegrasyonu**
   - CLI modu: ✅ Çalışıyor
   - Web modu: ✅ Çalışıyor
   - Silent mode: ✅ Çalışıyor
   - Log sistemi: ✅ Çalışıyor

## 📊 Sistem Durumu

```json
{
  "tests_path": "C:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\Tests",
  "testmodel_exists": true,
  "framework_files": {
    "TestModel/TestAssert.php": { "exists": true, "size": 11593 },
    "TestModel/TestDataGenerator.php": { "exists": true, "size": 10184 },
    "TestModel/TestLogger.php": { "exists": true, "size": 5289 },
    "TestModel/TestValidator.php": { "exists": true, "size": 8816 },
    "TestModel/TestRunner.php": { "exists": true, "size": 2566 }
  },
  "temp_files": 2
}
```

## 🎯 Sonuç

Test framework dosya yönetimi ve API sistemi başarıyla kuruldu ve test edildi. Tüm araçlar CLI ve web modunda çalışmaktadır.

**Önerilen Kullanım:**
1. Geliştirme sırasında `TestCurl.php` ile API testleri
2. Dosya organizasyonu için `TestMover.php`
3. Düzenli temizlik için `quick_clean.php`
4. Sistem durumu için `TestCurl.php debug`

Tüm araçlar PowerShell uyumlu ve Windows 11 ortamında test edilmiştir.
