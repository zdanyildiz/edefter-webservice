<?php
/**
 * E-Defter kullanım tablosu yapısını kontrol et
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('E-Defter Tablo Yapısı Kontrolü');

try {
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Direkt SQL ile tablo yapısını kontrol et
    $describeSQL = "DESCRIBE edefter_usage";
    $result = $db->query($describeSQL);
    
    if ($result) {
        TestLogger::info('Tablo yapısı:');
        $columns = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field'];
            TestLogger::info("  - {$row['Field']} ({$row['Type']})");
        }
        
        TestLogger::success('Tablo başarıyla oluşturulmuş, sütun sayısı: ' . count($columns));
        
        // Beklenen sütunları kontrol et
        $expectedColumns = ['id', 'user_identifier', 'user_type', 'usage_date', 'usage_count', 'last_usage_time', 'created_at', 'updated_at'];
        foreach ($expectedColumns as $expected) {
            if (in_array($expected, $columns)) {
                TestLogger::success("✅ $expected sütunu mevcut");
            } else {
                TestLogger::error("❌ $expected sütunu eksik");
            }
        }
        
    } else {
        TestLogger::error('Tablo yapısı sorgulanamadı');
    }
    
    // Örnek veri ekleme testi
    $insertSQL = "INSERT INTO edefter_usage (user_identifier, user_type, usage_date, usage_count, last_usage_time) 
                  VALUES ('test_session_123', 'visitor', CURDATE(), 1, NOW())
                  ON DUPLICATE KEY UPDATE 
                  usage_count = usage_count + 1, 
                  last_usage_time = NOW()";
    
    $insertResult = $db->query($insertSQL);
    TestAssert::assertTrue($insertResult !== false, 'Örnek veri eklenebilmeli');
    TestLogger::success('Örnek veri başarıyla eklendi');
    
    // Veriyi sorgula
    $selectSQL = "SELECT * FROM edefter_usage WHERE user_identifier = 'test_session_123'";
    $selectResult = $db->query($selectSQL);
    $row = $selectResult->fetch(PDO::FETCH_ASSOC);
    
    TestAssert::assertNotEmpty($row, 'Eklenen veri sorgulanabilmeli');
    TestLogger::success("Sorgu sonucu: ID={$row['id']}, Tip={$row['user_type']}, Sayı={$row['usage_count']}");
    
    // Test verisini temizle
    $deleteSQL = "DELETE FROM edefter_usage WHERE user_identifier = 'test_session_123'";
    $db->query($deleteSQL);
    TestLogger::info('Test verisi temizlendi');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

TestHelper::endTest();
