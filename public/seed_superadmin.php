<?php
require_once 'app/core/config.php';
require_once 'app/core/database.php';

echo "<h1>Seeding Super Admin...</h1>";

$db = get_db_connection();

// 1. Ensure Role Exists
$stmt = $db->prepare("SELECT id FROM roles WHERE code = 'SUPER_ADMIN'");
$stmt->execute();
$role = $stmt->fetch();

if (!$role) {
    echo "Creating SUPER_ADMIN role...<br>";
    $db->exec("INSERT INTO roles (code, name) VALUES ('SUPER_ADMIN', 'Super Admin')");
    $role_id = $db->lastInsertId();
} else {
    echo "SUPER_ADMIN role exists (ID: " . $role['id'] . ").<br>";
    $role_id = $role['id'];
}

// 2. Create User
$email = 'incomekaro@gmail.com';
$password = 'incomekaro';
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user) {
    echo "Creating Super Admin user...<br>";
    $sql = "INSERT INTO users (id, role_id, first_name, last_name, email, password_hash, status)
            VALUES (:id, :role_id, 'Super', 'Admin', :email, :password_hash, 'active')";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id' => uniqid('u-'),
        'role_id' => $role_id,
        'email' => $email,
        'password_hash' => $password_hash
    ]);
    echo "<strong style='color:green'>Super Admin created successfully!</strong><br>";
    echo "Email: $email<br>";
    echo "Password: $password<br>";
} else {
    echo "<strong style='color:orange'>User already exists. Updating password...</strong><br>";
    $stmt = $db->prepare("UPDATE users SET password_hash = :hash, role_id = :role_id, status = 'active' WHERE email = :email");
    $stmt->execute([
        'hash' => $password_hash,
        'role_id' => $role_id,
        'email' => $email
    ]);
    echo "Password updated.<br>";
}
?>
