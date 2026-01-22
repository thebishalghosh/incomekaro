<?php
require_once APP_PATH . '/models/bank.php';

function bank_index() {
    require_role('SUPER_ADMIN');
    $banks = get_all_banks();
    view('bank/index', ['banks' => $banks]);
}

function bank_import() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === 0) {
            $file_ext = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);

            if (strtolower($file_ext) !== 'csv') {
                flash('bank_error', 'Only CSV files are allowed.', 'alert alert-danger');
                redirect('bank/index');
            }

            if (import_banks_from_csv($_FILES['csv_file']['tmp_name'])) {
                flash('bank_success', 'Banks imported successfully.');
            } else {
                flash('bank_error', 'Failed to import banks.', 'alert alert-danger');
            }
        } else {
            flash('bank_error', 'Please upload a valid CSV file.', 'alert alert-danger');
        }
        redirect('bank/index');
    }
}

function bank_clear() {
    require_role('SUPER_ADMIN');
    if (clear_bank_data()) {
        flash('bank_success', 'All bank data cleared.');
    } else {
        flash('bank_error', 'Failed to clear data.', 'alert alert-danger');
    }
    redirect('bank/index');
}

function bank_search() {
    // Public API endpoint (or protected if needed)
    // header('Content-Type: application/json'); // View helper might interfere, so we echo and exit

    $pincode = $_GET['pincode'] ?? '';
    if (empty($pincode)) {
        echo json_encode([]);
        exit;
    }

    $banks = get_banks_by_pincode($pincode);
    echo json_encode($banks);
    exit;
}
