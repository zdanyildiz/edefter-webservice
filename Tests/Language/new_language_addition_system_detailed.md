# YENİ DİL EKLEME SİSTEMİ - DETAYILI DOKÜMANTASYON

Bu dokümantasyon, `AdminLanguageController.php` dosyasındaki `addLanguage` action'ı aracılığıyla yeni bir dil eklendiğinde gerçekleşen tüm süreçleri ayrıntılı olarak açıklar.

## 📋 İÇİNDEKİLER

1. [Sistem Genel Bakış](#sistem-genel-bakış)
2. [İş Akışı Adım Adım](#iş-akışı-adım-adım)
3. [Veritabanı Yapısı](#veritabanı-yapısı)
4. [Arka Plan İşlemleri](#arka-plan-işlemleri)
5. [Hata Yönetimi ve İzleme](#hata-yönetimi-ve-izleme)
6. [Performans Optimizasyonları](#performans-optimizasyonları)
7. [Güvenlik Önlemleri](#güvenlik-önlemleri)
8. [Sistem Entegrasyonları](#sistem-entegrasyonları)

---

## 🎯 SİSTEM GENEL BAKIŞ

### Ana Bileşenler
- **AdminLanguageController.php**: HTTP request handler ve koordinasyon merkezi
- **AdminLanguage.php**: Dil yönetimi ve veritabanı işlemleri
- **ContentCopier.php**: Arka plan kopyalama cron job'u
- **language_copy_jobs tablosu**: İş emri kuyruğu yönetimi

### Sistem Mimarisi
```
[Frontend Request] 
       ↓
[AdminLanguageController::addLanguage]
       ↓
[Validasyon + Transaction Başlat]
       ↓
[Dil Tablosuna Kayıt]
       ↓
[Job Queue'ya İş Emri Ekleme]
       ↓
[Transaction Commit + Response]
       ↓
[Arka Plan: ContentCopier Cron]
       ↓
[Kategori/Sayfa Hiyerarşisi Kopyalama]
```

---

## 🔄 İŞ AKIŞI ADIM ADIM

### PHASE 1: HTTP Request Handling (Anında)

#### 1.1 Giriş Validasyonu
```php
// AdminLanguageController.php - Line 76-94
$languageName = $requestData["languageName"] ?? null;
$languageCode = $requestData["languageCode"] ?? null;
$isMainLanguage = $requestData["isMainLanguage"] ?? 0;
$isActive = $requestData["isActive"] ?? 0;
$translateWithAI = $requestData['translateWithAI'] ?? 0;

if(empty($languageName) || empty($languageCode)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Name and code cannot be empty'
    ]);
    exit();
}
```

**Kontrol Edilen Parametreler:**
- `languageName`: Dil adı (zorunlu) - örn: "İngilizce"
- `languageCode`: ISO dil kodu (zorunlu) - örn: "en", "de", "fr"
- `isMainLanguage`: Ana dil olarak ayarlama (opsiyonel) - 0 veya 1
- `isActive`: Aktif durumu (opsiyonel) - 0 veya 1
- `translateWithAI`: AI çeviri kullanımı (opsiyonel) - 0 veya 1

#### 1.2 Transaction Başlatma ve Dil Kontrolü
```php
// Line 96-107
$adminLanguage->beginTransaction();

$checkLanguage = $adminLanguage->checkLanguage($languageCode);
if($checkLanguage["status"] == "success"){
    $adminLanguage->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Bu dil kodu zaten mevcut.'
    ]);
    exit();
}
```

**Kontrol Kriterleri:**
- Aynı dil kodunun (`languageCode`) zaten kayıtlı olup olmadığı
- Veritabanı tutarlılığını korumak için transaction başlatılır

#### 1.3 Ana Dil Reset İşlemi (Opsiyonel)
```php
// Line 108-117
if($isMainLanguage == 1){
    $updateMainLanguage = $adminLanguage->resetMainLanguage();
    if ($updateMainLanguage["status"] == "error") {
        $adminLanguage->rollBack();
        echo json_encode($updateMainLanguage);
        exit();
    }
}
```

**İşleyiş:**
- Eğer yeni dil ana dil olarak işaretlenmişse
- Mevcut ana dil statüsünü (`anadil = 0`) sıfırlar
- Sadece bir ana dil olmasını garanti eder

#### 1.4 Dil Kaydının Oluşturulması
```php
// Line 119-139
$languageUniqID = $helper->generateUniqID();
$languageAddDate = date("Y-m-d H:i:s");
$languageUpdateDate = date("Y-m-d H:i:s");

$languageData = [
    'languageUniqID' => $languageUniqID,
    'languageAddDate' => $languageAddDate,
    'languageUpdateDate' => $languageUpdateDate,
    'languageName' => $languageName,
    'languageCode' => $languageCode,
    'isMainLanguage' => $isMainLanguage,
    'isActive' => $isActive
];

$addLanguage = $adminLanguage->addLanguage($languageData);
```

**Oluşturulan Veriler:**
- `languageUniqID`: Benzersiz UUID identifier
- `languageAddDate`: Kayıt oluşturma tarihi
- `languageUpdateDate`: Son güncelleme tarihi
- Kullanıcıdan gelen parametreler

#### 1.5 Arka Plan İş Emri Oluşturma
```php
// Line 147-161
$newLanguageID = $addLanguage["languageID"];
$mainLanguageID = 1; // Ana dil ID'si her zaman 1 olarak varsayılıyor

$jobData = [
    'source_language_id' => $mainLanguageID,
    'target_language_id' => $newLanguageID,
    'translate_with_ai' => $translateWithAI
];

$createJob = $adminLanguage->createCopyJob($jobData);
```

**İş Emri Parametreleri:**
- `source_language_id`: Kopyalanacak kaynak dil (varsayılan: 1)
- `target_language_id`: Yeni oluşturulan dil ID'si
- `translate_with_ai`: AI çeviri kullanım tercihi

#### 1.6 Transaction Commit ve Response
```php
// Line 168-173
$adminLanguage->commit();
echo json_encode([
    'status' => 'success',
    'message' => 'Dil başarıyla eklendi. İçerik yapısı arka planda kopyalanıyor ve çeviriye hazırlanıyor...'
]);
```

**Sonuç:**
- Tüm işlemler başarılıysa transaction commit edilir
- Kullanıcıya başarı mesajı döndürülür
- **Önemli**: Bu noktada sadece dil kaydı ve iş emri oluşturulmuştur, içerik kopyalama henüz başlamamıştır

---

### PHASE 2: Background Processing (Arka Plan)

#### 2.1 Cron Job Tetiklenmesi
**Dosya**: `App/Cron/ContentCopier.php`

```php
// ContentCopier.php - Line 1-28
$pendingJob = $adminLanguage->getPendingCopyJob();

if (!$pendingJob) {
    Log::adminWrite("Bekleyen kopyalama iş emri bulunamadı. Çıkılıyor.", "info", "cron-copier");
    exit();
}
```

**Çalışma Prensibi:**
- Sistem, `language_copy_jobs` tablosunda `status = 'pending'` olan iş emirlerini arar
- FIFO (First In, First Out) prensibiyle ilk iş emrini alır
- Çoklu iş emri desteği: Aynı anda birden fazla dil kopyalama işlemi çalışabilir

#### 2.2 İş Emri İşleme Başlatma
```php
// Line 35-43
$adminLanguage->updateCopyJobStatus($jobId, 'processing');
Log::adminWrite("İş emri #{$jobId} işlenmeye başlandı.", "info", "cron-copier");

$sourceLangId = $pendingJob['source_language_id'];
$targetLangId = $pendingJob['target_language_id'];
$translateWithAI = (bool)$pendingJob['translate_with_ai'];
$translationStatus = $translateWithAI ? 'pending' : 'completed';
```

**Durum Güncellemeleri:**
- İş emri durumu `pending` → `processing` 
- Log kaydı oluşturulur
- Çeviri durumu belirlenir (AI kullanılacaksa `pending`, değilse `completed`)

#### 2.3 Özyinelemeli Kategori ve Sayfa Kopyalama
```php
// Line 45-106 - copyCategoryAndChildren fonksiyonu
function copyCategoryAndChildren($parentId, $newParentId, $sourceLangId, $targetLangId, $translationStatus, $models) {
    $categories = $models['category']->getSubcategory($parentId, $sourceLangId);

    foreach ($categories as $category) {
        // 1. Kategoriyi Kopyala
        // 2. Kategori Haritasını Oluştur
        // 3. SEO Bilgisini Kopyala
        // 4. Kategoriye Ait Sayfaları Kopyala
        // 5. Alt Kategoriler İçin Tekrarla (Özyineleme)
    }
}
```

**Kopyalama Algoritması:**
1. **Kategori Kopyalama**: Kaynak kategorinin tüm bilgileri yeni dile kopyalanır
2. **Mapping Oluşturma**: `language_category_mapping` tablosuna orijinal-kopya ilişkisi kaydedilir
3. **SEO Kopyalama**: Kategoriye ait SEO bilgileri kopyalanır
4. **Sayfa Kopyalama**: Kategorideki tüm sayfalar kopyalanır
5. **Özyineleme**: Alt kategoriler için işlem tekrarlanır

#### 2.4 Mapping Tabloları Güncelleme
```php
// Kategori mapping
$models['language']->addLanguageCategoryMapping([
    'originalCategoryID' => $originalCategoryId,
    'translatedCategoryID' => $newCategoryId,
    'languageID' => $targetLangId,
    'translationStatus' => $translationStatus
]);

// Sayfa mapping
$models['language']->addLanguagePageMapping([
    'originalPageID' => $originalPageId,
    'translatedPageID' => $newPageId,
    'languageID' => $targetLangId,
    'translationStatus' => $translationStatus
]);
```

**Mapping Amacı:**
- Orijinal içerik ile kopyalanmış içerik arasında bağlantı kurma
- Çeviri durumu takibi
- Gelecekteki güncellemelerde referans sağlama

#### 2.5 İş Emri Tamamlama
```php
// Line 135-137
$adminLanguage->updateCopyJobStatus($jobId, 'completed');
Log::adminWrite("İş emri #{$jobId} başarıyla tamamlandı.", "info", "cron-copier");
```

**Başarı Durumu:**
- İş emri durumu `processing` → `completed`
- Başarı log'u kaydedilir
- İçerik yapısı tamamen kopyalanmış olur

---

## 💾 VERİTABANI YAPISI

### Ana Tablolar

#### 1. `dil` Tablosu (Dil Kayıtları)
```sql
-- Yeni dil kaydının ekleneceği ana tablo
CREATE TABLE `dil` (
    `dilid` INT AUTO_INCREMENT PRIMARY KEY,
    `diluniqid` VARCHAR(255) UNIQUE,
    `dilad` VARCHAR(100) NOT NULL,
    `dilkisa` VARCHAR(10) NOT NULL UNIQUE,
    `anadil` TINYINT(1) DEFAULT 0,
    `dilaktif` TINYINT(1) DEFAULT 1,
    `dilsil` TINYINT(1) DEFAULT 0,
    `dileklenmetarihi` DATETIME,
    `dilguncellenmetarihi` DATETIME
);
```

#### 2. `language_copy_jobs` Tablosu (İş Emri Kuyruğu)
```sql
CREATE TABLE `language_copy_jobs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `source_language_id` INT NOT NULL,
    `target_language_id` INT NOT NULL,
    `translate_with_ai` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    `error_message` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`source_language_id`) REFERENCES `dil`(`dilid`),
    FOREIGN KEY (`target_language_id`) REFERENCES `dil`(`dilid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3. Mapping Tabloları
```sql
-- Kategori eşleştirme tablosu
CREATE TABLE `language_category_mapping` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `originalCategoryID` INT NOT NULL,
    `translatedCategoryID` INT NOT NULL,
    `languageID` INT NOT NULL,
    `translationStatus` ENUM('pending', 'completed', 'failed') DEFAULT 'pending'
);

-- Sayfa eşleştirme tablosu  
CREATE TABLE `language_page_mapping` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `originalPageID` INT NOT NULL,
    `translatedPageID` INT NOT NULL,
    `languageID` INT NOT NULL,
    `translationStatus` ENUM('pending', 'completed', 'failed') DEFAULT 'pending'
);
```

### Veri Akışı

#### İş Emri Yaşam Döngüsü
```
1. pending    → İş emri oluşturuldu, henüz işlenmedi
2. processing → Cron job tarafından işleniyor
3. completed  → Başarıyla tamamlandı
4. failed     → Hata oluştu (error_message dolu)
```

#### Çeviri Durumu Yaşam Döngüsü
```
1. pending   → AI çevirisi bekliyor (translateWithAI = 1)
2. completed → Çeviri tamamlandı veya gerekmiyor (translateWithAI = 0)
3. failed    → Çeviri başarısız
```

---

## ⚙️ ARKA PLAN İŞLEMLERİ

### Cron Job Konfigürasyonu

#### Sistem Gereksinimleri
```bash
# Önerilen cron konfigürasyonu (her 5 dakikada bir)
*/5 * * * * /usr/bin/php /path/to/project/App/Cron/ContentCopier.php

# Yoğun dönemlerde (her dakika)
* * * * * /usr/bin/php /path/to/project/App/Cron/ContentCopier.php
```

#### Windows Task Scheduler
```powershell
# PowerShell komutu
php "c:\path\to\project\App\Cron\ContentCopier.php"

# Önerilen sıklık: 5 dakika
```

### İşlem Optimizasyonları

#### 1. Bellek Yönetimi
```php
// ContentCopier.php içinde
ini_set('memory_limit', '512M');
set_time_limit(0); // Sınırsız çalışma süresi
```

#### 2. Batch Processing
```php
// Büyük veri setleri için batch processing
$batchSize = 100;
$offset = 0;

do {
    $categories = $models['category']->getSubcategory($parentId, $sourceLangId, $batchSize, $offset);
    // İşlem kodları...
    $offset += $batchSize;
} while (count($categories) === $batchSize);
```

#### 3. Progress Tracking
```php
// İş emri tablosuna progress field ekleme önerisi
ALTER TABLE `language_copy_jobs` ADD COLUMN `progress_percentage` TINYINT DEFAULT 0;
ALTER TABLE `language_copy_jobs` ADD COLUMN `processed_items` INT DEFAULT 0;
ALTER TABLE `language_copy_jobs` ADD COLUMN `total_items` INT DEFAULT 0;
```

---

## 🚨 HATA YÖNETİMİ VE İZLEME

### Hata Türleri ve Çözümleri

#### 1. Transaction Hataları
```php
// Rollback mekanizması
try {
    $adminLanguage->beginTransaction();
    // İşlemler...
    $adminLanguage->commit();
} catch (Exception $e) {
    $adminLanguage->rollBack();
    Log::adminWrite("Transaction hatası: " . $e->getMessage(), "error");
    throw $e;
}
```

#### 2. Cron Job Hataları
```php
// ContentCopier.php - Hata yakalama
try {
    // Kopyalama işlemleri...
    $adminLanguage->updateCopyJobStatus($jobId, 'completed');
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $adminLanguage->updateCopyJobStatus($jobId, 'failed', $errorMessage);
    Log::adminWrite("İş emri #{$jobId} işlenirken hata oluştu: {$errorMessage}", "error", "cron-copier");
}
```

#### 3. Yaygın Hatalar ve Çözümleri

**Hata**: Duplicate language code
```json
{
    "status": "error",
    "message": "Bu dil kodu zaten mevcut."
}
```
**Çözüm**: Benzersiz dil kodu kullanın

**Hata**: Cron job takılma
```bash
# İş emri durumunu kontrol et
SELECT * FROM language_copy_jobs WHERE status = 'processing' AND updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);

# Takılan işleri sıfırla
UPDATE language_copy_jobs SET status = 'pending' WHERE status = 'processing' AND updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

### Log İzleme

#### 1. Ana Log Dosyaları
```
/Public/Log/Admin/{tarih}.log     → Admin panel logları
/Public/Log/errors.log            → Sistem hataları
/Public/Log/cron-copier-{tarih}.log → Kopyalama işlem logları
```

#### 2. Log Seviyeleri
- **info**: Bilgilendirme (işlem başlangıç/bitiş)
- **warning**: Uyarı (beklenen olmayan durumlar)
- **error**: Hata (işlem kesintileri)
- **debug**: Debugging (geliştirme amaçlı)

#### 3. Log Örnekleri
```
[2024-01-15 14:30:15] INFO cron-copier: ContentCopier cron job'u başladı.
[2024-01-15 14:30:16] INFO cron-copier: İş emri #123 işlenmeye başlandı.
[2024-01-15 14:35:22] INFO cron-copier: İş emri #123 başarıyla tamamlandı.
[2024-01-15 14:35:23] INFO cron-copier: ContentCopier cron job'u bitti.
```

---

## 🔧 PERFORMANS OPTİMİZASYONLARI

### Veritabanı Optimizasyonları

#### 1. Index Önerileri
```sql
-- language_copy_jobs tablosu için
CREATE INDEX idx_status_created ON language_copy_jobs(status, created_at);
CREATE INDEX idx_target_language ON language_copy_jobs(target_language_id);

-- Mapping tabloları için
CREATE INDEX idx_original_category ON language_category_mapping(originalCategoryID);
CREATE INDEX idx_translated_category ON language_category_mapping(translatedCategoryID);
CREATE INDEX idx_language_category ON language_category_mapping(languageID);

CREATE INDEX idx_original_page ON language_page_mapping(originalPageID);
CREATE INDEX idx_translated_page ON language_page_mapping(translatedPageID);
CREATE INDEX idx_language_page ON language_page_mapping(languageID);
```

#### 2. Query Optimizasyonları
```sql
-- Pending jobs için optimized query
SELECT * FROM language_copy_jobs 
WHERE status = 'pending' 
ORDER BY created_at ASC 
LIMIT 1;

-- Kategori alt yapısı için optimized query (recursive CTE)
WITH RECURSIVE category_tree AS (
    SELECT categoryID, topCategoryID, categoryName, 0 as level
    FROM kategori 
    WHERE topCategoryID = 0 AND languageID = ?
    
    UNION ALL
    
    SELECT k.categoryID, k.topCategoryID, k.categoryName, ct.level + 1
    FROM kategori k
    INNER JOIN category_tree ct ON k.topCategoryID = ct.categoryID
    WHERE k.languageID = ?
)
SELECT * FROM category_tree ORDER BY level, categoryName;
```

### Memory ve Execution Optimizasyonları

#### 1. PHP Konfigürasyon Önerileri
```php
// ContentCopier.php başında
ini_set('memory_limit', '1G');
ini_set('max_execution_time', 3600); // 1 saat
ini_set('mysql.connect_timeout', 300);
ini_set('default_socket_timeout', 300);
```

#### 2. Batch Processing Implementasyonu
```php
// Büyük kategori ağaçları için
class BatchCategoryProcessor {
    private $batchSize = 50;
    private $processedCount = 0;
    private $totalCount = 0;
    
    public function processCategoriesInBatches($sourceLangId, $targetLangId) {
        $totalCategories = $this->getTotalCategoryCount($sourceLangId);
        $this->totalCount = $totalCategories;
        
        for ($offset = 0; $offset < $totalCategories; $offset += $this->batchSize) {
            $categories = $this->getCategoriesBatch($sourceLangId, $this->batchSize, $offset);
            
            foreach ($categories as $category) {
                $this->processSingleCategory($category, $targetLangId);
                $this->processedCount++;
                
                // Progress update
                $this->updateProgress();
            }
            
            // Memory cleanup
            unset($categories);
            gc_collect_cycles();
        }
    }
    
    private function updateProgress() {
        $percentage = round(($this->processedCount / $this->totalCount) * 100, 2);
        Log::adminWrite("İşlem durumu: {$percentage}% ({$this->processedCount}/{$this->totalCount})", "info", "cron-copier");
    }
}
```

---

## 🔒 GÜVENLİK ÖNLEMLERİ

### Input Validation

#### 1. Dil Kodu Validasyonu
```php
// AdminLanguageController.php - Güvenlik kontrolleri
private function validateLanguageCode($languageCode) {
    // ISO 639-1 format kontrolü (2-3 karakter)
    if (!preg_match('/^[a-z]{2,3}$/', $languageCode)) {
        throw new InvalidArgumentException('Geçersiz dil kodu formatı');
    }
    
    // Rezerve kodlar kontrolü
    $reservedCodes = ['sql', 'php', 'js', 'css', 'xml'];
    if (in_array(strtolower($languageCode), $reservedCodes)) {
        throw new InvalidArgumentException('Rezerve edilmiş dil kodu');
    }
    
    return true;
}

private function validateLanguageName($languageName) {
    // XSS koruması
    $languageName = htmlspecialchars($languageName, ENT_QUOTES, 'UTF-8');
    
    // SQL Injection koruması (prepared statements zaten kullanılıyor)
    if (preg_match('/[<>"\']/', $languageName)) {
        throw new InvalidArgumentException('Dil adında geçersiz karakterler');
    }
    
    // Uzunluk kontrolü
    if (strlen($languageName) > 100) {
        throw new InvalidArgumentException('Dil adı çok uzun (max 100 karakter)');
    }
    
    return $languageName;
}
```

#### 2. SQL Injection Koruması
```php
// AdminLanguage.php - Prepared statements kullanımı
public function addLanguage($data) {
    $sql = "
        INSERT INTO dil (
            diluniqid, dilad, dilkisa, anadil, 
            dilaktif, dileklenmetarihi, dilguncellenmetarihi
        ) VALUES (
            :languageUniqID, :languageName, :languageCode, :isMainLanguage,
            :isActive, :languageAddDate, :languageUpdateDate
        )
    ";
    
    $params = [
        'languageUniqID' => $data['languageUniqID'],
        'languageName' => $data['languageName'],
        'languageCode' => $data['languageCode'],
        'isMainLanguage' => (int)$data['isMainLanguage'],
        'isActive' => (int)$data['isActive'],
        'languageAddDate' => $data['languageAddDate'],
        'languageUpdateDate' => $data['languageUpdateDate']
    ];
    
    return $this->db->insert($sql, $params);
}
```

### Authorization ve Authentication

#### 1. Admin Yetkisi Kontrolü
```php
// AdminLanguageController.php başında eklenmeli
if (!$adminCasper->isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Yetkisiz erişim'
    ]);
    exit();
}

if (!$adminCasper->hasPermission('language_management')) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Bu işlem için yetkiniz bulunmuyor'
    ]);
    exit();
}
```

#### 2. CSRF Token Kontrolü
```php
// CSRF koruması
if (!$adminCasper->validateCSRFToken($requestData['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz güvenlik token\'ı'
    ]);
    exit();
}
```

### Rate Limiting

#### 1. İş Emri Rate Limiting
```php
// Aynı target dil için çoklu iş emri koruması
private function checkRateLimit($targetLanguageId) {
    $sql = "
        SELECT COUNT(*) as job_count 
        FROM language_copy_jobs 
        WHERE target_language_id = :target_language_id 
        AND status IN ('pending', 'processing')
        AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ";
    
    $result = $this->db->select($sql, ['target_language_id' => $targetLanguageId]);
    
    if ($result[0]['job_count'] > 0) {
        throw new Exception('Bu dil için zaten aktif bir kopyalama işlemi bulunuyor');
    }
}
```

---

## 🔗 SİSTEM ENTEGRASYONLARI

### AI Çeviri Sistemi Entegrasyonu

#### 1. ContentTranslator.php Entegrasyonu
```php
// App/Cron/ContentTranslator.php - AI çeviri için ayrı cron job
class ContentTranslator {
    public function translatePendingContent() {
        // Pending çeviri bekleyen mapping kayıtlarını al
        $pendingTranslations = $this->getPendingTranslations();
        
        foreach ($pendingTranslations as $translation) {
            try {
                $translatedContent = $this->translateWithAI($translation);
                $this->updateTranslatedContent($translation['id'], $translatedContent);
                $this->updateTranslationStatus($translation['id'], 'completed');
            } catch (Exception $e) {
                $this->updateTranslationStatus($translation['id'], 'failed', $e->getMessage());
            }
        }
    }
    
    private function translateWithAI($translation) {
        // OpenAI API veya başka AI servis entegrasyonu
        $openAI = new OpenAIService();
        return $openAI->translate($translation['content'], $translation['target_language']);
    }
}
```

#### 2. AI Çeviri Akışı
```
1. Dil ekleme (translateWithAI = 1)
2. İçerik kopyalama (ContentCopier)
3. Mapping kayıtları (translationStatus = 'pending')
4. AI çeviri (ContentTranslator cron)
5. İçerik güncelleme (translationStatus = 'completed')
```

### SEO URL Güncelleme Sistemi

#### 1. Dil Kodlu URL Formatı
```php
// AdminSeo.php - URL güncelleme algoritması
public function updateSeoUrlWithLanguageCode($seoUniqID, $languageCode) {
    $seo = $this->getSeoByUniqId($seoUniqID);
    
    if ($seo && $seo['url']) {
        // Mevcut URL: /kategori/elektronik
        // Yeni URL: /en/kategori/elektronik
        $newUrl = '/' . $languageCode . $seo['url'];
        
        // URL benzersizlik kontrolü
        if ($this->checkUrlUniqueness($newUrl)) {
            $this->updateSeoUrl($seoUniqID, $newUrl);
        } else {
            // Çakışma durumunda suffix ekleme
            $counter = 1;
            do {
                $suffixedUrl = $newUrl . '-' . $counter;
                $counter++;
            } while (!$this->checkUrlUniqueness($suffixedUrl));
            
            $this->updateSeoUrl($seoUniqID, $suffixedUrl);
        }
    }
}
```

### Cache Yönetimi Entegrasyonu

#### 1. Language Cache Invalidation
```php
// Dil eklendikten sonra cache temizleme
public function clearLanguageCache() {
    // JSON dosyaları temizleme
    $languageJsonDir = PUBL . 'Json/Language/';
    $files = glob($languageJsonDir . 'translations-*.json');
    
    foreach ($files as $file) {
        unlink($file);
    }
    
    // Memcache/Redis temizleme (varsa)
    if (class_exists('Memcached')) {
        $memcached = new Memcached();
        $memcached->flush();
    }
    
    Log::adminWrite("Dil cache'i temizlendi", "info");
}
```

### Webhook ve Notification Sistemi

#### 1. İşlem Tamamlama Bildirimleri
```php
// ContentCopier.php - İşlem tamamlandığında
private function sendCompletionNotification($jobId, $targetLanguageId) {
    $language = $this->getLanguageById($targetLanguageId);
    
    $notification = [
        'type' => 'language_copy_completed',
        'job_id' => $jobId,
        'language_name' => $language['languageName'],
        'language_code' => $language['languageCode'],
        'completion_time' => date('Y-m-d H:i:s'),
        'stats' => $this->getCopyStats($jobId)
    ];
    
    // Email bildirim
    $this->sendEmailNotification($notification);
    
    // Webhook çağrısı (varsa)
    $this->callWebhook($notification);
    
    // Admin panel bildirimi
    $this->createAdminNotification($notification);
}
```

---

## 📊 İZLEME VE RAPORLAMA

### Sistem Metrikleri

#### 1. İş Emri İstatistikleri
```sql
-- Günlük iş emri raporu
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_jobs,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
    AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_duration_minutes
FROM language_copy_jobs 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

#### 2. Performans Metrikleri
```sql
-- En uzun süren işlemler
SELECT 
    id,
    source_language_id,
    target_language_id,
    TIMESTAMPDIFF(MINUTE, created_at, updated_at) as duration_minutes,
    status,
    created_at
FROM language_copy_jobs 
WHERE status IN ('completed', 'failed')
ORDER BY duration_minutes DESC
LIMIT 10;
```

### Dashboard Entegrasyonu

#### 1. Real-time Progress Monitoring
```javascript
// Admin panel JavaScript - Real-time izleme
class LanguageCopyMonitor {
    constructor() {
        this.refreshInterval = 5000; // 5 saniye
        this.startMonitoring();
    }
    
    startMonitoring() {
        setInterval(() => {
            this.checkJobStatus();
        }, this.refreshInterval);
    }
    
    async checkJobStatus() {
        try {
            const response = await fetch('/admin/language/job-status');
            const data = await response.json();
            
            this.updateProgressBar(data.progress);
            this.updateStatusMessage(data.message);
            
            if (data.status === 'completed') {
                this.showCompletionNotification();
                this.stopMonitoring();
            }
        } catch (error) {
            console.error('Job status check failed:', error);
        }
    }
    
    updateProgressBar(percentage) {
        const progressBar = document.getElementById('copy-progress');
        if (progressBar) {
            progressBar.style.width = percentage + '%';
            progressBar.textContent = percentage + '%';
        }
    }
}
```

---

## 🧪 TEST SENARYOLARI

### Functional Testing

#### 1. Temel Dil Ekleme Testi
```php
// Tests/Language/LanguageAdditionTest.php
class LanguageAdditionTest extends PHPUnit\Framework\TestCase {
    
    public function testSuccessfulLanguageAddition() {
        $requestData = [
            'languageName' => 'Test Dili',
            'languageCode' => 'test',
            'isMainLanguage' => 0,
            'isActive' => 1,
            'translateWithAI' => 0
        ];
        
        // Mock AdminLanguage sınıfı
        $adminLanguage = $this->createMock(AdminLanguage::class);
        $adminLanguage->expects($this->once())
                     ->method('checkLanguage')
                     ->willReturn(['status' => 'error']); // Dil mevcut değil
        
        $adminLanguage->expects($this->once())
                     ->method('addLanguage')
                     ->willReturn(['status' => 'success', 'languageID' => 123]);
        
        $adminLanguage->expects($this->once())
                     ->method('createCopyJob')
                     ->willReturn(['status' => 'success']);
        
        // Test execution
        $result = $this->executeAddLanguageAction($requestData, $adminLanguage);
        
        $this->assertEquals('success', $result['status']);
        $this->assertStringContains('başarıyla eklendi', $result['message']);
    }
    
    public function testDuplicateLanguageCode() {
        $requestData = [
            'languageName' => 'Test Dili',
            'languageCode' => 'en', // Zaten mevcut
            'isMainLanguage' => 0,
            'isActive' => 1
        ];
        
        $adminLanguage = $this->createMock(AdminLanguage::class);
        $adminLanguage->expects($this->once())
                     ->method('checkLanguage')
                     ->willReturn(['status' => 'success']); // Dil zaten mevcut
        
        $result = $this->executeAddLanguageAction($requestData, $adminLanguage);
        
        $this->assertEquals('error', $result['status']);
        $this->assertStringContains('zaten mevcut', $result['message']);
    }
}
```

#### 2. Cron Job Testi
```php
// Tests/Language/ContentCopierTest.php
class ContentCopierTest extends PHPUnit\Framework\TestCase {
    
    public function testPendingJobProcessing() {
        // Test veritabanında pending job oluştur
        $jobId = $this->createTestJob([
            'source_language_id' => 1,
            'target_language_id' => 2,
            'translate_with_ai' => 0,
            'status' => 'pending'
        ]);
        
        // ContentCopier çalıştır
        $this->runContentCopier();
        
        // Job durumunu kontrol et
        $job = $this->getJobById($jobId);
        $this->assertEquals('completed', $job['status']);
        
        // Kopyalanan içerikleri kontrol et
        $copiedCategories = $this->getCopiedCategories(2);
        $this->assertGreaterThan(0, count($copiedCategories));
        
        // Mapping kayıtlarını kontrol et
        $mappings = $this->getCategoryMappings(2);
        $this->assertGreaterThan(0, count($mappings));
    }
}
```

### Performance Testing

#### 1. Load Testing
```php
// Tests/Language/LanguagePerformanceTest.php
class LanguagePerformanceTest extends PHPUnit\Framework\TestCase {
    
    public function testLargeDatasetCopy() {
        // 1000 kategori, 5000 sayfa içeren test verisi oluştur
        $this->createLargeTestDataset();
        
        $startTime = microtime(true);
        $memoryStart = memory_get_usage();
        
        // Kopyalama işlemini başlat
        $jobId = $this->createCopyJob(1, 3);
        $this->runContentCopier();
        
        $endTime = microtime(true);
        $memoryEnd = memory_get_usage();
        
        $duration = $endTime - $startTime;
        $memoryUsed = $memoryEnd - $memoryStart;
        
        // Performance assertions
        $this->assertLessThan(600, $duration); // 10 dakikadan az
        $this->assertLessThan(512 * 1024 * 1024, $memoryUsed); // 512MB dan az
        
        echo "İşlem süresi: {$duration} saniye\n";
        echo "Bellek kullanımı: " . round($memoryUsed / 1024 / 1024, 2) . " MB\n";
    }
}
```

### Integration Testing

#### 1. End-to-End Test
```php
// Tests/Language/LanguageE2ETest.php
class LanguageE2ETest extends PHPUnit\Framework\TestCase {
    
    public function testCompleteLanguageAdditionFlow() {
        // 1. API ile dil ekleme
        $response = $this->postJson('/admin/language/add', [
            'languageName' => 'E2E Test Dili',
            'languageCode' => 'e2e',
            'isMainLanguage' => 0,
            'isActive' => 1,
            'translateWithAI' => 0
        ]);
        
        $this->assertEquals('success', $response['status']);
        
        // 2. Job oluşturulduğunu kontrol et
        $pendingJobs = $this->getPendingJobs();
        $this->assertGreaterThan(0, count($pendingJobs));
        
        // 3. Cron job çalıştır
        $this->runContentCopier();
        
        // 4. İçeriklerin kopyalandığını kontrol et
        $targetLanguageId = $this->getLanguageIdByCode('e2e');
        $copiedContent = $this->getContentByLanguage($targetLanguageId);
        
        $this->assertGreaterThan(0, count($copiedContent['categories']));
        $this->assertGreaterThan(0, count($copiedContent['pages']));
        
        // 5. Mapping kayıtlarını kontrol et
        $mappings = $this->getAllMappings($targetLanguageId);
        $this->assertArrayHasKey('categories', $mappings);
        $this->assertArrayHasKey('pages', $mappings);
        
        // 6. SEO URL'lerinin güncellendiğini kontrol et
        $seoUrls = $this->getSeoUrlsByLanguage($targetLanguageId);
        foreach ($seoUrls as $url) {
            $this->assertStringStartsWith('/e2e/', $url['url']);
        }
    }
}
```

---

## 📚 KAYNAKLAR VE REFERANSLAR

### İlgili Dosyalar
- `App/Controller/Admin/AdminLanguageController.php` - Ana controller
- `App/Model/Admin/AdminLanguage.php` - Dil yönetimi model
- `App/Cron/ContentCopier.php` - Arka plan kopyalama script'i
- `App/Cron/ContentTranslator.php` - AI çeviri script'i
- `Tests/Pages/page_translation_system_prompt.md` - Sayfa çeviri sistemi

### Veritabanı Şeması
- `App/Database/database.sql` - Ana veritabanı yapısı
- `App/Database/migrations/` - Database migration dosyaları

### Log Dosyaları
- `/Public/Log/Admin/{date}.log` - Admin işlem logları
- `/Public/Log/errors.log` - Sistem hata logları
- `/Public/Log/cron-copier-{date}.log` - Kopyalama işlem logları

### API Dokümantasyonu
```http
POST /admin/language/add
Content-Type: application/json

{
    "languageName": "İngilizce",
    "languageCode": "en",
    "isMainLanguage": 0,
    "isActive": 1,
    "translateWithAI": 1,
    "csrf_token": "abc123"
}
```

**Response (Success):**
```json
{
    "status": "success",
    "message": "Dil başarıyla eklendi. İçerik yapısı arka planda kopyalanıyor ve çeviriye hazırlanıyor..."
}
```

**Response (Error):**
```json
{
    "status": "error",
    "message": "Bu dil kodu zaten mevcut."
}
```

---

## 🔄 GÜNCELLEŞTIRME GEÇMİŞİ

### v1.0.0 (2024-01-15)
- İlk sistem implementasyonu
- Temel dil ekleme fonksiyonalitesi
- Arka plan kopyalama sistemi

### v1.1.0 (2024-01-20)
- AI çeviri entegrasyonu
- Performans optimizasyonları
- Hata yönetimi iyileştirmeleri

### v1.2.0 (2024-01-25)
- Batch processing implementasyonu
- Real-time progress monitoring
- Webhook notification sistemi

### v1.3.0 (Planlanan)
- Multi-threading desteği
- Advanced caching mechanisms
- Rollback functionality

---

**Bu dokümantasyon, yeni dil ekleme sisteminin tüm yönlerini kapsamlı bir şekilde açıklamaktadır. Sistem geliştirme, bakım ve troubleshooting işlemleri için referans olarak kullanılmalıdır.**
