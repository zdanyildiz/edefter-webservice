# BANNER ADMİN SİSTEMİ DETAYLI PROMPT - yeni.globalpozitif.com.tr
*Banner yönetim sistemi için Model Context Protocol tabanlı rehber dokümantasyonu*

## 🏗️ SİSTEM MİMARİSİ

### Dosya Hierarşisi ve Bağımlılıklar
```
/_y/s/s/banners/AddBanner.php (Ana Admin Sayfası)
├── /_y/s/global.php (Global Admin Konfigürasyonu)
│   ├── App/Core/Config.php (Ana Konfigürasyon)
│   ├── App/Database/AdminDatabase.php (Admin DB Sınıfı)
│   ├── App/Core/AdminCasper.php (Admin Session Yöneticisi)
│   └── App/Model/Admin/AdminSession.php (Session Kontrolü)
├── App/Model/Admin/AdminLanguage.php (Dil Yönetimi)
├── App/Model/Admin/AdminBannerModel.php (Banner CRUD Modelleri)
│   ├── AdminBannerTypeModel (Banner Tipleri)
│   ├── AdminBannerLayoutModel (Banner Layout'ları)
│   ├── AdminBannerGroupModel (Banner Grupları)
│   ├── AdminBannerModel (Banner CRUD)
│   ├── AdminBannerDisplayRulesModel (Görüntüleme Kuralları)
│   ├── AdminBannerStyleModel (Banner Stilleri)
│   └── AdminBannerCreateModel (Tablo Oluşturma)
├── CSS/ (Banner Stil Dosyaları)
│   ├── TopBanner.css / TopBanner.min.css
│   ├── BottomBanner.css / BottomBanner.min.css
│   ├── Carousel.css / Carousel.min.css
│   └── [Diğer banner stil dosyaları]
└── JS/ (JavaScript Dosyaları)
    ├── BannerImage.js / BannerImage.min.js
    └── [Diğer JS dosyaları]
```

## 🔐 GÜVENLİK VE YETKİLENDİRME

### Admin Giriş Kontrolü (global.php)
```php
// 1. Config yükleme ve AdminDatabase bağlantısı
$config = new Config();
$db = new AdminDatabase($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

// 2. Admin Session kontrolü
$adminSession = new AdminSession($config->key, 3600, "/", $config->hostDomain, ...);
$adminCasper = $adminSession->getAdminCasper();

// 3. Login durumu kontrolü
if (!$loginStatus) {
    // Cookie kontrolü ve PIN ile giriş deneme
    // Başarısızsa: header("Location: /_y/s/guvenlik/giris.php");
}

// 4. Admin bilgilerini decrypt etme
$adminName = $helper->decrypt($admin["yoneticiadsoyad"], $config->key);
$adminEmail = $helper->decrypt($admin["yoneticieposta"], $config->key);

// 5. Yetki kontrolleri
$adminAuth = $admin["yoneticiyetki"]; // 0: Süper, 1: Yönetici, 2: Personel
```

### Admin Yetki Seviyeleri
- **0**: Süper Yönetici (Tam Erişim)
- **1**: Yönetici (Sınırlı Erişim)
- **2**: Personel (Temel İşlemler)

## 📊 VERİTABANI MODELLERİ

### AdminBannerTypeModel
```php
// Banner türleri (Slider, Tepe Banner, Orta Banner, vb.)
public function getAllTypes()      // Tüm banner tiplerini çek
public function getTypeById($id)   // ID'ye göre banner tipi
public function addType($type_name, $description)    // Yeni tip ekle
public function updateType($id, $type_name, $description) // Tip güncelle
public function deleteType($id)    // Tip sil
```

### AdminBannerLayoutModel  
```php
// Banner layout'ları (text, image, text_and_image)
public function getAllLayouts()            // Tüm layout'ları çek
public function getLayoutById($id)         // ID'ye göre layout
public function getLayoutsByTypeId($typeId) // Tip'e göre layout'lar
public function addLayout($layout_group, $layout_view, $type_id, ...) // Yeni layout
```

