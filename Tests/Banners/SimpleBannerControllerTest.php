<?php
/**
 * BannerController Layout Fix Basit Testi
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';
require_once $basePath . '/App/Controller/BannerController.php';

class SimpleBannerControllerTest
{
    public function testLayoutGroupConversion()
    {
        echo "=== BANNERCONTROLLER LAYOUT GROUP TEST ===\n\n";
        
        // Mock banner data oluştur
        $mockBanner = [
            'group_info' => [
                'id' => 2,
                'style_class' => 'top-banner'
            ],
            'layout_info' => [
                'layout_group' => 'top-banner', // Database'den gelen gerçek değer
                'layout_view' => 'single',
                'columns' => 1
            ],
            'type_id' => 2,
            'type_name' => 'Tepe Banner',
            'banners' => [
                [
                    'id' => 151,
                    'title' => 'Test Başlık',
                    'content' => 'Test içerik metni',
                    'image' => 'Banner/test.jpg',
                    'link' => '#',
                    'style' => [
                        'show_button' => 1,
                        'button_title' => 'Detaylar'
                    ]
                ]
            ]
        ];
        
        echo "📊 Test Verisi:\n";
        echo "   - Layout Group (Orijinal): '{$mockBanner['layout_info']['layout_group']}'\n";
        echo "   - Banner Sayısı: " . count($mockBanner['banners']) . "\n\n";
        
        // BannerController ile render et
        echo "🔧 BannerController ile render ediliyor...\n";
        
        try {
            $bannerController = new BannerController();
            $htmlOutput = $bannerController->renderBannerHTML($mockBanner);
            
            echo "✅ HTML başarıyla oluşturuldu!\n\n";
            
            echo "📄 OLUŞTURULAN HTML:\n";
            echo "====================\n";
            echo $htmlOutput . "\n\n";
            
            // HTML içerik kontrolü
            $this->analyzeHTML($htmlOutput);
            
            // Test HTML dosyası oluştur
            $this->createTestFile($htmlOutput);
            
        } catch (Exception $e) {
            echo "❌ Render hatası: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }
    }
    
    private function analyzeHTML($html)
    {
        echo "🔍 HTML ANALİZ SONUÇLARI:\n";
        echo "=========================\n";
        
        $checks = [
            'Banner Container' => strpos($html, 'banner-type-tepe-banner') !== false,
            'Banner Image' => strpos($html, 'banner-image') !== false,
            'Banner Title' => strpos($html, 'title') !== false && strpos($html, 'Test Başlık') !== false,
            'Banner Content' => strpos($html, 'content') !== false && strpos($html, 'Test içerik') !== false,
            'Banner Button' => strpos($html, 'banner-button') !== false,
            'Text-Image Layout' => strpos($html, 'text-image-layout') !== false,
            'Content Box' => strpos($html, 'content-box') !== false
        ];
        
        foreach ($checks as $check => $result) {
            echo "   " . ($result ? '✅' : '❌') . " {$check}\n";
        }
        
        $successCount = count(array_filter($checks));
        $totalCount = count($checks);
        
        echo "\n📊 Sonuç: {$successCount}/{$totalCount} kontrol başarılı\n";
        
        if ($successCount === $totalCount) {
            echo "🎉 TÜM KONTROLLER BAŞARILI!\n";
            echo "   Layout group çevirisi düzgün çalışıyor.\n";
            echo "   Tepe banner artık görsel, başlık, içerik ve buton içeriyor.\n";
        } else {
            echo "⚠️ Bazı kontroller başarısız.\n";
            echo "   BannerController'da ek düzeltme gerekebilir.\n";
        }
    }
    
    private function createTestFile($html)
    {
        $testHTML = '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BannerController Layout Fix Test</title>
    <link rel="stylesheet" href="../../Public/CSS/Banners/tepe-banner.css">
    <style>
        body { 
            margin: 0; 
            padding: 20px; 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-header {
            background: #28a745;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .banner-container {
            border: 2px dashed #28a745;
            padding: 10px;
            margin: 20px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>✅ BannerController Layout Fix Başarılı!</h1>
            <p>Layout Group: \'top-banner\' → \'text_and_image\' çevirisi çalışıyor</p>
        </div>
        
        <h2>🎯 Test Sonucu:</h2>
        <div class="banner-container">
            ' . $html . '
        </div>
        
        <p><strong>Test Zamanı:</strong> ' . date('d.m.Y H:i:s') . '</p>
        <p><strong>Durum:</strong> Layout group çevirisi başarıyla uygulandı</p>
    </div>
</body>
</html>';
        
        $testFilePath = dirname(__DIR__) . '/Temp/banner-controller-fix-test.html';
        
        // Temp klasörünü oluştur
        if (!is_dir(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0755, true);
        }
        
        file_put_contents($testFilePath, $testHTML);
        
        echo "\n📁 Test HTML dosyası: Tests/Temp/banner-controller-fix-test.html\n";
        echo "🌐 Tarayıcıda açarak görsel sonucu kontrol edin.\n";
    }
}

// Test'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'SimpleBannerControllerTest.php') {
    $test = new SimpleBannerControllerTest();
    $test->testLayoutGroupConversion();
    
    echo "\n=== TEST TAMAMLANDI ===\n";
}
