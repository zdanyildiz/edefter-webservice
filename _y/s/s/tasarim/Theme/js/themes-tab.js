/**
 * Themes Tab JavaScript - Theme Editor
 * Hazır temalar sekmesi için JavaScript kodları
 * jQuery yüklendikten sonra çalıştırılacak
 */

// Hazır tema verileri
function initializeThemesTab() {
    console.log('Initializing Themes Tab...');
    
    // Tema kartları için event handler
    $('.theme-card').click(function() {
        $('.theme-card').removeClass('active');
        $(this).addClass('active');
        
        const theme = $(this).data('theme');
        // updateThemePreview(theme); // Bu satır kaldırıldı
    });
        
    // Tema uygulama butonları
    $('.apply-theme-btn').click(function(e) {
        e.stopPropagation();
        const themeName = $(this).data('theme');
        applyPredefinedTheme(themeName);
    });
        
    // İlk tema kartını aktif yap ve önizleme güncelle (Bu satırlar kaldırıldı)
    // $('.theme-card:first').addClass('active');
    // updateThemePreview('google-material');

    // Tema export butonları
    $('#export-theme-btn').click(function() {
        exportCurrentTheme();
    });

    // Tema import input change handler
    $('#import-theme-file').change(function() {
        importThemeFromFile(this.files[0]);
    });
}

function applyPredefinedTheme(themeName) {
    if (!window.themeEditor || typeof window.themeEditor.applyColorTheme !== 'function') {
        console.error('ThemeEditor instance veya applyColorTheme fonksiyonu bulunamadı.');
        return;
    }

    // Modalı göster (Bootstrap 3/4 uyumlu)
    $('#themeApplyConfirmModal').modal('show');

    // Onay butonuna click eventi ekle
    $('#confirmThemeApplyBtn').off('click').on('click', function() {
        $('#themeApplyConfirmModal').modal('hide'); // Modalı kapat
        window.themeEditor.applyColorTheme(themeName);
    });
}

function exportCurrentTheme() {
    const formData = {};
    
    // Tüm form değerlerini topla
    $('#theme-form').find('input, select, textarea').each(function() {
        const $this = $(this);
        const name = $this.attr('name');
        const value = $this.val();
        
        if (name && value) {
            formData[name] = value;
        }
    });
    
    const themeData = {
        name: prompt('Tema adını girin:') || 'custom-theme',
        version: '1.0',
        colors: formData,
        timestamp: new Date().toISOString()
    };
    
    // JSON dosyası olarak indir
    const dataStr = JSON.stringify(themeData, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = themeData.name + '-theme.json';
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
    
    showThemeNotification('Tema başarıyla dışa aktarıldı!', 'success');
}

function importThemeFromFile(file) {
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const themeData = JSON.parse(e.target.result);
            
            if (!themeData.colors) {
                alert('Geçersiz tema dosyası!');
                return;
            }
            
            // Onay iste
            const themeName = themeData.name || 'imported-theme';
            if (!confirm(`${themeName} temasını içe aktarmak istediğinizden emin misiniz?\n\nMevcut ayarlarınız değişecektir.`)) {
                return;
            }
            
            // Tema değerlerini form alanlarına uygula
            Object.keys(themeData.colors).forEach(key => {
                const input = $(`input[name="${key}"]`);
                if (input.length) {
                    input.val(themeData.colors[key]);
                    // Color picker için özel tetikleme
                    if (input.hasClass('color-picker')) {
                        input.trigger('input');
                    }
                }
            });
            
            // Preview'ı güncelle
            if (window.themeEditor && window.themeEditor.updatePreview) {
                window.themeEditor.updatePreview();
            }
            
            showThemeNotification('Tema başarıyla içe aktarıldı!', 'success');
            
            // Dosya inputunu temizle
            const fileInput = document.getElementById('import-theme-file');
            if (fileInput) fileInput.value = '';
            
        } catch (error) {
            alert('Geçersiz JSON dosyası!');
        }
    };
    
    reader.readAsText(file);
}

function showThemeNotification(message, type) {
    if (window.themeEditor && typeof window.themeEditor.showNotification === 'function') {
        window.themeEditor.showNotification(message, type);
    } else {
        console.warn('showNotification: ThemeEditor instance not found');
        alert(message); // Fallback for notification
    }
}
