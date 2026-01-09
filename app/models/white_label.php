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

function create_white_label($data, $password = 'password123') {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Create Client
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
        $stmt->execute();

        // 2. Create User (Auto-generated)
        // Check if role exists
        $role_stmt = $db->prepare("SELECT id FROM roles WHERE code = 'WHITE_LABEL'");
        $role_stmt->execute();
        $role = $role_stmt->fetch();

        if ($role) {
            $user_id = uniqid('u-');
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (id, white_label_id, role_id, first_name, last_name, email, phone, password_hash, status)
                    VALUES (:id, :white_label_id, :role_id, :first_name, :last_name, :email, :phone, :password_hash, 'active')";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $user_id);
            $stmt->bindValue(':white_label_id', $data['id']);
            $stmt->bindValue(':role_id', $role['id']);
            $stmt->bindValue(':first_name', $data['company_name']);
            $stmt->bindValue(':last_name', 'Admin');
            $stmt->bindValue(':email', $data['support_email']);
            $stmt->bindValue(':phone', ''); // Optional
            $stmt->bindValue(':password_hash', $password_hash);
            $stmt->execute();
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function update_white_label($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Update Client
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
        $stmt->execute();

        // 2. Update Linked User Email
        // Find the user linked to this white label
        $sql = "UPDATE users SET email = :email WHERE white_label_id = :id AND role_id = (SELECT id FROM roles WHERE code = 'WHITE_LABEL')";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $data['support_email']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function delete_white_label($id) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // Delete linked users first
        $db->exec("DELETE FROM users WHERE white_label_id = '$id'");

        // Delete client
        $sql = "DELETE FROM white_label_clients WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}
