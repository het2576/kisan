<?php
require_once 'db_connect.php';

// First check if tables already exist
$tables_exist = true;
$required_tables = ['categories', 'products', 'product_images'];

foreach ($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        $tables_exist = false;
        break;
    }
}

if ($tables_exist) {
    echo "Tables already exist!<br>";
    exit();
}

// Read and execute SQL file
$sql = file_get_contents('create_marketplace_tables.sql');

// Split SQL into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = true;
$error_message = '';

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    
    try {
        if (!$conn->query($statement)) {
            $success = false;
            $error_message .= "Error executing statement: " . $conn->error . "<br>";
        }
    } catch (Exception $e) {
        $success = false;
        $error_message .= "Exception: " . $e->getMessage() . "<br>";
    }
}

if ($success) {
    echo "<div style='color: green; font-weight: bold;'>";
    echo "Database tables created successfully!<br>";
    echo "Created tables: " . implode(', ', $required_tables) . "<br>";
    echo "</div>";
} else {
    echo "<div style='color: red; font-weight: bold;'>";
    echo "Error creating tables:<br>";
    echo $error_message;
    echo "</div>";
}

// Verify tables were created
$verification_errors = [];
foreach ($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        $verification_errors[] = "Table '$table' was not created.";
    }
}

if (!empty($verification_errors)) {
    echo "<div style='color: red; margin-top: 20px;'>";
    echo "Verification Errors:<br>";
    echo implode("<br>", $verification_errors);
    echo "</div>";
}

// Check if users table exists (since products table depends on it)
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    echo "<div style='color: red; font-weight: bold; margin-top: 20px;'>";
    echo "Warning: The 'users' table does not exist. This is required for the foreign key constraint in the products table.<br>";
    echo "Please create the users table first.";
    echo "</div>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        line-height: 1.6;
    }
    div {
        margin: 10px 0;
        padding: 10px;
        border-radius: 4px;
    }
</style> 