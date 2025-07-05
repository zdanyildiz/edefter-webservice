# 🎨 Gelişmiş Tema Özelleştirme Sistemi

Bu proje için geliştirilmiş olan kapsamlı tema özelleştirme sistemi, admin kullanıcılarının sitenin görünümünü kolayca özelleştirmelerine olanak tanır.

## 📁 Dosya Yapısı

```
/_y/s/s/tasarim/
├── Theme.php              # Ana tema düzenleyici sayfası
├── CSSGenerator.php       # Dinamik CSS üretici
├── theme-editor.js        # Gelişmiş JavaScript fonksiyonları
└── Design.php             # Mevcut tasarım sayfası (korundu)

/App/Controller/Admin/
└── AdminDesignController.php  # Backend API controller

/Public/CSS/
├── index-theme.css        # Tema CSS değişkenleri
├── index-{languageID}.css # Dile özel dinamik CSS
└── index-preview-{languageID}.css  # Önizleme CSS

/Public/Json/CSS/
├── index-{languageID}.json    # Dile özel tema ayarları
└── index-preview-{languageID}.json  # Önizleme ayarları
```

## 🎯 Özellikler

### 🔧 Sekmeli Düzenleme Arayüzü
- **Genel Görünüm**: Temel renkler, metin renkleri, arka plan renkleri
- **Header & Menü**: Logo, menü stilleri, header ayarları  
- **Ürün Kutuları**: Ana sayfa ve kategori ürün kutuları
- **Banner & İçerik**: Banner düzenlemeleri
- **Form & Butonlar**: Form elemanları, buton stilleri
- **Responsive**: Mobil ve tablet uyumluluğu
- **Footer & Diğer**: Footer, alert, tooltip stilleri
- **Hazır Temalar**: Önceden tanımlanmış tema şablonları

### 🎨 Gelişmiş Renk Sistemi
- Otomatik renk varyasyonu üretimi (açık/koyu tonlar)
- Canlı renk önizlemesi
- Hex, RGB, HSL renk desteği
- Renk uyumluluk kontrolü

### 👁️ Canlı Önizleme
- Gerçek zamanlı değişiklik önizlemesi
- Ayrı pencerede site önizlemesi
- Responsive önizleme (Desktop, Tablet, Mobil)
- Değişiklikleri kaydetmeden test etme

### 💾 Tema Yönetimi
- Tema kaydetme/yükleme
- JSON formatında tema dışa/içe aktarma
- Hazır tema şablonları
- Otomatik kaydetme (5 dakikada bir)
- Geri alma/ileri alma (Ctrl+Z/Ctrl+Shift+Z)

### 🚀 Performans Optimizasyonları
- CSS değişken sistemi (CSS Custom Properties)
- Dinamik CSS üretimi
- Önbellek desteği
- Optimized CSS çıktısı

## 🛠️ Kurulum ve Kullanım

### 1. Dosya Yerleştirme
Oluşturulan dosyaları ilgili dizinlere yerleştirin:

```bash
# Tema dosyalarını kopyalayın
cp Theme.php /_y/s/s/tasarim/
cp CSSGenerator.php /_y/s/s/tasarim/
cp theme-editor.js /_y/s/s/tasarim/

# Controller güncellemesi yapıldı
# AdminDesignController.php güncellendi
```

### 2. Menü Entegrasyonu
Admin menüsüne (`/_y/s/b/menu.php`) yeni menü öğesi eklenmiştir:
```html
<li>
    <a href="/_y/s/s/tasarim/Theme.php" id="themephp">
        <span class="title">🎨 Gelişmiş Tema Düzenleyici</span>
    </a>
</li>
```

### 3. Dizin Yapısı Kontrolü
Gerekli dizinlerin var olduğundan emin olun:
```bash
mkdir -p /Public/CSS/
mkdir -p /Public/Json/CSS/
```

## 📖 API Endpoints

### POST /App/Controller/Admin/AdminDesignController.php

#### Tema Kaydetme
```javascript
{
    action: 'saveDesign',
    languageID: 1,
    'primary-color': '#4285f4',
    // ... diğer tema değişkenleri
}
```

#### Önizleme Kaydetme
```javascript
{
    action: 'savePreviewDesign',
    languageID: 1,
    // ... tema değişkenleri
}
```

#### Tema Sıfırlama
```javascript
{
    action: 'resetDesign',
    languageID: 1
}
```

#### Mevcut Tema Getirme
```javascript
{
    action: 'getCurrentTheme',
    languageID: 1
}
```

#### Hazır Temalar Getirme
```javascript
{
    action: 'getPredefinedThemes'
}
```

## 🎨 CSS Değişken Sistemi

Sistem CSS Custom Properties kullanarak tema yönetimi yapar:

