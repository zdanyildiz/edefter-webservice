# ORDER/PAYMENT SYSTEM - Model Context Protocol Prompt
*Bu dosya, GitHub Copilot için sipariş/ödeme sistem anlayış ve geliştirme notlarını içerir*

## 🎯 AMAÇ VE KAPSAM

Bu prompt, yeni.globalpozitif.com.tr projesindeki **Order/Payment** sisteminin tam analizi ve geliştirme rehberidir. Model Context Protocol (MCP) metodolojisi kullanılarak, sistemin her bileşeni detaylandırılmış ve geliştirici deneyimi optimize edilmiştir.

### Sistem Sorumlulukları
- ✅ Sipariş oluşturma ve yönetimi
- ✅ Ödeme işlemleri ve entegrasyonları
- ✅ Kargo takibi ve yönetimi
- ✅ Fatura oluşturma ve bilgi yönetimi
- ✅ Admin panel sipariş CRUD işlemleri
- ✅ Sipariş durumu takibi
- ✅ İndirim ve puan sistemi yönetimi
- ✅ Ödeme yöntemleri yönetimi
- ✅ E-fatura entegrasyonu
- ✅ Sipariş raporlama sistemi

## 🏗️ SİSTEM MİMARİSİ

### Core Bileşenler
```
Order/Payment System/
├── Frontend Models/
│   └── Order.php                 # Ana sipariş işlemleri
│
├── Admin Models/
│   └── AdminOrder.php            # Admin sipariş CRUD
│
├── Controllers/
│   ├── CheckoutController.php    # Ödeme süreçleri
│   └── PaymentController.php     # Ödeme entegrasyonları
│
├── Payment Gateways/
│   ├── PayTR.php                 # PayTR ödeme sistemi
│   └── HalbankPaymentAPI.php     # Halbank ödeme sistemi
│
├── Admin Interface/
│   ├── OrderList.php             # Sipariş listesi
│   ├── CreateOrder.php           # Sipariş oluşturma
│   ├── kargoTakipAdmin.php       # Kargo takip
│   └── siparisbul.php           # Sipariş arama
│
└── Database Tables/
    ├── uyesiparis               # Ana sipariş tablosu
    └── uyesiparisdurum          # Sipariş durum tablosu
```

## 🗃️ VERİTABANI YAPISI

