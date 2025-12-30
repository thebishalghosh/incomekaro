<?php
require_once APP_PATH . '/models/user.php';

function auth_login() {
    // Redirect to home page where the modal is
    redirect('');
}

function auth_login_post() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize inputs manually since FILTER_SANITIZE_STRING is deprecated
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password']; // Don't sanitize password, just trim

        $data = [
            'email' => trim($email),
            'password' => trim($password),
            'email_err' => '',
            'password_err' => ''
        ];

        // Validate Email
        if (empty($data['email'])) {
            $data['email_err'] = 'Please enter email';
        }

        // Validate Password
        if (empty($data['password'])) {
            $data['password_err'] = 'Please enter password';
        }

        // Check for user/email
        if (empty($data['email_err']) && empty($data['password_err'])) {
            $user = find_user_by_email($data['email']);

            if ($user) {
                // User found, check password
                if (password_verify($data['password'], $user['password_hash'])) {
                    // Password correct, start session
                    auth_login_session($user);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    flash('login_error', 'Password incorrect', 'alert alert-danger');
                    redirect(''); // Redirect back to home to show error
                }
            } else {
                $data['email_err'] = 'No user found';
                flash('login_error', 'No user found with that email', 'alert alert-danger');
                redirect('');
            }
        } else {
            flash('login_error', 'Please fill in all fields', 'alert alert-danger');
            redirect('');
        }
    } else {
        redirect('');
    }
}

function auth_logout() {
    auth_logout_session();
}

// Temporary function to seed a Super Admin
function auth_seed() {
    $db = get_db_connection();

    // 1. Create Roles if not exist
    $db->exec("INSERT IGNORE INTO roles (id, code, name) VALUES (1, 'SUPER_ADMIN', 'Super Admin')");
    $db->exec("INSERT IGNORE INTO roles (id, code, name) VALUES (2, 'RM', 'Relationship Manager')");

    // 2. Create Super Admin User
    // Password: password123
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $id = 'admin-' . time(); // Simple ID for now

    $data = [
        'id' => $id,
        'role_id' => 1,
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'admin@incomekaro.in',
        'password_hash' => $password
    ];

    // Check if admin exists
    $existing = find_user_by_email($data['email']);
    if (!$existing) {
        if (create_user($data)) {
            echo 'Super Admin created. Email: admin@incomekaro.in, Pass: password123';
        } else {
            echo 'Failed to create admin.';
        }
    } else {
        echo 'Super Admin already exists.';
    }
}
