<?php
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/models/white_label.php'; // Needed to verify WL context
require_once APP_PATH . '/core/mailer.php';

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

// --- Forgot Password Logic ---

function auth_forgot_password() {
    view('auth/forgot_password');
}

function auth_send_reset_link() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $user = find_user_by_email($email);

        if ($user) {
            // Generate Token
            $token = bin2hex(random_bytes(32));
            $token_hash = password_hash($token, PASSWORD_DEFAULT);

            // Save to DB
            $db = get_db_connection();

            // Delete old tokens for this email
            $stmt = $db->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute(['email' => $email]);

            // Insert new token
            $stmt = $db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())");
            $stmt->execute(['email' => $email, 'token' => $token_hash]);

            // Determine Branding
            $site_name = get_site_name();
            $logo_url = get_logo_url();
            $primary_color = get_primary_color();
            $support_email = 'support@incomekaro.in';
            $base_url = url('/'); // Default base URL
            $email_headers = []; // Initialize headers array

            if (!empty($user['white_label_id'])) {
                $wl = get_white_label_by_id($user['white_label_id']);
                if ($wl) {
                    $site_name = $wl['company_name'];
                    if (!empty($wl['logo_url'])) $logo_url = asset($wl['logo_url']);
                    if (!empty($wl['primary_color'])) $primary_color = $wl['primary_color'];
                    if (!empty($wl['support_email'])) $support_email = $wl['support_email'];

                    // Use WL domain for the link if available
                    if (!empty($wl['primary_domain'])) {
                        $base_url = "https://" . $wl['primary_domain']; // Use https for WL domain
                    }

                    // Set Custom Headers for White Label
                    $email_headers['from_name'] = $wl['company_name'];
                    $email_headers['reply_to'] = $support_email; // Use resolved support email (fallback handled above)

                    // Pass branding for the template
                    $email_headers['branding'] = [
                        'site_name' => $wl['company_name'],
                        'logo_url' => !empty($wl['logo_url']) ? asset($wl['logo_url']) : asset('images/logo.png'),
                        'primary_color' => $wl['primary_color'],
                        'url_root' => $base_url
                    ];
                }
            }

            // Send Email
            // Construct the full URL manually to ensure it points to the correct domain
            $reset_link = rtrim($base_url, '/') . '/auth/reset_password?token=' . $token . '&email=' . urlencode($email);

            $subject = "Reset Your Password - " . $site_name;
            $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: {$primary_color}; padding: 20px; text-align: center;'>
                    <img src='{$logo_url}' alt='{$site_name}' style='max-height: 50px; background: white; padding: 5px; border-radius: 4px;'>
                </div>
                <div style='padding: 30px; background-color: #ffffff;'>
                    <h2 style='color: #333; margin-top: 0;'>Password Reset Request</h2>
                    <p style='color: #555; line-height: 1.6;'>We received a request to reset your password for your <strong>{$site_name}</strong> account.</p>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$reset_link}' style='background-color: {$primary_color}; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Reset Password</a>
                    </div>

                    <p style='color: #777; font-size: 14px;'>If you did not request a password reset, please ignore this email. This link will expire in 60 minutes.</p>
                </div>
                <div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #888;'>
                    <p style='margin: 0;'>&copy; " . date('Y') . " {$site_name}. All rights reserved.</p>
                    <p style='margin: 5px 0 0 0;'>Need help? Contact us at <a href='mailto:{$support_email}' style='color: {$primary_color};'>{$support_email}</a></p>
                </div>
            </div>
            ";

            // Pass headers to send_email
            send_email($email, $subject, $message, true, $email_headers);
        }

        // Always show success message to prevent email enumeration
        flash('login_success', 'If an account exists with that email, a reset link has been sent.', 'alert alert-success');
        redirect('auth/forgot_password');
    }
}

function auth_reset_password() {
    $token = $_GET['token'] ?? '';
    $email = $_GET['email'] ?? '';

    if (empty($token) || empty($email)) {
        flash('login_error', 'Invalid password reset link.', 'alert alert-danger');
        redirect('auth/forgot_password');
    }

    view('auth/reset_password', ['token' => $token, 'email' => $email]);
}

function auth_update_password() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['token'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            flash('login_error', 'Passwords do not match.', 'alert alert-danger');
            redirect('auth/reset_password?token=' . $token . '&email=' . urlencode($email));
        }

        $db = get_db_connection();

        // Verify Token
        $stmt = $db->prepare("SELECT token, created_at FROM password_resets WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $record = $stmt->fetch();

        if ($record && password_verify($token, $record['token'])) {
            // Check Expiration (1 hour)
            $created_at = strtotime($record['created_at']);
            if (time() - $created_at > 3600) {
                flash('login_error', 'Reset link has expired.', 'alert alert-danger');
                redirect('auth/forgot_password');
            }

            // Update Password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password_hash = :hash WHERE email = :email");
            $update->execute(['hash' => $password_hash, 'email' => $email]);

            // Delete Token
            $delete = $db->prepare("DELETE FROM password_resets WHERE email = :email");
            $delete->execute(['email' => $email]);

            flash('login_success', 'Password updated successfully. Please login.', 'alert alert-success');
            redirect('/');
        } else {
            flash('login_error', 'Invalid or expired reset link.', 'alert alert-danger');
            redirect('auth/forgot_password');
        }
    }
}
