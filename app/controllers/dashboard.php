<?php
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/partner.php'; // Include Partner Model
require_once APP_PATH . '/models/user.php';    // Include User Model

function dashboard_index() {
    require_login();

    // Redirect based on role
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        redirect('dashboard/super_admin');
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        redirect('dashboard/white_label');
    } elseif ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        redirect('dashboard/partner');
    } else {
        // Fallback for other roles (RM, Sales Exec, etc.)
        view('dashboard/home', ['title' => 'Dashboard', 'message' => 'Welcome to your dashboard']);
    }
}

function dashboard_super_admin() {
    require_role('SUPER_ADMIN');
    view('dashboard/super_admin');
}

function dashboard_white_label() {
    require_login();
    view('dashboard/white_label');
}

function dashboard_partner() {
    require_login();
    require_agreement(); // Enforce agreement acceptance
    require_kyc_verification(); // Enforce KYC verification

    // Get the logged-in user's partner_id
    $user = find_user_by_id($_SESSION['user_id']);

    if (!$user || empty($user['partner_id'])) {
        die('Error: User is not linked to a partner account.');
    }

    // Fetch full partner details
    $partner = get_partner_by_id($user['partner_id']);

    view('dashboard/partner_home', ['partner' => $partner]);
}
