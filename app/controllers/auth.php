<?php
require_once APP_PATH . '/models/user.php';

function auth_login() {
    // If already logged in, redirect to dashboard
    if (isset($_SESSION['user_id'])) {
        redirect('dashboard/index');
    }

    // Instead of showing a standalone login page, redirect to home with a flag to open modal
    // Or just redirect to home and let user click login
    redirect('/?login=true');
}

function auth_login_post() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        $user = find_user_by_email($email);

        if ($user && password_verify($password, $user['password_hash'])) {

            // Check Status
            if ($user['status'] !== 'active') {
                flash('login_error', 'Your account is inactive or suspended.', 'alert alert-danger');
                redirect('/?login=true'); // Redirect to home with error
                return;
            }

            // Set Session
            auth_login_session($user); // Use helper function

        } else {
            flash('login_error', 'Invalid email or password.', 'alert alert-danger');
            redirect('/?login=true'); // Redirect to home with error
        }
    }
}

function auth_logout() {
    auth_logout_session(); // Use helper function
}

function auth_suspended() {
    view('errors/suspended');
}
