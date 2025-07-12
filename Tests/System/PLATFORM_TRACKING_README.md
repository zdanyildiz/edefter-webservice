# Platform Tracking Sistemi

Modern e-ticaret platformları için merkezi tracking kodu yönetim sistemi. Google Analytics, Facebook Pixel, TikTok Pixel, Google Ads ve daha fazla platform için tek noktadan yönetim sağlar.

## 🚀 Özellikler

- **Çoklu Platform Desteği**: Google Analytics, Facebook Pixel, TikTok Pixel, Google Ads, LinkedIn Insight Tag
- **Merkezi Yönetim**: Tüm tracking kodları tek bir yerden yönetilir
- **Çoklu Dil Desteği**: Her dil için farklı tracking konfigürasyonları
- **Otomatik Kod Enjeksiyonu**: Head bölümüne otomatik tracking kodu ekleme
- **Sayfa Tipi Bazlı Tracking**: Ürün, sepet, satın alma sayfaları için özel eventler
- **Performanslı**: Optimize edilmiş kod üretimi ve cache desteği
- **Geriye Uyumluluk**: Eski tracking sistemi ile tam uyumluluk

## 📋 Desteklenen Platformlar

| Platform | Kod | Özellikler |
|----------|-----|------------|
| Google Analytics | GA | Tracking ID, Measurement ID |
| Facebook Pixel | FB | Pixel ID, Purchase/AddToCart Events |
| Google Ads | GAD | Conversion ID, Conversion Label |
| TikTok Pixel | TT | Pixel ID, CompletePayment/AddToCart Events |
| LinkedIn Insight | LI | Partner ID |

## 🛠️ Kurulum

### 1. Veritabanı Tablosu

```sql
CREATE TABLE platform_tracking (
    tracking_id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    language_id INT DEFAULT 1,
    config TEXT,
    status BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_platform_lang (platform, language_id),
    KEY idx_status (status)
);
```

### 2. Dosya Yapısı

```
App/
├── Helpers/
│   ├── PlatformTrackingManager.php     # Ana yönetim sınıfı
│   ├── HeadTrackingInjector.php        # Otomatik kod enjeksiyonu
│   └── LegacyTrackingBridge.php        # Geriye uyumluluk
├── Controller/Admin/
│   └── AdminPluginsController.php       # Admin işlemleri
└── Database/migrations/
    └── xxxx_create_platform_tracking_table.php
```

### 3. Admin Panel

Platform tracking yönetimi için admin paneli:
```
/_y/s/s/ekkodlar/PlatformTracking.php
```

## 📖 Kullanım

### Temel Kullanım

```php
// Template head bölümünde
include_once ROOT . '/App/Helpers/LegacyTrackingBridge.php';
echo getAllTrackingCodes($db, $config, $languageID);
```

### Sayfa Tipi Bazlı Kullanım

```php
// Ürün sayfası
$productData = [
    'productID' => $product['productID'],
    'productName' => $product['productName'],
    'categoryName' => $product['categoryName'],
    'price' => $product['price']
];
echo getAllTrackingCodes($db, $config, $languageID, 'product', $productData);

// Satın alma sayfası
$orderData = [
    'orderID' => $order['orderID'],
    'total' => $order['total'],
    'items' => $order['items']
];
echo getAllTrackingCodes($db, $config, $languageID, 'thankyou', $orderData);
```

### Platform Kontrolü

```php
// Platform aktif mi kontrol et
if (isPlatformActive($db, 'google_analytics', $languageID)) {
    // Google Analytics aktif
}

// Platform konfigürasyonunu al
$gaConfig = getPlatformConfig($db, 'google_analytics', $languageID);
echo $gaConfig['tracking_id']; // GA-XXXXX-X
```

### Manuel Dönüşüm Kodu

```php
// Belirli platform için dönüşüm kodu oluştur
$conversionCode = getConversionCodes($db, $config, 'google_ads', 'purchase', [
    'value' => 299.99,
    'currency' => 'TRY',
    'order_id' => 'ORDER_123'
], $languageID);

echo $conversionCode;
```

## 🎛️ Admin Panel Kullanımı

### Platform Konfigürasyonu

1. `/_y/s/s/ekkodlar/PlatformTracking.php` adresine gidin
2. Dil seçin
3. Platform kartlarında gerekli alanları doldurun:
   - **Google Analytics**: Tracking ID (GA-XXXXX-X), Measurement ID (G-XXXXXXXXXX)
   - **Facebook Pixel**: Pixel ID (123456789)
   - **Google Ads**: Conversion ID (AW-123456789), Conversion Label
   - **TikTok Pixel**: Pixel ID (TTABCDEFG123456)

### Platform Aktifleştirme

Her platform için toggle switch ile aktif/pasif yapabilirsiniz.

