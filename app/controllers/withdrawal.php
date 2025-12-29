<?php
require_once APP_PATH . '/models/withdrawal.php';

function withdrawal_index() {
    require_role('SUPER_ADMIN');
    $withdrawals = get_all_withdrawals();
    view('dashboard/withdrawals_list', ['withdrawals' => $withdrawals]);
}

function withdrawal_update_status($id) {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        if (update_withdrawal_status($id, $status)) {
            flash('wd_success', 'Withdrawal status updated');
        } else {
            flash('wd_error', 'Failed to update status', 'alert alert-danger');
        }
        redirect('withdrawal/index');
    }
}
