<?php
function get_all_inquiries($page = 1, $limit = 10, $status_filter = '', $wl_id = null, $source_filter = '') {
    $db = get_db_connection();
    $offset = ($page - 1) * $limit;

    $sql = "SELECT c.*, wl.company_name as wl_name
            FROM contact_inquiries c
            LEFT JOIN white_label_clients wl ON c.white_label_id = wl.id
            WHERE 1=1";

    $params = [];

    if (!empty($status_filter)) {
        $sql .= " AND c.status = :status";
        $params[':status'] = $status_filter;
    }

    // wl_id is for role-based restriction (e.g. WL admin sees only their own)
    if ($wl_id !== null) {
        $sql .= " AND c.white_label_id = :wl_id";
        $params[':wl_id'] = $wl_id;
    }

    // source_filter is for Super Admin to filter by specific WL or Main Site
    if (!empty($source_filter)) {
        if ($source_filter === 'MAIN_SITE') {
            $sql .= " AND c.white_label_id IS NULL";
        } else {
            $sql .= " AND c.white_label_id = :source_filter";
            $params[':source_filter'] = $source_filter;
        }
    }

    $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_total_inquiries_count($status_filter = '', $wl_id = null, $source_filter = '') {
    $db = get_db_connection();
    $sql = "SELECT COUNT(*) FROM contact_inquiries c WHERE 1=1";
    $params = [];

    if (!empty($status_filter)) {
        $sql .= " AND c.status = :status";
        $params[':status'] = $status_filter;
    }

    if ($wl_id !== null) {
        $sql .= " AND c.white_label_id = :wl_id";
        $params[':wl_id'] = $wl_id;
    }

    if (!empty($source_filter)) {
        if ($source_filter === 'MAIN_SITE') {
            $sql .= " AND c.white_label_id IS NULL";
        } else {
            $sql .= " AND c.white_label_id = :source_filter";
            $params[':source_filter'] = $source_filter;
        }
    }

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchColumn();
}
