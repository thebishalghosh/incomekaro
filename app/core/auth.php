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
    if ($_SESSION['role_code'] !== $role_code) {
        die('Access Denied');
    }
}
