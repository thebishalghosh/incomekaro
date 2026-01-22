<?php
require_once APP_PATH . '/models/user.php'; // For wallet functions
require_once APP_PATH . '/core/database.php';
require_once APP_PATH . '/models/notification.php'; // Include Notification Model
require_once APP_PATH . '/models/withdrawal.php'; // Include Withdrawal Model

function withdrawal_index() {
    require_login();

    // Handle AJAX Search & Pagination
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;

        $role_code = $_SESSION['role_code'];
        $user_id = $_SESSION['user_id'];
        $wl_id = null;

        if ($role_code === 'WHITE_LABEL') {
            $current_user = find_user_by_id($user_id);
            $wl_id = $current_user['white_label_id'];
        }

        $withdrawals = get_all_withdrawals($page, $limit, $role_code, $user_id, $wl_id);
        $total_withdrawals = get_total_withdrawals_count($role_code, $user_id, $wl_id);
        $total_pages = ceil($total_withdrawals / $limit);

        // Return JSON
        header('Content-Type: application/json');
        echo json_encode([
            'withdrawals' => $withdrawals,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_withdrawals
            ]
        ]);
        exit;
    }

    // Initial Load
    $user = null;
    if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
    }

    view('withdrawal/index', ['user' => $user]);
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

        // Calculate TDS (2%)
        $tds_rate = 0.02;
        $tds_amount = $amount * $tds_rate;
        $net_amount = $amount - $tds_amount;

        // Logic: Deduct Balance & Create Request
        $db = get_db_connection();
        try {
            $db->beginTransaction();

            // 1. Deduct Balance (Gross Amount)
            update_wallet_balance($user['id'], $amount, 'debit', 'Withdrawal Request (TDS: ' . $tds_amount . ')', null);

            // 2. Create Withdrawal Record
            $sql = "INSERT INTO withdrawals (id, user_id, gross_amount, tds_amount, net_amount, status, account_holder_name, bank_name, bank_account_number, ifsc_code)
                    VALUES (:id, :user_id, :gross, :tds, :net, 'requested', :holder, :bank, :acc_num, :ifsc)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => uniqid('wd-'),
                'user_id' => $user['id'],
                'gross' => $amount,
                'tds' => $tds_amount,
                'net' => $net_amount,
                'holder' => $bank_details['account_holder_name'],
                'bank' => $bank_details['bank_name'],
                'acc_num' => $bank_details['account_number'],
                'ifsc' => $bank_details['ifsc_code']
            ]);

            $db->commit();

            // Notify Admin
            if (!empty($user['white_label_id'])) {
                // Notify WL Admin
                $stmt = $db->prepare("SELECT id FROM users WHERE white_label_id = :wl_id AND role_id = (SELECT id FROM roles WHERE code = 'WHITE_LABEL') LIMIT 1");
                $stmt->execute(['wl_id' => $user['white_label_id']]);
                $admin_id = $stmt->fetchColumn();
            } else {
                // Notify Super Admin
                $stmt = $db->prepare("SELECT id FROM users WHERE role_id = (SELECT id FROM roles WHERE code = 'SUPER_ADMIN') LIMIT 1");
                $stmt->execute();
                $admin_id = $stmt->fetchColumn();
            }

            if ($admin_id) {
                create_notification(
                    $admin_id,
                    'New Withdrawal Request',
                    "User {$user['first_name']} requested a withdrawal of ₹{$amount} (Net: ₹{$net_amount}).",
                    url('withdrawal/index')
                );
            }

            flash('withdraw_success', 'Withdrawal request submitted. Net payout: ₹' . number_format($net_amount, 2));

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
    $stmt = $db->prepare("SELECT u.white_label_id, r.code as role_code, w.user_id, w.net_amount FROM withdrawals w JOIN users u ON w.user_id = u.id JOIN roles r ON u.role_id = r.id WHERE w.id = :id");
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
        if ($target_role === 'PARTNER_ADMIN' && !empty($target_wl_id)) {
            flash('withdraw_error', 'Super Admin cannot approve White Label Partner withdrawals.', 'alert alert-danger');
            redirect('withdrawal/index');
        }
    }

    $stmt = $db->prepare("UPDATE withdrawals SET status = 'approved' WHERE id = :id AND status = 'requested'");

    if ($stmt->execute(['id' => $id])) {
        // Notify User
        create_notification(
            $result['user_id'],
            'Withdrawal Approved',
            "Your withdrawal request for ₹" . $result['net_amount'] . " has been approved.",
            url('withdrawal/index')
        );

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

            // 2. Refund Balance (Gross Amount)
            update_wallet_balance($withdrawal['user_id'], $withdrawal['gross_amount'], 'credit', 'Withdrawal Refund', $id);

            $db->commit();

            // Notify User
            create_notification(
                $withdrawal['user_id'],
                'Withdrawal Rejected',
                "Your withdrawal request has been rejected and refunded.",
                url('withdrawal/index')
            );

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
    $stmt = $db->prepare("SELECT u.white_label_id, r.code as role_code, w.user_id FROM withdrawals w JOIN users u ON w.user_id = u.id JOIN roles r ON u.role_id = r.id WHERE w.id = :id");
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
        // Notify User
        create_notification(
            $result['user_id'],
            'Withdrawal Paid',
            "Your withdrawal has been processed and paid.",
            url('withdrawal/index')
        );

        flash('withdraw_success', 'Marked as Paid.');
    }

    redirect('withdrawal/index');
}
