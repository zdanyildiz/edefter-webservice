<?php
// $root = $_SERVER['DOCUMENT_ROOT'];
// include_once "$root/App/Core/Log.php";

// ROOT sabitini kullan (Config.php'de tanımlandı)
if (defined('ROOT')) {
    include_once ROOT . "App/Core/Log.php";
} else {
    // Fallback - eğer ROOT tanımlı değilse
    $documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
    include_once $documentRoot . "/App/Core/Log.php";
}

class Database
{
    public $pdo;
    private $transactionActive = false;

    public function __construct($host, $db, $user, $pass, $charset = 'utf8mb4')
    {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            //PDO::ATTR_AUTOCOMMIT => 0,
        ];
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            //throw new PDOException($e->getMessage(), (int)$e->getCode());
            Log::write($e->getMessage().", ".(int)$e->getCode(), "error");
            die("Veritabanı bağlantı hatası, lütfen bilgilerin doğruluğundan emin olun");
        }
    }

    public function createTable($query)
    {
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            Log::write("Table creation failed: {$e->getMessage()}", "error");
            return false;
        }
    }

    public function select($query, $params = [])
    {
        try {
            //Log::write("Database select query: $query |params: ". implode("",$params), "info");
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            Log::write("Database error: {$e->getMessage()}|Sql: $query |params: ". implode("",$params), "error");
            return false;
        }
    }

    public function insert($query, $params)
    {
        try {
            Log::write("Database insert query: $query |params: " . implode("|", array_map(function($param) {
                    return is_array($param) ? json_encode($param) : $param;
                }, $params)), "info");

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        }
        catch (PDOException $e) {
            Log::write("Database error: {$e->getMessage()}", "error");
            return false;
        }
        catch (Exception $e) {
            Log::write("General error: {$e->getMessage()}", "error");
            return false;
        }
    }

    public function update($query, $params)
    {
        try {
            Log::write("Database update query: $query |params: ". implode("|",$params), "info");
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($params);

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                Log::write("Database execute error: " . $errorInfo[2], "error");
                return -1;
            }

            return $stmt->rowCount();
        }
        catch (PDOException $e) {
            Log::write("Database error: {$e->getMessage()}", "error");
            return -1;
        }
        catch (Exception $e) {
            Log::write("General error: {$e->getMessage()}", "error");
            return -1;
        }
    }

    public function delete($query, $params)
    {
        try {
            //Log::write("Database delete query: $query |params: ". implode("",$params), "warning");
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        }
        catch (PDOException $e) {
            Log::write("Database error: {$e->getMessage()}", "error");
            return false;
        }
    }

    public function prepare($query) {
        try {
            return $this->pdo->prepare($query);
        } catch (PDOException $e) {
            //return 'Prepare Error: ' . $e->getMessage();
            Log::write("Database error: {$e->getMessage()}", "error");
        }
    }

    /**
     * SQL dosyasını çalıştırma metodu.
     *
     * @param string $filePath SQL dosyasının tam yolu
     * @return array İşlem sonuçları ve hatalar
     */
    public function runSqlFile($filePath)
    {
        if (!file_exists($filePath)) {
            return [
                "status" => "error",
                "message" => "SQL dosyası bulunamadı."
            ];
        }

        try {
            $file = fopen($filePath, 'r');
            if (!$file) {
                return [
                    "status" => "error",
                    "message" => "SQL dosyası açılamadı."
                ];
            }

            $sql = '';
            $errors = []; // Hataları toplamak için
            while (($line = fgets($file)) !== false) {
                $line = trim($line);

                // Yorumları ve boş satırları atla
                if (substr($line, 0, 2) === '--' || $line === '') {
                    continue;
                }

                // Sorguyu birleştir
                $sql .= $line . " ";

                // Noktalı virgül ile biten sorguları çalıştır
                if (substr($line, -1) === ';') {
                    $trimmedSql = trim($sql);
                    if (!empty($trimmedSql)) {
                        try {
                            $this->pdo->exec($trimmedSql);
                        } catch (PDOException $e) {
                            $errors[] = [
                                "query" => $trimmedSql,
                                "error" => $e->getMessage()
                            ];
                        }
                    }
                    $sql = ''; // Sonraki sorgu için temizle
                }
            }

            fclose($file);

            // Kalan sorgu varsa çalıştır
            $trimmedSql = trim($sql);
            if (!empty($trimmedSql)) {
                try {
                    $this->pdo->exec($trimmedSql);
                } catch (PDOException $e) {
                    $errors[] = [
                        "query" => $trimmedSql,
                        "error" => $e->getMessage()
                    ];
                }
            }

            // Hataları döndür
            if (!empty($errors)) {
                return [
                    "status" => "error",
                    "message" => "SQL dosyasındaki bazı sorgular çalıştırılamadı.",
                    "errors" => $errors
                ];
            }

            return [
                "status" => "success",
                "message" => "SQL dosyası başarıyla çalıştırıldı."
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => "Beklenmeyen bir hata oluştu: " . $e->getMessage()
            ];
        }
    }

    public function close(){
        $this->pdo = null;
    }

    public function inTransaction(){
        return $this->pdo->inTransaction();
    }

    public function beginTransaction($funcName = "") {
        if (!$this->transactionActive) {
            Log::write("Database transaction started $funcName", "warning");
            $this->transactionActive = $this->pdo->beginTransaction();
        } else {
            Log::write("Transaction already active in $funcName", "warning");
        }
        return $this->transactionActive;
    }

    public function commit($funcName = "") {
        if ($this->transactionActive) {
            Log::write("Database transaction committed $funcName", "success");
            $this->transactionActive = !$this->pdo->commit();
        } else {
            Log::write("No active transaction to commit in $funcName", "warning");
        }
        return !$this->transactionActive;
    }

    public function rollback($funcName = "") {
        if ($this->transactionActive) {
            Log::write("Database transaction rolled back $funcName", "warning");
            $this->transactionActive = !$this->pdo->rollBack();
        } else {
            Log::write("No active transaction to roll back in $funcName", "warning");
        }
        return !$this->transactionActive;
    }

    public function isTransactionActive() {
        return $this->transactionActive;
    }

    public function listTablesWithColumnDetails(){
        $tablesQuery = $this->pdo->query("SHOW TABLES");
        $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

        $tableDetails = [];
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM $table");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tableDetails[$table][] = [
                    "field" => $row['Field'],
                    "type" => $row['Type'],
                    "null" => $row['Null'],
                    "key" => $row['Key'],
                    "default" => $row['Default'],
                    "extra" => $row['Extra']
                ];
            }
        }

        echo json_encode($tableDetails, JSON_PRETTY_PRINT);
    }

    public function matchDB(){
        $tablesQuery = $this->pdo->query("SHOW TABLES");
        $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

        $columns = [];
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM $table");
            $columns[$table] = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[$table][] = $row['Field'];
            }
        }

        $results = ['tables' => []];
        foreach ($columns as $table1 => $cols1) {
            foreach ($columns as $table2 => $cols2) {
                if ($table1 != $table2) {
                    $matches = array_intersect($cols1, $cols2);
                    if (!empty($matches)) {
                        foreach ($matches as $matchedField) {
                            $results['tables'][$table1]['fields'][] = $matchedField;
                            $results['tables'][$table1]['relations'][$table2] = 'unknown_relation'; // İlişki türü bilinmiyor
                        }
                    }
                }
            }
            $results['tables'][$table1]['fields'] = array_unique($results['tables'][$table1]['fields'] ?? []);
        }

        echo json_encode($results, JSON_PRETTY_PRINT);
    }

    public function matchFields(){
        $tablesQuery = $this->pdo->query("SHOW TABLES");
        $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

        $columnUsage = [];
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM $table");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columnUsage[$row['Field']][] = $table;
            }
        }

        $sharedColumns = array_filter($columnUsage, function($tables) {
            return count($tables) > 1;
        });

        $results = [];
        foreach ($sharedColumns as $field => $tables) {
            $results[] = [
                "field" => $field,
                "tables" => $tables
            ];
        }

        echo json_encode($results, JSON_PRETTY_PRINT);
    }
}