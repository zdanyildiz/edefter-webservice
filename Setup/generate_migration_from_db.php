<?php
// generate_migration_from_db.php

/**
 * Bu betik, mevcut bir veritabanına bağlanır, şemasını okur ve bu şemayı yeniden oluşturacak
 * eksiksiz bir Phinx migration dosyası oluşturur. Bu, özellikle mevcut bir veritabanından
 * Phinx sistemine geçiş yaparken veya migration geçmişi kaybolduğunda kullanışlıdır.
 */

// --- Veritabanı ve Çıktı Yapılandırması ---
$dbConfig = [
    'host' => 'localhost',
    'name' => 'pozitifeticaret.com',
    'user' => 'root',
    'pass' => 'Global2019*',
    'port' => 3306,
    'charset' => 'utf8mb4',
];

$projectRoot = dirname(__DIR__);
$migrationsPath = $projectRoot . '/App/Database/migrations';
$ignoreTables = ['phinxlog']; // Analiz dışında tutulacak tablolar
// --- Bitiş ---


// Helper Fonksiyonlar
function toCamelCase($str, $capitalise_first_char = false)
{
    $str = str_replace('_', ' ', $str);
    $str = ucwords($str);
    $str = str_replace(' ', '', $str);
    if (!$capitalise_first_char) {
        $str = lcfirst($str);
    }
    return $str;
}

function mapTypeToPhinx($mysqlType, &$options)
{
    $typeMap = [
        'int' => 'integer',
        'tinyint' => 'integer',
        'smallint' => 'integer',
        'mediumint' => 'integer',
        'bigint' => 'integer', // Phinx eski versiyonlarda bigint desteklemiyor
        'varchar' => 'string',
        'char' => 'string',
        'text' => 'text',
        'mediumtext' => 'text',
        'longtext' => 'text',
        'decimal' => 'decimal',
        'float' => 'float',
        'double' => 'float',
        'date' => 'date',
        'datetime' => 'datetime',
        'timestamp' => 'timestamp',
        'time' => 'time',
        'enum' => 'enum',
        'set' => 'string', // Phinx does not have a direct 'set' type, map to string
        'boolean' => 'boolean',
        'bit' => 'boolean',
        'json' => 'json',
    ];

    $baseType = strtolower(explode('(', $mysqlType)[0]);

    if ($baseType === 'tinyint' && preg_match('/tinyint\(1\)/', $mysqlType)) {
        return 'boolean';
    }

    // Extract limit for string/integer types
    if (preg_match('/(\w+)\((\d+)\)/', $mysqlType, $matches)) {
        $limit = (int)$matches[2];
        
        // Bigint için özel işlem - büyük limit ekle
        if ($baseType === 'bigint') {
            $options['limit'] = 20; // Bigint için standart limit
        } else {
            $options['limit'] = $limit;
        }
    } elseif (preg_match('/(\w+)\((\d+,\d+)\)/i', $mysqlType, $matches)) { // For decimal/float
        list($precision, $scale) = explode(',', $matches[2]);
        $options['precision'] = (int)$precision;
        $options['scale'] = (int)$scale;
    }

    // Handle ENUM and SET values
    if ($baseType === 'enum' || $baseType === 'set') {
        if (preg_match("/(?:enum|set)\((.+?)\)/i", $mysqlType, $matches)) {
            // Daha güvenli enum/set parsing
            $valueString = $matches[1];
            preg_match_all("/'([^']+)'/", $valueString, $valueMatches);
            if (!empty($valueMatches[1])) {
                $options['values'] = $valueMatches[1];
            } else {
                // Parsing başarısız olursa string'e çevir
                echo "UYARI: ENUM/SET değerleri parse edilemedi: {$mysqlType}\n";
                return 'string';
            }
        }
    }

    if (strpos($mysqlType, 'unsigned') !== false) {
        $options['signed'] = false;
    }

    return $typeMap[$baseType] ?? 'string';
}

