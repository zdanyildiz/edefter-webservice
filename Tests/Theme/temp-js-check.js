// Theme.php JavaScript Kısmının Ayrı Kontrolü
$(document).ready(function() {
    console.log('Theme.php DOM ready - Tab sistemi başlatılıyor...');
    
    // Menü aktif hale getirme
    $("#themephp").addClass("active");
    
    // Dil değişikliği
    $('#languageSelect').change(function() {
        window.location.href = '?languageID=' + $(this).val();
    });
    
    // Sayfa yüklendiğinde değer kontrolü
    validateAllInputs();
    
    // Bootstrap tabs manuel başlatma
    try {
        $('#themeTabs button[data-toggle="tab"]').tab();
    } catch(e) {
        console.log('Bootstrap tab plugin bulunamadı, manuel başlatma yapılıyor...');
        // Manuel tab sistemi
        $('#themeTabs button[data-toggle="tab"]').click(function(e) {
            e.preventDefault();
            
            // Tüm tabları deaktive et
            $('#themeTabs .nav-link').removeClass('active');
            $('.tab-pane').removeClass('active in show');
            
            // Tıklanan tab'ı aktive et
            $(this).addClass('active');
            const target = $(this).attr('data-target');
            $(target).addClass('active in');
            
            console.log('Manual tab switched to:', target);
        });
    }
    
    // Tab değişikliği olaylarını dinle
    $('#themeTabs button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        console.log('Tab changed to:', e.target.id);
        // İlgili tab'a gözel işlemler yapılabilir
    });
    
    // Bootstrap colorpicker'ı devre dışı bırak - Sadece HTML5 color input kullan
    $('.color-picker').off('colorpicker');
    
    // İlk sekmeyi aktif yap
    $('.nav-tabs .nav-link:first').addClass('active');
    $('.tab-content .tab-pane:first').addClass('active in');
    
    // Tab değişikliklerini yönet
    $('.nav-tabs .nav-link').click(function(e) {
        e.preventDefault();
        
        const target = $(this).attr('data-target');
        
        // Tüm sekmeleri pasif yap
        $('.nav-tabs .nav-link').removeClass('active');
        $('.tab-pane').removeClass('active in show');
        
        // Seçilen sekmeyi aktif yap
        $(this).addClass('active');
        $(target).addClass('active in');
        
        console.log('Tab switched to:', target);
    });
      // Form başlatma
    validateAllInputs();
    
    // ThemeEditor theme-editor.js tarafından başlatılacak
    // İlk yüklemede önizlemeyi güncelle
    if (typeof window.themeEditorInstance !== 'undefined') {
        setTimeout(() => {
            window.themeEditorInstance.updatePreview();
            console.log('🚀 İlk yüklemede önizleme güncellendi');
        }, 500);
    }
});

// Renk input değerlerini kontrol et ve düzelt
function validateColorInputs() {
    $('.color-picker').each(function() {
        const $input = $(this);
        let value = $input.val();
        
        // Boş değer kontrolü
        if (!value || value.trim() === '') {
            value = '#ffffff';
        }
        
        // # işareti yoksa ekle
        if (value && !value.startsWith('#')) {
            value = '#' + value;
        }
        
        // Geçersiz değerleri düzelt
        if (!isValidHexColor(value)) {
            const fallbackColor = $input.data('fallback') || '#ffffff';
            $input.val(fallbackColor);
            console.log('Fixed invalid color value:', value, 'to', fallbackColor);
        } else if ($input.val() !== value) {
            $input.val(value);
            console.log('Formatted color value:', $input.val(), 'to', value);
        }
    });
}

// Sayısal input değerlerini kontrol et ve düzelt
function validateNumericInputs() {
    $('input[type="number"]').each(function() {
        const $input = $(this);
        const value = $input.val();
        
        // Geçersiz değerleri düzelt
        if (value && isNaN(parseFloat(value))) {
            const fallbackValue = $input.data('fallback') || '0';
            $input.val(fallbackValue);
            console.log('Fixed invalid numeric value:', value, 'to', fallbackValue);
        }
    });
}

// Tüm input'ları kontrol et
function validateAllInputs() {
    validateColorInputs();
    validateNumericInputs();
}
  // Hex renk doğrulama
function isValidHexColor(hex) {
    return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
}

