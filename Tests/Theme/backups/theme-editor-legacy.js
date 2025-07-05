/**
 * Legacy Tema Düzenleyici JavaScript
 * Theme.php sayfası için eski JavaScript fonksiyonları
 * Bu dosya sadece geriye dönük uyumluluk için korunmaktadır.
 * Yeni ThemeEditor class'ı Theme/js/core.js dosyasında tanımlanmıştır.
 */

// Legacy değişkenler
var legacyThemeEditor = {
    currentTheme: {},
    previewWindow: null,
    unsavedChanges: false
};

// Legacy fonksiyonlar
function legacyBindEvents() {
    // Eski event binding'ler
    console.log('Legacy events bound');
}

function legacyLoadCurrentTheme() {
    // Eski tema yükleme
    console.log('Legacy theme loading');
}

function legacyUpdatePreview() {
    // Eski önizleme güncelleme
    console.log('Legacy preview update');
}

// Geriye dönük uyumluluk için alias'lar
if (typeof window.ThemeEditor === 'undefined') {
    window.ThemeEditor = {
        init: legacyBindEvents,
        loadTheme: legacyLoadCurrentTheme,
        updatePreview: legacyUpdatePreview
    };
}

console.log('Legacy theme editor loaded');
