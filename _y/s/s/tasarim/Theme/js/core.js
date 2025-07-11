/**
 * Theme Editor Core Functions
 * Ana tema editörü sınıfı ve temel fonksiyonlar
 */

class ThemeEditor {
    constructor() {
        this.formData = {};
        this.isInitialized = false;
        this.headerToggleTimeout = null;
        this.isHeaderToggling = false;
        this.unsavedChanges = false;
        this.changeTimeout = null;
        
        console.log('🎨 ThemeEditor Core başlatıldı');
        this.init();
    }
    init() {
        if (this.isInitialized) return;
        
        // Temel event listener'ları kur
        this.setupEventListeners();
        
        // Header preview toggle sistemini başlat
        this.initHeaderPreviewToggle();
        
        // Menu preview toggle sistemini başlat
        this.initMenuPreviewToggle();
        
        // Auto-save sistemini kur
        this.setupAutoSave();
        
        this.isInitialized = true;
        console.log('✅ ThemeEditor Core başlatma tamamlandı');
    }

    setupEventListeners() {
        // Form değişikliklerini dinle
        
        $(document).on('change input', '#themeForm input, #themeForm select', (e) => {
            this.onFormChange(e);
        });
        
        // Sınır & köşe ayarlarına özel event listener'lar
        $(document).on('change input', 'input[name="border-width"], input[name="border-radius-base"], input[name="card-border-radius"], input[name="input-border-radius"], input[name="input-border-radius-1"], input[name="btn-border-radius"], input[name="border-color"], input[name="border-light-color"], select[name="border-style"]', (e) =>{
            this.onBorderChange(e);
        });
        
        // Sekme değişimlerini dinle
        $(document).on('click', '[data-toggle="tab"]', (e) => {
            this.onTabChange(e);
        });
        
        console.log('✅ Core event listener\'lar kuruldu');
    }

    onFormChange(e) {

        if (e.target.name === 'input-border-radius-1') {
            console.log('🔄 input-border-radius-1 değişti, input-border-radius güncelleniyor...');
            const $mainInput = $('input[name="input-border-radius"]');
            if ($mainInput.length) {
                $mainInput.val(e.target.value);
                console.log('✅ input-border-radius değeri güncellendi:', e.target.value);
            }
        }
        // Debounce için timeout kullan
        clearTimeout(this.changeTimeout);
        this.changeTimeout = setTimeout(() => {
            console.log('🔄 Form değişikliği algılandı:', e.target.name, '=', e.target.value);
            this.unsavedChanges = true;
            this.updatePreview();
        }, 300);
    }

    onBorderChange(e) {
        console.log('🔄 Sınır ayarı değişti:', e.target.name, '=', e.target.value);
        
        // Hemen önizlemeyi güncelle (border değişiklikleri için daha hızlı feedback)
        clearTimeout(this.changeTimeout);
        this.changeTimeout = setTimeout(() => {
            const formData = this.getFormData();
            this.updateBorderPreview(formData);
        }, 100); // Daha hızlı güncelleme
    }

    onTabChange(e) {
        const targetId = $(e.target).attr('data-target');
        console.log('🔄 Sekme değişti:', targetId);
        
        // Sekmeye özel işlemler
        switch(targetId) {
            case '#header-panel':
                this.initHeaderPreviewToggle();
                break;
            case '#menu-panel':
                this.initMenuPreviewToggle();
                break;
            case '#responsive-panel':
                this.initResponsivePreview();
                break;
        }
    }

    initHeaderPreviewToggle() {
        // Header preview toggle sistemi
        console.log('🔧 Header preview toggle başlatıldı');
    }

    initResponsivePreview() {
        // Responsive preview sistemi
        console.log('🔧 Responsive preview başlatıldı');
    }

    setupAutoSave() {
        // 5 dakikada bir otomatik kaydetme
        setInterval(() => {
            if (this.unsavedChanges) {
                this.autoSave();
            }
        }, 300000);
    }
    autoSave() {
        console.log('💾 Otomatik kayıt yapılıyor...');
        // Basit auto-save implementasyonu
    }
    
    // Preview güncellemeleri
    updatePreview() {
        if (!this.isInitialized) return;
        
        console.log('🔄 Preview güncelleniyor...');
        
        const formData = this.getFormData();
        
        // Tüm sekmelerdeki önizlemeleri güncelle
        this.updateAllPreviews(formData);
        
        // CSS değişkenlerini güncelle
        this.updateCSSVariables(formData);
        
        console.log('✅ Tüm preview\'lar güncellendi');
    }

    updateMenuPreview(formData) {
        const menuPreview = $('#menu-preview');
        if (menuPreview.length === 0) return;
        
        console.log('🔄 Menu preview güncelleniyor...');
        
        // Menu renk değişkenleri
        const menuBg = formData['menu-background-color'] || '#ffffff';
        const menuText = formData['menu-text-color'] || '#333333';
        const menuHover = formData['menu-hover-color'] || '#f0f0f0';
        
        // Menu preview'ını güncelle
        menuPreview.css({
            '--menu-bg-color': menuBg,
            '--menu-text-color': menuText,
            '--menu-hover-color': menuHover
        });
        
        console.log('✅ Menu preview güncellendi');
    }

    updateHeaderPreview(formData) {
        const headerPreview = $('#header-preview');
        if (headerPreview.length === 0) return;
        
        console.log('🔄 Header preview güncelleniyor...');
        
        // Header renk değişkenleri
        const headerBg = formData['header-background-color'] || '#ffffff';
        const headerText = formData['header-text-color'] || '#333333';
        const logoSize = formData['logo-size'] || '150';
        
        // Header preview'ını güncelle
        headerPreview.css({
            '--header-bg-color': headerBg,
            '--header-text-color': headerText,
            '--logo-size': logoSize + 'px'
        });
        
        console.log('✅ Header preview güncellendi');
    }
    
    updateMobileMenuPreview(formData) {
        const mobileMenuPreview = $('#mobile-menu-preview');
        if (mobileMenuPreview.length === 0) return;
        
        console.log('🔄 Mobile menu preview güncelleniyor...');
        
        // Mobile menu renk değişkenleri
        const mobileBg = formData['mobile-menu-background-color'] || '#ffffff';
        const mobileText = formData['mobile-menu-text-color'] || '#333333';
        
        // Mobile menu preview'ını güncelle
        mobileMenuPreview.css({
            '--mobile-menu-bg-color': mobileBg,
            '--mobile-menu-text-color': mobileText
        });
        
        console.log('✅ Mobile menu preview güncellendi');
    }

    
    updateFormPreview(formData) {
        const formPreview = $('#form-preview');
        if (formPreview.length === 0) return;
        
        console.log('🔄 Form preview güncelleniyor...');
        
        // Form renk değişkenleri
        const buttonBg = formData['button-background-color'] || '#007bff';
        const buttonText = formData['button-text-color'] || '#ffffff';
        const inputBorder = formData['input-border-color'] || '#ced4da';
        
        // Form preview'ını güncelle
        formPreview.css({
            '--button-bg-color': buttonBg,
            '--button-text-color': buttonText,
            '--input-border-color': inputBorder
        });
        
        console.log('✅ Form preview güncellendi');
    }
    
    updateFooterPreview(formData) {
        const footerPreview = $('#footer-preview');
        if (footerPreview.length === 0) return;
        
        console.log('🔄 Footer preview güncelleniyor...');
        
        // Footer renk değişkenleri
        const footerBg = formData['footer-background-color'] || '#2c3e50';
        const footerText = formData['footer-text-color'] || '#ecf0f1';
        const footerLink = formData['footer-link-color'] || '#3498db';
        
        // Footer preview'ını güncelle
        footerPreview.css({
            '--footer-bg-color': footerBg,
            '--footer-text-color': footerText,
            '--footer-link-color': footerLink
        });
        
        console.log('✅ Footer preview güncellendi');
    }

    updateColorPreview(formData) {
        console.log('🎨 Renk önizlemesi güncelleniyor...');
        const colorPreviews = $('#colorPreview, #colorPreview2'); // Her iki önizleme alanını da seç
        if (colorPreviews.length === 0) return;

        // body-bg-color'ı doğrudan theme-preview elementlerine uygula
        const bodyBgColor = formData['body-bg-color'] || '#f8f9fa';
        $('.theme-preview').css('background', bodyBgColor);

        colorPreviews.each(function() {
            const $colorPreview = $(this);

            // Ana Renkler
            $colorPreview.find('.color-sample .color-box').each(function() {
                const $this = $(this);
                const smallText = $this.next('small').text().toLowerCase();
                let colorVar;

                if (smallText === 'primary') colorVar = 'primary-color';
                else if (smallText === 'secondary') colorVar = 'secondary-color';
                else if (smallText === 'accent') colorVar = 'accent-color';
                else if (smallText === 'success') colorVar = 'success-color';
                else if (smallText === 'warning') colorVar = 'warning-color';
                else if (smallText === 'danger') colorVar = 'danger-color';

                if (colorVar && formData[colorVar]) {
                    $this.css('background', formData[colorVar]);
                }
            });

            // Metin Örnekleri
            const textSamples = $colorPreview.find('.text-samples');
            if (textSamples.length) {
                textSamples.css({
                    'background': formData['content-bg-color'] || '#ffffff',
                    'border-color': formData['border-light-color'] || '#e9ecef'
                });
                textSamples.find('h5').css('color', formData['heading-color'] || '#1a1a1a');
                textSamples.find('p:eq(0)').css('color', formData['text-primary-color'] || '#202124');
                textSamples.find('p:eq(1)').css('color', formData['text-secondary-color'] || '#5f6368');
                textSamples.find('a').css('color', formData['link-color'] || '#4285f4');
                textSamples.find('p:eq(2)').css('color', formData['text-muted-color'] || '#9aa0a6');
            }
        });

        console.log('✅ Renk önizlemesi güncellendi');
    }
    
    updateBorderPreview(formData) {
        console.log('🔄 Sınır & köşe preview güncelleniyor...');
        
        // Sınır değişkenleri
        const borderColor = formData['border-color'];
        const borderLightColor = formData['border-light-color'];
        const borderStyle = formData['border-style'];
        const borderWidth = (formData['border-width']) + 'px';
        const borderRadiusBase = (formData['border-radius-base']) + 'px';
        const cardBorderRadius = (formData['card-border-radius']) + 'px';
        const inputBorderRadius = (formData['input-border-radius']) + 'px';
        const buttonBorderRadius = (formData['btn-border-radius']) + 'px';
        
        // CSS değişkenlerini güncelle
        const root = document.documentElement;
        root.style.setProperty('--border-color', borderColor);
        root.style.setProperty('--border-light-color', borderLightColor);
        root.style.setProperty('--border-style', borderStyle);
        root.style.setProperty('--border-width', borderWidth);
        root.style.setProperty('--border-radius-base', borderRadiusBase);
        root.style.setProperty('--card-border-radius', cardBorderRadius);
        root.style.setProperty('--input-border-radius', inputBorderRadius);
        root.style.setProperty('--btn-border-radius', buttonBorderRadius);
        
        // Önizleme kutularını doğrudan güncelle
        $('#borderPreviewGeneral .preview-border').css({
            'border': `${borderWidth} ${borderStyle} ${borderColor}`,
            'border-radius': borderRadiusBase
        });
        
        $('#borderPreviewCard .preview-border').css({
            'border': `${borderWidth} ${borderStyle} ${borderLightColor}`,
            'border-radius': cardBorderRadius
        });
        
        $('#borderPreviewInput .preview-border').css({
            'border': `${borderWidth} ${borderStyle} ${borderColor}`,
            'border-radius': inputBorderRadius
        });
        
        $('#borderPreviewButton .preview-border').css({
            'border': `${borderWidth} ${borderStyle} ${formData['primary-color'] || '#4285f4'}`,
            'border-radius': buttonBorderRadius,
            'background': formData['primary-color'] || '#4285f4'
        });
        
        console.log('✅ Sınır & köşe preview güncellendi');
    }
    
