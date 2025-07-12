<?php
// Bu dosya, Router tarafından çağrılan ana webservice giriş noktasıdır.
// Gelen isteği GoogleController'a yönlendirir.

$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);

// AdminGlobal.php'yi dahil et (gerekli bağımlılıklar için)
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';

// Gerekli sınıfları dahil et
require_once ROOT . 'vendor/autoload.php';
require_once ROOT . 'App/Model/Admin/AdminReports.php';

class GoogleController
{
    private $db;
    private $config;
    private $reportsModel;
    private $adminSession;

    public function __construct($db, $config, $adminSession)
    {
        $this->db = $db;
        $this->config = $config;
        $this->reportsModel = new AdminReports($db);
        $this->adminSession = $adminSession;
    }

    private function getGoogleClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Pozitif E-Ticaret Raporlama');
        $client->setScopes([
            Google_Service_AnalyticsData::ANALYTICS_READONLY,
            Google_Service_Adsense::ADSENSE_READONLY,
            Google_Service_Oauth2::USERINFO_EMAIL,
            Google_Service_Oauth2::USERINFO_PROFILE
        ]);
        $client->setAuthConfig(ROOT . 'App/Config/CloudFlare.json');
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/?/webservice/google/get/callback');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return $client;
    }

    public function getCallback($code)
    {
        $client = $this->getGoogleClient();
        $accessToken = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($accessToken['error'])) {
            return ['status' => 'error', 'message' => $accessToken['error_description']];
        }

        $refreshToken = $client->getRefreshToken();
        
        // AdminSession'dan client_id'yi al
        $clientId = $this->adminSession->getAdminCasper()->getClientId();

        // E-posta adresini almak için ek bir API çağrısı
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        $email = $userInfo->getEmail();

        if ($refreshToken) {
            $result = $this->reportsModel->saveRefreshToken($clientId, $email, $refreshToken);
            if ($result) {
                return ['status' => 'success', 'message' => 'Google hesabı başarıyla bağlandı.'];
            } else {
                return ['status' => 'error', 'message' => 'Refresh token veritabanına kaydedilemedi.'];
            }
        }

        return ['status' => 'error', 'message' => 'Refresh token alınamadı.'];
    }

    public function getAuthUrl()
    {
        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();
        return ['status' => 'success', 'authUrl' => $authUrl];
    }
}

// Router tarafından çağrılan webservice dosyası olduğu için
// doğrudan burada controller örneğini oluşturup metodu çağırıyoruz.
$controller = new GoogleController($db, $config, $adminSession);

$action = $_GET['action'] ?? null;

if ($action === 'getAuthUrl') {
    $response = $controller->getAuthUrl();
} elseif ($action === 'getCallback') {
    $response = $controller->getCallback($_GET['code'] ?? null);
} else {
    $response = ['status' => 'error', 'message' => 'Invalid action'];
}

echo json_encode($response);