<?php
/**
 * SYSTEM DOCUMENTATION ANALYZER
 * Bu script, oluşturulan dokümantasyon dosyalarını analiz eder ve
 * sistem durumunu raporlar.
 */

// Temel ayarlar
$projectRoot = __DIR__ . '/..';
$testsDir = $projectRoot . '/Tests';

echo "=== PROJE DOKÜMANTASYON ANALİZİ ===\n";
echo "Proje Dizini: $projectRoot\n";
echo "Test Dizini: $testsDir\n\n";

// Dokümantasyon dizinlerini kontrol et
$documentedSystems = [
    'Banners' => [
        'frontend' => 'Tests/Banners/banner_prompt.md',
        'admin' => 'Tests/Banners/banner_admin_prompt.md'
    ],
    'Products' => [
        'system' => 'Tests/Products/product_prompt.md'
    ],
    'Members' => [
        'system' => 'Tests/Members/member_prompt.md'
    ],
    'Orders' => [
        'system' => 'Tests/Orders/order_prompt.md'
    ]
];

// Ana proje prompt'unu kontrol et
$mainPromptFile = $testsDir . '/PROJECT_PROMPT.md';
echo "📋 ANA PROJE PROMPT'U:\n";
if (file_exists($mainPromptFile)) {
    $fileSize = filesize($mainPromptFile);
    $wordCount = str_word_count(file_get_contents($mainPromptFile));
    echo "✅ PROJECT_PROMPT.md mevcut ($fileSize bytes, ~$wordCount kelime)\n";
} else {
    echo "❌ PROJECT_PROMPT.md bulunamadı!\n";
}
echo "\n";

// Her sistem için dokümantasyon analizi
echo "📚 SİSTEM DOKÜMANTASYONLARI:\n";
$totalSystems = 0;
$documentedSystemsCount = 0;
$totalWords = 0;

foreach ($documentedSystems as $systemName => $systemFiles) {
    $totalSystems++;
    echo "🔍 $systemName Sistemi:\n";
    
    $systemDocumented = true;
    $systemWordCount = 0;
    
    foreach ($systemFiles as $type => $file) {
        $fullPath = $projectRoot . '/' . $file;
        if (file_exists($fullPath)) {
            $fileSize = filesize($fullPath);
            $words = str_word_count(file_get_contents($fullPath));
            $systemWordCount += $words;
            echo "   ✅ $type: $file ($fileSize bytes, $words kelime)\n";
        } else {
            echo "   ❌ $type: $file - BULUNAMADI\n";
            $systemDocumented = false;
        }
    }
    
    if ($systemDocumented) {
        $documentedSystemsCount++;
        $totalWords += $systemWordCount;
        echo "   📊 Toplam: $systemWordCount kelime\n";
    }
    echo "\n";
}

// Model dosyalarının varlığını kontrol et
echo "🔧 MODEL DOSYALARI KONTROLÜ:\n";
$modelsToCheck = [
    'App/Model/Banner.php',
    'App/Model/Product.php', 
    'App/Model/Member.php',
    'App/Model/Order.php',
    'App/Model/Admin/AdminBannerModel.php',
    'App/Model/Admin/AdminProduct.php',
    'App/Model/Admin/AdminMember.php',
    'App/Model/Admin/AdminOrder.php'
];

$existingModels = 0;
foreach ($modelsToCheck as $model) {
    $fullPath = $projectRoot . '/' . $model;
    if (file_exists($fullPath)) {
        $fileSize = filesize($fullPath);
        echo "✅ $model ($fileSize bytes)\n";
        $existingModels++;
    } else {
        echo "❌ $model - BULUNAMADI\n";
    }
}

echo "\n";

// Admin sayfalarını kontrol et
echo "🖥️ ADMIN SAYFALAR KONTROLÜ:\n";
$adminPagesToCheck = [
    '_y/s/s/banners/AddBanner.php',
    '_y/s/s/urunler/AddProduct.php',
    '_y/s/s/uyeler/AddMember.php',
    '_y/s/s/siparisler/OrderList.php'
];

$existingAdminPages = 0;
foreach ($adminPagesToCheck as $page) {
    $fullPath = $projectRoot . '/' . $page;
    if (file_exists($fullPath)) {
        $fileSize = filesize($fullPath);
        echo "✅ $page ($fileSize bytes)\n";
        $existingAdminPages++;
    } else {
        echo "❌ $page - BULUNAMADI\n";
    }
}

echo "\n";

// CSS/JS dosyalarını kontrol et
echo "🎨 FRONTEND DOSYALAR KONTROLÜ:\n";
$frontendFiles = [
    'Public/CSS/Banners/tepe-banner.css',
    'Public/JS/memberUpdateFormValidate.js',
    'Public/JS/memberAddressFormValidate.js'
];

