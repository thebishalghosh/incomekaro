<?php
function get_all_white_labels() {
    $db = get_db_connection();
    $sql = "SELECT * FROM white_label_clients ORDER BY created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_white_label_by_id($id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM white_label_clients WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
}

function create_white_label($data) {
    $db = get_db_connection();
    $sql = "INSERT INTO white_label_clients (id, company_name, primary_domain, logo_url, primary_color, secondary_color, support_email, status)
            VALUES (:id, :company_name, :primary_domain, :logo_url, :primary_color, :secondary_color, :support_email, :status)";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $data['id']);
    $stmt->bindValue(':company_name', $data['company_name']);
    $stmt->bindValue(':primary_domain', $data['primary_domain']);
    $stmt->bindValue(':logo_url', $data['logo_url']);
    $stmt->bindValue(':primary_color', $data['primary_color']);
    $stmt->bindValue(':secondary_color', $data['secondary_color']);
    $stmt->bindValue(':support_email', $data['support_email']);
    $stmt->bindValue(':status', $data['status']);

    return $stmt->execute();
}

function update_white_label($data) {
    $db = get_db_connection();
    $sql = "UPDATE white_label_clients SET
            company_name = :company_name,
            primary_domain = :primary_domain,
            logo_url = :logo_url,
            primary_color = :primary_color,
            secondary_color = :secondary_color,
            support_email = :support_email,
            status = :status
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $data['id']);
    $stmt->bindValue(':company_name', $data['company_name']);
    $stmt->bindValue(':primary_domain', $data['primary_domain']);
    $stmt->bindValue(':logo_url', $data['logo_url']);
    $stmt->bindValue(':primary_color', $data['primary_color']);
    $stmt->bindValue(':secondary_color', $data['secondary_color']);
    $stmt->bindValue(':support_email', $data['support_email']);
    $stmt->bindValue(':status', $data['status']);

    return $stmt->execute();
}

function delete_white_label($id) {
    $db = get_db_connection();
    // Note: This might fail if there are foreign key constraints.
    // In a real app, we should soft delete or delete related data first.
    $sql = "DELETE FROM white_label_clients WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}
