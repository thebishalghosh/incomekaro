<?php
require_once APP_PATH . '/models/service.php';
require_once APP_PATH . '/models/application.php';
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/notification.php'; // Include Notification Model

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
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $applications = get_applications_by_white_label($user['white_label_id']);
        view('dashboard/applications_list', ['applications' => $applications]);
    } elseif ($_SESSION['role_code'] === 'RM') {
        // RM Logic
        require_once APP_PATH . '/models/rm.php'; // Ensure RM model is loaded if needed, or use direct query
        // Using the query logic from previous RM controller update
        $db = get_db_connection();
        $sql = "SELECT sa.*, s.name as service_name,
                COALESCE(pp.full_name, p.name) as partner_full_name,
                p.id as partner_id,
                wl.company_name as white_label_name
                FROM service_applications sa
                JOIN services s ON sa.service_id = s.id
                JOIN partners p ON sa.partner_id = p.id
                LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
                LEFT JOIN white_label_clients wl ON sa.white_label_id = wl.id
                WHERE p.rm_id = :rm_id
                ORDER BY sa.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['rm_id' => $_SESSION['user_id']]);
        $applications = $stmt->fetchAll();
        view('dashboard/applications_list', ['applications' => $applications]);
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

    // Fetch White Label Colors if applicable
    $wl_colors = null;
    if (!empty($application['white_label_id'])) {
        $wl = get_white_label_by_id($application['white_label_id']);
        if ($wl) {
            $wl_colors = [
                'primary' => $wl['primary_color'],
                'secondary' => $wl['secondary_color']
            ];
        }
    }

    view('dashboard/application_view', ['application' => $application, 'wl_colors' => $wl_colors]);
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
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL' && $_SESSION['role_code'] !== 'RM') {
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
        } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL' && $_SESSION['role_code'] !== 'RM') {
            die('Access Denied');
        }

        $documents = [];
        $upload_dir = APP_ROOT . '/public/uploads/applications/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (isset($_FILES['docs']) && is_array($_FILES['docs']['name'])) {
            foreach ($_FILES['docs']['name'] as $key => $name) {
                // Ensure we have a valid file upload
                if (!empty($name) && $_FILES['docs']['error'][$key] === 0) {
                    $tmp_name = $_FILES['docs']['tmp_name'][$key];

                    // Verify tmp_name is a string (not array)
                    if (is_string($tmp_name) && is_uploaded_file($tmp_name)) {
                        $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                        $file_name = time() . '_' . uniqid() . '.' . $file_ext;
                        $target_file = $upload_dir . $file_name;

                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $documents[] = [
                                'type' => strtoupper($key),
                                'url' => 'uploads/applications/' . $file_name
                            ];
                        }
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
            $role_name = $_SESSION['role_code'];
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
    require_login(); // Changed from require_role('SUPER_ADMIN') to allow RM/WL

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        $comment = trim($_POST['comment'] ?? '');

        // Fetch current app to check if status changed
        $current_app = get_application_by_id($id);
        $status_changed = ($current_app['status'] !== $status);

        // Update Status if changed
        if ($status_changed) {
            // Security Check for Status Update: Only Super Admin & RM
            if ($_SESSION['role_code'] === 'SUPER_ADMIN' || $_SESSION['role_code'] === 'RM') {
                if (update_application_status($id, $status)) {
                    $log_message = "Status updated to " . str_replace('_', ' ', $status);
                    add_application_comment($id, $_SESSION['user_id'], $log_message);

                    // Notify Partner
                    $db = get_db_connection();
                    $stmt = $db->prepare("SELECT id FROM users WHERE partner_id = :pid AND role_id = (SELECT id FROM roles WHERE code = 'PARTNER_ADMIN') LIMIT 1");
                    $stmt->execute(['pid' => $current_app['partner_id']]);
                    $partner_user_id = $stmt->fetchColumn();

                    if ($partner_user_id) {
                        create_notification(
                            $partner_user_id,
                            'Application Status Update',
                            "Your application for {$current_app['customer_name']} has been updated to " . ucfirst(str_replace('_', ' ', $status)) . ".",
                            url('application/view/' . $id)
                        );
                    }
                    flash('app_success', 'Status Updated');
                } else {
                    flash('app_error', 'Failed to update status.', 'alert alert-danger');
                }
            } else {
                // WL Admin tried to change status (should be prevented by UI, but good to check)
                // But wait, the form submits status even if hidden.
                // If WL Admin submits, status will be same as current (from hidden input).
                // So $status_changed will be false.
            }
        }

        // Add Comment if provided
        if (!empty($comment)) {
            if (add_application_comment($id, $_SESSION['user_id'], $comment)) {
                if (!$status_changed) {
                    flash('app_success', 'Comment added.');
                }
            } else {
                flash('app_error', 'Failed to add comment.', 'alert alert-danger');
            }
        }

        redirect('application/view/' . $id);
    }
}

function application_add_comment($id) {
    // Kept for backward compatibility or direct API calls
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

    // Special handling for Instant Panel
    if ($parent_service['category'] === 'INSTANT_PANEL') {
        $db = get_db_connection();
        $sql = "SELECT DISTINCT panel_type FROM services WHERE parent_id = :parent_id AND panel_type IS NOT NULL";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':parent_id', $parent_id);
        $stmt->execute();
        $panel_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

        view('application/select_panel_type', ['parent_service' => $parent_service, 'panel_types' => $panel_types]);
        return;
    }

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

        if (isset($_FILES['docs']) && is_array($_FILES['docs']['name'])) {
            foreach ($_FILES['docs']['name'] as $key => $name) {
                if (!empty($name) && $_FILES['docs']['error'][$key] === 0) {
                    $tmp_name = $_FILES['docs']['tmp_name'][$key];

                    // Verify tmp_name is a string (not array)
                    if (is_string($tmp_name) && is_uploaded_file($tmp_name)) {
                        $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                        $file_name = time() . '_' . uniqid() . '.' . $file_ext;
                        $target_file = $upload_dir . $file_name;

                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $documents[] = [
                                'type' => strtoupper($key),
                                'url' => 'uploads/applications/' . $file_name
                            ];
                        }
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

            // Notify RM
            if (!empty($partner['rm_id'])) {
                create_notification(
                    $partner['rm_id'],
                    'New Application',
                    "Partner {$partner['name']} submitted a new application for {$data['customer']['name']}.",
                    url('application/view/' . $data['id'])
                );
            }

            // Notify White Label Admin (if applicable)
            if (!empty($partner['white_label_id'])) {
                $db = get_db_connection();
                $stmt = $db->prepare("SELECT id FROM users WHERE white_label_id = :wl_id AND role_id = (SELECT id FROM roles WHERE code = 'WHITE_LABEL') LIMIT 1");
                $stmt->execute(['wl_id' => $partner['white_label_id']]);
                $wl_admin_id = $stmt->fetchColumn();

                if ($wl_admin_id) {
                    create_notification(
                        $wl_admin_id,
                        'New Application',
                        "Partner {$partner['name']} submitted a new application.",
                        url('application/view/' . $data['id'])
                    );
                }
            }

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
