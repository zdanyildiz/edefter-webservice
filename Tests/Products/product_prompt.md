# PRODUCT SİSTEMİ DETAYLI PROMPT - yeni.globalpozitif.com.tr
*E-ticaret ürün yönetim sistemi için Model Context Protocol tabanlı rehber dokümantasyonu*

## 🏗️ PRODUCT SİSTEM MİMARİSİ

### Dosya Hierarşisi ve Bağımlılıklar
```
/_y/s/s/urunler/AddProduct.php (Ana Ürün Admin Sayfası)
├── /_y/s/global.php (Global Admin Konfigürasyonu)
├── App/Model/Product.php (Frontend Ürün Modeli)
├── App/Model/Admin/AdminProduct.php (Admin Ürün CRUD)
├── App/Model/Admin/AdminProductCategory.php (Kategori Yönetimi)
├── App/Model/Admin/AdminProductVariant.php (Varyasyon Yönetimi)
├── App/Model/Admin/AdminProductQuantityUnit.php (Birim Yönetimi)
├── App/Controller/ProductController.php (Ürün Controller'ı)
├── Tab Dosyaları (Modüler Admin Interface)
│   ├── addProductContentTab.php (İçerik sekmesi)
│   ├── addProductMediaTab.php (Medya sekmesi)
│   ├── addProductVariantTab.php (Varyasyon sekmesi)
│   ├── addProductPriceSettingTab.php (Fiyat sekmesi)
│   ├── addProductCategorySupplierBrandGroupTab.php (Kategori/Marka sekmesi)
│   ├── addProductSeoSettings.php (SEO sekmesi)
│   ├── addProductCargoSettings.php (Kargo sekmesi)
│   ├── addProductPaymentSettings.php (Ödeme sekmesi)
│   └── addProductShowcase.php (Vitrin sekmesi)
└── ProductList.php (Ürün Listesi)
```

## 📊 VERİTABANI YAPISI

### Ana Ürün Tabloları
```sql
-- ÜRÜN ÖZELLİKLERİ (Ana ürün veriler)
urunozellikleri:
    sayfaid (PK, sayfa tablosu ile ilişki)
    urunkodu (Ürün kodu/SKU)
    urunadi (Ürün adı)
    uruntip (Ürün tipi)
    urunaciklama (Ürün açıklaması)
    urunsatisfiyat (Satış fiyatı)
    uruneskifiyat (Eski fiyat)
    urunmiktar (Stok miktarı)
    urunmiktarbirimid (Birim ID'si)
    urunparabirim (Para birimi ID'si)
    markaid (Marka ID'si)
    tedarikciid (Tedarikçi ID'si)
    urundurum (Ürün durumu)
    urunaktif (Aktif/Pasif)
    urunsil (Silinmiş mi)

-- ÜRÜN VARYASYONLARI
variant_properties:
    id (PK)
    variant_id (Varyasyon ID'si)
    variant_stock_code (Varyasyon stok kodu)
    variant_quantity (Varyasyon miktarı)
    variant_selling_price (Varyasyon fiyatı)
    variant_image_ids (Varyasyon görsel ID'leri)
    attribute_name (Özellik adı: renk, beden, vb.)
    attribute_value (Özellik değeri: kırmızı, XL, vb.)

-- ÜRÜN KATEGORİLERİ
kategori:
    kategoriid (PK)
    kategoriad (Kategori adı)
    kategorilink (SEO URL)
    kategorisira (Sıralama)
    kategoriust (Üst kategori ID)
    dilid (Dil ID'si)

-- ÜRÜN-SAYFA İLİŞKİSİ
sayfalistekategori:
    sayfaid (Sayfa ID'si)
    kategoriid (Kategori ID'si)

-- SAYFA TABLOSU (Her ürün bir sayfa)
sayfa:
    sayfaid (PK)
    benzersizid (Benzersiz ID)
    sayfabaslik (Sayfa başlığı)
    sayfaicerik (Sayfa içeriği)
    sayfaaktif (Aktif/Pasif)
    sayfasil (Silinmiş mi)
```

## 🎯 PRODUCT MODEL ANALİZİ

