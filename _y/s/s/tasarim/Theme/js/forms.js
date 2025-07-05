/**
 * Forms Preview Functions
 * Form ve buton önizleme fonksiyonları
 */

// ThemeEditor sınıfına form fonksiyonları ekle
if (typeof ThemeEditor !== 'undefined') {
    
    // Form önizleme güncelleme fonksiyonu
    ThemeEditor.prototype.updateFormPreview = function(formData) {
        console.log('📝 updateFormPreview called', formData);
        
        const root = document.documentElement;
        const keysToUpdate = [
            'input-bg-color', 'input-border-color', 'input-focus-border-color', 'input-text-color',
            'input-placeholder-color', 'btn-primary-bg-color', 'btn-primary-text-color',
            'btn-primary-hover-bg-color', 'btn-primary-border-color', 'btn-secondary-bg-color',
            'btn-secondary-text-color', 'btn-secondary-hover-bg-color', 'btn-outline-color',
            'form-label-color', 'form-required-color', 'form-error-color', 'form-success-color',
            'input-height', 'input-padding', 'input-border-radius', 'btn-padding-y', 'btn-padding-x'
        ];

        keysToUpdate.forEach(key => {
            if (formData[key] !== undefined) {
                let value = formData[key];
                // Add 'px' unit to numeric values that need it
                if (['input-height', 'input-padding', 'input-border-radius', 'btn-padding-y', 'btn-padding-x'].includes(key)) {
                    if (String(value).trim() !== '' && !isNaN(parseFloat(value))) {
                        value = `${parseFloat(value)}px`;
                    }
                }
                root.style.setProperty(`--${key}`, value);
            }
        });
        
        console.log('✅ Form preview güncellendi');
    };

} else {
    console.error('❌ form.js: ThemeEditor sınıfı bulunamadı! core.js yüklenmiş mi?');
}

console.log('✅ forms.js yüklendi - Form önizleme sistemleri hazır');
