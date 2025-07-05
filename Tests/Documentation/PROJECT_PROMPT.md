# PROJE PROMPT DOSYASI - yeni.globalpozitif.com.tr
*Bu dosya, GitHub Copilot için proje anlayış ve geliştirme notlarını içerir*

## 🏗️ SİSTEM MİMARİSİ VE OLMAZSA OLMAZ BİLGİLER

### Ana Konfigürasyon Sistemi
- **Config.php**: Ana konfigürasyon sınıfı (`App/Core/Config.php`)
- **Key.php**: Encryption key (`App/Config/Key.php`) - `$key="Aom1eP50h72aEcCNSb4@(*722SXOHfmE"`
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
// Doğru yöntem - Config sistemi kullanarak:
require_once 'App/Core/Config.php';
$config = new Config();
$host = $config->dbServerName;
$username = $config->dbUsername;
$password = $config->dbPassword;
$database = $config->dbName;

// PDO bağlantısı:
$pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
```

### Dizin Sabitler ve Yollar
```php
// Config.php'de tanımlanan sabitler:
define('ROOT', $projectRoot . '/');
define('APP', ROOT . 'App/');
define('CORE', APP . 'Core/');
define('CONF', APP . 'Config/');
define('CONTROLLER', APP . 'Controller/');
define('MODEL', APP . 'Model/');
define('VIEW', APP . 'View/');
define('HELPERS', APP . 'Helpers/');
define('DATABASE', APP . 'Database/');
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

## 📊 BANNER SİSTEMİ ÖZETİ

### Banner Sistem Durumu: ✅ TAMAMLANDI
- **Frontend Sistemi**: `Tests/Banners/banner_prompt.md` - Tüm teknik detaylar
- **Admin Panel Sistemi**: `Tests/Banners/banner_admin_prompt.md` - CRUD işlemleri ve MCP
- **Optimizasyon**: BannerManager singleton + cache sistemi aktif
- **CSS Modernizasyonu**: 4 ana banner türü tamamen yenilendi (%32 sıkıştırma)
- **Tepe Banner**: Layout group mismatch sorunu çözüldü

## PROJE YAPISINI ANLAMAK
### Temel Dizin Yapısı:
```
App/
  ├── Core/         # Casper.php, Config.php, BannerManager.php, Router.php
  └── Database/     # database.sql dosyası

_y/s/s/           # Banner özel dosyaları ve SQL'ler
Public/           # Statik dosyalar (CSS, JS, Image)
Tests/            # Test ve geçici dosyalar (YENİ!)
```

### Temel Sınıflar:
- **Config**: Ana konfigürasyon, sabit tanımları, DB şifre çözme ve include yöneticisi.
- **Router**: URL'leri analiz eder, istekleri `contentType`, `languageID` gibi kritik bilgileri belirleyerek doğru controller'lara yönlendirir.
- **Casper**: Oturum (`Session`) tabanlı veri yöneticisidir. Kullanıcıya özel bilgileri (sepet, üye durumu vb.), genel site ayarlarını (`siteConfig` aracılığıyla) ve `Config` nesnesini tutar. Oturumda saklanır.
- **BannerManager**: Banner render optimizasyon sistemi (Singleton, cache kullanır. Detaylar: `Tests/Banners/banner_prompt.md`).
- **Database**: Veritabanı bağlantı sınıfı (PDO kullanır).

## HATALAR ve ÇÖZÜMLERİ
### Çözülen Hatalar:
1. **"Class BannerManager not found"**
   - Çözüm: Manuel include kontrolü + Config.php düzeltmesi

2. **"display_rules must be of type array, null given"**
   - Çözüm: matchesPageAndCategory metoduyla doğrudan page_id/category_id kullanımı

## PROJE GELİŞTİRME SÜRECİ
### Tamamlanan Görevler:
- [x] Banner sistem analizi
- [x] BannerManager optimizasyon sistemi
- [x] Cache mekanizması
- [x] Hata düzeltmeleri
- [x] SQL güncellemeleri
- [x] Dokümantasyon
- [x] Test ortamı kurulumu
- [x] Test sisteminin geliştirilmesi
- [x] BannerTester ve DatabaseTester oluşturulması
- [x] TestRunner ana sistem kurulumu

### Gelecek Görevler:
- [ ] Diğer controller'larda BannerManager kullanımı
- [ ] Redis/Memcached cache desteği
- [ ] Banner admin panel geliştirmeleri
- [ ] Performance monitoring

## TEST ORTAMI KULLANIMI
### Dosya Konumları:
- **Geçici dosyalar**: Tests/Temp/
- **Banner testleri**: Tests/Banners/
- **Database testleri**: Tests/Database/

### Test Komutları:
```bash
# Ana test sistemi
php Tests/TestRunner.php all        # Tüm testleri çalıştır
php Tests/TestRunner.php banner     # Banner testleri  
php Tests/TestRunner.php database   # Veritabanı testleri
php Tests/TestRunner.php list       # Mevcut testleri listele

# Bireysel testler
php Tests/Banners/BannerTester.php
php Tests/Database/DatabaseTester.php

# PHP syntax check
php -l dosya_adi.php

# Database backup
mysqldump -u root -p database_adi > backup.sql
```

