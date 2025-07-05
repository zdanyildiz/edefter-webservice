# MEMBER/AUTHENTICATION SYSTEM - Model Context Protocol Prompt
*Bu dosya, GitHub Copilot için üye/kimlik doğrulama sistem anlayış ve geliştirme notlarını içerir*

## 🎯 AMAÇ VE KAPSAM

Bu prompt, yeni.globalpozitif.com.tr projesindeki **Member/Authentication** sisteminin tam analizi ve geliştirme rehberidir. Model Context Protocol (MCP) metodolojisi kullanılarak, sistemin her bileşeni detaylandırılmış ve geliştirici deneyimi optimize edilmiştir.

### Sistem Sorumluluklarıyük 
- ✅ Üye kayıt ve giriş işlemleri
- ✅ Şifre yönetimi ve sıfırlama
- ✅ Üye profil yönetimi ve güncelleme
- ✅ Adres yönetimi (çoklu adres desteği)
- ✅ Admin panel üye CRUD işlemleri
- ✅ Oturum yönetimi ve güvenlik
- ✅ Veri şifreleme ve güvenlik
- ✅ E-posta bildirimleri
- ✅ Üye türü ve yetkilendirme sistemi

## 🏗️ SİSTEM MİMARİSİ

### Core Bileşenler
```
Member System/
├── Frontend Models/
│   ├── Member.php                 # Ana üye işlemleri
│   └── Session.php               # Oturum yönetimi
│
├── Admin Models/
│   └── AdminMember.php           # Admin üye CRUD
│
├── Controllers/
│   └── MemberController.php      # Frontend üye işlemleri
│
├── Admin Interface/
│   ├── AddMember.php             # Üye ekleme/düzenleme
│   ├── MemberList.php            # Üye listesi
│   └── uyesepet.php             # Üye sepeti
│
├── Frontend Assets/
│   ├── memberUpdateFormValidate.js
│   └── memberAddressFormValidate.js
│
└── Database Tables/
    ├── uye                       # Ana üye tablosu
    └── uyeadres                  # Üye adres tablosu
```

## 🗃️ VERİTABANI YAPISI

### Tablo: `uye` (Üyeler)
```sql
-- Temel Yapı (AdminMember.php'den alınan şema bilgisi)
uyeid INT AI PK                    -- Üye ID
benzersizid CHAR(20)               -- Benzersiz ID
uyeolusturmatarih DATETIME         -- Oluşturma tarihi
uyeguncellemetarih DATETIME        -- Güncelleme tarihi
uyetip TINYINT(1)                  -- Üye tipi (0: normal, 1: bayi, vb.)
uyetcno CHAR(11)                   -- TC Kimlik No (şifreli)
memberTitle VARCHAR(100)           -- Unvan (Mr/Mrs/Dr vb.)
uyead VARCHAR(100)                 -- Ad (şifreli)
uyesoyad VARCHAR(100)              -- Soyad (şifreli)
uyeeposta VARCHAR(100)             -- E-posta (şifreli)
uyesifre VARCHAR(100)              -- Şifre (şifreli)
uyetelefon VARCHAR(50)             -- Telefon (şifreli)
uyeaciklama VARCHAR(255)           -- Açıklama
uyefaturaad VARCHAR(255)           -- Fatura adı (şifreli)
uyefaturavergidairesi VARCHAR(255) -- Vergi dairesi (şifreli)
uyefaturavergino VARCHAR(12)       -- Vergi no (şifreli)
uyeaktif TINYINT(1)                -- Aktif/pasif durumu
uyesil TINYINT(1)                  -- Silme durumu
```

### Tablo: `uyeadres` (Üye Adresleri)
```sql
-- Adres Yapısı (AdminMember.php'den alınan şema bilgisi)
adresid INT AI PK                  -- Adres ID
uyeid INT                          -- Üye referansı
adresbaslik VARCHAR(50)            -- Adres başlığı (Ev, İş vb.)
adrestcno CHAR(11)                 -- İletişim TC No (şifreli)
adresad VARCHAR(50)                -- İletişim adı (şifreli)
adressoyad VARCHAR(50)             -- İletişim soyadı (şifreli)
adresulke VARCHAR(50)              -- Ülke
adressehir VARCHAR(50)             -- Şehir
adresilce VARCHAR(50)              -- İlçe
adressemt VARCHAR(50)              -- Semt
adresmahalle VARCHAR(50)           -- Mahalle
postakod VARCHAR(10)               -- Posta kodu
adresacik VARCHAR(255)             -- Açık adres (şifreli)
adrestelefon VARCHAR(10)           -- Telefon (şifreli)
adresulkekod VARCHAR(3)            -- Ülke kodu
adressil TINYINT                   -- Silme durumu
```

