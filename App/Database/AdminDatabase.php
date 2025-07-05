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

class AdminDatabase
{
    public $pdo;

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
            Log::adminWrite($e->getMessage().", ".(int)$e->getCode(), "error");
            die("Veritabanı bağlantı hatası, lütfen bilgilerin doğruluğundan emin olun");
        }
    }

    public function createTable($query)
    {
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            Log::adminWrite("Table creation failed: {$e->getMessage()}", "error");
            return false;
        }
    }

    public function beginTransaction($funcName = "") {
        if (!$this->pdo->inTransaction()) {
            Log::adminWrite("Database transaction started $funcName", "warning");
            $this->pdo->beginTransaction();
        }else {
            Log::adminWrite("A transaction already exists $funcName", "warning");
        }
    }

    public function commit($funcName = "") {
        if ($this->pdo->inTransaction()) { //Eğer transaction aktif ise
            try {
                Log::adminWrite("Database transaction committed $funcName", "warning");
                $this->pdo->commit();
            } catch (PDOException $e) {
                Log::adminWrite($e->getMessage(), "error");
                $this->rollBack($funcName);
                throw $e;
            }
        } else {
            Log::adminWrite("No active transaction to commit", "error");
        }
    }

    public function rollback($funcName = "") {
        if ($this->pdo->inTransaction()) {
            Log::adminWrite("Database transaction rolled back $funcName", "warning");
            $this->pdo->rollBack();
        } else {
            Log::adminWrite("No active transaction to rollback $funcName", "error");
        }
    }

    public function select($query, $params = [])
    {
        try {
            //params varsa eşleştirim log'a atalım
            if (!empty($params)) {
                $queryWithParams = $query;
                foreach ($params as $key => $value) {
                    $queryWithParams = str_replace(":$key", "'$value'", $queryWithParams);
                }
                //Log::adminWrite("Database select query: $queryWithParams", "info");
            }
            //Log::adminWrite("Database select query: $query |params: ". implode("",$params), "info");
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            Log::adminWrite("Database error: {$e->getMessage()}|Sql: $query |params: ". implode(" _ ",$params), "error");
            return false;
        }
    }

    public function insert($query, $params)
    {
        try {
            //Log::adminWrite("Database insert query: $query |params: ". implode("|",$params), "info");
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        }
        catch (PDOException $e) {
            Log::adminWrite("Database error: {$e->getMessage()}", "error");
            return false;
        }
        catch (Exception $e) {
            Log::adminWrite("General error: {$e->getMessage()}", "error");
            return false;
        }
    }

    public function update($query, $params)
    {
        try {
            //Log::adminWrite("Database update query: $query |params: ". implode("|",$params), "info");
            //query ve parametreleri eşleştir
            $queryWithParams = $query;
            foreach ($params as $key => $value) {
                $queryWithParams = str_replace(":$key", "'$value'", $query);
            }

            Log::adminWrite("Database update query: $queryWithParams", "info");

            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($params);

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                Log::adminWrite("Database update query: $query | params: " . json_encode($params) ."|errorInfo: ".json_encode($errorInfo), "error");
                return -1;
            }

            return $stmt->rowCount();
        }
        catch (PDOException $e) {
            Log::adminWrite("Database error: {$e->getMessage()}", "error");
            return -1;
        }
        catch (Exception $e) {
            Log::adminWrite("General error: {$e->getMessage()}", "error");
            return -1;
        }
    }

    public function delete($query, $params)
    {
        try {
            Log::adminWrite("Database delete query: $query |params: ". implode("|",$params), "warning");
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        }
        catch (PDOException $e) {
            Log::adminWrite("Database error: {$e->getMessage()}", "error");
            return false;
        }
    }

    public function prepare($query) {
        try {
            return $this->pdo->prepare($query);
        } catch (PDOException $e) {
            //return 'Prepare Error: ' . $e->getMessage();
            Log::adminWrite("Database error: {$e->getMessage()}", "error");
        }
    }

    public function truncateTable($tableName)
    {
        try {
            $stmt = $this->pdo->prepare("TRUNCATE TABLE $tableName");
            return $stmt->execute();
        } catch (PDOException $e) {
            Log::adminWrite("Truncate table failed: {$e->getMessage()}", "error");
            return false;
        }
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

    public function close(){
        $this->pdo = null;
    }

    public function inTransaction($funcname=""){
        Log::adminWrite("Transaction Kontrolü yapılıyor","warning");
        return $this->pdo->inTransaction();
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
}