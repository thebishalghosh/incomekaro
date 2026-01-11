<?php
require_once APP_PATH . '/models/user.php'; // For wallet functions
require_once APP_PATH . '/core/database.php';

function withdrawal_index() {
    require_login();

    $db = get_db_connection();
    $withdrawals = [];
    $user = null;

    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        // Admin sees all
        $sql = "SELECT w.*, u.first_name, u.last_name, u.email
                FROM withdrawals w
                JOIN users u ON w.user_id = u.id
                ORDER BY w.created_at DESC";
        $stmt = $db->query($sql);
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
    require_role('SUPER_ADMIN');

    $db = get_db_connection();
    $stmt = $db->prepare("UPDATE withdrawals SET status = 'approved' WHERE id = :id AND status = 'requested'");

    if ($stmt->execute(['id' => $id])) {
        flash('withdraw_success', 'Withdrawal approved.');
    } else {
        flash('withdraw_error', 'Failed to approve.');
    }

    redirect('withdrawal/index');
}

function withdrawal_reject($id) {
    require_role('SUPER_ADMIN');

    $db = get_db_connection();

    // Fetch withdrawal to refund
    $stmt = $db->prepare("SELECT * FROM withdrawals WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $withdrawal = $stmt->fetch();

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
    require_role('SUPER_ADMIN');

    $db = get_db_connection();
    $stmt = $db->prepare("UPDATE withdrawals SET status = 'paid' WHERE id = :id AND status = 'approved'");

    if ($stmt->execute(['id' => $id])) {
        flash('withdraw_success', 'Marked as Paid.');
    }

    redirect('withdrawal/index');
}
