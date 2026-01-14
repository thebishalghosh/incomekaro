<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';
require_once '../app/models/user.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please log in as WL Admin.');
}

$user = find_user_by_id($_SESSION['user_id']);
$wl_id = $user['white_label_id'];

echo "<h1>Debug Duplicates for WL: $wl_id</h1>";

$db = get_db_connection();

// Fetch Partner Admin Role ID
$role_stmt = $db->prepare("SELECT id FROM roles WHERE code = 'PARTNER_ADMIN'");
$role_stmt->execute();
$partner_role_id = $role_stmt->fetchColumn();
echo "Partner Role ID: $partner_role_id<br><br>";

// Run the query
$sql = "SELECT p.id as partner_id, u.id as user_id, ubd.id as bank_id
        FROM partners p
        LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
        LEFT JOIN white_label_clients wl ON p.white_label_id = wl.id
        LEFT JOIN users u ON u.partner_id = p.id AND u.role_id = :role_id
        LEFT JOIN user_bank_details ubd ON u.id = ubd.user_id
        WHERE p.white_label_id = :wl_id
        ORDER BY p.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute(['wl_id' => $wl_id, 'role_id' => $partner_role_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Partner ID</th><th>User ID</th><th>Bank ID</th></tr>";
foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>" . $row['partner_id'] . "</td>";
    echo "<td>" . $row['user_id'] . "</td>";
    echo "<td>" . $row['bank_id'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
