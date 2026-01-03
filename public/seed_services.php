<?php
require_once '../app/core/config.php';
require_once '../app/core/database.php';

echo "<h1>Seeding Services Hierarchy...</h1>";

$db = get_db_connection();

// Define the service hierarchy
$services_structure = [
    [
        'name' => 'Loan',
        'category' => 'LOAN',
        'service_type' => 'INTERNAL_FORM',
        'form_type' => 'NONE',
        'image_url' => 'images/services/loan.png', // Placeholder
        'children' => [
            [
                'name' => 'Govt. Loan',
                'category' => 'LOAN',
                'service_type' => 'INTERNAL_FORM',
                'form_type' => 'NONE',
                'image_url' => 'images/services/govt_loan.png',
                'children' => [
                    ['name' => 'MUDRA Loan', 'form_type' => 'GOVT_LOAN', 'image_url' => 'images/services/mudra.png'],
                    ['name' => 'MSME Loan', 'form_type' => 'GOVT_LOAN', 'image_url' => 'images/services/msme.png'],
                    ['name' => 'PMEGP Loan', 'form_type' => 'GOVT_LOAN', 'image_url' => 'images/services/pmegp.png'],
                    ['name' => 'OD-CC', 'form_type' => 'GOVT_LOAN', 'image_url' => 'images/services/odcc.png'],
                ]
            ],
            [
                'name' => 'Private Loan',
                'category' => 'LOAN',
                'service_type' => 'INTERNAL_FORM',
                'form_type' => 'NONE',
                'image_url' => 'images/services/private_loan.png',
                'children' => [
                    ['name' => 'Personal Loan', 'form_type' => 'PRIVATE_LOAN', 'image_url' => 'images/services/personal.png'],
                    ['name' => 'Business Loan', 'form_type' => 'PRIVATE_LOAN', 'image_url' => 'images/services/business.png'],
                    ['name' => 'Home Loan', 'form_type' => 'PRIVATE_LOAN', 'image_url' => 'images/services/home.png'],
                    ['name' => 'Loan Against Property', 'form_type' => 'PRIVATE_LOAN', 'image_url' => 'images/services/lap.png'],
                    ['name' => 'Car Loan', 'form_type' => 'PRIVATE_LOAN', 'image_url' => 'images/services/car.png'],
                    ['name' => 'Old Car Loan', 'form_type' => 'PRIVATE_LOAN', 'image_url' => 'images/services/old_car.png'],
                ]
            ]
        ]
    ],
    // You can add other top-level services like 'Tax' here in the future
];

function seed_services($db, $services, $parent_id = null) {
    $count = 0;
    foreach ($services as $service_data) {
        // Check if service exists
        $stmt = $db->prepare("SELECT id FROM services WHERE name = :name");
        $stmt->execute(['name' => $service_data['name']]);
        $existing_service = $stmt->fetch();

        if ($existing_service) {
            echo "<p style='color:orange;'>Service '" . $service_data['name'] . "' already exists. Skipping creation, but processing children.</p>";
            $service_id = $existing_service['id'];
        } else {
            // Insert service
            $sql = "INSERT INTO services (id, name, category, service_type, form_type, parent_id, image_url, is_active)
                    VALUES (:id, :name, :category, :service_type, :form_type, :parent_id, :image_url, 1)";

            $stmt = $db->prepare($sql);

            $service_id = 'svc-' . uniqid();
            $params = [
                'id' => $service_id,
                'name' => $service_data['name'],
                'category' => $service_data['category'] ?? 'OTHER',
                'service_type' => $service_data['service_type'] ?? 'INTERNAL_FORM',
                'form_type' => $service_data['form_type'] ?? 'NONE',
                'parent_id' => $parent_id,
                'image_url' => $service_data['image_url'] ?? null
            ];

            if ($stmt->execute($params)) {
                echo "<p style='color:green;'>Service '" . $service_data['name'] . "' created successfully.</p>";
                $count++;
            } else {
                echo "<p style='color:red;'>Failed to create service '" . $service_data['name'] . "'.</p>";
                continue; // Skip children if parent fails
            }
        }

        // If there are children, recurse
        if (!empty($service_data['children'])) {
            $count += seed_services($db, $service_data['children'], $service_id);
        }
    }
    return $count;
}

$total_created = seed_services($db, $services_structure);

echo "<h2>Seed Complete!</h2>";
echo "<p><b>$total_created</b> new service(s) created.</p>";
echo "<p><a href='" . URL_ROOT . "'>Go back to Home</a></p>";

?>