```css
:root {
    /* Temel Renkler */
    --primary-color: #4285f4;
    --primary-light-color: #74a9ff;
    --primary-dark-color: #0d5bdd;
    
    /* Metin Renkleri */
    --text-primary-color: #202124;
    --text-secondary-color: #5f6368;
    
    /* Arka Plan Renkleri */
    --body-bg-color: #f8f9fa;
    --content-bg-color: #ffffff;
    
    /* ... diğer değişkenler */
}
```

## 🔧 Özelleştirme

### Yeni Tema Değişkeni Ekleme

1. **CSS'e ekleyin** (`index-theme.css`):
```css
:root {
    --new-variable: #value;
}
```

2. **Form'a ekleyin** (`Theme.php`):
```html
<input type="color" name="new-variable" class="form-control color-picker" 
       value="<?=$customCSS['new-variable'] ?? '#default'?>">
```

3. **Controller'a ekleyin** (`AdminDesignController.php`):
```php
$newVariable = $requestData["new-variable"] ?? null;
```

### Yeni Hazır Tema Ekleme

`getPredefinedThemes()` fonksiyonuna yeni tema ekleyin:

```php
'new-theme' => [
    'name' => 'Yeni Tema',
    'description' => 'Tema açıklaması',
    'primary-color' => '#color',
    // ... diğer renkler
]
```

## 🧪 Test Etme

### 1. Fonksiyonel Test
```bash
# Admin paneline giriş yapın
# /_y/s/s/tasarim/Theme.php sayfasını açın
# Renk değişikliklerini test edin
# Kaydetme/önizleme fonksiyonlarını test edin
```

### 2. Responsive Test
```bash
# Farklı ekran boyutlarında test edin
# Mobil uyumluluğu kontrol edin
# Tablet görünümünü test edin
```

### 3. Browser Uyumluluğu
- Chrome (önerilen)
- Firefox
- Safari
- Edge

## 🔒 Güvenlik

### Dosya İzinleri
```bash
chmod 755 /_y/s/s/tasarim/
chmod 644 /_y/s/s/tasarim/*.php
chmod 644 /_y/s/s/tasarim/*.js
chmod 755 /Public/CSS/
chmod 755 /Public/Json/CSS/
```

### Veri Doğrulama
- Tüm girişler sunucu tarafında doğrulanır
- XSS koruması aktif
- Dosya yükleme güvenliği sağlanmış

## 🚨 Sorun Giderme

### CSS Dosyası Oluşturulmuyor
```bash
# Dizin izinlerini kontrol edin
ls -la /Public/CSS/

# PHP hata loglarını kontrol edin
tail -f /Public/Log/errors.log
```

### JavaScript Hataları
```javascript
// Browser console'u açın (F12)
// Hata mesajlarını kontrol edin
// jQuery ve Bootstrap yüklenmiş mi kontrol edin
```

### Renk Seçici Çalışmıyor
```html
<!-- Bootstrap Colorpicker CSS/JS'in yüklendiğinden emin olun -->
<link href="bootstrap-colorpicker.css" rel="stylesheet">
<script src="bootstrap-colorpicker.min.js"></script>
```

## 📋 Yapılacaklar (Roadmap)

### Phase 1 - Tamamlandı ✅
- [x] Temel tema düzenleyici arayüzü
- [x] Renk yönetimi sistemi
- [x] Canlı önizleme
- [x] Tema kaydetme/yükleme
- [x] Hazır tema şablonları

### Phase 2 - Geliştirilecek 🔄
- [ ] Header & Menü sekmesi tamamlanması
- [ ] Ürün kutuları sekmesi detaylandırılması
- [ ] Banner yönetimi entegrasyonu
- [ ] Form & Buton gelişmiş ayarları
- [ ] Responsive ayarlar paneli

### Phase 3 - Gelecek 📋
- [ ] Tipografi yönetimi
- [ ] Animasyon ayarları
- [ ] Dark mode otomatik geçiş
- [ ] A/B test desteği
- [ ] Tema pazarı (marketplace)

## 🤝 Katkıda Bulunma

Bu sistem sürekli geliştirilmektedir. Yeni özellik önerileri ve hata raporları için:

1. Sorunları `/Public/Log/` altındaki log dosyalarında takip edin
2. Yeni özellik önerileri için dokümantasyon güncelleyin
3. Test sonuçlarını kaydedin

## 📞 Destek

- **Log Dosyaları**: `/Public/Log/errors.log`
- **Admin Log**: `/Public/Log/Admin/YYYY-MM-DD.log`
- **Site Log**: `/Public/Log/YYYY-MM-DD.log`

---

**💡 İpucu**: Bu sistem mevcut `Design.php` sayfasını etkilemez. Her iki sistem de paralel olarak çalışabilir.

**⚠️ Önemli**: Tema değişiklikleri yapmadan önce mevcut ayarlarınızı dışa aktararak yedek alın.