### Frontend Product Model (App/Model/Product.php)
```php
class Product {
    private Database $db;
    private JSON $json;
    public string $productSql;  // Ana ürün sorgusu

    // Ana metodlar:
    public function getProductDetails($productID, $languageCode)  // Ürün detayları
    public function getProductsByCategory($categoryID)           // Kategoriye göre ürünler
    public function searchProducts($searchTerm)                 // Ürün arama
    public function getRelatedProducts($productID)              // İlgili ürünler
    public function getProductVariants($productID)              // Ürün varyasyonları
    public function updateProductStock($productID, $quantity)   // Stok güncelleme
}
```

### Admin Product Model (App/Model/Admin/AdminProduct.php)
```php
class AdminProduct {
    private AdminDatabase $db;
    public string $productSql;
    public Json $json;
    public int $resultsPerPage = 20;
    public int $currentPage = 1;

    // CRUD Metodları:
    public function addProduct($productData)                    // Ürün ekleme
    public function updateProduct($productID, $productData)     // Ürün güncelleme
    public function deleteProduct($productID)                  // Ürün silme
    public function getProductById($productID)                 // ID'ye göre ürün
    public function getAllProducts($filters = [])               // Ürün listesi
    public function getProductsBySupplier($supplierID)         // Tedarikçiye göre
    public function getProductsByBrand($brandID)               // Markaya göre
    
    // Varyasyon Metodları:
    public function addProductVariant($variantData)            // Varyasyon ekleme
    public function updateVariantStock($variantID, $stock)     // Varyasyon stok
    public function getVariantsByProduct($productID)          // Ürün varyasyonları
    
    // Kategori Metodları:
    public function assignProductToCategory($productID, $categoryID) // Kategori atama
    public function getProductCategories($productID)                // Ürün kategorileri
}
```

## 🎨 MODÜLER TAB SİSTEMİ

### Admin Interface Tab Yapısı
AddProduct.php sayfası modüler tab sistemi ile organize edilmiştir:

#### 1. İçerik Sekmesi (addProductContentTab.php)
```php
// Ürün temel bilgileri
- Ürün Adı
- Ürün Kodu/SKU
- Ürün Açıklaması (Summernote Editor)
- Kısa Açıklama
- Ürün Durumu (Yeni, İkinci El, vb.)
- Ürün Tipi (Fiziksel, Dijital, Hizmet)
```

#### 2. Medya Sekmesi (addProductMediaTab.php)
```php
// Görsel ve video yönetimi
- Ana Ürün Görseli
- Ürün Galerisi (Çoklu görsel)
- Video URL'leri
- 360° Görsel Desteği
- Görsel SEO Alt Metinleri
```

#### 3. Varyasyon Sekmesi (addProductVariantTab.php)
```php
// Ürün varyasyonları (Renk, Beden, vb.)
- Varyasyon Grubu Seçimi
- Varyasyon Değerleri
- Varyasyon Stok Kodları
- Varyasyon Fiyatları
- Varyasyon Görselleri
- Stok Miktarları
```

#### 4. Fiyat Sekmesi (addProductPriceSettingTab.php)
```php
// Fiyatlama ve stok
- Satış Fiyatı
- Eski Fiyat (İndirim gösterimi)
- Alış Fiyatı
- Para Birimi
- KDV Oranı
- Stok Miktarı
- Minimum Stok Uyarısı
- Miktar Birimi
```

#### 5. Kategori/Marka Sekmesi (addProductCategorySupplierBrandGroupTab.php)
```php
// Kategorizasyon
- Ana Kategori
- Alt Kategoriler (Çoklu seçim)
- Marka Seçimi
- Tedarikçi Bilgisi
- Ürün Grubu
- Etiketler
```

#### 6. SEO Sekmesi (addProductSeoSettings.php)
```php
// Arama motoru optimizasyonu
- SEO Başlığı
- Meta Açıklama
- SEO Anahtar Kelimeleri
- SEO URL (Slug)
- OpenGraph Bilgileri
- Schema.org Markup
```

#### 7. Kargo Sekmesi (addProductCargoSettings.php)
```php
// Kargo ayarları
- Kargo Ücreti Hesaplama
- Ücretsiz Kargo Limiti
- Kargo Firması Seçimi
- Teslimat Süresi
- Ürün Boyutları (Desi hesabı)
- Kargo Kısıtlamaları
```

#### 8. Ödeme Sekmesi (addProductPaymentSettings.php)
```php
// Ödeme yöntemleri
- Kredi Kartı Kabul
- Havale/EFT Kabul
- Kapıda Ödeme
- Taksit Seçenekleri
- Özel İndirimler
```

