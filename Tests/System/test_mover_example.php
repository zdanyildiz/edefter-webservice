<?php
/**
 * Test Taşıma Yardımcısı Örnek Kullanım
 * 
 * TestMover sınıfının nasıl kullanılacağını gösterir.
 * Test dosyalarını organize etmek için kullanılır.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// TestMover sınıfını yükle
include_once __DIR__ . '/TestMover.php';

echo "📦 Test Dosya Taşıma Yardımcısı - Örnek Kullanım\n";
echo "================================================\n\n";

// 1. Test Framework dosyalarını organize et
echo "1️⃣ Test Framework Organizasyonu:\n";
echo "--------------------------------\n";

echo "🔍 Önce dry run ile kontrol edilecek...\n";
$dryResults = TestMover::organizeTestFramework(true);

if (!empty($dryResults['moved'])) {
    echo "\n📋 Taşınacak dosyalar:\n";
    foreach ($dryResults['moved'] as $item) {
        echo "  - " . $item['source'] . " → " . $item['target'] . "\n";
    }
    
    if (!empty($dryResults['updated_refs'])) {
        echo "\n🔗 Güncellenecek referanslar:\n";
        foreach ($dryResults['updated_refs'] as $ref) {
            echo "  - " . $ref['file'] . "\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n";
    echo "💡 Gerçek taşıma için dry-run yorumunu kaldırın\n";
    
    // Gerçek taşıma (yorumu kaldırarak aktif hale getirin)
    // echo "\n🚀 Gerçek taşıma başlatılıyor...\n";
    // $realResults = TestMover::organizeTestFramework(false);
    
} else {
    echo "ℹ️  Taşınacak framework dosyası bulunamadı\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// 2. Manuel dosya taşıma örneği
echo "2️⃣ Manuel Dosya Taşıma Örneği:\n";
echo "------------------------------\n";

$manualMoveMap = [
    // 'Temp/old_file.php' => 'Archive/old_file.php',
    // 'System/debug_helper.php' => 'Utilities/debug_helper.php',
    // 'Products/temp_product.php' => 'Archive/Products/temp_product.php'
];

if (!empty($manualMoveMap)) {
    echo "📋 Manuel taşıma listesi:\n";
    foreach ($manualMoveMap as $source => $target) {
        echo "  - $source → $target\n";
    }
    
    echo "\n🔍 Dry run kontrolü:\n";
    // $manualResults = TestMover::moveFiles($manualMoveMap, true, true);
    
    // Gerçek taşıma (isteğe bağlı)
    // $manualResults = TestMover::moveFiles($manualMoveMap, false, true);
} else {
    echo "ℹ️  Manuel taşıma örneği için dosya listesi boş\n";
    echo "💡 Yukarıdaki \$manualMoveMap dizisini doldurun\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// 3. Referans güncellemesi örneği
echo "3️⃣ Referans Güncellemesi:\n";
echo "-------------------------\n";
echo "🔗 TestMover otomatik olarak şu referansları günceller:\n";
echo "   - include_once 'dosya.php'\n";
echo "   - require_once 'dosya.php'\n";
echo "   - include 'dosya.php'\n";
echo "   - require 'dosya.php'\n";
echo "\n💡 Bu sayede taşınan dosyalar otomatik olarak çalışmaya devam eder\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// 4. Güvenlik özellikleri
echo "4️⃣ Güvenlik Özellikleri:\n";
echo "-----------------------\n";
echo "🛡️  TestMover güvenlik özellikleri:\n";
echo "   ✅ Sadece Tests/ klasörü içinde çalışır\n";
echo "   ✅ Hassas dosyalar için otomatik backup\n";
echo "   ✅ Dosya uzantısı kontrolü\n";
echo "   ✅ Hedef dosya çakışma kontrolü\n";
echo "   ✅ Dry run ile önce kontrol\n";
echo "   ✅ Otomatik referans güncelleme\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Örnek kullanım tamamlandı!\n";
echo "\n💡 Gerçek taşıma için:\n";
echo "   - Dry run satırlarını comment out edin\n";
echo "   - Gerçek taşıma satırlarının comment'lerini kaldırın\n";
echo "\n🚨 Dikkat: Taşınan dosyalar geri getirilemez!\n";
echo "🔄 Hassas dosyalar için otomatik backup oluşturulur\n";
