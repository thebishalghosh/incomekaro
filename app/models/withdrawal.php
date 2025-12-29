<?php
function get_all_withdrawals() {
    $db = get_db_connection();
    $sql = "SELECT w.*, u.first_name, u.last_name, u.email
            FROM withdrawals w
            JOIN users u ON w.user_id = u.id
            ORDER BY w.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function update_withdrawal_status($id, $status) {
    $db = get_db_connection();
    $sql = "UPDATE withdrawals SET status = :status WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':status', $status);
    return $stmt->execute();
}
