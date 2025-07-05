# BANNER SYSTEM PROMPT
*MCP (Model Context Protocol) için Banner sistemi rehberi*

## 📋 SİSTEM ÖZETİ
- **Controller**: BannerController
- **Model**: Banner
- **Views**: 1 dosya
- **Database Tables**: 6 tablo
- **CSS Files**: 30 dosya
- **JS Files**: 4 dosya

## 🔧 CONTROLLER METHODS
- `__construct()`
- `getBannersByType()`
- `renderBannersByType()`
- `renderSliderBanners()`
- `renderTopBanners()`
- `renderBottomBanners()`
- `renderPopupBanners()`
- `renderAllBannerTypes()`
- `getBannersByPage()`
- `generateBannerCSS()`
- `loadBannerTypeCSS()`
- `renderBannerHTML()`
- `getPopupJS()`
- `renderBanners()`
- `getCssContent()`
- `getJsContent()`

## 🔗 BAĞIMLILIKLAR
- Helper
- Error

## 📊 VERİTABANI ANALIZI
### 📋 Tablo: `banner_display_rules`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `id` | int | NO | PRI | NULL |
| `group_id` | int | NO | MUL | NULL |
| `type_id` | int | NO |  | NULL |
| `page_id` | int | YES |  | NULL |
| `category_id` | int | YES |  | NULL |
| `language_code` | varchar(10) | YES |  | NULL |
| `created_at` | timestamp | YES |  | CURRENT_TIMESTAMP |
| `updated_at` | timestamp | YES |  | CURRENT_TIMESTAMP |

### 📋 Tablo: `banner_groups`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `id` | int | NO | PRI | NULL |
| `group_name` | varchar(100) | NO |  | NULL |
| `group_title` | varchar(100) | YES |  | NULL |
| `group_desc` | varchar(255) | YES |  | NULL |
| `layout_id` | int | YES | MUL | NULL |
| `group_kind` | varchar(100) | YES |  | NULL |
| `group_view` | varchar(20) | YES |  | NULL |
| `columns` | int | NO |  | NULL |
| `content_alignment` | enum('horizontal','vertical') | YES |  | horizontal |
| `style_class` | varchar(50) | YES |  | NULL |
| `background_color` | varchar(50) | YES |  | NULL |
| `group_title_color` | varchar(50) | YES |  | NULL |
| `group_desc_color` | varchar(50) | YES |  | NULL |
| `group_full_size` | tinyint | YES |  | 1 |
| `custom_css` | text | YES |  | NULL |
| `order_num` | int | YES |  | NULL |
| `visibility_start` | datetime | YES |  | NULL |
| `visibility_end` | datetime | YES |  | NULL |
| `banner_duration` | int | YES |  | NULL |
| `banner_full_size` | tinyint | YES |  | 0 |
| `created_at` | timestamp | YES |  | CURRENT_TIMESTAMP |
| `updated_at` | timestamp | YES |  | CURRENT_TIMESTAMP |

### 📋 Tablo: `banner_layouts`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `id` | int | NO | PRI | NULL |
| `layout_group` | varchar(50) | NO |  | text_and_image |
| `layout_view` | varchar(20) | NO |  | single |
| `type_id` | int | NO | MUL | NULL |
| `layout_name` | varchar(100) | NO |  | NULL |
| `description` | text | YES |  | NULL |
| `columns` | int | YES |  | 1 |
| `max_banners` | int | YES |  | 1 |
| `created_at` | timestamp | YES |  | CURRENT_TIMESTAMP |
| `updated_at` | timestamp | YES |  | CURRENT_TIMESTAMP |

### 📋 Tablo: `banner_styles`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `id` | int | NO | PRI | NULL |
| `banner_height_size` | int | NO |  | 0 |
| `background_color` | varchar(25) | YES |  | NULL |
| `content_box_bg_color` | varchar(25) | YES |  | NULL |
| `title_color` | varchar(25) | YES |  | NULL |
| `title_size` | int | YES |  | NULL |
| `content_color` | varchar(25) | YES |  | NULL |
| `content_size` | int | YES |  | NULL |
| `show_button` | tinyint(1) | NO |  | 1 |
| `button_title` | varchar(50) | YES |  | NULL |
| `button_location` | int | YES |  | NULL |
| `button_background` | varchar(25) | YES |  | NULL |
| `button_color` | varchar(25) | YES |  | NULL |
| `button_hover_background` | varchar(25) | YES |  | NULL |
| `button_hover_color` | varchar(25) | YES |  | NULL |
| `button_size` | int | YES |  | NULL |
| `created_at` | timestamp | NO |  | CURRENT_TIMESTAMP |
| `updated_at` | timestamp | NO |  | CURRENT_TIMESTAMP |

### 📋 Tablo: `banner_types`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `id` | int | NO | PRI | NULL |
| `type_name` | varchar(50) | NO |  | NULL |
| `description` | text | YES |  | NULL |

### 📋 Tablo: `banners`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `id` | int | NO | PRI | NULL |
| `group_id` | int | NO | MUL | NULL |
| `style_id` | int | YES | MUL | NULL |
| `title` | varchar(255) | YES |  | NULL |
| `content` | text | YES |  | NULL |
| `image` | varchar(255) | YES |  | NULL |
| `link` | varchar(255) | YES |  | NULL |
| `active` | tinyint(1) | YES |  | 1 |
| `created_at` | timestamp | YES |  | CURRENT_TIMESTAMP |
| `updated_at` | timestamp | YES |  | CURRENT_TIMESTAMP |

## 🎨 CSS DOSYALARI
- BgImageCenterText.css
- BgImageCenterText.min.css
- FadeFeatureCard.css
- FadeFeatureCard.min.css
- HoverCardBanner.css
- HoverCardBanner.min.css
- IconFeatureCard.css
- IconFeatureCard.min.css
- ImageLeftBanner.css
- ImageLeftBanner.min.css
- ImageRightBanner.css
- ImageRightBanner.min.css
- ImageTextOverlayBottom.css
- ImageTextOverlayBottom.min.css
- ProfileCard.css
- ProfileCard.min.css
- alt-banner.css
- alt-banner.min.css
- box.css
- box.min.css
- fullwidth.css
- fullwidth.min.css
- karsilama-banner-(popup).css
- karsilama-banner-(popup).min.css
- orta-banner.css
- orta-banner.min.css
- slider.css
- slider.min.css
- tepe-banner.css
- tepe-banner.min.css

## ⚡ JAVASCRIPT DOSYALARI
- Carousel-Claude.js
- Carousel-Claude.min.js
- Carousel.js
- Carousel.min.js

## 📁 DOSYA YERLEŞİMLERİ
```
App/Controller/BannerController.php
App/Model/Banner.php
/_y/index.php
```

---
*Bu prompt dosyası otomatik olarak AdvancedProjectAnalyzer tarafından oluşturulmuştur.*
*Son güncelleme: 2025-06-15 19:05:57*
