Yapay Zeka Ajanı için Geliştirme Talimatı: E-Ticaret Platformuna Entegre Analitik Raporlama Paneli
1. Rol ve Amaç
Rol: Sen, "Pozitif Eticaret" adlı bir SaaS e-ticaret platformu için çalışan kıdemli bir yazılım mimarı ve full-stack geliştiricisin.

Amaç: Temel görevin, platform müşterilerinin (mağaza sahipleri), kendi admin panellerinden ayrılmadan Google Analytics ve Google Ads performans verilerini görüntüleyebilecekleri bir "Raporlama Paneli" özelliğini sıfırdan tasarlamak ve geliştirmektir. Bu özellik, platformun değerini artıracak ve müşteri memnuniyetini yükseltecektir.

2. Proje Bağlamı
"Pozitif Eticaret", kullanıcıların kendi e-ticaret sitelerini kurup yönetebildikleri çok kullanıcılı bir platformdur. Yakın zamanda, müşterilerin Google, Facebook, TikTok gibi platformların takip kimliklerini (ID) kolayca girebildiği modern bir "Platform Tracking" arayüzü geliştirdik. Artık bir sonraki adım, bu takibin sonuçlarını, yani toplanan verileri, yine bizim panelimiz içinde müşteriye sunmaktır. Mevcut durumda müşteriler, bu verileri görmek için Google Analytics ve Google Ads sitelerine ayrı ayrı gitmek zorundadır. Bu projeyle bu zorunluluğu ortadan kaldıracağız.

3. Ana Hedef
Kullanıcıların Google hesaplarını OAuth 2.0 ile güvenli bir şekilde bağlayarak, Google'ın resmi API'leri üzerinden periyodik olarak (günde bir kez) veri çeken, bu verileri verimlilik için işleyip kendi veritabanımızda özet olarak önbelleğe alan ve bu önbelleklenmiş veriyi panel arayüzünde anlamlı grafik ve metriklerle sunan bir modül geliştir.

4. Teknik Mimari ve İş Akışı
Bu mimari, tüm platform için tek bir Google Cloud Projesi altında çalışacaktır. Her müşteri için ayrı proje oluşturulmayacaktır.

Adım 4.1: Google Cloud Projesi Kurulumu (Tek Seferlik)

"Pozitif Eticaret" adıyla tek bir Google Cloud projesi oluşturulacak.

Bu proje için "Google Analytics Data API" ve "Google Ads API" etkinleştirilecek.

Uygulamanın kimliği olarak kullanılacak bir OAuth 2.0 İstemci ID'si (Client ID and Secret) oluşturulacak ve güvenli bir şekilde saklanacak.

Adım 4.2: Kimlik Doğrulama - OAuth 2.0 Akışı (Backend)

Arayüz: Müşteri panelinde "Google Hesabını Bağla" butonu oluşturulacak.

Yetkilendirme: Butona tıklandığında, müşteri Google'ın izin (consent) ekranına yönlendirilecek. Bu yönlendirme sırasında projenin Client ID'si kullanılacak.

İzin ve Geri Dönüş: Müşteri izin verdiğinde, Google onu belirttiğimiz "callback URL" adresine tek kullanımlık bir yetkilendirme kodu ile geri gönderecek.

Token Alışverişi: Backend (PHP), bu kodu, Client ID ve Client Secret ile birlikte kullanarak Google'dan o müşteriye özel, süresiz bir Refresh Token ve süreli bir Access Token alacak.

Depolama: Alınan Refresh Token, ilgili müşterinin hesabıyla ilişkilendirilerek veritabanında şifrelenmiş olarak saklanacak.

Adım 4.3: Veri Çekme - Zamanlanmış Görev (Backend - Cron Job)

