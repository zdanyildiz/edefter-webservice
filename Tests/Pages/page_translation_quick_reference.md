# 🚀 SAYFA ÇEVİRİ SİSTEMİ - QUICK REFERENCE
*Page Translation System - Developer Quick Reference Card*

## 🎯 SİSTEM ÖZET

**Amaç**: Sayfa ve kategorileri otomatik olarak farklı dillere kopyalama  
**Özellik**: Hiyerarşi korunması + SEO URL güncelleme + Transaction güvenliği  
**Entegrasyon**: PageList.php → AdminPageController.php → AdminLanguage.php  

---

## ⚡ HIZLI BAŞLANGIÇ

### 1. Çeviri İşlemi Başlatma (JavaScript)
```javascript
// PageList.php içinde
$('.translatePageButton').click(function() {
    const pageID = $(this).data('pageid');
    showTranslationModal(pageID, pageName);
});

// Çeviri isteği gönderme
$.ajax({
    url: '/App/Controller/Admin/AdminPageController.php',
    type: 'POST',
    data: {
        action: 'triggerTranslation',
        pageID: pageID,
        targetLanguageIDs: [2, 3], // İngilizce, Fransızca
        translateWithAI: false
    }
});
```

### 2. Backend İşlem (PHP)
```php
// AdminPageController.php
elseif($action == "triggerTranslation"){
    $adminLanguageModel = new AdminLanguage($db);
    $result = $adminLanguageModel->processPageTranslation(
        $pageID, $targetLanguageID, $translateWithAI
    );
    echo json_encode($result);
}
```

---

## 🔧 ANA METODLAR

### AdminLanguage.php

#### `processPageTranslation($pageID, $targetLanguageID, $translateWithAI = false)`
**Kullanım**: Ana çeviri koordinatörü
```php
$result = $adminLanguage->processPageTranslation(123, 2, false);
// Döndürdüğü: ['status' => 'success', 'pageAction' => 'copied', ...]
```

#### `copyAndTranslateCategory($originalCategoryID, $targetLanguageID, $translateWithAI = false)`
**Kullanım**: Kategori kopyalama (özyinelemeli)
```php
$result = $adminLanguage->copyAndTranslateCategory(456, 2, false);
// Üst kategoriler otomatik kopyalanır
```

#### `copyAndTranslatePage($originalPageID, $targetLanguageID, $translateWithAI = false)`
**Kullanım**: Sayfa kopyalama
```php
$result = $adminLanguage->copyAndTranslatePage(123, 2, false);
// Kategori bağımlılık kontrolü dahil
```

#### `getCategoryMapping($originalCategoryID, $languageID)`
**Kullanım**: Kategori çeviri durumu kontrolü
```php
$mapping = $adminLanguage->getCategoryMapping(456, 2);
if ($mapping && $mapping['translated_category_id']) {
    // Kategori zaten çevrilmiş
}
```

#### `getPageMapping($originalPageID, $languageID)`
**Kullanım**: Sayfa çeviri durumu kontrolü
```php
$mapping = $adminLanguage->getPageMapping(123, 2);
```

---

## 🗄️ VERİTABANI TABLOLARI

### language_category_mapping
```sql
id, original_category_id, translated_category_id, dilid, 
translation_status, last_attempt_date, error_message
```

### language_page_mapping
```sql
id, original_page_id, translated_page_id, dilid, 
translation_status, last_attempt_date, error_message
```

### Mapping Kontrolü
```sql
-- Kategori çeviri durumu
SELECT * FROM language_category_mapping 
WHERE original_category_id = 456 AND dilid = 2;

-- Sayfa çeviri durumu  
SELECT * FROM language_page_mapping 
WHERE original_page_id = 123 AND dilid = 2;
```

---

## 🚨 YAYGIN HATALAR VE ÇÖZÜMLERİ

### 1. "Kategori kopyalanamadı" 
**Neden**: Üst kategori çevirilmemiş  
**Çözüm**: Sistem otomatik çözer (özyinelemeli kopyalama)

### 2. "Sayfa kategorisi önce çevrilmelidir"
**Neden**: Sayfa kategorilerinin mapping'i eksik  
**Çözüm**: `processPageTranslation()` kullan (otomatik kategori kontrolü)

### 3. AdminDatabase constructor hatası
**Neden**: Parametre eksikliği  
**Çözüm**: 
```php
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();
$db = new AdminDatabase($dbInfo['serverName'], $dbInfo['username'], $dbInfo['password'], $dbInfo['database']);
```

---

## 🔍 DEBUG KOMUTLARI

