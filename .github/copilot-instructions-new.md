# GitHub Copilot Proje Talimatları

Bu dosya, **erhanozel.globalpozitif.com.tr** projesi için GitHub Copilot asistanının proje yapısını, standartları ve geliştirme süreçlerini anlamasını sağlar.

---

## 🚀 Proje Genel Bilgileri

### Teknoloji Stack
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
2. **Key.php** → Şifreleme anahtarı (`$key="xxx"`)
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

```powershell
# ✅ Doğru
cd "c:\Users\zdany\PhpstormProjects\erhanozel.globalpozitif.com.tr"; php Tests\System\GetLocalDomain.php

# ❌ Yanlış (Linux format)
cd "c:\Users\zdany\PhpstormProjects\erhanozel.globalpozitif.com.tr" && php Tests/System/GetLocalDomain.php
```

**Kurallar:**
- Komut ayırıcı: `;` (PowerShell), `&&` DEĞİL
- Dosya yolu: `\` (Windows), `/` DEĞİL
- **ASLA** `php -r` inline komut kullanma (PowerShell syntax hatası)

### Yerel Ortam
- **Domain Tespiti**: `php Tests\System\GetLocalDomain.php`
- **Database Bilgileri**: `php Tests\System\GetLocalDatabaseInfo.php`
- **Tablo Kontrolleri**: `php Tests\System\GetTableInfo.php [tablename]`

---

## 🗄️ Veritabanı Yönetimi

### Migration Sistemi (Phinx)
**⚠️ KRİTİK**: Artık `database.sql` direkt düzenleme yapmayın!

```powershell
# Migration oluştur
vendor\bin\phinx create MigrationName -c App\Database\phinx.php

# Migration çalıştır
vendor\bin\phinx migrate -c App\Database\phinx.php

# Durum kontrol
vendor\bin\phinx status -c App\Database\phinx.php
```

### Tablo Kontrol Protokolü
**Her model/controller geliştirmeden ÖNCE mutlaka tablo kontrolü yapın:**

```php
include_once 'Tests/System/GetTableInfo.php';

// Tablo varlık kontrolü
if (!checkTableExists('sayfa')) {
    throw new Exception('Sayfa tablosu bulunamadı!');
}

// Sütun varlık kontrolü
if (!checkColumnExists('sayfa', 'sayfaad')) {
    throw new Exception('sayfaad sütunu bulunamadı!');
}
```

---

## 🧪 Test Stratejisi

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

# Database bilgilerini göster
php Tests\System\GetLocalDatabaseInfo.php

# Tablo yapısını kontrol et
php Tests\System\GetTableInfo.php tablename

# Log dosyalarını kontrol et
Get-Content "Public\Log\$(Get-Date -Format 'yyyy-MM-dd').log" -Tail 10
```

### Log Dosyaları
- **Site Log**: `Public/Log/YYYY-MM-DD.log`
- **Admin Log**: `Public/Log/Admin/YYYY-MM-DD.log`
- **Sistem Log**: `Public/Log/errors.log`

---

## 🎯 Geliştirme Süreci

### Yeni Özellik Geliştirme
1. **Analiz**: İlgili sistem prompt dosyasını incele
2. **Tablo Kontrolü**: GetTableInfo.php ile tablo yapısını kontrol et
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

*Bu dokümantasyon sürekli güncellenmektedir. Yeni sistemler keşfedildikçe ilgili bölümler güncellenecektir.*