### AdminBannerGroupModel
```php
// Banner grupları (style_class, full_size, vb. ayarlarla)
public function getAllGroups()     // Tüm grupları çek
public function getGroupById($id)  // ID'ye göre grup
public function addGroup($group_name, $group_title, ...) // Grup ekle
public function updateGroup($id, $group_name, ...)       // Grup güncelle
```

### AdminBannerModel
```php
// Ana banner verileri (title, content, image, link)
public function getAllBanners()              // Tüm bannerları çek
public function getBannerById($id)           // ID'ye göre banner  
public function getBannersByGroupID($groupID) // Grup'a göre bannerlar
public function addBanner($group_id, $style_id, $title, ...) // Banner ekle
public function updateBanner($id, $group_id, $title, ...)    // Banner güncelle
```

### AdminBannerDisplayRulesModel
```php
// Banner görüntüleme kuralları (sayfa, kategori, dil bazlı)
public function getDisplayRuleByLanguageId($langCode, $typeID) // Dile göre kurallar
public function getDisplayRuleByGroupId($group_id)             // Gruba göre kurallar  
public function addDisplayRule($group_id, $typeID, $page_id, $category_id, $language_code)
```

### AdminBannerStyleModel
```php
// Banner stil detayları (renk, boyut, buton ayarları)
public function getAllStyles()     // Tüm stilleri çek
public function addStyle($banner_height_size, $background_color, ...) // Stil ekle
public function updateStyle($id, $banner_height_size, ...)           // Stil güncelle
```

## 🎨 CSS VE STİL SİSTEMİ

### Dinamik CSS Yükleme (AddBanner.php)
```php
if($bannerTypeID == 1){         // Slider
    echo '<link rel="stylesheet" href="CSS/Carousel.min.css">';
    echo '<link rel="stylesheet" href="CSS/SlideFullWidth.min.css">';
}
elseif ($bannerTypeID == 2){    // Tepe Banner
    echo '<link rel="stylesheet" href="CSS/TopBanner.min.css">';
}
elseif ($bannerTypeID == 3){    // Orta Banner
    $ortaBannerStyles = ['Carousel', 'BgImageCenterText', 'FadeFeatureCard', ...];
    foreach ($ortaBannerStyles as $styleName) {
        echo '<link rel="stylesheet" href="CSS/' . $styleName . '.min.css">';
    }
}
```

### Banner Stil Sınıfları
- **TopBanner**: `top-banner` class'ı
- **HoverCardBanner**: Hover efektli kartlar
- **BgImageCenterText**: Arkaplan resim + orta metin
- **ImageLeftBanner/ImageRightBanner**: Görsel pozisyon kontrolleri
- **Carousel**: Dönen banner sistemi

### CSS Önizleme Sistemi
```css
#previewPanel {
    border: 1px dotted #ccc;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
}

.single { width: 100%; }
.double { width: calc(50% - 10px); }
.triple { width: calc(33.33% - 10px); }
.quad { width: calc(25% - 10px); }
```

## 🖼️ BANNER OLUŞTURMA SÜRECİ

### 1. Sayfa Başlangıcı
```php
// GET parametrelerini al
$bannerGroupID = $_GET["bannerGroupID"] ?? 0;
$languageId = $_GET["languageID"] ?? $_SESSION["languageID"] ?? 1;

// Düzenleme modunda group bilgilerini yükle
if($bannerGroupID > 0) {
    $bannerGroup = $bannerGroupModel->getGroupById($bannerGroupID);
    $bannerDisplayRules = $adminBannerDisplayRulesModel->getDisplayRuleByGroupId($bannerGroupID);
    $banners = $adminBannerModel->getBannersByGroupID($bannerGroupID);
}
```

