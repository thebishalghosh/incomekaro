<?php
require_once APP_PATH . '/models/user.php'; // For wallet functions
require_once APP_PATH . '/core/database.php';

function withdrawal_index() {
    require_login();

    $db = get_db_connection();
    $withdrawals = [];
    $user = null;

    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        // Admin sees all, fetch white_label_id and role_code to distinguish
        $sql = "SELECT w.*, u.first_name, u.last_name, u.email, u.white_label_id, r.code as role_code
                FROM withdrawals w
                JOIN users u ON w.user_id = u.id
                JOIN roles r ON u.role_id = r.id
                ORDER BY w.created_at DESC";
        $stmt = $db->query($sql);
        $withdrawals = $stmt->fetchAll();

    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        // White Label Admin sees withdrawals from their partners
        $current_user = find_user_by_id($_SESSION['user_id']);
        $wl_id = $current_user['white_label_id'];

        $sql = "SELECT w.*, u.first_name, u.last_name, u.email
                FROM withdrawals w
                JOIN users u ON w.user_id = u.id
                WHERE u.white_label_id = :wl_id
                ORDER BY w.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['wl_id' => $wl_id]);
        $withdrawals = $stmt->fetchAll();

    } else {
        // Partner sees own
        $user_id = $_SESSION['user_id'];

        // Fetch user details for the modal (balance & bank)
        $user = find_user_by_id($user_id);

        $sql = "SELECT * FROM withdrawals WHERE user_id = :id ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $user_id]);
        $withdrawals = $stmt->fetchAll();
    }

    view('withdrawal/index', ['withdrawals' => $withdrawals, 'user' => $user]);
}

function withdrawal_store() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $bank_details = [
            'account_holder_name' => $_POST['account_holder_name'],
            'bank_name' => $_POST['bank_name'],
            'account_number' => $_POST['account_number'],
            'ifsc_code' => $_POST['ifsc_code']
        ];

        $user = find_user_by_id($_SESSION['user_id']);

        // Validation
        if ($amount <= 0) {
            flash('withdraw_error', 'Invalid amount.', 'alert alert-danger');
            redirect('withdrawal/index');
        }

        if ($amount > $user['wallet_balance']) {
            flash('withdraw_error', 'Insufficient wallet balance.', 'alert alert-danger');
            redirect('withdrawal/index');
        }

        // Logic: Deduct Balance & Create Request
        $db = get_db_connection();
        try {
            $db->beginTransaction();

            // 1. Deduct Balance
            update_wallet_balance($user['id'], $amount, 'debit', 'Withdrawal Request', null);

            // 2. Create Withdrawal Record
            $sql = "INSERT INTO withdrawals (id, user_id, gross_amount, net_amount, status, account_holder_name, bank_name, bank_account_number, ifsc_code)
                    VALUES (:id, :user_id, :amount, :amount, 'requested', :holder, :bank, :acc_num, :ifsc)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => uniqid('wd-'),
                'user_id' => $user['id'],
                'amount' => $amount,
                'holder' => $bank_details['account_holder_name'],
                'bank' => $bank_details['bank_name'],
                'acc_num' => $bank_details['account_number'],
                'ifsc' => $bank_details['ifsc_code']
            ]);

            $db->commit();
            flash('withdraw_success', 'Withdrawal request submitted.');

        } catch (Exception $e) {
            $db->rollBack();
            flash('withdraw_error', 'Failed to process request.', 'alert alert-danger');
        }

        redirect('withdrawal/index');
    }
}