#### 9. Vitrin Sekmesi (addProductShowcase.php)
```php
// Görüntüleme ayarları
- Ana Sayfada Göster
- Öne Çıkan Ürün
- İndirimli Ürünler
- Yeni Ürünler
- Çok Satan Ürünler
- Önerilen Ürünler
```

## 🔧 JAVASCRIPT VE AJAX İŞLEMLERİ

### Dinamik Form İşleme
```javascript
// Varyasyon ekleme
function addVariantRow() {
    let variantHTML = `
        <div class="variant-row">
            <input type="text" name="variant_name[]" placeholder="Özellik Adı" />
            <input type="text" name="variant_value[]" placeholder="Özellik Değeri" />
            <input type="number" name="variant_stock[]" placeholder="Stok" />
            <input type="number" name="variant_price[]" placeholder="Fiyat" />
        </div>
    `;
    $('#variantContainer').append(variantHTML);
}

// Kategori seçimi (Ajax)
function loadSubCategories(parentCategoryID) {
    $.ajax({
        url: 'getSubCategories.php',
        type: 'POST',
        data: { parentID: parentCategoryID },
        dataType: 'json',
        success: function(response) {
            $('#subCategorySelect').html(response.html);
        }
    });
}

// Ürün kaydetme
$('#productForm').on('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    
    $.ajax({
        url: 'saveProduct.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.status === 'success') {
                window.location.href = 'AddProduct.php?productID=' + response.productID;
            }
        }
    });
});
```

## 🔍 ÜRÜN ARAMA VE FİLTRELEME

### Frontend Arama Sistemi
```php
// ProductSearch.php
class ProductSearch {
    public function searchProducts($searchTerm, $filters = []) {
        $sql = "
            SELECT p.*, s.sayfabaslik, s.sayfaicerik
            FROM urunozellikleri p
            INNER JOIN sayfa s ON s.sayfaid = p.sayfaid
            WHERE 1=1
        ";
        
        // Metin araması
        if (!empty($searchTerm)) {
            $sql .= " AND (s.sayfabaslik LIKE :search OR p.urunadi LIKE :search)";
        }
        
        // Kategori filtresi
        if (!empty($filters['category'])) {
            $sql .= " AND p.sayfaid IN (
                SELECT slk.sayfaid FROM sayfalistekategori slk 
                WHERE slk.kategoriid = :categoryID
            )";
        }
        
        // Fiyat aralığı
        if (!empty($filters['minPrice'])) {
            $sql .= " AND p.urunsatisfiyat >= :minPrice";
        }
        
        // Marka filtresi
        if (!empty($filters['brand'])) {
            $sql .= " AND p.markaid = :brandID";
        }
        
        return $this->db->select($sql, $params);
    }
}
```

### Admin Ürün Listesi Filtreleme
```php
// ProductList.php
- Kategori Filtresi
- Marka Filtresi  
- Tedarikçi Filtresi
- Stok Durumu Filtresi
- Aktif/Pasif Filtresi
- Fiyat Aralığı Filtresi
- Tarih Aralığı Filtresi
```

## 🎯 VARYASYON YÖNETİMİ

### Varyasyon Türleri
```php
// AdminProductVariant.php
class AdminProductVariant {
    // Varyasyon grupları: Renk, Beden, Materyal, vb.
    public function getVariantGroups($languageCode) {
        return $this->db->select("
            SELECT vg.*, 
                   COALESCE(vgc.grup_adi, vg.grup_adi) as grup_adi
            FROM varyasyon_grup vg
            LEFT JOIN varyasyon_grup_ceviri vgc ON vg.id = vgc.grup_id 
                AND vgc.dil_kodu = :lang
            WHERE vg.aktif = 1
        ", ['lang' => $languageCode]);
    }
    
    // Varyasyon değerleri: Kırmızı, XL, Pamuk, vb.
    public function getVariantValues($groupID, $languageCode) {
        return $this->db->select("
            SELECT vd.*, 
                   COALESCE(vdc.deger_adi, vd.deger_adi) as deger_adi
            FROM varyasyon_deger vd
            LEFT JOIN varyasyon_deger_ceviri vdc ON vd.id = vdc.deger_id 
                AND vdc.dil_kodu = :lang
            WHERE vd.grup_id = :groupID AND vd.aktif = 1
        ", ['groupID' => $groupID, 'lang' => $languageCode]);
    }
}
```

