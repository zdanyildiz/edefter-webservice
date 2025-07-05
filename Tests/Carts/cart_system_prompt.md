# CART SYSTEM PROMPT
*MCP (Model Context Protocol) için Cart sistemi rehberi*

## 📋 SİSTEM ÖZETİ
- **Controller**: CartController
- **Model**: Cart
- **Views**: 1 dosya
- **Database Tables**: 1 tablo
- **CSS Files**: 0 dosya
- **JS Files**: 0 dosya

## 🔗 BAĞIMLILIKLAR
- MODEL
- Cart
- Currency
- Page

## 📊 VERİTABANI ANALIZI
### 📋 Tablo: `cart_conversion_code`
| Sütun | Tip | Null | Key | Default |
|-------|-----|------|-----|----------|
| `cart_conversion_code_id` | int | NO | PRI | NULL |
| `language_id` | int | YES |  | NULL |
| `cart_conversion_code_name` | varchar(50) | YES |  | NULL |
| `cart_conversion_code` | varchar(500) | YES |  | NULL |
| `cart_conversion_code_deleted` | tinyint(1) | YES |  | NULL |
| `unique_id` | varchar(20) | YES |  | NULL |

## 📁 DOSYA YERLEŞİMLERİ
```
App/Controller/CartController.php
App/Model/Cart.php
/_y/index.php
```

---
*Bu prompt dosyası otomatik olarak AdvancedProjectAnalyzer tarafından oluşturulmuştur.*
*Son güncelleme: 2025-06-15 19:07:08*
