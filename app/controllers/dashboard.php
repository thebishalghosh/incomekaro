<?php
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/partner.php'; // Include Partner Model
require_once APP_PATH . '/models/user.php';    // Include User Model
require_once APP_PATH . '/models/dashboard.php'; // Include Dashboard Model

function dashboard_index() {
    require_login();

    // Redirect based on role
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        redirect('dashboard/super_admin');
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        redirect('dashboard/white_label');
    } elseif ($_SESSION['role_code'] === 'PARTNER_ADMIN') {
        redirect('dashboard/partner');
    } elseif ($_SESSION['role_code'] === 'RM') {
        redirect('rm/index');
    } elseif ($_SESSION['role_code'] === 'SALES_EXEC') {
        redirect('sales/index');
    } else {
        // Fallback for other roles
        view('dashboard/home', ['title' => 'Dashboard', 'message' => 'Welcome to your dashboard']);
    }
}

function dashboard_super_admin() {
    require_role('SUPER_ADMIN');

    $stats = get_super_admin_stats();

    view('dashboard/super_admin', ['stats' => $stats]);
}

function dashboard_white_label() {
    require_login();

    // Ensure user is linked to a white label
    $user = find_user_by_id($_SESSION['user_id']);
    if (empty($user['white_label_id'])) {
        die('Error: User is not linked to a White Label account.');
    }

    $stats = get_white_label_stats($user['white_label_id']);
    $subscription = get_white_label_subscription($user['white_label_id']); // Fetch active sub

    // Pass user to view for wallet balance
    view('dashboard/white_label', ['stats' => $stats, 'subscription' => $subscription, 'user' => $user]);
}

function dashboard_partner() {
    require_login();
    require_agreement();
    require_kyc_verification();

    $user = find_user_by_id($_SESSION['user_id']);

    if (!$user || empty($user['partner_id'])) {
        die('Error: User is not linked to a partner account.');
    }

    $partner = get_partner_by_id($user['partner_id']);
    $stats = get_partner_stats($user['partner_id']);

    // Fetch RM Name if assigned
    $rm_name = null;
    if (!empty($partner['rm_id'])) {
        $rm = find_user_by_id($partner['rm_id']);
        if ($rm) {
            $rm_name = $rm['first_name'] . ' ' . $rm['last_name'];
        }
    }

    // Pass user to view to access wallet_balance
    view('dashboard/partner_home', ['partner' => $partner, 'stats' => $stats, 'user' => $user, 'rm_name' => $rm_name]);
}
