<?php
/**
 * Banner Style Data Comparer
 * SiteConfig ve BannerController arasındaki style verilerini karşılaştırır
 */

require_once 'App/Core/Config.php';
$config = new Config();
require_once 'App/Database/Database.php';
$db = new Database($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

require_once 'App/Model/SiteConfig.php';
require_once 'App/Controller/BannerController.php';

echo "<h1>Banner Style Data Comparison Test</h1>\n";

// SiteConfig'den banner verilerini al
$languageId = 1;
$siteConfig = new SiteConfig($db, $languageId);
$siteConfig->createSiteConfig();
$bannerInfo = $siteConfig->getBannerInfo();

echo "<h2>SiteConfig Banner Data Structure:</h2>\n";
echo "<pre>\n";

if (!empty($bannerInfo)) {
    // İlk banner grubunu analiz et
    $firstBanner = $bannerInfo[0];
    
    echo "Banner Type: " . $firstBanner['type_name'] . "\n";
    echo "Group ID: " . $firstBanner['group_info']['id'] . "\n";
    echo "Banners Count: " . count($firstBanner['banners']) . "\n\n";
    
    if (!empty($firstBanner['banners'])) {
        $firstBannerItem = $firstBanner['banners'][0];
        
        echo "First Banner Item Structure:\n";
        echo "- id: " . $firstBannerItem['id'] . "\n";
        echo "- title: " . $firstBannerItem['title'] . "\n";
        echo "- content: " . $firstBannerItem['content'] . "\n";
        echo "- image: " . $firstBannerItem['image'] . "\n";
        echo "- link: " . $firstBannerItem['link'] . "\n";
        echo "- style: ";
        print_r($firstBannerItem['style']);
        echo "\n";
        
        // Style verileri kontrolü
        $style = $firstBannerItem['style'];
        echo "\nStyle Data Analysis:\n";
        echo "- show_button: " . (isset($style['show_button']) ? $style['show_button'] : 'NOT SET') . "\n";
        echo "- button_title: " . (isset($style['button_title']) ? $style['button_title'] : 'NOT SET') . "\n";
        echo "- button_background: " . (isset($style['button_background']) ? $style['button_background'] : 'NOT SET') . "\n";
        echo "- button_color: " . (isset($style['button_color']) ? $style['button_color'] : 'NOT SET') . "\n";
        echo "- button_size: " . (isset($style['button_size']) ? $style['button_size'] : 'NOT SET') . "\n";
        echo "- button_hover_background: " . (isset($style['button_hover_background']) ? $style['button_hover_background'] : 'NOT SET') . "\n";
        echo "- button_hover_color: " . (isset($style['button_hover_color']) ? $style['button_hover_color'] : 'NOT SET') . "\n";
    }
}

echo "</pre>\n";

// BannerController ile test et
echo "<h2>BannerController Test:</h2>\n";
echo "<pre>\n";

$bannerController = new BannerController($bannerInfo);

// İlk banner tipini render et (slider)
$sliderResult = $bannerController->renderSliderBanners();

echo "Slider Banners Found: " . count($sliderResult['banners']) . "\n";
echo "HTML Length: " . strlen($sliderResult['html']) . "\n";
echo "CSS Length: " . strlen($sliderResult['css']) . "\n";

// HTML'de buton kontrolü
if (strpos($sliderResult['html'], 'banner-button') !== false) {
    echo "✅ Banner button found in HTML\n";
    
    // Buton sayısını say
    $buttonCount = substr_count($sliderResult['html'], 'banner-button');
    echo "Button count in HTML: " . $buttonCount . "\n";
} else {
    echo "❌ No banner button found in HTML\n";
}

// CSS'de buton stillerini kontrol et
if (strpos($sliderResult['css'], '.banner-button') !== false) {
    echo "✅ Banner button styles found in CSS\n";
} else {
    echo "❌ No banner button styles found in CSS\n";
}

echo "</pre>\n";

// Tüm banner tiplerini test et
echo "<h2>All Banner Types Test:</h2>\n";
echo "<pre>\n";

$allTypes = $bannerController->renderAllBannerTypes();

foreach ($allTypes['types'] as $typeId => $typeData) {
    echo "Type ID: $typeId - " . $typeData['type_name'] . "\n";
    echo "  Banners: " . count($typeData['banners']) . "\n";
    echo "  HTML Length: " . strlen($typeData['html']) . "\n";
    echo "  Has Button HTML: " . (strpos($typeData['html'], 'banner-button') !== false ? 'YES' : 'NO') . "\n";
    echo "  Has Button CSS: " . (strpos($typeData['css'], '.banner-button') !== false ? 'YES' : 'NO') . "\n";
    echo "\n";
}

echo "</pre>\n";

// Database'den direkt style kontrolü
echo "<h2>Direct Database Style Check:</h2>\n";
echo "<pre>\n";

$sql = "SELECT id, banner_id, show_button, button_title, button_background, button_color FROM banner_styles LIMIT 5";
$styles = $db->select($sql);

if (!empty($styles)) {
    foreach ($styles as $style) {
        echo "Style ID: " . $style['id'] . "\n";
        echo "  Banner ID: " . $style['banner_id'] . "\n";
        echo "  Show Button: " . $style['show_button'] . "\n";
        echo "  Button Title: " . $style['button_title'] . "\n";
        echo "  Button Background: " . $style['button_background'] . "\n";
        echo "  Button Color: " . $style['button_color'] . "\n";
        echo "\n";
    }
} else {
    echo "No style data found in database\n";
}

echo "</pre>\n";
