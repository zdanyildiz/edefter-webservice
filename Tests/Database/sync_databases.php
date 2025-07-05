<?php

/**
 * Veritabanı Senkronizasyon Scripti
 * 
 * Bu script DB2'deki eksik tabloları DB1'e ekler ve banner sistemi yapısını senkronize eder.
 * Güvenli migration sistemi ile geri alınabilir değişiklikler yapar.
 * 
 * Kullanım:
 * php Tests/Database/sync_databases.php [dry-run|execute]
 * 
 * @author GitHub Copilot
 * @version 1.0
 * @date 2025-07-05
 */

// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// DatabaseSynchronizer sınıfını yükle
require_once __DIR__ . '/DatabaseSynchronizer.php';

// Komut satırı parametresini kontrol et
$mode = isset($argv[1]) ? $argv[1] : 'dry-run';
$execute_migration = ($mode === 'execute');

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$username = 'root';
$password = 'Global2019*';
$db1 = 'e-defter.globalpozitif.com.tr';  // Hedef DB
$db2 = 'johwears.globalpozitif.com.tr';   // Kaynak DB

try {
    // Test başlat
    TestHelper::startTest('Veritabanı Senkronizasyonu');
    
    echo "🔄 VERİTABANI SENKRONİZASYONU BAŞLATILIYOR\n";
    echo "========================================\n\n";
    
    if ($execute_migration) {
        echo "⚠️  GERÇEK MİGRATİON MODU AKTİF!\n";
        echo "⚠️  Bu işlem veritabanını değiştirecek!\n\n";
    } else {
        echo "🧪 DRY RUN MODU AKTİF\n";
        echo "🔍 Sadece analiz yapılacak, değişiklik uygulanmayacak\n\n";
    }
    
    TestLogger::info("Veritabanı senkronizasyon işlemi başlatılıyor");
    TestLogger::info("Hedef DB (DB1): {$db1}");
    TestLogger::info("Kaynak DB (DB2): {$db2}");
    TestLogger::info("Mod: " . ($execute_migration ? 'EXECUTE' : 'DRY-RUN'));
    
    // DatabaseSynchronizer'ı başlat
    $synchronizer = new DatabaseSynchronizer($host, $username, $password, $db1, $db2);
    
    // Bağlantıları kur
    echo "🔌 Veritabanı bağlantıları kuruluyor...\n";
    $synchronizer->connect();
    TestLogger::success("Veritabanı bağlantıları başarılı");
    
    // 1. Eksik tabloları tespit et
    echo "🔍 ADIM 1: Eksik tablolar tespit ediliyor...\n";
    $missing_tables = $synchronizer->findMissingTables();
    TestLogger::info("Eksik tablo sayısı: " . count($missing_tables));
    
    // 2. Banner sistemi sütun farklılıklarını tespit et
    echo "🎨 ADIM 2: Banner sistemi farklılıkları tespit ediliyor...\n";
    $synchronizer->findBannerColumnDifferences();
    TestLogger::info("Banner sistemi sütun farklılıkları tespit edildi");
    
    // 3. Migration SQL komutlarını oluştur
    echo "📝 ADIM 3: Migration SQL komutları oluşturuluyor...\n";
    $synchronizer->generateMigrationSQL();
    TestLogger::success("Migration SQL komutları oluşturuldu");
    
    // 4. Backup SQL komutlarını oluştur
    echo "💾 ADIM 4: Backup SQL komutları oluşturuluyor...\n";
    $synchronizer->generateBackupSQL();
    TestLogger::success("Backup SQL komutları oluşturuldu");
    
    // 5. Raporu yazdır
    echo "📊 ADIM 5: Senkronizasyon raporu oluşturuluyor...\n";
    $synchronizer->printSyncReport();
    
    // 6. Migration dosyasını kaydet
    echo "💾 ADIM 6: Migration dosyası kaydediliyor...\n";
    
    $timestamp = date('Y-m-d_H-i-s');
    $migrationsDir = __DIR__ . '/../Logs/migrations/';
    
    // Klasörü oluştur
    if (!is_dir($migrationsDir)) {
        mkdir($migrationsDir, 0755, true);
        TestLogger::info("Migration klasörü oluşturuldu: {$migrationsDir}");
    }
    
    // Migration dosyasını kaydet
    $migrationFile = $migrationsDir . "sync_migration_{$timestamp}.sql";
    $synchronizer->saveMigrationFile($migrationFile);
    echo "📄 Migration dosyası: {$migrationFile}\n";
    TestLogger::success("Migration dosyası kaydedildi: {$migrationFile}");
    
    // JSON raporu kaydet
    $jsonFile = $migrationsDir . "sync_report_{$timestamp}.json";
    file_put_contents($jsonFile, $synchronizer->getJsonReport());
    echo "📄 JSON raporu: {$jsonFile}\n";
    TestLogger::success("JSON raporu kaydedildi: {$jsonFile}");
    
    // 7. Migration'ı uygula (DRY RUN veya GERÇEK)
    echo "\n🚀 ADIM 7: Migration uygulaması...\n";
    
    if (!$execute_migration) {
        echo "🧪 DRY RUN: Migration simüle ediliyor...\n\n";
        $synchronizer->executeMigration(true);
        
        echo "\n📋 GERÇEK MİGRATİON İÇİN:\n";
        echo "php Tests\\Database\\sync_databases.php execute\n\n";
        
        TestLogger::info("DRY RUN migration tamamlandı");
        
    } else {
        echo "⚠️  Son onay: Gerçek migration uygulanacak. Devam etmek istiyor musunuz? (y/N): ";
        $handle = fopen("php://stdin", "r");
        $confirmation = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($confirmation) === 'y' || strtolower($confirmation) === 'yes') {
            echo "\n🚀 Gerçek migration başlatılıyor...\n";
            $synchronizer->executeMigration(false);
            TestLogger::success("Gerçek migration başarıyla tamamlandı");
            
            echo "\n🎉 Senkronizasyon başarıyla tamamlandı!\n";
            echo "✅ Değişiklikler uygulandı\n";
            echo "💾 Backup'lar oluşturuldu\n";
            
        } else {
            echo "\n❌ Migration iptal edildi\n";
            TestLogger::info("Kullanıcı tarafından migration iptal edildi");
        }
    }
    
    echo "\n📁 Dosyalar: {$migrationsDir}\n";
    TestLogger::success("Veritabanı senkronizasyon işlemi tamamlandı");
    
} catch (Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
    TestLogger::error("Veritabanı senkronizasyon hatası: " . $e->getMessage());
    
    // Hata detaylarını logla
    if (isset($e)) {
        TestLogger::error("Hata dosyası: " . $e->getFile());
        TestLogger::error("Hata satırı: " . $e->getLine());
        TestLogger::error("Stack trace: " . $e->getTraceAsString());
    }
}

// Test sonlandır
TestHelper::endTest();

echo "\n💡 KULLANIM ÖRNEKLERİ:\n";
echo "  🧪 Dry Run (Sadece Analiz):\n";
echo "    php Tests\\Database\\sync_databases.php\n";
echo "    php Tests\\Database\\sync_databases.php dry-run\n\n";
echo "  🚀 Gerçek Migration:\n";
echo "    php Tests\\Database\\sync_databases.php execute\n\n";
echo "🔍 Detaylı loglar için: Tests/Logs/ klasörünü kontrol edin\n";
echo "📄 Migration dosyası: Tests/Logs/migrations/ klasöründe\n";
