<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function view($view_path, $data = []) {
    extract($data);
    $full_path = APP_PATH . '/views/' . $view_path . '.php';
    if (file_exists($full_path)) {
        require_once $full_path;
    } else {
        die("View '$view_path' not found.");
    }
}

function redirect($path) {
    header('Location: ' . URL_ROOT . '/' . $path);
    exit;
}

function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

function url($path) {
    return URL_ROOT . '/' . ltrim($path, '/');
}

function asset($path) {
    return URL_ROOT . '/' . ltrim($path, '/');
}

// Flash Message Helper
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            if (!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            if (!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function get_email_template($content) {
    $logo_url = asset('images/logo.png'); // Ensure this is an absolute URL in production
    $site_name = SITE_NAME;
    $year = date('Y');
    $url_root = URL_ROOT;

    // Lavender Theme Colors
    $primary_color = '#6A5ACD'; // SlateBlue
    $accent_color = '#E6E6FA'; // Lavender
    $bg_color = '#F8F9FD';

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: $bg_color; margin: 0; padding: 0; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: $primary_color; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .content { padding: 40px; color: #333333; line-height: 1.6; }
        .footer { background-color: $accent_color; padding: 20px; text-align: center; color: #666666; font-size: 12px; }
        .btn { display: inline-block; background-color: $primary_color; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .info-box { background-color: $bg_color; border-left: 4px solid $primary_color; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div style="padding: 40px 0;">
        <div class="email-container">
            <div class="header">
                <h1>$site_name</h1>
            </div>
            <div class="content">
                $content
            </div>
            <div class="footer">
                &copy; $year $site_name. All rights reserved.<br>
                <a href="$url_root" style="color: $primary_color; text-decoration: none;">Visit Website</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

// Email Function using PHPMailer
function send_email($to, $subject, $body, $is_html = true) {
    if ($is_html) {
        $body = get_email_template($body);
    }

    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST') ?: 'localhost';
            $mail->SMTPAuth   = !empty(getenv('SMTP_USER'));
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');
            $mail->Port       = getenv('SMTP_PORT') ?: 1025;

            // Recipients
            $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'noreply@incomekaro.in', getenv('SMTP_FROM_NAME') ?: 'IncomeKaro');
            $mail->addAddress($to);

            // Content
            $mail->isHTML($is_html);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // Strip tags for plain text version
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    } else {
        // Fallback to logging
        $log_file = APP_ROOT . '/email_log.txt';
        $log_entry = "To: $to\nSubject: $subject\nDate: " . date('Y-m-d H:i:s') . "\nBody:\n$body\n-------------------\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
        return true;
    }
}