// Ana İşlem
echo "Migration oluşturma işlemi başlatıldı...\n";

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset={$dbConfig['charset']}",
        $dbConfig['user'],
        $dbConfig['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // MySQL strict mode'u geçici olarak kapat
    $pdo->exec("SET sql_mode = ''");
    echo "MySQL strict mode devre dışı bırakıldı.\n";
    
} catch (PDOException $e) {
    die("HATA: Veritabanına bağlanılamadı: " . $e->getMessage() . "\n");
}

echo "Veritabanı bağlantısı başarılı.\n";

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$schema = [];

// 1. Tablo Şemalarını ve Sütunları Topla
foreach ($tables as $tableName) {
    if (in_array($tableName, $ignoreTables)) continue;
    echo "- İşleniyor: $tableName\n";

    $schema[$tableName] = ['columns' => [], 'indexes' => [], 'foreign_keys' => [], 'options' => []];

    // Sütunlar
    $columnsResult = $pdo->query("SHOW FULL COLUMNS FROM `{$tableName}`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columnsResult as $col) {
        $colName = $col['Field'];
        $schema[$tableName]['columns'][$colName] = [
            'type' => $col['Type'],
            'null' => $col['Null'] === 'YES',
            'default' => $col['Default'],
            'extra' => $col['Extra'],
            'collation' => $col['Collation'],
            'comment' => $col['Comment'],
        ];
    }

    // İndeksler
    $indexesResult = $pdo->query("SHOW INDEX FROM `{$tableName}`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($indexesResult as $index) {
        $indexName = $index['Key_name'];
        if (!isset($schema[$tableName]['indexes'][$indexName])) {
            $schema[$tableName]['indexes'][$indexName] = [
                'type' => ($indexName === 'PRIMARY') ? 'primary' : ($index['Non_unique'] == 0 ? 'unique' : 'index'),
                'columns' => [],
            ];
        }
        $schema[$tableName]['indexes'][$indexName]['columns'][] = $index['Column_name'];
    }

    // Foreign Keys
    $fkResult = $pdo->query(
        "SELECT * FROM information_schema.KEY_COLUMN_USAGE kcu " .
        "JOIN information_schema.REFERENTIAL_CONSTRAINTS rc ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME AND kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA " .
        "WHERE kcu.TABLE_SCHEMA = '{$dbConfig['name']}' AND kcu.TABLE_NAME = '{$tableName}' AND kcu.REFERENCED_TABLE_NAME IS NOT NULL"
    )->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fkResult as $fk) {
        $schema[$tableName]['foreign_keys'][] = [
            'constraint' => $fk['CONSTRAINT_NAME'],
            'column' => $fk['COLUMN_NAME'],
            'ref_table' => $fk['REFERENCED_TABLE_NAME'],
            'ref_column' => $fk['REFERENCED_COLUMN_NAME'],
            'on_update' => $fk['UPDATE_RULE'],
            'on_delete' => $fk['DELETE_RULE'],
        ];
    }
}

// 2. Phinx Migration Kodunu Oluştur
$baseTimestamp = time(); // Unix timestamp kullan
$baseClassName = 'GeneratedSchemaFromDb';
$tablesPerFile = 40;

// Migrations klasörünü oluştur
if (!is_dir($migrationsPath)) {
    if (!mkdir($migrationsPath, 0755, true)) {
        die("HATA: Migrations klasörü oluşturulamadı: {$migrationsPath}\n");
    }
    echo "Migrations klasörü oluşturuldu: {$migrationsPath}\n";
} else {
    // Mevcut migration dosyalarını temizle
    $existingFiles = glob($migrationsPath . '/*.php');
    foreach ($existingFiles as $file) {
        unlink($file);
    }
    echo "Mevcut migration dosyaları temizlendi.\n";
}

// Tabloları gruplara böl
$tableGroups = array_chunk(array_keys($schema), $tablesPerFile, true);
$fileCount = 1;

