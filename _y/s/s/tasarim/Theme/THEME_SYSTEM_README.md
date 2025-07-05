# Tema Yönetim Sistemi Detaylı Açıklama

Bu belge, projenin tema yönetim sisteminin ana bileşenlerini, iş akışını ve yapılan iyileştirmeleri detaylı bir şekilde açıklamaktadır.

## 1. Sistemin Amacı

Bu tema yönetim sistemi, yöneticilerin sitenin görsel tasarımını (renkler, fontlar, boşluklar, sınırlar vb.) bir arayüz üzerinden kolayca özelleştirmesini sağlar. Yapılan değişiklikler dinamik olarak bir CSS dosyasına dönüştürülür ve sitenin ön yüzünde anında uygulanır. Bu sayede, kod bilgisi olmayan kullanıcılar bile sitenin görünümünü kişiselleştirebilir.

## 2. Ana Bileşenler

Tema yönetim sistemi, frontend ve backend arasında koordineli çalışan birden fazla dosyadan oluşur:

### 2.1. `_y/s/s/tasarim/Theme.php` (Ana Tema Düzenleyici Sayfası)

*   **Rolü:** Tema düzenleyici arayüzünü (HTML yapısı, sekmeler, form alanları) oluşturan ana PHP dosyasıdır.
*   **İçeriği:**
    *   Diğer tema sekme dosyalarını (`colors.php`, `header.php` vb.) `include` eder.
    *   Dil seçimi, logo bilgileri gibi dinamik verileri PHP ile çeker.
    *   `sanitizeColorValue` ve `sanitizeNumericValue` gibi yardımcı fonksiyonları tanımlar.
    *   Önizleme için gerekli olan CSS değişkenlerini `<style>` etiketi içinde dinamik olarak oluşturur.
    *   Frontend JavaScript dosyalarını (`core.js`, `theme-editor.js` vb.) yükler.
*   **Önemli Not:** JavaScript dosyalarının yükleme sırası kritik öneme sahiptir. `core.js` gibi temel sınıfları içeren dosyalar, bu sınıfları kullanan diğer dosyalardan (`theme-editor.js`) önce yüklenmelidir. `defer` attribute'u bu sıralamayı garanti altına alır.

### 2.2. `_y/s/s/tasarim/Theme/tabs/colors.php` (Renk Seçim Arayüzü)

*   **Rolü:** Tema düzenleyici arayüzündeki "Genel Görünüm" sekmesinin içeriğini oluşturan PHP dosyasıdır. Kullanıcının renkleri seçebileceği HTML `<input type="color">` elemanlarını içerir.
*   **İçeriği:**
    *   `primary-color`, `secondary-color`, `text-primary-color` gibi CSS değişkenlerine karşılık gelen `name` özelliklerine sahip renk seçicileri barındırır.
    *   Her bir renk seçicinin `value` özelliği, `getCustomCSS` fonksiyonundan gelen mevcut değeri veya bir varsayılan değeri gösterir.
    *   Renk önizleme alanları (örneğin, "Metin Örnekleri") içerir.

### 2.3. `_y/s/s/tasarim/Theme/js/core.js` (Frontend Mantığı)

*   **Rolü:** Tema düzenleyici arayüzündeki kullanıcı etkileşimlerini (form değişiklikleri, kaydetme butonuna tıklama vb.) yöneten ana JavaScript dosyasıdır. `ThemeEditor` sınıfını tanımlar.
*   **İçeriği:**
    *   `ThemeEditor` sınıfı:
        *   Formdaki `input` ve `select` elemanlarındaki değişiklikleri dinler (`onFormChange`).
        *   `getFormData()` metodu ile formdaki tüm verileri bir JavaScript objesi olarak toplar.
        *   `updatePreview()` metodu ile yapılan değişiklikleri anında arayüzde gösterir (CSS değişkenlerini güncelleyerek).
        *   `saveTheme()` metodu ile toplanan verileri AJAX isteğiyle backend'e (`AdminDesignController.php`) gönderir.
        *   Sunucudan gelen JSON yanıtını işleyerek kullanıcıya bildirimler (`showNotification`) gösterir.
    *   Global fonksiyonlar (`saveTheme`, `previewTheme`, `resetTheme`, `applyColorTheme`) tanımlar ve bunları `ThemeEditor` sınıfının metotlarına bağlar.

