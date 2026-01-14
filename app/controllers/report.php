<?php
require_once APP_PATH . '/models/report.php';
require_once APP_PATH . '/models/white_label.php';

function report_index() {
    require_login();

    // Permission Check
    if ($_SESSION['role_code'] !== 'SUPER_ADMIN' && $_SESSION['role_code'] !== 'WHITE_LABEL' && $_SESSION['role_code'] !== 'RM') {
        die('Access Denied');
    }

    // Filters
    $start_date = $_GET['start_date'] ?? date('Y-m-01'); // Default: First day of current month
    $end_date = $_GET['end_date'] ?? date('Y-m-d');     // Default: Today
    $white_label_id = null;

    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $white_label_id = $user['white_label_id'];
    } elseif ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $white_label_id = $_GET['white_label_id'] ?? null;
    }

    // Fetch Data
    $revenue = get_revenue_stats($start_date, $end_date, $white_label_id);
    $payouts = get_payout_stats($start_date, $end_date, $white_label_id);
    $app_stats = get_application_stats_report($start_date, $end_date, $white_label_id);
    $top_partners = get_top_partners(5, $white_label_id);
    $daily_trends = get_daily_trends($start_date, $end_date, $white_label_id);

    // Fetch WL list for dropdown (Super Admin only)
    $white_labels = [];
    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        $white_labels = get_all_white_labels();
    }

    view('dashboard/reports', [
        'revenue' => $revenue,
        'payouts' => $payouts,
        'app_stats' => $app_stats,
        'top_partners' => $top_partners,
        'daily_trends' => $daily_trends,
        'white_labels' => $white_labels,
        'filters' => [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'white_label_id' => $white_label_id
        ]
    ]);
}