## 🔧 ANA MODEL SINIFLARI

### 1. Member.php (Frontend Model)

#### Temel İşlevler
```php
// Kullanıcı girişi
login($email, $password)           // Giriş doğrulama
getMemberInfo($memberId, $uniqueId) // Üye bilgi alma

// Kayıt işlemleri
register($memberInfo)              // Normal kayıt
registerWithCheckout($memberPostData) // Sepet ile kayıt

// Profil yönetimi
update($memberInfo)                // Profil güncelleme
updatePassword($memberInfo)        // Şifre değiştirme
updatePasswordByEmail($email, $newPassword) // E-posta ile şifre sıfırlama

// Adres yönetimi
addAddress($addressInfo)           // Yeni adres ekleme
updateAddress($addressInfo)        // Adres güncelleme
deleteAddress($addressId)          // Adres silme
getAddresses($memberId)            // Üye adreslerini listeleme
```

#### Güvenlik Özellikleri
- **Transaction Support**: Kritik işlemlerde rollback desteği
- **Error Handling**: Detaylı hata mesajları ve durum kodları
- **Data Validation**: Giriş verilerinin kontrolü

### 2. AdminMember.php (Admin Model)

#### Admin İşlevler
```php
// CRUD İşlemleri
addMember($memberInfo)             // Yeni üye ekleme
updateMember($memberInfo)          // Üye güncelleme
deleteMember($memberID)            // Üye silme (soft delete)
getMemberInfo($memberID)           // Üye detay bilgisi

// Listeleme ve Arama
getMembersPaginated($page, $perPage) // Sayfalı üye listesi
getTotalMembersCount()             // Toplam üye sayısı
searchMembers($searchTerm)         // Üye arama

// Adres Yönetimi
getAddresses($memberID)            // Üye adreslerini getirme
getAddressByID($memberID, $addressID) // Spesifik adres bilgisi
addAddress($addressInfo)           // Yeni adres ekleme
updateAddress($addressInfo)        // Adres güncelleme
deleteAddress($addressID)          // Adres silme
```

#### Veri Şifreleme Sistemi
```php
// Şifreleme/Şifre Çözme (Helper sınıfı kullanılarak)
$memberName = $helper->decrypt($memberName, $config->key);
$memberEmail = $helper->decrypt($memberEmail, $config->key);
$memberPhone = $helper->decrypt($memberPhone, $config->key);
$memberIdentityNo = $helper->decrypt($memberIdentityNo, $config->key);

// Şifrelenen alanlar:
// - uyetcno (TC Kimlik No)
// - uyead (Ad)
// - uyesoyad (Soyad)
// - uyeeposta (E-posta)
// - uyesifre (Şifre)
// - uyetelefon (Telefon)
// - uyefaturaad (Fatura adı)
// - uyefaturavergidairesi (Vergi dairesi)
// - uyefaturavergino (Vergi no)
```

## 🎮 CONTROLLER İŞLEYİŞİ

### MemberController.php Akışı

#### 1. Sistem Hazırlığı
```php
// Casper (core sistem) kontrolü
$casper = $session->getCasper();
$config = $casper->getConfig();
$helper = $config->Helper;

// Router ve dil sistemi
$routerResult = $session->getSession("routerResult");
$languageID = $routerResult["languageID"] ?? 1;
$languageCode = $helper->toLowerCase($routerResult["languageCode"]) ?? "tr";

// Site konfigürasyon
$siteConfig = $casper->getSiteConfig();
$visitor = $casper->getVisitor();
```

#### 2. Ziyaretçi Kontrolü
```php
// Ziyaretçi bilgisi kontrol
if(!isset($visitor['visitorUniqID'])){
    header('Location: /?visitorID-None');
    exit();
}
```

