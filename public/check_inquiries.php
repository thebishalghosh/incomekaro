<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';

echo "<h1>Contact Inquiries Check</h1>";

$db = get_db_connection();
$stmt = $db->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC LIMIT 5");
$inquiries = $stmt->fetchAll();

if (empty($inquiries)) {
    echo "<p>No inquiries found.</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>White Label ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Created At</th></tr>";
    foreach ($inquiries as $inq) {
        echo "<tr>";
        echo "<td>" . $inq['id'] . "</td>";
        echo "<td>" . ($inq['white_label_id'] ?? 'NULL') . "</td>";
        echo "<td>" . $inq['name'] . "</td>";
        echo "<td>" . $inq['email'] . "</td>";
        echo "<td>" . $inq['subject'] . "</td>";
        echo "<td>" . substr($inq['message'], 0, 50) . "...</td>";
        echo "<td>" . $inq['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
