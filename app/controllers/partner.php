<?php
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/subscription.php';
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/core/mailer.php'; // Include Mailer
require_once APP_PATH . '/models/notification.php'; // Include Notification Model

function partner_index() {
    require_login();

    // Handle AJAX Search & Pagination
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $type_filter = isset($_GET['type']) ? trim($_GET['type']) : '';
        $wl_filter = isset($_GET['wl']) ? trim($_GET['wl']) : '';
        $creator_id = null;

        // Role-based restrictions
        if ($_SESSION['role_code'] === 'WHITE_LABEL') {
            $user = find_user_by_id($_SESSION['user_id']);
            $wl_filter = $user['white_label_id']; // Force WL filter
            $type_filter = 'WHITE_LABEL'; // Implicitly WL partners
        } elseif ($_SESSION['role_code'] === 'RM') {
            // RM logic is usually handled inside model, but for generic search we might need to pass RM ID
            // For now, let's keep RM logic separate or adapt get_all_partners_for_admin to accept RM ID
            // But the request is specifically for Super Admin filters (Platform vs WL)
        } elseif ($_SESSION['role_code'] === 'SALES_EXEC') {
            $creator_id = $_SESSION['user_id'];
        }

        $partners = get_all_partners_for_admin($page, $limit, $search, $type_filter, $wl_filter, $creator_id);
        $total_partners = get_total_partners_count($search, $type_filter, $wl_filter, $creator_id);
        $total_pages = ceil($total_partners / $limit);

        // Return JSON
        header('Content-Type: application/json');
        echo json_encode([
            'partners' => $partners,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_partners
            ]
        ]);
        exit;
    }

    // Initial Load
    $white_labels = [];
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $white_labels = get_all_white_labels();
    }

    view('dashboard/partners_list', ['white_labels' => $white_labels]);
}

function partner_create() {
    require_login();

    $white_labels = [];
    $wl_id_filter = null;

    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $white_labels = get_all_white_labels();
        $wl_id_filter = 'GLOBAL';
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $wl_id_filter = $user['white_label_id'];
    }

    $plans = get_active_subscription_plans($wl_id_filter);
    $rms = get_users_by_role('RM');

    view('forms/partner_form', ['white_labels' => $white_labels, 'plans' => $plans, 'rms' => $rms]);
}

