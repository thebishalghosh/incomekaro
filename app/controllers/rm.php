<?php
require_once APP_PATH . '/models/rm.php';
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/application.php';

function rm_index() {
    require_role('RM');
    $stats = get_rm_stats($_SESSION['user_id']);
    $recent_partners = get_rm_partners($_SESSION['user_id']);
    // Limit to 5 for dashboard
    $recent_partners = array_slice($recent_partners, 0, 5);

    view('dashboard/rm_home', ['stats' => $stats, 'recent_partners' => $recent_partners]);
}

function rm_partners() {
    require_role('RM');
    $partners = get_rm_partners($_SESSION['user_id']);
    // Reuse the admin partners list view
    view('dashboard/partners_list', ['partners' => $partners]);
}

function rm_applications() {
    require_role('RM');
    $applications = get_rm_applications($_SESSION['user_id']);
    // Reuse the admin applications list view
    view('dashboard/applications_list', ['applications' => $applications]);
}
