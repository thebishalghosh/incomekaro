<?php
// Auth Helper Functions

function auth_login_session($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['role_id'] = $user['role_id'];

    // Fetch role code for easier checking
    require_once APP_PATH . '/models/user.php';
    $role = get_user_role($user['role_id']);
    $_SESSION['role_code'] = $role['code'];

    // Redirect based on role
    if ($role['code'] === 'SUPER_ADMIN') {
        redirect('dashboard/super_admin');
    } elseif ($role['code'] === 'WHITE_LABEL') {
        redirect('dashboard/white_label');
    } else {
        redirect('dashboard/index');
    }
}

function auth_logout_session() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['role_id']);
    unset($_SESSION['role_code']);
    session_destroy();
    redirect(''); // Redirect to Home
}

function require_login() {
    if (!isLoggedIn()) {
        redirect(''); // Redirect to Home
    }
}

function require_role($role_code) {
    require_login();

    if (!isset($_SESSION['role_code']) || $_SESSION['role_code'] !== $role_code) {
        // If role code is missing or mismatch, redirect to home or show error
        // Ideally, redirect to their allowed dashboard if logged in
        if (isset($_SESSION['role_code'])) {
             if ($_SESSION['role_code'] === 'SUPER_ADMIN') redirect('dashboard/super_admin');
             elseif ($_SESSION['role_code'] === 'WHITE_LABEL') redirect('dashboard/white_label');
             else redirect('dashboard/index');
        }

        die('Access Denied');
    }
}

function require_agreement() {
    require_login();

    if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        $user = find_user_by_id($_SESSION['user_id']);
        if (empty($user['partner_id'])) return; // Not a partner, skip check

        $partner = get_partner_by_id($user['partner_id']);
        if (empty($partner['agreement_accepted_at'])) {
            redirect('agreement/index');
        }
    }
}

function require_kyc_verification() {
    require_login();

    if ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        $user = find_user_by_id($_SESSION['user_id']);
        if (empty($user['partner_id'])) return;

        $partner = get_partner_by_id($user['partner_id']);
        if ($partner['kyc_status'] === 'PENDING') {
            // Allow access to the KYC page itself
            if (strpos($_GET['url'], 'kyc') === false) {
                redirect('kyc/index');
            }
        }
    }
}