### Kod Önizleme

"Önizle" butonları ile oluşturulacak kodları kontrol edebilirsiniz.

## 🧪 Test

Sistem kapsamlı test edilmiştir:

```bash
# Platform tracking testi
php Tests/System/test_platform_tracking.php

# Tam entegrasyon testi
php Tests/System/test_full_platform_tracking_integration.php

# Eski sistemden geçiş testi
php Tests/System/migrate_to_platform_tracking.php
```

## 📊 Performans

- 100 head kodu oluşturma: ~150ms
- Veritabanı sorgu optimizasyonu
- Template cache desteği
- Minimum bellek kullanımı

## 🔄 Eski Sistemden Geçiş

### Otomatik Geçiş

```php
// Eski sistemden otomatik geçiş
php Tests/System/migrate_to_platform_tracking.php
```

### Manuel Geçiş

Eski tracking sisteminizdeki kodları yeni sisteme manuel olarak aktarabilirsiniz:

1. Google Analytics: `analysis_codes` → `google_analytics`
2. Sales Conversion: `sales_conversion_codes` → `google_ads`
3. Cart Conversion: `cart_conversion_codes` → `facebook_pixel`
4. Tag Manager: `tag_manager` → Platform-specific configs

## 🌍 Çoklu Dil Desteği

Her dil için farklı tracking konfigürasyonları:

```php
// Türkçe için Google Analytics
$trackingManager->savePlatformConfig('google_analytics', [
    'tracking_id' => 'GA-123456-1'
], 1);

// İngilizce için Google Analytics
$trackingManager->savePlatformConfig('google_analytics', [
    'tracking_id' => 'GA-654321-2'
], 2);
```

## 🔧 API Referansı

### PlatformTrackingManager

```php
// Platform konfigürasyonu kaydet
$trackingManager->savePlatformConfig($platform, $config, $languageID);

// Platform konfigürasyonu getir
$config = $trackingManager->getPlatformConfig($platform, $languageID);

// Head kodları oluştur
$headCodes = $trackingManager->generateHeadCodes($languageID);

// Dönüşüm kodu oluştur
$conversionCode = $trackingManager->generateConversionCode($platform, $eventType, $eventData, $languageID);

// Aktif platformları getir
$platforms = $trackingManager->getActivePlatforms($languageID);
```

### HeadTrackingInjector

```php
// Hızlı entegrasyon
$codes = HeadTrackingInjector::inject($db, $config, $languageID, $pageType, $pageData);

// Sayfa tipi bazlı kodlar
$injector = new HeadTrackingInjector($db, $config);
$codes = $injector->generatePageTrackingCodes($pageType, $pageData, $languageID);
```

## 🛡️ Güvenlik

- XSS koruması için `htmlspecialchars()` kullanımı
- SQL injection koruması
- Platform validation
- JSON konfigürasyon sanitization

## 📝 Örnek Konfigürasyonlar

### Google Analytics 4

```json
{
    "tracking_id": "GA-123456-1",
    "measurement_id": "G-XXXXXXXXXX"
}
```

### Facebook Pixel

```json
{
    "pixel_id": "123456789"
}
```

### Google Ads

```json
{
    "conversion_id": "AW-123456789",
    "conversion_label": "AbCdEfGhIjKlMnOpQr"
}
```

### TikTok Pixel

```json
{
    "pixel_id": "TTABCDEFG123456"
}
```

## 🐛 Hata Ayıklama

### Log Kontrolü

```php
// Error log kontrolü
tail -f /path/to/error.log
```

### Debug Modu

```php
// Debug için kod çıktısını konsola yazdır
echo "<!-- DEBUG: " . $headCodes . " -->";
```

### Platform Test

```php
// Platform aktiflik test
if (!isPlatformActive($db, 'google_analytics', $languageID)) {
    echo "Google Analytics pasif!";
}
```

## 📞 Destek

- Test dosyaları: `Tests/System/test_platform_tracking.php`
- Örnek entegrasyon: `Examples/template_integration_example.php`
- Legacy bridge: `App/Helpers/LegacyTrackingBridge.php`

## 🔄 Sürüm Geçmişi

### v1.0.0
- ✅ Temel platform desteği (GA, FB, Google Ads, TikTok)
- ✅ Admin panel arayüzü
- ✅ Otomatik kod enjeksiyonu
- ✅ Çoklu dil desteği
- ✅ Legacy sistem uyumluluğu
- ✅ Kapsamlı test coverage

## 🎯 Gelecek Özellikler

- [ ] Twitter Pixel desteği
- [ ] Pinterest Pixel desteği
- [ ] Snapchat Pixel desteği
- [ ] Custom event tracking
- [ ] A/B testing desteği
- [ ] Real-time analytics dashboard
