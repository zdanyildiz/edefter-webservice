/**
 * Responsive Preview Functions
 * Responsive ayarlarının önizleme fonksiyonları
 */

if (typeof ThemeEditor !== 'undefined') {

    ThemeEditor.prototype.updateResponsivePreview = function(formData) {
        console.log('📱 updateResponsivePreview called', formData);

        const root = document.documentElement;
        const keysToUpdate = [
            'mobile-breakpoint', 'tablet-breakpoint', 'desktop-breakpoint',
            'mobile-container-padding', 'tablet-container-padding', 'desktop-max-width',
            'mobile-base-font-size', 'mobile-h1-font-size', 'mobile-line-height',
            'mobile-section-margin', 'mobile-card-margin', 'mobile-button-height',
            'touch-target-size'
        ];

        keysToUpdate.forEach(key => {
            if (formData[key] !== undefined) {
                let value = formData[key];
                if (['mobile-breakpoint', 'tablet-breakpoint', 'desktop-breakpoint',
                     'mobile-container-padding', 'tablet-container-padding', 'desktop-max-width',
                     'mobile-base-font-size', 'mobile-h1-font-size',
                     'mobile-section-margin', 'mobile-card-margin', 'mobile-button-height',
                     'touch-target-size'].includes(key)) {
                    if (String(value).trim() !== '' && !isNaN(parseFloat(value))) {
                        value = `${parseFloat(value)}px`;
                    }
                }
                root.style.setProperty(`--${key}`, value);
            }
        });

        // Checkbox değerlerini güncelle
        root.style.setProperty('--hide-banner-mobile', formData['hide-banner-mobile'] ? 'none' : 'block');
        root.style.setProperty('--hide-sidebar-mobile', formData['hide-sidebar-mobile'] ? 'none' : 'block');
        root.style.setProperty('--hide-breadcrumb-mobile', formData['hide-breadcrumb-mobile'] ? 'none' : 'block');

        console.log('✅ Responsive preview güncellendi');
    };

    // Responsive önizleme cihaz değiştirme
    $(document).on('click', '.responsive-preview-tabs .btn', function() {
        const device = $(this).data('device');
        $('.responsive-preview-tabs .btn').removeClass('active');
        $(this).addClass('active');

        $('.preview-frame').removeClass('active');
        $(`.preview-frame.${device}-frame`).addClass('active');

        console.log(`Cihaz önizlemesi değiştirildi: ${device}`);
    });

} else {
    console.error('❌ responsive.js: ThemeEditor sınıfı bulunamadı! core.js yüklenmiş mi?');
}

console.log('✅ responsive.js yüklendi - Responsive önizleme sistemleri hazır');
