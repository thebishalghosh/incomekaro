<?php
require_once APP_PATH . '/models/policy.php';

function policy_index() {
    require_role('SUPER_ADMIN');
    $policies = get_all_policies();
    view('policy/index', ['policies' => $policies]);
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