### Tablo: `uyesiparis` (Siparişler)
```sql
-- Temel Sipariş Bilgileri
siparisid INT AI PK                   -- Sipariş ID
uyeid INT                             -- Üye referansı
siparisbenzersizid CHAR(20)           -- Benzersiz sipariş ID
siparistariholustur DATETIME(6)       -- Oluşturma tarihi
siparistarihguncelle DATETIME(6)      -- Güncelleme tarihi

-- Ödeme Bilgileri
siparisodemeparabirim VARCHAR(3)      -- Para birimi (TRY, USD, EUR)
siparisodemetaksit TINYINT            -- Taksit sayısı
siparisodemeyontemi VARCHAR(3)        -- Ödeme yöntemi (kk, bh, ko)
siparisodemedurum TINYINT             -- Ödeme durumu (0: bekliyor, 1: tamamlandı)

-- Ürün Bilgileri (CSV formatında)
siparisurunidler VARCHAR(500)         -- Ürün ID'leri (3073,3072)
siparisurunadlar VARCHAR(500)         -- Ürün adları (|| ile ayrılmış)
siparisurunstokkodlar VARCHAR(500)    -- Stok kodları (|| ile ayrılmış)
siparisurunkategoriler VARCHAR(500)   -- Kategori adları (|| ile ayrılmış)
siparisurunfiyatlar VARCHAR(500)      -- Ürün fiyatları (|| ile ayrılmış)
siparisurunadetler VARCHAR(500)       -- Ürün adetleri (|| ile ayrılmış)

-- Teslimat Bilgileri
siparisteslimatad VARCHAR(50)         -- Teslimat adı
siparisteslimatsoyad VARCHAR(50)      -- Teslimat soyadı
siparisteslimateposta VARCHAR(100)    -- Teslimat e-posta
siparisteslimatgsm VARCHAR(50)        -- Teslimat telefon
siparisteslimattcno CHAR(11)          -- Teslimat TC No
siparisteslimatadresulke VARCHAR(50)  -- Teslimat ülke
siparisteslimatadressehir VARCHAR(50) -- Teslimat şehir
siparisteslimatadresilce VARCHAR(50)  -- Teslimat ilçe
siparisteslimatadressemt VARCHAR(50)  -- Teslimat semt
siparisteslimatadresmahalle VARCHAR(50) -- Teslimat mahalle
siparisteslimatadrespostakod VARCHAR(10) -- Teslimat posta kodu
siparisteslimatadresacik VARCHAR(255) -- Teslimat açık adres
siparisteslimatadresulkekod VARCHAR(3) -- Teslimat ülke kodu

-- Fatura Bilgileri
siparisfaturaunvan VARCHAR(255)       -- Fatura unvanı
siparisfaturavergidairesi VARCHAR(100) -- Vergi dairesi
siparisfaturavergino CHAR(12)         -- Vergi numarası
siparisfaturaad VARCHAR(50)           -- Fatura adı
siparisfaturasoyad VARCHAR(50)        -- Fatura soyadı
siparisfaturaeposta VARCHAR(100)      -- Fatura e-posta
siparisfaturagsm VARCHAR(50)          -- Fatura telefon
siparisfaturaadresulke VARCHAR(50)    -- Fatura ülke
siparisfaturaadressehir VARCHAR(50)   -- Fatura şehir
siparisfaturaadresilce VARCHAR(50)    -- Fatura ilçe
siparisfaturaadressemt VARCHAR(50)    -- Fatura semt
siparisfaturaadresmahalle VARCHAR(50) -- Fatura mahalle
siparisfaturaadrespostakod VARCHAR(50) -- Fatura posta kodu
siparisfaturaadresacik VARCHAR(255)   -- Fatura açık adres
siparisfaturaadresulkekod VARCHAR(3)  -- Fatura ülke kodu

-- Kargo Bilgileri
kargoid TINYINT                       -- Kargo firması ID
sipariskargofiyat DECIMAL(8,2)        -- Kargo ücreti
sipariskargotarih DATETIME            -- Kargo tarihi
sipariskargoserino VARCHAR(64)        -- Kargo seri no
sipariskargodurum VARCHAR(64)         -- Kargo durumu
sipariskargotakip VARCHAR(64)         -- Kargo takip no
siparisteslimatid VARCHAR(64)         -- Teslimat ID
kargoCode VARCHAR(1)                  -- Kargo kodu
siparisKargoBarcode LONGTEXT          -- Kargo barkodu
tempBarcodeNumber VARCHAR(30)         -- Geçici barkod
siparisKargoSevkiyatYapildi TINYINT(1) -- Sevkiyat durumu
kargokod CHAR(50)                     -- Kargo kodu

-- Finansal Bilgiler
siparistoplamtutar DECIMAL(8,2)       -- Toplam tutar
sipariskdvtutar DECIMAL(8,2)          -- KDV tutarı
sipariskdvsiztutar DECIMAL(8,2)       -- KDV'siz tutar
sipariskargodahilfiyat DECIMAL(8,2)   -- Kargo dahil fiyat

-- İndirim Sistemi
siparistekcekimindirimorani DOUBLE    -- Tek çekim indirim oranı
siparistekcekimindirimlifiyat DOUBLE  -- Tek çekim indirimli fiyat  
siparishavaleorani DOUBLE             -- Havale indirim oranı
siparishavaleindirimlifiyat DECIMAL(8,2) -- Havale indirimli fiyat
sipariskargoindirim DOUBLE            -- Kargo indirim
sipariskargoindirimaciklama VARCHAR(100) -- Kargo indirim açıklama

-- Puan Sistemi
siparispuanindirim DOUBLE             -- Puan indirimi
siparispuanonceki DOUBLE              -- Önceki puan
siparispuanharcanan DOUBLE            -- Harcanan puan
siparispuankazanilan DOUBLE           -- Kazanılan puan
siparispuankalan DOUBLE               -- Kalan puan

-- Sistem Bilgileri
siparisdurum TINYINT(1)               -- Sipariş durumu
siparisip VARCHAR(15)                 -- Sipariş IP adresi
siparisdekont VARCHAR(25)             -- Dekont numarası
siparisnotalici MEDIUMTEXT            -- Alıcı notu
siparisnotyonetici MEDIUMTEXT         -- Yönetici notu
siparissil TINYINT(1)                 -- Silme durumu
```

