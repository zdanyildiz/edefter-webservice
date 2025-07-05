<?php

/**
 * DatabaseComparer - İki veritabanının tablo ve sütunlarını karşılaştırır
 * 
 * Bu sınıf iki farklı veritabanının yapılarını karşılaştırır ve farkları raporlar.
 * Tablo eksiklikleri, sütun farklılıkları ve veri tipi değişikliklerini tespit eder.
 * 
 * @author GitHub Copilot
 * @version 1.0
 * @date 2025-07-05
 */
class DatabaseComparer 
{
    private $host;
    private $username;
    private $password;
    private $db1_name;
    private $db2_name;
    private $db1_connection;
    private $db2_connection;
    private $report = [];
    
    /**
     * Constructor - Veritabanı bağlantı bilgilerini ayarlar
     * 
     * @param string $host Veritabanı host adresi
     * @param string $username Kullanıcı adı
     * @param string $password Şifre
     * @param string $db1_name İlk veritabanı adı
     * @param string $db2_name İkinci veritabanı adı
     */
    public function __construct($host, $username, $password, $db1_name, $db2_name)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->db1_name = $db1_name;
        $this->db2_name = $db2_name;
        
        $this->initializeReport();
    }
    
    /**
     * Rapor yapısını başlatır
     */
    private function initializeReport()
    {
        $this->report = [
            'comparison_date' => date('Y-m-d H:i:s'),
            'db1_name' => $this->db1_name,
            'db2_name' => $this->db2_name,
            'tables_only_in_db1' => [],
            'tables_only_in_db2' => [],
            'common_tables' => [],
            'column_differences' => [],
            'statistics' => [
                'db1_table_count' => 0,
                'db2_table_count' => 0,
                'common_table_count' => 0,
                'total_differences' => 0
            ]
        ];
    }
    
    /**
     * Veritabanı bağlantılarını kurar
     * 
     * @throws Exception Bağlantı hatası durumunda
     */
    public function connect()
    {
        try {
            // DB1 bağlantısı
            $this->db1_connection = new PDO(
                "mysql:host={$this->host};dbname={$this->db1_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            
            // DB2 bağlantısı
            $this->db2_connection = new PDO(
                "mysql:host={$this->host};dbname={$this->db2_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            
            echo "✅ Veritabanı bağlantıları başarılı\n";
            echo "📊 DB1: {$this->db1_name}\n";
            echo "📊 DB2: {$this->db2_name}\n\n";
            
        } catch (PDOException $e) {
            throw new Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Veritabanındaki tüm tabloları getirir
     * 
     * @param PDO $connection Veritabanı bağlantısı
     * @return array Tablo listesi
     */
    private function getTables($connection)
    {
        $stmt = $connection->query("SHOW TABLES");
        $tables = [];
        
        while ($row = $stmt->fetch()) {
            $tables[] = array_values($row)[0];
        }
        
        return $tables;
    }
    
    /**
     * Tablonun sütun bilgilerini getirir
     * 
     * @param PDO $connection Veritabanı bağlantısı
     * @param string $tableName Tablo adı
     * @return array Sütun bilgileri
     */
    private function getTableColumns($connection, $tableName)
    {
        $stmt = $connection->prepare("DESCRIBE `{$tableName}`");
        $stmt->execute();
        
        $columns = [];
        while ($row = $stmt->fetch()) {
            $columns[$row['Field']] = [
                'type' => $row['Type'],
                'null' => $row['Null'],
                'key' => $row['Key'],
                'default' => $row['Default'],
                'extra' => $row['Extra']
            ];
        }
        
        return $columns;
    }
    
    /**
     * İki veritabanını karşılaştırır
     */
    public function compare()
    {
        echo "🔍 Veritabanları karşılaştırılıyor...\n\n";
        
        // Tabloları getir
        $db1_tables = $this->getTables($this->db1_connection);
        $db2_tables = $this->getTables($this->db2_connection);
        
        // İstatistikleri güncelle
        $this->report['statistics']['db1_table_count'] = count($db1_tables);
        $this->report['statistics']['db2_table_count'] = count($db2_tables);
        
        // Tablo farklarını tespit et
        $this->report['tables_only_in_db1'] = array_diff($db1_tables, $db2_tables);
        $this->report['tables_only_in_db2'] = array_diff($db2_tables, $db1_tables);
        $this->report['common_tables'] = array_intersect($db1_tables, $db2_tables);
        $this->report['statistics']['common_table_count'] = count($this->report['common_tables']);
        
        echo "📋 Tablo Özeti:\n";
        echo "   DB1 Tablo Sayısı: " . count($db1_tables) . "\n";
        echo "   DB2 Tablo Sayısı: " . count($db2_tables) . "\n";
        echo "   Ortak Tablo Sayısı: " . count($this->report['common_tables']) . "\n";
        echo "   Sadece DB1'de: " . count($this->report['tables_only_in_db1']) . "\n";
        echo "   Sadece DB2'de: " . count($this->report['tables_only_in_db2']) . "\n\n";
        
        // Ortak tabloların sütunlarını karşılaştır
        $this->compareTableColumns();
        
        echo "✅ Karşılaştırma tamamlandı!\n\n";
    }
    
    /**
     * Ortak tabloların sütunlarını karşılaştırır
     */
    private function compareTableColumns()
    {
        echo "🔎 Sütun farklılıkları kontrol ediliyor...\n\n";
        
        $progressCounter = 0;
        foreach ($this->report['common_tables'] as $tableName) {
            $progressCounter++;
            echo "📊 [{$progressCounter}/" . count($this->report['common_tables']) . "] {$tableName}";
            
            $db1_columns = $this->getTableColumns($this->db1_connection, $tableName);
            $db2_columns = $this->getTableColumns($this->db2_connection, $tableName);
            
            $differences = $this->findColumnDifferences($tableName, $db1_columns, $db2_columns);
            
            if (!empty($differences)) {
                $this->report['column_differences'][$tableName] = $differences;
                $this->report['statistics']['total_differences'] += count($differences);
                echo " ⚠️  " . count($differences) . " fark bulundu\n";
            } else {
                echo " ✅ Aynı\n";
            }
        }
        
        echo "\n";
    }
    
    /**
     * İki tablonun sütun farklılıklarını bulur
     * 
     * @param string $tableName Tablo adı
     * @param array $db1_columns DB1 sütunları
     * @param array $db2_columns DB2 sütunları
     * @return array Farklar listesi
     */
    private function findColumnDifferences($tableName, $db1_columns, $db2_columns)
    {
        $differences = [];
        
        // DB1'de olup DB2'de olmayan sütunlar
        $columns_only_in_db1 = array_diff_key($db1_columns, $db2_columns);
        foreach ($columns_only_in_db1 as $columnName => $columnInfo) {
            $differences[] = [
                'type' => 'missing_in_db2',
                'column' => $columnName,
                'db1_info' => $columnInfo,
                'db2_info' => null
            ];
        }
        
        // DB2'de olup DB1'de olmayan sütunlar
        $columns_only_in_db2 = array_diff_key($db2_columns, $db1_columns);
        foreach ($columns_only_in_db2 as $columnName => $columnInfo) {
            $differences[] = [
                'type' => 'missing_in_db1',
                'column' => $columnName,
                'db1_info' => null,
                'db2_info' => $columnInfo
            ];
        }
        
        // Ortak sütunların farklılıkları
        $common_columns = array_intersect_key($db1_columns, $db2_columns);
        foreach ($common_columns as $columnName => $db1_info) {
            $db2_info = $db2_columns[$columnName];
            
            $columnDiffs = [];
            
            // Tip kontrolü
            if ($db1_info['type'] !== $db2_info['type']) {
                $columnDiffs['type'] = [
                    'db1' => $db1_info['type'],
                    'db2' => $db2_info['type']
                ];
            }
            
            // Null kontrolü
            if ($db1_info['null'] !== $db2_info['null']) {
                $columnDiffs['null'] = [
                    'db1' => $db1_info['null'],
                    'db2' => $db2_info['null']
                ];
            }
            
            // Key kontrolü
            if ($db1_info['key'] !== $db2_info['key']) {
                $columnDiffs['key'] = [
                    'db1' => $db1_info['key'],
                    'db2' => $db2_info['key']
                ];
            }
            
            // Default kontrolü
            if ($db1_info['default'] !== $db2_info['default']) {
                $columnDiffs['default'] = [
                    'db1' => $db1_info['default'],
                    'db2' => $db2_info['default']
                ];
            }
            
            // Extra kontrolü
            if ($db1_info['extra'] !== $db2_info['extra']) {
                $columnDiffs['extra'] = [
                    'db1' => $db1_info['extra'],
                    'db2' => $db2_info['extra']
                ];
            }
            
            if (!empty($columnDiffs)) {
                $differences[] = [
                    'type' => 'column_difference',
                    'column' => $columnName,
                    'differences' => $columnDiffs
                ];
            }
        }
        
        return $differences;
    }
    
    /**
     * Konsol raporu yazdırır
     */
    public function printReport()
    {
        echo "📊 VERİTABANI KARŞILAŞTIRMA RAPORU\n";
        echo "================================\n\n";
        
        echo "📅 Tarih: " . $this->report['comparison_date'] . "\n";
        echo "🏢 DB1: " . $this->report['db1_name'] . "\n";
        echo "🏢 DB2: " . $this->report['db2_name'] . "\n\n";
        
        // İstatistikler
        echo "📈 İSTATİSTİKLER\n";
        echo "---------------\n";
        echo "DB1 Tablo Sayısı: " . $this->report['statistics']['db1_table_count'] . "\n";
        echo "DB2 Tablo Sayısı: " . $this->report['statistics']['db2_table_count'] . "\n";
        echo "Ortak Tablo Sayısı: " . $this->report['statistics']['common_table_count'] . "\n";
        echo "Toplam Sütun Farkı: " . $this->report['statistics']['total_differences'] . "\n\n";
        
        // Sadece DB1'de olan tablolar
        if (!empty($this->report['tables_only_in_db1'])) {
            echo "⚠️  SADECE DB1'DE OLAN TABLOLAR\n";
            echo "------------------------------\n";
            foreach ($this->report['tables_only_in_db1'] as $table) {
                echo "• " . $table . "\n";
            }
            echo "\n";
        }
        
        // Sadece DB2'de olan tablolar
        if (!empty($this->report['tables_only_in_db2'])) {
            echo "⚠️  SADECE DB2'DE OLAN TABLOLAR\n";
            echo "------------------------------\n";
            foreach ($this->report['tables_only_in_db2'] as $table) {
                echo "• " . $table . "\n";
            }
            echo "\n";
        }
        
        // Sütun farklılıkları
        if (!empty($this->report['column_differences'])) {
            echo "🔍 SÜTUN FARKLILIKLARI\n";
            echo "----------------------\n";
            
            foreach ($this->report['column_differences'] as $tableName => $differences) {
                echo "\n📋 Tablo: {$tableName}\n";
                echo str_repeat("-", strlen($tableName) + 8) . "\n";
                
                foreach ($differences as $diff) {
                    switch ($diff['type']) {
                        case 'missing_in_db2':
                            echo "  ❌ Sütun DB2'de yok: {$diff['column']} ({$diff['db1_info']['type']})\n";
                            break;
                            
                        case 'missing_in_db1':
                            echo "  ➕ Sütun DB1'de yok: {$diff['column']} ({$diff['db2_info']['type']})\n";
                            break;
                            
                        case 'column_difference':
                            echo "  🔄 Sütun farkı: {$diff['column']}\n";
                            foreach ($diff['differences'] as $property => $values) {
                                echo "     • {$property}: '{$values['db1']}' → '{$values['db2']}'\n";
                            }
                            break;
                    }
                }
            }
        }
        
        echo "\n✅ Rapor tamamlandı.\n";
    }
    
    /**
     * JSON formatında rapor döndürür
     * 
     * @return string JSON rapor
     */
    public function getJsonReport()
    {
        return json_encode($this->report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * HTML formatında rapor oluşturur
     * 
     * @return string HTML rapor
     */
    public function getHtmlReport()
    {
        $html = "<!DOCTYPE html>\n<html lang='tr'>\n<head>\n";
        $html .= "<meta charset='UTF-8'>\n";
        $html .= "<title>Veritabanı Karşılaştırma Raporu</title>\n";
        $html .= "<style>\n";
        $html .= "body { font-family: Arial, sans-serif; margin: 20px; }\n";
        $html .= ".header { background: #f4f4f4; padding: 20px; border-radius: 5px; }\n";
        $html .= ".stats { display: flex; gap: 20px; margin: 20px 0; }\n";
        $html .= ".stat-box { background: #e9f4ff; padding: 15px; border-radius: 5px; text-align: center; }\n";
        $html .= ".table-section { margin: 20px 0; }\n";
        $html .= ".table-list { background: #fff; border: 1px solid #ddd; padding: 15px; }\n";
        $html .= ".difference { background: #fff3cd; border: 1px solid #ffeaa7; margin: 10px 0; padding: 15px; }\n";
        $html .= ".missing-db2 { color: #d63031; }\n";
        $html .= ".missing-db1 { color: #00b894; }\n";
        $html .= ".column-diff { color: #e17055; }\n";
        $html .= "</style>\n</head>\n<body>\n";
        
        $html .= "<div class='header'>\n";
        $html .= "<h1>📊 Veritabanı Karşılaştırma Raporu</h1>\n";
        $html .= "<p><strong>Tarih:</strong> " . $this->report['comparison_date'] . "</p>\n";
        $html .= "<p><strong>DB1:</strong> " . $this->report['db1_name'] . "</p>\n";
        $html .= "<p><strong>DB2:</strong> " . $this->report['db2_name'] . "</p>\n";
        $html .= "</div>\n";
        
        $html .= "<div class='stats'>\n";
        $html .= "<div class='stat-box'><h3>" . $this->report['statistics']['db1_table_count'] . "</h3><p>DB1 Tablo</p></div>\n";
        $html .= "<div class='stat-box'><h3>" . $this->report['statistics']['db2_table_count'] . "</h3><p>DB2 Tablo</p></div>\n";
        $html .= "<div class='stat-box'><h3>" . $this->report['statistics']['common_table_count'] . "</h3><p>Ortak Tablo</p></div>\n";
        $html .= "<div class='stat-box'><h3>" . $this->report['statistics']['total_differences'] . "</h3><p>Sütun Farkı</p></div>\n";
        $html .= "</div>\n";
        
        // Tablo farklılıkları
        if (!empty($this->report['tables_only_in_db1'])) {
            $html .= "<div class='table-section'>\n";
            $html .= "<h2>⚠️ Sadece DB1'de Olan Tablolar</h2>\n";
            $html .= "<div class='table-list'>\n";
            foreach ($this->report['tables_only_in_db1'] as $table) {
                $html .= "<p>• " . htmlspecialchars($table) . "</p>\n";
            }
            $html .= "</div>\n</div>\n";
        }
        
        if (!empty($this->report['tables_only_in_db2'])) {
            $html .= "<div class='table-section'>\n";
            $html .= "<h2>⚠️ Sadece DB2'de Olan Tablolar</h2>\n";
            $html .= "<div class='table-list'>\n";
            foreach ($this->report['tables_only_in_db2'] as $table) {
                $html .= "<p>• " . htmlspecialchars($table) . "</p>\n";
            }
            $html .= "</div>\n</div>\n";
        }
        
        // Sütun farklılıkları
        if (!empty($this->report['column_differences'])) {
            $html .= "<div class='table-section'>\n";
            $html .= "<h2>🔍 Sütun Farklılıkları</h2>\n";
            
            foreach ($this->report['column_differences'] as $tableName => $differences) {
                $html .= "<div class='difference'>\n";
                $html .= "<h3>📋 " . htmlspecialchars($tableName) . "</h3>\n";
                
                foreach ($differences as $diff) {
                    switch ($diff['type']) {
                        case 'missing_in_db2':
                            $html .= "<p class='missing-db2'>❌ Sütun DB2'de yok: <strong>" . htmlspecialchars($diff['column']) . "</strong> (" . htmlspecialchars($diff['db1_info']['type']) . ")</p>\n";
                            break;
                            
                        case 'missing_in_db1':
                            $html .= "<p class='missing-db1'>➕ Sütun DB1'de yok: <strong>" . htmlspecialchars($diff['column']) . "</strong> (" . htmlspecialchars($diff['db2_info']['type']) . ")</p>\n";
                            break;
                            
                        case 'column_difference':
                            $html .= "<p class='column-diff'>🔄 Sütun farkı: <strong>" . htmlspecialchars($diff['column']) . "</strong></p>\n";
                            $html .= "<ul>\n";
                            foreach ($diff['differences'] as $property => $values) {
                                $html .= "<li>" . htmlspecialchars($property) . ": '" . htmlspecialchars($values['db1']) . "' → '" . htmlspecialchars($values['db2']) . "'</li>\n";
                            }
                            $html .= "</ul>\n";
                            break;
                    }
                }
                $html .= "</div>\n";
            }
            $html .= "</div>\n";
        }
        
        $html .= "</body>\n</html>";
        
        return $html;
    }
    
    /**
     * Raporu dosyaya kaydet
     * 
     * @param string $format Format türü (json, html, txt)
     * @param string $filePath Dosya yolu (isteğe bağlı)
     * @return string Kaydedilen dosya yolu
     */
    public function saveReport($format = 'json', $filePath = null)
    {
        if ($filePath === null) {
            $timestamp = date('Y-m-d_H-i-s');
            $filePath = __DIR__ . "/comparison_report_{$timestamp}.{$format}";
        }
        
        switch ($format) {
            case 'json':
                $content = $this->getJsonReport();
                break;
                
            case 'html':
                $content = $this->getHtmlReport();
                break;
                
            case 'txt':
                ob_start();
                $this->printReport();
                $content = ob_get_clean();
                break;
                
            default:
                throw new Exception("Desteklenmeyen format: {$format}");
        }
        
        if (file_put_contents($filePath, $content) === false) {
            throw new Exception("Dosya kaydedilemedi: {$filePath}");
        }
        
        return $filePath;
    }
    
    /**
     * Bağlantıları kapat
     */
    public function __destruct()
    {
        $this->db1_connection = null;
        $this->db2_connection = null;
    }
}
