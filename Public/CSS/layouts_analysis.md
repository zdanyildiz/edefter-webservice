### CSS Değişken Kullanım Analizi

Aşağıda, `Public/CSS/Layouts/` dizinindeki her bir CSS dosyasının `index.css` içinde tanımlanan global değişkenleri ne kadar iyi kullandığına dair bir analiz bulunmaktadır.

---

#### 1. `aside_left.css`

*   **Durum:** Değişken kullanımı çok az.
*   **Tespitler:**
    *   Renkler (`#fff`, `#000`, `#ccc`), gölgeler (`box-shadow`), genişlik (`max-width: 400px`) ve geçiş efektleri (`transition`) için sabit değerler kullanılmış.
    *   Yazı tipi boyutları (`16px`, `14px`, `12px`) sabit olarak tanımlanmış.
*   **Öneri:**
    *   `background-color`, `color`, `border-color` gibi özellikler için `--content-bg-color`, `--text-primary-color`, `--border-color` gibi değişkenler kullanılmalıdır.
    *   `transition` için `--transition-speed` ve `--transition-timing` değişkenleri kullanılmalıdır.
    *   Yazı tipi boyutları için `--font-size-normal`, `--font-size-small` gibi değişkenler tercih edilmelidir.

---

#### 2. `aside_right.css`

*   **Durum:** Kısmi değişken kullanımı mevcut.
*   **Tespitler:**
    *   Buton renkleri (`--button-text-color`, `--button-color`) ve bazı metin renkleri için değişkenler doğru bir şekilde kullanılmış.
    *   Ancak, arka plan (`#fff`), gölge rengi (`#ccc`), geçiş efektleri ve bazı metin renkleri (`red`, `#666`) hala sabit.
    *   Kaydırma çubuğu (scrollbar) renkleri sabit olarak kodlanmış.
*   **Öneri:**
    *   Arka plan, kenarlık ve gölge renkleri için değişkenler kullanılmalıdır.
    *   `transition` özelliği değişkenlerle standartlaştırılmalıdır.
    *   Kaydırma çubuğu renkleri için `--accent-color` ve `--secondary-color` gibi değişkenler kullanılabilir.

---

#### 3. `aside_right_visitor.css`

*   **Durum:** Değişken kullanımı yok.
*   **Tespitler:**
    *   Tüm renkler (`#fff`, `#000`, `#ddd`, `#f5f5f5`), geçiş efektleri ve yazı tipi boyutları sabit değerlerdir.
*   **Öneri:**
    *   Bu dosyadaki tüm stil kuralları, `index.css`'teki renk, yazı tipi ve geçiş değişkenleri kullanılarak yeniden düzenlenmelidir.

---

#### 4. `assistant.css`

*   **Durum:** Değişken kullanımı yok.
*   **Tespitler:**
    *   Renkler, gölgeler, yazı tipi boyutları ve medya sorgusu (`@media screen and (max-width: 960px)`) için sabit değerler kullanılıyor.
*   **Öneri:**
    *   `960px` breakpoint'i için `--tablet-breakpoint` değişkeni kullanılmalıdır.
    *   Tüm renk ve yazı tipi değerleri değişkenlerle değiştirilmelidir.

---

#### 5. `body.css`

*   **Durum:** Çok iyi. Değişkenler etkin bir şekilde kullanılıyor.
*   **Tespitler:**
    *   Ana gövde, metin ve bağlantı renkleri için değişkenler doğru bir şekilde atanmış.
    *   Kaydırma çubuğu stilleri değişkenleri kullanıyor.
*   **Öneri:**
    *   `@media screen and (max-width:1024px)` medya sorgusu, `--tablet-breakpoint` veya `--desktop-breakpoint` gibi standart bir değişken kullanmalıdır.

---

#### 6. `file.css`

*   **Durum:** Kısmi değişken kullanımı mevcut.
*   **Tespitler:**
    *   `--font-size-small` ve `--accent-color` gibi değişkenler kullanılmış, bu harika.
    *   Ancak `box-shadow` içindeki renk (`#ccc`) ve `rgba(0, 0, 0, 0.33)` sabit olarak bırakılmış.
*   **Öneri:**
    *   Gölge renkleri için de değişkenler tanımlanıp kullanılabilir.

---

#### 7. `footer.css`

*   **Durum:** Çok iyi. Değişkenler geniş çapta kullanılmış.
*   **Tespitler:**
    *   Footer arka planı, metin ve bağlantı renkleri için değişkenler doğru bir şekilde uygulanmış.
*   **Öneri:**
    *   `@media (max-width: 960px)` medya sorgusu `--tablet-breakpoint` değişkenini kullanmalıdır.
    *   Animasyon içindeki renk (`#2471aa`) bir değişkene atanabilir.

---

#### 8. `gallery.css`

