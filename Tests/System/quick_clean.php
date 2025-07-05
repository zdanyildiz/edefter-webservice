<?php
/**
 * Hızlı Test Temizleyici
 * 
 * En sık kullanılan temizleme işlemlerini tek dosyada toplar.
 * Gereksiz test dosyalarını hızlıca temizlemek için kullanılır.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// TestCleaner sınıfını yükle
include_once __DIR__ . '/TestCleaner.php';

echo "⚡ Hızlı Test Temizleyici\n";
echo "========================\n\n";

// Sık temizlenen dosya türleri
$commonTempFiles = [
    // Temp klasöründeki tüm dosyalar
    ...TestCleaner::listFiles('Temp'),
    
    // Debug ve test dosyaları
    ...array_filter(TestCleaner::listFiles('Orders'), function($file) {
        return preg_match('/^(temp_|debug_|test_|old_)/', basename($file));
    }),
    
    ...array_filter(TestCleaner::listFiles('Products'), function($file) {
        return preg_match('/^(temp_|debug_|test_|old_)/', basename($file));
    }),
    
    ...array_filter(TestCleaner::listFiles('Members'), function($file) {
        return preg_match('/^(temp_|debug_|test_|old_)/', basename($file));
    }),
    
    ...array_filter(TestCleaner::listFiles('Carts'), function($file) {
        return preg_match('/^(temp_|debug_|test_|old_)/', basename($file));
    }),
    
    // System klasöründeki geçici dosyalar
    ...array_filter(TestCleaner::listFiles('System'), function($file) {
        return preg_match('/^(temp_|debug_|test_output)/', basename($file));
    })
];

// Benzersiz dosyaları al
$commonTempFiles = array_unique($commonTempFiles);

if (empty($commonTempFiles)) {
    echo "✅ Temizlenecek geçici dosya bulunamadı!\n";
    echo "🎉 Test ortamı zaten temiz.\n";
    exit(0);
}

echo "📋 Bulunan geçici dosyalar (" . count($commonTempFiles) . " adet):\n";
echo str_repeat("-", 40) . "\n";

foreach ($commonTempFiles as $file) {
    echo "📄 $file\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 Önce kontrol ediliyor (Dry Run):\n";
echo str_repeat("=", 50) . "\n";

// Dry run ile kontrol
$dryResults = TestCleaner::cleanFiles($commonTempFiles, true);

echo "\n" . str_repeat("=", 50) . "\n";
echo "🧹 Gerçek temizleme başlatılıyor:\n";
echo str_repeat("=", 50) . "\n";

// Gerçek temizleme
$realResults = TestCleaner::cleanFiles($commonTempFiles, false);

// Final özet
echo "\n🎯 FİNAL ÖZET:\n";
echo str_repeat("=", 30) . "\n";
echo "✅ Silinen: " . count($realResults['deleted']) . " dosya\n";
echo "🛡️  Korunan: " . count($realResults['protected']) . " dosya\n";
echo "⏭️  Atlanan: " . count($realResults['skipped']) . " dosya\n";
echo "❌ Hatalı: " . count($realResults['errors']) . " dosya\n";

if (count($realResults['deleted']) > 0) {
    echo "\n🗑️  Silinen dosyalar:\n";
    foreach ($realResults['deleted'] as $item) {
        echo "   ✓ " . $item['file'] . "\n";
    }
}

// Eski logları da temizle (30 günden eski)
echo "\n" . str_repeat("-", 50) . "\n";
echo "📜 Eski log dosyaları kontrol ediliyor...\n";

$logResults = TestCleaner::cleanOldLogs(30, false);
if (count($logResults['deleted']) > 0) {
    echo "✅ " . count($logResults['deleted']) . " eski log dosyası silindi\n";
} else {
    echo "ℹ️  Silinecek eski log dosyası yok\n";
}

echo "\n🎉 Hızlı temizleme tamamlandı!\n";
echo "💡 Test ortamı temizlendi ve hazır.\n";
