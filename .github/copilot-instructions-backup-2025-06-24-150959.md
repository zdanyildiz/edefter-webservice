````````instructions
````instructions
# PROJE PROMPT DOSYASI - @workspace - #codebase
*Bu dosya, GitHub Copilot için proje anlayış ve geliştirme notlarını içerir*

## 🏗️ SİSTEM MİMARİSİ VE OLMAZSA OLMAZ BİLGİLER

### Ana Konfigürasyon Sistemi
- **Config.php**: Ana konfigürasyon sınıfı (`App/Core/Config.php`)
- **Domain.php**: Yerel geliştirme için `l.*` ile başlayan domainler
- **Key.php**: Encryption key (`App/Config/Key.php`) - `$key="xxx"`
- **Sql.php**: Encrypted DB credentials (`App/Config/Sql.php`)
- **Helper.php**: Yardımcı sınıf ve şifre çözme (`App/Helpers/Helper.php`)

### Veritabanı Bağlantı Sistemi
```php
// Config.php'deki ana işleyiş:
// 1. Domain kontrolü (localhost: l.* ile başlarsa)
// 2. Sql.php'den encrypted bilgileri yükle
// 3. Key.php'den encryption key'i al
// 4. Helper->decrypt() ile şifreleri çöz

// Yerel geliştirme (l. ile başlayan domainler için):
$this->dbServerName = $this->Helper->decrypt($dbLocalServerName, $this->key);
$this->dbUsername = $this->Helper->decrypt($dbLocalUsername, $this->key);
$this->dbPassword = $this->Helper->decrypt($dbLocalPassword, $this->key);
$this->dbName = $this->Helper->decrypt($dbLocalName, $this->key);

// Canlı sunucu için:
$this->dbServerName = $this->Helper->decrypt($dbServerName, $this->key);
// ... diğer bilgiler
```

### Test/Geliştirme Ortamında DB Erişimi
```php
// Yöntem 1: Config sistemi kullanarak (Önerilen - Tam sistem entegrasyonu)
require_once 'App/Core/Config.php';
$config = new Config();
$host = $config->dbServerName;
$username = $config->dbUsername;
$password = $config->dbPassword;
$database = $config->dbName;

// PDO bağlantısı:
$pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);

// Yöntem 2: GetLocalDatabaseInfo.php kullanarak (Basit test scriptleri için)
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
```

### Dizin Sabitler ve Yollar
```php
// Config.php'de tanımlanan sabitler:
define("ROOT", $documentRoot . $directorySeparator);
    define("PUBL", ROOT . "Public" . $directorySeparator);
define("LOG", PUBL . "log" . $directorySeparator);
define("APP", ROOT . "App" . $directorySeparator);
define("MODEL", APP . "Model" . $directorySeparator);
define("VIEW", APP . "View" . $directorySeparator);
define("CONTROLLER", APP . "Controller" . $directorySeparator);
define("CONF", APP . "Config" . $directorySeparator);
define("CORE", APP . "Core" . $directorySeparator);
define("DATABASE", APP . "Database" . $directorySeparator);
define("Helpers", APP . "Helpers" . $directorySeparator);
define("LOG_DIR", PUBL . "Log" . $directorySeparator);
define("JSON_DIR", PUBL . "Json" . $directorySeparator);
define("IMG", PUBL . "Image" . $directorySeparator);
define("imgRoot","/Public/Image/");
define("FILE", PUBL . "File" . $directorySeparator);
define("fileRoot","/Public/File/");
define("JS", PUBL . "JS" . $directorySeparator);
define("CSS", PUBL . "CSS" . $directorySeparator);
```

###  Genel İstek Yaşam Döngüsü (Ön Yüz)
Bir kullanıcı siteye eriştiğinde gerçekleşen temel adımlar şunlardır:

1.  **`index.php` Başlatılır:**
    *   `vendor/autoload.php`: Composer bağımlılıkları yüklenir.
    *   `App/Core/Config.php` (`$config`): Ana yapılandırma, sabitler tanımlanır, veritabanı şifreleri çözülür.
    *   `App/Core/Database.php` (`$db`): Veritabanı bağlantısı kurulur.
    *   `App/Core/Router.php` (`$router`): Gelen URL analiz edilir; `languageID`, `contentType` (örn: PAGE, CATEGORY), `contentName`, `contentID` belirlenir.
        *   Eğer istek `ADMIN` veya `WEBSERVICE` ise, ilgili akışlar `index.php` içinde yönetilir ve sonlanır.
2.  **Oturum ve Casper:**
    *   `App/Core/Session.php` (`$session`): PHP oturumu başlatılır.
    *   `App/Core/Casper.php` (`$casper`): Oturumdan alınır veya yeni oluşturulur. `Config` nesnesini alır, `siteConfig` (site ayarları, versiyon vb.), üye ve sepet gibi oturuma bağlı verileri yönetir.
