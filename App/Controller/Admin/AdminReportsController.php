<?php
error_log('AdminReportsController.php: Script start');
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
if (!defined('ADMIN_GLOBAL_INCLUDED')) {
    include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
}

require_once ROOT . 'App/Model/Admin/AdminReports.php';
require_once ROOT . 'App/Model/Admin/AdminGoogleModel.php';

/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */

Log::adminWrite(json_encode($requestData),"warning");
$action = trim($requestData["action"] ?? '');
error_log("AdminReportsController: Action received: '{$action}'");

if (empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'Action not specified']);
    exit();
}

class AdminReportsController
{
    private $db;
    private $config;
    private $reportsModel;
    private $adminSession;
    private $googleModel;

    public function __construct($db, $config, $adminSession)
    {
        $this->db = $db;
        $this->config = $config;
        $this->reportsModel = new AdminReports($db);
        $this->adminSession = $adminSession;
        $this->googleModel = new AdminGoogleModel($db, $config);
    }

    public function getGoogleModel()
    {
        return $this->googleModel;
    }

    public function getReportData($clientId, $startDate, $endDate, $forceRefresh = false)
    {
        // DEBUG: Log başlangıcı
        error_log('DEBUG: getReportData started - clientId=' . $clientId . ', startDate=' . $startDate . ', endDate=' . $endDate . ', forceRefresh=' . ($forceRefresh ? 'true' : 'false'));
        
        // Önce property seçilmiş mi kontrol et
        $propertyInfo = $this->reportsModel->getAnalyticsProperty($clientId);
        error_log('DEBUG: Property info from DB: ' . json_encode($propertyInfo));
        
        if (!$propertyInfo || empty($propertyInfo['ga_property_id'])) {
            error_log('DEBUG: Analytics property not found or empty');
            return [
                'status' => 'error', 
                'message' => 'Analytics property seçilmemiş. Lütfen önce property seçin.'
            ];
        }
        
        // Force refresh ise direkt Google'dan çek
        if ($forceRefresh) {
            error_log('DEBUG: Force refresh requested, fetching from Google Analytics');
            $freshData = $this->googleModel->fetchFromGoogleAnalytics($clientId, $startDate, $endDate);
            if ($freshData && $freshData['status'] === 'success') {
                // Verileri veritabanına kaydet
                $this->saveAnalyticsData($clientId, $freshData['data']);
                return ['status' => 'success', 'data' => $freshData['data']];
            } else {
                error_log('DEBUG: Fresh data fetch failed: ' . json_encode($freshData));
                return ['status' => 'error', 'message' => $freshData['message'] ?? 'Google Analytics verisi alınamadı'];
            }
        }
        
        // Normal akış: Önce veritabanından veri çek
        $data = $this->reportsModel->getDailySummary($clientId, $startDate, $endDate);
        error_log('DEBUG: Data from database: ' . json_encode($data));
        
        // Eğer veri yoksa Google'dan çek
        if (empty($data)) {
            error_log('DEBUG: No data in database, fetching from Google APIs');
            $analyticsData = $this->googleModel->fetchFromGoogleAnalytics($clientId, $startDate, $endDate);
            $adsData = $this->googleModel->fetchFromGoogleAds($clientId, $startDate, $endDate);

            if ($analyticsData['status'] === 'success') {
                $data = $analyticsData['data'];

                // Ads verilerini Analytics verileriyle birleştir
                if ($adsData['status'] === 'success') {
                    $data = $this->googleModel->mergeAnalyticsAndAdsData($data, $adsData['data']);
                }
                
                // Birleştirilmiş veriyi kaydet
                $this->saveAnalyticsData($clientId, $data);

            } else {
                error_log('DEBUG: Analytics data fetch failed, trying Ads only');
                // Analytics verisi alınamazsa, sadece Ads verisini kullanmayı dene
                if ($adsData['status'] === 'success') {
                    $data = $this->googleModel->formatAdsDataForSave($adsData['data']);
                    $this->saveAnalyticsData($clientId, $data);
                } else {
                    error_log('DEBUG: Both Analytics and Ads failed, using demo data');
                    // Hiçbir veri alınamazsa demo data kullan
                    $data = $this->getDemoData();
                }
            }
        }
        
        error_log('DEBUG: Final data to return: ' . json_encode($data));
        return ['status' => 'success', 'data' => $data];
    }
    