function partner_store() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $profile_image = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/partners/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/partners/' . $file_name;
            }
        }

        $white_label_id = null;
        $partner_type = 'PLATFORM';

        if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
            if (!empty($_POST['white_label_id'])) {
                $white_label_id = $_POST['white_label_id'];
                $partner_type = 'WHITE_LABEL';
            }
        } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
            $user = find_user_by_id($_SESSION['user_id']);
            $white_label_id = $user['white_label_id'];
            $partner_type = 'WHITE_LABEL';
        }

        $raw_password = $_POST['password']; // Keep raw password for email

        $data = [
            'id' => uniqid('ptr-'),
            'white_label_id' => $white_label_id,
            'partner_type' => $partner_type,
            'status' => 'active',
            'created_by' => $_SESSION['user_id'],

            'profile' => [
                'full_name' => trim($_POST['full_name']),
                'mobile' => trim($_POST['mobile']),
                'email' => trim($_POST['email']),
                'whatsapp' => trim($_POST['whatsapp']),
                'dob' => $_POST['dob'],
                'gender' => $_POST['gender'],
                'profile_image' => $profile_image
            ],

            'address_permanent' => [
                'address' => trim($_POST['perm_address']),
                'state' => trim($_POST['perm_state']),
                'city' => trim($_POST['perm_city']),
                'pincode' => trim($_POST['perm_pincode'])
            ],

            'address_office' => [
                'address' => trim($_POST['office_address']),
                'state' => trim($_POST['office_state']),
                'city' => trim($_POST['office_city']),
                'pincode' => trim($_POST['office_pincode'])
            ],

            'identity' => [
                'gst' => trim($_POST['gst']),
                'aadhaar' => trim($_POST['aadhaar']),
                'pan' => trim($_POST['pan'])
            ],

            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['branch'])
            ],

            'subscription' => [
                'plan_name' => $_POST['plan_name'] ?? '',
                'payment_amount' => !empty($_POST['payment_amount']) ? $_POST['payment_amount'] : 0,
                'due_amount' => !empty($_POST['due_amount']) ? $_POST['due_amount'] : 0,
                'payment_mode' => $_POST['payment_mode'] ?? 'Cash',
                'transaction_id' => $_POST['transaction_id'] ?? ''
            ],

            'user_password_hash' => password_hash($raw_password, PASSWORD_DEFAULT)
        ];

        if (!empty($_POST['plan_id'])) {
            $plan = get_subscription_plan_by_id($_POST['plan_id']);
            if ($plan) {
                $data['subscription']['plan_name'] = $plan['name'];
            }
        }

        $result = create_full_partner($data);
        if ($result === true) {
            if (!empty($_POST['rm_id']) && $_SESSION['role_code'] === 'SUPER_ADMIN') {
                assign_rm_to_partner($data['id'], $_POST['rm_id']);
            }

            // Send Welcome Email (Wrapped in Try-Catch)
            try {
                // Determine Branding
                $site_name = get_site_name(); // Default
                $login_url = url('/'); // Default
                $logo_url = get_logo_url(); // Default logo
                $support_email = 'support@incomekaro.in'; // Default support email
                $primary_color = '#0d6efd'; // Default primary color

                if (!empty($white_label_id)) {
                    $wl = get_white_label_by_id($white_label_id);
                    if ($wl) {
                        $site_name = $wl['company_name'];
                        $login_url = "http://" . $wl['primary_domain']; // Assuming http for dev
                        if (!empty($wl['logo_url'])) {
                            $logo_url = asset($wl['logo_url']);
                        }
                        if (!empty($wl['support_email'])) {
                            $support_email = $wl['support_email'];
                        }
                        if (!empty($wl['primary_color'])) {
                            $primary_color = $wl['primary_color'];
                        }
                    }
                }

                $to = $data['profile']['email'];
                $subject = "Welcome to " . $site_name . " - Your Login Credentials";

                // HTML Email Template
                $message = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: {$primary_color}; padding: 20px; text-align: center;'>
                        <img src='{$logo_url}' alt='{$site_name}' style='max-height: 50px; background: white; padding: 5px; border-radius: 4px;'>
                    </div>
                    <div style='padding: 30px; background-color: #ffffff;'>
                        <h2 style='color: #333; margin-top: 0;'>Welcome, {$data['profile']['full_name']}!</h2>
                        <p style='color: #555; line-height: 1.6;'>Your partner account has been successfully created on <strong>{$site_name}</strong>. We are excited to have you on board!</p>

                        <div style='background-color: #f8f9fa; border-left: 4px solid {$primary_color}; padding: 15px; margin: 20px 0;'>
                            <p style='margin: 0 0 10px 0;'><strong>Login Details:</strong></p>
                            <ul style='margin: 0; padding-left: 20px; color: #555;'>
                                <li style='margin-bottom: 5px;'><strong>Email:</strong> {$data['profile']['email']}</li>
                                <li><strong>Password:</strong> {$raw_password}</li>
                            </ul>
                        </div>

                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$login_url}' style='background-color: {$primary_color}; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Login to Dashboard</a>
                        </div>

                        <p style='color: #777; font-size: 14px;'>Please change your password after your first login for security purposes.</p>
                    </div>
                    <div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #888;'>
                        <p style='margin: 0;'>&copy; " . date('Y') . " {$site_name}. All rights reserved.</p>
                        <p style='margin: 5px 0 0 0;'>Need help? Contact us at <a href='mailto:{$support_email}' style='color: {$primary_color};'>{$support_email}</a></p>
                    </div>
                </div>
                ";

                // Send using the configured mailer
                // Note: send_email function signature might need to support HTML or headers if not already
                // Assuming send_email handles basic HTML content type
                send_email($to, $subject, $message);

            } catch (Exception $e) {
                error_log("Failed to send welcome email to partner: " . $e->getMessage());
            }

            flash('ptr_success', 'Partner Created Successfully. Email sent.');
            redirect('partner/index');
        } else {
            // Display the specific error message returned from the model
            $error_msg = is_string($result) ? $result : 'Failed to create partner';
            flash('ptr_error', $error_msg, 'alert alert-danger');
            redirect('partner/create');
        }
    }
}

