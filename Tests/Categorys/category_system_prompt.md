# CATEGORY SYSTEM PROMPT
*MCP (Model Context Protocol) için Category sistemi rehberi*

## 📋 SİSTEM ÖZETİ
- **Controller**: CategoryController
- **Model**: Category
- **Views**: 3 dosya
- **Database Tables**: 1 tablo
- **CSS Files**: 0 dosya
- **JS Files**: 0 dosya

## 🔗 BAĞIMLILIKLAR
- SchemaGenerator
- Category
- BannerController
- Product
- Page
- SeoModel

## 📊 VERİTABANI ANALIZI
### 📋 Tablo: `language_category_mapping`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `id` | int | NO | PRI | NULL |
| `original_category_id` | int | YES | MUL | NULL |
| `translated_category_id` | int | YES | MUL | NULL |
| `dilid` | int | YES | MUL | NULL |

## 📁 DOSYA YERLEŞİMLERİ
```
App/Controller/CategoryController.php
App/Model/Category.php
/App/View/category/BlogPageBox.php
/App/View/category/Category.php
/_y/index.php
```

---
*Bu prompt dosyası otomatik olarak AdvancedProjectAnalyzer tarafından oluşturulmuştur.*
*Son güncelleme: 2025-06-15 19:07:20*
