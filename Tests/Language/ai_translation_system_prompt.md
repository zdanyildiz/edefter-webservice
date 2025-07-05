# Yapay Zeka Destekli Asenkron Çeviri Sistemi

*Bu dosya, yeni dil eklendiğinde kategori ve sayfaların yapay zeka ile otomatik olarak çevrilmesini sağlayan asenkron sistemin mimarisini, işleyişini ve ilgili dosyalarını belgeler.*

## 1. Amaç ve Kapsam

**Eski Sistemdeki Sorun:** Yeni bir dil eklendiğinde, mevcut sistem ana dildeki kategori ve sayfaları sadece kopyalayıp isimlerinin başına "new-" ön eki ekliyordu. İçeriklerin manuel olarak çevrilmesi gerekiyordu, bu da zaman alıcı ve verimsiz bir süreçti.

**Yeni Sistemin Amacı:** Bu geliştirmeyle, yeni dil ekleme sürecini modernize ederek, seçime bağlı olarak tüm kategori ve sayfa içeriklerinin (başlıklar, HTML içerikler, SEO verileri) yapay zeka tarafından **otomatik** ve **arka planda** çevrilmesini sağlamak hedeflenmiştir. Bu, hem zamandan tasarruf sağlar hem de süreci otomatikleştirerek kullanıcı yükünü azaltır.

**Temel Faydalar:**
- **Kullanıcı Deneyimi:** Dil ekleme işlemi anında tamamlanır, kullanıcı uzun süre beklemek zorunda kalmaz.
- **Güvenilirlik:** Çeviri işlemleri arka planda, küçük gruplar halinde ve hata toleranslı bir yapıda çalışır. Bir çevirideki hata, tüm süreci durdurmaz.
- **Ölçeklenebilirlik:** Binlerce sayfa ve kategori olsa bile sistem, sunucuyu yormadan zamanla tüm çevirileri tamamlayabilir.
- **Esneklik:** Kullanıcı, dil eklerken yapay zeka çevirisini isteyip istemediğini bir onay kutusu ile seçebilir.

---

## 2. Sistem Mimarisi ve İşleyişi

Sistem, kullanıcı etkileşimini sunucu yükünden ayıran **asenkron (asynchronous)** bir model üzerine kurulmuştur.

### Adım 1: Kullanıcı Arayüzü - Dil Ekleme (Anlık İşlem)

1.  **Tetikleme Noktası:** Yönetim panelindeki `_y/s/b/Diller.php` sayfasında bulunan "Yeni Dil Ekle" formu.
2.  **Yeni Seçenek:** Forma "Kategori ve sayfaları Yapay Zeka ile çevir" (`translateWithAI`) adında bir onay kutusu eklendi.
3.  **Controller Mantığı (`AdminLanguageController.php`):
    -   Kullanıcı formu gönderdiğinde, `addLanguage` aksiyonu çalışır.
    -   Ana dildeki tüm kategoriler ve sayfalar, yeni dil için kopyalanır.
    -   `language_category_mapping` ve `language_page_mapping` tablolarına bu kopyalama ilişkisini belirten kayıtlar atılır.
    -   **Koşullu Durum Ataması:**
        -   Eğer `translateWithAI` kutusu **işaretliyse**, haritalama tablolarındaki yeni kayıtların `translation_status` sütunu `'pending'` (beklemede) olarak ayarlanır.
        -   Eğer `translateWithAI` kutusu **işaretli değilse**, `translation_status` sütunu doğrudan `'completed'` (tamamlandı) olarak ayarlanır. Bu, arka plan işçisinin bu kayıtları görmezden gelmesini sağlar.
4.  **Sonuç:** İşlem anında biter ve kullanıcıya işlemin başarılı olduğuna ve çevirilerin (seçildiyse) arka planda yapılacağına dair bir mesaj gösterilir.

### Adım 2: Arka Plan İşçisi - Cron Job (Zamanlanmış Görev)

Bu, sistemin ağır yükünü çeken asıl mekanizmadır.