#### 3. İşlem Yönlendirme
- **POST işlemleri**: Form verilerini işleme
- **GET işlemleri**: Sayfa görüntüleme ve bilgi çekme
- **AJAX istekleri**: Asenkron veri işleme

## 🖥️ ADMIN PANEL SİSTEMİ

### Admin Güvenlik Sistemi
```php
// global.php üzerinden kimlik doğrulama
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");

// Otomatik değişkenler:
// @var AdminDatabase $db
// @var Config $config  
// @var Helper $helper
```

### AddMember.php (Üye Ekleme/Düzenleme)

#### İşlevsel Özellikler
- **Dual Mode**: Yeni üye ekleme ve mevcut üye düzenleme
- **Address Management**: Çoklu adres ekleme/düzenleme
- **Data Encryption**: Tüm hassas verilerin şifrelenmesi
- **Location Integration**: AdminLocation ile ülke/şehir/ilçe seçimi
- **Form Validation**: Client-side ve server-side doğrulama

#### URL Parametreleri
```php
// Üye düzenleme
/AddMember.php?memberID=123

// Adres düzenleme  
/AddMember.php?memberID=123&addressID=456
```

### MemberList.php (Üye Listesi)

#### Özellikler
- **Pagination**: Sayfa başına 20 üye
- **Data Decryption**: Listede şifreli verilerin çözülmesi
- **Status Display**: Aktif/pasif durumu gösterimi
- **Action Buttons**: Düzenleme ve silme butonları
- **Responsive Design**: Material Admin teması

## 🔒 GÜVENLİK VE ŞİFRELEME

### Şifreleme Sistemi
```php
// Config.php'den encryption key
$key = "Aom1eP50h72aEcCNSb4@(*722SXOHfmE";

// Helper sınıfı kullanımı
$encryptedData = $helper->encrypt($plainText, $config->key);
$decryptedData = $helper->decrypt($encryptedData, $config->key);
```

### Şifrelenen Veriler
- **Kişisel Bilgiler**: Ad, soyad, TC kimlik no
- **İletişim Bilgileri**: E-posta, telefon
- **Fatura Bilgileri**: Fatura adı, vergi bilgileri
- **Şifreler**: Üye giriş şifreleri
- **Adres Bilgileri**: Açık adres, iletişim bilgileri

### Güvenlik Önlemleri
1. **SQL Injection**: Prepared statements kullanımı
2. **Data Validation**: Input kontrolü ve sanitization
3. **Session Management**: Güvenli oturum yönetimi
4. **Access Control**: Admin panel yetkilendirme sistemi
5. **HTTPS**: Şifreli veri iletimi (production)

## 🎨 FRONTEND ENTEGRASYONU

### JavaScript Validasyonları
```javascript
// memberUpdateFormValidate.js
// - Form validasyonu
// - Real-time hata gösterimi
// - AJAX form gönderimi

// memberAddressFormValidate.js  
// - Adres form validasyonu
// - Şehir/ilçe otomatik yükleme
// - Posta kodu kontrolü
```

### CSS Entegrasyonu
- **Bootstrap**: Responsive grid sistem
- **Material Design**: Admin panel teması
- **Custom Styles**: Özel form stilleri

## 📋 KULLANIM REHBERİ

### Yeni Üye Kaydı (Frontend)
```php
// Controller'da işlem
$memberModel = new Member($db);
$result = $memberModel->register($memberInfo);

// Gerekli veriler:
// - benzersizid (unique ID)
// - uyead, uyesoyad (ad, soyad)
// - uyeeposta (e-posta) 
// - uyesifre (şifre)
// - uyetelefon (telefon)
// - uyeaktif (1: aktif)
```

### Üye Girişi
```php
$result = $memberModel->login($email, $password);
if (!empty($result)) {
    // Giriş başarılı - session'a kaydet
    $session->addSession('member', $result[0]);
}
```

### Admin Panel Üye Yönetimi
```php
// Üye listesi
$members = $memberModel->getMembersPaginated($pageNumber, $perPage);

// Üye detayı
$member = $memberModel->getMemberInfo($memberID);

// Şifreli verileri çöz
$memberName = $helper->decrypt($member['memberName'], $config->key);
```

