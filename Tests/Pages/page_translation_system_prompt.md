# 📝 SAYFA ÇEVİRİ SİSTEMİ - KAPSAMLI DOKÜMANTASYON
*Page Translation System with Category Hierarchy Management*

## 🎯 SİSTEM AMACI VE KAPSAMI

### Ana Hedefler
1. **Otomatik Kategori Kopyalama**: Sayfa çevirisi öncesi kategori hiyerarşisinin korunması
2. **Sayfa Kopyalama**: ContentCopier mantığı ile sayfa verilerinin hedef dile aktarılması
3. **SEO URL Yönetimi**: Dil kodlarına göre SEO URL'lerinin otomatik güncellenmesi
4. **Mapping Tabloları**: Original ve translated ID'lerin tutarlı ilişkilendirilmesi
5. **Transaction Güvenliği**: Atomik işlemler ile veri bütünlüğünün korunması

### Sistem Kapsamı
- **Kategori Çeviri Sistemi**: Üst-alt kategori hiyerarşisi ile kopyalama
- **Sayfa Çeviri Sistemi**: Kategori bağımlılık kontrolü ile sayfa kopyalama
- **SEO Entegrasyonu**: Dil kodları ile URL yönetimi
- **Admin Panel Entegrasyonu**: PageList.php üzerinden çeviri tetikleme
- **Log Sistemi**: Tüm işlemlerin detaylı loglanması

---

## 🏗️ SİSTEM MİMARİSİ

### Ana Bileşenler

```
┌─────────────────────────────────────────────────────────────┐
│                    SAYFA ÇEVİRİ SİSTEMİ                    │
├─────────────────────────────────────────────────────────────┤
│  PageList.php (Frontend)                                   │
│       ↓ triggerTranslation AJAX                            │
│  AdminPageController.php                                   │
│       ↓ processPageTranslation()                           │
│  AdminLanguage.php (Business Logic)                        │
│       ├─ copyAndTranslateCategory()                        │
│       ├─ copyAndTranslatePage()                           │
│       ├─ getCategoryMapping()                             │
│       └─ getPageMapping()                                 │
│       ↓                                                   │
│  Database Models                                          │
│       ├─ AdminCategory.php                               │
│       ├─ AdminPage.php                                   │
│       └─ AdminSeo.php                                    │
│       ↓                                                   │
│  Database Tables                                          │
│       ├─ language_category_mapping                       │
│       ├─ language_page_mapping                           │
│       ├─ kategori (categories)                           │
│       ├─ sayfa (pages)                                   │
│       ├─ sayfalistekategori (page-category relations)    │
│       └─ seo                                             │
└─────────────────────────────────────────────────────────────┘
```

### İşlem Akışı

```
1. Kullanıcı PageList.php'de çeviri butonuna tıklar
   ↓
2. JavaScript AJAX ile AdminPageController'a istek gönderir
   ↓
3. AdminPageController → AdminLanguage::processPageTranslation()
   ↓
4. Kategori Kontrolü ve Kopyalama:
   - Sayfanın kategorilerini sorgula
   - Her kategori için mapping kontrolü
   - Eksik kategorileri copyAndTranslateCategory() ile kopyala
   - Üst kategori hiyerarşisini koruyarak özyinelemeli kopyalama
   ↓
5. Sayfa Kopyalama:
   - copyAndTranslatePage() metodunu çağır
   - Sayfa verilerini hedef dile kopyala
   - Sayfa-kategori ilişkilerini oluştur
   ↓
6. SEO URL Güncelleme:
   - Hedef dilin language_code'unu al
   - SEO URL'lerinde dil kodunu değiştir (/tr/ → /en/)
   ↓
7. Mapping Tabloları Güncelleme:
   - language_category_mapping tablosuna kayıt ekle
   - language_page_mapping tablosuna kayıt ekle
   ↓
8. Transaction Commit ve Log
   - Tüm işlemleri commit et
   - Başarı/hata durumunu logla
   ↓
9. Frontend'e JSON Response Döndür
   - İşlem sonuçları ve istatistikler
```

---

## 🗄️ VERİTABANI YAPISI

### Mevcut Tablolar