### Varyasyon Fiyatlandırma
```javascript
// Varyasyon seçimine göre fiyat güncelleme
function updatePriceByVariant() {
    let selectedVariants = [];
    $('.variant-select:checked').each(function() {
        selectedVariants.push($(this).val());
    });
    
    if (selectedVariants.length > 0) {
        $.ajax({
            url: 'getVariantPrice.php',
            type: 'POST',
            data: { 
                productID: productID,
                variants: selectedVariants 
            },
            success: function(response) {
                $('#productPrice').text(response.price);
                $('#productStock').text(response.stock);
                updateProductImages(response.images);
            }
        });
    }
}
```

## 📦 STOK YÖNETİMİ

### Stok Takip Sistemi
```php
// Stock Control
class StockManager {
    public function updateStock($productID, $variantID, $quantity, $operation = 'decrease') {
        if ($variantID) {
            // Varyasyonlu ürün stok güncelleme
            $sql = "UPDATE variant_properties 
                   SET variant_quantity = variant_quantity " . 
                   ($operation === 'increase' ? '+' : '-') . " :quantity 
                   WHERE variant_id = :variantID";
        } else {
            // Ana ürün stok güncelleme
            $sql = "UPDATE urunozellikleri 
                   SET urunmiktar = urunmiktar " . 
                   ($operation === 'increase' ? '+' : '-') . " :quantity 
                   WHERE sayfaid = :productID";
        }
        
        return $this->db->update($sql, [
            'quantity' => $quantity,
            'variantID' => $variantID,
            'productID' => $productID
        ]);
    }
    
    public function checkLowStock($limit = 5) {
        return $this->db->select("
            SELECT p.sayfaid, p.urunadi, p.urunmiktar
            FROM urunozellikleri p
            WHERE p.urunmiktar <= :limit AND p.urunaktif = 1
        ", ['limit' => $limit]);
    }
}
```

## 💰 FİYATLANDIRMA SİSTEMİ

### Dinamik Fiyatlama
```php
// Price Calculation
class PriceManager {
    public function calculateFinalPrice($basePrice, $discounts = [], $taxes = []) {
        $finalPrice = $basePrice;
        
        // İndirimler
        foreach ($discounts as $discount) {
            if ($discount['type'] === 'percentage') {
                $finalPrice -= ($finalPrice * $discount['value'] / 100);
            } else {
                $finalPrice -= $discount['value'];
            }
        }
        
        // Vergiler
        foreach ($taxes as $tax) {
            if ($tax['type'] === 'percentage') {
                $finalPrice += ($finalPrice * $tax['value'] / 100);
            } else {
                $finalPrice += $tax['value'];
            }
        }
        
        return round($finalPrice, 2);
    }
    
    public function getPriceHistory($productID) {
        return $this->db->select("
            SELECT * FROM urun_fiyat_gecmisi 
            WHERE sayfaid = :productID 
            ORDER BY tarih DESC
        ", ['productID' => $productID]);
    }
}
```

## 🚚 KARGO ENTEGRASYONU

### Kargo Hesaplama
```php
// Cargo Calculation
class CargoManager {
    public function calculateShipping($productData, $address) {
        $cargoOptions = [];
        
        // Ürün boyutları ile desi hesaplama
        $desi = ($productData['width'] * $productData['height'] * $productData['depth']) / 3000;
        $weight = $productData['weight'];
        
        // Kargo firması seçenekleri
        $cargoCompanies = ['Aras', 'Yurtiçi', 'MNG', 'PTT'];
        
        foreach ($cargoCompanies as $company) {
            $price = $this->getCargoPrice($company, $weight, $desi, $address);
            $cargoOptions[] = [
                'company' => $company,
                'price' => $price,
                'delivery_time' => $this->getDeliveryTime($company, $address)
            ];
        }
        
        return $cargoOptions;
    }
}
```

## 📊 RAPORLAMA VE ANALİTİK

