<?php
/**
 * Canlı Site Tepe Banner Analizi
 * Tepe banner ortalama sorunu için detaylı analiz
 */

// Proje konfigürasyonunu yükle
require_once 'App/Core/Config.php';

try {
    $config = new Config();
    
    echo "🔍 Canlı Site Tepe Banner Analizi\n";
    echo "==================================\n\n";
    
    // 1. Banner veritabanı kontrolü
    echo "1️⃣ Banner Veritabanı Kontrolü:\n";
    echo "-------------------------------\n";
    
    $db = new Database($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);
    
    // Tepe banner bilgilerini al
    $query = "SELECT bg.*, bt.name as type_name, bl.layout_group, bl.layout_view 
              FROM banner_groups bg 
              JOIN banner_types bt ON bg.type_id = bt.id 
              LEFT JOIN banner_layouts bl ON bg.layout_id = bl.id 
              WHERE bt.id = 2 AND bg.is_active = 1 
              ORDER BY bg.sort_order";
    
    $result = $db->query($query);
    $topBanners = $result->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($topBanners)) {
        echo "✓ Tepe banner grupları bulundu: " . count($topBanners) . " adet\n";
        
        foreach ($topBanners as $banner) {
            echo "  • Grup ID: {$banner['id']}\n";
            echo "    Başlık: {$banner['group_title']}\n";
            echo "    Layout: {$banner['layout_group']}\n";
            echo "    Tam boyut: " . ($banner['group_full_size'] ? 'Evet' : 'Hayır') . "\n";
            echo "    Stil sınıfı: {$banner['style_class']}\n\n";
        }
    } else {
        echo "✗ Aktif tepe banner bulunamadı\n";
    }
    
    // 2. CSS dosyalarını kontrol et
    echo "2️⃣ CSS Dosyalarını Kontrol:\n";
    echo "-----------------------------\n";
    
    $cssFiles = [
        'Public/CSS/Banners/tepe-banner.css' => 'Tepe banner CSS',
        'Public/CSS/Banners/tepe-banner.min.css' => 'Minified tepe banner CSS',
        'Public/CSS/Banners/banner-fixes.css' => 'Banner düzeltme CSS'
    ];
    
    foreach ($cssFiles as $file => $desc) {
        if (file_exists($file)) {
            $size = filesize($file);
            echo "✓ $desc: $file ($size bytes)\n";
            
            // CSS içeriğinde ortalama kurallarını ara
            $content = file_get_contents($file);
            if (strpos($content, 'margin: 0 auto') !== false) {
                echo "  ✓ margin: 0 auto kuralı mevcut\n";
            }
            if (strpos($content, 'text-align: center') !== false) {
                echo "  ✓ text-align: center kuralı mevcut\n";
            }
            if (strpos($content, 'banner-group-2') !== false) {
                echo "  ✓ banner-group-2 sınıfı mevcut\n";
            }
        } else {
            echo "✗ $desc: $file (bulunamadı)\n";
        }
    }
    
    // 3. BannerController kontrolü
    echo "\n3️⃣ BannerController Kontrol:\n";
    echo "-----------------------------\n";
    
    if (file_exists('App/Controller/BannerController.php')) {
        $controllerContent = file_get_contents('App/Controller/BannerController.php');
        
        echo "✓ BannerController bulundu\n";
        
        // Tepe banner için özel CSS kontrolü
        if (strpos($controllerContent, 'bannerType == 2') !== false) {
            echo "  ✓ Tepe banner (tip 2) kontrolü mevcut\n";
        }
        
        if (strpos($controllerContent, 'margin: 0 auto !important') !== false) {
            echo "  ✓ Dinamik ortalama CSS mevcut\n";
        }
        
        if (strpos($controllerContent, 'text-align: center !important') !== false) {
            echo "  ✓ Dinamik metin ortalama CSS mevcut\n";
        }
    }
    
    // 4. Tema değişkenleri kontrolü
    echo "\n4️⃣ Tema Değişkenleri Kontrolü:\n";
    echo "-------------------------------\n";
    
    if (file_exists('Public/CSS/index.css')) {
        $themeContent = file_get_contents('Public/CSS/index.css');
        
        $themeVars = [
            '--content-max-width' => 'İçerik maksimum genişlik',
            '--primary-color' => 'Ana renk',
            '--border-radius-base' => 'Köşe yuvarlaklığı',
            '--transition-speed' => 'Geçiş hızı'
        ];
        
        foreach ($themeVars as $var => $desc) {
            if (strpos($themeContent, $var) !== false) {
                preg_match('/' . preg_quote($var) . ':\s*([^;]+);/', $themeContent, $matches);
                $value = isset($matches[1]) ? trim($matches[1]) : 'bulunamadı';
                echo "  ✓ $desc ($var): $value\n";
            } else {
                echo "  ✗ $desc ($var): bulunamadı\n";
            }
        }
    }
    
    echo "\n🔧 SORUN TESPİTİ VE ÇÖZÜMLERİ:\n";
    echo "================================\n";
    
    echo "Muhtemel sorunlar:\n";
    echo "1. CSS'ler head'e yüklenmiyor\n";
    echo "2. CSS sınıf isimleri banner HTML'inde farklı\n";
    echo "3. Başka CSS kuralları override ediyor\n";
    echo "4. JavaScript ile layout değiştiriliyor\n\n";
    
    echo "Önerilen çözümler:\n";
    echo "1. CSS yükleme durumunu kontrol et\n";
    echo "2. Banner HTML yapısını incele\n";
    echo "3. !important kurallarını güçlendir\n";
    echo "4. Inline style ekle\n";
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}
?>
