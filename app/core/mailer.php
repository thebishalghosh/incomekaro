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