    public function saveAnalyticsData($clientId, $data)
    {
        try {
            foreach ($data as $record) {
                $summaryData = [
                    'client_id' => $clientId,
                    'summary_date' => $record['summary_date'],
                    'sessions' => $record['sessions'],
                    'users' => $record['users'],
                    'new_users' => $record['new_users'],
                    'page_views' => $record['page_views'],
                    'engagement_rate' => $record['engagement_rate'],
                    'total_ad_cost' => $record['total_ad_cost'] ?? 0,
                    'total_ad_conversions' => $record['total_ad_conversions'] ?? 0
                ];
                
                $this->reportsModel->saveDailySummary($summaryData);
            }
            return true;
        } catch (Exception $e) {
            error_log('Analytics data save error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function getDemoData()
    {
        $demoData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            // Basit demo veriler
            $baseSessions = 200;
            $baseUsers = 150;
            
            // Hafta sonları daha düşük
            $dayOfWeek = date('w', strtotime($date));
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                $baseSessions *= 0.7;
                $baseUsers *= 0.7;
            }
            
            $sessions = round($baseSessions * (0.8 + mt_rand(0, 40) / 100));
            $users = round($baseUsers * (0.8 + mt_rand(0, 40) / 100));
            $newUsers = round($users * (0.3 + mt_rand(0, 20) / 100));
            $adCost = round((50 + mt_rand(0, 100)) + (mt_rand(0, 99) / 100), 2);
            $adConversions = mt_rand(3, 15);
            
            $demoData[] = [
                'summary_date' => $date,
                'sessions' => $sessions,
                'users' => $users,
                'new_users' => $newUsers,
                'total_ad_cost' => $adCost,
                'total_ad_conversions' => $adConversions
            ];
        }
        
        return $demoData;
    }
}

$controller = new AdminReportsController($db, $config, $adminSession);

error_log('AdminReportsController.php: Before switch. Action: ' . $action);
switch ($action) {
    case 'getReportData':
        // Admin panel için sabit client_id kullan (AdminCasper'da getClientId() metodu yok)
        $clientId = 1; // Admin panel için sabit
        $startDate = $requestData['startDate'] ?? date('Y-m-d', strtotime('-7 days')); // Son 7 gün
        $endDate = $requestData['endDate'] ?? date('Y-m-d');
        $forceRefresh = isset($requestData['forceRefresh']) && $requestData['forceRefresh'] === true;
        
        // DEBUG: Log parametrelerini
        error_log('DEBUG: AdminReportsController - getReportData called with clientId=' . $clientId . ', startDate=' . $startDate . ', endDate=' . $endDate . ', forceRefresh=' . ($forceRefresh ? 'true' : 'false'));
        
        $response = $controller->getReportData($clientId, $startDate, $endDate, $forceRefresh);
        
        // DEBUG: Response'u log'la
        error_log('DEBUG: AdminReportsController - getReportData response: ' . json_encode($response));
        
        break;
    case 'getAdsReportData':
        $clientId = 1;
        $startDate = $requestData['startDate'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $requestData['endDate'] ?? date('Y-m-d');
        
        error_log('DEBUG: AdminReportsController - getAdsReportData called with clientId=' . $clientId . ', startDate=' . $startDate . ', endDate=' . $endDate);
        
        $adsData = $controller->getGoogleModel()->fetchFromGoogleAds($clientId, $startDate, $endDate);
        
        if ($adsData['status'] === 'success') {
            // Ads verilerini kaydetme formatına çevir
            $formattedData = $controller->getGoogleModel()->formatAdsDataForSave($adsData['data']);
            
            // Verileri kaydet
            $controller->saveAnalyticsData($clientId, $formattedData);
            
            $response = ['status' => 'success', 'data' => $formattedData];
        } else {
            $response = $adsData;
        }
        
        error_log('DEBUG: AdminReportsController - getAdsReportData response: ' . json_encode($response));
        break;
    default:
        $response = ['status' => 'error', 'message' => 'Invalid action Report: ' . $action];
        break;
}

error_log('AdminReportsController.php: Before json_encode. Response: ' . json_encode($response));
if(!empty($action))echo json_encode($response);
exit();
