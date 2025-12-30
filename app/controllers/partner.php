<?php
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/models/subscription.php'; // Include Subscription Model

function partner_index() {
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $partners = get_all_partners_for_admin();
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        // We need to implement this function
        // $partners = get_partners_by_white_label($_SESSION['user_id']);
        $partners = [];
    } else {
        $partners = [];
    }

    view('dashboard/partners_list', ['partners' => $partners]);
}

function partner_profile($id) {
    $partner = get_partner_by_id($id);
    if (!$partner) {
        redirect('partner/index');
    }

    // Authorization check
    if ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        // If WL, check ownership
        $db = get_db_connection();
        $u = $db->query("SELECT white_label_id FROM users WHERE id = '" . $_SESSION['user_id'] . "'")->fetch();
        if ($partner['white_label_id'] !== $u['white_label_id']) {
            die('Access Denied');
        }
    }

    // Fetch available RMs
    $rms = get_users_by_role('RM');

    view('dashboard/partner_profile', ['partner' => $partner, 'rms' => $rms]);
}

function partner_assign_rm($partner_id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $rm_id = $_POST['rm_id'];
        if (assign_rm_to_partner($partner_id, $rm_id)) {
            flash('ptr_success', 'RM Assigned Successfully');
        } else {
            flash('ptr_error', 'Failed to assign RM', 'alert alert-danger');
        }
        redirect('partner/profile/' . $partner_id);
    }
}

function partner_verify_kyc($partner_id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validation: Check if documents exist before verifying
        $documents = get_partner_documents($partner_id);
        if (empty($documents)) {
            flash('ptr_error', 'Cannot verify KYC. No documents have been uploaded.', 'alert alert-danger');
            redirect('partner/profile/' . $partner_id);
            return;
        }

        $status = $_POST['status']; // VERIFIED or REJECTED
        if (update_kyc_status($partner_id, $status)) {
            flash('ptr_success', 'KYC Status Updated');
        } else {
            flash('ptr_error', 'Failed to update KYC status', 'alert alert-danger');
        }
        redirect('partner/profile/' . $partner_id);
    }
}

function partner_create() {
    $white_labels = [];
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $white_labels = get_all_white_labels();
    }

    // Fetch Active Subscription Plans
    $plans = get_all_subscription_plans();

    view('forms/partner_form', ['white_labels' => $white_labels, 'plans' => $plans]);
}

function partner_store() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $partner_id = uniqid('ptr-');

        $partner_type = 'PLATFORM';
        $white_label_id = null;

        if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
            if (isset($_POST['partner_type']) && $_POST['partner_type'] === 'WHITE_LABEL') {
                $partner_type = 'WHITE_LABEL';
                $white_label_id = $_POST['white_label_id'];
            }
        } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
            $partner_type = 'WHITE_LABEL';
            $db = get_db_connection();
            $u = $db->query("SELECT white_label_id FROM users WHERE id = '" . $_SESSION['user_id'] . "'")->fetch();
            $white_label_id = $u['white_label_id'];
        }

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

        $dob = trim($_POST['dob']);
        $password_plain = !empty($dob) ? $dob : 'password123';
        $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

        // Get Plan Name if ID is provided
        $plan_name = '';
        if (!empty($_POST['plan_id'])) {
            $plan = get_subscription_plan_by_id($_POST['plan_id']);
            if ($plan) {
                $plan_name = $plan['name'];
            }
        }

        $data = [
            'id' => $partner_id,
            'white_label_id' => $white_label_id,
            'partner_type' => $partner_type,
            'status' => 'active',
            'user_password_hash' => $password_hash,
            'created_by' => $_SESSION['user_id'],

            'profile' => [
                'full_name' => trim($_POST['full_name']),
                'mobile' => trim($_POST['mobile']),
                'email' => trim($_POST['email']),
                'whatsapp' => trim($_POST['whatsapp']),
                'dob' => $dob,
                'gender' => trim($_POST['gender']),
                'profile_image' => $profile_image
            ],

            'subscription' => [
                'plan_name' => $plan_name,
                'payment_amount' => trim($_POST['payment_amount']),
                'due_amount' => trim($_POST['due_amount']),
                'payment_mode' => trim($_POST['payment_mode']),
                'transaction_id' => trim($_POST['transaction_id'])
            ],

            'address_permanent' => [
                'address' => trim($_POST['perm_address']),
                'state' => trim($_POST['perm_state']),
                'city' => trim($_POST['perm_city']),
                'pincode' => trim($_POST['perm_pincode'])
            ],

            'address_office' => [
                'address' => isset($_POST['same_as_perm']) ? trim($_POST['perm_address']) : trim($_POST['office_address']),
                'state' => isset($_POST['same_as_perm']) ? trim($_POST['perm_state']) : trim($_POST['office_state']),
                'city' => isset($_POST['same_as_perm']) ? trim($_POST['perm_city']) : trim($_POST['office_city']),
                'pincode' => isset($_POST['same_as_perm']) ? trim($_POST['perm_pincode']) : trim($_POST['office_pincode'])
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
            ]
        ];

        if (create_full_partner($data)) {
            $email_body = "<p>Hello <b>" . $data['profile']['full_name'] . "</b>,</p>";
            $email_body .= "<p>Welcome to <b>" . SITE_NAME . "</b>. Your account has been created successfully.</p>";
            $email_body .= "<div class='info-box'>";
            $email_body .= "<h3 style='margin-top:0;'>Login Details</h3>";
            $email_body .= "<p><b>URL:</b> <a href='" . URL_ROOT . "'>" . URL_ROOT . "</a></p>";
            $email_body .= "<p><b>Email:</b> " . $data['profile']['email'] . "</p>";
            $email_body .= "<p><b>Password:</b> " . $password_plain . "</p>";
            $email_body .= "</div>";
            $email_body .= "<p>Please change your password after logging in.</p>";
            $email_body .= "<a href='" . URL_ROOT . "' class='btn'>Login Now</a>";

            send_email($data['profile']['email'], 'Welcome to ' . SITE_NAME, $email_body);

            flash('ptr_success', 'Partner Added and User Created. Email Sent.');
            redirect('partner/index');
        } else {
            flash('ptr_error', 'Failed to add partner', 'alert alert-danger');
            redirect('partner/create');
        }
    }
}

