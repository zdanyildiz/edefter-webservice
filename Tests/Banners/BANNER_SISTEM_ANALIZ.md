# BANNER SİSTEMİ ANALİZ VE PROMPT

## 📋 BANNER SİSTEMİ YAPISINI ANLAMAK

### VERİTABANI YAPISI

#### 1. banner_types (Banner Tipleri)
```sql
- id (AUTO_INCREMENT PRIMARY KEY)
- type_name (VARCHAR) - "Tepe Banner", "Slider", "Orta Banner", "Alt Banner"
- description (TEXT)
```

#### 2. banner_layouts (Banner Layout'ları)
```sql
- id (AUTO_INCREMENT PRIMARY KEY)  
- layout_name (VARCHAR) - Layout ismi
- layout_group (VARCHAR) - "text_and_image", "IconFeatureCard", "HoverCardBanner"
- layout_view (VARCHAR) - "single", "multi"
- type_id (INT) - Hangi banner tipine ait
- max_banners (INT) - Maksimum banner sayısı
- description (TEXT)
```

#### 3. banner_groups (Banner Grupları) ⭐ DİNAMİK ID
```sql
- id (AUTO_INCREMENT PRIMARY KEY) ⚠️ DİNAMİK: 1, 2, 28, 58, 99 vs.
- group_name (VARCHAR) - Grup ismi
- group_title (VARCHAR) - Başlık
- group_desc (VARCHAR) - Açıklama
- layout_id (INT) - Hangi layout kullanacak
- group_kind (VARCHAR) - "text_and_image" vs.
- group_view (VARCHAR) - "single", "multi"
- columns (INT) - Kaç sütun
- content_alignment (ENUM) - "horizontal", "vertical"
- style_class (VARCHAR) - "IconFeatureCard", "HoverCardBanner"
- background_color (VARCHAR)
- group_title_color (VARCHAR)
- group_desc_color (VARCHAR)
- group_full_size (TINYINT) - Tam genişlik mi?
- banner_full_size (TINYINT) - Banner tam genişlik mi?
- custom_css (TEXT)
- order_num (INT) - Sıralama
- visibility_start/end (DATETIME) - Görünürlük tarihleri
- banner_duration (INT)
```

#### 4. banners (Tekil Banner Verileri)
```sql
- id (AUTO_INCREMENT PRIMARY KEY)
- group_id (INT) - Hangi gruba ait
- style_id (INT) - Hangi stil
- title (VARCHAR)
- content (TEXT)
- image (VARCHAR)
- link (VARCHAR)
- active (TINYINT)
```

#### 5. banner_display_rules (Görünüm Kuralları)
```sql
- id (AUTO_INCREMENT PRIMARY KEY)
- group_id (INT) - Hangi grup
- type_id (INT) - Hangi tip (1=Slider, 2=Tepe, 3=Orta, 4=Alt)
- page_id (INT) - Hangi sayfada gösterilecek
- category_id (INT) - Hangi kategoride gösterilecek
- language_code (VARCHAR) - Hangi dilde
```

### BANNER İŞLEYİŞİ

#### 1. VERİ AKIŞI
```
SiteConfig::getBannerInfo()
├── BannerDisplayRulesModel::getDisplayRulesByLanguageCode()
├── BannerGroupModel::getGroupById() ⭐ DİNAMİK ID
├── BannerLayoutModel::getLayoutById()
├── BannerModel::getBannersByGroupId()
└── BannerStyleModel::getStyleById()
```

#### 2. HTML ÇIKTISI
```html
<!-- DİNAMİK CLASS'LAR -->
<div class='banner-group-{GROUP_ID} banner-type-{TYPE_NAME} {STYLE_CLASS}' 
     data-type='{TYPE_ID}' 
     data-layout-group='{LAYOUT_GROUP}' 
     data-layout='{LAYOUT_VIEW}'>
     
    <div class='banner-container {CONTAINER_CLASS}'>
        <div class='banner-item banner-{BANNER_ID}'>
            <!-- Banner içeriği -->
        </div>
    </div>
</div>
```

#### 3. CSS OLUŞTURMA
```php
// BannerController::generateBannerCSS()
foreach ($banners as $banner) {
    $bannerGroupId = $banner['group_info']['id']; // ⚠️ DİNAMİK!
    
    // CSS kuralları:
    $css .= ".banner-group-{$bannerGroupId} { ... }";
    $css .= ".banner-group-{$bannerGroupId} .banner-container { ... }";
    $css .= ".banner-{$bannerItemId} { ... }";
}
```

