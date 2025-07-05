# PROJE TEST VE GELİŞTİRME SİSTEMİ

Bu dizin, `yeni.globalpozitif.com.tr` projesi için test dosyaları ve geliştirme araçlarını içerir.

## 📁 DİZİN YAPISI

```
Tests/
├── README.md                    # Bu dosya
├── PROJECT_PROMPT.md           # Ana proje prompt/notlar
├── development_notes.json      # Fonksiyon referansları
├── TestRunner.php              # Ana test çalıştırıcı
├── Banners/                    # Banner sistem testleri
│   └── BannerTester.php
├── Database/                   # Veritabanı testleri  
│   └── DatabaseTester.php
└── Temp/                       # Geçici test dosyaları
```

## 🚀 KULLANIM

### Test Sistemini Çalıştırma:

```bash
# Tüm testleri çalıştır
php Tests/TestRunner.php all

# Sadece banner testlerini çalıştır  
php Tests/TestRunner.php banner

# Sadece veritabanı testlerini çalıştır
php Tests/TestRunner.php database

# Kullanılabilir testleri listele
php Tests/TestRunner.php list
```

### Bireysel Test Dosyaları:

```bash
# Banner sistem testi
php Tests/Banners/BannerTester.php

# Veritabanı bağlantı testi
php Tests/Database/DatabaseTester.php
```

## 📋 TEST KATEGORİLERİ

### 🎯 Banner Testleri
- BannerManager singleton testi
- Cache sistem testi  
- Banner veri çekme testi
- Render işlem testi

### 🔌 Veritabanı Testleri
- Bağlantı testi
- Tablo varlık kontrolü
- Veri tutarlılık kontrolü
- Banner tablolarına özel kontroller

## 🛠️ GELİŞTİRME KURALLARI

### Geçici Dosya Oluşturma:
1. **Tests/Temp/** dizinini kullan
2. Dosya adına tarih/saat ekle: `test_2025-06-15_14-30.php`
3. İşin bitince temizle

### Yeni Test Ekleme:
1. İlgili alt dizinde test sınıfı oluştur
2. `runAllTests()` metodunu implement et  
3. `TestRunner.php`'ye ekle
4. Bu README'yi güncelle

### Veritabanı Erişimi:
```php
// Test dosyalarında şu bağlantı bilgilerini kullan:
$host = 'localhost';
$username = 'root';
$password = 'Global2019*';
$database = 'yeni.globalpozitif.com.tr';
```

## 📚 REFERANSLAR

- **PROJECT_PROMPT.md**: Proje genel bilgileri ve notlar
- **development_notes.json**: Fonksiyon referansları ve kodlar
- **Banner Dokümantasyon**: `_y/s/s/banners/BANNER_*.md`

## 🔄 GELİŞTİRME AKIŞI

1. **Analiz**: Sorunu/ihtiyacı tanımla
2. **Test**: Tests/Temp/ altında denemeler yap
3. **Uygulama**: Ana proje dosyalarına entegre et
4. **Doğrulama**: Test sistemini çalıştır
5. **Dokümantasyon**: Değişiklikleri kaydet

## ⚠️ DİKKAT

- Test dosyaları production'a deploy edilmemeli
- Geçici dosyaları commit etme
- Veritabanı değişikliklerinde SQL dosyalarını güncelle
- Her önemli değişiklikten sonra testleri çalıştır

---
*Son güncelleme: 15 Haziran 2025*
*GitHub Copilot tarafından oluşturuldu*
