<?php
/**
 * Banner Full Width Merkezleme Testi
 * 
 * Full width ve merkezleme özelliklerini test eder
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';
require_once $basePath . '/App/Controller/BannerController.php';

class BannerCenteringTest
{
    public function testBannerCentering()
    {
        echo "=== BANNER MERKEZLEME TESTİ ===\n\n";
        
        // Test verileri - Full width ve Non-full width
        $testCases = [
            [
                'name' => 'Full Width Banner (Mevcut)',
                'group_full_size' => 1,
                'banner_full_size' => 0
            ],
            [
                'name' => 'Centered Banner (Simülasyon)',
                'group_full_size' => 0,
                'banner_full_size' => 0
            ]
        ];
        
        foreach ($testCases as $index => $testCase) {
            echo ($index + 1) . ". {$testCase['name']} TESTİ\n";
            echo str_repeat('=', 50) . "\n";
            
            $mockBanner = $this->createMockBanner($testCase);
            $this->testBannerHTML($mockBanner, $testCase['name']);
            echo "\n";
        }
        
        $this->generateComparisonHTML();
    }
    
    private function createMockBanner($testCase)
    {
        return [
            'group_info' => [
                'id' => 2,
                'style_class' => 'top-banner',
                'group_full_size' => $testCase['group_full_size'],
                'banner_full_size' => $testCase['banner_full_size']
            ],
            'layout_info' => [
                'layout_group' => 'top-banner',
                'layout_view' => 'single',
                'columns' => 1
            ],
            'type_id' => 2,
            'type_name' => 'Tepe Banner',
            'banners' => [
                [
                    'id' => 151,
                    'title' => 'Merkezleme Test Başlığı',
                    'content' => 'Bu banner full width = ' . ($testCase['group_full_size'] ? 'Evet' : 'Hayır') . ' ayarı ile test ediliyor.',
                    'image' => 'Banner/test.jpg',
                    'link' => '#test',
                    'style' => [
                        'show_button' => 1,
                        'button_title' => 'Test Butonu'
                    ]
                ]
            ]
        ];
    }
    
    private function testBannerHTML($mockBanner, $testName)
    {
        try {
            $bannerController = new BannerController();
            $htmlOutput = $bannerController->renderBannerHTML($mockBanner);
            
            echo "✅ HTML başarıyla oluşturuldu\n";
            
            // CSS sınıf kontrolü
            $this->analyzeCSSClasses($htmlOutput, $mockBanner);
            
            echo "📄 HTML Önizleme:\n";
            echo substr($htmlOutput, 0, 200) . "...\n";
            
            return $htmlOutput;
            
        } catch (Exception $e) {
            echo "❌ Test hatası: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    private function analyzeCSSClasses($html, $bannerData)
    {
        echo "🔍 CSS Sınıf Analizi:\n";
        
        $checks = [
            'banner-centered' => [
                'exists' => strpos($html, 'banner-centered') !== false,
                'expected' => $bannerData['group_info']['group_full_size'] == 0,
                'description' => 'Group merkezleme sınıfı'
            ],
            'banner-content-centered' => [
                'exists' => strpos($html, 'banner-content-centered') !== false,
                'expected' => $bannerData['group_info']['banner_full_size'] == 0,
                'description' => 'Content merkezleme sınıfı'
            ],
            'banner-container' => [
                'exists' => strpos($html, 'banner-container') !== false,
                'expected' => true,
                'description' => 'Banner container'
            ]
        ];
        
        foreach ($checks as $class => $check) {
            $status = $check['exists'] === $check['expected'] ? '✅' : '❌';
            $result = $check['exists'] ? 'Mevcut' : 'Yok';
            $expected = $check['expected'] ? 'Bekleniyor' : 'Beklenmez';
            
            echo "   {$status} .{$class}: {$result} ({$expected}) - {$check['description']}\n";
        }
    }
    
    private function generateComparisonHTML()
    {
        echo "📁 Karşılaştırma HTML dosyası oluşturuluyor...\n";
        
        // Full width banner
        $fullWidthBanner = $this->createMockBanner(['group_full_size' => 1, 'banner_full_size' => 0]);
        $centeredBanner = $this->createMockBanner(['group_full_size' => 0, 'banner_full_size' => 0]);
        
        $bannerController = new BannerController();
        $fullWidthHTML = $bannerController->renderBannerHTML($fullWidthBanner);
        $centeredHTML = $bannerController->renderBannerHTML($centeredBanner);
        
        $comparisonHTML = '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Merkezleme Karşılaştırması</title>
    <link rel="stylesheet" href="../../Public/CSS/Banners/tepe-banner.css">
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }
        .test-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .test-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }
        .banner-demo {
            border: 2px solid #e9ecef;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        .banner-demo::before {
            content: attr(data-title);
            position: absolute;
            top: -15px;
            left: 15px;
            background: #007bff;
            color: white;
            padding: 5px 15px;
            font-size: 12px;
            border-radius: 15px;
            font-weight: bold;
            z-index: 10;
        }
        .demo-fullwidth::before { background: #dc3545; }
        .demo-centered::before { background: #28a745; }
        
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        
        .info-panel {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        
        @media (max-width: 768px) {
            .comparison-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>🎯 Banner Full Width vs Merkezleme Karşılaştırması</h1>
            <p><strong>Test Zamanı:</strong> ' . date('d.m.Y H:i:s') . '</p>
            <p>BannerController merkezleme özelliği test ediliyor</p>
        </div>
        
        <div class="info-panel">
            <h3>📋 Test Bilgileri:</h3>
            <ul>
                <li><strong>Full Width Banner:</strong> group_full_size = 1 (Kırmızı kenarlık)</li>
                <li><strong>Centered Banner:</strong> group_full_size = 0 (Yeşil kenarlık)</li>
                <li><strong>CSS Sınıfları:</strong> .banner-centered ve .banner-content-centered eklendi</li>
                <li><strong>Responsive:</strong> Farklı ekran boyutlarında test edin</li>
            </ul>
        </div>
        
        <div class="comparison-grid">
            <div>
                <h2>🔴 Full Width Banner</h2>
                <div class="banner-demo demo-fullwidth" data-title="FULL WIDTH (group_full_size=1)">
                    ' . $fullWidthHTML . '
                </div>
                <p><small>Bu banner tam genişlik kaplar ve merkezleme sınıfı almaz.</small></p>
            </div>
            
            <div>
                <h2>🟢 Centered Banner</h2>
                <div class="banner-demo demo-centered" data-title="CENTERED (group_full_size=0)">
                    ' . $centeredHTML . '
                </div>
                <p><small>Bu banner .banner-centered sınıfı ile merkezlenir (max-width: 1200px).</small></p>
            </div>
        </div>
        
        <div class="info-panel">
            <h3>🔍 CSS Sınıf Kontrolü:</h3>
            <div id="css-check-results">
                <p>Sayfa yüklendikten sonra JavaScript ile kontrol edilecek...</p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fullWidthContainer = document.querySelector(".demo-fullwidth .banner-container");
            const centeredContainer = document.querySelector(".demo-centered .banner-container");
            
            let results = "<ul>";
            
            // Full width banner kontrolü
            if (fullWidthContainer) {
                const hasCenteredClass = fullWidthContainer.classList.contains("banner-centered");
                results += "<li>" + (hasCenteredClass ? "❌" : "✅") + " Full width banner merkezleme sınıfı: " + (hasCenteredClass ? "Var (Hatalı)" : "Yok (Doğru)") + "</li>";
            }
            
            // Centered banner kontrolü  
            if (centeredContainer) {
                const hasCenteredClass = centeredContainer.classList.contains("banner-centered");
                results += "<li>" + (hasCenteredClass ? "✅" : "❌") + " Centered banner merkezleme sınıfı: " + (hasCenteredClass ? "Var (Doğru)" : "Yok (Hatalı)") + "</li>";
            }
            
            results += "</ul>";
            results += "<p><strong>Sonuç:</strong> " + (results.includes("❌") ? "Bazı kontroller başarısız" : "Tüm kontroller başarılı") + "</p>";
            
            document.getElementById("css-check-results").innerHTML = results;
        });
        
        // Responsive test
        window.addEventListener("resize", function() {
            console.log("Viewport genişliği: " + window.innerWidth + "px");
        });
    </script>
</body>
</html>';
        
        $testFilePath = dirname(__DIR__) . '/Temp/banner-centering-test.html';
        
        // Temp klasörünü oluştur
        if (!is_dir(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0755, true);
        }
        
        file_put_contents($testFilePath, $comparisonHTML);
        
        echo "📁 Karşılaştırma dosyası: Tests/Temp/banner-centering-test.html\n";
        echo "🌐 Tarayıcıda açarak full width vs centered banner karşılaştırmasını görün\n";
    }
}

// Test'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'BannerCenteringTest.php') {
    $test = new BannerCenteringTest();
    $test->testBannerCentering();
    
    echo "\n=== MERKEZLEME TESTİ TAMAMLANDI ===\n";
}