### ⚠️ HATALI YAKLAŞIM (ESKİ)
```css
/* YANLIŞ: Statik ID'ler */
.banner-group-2 { ... }
.banner-group-3 { ... }
```

### ✅ DOĞRU YAKLAŞIM (YENİ)
```css
/* DOĞRU: Dinamik selectorlar */
[class^="banner-group-"] { ... }
.banner-container { ... }
[data-type="2"] { ... } /* Tepe banner */
[data-type="3"] { ... } /* Orta banner */
```

## 🎯 CSS YAPISINI DÜZELTME PLANI

### 1. STATİK CSS'LER (Public/CSS/Banners/)
```css
/* Genel banner kuralları */
[class^="banner-group-"] {
    width: 100%;
    margin: 0 auto;
}

.banner-container {
    max-width: var(--content-max-width);
    margin: 0 auto;
    text-align: center;
}

/* Tip bazlı kurallar */
[data-type="2"] .banner-container { /* Tepe */ }
[data-type="1"] .banner-container { /* Slider */ }
[data-type="3"] .banner-container { /* Orta */ }
[data-type="4"] .banner-container { /* Alt */ }

/* Layout bazlı kurallar */
[data-layout-group="IconFeatureCard"] { 
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}
```

### 2. DİNAMİK CSS'LER (BannerController)
```php
// SADECE değişken veriler için CSS üret
foreach ($banners as $banner) {
    $groupId = $banner['group_info']['id'];
    
    // Arka plan rengi (değişken)
    if ($bgColor) {
        $css .= ".banner-group-{$groupId} { background-color: {$bgColor}; }";
    }
    
    // Özel boyutlar (değişken)
    if ($customWidth) {
        $css .= ".banner-group-{$groupId} { max-width: {$customWidth}; }";
    }
    
    // Banner özel stilleri (değişken)
    foreach ($banner['banners'] as $item) {
        $css .= ".banner-{$item['id']} { 
            background-color: {$item['style']['bg_color']};
            height: {$item['style']['height']}px;
        }";
    }
}
```

## 📝 GÜNCELLENMESİ GEREKEN DOSYALAR

### 1. CSS Dosyaları
- [ ] `Public/CSS/Banners/tepe-banner.css` - Statik selectorlar
- [ ] `Public/CSS/Banners/orta-banner.css` - Statik selectorlar  
- [ ] `Public/CSS/Banners/slider.css` - Statik selectorlar
- [ ] `Public/CSS/Banners/alt-banner.css` - Statik selectorlar

### 2. PHP Dosyaları
- [ ] `App/Controller/BannerController.php` - Dinamik CSS sadece değişkenler için
- [ ] CSS yükleme mantığında düzeltmeler

### 3. Test Dosyaları
- [ ] `Tests/Banners/` altında güncel testler
- [ ] Dinamik ID'leri test eden senaryolar

## 🔧 ÖRNEK DÜZELTME KODU

### CSS Düzeltmesi
```css
/* Önceki hatalı kod */
.banner-group-2 { margin: 0 auto; } /* ❌ */

/* Yeni doğru kod */
[class^="banner-group-"] { margin: 0 auto; } /* ✅ */
[data-type="2"] { /* Tepe banner özel kuralları */ } /* ✅ */
```

### PHP Düzeltmesi  
```php
// Önceki hatalı kod
$css .= ".banner-group-2 { display: flex; }"; // ❌

// Yeni doğru kod
$css .= ".banner-group-{$groupId} { background: {$bgColor}; }"; // ✅
// Flex kuralları statik CSS'de olmalı
```

## 🚀 PROMPT GÜNCELLEMELERİ

Banner ile ilgili tüm geliştirmelerde:

1. **Banner grup ID'lerinin dinamik** olduğunu unutma
2. **Statik kuralları** CSS dosyalarında tut
3. **Dinamik kuralları** sadece değişken veriler için üret
4. **!important** kullanma
5. **CSS selectorları** dinamik yap: `[class^="banner-group-"]`, `[data-type="X"]`
6. **Test edilecek senaryolar**: Farklı grup ID'leri (1, 2, 28, 58, 99)

Bu prompt ile banner sisteminin tüm detaylarını anladık! 🎯
