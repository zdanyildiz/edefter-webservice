# Page Sistemi Dokümantasyonu

## 🎯 AMAÇ VE KAPSAM
Page (Sayfa) modülü, web sitesindeki statik ve dinamik içerikleri yöneten bir sistemdir. Bu modül şunları sağlar:
- Temel sayfa içeriklerinin yönetimi (başlık, içerik, tipler)
- SEO verileri entegrasyonu
- Sayfalara resim, galeri, dosya ve video ekleyebilme
- İletişim bilgileri ve sosyal medya entegrasyonu
- Çoklu dil desteği için kategori-dil ilişkisi

## 🏗️ SİSTEM MİMARİSİ

### 📁 Dosya Yapısı
```
App/
  ├── Controller/
  │   ├── PageController.php
  │   └── Admin/
  │       └── AdminPageController.php
  ├── Model/
  │   ├── Page.php
  │   └── Admin/
  │       └── AdminPage.php
  └── View/
      ├── Page/ 
      │   └── (çeşitli sayfa şablonları)
      └── Admin/
          └── Page/ 
              └── (admin için sayfa yönetim şablonları)
```

### 📊 VERİTABANI YAPISI

#### Ana Tablolar
1. **sayfa**
   - `sayfaid` (PK) - INT, AUTO_INCREMENT
   - `benzersizid` - CHAR(20), Eşsiz tanımlayıcı
   - `sayfatariholustur` - DATETIME(6), Oluşturulma tarihi
   - `sayfatarihguncel` - DATETIME(6), Güncellenme tarihi
   - `sayfatip` - TINYINT(1), Sayfa tipi (ilişkili sayfatip tablosuna bağlı)
   - `sayfaad` - VARCHAR(255), Sayfa başlığı
   - `sayfaicerik` - LONGTEXT, HTML içerik
   - `sayfalink` - VARCHAR(255), Direkt link (SEO dışı)
   - `sayfasira` - TINYINT(4), DEFAULT 0, Görüntüleme sırası
   - `sayfaaktif` - TINYINT(1), DEFAULT 1, Aktif/pasif durumu
   - `sayfasil` - TINYINT(1), DEFAULT 0, Silinip silinmediği
   - `sayfahit` - INT(11), DEFAULT 0, Görüntülenme sayısı

2. **sayfatip** - Sayfa tipleri için referans tablosu
   - `sayfatipid` (PK) - INT, AUTO_INCREMENT
   - `sayfatipad` - VARCHAR(50), Tip adı
   - `yetki` - TINYINT(1), DEFAULT 0, Yetki gerekip gerekmediği
   - `gorunum` - TINYINT(1), DEFAULT 1, Görünür olup olmadığı
   - `sayfatipsil` - TINYINT(1), DEFAULT 0, Silinip silinmediği

3. **seo** - SEO bilgilerini içerir
   - `seoid` (PK) - INT, AUTO_INCREMENT
   - `benzersizid` - CHAR(20), FK (sayfa.benzersizid ile ilişkili)
   - `baslik` - VARCHAR(100), SEO başlığı
   - `aciklama` - VARCHAR(355), SEO açıklaması
   - `kelime` - VARCHAR(255), SEO anahtar kelimeler
   - `link` - VARCHAR(1000), SEO dostu URL
   - `orjinallink` - VARCHAR(1000), Orijinal link (NULL olabilir)
   - `resim` - LONGTEXT, Sosyal medya paylaşımı için resim (NULL olabilir)

4. **sayfapaylasim** - Paylaşım istatistikleri
   - `sayfapaylasimid` (PK) - INT, AUTO_INCREMENT
   - `benzersizid` - VARCHAR(20), FK (sayfa.benzersizid ile ilişkili)
   - `paylasimyeri` - VARCHAR(10), Paylaşım platformu
   - `paylasimsayisi` - INT(11), Paylaşım sayısı

#### İlişki Tabloları
1. **sayfalisteresim** - Sayfalar ile resimler arasındaki ilişki
   - `sayfalisteresimid` (PK) - INT, AUTO_INCREMENT
   - `sayfaid` - INT(11), FK (sayfa.sayfaid)
   - `resimid` - INT(11), FK (resim.resimid)

2. **sayfalistekategori** - Sayfalar ile kategoriler arasındaki ilişki
   - `sayfalistekategoriid` (PK) - INT, AUTO_INCREMENT
   - `sayfaid` - INT(11), FK (sayfa.sayfaid)
   - `kategoriid` - INT(11), FK (kategori.kategoriid)

