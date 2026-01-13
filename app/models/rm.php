<?php
function get_rm_stats($rm_id) {
    $db = get_db_connection();
    $stats = [];

    // 1. Fetch RM's Target from users table
    $stmt = $db->prepare("SELECT monthly_target FROM users WHERE id = :rm_id");
    $stmt->execute(['rm_id' => $rm_id]);
    $stats['monthly_target'] = $stmt->fetchColumn() ?: 0;

    // 2. Calculate Achieved Amount
    // Assumption: Sum of 'payment_amount' from subscriptions of partners created by this RM in the current month.
    // This is a complex assumption. A simpler one might be sum of application values.
    // Let's use a simpler metric for now: Sum of 'amount' from 'service_applications' meta where key is 'loan_amount' or similar.
    // Even simpler: Let's just count approved applications for now.
    // The most direct metric is probably revenue from their partners' subscriptions.

    $sql = "SELECT SUM(ps.payment_amount)
            FROM partner_subscriptions ps
            JOIN partners p ON ps.partner_id = p.id
            WHERE p.rm_id = :rm_id
            AND MONTH(ps.created_at) = MONTH(CURRENT_DATE())
            AND YEAR(ps.created_at) = YEAR(CURRENT_DATE())";

    $stmt = $db->prepare($sql);
    $stmt->execute(['rm_id' => $rm_id]);
    $stats['achieved_amount'] = $stmt->fetchColumn() ?: 0;

    // 3. Calculate Percentage
    if ($stats['monthly_target'] > 0) {
        $stats['achieved_percentage'] = ($stats['achieved_amount'] / $stats['monthly_target']) * 100;
    } else {
        $stats['achieved_percentage'] = 0;
    }

    // 4. Total Assigned Partners
    $stmt = $db->prepare("SELECT COUNT(*) FROM partners WHERE rm_id = :rm_id");
    $stmt->execute(['rm_id' => $rm_id]);
    $stats['total_partners'] = $stmt->fetchColumn();

    // 5. Pending KYC
    $stmt = $db->prepare("SELECT COUNT(*) FROM partners WHERE rm_id = :rm_id AND kyc_status = 'PENDING'");
    $stmt->execute(['rm_id' => $rm_id]);
    $stats['pending_kyc'] = $stmt->fetchColumn();

    // 6. Pending Applications
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM service_applications sa
        JOIN partners p ON sa.partner_id = p.id
        WHERE p.rm_id = :rm_id AND sa.status IN ('submitted', 'under_verification')
    ");
    $stmt->execute(['rm_id' => $rm_id]);
    $stats['pending_applications'] = $stmt->fetchColumn();

    // 7. Recent Pending KYC List
    $stmt = $db->prepare("SELECT * FROM partners WHERE rm_id = :rm_id AND kyc_status = 'PENDING' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute(['rm_id' => $rm_id]);
    $stats['kyc_list'] = $stmt->fetchAll();

    // 8. Recent Pending Applications List
    $sql = "SELECT sa.*, s.name as service_name, p.name as partner_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            WHERE p.rm_id = :rm_id AND sa.status IN ('submitted', 'under_verification')
            ORDER BY sa.created_at DESC LIMIT 5";
    $stmt = $db->prepare($sql);
    $stmt->execute(['rm_id' => $rm_id]);
    $stats['application_list'] = $stmt->fetchAll();

    return $stats;
}
