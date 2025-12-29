<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';

echo "<h1>Seeding Roles...</h1>";

$db = get_db_connection();

$roles = [
    ['code' => 'SUPER_ADMIN', 'name' => 'Super Admin'],
    ['code' => 'RM', 'name' => 'Relationship Manager'],
    ['code' => 'SALES_EXEC', 'name' => 'Sales Executive'],
    ['code' => 'PARTNER_ADMIN', 'name' => 'Partner Admin'],
];

$count = 0;
foreach ($roles as $role) {
    $stmt = $db->prepare("SELECT id FROM roles WHERE code = :code");
    $stmt->execute(['code' => $role['code']]);

    if ($stmt->fetch()) {
        echo "<p style='color:orange;'>Role '" . $role['name'] . "' already exists. Skipping.</p>";
    } else {
        $insert_stmt = $db->prepare("INSERT INTO roles (code, name) VALUES (:code, :name)");
        if ($insert_stmt->execute($role)) {
            echo "<p style='color:green;'>Role '" . $role['name'] . "' created successfully.</p>";
            $count++;
        }
    }
}

echo "<h2>Seed Complete!</h2>";
echo "<p><b>$count</b> new role(s) created.</p>";
echo "<p><a href='" . URL_ROOT . "/user/create'>Go back to Create User form</a></p>";
