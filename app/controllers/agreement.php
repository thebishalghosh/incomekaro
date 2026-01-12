<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/user.php';
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
    $company_details = get_company_details_for_agreement();

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
                $company_name = get_site_name();

                $email_body = "<p>Hello <b>" . $partner['profile']['full_name'] . "</b>,</p>";
                $email_body .= "<p>Thank you for accepting the Business Partnership Agreement with <b>" . $company_name . "</b>.</p>";
                $email_body .= "<p>Your acceptance was recorded on: <b>" . date('jS F Y, H:i:s') . "</b>.</p>";
                $email_body .= "<div class='info-box'>";
                $email_body .= "<p>A copy of the agreement can be downloaded from your partner dashboard at any time.</p>";
                $email_body .= "</div>";
                $email_body .= "<p>We are excited to have you on board!</p>";

                send_email($partner['profile']['email'], 'Agreement Accepted - Welcome to ' . $company_name, $email_body);

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
    $company_details = get_company_details_for_agreement();

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

// Helper function to get dynamic company details
function get_company_details_for_agreement() {
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
