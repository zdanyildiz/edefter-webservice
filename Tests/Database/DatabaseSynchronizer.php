<?php

/**
 * DatabaseSynchronizer - Veritabanı senkronizasyon sistemi
 * 
 * Bu sınıf veritabanları arasında yapısal senkronizasyon yapar:
 * - DB2'de olup DB1'de olmayan tabloları DB1'e ekler
 * - DB2'nin banner sistemi yapısını DB1'e aktarır
 * - Güvenli migration oluşturur
 * 
 * @author GitHub Copilot
 * @version 1.0
 * @date 2025-07-05
 */
class DatabaseSynchronizer 
{
    private $host;
    private $username;
    private $password;
    private $db1_name;
    private $db2_name;
    private $db1_connection;
    private $db2_connection;
    private $sync_report = [];
    private $migration_sql = [];
    
    /**
     * Constructor - Veritabanı bağlantı bilgilerini ayarlar
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
        $this->sync_report = [
            'sync_date' => date('Y-m-d H:i:s'),
            'db1_name' => $this->db1_name,
            'db2_name' => $this->db2_name,
            'tables_to_create' => [],
            'columns_to_add' => [],
            'columns_to_modify' => [],
            'migration_queries' => [],
            'backup_queries' => [],
            'statistics' => [
                'tables_created' => 0,
                'columns_added' => 0,
                'columns_modified' => 0,
                'total_queries' => 0
            ]
        ];
    }
    
    /**
     * Veritabanı bağlantılarını kurar
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
            echo "📊 DB1 (Hedef): {$this->db1_name}\n";
            echo "📊 DB2 (Kaynak): {$this->db2_name}\n\n";
            
        } catch (PDOException $e) {
            throw new Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    /**
     * DB2'de olup DB1'de olmayan tabloları tespit eder
     */
    public function findMissingTables()
    {
        echo "🔍 Eksik tablolar tespit ediliyor...\n";
        
        $db1_tables = $this->getTables($this->db1_connection);
        $db2_tables = $this->getTables($this->db2_connection);
        
        $missing_tables = array_diff($db2_tables, $db1_tables);
        
        foreach ($missing_tables as $table) {
            $this->sync_report['tables_to_create'][] = $table;
            echo "  ➕ Eklenecek tablo: {$table}\n";
        }
        
        echo "📋 Toplam {" . count($missing_tables) . "} tablo eklenecek\n\n";
        
        return $missing_tables;
    }
    
    /**
     * Banner sistemindeki sütun farklılıklarını tespit eder
     */
    public function findBannerColumnDifferences()
    {
        echo "🎨 Banner sistemi sütun farklılıkları tespit ediliyor...\n";
        
        $banner_tables = ['banner_groups', 'banner_layouts', 'banner_styles'];
        
        foreach ($banner_tables as $table) {
            if ($this->tableExists($this->db1_connection, $table) && 
                $this->tableExists($this->db2_connection, $table)) {
                
                echo "  📊 {$table} tablosu kontrol ediliyor...\n";
                
                $db1_columns = $this->getTableColumns($this->db1_connection, $table);
                $db2_columns = $this->getTableColumns($this->db2_connection, $table);
                
                // DB1'de olmayan sütunlar
                $missing_columns = array_diff_key($db2_columns, $db1_columns);
                foreach ($missing_columns as $column => $info) {
                    $this->sync_report['columns_to_add'][] = [
                        'table' => $table,
                        'column' => $column,
                        'definition' => $info
                    ];
                    echo "    ➕ Eklenecek sütun: {$column} ({$info['type']})\n";
                }
                
                // Farklı olan sütunlar
                $common_columns = array_intersect_key($db1_columns, $db2_columns);
                foreach ($common_columns as $column => $db1_info) {
                    $db2_info = $db2_columns[$column];
                    
                    if ($db1_info['type'] !== $db2_info['type'] || 
                        $db1_info['null'] !== $db2_info['null'] ||
                        $db1_info['default'] !== $db2_info['default']) {
                        
                        $this->sync_report['columns_to_modify'][] = [
                            'table' => $table,
                            'column' => $column,
                            'old_definition' => $db1_info,
                            'new_definition' => $db2_info
                        ];
                        echo "    🔄 Güncellenecek sütun: {$column} ({$db1_info['type']} → {$db2_info['type']})\n";
                    }
                }
            }
        }
        
        echo "\n";
    }
    
