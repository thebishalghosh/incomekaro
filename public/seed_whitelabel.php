<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';

echo "<h1>Seeding White Label Client & User...</h1>";

$db = get_db_connection();

// 1. Ensure WHITE_LABEL role exists
$stmt = $db->prepare("SELECT id FROM roles WHERE code = 'WHITE_LABEL'");
$stmt->execute();
$role = $stmt->fetch();

if (!$role) {
    $db->exec("INSERT INTO roles (code, name) VALUES ('WHITE_LABEL', 'White Label Admin')");
    $role_id = $db->lastInsertId();
    echo "<p style='color:green;'>Created WHITE_LABEL role.</p>";
} else {
    $role_id = $role['id'];
}

// 2. Create/Check Client
$domain = 'partner1.test';
$stmt = $db->prepare("SELECT id FROM white_label_clients WHERE primary_domain = :domain");
$stmt->execute(['domain' => $domain]);
$existing_client = $stmt->fetch();

$client_id = $existing_client ? $existing_client['id'] : 'wl-' . uniqid();

if ($existing_client) {
    echo "<p style='color:orange;'>Client for domain '$domain' already exists. ID: " . $client_id . "</p>";
} else {
    $company_name = 'Partner One Finance';
    $logo_url = 'images/partner1-logo.png';
    $primary_color = '#28a745';
    $secondary_color = '#218838';

    $landing_data = json_encode([
        'hero_title' => 'Partner One Finance Solutions',
        'hero_text' => 'Your trusted partner for loans and credit cards.',
        'about_text' => 'We are a leading distributor of financial products...',
        'contact_email' => 'support@partner1.test',
        'contact_phone' => '+91 98765 43210'
    ]);

    $sql = "INSERT INTO white_label_clients (id, company_name, primary_domain, logo_url, primary_color, secondary_color, landing_page_data, status)
            VALUES (:id, :company_name, :primary_domain, :logo_url, :primary_color, :secondary_color, :landing_page_data, 'active')";

    $stmt = $db->prepare($sql);
    $params = [
        'id' => $client_id,
        'company_name' => $company_name,
        'primary_domain' => $domain,
        'logo_url' => $logo_url,
        'primary_color' => $primary_color,
        'secondary_color' => $secondary_color,
        'landing_page_data' => $landing_data
    ];

    if ($stmt->execute($params)) {
        echo "<p style='color:green;'>Successfully created White Label Client.</p>";
    } else {
        echo "<p style='color:red;'>Failed to create client.</p>";
        print_r($stmt->errorInfo());
        exit;
    }
}

// 3. Create User for this Client
$user_email = 'admin@partner1.test';
$stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute(['email' => $user_email]);
$existing_user = $stmt->fetch();

if ($existing_user) {
    echo "<p style='color:orange;'>User '$user_email' already exists.</p>";
} else {
    $user_id = 'u-' . uniqid();
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (id, white_label_id, role_id, first_name, last_name, email, phone, password_hash, status)
            VALUES (:id, :white_label_id, :role_id, :first_name, :last_name, :email, :phone, :password_hash, 'active')";

    $stmt = $db->prepare($sql);
    $params = [
        'id' => $user_id,
        'white_label_id' => $client_id,
        'role_id' => $role_id,
        'first_name' => 'Partner',
        'last_name' => 'One Admin',
        'email' => $user_email,
        'phone' => '9876543210',
        'password_hash' => $password_hash
    ];

    if ($stmt->execute($params)) {
        echo "<p style='color:green;'>Successfully created User: $user_email / password123</p>";
    } else {
        echo "<p style='color:red;'>Failed to create user.</p>";
        print_r($stmt->errorInfo());
    }
}
?>
