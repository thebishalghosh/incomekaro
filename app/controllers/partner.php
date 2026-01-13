<?php
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/subscription.php';
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/core/mailer.php'; // Include Mailer
require_once APP_PATH . '/models/notification.php'; // Include Notification Model

function partner_index() {
    require_login();

    $partners = [];
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $partners = get_all_partners_for_admin();
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $db = get_db_connection();
        // Added JOIN to fetch user_id and wallet_balance
        $sql = "SELECT p.*, pp.full_name, pp.mobile, pp.email, pp.profile_image, wl.company_name as white_label_name,
                u.id as user_id, u.wallet_balance,
                (SELECT COUNT(*) FROM user_bank_details WHERE user_id = u.id) as has_bank_details,
                ubd.account_holder_name, ubd.bank_name, ubd.account_number, ubd.ifsc_code
                FROM partners p
                LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
                LEFT JOIN white_label_clients wl ON p.white_label_id = wl.id
                LEFT JOIN users u ON u.partner_id = p.id
                LEFT JOIN user_bank_details ubd ON u.id = ubd.user_id
                WHERE p.white_label_id = :wl_id
                ORDER BY p.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['wl_id' => $user['white_label_id']]);
        $partners = $stmt->fetchAll();

    } elseif ($_SESSION['role_code'] === 'RM') {
        $partners = get_partners_by_rm($_SESSION['user_id']);
    } else {
        die('Access Denied');
    }

    view('dashboard/partners_list', ['partners' => $partners]);
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
                'payment_amount' => $_POST['payment_amount'] ?? 0,
                'due_amount' => $_POST['due_amount'] ?? 0,
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

        if (create_full_partner($data)) {
            if (!empty($_POST['rm_id']) && $_SESSION['role_code'] === 'SUPER_ADMIN') {
                assign_rm_to_partner($data['id'], $_POST['rm_id']);
            }

            // Send Welcome Email
            $to = $data['profile']['email'];
            $subject = "Welcome to " . get_site_name() . " - Your Login Credentials";
            $login_url = url('/'); // Or specific login page

            $message = "
                <h3>Welcome, {$data['profile']['full_name']}!</h3>
                <p>Your partner account has been successfully created.</p>
                <p><strong>Login Details:</strong></p>
                <ul>
                    <li><strong>Email:</strong> {$data['profile']['email']}</li>
                    <li><strong>Password:</strong> {$raw_password}</li>
                </ul>
                <p><a href='{$login_url}'>Click here to Login</a></p>
                <p>Please change your password after your first login.</p>
                <br>
                <p>Best Regards,<br>" . get_site_name() . " Team</p>
            ";

            send_email($to, $subject, $message);

            flash('ptr_success', 'Partner Created Successfully. Email sent.');
            redirect('partner/index');
        } else {
            flash('ptr_error', 'Failed to create partner', 'alert alert-danger');
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