### 2. Form Alanları ve Değişkenler
```php
// Varsayılan değerler
$bannerGroupName = $bannerGroupName ?? $helper->createPassword(8,2); // Rastgele grup adı
$bannerGroupStyleClass = $bannerGroupStyleClass ?? "";              // CSS sınıfı
$bannerGroupFullSize = $bannerGroupFullSize ?? 1;                   // Full width
$bannerFullSize = $bannerFullSize ?? 0;                             // Banner genişliği
$bannerLayoutGroup = $bannerLayoutGroup ?? "";                      // Layout grubu
$bannerVisibilityStart = $bannerVisibilityStart ?? "";              // Başlangıç tarihi
$bannerVisibilityEnd = $bannerVisibilityEnd ?? "";                  // Bitiş tarihi
```

### 3. JavaScript Banner Oluşturma
```javascript
// Banner box template'i
let bannerBox = `<div class="card panel" id="card-panel-[n]">...</div>`;

// Layout'a göre farklı template'ler
let bannerBox_onlyText = `<div id="bannerContainer-[n]" class="[class]">...</div>`;
let bannerBox_onlyImage = `<div id="bannerContainer-[n]" class="[class] onlyImage">...</div>`;
let bannerBox_TextAndImage = `<div id="bannerContainer-[n]" class="[class]">...</div>`;

// Banner ekleme
$(document).on("click", "#addBannerBox", function(){
    // Yeni banner box oluştur ve ekle
});
```

## 🔄 FORM İŞLEME SÜRECİ

### Banner Grup İşlemleri
```javascript
$(document).on("submit", "#addBannerForm", function(e){
    e.preventDefault();
    
    // Form verilerini topla
    let formData = new FormData(this);
    
    // AJAX ile veri gönder
    $.ajax({
        url: 'bannerProcess.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // Başarılı işlem sonrası
        }
    });
});
```

### Banner Önizleme Sistemi
```javascript
// Önizleme panelini güncelle
function updatePreviewPanel() {
    let bannerType = $("#bannerTypeID").val();
    let bannerLayout = $("#bannerLayoutID").val();
    
    // CSS sınıflarını uygula
    applyPreviewBannerStyle(styleClass);
}

// Stil değişikliklerini dinle
$(document).on("change", "#bannerStyle", function() {
    resetPreviewBannerStyles();
    applyPreviewBannerStyle($(this).val());
});
```

## 🖼️ GÖRSEL YÖNETİMİ

### Dropzone Konfigürasyonu
```javascript
Dropzone.options.imageDropzone = {
    maxFiles: 1,
    maxFilesize: 3,
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    accept: function (file, done) {
        let imageName = $("#imageName").val();
        if (imageName === "") {
            // Hata göster
        } else {
            $("#formImageName").val(imageName);
            done();
        }
    }
};
```

### Görsel Seçim Sistemi
```javascript
$(document).on("click", ".selectImage", function () {
    let imageID = $(this).data("imageid");
    let imagePath = $(this).data("imagepath");
    
    // Hedef input'a görsel yolunu ata
    $("#bannerImage").val(imagePath);
    $("#bannerImage img").attr("src", imgRoot + imagePath);
});
```

## 🎯 BANNER TİP VE LAYOUT SİSTEMİ

### Banner Tipleri (Database)
1. **Slider** (ID: 1) - Sayfa üstünde dönen görseller
2. **Tepe Banner** (ID: 2) - Sayfanın en üst alanı  
3. **Orta Banner** (ID: 3) - Sayfanın orta kısmı
4. **Alt Banner** (ID: 4) - Sayfanın alt kısmı
5. **Popup Banner** (ID: 5) - Popup olarak çıkan karşılama
6. **Carousel Slider** (ID: 6) - Dönerek değişen birden fazla görsel
7. **Başlık Banner** (ID: 7) - Sayfa/kategori başlığı altı

### Layout Grupları (Database)
- **text**: Sadece metin içeren bannerlar
- **image**: Sadece görsel içeren bannerlar  
- **text_and_image**: Metin ve görsel bir arada
- **single/double/triple/quad/quinary**: Sütun sayısı (1-5)

