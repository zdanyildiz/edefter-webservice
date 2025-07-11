# Proje Analizi: Global Pozitif E-Defter Okuyucu

## Projenin Amacı

Bu proje, "Global Pozitif E-Defter Okuyucu" adında bir Windows Forms uygulamasıdır. Temel amacı, kullanıcıların e-defter formatındaki XML dosyalarını (berat, yevmiye, kebir, defterraporu) kolayca görüntülemesini sağlamaktır. XML dosyaları, XSLT şablonları kullanılarak HTML formatına dönüştürülür ve uygulama içindeki sekmelerde gösterilir.

## Ana Bileşenler ve İşleyiş

1.  **`Program.cs` (Başlangıç Noktası):**
    *   Uygulama buradan başlar.
    *   **Tek Örnek Kontrolü:** `Mutex` kullanarak uygulamanın aynı anda sadece bir kopyasının çalışmasını sağlar. Eğer uygulama zaten çalışıyorsa ve yeni bir XML dosyası açılmak istenirse, dosya yolunu çalışan uygulamaya gönderir ve yeni bir kopya açmaz.
    *   **Kimlik Doğrulama ve Oturum Yönetimi:**
        *   Uygulama ilk kez çalıştığında veya internet bağlantısı olmadığında, yerel olarak şifrelenmiş kimlik bilgilerini (`encrypted_credentials.txt`) kontrol eder.
        *   İnternet varsa, `https://e-defter.globalpozitif.com.tr` adresindeki bir web servisi üzerinden kullanıcı kimlik bilgilerini (e-posta, şifre) ve bilgisayar kimliğini (`computerId`) doğrular.
        *   Başarılı giriş sonrası, sunucudan gelen `expireTime` (son kullanma tarihi) ve `keyCode` ile birlikte kimlik bilgileri şifrelenerek `AppData\Local\GlobalPozitifXMLViewer` klasörüne kaydedilir. Bu, çevrimdışı çalışmayı sağlar.
        *   Oturum süresi dolduğunda veya başka bir cihazdan giriş yapıldığında kullanıcıdan tekrar giriş yapması istenir.
    *   **Argüman İşleme:** Komut satırından (`/fixjnlp`) veya bir XML dosyasına çift tıklanarak (`.xml` dosya yolu) başlatılabilir.

2.  **`LoginForm.cs` (Giriş Formu):**
    *   Kullanıcıdan e-posta ve şifre bilgilerini alır.
    *   Girilen bilgileri `Program.cs`'in bahsettiği web servisine göndererek doğrular.
    *   "Şifremi Unuttum" butonu kullanıcıyı web sitesine yönlendirir.

3.  **`Form1.cs` (Ana Form):**
    *   Uygulamanın ana penceresidir.
    *   **XML Görüntüleme:**
        *   Kullanıcı "Dosya -> Aç" menüsünden veya dosyaları doğrudan pencereye sürükleyip bırakarak XML dosyalarını yükleyebilir.
        *   Yüklenen her XML dosyası yeni bir sekmede açılır.
        *   `XmlProcessor.cs` sınıfını kullanarak XML içeriğini XSLT şablonu ile HTML'e dönüştürür.
        *   Dönüştürülen HTML içeriğini `WebView2` kontrolü ile görüntüler.
    *   **Sekme Yönetimi:** Sekmelerin üzerinde kapatma butonu bulunur.
    *   **Kullanıcı Bilgisi ve Çıkış:** Sağ üstte kullanıcı e-postasını gösterir ve "Çıkış" seçeneği sunar. Çıkış yapıldığında kaydedilen kimlik bilgileri silinir.
    *   **Loglama:** Sağ tarafta bir `TextBox` içinde uygulama içi olayların loglarını gösterir.
    *   **Araçlar Menüsü:** "GİB İmza Aracı Kontrolü" adında bir araç sunar.

