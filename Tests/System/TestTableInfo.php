<?php
/**
 * Tablo Kontrol Test Scripti
 * GetTableInfo.php fonksiyonlarının test edilmesi için
 */

require_once 'GetTableInfo.php';

echo "=== TABLO KONTROL SİSTEMİ TEST ===" . PHP_EOL;
echo "Tarih: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// 1. Temel tablo kontrolü
echo "1. TEMEL TABLO KONTROLÜ:" . PHP_EOL;
$commonTables = ['sayfa', 'dil', 'language_page_mapping', 'language_category_mapping'];

foreach ($commonTables as $table) {
    $exists = checkTableExists($table);
    echo "• $table: " . ($exists ? '✅ Mevcut' : '❌ Yok') . PHP_EOL;
}

echo PHP_EOL . "2. DETAYLI TABLO ANALİZİ:" . PHP_EOL;
printTableInfo('sayfa', true);

echo "3. SÜTUN KONTROL TESTİ:" . PHP_EOL;
$columnChecks = [
    ['sayfa', 'sayfaad'],
    ['sayfa', 'sayfabaslik'], // Bu olmayabilir
    ['dil', 'dilad'],
    ['dil', 'dilkisa'],
    ['language_page_mapping', 'translation_status'],
    ['language_page_mapping', 'nonexistent_column'] // Bu kesinlikle yok
];

foreach ($columnChecks as [$table, $column]) {
    $exists = checkColumnExists($table, $column);
    echo "• $table.$column: " . ($exists ? '✅ Mevcut' : '❌ Yok') . PHP_EOL;
}

echo PHP_EOL . "4. SÜTUN LİSTESİ TESTİ:" . PHP_EOL;
$columns = getTableColumns('dil');
echo "DİL tablosu sütunları: " . implode(', ', $columns) . PHP_EOL;

echo PHP_EOL . "5. ÇOKLU TABLO BİLGİSİ:" . PHP_EOL;
$multiTableInfo = getMultipleTablesInfo(['sayfa', 'dil', 'nonexistent_table']);
foreach ($multiTableInfo as $tableName => $info) {
    echo "• $tableName: " . ($info['exists'] ? "✅ ({$info['column_count']} sütun)" : '❌ Yok') . PHP_EOL;
}

echo PHP_EOL . "✅ Test tamamlandı!" . PHP_EOL;
?>
