<?php
$servername = "localhost";
$database = "makinaelemanlari.com";
$username = "makinaaksesuarlari.com";
$password = "Global2019*";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    echo json_encode(["error" => "Connection failed: " . mysqli_connect_error()]);
    exit;
}

// List all tables
$sql = "SHOW TABLES";
$result = mysqli_query($conn, $sql);

$tables = [];

while ($row = mysqli_fetch_array($result)) {
    $tableName = $row[0];
    $columns = [];

    // List columns of the table
    $sql = "DESCRIBE $tableName";
    $result2 = mysqli_query($conn, $sql);

    while ($row2 = mysqli_fetch_array($result2)) {
        $columns[] = [
            "columnName" => $row2["Field"],
            "columnType" => $row2["Type"]
        ];
    }

    $tables[] = [
        "tableName" => $tableName,
        "columns" => $columns
    ];
}

echo json_encode($tables, JSON_PRETTY_PRINT);

// Close the connection
mysqli_close($conn);
?>
