<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Analitik Raporlar - Pozitif E-Ticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
</head>
<body class="menubar-hoverable header-fixed ">
    <?php require_once(ROOT."/_y/s/b/header.php");?>
    <div id="base">
        <div id="content">
            <section>
                <div class="section-header">
                    <ol class="breadcrumb">
                        <li class="active">Analitik Raporlar</li>
                    </ol>
                </div>
                <div class="section-body contain-lg">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                <div class="card-head">
                    <div class="tools">
                        <div class="btn-group">                                    <button id="refreshDataBtn" class="btn btn-success btn-sm" style="display: none;">
                                        <i class="fa fa-refresh"></i> Verileri Yenile
                                    </button>
                                    <button id="fetchRealDataBtn" class="btn btn-warning btn-sm" style="display: none;">
                                        <i class="fa fa-download"></i> Google'dan Veri Çek
                                    </button>
                                    <button id="testConnectionBtn" class="btn btn-info btn-sm">
                                        <i class="fa fa-plug"></i> Bağlantıyı Test Et
                                    </button>
                        </div>
                    </div>
                    <header>🎯 Google Analytics & Ads Raporları</header>
                </div>
                                <div class="card-body">
                                    <!-- Google Bağlantı Durumu -->
                                    <div class="row" id="connectionStatus">
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <strong>⚠️ Google Analytics Bağlantısı:</strong> 
                                                Raporları görüntülemek için önce Google hesabınızı bağlamanız gerekiyor.
                                                <button id="googleConnectBtn" class="btn btn-primary btn-sm ml-2">
                                                    <i class="fa fa-google"></i> Google Hesabını Bağla
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Metrik Kartları -->
                                    <div class="row" id="metricsSection" style="display: none;">
                                        <div class="col-md-12">
                                            <h4 class="mb-3">📊 Son 30 Günün Özeti 
                                                <small class="text-muted" id="lastUpdate"></small>
                                            </h4>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-info no-margin">
                                                        <strong class="pull-right text-success text-lg">0</strong>
                                                        <strong class="text-xl">Toplam Oturum</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-warning no-margin">
                                                        <strong class="pull-right text-success text-lg">0</strong>
                                                        <strong class="text-xl">Toplam Kullanıcı</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-danger no-margin">
                                                        <strong class="pull-right text-success text-lg">0</strong>
                                                        <strong class="text-xl">Reklam Harcaması</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-success no-margin">
                                                        <strong class="pull-right text-success text-lg">0</strong>
                                                        <strong class="text-xl">Toplam Dönüşüm</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Grafikler Bölümü -->
                                    <div class="row" id="chartsSection" style="display: none;">
                                        <div class="col-md-12">
                                            <h4 class="mb-3">📈 Detaylı Analiz</h4>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-head">
                                                    <header>Oturum ve Kullanıcı Grafiği</header>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="sessionChart" height="150"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-head">
                                                    <header>Reklam Harcaması Grafiği</header>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="adCostChart" height="150"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-head">
                                                    <header>Dönüşüm Grafiği</header>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="conversionChart" height="150"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-head">
                                                    <header>Trafik Kaynakları</header>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="trafficSourceChart" height="150"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once(ROOT."/_y/s/b/menu.php");?>
    </div>

    <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
    <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
    <script src="/_y/assets/js/core/source/App.js"></script>
    <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
    <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
    <script src="/_y/assets/js/core/source/AppCard.js"></script>
    <script src="/_y/assets/js/core/source/AppForm.js"></script>
    <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
    <script src="/_y/assets/js/core/source/AppVendor.js"></script>
    <script src="/_y/assets/js/libs/chartjs/Chart.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#reportsphp').addClass('active');
            
            let isConnected = false;
            let sessionChart, adCostChart, conversionChart, trafficChart;

            // Sayfa yüklendiğinde bağlantı durumunu kontrol et
            checkOAuthResult();
            checkConnectionStatus();
            
            // OAuth sonuçlarını kontrol et (URL parametrelerinden)
            function checkOAuthResult() {
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('oauth_status');
                const message = urlParams.get('oauth_message');
                
                if (status && message) {
                    const decodedMessage = decodeURIComponent(message);
                    
                    if (status === 'success') {
                        $('#connectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-success">
                                    <strong>✅ Başarılı:</strong> ${decodedMessage}
                                    <button id="clearOAuthBtn" class="btn btn-secondary btn-sm ml-2">
                                        <i class="fa fa-times"></i> Temizle
                                    </button>
                                </div>
                            </div>
                        `);
                    } else if (status === 'setup') {
                        $('#connectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>🔧 Kurulum:</strong> ${decodedMessage}
                                    <button id="selectPropertyBtn" class="btn btn-primary btn-sm ml-2">
                                        <i class="fa fa-cogs"></i> Property Seç
                                    </button>
                                </div>
                            </div>
                        `);
                    } else if (status === 'error') {
                        $('#connectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    <strong>❌ Hata:</strong> ${decodedMessage}
                                    <button id="clearOAuthBtn" class="btn btn-secondary btn-sm ml-2">
                                        <i class="fa fa-times"></i> Temizle
                                    </button>
                                </div>
                            </div>
                        `);
                    }
                    
                    // URL'den parametreleri temizle (tarayıcı geçmişini karıştırmadan)
                    if (window.history.replaceState) {
                        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({path: cleanUrl}, '', cleanUrl);
                    }
                }
            }
            
            // OAuth parametrelerini temizle ve sayfayı yenile
            window.clearOAuthParams = function() {
                location.reload();
            }
            
            // Analytics property seçim arayüzünü göster
            window.showPropertySelection = function() {
                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>🔄 Analytics Properties yükleniyor...</strong>
                            <div class="progress mt-2">
                                <div class="progress-bar progress-bar-striped active" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                `);
                
                $.ajax({
                    url: '/?/webservice/google/get/getProperties',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success' && response.properties.length > 0) {
                            showPropertiesList(response.properties);
                        } else {
                            
                            $('#connectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <strong>⚠️ Analytics Property Bulunamadı:</strong> 
                                        ${response.message}
                                        <br><small>Google Analytics'te property oluşturun veya erişim izinlerini kontrol edin.</small>
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#connectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger"><strong>❌ Hata:</strong> Properties listesi alınamadı.
                                        <button id="retryPropertiesBtn" class="btn btn-warning btn-sm ml-2">
                                            <i class="fa fa-retry"></i> Tekrar Dene
                                        </button>
                                </div>
                            </div>
                        `);
                    }
                });
            }

            // Google Ads müşteri seçim arayüzünü göster
            window.showAdsCustomerSelection = function() {
                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>🔄 Google Ads Müşterileri yükleniyor...</strong>
                            <div class="progress mt-2">
                                <div class="progress-bar progress-bar-striped active" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                `);
                
                $.ajax({
                    url: '/?/webservice/google/get/getAdsCustomers',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success' && response.customers.length > 0) {
                            showAdsCustomersList(response.customers);
                        } else {
                            $('#connectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <strong>⚠️ Google Ads Müşterisi Bulunamadı:</strong> 
                                        ${response.message}
                                        <br><small>Google Ads hesabınızda müşteri bulunmuyor veya erişim izinlerini kontrol edin.</small>
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#connectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger"><strong>❌ Hata:</strong> Google Ads müşteri listesi alınamadı.
                                        <button id="retryAdsCustomersBtn" class="btn btn-warning btn-sm ml-2">
                                            <i class="fa fa-retry"></i> Tekrar Dene
                                        </button>
                                </div>
                            </div>
                        `);
                    }
                });
            }
            
            // Google Ads müşterileri listesini göster
            window.showAdsCustomersList = function(customers) {
                let customersHtml = `
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>💰 Google Ads Müşterisi Seçin:</strong> (${customers.length} müşteri bulundu)
                            <div class="mt-3">
                `;
                
                customers.forEach(function(customer, index) {
                    customersHtml += `
                        <div class="customer-item" style="border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px; background: #f9f9f9;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>${customer.descriptive_name}</h5>
                                    <p class="mb-1"><strong>ID:</strong> ${customer.id}</p>
                                    <p class="mb-1"><strong>Para Birimi:</strong> ${customer.currency_code}</p>
                                    <small class="text-muted">Zaman Dilimi: ${customer.time_zone}</small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-success btn-sm select-ads-customer-btn" 
                                            data-customer-id="${customer.id}">
                                        <i class="fa fa-check"></i> Bu Müşteriyi Seç
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                customersHtml += `
                            </div>
                        </div>
                    </div>
                `;
                
                $('#connectionStatus').html(customersHtml);
            }

            // Google Ads müşteri seçimi yapıldığında
            window.selectAdsCustomer = function(adsCustomerId) {
                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>💾 Google Ads Müşterisi kaydediliyor:</strong> ${adsCustomerId}
                        </div>
                    </div>
                `);
                
                $.ajax({
                    url: '/?/webservice/google/get/saveAdsCustomer',
                    type: 'POST',
                    data: {
                        ads_customer_id: adsCustomerId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#connectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <strong>✅ Google Ads Müşterisi Kaydedildi:</strong> ${adsCustomerId}
                                        <button id="refreshAfterSaveBtn" class="btn btn-primary btn-sm ml-2">
                                            <i class="fa fa-refresh"></i> Sayfayı Yenile
                                        </button>
                                    </div>
                                </div>
                            `);
                            
                            // 2 saniye sonra otomatik yenile
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#connectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <strong>❌ Kaydetme Hatası:</strong> ${response.message}
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#connectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    <strong>❌ Hata:</strong> Google Ads Müşterisi kaydedilemedi.
                                </div>
                            </div>
                        `);
                    }
                });
            }
            
            // Properties listesini göster
            window.showPropertiesList = function(properties) {
                let propertiesHtml = `
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>📊 Analytics Property Seçin:</strong> (${properties.length} property bulundu)
                            <div class="mt-3">
                `;
                
                properties.forEach(function(property, index) {
                    propertiesHtml += `
                        <div class="property-item" style="border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px; background: #f9f9f9;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>${property.display_name}</h5>
                                    <p class="mb-1"><strong>Hesap:</strong> ${property.account_name}</p>
                                    <p class="mb-1"><strong>URL:</strong> ${property.website_url || 'Belirtilmemiş'}</p>
                                    <small class="text-muted">Property ID: ${property.property_id}</small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-success btn-sm select-property-btn" 
                                            data-property-id="${property.property_id}" 
                                            data-property-name="${property.display_name}">
                                        <i class="fa fa-check"></i> Bu Property'yi Seç
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                propertiesHtml += `
                            </div>
                        </div>
                    </div>
                `;
                
                $('#connectionStatus').html(propertiesHtml);
            }
            
            // Property seçimi yapıldığında
            window.selectProperty = function(propertyId, propertyName) {
                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>💾 Property kaydediliyor:</strong> ${propertyName}
                        </div>
                    </div>
                `);
                
                $.ajax({
                    url: '/?/webservice/google/get/saveProperty',
                    type: 'POST',
                    data: {
                        property_id: propertyId,
                        property_name: propertyName
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#connectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <strong>✅ Property Kaydedildi:</strong> ${propertyName}
                                        <button id="refreshAfterSaveBtn" class="btn btn-primary btn-sm ml-2">
                                            <i class="fa fa-refresh"></i> Sayfayı Yenile
                                        </button>
                                    </div>
                                </div>
                            `);
                            
                            // 2 saniye sonra otomatik yenile
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#connectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <strong>❌ Kaydetme Hatası:</strong> ${response.message}
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#connectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    <strong>❌ Hata:</strong> Property kaydedilemedi.
                                </div>
                            </div>
                        `);
                    }
                });
            }

            function checkConnectionStatus() {
                $.ajax({
                    url: '/?/webservice/google/get/checkConnection',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'connected') {
                            showConnectedState(response);
                            fetchReportData();
                        } else if (response.status === 'setup_needed') {
                            showSetupNeededState(response);
                        } else {
                            showDisconnectedState();
                        }
                    },
                    error: function() {
                        showDisconnectedState();
                    }
                });
            }

            function showConnectedState(response) {
                isConnected = true;
                const propertyName = response.property_name || 'Bilinmeyen Property';
                const adsCustomerId = response.ads_customer_id || 'Yok';
                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <strong>✅ Google Bağlantısı Başarılı:</strong> 
                            Veriler başarıyla alınıyor.
                            <br><small><strong>Analytics Property:</strong> ${propertyName}</small>
                            <br><small><strong>Ads Müşteri ID:</strong> ${adsCustomerId}</small>
                            <div class="mt-2">
                                <button id="disconnectBtn" class="btn btn-warning btn-sm">
                                    <i class="fa fa-unlink"></i> Bağlantıyı Kes
                                </button>
                                <button id="changePropertyBtn" class="btn btn-info btn-sm">
                                    <i class="fa fa-exchange"></i> Property Değiştir
                                </button>
                                <button id="changeAdsCustomerBtn" class="btn btn-info btn-sm">
                                    <i class="fa fa-google"></i> Ads Müşteri Değiştir
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                $('#metricsSection, #chartsSection').show();
                $('#refreshDataBtn, #fetchRealDataBtn').show();
            }
            
            function showSetupNeededState(response) {
                isConnected = false;
                let buttonsHtml = '';
                if (response.message.includes('Analytics property seçilmemiş')) {
                    buttonsHtml += `
                        <button id="selectPropertyFromSetupBtn" class="btn btn-primary btn-sm ml-2">
                            <i class="fa fa-cogs"></i> Analytics Property Seç
                        </button>
                    `;
                }
                if (response.message.includes('Ads müşteri hesabı seçilmemiş')) {
                    buttonsHtml += `
                        <button id="selectAdsCustomerFromSetupBtn" class="btn btn-info btn-sm ml-2">
                            <i class="fa fa-google"></i> Ads Müşteri Seç
                        </button>
                    `;
                }
                buttonsHtml += `
                    <button id="disconnectBtn" class="btn btn-warning btn-sm ml-2">
                        <i class="fa fa-unlink"></i> Bağlantıyı Kes
                    </button>
                `;

                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <strong>⚠️ Kurulum Gerekli:</strong> 
                            ${response.message}
                            ${buttonsHtml}
                        </div>
                    </div>
                `);
                $('#metricsSection, #chartsSection').hide();
                $('#refreshDataBtn, #fetchRealDataBtn').hide();
            }

            function showDisconnectedState() {
                isConnected = false;
                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <strong>⚠️ Google Analytics Bağlantısı:</strong> 
                            Raporları görüntülemek için önce Google hesabınızı bağlamanız gerekiyor.
                            <button id="googleConnectBtn" class="btn btn-primary btn-sm ml-2">
                                <i class="fa fa-google"></i> Google Hesabını Bağla
                            </button>
                        </div>
                    </div>
                `);
                $('#metricsSection, #chartsSection').hide();
                $('#refreshDataBtn, #fetchRealDataBtn').hide();
            }

            function fetchReportData() {
                if (!isConnected) return;
                
                // Loading göster
                showLoading();
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminReportsController.php',
                    type: 'POST',
                    data: { action: 'getReportData' },
                    dataType: 'json',
                    success: function(response) {
                        hideLoading();
                        if (response.status === 'success') {
                            updateCharts(response.data);
                            updateMetricCards(response.data);
                        } else {
                            showError('Rapor verileri alınamadı: ' + response.message);
                        }
                    },
                    error: function() {
                        hideLoading();
                        showError('Rapor verileri alınırken bir sunucu hatası oluştu.');
                    }
                });
            }

            function showLoading() {
                $('#metricsSection .alert').each(function() {
                    $(this).find('.pull-right').html('<i class="fa fa-spinner fa-spin"></i>');
                });
            }

            function hideLoading() {
                // Loading gizlenir, updateMetricCards tarafından güncellenir
            }

            function showError(message) {
                $('#connectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <strong>❌ Hata:</strong> ${message}
                            <button id="reloadPageBtn" class="btn btn-primary btn-sm ml-2">
                                <i class="fa fa-refresh"></i> Sayfayı Yenile
                            </button>
                        </div>
                    </div>
                `);
            }

            function updateCharts(data) {
                const labels = data.map(item => item.summary_date);
                const sessions = data.map(item => item.sessions);
                const users = data.map(item => item.users);
                const totalAdCost = data.map(item => parseFloat(item.total_ad_cost));
                const totalAdConversions = data.map(item => parseInt(item.total_ad_conversions || 0, 10));

                // Oturum ve Kullanıcı Grafiği
                sessionChart.data.labels = labels;
                sessionChart.data.datasets[0].data = sessions;
                sessionChart.data.datasets[1].data = users;
                sessionChart.update();

                // Reklam Harcaması Grafiği
                adCostChart.data.labels = labels;
                adCostChart.data.datasets[0].data = totalAdCost;
                adCostChart.update();

                // Dönüşüm Grafiği
                conversionChart.data.labels = labels;
                conversionChart.data.datasets[0].data = totalAdConversions;
                conversionChart.update();

                // Trafik kaynakları için örnek veri (gerçek veri API'den gelmeli)
                // Bu kısım için API'den gelen gerçek trafik kaynağı verisi olmadığından
                // şimdilik sabit değerler kullanmaya devam edeceğiz.
                trafficChart.data.labels = ['Organik', 'Direkt', 'Referans'];
                trafficChart.data.datasets[0].data = [120, 80, 30];
                trafficChart.update();
            }

            function updateMetricCards(data) {
                let totalSessions = 0;
                let totalUsers = 0;
                let totalAdCost = 0;
                let totalAdConversions = 0;

                if (data && data.length > 0) {
                    data.forEach(item => {
                        totalSessions += parseInt(item.sessions || 0, 10);
                        totalUsers += parseInt(item.users || 0, 10);
                        totalAdCost += parseFloat(item.total_ad_cost || 0);
                        totalAdConversions += parseInt(item.total_ad_conversions || 0, 10);
                    });
                }

                $('.alert-info strong.pull-right').html(totalSessions.toLocaleString());
                $('.alert-warning strong.pull-right').html(totalUsers.toLocaleString());
                $('.alert-danger strong.pull-right').html('₺' + totalAdCost.toFixed(2));
                $('.alert-success strong.pull-right').html(totalAdConversions.toLocaleString());
                
                // Son güncelleme zamanını göster
                const now = new Date();
                const timeStr = now.toLocaleTimeString('tr-TR');
                $('#lastUpdate').text(`Son güncelleme: ${timeStr}`);
            }

            // Event listeners
            $(document).on('click', '#googleConnectBtn', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/?/webservice/google/get/authUrl',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = response.authUrl;
                        } else {
                            showError('Google bağlantı URL\'si alınamadı: ' + response.message);
                        }
                    },
                    error: function() {
                        showError('Google bağlantı URL\'si alınırken bir hata oluştu.');
                    }
                });
            });

            $(document).on('click', '#refreshDataBtn', function(e) {
                e.preventDefault();
                fetchReportData();
            });

            $(document).on('click', '#testConnectionBtn', function(e) {
                e.preventDefault();
                checkConnectionStatus();
            });

            $(document).on('click', '#disconnectBtn', function(e) {
                e.preventDefault();
                if (confirm('Google bağlantısını kesmek istediğinizden emin misiniz?')) {
                    $.ajax({
                        url: '/?/webservice/google/get/disconnect',
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            showDisconnectedState();
                        },
                        error: function() {
                            showError('Bağlantı kesilirken hata oluştu.');
                        }
                    });
                }
            });
            
            $(document).on('click', '#changePropertyBtn', function(e) {
                e.preventDefault();
                showPropertySelection();
            });

            $(document).on('click', '#changeAdsCustomerBtn', function(e) {
                e.preventDefault();
                showAdsCustomerSelection();
            });
            
            // Yeni event handler'lar
            $(document).on('click', '#clearOAuthBtn', function(e) {
                e.preventDefault();
                window.clearOAuthParams();
            });
            
            $(document).on('click', '#selectPropertyBtn, #selectPropertyFromSetupBtn', function(e) {
                e.preventDefault();
                window.showPropertySelection();
            });

            $(document).on('click', '#selectAdsCustomerBtn, #selectAdsCustomerFromSetupBtn', function(e) {
                e.preventDefault();
                window.showAdsCustomerSelection();
            });
            
            $(document).on('click', '#retryPropertiesBtn', function(e) {
                e.preventDefault();
                window.showPropertySelection();
            });

            $(document).on('click', '#retryAdsCustomersBtn', function(e) {
                e.preventDefault();
                window.showAdsCustomerSelection();
            });
            
            $(document).on('click', '.select-property-btn', function(e) {
                e.preventDefault();
                const propertyId = $(this).data('property-id');
                const propertyName = $(this).data('property-name');
                window.selectProperty(propertyId, propertyName);
            });

            $(document).on('click', '.select-ads-customer-btn', function(e) {
                e.preventDefault();
                const customerId = $(this).data('customer-id');
                window.selectAdsCustomer(customerId);
            });
            
            $(document).on('click', '#reloadPageBtn', function(e) {
                e.preventDefault();
                location.reload();
            });
            
            $(document).on('click', '#refreshAfterSaveBtn', function(e) {
                e.preventDefault();
                location.reload();
            });
            
            $(document).on('click', '#fetchRealDataBtn', function(e) {
                e.preventDefault();
                fetchRealDataFromGoogle();
            });
            
            $(document).on('click', '#refreshPageAfterDataBtn', function(e) {
                e.preventDefault();
                location.reload();
            });
            
            // Google Analytics'ten gerçek veri çek
            function fetchRealDataFromGoogle() {
                if (!isConnected) {
                    alert('Önce Google Analytics bağlantısını yapın.');
                    return;
                }
                
                // Buton durumunu değiştir
                $('#fetchRealDataBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Google\'dan Çekiliyor...');
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminReportsController.php',
                    type: 'POST',
                    data: { 
                        action: 'getReportData',
                        forceRefresh: true // Gerçek veri çekmek için
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#fetchRealDataBtn').prop('disabled', false).html('<i class="fa fa-download"></i> Google\'dan Veri Çek');
                        
                        if (response.status === 'success') {
                            // Başarı mesajı göster
                            $('#connectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <strong>✅ Gerçek Veriler Alındı:</strong> 
                                        Google Analytics\'ten ${response.data.length} günlük veri başarıyla çekildi ve kaydedildi.
                                        <button id="refreshPageAfterDataBtn" class="btn btn-primary btn-sm ml-2">
                                            <i class="fa fa-refresh"></i> Sayfayı Yenile
                                        </button>
                                    </div>
                                </div>
                            `);
                            
                            // Grafikleri güncelle
                            updateCharts(response.data);
                            updateMetricCards(response.data);
                            
                            // 3 saniye sonra otomatik yenile
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        } else {
                            alert('Hata: ' + response.message);
                        }
                    },
                    error: function() {
                        $('#fetchRealDataBtn').prop('disabled', false).html('<i class="fa fa-download"></i> Google\'dan Veri Çek');
                        alert('Google Analytics verisi çekilirken bir hata oluştu.');
                    }
                });
            }

            // Chart initialization
            function initializeCharts() {
                const sessionCtx = document.getElementById('sessionChart');
                const trafficCtx = document.getElementById('trafficSourceChart');
                const adCostCtx = document.getElementById('adCostChart');
                const conversionCtx = document.getElementById('conversionChart');
                
                if (sessionCtx) {
                    sessionChart = new Chart(sessionCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Oturumlar',
                                data: [],
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                fill: true
                            },{
                                label: 'Kullanıcılar',
                                data: [],
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                if (adCostCtx) {
                    adCostChart = new Chart(adCostCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Reklam Harcaması (₺)',
                                data: [],
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                if (conversionCtx) {
                    conversionChart = new Chart(conversionCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Dönüşümler',
                                data: [],
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                if (trafficCtx) {
                    trafficChart = new Chart(trafficCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: [],
                            datasets: [{
                                data: [],
                                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            }

            // Initialize charts when page loads
            initializeCharts();
        });
    </script>
</body>
</html>