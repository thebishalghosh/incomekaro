<?php
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/models/white_label.php'; // For dropdowns if needed
require_once APP_PATH . '/core/database.php'; // Ensure DB connection is available

function user_index() {
    require_role('SUPER_ADMIN');
    $users = get_all_users();
    view('dashboard/users_list', ['users' => $users]);
}

function user_create() {
    require_role('SUPER_ADMIN');
    $roles = get_all_roles();
    view('forms/user_form', ['roles' => $roles]);
}

function user_store() {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Handle Bank Details
        $bank_details = [];
        if (!empty($_POST['account_number'])) {
            $bank_details = [
                'account_holder_name' => $_POST['account_holder_name'],
                'bank_name' => $_POST['bank_name'],
                'account_number' => $_POST['account_number'],
                'ifsc_code' => $_POST['ifsc_code'],
                'branch' => $_POST['branch']
            ];
        }

        $data = [
            'id' => uniqid('u-'),
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'role_id' => $_POST['role_id'],
            'status' => isset($_POST['is_active']) ? 'active' : 'inactive',
            'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'bank_details' => $bank_details
        ];

        if (create_user($data)) {
            flash('user_success', 'User Created Successfully');
            redirect('user/index');
        } else {
            flash('user_error', 'Failed to create user', 'alert alert-danger');
            redirect('user/create');
        }
    }
}

function user_edit($id) {
    require_role('SUPER_ADMIN');
    $user = find_user_by_id($id);
    if (!$user) {
        redirect('user/index');
    }
    $roles = get_all_roles();
    view('forms/user_form', ['user' => $user, 'roles' => $roles]);
}

function user_update($id) {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Handle Bank Details
        $bank_details = [];
        if (!empty($_POST['account_number'])) {
            $bank_details = [
                'account_holder_name' => $_POST['account_holder_name'],
                'bank_name' => $_POST['bank_name'],
                'account_number' => $_POST['account_number'],
                'ifsc_code' => $_POST['ifsc_code'],
                'branch' => $_POST['branch']
            ];
        }

        $data = [
            'id' => $id,
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'role_id' => $_POST['role_id'],
            'status' => isset($_POST['is_active']) ? 'active' : 'inactive',
            'bank_details' => $bank_details
        ];

        if (update_user($data)) {
            flash('user_success', 'User Updated');
            redirect('user/index');
        } else {
            flash('user_error', 'Failed to update user', 'alert alert-danger');
            redirect('user/edit/' . $id);
        }
    }
}

function user_delete($id) {
    require_role('SUPER_ADMIN');
    if (delete_user($id)) {
        flash('user_success', 'User Deleted');
    } else {
        flash('user_error', 'Failed to delete user', 'alert alert-danger');
    }
    redirect('user/index');
}

// --- Wallet Management ---

function user_wallet_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $type = $_POST['type']; // credit or debit
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

        if ($amount <= 0) {
            flash('user_error', 'Amount must be greater than 0.', 'alert alert-danger');
            redirect('user/index');
        }

        $db = get_db_connection();
        try {
            $db->beginTransaction();

            if (update_wallet_balance($id, $amount, $type, $description, 'MANUAL_ADJUSTMENT')) {
                $db->commit();
                flash('user_success', 'Wallet updated successfully.');
            } else {
                $db->rollBack();
                flash('user_error', 'Failed to update wallet.', 'alert alert-danger');
            }
        } catch (Exception $e) {
            $db->rollBack();
            flash('user_error', 'Error: ' . $e->getMessage(), 'alert alert-danger');
        }

        redirect('user/index');
    }
}
