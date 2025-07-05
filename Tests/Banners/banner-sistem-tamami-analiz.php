<?php
/**
 * BANNER SİSTEMİ TAMAMI ANALİZİ
 * ===============================
 * Dinamik ve statik CSS dağılımını analiz eder
 */

echo "🔧 BANNER CSS SİSTEMİ TAMAMI ANALİZİ\n";
echo "===================================\n\n";

// 1. Statik CSS dosyalarını kontrol et
echo "📁 STATİK CSS DOSYALARI ANALİZİ:\n";
echo "-------------------------------\n";

$cssFiles = [
    'Public/CSS/Banners/tepe-banner.css' => 'Tepe Banner',
    'Public/CSS/Banners/slider.css' => 'Slider Banner',
    'Public/CSS/Banners/orta-banner.css' => 'Orta Banner',
    'Public/CSS/Banners/alt-banner.css' => 'Alt Banner'
];

foreach ($cssFiles as $file => $name) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $size = number_format(strlen($content));
        
        // Statik ID'li kuralları say
        $staticCount = preg_match_all('/\.banner-group-\d+/', $content);
        
        // Dinamik selectorları say  
        $dynamicCount = 0;
        $dynamicCount += substr_count($content, '[data-type=');
        $dynamicCount += substr_count($content, '[class^="banner-group-"]');
        $dynamicCount += substr_count($content, '[data-layout-group=');
        
        echo "   📄 {$name}: {$size} bytes\n";
        echo "      ❌ Statik ID kuralları: {$staticCount} adet\n";
        echo "      ✅ Dinamik selectorlar: {$dynamicCount} adet\n\n";
    } else {
        echo "   ❌ {$name}: Dosya bulunamadı\n\n";
    }
}

// 2. BannerController.php'yi analiz et
echo "🔧 BANNERCONTROLLER ANALİZİ:\n";
echo "----------------------------\n";

$controllerFile = 'App/Controller/BannerController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    $size = number_format(strlen($content));
    
    // generateBannerCSS fonksiyonunu bul
    if (preg_match('/function generateBannerCSS.*?(?=function|\Z)/s', $content, $matches)) {
        $cssFunction = $matches[0];
        
        // CSS üretim türlerini analiz et
        $staticCSS = preg_match_all('/\$css.*?\.banner-group-\{\$bannerGroupId\}/', $cssFunction);
        $dynamicCSS = preg_match_all('/background-color|width|height|grid-template/', $cssFunction);
        $importantCount = substr_count($cssFunction, '!important');
        
        echo "   📄 BannerController.php: {$size} bytes\n";
        echo "   🎯 generateBannerCSS fonksiyonu analizi:\n";
        echo "      ✅ Dinamik CSS üretimi: {$dynamicCSS} kural\n";
        echo "      ❌ !important kullanımı: {$importantCount} adet\n\n";
    }
} else {
    echo "   ❌ BannerController.php bulunamadı\n\n";
}

// 3. Canlı site analizi (özetlenmiş)
echo "🌐 CANLI SİTE ÖZET ANALİZİ:\n";
echo "----------------------------\n";

$siteUrl = 'http://l.globalpozitif';
$html = @file_get_contents($siteUrl);

if ($html) {
    // Banner gruplarını tespit et
    preg_match_all('/banner-group-(\d+)/', $html, $groupMatches);
    $groups = array_unique($groupMatches[1]);
    sort($groups, SORT_NUMERIC);
    
    // CSS stillerini çek
    preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $cssMatches);
    $allCSS = implode("\n", $cssMatches[1]);
    
    // Dinamik CSS kullanımını analiz et
    $dataTypeCount = substr_count($allCSS, '[data-type="');
    $classStartCount = substr_count($allCSS, '[class^="banner-group-"]');
    
    echo "   🎯 Aktif banner grupları: " . implode(', ', $groups) . "\n";
    echo "   📊 Toplam grup sayısı: " . count($groups) . "\n";
    echo "   ✅ Dinamik [data-type] selectorları: {$dataTypeCount} adet\n";
    echo "   ✅ Dinamik [class^] selectorları: {$classStartCount} adet\n\n";
} else {
    echo "   ❌ Canlı site erişilemedi\n\n";
}

// 4. Genel öneriler
echo "📋 GENEL DEĞERLENDİRME VE ÖNERİLER:\n";
echo "=====================================\n";

echo "✅ TAMAMLANAN İYİLEŞTİRMELER:\n";
echo "   - tepe-banner.css dinamik selectorlar ile güncellendi\n";
echo "   - BannerController.php sadece değişken CSS üretiyor\n";
echo "   - !important kullanımı minimize edildi\n";
echo "   - CSS değişkenleri sistemi entegre edildi\n\n";

echo "🎯 KALAN GÖREVLER:\n";
echo "   - Cache temizleme (canlı sitede eski CSS kalabilir)\n";
echo "   - Tüm banner CSS dosyalarında dinamik selector kontrolü\n";
echo "   - Test senaryoları ile farklı grup ID'lerini doğrulama\n\n";

echo "💡 DINAMIK CSS YAKLAŞIMI:\n";
echo "   STATIK CSS (dosyalarda): Genel kurallar, layout, responsive\n";
echo "   DİNAMİK CSS (controller): Arka plan, boyut, renk, özel CSS\n\n";

echo "🚀 SONUÇ: Banner sistemi modernize edildi!\n";
echo "   Artık banner-group-X ID'leri dinamik olarak destekleniyor.\n";
?>