*   **Durum:** Çok iyi.
*   **Tespitler:**
    *   Arka plan, metin ve kenarlık renkleri için değişkenler doğru bir şekilde kullanılmış. Bu dosya, değişken kullanımına iyi bir örnektir.

---

#### 9. `header.css`

*   **Durum:** Kısmi değişken kullanımı mevcut ancak iyileştirme potansiyeli yüksek.
*   **Tespitler:**
    *   Input, buton ve ikon renkleri gibi birçok alanda değişkenler kullanılmış.
    *   Ancak, medya sorgularındaki breakpoint'ler (`1360px`, `960px`) sabit.
    *   Logo genişliği, header yüksekliği gibi birçok boyutlandırma değeri sabit. `index.css` içinde bu değerler için `--header-logo-width` gibi değişkenler olmasına rağmen burada kullanılmamış.
*   **Öneri:**
    *   Tüm medya sorguları (`@media`) breakpoint değişkenlerini (`--tablet-breakpoint`, `--desktop-breakpoint` vb.) kullanmalıdır.
    *   Logo ve header boyutları gibi değerler için ilgili değişkenler kullanılmalıdır.

---

#### 10. `main.css`

*   **Durum:** Değişken kullanımı yok.
*   **Tespitler:**
    *   WhatsApp butonu için kullanılan tüm renkler (`lawngreen`, `#128C7E`, `lightgreen`) sabit.
*   **Öneri:**
    *   Bu renkler, markanın renk paletine uygun değişkenlerle değiştirilmelidir.

---

#### 11. `nav-footer.css`

*   **Durum:** İyi.
*   **Tespitler:**
    *   Menü renkleri ve yazı tipleri için değişkenler kullanılmış.
*   **Öneri:**
    *   `@media (max-width: 960px)` sorgusu `--tablet-breakpoint` değişkenini kullanmalıdır.
    *   Kaydırma çubuğu renkleri sabit, değişkenlerle değiştirilmelidir.

---

#### 12. `nav-main.css`

*   **Durum:** Çok iyi, ancak küçük iyileştirmeler yapılabilir.
*   **Tespitler:**
    *   Menü ve alt menülerin stillendirmesinde değişkenler yoğun bir şekilde kullanılmış. Bu, en iyi örneklerden biri.
*   **Öneri:**
    *   Medya sorgularındaki breakpoint'ler (`1680px`, `1340px`, `1280px`) standart değişkenlerle (`--breakpoint-xxl`, `--desktop-breakpoint`) değiştirilebilir veya bu değerler için yeni değişkenler oluşturulabilir.

---

#### 13. `nav-top.css`

*   **Durum:** İyileştirilebilir.
*   **Tespitler:**
    *   `@media screen and (max-width: 1024px)` için sabit bir değer kullanılmış.
*   **Öneri:**
    *   Breakpoint, `--tablet-breakpoint` veya `--desktop-breakpoint` ile değiştirilmelidir.

---

#### 14. `newsLetter.css`

*   **Durum:** Değişken kullanımı yok.
*   **Tespitler:**
    *   Arka plan, metin, kenarlık ve buton renklerinin tamamı sabit.
*   **Öneri:**
    *   Tüm stil özellikleri, `index.css`'teki değişkenler (buton, input, renk vb.) kullanılarak yeniden yazılmalıdır.

---

#### 15. `video.css`

*   **Durum:** İyi.
*   **Tespitler:**
    *   Arka plan rengi ve maksimum genişlik için değişkenler kullanılmış.
*   **Öneri:**
    *   `box-shadow` içindeki renk (`#ccc`) bir değişkenle değiştirilebilir.

### Genel Sonuç ve Eylem Planı

**Genel Durum:** Projedeki CSS dosyalarında değişken kullanımı konusunda bir standartlaşma eksikliği bulunmaktadır. `body.css`, `footer.css` ve `nav-main.css` gibi bazı dosyalar değişkenleri etkin bir şekilde kullanırken, `aside_left.css`, `newsLetter.css` gibi diğerleri neredeyse hiç kullanmamaktadır.

**Eylem Planı:**

1.  **Standardizasyon:** Tüm dosyalardaki sabit renk, yazı tipi, boşluk ve breakpoint değerleri `index.css`'teki değişkenlerle değiştirilmelidir.
2.  **Breakpoint'leri Güncelleme:** Tüm `@media` sorguları, `--mobile-breakpoint`, `--tablet-breakpoint` gibi standart değişkenleri kullanmalıdır.
3.  **Yeni Değişkenler:** Gerekli görülen yerlerde (örneğin, gölge renkleri, özel animasyon renkleri) yeni global değişkenler oluşturulabilir.

Bu analiz, projenin CSS altyapısını daha modüler, bakımı kolay ve tutarlı hale getirmek için bir yol haritası sunmaktadır.