## 🔧 TROUBLESHOOTING

### Yaygın Sorunlar ve Çözümler

#### 1. Şifre Çözme Hatası
```php
// Problem: Encryption key hatası
// Çözüm: Config.php'de key kontrolü
$key = $config->key; // "Aom1eP50h72aEcCNSb4@(*722SXOHfmE"
```

#### 2. Session Problemi
```php
// Problem: Üye bilgisi session'da yok
// Çözüm: Casper sistemini kontrol et
$visitor = $casper->getVisitor();
if(!isset($visitor['visitorUniqID'])){
    // Visitor ID eksik - redirect
}
```

#### 3. Database Connection
```php
// Problem: AdminDatabase bağlantısı yok
// Çözüm: global.php'nin doğru yüklendiğinden emin ol
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
```

## 🚀 GELİŞTİRME REHBERİ

### Yeni Özellik Ekleme

#### 1. Model Katmanında
```php
// Member.php'ye yeni metod ekleme
public function newFeature($param) {
    $sql = "SELECT * FROM uye WHERE condition = :param";
    return $this->db->select($sql, ['param' => $param]);
}
```

#### 2. Admin Model'de
```php
// AdminMember.php'ye admin işlevi ekleme
public function adminNewFeature($memberID, $data) {
    // Transaction ile güvenli işlem
    $this->db->beginTransaction();
    try {
        $result = $this->db->update($sql, $params);
        $this->db->commit();
        return $result;
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
```

#### 3. Controller'da
```php
// MemberController.php'de yeni endpoint
if ($action === 'new-feature') {
    $memberModel = new Member($db);
    $result = $memberModel->newFeature($param);
    echo $json->encode($result);
}
```

### Test Stratejisi
```php
// Test dosyası oluşturma
// Tests/Members/MemberSystemTester.php

class MemberSystemTester {
    public function testLogin() {
        // Login fonksiyonunu test et
    }
    
    public function testRegistration() {
        // Kayıt fonksiyonunu test et
    }
    
    public function testEncryption() {
        // Şifreleme sistemini test et
    }
}
```

## 📈 PERFORMANS OPTİMİZASYONU

### Cache Stratejileri
- **Member Info Cache**: Sık kullanılan üye bilgilerini cache'le
- **Address Cache**: Adres listelerini geçici olarak sakla
- **Location Cache**: Ülke/şehir verilerini cache'le

### Database Optimizasyonu
- **Index Usage**: Email ve benzersizid alanlarında index
- **Query Optimization**: Join'leri minimize et
- **Connection Pooling**: Database bağlantı havuzu

## 🔄 DİĞER SİSTEMLERLE ENTEGRASYON

### Bağlantılı Sistemler
- **Order System**: Üye siparişleri (`uyesiparis` tablosu)
- **Cart System**: Sepet işlemleri
- **Payment System**: Ödeme bilgileri
- **Banner System**: Üye tabanlı banner gösterimi
- **Location System**: Adres yönetimi için AdminLocation

### Veri Akışı
```
Member Registration → Cart → Checkout → Order → Payment
                 ↓
         Address Management → Location System
                 ↓  
            Admin Panel → Member CRUD
```

## 📚 KAYNAKLAR VE REFERANSLAR

### İlgili Dosyalar
- `App/Model/Member.php` - Ana üye modeli
- `App/Model/Admin/AdminMember.php` - Admin üye modeli
- `App/Controller/MemberController.php` - Üye controller
- `_y/s/s/uyeler/AddMember.php` - Admin üye ekleme/düzenleme
- `_y/s/s/uyeler/MemberList.php` - Admin üye listesi
- `Public/JS/member*.js` - Frontend validasyon scripts

### Bağımlı Sistemler
- **Core System**: Config, Helper, Database, Session
- **Security System**: Encryption/Decryption (Helper)
- **Location System**: AdminLocation model
- **Language System**: Multi-language support

---

*Bu dokümantasyon, Model Context Protocol metodolojisi ile oluşturulmuş olup, GitHub Copilot'un proje hakkındaki anlayışını optimize etmek için tasarlanmıştır.*
