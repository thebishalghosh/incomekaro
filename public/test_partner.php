<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';
require_once '../app/models/partner.php';

echo "<h1>Partner Subscription Debug</h1>";

$db = get_db_connection();
$stmt = $db->query("SELECT id, name FROM partners LIMIT 1");
$partner_basic = $stmt->fetch();

if (!$partner_basic) {
    die("No partners found.");
}

$partner_id = $partner_basic['id'];
echo "<h3>Testing Partner: " . $partner_basic['name'] . " ($partner_id)</h3>";

$partner = get_partner_by_id($partner_id);

if (!$partner) {
    die("Could not fetch full partner details.");
}

echo "<h4>Raw Services in Subscription:</h4>";
if (empty($partner['subscription']['services'])) {
    echo "<p style='color:red;'>No services found in subscription.</p>";
} else {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Parent ID</th><th>Is Null?</th><th>Empty?</th></tr>";

    foreach ($partner['subscription']['services'] as $svc) {
        $parent_id = $svc['parent_id'];
        $is_null = is_null($parent_id) ? 'YES' : 'NO';
        $is_empty = empty($parent_id) ? 'YES' : 'NO';

        echo "<tr>";
        echo "<td>" . $svc['id'] . "</td>";
        echo "<td>" . $svc['name'] . "</td>";
        echo "<td>" . $svc['category'] . "</td>";
        echo "<td>" . var_export($parent_id, true) . "</td>";
        echo "<td>" . $is_null . "</td>";
        echo "<td>" . $is_empty . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h4>Filtered Services (Parent Only):</h4>";
$parent_services = array_filter($partner['subscription']['services'], function($svc) {
    return empty($svc['parent_id']);
});

if (empty($parent_services)) {
    echo "<p style='color:red;'>No parent services found after filtering.</p>";
} else {
    echo "<ul>";
    foreach ($parent_services as $svc) {
        echo "<li>" . $svc['name'] . "</li>";
    }
    echo "</ul>";
}
?>