### Layout View Seçenekleri
- **single**: Tek sütun (width: 100%)
- **double**: İki sütun (width: calc(50% - 10px))
- **triple**: Üç sütun (width: calc(33.33% - 10px))
- **quad**: Dört sütun (width: calc(25% - 10px))
- **quinary**: Beş sütun (width: calc(20% - 10px))

## 🎨 STİL ÖZELLEŞTİRME SİSTEMİ

### Banner Stil Parametreleri (banner_styles tablosu)
```sql
banner_height_size      -- Banner yüksekliği (px)
background_color        -- Arkaplan rengi
content_box_bg_color   -- İçerik kutusu arkaplan rengi
title_color            -- Başlık rengi
title_size             -- Başlık font boyutu (px)
content_color          -- İçerik metni rengi
content_size           -- İçerik font boyutu (px)
show_button            -- Buton göster/gizle (0/1)
button_title           -- Buton metni
button_location        -- Buton konumu (0-9)
button_background      -- Buton arkaplan rengi
button_color           -- Buton metin rengi
button_hover_background -- Buton hover arkaplan
button_hover_color     -- Buton hover metin rengi
button_size            -- Buton font boyutu (px)
```

### Buton Konumları (CSS)
```css
.location-0  /* Alt Orta (varsayılan) */
.location-1  /* Üst Sol */
.location-2  /* Üst Orta */ 
.location-3  /* Üst Sağ */
.location-4  /* Orta Sol */
.location-5  /* Orta */
.location-6  /* Orta Sağ */
.location-7  /* Alt Sol */
.location-8  /* Alt Orta */
.location-9  /* Alt Sağ */
```

## 🔍 GÖRÜNTÜLEME KURALLARI

### Display Rules Sistemi (banner_display_rules)
```sql
group_id       -- Banner grup ID'si
type_id        -- Banner tip ID'si (1-7)
page_id        -- Hangi sayfada gösterilecek (NULL = tümü)
category_id    -- Hangi kategoride gösterilecek (NULL = tümü)  
language_code  -- Hangi dilde gösterilecek (tr, en, vb.)
```

### Kural Örnekleri
```php
// Sadece ana sayfada göster
$rule = ['group_id' => 1, 'type_id' => 2, 'page_id' => 1, 'category_id' => NULL, 'language_code' => 'tr'];

// Sadece belirli kategoride göster  
$rule = ['group_id' => 2, 'type_id' => 3, 'page_id' => NULL, 'category_id' => 5, 'language_code' => 'tr'];

// Tüm sayfalarda göster
$rule = ['group_id' => 3, 'type_id' => 4, 'page_id' => NULL, 'category_id' => NULL, 'language_code' => 'tr'];
```

## 🔧 TEKNİK İMPLEMENTASYON

### MutationObserver Kullanımı
```javascript
// Görsel değişikliklerini takip et
function observeImageSrcChange(bannerCount) {
    const sourceImage = $("#card-panel-" + bannerCount + " #bannerImage-" + bannerCount);
    const targetImage = $("#previewPanel #bannerImage-" + bannerCount);
    
    const observer = new MutationObserver(function(mutationsList) {
        for (let mutation of mutationsList) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                targetImage.attr('src', sourceImage.attr('src'));
            }
        }
    });
    
    observer.observe(sourceImage[0], { attributes: true, attributeFilter: ['src'] });
}
```

### Color Picker Entegrasyonu
```javascript
// Bootstrap colorpicker kullanımı
$(".bannerBgColorContainer").colorpicker();
$(".bannerContentBoxBgColorContainer").colorpicker();
$(".bannerButtonColorContainer").colorpicker();

// Renk değişikliklerini dinle
$(document).on("change", "[id^=bannerBgColorContainer-]", function () {
    let bannerBgColor = $(this).val();
    bannerContainer.css("background-color", bannerBgColor);
});
```