    /**
     * Migration SQL komutlarını oluşturur
     */
    public function generateMigrationSQL()
    {
        echo "📝 Migration SQL komutları oluşturuluyor...\n";
        
        // 1. Eksik tabloları oluştur
        foreach ($this->sync_report['tables_to_create'] as $table) {
            $createSQL = $this->getCreateTableSQL($this->db2_connection, $table);
            if ($createSQL) {
                $this->migration_sql[] = $createSQL;
                $this->sync_report['migration_queries'][] = $createSQL;
                echo "  📋 {$table} tablosu için CREATE SQL oluşturuldu\n";
            }
        }
        
        // 2. Eksik sütunları ekle
        foreach ($this->sync_report['columns_to_add'] as $column_info) {
            $addSQL = $this->generateAddColumnSQL($column_info);
            $this->migration_sql[] = $addSQL;
            $this->sync_report['migration_queries'][] = $addSQL;
            echo "  ➕ {$column_info['table']}.{$column_info['column']} için ADD COLUMN SQL oluşturuldu\n";
        }
        
        // 3. Sütunları güncelle
        foreach ($this->sync_report['columns_to_modify'] as $column_info) {
            $modifySQL = $this->generateModifyColumnSQL($column_info);
            $this->migration_sql[] = $modifySQL;
            $this->sync_report['migration_queries'][] = $modifySQL;
            echo "  🔄 {$column_info['table']}.{$column_info['column']} için MODIFY COLUMN SQL oluşturuldu\n";
        }
        
        // İstatistikleri güncelle
        $this->sync_report['statistics']['tables_created'] = count($this->sync_report['tables_to_create']);
        $this->sync_report['statistics']['columns_added'] = count($this->sync_report['columns_to_add']);
        $this->sync_report['statistics']['columns_modified'] = count($this->sync_report['columns_to_modify']);
        $this->sync_report['statistics']['total_queries'] = count($this->migration_sql);
        
        echo "✅ Migration SQL tamamlandı: {" . count($this->migration_sql) . "} komut\n\n";
    }
    
    /**
     * Backup SQL komutlarını oluşturur
     */
    public function generateBackupSQL()
    {
        echo "💾 Backup SQL komutları oluşturuluyor...\n";
        
        // Banner tablolarının backup'ını al
        $banner_tables = ['banner_groups', 'banner_layouts', 'banner_styles'];
        
        foreach ($banner_tables as $table) {
            if ($this->tableExists($this->db1_connection, $table)) {
                $backupSQL = "CREATE TABLE IF NOT EXISTS `{$table}_backup_" . date('Ymd_His') . "` AS SELECT * FROM `{$table}`;";
                $this->sync_report['backup_queries'][] = $backupSQL;
                echo "  💾 {$table} backup SQL oluşturuldu\n";
            }
        }
        
        echo "✅ Backup SQL tamamlandı\n\n";
    }
    
    /**
     * Migration'ı uygular (DRY RUN veya GERÇEK)
     */
    public function executeMigration($dry_run = true)
    {
        if ($dry_run) {
            echo "🧪 DRY RUN: Migration komutları simüle ediliyor...\n\n";
            
            foreach ($this->migration_sql as $index => $sql) {
                echo "-- Komut " . ($index + 1) . ":\n";
                echo $sql . "\n\n";
            }
            
            echo "⚠️  DRY RUN tamamlandı. Gerçek uygulamak için dry_run=false yapın.\n";
            return true;
        }
        
        echo "🚀 GERÇEK MİGRATİON başlatılıyor...\n";
        echo "⚠️  Bu işlem geri alınamaz!\n\n";
        
        $this->db1_connection->beginTransaction();
        
        try {
            // Önce backup'ları oluştur
            foreach ($this->sync_report['backup_queries'] as $backupSQL) {
                $this->db1_connection->exec($backupSQL);
                echo "✅ Backup oluşturuldu\n";
            }
            
            // Migration komutlarını çalıştır
            foreach ($this->migration_sql as $index => $sql) {
                echo "🔧 Komut " . ($index + 1) . " çalıştırılıyor...\n";
                $this->db1_connection->exec($sql);
                echo "✅ Başarılı\n";
            }
            
            $this->db1_connection->commit();
            echo "\n🎉 Migration başarıyla tamamlandı!\n";
            
            return true;
            
        } catch (Exception $e) {
            $this->db1_connection->rollBack();
            echo "❌ Migration hatası: " . $e->getMessage() . "\n";
            echo "🔄 Tüm değişiklikler geri alındı\n";
            
            throw $e;
        }
    }
    
    /**
     * Tablo varlığını kontrol eder
     */
    private function tableExists($connection, $tableName)
    {
        $stmt = $connection->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tableName]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Veritabanındaki tüm tabloları getirir
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
     * Tablo CREATE SQL'ini getirir
     */
    private function getCreateTableSQL($connection, $tableName)
    {
        try {
            $stmt = $connection->prepare("SHOW CREATE TABLE `{$tableName}`");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result && isset($result['Create Table'])) {
                return $result['Create Table'] . ";";
            }
            
            return null;
            
        } catch (Exception $e) {
            echo "⚠️  {$tableName} CREATE SQL alınamadı: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    /**
     * ADD COLUMN SQL'i oluşturur
     */
    private function generateAddColumnSQL($column_info)
    {
        $table = $column_info['table'];
        $column = $column_info['column'];
        $definition = $column_info['definition'];
        
        $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition['type']}";
        
        if ($definition['null'] === 'NO') {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' NULL';
        }
        
        if ($definition['default'] !== null) {
            if (in_array(strtolower($definition['default']), ['current_timestamp', 'now()'])) {
                $sql .= " DEFAULT {$definition['default']}";
            } else {
                $sql .= " DEFAULT '{$definition['default']}'";
            }
        }
        
        if (!empty($definition['extra'])) {
            $sql .= " {$definition['extra']}";
        }
        
        $sql .= ";";
        
        return $sql;
    }
    
