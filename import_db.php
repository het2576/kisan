<?php
require_once 'db_connect.php';

$sql = file_get_contents('kisan_db.sql');

try {
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        if (!$conn->query($statement)) {
            echo "Error executing statement: " . $conn->error . "<br>";
        }
    }
    
    echo "Database setup completed successfully!";
} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?> 