### Log Dosyalarını İzleme
```bash
# Admin log
tail -f Public/Log/Admin/2025-06-23.log

# Sistem hataları
tail -f Public/Log/errors.log

# Genel log
tail -f Public/Log/2025-06-23.log
```

### Veritabanı Kontrolü
```sql
-- Son çeviri işlemleri
SELECT * FROM language_page_mapping ORDER BY last_attempt_date DESC LIMIT 10;

-- Kategori hiyerarşisi kontrolü
SELECT k1.kategoriad as parent, k2.kategoriad as child 
FROM kategori k1 
RIGHT JOIN kategori k2 ON k1.kategoriid = k2.ustkategoriid 
WHERE k2.dilid = 2;

-- SEO URL kontrolü
SELECT s.seolink, k.kategoriad FROM seo s 
JOIN kategori k ON s.benzersizid = k.benzersizid 
WHERE s.seolink LIKE '/en/%';
```

---

## 🧪 TEST DOSYALARI

### Temel Test
```bash
php Tests/Pages/PageTranslationSystemTester.php
```

### Kopyalama Testi
```bash
php Tests/Pages/PageTranslationWithCopyTester.php?confirm_test=yes
```

### Kategori SEO Testi
```bash
php Tests/Language/TestCategoryTranslationWithSeoFix.php
```

---

## 📊 PERFORMANS İPUÇLARI

### Memory Optimizasyonu
```php
// Büyük sayfa setleri için
unset($largeArrays);
gc_collect_cycles();
```

### Transaction Optimizasyonu
```php
// Toplu işlemler için
$db->beginTransaction("bulkTranslation");
foreach ($pages as $page) {
    // İşlemler
}
$db->commit("bulkTranslation");
```

### Cache Stratejisi
```php
// Mapping bilgilerini cache'le
$mappingCache = [];
if (!isset($mappingCache[$categoryID])) {
    $mappingCache[$categoryID] = $this->getCategoryMapping($categoryID, $langID);
}
```

---

## 🔗 ÖNEMLİ DOSYA YOLLARI

```
# Backend Logic
App/Model/Admin/AdminLanguage.php

# Controller
App/Controller/Admin/AdminPageController.php

# Frontend
_y/s/s/sayfalar/PageList.php

# Test Files
Tests/Pages/PageTranslationSystemTester.php
Tests/Pages/PageTranslationWithCopyTester.php

# Database
App/Database/AdminDatabase.php

# Utility
Tests/System/GetLocalDatabaseInfo.php
Tests/System/GetTableInfo.php
```

---

## 🎯 EN ÇOK KULLANILAN CODE SNIPPETS

### Dil Kodu Alma
```php
$targetLanguageCode = $adminLanguage->getLanguageCode($targetLanguageID);
```

### SEO URL Güncelleme
```php
$seoData['seoLink'] = preg_replace('/^\/[a-z]{2}\//', "/{$targetLanguageCode}/", $originalLink);
```

### Transaction Pattern
```php
$this->db->beginTransaction("operationName");
try {
    // Operations
    $this->db->commit("operationName");
    return ['status' => 'success'];
} catch (Exception $e) {
    $this->db->rollback("operationName");
    return ['status' => 'error', 'message' => $e->getMessage()];
}
```

### Duplicate Check Pattern
```php
$existing = $this->getPageMapping($originalID, $targetLanguageID);
if ($existing && $existing['translated_page_id']) {
    return ['status' => 'success', 'message' => 'Already translated'];
}
```

---

## 📈 BAŞARI KRİTERLERİ

- ✅ **Hiyerarşi korunmalı**: Üst-alt kategori ilişkileri
- ✅ **SEO URL güncel**: Doğru dil kodu (/tr/ → /en/)
- ✅ **Mapping tutarlı**: Original ↔ Translated ID eşleşmesi
- ✅ **Transaction güvenli**: Rollback/commit doğru çalışmalı
- ✅ **Performance**: <5 saniye yanıt süresi
- ✅ **Memory**: <50 MB memory kullanımı

---

## 🚀 GELECEKTEKİ GELİŞTİRMELER

### Öncelikli
- **AI Çeviri**: `translateWithAI=true` aktif et
- **Progress Bar**: Büyük işlemler için UI feedback
- **Batch Processing**: Çoklu sayfa çevirisi

### Uzun Vadeli  
- **Queue System**: Background job processing
- **Version Control**: Çeviri versiyonlama
- **Quality Control**: Manuel onay sistemi

---

*Quick Reference v1.0 | 23 Haziran 2025*