// Debug: Header Preview Toggle Test
$(document).ready(function() {
    console.log('🔍 Header Preview Toggle Debug Başlatıldı');
    
    // Buton kontrolü
    const headerBtn = $('#toggleHeaderPreview');
    const mobileBtn = $('#toggleMobileHeaderPreview');
    
    console.log('📋 Buton kontrolü:', {
        headerBtn: headerBtn.length,
        mobileBtn: mobileBtn.length,
        themeEditor: typeof window.themeEditorInstance
    });
      // Manuel test event'leri - KALDIRILDI (Çakışma yaratıyordu)
    /*
    $('#toggleHeaderPreview').on('click', function(e) {
        e.preventDefault();
        console.log('🖱️ MANUEL: Desktop header buton tıklandı');
        
        if (window.themeEditorInstance) {
            window.themeEditorInstance.toggleHeaderPreview('desktop');
        } else {
            console.error('❌ themeEditorInstance bulunamadı!');
        }
    });
    
    $('#toggleMobileHeaderPreview').on('click', function(e) {
        e.preventDefault();
        console.log('🖱️ MANUEL: Mobile header buton tıklandı');
        
        if (window.themeEditorInstance) {
            window.themeEditorInstance.toggleHeaderPreview('mobile');
        } else {
            console.error('❌ themeEditorInstance bulunamadı!');
        }
    });
    */
      // 2 saniye sonra instance kontrolü
    setTimeout(() => {
        console.log('⏰ Gecikmeli instance kontrolü:', {
            themeEditorInstance: typeof window.themeEditorInstance,
            methods: window.themeEditorInstance ? Object.getOwnPropertyNames(Object.getPrototypeOf(window.themeEditorInstance)) : 'N/A'
        });
    }, 2000);
    
    // BASIT TEST FONKSİYONU
    window.testHeaderPin = function() {
        console.log('🧪 TEST: Header pin test başlatıldı');
        const $card = $('#headerPreviewCard');
        
        console.log('📋 Test card durumu:', {
            exists: $card.length,
            visible: $card.is(':visible'),
            classes: $card.attr('class')
        });
        
        // Manuel olarak fixed class ekle
        $card.addClass('header-preview-fixed');
        $('body').addClass('header-preview-pinned');
        
        console.log('✅ Manuel fixed class eklendi');
        
        setTimeout(() => {
            console.log('⏰ 3 saniye sonra durum:', {
                hasFixedClass: $card.hasClass('header-preview-fixed'),
                position: $card.css('position'),
                top: $card.css('top'),
                zIndex: $card.css('z-index')
            });
        }, 3000);
    };
      console.log('🧪 Test fonksiyonu hazır: window.testHeaderPin()');
});

// ==========================================
// TAB MODÜL JAVASCRIPT KODLARI - KONSOLIDE
// ==========================================

// Banner Tab JavaScript
function initBannersTab() {
    // Opacity slider değerlerini güncelle
    $('input[name="banner-overlay-opacity"]').on('input', function() {
        $('#overlay-opacity-value').text($(this).val());
    });
    
    $('input[name="card-shadow-opacity"]').on('input', function() {
        $('#shadow-opacity-value').text($(this).val());
    });
}

// Forms Tab JavaScript  
function initFormsTab() {
    // Form önizleme interaktif öğeler
    $('.preview-input, .preview-textarea').focus(function() {
        $('.error-message').hide();
        $('.success-message').hide();
    });
    
    $('.btn-primary-preview').click(function() {
        $('.error-message').hide();
        $('.success-message').show().delay(3000).fadeOut();
    });
    
    $('.btn-secondary-preview').click(function() {
        $('.success-message').hide();
        $('.error-message').show().delay(3000).fadeOut();
    });
    
    $('.btn-outline-preview').click(function() {
        $('.preview-input, .preview-textarea').val('');
        $('.error-message, .success-message').hide();
    });
}

// Responsive Tab JavaScript
function initResponsiveTab() {
    // Responsive preview device switcher
    $('.responsive-preview-tabs .btn').click(function() {
        const device = $(this).data('device');
        
        // Button states
        $('.responsive-preview-tabs .btn').removeClass('active');
        $(this).addClass('active');
        
        // Frame visibility
        $('.preview-frame').removeClass('active');
        $(`.${device}-frame`).addClass('active');
    });
}

// Themes Tab JavaScript
function initThemesTab() {
    // Tema kartlarına tıklama olayı
    $('.theme-card').click(function() {
        $('.theme-card').removeClass('active');
        $(this).addClass('active');
        
        const theme = $(this).data('theme');
        // updateThemePreview(theme); // Bu fonksiyon daha sonra eklenecek
    });
    
    // Tema uygulama butonları
    $('.apply-theme-btn').click(function(e) {
        e.stopPropagation();
        const themeName = $(this).data('theme');
        // applyPredefinedTheme(themeName); // Bu fonksiyon daha sonra eklenecek
        console.log('Theme apply:', themeName);
    });
    
    // İlk tema kartını aktif yap
    $('.theme-card:first').addClass('active');
}

// Tüm tab modüllerini başlat
$(document).ready(function() {
    console.log('🔧 Tab modülleri başlatılıyor...');
    
    // 1 saniye gecikme ile tab modüllerini başlat (DOM hazır olmasını bekle)
    setTimeout(() => {
        initBannersTab();
        initFormsTab(); 
        initResponsiveTab();
        initThemesTab();
        console.log('✅ Tüm tab modülleri başlatıldı');
    }, 1000);
});

// Global Theme Functions (Placeholder)
window.exportCurrentTheme = function() {
    console.log('📤 Export theme - yakında eklenecek');
};

window.importThemeFile = function() {
    console.log('📥 Import theme - yakında eklenecek');
};

window.applyPredefinedTheme = function(themeName) {
    console.log('🎨 Apply theme:', themeName, '- yakında eklenecek');
};
