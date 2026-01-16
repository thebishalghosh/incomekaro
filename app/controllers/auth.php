<?php
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/models/white_label.php'; // Needed to verify WL context

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
                redirect('/');
                return;
            }

            // --- DOMAIN ISOLATION LOGIC ---
            $is_white_label_domain = defined('IS_WHITE_LABEL') && IS_WHITE_LABEL;
            global $WL_CONFIG;
            $current_wl_id = $is_white_label_domain ? $WL_CONFIG['id'] : null;

            // Fetch Role Code for clearer logic
            $db = get_db_connection();
            $stmt = $db->prepare("SELECT code FROM roles WHERE id = :id");
            $stmt->execute(['id' => $user['role_id']]);
            $role_code = $stmt->fetchColumn();

            // 1. Super Admin / RM / Sales Exec -> Main Site Only
            if (in_array($role_code, ['SUPER_ADMIN', 'RM', 'SALES_EXEC'])) {
                if ($is_white_label_domain) {
                    flash('login_error', 'Access Denied: Please login via the main portal.', 'alert alert-danger');
                    redirect('/');
                    return;
                }
            }

            // 2. White Label Admin -> Specific WL Domain Only
            if ($role_code === 'WHITE_LABEL') {
                if (!$is_white_label_domain || $user['white_label_id'] !== $current_wl_id) {
                    flash('login_error', 'Access Denied: Invalid domain for this account.', 'alert alert-danger');
                    redirect('/');
                    return;
                }
            }

            // 3. Partner -> Context Aware
            if ($role_code === 'PARTNER_ADMIN') {
                // Platform Partner (No WL ID) -> Main Site Only
                if (empty($user['white_label_id'])) {
                    if ($is_white_label_domain) {
                        flash('login_error', 'Access Denied: Please login via the main portal.', 'alert alert-danger');
                        redirect('/');
                        return;
                    }
                }
                // White Label Partner -> Specific WL Domain Only
                else {
                    if (!$is_white_label_domain || $user['white_label_id'] !== $current_wl_id) {
                        flash('login_error', 'Access Denied: Please login via your partner portal.', 'alert alert-danger');
                        redirect('/');
                        return;
                    }
                }
            }

            // --- END DOMAIN ISOLATION ---

            // Set Session
            auth_login_session($user); // Use helper function

        } else {
            flash('login_error', 'Invalid email or password.', 'alert alert-danger');
            redirect('/');
        }
    }
}

function auth_logout() {
    auth_logout_session(); // Use helper function
}

function auth_suspended() {
    view('errors/suspended');
}