3.  **Ziyaretçi Takibi:**
    *   `App/Controller/VisitorController.php`: Ziyaretçi bilgileri işlenir/kaydedilir.
4.  **Ana İçerik Controller'ı:**
    *   `$router`'ın belirlediği `contentType` ve `contentName`/`contentID`'ye göre ilgili ana controller (örn: `HomePageController`, `PageController`, `CategoryController`) yüklenir ve çalıştırılır.
5.  **Veri ve Banner İşleme (İlgili Controller İçinde):**
    *   Controller, gerekli verileri Modeller aracılığıyla veritabanından çeker.
    *   Banner'lar için `App/Core/BannerManager.php` (singleton, cache destekli) ve `App/Controller/BannerController.php` kullanılır. Banner HTML, CSS ve JS'i üretilir.
6.  **View Yükleme:**
    *   Controller, toplanan verileri ve banner içeriklerini `Config->loadView()` metodu ile ilgili View dosyasına (`App/View/`) aktarır.
7.  **HTML Çıktısı:**
    *   View, son HTML sayfasını oluşturur ve tarayıcıya gönderir.




## PROJE YAPISINI ANLAMAK
### Temel Dizin Yapısı:
```
App/
  ├── Core/         # Casper.php, Config.php, BannerManager.php, Router.php
  └── Database/     # database.sql, phinx.php, migrations/, seeds/

_y/               # Site Kontrol Paneli
_y/s/s/           # Banner özel dosyaları ve SQL'ler
Public/           # Statik dosyalar (CSS, JS, Image)
Tests/            # Test ve geçici dosyalar
```

### Temel Sınıflar:
- **Config**: Ana konfigürasyon, sabit tanımları, DB şifre çözme ve include yöneticisi.
- **Router**: URL'leri analiz eder, istekleri `contentType`, `languageID` gibi kritik bilgileri belirleyerek doğru controller'lara yönlendirir.
- **Casper**: Oturum (`Session`) tabanlı veri yöneticisidir. Kullanıcıya özel bilgileri (sepet, üye durumu vb.), genel site ayarlarını (`siteConfig` aracılığıyla) ve `Config` nesnesini tutar. Oturumda saklanır.
- **BannerManager**: Banner render optimizasyon sistemi (Singleton, cache kullanır. Detaylar: `Tests/Banners/banner_prompt.md`).
- **Database**: Veritabanı bağlantı sınıfı (PDO kullanır).



## TEST ORTAMI KULLANIMI
### Dosya Konumları:
- **Geçici dosyalar**: Tests/Temp/
- **Banner testleri**: Tests/Banners/
- **Database testleri**: Tests/Database/


## 💻 Geliştirme Ortamı Bilgileri

### 🖥️ Geliştirme Ortamı Yapılandırması
*Copilot asistanı, aşağıdaki ortam bilgilerini dikkate alarak kod üretmeli ve komutları bu yapılandırmaya uygun olarak çalıştırmalıdır:*

- **İşletim Sistemi**: Windows 11
- **Web Server**: IIS (Internet Information Services)
- **PHP Version**: PHP 8.3.4
- **IDE**: Visual Studio Code
- **Shell**: PowerShell (varsayılan) - `&&` yerine `;` kullanılmalı
- **Database**: MySQL 
- **Project Root**: `c:\Users\zdany\PhpstormProjects\{project_name}`
- **Yerel Site URL**: `http://{local_domain}/` (dinamik olarak tespit edilir)

