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

function application_edit($id) {
    require_login();

    $application = get_application_by_id($id);
    if (!$application) {
        flash('app_error', 'Application not found.', 'alert alert-danger');
        redirect('application/index');
    }

    // Security Check
    if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        $user = find_user_by_id($_SESSION['user_id']);
        if ($application['partner_id'] !== $user['partner_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        die('Access Denied');
    }

    $service = get_service_by_id($application['service_id']);

    // Reuse the show_form logic but pass the application data
    application_show_form($service, $application);
}

function application_update($id) {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Security Check (Fetch app first)
        $current_app = get_application_by_id($id);
        if (!$current_app) {
            flash('app_error', 'Application not found.', 'alert alert-danger');
            redirect('application/index');
        }

        if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
            $user = find_user_by_id($_SESSION['user_id']);
            if ($current_app['partner_id'] !== $user['partner_id']) {
                die('Access Denied');
            }
        } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
            die('Access Denied');
        }

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
            'id' => $id,
            'created_by' => $_SESSION['user_id'], // For document upload tracking
            'customer' => $_POST['customer'],
            'meta' => $_POST['meta'],
            'documents' => $documents
        ];

        if (update_full_application($data)) {
            // Log the update with current status
            $role_name = ($_SESSION['role_code'] === 'SUPER_ADMIN') ? 'Admin' : 'Partner';
            $log_message = "Application details updated by $role_name. (Current Status: " . str_replace('_', ' ', $current_app['status']) . ")";
            add_application_comment($id, $_SESSION['user_id'], $log_message);

            flash('app_success', 'Application updated successfully.');
            redirect('application/view/' . $id);
        } else {
            flash('app_error', 'Failed to update application.', 'alert alert-danger');
            redirect('application/edit/' . $id);
        }
    }
}

function application_update_status($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        $comment = trim($_POST['comment'] ?? '');

        if (update_application_status($id, $status)) {
            $log_message = "Status updated to " . str_replace('_', ' ', $status);
            if (!empty($comment)) {
                $log_message .= "\nNote: " . $comment;
            }
            add_application_comment($id, $_SESSION['user_id'], $log_message);

            flash('app_success', 'Application status updated to ' . ucfirst($status));
        } else {
            flash('app_error', 'Failed to update status.', 'alert alert-danger');
        }
        redirect('application/view/' . $id);
    }
}

function application_add_comment($id) {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $comment = trim($_POST['comment']);
        if (!empty($comment)) {
            if (add_application_comment($id, $_SESSION['user_id'], $comment)) {
                flash('app_success', 'Comment added.');
            } else {
                flash('app_error', 'Failed to add comment.', 'alert alert-danger');
            }
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

    if ($service['form_type'] === 'TAX_FORM' || $service['form_type'] === 'INSURANCE_FORM' || $service['form_type'] === 'CREDIT_CARD_FORM') {
        redirect('application/list_service/' . $service_id);
        return;
    }

    application_show_form($service);
}

function application_list_service($service_id) {
    require_login();
    $user = find_user_by_id($_SESSION['user_id']);
    $service = get_service_by_id($service_id);

    $applications = get_partner_applications_by_service($user['partner_id'], $service_id);

    view('application/service_list', [
        'service' => $service,
        'applications' => $applications
    ]);
}

function application_new($service_id) {
    require_login();
    $service = get_service_by_id($service_id);
    application_show_form($service);
}

function application_show_form($service, $application = null) {
    $data = ['service' => $service];
    if ($application) {
        $data['application'] = $application;
    }

    switch ($service['form_type']) {
        case 'GOVT_LOAN':
            view('application/form_govt', $data);
            break;
        case 'PRIVATE_LOAN':
            view('application/form_private', $data);
            break;
        case 'TAX_FORM':
            view('application/form_tax', $data);
            break;
        case 'INSURANCE_FORM':
            view('application/form_insurance', $data);
            break;
        case 'CREDIT_CARD_FORM':
            view('application/form_credit_card', $data);
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

        $status = empty($documents) ? 'DOCUMENTS_PENDING' : 'FRESH';

        $data = [
            'id' => 'app-' . uniqid(),
            'white_label_id' => $partner['white_label_id'],
            'partner_id' => $partner['id'],
            'service_id' => $_POST['service_id'],
            'created_by' => $_SESSION['user_id'],
            'customer' => $_POST['customer'],
            'meta' => $_POST['meta'],
            'documents' => $documents,
            'status' => $status
        ];

        if (create_full_application($data)) {
            add_application_comment($data['id'], $_SESSION['user_id'], "Application submitted with status: " . str_replace('_', ' ', $status));
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
