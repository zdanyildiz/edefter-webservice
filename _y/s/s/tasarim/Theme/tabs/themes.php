<?php
/**
 * Themes Tab Content - Theme Editor
 * Hazır temalar için sekme içeriği
 */
?>

<!-- Themes Panel -->
<div class="tab-pane fade" id="themes-panel" role="tabpanel">
    
    <!-- Bilgilendirme Paneli -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="fa fa-paint-brush"></i> Hazır Temalar vs Hızlı Renk Temaları</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fa fa-paint-brush text-primary"></i> Hazır Temalar (Bu Sekme)</h6>
                        <ul class="mb-2">
                            <li><strong>Komple tasarım sistemi:</strong> Renkler, tipografi, spacing</li>
                            <li><strong>Profesyonel çözümler:</strong> Uyumlu ve tutarlı</li>
                            <li><strong>Bütüncül değişim:</strong> Tüm site görünümü</li>
                            <li><strong>Geri dönüş:</strong> Tüm ayarları etkiler</li>
                        </ul>
                        <div class="badge badge-success">Komple Yeniden Tasarım İçin İdeal</div>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fa fa-magic text-info"></i> Hızlı Renk Temaları (Genel Sekme)</h6>
                        <ul class="mb-2">
                            <li><strong>Sadece renkler:</strong> Ana, ikincil, vurgu renkleri</li>
                            <li><strong>Hızlı uygulama:</strong> Anında değişiklik</li>
                            <li><strong>Kısmi değişim:</strong> Mevcut tasarım korunur</li>
                            <li><strong>Geri dönüş:</strong> Kolayca değiştirilebilir</li>
                        </ul>
                        <div class="badge badge-info">Hızlı Renk Değişimi İçin İdeal</div>
                    </div>
                </div>
                <hr class="my-2">
                <p class="mb-0"><strong>⚠️ Dikkat:</strong> Hazır tema uyguladığınızda mevcut tüm renk ve tasarım ayarlarınız değişecektir. Yedekleme önerilir.</p>
            </div>
        </div>
    </div>
    
    <h5 class="mb-3">
        <i class="fas fa-magic me-2"></i>Hazır Temalar
    </h5>
    
    <div class="row">
        <!-- Google Material Theme -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="google-material">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #4285f4;"></span>
                            <span class="color-dot" style="background: #34a853;"></span>
                            <span class="color-dot" style="background: #fbbc05;"></span>
                            <span class="color-dot" style="background: #ea4335;"></span>
                        </div>
                    </div>
                    <h6>Google Material</h6>
                    <p class="text-muted small">Modern ve temiz tasarım</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn" data-theme="google-material">
                        Uygula
                    </button>
                </div>
            </div>
        </div>

        <!-- Creative Theme -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="creative-colors">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #ff6b35;"></span>
                            <span class="color-dot" style="background: #f7931e;"></span>
                            <span class="color-dot" style="background: #ffd23f;"></span>
                            <span class="color-dot" style="background: #06d6a0;"></span>
                        </div>
                    </div>
                    <h6>Creative Colors</h6>
                    <p class="text-muted small">Yaratıcı ve canlı</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn" data-theme="creative-colors">
                        Uygula
                    </button>
                </div>
            </div>
        </div>

        <!-- Bootstrap Classic -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="bootstrap-classic">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #007bff;"></span>
                            <span class="color-dot" style="background: #28a745;"></span>
                            <span class="color-dot" style="background: #ffc107;"></span>
                            <span class="color-dot" style="background: #dc3545;"></span>
                        </div>
                    </div>
                    <h6>Bootstrap Classic</h6>
                    <p class="text-muted small">Klasik Bootstrap renkleri</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn" 
                            data-theme="bootstrap-classic">
                        Uygula
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Dark Theme -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="dark-modern">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #212529;"></span>
                            <span class="color-dot" style="background: #495057;"></span>
                            <span class="color-dot" style="background: #6c757d;"></span>
                            <span class="color-dot" style="background: #17a2b8;"></span>
                        </div>
                    </div>
                    <h6>Dark Modern</h6>
                    <p class="text-muted small">Koyu tema, modern tasarım</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn" 
                            data-theme="dark-modern">
                        Uygula
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Minimal Light -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="minimal-light">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #ffffff; border: 1px solid #ddd;"></span>
                            <span class="color-dot" style="background: #f8f9fa;"></span>
                            <span class="color-dot" style="background: #e9ecef;"></span>
                            <span class="color-dot" style="background: #6c757d;"></span>
                        </div>
                    </div>
                    <h6>Minimal Light</h6>
                    <p class="text-muted small">Minimal ve sade tasarım</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn" 
                            data-theme="minimal-light">
                        Uygula
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Corporate Blue -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="corporate-blue">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #2c3e50;"></span>
                            <span class="color-dot" style="background: #3498db;"></span>
                            <span class="color-dot" style="background: #ecf0f1;"></span>
                            <span class="color-dot" style="background: #e74c3c;"></span>
                        </div>
                    </div>
                    <h6>Corporate Blue</h6>
                    <p class="text-muted small">Kurumsal mavi tema</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn" 
                            data-theme="corporate-blue">
                        Uygula
                    </button>
                </div>
            </div>
        </div>
        
        <!-- E-commerce Orange -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="ecommerce-orange">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #ff6b35;"></span>
                            <span class="color-dot" style="background: #f7931e;"></span>
                            <span class="color-dot" style="background: #ffd23f;"></span>
                            <span class="color-dot" style="background: #06d6a0;"></span>
                        </div>
                    </div>
                    <h6>E-commerce Orange</h6>
                    <p class="text-muted small">Enerjik ve çekici tasarım</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn" 
                            data-theme="ecommerce-orange">
                        Uygula
                    </button>
                </div>
            </div>
        </div>

        <!-- Chic Red -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="chic-red">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #C62828;"></span>
                            <span class="color-dot" style="background: #E53935;"></span>
                            <span class="color-dot" style="background: #212121;"></span>
                            <span class="color-dot" style="background: #424242;"></span>
                        </div>
                    </div>
                    <h6>Chic Red</h6>
                    <p class="text-muted small">Şık ve modern kırmızı tema</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn"
                            data-theme="chic-red">
                        Uygula
                    </button>
                </div>
            </div>
        </div>

        <!-- Lovely Purple -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="lovely-purple">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #a855f7;"></span>
                            <span class="color-dot" style="background: #f472b6;"></span>
                            <span class="color-dot" style="background: #d8b4fe;"></span>
                            <span class="color-dot" style="background: #fbcfe8;"></span>
                        </div>
                    </div>
                    <h6>Lovely Purple</h6>
                    <p class="text-muted small">Sevimli ve zarif mor tema</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn"
                            data-theme="lovely-purple">
                        Uygula
                    </button>
                </div>
            </div>
        </div>

        <!-- Solaris -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="solaris">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #FFB300;"></span>
                            <span class="color-dot" style="background: #37474F;"></span>
                            <span class="color-dot" style="background: #263238;"></span>
                            <span class="color-dot" style="background: #4DB6AC;"></span>
                        </div>
                    </div>
                    <h6>Solaris</h6>
                    <p class="text-muted small">Güçlü ve dinamik koyu tema</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn"
                            data-theme="solaris">
                        Uygula
                    </button>
                </div>
            </div>
        </div>

        <!-- Midnight Luxury -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="midnight-luxury">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #D4AF37;"></span>
                            <span class="color-dot" style="background: #1A1A1A;"></span>
                            <span class="color-dot" style="background: #2D2D2D;"></span>
                            <span class="color-dot" style="background: #8B4513;"></span>
                        </div>
                    </div>
                    <h6>Midnight Luxury</h6>
                    <p class="text-muted small">Premium siyah altın tema</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn"
                            data-theme="midnight-luxury">
                        Uygula
                    </button>
                </div>
            </div>
        </div>

        <!-- Nature Fresh -->
        <div class="col-md-4 mb-4">
            <div class="card theme-card" data-theme="nature-fresh">
                <div class="card-body text-center">
                    <div class="theme-preview mb-3">
                        <div class="theme-colors">
                            <span class="color-dot" style="background: #2E7D32;"></span>
                            <span class="color-dot" style="background: #66BB6A;"></span>
                            <span class="color-dot" style="background: #A5D6A7;"></span>
                            <span class="color-dot" style="background: #8BC34A;"></span>
                        </div>
                    </div>
                    <h6>Nature Fresh</h6>
                    <p class="text-muted small">Doğal yeşil ferahlık</p>
                    <button type="button" class="btn btn-sm btn-primary apply-theme-btn"
                            data-theme="nature-fresh">
                        Uygula
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Tema Önizleme -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Seçilen Tema Önizleme</h6>
                </div>
                <div class="card-body">
                    <div id="selected-theme-preview" class="theme-preview-large">
                        <div class="preview-header">
                            <div class="logo-area"></div>
                            <div class="menu-area">
                                <span class="menu-item active">Ana Sayfa</span>
                                <span class="menu-item">Ürünler</span>
                                <span class="menu-item">Hakkımızda</span>
                                <span class="menu-item">İletişim</span>
                            </div>
                        </div>
                        <div class="preview-content">
                            <div class="content-section">
                                <h5>Başlık Örneği</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                <button class="preview-button primary">Primary Button</button>
                                <button class="preview-button secondary">Secondary Button</button>
                            </div>
                        </div>
                        <div class="preview-footer">
                            <p>&copy; 2024 Firma Adı - Tüm hakları saklıdır.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .theme-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .theme-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .theme-card.active {
        border-color: #4285f4;
        box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
    }

    .theme-colors {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 15px;
    }

    .color-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .theme-preview-large {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
    }

    .preview-header {
        background: var(--preview-header-bg, #4285f4);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo-area {
        width: 120px;
        height: 30px;
        background: rgba(255,255,255,0.2);
        border-radius: 4px;
    }

    .menu-area {
        display: flex;
        gap: 20px;
    }

    .menu-item {
        padding: 8px 12px;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .menu-item.active {
        background: rgba(255,255,255,0.2);
    }

    .preview-content {
        padding: 40px 20px;
        text-align: center;
    }

    .content-section h5 {
        color: var(--preview-text-color, #212529);
        margin-bottom: 15px;
    }

    .content-section p {
        color: var(--preview-text-secondary, #6c757d);
        margin-bottom: 25px;
    }

    .preview-button {
        margin: 0 10px 10px 0;
        padding: 10px 20px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .preview-button.primary {
        background: var(--preview-primary-color, #4285f4);
        color: white;
    }

    .preview-button.secondary {
        background: var(--preview-secondary-color, #6c757d);
        color: white;
    }

    .preview-footer {
        background: var(--preview-footer-bg, #f8f9fa);
        color: var(--preview-footer-text, #6c757d);
        padding: 20px;
        text-align: center;
        border-top: 1px solid #dee2e6;
    }

    .preview-footer p {
        margin: 0;
        font-size: 14px;
    }
</style>
