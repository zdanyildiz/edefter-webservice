<?php
/**
 * UpdateDatabaseSchema.php - database.sql dosyasını güncelleyen betik
 *
 * Bu betik, yerel veritabanı bilgilerini kullanarak belirli tabloların
 * CREATE TABLE ifadelerini çeker ve database.sql dosyasına ekler.
 *
 * Kullanım:
 * php Tests/System/UpdateDatabaseSchema.php
 *
 * @author GitHub Copilot
 * @date 2025-07-12
 */

// Test framework'ünü yükle (gerekli sınıflar için)
include_once __DIR__ . '/../index.php';

TestHelper::startTest('database.sql Güncelleme');

try {
    $db = TestDatabase::getInstance();
    $outputFile = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'database.sql';

    $tablesToUpdate = [
        'client_api_credentials',
        'analytics_daily_summary',
        'platform_tracking'
    ];

    $sqlContent = "";

    foreach ($tablesToUpdate as $tableName) {
        TestHelper::info("'{$tableName}' tablosunun CREATE TABLE ifadesi çekiliyor...");
        $createTableSql = $db->getCreateTableStatement($tableName);

        if ($createTableSql) {
            $sqlContent .= "\n\n--\n-- Table structure for table `{$tableName}`\n--\n\n" . $createTableSql . ";\n";
            TestHelper::success("'{$tableName}' tablosunun CREATE TABLE ifadesi başarıyla çekildi.");
        } else {
            TestHelper::warning("'{$tableName}' tablosunun CREATE TABLE ifadesi bulunamadı veya çekilemedi.");
        }
    }

    if (!empty($sqlContent)) {
        // Mevcut database.sql içeriğini oku
        $currentContent = file_exists($outputFile) ? file_get_contents($outputFile) : '';

        // Sadece yeni tabloların CREATE TABLE ifadelerini ekle
        // Mevcut tabloların CREATE TABLE ifadelerini güncellemek için daha karmaşık bir parsing gerekir.
        // Şimdilik sadece ekleme yapıyoruz.
        file_put_contents($outputFile, $currentContent . $sqlContent);
        TestHelper::success("Güncel tablo şemaları başarıyla \"{$outputFile}\" dosyasına eklendi.");
    } else {
        TestHelper::warning("Güncellenecek tablo şeması bulunamadı.");
    }

} catch (Exception $e) {
    TestHelper::error("Veritabanı şeması aktarılırken hata oluştu: " . $e->getMessage());
} finally {
    TestHelper::endTest();
}