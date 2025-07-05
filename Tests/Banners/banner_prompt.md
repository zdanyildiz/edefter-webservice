# BANNER SİSTEMİ DETAYLI PROMPT - yeni.globalpozitif.com.tr
*Bu dosya, Banner sistemi için tüm teknik detayları ve işleyiş bilgilerini içerir*

## 📋 BANNER SİSTEMİ GENEL BAKIŞ

### Banner Sistem Mimarisi
```
BannerManager (Core/BannerManager.php)
    ↓
BannerController (Controller/BannerController.php)
    ↓
Banner Models (Model/Banner.php)
    ↓
Database Tables (banners, banner_groups, banner_layouts)
    ↓
View Templates & CSS (View/*, Public/CSS/Banners/*)
```

## 🗄️ VERİTABANI YAPISI

### Ana Tablolar ve İlişkiler
```sql
-- BANNERS (Ana banner verileri)
banners: 
- id (primary key)
- group_id (banner_groups.id ile ilişki)
- style_id (banner_layouts.id ile ilişki) 
- title (başlık)
- content (içerik)
- image (görsel yolu)
- link (bağlantı URL)
- is_active (aktiflik durumu)
- created_at, updated_at

-- BANNER_GROUPS (Banner grupları)
banner_groups:
- id (primary key)
- name (grup adı)
- style_class (CSS sınıfı)
- group_full_size (tam genişlik: 0/1)
- banner_full_size (banner tam genişlik: 0/1)
- layout_group (layout grubu)
- type_id (banner_layouts.type_id ile ilişki)
- is_active (aktiflik durumu)
- order_index (sıralama)

-- BANNER_LAYOUTS (Layout şablonları)
banner_layouts:
- id (primary key)
- layout_group (layout grup adı: top-banner, carousel, vb.)
- layout_view (görünüm tipi)
- type_id (banner tipi)
- layout_name (layout adı)
- columns (sütun sayısı)
- max_banners (maksimum banner sayısı)
```

### Tablo İlişkileri
```
banners.group_id → banner_groups.id
banners.style_id → banner_layouts.id
banner_groups.type_id → banner_layouts.type_id
```

## 🎯 BANNER TİPLERİ VE LAYOUT GRUPLARI

### Banner Tipleri (type_id)
1. **Slider** - Klasik kaydırmalı banner
2. **Static** - Statik banner
3. **Top Banner** - Tepe banner
4. **Middle Banner** - Orta banner
5. **Bottom Banner** - Alt banner
6. **Carousel** - Carousel banner

### Layout Grupları (layout_group)
- `top-banner` - Tepe banner layout
- `fullwidth` - Tam genişlik layout
- `carousel` - Carousel layout
- `ImageRightBanner` - Görsel sağda layout
- `ImageLeftBanner` - Görsel solda layout
- `HoverCardBanner` - Hover kart layout
- `BgImageCenterText` - Arkaplan görsel + orta metin

## 🔧 SİSTEM İŞLEYİŞİ

### 1. Banner Çekme Süreci
```php
// 1. BannerManager singleton'dan instance al
$bannerManager = BannerManager::getInstance();

// 2. Belirli tip bannerları çek
$topBannerResult = $bannerManager->getTopBanners($pageId, $categoryId);

// 3. HTML çıktısını al
$topBannersHtml = $topBannerResult['html'];
```

### 2. BannerController İşleyişi
```php
// Layout grup dönüştürme
$layoutGroup = $this->convertLayoutGroup($banner['layout_info']['layout_group']);

// Ortalama sınıfları ekleme
$containerClass = '';
if ($banner['group_info']['group_full_size'] == 0) {
    $containerClass .= ' banner-centered';
}
if ($banner['group_info']['banner_full_size'] == 0) {
    $containerClass .= ' banner-content-centered';
}

// HTML render
$html = "<div class='banner-group-{$bannerId} banner-type-{$bannerTypeName} {$styleClass}' data-type='{$bannerType}' data-layout-group='{$layoutGroup}' data-layout='{$layoutView}'>
    <div class='banner-container{$containerClass}'>
        {$bannerContent}
    </div>
</div>";
```

