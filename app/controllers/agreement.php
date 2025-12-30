<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/user.php';

function agreement_index() {
    require_login();

    // Ensure user is a partner
    $user = find_user_by_id($_SESSION['user_id']);
    if (empty($user['partner_id'])) {
        redirect('dashboard/index'); // Not a partner, send to their dashboard
    }

    // Fetch full partner details
    $partner = get_partner_by_id($user['partner_id']);

    // If already accepted, redirect away
    if (!empty($partner['agreement_accepted_at'])) {
        redirect('dashboard/partner');
    }

    view('agreement/index', ['partner' => $partner]);
}

function agreement_accept() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = find_user_by_id($_SESSION['user_id']);
        if (!empty($user['partner_id'])) {
            if (accept_agreement($user['partner_id'])) {

                // Send confirmation email
                $partner = get_partner_by_id($user['partner_id']);
                $email_body = "<p>Hello <b>" . $partner['profile']['full_name'] . "</b>,</p>";
                $email_body .= "<p>Thank you for accepting the Business Partnership Agreement with <b>" . SITE_NAME . "</b>.</p>";
                $email_body .= "<p>Your acceptance was recorded on: <b>" . date('jS F Y, H:i:s') . "</b>.</p>";
                $email_body .= "<div class='info-box'>";
                $email_body .= "<p>A copy of the agreement can be downloaded from your partner dashboard at any time.</p>";
                $email_body .= "</div>";
                $email_body .= "<p>We are excited to have you on board!</p>";

                send_email($partner['profile']['email'], 'Agreement Accepted - Welcome to ' . SITE_NAME, $email_body);

                redirect('dashboard/partner');
            } else {
                die('Failed to accept agreement. Please try again.');
            }
        }
    }
    redirect('agreement/index');
}

function agreement_download() {
    require_login();

    $user = find_user_by_id($_SESSION['user_id']);
    if (empty($user['partner_id'])) {
        die('Access Denied.');
    }

    $partner = get_partner_by_id($user['partner_id']);

    // To render the HTML of the view into a variable
    ob_start();
    view('agreement/pdf', ['partner' => $partner]);
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream("IncomeKaro_Agreement_" . $partner['id'] . ".pdf", ["Attachment" => false]);
}
