<?php
/**
 * Forms Tab Content - Theme Editor
 * Form ve buton ayarları için sekme içeriği
 */
?>

<!-- Forms Panel -->
<div class="tab-pane fade" id="forms-panel" role="tabpanel">
    <h5 class="mb-3">
        <i class="fas fa-edit me-2"></i>Form & Buton Ayarları
    </h5>
    
    <div class="row">
        <!-- Input Ayarları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Input Alanları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Input Arkaplan Rengi</label>
                        <input type="color" name="input-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['input-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Input Border Rengi</label>
                        <input type="color" name="input-border-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['input-border-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Input Focus Border</label>
                        <input type="color" name="input-focus-border-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['input-focus-border-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Input Metin Rengi</label>
                        <input type="color" name="input-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['input-text-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Placeholder Rengi</label>
                        <input type="color" name="input-placeholder-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['input-placeholder-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Primary Button -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Primary Butonlar</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Primary Arkaplan</label>
                        <input type="color" name="btn-primary-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-primary-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Primary Metin Rengi</label>
                        <input type="color" name="btn-primary-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-primary-text-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Primary Hover Arkaplan</label>
                        <input type="color" name="btn-primary-hover-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-primary-hover-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Primary Border Rengi</label>
                        <input type="color" name="btn-primary-border-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-primary-border-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Secondary Button -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Secondary Butonlar</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Secondary Arkaplan</label>
                        <input type="color" name="btn-secondary-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-secondary-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Secondary Metin Rengi</label>
                        <input type="color" name="btn-secondary-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-secondary-text-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Secondary Hover Arkaplan</label>
                        <input type="color" name="btn-secondary-hover-bg-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-secondary-hover-bg-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Outline Buton Rengi</label>
                        <input type="color" name="btn-outline-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['btn-outline-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Label ve Diğer Öğeler -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Label ve Diğer Öğeler</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Label Metin Rengi</label>
                        <input type="color" name="form-label-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['form-label-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Required (*) Rengi</label>
                        <input type="color" name="form-required-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['form-required-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Error Message Rengi</label>
                        <input type="color" name="form-error-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['form-error-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Success Message Rengi</label>
                        <input type="color" name="form-success-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['form-success-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Form Boyutları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Form Boyutları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Input Yüksekliği (px)</label>
                        <input type="number" name="input-height" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['input-height'] ) ?>" min="30" max="60">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Input Padding (px)</label>
                        <input type="number" name="input-padding" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['input-padding'] ) ?>" min="8" max="20">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Input Border Radius (px)</label>
                        <input type="number" name="input-border-radius" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['input-border-radius']."px" ) ?>" min="0" max="20">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Button Padding Y (px)</label>
                        <input type="number" name="btn-padding-y" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['btn-padding-y'] ) ?>" min="8" max="20">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Button Padding X (px)</label>
                        <input type="number" name="btn-padding-x" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['btn-padding-x'] ) ?>" min="16" max="40">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Önizleme -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Form Önizleme</h6>
                </div>
                <div class="card-body">
                    <div id="form-preview" class="form-preview-container">
                        <form class="preview-form">
                            <div class="mb-3">
                                <label class="form-label">Ad Soyad <span class="required">*</span></label>
                                <input type="text" class="form-control preview-input" placeholder="Adınızı giriniz">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" class="form-control preview-input" placeholder="ornek@email.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mesaj</label>
                                <textarea class="form-control preview-textarea" rows="3" placeholder="Mesajınızı yazınız..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="error-message">Bu alan zorunludur.</div>
                                <div class="success-message">Form başarıyla gönderildi!</div>
                            </div>
                            
                            <div class="button-group">
                                <button type="button" class="btn-primary-preview">Gönder</button>
                                <button type="button" class="btn-secondary-preview">İptal</button>
                                <button type="button" class="btn-outline-preview">Sıfırla</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-preview-container {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

#form-preview.form-preview-container .form-label {
    color: var(--form-label-color, #212529) !important; /* Özgüllük artırıldı */
    margin-bottom: 8px;
    font-weight: 500;
}

.preview-form .required {
    color: var(--form-required-color, #dc3545);
}

.preview-input,
.preview-textarea {
    background: var(--input-bg-color, #ffffff);
    border: 1px solid var(--input-border-color, #ced4da);
    border-radius: var(--input-border-radius, 4px);
    padding: var(--input-padding, 12px);
    height: var(--input-height, 38px); /* Eksik olan satır eklendi */
    color: var(--input-text-color, #495057);
    width: 100%;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.preview-input::placeholder,
.preview-textarea::placeholder {
    color: var(--input-placeholder-color, #6c757d);
}

.preview-input:focus,
.preview-textarea:focus {
    outline: none;
    border-color: var(--input-focus-border-color, #4285f4);
    box-shadow: 0 0 0 0.2rem rgba(66, 133, 244, 0.25);
}

.error-message {
    color: var(--form-error-color, #dc3545);
    font-size: 14px;
    margin-bottom: 10px;
    display: none;
}

.success-message {
    color: var(--form-success-color, #28a745);
    font-size: 14px;
    margin-bottom: 10px;
    display: none;
}

.button-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-primary-preview {
    background: var(--btn-primary-bg-color, #4285f4);
    color: var(--btn-primary-text-color, #ffffff);
    border: 1px solid var(--btn-primary-border-color, #4285f4);
    padding: var(--btn-padding-y, 12px) var(--btn-padding-x, 24px);
    border-radius: var(--input-border-radius, 4px);
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary-preview:hover {
    background: var(--btn-primary-hover-bg-color, #3367d6);
    transform: translateY(-1px);
}

.btn-secondary-preview {
    background: var(--btn-secondary-bg-color, #6c757d);
    color: var(--btn-secondary-text-color, #ffffff);
    border: 1px solid var(--btn-secondary-bg-color, #6c757d);
    padding: var(--btn-padding-y, 12px) var(--btn-padding-x, 24px);
    border-radius: var(--input-border-radius, 4px);
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary-preview:hover {
    background: var(--btn-secondary-hover-bg-color, #5a6268);
}

.btn-outline-preview {
    background: transparent;
    color: var(--btn-outline-color, #4285f4);
    border: 1px solid var(--btn-outline-color, #4285f4);
    padding: var(--btn-padding-y, 12px) var(--btn-padding-x, 24px);
    border-radius: var(--input-border-radius, 4px);
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-outline-preview:hover {
    background: var(--btn-outline-color, #4285f4);
    color: white;
}

/* Responsive form */
@media (max-width: 576px) {
    .button-group {        flex-direction: column;
    }
    
    .btn-primary-preview,
    .btn-secondary-preview,
    .btn-outline-preview {
        width: 100%;
        text-align: center;
    }
}
</style>