### Admin Raporlar
```php
// Product Analytics
class ProductAnalytics {
    public function getTopSellingProducts($limit = 10, $dateRange = null) {
        return $this->db->select("
            SELECT p.sayfaid, p.urunadi, COUNT(si.siparisid) as satis_adedi,
                   SUM(si.siparismiktar * si.siparisfiyat) as toplam_satis
            FROM urunozellikleri p
            LEFT JOIN siparisicerik si ON si.sayfaid = p.sayfaid
            WHERE si.siparisid IS NOT NULL
            GROUP BY p.sayfaid
            ORDER BY satis_adedi DESC
            LIMIT :limit
        ", ['limit' => $limit]);
    }
    
    public function getStockReport() {
        return $this->db->select("
            SELECT 
                COUNT(*) as toplam_urun,
                SUM(CASE WHEN urunmiktar > 0 THEN 1 ELSE 0 END) as stokta_olan,
                SUM(CASE WHEN urunmiktar = 0 THEN 1 ELSE 0 END) as tukenen,
                SUM(CASE WHEN urunmiktar <= 5 THEN 1 ELSE 0 END) as az_stok
            FROM urunozellikleri 
            WHERE urunaktif = 1 AND urunsil = 0
        ");
    }
}
```

## 🔧 PERFORMANS OPTİMİZASYONU

### Veritabanı İndexleme
```sql
-- Önemli indexler
CREATE INDEX idx_urunozellikleri_sayfaid ON urunozellikleri(sayfaid);
CREATE INDEX idx_urunozellikleri_markaid ON urunozellikleri(markaid);
CREATE INDEX idx_urunozellikleri_tedarikciid ON urunozellikleri(tedarikciid);
CREATE INDEX idx_sayfalistekategori_kategoriid ON sayfalistekategori(kategoriid);
CREATE INDEX idx_variant_properties_variant_id ON variant_properties(variant_id);
```

### Caching Stratejisi
```php
// JSON Cache kullanımı
$productDetails = $this->json->readJson(["Product/ProductDetails", $productID]);
if ($productDetails === null) {
    $productDetails = $this->getProductFromDatabase($productID);
    $this->json->writeJson(["Product/ProductDetails", $productID], $productDetails);
}
```

## 🚨 HATA YÖNETİMİ

### Validasyon Kuralları
```php
// Ürün ekleme validasyonu
function validateProduct($productData) {
    $errors = [];
    
    if (empty($productData['urunadi'])) {
        $errors[] = 'Ürün adı zorunludur';
    }
    
    if ($productData['urunsatisfiyat'] <= 0) {
        $errors[] = 'Satış fiyatı 0\'dan büyük olmalıdır';
    }
    
    if (empty($productData['urunkodu'])) {
        $errors[] = 'Ürün kodu zorunludur';
    }
    
    // Ürün kodu tekrarı kontrolü
    $existingProduct = $this->db->select("
        SELECT sayfaid FROM urunozellikleri 
        WHERE urunkodu = :code AND sayfaid != :currentID
    ", ['code' => $productData['urunkodu'], 'currentID' => $productData['sayfaid'] ?? 0]);
    
    if (!empty($existingProduct)) {
        $errors[] = 'Bu ürün kodu zaten kullanılıyor';
    }
    
    return $errors;
}
```

---

## 🎯 MODEL CONTEXT PROTOCOL USAGE

Bu dokümant, e-ticaret ürün sisteminin tüm bileşenlerini Model Context Protocol prensipleriyle organize eder:

### Context Management
- **Scope**: E-ticaret ürün yönetim sistemi
- **Dependencies**: Admin auth, database modelleri, file upload, SEO sistemi
- **Interfaces**: Product CRUD, variant management, stock control, pricing

### Tool Usage Guidelines
- **read_file**: Ürün model dosyalarını analiz etmek için
- **grep_search**: Ürün-ilgili metodları bulmak için
- **create_file**: Ürün test ve analiz dosyaları oluşturmak için
- **run_in_terminal**: Ürün veritabanı sorguları için

### Knowledge Integration
- **Database Schema**: urunozellikleri, variant_properties, kategori, sayfa, sayfalistekategori
- **Business Logic**: Ürün CRUD, varyasyon yönetimi, stok takibi, fiyatlama, kargo hesaplama
- **UI/UX Patterns**: Modüler tab sistemi, Ajax form processing, real-time preview

Bu prompt, e-ticaret ürün sisteminin her detayını kapsar ve GitHub Copilot'un bu sistemi tam olarak anlamasını sağlar.

---
*Son güncelleme: 15 Haziran 2025*
*Model Context Protocol tabanlı ürün yönetim sistem rehberi*
