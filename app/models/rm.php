<?php
function get_rm_partners($rm_id) {
    $db = get_db_connection();
    $sql = "SELECT p.*, pp.full_name, pp.mobile, pp.email, pp.profile_image
            FROM partners p
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            WHERE p.rm_id = :rm_id
            ORDER BY p.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_rm_applications($rm_id) {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name, p.name as partner_name, pp.full_name as partner_full_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            WHERE p.rm_id = :rm_id
            ORDER BY sa.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_rm_stats($rm_id) {
    $db = get_db_connection();
    $stats = [];

    // Total Partners
    $sql = "SELECT COUNT(*) FROM partners WHERE rm_id = :rm_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id);
    $stmt->execute();
    $stats['total_partners'] = $stmt->fetchColumn();

    // Total Applications
    $sql = "SELECT COUNT(sa.id) FROM service_applications sa
            JOIN partners p ON sa.partner_id = p.id
            WHERE p.rm_id = :rm_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id);
    $stmt->execute();
    $stats['total_applications'] = $stmt->fetchColumn();

    // Pending Applications (Fresh, Docs Uploaded, etc.)
    $sql = "SELECT COUNT(sa.id) FROM service_applications sa
            JOIN partners p ON sa.partner_id = p.id
            WHERE p.rm_id = :rm_id AND sa.status IN ('FRESH', 'DOCUMENTS_UPLOAD', 'DOCUMENTS_PENDING')";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id);
    $stmt->execute();
    $stats['pending_applications'] = $stmt->fetchColumn();

    return $stats;
}
