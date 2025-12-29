<?php
function get_all_users() {
    $db = get_db_connection();
    $sql = "SELECT
                u.*,
                r.name as role_name,
                COALESCE(u.profile_image, pp.profile_image) as final_profile_image
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN partner_profiles pp ON u.partner_id = pp.partner_id
            ORDER BY u.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_users_by_role($role_code) {
    $db = get_db_connection();
    $sql = "SELECT u.* FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE r.code = :role_code AND u.status = 'active'
            ORDER BY u.first_name";
    $stmt = $db->prepare($sql);
    $stmt->execute(['role_code' => $role_code]);
    return $stmt->fetchAll();
}

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
    $user = $stmt->fetch();

    if ($user) {
        $sql = "SELECT * FROM user_bank_details WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $id);
        $stmt->execute();
        $user['bank_details'] = $stmt->fetch();
    }

    return $user;
}

function get_all_roles() {
    $db = get_db_connection();
    $sql = "SELECT * FROM roles";
    return $db->query($sql)->fetchAll();
}

function create_full_user($data) {
    $db = get_db_connection();
    try {
        $db->beginTransaction();

        // 1. Create User
        $sql = "INSERT INTO users (id, role_id, first_name, last_name, email, phone, password_hash, profile_image, status)
                VALUES (:id, :role_id, :first_name, :last_name, :email, :phone, :password_hash, :profile_image, 'active')";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':role_id', $data['role_id']);
        $stmt->bindValue(':first_name', $data['first_name']);
        $stmt->bindValue(':last_name', $data['last_name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':phone', $data['phone']);
        $stmt->bindValue(':password_hash', $data['password_hash']);
        $stmt->bindValue(':profile_image', $data['profile_image']);
        $stmt->execute();

        // 2. Create Bank Details
        if (!empty($data['bank_details']['account_holder_name'])) {
            $sql = "INSERT INTO user_bank_details (id, user_id, account_holder_name, bank_name, account_number, ifsc_code, branch)
                    VALUES (:id, :user_id, :account_holder_name, :bank_name, :account_number, :ifsc_code, :branch)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', uniqid('ub-'));
            $stmt->bindValue(':user_id', $data['id']);
            $stmt->bindValue(':account_holder_name', $data['bank_details']['account_holder_name']);
            $stmt->bindValue(':bank_name', $data['bank_details']['bank_name']);
            $stmt->bindValue(':account_number', $data['bank_details']['account_number']);
            $stmt->bindValue(':ifsc_code', $data['bank_details']['ifsc_code']);
            $stmt->bindValue(':branch', $data['bank_details']['branch'] ?? null);
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

function update_full_user($data) {
    $db = get_db_connection();
    try {
        $db->beginTransaction();

        // 1. Update User
        $sql = "UPDATE users SET
                role_id = :role_id, first_name = :first_name, last_name = :last_name,
                email = :email, phone = :phone, status = :status" .
                ($data['profile_image'] ? ", profile_image = :profile_image" : "") .
                " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':role_id', $data['role_id']);
        $stmt->bindValue(':first_name', $data['first_name']);
        $stmt->bindValue(':last_name', $data['last_name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':phone', $data['phone']);
        $stmt->bindValue(':status', $data['status']);
        if ($data['profile_image']) {
            $stmt->bindValue(':profile_image', $data['profile_image']);
        }
        $stmt->execute();

        // 2. Update/Insert Bank Details
        if (!empty($data['bank_details']['account_holder_name'])) {
            $check_bank = $db->query("SELECT id FROM user_bank_details WHERE user_id = '" . $data['id'] . "'")->fetch();
            if ($check_bank) {
                $sql = "UPDATE user_bank_details SET account_holder_name = :account_holder_name, bank_name = :bank_name,
                        account_number = :account_number, ifsc_code = :ifsc_code, branch = :branch WHERE user_id = :user_id";
            } else {
                $sql = "INSERT INTO user_bank_details (id, user_id, account_holder_name, bank_name, account_number, ifsc_code, branch)
                        VALUES (:id, :user_id, :account_holder_name, :bank_name, :account_number, :ifsc_code, :branch)";
            }
            $stmt = $db->prepare($sql);
            if (!$check_bank) $stmt->bindValue(':id', uniqid('ub-'));
            $stmt->bindValue(':user_id', $data['id']);
            $stmt->bindValue(':account_holder_name', $data['bank_details']['account_holder_name']);
            $stmt->bindValue(':bank_name', $data['bank_details']['bank_name']);
            $stmt->bindValue(':account_number', $data['bank_details']['account_number']);
            $stmt->bindValue(':ifsc_code', $data['bank_details']['ifsc_code']);
            $stmt->bindValue(':branch', $data['bank_details']['branch'] ?? null);
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

function delete_user($id) {
    $db = get_db_connection();
    $db->exec("DELETE FROM user_bank_details WHERE user_id = '$id'");
    $db->exec("DELETE FROM users WHERE id = '$id'");
    return true;
}