### Sortable Banner Sistemi
```javascript
// Banner sıralama (jQuery UI Sortable)
$("#bannerContainer").sortable({
    handle: '.fa.fa-arrows',
    items: '.card.panel',
    opacity: 0.8,
    cursor: 'move',
    axis: 'y',
    update: function () {
        // Sıralama değiştiğinde ajax ile kaydet
    }
});
```

## 📝 FORM VERİLERİ VE VALİDASYON

### Ana Form Alanları
```html
<!-- Banner Grup Bilgileri -->
<input name="bannerGroupName" />        <!-- Grup adı -->
<input name="bannerGroupTitle" />       <!-- Grup başlığı --> 
<textarea name="bannerGroupDesc" />     <!-- Grup açıklaması -->
<select name="bannerTypeID" />          <!-- Banner tipi -->
<select name="bannerLayoutID" />        <!-- Layout seçimi -->
<input name="bannerGroupStyleClass" />  <!-- CSS sınıfı -->

<!-- Görüntüleme Ayarları -->
<input name="bannerGroupFullSize" />    <!-- Full width (0/1) -->
<input name="bannerFullSize" />         <!-- Banner full width (0/1) -->
<input name="bannerStartDate" />        <!-- Başlangıç tarihi -->
<input name="bannerEndDate" />          <!-- Bitiş tarihi -->
<input name="bannerDuration" />         <!-- Banner süresi (ms) -->

<!-- Görüntüleme Kuralları -->
<select name="bannerLanguageCode" />    <!-- Dil seçimi -->
<select name="bannerDisplayPageIDs[]" />    <!-- Sayfa seçimi (multiple) -->
<select name="bannerDisplayCategoryIDs[]" /> <!-- Kategori seçimi (multiple) -->
```

### Dinamik Banner Alanları
```html
<!-- Her banner için -->
<input name="bannerSlogan[]" />         <!-- Banner başlığı -->
<textarea name="bannerText[]" />        <!-- Banner içeriği -->
<input name="bannerImage[]" />          <!-- Banner görseli -->
<input name="bannerLink[]" />           <!-- Banner linki -->
<input name="bannerActive[]" />         <!-- Aktif/Pasif -->

<!-- Stil Ayarları -->
<input name="bannerHeightSize[]" />     <!-- Banner yüksekliği -->
<input name="bannerBgColor[]" />        <!-- Arkaplan rengi -->
<input name="bannerTitleColor[]" />     <!-- Başlık rengi -->
<input name="bannerContentColor[]" />   <!-- İçerik rengi -->
<input name="bannerButtonBgColor[]" />  <!-- Buton arkaplan -->
<input name="bannerButtonTextColor[]" /> <!-- Buton metin rengi -->
```

## 🚀 AJAX İŞLEM SÜRECİ

### Form Gönderimi
```javascript
$(document).on("submit", "#addBannerForm", function(e){
    e.preventDefault();
    
    let formData = new FormData(this);
    formData.append('action', 'saveBannerGroup');
    
    $.ajax({
        url: 'processBanner.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                // Başarı mesajı göster
                // Sayfayı yönlendir veya yenile
                window.location.href = 'AddBanner.php?bannerGroupID=' + response.groupID;
            } else {
                // Hata mesajı göster
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
});
```

### Backend İşlem Dosyası (processBanner.php)
```php
if($_POST['action'] === 'saveBannerGroup') {
    try {
        $db->beginTransaction('saveBannerGroup');
        
        // 1. Banner grup bilgilerini kaydet/güncelle
        if($bannerGroupID > 0) {
            $groupResult = $bannerGroupModel->updateGroup($bannerGroupID, ...);
        } else {
            $groupResult = $bannerGroupModel->addGroup(...);
            $bannerGroupID = $db->lastInsertId();
        }
        
        // 2. Display rules'ları temizle ve yeniden ekle
        $adminBannerDisplayRulesModel->deleteDisplayRuleByGroupID($bannerGroupID);
        foreach($displayRules as $rule) {
            $adminBannerDisplayRulesModel->addDisplayRule(...);
        }
        
        // 3. Banner'ları kaydet/güncelle
        $adminBannerModel->deleteBannersByGroupID($bannerGroupID);
        foreach($banners as $banner) {
            $adminBannerModel->addBanner(...);
        }
        
        $db->commit('saveBannerGroup');
        echo json_encode(['status' => 'success', 'groupID' => $bannerGroupID]);
        
    } catch (Exception $e) {
        $db->rollback('saveBannerGroup');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
```