function partner_edit($id) {
    $partner = get_partner_by_id($id);
    if (!$partner) {
        redirect('partner/index');
    }

    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $db = get_db_connection();
        $u = $db->query("SELECT white_label_id FROM users WHERE id = '" . $_SESSION['user_id'] . "'")->fetch();
        if ($partner['white_label_id'] !== $u['white_label_id']) {
            die('Access Denied');
        }
    }

    $white_labels = [];
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $white_labels = get_all_white_labels();
    }

    // Fetch Active Subscription Plans
    $plans = get_all_subscription_plans();

    view('forms/partner_form', ['partner' => $partner, 'white_labels' => $white_labels, 'plans' => $plans]);
}

function partner_update($id) {
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

        // Get Plan Name if ID is provided
        $plan_name = '';
        if (!empty($_POST['plan_id'])) {
            $plan = get_subscription_plan_by_id($_POST['plan_id']);
            if ($plan) {
                $plan_name = $plan['name'];
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
                'dob' => trim($_POST['dob']),
                'gender' => trim($_POST['gender']),
                'profile_image' => $profile_image
            ],

            'subscription' => [
                'plan_name' => $plan_name,
                'payment_amount' => trim($_POST['payment_amount']),
                'due_amount' => trim($_POST['due_amount']),
                'payment_mode' => trim($_POST['payment_mode']),
                'transaction_id' => trim($_POST['transaction_id'])
            ],

            'address_permanent' => [
                'address' => trim($_POST['perm_address']),
                'state' => trim($_POST['perm_state']),
                'city' => trim($_POST['perm_city']),
                'pincode' => trim($_POST['perm_pincode'])
            ],

            'address_office' => [
                'address' => isset($_POST['same_as_perm']) ? trim($_POST['perm_address']) : trim($_POST['office_address']),
                'state' => isset($_POST['same_as_perm']) ? trim($_POST['perm_state']) : trim($_POST['office_state']),
                'city' => isset($_POST['same_as_perm']) ? trim($_POST['perm_city']) : trim($_POST['office_city']),
                'pincode' => isset($_POST['same_as_perm']) ? trim($_POST['perm_pincode']) : trim($_POST['office_pincode'])
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
            ]
        ];

        if (update_full_partner($data)) {
            flash('ptr_success', 'Partner Updated Successfully');
            redirect('partner/index');
        } else {
            flash('ptr_error', 'Failed to update partner', 'alert alert-danger');
            redirect('partner/edit/' . $id);
        }
    }
}

function partner_delete($id) {
    if ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        $db = get_db_connection();
        $partner = get_partner_by_id($id);
        $u = $db->query("SELECT white_label_id FROM users WHERE id = '" . $_SESSION['user_id'] . "'")->fetch();
        if ($partner['white_label_id'] !== $u['white_label_id']) {
            die('Access Denied');
        }
    }

    if (delete_partner($id)) {
        flash('ptr_success', 'Partner Deleted');
    } else {
        flash('ptr_error', 'Could not delete partner.', 'alert alert-danger');
    }
    redirect('partner/index');
}