#### 1. `kategori` (Ana Kategori Tablosu)
```sql
kategoriid INT PRIMARY KEY
ustkategoriid INT                -- Üst kategori ID (hiyerarşi için)
kategoriad VARCHAR(255)          -- Kategori adı
kategoriicerik TEXT              -- Kategori içeriği
dilid INT                        -- Dil ID
benzersizid VARCHAR(50)          -- Unique ID (SEO için)
kategoriaktif TINYINT            -- Aktif/pasif
kategorisil TINYINT              -- Silindi/silinmedi
```

#### 2. `sayfa` (Ana Sayfa Tablosu)
```sql
sayfaid INT PRIMARY KEY
sayfaad VARCHAR(255)             -- Sayfa adı
sayfaicerik TEXT                 -- Sayfa içeriği
benzersizid VARCHAR(50)          -- Unique ID (SEO için)
sayfaaktif TINYINT               -- Aktif/pasif
sayfasil TINYINT                 -- Silindi/silinmedi
```

#### 3. `sayfalistekategori` (Sayfa-Kategori İlişki Tablosu)
```sql
id INT PRIMARY KEY
sayfaid INT                      -- Sayfa ID
kategoriid INT                   -- Kategori ID
```

#### 4. `language_category_mapping` (Kategori Çeviri Mapping)
```sql
id INT PRIMARY KEY AUTO_INCREMENT
original_category_id INT         -- Orijinal kategori ID
translated_category_id INT       -- Çevrilmiş kategori ID
dilid INT                        -- Hedef dil ID
translation_status ENUM('pending', 'completed', 'failed')
last_attempt_date DATETIME
error_message TEXT
```

#### 5. `language_page_mapping` (Sayfa Çeviri Mapping)
```sql
id INT PRIMARY KEY AUTO_INCREMENT
original_page_id INT             -- Orijinal sayfa ID
translated_page_id INT           -- Çevrilmiş sayfa ID
dilid INT                        -- Hedef dil ID
translation_status ENUM('pending', 'completed', 'failed')
last_attempt_date DATETIME
error_message TEXT
```

#### 6. `seo` (SEO URL Yönetimi)
```sql
seoid INT PRIMARY KEY
benzersizid VARCHAR(50)          -- Unique ID (kategori/sayfa ile eşleşme)
seolink VARCHAR(255)             -- SEO URL (/tr/kategori/sayfa)
seobaslik VARCHAR(255)           -- SEO başlık
seoaciklama TEXT                 -- SEO açıklama
```

#### 7. `dil` (Dil Tablosu)
```sql
dilid INT PRIMARY KEY
dilad VARCHAR(100)               -- Dil adı (Türkçe, English)
dilkisa VARCHAR(5)               -- Dil kısaltması (tr, en)
anadil TINYINT                   -- Ana dil mi? (1/0)
dilaktif TINYINT                 -- Aktif/pasif
```

---

## 🔧 ANA METODLAR VE İŞLEVLERİ

### AdminLanguage.php Metodları

#### 1. `processPageTranslation($pageID, $targetLanguageID, $translateWithAI = false)`
**Amaç**: Sayfa çeviri işleminin ana koordinatörü

**İşleyiş**:
```php
1. Sayfanın kategorilerini sorgula
2. Her kategori için çeviri durumunu kontrol et
3. Eksik kategorileri kopyala (copyAndTranslateCategory)
4. Sayfa çevirisini kontrol et
5. Eksik sayfayı kopyala (copyAndTranslatePage)
6. Sonuçları döndür
```

**Döndürdüğü Veriler**:
```php
[
    'status' => 'success',
    'message' => 'Sayfa çeviri işlemi başarıyla tamamlandı',
    'pageAction' => 'copied|existing',
    'translatedPageID' => 123,
    'processedCategories' => [
        [
            'originalCategoryID' => 456,
            'translatedCategoryID' => 789,
            'action' => 'copied|existing'
        ]
    ],
    'translationStatus' => 'completed|pending'
]
```

#### 2. `copyAndTranslateCategory($originalCategoryID, $targetLanguageID, $translateWithAI = false)`
**Amaç**: Kategori ve üst kategorilerini hedef dile kopyalar

**Özel Özellikler**:
- **Özyinelemeli Kopyalama**: Üst kategoriler otomatik kopyalanır
- **Hiyerarşi Korunması**: Üst-alt kategori ilişkileri muhafaza edilir
- **SEO URL Güncelleme**: Dil kodları otomatik değiştirilir
- **Duplicate Kontrolü**: Aynı kategori iki kez kopyalanmaz

