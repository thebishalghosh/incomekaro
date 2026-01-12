<?php
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/user.php';

function settings_index() {
    require_role('WHITE_LABEL');

    $user = find_user_by_id($_SESSION['user_id']);
    $wl = get_white_label_by_id($user['white_label_id']);

    // Decode landing page data
    $landing_data = !empty($wl['landing_page_data']) ? json_decode($wl['landing_page_data'], true) : [];

    // Default structure if empty
    if (empty($landing_data)) {
        $landing_data = [
            'hero' => ['title' => '', 'text' => '', 'image' => ''],
            'about' => ['title' => '', 'text' => '', 'image' => ''],
            'contact_phone' => '',
            'contact_address' => '',
            'products' => [
                ['title' => 'Credit Card DSA', 'desc' => 'Sell credit cards from leading banks like SBI, ICICI, HDFC, Citi, RBL, etc.'],
                ['title' => 'Personal & Business Loan', 'desc' => 'Sell instant personal loans, business loans, home loans, LAP from top banks.'],
                ['title' => 'General & Life Insurance', 'desc' => 'Sell insurance plans from top companies like HDFC Ergo, ICICI Lombard, etc.'],
                ['title' => 'Demat, Mutual Funds & Forex', 'desc' => 'Open FREE DEMAT Account and sell top mutual funds and stock market products.'],
                ['title' => 'FD, RD & Gold Bonds', 'desc' => 'Open deposits accounts for your clients with top fixed deposit plans.'],
                ['title' => 'Banking Services', 'desc' => 'Offer a wide range of paperless, fully online banking services.']
            ]
        ];
    }

    view('dashboard/settings', ['wl' => $wl, 'landing' => $landing_data]);
}

function settings_update() {
    require_role('WHITE_LABEL');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = find_user_by_id($_SESSION['user_id']);
        $wl = get_white_label_by_id($user['white_label_id']);

        // 1. Branding Data
        $company_name = trim($_POST['company_name']);
        $primary_color = trim($_POST['primary_color']);
        $secondary_color = trim($_POST['secondary_color']);

        // Handle Logo Upload
        $logo_url = $wl['logo_url'];
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/logos/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_logo_' . basename($_FILES['logo']['name']);
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $file_name)) {
                $logo_url = 'uploads/logos/' . $file_name;
            }
        }

        // Handle Signature Upload
        $signature_url = $wl['signature_url'] ?? null;
        if (isset($_FILES['signature']) && $_FILES['signature']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/signatures/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_sig_' . basename($_FILES['signature']['name']);
            if (move_uploaded_file($_FILES['signature']['tmp_name'], $upload_dir . $file_name)) {
                $signature_url = 'uploads/signatures/' . $file_name;
            }
        }

        // 2. Landing Page Data
        $current_landing = !empty($wl['landing_page_data']) ? json_decode($wl['landing_page_data'], true) : [];

        // Hero Image
        $hero_image = $current_landing['hero']['image'] ?? '';
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/landing/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_hero_' . basename($_FILES['hero_image']['name']);
            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $upload_dir . $file_name)) {
                $hero_image = 'uploads/landing/' . $file_name;
            }
        }

        // About Image
        $about_image = $current_landing['about']['image'] ?? '';
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/landing/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_about_' . basename($_FILES['about_image']['name']);
            if (move_uploaded_file($_FILES['about_image']['tmp_name'], $upload_dir . $file_name)) {
                $about_image = 'uploads/landing/' . $file_name;
            }
        }

        // Construct JSON
        $landing_data = [
            'hero' => [
                'title' => $_POST['hero_title'],
                'text' => $_POST['hero_text'],
                'image' => $hero_image
            ],
            'about' => [
                'title' => $_POST['about_title'],
                'text' => $_POST['about_text'],
                'image' => $about_image
            ],
            'contact_phone' => $_POST['contact_phone'],
            'contact_address' => $_POST['contact_address'],
            'products' => []
        ];

        // Process Products (Loop through 6 items)
        for ($i = 0; $i < 6; $i++) {
            if (isset($_POST['prod_title'][$i])) {
                $landing_data['products'][] = [
                    'title' => $_POST['prod_title'][$i],
                    'desc' => $_POST['prod_desc'][$i]
                ];
            }
        }

        // Prepare Data for Update
        $update_data = [
            'company_name' => $company_name,
            'logo_url' => $logo_url,
            'primary_color' => $primary_color,
            'secondary_color' => $secondary_color,
            'landing_page_data' => json_encode($landing_data),
            'signatory_name' => $_POST['signatory_name'] ?? null,
            'signatory_designation' => $_POST['signatory_designation'] ?? null,
            'signature_url' => $signature_url
        ];

        // 3. Update Database
        if (update_white_label_settings($wl['id'], $update_data)) {
            flash('settings_success', 'Settings Updated Successfully');
        } else {
            flash('settings_error', 'Failed to update settings', 'alert alert-danger');
        }

        redirect('settings/index');
    }
}
