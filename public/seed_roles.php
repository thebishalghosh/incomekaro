<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';

echo "<h1>Seeding Roles...</h1>";

$db = get_db_connection();

$roles = [
    'SUPER_ADMIN' => 'Super Admin',
    'WHITE_LABEL' => 'White Label Admin',
    'PARTNER_ADMIN' => 'Partner Admin',
    'RM' => 'Relationship Manager',
    'SALES_EXEC' => 'Sales Executive'
];

foreach ($roles as $code => $name) {
    // Check if exists
    $stmt = $db->prepare("SELECT id FROM roles WHERE code = :code");
    $stmt->execute(['code' => $code]);
    $existing = $stmt->fetch();

    if (!$existing) {
        $sql = "INSERT INTO roles (code, name) VALUES (:code, :name)";
        $stmt = $db->prepare($sql);
        $stmt->execute(['code' => $code, 'name' => $name]);
        echo "Created role: <strong>$name ($code)</strong><br>";
    } else {
        echo "Role exists: $name ($code)<br>";
    }
}

echo "<br><strong style='color:green'>Role seeding completed.</strong>";
?>