    // Tema kaydetme
    saveTheme() {
        console.log('💾 Tema kaydediliyor...');
        this.showLoader('Tema kaydediliyor...');
        
        const formData = this.getFormData();
        
        // AJAX ile tema verilerini kaydet
        $.ajax({
            url: "/App/Controller/Admin/AdminDesignController.php",
            method: 'POST',
            dataType: 'json', // Sunucudan JSON yanıtı beklediğimizi belirtelim
            data: {
                action: 'saveDesign',
                ...formData
            },
            success: (response) => {
                this.hideLoader();
                if (response && response.status === 'success') {
                    this.showNotification(response.message || 'Tema başarıyla kaydedildi!', 'success');
                    this.unsavedChanges = false;
                    console.log('✅ Tema kaydedildi:', response);
                } else {
                    // Sunucudan gelen hata mesajını göster
                    this.showNotification(response.message || 'Bilinmeyen bir sunucu hatası oluştu.', 'error');
                    console.error('❌ Tema kaydetme sunucu hatası:', response);
                }
            },
            error: (xhr, status, error) => {
                this.hideLoader();
                let errorMessage = 'Tema kaydedilirken bir ağ hatası oluştu.';
                try {
                    const jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse && jsonResponse.message) {
                        errorMessage = jsonResponse.message;
                    }
                } catch (e) {
                    // Yanıt JSON değilse, ham yanıtı logla
                    console.error('Sunucudan gelen yanıt ayrıştırılamadı:', xhr.responseText);
                }
                this.showNotification(errorMessage, 'error');
                console.error('❌ Tema kaydetme AJAX hatası:', status, error);
            }
        });
    }
    
    // Tema önizleme
    previewTheme() {
        console.log('👁️ Tema önizlemesi açılıyor...');
        const formData = this.getFormData();
        
        // Önizleme sayfasını yeni sekmede aç
        const previewUrl = window.location.origin + '/';
        const previewWindow = window.open(previewUrl, 'themePreview', 'width=1200,height=800');
        
        if (previewWindow) {
            this.showNotification('Önizleme yeni sekmede açıldı', 'info');
        } else {
            this.showNotification('Popup blocker nedeniyle önizleme açılamadı', 'error');
        }
    }
    
    // Tema sıfırlama
    resetTheme() {
        console.log('🔄 Tema sıfırlanıyor...');
        
        if (confirm('Tüm tema ayarları varsayılan değerlere dönecek. Emin misiniz?')) {
            this.showLoader('Tema sıfırlanıyor...');
            
            const languageID = $('#languageSelect').val();

            $.ajax({
                url: "/App/Controller/Admin/AdminDesignController.php",
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'resetDesign',
                    languageID: languageID
                },
                success: (response) => {
                    this.hideLoader();
                    if (response && response.status === 'success') {
                        // Formu sıfırla
                        $('#themeForm')[0].reset();
                        
                        // CSS değişkenlerini temizle
                        const oldStyle = document.getElementById('theme-preview-vars');
                        if (oldStyle) {
                            oldStyle.remove();
                        }
                        
                        this.showNotification(response.message || 'Tema varsayılan ayarlara sıfırlandı', 'success');
                        this.unsavedChanges = false;
                        this.updateAllPreviews(this.getFormData()); // Tüm önizlemeleri varsayılan değerlerle güncelle
                        window.location.reload(); // Sayfayı yenile
                    } else {
                        this.showNotification(response.message || 'Tema sıfırlanırken bir hata oluştu.', 'error');
                        console.error('❌ Tema sıfırlama sunucu hatası:', response);
                    }
                },
                error: (xhr, status, error) => {
                    this.hideLoader();
                    this.showNotification('Tema sıfırlanırken bir ağ hatası oluştu.', 'error');
                    console.error('❌ Tema sıfırlama AJAX hatası:', status, error);
                }
            });
        }
    }
    
    // Renk Tema Uygulama Sistemi
    applyColorTheme(themeName) {
        console.log('🎨 Renk teması uygulanıyor:', themeName);
        
        const colorThemes = {
            'google-material': {
                "primary-color": "#4285F4",
                "primary-light-color": "#82B1FF",
                "primary-dark-color": "#1967D2",
                "secondary-color": "#607D8B",
                "secondary-light-color": "#CFD8DC",
                "secondary-dark-color": "#455A64",
                "accent-color": "#E91E63",
                "success-color": "#4CAF50",
                "info-color": "#2196F3",
                "warning-color": "#FF9800",
                "danger-color": "#F44336",
                "body-bg-color": "#F5F5F5",
                "content-bg-color": "#FFFFFF",
                "background-primary-color": "#FFFFFF",
                "background-secondary-color": "#F5F5F5",
                "background-light-color": "#FAFAFA",
                "background-dark-color": "#E0E0E0",
                "text-primary-color": "#212121",
                "body-text-color": "#212121",
                "text-secondary-color": "#757575",
                "text-light-color": "#FFFFFF",
                "text-dark-color": "#000000",
                "heading-color": "#212121",
                "link-color": "#4285F4",
                "link-hover-color": "#1967D2",
                "text-muted-color": "#9E9E9E",
                "border-color": "#E0E0E0",
                "border-light-color": "#EEEEEE",
                "border-dark-color": "#BDBDBD",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "4",
                "card-border-radius": "4",
                "input-border-radius-1": "4",
                "btn-border-radius": "4",
                "top-contact-and-social-bg-color": "#F5F5F5",
                "top-contact-and-social-link-color": "#757575",
                "top-contact-and-social-link-hover-color": "#4285F4",
                "top-contact-and-social-icon-color": "#757575",
                "top-contact-and-social-icon-hover-color": "#4285F4",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#F5F5F5",
                "top-contact-and-social-link-color-mobile": "#757575",
                "top-contact-and-social-link-hover-color-mobile": "#4285F4",
                "top-contact-and-social-icon-color-mobile": "#757575",
                "top-contact-and-social-icon-hover-color-mobile": "#4285F4",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#212121",
                "shop-menu-container-icon-color-member": "#212121",
                "shop-menu-container-icon-color-favorites": "#212121",
                "shop-menu-container-icon-color-basket": "#212121",
                "shop-menu-container-icon-hover-color": "#4285F4",
                "action-icon-basket-counter-bg-color": "#F44336",
                "shop-menu-container-mobile-icon-color-search": "#212121",
                "shop-menu-container-mobile-icon-color-member": "#212121",
                "shop-menu-container-mobile-icon-color-favorites": "#212121",
                "shop-menu-container-mobile-icon-color-basket": "#212121",
                "mobile-action-icon-phone-bg-color": "#4CAF50",
                "mobile-action-icon-whatsapp-bg-color": "#25D366",
                "mobile-action-icon-basket-bg-color": "#4285F4",
                "mobile-action-icon-basket-counter-bg-color": "#F44336",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "180",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "140",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#FFFFFF",
                "header-min-height": "72",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#E0E0E0",
                "header-mobile-bg-color": "#FFFFFF",
                "header-mobile-min-height": "72",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#E0E0E0",
                "menu-background-color": "#FFFFFF",
                "menu-text-color": "#757575",
                "menu-hover-color": "#212121",
                "menu-hover-bg-color": "#F5F5F5",
                "menu-active-color": "#4285F4",
                "menu-active-bg-color": "rgba(66, 133, 244, 0.1)",
                "mobile-menu-background-color": "#FFFFFF",
                "mobile-menu-text-color": "#212121",
                "hamburger-icon-color": "#212121",
                "mobile-menu-hover-color": "#4285F4",
                "mobile-menu-hover-bg-color": "#F5F5F5",
                "mobile-menu-divider-color": "#E0E0E0",
                "submenu-bg-color": "#FFFFFF",
                "submenu-text-color": "#757575",
                "submenu-hover-color": "#212121",
                "submenu-hover-bg-color": "#F5F5F5",
                "submenu-border-color": "#E0E0E0",
                "submenu-width": "200",
                "menu-font-size": "14",
                "mobile-menu-font-size": "14",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#FFFFFF",
                "product-box-border-color": "#E0E0E0",
                "product-box-hover-border-color": "#BDBDBD",
                "product-title-color": "#212121",
                "product-price-color": "#212121",
                "product-sale-price-color": "#F44336",
                "product-old-price-color": "#757575",
                "product-discount-badge-color": "#4CAF50",
                "add-to-cart-bg-color": "#4285F4",
                "add-to-cart-text-color": "#FFFFFF",
                "add-to-cart-hover-bg-color": "#1967D2",
                "product-box-padding": "15",
                "product-box-border-radius": "4",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#F5F5F5",
                "input-border-color": "rgba(0, 0, 0, 0.42)",
                "input-focus-border-color": "#4285F4",
                "input-text-color": "#212121",
                "input-placeholder-color": "#757575",
                "btn-primary-bg-color": "#4285F4",
                "btn-primary-text-color": "#FFFFFF",
                "btn-primary-hover-bg-color": "#1967D2",
                "btn-primary-border-color": "#4285F4",
                "btn-secondary-bg-color": "transparent",
                "btn-secondary-text-color": "#4285F4",
                "btn-secondary-hover-bg-color": "rgba(66, 133, 244, 0.1)",
                "btn-outline-color": "#4285F4",
                "form-label-color": "#757575",
                "form-required-color": "#F44336",
                "form-error-color": "#F44336",
                "form-success-color": "#4CAF50",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "4",
                "btn-padding-y": "8",
                "btn-padding-x": "16",
                "footer-background-color": "#455A64",
                "footer-text-color": "#CFD8DC",
                "footer-link-color": "#FFFFFF",
                "footer-link-hover-color": "#FAFAFA",
                "copyright-background-color": "#37474F",
                "copyright-text-color": "#B0BEC5",
                "copyright-link-color": "#CFD8DC",
                "copyright-border-top-color": "#607D8B",
                "social-icon-color": "#B0BEC5",
                "social-icon-hover-color": "#FFFFFF",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "48",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            'creative-colors': {
                "primary-color": "#00796B",
                "primary-light-color": "#4DB6AC",
                "primary-dark-color": "#004D40",
                "secondary-color": "#00BCD4",
                "secondary-light-color": "#80DEEA",
                "secondary-dark-color": "#00838F",
                "accent-color": "#FF5722",
                "success-color": "#8BC34A",
                "info-color": "#03A9F4",
                "warning-color": "#FFC107",
                "danger-color": "#F44336",
                "body-bg-color": "#F1F8E9",
                "content-bg-color": "#FFFFFF",
                "background-primary-color": "#FFFFFF",
                "background-secondary-color": "#F1F8E9",
                "background-light-color": "#FAFAFA",
                "background-dark-color": "#E8F5E9",
                "text-primary-color": "#1B2D2B",
                "body-text-color": "#1B2D2B",
                "text-secondary-color": "#4F6360",
                "text-light-color": "#FFFFFF",
                "text-dark-color": "#00251A",
                "heading-color": "#004D40",
                "link-color": "#00796B",
                "link-hover-color": "#FF5722",
                "text-muted-color": "#7C8C88",
                "border-color": "#B2DFDB",
                "border-light-color": "#E0F2F1",
                "border-dark-color": "#80CBC4",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "6",
                "card-border-radius": "6",
                "input-border-radius-1": "6",
                "btn-border-radius": "6",
                "top-contact-and-social-bg-color": "#F1F8E9",
                "top-contact-and-social-link-color": "#4F6360",
                "top-contact-and-social-link-hover-color": "#00796B",
                "top-contact-and-social-icon-color": "#4F6360",
                "top-contact-and-social-icon-hover-color": "#00796B",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#F1F8E9",
                "top-contact-and-social-link-color-mobile": "#4F6360",
                "top-contact-and-social-link-hover-color-mobile": "#00796B",
                "top-contact-and-social-icon-color-mobile": "#4F6360",
                "top-contact-and-social-icon-hover-color-mobile": "#00796B",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#1B2D2B",
                "shop-menu-container-icon-color-member": "#1B2D2B",
                "shop-menu-container-icon-color-favorites": "#1B2D2B",
                "shop-menu-container-icon-color-basket": "#1B2D2B",
                "shop-menu-container-icon-hover-color": "#00796B",
                "action-icon-basket-counter-bg-color": "#FF5722",
                "shop-menu-container-mobile-icon-color-search": "#1B2D2B",
                "shop-menu-container-mobile-icon-color-member": "#1B2D2B",
                "shop-menu-container-mobile-icon-color-favorites": "#1B2D2B",
                "shop-menu-container-mobile-icon-color-basket": "#1B2D2B",
                "mobile-action-icon-phone-bg-color": "#8BC34A",
                "mobile-action-icon-whatsapp-bg-color": "#25D366",
                "mobile-action-icon-basket-bg-color": "#00796B",
                "mobile-action-icon-basket-counter-bg-color": "#FF5722",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "190",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "150",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#FFFFFF",
                "header-min-height": "80",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#E0F2F1",
                "header-mobile-bg-color": "#FFFFFF",
                "header-mobile-min-height": "80",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#E0F2F1",
                "menu-background-color": "#FFFFFF",
                "menu-text-color": "#4F6360",
                "menu-hover-color": "#00796B",
                "menu-hover-bg-color": "#E0F2F1",
                "menu-active-color": "#004D40",
                "menu-active-bg-color": "rgba(0, 121, 107, 0.1)",
                "mobile-menu-background-color": "#FFFFFF",
                "mobile-menu-text-color": "#1B2D2B",
                "hamburger-icon-color": "#1B2D2B",
                "mobile-menu-hover-color": "#00796B",
                "mobile-menu-hover-bg-color": "#E0F2F1",
                "mobile-menu-divider-color": "#E0F2F1",
                "submenu-bg-color": "#FFFFFF",
                "submenu-text-color": "#4F6360",
                "submenu-hover-color": "#004D40",
                "submenu-hover-bg-color": "#E0F2F1",
                "submenu-border-color": "#E0F2F1",
                "submenu-width": "220",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#FFFFFF",
                "product-box-border-color": "#B2DFDB",
                "product-box-hover-border-color": "#00796B",
                "product-title-color": "#1B2D2B",
                "product-price-color": "#004D40",
                "product-sale-price-color": "#F44336",
                "product-old-price-color": "#4F6360",
                "product-discount-badge-color": "#FF5722",
                "add-to-cart-bg-color": "#FF5722",
                "add-to-cart-text-color": "#FFFFFF",
                "add-to-cart-hover-bg-color": "#E64A19",
                "product-box-padding": "15",
                "product-box-border-radius": "6",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#E0F2F1",
                "input-border-color": "#B2DFDB",
                "input-focus-border-color": "#00796B",
                "input-text-color": "#1B2D2B",
                "input-placeholder-color": "#4F6360",
                "btn-primary-bg-color": "#FF5722",
                "btn-primary-text-color": "#FFFFFF",
                "btn-primary-hover-bg-color": "#E64A19",
                "btn-primary-border-color": "#FF5722",
                "btn-secondary-bg-color": "#E0F2F1",
                "btn-secondary-text-color": "#00796B",
                "btn-secondary-hover-bg-color": "#B2DFDB",
                "btn-outline-color": "#00796B",
                "form-label-color": "#1B2D2B",
                "form-required-color": "#F44336",
                "form-error-color": "#F44336",
                "form-success-color": "#8BC34A",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "6",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#004D40",
                "footer-text-color": "#B2DFDB",
                "footer-link-color": "#FFFFFF",
                "footer-link-hover-color": "#FFC107",
                "copyright-background-color": "#00251A",
                "copyright-text-color": "#80CBC4",
                "copyright-link-color": "#B2DFDB",
                "copyright-border-top-color": "#004D40",
                "social-icon-color": "#B2DFDB",
                "social-icon-hover-color": "#FFFFFF",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            'bootstrap-classic': {
                "primary-color": "#337ab7",
                "primary-light-color": "#5bc0de",
                "primary-dark-color": "#286090",
                "secondary-color": "#cccccc",
                "secondary-light-color": "#e6e6e6",
                "secondary-dark-color": "#adadad",
                "accent-color": "#337ab7",
                "success-color": "#5cb85c",
                "info-color": "#5bc0de",
                "warning-color": "#f0ad4e",
                "danger-color": "#d9534f",
                "body-bg-color": "#ffffff",
                "content-bg-color": "#ffffff",
                "background-primary-color": "#ffffff",
                "background-secondary-color": "#f8f9fa",
                "background-light-color": "#f8f9fa",
                "background-dark-color": "#dee2e6",
                "text-primary-color": "#333333",
                "body-text-color": "#333333",
                "text-secondary-color": "#6c757d",
                "text-light-color": "#ffffff",
                "text-dark-color": "#212529",
                "heading-color": "#333333",
                "link-color": "#337ab7",
                "link-hover-color": "#23527c",
                "text-muted-color": "#777777",
                "border-color": "#dddddd",
                "border-light-color": "#eeeeee",
                "border-dark-color": "#cccccc",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "4",
                "card-border-radius": "4",
                "input-border-radius-1": "4",
                "btn-border-radius": "4",
                "top-contact-and-social-bg-color": "#f8f9fa",
                "top-contact-and-social-link-color": "#777777",
                "top-contact-and-social-link-hover-color": "#337ab7",
                "top-contact-and-social-icon-color": "#777777",
                "top-contact-and-social-icon-hover-color": "#337ab7",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#f8f9fa",
                "top-contact-and-social-link-color-mobile": "#777777",
                "top-contact-and-social-link-hover-color-mobile": "#337ab7",
                "top-contact-and-social-icon-color-mobile": "#777777",
                "top-contact-and-social-icon-hover-color-mobile": "#337ab7",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#333333",
                "shop-menu-container-icon-color-member": "#333333",
                "shop-menu-container-icon-color-favorites": "#333333",
                "shop-menu-container-icon-color-basket": "#333333",
                "shop-menu-container-icon-hover-color": "#337ab7",
                "action-icon-basket-counter-bg-color": "#d9534f",
                "shop-menu-container-mobile-icon-color-search": "#333333",
                "shop-menu-container-mobile-icon-color-member": "#333333",
                "shop-menu-container-mobile-icon-color-favorites": "#333333",
                "shop-menu-container-mobile-icon-color-basket": "#333333",
                "mobile-action-icon-phone-bg-color": "#5cb85c",
                "mobile-action-icon-whatsapp-bg-color": "#25d366",
                "mobile-action-icon-basket-bg-color": "#337ab7",
                "mobile-action-icon-basket-counter-bg-color": "#d9534f",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "150",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "120",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#f8f9fa",
                "header-min-height": "70",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#e7e7e7",
                "header-mobile-bg-color": "#f8f9fa",
                "header-mobile-min-height": "70",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#e7e7e7",
                "menu-background-color": "#f8f9fa",
                "menu-text-color": "#777777",
                "menu-hover-color": "#333333",
                "menu-hover-bg-color": "#e7e7e7",
                "menu-active-color": "#555555",
                "menu-active-bg-color": "#e7e7e7",
                "mobile-menu-background-color": "#ffffff",
                "mobile-menu-text-color": "#333333",
                "hamburger-icon-color": "#777777",
                "mobile-menu-hover-color": "#337ab7",
                "mobile-menu-hover-bg-color": "#f8f9fa",
                "mobile-menu-divider-color": "#e7e7e7",
                "submenu-bg-color": "#ffffff",
                "submenu-text-color": "#333333",
                "submenu-hover-color": "#ffffff",
                "submenu-hover-bg-color": "#337ab7",
                "submenu-border-color": "#e7e7e7",
                "submenu-width": "200",
                "menu-font-size": "14",
                "mobile-menu-font-size": "14",
                "menu-height": "50",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#ffffff",
                "product-box-border-color": "#dddddd",
                "product-box-hover-border-color": "#aaaaaa",
                "product-title-color": "#333333",
                "product-price-color": "#333333",
                "product-sale-price-color": "#d9534f",
                "product-old-price-color": "#777777",
                "product-discount-badge-color": "#5cb85c",
                "add-to-cart-bg-color": "#337ab7",
                "add-to-cart-text-color": "#ffffff",
                "add-to-cart-hover-bg-color": "#286090",
                "product-box-padding": "15",
                "product-box-border-radius": "4",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#ffffff",
                "input-border-color": "#cccccc",
                "input-focus-border-color": "#66afe9",
                "input-text-color": "#555555",
                "input-placeholder-color": "#999999",
                "btn-primary-bg-color": "#337ab7",
                "btn-primary-text-color": "#ffffff",
                "btn-primary-hover-bg-color": "#286090",
                "btn-primary-border-color": "#2e6da4",
                "btn-secondary-bg-color": "#ffffff",
                "btn-secondary-text-color": "#333333",
                "btn-secondary-hover-bg-color": "#e6e6e6",
                "btn-outline-color": "#337ab7",
                "form-label-color": "#333333",
                "form-required-color": "#d9534f",
                "form-error-color": "#d9534f",
                "form-success-color": "#5cb85c",
                "input-height": "34",
                "input-padding": "12",
                "input-border-radius": "4",
                "btn-padding-y": "6",
                "btn-padding-x": "12",
                "footer-background-color": "#222222",
                "footer-text-color": "#9d9d9d",
                "footer-link-color": "#ffffff",
                "footer-link-hover-color": "#dddddd",
                "copyright-background-color": "#111111",
                "copyright-text-color": "#777777",
                "copyright-link-color": "#9d9d9d",
                "copyright-border-top-color": "#333333",
                "social-icon-color": "#9d9d9d",
                "social-icon-hover-color": "#ffffff",
                "social-icon-size": "20",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1170",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.428",
                "mobile-section-margin": "20",
                "mobile-card-margin": "15",
                "mobile-button-height": "34",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "15",
                "spacing-lg": "20",
                "spacing-xl": "30",
                "spacing-xxl": "40",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "23%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            'dark-modern': {
                "primary-color": "#03A9F4",
                "primary-light-color": "#67DAFF",
                "primary-dark-color": "#007AC1",
                "secondary-color": "#37474F",
                "secondary-light-color": "#62727b",
                "secondary-dark-color": "#102027",
                "accent-color": "#03A9F4",
                "success-color": "#81C784",
                "info-color": "#4FC3F7",
                "warning-color": "#FFB74D",
                "danger-color": "#E57373",
                "body-bg-color": "#121212",
                "content-bg-color": "#1E1E1E",
                "background-primary-color": "#1E1E1E",
                "background-secondary-color": "#242424",
                "background-light-color": "#2c2c2c",
                "background-dark-color": "#121212",
                "text-primary-color": "#EAEAEA",
                "body-text-color": "#EAEAEA",
                "text-secondary-color": "#A0A0A0",
                "text-light-color": "#FFFFFF",
                "text-dark-color": "#EAEAEA",
                "heading-color": "#FFFFFF",
                "link-color": "#03A9F4",
                "link-hover-color": "#67DAFF",
                "text-muted-color": "#757575",
                "border-color": "#333333",
                "border-light-color": "#424242",
                "border-dark-color": "#212121",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "6",
                "card-border-radius": "6",
                "input-border-radius-1": "6",
                "btn-border-radius": "6",
                "top-contact-and-social-bg-color": "#121212",
                "top-contact-and-social-link-color": "#A0A0A0",
                "top-contact-and-social-link-hover-color": "#03A9F4",
                "top-contact-and-social-icon-color": "#A0A0A0",
                "top-contact-and-social-icon-hover-color": "#03A9F4",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#121212",
                "top-contact-and-social-link-color-mobile": "#A0A0A0",
                "top-contact-and-social-link-hover-color-mobile": "#03A9F4",
                "top-contact-and-social-icon-color-mobile": "#A0A0A0",
                "top-contact-and-social-icon-hover-color-mobile": "#03A9F4",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#EAEAEA",
                "shop-menu-container-icon-color-member": "#EAEAEA",
                "shop-menu-container-icon-color-favorites": "#EAEAEA",
                "shop-menu-container-icon-color-basket": "#EAEAEA",
                "shop-menu-container-icon-hover-color": "#03A9F4",
                "action-icon-basket-counter-bg-color": "#E57373",
                "shop-menu-container-mobile-icon-color-search": "#EAEAEA",
                "shop-menu-container-mobile-icon-color-member": "#EAEAEA",
                "shop-menu-container-mobile-icon-color-favorites": "#EAEAEA",
                "shop-menu-container-mobile-icon-color-basket": "#EAEAEA",
                "mobile-action-icon-phone-bg-color": "#81C784",
                "mobile-action-icon-whatsapp-bg-color": "#25D366",
                "mobile-action-icon-basket-bg-color": "#03A9F4",
                "mobile-action-icon-basket-counter-bg-color": "#E57373",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "200",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "150",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#1E1E1E",
                "header-min-height": "80",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#2c2c2c",
                "header-mobile-bg-color": "#1E1E1E",
                "header-mobile-min-height": "80",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#2c2c2c",
                "menu-background-color": "#1E1E1E",
                "menu-text-color": "#A0A0A0",
                "menu-hover-color": "#FFFFFF",
                "menu-hover-bg-color": "#2c2c2c",
                "menu-active-color": "#FFFFFF",
                "menu-active-bg-color": "#03A9F4",
                "mobile-menu-background-color": "#1E1E1E",
                "mobile-menu-text-color": "#ECEFF1",
                "hamburger-icon-color": "#ECEFF1",
                "mobile-menu-hover-color": "#FFFFFF",
                "mobile-menu-hover-bg-color": "#03A9F4",
                "mobile-menu-divider-color": "#2c2c2c",
                "submenu-bg-color": "#2c2c2c",
                "submenu-text-color": "#A0A0A0",
                "submenu-hover-color": "#FFFFFF",
                "submenu-hover-bg-color": "#03A9F4",
                "submenu-border-color": "#333333",
                "submenu-width": "200",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#242424",
                "product-box-border-color": "#333333",
                "product-box-hover-border-color": "#03A9F4",
                "product-title-color": "#EAEAEA",
                "product-price-color": "#FFFFFF",
                "product-sale-price-color": "#E57373",
                "product-old-price-color": "#A0A0A0",
                "product-discount-badge-color": "#FFB74D",
                "add-to-cart-bg-color": "#03A9F4",
                "add-to-cart-text-color": "#000000",
                "add-to-cart-hover-bg-color": "#67DAFF",
                "product-box-padding": "15",
                "product-box-border-radius": "6",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#2c2c2c",
                "input-border-color": "#424242",
                "input-focus-border-color": "#03A9F4",
                "input-text-color": "#EAEAEA",
                "input-placeholder-color": "#757575",
                "btn-primary-bg-color": "#03A9F4",
                "btn-primary-text-color": "#000000",
                "btn-primary-hover-bg-color": "#67DAFF",
                "btn-primary-border-color": "#03A9F4",
                "btn-secondary-bg-color": "#37474F",
                "btn-secondary-text-color": "#EAEAEA",
                "btn-secondary-hover-bg-color": "#62727b",
                "btn-outline-color": "#03A9F4",
                "form-label-color": "#EAEAEA",
                "form-required-color": "#E57373",
                "form-error-color": "#E57373",
                "form-success-color": "#81C784",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "6",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#102027",
                "footer-text-color": "#A0A0A0",
                "footer-link-color": "#EAEAEA",
                "footer-link-hover-color": "#03A9F4",
                "copyright-background-color": "#121212",
                "copyright-text-color": "#757575",
                "copyright-link-color": "#A0A0A0",
                "copyright-border-top-color": "#2c2c2c",
                "social-icon-color": "#A0A0A0",
                "social-icon-hover-color": "#03A9F4",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            "chic-red" : {
                "primary-color": "#a4243b",
                "primary-light-color": "#d85b6b",
                "primary-dark-color": "#700015",
                "secondary-color": "#6c757d",
                "secondary-light-color": "#e9ecef",
                "secondary-dark-color": "#495057",
                "accent-color": "#d85b6b",
                "success-color": "#28a745",
                "info-color": "#17a2b8",
                "warning-color": "#ffc107",
                "danger-color": "#dc3545",
                "body-bg-color": "#fffdfa",
                "content-bg-color": "#ffffff",
                "background-primary-color": "#ffffff",
                "background-secondary-color": "#f8f9fa",
                "background-light-color": "#fdfdfe",
                "background-dark-color": "#e9ecef",
                "text-primary-color": "#2d2d2d",
                "body-text-color": "#2d2d2d",
                "text-secondary-color": "#6c757d",
                "text-light-color": "#ffffff",
                "text-dark-color": "#1a1a1a",
                "heading-color": "#1a1a1a",
                "link-color": "#a4243b",
                "link-hover-color": "#700015",
                "text-muted-color": "#999999",
                "border-color": "#eaeaea",
                "border-light-color": "#f1f3f4",
                "border-dark-color": "#dee2e6",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "6",
                "card-border-radius": "6",
                "input-border-radius-1": "6",
                "btn-border-radius": "6",
                "top-contact-and-social-bg-color": "#fffdfa",
                "top-contact-and-social-link-color": "#6c757d",
                "top-contact-and-social-link-hover-color": "#a4243b",
                "top-contact-and-social-icon-color": "#6c757d",
                "top-contact-and-social-icon-hover-color": "#a4243b",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#ffffff",
                "top-contact-and-social-link-color-mobile": "#6c757d",
                "top-contact-and-social-link-hover-color-mobile": "#a4243b",
                "top-contact-and-social-icon-color-mobile": "#6c757d",
                "top-contact-and-social-icon-hover-color-mobile": "#a4243b",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#2d2d2d",
                "shop-menu-container-icon-color-member": "#2d2d2d",
                "shop-menu-container-icon-color-favorites": "#2d2d2d",
                "shop-menu-container-icon-color-basket": "#2d2d2d",
                "shop-menu-container-icon-hover-color": "#a4243b",
                "action-icon-basket-counter-bg-color": "#a4243b",
                "shop-menu-container-mobile-icon-color-search": "#2d2d2d",
                "shop-menu-container-mobile-icon-color-member": "#2d2d2d",
                "shop-menu-container-mobile-icon-color-favorites": "#2d2d2d",
                "shop-menu-container-mobile-icon-color-basket": "#2d2d2d",
                "mobile-action-icon-phone-bg-color": "#28a745",
                "mobile-action-icon-whatsapp-bg-color": "#25d366",
                "mobile-action-icon-basket-bg-color": "#a4243b",
                "mobile-action-icon-basket-counter-bg-color": "#700015",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "200",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "150",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#ffffff",
                "header-min-height": "80",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#eaeaea",
                "header-mobile-bg-color": "#ffffff",
                "header-mobile-min-height": "80",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#eaeaea",
                "menu-background-color": "#ffffff",
                "menu-text-color": "#6c757d",
                "menu-hover-color": "#a4243b",
                "menu-hover-bg-color": "#fff8f9",
                "menu-active-color": "#a4243b",
                "menu-active-bg-color": "rgba(164, 36, 59, 0.05)",
                "mobile-menu-background-color": "#ffffff",
                "mobile-menu-text-color": "#2d2d2d",
                "hamburger-icon-color": "#2d2d2d",
                "mobile-menu-hover-color": "#a4243b",
                "mobile-menu-hover-bg-color": "#fff8f9",
                "mobile-menu-divider-color": "#eaeaea",
                "submenu-bg-color": "#ffffff",
                "submenu-text-color": "#6c757d",
                "submenu-hover-color": "#a4243b",
                "submenu-hover-bg-color": "#fff8f9",
                "submenu-border-color": "#eaeaea",
                "submenu-width": "220",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#ffffff",
                "product-box-border-color": "#eaeaea",
                "product-box-hover-border-color": "#d85b6b",
                "product-title-color": "#2d2d2d",
                "product-price-color": "#1a1a1a",
                "product-sale-price-color": "#a4243b",
                "product-old-price-color": "#6c757d",
                "product-discount-badge-color": "#a4243b",
                "add-to-cart-bg-color": "#a4243b",
                "add-to-cart-text-color": "#ffffff",
                "add-to-cart-hover-bg-color": "#700015",
                "product-box-padding": "15",
                "product-box-border-radius": "6",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#fdfdfe",
                "input-border-color": "#dee2e6",
                "input-focus-border-color": "#a4243b",
                "input-text-color": "#2d2d2d",
                "input-placeholder-color": "#999999",
                "btn-primary-bg-color": "#a4243b",
                "btn-primary-text-color": "#ffffff",
                "btn-primary-hover-bg-color": "#700015",
                "btn-primary-border-color": "#a4243b",
                "btn-secondary-bg-color": "#fce7f3",
                "btn-secondary-text-color": "#a4243b",
                "btn-secondary-hover-bg-color": "#fbcfe8",
                "btn-outline-color": "#a4243b",
                "form-label-color": "#2d2d2d",
                "form-required-color": "#dc3545",
                "form-error-color": "#dc3545",
                "form-success-color": "#28a745",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "6",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#2d2d2d",
                "footer-text-color": "#e9ecef",
                "footer-link-color": "#ffffff",
                "footer-link-hover-color": "#d85b6b",
                "copyright-background-color": "#1a1a1a",
                "copyright-text-color": "#adb5bd",
                "copyright-link-color": "#e9ecef",
                "copyright-border-top-color": "#495057",
                "social-icon-color": "#adb5bd",
                "social-icon-hover-color": "#ffffff",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            "minimal-light" : {
                "primary-color": "#84a98c",
                "primary-light-color": "#b4dacf",
                "primary-dark-color": "#567a5e",
                "secondary-color": "#cad2c5",
                "secondary-light-color": "#f2f4f1",
                "secondary-dark-color": "#99a196",
                "accent-color": "#84a98c",
                "success-color": "#28a745",
                "info-color": "#17a2b8",
                "warning-color": "#ffc107",
                "danger-color": "#dc3545",
                "body-bg-color": "#f8f9fa",
                "content-bg-color": "#ffffff",
                "background-primary-color": "#ffffff",
                "background-secondary-color": "#f8f9fa",
                "background-light-color": "#ffffff",
                "background-dark-color": "#e9ecef",
                "text-primary-color": "#212529",
                "body-text-color": "#212529",
                "text-secondary-color": "#6c757d",
                "text-light-color": "#ffffff",
                "text-dark-color": "#000000",
                "heading-color": "#343a40",
                "link-color": "#84a98c",
                "link-hover-color": "#567a5e",
                "text-muted-color": "#999999",
                "border-color": "#e9ecef",
                "border-light-color": "#f1f3f4",
                "border-dark-color": "#dee2e6",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "4",
                "card-border-radius": "4",
                "input-border-radius-1": "4",
                "btn-border-radius": "4",
                "top-contact-and-social-bg-color": "#f8f9fa",
                "top-contact-and-social-link-color": "#6c757d",
                "top-contact-and-social-link-hover-color": "#84a98c",
                "top-contact-and-social-icon-color": "#6c757d",
                "top-contact-and-social-icon-hover-color": "#84a98c",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#ffffff",
                "top-contact-and-social-link-color-mobile": "#6c757d",
                "top-contact-and-social-link-hover-color-mobile": "#84a98c",
                "top-contact-and-social-icon-color-mobile": "#6c757d",
                "top-contact-and-social-icon-hover-color-mobile": "#84a98c",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#343a40",
                "shop-menu-container-icon-color-member": "#343a40",
                "shop-menu-container-icon-color-favorites": "#343a40",
                "shop-menu-container-icon-color-basket": "#343a40",
                "shop-menu-container-icon-hover-color": "#84a98c",
                "action-icon-basket-counter-bg-color": "#84a98c",
                "shop-menu-container-mobile-icon-color-search": "#343a40",
                "shop-menu-container-mobile-icon-color-member": "#343a40",
                "shop-menu-container-mobile-icon-color-favorites": "#343a40",
                "shop-menu-container-mobile-icon-color-basket": "#343a40",
                "mobile-action-icon-phone-bg-color": "#28a745",
                "mobile-action-icon-whatsapp-bg-color": "#25d366",
                "mobile-action-icon-basket-bg-color": "#84a98c",
                "mobile-action-icon-basket-counter-bg-color": "#567a5e",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "180",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "140",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#ffffff",
                "header-min-height": "80",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#e9ecef",
                "header-mobile-bg-color": "#ffffff",
                "header-mobile-min-height": "80",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#e9ecef",
                "menu-background-color": "#ffffff",
                "menu-text-color": "#6c757d",
                "menu-hover-color": "#212529",
                "menu-hover-bg-color": "#f8f9fa",
                "menu-active-color": "#84a98c",
                "menu-active-bg-color": "rgba(132, 169, 140, 0.1)",
                "mobile-menu-background-color": "#ffffff",
                "mobile-menu-text-color": "#212529",
                "hamburger-icon-color": "#212529",
                "mobile-menu-hover-color": "#84a98c",
                "mobile-menu-hover-bg-color": "#f8f9fa",
                "mobile-menu-divider-color": "#e9ecef",
                "submenu-bg-color": "#ffffff",
                "submenu-text-color": "#6c757d",
                "submenu-hover-color": "#212529",
                "submenu-hover-bg-color": "#f8f9fa",
                "submenu-border-color": "#e9ecef",
                "submenu-width": "220",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#ffffff",
                "product-box-border-color": "#e9ecef",
                "product-box-hover-border-color": "#cad2c5",
                "product-title-color": "#343a40",
                "product-price-color": "#212529",
                "product-sale-price-color": "#dc3545",
                "product-old-price-color": "#6c757d",
                "product-discount-badge-color": "#84a98c",
                "add-to-cart-bg-color": "#343a40",
                "add-to-cart-text-color": "#ffffff",
                "add-to-cart-hover-bg-color": "#212529",
                "product-box-padding": "15",
                "product-box-border-radius": "4",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#f8f9fa",
                "input-border-color": "#dee2e6",
                "input-focus-border-color": "#84a98c",
                "input-text-color": "#212529",
                "input-placeholder-color": "#999999",
                "btn-primary-bg-color": "#84a98c",
                "btn-primary-text-color": "#ffffff",
                "btn-primary-hover-bg-color": "#567a5e",
                "btn-primary-border-color": "#84a98c",
                "btn-secondary-bg-color": "#e9ecef",
                "btn-secondary-text-color": "#343a40",
                "btn-secondary-hover-bg-color": "#dee2e6",
                "btn-outline-color": "#84a98c",
                "form-label-color": "#343a40",
                "form-required-color": "#dc3545",
                "form-error-color": "#dc3545",
                "form-success-color": "#28a745",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "4",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#343a40",
                "footer-text-color": "#e9ecef",
                "footer-link-color": "#ffffff",
                "footer-link-hover-color": "#b4dacf",
                "copyright-background-color": "#212529",
                "copyright-text-color": "#adb5bd",
                "copyright-link-color": "#e9ecef",
                "copyright-border-top-color": "#495057",
                "social-icon-color": "#adb5bd",
                "social-icon-hover-color": "#ffffff",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            "corporate-blue" : {
                "primary-color": "#0d6efd",
                "primary-light-color": "#6ea8fe",
                "primary-dark-color": "#0a58ca",
                "secondary-color": "#6c757d",
                "secondary-light-color": "#e9ecef",
                "secondary-dark-color": "#495057",
                "accent-color": "#0d6efd",
                "success-color": "#198754",
                "info-color": "#0dcaf0",
                "warning-color": "#ffc107",
                "danger-color": "#dc3545",
                "body-bg-color": "#f8f9fa",
                "content-bg-color": "#ffffff",
                "background-primary-color": "#ffffff",
                "background-secondary-color": "#f8f9fa",
                "background-light-color": "#ffffff",
                "background-dark-color": "#dee2e6",
                "text-primary-color": "#212529",
                "body-text-color": "#212529",
                "text-secondary-color": "#6c757d",
                "text-light-color": "#ffffff",
                "text-dark-color": "#000000",
                "heading-color": "#0a2540",
                "link-color": "#0d6efd",
                "link-hover-color": "#0a58ca",
                "text-muted-color": "#869ab8",
                "border-color": "#dee2e6",
                "border-light-color": "#f1f3f4",
                "border-dark-color": "#adb5bd",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "5",
                "card-border-radius": "5",
                "input-border-radius-1": "5",
                "btn-border-radius": "5",
                "top-contact-and-social-bg-color": "#f8f9fa",
                "top-contact-and-social-link-color": "#6c757d",
                "top-contact-and-social-link-hover-color": "#0d6efd",
                "top-contact-and-social-icon-color": "#6c757d",
                "top-contact-and-social-icon-hover-color": "#0d6efd",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#ffffff",
                "top-contact-and-social-link-color-mobile": "#6c757d",
                "top-contact-and-social-link-hover-color-mobile": "#0d6efd",
                "top-contact-and-social-icon-color-mobile": "#6c757d",
                "top-contact-and-social-icon-hover-color-mobile": "#0d6efd",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#212529",
                "shop-menu-container-icon-color-member": "#212529",
                "shop-menu-container-icon-color-favorites": "#212529",
                "shop-menu-container-icon-color-basket": "#212529",
                "shop-menu-container-icon-hover-color": "#0d6efd",
                "action-icon-basket-counter-bg-color": "#dc3545",
                "shop-menu-container-mobile-icon-color-search": "#212529",
                "shop-menu-container-mobile-icon-color-member": "#212529",
                "shop-menu-container-mobile-icon-color-favorites": "#212529",
                "shop-menu-container-mobile-icon-color-basket": "#212529",
                "mobile-action-icon-phone-bg-color": "#198754",
                "mobile-action-icon-whatsapp-bg-color": "#25d366",
                "mobile-action-icon-basket-bg-color": "#0d6efd",
                "mobile-action-icon-basket-counter-bg-color": "#dc3545",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "180",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "140",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#ffffff",
                "header-min-height": "75",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#dee2e6",
                "header-mobile-bg-color": "#ffffff",
                "header-mobile-min-height": "75",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#dee2e6",
                "menu-background-color": "#ffffff",
                "menu-text-color": "#495057",
                "menu-hover-color": "#000000",
                "menu-hover-bg-color": "#f8f9fa",
                "menu-active-color": "#0d6efd",
                "menu-active-bg-color": "rgba(13, 110, 253, 0.05)",
                "mobile-menu-background-color": "#ffffff",
                "mobile-menu-text-color": "#212529",
                "hamburger-icon-color": "#212529",
                "mobile-menu-hover-color": "#0d6efd",
                "mobile-menu-hover-bg-color": "#f8f9fa",
                "mobile-menu-divider-color": "#dee2e6",
                "submenu-bg-color": "#ffffff",
                "submenu-text-color": "#495057",
                "submenu-hover-color": "#000000",
                "submenu-hover-bg-color": "#f8f9fa",
                "submenu-border-color": "#dee2e6",
                "submenu-width": "220",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#ffffff",
                "product-box-border-color": "#dee2e6",
                "product-box-hover-border-color": "#0d6efd",
                "product-title-color": "#0a2540",
                "product-price-color": "#212529",
                "product-sale-price-color": "#dc3545",
                "product-old-price-color": "#6c757d",
                "product-discount-badge-color": "#198754",
                "add-to-cart-bg-color": "#0d6efd",
                "add-to-cart-text-color": "#ffffff",
                "add-to-cart-hover-bg-color": "#0a58ca",
                "product-box-padding": "15",
                "product-box-border-radius": "5",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#ffffff",
                "input-border-color": "#ced4da",
                "input-focus-border-color": "#0d6efd",
                "input-text-color": "#212529",
                "input-placeholder-color": "#6c757d",
                "btn-primary-bg-color": "#0d6efd",
                "btn-primary-text-color": "#ffffff",
                "btn-primary-hover-bg-color": "#0a58ca",
                "btn-primary-border-color": "#0d6efd",
                "btn-secondary-bg-color": "#6c757d",
                "btn-secondary-text-color": "#ffffff",
                "btn-secondary-hover-bg-color": "#5c636a",
                "btn-outline-color": "#0d6efd",
                "form-label-color": "#212529",
                "form-required-color": "#dc3545",
                "form-error-color": "#dc3545",
                "form-success-color": "#198754",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "5",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#0a2540",
                "footer-text-color": "#adb5bd",
                "footer-link-color": "#ffffff",
                "footer-link-hover-color": "#6ea8fe",
                "copyright-background-color": "#051321",
                "copyright-text-color": "#869ab8",
                "copyright-link-color": "#adb5bd",
                "copyright-border-top-color": "#213d5b",
                "social-icon-color": "#adb5bd",
                "social-icon-hover-color": "#ffffff",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            "ecommerce-orange" : {
                "primary-color": "#ff7f00",
                "primary-light-color": "#ffad4e",
                "primary-dark-color": "#c55000",
                "secondary-color": "#495057",
                "secondary-light-color": "#e9ecef",
                "secondary-dark-color": "#343a40",
                "accent-color": "#ff7f00",
                "success-color": "#198754",
                "info-color": "#0dcaf0",
                "warning-color": "#ffc107",
                "danger-color": "#dc3545",
                "body-bg-color": "#f8f9fa",
                "content-bg-color": "#ffffff",
                "background-primary-color": "#ffffff",
                "background-secondary-color": "#f8f9fa",
                "background-light-color": "#ffffff",
                "background-dark-color": "#e9ecef",
                "text-primary-color": "#212529",
                "body-text-color": "#212529",
                "text-secondary-color": "#495057",
                "text-light-color": "#ffffff",
                "text-dark-color": "#000000",
                "heading-color": "#343a40",
                "link-color": "#ff7f00",
                "link-hover-color": "#c55000",
                "text-muted-color": "#6c757d",
                "border-color": "#dee2e6",
                "border-light-color": "#f1f3f4",
                "border-dark-color": "#adb5bd",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "6",
                "card-border-radius": "6",
                "input-border-radius-1": "6",
                "btn-border-radius": "6",
                "top-contact-and-social-bg-color": "#f8f9fa",
                "top-contact-and-social-link-color": "#495057",
                "top-contact-and-social-link-hover-color": "#ff7f00",
                "top-contact-and-social-icon-color": "#495057",
                "top-contact-and-social-icon-hover-color": "#ff7f00",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#ffffff",
                "top-contact-and-social-link-color-mobile": "#495057",
                "top-contact-and-social-link-hover-color-mobile": "#ff7f00",
                "top-contact-and-social-icon-color-mobile": "#495057",
                "top-contact-and-social-icon-hover-color-mobile": "#ff7f00",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#212529",
                "shop-menu-container-icon-color-member": "#212529",
                "shop-menu-container-icon-color-favorites": "#212529",
                "shop-menu-container-icon-color-basket": "#212529",
                "shop-menu-container-icon-hover-color": "#ff7f00",
                "action-icon-basket-counter-bg-color": "#ff7f00",
                "shop-menu-container-mobile-icon-color-search": "#212529",
                "shop-menu-container-mobile-icon-color-member": "#212529",
                "shop-menu-container-mobile-icon-color-favorites": "#212529",
                "shop-menu-container-mobile-icon-color-basket": "#212529",
                "mobile-action-icon-phone-bg-color": "#198754",
                "mobile-action-icon-whatsapp-bg-color": "#25d366",
                "mobile-action-icon-basket-bg-color": "#ff7f00",
                "mobile-action-icon-basket-counter-bg-color": "#c55000",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "190",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "150",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#ffffff",
                "header-min-height": "80",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#dee2e6",
                "header-mobile-bg-color": "#ffffff",
                "header-mobile-min-height": "80",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#dee2e6",
                "menu-background-color": "#ffffff",
                "menu-text-color": "#495057",
                "menu-hover-color": "#000000",
                "menu-hover-bg-color": "#f8f9fa",
                "menu-active-color": "#ff7f00",
                "menu-active-bg-color": "rgba(255, 127, 0, 0.05)",
                "mobile-menu-background-color": "#ffffff",
                "mobile-menu-text-color": "#212529",
                "hamburger-icon-color": "#212529",
                "mobile-menu-hover-color": "#ff7f00",
                "mobile-menu-hover-bg-color": "#f8f9fa",
                "mobile-menu-divider-color": "#dee2e6",
                "submenu-bg-color": "#ffffff",
                "submenu-text-color": "#495057",
                "submenu-hover-color": "#000000",
                "submenu-hover-bg-color": "#f8f9fa",
                "submenu-border-color": "#dee2e6",
                "submenu-width": "220",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#ffffff",
                "product-box-border-color": "#dee2e6",
                "product-box-hover-border-color": "#ffad4e",
                "product-title-color": "#343a40",
                "product-price-color": "#212529",
                "product-sale-price-color": "#dc3545",
                "product-old-price-color": "#6c757d",
                "product-discount-badge-color": "#ff7f00",
                "add-to-cart-bg-color": "#ff7f00",
                "add-to-cart-text-color": "#ffffff",
                "add-to-cart-hover-bg-color": "#c55000",
                "product-box-padding": "15",
                "product-box-border-radius": "6",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#f8f9fa",
                "input-border-color": "#ced4da",
                "input-focus-border-color": "#ff7f00",
                "input-text-color": "#212529",
                "input-placeholder-color": "#6c757d",
                "btn-primary-bg-color": "#ff7f00",
                "btn-primary-text-color": "#ffffff",
                "btn-primary-hover-bg-color": "#c55000",
                "btn-primary-border-color": "#ff7f00",
                "btn-secondary-bg-color": "#6c757d",
                "btn-secondary-text-color": "#ffffff",
                "btn-secondary-hover-bg-color": "#5a6268",
                "btn-outline-color": "#ff7f00",
                "form-label-color": "#212529",
                "form-required-color": "#dc3545",
                "form-error-color": "#dc3545",
                "form-success-color": "#198754",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "6",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#343a40",
                "footer-text-color": "#e9ecef",
                "footer-link-color": "#ffffff",
                "footer-link-hover-color": "#ffad4e",
                "copyright-background-color": "#212529",
                "copyright-text-color": "#adb5bd",
                "copyright-link-color": "#e9ecef",
                "copyright-border-top-color": "#495057",
                "social-icon-color": "#adb5bd",
                "social-icon-hover-color": "#ffffff",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            "lovely-purple" : {
                "primary-color": "#a855f7",
                "primary-light-color": "#d8b4fe",
                "primary-dark-color": "#7e22ce",
                "secondary-color": "#f472b6",
                "secondary-light-color": "#fbcfe8",
                "secondary-dark-color": "#db2777",
                "accent-color": "#f472b6",
                "success-color": "#4ade80",
                "info-color": "#60a5fa",
                "warning-color": "#facc15",
                "danger-color": "#f87171",
                "body-bg-color": "#f5f3ff",
                "content-bg-color": "#ffffff",
                "background-primary-color": "#ffffff",
                "background-secondary-color": "#f5f3ff",
                "background-light-color": "#fafafa",
                "background-dark-color": "#e9d5ff",
                "text-primary-color": "#3c3645",
                "body-text-color": "#3c3645",
                "text-secondary-color": "#6b7280",
                "text-light-color": "#ffffff",
                "text-dark-color": "#1f1d24",
                "heading-color": "#1f1d24",
                "link-color": "#a855f7",
                "link-hover-color": "#7e22ce",
                "text-muted-color": "#9ca3af",
                "border-color": "#e9d5ff",
                "border-light-color": "#f5f3ff",
                "border-dark-color": "#c084fc",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "8",
                "card-border-radius": "8",
                "input-border-radius-1": "8",
                "btn-border-radius": "8",
                "top-contact-and-social-bg-color": "#f5f3ff",
                "top-contact-and-social-link-color": "#6b7280",
                "top-contact-and-social-link-hover-color": "#a855f7",
                "top-contact-and-social-icon-color": "#6b7280",
                "top-contact-and-social-icon-hover-color": "#a855f7",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#f5f3ff",
                "top-contact-and-social-link-color-mobile": "#6b7280",
                "top-contact-and-social-link-hover-color-mobile": "#a855f7",
                "top-contact-and-social-icon-color-mobile": "#6b7280",
                "top-contact-and-social-icon-hover-color-mobile": "#a855f7",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#3c3645",
                "shop-menu-container-icon-color-member": "#3c3645",
                "shop-menu-container-icon-color-favorites": "#3c3645",
                "shop-menu-container-icon-color-basket": "#3c3645",
                "shop-menu-container-icon-hover-color": "#a855f7",
                "action-icon-basket-counter-bg-color": "#f472b6",
                "shop-menu-container-mobile-icon-color-search": "#3c3645",
                "shop-menu-container-mobile-icon-color-member": "#3c3645",
                "shop-menu-container-mobile-icon-color-favorites": "#3c3645",
                "shop-menu-container-mobile-icon-color-basket": "#3c3645",
                "mobile-action-icon-phone-bg-color": "#4ade80",
                "mobile-action-icon-whatsapp-bg-color": "#25d366",
                "mobile-action-icon-basket-bg-color": "#a855f7",
                "mobile-action-icon-basket-counter-bg-color": "#f472b6",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "200",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "150",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#ffffff",
                "header-min-height": "80",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#e9d5ff",
                "header-mobile-bg-color": "#ffffff",
                "header-mobile-min-height": "80",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#e9d5ff",
                "menu-background-color": "#ffffff",
                "menu-text-color": "#6b7280",
                "menu-hover-color": "#a855f7",
                "menu-hover-bg-color": "#f5f3ff",
                "menu-active-color": "#7e22ce",
                "menu-active-bg-color": "#f5f3ff",
                "mobile-menu-background-color": "#ffffff",
                "mobile-menu-text-color": "#3c3645",
                "hamburger-icon-color": "#3c3645",
                "mobile-menu-hover-color": "#a855f7",
                "mobile-menu-hover-bg-color": "#f5f3ff",
                "mobile-menu-divider-color": "#e9d5ff",
                "submenu-bg-color": "#ffffff",
                "submenu-text-color": "#6b7280",
                "submenu-hover-color": "#7e22ce",
                "submenu-hover-bg-color": "#f5f3ff",
                "submenu-border-color": "#e9d5ff",
                "submenu-width": "220",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#ffffff",
                "product-box-border-color": "#e9d5ff",
                "product-box-hover-border-color": "#a855f7",
                "product-title-color": "#3c3645",
                "product-price-color": "#1f1d24",
                "product-sale-price-color": "#db2777",
                "product-old-price-color": "#6b7280",
                "product-discount-badge-color": "#f472b6",
                "add-to-cart-bg-color": "#a855f7",
                "add-to-cart-text-color": "#ffffff",
                "add-to-cart-hover-bg-color": "#7e22ce",
                "product-box-padding": "15",
                "product-box-border-radius": "8",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#f5f3ff",
                "input-border-color": "#e9d5ff",
                "input-focus-border-color": "#a855f7",
                "input-text-color": "#3c3645",
                "input-placeholder-color": "#9ca3af",
                "btn-primary-bg-color": "#a855f7",
                "btn-primary-text-color": "#ffffff",
                "btn-primary-hover-bg-color": "#7e22ce",
                "btn-primary-border-color": "#a855f7",
                "btn-secondary-bg-color": "#f5f3ff",
                "btn-secondary-text-color": "#a855f7",
                "btn-secondary-hover-bg-color": "#e9d5ff",
                "btn-outline-color": "#a855f7",
                "form-label-color": "#3c3645",
                "form-required-color": "#db2777",
                "form-error-color": "#f87171",
                "form-success-color": "#4ade80",
                "input-height": "40",
                "input-padding": "12",
                "input-border-radius": "8",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#581c87",
                "footer-text-color": "#e9d5ff",
                "footer-link-color": "#ffffff",
                "footer-link-hover-color": "#fbcfe8",
                "copyright-background-color": "#2a0e44",
                "copyright-text-color": "#d8b4fe",
                "copyright-link-color": "#e9d5ff",
                "copyright-border-top-color": "#581c87",
                "social-icon-color": "#d8b4fe",
                "social-icon-hover-color": "#fbcfe8",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "16",
                "spacing-lg": "24",
                "spacing-xl": "32",
                "spacing-xxl": "48",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            "solaris" : {
                "primary-color": "#FFB300",
                "primary-light-color": "#FFD54F",
                "primary-dark-color": "#FF8F00",
                "secondary-color": "#546E7A",
                "secondary-light-color": "#78909C",
                "secondary-dark-color": "#37474F",
                "accent-color": "#FFB300",
                "success-color": "#4DB6AC",
                "info-color": "#4FC3F7",
                "warning-color": "#FFB74D",
                "danger-color": "#E57373",
                "body-bg-color": "#263238",
                "content-bg-color": "#37474F",
                "background-primary-color": "#37474F",
                "background-secondary-color": "#263238",
                "background-light-color": "#455A64",
                "background-dark-color": "#102027",
                "text-primary-color": "#ECEFF1",
                "body-text-color": "#ECEFF1",
                "text-secondary-color": "#B0BEC5",
                "text-light-color": "#FFFFFF",
                "text-dark-color": "#ECEFF1",
                "heading-color": "#FFFFFF",
                "link-color": "#FFB300",
                "link-hover-color": "#FFD54F",
                "text-muted-color": "#90A4AE",
                "border-color": "#546E7A",
                "border-light-color": "#78909C",
                "border-dark-color": "#263238",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "4",
                "card-border-radius": "4",
                "input-border-radius-1": "4",
                "btn-border-radius": "4",
                "top-contact-and-social-bg-color": "#263238",
                "top-contact-and-social-link-color": "#B0BEC5",
                "top-contact-and-social-link-hover-color": "#FFB300",
                "top-contact-and-social-icon-color": "#B0BEC5",
                "top-contact-and-social-icon-hover-color": "#FFB300",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#263238",
                "top-contact-and-social-link-color-mobile": "#B0BEC5",
                "top-contact-and-social-link-hover-color-mobile": "#FFB300",
                "top-contact-and-social-icon-color-mobile": "#B0BEC5",
                "top-contact-and-social-icon-hover-color-mobile": "#FFB300",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#ECEFF1",
                "shop-menu-container-icon-color-member": "#ECEFF1",
                "shop-menu-container-icon-color-favorites": "#ECEFF1",
                "shop-menu-container-icon-color-basket": "#ECEFF1",
                "shop-menu-container-icon-hover-color": "#FFB300",
                "action-icon-basket-counter-bg-color": "#E57373",
                "shop-menu-container-mobile-icon-color-search": "#ECEFF1",
                "shop-menu-container-mobile-icon-color-member": "#ECEFF1",
                "shop-menu-container-mobile-icon-color-favorites": "#ECEFF1",
                "shop-menu-container-mobile-icon-color-basket": "#ECEFF1",
                "mobile-action-icon-phone-bg-color": "#4DB6AC",
                "mobile-action-icon-whatsapp-bg-color": "#25D366",
                "mobile-action-icon-basket-bg-color": "#FFB300",
                "mobile-action-icon-basket-counter-bg-color": "#E57373",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "180",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "140",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#37474F",
                "header-min-height": "72",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#455A64",
                "header-mobile-bg-color": "#37474F",
                "header-mobile-min-height": "72",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#455A64",
                "menu-background-color": "#37474F",
                "menu-text-color": "#B0BEC5",
                "menu-hover-color": "#FFFFFF",
                "menu-hover-bg-color": "#455A64",
                "menu-active-color": "#FFB300",
                "menu-active-bg-color": "rgba(255, 179, 0, 0.05)",
                "mobile-menu-background-color": "#37474F",
                "mobile-menu-text-color": "#ECEFF1",
                "hamburger-icon-color": "#ECEFF1",
                "mobile-menu-hover-color": "#FFB300",
                "mobile-menu-hover-bg-color": "#455A64",
                "mobile-menu-divider-color": "#546E7A",
                "submenu-bg-color": "#455A64",
                "submenu-text-color": "#B0BEC5",
                "submenu-hover-color": "#263238",
                "submenu-hover-bg-color": "#FFB300",
                "submenu-border-color": "#546E7A",
                "submenu-width": "220",
                "menu-font-size": "15",
                "mobile-menu-font-size": "15",
                "menu-height": "60",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#455A64",
                "product-box-border-color": "#546E7A",
                "product-box-hover-border-color": "#FFB300",
                "product-title-color": "#ECEFF1",
                "product-price-color": "#212529",
                "product-sale-price-color": "#dc3545",
                "product-old-price-color": "#6c757d",
                "product-discount-badge-color": "#28a745",
                "add-to-cart-bg-color": "#FFB300",
                "add-to-cart-text-color": "#FFFFFF",
                "add-to-cart-hover-bg-color": "#FF8F00",
                "product-box-padding": "15",
                "product-box-border-radius": "4",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#263238",
                "input-border-color": "#546E7A",
                "input-focus-border-color": "#FFB300",
                "input-text-color": "#ECEFF1",
                "input-placeholder-color": "#90A4AE",
                "btn-primary-bg-color": "#FFB300",
                "btn-primary-text-color": "#263238",
                "btn-primary-hover-bg-color": "#FFD54F",
                "btn-primary-border-color": "#FFB300",
                "btn-secondary-bg-color": "#546E7A",
                "btn-secondary-text-color": "#ECEFF1",
                "btn-secondary-hover-bg-color": "#78909C",
                "btn-outline-color": "#FFB300",
                "form-label-color": "#B0BEC5",
                "form-required-color": "#E57373",
                "form-error-color": "#E57373",
                "form-success-color": "#4DB6AC",
                "input-height": "42",
                "input-padding": "12",
                "input-border-radius": "4",
                "btn-padding-y": "10",
                "btn-padding-x": "20",
                "footer-background-color": "#343A40",
                "footer-text-color": "#ADB5BD",
                "footer-link-color": "#FFFFFF",
                "footer-link-hover-color": "#007BFF",
                "copyright-background-color": "#212529",
                "copyright-text-color": "#6c757d",
                "copyright-link-color": "#ADB5BD",
                "copyright-border-top-color": "#495057",
                "social-icon-color": "#ADB5BD",
                "social-icon-hover-color": "#FFFFFF",
                "social-icon-size": "22",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1140",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.5",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "4",
                "spacing-sm": "8",
                "spacing-md": "15",
                "spacing-lg": "20",
                "spacing-xl": "30",
                "spacing-xxl": "40",
                "font-size-xs": "10",
                "font-size-small": "12",
                "font-size-normal": "14",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "23%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            },
            "midnight-luxury": {
                "primary-color": "#D4AF37",
                "primary-light-color": "#FFD700",
                "primary-dark-color": "#B8860B",
                "secondary-color": "#2D2D2D",
                "secondary-light-color": "#404040",
                "secondary-dark-color": "#1A1A1A",
                "accent-color": "#D4AF37",
                "success-color": "#4CAF50",
                "info-color": "#2196F3",
                "warning-color": "#FF9800",
                "danger-color": "#F44336",
                "body-bg-color": "#0D0D0D",
                "content-bg-color": "#1A1A1A",
                "background-primary-color": "#1A1A1A",
                "background-secondary-color": "#0D0D0D",
                "background-light-color": "#2D2D2D",
                "background-dark-color": "#000000",
                "text-primary-color": "#F5F5F5",
                "body-text-color": "#F5F5F5",
                "text-secondary-color": "#CCCCCC",
                "text-light-color": "#FFFFFF",
                "text-dark-color": "#F5F5F5",
                "heading-color": "#D4AF37",
                "link-color": "#D4AF37",
                "link-hover-color": "#FFD700",
                "text-muted-color": "#999999",
                "border-color": "#404040",
                "border-light-color": "#555555",
                "border-dark-color": "#2D2D2D",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "2",
                "card-border-radius": "2",
                "input-border-radius-1": "2",
                "btn-border-radius": "2",
                "top-contact-and-social-bg-color": "#0D0D0D",
                "top-contact-and-social-link-color": "#CCCCCC",
                "top-contact-and-social-link-hover-color": "#D4AF37",
                "top-contact-and-social-icon-color": "#CCCCCC",
                "top-contact-and-social-icon-hover-color": "#D4AF37",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#0D0D0D",
                "top-contact-and-social-link-color-mobile": "#CCCCCC",
                "top-contact-and-social-link-hover-color-mobile": "#D4AF37",
                "top-contact-and-social-icon-color-mobile": "#CCCCCC",
                "top-contact-and-social-icon-hover-color-mobile": "#D4AF37",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#F5F5F5",
                "shop-menu-container-icon-color-member": "#F5F5F5",
                "shop-menu-container-icon-color-favorites": "#F5F5F5",
                "shop-menu-container-icon-color-basket": "#F5F5F5",
                "shop-menu-container-icon-hover-color": "#D4AF37",
                "action-icon-basket-counter-bg-color": "#8B4513",
                "shop-menu-container-mobile-icon-color-search": "#F5F5F5",
                "shop-menu-container-mobile-icon-color-member": "#F5F5F5",
                "shop-menu-container-mobile-icon-color-favorites": "#F5F5F5",
                "shop-menu-container-mobile-icon-color-basket": "#F5F5F5",
                "mobile-action-icon-phone-bg-color": "#4CAF50",
                "mobile-action-icon-whatsapp-bg-color": "#25D366",
                "mobile-action-icon-basket-bg-color": "#D4AF37",
                "mobile-action-icon-basket-counter-bg-color": "#8B4513",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "220",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "160",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#1A1A1A",
                "header-min-height": "90",
                "header-padding": "20",
                "header-border-width": "1",
                "header-border-color": "#404040",
                "header-mobile-bg-color": "#1A1A1A",
                "header-mobile-min-height": "80",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#404040",
                "menu-background-color": "#1A1A1A",
                "menu-text-color": "#CCCCCC",
                "menu-hover-color": "#D4AF37",
                "menu-hover-bg-color": "#2D2D2D",
                "menu-active-color": "#FFD700",
                "menu-active-bg-color": "rgba(212, 175, 55, 0.2)",
                "mobile-menu-background-color": "#1A1A1A",
                "mobile-menu-text-color": "#F5F5F5",
                "hamburger-icon-color": "#F5F5F5",
                "mobile-menu-hover-color": "#D4AF37",
                "mobile-menu-hover-bg-color": "#2D2D2D",
                "mobile-menu-divider-color": "#404040",
                "submenu-bg-color": "#2D2D2D",
                "submenu-text-color": "#CCCCCC",
                "submenu-hover-color": "#FFD700",
                "submenu-hover-bg-color": "#D4AF37",
                "submenu-border-color": "#404040",
                "submenu-width": "240",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "70",
                "submenu-font-size": "15",
                "menu-padding": "20",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#2D2D2D",
                "product-box-border-color": "#404040",
                "product-box-hover-border-color": "#D4AF37",
                "product-title-color": "#F5F5F5",
                "product-price-color": "#D4AF37",
                "product-sale-price-color": "#8B4513",
                "product-old-price-color": "#999999",
                "product-discount-badge-color": "#D4AF37",
                "add-to-cart-bg-color": "#D4AF37",
                "add-to-cart-text-color": "#000000",
                "add-to-cart-hover-bg-color": "#FFD700",
                "product-box-padding": "20",
                "product-box-border-radius": "2",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#2D2D2D",
                "input-border-color": "#404040",
                "input-focus-border-color": "#D4AF37",
                "input-text-color": "#F5F5F5",
                "input-placeholder-color": "#999999",
                "btn-primary-bg-color": "#D4AF37",
                "btn-primary-text-color": "#000000",
                "btn-primary-hover-bg-color": "#FFD700",
                "btn-primary-border-color": "#D4AF37",
                "btn-secondary-bg-color": "#404040",
                "btn-secondary-text-color": "#F5F5F5",
                "btn-secondary-hover-bg-color": "#555555",
                "btn-outline-color": "#D4AF37",
                "form-label-color": "#F5F5F5",
                "form-required-color": "#8B4513",
                "form-error-color": "#F44336",
                "form-success-color": "#4CAF50",
                "input-height": "45",
                "input-padding": "15",
                "input-border-radius": "2",
                "btn-padding-y": "12",
                "btn-padding-x": "30",
                "footer-background-color": "#000000",
                "footer-text-color": "#CCCCCC",
                "footer-link-color": "#D4AF37",
                "footer-link-hover-color": "#FFD700",
                "copyright-background-color": "#0D0D0D",
                "copyright-text-color": "#999999",
                "copyright-link-color": "#CCCCCC",
                "copyright-border-top-color": "#404040",
                "social-icon-color": "#CCCCCC",
                "social-icon-hover-color": "#D4AF37",
                "social-icon-size": "24",
                "footer-padding-y": "50",
                "footer-font-size": "14",
                "copyright-padding": "25",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1400",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "28",
                "mobile-line-height": "1.6",
                "mobile-section-margin": "25",
                "mobile-card-margin": "15",
                "mobile-button-height": "50",
                "enable-touch-swipe": "on",
                "touch-target-size": "48",
                "spacing-xs": "5",
                "spacing-sm": "10",
                "spacing-md": "20",
                "spacing-lg": "35",
                "spacing-xl": "50",
                "spacing-xxl": "70",
                "font-size-xs": "12",
                "font-size-small": "14",
                "font-size-normal": "16",
                "font-size-large": "20",
                "font-size-xlarge": "24",
                "font-size-xxlarge": "32",
                "homepage-product-box-width": "20%",
                "category-product-box-width": "25%",
                "search-product-box-width": "25%",
                "page-product-box-width": "25%"
            },
            "nature-fresh": {
                "primary-color": "#2E7D32",
                "primary-light-color": "#66BB6A",
                "primary-dark-color": "#1B5E20",
                "secondary-color": "#8BC34A",
                "secondary-light-color": "#A5D6A7",
                "secondary-dark-color": "#558B2F",
                "accent-color": "#4CAF50",
                "success-color": "#8BC34A",
                "info-color": "#00BCD4",
                "warning-color": "#FFC107",
                "danger-color": "#FF5722",
                "body-bg-color": "#F1F8E9",
                "content-bg-color": "#FFFFFF",
                "background-primary-color": "#FFFFFF",
                "background-secondary-color": "#F1F8E9",
                "background-light-color": "#F9FBE7",
                "background-dark-color": "#E8F5E8",
                "text-primary-color": "#1B5E20",
                "body-text-color": "#2E7D32",
                "text-secondary-color": "#4E7C4A",
                "text-light-color": "#FFFFFF",
                "text-dark-color": "#1B5E20",
                "heading-color": "#1B5E20",
                "link-color": "#2E7D32",
                "link-hover-color": "#66BB6A",
                "text-muted-color": "#81C784",
                "border-color": "#C8E6C9",
                "border-light-color": "#E8F5E8",
                "border-dark-color": "#A5D6A7",
                "border-style": "solid",
                "border-width": "1",
                "border-radius-base": "8",
                "card-border-radius": "12",
                "input-border-radius-1": "8",
                "btn-border-radius": "25",
                "top-contact-and-social-bg-color": "#F1F8E9",
                "top-contact-and-social-link-color": "#4E7C4A",
                "top-contact-and-social-link-hover-color": "#2E7D32",
                "top-contact-and-social-icon-color": "#4E7C4A",
                "top-contact-and-social-icon-hover-color": "#2E7D32",
                "top-contact-and-social-container-margin-top": "0",
                "top-contact-and-social-bg-color-mobile": "#F1F8E9",
                "top-contact-and-social-link-color-mobile": "#4E7C4A",
                "top-contact-and-social-link-hover-color-mobile": "#2E7D32",
                "top-contact-and-social-icon-color-mobile": "#4E7C4A",
                "top-contact-and-social-icon-hover-color-mobile": "#2E7D32",
                "top-contact-and-social-container-mobile-margin-top": "0",
                "shop-menu-container-icon-color-search": "#2E7D32",
                "shop-menu-container-icon-color-member": "#2E7D32",
                "shop-menu-container-icon-color-favorites": "#2E7D32",
                "shop-menu-container-icon-color-basket": "#2E7D32",
                "shop-menu-container-icon-hover-color": "#66BB6A",
                "action-icon-basket-counter-bg-color": "#FF5722",
                "shop-menu-container-mobile-icon-color-search": "#2E7D32",
                "shop-menu-container-mobile-icon-color-member": "#2E7D32",
                "shop-menu-container-mobile-icon-color-favorites": "#2E7D32",
                "shop-menu-container-mobile-icon-color-basket": "#2E7D32",
                "mobile-action-icon-phone-bg-color": "#8BC34A",
                "mobile-action-icon-whatsapp-bg-color": "#25D366",
                "mobile-action-icon-basket-bg-color": "#4CAF50",
                "mobile-action-icon-basket-counter-bg-color": "#FF5722",
                "mobile-action-icon-size": "32",
                "mobile-action-icon-gap": "12",
                "header-logo-width": "200",
                "header-logo-margin-top": "0",
                "header-logo-margin-right": "0",
                "header-logo-margin-bottom": "0",
                "header-logo-margin-left": "0",
                "header-logo-mobile-width": "150",
                "header-mobile-logo-margin-top": "0",
                "header-mobile-logo-margin-right": "0",
                "header-mobile-logo-margin-bottom": "0",
                "header-mobile-logo-margin-left": "0",
                "header-bg-color": "#FFFFFF",
                "header-min-height": "80",
                "header-padding": "15",
                "header-border-width": "1",
                "header-border-color": "#E8F5E8",
                "header-mobile-bg-color": "#FFFFFF",
                "header-mobile-min-height": "75",
                "header-mobile-padding": "15",
                "header-mobile-border-width": "1",
                "header-mobile-border-color": "#E8F5E8",
                "menu-background-color": "#FFFFFF",
                "menu-text-color": "#2E7D32",
                "menu-hover-color": "#66BB6A",
                "menu-hover-bg-color": "#F1F8E9",
                "menu-active-color": "#1B5E20",
                "menu-active-bg-color": "rgba(76, 175, 80, 0.1)",
                "mobile-menu-background-color": "#FFFFFF",
                "mobile-menu-text-color": "#2E7D32",
                "hamburger-icon-color": "#2E7D32",
                "mobile-menu-hover-color": "#66BB6A",
                "mobile-menu-hover-bg-color": "#F1F8E9",
                "mobile-menu-divider-color": "#C8E6C9",
                "submenu-bg-color": "#FFFFFF",
                "submenu-text-color": "#2E7D32",
                "submenu-hover-color": "#FFFFFF",
                "submenu-hover-bg-color": "#4CAF50",
                "submenu-border-color": "#C8E6C9",
                "submenu-width": "220",
                "menu-font-size": "16",
                "mobile-menu-font-size": "16",
                "menu-height": "65",
                "submenu-font-size": "14",
                "menu-padding": "15",
                "mobile-menu-padding": "15",
                "product-box-background-color": "#FFFFFF",
                "product-box-border-color": "#C8E6C9",
                "product-box-hover-border-color": "#A5D6A7",
                "product-title-color": "#1B5E20",
                "product-price-color": "#2E7D32",
                "product-sale-price-color": "#FF5722",
                "product-old-price-color": "#81C784",
                "product-discount-badge-color": "#8BC34A",
                "add-to-cart-bg-color": "#4CAF50",
                "add-to-cart-text-color": "#FFFFFF",
                "add-to-cart-hover-bg-color": "#66BB6A",
                "product-box-padding": "15",
                "product-box-border-radius": "12",
                "product-image-aspect-ratio": "1/1",
                "input-bg-color": "#F9FBE7",
                "input-border-color": "#C8E6C9",
                "input-focus-border-color": "#4CAF50",
                "input-text-color": "#1B5E20",
                "input-placeholder-color": "#81C784",
                "btn-primary-bg-color": "#4CAF50",
                "btn-primary-text-color": "#FFFFFF",
                "btn-primary-hover-bg-color": "#66BB6A",
                "btn-primary-border-color": "#4CAF50",
                "btn-secondary-bg-color": "#F1F8E9",
                "btn-secondary-text-color": "#2E7D32",
                "btn-secondary-hover-bg-color": "#E8F5E8",
                "btn-outline-color": "#2E7D32",
                "form-label-color": "#1B5E20",
                "form-required-color": "#FF5722",
                "form-error-color": "#FF5722",
                "form-success-color": "#8BC34A",
                "input-height": "42",
                "input-padding": "12",
                "input-border-radius": "8",
                "btn-padding-y": "12",
                "btn-padding-x": "25",
                "footer-background-color": "#1B5E20",
                "footer-text-color": "#A5D6A7",
                "footer-link-color": "#FFFFFF",
                "footer-link-hover-color": "#8BC34A",
                "copyright-background-color": "#0F3E14",
                "copyright-text-color": "#81C784",
                "copyright-link-color": "#A5D6A7",
                "copyright-border-top-color": "#2E7D32",
                "social-icon-color": "#A5D6A7",
                "social-icon-hover-color": "#8BC34A",
                "social-icon-size": "24",
                "footer-padding-y": "40",
                "footer-font-size": "14",
                "copyright-padding": "20",
                "mobile-breakpoint": "768",
                "tablet-breakpoint": "992",
                "desktop-breakpoint": "1200",
                "mobile-container-padding": "15",
                "tablet-container-padding": "20",
                "desktop-max-width": "1200",
                "mobile-base-font-size": "14",
                "mobile-h1-font-size": "24",
                "mobile-line-height": "1.6",
                "mobile-section-margin": "20",
                "mobile-card-margin": "10",
                "mobile-button-height": "44",
                "enable-touch-swipe": "on",
                "touch-target-size": "44",
                "spacing-xs": "6",
                "spacing-sm": "12",
                "spacing-md": "18",
                "spacing-lg": "28",
                "spacing-xl": "36",
                "spacing-xxl": "50",
                "font-size-xs": "11",
                "font-size-small": "13",
                "font-size-normal": "15",
                "font-size-large": "18",
                "font-size-xlarge": "22",
                "font-size-xxlarge": "26",
                "homepage-product-box-width": "18%",
                "category-product-box-width": "23%",
                "search-product-box-width": "23%",
                "page-product-box-width": "23%"
            }
        };

        const theme = colorThemes[themeName];
        if (!theme) {
            console.error('❌ Tema bulunamadı:', themeName);
            return;
        }

        console.log("Tema: " + theme);

        // Form alanlarını güncelle
        Object.keys(theme).forEach(fieldName => {
            const $input = $(`input[name="${fieldName}"], select[name="${fieldName}"]`);
            if ($input.length) {
                const oldValue = $input.val();
                $input.val(theme[fieldName]);
                
                console.log(`🎨 ${fieldName}: ${oldValue} → ${theme[fieldName]}`);
                
                // Change event'ini tetikle
                $input.trigger('change');
            }
        });

        // CSS değişkenlerini anında güncelle (önizleme için)
        this.updateCSSVariables(theme);
        
        // Tüm sekmelerdeki önizlemeleri güncelle
        this.updateAllPreviews(theme);
        
        // Başarı mesajı göster
        this.showNotification(`${themeName.charAt(0).toUpperCase() + themeName.slice(1)} teması uygulandı!`, 'success');
        
        // Temayı kaydet
        this.saveTheme();

        console.log('✅ Renk teması uygulandı, tüm sekmeler güncellendi ve kaydedildi');
    }
    
    // Tüm sekmelerdeki önizlemeleri güncelle
    updateAllPreviews(formData) {
        console.log('🔄 Tüm sekmeler güncelleniyor...');
        
        // Mevcut formData'yı al (eğer parametre olarak verilmediyse)
        if (!formData) {
            formData = this.getFormData();
        }
        
        // Tüm preview fonksiyonlarını çağır
        this.updateHeaderPreview(formData);
        this.updateMenuPreview(formData);
        this.updateMobileMenuPreview(formData);
        if (typeof this.updateProductPreview === 'function') {
            this.updateProductPreview(formData);
        }
        if (typeof this.updateFormPreview === 'function') {
            this.updateFormPreview(formData);
        }
        if (typeof this.updateFooterPreview === 'function') {
            this.updateFooterPreview(formData);
        }
        if (typeof this.updateColorPreview === 'function') {
            this.updateColorPreview(formData);
        }
        console.log('✅ Tüm sekmeler güncellendi');
    }
    
    // CSS değişkenlerini güncelle
    updateCSSVariables(variables) {
        const style = document.createElement('style');
        style.id = 'theme-preview-vars';
        
        // Eski style'ı kaldır
        const oldStyle = document.getElementById('theme-preview-vars');
        if (oldStyle) {
            oldStyle.remove();
        }
        
        // Yeni CSS değişkenlerini oluştur
        let cssText = ':root {\n';
        Object.keys(variables).forEach(key => {
            cssText += `  --${key}: ${variables[key]};\n`;
        });
        cssText += '}';
        
        style.textContent = cssText;
        document.head.appendChild(style);
        
        console.log('✅ CSS değişkenleri güncellendi');
    }
    
    showNotification(message, type = 'info', duration = 4000) {
        const notification = $(`
            <div class="theme-notification ${type}" style="
                position: fixed; 
                top: 20px; 
                right: 20px; 
                z-index: 9999; 
                min-width: 300px;
                padding: 15px;
                border-radius: 6px;
                color: white;
                font-weight: 500;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
                background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            ">
                <div class="notification-content">
                    <span class="notification-icon">
                        ${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}
                    </span>
                    <span class="notification-message">${message}</span>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.css({
                opacity: '1',
                transform: 'translateX(0)'
            });
        }, 100);
        
        setTimeout(() => {
            notification.css({
                opacity: '0',
                transform: 'translateX(100%)'
            });
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    showLoader(message = 'İşlem yapılıyor...') {
        if ($('.theme-loader').length > 0) return;
        
        const loader = $(`
            <div class="theme-loader" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.3s ease;
            ">
                <div style="
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    text-align: center;
                    min-width: 200px;
                ">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="loader-text">${message}</div>
                </div>
            </div>
        `);
        
        $('body').append(loader);
        setTimeout(() => loader.css('opacity', '1'), 100);
    }

    hideLoader() {
        const loader = $('.theme-loader');
        loader.css('opacity', '0');
        setTimeout(() => loader.remove(), 300);
    }

    // Form verilerini topla
    getFormData() {
        const formData = {};
        
        // Theme formu içindeki tüm input, select ve textarea'ları topla
        $('#themeForm input, #themeForm select, #themeForm textarea').each(function() {
            const $input = $(this);
            const name = $input.attr('name');
            
            if (name) {
                if ($input.attr('type') === 'checkbox' || $input.attr('type') === 'radio') {
                    if ($input.is(':checked')) {
                        formData[name] = $input.val();
                    }
                } else {
                    formData[name] = $input.val();
                }
            }
        });
        
        console.log('📋 Form verileri toplandı:', Object.keys(formData).length, 'alan');
        return formData;
    }
}

// Global değişkenler
let themeEditorInstance;

// Sayfa yüklendiğinde tema editörünü başlat
$(document).ready(function() {
    // ThemeEditor sınıfını başlat
    window.themeEditor = new ThemeEditor();
    window.themeEditorInstance = window.themeEditor;
    themeEditorInstance = window.themeEditor;
    
    console.log('✅ ThemeEditor Core yüklendi:', window.themeEditorInstance);
    
    // Global fonksiyonları bağla
    window.saveTheme = function() { return window.themeEditor.saveTheme(); };
    window.previewTheme = function() { return window.themeEditor.previewTheme(); };
    window.resetTheme = function() { return window.themeEditor.resetTheme(); };
});

// Global hızlı aksiyon fonksiyonları
function quickSave() {
    if (themeEditorInstance) {
        themeEditorInstance.saveTheme();
    }
}

function quickPreview() {
    if (themeEditorInstance) {
        themeEditorInstance.previewTheme();
    }
}

function quickReset() {
    if (themeEditorInstance && confirm('Tüm değişiklikler sıfırlanacak. Emin misiniz?')) {
        themeEditorInstance.resetTheme();
    }
}

// Global renk tema fonksiyonu
function applyColorTheme(themeName) {
    if (themeEditorInstance) {
        themeEditorInstance.applyColorTheme(themeName);
    } else {
        console.error('❌ ThemeEditor instance bulunamadı!');
    }
}
