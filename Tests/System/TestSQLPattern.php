<?php
/**
 * TestSQLPattern.php - SQL CREATE TABLE ve INSERT pattern testleri
 */

// Test SQL örnekleri
$testCreateSQLs = [
    "CREATE TABLE `ayaranaliz` (",
    "CREATE TABLE IF NOT EXISTS ad_conversion_code (",
    "CREATE TABLE user_table (",
    "CREATE TABLE `quoted_table` (",
    "  CREATE TABLE   spaced_table (",
    "create table lowercase_table (",
    "CREATE table MixedCase_table (",
];

$testInsertSQLs = [
    "INSERT INTO `ayaranaliz` VALUES (1, 'test');",
    "INSERT IGNORE INTO `users` VALUES (1, 'test');",
    "INSERT INTO users (id, name) VALUES (1, 'test');",
    "  INSERT   INTO   spaced_table VALUES (1);",
    "insert into lowercase_table VALUES (1);",
    "INSERT INTO `quoted_table` VALUES (1);",
];

echo "=== SQL PATTERN TEST ===\n\n";

echo "CREATE TABLE PATTERN TEST:\n";
echo str_repeat("-", 50) . "\n";

foreach ($testCreateSQLs as $sql) {
    $original = trim($sql);
    
    $converted = preg_replace(
        '/CREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS\s+)/i',
        'CREATE TABLE IF NOT EXISTS ',
        $sql
    );
    
    $converted = trim($converted);
    
    echo "'{$original}' → '{$converted}'\n";
    
    if ($original === $converted) {
        echo "⚠️  DEĞİŞİKLİK YOK\n";
    } else {
        echo "✅ DÖNÜŞTÜRÜLDÜ\n";
    }
    echo "\n";
}

echo "\nINSERT PATTERN TEST:\n";
echo str_repeat("-", 50) . "\n";

foreach ($testInsertSQLs as $sql) {
    $original = trim($sql);
    
    $converted = preg_replace(
        '/INSERT\s+INTO\s+(?!IGNORE\s+)/i',
        'INSERT IGNORE INTO ',
        $sql
    );
    
    $converted = trim($converted);
    
    echo "'{$original}' → '{$converted}'\n";
    
    if ($original === $converted) {
        echo "⚠️  DEĞİŞİKLİK YOK\n";
    } else {
        echo "✅ DÖNÜŞTÜRÜLDÜ\n";
    }
    echo "\n";
}
