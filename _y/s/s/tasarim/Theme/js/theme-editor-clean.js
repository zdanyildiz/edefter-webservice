/**
 * Theme Editor Legacy Functions
 * Geriye donuk uyumluluk icin legacy fonksiyonlar
 * Ana logic artik Theme/js/core.js'de
 */

// Legacy global functions - backward compatibility
function updateHeaderPreview() {
    if (window.themeEditor && typeof window.themeEditor.updateHeaderPreview === 'function') {
        return window.themeEditor.updateHeaderPreview();
    }
    console.warn('updateHeaderPreview: ThemeEditor instance not found');
}

function updateMenuPreview() {
    if (window.themeEditor && typeof window.themeEditor.updateMenuPreview === 'function') {
        return window.themeEditor.updateMenuPreview();
    }
    console.warn('updateMenuPreview: ThemeEditor instance not found');
}

function updateProductPreview() {
    if (window.themeEditor && typeof window.themeEditor.updateProductPreview === 'function') {
        return window.themeEditor.updateProductPreview();
    }
    console.warn('updateProductPreview: ThemeEditor instance not found');
}

function updateFormPreview() {
    if (window.themeEditor && typeof window.themeEditor.updateFormPreview === 'function') {
        return window.themeEditor.updateFormPreview();
    }
    console.warn('updateFormPreview: ThemeEditor instance not found');
}

function updateBannerPreview() {
    if (window.themeEditor && typeof window.themeEditor.updateBannerPreview === 'function') {
        return window.themeEditor.updateBannerPreview();
    }
    console.warn('updateBannerPreview: ThemeEditor instance not found');
}

function saveTheme() {
    if (window.themeEditor && typeof window.themeEditor.saveTheme === 'function') {
        return window.themeEditor.saveTheme();
    }
    console.warn('saveTheme: ThemeEditor instance not found');
}

function previewTheme() {
    if (window.themeEditor && typeof window.themeEditor.previewTheme === 'function') {
        return window.themeEditor.previewTheme();
    }
    console.warn('previewTheme: ThemeEditor instance not found');
}

function resetTheme() {
    if (window.themeEditor && typeof window.themeEditor.resetTheme === 'function') {
        return window.themeEditor.resetTheme();
    }
    console.warn('resetTheme: ThemeEditor instance not found');
}

function openPreview(mobile) {
    if (window.themeEditor && typeof window.themeEditor.openPreview === 'function') {
        return window.themeEditor.openPreview(mobile);
    }
    console.warn('openPreview: ThemeEditor instance not found');
}

// Core.js yuklendikten sonra legacy functions setup
$(document).ready(function() {
    console.log('Theme Editor Legacy Functions yuklendi');
    
    // 1 saniye bekle ki core.js yuklenmis olsun
    setTimeout(function() {
        // Core.js'den ThemeEditor sinifini bekle
        if (typeof ThemeEditor !== 'undefined') {
            window.themeEditor = new ThemeEditor();
            console.log('Legacy theme-editor.js: ThemeEditor instance olusturuldu');
        } else {
            console.error('ThemeEditor class not found! Core.js yuklenmis mi?');
        }
    }, 1000);
});

console.log('Theme Editor Legacy Functions yuklendi');