## 📱 RESPONSİVE VE ÖNİZLEME

### Önizleme Panel Sistemi
```css
#previewPanel {
    border: 1px dotted #ccc;
    padding: 10px 0;
    margin: 10px 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    width: 100%;
    overflow: hidden;
    gap: 10px;
}

/* Carousel önizleme için özel stiller */
#previewPanel.carousel {
    overflow-x: auto;
    min-height: 250px;
    align-items: stretch;
}
```

### Dinamik CSS Class Uygulaması
```javascript
// Banner tipi değiştiğinde önizlemeyi güncelle
$(document).on("change", "#bannerTypeID", function(){
    let bannerTypeID = $(this).val();
    loadCSS(bannerTypeID); // Gerekli CSS dosyalarını yükle
    
    // Layout seçeneklerini güncelle
    let layouts = bannerAllLayouts[bannerTypeID] || [];
    $("#bannerLayoutID").empty();
    layouts.forEach(function(layout) {
        $("#bannerLayoutID").append(`<option value="${layout.id}">${layout.layout_name}</option>`);
    });
});

// Layout değiştiğinde açıklamayı güncelle
$(document).on("change", "#bannerLayoutID", function(){
    let selectedLayout = bannerAllLayouts[bannerTypeID].find(l => l.id == $(this).val());
    $("#layoutDescription").text(selectedLayout.description);
});
```

## 🔗 EXTERNAL RESOURCE ENTEGRASYONLARİ

### CSS/JS Kütüphaneleri
```html
<!-- Bootstrap ve Material Design -->
<link href="/_y/assets/css/theme-3/bootstrap.css" rel="stylesheet" />
<link href="/_y/assets/css/theme-3/materialadmin.css" rel="stylesheet" />
<link href="/_y/assets/css/theme-3/material-design-iconic-font.min.css" rel="stylesheet" />

<!-- Form Eklentileri -->  
<link href="/_y/assets/css/theme-3/libs/summernote/summernote.min.css" rel="stylesheet" />
<link href="/_y/assets/css/theme-3/libs/select2/select2.css" rel="stylesheet" />
<link href="/_y/assets/css/theme-3/libs/bootstrap-datepicker/datepicker3.css" rel="stylesheet" />
<link href="/_y/assets/css/theme-3/libs/bootstrap-colorpicker/bootstrap-colorpicker.css" rel="stylesheet" />

<!-- Dropzone (Dosya Yükleme) -->
<link href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css" rel="stylesheet" />
```

### JavaScript Kütüphaneleri
```html
<!-- Temel Kütüphaneler -->
<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<!-- Form Eklentileri -->
<script src="/_y/assets/js/libs/summernote/summernote.min.js"></script>
<script src="/_y/assets/js/libs/select2/select2.js"></script>
<script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<!-- Özel Banner Scripti -->
<script src="JS/BannerImage.min.js"></script>
```

## 🎯 KULLANIM SENARYOLARI

### Yeni Banner Grubu Oluşturma
1. AddBanner.php sayfasını açma (bannerGroupID olmadan)
2. Banner tipi seçimi (Slider, Tepe Banner, vb.)
3. Layout seçimi (text, image, text_and_image)
4. Grup ayarları (ad, başlık, stil sınıfı, full-width)
5. Görüntüleme kuralları (sayfa, kategori, dil)
6. Banner'ları ekleme (başlık, içerik, görsel, link)
7. Stil özelleştirme (renk, boyut, buton ayarları)
8. Önizleme kontrolü
9. Kaydetme işlemi