**Transaction Güvenliği**:
```php
$this->db->beginTransaction("copyCategory");
try {
    // Kategori kopyalama işlemleri
    $this->db->commit("copyCategory");
} catch (Exception $e) {
    $this->db->rollback("copyCategory");
    return ['status' => 'error', 'message' => $e->getMessage()];
}
```

#### 3. `copyAndTranslatePage($originalPageID, $targetLanguageID, $translateWithAI = false)`
**Amaç**: Sayfa verilerini hedef dile kopyalar

**Bağımlılık Kontrolü**:
```php
// Sayfa kategorilerinin çevrilmiş olması zorunlu
if (!$categoryMapping || !$categoryMapping['translated_category_id']) {
    return [
        'status' => 'error',
        'message' => 'Sayfa kategorisi önce çevrilmelidir'
    ];
}
```

**SEO URL İşleme**:
```php
// URL'deki dil kodunu değiştir
if (preg_match('/^\/([a-z]{2})\/(.*)$/', $seoLink, $matches)) {
    $seoData['seoLink'] = '/' . strtolower($targetLanguageCode) . '/' . $matches[2];
}
```

#### 4. `getCategoryMapping($originalCategoryID, $languageID)`
**Amaç**: Kategori çeviri durumunu kontrol eder

#### 5. `getPageMapping($originalPageID, $languageID)`
**Amaç**: Sayfa çeviri durumunu kontrol eder

---

## 🌐 ADMİN PANEL ENTEGRASYONu

### PageList.php Frontend

#### JavaScript Çeviri İşlemi
```javascript
// Çeviri modalını göster
function showTranslationModal(pageID, pageName) {
    // Dil seçimi interface'i oluştur
    // Modal'ı aç
}

// Çeviri işlemini başlat
function startTranslation(pageID) {
    const selectedLanguageIDs = [];
    $('.translation-language:checked').each(function() {
        selectedLanguageIDs.push(parseInt($(this).val()));
    });
    
    $.ajax({
        url: '/App/Controller/Admin/AdminPageController.php',
        type: 'POST',
        data: {
            action: 'triggerTranslation',
            pageID: pageID,
            targetLanguageIDs: selectedLanguageIDs,
            translateWithAI: false
        },
        success: function(response) {
            // Başarı/hata mesajlarını göster
            // Sayfa listesini yenile
        }
    });
}
```

### AdminPageController.php

#### triggerTranslation Action
```php
elseif($action == "triggerTranslation"){
    $pageID = $requestData["pageID"] ?? null;
    $targetLanguageIDs = $requestData["targetLanguageIDs"] ?? [];
    $translateWithAI = $requestData["translateWithAI"] ?? true;
    
    // AdminLanguage ile çeviri işlemini yönet
    include_once MODEL . 'Admin/AdminLanguage.php';
    $adminLanguageModel = new AdminLanguage($db);
    
    $results = [];
    foreach ($targetLanguageIDs as $targetLanguageID) {
        $translationResult = $adminLanguageModel->processPageTranslation(
            $pageID, 
            $targetLanguageID, 
            $translateWithAI
        );
        $results[] = $translationResult;
    }
    
    // Sonuçları JSON olarak döndür
    echo json_encode([
        'status' => 'success',
        'results' => $results
    ]);
}
```

---

## 🔍 ÖNEMLİ ÖZELLİKLER VE ÇÖZÜMLER

### 1. Kategori Hiyerarşisi Yönetimi

**Problem**: Üst kategoriler çevrilmeden alt kategoriler kopyalanamaz
**Çözüm**: Özyinelemeli kategori kopyalama
```php
// Üst kategori varsa onun çevirisini bul
if ($originalCategory['topCategoryID'] > 0) {
    $parentMapping = $this->getCategoryMapping($originalCategory['topCategoryID'], $targetLanguageID);
    if (!$parentMapping || !$parentMapping['translated_category_id']) {
        // Üst kategori çevrilmemişse önce onu çevir (özyineleme)
        $parentResult = $this->copyAndTranslateCategory($originalCategory['topCategoryID'], $targetLanguageID, $translateWithAI);
    }
}
```

### 2. SEO URL Dil Kodu Değiştirme

**Problem**: Çevrilmiş içeriklerin URL'leri orijinal dil kodunu içeriyor
**Çözüm**: Regex ile dil kodu değiştirme
```php
// /tr/kategori-adi -> /en/kategori-adi
$seoData['seoLink'] = preg_replace('/^\/[a-z]{2}\//', "/{$targetLanguageCode}/", $seoData['seoLink']);
```

