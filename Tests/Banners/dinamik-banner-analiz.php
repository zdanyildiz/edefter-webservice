<?php
/**
 * GÜNCEL BANNER SİSTEMİ ANALİZİ
 * =====================================
 * Dinamik banner grup ID'lerini ve CSS yapısını analiz eder
 */

$site_url = 'http://l.globalpozitif';

echo "🔍 BANNER SİSTEMİ ANALİZİ - DİNAMİK ID'LER\n";
echo "==========================================\n";

// 1. HTML İçeriğini çek
$html = file_get_contents($site_url);
if (!$html) {
    echo "❌ Site içeriği çekilemedi!\n";
    exit;
}

echo "✅ Site HTML'i alındı (" . number_format(strlen($html)) . " karakter)\n\n";

// 2. Banner gruplarını dinamik ID'lerle tespit et
echo "📊 DİNAMİK BANNER GRUP ANALİZİ:\n";
echo "---------------------------------\n";

$bannerGroups = [];
if (preg_match_all('/banner-group-(\d+)/i', $html, $matches)) {
    $bannerGroups = array_unique($matches[1]);
    sort($bannerGroups, SORT_NUMERIC);
    
    echo "🎯 Tespit edilen banner grup ID'leri:\n";
    foreach ($bannerGroups as $groupId) {
        echo "   ├── banner-group-{$groupId}\n";
    }
    echo "   └── Toplam: " . count($bannerGroups) . " farklı grup\n\n";
} else {
    echo "❌ Banner grup ID'leri bulunamadı\n\n";
}

// 3. Banner tiplerini analiz et
echo "📋 BANNER TİP ANALİZİ:\n";
echo "----------------------\n";

$bannerTypes = [];
if (preg_match_all('/data-type=["\'](\d+)["\']/', $html, $typeMatches)) {
    $bannerTypes = array_unique($typeMatches[1]);
    sort($bannerTypes, SORT_NUMERIC);
    
    $typeNames = [
        '1' => 'Slider',
        '2' => 'Tepe Banner',
        '3' => 'Orta Banner', 
        '4' => 'Alt Banner',
        '5' => 'Popup Banner',
        '6' => 'Carousel'
    ];
    
    echo "🏷️ Tespit edilen banner tipleri:\n";
    foreach ($bannerTypes as $typeId) {
        $typeName = $typeNames[$typeId] ?? 'Bilinmeyen Tip';
        echo "   ├── Type {$typeId}: {$typeName}\n";
    }
    echo "   └── Toplam: " . count($bannerTypes) . " farklı tip\n\n";
}

// 4. Layout gruplarını analiz et
echo "🎨 LAYOUT GRUP ANALİZİ:\n";
echo "----------------------\n";

$layoutGroups = [];
if (preg_match_all('/data-layout-group=["\']([^"\']+)["\']/', $html, $layoutMatches)) {
    $layoutGroups = array_unique($layoutMatches[1]);
    sort($layoutGroups);
    
    echo "📐 Tespit edilen layout grupları:\n";
    foreach ($layoutGroups as $layout) {
        echo "   ├── {$layout}\n";
    }
    echo "   └── Toplam: " . count($layoutGroups) . " farklı layout\n\n";
}

// 5. CSS selectorları analiz et
echo "🎯 CSS SELECTOR UYGUNLUK ANALİZİ:\n";
echo "--------------------------------\n";

// Dinamik CSS'den inline stilleri çek
if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $cssMatches)) {
    $allCSS = implode("\n", $cssMatches[1]);
    
    // Statik ID'li kuralları tespit et
    $staticRules = [];
    if (preg_match_all('/\.banner-group-(\d+)([^{]*){/', $allCSS, $staticMatches)) {
        foreach ($staticMatches[1] as $i => $groupId) {
            $selector = '.banner-group-' . $groupId . $staticMatches[2][$i];
            $staticRules[] = trim($selector);
        }
    }
    
    if (!empty($staticRules)) {
        echo "⚠️ STATIK ID'Lİ CSS KURALLARI TESPIT EDİLDİ:\n";
        $uniqueStatic = array_unique($staticRules);
        foreach (array_slice($uniqueStatic, 0, 10) as $rule) {
            echo "   ❌ {$rule}\n";
        }
        if (count($uniqueStatic) > 10) {
            echo "   📝 ... ve " . (count($uniqueStatic) - 10) . " kural daha\n";
        }
        echo "\n";
    }
    
    // Dinamik selectorları kontrol et
    $dynamicSelectors = [
        '[data-type="' => 'Banner tip selectorları',
        '[class^="banner-group-"]' => 'Grup başlangıç selectorları',
        '[data-layout-group="' => 'Layout grup selectorları'
    ];
    
    echo "✅ DİNAMİK SELECTOR KONTROLÜ:\n";
    foreach ($dynamicSelectors as $selector => $description) {
        $count = substr_count($allCSS, $selector);
        $status = $count > 0 ? "✓" : "❌";
        echo "   {$status} {$description}: {$count} adet\n";
    }
    echo "\n";
}

// 6. Banner container yapısı analiz et
echo "🏗️ BANNER YAPISAL ANALİZ:\n";
echo "-------------------------\n";

$containerTypes = [
    'banner-container' => 'Ana container',
    'banner-content-centered' => 'Merkezlenmiş içerik',
    'slider-container' => 'Slider container',
    'carousel-container' => 'Carousel container'
];

foreach ($containerTypes as $class => $description) {
    $count = substr_count($html, $class);
    if ($count > 0) {
        echo "   ✅ {$description}: {$count} adet\n";
    }
}

// 7. Öneriler ve sonuçlar
echo "\n🎯 ANALİZ SONUÇLARI VE ÖNERİLER:\n";
echo "================================\n";

if (!empty($bannerGroups)) {
    $minId = min($bannerGroups);
    $maxId = max($bannerGroups);
    echo "📈 Banner grup ID aralığı: {$minId} - {$maxId}\n";
    echo "📊 Toplam aktif grup sayısı: " . count($bannerGroups) . "\n";
}

echo "\n💡 ÖNERİLER:\n";
echo "------------\n";
echo "1. ✅ [data-type=\"X\"] selectorları kullan (tip bazlı)\n";
echo "2. ✅ [class^=\"banner-group-\"] selectorları kullan (grup bazlı)\n";
echo "3. ❌ .banner-group-2 gibi statik selectorlar kullanma\n";
echo "4. ✅ CSS değişkenleri kullan (--content-max-width)\n";
echo "5. ✅ !important kullanımını minimize et\n";

echo "\n🔧 CSS DÜZELTME ÖRNEKLERİ:\n";
echo "-------------------------\n";
echo "❌ HATALI: .banner-group-2 { margin: 0 auto; }\n";
echo "✅ DOĞRU: [data-type=\"2\"] { margin: 0 auto; }\n";
echo "✅ DOĞRU: [class^=\"banner-group-\"] { width: 100%; }\n";

echo "\n✨ Analiz tamamlandı!\n";
?>
