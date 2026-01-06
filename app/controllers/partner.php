<?php
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/subscription.php';
require_once APP_PATH . '/models/user.php';

function partner_index() {
    require_role('SUPER_ADMIN');
    $partners = get_all_partners_for_admin();
    view('dashboard/partners_list', ['partners' => $partners]);
}

function partner_create() {
    require_role('SUPER_ADMIN');
    $white_labels = get_all_white_labels();
    $plans = get_active_subscription_plans();
    $rms = get_users_by_role('RM'); // Fetch RMs
    view('forms/partner_form', ['white_labels' => $white_labels, 'plans' => $plans, 'rms' => $rms]);
}

function partner_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize inputs
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Handle File Upload
        $profile_image = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/partners/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/partners/' . $file_name;
            }
        }

        // Prepare Data Array
        $data = [
            'id' => uniqid('ptr-'),
            'white_label_id' => !empty($_POST['white_label_id']) ? $_POST['white_label_id'] : null,
            'partner_type' => !empty($_POST['white_label_id']) ? 'WHITE_LABEL' : 'PLATFORM',
            'status' => 'active',
            'created_by' => $_SESSION['user_id'], // Track who created this partner

            'profile' => [
                'full_name' => trim($_POST['full_name']),
                'mobile' => trim($_POST['mobile']),
                'email' => trim($_POST['email']),
                'whatsapp' => trim($_POST['whatsapp']),
                'dob' => $_POST['dob'],
                'gender' => $_POST['gender'],
                'profile_image' => $profile_image
            ],

            'address_permanent' => [
                'address' => trim($_POST['perm_address']),
                'state' => trim($_POST['perm_state']),
                'city' => trim($_POST['perm_city']),
                'pincode' => trim($_POST['perm_pincode'])
            ],

            'address_office' => [
                'address' => trim($_POST['office_address']),
                'state' => trim($_POST['office_state']),
                'city' => trim($_POST['office_city']),
                'pincode' => trim($_POST['office_pincode'])
            ],

            'identity' => [
                'gst' => trim($_POST['gst']),
                'aadhaar' => trim($_POST['aadhaar']),
                'pan' => trim($_POST['pan'])
            ],

            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['branch'])
            ],

            'subscription' => [
                'plan_name' => $_POST['plan_name'],
                'payment_amount' => $_POST['payment_amount'],
                'due_amount' => $_POST['due_amount'],
                'payment_mode' => $_POST['payment_mode'],
                'transaction_id' => $_POST['transaction_id']
            ],

            'user_password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

        if (create_full_partner($data)) {
            // Assign RM if selected
            if (!empty($_POST['rm_id'])) {
                assign_rm_to_partner($data['id'], $_POST['rm_id']);
            }

            flash('ptr_success', 'Partner Created Successfully');
            redirect('partner/index');
        } else {
            flash('ptr_error', 'Failed to create partner', 'alert alert-danger');
            redirect('partner/create');
        }
    }
}

function partner_edit($id) {
    require_role('SUPER_ADMIN');
    $partner = get_partner_by_id($id);
    if (!$partner) {
        redirect('partner/index');
    }
    $white_labels = get_all_white_labels();
    $plans = get_active_subscription_plans();
    $rms = get_users_by_role('RM');
    view('forms/partner_form', ['partner' => $partner, 'white_labels' => $white_labels, 'plans' => $plans, 'rms' => $rms]);
}

function partner_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Handle File Upload (Only if new file is uploaded)
        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/partners/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/partners/' . $file_name;
            }
        }

        $data = [
            'id' => $id,
            'status' => 'active', // Status update handled separately usually, but keeping active on edit

            'profile' => [
                'full_name' => trim($_POST['full_name']),
                'mobile' => trim($_POST['mobile']),
                'email' => trim($_POST['email']),
                'whatsapp' => trim($_POST['whatsapp']),
                'dob' => $_POST['dob'],
                'gender' => $_POST['gender'],
                'profile_image' => $profile_image
            ],

            'address_permanent' => [
                'address' => trim($_POST['perm_address']),
                'state' => trim($_POST['perm_state']),
                'city' => trim($_POST['perm_city']),
                'pincode' => trim($_POST['perm_pincode'])
            ],

            'address_office' => [
                'address' => trim($_POST['office_address']),
                'state' => trim($_POST['office_state']),
                'city' => trim($_POST['office_city']),
                'pincode' => trim($_POST['office_pincode'])
            ],

            'identity' => [
                'gst' => trim($_POST['gst']),
                'aadhaar' => trim($_POST['aadhaar']),
                'pan' => trim($_POST['pan'])
            ],

            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['branch'])
            ],

            'subscription' => [
                'plan_name' => $_POST['plan_name'],
                'payment_amount' => $_POST['payment_amount'],
                'due_amount' => $_POST['due_amount'],
                'payment_mode' => $_POST['payment_mode'],
                'transaction_id' => $_POST['transaction_id']
            ]
        ];

        if (update_full_partner($data)) {
            // Update RM
            if (isset($_POST['rm_id'])) {
                assign_rm_to_partner($id, $_POST['rm_id']);
            }

            flash('ptr_success', 'Partner Updated Successfully');
            redirect('partner/index');
        } else {
            flash('ptr_error', 'Failed to update partner', 'alert alert-danger');
            redirect('partner/edit/' . $id);
        }
    }
}

function partner_update_status($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        if (update_partner_status($id, $status)) {
            flash('ptr_success', 'Partner status updated to ' . ucfirst($status));
        } else {
            flash('ptr_error', 'Failed to update status.', 'alert alert-danger');
        }
        redirect('partner/index');
    }
}

function partner_delete($id) {
    require_role('SUPER_ADMIN');
    if (delete_partner($id)) {
        flash('ptr_success', 'Partner Deleted');
    } else {
        flash('ptr_error', 'Could not delete partner.', 'alert alert-danger');
    }
    redirect('partner/index');
}

function partner_profile($id) {
    require_role('SUPER_ADMIN');
    $partner = get_partner_by_id($id);
    if (!$partner) {
        redirect('partner/index');
    }
    $rms = get_users_by_role('RM');
    view('dashboard/partner_profile', ['partner' => $partner, 'rms' => $rms]);
}

function partner_verify_kyc($id) {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        if (update_kyc_status($id, $status)) {
            flash('ptr_success', 'KYC Status Updated');
        } else {
            flash('ptr_error', 'Failed to update KYC status', 'alert alert-danger');
        }
        redirect('partner/profile/' . $id);
    }
}

function partner_assign_rm($id) {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $rm_id = $_POST['rm_id'];
        if (assign_rm_to_partner($id, $rm_id)) {
            flash('ptr_success', 'RM Assigned Successfully');
        } else {
            flash('ptr_error', 'Failed to assign RM', 'alert alert-danger');
        }
        redirect('partner/profile/' . $id);
    }
}
