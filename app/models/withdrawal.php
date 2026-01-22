<?php
function get_all_withdrawals($page = 1, $limit = 10, $role_code = '', $user_id = null, $wl_id = null) {
    $db = get_db_connection();
    $offset = ($page - 1) * $limit;

    $sql = "SELECT w.*, u.first_name, u.last_name, u.email, u.white_label_id, r.code as role_code
            FROM withdrawals w
            JOIN users u ON w.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE 1=1";

    $params = [];

    if ($role_code === 'WHITE_LABEL' && $wl_id) {
        $sql .= " AND u.white_label_id = :wl_id";
        $params[':wl_id'] = $wl_id;
    } elseif ($role_code !== 'SUPER_ADMIN' && $role_code !== 'WHITE_LABEL' && $user_id) {
        $sql .= " AND w.user_id = :user_id";
        $params[':user_id'] = $user_id;
    }

    $sql .= " ORDER BY w.created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_total_withdrawals_count($role_code = '', $user_id = null, $wl_id = null) {
    $db = get_db_connection();
    $sql = "SELECT COUNT(*) FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE 1=1";
    $params = [];

    if ($role_code === 'WHITE_LABEL' && $wl_id) {
        $sql .= " AND u.white_label_id = :wl_id";
        $params[':wl_id'] = $wl_id;
    } elseif ($role_code !== 'SUPER_ADMIN' && $role_code !== 'WHITE_LABEL' && $user_id) {
        $sql .= " AND w.user_id = :user_id";
        $params[':user_id'] = $user_id;
    }

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchColumn();
}

function update_withdrawal_status($id, $status) {
    $db = get_db_connection();
    $sql = "UPDATE withdrawals SET status = :status WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':status', $status);
    return $stmt->execute();
}
