<?php

function auth_login() {
    view('auth/login');
}

function auth_login_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('auth/login');
        return;
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    $user = find_user_by_email($email);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        flash('login_error', 'Invalid email or password.', 'alert alert-danger');
        redirect('');
        return;
    }

    // Block inactive users
    if (isset($user['status']) && $user['status'] === 'inactive') {
        redirect('auth/suspended');
        return;
    }

    // Successful login: establish session and redirect by role
    auth_login_session($user);
}

function auth_logout() {
    auth_logout_session();
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

            // Use site name as default sender name; actual sender email assigned after WL overrides
            $email_headers['from_name'] = $site_name;

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

                    // Set Custom Headers for White Label (name and reply-to will be finalized below)
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

            // Finalize sender headers. The 'From' email MUST be the one authorized by the SMTP provider.
            // We comment out the line that was overriding it, but we keep the Reply-To header.
            // $email_headers['from_email'] = $support_email;
            if (empty($email_headers['reply_to'])) {
                $email_headers['reply_to'] = $support_email;
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
                    <p style='font-size:13px; color:#555; word-break:break-all; margin-top:14px;'>Or copy/paste this link into your browser:<br><a href='{$reset_link}' style='color: {$primary_color};'>{$reset_link}</a></p>
                </div>
                <div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #888;'>
                    <p style='margin: 0;'>&copy; " . date('Y') . " {$site_name}. All rights reserved.</p>
                    <p style='margin: 5px 0 0 0;'>Need help? Contact us at <a href='mailto:{$support_email}' style='color: {$primary_color};'>{$support_email}</a></p>
                </div>
            </div>
            ";

            // Save the exact HTML that will be sent for debugging
            try {
                file_put_contents(APP_ROOT . '/last_reset_email.html', $message);
            } catch (Exception $e) {
                error_log('Unable to write last_reset_email.html: ' . $e->getMessage());
            }

            // Pass headers to send_email and log any failure for troubleshooting
            $email_sent = send_email($email, $subject, $message, true, $email_headers);
            if (!$email_sent) {
                error_log("Forgot-password email failed to send to {$email}.");
            }
        }

        // Always show success message to prevent email enumeration
        flash('login_success', 'If an account exists with that email, a reset link has been sent.', 'alert alert-success');
        redirect('auth/forgot_password');
        return;
    }

    // If accessed via GET (or any non-POST), redirect user to the forgot-password form
    redirect('auth/forgot_password');
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
