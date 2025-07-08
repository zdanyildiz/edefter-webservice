# E-Defter Kullanım Sınırlaması Sistemi

Bu dokümantasyon, EDefterController.php üzerinde uygulanan kullanım sınırlaması sistemini açıklar.

## 📋 Sistem Özeti

### Sınırlamalar
- **Üye olmayanlar (visitor)**: Günde **5** XML işlemi
- **Üye olanlar (member)**: Günde **20** XML işlemi

### Çalışma Mantığı
1. Her XML işlemi öncesi kullanım sınırı kontrol edilir
2. Sınır aşılmışsa hata mesajı döner
3. Başarılı işlem sonrası sayaç artırılır
4. Kullanım bilgileri JSON yanıtta yer alır

## 🗄️ Veritabanı Yapısı

### `edefter_usage` Tablosu
```sql
CREATE TABLE `edefter_usage` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_identifier` varchar(255) NOT NULL COMMENT 'Üye ID veya Session ID',
    `user_type` enum('member','visitor') NOT NULL DEFAULT 'visitor',
    `usage_date` date NOT NULL COMMENT 'Kullanım tarihi',
    `usage_count` int(11) NOT NULL DEFAULT '0',
    `last_usage_time` datetime NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_user_date` (`user_identifier`,`usage_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
```

## 🧰 Model Sınıfı: EDefterUsage

### Temel Metodlar
- **`incrementUsage()`**: Kullanım sayacını artır
- **`getDailyUsage()`**: Günlük kullanım sayısını getir
- **`isLimitExceeded()`**: Sınır aşımını kontrol et
- **`getRemainingUsage()`**: Kalan kullanım hakkını getir
- **`getUsageInfo()`**: Detaylı kullanım bilgisi
- **`cleanOldRecords()`**: Eski kayıtları temizle

### Sabitler
```php
const VISITOR_DAILY_LIMIT = 5;   // Ziyaretçi günlük sınır
const MEMBER_DAILY_LIMIT = 20;   // Üye günlük sınır
```

## 🎮 Controller Entegrasyonu

### EDefterController.php Değişiklikleri

#### 1. Başlangıç Kontrolü
```php
// Kullanım sınırlaması kontrolü için gerekli değişkenler
$memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;
$userIdentifier = $memberStatus ? 
    $visitor['visitorIsMember']['memberID'] : 
    $visitor['visitorUniqID'];
$userType = $memberStatus ? 'member' : 'visitor';

// EDefterUsage modelini yükle
require_once MODEL . 'EDefterUsage.php';
$usageModel = new EDefterUsage($db);
```

#### 2. İşlem Öncesi Sınır Kontrolü
```php
if ($action == "process" && isset($_FILES['xml_file']) && isset($requestData['type'])) {
    
    // Kullanım sınırı kontrolü
    if ($usageModel->isLimitExceeded($userIdentifier, $userType)) {
        $usageInfo = $usageModel->getUsageInfo($userIdentifier, $userType);
        $limitMessage = $userType === 'member' ? 
            "Günlük işlem sınırınız (20) dolmuştur." : 
            "Günlük işlem sınırınız (5) dolmuştur. Üye olarak 20 işlem yapabilirsiniz.";
        
        echo json_encode([
            'success' => false, 
            'errors' => [$limitMessage],
            'usage_info' => $usageInfo
        ]);
        exit;
    }
    // ... işlem devam eder
}
```

#### 3. Başarılı İşlem Sonrası Sayaç Artırma
```php
if (!empty($results)) {
    // Başarılı işlem - kullanım sayacını artır
    $usageModel->incrementUsage($userIdentifier, $userType);
    
    // Güncel kullanım bilgilerini al
    $usageInfo = $usageModel->getUsageInfo($userIdentifier, $userType);
    
    echo json_encode([
        'success' => true, 
        'results' => $results,
        'usage_info' => $usageInfo
    ]);
}
```

## 📤 JSON Yanıt Formatları

### Sınır Aşımı (Ziyaretçi)
```json
{
    "success": false,
    "errors": ["Günlük işlem sınırınız (5) dolmuştur. Üye olarak 20 işlem yapabilirsiniz."],
    "usage_info": {
        "current_usage": 5,
        "daily_limit": 5,
        "remaining_usage": 0,
        "is_limit_exceeded": true,
        "user_type": "visitor"
    }
}
```

### Sınır Aşımı (Üye)
```json
{
    "success": false,
    "errors": ["Günlük işlem sınırınız (20) dolmuştur."],
    "usage_info": {
        "current_usage": 20,
        "daily_limit": 20,
        "remaining_usage": 0,
        "is_limit_exceeded": true,
        "user_type": "member"
    }
}
```

### Başarılı İşlem
```json
{
    "success": true,
    "results": ["<div class='result-item'>Çıktı oluşturuldu: ...</div>"],
    "usage_info": {
        "current_usage": 3,
        "daily_limit": 5,
        "remaining_usage": 2,
        "is_limit_exceeded": false,
        "user_type": "visitor"
    }
}
```

## 🧪 Test Dosyaları

### Mevcut Test Dosyaları
1. **`create_usage_table.php`**: Veritabanı tablosu oluşturma
2. **`check_table_structure.php`**: Tablo yapısı kontrolü
3. **`test_usage_model.php`**: Model sınıfı testleri
4. **`test_controller_integration.php`**: Controller entegrasyonu testleri

### Test Komutları
```powershell
# Tablo oluşturma
php Tests\EDefterUsage\create_usage_table.php

