<?php
require_once APP_PATH . '/core/mailer.php';
require_once APP_PATH . '/core/database.php';

function contact_index() {
    view('contact/index');
}

function contact_store() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

        if (empty($name) || empty($email) || empty($message)) {
            flash('contact_error', 'Please fill in all required fields.', 'alert alert-danger');
            redirect('contact/index');
        }

        // Determine White Label ID
        global $WL_CONFIG;
        $white_label_id = null;
        $admin_email = 'support@incomekaro.in'; // Default Super Admin Email

        if (defined('IS_WHITE_LABEL') && IS_WHITE_LABEL && isset($WL_CONFIG)) {
            $white_label_id = $WL_CONFIG['id'];
            $admin_email = $WL_CONFIG['support_email']; // Send to WL Admin
        }

        // Save to Database
        $db = get_db_connection();
        $sql = "INSERT INTO contact_inquiries (id, white_label_id, name, email, subject, message, status)
                VALUES (:id, :white_label_id, :name, :email, :subject, :message, 'new')";

        $stmt = $db->prepare($sql);
        $params = [
            'id' => uniqid('cnt-'),
            'white_label_id' => $white_label_id,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];

        if ($stmt->execute($params)) {
            // Send Email Notification
            $email_subject = "New Contact Inquiry: " . $subject;
            $email_body = "
                <h3>New Contact Message</h3>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong><br>$message</p>
                <hr>
                <p><small>This inquiry has been saved to your dashboard.</small></p>
            ";

            // We try to send email, but success depends on DB save primarily
            send_email($admin_email, $email_subject, $email_body);

            flash('contact_success', 'Thank you! Your message has been sent successfully.');
        } else {
            flash('contact_error', 'Failed to save message. Please try again later.', 'alert alert-danger');
        }

        redirect('contact/index');
    }
}