3. **sayfalistedosya** - Sayfalar ile dosyalar arasındaki ilişki
   - `sayfalistedosyaid` (PK) - INT, AUTO_INCREMENT
   - `sayfaid` - INT(11), FK (sayfa.sayfaid)
   - `dosyaid` - INT(11), FK (dosya.dosyaid)

4. **sayfalistegaleri** - Sayfalar ile galeriler arasındaki ilişki
   - `sayfalistegaleriid` (PK) - INT, AUTO_INCREMENT
   - `sayfaid` - INT(11), FK (sayfa.sayfaid)
   - `resimgaleriid` - INT(11), FK (resimgaleri.resimgaleriid)

5. **sayfalistevideo** - Sayfalar ile videolar arasındaki ilişki
   - `sayfalistevideoid` (PK) - INT, AUTO_INCREMENT
   - `sayfaid` - INT(11), FK (sayfa.sayfaid)
   - `videoid` - INT(11), FK (video.videoid)

### 🔄 SİSTEM İŞLEYİŞİ

1. **PageController**
   - Kullanıcı isteğini alır (URL'den)
   - `Router` tarafından belirlenen içerik tipine göre (PAGE) yönlenir
   - Sayfa verilerini `Page` model üzerinden alır
   - Sayfa tipine göre ekstra verileri hazırlar (iletişim formları, firma bilgileri)
   - Verileri View'a aktarır

2. **Page Model**
   - Sayfa verilerini veritabanından çeker
   - İlgili tüm ilişkili verileri birleştirir (resimler, kategoriler)
   - JSON cache sistemini kullanır
   - Sayfa içeriği içindeki özel etiketleri ([iletisimform], [sosyalmedya], [firmatelefon] vb.) işler
   - Sayfa tipine göre özel içerikleri hazırlar

3. **İçerik İşleme**
   - İletişim sayfaları (tip=1): Form, harita ve iletişim bilgileri eklenir
   - Firma bilgi sayfaları (tip=10/12/13/14/15/18/25): Site ayarlarından firma bilgileri sayfaya entegre edilir
   - Sosyal medya: Site ayarlarından sosyal medya linkleri ve ikonları eklenir

### 📋 Sayfa Tipleri Referansı

Page sisteminde kullanılan sayfa tipleri şunlardır:

| ID | Tip Adı | Açıklama | Yetki Gerekir |
|----|---------|----------|---------------|
| 1 | İletişim | İletişim formu ve bilgileri | Evet |
| 2 | Haber | Haber içerikleri | Evet |
| 3 | Resim Galerisi | Resim galerisi içeren sayfalar | Evet |
| 4 | Video | Video içeren sayfalar | Evet |
| 5 | Dosya | Dosya indirme sayfaları | Evet |
| 6 | Duyuru | Duyuru içerikleri | Evet |
| 7 | Ürün | Ürün sayfaları | Evet |
| 8 | Sepet | Alışveriş sepeti | Hayır |
| 9 | Ödeme Kontrol | Ödeme doğrulama sayfası | Hayır |
| 10 | Üyelik Sözleşmesi | Üyelik koşulları | Hayır |
| 11 | Bayi Girişi | Bayilik girişi | Hayır |
| 12 | Mesafeli Satış | Mesafeli satış sözleşmesi | Hayır |
| 13 | Çerez Politikası | Çerez bildirimi | Hayır |
| 14 | Şartlar ve koşullar | Kullanım şartları | Hayır |
| 15 | Gizlilik İlkeleri | Gizlilik politikası | Hayır |
| 16 | Markalar | Marka listesi | Hayır |
| 17 | Üye Ol / Üye Giriş | Üyelik işlemleri | Hayır |
| 18 | İptal, İade Formu | İade talep sayfası | Hayır |
| 19 | Favori | Favori ürünler | Hayır |
| 20 | Katalog | Ürün katalogları | Evet |
| 21 | Hakkımızda | Kurumsal bilgiler | Evet |
| 22 | Ödeme | Ödeme işleme sayfası | Hayır |
| 23 | Genel | Genel içerik sayfaları | Evet |
| 24 | Blog | Blog yazıları | Evet |
| 25 | KVKK | Kişisel verilerin korunması | Hayır |

## 🔌 ENTEGRASYONLAR

### 📱 Diğer Modüllerle İlişki
1. **SEO Sistemi**
   - Sayfa SEO verileri `seo` tablosu üzerinden sağlanır
   - `benzersizid` ile ilişki kurulur

2. **Kategori Sistemi**
   - Sayfalar kategoriler ile ilişkilendirilir
   - Bu ilişki ile dil bilgisi de sağlanır
   - `sayfalistekategori` tablosu ile many-to-many ilişki kurulur

3. **Medya Sistemi**
   - Resim, galeri, dosya ve video ile ilişkiler
   - İlgili ilişki tabloları üzerinden bağlantı kurulur

4. **Site Ayarları**
   - Şirket bilgileri ve sosyal medya ayarları `siteConfig` üzerinden alınır
   - Bu bilgiler sayfa içeriğine entegre edilir

5. **Form Sistemi**
   - İletişim formları dinamik olarak oluşturulur
   - CloudFlare Turnstile entegrasyonu için ayarlar kullanılır

## 🔧 KULLANIM ÖRNEKLERİ

### 1. Standart Sayfa Çağırma
```php
// PageController içinde:
$pageData = $this->pageModel->getPageById(0, 'hakkimizda');
$this->config->loadView('Page/Standard', $pageData);
```

### 2. İletişim Sayfası
```php
// İletişim sayfası için özel işlemler:
if ($pageData['sayfatip'] == 1) {
    // İletişim formu ve bilgileri eklenir
    // CloudFlare entegrasyonu sağlanır
}
```

### 3. Firma Bilgilerini Sayfalara Entegre Etme
```php
// Sayfa içeriğinde etiketlerin değiştirilmesi:
$pageContent = str_replace("[firmaad]", $companyName, $pageContent);
$pageContent = str_replace("[adres]", $companyAddress, $pageContent);
```

## 🧩 GELİŞTİRME REHBERİ

### Yeni Sayfa Tipi Ekleme
1. `sayfatip` tablosuna yeni bir tip ekleyin
2. Page.php içinde bu tip için özel bir işleme metodu ekleyin
3. AdminPageController.php'de bu tip için form alanlarını güncelleyin
4. View klasöründe bu tip için özel şablon oluşturun

### Özel Etiket Ekleme
1. Page.php'ye yeni bir etiket için işleme kodu ekleyin:
```php
$pageContent = str_replace("[yeniozel]", $ozelVeri, $pageContent);
```
2. Bu etiketin verilerini hazırlayacak metodu ekleyin

### Performans İyileştirmeleri
1. JSON cache sistemini etkin kullanın
2. SQL sorgularını optimize edin, gereksiz JOIN'leri azaltın
3. GROUP_CONCAT kullanımını sınırlayın, büyük veriler için parçalı sorgular yapın

## 🔍 TROUBLESHOOTING

### Yaygın Sorunlar ve Çözümleri

1. **Sayfa Bulunamadı (404)**
   - `benzersizid` veya `sayfaid` parametrelerini kontrol edin
   - SEO tablosundaki `link` değerini kontrol edin
   - Log dosyalarını `Public/Log/` altında inceleyin

2. **İçerik Etiketleri Çalışmıyor**
   - Etiketi doğru yazdığınızdan emin olun (örn. [firmatelefon])
   - İlgili site ayarlarının tanımlı olduğunu kontrol edin
   - Sayfa tipinin doğru seçildiğini doğrulayın

3. **Resimler Görünmüyor**
   - `sayfalisteresim` ilişkisini kontrol edin
   - Resim yolunu ve klasör yapısını doğrulayın
   - Resim dosyasının var olduğundan emin olun

4. **İletişim Formları Çalışmıyor**
   - CloudFlare Turnstile ayarlarını kontrol edin
   - POST formunun doğru endpoint'e gönderildiğinden emin olun

## 🚀 GELECEK GELİŞTİRMELER

1. **Tip Sistemi İyileştirmesi**
   - Sayfa tiplerini daha modüler ve genişletilebilir yapıya dönüştürme
   - Tip spesifik ayarların ayrı bir tabloda saklanması

2. **Sayfa Versiyonlama**
   - Değişiklik geçmişini tutma
   - Önceki sürümlere dönebilme

3. **Gelişmiş İçerik Editörü**
   - Blok tabanlı içerik oluşturma
   - Drag & drop bileşen yerleştirme

4. **Otomatik İçerik Optimizasyonu**
   - SEO önerileri
   - İçerik analizi ve iyileştirme önerileri

5. **Çoklu Şablon Desteği**
   - Sayfa başına şablon seçimi
   - Dinamik şablon değişkenleri
