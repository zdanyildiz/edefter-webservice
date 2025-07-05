<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialSchema extends AbstractMigration
{
    /**
     * Database.sql dosyasını Phinx ile uyumlu hale getirip çalıştırır
     * 
     * CREATE TABLE ifadelerini CREATE TABLE IF NOT EXISTS yapar
     * INSERT ifadelerini INSERT IGNORE yapar
     * SQL'i statement'lara bölerek güvenli şekilde çalıştırır
     */
    public function change(): void
    {
        $sqlFile = __DIR__ . '/../../App/Database/database.sql';

        if (!file_exists($sqlFile)) {
            throw new \RuntimeException('database.sql dosyası bulunamadı: ' . $sqlFile);
        }

        // Büyük dosyayı oku
        $sql = file_get_contents($sqlFile);

        if ($sql === false) {
            throw new \RuntimeException('database.sql dosyası okunamadı.');
        }

        // 1. CREATE TABLE ifadelerini CREATE TABLE IF NOT EXISTS yap
        $sql = preg_replace(
            '/CREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS\s+)/i',
            'CREATE TABLE IF NOT EXISTS ',
            $sql
        );

        // 2. INSERT INTO ifadelerini INSERT IGNORE INTO yap
        $sql = preg_replace(
            '/INSERT\s+INTO\s+(?!IGNORE\s+)/i',
            'INSERT IGNORE INTO ',
            $sql
        );

        // 3. SQL'i statement'lara böl (`;` ile)
        $statements = explode(';', $sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            
            // Boş statement'leri atla
            if (empty($statement)) {
                continue;
            }
            
            // Sadece yorum satırlarını atla
            if (strpos($statement, '--') === 0 || strpos($statement, '/*') === 0) {
                continue;
            }
            
            try {
                // Her statement'i ayrı ayrı çalıştır
                $this->execute($statement);
            } catch (\Exception $e) {
                // Hataları logla ama devam et
                echo "Warning: SQL statement failed: " . $e->getMessage() . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
}
