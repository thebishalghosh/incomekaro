<?php
// Load Core Files
require_once '../app/core/config.php';
require_once '../app/core/database.php';
require_once '../app/models/user.php';

echo "<h1>Fixing Legacy Partner Data...</h1>";

$db = get_db_connection();

// 1. Find the Super Admin user
$admin_email = 'test@test.com'; // Use the correct admin email
$admin_user = find_user_by_email($admin_email);

if (!$admin_user) {
    die("<p style='color:red;'>Error: Super Admin user with email '$admin_email' not found. Please create the admin user first.</p>");
}

$admin_id = $admin_user['id'];
echo "<p>Found Super Admin with ID: $admin_id</p>";

// 2. Find all partners with no creator
$sql = "SELECT id, name FROM partners WHERE created_by IS NULL";
$stmt = $db->query($sql);
$legacy_partners = $stmt->fetchAll();

if (empty($legacy_partners)) {
    die("<p style='color:green;'>No legacy partners found. Nothing to do!</p>");
}

echo "<p>Found " . count($legacy_partners) . " legacy partners to update.</p>";

// 3. Update the partners
$update_sql = "UPDATE partners SET created_by = :admin_id WHERE id = :partner_id";
$update_stmt = $db->prepare($update_sql);
$update_stmt->bindValue(':admin_id', $admin_id);

$updated_count = 0;
foreach ($legacy_partners as $partner) {
    $update_stmt->bindValue(':partner_id', $partner['id']);
    if ($update_stmt->execute()) {
        echo "<p>Updated partner: " . $partner['name'] . " (ID: " . $partner['id'] . ")</p>";
        $updated_count++;
    }
}

echo "<h2>Update Complete!</h2>";
echo "<p style='color:green;'>Successfully updated $updated_count partners.</p>";
echo "<p><a href='" . URL_ROOT . "/partner/index'>Go back to Partner List</a></p>";
