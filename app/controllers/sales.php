<?php
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/subscription.php';
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/core/mailer.php';

function sales_index() {
    require_role('SALES_EXEC');

    $user_id = $_SESSION['user_id'];
    $user = find_user_by_id($user_id); // Fetch user details for wallet
    $partners = get_partners_by_creator($user_id);

    $stats = [
        'total_partners' => count($partners),
        'this_month' => 0
    ];

    foreach ($partners as $p) {
        if (date('Y-m', strtotime($p['created_at'])) === date('Y-m')) {
            $stats['this_month']++;
        }
    }

    view('dashboard/sales_home', ['stats' => $stats, 'partners' => array_slice($partners, 0, 5), 'user' => $user]);
}

function sales_partners() {
    require_role('SALES_EXEC');
    $partners = get_partners_by_creator($_SESSION['user_id']);
    view('dashboard/partners_list', ['partners' => $partners]);
}

function sales_create_partner() {
    require_role('SALES_EXEC');

    // Sales Execs create Platform Partners (Global)
    $wl_id_filter = 'GLOBAL';
    $plans = get_active_subscription_plans($wl_id_filter);

    // Sales Exec cannot assign RM (Super Admin does that)
    $rms = [];

    view('forms/partner_form', ['plans' => $plans, 'rms' => $rms]);
}

function sales_store_partner() {
    require_role('SALES_EXEC');

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

        $raw_password = $_POST['password'];

        $data = [
            'id' => uniqid('ptr-'),
            'white_label_id' => null, // Platform Partner
            'partner_type' => 'PLATFORM',
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
            // Send Welcome Email
            $to = $data['profile']['email'];
            $subject = "Welcome to " . get_site_name() . " - Your Login Credentials";
            $login_url = url('/');

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

            flash('ptr_success', 'Partner Created Successfully.');
            redirect('sales/index');
        } else {
            flash('ptr_error', 'Failed to create partner', 'alert alert-danger');
            redirect('sales/create_partner');
        }
    }
}
