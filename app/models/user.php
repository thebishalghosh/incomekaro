<?php
function get_all_users() {
    $db = get_db_connection();
    $sql = "SELECT u.*, r.name as role_name, wl.company_name as wl_name, p.name as partner_name,
            ubd.account_holder_name, ubd.bank_name, ubd.account_number, ubd.ifsc_code,
            (CASE WHEN ubd.id IS NOT NULL THEN 1 ELSE 0 END) as has_bank_details
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN white_label_clients wl ON u.white_label_id = wl.id
            LEFT JOIN partners p ON u.partner_id = p.id
            LEFT JOIN user_bank_details ubd ON u.id = ubd.user_id
            ORDER BY u.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function find_user_by_email($email) {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    return $stmt->fetch();
}

function find_user_by_id($id) {
    $db = get_db_connection();

    // Fetch User
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Fetch Bank Details
        $stmt = $db->prepare("SELECT * FROM user_bank_details WHERE user_id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $bank = $stmt->fetch();

        // Attach to user array
        $user['bank_details'] = $bank ? $bank : [];
    }

    return $user;
}

function get_user_role($role_id) {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT * FROM roles WHERE id = :id");
    $stmt->bindValue(':id', $role_id);
    $stmt->execute();
    return $stmt->fetch();
}

function get_all_roles() {
    $db = get_db_connection();
    $stmt = $db->query("SELECT * FROM roles ORDER BY name");
    return $stmt->fetchAll();
}

function get_users_by_role($role_code) {
    $db = get_db_connection();
    $sql = "SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE r.code = :role_code
            ORDER BY u.first_name";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':role_code', $role_code);
    $stmt->execute();
    return $stmt->fetchAll();
}

function create_user($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Insert User
        $sql = "INSERT INTO users (id, white_label_id, partner_id, role_id, first_name, last_name, email, phone, password_hash, status)
                VALUES (:id, :white_label_id, :partner_id, :role_id, :first_name, :last_name, :email, :phone, :password_hash, :status)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':white_label_id', $data['white_label_id'] ?? null);
        $stmt->bindValue(':partner_id', $data['partner_id'] ?? null);
        $stmt->bindValue(':role_id', $data['role_id']);
        $stmt->bindValue(':first_name', $data['first_name']);
        $stmt->bindValue(':last_name', $data['last_name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':phone', $data['phone']);
        $stmt->bindValue(':password_hash', $data['password_hash']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // 2. Insert Bank Details (if provided)
        if (!empty($data['bank_details'])) {
            $sql = "INSERT INTO user_bank_details (id, user_id, account_holder_name, bank_name, account_number, ifsc_code, branch)
                    VALUES (:id, :user_id, :holder, :bank, :acc_num, :ifsc, :branch)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => uniqid('bnk-'),
                'user_id' => $data['id'],
                'holder' => $data['bank_details']['account_holder_name'],
                'bank' => $data['bank_details']['bank_name'],
                'acc_num' => $data['bank_details']['account_number'],
                'ifsc' => $data['bank_details']['ifsc_code'],
                'branch' => $data['bank_details']['branch']
            ]);
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

function update_user($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Update User
        $sql = "UPDATE users SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                status = :status,
                role_id = :role_id
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':first_name', $data['first_name']);
        $stmt->bindValue(':last_name', $data['last_name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':phone', $data['phone']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->bindValue(':role_id', $data['role_id']);
        $stmt->execute();

        // 2. Update Bank Details
        if (!empty($data['bank_details'])) {
            // Check if exists
            $check = $db->prepare("SELECT id FROM user_bank_details WHERE user_id = :id");
            $check->execute(['id' => $data['id']]);

            if ($check->fetch()) {
                // Update
                $sql = "UPDATE user_bank_details SET
                        account_holder_name = :holder,
                        bank_name = :bank,
                        account_number = :acc_num,
                        ifsc_code = :ifsc,
                        branch = :branch
                        WHERE user_id = :user_id";
            } else {
                // Insert
                $sql = "INSERT INTO user_bank_details (id, user_id, account_holder_name, bank_name, account_number, ifsc_code, branch)
                        VALUES (:id, :user_id, :holder, :bank, :acc_num, :ifsc, :branch)";
            }

            $stmt = $db->prepare($sql);
            $params = [
                'user_id' => $data['id'],
                'holder' => $data['bank_details']['account_holder_name'],
                'bank' => $data['bank_details']['bank_name'],
                'acc_num' => $data['bank_details']['account_number'],
                'ifsc' => $data['bank_details']['ifsc_code'],
                'branch' => $data['bank_details']['branch']
            ];

            if (!$check->rowCount()) { // If inserting, add ID
                $params['id'] = uniqid('bnk-');
            }

            $stmt->execute($params);
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

function delete_user($id) {
    $db = get_db_connection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}

// --- Wallet Functions ---

function update_wallet_balance($user_id, $amount, $type, $description, $reference_id = null) {
    $db = get_db_connection();

    // Removed internal transaction handling to allow nested calls

    // 1. Update User Balance
    $operator = ($type === 'credit') ? '+' : '-';
    $sql = "UPDATE users SET wallet_balance = wallet_balance $operator :amount WHERE id = :id";
    $stmt = $db->prepare($sql);
    $result1 = $stmt->execute(['amount' => $amount, 'id' => $user_id]);

    // 2. Log Transaction
    $sql = "INSERT INTO wallet_transactions (id, user_id, type, amount, description, reference_id)
            VALUES (:id, :user_id, :type, :amount, :description, :reference_id)";
    $stmt = $db->prepare($sql);
    $result2 = $stmt->execute([
        'id' => uniqid('txn-'),
        'user_id' => $user_id,
        'type' => $type,
        'amount' => $amount,
        'description' => $description,
        'reference_id' => $reference_id
    ]);

    return $result1 && $result2;
}

function get_wallet_transactions($user_id) {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT * FROM wallet_transactions WHERE user_id = :id ORDER BY created_at DESC");
    $stmt->execute(['id' => $user_id]);
    return $stmt->fetchAll();
}
