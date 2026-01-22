<?php
require_once APP_PATH . '/models/policy.php';

function policy_index() {
    require_role('SUPER_ADMIN');

    // Handle AJAX Search & Pagination
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $type_filter = isset($_GET['type']) ? trim($_GET['type']) : '';

        $policies = get_all_policies($page, $limit, $type_filter);
        $total_policies = get_total_policies_count($type_filter);
        $total_pages = ceil($total_policies / $limit);

        // Return JSON
        header('Content-Type: application/json');
        echo json_encode([
            'policies' => $policies,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_policies
            ]
        ]);
        exit;
    }

    // Initial Load
    view('policy/index');
}

function policy_list() {
    require_login();
    // Allow Partners, RMs, Sales Execs, WL Admins
    // Basically anyone logged in can view policies

    // For now, list view doesn't have pagination/ajax requested, keeping it simple or can be upgraded later
    // But get_all_policies now requires arguments or defaults.
    // Let's fetch all for list view (limit 100 or so)
    $policies = get_all_policies(1, 100);

    // If Partner, use partner layout
    if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        view('policy/list_partner', ['policies' => $policies]);
    } else {
        // For others, maybe a generic list or redirect
        // For now, let's use the same view but header might differ
        view('policy/list_partner', ['policies' => $policies]);
    }
}

function policy_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name']);
        $type = $_POST['type'];

        $file_url = '';
        if (isset($_FILES['policy_file']) && $_FILES['policy_file']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/policies/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['policy_file']['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['policy_file']['tmp_name'], $target_file)) {
                $file_url = 'uploads/policies/' . $file_name;
            }
        }

        if (empty($file_url)) {
            flash('policy_error', 'Please upload a PDF file.', 'alert alert-danger');
            redirect('policy/index');
        }

        if (create_policy(['name' => $name, 'type' => $type, 'file_url' => $file_url])) {
            flash('policy_success', 'Policy added successfully.');
        } else {
            flash('policy_error', 'Failed to add policy.', 'alert alert-danger');
        }

        redirect('policy/index');
    }
}

function policy_delete($id) {
    require_role('SUPER_ADMIN');
    if (delete_policy($id)) {
        flash('policy_success', 'Policy deleted.');
    } else {
        flash('policy_error', 'Failed to delete policy.', 'alert alert-danger');
    }
    redirect('policy/index');
}
