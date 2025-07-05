<?php
$documentRoot = str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

// Ön tanımlı ayarlar
$config = new Config();

// Database sınıfları
$db1 = new Database("localhost", "eticaret.globalpozitif.com", "root", "Global2019*");
$db2 = new Database("localhost", "mete.com.tr", "root", "Global2019*");

// Tablo ve sütunları al
function getTableDetails($db) {
    $tablesQuery = $db->pdo->query("SHOW TABLES");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    $tableDetails = [];
    foreach ($tables as $table) {
        $stmt = $db->pdo->query("SHOW COLUMNS FROM $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tableDetails[$table][] = $row['Field'];
        }
    }
    return $tableDetails;
}

$db1Tables = getTableDetails($db1);
$db2Tables = getTableDetails($db2);

// Farkları bul
function compareTables($db1Tables, $db2Tables) {
    $allTables = array_unique(array_merge(array_keys($db1Tables), array_keys($db2Tables)));
    $comparison = [];

    foreach ($allTables as $table) {
        $db1Columns = $db1Tables[$table] ?? [];
        $db2Columns = $db2Tables[$table] ?? [];
        $comparison[$table] = [
            "db1" => $db1Columns,
            "db2" => $db2Columns,
        ];
    }

    return $comparison;
}

$comparison = compareTables($db1Tables, $db2Tables);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Comparison</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            gap: 20px;
        }
        .table-container {
            flex: 1;
            overflow-x: auto;
        }
        .highlight {
            background-color: #ffcccc;
        }
    </style>
</head>
<body>
<h1>Database Comparison</h1>
<div class="container">
    <div class="table-container">
        <h2>Pozitif Eticaret</h2>
        <?php foreach ($comparison as $table => $columns): ?>
            <table>
                <thead>
                <tr>
                    <th>Table: <?= htmlspecialchars($table) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($columns['db1'] as $column): ?>
                    <tr>
                        <td><?= htmlspecialchars($column) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($columns['db1'])): ?>
                    <tr>
                        <td class="highlight">Not Found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
    <div class="table-container">
        <h2>Mete Database</h2>
        <?php foreach ($comparison as $table => $columns): ?>
            <table>
                <thead>
                <tr>
                    <th>Table: <?= htmlspecialchars($table) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($columns['db2'] as $column): ?>
                    <tr>
                        <td><?= htmlspecialchars($column) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($columns['db2'])): ?>
                    <tr>
                        <td class="highlight">Not Found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
