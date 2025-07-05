<?php
/**
 * Banners Tab Content - Theme Editor
 * Banner ve içerik ayarları için sekme içeriği
 */
?>

<!-- Banners Panel -->
<div class="tab-pane fade" id="banners-panel" role="tabpanel">
    <h5 class="mb-3">
        <i class="fas fa-image me-2"></i>Banner & İçerik Ayarları
    </h5>
    
    <div class="row">
        <!-- Banner Ayarları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Banner Ayarları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Banner Container Arkaplan</label>
                        <input type="color" name="banner-container-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['banner-container-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Banner Overlay Rengi</label>
                        <input type="color" name="banner-overlay-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['banner-overlay-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Banner Overlay Opacity (%)</label>
                        <input type="range" name="banner-overlay-opacity" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['banner-overlay-opacity'] ) ?>" 
                               min="0" max="100" step="5">
                        <div class="form-text">Mevcut: <span id="overlay-opacity-value">30</span>%</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Banner Metin Rengi</label>
                        <input type="color" name="banner-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['banner-text-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- İçerik Alanı Ayarları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">İçerik Alanı</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">İçerik Arkaplan Rengi</label>
                        <input type="color" name="content-area-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['content-area-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İçerik Metin Rengi</label>
                        <input type="color" name="content-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['content-text-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Link Rengi</label>
                        <input type="color" name="content-link-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['content-link-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Link Hover Rengi</label>
                        <input type="color" name="content-link-hover-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['content-link-hover-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Slider Ayarları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Slider Ayarları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Slider Dot Rengi</label>
                        <input type="color" name="slider-dot-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['slider-dot-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Aktif Dot Rengi</label>
                        <input type="color" name="slider-dot-active-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['slider-dot-active-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Navigation Arrow Rengi</label>
                        <input type="color" name="slider-arrow-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['slider-arrow-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Navigation Arrow Hover</label>
                        <input type="color" name="slider-arrow-hover-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['slider-arrow-hover-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Ayarları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Card & Panel Ayarları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Card Arkaplan Rengi</label>
                        <input type="color" name="card-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['card-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Card Border Rengi</label>
                        <input type="color" name="card-border-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['card-border-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Card Shadow Rengi</label>
                        <input type="color" name="card-shadow-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['card-shadow-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Card Shadow Opacity (%)</label>
                        <input type="range" name="card-shadow-opacity" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['card-shadow-opacity'] ) ?>" 
                               min="0" max="50" step="5">
                        <div class="form-text">Mevcut: <span id="shadow-opacity-value">10</span>%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Banner Boyutları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Banner Boyutları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Banner Yüksekliği (px)</label>
                        <input type="number" name="banner-height" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['banner-height'] ) ?>" min="200" max="800">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Banner Padding (px)</label>
                        <input type="number" name="banner-padding" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['banner-padding'] ) ?>" min="0" max="50">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Banner Margin Bottom (px)</label>
                        <input type="number" name="banner-margin-bottom" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['banner-margin-bottom'] ) ?>" min="0" max="100">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Banner Önizleme -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Banner Önizleme</h6>
                </div>
                <div class="card-body p-0">
                    <div id="banner-preview" class="banner-preview-container">
                        <div class="banner-slide">
                            <div class="banner-overlay"></div>
                            <div class="banner-content">
                                <h3>Örnek Banner Başlığı</h3>
                                <p>Banner açıklama metni burada yer alacak.</p>
                                <button class="banner-cta-button">Daha Fazla</button>
                            </div>
                        </div>
                        
                        <div class="slider-controls">
                            <div class="slider-dots">
                                <span class="dot active"></span>
                                <span class="dot"></span>
                                <span class="dot"></span>
                            </div>
                            <div class="slider-arrows">
                                <button class="arrow-left"><i class="fas fa-chevron-left"></i></button>
                                <button class="arrow-right"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.banner-preview-container {
    position: relative;
    height: 250px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    overflow: hidden;
}

.banner-slide {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-size: cover;
    background-position: center;
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--banner-overlay-color, #000000);
    opacity: calc(var(--banner-overlay-opacity, 30) / 100);
}

.banner-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: var(--banner-text-color, #ffffff);
    padding: 20px;
}

.banner-content h3 {
    margin-bottom: 15px;
    font-size: 24px;
    font-weight: 600;
}

.banner-content p {
    margin-bottom: 20px;
    opacity: 0.9;
}

.banner-cta-button {
    background: var(--primary-color, #4285f4);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.banner-cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.slider-controls {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: 20px;
}

.slider-dots {
    display: flex;
    gap: 8px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--slider-dot-color, #ffffff);
    opacity: 0.5;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active {
    background: var(--slider-dot-active-color, #4285f4);
    opacity: 1;
}

.slider-arrows button {
    background: var(--slider-arrow-color, #ffffff);
    color: var(--banner-overlay-color, #000000);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider-arrows button:hover {
    background: var(--slider-arrow-hover-color, #4285f4);
    color: white;
}

/* Range slider styling */
input[type="range"] {
    width: 100%;
    margin: 10px 0;
}

input[type="range"]::-webkit-slider-thumb {
    background: var(--primary-color, #4285f4);
}

input[type="range"]::-moz-range-thumb {
    background: var(--primary-color, #4285f4);
}
</style>
