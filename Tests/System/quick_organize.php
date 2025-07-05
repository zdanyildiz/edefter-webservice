<?php
/**
 * Hızlı Test Framework Organizasyonu
 * 
 * Test framework sınıflarını TestModel dizinine organize eder.
 * Referansları otomatik günceller ve backup oluşturur.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// TestMover sınıfını yükle
include_once __DIR__ . '/TestMover.php';

echo "⚡ Hızlı Test Framework Organizasyonu\n";
echo "====================================\n\n";

echo "🔍 Mevcut durum kontrol ediliyor...\n";

// Mevcut test sınıflarını kontrol et
$testFiles = [
    'TestModel/TestAssert.php',
    'TestModel/TestDataGenerator.php', 
    'TestModel/TestLogger.php',
    'TestModel/TestRunner.php',
    'TestModel/TestValidator.php'
];

$existingFiles = [];
$testsPath = realpath(__DIR__ . '/../');

foreach ($testFiles as $file) {
    $filePath = $testsPath . DIRECTORY_SEPARATOR . $file;
    if (file_exists($filePath)) {
        $existingFiles[] = $file;
        echo "📄 Bulundu: $file\n";
    }
}

if (empty($existingFiles)) {
    echo "ℹ️  Test framework dosyaları zaten organize edilmiş veya bulunamadı\n";
    echo "✅ İşlem tamamlandı\n";
    exit(0);
}

echo "\n📊 Toplam " . count($existingFiles) . " dosya taşınacak\n";
echo "🎯 Hedef: Tests/TestModel/ dizini\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 Önce kontrol ediliyor (Dry Run):\n";
echo str_repeat("=", 50) . "\n";

// Dry run ile kontrol
$dryResults = TestMover::organizeTestFramework(true);

if (empty($dryResults['moved']) && empty($dryResults['errors'])) {
    echo "ℹ️  Taşınacak dosya yok\n";
    exit(0);
}

if (!empty($dryResults['errors'])) {
    echo "❌ Hata tespit edildi:\n";
    foreach ($dryResults['errors'] as $error) {
        echo "   - " . $error['source'] . ": " . $error['message'] . "\n";
    }
    echo "\n🛑 Hatalar düzeltilmeden taşıma yapılamaz\n";
    exit(1);
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🚀 Gerçek taşıma başlatılıyor:\n";
echo str_repeat("=", 50) . "\n";

// Gerçek taşıma
$realResults = TestMover::organizeTestFramework(false);

// Final özet
echo "\n🎯 FİNAL ÖZET:\n";
echo str_repeat("=", 30) . "\n";
echo "✅ Taşınan: " . count($realResults['moved']) . " dosya\n";
echo "🔗 Güncellenen referans: " . count($realResults['updated_refs']) . " dosya\n";
echo "⏭️  Atlanan: " . count($realResults['skipped']) . " dosya\n";
echo "❌ Hatalı: " . count($realResults['errors']) . " dosya\n";

if (count($realResults['moved']) > 0) {
    echo "\n📦 Taşınan dosyalar:\n";
    foreach ($realResults['moved'] as $item) {
        echo "   ✓ " . $item['source'] . " → " . $item['target'] . "\n";
    }
}

if (count($realResults['updated_refs']) > 0) {
    echo "\n🔗 Güncellenen referanslar:\n";
    foreach ($realResults['updated_refs'] as $ref) {
        echo "   ✓ " . $ref['file'] . " (" . $ref['changes'] . " değişiklik)\n";
    }
}

echo "\n" . str_repeat("-", 50) . "\n";
echo "📋 Tests/index.php güncellenmesi gerekebilir!\n";
echo "🔄 Include yollarını kontrol edin\n";

echo "\n🎉 Test framework organizasyonu tamamlandı!\n";
echo "💡 Dosyalar artık Tests/TestModel/ dizininde organize\n";
