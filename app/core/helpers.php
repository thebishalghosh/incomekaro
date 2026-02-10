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
    // Check if path is already a full URL
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . URL_ROOT . '/' . ltrim($path, '/'));
    }
    exit;
}

function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

function url($path) {
    // Ensure URL_ROOT doesn't have a trailing slash
    $root = rtrim(URL_ROOT, '/');
    return $root . '/' . ltrim($path, '/');
}

function asset($path) {
    $root = rtrim(URL_ROOT, '/');
    return $root . '/' . ltrim($path, '/');
}

// Flash Message Helper - Updated for Toast Style
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

            // Toast HTML Structure
            echo '
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050; margin-top: 80px;">
                <div class="toast show align-items-center text-white ' . (strpos($class, 'danger') !== false ? 'bg-danger' : 'bg-success') . ' border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ' . $_SESSION[$name] . '
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Wait a bit to ensure Bootstrap is loaded if it is deferred
                    setTimeout(function() {
                        if (typeof bootstrap !== "undefined") {
                            var toastElList = [].slice.call(document.querySelectorAll(".toast"))
                            var toastList = toastElList.map(function(toastEl) {
                                return new bootstrap.Toast(toastEl)
                            })
                            // Auto-hide after 5 seconds
                            setTimeout(function() {
                                toastList.forEach(toast => toast.hide());
                            }, 5000);
                        }
                    }, 500);
                });
            </script>
            ';

            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function get_email_template($content, $branding = null) {
    // If the content itself is a full HTML page (starts with <!DOCTYPE or <html>), return it as is.
    if (stripos(trim($content), '<!DOCTYPE') === 0 || stripos(trim($content), '<html') === 0) {
        return $content;
    }

    // Default Branding
    $logo_url = asset('images/logo.png'); // Ensure this is an absolute URL in production
    $site_name = SITE_NAME;
    $year = date('Y');
    $url_root = URL_ROOT;
    $primary_color = '#6A5ACD'; // SlateBlue
    $accent_color = '#E6E6FA'; // Lavender
    $bg_color = '#F8F9FD';

    // Override with Custom Branding if provided
    if ($branding) {
        if (!empty($branding['site_name'])) $site_name = $branding['site_name'];
        if (!empty($branding['logo_url'])) $logo_url = $branding['logo_url'];
        if (!empty($branding['primary_color'])) $primary_color = $branding['primary_color'];
        if (!empty($branding['url_root'])) $url_root = $branding['url_root'];
        // Add other overrides as needed
    }

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
function send_email($to, $subject, $body, $is_html = true, $headers = []) {
    // Check if the body is already a full HTML document
    if ($is_html && (stripos(trim($body), '<!DOCTYPE') === 0 || stripos(trim($body), '<html') === 0 || stripos(trim($body), '<div style=') === 0)) {
        // Do not wrap in default template
    } else if ($is_html) {
        // Extract branding if available
        $branding = isset($headers['branding']) ? $headers['branding'] : null;
        $body = get_email_template($body, $branding);
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

            // SSL/TLS settings (Auto-detect based on port)
            if ($mail->Port == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($mail->Port == 587) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Recipients
            $from_email = !empty($headers['from_email']) ? $headers['from_email'] : (getenv('SMTP_FROM_EMAIL') ?: 'noreply@incomekaro.in');
            $from_name = !empty($headers['from_name']) ? $headers['from_name'] : (getenv('SMTP_FROM_NAME') ?: 'IncomeKaro');

            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to);

            // CC
            if (!empty($headers['cc'])) {
                if (is_array($headers['cc'])) {
                    foreach ($headers['cc'] as $cc_email) {
                        $mail->addCC($cc_email);
                    }
                } else {
                    $mail->addCC($headers['cc']);
                }
            }

            // BCC
            if (!empty($headers['bcc'])) {
                if (is_array($headers['bcc'])) {
                    foreach ($headers['bcc'] as $bcc_email) {
                        $mail->addBCC($bcc_email);
                    }
                } else {
                    $mail->addBCC($headers['bcc']);
                }
            }

            // Reply-To
            if (!empty($headers['reply_to'])) {
                $mail->addReplyTo($headers['reply_to']);
            }

            // Content
            $mail->isHTML($is_html);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // Strip tags for plain text version
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log error to a file for debugging
            $log_file = APP_ROOT . '/debug_mail_error.txt';
            $log_entry = date('Y-m-d H:i:s') . " - Error sending to $to: " . $mail->ErrorInfo . "\n";
            file_put_contents($log_file, $log_entry, FILE_APPEND);

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

// Helper function to get dynamic company details
function get_company_details() {
    global $WL_CONFIG;

    $details = [
        'name' => 'IncomeKaro',
        'address' => 'Astra Tower, Unit No. ASO-303, 3rd Floor, Astra Tower, New Town, North 24 Parganas – 700161, West Bengal, India', // Default
        'email' => 'support@incomekaro.in',
        'logo' => asset('images/logo.png'),
        'signatory_name' => 'Pratap Mondal',
        'signatory_designation' => 'CEO',
        'signature_url' => asset('images/PratapMondal.png') // Default signature
    ];

    if (defined('IS_WHITE_LABEL') && IS_WHITE_LABEL && $WL_CONFIG) {
        $details['name'] = $WL_CONFIG['company_name'];
        $details['email'] = $WL_CONFIG['support_email'];
        if (!empty($WL_CONFIG['logo_url'])) {
            $details['logo'] = asset($WL_CONFIG['logo_url']);
        }

        // Check landing page data for address override
        $landing = !empty($WL_CONFIG['landing_page_data']) ? json_decode($WL_CONFIG['landing_page_data'], true) : [];
        if (!empty($landing['contact_address'])) {
            $details['address'] = $landing['contact_address'];
        }

        // Signature Details
        if (!empty($WL_CONFIG['signatory_name'])) {
            $details['signatory_name'] = $WL_CONFIG['signatory_name'];
        } else {
            $details['signatory_name'] = 'Authorized Signatory';
        }

        if (!empty($WL_CONFIG['signatory_designation'])) {
            $details['signatory_designation'] = $WL_CONFIG['signatory_designation'];
        } else {
            $details['signatory_designation'] = '';
        }

        if (!empty($WL_CONFIG['signature_url'])) {
            $details['signature_url'] = asset($WL_CONFIG['signature_url']);
        } else {
            $details['signature_url'] = null; // No signature uploaded
        }
    }

    return $details;
}

// Helper to get base64 image if needed (DomPDF handles URLs if isRemoteEnabled=true, but base64 is safer for local files)
function get_image_src($path_or_url) {
    // If it's a URL (http), return as is (assuming isRemoteEnabled=true)
    if (strpos($path_or_url, 'http') === 0) {
        return $path_or_url;
    }
    // If local path, convert to base64
    if (file_exists($path_or_url)) {
        $type = pathinfo($path_or_url, PATHINFO_EXTENSION);
        $data = file_get_contents($path_or_url);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    return '';
}

// Notification Helpers
function get_my_notifications($limit = 5) {
    if (!isLoggedIn()) return [];
    require_once APP_PATH . '/models/notification.php';
    return get_user_notifications($_SESSION['user_id'], $limit);
}

function get_my_unread_count() {
    if (!isLoggedIn()) return 0;
    require_once APP_PATH . '/models/notification.php';
    return get_unread_count($_SESSION['user_id']);
}
