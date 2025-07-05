/**
 * Header Preview Functions
 * Header ve Mobile Header önizleme fonksiyonları
 */

// Header tema uygulama fonksiyonları
const HeaderThemes = {
    light: {
        'header-bg-color': '#ffffff',
        'main-menu-bg-color': '#ffffff',
        'main-menu-link-color': '#333333',
        'main-menu-link-hover-color': '#4285f4',
        'main-menu-link-bg-color': 'transparent',
        'main-menu-link-hover-bg-color': '#f8f9fa',
        'main-menu-ul-bg-color': '#ffffff',
        'main-menu-ul-submenu-link-color': '#333333',
        'main-menu-ul-submenu-link-hover-color': '#4285f4',
        'main-menu-ul-submenu-link-bg-color': '#ffffff',
        'main-menu-ul-submenu-link-hover-bg-color': '#f8f9fa'
    },
    dark: {
        'header-bg-color': '#2c3e50',
        'main-menu-bg-color': '#2c3e50',
        'main-menu-link-color': '#ecf0f1',
        'main-menu-link-hover-color': '#3498db',
        'main-menu-link-bg-color': 'transparent',
        'main-menu-link-hover-bg-color': '#34495e',
        'main-menu-ul-bg-color': '#34495e',
        'main-menu-ul-submenu-link-color': '#ecf0f1',
        'main-menu-ul-submenu-link-hover-color': '#3498db',
        'main-menu-ul-submenu-link-bg-color': '#34495e',
        'main-menu-ul-submenu-link-hover-bg-color': '#2c3e50'
    },
    transparent: {
        'header-bg-color': 'rgba(255,255,255,0.95)',
        'main-menu-bg-color': 'transparent',
        'main-menu-link-color': '#333333',
        'main-menu-link-hover-color': '#4285f4',
        'main-menu-link-bg-color': 'transparent',
        'main-menu-link-hover-bg-color': 'rgba(66,133,244,0.1)',
        'main-menu-ul-bg-color': '#ffffff',
        'main-menu-ul-submenu-link-color': '#333333',
        'main-menu-ul-submenu-link-hover-color': '#4285f4',
        'main-menu-ul-submenu-link-bg-color': '#ffffff',
        'main-menu-ul-submenu-link-hover-bg-color': '#f8f9fa'
    }
};

