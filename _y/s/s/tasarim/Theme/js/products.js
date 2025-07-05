/**
 * Products Preview Functions
 * Ürün kutusu önizleme fonksiyonları
 */

// ThemeEditor sınıfına product fonksiyonları ekle
if (typeof ThemeEditor !== 'undefined') {
    
    // Ürün kutusu önizleme güncelleme fonksiyonu
    ThemeEditor.prototype.updateProductPreview = function(formData) {
        //console.log('🛍️ updateProductPreview called', formData);
        
        const root = document.documentElement;
        const keysToUpdate = [
            'product-box-background-color', 'product-box-border-color', 'product-box-hover-border-color',
            'product-box-border-radius', 'product-box-padding', 'product-title-color', 'product-price-color',
            'product-sale-price-color', 'product-old-price-color', 'product-discount-badge-color',
            'add-to-cart-bg-color', 'add-to-cart-text-color', 'add-to-cart-hover-bg-color'
        ];

        keysToUpdate.forEach(key => {
            if (formData[key] !== undefined) {
                let value = formData[key];
                // Add 'px' unit to numeric values that need it
                if (key === 'product-box-border-radius' || key === 'product-box-padding') {
                    if (String(value).trim() !== '' && !isNaN(parseFloat(value))) {
                        value = `${parseFloat(value)}px`;
                    }
                }
                root.style.setProperty(`--${key}`, value);
            }
        });

        // Aspect ratio değişikliğini uygula
        this.updateImageAspectRatio(formData['product-image-aspect-ratio']);
        
        //console.log('✅ Product preview güncellendi');
    };
    
    // Resim aspect ratio güncelleme
    ThemeEditor.prototype.updateImageAspectRatio = function(aspectRatio) {
        if (!aspectRatio) return;
        
        const $productImages = $('.product-preview-container .product-image');
        
        // Önce tüm aspect ratio class'larını kaldır
        $productImages.removeAttr('data-aspect');
        
        // Yeni aspect ratio'yu uygula
        $productImages.attr('data-aspect', aspectRatio);
        
        //console.log('📐 Aspect ratio güncellendi:', aspectRatio);
    };
    
    // Ürün kutularına interaktif efektler ekleme
    ThemeEditor.prototype.initProductInteractions = function() {
        //console.log('🛍️ Ürün kutusu interaksiyonları başlatılıyor...');
        
        // Sepete ekle buton hover efekti
        $(document).on('mouseenter', '.product-preview-container .btn-add-to-cart', function() {
            $(this).closest('.product-box').addClass('button-hover');
        });
        
        $(document).on('mouseleave', '.product-preview-container .btn-add-to-cart', function() {
            $(this).closest('.product-box').removeClass('button-hover');
        });
        
        // Ürün kutusu tıklama efekti (demo)
        $(document).on('click', '.product-preview-container .product-box', function(e) {
            if ($(e.target).hasClass('btn-add-to-cart')) return; // Buton tıklamasını engelle
            
            $(this).addClass('clicked');
            setTimeout(() => {
                $(this).removeClass('clicked');
            }, 300);
            
            //console.log('🎯 Ürün kutusu tıklandı (demo)');
        });
        
        // Sepete ekle buton tıklama efekti (demo)
        $(document).on('click', '.product-preview-container .btn-add-to-cart', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $btn = $(this);
            const originalText = $btn.text();
            
            // Loading efekti
            $btn.text('Ekleniyor...').prop('disabled', true);
            $btn.closest('.product-box').addClass('loading');
            
            setTimeout(() => {
                $btn.text('✓ Eklendi').removeClass('btn-primary').addClass('btn-success');
                $btn.closest('.product-box').removeClass('loading');
                
                setTimeout(() => {
                    $btn.text(originalText).prop('disabled', false).removeClass('btn-success').addClass('btn-primary');
                }, 1500);
            }, 1000);
            
            //console.log('🛒 Sepete ekle buton tıklandı (demo)');
        });
        
        //console.log('✅ Ürün kutusu interaksiyon event listeners eklendi');
    };
    
    // Ürün kutusu preview toggle (gelecekte kullanım için)
    ThemeEditor.prototype.initProductPreviewToggle = function() {
        //console.log('🛍️ Ürün kutusu preview toggle sistemi hazır');
        
        // Gelecekte ürün kutusu önizlemesini sabitleme özelliği eklenebilir
        // Header ve menu gibi toggle butonları burada tanımlanacak
    };
    
    // Ürün kutusu layout değiştirme (grid/list view)
    ThemeEditor.prototype.toggleProductLayout = function(layout = 'grid') {
        const $container = $('.product-preview-container');
        
        if (layout === 'list') {
            $container.addClass('list-layout').removeClass('grid-layout');
            //console.log('📋 Ürün kutuları liste görünümüne geçti');
        } else {
            $container.addClass('grid-layout').removeClass('list-layout');
            //console.log('⊞ Ürün kutuları grid görünümüne geçti');
        }
    };
    
    // Ürün kutusu responsive test
    ThemeEditor.prototype.testProductResponsive = function() {
        const $container = $('.product-preview-container');
        
        // Mobil görünüm testi
        $container.addClass('mobile-test');
        //console.log('📱 Mobil responsive test başlatıldı');
        
        setTimeout(() => {
            $container.removeClass('mobile-test');
            //console.log('📱 Mobil responsive test tamamlandı');
        }, 3000);
    };

} else {
    console.error('❌ product.js: ThemeEditor sınıfı bulunamadı! core.js yüklenmiş mi?');
}

//console.log('✅ Products.js yüklendi - Ürün kutusu önizleme sistemleri hazır');