### 3. CSS Sistem Yapısı
```
Public/CSS/Banners/
├── tepe-banner.css (✅ Modern)
├── slider.css (✅ Modern)
├── orta-banner.css (✅ Modern)
├── alt-banner.css (✅ Modern)
├── [diğer banner CSS'leri]
└── *.min.css (minified versiyonlar)
```

## 🏗️ SİSTEM BİLEŞENLERİ VE MİMARİ

### Banner Sistem Bileşenleri
- **BannerManager**: Singleton cache sistemi (`App/Core/BannerManager.php`)
- **BannerController**: HTML render sistemi (`App/Controller/BannerController.php`)  
- **Banner Models**: Veri modelleri (`App/Model/Banner.php`)
- **Admin Models**: Admin CRUD modelleri (`App/Model/Admin/AdminBannerModel.php`)
- **CSS System**: Modern responsive CSS (`Public/CSS/Banners/`)
- **Admin CSS**: Admin panel CSS (`_y/s/s/banners/CSS/`)
- **Database**: 6 ana tablo (banner_types, banner_layouts, banner_groups, banners, banner_display_rules, banner_styles)

### Banner Admin Sistemi Özeti
- **Global Admin Auth**: `_y/s/global.php` - Güvenlik ve yetkilendirme sistemi
- **AddBanner.php**: Ana admin sayfası - Banner CRUD işlemleri
- **Model Context Protocol**: AdminBannerModel.php'de 6 farklı model sınıfı
- **Dinamik CSS/JS**: Banner tipine göre otomatik stil dosyası yükleme
- **Real-time Preview**: JavaScript ile anlık önizleme sistemi
- **Dropzone Integration**: Görsel yükleme sistemi
- **Color Picker**: Bootstrap colorpicker entegrasyonu

### Banner Optimizasyon Sistemi
- ✅ BannerManager singleton sınıfı oluşturuldu
- ✅ Cache sistemi eklendi
- ✅ Duplicate render sorunu çözüldü
- ✅ display_rules hatası düzeltildi
- ✅ SQL dosyaları canlı veritabanıyla güncellendi

### Banner Akışı
1. **SiteConfig**: Banner verilerini veritabanından çeker
2. **BannerManager**: Verileri cache'ler ve render eder
3. **BannerController**: Gerçek render işlemini yapar
4. **Templates**: HTML çıktısını gösterir

### Banner Türleri (Canlı DB'den)
- Klasik Slayt (fullwidth)
- Carousel Slayt 
- Arkaplan Resim ve Yazı Ortalı
- Resim Sağda/Solda Yazı
- Hover Card Banner
- Icon/Fade Özellik Kartları
- Popup/Header/Bottom Banner

## 🎯 TEPE BANNER SİSTEMİ

### Tepe Banner Durumu
- ✅ Layout group çevirici eklendi (top-banner → text_and_image)
- ✅ CSS ortalama sistemi çalışıyor
- ✅ HTML wrapper mevcut (`<section id="topBanner">` - header.php)
- ✅ Full-width/ortalama kontrolü aktif

### Banner Ortalama Mantığı
```php
// BannerController'da otomatik sınıf ekleme:
if ($banner['group_info']['group_full_size'] == 0) {
    $containerClass .= ' banner-centered';      // Max-width + margin: auto
}
if ($banner['group_info']['banner_full_size'] == 0) {
    $containerClass .= ' banner-content-centered';  // İçerik ortalama
}
```

### Tepe Banner Sorun Çözümleri (15 Haziran 2025)

#### 🔧 Ana Sorun: Layout Group Mismatch 
**Problem:** Database'de `layout_group = 'top-banner'` ama BannerController `text_and_image`, `text`, `image` değerlerini bekliyordu.