$existingFrontendFiles = 0;
foreach ($frontendFiles as $file) {
    $fullPath = $projectRoot . '/' . $file;
    if (file_exists($fullPath)) {
        $fileSize = filesize($fullPath);
        echo "✅ $file ($fileSize bytes)\n";
        $existingFrontendFiles++;
    } else {
        echo "❌ $file - BULUNAMADI\n";
    }
}

echo "\n";

// Veritabanı config kontrolü
echo "🗄️ VERİTABANI CONFIG KONTROLÜ:\n";
$dbConfigFiles = [
    'App/Config/Sql.php',
    'App/Config/Key.php',
    'App/Core/Config.php',
    'App/Database/Database.php',
    'App/Database/AdminDatabase.php'
];

$existingDbFiles = 0;
foreach ($dbConfigFiles as $file) {
    $fullPath = $projectRoot . '/' . $file;
    if (file_exists($fullPath)) {
        $fileSize = filesize($fullPath);
        echo "✅ $file ($fileSize bytes)\n";
        $existingDbFiles++;
    } else {
        echo "❌ $file - BULUNAMADI\n";
    }
}

echo "\n";

// Özet rapor
echo "📊 ÖZET RAPOR:\n";
echo "===============================\n";
echo "Toplam Sistem: $totalSystems\n";
echo "Dokümante Edilen Sistem: $documentedSystemsCount\n";
echo "Dokümantasyon Oranı: " . round(($documentedSystemsCount / $totalSystems) * 100, 2) . "%\n";
echo "Toplam Dokümantasyon Kelime Sayısı: $totalWords\n";
echo "Mevcut Model Dosyaları: $existingModels/" . count($modelsToCheck) . "\n";
echo "Mevcut Admin Sayfaları: $existingAdminPages/" . count($adminPagesToCheck) . "\n";
echo "Mevcut Frontend Dosyaları: $existingFrontendFiles/" . count($frontendFiles) . "\n";
echo "Mevcut DB Config Dosyaları: $existingDbFiles/" . count($dbConfigFiles) . "\n";
echo "\n";

// Model Context Protocol kalitesi kontrolü
echo "🎯 MODEL CONTEXT PROTOCOL KALİTE KONTROLÜ:\n";
$mcpKeywords = [
    'Model Context Protocol',
    'AMAÇ VE KAPSAM', 
    'SİSTEM MİMARİSİ',
    'VERİTABANI YAPISI',
    'TROUBLESHOOTING',
    'GELİŞTİRME REHBERİ'
];

foreach ($documentedSystems as $systemName => $systemFiles) {
    foreach ($systemFiles as $type => $file) {
        $fullPath = $projectRoot . '/' . $file;
        if (file_exists($fullPath)) {
            $content = file_get_contents($fullPath);
            $foundKeywords = 0;
            foreach ($mcpKeywords as $keyword) {
                if (strpos($content, $keyword) !== false) {
                    $foundKeywords++;
                }
            }
            $quality = round(($foundKeywords / count($mcpKeywords)) * 100, 2);
            echo "📄 $systemName ($type): $quality% MCP uyumluluğu ($foundKeywords/" . count($mcpKeywords) . " anahtar kelime)\n";
        }
    }
}

echo "\n";

// Sistem entegrasyonu analizi
echo "🔄 SİSTEM ENTEGRASYON ANALİZİ:\n";
$integrationPoints = [
    'Banner-Product' => 'Banner sisteminin ürün sayfalarında gösterimi',
    'Member-Order' => 'Üye sistemi ile sipariş sistemi entegrasyonu',
    'Product-Cart' => 'Ürün sisteminin sepet ile entegrasyonu',
    'Order-Payment' => 'Sipariş sisteminin ödeme sistemi ile entegrasyonu'
];

foreach ($integrationPoints as $integration => $description) {
    echo "🔗 $integration: $description\n";
}

echo "\n";

// Sonraki adımlar önerisi
echo "⏭️ SONRAKİ ADIMLAR ÖNERİSİ:\n";
echo "1. SEO sistemi dokümantasyonu (Tests/SEO/seo_prompt.md)\n";
echo "2. Cart/Sepet sistemi dokümantasyonu (Tests/Cart/cart_prompt.md)\n";
echo "3. Email/Notification sistemi dokümantasyonu\n";
echo "4. File/Image sistemi dokümantasyonu\n";
echo "5. Admin Panel genel dokümantasyonu\n";
echo "6. API endpoints dokümantasyonu\n";
echo "7. Test automation scripts geliştirme\n";
echo "8. CI/CD pipeline dokümantasyonu\n";

echo "\n=== ANALİZ TAMAMLANDI ===\n";