# Tablo yapısı kontrolü
php Tests\EDefterUsage\check_table_structure.php

# Model testleri
php Tests\EDefterUsage\test_usage_model.php

# Controller entegrasyon testleri
php Tests\EDefterUsage\test_controller_integration.php
```

## 🔧 Kullanıcı Tipleri ve Kimlik Belirlemesi

### Ziyaretçi (visitor)
- **Kimlik**: `$visitor['visitorUniqID']`
- **Sınır**: 5 işlem/gün
- **Mesaj**: "Günlük işlem sınırınız (5) dolmuştur. Üye olarak 20 işlem yapabilirsiniz."

### Üye (member)
- **Kimlik**: `$visitor['visitorIsMember']['memberID']`
- **Sınır**: 20 işlem/gün
- **Mesaj**: "Günlük işlem sınırınız (20) dolmuştur."

## 📊 İstatistikler ve Raporlama

### Kullanım Bilgisi Alımı
```php
$usageInfo = $usageModel->getUsageInfo($userIdentifier, $userType);
```

### Dönen Bilgiler
- `current_usage`: Günlük kullanım sayısı
- `daily_limit`: Günlük sınır
- `remaining_usage`: Kalan hak
- `is_limit_exceeded`: Sınır aşım durumu
- `user_type`: Kullanıcı tipi

## 🗑️ Bakım ve Temizlik

### Eski Kayıt Temizliği
```php
$usageModel->cleanOldRecords(); // 30 günden eski kayıtları sil
```

### Manuel Temizlik
```sql
DELETE FROM edefter_usage WHERE usage_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY);
```

## ⚡ Performans Notları

1. **Unique Index**: `(user_identifier, usage_date)` ile aynı gün tekrar ekleme engellenmiş
2. **ON DUPLICATE KEY UPDATE**: Var olan kayıt varsa sayaç artırılır
3. **Günlük Temizlik**: Eski kayıtlar performans için temizlenmeli
4. **Index Kullanımı**: `user_type` ve `usage_date` indexli

## 🔒 Güvenlik

- Kullanıcı kimlikleri session/member tabanlı
- SQL injection koruması (prepared statements)
- Günlük sınırlar sabit değerlerle korunmuş
- Sınır aşımında işlem tamamen durdurulur

---

**Oluşturulma Tarihi**: 08.07.2025  
**Son Güncelleme**: 08.07.2025  
**Test Durumu**: ✅ Tüm testler başarılı  
**Üretim Durumu**: 🚀 Üretim için hazır