function partner_edit($id) {
    require_login();

    $partner = get_partner_by_id($id);
    if (!$partner) {
        redirect('partner/index');
    }

    $white_labels = [];
    $wl_id_filter = null;

    if ($_SESSION['role_code'] === 'RM') {
        if ($partner['rm_id'] !== $_SESSION['user_id']) {
            die('Access Denied');
        }
        if ($partner['white_label_id']) {
            $wl_id_filter = $partner['white_label_id'];
        } else {
            $wl_id_filter = 'GLOBAL';
        }

    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        if ($partner['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
        $wl_id_filter = $user['white_label_id'];

    } elseif ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $white_labels = get_all_white_labels();
        if ($partner['white_label_id']) {
            $wl_id_filter = $partner['white_label_id'];
        } else {
            $wl_id_filter = 'GLOBAL';
        }
    } else {
        die('Access Denied');
    }

    $plans = get_active_subscription_plans($wl_id_filter);
    $rms = get_users_by_role('RM');

    view('forms/partner_form', ['partner' => $partner, 'white_labels' => $white_labels, 'plans' => $plans, 'rms' => $rms]);
}

function partner_update($id) {
    require_login();

    if ($_SESSION['role_code'] === 'RM') {
        $partner = get_partner_by_id($id);
        if (!$partner || $partner['rm_id'] !== $_SESSION['user_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $partner = get_partner_by_id($id);
        if (!$partner || $partner['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        die('Access Denied');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/partners/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/partners/' . $file_name;
            }
        }

        $data = [
            'id' => $id,
            'status' => 'active',

            'profile' => [
                'full_name' => trim($_POST['full_name']),
                'mobile' => trim($_POST['mobile']),
                'email' => trim($_POST['email']),
                'whatsapp' => trim($_POST['whatsapp']),
                'dob' => $_POST['dob'],
                'gender' => $_POST['gender'],
                'profile_image' => $profile_image
            ],

            'address_permanent' => [
                'address' => trim($_POST['perm_address']),
                'state' => trim($_POST['perm_state']),
                'city' => trim($_POST['perm_city']),
                'pincode' => trim($_POST['perm_pincode'])
            ],

            'address_office' => [
                'address' => trim($_POST['office_address']),
                'state' => trim($_POST['office_state']),
                'city' => trim($_POST['office_city']),
                'pincode' => trim($_POST['office_pincode'])
            ],

            'identity' => [
                'gst' => trim($_POST['gst']),
                'aadhaar' => trim($_POST['aadhaar']),
                'pan' => trim($_POST['pan'])
            ],

            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['branch'])
            ],

            'subscription' => [
                'plan_name' => $_POST['plan_name'] ?? '',
                'payment_amount' => $_POST['payment_amount'],
                'due_amount' => $_POST['due_amount'],
                'payment_mode' => $_POST['payment_mode'],
                'transaction_id' => $_POST['transaction_id']
            ]
        ];

        if (!empty($_POST['plan_id'])) {
            $plan = get_subscription_plan_by_id($_POST['plan_id']);
            if ($plan) {
                $data['subscription']['plan_name'] = $plan['name'];
            }
        }

        if (update_full_partner($data)) {
            if (isset($_POST['rm_id']) && $_SESSION['role_code'] === 'SUPER_ADMIN') {
                assign_rm_to_partner($id, $_POST['rm_id']);
            }

            flash('ptr_success', 'Partner Updated Successfully');

            if ($_SESSION['role_code'] === 'RM') {
                redirect('rm/partners');
            } else {
                redirect('partner/index');
            }
        } else {
            flash('ptr_error', 'Failed to update partner', 'alert alert-danger');
            redirect('partner/edit/' . $id);
        }
    }
}

function partner_update_status($id) {
    require_login();

    // Security Check
    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $partner = get_partner_by_id($id);
        if ($partner['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        die('Access Denied');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        if (update_partner_status($id, $status)) {
            flash('ptr_success', 'Partner status updated to ' . ucfirst($status));
        } else {
            flash('ptr_error', 'Failed to update status.', 'alert alert-danger');
        }
        redirect('partner/index');
    }
}

function partner_delete($id) {
    require_login();

    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $partner = get_partner_by_id($id);
        if ($partner['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        die('Access Denied');
    }

    if (delete_partner($id)) {
        flash('ptr_success', 'Partner Deleted');
    } else {
        flash('ptr_error', 'Failed to delete partner', 'alert alert-danger');
    }
    redirect('partner/index');
}

function partner_profile($id) {
    require_login();

    $partner = get_partner_by_id($id);
    if (!$partner) {
        flash('ptr_error', 'Partner not found.', 'alert alert-danger');
        redirect('dashboard/index');
    }

    if ($_SESSION['role_code'] === 'RM') {
        if ($partner['rm_id'] !== $_SESSION['user_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        if ($partner['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        die('Access Denied');
    }

    $rms = get_users_by_role('RM');
    view('dashboard/partner_profile', ['partner' => $partner, 'rms' => $rms]);
}

function partner_verify_kyc($id) {
    require_login();

    if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'RM' && $_SESSION['role_code'] !== 'WHITE_LABEL') {
        die('Access Denied');
    }

    if ($_SESSION['role_code'] === 'RM') {
        $partner = get_partner_by_id($id);
        if ($partner['rm_id'] !== $_SESSION['user_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $partner = get_partner_by_id($id);
        if ($partner['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        if (update_kyc_status($id, $status)) {

            // Notify Partner
            $db = get_db_connection();
            $stmt = $db->prepare("SELECT id FROM users WHERE partner_id = :pid AND role_id = (SELECT id FROM roles WHERE code = 'PARTNER_ADMIN') LIMIT 1");
            $stmt->execute(['pid' => $id]);
            $partner_user_id = $stmt->fetchColumn();

            if ($partner_user_id) {
                create_notification(
                    $partner_user_id,
                    'KYC Status Update',
                    "Your KYC status has been updated to " . $status . ".",
                    url('dashboard/partner')
                );
            }

            flash('ptr_success', 'KYC Status Updated');
        } else {
            flash('ptr_error', 'Failed to update KYC status', 'alert alert-danger');
        }
        redirect('partner/profile/' . $id);
    }
}

function partner_assign_rm($id) {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $rm_id = $_POST['rm_id'];
        if (assign_rm_to_partner($id, $rm_id)) {
            flash('ptr_success', 'RM Assigned Successfully');
        } else {
            flash('ptr_error', 'Failed to assign RM', 'alert alert-danger');
        }
        redirect('partner/profile/' . $id);
    }
}