**Çözüm:** BannerController'a layout group çevirici eklendi:

```php
// App/Controller/BannerController.php'ye eklenen metod
private function convertLayoutGroup($layoutGroup) 
{
    $layoutGroupMap = [
        'top-banner' => 'text_and_image',
        'fullwidth' => 'text_and_image',
        'carousel' => 'text_and_image',
        // ... diğer çeviriler
    ];
    return $layoutGroupMap[$layoutGroup] ?? 'text_and_image';
}

// renderBannerHTML metodunda kullanımı:
$layoutGroup = $this->convertLayoutGroup($banner['layout_info']['layout_group']);
```

## 🎨 BANNER CSS SİSTEMİ (GÜNCEL - 15 Haziran 2025)

### Tamamlanan İyileştirmeler

#### ✅ Modernize Edilmiş Banner Türleri
1. **tepe-banner.css** - Tamamen iyileştirildi
2. **slider.css** - Sıfırdan yeniden yazıldı (9.5KB->6.4KB, %33 sıkıştırma)
3. **orta-banner.css** - Minimal'den modern card design'a (9.9KB->6.6KB, %33.2 sıkıştırma)
4. **alt-banner.css** - Glassmorphism ile yeniden yazıldı (10.8KB->7.6KB, %29.9 sıkıştırma)

#### 🔧 Teknik Özellikler
- **CSS Custom Properties**: Dinamik değişken sistemi
- **Backdrop Filter**: Modern glassmorphism efektleri
- **Clamp()**: Fluid typography ve responsive spacing
- **Grid & Flexbox**: Modern layout sistemi
- **Accessibility**: Reduced motion, high contrast desteği
- **Performance**: Ortalama %32 dosya boyutu azaltımı

#### 📱 Responsive Design
- Mobile-first approach ile tam responsive
- Breakpoints: 480px, 768px, 1024px, 1200px
- Fluid typography ve flexible spacing
- Cross-browser compatibility

#### 🔗 Dinamik CSS Entegrasyonu
- PHP ile runtime değişken sistemi
- BannerController ile tam entegrasyon
- Database-driven styling desteği
- Real-time color/animation customization

### CSS Dosya Yapısı
```
Public/CSS/Banners/
├── tepe-banner.css (✅ Modern)
├── slider.css (✅ Modern)  
├── orta-banner.css (✅ Modern)
├── alt-banner.css (✅ Modern)
├── slider-dynamic.css.php (✅ PHP Template)
├── [diğer banner türleri] (📋 Kısmi iyileştirmeler)
└── *.min.css (✅ Güncel minified dosyalar)
```

### Test Dosyaları
```
Tests/Banners/
├── BannerCSSAnalyzer.php (✅ CSS analiz aracı)
├── BANNER_CSS_IYILESTIRME_RAPORU.md (✅ Detaylı rapor)
└── [Banner test dosyaları]
```

### Kalan İyileştirmeler
- ImageLeftBanner, ImageRightBanner: Dinamik CSS eklenmesi
- Box, Fullwidth: Tam modernizasyon
- Visual regression testing
- Real-device testing

## 🚨 HATA ÇÖZÜMLER VE SORUN GİDERME

### Çözülen Hatalar
1. **"Class BannerManager not found"**
   - Çözüm: Manuel include kontrolü + Config.php düzeltmesi

2. **"display_rules must be of type array, null given"**
   - Çözüm: matchesPageAndCategory metoduyla doğrudan page_id/category_id kullanımı

3. **Layout Group Mismatch**
   - Çözüm: convertLayoutGroup() metodu ile mapping sistemi

### Yaygın Sorunlar
- CSS dosyalarının yüklenmemesi → PHP include path kontrolü
- Banner görüntülenmemesi → Active durumu ve display rules kontrolü
- Responsive problemleri → Breakpoint tanımları kontrolü

---
*Son güncelleme: 15 Haziran 2025*
*Geliştirici: Banner sistem analizi ve optimizasyonu tamamlandı*
