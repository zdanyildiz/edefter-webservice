<?php
/**
 * Tepe Banner HTML Test Aracı
 * 
 * Bu araç tepe banner'ın gerçek HTML çıktısını oluşturur ve
 * stil sorunlarını tespit eder.
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';
require_once $basePath . '/App/Controller/BannerController.php';
require_once $basePath . '/App/Core/Config.php';

class TopBannerHTMLTester
{
    private $pdo;
    private $helper;
    
    public function __construct()
    {
        $this->helper = new Helper();
        $this->connectDatabase();
    }
    
    private function connectDatabase()
    {
        global $key, $dbLocalServerName, $dbLocalUsername, $dbLocalPassword, $dbLocalName;
        
        try {
            $decryptedHost = $this->helper->decrypt($dbLocalServerName, $key);
            $decryptedUsername = $this->helper->decrypt($dbLocalUsername, $key);
            $decryptedPassword = $this->helper->decrypt($dbLocalPassword, $key);
            $decryptedDatabase = $this->helper->decrypt($dbLocalName, $key);
            
            $this->pdo = new PDO(
                "mysql:host={$decryptedHost};dbname={$decryptedDatabase};charset=utf8mb4",
                $decryptedUsername,
                $decryptedPassword,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            echo "✅ Veritabanı bağlantısı başarılı\n\n";
            
        } catch (Exception $e) {
            echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function testTopBannerHTML()
    {
        echo "=== TEPE BANNER HTML TEST ARACI ===\n\n";
        
        // Gerçek banner verilerini al
        $bannerData = $this->getTopBannerData();
        
        if (empty($bannerData)) {
            echo "❌ Tepe banner verisi bulunamadı!\n";
            return;
        }
        
        echo "✅ Banner verisi bulundu, HTML oluşturuluyor...\n\n";
        
        // BannerController ile HTML oluştur
        $bannerController = new BannerController();
        $htmlOutput = $bannerController->renderBannerHTML($bannerData);
        
        echo "📄 OLUŞTURULAN HTML:\n";
        echo "====================\n";
        echo $htmlOutput . "\n\n";
        
        // Test HTML dosyası oluştur
        $this->createTestHTMLFile($htmlOutput, $bannerData);
        
        // CSS analizi yap
        $this->analyzeCSSClasses($htmlOutput);
        
        return $htmlOutput;
    }
    
    private function getTopBannerData()
    {
        try {
            // Banner grubu bilgisini al
            $stmt = $this->pdo->prepare("SELECT * FROM banner_groups WHERE id = 2");
            $stmt->execute();
            $group = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$group) {
                return null;
            }
            
            // Layout bilgisini al
            $stmt = $this->pdo->prepare("SELECT * FROM banner_layouts WHERE id = ?");
            $stmt->execute([$group['layout_id']]);
            $layout = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Banner verilerini al
            $stmt = $this->pdo->prepare("SELECT * FROM banners WHERE group_id = 2 AND active = 1");
            $stmt->execute();
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($banners)) {
                return null;
            }
              // BannerController'ın beklediği formatta veri oluştur
            return [
                'group_info' => [
                    'id' => $group['id'],
                    'style_class' => $group['style_class']
                ],
                'layout_info' => [
                    'layout_group' => 'text_and_image', // BannerController'ın anladığı değer
                    'layout_view' => $layout['layout_view'],
                    'columns' => $layout['columns']
                ],
                'type_id' => 2, // Tepe banner type ID
                'type_name' => 'Tepe Banner',
                'banners' => array_map(function($banner) {
                    return [
                        'id' => $banner['id'],
                        'title' => $banner['title'],
                        'content' => $banner['content'],
                        'image' => $banner['image'],
                        'link' => $banner['link'],
                        'style' => [
                            'show_button' => 1,
                            'button_title' => 'Detaylar'
                        ]
                    ];
                }, $banners)
            ];
            
        } catch (Exception $e) {
            echo "❌ Veri alma hatası: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    private function createTestHTMLFile($htmlOutput, $bannerData)
    {
        $testHTML = '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tepe Banner Test</title>
    <link rel="stylesheet" href="../../Public/CSS/Banners/tepe-banner.css">
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-header {
            background: #007cba;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-radius: 8px 8px 0 0;
        }
        .banner-debug {
            border: 2px dashed #ccc;
            margin: 20px 0;
            position: relative;
        }
        .banner-debug::before {
            content: "Banner Çıktısı";
            position: absolute;
            top: -10px;
            left: 10px;
            background: white;
            padding: 5px 10px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>🎯 Tepe Banner Test Sayfası</h1>
            <p>Oluşturulma: ' . date('d.m.Y H:i:s') . '</p>
        </div>
        
        <h2>📊 Banner Bilgileri:</h2>
        <ul>
            <li><strong>Group ID:</strong> ' . $bannerData['group_info']['id'] . '</li>
            <li><strong>Style Class:</strong> ' . $bannerData['group_info']['style_class'] . '</li>
            <li><strong>Layout Group:</strong> ' . $bannerData['layout_info']['layout_group'] . '</li>
            <li><strong>Layout View:</strong> ' . $bannerData['layout_info']['layout_view'] . '</li>
            <li><strong>Banner Sayısı:</strong> ' . count($bannerData['banners']) . '</li>
        </ul>
        
        <div class="banner-debug">
            ' . $htmlOutput . '
        </div>
        
        <h2>🔍 CSS Sınıf Kontrol Listesi:</h2>
        <div id="css-check-results">
            <p>Sayfa yüklendikten sonra JavaScript ile kontrol edilecek...</p>
        </div>
    </div>
    
    <script>
        // CSS sınıflarının mevcut olup olmadığını kontrol et
        document.addEventListener("DOMContentLoaded", function() {
            const banner = document.querySelector(".banner-type-tepe-banner");
            const image = document.querySelector(".banner-image img");
            const title = document.querySelector(".title");
            const content = document.querySelector(".content");
            const button = document.querySelector(".banner-button");
            
            let results = "<ul>";
            results += banner ? "<li>✅ .banner-type-tepe-banner bulundu</li>" : "<li>❌ .banner-type-tepe-banner bulunamadı</li>";
            results += image ? "<li>✅ Banner görseli bulundu</li>" : "<li>❌ Banner görseli bulunamadı</li>";
            results += title ? "<li>✅ Banner başlığı bulundu</li>" : "<li>❌ Banner başlığı bulunamadı</li>";
            results += content ? "<li>✅ Banner içeriği bulundu</li>" : "<li>❌ Banner içeriği bulunamadı</li>";
            results += button ? "<li>✅ Banner butonu bulundu</li>" : "<li>❌ Banner butonu bulunamadı</li>";
            results += "</ul>";
            
            document.getElementById("css-check-results").innerHTML = results;
            
            // Görsel yükleme kontrolü
            if (image) {
                image.onload = function() {
                    console.log("✅ Görsel yüklendi:", this.src);
                };
                image.onerror = function() {
                    console.log("❌ Görsel yüklenemedi:", this.src);
                    this.style.border = "2px dashed red";
                    this.alt = "GÖRSEL BULUNAMADI: " + this.src;
                };
            }
        });
    </script>
</body>
</html>';
        
        $testFilePath = dirname(__DIR__) . '/Temp/tepe-banner-live-test.html';
        
        // Temp klasörünü oluştur
        if (!is_dir(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0755, true);
        }
        
        file_put_contents($testFilePath, $testHTML);
        
        echo "📁 Test HTML dosyası oluşturuldu: Tests/Temp/tepe-banner-live-test.html\n";
        echo "🌐 Tarayıcıda görüntülemek için dosyayı açın.\n\n";
    }
    
    private function analyzeCSSClasses($htmlOutput)
    {
        echo "🎨 CSS SINIF ANALİZİ:\n";
        echo "=====================\n";
        
        // HTML'den CSS sınıflarını çıkar
        preg_match_all('/class=["\']([^"\']+)["\']/', $htmlOutput, $matches);
        
        if (empty($matches[1])) {
            echo "❌ HTML'de CSS sınıfı bulunamadı!\n";
            return;
        }
        
        $allClasses = [];
        foreach ($matches[1] as $classString) {
            $classes = explode(' ', $classString);
            $allClasses = array_merge($allClasses, $classes);
        }
        
        $uniqueClasses = array_unique(array_filter($allClasses));
        
        echo "📋 Kullanılan CSS sınıfları:\n";
        foreach ($uniqueClasses as $class) {
            echo "   - .{$class}\n";
        }
        
        // CSS dosyasında bu sınıfların mevcut olup olmadığını kontrol et
        $cssPath = dirname(__DIR__, 2) . '/Public/CSS/Banners/tepe-banner.css';
        
        if (file_exists($cssPath)) {
            $cssContent = file_get_contents($cssPath);
            
            echo "\n🔍 CSS dosyasında sınıf kontrolü:\n";
            $missingClasses = [];
            
            foreach ($uniqueClasses as $class) {
                if (strpos($cssContent, ".{$class}") !== false) {
                    echo "   ✅ .{$class} - Mevcut\n";
                } else {
                    echo "   ❌ .{$class} - Eksik\n";
                    $missingClasses[] = $class;
                }
            }
            
            if (!empty($missingClasses)) {
                echo "\n⚠️ Eksik CSS sınıfları tespit edildi!\n";
                echo "Bu sınıflar tepe-banner.css'e eklenmelidir.\n";
            }
            
        } else {
            echo "\n❌ CSS dosyası bulunamadı: {$cssPath}\n";
        }
    }
}

// Script'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'TopBannerHTMLTester.php') {
    try {
        $tester = new TopBannerHTMLTester();
        $htmlOutput = $tester->testTopBannerHTML();
        
        echo "\n=== TEST TAMAMLANDI ===\n";
        echo "HTML çıktısı oluşturuldu ve analiz edildi.\n";
        
    } catch (Exception $e) {
        echo "❌ Test hatası: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
}
