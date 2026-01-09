<?php
require_once APP_PATH . '/models/service.php';

function get_main_instant_panel_id() {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT id FROM services WHERE category = 'INSTANT_PANEL' AND parent_id IS NULL LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result ? $result['id'] : null;
}

function instant_panel_index() {
    require_role('SUPER_ADMIN');

    // Fetch only Instant Panel services that are NOT the parent container
    $db = get_db_connection();
    $sql = "SELECT * FROM services WHERE category = 'INSTANT_PANEL' AND parent_id IS NOT NULL ORDER BY created_at DESC";
    $stmt = $db->query($sql);
    $panels = $stmt->fetchAll();

    // Group by type for the accordion view
    $panels_by_type = [];
    foreach ($panels as $panel) {
        $type = $panel['panel_type'] ?: 'Uncategorized';
        $panels_by_type[$type][] = $panel;
    }

    view('dashboard/instant_panel', ['panels_by_type' => $panels_by_type]);
}

function instant_panel_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $parent_id = get_main_instant_panel_id();

        // If parent doesn't exist, create it automatically
        if (!$parent_id) {
            $db = get_db_connection();
            $parent_id = uniqid('svc-');
            $sql = "INSERT INTO services (id, name, category, service_type, form_type, is_active) VALUES (:id, 'Instant Panel', 'INSTANT_PANEL', 'INTERNAL_FORM', 'NONE', 1)";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $parent_id]);
        }

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/panels/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'uploads/panels/' . $file_name;
            }
        }

        $data = [
            'id' => uniqid('svc-'),
            'name' => $name,
            'description' => '',
            'url' => $url,
            'image_url' => $image_url,
            'category' => 'INSTANT_PANEL',
            'is_active' => 1,
            'service_type' => 'EXTERNAL_REDIRECT',
            'parent_id' => $parent_id, // Always link to parent
            'form_type' => 'NONE',
            'panel_type' => $type
        ];

        if (create_service($data)) {
            flash('panel_success', 'Instant Panel Added Successfully');
        } else {
            flash('panel_error', 'Failed to add panel', 'alert alert-danger');
        }

        redirect('instant_panel/index');
    }
}

function instant_panel_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

        $service = get_service_by_id($id);
        $image_url = $service['image_url'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/panels/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'uploads/panels/' . $file_name;
            }
        }

        $data = [
            'id' => $id,
            'name' => $name,
            'description' => $service['description'],
            'url' => $url,
            'category' => 'INSTANT_PANEL',
            'is_active' => $service['is_active'],
            'service_type' => 'EXTERNAL_REDIRECT',
            'parent_id' => $service['parent_id'],
            'form_type' => 'NONE',
            'panel_type' => $type,
            'image_url' => $image_url
        ];

        if (update_service($data)) {
            flash('panel_success', 'Instant Panel Updated Successfully');
        } else {
            flash('panel_error', 'Failed to update panel', 'alert alert-danger');
        }

        redirect('instant_panel/index');
    }
}

function instant_panel_delete($id) {
    require_role('SUPER_ADMIN');

    if (delete_service($id)) {
        flash('panel_success', 'Instant Panel Deleted');
    } else {
        flash('panel_error', 'Failed to delete panel', 'alert alert-danger');
    }

    redirect('instant_panel/index');
}

function instant_panel_list_by_type($encoded_type) {
    require_login();
    require_agreement();
    require_kyc_verification();

    // Decode the base64 encoded type
    $type = base64_decode($encoded_type);

    if ($type === false) {
        // Fallback if decoding fails (e.g. user manually typed URL)
        $type = urldecode($encoded_type);
    }

    $db = get_db_connection();
    $sql = "SELECT * FROM services WHERE panel_type = :type AND is_active = 1 ORDER BY name";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':type', $type);
    $stmt->execute();
    $panels = $stmt->fetchAll();

    view('application/list_panels', ['type' => $type, 'panels' => $panels]);
}
