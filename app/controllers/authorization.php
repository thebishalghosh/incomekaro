<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/user.php';

function authorization_download() {
    require_login();

    $user = find_user_by_id($_SESSION['user_id']);
    if (empty($user['partner_id'])) {
        die('Access Denied.');
    }

    $partner = get_partner_by_id($user['partner_id']);

    if (empty($partner['agreement_accepted_at'])) {
        flash('ptr_error', 'Please accept the agreement first.', 'alert alert-warning');
        redirect('agreement/index');
    }

    $company_details = get_company_details();

    ob_start();
    view('authorization/pdf', ['partner' => $partner, 'company' => $company_details]);
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream("Authorization_" . $partner['id'] . ".pdf", ["Attachment" => false]);
}
