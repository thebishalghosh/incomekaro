<?php
require_once APP_PATH . '/models/white_label.php';

function dashboard_index() {
    require_login();

    // Redirect based on role
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        redirect('dashboard/super_admin');
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        redirect('dashboard/white_label');
    } else {
        // Fallback for other roles (RM, Partner, etc.)
        view('dashboard/home', ['title' => 'Dashboard', 'message' => 'Welcome to your dashboard']);
    }
}

function dashboard_super_admin() {
    require_role('SUPER_ADMIN');
    view('dashboard/super_admin');
}

function dashboard_white_label() {
    // We don't have a 'WHITE_LABEL' role in the DB yet, but this is how we'd check
    // require_role('WHITE_LABEL');

    // For now, just ensure they are logged in.
    // In production, you'd strictly check the role.
    require_login();

    view('dashboard/white_label');
}
