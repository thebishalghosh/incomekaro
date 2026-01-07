<?php
require_once APP_PATH . '/core/mailer.php';

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

        // Send Email to Admin
        $admin_email = 'support@incomekaro.in'; // Replace with config value later
        $email_subject = "New Contact Inquiry: " . $subject;
        $email_body = "
            <h3>New Contact Message</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong><br>$message</p>
        ";

        if (send_email($admin_email, $email_subject, $email_body)) {
            flash('contact_success', 'Thank you! Your message has been sent successfully.');
        } else {
            flash('contact_error', 'Failed to send message. Please try again later.', 'alert alert-danger');
        }

        redirect('contact/index');
    }
}