4.  **`XmlProcessor.cs` (XML İşleyici):**
    *   Ana görevi, verilen bir XML dosyasını uygun XSLT şablonuyla birleştirmektir.
    *   XML dosyasının içindeki `<?xml-stylesheet ... href="berat.xslt"?>` gibi yönergelere bakarak hangi XSLT dosyasının kullanılacağını belirler.
    *   Gerekli XSLT dosyası (`berat.xslt`, `yevmiye.xslt` vb.) `Resources` klasöründe yoksa, `https://e-defter.globalpozitif.com.tr` adresinden indirir ve yerel olarak kaydeder. Bu, uygulamanın güncel şablonları kullanmasını sağlar.

5.  **`GibCheckerForm.cs` (GİB Kontrol Formu):**
    *   "Araçlar" menüsünden açılır.
    *   Kullanıcının bilgisayarında Gelir İdaresi Başkanlığı'nın (GİB) e-imza uygulamaları için gerekli olan bileşenlerin (Java, AKİS akıllı kart sürücüsü) yüklü olup olmadığını kontrol eder.
    *   **Güvenilir AKİS Kontrolü:** Sürücü kontrolü, program kaldırılsa bile sistemde kalabilen artık dosyalara aldanmamak için, `runAkiA.bat` ve `AKIA_Init.exe` gibi kurulumun temel dosyalarını denetleyerek daha isabetli bir şekilde yapılır.
    *   Ayrıca, `.jnlp` (Java Web Start) dosyalarının doğru şekilde Java ile ilişkilendirilip ilişkilendirilmediğini kontrol eder.
    *   **Kullanıcı Dostu Yardım:** Eğer `.jnlp` dosya ilişkilendirmesi bozuksa, otomatik düzeltme yapmak yerine, kullanıcıya sorunu nasıl çözebileceğini gösteren bir yardım videosuna yönlendiren bir "Yardım" butonu sunar. Bu yaklaşım, Windows güvenlik kısıtlamalarıyla uyumludur ve kullanıcıyı bilgilendirir.

6.  **`Logger.cs` (Loglama Sınıfı):**
    *   Uygulama genelindeki önemli olayları (dosya yükleme, hatalar, API yanıtları vb.) hem ana formdaki `TextBox`'a hem de `AppData\Local\GlobalPozitifXMLViewer\log.txt` dosyasına yazar.
    *   Log dosyası 5MB'ı aştığında otomatik olarak arşivleme (log rotasyonu) yapar.

7.  **`ChatbotService.cs` (Chatbot Hizmeti):**
    *   **Güvenli Web Servis Yaklaşımı:** API anahtarları kullanıcının bilgisayarında saklanmadığı için güvenli bir yaklaşım kullanır.
    *   Chat mesajları `http://l.edefter/?/webservice/chatbot/post/message` adresine gönderilir.
    *   **Kimlik Doğrulama:** Email bilgisi hem status hem de message endpoint'lerine `email` parametresi olarak gönderilir.
    *   **Çevrimdışı Destek:** İnternet olmadığında da email bilgisi yüklenerek chat servisinin başlatılması sağlanır.
    *   Sistem bilgilerini (işletim sistemi mimarisi, Java durumu, AKİS durumu, JNLP ilişkilendirmesi) otomatik olarak web servisine gönderir.
    *   **Teknik Destek Odaklı:** Java kurulumu, AKİS programı sorunları, .jnlp dosya ilişkilendirmesi gibi teknik konularda kullanıcılara yardımcı olur.
    *   **JSON Parsing:** Web servisin yanıt formatını (`data.response`, `remaining_messages`, `remaining_tokens`) doğru şekilde parse eder.
    *   **Kullanıcı Dostu Hata Yönetimi:** JSON formatındaki karışık hata mesajlarını temizleyip kullanıcı dostu mesajlara dönüştürür.
    *   **Akıllı Hata Mesajları:** HTTP hatalarını (timeout, connection refused, 404, 500 vb.) analiz ederek uygun Türkçe açıklamalar sunar.
    *   **Demo Mod Desteği:** Web servis erişilemediğinde kullanıcıya faydalı demo yanıtları döndürür.
    *   **Debug Logging:** Tüm web servis iletişimi detaylı şekilde loglanır.