### 2.4. `App/Controller/Admin/AdminDesignController.php` (Backend Mantığı ve CSS Oluşturma)

*   **Rolü:** Frontend'den gelen AJAX isteklerini işleyen ve dinamik CSS dosyasını oluşturan PHP kontrolcüsüdür.
*   **İş Akışı:**
    *   `action` parametresine göre (`saveDesign`, `savePreviewDesign`, `resetDesign`) farklı işlemler yapar.
    *   `$requestData` dizisinden gelen tüm form verilerini alır.
    *   Bu verileri kullanarak bir CSS `:root` bloğu oluşturur.
    *   **Akıllı Birim Ekleme Mantığı:** Sayısal değerlerin sonuna (genişlik, yükseklik, boyut, boşluk vb.) `px` birimini akıllıca ekler. `0` değerlerine, zaten birim içeren değerlere (`%`, `rem`) veya birimsiz olması gereken değerlere (`aspect-ratio`, `line-height`) dokunmaz.
    *   Oluşturulan CSS içeriğini `Public/CSS/index-{languageID}.css` (veya önizleme için `index-preview-{languageID}.css`) dosyasına yazar.
    *   İşlem sonucunu (`status` ve `message`) içeren bir JSON yanıtı döndürür.

### 2.5. `Public/CSS/index.css` (Varsayılan Tema Değişkenleri)

*   **Rolü:** Sitenin varsayılan tema değişkenlerini (`--primary-color`, `--body-bg-color` vb.) içeren ana CSS dosyasıdır.
*   **İçeriği:** Tüm sitenin genel görünümünü belirleyen CSS değişkenlerini tanımlar.
*   **Etkileşim:** `AdminDesignController.php` tarafından oluşturulan `index-{languageID}.css` dosyası, bu dosyadaki değişkenlerin üzerine yazar (override eder), böylece kullanıcı tarafından yapılan özelleştirmeler geçerli olur.

### 2.6. `Public/CSS/index-{languageID}.css` (Kullanıcı Tarafından Oluşturulan Tema)

*   **Rolü:** Kullanıcının tema düzenleyici arayüzünde yaptığı değişiklikler sonucunda dinamik olarak oluşturulan ve kaydedilen CSS dosyasıdır.
*   **İçeriği:** `AdminDesignController.php` tarafından oluşturulan, kullanıcının seçtiği değerleri içeren CSS değişkenlerini barındırır.
*   **Konum:** `Public/CSS/` dizini altında, dil koduna göre (örneğin `index-1.css` veya `index-tr.css`) kaydedilir.

## 3. İş Akışı (Kaydetme Örneği)