**⚠️ Önemli Notlar:**
- Tüm terminal komutları PowerShell formatında olmalıdır
- Dosya yolları Windows formatında (`\` ayırıcı) yazılmalıdır
- Local domain her proje için farklıdır, `GetLocalDomain.php` ile tespit edilir


### 💻 Terminal/PowerShell Komut Formatı
**Copilot asistanı, tüm terminal komutlarını aşağıdaki PowerShell formatında üretmelidir:**

```powershell
# ✅ Doğru format (PowerShell için)
cd "c:\Users\zdany\PhpstormProjects\{project_name}"; php Tests\Banners\BannerCSSAnalyzer.php

# ✅ Çoklu komut zinciri
cd "c:\Users\zdany\PhpstormProjects\{project_name}"; php Tests\System\GetLocalDomain.php; php Tests\System\GetLocalDatabaseInfo.php

# ❌ Yanlış format (Linux/Unix için) - KULLANILMAMALI
cd "c:\Users\zdany\PhpstormProjects\{project_name}" && php Tests\Banners\BannerCSSAnalyzer.php
```

**Kritik Kurallar:**
- Komut ayırıcı olarak `;` kullanın, `&&` kullanmayın
- Dosya yollarında `\` (backslash) kullanın, `/` (forward slash) kullanmayın
- Tüm yolları çift tırnak içinde yazın: `"path\to\file"`
- PHP dosyaları için tam yol belirtin: `php Tests\System\FileName.php`

### ⚠️ KRITIK: PowerShell PHP Inline Komut Hatası
**ASLA PowerShell'de `php -r` ile inline kod kullanma!**

```powershell
# ❌ YANLIŞ - Syntax hataları oluşur:
php -r "include_once 'file.php'; \$var = \$something;"

# ✅ DOĞRU - Her zaman ayrı script dosyası oluştur:
# 1. Önce geçici PHP script dosyası oluştur
# 2. Sonra o dosyayı çalıştır
php Tests\System\TempScript.php
```

**Sebep:** PowerShell'de `\$`, `\'`, `\"`, `[]`, `;` gibi karakterler farklı yorumlanır ve PHP syntax hatalarına neden olur.

**Çözüm:** Karmaşık sorgular için her zaman ayrı `.php` dosyası oluşturup çalıştır.

**Örnekler:**
- `Tests\System\PowerShellTestExample.php` - Güvenli PowerShell PHP kullanımı  
- `Tests\System\PowerShellPHPDemo.php` - Inline komut sorunları demonstrasyonu

```
php Tests\System\TempScript.php
```

**Sebep:** PowerShell'de `\$`, `\'`, `\"`, `[]`, `;` gibi karakterler farklı yorumlanır ve PHP syntax hatalarına neden olur.

**Çözüm:** Karmaşık sorgular için her zaman ayrı `.php` dosyası oluşturup çalıştır.

**Örnekler:**
- `Tests\System\PowerShellTestExample.php` - Güvenli PowerShell PHP kullanımı  
- `Tests\System\PowerShellPHPDemo.php` - Inline komut sorunları demonstrasyonu

** NOTLAR
- Banner sisteminde her zaman BannerManager kullan
- Test dosyaları oluştururken Tests/ dizinini kullan
- Veritabanı değişikliklerinde SQL dosyalarını güncelle
- Hata düzeltmelerini dokümante et
- **⚠️ ÖNEMLİ: min.css ve min.js dosyaları otomatik oluşturulur**
  - `.min.css` ve `.min.js` dosyalarını manuel olarak oluşturmayın
  - Bu dosyalar sistem tarafından otomatik olarak generate edilir
  - Sadece ana CSS ve JS dosyalarını düzenleyin (örn: `banner.css`, `app.js`)
  - Sistem, gerektiğinde bu dosyaların minified versiyonlarını otomatik oluşturur

---


### 🔧 Sistem Mimarisi Özetı:
```
Config.php (domain kontrolü + DB decrypt)
    ↓
Key.php (encryption key)
    ↓  
Sql.php (encrypted credentials)
    ↓
Helper.php (decrypt metodu)
    ↓
BannerManager → BannerController → CSS/HTML
```

### 🌐 Yerel Test Adresi:
**Yerel Site Adresinin Tespiti:** 
- Yerel domainler her zaman `l.` öneki ile başlar (örneğin: `l.zafer`, `l.hastane`, `l.erhanozel`) 
- Bu domain bilgisi dinamiktir ve `App/Config/Domain.php` dosyasında tanımlanır
- Yerel domaini programatik olarak tespit etmek için: `php Tests/System/GetLocalDomain.php` komutunu çalıştırabilirsiniz
- Her projenin kendi yerel domain yapılandırması olduğundan, kodda sabit bir domain ismi kullanmaktan kaçınılmalıdır

### 📋 Sonraki Geliştirme Adımları:
1. **Yeni sistem keşfederken** → Tests/[sistem]/ dizininde analiz yap
   2. **Her yeni bulgu için** → İlgili prompt dosyasını güncelle
3. **Adım adım ilerle** → Her değişikliği dokümantate et

### 🔍 Gelecek Analiz Hedefleri:
- Product sistemi → Tests/Products/product_prompt.md
- Member sistemi → Tests/Members/member_prompt.md
- Payment sistemi → Tests/Payments/payment_prompt.md
- SEO sistemi → Tests/SEO/seo_prompt.md

**Proje İlkesi:** Her sistem için ayrı detaylı prompt + ana prompt'ta özet referans

## 🚨 ÖNEMLİ: Log Okuma (Hataları takip etmek için kullan)
-çalıştırılan projenin log çıktıları site log, admin log ve sistem log olarak gruplanmışştır.
-site: /Public/Log/2025-06-18.log (o gün tarihli, yıl ay gün)
-admin: /Public/Log/Admin/2025-06-15.log (o gün tarihli, yıl ay gün)
-sistem: /Public/Log/errors.log

## 🚨 ÖNEMLİ: VERİTABANI DEĞİŞİKLİK KURALI
**Projede artık Phinx migration sistemi kullanılmaktadır. Veritabanı değişikliklerini doğrudan `database.sql` dosyasında yapmak yerine migration dosyaları kullanın:**

1. **Migration Bazlı Veritabanı Yönetimi:**
   - Doğrudan `App/Database/database.sql` dosyasını düzenlemeyin
   - Her yeni veritabanı değişikliği için ayrı bir migration dosyası oluşturun
   ```bash
   # Migration oluşturma:
   vendor\bin\phinx create MigrationAdi -c App\Database\phinx.php
   ```
   - Migration dosyasını düzenleyin (`App/Database/migrations/` klasöründe)
   - Değişikliği veritabanına uygulayın:
   ```bash
   # Migration çalıştırma:
   vendor\bin\phinx migrate -c App\Database\phinx.php
   ```

2. **Örnek Migration İş Akışı:**
   ```php
   // App/Database/migrations/20250625123456_add_new_column_to_users.php
   public function change(): void
   {
       // Tabloya yeni sütun ekleme
       $this->table('users')
           ->addColumn('new_column', 'string', ['limit' => 255, 'null' => true])
           ->update();
           
       // Tablo oluşturma örneği
       $this->table('new_table', ['id' => 'table_id'])
           ->addColumn('name', 'string', ['limit' => 50])
           ->addColumn('created_at', 'datetime')
           ->create();
   }
   ```

3. **Migration Durumunu Kontrol Etme:**
   ```bash
   # Migration durumu:
   vendor\bin\phinx status -c App\Database\phinx.php
   ```

4. **Eski Yaklaşım (database.sql) ile Karşılaştırma Avantajları:**
   - ✅ Daha küçük ve yönetilebilir değişiklikler
   - ✅ Değişiklik geçmişi takibi
   - ✅ Geriye dönüş (rollback) imkanı
   - ✅ Farklı ortamlarda tutarlı veritabanı yapısı
   - ✅ Takım çalışmasında daha az çakışma

**NOT**: 
- `Tests/System/GetLocalDomain.php` betiği local domain bulmak için
- `Tests/System/GetLocalDatabaseInfo.php` betiği yerel veritabanı bilgilerini almak için
- `Tests/Database/simple-marker.php` betiği ise mevcut veritabanını Phinx ile senkronize etmek için kullanılabilir.

**Şifre Çözme Sistemi:**
    - Helper.php decrypt() metodu kullanılır
    - Key.php'den encryption key alınır: `$key="xx"`
    - Sql.php'den encrypted DB bilgileri çözülür

### 🔑 Yerel Veritabanı Bilgilerine Erişim
**Programatik Erişim - GetLocalDatabaseInfo.php:**
```php
// Include ederek fonksiyon çağrısı
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();

// Dönen array:
[
    'serverName' => 'localhost',
    'username' => 'root', 
    'password' => 'şifre',
    'database' => 'veritabanı_adı'
]

// PDO bağlantısı için kullanım:
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
```

**CLI Kullanımı:**
```powershell
# Veritabanı bilgilerini göster
php Tests\System\GetLocalDatabaseInfo.php

# Çıktı:
# === YEREL VERİTABANI BİLGİLERİ ===
# Server: localhost
# Username: root
# Password: şifre
# Database: veritabanı_adı
```

### 🔍 Tablo ve Sütun Kontrol Sistemi
**⚠️ KRİTİK KURAL: Her model/controller geliştirme öncesi MUTLAKA tablo kontrolü yapın!**

**Tablo Kontrol Fonksiyonları - GetTableInfo.php:**
```php
// Include ederek fonksiyon kullanımı
include_once 'Tests/System/GetTableInfo.php';

// Tek tablo kontrolü
$tableInfo = getTableInfo('sayfa');
if (!$tableInfo['exists']) {
    echo "HATA: sayfa tablosu bulunamadı!";
    return;
}

// Sütun varlık kontrolü
if (!checkColumnExists('sayfa', 'sayfaad')) {
    echo "HATA: sayfa.sayfaad sütunu bulunamadı!";
    return;
}

// Çoklu tablo kontrolü
$tables = ['sayfa', 'dil', 'language_page_mapping'];
$tablesInfo = getMultipleTablesInfo($tables);

// Sütun listesi alma
$columns = getTableColumns('sayfa');
```

**CLI Kullanımı:**
```powershell
# Tek tablo kontrolü (basit)
php Tests\System\GetTableInfo.php sayfa

# Tek tablo kontrolü (detaylı)
php Tests\System\GetTableInfo.php sayfa detail

# Test scripti çalıştırma
php Tests\System\TestTableInfo.php
```

**Geliştirme Süreci Kuralları:**
1. **Model Oluşturma Öncesi**: İlgili tabloların varlığını kontrol edin
2. **Controller Geliştirme**: Kullanılacak tablo/sütunları doğrulayın
3. **SQL Sorguları**: Sütun adlarını GetTableInfo ile onaylayın
4. **Migration Yazma**: Mevcut tablo yapısını analiz edin

**Örnek Geliştirme Akışı:**
```php
// 1. Tablo kontrolü yap
$pageTableInfo = getTableInfo('sayfa');
if (!$pageTableInfo['exists']) {
    throw new Exception('Sayfa tablosu bulunamadı!');
}

// 2. Gerekli sütunları kontrol et
$requiredColumns = ['sayfaid', 'sayfaad', 'sayfaicerik'];
foreach ($requiredColumns as $column) {
    if (!checkColumnExists('sayfa', $column)) {
        throw new Exception("Gerekli sütun bulunamadı: $column");
    }
}

// 3. Şimdi güvenle model/controller geliştir
```

# **Site yapısı Kontrol Panel ilişkisi**
-/_y/s/b/menu.php kodlarını inceleyerek örneğin bir sayfa ekleme işlemini hangi sayfadan hangi yöntemleri kullanarak yapıyoruz belirlerin. Bir sınıfı anlama için /App/Controller/SınıfAdı,/App/Controller/Admin/AdminSınıfAdı, /App/Model/SınıfAdı,/App/Model/Admin/AdminSınıfAdı dosyalarını da mutlaka inceleyin.


### 📈 PROJE DOKÜMANTASYON DURUMU

### ✅ Tamamlanan Sistemler (Model Context Protocol ile)
1. **Banner Sistemi** - 100% Tamamlandı
    - Frontend: `Tests/Banners/banner_prompt.md` (863 kelime)
    - Admin Panel: `Tests/Banners/banner_admin_prompt.md` (2877 kelime)
    - CSS Refactoring: `Public/CSS/Banners/tepe-banner.css` (dinamik sınıflar)
    - Test Scripts: `Tests/Banners/BannerAdminAnalyzer.php`

2. **Product/E-commerce Sistemi** - 100% Tamamlandı
    - Sistem Dokümantasyonu: `Tests/Products/product_prompt.md` (2036 kelime)
    - Admin CRUD: AddProduct.php analizi
    - Varyant Yönetimi: ProductVariant sistemi
    - Kategori Entegrasyonu: Category model analizi

3. **Member/Authentication Sistemi** - 100% Tamamlandı
    - Sistem Dokümantasyonu: `Tests/Members/member_prompt.md` (1639 kelime)
    - Admin Panel: AddMember.php ve MemberList.php analizi
    - Güvenlik: Veri şifreleme sistemi
    - Adres Yönetimi: Çoklu adres desteği

4. **Order/Payment Sistemi** - 100% Tamamlandı
    - Sistem Dokümantasyonu: `Tests/Orders/order_prompt.md` (2178 kelime)
    - Ödeme Entegrasyonları: PayTR, Halbank
    - Kargo Sistemi: Takip ve barkod sistemi
    - Admin Panel: OrderList.php ve CreateOrder.php


### 🔄 Sistem Entegrasyonu Haritası
```
     ┌─────────────┐    ┌─────────────┐
     │   Banner    │    │   Product   │
     │   System    │────│   System    │
     └─────────────┘    └─────────────┘
              │                  │
              │                  │
     ┌─────────────┐    ┌─────────────┐
     │   Member    │────│    Order    │
     │   System    │    │   System    │
     └─────────────┘    └─────────────┘
              │                  │
              └─────── Cart ──────┘
```

### 🎯 PHASE 2 - Gelecek Sistemler

#### Öncelikli Sistemler
1. **SEO Sistemi** - Tests/SEO/seo_prompt.md
    - SeoModel.php ve AdminSeo.php analizi
    - Meta tag yönetimi
    - Canonical URL sistemi
    - Sitemap oluşturma

2. **Cart/Sepet Sistemi** - Tests/Cart/cart_prompt.md
    - Cart.php model analizi
    - AdminCart.php CRUD işlemleri
    - Session tabanlı sepet yönetimi
    - Çoklu para birimi desteği

3. **Email/Notification Sistemi** - Tests/Email/email_prompt.md
    - EmailSender.php helper analizi
    - SMTP ayarları yönetimi
    - Template sistemi
    - Otomatik bildirimler

#### İkincil Sistemler
4. **File/Image Sistemi** - Tests/Media/media_prompt.md
5. **Location Sistemi** - Tests/Location/location_prompt.md
6. **Language/Multi-language** - Tests/Language/language_prompt.md
7. **Admin Panel Core** - Tests/Admin/admin_prompt.md

### 🧪 Test Automation Expansion
- **BannerSystemTester.php** - Banner sistemi otomatik testleri
- **ProductSystemTester.php** - Ürün sistemi testleri
- **MemberSystemTester.php** - Üye sistemi testleri
- **OrderSystemTester.php** - Sipariş sistemi testleri
- **IntegrationTester.php** - Sistem entegrasyon testleri

### 📋 Methodology Notes
**Model Context Protocol Kriterleri:**
1. ✅ AMAÇ VE KAPSAM - Sistem sorumluluklarını net tanımla
2. ✅ SİSTEM MİMARİSİ - Dosya/sınıf yapısını görselleştir
3. ✅ VERİTABANI YAPISI - Tablo şemalarını detaylandır
4. ✅ TROUBLESHOOTING - Yaygın sorunlar ve çözümler
5. ✅ GELİŞTİRME REHBERİ - Yeni özellik ekleme adımları
6. ✅ ENTEGRASYON - Diğer sistemlerle bağlantılar

**Sürekli Güncelleme:**
- Her yeni özellik/refactor sonrası prompt güncelleme
- Test scriptleri ile dokümantasyon doğrulama
- Cross-reference kontrolü (sistem arası bağlantılar)
- Performance impact analizi

## 🔍 MODÜL ANALİZ SONUÇLARI - SİSTEMATİK PROJEKSİYON
*Son analiz: 2025-06-15 19:07:37*

### 📊 Advanced Project Analyzer ile Keşfedilen Sistemler

#### 🎯 Ana Modüller (Controller + Model + Database)
1. **Banner Sistemi** ✅ Tamamlanmış
    - Controller: BannerController (16 method)
    - Model: Banner + BannerModel
    - Database: 6 tablo (banners, banner_groups, banner_layouts, banner_types, banner_styles, banner_display_rules)
    - CSS: 30 dosya, JS: 4 dosya
    - Prompt: `Tests/Banners/banner_system_prompt.md`

2. **Member/Üye Sistemi** ✅ Analiz Tamamlandı
    - Controller: MemberController
    - Model: Member
    - Database: uye, uyeadres, uyebayigrup, uyesepet, uyesiparis
    - Prompt: `Tests/Members/member_system_prompt.md`

3. **Cart/Sepet Sistemi** ✅ Analiz Tamamlandı
    - Controller: CartController
    - Model: Cart
    - Database: uyesepet, uyesepetdurum
    - Prompt: `Tests/Carts/cart_system_prompt.md`

4. **Category/Kategori Sistemi** ✅ Analiz Tamamlandı
    - Controller: CategoryController
    - Model: Category
    - Database: kategori
    - Views: 3 dosya
    - Prompt: `Tests/Categorys/category_system_prompt.md`

5. **HomePage/Ana Sayfa Sistemi** ✅ Analiz Tamamlandı
    - Controller: HomePageController
    - Model: HomePage
    - Views: 2 dosya
    - Prompt: `Tests/HomePages/homepage_system_prompt.md`

#### 🔧 Yardımcı Modüller
6. **AI Sistemi**
    - Controller: AIController
    - OpenAI entegrasyonu

7. **Search/Arama Sistemi**
    - Controller: SearchController
    - Model: Search + ProductSearch
    - Views: 2 dosya

8. **Page/Sayfa Sistemi**
    - Controller: PageController
    - Model: Page
    - Views: 11 dosya (En fazla view dosyası)

9. **Form Sistemi**
    - Controller: FormController
    - Model: Form

10. **Image/Resim Sistemi**
    - Controller: ImageController
    - Model: Image + Gallery

#### 🛠️ Özel Modüller

12. **BannerModel** - Banner model yönetimi için özel controller
13. **Checkout** - Ödeme sistemi
14. **Payment** - Ödeme işlemleri
15. **Popup** - Popup yönetimi
16. **Cookie** - Cookie yönetimi
17. **Location** - Konum/adres sistemi
18. **Visitor** - Ziyaretçi takibi


### 🔗 WEB ANALİZ ARAYÜZÜ
**Dosya**: `Tests/Analyzer/analyzer.html`
**Özellikler**:
- Modül keşfi ve analiz butonları
- Ana site test linki: http://{local_domain}/
- Veritabanı test arayüzü
- Gerçek zamanlı log görüntüleme
- Console entegrasyonu

**Kullanım**:
```javascript
// Browser console'da:
projectAnalyzer.executeCommand('discover', 'Modül keşfi');
projectAnalyzer.modules(); // Modül listesi
```

### 🎯 SONRAKI ADIMLAR

#### Öncelik 1: Eksik Controller'ları Tamamla
1. **ProductController.php** oluştur
    - Model: Product.php, ProductVariant.php, ProductSearch.php mevcut
    - Database tabloları mevcut
    - E-commerce için kritik

2. **OrderController.php** oluştur
    - Model: Order.php mevcut
    - Database: uyesiparis, uyesiparisdurum tabloları mevcut
    - Sipariş yönetimi için kritik

#### Öncelik 2: Derin Analiz
1. **Product Sistemi** - Tüm product tabloları ve model ilişkilerini analiz et
2. **Order/Payment Sistemi** - Ödeme entegrasyonları ve sipariş akışını analiz et
3. **SEO Sistemi** - Seo.php, SeoModel.php analizi




## 🖼️ BANNER RESİM SİSTEMİ KEŞFİ
*Chrome DevTools Network analizi sonucu keşfedilen sistem*

### 📍 Resim Handler Sistemi
- **URL Formatı**: `http://{local_domain}/Public/Image/?imagePath=Banner/dosya_adi.jpg`
- **Handler Dosyası**: `Public/Image/index.php`
- **Gerçek Dosya Yeri**: `Public/Image/Banner/`
- **Sistem**: IMG sabiti + imagePath parametresi

### 🔍 İşleyiş Mekanizması
```php
// Public/Image/index.php sisteminin özeti:
$imagePath = $_GET['imagePath'] ?? null; // Banner/tepe-banner_1.jpg
$imagePath = IMG . $imagePath; // Public/Image/ + Banner/tepe-banner_1.jpg
$width = $_GET['width'] ?? null; // Otomatik resize
$height = $_GET['height'] ?? null; // Otomatik resize

// Image.php helper'ı ile resim işleme
$image = new Image($imagePath);
$imageOutputPath = $image->resize($width, $height);
```



### 🔧 Resim Handler Özellikleri
1. **Otomatik Resize**: width/height parametreleri ile
2. **Format Desteği**: JPEG, PNG, GIF, WebP
3. **Hata Yönetimi**: Dosya bulunamadığında 500x500 placeholder
4. **Cache**: Resize edilmiş görseller cache'leniyor
5. **Crop Desteği**: (Geliştirme aşamasında - @todo)


## 📝 GÜNCEL GELIŞTIRME NOTLARI



**_y/s/s/tasarim/Design.php**:
- getCustomCSS() fonksiyonu çoklu CSS dosyası desteği ile güncellendi
- CSS→JSON dönüştürme sistemi geliştirildi
- Değişken referans çözme algoritması iyileştirildi

**App/Controller/Admin/AdminDesignController.php**:
- Yeni tema değişkenleri için form parametreleri eklendi
- primary-light-color, primary-dark-color gibi varyant renkler
- text-primary-color, text-secondary-color gibi metin renkleri
- background-primary-color, background-secondary-color
- border-color varyantları ve border-radius değişkenleri

#### 🎯 ÖNEMLİ: Tema Sistemi Mimarisi
- **CSS Değişkenler**: Modern CSS custom properties sistemi
- **JSON Entegrasyonu**: Design.php ile otomatik CSS→JSON dönüştürme
- **Responsive Design**: Breakpoint sistemi ile mobil uyumluluk
- **Renk Paleti**: Tutarlı renk sistemi (primary, secondary, accent, semantic colors)
- **Tipografi**: Ölçeklenebilir yazı tipi sistemi

# Genel Geliştirme Talimatları

Bu talimatlar, bu proje üzerinde çalışırken uyman gereken temel kuralları, standartları ve yetenekleri tanımlar. Amacımız, tutarlı, test edilebilir ve yüksek kaliteli kod üretmektir.

---

## 1. Genel Kodlama Standartları

* **Dil ve Teknoloji:** Bu proje PHP, MySQL, HTML5, CSS ve JavaScript kullanılarak geliştirilmiştir. Tüm kod önerilerin bu teknolojilere ve en iyi pratiklerine uygun olmalıdır.
* **Kodlama Stili:** Değişkenler için `camelCase`, sınıflar için `PascalCase` kullanılacaktır. PSR-12 kodlama standartlarına uymaya özen göster.
* **Kod Formatı:** Tüm dillerde (PHP, JavaScript, CSS) her komut/statement ayrı satırda olmalıdır. Tek satırda birden fazla komut sıkıştırma. Her blok uygun girintileme ile yazılmalıdır.
* **Yorumlama:** Anlaşılması zor veya karmaşık olan tüm fonksiyonlar, sınıflar ve algoritmalar için **Türkçe** ve **açıklayıcı** yorum blokları (phpDoc formatında) ekle.
* **Güvenlik:** SQL Injection, XSS ve CSRF gibi zafiyetlere karşı daima dikkatli ol. Veritabanı sorgularında **PDO prepared statements** kullan. Kullanıcıdan gelen verileri asla doğrudan sorguya ekleme.

---

## 2. Test ve Doğrulama Yetenekleri

Projenin kalitesini sağlamak için testler kritik öneme sahiptir. Aşağıdaki araçları ve yöntemleri kullanarak proaktif olarak test kodu oluşturabilir ve analiz yapabilirsin.

### ⚠️ ÖNEMLİ: Ne Zaman Test Dosyası Oluşturmayacaksın

**Basit CSS/JS hataları için test dosyası oluşturma:**
- JavaScript fonksiyon tanımlı değil hatası
- CSS stil sorunları (görsel düzen, renk, spacing)
- HTML tab sistemleri, form görünümü gibi UI hatalar
- Bootstrap, jQuery gibi frontend kütüphane entegrasyonu sorunları

**Bu durumlar için doğrudan:**
1. İlgili dosyayı düzenle (CSS, JS, PHP)
2. Tarayıcıda test et
3. Hata loglarını kontrol et
4. **Test dosyası oluşturma!**

### ✅ Ne Zaman Test Dosyası Oluşturacaksın

**Sistem entegrasyonu ve karmaşık işlevsellik için:**
- Veritabanı CRUD işlemleri
- Ödeme sistemi entegrasyonları
- Email gönderme sistemleri
- API entegrasyonları
- Multi-step formlar ve işlem akışları
- Güvenlik sistemleri (authentication, authorization)

* **Tarayıcı Testleri (Playwright):** Her modül veya sınıf için kullanıcı arayüzü testleri gerekebilir. Bir özellik geliştirildiğinde veya değiştirildiğinde, bu değişikliği doğrulayan bir **Playwright testi** oluşturmanı isteyebilirim. Kullanıcı etkileşimlerini (form doldurma, butona tıklama, gezinme) simüle etmeli ve sonuçları (DOM değişiklikleri, konsol çıktıları) doğrulamalısın.
* **HTML Çıktı Analizi (PHP Test Scripts):** Bir PHP sayfasının sunucu tarafında nasıl render edildiğini anlamak için `Tests/` klasörü altında PHP test scripti oluştur. `curl` komutları sistemi durdurabilir, bu nedenle `file_get_contents()` veya `cURL` PHP kütüphanesini kullanarak HTTP istekleri yapan PHP dosyaları oluştur. Bu scriptler belirli bir elementin varlığını veya içeriğini kontrol etmeli ve JavaScript'in çalışmadığı ortamdaki çıktıyı test edebilmelidir.

---

## 3. Veri ve Veritabanı Etkileşimi

Veritabanı, projemizin kalbidir. Veritabanı şemasını ve verilerini anlaman, daha isabetli kodlar üretmeni sağlar.

* **⚠️ ZORUNLU: Tablo Kontrol Protokolü** 
  - **Her model/controller geliştirmeden ÖNCE** `Tests/System/GetTableInfo.php` ile tablo yapısını kontrol et
  - **Her SQL sorgusu yazarken** sütun adlarının doğru olduğunu onaylat
  - **Migration yazmadan önce** mevcut tablo yapısını analiz et
  
* **Tablo Kontrol Araçları:**
  ```php
  include_once 'Tests/System/GetTableInfo.php';
  
  // Tablo var mı?
  if (!checkTableExists('sayfa')) {
      throw new Exception('Sayfa tablosu bulunamadı!');
  }
  
  // Sütun var mı?
  if (!checkColumnExists('sayfa', 'sayfaad')) {
      throw new Exception('sayfaad sütunu bulunamadı!');
  }
  
  // Tablo detaylarını al
  $tableInfo = getTableInfo('sayfa');
  printTableInfo('sayfa', true); // Debug için
  ```

* **Veritabanı Analizi:** Sana sağlanan veritabanı bağlantı bilgilerini kullanarak (güvenli aracı script `Tests/System/GetLocalDatabaseInfo.php` aracılığıyla), mevcut **tabloları listeleyebilir**, bir tablonun **sütun yapısını (`DESCRIBE`) çekebilir** ve hatta belirli sorgularla örnek **verileri analiz edebilirsin**. Bu bilgileri, özellikle karmaşık sorgular veya Model sınıfları yazarken bağlam olarak kullan.

---

## 4. Proje Anlama ve Geliştirme

Sen statik bir kod yazıcı değilsin, projenin gelişimine katkıda bulunan bir asistansın.

* **Proje Talimatlarını Geliştirme:** Bu projeyi ve kod yapısını daha iyi anladıkça, bu talimatlar dosyasını daha verimli hale getirmek için **bana önerilerde bulun**. Örneğin, sık tekrarlanan bir kod deseni fark edersen, bunu standartlaştırmak için yeni bir kural önerebilirsin. Projenin mimarisini (Model-Controller yapısı vb.) analiz ederek bu talimatlara eklemeler yapmanı bekliyorum.
* **Bağlam Analizi:** Bir görev verildiğinde, sadece o anki dosyayı değil, `@workspace` aracılığıyla projenin genelini analiz et. İlgili modelleri, servisleri ve controller'ı bularak bütüncül bir çözüm üret.
````````