8.  **`SystemInfoService.cs` (Sistem Bilgisi Hizmeti):**
    *   **İşletim Sistemi Mimarisi Tespiti:** `Environment.Is64BitOperatingSystem` kullanarak 32-bit veya 64-bit mimarisini tespit eder.
    *   Java durumu, AKİS kurulumu ve JNLP ilişkilendirmesi kontrollerini yapar.
    *   Chatbot'a gönderilecek detaylı sistem bilgilerini toplar.
    *   **Kullanıcı Dostu Teknik Destek:** Sistem mimarisine göre doğru Java sürümü veya AKİS kurulum dosyası önerilerinde bulunabilir.

9.  **`ChatbotForm.cs` (Chatbot Arayüzü):**
    *   **Chat Penceresi Tasarımı:** 400x500 piksel boyutunda, kullanıcı dostu bir chat penceresi sunar.
    *   **"Pozitif Teknik Destek Asistanı"** olarak çalışır.
    *   **Açık Rıza Onayı Sistemi:**
        *   Chat asistanı açılmadan önce KVKK uyumlu açık rıza onay formu gösterilir
        *   Kullanıcı onay vermeden chat hizmeti aktif olmaz
        *   Onay bilgisi `https://e-defter.globalpozitif.com.tr/?/webservice/chatbot/post/consent` endpoint'ine gönderilir
        *   Kişisel verilerin işlenmesi ile ilgili detaylı bilgilendirme metni
    *   **Kullanıcı Deneyimi İyileştirmeleri:**
        *   Enter tuşuna basarak mesaj gönderme özelliği
        *   Mesaj gönderilirken "Pozitif Asistan düşünüyor..." animasyonlu bekleme göstergesi
        *   Otomatik focus yönetimi (form açılınca ve mesaj gönderildikten sonra input alanına focus)
        *   Responsive boyutlandırma (minimum 350x400, maksimum 500x700)
    *   **Animasyonlu Feedback:** Yanıt beklenirken nokta animasyonu ile kullanıcıya görsel geri bildirim
    *   Kullanıcının e-posta bilgisini alarak kişiselleştirilmiş destek sağlar.
    *   Sistem bilgilerini otomatik olarak web servisine gönderir, böylece daha isabetli teknik öneriler alabilir.

10. **`ConsentForm.cs` (Açık Rıza Onay Formu):**
    *   **KVKK Uyumlu Onay Sistemi:** 6698 sayılı Kişisel Verilerin Korunması Kanunu'na uygun açık rıza formu
    *   **Detaylı Bilgilendirme:** Yapay zeka hizmeti kapsamında işlenecek veriler hakkında şeffaf bilgilendirme
    *   **Zorunlu Onay Mekanizması:** Checkbox ile açık onay vermeden chat hizmeti aktif olmaz
    *   **Profesyonel Tasarım:** 600x700 piksel boyutunda, kullanıcı dostu arayüz
    *   **İptal/Kabul Seçenekleri:** Kullanıcı dilerse hizmeti reddedebilir
    *   **Veri İşleme Şeffaflığı:** Hangi verilerin, hangi amaçla işleneceği açıkça belirtilir
    *   **Yasal Uyumluluk:** Global Pozitif Teknolojileri gizlilik politikalarına referans

11. **`assistant_prompt.txt` (Asistan Davranış Rehberi):**
    *   **Profesyonel Teknik Destek Promptu:** Web servis tarafında kullanılmak üzere hazırlanmış kapsamlı asistan davranış rehberi
    *   **Rol Tanımı:** Global Pozitif Teknolojileri'nin teknik destek asistanı kimliği
    *   **Teknik Uzmanlık Alanları:** Java, AKİS, JNLP, GİB E-İmza sistemleri konularında uzmanlaşmış rehberlik
    *   **İletişim Kuralları:** Kibar, profesyonel ve yardımsever iletişim standartları
    *   **Sınır Belirleme:** Teknik destek dışındaki konularda konu dışına çıkmama kuralları
    *   **Hazır Yanıt Şablonları:** Konu dışı sorular ve firma bilgileri için önceden hazırlanmış profesyonel yanıtlar
    *   **Sistem Uyumluluğu:** 32-bit/64-bit mimari kontrolüne göre özelleştirilmiş çözüm önerileri
    *   **Örnek Senaryolar:** Gerçek kullanım durumları için somut yanıt örnekleri

