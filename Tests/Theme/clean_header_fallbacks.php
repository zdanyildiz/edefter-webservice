<?php
/**
 * Header Settings Fallback Temizleyici
 * Bu script header-settings.php dosyasındaki tüm fallback değerlerini kaldırır
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Header Settings Fallback Temizleme');

try {
    $headerSettingsFile = realpath(__DIR__ . '/../../_y/s/s/tasarim/Theme/tabs/header-settings.php');
    
    TestLogger::info("İşlenen dosya: " . $headerSettingsFile);
    
    // Dosya varlık kontrolü
    TestAssert::assertTrue(file_exists($headerSettingsFile), 'header-settings.php dosyası mevcut olmalı');
    
    // Dosya içeriğini oku
    $content = file_get_contents($headerSettingsFile);
    $originalContent = $content;
    
    TestLogger::info("Orijinal dosya boyutu: " . strlen($content) . " byte");
    
    // Fallback temizleme işlemleri
    $cleanupCount = 0;
    
    // 1. Çoklu fallback temizleme (A ?? B ?? C formatı)
    $pattern1 = '/(\$customCSS\[[^\]]+\])\s*\?\?\s*(\$customCSS\[[^\]]+\])\s*\?\?\s*([^)]+)/';
    $replacement1 = '$1';
    $content = preg_replace_callback($pattern1, function($matches) use (&$cleanupCount) {
        $cleanupCount++;
        TestLogger::info("Çoklu fallback temizlendi: " . trim($matches[0]));
        return $matches[1];
    }, $content);
    
    // 2. Basit fallback temizleme (A ?? B formatı)
    $pattern2 = '/(\$customCSS\[[^\]]+\])\s*\?\?\s*[^,)]+/';
    $replacement2 = '$1';
    $content = preg_replace_callback($pattern2, function($matches) use (&$cleanupCount) {
        $cleanupCount++;
        TestLogger::info("Basit fallback temizlendi: " . trim($matches[0]));
        return $matches[1];
    }, $content);
    
    // 3. sanitizeNumericValue fonksiyonundaki son parametreleri temizle
    $pattern3 = '/(sanitizeNumericValue\([^,]+,\s*[^,]+),\s*[^)]+\)/';
    $replacement3 = '$1)';
    $content = preg_replace_callback($pattern3, function($matches) use (&$cleanupCount) {
        $cleanupCount++;
        TestLogger::info("sanitizeNumericValue parametresi temizlendi");
        return $matches[1] . ')';
    }, $content);
    
    TestLogger::info("Toplam temizlenen fallback sayısı: " . $cleanupCount);
    TestLogger::info("Yeni dosya boyutu: " . strlen($content) . " byte");
    TestLogger::info("Boyut farkı: " . (strlen($originalContent) - strlen($content)) . " byte azaldı");
    
    // Değişiklik kontrolü
    if ($content !== $originalContent) {
        // Yedek oluştur
        $backupFile = $headerSettingsFile . '.backup.' . date('Y-m-d_H-i-s');
        file_put_contents($backupFile, $originalContent);
        TestLogger::info("Yedek dosya oluşturuldu: " . basename($backupFile));
        
        // Temizlenmiş içeriği kaydet
        file_put_contents($headerSettingsFile, $content);
        TestLogger::success("Fallback değerler başarıyla temizlendi!");
        
        echo "\n=== BAŞARILI ===\n";
        echo "✅ Temizlenen fallback sayısı: {$cleanupCount}\n";
        echo "✅ Dosya güncellendi: header-settings.php\n";
        echo "✅ Yedek oluşturuldu: " . basename($backupFile) . "\n";
        echo "✅ Dosya boyutu: " . (strlen($originalContent) - strlen($content)) . " byte azaldı\n";
        
    } else {
        TestLogger::warning("Hiçbir fallback değer bulunamadı veya değişiklik yapılmadı");
        echo "\n⚠️ Hiçbir fallback değer bulunamadı!\n";
    }
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

TestHelper::endTest();
