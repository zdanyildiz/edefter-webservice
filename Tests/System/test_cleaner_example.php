<?php
/**
 * Test Temizleyici Örnek Kullanım
 * 
 * TestCleaner sınıfının nasıl kullanılacağını gösterir.
 * Gereksiz test dosyalarını otomatik olarak temizler.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// TestCleaner sınıfını yükle
include_once __DIR__ . '/TestCleaner.php';

echo "🧹 Test Dosya Temizleyici - Örnek Kullanım\n";
echo "=========================================\n\n";

// 1. Manuel dosya listesi ile temizleme
echo "1️⃣ Manuel Dosya Temizleme:\n";
echo "-------------------------\n";

$manualFiles = [
    'Temp/test_output.txt',
    'Temp/debug.log',
    'Temp/sample_data.json',
    'Orders/temp_order_test.php',
    'Products/old_product_test.php'
];

echo "📋 Silinecek dosyalar:\n";
foreach ($manualFiles as $file) {
    echo "  - $file\n";
}

// Önce dry run ile kontrol et
echo "\n🔍 Dry Run (Kontrol):\n";
$results = TestCleaner::cleanFiles($manualFiles, true);

// Gerçek silme işlemi (isteğe bağlı)
// $results = TestCleaner::cleanFiles($manualFiles, false);

echo "\n" . str_repeat("-", 50) . "\n\n";

// 2. Temp klasörünü otomatik temizle
echo "2️⃣ Temp Klasörü Otomatik Temizleme:\n";
echo "-----------------------------------\n";

// Önce hangi dosyalar var görelim
$tempFiles = TestCleaner::listFiles('Temp');
if (!empty($tempFiles)) {
    echo "📁 Temp klasöründeki dosyalar:\n";
    foreach ($tempFiles as $file) {
        echo "  - $file\n";
    }
    
    echo "\n🔍 Dry Run ile kontrol:\n";
    TestCleaner::cleanTempFiles(true);
    
    // Gerçek temizleme (isteğe bağlı)
    // TestCleaner::cleanTempFiles(false);
} else {
    echo "✅ Temp klasörü zaten temiz\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// 3. Eski log dosyalarını temizle
echo "3️⃣ Eski Log Dosyalarını Temizleme:\n";
echo "----------------------------------\n";

$logFiles = TestCleaner::listFiles('Logs', ['log']);
if (!empty($logFiles)) {
    echo "📁 Log dosyaları:\n";
    foreach ($logFiles as $file) {
        echo "  - $file\n";
    }
    
    echo "\n🔍 30 günden eski logları kontrol et:\n";
    TestCleaner::cleanOldLogs(30, true);
    
    // Gerçek temizleme (isteğe bağlı)
    // TestCleaner::cleanOldLogs(30, false);
} else {
    echo "✅ Log klasöründe dosya yok\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// 4. Tüm geçici dosyaları toplu temizleme
echo "4️⃣ Toplu Temizleme Örneği:\n";
echo "-------------------------\n";

// Farklı klasörlerden gereksiz dosyaları topla
$allTempFiles = array_merge(
    TestCleaner::listFiles('Temp'),
    array_filter(TestCleaner::listFiles('Orders'), function($file) {
        return strpos($file, 'temp_') !== false || strpos($file, 'old_') !== false;
    }),
    array_filter(TestCleaner::listFiles('Products'), function($file) {
        return strpos($file, 'temp_') !== false || strpos($file, 'debug_') !== false;
    })
);

if (!empty($allTempFiles)) {
    echo "📋 Toplu temizlenecek dosyalar:\n";
    foreach ($allTempFiles as $file) {
        echo "  - $file\n";
    }
    
    echo "\n🔍 Toplu dry run:\n";
    TestCleaner::cleanFiles($allTempFiles, true);
    
    // Gerçek toplu temizleme (isteğe bağlı)
    // TestCleaner::cleanFiles($allTempFiles, false);
} else {
    echo "✅ Temizlenecek geçici dosya yok\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Örnek kullanım tamamlandı!\n";
echo "\n💡 Gerçek silme işlemi için:\n";
echo "   - Dry run satırlarını comment out edin\n";
echo "   - Gerçek silme satırlarının comment'lerini kaldırın\n";
echo "\n🚨 Dikkat: Silinen dosyalar geri getirilemez!\n";
