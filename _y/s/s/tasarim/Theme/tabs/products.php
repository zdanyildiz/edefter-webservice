<?php
/**
 * Products Tab Content - Theme Editor
 * Ürün kutuları ve liste ayarları için sekme içeriği
 */
?>

<!-- Products Panel -->
<div class="tab-pane fade" id="products-panel" role="tabpanel">
    <h5 class="mb-3">
        <i class="fas fa-shopping-bag me-2"></i>Ürün Kutuları
    </h5>
    
    <div class="row">
        <!-- Ürün Kutusu Renkleri -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Ürün Kutusu Renkleri</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kutu Arkaplan Rengi</label>
                        <input type="color" name="product-box-background-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-box-background-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kutu Border Rengi</label>
                        <input type="color" name="product-box-border-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-box-border-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hover Border Rengi</label>
                        <input type="color" name="product-box-hover-border-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-box-hover-border-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ürün Adı Rengi</label>
                        <input type="color" name="product-title-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-title-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Fiyat Renkleri -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Fiyat Renkleri</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Normal Fiyat Rengi</label>
                        <input type="color" name="product-price-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-price-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İndirimli Fiyat Rengi</label>
                        <input type="color" name="product-sale-price-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-sale-price-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Eski Fiyat Rengi</label>
                        <input type="color" name="product-old-price-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-old-price-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İndirim Etiketi Rengi</label>
                        <input type="color" name="product-discount-badge-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['product-discount-badge-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Buton Renkleri -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Sepete Ekle Butonu</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Buton Arkaplan Rengi</label>
                        <input type="color" name="add-to-cart-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['add-to-cart-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Buton Metin Rengi</label>
                        <input type="color" name="add-to-cart-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['add-to-cart-text-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hover Arkaplan Rengi</label>
                        <input type="color" name="add-to-cart-hover-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['add-to-cart-hover-bg-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ürün Kutusu Boyutları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Boyutlar ve Spacing</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kutu Padding (px)</label>
                        <input type="number" name="product-box-padding" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['product-box-padding'] ) ?>" min="5" max="30">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Border Radius (px)</label>
                        <input type="number" name="product-box-border-radius" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['product-box-border-radius'] ) ?>" min="0" max="20">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Resim Aspect Ratio</label>
                        <select name="product-image-aspect-ratio" class="form-select">
                            <option value="1/1" <?= ($customCSS['product-image-aspect-ratio'] ) === '1/1' ? 'selected' : '' ?>>1/1 (Kare)</option>
                            <option value="4/3" <?= ($customCSS['product-image-aspect-ratio'] ) === '4/3' ? 'selected' : '' ?>>4/3</option>
                            <option value="16/9" <?= ($customCSS['product-image-aspect-ratio'] ) === '16/9' ? 'selected' : '' ?>>16/9</option>
                            <option value="3/4" <?= ($customCSS['product-image-aspect-ratio'] ) === '3/4' ? 'selected' : '' ?>>3/4</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Ürün Kutusu Önizleme -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Ürün Kutusu Önizleme</h6>
                </div>
                <div class="card-body">
                    <div id="product-preview" class="product-preview-container">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="product-box">
                                    <div class="product-image">
                                        <img src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%23e9ecef'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='16' fill='%23495057'%3EÜrün 1%3C/text%3E%3C/svg%3E" alt="Ürün 1" class="img-fluid">
                                        <span class="discount-badge">%20</span>
                                    </div>
                                    <div class="product-info">
                                        <h6 class="product-title">Örnek Ürün 1</h6>
                                        <div class="product-price">
                                            <span class="old-price">₺100,00</span>
                                            <span class="current-price">₺80,00</span>
                                        </div>
                                        <button class="btn-add-to-cart">Sepete Ekle</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="product-box">
                                    <div class="product-image">
                                        <img src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%23e9ecef'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='16' fill='%23495057'%3EÜrün 2%3C/text%3E%3C/svg%3E" alt="Ürün 2" class="img-fluid">
                                    </div>
                                    <div class="product-info">
                                        <h6 class="product-title">Örnek Ürün 2</h6>
                                        <div class="product-price">
                                            <span class="current-price">₺120,00</span>
                                        </div>
                                        <button class="btn-add-to-cart">Sepete Ekle</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-preview-container .product-box {
    background: var(--product-box-background-color, #ffffff);
    border: 1px solid var(--product-box-border-color, #e9ecef);
    border-radius: var(--product-box-border-radius, 8px);
    padding: var(--product-box-padding, 15px);
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.product-preview-container .product-box:hover {
    border-color: var(--product-box-hover-border-color, #4285f4);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.product-preview-container .product-image {
    position: relative;
    margin-bottom: 15px;
}

.product-preview-container .product-image img {
    width: 100%;
    height: auto; /* Let aspect-ratio control the height */
    object-fit: cover;
    border-radius: 4px;
}

.product-preview-container .discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--product-discount-badge-color, #dc3545);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.product-preview-container .product-title {
    color: var(--product-title-color, #212529);
    font-size: 16px;
    margin-bottom: 10px;
    font-weight: 500;
}

.product-preview-container .product-price {
    margin-bottom: 15px;
}

.product-preview-container .old-price {
    color: var(--product-old-price-color, #6c757d);
    text-decoration: line-through;
    margin-right: 10px;
    font-size: 14px;
}

.product-preview-container .current-price {
    color: var(--product-price-color, #212529);
    font-weight: bold;
    font-size: 18px;
}

.product-preview-container .old-price + .current-price {
    color: var(--product-sale-price-color, #dc3545);
}

.product-preview-container .btn-add-to-cart {
    background: var(--add-to-cart-bg-color, #4285f4);
    color: var(--add-to-cart-text-color, #ffffff);
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    width: 100%;
    transition: all 0.3s ease;
    font-weight: 500;
    cursor: pointer;
}

.product-preview-container .btn-add-to-cart:hover {
    background: var(--add-to-cart-hover-bg-color, #3367d6);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Aspect Ratio Support */
.product-preview-container .product-image[data-aspect="1:1"] img {
    aspect-ratio: 1 / 1;
}

.product-preview-container .product-image[data-aspect="4:3"] img {
    aspect-ratio: 4 / 3;
}

.product-preview-container .product-image[data-aspect="16:9"] img {
    aspect-ratio: 16 / 9;
}

/* Interactive Preview Features */
.product-preview-container .product-box {
    cursor: pointer;
}

.product-preview-container .product-box:hover .product-title {
    color: var(--product-title-color, #212529);
}

.product-preview-container .product-box:hover .discount-badge {
    transform: scale(1.05);
}

/* Responsive Grid */
@media (max-width: 768px) {
    .product-preview-container .row {
        gap: 15px;
    }

    .product-preview-container .col-md-3 {
        flex: 0 0 calc(50% - 7.5px);
        max-width: calc(50% - 7.5px);
    }
}

/* Loading State */
.product-preview-container .product-box.loading {
    opacity: 0.7;
    pointer-events: none;
}

.product-preview-container .product-box.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 2px solid var(--product-box-border-color, #e9ecef);
    border-top: 2px solid var(--add-to-cart-bg-color, #4285f4);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Ek Product Box Efektleri */
.product-preview-container .product-box.button-hover {
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.product-preview-container .product-box.clicked {
    transform: scale(0.98);
}

.product-preview-container.list-layout .product-box {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.product-preview-container.list-layout .product-image {
    flex: 0 0 120px;
    margin-right: 15px;
    margin-bottom: 0;
}

.product-preview-container.list-layout .product-info {
    flex: 1;
}

.product-preview_container.mobile-test {
    max-width: 375px;
    margin: 0 auto;
}

.product-preview_container.mobile-test .col-md-3 {
    flex: 0 0 50%;
    max-width: 50%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Products tab DOM loaded');

    function initializeProductFunctions() {
        if (window.themeEditorInstance) {
            //console.log('🛍️ ThemeEditor instance bulundu, ürün kutusu önizleme başlatılıyor...');

            // Ürün kutusu önizleme toggle sistem başlatma
            if (typeof window.themeEditorInstance.initProductPreviewToggle === 'function') {
                window.themeEditorInstance.initProductPreviewToggle();
            } else {
                console.error('❌ initProductPreviewToggle fonksiyonu ThemeEditor\'da bulunamadı!');
            }

            // Ürün kutusu interaksiyonları başlatma
            if (typeof window.themeEditorInstance.initProductInteractions === 'function') {
                window.themeEditorInstance.initProductInteractions();
            } else {
                console.error('❌ initProductInteractions fonksiyonu ThemeEditor\'da bulunamadı!');
            }

            // İlk yüklemede ürün kutusu önizlemesini güncelle
            const initialFormData = window.themeEditorInstance.getFormData();
            if (typeof window.themeEditorInstance.updateProductPreview === 'function') {
                window.themeEditorInstance.updateProductPreview(initialFormData);
            } else {
                console.error('❌ updateProductPreview fonksiyonu ThemeEditor\'da bulunamadı!');
            }
        } else {
            console.warn('⏳ ThemeEditor instance henüz hazır değil, 100ms sonra tekrar denenecek...');
            setTimeout(initializeProductFunctions, 100);
        }
    }

    // Aspect ratio değişikliği event listener
    $(document).on('change', 'select[name="product-image-aspect-ratio"]', function() {
        const aspectRatio = $(this).val();
        if (window.themeEditorInstance && typeof window.themeEditorInstance.updateImageAspectRatio === 'function') {
            window.themeEditorInstance.updateImageAspectRatio(aspectRatio);
        }
        //console.log('📐 Aspect ratio değiştirildi:', aspectRatio);
    });

    // Test butonları (geliştirme amaçlı)
    window.testProductLayout = function() {
        if (window.themeEditorInstance && typeof window.themeEditorInstance.toggleProductLayout === 'function') {
            window.themeEditorInstance.toggleProductLayout('list');
            setTimeout(() => {
                window.themeEditorInstance.toggleProductLayout('grid');
            }, 3000);
        }
    };

    window.testProductResponsive = function() {
        if (window.themeEditorInstance && typeof window.themeEditorInstance.testProductResponsive === 'function') {
            window.themeEditorInstance.testProductResponsive();
        }
    };

    initializeProductFunctions();
});
</script>
