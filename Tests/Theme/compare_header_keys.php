<?php
/**
 * Header Settings Input Names ile index.json Anahtarları Karşılaştırması
 * Bu script header-settings.php dosyasındaki input name değerlerini 
 * index.json dosyasındaki anahtarlarla karşılaştırır
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Header Settings Input Names - index.json Anahtarları Karşılaştırması');

try {
    // Dosya yolları
    $headerSettingsFile = realpath(__DIR__ . '/../../_y/s/s/tasarim/Theme/tabs/header-settings.php');
    $indexJsonFile = realpath(__DIR__ . '/../../Public/Json/CSS/index.json');
    
    TestLogger::info("Header Settings Dosyası: " . $headerSettingsFile);
    TestLogger::info("Index JSON Dosyası: " . $indexJsonFile);
    
    // Dosya varlık kontrolü
    TestAssert::assertTrue(file_exists($headerSettingsFile), 'header-settings.php dosyası mevcut olmalı');
    TestAssert::assertTrue(file_exists($indexJsonFile), 'index.json dosyası mevcut olmalı');
    
    // header-settings.php dosyasındaki input name değerlerini çıkar
    $headerContent = file_get_contents($headerSettingsFile);
    preg_match_all('/name="([^"]*)"/', $headerContent, $headerMatches);
    $headerInputNames = array_unique($headerMatches[1]);
    sort($headerInputNames);
    
    TestLogger::info("Header Settings'de bulunan input name sayısı: " . count($headerInputNames));
    
    // index.json dosyasındaki anahtarları çıkar
    $jsonContent = file_get_contents($indexJsonFile);
    $jsonData = json_decode($jsonContent, true);
    TestAssert::assertNotNull($jsonData, 'JSON dosyası geçerli olmalı');
    
    $jsonKeys = array_keys($jsonData);
    sort($jsonKeys);
    
    TestLogger::info("index.json'da bulunan anahtar sayısı: " . count($jsonKeys));
    
    // Header input names listesi
    echo "\n=== HEADER-SETTINGS.PHP INPUT NAMES ===\n";
    foreach ($headerInputNames as $index => $name) {
        echo sprintf("%2d. %s\n", $index + 1, $name);
    }
    
    // JSON keys'in header ile ilgili olanları
    $headerRelatedKeys = array_filter($jsonKeys, function($key) {
        return strpos($key, 'header-') === 0 || 
               strpos($key, 'top-contact-and-social-') === 0 ||
               strpos($key, 'shop-menu-container-') === 0 ||
               strpos($key, 'mobile-action-icon-') === 0;
    });
    
    echo "\n=== INDEX.JSON'DA HEADER İLE İLGİLİ ANAHTARLAR ===\n";
    foreach ($headerRelatedKeys as $index => $key) {
        echo sprintf("%2d. %s\n", array_search($key, array_values($headerRelatedKeys)) + 1, $key);
    }
    
    // Karşılaştırma analizi
    echo "\n=== KARŞILAŞTIRMA ANALİZİ ===\n";
    
    // Header input names'te olup JSON'da olmayan
    $missingInJson = array_diff($headerInputNames, $jsonKeys);
    if (!empty($missingInJson)) {
        echo "\n❌ HEADER'DA VAR ANCAK JSON'DA YOK:\n";
        foreach ($missingInJson as $name) {
            echo "   - " . $name . "\n";
        }
        TestLogger::warning("Header'da var ancak JSON'da olmayan " . count($missingInJson) . " anahtar bulundu");
    } else {
        echo "\n✅ Header'daki tüm input name'ler JSON'da mevcut\n";
        TestLogger::success("Header'daki tüm input name'ler JSON'da mevcut");
    }
    
    // JSON'da olup header input names'te olmayan header ile ilgili anahtarlar
    $missingInHeader = array_diff($headerRelatedKeys, $headerInputNames);
    if (!empty($missingInHeader)) {
        echo "\n⚠️  JSON'DA VAR ANCAK HEADER'DA YOK:\n";
        foreach ($missingInHeader as $name) {
            echo "   - " . $name . "\n";
        }
        TestLogger::warning("JSON'da var ancak header'da olmayan " . count($missingInHeader) . " header anahtarı bulundu");
    } else {
        echo "\n✅ JSON'daki tüm header anahtarları header dosyasında kullanılıyor\n";
        TestLogger::success("JSON'daki tüm header anahtarları header dosyasında kullanılıyor");
    }
    
    // Tam eşleşenler
    $exactMatches = array_intersect($headerInputNames, $jsonKeys);
    echo "\n✅ TAM EŞLEŞEN ANAHTARLAR (" . count($exactMatches) . " adet):\n";
    foreach ($exactMatches as $name) {
        echo "   ✓ " . $name . "\n";
    }
    TestLogger::success("Tam eşleşen anahtar sayısı: " . count($exactMatches));
    
    // Sonuç özeti
    echo "\n=== ÖZET ===\n";
    echo "Header Input Names: " . count($headerInputNames) . " adet\n";
    echo "JSON Header Anahtarları: " . count($headerRelatedKeys) . " adet\n";
    echo "Tam Eşleşme: " . count($exactMatches) . " adet\n";
    echo "Header'da var, JSON'da yok: " . count($missingInJson) . " adet\n";
    echo "JSON'da var, Header'da yok: " . count($missingInHeader) . " adet\n";
    
    // Başarı değerlendirmesi
    $successRate = count($exactMatches) / count($headerInputNames) * 100;
    echo "Başarı Oranı: " . number_format($successRate, 1) . "%\n";
    
    if ($successRate == 100) {
        TestLogger::success("🎉 Mükemmel! Tüm header input name'leri JSON'da mevcut");
    } elseif ($successRate >= 90) {
        TestLogger::warning("⚠️ İyi durumda, birkaç eksik var");
    } else {
        TestLogger::error("❌ Ciddi uyumsuzluklar var, düzeltme gerekli");
    }
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    echo "Hata: " . $e->getMessage() . "\n";
}

TestHelper::endTest();