### Tablo: `uyesiparisdurum` (Sipariş Durumları)
```sql
-- Durum tablosu (AdminOrder.php'den referans alınarak)
durumid INT AI PK                     -- Durum ID
durumad VARCHAR(100)                  -- Durum adı
durumrenk VARCHAR(7)                  -- Durum rengi (hex)
durumaktif TINYINT(1)                 -- Aktif/pasif
durumsil TINYINT(1)                   -- Silme durumu
```

## 🔧 ANA MODEL SINIFLARI

### 1. Order.php (Frontend Model)

#### Temel İşlevler
```php
// Sipariş oluşturma
createOrder($orderData)               // Yeni sipariş oluşturma
processOrderFromCart($cartData)       // Sepetten sipariş işleme

// Sipariş sorgulama
getOrderInfo($orderID)                // Sipariş detay bilgisi
getOrderByUniqID($uniqID)             // Benzersiz ID ile sipariş getirme
getMemberOrders($memberID)            // Üye siparişlerini listeleme

// Sipariş güncelleme
updateOrderStatus($orderID, $status)  // Sipariş durumu güncelleme
updatePaymentStatus($orderID, $status) // Ödeme durumu güncelleme
addOrderNote($orderID, $note)         // Sipariş notu ekleme

// Kargo işlemleri
updateCargoInfo($orderID, $cargoData) // Kargo bilgisi güncelleme
getCargoTrackingInfo($orderID)        // Kargo takip bilgisi
```

#### Sipariş Veri Yapısı
```php
// CSV formatında ürün bilgileri
$productIDs = "3073,3072";            // Virgülle ayrılmış
$productNames = "Ürün 1||Ürün 2";     // || ile ayrılmış
$productPrices = "64.41||32.20";      // || ile ayrılmış
$productQuantities = "20.0000||40.0000"; // || ile ayrılmış

// Örnek sipariş verisi:
$orderData = [
    'memberID' => 438,
    'orderUniqID' => 'SPRR6M00000000000724',
    'currency' => 'TRY',
    'paymentMethod' => 'kk', // kk: kredi kartı, bh: banka havalesi, ko: kapıda ödeme
    'totalAmount' => 2577.20,
    'deliveryInfo' => [...],
    'billingInfo' => [...],
    'cartItems' => [...]
];
```

### 2. AdminOrder.php (Admin Model)

#### Admin İşlevler
```php
// CRUD İşlemleri
getOrdersPaginated($page, $perPage)   // Sayfalı sipariş listesi
getOrderInfo($orderID)                // Sipariş detay bilgisi
updateOrder($orderID, $orderData)     // Sipariş güncelleme
deleteOrder($orderID)                 // Sipariş silme (soft delete)

// Filtreleme ve Arama
getOrdersByStatus($status)            // Duruma göre siparişler
getOrdersByPaymentType($paymentType)  // Ödeme tipine göre siparişler
getOrdersByDateRange($startDate, $endDate) // Tarih aralığına göre
searchOrders($searchTerm)             // Sipariş arama

// Durum Yönetimi
getOrderStatuses()                    // Sipariş durumlarını getirme
updateOrderStatus($orderID, $statusID) // Sipariş durumu güncelleme
getOrdersByPaymentStatus($paymentStatus) // Ödeme durumuna göre

// Finansal İşlemler
calculateOrderTotals($orderData)      // Sipariş toplamlarını hesaplama
applyDiscounts($orderData, $discounts) // İndirim uygulama
calculateTax($orderData)              // Vergi hesaplama
```

#### Admin Özel Fonksiyonlar
```php
// Ödeme yöntemlerine göre listeleme
getOrdersByPaymentTypeAndOrderStatus('kk', 1)  // Kredi kartı, tamamlanmış
getOrdersByPaymentTypeAndOrderStatus('bh', 1)  // Banka havalesi
getOrdersByPaymentTypeAndOrderStatus('ko', 1)  // Kapıda ödeme

// Sipariş durumlarına göre
getOrdersByPaymentStatusAndOrderStatus(0, 6)   // Ödeme bekleyen, iptal edilen
```

