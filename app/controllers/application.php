<?php
require_once APP_PATH . '/models/service.php';
require_once APP_PATH . '/models/application.php';
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/models/partner.php';

function application_index() {
    require_login();

    $applications = [];

    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $applications = get_all_applications_for_admin();
        view('dashboard/applications_list', ['applications' => $applications]);
    } elseif ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        $user = find_user_by_id($_SESSION['user_id']);
        if (!empty($user['partner_id'])) {
            $applications = get_partner_applications($user['partner_id']);
            // We might want a different view for partners later, but for now reuse or create new
            view('dashboard/partner_applications_list', ['applications' => $applications]);
        } else {
            redirect('dashboard/index');
        }
    } else {
        // RM or other roles
        $applications = []; // Implement RM logic later
        view('dashboard/applications_list', ['applications' => $applications]);
    }
}

function application_view($id) {
    require_login();

    $application = get_application_by_id($id);
    if (!$application) {
        flash('app_error', 'Application not found.', 'alert alert-danger');
        redirect('application/index');
    }

    // Authorization Check
    if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        $user = find_user_by_id($_SESSION['user_id']);
        if ($application['partner_id'] !== $user['partner_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        // Add RM check here later
    }

    view('dashboard/application_view', ['application' => $application]);
}

function application_update_status($id) {
    require_role('SUPER_ADMIN'); // Or RM

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        if (update_application_status($id, $status)) {
            flash('app_success', 'Application status updated to ' . ucfirst($status));
        } else {
            flash('app_error', 'Failed to update status.', 'alert alert-danger');
        }
        redirect('application/view/' . $id);
    }
}

function application_select($parent_id) {
    require_login();
    require_agreement();
    require_kyc_verification();

    $parent_service = get_service_by_id($parent_id);
    $child_services = get_child_services($parent_id);

    if (empty($child_services)) {
        redirect('application/create/' . $parent_id);
    }

    view('application/select', [
        'parent_service' => $parent_service,
        'child_services' => $child_services
    ]);
}

function application_create($service_id) {
    require_login();
    require_agreement();
    require_kyc_verification();

    $service = get_service_by_id($service_id);
    if (!$service) {
        die('Service not found.');
    }

    switch ($service['form_type']) {
        case 'GOVT_LOAN':
            view('application/form_govt', ['service' => $service]);
            break;
        case 'PRIVATE_LOAN':
            view('application/form_private', ['service' => $service]);
            break;
        default:
            flash('app_error', 'This service does not have an application form.', 'alert alert-warning');
            redirect('dashboard/partner');
            break;
    }
}

function application_store() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // 1. Get User & Partner Info
        $user = find_user_by_id($_SESSION['user_id']);
        $partner = get_partner_by_id($user['partner_id']);

        // 2. Handle File Uploads
        $documents = [];
        $upload_dir = APP_ROOT . '/public/uploads/applications/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (isset($_FILES['docs'])) {
            foreach ($_FILES['docs']['name'] as $key => $name) {
                if (!empty($name) && $_FILES['docs']['error'][$key] === 0) {
                    $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                    $file_name = time() . '_' . uniqid() . '.' . $file_ext;
                    $target_file = $upload_dir . $file_name;

                    if (move_uploaded_file($_FILES['docs']['tmp_name'][$key], $target_file)) {
                        $documents[] = [
                            'type' => strtoupper($key),
                            'url' => 'uploads/applications/' . $file_name
                        ];
                    }
                }
            }
        }

        // 3. Prepare Data
        $data = [
            'id' => 'app-' . uniqid(),
            'white_label_id' => $partner['white_label_id'],
            'partner_id' => $partner['id'],
            'service_id' => $_POST['service_id'],
            'created_by' => $_SESSION['user_id'],
            'customer' => $_POST['customer'], // Array [first_name, last_name, email, phone]
            'meta' => $_POST['meta'],         // Array of all other fields
            'documents' => $documents
        ];

        // 4. Save to DB
        if (create_full_application($data)) {
            // Optional: Send Email Notification to Admin/Partner
            // send_application_email($data);

            redirect('application/success');
        } else {
            flash('app_error', 'Failed to submit application. Please try again.', 'alert alert-danger');
            redirect('application/create/' . $_POST['service_id']);
        }
    }
}

function application_success() {
    require_login();
    view('application/success');
}