### Mevcut Banner Grubu Düzenleme
1. AddBanner.php?bannerGroupID=X ile açma
2. Mevcut verilerin yüklenmesi (PHP tarafında)
3. Form alanlarının doldurulması
4. Değişiklikleri yapma
5. Önizlemeyi kontrol etme
6. Güncelleme işlemi

### Banner Stilini Özelleştirme
1. Banner tipine göre stil seçimi
2. Renk seçiciler ile özelleştirme
3. Buton konumu ve stil ayarları
4. Önizlemede anlık görüntüleme
5. CSS sınıfı ile kaydetme

## 🚨 HATA YÖNETİMİ VE DEBUGGING

### Yaygın Hatalar ve Çözümleri
```javascript
// 1. Layout seçimi sonrası stil yükleme hatası
if (typeof bannerAllLayouts[bannerTypeID] === 'undefined') {
    console.error('Banner type layouts not found:', bannerTypeID);
    return;
}

// 2. Görsel yükleme hatası
$(document).on('error', 'img', function() {
    $(this).attr('src', '/_y/assets/img/header.jpg'); // Varsayılan görsel
});

// 3. Form validasyon
if ($("#bannerGroupName").val().trim() === '') {
    alert('Banner grup adı boş olamaz!');
    return false;
}
```

### Debug Araçları
```javascript
// Console log ile takip
console.log('Banner Type Changed:', bannerTypeID);
console.log('Available Layouts:', bannerAllLayouts[bannerTypeID]);

// Error handling
try {
    updatePreviewPanel();
} catch (error) {
    console.error('Preview update failed:', error);
}
```

## 📈 PERFORMANS OPTİMİZASYONU

### CSS/JS Minification
- Tüm CSS dosyaları .min.css versiyonuna sahip
- JavaScript dosyaları .min.js versiyonunda
- Dinamik yükleme ile gereksiz dosya yüklemesinin önlenmesi

### Database Query Optimizasyonu
```php
// Tek sorguda ilişkili verileri çekme
$query = "
    SELECT 
        bg.*,
        bl.layout_name,
        bl.layout_group,
        bt.type_name
    FROM banner_groups bg
    LEFT JOIN banner_layouts bl ON bg.layout_id = bl.id
    LEFT JOIN banner_types bt ON bl.type_id = bt.id
    WHERE bg.id = :id
";
```

### Lazy Loading
```javascript
// Görsel lazy loading
$('img[data-src]').each(function() {
    $(this).attr('src', $(this).data('src'));
});
```

---

## 🎯 MODEL CONTEXT PROTOCOL USAGE

Bu dokümant, banner admin sisteminin tüm bileşenlerini Model Context Protocol prensipleriyle organize eder:

### Context Management
- **Scope**: Banner admin sistemi
- **Dependencies**: Global admin sistem, database modelleri, CSS/JS kütüphaneleri
- **Interfaces**: AdminDatabase, Form işleme, AJAX communication

### Tool Usage Guidelines
- **read_file**: Model dosyalarını analiz etmek için
- **grep_search**: Belirli sınıf ve metodları bulmak için
- **create_file**: Test ve analiz dosyaları oluşturmak için
- **run_in_terminal**: Database sorguları ve testler için

### Knowledge Integration
- **Database Schema**: banner_types, banner_layouts, banner_groups, banners, banner_display_rules, banner_styles
- **Business Logic**: Banner oluşturma, düzenleme, stil özelleştirme, önizleme
- **UI/UX Patterns**: Drag&drop, color picker, real-time preview, responsive design

Bu prompt, banner admin sisteminin her detayını kapsar ve GitHub Copilot'un bu sistemi tam olarak anlamasını sağlar. Sistem karmaşıklığına rağmen, bu rehberle herhangi bir banner işlemini gerçekleştirebilir.

---
*Son güncelleme: 15 Haziran 2025*
*Model Context Protocol tabanlı banner admin sistem rehberi*
