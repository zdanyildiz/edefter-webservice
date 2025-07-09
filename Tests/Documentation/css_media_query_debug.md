# CSS Custom Properties Media Query Test

## Browser DevTools'da Test Etme

### 1. F12 ile DevTools'u açın
### 2. Console'da şu komutları çalıştırın:

```javascript
// CSS değişkenini kontrol et
getComputedStyle(document.documentElement).getPropertyValue('--tablet-breakpoint')

// Media query'yi test et
window.matchMedia('(max-width: 992px)').matches

// Viewport genişliğini kontrol et
window.innerWidth
```

### 3. Elements sekmesinde :root kontrol et
- :root elemanını bulun
- --tablet-breakpoint değerini kontrol edin
- Computed sekmesinde değerin doğru hesaplandığını kontrol edin

## Tarayıcı Desteği Kontrolü

### CSS Custom Properties (CSS Variables) Desteği:
- ✅ Chrome 49+
- ✅ Firefox 31+
- ✅ Safari 9.1+
- ✅ Edge 16+
- ❌ IE 11 (Kısmi destek)

### Media Query içinde CSS Variables:
- ✅ Chrome 88+
- ✅ Firefox 31+
- ✅ Safari 9.1+
- ✅ Edge 88+

## Test Senaryoları

### 1. Statik Değer Test
```css
/* Bu çalışıyor mu? */
@media screen and (max-width: 992px) {
    .test { color: red; }
}
```

### 2. CSS Variable Test
```css
/* Bu çalışıyor mu? */
@media screen and (max-width: var(--tablet-breakpoint)) {
    .test { color: blue; }
}
```

### 3. Alternatif Syntax Test
```css
/* Bu çalışıyor mu? */
@media (max-width: var(--tablet-breakpoint)) {
    .test { color: green; }
}
```

## Debugging Adımları

1. **Viewport Meta Tag Kontrolü**
2. **CSS Yükleme Sırası Kontrolü**
3. **CSS Syntax Hatası Kontrolü**
4. **Tarayıcı Cache Temizliği**
5. **Responsive Design Mode Testi**