foreach ($tableGroups as $groupIndex => $tableNames) {
    $className = $baseClassName . sprintf('%02d', $fileCount);
    // Her dosya için gerçek zamanlı timestamp oluştur
    $timestamp = date('YmdHis');
    $fileNamePart = strtolower(preg_replace('/([A-Z])/', '_$1', $className));
    $fileNamePart = ltrim($fileNamePart, '_');
    $fileName = $timestamp . '_' . $fileNamePart . '.php';
    $filePath = $migrationsPath . '/' . $fileName;

    $phpCode = "<?php\n\ndeclare(strict_types=1);\n\nuse Phinx\\Migration\\AbstractMigration;\n\nfinal class {$className} extends AbstractMigration\n{\n    public function change(): void\n    {\n";
    
    // MySQL strict mode'u kapat
    $phpCode .= "        // MySQL strict mode'u kapat\n";
    $phpCode .= "        \$this->execute(\"SET sql_mode = ''\");\n\n";

    // Bu gruptaki tabloları işle
    foreach ($tableNames as $tableName) {
        $details = $schema[$tableName];
        
        // Primary key ve identity column'u belirle
        $primaryKey = [];
        $identityColumn = null;
        
        foreach ($details['indexes'] as $indexName => $indexDetails) {
            if ($indexName === 'PRIMARY') {
                $primaryKey = $indexDetails['columns'];
                break;
            }
        }
        
        // Identity column'u bul (auto_increment olan column)
        foreach ($details['columns'] as $colName => $colDetails) {
            if (strpos($colDetails['extra'], 'auto_increment') !== false) {
                $identityColumn = $colName;
                break;
            }
        }

        // Tablo seçeneklerini belirle
        $tableOptionsArray = [];
        
        // Eğer hiç primary key yoksa ve identity column varsa
        if ($identityColumn && empty($primaryKey)) {
            $tableOptionsArray['id'] = false;
            $tableOptionsArray['primary_key'] = [$identityColumn];
        }
        // Eğer primary key var ama identity column farklıysa
        elseif (!empty($primaryKey) && $identityColumn && !in_array($identityColumn, $primaryKey)) {
            $tableOptionsArray['id'] = false;
            $tableOptionsArray['primary_key'] = $primaryKey;
        }
        // Eğer primary key var ve identity column aynıysa
        elseif (!empty($primaryKey) && $identityColumn && in_array($identityColumn, $primaryKey)) {
            $tableOptionsArray['id'] = false;
            $tableOptionsArray['primary_key'] = $primaryKey;
        }
        // Eğer primary key var ama identity column yoksa
        elseif (!empty($primaryKey) && !$identityColumn) {
            $tableOptionsArray['id'] = false;
            $tableOptionsArray['primary_key'] = $primaryKey;
        }
        // Hiçbir özel durum yoksa Phinx default'unu kullan
        // (otomatik id column oluşturur)

        $tableOptionsString = '';
        if (!empty($tableOptionsArray)) {
            $tempOptions = [];
            foreach ($tableOptionsArray as $key => $value) {
                if (is_bool($value)) {
                    $tempOptions[] = "'$key' => " . ($value ? 'true' : 'false');
                } elseif (is_array($value)) {
                    $tempOptions[] = "'$key' => [" . implode(", ", array_map(function($item) {
                        return is_string($item) ? "'" . $item . "'" : var_export($item, true);
                    }, $value)) . "]";
                } else {
                    $tempOptions[] = "'$key' => " . var_export($value, true);
                }
            }
            $tableOptionsString = ", [" . implode(", ", $tempOptions) . "]";
        }

        $phpCode .= "        \$this->table('{$tableName}'" . $tableOptionsString . ")\n";

        // Sütunları ekle
        foreach ($details['columns'] as $colName => $colDetails) {
            
            if ($colName === $identityColumn) {
                $options = ['identity' => true];
                $phinxType = mapTypeToPhinx($colDetails['type'], $options);
                
                // Identity column için özel kontroller
                if ($phinxType !== 'integer' && $phinxType !== 'bigint') {
                    echo "UYARI: {$tableName}.{$colName} - Identity column integer/bigint olmalı, {$phinxType} bulundu!\n";
                    $phinxType = 'integer';
                }
                
                $optionsStr = implode(", ", array_map(function ($v, $k) { 
                    return "'$k' => " . (is_bool($v) ? ($v ? 'true' : 'false') : var_export($v, true)); 
                }, $options, array_keys($options)));
                $phpCode .= "            ->addColumn('{$colName}', '{$phinxType}', [{$optionsStr}])\n";
                continue;
            }

            $options = [];
            $phinxType = mapTypeToPhinx($colDetails['type'], $options);

            // Potansiyel hataları önceden tespit et ve düzelt
            
            // 1. Bigint kontrolü - integer'a çevir ama limit ekle
            if ($phinxType === 'bigint' || ($phinxType === 'integer' && stripos($colDetails['type'], 'bigint') !== false)) {
                echo "BİLGİ: {$tableName}.{$colName} - BIGINT integer'a çevriliyor (limit: 20)!\n";
                $phinxType = 'integer';
                $options['limit'] = 20;
            }
            
            // 2. Enum değerleri kontrolü
            if ($phinxType === 'enum' && (!isset($options['values']) || empty($options['values']))) {
                echo "UYARI: {$tableName}.{$colName} - Enum values bulunamadı, string'e çevriliyor!\n";
                $phinxType = 'string';
                unset($options['values']);
                if (!isset($options['limit'])) {
                    $options['limit'] = 255;
                }
            }
            
            // 3. Decimal precision/scale kontrolü
            if ($phinxType === 'decimal') {
                if (!isset($options['precision']) || !isset($options['scale'])) {
                    echo "UYARI: {$tableName}.{$colName} - Decimal precision/scale bulunamadı, varsayılan atanıyor!\n";
                    $options['precision'] = 10;
                    $options['scale'] = 2;
                }
            }
            
            // 4. String limit kontrolü
            if ($phinxType === 'string' && !isset($options['limit'])) {
                echo "UYARI: {$tableName}.{$colName} - String limit bulunamadı, 255 atanıyor!\n";
                $options['limit'] = 255;
            }

            // 5. Integer limit kontrolü - çok büyük limitler için uyarı
            if ($phinxType === 'integer' && isset($options['limit']) && $options['limit'] > 11) {
                echo "BİLGİ: {$tableName}.{$colName} - Büyük integer limit: {$options['limit']}\n";
            }

            // NULL durumu kontrolü
            if ($colDetails['null']) {
                $options['null'] = true;
            }
            
            // Default değer işlemi - Güvenli yaklaşım
            if ($colDetails['default'] !== null) {
                // CURRENT_TIMESTAMP kontrolü
                if ($colDetails['default'] === 'CURRENT_TIMESTAMP' || $colDetails['default'] === 'current_timestamp()') {
                    if ($phinxType === 'timestamp') {
                        $options['default'] = 'CURRENT_TIMESTAMP';
                    } else {
                        // Timestamp olmayan field'lar için NULL yap
                        $options['null'] = true;
                    }
                } elseif ($phinxType === 'boolean') {
                    $options['default'] = (bool)$colDetails['default'];
                } else {
                    $options['default'] = $colDetails['default'];
                }
            } else {
                // Default değer yoksa ve NOT NULL ise - GÜVENLİ YAKLAŞIM
                if (!$colDetails['null'] && strpos($colDetails['extra'], 'auto_increment') === false) {
                    // Hepsini NULL yapılabilir yap, migration sonrası manuel düzeltilir
                    $options['null'] = true;
                    echo "BİLGİ: {$tableName}.{$colName} - NOT NULL ama default yok, NULL yapıldı!\n";
                }
            }
            
            if (!empty($colDetails['comment'])) $options['comment'] = $colDetails['comment'];

            $optionsStr = '';
            if (!empty($options)) {
                $optionsStr = implode(", ", array_map(function ($v, $k) {
                    if (is_bool($v)) return "'$k' => " . ($v ? 'true' : 'false');
                    if ($k === 'values' && is_array($v)) {
                        return "'$k' => ['" . implode("', '", $v) . "']";
                    }
                    if ($k === 'default' && $v === 'CURRENT_TIMESTAMP') {
                        return "'$k' => 'CURRENT_TIMESTAMP'";
                    }
                    return "'$k' => " . var_export($v, true);
                }, $options, array_keys($options)));
            }

            $phpCode .= "            ->addColumn('{$colName}', '{$phinxType}'" . (!empty($optionsStr) ? ", [{$optionsStr}]" : '') . ")\n";
        }

        // İndeksleri ekle
        foreach ($details['indexes'] as $indexName => $indexDetails) {
            if ($indexName === 'PRIMARY') continue;
            $options = [];
            if ($indexDetails['type'] === 'unique') $options['unique'] = true;
            $options['name'] = $indexName;
            $optionsStr = implode(", ", array_map(function ($v, $k) { 
                return "'$k' => " . var_export($v, true); 
            }, $options, array_keys($options)));
            
            $columnsArray = implode(", ", array_map(function($col){ 
                return "'" . $col . "'"; 
            }, $indexDetails['columns']));
            
            $phpCode .= "            ->addIndex([{$columnsArray}]" . (!empty($optionsStr) ? ", [{$optionsStr}]" : "") . ")\n";
        }
        
        $phpCode .= "            ->create();\n\n";
    }

    $phpCode .= "    }\n}\n";

    // Dosyayı yaz
    echo "Migration dosyası yazılıyor: {$filePath} (Tablolar: " . count($tableNames) . ")\n";
    if (!file_put_contents($filePath, $phpCode)) {
        die("HATA: Migration dosyası yazılamadı: {$filePath}\n");
    }
    
    $fileCount++;
    
    // Timestamp çakışmasını önlemek için 1 saniye bekle
    if ($fileCount <= count($tableGroups)) {
        echo "Sonraki dosya için bekleniyor...\n";
        sleep(1);
    }
}

