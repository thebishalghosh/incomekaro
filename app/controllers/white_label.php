<?php
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/core/mailer.php'; // Include Mailer

function white_label_index() {
    require_role('SUPER_ADMIN');
    $white_labels = get_all_white_labels();
    view('dashboard/white_labels_list', ['white_labels' => $white_labels]);
}

function white_label_create() {
    require_role('SUPER_ADMIN');
    view('forms/white_label_form');
}

function white_label_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
            'id' => uniqid('wl-'),
            'company_name' => trim($_POST['company_name']),
            'primary_domain' => trim($_POST['primary_domain']),
            'primary_color' => trim($_POST['primary_color']),
            'secondary_color' => trim($_POST['secondary_color']),
            'support_email' => trim($_POST['support_email']),
            'status' => trim($_POST['status']),
            'logo_url' => ''
        ];

        // Handle File Upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = time() . '_' . basename($_FILES['logo']['name']);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                $data['logo_url'] = 'uploads/logos/' . $file_name;
            }
        }

        // Generate a random password or use default
        $password = 'password123'; // You can use bin2hex(random_bytes(4)) for random

        if (create_white_label($data, $password)) {
            // Send Emails
            send_whitelabel_welcome_email($data, $password);
            send_admin_notification_email($data, $password); // Pass password here

            flash('wl_success', 'White Label Client Added and Emails Sent');
            redirect('white_label/index');
        } else {
            die('Something went wrong');
        }
    }
}

function white_label_edit($id) {
    require_role('SUPER_ADMIN');
    $wl = get_white_label_by_id($id);
    if (!$wl) {
        redirect('white_label/index');
    }
    view('forms/white_label_form', ['wl' => $wl]);
}

function white_label_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $wl = get_white_label_by_id($id);

        $data = [
            'id' => $id,
            'company_name' => trim($_POST['company_name']),
            'primary_domain' => trim($_POST['primary_domain']),
            'primary_color' => trim($_POST['primary_color']),
            'secondary_color' => trim($_POST['secondary_color']),
            'support_email' => trim($_POST['support_email']),
            'status' => trim($_POST['status']),
            'logo_url' => $wl['logo_url'] // Keep old logo by default
        ];

        // Handle File Upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = time() . '_' . basename($_FILES['logo']['name']);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                $data['logo_url'] = 'uploads/logos/' . $file_name;
            }
        }

        if (update_white_label($data)) {
            flash('wl_success', 'White Label Client Updated');
            redirect('white_label/index');
        } else {
            die('Something went wrong');
        }
    }
}

function white_label_delete($id) {
    require_role('SUPER_ADMIN');
    if (delete_white_label($id)) {
        flash('wl_success', 'White Label Client Deleted');
    } else {
        flash('wl_error', 'Could not delete client. It may have related data.', 'alert alert-danger');
    }
    redirect('white_label/index');
}