## 💳 ÖDEME SİSTEMİ

### PayTR Entegrasyonu

#### PayTR.php Sınıfı
```php
// Ödeme isteği gönderme
sendPaymentRequest($visitor, $orderData)

// Ödeme verifikasyonu
verifyPaymentCallback($postData)

// İade işlemi
processRefund($orderID, $amount)
```

#### PayTR İşlem Akışı
```php
// 1. Ödeme sayfası oluşturma
$payTR = new PayTR($merchantID, $merchantKey, $merchantSalt);
$paymentFrame = $payTR->sendPaymentRequest($visitor, $orderData);

// 2. Callback işleme
$isPaymentSuccessful = $payTR->verifyPaymentCallback($_POST);

// 3. Sipariş durumu güncelleme
if ($isPaymentSuccessful) {
    $orderModel->updatePaymentStatus($orderID, 1);
}
```

### Halbank Entegrasyonu

#### HalbankPaymentAPI.php
```php
// Halbank API entegrasyonu
// - 3D Secure ödeme işlemleri
// - Token bazlı güvenlik
// - Ödeme durumu sorgulamaları
```

### Ödeme Yöntemleri
```php
// Desteklenen ödeme yöntemleri:
'kk' => 'Kredi Kartı',         // PayTR, Halbank
'bh' => 'Banka Havalesi',      // Manuel onay sistemi
'ko' => 'Kapıda Ödeme',        // Nakit/Kartla kapıda ödeme
```

## 🖥️ ADMIN PANEL SİSTEMİ

### Admin Güvenlik Sistemi
```php
// global.php üzerinden kimlik doğrulama
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");

// Otomatik değişkenler:
// @var AdminDatabase $db
// @var Config $config
// @var Helper $helper
// @var AdminCasper $adminCasper
// @var AdminSession $adminSession
```

### OrderList.php (Sipariş Listesi)

#### Filtreleme Sistemi
```php
// URL parametreleri ile filtreleme
// ?orderStatus=99  -> Kredi kartı siparişleri
// ?orderStatus=98  -> Banka havalesi siparişleri  
// ?orderStatus=97  -> Kapıda ödeme siparişleri
// ?orderStatus=96  -> Ödeme bekleyen siparişler
```

#### Sayfalama Sistemi
```php
$limit = $_GET['limit'] ?? 10;        // Sayfa başı kayıt
$page = $_GET['page'] ?? 1;           // Mevcut sayfa
$offset = ($page - 1) * $limit;      // Offset hesaplama
```

#### Session Optimizasyonu
```php
// Siparişleri session'da cache'leme
$sessionOrders = $_SESSION['orders'] ?? null;
if ($sessionOrdersType != $orderType) {
    // Yeniden veritabanından çek
    $orders = $adminOrder->getOrdersByStatus($orderType);
    $_SESSION['orders'] = [
        'type' => $orderType,
        'data' => $orders,
        'count' => count($orders)
    ];
}
```

### CreateOrder.php (Sipariş Oluşturma)

#### Manuel Sipariş Oluşturma
- **Üye seçimi**: Mevcut üye veya misafir sipariş
- **Ürün ekleme**: Dinamik ürün ekleme sistemi
- **Adres yönetimi**: Teslimat ve fatura adresi
- **Ödeme ayarları**: Ödeme yöntemi ve taksit seçimi
- **İndirim uygulama**: Manuel indirim ve puan kullanımı

### kargoTakipAdmin.php (Kargo Takip)

#### Kargo Yönetim Özellikleri
- **Toplu kargo oluşturma**: Seçili siparişler için
- **Barkod yazdırma**: Kargo etiketleri
- **Takip numarası güncelleme**: Otomatik/manuel
- **Durum güncelleme**: Kargo firması entegrasyonu

## 🔄 İŞ AKIŞLARI (WORKFLOWS)

### Sipariş Oluşturma Akışı
```
1. Sepet → Ödeme Sayfası
2. Üye Bilgilerini Doğrula
3. Teslimat/Fatura Adreslerini Al
4. Ödeme Yöntemi Seç
5. İndirim/Puan Hesapla
6. Sipariş Kaydet (uyesiparis)
7. Ödeme İşlemini Başlat
8. Ödeme Onayını Bekle
9. Stok Güncelle
10. E-posta Bildirimi Gönder
```

