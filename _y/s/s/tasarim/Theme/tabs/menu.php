<?php
/**
 * Menu Tab Content - Theme Editor
 * Menü ayarları için sekme içeriği
 */
?>

<!-- Menu Panel -->
<div class="tab-pane fade" id="menu-panel" role="tabpanel">
    <h5 class="mb-3">
        <i class="fas fa-bars me-2"></i>Menü Ayarları
    </h5>
    
    <!-- Desktop ve Mobile Menü Ayarları -->
    <div class="row">
        <!-- Desktop Ana Menü Renkleri -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fa fa-desktop mr-2"></i>Desktop Ana Menü</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Arkaplan Rengi</label>
                                <input type="color" name="menu-background-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['menu-background-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Metin Rengi</label>
                                <input type="color" name="menu-text-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['menu-text-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hover Rengi</label>
                                <input type="color" name="menu-hover-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['menu-hover-color'] ) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hover Arkaplan</label>
                                <input type="color" name="menu-hover-bg-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['menu-hover-bg-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Aktif Link Rengi</label>
                                <input type="color" name="menu-active-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['menu-active-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Aktif Arkaplan</label>
                                <input type="color" name="menu-active-bg-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['menu-active-bg-color'] ) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menü Renkleri -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fa fa-mobile mr-2"></i>Mobile Menü</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mobil Arkaplan</label>
                                <input type="color" name="mobile-menu-background-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['mobile-menu-background-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mobil Metin Rengi</label>
                                <input type="color" name="mobile-menu-text-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['mobile-menu-text-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hamburger Icon Rengi</label>
                                <input type="color" name="hamburger-icon-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['hamburger-icon-color'] ) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mobil Link Hover Metin Rengi</label>
                                <input type="color" name="mobile-menu-hover-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['mobile-menu-hover-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mobil Link Hover Arkaplan Rengi</label>
                                <input type="color" name="mobile-menu-hover-bg-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['mobile-menu-hover-bg-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mobil Divider Rengi</label>
                                <input type="color" name="mobile-menu-divider-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['mobile-menu-divider-color'] ) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alt Menü (Dropdown) Ayarları -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fa fa-list mr-2"></i>Alt Menü (Dropdown)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Alt Menü Arkaplan</label>
                                <input type="color" name="submenu-bg-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['submenu-bg-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alt Menü Metin</label>
                                <input type="color" name="submenu-text-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['submenu-text-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alt Menü Hover</label>
                                <input type="color" name="submenu-hover-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['submenu-hover-color'] ) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Alt Menü Hover Arkaplan</label>
                                <input type="color" name="submenu-hover-bg-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['submenu-hover-bg-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alt Menü Sınır</label>
                                <input type="color" name="submenu-border-color" class="form-control color-picker" 
                                       value="<?= sanitizeColorValue($customCSS['submenu-border-color'] ) ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alt Menü Genişlik (px)</label>
                                <input type="number" name="submenu-width" class="form-control" 
                                       value="<?= sanitizeNumericValue($customCSS['submenu-width'] ) ?>" min="150" max="400">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menü Boyutları ve Font -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fa fa-text-height mr-2"></i>Boyutlar ve Font</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Desktop Font Boyutu (px)</label>
                                <input type="number" name="menu-font-size" class="form-control" 
                                       value="<?= sanitizeNumericValue($customCSS['menu-font-size'] ) ?>" min="12" max="24">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mobile Font Boyutu (px)</label>
                                <input type="number" name="mobile-menu-font-size" class="form-control" 
                                       value="<?= sanitizeNumericValue($customCSS['mobile-menu-font-size'] ) ?>" min="12" max="24">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Menü Yüksekliği (px)</label>
                                <input type="number" name="menu-height" class="form-control" 
                                       value="<?= sanitizeNumericValue($customCSS['menu-height'] ) ?>" min="40" max="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Alt Menü Font (px)</label>
                                <input type="number" name="submenu-font-size" class="form-control" 
                                       value="<?= sanitizeNumericValue($customCSS['submenu-font-size'] ) ?>" min="12" max="20">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Menü Padding (px)</label>
                                <input type="number" name="menu-padding" class="form-control" 
                                       value="<?= sanitizeNumericValue($customCSS['menu-padding'] ) ?>" min="5" max="30">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mobile Padding (px)</label>
                                <input type="number" name="mobile-menu-padding" class="form-control" 
                                       value="<?= sanitizeNumericValue($customCSS['mobile-menu-padding'] ) ?>" min="5" max="30">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <div class="row mt-4">
        <!-- Menü Önizleme -->
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fa fa-desktop mr-2"></i>Desktop Önizleme</h6>
                    <div class="card-header-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleMenuPreview">
                            <i class="fa fa-expand" id="menuPreviewToggleIcon"></i> Sabitle
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="menu-preview" class="menu-preview-container">
                        <nav class="navbar navbar-expand-lg" style="height: var(--menu-height);">
                            <div class="navbar-nav">
                                <a class="nav-link active" href="#" onclick="return false;">Ana Sayfa</a>
                                <div class="nav-item dropdown position-relative">
                                    <a class="nav-link dropdown-toggle" href="#" onclick="return false;"
                                       data-toggle="dropdown" aria-expanded="false">
                                        Ürünler <i class="fa fa-chevron-down ml-1" style="font-size: 10px;"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="return false;">
                                            <i class="fa fa-tv mr-2"></i>Elektronik
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="return false;">
                                            <i class="fa fa-tshirt mr-2"></i>Giyim
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="return false;">
                                            <i class="fa fa-home mr-2"></i>Ev & Yaşam
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#" onclick="return false;">
                                            <i class="fa fa-star mr-2"></i>Öne Çıkanlar
                                        </a>
                                    </div>
                                </div>
                                <a class="nav-link" href="#" onclick="return false;">Hakkımızda</a>
                                <a class="nav-link" href="#" onclick="return false;">İletişim</a>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100" id="mobileMenuPreviewCard">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fa fa-mobile mr-2"></i>Mobil Önizleme</h6>
                    <div class="card-header-actions">
                        <button type="button" class="btn btn-sm btn-outline-info" id="toggleMobileMenuPreview">
                            <i class="fa fa-expand" id="mobileMenuPreviewToggleIcon"></i> Sabitle
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="openMobileMenuDualPreview">
                            <i class="fa fa-window-restore"></i> Dual
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="mobileMenuPreview" class="mobile-menu-preview-container">
                        <div class="hamburger-menu-header">
                            <div class="hamburger-icon"><i class="fa fa-bars"></i></div>
                            <div class="mobile-logo">LOGO</div>
                            <div class="mobile-search"><i class="fa fa-search"></i></div>
                        </div>
                        <div class="mobile-menu-content">
                            <a href="#" class="mobile-menu-item active">
                                <i class="fa fa-home mr-2"></i> Ana Sayfa
                            </a>
                            <div class="mobile-menu-item mobile-submenu">
                                <div class="mobile-submenu-header">
                                    <i class="fa fa-shopping-bag mr-2"></i> Ürünler
                                    <i class="fa fa-chevron-down submenu-arrow"></i>
                                </div>
                                <div class="mobile-submenu-content">
                                    <a href="#" class="mobile-submenu-item"><i class="fa fa-tv mr-2"></i> Elektronik</a>
                                    <a href="#" class="mobile-submenu-item"><i class="fa fa-tshirt mr-2"></i> Giyim</a>
                                    <a href="#" class="mobile-submenu-item"><i class="fa fa-home mr-2"></i> Ev & Yaşam</a>
                                </div>
                            </div>
                            <a href="#" class="mobile-menu-item">
                                <i class="fa fa-info-circle mr-2"></i> Hakkımızda
                            </a>
                            <a href="#" class="mobile-menu-item">
                                <i class="fa fa-envelope mr-2"></i> İletişim
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* Genel Önizleme Stilleri */
.menu-preview-container {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 10px;
    background: var(--menu-background-color, #ffffff);
    height: 100%;
}

/* Desktop Menü Stilleri */
#menu-preview .navbar {
    height: var(--menu-height, 50px);
    padding: 0 var(--menu-padding, 15px);
    background: var(--menu-background-color, #ffffff);
}
#menu-preview .nav-link {
    color: var(--menu-text-color, #333333);
    font-size: var(--menu-font-size, 16px);
    padding: 0.5rem 1rem;
    transition: all 0.2s ease-in-out;
}
#menu-preview .nav-link:hover {
    color: var(--menu-hover-color, #4285f4);
    background-color: var(--menu-hover-bg-color, #f8f9fa);
    border-radius: 4px;
}
#menu-preview .nav-link.active {
    color: var(--menu-active-color, #4285f4);
    background-color: var(--menu-active-bg-color, rgba(66,133,244,0.1));
    font-weight: 500;
    border-radius: 4px;
}

/* Desktop Dropdown/Submenu Stilleri */
#menu-preview .dropdown {
    position: relative;
}
#menu-preview .dropdown-menu {
    background: var(--submenu-bg-color, #ffffff);
    border: 1px solid var(--submenu-border-color, #e9ecef);
    border-radius: var(--submenu-border-radius, 6px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: var(--submenu-width, 200px);
    padding: var(--submenu-padding, 10px) 0;
    margin-top: 2px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    display: none; /* Varsayılan olarak gizli */
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}
#menu-preview .dropdown:hover .dropdown-menu,
#menu-preview .dropdown-menu:hover {
    display: block !important;
    opacity: 1;
    transform: translateY(0);
}
#menu-preview .dropdown-item {
    color: var(--submenu-text-color, #333333);
    font-size: var(--submenu-font-size, 14px);
    padding: 8px 20px;
    transition: all 0.2s ease-in-out;
    border: none;
    background: none;
    text-decoration: none;
    display: block;
    white-space: nowrap;
}
#menu-preview .dropdown-item:hover {
    color: var(--submenu-hover-color, #4285f4);
    background-color: var(--submenu-hover-bg-color, #f8f9fa);
    text-decoration: none;
}
#menu-preview .dropdown-divider {
    height: 0;
    margin: 8px 0;
    overflow: hidden;
    border-top: 1px solid var(--submenu-border-color, #e9ecef);
}
/* Dropdown toggle icon */
#menu-preview .dropdown-toggle .fa-chevron-down {
    margin-left: 5px;
    transition: transform 0.3s ease;
}
#menu-preview .dropdown:hover .dropdown-toggle .fa-chevron-down {
    transform: rotate(180deg);
}

/* Mobil Menü Stilleri */
.mobile-menu-preview-container {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: var(--mobile-menu-background-color, #ffffff);
    max-width: 375px;
    margin: 0 auto;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: 100%;
}
.hamburger-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--mobile-menu-padding, 15px);
    background: var(--mobile-menu-background-color, #ffffff);
    border-bottom: 1px solid var(--mobile-menu-divider-color, #e9ecef);
}
.hamburger-icon {
    font-size: var(--mobile-menu-font-size, 24px);
    color: var(--hamburger-icon-color, #333333);
    cursor: pointer;
}
.mobile-logo {
    font-weight: bold;
    color: var(--mobile-menu-text-color, #333333);
}
.mobile-search {
    color: var(--mobile-menu-text-color, #333333);
}
.mobile-menu-content {
    background: var(--mobile-menu-background-color, #ffffff);
}
.mobile-menu-item {
    display: block;
    padding: 15px 20px;
    color: var(--mobile-menu-text-color, #333333);
    font-size: var(--mobile-menu-font-size, 16px);
    text-decoration: none;
    border-bottom: 1px solid var(--mobile-menu-divider-color, #e9ecef);
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}
.mobile-menu-item:hover {
    color: var(--mobile-menu-hover-color, #4285f4);
    background-color: var(--mobile-menu-hover-bg-color, #f8f9fa);
}
.mobile-menu-item.active {
    color: var(--menu-active-color, #4285f4);
    background-color: var(--menu-active-bg-color, rgba(66,133,244,0.1));
}

/* Mobil Submenu Stilleri */
.mobile-submenu {
    background: var(--mobile-menu-background-color, #ffffff);
}
.mobile-submenu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    color: var(--mobile-menu-text-color, #333333);
    font-size: var(--mobile-menu-font-size, 16px);
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.mobile-submenu-header:hover {
    color: var(--mobile-menu-hover-color, #4285f4);
    background-color: var(--mobile-menu-hover-bg-color, #f8f9fa);
}
.submenu-arrow {
    font-size: 12px;
    transition: transform 0.3s ease;
}
.mobile-submenu.expanded .submenu-arrow {
    transform: rotate(180deg);
}
.mobile-submenu-content {
    background: var(--submenu-bg-color, #f8f9fa);
    border-top: 1px solid var(--mobile-menu-divider-color, #e9ecef);
    display: none;
}
.mobile-submenu.expanded .mobile-submenu-content {
    display: block;
}
.mobile-submenu-item {
    display: block;
    padding: 12px 20px 12px 40px;
    color: var(--submenu-text-color, #5f6368);
    font-size: calc(var(--mobile-menu-font-size, 16px) - 1px);
    text-decoration: none;
    border-bottom: 1px solid var(--mobile-menu-divider-color, #e9ecef);
    transition: all 0.2s ease-in-out;
}
.mobile-submenu-item:hover {
    color: var(--submenu-hover-color, #4285f4);
    background-color: var(--submenu-hover-bg-color, #f0f0f0);
}

/* Sabitleme Stilleri */
.menu-preview-fixed, .mobile-menu-preview-fixed {
    position: fixed !important;
    top: 60px !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 1050 !important;
    margin: 0 !important;
    border-radius: 0 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    animation: slideDownMenuPreview 0.3s ease-out;
}
@keyframes slideDownMenuPreview { from { transform: translateY(-100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

/* Body Padding */
body.menu-preview-pinned, body.mobile-menu-preview-pinned {
    padding-top: 250px !important;
    transition: padding-top 0.3s ease;
}

/* Buton ve İkon Stilleri */
.card-header-actions { display: flex; gap: 5px; }
.card-header { display: flex; justify-content: space-between; align-items: center; }
#toggleMenuPreview.preview-pinned, #toggleMobileMenuPreview.preview-pinned {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: white !important;
}
#toggleMenuPreview.preview-pinned .fa-expand:before, #toggleMobileMenuPreview.preview-pinned .fa-expand:before {
    content: "\f066"; /* fa-compress */
}

/* Dual Preview Stilleri */
.dual-menu-preview-container { position: fixed; top: 60px; left: 0; right: 0; z-index: 1050; background: #f8f9fa; border-bottom: 3px solid #007bff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideDownMenuPreview 0.3s ease-out; }
body.dual-menu-preview-active { padding-top: 450px !important; }
.header-preview-removing { animation: slideUpPreview 0.3s ease-in-out; }
@keyframes slideUpPreview { from { transform: translateY(0); opacity: 1; } to { transform: translateY(-100%); opacity: 0; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Menu tab DOM loaded');
    
    function initializeMenuFunctions() {
        if (window.themeEditorInstance) {
            console.log('🚀 ThemeEditor instance bulundu, menü önizleme başlatılıyor...');
            if (typeof window.themeEditorInstance.initMenuPreviewToggle === 'function') {
                window.themeEditorInstance.initMenuPreviewToggle();
            } else {
                console.error('❌ initMenuPreviewToggle fonksiyonu ThemeEditor'da bulunamadı!');
            }
            const initialFormData = window.themeEditorInstance.getFormData();
            if (typeof window.themeEditorInstance.updateMenuPreview === 'function') {
                window.themeEditorInstance.updateMenuPreview(initialFormData);
            } else {
                console.error('❌ updateMenuPreview fonksiyonu ThemeEditor'da bulunamadı!');
            }
        } else {
            console.warn('⏳ ThemeEditor instance henüz hazır değil, 100ms sonra tekrar denenecek...');
            setTimeout(initializeMenuFunctions, 100);
        }
    }
    
    // Mobil menü altmenü interaktifliği
    function initializeMobileMenuInteractions() {
        // Desktop dropdown hover efekti
        $('#menu-preview .dropdown-toggle').on('mouseenter', function() {
            $(this).next('.dropdown-menu').show();
        });

        $('#menu-preview .dropdown').on('mouseleave', function() {
            $(this).find('.dropdown-menu').hide();
        });

        // Mobil submenu tıklama efekti
        $(document).on('click', '.mobile-submenu-header', function(e) {
            e.preventDefault();
            const $submenu = $(this).closest('.mobile-submenu');

            // Diğer açık submenu'ları kapat
            $('.mobile-submenu').not($submenu).removeClass('expanded');

            // Bu submenu'yu aç/kapat
            $submenu.toggleClass('expanded');

            console.log('📱 Mobil submenu toggle:', $submenu.hasClass('expanded') ? 'açıldı' : 'kapandı');
        });

        console.log('✅ Mobil menü interaksiyon event listeners eklendi');
    }

    initializeMenuFunctions();
    initializeMobileMenuInteractions();
});
</script>
