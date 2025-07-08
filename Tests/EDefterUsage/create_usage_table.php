<?php
/**
 * E-Defter kullanım geçmişi tablosu oluşturma scripti
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('E-Defter Kullanım Tablosu Oluşturma');

try {
    $db = TestDatabase::getInstance();
    TestAssert::assertNotNull($db, 'Veritabanı bağlantısı kurulmalı');
    
    // Tablo varlığını kontrol et
    if ($db->tableExists('edefter_usage')) {
        TestLogger::info('edefter_usage tablosu zaten mevcut');
    } else {
        // Tablo oluştur
        $createTableSQL = "
        CREATE TABLE `edefter_usage` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_identifier` varchar(255) NOT NULL COMMENT 'Üye ID veya Session ID',
            `user_type` enum('member','visitor') NOT NULL DEFAULT 'visitor' COMMENT 'Kullanıcı tipi',
            `usage_date` date NOT NULL COMMENT 'Kullanım tarihi',
            `usage_count` int(11) NOT NULL DEFAULT '0' COMMENT 'Günlük kullanım sayısı',
            `last_usage_time` datetime NOT NULL COMMENT 'Son kullanım zamanı',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_user_date` (`user_identifier`,`usage_date`),
            KEY `idx_user_type` (`user_type`),
            KEY `idx_usage_date` (`usage_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='E-Defter kullanım sınırlaması tablosu'
        ";
        
        $result = $db->query($createTableSQL);
        TestAssert::assertTrue($result !== false, 'edefter_usage tablosu oluşturulmalı');
        TestLogger::success('edefter_usage tablosu başarıyla oluşturuldu');
    }
    
    // Tablo yapısını kontrol et
    $tableInfo = $db->getTableInfo('edefter_usage');
    TestAssert::assertNotEmpty($tableInfo, 'Tablo bilgileri alınmalı');
    
    $expectedColumns = ['id', 'user_identifier', 'user_type', 'usage_date', 'usage_count', 'last_usage_time'];
    foreach ($expectedColumns as $column) {
        TestAssert::assertTrue(
            $db->columnExists('edefter_usage', $column), 
            "$column sütunu mevcut olmalı"
        );
    }
    
    TestLogger::success('Tüm gerekli sütunlar mevcut');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

TestHelper::endTest();
