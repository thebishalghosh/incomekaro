<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';
require_once '../app/models/partner.php';
require_once '../app/models/user.php';

$partner_id = $_GET['id'] ?? 'ptr-6963f42241416'; // Default to the ID you provided

echo "<h1>Debug Partner Profile: $partner_id</h1>";

$partner = get_partner_by_id($partner_id);

if ($partner) {
    echo "<h3>Partner Found</h3>";
    echo "Name: " . $partner['profile']['full_name'] . "<br>";
    echo "WL ID: " . var_export($partner['white_label_id'], true) . "<br>";

    echo "<h3>Subscription Data</h3>";
    if ($partner['subscription']) {
        echo "<pre>";
        print_r($partner['subscription']);
        echo "</pre>";

        if (empty($partner['subscription']['services'])) {
            echo "<strong style='color:red'>WARNING: No services found for this plan.</strong><br>";
            echo "Possible reasons: Plan ID mismatch, Services not linked, or Plan Name ambiguity.";
        } else {
            echo "<strong style='color:green'>Services found: " . count($partner['subscription']['services']) . "</strong>";
        }
    } else {
        echo "<strong style='color:orange'>No active subscription found.</strong>";
    }
} else {
    echo "<strong style='color:red'>Partner not found.</strong>";
}
?>
