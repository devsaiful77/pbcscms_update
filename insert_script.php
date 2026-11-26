<?php
// include DB credentials
include 'db_credential.php'; 

try {
    // Create connection (adjust variables from db_credential.php)
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare query
    $sql = "INSERT INTO roles 
        (name, display_name, description, is_user, is_head, guard_name, module, created_at, updated_at) 
        VALUES 
        ('complaint_closing_test', 'complaint closing', 'Complaint Closing Role', 1, 0, 'web', 1, NOW(), NOW())";

    // Execute query
    $pdo->exec($sql);

    echo "New role inserted successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
