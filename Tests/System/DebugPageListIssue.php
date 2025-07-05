<?php
/**
 * PageList "Sayfa bulunamadı" hatasını debug etmek için test scripti
 * 
 * Bu script AdminPageController'ın getPagesWithTranslationStatus methodunu test eder
 * ve hangi dil seçimlerinde sorun olduğunu tespit eder.
 */

// Admin sistemi yükle
require_once $_SERVER['DOCUMENT_ROOT'] . '/_y/s/global.php';

echo "=== PAGE LIST DEBUG TEST ===\n";

// Test varyasyonları
$testCases = [
    ['languageID' => 1, 'translationFilter' => 'all', 'categoryID' => 0, 'title' => 'Türkçe - Tüm Sayfalar'],
    ['languageID' => 2, 'translationFilter' => 'all', 'categoryID' => 0, 'title' => 'İngilizce - Tüm Sayfalar'],
    ['languageID' => 1, 'translationFilter' => 'untranslated', 'categoryID' => 0, 'title' => 'Türkçe - Çevrilmemiş'],
    ['languageID' => 2, 'translationFilter' => 'completed', 'categoryID' => 0, 'title' => 'İngilizce - Tamamlanmış'],
];

foreach ($testCases as $case) {
    echo "\n=== {$case['title']} ===\n";
    
    // POST verilerini simüle et
    $_POST = [
        'action' => 'getPagesWithTranslationStatus',
        'languageID' => $case['languageID'],
        'translationFilter' => $case['translationFilter'],
        'categoryID' => $case['categoryID']
    ];
    
    try {
        // Output buffering ile controller çıktısını yakala
        ob_start();
        include_once MODEL . 'Admin/AdminPage.php';
        include CONTROLLER . 'Admin/AdminPageController.php';
        $response = ob_get_clean();
        
        echo "Controller Response:\n";
        echo $response . "\n";
        
        // JSON parse et
        $data = json_decode($response, true);
        if ($data) {
            echo "Status: " . ($data['status'] ?? 'N/A') . "\n";
            echo "Message: " . ($data['message'] ?? 'N/A') . "\n";
            if (isset($data['pages'])) {
                echo "Pages Count: " . count($data['pages']) . "\n";
                if (count($data['pages']) > 0) {
                    echo "First Page: " . $data['pages'][0]['pageName'] . "\n";
                    echo "Translation Details: " . json_encode($data['pages'][0]['translationDetails'] ?? []) . "\n";
                }
            }
        } else {
            echo "JSON Parse HATASI - Raw Response:\n";
            echo $response;
        }
        
    } catch (Exception $e) {
        echo "HATA: " . $e->getMessage() . "\n";
        echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    }
    
    // POST verilerini temizle
    $_POST = [];
    
    echo str_repeat("-", 50) . "\n";
}

echo "\n=== DIRECT DATABASE CHECK ===\n";

// Doğrudan veritabanından sayfa sayısı kontrol et
try {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM sayfa WHERE sayfadurum='on'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Aktif Sayfa Sayısı: " . $result['total'] . "\n";
    
    // Dil bilgilerini kontrol et
    $stmt = $db->prepare("SELECT * FROM dil WHERE durumu='1'");
    $stmt->execute();
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Aktif Diller:\n";
    foreach ($languages as $lang) {
        echo "- ID: {$lang['languageID']}, Name: {$lang['languageName']}, Code: {$lang['languageCode']}\n";
    }
    
    // Language mapping kontrol et
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM language_page_mapping");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Translation Mapping Count: " . $result['total'] . "\n";
    
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>
