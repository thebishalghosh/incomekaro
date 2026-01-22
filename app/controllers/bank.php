<?php
require_once APP_PATH . '/models/bank.php';

function bank_index() {
    require_role('SUPER_ADMIN');

    // Handle AJAX Search & Pagination
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $banks = get_all_banks($page, $limit, $search);
        $total_banks = get_total_banks_count($search);
        $total_pages = ceil($total_banks / $limit);

        // Return JSON
        header('Content-Type: application/json');
        echo json_encode([
            'banks' => $banks,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_banks
            ]
        ]);
        exit;
    }

    // Initial Load
    view('bank/index');
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
