<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../app/core/config.php';
require_once '../app/core/mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<h1>SMTP Test Script</h1>";

$to = 'contact@incomekaro.org'; // Target email
$subject = 'Test Email from IncomeKaro Server';
$body = 'This is a test email to verify SMTP settings. Time: ' . date('Y-m-d H:i:s');

echo "Attempting to send email to: <strong>$to</strong><br><br>";

// Manual PHPMailer setup for verbose debug output
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = !empty(getenv('SMTP_USER'));
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->Port       = getenv('SMTP_PORT');

        // SSL/TLS settings (Auto-detect based on port)
        if ($mail->Port == 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($mail->Port == 587) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // Recipients
        $mail->setFrom(getenv('SMTP_FROM_EMAIL'), getenv('SMTP_FROM_NAME'));
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "<h2 style='color:green'>Message has been sent successfully!</h2>";
    } catch (Exception $e) {
        echo "<h2 style='color:red'>Message could not be sent.</h2>";
        echo "<strong>Mailer Error:</strong> " . $mail->ErrorInfo;
    }
} else {
    echo "<h2 style='color:red'>PHPMailer class not found!</h2>";
}

echo "<hr><h3>Current Config:</h3>";
echo "Host: " . getenv('SMTP_HOST') . "<br>";
echo "Port: " . getenv('SMTP_PORT') . "<br>";
echo "User: " . getenv('SMTP_USER') . "<br>";
?>
