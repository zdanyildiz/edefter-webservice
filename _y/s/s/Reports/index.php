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
                                        <div class="btn-group">
                                            <a id="googleConnectBtn" href="#" class="btn btn-primary">Google Hesabını Bağla</a>
                                        </div>
                                    </div>
                                    <header>Genel Bakış</header>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Metrik Kartları -->
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
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="card">
                                                <div class="card-head">
                                                    <header>Oturum ve Kullanıcı Grafiği</header>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="sessionChart" height="150"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
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

            function fetchReportData() {
                $.ajax({
                    url: '/App/Controller/Admin/AdminReportsController.php',
                    type: 'POST',
                    data: { action: 'getReportData' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            updateCharts(response.data);
                            updateMetricCards(response.data);
                        } else {
                            console.error('Rapor verileri alınamadı: ' + response.message);
                        }
                    },
                    error: function() {
                        console.error('Rapor verileri alınırken bir sunucu hatası oluştu.');
                    }
                });
            }

            function updateCharts(data) {
                const labels = data.map(item => item.summary_date);
                const sessions = data.map(item => item.sessions);
                const users = data.map(item => item.users);
                const totalAdCost = data.map(item => parseFloat(item.total_ad_cost));
                const totalAdConversions = data.map(item => item.total_ad_conversions);

                // Oturum ve Kullanıcı Grafiği
                sessionChart.data.labels = labels;
                sessionChart.data.datasets[0].data = sessions;
                sessionChart.data.datasets[1].data = users;
                sessionChart.update();

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

                data.forEach(item => {
                    totalSessions += parseInt(item.sessions, 10);
                    totalUsers += parseInt(item.users, 10);
                    totalAdCost += parseFloat(item.total_ad_cost);
                    totalAdConversions += parseInt(item.total_ad_conversions, 10);
                });

                $('.alert-info strong.pull-right').text(totalSessions);
                $('.alert-warning strong.pull-right').text(totalUsers);
                $('.alert-danger strong.pull-right').text(totalAdCost.toFixed(2));
                $('.alert-success strong.pull-right').text(totalAdConversions);
            }

            $('#googleConnectBtn').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '/webservice/google/get/authUrl', // Yeni URL
                    type: 'GET', // GET isteği
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            window.location.href = response.authUrl;
                        } else {
                            alert('Hata: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Google bağlantı URL\'si alınırken bir hata oluştu.');
                    }
                });
            });

            var sessionCtx = document.getElementById('sessionChart').getContext('2d');
            var sessionChart = new Chart(sessionCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Oturumlar',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },{
                        label: 'Kullanıcılar',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                }
            });

            var trafficCtx = document.getElementById('trafficSourceChart').getContext('2d');
            var trafficChart = new Chart(trafficCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                }
            });

            fetchReportData(); // Sayfa yüklendiğinde verileri çek
        });
    </script>
</body>
</html>
