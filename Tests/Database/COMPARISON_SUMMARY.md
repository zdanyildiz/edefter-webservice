# Veritabanı Karşılaştırma Raporu Özeti

**Tarih:** 05 Temmuz 2025, 18:57  
**DB1:** e-defter.globalpozitif.com.tr  
**DB2:** johwears.globalpozitif.com.tr  

## 📊 İstatistikler

| Özellik | DB1 | DB2 | Ortak |
|---------|-----|-----|-------|
| **Tablo Sayısı** | 110 | 106 | 104 |
| **Sütun Farkı** | - | - | 30 |

## ⚠️ Tablo Farklılıkları

### Sadece DB1'de Olan Tablolar (6 Tablo)
1. **chatbot_packages** - Chatbot paket yönetimi
2. **chatbot_requests_log** - Chatbot istek logları
3. **user_chatbot_usage** - Kullanıcı chatbot kullanımı
4. **user_consent** - Kullanıcı onay bilgileri
5. **user_sessions** - Kullanıcı oturum yönetimi
6. **user_sessions_log** - Kullanıcı oturum logları

### Sadece DB2'de Olan Tablolar (2 Tablo)
1. **language_copy_jobs** - Dil kopyalama işleri
2. **site_config_versions** - Site konfigürasyon versiyonları

## 🔍 Kritik Sütun Farklılıkları

### 1. Banner Sistemi Farklılıkları
**banner_groups** tablosunda büyük farklılık (12 sütun farkı):
- DB2'de gelişmiş banner grup özellikleri mevcut
- Stil, renk ve görünüm ayarları DB2'de daha kapsamlı

**banner_styles** tablosunda 10 sütun farkı:
- DB2'de gelişmiş stil özellikleri
- Renk alan boyutları farklı (varchar(20) → varchar(25))

### 2. Çoklu Dil Sistemi Farklılıkları
**language_category_mapping** ve **language_page_mapping** tablolarında:
- DB2'de çeviri durumu takibi mevcut
- Hata yönetimi sistemi DB2'de gelişmiş

## 🎯 Öneriler

### Acil Müdahale Gereken
1. **Banner Sistemi Senkronizasyonu**
   - DB1'e banner yönetim sütunları eklenmeli
   - Stil sistemleri uyumlaştırılmalı

2. **Dil Sistemi Güncelleme**
   - DB1'e çeviri durumu takip sistemi eklenmeli

### İsteğe Bağlı
1. **Chatbot Sistemi**
   - DB2'ye chatbot tabloları eklenebilir
   - Kullanıcı yönetimi sistemi uyumlaştırılabilir

2. **Site Konfigürasyonu**
   - DB1'e versiyon takip sistemi eklenebilir

## 📁 Dosya Konumları

- **JSON Raporu:** `Tests/Logs/database_comparison/comparison_2025-07-05_18-57-30.json`
- **HTML Raporu:** `Tests/Logs/database_comparison/comparison_2025-07-05_18-57-30.html`
- **TXT Raporu:** `Tests/Logs/database_comparison/comparison_2025-07-05_18-57-30.txt`

## 🛠️ Tekrar Çalıştırma

```powershell
# Karşılaştırmayı tekrar çalıştır
php Tests\Database\compare_databases.php

# Sistem testini çalıştır
php Tests\Database\test_database_comparer.php
```

---
*Bu rapor otomatik olarak DatabaseComparer sistemi tarafından oluşturulmuştur.*