## Projenin İş Akışı

1.  Kullanıcı uygulamayı başlatır.
2.  `Program.cs`, mevcut bir oturum olup olmadığını kontrol eder.
3.  Oturum yoksa `LoginForm` gösterilir. Kullanıcı giriş yapar, bilgiler web servisinde doğrulanır ve yerel olarak şifreli bir şekilde saklanır.
4.  Oturum varsa veya giriş başarılıysa `Form1` (ana pencere) açılır.
5.  Kullanıcı bir XML dosyası açar.
6.  `Form1`, `XmlProcessor`'ı çağırır.
7.  `XmlProcessor`, XML'i analiz eder, uygun XSLT'yi bulur (gerekirse indirir) ve XML'i HTML'e dönüştürür.
8.  `Form1`, dönüştürülen HTML'i bir `WebView2` sekmesinde görüntüler.
9.  Kullanıcı dilerse "Araçlar" menüsünden sistemindeki GİB e-imza uyumluluğunu kontrol edebilir.
10. **Teknik Destek:** "Pozitif Asistan" menüsünden chatbot'a erişmek için önce KVKK uyumlu açık rıza onay formu gösterilir.
11. **Açık Rıza Onayı:** Kullanıcı kişisel verilerinin işlenmesi için onay verirse chat penceresi açılır, reddederse hizmet başlatılmaz.
12. Onay bilgisi `https://e-defter.globalpozitif.com.tr/?/webservice/chatbot/post/consent` endpoint'ine gönderilir.
13. Chatbot, kullanıcının sistem bilgilerini otomatik olarak web servisine göndererek kişiselleştirilmiş teknik destek sağlar.
14. **Asistan Davranış Kontrolü:** Web servis, `assistant_prompt.txt` dosyasındaki kurallara göre profesyonel, teknik odaklı yanıtlar üretir.
15. Tüm işlemler `Logger` tarafından loglanır.

## Değerlendirme

Proje, oldukça yetenekli ve kullanıcı dostu bir e-defter görüntüleyici olarak tasarlanmıştır. Hem çevrimiçi hem de çevrimdışı çalışabilmesi, otomatik XSLT güncellemesi, GİB uyumluluk aracı ve **güvenli chatbot teknik destek sistemi** gibi özellikleri onu güçlü kılmaktadır.

### Güvenlik Avantajları:
- **API Anahtarı Güvenliği:** Chatbot API anahtarları kullanıcının bilgisayarında saklanmaz, tüm işlemler web servis üzerinden yapılır.
- **Kimlik Doğrulama:** Email bilgisi her web servis çağrısında gönderilerek kullanıcı kimlik doğrulaması yapılır.
- **Çevrimdışı Esneklik:** İnternet olmadığında da oturum bilgileri yüklenerek chat sistemi başlatılabilir.
- **Kişiselleştirilmiş Destek:** Sistem mimarisini otomatik tespit ederek (32-bit/64-bit) kullanıcıya uygun teknik çözümler önerir.
- **Proaktif Teknik Yardım:** Java, AKİS ve JNLP sorunları için önceden hazırlanmış çözüm önerileri sunar.
- **KVKK Uyumluluğu:** 6698 sayılı Kişisel Verilerin Korunması Kanunu'na uygun açık rıza onay sistemi
- **Veri İşleme Şeffaflığı:** Hangi kişisel verilerin hangi amaçla işleneceği kullanıcıya açık şekilde bildirilir
- **Zorunlu Onay Mekanizması:** Kullanıcı açık rıza vermeden yapay zeka hizmeti aktif olmaz
- **Yasal Uyumluluk:** Global Pozitif Teknolojileri gizlilik politikalarına tam uyum
- **Profesyonel Asistan Davranışı:** `assistant_prompt.txt` dosyası ile web servis tarafında tutarlı, kibar ve teknik odaklı yanıtlar garanti edilir.
- **Sınırlı Kapsam Kontrolü:** Asistan sadece teknik destek konularında çalışır, konu dışı sorularda kibar yönlendirmeler yapar.
- **Akıllı Hata Yönetimi:** JSON formatındaki karışık hata mesajları otomatik olarak temizlenerek kullanıcı dostu Türkçe mesajlara dönüştürülür.
- **Çevrimdışı Esneklik:** İnternet olmadığında da demo modunda faydalı teknik destek sağlanır.
- **Detaylı Loglama:** Tüm web servis iletişimi debug loglarında takip edilebilir.