function withdrawal_approve($id) {
    require_login(); // Changed from require_role('SUPER_ADMIN')

    // Permission Check
    if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL') {
        die('Access Denied');
    }

    $db = get_db_connection();

    // Fetch withdrawal details including user's WL ID
    $stmt = $db->prepare("SELECT u.white_label_id, r.code as role_code FROM withdrawals w JOIN users u ON w.user_id = u.id JOIN roles r ON u.role_id = r.id WHERE w.id = :id");
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    $target_wl_id = $result['white_label_id'];
    $target_role = $result['role_code'];

    // If White Label, ensure the withdrawal belongs to their partner
    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $current_user = find_user_by_id($_SESSION['user_id']);
        if ($target_wl_id !== $current_user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        // Super Admin cannot approve WL Partner withdrawals
        // But CAN approve WL Admin (Client) withdrawals or Platform Partner withdrawals

        if ($target_role === 'PARTNER_ADMIN' && !empty($target_wl_id)) {
            flash('withdraw_error', 'Super Admin cannot approve White Label Partner withdrawals.', 'alert alert-danger');
            redirect('withdrawal/index');
        }
    }

    $stmt = $db->prepare("UPDATE withdrawals SET status = 'approved' WHERE id = :id AND status = 'requested'");

    if ($stmt->execute(['id' => $id])) {
        flash('withdraw_success', 'Withdrawal approved.');
    } else {
        flash('withdraw_error', 'Failed to approve.');
    }

    redirect('withdrawal/index');
}

function withdrawal_reject($id) {
    require_login(); // Changed from require_role('SUPER_ADMIN')

    // Permission Check
    if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL') {
        die('Access Denied');
    }

    $db = get_db_connection();

    // Fetch withdrawal to refund
    $stmt = $db->prepare("SELECT w.*, u.white_label_id, r.code as role_code FROM withdrawals w JOIN users u ON w.user_id = u.id JOIN roles r ON u.role_id = r.id WHERE w.id = :id");
    $stmt->execute(['id' => $id]);
    $withdrawal = $stmt->fetch();

    // If White Label, ensure permission
    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $current_user = find_user_by_id($_SESSION['user_id']);
        if ($withdrawal['white_label_id'] !== $current_user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        // Super Admin Restriction
        if ($withdrawal['role_code'] === 'PARTNER_ADMIN' && !empty($withdrawal['white_label_id'])) {
            flash('withdraw_error', 'Super Admin cannot reject White Label Partner withdrawals.', 'alert alert-danger');
            redirect('withdrawal/index');
        }
    }

    if ($withdrawal && $withdrawal['status'] == 'requested') {
        try {
            $db->beginTransaction();

            // 1. Update Status
            $update = $db->prepare("UPDATE withdrawals SET status = 'rejected' WHERE id = :id");
            $update->execute(['id' => $id]);

            // 2. Refund Balance
            update_wallet_balance($withdrawal['user_id'], $withdrawal['gross_amount'], 'credit', 'Withdrawal Refund', $id);

            $db->commit();
            flash('withdraw_success', 'Withdrawal rejected and refunded.');

        } catch (Exception $e) {
            $db->rollBack();
            flash('withdraw_error', 'Failed to reject.');
        }
    }

    redirect('withdrawal/index');
}

function withdrawal_mark_paid($id) {
    require_login(); // Changed from require_role('SUPER_ADMIN')

    // Permission Check
    if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL') {
        die('Access Denied');
    }

    $db = get_db_connection();

    // Fetch details
    $stmt = $db->prepare("SELECT u.white_label_id, r.code as role_code FROM withdrawals w JOIN users u ON w.user_id = u.id JOIN roles r ON u.role_id = r.id WHERE w.id = :id");
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    $target_wl_id = $result['white_label_id'];
    $target_role = $result['role_code'];

    // If White Label, ensure permission
    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $current_user = find_user_by_id($_SESSION['user_id']);
        if ($target_wl_id !== $current_user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        // Super Admin Restriction
        if ($target_role === 'PARTNER_ADMIN' && !empty($target_wl_id)) {
            flash('withdraw_error', 'Super Admin cannot manage White Label Partner withdrawals.', 'alert alert-danger');
            redirect('withdrawal/index');
        }
    }

    $stmt = $db->prepare("UPDATE withdrawals SET status = 'paid' WHERE id = :id AND status = 'approved'");

    if ($stmt->execute(['id' => $id])) {
        flash('withdraw_success', 'Marked as Paid.');
    }

    redirect('withdrawal/index');
}