### Test Sonuçları (Son çalıştırma):
- ✅ Database bağlantı testi: BAŞARILI
- ✅ Banner tabloları kontrolü: 6 tip, 21 layout, 8 grup, 15 banner
- ✅ Veri tutarlılık kontrolü: BAŞARILI  
- ✅ BannerManager singleton: ÇALIŞIYOR
- ✅ Cache sistemi: ÇALIŞIYOR

## 💻 Geliştirme Ortamı Bilgileri

### Sistem Gereksinimleri
- **İşletim Sistemi**: Windows 11
- **Web Server**: IIS (Internet Information Services)
- **PHP Version**: PHP 8.3.4
- **IDE**: Visual Studio Code
- **Shell**: PowerShell (varsayılan)
- **Database**: MySQL
- **Kullanılan Domainler**: `c:\Users\zdany\PhpstormProjects\{project_name}\App\Config\Domain.php`
- **Project Path**: `c:\Users\zdany\PhpstormProjects\{project_name}`
- **Yerel Site Adresi**: (her zaman 'l.' ibaresiyle başlar) `c:\Users\zdany\PhpstormProjects\{project_name}\App\Config\Domain.php` dosyasında 'l.**' adıyla başlayan domain.


### Geliştirme Yaklaşımı
- **Adım adım geliştirme**: Her banner türü tek tek optimize edilecek
- **Veri tablosu anlayışı**: Her yeni keşfedilen tablo/sütun yapısı bu prompt'a eklenecek
- **Sürekli dokümantasyon**: Kod değişiklikleri mutlaka PROJECT_PROMPT.md'ye yansıtılacak
- **Test odaklı**: Her değişiklik sonrası test edilecek

### Terminal/PowerShell Komut Formatı
```powershell
# Doğru format (PowerShell için)
cd "c:\Users\zdany\PhpstormProjects\{project_name}"; php Tests\Banners\BannerCSSAnalyzer.php

# Yanlış format (Linux/Unix için)
cd "c:\Users\zdany\PhpstormProjects\{project_name}" && php Tests\Banners\BannerCSSAnalyzer.php
```

### VS Code Eklentileri (Kurulum Gerekli)
```json
{
  "css_tools": [
    "bradlc.vscode-tailwindcss",
    "ritwickdey.live-sass",
    "pranaygp.vscode-css-peek",
    "ecmel.vscode-html-css"
  ],
  "minification": [
    "olback.es6-css-minify",
    "matthewadams.minify-css",
    "HookyQR.minify"
  ],
  "php_tools": [
    "bmewburn.vscode-intelephense-client",
    "xdebug.php-debug"
  ]
}
```

### Proje Dizin Yapısı
```
c:\Users\zdany\PhpstormProjects\{project_name}\
├── Public\CSS\Banners\ (CSS dosyaları)
├── Tests\ (Test ve geliştirme dosyaları)
├── App\Core\ (BannerManager, Config vb.)
├── App\Controller\ (Banner Controller'lar)
└── App\Model\ (Banner modelleri)
```

### Geliştirme İş Akışı
1. **CSS Düzenlemesi**: VS Code'da CSS dosyalarını düzenle
2. **Otomatik Minify**: IDE eklentisi ile otomatik minification
3. **Test**: `php Tests\TestRunner.php banner` ile test et
4. **Analiz**: `php Tests\Banners\BannerCSSAnalyzer.php` ile analiz et
5. **Browser Test**: IIS üzerinden canlı test

## NOTLAR
- Banner sisteminde her zaman BannerManager kullan
- Test dosyaları oluştururken Tests/ dizinini kullan
- Veritabanı değişikliklerinde SQL dosyalarını güncelle
- Hata düzeltmelerini dokümante et

---
*Son güncelleme: 15 Haziran 2025*
*Geliştirici notları: Sistem mimarisi anlaşıldı, Config/DB sistemi dokümante edildi*

## 🎯 TEPE BANNER DURUMU - ÖZET

### ✅ Banner Sistemi: TAMAMLANDI (15 Haziran 2025)
- **Sistem Analizi**: Config/DB sistemi dokümante edildi
- **Layout Group Mismatch**: Çözüldü (top-banner → text_and_image)
- **CSS Modernizasyonu**: 4 ana CSS dosyası optimize edildi
- **Dokümantasyon**: `Tests/Banners/banner_prompt.md` + `Tests/Banners/banner_admin_prompt.md`

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
- Tüm yerel domainler `l.` öneki ile başlar (örn: `l.zafer`, `l.hastane`, `l.erhanozel`) 
- Domain bilgisi `App/Config/Domain.php` dosyasında tanımlanır
- Yerel domain bilgisini tespit etmek için: `php Tests/System/GetLocalDomain.php` komutunu kullanabilirsiniz