    /**
     * MODIFY COLUMN SQL'i oluşturur
     */
    private function generateModifyColumnSQL($column_info)
    {
        $table = $column_info['table'];
        $column = $column_info['column'];
        $definition = $column_info['new_definition'];
        
        $sql = "ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` {$definition['type']}";
        
        if ($definition['null'] === 'NO') {
            $sql .= ' NOT NULL';
        } else {
            $sql .= ' NULL';
        }
        
        if ($definition['default'] !== null) {
            if (in_array(strtolower($definition['default']), ['current_timestamp', 'now()'])) {
                $sql .= " DEFAULT {$definition['default']}";
            } else {
                $sql .= " DEFAULT '{$definition['default']}'";
            }
        }
        
        if (!empty($definition['extra'])) {
            $sql .= " {$definition['extra']}";
        }
        
        $sql .= ";";
        
        return $sql;
    }
    
    /**
     * Senkronizasyon raporunu yazdırır
     */
    public function printSyncReport()
    {
        echo "📊 VERİTABANI SENKRONİZASYON RAPORU\n";
        echo "==================================\n\n";
        
        echo "📅 Tarih: " . $this->sync_report['sync_date'] . "\n";
        echo "🎯 Hedef DB (DB1): " . $this->sync_report['db1_name'] . "\n";
        echo "📋 Kaynak DB (DB2): " . $this->sync_report['db2_name'] . "\n\n";
        
        // İstatistikler
        echo "📈 İSTATİSTİKLER\n";
        echo "---------------\n";
        echo "Oluşturulacak Tablo: " . $this->sync_report['statistics']['tables_created'] . "\n";
        echo "Eklenecek Sütun: " . $this->sync_report['statistics']['columns_added'] . "\n";
        echo "Güncellenecek Sütun: " . $this->sync_report['statistics']['columns_modified'] . "\n";
        echo "Toplam SQL Komutu: " . $this->sync_report['statistics']['total_queries'] . "\n\n";
        
        // Eklenecek tablolar
        if (!empty($this->sync_report['tables_to_create'])) {
            echo "➕ EKLENECEK TABLOLAR\n";
            echo "-------------------\n";
            foreach ($this->sync_report['tables_to_create'] as $table) {
                echo "• " . $table . "\n";
            }
            echo "\n";
        }
        
        // Eklenecek sütunlar
        if (!empty($this->sync_report['columns_to_add'])) {
            echo "➕ EKLENECEK SÜTUNLAR\n";
            echo "-------------------\n";
            foreach ($this->sync_report['columns_to_add'] as $column) {
                echo "• {$column['table']}.{$column['column']} ({$column['definition']['type']})\n";
            }
            echo "\n";
        }
        
        // Güncellenecek sütunlar
        if (!empty($this->sync_report['columns_to_modify'])) {
            echo "🔄 GÜNCELLENECEK SÜTUNLAR\n";
            echo "------------------------\n";
            foreach ($this->sync_report['columns_to_modify'] as $column) {
                echo "• {$column['table']}.{$column['column']}: ";
                echo "{$column['old_definition']['type']} → {$column['new_definition']['type']}\n";
            }
            echo "\n";
        }
        
        echo "✅ Rapor tamamlandı.\n";
    }
    
    /**
     * JSON formatında rapor döndürür
     */
    public function getJsonReport()
    {
        return json_encode($this->sync_report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Migration dosyasını kaydet
     */
    public function saveMigrationFile($filePath = null)
    {
        if ($filePath === null) {
            $timestamp = date('Y-m-d_H-i-s');
            $filePath = __DIR__ . "/../Logs/migrations/migration_{$timestamp}.sql";
        }
        
        // Migration dosyası içeriği
        $content = "-- Veritabanı Senkronizasyon Migration\n";
        $content .= "-- Tarih: " . $this->sync_report['sync_date'] . "\n";
        $content .= "-- Hedef DB: " . $this->sync_report['db1_name'] . "\n";
        $content .= "-- Kaynak DB: " . $this->sync_report['db2_name'] . "\n";
        $content .= "-- Oluşturan: DatabaseSynchronizer\n\n";
        
        $content .= "-- BACKUP KOMUTLARI\n";
        $content .= "-- ================\n\n";
        foreach ($this->sync_report['backup_queries'] as $backupSQL) {
            $content .= $backupSQL . "\n\n";
        }
        
        $content .= "-- MIGRATION KOMUTLARI\n";
        $content .= "-- ===================\n\n";
        foreach ($this->migration_sql as $sql) {
            $content .= $sql . "\n\n";
        }
        
        // Klasörü oluştur
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (file_put_contents($filePath, $content) === false) {
            throw new Exception("Migration dosyası kaydedilemedi: {$filePath}");
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
