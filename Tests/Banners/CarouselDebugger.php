<?php
/**
 * Carousel Banner Debug Test
 * Bu dosya carousel butonlarının neden çalışmadığını tespit etmek için oluşturulmuştur
 */

// Proje kök dizinini belirle
$projectRoot = realpath(__DIR__ . '/../../');

// Gerekli dosyaları yükle
require_once $projectRoot . '/App/Core/Config.php';
require_once $projectRoot . '/App/Controller/BannerController.php';
require_once $projectRoot . '/App/Model/Banner.php';
require_once $projectRoot . '/App/Helpers/Helper.php';

// Config'i başlat
$config = new Config();

// Test için örnek carousel banner verisi oluştur
$testBannerData = [
    [
        'group_info' => [
            'id' => 999,
            'group_title_color' => '#333333',
            'group_desc_color' => '#666666',
            'background_color' => '#ffffff',
            'group_full_size' => 1,
            'banner_full_size' => 1,
            'custom_css' => '',
            'style_class' => 'Carousel',
            'group_view' => 'triple'
        ],
        'layout_info' => [
            'layout_group' => 'carousel',
            'layout_view' => 'box',
            'columns' => 3
        ],
        'type_id' => 2,
        'type_name' => 'Tepe Banner',
        'page_id' => null,
        'category_id' => null,
        'banners' => [
            [
                'id' => 1001,
                'title' => 'Test Banner 1',
                'content' => 'Bu test banner içeriğidir 1',
                'image' => 'Banner/test1.jpg',
                'link' => '#',
                'style' => [
                    'background_color' => '#f8f9fa',
                    'content_box_bg_color' => '#ffffff',
                    'title_color' => '#333333',
                    'title_size' => 18,
                    'content_color' => '#666666',
                    'content_size' => 14,
                    'show_button' => 0,
                    'button_text' => '',
                    'button_color' => '#007bff',
                    'button_bg_color' => '#ffffff'
                ]
            ],
            [
                'id' => 1002,
                'title' => 'Test Banner 2',
                'content' => 'Bu test banner içeriğidir 2',
                'image' => 'Banner/test2.jpg',
                'link' => '#',
                'style' => [
                    'background_color' => '#f8f9fa',
                    'content_box_bg_color' => '#ffffff',
                    'title_color' => '#333333',
                    'title_size' => 18,
                    'content_color' => '#666666',
                    'content_size' => 14,
                    'show_button' => 0,
                    'button_text' => '',
                    'button_color' => '#007bff',
                    'button_bg_color' => '#ffffff'
                ]
            ],
            [
                'id' => 1003,
                'title' => 'Test Banner 3',
                'content' => 'Bu test banner içeriğidir 3',
                'image' => 'Banner/test3.jpg',
                'link' => '#',
                'style' => [
                    'background_color' => '#f8f9fa',
                    'content_box_bg_color' => '#ffffff',
                    'title_color' => '#333333',
                    'title_size' => 18,
                    'content_color' => '#666666',
                    'content_size' => 14,
                    'show_button' => 0,
                    'button_text' => '',
                    'button_color' => '#007bff',
                    'button_bg_color' => '#ffffff'
                ]
            ],
            [
                'id' => 1004,
                'title' => 'Test Banner 4',
                'content' => 'Bu test banner içeriğidir 4',
                'image' => 'Banner/test4.jpg',
                'link' => '#',
                'style' => [
                    'background_color' => '#f8f9fa',
                    'content_box_bg_color' => '#ffffff',
                    'title_color' => '#333333',
                    'title_size' => 18,
                    'content_color' => '#666666',
                    'content_size' => 14,
                    'show_button' => 0,
                    'button_text' => '',
                    'button_color' => '#007bff',
                    'button_bg_color' => '#ffffff'
                ]
            ]
        ]
    ]
];

// BannerController'ı başlat
$bannerController = new BannerController($testBannerData);

