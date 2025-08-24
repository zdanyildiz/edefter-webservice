<?php
// Bu dosya, Router tarafından çağrılan ana webservice giriş noktasıdır.
// Gelen isteği GoogleController'a yönlendirir.

// Config ve DB zaten index.php'den gelir
// Gerekli sınıfları dahil et
$rootPath = dirname(dirname(__DIR__));
require_once $rootPath . '/vendor/autoload.php';
require_once $rootPath . '/App/Model/Admin/AdminReports.php';

use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V20\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V20\Enums\CustomerStatusEnum\CustomerStatus;
use Google\Ads\GoogleAds\V20\Errors\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\V20\GoogleAdsClient;

class GoogleController
{
    private $db;
    private $config;
    private $reportsModel;
    private $rootPath;
    private $adsConfig;

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
        $this->reportsModel = new AdminReports($db);
        $this->rootPath = dirname(dirname(__DIR__));
        $this->adsConfig = json_decode(file_get_contents($this->rootPath . '/App/Config/GoogleAds.json'), true);
    }

    private function getGoogleClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Pozitif E-Ticaret Raporlama');
        $client->setScopes([
            'https://www.googleapis.com/auth/analytics.readonly',
            'https://www.googleapis.com/auth/adwords', // Google Ads API için gerekli kapsam
            Google_Service_Oauth2::USERINFO_EMAIL,
            Google_Service_Oauth2::USERINFO_PROFILE,
            'openid'
        ]);
        $client->setAuthConfig($this->rootPath . '/App/Config/GoogleOAuth.json');
        
        // HTTPS kullan (production ve local development için)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $client->setRedirectUri($protocol . '://' . $_SERVER['HTTP_HOST'] . '/?/webservice/google/get/callback');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return $client;
    }

    private function getGoogleAdsClient($refreshToken)
    {
        try {
            // Eğer client_id placeholder ise veya yoksa, GoogleOAuth.json'dan al
            $adsConfig = $this->adsConfig;
            if (empty($adsConfig['client_id']) || $adsConfig['client_id'] === 'YOUR_CLIENT_ID') {
                $oauthConfigPath = $this->rootPath . '/App/Config/GoogleOAuth.json';
                if (file_exists($oauthConfigPath)) {
                    $oauthConfig = json_decode(file_get_contents($oauthConfigPath), true);
                    $adsConfig['client_id'] = $oauthConfig['web']['client_id'] ?? '';
                    $adsConfig['client_secret'] = $oauthConfig['web']['client_secret'] ?? '';
                }
            }
            
            // OAuth2 credential oluştur
            try {
                $oauth2Credential = (new OAuth2TokenBuilder())
                    ->withClientId($adsConfig['client_id'] ?? '')
                    ->withClientSecret($adsConfig['client_secret'] ?? '')
                    ->withRefreshToken($refreshToken)
                    ->build();
            } catch (Exception $tokenError) {
                error_log('OAuth2 Token Error in Google.php: ' . $tokenError->getMessage());
                
                // Token geçersizse, veritabanından sil
                if (strpos($tokenError->getMessage(), 'invalid_grant') !== false || 
                    strpos($tokenError->getMessage(), 'expired') !== false ||
                    strpos($tokenError->getMessage(), 'revoked') !== false) {
                    return null; // Client döndürme, hata yönetimi üst seviyede
                }
                
                return null;
            }

            // Login Customer ID'yi integer'a çevir
            $loginCustomerId = null;
            if (!empty($this->adsConfig['login_customer_id'])) {
                $loginCustomerId = (int) str_replace('-', '', $this->adsConfig['login_customer_id']);
            }

            // Google Ads Client oluştur
            $googleAdsClient = (new GoogleAdsClientBuilder())
                ->withDeveloperToken($this->adsConfig['developer_token'] ?? '')
                ->withLoginCustomerId($loginCustomerId)
                ->withOAuth2Credential($oauth2Credential)
                ->build();

            return $googleAdsClient;
        } catch (GoogleAdsException $e) {
            error_log('Google Ads Client oluşturulurken hata: ' . $e->getMessage());
            return null;
        }
    }

    public function getGoogleAdsCustomers()
    {
        $clientId = 1;
        $refreshToken = $this->reportsModel->getRefreshToken($clientId);

        if (!$refreshToken) {
            return ['status' => 'error', 'message' => 'Google hesabı bağlı değil'];
        }

        try {
            $googleAdsClient = $this->getGoogleAdsClient($refreshToken);
            if (is_null($googleAdsClient)) {
                // Token süresi dolmuşsa kullanıcıyı bilgilendir
                $this->reportsModel->deleteRefreshToken($clientId);
                return ['status' => 'error', 'message' => 'OAuth token süresi dolmuş. Lütfen Google hesabınızı yeniden bağlayın.'];
            }

            $customerServiceClient = $googleAdsClient->getCustomerServiceClient();
            
            // V20 için doğru request class'ını dinamik oluştur
            try {
                // Request class'ını dinamik olarak oluştur
                $requestClassName = 'Google\\Ads\\GoogleAds\\V20\\Services\\ListAccessibleCustomersRequest';
                
                if (class_exists($requestClassName)) {
                    $request = new $requestClassName();
                    $accessibleCustomers = $customerServiceClient->listAccessibleCustomers($request);
                } else {
                    // Eğer class bulunamazsa manual oluştur
                    error_log('ListAccessibleCustomersRequest class not found, trying alternative');
                    
                    // Boş obje dene (bazı SDK versiyonlarında çalışır)
                    $request = (object)[];
                    $accessibleCustomers = $customerServiceClient->listAccessibleCustomers($request);
                }
            } catch (Exception $e) {
                error_log('Google Ads listAccessibleCustomers error: ' . $e->getMessage());
                return [
                    'status' => 'error',
                    'message' => 'Google Ads müşteri listesi alınamadı: ' . $e->getMessage(),
                    'debug' => 'Request class: ' . (class_exists($requestClassName) ? 'found' : 'not found')
                ];
            }

            $customers = [];
            if ($accessibleCustomers && $accessibleCustomers->getResourceNames()) {
                foreach ($accessibleCustomers->getResourceNames() as $customerResourceName) {
                    try {
                        // Customer ID'yi resource name'den çıkar
                        $customerId = str_replace('customers/', '', $customerResourceName);
                        
                        // Google Ads V20 SDK'da GetCustomerRequest bulunmadığı için basit bilgiler kullan
                        $customers[] = [
                            'id' => $customerId,
                            'resource_name' => $customerResourceName,
                            'descriptive_name' => 'Google Ads Customer ' . $customerId,
                            'currency_code' => 'TRY', // Varsayılan
                            'time_zone' => 'Europe/Istanbul', // Varsayılan
                            'status' => 'ENABLED' // Varsayılan
                        ];
                    } catch (Exception $customerError) {
                        // Bu customer'ı atla, devam et
                        error_log('Customer bilgisi alınamadı: ' . $customerResourceName . ' - ' . $customerError->getMessage());
                        continue;
                    }
                }
            }

            return ['status' => 'success', 'customers' => $customers];

        } catch (GoogleAdsException $e) {
            return [
                'status' => 'error',
                'message' => 'Google Ads müşterileri alınamadı: ' . $e->getMessage(),
                'debug' => 'Google Ads API çağrısında hata oluştu.',
                'error_type' => get_class($e)
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Beklenmeyen hata: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ];
        }
    }

    public function getCallback($code)
    {
        $client = $this->getGoogleClient();
        $accessToken = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($accessToken['error'])) {
            // Hata durumunda Reports sayfasına error parametresi ile yönlendir
            $this->redirectToReports('error', urlencode($accessToken['error_description']));
            return;
        }

        $refreshToken = $accessToken['refresh_token'] ?? null;
        
        // Admin paneli için genellikle client_id = 1 kullanılır
        $clientId = 1; // Sabit admin client ID

        // E-posta adresini almak için ek bir API çağrısı
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        $email = $userInfo->getEmail();

        if ($refreshToken) {
            $result = $this->reportsModel->saveRefreshToken($clientId, $email, $refreshToken);
            if ($result) {
                // Başarı durumunda Reports sayfasına success parametresi ile yönlendir
                // Property seçimi için setup parametresi ekle
                $this->redirectToReports('setup', urlencode('Google hesabı bağlandı: ' . $email . '. Şimdi Analytics property seçin.'));
                return;
            } else {
                // Veritabanı hatası
                $this->redirectToReports('error', urlencode('Refresh token veritabanına kaydedilemedi'));
                return;
            }
        }

        // Refresh token alınamadı hatası
        $this->redirectToReports('error', urlencode('Refresh token alınamadı'));
    }
    
    /**
     * Reports sayfasına yönlendirme yapar
     */
    private function redirectToReports($status, $message)
    {
        if (!session_id()) {
            session_start();
        }
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Hangi sayfadan geldiğini kontrol et (session veya referer'dan)
        $targetPage = 'AnalyticsReports.php'; // varsayılan
        
        // Session'da kayıtlı sayfa varsa onu kullan
        if (isset($_SESSION['oauth_return_page'])) {
            $targetPage = $_SESSION['oauth_return_page'];
            unset($_SESSION['oauth_return_page']); // Temizle
        }
        // Veya HTTP_REFERER'dan belirle
        elseif (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'AdsReports.php') !== false) {
            $targetPage = 'AdsReports.php';
        }
        
        $redirectUrl = $protocol . '://' . $host . '/_y/s/s/Reports/' . $targetPage . '?oauth_status=' . $status . '&oauth_message=' . $message;
        
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function getAuthUrl()
    {
        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();
        return ['status' => 'success', 'authUrl' => $authUrl];
    }

    public function checkConnection()
    {
        // Admin paneli için genellikle client_id = 1 kullanılır
        $clientId = 1; // Sabit admin client ID
        
        $refreshToken = $this->reportsModel->getRefreshToken($clientId);
        
        if ($refreshToken) {
            // Property bilgisi de kontrol et
            $propertyInfo = $this->reportsModel->getAnalyticsProperty($clientId);
            $adsCustomer = $this->reportsModel->getAdsCustomer($clientId);
            
            if ($propertyInfo && !empty($propertyInfo['ga_property_id'])) {
                // Analytics property seçili ise, bu sayfa için bağlı kabul et
                $message = 'Google hesabı ve Analytics property bağlı.';
                if (!$adsCustomer) {
                    $message .= ' Ancak Ads müşteri hesabı seçilmemiş.';
                }
                return [
                    'status' => 'connected', 
                    'message' => $message,
                    'property_name' => $propertyInfo['ga_property_name'] ?? 'Bilinmeyen Property',
                    'property_id' => $propertyInfo['ga_property_id'],
                    'ads_customer_id' => $adsCustomer
                ];
            } elseif ($adsCustomer) {
                return [
                    'status' => 'setup_needed', 
                    'message' => 'Google hesabı ve Ads müşteri hesabı bağlı ancak Analytics property seçilmemiş'
                ];
            } else {
                return [
                    'status' => 'setup_needed', 
                    'message' => 'Google hesabı bağlı ancak Analytics property veya Ads müşteri hesabı seçilmemiş'
                ];
            }
        } else {
            return ['status' => 'disconnected', 'message' => 'Google hesabı bağlı değil'];
        }
    }

    public function disconnect()
    {
        // Admin paneli için genellikle client_id = 1 kullanılır
        $clientId = 1; // Sabit admin client ID
        
        // Refresh token'ı sil
        $result = $this->reportsModel->deleteRefreshToken($clientId);
        
        if ($result) {
            return ['status' => 'success', 'message' => 'Google bağlantısı kesildi'];
        } else {
            return ['status' => 'error', 'message' => 'Bağlantı kesilemedi'];
        }
    }
    
    public function getAnalyticsProperties()
    {
        $clientId = 1;
        $refreshToken = $this->reportsModel->getRefreshToken($clientId);
        
        if (!$refreshToken) {
            return ['status' => 'error', 'message' => 'Google hesabı bağlı değil'];
        }
        
        try {
            $client = $this->getGoogleClient();
            $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
            
            // Access token hatası kontrolü
            if (isset($accessToken['error'])) {
                return [
                    'status' => 'error', 
                    'message' => 'Access token hatası: ' . $accessToken['error_description'] ?? $accessToken['error']
                ];
            }

            $adminService = new Google_Service_GoogleAnalyticsAdmin($client);
            
            // Önce account listesini alalım
            $accounts = [];
            try {
                $accountsResponse = $adminService->accounts->listAccounts();
                error_log('Google Analytics Admin API - Accounts Response: ' . print_r($accountsResponse, true)); // DEBUG LOG
                $accounts = $accountsResponse->getAccounts();
            } catch (Exception $e) {
                return [
                    'status' => 'error', 
                    'message' => 'Accounts listelenemedi: ' . $e->getMessage(),
                    'debug' => 'Yetki sorunu olabilir. Analytics hesabınızda yönetici yetkisine sahip olduğunuzdan emin olun.'
                ];
            }
            
            if (empty($accounts)) {
                return [
                    'status' => 'error', 
                    'message' => 'Analytics hesabı bulunamadı',
                    'debug' => 'Google Analytics hesabınızda property ve hesap bulunmuyor.'
                ];
            }
            
            $properties = [];
            
            foreach ($accounts as $account) {
                $accountName = $account->getDisplayName();
                $accountId = $account->getName(); // accounts/XXXXXX formatında
                
                try {
                    // Her hesap için properties listesini al
                    $propertiesResponse = $adminService->properties->listProperties(
                        ['filter' => 'parent:' . $accountId]
                    );
                    error_log('Google Analytics Admin API - Properties Response for ' . $accountId . ': ' . print_r($propertiesResponse, true)); // DEBUG LOG
                    $accountProperties = $propertiesResponse->getProperties();
                    
                    if ($accountProperties) {
                        foreach ($accountProperties as $property) {
                            $properties[] = [
                                'property_id' => $property->getName(), // properties/XXXXXX formatında
                                'display_name' => $property->getDisplayName(),
                                'account_name' => $accountName,
                                'website_url' => '', // GA4 property nesnelerinde websiteUrl bulunmaz
                                'property_type' => $property->getPropertyType() ?? 'GA4'
                            ];
                        }
                    }
                } catch (Exception $e) {
                    // Property listelerken hata oluştu, devam et
                    continue;
                }
            }
            
            if (empty($properties)) {
                return [
                    'status' => 'error', 
                    'message' => 'Analytics property bulunamadı',
                    'debug' => 'Hesaplarınızda property bulundu ancak erişim izni yok veya GA4 property yok.',
                    'accounts_found' => count($accounts)
                ];
            }
            
            return [
                'status' => 'success', 
                'properties' => $properties,
                'count' => count($properties),
                'accounts_count' => count($accounts)
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error', 
                'message' => 'Properties alınamadı: ' . $e->getMessage(),
                'debug' => 'OAuth token geçerli ancak API çağrısında hata oluştu.',
                'error_type' => get_class($e)
            ];
        }
    }
    
    /**
     * Seçilen Analytics property'yi kaydet
     */
    public function saveSelectedProperty($propertyId, $propertyName)
    {
        $clientId = 1;
        
        try {
            // Property bilgilerini veritabanına kaydet
            $result = $this->reportsModel->saveAnalyticsProperty($clientId, $propertyId, $propertyName);
            
            if ($result) {
                return ['status' => 'success', 'message' => 'Analytics property kaydedildi: ' . $propertyName];
            } else {
                return ['status' => 'error', 'message' => 'Property kaydedilemedi'];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Kaydetme hatası: ' . $e->getMessage()];
        }
    }

    public function saveSelectedAdsCustomer($adsCustomerId)
    {
        $clientId = 1;

        try {
            $result = $this->reportsModel->saveSelectedAdsCustomer($clientId, $adsCustomerId);

            if ($result) {
                return ['status' => 'success', 'message' => 'Google Ads müşteri hesabı kaydedildi: ' . $adsCustomerId];
            } else {
                return ['status' => 'error', 'message' => 'Google Ads müşteri hesabı kaydedilemedi'];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Kaydetme hatası: ' . $e->getMessage()];
        }
    }

    public function checkAdsConnection()
    {
        // Admin paneli için genellikle client_id = 1 kullanılır
        $clientId = 1; // Sabit admin client ID
        
        $refreshToken = $this->reportsModel->getRefreshToken($clientId);
        
        if ($refreshToken) {
            // Ads customer bilgisini kontrol et
            $adsCustomer = $this->reportsModel->getAdsCustomer($clientId);
            
            if ($adsCustomer && !empty($adsCustomer)) {
                // Ads customer seçili ise, bağlı kabul et
                return [
                    'status' => 'connected', 
                    'message' => 'Google hesabı ve Ads müşteri hesabı bağlı.',
                    'ads_customer_id' => $adsCustomer
                ];
            } else {
                return [
                    'status' => 'setup_needed', 
                    'message' => 'Google hesabı bağlı ancak Ads müşteri hesabı seçilmemiş'
                ];
            }
        } else {
            return ['status' => 'disconnected', 'message' => 'Google hesabı bağlı değil'];
        }
    }

    public function setOAuthReturnPage($returnPage)
    {
        if (!session_id()) {
            session_start();
        }
        
        $_SESSION['oauth_return_page'] = $returnPage;
        return ['status' => 'success', 'message' => 'Return page set: ' . $returnPage];
    }

    private function getDemoCustomers()
    {
        return [
            [
                'id' => '1234567890',
                'resource_name' => 'customers/1234567890',
                'descriptive_name' => 'Demo Ads Account - Pozitif E-Ticaret',
                'currency_code' => 'TRY',
                'time_zone' => 'Europe/Istanbul',
                'status' => 'ENABLED'
            ],
            [
                'id' => '9876543210',
                'resource_name' => 'customers/9876543210',
                'descriptive_name' => 'Test Ads Account - E-commerce',
                'currency_code' => 'TRY',
                'time_zone' => 'Europe/Istanbul',
                'status' => 'ENABLED'
            ]
        ];
    }
}

$response = null;
try {
    $controller = new GoogleController($db, $config);
    
    $action = $requestData['action'] ?? $_GET['action'] ?? null;
    
    if ($action === 'authUrl' || $action === 'getAuthUrl') {
        $response = $controller->getAuthUrl();
    } elseif ($action === 'callback' || $action === 'getCallback') {
        $response = $controller->getCallback($_GET['code'] ?? null);
    } elseif ($action === 'checkConnection' || $action === 'getCheckConnection') {
        $response = $controller->checkConnection();
    } elseif ($action === 'disconnect' || $action === 'getDisconnect') {
        $response = $controller->disconnect();
    } elseif ($action === 'getProperties' || $action === 'getAnalyticsProperties') {
        $response = $controller->getAnalyticsProperties();
    } elseif ($action === 'saveProperty' || $action === 'saveSelectedProperty') {
        $propertyId = $requestData['property_id'] ?? $_POST['property_id'] ?? null;
        $propertyName = $requestData['property_name'] ?? $_POST['property_name'] ?? null;
        $response = $controller->saveSelectedProperty($propertyId, $propertyName);
    } elseif ($action === 'getAdsCustomers') {
        $response = $controller->getGoogleAdsCustomers();
    } elseif ($action === 'saveAdsCustomer' || $action === 'saveSelectedAdsCustomer') {
        $adsCustomerId = $requestData['ads_customer_id'] ?? $_POST['ads_customer_id'] ?? null;
        $response = $controller->saveSelectedAdsCustomer($adsCustomerId);
    } elseif ($action === 'checkAdsConnection') {
        $response = $controller->checkAdsConnection();
    } elseif ($action === 'setOAuthReturnPage') {
        $returnPage = $requestData['return_page'] ?? $_POST['return_page'] ?? null;
        $response = $controller->setOAuthReturnPage($returnPage);
    } else {
        Log::adminWrite('Google.php', 'Invalid action: ' . $action);
        $response = ['status' => 'error', 'message' => 'Invalid action Googe.php: ' . $action];
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()];
}
if(!empty($action)){
    echo json_encode($response);
}


