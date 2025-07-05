<?php
/**
 * Responsive Tab Content - Theme Editor
 * Responsive tasarım ayarları için sekme içeriği
 */
?>

<!-- Responsive Panel -->
<div class="tab-pane fade" id="responsive-panel" role="tabpanel">
    <h5 class="mb-3">
        <i class="fas fa-mobile-alt me-2"></i>Responsive Ayarları
    </h5>
    
    <div class="row">
        <!-- Breakpoint Ayarları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Breakpoint Ayarları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Mobil Breakpoint (px)</label>
                        <input type="number" name="mobile-breakpoint" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['mobile-breakpoint'] ) ?>" min="320" max="992">
                        <div class="form-text">Bu değerin altında mobil görünüm aktif olur</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tablet Breakpoint (px)</label>
                        <input type="number" name="tablet-breakpoint" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['tablet-breakpoint'] ) ?>" min="768" max="1200">
                        <div class="form-text">Tablet görünüm için breakpoint</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Desktop Breakpoint (px)</label>
                        <input type="number" name="desktop-breakpoint" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['desktop-breakpoint'] ) ?>" min="992" max="1400">
                        <div class="form-text">Desktop görünüm için breakpoint</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobil Container Ayarları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Container Ayarları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Mobil Container Padding (px)</label>
                        <input type="number" name="mobile-container-padding" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['mobile-container-padding'] ) ?>" min="10" max="30">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tablet Container Padding (px)</label>
                        <input type="number" name="tablet-container-padding" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['tablet-container-padding'] ) ?>" min="15" max="40">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Desktop Max Width (px)</label>
                        <input type="number" name="desktop-max-width" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['desktop-max-width'] ) ?>" min="1000" max="1400">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Mobil Typography -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Mobil Typography</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Mobil Base Font Size (px)</label>
                        <input type="number" name="mobile-base-font-size" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['mobile-base-font-size'] ) ?>" min="12" max="18">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mobil H1 Font Size (px)</label>
                        <input type="number" name="mobile-h1-font-size" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['mobile-h1-font-size'] ) ?>" min="20" max="36">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mobil Line Height</label>
                        <input type="number" name="mobile-line-height" class="form-control" step="0.1"
                               value="<?= $customCSS['mobile-line-height']  ?>" min="1.2" max="2.0">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobil Spacing -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Mobil Spacing</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Mobil Section Margin (px)</label>
                        <input type="number" name="mobile-section-margin" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['mobile-section-margin'] ) ?>" min="10" max="40">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mobil Card Margin (px)</label>
                        <input type="number" name="mobile-card-margin" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['mobile-card-margin'] ) ?>" min="5" max="20">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mobil Button Height (px)</label>
                        <input type="number" name="mobile-button-height" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['mobile-button-height'] ) ?>" min="36" max="60">
                        <div class="form-text">Touch-friendly için minimum 44px önerilir</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Responsive Gizleme -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Responsive Gizleme</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="hide-banner-mobile" 
                                   <?= ($customCSS['hide-banner-mobile'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label">Banner'ları mobilde gizle</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="hide-sidebar-mobile" 
                                   <?= ($customCSS['hide-sidebar-mobile'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label">Sidebar'ı mobilde gizle</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="hide-breadcrumb-mobile" 
                                   <?= ($customCSS['hide-breadcrumb-mobile'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label">Breadcrumb'ı mobilde gizle</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Touch Optimizasyonu -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Touch Optimizasyonu</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enable-touch-swipe" 
                                   <?= ($customCSS['enable-touch-swipe'] ?? true) ? 'checked' : '' ?>>
                            <label class="form-check-label">Touch swipe gestures</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enable-pinch-zoom" 
                                   <?= ($customCSS['enable-pinch-zoom'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label">Pinch-to-zoom desteği</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Touch Target Size (px)</label>
                        <input type="number" name="touch-target-size" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['touch-target-size'] ) ?>" min="36" max="60">
                        <div class="form-text">Minimum 44px önerilir (Apple HIG)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Responsive Önizleme -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Responsive Önizleme</h6>
                </div>
                <div class="card-body">
                    <div class="responsive-preview-tabs mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-device="desktop">
                            <i class="fas fa-desktop me-1"></i>Desktop
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-device="tablet">
                            <i class="fas fa-tablet-alt me-1"></i>Tablet
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-device="mobile">
                            <i class="fas fa-mobile-alt me-1"></i>Mobile
                        </button>
                    </div>
                    
                    <div id="responsive-preview" class="responsive-preview-container">
                        <div class="preview-frame desktop-frame active">
                            <div class="preview-content">
                                <h5>Desktop Görünüm (1200px+)</h5>
                                <p>Tam genişlikte içerik, sidebar gösteriliyor, tüm özellikler aktif.</p>
                                <div class="sample-grid">
                                    <div class="grid-item">İçerik 1</div>
                                    <div class="grid-item">İçerik 2</div>
                                    <div class="grid-item">İçerik 3</div>
                                    <div class="grid-item">İçerik 4</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="preview-frame tablet-frame">
                            <div class="preview-content">
                                <h5>Tablet Görünüm (768px - 1199px)</h5>
                                <p>Orta genişlik, optimize edilmiş layout.</p>
                                <div class="sample-grid tablet-grid">
                                    <div class="grid-item">İçerik 1</div>
                                    <div class="grid-item">İçerik 2</div>
                                    <div class="grid-item">İçerik 3</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="preview-frame mobile-frame">
                            <div class="preview-content">
                                <h5>Mobil Görünüm (767px-)</h5>
                                <p>Single column layout, büyük dokunmatik hedefler.</p>
                                <div class="sample-grid mobile-grid">
                                    <div class="grid-item">İçerik 1</div>
                                    <div class="grid-item">İçerik 2</div>
                                </div>
                                <button class="mobile-button">Touch Optimizasyonlu Buton</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.responsive-preview-container {
    position: relative;
    min-height: 400px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.preview-frame {
    display: none;
    padding: 20px;
    height: 400px;
    overflow-y: auto;
}

.preview-frame.active {
    display: block;
}

.desktop-frame {
    background-color: var(--desktop-frame-bg-color, #f8f9fa) !important;
}

.tablet-frame {
    background-color: var(--tablet-frame-bg-color, #e3f2fd) !important;
}

.mobile-frame {
    background-color: var(--mobile-frame-bg-color, #f3e5f5) !important;
}

.sample-grid {
    display: grid;
    gap: 15px;
    margin-top: 20px;
}

.sample-grid {
    grid-template-columns: repeat(4, 1fr);
}

.tablet-grid {
    grid-template-columns: repeat(3, 1fr);
}

.mobile-grid {
    grid-template-columns: repeat(2, 1fr);
}

.grid-item {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mobile-button {
    width: 100%;
    padding: var(--mobile-button-height, 44px) 0;
    margin-top: 20px;
    background-color: #4285f4;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    min-height: var(--mobile-button-height, 44px);
}

.responsive-preview-tabs .btn {
    margin-right: 10px;
}

.responsive-preview-tabs .btn.active {
    background-color: #4285f4;
    border-color: #4285f4;
    color: white;
}

/* Responsive CSS Generator */
@media (max-width: var(--mobile-breakpoint, 768px)) {
    .preview-content {        font-size: var(--mobile-base-font-size, 14px);
        line-height: var(--mobile-line-height, 1.5);
    }
    
    .preview-content h5 {
        font-size: var(--mobile-h1-font-size, 24px);
    }
}
</style>
