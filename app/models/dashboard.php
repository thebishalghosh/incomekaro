<?php
function get_super_admin_stats() {
    $db = get_db_connection();
    $stats = [];

    // 1. Total Partners
    $stmt = $db->query("SELECT COUNT(*) FROM partners");
    $stats['total_partners'] = $stmt->fetchColumn();

    // 2. Total Applications
    $stmt = $db->query("SELECT COUNT(*) FROM service_applications");
    $stats['total_applications'] = $stmt->fetchColumn();

    // 3. Pending Applications
    $stmt = $db->query("SELECT COUNT(*) FROM service_applications WHERE status IN ('submitted', 'under_verification')");
    $stats['pending_applications'] = $stmt->fetchColumn();

    // 4. Total White Labels
    $stmt = $db->query("SELECT COUNT(*) FROM white_label_clients");
    $stats['total_white_labels'] = $stmt->fetchColumn();

    // 5. Recent Applications
    $sql = "SELECT sa.*, s.name as service_name, p.name as partner_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            ORDER BY sa.created_at DESC LIMIT 5";
    $stmt = $db->query($sql);
    $stats['recent_applications'] = $stmt->fetchAll();

    // 6. By Status (for chart)
    $sql = "SELECT status, COUNT(*) as count FROM service_applications GROUP BY status";
    $stmt = $db->query($sql);
    $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 7. By Service Category (for chart)
    $sql = "SELECT s.category, COUNT(*) as count
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            GROUP BY s.category";
    $stmt = $db->query($sql);
    $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 8. Partner Growth (Last 6 Months)
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
            FROM partners
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month ASC";
    $stmt = $db->query($sql);
    $stats['partner_growth'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    return $stats;
}
