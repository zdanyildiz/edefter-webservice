<!-- Header Sekmesi -->
<div class="tab-pane fade" id="header-panel" role="tabpanel">
    <div class="row" id="headerPreviewRow">
        <div class="col-md-6">
            <!-- Header Önizleme -->                                    
            <div class="card" id="headerPreviewCard">                                        
                <div class="card-header">
                    <h4>
                        <i class="fa fa-eye"></i> Header Önizleme
                        <div class="float-right">
                            <button type="button" class="btn btn-sm btn-outline-info mr-2" id="openDualPreview" title="Desktop ve Mobile önizlemeyi yan yana göster" onclick="return false;">
                                <i class="fa fa-columns"></i> Sabitle
                            </button>
                            <!-- button type="button" class="btn btn-sm btn-outline-primary" id="toggleHeaderPreview" title="Header önizlemeyi sayfanın üstüne sabitle/kaldır" onclick="return false;">
                                <i class="fa fa-expand" id="headerPreviewToggleIcon"></i>
                            </button -->
                        </div>
                    </h4>
                </div>                                        
                <div class="card-body">
                    <div class="theme-preview" id="headerPreview">                                                
                        <!-- Üst İletişim Çubuğu -->
                        <div id="topContactPreview" style="
                            background: var(--top-contact-and-social-bg-color, #f8f9fa);
                            padding: 6px 16px;
                            border-bottom: 1px solid var(--border-light-color, #e9ecef);
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            font-size: 12px;
                            min-height: 32px;
                            max-height: 40px;
                        ">
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <span style="color: var(--top-contact-and-social-link-color, #5f6368); display: flex; align-items: center; gap: 5px;">
                                    <i class="fa fa-phone" style="color: var(--top-contact-and-social-icon-color, #5f6368); font-size: 11px;"></i>
                                    <span>0212 555 0000</span>
                                </span>
                                <span style="color: var(--top-contact-and-social-link-color, #5f6368); display: flex; align-items: center; gap: 5px;">
                                    <i class="fa fa-envelope" style="color: var(--top-contact-and-social-icon-color, #5f6368); font-size: 11px;"></i>
                                    <span>info@example.com</span>
                                </span>
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <i class="fa fa-facebook" style="color: var(--top-contact-and-social-icon-color, #5f6368); cursor: pointer; font-size: 11px;"></i>
                                <i class="fa fa-twitter" style="color: var(--top-contact-and-social-icon-color, #5f6368); cursor: pointer; font-size: 11px;"></i>
                                <i class="fa fa-instagram" style="color: var(--top-contact-and-social-icon-color, #5f6368); cursor: pointer; font-size: 11px;"></i>
                            </div>
                        </div>
                        
                        <!-- Ana Header Alanı -->
                        <div id="headerPreviewContent" style="
                            background: var(--header-bg-color, #ffffff);
                            border-bottom: var(--header-border-width, 1px) solid var(--header-border-color, #e9ecef);
                            padding: var(--header-padding, 15px);
                            min-height: var(--header-min-height, 80px);
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                        ">
                            <div style="
                                width: var(--header-logo-width, 150px);
                                height: 40px;
                                background: var(--primary-color, #4285f4);
                                border-radius: 4px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-weight: bold;
                                margin: var(--header-logo-margin-top, 0) var(--header-logo-margin-right, 0) var(--header-logo-margin-bottom, 0) var(--header-logo-margin-left, 0);
                            ">LOGO</div>
                            
                            <div style="flex: 1; text-align: center;">
                                <input type="text" placeholder="Ürün arayın..." style="
                                    width: 70%;
                                    padding: 8px 12px;
                                    border: 1px solid var(--border-color, #dadce0);
                                    border-radius: 4px;
                                ">
                            </div>
                            
                            <div style="display: flex; gap: 15px;">
                                <i class="fa fa-search" style="color: var(--shop-menu-container-icon-color-search, #333333); font-size: 18px; cursor: pointer;"></i>
                                <i class="fa fa-user" style="color: var(--shop-menu-container-icon-color-member, #333333); font-size: 18px; cursor: pointer;"></i>
                                <i class="fa fa-heart" style="color: var(--shop-menu-container-icon-color-favorites, #333333); font-size: 18px; cursor: pointer;"></i>
                                <i class="fa fa-shopping-cart" style="color: var(--shop-menu-container-icon-color-basket, #333333); font-size: 18px; cursor: pointer; position: relative;">
                                    <span style="
                                        position: absolute;
                                        top: -8px;
                                        right: -8px;
                                        background: var(--danger-color, #ea4335);
                                        color: white;
                                        border-radius: 50%;
                                        width: 16px;
                                        height: 16px;
                                        font-size: 10px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                    ">3</span>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">                           
            <!-- Mobile Header Önizleme -->
            <div class="card" id="mobileHeaderPreviewCard">                                        
                <div class="card-header">
                    <h4>
                        <i class="fa fa-mobile"></i> Mobile Header Önizleme
                        <div class="float-right">
                            <button type="button" class="btn btn-sm btn-outline-info mr-2" id="openDualPreviewFromMobile" title="Desktop ve Mobile önizlemeyi yan yana göster" onclick="return false;">
                                <i class="fa fa-columns"></i> Sabitle
                            </button>
                            <!-- button type="button" class="btn btn-sm btn-outline-success" id="toggleMobileHeaderPreview" title="Mobile Header önizlemeyi sayfanın üstüne sabitle/kaldır" onclick="return false;">
                                <i class="fa fa-expand" id="mobileHeaderPreviewToggleIcon"></i>
                            </button -->
                        </div>
                    </h4>
                </div>                                        
                <div class="card-body">                                              
                    <div class="theme-preview" id="mobileHeaderPreview">                                                
                        <!-- Mobil Üst İletişim Alanı -->
                        <div style="
                            background: var(--top-contact-and-social-bg-color-mobile, var(--top-contact-and-social-bg-color, #f8f9fa));
                            padding: 4px 15px;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            font-size: 10px;
                            border-bottom: 1px solid rgba(0,0,0,0.1);
                            min-height: 28px;
                            max-height: 32px;
                        ">
                            <div style="
                                display: flex;
                                align-items: center;
                                gap: 6px;
                                color: var(--top-contact-and-social-link-color-mobile, var(--top-contact-and-social-link-color, #5f6368));
                            ">
                                <i class="fa fa-phone" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                                <span>0212 XXX XX XX</span>
                            </div>
                            <div style="
                                display: flex;
                                align-items: center;
                                gap: 5px;
                                color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368));
                            ">                                                        
                                <i class="fa fa-facebook" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                                <i class="fa fa-instagram" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                                <i class="fa fa-whatsapp" style="color: var(--top-contact-and-social-icon-color-mobile, var(--top-contact-and-social-icon-color, #5f6368)); font-size: 9px;"></i>
                            </div>
                        </div>                                                
                        
                        <!-- Ana Header Alanı -->
                        <div style="
                            background: var(--header-mobile-bg-color, #ffffff);
                            border-bottom: var(--header-mobile-border-width, 1px) solid var(--header-mobile-border-color, #e9ecef);
                            padding: var(--header-mobile-padding, 15px);
                            min-height: var(--header-mobile-min-height, 60px);
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                        ">
                            <!-- Menü -->
                            <div style="
                                width: 24px;
                                height: 24px;
                                background: var(--text-primary-color, #333333);
                                border-radius: 2px;
                                position: relative;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <div style="
                                    color: white;
                                    font-size: 12px;
                                    line-height: 1;
                                ">☰</div>                                                    
                            </div>
                            
                            <!-- Logo -->
                            <div style="
                                width: var(--header-logo-mobile-width, 100px);
                                height: 30px;
                                background: var(--primary-color, #4285f4);
                                border-radius: 4px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-size: 12px;
                                font-weight: bold;
                                margin: var(--header-mobile-logo-margin-top, 0) var(--header-mobile-logo-margin-right, 0) var(--header-mobile-logo-margin-bottom, 0) var(--header-mobile-logo-margin-left, 0);
                            ">LOGO</div>
                              
                            <!-- Eylem İkonları -->
                            <div style="
                                display: flex;
                                align-items: center;
                                gap: var(--mobile-action-icon-gap, 12px);
                            ">
                                <!-- Telefon -->
                                <div style="
                                    width: var(--mobile-action-icon-size);
                                    height: var(--mobile-action-icon-size);
                                    background: var(--mobile-action-icon-phone-bg-color);
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    cursor: pointer;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                ">
                                    <i class="fa fa-phone" style="color: white; font-size: 12px;"></i>                                                        
                                </div>
                                
                                <!-- WhatsApp -->
                                <div style="
                                    width: var(--mobile-action-icon-size);
                                    height: var(--mobile-action-icon-size);
                                    background: var(--mobile-action-icon-whatsapp-bg-color);
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    cursor: pointer;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                ">
                                    <i class="fa fa-whatsapp" style="color: white; font-size: 12px;"></i>                                                        
                                </div>
                                  
                                <!-- Sepet -->
                                <div style="
                                    width: var(--mobile-action-icon-size, 32px);
                                    height: var(--mobile-action-icon-size, 32px);
                                    background: var(--mobile-action-icon-basket-bg-color, #4285f4);
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    cursor: pointer;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    position: relative;
                                ">                                                            
                                    <i class="fa fa-shopping-cart" style="color: white; font-size: 11px;"></i>
                                    <!-- Sayaç -->
                                    <div style="
                                        position: absolute;
                                        top: -4px;
                                        right: -4px;
                                        width: 16px;
                                        height: 16px;
                                        background: var(--mobile-action-icon-basket-counter-bg-color, #dc3545);
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        color: white;
                                        font-size: 8px;
                                        font-weight: bold;
                                    ">3</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                                    
            </div>
        </div>
    </div>

    <!-- Header Ayarları -->
    <?php include __DIR__ . '/header-settings.php'; ?>
</div>
