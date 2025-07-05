<?php
/**
 * Footer Tab Content - Theme Editor
 * Footer ayarları için sekme içeriği
 */
?>

<!-- Footer Panel -->
<div class="tab-pane fade" id="footer-panel" role="tabpanel">
    <h5 class="mb-3">
        <i class="fas fa-grip-horizontal me-2"></i>Footer Ayarları
    </h5>
    
    <div class="row">
        <!-- Footer Renkleri -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Footer Renkleri</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Footer Arkaplan Rengi</label>
                        <input type="color" name="footer-background-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['footer-background-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Footer Metin Rengi</label>
                        <input type="color" name="footer-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['footer-text-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Footer Link Rengi</label>
                        <input type="color" name="footer-link-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['footer-link-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Footer Link Hover Rengi</label>
                        <input type="color" name="footer-link-hover-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['footer-link-hover-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright Alanı -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Copyright Alanı</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Copyright Arkaplan</label>
                        <input type="color" name="copyright-background-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['copyright-background-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Copyright Metin Rengi</label>
                        <input type="color" name="copyright-text-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['copyright-text-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Copyright Link Rengi</label>
                        <input type="color" name="copyright-link-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['copyright-link-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Border Üst Rengi</label>
                        <input type="color" name="copyright-border-top-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['copyright-border-top-color'] ) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Sosyal Medya -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Sosyal Medya İkonları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Sosyal İkon Rengi</label>
                        <input type="color" name="social-icon-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['social-icon-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sosyal İkon Hover Rengi</label>
                        <input type="color" name="social-icon-hover-color" class="form-control color-picker" 
                               value="<?= sanitizeColorValue($customCSS['social-icon-hover-color'] ) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İkon Boyutu (px)</label>
                        <input type="number" name="social-icon-size" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['social-icon-size'] ) ?>" min="16" max="48">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Boyutları -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Footer Boyutları</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Footer Padding Üst/Alt (px)</label>
                        <input type="number" name="footer-padding-y" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['footer-padding-y']."px" ) ?>" min="20" max="80">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Footer Font Boyutu (px)</label>
                        <input type="number" name="footer-font-size" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['footer-font-size']."px" ) ?>" min="12" max="18">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Copyright Padding (px)</label>
                        <input type="number" name="copyright-padding" class="form-control" 
                               value="<?= sanitizeNumericValue($customCSS['copyright-padding']."px" ) ?>" min="10" max="40">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Footer Önizleme -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Footer Önizleme</h6>
                </div>
                <div class="card-body p-0">
                    <div id="footer-preview" class="footer-preview-container">
                        <!-- Ana Footer -->
                        <footer class="main-footer">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h6>Firma Adı</h6>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                        <div class="social-icons">
                                            <a href="#"><i class="fab fa-facebook"></i></a>
                                            <a href="#"><i class="fab fa-twitter"></i></a>
                                            <a href="#"><i class="fab fa-instagram"></i></a>
                                            <a href="#"><i class="fab fa-linkedin"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h6>Hızlı Linkler</h6>
                                        <ul class="footer-links">
                                            <li><a href="#">Ana Sayfa</a></li>
                                            <li><a href="#">Ürünler</a></li>
                                            <li><a href="#">Hakkımızda</a></li>
                                            <li><a href="#">İletişim</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-3">
                                        <h6>Kategoriler</h6>
                                        <ul class="footer-links">
                                            <li><a href="#">Kategori 1</a></li>
                                            <li><a href="#">Kategori 2</a></li>
                                            <li><a href="#">Kategori 3</a></li>
                                            <li><a href="#">Kategori 4</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-3">
                                        <h6>İletişim</h6>
                                        <p><i class="fas fa-map-marker-alt me-2"></i>Adres Bilgisi</p>
                                        <p><i class="fas fa-phone me-2"></i>+90 555 123 45 67</p>
                                        <p><i class="fas fa-envelope me-2"></i>info@example.com</p>
                                    </div>
                                </div>
                            </div>
                        </footer>
                        
                        <!-- Copyright -->
                        <div class="copyright-section">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p>&copy; 2024 Firma Adı. Tüm hakları saklıdır.</p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <a href="#">Gizlilik Politikası</a> | 
                                        <a href="#">Kullanım Şartları</a>
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
.footer-preview-container .main-footer {
    background-color: var(--footer-background-color, #343a40) !important;
    color: var(--footer-text-color, #ffffff);
    padding: var(--footer-padding-y, 40px) 0;
    font-size: var(--footer-font-size, 14px);
}

.footer-preview-container .main-footer h6 {
    color: var(--footer-text-color, #ffffff);
    margin-bottom: 15px;
    font-weight: 600;
}

.footer-preview-container .footer-links {
    list-style: none;
    padding: 0;
}

.footer-preview-container .footer-links li {
    margin-bottom: 8px;
}

.footer-preview-container .footer-links a {
    color: var(--footer-link-color, #adb5bd);
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-preview-container .footer-links a:hover {
    color: var(--footer-link-hover-color, #ffffff);
}

.footer-preview-container .social-icons a {
    color: var(--social-icon-color, #adb5bd);
    font-size: var(--social-icon-size, 24px);
    margin-right: 15px;
    transition: color 0.3s ease;
}

.footer-preview-container .social-icons a:hover {
    color: var(--social-icon-hover-color, #ffffff);
}

.footer-preview-container .copyright-section {
    background-color: var(--copyright-background-color, #212529) !important;
    color: var(--copyright-text-color, #6c757d);
    padding: var(--copyright-padding, 20px) 0;
    border-top: 1px solid var(--copyright-border-top-color, #495057);
    font-size: calc(var(--footer-font-size, 14px) - 1px);
}

.footer-preview-container .copyright-section a {
    color: var(--copyright-link-color, #adb5bd);
    text-decoration: none;
}

.footer-preview-container .copyright-section p {
    margin: 0;
}
</style>