// Carousel banner'ı render et
$result = $bannerController->renderBannersByType(2); // Tepe Banner tipi

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel Debug Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .debug-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .debug-info h3 {
            color: #333;
            margin-top: 0;
        }
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
        .banner-test-area {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .console-output {
            background: #000;
            color: #0f0;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
        }
        
        /* Test için carousel CSS'i dahil et */
        <?= $result['css'] ?>
    </style>
</head>
<body>
    <div class="debug-info">
        <h3>🔍 Carousel Debug Test Sayfası</h3>
        <p><strong>Test Amacı:</strong> Carousel butonlarının neden çalışmadığını tespit etmek</p>
        <p><strong>Test Zamanı:</strong> <?= date('Y-m-d H:i:s') ?></p>
        
        <h4>Render Edilen Banner Sayısı:</h4>
        <p><?= count($result['banners']) ?> adet banner grubu</p>
        
        <h4>Üretilen CSS Uzunluğu:</h4>
        <p><?= strlen($result['css']) ?> karakter</p>
        
        <h4>Üretilen JS Uzunluğu:</h4>
        <p><?= strlen($result['js']) ?> karakter</p>
    </div>

    <div class="debug-info">
        <h3>📝 Üretilen JavaScript Kodu</h3>
        <div class="code-block"><?= htmlspecialchars($result['js']) ?></div>
    </div>

    <div class="debug-info">
        <h3>🎨 Üretilen CSS Kodu</h3>
        <div class="code-block"><?= htmlspecialchars($result['css']) ?></div>
    </div>        <div class="debug-info">
            <h3>🔧 Carousel Buton Test Alanı</h3>
            <div style="display: flex; gap: 10px; margin: 10px 0;">
                <button onclick="manualTestPrev()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    ← Test Prev
                </button>
                <button onclick="manualTestNext()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Test Next →
                </button>
                <button onclick="testButtons()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Otomatik Test
                </button>
            </div>
            <p><em>Bu butonlar carousel butonlarını programatik olarak test eder.</em></p>
        </div>

        <div class="banner-test-area">
            <h3>🧪 Canlı Test Alanı</h3>
            <?= $result['html'] ?>
        </div>

    <div class="debug-info">
        <h3>💻 Console Çıktıları</h3>
        <div id="console-output" class="console-output">Console çıktıları burada görünecek...</div>
    </div>

    <script>
        // Console log'larını yakalayıp sayfada göster
        (function() {
            const originalLog = console.log;
            const originalError = console.error;
            const originalWarn = console.warn;
            const consoleOutput = document.getElementById('console-output');
            
            function addToOutput(type, args) {
                const timestamp = new Date().toLocaleTimeString();
                const message = Array.from(args).map(arg => 
                    typeof arg === 'object' ? JSON.stringify(arg, null, 2) : String(arg)
                ).join(' ');
                
                consoleOutput.innerHTML += `[${timestamp}] ${type.toUpperCase()}: ${message}\n`;
                consoleOutput.scrollTop = consoleOutput.scrollHeight;
            }
            
            console.log = function() {
                originalLog.apply(console, arguments);
                addToOutput('log', arguments);
            };
            
            console.error = function() {
                originalError.apply(console, arguments);
                addToOutput('error', arguments);
            };
            
            console.warn = function() {
                originalWarn.apply(console, arguments);
                addToOutput('warn', arguments);
            };
            
            // İlk mesaj
            console.log('Debug sayfası yüklendi. Carousel test başlıyor...');
            
            // Banner butonlarını test et
            setTimeout(function() {
                console.log('Banner butonları test ediliyor...');
                
                const bannerGroup = document.querySelector('.banner-group-999');
                console.log('Banner grubu bulundu mu?', bannerGroup ? 'EVET' : 'HAYIR');
                
                if (bannerGroup) {
                    const prevButton = bannerGroup.querySelector('.prev-carousel');
                    const nextButton = bannerGroup.querySelector('.next-carousel');
                    
                    console.log('Prev button bulundu mu?', prevButton ? 'EVET' : 'HAYIR');
                    console.log('Next button bulundu mu?', nextButton ? 'EVET' : 'HAYIR');
                    
                    if (prevButton) {
                        console.log('Prev button HTML:', prevButton.outerHTML);
                        console.log('Prev button onclick eventi:', prevButton.onclick);
                        console.log('Prev button event listeners:', getEventListeners ? getEventListeners(prevButton) : 'DevTools gerekli');
                    }
                    
                    if (nextButton) {
                        console.log('Next button HTML:', nextButton.outerHTML);
                        console.log('Next button onclick eventi:', nextButton.onclick);
                        console.log('Next button event listeners:', getEventListeners ? getEventListeners(nextButton) : 'DevTools gerekli');
                    }
                    
                    const carouselContainer = bannerGroup.querySelector('.carousel-container');
                    console.log('Carousel container bulundu mu?', carouselContainer ? 'EVET' : 'HAYIR');
                    
                    if (carouselContainer) {
                        const items = carouselContainer.querySelectorAll('.banner-item');
                        console.log('Banner item sayısı:', items.length);
                        console.log('Container scroll genişliği:', carouselContainer.scrollWidth);
                        console.log('Container görünür genişliği:', carouselContainer.clientWidth);
                    }
                }
            }, 1000);
              // Manuel buton test fonksiyonları
            window.manualTestPrev = function() {
                console.log('Manuel Prev test başlatılıyor...');
                const prevButton = document.querySelector('.prev-carousel');
                if (prevButton) {
                    console.log('Prev button bulundu, tıklanıyor...');
                    prevButton.click();
                } else {
                    console.error('Prev button bulunamadı!');
                }
            };
            
            window.manualTestNext = function() {
                console.log('Manuel Next test başlatılıyor...');
                const nextButton = document.querySelector('.next-carousel');
                if (nextButton) {
                    console.log('Next button bulundu, tıklanıyor...');
                    nextButton.click();
                } else {
                    console.error('Next button bulunamadı!');
                }
            };
            
            // Manuel buton test fonksiyonu
            window.testButtons = function() {
                console.log('Otomatik buton testi başlatılıyor...');
                const prevButton = document.querySelector('.prev-carousel');
                const nextButton = document.querySelector('.next-carousel');
                
                if (prevButton) {
                    console.log('Prev button tıklanıyor...');
                    prevButton.click();
                } else {
                    console.error('Prev button bulunamadı!');
                }
                
                setTimeout(function() {
                    if (nextButton) {
                        console.log('Next button tıklanıyor...');
                        nextButton.click();
                    } else {
                        console.error('Next button bulunamadı!');
                    }
                }, 1000);
            };
        })();
        
        // Sayfa içindeki JS kodunu çalıştır
        <?= $result['js'] ?>
    </script>
    
    <div class="debug-info">
        <h3>🎮 Manuel Test Alanı</h3>
        <button onclick="testButtons()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Butonları Manuel Test Et
        </button>
        <p><em>Bu buton carousel butonlarını programatik olarak tıklar ve sonuçları console'da gösterir.</em></p>
    </div>
</body>
</html>
