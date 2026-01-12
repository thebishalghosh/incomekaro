<?php
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/subscription.php'; // Need to fetch plans

function white_label_index() {
    require_role('SUPER_ADMIN');
    $clients = get_all_white_labels();
    view('dashboard/white_labels_list', ['clients' => $clients]);
}

function white_label_create() {
    require_role('SUPER_ADMIN');
    view('forms/white_label_form');
}

function white_label_store() {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and Validate
        $data = [
            'id' => uniqid('wl-'),
            'company_name' => trim($_POST['company_name']),
            'primary_domain' => trim($_POST['primary_domain']),
            'logo_url' => '', // Handle upload
            'primary_color' => $_POST['primary_color'],
            'secondary_color' => $_POST['secondary_color'],
            'support_email' => trim($_POST['support_email']),
            'status' => $_POST['status']
        ];

        // Handle Logo Upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['logo']['name']);
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $file_name)) {
                $data['logo_url'] = 'uploads/logos/' . $file_name;
            }
        }

        if (create_white_label($data)) {
            flash('wl_success', 'White Label Client Created Successfully');
            redirect('white_label/index');
        } else {
            flash('wl_error', 'Failed to create client', 'alert alert-danger');
            redirect('white_label/create');
        }
    }
}

function white_label_edit($id) {
    require_role('SUPER_ADMIN');
    $client = get_white_label_by_id($id);
    if (!$client) {
        redirect('white_label/index');
    }
    // Changed 'client' to 'wl' to match view expectation
    view('forms/white_label_form', ['wl' => $client]);
}

function white_label_update($id) {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $client = get_white_label_by_id($id);

        $data = [
            'id' => $id,
            'company_name' => trim($_POST['company_name']),
            'primary_domain' => trim($_POST['primary_domain']),
            'logo_url' => $client['logo_url'], // Keep old logo by default
            'primary_color' => $_POST['primary_color'],
            'secondary_color' => $_POST['secondary_color'],
            'support_email' => trim($_POST['support_email']),
            'status' => $_POST['status']
        ];

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/logos/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES['logo']['name']);
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $file_name)) {
                $data['logo_url'] = 'uploads/logos/' . $file_name;
            }
        }

        if (update_white_label($data)) {
            flash('wl_success', 'White Label Client Updated');
            redirect('white_label/index');
        } else {
            flash('wl_error', 'Failed to update client', 'alert alert-danger');
            redirect('white_label/edit/' . $id);
        }
    }
}

function white_label_delete($id) {
    require_role('SUPER_ADMIN');
    if (delete_white_label($id)) {
        flash('wl_success', 'White Label Client Deleted');
    } else {
        flash('wl_error', 'Failed to delete client', 'alert alert-danger');
    }
    redirect('white_label/index');
}

// --- Subscription Management ---

function white_label_subscription($id) {
    require_role('SUPER_ADMIN');

    $client = get_white_label_by_id($id);
    if (!$client) {
        redirect('white_label/index');
    }

    $active_sub = get_white_label_subscription($id);
    $plans = get_all_subscription_plans('WHITE_LABEL');

    view('white_label/subscription', [
        'client' => $client,
        'active_sub' => $active_sub,
        'plans' => $plans
    ]);
}

function white_label_subscription_store($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'white_label_id' => $id,
            'plan_id' => $_POST['plan_id'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'amount' => $_POST['amount'],
            'due_amount' => $_POST['due_amount'],
            'payment_status' => $_POST['payment_status']
        ];

        if (assign_white_label_subscription($data)) {
            flash('wl_success', 'Subscription Assigned Successfully');
        } else {
            flash('wl_error', 'Failed to assign subscription', 'alert alert-danger');
        }

        redirect('white_label/index');
    }
}
