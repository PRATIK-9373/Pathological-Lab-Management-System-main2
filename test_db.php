<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('classes/DBConnection.php');

echo "<h1>Database Connection Test</h1>";

try {
    $db = new DBConnection();
    $conn = $db->conn;

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "<p style='color:green'><strong>Connected successfully to MySQL.</strong></p>";
    echo "Host info: " . $conn->host_info . "<br>";
    
    // Check if database is selected
    $result = $conn->query("SELECT DATABASE()");
    if ($result) {
        $row = $result->fetch_row();
        echo "Selected Database: <strong>" . $row[0] . "</strong><br>";
    } else {
        echo "<p style='color:red'>Could not query CURRENT DATABASE.</p>";
    }

    // Check table existence
    echo "<h2>Checking 'client_list' Table</h2>";
    $result = $conn->query("SHOW TABLES LIKE 'client_list'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color:green'>Table 'client_list' exists.</p>";
        
        // Show columns
        $result = $conn->query("SHOW COLUMNS FROM client_list");
        if ($result) {
            echo "<h3>Columns:</h3><ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color:red'>Table 'client_list' DOES NOT EXIST in this database.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
}
?>
