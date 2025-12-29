<?php
function get_all_applications() {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name, u.first_name, u.last_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            LEFT JOIN users u ON sa.created_by = u.id
            ORDER BY sa.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_application_by_id($id) {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name, u.first_name, u.last_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            LEFT JOIN users u ON sa.created_by = u.id
            WHERE sa.id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
}

function update_application_status($id, $status) {
    $db = get_db_connection();
    $sql = "UPDATE service_applications SET status = :status WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':status', $status);
    return $stmt->execute();
}