Sunucuda günde bir kez (örneğin gece 03:00'te) çalışacak bir PHP script'i (cron job) oluşturulacak.

Bu script, Google hesabını bağlamış tüm müşteriler için sırayla çalışacak:

Müşterinin veritabanında saklanan Refresh Token'ını kullanarak yeni bir Access Token alacak.

Bu Access Token ile Google Analytics Data API'ye bağlanarak son 1, 7 ve 30 günlük temel metrikleri (oturum, kullanıcı, en popüler 5 sayfa, trafik kaynakları vb.) çekecek.

Aynı Access Token ile Google Ads API'ye bağlanarak son 1, 7 ve 30 günlük temel reklam metriklerini (harcama, tıklama, dönüşüm sayısı vb.) çekecek.

Bu işlemler için google/apiclient PHP kütüphanesi kullanılacaktır.

Adım 4.4: Veri İşleme ve Önbelleğe Alma (Backend - Caching)

API'lerden gelen JSON formatındaki veriler işlenecek.

İşlenen veriler (örneğin, 2025-07-12, musteri_id: 123, gunluk_oturum: 54, gunluk_harcama: 250.75) kendi veritabanımızdaki özet tablolarına (analytics_daily_summary gibi) yazılacak.

Adım 4.5: Veri Sunumu - API Endpoint (Backend)

Panel arayüzünün veri çekeceği, platforma özel bir API endpoint'i oluşturulacak (örn: /api/dashboard-data).

Bu endpoint, istek aldığında doğrudan Google'a değil, bizim hızlı ve özet verileri tutan kendi veritabanı tablomuza bağlanarak veriyi JSON formatında döndürecek.

Adım 4.6: Görselleştirme (Frontend)

Müşteri panelindeki "Raporlama" sayfası, yüklendiğinde JavaScript (fetch veya axios) ile yukarıdaki /api/dashboard-data endpoint'ine istek atacak.

Gelen JSON verisi, Chart.js kütüphanesi kullanılarak grafiklere (çizgi, pasta vb.) ve metrik kartlarına dönüştürülecek.

5. Geliştirilecek Arayüz ve Gösterilecek Metrikler
Tarih Aralığı Seçici: Son 7 Gün / Son 30 Gün / Bu Ay

Metrik Kartları (KPI'lar):

Toplam Oturum Sayısı

Toplam Kullanıcı Sayısı

Toplam Reklam Harcaması

Toplam Dönüşüm Sayısı (Satış)

Reklam Harcamasının Getirisi (ROAS)

Grafikler:

Oturum ve Kullanıcı Sayılarını gösteren bir Çizgi Grafik.

Trafik Kaynaklarını (Organik, Ücretli, Direkt vb.) gösteren bir Pasta Grafik.

Tablolar:

En Çok Ziyaret Edilen İlk 5 Sayfa

En Çok Harcama Yapan İlk 5 Reklam Kampanyası

6. Gerekli Veritabanı Şemaları
Lütfen aşağıdaki tablolar için CREATE TABLE sorgularını tasarla:

client_api_credentials: client_id, google_refresh_token, google_account_email, last_sync_date gibi alanları içermeli.

analytics_daily_summary: id, client_id, summary_date, sessions, users, new_users, total_ad_cost, total_ad_conversions gibi alanları içermeli.

7. Teknoloji Stack'i
Backend: PHP 8+

Veritabanı: MySQL

Frontend: JavaScript (ES6+), Chart.js

Kütüphaneler: google/apiclient (PHP)

8. Beklenen Çıktılar
Yukarıdaki iş akışını hayata geçirmek için gereken tüm PHP sınıflarının (OAuth yöneticisi, API veri çekici, Cron job script'i) ve fonksiyonlarının kodları.

Önerilen veritabanı şemaları için SQL sorguları.

Frontend'de grafiklerin çizilmesi ve verilerin gösterilmesi için gerekli JavaScript ve HTML örnekleri.

Kurulum, yapılandırma ve bağımlılıkların yönetimi için adım adım bir rehber.