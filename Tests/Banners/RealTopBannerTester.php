<?php
/**
 * Gerçek SiteConfig ile Tepe Banner Testi
 * 
 * BannerController düzeltmesi sonrası gerçek veri akışıyla test
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);

// Sabitleri tanımla
define('MODEL', $basePath . '/App/Model/');
define('DATABASE', $basePath . '/App/Database/');
define('CORE', $basePath . '/App/Core/');

require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';
require_once $basePath . '/App/Core/Config.php';
require_once $basePath . '/App/Controller/BannerController.php';
require_once $basePath . '/App/Model/SiteConfig.php';

class RealTopBannerTester
{
    private $siteConfig;
    
    public function __construct()
    {
        $this->siteConfig = new SiteConfig();
    }
    
    public function testRealTopBanner()
    {
        echo "=== GERÇEK SİTECONFİG İLE TEPE BANNER TESTİ ===\n\n";
        
        try {
            // SiteConfig'den gerçek banner verilerini al
            $banners = $this->siteConfig->getBanners(1, null); // Page ID 1 (homepage)
            
            echo "📊 SiteConfig'den alınan toplam banner tipi: " . count($banners) . "\n\n";
            
            // Tepe banner'ı bul (type_id = 2)
            $topBanner = null;
            foreach ($banners as $banner) {
                if ($banner['type_id'] == 2) {
                    $topBanner = $banner;
                    break;
                }
            }
            
            if (!$topBanner) {
                echo "❌ Tepe banner bulunamadı!\n";
                echo "Mevcut banner tipleri:\n";
                foreach ($banners as $banner) {
                    echo "  - Type ID: {$banner['type_id']}, Type: {$banner['type_name']}\n";
                }
                return;
            }
            
            echo "✅ Tepe banner bulundu!\n";
            echo "📋 Banner Bilgileri:\n";
            echo "   - Type ID: {$topBanner['type_id']}\n";
            echo "   - Type Name: {$topBanner['type_name']}\n";
            echo "   - Group ID: {$topBanner['group_info']['id']}\n";
            echo "   - Group Name: {$topBanner['group_info']['name']}\n";
            echo "   - Layout Group: {$topBanner['layout_info']['layout_group']}\n";
            echo "   - Layout View: {$topBanner['layout_info']['layout_view']}\n";
            echo "   - Style Class: {$topBanner['group_info']['style_class']}\n";
            echo "   - Banner Sayısı: " . count($topBanner['banners']) . "\n\n";
            
            // BannerController ile HTML render et
            echo "🔧 BannerController ile HTML render ediliyor...\n";
            $bannerController = new BannerController();
            $htmlOutput = $bannerController->renderBannerHTML($topBanner);
            
            echo "📄 OLUŞTURULAN HTML:\n";
            echo "====================\n";
            echo $htmlOutput . "\n\n";
            
            // Test HTML dosyası oluştur
            $this->createRealTestHTML($htmlOutput, $topBanner);
            
            // HTML yapısını analiz et
            $this->analyzeHTMLStructure($htmlOutput);
            
            return $htmlOutput;
            
        } catch (Exception $e) {
            echo "❌ Test hatası: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }
    }
    
    private function createRealTestHTML($htmlOutput, $bannerData)
    {
        $testHTML = '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerçek Tepe Banner Test</title>
    <link rel="stylesheet" href="../../Public/CSS/Banners/tepe-banner.css">
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 12px 12px 0 0;
        }
        .banner-debug {
            border: 2px solid #e3f2fd;
            margin: 20px 0;
            position: relative;
            border-radius: 8px;
            background: #fafafa;
            overflow: hidden;
        }
        .banner-debug::before {
            content: "🎯 Gerçek Banner Çıktısı";
            position: absolute;
            top: -12px;
            left: 15px;
            background: #2196f3;
            color: white;
            padding: 5px 15px;
            font-size: 12px;
            border-radius: 15px;
            font-weight: bold;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }
        .info-card h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-success { background: #28a745; }
        .status-warning { background: #ffc107; }
        .status-error { background: #dc3545; }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>🎯 Gerçek SiteConfig Tepe Banner Testi</h1>
            <p><strong>Test Zamanı:</strong> ' . date('d.m.Y H:i:s') . '</p>
            <p><strong>Düzeltme Sonrası:</strong> BannerController Layout Group Fix Uygulandı</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h4>📊 Banner Grubu</h4>
                <p><strong>ID:</strong> ' . $bannerData['group_info']['id'] . '</p>
                <p><strong>Adı:</strong> ' . $bannerData['group_info']['name'] . '</p>
                <p><strong>Style Class:</strong> ' . $bannerData['group_info']['style_class'] . '</p>
            </div>
            
            <div class="info-card">
                <h4>🎨 Layout Bilgisi</h4>
                <p><strong>Layout ID:</strong> ' . $bannerData['layout_info']['id'] . '</p>
                <p><strong>Layout Group:</strong> ' . $bannerData['layout_info']['layout_group'] . '</p>
                <p><strong>Layout View:</strong> ' . $bannerData['layout_info']['layout_view'] . '</p>
            </div>
            
            <div class="info-card">
                <h4>🎯 Banner Verisi</h4>
                <p><strong>Tip ID:</strong> ' . $bannerData['type_id'] . '</p>
                <p><strong>Tip Adı:</strong> ' . $bannerData['type_name'] . '</p>
                <p><strong>Banner Sayısı:</strong> ' . count($bannerData['banners']) . '</p>
            </div>
            
            <div class="info-card">
                <h4>📋 İçerik Durumu</h4>';
        
        $banner = $bannerData['banners'][0];
        $testHTML .= '
                <p><span class="status-indicator ' . (!empty($banner['title']) ? 'status-success' : 'status-error') . '"></span><strong>Başlık:</strong> ' . ($banner['title'] ? 'Mevcut' : 'Eksik') . '</p>
                <p><span class="status-indicator ' . (!empty($banner['content']) ? 'status-success' : 'status-error') . '"></span><strong>İçerik:</strong> ' . ($banner['content'] ? 'Mevcut' : 'Eksik') . '</p>
                <p><span class="status-indicator ' . (!empty($banner['image']) ? 'status-success' : 'status-error') . '"></span><strong>Görsel:</strong> ' . ($banner['image'] ? 'Mevcut' : 'Eksik') . '</p>
                <p><span class="status-indicator ' . (!empty($banner['link']) ? 'status-success' : 'status-error') . '"></span><strong>Link:</strong> ' . ($banner['link'] ? 'Mevcut' : 'Eksik') . '</p>
            </div>
        </div>
        
        <h2>🎨 Banner Önizleme:</h2>
        <div class="banner-debug">
            ' . $htmlOutput . '
        </div>
        
        <h2>🔍 Teknik Detaylar:</h2>
        <div id="tech-details">
            <p>Bu test, gerçek SiteConfig.php ve düzeltilmiş BannerController.php kullanılarak yapılmıştır.</p>
            <p><strong>Layout Group Çevirisi:</strong> \'' . $bannerData['layout_info']['layout_group'] . '\' → \'text_and_image\'</p>
        </div>
        
        <div id="css-check-results">
            <p>JavaScript ile CSS kontrolleri yükleniyor...</p>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const banner = document.querySelector(".banner-type-tepe-banner");
            const image = document.querySelector(".banner-image img");
            const title = document.querySelector(".title");
            const content = document.querySelector(".content");
            const button = document.querySelector(".banner-button");
            
            let results = "<h3>🔍 CSS Element Kontrolleri:</h3><ul>";
            results += banner ? "<li>✅ Ana banner container bulundu</li>" : "<li>❌ Ana banner container bulunamadı</li>";
            results += image ? "<li>✅ Banner görseli bulundu</li>" : "<li>❌ Banner görseli bulunamadı</li>";
            results += title ? "<li>✅ Banner başlığı bulundu</li>" : "<li>❌ Banner başlığı bulunamadı</li>";
            results += content ? "<li>✅ Banner içeriği bulundu</li>" : "<li>❌ Banner içeriği bulunamadı</li>";
            results += button ? "<li>✅ Banner butonu bulundu</li>" : "<li>❌ Banner butonu bulunamadı</li>";
            results += "</ul>";
            
            // Görsel yükleme testi
            if (image) {
                results += "<h3>🖼️ Görsel Yükleme Testi:</h3>";
                image.onload = function() {
                    results += "<p>✅ Görsel başarıyla yüklendi: " + this.src + "</p>";
                    document.getElementById("css-check-results").innerHTML = results;
                };
                image.onerror = function() {
                    results += "<p>❌ Görsel yüklenemedi: " + this.src + "</p>";
                    this.style.border = "2px dashed red";
                    this.alt = "GÖRSEL BULUNAMADI";
                    document.getElementById("css-check-results").innerHTML = results;
                };
            }
            
            document.getElementById("css-check-results").innerHTML = results;
        });
    </script>
</body>
</html>';
        
        $testFilePath = dirname(__DIR__) . '/Temp/real-tepe-banner-test.html';
        
        // Temp klasörünü oluştur
        if (!is_dir(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0755, true);
        }
        
        file_put_contents($testFilePath, $testHTML);
        
        echo "📁 Gerçek test HTML dosyası: Tests/Temp/real-tepe-banner-test.html\n";
        echo "🌐 Tarayıcıda açarak görsel sonucu kontrol edin.\n\n";
    }
    
    private function analyzeHTMLStructure($htmlOutput)
    {
        echo "🔍 HTML YAPI ANALİZİ:\n";
        echo "=====================\n";
        
        // HTML elementlerini say
        $imageCount = substr_count($htmlOutput, '<img');
        $titleCount = substr_count($htmlOutput, 'class=\'title\'');
        $contentCount = substr_count($htmlOutput, 'class=\'content\'');
        $buttonCount = substr_count($htmlOutput, 'class=\'banner-button\'');
        
        echo "📊 Element Sayıları:\n";
        echo "   - Görseller: {$imageCount}\n";
        echo "   - Başlıklar: {$titleCount}\n";
        echo "   - İçerikler: {$contentCount}\n";
        echo "   - Butonlar: {$buttonCount}\n";
        
        // Layout class kontrolü
        $hasTextImageLayout = strpos($htmlOutput, 'text-image-layout') !== false;
        $hasContentBox = strpos($htmlOutput, 'content-box') !== false;
        $hasBannerImage = strpos($htmlOutput, 'banner-image') !== false;
        
        echo "\n✅ Layout Sınıf Kontrolleri:\n";
        echo "   - text-image-layout: " . ($hasTextImageLayout ? '✅ Mevcut' : '❌ Eksik') . "\n";
        echo "   - content-box: " . ($hasContentBox ? '✅ Mevcut' : '❌ Eksik') . "\n";
        echo "   - banner-image: " . ($hasBannerImage ? '✅ Mevcut' : '❌ Eksik') . "\n";
        
        // Teşhis sonucu
        if ($imageCount > 0 && $titleCount > 0 && $contentCount > 0 && $hasTextImageLayout) {
            echo "\n🎉 BAŞARILI: Tepe banner HTML yapısı doğru oluşturuldu!\n";
            echo "   Layout group çevirisi çalışıyor.\n";
        } else {
            echo "\n⚠️ SORUN: HTML yapısında eksiklik var.\n";
            echo "   BannerController düzeltmesi kontrol edilmeli.\n";
        }
    }
}

// Script'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'RealTopBannerTester.php') {
    try {
        $tester = new RealTopBannerTester();
        $tester->testRealTopBanner();
        
        echo "\n=== GERÇEK TEST TAMAMLANDI ===\n";
        
    } catch (Exception $e) {
        echo "❌ Test hatası: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
}
