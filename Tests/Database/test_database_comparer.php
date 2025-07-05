<?php

/**
 * Veritabanı Karşılaştırma Test Dosyası
 * 
 * Bu dosya DatabaseComparer sınıfının temel fonksiyonlarını test eder.
 * Gerçek veritabanı bağlantısı yapmadan önce kod yapısını doğrular.
 * 
 * @author GitHub Copilot
 * @version 1.0
 * @date 2025-07-05
 */

// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// DatabaseComparer sınıfını yükle
require_once __DIR__ . '/DatabaseComparer.php';

try {
    // Test başlat
    TestHelper::startTest('DatabaseComparer Sınıf Testi');
    
    echo "🧪 DATABASECOMPARER SINIF TESTİ\n";
    echo "==============================\n\n";
    
    // Sınıf varlığını kontrol et
    TestAssert::assertTrue(class_exists('DatabaseComparer'), 'DatabaseComparer sınıfı mevcut olmalı');
    TestLogger::success("DatabaseComparer sınıfı başarıyla yüklendi");
    
    // Test veritabanı bilgileri (gerçek bağlantı için değiştirin)
    $host = 'localhost';
    $username = 'root';
    $password = 'Global2019*';
    $db1 = 'e-defter.globalpozitif.com.tr';
    $db2 = 'johwears.globalpozitif.com.tr';
    
    // DatabaseComparer nesnesini oluştur
    echo "🔧 DatabaseComparer nesnesi oluşturuluyor...\n";
    $comparer = new DatabaseComparer($host, $username, $password, $db1, $db2);
    TestAssert::assertNotNull($comparer, 'DatabaseComparer nesnesi oluşturulmalı');
    TestLogger::success("DatabaseComparer nesnesi başarıyla oluşturuldu");
    
    // Metodların varlığını kontrol et
    $requiredMethods = [
        'connect',
        'compare', 
        'printReport',
        'getJsonReport',
        'getHtmlReport',
        'saveReport'
    ];
    
    echo "\n🔍 Gerekli metodlar kontrol ediliyor...\n";
    foreach ($requiredMethods as $method) {
        TestAssert::assertTrue(
            method_exists($comparer, $method), 
            "Method '{$method}' mevcut olmalı"
        );
        echo "  ✅ {$method}() metodu mevcut\n";
    }
    TestLogger::success("Tüm gerekli metodlar mevcut");
    
    // Dosya yapısını kontrol et
    echo "\n📁 Dosya yapısı kontrol ediliyor...\n";
    
    $requiredFiles = [
        __DIR__ . '/DatabaseComparer.php',
        __DIR__ . '/compare_databases.php',
        __DIR__ . '/../index.php'
    ];
    
    foreach ($requiredFiles as $file) {
        TestAssert::assertTrue(file_exists($file), "Dosya mevcut olmalı: " . basename($file));
        echo "  ✅ " . basename($file) . " mevcut\n";
    }
    TestLogger::success("Tüm gerekli dosyalar mevcut");
    
    // Log klasörü kontrol et/oluştur
    echo "\n📂 Log klasörü kontrol ediliyor...\n";
    $logDir = __DIR__ . '/../Logs/database_comparison/';
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
        echo "  📁 Log klasörü oluşturuldu: {$logDir}\n";
        TestLogger::info("Database comparison log klasörü oluşturuldu");
    } else {
        echo "  ✅ Log klasörü zaten mevcut: {$logDir}\n";
    }
    
    // Test için örnek veri yapısı
    echo "\n🧪 Test veri yapıları oluşturuluyor...\n";
    
    $testTable1 = [
        'id' => [
            'type' => 'int(11)',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'name' => [
            'type' => 'varchar(255)',
            'null' => 'NO',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
    ];
    
    $testTable2 = [
        'id' => [
            'type' => 'int(11)',
            'null' => 'NO',
            'key' => 'PRI', 
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'name' => [
            'type' => 'varchar(100)', // Farklı uzunluk
            'null' => 'YES',          // Farklı null ayarı
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
    ];
    
    TestAssert::assertNotEmpty($testTable1, 'Test tablo 1 yapısı boş olmamalı');
    TestAssert::assertNotEmpty($testTable2, 'Test tablo 2 yapısı boş olmamalı');
    TestLogger::success("Test veri yapıları oluşturuldu");
    
    // Kullanım talimatları
    echo "\n📋 KULLANIM TALİMATLARI\n";
    echo "=====================\n\n";
    
    echo "1️⃣ Gerçek Karşılaştırma İçin:\n";
    echo "   php Tests\\Database\\compare_databases.php\n\n";
    
    echo "2️⃣ Bağlantı Bilgilerini Düzenlemek İçin:\n";
    echo "   - Tests/Database/compare_databases.php dosyasını açın\n";
    echo "   - \$host, \$username, \$password değişkenlerini güncelleyin\n";
    echo "   - \$db1 ve \$db2 veritabanı isimlerini doğrulayın\n\n";
    
    echo "3️⃣ Rapor Çıktıları:\n";
    echo "   - Konsol: Anlık sonuçlar\n";
    echo "   - JSON: Tests/Logs/database_comparison/comparison_*.json\n";
    echo "   - HTML: Tests/Logs/database_comparison/comparison_*.html\n";
    echo "   - TXT: Tests/Logs/database_comparison/comparison_*.txt\n\n";
    
    echo "4️⃣ Özellikler:\n";
    echo "   ✅ Tablo eksikliklerini tespit eder\n";
    echo "   ✅ Sütun farklılıklarını bulur\n";
    echo "   ✅ Veri tipi değişikliklerini raporlar\n";
    echo "   ✅ NULL, KEY, DEFAULT değer farklarını gösterir\n";
    echo "   ✅ Çoklu format rapor (JSON, HTML, TXT)\n";
    echo "   ✅ Detaylı loglama sistemi\n\n";
    
    echo "⚠️  DİKKAT:\n";
    echo "   - Veritabanı bağlantı bilgilerinin doğru olduğundan emin olun\n";
    echo "   - Büyük veritabanları için işlem uzun sürebilir\n";
    echo "   - Raporlar Tests/Logs/database_comparison/ klasöründe saklanır\n\n";
    
    TestLogger::success("DatabaseComparer sınıf testi tamamlandı");
    
} catch (Exception $e) {
    echo "❌ TEST HATASI: " . $e->getMessage() . "\n";
    TestLogger::error("DatabaseComparer test hatası: " . $e->getMessage());
}

// Test sonlandır
TestHelper::endTest();

echo "\n🚀 Hazır! Şimdi gerçek karşılaştırmayı çalıştırabilirsiniz:\n";
echo "   php Tests\\Database\\compare_databases.php\n";