## Kurulum, Dağıtım ve Sistem Entegrasyonu

Projenin son kullanıcıya ulaştırılması ve sistemle entegrasyonu, Inno Setup kullanılarak hazırlanan `setup.iss` betiği ile yönetilmektedir. Bu betik, uygulamanın dağıtım (deployment) stratejisinin temelini oluşturur.

### 1. Sistem Gereksinimleri ve Bağımlılık Yönetimi

*   **.NET 6.0 Desktop Runtime Zorunluluğu:** Uygulama, çalışmak için **.NET 6.0 Desktop Runtime**'a ihtiyaç duyar.
    *   **Otomatik Kurulum:** Kurulum sihirbazı (`setup.iss`), kullanıcının sisteminde .NET 6.0'ın yüklü olup olmadığını (hem 32-bit hem de 64-bit için) kontrol eder.
    *   Eğer .NET 6.0 yüklü değilse, kurulum sihirbazı gerekli runtime'ı otomatik olarak indirip kurar (`dotnet-runtime-6.0.x-win-x64.exe` veya `...x86.exe`). Bu, son kullanıcının ek bir işlem yapmasına gerek kalmadan uygulamanın sorunsuz çalışmasını sağlar.

### 2. Kurulum Stratejisi

*   **Kullanıcı Bazlı Kurulum:** Uygulama, yönetici (administrator) hakları gerektirmeyecek şekilde tasarlanmıştır (`PrivilegesRequired=lowest`). Kurulum, varsayılan olarak kullanıcıya özel `AppData` klasörüne (`{userappdata}\GlobalPozitifEDefter`) yapılır. Bu yaklaşım, özellikle kısıtlı yetkilere sahip kurumsal ortamlarda dağıtımı kolaylaştırır.
*   **.xml Dosya İlişkilendirmesi:** Kurulum sırasında, `.xml` dosya uzantısı `GlobalPozitifEDefter.xml` dosya türü ile ilişkilendirilir. Bu sayede kullanıcılar, bir XML dosyasına çift tıkladıklarında dosyanın doğrudan bu uygulama ile açılmasını sağlarlar.
*   **Kayıt Defteri (Registry) Yönetimi:**
    *   Uygulamanın "Program Ekle/Kaldır" listesinde doğru şekilde görünmesi için gerekli kayıt defteri girdileri oluşturulur.
    *   Bu girdiler, 32-bit ve 64-bit sistemler için ayrı ayrı ve doğru konumlara (`HKCU\SOFTWARE` ve `HKCU\SOFTWARE\WOW6432Node`) yazılır.

### 3. Temiz ve Güvenli Kaldırma İşlemi

*   **Akıllı Dosya İlişkilendirmesi Temizliği:** Uygulama kaldırılırken, `.xml` dosya ilişkilendirmesinin başka bir program tarafından değiştirilip değiştirilmediği kontrol edilir. Eğer ilişkilendirme hala bu uygulamaya aitse güvenli bir şekilde kaldırılır. Aksi takdirde, kullanıcının diğer programlarının ayarları bozulmasın diye dokunulmaz.
*   **Kapsamlı Temizlik:** Kaldırma işlemi, sadece program dosyalarını değil, aynı zamanda aşağıdaki verileri de sistemden temizler:
    *   Kullanıcının `AppData\Local` ve `AppData\Roaming` klasörlerinde oluşturulan uygulama verileri (`log.txt`, `encrypted_credentials.txt` vb.).
    *   Uygulama ile ilgili tüm kayıt defteri anahtarları.
    *   Oluşturulan masaüstü ve başlat menüsü kısayolları.

Bu strateji, uygulamanın kullanıcının sistemine temiz bir şekilde kurulmasını ve kaldırıldığında geride gereksiz dosya veya kayıt bırakmamasını garanti altına alır.

