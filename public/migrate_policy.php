<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';

echo "<h1>Migrating Policy Table...</h1>";

$db = get_db_connection();

try {
    $sql = "CREATE TABLE IF NOT EXISTS policies (
        id CHAR(36) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        type ENUM('Bank', 'Credit Card', 'Account Opening', 'Insurance', 'Other') NOT NULL,
        file_url TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $db->exec($sql);
    echo "<strong style='color:green'>Table 'policies' created successfully.</strong>";
} catch (PDOException $e) {
    echo "<strong style='color:red'>Error: " . $e->getMessage() . "</strong>";
}
?>