1.  **Kullanıcı Etkileşimi:** Yönetici, `_y/s/s/tasarim/Theme.php` sayfasında bir renk seçer veya bir sayısal değeri değiştirir.
2.  **Frontend Yakalama (`core.js`):** `core.js` içindeki `ThemeEditor` sınıfı, formdaki `change` veya `input` olaylarını yakalar.
3.  **Anında Önizleme (`core.js`):** `onFormChange` metodu tetiklenir, `getFormData()` ile güncel veriler toplanır ve `updatePreview()` çağrılır. `updatePreview()` ise tarayıcının `:root` elementindeki CSS değişkenlerini anında güncelleyerek canlı önizleme sağlar.
4.  **Kaydetme İsteği (`core.js`):** Yönetici "Temayı Kaydet" butonuna tıklar. `saveTheme()` metodu tetiklenir.
5.  **AJAX İsteği:** `saveTheme()` metodu, `getFormData()` ile toplanan tüm verileri ve `action: 'saveDesign'` parametresini içeren bir AJAX (POST) isteğini `window.location.href` adresine (yani `Theme.php`'ye) gönderir.
6.  **Backend İşleme (`AdminDesignController.php`):** `Theme.php` tarafından dahil edilen `AdminDesignController.php`, gelen POST isteğini yakalar. `action` parametresinin `saveDesign` olduğunu kontrol eder.
7.  **CSS Oluşturma:** `AdminDesignController.php`, `$requestData` içindeki her bir anahtar-değer çiftini döngüye alır. Bu döngü içinde, sayısal değerlere akıllıca `px` birimi eklenir (yukarıda açıklanan mantıkla). Sonuç olarak bir CSS `:root` bloğu dizesi oluşturulur.
8.  **Dosya Yazma:** Oluşturulan CSS dizesi, `file_put_contents` fonksiyonu ile `Public/CSS/index-{languageID}.css` dosyasına yazılır. Aynı zamanda bir JSON dosyası da (`JSON_DIR . 'CSS/' . $fileName . '.json'`) oluşturulur.
9.  **Yanıt ve Bildirim:** `AdminDesignController.php`, işlemin başarılı veya başarısız olduğunu belirten bir JSON yanıtı (`status` ve `message` ile) döndürür. `core.js` bu yanıtı alır ve kullanıcıya uygun bir bildirim (`showNotification`) gösterir.

## 4. Yapılan İyileştirmeler

Bu proje kapsamında tema yönetim sistemine aşağıdaki önemli iyileştirmeler yapılmıştır:

*   **`heading-color`'ın `index.css`'e Eklenmesi:** `colors.php`'de tanımlı olan `heading-color` değişkeni için `Public/CSS/index.css` dosyasına `--heading-color` CSS değişkeni eklenerek tutarlılık sağlandı.
*   **Eksik Temel Renk Seçicilerinin `colors.php`'ye Eklenmesi:** `index.css`'te tanımlı olup `colors.php`'de eksik olan `--info-color`, `--secondary-light-color`, `--secondary-dark-color`, `--background-light-color`, `--background-dark-color`, `--body-text-color`, `--text-light-color`, `--text-dark-color` ve `--border-dark-color` gibi temel renkler için `colors.php`'ye ilgili input alanları eklendi.
*   **Link Hover Önizlemesinin `colors.php`'ye Eklenmesi:** "Metin Örnekleri" bölümündeki linkin üzerine gelindiğinde renginin değişmesini sağlayan JavaScript kodu, `colors.php`'deki ilgili `<a>` etiketine eklendi. Bu, `link-hover-color` ayarının canlı önizlemesini mümkün kıldı.
*   **`core.js`'deki AJAX `action` Parametresi Hatasının Düzeltilmesi:** `core.js`'deki `saveTheme` fonksiyonunun AJAX isteğinde kullanılan `action` parametresi, `save` yerine backend'in beklediği `saveDesign` olarak düzeltildi. Bu, kaydetme işleminin backend tarafından doğru şekilde tanınmasını sağladı.
*   **`Theme.php`'deki JS Yükleme Sıralaması (`defer`):** JavaScript dosyalarının yükleme sırasından kaynaklanan "class not found" hatalarını gidermek için `Theme.php` dosyasındaki `<script>` etiketlerine `defer` attribute'u eklendi. Bu, scriptlerin doğru sırada ve DOM yüklendikten sonra çalışmasını garanti eder.
*   **`AdminDesignController.php`'deki Akıllı Birim Ekleme Mantığı:** CSS değişkenleri oluşturulurken sayısal değerlere (genişlik, yükseklik, boşluk vb.) `px` biriminin akıllıca eklenmesi sağlandı. `0` değerleri, zaten birim içeren değerler (`%`, `rem`) ve `aspect-ratio` gibi birimsiz olması gereken değerler bu işlemden muaf tutuldu.

## 5. Gelecek Geliştirmeler/Notlar

*   **Daha Kapsamlı Birim Yönetimi:** Şu anki birim ekleme mantığı temel ihtiyaçları karşılasa da, gelecekte `calc()`, `min()`, `max()` gibi CSS fonksiyonlarını veya daha karmaşık birim dönüşümlerini desteklemek için geliştirilebilir.
*   **Hata Yönetimi ve Loglama:** Frontend ve backend arasındaki hata mesajları daha detaylı hale getirilebilir ve sunucu tarafında daha kapsamlı loglama mekanizmaları eklenebilir.
*   **Performans Optimizasyonu:** Büyük tema dosyaları için CSS oluşturma ve yazma süreçleri optimize edilebilir (örneğin, sadece değişen değerleri yazma).
*   **Kullanıcı Arayüzü Geliştirmeleri:** Tema düzenleyici arayüzü, daha fazla önizleme alanı, sürükle-bırak özellikleri veya daha gelişmiş renk seçicilerle zenginleştirilebilir.
*   **Versiyon Kontrolü:** Tema ayarlarının versiyon kontrolü (eski ayarlara geri dönme) özelliği eklenebilir.

## 6. Geliştiriciler İçin Ek Bilgiler

### 6.1. Yeni Tema Değişkeni Ekleme Rehberi

Sisteme yeni bir CSS değişkeni eklemek için aşağıdaki adımları izleyin:

1.  **Frontend (HTML/PHP):**
    *   `_y/s/s/tasarim/Theme/tabs/` altındaki ilgili PHP dosyasına (örneğin, `colors.php` veya `header.php`) yeni değişken için bir HTML `<input>` veya `<select>` alanı ekleyin.
    *   `name` özelliğini CSS değişken adıyla (örneğin, `my-new-variable`) aynı yapın.
    *   `value` özelliğini `sanitizeColorValue` veya `sanitizeNumericValue` fonksiyonlarını kullanarak mevcut değeri veya varsayılan değeri çekecek şekilde ayarlayın.
    *   `Theme.php` dosyasındaki `<style>` bloğunda, bu yeni değişkeni `--my-new-variable: <?=$customCSS['my-new-variable'] ?? 'default-value'?>;` şeklinde tanımlayın.

2.  **Frontend (JavaScript):**
    *   `_y/s/s/tasarim/Theme/js/core.js` dosyasındaki `ThemeEditor` sınıfının `getFormData()` metodunun yeni değişkeni otomatik olarak topladığından emin olun. (Genellikle formdaki `name` özelliğine göre otomatik toplanır.)
    *   Eğer yeni değişkenin canlı önizlemede özel bir işleme ihtiyacı varsa, `ThemeEditor` sınıfındaki `updatePreview()` veya ilgili `updateXPreview()` metodunu güncelleyin.

3.  **Backend (PHP):**
    *   `App/Controller/Admin/AdminDesignController.php` dosyasında, `saveDesign` action'ı içinde, `$requestData`'dan yeni değişkeni alın: `$myNewVariable = $requestData["my-new-variable"] ?? null;`.
    *   `$themeConfig` dizisine bu yeni değişkeni ekleyin: `'my-new-variable' => $myNewVariable,`.
    *   CSS çıktısı oluşturulurken `AdminDesignController.php` içindeki `foreach ($cssContent as $key => $value)` döngüsünde, yeni değişkenin doğru şekilde formatlandığından (örneğin, birim eklenmesi gerekiyorsa) emin olun.

### 6.2. Sıkça Sorulan Sorular ve Sorun Giderme

*   **Tema değişiklikleri neden uygulanmıyor?**
    *   Tarayıcı önbelleğini temizlemeyi deneyin.
    *   `AdminDesignController.php` dosyasının doğru şekilde kaydedildiğinden ve erişilebilir olduğundan emin olun.
    *   Tarayıcının geliştirici konsolunda (F12) herhangi bir JavaScript hatası olup olmadığını kontrol edin.
    *   Sunucu tarafında PHP hata loglarını kontrol edin.
*   **Renk seçici veya sayısal inputlar düzgün çalışmıyor?**
    *   `Theme.php` dosyasında `defer` attribute'unun `<script>` etiketlerinde doğru kullanıldığından emin olun.
    *   `core.js` dosyasının doğru yüklendiğinden ve `ThemeEditor` sınıfının başlatıldığından emin olun.
*   **CSS dosyasında beklenmedik karakterler veya formatlama sorunları var?**
    *   `AdminDesignController.php` içindeki CSS oluşturma mantığını ve `sanitizeColorValue`, `sanitizeNumericValue` gibi fonksiyonları kontrol edin. Özellikle birim ekleme veya tırnaklama kurallarını gözden geçirin.

### 6.3. Görsel Akış Şeması (Kavramsal)

Tema yönetim sisteminin veri akışını daha iyi anlamak için aşağıdaki kavramsal akış şemasını inceleyebilirsiniz:

```
[Kullanıcı Arayüzü (Theme.php)]
       | (Form Değişiklikleri)
       V
[Frontend JavaScript (core.js)]
       | (getFormData(), updatePreview())
       | (AJAX İsteği: saveDesign)
       V
[Backend PHP (AdminDesignController.php)]
       | (Request Data İşleme)
       | (CSS Oluşturma)
       | (JSON ve CSS Dosyalarına Yazma)
       V
[Public/CSS/index-{languageID}.css] <--- (Tarayıcı tarafından okunur)
[Public/Json/CSS/index-{languageID}.json] <--- (Theme.php tarafından okunur)
```
