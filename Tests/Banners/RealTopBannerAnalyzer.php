<?php
/**
 * Gerçek Tablo Yapısına Göre Tepe Banner Analizi
 */

try {
    $pdo = new PDO("mysql:host=localhost;dbname=yeni.globalpozitif.com.tr;charset=utf8mb4", 'root', 'Global2019*', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "=== TEPE BANNER DETAYLI ANALİZ ===\n\n";
    
    // 1. Banner tiplerini bul
    echo "1. BANNER TİPLERİ\n";
    echo "================\n";
    $stmt = $pdo->query("SELECT * FROM banner_types ORDER BY id");
    $topTypeId = null;
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']} | {$row['type_name']}\n";
        if (stripos($row['type_name'], 'tepe') !== false || stripos($row['type_name'], 'top') !== false) {
            echo "   🎯 TEPE BANNER TİPİ BULUNDU!\n";
            $topTypeId = $row['id'];
        }
    }
    echo "\n";
    
    // 2. Tepe banner layoutlarını bul
    echo "2. TEPE BANNER LAYOUTLARI\n";
    echo "========================\n";
    if ($topTypeId) {
        $stmt = $pdo->prepare("
            SELECT bl.*, bt.type_name 
            FROM banner_layouts bl 
            JOIN banner_types bt ON bl.type_id = bt.id 
            WHERE bl.type_id = ?
            ORDER BY bl.id
        ");
        $stmt->execute([$topTypeId]);
        
        $topLayouts = [];
        while ($row = $stmt->fetch()) {
            $topLayouts[] = $row;
            echo "Layout ID: {$row['id']}\n";
            echo "  Tip: {$row['type_name']}\n";
            echo "  Grup: {$row['layout_group']}\n";
            echo "  View: {$row['layout_view']}\n";
            echo "  Ad: {$row['layout_name']}\n";
            echo "  Açıklama: {$row['description']}\n";
            echo "  Columns: {$row['columns']}\n";
            echo "  Max Banners: {$row['max_banners']}\n";
            echo "  ----\n";
        }
    } else {
        echo "❌ Tepe banner tipi bulunamadı!\n";
    }
    echo "\n";
    
    // 3. Tepe banner gruplarını bul
    echo "3. TEPE BANNER GRUPLARI\n";
    echo "======================\n";
    if (!empty($topLayouts)) {
        $layoutIds = array_column($topLayouts, 'id');
        $placeholders = str_repeat('?,', count($layoutIds) - 1) . '?';
        
        $stmt = $pdo->prepare("
            SELECT bg.*, bl.layout_name, bl.layout_group, bl.layout_view
            FROM banner_groups bg
            JOIN banner_layouts bl ON bg.layout_id = bl.id
            WHERE bg.layout_id IN ({$placeholders})
            ORDER BY bg.id
        ");
        $stmt->execute($layoutIds);
        
        $topGroups = [];
        while ($row = $stmt->fetch()) {
            $topGroups[] = $row;
            echo "Grup ID: {$row['id']}\n";
            echo "  Ad: {$row['group_name']}\n";
            echo "  Başlık: {$row['group_title']}\n";
            echo "  Açıklama: {$row['group_desc']}\n";
            echo "  Layout: {$row['layout_name']} ({$row['layout_group']}/{$row['layout_view']})\n";
            echo "  Görünüm: {$row['group_view']}\n";
            echo "  Columns: {$row['columns']}\n";
            echo "  Alignment: {$row['content_alignment']}\n";
            echo "  Style Class: {$row['style_class']}\n";
            echo "  Background: {$row['background_color']}\n";
            echo "  Title Color: {$row['group_title_color']}\n";
            echo "  Desc Color: {$row['group_desc_color']}\n";
            echo "  Full Size: " . ($row['group_full_size'] ? 'Evet' : 'Hayır') . "\n";
            echo "  Custom CSS: " . (!empty($row['custom_css']) ? 'Mevcut' : 'Yok') . "\n";
            echo "  ----\n";
        }
    }
    echo "\n";
    
    // 4. Aktif tepe bannerları
    echo "4. AKTİF TEPE BANNERLAR\n";
    echo "======================\n";
    if (!empty($topGroups)) {
        $groupIds = array_column($topGroups, 'id');
        $placeholders = str_repeat('?,', count($groupIds) - 1) . '?';
        
        $stmt = $pdo->prepare("
            SELECT b.*, bg.group_name, bg.group_title, bs.banner_height_size, 
                   bs.background_color, bs.title_color, bs.content_color,
                   bs.show_button, bs.button_title, bs.button_background, bs.button_color
            FROM banners b
            JOIN banner_groups bg ON b.group_id = bg.id
            LEFT JOIN banner_styles bs ON b.style_id = bs.id
            WHERE b.group_id IN ({$placeholders}) AND b.active = 1
            ORDER BY b.id
        ");
        $stmt->execute($groupIds);
        
        $bannerCount = 0;
        while ($row = $stmt->fetch()) {
            $bannerCount++;
            echo "🎯 BANNER #{$bannerCount} - ID: {$row['id']}\n";
            echo "  Grup: {$row['group_name']} ({$row['group_title']})\n";
            echo "  ----\n";
            echo "  📝 İÇERİK:\n";
            echo "    Başlık: " . (!empty($row['title']) ? "✅ '{$row['title']}'" : "❌ BOŞ") . "\n";
            echo "    İçerik: " . (!empty($row['content']) ? "✅ " . strlen(strip_tags($row['content'])) . " karakter" : "❌ BOŞ") . "\n";
            if (!empty($row['content'])) {
                echo "      Önizleme: " . substr(strip_tags($row['content']), 0, 100) . "...\n";
            }
            echo "    Resim: " . (!empty($row['image']) ? "✅ '{$row['image']}'" : "❌ BOŞ") . "\n";
            echo "    Link: " . (!empty($row['link']) ? "✅ '{$row['link']}'" : "❌ BOŞ") . "\n";
            echo "  ----\n";
            echo "  🎨 STIL (Banner Style ID: {$row['style_id']}):\n";
            echo "    Banner Yükseklik: {$row['banner_height_size']}px\n";
            echo "    Arkaplan: {$row['background_color']}\n";
            echo "    Başlık Renk: {$row['title_color']}\n";
            echo "    İçerik Renk: {$row['content_color']}\n";
            echo "    Buton Göster: " . ($row['show_button'] ? 'Evet' : 'Hayır') . "\n";
            echo "    Buton Metni: {$row['button_title']}\n";
            echo "    Buton Arkaplan: {$row['button_background']}\n";
            echo "    Buton Renk: {$row['button_color']}\n";
            echo "  ====\n\n";
            
            // Dosya kontrolü
            if (!empty($row['image'])) {
                $imagePath = dirname(__DIR__, 2) . '/Public/Image/' . $row['image'];
                if (file_exists($imagePath)) {
                    $imageSize = getimagesize($imagePath);
                    echo "  ✅ Resim dosyası mevcut: {$imagePath}\n";
                    echo "     Boyut: {$imageSize[0]}x{$imageSize[1]} px\n";
                } else {
                    echo "  ❌ Resim dosyası eksik: {$imagePath}\n";
                }
            }
        }
        
        if ($bannerCount === 0) {
            echo "❌ Aktif tepe banner bulunamadı!\n";
        } else {
            echo "📊 Toplam {$bannerCount} aktif tepe banner bulundu.\n";
        }
    }
    echo "\n";
    
    // 5. CSS ve template dosya kontrolü
    echo "5. DOSYA KONTROL\n";
    echo "===============\n";
    
    $cssPath = dirname(__DIR__, 2) . '/Public/CSS/Banners/tepe-banner.css';
    if (file_exists($cssPath)) {
        $cssSize = filesize($cssPath);
        echo "✅ tepe-banner.css mevcut ({$cssSize} bytes)\n";
    } else {
        echo "❌ tepe-banner.css bulunamadı!\n";
    }
    
    $minCssPath = dirname(__DIR__, 2) . '/Public/CSS/Banners/tepe-banner.min.css';
    if (file_exists($minCssPath)) {
        $minCssSize = filesize($minCssPath);
        echo "✅ tepe-banner.min.css mevcut ({$minCssSize} bytes)\n";
    } else {
        echo "❌ tepe-banner.min.css bulunamadı!\n";
    }
    
    // View dosyalarını kontrol et
    $viewPath = dirname(__DIR__, 2) . '/App/View/';
    echo "\n📁 View klasörü: {$viewPath}\n";
    if (is_dir($viewPath)) {
        $viewFiles = glob($viewPath . '*banner*');
        if (!empty($viewFiles)) {
            echo "Banner view dosyaları:\n";
            foreach ($viewFiles as $file) {
                echo "  - " . basename($file) . "\n";
            }
        } else {
            echo "❌ Banner view dosyası bulunamadı!\n";
        }
    }
    
    echo "\n=== ANALİZ TAMAMLANDI ===\n";
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}
