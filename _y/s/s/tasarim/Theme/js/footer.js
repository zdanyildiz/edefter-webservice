/**
 * Footer Preview Functions
 * Footer önizleme fonksiyonları
 */

// ThemeEditor sınıfına footer fonksiyonları ekle
if (typeof ThemeEditor !== 'undefined') {
    
    // Footer önizleme güncelleme fonksiyonu
    ThemeEditor.prototype.updateFooterPreview = function(formData) {
        console.log('🦶 updateFooterPreview called', formData);
        
        const root = document.documentElement;
        const keysToUpdate = [
            'footer-background-color', 'footer-text-color', 'footer-link-color', 'footer-link-hover-color',
            'copyright-background-color', 'copyright-text-color', 'copyright-link-color', 'copyright-border-top-color',
            'social-icon-color', 'social-icon-hover-color', 'social-icon-size',
            'footer-padding-y', 'footer-font-size', 'copyright-padding'
        ];

        keysToUpdate.forEach(key => {
            if (formData[key] !== undefined) {
                let value = formData[key];
                // Add 'px' unit to numeric values that need it
                if (['social-icon-size', 'footer-padding-y', 'footer-font-size', 'copyright-padding'].includes(key)) {
                    if (String(value).trim() !== '' && !isNaN(parseFloat(value))) {
                        value = `${parseFloat(value)}px`;
                    }
                }
                root.style.setProperty(`--${key}`, value);
            }
        });
        
        console.log('✅ Footer preview güncellendi');
    };

} else {
    console.error('❌ footer.js:ThemeEditor sınıfı bulunamadı! core.js yüklenmiş mi?');
}

console.log('✅ footer.js yüklendi - Footer önizleme sistemleri hazır');
