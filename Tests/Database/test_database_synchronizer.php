<?php

/**
 * Veritabanı Senkronizasyon Test Dosyası
 * 
 * Bu dosya DatabaseSynchronizer sınıfının temel fonksiyonlarını test eder.
 * 
 * @author GitHub Copilot
 * @version 1.0
 * @date 2025-07-05
 */

// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// DatabaseSynchronizer sınıfını yükle
require_once __DIR__ . '/DatabaseSynchronizer.php';

try {
    // Test başlat
    TestHelper::startTest('DatabaseSynchronizer Sınıf Testi');
    
    echo "🧪 DATABASESYNCHRONIZER SINIF TESTİ\n";
    echo "==================================\n\n";
    
    // Sınıf varlığını kontrol et
    TestAssert::assertTrue(class_exists('DatabaseSynchronizer'), 'DatabaseSynchronizer sınıfı mevcut olmalı');
    TestLogger::success("DatabaseSynchronizer sınıfı başarıyla yüklendi");
    
    // Test veritabanı bilgileri
    $host = 'localhost';
    $username = 'root';
    $password = 'Global2019*';
    $db1 = 'e-defter.globalpozitif.com.tr';
    $db2 = 'johwears.globalpozitif.com.tr';
    
    // DatabaseSynchronizer nesnesini oluştur
    echo "🔧 DatabaseSynchronizer nesnesi oluşturuluyor...\n";
    $synchronizer = new DatabaseSynchronizer($host, $username, $password, $db1, $db2);
    TestAssert::assertNotNull($synchronizer, 'DatabaseSynchronizer nesnesi oluşturulmalı');
    TestLogger::success("DatabaseSynchronizer nesnesi başarıyla oluşturuldu");
    
    // Metodların varlığını kontrol et
    $requiredMethods = [
        'connect',
        'findMissingTables',
        'findBannerColumnDifferences',
        'generateMigrationSQL',
        'generateBackupSQL',
        'executeMigration',
        'printSyncReport',
        'getJsonReport',
        'saveMigrationFile'
    ];
    
    echo "\n🔍 Gerekli metodlar kontrol ediliyor...\n";
    foreach ($requiredMethods as $method) {
        TestAssert::assertTrue(
            method_exists($synchronizer, $method), 
            "Method '{$method}' mevcut olmalı"
        );
        echo "  ✅ {$method}() metodu mevcut\n";
    }
    TestLogger::success("Tüm gerekli metodlar mevcut");
    
    // Dosya yapısını kontrol et
    echo "\n📁 Dosya yapısı kontrol ediliyor...\n";
    
    $requiredFiles = [
        __DIR__ . '/DatabaseSynchronizer.php',
        __DIR__ . '/sync_databases.php',
        __DIR__ . '/../index.php'
    ];
    
    foreach ($requiredFiles as $file) {
        TestAssert::assertTrue(file_exists($file), "Dosya mevcut olmalı: " . basename($file));
        echo "  ✅ " . basename($file) . " mevcut\n";
    }
    TestLogger::success("Tüm gerekli dosyalar mevcut");
    
    // Log klasörü kontrol et/oluştur
    echo "\n📂 Migration klasörü kontrol ediliyor...\n";
    $migrationDir = __DIR__ . '/../Logs/migrations/';
    
    if (!is_dir($migrationDir)) {
        mkdir($migrationDir, 0755, true);
        echo "  📁 Migration klasörü oluşturuldu: {$migrationDir}\n";
        TestLogger::info("Migration klasörü oluşturuldu");
    } else {
        echo "  ✅ Migration klasörü zaten mevcut: {$migrationDir}\n";
    }
    
    // Senkronizasyon planını analiz et
    echo "\n📊 Senkronizasyon planı analizi...\n";
    
    // Karşılaştırma raporundan verileri al
    $comparisonFile = __DIR__ . '/../Logs/database_comparison/comparison_2025-07-05_18-57-30.json';
    
    if (file_exists($comparisonFile)) {
        $comparisonData = json_decode(file_get_contents($comparisonFile), true);
        
        echo "  📋 Analiz edilen veriler:\n";
        echo "    • DB2'de olup DB1'de olmayan tablolar: " . count($comparisonData['tables_only_in_db2']) . "\n";
        echo "    • Banner sistemi sütun farklılıkları: " . count($comparisonData['column_differences']) . "\n";
        
        // DB2'de olan ama DB1'de olmayan tablolar
        if (!empty($comparisonData['tables_only_in_db2'])) {
            echo "\n  ➕ Eklenecek tablolar:\n";
            foreach ($comparisonData['tables_only_in_db2'] as $table) {
                echo "    • {$table}\n";
            }
        }
        
        // Banner sistemi farklılıkları
        if (isset($comparisonData['column_differences'])) {
            echo "\n  🎨 Banner sistemi güncellemeleri:\n";
            foreach ($comparisonData['column_differences'] as $table => $differences) {
                if (strpos($table, 'banner') !== false) {
                    echo "    • {$table}: " . count($differences) . " sütun farkı\n";
                }
            }
        }
        
        TestLogger::success("Karşılaştırma verisi analiz edildi");
        
    } else {
        echo "  ⚠️  Karşılaştırma raporu bulunamadı\n";
        echo "  💡 Önce php Tests\\Database\\compare_databases.php çalıştırın\n";
        TestLogger::warning("Karşılaştırma raporu bulunamadı");
    }
    
    // Kullanım talimatları
    echo "\n📋 KULLANIM TALİMATLARI\n";
    echo "=====================\n\n";
    
    echo "1️⃣ Dry Run (Analiz Modu):\n";
    echo "   php Tests\\Database\\sync_databases.php\n";
    echo "   php Tests\\Database\\sync_databases.php dry-run\n\n";
    
    echo "2️⃣ Gerçek Senkronizasyon:\n";
    echo "   php Tests\\Database\\sync_databases.php execute\n\n";
    
    echo "3️⃣ Senkronizasyon Kapsamı:\n";
    echo "   ✅ DB2'de olan tabloları DB1'e ekler\n";
    echo "   ✅ Banner sistemi sütunlarını DB1'e aktarır\n";
    echo "   ✅ Güvenli backup oluşturur\n";
    echo "   ✅ Geri alınabilir migration sistemi\n\n";
    
    echo "4️⃣ Çıktı Dosyaları:\n";
    echo "   📄 Migration SQL: Tests/Logs/migrations/sync_migration_*.sql\n";
    echo "   📄 JSON Rapor: Tests/Logs/migrations/sync_report_*.json\n";
    echo "   📄 Log Dosyası: Tests/Logs/test_*.log\n\n";
    
    echo "5️⃣ Güvenlik Özellikleri:\n";
    echo "   🔒 Transaction desteği (rollback)\n";
    echo "   💾 Otomatik backup oluşturma\n";
    echo "   🧪 Dry run mode (risk yok)\n";
    echo "   ✋ Manuel onay sistemi\n\n";
    
    echo "⚠️  DİKKAT:\n";
    echo "   - Önce dry-run ile test edin\n";
    echo "   - Veritabanının backup'ını alın\n";
    echo "   - Execute modunda dikkatli olun\n";
    echo "   - Migration dosyalarını saklayın\n\n";
    
    echo "📈 SENKRONIZASYON PLANI:\n";
    echo "   1. language_copy_jobs tablosu eklenecek\n";
    echo "   2. site_config_versions tablosu eklenecek\n";
    echo "   3. banner_groups tablosuna 11 sütun eklenecek\n";
    echo "   4. banner_layouts tablosuna 2 sütun eklenecek\n";
    echo "   5. banner_styles tablosuna 5 sütun eklenecek + 5 sütun güncellenecek\n";
    echo "   6. language_*_mapping tablolarına çeviri takip sütunları eklenecek\n\n";
    
    TestLogger::success("DatabaseSynchronizer sınıf testi tamamlandı");
    
} catch (Exception $e) {
    echo "❌ TEST HATASI: " . $e->getMessage() . "\n";
    TestLogger::error("DatabaseSynchronizer test hatası: " . $e->getMessage());
}

// Test sonlandır
TestHelper::endTest();

echo "\n🚀 Hazır! Şimdi senkronizasyonu çalıştırabilirsiniz:\n";
echo "   🧪 Dry Run: php Tests\\Database\\sync_databases.php\n";
echo "   🚀 Execute: php Tests\\Database\\sync_databases.php execute\n";