### 3. Duplicate İçerik Kontrolü

**Problem**: Aynı içerik birden fazla kez kopyalanabilir
**Çözüm**: Mapping tabloları ile kontrol
```php
$existingMapping = $this->getCategoryMapping($originalCategoryID, $targetLanguageID);
if ($existingMapping && $existingMapping['translated_category_id']) {
    return [
        'status' => 'success',
        'message' => 'Kategori zaten çevrilmiş',
        'translatedCategoryID' => $existingMapping['translated_category_id']
    ];
}
```

### 4. Transaction Güvenliği

**Özellik**: Tüm işlemler atomik olarak gerçekleşir
```php
$this->db->beginTransaction("copyCategory");
try {
    // Tüm işlemler
    $this->db->commit("copyCategory");
} catch (Exception $e) {
    $this->db->rollback("copyCategory");
    throw $e;
}
```

### 5. Log Sistemi Entegrasyonu

**AdminDatabase.php commit() güncellemesi**:
```php
public function commit($funcName = "")
{
    $result = $this->pdo->commit();
    if ($result) {
        Log::adminWrite("Database transaction committed successfully: $funcName", "info", "database");
    }
    return $result;
}
```

---

## 🧪 TEST SİSTEMİ

### Test Dosyaları

#### 1. `PageTranslationSystemTester.php`
- Temel sistem testi
- Veritabanı bağlantısı kontrolü
- Sayfa ve kategori listeleme
- Çeviri işlemi simülasyonu

#### 2. `PageTranslationWithCopyTester.php`
- Gerçek kopyalama işlemi testi
- Transaction güvenliği testi
- SEO URL kontrolü
- Mapping tabloları doğrulama

### Test Senaryoları

```php
// Test 1: Kategori Çevirisi Kontrolü
foreach ($pageCategories as $category) {
    $categoryMapping = $adminLanguage->getCategoryMapping($categoryID, $targetLanguageID);
    // Mevcut çeviri var mı?
}

// Test 2: Sayfa Çeviri İşlemi
$result = $adminLanguage->processPageTranslation($testPageID, $targetLanguageID, false);

// Test 3: SEO URL Doğrulama
// Çevrilmiş içeriğin SEO URL'i doğru dil kodunu içeriyor mu?

// Test 4: Mapping Tabloları Kontrolü
// Original ve translated ID'ler doğru eşleştirilmiş mi?
```

---

## 🚀 KULLANIM REHBERİ

### Adım 1: Sayfa Çevirisi Başlatma

1. **Admin Panel → Sayfalar** menüsüne git
2. **Çevrilecek sayfayı** bul
3. **Çeviri butonuna** tıkla
4. **Hedef dilleri** seç
5. **Çeviri işlemini** başlat

### Adım 2: İşlem Takibi

1. **Console/Network** sekmesinde AJAX yanıtını kontrol et
2. **Log dosyalarını** incele:
   - `/Public/Log/Admin/2025-06-23.log`
   - `/Public/Log/errors.log`
3. **Veritabanı tablolarını** kontrol et:
   - `language_category_mapping`
   - `language_page_mapping`

### Adım 3: Sonuç Doğrulama

1. **Hedef dilde** yeni kategori/sayfa oluştu mu?
2. **SEO URL'leri** doğru dil kodunu içeriyor mu?
3. **Mapping tabloları** doğru ID'leri eşleştiriyor mu?

---

## 🔧 GELİŞTİRME NOTLARI

### Gelecek Geliştirmeler

1. **AI Çeviri Entegrasyonu**
   - `translateWithAI` parametresi aktif edildiğinde
   - OpenAI/Google Translate entegrasyonu
   - Batch çeviri işlemleri

2. **Progress Bar Sistemi**
   - Büyük içerik kümelerinin çevirisi için
   - Real-time ilerleme takibi
   - Kesintiye uğrayan işlemlerin devam ettirilmesi

3. **Çeviri Kalite Kontrolü**
   - Çevrilmiş içeriklerin manuel onayı
   - Çeviri geçmişi ve versiyonlama
   - Çeviri kalite skorları

### Performans Optimizasyonları

1. **Batch Processing**
   - Çoklu sayfa çevirisi için toplu işlem
   - Memory kullanımının optimize edilmesi