// ThemeEditor sınıfına header fonksiyonları ekle
if (typeof ThemeEditor !== 'undefined') {
    
    // Header tema uygulama
    ThemeEditor.prototype.applyHeaderTheme = function(themeType) {
        const theme = HeaderThemes[themeType];
        if (!theme) return;

        // Header tema değerlerini form alanlarına uygula
        Object.keys(theme).forEach(key => {
            const input = $(`input[name="${key}"], select[name="${key}"]`);
            if (input.length > 0) {
                input.val(theme[key]).trigger('change');
            }
        });

        this.updatePreview();
        this.showNotification(`${themeType.charAt(0).toUpperCase() + themeType.slice(1)} header teması uygulandı!`, 'success');
    };

    // Ana header preview güncelleme
    ThemeEditor.prototype.updateHeaderPreview = function(formData) {
        console.log('🔧 updateHeaderPreview called', formData);
        
        // Üst İletişim & Sosyal Medya önizlemesi güncelle
        this.updateTopContactPreview(formData);
        
        // Header Desktop önizlemesi güncelle
        this.updateHeaderDesktopPreview(formData);
        
        // Header Mobile önizlemesi güncelle  
        this.updateHeaderMobilePreview(formData);
          
        // Alışveriş ikonları önizlemesi güncelle
        this.updateShopIconsPreview(formData);
        
        // Menü önizlemesi güncelle
        this.updateMenuPreview(formData);
        
        // Dual preview aktifse onları da güncelle
        if ($('.dual-preview-container').length > 0) {
            this.updateDualPreview(formData);
        }
    };

    // Üst iletişim ve sosyal medya önizlemesi
    ThemeEditor.prototype.updateTopContactPreview = function(formData) {
        console.log('🔧 updateTopContactPreview called', formData);
        
        // CSS değişkenlerini güncelle
        const root = document.documentElement;
        
        // Desktop değişkenleri
        const topContactVars = [
            'top-contact-and-social-bg-color',
            'top-contact-and-social-link-color', 
            'top-contact-and-social-link-hover-color',
            'top-contact-and-social-icon-color',
            'top-contact-and-social-icon-hover-color',
            'top-contact-and-social-container-margin-top'
        ];
        
        // Mobile değişkenleri
        const topContactMobileVars = [
            'top-contact-and-social-bg-color-mobile',
            'top-contact-and-social-link-color-mobile', 
            'top-contact-and-social-link-hover-color-mobile',
            'top-contact-and-social-icon-color-mobile',
            'top-contact-and-social-icon-hover-color-mobile',
            'top-contact-and-social-container-mobile-margin-top'
        ];
        
        // Desktop değişkenleri güncelle
        topContactVars.forEach(varName => {
            if (formData[varName]) {
                let value = formData[varName];
                
                // Margin için px ekle
                if (varName.includes('margin') && !isNaN(value) && value !== '') {
                    value += 'px';
                }
                
                root.style.setProperty(`--${varName}`, value);
                console.log(`✅ CSS Variable güncellendi: --${varName} = ${value}`);
            }
        });
        
        // Mobile değişkenleri güncelle
        topContactMobileVars.forEach(varName => {
            if (formData[varName]) {
                let value = formData[varName];
                
                // Margin için px ekle
                if (varName.includes('margin') && !isNaN(value) && value !== '') {
                    value += 'px';
                }
                
                root.style.setProperty(`--${varName}`, value);
                console.log(`✅ Mobile CSS Variable güncellendi: --${varName} = ${value}`);
            }
        });
        
        // Desktop üst iletişim önizlemesini doğrudan güncelle
        const topContactPreview = document.querySelector('#topContactPreview');
        if (topContactPreview && formData['top-contact-and-social-bg-color']) {
            topContactPreview.style.background = formData['top-contact-and-social-bg-color'];
        }
        
        // Mobile üst iletişim önizlemesini doğrudan güncelle  
        const mobileTopContact = document.querySelector('#mobileHeaderPreview > div:first-child');
        if (mobileTopContact) {
            const mobileBgColor = formData['top-contact-and-social-bg-color-mobile'] || formData['top-contact-and-social-bg-color'];
            if (mobileBgColor) {
                mobileTopContact.style.background = mobileBgColor;
            }
        }
        
        // Preview alanlarını yenile
        setTimeout(() => {
            if (topContactPreview) {
                topContactPreview.style.display = 'none';
                topContactPreview.offsetHeight; // trigger reflow
                topContactPreview.style.display = 'flex';
            }
            
            const mobileHeaderPreview = document.querySelector('#mobileHeaderPreview');
            if (mobileHeaderPreview) {
                mobileHeaderPreview.style.display = 'none';
                mobileHeaderPreview.offsetHeight; // trigger reflow
                mobileHeaderPreview.style.display = 'block';
            }
        }, 10);
    };

    // Desktop header önizlemesi
    ThemeEditor.prototype.updateHeaderDesktopPreview = function(formData) {
        const headerPreview = document.querySelector('#headerPreviewContent');
        if (!headerPreview) return;

        // Header değerlerini al
        const headerBgColor = formData['header-bg-color'] || '#ffffff';
        const headerBorderWidth = formData['header-border-width'] || '1';
        const headerBorderColor = formData['header-border-color'] || '#e9ecef';
        const headerPadding = formData['header-padding'] || '15';
        const headerMinHeight = formData['header-min-height'] || '80';
        const headerLogoWidth = formData['header-logo-width'] || '150';
        const primaryColor = formData['primary-color'] || '#4285f4';

        // Header container stilini güncelle
        headerPreview.style.background = headerBgColor;
        headerPreview.style.borderBottom = `${headerBorderWidth}px solid ${headerBorderColor}`;
        headerPreview.style.padding = `${headerPadding}px`;
        headerPreview.style.minHeight = `${headerMinHeight}px`;
        
        // Logo stilini güncelle
        const logo = headerPreview.querySelector('div[style*="LOGO"]');
        if (logo) {
            logo.style.width = `${headerLogoWidth}px`;
            logo.style.background = primaryColor;
        }

        console.log('✅ Header Desktop önizlemesi güncellendi');
    };

    // Mobile header önizlemesi
    ThemeEditor.prototype.updateHeaderMobilePreview = function(formData) {
        const mobilePreview = document.querySelector('#mobilePreview > div, #dualMobileHeaderPreview > div:nth-child(2)');
        
        if (!mobilePreview) return;

        // Mobile Header değerlerini al
        const headerMobileBgColor = formData['header-mobile-bg-color'];
        const headerMobileBorderWidth = formData['header-mobile-border-width'];
        const headerMobileBorderColor = formData['header-mobile-border-color'];
        const headerMobilePadding = formData['header-mobile-padding'];
        const headerMobileMinHeight = formData['header-mobile-min-height'];
        const headerMobileLogoWidth = formData['header-mobile-logo-width'];
        const primaryColor = formData['primary-color'];

        // Mobile header container stilini güncelle
        mobilePreview.style.background = headerMobileBgColor;
        mobilePreview.style.borderBottom = `${headerMobileBorderWidth}px solid ${headerMobileBorderColor}`;
        mobilePreview.style.padding = `${headerMobilePadding}px`;
        mobilePreview.style.minHeight = `${headerMobileMinHeight}px`;
        
        // Mobile logo stilini güncelle
        const mobileLogo = mobilePreview.querySelector('div[style*="LOGO"]');
        if (mobileLogo) {
            mobileLogo.style.width = `${headerMobileLogoWidth}px`;
            mobileLogo.style.background = primaryColor;
        }

        console.log('✅ Header Mobile önizlemesi güncellendi');
    };

    // Alışveriş ikonları önizlemesi
    ThemeEditor.prototype.updateShopIconsPreview = function(formData) {
        // Alışveriş ikon renklerini tanımla
        const iconSearchColor = formData['shop-menu-container-icon-color-search'];
        const iconMemberColor = formData['shop-menu-container-icon-color-member'] ;
        const iconFavoritesColor = formData['shop-menu-container-icon-color-favorites'];
        const iconBasketColor = formData['shop-menu-container-icon-color-basket']
        const iconHoverColor = formData['shop-menu-container-icon-hover-color'];

        // Desktop alışveriş ikonları
        const headerPreview = document.querySelector('#headerPreviewContent');
        if (headerPreview) {
            const searchIcon = headerPreview.querySelector('.fa-search');
            if (searchIcon) {
                searchIcon.style.color = iconSearchColor;
                searchIcon.onmouseover = () => searchIcon.style.color = iconHoverColor;
                searchIcon.onmouseout = () => searchIcon.style.color = iconSearchColor;
            }

            const memberIcon = headerPreview.querySelector('.fa-user');
            if (memberIcon) {
                memberIcon.style.color = iconMemberColor;
                memberIcon.onmouseover = () => memberIcon.style.color = iconHoverColor;
                memberIcon.onmouseout = () => memberIcon.style.color = iconMemberColor;
            }

            const favoritesIcon = headerPreview.querySelector('.fa-heart');
            if (favoritesIcon) {
                favoritesIcon.style.color = iconFavoritesColor;
                favoritesIcon.onmouseover = () => favoritesIcon.style.color = iconHoverColor;
                favoritesIcon.onmouseout = () => favoritesIcon.style.color = iconFavoritesColor;
            }

            const basketIcon = headerPreview.querySelector('.fa-shopping-cart');
            if (basketIcon) {
                basketIcon.style.color = iconBasketColor;
                basketIcon.onmouseover = () => basketIcon.style.color = iconHoverColor;
                basketIcon.onmouseout = () => basketIcon.style.color = iconBasketColor;
            }
        }
        
        // Mobile alışveriş ikonlarını güncelle
        this.updateMobileShopIconsPreview(formData);

        console.log('✅ Alışveriş ikonları önizlemesi güncellendi');
    };

    // Mobile alışveriş ikonları önizlemesi
    ThemeEditor.prototype.updateMobileShopIconsPreview = function(formData) {
        console.log('🔧 updateMobileShopIconsPreview called', formData);
        
        // CSS değişkenlerini güncelle
        const root = document.documentElement;
        
        const mobileIconVars = [
            'mobile-action-icon-phone-bg-color',
            'mobile-action-icon-whatsapp-bg-color',
            'mobile-action-icon-basket-bg-color',
            'mobile-action-icon-basket-counter-bg-color',
            'mobile-action-icon-size',
            'mobile-action-icon-gap'
        ];
        
        // CSS değişkenlerini güncelle
        mobileIconVars.forEach(varName => {
            if (formData[varName]) {
                let value = formData[varName];
                
                // Size ve gap için px ekle
                if ((varName.includes('size') || varName.includes('gap')) && !isNaN(value) && value !== '') {
                    value += 'px';
                }
                
                root.style.setProperty(`--${varName}`, value);
            }
        });
        
        // Mobile header preview'daki action iconları güncelle
        const mobileActionContainer = document.querySelector('#mobileHeaderPreview div:last-child > div:last-child');
        if (mobileActionContainer) {
            // Gap güncelle
            if (formData['mobile-action-icon-gap']) {
                mobileActionContainer.style.gap = formData['mobile-action-icon-gap'] + 'px';
            }
            
            // Action iconları güncelle
            const actionIcons = mobileActionContainer.children;
            
            if (actionIcons[0] && formData['mobile-action-icon-phone-bg-color']) {
                actionIcons[0].style.background = formData['mobile-action-icon-phone-bg-color'];
                if (formData['mobile-action-icon-size']) {
                    actionIcons[0].style.width = formData['mobile-action-icon-size'] + 'px';
                    actionIcons[0].style.height = formData['mobile-action-icon-size'] + 'px';
                }
            }
            
            if (actionIcons[1] && formData['mobile-action-icon-whatsapp-bg-color']) {
                actionIcons[1].style.background = formData['mobile-action-icon-whatsapp-bg-color'];
                if (formData['mobile-action-icon-size']) {
                    actionIcons[1].style.width = formData['mobile-action-icon-size'] + 'px';
                    actionIcons[1].style.height = formData['mobile-action-icon-size'] + 'px';
                }
            }
            
            if (actionIcons[2]) {
                if (formData['mobile-action-icon-basket-bg-color']) {
                    actionIcons[2].style.background = formData['mobile-action-icon-basket-bg-color'];
                }
                if (formData['mobile-action-icon-size']) {
                    actionIcons[2].style.width = formData['mobile-action-icon-size'] + 'px';
                    actionIcons[2].style.height = formData['mobile-action-icon-size'] + 'px';
                }
                
                // Sepet sayacı
                const counter = actionIcons[2].querySelector('div');
                if (counter && formData['mobile-action-icon-basket-counter-bg-color']) {
                    counter.style.background = formData['mobile-action-icon-basket-counter-bg-color'];
                }
            }
        }
        
        console.log('🎨 Mobile action icons preview güncellendi');
    };

    // Header Preview Toggle Sistemi
    ThemeEditor.prototype.initHeaderPreviewToggle = function() {
        const self = this;
        console.log('🔧 initHeaderPreviewToggle başlatıldı');
        
        // Desktop Header Toggle
        $(document).on('click', '#toggleHeaderPreview', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (self.isHeaderToggling) return false;
            
            self.isHeaderToggling = true;
            
            setTimeout(() => {
                self.toggleHeaderPreview('desktop');
                setTimeout(() => {
                    self.isHeaderToggling = false;
                }, 500);
            }, 100);
            
            return false;
        });
        
        // Mobile Header Toggle
        $(document).on('click', '#toggleMobileHeaderPreview', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (self.isHeaderToggling) return false;
            
            self.isHeaderToggling = true;
            
            setTimeout(() => {
                self.toggleHeaderPreview('mobile');
                setTimeout(() => {
                    self.isHeaderToggling = false;
                }, 500);
            }, 100);
            
            return false;
        });
        
        // Dual Preview butonları
        $(document).on('click', '#openDualPreview, #openDualPreviewFromMobile', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            if (self.isHeaderToggling) return false;
            
            self.isHeaderToggling = true;
            
            setTimeout(() => {
                self.openDualPreview();
                self.isHeaderToggling = false;
            }, 100);
            
            return false;
        });
        
        console.log('✅ Header preview toggle event listeners eklendi');
    };

    // Header Preview Toggle Logic
    ThemeEditor.prototype.toggleHeaderPreview = function(type) {
        console.log(`🔄 toggleHeaderPreview çağrıldı: ${type}`);
        
        // Dual preview kontrol et
        if ($('.dual-preview-container').length > 0) {
            console.log('📍 Dual preview kapatılıyor...');
            this.closeDualPreview();
            return;
        }
        
        const isDesktop = type === 'desktop';
        const cardId = isDesktop ? '#headerPreviewCard' : '#mobileHeaderPreviewCard';
        const iconId = isDesktop ? '#headerPreviewToggleIcon' : '#mobileHeaderPreviewToggleIcon';
        const bodyClass = isDesktop ? 'header-preview-pinned' : 'mobile-header-preview-pinned';
        const buttonId = isDesktop ? '#toggleHeaderPreview' : '#toggleMobileHeaderPreview';
        const previewFixedClass = isDesktop ? 'header-preview-fixed' : 'mobile-header-preview-fixed';
        
        const $card = $(cardId);
        const $icon = $(iconId);
        const $button = $(buttonId);
        const $body = $('body');
        
        if ($card.length === 0) {
            console.error(`❌ ${cardId} elementi bulunamadı!`);
            return;
        }
        
        // Tek preview aktifse kapat, değilse dual preview aç
        if ($card.hasClass(previewFixedClass)) {
            console.log('📍 Tek preview kapatılıyor...');
            this.unpinHeaderPreview(type, $card, $icon, $button, $body, bodyClass);
        } else {
            console.log('🔄 Dual preview açılıyor...');
            this.openDualPreview();
        }
    };

    // Dual Preview Sistemi
    ThemeEditor.prototype.openDualPreview = function() {
        console.log('🔄 openDualPreview başlatıldı');
        
        // Mevcut sabitlenmiş preview'ları kaldır
        $('.header-preview-fixed, .mobile-header-preview-fixed').removeClass('header-preview-fixed mobile-header-preview-fixed');
        $('body').removeClass('header-preview-pinned mobile-header-preview-pinned');
        
        // Icon ve buton durumlarını sıfırla
        $('#headerPreviewToggleIcon, #mobileHeaderPreviewToggleIcon').removeClass('fa-compress').addClass('fa-expand');
        $('#toggleHeaderPreview, #toggleMobileHeaderPreview').removeClass('preview-pinned');
        
        // Desktop ve Mobile preview içeriklerini al
        const desktopTopContactContent = $('#topContactPreview').prop('outerHTML');
        const desktopMainHeaderContent = $('#headerPreviewContent').prop('outerHTML');
        const mobileHeaderContent = $('#mobileHeaderPreview').html();
        
        // Dual preview container'ı oluştur
        const dualPreviewHTML = `
            <div class="dual-preview-container" id="dualPreviewContainer">
                <button type="button" class="dual-preview-close" id="closeDualPreview" title="Önizlemeyi kapat">
                    <i class="fa fa-times"></i>
                </button>
                
                <div class="dual-preview-desktop">
                    <div class="card-header">
                        <h5><i class="fa fa-desktop"></i> Desktop Header Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="theme-preview" id="dualDesktopHeaderPreview">
                            ${desktopTopContactContent}
                            ${desktopMainHeaderContent}
                        </div>
                    </div>
                </div>
                
                <div class="dual-preview-mobile">
                    <div class="card-header">
                        <h5><i class="fa fa-mobile"></i> Mobile Header Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="theme-preview" id="dualMobileHeaderPreview">
                            ${mobileHeaderContent}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Body'ye ekle
        $('body').append(dualPreviewHTML);
        $('body').addClass('dual-preview-active');
        $('#headerPreviewRow').addClass('hidden');
        
        // Close butonuna event listener ekle
        $(document).on('click', '#closeDualPreview', () => {
            this.closeDualPreview();
        });
        
        // ESC tuşu ile kapatma
        $(document).on('keydown.dualpreview', (e) => {
            if (e.keyCode === 27) { // ESC
                this.closeDualPreview();
            }
        });
        
        // İkon durumlarını güncelle
        $('#headerPreviewToggleIcon, #mobileHeaderPreviewToggleIcon').removeClass('fa-expand').addClass('fa-compress');
        $('#toggleHeaderPreview, #toggleMobileHeaderPreview').addClass('preview-pinned');
        
        // Otomatik scroll
        setTimeout(() => {
            $('html, body').animate({ scrollTop: 0 }, 300);
        }, 100);
        
        // Bildirim göster
        this.showNotification('🔄 Dual Preview: Desktop ve Mobile header yan yana görüntüleniyor', 'success', 3000);
        
        console.log('✅ Dual preview açıldı');
    };
    
    ThemeEditor.prototype.closeDualPreview = function() {
        console.log('🔄 closeDualPreview başlatıldı');
        
        // Animasyonlı kaldırma
        $('.dual-preview-container').addClass('header-preview-removing');
        
        setTimeout(() => {
            // Container'ı kaldır
            $('.dual-preview-container').remove();
            $('body').removeClass('dual-preview-active');
            
            // Event listener'ları temizle
            $(document).off('keydown.dualpreview');
            $(document).off('click', '#closeDualPreview');
            
            // İkon durumlarını sıfırla
            $('#headerPreviewToggleIcon, #mobileHeaderPreviewToggleIcon').removeClass('fa-compress').addClass('fa-expand');
            $('#toggleHeaderPreview, #toggleMobileHeaderPreview').removeClass('preview-pinned');
            
            console.log('✅ Dual preview kapatıldı');
        }, 300);

        $('#headerPreviewRow').removeClass('hidden');
        
        // Bildirim göster
        this.showNotification('📍 Dual Preview kapatıldı', 'info', 2000);
    };

    ThemeEditor.prototype.updateDualPreview = function(formData) {
        console.log('🔧 updateDualPreview called');
        
        // Dual preview elemanını güncelle
        const $dualDesktopPreview = $('#dualDesktopHeaderPreview');
        const $dualMobilePreview = $('#dualMobileHeaderPreview');
        
        if ($dualDesktopPreview.length > 0) {
            const topContactHTML = $('#topContactPreview').prop('outerHTML');
            const headerContentHTML = $('#headerPreviewContent').prop('outerHTML');
            
            $dualDesktopPreview.html(`
                ${topContactHTML}
                ${headerContentHTML}
            `);
        }
        
        if ($dualMobilePreview.length > 0) {
            const mobileContent = $('#mobileHeaderPreview').html();
            $dualMobilePreview.html(mobileContent);
        }
        
        console.log('✅ Dual preview içerikleri güncellendi');
    };
} else {
    console.error('❌ header.js:ThemeEditor sınıfı bulunamadı! core.js yüklenmiş mi?');
}

// HTML'den çağrılacak global fonksiyon
function applyHeaderTheme(themeName) {
    if (window.themeEditorInstance) {
        window.themeEditorInstance.applyHeaderTheme(themeName);
    }
}
