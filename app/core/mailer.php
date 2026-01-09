<?php
// send_email is already defined in helpers.php, so we don't redefine it here.

function send_welcome_email($user_data, $password) {
    $subject = "Welcome to " . SITE_NAME . " - Your Login Credentials";

    $message = "
    <html>
    <head>
        <title>Welcome to " . SITE_NAME . "</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #6A5ACD;'>Welcome, " . $user_data['first_name'] . "!</h2>
            <p>Your account has been successfully created on <strong>" . SITE_NAME . "</strong>.</p>
            <p>You can now log in to your dashboard using the credentials below:</p>

            <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p style='margin: 5px 0;'><strong>Login URL:</strong> <a href='" . URL_ROOT . "'>" . URL_ROOT . "</a></p>
                <p style='margin: 5px 0;'><strong>Email:</strong> " . $user_data['email'] . "</p>
                <p style='margin: 5px 0;'><strong>Password:</strong> " . $password . "</p>
            </div>

            <p>Please log in and change your password immediately for security.</p>

            <p>Best Regards,<br>" . SITE_NAME . " Team</p>
        </div>
    </body>
    </html>
    ";

    return send_email($user_data['email'], $subject, $message);
}

function send_whitelabel_welcome_email($client_data, $password) {
    $subject = "Welcome to " . SITE_NAME . " - Your White Label Platform is Ready";
    $login_url = "http://" . $client_data['primary_domain']; // Assuming http for local/dev, use https in prod

    $message = "
    <html>
    <head>
        <title>Welcome to " . SITE_NAME . "</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #6A5ACD;'>Welcome, " . $client_data['company_name'] . "!</h2>
            <p>Your White Label platform has been successfully provisioned.</p>

            <div style='background-color: #f0fdf4; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;'>
                <h3 style='margin-top: 0; color: #28a745;'>Your Platform Details</h3>
                <p style='margin: 5px 0;'><strong>Domain:</strong> <a href='" . $login_url . "'>" . $client_data['primary_domain'] . "</a></p>
                <p style='margin: 5px 0;'><strong>Admin Email:</strong> " . $client_data['support_email'] . "</p>
                <p style='margin: 5px 0;'><strong>Password:</strong> " . $password . "</p>
            </div>

            <p>You can now log in to your dedicated dashboard to manage your partners and settings.</p>

            <p>Best Regards,<br>" . SITE_NAME . " Team</p>
        </div>
    </body>
    </html>
    ";

    return send_email($client_data['support_email'], $subject, $message);
}

function send_admin_notification_email($client_data, $password = null) {
    // In a real app, fetch Super Admin email from DB or Config
    $admin_email = 'admin@incomekaro.in';
    $subject = "New White Label Client Created: " . $client_data['company_name'];

    $password_html = $password ? "<li><strong>Password:</strong> " . $password . "</li>" : "";

    $message = "
    <html>
    <head>
        <title>New Client Notification</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #333;'>New White Label Client</h2>
            <p>A new White Label client has been onboarded.</p>

            <ul style='background-color: #f9f9f9; padding: 15px; border-radius: 5px;'>
                <li><strong>Company:</strong> " . $client_data['company_name'] . "</li>
                <li><strong>Domain:</strong> " . $client_data['primary_domain'] . "</li>
                <li><strong>Email:</strong> " . $client_data['support_email'] . "</li>
                $password_html
                <li><strong>Status:</strong> " . $client_data['status'] . "</li>
            </ul>

            <p><a href='" . URL_ROOT . "/white_label/index'>View in Dashboard</a></p>
        </div>
    </body>
    </html>
    ";

    return send_email($admin_email, $subject, $message);
}