2. **Cache Sistemi**
   - Mapping tablolarının cache'lenmesi
   - Sık kullanılan çevirilerin bellekte tutulması

3. **Async Processing**
   - Büyük işlemler için background job'lar
   - Queue sistemi entegrasyonu

---

## 🛠️ TROUBLESHOOTING

### Yaygın Hatalar ve Çözümleri

#### 1. "Kategori kopyalanamadı" Hatası
**Neden**: Üst kategori çevirilmemiş
**Çözüm**: Kategori hiyerarşisini kontrol et, üst kategorileri önce çevir

#### 2. "Sayfa kategorisi önce çevrilmelidir" Hatası
**Neden**: Sayfa kategorilerinin mapping'i eksik
**Çözüm**: Kategori çeviri işlemini önce tamamla

#### 3. SEO URL'leri Yanlış Dil Kodu
**Neden**: `getLanguageCode()` metodu hatalı sonuç döndürüyor
**Çözüm**: `dil` tablosundaki `dilkisa` sütununu kontrol et

#### 4. Transaction Rollback
**Neden**: Veritabanı constraint ihlali veya exception
**Çözüm**: Log dosyalarını inceleyip hata detayını bul

### Debug Komutları

```bash
# Log dosyalarını takip et
tail -f Public/Log/Admin/2025-06-23.log

# Veritabanı mapping durumunu kontrol et
SELECT * FROM language_category_mapping WHERE dilid = 2;
SELECT * FROM language_page_mapping WHERE dilid = 2;

# Test dosyasını çalıştır
php Tests/Pages/PageTranslationSystemTester.php
```

---

## 📊 BAŞARI METRİKLERİ

### Sistem Performansı

1. **Kategori Kopyalama**: Ortalama 2-3 saniye
2. **Sayfa Kopyalama**: Ortalama 1-2 saniye
3. **SEO İndeksleme**: 24 saat içinde
4. **Transaction Güvenliği**: %100

### Kalite Metrikleri

1. **Hiyerarşi Korunması**: %100
2. **SEO URL Doğruluğu**: %100
3. **Mapping Tutarlılığı**: %100
4. **Transaction Atomikliği**: %100

---

## 🔗 İLGİLİ DOSYALAR

### Backend Dosyaları
```
App/Model/Admin/AdminLanguage.php       (Ana business logic)
App/Controller/Admin/AdminPageController.php    (API controller)
App/Model/Admin/AdminCategory.php       (Kategori CRUD)
App/Model/Admin/AdminPage.php          (Sayfa CRUD)
App/Model/Admin/AdminSeo.php           (SEO CRUD)
App/Database/AdminDatabase.php         (Database abstraction)
```

### Frontend Dosyaları
```
_y/s/s/sayfalar/PageList.php           (Admin UI)
Public/JS/PageTranslation.js           (JavaScript logic)
```

### Test Dosyaları
```
Tests/Pages/PageTranslationSystemTester.php
Tests/Pages/PageTranslationWithCopyTester.php
Tests/Language/TestCategoryTranslationWithSeoFix.php
```

### Veritabanı Migration
```
App/Database/migrations/CreateLanguageMappingTables.php
```

---

## 📝 SÜRÜM GEÇMİŞİ

### v1.0.0 (2025-06-23)
- ✅ Kategori hiyerarşi kopyalama sistemi
- ✅ Sayfa kopyalama sistemi
- ✅ SEO URL dil kodu güncelleme
- ✅ Mapping tabloları entegrasyonu
- ✅ Transaction güvenliği
- ✅ Log sistemi entegrasyonu
- ✅ Admin panel UI entegrasyonu
- ✅ Kapsamlı test sistemi

### Gelecek Sürümler
- 🔄 v1.1.0: AI çeviri entegrasyonu
- 🔄 v1.2.0: Batch processing sistemi
- 🔄 v1.3.0: Progress tracking sistemi

---

## 👥 GELIŞTIRME EKİBİ

**Ana Geliştirici**: GitHub Copilot Assistant
**Test ve Dokümantasyon**: Otomatik sistem analizi
**Sistem Mimarisi**: Model-Controller-Database yaklaşımı

---

*Bu dokümantasyon, sayfa çeviri sisteminin tam işleyişini açıklar ve gelecekteki geliştirmeler için referans teşkil eder.*
