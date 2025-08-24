<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Reklam Raporları - Pozitif E-Ticaret</title>
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
                        <li class="active">Reklam Raporları</li>
                    </ol>
                </div>
                <div class="section-body contain-lg">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-head">
                                    <div class="tools">
                                        <div class="btn-group">
                                            <button id="refreshAdsDataBtn" class="btn btn-success btn-sm">
                                                <i class="fa fa-refresh"></i> Verileri Yenile
                                            </button>
                                        </div>
                                    </div>
                                    <header>📈 Google Ads Raporları</header>
                                </div>
                                <div class="card-body">
                                    <!-- Google Bağlantı Durumu -->
                                    <div class="row" id="adsConnectionStatus">
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <strong>⚠️ Google Ads Bağlantısı:</strong> 
                                                Reklam raporlarını görüntülemek için önce Google hesabınızı bağlamanız gerekiyor.
                                                <button id="googleAdsConnectBtn" class="btn btn-primary btn-sm ml-2">
                                                    <i class="fa fa-google"></i> Google Hesabını Bağla
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Metrik Kartları -->
                                    <div class="row" id="adsMetricsSection" style="display: none;">
                                        <div class="col-md-12">
                                            <h4 class="mb-3">📊 Son 30 Günün Reklam Özeti 
                                                <small class="text-muted" id="lastAdsUpdate"></small>
                                            </h4>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-info no-margin">
                                                        <strong class="pull-right text-success text-lg" id="totalAdCost">0.00 TL</strong>
                                                        <strong class="text-xl">Toplam Reklam Maliyeti</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-warning no-margin">
                                                        <strong class="pull-right text-success text-lg" id="totalAdConversions">0</strong>
                                                        <strong class="text-xl">Toplam Dönüşüm</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-success no-margin">
                                                        <strong class="pull-right text-success text-lg" id="totalAdClicks">0</strong>
                                                        <strong class="text-xl">Toplam Tıklama</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card">
                                                <div class="card-body no-padding">
                                                    <div class="alert alert-callout alert-danger no-margin">
                                                        <strong class="pull-right text-success text-lg" id="averageCPC">0.00 TL</strong>
                                                        <strong class="text-xl">Ortalama CPC</strong><br/>
                                                        <span class="opacity-50">Son 30 gün</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Grafikler Bölümü -->
                                    <div class="row" id="adsChartsSection" style="display: none;">
                                        <div class="col-md-12">
                                            <h4 class="mb-3">📈 Detaylı Reklam Analizi</h4>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-head">
                                                    <header>Reklam Maliyeti ve Dönüşüm Grafiği</header>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="adsChart" height="150"></canvas>
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
    <script>$('#adsreportsphp').addClass('active');
        $(document).ready(function() {
            
            
            let adsChart;
            let isAdsConnected = false;

            // Sayfa yüklendiğinde OAuth sonuçlarını kontrol et
            checkOAuthResult();
            
            // OAuth sonuçlarını kontrol et (URL parametrelerinden)
            function checkOAuthResult() {
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('oauth_status');
                const message = urlParams.get('oauth_message');
                
                if (status && message) {
                    const decodedMessage = decodeURIComponent(message);
                    
                    if (status === 'success') {
                        // Başarılı bağlantı sonrası durum kontrol et
                        setTimeout(function() {
                            checkAdsConnectionStatus();
                        }, 1000);
                    } else if (status === 'setup') {
                        // Kurulum gerekli durumunu göster
                        showAdsSetupNeededState({
                            status: 'setup_needed',
                            message: decodedMessage
                        });
                        // Customer seçim arayüzünü göster
                        setTimeout(function() {
                            showCustomerSelection();
                        }, 1000);
                    } else if (status === 'error') {
                        showError(decodedMessage);
                    }
                    
                    // URL'den parametreleri temizle
                    if (window.history.replaceState) {
                        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({path: cleanUrl}, '', cleanUrl);
                    }
                } else {
                    // Normal bağlantı durumu kontrolü
                    checkAdsConnectionStatus();
                }
            }
            
            function checkAdsConnectionStatus() {
                console.log('DEBUG: checkAdsConnectionStatus called');
                $.ajax({
                    url: '/?/webservice/google/get/checkAdsConnection',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('DEBUG: checkAdsConnection response:', response);
                        console.log('DEBUG: ads_customer_id value:', response.ads_customer_id);
                        console.log('DEBUG: ads_customer_id type:', typeof response.ads_customer_id);
                        
                        if (response.status === 'connected' && response.ads_customer_id && response.ads_customer_id !== null && response.ads_customer_id !== '') {
                            console.log('DEBUG: Showing connected state');
                            showAdsConnectedState(response);
                        } else if (response.status === 'connected') {
                            console.log('DEBUG: Showing setup needed state - ads_customer_id is:', response.ads_customer_id);
                            showAdsSetupNeededState(response);
                        } else {
                            console.log('DEBUG: Showing disconnected state');
                            showAdsDisconnectedState();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('DEBUG: checkAdsConnectionStatus error:', { xhr: xhr, status: status, error: error });
                        showAdsDisconnectedState();
                    }
                });
            }

            function showAdsConnectedState(response) {
                console.log('DEBUG: showAdsConnectedState called with response:', response);
                isAdsConnected = true;
                const customerName = response.ads_customer_id || 'Bilinmeyen Customer';
                $('#adsConnectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <strong>✅ Google Ads Bağlantısı Başarılı:</strong> 
                            Reklam verileri başarıyla alınıyor.
                            <br><small><strong>Ads Customer ID:</strong> ${customerName}</small>
                            <div class="mt-2">
                                <button id="disconnectAdsBtn" class="btn btn-warning btn-sm">
                                    <i class="fa fa-unlink"></i> Bağlantıyı Kes
                                </button>
                                <button id="changeCustomerBtn" class="btn btn-info btn-sm">
                                    <i class="fa fa-exchange"></i> Customer Değiştir
                                </button>
                            </div>
                        </div>
                    </div>
                `);
                $('#adsMetricsSection, #adsChartsSection').show();
                
                console.log('DEBUG: Ads Connected state UI updated, calling fetchAdsReportData');
                fetchAdsReportData();
            }
            
            function showAdsSetupNeededState(response) {
                isAdsConnected = false;
                let buttonsHtml = `
                    <button id="selectCustomerFromSetupBtn" class="btn btn-primary btn-sm ml-2">
                        <i class="fa fa-cogs"></i> Ads Customer Seç
                    </button>
                    <button id="disconnectAdsBtn" class="btn btn-warning btn-sm ml-2">
                        <i class="fa fa-unlink"></i> Bağlantıyı Kes
                    </button>
                `;

                $('#adsConnectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <strong>⚠️ Kurulum Gerekli:</strong> 
                            Google hesabı bağlı ancak Ads customer hesabı seçilmemiş.
                            ${buttonsHtml}
                        </div>
                    </div>
                `);
                $('#adsMetricsSection, #adsChartsSection').hide();
            }

            function showAdsDisconnectedState() {
                isAdsConnected = false;
                $('#adsConnectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <strong>⚠️ Google Ads Bağlantısı:</strong> 
                            Reklam raporlarını görüntülemek için önce Google hesabınızı bağlamanız gerekiyor.
                            <button id="googleAdsConnectBtn" class="btn btn-primary btn-sm ml-2">
                                <i class="fa fa-google"></i> Google Hesabını Bağla
                            </button>
                        </div>
                    </div>
                `);
                $('#adsMetricsSection, #adsChartsSection').hide();
            }
            
            function fetchAdsReportData() {
                if (!isAdsConnected) return;
                
                console.log('DEBUG: fetchAdsReportData called, isAdsConnected:', isAdsConnected);
                
                // Loading göster
                showLoading();
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminReportsController.php',
                    type: 'POST',
                    data: { action: 'getAdsReportData' },
                    dataType: 'json',
                    success: function(response) {
                        console.log('DEBUG: AJAX success response (Ads):', response);
                        hideLoading();
                        if (response.status === 'success') {
                            console.log('DEBUG: Response data (Ads):', response.data);
                            if (response.data && response.data.length > 0) {
                                updateAdsCharts(response.data);
                                updateAdsMetricCards(response.data);
                            } else {
                                showInfoMessage('Seçilen tarih aralığında reklam verisi bulunamadı. Demo veri görüntüleniyor.');
                                // Demo veri oluştur
                                const demoData = generateAdsDemoData();
                                updateAdsCharts(demoData);
                                updateAdsMetricCards(demoData);
                            }
                        } else {
                            console.log('DEBUG: Response error (Ads):', response.message);
                            if (response.message && response.message.includes('Customer ID')) {
                                showError('Google Ads müşteri hesabı bulunamadı. Lütfen önce Ads customer hesabını seçin.');
                            } else if (response.message && response.message.includes('OAuth')) {
                                showError('Google hesabı bağlantısı geçersiz. Lütfen yeniden giriş yapın.');
                            } else {
                                showError('Reklam verileri alınamadı: ' + response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('DEBUG: AJAX error (Ads):', { xhr: xhr, status: status, error: error });
                        console.log('DEBUG: AJAX responseText (Ads):', xhr.responseText);
                        hideLoading();
                        
                        if (xhr.status === 403) {
                            showError('Erişim reddedildi. Google Ads hesabınızın yetkileri kontrol edin.');
                        } else if (xhr.status === 401) {
                            showError('Kimlik doğrulama hatası. Lütfen Google hesabınızı yeniden bağlayın.');
                        } else if (xhr.status === 0) {
                            showError('Bağlantı hatası. İnternet bağlantınızı kontrol edin.');
                        } else {
                            showError('Reklam verileri alınırken bir sunucu hatası oluştu (HTTP ' + xhr.status + ').');
                        }
                    }
                });
            }

            function showLoading() {
                $('#adsMetricsSection .alert').each(function() {
                    $(this).find('.pull-right').html('<i class="fa fa-spinner fa-spin"></i>');
                });
            }

            function hideLoading() {
                // Loading gizlenir, updateAdsMetricCards tarafından güncellenir
            }

            function showError(message) {
                $('#adsConnectionStatus').html(`
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

            function showInfoMessage(message) {
                $('#adsConnectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>ℹ️ Bilgi:</strong> ${message}
                        </div>
                    </div>
                `);
            }

            function generateAdsDemoData() {
                const demoData = [];
                
                for (let i = 29; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    const dateStr = date.toISOString().split('T')[0];
                    
                    // Basit demo veriler
                    const baseCost = 100;
                    const baseConversions = 5;
                    const baseClicks = 50;
                    
                    // Hafta sonları daha düşük
                    const dayOfWeek = date.getDay();
                    let multiplier = 1;
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        multiplier = 0.6;
                    }
                    
                    const cost = Math.round((baseCost * multiplier * (0.7 + Math.random() * 0.6)) * 100) / 100;
                    const conversions = Math.round(baseConversions * multiplier * (0.5 + Math.random() * 1));
                    const clicks = Math.round(baseClicks * multiplier * (0.7 + Math.random() * 0.6));
                    const impressions = Math.round(clicks * (8 + Math.random() * 12)); // CTR %4-10 arası
                    
                    demoData.push({
                        'summary_date': dateStr,
                        'total_ad_cost': cost,
                        'total_ad_conversions': conversions,
                        'total_ad_clicks': clicks,
                        'total_ad_impressions': impressions
                    });
                }
                
                return demoData;
            }

            function updateAdsCharts(data) {
                const labels = data.map(item => item.summary_date);
                const costs = data.map(item => parseFloat(item.total_ad_cost || 0));
                const conversions = data.map(item => parseInt(item.total_ad_conversions || 0));

                adsChart.data.labels = labels;
                adsChart.data.datasets[0].data = costs;
                adsChart.data.datasets[1].data = conversions;
                adsChart.update();
            }

            function updateAdsMetricCards(data) {
                console.log('DEBUG: updateAdsMetricCards called with data:', data);
                
                let totalCost = 0;
                let totalConversions = 0;
                let totalClicks = 0;
                let totalImpressions = 0;

                if (data && data.length > 0) {
                    data.forEach(item => {
                        totalCost += parseFloat(item.total_ad_cost || 0);
                        totalConversions += parseInt(item.total_ad_conversions || 0);
                        totalClicks += parseInt(item.total_ad_clicks || 0);
                        totalImpressions += parseInt(item.total_ad_impressions || 0);
                    });
                }

                console.log('DEBUG: Calculated totals - Cost:', totalCost, 'Conversions:', totalConversions, 'Clicks:', totalClicks, 'Impressions:', totalImpressions);
                
                // Metrik kartlarını güncelle
                $('#totalAdCost').text(totalCost.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL');
                $('#totalAdConversions').text(totalConversions.toLocaleString());
                $('#totalAdClicks').text(totalClicks.toLocaleString());
                
                // Ortalama CPC hesapla
                const averageCPC = totalClicks > 0 ? (totalCost / totalClicks) : 0;
                $('#averageCPC').text(averageCPC.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL');
                
                // Son güncelleme zamanını göster
                const now = new Date();
                const timeStr = now.toLocaleTimeString('tr-TR');
                $('#lastAdsUpdate').text(`Son güncelleme: ${timeStr}`);
                
                console.log('DEBUG: Ads Metrics updated, last update time:', timeStr);
            }

            // Ads customer seçim arayüzünü göster
            window.showCustomerSelection = function() {
                $('#adsConnectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>🔄 Google Ads Customers yükleniyor...</strong>
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
                            showCustomersList(response.customers);
                        } else {
                            const errorMessage = response.message || 'Customer listesi alınamadı';
                            $('#adsConnectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <strong>⚠️ Ads Customer Bulunamadı:</strong> 
                                        ${errorMessage}
                                        <br><small>Google Ads'te customer oluşturun veya erişim izinlerini kontrol edin.</small>
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#adsConnectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger"><strong>❌ Hata:</strong> Customers listesi alınamadı.
                                        <button id="retryCustomersBtn" class="btn btn-warning btn-sm ml-2">
                                            <i class="fa fa-retry"></i> Tekrar Dene
                                        </button>
                                </div>
                            </div>
                        `);
                    }
                });
            }

            // Customers listesini göster
            window.showCustomersList = function(customers) {
                let customersHtml = `
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>📊 Google Ads Customer Seçin:</strong> (${customers.length} customer bulundu)
                            <div class="mt-3">
                `;
                
                customers.forEach(function(customer, index) {
                    customersHtml += `
                        <div class="customer-item" style="border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px; background: #f9f9f9;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>${customer.descriptive_name}</h5>
                                    <p class="mb-1"><strong>Customer ID:</strong> ${customer.id}</p>
                                    <p class="mb-1"><strong>Para Birimi:</strong> ${customer.currency_code}</p>
                                    <small class="text-muted">Durum: ${customer.status}</small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-success btn-sm select-customer-btn" 
                                            data-customer-id="${customer.id}" 
                                            data-customer-name="${customer.descriptive_name}">
                                        <i class="fa fa-check"></i> Bu Customer'ı Seç
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
                
                $('#adsConnectionStatus').html(customersHtml);
            }
            
            // Customer seçimi yapıldığında
            window.selectCustomer = function(customerId, customerName) {
                $('#adsConnectionStatus').html(`
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>💾 Customer kaydediliyor:</strong> ${customerName}
                        </div>
                    </div>
                `);
                
                $.ajax({
                    url: '/?/webservice/google/get/saveAdsCustomer',
                    type: 'POST',
                    data: {
                        ads_customer_id: customerId,
                        customer_name: customerName
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#adsConnectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <strong>✅ Customer Kaydedildi:</strong> ${customerName}
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
                            $('#adsConnectionStatus').html(`
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <strong>❌ Kaydetme Hatası:</strong> ${response.message}
                                    </div>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#adsConnectionStatus').html(`
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    <strong>❌ Hata:</strong> Customer kaydedilemedi.
                                </div>
                            </div>
                        `);
                    }
                });
            }

            // Event listeners
            $(document).on('click', '#googleAdsConnectBtn', function(e) {
                e.preventDefault();
                
                // Session'a hangi sayfadan geldiğini kaydet
                $.ajax({
                    url: '/?/webservice/google/get/setOAuthReturnPage',
                    type: 'POST',
                    data: { return_page: 'AdsReports.php' },
                    success: function() {
                        // Session kaydedildikten sonra auth URL'sini al
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
                    }
                });
            });

            $(document).on('click', '#refreshAdsDataBtn', function(e) {
                e.preventDefault();
                fetchAdsReportData();
            });

            $(document).on('click', '#disconnectAdsBtn', function(e) {
                e.preventDefault();
                if (confirm('Google bağlantısını kesmek istediğinizden emin misiniz?')) {
                    $.ajax({
                        url: '/?/webservice/google/get/disconnect',
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            showAdsDisconnectedState();
                        },
                        error: function() {
                            showError('Bağlantı kesilirken hata oluştu.');
                        }
                    });
                }
            });
            
            $(document).on('click', '#changeCustomerBtn, #selectCustomerFromSetupBtn', function(e) {
                e.preventDefault();
                showCustomerSelection();
            });

            $(document).on('click', '#retryCustomersBtn', function(e) {
                e.preventDefault();
                showCustomerSelection();
            });

            $(document).on('click', '.select-customer-btn', function(e) {
                e.preventDefault();
                const customerId = $(this).data('customer-id');
                const customerName = $(this).data('customer-name');
                selectCustomer(customerId, customerName);
            });

            $(document).on('click', '#refreshAfterSaveBtn', function(e) {
                e.preventDefault();
                location.reload();
            });

            $(document).on('click', '#reloadPageBtn', function(e) {
                e.preventDefault();
                location.reload();
            });
            
            // Chart initialization
            function initializeAdsCharts() {
                const adsCtx = document.getElementById('adsChart');
                
                if (adsCtx) {
                    adsChart = new Chart(adsCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Reklam Maliyeti',
                                data: [],
                                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                borderColor: 'rgba(255, 159, 64, 1)',
                                borderWidth: 2,
                                fill: true
                            },{
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
            }

            // Initialize charts when page loads
            initializeAdsCharts();
        });
    </script>
</body>
</html>