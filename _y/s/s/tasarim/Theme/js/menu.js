/**
 * Menu Preview Functions
 * Menü ve Mobile Menü önizleme fonksiyonları
 */

// ThemeEditor sınıfına menü fonksiyonları ekle
if (typeof ThemeEditor !== 'undefined') {
    
    // Menü önizleme güncelleme fonksiyonu
    ThemeEditor.prototype.updateMenuPreview = function(formData) {
        console.log('🔧 updateMenuPreview called', formData);
        
        // İlgili tüm menü CSS değişkenlerini güncelle
        this.updateCSSVariables(formData, [
            'menu-background-color', 'menu-text-color', 'menu-hover-color', 'menu-hover-bg-color',
            'menu-active-color', 'menu-active-bg-color', 'menu-font-size', 'menu-height', 'menu-padding',
            'submenu-bg-color', 'submenu-text-color', 'submenu-hover-color', 'submenu-hover-bg-color',
            'submenu-border-color', 'submenu-width', 'submenu-font-size',
            'mobile-menu-background-color', 'mobile-menu-text-color', 'mobile-menu-hover-color', 'mobile-menu-hover-bg-color',
            'hamburger-icon-color', 'mobile-menu-divider-color', 'mobile-menu-font-size', 'mobile-menu-padding'
        ]);
        
        console.log('✅ Menu preview güncellendi (CSS Değişkenleri aracılığıyla)');
    };
    
    // CSS değişkenlerini toplu güncelleme yardımcısı
    ThemeEditor.prototype.updateCSSVariables = function(formData, varNames) {
        // Güvenlik kontrolü: varNames tanımsız veya bir dizi değilse işlemi durdur
        if (!varNames || !Array.isArray(varNames)) {
            // console.warn('updateCSSVariables çağrıldı ancak varNames tanımsız veya dizi değil.');
            return;
        }

        const root = document.documentElement;
        varNames.forEach(varName => {
            if (formData[varName] !== undefined) {
                let value = formData[varName];
                // Sayısal değerlere birim ekle (eğer birimsizse)
                if (this.isNumericVariable(varName) && this.isUnitless(value)) {
                    value += 'px';
                }
                root.style.setProperty(`--${varName}`, value);
            }
        });
    };
    
    // Değişkenin sayısal olup olmadığını kontrol et
    ThemeEditor.prototype.isNumericVariable = function(varName) {
        return varName.includes('size') || varName.includes('width') || varName.includes('height') || varName.includes('padding');
    };
    
    // Değerin birimsiz olup olmadığını kontrol et
    ThemeEditor.prototype.isUnitless = function(value) {
        return value !== '' && !isNaN(value) && !/px|rem|em|%|vw|vh/.test(value.toString());
    };
    
    // Menü önizleme toggle fonksiyonları
    ThemeEditor.prototype.initMenuPreviewToggle = function() {
        const self = this;
        console.log('🔧 initMenuPreviewToggle başlatıldı');
        
        // Desktop menü toggle
        $(document).on('click', '#toggleMenuPreview', function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.toggleMenuPreview('desktop');
        });
        
        // Mobile menü toggle
        $(document).on('click', '#toggleMobileMenuPreview', function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.toggleMenuPreview('mobile');
        });
        
        // Dual preview açma
        $(document).on('click', '#openMobileMenuDualPreview', function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.openMenuDualPreview();
        });
        
        console.log('✅ Menu preview toggle event listeners eklendi');
    };
    
    ThemeEditor.prototype.toggleMenuPreview = function(type) {
        console.log(`🔄 toggleMenuPreview çağrıldı: ${type}`);
        
        const isDesktop = type === 'desktop';
        const cardId = isDesktop ? '#menu-preview' : '#mobileMenuPreviewCard';
        const iconId = isDesktop ? '#menuPreviewToggleIcon' : '#mobileMenuPreviewToggleIcon';
        const bodyClass = isDesktop ? 'menu-preview-pinned' : 'mobile-menu-preview-pinned';
        const buttonId = isDesktop ? '#toggleMenuPreview' : '#toggleMobileMenuPreview';
        const previewFixedClass = isDesktop ? 'menu-preview-fixed' : 'mobile-menu-preview-fixed';
        
        const $card = $(cardId);
        const $icon = $(iconId);
        const $button = $(buttonId);
        const $body = $('body');
        
        if ($card.length === 0) {
            console.error(`❌ ${cardId} elementi bulunamadı!`);
            return;
        }
        
        if ($card.hasClass(previewFixedClass)) {
            console.log('📍 Menü preview kapatılıyor...');
            this.unpinMenuPreview(type, $card, $icon, $button, $body, bodyClass, previewFixedClass);
        } else {
            console.log('📌 Menü preview sabitleniyor...');
            this.pinMenuPreview(type, $card, $icon, $button, $body, bodyClass, previewFixedClass);
        }
    };
    
    ThemeEditor.prototype.pinMenuPreview = function(type, $card, $icon, $button, $body, bodyClass, previewFixedClass) {
        console.log(`📌 Menü preview sabitleniyor: ${type}`);
        
        // Önce mevcut sabit preview'ları kaldır
        $('.menu-preview-fixed, .mobile-menu-preview-fixed').removeClass('menu-preview-fixed mobile-menu-preview-fixed');
        $('body').removeClass('menu-preview-pinned mobile-menu-preview-pinned');
        $('#menuPreviewToggleIcon, #mobileMenuPreviewToggleIcon').removeClass('fa-compress').addClass('fa-expand');
        $('#toggleMenuPreview, #toggleMobileMenuPreview').removeClass('preview-pinned');
        
        // Yeni preview'ı sabitle
        $card.addClass(previewFixedClass);
        $body.addClass(bodyClass);
        $icon.removeClass('fa-expand').addClass('fa-compress');
        $button.addClass('preview-pinned');
        
        // Sayfayı en üste kaydır
        setTimeout(() => {
            $('html, body').animate({ scrollTop: 0 }, 300);
        }, 100);
        
        this.showNotification(`📌 ${type === 'desktop' ? 'Desktop' : 'Mobil'} menü önizlemesi sabitlendi`, 'success', 3000);
        console.log(`✅ ${type} menü preview sabitlendi`);
    };
    
    ThemeEditor.prototype.unpinMenuPreview = function(type, $card, $icon, $button, $body, bodyClass, previewFixedClass) {
        console.log(`📍 Menü preview kaldırılıyor: ${type}`);
        
        $card.addClass('header-preview-removing');
        
        setTimeout(() => {
            $card.removeClass(`${previewFixedClass} header-preview-removing`);
            $body.removeClass(bodyClass);
            $icon.removeClass('fa-compress').addClass('fa-expand');
            $button.removeClass('preview-pinned');
            
            console.log(`✅ ${type} menü preview kaldırıldı`);
        }, 300);
        
        this.showNotification(`📍 ${type === 'desktop' ? 'Desktop' : 'Mobil'} menü önizlemesi kapatıldı`, 'info', 2000);
    };
    
    ThemeEditor.prototype.openMenuDualPreview = function() {
        console.log('🔄 openMenuDualPreview başlatıldı');
        
        // Mevcut sabit preview'ları kaldır
        this.unpinAllPreviews();
        
        // Desktop ve mobil menü içeriklerini al
        const desktopMenuContent = $('#menu-preview').prop('outerHTML');
        const mobileMenuContent = $('#mobileMenuPreview').prop('outerHTML');
        
        const dualPreviewHTML = `
            <div class="dual-menu-preview-container">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">
                                    <i class="fa fa-window-restore text-primary"></i>
                                    Dual Menü Önizlemesi
                                </h5>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="closeDualMenuPreview">
                                    <i class="fa fa-times"></i> Kapat
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="card dual-preview-desktop h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fa fa-desktop"></i> Desktop Menü</h6>
                                </div>
                                <div class="card-body">${desktopMenuContent}</div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card dual-preview-mobile h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fa fa-mobile"></i> Mobile Menü</h6>
                                </div>
                                <div class="card-body">${mobileMenuContent}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(dualPreviewHTML);
        $('body').addClass('dual-menu-preview-active');
        
        // Kapatma event'lerini ekle
        $(document).on('click', '#closeDualMenuPreview', () => this.closeDualMenuPreview());
        $(document).on('keydown.dualmenupreview', (e) => {
            if (e.keyCode === 27) this.closeDualMenuPreview(); // ESC
        });
        
        setTimeout(() => $('html, body').animate({ scrollTop: 0 }, 300), 100);
        this.showNotification('🔄 Dual Menu Preview: Desktop ve Mobile yan yana görüntüleniyor', 'success', 3000);
        console.log('✅ Dual menu preview açıldı');
    };
    
    ThemeEditor.prototype.closeDualMenuPreview = function() {
        console.log('🔄 closeDualMenuPreview başlatıldı');
        const $container = $('.dual-menu-preview-container');
        
        $container.addClass('header-preview-removing');
        
        setTimeout(() => {
            $container.remove();
            $('body').removeClass('dual-menu-preview-active');
            $(document).off('.dualmenupreview');
            console.log('✅ Dual menu preview kapatıldı');
        }, 300);
        
        this.showNotification('📍 Dual Menu Preview kapatıldı', 'info', 2000);
    };
    
    ThemeEditor.prototype.unpinAllPreviews = function() {
        $('.menu-preview-fixed, .mobile-menu-preview-fixed').removeClass('menu-preview-fixed mobile-menu-preview-fixed');
        $('body').removeClass('menu-preview-pinned mobile-menu-preview-pinned');
        $('#menuPreviewToggleIcon, #mobileMenuPreviewToggleIcon').removeClass('fa-compress').addClass('fa-expand');
        $('#toggleMenuPreview, #toggleMobileMenuPreview').removeClass('preview-pinned');
    };

} else {
    console.error('❌ menu.js: ThemeEditor sınıfı bulunamadı! core.js yüklenmiş mi?');
}

console.log('✅ Menu.js yüklendi - Menü önizleme sistemleri hazır');