// Foreign Key dosyası oluşturmadan önce de bekle
echo "Foreign Key dosyası için bekleniyor...\n";
sleep(1);

// Foreign Key'leri ayrı bir migration dosyasında oluştur
$fkClassName = $baseClassName . 'ForeignKeys';
// FK için gerçek zamanlı timestamp
$fkTimestamp = date('YmdHis');
$fkFileNamePart = strtolower(preg_replace('/([A-Z])/', '_$1', $fkClassName));
$fkFileNamePart = ltrim($fkFileNamePart, '_');
$fkFileName = $fkTimestamp . '_' . $fkFileNamePart . '.php';
$fkFilePath = $migrationsPath . '/' . $fkFileName;

$fkPhpCode = "<?php\n\ndeclare(strict_types=1);\n\nuse Phinx\\Migration\\AbstractMigration;\n\nfinal class {$fkClassName} extends AbstractMigration\n{\n    public function change(): void\n    {\n";
$fkPhpCode .= "        // Foreign Key Constraints\n";

$hasForeignKeys = false;
foreach ($schema as $tableName => $details) {
    if (empty($details['foreign_keys'])) continue;
    
    $hasForeignKeys = true;
    $fkPhpCode .= "        \$this->table('{$tableName}')\n";
    foreach ($details['foreign_keys'] as $fk) {
        $options = [
            'delete' => strtoupper($fk['on_delete']),
            'update' => strtoupper($fk['on_update']),
            'constraint' => $fk['constraint'],
        ];
        $optionsStr = implode(", ", array_map(function ($v, $k) { 
            return "'$k' => '" . $v . "'"; 
        }, $options, array_keys($options)));
        $fkPhpCode .= "            ->addForeignKey('{$fk['column']}', '{$fk['ref_table']}', '{$fk['ref_column']}', [{$optionsStr}])\n";
    }
    $fkPhpCode .= "            ->save();\n\n";
}

$fkPhpCode .= "    }\n}\n";

// Foreign Key dosyasını yaz (eğer foreign key varsa)
if ($hasForeignKeys) {
    echo "Foreign Key migration dosyası yazılıyor: {$fkFilePath}\n";
    if (!file_put_contents($fkFilePath, $fkPhpCode)) {
        die("HATA: Foreign Key migration dosyası yazılamadı.\n");
    }
}

// 3. Sonuç
echo "Başarılı! " . ($fileCount - 1) . " migration dosyası oluşturuldu.\n";
if ($hasForeignKeys) {
    echo "1 Foreign Key migration dosyası oluşturuldu.\n";
}
echo "Lütfen dosyaları kontrol edin ve ardından mevcut migration geçmişinizi temizleyip (gerekirse) \`vendor/bin/phinx migrate\` komutunu çalıştırın.\n";

?>