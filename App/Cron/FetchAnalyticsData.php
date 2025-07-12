<?php
// Bu dosya sunucuda cron job olarak günde bir kez çalıştırılmalıdır.
require_once(dirname(__FILE__) . '/../../App/Controller/Admin/AdminGlobal.php');
require_once(ROOT . 'vendor/autoload.php');
require_once(ROOT . 'App/Model/Admin/AdminReports.php');

/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$reportsModel = new AdminReports($db);
$allCredentials = $reportsModel->getAllCredentials();

// .env dosyasından kimlik bilgilerini yükle
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();

$googleClientId = $_ENV['GOOGLE_CLIENT_ID'];
$googleClientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];

foreach ($allCredentials as $credential) {
    $clientId = $credential['client_id'];
    $refreshToken = $credential['google_refresh_token'];
    $gaPropertyId = $credential['ga_property_id'];
    $adsCustomerId = $credential['ads_customer_id'];

    if (empty($refreshToken)) {
        error_log("Client {$clientId} için refresh token bulunamadı. Atlanıyor.");
        continue;
    }

    $client = new Google_Client();
    $client->setClientId($googleClientId);
    $client->setClientSecret($googleClientSecret);
    $client->fetchAccessTokenWithRefreshToken($refreshToken);

    if (isset($client->getAccessToken()['error'])) {
        error_log("Client {$clientId} için access token yenilenemedi: " . $client->getAccessToken()['error_description']);
        continue;
    }

    $today = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime('-30 days'));

    $summaryData = [
        'client_id' => $clientId,
        'summary_date' => $today,
        'sessions' => 0,
        'users' => 0,
        'new_users' => 0,
        'total_ad_cost' => 0.00,
        'total_ad_conversions' => 0
    ];

    // Google Analytics Data API
    if (!empty($gaPropertyId)) {
        try {
            $analytics = new Google_Service_AnalyticsData($client);
            $response = $analytics->properties->runReport(
                'properties/' . $gaPropertyId,
                new Google_Service_AnalyticsData_RunReportRequest([
                    'dateRanges' => [
                        new Google_Service_AnalyticsData_DateRange(['startDate' => $startDate, 'endDate' => $today])
                    ],
                    'metrics' => [
                        new Google_Service_AnalyticsData_Metric(['name' => 'sessions']),
                        new Google_Service_AnalyticsData_Metric(['name' => 'totalUsers']),
                        new Google_Service_AnalyticsData_Metric(['name' => 'newUsers'])
                    ]
                ])
            );

            if (!empty($response->getRows())) {
                $row = $response->getRows()[0];
                $summaryData['sessions'] = (int)$row->getMetricValues()[0]->getValue();
                $summaryData['users'] = (int)$row->getMetricValues()[1]->getValue();
                $summaryData['new_users'] = (int)$row->getMetricValues()[2]->getValue();
            }
        } catch (Exception $e) {
            error_log("Analytics data fetch failed for client {$clientId} (GA Property ID: {$gaPropertyId}): " . $e->getMessage());
        }
    }

    // Google Ads API (Basitleştirilmiş Örnek - Gerçek entegrasyon daha karmaşıktır)
    // Google Ads API için farklı bir kimlik doğrulama akışı ve servis hesabı gerekebilir.
    // Bu örnek sadece yer tutucudur.
    if (!empty($adsCustomerId)) {
        try {
            // Google Ads API entegrasyonu burada yer alacak
            // Örneğin:
            // $adsService = new Google_Service_Adwords($client);
            // $adsData = $adsService->reports->query(...);
            
            // Şimdilik sabit değerler atayalım
            $summaryData['total_ad_cost'] = 150.75;
            $summaryData['total_ad_conversions'] = 10;

        } catch (Exception $e) {
            error_log("Ads data fetch failed for client {$clientId} (Ads Customer ID: {$adsCustomerId}): " . $e->getMessage());
        }
    }

    // Verileri veritabanına kaydet
    $reportsModel->saveDailySummary($summaryData);
    error_log("Client {$clientId} için günlük özet verileri kaydedildi.");
}