<?php
function find_user_by_email($email) {
    $db = get_db_connection();
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    return $stmt->fetch();
}

function find_user_by_id($id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
}

function get_users_by_partner_id($partner_id) {
    $db = get_db_connection();
    $sql = "SELECT u.*, r.code as role_code
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.partner_id = :partner_id
            ORDER BY r.code"; // Show admin first
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_user_role($role_id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM roles WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $role_id);
    $stmt->execute();
    return $stmt->fetch();
}

// For seeding/testing
function create_user($data) {
    $db = get_db_connection();
    $sql = "INSERT INTO users (id, role_id, first_name, last_name, email, password_hash, status)
            VALUES (:id, :role_id, :first_name, :last_name, :email, :password_hash, :status)";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $data['id']);
    $stmt->bindValue(':role_id', $data['role_id']);
    $stmt->bindValue(':first_name', $data['first_name']);
    $stmt->bindValue(':last_name', $data['last_name']);
    $stmt->bindValue(':email', $data['email']);
    $stmt->bindValue(':password_hash', $data['password_hash']);
    $stmt->bindValue(':status', 'active');

    return $stmt->execute();
}
