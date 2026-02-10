<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/models/white_label.php'; // Include White Label Model
require_once APP_PATH . '/core/mailer.php'; // Ensure mailer is included

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

    // Prepare Company Details
    $company_details = get_company_details();

    view('agreement/index', ['partner' => $partner, 'company' => $company_details]);
}

function agreement_accept() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = find_user_by_id($_SESSION['user_id']);
        if (!empty($user['partner_id'])) {
            if (accept_agreement($user['partner_id'])) {

                // Send confirmation email
                $partner = get_partner_by_id($user['partner_id']);

                // Determine Branding & Headers
                $company_name = get_site_name();
                $email_headers = [];

                if (!empty($partner['white_label_id'])) {
                    $wl = get_white_label_by_id($partner['white_label_id']);
                    if ($wl) {
                        $company_name = $wl['company_name'];

                        // Set Custom Headers for White Label
                        $email_headers['from_name'] = $wl['company_name'];
                        $email_headers['reply_to'] = $wl['support_email'];
                        $email_headers['cc'] = $wl['support_email'];

                        // Pass branding for the template
                        $email_headers['branding'] = [
                            'site_name' => $wl['company_name'],
                            'logo_url' => asset($wl['logo_url']),
                            'primary_color' => $wl['primary_color'],
                            'url_root' => 'http://' . $wl['primary_domain']
                        ];
                    }
                }

                $email_body = "<p>Hello <b>" . $partner['profile']['full_name'] . "</b>,</p>";
                $email_body .= "<p>Thank you for accepting the Business Partnership Agreement with <b>" . $company_name . "</b>.</p>";
                $email_body .= "<p>Your acceptance was recorded on: <b>" . date('jS F Y, H:i:s') . "</b>.</p>";
                $email_body .= "<div class='info-box'>";
                $email_body .= "<p>A copy of the agreement can be downloaded from your partner dashboard at any time.</p>";
                $email_body .= "</div>";
                $email_body .= "<p>We are excited to have you on board!</p>";

                send_email($partner['profile']['email'], 'Agreement Accepted - Welcome to ' . $company_name, $email_body, true, $email_headers);

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
    $company_details = get_company_details();

    // To render the HTML of the view into a variable
    ob_start();
    view('agreement/pdf', ['partner' => $partner, 'company' => $company_details]);
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream("Agreement_" . $partner['id'] . ".pdf", ["Attachment" => false]);
}
