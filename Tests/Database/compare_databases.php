<?php

/**
 * Veritabanı Tablo Karşılaştırma Scripti
 * 
 * Bu script iki veritabanının tablo ve sütun yapılarını karşılaştırır.
 * Kullanıcı tarafından verilen bağlantı bilgileriyle çalışır.
 * 
 * Kullanım:
 * php Tests/Database/compare_databases.php
 * 
 * @author GitHub Copilot
 * @version 1.0
 * @date 2025-07-05
 */

// Test framework'ünü yükle
include_once __DIR__ . '/../index.php';

// DatabaseComparer sınıfını yükle
require_once __DIR__ . '/DatabaseComparer.php';

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$username = 'root';
$password = 'Global2019*';
$db1 = 'e-defter.globalpozitif.com.tr';
$db2 = 'johwears.globalpozitif.com.tr';

try {
    // Test başlat
    TestHelper::startTest('Veritabanı Karşılaştırma');
    
    echo "🚀 VERİTABANI KARŞILAŞTIRMA BAŞLATILIYOR\n";
    echo "======================================\n\n";
    
    TestLogger::info("Veritabanı karşılaştırma işlemi başlatılıyor");
    TestLogger::info("DB1: {$db1}");
    TestLogger::info("DB2: {$db2}");
    TestLogger::info("Host: {$host}");
    
    // DatabaseComparer'ı başlat
    $comparer = new DatabaseComparer($host, $username, $password, $db1, $db2);
    
    // Bağlantıları kur
    echo "🔌 Veritabanı bağlantıları kuruluyor...\n";
    $comparer->connect();
    TestLogger::success("Veritabanı bağlantıları başarılı");
    
    // Karşılaştırmayı yap
    echo "🔍 Karşılaştırma işlemi başlatılıyor...\n";
    $comparer->compare();
    TestLogger::success("Karşılaştırma işlemi tamamlandı");
    
    // Konsol raporu yazdır
    $comparer->printReport();
    
    // Raporları kaydet
    echo "\n💾 Raporlar kaydediliyor...\n";
    
    $timestamp = date('Y-m-d_H-i-s');
    $reportsDir = __DIR__ . '/../Logs/database_comparison/';
    
    // Klasörü oluştur
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
        TestLogger::info("Rapor klasörü oluşturuldu: {$reportsDir}");
    }
    
    // JSON raporu kaydet
    $jsonFile = $reportsDir . "comparison_{$timestamp}.json";
    $comparer->saveReport('json', $jsonFile);
    echo "📄 JSON raporu: {$jsonFile}\n";
    TestLogger::success("JSON raporu kaydedildi: {$jsonFile}");
    
    // HTML raporu kaydet
    $htmlFile = $reportsDir . "comparison_{$timestamp}.html";
    $comparer->saveReport('html', $htmlFile);
    echo "🌐 HTML raporu: {$htmlFile}\n";
    TestLogger::success("HTML raporu kaydedildi: {$htmlFile}");
    
    // TXT raporu kaydet
    $txtFile = $reportsDir . "comparison_{$timestamp}.txt";
    $comparer->saveReport('txt', $txtFile);
    echo "📝 TXT raporu: {$txtFile}\n";
    TestLogger::success("TXT raporu kaydedildi: {$txtFile}");
    
    echo "\n✅ Tüm işlemler başarıyla tamamlandı!\n";
    echo "📁 Raporlar: {$reportsDir}\n";
    
    TestLogger::success("Veritabanı karşılaştırma işlemi başarıyla tamamlandı");
    
} catch (Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
    TestLogger::error("Veritabanı karşılaştırma hatası: " . $e->getMessage());
    
    // Hata detaylarını logla
    if (isset($e)) {
        TestLogger::error("Hata dosyası: " . $e->getFile());
        TestLogger::error("Hata satırı: " . $e->getLine());
        TestLogger::error("Stack trace: " . $e->getTraceAsString());
    }
}

// Test sonlandır
TestHelper::endTest();

echo "\n🔍 Detaylı loglar için: Tests/Logs/ klasörünü kontrol edin\n";
echo "📊 HTML raporu web tarayıcısında açılabilir\n";
echo "📋 JSON raporu programatik kullanım için uygundur\n";