1.  **Çalışma Ortamı:** Sunucu tarafından düzenli aralıklarla (örn: her 5 dakikada bir) çalıştırılan `App/Cron/ContentTranslator.php` script'i.
2.  **Özel Başlatıcı (`CronGlobal.php`):** Bu script, yönetici girişi kontrolü yapan `AdminGlobal.php` yerine, sadece veritabanı bağlantısı gibi temel bileşenleri yükleyen, ancak **login kontrolü yapmayan** `App/Core/CronGlobal.php` dosyasını kullanır. Bu, sunucu tarafından sorunsuzca çalışabilmesi için kritik bir adımdır.
3.  **İşleyiş Döngüsü:**
    a.  **Görev Bulma:** Script, `language_category_mapping` ve `language_page_mapping` tablolarından `translation_status = 'pending'` olan sınırlı sayıda (örn: 5) kaydı çeker.
    b.  **Veri Çekme:** Her bir kayıt için orijinal içeriğin (kategori/sayfa) metinlerini ve SEO bilgilerini veritabanından okur.
    c.  **Yapay Zeka Çevirisi (`AdminChatCompletion.php`):
        -   Normal metinler `translateConstant` ile çevrilir.
        -   HTML içerikler, etiket yapısını korumak ve `[placeholder]` gibi özel ifadeleri atlamak için tasarlanmış `translateHtmlContent` metodu ile çevrilir.
    d.  **Link Oluşturma:** Çevrilen başlıkları kullanarak, projenin hiyerarşik yapısına uygun (`/{dil_kodu}/{ust_kategori}/.../{yeni_link}`), doğru SEO linklerini `createSeoLink` ve `getCategoryHierarchy` yardımcı fonksiyonlarıyla oluşturur.
    e.  **Veritabanı Güncelleme:** Çevrilen metinleri ve yeni linkleri ilgili kopyalanmış kayıtların üzerine yazar (`updateCategoryField`, `updatePageField`, `updateSeo`).
    f.  **Durum Güncelleme:** İşlem başarılıysa, harita kaydının `translation_status` alanını `'completed'` olarak günceller.

### Adım 3: Hata Yönetimi

-   Tüm çeviri ve veritabanı işlemleri `try-catch` blokları içinde yürütülür.
-   Bir çeviri sırasında herhangi bir hata (örn: API hatası) meydana gelirse, script çöker yerine:
    -   Harita kaydının `translation_status` alanını `'failed'` olarak günceller.
    -   Alınan hata mesajını `error_message` sütununa yazar.
    -   Bir sonraki `pending` kaydı ile çalışmaya devam eder.
-   Tüm önemli adımlar ve hatalar `Public/Log/Admin/` dizinine loglanır.

---

## 3. İlgili Dosyalar ve Fonksiyonlar

-   **`_y/s/b/Diller.php`**: Kullanıcı arayüzü, "Yapay Zeka ile çevir" onay kutusu eklendi.
-   **`App/Controller/Admin/AdminLanguageController.php`**: `addLanguage` aksiyonu, `translateWithAI` seçeneğine göre `translation_status` ataması yapacak şekilde güncellendi.
-   **`App/Cron/ContentTranslator.php`**: Tüm asenkron çeviri mantığını yürüten ana cron job script'i. (Yeni oluşturuldu)
-   **`App/Core/CronGlobal.php`**: Cron job'lar için yönetici girişi kontrolü yapmayan özel başlatıcı. (Yeni oluşturuldu)
-   **`App/Model/Admin/AdminLanguage.php`**:
    -   `addLanguagePageMapping`, `addLanguageCategoryMapping`: `translation_status` parametresi eklendi.
    -   `getPendingCategoryTranslations`, `getPendingPageTranslations`: Bekleyen görevleri çeker. (Yeni)
    -   `updateCategoryTranslationStatus`, `updatePageTranslationStatus`: Görev durumunu günceller. (Yeni)
-   **`App/Model/Admin/AdminCategory.php`**:
    -   `getCategoryById`: ID ile kategori bilgilerini çeker.
    -   `updateCategoryField`: Tek bir alanı (örn: `kategoriad`) günceller. (Yeni)
-   **`App/Model/Admin/AdminPage.php`**:
    -   `getPageById`: ID ile sayfa bilgilerini çeker. (Yeni)
    -   `updatePageField`: Tek bir alanı (örn: `sayfaicerik`) günceller. (Yeni)
-   **`App/Model/Admin/AdminSeo.php`**:
    -   `updateSeo`: `seo` tablosunu toplu veri ile günceller.
-   **`App/Model/Admin/AdminChatCompletion.php`**:
    -   `translateHtmlContent`: HTML etiketlerini ve `[placeholder]` ifadelerini koruyarak çeviri yapan özel fonksiyon eklendi.

---

## 4. Kurulum ve Kullanım

Sistemin çalışması için sunucuda bir **cron job (zamanlanmış görev)** ayarlanması zorunludur. Aşağıdaki komut, sunucu yapılandırmasına göre düzenlenerek cron job olarak eklenmelidir:

```bash
/usr/bin/php /path/to/your/project/App/Cron/ContentTranslator.php
```

Bu kurulum yapıldıktan sonra, yönetim panelinden "Yapay Zeka ile çevir" seçeneği işaretlenerek eklenen her yeni dil için çeviriler otomatik olarak arka planda başlayacaktır.
