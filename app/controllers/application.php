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
            view('dashboard/partner_applications_list', ['applications' => $applications]);
        } else {
            redirect('dashboard/index');
        }
    } else {
        $applications = [];
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

    if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        $user = find_user_by_id($_SESSION['user_id']);
        if ($application['partner_id'] !== $user['partner_id']) {
            die('Access Denied');
        }
    }

    view('dashboard/application_view', ['application' => $application]);
}

function application_update_status($id) {
    require_role('SUPER_ADMIN');

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

// This function decides whether to show a list or a form based on the service type
function application_create($service_id) {
    require_login();
    require_agreement();
    require_kyc_verification();

    $service = get_service_by_id($service_id);
    if (!$service) {
        die('Service not found.');
    }

    // If it's a TAX form, show the list of existing applications first
    if ($service['form_type'] === 'TAX_FORM') {
        redirect('application/list_service/' . $service_id);
        return;
    }

    // For Loans, go directly to the form (as per previous logic)
    application_show_form($service);
}

// New function to show the list of applications for a specific service
function application_list_service($service_id) {
    require_login();
    $user = find_user_by_id($_SESSION['user_id']);
    $service = get_service_by_id($service_id);

    // Fetch applications only for this specific service and partner
    $applications = get_partner_applications_by_service($user['partner_id'], $service_id);

    view('application/service_list', [
        'service' => $service,
        'applications' => $applications
    ]);
}

// New function to explicitly show the form (used by the "Add New" button)
function application_new($service_id) {
    require_login();
    $service = get_service_by_id($service_id);
    application_show_form($service);
}

// Helper to load the correct view
function application_show_form($service) {
    switch ($service['form_type']) {
        case 'GOVT_LOAN':
            view('application/form_govt', ['service' => $service]);
            break;
        case 'PRIVATE_LOAN':
            view('application/form_private', ['service' => $service]);
            break;
        case 'TAX_FORM':
            view('application/form_tax', ['service' => $service]);
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
        $user = find_user_by_id($_SESSION['user_id']);
        $partner = get_partner_by_id($user['partner_id']);

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

        $data = [
            'id' => 'app-' . uniqid(),
            'white_label_id' => $partner['white_label_id'],
            'partner_id' => $partner['id'],
            'service_id' => $_POST['service_id'],
            'created_by' => $_SESSION['user_id'],
            'customer' => $_POST['customer'],
            'meta' => $_POST['meta'],
            'documents' => $documents
        ];

        if (create_full_application($data)) {
            // Redirect back to the service list if it was a Tax form, otherwise success page
            // For now, let's stick to the success page for consistency
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
