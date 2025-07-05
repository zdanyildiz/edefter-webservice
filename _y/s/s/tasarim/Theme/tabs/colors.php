<?php
/**
 * Renkler Sekmesi
 * Tema editörü ana renk ayarları
 */
?>
<!-- Renkler Sekmesi -->
<div class="tab-pane fade active in show" id="general-panel" role="tabpanel">
      <!-- Bilgilendirme Paneli -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fa fa-info-circle"></i> Hızlı Renk Temaları vs Hazır Temalar</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fa fa-magic"></i> Hızlı Renk Temaları (Bu Sekme)</h6>
                        <ul class="mb-2">
                            <li><strong>Amaç:</strong> Sadece renkleri hızlıca değiştir</li>
                            <li><strong>Etki:</strong> Ana renkler, durum renkleri, metin renkleri</li>
                            <li><strong>Hız:</strong> Anında uygulanır, tek tıkla</li>
                            <li><strong>Özelleştirme:</strong> Renkler üzerinde devam edilebilir</li>
                        </ul>
                        <small class="text-muted"><strong>İdeal:</strong> Mevcut tasarımından memnunsan ama sadece renk uyumunu değiştirmek istiyorsan</small>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fa fa-paint-brush"></i> Hazır Temalar (Temalar Sekmesi)</h6>
                        <ul class="mb-2">
                            <li><strong>Amaç:</strong> Komple tema değişimi</li>
                            <li><strong>Etki:</strong> Renkler, tipografi, spacing, efektler</li>
                            <li><strong>Hız:</strong> Tema yükleme gerekir</li>
                            <li><strong>Özelleştirme:</strong> Profesyonel bütüncül çözüm</li>
                        </ul>
                        <small class="text-muted"><strong>İdeal:</strong> Sitenin tüm görünümünü komple değiştirmek istiyorsan</small>
                    </div>
                </div>
                <hr class="my-2">
                <p class="mb-0"><strong>💡 İpucu:</strong> Aşağıdaki Hızlı Renk Temalarından birini seçtiğinizde, yukarıdaki tüm renk form alanları otomatik olarak güncellenir ve anında önizleme yapabilirsiniz.</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-palette"></i> Ana Renkler</h4>
                </div>
                <div class="card-body">
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Birincil Renk (Primary)</label>
                                <input type="color" name="primary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['primary-color'] )?>" data-fallback="#4285f4">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Açık Birincil Renk</label>
                                <input type="color" name="primary-light-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['primary-light-color'] )?>" data-fallback="#6ea6f7">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Koyu Birincil Renk</label>
                                <input type="color" name="primary-dark-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['primary-dark-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>İkincil Renk (Secondary)</label>
                                <input type="color" name="secondary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['secondary-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Açık İkincil Renk</label>
                                <input type="color" name="secondary-light-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['secondary-light-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Koyu İkincil Renk</label>
                                <input type="color" name="secondary-dark-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['secondary-dark-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Vurgu Rengi (Accent)</label>
                                <input type="color" name="accent-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['accent-color'] )?>">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Başarı Rengi (Success)</label>
                                <input type="color" name="success-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['success-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Bilgi Rengi (Info)</label>
                                <input type="color" name="info-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['info-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Uyarı Rengi (Warning)</label>
                                <input type="color" name="warning-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['warning-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Hata Rengi (Danger)</label>
                                <input type="color" name="danger-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['danger-color'] )?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arka Plan Renkleri -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-fill"></i> Arka Plan Renkleri</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Site Arka Plan Rengi</label>
                                <input type="color" name="body-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['body-bg-color'] )?>" data-fallback="#ffffff">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İçerik Arka Plan Rengi</label>
                                <input type="color" name="content-bg-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['content-bg-color'] )?>" data-fallback="#ffffff">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Birincil Arka Plan</label>
                                <input type="color" name="background-primary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['background-primary-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İkincil Arka Plan</label>
                                <input type="color" name="background-secondary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['background-secondary-color'] )?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Açık Arka Plan</label>
                                <input type="color" name="background-light-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['background-light-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Koyu Arka Plan</label>
                                <input type="color" name="background-dark-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['background-dark-color'] )?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metin Renkleri -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-font"></i> Metin Renkleri</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ana Metin Rengi</label>
                                <input type="color" name="text-primary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-primary-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gövde Metin Rengi</label>
                                <input type="color" name="body-text-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['body-text-color'] )?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İkincil Metin Rengi</label>
                                <input type="color" name="text-secondary-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-secondary-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Açık Zemin Metin Rengi</label>
                                <input type="color" name="text-light-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-light-color'] )?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Koyu Zemin Metin Rengi</label>
                                <input type="color" name="text-dark-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-dark-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Başlık Rengi</label>
                                <input type="color" name="heading-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['heading-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Link Rengi</label>
                                <input type="color" name="link-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['link-color'] )?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Link Hover Rengi</label>
                                <input type="color" name="link-hover-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['link-hover-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Soluk Metin Rengi</label>
                                <input type="color" name="text-muted-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['text-muted-color'] )?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Renk Önizleme -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-eye"></i> Renk Önizleme</h4>
                </div>
                <div class="card-body">
                    <div class="theme-preview" id="colorPreview">
                        <!-- Birincil Renkler -->
                        <div class="color-sample-group">
                            <h6>Ana Renkler</h6>
                            <div class="color-samples">
                                <div class="color-sample">
                                    <div class="color-box" style="background: var(--primary-color, #4285f4);"></div>
                                    <small>Primary</small>
                                </div>
                                <div class="color-sample">
                                    <div class="color-box" style="background: var(--secondary-color, #6c757d);"></div>
                                    <small>Secondary</small>
                                </div>
                                <div class="color-sample">
                                    <div class="color-box" style="background: var(--accent-color, #ff5722);"></div>
                                    <small>Accent</small>
                                </div>
                            </div>
                        </div>

                        <!-- Durum Renkleri -->
                        <div class="color-sample-group mt-3">
                            <h6>Durum Renkleri</h6>
                            <div class="color-samples">
                                <div class="color-sample">
                                    <div class="color-box" style="background: var(--success-color, #28a745);"></div>
                                    <small>Success</small>
                                </div>
                                <div class="color-sample">
                                    <div class="color-box" style="background: var(--warning-color, #ffc107);"></div>
                                    <small>Warning</small>
                                </div>
                                <div class="color-sample">
                                    <div class="color-box" style="background: var(--danger-color, #dc3545);"></div>
                                    <small>Danger</small>
                                </div>
                            </div>
                        </div>

                        <!-- Metin Örnekleri -->
                        <div class="mt-3">
                            <h6>Metin Örnekleri</h6>
                            <div class="text-samples" style="background: var(--content-bg-color, #ffffff); padding: 15px; border-radius: 8px; border: 1px solid var(--border-light-color, #e9ecef);">
                                <h5 style="color: var(--heading-color, #1a1a1a); margin: 0 0 10px 0;">Ana Başlık</h5>
                                <p style="color: var(--text-primary-color, #202124); margin: 0 0 8px 0;">Bu bir ana metin örneğidir. Normal içerik metinleri bu renkte görünecektir.</p>
                                <p style="color: var(--text-secondary-color, #5f6368); margin: 0 0 8px 0; font-size: 14px;">Bu ikincil metin rengidir. Açıklamalar ve yan bilgiler için kullanılır.</p>
                                <a href="#" class="preview-link" style="color: var(--link-color, #4285f4); text-decoration: none;">Bu bir link örneğidir</a>
                                <p style="color: var(--text-muted-color, #9aa0a6); margin: 8px 0 0 0; font-size: 12px;">Bu soluk metin rengidir. Çok önemli olmayan bilgiler için.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Renk Önizleme -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-eye"></i> Renk Önizleme</h4>
                </div>
                <div class="card-body">
                    <div class="theme-preview" id="colorPreview2">
                        <!-- Metin Örnekleri -->
                        <div class="mt-3">
                            <h6>Metin Örnekleri</h6>
                            <div class="text-samples" style="background: var(--content-bg-color, #ffffff); padding: 15px; border-radius: 8px; border: 1px solid var(--border-light-color, #e9ecef);">
                                <h5 style="color: var(--heading-color, #1a1a1a); margin: 0 0 10px 0;">Ana Başlık</h5>
                                <p style="color: var(--text-primary-color, #202124); margin: 0 0 8px 0;">Bu bir ana metin örneğidir. Normal içerik metinleri bu renkte görünecektir.</p>
                                <p style="color: var(--text-secondary-color, #5f6368); margin: 0 0 8px 0; font-size: 14px;">Bu ikincil metin rengidir. Açıklamalar ve yan bilgiler için kullanılır.</p>
                                <a href="#" class="preview-link" style="color: var(--link-color, #4285f4); text-decoration: none;">Bu bir link örneğidir</a>
                                <p style="color: var(--text-muted-color, #9aa0a6); margin: 8px 0 0 0; font-size: 12px;">Bu soluk metin rengidir. Çok önemli olmayan bilgiler için.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Renk Önizleme -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-eye"></i> Renk Önizleme</h4>
                </div>
                <div class="card-body">
                    <div class="theme-preview" id="colorPreview2">
                        <!-- Metin Örnekleri -->
                        <div class="mt-3">
                            <h6>Metin Örnekleri</h6>
                            <div class="text-samples" style="background: var(--content-bg-color, #ffffff); padding: 15px; border-radius: 8px; border: 1px solid var(--border-light-color, #e9ecef);">
                                <h5 style="color: var(--heading-color, #1a1a1a); margin: 0 0 10px 0;">Ana Başlık</h5>
                                <p style="color: var(--text-primary-color, #202124); margin: 0 0 8px 0;">Bu bir ana metin örneğidir. Normal içerik metinleri bu renkte görünecektir.</p>
                                <p style="color: var(--text-secondary-color, #5f6368); margin: 0 0 8px 0; font-size: 14px;">Bu ikincil metin rengidir. Açıklamalar ve yan bilgiler için kullanılır.</p>
                                <a href="#" class="preview-link" style="color: var(--link-color, #4285f4); text-decoration: none;">Bu bir link örneğidir</a>
                                <p style="color: var(--text-muted-color, #9aa0a6); margin: 8px 0 0 0; font-size: 12px;">Bu soluk metin rengidir. Çok önemli olmayan bilgiler için.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hızlı Renk Temaları - ->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-magic"></i> Hızlı Renk Temaları</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="theme-card" onclick="applyColorTheme('blue')">
                                <h6>Mavi Tema</h6>
                                <div class="theme-preview-colors">
                                    <div class="theme-preview-color" style="background: #4285f4;"></div>
                                    <div class="theme-preview-color" style="background: #6ea6f7;"></div>
                                    <div class="theme-preview-color" style="background: #2c5aa0;"></div>
                                </div>
                                <small>Profesyonel ve güvenilir</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="theme-card" onclick="applyColorTheme('green')">
                                <h6>Yeşil Tema</h6>
                                <div class="theme-preview-colors">
                                    <div class="theme-preview-color" style="background: #28a745;"></div>
                                    <div class="theme-preview-color" style="background: #5cb85c;"></div>
                                    <div class="theme-preview-color" style="background: #155724;"></div>
                                </div>
                                <small>Doğal ve huzurlu</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="theme-card" onclick="applyColorTheme('purple')">
                                <h6>Mor Tema</h6>
                                <div class="theme-preview-colors">
                                    <div class="theme-preview-color" style="background: #6f42c1;"></div>
                                    <div class="theme-preview-color" style="background: #9561e2;"></div>
                                    <div class="theme-preview-color" style="background: #4e2a8e;"></div>
                                </div>
                                <small>Yaratıcı ve modern</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="theme-card" onclick="applyColorTheme('orange')">
                                <h6>Turuncu Tema</h6>
                                <div class="theme-preview-colors">
                                    <div class="theme-preview-color" style="background: #fd7e14;"></div>
                                    <div class="theme-preview-color" style="background: #ff9800;"></div>
                                    <div class="theme-preview-color" style="background: #d63384;"></div>
                                </div>
                                <small>Enerjik ve canlı</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div -->
        </div>
    </div>

    <!-- Sınır & Köşe Ayarları -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-border-style"></i> Sınır & Köşe Ayarları</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sınır Rengi</label>
                                <input type="color" name="border-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['border-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>İkincil Sınır Rengi</label>
                                <input type="color" name="border-light-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['border-light-color'] )?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Koyu Sınır Rengi</label>
                                <input type="color" name="border-dark-color" class="form-control color-picker" value="<?=sanitizeColorValue($customCSS['border-dark-color'] )?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sınır Stili</label>
                                <select name="border-style" class="form-control">
                                    <option value="solid" <?=($customCSS['border-style'] ) == 'solid' ? 'selected' : ''?>>Düz (Solid)</option>
                                    <option value="dashed" <?=($customCSS['border-style'] ) == 'dashed' ? 'selected' : ''?>>Kesikli (Dashed)</option>
                                    <option value="dotted" <?=($customCSS['border-style'] ) == 'dotted' ? 'selected' : ''?>>Noktalı (Dotted)</option>
                                    <option value="double" <?=($customCSS['border-style'] ) == 'double' ? 'selected' : ''?>>Çift Çizgi (Double)</option>
                                    <option value="groove" <?=($customCSS['border-style'] ) == 'groove' ? 'selected' : ''?>>Oyuklu (Groove)</option>
                                    <option value="ridge" <?=($customCSS['border-style'] ) == 'ridge' ? 'selected' : ''?>>Çıkıntılı (Ridge)</option>
                                </select>
                            </div>
                        </div>                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sınır Genişliği (px)</label>
                                <input type="number" name="border-width" class="form-control" min="0" max="10" step="1" value="<?=intval(sanitizeNumericValue($customCSS['border-width'] ))?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Köşe Yuvarlaklığı (px)</label>
                                <input type="number" name="border-radius-base" class="form-control" min="0" max="50" step="1" value="<?=intval(sanitizeNumericValue($customCSS['border-radius-base'] ))?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kart Köşe Yuvarlaklığı (px)</label>
                                <input type="number" name="card-border-radius" class="form-control" min="0" max="50" step="1" value="<?=intval(sanitizeNumericValue($customCSS['card-border-radius'] ))?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Input Köşe Yuvarlaklığı (px)</label>
                                <input type="number" name="input-border-radius-1" class="form-control" min="0" max="25" step="1" value="<?=intval(sanitizeNumericValue($customCSS['input-border-radius'] ))?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Buton Köşe Yuvarlaklığı (px)</label>
                                <input type="number" name="btn-border-radius" class="form-control" min="0" max="50" step="1" value="<?=intval(sanitizeNumericValue($customCSS['btn-border-radius'] ))?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Sınır Önizleme -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-eye"></i> Sınır & Köşe Önizleme</h4>
                </div>
                <div class="card-body">
                    <div class="border-preview-container">
                        <!-- Genel Sınır -->
                        <div class="border-preview-item" id="borderPreviewGeneral">
                            <div class="preview-border" style="
                                border: var(--border-width, 1px) var(--border-style, solid) var(--border-color, #dadce0);
                                border-radius: var(--border-radius-base, 8px);
                                padding: 15px;
                                margin: 8px;
                                background: var(--content-bg-color, #ffffff);
                                color: var(--text-primary-color, #333);
                                text-align: center;
                                font-size: 12px;
                            ">
                                Genel Köşe<br>
                                <small>(border-radius-base)</small>
                            </div>
                        </div>

                        <!-- Kart Sınırı -->
                        <div class="border-preview-item" id="borderPreviewCard">
                            <div class="preview-border" style="
                                border: var(--border-width, 1px) var(--border-style, solid) var(--border-light-color, #e9ecef);
                                border-radius: var(--card-border-radius, 12px);
                                padding: 15px;
                                margin: 8px;
                                background: var(--content-bg-color, #ffffff);
                                color: var(--text-primary-color, #333);
                                text-align: center;
                                font-size: 12px;
                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            ">
                                Kart Köşesi<br>
                                <small>(card-border-radius)</small>
                            </div>
                        </div>

                        <!-- Input Sınırı -->
                        <div class="border-preview-item" id="borderPreviewInput">
                            <div class="preview-border" style="
                                border: var(--border-width) var(--border-style) var(--border-color);
                                border-radius: var(--input-border-radius);
                                padding: 8px 12px;
                                margin: 8px;
                                background: var(--content-bg-color);
                                color: var(--text-secondary-color);
                                text-align: center;
                                font-size: 12px;
                                min-height: 35px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                Input Köşesi<br>
                                <small>(input-border-radius)</small>
                            </div>
                        </div>

                        <!-- Buton Sınırı -->
                        <div class="border-preview-item" id="borderPreviewButton">
                            <div class="preview-border" style="
                                border: var(--border-width, 1px) var(--border-style, solid) var(--primary-color, #4285f4);
                                border-radius: var(--btn-border-radius, 6px);
                                padding: 10px 15px;
                                margin: 8px;
                                background: var(--primary-color, #4285f4);
                                color: white;
                                text-align: center;
                                font-size: 12px;
                                font-weight: bold;
                                cursor: pointer;
                                transition: all 0.3s ease;
                            ">
                                Buton Köşesi<br>
                                <small>(btn-border-radius)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">💡 Sınır stili tüm örneklere uygulanır, her biri farklı köşe yuvarlaklığını gösterir</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.color-samples {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.color-sample {
    text-align: center;
}

.color-box {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    border: 2px solid rgba(0,0,0,0.1);
    margin-bottom: 5px;
}

.color-sample small {
    display: block;
    font-size: 10px;
    color: #666;
}

.color-sample-group h6 {
    margin-bottom: 10px;
    color: #333;
    font-weight: 600;
}

.theme-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    margin-bottom: 10px;
}

.theme-card:hover {
    border-color: #4285f4;
    box-shadow: 0 2px 8px rgba(66,133,244,0.2);
    transform: translateY(-2px);
}

.theme-preview-colors {
    display: flex;
    gap: 5px;
    justify-content: center;
    margin: 10px 0;
}

.theme-preview-color {
    width: 25px;
    height: 25px;
    border-radius: 4px;
    border: 1px solid rgba(0,0,0,0.1);
}

.border-preview-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 5px;
}

.border-preview-item {
    display: flex;
    justify-content: center;
}

.preview-border:hover {
    transform: scale(1.02);
    cursor: pointer;
}

.preview-link:hover {
    color: var(--link-hover-color) !important;
}
</style>
