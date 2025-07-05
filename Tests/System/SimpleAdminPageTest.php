<?php
// Sadece AdminPage metodlarını test edelim
include_once 'GetLocalDatabaseInfo.php';

// AdminDatabase include
require_once __DIR__ . '/../../App/Database/AdminDatabase.php';
require_once __DIR__ . '/../../App/Model/Admin/AdminPage.php';

$dbInfo = getLocalDatabaseInfo();
$pdo = new PDO("mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8", $dbInfo['username'], $dbInfo['password']);

// AdminDatabase mock oluştur
class SimpleAdminDatabase {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function select($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$db = new SimpleAdminDatabase($pdo);
$adminPageModel = new AdminPage($db);

echo "=== ADMINPAGE MODEL TEST ===" . PHP_EOL;

// Belirli bir sayfanın çeviri durumunu test edelim
$pageID = 35; // Anlaşmalı Kurumlar
echo "Test Page ID: $pageID" . PHP_EOL . PHP_EOL;

try {
    $translationDetails = $adminPageModel->getPageTranslationStatus($pageID);
    
    echo "Çeviri Detayları:" . PHP_EOL;
    if (empty($translationDetails)) {
        echo "  Çeviri detayı bulunamadı!" . PHP_EOL;
    } else {
        foreach ($translationDetails as $detail) {
            echo "  • Dil: {$detail['languageName']} ({$detail['languageCode']})" . PHP_EOL;
            echo "    Status: " . ($detail['translationStatus'] ?? 'NULL') . PHP_EOL;
            echo "    Translated Page ID: " . ($detail['translatedPageID'] ?? 'NULL') . PHP_EOL;
            echo "    Date: " . ($detail['translationDate'] ?? 'NULL') . PHP_EOL;
            echo str_repeat("-", 30) . PHP_EOL;
        }
    }
    
    // JSON çıktısını simüle edelim - frontend'in aldığı formatı kontrol
    $jsonOutput = [
        'pageID' => $pageID,
        'pageName' => 'Anlaşmalı Kurumlar',
        'translationDetails' => $translationDetails
    ];
    
    echo PHP_EOL . "JSON ÇIKTISI (Frontend'in aldığı format):" . PHP_EOL;
    echo json_encode($jsonOutput, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "✅ Test tamamlandı!" . PHP_EOL;
?>
