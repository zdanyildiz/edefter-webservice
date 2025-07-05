<?php
/**
 * JSON Anahtarları vs Tab Dosyaları Analizi
 * Bu script index.json'daki tüm anahtarların tab dosyalarında doğru isimle var olup olmadığını kontrol eder
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('JSON Anahtarları - Tab Dosyaları Uyum Kontrolü');

try {
    // Dosya yolları
    $indexJsonFile = realpath(__DIR__ . '/../../Public/Json/CSS/index.json');
    $tabsDir = realpath(__DIR__ . '/../../_y/s/s/tasarim/Theme/tabs');
    
    TestLogger::info("Index JSON: " . $indexJsonFile);
    TestLogger::info("Tabs Directory: " . $tabsDir);
    
    // index.json'u yükle
    TestAssert::assertTrue(file_exists($indexJsonFile), 'index.json dosyası mevcut olmalı');
    $jsonContent = file_get_contents($indexJsonFile);
    $jsonData = json_decode($jsonContent, true);
    TestAssert::assertNotNull($jsonData, 'JSON dosyası geçerli olmalı');
    
    $jsonKeys = array_keys($jsonData);
    TestLogger::info("JSON'da toplam anahtar sayısı: " . count($jsonKeys));
    
    // Tab dosyalarını bul
    $tabFiles = glob($tabsDir . '/*.php');
    TestLogger::info("Bulunan tab dosyası sayısı: " . count($tabFiles));
    
    // Tüm tab dosyalarındaki input name'leri topla
    $allInputNames = [];
    $fileInputCounts = [];
    
    foreach ($tabFiles as $tabFile) {
        $fileName = basename($tabFile);
        $fileContent = file_get_contents($tabFile);
        
        // Input name'leri bul
        preg_match_all('/name="([^"]*)"/', $fileContent, $matches);
        $inputNames = array_unique($matches[1]);
        
        $allInputNames = array_merge($allInputNames, $inputNames);
        $fileInputCounts[$fileName] = count($inputNames);
        
        TestLogger::info("$fileName: " . count($inputNames) . " input bulundu");
    }
    
    $allInputNames = array_unique($allInputNames);
    TestLogger::info("Toplam benzersiz input name sayısı: " . count($allInputNames));
    
    echo "\n=== JSON ANAHTARLARI ANALİZİ ===\n";
    
    // JSON'daki her anahtarı kontrol et
    $foundInTabs = [];
    $notFoundInTabs = [];
    
    foreach ($jsonKeys as $jsonKey) {
        if (in_array($jsonKey, $allInputNames)) {
            $foundInTabs[] = $jsonKey;
        } else {
            $notFoundInTabs[] = $jsonKey;
        }
    }
    
    echo "\n✅ JSON'DA VAR VE TAB DOSYALARINDA KULLANILAN ANAHTARLAR (" . count($foundInTabs) . " adet):\n";
    foreach ($foundInTabs as $key) {
        echo "   ✓ " . $key . "\n";
    }
    
    echo "\n❌ JSON'DA VAR ANCAK TAB DOSYALARINDA KULLANILMAYAN ANAHTARLAR (" . count($notFoundInTabs) . " adet):\n";
    $categorizedMissing = [];
    foreach ($notFoundInTabs as $key) {
        // Kategori bazında grupla
        $category = 'other';
        if (strpos($key, 'menu-') === 0) $category = 'menu';
        elseif (strpos($key, 'product-') === 0) $category = 'product';
        elseif (strpos($key, 'homepage-') === 0) $category = 'homepage';
        elseif (strpos($key, 'category-') === 0) $category = 'category';
        elseif (strpos($key, 'banner-') === 0) $category = 'banner';
        elseif (strpos($key, 'button-') === 0) $category = 'button';
        elseif (strpos($key, 'input-') === 0) $category = 'form';
        elseif (strpos($key, 'btn-') === 0) $category = 'form';
        elseif (strpos($key, 'form-') === 0) $category = 'form';
        elseif (strpos($key, 'footer-') === 0) $category = 'footer';
        elseif (strpos($key, 'font-') === 0) $category = 'typography';
        elseif (strpos($key, 'border-') === 0) $category = 'general';
        elseif (strpos($key, 'spacing-') === 0) $category = 'general';
        elseif (strpos($key, 'breakpoint-') === 0) $category = 'responsive';
        elseif (strpos($key, 'mobile-') === 0) $category = 'responsive';
        elseif (strpos($key, 'tablet-') === 0) $category = 'responsive';
        elseif (strpos($key, 'desktop-') === 0) $category = 'responsive';
        
        if (!isset($categorizedMissing[$category])) {
            $categorizedMissing[$category] = [];
        }
        $categorizedMissing[$category][] = $key;
    }
    
    foreach ($categorizedMissing as $category => $keys) {
        echo "\n   📂 " . strtoupper($category) . " (" . count($keys) . " adet):\n";
        foreach ($keys as $key) {
            echo "      - " . $key . " = " . $jsonData[$key] . "\n";
        }
    }
    
    // Tersine analiz - Tab dosyalarında olup JSON'da olmayan
    $missingInJson = array_diff($allInputNames, $jsonKeys);
    echo "\n⚠️  TAB DOSYALARINDA VAR ANCAK JSON'DA OLMAYAN INPUT NAME'LER (" . count($missingInJson) . " adet):\n";
    foreach ($missingInJson as $inputName) {
        echo "   - " . $inputName . "\n";
    }
    
    // İstatistikler
    echo "\n=== İSTATİSTİKLER ===\n";
    echo "JSON Anahtarları: " . count($jsonKeys) . "\n";
    echo "Tab Input Names: " . count($allInputNames) . "\n";
    echo "Ortak Anahtarlar: " . count($foundInTabs) . "\n";
    echo "JSON'da var, Tab'da yok: " . count($notFoundInTabs) . "\n";
    echo "Tab'da var, JSON'da yok: " . count($missingInJson) . "\n";
    
    $utilizationRate = count($foundInTabs) / count($jsonKeys) * 100;
    echo "JSON Kullanım Oranı: " . number_format($utilizationRate, 1) . "%\n";
    
    // Dosya bazında detay
    echo "\n=== DOSYA BAZINDA INPUT SAYILARI ===\n";
    foreach ($fileInputCounts as $fileName => $count) {
        echo sprintf("%-25s: %3d input\n", $fileName, $count);
    }
    
    if ($utilizationRate < 50) {
        TestLogger::warning("⚠️ JSON kullanım oranı düşük: %" . number_format($utilizationRate, 1));
    } elseif ($utilizationRate < 80) {
        TestLogger::info("📊 JSON kullanım oranı orta: %" . number_format($utilizationRate, 1));
    } else {
        TestLogger::success("🎯 JSON kullanım oranı yüksek: %" . number_format($utilizationRate, 1));
    }
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    echo "Hata: " . $e->getMessage() . "\n";
}

TestHelper::endTest();
