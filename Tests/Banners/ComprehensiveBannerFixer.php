<?php
/**
 * Banner CSS Genel Düzeltici ve Optimizator
 * Tüm banner tiplerini (tepe, icon, vs.) düzeltir
 */

echo "Banner CSS Genel Düzeltici\n";
echo "===========================\n\n";

// Minified CSS dosyalarını güncelle
$cssFiles = [
    'IconFeatureCard.min.css' => 'icon banner',
    'tepe-banner.min.css' => 'tepe banner',
    'orta-banner.min.css' => 'orta banner',
    'alt-banner.min.css' => 'alt banner'
];

$updatedFiles = [];

foreach ($cssFiles as $fileName => $description) {
    $filePath = "Public/CSS/Banners/{$fileName}";
    
    if (!file_exists($filePath)) {
        echo "⚠️ Dosya bulunamadı: {$fileName}\n";
        continue;
    }
    
    $originalCSS = file_get_contents($filePath);
    $updatedCSS = $originalCSS;
    
    echo "🔧 Düzeltiliyor: {$fileName} ({$description})\n";
    
    // Icon Banner düzeltmeleri
    if (strpos($fileName, 'Icon') !== false) {
        // Grid düzeni düzeltmeleri
        $iconFixes = "
        /* Grid container düzeltmeleri */
        .banner-group-3 {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
            gap: 20px !important;
            margin: 0 auto !important;
            max-width: 1400px !important;
            padding: 20px !important;
        }
        
        /* Icon banner item düzeltmeleri */
        .IconFeatureCard .banner-item {
            background: #ffffff !important;
            border: 1px solid #e5e5e5 !important;
            border-radius: 8px !important;
            padding: 30px 20px !important;
            text-align: center !important;
            transition: all 0.3s ease !important;
            height: auto !important;
            min-height: 200px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
        }
        
        /* Icon hover efekti */
        .IconFeatureCard .banner-item:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
            border-color: #ddd !important;
        }
        
        /* Icon resim düzeltmeleri */
        .IconFeatureCard .banner-image {
            width: 64px !important;
            height: 64px !important;
            margin: 0 auto 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .IconFeatureCard .banner-image img {
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
        }
        
        /* Icon başlık düzeltmeleri */
        .IconFeatureCard .banner-title {
            font-size: 18px !important;
            font-weight: 600 !important;
            margin: 0 0 10px !important;
            color: #333 !important;
        }
        
        /* Icon açıklama düzeltmeleri */
        .IconFeatureCard .banner-content {
            font-size: 14px !important;
            color: #666 !important;
            line-height: 1.5 !important;
            margin: 0 !important;
        }
        
        /* Responsive düzeltmeler */
        @media (max-width: 768px) {
            .banner-group-3 {
                grid-template-columns: 1fr !important;
                padding: 15px !important;
            }
        }
        
        @media (max-width: 480px) {
            .IconFeatureCard .banner-item {
                padding: 20px 15px !important;
                min-height: 150px !important;
            }
        }
        ";
        
        // Minified hale getir
        $iconFixes = preg_replace('/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $iconFixes);
        $iconFixes = str_replace(["\r\n", "\r", "\n", "\t"], '', $iconFixes);
        $iconFixes = preg_replace('/\s+/', ' ', $iconFixes);
        $iconFixes = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;', ', ', ' ,'], ['{', '{', '}', '}', ':', ':', ';', ';', ',', ','], $iconFixes);
        
        $updatedCSS .= $iconFixes;
    }
    
    // Tepe Banner düzeltmeleri
    if (strpos($fileName, 'tepe') !== false) {
        $tepeFixes = "
        /* Tepe banner ortalama düzeltmeleri */
        .banner-group-2 {
            margin: 0 auto !important;
            max-width: 1400px !important;
            width: 100% !important;
            display: block !important;
        }
        
        .banner-group-2 .banner-container {
            margin: 0 auto !important;
            text-align: center !important;
            display: block !important;
        }
        
        .banner-type-tepe-banner .banner-item {
            text-align: center !important;
            margin: 0 auto !important;
        }
        
        /* Responsive ortalama */
        @media (max-width: 1400px) {
            .banner-group-2 {
                max-width: 95% !important;
                padding: 0 20px !important;
            }
        }
        
        @media (max-width: 768px) {
            .banner-group-2 {
                max-width: 100% !important;
                padding: 0 15px !important;
            }
        }
        ";
        
        // Minified hale getir
        $tepeFixes = preg_replace('/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $tepeFixes);
        $tepeFixes = str_replace(["\r\n", "\r", "\n", "\t"], '', $tepeFixes);
        $tepeFixes = preg_replace('/\s+/', ' ', $tepeFixes);
        $tepeFixes = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;', ', ', ' ,'], ['{', '{', '}', '}', ':', ':', ';', ';', ',', ','], $tepeFixes);
        
        $updatedCSS .= $tepeFixes;
    }
    
    // Dosyayı güncelle
    if ($updatedCSS !== $originalCSS) {
        file_put_contents($filePath, $updatedCSS);
        $updatedFiles[] = $fileName;
        echo "  ✅ Düzeltmeler uygulandı\n";
    } else {
        echo "  ℹ️ Değişiklik gerekmedi\n";
    }
}

echo "\n📊 Özet:\n";
echo "--------\n";
echo "Güncellenen dosyalar: " . count($updatedFiles) . "\n";
foreach ($updatedFiles as $file) {
    echo "  - {$file}\n";
}

// Yeni genel düzeltme CSS'i oluştur
$generalFixesCSS = "
/* ==============================================
   GENEL BANNER DÜZELTMELERİ - RESPONSIVE & CENTERED
   ============================================== */

/* Tepe Banner Ortalama */
.banner-group-2 {
    margin: 0 auto !important;
    max-width: 1400px !important;
    width: 100% !important;
    display: block !important;
}

.banner-group-2 .banner-container {
    margin: 0 auto !important;
    text-align: center !important;
}

/* Icon Banner Grid */
.banner-group-3 {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
    gap: 25px !important;
    margin: 0 auto !important;
    max-width: 1400px !important;
    padding: 30px 20px !important;
}

/* Icon Banner Items */
.IconFeatureCard .banner-item {
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 10px !important;
    padding: 35px 25px !important;
    text-align: center !important;
    transition: all 0.3s ease !important;
    min-height: 220px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: center !important;
}

.IconFeatureCard .banner-item:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    border-color: #ccc !important;
}

/* Responsive Düzeltmeler */
@media (max-width: 768px) {
    .banner-group-2 {
        max-width: 100% !important;
        padding: 0 15px !important;
    }
    
    .banner-group-3 {
        grid-template-columns: 1fr !important;
        padding: 20px 15px !important;
        gap: 20px !important;
    }
    
    .IconFeatureCard .banner-item {
        padding: 25px 20px !important;
        min-height: 180px !important;
    }
}

@media (max-width: 480px) {
    .banner-group-2 {
        padding: 0 10px !important;
    }
    
    .banner-group-3 {
        padding: 15px 10px !important;
    }
    
    .IconFeatureCard .banner-item {
        padding: 20px 15px !important;
        min-height: 160px !important;
    }
}
";

// Genel düzeltme CSS'ini minify et ve kaydet
$generalFixesMinified = preg_replace('/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $generalFixesCSS);
$generalFixesMinified = str_replace(["\r\n", "\r", "\n", "\t"], '', $generalFixesMinified);
$generalFixesMinified = preg_replace('/\s+/', ' ', $generalFixesMinified);
$generalFixesMinified = str_replace([' {', '{ ', ' }', '} ', ': ', ' :', '; ', ' ;', ', ', ' ,'], ['{', '{', '}', '}', ':', ':', ';', ';', ',', ','], $generalFixesMinified);

file_put_contents('Public/CSS/Banners/banner-general-fixes.min.css', $generalFixesMinified);

echo "\n✅ Genel düzeltme dosyası oluşturuldu: banner-general-fixes.min.css\n";
echo "\n🎯 Banner düzeltmeleri tamamlandı!\n";
echo "\nSonraki adımlar:\n";
echo "1. Tarayıcı cache'ini temizleyin\n";
echo "2. Siteyi yeniden yükleyin\n";
echo "3. Banner görünümlerini kontrol edin\n";

?>
