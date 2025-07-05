<?php
/**
 * Theme Tabs ve index.json Kapsamlı Karşılaştırma Analizi
 * Bu script tüm Theme/tabs dosyalarındaki input name'leri index.json ile karşılaştırır
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Theme Tabs - index.json Kapsamlı Karşılaştırma');

try {
    // Dosya yolları
    $tabsDir = realpath(__DIR__ . '/../../_y/s/s/tasarim/Theme/tabs');
    $indexJsonFile = realpath(__DIR__ . '/../../Public/Json/CSS/index.json');
    
    TestLogger::info("Tabs Dizini: " . $tabsDir);
    TestLogger::info("Index JSON: " . $indexJsonFile);
    
    // Dosya varlık kontrolü
    TestAssert::assertTrue(is_dir($tabsDir), 'Theme/tabs dizini mevcut olmalı');
    TestAssert::assertTrue(file_exists($indexJsonFile), 'index.json dosyası mevcut olmalı');
    
    // index.json dosyasını yükle
    $jsonContent = file_get_contents($indexJsonFile);
    $jsonData = json_decode($jsonContent, true);
    TestAssert::assertNotNull($jsonData, 'JSON dosyası geçerli olmalı');
    
    $jsonKeys = array_keys($jsonData);
    sort($jsonKeys);
    
    TestLogger::info("index.json'da toplam anahtar sayısı: " . count($jsonKeys));
    
    // Tabs dizinindeki PHP dosyalarını bul
    $tabFiles = glob($tabsDir . '/*.php');
    TestLogger::info("Bulunan tab dosyası sayısı: " . count($tabFiles));
    
    $allInputNames = [];
    $fileInputNames = [];
    $totalInputCount = 0;
    
    echo "\n=== TAB DOSYALARI ANALİZİ ===\n";
    
    foreach ($tabFiles as $tabFile) {
        $fileName = basename($tabFile);
        echo "\n📄 " . $fileName . "\n";
        
        $content = file_get_contents($tabFile);
        
        // Input name'leri çıkar
        preg_match_all('/name="([^"]*)"/', $content, $matches);
        $inputNames = array_unique($matches[1]);
        
        $fileInputNames[$fileName] = $inputNames;
        $allInputNames = array_merge($allInputNames, $inputNames);
        $totalInputCount += count($inputNames);
        
        echo "   ✓ Input sayısı: " . count($inputNames) . "\n";
        
        if (count($inputNames) > 0) {
            echo "   📝 Input'lar:\n";
            foreach ($inputNames as $inputName) {
                $inJson = isset($jsonData[$inputName]) ? "✅" : "❌";
                echo "      {$inJson} {$inputName}\n";
            }
        }
    }
    
    // Benzersiz input name'leri al
    $allInputNames = array_unique($allInputNames);
    sort($allInputNames);
    
    echo "\n=== GENEL ÖZET ===\n";
    echo "Toplam tab dosyası: " . count($tabFiles) . "\n";
    echo "Toplam input (tekrarlı): " . $totalInputCount . "\n";
    echo "Benzersiz input sayısı: " . count($allInputNames) . "\n";
    echo "JSON anahtar sayısı: " . count($jsonKeys) . "\n";
    
    // Karşılaştırma analizi
    echo "\n=== KARŞILAŞTIRMA ANALİZİ ===\n";
    
    // Input'larda olup JSON'da olmayan
    $missingInJson = array_diff($allInputNames, $jsonKeys);
    if (!empty($missingInJson)) {
        echo "\n❌ INPUT'LARDA VAR ANCAK JSON'DA YOK (" . count($missingInJson) . " adet):\n";
        foreach ($missingInJson as $name) {
            echo "   - " . $name . "\n";
            
            // Bu input hangi dosyalarda var?
            foreach ($fileInputNames as $file => $inputs) {
                if (in_array($name, $inputs)) {
                    echo "     └── " . $file . "\n";
                }
            }
        }
        TestLogger::warning("Input'larda var ancak JSON'da olmayan " . count($missingInJson) . " anahtar bulundu");
    } else {
        echo "\n✅ Tüm input name'ler JSON'da mevcut\n";
        TestLogger::success("Tüm input name'ler JSON'da mevcut");
    }
    
    // JSON'da olup input'larda olmayan (tema ile ilgili olanlar)
    $themeRelatedKeys = array_filter($jsonKeys, function($key) {
        $patterns = [
            'top-contact', 'header-', 'shop-menu', 'mobile-action',
            'menu-', 'product-', 'homepage-', 'category-', 'banner-',
            'button-', 'input-', 'form-', 'btn-', 'footer-'
        ];
        
        foreach ($patterns as $pattern) {
            if (strpos($key, $pattern) === 0) {
                return true;
            }
        }
        return false;
    });
    
    $missingInInputs = array_diff($themeRelatedKeys, $allInputNames);
    if (!empty($missingInInputs)) {
        echo "\n⚠️ JSON'DA VAR ANCAK HİÇBİR INPUT'TA YOK (" . count($missingInInputs) . " adet):\n";
        foreach ($missingInInputs as $name) {
            echo "   - " . $name . " = " . $jsonData[$name] . "\n";
        }
        TestLogger::warning("JSON'da var ancak input'larda olmayan " . count($missingInInputs) . " tema anahtarı bulundu");
    } else {
        echo "\n✅ JSON'daki tüm tema anahtarları input'larda kullanılıyor\n";
        TestLogger::success("JSON'daki tüm tema anahtarları input'larda kullanılıyor");
    }
    
    // Tam eşleşenler
    $exactMatches = array_intersect($allInputNames, $jsonKeys);
    echo "\n✅ TAM EŞLEŞEN ANAHTARLAR (" . count($exactMatches) . " adet):\n";
    
    // Dosya bazında eşleşme raporu
    echo "\n=== DOSYA BAZINDA EŞLEŞME RAPORU ===\n";
    foreach ($fileInputNames as $fileName => $inputs) {
        if (empty($inputs)) continue;
        
        $fileMatches = array_intersect($inputs, $jsonKeys);
        $fileMisses = array_diff($inputs, $jsonKeys);
        $successRate = count($inputs) > 0 ? (count($fileMatches) / count($inputs)) * 100 : 100;
        
        echo "\n📄 " . $fileName . "\n";
        echo "   📊 Başarı Oranı: " . number_format($successRate, 1) . "%\n";
        echo "   ✅ Eşleşen: " . count($fileMatches) . "/" . count($inputs) . "\n";
        
        if (!empty($fileMisses)) {
            echo "   ❌ Eksik anahtarlar:\n";
            foreach ($fileMisses as $miss) {
                echo "      - " . $miss . "\n";
            }
        }
    }
    
    // Genel başarı değerlendirmesi
    $overallSuccessRate = count($allInputNames) > 0 ? (count($exactMatches) / count($allInputNames)) * 100 : 100;
    echo "\n=== GENEL BAŞARI DEĞERLENDİRMESİ ===\n";
    echo "Genel Başarı Oranı: " . number_format($overallSuccessRate, 1) . "%\n";
    echo "Tam Eşleşme: " . count($exactMatches) . "/" . count($allInputNames) . "\n";
    echo "Eksik Anahtar: " . count($missingInJson) . " adet\n";
    echo "Kullanılmayan JSON Anahtarı: " . count($missingInInputs) . " adet\n";
    
    if ($overallSuccessRate == 100) {
        TestLogger::success("🎉 Mükemmel! Tüm input name'leri JSON'da mevcut");
    } elseif ($overallSuccessRate >= 90) {
        TestLogger::warning("⚠️ İyi durumda, birkaç eksik var");
    } else {
        TestLogger::error("❌ Ciddi uyumsuzluklar var, düzeltme gerekli");
    }
    
    // Öneriler
    if (!empty($missingInJson)) {
        echo "\n=== ÖNERİLER ===\n";
        echo "1. Eksik JSON anahtarlarını index.json'a ekleyin\n";
        echo "2. Veya input name'lerini mevcut JSON anahtarlarına uygun olarak değiştirin\n";
        echo "3. Fallback değerleri kaldırıldığından, tüm input'ların JSON karşılığı olmalı\n";
    }
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    echo "Hata: " . $e->getMessage() . "\n";
}

TestHelper::endTest();
