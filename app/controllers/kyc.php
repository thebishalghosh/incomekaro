<?php
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/user.php';

function kyc_index() {
    require_login();

    $user = find_user_by_id($_SESSION['user_id']);
    if (empty($user['partner_id'])) {
        redirect('dashboard/index');
    }

    $partner = get_partner_by_id($user['partner_id']);

    // If already verified, redirect to dashboard
    if ($partner['kyc_status'] === 'VERIFIED') {
        redirect('dashboard/partner');
    }

    view('kyc/index', ['partner' => $partner]);
}

function kyc_upload() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = find_user_by_id($_SESSION['user_id']);
        $partner_id = $user['partner_id'];

        $upload_dir = APP_ROOT . '/public/uploads/documents/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $uploaded_count = 0;
        $doc_types = ['aadhaar_front', 'aadhaar_back', 'pan_card', 'cheque', 'certificate'];

        foreach ($doc_types as $type) {
            if (isset($_FILES[$type]) && $_FILES[$type]['error'] === 0) {
                $file_ext = pathinfo($_FILES[$type]['name'], PATHINFO_EXTENSION);
                $file_name = time() . '_' . $partner_id . '_' . $type . '.' . $file_ext;
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES[$type]['tmp_name'], $target_file)) {
                    $file_url = 'uploads/documents/' . $file_name;
                    add_partner_document($partner_id, strtoupper($type), $file_url);
                    $uploaded_count++;
                }
            }
        }

        if ($uploaded_count > 0) {
            flash('kyc_success', 'Documents uploaded successfully. Please wait for verification.');
        } else {
            flash('kyc_error', 'No files were uploaded. Please try again.', 'alert alert-danger');
        }
    }

    redirect('kyc/index');
}
