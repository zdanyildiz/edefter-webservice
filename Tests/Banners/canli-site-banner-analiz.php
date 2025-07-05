<?php
/**
 * Canlı Site Banner Analizi
 * Tepe banner HTML/CSS yapısını analiz eder
 */

echo "🔍 Canlı Site Banner Analizi\n";
echo "============================\n\n";

// Canlı siteden HTML içeriğini al
$url = "http://l.globalpozitif";
$html = file_get_contents($url);

if (!$html) {
    echo "❌ Site erişilemedi\n";
    exit;
}

echo "✅ Site HTML'i alındı (" . strlen($html) . " karakter)\n\n";

// Banner-group sınıflarını bul
preg_match_all('/class="([^"]*banner-group[^"]*)"/', $html, $bannerMatches);

if (!empty($bannerMatches[1])) {
    echo "🎯 Bulunan Banner Grupları:\n";
    echo "---------------------------\n";
    foreach (array_unique($bannerMatches[1]) as $class) {
        echo "  • $class\n";
    }
    echo "\n";
} else {
    echo "❌ Banner-group sınıfı bulunamadı\n";
}

// Banner HTML yapısını bul
preg_match_all('/<div[^>]*banner-group[^>]*>.*?<\/div>/s', $html, $bannerStructures);

if (!empty($bannerStructures[0])) {
    echo "📝 Banner HTML Yapıları:\n";
    echo "------------------------\n";
    
    foreach ($bannerStructures[0] as $index => $structure) {
        // Uzun HTML'i kısalt
        $shortStructure = substr($structure, 0, 200) . '...';
        $shortStructure = preg_replace('/\s+/', ' ', $shortStructure);
        
        echo "Banner " . ($index + 1) . ":\n";
        echo "$shortStructure\n\n";
    }
}

// CSS linklerini bul
preg_match_all('/<link[^>]+href="([^"]*\.css[^"]*)"[^>]*>/', $html, $cssMatches);

if (!empty($cssMatches[1])) {
    echo "🎨 CSS Dosyaları:\n";
    echo "----------------\n";
    foreach ($cssMatches[1] as $cssFile) {
        if (strpos($cssFile, 'banner') !== false || strpos($cssFile, 'index') !== false) {
            echo "  • $cssFile\n";
        }
    }
    echo "\n";
}

// Dinamik CSS stillerini bul (style tag içindeki banner CSS'leri)
preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $html, $styleMatches);

if (!empty($styleMatches[1])) {
    echo "🔧 Dinamik CSS Stilleri:\n";
    echo "-----------------------\n";
    
    foreach ($styleMatches[1] as $styleContent) {
        // Banner ile ilgili CSS kurallarını bul
        if (preg_match_all('/\.banner-group[^{]*\{[^}]+\}/s', $styleContent, $bannerCssMatches)) {
            foreach ($bannerCssMatches[0] as $bannerCss) {
                $bannerCss = preg_replace('/\s+/', ' ', trim($bannerCss));
                echo "$bannerCss\n\n";
            }
        }
    }
}

// Tepe banner özel analizi
echo "🎯 Tepe Banner Özel Analizi:\n";
echo "----------------------------\n";

// banner-group-2 (tepe banner) yapısını bul
if (preg_match('/<div[^>]*banner-group-2[^>]*>(.*?)<\/div>/s', $html, $tepeBannerMatch)) {
    echo "✅ Tepe banner HTML yapısı bulundu\n";
    
    // Container yapısını kontrol et
    if (strpos($tepeBannerMatch[1], 'banner-container') !== false) {
        echo "✅ banner-container sınıfı mevcut\n";
    } else {
        echo "❌ banner-container sınıfı eksik\n";
    }
    
    // Ortalama CSS'ini kontrol et
    if (preg_match('/\.banner-group-2[^{]*\{[^}]*margin[^}]*auto[^}]*\}/', $html)) {
        echo "✅ Tepe banner için margin: auto CSS'i mevcut\n";
    } else {
        echo "❌ Tepe banner için margin: auto CSS'i eksik\n";
    }
    
} else {
    echo "❌ Tepe banner (banner-group-2) bulunamadı\n";
}

echo "\n✨ Analiz tamamlandı.\n";
?>
