<?php
function get_revenue_stats($start_date, $end_date, $white_label_id = null) {
    $db = get_db_connection();
    $params = ['start' => $start_date, 'end' => $end_date];

    // 1. Subscription Revenue (From Partners)
    $sql = "SELECT SUM(payment_amount) as total_revenue, COUNT(*) as count
            FROM partner_subscriptions ps
            JOIN partners p ON ps.partner_id = p.id
            WHERE ps.created_at BETWEEN :start AND :end AND ps.status = 'active'";

    if ($white_label_id) {
        $sql .= " AND p.white_label_id = :wl_id";
        $params['wl_id'] = $white_label_id;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $sub_stats = $stmt->fetch();

    // 2. White Label Subscription Revenue (Only for Super Admin)
    $wl_revenue = 0;
    if (!$white_label_id) {
        $sql = "SELECT SUM(amount) FROM white_label_subscriptions
                WHERE created_at BETWEEN :start AND :end AND status = 'active'";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => $start_date, 'end' => $end_date]);
        $wl_revenue = $stmt->fetchColumn() ?: 0;
    }

    return [
        'partner_revenue' => $sub_stats['total_revenue'] ?: 0,
        'wl_revenue' => $wl_revenue,
        'total_revenue' => ($sub_stats['total_revenue'] ?: 0) + $wl_revenue,
        'subscription_count' => $sub_stats['count']
    ];
}

function get_payout_stats($start_date, $end_date, $white_label_id = null) {
    $db = get_db_connection();
    $params = ['start' => $start_date, 'end' => $end_date];

    $sql = "SELECT SUM(net_amount) as total_payout, COUNT(*) as count
            FROM withdrawals w
            JOIN users u ON w.user_id = u.id
            WHERE w.created_at BETWEEN :start AND :end AND w.status = 'paid'";

    if ($white_label_id) {
        $sql .= " AND u.white_label_id = :wl_id";
        $params['wl_id'] = $white_label_id;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

function get_application_stats_report($start_date, $end_date, $white_label_id = null) {
    $db = get_db_connection();
    $params = ['start' => $start_date, 'end' => $end_date];

    $sql = "SELECT status, COUNT(*) as count
            FROM service_applications
            WHERE created_at BETWEEN :start AND :end";

    if ($white_label_id) {
        $sql .= " AND white_label_id = :wl_id";
        $params['wl_id'] = $white_label_id;
    }

    $sql .= " GROUP BY status";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function get_top_partners($limit = 5, $white_label_id = null) {
    $db = get_db_connection();
    $params = [];

    // Ranking by Approved Applications
    $sql = "SELECT p.name, p.id, COUNT(sa.id) as app_count,
            (SELECT SUM(payment_amount) FROM partner_subscriptions WHERE partner_id = p.id) as revenue
            FROM partners p
            LEFT JOIN service_applications sa ON p.id = sa.partner_id AND sa.status = 'APPROVED'
            WHERE 1=1";

    if ($white_label_id) {
        $sql .= " AND p.white_label_id = :wl_id";
        $params['wl_id'] = $white_label_id;
    }

    $sql .= " GROUP BY p.id ORDER BY app_count DESC LIMIT $limit";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_daily_trends($start_date, $end_date, $white_label_id = null) {
    $db = get_db_connection();
    $params = ['start' => $start_date, 'end' => $end_date];

    // Daily Applications
    $sql = "SELECT DATE(created_at) as date, COUNT(*) as count
            FROM service_applications
            WHERE created_at BETWEEN :start AND :end";

    if ($white_label_id) {
        $sql .= " AND white_label_id = :wl_id";
        $params['wl_id'] = $white_label_id;
    }

    $sql .= " GROUP BY DATE(created_at) ORDER BY date ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}
