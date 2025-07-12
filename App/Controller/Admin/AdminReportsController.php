<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';

/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */

$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode(['status' => 'error', 'message' => 'Action not specified']);
    exit();
}

require_once ROOT . 'App/Model/Admin/AdminReports.php';

class AdminReportsController
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

    public function getReportData($clientId, $startDate, $endDate)
    {
        $data = $this->reportsModel->getDailySummary($clientId, $startDate, $endDate);
        return ['status' => 'success', 'data' => $data];
    }
}

$controller = new AdminReportsController($db, $config, $adminSession);

switch ($action) {
    case 'getReportData':
        $clientId = $adminSession->getAdminCasper()->getClientId(); // Örnek olarak session'dan alınıyor
        $startDate = $requestData['startDate'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $requestData['endDate'] ?? date('Y-m-d');
        $response = $controller->getReportData($clientId, $startDate, $endDate);
        break;
    default:
        $response = ['status' => 'error', 'message' => 'Invalid action'];
        break;
}

echo json_encode($response);