### Ödeme İşleme Akışı
```
PayTR/Halbank:
1. Ödeme sayfası oluştur
2. 3D Secure doğrulama
3. Callback'i işle
4. Sipariş durumunu güncelle
5. Fatura oluştur

Banka Havalesi:
1. Banka bilgilerini göster
2. Dekont yükleme sistemi
3. Manuel onay bekle
4. Admin onayı ile işle

Kapıda Ödeme:
1. Sipariş onayı
2. Kargo hazırlama
3. Teslimat sırasında tahsilat
```

### Kargo İşleme Akışı
```
1. Sipariş onaylandı
2. Kargo firması seç
3. Teslimat adresi doğrula
4. Kargo etiketi oluştur
5. Barkod yazdır
6. Sevkiyat işaretleme
7. Takip numarası güncelle
8. Müşteriye bildirim gönder
9. Teslimat takibi
```

## 🔒 GÜVENLİK VE DOĞRULAMA

### Ödeme Güvenliği
```php
// PayTR hash doğrulama
$hash = base64_encode(hash_hmac('sha256', $data, $merchant_key, true));

// 3D Secure entegrasyonu
$secure3D = true;  // Tüm kartlı ödemelerde zorunlu

// IP güvenliği
$allowedIPs = ['PayTR_IP_addresses'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    die('Unauthorized access');
}
```

### Veri Validasyonu
```php
// Sipariş verisi doğrulama
function validateOrderData($orderData) {
    // E-posta formatı kontrolü
    if (!filter_var($orderData['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Telefon numarası kontrolü
    if (!preg_match('/^[0-9]{10,11}$/', $orderData['phone'])) {
        return false;
    }
    
    // Tutar kontrolü
    if ($orderData['amount'] <= 0) {
        return false;
    }
    
    return true;
}
```

### SQL Injection Koruması
```php
// Prepared statements kullanımı
$sql = "SELECT * FROM uyesiparis WHERE siparisid = :orderID";
$params = ['orderID' => $orderID];
return $this->db->select($sql, $params);
```

## 📊 RAPORLAMA VE ANALİTİK

### Sipariş Raporları
```php
// Günlük sipariş raporu
getDailyOrderReport($date)

// Aylık satış raporu  
getMonthlySalesReport($month, $year)

// Ödeme yöntemi analizi
getPaymentMethodAnalysis($startDate, $endDate)

// Müşteri sipariş geçmişi
getCustomerOrderHistory($memberID)
```

### Finansal Raporlar
```php
// Gelir analizi
getRevenueAnalysis($period)

// İndirim kullanım raporu
getDiscountUsageReport($startDate, $endDate)

// Vergi raporu
getTaxReport($period)

// Kargo maliyet analizi
getShippingCostAnalysis($period)
```

## 🔧 TROUBLESHOOTING

### Yaygın Sorunlar ve Çözümler

#### 1. Ödeme Callback'i Çalışmıyor
```php
// Problem: PayTR callback'i alınamıyor
// Çözüm: Server IP'sini PayTR'ye bildirin, SSL sertifikası kontrol edin
$callback_url = 'https://domain.com/payment-callback.php';
```

#### 2. Sipariş Durumu Güncellenmiyor
```php
// Problem: Ödeme başarılı ama sipariş durumu güncellenmiyor
// Çözüm: Transaction kontrolü yapın
$this->db->beginTransaction();
try {
    $this->updateOrderStatus($orderID, 1);
    $this->updatePaymentStatus($orderID, 1);
    $this->db->commit();
} catch (Exception $e) {
    $this->db->rollBack();
    Log::write("Order update failed: " . $e->getMessage());
}
```

#### 3. Kargo Takip Numarası Güncellenmiyor
```php
// Problem: Kargo firması API'si çalışmıyor
// Çözüm: Fallback mekanizması ekleyin
if (!$cargoAPI->updateTrackingNumber($orderID, $trackingNumber)) {
    // Manuel güncelleme için admin'e bildirim gönder
    $this->sendAdminNotification("Kargo takip güncelleme hatası", $orderID);
}
```

