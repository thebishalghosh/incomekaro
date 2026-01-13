<?php
require_once APP_PATH . '/models/rm.php';
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/application.php';

function rm_index() {
    require_role('RM');

    $stats = get_rm_stats($_SESSION['user_id']);

    view('dashboard/rm_home', ['stats' => $stats]);
}

function rm_partners() {
    require_role('RM');
    $partners = get_partners_by_rm($_SESSION['user_id']);
    view('dashboard/partners_list', ['partners' => $partners]);
}

function rm_applications() {
    require_role('RM');

    // Fetch applications for partners assigned to this RM
    $db = get_db_connection();
    // Updated query to join partner_profiles and fetch partner_full_name
    $sql = "SELECT sa.*, s.name as service_name,
            COALESCE(pp.full_name, p.name) as partner_full_name,
            p.id as partner_id,
            wl.company_name as white_label_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            LEFT JOIN white_label_clients wl ON sa.white_label_id = wl.id
            WHERE p.rm_id = :rm_id
            ORDER BY sa.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute(['rm_id' => $_SESSION['user_id']]);
    $applications = $stmt->fetchAll();

    view('dashboard/applications_list', ['applications' => $applications]);
}