## 🚀 GELİŞTİRME REHBERİ

### Yeni Ödeme Yöntemi Ekleme

#### 1. Payment Model Oluşturma
```php
// App/Model/Payment/YeniOdeme.php
class YeniOdeme {
    public function sendPaymentRequest($orderData) {
        // Ödeme API'si entegrasyonu
    }
    
    public function verifyCallback($callbackData) {
        // Callback doğrulama
    }
}
```

#### 2. Controller'a Entegrasyon
```php
// CheckoutController.php'de
if ($paymentMethod === 'yeni_odeme') {
    $yeniOdeme = new YeniOdeme($config);
    $result = $yeniOdeme->sendPaymentRequest($orderData);
}
```

#### 3. Admin Panel Ayarları
```php
// AdminPaymentGateway.php'de yeni ödeme yöntemi ayarları
$paymentMethods = [
    'kk' => 'Kredi Kartı',
    'bh' => 'Banka Havalesi', 
    'ko' => 'Kapıda Ödeme',
    'yeni_odeme' => 'Yeni Ödeme Sistemi'
];
```

### Test Stratejisi
```php
// Test dosyası oluşturma
// Tests/Orders/OrderSystemTester.php

class OrderSystemTester {
    public function testOrderCreation() {
        // Sipariş oluşturma testı
    }
    
    public function testPaymentProcessing() {
        // Ödeme işleme testı
    }
    
    public function testCargoIntegration() {
        // Kargo entegrasyon testı  
    }
}
```

## 📈 PERFORMANS OPTİMİZASYONU

### Cache Stratejileri
```php
// Sipariş listesi cache'leme
$cacheKey = "orders_" . $orderStatus . "_" . $page;
$cachedOrders = $cache->get($cacheKey);
if (!$cachedOrders) {
    $orders = $adminOrder->getOrdersByStatus($orderStatus);
    $cache->set($cacheKey, $orders, 300); // 5 dakika
}
```

### Database Optimizasyonu
```php
// Index önerileri
CREATE INDEX idx_siparis_durum ON uyesiparis(siparisdurum);
CREATE INDEX idx_siparis_tarih ON uyesiparis(siparistariholustur);
CREATE INDEX idx_siparis_uye ON uyesiparis(uyeid);
CREATE INDEX idx_siparis_benzersiz ON uyesiparis(siparisbenzersizid);
```

## 🔄 DİĞER SİSTEMLERLE ENTEGRASYON

### Bağlantılı Sistemler
- **Member System**: Üye sipariş geçmişi
- **Product System**: Stok güncellemeleri
- **Cart System**: Sepetten sipariş dönüşümü
- **Banner System**: Sipariş tabanlı banner gösterimi
- **Email System**: Sipariş bildirimleri

### Veri Akışı
```
Cart → Checkout → Order Creation → Payment Processing
   ↓         ↓           ↓              ↓
Member   Address    Stock Update   Email Notification
System   Validation    System         System
   ↓         ↓           ↓              ↓  
History  Location    Product         Admin
Update   System      System        Notification
```

## 📚 KAYNAKLAR VE REFERANSLAR

### İlgili Dosyalar
- `App/Model/Order.php` - Ana sipariş modeli
- `App/Model/Admin/AdminOrder.php` - Admin sipariş modeli
- `App/Controller/CheckoutController.php` - Ödeme controller
- `App/Model/Payment/PayTR.php` - PayTR ödeme entegrasyonu
- `App/Model/Payment/HalbankPaymentAPI.php` - Halbank entegrasyonu
- `_y/s/s/siparisler/OrderList.php` - Admin sipariş listesi
- `_y/s/s/siparisler/CreateOrder.php` - Admin sipariş oluşturma

### Bağımlı Sistemler
- **Core System**: Config, Database, Helper
- **Member System**: Üye yönetimi ve kimlik doğrulama
- **Product System**: Ürün bilgileri ve stok yönetimi
- **Cart System**: Sepet işlemleri
- **Location System**: Adres ve kargo bilgileri
- **Email System**: Otomatik bildirimler

---

*Bu dokümantasyon, Model Context Protocol metodolojisi ile oluşturulmuş olup, GitHub Copilot'un proje hakkındaki anlayışını optimize etmek için tasarlanmıştır.*
